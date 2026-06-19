<?php

class SchemeEditor extends Model
{
    public function createRow(string $workType, string $object)
    {
        $this->DB->Insert("osk_scheme_editor", ['work_type' => '"' . $workType . '"', 'object' => '"' . $object . '"', 'type' => 0]);
    }

    public function getDataToJournal(array $filter): array
    {
        $where = $this->collectWhereString($filter);
        $limit = $this->collectLimitString($filter);

        // todo: костылище, надо переделать
        $groupBy = "GROUP BY ose.id";
        if (!empty($filter['search'])) {
            if (isset($filter['search']['scheme_list'])) {
                $groupBy = "";
            }
        }

        $sql = "SELECT ose.*, os.name FROM osk_scheme_editor as ose
                LEFT JOIN osk_schemes AS os ON ose.id = os.work_type_id
                {$groupBy} {$where}
                AND `ose`.`deleted_at` IS NULL ORDER BY id DESC {$limit}";

        $data = $this->DB->Query($sql);
        if (!$data) {
            return [];
        }

        $schemeCard = new SchemeCard();

        $result = [];
        while ($row = $data->Fetch()) {

            $schemes = $schemeCard->getSchemes($row['id']);

            $result[] = [
                'work_type_id' => $row['id'],
                'work_type' => $row['work_type'],
                'object' => $row['object'],
                'scheme_list' => $schemes
            ];
        }

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM osk_scheme_editor
                    WHERE 1=1;"
        )->Fetch();

        $dataFiltered = $this->DB->Query(
            "SELECT COUNT(*) AS val
                FROM (
                    SELECT ose.*, os.name
                    FROM osk_scheme_editor AS ose
                    LEFT JOIN osk_schemes AS os ON ose.id = os.work_type_id
                    WHERE 1=1 AND ose.deleted_at IS NULL
                    GROUP BY ose.id
                ) AS subquery;"
        )->Fetch();

        $result['recordsTotal'] = $dataTotal['val'];
        $result['recordsFiltered'] = $dataFiltered['val'];

        return $result;
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

    protected function collectWhereString(array $filter): string
    {
        $where = "HAVING ";

        // работа с фильтрами
        if (!empty($filter['search'])) {
            // Тип работ
            if (isset($filter['search']['work_type'])) {
                $where .= "ose.work_type LIKE '%{$filter['search']['work_type']}%' AND ";
            }
            // Объект
            if (isset($filter['search']['object'])) {
                $where .= "ose.object LIKE '%{$filter['search']['object']}%' AND ";
            }
            // Объект
            if (isset($filter['search']['scheme_list'])) {
                $where .= "os.name LIKE '%{$filter['search']['scheme_list']}%' AND ";
            }
        }

        return $where . " 1=1";
    }

    public function editScheme(array $attrs)
    {
        $this->DB->Update("osk_scheme_editor", [
            'work_type' => '"' . $attrs['work_type'] . '"',
            'object' => '"' . $attrs['object'] . '"',
        ], "WHERE id = {$attrs['work_type_id']}");
    }

    public function delete(int $workTypeId)
    {
        $this->DB->Update("osk_scheme_editor", [
            'deleted_at' => '"' . date('Y-m-d H:i:s') . '"',
        ], "WHERE id=$workTypeId");
    }

    public function deleteScheme()
    {
//        $this->DB->Update("osk_scheme_editor", [
//            'deleted_at' => '"' . date('Y-m-d H:i:s') . '"',
//        ], "WHERE id=$workTypeId");
    }
}