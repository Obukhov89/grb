<?php

class Overhead extends Model
{
    /**
     * @param $filter
     * @return array
     */
    public function getJournal($filter)
    {
        global $DB;

        $where = "";
        $limit = "";
        $order = [
            'by' => 'oo.id',
            'dir' => 'DESC'
        ];
        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {

                if (isset($filter['search']['project_id'])) {
                    $where .= "oo.project_id = {$filter['search']['project_id']} AND ";
                }

                if (isset($filter['search']['date_start'])) {
                    $where .= "oo.date >= '{$filter['search']['date_start']}' AND ";
                }

                if (isset($filter['search']['date_end'])) {
                    $where .= "oo.date <= '{$filter['search']['date_end']}' AND ";
                }
            }

            // работа с сортировкой
            if (!empty($filter['order'])) {
                if ($filter['order']['dir'] === 'asc') {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'id':
                        $order['by'] = "oo.id";
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

        $data = $DB->Query(
            "SELECT oo.*, DATE_FORMAT(date, '%d.%m.%Y') AS date_ru, 
                    op.name AS project_name, op.code AS project_code  
             FROM osk_overhead AS oo
             LEFT JOIN osk_project AS op ON op.id = oo.project_id
             WHERE {$where} 
             GROUP BY oo.id 
             ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT oo.*, DATE_FORMAT(date, '%d.%m.%Y') AS date_ru, 
                    op.name AS project_name, op.code AS project_code  
             FROM osk_overhead AS oo
             LEFT JOIN osk_project AS op ON op.id = oo.project_id
             GROUP BY oo.id"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT oo.*, DATE_FORMAT(date, '%d.%m.%Y') AS date_ru, 
                    op.name AS project_name, op.code AS project_code  
             FROM osk_overhead AS oo
             LEFT JOIN osk_project AS op ON op.id = oo.project_id
             WHERE {$where} 
             GROUP BY oo.id "
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;
        //$result['test'] = $filter;

        return $result;
    }

    public function insertRow($data)
    {
        $sqlData = $this->prepearTableData('osk_overhead', $data);
        return $this->DB->Insert('osk_overhead', $sqlData);
    }

    public function updateRow($data, $id)
    {
        $where = "WHERE id = {$id}";
        $sqlData = $this->prepearTableData('osk_overhead', $data);
        return $this->DB->Update('osk_overhead', $sqlData, $where);
    }
}