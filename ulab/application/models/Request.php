<?php

use \Bitrix\Main\Loader;

/**
 * Модель для заявки на испытания
 * Class Request
 */
class Request extends Model
{
    const DATE_START = '2021-01-01';
    //ID Сделки с которого начинается рефакторинг Результатов испытания (TODO: Для новых лабораторий удалить или добавить если производится рефакторинг результатов испытаний, так же убрать из карточки card.php, tz_show.php, probe.php)
    const RESULT_REFACTORING_START_ID = 1;


    /**
     * получить ID сделки с которого начинается рефакторинг "Результатов испытания"
     * получить
     * @return int
     */
    public function getResultRefactoringStartId(): int
    {
        return self::RESULT_REFACTORING_START_ID;
    }


    public function getList(): array
    {
        $cdbResult = $this->DB->Query("select * from ba_tz");

        $requestList = [];

        while ($row = $cdbResult->Fetch()) {
            $requestList[] = $row;
        }

        return $requestList;
    }

    /**
     * @param int $idDeal - ид сделки (из битрикса)
     * @return array|false|null
     */
    public function getDealById(int $idDeal)
    {
        return CCrmDeal::GetByID($idDeal);
    }


    /**
     * @param $dealId
     * @return array|mixed
     */
    public function getCrmUserFields($dealId)
    {
        global $USER_FIELD_MANAGER;

        return $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $dealId);
    }


    public function saveSign($dealId, $base64, $actId = 0)
    {
        if ($dealId == 15554 || $dealId == 16166 || $dealId == 17409) {
            $path = "/home/bitrix/www/protocol_generator/archive_client/{$dealId}/{$actId}";
        } else {
            $path = "/home/bitrix/www/protocol_generator/archive_client/{$dealId}";
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            $data = substr($base64, strpos($base64, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                return [
                    'success' => false,
                    'error' => "Не корректный тип изображения"
                ];
            }

            $data = str_replace(' ', '+', $data);
            $data = base64_decode($data);

            if ($data === false) {
                return [
                    'success' => false,
                    'error' => "Не удалось распознать base64"
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => "Не корректные данные"
            ];
        }

        if (!is_dir($path)) {
            $mkdirResult = mkdir($path);

            if ($mkdirResult === false) {
                return [
                    'success' => false,
                    'error' => "Не удалось создать папку"
                ];
            }
        }

        $saveResult = file_put_contents("{$path}/sign.{$type}", $data);

        if ($saveResult === false) {
            return [
                'success' => false,
                'error' => "Не удалось сохранить файл"
            ];
        }

        return [
            'success' => true,
        ];
    }


    /**
     * @param $companyId
     * @return array
     */
    public function getContractsByCompanyId($companyId): array
    {
        $result = [];

        if (empty($companyId)) {
            return $result;
        }

        $ba_contr = $this->DB->Query(
                "
                SELECT DISTINCT d.*
                FROM `DOGOVOR` d
                LEFT JOIN ba_tz bt1 ON bt1.ID_Z = d.DEAL_ID AND bt1.COMPANY_ID = {$companyId}
                LEFT JOIN ba_tz bt2 ON bt2.ID = d.TZ_ID AND bt2.COMPANY_ID = {$companyId}
                WHERE d.IS_ACTION > 0 
                  AND (d.CLIENT_ID = {$companyId} OR bt1.ID_Z IS NOT NULL OR bt2.ID IS NOT NULL)
            "
            );

        while ($row = $ba_contr->Fetch()) {
            $row['DATE'] = date("d.m.Y", strtotime($row['DATE']));

            $row['endless_text'] = '';

            if ($row['date_end']) {
                $currenRuDate = date('d.m.Y', strtotime($row['date_end']));

                $row['endless_text'] = " (срок действия договора до {$currenRuDate})";
            }

            if ($row['custom_number']) {
                $row['NUMBER'] = $row['number_client'];
            }

            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param $companyId
     * @return array
     */
    public function getContractsOfferByCompanyId($companyId): array
    {
        $result = [];

        if (empty($companyId)) {
            return $result;
        }

        $ba_contr = $this->DB->Query(
            "
                SELECT d.* 
                FROM InvoiceOffer io
                INNER JOIN DOGOVOR d ON d.ID = io.dogovor_id
                WHERE io.client_id = {$companyId} and d.IS_ACTION > 0
            "
        );

        while ($row = $ba_contr->Fetch()) {
            $row['DATE'] = date("d.m.Y", strtotime($row['DATE']));
            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param int $dealId
     * @return array
     */
    public function getTzByDealId(int $dealId)
    {
        $result = [];

        $ba_tz = $this->DB->Query(
            "SELECT tz.*, c.leader, c.confirm, count(c.id) c_count, count(c.date_return) с_date_return,   
                    (select min(date_start) from ulab_tz_lab_duration where deal_id = {$dealId}) as day_start 
       				FROM `ba_tz` tz 
                    left join ulab_tz_lab_duration as utld on tz.ID_Z = utld.deal_id   
                    LEFT JOIN CHECK_TZ c ON tz.ID = c.tz_id 
                    WHERE tz.`ID_Z` = {$dealId} "
        );

        while ($row = $ba_tz->Fetch()) {
            //TODO: deserialize
            $addMailList = unserialize($row['SAVE_MAIL']);
            $row['addMail'] = [];
            foreach ($addMailList as $mail) {
                if (!empty($mail)) {
                    $row['addMail'][] = $mail;
                }
            }

            $tz = unserialize($row['TZ']);
            $row['TZ'] = $tz;

            $probe = unserialize($row['PROBE']);
            $row['PROBE'] = $probe;

            $regx = "/(\d+)$/";
            preg_match($regx, $row['DOGOVOR_NUM'], $match);
            $row['DOGOVOR_NUM'] = $match[1] ?? '';
            $row['PRICE_FL'] = $row['PRICE'];
            $price = 0;
            if (!empty($row['PRICE'])) {
                $price = (float)$row['PRICE'];

                if ($row['discount_type'] == 'percent') {
                    $price -= $price * $row['DISCOUNT'] / 100;
                } elseif ($row['discount_type'] == 'rub') {
                    $price -= $row['DISCOUNT'];
                }


                $row['PRICE'] = StringHelper::priceFormatRus($row['PRICE']);
            } else {
                $row['PRICE'] = '--';
            }

            if ($price < 0) {
                $price = 0;
            }

            $row['price_discount'] = $price;
            $row['price_ru'] = StringHelper::priceFormatRus($price);

            $result = $row;
        }

        return $result;
    }

    /**
     * @param $tzId
     * @return bool
     */
    public function isConfirmTz(int $tzId)
    {
        $result = $this->DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`={$tzId}");

        if ($result->SelectedRowsCount() > 0) {
            while ($row = $result->Fetch()) {
                if ($row['confirm'] == 0 || empty($row['confirm'])) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param $tzId
     * @return array
     */
    public function getInvoice($tzId)
    {
        return $this->DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`={$tzId}")->Fetch();
    }

    /**
     * @param $tzId
     * @return array
     */
    public function getInvoice1С($tzId)
    {
        return $this->DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`={$tzId} AND `ANSWER_1C` = 1")->Fetch();
    }

    /**
     * @param $tzId
     * @return array
     */
    public function getProtocols($tzId)
    {
        $protocols = $this->DB->Query("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={$tzId}");

        $result = [];

        while ($row = $protocols->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param $dealId
     * @param $data
     * @return false|mixed|string
     */
    public function addTz($dealId, $data)
    {
        $dateCreate = date('d.m.Y');
        $dateCreateTimestamp = date('Y-m-d H:i:s');

        $data['ID_Z'] = $dealId;
        $data['DATE_CREATE'] = "'{$dateCreate}'";
        $data['DATE_CREATE_TIMESTAMP'] = "'{$dateCreateTimestamp}'";

        return $this->DB->Insert('ba_tz', $data);
    }

    /**
     * @param $sourceDealId
     * @param $newDealId
     */
    public function copyTz($sourceDealId, $newDealId)
    {
        $ba_tz = $this->DB->Query(
            "SELECT TZ, OBJECT, PROBE, SAVE_MAIL, MATERIAL, 
                    COMPANY_ID, COMPANY_TITLE, TYPE_ID, ASSIGNED, 
                    DESCRIPTION, OBJECT, PRICE, LABA_ID 
                FROM `ba_tz` 
                WHERE `ID_Z` = {$sourceDealId}"
        )->Fetch();

        $priceResult = $this->DB->Query(
            "SELECT um.price FROM ulab_gost_to_probe ugtp
        INNER JOIN 
            ulab_material_to_request umtr
            ON ugtp.material_to_request_id = umtr.id
        INNER JOIN 
            ulab_methods um
            ON ugtp.method_id = um.id
        WHERE 
            umtr.deal_id = {$sourceDealId}"
        );

        $totalPrice = 0;
        while ($row = $priceResult->Fetch()) {
            $totalPrice += (float)$row['price'];
        }

        $dealData = $this->getDealById($newDealId);

        $data = [
            'TYPE_ID' =>        $this->quoteStr($dealData['TYPE_ID']),
            'ASSIGNED' =>       $this->quoteStr($dealData['ASSIGNED']),
            'REQUEST_TITLE' =>  $this->quoteStr($dealData['TITLE']),
            'STAGE_ID' =>       $this->quoteStr('NEW'),
            'TZ' =>             $this->quoteStr($ba_tz['TZ']),
            'OBJECT' =>         $this->quoteStr($ba_tz['OBJECT']),
            //            'PROBE' =>          $this->quoteStr($ba_tz['PROBE']),
            'SAVE_MAIL' =>      $this->quoteStr($ba_tz['SAVE_MAIL']),
            'MATERIAL' =>       $this->quoteStr($ba_tz['MATERIAL']),
            'COMPANY_ID' =>     $this->quoteStr($ba_tz['COMPANY_ID']),
            'COMPANY_TITLE' =>  $this->quoteStr($ba_tz['COMPANY_TITLE']),
            'DESCRIPTION' =>    $this->quoteStr($ba_tz['DESCRIPTION']),
            'PRICE' =>          $this->quoteStr($totalPrice),
            'LABA_ID' =>        $this->quoteStr($ba_tz['LABA_ID']),
        ];

        $this->addTz($newDealId, $data);
    }


    /**
     * @param $dealId
     * @param $amount
     * @param $date
     */
    public function addPay($dealId, $amount, $date)
    {
        $this->DB->Query("UPDATE `ba_tz` SET `OPLATA` = `OPLATA` + {$amount}, `DATE_OPLATA` = {$date} WHERE ID_Z = {$dealId}");
    }

    /**
     * @param $dealId
     * @param $amount
     * @param $date
     */
    public function addMessage($dealId, $amount)
    {
        $res = $this->DB->Query("SELECT `REQUEST_TITLE`, `COMPANY_TITLE` FROM `ba_tz` WHERE `ID_Z` = {$dealId}")->Fetch();
        CModule::IncludeModule('im');
        $ar = array(
            "TO_CHAT_ID" => 855, // ID чата
            "FROM_USER_ID" => 278, // ID пользователя состоящего в чате
            "MESSAGE"     => "По заявке [URL=/ulab/request/card/{$dealId}]{$res['REQUEST_TITLE']} {$res['COMPANY_TITLE']}[/URL] внесена оплата в размере {$amount} рублей", // Произвольный текст
        );
        CIMChat::AddMessage($ar);

        $array = [83];
        foreach ($array as $a) {
            $notify = [
                "NOTIFY_TITLE" => 'что-то там',
                "TO_USER_ID" => $a,
                "FROM_USER_ID" => 278,
                "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                "NOTIFY_MESSAGE" => "По заявке <a href='/ulab/request/card/{$dealId}'>{$res['REQUEST_TITLE']} {$res['COMPANY_TITLE']}</a> внесена оплата в размере {$amount} рублей",
            ];
            CIMNotify::Add($notify);
        }
    }

    /**
     * @param $dealId
     * @param $data
     * @return bool|int|string
     */
    public function updateTz($dealId, $data)
    {
        $where = "WHERE ID_Z = {$dealId}";
        return $this->DB->Update('ba_tz', $data, $where);
    }

    public function getCountDeal($type = '')
    {
        //        $typeFilter = [];
        //
        //        if ( !empty($type) ) {
        //            $typeFilter = [
        //                'TYPE_ID' => "'{$type}'"
        //            ];
        //        }
        //
        //        $curYear = date("Y");
        //        $deals = CCrmDeal::GetList(['DATE_CREATE' => 'DESC'], ['>DATE_CREATE' => "01.01.{$curYear} 00:00:00", '!TYPE_ID' => "1"]); // TODO: разобраться
        //
        //        // TODO: разобраться
        //        $count = $deals->SelectedRowsCount();
        //
        //        return $count;

        $curYear = date("Y");
        $nameType = $type['type'];

        $sql = $this->DB->Query("select max(request_number) as `count` from ba_tz where year(DATE_CREATE_TIMESTAMP) = {$curYear} AND TYPE_ID = '{$nameType}'")->Fetch();

        return $sql['count'] ?? 0;
    }

    public function getCurrentY()
    {
        return (int)date("Y") % 10 ? substr(date("Y"), -2) : date("Y");
    }

    /**
     * @param $data
     * @return false|int
     */
    public function create($data)
    {
        $year = (int)date("Y") % 10 ? substr(date("Y"), -2) : date("Y");
        $countDeal = $this->getCountDeal() + 1;

        $newDeal = new CCrmDeal;

        // TODO: убрать костыль
        while (1) {
            $title = "{$data['type_rus']} №{$countDeal}/{$year}";

            $tmp = $this->DB->Query("SELECT ID FROM `b_crm_deal` WHERE `TITLE` = '{$title}'")->Fetch();

            if (empty($tmp)) {
                break;
            } else {
                $countDeal++;
            }
        }


        $arFields = [
            "TITLE" => $title,
            "COMPANY_ID" => $data['company_id'],
            "TYPE_ID" => $data['type'],
            "ASSIGNED_BY_ID" => $data['assigned'],
        ];

        $result = $newDeal->Add($arFields);

        $arDealUpdate = [
            'STAGE_ID' => 'PREPARATION',
            'UF_CRM_1571643970' => $data['arrAssigned'],
        ];

        if ($result) {
            $newDeal->Update(
                $newDeal,
                $arDealUpdate,
                true,
                true,
                ['DISABLE_USER_FIELD_CHECK' => true]
            );
        }



        return $result;
    }

    /**
     * @param $data
     * @return bool
     */
    public function update($data): bool
    {
        $deal = new CCrmDeal;

        $arDealUpdate = [
            "COMPANY_ID" => $data['company_id'],
            "ASSIGNED_BY_ID" => $data['assigned'],
            "TYPE_ID" => $data['type'],
            "TITLE" => $data['title'],
            'STAGE_ID' => 'PREPARATION',
        ];

        return $deal->Update(
            $data['ID'],
            $arDealUpdate,
            true,
            true,
            ['DISABLE_USER_FIELD_CHECK' => true]
        );
    }

    /**
     * @param $dealId
     * @param $stage
     */
    public function updateStageDeal($dealId, $stage, $stageNumber = 0)
    {
        $deal = new CCrmDeal;
        $arDealUpdate = ['STAGE_ID' => $stage];
        $deal->Update(
            $dealId,
            $arDealUpdate,
            true,
            true
        );

        $data = [
            'STAGE_ID' => "'{$stage}'",
            'STAGE_NUMBER' => $stageNumber
        ];
        $this->updateTz($dealId, $data);

        if ($stage == 2) {
            $idTz = $this->getTzIdByDealId($dealId);
            $this->DB->Query("DELETE FROM CHECK_TZ WHERE tz_id = {$idTz['ID']}");
        }
    }

    public function getTzIdByDealId(int $dealId)
    {
        return $this->DB->Query("SELECT ID FROM `ba_tz` WHERE ID_Z = {$dealId}")->Fetch();
    }

    public function getDealIdByTzId(int $tzId)
    {
        $sql = $this->DB->Query("SELECT ID_Z FROM `ba_tz` WHERE ID = {$tzId}")->Fetch();
        return $sql['ID_Z'];
    }

    public function getDealIdByProbeToLab(int $probeToLabId)
    {
        $result = $this->DB->Query(
            "
                SELECT umtr.deal_id
                FROM probe_to_lab ptl
                INNER JOIN ulab_material_to_request umtr ON umtr.id = ptl.umtr_id
                WHERE ptl.id = '{$probeToLabId}'
                LIMIT 1
            "
        )->Fetch()['deal_id'];

        return $result;
    }

    public function fillAssigned()
    {
        if (!isset($_COOKIE['test'])) {
            return;
        }

        global $USER_FIELD_MANAGER;

        $baTz = $this->DB->Query("SELECT ID_Z FROM `ba_tz` WHERE 1");

        while ($row = $baTz->Fetch()) {
            $userFields = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $row['ID_Z']);

            if (!empty($row['ID_Z'])) {
                $deal = CCrmDeal::GetByID($row['ID_Z']);
                if (!empty($deal['ASSIGNED_BY_ID'])) {
                    $data = [
                        'deal_id' => $row['ID_Z'],
                        'user_id' => (int)$deal['ASSIGNED_BY_ID'],
                        'is_main' => 1,
                    ];
                    $this->DB->Insert('assigned_to_request', $data);
                    foreach ($userFields['UF_CRM_1571643970']['VALUE'] as $ass) {
                        $data = [
                            'deal_id' => $row['ID_Z'],
                            'user_id' => (int)$ass,
                        ];
                        $this->DB->Insert('assigned_to_request', $data);
                    }
                }
            }
        }
    }

    public function getProtocolsByTzId(int $tzId): array
    {
        $result = [];

        $protocols = $this->DB->Query("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ` = {$tzId}");

        while ($row = $protocols->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getProtocolListByTzIds(array $tzIds): array
    {
        $result = [];

        $strId = implode(',', $tzIds);

        $protocols = $this->DB->Query("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ` IN ({$strId})");

        while ($row = $protocols->Fetch()) {
            $result[$row['ID_TZ']][] = $row;
        }

        return $result;
    }

    /**
     * @param $dealId
     * @return array
     */
    public function getAssignedByDealId($dealId)
    {
        $users = $this->DB->Query("SELECT user_id, is_main FROM `assigned_to_request` WHERE deal_id = {$dealId}");

        $result = [];
        while ($row = $users->Fetch()) {
            $user = CUser::GetByID($row['user_id'])->Fetch();
            $name = trim($user['NAME']);
            $lastName = trim($user['LAST_NAME']);
            $shortName = StringHelper::shortName($name);

            $resultData = [
                'user_id'       => $row['user_id'],
                'name'          => trim($name),
                'last_name'     => trim($lastName),
                'user_name'     => "{$name} {$lastName}",
                'short_name'    => "{$shortName}. {$lastName}",
                'is_main'       => $row['is_main'],
                'department'    => $user["UF_DEPARTMENT"],
            ];

            $result[] = $resultData;
        }

        // TODO: старое
        if (empty($result)) {
            global $USER_FIELD_MANAGER;

            $userFields = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $dealId);

            $deal = CCrmDeal::GetByID($dealId);

            if (!empty($deal['ASSIGNED_BY_ID'])) {
                $user = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
                $name = trim($user['NAME']);
                $lastName = trim($user['LAST_NAME']);
                $shortName = StringHelper::shortName($name);

                $resultData = [
                    'user_id' => $deal['ASSIGNED_BY_ID'],
                    'name' => $name,
                    'last_name' => $lastName,
                    'user_name' => "{$lastName} {$name}",
                    'short_name' => "{$shortName}. {$lastName}",
                    'is_main' => 1,
                    'department' => $user["UF_DEPARTMENT"],
                ];

                $result[] = $resultData;
            }

            foreach ($userFields['UF_CRM_1571643970']['VALUE'] as $ass) {
                $user = CUser::GetByID($ass)->Fetch();
                $name = trim($user['NAME']);
                $lastName = trim($user['LAST_NAME']);
                $shortName = StringHelper::shortName($name);

                $resultData = [
                    'user_id'       => $ass,
                    'name'          => $name,
                    'last_name'     => $lastName,
                    'user_name'     => "{$lastName} {$name}",
                    'short_name'    => "{$shortName}. {$lastName}",
                    'is_main'       => 0,
                    'department'    => $user["UF_DEPARTMENT"],
                ];

                $result[] = $resultData;
            }
        }

        return $result;
    }


    /**
     *
     * @param $userId
     * @return array
     */
    public function tzUnderConsideration($userId)
    {
        $year = (int) date("Y", time()) % 10 ? substr(date("Y", time()), -2) : date("Y", time());
        $yearLast = $year - 1;

        //TODO: почему account not null?
        /*$stmp = $this->DB->Query(
            "SELECT DISTINCT
                        ba.`REQUEST_TITLE`, ba.`COMPANY_TITLE`, ba.`ID_Z`, ba.`NUM_P`,
                        ba.`ACCOUNT`, ch.`tz_id`, ch.`date_reply`, ch.`date_return`, ch.`confirm`
                    FROM
                        `CHECK_TZ` ch, `ba_tz` ba
                    WHERE
                        ba.`ACCOUNT` is NULL AND ch.`leader` = {$userId} AND ch.confirm = 0 AND ch.date_return IS NULL
                        AND ch.`tz_id` = ba.`ID` AND (ba.REQUEST_TITLE LIKE '%/{$year}' or ba.REQUEST_TITLE LIKE '%/{$yearLast}')
                    ORDER BY ch.`date_submission` DESC"
        );*/

        $stmp = $this->DB->Query(
            "SELECT DISTINCT 
                        ba.`REQUEST_TITLE`, ba.`COMPANY_TITLE`, ba.`ID_Z`, ba.`NUM_P`, 
                        ba.`ACCOUNT`, ch.`tz_id`, ch.`date_reply`, ch.`date_return`, ch.`confirm`, ba.`STAGE_ID`
                    FROM 
                        `CHECK_TZ` ch, `ba_tz` ba 
                    WHERE 
                        ba.`ACCOUNT` is NULL AND ch.`leader` = {$userId} AND ch.confirm = 0 AND ch.date_return IS NULL 
                        AND ch.`tz_id` = ba.`ID` AND (ba.REQUEST_TITLE LIKE '%/{$year}' or ba.REQUEST_TITLE LIKE '%/{$yearLast}')
                    ORDER BY ch.`date_submission` DESC"
        );

        $result = [];

        while ($row = $stmp->Fetch()) {
            if ($row['STAGE_ID'] == 'LOSE') {
                continue;
            }
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param $userId
     * @return array
     */
    public function probeInLab($userId)
    {
        //        $stmp = $this->DB->Query(
        //            "SELECT DISTINCT
        //                    ba.`REQUEST_TITLE`, ba.`COMPANY_TITLE`, ba.`ID_Z`
        //                FROM
        //                    `ba_tz` as ba, assigned_to_request as atr
        //                WHERE
        //                    ba.`ID_Z` = atr.deal_id AND atr.user_id = {$userId} AND ba.`PROBE_IN_LAB` = '1'
        //                    AND (ba.RESULTS IS NULL OR ba.RESULTS = '') AND ba.ACT_NUM IS NOT NULL AND ba.ACT_NUM <> ''");
        $stmp = $this->DB->Query(
            "SELECT ptl.*, ba.`REQUEST_TITLE`, ba.`COMPANY_TITLE`, ba.`ID_Z`, umtr.cipher, DATE_FORMAT(sp.date_send, '%d.%m.%Y %H:%i') as date_send
                FROM probe_to_lab ptl
                join ulab_material_to_request as umtr on umtr.id = ptl.umtr_id
				join ba_tz as ba on umtr.deal_id = ba.ID_Z
                inner join CHECK_TZ ct ON ct.tz_id = ba.ID AND ct.leader = '{$userId}'
                left join ba_laba bl ON bl.HEAD_ID = '{$userId}'
                left join sended_probes sp ON sp.probe_id = umtr.id AND sp.lab_id = bl.ID
                WHERE ptl.user_id = {$userId} AND ptl.accept_probe = 0 and ba.OPLATA <= 0 and umtr.cipher != '' AND ba.STAGE_ID <> 'LOSE'"
        );

        $result = [];

        while ($row = $stmp->Fetch()) {
            $result[$row['ID_Z']]['data'][] = $row;

            if ($row['date_send']) {
                $result[$row['ID_Z']]['sended_count'] = !empty($result[$row['ID_Z']]['sended_count']) ? $result[$row['ID_Z']]['sended_count'] + 1 : 1;
            }
        }

        return $result;
    }


    /**
     * @param $userId
     * @return array
     */
    public function probeInLabPayed($userId)
    {
        $stmp = $this->DB->Query(
            "SELECT ptl.*, ba.`REQUEST_TITLE`, ba.`COMPANY_TITLE`, ba.`ID_Z`, umtr.cipher, DATE_FORMAT(sp.date_send, '%d.%m.%Y %H:%i') as date_send
                FROM probe_to_lab ptl
                join ulab_material_to_request as umtr on umtr.id = ptl.umtr_id
				join ba_tz as ba on umtr.deal_id = ba.ID_Z
 
                left join CHECK_TZ ctz ON ctz.tz_id = ba.ID
                left join ba_laba bl ON bl.HEAD_ID = '{$userId}'
                left join sended_probes sp ON sp.probe_id = umtr.id AND sp.lab_id = bl.ID
                WHERE ptl.user_id = {$userId} and ctz.leader = {$userId} AND ptl.accept_probe = 0 and ba.OPLATA > 0 and umtr.cipher != '' "
        );

        //        $stmp = $this->DB->Query(
        //            "SELECT DISTINCT
        //                    ba.`REQUEST_TITLE`, ba.`COMPANY_TITLE`, ba.`ID_Z`
        //                FROM
        //                    `ba_tz` as ba, assigned_to_request as atr
        //                WHERE
        //                    ba.`ID_Z` = atr.deal_id AND atr.user_id = {$userId} AND ba.`PROBE_IN_LAB` = '1'
        //                    AND (ba.RESULTS IS NULL OR ba.RESULTS = '') AND ba.ACT_NUM IS NOT NULL AND ba.ACT_NUM <> '' and ba.OPLATA > 0");

        $result = [];

        while ($row = $stmp->Fetch()) {
            $result[$row['ID_Z']]['data'][] = $row;

            if ($row['date_send']) {
                $result[$row['ID_Z']]['sended_count'] = !empty($result[$row['ID_Z']]['sended_count']) ? $result[$row['ID_Z']]['sended_count'] + 1 : 1;
            }
        }

        return $result;
    }


    /**
     * заявки, у которых не проставлены ответственные у методик в тз
     * @param $userId - ид руководителя
     * @return array
     */
    public function getRequestListNoSetAssigned($userId)
    {
        $labModel = new Lab();

        $labInfo = $labModel->getLabByUserId($userId);

        $sql = $this->DB->Query(
            "select distinct b.ID, b.ID_Z, b.REQUEST_TITLE 
                    from ba_tz as b
                    inner join ulab_material_to_request as probe on b.ID_Z = probe.deal_id
                    inner join ulab_gost_to_probe as method on `probe`.id = `method`.material_to_request_id and (`method`.assigned_id is null or `method`.assigned_id = '')
                    inner join ulab_methods_lab as lab on lab.method_id = `method`.new_method_id and lab.lab_id = {$labInfo['ID']}
                    where b.OPLATA > 0 and b.ACT_NUM is not null and b.DATE_SOZD >= '2023-10-01' and b.STAGE_ID NOT IN ('WON', 'LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13')
                    order by b.id desc "
        );

        $result = [];
        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param int $userId
     * @param array $filter = [ 'search' => [], 'order' => [], 'paginate' => [] ]
     * @return array
     */
    public function getDataToJournalRequests(int $userId, array $filter = []): array
    {
        $requirementModel = new Requirement();
        $statusModel = new Status();

        $where = "";
        $limit = "";
        $order = [
            'by' => 'b.ID',
            'dir' => 'DESC'
        ];

        $statusWhere = "";
        $statusLeft = "";
        $groupId = 0;

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // Заявка
                if (isset($filter['search']['requestTitle'])) {
                    $where .= "b.REQUEST_TITLE LIKE '%{$filter['search']['requestTitle']}%' AND ";
                }
                // ID заявки
                if (isset($filter['search']['deal_id'])) {
                    $where .= "b.ID_Z = {$filter['search']['deal_id']} AND ";
                }
                // Дата
                if (isset($filter['search']['DATE_CREATE_TIMESTAMP'])) {
                    $where .= "LOCATE('{$filter['search']['DATE_CREATE_TIMESTAMP']}', DATE_FORMAT(b.DATE_CREATE_TIMESTAMP, '%d.%m.%Y')) > 0 AND ";
                }
                if (isset($filter['search']['dateStart'])) {
                    $where .= "(b.DATE_CREATE_TIMESTAMP >= '{$filter['search']['dateStart']}' AND b.DATE_CREATE_TIMESTAMP <= '{$filter['search']['dateEnd']}') AND ";
                }
                // Клиент
                if (isset($filter['search']['COMPANY_TITLE'])) {
                    $where .= "b.COMPANY_TITLE LIKE '%{$filter['search']['COMPANY_TITLE']}%' AND ";
                }
                // Крайний срок
                if (isset($filter['search']['DEADLINE_TABLE'])) {
                    $where .= "b.DEADLINE_TABLE LIKE '%{$filter['search']['DEADLINE_TABLE']}%' AND ";
                }
                // Счет
                if (isset($filter['search']['ACCOUNT'])) {
                    $where .= "b.ACCOUNT LIKE '%{$filter['search']['ACCOUNT']}%' AND ";
                }
                // Объект испытаний
                if (isset($filter['search']['MATERIAL'])) {
                    $where .= "b.MATERIAL LIKE '%{$filter['search']['MATERIAL']}%' AND ";
                }
                // Ответственный
                if (isset($filter['search']['ASSIGNED'])) {
                    $where .= "usr.LAST_NAME LIKE '%{$filter['search']['ASSIGNED']}%' AND ";
                }
                // Акт ПП
                if (isset($filter['search']['NUM_ACT_TABLE'])) {
                    $where .= "b.NUM_ACT_TABLE LIKE '%{$filter['search']['NUM_ACT_TABLE']}%' AND ";
                }
                // ТЗ
                if (isset($filter['search']['tz'])) {
                    //                    $where .= "b.ID LIKE '%{$filter['search']['tz']}%' AND ";
                    if ($filter['search']['tz'] == 'n') {
                        $where .= "tzdoc.pdf is null AND ";
                    } else if ($filter['search']['tz'] == 'y') {
                        $where .= "tzdoc.pdf is not null AND ";
                    }
                }
                // Договор
                if (isset($filter['search']['DOGOVOR_TABLE'])) {
                    $where .= "d.NUMBER LIKE '%{$filter['search']['DOGOVOR_TABLE']}%' AND ";
                }
                // Стоимость
                if (isset($filter['search']['price_discount'])) {
                    $where .= "b.price_discount = '{$filter['search']['price_discount']}' AND ";
                }
                // Дата оплаты
                if (isset($filter['search']['DATE_OPLATA'])) {
                    $where .= "b.DATE_OPLATA LIKE '%{$filter['search']['DATE_OPLATA']}%' AND ";
                }
                // Производитель
                if (isset($filter['search']['MANUFACTURER_TITLE'])) {
                    $where .= "b.MANUFACTURER_TITLE LIKE '%{$filter['search']['MANUFACTURER_TITLE']}%' AND ";
                }
                // Последнее изменение (пользователь)
                if (isset($filter['search']['USER_HISTORY'])) {
                    $where .= "b.USER_HISTORY LIKE '%{$filter['search']['USER_HISTORY']}%' AND ";
                }
                // Лаборатории
                if (isset($filter['search']['lab'])) {
                    $where .= "b.LABA_ID LIKE '%{$filter['search']['lab']}%' AND ";
                }
                // Протокол
                if (isset($filter['search']['PROTOCOLS']) && !empty($filter['search']['PROTOCOLS'])) {
                    $where .= "p.NUMBER_AND_YEAR LIKE '%{$filter['search']['PROTOCOLS']}%' AND ";
                }

                if (isset($filter['search']['stageGroup']) && is_array($filter['search']['stageGroup'])) {
                    $groupConditions = []; // Собираем условия для OR внутри этой группы фильтров

                    foreach ($filter['search']['stageGroup'] as $index => $stageValue) {
                        if ($stageValue === '0' || $stageValue === 0) continue; // Пропускаем "Все"

                        // 1. Обработка кастомного фильтра
                        if ($stageValue == 'custom_1') {
                            $aliasTsh = "tsh_c1";
                            $aliasUmtr = "umtr_c1";
                            $aliasP = "p_c1";

                            $statusWhere .= " LEFT JOIN tz_status_history {$aliasTsh} ON {$aliasTsh}.deal_id = b.ID_Z AND {$aliasTsh}.status_id = 18 ";
                            $statusWhere .= " LEFT JOIN ulab_material_to_request {$aliasUmtr} ON {$aliasUmtr}.deal_id = b.ID_Z ";
                            $statusWhere .= " INNER JOIN PROTOCOLS {$aliasP} ON {$aliasP}.ID = {$aliasUmtr}.protocol_id AND {$aliasP}.NUMBER IS NULL ";

                            $groupConditions[] = "({$aliasTsh}.id IS NOT NULL AND {$aliasP}.id IS NOT NULL)";
                        }
                        // 2. Обработка групп (например, "g 1")
                        else if (strpos((string)$stageValue, 'g') === 0) {
                            $groupId = explode(' ', $stageValue)[1];
                            $statuses = $statusModel->getStatusesForGroup($groupId);

                            $subGroupConditions = [];
                            foreach ($statuses as $key => $statusId) {
                                $alias = "tsh_g{$groupId}_{$key}";
                                $statusWhere .= " LEFT JOIN tz_status_history {$alias} ON {$alias}.deal_id = b.ID_Z AND {$alias}.status_id = {$statusId} ";
                                $subGroupConditions[] = "({$alias}.id IS NOT NULL)";
                            }

                            // Если это группа 1, добавляем проверку на отсутствие истории
                            if ($groupId == 1) {
                                $aliasNone = "tsh_none_{$groupId}";
                                $statusWhere .= " LEFT JOIN tz_status_history {$aliasNone} ON {$aliasNone}.deal_id = b.ID_Z ";
                                $subGroupConditions[] = "({$aliasNone}.id IS NULL)";
                            }

                            if (!empty($subGroupConditions)) {
                                $groupConditions[] = "(" . implode(' AND ', $subGroupConditions) . ")";
                            }
                        }
                        // 3. Обработка конкретных ID статусов
                        else {
                            $prStatus = "";
                            $prWhere = "";
                            $alias = "tsh_st_" . $index;

                            if ($stageValue == 1 || $stageValue == 2) {
                                $aliasPr = "tsh_pr_" . $index;
                                $prStatus = " LEFT JOIN tz_status_history {$aliasPr} ON {$aliasPr}.deal_id = b.ID_Z ";
                                $prWhere = " OR {$aliasPr}.id IS NULL";
                            }

                            $statusWhere .= " LEFT JOIN tz_status_history {$alias} ON {$alias}.deal_id = b.ID_Z AND {$stageValue} = 
                (SELECT max(status_id) FROM tz_status_history WHERE deal_id = b.ID_Z) {$prStatus}";

                            $groupConditions[] = "({$alias}.id IS NOT NULL{$prWhere})";
                        }
                    }

                    // Если набрались условия, добавляем их в основной WHERE через OR
                    if (!empty($groupConditions)) {
                        $where .= "(" . implode(' OR ', $groupConditions) . ") AND ";
                    }
                }
            }
            if (isset($filter['search']['certification'])) {
                $certificationFilter = $filter['search']['certification'] ?? 'all';
                if ($certificationFilter === 'certification') {
                    // Фильтр для сертификационных заявок (с буквой "С")
                    $where .= "b.TAKEN_SERT_ISP = 1 AND ";
                } elseif ($certificationFilter === 'regular') {
                    // Фильтр для обычных заявок (без буквы "С")
                    $where .= "b.TAKEN_SERT_ISP = 0 AND ";
                }
            }

            // работа с сортировкой
            if (!empty($filter['order'])) {
                if ($filter['order']['dir'] === 'asc') {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'requestTitle':
                        $order['by'] = 'b.REQUEST_TITLE';
                        break;
                    case 'DATE_CREATE_TIMESTAMP':
                        $order['by'] = 'b.DATE_CREATE_TIMESTAMP';
                        break;
                    case 'COMPANY_TITLE':
                        $order['by'] = 'b.COMPANY_TITLE';
                        break;
                    case 'DEADLINE_TABLE':
                        $order['by'] = 'b.DEADLINE_TABLE';
                        break;
                    case 'ACCOUNT':
                        $order['by'] = 'b.ACCOUNT';
                        break;
                    case 'MATERIAL':
                        $order['by'] = 'b.MATERIAL';
                        break;
                    case 'NUM_ACT_TABLE':
                        $order['by'] = 'YEAR(ACT_DATE) DESC, a.ACT_NUM';
                        break;
                    //                    case 'NUM_ACT_TABLE':
                    //                        $order['by'] = 'b.NUM_ACT_TABLE';
                    //                        break;
                    case 'DOGOVOR_TABLE':
                        $order['by'] = 'd.NUMBER';
                        break;
                    case 'price_discount':
                        $order['by'] = 'b.price_discount';
                        break;
                    case 'DATE_OPLATA':
                        $order['by'] = 'b.DATE_OPLATA';
                        break;
                    case 'MANUFACTURER_TITLE':
                        $order['by'] = 'b.MANUFACTURER_TITLE';
                        break;
                    case 'USER_HISTORY':
                        $order['by'] = 'b.USER_HISTORY';
                        break;
                }
            }

            // работа с пагинацией
            if (isset($filter['paginate'])) {
                $offset = 0;
                // количество строк на страницу
                if (isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0) {
                    $length = $filter['paginate']['length'];

                    if (isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT {$offset}, {$length}";
                }
            }
        }
        $where .= "1 ";

        $result = [];
        $baTzIds = [];

        $data = $this->DB->Query(
            "SELECT DISTINCT b.ID b_id, b.TZ, b.STAGE_ID, b.ID_Z, b.ACT_NUM, b.REQUEST_TITLE, b.TAKEN_SERT_ISP, b.RESULTS, b.TAKEN_ID_DEAL, b.TYPE_ID, b.is_nds, b.is_nds_7,
                        CONVERT(substring_index(substring_index(b.REQUEST_TITLE, '№', -1), '/', 1 ),UNSIGNED INTEGER) request,
                        b.DATE_CREATE_TIMESTAMP, b.COMPANY_TITLE, b.DEADLINE,  b.DEADLINE_TABLE, b.ACCOUNT, b.COMPANY_ID,
                        b.ASSIGNED, b.NUM_ACT_TABLE, b.PRICE, b.price_discount, b.OPLATA, b.DATE_OPLATA, b.PDF,
                        b.discount_type, b.DISCOUNT, 
                        b.MANUFACTURER_TITLE, b.USER_HISTORY, b.LABA_ID, b.ACTUAL_VER b_actual_ver, c.leader, c.confirm,
                        bcc.TITLE as company_title_bcc,
                        count(c.id) c_count, count(c.date_return) с_date_return, k.ID k_id , d.IS_ACTION, CONCAT(d.CONTRACT_TYPE, ' ', d.NUMBER, ' от ', DATE_FORMAT(d.DATE, '%d.%m.%Y')) as DOGOVOR_TABLE,
                        tzdoc.pdf tz_pdf,
                        b.MATERIAL,
                        d.CONTRACT_TYPE as cnt, d.number_client as nmb, DATE_FORMAT(d.DATE, '%d.%m.%Y') as dt, d.custom_number,
                        GROUP_CONCAT(distinct p.`NUMBER` SEPARATOR ', ') as PROTOCOLS
                    FROM ba_tz b
                    LEFT JOIN ACT_BASE a ON a.ID_TZ = b.ID 
                    LEFT JOIN CHECK_TZ c ON b.ID=c.tz_id
                    LEFT JOIN KP k ON b.ID=k.TZ_ID 
                    LEFT JOIN PROTOCOLS p ON p.ID_TZ = b.ID and p.NUMBER > 0 
                    LEFT JOIN DEALS_TO_CONTRACTS dtc ON dtc.ID_DEAL=b.ID_Z
                    LEFT JOIN DOGOVOR d ON d.ID=dtc.ID_CONTRACT 
                    LEFT JOIN AKT_VR act ON act.TZ_ID=b.ID 
                    LEFT JOIN assigned_to_request ass ON ass.deal_id = b.ID_Z
                    LEFT JOIN b_user usr ON ass.user_id = usr.ID 
                    LEFT JOIN TZ_DOC tzdoc ON tzdoc.TZ_ID = b.ID
                    LEFT JOIN b_crm_company bcc ON bcc.ID = b.COMPANY_ID
                    {$statusWhere}
                    WHERE b.TYPE_ID != '3' AND {$where}
                    GROUP BY b.ID ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT b.ID val
                    FROM ba_tz AS b
                    LEFT JOIN ACT_BASE a ON a.ID_TZ = b.ID 
                    LEFT JOIN CHECK_TZ AS c ON b.ID=c.tz_id
                    LEFT JOIN KP AS k ON b.ID=k.TZ_ID 
                    LEFT JOIN PROTOCOLS p ON p.ID_TZ=b.ID 
                    LEFT JOIN DOGOVOR d ON d.TZ_ID=b.ID
                    LEFT JOIN assigned_to_request ass ON ass.deal_id = b.ID_Z
                    LEFT JOIN b_user usr ON ass.user_id = usr.ID
                    LEFT JOIN b_crm_company bcc ON bcc.ID = b.COMPANY_ID
                    WHERE b.TYPE_ID != '3'
                    GROUP BY b.ID"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT b.ID val
                    FROM ba_tz AS b
                    LEFT JOIN ACT_BASE a ON a.ID_TZ = b.ID 
                    LEFT JOIN CHECK_TZ AS c ON b.ID=c.tz_id
                    LEFT JOIN KP AS k ON b.ID=k.TZ_ID 
                    LEFT JOIN PROTOCOLS p ON p.ID_TZ=b.ID
                    LEFT JOIN DOGOVOR d ON d.TZ_ID=b.ID
                    LEFT JOIN AKT_VR act ON act.TZ_ID=b.ID 
                    LEFT JOIN assigned_to_request ass ON ass.deal_id = b.ID_Z
                    LEFT JOIN b_user usr ON ass.user_id = usr.ID 
                    LEFT JOIN b_crm_company bcc ON bcc.ID = b.COMPANY_ID    
                    LEFT JOIN TZ_DOC tzdoc ON tzdoc.TZ_ID = b.ID
                    {$statusWhere}
                    WHERE b.TYPE_ID != '3' AND {$where}
                    GROUP BY b.ID"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $statusInfo = $statusModel->getStatusesForDeal($row['ID_Z']);
            $stage = $this->getStage($row);

            if ($row['custom_number']) {
                $row['DOGOVOR_TABLE'] = "{$row['cnt']} {$row['nmb']} от {$row['dt']}";
            }

            if ($row['ID_Z'] < DEAL_NEW_STATUS) {
                $row['titleStage'] = $stage['title'];
                $row['bgStage'] = $stage['color'];
            } else {
                $row['titleStage'] = $statusInfo['title'];
                $row['bgStage'] = $statusInfo['color'];
            }

            //            $materials = $requirementModel->getMaterialFromTz($row['ID_Z']);
            //            $materialsName = array_column($materials, 'name');
            //            $row['MATERIAL'] = implode(', ', $materialsName);

            $row['start_new_area'] = DEAL_START_NEW_AREA;
            $isExistTz = $requirementModel->isExistTz($row['ID_Z']);

            //            $protocols = $this->getProtocolsByTzId($row['b_id']);

            $crmDeal = $this->getDealById($row['ID_Z']);

            $row['REQUEST_TITLE'] = $crmDeal['TITLE'];
            $assigned = $this->getAssignedByDealId($row['ID_Z']);
            $arrAss = [];
            foreach ($assigned as $item) {
                $arrAss[] = $item['short_name'];
            }

            if (!empty($arrAss)) {
                $row['ASSIGNED'] = implode(', ', $arrAss);
            }

            $baTzIds[] = $row['b_id'];

            $protocolsData = [];
            $firstProtocol = [];
            $mangoClass = '';
            $greenClass = '';

            if (!empty($row['c_count'])) {
                if (!empty($row['leader']) && $row['leader'] == $userId && empty($row['confirm'])) {
                    $mangoClass = 'mango-class';
                }

                if (!empty($check_tz['confirm']) && empty($res_kp['ID']) && in_array($userId, [35, 62])) {
                    $greenClass = 'green_class';
                }
            }
            // оповещение о закрытом договоре

            if (isset($row['IS_ACTION']) && $row['IS_ACTION'] == 0) {
                $row['bgOrder'] = 'bg-light-red';
            }

            if (!empty($row['tz_pdf'])) {
                $row['bgPdf'] = 'bg-green-transp';
            } else {
                $row['bgPdf'] = '';
            }



            $row['bgCheck'] = $mangoClass;

            $row['bgConfirm'] = $greenClass;

            $row['certificate'] = $row['REQUEST_TITLE'] && $row['TAKEN_SERT_ISP'] == 1 ? ' C' : '';

            $row['b_tz_id'] = $isExistTz ? $row['b_id'] : '';

            if (empty($row['price_discount'])) {
                $row['price_discount'] = $row['PRICE'];
            }

            if ($row['is_nds']) {
                if ($row['is_nds_7']) {
                    $row['price_discount'] = $row['price_discount'] * (1 + 0.07);
                } else {
                    $row['price_discount'] *= (1 + 0.05);
                }
            }

            $row['price_ru'] = StringHelper::priceFormatRus($row['price_discount']);

            if ($row['TAKEN_ID_DEAL'] || $row['TYPE_ID'] == 7) {
                $row['bgPrice'] = 'bg-green-transp';
            } elseif (round($row['price_discount'], 2) && round($row['OPLATA'], 2) >= round($row['price_discount'], 2) || !$row['price_discount']) {
                $row['bgPrice'] = 'bg-transparent';
            } else {
                $row['bgPrice'] = 'bg-light-red';
            }

            //Название компании
            if (!empty($row['COMPANY_ID'])) {
                $row['COMPANY_TITLE'] = $row['company_title_bcc'];
            }


            //ID Сделки с которого начинается рефакторинг Результатов испытания (TODO: Для новых лабораторий удалить или добавить если производится рефакторинг результатов испытаний, так же убрать из карточки card.php)
            $row['is_result_refactoring'] = $row['ID_Z'] > self::RESULT_REFACTORING_START_ID;

            $row['linkName'] = $row['b_id'] && $row['ACT_NUM'] ? 'Открыть' : '';

            //            if (count($protocols) > 0) {
            //                $firstProtocol = current($protocols);
            //                foreach ($protocols as $key => $value) {
            //                    $numberAndYear = !empty($value['NUMBER_AND_YEAR']) ? $value['NUMBER_AND_YEAR'] : '';
            //                    $protocolsData[$key] = [
            //                        'ID' => $value['ID'],
            //                        'NUMBER_AND_YEAR' => $numberAndYear,
            //                        'ACTUAL_VERSION' => unserialize($value['ACTUAL_VERSION']),
            //                        'PDF' => $value['PDF'],
            //                        'PROTOCOL_OUTSIDE_LIS' => $value['PROTOCOL_OUTSIDE_LIS'],
            //                        'YEAR' => !empty($value['DATE']) ? date('Y', strtotime($value['DATE'])) : ''
            //                    ];
            //
            //                    if (empty($value['PDF'])) {
            //                        $files = scandir($_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/archive/' . $row['b_id'] . $protocolsData[$key]['YEAR'] . '/' . $protocolsData[$key]['ID'] . '/');
            //
            //                        $protocolsData[$key]['FILES'] = !empty($files) ? $files : [];
            //                    } else {
            //                        $protocolsData[$key]['FILES'] = [];
            //                    }
            //                }
            //            }

            $row['firstProtocolId'] = $firstProtocol['ID'] ?? null;

            //            $row['PROTOCOLS'] = $protocolsData;

            $row['NUM_P_TABLE'] = !empty($row['NUM_P_TABLE']) ? $row['NUM_P_TABLE'] : '';

            $row['NUM_ACT_TABLE'] = $row['NUM_ACT_TABLE'] ?? '';

            $row['ACT_NUM'] = !empty($row['NUM_ACT_TABLE'])
                ? preg_replace('/\/[^.]+/', '', $row['NUM_ACT_TABLE'])
                : ''; //TODO: Проверить почему не всегда записывалось поле ACT_NUM и заменить

            $row['TAKEN_SERT_ISP'] = (int)$row['TAKEN_SERT_ISP'];

            $row['YEAR_ACT'] = !empty($row['DEADLINE']) ? date('Y',  strtotime($row['DATE_ACT'])) : '';

            $row['DEADLINE_TABLE'] = !empty($row['DEADLINE_TABLE']) ? date('d.m.Y',  strtotime($row['DEADLINE_TABLE'])) : '';
            $row['deadlineISO'] = !empty($row['DEADLINE_TABLE']) ? date('Y-m-d',  strtotime($row['DEADLINE_TABLE'])) : ''; //TODO: Проверить почему не всегда записывается поле DEADLINE и заменить

            $row['dateOplataISO'] = !empty($row['DATE_OPLATA']) ? date('Y-m-d',  strtotime($row['DATE_OPLATA'])) : '';

            $textColor = '';
            if (
                date('Y-m-d') > $row['deadlineISO']
                && !in_array($row['STAGE_ID'], ['2', '4', 'WON', 'LOSE', '5', '6', '7', '8', '9'])
            ) {
                $textColor = 'text-red';
            }

            $row['textColor'] = $textColor;

            $row['DATE_CREATE_TIMESTAMP'] = !empty($row['DATE_CREATE_TIMESTAMP']) && $row['DATE_CREATE_TIMESTAMP'] != "0000-00-00 00:00:00"
                ? date('Y-m-d',  strtotime($row['DATE_CREATE_TIMESTAMP']))
                : '';

            $row['dateCreateRu'] = !empty($row['DATE_CREATE_TIMESTAMP']) && $row['DATE_CREATE_TIMESTAMP'] != "0000-00-00 00:00:00"
                ? date('d.m.Y',  strtotime($row['DATE_CREATE_TIMESTAMP']))
                : '';

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    public function getNonCorrectDeals()
    {
        //        $result = [];
        //
        //        $response = $this->DB->Query("SELECT bt.ID_Z, umtr.id, p.NUMBER
        //                                      FROM ba_tz bt
        //                                      LEFT JOIN ulab_material_to_request umtr ON umtr.deal_id = bt.ID_Z
        //                                      LEFT JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
        //                                      LEFT JOIN ulab_start_trials ust ON ust.ugtp_id = ugtp.id
        //                                      LEFT JOIN PROTOCOLS p ON p.ID = umtr.protocol_id
        //                                      WHERE p.NUMBER IS NULL OR umtr.protocol_id IS NULL
        //                                      ORDER BY bt.ID_Z DESC
        //                                     ");
        //
        //        while($row = $response->Fetch()) {
        //            $trials = $this->getTrials($row['ID_Z']);
        //
        //            if(in_array(18, $trials)) {
        //                $result []= $row['ID_Z'];
        //            }
        //        }
        //
        //        $result = array_unique($result);
        //
        //        return implode(',', $result);
    }

    /**
     * @param $row - ba_tz
     * @return string[]
     */
    public function getStage($row)
    {
        $requirementModel = new Requirement();

        switch ($row['STAGE_ID']) {
            case 'NEW':
            case 'PREPARATION':
            case 'PREPAYMENT_INVOICE':
            case 'EXECUTING':
                $bgColor = 'bg-light-blue';
                $title = 'Испытания еще не проводились. Пробы не получены.';
                break;
            case 'FINAL_INVOICE':
                $bgColor = 'bg-yellow';
                $title = 'Пробы получены. Проводятся испытания';
                break;
            case '1':
                $bgColor = 'bg-yellow';
                $title = 'Пробы получены. Проводятся испытания.';
                break;
            case '2':
                $bgColor = 'bg-purple';
                $title = 'Испытания в лаборатории завершены. Оплата получена или не требуется. УПД не отправлены.';
                break;
            case '4':
                $bgColor = 'bg-light-green';
                $title = 'УПД отправлены заказчику.';
                break;
            case 'WON':
                $bgColor = 'bg-green';
                $title = 'УПД получены. Заявка успешно завершена.';
                break;
            case 'LOSE':
                $bgColor = 'bg-red';
                $title = 'Испытания не проведены. Заявка прекращена.';
                break;
            case '7':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Не проводим подобные испытания.';
                break;
            case '6':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Заказчик не вышел на связь.';
                break;
            case '5':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Заказчика не устроила цена.';
                break;
            case '8':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Создана другая заявка.';
                break;
            case '9':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Заказчик выбрал лабораторию в своем городе.';
                break;
            case '10':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Заказчик решил не проводить испытания.';
                break;
            case '11':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Отказались сами в связи с высокой загруженностью.';
                break;
            case '12':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Судебная экспертиза.';
                break;
            case '13':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Участие в тендере.';
                break;
            default:
                $bgColor = 'bg-red';
                $title = '';
                break;
        }

        if (
            $row['ACT_NUM']
            && in_array($row['STAGE_ID'], ['NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING'])
        ) {
            $bgColor = 'bg-yellow';
            $title = 'Пробы получены. Проводятся испытания';
        }

        if (!empty($row['price_discount'])) {
            $row['PRICE'] = $row['price_discount'];
        }

        if (
            $row['PRICE'] && $row['RESULTS']
            && (!$row['OPLATA'] || (float)$row['OPLATA'] < (float)$row['PRICE'])
            && $row['STAGE_ID'] === '2'
            && empty($row['TAKEN_ID_DEAL'])
        ) {
            $bgColor = 'bg-dark-red';
            $title = 'Испытания в лаборатории завершены. Оплата не поступила.';
        }

        $confirm = $requirementModel->getStateConfirm($row['ID_Z']);
        if ($confirm == CHECK_TZ_WAIT) {
            $bgColor = 'bg-dark-blue';
            $title = 'Заявка на стадии проверки ТЗ';
        }

        return [
            'title' => $title,
            'color' => $bgColor,
        ];
    }


    /**
     * @return array
     */
    public function getCheckTz(): array
    {
        $result = [];

        $checkTz = $this->DB->Query("SELECT ba.REQUEST_TITLE, ba.COMPANY_TITLE, ba.ID_Z, ba.ACCOUNT, ch.tz_id, ch.date_reply, ch.date_return, ch.confirm 
            FROM CHECK_TZ ch, ba_tz ba WHERE ba.ACCOUNT IS NULL AND ch.tz_id = ba.ID");

        while ($row = $checkTz->Fetch()) {
            $bgColor = $status = '';

            if (empty($row['date_reply']) && empty($row['date_return'])) {
                $status = 'На рассмотрении руководителя лаборатории';
            } elseif (!empty($row['date_reply']) && empty($row['date_return']) &&  !empty($row['confirm'])) {
                $status = 'Заявка принята руководителем. Счет не выставлен.';
                $bgColor = 'bg-light-pink';
            }

            unset($row['date_reply'], $row['date_return']);

            $row['REQUEST_TITLE'] = StringHelper::encode($row['REQUEST_TITLE']);

            $row['cropRequestTitle'] = StringHelper::cropString($row['REQUEST_TITLE'], 20, '...');

            $row['COMPANY_TITLE'] = StringHelper::encode($row['COMPANY_TITLE']);

            $row['cropCompanyTitle'] = StringHelper::cropString($row['COMPANY_TITLE'], 40, '...');

            $row['status'] = StringHelper::encode($status);

            $row['cropStatus'] = StringHelper::cropString($status, 40, '...');

            $row['bgColor'] = $bgColor;

            $result[] = $row;
        }

        return $result;
    }

    public function getDateStart()
    {
        return self::DATE_START;
    }

    /**
     * @param $idDeal
     * @param $comment
     */
    public function addComment($idDeal, $comment, $oldComment)
    {
        $currentDate = date('Y-m-d H:i:s');
        $userId = $_SESSION['SESS_AUTH']['USER_ID'];

        $this->DB->Query("DELETE FROM `COMMENTS` WHERE `ID_REQ` = {$idDeal}");

        $this->DB->Insert(
            'COMMENTS',
            [
                'ID_REQ' => $idDeal,
                'TEXT' => $this->quoteStr($this->DB->ForSql($comment))
            ]
        );

        $this->DB->Query(
            "
            INSERT INTO deals_comments_history(old_comment, new_comment, user_id, deal_id, date_comment) 
            VALUES('{$oldComment}', '{$comment}', '{$userId}', '{$idDeal}', '{$currentDate}')  
            "
        );
    }

    /**
     * @param $idDeal
     * @return mixed
     */
    public function getComment($idDeal)
    {
        $result = $this->DB->Query("SELECT `TEXT` FROM `COMMENTS` WHERE `ID_REQ` = {$idDeal}")->Fetch();
        return $result['TEXT'];
    }


    /**
     * @param $file
     * @param $dealId
     * @return array
     */
    public function savePhoto($file, $dealId)
    {
        // проверка что файл формата .png или .jpg //
        $pattern = "/\.(png|jpg|jpeg)$/i";
        if (!preg_match($pattern, $file['name'])) {
            $result = [
                'success' => false,
                'error' => [
                    'message' => 'Ошибка! Файл должен быть png, jpg, jpeg',
                ]
            ];

            return $result;
        }

        $uploaddir = '/home/bitrix/www/photo/' . $dealId;

        return $this->saveFile($uploaddir, $file['name'], $file['tmp_name']);
    }


    /**
     * @param $file
     * @param $dealId
     * @return array
     */
    public function saveAnyFile($path, $file)
    {
        $uploaddir = UPLOAD_DIR . "/{$path}";

        return $this->saveFile($uploaddir, $file['name'], $file['tmp_name']);
    }


    /**
     * @param $path - путь_до_файла/имя
     */
    public function deleteUploadedFile($path)
    {
        $file = UPLOAD_DIR . "/{$path}";

        unlink($file);
    }


    /**
     * журнал акта приёмки проб
     * @param array $filter
     * @return array
     */
    public function getDatatoJournalActProbe(array $filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'b.ID',
            'dir' => 'DESC'
        ];

        $labModel = new Lab();
        $permissionModel = new Permission();
        $perm = $permissionModel->getUserPermission($_SESSION['SESS_AUTH']['USER_ID']);

        if ($perm['view_name'] == 'lab') {
            //            $where .= "ass.user_id = '{$_SESSION['SESS_AUTH']['USER_ID']}' AND ";
        }

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // Заявка
                if (isset($filter['search']['REQUEST_TITLE'])) {
                    $where .= "b.REQUEST_TITLE LIKE '%{$filter['search']['REQUEST_TITLE']}%' AND ";
                }
                // Дата
                if (isset($filter['search']['DATE_ACT'])) {
                    $where .= "LOCATE('{$filter['search']['DATE_ACT']}', DATE_FORMAT(a.ACT_DATE, '%d.%m.%Y')) > 0 AND ";
                }
                if (isset($filter['search']['dateStart'])) {
                    $where .= "(a.ACT_DATE >= '{$filter['search']['dateStart']}' AND a.ACT_DATE <= '{$filter['search']['dateEnd']}') AND ";
                }
                // Клиент
                if (isset($filter['search']['COMPANY_TITLE'])) {
                    $where .= "b.COMPANY_TITLE LIKE '%{$filter['search']['COMPANY_TITLE']}%' AND ";
                }
                // Объект испытаний
                if (isset($filter['search']['MATERIAL'])) {
                    $where .= "b.MATERIAL LIKE '%{$filter['search']['MATERIAL']}%' AND ";
                }
                // Ответственный
                if (isset($filter['search']['ASSIGNED'])) {
                    //                    $where .= "b.ASSIGNED LIKE '%{$filter['search']['ASSIGNED']}%' AND ";
                    $where .= "(u.NAME LIKE '%{$filter['search']['ASSIGNED']}%' OR u.LAST_NAME LIKE '%{$filter['search']['ASSIGNED']}%') AND ";
                }
                // Акт ПП
                if (isset($filter['search']['NUM_ACT_TABLE'])) {
                    $where .= "b.NUM_ACT_TABLE LIKE '%{$filter['search']['NUM_ACT_TABLE']}%' AND ";
                }
                // Договор
                if (isset($filter['search']['DOGOVOR_TABLE'])) {
                    $where .= "b.DOGOVOR_TABLE LIKE '%{$filter['search']['DOGOVOR_TABLE']}%' AND ";
                }
                // Лаборатории
                if (isset($filter['search']['lab'])) {
                    $where .= "b.LABA_ID LIKE '%{$filter['search']['lab']}%' AND ";
                }
                if (isset($filter['search']['selectIrrevocable'])) {
                    $irrevocable = $filter['search']['selectIrrevocable'];
                    $where .= "umtr.irrevocable = {$filter['search']['selectIrrevocable']} AND ";
                }
                if (isset($filter['search']['selectAcceptProbe'])) {
                    $where .= "ptl.accept_probe = {$filter['search']['selectAcceptProbe']} AND ";
                }
                // везде
                if (isset($filter['search']['everywhere'])) {
                    $where .=
                        "(
                        b.NUM_ACT_TABLE LIKE '%{$filter['search']['everywhere']}%' 
                        OR b.DOGOVOR_TABLE LIKE '%{$filter['search']['everywhere']}%' 
                        OR b.DATE_ACT LIKE '%{$filter['search']['everywhere']}%' 
                        OR b.COMPANY_TITLE LIKE '%{$filter['search']['everywhere']}%' 
                        OR b.MATERIAL LIKE '%{$filter['search']['everywhere']}%' 
                        OR b.ASSIGNED LIKE '%{$filter['search']['everywhere']}%' 
                        OR b.REQUEST_TITLE LIKE '%{$filter['search']['everywhere']}%'  
                        ) AND ";
                }
            }

            // работа с сортировкой
            if (!empty($filter['order'])) {
                if ($filter['order']['dir'] === 'asc') {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'NUM_ACT_TABLE':
                        $order['by'] = 'a.ACT_NUM';
                        break;
                    case 'DOGOVOR_TABLE':
                        $order['by'] = 'b.DOGOVOR_TABLE';
                        break;
                    case 'DATE_ACT':
                        $order['by'] = 'b.DATE_ACT';
                        break;
                    case 'COMPANY_TITLE':
                        $order['by'] = 'b.COMPANY_TITLE';
                        break;
                    case 'MATERIAL':
                        $order['by'] = 'b.MATERIAL';
                        break;
                    case 'ASSIGNED':
                        $order['by'] = 'b.ASSIGNED';
                        break;
                    case 'REQUEST_TITLE':
                        $order['by'] = 'b.REQUEST_TITLE';
                        break;
                    default:
                        $order['by'] = 'b.ID';
                }
            }

            // работа с пагинацией
            if (isset($filter['paginate'])) {
                $offset = 0;
                // количество строк на страницу
                if (isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0) {
                    $length = $filter['paginate']['length'];

                    if (isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT {$offset}, {$length}";
                }
            }
        }

        $where .= "1 ";

        $result = [];


        $data = $this->DB->Query(
            "SELECT DISTINCT b.ID as tz_id, b.ID as b_id, b.TZ, b.NUM_ACT_TABLE, b.ID_Z, b.DOGOVOR_TABLE, b.REQUEST_TITLE, 
                        b.DATE_ACT, b.COMPANY_TITLE, b.MATERIAL, b.ASSIGNED, a.ACT_NUM, umtr.irrevocable, ugtp.date_last_update_actual_value, ptl.accept_probe
                    FROM ba_tz b
                    LEFT JOIN ACT_BASE a ON a.ID_TZ = b.ID
                    LEFT JOIN assigned_to_request as ass ON ass.deal_id = b.ID_Z
                    LEFT JOIN ulab_material_to_request umtr ON umtr.deal_id = b.ID_Z AND umtr.irrevocable = '1'
                    LEFT JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id AND ugtp.date_last_update_actual_value IS NOT NULL
                    LEFT JOIN probe_to_lab ptl ON ptl.umtr_id = umtr.id AND ptl.accept_probe IS NOT NULL
                    LEFT JOIN b_user as u ON u.ID = ass.user_id
                    WHERE b.TYPE_ID != '3' AND b.REQUEST_TITLE <> '' AND u.ACTIVE = 'Y' AND {$where}
                    GROUP BY b.ID ORDER BY YEAR(ACT_DATE) DESC, {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT b.ID val
                    FROM ba_tz AS b
                    LEFT JOIN ACT_BASE a ON a.ID_TZ = b.ID
                    LEFT JOIN assigned_to_request as ass ON ass.deal_id = b.ID_Z
                    LEFT JOIN b_user as u ON u.ID = ass.user_id
                    WHERE b.TYPE_ID != '3' AND b.REQUEST_TITLE <> '' AND u.ACTIVE = 'Y' 
                    GROUP BY b.ID"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT b.ID val
                    FROM ba_tz AS b
                    LEFT JOIN ACT_BASE a ON a.ID_TZ = b.ID
                    LEFT JOIN assigned_to_request as ass ON ass.deal_id = b.ID_Z
                    LEFT JOIN ulab_material_to_request umtr ON umtr.deal_id = b.ID_Z
                    LEFT JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id AND ugtp.date_last_update_actual_value IS NOT NULL
                    LEFT JOIN probe_to_lab ptl ON ptl.umtr_id = umtr.id
                    LEFT JOIN b_user as u ON u.ID = ass.user_id
                    WHERE b.TYPE_ID != '3' AND b.REQUEST_TITLE <> '' AND u.ACTIVE = 'Y' AND {$where}
                    GROUP BY b.ID"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $row['DATE_ACT'] = !empty($row['DATE_ACT']) ? date('d.m.Y',  strtotime($row['DATE_ACT'])) : '';

            $assigned = $this->getAssignedByDealId($row['ID_Z']);
            $arrAss = [];
            foreach ($assigned as $item) {
                $arrAss[] = $item['short_name'];
            }

            if (!empty($arrAss)) {
                $row['ASSIGNED'] = implode(', ', $arrAss);
            }

            $cipher = $this->getCipherByDealId($row['ID_Z']);
            $arrCipher = [];

            foreach ($cipher as $item) {
                $arrCipher[] = $item['cipher'];
            }

            if (!empty($arrCipher)) {
                $row['CIPHER'] = implode(', ', $arrCipher);
            } else {
                $row['CIPHER'] = '';
            }
            if (is_null($row['accept_probe']) || $row['accept_probe'] == '0') {
                $row['accept_probe'] = '';
            } else {
                $row['accept_probe'] = 'Да';
            }
            if (is_null($row['irrevocable']) || $row['irrevocable'] == '0') {
                $row['irrevocable'] = '';
            } elseif (isset($row['date_last_update_actual_value'])) {
                $givenDateTime = new DateTime($row['date_last_update_actual_value']);
                $currentDateTime = new DateTime();

                $interval = $currentDateTime->diff($givenDateTime);

                $daysDifference = $interval->days;

                $moreThan14Days = ($interval->invert && $daysDifference > 14);
                if ($moreThan14Days) {
                    $row['irrevocable'] = 'Да';
                }
            }

            $protocols = $this->getProtocolsByTzId($row['b_id']);
            $protocolsData = [];
            $protocolsTest = [];

            foreach ($protocols as $key => $value) {
                $protocolsData[$key] = $value['NUMBER_AND_YEAR'];
                $protocolsTest[$key] = $value['DATE'];
            }

            $row['PROTOCOLS'] = implode(', <br>', $protocolsData);
            $row['PROTOCOLS_TEST'] = implode(', <br>', $protocolsTest);

            $labs = $labModel->getLabByCheckTz($row['tz_id']);

            $row['LAB'] = implode(', <br>', array_column($labs, 'short_name'));

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


    /**
     * журнал акта приёмки проб
     * @param array $filter
     * @return array
     */
    public function getDatatoJournalInvoice(array $filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'b.ID',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // Заявка
                if (isset($filter['search']['REQUEST_TITLE'])) {
                    $where .= "b.REQUEST_TITLE LIKE '%{$filter['search']['REQUEST_TITLE']}%' AND ";
                }
                // №
                if (isset($filter['search']['ACCOUNT'])) {
                    $where .= "b.ACCOUNT LIKE '%{$filter['search']['ACCOUNT']}%' AND ";
                }
                // Дата
                if (isset($filter['search']['DATE'])) {
                    $where .= "LOCATE('{$filter['search']['DATE']}', DATE_FORMAT(i.DATE, '%d.%m.%Y')) > 0 AND ";
                }
                // Дата
                if (isset($filter['search']['DATE_ACT_VR'])) {
                    $where .= "LOCATE('{$filter['search']['DATE_ACT_VR']}', DATE_FORMAT(a.DATE, '%d.%m.%Y')) > 0 AND ";
                }
                // Сумма
                if (isset($filter['search']['PRICE'])) {
                    $where .= "b.PRICE LIKE '%{$filter['search']['PRICE']}%' AND ";
                }
                // Act VR
                if (isset($filter['search']['ACT_VR'])) {
                    $where .= "a.NUMBER LIKE '%{$filter['search']['ACT_VR']}%' AND ";
                }
                if (isset($filter['search']['dateStart'])) {
                    $where .= "(i.DATE >= '{$filter['search']['dateStart']}' AND i.DATE <= '{$filter['search']['dateEnd']}') AND ";
                }
                // Клиент
                if (isset($filter['search']['COMPANY_TITLE'])) {
                    $where .= "b.COMPANY_TITLE LIKE '%{$filter['search']['COMPANY_TITLE']}%' AND ";
                }
                // Объект испытаний
                if (isset($filter['search']['MATERIAL'])) {
                    $where .= "b.MATERIAL LIKE '%{$filter['search']['MATERIAL']}%' AND ";
                }
                // Ответственный
                if (isset($filter['search']['ASSIGNED'])) {
                    $where .= "b.ASSIGNED LIKE '%{$filter['search']['ASSIGNED']}%' AND ";
                }
                // Акт ПП
                if (isset($filter['search']['NUM_ACT_TABLE'])) {
                    $where .= "b.NUM_ACT_TABLE LIKE '%{$filter['search']['NUM_ACT_TABLE']}%' AND ";
                }
                // Договор
                if (isset($filter['search']['DOGOVOR_TABLE'])) {
                    $where .= "b.DOGOVOR_TABLE LIKE '%{$filter['search']['DOGOVOR_TABLE']}%' AND ";
                }
                // Лаборатории
                if (isset($filter['search']['lab'])) {
                    $where .= "b.LABA_ID LIKE '%{$filter['search']['lab']}%' AND ";
                }
                // Стадия
                if (isset($filter['search']['stage'])) {
                    switch ($filter['search']['stage']) { // PRICE OPLATA
                        case '0': // Счет не оплачен
                            $where .= "b.PRICE > 0 and (b.OPLATA = 0 or b.OPLATA is NULL) AND ";
                            break;
                        case '1': // Счет оплачен не полностью
                            $where .= "b.PRICE > 0 and b.OPLATA > 0 and b.OPLATA < b.PRICE AND ";
                            break;
                        case '2': // Счет оплачен полностью
                            $where .= "b.PRICE > 0 and b.OPLATA > 0 and b.OPLATA = b.PRICE AND ";
                            break;
                        case '3': // Акт ВР сформирован и не отправлен
                            break;
                        default: // Все счета
                    }
                }
            }

            // работа с сортировкой
            if (!empty($filter['order'])) {
                if ($filter['order']['dir'] === 'asc') {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'ACCOUNT':
                        $order['by'] = 'year(i.DATE) desc, b.ACCOUNT';
                        break;
                    case 'PRICE':
                        $order['by'] = 'b.PRICE';
                        break;
                    case 'ACT_VR':
                        $order['by'] = 'a.NUMBER';
                        break;
                    case 'DOGOVOR_TABLE':
                        $order['by'] = 'b.DOGOVOR_TABLE';
                        break;
                    case 'DATE':
                        $order['by'] = 'i.DATE';
                        break;
                    case 'DATE_ACT_VR':
                        $order['by'] = 'a.DATE';
                        break;
                    case 'SEND_DATE_ACT_VR':
                        $order['by'] = 'a.SEND_DATE';
                        break;
                    case 'COMPANY_TITLE':
                        $order['by'] = 'b.COMPANY_TITLE';
                        break;
                    case 'MATERIAL':
                        $order['by'] = 'b.MATERIAL';
                        break;
                    case 'ASSIGNED':
                        $order['by'] = 'b.ASSIGNED';
                        break;
                    case 'REQUEST_TITLE':
                        $order['by'] = 'b.REQUEST_TITLE';
                        break;
                    default:
                        $order['by'] = 'b.ID';
                }
            }

            // работа с пагинацией
            if (isset($filter['paginate'])) {
                $offset = 0;
                // количество строк на страницу
                if (isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0) {
                    $length = $filter['paginate']['length'];

                    if (isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT {$offset}, {$length}";
                }
            }
        }
        $where .= "1 ";

        $result = [];

        $stageArray = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '4', 'WON');

        $data = $this->DB->Query(
            "SELECT DISTINCT 
                        b.ID_Z, b.ID, b.REQUEST_TITLE, b.DOGOVOR_TABLE, b.MATERIAL, b.ASSIGNED, b.COMPANY_TITLE, b.ACCOUNT, b.PRICE, b.OPLATA, b.STAGE_ID, 
                        i.DATE, 
                        a.DATE AS DATE_ACT_VR, a.SEND_DATE AS SEND_DATE_ACT_VR, a.NUMBER AS ACT_VR 
                    FROM `ba_tz` b 
                    INNER JOIN `INVOICE` i ON b.ID=i.TZ_ID 
                    INNER JOIN `AKT_VR` a ON b.ID=a.TZ_ID
                    WHERE b.TYPE_ID != '3' AND b.REQUEST_TITLE <> '' AND {$where}
                    GROUP BY b.ID ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT 
                        b.ID val 
                    FROM `ba_tz` b 
                    INNER JOIN `INVOICE` i ON b.ID=i.TZ_ID 
                    INNER JOIN `AKT_VR` a ON b.ID=a.TZ_ID
                    WHERE b.TYPE_ID != '3' AND b.REQUEST_TITLE <> ''
                    GROUP BY b.ID"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT 
                        b.ID val 
                    FROM `ba_tz` b 
                    INNER JOIN `INVOICE` i ON b.ID=i.TZ_ID 
                    INNER JOIN `AKT_VR` a ON b.ID=a.TZ_ID
                    WHERE b.TYPE_ID != '3' AND b.REQUEST_TITLE <> '' AND {$where}
                    GROUP BY b.ID"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $row['STAGE_NUMBER'] = '';
            $row['DATE'] = !empty($row['DATE']) ? date('d.m.Y',  strtotime($row['DATE'])) : '';
            $row['DATE_ACT_VR'] = !empty($row['DATE_ACT_VR']) ? date('d.m.Y',  strtotime($row['DATE_ACT_VR'])) : '';
            $row['SEND_DATE_ACT_VR'] = !empty($row['SEND_DATE_ACT_VR']) ? date('d.m.Y',  strtotime($row['SEND_DATE_ACT_VR'])) : '';

            $assigned = $this->getAssignedByDealId($row['ID_Z']);
            $arrAss = [];
            foreach ($assigned as $item) {
                $arrAss[] = $item['short_name'];
            }

            if (!empty($arrAss)) {
                $row['ASSIGNED'] = implode(', ', $arrAss);
            }

            if (
                in_array($row['STAGE_ID'], $stageArray)
                && !empty($row['PRICE'])
                && empty($row['OPLATA'])
            ) {
                $row['color'] = 'bg-red';
                $row['title'] = 'Счет не оплачен';
            } else if (
                in_array($row['STAGE_ID'], $stageArray)
                && !empty($row['PRICE'])
                && !empty($row['OPLATA'])
                && $row['PRICE'] > $row['OPLATA']
            ) {
                $row['color'] = 'bg-light-pink';
                $row['title'] = 'Счет оплачен не полностью';
            } else if (
                in_array($row['STAGE_ID'], $stageArray)
                && !empty($row['PRICE'])
                && !empty($row['OPLATA'])
                && $row['PRICE'] <= $row['OPLATA'] //TODO: скидка учитывается? см. карточка
            ) {
                $row['color'] = 'bg-green';
                $row['title'] = 'Счет оплачен полностью';
            } else {
                $row['color'] = 'bg-grey';
                $row['title'] = 'Заявка неуспешна';
            }

            $row['PRICE'] = StringHelper::priceFormatRus($row['PRICE']);

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


    /**
     * @return array
     */
    public function getConfirmNotAccountTz()
    {
        $year = date("Y", time()) % 10 ? substr(date("Y", time()), -2) : date("Y", time());

        $lastYear = (int)$year - 1;

        $smtp = $this->DB->Query(
            "SELECT b.ID_Z, b.ID, b.REQUEST_TITLE, b.COMPANY_TITLE 
                    FROM ba_tz b, `CHECK_TZ` ch 
                    WHERE b.ACCOUNT IS NULL AND ch.tz_id = b.ID AND ch.confirm = 1 AND (b.REQUEST_TITLE LIKE '%/{$year}' or b.REQUEST_TITLE LIKE '%/{$lastYear}') GROUP BY b.ID ORDER BY b.ID DESC"
        );

        $result = [];

        while ($row = $smtp->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getConfirmedTz()
    {
        $year = date("Y", time()) % 10 ? substr(date("Y", time()), -2) : date("Y", time());

        $lastYear = (int)$year - 1;

        $smtp = $this->DB->Query(
            "SELECT b.ID_Z, b.ID, b.REQUEST_TITLE, b.COMPANY_TITLE, ch.confirm, b.not_need_send
                    FROM ba_tz b, `CHECK_TZ` ch 
                    WHERE ch.tz_id = b.ID AND (b.REQUEST_TITLE LIKE '%/{$year}' or b.REQUEST_TITLE LIKE '%/{$lastYear}') ORDER BY b.ID DESC"
        );

        $result = [];
        while ($row = $smtp->Fetch()) {
            if ($row['not_need_send']) {
                continue;
            }

            if (empty($result[$row['ID_Z']])) {
                $result[$row['ID_Z']] = $row;
            }

            $result[$row['ID_Z']]['check'][] = $row['confirm'];
        }

        //        echo "<pre>";
        //        print_r($result);
        //        echo "</pre>";
        //        die();

        foreach ($result as $key => $value) {
            if (in_array(0, $value['check'])) {
                unset($result[$key]);
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getReturnedTz($userId)
    {
        $year = date("Y", time()) % 10 ? substr(date("Y", time()), -2) : date("Y", time());

        $lastYear = (int)$year - 1;

        $smtp = $this->DB->Query(
            "
            SELECT bt.ID_Z, bt.ID, bt.REQUEST_TITLE, bt.COMPANY_TITLE, bt.`STAGE_ID`
            FROM CHECK_TZ ct  
            INNER JOIN ba_tz bt ON bt.ID = ct.tz_id
            INNER JOIN ba_laba bl ON bl.HEAD_ID = ct.leader
            WHERE bt.ACCOUNT IS NULL AND (bt.REQUEST_TITLE LIKE '%/{$year}' or bt.REQUEST_TITLE LIKE '%/{$lastYear}')
              AND ct.date_return IS NOT NULL AND IFNULL(ct.date_return, '') <> ''
              AND bl.short_name <> '' AND bl.short_name IS NOT NULL AND ct.confirm <> 1
            GROUP BY ct.tz_id 
            ORDER BY bt.ID DESC
            "
        );

        //        $smtp = $this->DB->Query(
        //            "SELECT b.ID_Z, b.ID, b.REQUEST_TITLE, b.COMPANY_TITLE
        //                    FROM ba_tz b, `CHECK_TZ` ch
        //                    WHERE b.ACCOUNT IS NULL AND ch.tz_id = b.ID AND ch.confirm = 1 AND (b.REQUEST_TITLE LIKE '%/{$year}' or b.REQUEST_TITLE LIKE '%/{$lastYear}') GROUP BY b.ID ORDER BY b.ID DESC");

        $result = [];

        while ($row = $smtp->Fetch()) {
            if ($row['STAGE_ID'] == 'LOSE') {
                continue;
            }
            $result[$row['ID_Z']] = $row;

            $result[$row['ID_Z']]['comments'] = $this->getReturnComments($row['ID']);
        }

        return $result;
    }

    public function getReturnComments($tzId)
    {
        $userModel = new User();

        $result = [];

        $response = $this->DB->Query(
            "
            SELECT *
            FROM check_tz_return ctr
            WHERE ctr.tz_id = '{$tzId}'
            "
        );

        while ($row = $response->Fetch()) {
            $currentUser = $userModel->getUserData($row['user_return']);

            $result[] = [
                'user' => $currentUser,
                'desc' => $row['desc_return'] ? $row['desc_return'] : '--'
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getNotSendedTz()
    {
        $year = date("Y", time()) % 10 ? substr(date("Y", time()), -2) : date("Y", time());

        $lastYear = (int)$year - 1;

        $smtp = $this->DB->Query(
            "
            SELECT bt.ID_Z, bt.ID, bt.REQUEST_TITLE, bt.COMPANY_TITLE, bt.not_need_send
            FROM ba_tz bt
            LEFT JOIN CHECK_TZ ct ON ct.tz_id = bt.ID
            WHERE bt.ACCOUNT IS NULL AND (bt.REQUEST_TITLE LIKE '%/{$year}' or bt.REQUEST_TITLE LIKE '%/{$lastYear}') 
            AND (ct.id IS NULL OR ct.id = '') 
            AND bt.REQUEST_TITLE IS NOT NULL AND bt.REQUEST_TITLE <> ''
            AND bt.TYPE_ID <> 7
            AND bt.sent_out_lims <> 1
            ORDER BY bt.ID DESC
            "
        );

        //        $smtp = $this->DB->Query(
        //            "SELECT b.ID_Z, b.ID, b.REQUEST_TITLE, b.COMPANY_TITLE
        //                    FROM ba_tz b, `CHECK_TZ` ch
        //                    WHERE b.ACCOUNT IS NULL AND ch.tz_id = b.ID AND ch.confirm = 1 AND (b.REQUEST_TITLE LIKE '%/{$year}' or b.REQUEST_TITLE LIKE '%/{$lastYear}') GROUP BY b.ID ORDER BY b.ID DESC");

        $result = [];

        while ($row = $smtp->Fetch()) {
            if ($row['not_need_send']) {
                continue;
            }

            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param $dealId
     */
    public function getCipherByDealId($dealId)
    {
        if ($dealId < 9763) {
            $cipherArr = $this->DB->Query(
                "SELECT * FROM MATERIALS_TO_REQUESTS mtr, probe_to_materials ptm WHERE ptm.material_request_id = mtr.ID AND mtr.ID_DEAL = {$dealId}"
            );
        } else {
            $cipherArr = $this->DB->Query(
                "SELECT * FROM ulab_material_to_request mtr WHERE mtr.deal_id = {$dealId}"
            );
        }
        $result = [];

        while ($cipher = $cipherArr->Fetch()) {
            $result[] = [
                'cipher' => $cipher['cipher']
            ];
        }

        return $result;
    }

    /**
     * добавляет к ответственным в заявке новых ответственных
     * @param int $dealId - ид сделки
     * @param array $arrayUserId - список ид новых ответственных
     */
    public function addAssignedToRequest(int $dealId, array $arrayUserId)
    {
        $userModel = new User();

        $assigned = $userModel->getAssignedByDealId($dealId);

        $userIdList = [];

        foreach ($assigned as $row) {
            $userIdList[] = $row['user_id'];
        }

        $resultArray = array_diff($arrayUserId, $userIdList);

        foreach ($resultArray as $item) {
            $this->DB->Insert('assigned_to_request', ['deal_id' => $dealId, 'user_id' => $item]);
        }
    }

    public function getStageRequestById($dealId)
    {
        $request =  CCrmDeal::GetByID($dealId);

        return $request['STAGE_ID'];
    }

    public function getTakenDealByDealId($dealId)
    {
        $takenDeal = $this->DB->Query("SELECT TAKEN_ID_DEAL FROM ba_tz WHERE ID_Z = {$dealId}")->Fetch();

        return $takenDeal['TAKEN_ID_DEAL'];
    }


    /**
     * @param $takenDealId
     * @return array
     */
    public function getDealByTakenDeal($takenDealId)
    {
        $sql = $this->DB->Query("SELECT ID_Z, REQUEST_TITLE FROM ba_tz WHERE TAKEN_ID_DEAL = {$takenDealId}");

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = [
                'id' =>   $row['ID_Z'],
                'title' =>   $row['REQUEST_TITLE'],
            ];
        }

        return $result;
    }


    public function updateToNewStatus()
    {
        $result = [];

        $status = new Status();

        $response = $this->DB->Query("
         SELECT ID_Z, STAGE_ID FROM ba_tz
        ");

        while ($row = $response->Fetch()) {
            switch ($row['STAGE_ID']) {
                case '1':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                                        VALUES ('{$row['ID_Z']}', 61, 2, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 4, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 9, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 10, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 12, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 13, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 16, '2024-06-10 12:00:00')
                                               ");
                    break;
                case '2':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                                        VALUES ('{$row['ID_Z']}', 61, 2, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 4, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 9, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 10, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 12, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 13, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 16, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 18, '2024-06-10 12:00:00')
                                               ");
                    break;
                case '4':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                                        VALUES ('{$row['ID_Z']}', 61, 2, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 4, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 9, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 10, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 12, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 13, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 16, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 20, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 21, '2024-06-10 12:00:00')
                                               ");
                    break;
                case '5':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 23, '2024-06-10 12:00:00')");
                    break;
                case '6':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 23, '2024-06-10 12:00:00')");
                    break;
                case '7':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 23, '2024-06-10 12:00:00')");
                    break;
                case '8':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 23, '2024-06-10 12:00:00')");
                    break;
                case '9':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 23, '2024-06-10 12:00:00')");
                    break;
                case '10':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 23, '2024-06-10 12:00:00')");
                    break;
                case '11':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 23, '2024-06-10 12:00:00')");
                    break;
                case '12':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 23, '2024-06-10 12:00:00')");
                    break;
                case '13':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 23, '2024-06-10 12:00:00')");
                    break;
                case 'EXECUTING':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                                        VALUES ('{$row['ID_Z']}', 61, 2, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 4, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 9, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 10, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 12, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 13, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 16, '2024-06-10 12:00:00')
                                               ");
                    break;
                case 'FINAL_INVOICE':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                                        VALUES ('{$row['ID_Z']}', 61, 2, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 4, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 9, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 10, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 12, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 13, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 16, '2024-06-10 12:00:00')
                                               ");
                    break;
                case 'LOSE':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 23, '2024-06-10 12:00:00')");
                    break;
                case 'NEW':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                                        VALUES ('{$row['ID_Z']}', 61, 2, '2024-06-10 12:00:00')
                                               ");
                    break;
                case 'PREPARATION':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                                        VALUES ('{$row['ID_Z']}', 61, 2, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 4, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 9, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 10, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 12, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 13, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 16, '2024-06-10 12:00:00')
                                               ");
                    break;
                case 'PREPAYMENT_INVOICE':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                                        VALUES ('{$row['ID_Z']}', 61, 2, '2024-06-10 12:00:00')
                                               ");
                    break;
                case 'WON':
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                                        VALUES ('{$row['ID_Z']}', 61, 2, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 4, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 9, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 10, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 12, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 13, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 16, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 20, '2024-06-10 12:00:00'),
                                               ('{$row['ID_Z']}', 61, 22, '2024-06-10 12:00:00')
                                               ");
                    break;
                case null:
                    $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 23, '2024-06-10 12:00:00')");
                    break;
            }
        }
    }

    public function setWinStatuses()
    {
        $response = $this->DB->Query("SELECT TZ_ID FROM AKT_VR");

        while ($row = $response->Fetch()) {
            $dealId = $this->DB->Query("SELECT ID_Z FROM ba_tz WHERE ID = '{$row['TZ_ID']}'")->Fetch()['ID_Z'];

            $this->DB->Query("DELETE FROM tz_status_history WHERE deal_id = '{$dealId}' AND status_id = 21");

            $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$dealId}', 61, 21, '2024-06-10 12:00:00')");
        }
    }

    public function setWinStatusesWithStageId()
    {
        $response = $this->DB->Query("SELECT ID_Z as deal_id, STAGE_ID stage FROM ba_tz WHERE STAGE_ID = '4' OR STAGE_ID = 'WON'");

        while ($row = $response->Fetch()) {
            if ($row['stage'] === 'WON') {
                $this->DB->Query("DELETE FROM tz_status_history WHERE status_id IN (2, 4, 9, 10, 12, 13, 16, 18, 21, 22) AND deal_id = '{$row['deal_id']}'");

                $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                                    VALUES('{$row['deal_id']}', 61, 2, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 4, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 9, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 10, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 12, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 13, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 16, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 18, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 21, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 22, '2024-06-10 12:00:00')
                                          ");
            } else {
                $this->DB->Query("DELETE FROM tz_status_history WHERE status_id IN (2, 4, 9, 10, 12, 13, 16, 18, 21) AND deal_id = '{$row['deal_id']}'");

                $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) 
                                    VALUES('{$row['deal_id']}', 61, 2, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 4, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 9, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 10, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 12, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 13, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 16, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 18, '2024-06-10 12:00:00'),
                                          ('{$row['deal_id']}', 61, 21, '2024-06-10 12:00:00')
                                          ");
            }
        }
    }

    public function changeStatuses()
    {
        $requirementModel = new Requirement();

        $result = [];

        $response = $this->DB->Query(
            "
                SELECT bt.ID_Z, bt.ID as ba_id, bt.ACT_NUM is_take_probe, bt.OPLATA as oplata, bt.price_discount as price, bt.STAGE_ID as stage_id,
                ugtp.id as is_create_tz,
                k.SEND_DATE as is_send_kp,
                ct.id as is_send_check,
                dog.SEND_DATE as is_send_dogovor,
                td.SEND_DATE as is_send_tz, td.pdf as is_take_tz
                FROM ba_tz bt
                LEFT JOIN ulab_material_to_request umtr ON umtr.deal_id = bt.ID_Z
                LEFT JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
                LEFT JOIN KP k ON k.TZ_ID = bt.ID
                LEFT JOIN CHECK_TZ ct ON ct.tz_id = bt.ID
                LEFT JOIN DOGOVOR dog ON dog.TZ_ID = bt.ID
                LEFT JOIN TZ_DOC td ON td.TZ_ID = bt.ID
                GROUP BY bt.ID_Z
                LIMIT 5
            "
        );

        while ($row = $response->Fetch()) {
            $protocols = [];

            $result[$row['ID_Z']][] = 1;

            $protocolResponse = $this->DB->Query("SELECT NUMBER FROM PROTOCOLS WHERE ID_TZ = {$row['ba_id']}");

            while ($protocolRow = $protocolResponse->Fetch()) {
                $protocols[] = $protocolRow['NUMBER'];
            }

            if (!in_array('', $protocols) && count($protocols) > 0) {
                $result[$row['ID_Z']][] = 20;
            }

            $assignedIds = $requirementModel->getAssignedGostToProbe($row['ID_Z']);

            if (!in_array(0, $assignedIds) && !in_array('', $assignedIds)) {
                $result[$row['ID_Z']][] = 15;
            }

            if ($row['is_take_probe']) $result[$row['ID_Z']][] = 10;
            if ($row['is_create_tz']) $result[$row['ID_Z']][] = 2;
            if ($row['is_send_kp']) $result[$row['ID_Z']][] = 3;
            if ($row['is_send_dogovor']) $result[$row['ID_Z']][] = 7;
            if ($row['is_send_tz']) $result[$row['ID_Z']][] = 8;
            if ($row['is_take_tz']) $result[$row['ID_Z']][] = 9;
            if ($row['is_send_check']) $result[$row['ID_Z']][] = 4;

            if ($row['oplata'] && $row['price']) {
                if (floatval($row['oplata']) < floatval($row['price'])) $result[$row['ID_Z']][] = 13;
                else {
                    $result[$row['ID_Z']][] = 13;
                    $result[$row['ID_Z']][] = 14;
                }

                if ($row['is_take_probe']) {
                    $result[$row['ID_Z']][] = 16;
                    $result[$row['ID_Z']][] = 12;
                }
            }

            $startTrials = $this->getTrials($row['ID_Z']);

            if ($startTrials[0] != 0) {
                foreach ($startTrials as $trial) {
                    $result[$row['ID_Z']][] = $trial;
                }
            }

            if ($row['stage_id'] == '4') {
                $result[$row['ID_Z']][] = 21;
            }

            if ($row['stage_id'] == 'WON') {
                $result[$row['ID_Z']][] = 22;
            }

            if ($row['stage_id'] == 'LOSE') {
                $result[$row['ID_Z']][] = 23;
            }

            $checkTz = $this->getCheck($row['ba_id']);

            if (count($checkTz) <= 0) {
                $result[$row['ID_Z']][] = 4;
                $result[$row['ID_Z']][] = 6;
            }

            if ($row['is_send_check']) {
                if (in_array(1, $checkTz) && !in_array(0, $checkTz)) {
                    $result[$row['ID_Z']][] = 6;
                } else if (!in_array(1, $checkTz) && in_array(0, $checkTz)) {
                    $result[$row['ID_Z']][] = 5;
                }
            }
        }

        foreach ($result as $key => $value) {
            foreach ($value as $val) {
                $this->DB->Query("DELETE FROM tz_status_history WHERE deal_id = '{$key}' AND status_id = '{$val}'");
                $this->DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$key}', 61, '{$val}', '2024-06-10 12:00:00')");
            }
        }
    }

    public function getTrials($deal_id)
    {
        $result = [];

        $response = $this->DB->Query("
            SELECT umtr.id as umtr_id, ugtp.id as ugtp_id, ust.state as ust_stage
            FROM ulab_material_to_request umtr
            LEFT JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            LEFT JOIN ulab_start_trials ust ON ust.ugtp_id = ugtp.id
            WHERE umtr.deal_id = '{$deal_id}'
        ");

        while ($row = $response->Fetch()) {
            $result[$row['ugtp_id']] = $row['ust_stage'];
        }

        if (!in_array('start', $result) && !in_array('pause', $result) && !in_array('', $result)) {
            return [12, 16, 17, 18, 19];
        } else if (in_array('start', $result)) {
            return [12, 16, 17];
        } else {
            return [0];
        }
    }

    public function getCheck($id)
    {
        $result = [];

        $response = $this->DB->Query(
            "
                SELECT confirm FROM CHECK_TZ WHERE tz_id = '{$id}'  
            "
        );

        while ($row = $response->Fetch()) {
            $result[] = $row['confirm'];
        }

        return $result;
    }

    public function copyGostMaterial($materialToRequestId, $count)
    {
        $response = $this->DB->Query("
            SELECT * 
            FROM ulab_gost_to_probe
            WHERE material_to_request_id = '{$materialToRequestId}'
        ");

        $ugtpArr = [];

        while ($row = $response->Fetch()) {
            $ugtpArr[] = $row;
        }

        $materialData = $this->DB->Query("SELECT * FROM ulab_material_to_request WHERE id = '{$materialToRequestId}'")->Fetch();

        $startNumber = $this->DB->Query("SELECT max(probe_number) as probe_number FROM ulab_material_to_request WHERE deal_id = '{$materialData['deal_id']}'")->Fetch()['probe_number'];

        for ($i = 1; $i <= $count; $i++) {
            $number = $startNumber + $i;

            $this->DB->Query(
                "
                INSERT INTO ulab_material_to_request(mtr_id, deal_id, material_id, probe_number, material_number, cipher
                , name_for_protocol, is_only_name_for_protocol, `group`, place, date_probe, quarry_id, in_act, protocol_id, child_deal_id)
                VALUES('{$materialData['mtr_id']}',
                       '{$materialData['deal_id']}',
                       '{$materialData['material_id']}',
                       '{$number}',
                       '{$materialData['material_number']}',
                       '{$materialData['cipher']}',
                       '{$materialData['name_for_protocol']}',
                       '{$materialData['is_only_name_for_protocol']}',
                       '{$materialData['group']}',
                       '{$materialData['place']}',
                       '{$materialData['date_probe']}',
                       '{$materialData['quarry_id']}',
                       '{$materialData['in_act']}',
                       '{$materialData['protocol_id']}',
                       '{$materialData['child_deal_id']}')
                "
            );

            $lastId = $this->DB->Query(
                "
                SELECT id 
                FROM ulab_material_to_request
                WHERE deal_id = '{$materialData['deal_id']}'
                ORDER BY id DESC LIMIT 1;  
                "
            )->Fetch()['id'];

            foreach ($ugtpArr as $row) {
                $json = json_encode(json_decode($row['measuring_sheet'], JSON_UNESCAPED_UNICODE), JSON_UNESCAPED_UNICODE);

                $this->DB->Query(
                    "
                        INSERT INTO ulab_gost_to_probe(method_id, new_method_id, conditions_id, tech_condition_id, price
                        , material_to_request_id, gost_number, measuring_sheet, assigned_id, normative_val, actual_value
                        , `match`, is_confirm_oa, protocol_id, comment)
                        VALUES('{$row['method_id']}',
                               '{$row['new_method_id']}',
                               '{$row['conditions_id']}',
                               '{$row['tech_condition_id']}',
                               '{$row['price']}',
                               '{$lastId}',
                               '{$row['gost_number']}',
                               '{$json}',
                               '{$row['assigned_id']}',
                               '{$row['normative_val']}',
                               '{$row['actual_value']}',
                               '{$row['match']}',
                               '{$row['is_confirm_oa']}',
                               '{$row['protocol_id']}',
                               '{$row['comment']}')
                    "
                );
            }
        }
    }

    public function getNextInvoiceNumber()
    {
        $year = date("Y");
        $curNumber = 1;
        $maxInvoice = $this->DB->Query("SELECT MAX(NUMBER) + 1 as max FROM `INVOICE` WHERE type = 1 and YEAR(DATE) = {$year}")->Fetch();
        if (!$maxInvoice) {
            $curNumber = 1;
        }
        $curNumber = $maxInvoice['max'];

        return $curNumber;
    }

    public function changeMethods()
    {
        $response = $this->DB->Query("SELECT ugtp.id
                                        FROM ulab_material_to_request umtr
                                        LEFT JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
                                        WHERE umtr.deal_id = 15008 AND umtr.id <= 87656 AND umtr.id >= 86533 AND ugtp.method_id = 1334");

        while ($row = $response->Fetch()) {
            $this->DB->Query("UPDATE ulab_gost_to_probe SET method_id = 1337 WHERE id = {$row['id']}");
        }
    }

    public function deleteTechSpec()
    {
        $response = $this->DB->Query(
            "
            SELECT id
            FROM ulab_material_to_request umtr
            WHERE umtr.deal_id = 14830
            "
        );

        while ($row = $response->Fetch()) {
            $this->DB->Query("UPDATE ulab_gost_to_probe SET tech_condition_id = '' WHERE material_to_request_id = '{$row['id']}'");
        }
    }

    public function updateNeedleSend($dealId, $newNeedle)
    {
        $this->DB->Query("UPDATE ba_tz SET not_need_send = '{$newNeedle}' WHERE ID_Z = '{$dealId}'");
    }

    public function getActArray($dealId)
    {
        $result = [];

        $response = $this->DB->Query("SELECT * FROM ACT_BASE WHERE ID_Z = '{$dealId}'");

        while ($row = $response->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function updateLoseComment($dealId, $comment)
    {
        $this->DB->Query("UPDATE ba_tz SET lose_comment = '{$comment}' WHERE ID_Z = '{$dealId}'");
    }

    public function getRequestType($dealId)
    {
        $result = $this->DB->Query("SELECT TYPE_ID FROM ba_tz WHERE ID_Z = '{$dealId}'")->Fetch()['TYPE_ID'];

        return $result;
    }

    public function getTakenDealId($dealId)
    {
        $result = $this->DB->Query("SELECT TAKEN_ID_DEAL FROM ba_tz WHERE ID_Z = '{$dealId}'")->Fetch()['TAKEN_ID_DEAL'];

        return $result;
    }

    public function getMaterialsForDeal($dealId)
    {
        $result = [];

        $response = $this->DB->Query(
            "
            SELECT DISTINCT m.NAME
            FROM ulab_material_to_request umtr
            INNER JOIN MATERIALS m ON m.ID = umtr.material_id
            WHERE umtr.deal_id = '{$dealId}'
            "
        );

        while ($row = $response->Fetch()) {
            $result[] = $row['NAME'];
        }

        return $result;
    }

    public function getDateStartIsp($dealId)
    {
        $result = [];
    }

    public function getCommentsHistoryByDealId($dealId)
    {
        $userModel = new User();

        $result = [];

        $response = $this->DB->Query(
            "
            SELECT * 
            FROM deals_comments_history
            WHERE deal_id = '{$dealId}'
            ORDER BY id DESC
            "
        );

        $greyBg = true;

        while ($row = $response->Fetch()) {
            $row['user_short'] = $userModel->getUserData($row['user_id'])['short_name'];
            $row['date'] = date('d.m.Y H:i', strtotime($row['date_comment']));

            if ($greyBg) $row['bg'] = 'bg-light-grey';
            else $row['bg'] = '';

            $greyBg = !$greyBg;

            $result[] = $row;
        }

        return $result;
    }
    public function getDateRequestUPD($dealId)
    {
        $result = $this->DB->Query("SELECT date FROM request_upd WHERE deal_id = '{$dealId}'")->Fetch()['date'];
        if (empty($result)) {
            $result2 = null;
        } else {
            $result2 = date('d.m.Y', strtotime($result));
        }
        return $result2;
    }
    public function requestUPD($dealId, $text)
    {
        $userModel = new User();
        $user = $userModel->getUserData($_SESSION['SESS_AUTH']['USER_ID']);
        $userfio = $user['user_name'];
        CModule::IncludeModule('im');
        $res = $this->DB->Query("SELECT `REQUEST_TITLE`, `COMPANY_TITLE` FROM `ba_tz` WHERE `ID_Z` = {$dealId}")->Fetch();
        if (!empty($text)) {
            $ar = array(
                "TO_CHAT_ID" => 3523, // ID чата
                "FROM_USER_ID" => 61, // ID пользователя состоящего в чате
                "MESSAGE" => "По заявке [URL=/ulab/request/card/{$dealId}]{$res['REQUEST_TITLE']} {$res['COMPANY_TITLE']}[/URL] необходимо сформировать УПД. Лицо, запросившее УПД: " . $userfio . ". Комментарий: " . "\"" . $text . "\"", // Произвольный текст
            );
        } else {
            $ar = array(
                "TO_CHAT_ID" => 3523, // ID чата
                "FROM_USER_ID" => 61, // ID пользователя состоящего в чате
                "MESSAGE" => "По заявке [URL=/ulab/request/card/{$dealId}]{$res['REQUEST_TITLE']} {$res['COMPANY_TITLE']}[/URL] необходимо сформировать УПД. Лицо, запросившее УПД: " . $userfio . ".", // Произвольный текст
            );
        }
        CIMChat::AddMessage($ar);
        $currentDate = date('Y-m-d');
        $this->DB->Query(
            "
            INSERT INTO request_upd(deal_id, date, message) 
            VALUES('{$dealId}', '{$currentDate}', '{$text}')  
            "
        );
    }

    public function getChangesHistoryByDealId($tzId)
    {
        $result = [];
        $userModel = new User();
        $response = $this->DB->Query(
            "
            SELECT * 
            FROM tz_changes_history
            WHERE tz_id = '{$tzId}'
            ORDER BY id DESC
            "
        );

        while ($row = $response->Fetch()) {
            $row['user_short'] = $userModel->getUserData($row['user_id'])['short_name'];
            $result[] = $row;
        }

        return $result;
    }

    public function getChangesHistoryDurationByDealId($dealId)
    {
        $result = [];
        $userModel = new User();
        $response = $this->DB->Query(
            "
            SELECT * 
            FROM ulab_tz_lab_duration_history
            WHERE deal_id = '{$dealId}'
            ORDER BY id DESC
            "
        );

        while ($row = $response->Fetch()) {
            $labId = $row['lab_id'];
            $sql = $this->DB->Query("SELECT HEAD_ID FROM ba_laba WHERE ID = '{$labId}'")->Fetch()['HEAD_ID'];
            $row['user_short'] = $userModel->getUserData($sql)['short_name'];
            $result[] = $row;
        }

        return $result;
    }

    public function getActDataByDealID($dealId)
    {
        $response = $this->DB->Query("SELECT * FROM ba_tz WHERE ID_Z = '{$dealId}'")->Fetch();

        return $response;
    }

    public function getDeterminedCharacteristicsFromProtocolId($protocolId)
    {
        $result = [];

        $response = $this->DB->Query(
            "
            SELECT DISTINCT um.name
            FROM ulab_gost_to_probe ugtp
            INNER JOIN ulab_methods um ON um.id = ugtp.method_id
            WHERE ugtp.protocol_id = '{$protocolId}'
            "
        );

        while ($row = $response->Fetch()) {
            $result[] = $row['name'];
        }

        return $result;
    }
}
