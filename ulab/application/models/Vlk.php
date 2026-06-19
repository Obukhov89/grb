<?php

/**
 * Модель для работы с ВЛК
 * Class Vlk
 */
class Vlk extends Model
{
    /**
     * Получение данных для журнала методов и образцов контроля
     * @param array $filter
     * @return array
     */
    public function getMethodList($filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => "um.name",
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // Определяемая характеристика / показатель
                if ( isset($filter['search']['name']) ) {
                    $where .= "um.name LIKE '%{$filter['search']['name']}%' AND ";
                }
                // Номер документа
                if ( isset($filter['search']['reg_doc']) ) {
                    $where .= "ug.reg_doc LIKE '%{$filter['search']['reg_doc']}%' AND ";
                }

                // Лаба Комната
                if ( isset($filter['search']['lab']) ) {
                    if ( $filter['search']['lab'] < 100 ) {
                        $where .= "l.`lab_id` = {$filter['search']['lab']} AND ";
                    } else if ($filter['search']['lab'] > 100) {
                        $roomId = (int) $filter['search']['lab'] - 100;
                        $where .= "r.`room_id` = {$roomId} AND ";
                    }
                }

                // Статус
                if ( isset($filter['search']['stage']) ) {
                    if ( $filter['search']['stage'] == 1 ) { // Актуальные
                        $where .= "um.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 2 ) { // В ОА
                        $where .= "um.in_field = 1 AND um.is_extended_field = 0 AND um.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 3 ) { // РОА
                        $where .= "um.is_extended_field = 1 AND um.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 5 ) { // Вне ОА
                        $where .= "um.in_field = 0 AND um.is_actual = 1 AND um.is_extended_field = 0 AND ";
                    }
                    if ( $filter['search']['stage'] == 7 ) { // Не актуальные
                        $where .= "um.is_actual = 0 AND ";
                    }
                } else {
                    $where .= "um.is_actual = 1 AND ";
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
                case 'name':
                    $order['by'] = 'um.name';
                    break;
                case 'clause':
                    $order['by'] = 'um.clause';
                    break;
                case 'reg_doc':
                    $order['by'] = 'ug.reg_doc';
                    break;
                default:
                    $order['by'] = 'um.name';
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
            "SELECT DISTINCT 
                        um.id um_id, um.clause, um.name, 
                        ug.reg_doc 
                    FROM ulab_methods um 
                    INNER JOIN ulab_gost ug ON ug.id = um.gost_id 
                    LEFT JOIN ulab_methods_lab as l ON l.method_id = um.id 
                    LEFT JOIN ulab_methods_room as r ON r.method_id = um.id 
                    WHERE {$where}
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT DISTINCT 
                        um.id um_id, um.clause, um.name, 
                        ug.reg_doc 
                    FROM ulab_methods um 
                    INNER JOIN ulab_gost ug ON ug.id = um.gost_id 
                    LEFT JOIN ulab_methods_lab as l ON l.method_id = um.id 
                    LEFT JOIN ulab_methods_room as r ON r.method_id = um.id 
                    WHERE {$where}"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT DISTINCT 
                        um.id um_id, um.clause, um.name, 
                        ug.reg_doc 
                    FROM ulab_methods um 
                    INNER JOIN ulab_gost ug ON ug.id = um.gost_id 
                    LEFT JOIN ulab_methods_lab as l ON l.method_id = um.id 
                    LEFT JOIN ulab_methods_room as r ON r.method_id = um.id 
                    WHERE {$where}"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    /**
     * @param $methodId
     * @return array
     */
    public function getComponentsByMethod($methodId)
    {
        $response = [];

        if (empty($methodId) || $methodId < 0) {
            return $response;
        }

        $oborudModel = new Oborud();

        $result = $this->DB->Query(
            "SELECT uc.id uc_id, uc.name uc_name, uc.certified_value, uc.certified_value, 
                            umc.id umc_id, 
                            ss.*, ss.ID ss_id 
                        FROM ulab_component uc 
                        INNER JOIN ulab_method_component umc ON umc.component_id = uc.id 
                        INNER JOIN ST_SAMPLE ss ON ss.ID = uc.st_sample_id 
                        WHERE umc.method_id = {$methodId} AND umc.is_deleted = 0"
        );

        while ($row = $result->Fetch()) {
            $sampleStage = $oborudModel->getSampleStage($row);

            $row['bgStage'] = $sampleStage['bgStage'];
            $row['titleStage'] = $sampleStage['titleStage'];

            $response[] = $row;
        }

        return $response;
    }

    /**
     * @param $umcId - ulab_method_component
     * @return array
     */
    public function getMethodComponent($umcId)
    {
        $response = [];

        if (empty($umcId) || $umcId < 0) {
            return $response;
        }

        $userModel = new User();

        $result = $this->DB->Query(
            "SELECT uc.id uc_id, uc.name uc_name, 
                            umc.id umc_id, umc.*, 
                            ss.ID ss_id, ss.NUMBER ss_number 
                        FROM ulab_component uc 
                        INNER JOIN ulab_method_component umc ON umc.component_id = uc.id 
                        INNER JOIN ST_SAMPLE ss ON ss.ID = uc.st_sample_id 
                        WHERE umc.id = {$umcId}"
        )->Fetch();

        if (!empty($result)) {
            $user = $userModel->getUserData($result['create_user_id']);
            $result['short_name'] =  $user['short_name'];
            $result['create_at_ru'] = $result['create_at'] ?
                date('d.m.Y H:i:s', strtotime($result['create_at'])) : '';

            $response = $result;
        }

        return $response;
    }

    /**
     * Проверить существует ли связи методики и метрологической характеристики
     * @param $methodId
     * @param $componentId
     * @return bool
     */
    public function hasMethodComponentRelation($methodId, $componentId)
    {
        if (empty($methodId) || $methodId < 0 || empty($componentId) || $componentId < 0) {
            return false;
        }

        $result = $this->DB->Query(
            "SELECT * FROM ulab_method_component WHERE method_id = {$methodId} AND component_id = {$componentId} AND is_deleted = 0"
        )->Fetch();

        return !empty($result);
    }

    /**
     * @param $data
     * @return bool|string
     */
    public function addMethodComponent($data)
    {
        $data['create_user_id'] = $_SESSION['SESS_AUTH']['USER_ID'];

        $sqlData = $this->prepearTableData('ulab_method_component', $data);
        return $this->DB->Insert('ulab_method_component', $sqlData);
    }

    public function deleteMethodComponent($umcId)
    {
        $currentDatetime = date('Y-m-d h:i:s');
        $data = [
            'delete_user_id' => $_SESSION['SESS_AUTH']['USER_ID'],
            'delete_at' => "{$currentDatetime}",
            'is_deleted' => 1,
        ];

        $sqlData = $this->prepearTableData('ulab_method_component', $data);

        $where = "WHERE id = {$umcId}";
        return $this->DB->Update('ulab_method_component', $sqlData, $where);
    }

    public function editMethodComponent($umcId, $data)
    {
        $sqlData = $this->prepearTableData('ulab_method_component', $data);

        $where = "WHERE id = {$umcId}";
        return $this->DB->Update('ulab_method_component', $sqlData, $where);
    }

    /**
     * @param $umcId
     * @return array
     */
    public function getVlkMeasuring($umcId)
    {
        $response = [];

        if (empty($umcId) || $umcId < 0) {
            return $response;
        }

        $result = $this->DB->Query(
            "SELECT * FROM ulab_vlk_measuring WHERE umc_id = {$umcId} AND is_deleted = 0"
        );

        while ($row = $result->Fetch()) {
            $row['date_ru'] = $row['date'] ? date('d.m.Y', strtotime($row['date'])) : '';
            $row['result'] = json_decode($row['result'], true);

            $response[] = $row;
        }

        return $response;
    }

    /**
     * @param $uvmId
     * @return array
     */
    public function getVlkMeasuringById($uvmId)
    {
        $response = [];

        if (empty($uvmId) || $uvmId < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM ulab_vlk_measuring WHERE id = {$uvmId}")->Fetch();

        if (!empty($result)) {
            $result['date_ru'] = $result['date'] ? date('d.m.Y', strtotime($result['date'])) : '';
            $result['result'] = json_decode($result['result'], true);

            $response = $result;
        }

        return $response;
    }

    public function addVlkMeasuring($data)
    {
        if (isset($data['result'])) {
            $data['result'] = json_encode($data['result'], JSON_UNESCAPED_UNICODE);
        }

        $sqlData = $this->prepearTableData('ulab_vlk_measuring', $data);
        return $this->DB->Insert('ulab_vlk_measuring', $sqlData);
    }

    public function editVlkMeasuring($id, $data)
    {
        if (isset($data['result'])) {
            $data['result'] = json_encode($data['result'], JSON_UNESCAPED_UNICODE);
        }

        $sqlData = $this->prepearTableData('ulab_vlk_measuring', $data);

        $where = "WHERE id = {$id}";
        return $this->DB->Update('ulab_vlk_measuring', $sqlData, $where);
    }

    /**
     * Мягкое удаление результатов измерения
     * @param int $id
     * @return bool|false|int
     */
    public function softDelVlkMeasuring(int $id)
    {
        $data = [
            'is_deleted' => 1,
        ];

        $sqlData = $this->prepearTableData('ulab_vlk_measuring', $data);

        $where = "WHERE id = {$id}";
        return $this->DB->Update('ulab_vlk_measuring', $sqlData, $where);
    }

    /**
     * @param $uvmId - ulab_vlk_measuring
     * @param $action
     */
    public function addHistoryMeasuring($uvmId, $action)
    {
        $data = [
            'user_id' => $_SESSION['SESS_AUTH']['USER_ID'],
            'vlk_measuring_id' => $uvmId,
            'date' => date('Y-m-d H:i:s'),
            'action' => $action,
        ];

        $sqlData = $this->prepearTableData('ulab_vlk_measuring_history', $data);
        $this->DB->Insert('ulab_vlk_measuring_history', $sqlData);
    }

    /**
     * @param $uvmId
     * @return array
     */
    public function getHistoryMeasuring($uvmId)
    {
        $userModel = new User();

        $sql = $this->DB->Query(
            "SELECT * FROM ulab_vlk_measuring_history WHERE vlk_measuring_id = {$uvmId} order by id asc"
        );

        $result = [];

        while ($row = $sql->Fetch()) {
            $user = $userModel->getUserData($row['user_id']);
            $row['short_name'] =  $user['short_name'];
            $row['date'] = date('d.m.Y H:i:s', strtotime($row['date']));

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Журнал измерений ВЛК
     * @param array $filter
     * @return array
     */
    public function getVlkMeasuringList($filter = [])
    {
        $permissionModel = new Permission;

        $permissionInfo = $permissionModel->getUserPermission($_SESSION['SESS_AUTH']['USER_ID']);

        $where = "";
        $limit = "";
        $order = [
            'by' => 'uvm.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // Связи методик и метрологической характеристики
                if ( isset($filter['search']['umc_id']) ) {
                    $where .= "uvm.umc_id = {$filter['search']['umc_id']} AND ";
                }
                // Дата
                if ( isset($filter['search']['date']) ) {
                    $where .= "LOCATE('{$filter['search']['date']}', DATE_FORMAT(uvm.date, '%d.%m.%Y')) > 0 AND ";
                }
            }
        }

        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
                case 'date':
                    $order['by'] = 'uvm.date';
                    break;
                default:
                    $order['by'] = 'uvm.id';
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
            "SELECT uvm.* 
                    FROM ulab_vlk_measuring uvm 
                    WHERE uvm.is_deleted = 0 AND {$where} 
                    ORDER BY  {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT uvm.* 
                    FROM ulab_vlk_measuring uvm 
                    WHERE uvm.is_deleted = 0 AND {$where}"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT uvm.* 
                    FROM ulab_vlk_measuring uvm 
                    WHERE uvm.is_deleted = 0 AND {$where}"
        )->SelectedRowsCount();


        //проверка на допуск к редактированию
        $isCanEdit = in_array($permissionInfo['id'], [ADMIN_PERMISSION, HEAD_IC_PERMISSION]);

        while ($row = $data->Fetch()) {
            $row['result'] = json_decode($row['result'], true);
            $row['ru_date'] = date('d.m.Y', strtotime($row['date']));
            $row['is_can_edit'] = $isCanEdit;

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    /**
     * @param $umcId
     * @param $dateStart
     * @param $dateEnd
     * @return array
     */
    public function getVlkMeasuringByDate($umcId, $dateStart, $dateEnd)
    {
        $response = [];

        if (empty($dateStart) || empty($dateEnd) || empty($umcId)) {
            return $response;
        }

        $result = $this->DB->Query(
            "SELECT * FROM ulab_vlk_measuring 
                        WHERE umc_id = {$umcId} AND date between '{$dateStart}' AND '{$dateEnd}' AND is_deleted = 0"
        );

        while ($row = $result->Fetch()) {
            $row['result'] = json_decode($row['result'], true);

            $response[] = $row;
        }

        return $response;
    }

}