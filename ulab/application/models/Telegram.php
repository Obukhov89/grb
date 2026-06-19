<?php


class Telegram extends Model
{
    public function sendText($tgUserId, $text, $token)
    {
        $url = "https://api.telegram.org/bot" . $token . "/sendMessage";

        $data = [
            "chat_id" => $tgUserId,
            "text" => $text,
            "parse_mode" => "HTML"
        ];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        //curl_setopt($curl, CURLOPT_TIMEOUT_MS, 3000);

        $response = json_decode(curl_exec($curl), true);
        $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return $response;
    }
}