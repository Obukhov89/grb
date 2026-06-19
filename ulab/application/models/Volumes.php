<?php

/**
 * Клас лаборатории
 * Class Lab
 */
class Volumes extends Model
{

    private const DB_TABLE = "osk_volumes";

    public function getVolume($filter)
    {
        $db_table = "osk_volumes";

        $where = "";
        $limit = "";
        $order = [
            'by' => 'id',
            'dir' => 'DESC'
        ];
        $groupBy = "";


        if (!empty($filter)) {
//            if (!empty($filter['search'])) {
//                if (isset($filter['search']['order_number'])) {
//                    $where .= "cf.order_number LIKE '%{$filter['search']['order_number']}%' AND ";
//                }
//                if (isset($filter['search']['date'])) {
//                    $where .= "cf.date LIKE '%{$filter['search']['date']}%' AND ";
//                }
//                if (isset($filter['search']['product_name'])) {
//                    $where .= "cf.product_name LIKE '%{$filter['search']['product_name']}%' AND ";
//                }
//                if (isset($filter['search']['customer'])) {
//                    $where .= "cf.customer LIKE '%{$filter['search']['customer']}%' AND ";
//                }
//                if (isset($filter['search']['drawing_number'])) {
//                    $where .= "cf.drawing_number LIKE '%{$filter['search']['drawing_number']}%' AND ";
//                }
        }

        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'asc';
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

        $where .= "1";

        $mainSql = $this->DB->Query("
            SELECT ov.*, uom.name as unitName
            FROM osk_volumes as ov
            LEFT JOIN unit_of_measurement as uom ON uom.id = ov.unit
            ORDER BY {$order['by']} {$order['dir']} {$limit}
        ");

        $dataTotal = $this->DB->Query(
            "SELECT * FROM osk_volumes as uv group by uv.id"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT * FROM osk_volumes as uv group by uv.id"
        )->SelectedRowsCount();

        $result = [];

        while ($row = $mainSql->fetch()) {
            $result[] = $row;
            $result['recordsTotal'] = $dataTotal;
            $result['recordsFiltered'] = $dataFiltered;
        }

        return $result;
    }

    public function create()
    {
        $mainSql = $this->DB->Query("
        SELECT * FROM unit_of_measurement
        ");

        while ($unit = $mainSql->fetch()) {
            $result[] = $unit;
        }
        return [
            'units' => $result
        ];
    }

    public function store($request)
    {
        $request = $this->prepareData($request);
        return $this->DB->Insert("osk_volumes", $request);
    }

    public function edit($id)
    {
        $sql = $this->DB->Query("
        SELECT
        osk_volumes.*,
        unit_of_measurement.name as unitName,
        unit_of_measurement.id as unitId
        FROM osk_volumes 
        LEFT JOIN unit_of_measurement ON unit_of_measurement.id = osk_volumes.unit
        WHERE osk_volumes.id = {$id}
        ");

        return $sql->fetch();
    }

    public function update($request, $id)
    {
        $request = $this->prepareData($request);
        $this->DB->Update("osk_volumes", $request, "WHERE id = {$id}");
    }

    public function units()
    {
        $sql = $this->DB->Query("
        SELECT * FROM unit_of_measurement
        ");

        while ($unit = $sql->fetch()) {
            $result[] = $unit;
        }

        return $result;
    }

    public function getDateWorkJournal()
    {
        $result = [];
        $workJournalData = $this->DB->Query("
            SELECT DATE_FORMAT(inspection_date, '%Y-%m') AS month,
            MONTHNAME(month) AS month_name,
            GROUP_CONCAT(CONCAT(MONTHNAME(inspection_date), ' - ', SUBSTRING(YEAR(inspection_date), 3, 2)) ORDER BY inspection_date SEPARATOR ',') AS dates
            FROM (
                SELECT DATE_FORMAT(inspection_date, '%Y-%m') AS month,
                       MIN(inspection_date) AS inspection_date
                FROM osk_diary_journal
                GROUP BY DATE_FORMAT(inspection_date, '%Y-%m')
            ) AS subquery
            ORDER BY month
            "
        );

        while ($workJournal = $workJournalData->fetch()) {
            $result[] = $workJournal;
        }

        return $result;
    }

    public function getWorkJournal()
    {
        $result = [];
        $workJournalData = $this->DB->Query("
            SELECT 
            dj.project,
            dj.work_type,
            dj.inspection_date,
            dj.constructive_name,
            SUM(dj.volume) AS total_volume,
            CONCAT(MONTHNAME(dj.inspection_date), ' - ', SUBSTRING(YEAR(dj.inspection_date), 3, 2)) AS formatted_date
            FROM osk_diary_journal dj
            JOIN osk_volumes v ON dj.constructive_name = v.structureName
             AND dj.project = v.sectionNumber
            AND dj.work_type = v.workType
            GROUP BY dj.project
            ORDER BY dj.project
            "
        );
        while ($workJournal = $workJournalData->fetch()) {
            $result[] = $workJournal;
        }

        return $result;
    }
}