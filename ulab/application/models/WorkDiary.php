<?php

/**
 * related tables:
 * osk_diary_contractor_handbook - подрядчики (справочник)
 * osk_diary_kind_of_work_handbook - тип работы (справочник)
 * osk_diary_responsible_for_acceptance_handbook - Ответственный ФИО по приемке (справочник)
 */
class WorkDiary extends Model
{
    private array $sortFields = [
        "inspection_date",
        "contractor_name",
        "sectionNumber",
        "workType",
        "structureName",
        "kind_of_work_name",
        "coordinates",
        "acceptance_results",
        "precepts",
        "сontent_of_remarks",
        "technical_regulations",
        "elimination_date",
        "elimination_remark",
        "note",
        "volume",
        "responsible_for_acceptance_name",
        "order_number",
    ];

    protected function collectOrderByString(array $filter): string
    {
        $order = ['by' => 'odj.ID', 'dir' => 'DESC',];

        $orderBy = "ORDER BY odj.ID DESC";
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
            // № заявки
            if (isset($filter['search']['order_number'])) {
                $where .= "concat(monthly_order_number, '/', DATE_FORMAT(odj.created_at, '%m')) LIKE '%{$filter['search']['order_number']}%' AND ";
            }
            //
            if (isset($filter['search']['inspection_date'])) {
                $where .= "odj.inspection_date LIKE '%{$filter['search']['inspection_date']}%' AND ";
            }
            //
            if (isset($filter['search']['contractor_name'])) {
                $where .= "contractor_handbook.contractor_name LIKE '%{$filter['search']['contractor_name']}%' AND ";
            }
            if (isset($filter['search']['sectionNumber'])) {
                $where .= "volumes.sectionNumber LIKE '%{$filter['search']['sectionNumber']}%' AND ";
            }
            if (isset($filter['search']['workType'])) {
                $where .= "volumes.workType LIKE '%{$filter['search']['workType']}%' AND ";
            }
            if (isset($filter['search']['structureName'])) {
                $where .= "volumes.structureName LIKE '%{$filter['search']['structureName']}%' AND ";
            }
            if (isset($filter['search']['coordinates'])) {
                $where .= "odj.coordinates LIKE '%{$filter['search']['coordinates']}%' AND ";
            }
            if (isset($filter['search']['coordinates'])) {
                $where .= "odj.coordinates LIKE '%{$filter['search']['coordinates']}%' AND ";
            }
            if (isset($filter['search']['kind_of_work_name'])) {
                $where .= "kind_of_work_handbook.name LIKE '%{$filter['search']['kind_of_work_name']}%' AND ";
            }
            if (isset($filter['search']['acceptance_results'])) {
                $results = $filter['search']['acceptance_results'];
                if ($results == "2") {
                    $where .= "odj.acceptance_results = '0' AND ";
                } else {
                    $where .= "odj.acceptance_results = '1' AND ";
                }
            }
            if (isset($filter['search']['precepts'])) {
                $where .= "odj.precepts LIKE '%{$filter['search']['precepts']}%' AND ";
            }
            if (isset($filter['search']['сontent_of_remarks'])) {
                $where .= "odj.сontent_of_remarks LIKE '%{$filter['search']['сontent_of_remarks']}%' AND ";
            }
            if (isset($filter['search']['technical_regulations'])) {
                $where .= "odj.technical_regulations LIKE '%{$filter['search']['technical_regulations']}%' AND ";
            }
            if (isset($filter['search']['elimination_date'])) {
                $where .= "odj.elimination_date LIKE '%{$filter['search']['elimination_date']}%' AND ";
            }
            if (isset($filter['search']['elimination_remark'])) {
                $elimination_remark = $filter['search']['elimination_remark'];
                if ($elimination_remark == "2") {
                    $where .= "odj.elimination_remark = '1' AND ";
                } else {
                    $where .= "odj.elimination_remark = '0' AND ";
                }
            }
            if (isset($filter['search']['note'])) {
                $where .= "odj.note LIKE '%{$filter['search']['note']}%' AND ";
            }
            if (isset($filter['search']['volume'])) {
                $where .= "odj.volume LIKE '%{$filter['search']['volume']}%' AND ";
            }
            if (isset($filter['search']['responsible_for_acceptance_name'])) {
                $where .= "responsible.name LIKE '%{$filter['search']['responsible_for_acceptance_name']}%' AND ";
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

    public function getCountMonth()
    {
        $year = date("Y");
        $month = date("m");
        $sql = "SELECT id FROM osk_diary_journal 
                WHERE DATE_FORMAT(created_at, '%Y') = {$year} AND DATE_FORMAT(created_at, '%m') = {$month}";

        return $this->DB->Query($sql)->SelectedRowsCount();
    }

    public function getVolumesId(string $project, string $workType, string $constructiveName)
    {
        $sql = "select * from osk_volumes where sectionNumber = '{$project}' and workType = '{$workType}' and structureName = '{$constructiveName}';";
        $data = $this->DB->Query($sql)->Fetch();

        return $data['id'];
    }

    public function create(array $attributes, $files)
    {
        // filter_var($schemaInfo['checkbox'], FILTER_VALIDATE_BOOLEAN)
        $filePath = $this->getFilePath($files);
        $volumesId = $this->getVolumesId($attributes['project'], $attributes['work_type'], $attributes['constructive_name']);

        $dataToUpdate = [
            "inspection_date" => $attributes['inspection_date'],
            "contractor_id" => $attributes['сontractor'],
            "project" => $attributes['project'],
            "work_type" => $attributes['work_type'],
            "constructive_name" => $attributes['constructive_name'],
            "kind_of_work_id" => $attributes['kind_of_work'],
            "coordinates" => $attributes['coordinates'],
            "acceptance_results" => $attributes['acceptance_results'],
            "precepts" => $attributes['precepts'],
            "сontent_of_remarks" => $attributes['сontent_of_remarks'],
            "file_path" => $filePath,
            "technical_regulations" => $attributes['technical_regulations'],
            "elimination_date" => $attributes['elimination_date'],
            "elimination_remark" => (bool)$attributes['elimination_remark'],
            "note" => $attributes['note'],
            "volume" => $attributes['volume'],
            "responsible_for_acceptance_id" => $attributes['responsible_for_acceptance'],
            "created_at" => date('Y-m-d'),
            "monthly_order_number" => $this->getCountMonth() + 1,
            "volumes_id" => $volumesId,
        ];

        $this->DB->Insert("osk_diary_journal", $this->prepearTableData("osk_diary_journal", $dataToUpdate));
    }

    public function edit(array $attributes, $files)
    {
        $rowId = $attributes['row_id'];
        $volumesId = $this->getVolumesId($attributes['project'], $attributes['work_type'], $attributes['constructive_name']);

        $dataToUpdate = [
            "inspection_date" => $attributes['inspection_date'],
            "contractor_id" => $attributes['сontractor'],
            "project" => $attributes['project'],
            "work_type" => $attributes['work_type'],
            "constructive_name" => $attributes['constructive_name'],
            "kind_of_work_id" => $attributes['kind_of_work'],
            "coordinates" => $attributes['coordinates'],
            "acceptance_results" => $attributes['acceptance_results'],
            "precepts" => $attributes['precepts'],
            "сontent_of_remarks" => $attributes['сontent_of_remarks'],
            "technical_regulations" => $attributes['technical_regulations'],
            "elimination_date" => $attributes['elimination_date'],
            "elimination_remark" => (bool)$attributes['elimination_remark'],
            "note" => $attributes['note'],
            "volume" => $attributes['volume'],
            "responsible_for_acceptance_id" => $attributes['responsible_for_acceptance'],
            "volumes_id" => $volumesId,
        ];

        if ($files['photo']['size']) {
            $dataToUpdate['file_path'] = $this->getFilePath($files);
        }

        $this->DB->Update("osk_diary_journal",
            $this->prepearTableData("osk_diary_journal", $dataToUpdate),
            "where id = {$rowId}"
        );
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
        $uploadPath = "/work_diary/";

        $File->uploadFileServer(
            $name,
            $tmpName,
            $uploadPath
        );

        return "/ulab/upload" . $uploadPath . $name;
    }

    public function getContractorHandbook($filter=[]): array
    {
        $limit = $this->collectLimitString($filter);
        $sql = "SELECT * FROM `osk_diary_contractor_handbook` ORDER BY `contractor_name` ASC {$limit}";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM `osk_diary_contractor_handbook`"
        )->Fetch();

        $dataFiltered = $this->DB->Query(
            "SELECT COUNT(*) AS val
                FROM (
                    SELECT *
                    FROM osk_diary_contractor_handbook
                    WHERE 1=1
                ) AS subquery;"
        )->Fetch();

        return [
            'data' => $result,
            'total' => $dataTotal['val'],
            'filtered' => $dataFiltered['val'],
        ];
    }

    public function getDataToJournal(array $filter): array
    {
        $limit = $this->collectLimitString($filter);
        $order = $this->collectOrderByString($filter);
        $where = $this->collectWhereString($filter);
        $sql = "SELECT 
                odj.id, 
                odj.inspection_date,
                contractor_handbook.contractor_name,
                volumes.sectionNumber,
                volumes.workType,
                volumes.structureName,
                DATE_FORMAT(odj.created_at, '%m') AS month_number,
                kind_of_work_handbook.name as kind_of_work_name,
                odj.coordinates,
                odj.acceptance_results,
                odj.precepts,
                odj.сontent_of_remarks,
                odj.file_path,
                odj.technical_regulations,
                odj.elimination_date,
                odj.elimination_remark,
                odj.note,
                odj.volume,
                odj.monthly_order_number,
                responsible.name as responsible_for_acceptance_name,
                concat(monthly_order_number, '/', DATE_FORMAT(odj.created_at, '%m')) as order_number
                FROM `osk_diary_journal` as odj
                join `osk_diary_contractor_handbook` as contractor_handbook on odj.contractor_id = contractor_handbook.id
                join `osk_volumes` as volumes on volumes.id = odj.volumes_id
                join `osk_diary_kind_of_work_handbook` as kind_of_work_handbook on odj.kind_of_work_id = kind_of_work_handbook.id
                join `osk_diary_responsible_for_acceptance_handbook` as responsible on responsible.id = odj.responsible_for_acceptance_id
                where odj.deleted_at is null and {$where} {$order} {$limit};";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($row = $data->Fetch()) {
//            $row['order_number'] = $row['monthly_order_number'] . '/' .  $row['month_number'];
            $result[] = $row;
        }

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM osk_diary_journal
                    WHERE deleted_at is null and 1=1;"
        )->Fetch();

