<?php

class Concreting extends Model
{

    public function concretingJournal($filter)
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
            SELECT 
                odj.*,
                ov.id as volumes_id,
                ov.sectionNumber as volumes_sectionNumber,
                ov.workType as volumes_workType,
                ov.structureName as volumes_id,
                ov.unit as volumes_unit,
                ov.projectVolumes as volumes_projectVolumes,
                okwh.id as osk_diary_kind_of_work_handbook_id,
                okwh.name as osk_diary_kind_of_work_handbook_name,
                oc.id as concrete_id,
                oc.mark as concrete_mark,
                oc.sevenDaysCubeProtocol as concrete_sevenDaysCubeProtocol,
                oc.twentyEigthDaysCubeProtocol as concrete_twentyEigthDaysCubeProtocol,
                oc.sevenDaysCubeProtocolUrl as concrete_sevenDaysCubeProtocolUrl,
                oc.twentyEigthDaysCubeUrl as concrete_twentyEigthDaysCubeUrl,
                oc.sevenDaysCubeStatus as concrete_sevenDaysCubeStatus,
                oc.twentyEigthDaysCubeStatus as concrete_twentyEigthDaysCubeStatus,
                oc.sevenDaysNKStatus as concrete_sevenDaysNKStatus,
                oc.twentyEigthNKStatus as concrete_twentyEigthNKStatus,
                oc.sevenDaysNKProtocolUrl as concrete_sevenDaysNKProtocolUrl,
                oc.twentyEigthDaysNKProtocolUrl as concrete_twentyEigthDaysNKProtocolUrl,
                oc.sevenDaysNKProtocol as concrete_sevenDaysNKProtocol,
                oc.twentyEigthDaysNKProtocol as concrete_twentyEigthDaysNKProtocol,
                oct.id as concrete_type_id,
                oct.name as concrete_type_name
            FROM osk_diary_journal odj
            LEFT JOIN osk_volumes ov ON odj.project = ov.sectionNumber AND odj.work_type = ov.workType AND odj.constructive_name = ov.structureName
            LEFT JOIN osk_diary_kind_of_work_handbook okwh ON okwh.id = odj.kind_of_work_id
            LEFT JOIN osk_concrete oc ON oc.diaryJournal = odj.id
            LEFT JOIN osk_concrete_type oct ON oct.id = oc.concrete
        
            WHERE odj.kind_of_work_id = 6

        ");

        $dataTotal = $this->DB->Query(
            " SELECT odj.*, ov.*, okwh.* FROM osk_diary_journal odj
            LEFT JOIN osk_volumes ov ON odj.project = ov.sectionNumber AND odj.work_type = ov.workType AND odj.constructive_name = ov.structureName
            LEFT JOIN osk_diary_kind_of_work_handbook okwh ON okwh.id = odj.kind_of_work_id
            WHERE odj.kind_of_work_id = 6
            "
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "  SELECT odj.*, ov.*, okwh.* FROM osk_diary_journal odj
            LEFT JOIN osk_volumes ov ON odj.project = ov.sectionNumber AND odj.work_type = ov.workType AND odj.constructive_name = ov.structureName
            LEFT JOIN osk_diary_kind_of_work_handbook okwh ON okwh.id = odj.kind_of_work_id 
            WHERE ${where} AND  odj.kind_of_work_id = 6
          "
        )->SelectedRowsCount();

        $result = [];

        while ($row = $mainSql->fetch()) {
            $result[] = $row;
            $result['recordsTotal'] = $dataTotal;
            $result['recordsFiltered'] = $dataFiltered;
        }

        return $result;
    }

    public function createConcreting($request)
    {
        $request = $this->prepareData($request);
        return $this->DB->insert('osk_concrete', $request);
    }

    public function updateConcreting($request, $id)
    {
        $data['request_before'] = $request;
        $request = $this->prepareData($request);
        $data['sql_status'] = $this->DB->Update('osk_concrete', $request, "WHERE diaryJournal = {$id}");
        $data['request'] = $request;
        $data['id'] = $id;
        return $data;
    }

    public function concretingType()
    {
        $sql = $this->DB->query("
        SELECT * FROM osk_concrete_type
        ");

        while ($row = $sql->fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getConcretingById($id)
    {
        $sql = $this->DB->query("
        SELECT oc.* FROM osk_concrete oc
        WHERE oc.diaryJournal = {$id}
        ");

        while ($row = $sql->fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getStatusList()
    {
        $sql = $this->DB->query("
        SELECT * FROM osk_concrete_status
        ");

        while ($row = $sql->fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getStatusBuId($id)
    {
        $sql = $this->DB->query("
        SELECT * FROM osk_concrete_status
        WHERE id = {$id}
        ");

        $result = $sql->fetch();
        return $result;
    }
}