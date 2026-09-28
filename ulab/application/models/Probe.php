<?php

/**
 * Модель для работы с Пробами
 * Class Probe
 */
class Probe extends Model
{
    /**
     * @param $probeId
     * @param $action
     */
    public function addHistory($probeId, $action)
    {
        $data = [
            'user_id' => $_SESSION['SESS_AUTH']['USER_ID'],
            'probe_id' => $probeId,
            'date' => date('Y-m-d H:i:s'),
            'action' => $action,
        ];

        $sqlData = $this->prepearTableData('ulab_probe_history', $data);

        $this->DB->Insert('ulab_probe_history', $sqlData);
    }


    /**
     * @param $probeId
     * @return array
     */
    public function getHistory($probeId)
    {
        $userModel = new User();

        $sql = $this->DB->Query("select * from ulab_probe_history where probe_id = {$probeId} order by id asc");

        $result = [];

        while ($row = $sql->Fetch()) {
            $user = $userModel->getUserData($row['user_id']);
            $row['short_name'] = $user['short_name'];
            $row['date'] = date('d.m.Y H:i:s', strtotime($row['date']));

            $result[] = $row;
        }

        return $result;
    }


    /**
     * редактирует данные пробы
     * @param $id
     * @param $data
     */
    public function edit($id, $data)
    {
        $sqlData = $this->prepearTableData('ulab_material_to_request', $data);

        $this->DB->Update('ulab_material_to_request', $sqlData, "where id = {$id}");
    }


    /**
     * получить одну пробу по ид
     * @param $id
     * @return array|false
     */
    public function get($id)
    {
        return $this->DB->Query("select * from ulab_material_to_request where id = {$id}")->Fetch();
    }


    /**
     * копирует информацию из пробы в пробу
     * @param $probeId
     * @param $sourceProbeId
     */
    public function copyProbeInfo($probeId, $sourceProbeId)
    {
        $sourceProbe = $this->get($sourceProbeId);

        $data = [
            'cipher' => $this->quoteStr($sourceProbe['cipher']),
            'name_for_protocol' => $this->quoteStr($sourceProbe['name_for_protocol']),
            'place' => $this->quoteStr($sourceProbe['place']),
            'date_probe' => $this->quoteStr($sourceProbe['date_probe']),
            'quarry_id' => $sourceProbe['quarry_id'],
        ];

        $this->DB->Update('ulab_material_to_request', $data, "where id = {$probeId}");
    }


