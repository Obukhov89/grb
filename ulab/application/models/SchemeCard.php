<?php

class SchemeCard extends Model
{
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

    public function getSchemes(int $rowId): array
    {
        $sql = "SELECT * FROM osk_schemes WHERE work_type_id = {$rowId};";
        $schemes = $this->DB->Query($sql);

        $result = [];
        while ($row = $schemes->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function createScheme(array $attrs)
    {
        $this->DB->Insert("osk_schemes", [
            'name' => '"' . $attrs['name'] . '"',
            'work_type_id' => '"' . $attrs['work_type_id'] . '"',
        ]);
    }

    public function getSchemeCardData(int $cardId)
    {
        $sql = "SELECT os.*, ose.work_type FROM `osk_schemes` AS os  
                JOIN osk_scheme_editor AS ose ON ose.id = os.work_type_id
                WHERE `os`.`id` = {$cardId};";
        $scheme = $this->DB->Query($sql)->Fetch();

        if (!$scheme)
            return null;

        return $scheme;
    }

    public function createID(string $name)
    {
        $this->DB->Insert("osk_id_type", [
            'name' => '"' . $name . '"',
        ]);
    }

    public function getIDTypes(): array
    {
        $sql = "SELECT * FROM osk_id_type;";
        $types = $this->DB->Query($sql);

        $result = [];
        while ($row = $types->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function createIDType(array $attributes)
    {
        $this->DB->Insert("osk_isp_doc_types", [
            'type_id' => '"' . $attributes['type_id'] . '"',
            'card_id' => '"' . $attributes['card_id'] . '"',
        ]);
    }

    public function getAllSchemeCardData(int $cardId, array $filter): array
    {
        $limit = $this->collectLimitString($filter);
        $sql = "SELECT oidt.*, oit.name
                FROM `osk_isp_doc_types` as oidt
                join osk_id_type as oit on oit.id=oidt.type_id
                where oidt.card_id = {$cardId} AND `deleted_at` IS NULL
                order by oidt.id desc {$limit}";

        $types = $this->DB->Query($sql);

        $result = [];
        while ($row = $types->Fetch()) {
            $result[] = [
                'name' => $row['name'],
                'scheme_type_id' => $row['id'],
                'card_id' => $row['card_id'],
                'type_id' => $row['type_id'],
            ];
        }

//        $dataTotal = $this->DB->Query(
//            "SELECT count(*) val
//                    FROM osk_scheme_editor
//                    WHERE 1=1;"
//        )->Fetch();

        $dataFiltered = $this->DB->Query(
            "select count(*) as val from (SELECT oidt.*, oit.name
                FROM `osk_isp_doc_types` as oidt
                join osk_id_type as oit on oit.id=oidt.type_id
                where oidt.card_id = {$cardId} AND `deleted_at` IS NULL
                order by oidt.id desc) as subquery;"
        )->Fetch();

        $result['recordsTotal'] = $dataFiltered['val'];
        $result['recordsFiltered'] = $dataFiltered['val'];

        return $result;
    }

    public function deleteIDType(int $schemeTypeId)
    {
        $this->DB->Update("osk_isp_doc_types", [
            'deleted_at' => '"' . date('Y-m-d H:i:s') . '"',
        ], "WHERE id=$schemeTypeId");
    }

    public function editIDType(array $attrs)
    {
        $this->DB->Update("osk_id_type", [
            'name' => '"' . $attrs['name'] . '"',
        ], "WHERE id={$attrs['type_id']}");
    }

    public function deleteScheme(array $attrs)
    {
        $this->DB->Update("osk_schemes", [
            'work_type_id' => '"' . -1 . '"',
            'deleted_at' => '"' . date('Y-m-d H:i:s') . '"',
        ], "WHERE id={$attrs['scheme_id']}");
    }
}