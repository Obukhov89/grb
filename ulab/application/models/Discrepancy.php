<?php

/**
 * related tables: osk_responsible, osk_discrepancy_journal
 */
class Discrepancy extends Model
{
    public function clearTableForImport()
    {
        $this->DB->Update("osk_discrepancy_journal", ['deleted_at' => '"' . date('Y-m-d H:i:s') . '"',], "WHERE id > 0");
    }

    public function createResponsible(string $fio)
    {
        $data = $this->prepearTableData("osk_responsible", ['fio' => $fio]);

        $this->DB->Insert("osk_responsible", $data);
    }


    public function getResponsible(): array
    {
        $query = "select * from osk_responsible order by fio asc;";
        $data = $this->DB->Query($query);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function createDiscrepancy(array $attributes)
    {

        $dataToCreate = [
            'created_at' => $attributes['created_at'],
            'responsible' => $attributes['responsible'],
            'summary' => $attributes['summary'],
            'fix_number' => empty($attributes['fix_number']) ? null : $attributes['fix_number'],
            'fix_date' => empty($attributes['fix_date']) ? null : $attributes['fix_date'],
            'remark_on_elimination' => $this->getRemark($attributes['fix_number'], $attributes['fix_date']),
        ];

        return $this->DB->Insert("osk_discrepancy_journal", $this->prepearTableData("osk_discrepancy_journal", $dataToCreate));
    }

    public function getRemark($fixNumber, $fixDate): string
    {
        if (empty($fixNumber) || empty($fixDate)) {
            return "";
        }

        return "Акт №" . $fixNumber . " от " . DateTime::createFromFormat('Y-m-d', $fixDate)->format('d.m.y');
    }

    public function editDiscrepancy(array $attributes)
    {
        $dataToCreate = [
            'responsible' => $attributes['responsible'],
            'summary' => $attributes['summary'],
            'fix_number' => empty($attributes['fix_number']) ? null : $attributes['fix_number'],
            'fix_date' => empty($attributes['fix_date']) ? null : $attributes['fix_date'],
            'remark_on_elimination' => $this->getRemark($attributes['fix_number'], $attributes['fix_date']),
        ];

        return $this->DB->Update("osk_discrepancy_journal", $this->prepearTableData("osk_discrepancy_journal", $dataToCreate), "where id={$attributes['row_id']}");
    }

    protected function collectWhereString(array $filter): string
    {
        $where = "WHERE ";

        // работа с фильтрами
        if (!empty($filter['search'])) {
            // created_at
            if (isset($filter['search']['created_at'])) {
                $date = DateTime::createFromFormat('d.m.Y', $filter['search']['created_at'])->format('Y-m-d');
                $where .= "created_at LIKE '%{$date}%' AND ";
            }
            // ответственный
            if (isset($filter['search']['responsible'])) {
                $where .= "responsible LIKE '%{$filter['search']['responsible']}%' AND ";
            }
            // краткое описание
            if (isset($filter['search']['summary'])) {
                $where .= "summary LIKE '%{$filter['search']['summary']}%' AND ";
            }
            // отметка об устранении
            if (isset($filter['search']['remark_on_elimination'])) {
                $where .= "remark_on_elimination LIKE '%{$filter['search']['remark_on_elimination']}%' AND ";
            }
        }

        return $where . " 1=1";
    }

    protected function collectLimitString(array $filter): string
    {
        $limit = "";
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

        return $limit;
    }

    protected function collectOrderByString(array $filter): string
    {
        $sortable = ['id', 'created_at', 'responsible', 'summary', 'remark_on_elimination'];
        $order = ['by' => 'id', 'dir' => 'DESC',];

        $orderBy = "ORDER BY id DESC";
        if (!empty($filter['order'])) {
            if (in_array($filter['order']['by'], $sortable)) {
                $order['by'] = $filter['order']['by'];
                $order['dir'] = $filter['order']['dir'];

                $orderBy = "ORDER BY {$order['by']} {$order['dir']}";
            }
        }

        return $orderBy;
    }

    public function getDataToJournal(array $filter): array
    {
        $limit = $this->collectLimitString($filter);
        $orderBy = $this->collectOrderByString($filter);
        $where = $this->collectWhereString($filter);
        $sql = "SELECT * FROM `osk_discrepancy_journal` $where and deleted_at is null {$orderBy} {$limit}";

        $data = $this->DB->Query($sql);
        if (!$data) {
            return [];
        }

        $result = ['data' => []];
        $iter = 1;
        while ($row = $data->Fetch()) {
            $row['row_number'] = $iter;
            $row['created_at'] = DateTime::createFromFormat('Y-m-d', $row['created_at'])->format('d.m.Y');
            $result['data'][] = $row;

            $iter++;
        }

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM osk_discrepancy_journal
                    WHERE deleted_at is null and 1=1;"
        )->Fetch();

        $dataFiltered = $this->DB->Query(
            "SELECT COUNT(*) AS val
                FROM (
                    SELECT * FROM `osk_discrepancy_journal` $where and deleted_at is null
                ) AS subquery;"
        )->Fetch();

        $result['recordsTotal'] = $dataTotal['val'];
        $result['recordsFiltered'] = $dataFiltered['val'];

        return $result;
    }

    public function deleteRow(int $id)
    {
        $this->DB->Update("osk_discrepancy_journal", [
            'deleted_at' => '"' . date('Y-m-d H:i:s') . '"',
        ], "WHERE id=$id");
    }

    public function getRow(int $id)
    {
        $query = "select * from osk_discrepancy_journal where 1=1 and id = $id and deleted_at is null;";

        return $this->DB->Query($query)->Fetch();
    }
}