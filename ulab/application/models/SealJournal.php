<?php

class SealJournal extends model
{
    private array $sortFields = [
        "date_field",
        "coordinates",
        "remark",
        "protocol",
    ];
    public function getDatesFromOSKDiary(): array
    {
        $sql = "SELECT DISTINCT inspection_date FROM `osk_diary_journal` ORDER BY inspection_date DESC;";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($row = $data->Fetch()) {
            $inspectionDate = DateTime::createFromFormat('Y-m-d', $row['inspection_date'])->format('d.m.y');

            $result[] = [
//                'id' => $row['id'],
                'inspection_date' => $inspectionDate
            ];
        }

        return $result;
    }
    public function getCoordinatesFromOSKDiary(): array
    {
        $sql = "SELECT DISTINCT coordinates FROM `osk_diary_journal` ORDER BY coordinates DESC;";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = [
//                'id' => $row['id'],
                'coordinates' => $row['coordinates']
            ];
        }

        return $result;
    }

    public function getFilePath($files): ?string
    {
        $image = $files['photo'];
        if (!$image) {
            return null;
        }

        if ($image['size'] == 0) {
            return null;
        }

        $File = new File();

        $name = uniqid() . "_" . htmlspecialchars($image["name"]);
        $tmpName = $image["tmp_name"];
        $uploadPath = "/seal_journal/";

        $File->uploadFileServer(
            $name,
            $tmpName,
            $uploadPath
        );

        return "/ulab/upload" . $uploadPath . $name;
    }

    public function create(array $attributes, $files)
    {
        $filePath = $this->getFilePath($files);

        $dataToCreate = [
            'date_field' => $attributes['date_field'],
            'file_path' => $filePath,
            'coordinates' => $attributes['coordinates'],
            'remark' => $attributes['remark'],
            'protocol' => $attributes['protocol'],
        ];

        $this->DB->Insert("osk_seal_journal", $this->prepearTableData("osk_seal_journal", $dataToCreate));
    }


    public function edit(array $attributes, $files)
    {
        $rowId = $attributes['row_id'];

        $dataToUpdate = [
            'date_field' => $attributes['date_field'],
            'coordinates' => $attributes['coordinates'],
            'remark' => $attributes['remark'],
            'protocol' => $attributes['protocol'],
        ];

        if ($files['photo']['size']) {
            $dataToUpdate['file_path'] = $this->getFilePath($files);
        }

        $this->DB->Update("osk_seal_journal",
            $this->prepearTableData("osk_seal_journal", $dataToUpdate),
            "where id = {$rowId}"
        );
    }

    protected function collectOrderByString(array $filter): string
    {
        $order = ['by' => 'id', 'dir' => 'DESC',];

        $orderBy = "ORDER BY id DESC";
        if (!empty($filter['order'])) {
            if (in_array($filter['order']['by'], $this->sortFields)) {
                $order['by'] = $filter['order']['by'];
                $order['dir'] = $filter['order']['dir'];

                $orderBy = "ORDER BY {$order['by']} {$order['dir']}";
            }
        }

        return $orderBy;
    }

    protected function collectWhereString(array $filter): string
    {
        $where = " ";
//        var_dump($filter['search']);

        // работа с фильтрами
        if (!empty($filter['search'])) {
            //
            if (isset($filter['search']['date_field'])) {
                $where .= "date_field LIKE '%{$filter['search']['date_field']}%' AND ";
            }
            //
            if (isset($filter['search']['coordinates'])) {
                $where .= "coordinates LIKE '%{$filter['search']['coordinates']}%' AND ";
            }
            if (isset($filter['search']['remark'])) {
                $where .= "remark LIKE '%{$filter['search']['remark']}%' AND ";
            }
            if (isset($filter['search']['protocol'])) {
                $where .= "protocol LIKE '%{$filter['search']['protocol']}%' AND ";
            }
        }

        return $where . "1=1";
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

    public function getDataToJournal(array $filter): array
    {
        $limit = $this->collectLimitString($filter);
        $order = $this->collectOrderByString($filter);
        $where = $this->collectWhereString($filter);
        $sql = "select * from osk_seal_journal where deleted_at is null and {$where} {$order} {$limit}";
        $data = $this->DB->Query($sql);

        $result = [];
        $iter = 1;
        while ($row = $data->Fetch()) {
            $row['row_number'] = $iter;
            $result[] = $row;

            $iter++;
        }

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM osk_seal_journal
                    WHERE deleted_at is null and 1=1;"
        )->Fetch();

        $dataFiltered = $this->DB->Query(
            "SELECT count(*) val
            FROM `osk_seal_journal`
            where deleted_at is null and {$where};"
        )->Fetch();

        return [
            'data' => $result,
            'recordsTotal' => $dataTotal['val'],
            'recordsFiltered' => $dataFiltered['val'],
        ];
    }

    public function delete(int $id)
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        return $this->DB->Update("osk_seal_journal", $this->prepearTableData('osk_seal_journal', $data), "WHERE id = $id");
    }

    public function getJournalById(int $id)
    {
        $sql = "select * from osk_seal_journal where id = {$id}";

        return $this->DB->Query($sql)->Fetch();
    }
}