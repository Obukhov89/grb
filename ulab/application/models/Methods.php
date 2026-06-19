<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Модель для работы с методиками для ГОСТа
 * Class Methods
 */
class Methods extends Model
{

    /**
     * @param $data
     * @return array
     */
    private function prepearData($data)
    {
        $columns = $this->getColumnsByTable('ulab_methods');

        $sqlData = [];

        if ( isset($data['clause']) ) {
            $data['clause'] = StringHelper::removeSpace($data['clause']);
        }
        if ( isset($data['name']) ) {
            $data['name'] = StringHelper::removeSpace($data['name']);
        }

        foreach ($columns as $column) {
            if (isset($data[$column])) {
                $sqlData[$column] = $this->quoteStr($this->DB->ForSql(trim($data[$column])));
            }
        }

        return $sqlData;
    }


    /**
     * @param $data
     * @return false|mixed|string
     */
    public function add($data)
    {
        $sqlData = $this->prepearData($data);

        $historyModel = new History();
        $userModel = new User();

        $user = $userModel->getUserData($_SESSION['SESS_AUTH']['USER_ID']);

        $idMethod = $this->DB->Insert('ulab_methods', $sqlData);

        $dataHistory = [
            'DATE' => date('Y-m-d H:i:s'),
            'TYPE' => "Создана Методика. ид новой Методики: {$idMethod}. ид ГОСТа: {$data['gost_id']}.",
            'USER_ID' => $_SESSION['SESS_AUTH']['USER_ID'],
            'ASSIGNED' => $user['user_name']
        ];

        $historyModel->addHistory($dataHistory);

        return $idMethod;
    }


    /**
     * @param $id
     * @param $data
     * @return bool|int|string
     */
    public function update($id, $data)
    {
        $sqlData = $this->prepearData($data);

        $historyModel = new History();
        $userModel = new User();

        $user = $userModel->getUserData($_SESSION['SESS_AUTH']['USER_ID']);

        $methodInfo = $this->get($id);

        $dataHistory = [
            'DATE' => date('Y-m-d H:i:s'),
            'TYPE' => "Отредактирована Методика. ид: {$id}, ид ГОСТа: {$methodInfo['gost_id']}",
            'USER_ID' => $_SESSION['SESS_AUTH']['USER_ID'],
            'ASSIGNED' => $user['user_name']
        ];

        $historyModel->addHistory($dataHistory);

        $where = "WHERE id = {$id}";
        return $this->DB->Update('ulab_methods', $sqlData, $where);
    }


    /**
     * @param $id
     * @return array|false
     */
    public function get($id)
    {
        if ( empty($id) ) {
            return [];
        }

        $userMethod = new User();
        $labModel = new Lab();

        $methodSql = $this->DB->Query(
            "select 
                    m.*, 
                    d.unit_rus, d.name as unit_name, d.fsa_id as unit_fsa_id,
                    g.reg_doc, g.year, g.description, g.materials,
                    p.fsa_id as mp_fsa_id, p.name as mp_name,
                    ml.name as measurement_name, ml.name_ru as measurement_name_ru
                from `ulab_methods` as m
                inner join `ulab_gost` as g on g.id = m.gost_id 
                left join `ulab_dimension` as d on d.id = m.unit_id
                left join ulab_measured_properties as p on p.id = m.measured_properties_id 
                left join ulab_measurement as ml on ml.id = m.measurement_id 
                where m.id = {$id}"
        );


        $result = $methodSql->Fetch();


        if ( !empty($result) ) {
        	if ($result['id'] == 2669) {
				$assignedList = $userMethod->getAllActiveUserId();
			} else {
            	$assignedList = $this->getAssigned($result['id']);
			}
            foreach ($assignedList as $user) {
                $result['assigned'][] = $userMethod->getUserShortById($user);
            }

            $result['laba'] = $this->getLab($result['id']);

            $strYear = !empty($result['year']) ? "-{$result['year']}" : '';
            $strClause = !empty($result['clause']) ? " {$result['clause']}" : '';

            $result['view_gost'] = "{$result['reg_doc']}{$strYear}{$strClause} | {$result['name']}";
            $result['view_gost_for_protocol'] = "{$result['reg_doc']}{$strYear}{$strClause}";
        }

        return $result;
    }


    public function getList()
    {
        $methodSql = $this->DB->Query(
            "select 
                    m.*, 
                    d.unit_rus, d.name as unit_name, d.fsa_id as unit_fsa_id,
                    g.reg_doc, g.year, g.description, g.materials,
                    p.fsa_id as mp_fsa_id, p.name as mp_name,
                    ml.name as measurement_name, ml.name_ru as measurement_name_ru
                from `ulab_methods` as m
                inner join `ulab_gost` as g on g.id = m.gost_id 
                left join `ulab_dimension` as d on d.id = m.unit_id
                left join ulab_measured_properties as p on p.id = m.measured_properties_id 
                left join ulab_measurement as ml on ml.id = m.measurement_id 
                where 1
                order by m.is_actual desc, m.gost_id asc, m.clause asc"
        );

        $result = [];

        while ($row = $methodSql->Fetch()) {
            $strYear = !empty($row['year']) ? "-{$row['year']}" : '';
            $strClause = !empty($row['clause']) ? " {$row['clause']}" : '';
            $row['view_gost'] = "{$row['reg_doc']}{$strYear}{$strClause} | {$row['name']}";

            if ( !$row['is_confirm'] ) {
                $row['date_color'] = '#dfdf11';
            }

            if ( !$row['is_actual'] ) {
                $row['date_color'] = '#f00';
            }

            $result[] = $row;
        }

        return $result;
    }