        $dataFiltered = $this->DB->Query(
            "SELECT count(*) val
            FROM `osk_diary_journal` as odj
            join `osk_diary_contractor_handbook` as contractor_handbook on odj.contractor_id = contractor_handbook.id
            join `osk_volumes` as volumes on volumes.id = odj.volumes_id
            join `osk_diary_kind_of_work_handbook` as kind_of_work_handbook on odj.kind_of_work_id = kind_of_work_handbook.id
            join `osk_diary_responsible_for_acceptance_handbook` as responsible on responsible.id = odj.responsible_for_acceptance_id
            where odj.deleted_at is null and {$where};"
        )->Fetch();

        return [
            'data' => $result,
            'recordsTotal' => $dataTotal['val'],
            'recordsFiltered' => $dataFiltered['val'],
        ];
    }

    public function getJournalById(int $id)
    {
        $sql = "SELECT 
                odj.id, 
                odj.inspection_date,
                contractor_handbook.contractor_name,
                contractor_handbook.id as contractor_id,
                volumes.sectionNumber,
                volumes.workType,
                volumes.structureName,
                DATE_FORMAT(odj.created_at, '%m') AS month_number,
                kind_of_work_handbook.name as kind_of_work_name,
                kind_of_work_handbook.id as kind_of_work_id,
                odj.coordinates,
                odj.acceptance_results,
                odj.precepts,
                odj.сontent_of_remarks,
                odj.file_path,
                odj.technical_regulations,
                odj.elimination_date,
                odj.elimination_remark,
                odj.note,
                odj.volume,
                odj.monthly_order_number,
                responsible.name as responsible_for_acceptance_name,
                responsible.id as responsible_for_acceptance_id
                FROM `osk_diary_journal` as odj
                join `osk_diary_contractor_handbook` as contractor_handbook on odj.contractor_id = contractor_handbook.id
                join `osk_volumes` as volumes on volumes.id = odj.volumes_id
                join `osk_diary_kind_of_work_handbook` as kind_of_work_handbook on odj.kind_of_work_id = kind_of_work_handbook.id
                join `osk_diary_responsible_for_acceptance_handbook` as responsible on responsible.id = odj.responsible_for_acceptance_id
                where odj.deleted_at is null and odj.id = $id;";
        $data = $this->DB->Query($sql);
        $row = $data->Fetch();
        $row['order_number'] = $row['monthly_order_number'] . '/' .  $row['month_number'];

        $result = [];
        $result['journal'] = $row;
        $result['contractors_handbook'] = $this->getContractorHandbook()['data'];
        $result['kind_of_work_handbook'] = $this->getKindOfWorkHandbook()['data'];
        $result['responsible_for_acceptance_handbook'] = $this->getResponsibleForAcceptanceHandbook()['data'];
        $result['project_data'] = $this->getProjectData();
        $result['work_type'] = $this->getWorkType($row['sectionNumber']);
        $result['constructive_name'] = $this->getConstructiveName($row['sectionNumber'], $row['workType']);

        return $result;
    }

    public function delete(int $id)
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        return $this->DB->Update("osk_diary_journal", $this->prepearTableData('osk_diary_journal', $data), "WHERE id = $id");
    }

    public function getKindOfWorkHandbook($filter=[]): array
    {
        $limit = $this->collectLimitString($filter);
        $sql = "SELECT * FROM `osk_diary_kind_of_work_handbook` ORDER BY `name` ASC {$limit}";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM `osk_diary_kind_of_work_handbook` WHERE 1=1;"
        )->Fetch();

        $dataFiltered = $this->DB->Query(
            "SELECT COUNT(*) AS val
                FROM (
                    SELECT *
                    FROM osk_diary_kind_of_work_handbook
                    WHERE 1=1
                ) AS subquery;"
        )->Fetch();


        return [
            'data' => $result,
            'total' => $dataTotal['val'],
            'filtered' => $dataFiltered['val'],
        ];
    }

    public function getResponsibleForAcceptanceHandbook($filter=[]): array
    {
        $limit = $this->collectLimitString($filter);
        $sql = "SELECT * FROM `osk_diary_responsible_for_acceptance_handbook` ORDER BY `name` ASC {$limit}";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM `osk_diary_responsible_for_acceptance_handbook` WHERE 1=1;"
        )->Fetch();

        $dataFiltered = $this->DB->Query(
            "SELECT COUNT(*) AS val
                FROM (
                    SELECT *
                    FROM osk_diary_responsible_for_acceptance_handbook
                    WHERE 1=1
                ) AS subquery;"
        )->Fetch();


        return [
            'data' => $result,
            'total' => $dataTotal['val'],
            'filtered' => $dataFiltered['val'],
        ];
    }

    public function getProjectData(): array
    {
        $sql = "SELECT DISTINCT sectionNumber as section_number FROM `osk_volumes`;";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = $row;
        }


        return $result;
    }

    public function createContractor($attributes)
    {
        $this->DB->Insert("osk_diary_contractor_handbook", $this->prepearTableData("osk_diary_contractor_handbook", $attributes));
    }

    public function deleteContractor($id)
    {
        $this->DB->Query("DELETE FROM osk_diary_contractor_handbook WHERE id = {$id}");
    }

    public function editContractor(array $attributes)
    {
        $rowId = $attributes['contractor_id'];
        $attrs = [
            'contractor_name' => $attributes['contractor_name'],
        ];

        $this->DB->Update("osk_diary_contractor_handbook",
            $this->prepearTableData("osk_diary_contractor_handbook", $attrs),
            "WHERE id={$rowId}"
        );
    }

    public function deleteKindOfWork($id)
    {
        $this->DB->Query("DELETE FROM osk_diary_kind_of_work_handbook WHERE id = {$id}");
    }

    public function createKindOfWork(array $attributes)
    {
        $this->DB->Insert("osk_diary_kind_of_work_handbook", $this->prepearTableData("osk_diary_kind_of_work_handbook", $attributes));
    }

    public function editKindOfWork($attributes)
    {
        $rowId = $attributes['kind_of_work_id'];
        $attrs = [
            'name' => $attributes['name'],
        ];

        $this->DB->Update("osk_diary_kind_of_work_handbook",
            $this->prepearTableData("osk_diary_kind_of_work_handbook", $attrs),
            "WHERE id={$rowId}"
        );
    }

    public function deleteResponsibleForAcceptance($id)
    {
        $this->DB->Query("DELETE FROM osk_diary_responsible_for_acceptance_handbook WHERE id = {$id}");
    }

    public function editResponsibleForAcceptance($attributes)
    {
        $rowId = $attributes['responsible_id'];
        $attrs = [
            'name' => $attributes['name'],
        ];

        $this->DB->Update("osk_diary_responsible_for_acceptance_handbook",
            $this->prepearTableData("osk_diary_responsible_for_acceptance_handbook", $attrs),
            "WHERE id={$rowId}"
        );
    }

    public function createResponsibleForAcceptance(array $attributes)
    {
        $this->DB->Insert("osk_diary_responsible_for_acceptance_handbook", $this->prepearTableData("osk_diary_responsible_for_acceptance_handbook", $attributes));
    }

    public function getWorkType(string $projectName): array
    {
        $sql = "SELECT distinct workType FROM `osk_volumes` where sectionNumber='{$projectName}';";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getConstructiveName(string $projectName, string $workType): array
    {
        $sql = "SELECT distinct structureName FROM `osk_volumes` where sectionNumber='{$projectName}' and workType = '{$workType}';";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }
}