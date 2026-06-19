<?php


class Contractor extends Model
{
    public function getJournal($filter)
    {
        global $DB;

        $where = "";
        $limit = "";
        $order = [
            'by' => 'c.id',
            'dir' => 'DESC'
        ];
        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {

                if (isset($filter['search']['id'])) {
                    $where .= "c.id LIKE '%{$filter['search']['id']}%' AND ";
                }
                if (isset($filter['search']['weather'])) {
                    $where .= "c.weather LIKE '%{$filter['search']['weather']}%' AND ";
                }

                if (isset($filter['search']['content'])) {
                    $where .= "c.content LIKE '%{$filter['search']['content']}%' AND ";
                }

                if (isset($filter['search']['area_number'])) {
                    $where .= "c.area_number LIKE '%{$filter['search']['area_number']}%' AND ";
                }

                if (isset($filter['search']['job_desc'])) {
                    $where .= "c.job_desc LIKE '%{$filter['search']['job_desc']}%' AND ";
                }

                if (isset($filter['search']['datetime'])) {
                    $where .= "c.datetime LIKE '%{$filter['search']['datetime']}%' AND ";
                }

                if (isset($filter['search']['work_place'])) {
                    $where .= "c.work_place LIKE '%{$filter['search']['work_place']}%' AND ";
                }
                if (isset($filter['search']['constructive'])) {
                    $where .= "c.constructive LIKE '%{$filter['search']['constructive']}%' AND ";
                }
                if (isset($filter['search']['work_object'])) {
                    $where .= "c.work_object LIKE '%{$filter['search']['work_object']}%' AND ";
                }
                if (isset($filter['search']['assigned_completed'])) {
                    $where .= "c.assigned_completed LIKE '%{$filter['search']['assigned_completed']}%' AND ";
                }
                if (isset($filter['search']['checklist'])) {
                    $where .= "c.checklist LIKE '%{$filter['search']['checklist']}%' AND ";
                }
                if (isset($filter['search']['comment'])) {
                    $where .= "c.comment LIKE '%{$filter['search']['comment']}%' AND ";
                }
                if (isset($filter['search']['aok'])) {
                    if ($filter['search']['aok'] == "Да") $filter['search']['aok'] = 1;
                    if ($filter['search']['aok'] == "Нет") $filter['search']['aok'] = 0;
                    $where .= "c.aok LIKE '%{$filter['search']['aok']}%' AND ";
                }
                if (isset($filter['search']['act'])) {
                    $where .= "c.act LIKE '%{$filter['search']['act']}%' AND ";
                }
                if (isset($filter['search']['result'])) {
                    $where .= "c.result LIKE '%{$filter['search']['result']}%' AND ";
                }

                if (isset($filter['search']['company_name'])) {
                    $where .= "u.company_name LIKE '%{$filter['search']['company_name']}%' AND ";
                }
                if (isset($filter['search']['phone'])) {
                    $where .= "u.phone LIKE '%{$filter['search']['phone']}%' AND ";
                }
                if (isset($filter['search']['fio'])) {
                    $where .= "u.fio LIKE '%{$filter['search']['fio']}%' AND ";
                }
            }

            // работа с сортировкой
            if (!empty($filter['order'])) {
                if ($filter['order']['dir'] === 'asc') {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'id':
                        $order['by'] = "c.id";
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

        $userId = $_SESSION['SESS_AUTH']['USER_ID'];

        $data = $DB->Query(
            "SELECT c.*, DATE_FORMAT(created_at, '%m') AS month_number, u.phone, u.fio, u.company_name, u.id as user_id  
             FROM tg_contractor AS c 
             LEFT JOIN tg_user AS u ON u.tg_id = c.tg_id
             WHERE {$where} 
             GROUP BY c.id 
             ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT c.id
             FROM tg_contractor AS c 
             LEFT JOIN tg_user AS u ON u.tg_id = c.tg_id
             GROUP BY c.id"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT c.id
             FROM tg_contractor AS c 
             LEFT JOIN tg_user AS u ON u.tg_id = c.tg_id
             WHERE {$where} 
             GROUP BY c.id"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    public function getById($id)
    {
        global $DB;

        $stmt = $DB->Query(
            "SELECT c.*, cs.name AS status_name, u.phone, u.fio, u.company_name, CONCAT(monthly_order_number, '/', DATE_FORMAT(created_at, '%m')) AS order_number 
             FROM tg_contractor AS c 
             LEFT JOIN tg_contractor_status AS cs ON cs.id = c.status
             LEFT JOIN tg_user AS u ON u.tg_id = c.tg_id
             WHERE c.id = {$id}"
        );

        return $stmt->Fetch();
    }

    public function updateRow($data, $id)
    {
        $updateData = [];

        foreach ($data as $param => $value) {
            $updateData[$param] = $this->quoteStr($this->DB->ForSql(trim($value)));
        }

        $this->DB->Update('tg_contractor', $updateData, "WHERE id = {$id}");
    }

    public function insertRow($data)
    {
        $updateData = [];

        foreach ($data as $param => $value) {
            $updateData[$param] = $this->quoteStr($this->DB->ForSql(trim($value)));
        }

        $this->DB->Insert('tg_contractor', $updateData);
    }

    public function getCountMonth()
    {
        $year = date("Y");
        $month = date("m");
        $sql = "SELECT id FROM tg_contractor 
                WHERE DATE_FORMAT(created_at, '%Y') = {$year} AND DATE_FORMAT(created_at, '%m') = {$month}";
        return $this->DB->Query($sql)->SelectedRowsCount();
    }

    public function getUserByFilter($filter = "")
    {
        $result = [];
        $where = 1;

        if (!empty($filter)) {
            $where = $filter;
        }

        $sql = "SELECT tg_id FROM tg_user WHERE {$where}";

        $stmt = $this->DB->Query($sql);

        while ($row = $stmt->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getUser($id)
    {
        global $DB;

        $stmt = $DB->Query(
            "SELECT *
             FROM tg_user
             WHERE id = {$id}"
        );

        return $stmt->Fetch();
    }

    public function getTgUserList()
    {
        global $DB;

        $sql = "SELECT * FROM tg_user";

        $result = [];
        $stmt = $DB->Query($sql);

        while ($row = $stmt->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function updateUser($data, $id)
    {
        $updateData = [];

        foreach ($data as $param => $value) {
            $updateData[$param] = $this->quoteStr($this->DB->ForSql(trim($value)));
        }

        //var_dump($updateData);
        $this->DB->Update('tg_user', $updateData, "WHERE id = {$id}");
    }

    // Получить результат (список)
    public function getContractorResult()
    {
        global $DB;

        $sql = "SELECT * FROM tg_contractor_result";

        $result = [];
        $stmt = $DB->Query($sql);

        while ($row = $stmt->Fetch()) {
            $result[$row["id"]] = $row;
        }

        return $result;
    }
}