    /**
     * Получает список листов измерений
     * @return array
     */
    public function getMeasurementList()
    {
        $sql = $this->DB->Query(
            "select m.*
                    from `ulab_measurement` as m
                    where 1"
        );

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param $gostId
     * @return array
     */
    public function getListByGostId($gostId)
    {
        $sql = $this->DB->Query(
            "select m.*,
                        d.id unit_id, d.unit_rus, d.name unit_name, 
                        t.name test_method_name,
                        p.name mp_name
                    from `ulab_methods` as m
                    left join `ulab_dimension` as d on d.id = m.unit_id
                    left join `ulab_test_method` as t on t.id = m.test_method_id 
                    left join `ulab_measured_properties` as p on p.id = m.measured_properties_id 
                    where m.`is_actual` = 1 and m.`gost_id` = {$gostId} 
                    order by m.id desc"
        );

        $result = [];

        while ($row = $sql->Fetch()) {

            $result[] = $row;
        }

        return $result;
    }


    /**
     * делает методику неактуальной
     * @param $id
     */
    public function delete($id)
    {
        $historyModel = new History();
        $userModel = new User();

        $user = $userModel->getUserData($_SESSION['SESS_AUTH']['USER_ID']);

        $dataHistory = [
            'DATE' => date('Y-m-d H:i:s'),
            'TYPE' => "Методика отмечена неактуальной. ид: {$id}",
            'USER_ID' => $_SESSION['SESS_AUTH']['USER_ID'],
            'ASSIGNED' => $user['user_name']
        ];

        $historyModel->addHistory($dataHistory);

        $this->DB->Query("update ulab_methods set is_actual = 0, clause = concat('(не актульно) ', clause) where id = {$id}");
    }


    public function deleteByGost($gostId)
    {
        $this->DB->Update('ulab_methods', ['is_actual' => 0], "where gost_id = {$gostId}");
    }


    /**
     * Получает список Определяемой хар-ки
     * @return array
     */
    public function getMeasuredPropertiesList()
    {
        $sql = $this->DB->Query(
            "select m.*
                    from `ulab_measured_properties` as m
                    where m.`is_used` = 1"
        );

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * Получает список единиц измерений
     * @return array
     */
    public function getUnitList()
    {
        $sql = $this->DB->Query(
            "select m.*
                    from `ulab_dimension` as m
                    where m.`is_used` = 1"
        );

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getTestMethodList()
    {
        $sql = $this->DB->Query(
            "select * from `ulab_test_method`"
        );

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param $idMethod
     * @param $data
     */
    public function updateLab($idMethod, $data)
    {
        $this->DB->Query("delete from ulab_methods_lab where method_id = {$idMethod}");

        foreach ($data as $item) {
            $this->DB->Insert('ulab_methods_lab', ['method_id' => $idMethod, 'lab_id' => $item]);
        }
    }


    /**
     * @param $idMethod
     * @return array
     */
    public function getLab($idMethod)
    {
        if (empty($idMethod)) { return []; }

        $sql = $this->DB->Query("select lab_id from ulab_methods_lab where method_id = {$idMethod}");

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row['lab_id'];
        }

        return $result;
    }


    /**
     * @param $idMethod
     * @param $data
     */
    public function updateRoom($idMethod, $data)
    {
        $this->DB->Query("delete from ulab_methods_room where method_id = {$idMethod}");

        foreach ($data as $item) {
            $this->DB->Insert('ulab_methods_room', ['method_id' => $idMethod, 'room_id' => $item]);
        }
    }


    /**
     * @param $idMethod
     * @return array
     */
    public function getRoom($idMethod)
    {
        $sql = $this->DB->Query("select room_id from ulab_methods_room where method_id = {$idMethod}");

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row['room_id'];
        }

        return $result;
    }


    /**
     * @param $idMethod
     * @param $data
     */
    public function updateAssigned($idMethod, $data)
    {
        $this->DB->Query("delete from ulab_methods_assigned where method_id = {$idMethod}");

        foreach ($data as $item) {
            $this->DB->Insert('ulab_methods_assigned', ['method_id' => $idMethod, 'user_id' => $item]);
        }
    }


    /**
     * @param $methodId
     * @param $userId
     */
    public function toggleAssigned($methodId, $userId)
    {
        $sql = $this->DB->Query("select * from ulab_methods_assigned where method_id = {$methodId} and user_id = {$userId}");
        $rowCount = $sql->SelectedRowsCount();

        if ( $rowCount > 0 ) {
            $this->DB->Query("delete from ulab_methods_assigned where method_id = {$methodId} and user_id = {$userId}");
        } else {
            $this->DB->Insert('ulab_methods_assigned', ['method_id' => $methodId, 'user_id' => $userId]);
        }
    }


    /**
     * @param $idMethod
     * @return array
     */
    public function getAssigned($idMethod)
    {
        $sql = $this->DB->Query("select user_id from ulab_methods_assigned where method_id = {$idMethod}");

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row['user_id'];
        }

        return $result;
    }


    /**
     * журнал ОА
     * @param array $filter
     * @return array
     */
    public function getJournalList($filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'm.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // № ОА
                if ( isset($filter['search']['num_oa']) ) {
                    $where .= "m.num_oa LIKE '%{$filter['search']['num_oa']}%' AND ";
                }
                // Номер документа
                if ( isset($filter['search']['reg_doc']) ) {
                    $where .= "g.reg_doc LIKE '%{$filter['search']['reg_doc']}%' AND ";
                }
                // Наименование документа
                if ( isset($filter['search']['description']) ) {
                    $where .= "g.description LIKE '%{$filter['search']['description']}%' AND ";
                }
                // Год
                if ( isset($filter['search']['year']) ) {
                    $where .= "g.year LIKE '%{$filter['search']['year']}%' AND ";
                }
                // Наименование объекта
                if ( isset($filter['search']['materials']) ) {
                    $where .= "g.materials LIKE '%{$filter['search']['materials']}%' AND ";
                }
                // Определяемая характеристика / показатель
                if ( isset($filter['search']['name']) ) {
                    $where .= "m.name LIKE '%{$filter['search']['name']}%' AND ";
                }
                // Метод
                if ( isset($filter['search']['test_method']) ) {
                    $where .= "tm.name LIKE '%{$filter['search']['test_method']}%' AND ";
                }
                // Единица измерения
                if ( isset($filter['search']['unit_rus']) ) {
                    $where .= "d.unit_rus LIKE '%{$filter['search']['unit_rus']}%' AND ";
                }
                // Единица измерения
                if ( isset($filter['search']['in_field']) ) {
                    $where .= "m.in_field = '%{$filter['search']['in_field']}%' AND ";
                }
                // Единица измерения
                if ( isset($filter['search']['is_extended_field']) ) {
                    $where .= "m.is_extended_field = '%{$filter['search']['is_extended_field']}%' AND ";
                }
                // Цена
                if ( isset($filter['search']['price']) ) {
                    $where .= "m.price = '{$filter['search']['price']}' AND ";
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
                        $where .= "m.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 2 ) { // В ОА
                        $where .= "m.in_field = 1 AND m.is_extended_field = 0 AND m.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 3 ) { // РОА
                        $where .= "m.is_extended_field = 1 AND m.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 5 ) { // Вне ОА
                        $where .= "m.in_field = 0 AND m.is_actual = 1 AND m.is_extended_field = 0 AND ";
                    }
                    if ( $filter['search']['stage'] == 7 ) { // Не актуальные
                        $where .= "m.is_actual = 0 AND ";
                    }
                } else {
                    $where .= "m.is_actual = 1 AND ";
                }

            }
        }

        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
                case 'num_oa':
                    $order['by'] = 'm.num_oa';
                    break;
                case 'reg_doc':
                    $order['by'] = 'g.reg_doc';
                    break;
                case 'description':
                    $order['by'] = 'g.description';
                    break;
                case 'year':
                    $order['by'] = 'g.year';
                    break;
                case 'name':
                    $order['by'] = 'm.name';
                    break;
                case 'test_method':
                    $order['by'] = 'tm.name';
                    break;
                case 'unit_rus':
                    $order['by'] = 'd.unit_rus';
                    break;
                case 'clause':
                    $order['by'] = 'm.clause';
                    break;
                case 'gost_id':
                    $order['by'] = 'g.id';
                    break;
                default:
                    $order['by'] = 'm.num_oa';
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
            "SELECT distinct 
                        m.*, m.id method_id,
                        g.*, g.id gost_id,  
                        tm.name test_method, 
                        d.unit_rus,
                        p.fsa_id mp_fsa_id, p.name mp_name
                    FROM ulab_gost g
                    LEFT JOIN ulab_methods as m ON g.id = m.gost_id 
                    LEFT JOIN ulab_dimension as d ON d.id = m.unit_id 
                    LEFT JOIN ulab_measured_properties as p ON p.id = m.measured_properties_id 
                    LEFT JOIN ulab_test_method as tm ON tm.id = m.test_method_id 
                    LEFT JOIN ulab_methods_room as r ON r.method_id = m.id 
                    LEFT JOIN ulab_methods_lab as l ON l.method_id = m.id 
                    WHERE {$where}
                    ORDER BY  {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT distinct *
                    FROM ulab_gost g
                    LEFT JOIN ulab_methods m ON g.id = m.gost_id 
                    LEFT JOIN ulab_dimension d ON d.id = m.unit_id 
                    LEFT JOIN ulab_measured_properties as p ON p.id = m.measured_properties_id 
                    LEFT JOIN ulab_test_method tm ON tm.id = m.test_method_id 
                    LEFT JOIN ulab_methods_room as r ON r.method_id = m.id 
                    LEFT JOIN ulab_methods_lab as l ON l.method_id = m.id 
                    WHERE m.is_actual = 1"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT distinct *
                    FROM ulab_gost g
                    LEFT JOIN ulab_methods m ON g.id = m.gost_id 
                    LEFT JOIN ulab_dimension d ON d.id = m.unit_id
                    LEFT JOIN ulab_measured_properties as p ON p.id = m.measured_properties_id 
                    LEFT JOIN ulab_test_method tm ON tm.id = m.test_method_id 
                    LEFT JOIN ulab_methods_room as r ON r.method_id = m.id 
                    LEFT JOIN ulab_methods_lab as l ON l.method_id = m.id 
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
     * журнал ОА
     * @param array $filter
     * @return array
     */
    public function getJournaGostlList($filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'm.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // Номер документа
                if ( isset($filter['search']['reg_doc']) ) {
                    $where .= "g.reg_doc LIKE '%{$filter['search']['reg_doc']}%' AND ";
                }
                // Наименование документа
                if ( isset($filter['search']['description']) ) {
                    $where .= "g.description LIKE '%{$filter['search']['description']}%' AND ";
                }
                // Год
                if ( isset($filter['search']['year']) ) {
                    $where .= "g.year LIKE '%{$filter['search']['year']}%' AND ";
                }
                // Наименование объекта
                if ( isset($filter['search']['materials']) ) {
                    $where .= "g.materials LIKE '%{$filter['search']['materials']}%' AND ";
                }
            }
        }

        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
                case 'reg_doc':
                    $order['by'] = 'g.reg_doc';
                    break;
                case 'description':
                    $order['by'] = 'g.description';
                    break;
                case 'year':
                    $order['by'] = 'g.year';
                    break;
                default:
                    $order['by'] = 'g.id';
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
            "SELECT 
                    g.*, g.id gost_id, count(m.gost_id) as count_method
                FROM ulab_gost g
                LEFT JOIN ulab_methods as m ON g.id = m.gost_id 
                WHERE {$where}
                group by g.id
                ORDER BY  {$order['by']} {$order['dir']} {$limit}
                "
        );
		$this->pre( "SELECT 
                    g.*, g.id gost_id, count(m.gost_id) as count_method
                FROM ulab_gost g
                LEFT JOIN ulab_methods as m ON g.id = m.gost_id 
                WHERE {$where}
                group by g.id
                ORDER BY  {$order['by']} {$order['dir']} {$limit}
                ");
        $dataTotal = $this->DB->Query(
            "SELECT  g.id
                    FROM ulab_gost g
                    LEFT JOIN ulab_methods m ON g.id = m.gost_id 
                    WHERE m.is_actual = 1
                    group by g.id"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT  g.id
                    FROM ulab_gost g
                    LEFT JOIN ulab_methods m ON g.id = m.gost_id 
                    WHERE {$where}
                    group by g.id"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


    /**
     * журнал Отчет об использовании области аккредитации
     * @param array $filter
     * @return array
     */
    public function getJournalReportList($filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'm.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                if ( isset($filter['search']['dateStart']) ) {
                    $where .= "st.updated_at between '{$filter['search']['dateStart']}' AND '{$filter['search']['dateEnd']}' AND ";
                }
                // Номер документа
                if ( isset($filter['search']['reg_doc']) ) {
                    $where .= "g.reg_doc LIKE '%{$filter['search']['reg_doc']}%' AND ";
                }
                // Наименование документа
                if ( isset($filter['search']['description']) ) {
                    $where .= "g.description LIKE '%{$filter['search']['description']}%' AND ";
                }
                // Определяемая характеристика / показатель
                if ( isset($filter['search']['name']) ) {
                    $where .= "m.name LIKE '%{$filter['search']['name']}%' AND ";
                }
                // В области?
                if ( isset($filter['search']['in_field']) ) {
                    $where .= "m.in_field = '{$filter['search']['in_field']}' AND ";
                }
                // Лаба
                if ( isset($filter['search']['lab']) ) {
                    $where .= "l.`lab_id` = {$filter['search']['lab']} AND ";
                }

                // Статус
                if ( isset($filter['search']['stage']) ) {
                    if ( $filter['search']['stage'] == 1 ) { // Актуальные
                        $where .= "m.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 2 ) { // В ОА
                        $where .= "m.in_field = 1 AND m.is_extended_field = 0 AND m.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 3 ) { // РОА
                        $where .= "m.is_extended_field = 1 AND m.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 5 ) { // Вне ОА
                        $where .= "m.in_field = 0 AND m.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 7 ) { // Не актуальные
                        $where .= "m.is_actual = 0 AND ";
                    }
                } else {
                    $where .= "m.is_actual = 1 AND ";
                }

            }
        }

        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
                case 'reg_doc':
                    $order['by'] = 'g.reg_doc';
                    break;
                case 'description':
                    $order['by'] = 'g.description';
                    break;
                case 'name':
                    $order['by'] = 'm.name';
                    break;
                case 'clause':
                    $order['by'] = 'm.clause';
                    break;
                case 'count_method':
                    $order['by'] = 'count(m.id)';
                    break;
                default:
                    $order['by'] = 'm.num_oa';
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
            "SELECT 
                        m.*, m.id method_id,
                        g.*, g.id gost_id,  
                        p.fsa_id mp_fsa_id, p.name mp_name, 
                        count(m.id) as count_method
                    FROM ulab_gost as g
                    inner JOIN ulab_methods as m ON g.id = m.gost_id 
                    inner JOIN ulab_measured_properties as p ON p.id = m.measured_properties_id 
                    inner JOIN ulab_methods_lab as l ON l.method_id = m.id
                    inner join ulab_gost_to_probe as gtp on gtp.new_method_id = m.id AND gtp.actual_value IS NOT NULL
                    inner join ulab_material_to_request as mater on gtp.material_to_request_id = mater.id
                    inner join ulab_start_trials as st ON st.ugtp_id = gtp.id
                    WHERE {$where} and st.state = 'complete'
                    group by m.id
                    ORDER BY  {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT *
                    FROM ulab_gost as g
                    inner JOIN ulab_methods m ON g.id = m.gost_id 
                    inner JOIN ulab_measured_properties as p ON p.id = m.measured_properties_id 
                    inner JOIN ulab_methods_lab as l ON l.method_id = m.id 
                    inner join ulab_gost_to_probe as gtp on gtp.new_method_id = m.id AND gtp.actual_value IS NOT NULL
                    inner join ulab_material_to_request as mater on gtp.material_to_request_id = mater.id
                    inner join ulab_start_trials as st ON st.ugtp_id = gtp.id                
                    WHERE 1
                    group by m.id"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT *
                    FROM ulab_gost as g
                    inner JOIN ulab_methods as m ON g.id = m.gost_id 
                    inner JOIN ulab_measured_properties as p ON p.id = m.measured_properties_id 
                    inner JOIN ulab_methods_lab as l ON l.method_id = m.id
                    inner join ulab_gost_to_probe as gtp on gtp.new_method_id = m.id AND gtp.actual_value IS NOT NULL
                    inner join ulab_material_to_request as mater on gtp.material_to_request_id = mater.id
                    inner join ulab_start_trials as st ON st.ugtp_id = gtp.id
                    WHERE {$where} and st.state = 'complete'
                    group by m.id"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {

            $changeValue = 0;
            if ($row['count_method'] >= 2 && $row['count_method'] <= 25)
                $changeValue = 2;
            elseif ($row['count_method'] >= 26 && $row['count_method'] <= 50)
                $changeValue = 3;
            elseif ($row['count_method'] >= 51 && $row['count_method'] <= 90)
                $changeValue = 5;
            elseif ($row['count_method'] >= 91 && $row['count_method'] <= 150)
                $changeValue = 8;
            elseif ($row['count_method'] >= 151 && $row['count_method'] <= 280)
                $changeValue = 13;
            elseif ($row['count_method'] >= 281 && $row['count_method'] <= 500)
                $changeValue = 20;
            elseif ($row['count_method'] >= 501 && $row['count_method'] <= 1200)
                $changeValue = 32;
            elseif ($row['count_method'] >= 1201 && $row['count_method'] <= 3200)
                $changeValue = 50;
            elseif ($row['count_method'] >= 3201 && $row['count_method'] <= 10000)
                $changeValue = 80;
            elseif ($row['count_method'] >= 10001 && $row['count_method'] <= 35000)
                $changeValue = 125;
            elseif ($row['count_method'] >= 35001 && $row['count_method'] <= 150000)
                $changeValue = 200;
            elseif ($row['count_method'] >= 150001 && $row['count_method'] <= 500000)
                $changeValue = 315;
            elseif ($row['count_method'] >= 500001)
                $changeValue = 500;

            $row['count_vlk'] = $changeValue;
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


    /**
     * Журнал матрицы компетенции
     * @param array $filter
     * @return array
     */
    public function getJournalMatrixList($filter = [])
    {
        $labModel = new Lab();

        $labId = 0;
        $where = "";
        $limit = "";
        $order = [
            'by' => 'm.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // № ОА
                if ( isset($filter['search']['num_oa']) ) {
                    $where .= "m.num_oa LIKE '%{$filter['search']['num_oa']}%' AND ";
                }
                // Номер документа
                if ( isset($filter['search']['reg_doc']) ) {
                    $where .= "g.reg_doc LIKE '%{$filter['search']['reg_doc']}%' AND ";
                }
                // Год
                if ( isset($filter['search']['year']) ) {
                    $where .= "g.year LIKE '%{$filter['search']['year']}%' AND ";
                }

                // Определяемая характеристика / показатель
                if ( isset($filter['search']['name']) ) {
                    $where .= "m.name LIKE '%{$filter['search']['name']}%' AND ";
                }
                // Единица измерения
                if ( isset($filter['search']['in_field']) ) {
                    $where .= "m.in_field = '%{$filter['search']['in_field']}%' AND ";
                }
                // Единица измерения
                if ( isset($filter['search']['is_extended_field']) ) {
                    $where .= "m.is_extended_field = '%{$filter['search']['is_extended_field']}%' AND ";
                }

                // Лаба Комната
                if ( isset($filter['search']['lab']) ) {
                    $labId = $filter['search']['lab'];
                }

                // Статус
                if ( isset($filter['search']['stage']) ) {
                    if ( $filter['search']['stage'] == 1 ) { // Актуальные
                        $where .= "m.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 2 ) { // В ОА
                        $where .= "m.in_field = 1 AND m.is_extended_field = 0 AND m.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 3 ) { // РОА
                        $where .= "m.is_extended_field = 1 AND m.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 5 ) { // Вне ОА
                        $where .= "m.in_field = 0 AND m.is_actual = 1 AND ";
                    }
                    if ( $filter['search']['stage'] == 7 ) { // Не актуальные
                        $where .= "m.is_actual = 0 AND ";
                    }
                } else {
                    $where .= "m.is_actual = 1 AND ";
                }

            }
        }

        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
                case 'num_oa':
                    $order['by'] = 'm.num_oa';
                    break;
                case 'reg_doc':
                    $order['by'] = 'g.reg_doc';
                    break;
                case 'year':
                    $order['by'] = 'g.year';
                    break;
                case 'name':
                    $order['by'] = 'm.name';
                    break;
                case 'clause':
                    $order['by'] = 'm.clause';
                    break;
                default:
                    $order['by'] = 'm.num_oa';
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
            "SELECT distinct 
                        m.*, m.id method_id,
                        g.*, g.id gost_id,
                        p.fsa_id mp_fsa_id, p.name mp_name
                    FROM ulab_gost g
                    LEFT JOIN ulab_methods as m ON g.id = m.gost_id 
                    LEFT JOIN ulab_measured_properties as p ON p.id = m.measured_properties_id  
                    WHERE {$where}
                    ORDER BY  {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT distinct *
                    FROM ulab_gost g
                    LEFT JOIN ulab_methods m ON g.id = m.gost_id 
                    LEFT JOIN ulab_measured_properties as p ON p.id = m.measured_properties_id 
                    WHERE 1"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT distinct *
                    FROM ulab_gost g
                    LEFT JOIN ulab_methods m ON g.id = m.gost_id 
                    LEFT JOIN ulab_measured_properties as p ON p.id = m.measured_properties_id 
                    WHERE {$where}"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $row['assigned'] = $this->getAssigned($row['method_id']);

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;
        $result['columns'] = $labModel->getLabAndUser($labId);

        return $result;
    }


    /**
     * сохраняет расчет неопределенности
     * @param $idMethod
     * @param $data
     */
    public function updateUncertainty($idMethod, $data)
    {
        $this->DB->Query("delete from `ulab_methods_uncertainty` where method_id = {$idMethod}");
        foreach ($data as $row) {
            if ( $row['uncertainty_1'] == '' || $row['uncertainty_2'] == '' || $row['uncertainty_3'] == '' ) {
                continue;
            }

            if (isset($row['Rl']) && !is_numeric($row['Rl'])) {
                $row['Rl'] = 'NULL';
            }

            if (isset($row['r']) && !is_numeric($row['r'])) {
                $row['r'] = 'NULL';
            }

            if (isset($row['Kt']) && !is_numeric($row['Kt'])) {
                $row['Kt'] = 'NULL';
            }

            $row['method_id'] = $idMethod;
            unset($row['id']);

            $this->DB->Insert('ulab_methods_uncertainty', $row);
        }
    }


    public function updateResult($idMethod, $data)
    {
        $this->DB->Query("delete from `ulab_methods_result` where method_id = {$idMethod}");
        foreach ($data as $row) {
            if ( empty($row['unit_id']) ) {
                continue;
            }

            $row['method_id'] = $idMethod;
            unset($row['id']);

            $this->DB->Insert('ulab_methods_result', $row);
        }
    }


    /**
     * получает расчет неопределенности
     * @param $idMethod
     * @return array
     */
    public function getUncertainty($idMethod)
    {
        $sql = $this->DB->Query("select * from ulab_methods_uncertainty where method_id = {$idMethod}");

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Получает данные неопределённости в диапазоне от и до
     * @param $data
     * @param $range
     * @return array
     */
    public function findUncertaintyData($data, $range) {
        $response = [];

        if (!is_numeric($range)) {
            return $response;
        }

        foreach ($data as $item) {
            $uncertainty_1 = $item['uncertainty_1'] ?? null;
            $uncertainty_2 = $item['uncertainty_2'] ?? null;

            if (!is_numeric($uncertainty_1) || !is_numeric($uncertainty_2)) {
                continue;
            }

            if ($range >= $uncertainty_1 && $range <= $uncertainty_2) {
                $response = $item;
            }
        }

        return $response;
    }

    /**
     * сохраняет оборудование
     * @param $idMethod
     * @param $data
     */
    public function updateOborud($idMethod, $data)
    {
//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";
//        die();

        $this->DB->Query("delete from `ulab_methods_oborud` where method_id = {$idMethod}");
        foreach ($data as $row) {
//            if ( $row['gost'] == '' ) {
//                continue;
//            }

            $row['method_id'] = $idMethod;
            unset($row['id']);

            $pRow = $this->prepearTableData('ulab_methods_oborud', $row);

            $this->DB->Insert('ulab_methods_oborud', $pRow);
        }
    }




    /**
     * получает оборудование
     * @param $idMethod
     * @return array
     */
    public function getOborud($idMethod)
    {
        $sql = $this->DB->Query(
            "select mo.*, o.IDENT, o.IN_AREA, o.IN_STOCK, o.CHECKED, o.REG_NUM  
                from ulab_methods_oborud as mo
                INNER JOIN ba_oborud as o ON o.ID = mo.id_oborud
                where mo.method_id = {$idMethod} and o.ID = mo.id_oborud"
        );



        $result = [];

        while ($row = $sql->Fetch()) {

            switch ($row['IDENT']) {
                case 'SI':
                    $ident = "СИ";
                    break;
                case 'IO':
                    $ident = "ИО";
                    break;
                case 'VO':
                    $ident = "ВО";
                    break;
                case 'TS':
                    $ident = "ТС";
                    break;
                case 'SO':
                    $ident = "ТС";
                    break;
                case 'REACT':
                    $ident = "Реактивы";
                    break;
                case 'OOPP':
                    $ident = "ООПП";
                    break;
                default:
                    $ident = "";
            }

            $row['ident'] = $ident;

            $result[] = $row;
        }

        return $result;
    }

    public function getInterchangableOborud($idMethod)
    {
        $sql = $this->DB->Query(
            "
            SELECT umo.id, umo.method_id, umo.gost
                 , ba.ID as 'id_oborud1', umo.usage_time, umo.comment, ba.IDENT as 'IDENT1', ba.IN_AREA, ba.IN_STOCK, ba.CHECKED
                 , ba.OBJECT as 'OBJECT1', ba.TYPE_OBORUD as 'TYPE_OBORUD1', ba.FACTORY_NUMBER as 'FACTORY_NUMBER1', ba.REG_NUM as 'REG_NUM1'
                 , o.OBJECT as 'OBJECT2', o.TYPE_OBORUD as 'TYPE_OBORUD2', o.FACTORY_NUMBER as 'FACTORY_NUMBER2', o.REG_NUM as 'REG_NUM2'
                 , o.ID as 'id_oborud2'
                 , boi.id as 'inter_id'
            FROM ulab_methods_oborud umo
            INNER JOIN ba_oborud o ON o.ID = umo.id_oborud
            INNER JOIN ba_oborud_interchangeable boi ON boi.oborud_id = o.ID
            INNER JOIN ba_oborud ba ON ba.ID = boi.inter_oborud_id
            WHERE umo.method_id = {$idMethod} 
            GROUP BY ba.ID
            "
        );

        $result = [];

        while ($row = $sql->Fetch()) {

            switch ($row['IDENT1']) {
                case 'SI':
                    $ident = "СИ";
                    break;
                case 'IO':
                    $ident = "ИО";
                    break;
                case 'VO':
                    $ident = "ВО";
                    break;
                case 'TS':
                    $ident = "ТС";
                    break;
                case 'SO':
                    $ident = "ТС";
                    break;
                case 'REACT':
                    $ident = "Реактивы";
                    break;
                case 'OOPP':
                    $ident = "ООПП";
                    break;
                default:
                    $ident = "";
            }

            $row['ident1'] = $ident;

            switch ($row['IDENT2']) {
                case 'SI':
                    $ident = "СИ";
                    break;
                case 'IO':
                    $ident = "ИО";
                    break;
                case 'VO':
                    $ident = "ВО";
                    break;
                case 'TS':
                    $ident = "ТС";
                    break;
                case 'SO':
                    $ident = "ТС";
                    break;
                case 'REACT':
                    $ident = "Реактивы";
                    break;
                case 'OOPP':
                    $ident = "ООПП";
                    break;
                default:
                    $ident = "";
            }

            $row['ident2'] = $ident;

            $result[$row['id_oborud1']] = $row;

//            $this->pre($result);

        }

        return $result;
    }

    /**
     * получает оборудование с включением взаимозаменяемого оборудования
     * @param $idMethod
     * @return array
     */
    public function getOborudWithInterchangable($idMethod)
    {
        $sql = $this->DB->Query(
            "select mo.*, o.IDENT, o.IN_AREA, o.IN_STOCK, o.CHECKED, o.REG_NUM  
                from ulab_methods_oborud as mo
                INNER JOIN ba_oborud as o ON o.ID = mo.id_oborud
                where mo.method_id = {$idMethod} and o.ID = mo.id_oborud"
        );

//        $withInterResponse = $this->DB->Query(
//            "
//            SELECT umo.id, umo.method_id, umo.gost, ba.ID as 'id_oborud', umo.usage_time, umo.comment, ba.IDENT, ba.IN_AREA, ba.IN_STOCK, ba.CHECKED, ba.REG_NUM
//            FROM ulab_methods_oborud umo
//            INNER JOIN ba_oborud o ON o.ID = umo.id_oborud
//            INNER JOIN ba_oborud_interchangeable boi ON boi.oborud_id = o.ID
//            INNER JOIN ba_oborud ba ON ba.ID = boi.inter_oborud_id
//            WHERE umo.method_id = {$idMethod}
//            GROUP BY ba.ID
//            "
//        );



        $result = [];

        while ($row = $sql->Fetch()) {

            switch ($row['IDENT']) {
                case 'SI':
                    $ident = "СИ";
                    break;
                case 'IO':
                    $ident = "ИО";
                    break;
                case 'VO':
                    $ident = "ВО";
                    break;
                case 'TS':
                    $ident = "ТС";
                    break;
                case 'SO':
                    $ident = "ТС";
                    break;
                case 'REACT':
                    $ident = "Реактивы";
                    break;
                case 'OOPP':
                    $ident = "ООПП";
                    break;
                default:
                    $ident = "";
            }

            $row['ident'] = $ident;

            $result[$row['id_oborud']] = $row;
        }

//        while ($row = $withInterResponse->Fetch()) {
//
//            switch ($row['IDENT']) {
//                case 'SI':
//                    $ident = "СИ";
//                    break;
//                case 'IO':
//                    $ident = "ИО";
//                    break;
//                case 'VO':
//                    $ident = "ВО";
//                    break;
//                case 'TS':
//                    $ident = "ТС";
//                    break;
//                case 'SO':
//                    $ident = "ТС";
//                    break;
//                case 'REACT':
//                    $ident = "Реактивы";
//                    break;
//                case 'OOPP':
//                    $ident = "ООПП";
//                    break;
//                default:
//                    $ident = "";
//            }
//
//            $row['ident'] = $ident;
//
//            $result[$row['id_oborud']] = $row;
//        }

        return $result;
    }


    /**
     * @param $methodId
     * @return false|mixed|string
     */
    public function copyMethod($methodId, $newGostId = '')
    {
        $dataSource = $this->get($methodId);

        if ( empty($dataSource) ) {
            return false;
        }

        if ( !empty($newGostId) ) {
            $dataSource['gost_id'] = $newGostId;
        }

        unset($dataSource['id']);
//        $dataSource['name'] = 'КОПИЯ ' . $dataSource['name'];
        $dataSource['is_confirm'] = 0;

        $idNewMethod = $this->add($dataSource);

        if ( empty($idNewMethod) ) {
            return false;
        }

        $dataLab = $this->getLab($methodId);
        $dataRoom = $this->getRoom($methodId);
        $dataAssigned = $this->getAssigned($methodId);
        $dataOborud = $this->getOborud($methodId);
        $dataUncertainty = $this->getUncertainty($methodId);

        $this->updateLab($idNewMethod, $dataLab);
        $this->updateRoom($idNewMethod, $dataRoom);
        $this->updateAssigned($idNewMethod, $dataAssigned);
        $this->updateOborud($idNewMethod, $dataOborud);
        $this->updateUncertainty($idNewMethod, $dataUncertainty);

        $historyModel = new History();
        $userModel = new User();

        $user = $userModel->getUserData($_SESSION['SESS_AUTH']['USER_ID']);

        $dataHistory = [
            'DATE' => date('Y-m-d H:i:s'),
            'TYPE' => "Скопирована Методика. ид источник: {$methodId}, ид новый: {$idNewMethod}",
            'USER_ID' => $_SESSION['SESS_AUTH']['USER_ID'],
            'ASSIGNED' => $user['user_name']
        ];

        $historyModel->addHistory($dataHistory);

        return $idNewMethod;
    }


    /**
     * @param $methodId
     */
    public function deletePermanentlyMethod($methodId)
    {
        $this->updateLab($methodId, []);
        $this->updateRoom($methodId, []);
        $this->updateAssigned($methodId, []);
        $this->updateOborud($methodId, []);
        $this->updateUncertainty($methodId, []);

        $this->DB->Query("delete from ulab_methods where id = {$methodId}");
    }

    /**
     * @param int $roomId
     * @return array
     */
    public function getMethodsByRoom(int $roomId): array
    {
        $result = [];

        if (empty($roomId) && $roomId < 0) {
            return $result;
        }

        $sql = $this->DB->Query(
            "select um.*, ug.reg_doc from ulab_methods_room umr 
                inner join ulab_methods um on um.id = umr.method_id 
                inner join ulab_gost ug on ug.id = um.gost_id 
                where umr.room_id = {$roomId}"
        );

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param $methodId
     * @param $newPrice
     * @return array|bool[]
     */
    public function setNewPrice($methodId, $newPrice)
    {
        $data = $this->prepearTableData('ulab_methods', ['price' => $newPrice]);

        $result1 = $this->DB->Update('ulab_methods', $data, "WHERE id = {$methodId}");

        $result2 = $this->DB->Update('ba_gost', ['PRICE' => (float)$newPrice], "WHERE ulab_method_id = {$methodId}");

        if ( empty($result1)/* || empty($result2) */) {
            return ['success' => false, 'error' => 'Не удалось обновить данные'];
        }

        return ['success' => true];
    }

    /**
     * @param array $ugtpIds
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     */
    public function getConditionsForGost(array $ugtpIds, string $dateStart, string $dateEnd): array
    {
        $labModel = new Lab();

        $result = [];

        if ( empty($ugtpIds) ) {
            return $result;
        }

        $periods = $this->getDatesFromRange($dateStart, $dateEnd, $format = 'd.m.Y');

        $strUgtpIds = implode(',', $ugtpIds);
        $where = "ugtp.id IN ({$strUgtpIds})";

        $sql = $this->DB->Query(
            "SELECT ugtp.*, room.room_id  
                    FROM ulab_gost_to_probe ugtp 
                    INNER JOIN ulab_methods_room room ON room.method_id = ugtp.new_method_id 
                    INNER JOIN ulab_gost_room ugtp_room ON ugtp_room.ugtp_id = ugtp.id and room.room_id = ugtp_room.room_id
                    WHERE {$where}"
        );

        while ($row = $sql->Fetch()) {
            $roomId = (int)$row['room_id'];

            $result[$row['id']]['id'] = $row['id']; // id - ulab_gost_to_probe
            $result[$row['id']]['room_ids'][] = $row['room_id'];
            $result[$row['id']]['rooms'][] = $labModel->getRoomById($roomId);
            $result[$row['id']]['conditions'][] = $labModel->getConditionsByRoom($roomId, $dateStart, $dateEnd);
            $result[$row['id']]['method'] = $this->get($row['method_id']);

            //находим даты для которых не были заполнены условия окружающей среды
            $periodsDB = $labModel->getDatesByPeriodsForRoom($roomId, $dateStart, $dateEnd);
            $periodsDiff = array_diff($periods, $periodsDB);
            $result[$row['id']]['no_conditions'][] = implode(',' , $periodsDiff);
        }

        return $result;
    }

    /**
     * @param $start
     * @param $end
     * @param string $format
     * @return array
     * @throws Exception
     */
    public function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = [];
        $year = (new DateTime())->format('Y');
        /*1, 2, 3, 4, 5, 6 и 8 января — Новогодние каникулы
        7 января — Рождество Христово
        23 февраля — День защитника Отечества
        8 марта — Международный женский день
        1 мая — Праздник Весны и Труда
        9 мая — День Победы
        12 июня — День России
        4 ноября — День народного единства*/
        $holidays = [
            "{$year}-01-01", "{$year}-01-02", "{$year}-01-03", "{$year}-01-04", "{$year}-01-05", "{$year}-01-06", "{$year}-01-08",
            "{$year}-01-07",
            "{$year}-02-23",
            "{$year}-03-08",
            "{$year}-05-01",
            "{$year}-05-09",
            "{$year}-06-12",
            "{$year}-11-04",
            "2023-11-06"
        ];

        $interval = new DateInterval('P1D');

        if ( empty($start) && empty($end) ) {
            return $array;
        }

        if ( empty($start) ) {
            $start = $end;
        }

        if ( empty($end) ) {
            $end = $start;
        }

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach($period as $date) {
            $curr = $date->format('D');
            $d = $date->format('Y-m-d');
            
            // Не учитывать если суббота, воскресенье и праздники
            if ($curr == 'Sat' || $curr == 'Sun' || in_array($d, HOLIDAYS)) {
                continue;
            }

            $array[] = $date->format($format);
        }

        return $array;
    }

    /**
     * @param array $ugtpIds
     * @param string $dateStart
     * @param string $dateEnd
     * @param int $protocolId
     * @return array
     */
    public function validateMethods($ugtpIds, $dateStart, $dateEnd, $protocolId)
    {
        if ( $protocolId == 6714 ) {
            return [
                'success' => true,
            ];
        }

        $oborudModel = new Oborud();
        $protocolModel = new Protocol();
        $labModel = new Lab();

        $isSuccess = true;
        $errors = []; // тексты сообщений ошибок
        $conditionList = "<a href='".URI."/lab/conditionList'>Журнал условий</a>";
        $dateStartRu = $dateStart ? date('d.m.Y', strtotime($dateStart)) : '';
        $dateEndRu = $dateEnd ? date('d.m.Y', strtotime($dateEnd)) : '';

        $gostForConditions = $this->getConditionsForGost($ugtpIds, $dateStart, $dateEnd);
        $conditionsForOboruds = $oborudModel->getConditionsForOboruds($ugtpIds, $dateStart, $dateEnd);

        //для проверки оборудования которое есть у методик и оборудование которые выбрано в протоколе
        $oborudsForProtocols = $oborudModel->getOborudsForProtocols($protocolId);
        $oborudsForrMethods = $oborudModel->getOborudsForMethods($protocolId);
        $oborudsForProtocolsIds = array_column($oborudsForProtocols, 'bo_id');
        $oborudsForMethodsIds = array_column($oborudsForrMethods, 'bo_id');

        $oborudProtocol = $oborudModel->getTzObConnectByProtocolId($protocolId);
        $protocol = $protocolModel->getProtocolById((int)$protocolId);

        $haveException = false;


        // Текущий диапазон проверки
        if ( empty($dateEnd) ) {
            $range = $dateStartRu;
        } elseif ( empty($dateStart) ) {
            $range = $dateEndRu;
        } else {
            $range = "$dateStartRu - $dateEndRu";
        }
        
        // Проверка привязки оборудования к протоколу
        if ( !empty($protocolId) && empty($oborudProtocol) ) {
            $protocolNumber = $protocol['NUMBER'] ?: 'Номер не присвоен';
            $protocolAnchor = "<a href='".URI."/result/resultCard/{$protocol['DEAL_ID']}?protocol_id={$protocol['ID']}'>{$protocolNumber}</a>";
            $errors[] =
                "Внимание! Не выбрано оборудование или не сохранили данные для испытания в протоколе {$protocolAnchor}. Выберите оборудование и сохраните данные";
        }

        if ( !empty($dateStart) && !empty($dateEnd) && ($dateStart > $dateEnd)  ) {
            //$errors[] =
            //    "Внимание! Дата начала испытания {$dateStartRu} , больше даты окончания испытания {$dateEndRu}!";
        }

        // Проверка методик
        foreach ($gostForConditions as $gost) {
            $method = $gost['method'] ?: [];

            // методики отбора пропускаются
            if ($method['is_selection']) {
                continue;
            }

            $anchor = "<a href='".URI."/gost/method/{$method['id']}'>{$method['view_gost_for_protocol']}</a>";

            // Проверка соответствия текущих условий и условий методик
            foreach ($gost['conditions'] as $key => $condition) {
                $roomName = $gost['rooms'][$key]['name'] ?? '';

                // Проверка привязки методики к помещению
                if (empty($gost['room_ids'][$key])) {
                    $errors[] =
                        "Внимание! Не выбрано помещение для проведения испытаний или методика {$anchor} не привязна к помещению!";
                    continue;
                }

                // Проверка на внесение данных условий окружающей среды в "Журнал условий" за $range, для помещений привязаных к методике
                if (empty($condition['amount'])) {
                    $errors[] =
                        "Внимание! Не занесены данные условий окружающей среды за {$range}, методика {$anchor}, {$roomName}! {$conditionList}";
                    continue;
                }

                // Если не для всех дат были заполнены условия окружающей среды
                if (!empty($gost['no_conditions'][$key])) {
                    $errors[] =
                        "Внимание! Для методики {$anchor}, {$roomName}, за {$gost['no_conditions'][$key]} нет данных условий окружающей среды! {$conditionList}";
                    continue;
                }

                // Температура
                if (!$method['is_not_cond_temp']) { // если нормируется
                    if ($condition['min_temp'] < $method['cond_temp_1'] || $condition['max_temp'] > $method['cond_temp_2']) {
                        $errors[] =
                            "Внимание! Температура при проведении испытаний, {$roomName} {$conditionList}, не соответствует условиям в методике {$anchor} за {$range}!";

                        $haveException = true;
                    }
                }

                // Влажность
                if (!$method['is_not_cond_wet']) { // если нормируется
                    if ($condition['min_humidity'] < $method['cond_wet_1'] || $condition['max_humidity'] > $method['cond_wet_2']) {
                        $errors[] =
                            "Внимание! Влажность при проведении испытаний, {$roomName} {$conditionList}, не соответствует условиям в методике {$anchor} за {$range}!";
                    }
                }

                // Актуальность методики
                if ( !$method['is_actual'] ) {
                    $errors[] =
                        "Внимание! Методика {$anchor} неактуальна!";
                }

                // Подтвержденность
                if ( !$method['is_confirm'] ) {
                    $errors[] =
                        "Внимание! Методика {$anchor} не проверена отделом метрологии!";
                }
            }
        }


        // Проверка на условия оборудования
		foreach ($conditionsForOboruds as $data) {
            foreach ($data['oborud'] as $key => $oborud) {
                $roomName = $data['rooms'][$key]['name'] ?? '';
                $condition = $data['conditions'][$key] ?: [];

                $anchor = "<a href='/ulab/oborud/edit/{$oborud['bo_id']}'>{$oborud['OBJECT']} {$oborud['TYPE_OBORUD']} {$oborud['REG_NUM']}</a>";

                // Если переосное оборудование то не проверять
				$this->pre($data);
                if (!$oborud['is_portable']) {
                    // Если не для всех дат были заполнены условия окружающей среды
                    if (!empty($data['no_conditions'][$key])) {
                        $errors[] =
                            "Внимание! Для оборудования {$anchor}, {$roomName}, за {$data['no_conditions'][$key]} нет данных условий окружающей среды! {$conditionList}";
                        continue;
                    }

                    // Проверка привязки оборудования к помещению
                    if (empty($oborud['roomnumber'])) {
                        $errors[] =
                            "Внимание! Оборудование {$anchor} не привязно к помещению!";
                        continue;
                    }

                    // Температура
                    if (empty($oborud['TEMPERATURE'])) { // если нормируется
                        $tempO1 = (float)$oborud['TOO_EX'];
                        $tempO2 = (float)$oborud['TOO_EX2'];

                        if ($condition['min_temp'] < $tempO1 || $condition['max_temp'] > $tempO2) {
                            $errors[] =
                                "Внимание! Температура при проведении испытаний, {$roomName} {$conditionList}, не соответствует условиям эксплуатации оборудования {$anchor} за {$range}!";
                        }
                    }

                    // Влажность
                    if (empty($oborud['HUMIDITY'])) { // если нормируется
                        $wetO1 = (float)$oborud['OVV_EX'];
                        $wetO2 = (float)$oborud['OVV_EX2'];

                        if ($condition['min_humidity'] < $wetO1 || $condition['max_humidity'] > $wetO2) {
                            $errors[] =
                                "Внимание! Влажность при проведении испытаний, {$roomName} {$conditionList}, не соответствует условиям эксплуатации оборудования {$anchor} за {$range}!";
                        }
                    }
                }

                // Проверка на проверку
                if (!$oborud['CHECKED']) {
                    $errors[] = "Внимание! Оборудование {$anchor} не проверено!";
                }

                // Срок поверки
                $poverka = strtotime($oborud['POVER']) - strtotime($dateEnd);
                if ($poverka <= 0 && $oborud['IDENT'] != "OOPP" && $oborud['IDENT'] != "VO") {
                    $errors[] = "Внимание! Истек срок поверки оборудования {$anchor}!";
                }

                // TODO: Сертификаты??
                if ($oborud['IDENT'] != "VO" && $oborud['IDENT'] != "TS" && $oborud['IDENT'] != "REACT") {

                }
            }
        }

        // Сравниваем выбранное оборудование в протоколах $idsOborudsForProtocols  с оборудованием у методик $idsOborudForMethods
        $oborudsDiff = array_diff($oborudsForProtocolsIds, $oborudsForMethodsIds);
        if ( !empty($oborudsDiff) ) {
            foreach ($oborudsForProtocols as $oborud) {
                if ( !in_array($oborud['bo_id'], $oborudsDiff) ) {
                    continue;
                }

                // Если переосное оборудование то не проверять
                if (!$oborud['is_portable']) {
                    $protocolNumber = $oborud['p_number'] ?: 'Номер не присвоен';
                    $oborudAnchor = "<a href='/oborud.php?ID={$oborud['bo_id']}'>{$oborud['OBJECT']} {$oborud['TYPE_OBORUD']} {$oborud['REG_NUM']}</a>";
                    $protocolAnchor = "<a href='" . URI . "/result/resultCard/{$oborud['deal_id']}?protocol_id={$oborud['protocol_id']}'>{$protocolNumber}</a>";
                    $errors[] = "Внимание! Выбранное оборудование {$oborudAnchor} для протокола {$protocolAnchor} в результатах испытаний, не привязано к методикам по которым производят испытания!";
                }
            }
        }

        $errors = array_unique($errors);


        if ( !empty($errors)) {
            $isSuccess = false;
        }

        if (count($errors) == 1 && $haveException) {
            $isSuccess = true;
        }

        return [
            'success' => $isSuccess,
            'errors' => $errors,
            'data' => '',
        ];

    }

    /**
     * @param $ugtpId
     * @return array|false
     */
    public function getMethodByUgtpId($ugtpId)
    {
        if ( empty($ugtpId) ) {
            return [];
        }

        $methodSql = $this->DB->Query(
            "select 
                    m.*, 
                    d.unit_rus, d.name as unit_name, d.fsa_id as unit_fsa_id,
                    g.reg_doc, g.year, g.description, g.materials,
                    p.fsa_id as mp_fsa_id, p.name as mp_name,
                    ml.name as measurement_name, ml.name_ru as measurement_name_ru
                from `ulab_gost_to_probe` as gtp
                left join `ulab_methods` as m on m.id = gtp.method_id 
                left join `ulab_gost` as g on g.id = m.gost_id 
                left join `ulab_dimension` as d on d.id = m.unit_id
                left join ulab_measured_properties as p on p.id = m.measured_properties_id 
                left join ulab_measurement as ml on ml.id = m.measurement_id 
                where gtp.id = {$ugtpId}"
        );

        $result = $methodSql->Fetch();

        if ( !empty($result) ) {
            $strYear = !empty($result['year']) ? "-{$result['year']}" : '';
            $strClause = !empty($result['clause']) ? " {$result['clause']}" : '';
            $result['laba'] = $this->getLab($result['id']);
            $result['assigned'] = $this->getAssigned($result['id']);
            $result['view_gost'] = "{$result['reg_doc']}{$strYear}{$strClause} | {$result['name']}";
            $result['view_gost_for_protocol'] = "{$result['reg_doc']}{$strYear}{$strClause}";
        }

        return $result;
    }

    public function statsUsedTests()
	{
		$lab = new Lab();
		$where = "";
		$limit = "";
		$order = [
			'by' => 'cmi',
			'dir' => 'DESC'
		];
		if ( !empty($filter) ) {
			// из $filter собирать строку $where тут
			// формат такой: $where .= "что-то = чему-то AND ";
			// или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
			// слева без пробела, справа всегда AND пробел

			// работа с фильтрами
			if ( !empty($filter['search']) ) {
				// Номер
				if ( isset($filter['search']['NUMBER']) ) {
					$where .= "d.NUMBER LIKE '%{$filter['search']['NUMBER']}%' AND ";
				}
				// Дата
				if ( isset($filter['search']['DATE']) ) {
					$where .= "LOCATE('{$filter['search']['DATE']}', DATE_FORMAT(d.DATE, '%d.%m.%Y')) > 0 AND ";
				}
				if ( isset($filter['search']['dateStart']) ) {
					$where .= "(d.DATE >= '{$filter['search']['dateStart']}' AND d.DATE <= '{$filter['search']['dateEnd']}') AND ";
				}
				// Клиент
				if ( isset($filter['search']['COMPANY_TITLE']) ) {
					$where .= "b.COMPANY_TITLE LIKE '%{$filter['search']['COMPANY_TITLE']}%' AND ";
				}
				// Клиент
				if ( isset($filter['search']['linkName2']) ) {
					if ($filter['search']['linkName2'] == 1) {
						$where .= "d.PDF is not null AND ";
					} elseif ($filter['search']['linkName2'] == 2) {
						$where .= "d.PDF is null AND ";
					}
				}
				// везде
				if ( isset($filter['search']['everywhere']) ) {
					$where .=
						"(
                        d.NUMBER LIKE '%{$filter['search']['everywhere']}%' 
                        OR d.DATE LIKE '%{$filter['search']['everywhere']}%' 
                        OR b.COMPANY_TITLE LIKE '%{$filter['search']['everywhere']}%' 
                        ) AND ";
				}
			}

			// работа с сортировкой
			if ( !empty($filter['order']) ) {
				if ( $filter['order']['dir'] === 'asc' ) {
					$order['dir'] = 'ASC';
				}

				switch ($filter['order']['by']) {
					case 'number':
						$order['by'] = 'd.NUMBER';
						break;
					case 'DATE':
						$order['by'] = 'd.DATE';
						break;
					case 'COMPANY_TITLE':
						$order['by'] = 'b.COMPANY_TITLE';
						break;
				}
			}

			// работа с пагинацией
			if ( isset($filter['paginate']) ) {
				$offset = 0;
				// количество строк на страницу
				if ( isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0 ) {
					$length = $filter['paginate']['length'];

					if ( isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0 ) {
						$offset = $filter['paginate']['start'];
					}
					$limit = "LIMIT {$offset}, {$length}";
				}
			}
		}
		$where .= "1 ";

		$data = $this->DB->Query(
			"select ugtp.method_id, count(ugtp.method_id) as cmi
 							from ulab_material_to_request as umtr
							left join ba_tz as bt on bt.ID_Z = umtr.deal_id
							left join ulab_gost_to_probe as ugtp on umtr.id = ugtp.material_to_request_id
							where bt.DATE_CREATE_TIMESTAMP >= '2023-01-01 00:00:00' and umtr.deal_id > 9357  
							and bt.STAGE_ID IN ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON') 
							AND IF(bt.ACT_NUM, TRUE, FALSE) = TRUE AND {$where}
							group by ugtp.method_id
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
		);

		$dataTotal = $this->DB->Query(
			"select ugtp.method_id, count(ugtp.method_id) as cmi
 							from ulab_material_to_request as umtr
							left join ba_tz as bt on bt.ID_Z = umtr.deal_id
							left join ulab_gost_to_probe as ugtp on umtr.id = ugtp.material_to_request_id
							where bt.DATE_CREATE_TIMESTAMP >= '2023-01-01 00:00:00' and umtr.deal_id > 9357  
							and bt.STAGE_ID IN ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON') 
							AND IF(bt.ACT_NUM, TRUE, FALSE) = TRUE AND  {$where}
							group by ugtp.method_id"
		)->Fetch();

		$dataFiltered = $this->DB->Query(
			"select ugtp.method_id, count(ugtp.method_id) as cmi
 							from ulab_material_to_request as umtr
							left join ba_tz as bt on bt.ID_Z = umtr.deal_id
							left join ulab_gost_to_probe as ugtp on umtr.id = ugtp.material_to_request_id
							where bt.DATE_CREATE_TIMESTAMP >= '2023-01-01 00:00:00' and umtr.deal_id > 9357  
							and bt.STAGE_ID IN ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON') 
							AND IF(bt.ACT_NUM, TRUE, FALSE) = TRUE AND  {$where}
							group by ugtp.method_id"
		)->Fetch();

		$result = [];
		$row = [];

		while ($row = $data->Fetch()) {

			$gost = $this->get($row['method_id']);
			$row['name_gost'] = $gost['view_gost_for_protocol'];
			$row['specification'] = $gost['name'];
			$labs = $this->getLab($row['method_id']);
			if (empty($labs)) {$row['lab'] = [];}
			foreach ($labs as $itemLab) {
				if (empty($itemLab)) {$row['lab'] = [];}
				$row['lab'][] = $lab->getLabaById($itemLab);
			}

			$result[] = $row;
		}

		$result['recordsTotal'] = $dataTotal['val'];
		$result['recordsFiltered'] = $dataFiltered['val'];

		return $result;

	}

	public function methodsJournal($filter)
	{
		$where = "";
		$limit = "";
		$order = [
			'by' => 'm.id',
			'dir' => 'DESC'
		];
		if ( !empty($filter) ) {
			// из $filter собирать строку $where тут
			// формат такой: $where .= "что-то = чему-то AND ";
			// или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
			// слева без пробела, справа всегда AND пробел

			// работа с фильтрами
			if ( !empty($filter['search']) ) {
				// ид
				if ( isset($filter['search']['id']) ) {
					$where .= "m.`gost_id` =  '{$filter['search']['id']}' AND ";
				}
				// Определяемая характеристика
				if ( isset($filter['search']['name']) ) {
					$where .= "m.name LIKE '%{$filter['search']['name']}%' AND ";
				}
				// Пункт
				if ( isset($filter['search']['clause']) ) {
					$where .= "m.clause LIKE '%{$filter['search']['clause']}%' AND ";
				}
				// Метод
				if ( isset($filter['search']['test_method_name']) ) {
					$where .= "m.test_method_name LIKE '%{$filter['search']['test_method_name']}%' AND ";
				}
			}

			// работа с сортировкой
			if ( !empty($filter['order']) ) {
				if ( $filter['order']['dir'] === 'asc' ) {
					$order['dir'] = 'ASC';
				}

				switch ($filter['order']['by']) {
					case 'is_confirm':
						$order['by'] = 'm.is_confirm';
						break;
					case 'in_field':
						$order['by'] = 'm.in_field';
						break;
					case 'is_extended_field':
						$order['by'] = 'm.is_extended_field';
						break;
				}
			}

			// работа с пагинацией
			if ( isset($filter['paginate']) ) {
				$offset = 0;
				// количество строк на страницу
				if ( isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0 ) {
					$length = $filter['paginate']['length'];

					if ( isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0 ) {
						$offset = $filter['paginate']['start'];
					}
					$limit = "LIMIT {$offset}, {$length}";
				}
			}
		}
		$where .= "1 ";

		$data = $this->DB->Query(
			"select m.*,
                        d.id unit_id, d.unit_rus, d.name unit_name, 
                        t.name test_method_name,
                        p.name mp_name
                    from `ulab_methods` as m
                    left join `ulab_dimension` as d on d.id = m.unit_id
                    left join `ulab_test_method` as t on t.id = m.test_method_id 
                    left join `ulab_measured_properties` as p on p.id = m.measured_properties_id 
                    where m.`is_actual` = 1 and {$where} 
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
		);

		$dataTotal = $this->DB->Query(
			"select count(*) val
                    from `ulab_methods` as m
                    left join `ulab_dimension` as d on d.id = m.unit_id
                    left join `ulab_test_method` as t on t.id = m.test_method_id 
                    left join `ulab_measured_properties` as p on p.id = m.measured_properties_id 
                    where m.`is_actual` = 1 and m.`gost_id` =  {$filter['search']['id']}"
		)->Fetch();

		$dataFiltered = $this->DB->Query(
			"select count(*) val
                    from `ulab_methods` as m
                    left join `ulab_dimension` as d on d.id = m.unit_id
                    left join `ulab_test_method` as t on t.id = m.test_method_id 
                    left join `ulab_measured_properties` as p on p.id = m.measured_properties_id 
                    where m.`is_actual` = 1 and {$where} "
		)->Fetch();

		$result = [];
		$row = [];

		while ($row = $data->Fetch()) {

			$gost = $this->get($row['method_id']);
			$row['name_gost'] = $gost['view_gost_for_protocol'];
			$row['specification'] = $gost['name'];
			$labs = $this->getLab($row['method_id']);
			if (empty($labs)) {$row['lab'] = [];}
			foreach ($labs as $itemLab) {
				if (empty($itemLab)) {$row['lab'] = [];}
				$row['lab'][] = $lab->getLabaById($itemLab);
			}

			$result[] = $row;
		}

		$result['recordsTotal'] = $dataTotal['val'];
		$result['recordsFiltered'] = $dataFiltered['val'];

		return $result;

	}


    /**
     * Методики, у которых прикреплено данное оборудование
     * @param $oborudId
     * @return array
     */
	public function getMethodListByOborudId($oborudId)
    {
        $methodsArray = [];

        $sql = $this->DB->Query("select * from ulab_methods_oborud where id_oborud = {$oborudId}");
        $interchangeableResponse = $this->DB->Query(
            "
            SELECT umo.*
            FROM ba_oborud_interchangeable boi
            LEFT JOIN ulab_methods_oborud umo ON umo.id_oborud = boi.oborud_id
            WHERE boi.inter_oborud_id = {$oborudId} AND umo.id IS NOT NULL
            GROUP BY umo.method_id
            "
        );

        $result = [];
        while ($row = $sql->Fetch()) {
//            $result[] = $this->get($row['method_id']);
            $methodsArray[] = $row['method_id'];
        }

        while($row = $interchangeableResponse->Fetch()) {
            $methodsArray[] = $row['method_id'];
        }

        $methodsArray = array_unique($methodsArray);

        foreach ($methodsArray as $value) {
            $result[] = $this->get($value);
        }

        return $result;
    }

    public function updateFacts($methodId, $data)
    {
        $this->DB->Query("UPDATE ulab_methods SET facts_select = '{$data}' WHERE id = '{$methodId}'");
    }


	public function setPriceList($urlFile)
	{
		$reader = new Xlsx();
		$file = $urlFile;
		$spreadsheet = $reader->load($file); // тут наш файл с таблицей Excel
		$worksheet = $spreadsheet->setActiveSheetIndex(0);
		$highestRow = $worksheet->getHighestRow();
//		$highestCol = $worksheet->getHighestColumn();
		$highestCol = 'I';

		$infoByTableReviews = $worksheet->rangeToArray("A2:$highestCol$highestRow", null, true, false, false);
		$count = 0;
		$countAll = 0;
		foreach ($infoByTableReviews as $method) {
			if (!empty($method[8]) || $method[8] == '0') {
				$id = $method[0];
				$data = [
					'price' => $method[8]
				];
				$this->DB->Update('ulab_methods', $data, "where id = {$id}");
				$count++;
			}
			$countAll++;
		}
		$responce['count'] = $count;
		$responce['countAll'] = $countAll;

		return $responce;
	}


	public function setMesList()
	{
//		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//			$this->handleFormSubmission($_POST);
//		}

		// Загружаем файл Excel
		$filePath = '/home/bitrix/www/ulab/HW.xlsx'; // Путь к вашему файлу Excel
		$reader = new Xlsx();
		$spreadsheet = $reader->load($filePath);

		// Получаем первый лист
		$worksheet = $spreadsheet->getActiveSheet();

		// Генерируем HTML-форму
		$html = $this->generateHtmlForm($worksheet);

		// Выводим HTML-форму
		return $html;
	}

	public function generateHtmlForm($worksheet) {
		$html = '';
		$highestRow = $worksheet->getHighestRow(); // Максимальная строка
		$highestColumn = $worksheet->getHighestColumn(); // Максимальная колонка

		foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false);
			$html .= '<tr>';
			foreach ($cellIterator as $columnIndex => $cell) {
				$cellAddress = $cell->getCoordinate();
				$cellValue = $cell->getValue();
				$abc = 'class="variable"';
				$value = $cellValue;
				$readonly = '';
				if ($cell->isFormula()) {
					$abc = 'class="isFormula"';
					$cellValue = str_replace('=', '', $cellValue);
					$cellValue = str_replace('$', '', $cellValue);
					$cellValue = str_replace('!', '', $cellValue);
					$value = '';
					$readonly = 'readonly';
				}

				if ($cellValue != '') {
					$html .= "<td><input type='text' {$abc} id='{$cellAddress}' name='{$cellAddress}' data-formula='{$cellValue}' value='{$value}' {$readonly}></td>";
				} else {
					$html .= "<td><input type='text' {$abc} id='{$cellAddress}' name='{$cellAddress}' data-formula='{$cellValue}' value='{$value}' {$readonly}></td>";
				}
			}
			$html .= '</tr>';
		}

		return $html;
	}

    /**
     * @param $umtrId
     * @param array $exceptUgtp - исключить госты
     * @param bool $withMeasuring - с измерением?
     * @return array
     */
    public function getMethodByUmtrId($umtrId, $exceptUgtp = [], $withMeasuring = false)
    {
        $result = [];

        if (empty($umtrId)) {
            return $result;
        }

        $where = "umtr.id = {$umtrId} ";

        if (!empty($exceptUgtp) && is_array($exceptUgtp)) {
            $strUgtp = implode(',', $exceptUgtp);
            $where .= "AND ugtp.id NOT IN ({$strUgtp}) ";
        }

        $methodSql = $this->DB->Query(
            "SELECT um.*, 
                            umtr.name_for_protocol, 
                            ugtp.measuring_sheet, ugtp.id ugtp_id, 
                            m.`NAME` material 
                        FROM `ulab_material_to_request` umtr 
                        LEFT JOIN `MATERIALS` m ON m.`ID` = umtr.material_id 
                        INNER JOIN `ulab_gost_to_probe` ugtp ON ugtp.material_to_request_id = umtr.id 
                        INNER JOIN `ulab_methods` as um ON um.id = ugtp.method_id 
                        WHERE {$where}"
        );

        while ($row = $methodSql->Fetch()) {
            // Получаем данные методик с сохранёнными данными листов измерений
            if ($withMeasuring && $row['measuring_sheet'] === null) {
                continue;
            }

            $row['measuring_sheet'] = json_decode($row['measuring_sheet'], true);

            $row['material'] = trim($row['material']);
            $row['name'] = trim($row['name']);
            $result[] = $row;
        }

        return $result;
    }

	public function setProbeListTest()
	{
		$reader = new Xlsx();
		$file = '/home/bitrix/www/rsr.xlsx';
		$spreadsheet = $reader->load($file); // тут наш файл с таблицей Excel
		$worksheet = $spreadsheet->setActiveSheetIndex(0);
		$highestRow = $worksheet->getHighestRow();
		$highestCol = $worksheet->getHighestColumn();
		$highestCol = 'I';

//		$infoByTableReviews = $worksheet->rangeToArray("A4:$highestCol$highestRow", null, true, false, false);
		$infoByTableReviews = $worksheet->rangeToArray("A1:D937", null, true, false, false);
		$count = 0;
		$countAll = 0;
		$data = [];
		foreach ($infoByTableReviews as $method) {
            if(!$method[0] || $count == 0){
                $count++;
                continue;
            }

			$data[] = [
					'cipher' => $method[0],
					'from' => $method[1],
					'to' => $method[2],
                    'name_for_protocol' => $method[3],
				];
		}

		$probe = $this->DB->Query("SELECT *
				FROM `ulab_material_to_request`
				WHERE id = 93616")->Fetch();

        foreach ($data as $key => $value)
        {
            $nameForProtocol = $value['cipher'] . " интервал {$value['from']} - {$value['to']}, длина пробы - {$value['name_for_protocol']}";

            $this->DB->Query("INSERT INTO ulab_material_to_request(deal_id, material_id, material_number, name_for_protocol) VALUES('{$probe['deal_id']}', '{$probe['material_id']}', '{$probe['material_number']}', '{$nameForProtocol}')");
            $currentProbe = $this->DB->Query("SELECT * FROM ulab_material_to_request WHERE deal_id = 15657 ORDER BY id DESC LIMIT 1")->Fetch();

            $ugtpArray = $this->DB->Query("SELECT * FROM ulab_gost_to_probe WHERE material_to_request_id = 93616");

            while ($ugtp = $ugtpArray->fetch()) {

                $this->DB->Query("INSERT INTO ulab_gost_to_probe(method_id, new_method_id, price, material_to_request_id, gost_number, assigned_id)
                VALUES('{$ugtp['method_id']}', '{$ugtp['new_method_id']}', '{$ugtp['price']}'
                , '{$currentProbe['id']}', '{$ugtp['gost_number']}', '{$ugtp['assigned_id']}')");
            }
        }

	}

    /**
     * Получить все уникальные методики, связанные с заданными пробами
     *
     * @param array $probeIdList
     * @return array
     */
    public function getMethodsByProbeList($probeIdList)
    {
        $probeIdsStr = implode(',', $probeIdList);

        $sql = "
            SELECT DISTINCT 
                m.id, 
                m.name,
                m.clause,
                g.reg_doc,
                g.year
            FROM ulab_gost_to_probe ugp
            INNER JOIN ulab_methods m ON ugp.method_id = m.id
            INNER JOIN ulab_gost g ON g.id = m.gost_id
            WHERE ugp.material_to_request_id IN ($probeIdsStr)
        ";

        $result = $this->DB->Query($sql);

        $methods = [];
        while ($row = $result->Fetch()) {
            $strYear = !empty($row['year']) ? "-{$row['year']}" : '';
            $strClause = !empty($row['clause']) ? " {$row['clause']}" : '';
            $row['name'] = "{$row['reg_doc']}{$strYear}{$strClause} | {$row['name']}";

            $methods[] = $row;
        }

        return $methods;
    }

    public function removeInter($id)
    {
        $this->DB->Query("DELETE FROM ba_oborud_interchangeable WHERE id = '{$id}'");
    }

//	public function handleFormSubmission($data)
//	{
//		$values = [];
//		foreach ($data as $item) {
//			$values[$item['name']] = $item['value'];
//		}
//
//		$output = '';
//		foreach ($values as $key => $value) {
//			$output .= "$key: $value<br>";
//		}
//
//		echo $output;
//	}
}