    /**
     * получает список проб в акте
     * @param $dealId
     * @return array
     */
    public function getProbeInAct($dealId)
    {
        $sql = $this->DB->Query("select * from ulab_material_to_request where deal_id = {$dealId} and `in_act` = 1");

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getProbeInAct2($dealId)
    {
        $requirement = new Requirement();
        $sql = $this->DB->Query("select * from ulab_material_to_request where deal_id = {$dealId} and `cipher` = ''");
        $actBase = $requirement->getActBase($dealId);
        $result = [];

        while ($row = $sql->Fetch()) {
            $row['cipher'] = $actBase['ACT_NUM'] . '.' . ($row['probe_number'] + 2) . '/26';
            $result[] = $row;
        }

        return $result;
    }

    public function saveActProbeChanges(int $dealId, array $rows): int
    {
        $updated = 0;

        foreach ($rows as $umtrId => $fields) {
            $umtrId = (int)$umtrId;
            if ($umtrId <= 0) continue;

            $upd = [];

            // ----- cipher: обновляем ТОЛЬКО если был "Присвоить шифр"
            $cipherAssigned = (int)($fields['cipher_assigned'] ?? 0);

            if ($cipherAssigned === 1) {
                $cipher = trim((string)($fields['cipher'] ?? ''));
                if ($cipher !== '') {
                    $cipherEsc = $cipher;
                    $upd['cipher'] = "'{$cipherEsc}'";
                    $upd['in_act'] = 1;
                }
            }

            // ----- date_probe
            if (array_key_exists('date_probe', $fields)) {
                $dateProbe = trim((string)$fields['date_probe']);
                if ($dateProbe === '') {
                    $upd['date_probe'] = "NULL";
                } else {
                    $dateEsc = $dateProbe;
                    $upd['date_probe'] = "'{$dateEsc}'";
                }
            }

            // ----- place
            if (array_key_exists('place', $fields)) {
                $place = trim((string)$fields['place']);
                if ($place === '') {
                    $upd['place'] = "NULL";
                } else {
                    $placeEsc = $place;
                    $upd['place'] = "'{$placeEsc}'";
                }
            }

            // ----- quarry_id
            if (array_key_exists('quarry_id', $fields)) {
                $quarryId = (int)($fields['quarry_id'] ?? 0);
                $upd['quarry_id'] = ($quarryId > 0) ? $quarryId : "NULL";
            }

            if (empty($upd)) continue;

            $this->DB->Update(
                "ulab_material_to_request",
                $upd,
                "WHERE id = {$umtrId} AND deal_id = {$dealId}"
            );
            $this->addHistory($umtrId, "Пробу принял");
            $updated++;
        }

        return $updated;
    }

    /**
     * получение рук лабораторий по пробе
     * @param $umtrId
     * @return array
     */
    public function getLabHeadByProbe($umtrId)
    {
        $sql = $this->DB->Query(
            "select distinct 
                        new_method_id as method_id 
                    from ulab_gost_to_probe
                    where material_to_request_id = {$umtrId}"
        );

        $methodModel = new Methods();
        $labModel = new Lab();
        $userModel = new User();

        $heads = [];

        while ($row = $sql->Fetch()) {

            $labs = $methodModel->getLab($row['method_id']);

            foreach ($labs as $lab) {
                $user = $userModel->checkHeader($labModel->getDepartmentIdByLabId($lab));

                if (empty($user) || in_array($lab, [5, 6])) {
                    continue;
                }

                $heads[$lab] = $user;
            }
        }

        return $heads;
    }


    /**
     * отправить пробу руководителям лабораторий
     * @param $umtrId
     */
    public function transferProbe($umtrId)
    {
//        $this->DB->Query("update ulab_material_to_request set `state` = 2 where id = {$umtrId}");

        $heads = $this->getLabHeadByProbe($umtrId);

        foreach ($heads as $user) {
            $this->DB->Insert('probe_to_lab', ['umtr_id' => $umtrId, 'user_id' => $user['user_id']], '', false, '', true);
        }

        $this->addHistory($umtrId, "Передал в пробоподготовку");
    }


    /**
     * пользователь принимает пробу
     * @param $umtrId
     * @param $userId
     */
    public function takeProbe($umtrId, $userId)
    {
        $this->DB->Update("probe_to_lab", ['accept_probe' => 1], "where umtr_id = {$umtrId} and user_id = {$userId}");

        $this->addHistory($umtrId, "Проба принята руководителем лаборатории на испытания");
    }


    /**
     * получить информацию о руководителей, которым отправилась проба
     * @param $umtrId
     * @return array
     */
    public function getAcceptStatus($umtrId)
    {
        $userModel = new User();

        $sql = $this->DB->Query("select * from probe_to_lab where umtr_id = {$umtrId}");

        $result = [];

        while ($row = $sql->Fetch()) {
            $row['user_info'] = $userModel->getUserData($row['user_id']);
            $result[] = $row;
        }

        return $result;
    }


    /**
     * возвращает статус принятой пробы у пользователя. -1 - не отправлено пользователю. 0 - еще не принято. 1 - принято.
     * @param $umtrId
     * @param $userId
     * @return int|mixed
     */
    public function getAcceptStatusUser($umtrId, $userId)
    {
        $row = $this->DB->Query("select accept_probe from probe_to_lab where umtr_id = {$umtrId} and user_id = {$userId}")->Fetch();

        if (isset($row['accept_probe'])) {
            return $row['accept_probe'];
        } else {
            return -1;
        }
    }


    public function getCipherByMtrID($mtrID)
    {

        $result = [];

        $res = $this->DB->Query("SELECT ptm.`cipher`, ptm.`id`
										FROM `MATERIALS_TO_REQUESTS` mtr, `probe_to_materials` ptm
										WHERE ptm.`material_request_id` = mtr.`ID` AND mtr.`ID`= {$mtrID}
										");

        while ($row = $res->Fetch()) {

            $rowArr = [
                'cipher' => $row['cipher'],
                'id' => $row['id']
            ];

//			$result[] = $row['cipher'];
            $result[] = $rowArr;

        }

        return $result;

    }


    /**
     * @param $umtrId
     * @return mixed
     */
    public function getDealIdByProbe($umtrId)
    {
        $dealId = $this->DB->Query("select deal_id from ulab_material_to_request where id = {$umtrId}")->Fetch();

        return $dealId['deal_id'];
    }

    /**
     * @param $umtrId
     * @return mixed
     */
    public function getActIdByProbeAndDeal($umtrId, $dealId)
    {
        $act = $this->DB->Query(
            "
            SELECT ab.ID
            FROM ACT_BASE ab
            INNER JOIN ulab_material_to_request umtr ON umtr.id = '{$umtrId}'
            WHERE ab.ID_Z = '{$dealId}' AND umtr.cipher LIKE CONCAT(ab.ACT_NUM, '.%')
        ")->Fetch();

        return $act['ID'];
    }


    public function getProbeByDealId($dealId): array
    {
        $methodModel = new Methods();
        $userModel = new User();
        $result = [];
        $gosts = [];
        $tester = [];

        $res = $this->DB->Query("SELECT *, umtr.id as umtr_id 
										from ulab_material_to_request as umtr
										left join ulab_gost_to_probe as ugtp ON ugtp.material_to_request_id = umtr.id
										left join MATERIALS m on umtr.material_id = m.ID
								WHERE umtr.deal_id = {$dealId} order by umtr.probe_number, ugtp.gost_number");

        while ($row = $res->Fetch()) {

            $method = $methodModel->get($row['method_id']);
            if (!empty($row['assigned_id'])) {
                $test = $userModel->getUserShortById($row['assigned_id']);
            }

            $gosts[$row['umtr_id']][] = $method['view_gost'];
            $tester[$row['umtr_id']][] = $test['short_name'];

            $result[$row['umtr_id']] = [
                'cipher' => !empty($row['cipher']) ? $row['cipher'] : 'Не присвоен шифр',
                'material' => $row['NAME'],
                'gost' => $gosts[$row['umtr_id']],
                'tester' => $tester[$row['umtr_id']],
                'irrevocable' => $row['irrevocable'],
                'date_probe' => !empty($probe['date_probe']) ? date('d.m.Y', strtotime($probe['date_probe'])) : ''
            ];

        }

        return $result;
    }


    /**
     * Создает или обновляет Акт приемки проб
     * @param $data
     */
    public function insertUpdateActProbe($data)
    {
        $historyModel = new History();
        $userModel = new User();
        $requestModel = new Request();
        $probeModel = new Probe();

//        $requestData = $requestModel->getDealById($data['deal_id']);

        $curYear = date("Y");

        $actData = [
            'ACT_DATE' => $data['actDate'],
            'ID_Z' => $data['deal_id'],
            'ID_TZ' => $data['tz_id'],
            'PLACE_PROBE' => $data['samplePlace'],
            'DATE_PROBE' => $data['sampleDate'],
            'PROBE_PROIZV' => $data['sampleMaker'],
            'deliveryman' => $data['deliveryman'],
            'act_type' => $data['actType'],
            'SELECTION_TYPE' => isset($data['exampleCheck1']) ? 1 : 0,
        ];

        $requestData = [
            'SELECTION_TYPE' => isset($data['exampleCheck1']) ? 1 : 0,
            'DESCRIPTION' => $data['description'],
            'QUARRY_ID' => $data['quarry'],
            'DATE_ACT' => $data['actDate'],
            'PROBE_IN_LAB' => 1,
        ];

        if (!empty($data['act_id'])) { // обновление
            $historyType = "Обновление АКТа приемки проб";
            $sqlData = $this->prepearTableData('ACT_BASE', $actData);
            $this->DB->Update('ACT_BASE', $sqlData, "where ID = {$data['act_id']}");
        } else { // добавление
            $historyType = "Формирование АКТа приемки проб";
            $maxNumAct = $this->DB->Query(
                "select max(CONVERT(ACT_NUM, UNSIGNED INTEGER )) as max 
                FROM `ACT_BASE` 
                WHERE `act_type` = {$data['actType']} AND `ACT_DATE` > '{$curYear}-01-01'"
            )->Fetch();

            $numAct = $maxNumAct['max'] + 1;

            $actData['ACT_NUM'] = $numAct;

            $sqlData = $this->prepearTableData('ACT_BASE', $actData);
            $this->DB->Insert('ACT_BASE', $sqlData);

            $requestData['ACT_NUM'] = $numAct;
            $requestData['NUM_ACT_TABLE'] = "$numAct/$curYear";
        }

        $requestSqlData = $this->prepearTableData('ba_tz', $requestData);
        $requestModel->updateTz($data['deal_id'], $requestSqlData);

        // TODO: для старой версии
        $where = "mtr.ID_DEAL = {$data['deal_id']}";

        $stmpMtr = $this->DB->Query(
            "SELECT 
				@row_number:=CASE
					WHEN @deal = mtr.ID_DEAL THEN @row_number + 1
					ELSE 1
				END AS num,
				@deal:=mtr.ID_DEAL as ID_DEAL, ptm.id AS ptm_id, mtr.ID mtr_id, mtr.ID_MATERIAL,
				ab.ACT_NUM, ab.ACT_DATE 
			FROM 
				MATERIALS_TO_REQUESTS AS mtr, ACT_BASE AS ab, 
				probe_to_materials as ptm, 
				(SELECT @deal:=0,@row_number:=0) as t
			WHERE 
				mtr.ID_DEAL = ab.ID_Z AND ptm.material_request_id = mtr.ID AND {$where}
			ORDER BY 
				mtr.ID_DEAL, mtr.ID, ptm.id"
        );

        while ($row = $stmpMtr->Fetch()) {
            $date = strtotime($row['ACT_DATE']);
            $year = date("Y", $date) % 10 ? substr(date("Y", $date), -2) : date("Y", $date);

            $cipher = $row['ACT_NUM'] . '.' . $row['num'] . '/' . $year;

            $dateProbe = [
                'cipher' => "'{$cipher}'"
            ];

            $this->DB->Update("probe_to_materials", $dateProbe, "WHERE id = {$row['ptm_id']}");
        }


        // собирает шифры для проб в заявке, для таблицы ulab_material_to_request
        $umtrWhere = "umtr.deal_id = {$data['deal_id']}";

        $umtr = $this->DB->Query(
            "SELECT
               @row_number:=CASE
                WHEN @deal = umtr.deal_id THEN @row_number + 1
                ELSE 1
            END AS num,
              @deal:=umtr.deal_id as deal_id, umtr.id umtr_id, umtr.material_id, umtr.cipher, ab.ACT_NUM, ab.ACT_DATE, @row_number, @deal, umtr.material_number
            FROM 
              ulab_material_to_request AS umtr, 
              ACT_BASE AS ab, 
              (SELECT @deal:=0,@row_number:=0) as t
            WHERE 
              umtr.deal_id = ab.ID_Z AND {$umtrWhere} 
            ORDER BY 
              umtr.material_number, umtr.id"
        );

        $i = 1;
        while ($row = $umtr->Fetch()) {
            $date = strtotime($row['ACT_DATE']);
            $year = date("Y", $date) % 10 ? substr(date("Y", $date), -2) : date("Y", $date);


            $cipher = $row['ACT_NUM'] . '.' . $i . '/' . $year;

            $dateProbe = [
                'cipher' => "'{$cipher}'"
            ];

            $this->DB->Update("ulab_material_to_request", $dateProbe, "WHERE id = {$row['umtr_id']}");
            $i++;

            $probeModel->addHistory($row['umtr_id'], "Пробу принял");
        }


        $currentUser = $userModel->getCurrentUser();
        $history = [
            'DATE' => $date,
            'ASSIGNED' => $currentUser['NAME'] . ' ' . $currentUser['LAST_NAME'],
            'TZ_ID' => $data['tz_id'],
            'USER_ID' => $currentUser['ID'],
            'TYPE' => $historyType,
            'REQUEST' => $requestData['TITLE'],
        ];
        $historyModel->addHistory($history);
    }

    /**
     * Создает или обновляет Акт приемки проб
     * @param $data
     */
    public function insertUpdateActProbeNew($data)
    {
        $historyModel = new History();
        $userModel = new User();
        $requestModel = new Request();
        $requirementModel = new Requirement();

        $tzId = $requirementModel->getTzIdByDealId((int)$data['deal_id']);
//        $probe = $requirementModel->getProbeFromTzWithoutAct($data['deal_id']);

        $probes = $data['probe']['umtr_id'];

        $curYear = date("Y");
        $curYearCipher = date("y");

        //$data['ACT_NUM'] = $this->getNewActNumber();
        $actData = [
            'ACT_DATE' => $data['ACT_DATE'],
            'ACT_NUM' => $data['ACT_NUM'],
//			'ACT_NUM' => $number,
            'ID_Z' => $data['deal_id'],
            'ID_TZ' => $tzId,
            'deliveryman' => $data['deliveryman'],
            'act_type' => 1,
            'creater' => $_SESSION['SESS_AUTH']['USER_ID']
        ];

        if (!empty($data['act_id'])) { // обновление
            $historyType = "Обновление АКТа приемки проб";
            $sqlData = $this->prepearTableData('ACT_BASE', $actData);
            $this->DB->Update('ACT_BASE', $sqlData, "where ID = {$data['act_id']}");
        } else { // добавление
            $historyType = "Формирование АКТа приемки проб";

            $sqlData = $this->prepearTableData('ACT_BASE', $actData);
            $this->DB->Insert('ACT_BASE', $sqlData);

            $requestData['ACT_NUM'] = $data['ACT_NUM'];
            $requestData['DATE_ACT'] = $data['ACT_DATE'];
            $requestData['NUM_ACT_TABLE'] = $data['ACT_NUM'] . "/$curYear";
            $requestData['PROBE_IN_LAB'] = 1;
            $requestData['SELECTION_TYPE'] = $data['SELECTION_TYPE'];
        }

        $requestSqlData = $this->prepearTableData('ba_tz', $requestData);
        $requestModel->updateTz($data['deal_id'], $requestSqlData);

        $i = 1;
        foreach ($probes as $val) {

            $currentVal = [];

            $currentVal['cipher'] = $data['ACT_NUM'] . '.' . $i . "/$curYearCipher";
            $currentVal['in_act'] = 1;
            if (!empty($data['place_all'])) {
                $currentVal['place'] = $data['place_all'];
            }
            if (!empty($data['date_all']) && $data['date_all'] !== '0000-00-00') {
                $currentVal['date_probe'] = $data['date_all'];
            }
            if (!empty($data['quarry_all'])) {
                $currentVal['quarry_id'] = $data['quarry_all'];
            }

            $this->setLabHeaderByUmtrId($val);

            $umtrSqlData = $this->prepearTableData('ulab_material_to_request', $currentVal);

            $this->DB->Update('ulab_material_to_request', $umtrSqlData, "where id = {$val} AND in_act <> 1");
            $i++;

            $this->addHistory($val, "Пробу принял");
        }


        $date = date('Y-m-d h:i:s');
        $currentUser = $userModel->getCurrentUser();
        $history = [
            'DATE' => $date,
            'ASSIGNED' => $currentUser['NAME'] . ' ' . $currentUser['LAST_NAME'],
            'TZ_ID' => $data['tz_id'],
            'USER_ID' => $currentUser['ID'],
            'TYPE' => $historyType,
            'REQUEST' => $requestData['TITLE'],
        ];
        $historyModel->addHistory($history);
    }

    public function getNewActNumber()
    {

        $curYear = date("Y");

        $maxNumAct = $this->DB->Query(
            "select max(CONVERT(ACT_NUM, UNSIGNED INTEGER )) as max 
                FROM `ACT_BASE` 
                WHERE `act_type` = 1 AND `ACT_DATE` > '{$curYear}-01-01'"
        )->Fetch();

        $num = $maxNumAct['max'] + 1;

        return $num;
    }


    /**
     * @param $umtrId
     * @return array
     */
    public function getProbeLab($umtrId)
    {
        $sql = $this->DB->Query("select * from `probe_to_lab`  WHERE `umtr_id` = {$umtrId}");

        $result = [];
        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param $idReq
     */
    public function acceptProbe($idReq)
    {
        $currentDate = date('Y-m-d H:i:s');
        $this->DB->Query("UPDATE `probe_to_lab` SET `accept_probe` = 1, accept_date = '{$currentDate}' WHERE `id` = {$idReq}");
        $sql = $this->DB->Query("select * from `probe_to_lab`  WHERE `id` = {$idReq}")->Fetch();
        $id = $sql['umtr_id'];
        $this->addHistory($id, "Проба принята руководителем лаборатории на испытания");
    }

    /**
     * @param $idReq
     */
    public function removeAcceptProbe($idReq)
    {
        $this->DB->Query("UPDATE `probe_to_lab` SET `accept_probe` = 0 WHERE `id` = {$idReq}");
    }

    public function setLabHeaderByUmtrId($umtr_id)
    {
        $sql = $this->DB->Query("select * 
							from ulab_material_to_request as umtr
							join ulab_gost_to_probe as ugtp on umtr.id = material_to_request_id 
							where umtr.id = {$umtr_id}");

        $methodModel = new Methods();
        $labModel = new Lab();
        $userModel = new User();

        $users = [];
        $labList = [];

        while ($row = $sql->Fetch()) {
            $labs = $methodModel->getLab($row['method_id']);
            foreach ($labs as $lab) {
                $depId = $labModel->getDepartmentIdByLabId($lab);
                $labList[$lab] = $depId;
                $user = $userModel->checkHeader($depId);
                if (empty($user) || in_array($lab, [5, 6])) {
                    continue;
                }

                $userArr[$user['user_id']] = $user['user_id'];
            }

        }

        $headerArr = $this->checkLabHeaderByUmtrId($umtr_id);

        if (!empty($headerArr)) {
            $newHeader = array_diff($headerArr, $userArr);

            foreach ($newHeader as $userId) {

                $data = ['umtr_id' => $umtr_id,
                    'user_id' => $userId];

                $this->DB->Insert('probe_to_lab', $data);
            }
        } else {
            foreach ($userArr as $userId) {

                $data = ['umtr_id' => $umtr_id,
                    'user_id' => $userId];

                $this->DB->Insert('probe_to_lab', $data);
            }
        }

    }

    public function checkLabHeaderByUmtrId($umtr_id)
    {
        $sql = $this->DB->Query("select * 
							from probe_to_lab as ptl 
							where umtr_id = {$umtr_id}");

        $users = [];

        while ($row = $sql->Fetch()) {
            $users[] = $row['user_id'];
        }

        return $users;
    }

    public function setSelectionType($id, $prop)
    {

        $st = 'NULL';
        if ($prop == 'true') {
            $st = 1;
        }

        $a = $this->DB->Query("update ba_tz set `SELECTION_TYPE` = {$st} WHERE `ID_Z` = {$id}");
    }


    public function getProbeJournal($filter)
    {
        $result = [];

        $where = "";
        $limit = "";
        $order = [
            'by' => 'id',
            'dir' => 'DESC'
        ];
        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                if (isset($filter['search']['deal_id'])) {
                    $where .= "umtr.deal_id = {$filter['search']['deal_id']} and ";
                }
                if (isset($filter['search']['material_id'])) {
                    $where .= "umtr.material_id = {$filter['search']['material_id']} and ";
                }
                if (isset($filter['search']['probe_id'])) {
                    $where .= "umtr.id = {$filter['search']['probe_id']} and ";
                }
            }

            // работа с сортировкой
            if (!empty($filter['order'])) {
                if ($filter['order']['dir'] === 'asc') {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'material_name':
                        $order['by'] = 'mater.NAME';
                        break;
                    case 'name_for_protocol':
                        $order['by'] = 'umtr.name_for_protocol';
                        break;
                }
            }

            // работа с пагинацией
            $offset = 0;
            if (isset($filter['paginate'])) {
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


        $data = $this->DB->Query(
            "select 
                umtr.*,
                mater.NAME material_name, mater.GROUPS,
                ugtp.id as ugtp_id, ugtp.new_method_id as method_id, ugtp.measuring_sheet,  
                ugtp.tech_condition_id as condition_id, ugtp.assigned_id, ugtp.price, ugtp.gost_number, 
                res.actual_value, umtr.id as umtr_id
            from ulab_material_to_request as umtr
            left join MATERIALS as mater on mater.ID = umtr.material_id
            left join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
            left join ulab_trial_results as res on res.gost_to_probe_id = ugtp.id
            WHERE {$where} AND umtr.in_act <> 1
            group by umtr.id
            ORDER BY umtr.material_number, umtr.probe_number {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "select 
                umtr.id
            from ulab_material_to_request as umtr
            left join MATERIALS as mater on mater.ID = umtr.material_id
            left join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
            left join ulab_trial_results as res on res.gost_to_probe_id = ugtp.id
            where umtr.deal_id = {$filter['search']['deal_id']} AND umtr.in_act <> 1
            group by umtr.id"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "select 
                umtr.id
            from ulab_material_to_request as umtr
            left join MATERIALS as mater on mater.ID = umtr.material_id
            left join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
            left join ulab_trial_results as res on res.gost_to_probe_id = ugtp.id
            WHERE {$where} AND umtr.in_act <> 1
            group by umtr.id"
        )->SelectedRowsCount();

        $i = $offset;
        while ($row = $data->Fetch()) {
            $row['n'] = ++$i;

            if ($row['date_probe'] == '0000-00-00') {
                $row['date_probe'] = '';
            }

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    public function getProbeJournal2($filter)
    {
        $materialModel = new Material();
        $requirement = new Requirement();
        $user = new User();
        $probe = new Probe();
        $result = [];

        $where = "";
        $limit = "";
        $order = [
            'by' => 'id',
            'dir' => 'DESC'
        ];
        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                if (isset($filter['search']['cipher'])) {
                    $input = trim((string)$filter['search']['cipher']);

                    if ($input !== '') {
                        $parts = preg_split('/\s*,\s*/', $input, -1, PREG_SPLIT_NO_EMPTY);

                        $exact = [];   // точные шифры '277.1/26'
                        $rangeClauses = [];

                        // парсим "277.1" или "277.1/26"
                        $parse = function (string $s) {
                            $s = trim($s);
                            if (preg_match('~^(\d+)\.(\d+)(?:/26)?$~', $s, $m)) {
                                $num = (int)$m[1];  // 277
                                $party = (int)$m[2];  // 1,2,3...
                                return [
                                    'num' => $num,
                                    'party' => $party,
                                    'cipher' => $num . '.' . $party . '/26',
                                ];
                            }
                            return null;
                        };

                        foreach ($parts as $p) {
                            // диапазон "a-b"
                            if (preg_match('/^(.+?)\s*-\s*(.+)$/u', $p, $m)) {
                                $from = $parse($m[1]);
                                $to = $parse($m[2]);

                                if ($from && $to) {
                                    // если номер одинаковый (как в вашем примере 277.1-277.11)
                                    if ($from['num'] === $to['num']) {
                                        $num = $from['num'];
                                        $a = $from['party'];
                                        $b = $to['party'];
                                        if ($a > $b) {
                                            [$a, $b] = [$b, $a];
                                        }

                                        $rangeClauses[] =
                                            "(SUBSTRING_INDEX(umtr.cipher,'.',1) = '$num'
                              AND CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(umtr.cipher,'/',1),'.',-1) AS UNSIGNED) BETWEEN $a AND $b)";
                                    } else {
                                        // если вдруг когда-нибудь будет диапазон между разными номерами — скажите, допишем логику
                                    }
                                }

                                continue;
                            }

                            // одиночное значение
                            $one = $parse($p);
                            if ($one) {
                                $exact[] = $one['cipher'];
                            }
                        }

                        $clauses = [];

                        if (!empty($exact)) {
                            $exact = array_values(array_unique($exact));
                            $quoted = array_map(fn($v) => "'" . addslashes($v) . "'", $exact);
                            $clauses[] = "umtr.cipher IN (" . implode(',', $quoted) . ")";
                        }

                        if (!empty($rangeClauses)) {
                            $clauses[] = "(" . implode(" OR ", $rangeClauses) . ")";
                        }

                        if (!empty($clauses)) {
                            $where .= "(" . implode(" OR ", $clauses) . ") AND ";
                        }
                    }
                }
                if (isset($filter['search']['mg_name'])) {
                    $where .= "mg.name LIKE '%{$filter['search']['mg_name']}%' and ";
                }
                if (isset($filter['search']['name_for_protocol'])) {
                    $where .= "umtr.name_for_protocol LIKE '%{$filter['search']['name_for_protocol']}%' and ";
                }
            }

            // работа с сортировкой
            if (!empty($filter['order'])) {
                if ($filter['order']['dir'] === 'asc') {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'material_name':
                        $order['by'] = 'mater.NAME';
                        break;
                    case 'name_for_protocol':
                        $order['by'] = 'umtr.name_for_protocol';
                        break;
                }
            }

            // работа с пагинацией
            $offset = 0;
            if (isset($filter['paginate'])) {
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


        $data = $this->DB->Query(
            "select 
                umtr.*,
                mater.NAME as material_name, mater.GROUPS,
                ugtp.id as ugtp_id, ugtp.new_method_id as method_id, ugtp.measuring_sheet,  
                ugtp.tech_condition_id as condition_id, ugtp.assigned_id, ugtp.price, ugtp.gost_number, 
                res.actual_value, umtr.id as umtr_id, mg.name as mg_name 
            from ulab_material_to_request as umtr
            left join MATERIALS as mater on mater.ID = umtr.material_id
            left join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
            left join ulab_trial_results as res on res.gost_to_probe_id = ugtp.id
            left join materials_groups mg on umtr.group = mg.id
            where umtr.deal_id = {$filter['search']['deal_id']} AND {$where}
            group by umtr.id
            ORDER BY umtr.material_number, umtr.probe_number {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "select 
                umtr.id
            from ulab_material_to_request as umtr
            left join MATERIALS as mater on mater.ID = umtr.material_id
            left join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
            left join ulab_trial_results as res on res.gost_to_probe_id = ugtp.id
            left join materials_groups mg on umtr.group = mg.id
            where umtr.deal_id = {$filter['search']['deal_id']} AND {$where}
            group by umtr.id"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "select 
                umtr.id
            from ulab_material_to_request as umtr
            left join MATERIALS as mater on mater.ID = umtr.material_id
            left join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
            left join ulab_trial_results as res on res.gost_to_probe_id = ugtp.id
           left join materials_groups mg on umtr.group = mg.id
            where umtr.deal_id = {$filter['search']['deal_id']} AND {$where}
            group by umtr.id"
        )->SelectedRowsCount();

        $i = $offset;
        while ($row = $data->Fetch()) {
            $row['n'] = ++$i;

            if ($row['date_probe'] == '0000-00-00') {
                $row['date_probe'] = '';
            }
            $labs = $materialModel->getLabsForProbeWithCheckTz($row['id']);
            $labsArray = [];

            foreach ($labs as $value) {
                $labsArray [] = $value['short_name'];
            }

            $currentProbeState = 'МФЦ';

            $currentProbeToLab = $this->DB->Query("SELECT id FROM probe_to_lab WHERE umtr_id = '{$row['id']}'")->Fetch()['id'];

            if ($currentProbeToLab) {
                $currentProbeState = '';
            }
            $currUser = $user->getCurrentUser();
            $count = $row['probe_number'] + 1;
            $result[$row['material_id']]['probe'][$row['id']]['material_name'] = $row['material_name'];
            $result[$row['material_id']]['probe'][$row['id']]['cipher'] = empty($row['cipher']) ? 'Не присвоен шифр #' . $count : $row['cipher'];
            $result[$row['material_id']]['probe'][$row['id']]['probe_number'] = $row['probe_number'];
            $result[$row['material_id']]['probe'][$row['id']]['name_for_protocol'] = htmlentities($row['name_for_protocol']);
            $result[$row['material_id']]['probe'][$row['id']]['group'] = $row['group'];
            $result[$row['material_id']]['probe'][$row['id']]['place'] = htmlentities($row['place']);
            $result[$row['material_id']]['probe'][$row['id']]['date_probe'] = $row['date_probe'];
            $result[$row['material_id']]['probe'][$row['id']]['quarry_id'] = $row['quarry_id'];
            $result[$row['material_id']]['probe'][$row['id']]['price'] += $row['price'];
            $result[$row['material_id']]['probe'][$row['id']]['is_send'] = $row['is_send'];
            $result[$row['material_id']]['probe'][$row['id']]['date_send'] = date('d.m.Y H:i', strtotime($row['date_send']));
            $result[$row['material_id']]['probe'][$row['id']]['mg_name'] = $row['mg_name'];
            $result[$row['material_id']]['probe'][$row['id']]['is_in_act'] = $row['in_act'];
            $result[$row['material_id']]['probe'][$row['id']]['lab_info'] = $labs;
            $result[$row['material_id']]['probe'][$row['id']]['lab_name'] = implode(', ', $labsArray);
            $result[$row['material_id']]['probe'][$row['id']]['quarry'] = $requirement->getQuarry();
            $result[$row['material_id']]['probe'][$row['id']]['is_hand_over'] = in_array(GROUP_MANAGER_ID, $currUser['groups']);
            $result[$row['material_id']]['probe'][$row['id']]['state'] = $currentProbeState;
            $result[$row['material_id']]['probe'][$row['id']]['user_status'] = $probe->getAcceptStatusUser($row['id'], $_SESSION['SESS_AUTH']['USER_ID']);

            $acceptStatus = $probe->getAcceptStatus($row['id']);
            $acceptUser = [];
            foreach ($acceptStatus as $status) {
                if ($status['accept_probe'] == 1) {
                    $acceptUser[] = $status['user_info'];
                }
            }

            if (!empty($acceptUser)) {
                $result[$row['material_id']]['probe'][$row['id']]['state'] = "Проба принята: " . implode(', ', array_column($acceptUser, 'short_name'));
            }
        }
        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;
        //$result['quarry'] = $requirement->getQuarry();
        return $result;
    }


    /**
     * @param $probeId
     * @param $data
     */
    public function updateProbeData($probeId, $data)
    {
        $user = new User();
        $request = new Request();
        $requirementModel = new Requirement();

        $dataSql = $this->prepearTableData('ulab_material_to_request', $data);
        $dealId = $this->getDealIdByProbe($probeId);

        $currentUser = $user->getCurrentUser();
        $dealName = $request->getDealById($dealId);
        $tzId = $request->getTzByDealId($dealId)['ID'];
        if (!isset($data['irrevocable'])) {
            $this->pre($data);
        }

        $historyData = [
            'DATE' => date('Y-m-d H:i:s'),
            'ASSIGNED' => "{$currentUser['NAME']} {$currentUser['LAST_NAME']}",
            'TZ_ID' => $tzId,
            'USER_ID' => $currentUser['ID'],
            'TYPE' => "Изменены данные пробы",
            'REQUEST' => $dealName['TITLE']
        ];

        $requirementModel->saveHistory($historyData);

        $this->DB->Update('ulab_material_to_request', $dataSql, "where id = {$probeId}");
    }

    public function sendProbe($probeId, $lab_id)
    {
        $date = date('Y-m-d H:i:s');

        $this->DB->Query("INSERT INTO sended_probes(probe_id, date_send, lab_id) VALUES('{$probeId}', '{$date}', '{$lab_id}')");
        $sql = $this->DB->Query("select * from `ba_laba`  WHERE `ID` = {$lab_id}")->Fetch();
        $name = $sql['short_name'];
        $this->addHistory($probeId, "Проба готова к испытаниям " . $name);
    }


    public function setProbesFromFiles($actData, $data = null)
    {
        $sqlData = $this->prepearTableData('ACT_BASE', $actData);
        $this->DB->Insert('ACT_BASE', $sqlData);
        $curYearCipher = date("y");
        $idTz = $actData['ID_TZ'];
        $i = 1;

        foreach ($data as $val) {
            $currentVal = [];
            $currentVal['cipher'] = $actData['ACT_NUM'] . '.' . $i . "/$curYearCipher";
            $currentVal['in_act'] = 1;
            $currentVal['place'] = $val['sampling_place']; /* * Дата отбора образца */
            $drawSampleDate = trim($val['sampling_date']);

            $date = DateTime::createFromFormat('n/j/Y', $drawSampleDate);

            if ($date === false) {
                throw new RuntimeException("Некорректная дата sampling_date: {$drawSampleDate}");
            } /* * Срок годности */
            $drawExpDate = trim($val['expiration_date']);
            $expDate = DateTime::createFromFormat('n/j/Y', $drawExpDate);

            if ($expDate === false) {
                throw new RuntimeException("Некорректная дата expiration_date: {$drawExpDate}");
            } /* * Дата отбора для ulab_material_to_request */
            $currentVal['date_probe'] = $date->format('Y-m-d');

            $umtrSqlData = $this->prepearTableData('ulab_material_to_request', $currentVal);
            $this->DB->Update('ulab_material_to_request', $umtrSqlData, "where id = {$val['ugtp_id']} AND in_act <> 1");

            $drawPackName = explode(',', $val['package']);
            $packName = $drawPackName[0];
            $packaging_integrity = trim($drawPackName[1]);
            $dopInfoProbe =
                [
                    'id_tz' => $idTz,
                    'ugtp_id' => $val['ugtp_id'],
                    'nd' => $val['normative_level'],
                    'manufacturer' => $val['manufacturer'],
                    'batch_size' => $val['sample_quantity'],
                    'packing_name' => $packName,
                    'packaging_integrity' => $packaging_integrity,
                    'manufacturing_date' => $date->format('Y-m-d'),
                    'expiration_date' => $expDate->format('Y-m-d'),
                    'storage_conditions' => $val['storage_conditions'],
                    'delivery_method' => $val['delivery_method'],
                    'sampling_place' => $val['sampling_place'],
                    'sampling_by' => $val['sampling_by'],
                ];

            $dopInfoDate = $this->prepearTableData('dop_info_requirement', $dopInfoProbe);
            $this->DB->Insert('dop_info_requirement', $dopInfoDate);
        }
    }
}
