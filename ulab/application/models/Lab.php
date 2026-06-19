<?php

/**
 * Клас лаборатории
 * Class Lab
 */
class Lab extends Model
{
    const USERS_CAN_EDIT_CONDITIONS = [1, 7, 61, 75, 10, 33, 9, 58, 13, 32, 11, 15, 100, 87]; //пользователи могут редактировать условия
    const USERS_CAN_EDIT_DOP_CONDITIONS = [222]; //пользователи могут редактировать доп условия
	const TABLE_BAR = [
		'80' => 0.27,
		'81' => 0.28,
		'82' => 0.29,
		'83' => 0.30,
		'84' => 0.28,
		'85' => 0.25,
		'86' => 0.23,
		'87' => 0.21,
		'88' => 0.20,
		'89' => 0.19,
		'90' => 0.17,
		'91' => 0.14,
		'92' => 0.11,
		'93' => 0.28,
		'94' => 0.05,
		'95' => 0.02,
		'96' => 0.00,
		'97' => -0.02,
		'98' => -0.01,
		'99' => 0.00,
		'100' => 0.00,
		'101' => -0.01,
		'102' => 0.06,
		'103' => 0.13,
		'104' => 0.17,
		'105' => 0.20,
		'106' => 0.23,
	];

    /**
     * @param $labId
     * @return int
     */
    public function getDepartmentIdByLabId($labId)
    {
        return $this->DB->Query("select * from ba_laba where ID = {$labId}")->Fetch()['id_dep'];

//        $labDepartment = [
//            1 => 54, // ЛФХИ
//            4 => 55, // ДСЛ
//            3 => 56, // ЛФМИ
//            2 => 57, // ЛСМ
//            5 => 58, // ОСК
//            6 => 53, // Администрация ИЦ
//        ];
//
//        return $labDepartment[$labId];
    }


    /**
     * @param $userId
     * @return array|false
     */
    public function getLabByUserId($userId)
    {
//        return $this->DB->Query("select l.* from ba_laba as l, b_intranet_user2dep as dep where l.id_dep = dep.DEPARTMENT_ID and dep.USER_ID = {$userId}")->Fetch();
        $sql = $this->DB->Query(
            "select UF_DEPARTMENT from b_uts_user where VALUE_ID = {$userId}"
        )->Fetch();

        $deps = unserialize($sql['UF_DEPARTMENT']);

        return $this->DB->Query("select l.* from ba_laba as l where l.id_dep = {$deps[0]}")->Fetch();
    }


    /**
     * @param $tzId
     * @return array
     */
    public function getLabByCheckTz($tzId)
    {
        if ( empty($tzId) ) {
            return [];
        }

        $sql = $this->DB->Query("select leader from CHECK_TZ where tz_id = {$tzId}");

        $result = [];
        while($row = $sql->Fetch()) {
            $result[] = $this->getLabByUserId($row['leader']);
        }

        return $result;
    }


