<?php

/**
 * Модель журнала исполнительной документации
 * class ExecJournal
 */
class ExecJournal extends Model
{

    protected $fillable = ['project', 'act', 'executive_scheme', 'materials_used', 'quality_document', 'avk_for_materials', 'protocols_conclusions_acts', 'volumes', 'summa',];


    public function editJournal($id, $name, $value): array
    {
        $query = "SELECT * FROM osk_executive_documentation WHERE id = $id;";
        $row = $this->DB->Query($query)->Fetch();

        if (!$row) {
            return ['message' => "Отсутствует запись в базе данных", 'status' => "Bad request", 'error' => true,];
        }

        if (!in_array($name, $this->fillable)) {
            return ['message' => "Такого поля не существует", 'status' => "Bad request", 'error' => true,];
        }

        $this->DB->Update("osk_executive_documentation", [$name => '"' . $value . '"'], "WHERE id = $id");

        return ['message' => "Запись успешно обновлена", 'status' => "ok", 'error' => false,];
    }

    public function synchronizeRows(): void
    {
        $query = "SELECT `id` FROM `tg_contractor` WHERE `id` NOT IN (SELECT `contractor_id` FROM `osk_executive_documentation`);";
        $data = $this->DB->Query($query);

        while ($row = $data->Fetch()) {
            $this->DB->Insert("osk_executive_documentation", ['contractor_id' => $row['id']]);
        }
    }

    public function getDataToJournal(array $filter): array
    {
        $orderBy = $this->collectOrderByString($filter);
        $where = $this->collectWhereString($filter);
        $limit = $this->collectLimitString($filter);

        $query = "SELECT *, DATE_FORMAT(created_at, '%m') AS month_number,
            osk.project,
            osk.contractor_id,
            osk.act,
            osk.executive_scheme,
            osk.materials_used,
            osk.quality_document,
            osk.avk_for_materials,
            osk.protocols_conclusions_acts,
            osk.closed,
            osk.volumes FROM tg_contractor 
            join osk_executive_documentation osk on tg_contractor.id = osk.contractor_id
            {$where} {$orderBy} {$limit}";

        $data = $this->DB->Query($query);

        $result = [];
        while ($row = $data->Fetch()) {
            $contractorId = $row['id'];

            $result[] = [
                'status' => "status",
                'application_number' => $row['monthly_order_number'] . '/' . $row['month_number'],
                'acceptance_date' => $row['datetime'],
                'work_name' => $row['content'],
                'work_place' => $row['work_place'],
                'project' => $row['project'] ?? "",
                'act' => $row['act'],
                'executive_scheme' => $row['executive_scheme'],
                'materials_used' => $row['materials_used'],
                'quality_document' => $row['quality_document'],
                'avk_for_materials' => $row['avk_for_materials'],
                'protocols_conclusions_acts' => $row['protocols_conclusions_acts'],
                'volumes' => $row['volumes'] ?? "",
                'summa' => $row['summa'] ?? "",
                'id' => $contractorId,
                'contractor_id' => $row['contractor_id'],
                'closed' => $row['closed'],
                ];
        }

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM tg_contractor
                    WHERE 1=1;"
        )->Fetch();

        $dataFiltered = $this->DB->Query(
            "SELECT count(*) val FROM tg_contractor 
            join osk_executive_documentation osk on tg_contractor.id = osk.contractor_id
            {$where}"
        )->Fetch();

        $result['recordsTotal'] = $dataTotal['val'];
        $result['recordsFiltered'] = $dataFiltered['val'];

        return $result;
    }

    protected function collectOrderByString(array $filter): string
    {
        $order = ['by' => 'tg_contractor.ID', 'dir' => 'DESC',];

        $orderBy = "ORDER BY tg_contractor.ID DESC";
        if (!empty($filter['order'])) {
            if (in_array($filter['order']['by'], $this->fillable)) {
                $order['by'] = $filter['order']['by'];
                $order['dir'] = $filter['order']['dir'];

                $orderBy = "ORDER BY osk.{$order['by']} {$order['dir']}";
            }
        }

        return $orderBy;
    }

    protected function collectWhereString(array $filter): string
    {
        $where = "WHERE ";

        // работа с фильтрами
        if (!empty($filter['search'])) {
            // № заявки
            if (isset($filter['search']['application_number'])) {
                $where .= "CONCAT(monthly_order_number, '/', DATE_FORMAT(created_at, '%m')) LIKE '%{$filter['search']['application_number']}%' AND ";
            }
            // Дата приемочной комиссии
            if (isset($filter['search']['acceptance_date'])) {
                $where .= "tg_contractor.datetime LIKE '%{$filter['search']['acceptance_date']}%' AND ";
            }
            // Наименование работ
            if (isset($filter['search']['work_name'])) {
                $where .= "tg_contractor.content LIKE '%{$filter['search']['work_name']}%' AND ";
            }
            // Место работ
            if (isset($filter['search']['work_place'])) {
                $where .= "tg_contractor.work_place LIKE '%{$filter['search']['work_place']}%' AND ";
            }
            // Проект
            if (isset($filter['search']['project'])) {
                $where .= "osk.project LIKE '%{$filter['search']['project']}%' AND ";
            }
            // Объемы
            if (isset($filter['search']['volumes'])) {
                $where .= "osk.volumes LIKE '%{$filter['search']['volumes']}%' AND ";
            }
            // Сумма
            if (isset($filter['search']['summa'])) {
                $where .= "osk.summa LIKE '%{$filter['search']['summa']}%' AND ";
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

    public function getRowById(int $id)
    {
        $query = "SELECT tg_contractor.*, DATE_FORMAT(tg_contractor.created_at, '%m') AS month_number,
            osk.project,
            osk.act,
            osk.executive_scheme,
            osk.materials_used,
            osk.quality_document,
            osk.avk_for_materials,
            osk.protocols_conclusions_acts,
            osk.volumes,
            osk.summa,
            osk.general_comment,
            osk.scheme_id,
            osk.id as card_id,
            osk.closed,
            u.phone, u.fio, u.company_name, u.id as user_id
            FROM tg_contractor 
            join osk_executive_documentation osk on tg_contractor.id = osk.contractor_id
            LEFT JOIN tg_user AS u ON u.tg_id = tg_contractor.tg_id
            WHERE osk.contractor_id = $id;";

        $row = $this->DB->Query($query)->Fetch();
        if (!$row) {
            return null;
        }

        (new ApplicationCard())->synchroniseSchemaInformation($id, $row['scheme_id']);
        $row['application_number'] = $row['monthly_order_number'] . '/' . $row['month_number'];

        return $row;
    }

    public function updateRow(array $data)
    {
        $rowId = $data['row_id'];
        unset($data['row_id']);

        foreach ($this->fillable as $value) {
            if (!array_key_exists($value, $data)) {
                $data[$value] = null;
            }
        }

        foreach ($data as $key => $value) {
            if ($value == "on") {
                $value = true;
            } else if ($value == "false") {
                $value = false;
            }

            $this->DB->Update("osk_executive_documentation", [$key => '"' . $value . '"'], "WHERE contractor_id = $rowId");
        }
    }

    public function getHumanReadableAct(int $act): string
    {
        switch ($act) {
            case 1:
                return "АОСР";
            case 2:
                return "АООК";
            case 3:
                return "Не требуется";
            case 0:
            default:
                return "Не выбрано";
        }
    }
}