<?php

class Notification extends Model
{

    private function checkChat($user_id, $chat_title){
        $user_id = (int) $user_id;

        $query = $this->DB->Query("
                SELECT r.CHAT_ID
                FROM b_im_relation r
                INNER JOIN b_im_chat c ON c.ID = r.CHAT_ID
                WHERE r.USER_ID = {$user_id}
                  AND c.TITLE = '" . $this->DB->ForSql($chat_title) . "'
                LIMIT 1
            ");

        return $query->Fetch();
    }

    public function sendNotifyToUser($chat_title, $message, $description, $user_id, $url = null){

        $chatManager = new CIMChat();
        $adminId = 61;

        $notifyUrl = '';
        if (!empty($url)) {
            $serverName = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $notifyUrl = $scheme . '://' . $serverName . $url;
        }

        $chat_id = $this->checkChat($user_id, $chat_title);

        if ($chat_id) {
            $chatId = (int)$chat_id['CHAT_ID'];
            // Отправляем сообщение
            if ((int)$chatId > 0) {
                CIMChat::AddMessage([
                    'TO_CHAT_ID' => (int)$chatId,
                    'FROM_USER_ID' => (int)$adminId,
                    'MESSAGE' => $message . $notifyUrl,
                ]);
            }
        } else {
            $chatId = $chatManager->Add([
                'TITLE' => $chat_title,
                'DESCRIPTION' => $description,
                'COLOR' => 'MINT',
                'TYPE' => IM_MESSAGE_CHAT,
                'AUTHOR_ID' => (int)$adminId,
                'USERS' => [(int)$adminId, $user_id],
            ]);

            if ((int)$chatId > 0) {
                CIMChat::AddMessage([
                    'TO_CHAT_ID' => (int)$chatId,
                    'FROM_USER_ID' => (int)$adminId,
                    'MESSAGE' => $message . $notifyUrl,
                ]);
            }
        }
    }
}