    /**
     * получает лаборатории и юзеров в них
     * @param $labId
     * @return array
     */
    public function getLabAndUser($labId = 0)
    {
        $userModel = new User();

        $where = "1";

        if ( $labId > 0 ) {
            $where = "l.id = {$labId}";
        }

        $sql = $this->DB->Query("select l.* from ba_laba as l where {$where}");

        $result = [];
        while ($row = $sql->Fetch()) {
            $depUserSql = $this->DB->Query(
                "select utsu.VALUE_ID 
                        from b_uts_user as utsu
                        inner join b_user as u on u.ID = utsu.VALUE_ID
                        where utsu.UF_DEPARTMENT like '%:{$row['id_dep']};%' and u.ACTIVE = 'Y'"
            );
            $result[$row['id_dep']]['short_name'] = $row['short_name'];
            $result[$row['id_dep']]['lab_id'] = $row['ID'];

            while ($rowUser = $depUserSql->Fetch()) {
                if ( !empty($rowUser['VALUE_ID']) ) {
                    $user = $userModel->getUserData($rowUser['VALUE_ID']);
                    if ( empty($user) ) { continue; }
                    $userData = [
                        'id' => $user['ID'],
                        'name' => $user['NAME'],
                        'last_name' => $user['LAST_NAME'],
                        'user_name' => $user['user_name'],
                        'short_name' => $user['short_name'],
                    ];

                    $result[$row['id_dep']]['users'][$rowUser['VALUE_ID']] = $userData;
                }
            }
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getList(): array
    {
        $results = [];

        $laboratories = $this->DB->Query("SELECT * FROM `ba_laba`");

        while ($row = $laboratories->Fetch()) {
            $row['DEPARTMENT'] = $row['id_dep'];
            $results[] = $row;
        }


        return $results;
    }


    /**
     * @param $labId
     * @return array
     */
    public function get($labId)
    {
        if (empty($id)) { return []; }

        return $this->DB->Query("select * from ba_laba where ID = {$labId}")->Fetch();
    }


	/**
	 * @param $idMethod
	 * @return array
	 */
	public function getLabaById($id)
	{
		if (empty($id)) { return []; }

		$result = $this->DB->Query("select * from ba_laba where ID = {$id}")->Fetch();

		return $result['short_name'];
	}


    /**
     * @return array
     */
    public function getLabaRoom($labIdList = [])
    {
        $where = '1';

        if ( !empty($labIdList) ) {
            $str = implode(',', $labIdList);
            $where = "l.ID IN ({$str})";
        }

        $laboratories = $this->DB->Query(
            "SELECT
              l.NAME AS laba_name,
              r.LAB_ID,
              r.NUMBER,
              r.ID   AS room_id,
              r.NAME AS room_name,
              r.PLACEMENT
            FROM ba_laba l
            LEFT JOIN ROOMS r
              ON r.LAB_ID = l.ID
            WHERE r.LAB_ID IS NOT NULL
               OR l.ID = 18 AND {$where}
            ORDER BY COALESCE(r.LAB_ID, l.ID);"
        );

        $result = [];

        $lastId = 0;
        while ($row = $laboratories->Fetch()) {
            if ($row['laba_name'] == 'Производственная лаборатория') {
                $row['LAB_ID'] = 18;
            }
            if ( $lastId != $row['LAB_ID'] ) {
                $result[] = [
                    'id' => $row['LAB_ID'],
                    'name' => $row['laba_name'],
                ];

                $result[] = [
                    'id' => $row['room_id'] + 100,
                    'name' => trim($row['room_name']) . ' ' . trim($row['NUMBER']),
                ];

                $lastId = $row['LAB_ID'];
            } else {
                $result[] = [
                    'id' => $row['room_id'] + 100,
                    'name' => trim($row['room_name']) . ' ' . trim($row['NUMBER']),
                ];
            }
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getRooms()
    {
        $smtp = $this->DB->Query("SELECT * FROM `ROOMS`");

        $result = [];

        while ($row = $smtp->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param int|array $labID
     * @return array
     */
    public function getRoomByLabId($labID)
    {
        if ( empty($labID) ) {
            return [];
        }
        if ( is_array($labID) ) {
            $labID = implode(', ', $labID);
        }

        $smtp = $this->DB->Query(
            "SELECT * 
                FROM ROOMS 
                WHERE LAB_ID IN ({$labID})
                ORDER BY LAB_ID"
        );

        $result = [];

        while ($row = $smtp->Fetch()) {
            $row['name'] = trim($row['NAME']) . ' ' . trim($row['NUMBER']);
            $result[] = $row;
        }

        return $result;
    }


    /**
     * журнал условий
     * @param array $filter
     * @return array
     */
    public function getJournalCondition($filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'u_c.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // соответствие
                if (isset($filter['search']['is_match']) && $filter['search']['is_match'] === '0') {
                    $where .= "(u_c.is_method_match = '{$filter['search']['is_match']}' OR u_c.is_oborud_match = '{$filter['search']['is_match']}') AND ";
                }
                if (isset($filter['search']['is_match']) && $filter['search']['is_match'] === '1') {
                    $where .= "(u_c.is_method_match = '{$filter['search']['is_match']}' AND u_c.is_oborud_match = '{$filter['search']['is_match']}') AND ";
                }
//                // дата
//                if (isset($filter['search']['updated_at'])) {
//                    $where .= "LOCATE('{$filter['search']['updated_at']}', DATE_FORMAT(u_c.updated_at, '%d.%m.%Y %H:%i:%s')) > 0 AND ";
//                }
//                if ( isset($filter['search']['dateStart']) ) {
//                    $where .= "(u_c.updated_at >= '{$filter['search']['dateStart']}' AND u_c.updated_at <= '{$filter['search']['dateEnd']}') AND ";
//                }
                // дата
                if (isset($filter['search']['created_at'])) {
                    $where .= "LOCATE('{$filter['search']['created_at']}', DATE_FORMAT(u_c.created_at, '%d.%m.%Y %H:%i:%s')) > 0 AND ";
                }
                if ( isset($filter['search']['dateStart']) ) {
                    $where .= "(u_c.created_at >= '{$filter['search']['dateStart']}' AND u_c.created_at <= '{$filter['search']['dateEnd']}') AND ";
                }
                // температура
                if (isset($filter['search']['temp'])) {
                    $where .= "u_c.temp LIKE '%{$filter['search']['temp']}%' AND ";
                }
                // влажность
                if (isset($filter['search']['humidity'])) {
                    $where .= "u_c.humidity LIKE '%{$filter['search']['humidity']}%' AND ";
                }
                // давление
                if (isset($filter['search']['pressure'])) {
                    $where .= "u_c.pressure LIKE '%{$filter['search']['pressure']}%' AND ";
                }


                // помещение
                if (isset($filter['search']['room'])) {
                    if ($filter['search']['room'] > 100) {
                        $roomId = (int)$filter['search']['room'] - 100;
                        $where .= "u_c.`room_id` = {$roomId} AND ";
                    }
                }
            }

            // везде
            if (isset($filter['search']['everywhere'])) {
                $where .=
                    "";
            }
        }


        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
//                case 'updated_at':
//                    $order['by'] = 'u_c.updated_at';
//                    break;
                case 'created_at':
                    $order['by'] = 'u_c.created_at';
                    break;
                case 'temp':
                    $order['by'] = 'u_c.temp';
                    break;
                case 'humidity':
                    $order['by'] = 'u_c.humidity';
                    break;
                case 'pressure':
                    $order['by'] = 'u_c.pressure';
                    break;
                default:
                    $order['by'] = 'u_c.id';
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

        $where .= "1 ";

        $result = [];

        $data = $this->DB->Query(
            "SELECT u_c.*, u_c.id u_c_id, CONCAT(r.NAME, ' ', r.NUMBER) room  
                    FROM ulab_conditions u_c 
                    LEFT JOIN ROOMS as r ON r.ID = u_c.room_id 
                    WHERE {$where}
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT u_c.*, u_c.id u_c_id, CONCAT(r.NAME, ' ', r.NUMBER) room
                    FROM ulab_conditions u_c 
                    LEFT JOIN ROOMS as r ON r.ID = u_c.room_id 
                    WHERE {$where}"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT u_c.*, u_c.id u_c_id, CONCAT(r.NAME, ' ', r.NUMBER) room
                    FROM ulab_conditions u_c 
                    LEFT JOIN ROOMS as r ON r.ID = u_c.room_id 
                    WHERE {$where}"
        )->SelectedRowsCount();

        //проверка на допуск к редактированию
        $isCanEdit = in_array($_SESSION['SESS_AUTH']['USER_ID'], self::USERS_CAN_EDIT_CONDITIONS);
        $isCanDopEdit = in_array($_SESSION['SESS_AUTH']['USER_ID'], self::USERS_CAN_EDIT_DOP_CONDITIONS);

        while ($row = $data->Fetch()) {
            $row['ru_updated_at'] = date('d.m.Y H:i:s', strtotime($row['updated_at']));
            $row['ru_created_at'] = date('d.m.Y H:i:s', strtotime($row['created_at']));
            $row['is_can_edit'] = $isCanEdit;
            $row['is_can_dop_edit'] = $isCanDopEdit;
            $temp =$row['temp'];
            $pressure = $row['pressure'];
            $pressure1000 = $row['pressure'] * 1000;
            $bar = $this->bar($pressure);
            $row['bar'] = $bar['result'];
            $row['f1'] = $bar['f1'];
            $row['f2'] = $bar['f2'];
            $row['f3'] = $bar['f3'];
            $row['af'] = $bar['fa'];
            $row['fb'] = $bar['fb'];
            $row['inf_pressure'] = $row['pressure'];
            $row['pressure'] = round((0.024 - 0.0012 * $temp - 0.00000186*pow($temp, 2) + 0.00000026*pow($temp, 3) + 0.000312 * ($temp-20) * (97.592- ($pressure))) + $pressure + $bar['result'], 2);
            $row['first_pressure'] = round((0.024 - 0.0012 * $temp - 0.00000186*pow($temp, 2) + 0.00000026*pow($temp, 3) + 0.000312 * ($temp-20) * (97.592- ($pressure))), 3);
            $row['formula_bar'] = $bar['formula'];
            $row['formula_result'] = "{$row['pressure']} + {$bar['result']} + {$row['first_pressure']}  ≈ " . $row['pressure'];
            $row['formula'] = "(0.024 - 0.0012 * {$temp} - 0.00000186 * {$temp}² + 0.00000026 * {$temp}³ + 0.000312 * ({$temp}-20) * (97.592- {$pressure})) ≈ " . $row['first_pressure'];

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    /**
     * подготовить данные условий
     * @param $data
     * @return array
     */
    private function prepareConditionsData($data)
    {
        $columns = $this->getColumnsByTable('ulab_conditions');

        $sqlData = [];

        foreach ($columns as $column) {
            if (isset($data[$column])) {
                $sqlData[$column] = $this->quoteStr($this->DB->ForSql($data[$column]));
            }
        }

        return $sqlData;
    }

    /**
     * получить данные условий
     * @param int $id
     * @return array
     */
    public function getConditionById(int $id): array
    {
        $response = [];

        if (empty($id) || $id < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM ulab_conditions WHERE id = {$id}")->Fetch();

        if (!empty($result)) {
            $result['room_id'] = $result['room_id'] + 100;
            //$result['date'] = date('Y-m-d', strtotime($result['updated_at']));
            $result['date'] = date('Y-m-d H:i', strtotime($result['updated_at']));
            $response = $result;
        }

        return $response;
    }

    /**
     * добавить данные условий
     * @param array $data
     * @return int
     */
    public function addConditions(array $data): int
    {
        $sqlData = $this->prepareConditionsData($data);

        $result = $this->DB->Insert('ulab_conditions', $sqlData);

        return intval($result);
    }

    /**
     * обновить данные условий
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updateConditions(int $id, array $data)
    {
        $sqlData = $this->prepareConditionsData($data);

        $where = "WHERE id = {$id}";
        return $this->DB->Update('ulab_conditions', $sqlData, $where);
    }

    /**
     * удалить данные условий по id
     * @param int $id
     */
    public function removeConditionById(int $id)
    {
        $this->DB->Query("DELETE FROM ulab_conditions WHERE id = {$id}");
    }

    /**
     * получить средние значение условий
     * @param int $id
     * @return array
     */
    public function getMeanConditions(int $id): array
    {
        $response = [];

        if (empty($id) || $id < 0) {
            return $response;
        }

        $resultTemp = $this->DB->Query(
            "select FLOOR(RAND() * (avg_max_temp-avg_min_temp) + avg_min_temp) as random_temp 
                FROM (
                    select MAX(m.min_temp) avg_min_temp, MIN(m.max_temp) avg_max_temp 
                        FROM (
                            select MAX(um.cond_temp_1) min_temp, MIN(um.cond_temp_2) max_temp  
                                FROM ulab_methods um 
                                    inner join ulab_methods_room umr on umr.method_id = um.id 
                                    where umr.room_id = {$id} AND is_not_cond_temp <> 1
                                    union all 
                            select MAX(bo.TOO_EX + 0) min_temp, MIN(bo.TOO_EX2 + 0) max_temp  
                                FROM ba_oborud bo where bo.roomnumber = {$id} AND TEMPERATURE <> 1
                        ) AS m
                ) r"
        )->Fetch();

        /*$resultTemp = $this->DB->Query(
            "select FLOOR(RAND() * (avg_max_temp-avg_min_temp) + avg_min_temp) as random_temp
                FROM (
                    select (((MAX(m.max_temp) + MIN(m.min_temp))/2)+5) avg_max_temp, (((MAX(m.max_temp) + MIN(m.min_temp))/2)-5) avg_min_temp
                        FROM (
                            select MAX(um.cond_temp_2) max_temp, MIN(um.cond_temp_1) min_temp
                                FROM ulab_methods um
                                    inner join ulab_methods_room umr on umr.method_id = um.id
                                    where umr.room_id = {$id} AND is_not_cond_temp <> 1
                                    union all
                            select MAX(bo.TOO_EX2) max_temp, MIN(bo.TOO_EX) min_temp
                                FROM ba_oborud bo where bo.roomnumber = {$id} AND TEMPERATURE <> 1
                        ) AS m
                ) r"
            )->Fetch();*/

        $resultWet = $this->DB->Query(
            "select FLOOR(RAND() * (avg_max_wet-avg_min_wet) + avg_min_wet) as random_wet
                FROM (
                    select MAX(m.min_wet) avg_min_wet, MIN(m.max_wet) avg_max_wet
                        FROM (
                            select MAX(um.cond_wet_1) min_wet, MIN(um.cond_wet_2) max_wet
                                FROM ulab_methods um
                                    inner join ulab_methods_room umr on umr.method_id = um.id
                                    where umr.room_id = {$id} AND is_not_cond_wet <> 1
                                    union all
                            select MAX(bo.OVV_EX + 0) min_wet, MIN(bo.OVV_EX2 + 0) max_wet
                                FROM ba_oborud bo where bo.roomnumber = {$id} AND TEMPERATURE <> 1
                        ) AS m
                ) r"
        )->Fetch();
        /*$resultWet = $this->DB->Query(
            "select FLOOR(RAND() * (avg_max_wet-avg_min_wet) + avg_min_wet) as random_wet
                FROM (
                    select (((MAX(m.max_wet) + MIN(m.min_wet))/2)+5) avg_max_wet, (((MAX(m.max_wet) + MIN(m.min_wet))/2)-5) avg_min_wet
                        FROM (
                            select MAX(um.cond_wet_2) max_wet, MIN(um.cond_wet_1) min_wet
                                FROM ulab_methods um
                                    inner join ulab_methods_room umr on umr.method_id = um.id
                                    where umr.room_id = {$id} AND is_not_cond_wet <> 1
                                    union all
                            select MAX(bo.OVV_EX2) max_wet, MIN(bo.OVV_EX) min_wet
                                FROM ba_oborud bo where bo.roomnumber = {$id} AND HUMIDITY <> 1
                        ) AS m
                ) r"
        )->Fetch();*/

        $resultPressure = $this->DB->Query(
            "select FLOOR(RAND() * (avg_max_pressure-avg_min_pressure) + avg_min_pressure) as random_pressure
                FROM (
                    select MAX(m.min_pressure) avg_min_pressure, MIN(m.max_pressure) avg_max_pressure
                        FROM (
                            select MAX(um.cond_pressure_1) min_pressure, MIN(um.cond_pressure_2) max_pressure
                                FROM ulab_methods um
                                    inner join ulab_methods_room umr on umr.method_id = um.id
                                    where umr.room_id = {$id} AND is_not_cond_pressure <> 1
                                    union all
                            select MAX(bo.AD_EX + 0) min_pressure, MIN(bo.AD_EX2 + 0) max_pressure
                                FROM ba_oborud bo where bo.roomnumber = {$id} AND TEMPERATURE <> 1
                        ) AS m
                ) r"
        )->Fetch();
        /*$resultPressure = $this->DB->Query(
            "select FLOOR(RAND() * (avg_max_pressure-avg_min_pressure) + avg_min_pressure) as random_pressure
                FROM (
                    select (((MAX(m.max_pressure) + MIN(m.min_pressure))/2)+5) avg_max_pressure, (((MAX(m.max_pressure) + MIN(m.min_pressure))/2)-5) avg_min_pressure
                        FROM (
                            select MAX(um.cond_pressure_2) max_pressure, MIN(um.cond_pressure_1) min_pressure
                                FROM ulab_methods um
                                    inner join ulab_methods_room umr on umr.method_id = um.id
                                    where umr.room_id = {$id} AND is_not_cond_pressure <> 1
                                    union all
                            select MAX(bo.AD_EX2) max_pressure, MIN(bo.AD_EX) min_pressure
                                FROM ba_oborud bo where bo.roomnumber = {$id} AND PRESSURE <> 1
                        ) AS m
                ) r"
        )->Fetch();*/

        if (!empty($resultTemp) || !empty($resultWet) || !empty($resultPressure)) {
            $response['random_temp'] = $resultTemp['random_temp'] ?? null;
            $response['random_wet'] = $resultWet['random_wet'] ?? null;
            $response['random_pressure'] = $resultPressure['random_pressure'] ?? null;
        }

        return $response;
    }

	public function getLabList()
	{
		$result = [];
		$res = $this->DB->Query("SELECT * FROM ba_laba WHERE ID <> 6");

		while ($row = $res->Fetch()) {
			$result[] = $row;
		}

		return $result;
	}

    /**
     * @param int|null $protocolId
     * @return array
     */
    public function getConditionByProtocol(?int $protocolId): array
    {
        $response = [];

        if ( empty($protocolId) || $protocolId < 0) {
            return $response;
        }

        $ustSql = $this->DB->Query(
            "SELECT ust.* 
            FROM ulab_material_to_request umtr 
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id 
            INNER JOIN ulab_start_trials ust on ust.ugtp_id = ugtp.id 
            WHERE (umtr.protocol_id = {$protocolId} or (umtr.protocol_id = 0 and ugtp.protocol_id = {$protocolId})) AND ust.is_actual = 1 ORDER BY ust.ugtp_id ASC, ust.created_at ASC"
        );

        $arrUst = [];
        while ($row = $ustSql->Fetch()) {
            $arrUst[] = $row;
        }

        $where = '1';
        $periods = [];
        foreach ($arrUst as $ust) {
            $date = date('Y-m-d', strtotime($ust['created_at']));

            if ($ust['state'] == 'start') {
                $ugtpId = $ust['ugtp_id'];
                $range = "(DATE(uc.created_at) >= '{$date}'";
            }

            if (!empty($range) && !empty($ugtpId) && $ugtpId == $ust['ugtp_id'] &&
                ($ust['state'] == 'pause' || $ust['state'] == 'complete')) {
                $range .= " AND DATE(uc.created_at) <= '{$date}'";
                $range .= " AND ugtp.id = {$ugtpId})";

                $periods[] = $range;
                $ugtpId = 0;
                $range = '';
            }
        }

        if ( !empty($periods) ) {
            $where .= ' AND ' . implode(' OR ', $periods);
        } else {
            return $response;
        }

        $conditionsSql = $this->DB->Query(
            "SELECT MAX(uc.temp) max_temp, MIN(uc.temp) min_temp, MAX(uc.humidity) max_humidity, MIN(uc.humidity) min_humidity  
                FROM ulab_gost_to_probe ugtp 
                    INNER JOIN ulab_gost_room ugr on ugr.ugtp_id = ugtp.id 
                    INNER JOIN ulab_conditions uc on uc.room_id = ugr.room_id 
                    WHERE {$where}"
        )->Fetch();

        if ( !empty($conditionsSql) ) {
            $response = $conditionsSql;
        }

        return $response;
    }

    /**
     * Получить диапазоны испытаний
     * @param int $protocolId
     * @return array
     */
    public function getTrialsRange(int $protocolId): array
    {
        $response = [];

        if ( empty($protocolId) || $protocolId < 0) {
            return $response;
        }

        $ustSql = $this->DB->Query(
            "SELECT ust.*  
                FROM ulab_material_to_request umtr 
                    INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id 
                    LEFT JOIN ulab_start_trials ust on ust.ugtp_id = ugtp.id 
                    WHERE umtr.protocol_id = {$protocolId} AND ust.is_actual = 1 ORDER BY ust.ugtp_id ASC, ust.created_at ASC"
        );

        $arrUst = [];
        while ($row = $ustSql->Fetch()) {
            $arrUst[] = $row;
        }

        $periods = [];
        foreach ($arrUst as $ust) {
            //$date = date('Y-m-d', strtotime($ust['created_at']));
            $date = date('Y-m-d', strtotime($ust['date']));

            if ($ust['state'] == 'start') {
                $ugtpId = $ust['ugtp_id'];
                $range['date_start'] = $date;
            }

            if (!empty($range) && !empty($ugtpId) && $ugtpId == $ust['ugtp_id'] &&
                ($ust['state'] == 'pause' || $ust['state'] == 'complete')) {
                $range['date_end'] = $date;
                $range['ugtp_id']= $ugtpId;

                $periods[] = $range;
                $ugtpId = 0;
                $range = [];
            }
        }

        return $periods;
    }

    /**
     * Получить выбранные помещения для проведения испытаний
     * @param int $ugtpId
     * @return array
     */
    public function getGostRoom(int $ugtpId): array
    {
        $result = [];

        if ( empty($ugtpId) || $ugtpId < 0 ) {
            return $result;
        }

        $sql = $this->DB->Query(
            "SELECT r.* FROM ulab_gost_room ugr 
                INNER JOIN ROOMS AS r ON r.ID = ugr.room_id 
                WHERE ugtp_id = {$ugtpId}"
        );

        while ($row = $sql->Fetch()) {
            $row['name'] = trim($row['NAME']) . ' ' . trim($row['NUMBER']);
            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param array $ugtpIds
     * @return array
     */
    public function checkNotSelectedRooms($ugtpIds)
    {
        $methodsModel = new Methods();

        $result = [];
        $errors = [];
        $isSuccess = true;
        $strUgtpIds = implode(',', $ugtpIds);

        if ( empty($ugtpIds) || empty($strUgtpIds) ) {
            return $result;
        }

        $sql = $this->DB->Query(
            "SELECT 
                umtr.*,
                m.NAME material_name, 
                ugtp.id ugtp_id, ugtp.method_id  
                    FROM ulab_material_to_request AS umtr 
                    INNER JOIN ulab_gost_to_probe AS ugtp ON ugtp.material_to_request_id = umtr.id 
                    LEFT JOIN MATERIALS AS m ON m.ID = umtr.material_id
                    WHERE ugtp.id IN ({$strUgtpIds})"
        );

        while ($row = $sql->Fetch()) {
            $rooms = $this->getRoomsByMethod($row['method_id']);
            $methodData = $methodsModel->get($row['method_id']);
            $gostRoom = $this->getGostRoom($row['ugtp_id']);

            $row['selected_room'] = $gostRoom;
            $row['rooms'] = $rooms;
            $countRooms = count($rooms);

            // если помещений у методики выбрано, пропускаем
            if ( !empty($gostRoom) ) {
                continue;
            }

            $anchor = "<a href='".URI."/gost/method/{$row['method_id']}'>{$methodData['view_gost_for_protocol']}</a>";

            //если помещение не выбрано и более 1
            $isNotOne = $countRooms > 1 && empty($gostRoom);
            if ( $isNotOne ) {
                $isSuccess = false;
                $errors[] = "У Материала: {$row['material_name']} {$row['cipher']}, Методики: {$anchor} не выбрано помещение для испытания";
                continue;
            }

            //если к методике не привязано помещение
            if ( !$countRooms ) {
                $isSuccess = false;
                $errors[] = "Методика {$anchor} не привязана к помещению";
                continue;
            }

            $result[] = $row;
        }

        return $response = [
            'success' => $isSuccess,
            'errors' => $errors,
            'data' => $result,
        ];
    }

    /**
     * @param int $roomId
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     */
    public function getDatesByPeriodsForRoom(int $roomId, string $dateStart, string $dateEnd): array
    {
        $response = [];
        $where = "";

        if ( empty($roomId) || $roomId < 0 || (empty($dateStart) && empty($dateEnd)) )  {
            return $response;
        }

        if ( empty($dateEnd) ) {
            $where = " AND DATE(created_at) = '{$dateStart}'";
        }

        if ( empty($dateStart) ) {
            $where = " AND DATE(created_at) = '{$dateEnd}'";
        }

        if ( !empty($dateStart) && !empty($dateEnd) ) {
            $where = " AND DATE(created_at) >= '{$dateStart}' AND DATE(created_at) <= '{$dateEnd}'";
        }

        $result = $this->DB->Query(
            "SELECT DATE_FORMAT(DATE(created_at), '%d.%m.%Y') grouped_date FROM ulab_conditions
                WHERE room_id = {$roomId} {$where} GROUP BY DATE(created_at)"
        );

        while ($row = $result->Fetch()) {
            $response[] = $row['grouped_date'];
        }

        return $response;
    }

    /**
     * получить данные атмосферного давления по дате
     * @param string $date
     * @return array
     */
    public function getPressureByDate(string $date): array
    {
        $response = [];

        if ( empty($date) ) {
            return $response;
        }

        $result = $this->DB->Query(
            "SELECT 
                    *, DATE_FORMAT(date, '%Y-%m-%d %H:%i') datetime_hi, DATE_FORMAT(date, '%d.%m.%Y %H:%i') datetime_ru 
                        FROM ulab_pressure WHERE DATE_FORMAT(date, '%Y-%m-%d') = '{$date}'
            ");

        while ($row = $result->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    /**
     * Добавить данные атмосферного давления
     * @param array $data
     * @return int
     */
    public function addPressure(array $data): int
    {
        $sqlData = $this->prepearTableData('ulab_pressure', $data);
        $result = $this->DB->Insert('ulab_pressure', $sqlData);

        return intval($result);
    }

    /**
     * обновить данные атмосферного давления
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updatePressure(int $id, array $data)
    {
        $sqlData = $this->prepearTableData('ulab_pressure', $data);

        $where = "WHERE id = {$id}";
        return $this->DB->Update('ulab_pressure', $sqlData, $where);
    }

    /**
     * @param int $roomId
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     */
    public function getConditionsByRoom(int $roomId, string $dateStart, string $dateEnd): array
    {
        $response = [];
        $where = "";

        if ( empty($roomId) || $roomId < 0 || (empty($dateStart) && empty($dateEnd)) )  {
            return $response;
        }

        if ( empty($dateEnd) ) {
            $where = " AND DATE(created_at) = '{$dateStart}'";
        }

        if ( empty($dateStart) ) {
            $where = " AND DATE(created_at) = '{$dateEnd}'";
        }

        if ( !empty($dateStart) && !empty($dateEnd) ) {
            $where = " AND DATE(created_at) >= '{$dateStart}' AND DATE(created_at) <= '{$dateEnd}'";
        }

        $result = $this->DB->Query(
            "SELECT COUNT(*) amount, MIN(temp) min_temp, MAX(temp) max_temp, MIN(humidity) min_humidity, MAX(humidity) max_humidity FROM ulab_conditions 
                WHERE room_id = {$roomId} {$where}"
        )->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * @param $roomId
     * @return array
     */
    public function getRoomById($roomId)
    {
        if ( empty($roomId) ) {
            return [];
        }

        $room = $this->DB->Query("SELECT * FROM ROOMS WHERE ID = {$roomId}")->Fetch();

        $result = [];

        if (!empty($room)) {
            $room['name'] = trim($room['NAME']) . ' ' . trim($room['NUMBER']);
            $result = $room;
        }

        return $result;
    }

    /**
     * @param array $ugtpIds
     * @return array
     */
    public function checkCountRooms($ugtpIds)
    {
        $methodsModel = new Methods();
        $resultModel = new Result();

        $result = [];
        $errors = [];
        $isSuccess = true;
        $strUgtpIds = implode(',', $ugtpIds);

        if ( empty($ugtpIds) || empty($strUgtpIds) ) {
            return $result;
        }

        $sql = $this->DB->Query(
            "SELECT 
                umtr.*,
                m.NAME material_name, 
                ugtp.id ugtp_id, ugtp.method_id  
                    FROM ulab_material_to_request AS umtr 
                    INNER JOIN ulab_gost_to_probe AS ugtp ON ugtp.material_to_request_id = umtr.id 
                    LEFT JOIN MATERIALS AS m ON m.ID = umtr.material_id
                    WHERE ugtp.id IN ({$strUgtpIds})"
        );

        while ($row = $sql->Fetch()) {
            $rooms = $this->getRoomsByMethod($row['method_id']);
            $gostRoom = $this->getGostRoom($row['ugtp_id']);
            $startTrials = $resultModel->getStartTrials($row['ugtp_id']);
            $methodData = $methodsModel->get($row['method_id']);

            $countRooms = count($rooms);

            if ( !$countRooms ) {
                $isSuccess = false;
                $anchor = "<a href='".URI."/gost/method/{$row['method_id']}'>{$methodData['view_gost_for_protocol']}</a>";
                $errors[] = "Не удалось начать испытание, методика {$anchor} не привязана к помещению";
            }

            // если помещений у методики более 1 и помещения небыли выбраны для испытания, то false показываем список помещений для выбора где будет проходить испытание
            $isNotOne = $countRooms > 1 && empty($gostRoom);
            if (!$isNotOne) {
                continue;
            }

            $isSuccess = false;

            $result[$row['material_id']]['material_id'] = $row['material_id'];
            $result[$row['material_id']]['deal_id'] = $row['deal_id'];

            $result[$row['material_id']]['material_name'] = $row['material_name'];
            $result[$row['material_id']]['probe'][$row['id']]['cipher'] = $row['cipher'] ?: 'Не присвоен шифр';
            $result[$row['material_id']]['probe'][$row['id']]['probe_number'] = $row['probe_number'];
            $result[$row['material_id']]['probe'][$row['id']]['probe_id'] = $row['id']; // id - ulab_material_to_request

            $methodData['ugtp_id'] = $row['ugtp_id'];
            $methodData['is_not_one'] = $isNotOne;
            $methodData['count_room'] = $countRooms;

            $result[$row['material_id']]['probe'][$row['id']]['method'][$row['ugtp_id']] = $methodData;
            $result[$row['material_id']]['probe'][$row['id']]['start_trials'][$row['ugtp_id']] = $startTrials;
            $result[$row['material_id']]['probe'][$row['id']]['rooms'][$row['ugtp_id']] = $rooms;
        }

        return $response = [
            'success' => $isSuccess,
            'errors' => $errors,
            'data' => $result,
        ];
    }

    /**
     * @param int $idMethod
     * @return array
     */
    public function getRoomsByMethod(int $idMethod): array
    {
        $result = [];

        if ( empty($idMethod) || $idMethod < 0 ) {
            return $result;
        }

        $sql = $this->DB->Query(
            "SELECT r.*  
                FROM ulab_methods_room AS umr 
                INNER JOIN ROOMS AS r ON r.ID = umr.room_id
                    where umr.method_id = {$idMethod}"
        );

        while ($row = $sql->Fetch()) {
            $row['name'] = trim($row['NAME']) . ' ' . trim($row['NUMBER']);

            $result[] = $row;
        }

        return $result;
    }

	/**
	 * @param bool $id_bitrix
	 * @param false $is_short
	 * @return array
	 */
	public function getListAlt($id_bitrix = true, $is_short = false): array
	{
		$laboratories = $this->DB->Query("
            SELECT BL.*, BL.id_dep as DEPARTMENT
            FROM ba_laba as BL 
        ");

		while ($row = $laboratories->Fetch()) {
			if ($id_bitrix)
				$results[$row['DEPARTMENT']] = $row['NAME'];
			else
				$results[$row['ID']] = $row['NAME'];

		}
		if ($is_short) {
			foreach ($results as $id => $name) {
				$name = str_replace("Отдел ", "", $name);
				$name = str_replace("-", " ", $name);
				$name = preg_replace("\([^\)]+\)", "", $name);
				$name = preg_replace("`\s\w{1,2}\s`u", " ", $name);
				$words = explode(" ", $name);
				foreach ($words as $key => $word) {
					$words[$key] = mb_strtoupper(mb_substr($word, 0, 1));
				}
				$name = implode("", $words);
				$results[$id] = $name;
			}
		}

		return $results ?? [];
	}


    /**
     * @param $roomId
     * @return array|false
     */
    public function getConditionsRoomToday($roomId)
    {
        $now = date("Y-m-d");
//
//        if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
//            echo '<pre>';
//            var_dump("select * from ulab_conditions where room_id = {$roomId} and DATE(created_at) = DATE('{$now}') order by id desc ");
//
//            exit();
//        }
        return $this->DB->Query("select * from ulab_conditions where room_id = {$roomId} and DATE(created_at) = DATE('{$now}') order by id desc ")->Fetch();
    }

    /**
     * журнал отделов
     * @param array $filter
     * @return array
     */
    public function getJournalList($filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'bl.ID',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                foreach ($filter['search'] as $key => $value) {
                    $filter['search'][$key] = $this->DB->ForSql(trim($value));
                }

                // Наименование
                if ( isset($filter['search']['NAME']) ) {
                    $where .= "bl.NAME LIKE '%{$filter['search']['NAME']}%' AND ";
                }
                // Начальник
                if ( isset($filter['search']['FULL_NAME']) ) {
                    $id = $filter['search']['FULL_NAME'];

                    if ($id == -1) {
                        $where .= "";
                    }
                    else if ($id == -2) {
                        $where .= "bl.HEAD_ID IS NULL AND ";
                    }
                    else if ($id == -3) {
                        $where .= "bl.HEAD_ID IS NOT NULL AND ";
                    }
                    else {
                        $where .= "bl.HEAD_ID = $id AND bl.HEAD_ID IS NOT NULL AND ";
                    }
                }
            }
            // везде
            if (isset($filter['search']['everywhere'])) {
                $where .=
                    "";
            }
        }

        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
                case 'NAME':
                    $order['by'] = 'bl.NAME';
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

        $where .= "1 ";

        $result = [];

        $data = $this->DB->Query(
            "SELECT bl.ID, bl.NAME,
                    IFNULL(
                        GROUP_CONCAT(CONCAT(SUBSTRING(bu.NAME, 1, 1), '. ', bu.LAST_NAME) ORDER BY bu.NAME ASC SEPARATOR ', '),
                        '-'
                    ) as FULL_NAME
                    FROM ba_laba bl
                    LEFT JOIN b_user bu ON bl.HEAD_ID = bu.ID
                    WHERE {$where} 
                    GROUP BY bl.ID, bl.NAME
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT bl.ID, bl.NAME,
                    IFNULL(
                        GROUP_CONCAT(CONCAT(SUBSTRING(bu.NAME, 1, 1), '. ', bu.LAST_NAME) ORDER BY bu.NAME ASC SEPARATOR ', '),
                        '-'
                    ) as FULL_NAME
                    FROM ba_laba bl
                    LEFT JOIN b_user bu ON bl.HEAD_ID = bu.ID
                    GROUP BY bl.ID, bl.NAME"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT bl.ID, bl.NAME,
                    IFNULL(
                        GROUP_CONCAT(CONCAT(SUBSTRING(bu.NAME, 1, 1), '. ', bu.LAST_NAME) ORDER BY bu.NAME ASC SEPARATOR ', '),
                        '-'
                    ) as FULL_NAME
                    FROM ba_laba bl
                    LEFT JOIN b_user bu ON bl.HEAD_ID = bu.ID
                    WHERE {$where} 
                    GROUP BY bl.ID, bl.NAME
                    ORDER BY {$order['by']} {$order['dir']}"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    /**
     * @param $labId
     * @return array|false
     */
    public function getLab($labId)
    {
        if (empty($labId) || $labId < 0) {
            return [];
        }

        return $this->DB->Query("
            select `BL`.*, `BL`.id_dep as id_dep, `BL`.HEAD_ID, uup.permission_id as ROLE_USER_ID
            from `ba_laba` as `BL`
            left join ulab_user_permission as uup on uup.user_id = BL.HEAD_ID
            where `BL`.ID = {$labId}
        ")->Fetch();
    }

    public function createDeptBitrix($name)
    {
        $fieldsToCreate = array(
            "NAME" => $name,
            "IBLOCK_ID" => 5,
            "IBLOCK_SECTION_ID" => 53
        );

        $department = new CIBlockSection;
        $createResult = $department->Add($fieldsToCreate);

        if ($createResult) {
            return $createResult; // Возвращает ID нового подразделения
        } else {
            return $department->LAST_ERROR;
            //echo "Ошибка при создании нового подразделения: " . $department->LAST_ERROR;
        }
    }

    public function addDept($data)
    {
        $permissionModel = new Permission();

        $sqlData = $this->prepareDeptData($data);
        unset($sqlData["ID"]);

        if ($data['HEAD_ID'] == -1)
            $data['HEAD_ID'] = NULL;

        if ($data['HEAD_ID'] && $data['HEAD_ROLE_ID']) {
            $permissionModel->updateUser($data['HEAD_ID'], $data['HEAD_ROLE_ID']);
        }

        return $this->DB->Insert('ba_laba', $sqlData);
    }

    /**
     * @param $data
     * @return array
     */
    private function prepareDeptData($data, $table = 'ba_laba')
    {
        $columns = $this->getColumnsByTable($table);

        $sqlData = [];

        foreach ($columns as $column) {
            if ( isset($data[$column]) ) {
                $sqlData[$column] = $this->quoteStr($this->DB->ForSql(trim($data[$column])));
            }
        }
        return $sqlData;
    }

    public function connectionBitrixAndUlab($ulabId, $bitrixId)
    {
        $this->DB->Query("UPDATE ba_laba SET id_dep = '{$bitrixId}' WHERE ID = '{$ulabId}'");
    }

    public function getRoom($roomId)
    {
        if (empty($roomId) || $roomId < 0) {
            return [];
        }

        $oborudModel = new Oborud();

        $room = $this->DB->Query("
            SELECT R.*, rtl.id_lab as LAB_ID
            FROM `ROOMS` as R
            LEFT JOIN `rooms_to_labs` AS rtl ON rtl.`id_room` = R.`id`
            WHERE R.`id` = {$roomId}
        ")->Fetch();

        $equipment_storaged = $oborudModel->getOborudByStorageRoom($roomId);
        $equipment_operating = $oborudModel->getOborudByOperatingRoom($roomId);

        $room['equipment_storaged'] = $equipment_storaged ?? [];
        $room['equipment_operating'] = $equipment_operating ?? [];

        return $room ?? [];
    }

    public function getOborudByOperatingRoom(string $roomId = ''): array
    {
        $result = [];

        $sql = $this->DB->Query("
            SELECT eg.id, eg.name, eg.id_operating_room
            FROM `equipment_general` AS eg
            WHERE eg.`id_operating_room` = '{$roomId}'
                OR eg.`id_operating_room` IS NULL
        ");

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function updateRoom($id, $data)
    {
        $sqlData = $this->prepareRoomData($data);

        $where = "WHERE ID = {$id}";
        return $this->DB->Update('ROOMS', $sqlData, $where);
    }

    private function prepareRoomData($data)
    {
        $columns = $this->getColumnsByTable('ROOMS');

        $sqlData = [];

        foreach ($columns as $column) {
            if ( isset($data[$column]) ) {
                $sqlData[$column] = $this->quoteStr($this->DB->ForSql(trim($data[$column])));
            }
        }

        $sqlData['SPEC'] = !empty($data['SPEC']) ? 1 : 0;

        return $sqlData;
    }

    public function updateDept($id, $data)
    {
        $permissionModel = new Permission();
        $sqlData = $this->prepareDeptData($data);
        $where = "WHERE ID = {$id}";

        $bitrixId = $this->DB->Query("
            SELECT `BL`.*, `BL`.id_dep as id_dep
            FROM `ba_laba` as `BL`
            WHERE `BL`.ID = $id")
            ->Fetch()['id_dep'];

        if (isset($bitrixId)) {
            $fieldsToUpdate = array(
                "NAME" => $data['NAME'],
            );

            $department = new CIBlockSection;
            $department->Update($bitrixId, $fieldsToUpdate);
        }

        if ($data['HEAD_ID'] == -1)
            $data['HEAD_ID'] = NULL;

        if ($data['HEAD_ID'] && $data['HEAD_ROLE_ID']) {
            $permissionModel->updateUser($data['HEAD_ID'], $data['HEAD_ROLE_ID']);
        }

        return $this->DB->Update('ba_laba', $sqlData, $where);
    }

    /**
     * @param $data
     * @return false|mixed|string
     */
    public function addRoom($data)
    {
        $sqlData = $this->prepareRoomData($data);

        return $this->DB->Insert('ROOMS', $sqlData);
    }

    public function assignRoomToLab($roomId, $deptId) {
        return $this->DB->Query("INSERT INTO rooms_to_labs (id_lab, id_room) VALUES ($deptId, $roomId);");
    }

    /**
     * @param int $id
     */
    public function deleteRoom($id)
    {
        $this->DB->Query("DELETE FROM rooms_to_labs WHERE id_room = {$id}");
        $this->DB->Query("DELETE FROM ROOMS WHERE id = {$id}");
    }

    /**
     * @param $labId
     * @return mixed
     */
    public function deleteLab($labId)
    {
        $bitrixId = $this->DB->Query("
            SELECT `BL`.*, `BL`.id_dep as id_dep
            FROM `ba_laba` as `BL`
            WHERE `BL`.ID = $labId")
            ->Fetch()['id_dep'];

        $result2 = $this->DB->Query("delete from ba_laba where id = {$labId}");

        $deleteDone = false;
        if (isset($bitrixId) && isset($result2)) {
            $department = new CIBlockSection;
            $result = $department->Delete($bitrixId);

            if ($result) {
                $deleteDone = true;
            } else {
                $deleteDone = false;
            }
        }

        return $deleteDone;
    }

    private function bar($pressure) {
		$res['f1'] = floor($pressure);
		$res['fa'] = self::TABLE_BAR[$res['f1']];
		$res['f2'] = ceil($pressure);
		$res['fb'] = self::TABLE_BAR[$res['f2']];
		$res['f3'] = round($pressure - $res['f1'], 3);
		$res['result'] = round($res['fa'] * (1 - $res['f3']) + ($res['fb'] * $res['f3']), 3);
		$res['formula'] = "{$res['fa']} * (1 - {$res['f3']}) + ({$res['fb']} * {$res['f3']})  ≈ " . $res['result'];

		return $res;
	}

	public function getScaleBar()
	{
		$result = [];

		$res = $this->DB->Query("SELECT * FROM barometer_scale");

		while ($row = $res->fetch()) {
			$result[] = $row;
		}

		return $result;
	}

    public function updateDop($id, $data)
    {
        $this->DB->Query("UPDATE ulab_conditions SET voltage = '{$data['voltage']}', frequency = '{$data['frequency']}' WHERE id = '{$id}'");
    }
}
