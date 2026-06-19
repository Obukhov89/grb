<?php


/**
 * Класс неразрушающий контроль
 * Class Nk
 */
class Nk extends Model {
    /**
     * Получение данных листа измерений по градуировочной зависимости
     * @param $id
     * @return array
     */
    public function getGraduation($id)
    {
        $response = [];

        if (empty($id) || $id < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM ulab_graduation WHERE id = {$id}")->Fetch();

        if (!empty($result)) {
            $result['data_json'] = $result['data'];
            $result['data'] = json_decode($result['data'], true);
            $response = $result;
        }

        return $response;
    }

    /**
     * Получение данных листов измерений по градуировочной зависимости
     * @return array
     */
    public function getGraduationList()
    {
        $response = [];

        $result = $this->DB->Query("SELECT * FROM ulab_graduation");

        while ($row = $result->Fetch()) {
            $row['data_json'] = $row['data'];
            $row['data'] = json_decode($row['data'], true);
            $row['ru_date'] = !empty($row['date']) ? date('d.m.Y', strtotime($row['date'])) : '';
            $response[] = $row;
        }

        return $response;
    }

    /**
     * Сохранение данных листа измерений по градуировочной зависимости
     * @param $data
     * @return false|mixed|string
     */
    public function addGraduation($data)
    {
        $data['data'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        $data['date'] = $data['date'] ?: date('Y-m-d');

        $sqlData = $this->prepearTableData('ulab_graduation', $data);

        return $this->DB->Insert('ulab_graduation', $sqlData);
    }

    /**
     * Обновление данных  листа измерений по градуировочной зависимости
     * @param $id
     * @param $data
     * @return false|mixed|string
     */
    public function updateGraduation($id, $data)
    {
        $data['data'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        $data['date'] = $data['date'] ?: date('Y-m-d');

        $sqlData = $this->prepearTableData('ulab_graduation', $data);

        $where = "WHERE id = {$id}";
        return $this->DB->Update('ulab_graduation', $sqlData, $where);
    }

    /**
     * Получить данные для журнала листов градуировочной зависимости
     * @param array $filter
     * @return array
     */
    public function getGraduationJournal($filter = [])
    {
        /** @var Permission $permissionModel */
        $permissionModel = new Permission;

        $where = "";
        $limit = "";
        $order = [
            'by' => 'ug.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // Номер
                if (isset($filter['search']['id'])) {
                    $where .= "ug.id LIKE '%{$filter['search']['id']}%' AND ";
                }

                // дата
                if (isset($filter['search']['date'])) {
                    $where .= "LOCATE('{$filter['search']['date']}', DATE_FORMAT(ug.date, '%d.%m.%Y %H:%i:%s')) > 0 AND ";
                }
                if ( isset($filter['search']['dateStart']) ) {
                    $where .= "(ug.date >= '{$filter['search']['dateStart']}' AND ug.date <= '{$filter['search']['dateEnd']}') AND ";
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
                case 'id':
                    $order['by'] = 'ug.id';
                    break;
                case 'date':
                    $order['by'] = 'ug.date';
                    break;
                default:
                    $order['by'] = 'ug.id';
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
            "SELECT * 
                    FROM ulab_graduation ug 
                    WHERE {$where}
                    ORDER BY  {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT *
                    FROM ulab_graduation ug"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT distinct *
                    FROM ulab_graduation ug 
                    WHERE {$where}"
        )->SelectedRowsCount();

        // Проверка на доступ редактирования данных градуационной зависимости
        $permissionInfo = $permissionModel->getUserPermission($_SESSION['SESS_AUTH']['USER_ID']);
        $isCanEdit = in_array($permissionInfo['id'],  [ADMIN_PERMISSION, HEAD_IC_PERMISSION, LAB_PERMISSION]);

        while ($row = $data->Fetch()) {
            $row['ru_date'] = date('d.m.Y', strtotime($row['date']));
            $row['is_can_edit'] = $isCanEdit;

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    /**
     * @param string $path
     * @param string $fileName
     * @param string $img
     * @return array
     */
    public function saveChartImage(string $path, string $fileName, string $img): array
    {
        if (!is_dir($path)) {
            $mkdirResult = mkdir($path, 0766, true);

            if (!$mkdirResult) {
                return [
                    'success' => false,
                    'error' => [
                        'message' => "Ошибка! Не удалось создать папку. {$path}",
                    ]
                ];
            }
        }

        $file = $path."/".$fileName;
        list($type, $data) = explode(';', $img);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        if (!file_put_contents($file, $data)) {
            return [
                'success' => false,
                'error' => [
                    'message' => "Ошибка! Не удалось сохранить файл на сервер!",
                ]
            ];
        } else {
            return [
                'success' => true,
                'data' => $fileName
            ];
        }
    }
}