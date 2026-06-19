<?php

/**
 * Модель для работы с Проектами (ОСК)
 * Class Project
 */
class Project extends Model
{
    public function getDataById($id = 1, $date = "")
    {

        $gantt = new Gantt();

        $transportWhere = $date ? "DATE_FORMAT(trr.date, '%Y-%m') = '{$date}'" : 1;
        $secondmentWhere = $date ? "DATE_FORMAT(date_end, '%Y-%m') = '{$date}'" : 1;
        $overheadWhere = $date ? "DATE_FORMAT(oo.date, '%Y-%m') = '{$date}'" : 1;

       // $date = $date ?: 1;

        $stmt = $this->DB->Query("
                    SELECT op.*, opm.plan_expenses AS plan_expenses_month,
                     (SELECT SUM(s.total_spent) 
                      FROM secondment AS s
                      LEFT JOIN osk_project_company AS opc ON opc.company_id = s.company_id
                      WHERE opc.project_id = op.id AND {$secondmentWhere}) AS secondment_expenses,
                     (SELECT SUM(trr.price * trr.gsm) FROM transport_report_row AS trr
                      LEFT JOIN transport_report AS tr ON tr.id = trr.report_id
                      WHERE trr.project_id = op.id AND {$transportWhere}) AS fuel_sum,
                     (SELECT SUM(oo.sum) FROM osk_overhead AS oo 
                      WHERE oo.project_id = op.id AND {$overheadWhere}) AS overhead_sum
                    FROM osk_project AS op
                    LEFT JOIN osk_project_month AS opm ON opm.project_id = op.id AND DATE_FORMAT(opm.date, '%Y-%m') = '{$date}' 
                    WHERE op.id = {$id} 
        ");

        $row = $stmt->Fetch();

        if ($date == "") $date = null;

        $row["fot"] = $gantt->getFOTData($id, $date);
        $row["plan_expenses"] = $date ? $row["plan_expenses_month"] : $row["plan_expenses"];
        $row["secondment_expenses"] = round($row["secondment_expenses"], 2);
        $row["fuel_sum"] = round($row["fuel_sum"], 2);
        $row["overhead_sum"] = round($row["overhead_sum"], 2);
        $row["fact_expenses"] = $row["secondment_expenses"] + $row["fuel_sum"] + $row["overhead_sum"] + $row["fot"];
        $row["profitability"] = round(($row["plan_expenses"] - $row["fact_expenses"] ) / $row["plan_expenses"] * 100, 2);



        return $row;
    }

    public function getFuelReportList($filter)
    {
        $where = "trr.project_id = {$filter["project_id"]} AND ";

        if ($filter["date"]) {
            $where .= "DATE_FORMAT(date, '%Y-%m') = '{$filter["date"]}' AND ";
        }

        $where .= 1;
        $sql = "SELECT tr.*, trr.date, DATE_FORMAT(trr.date, '%m') AS month,
                       t.model, t.number, t.consumption_rate, 
                       CONCAT(bu.LAST_NAME, ' ', UPPER(SUBSTRING(bu.NAME,1,1)), '.') AS fio, 
                       (SELECT SUM(price * gsm) FROM transport_report_row WHERE report_id = tr.id AND project_id = {$filter["project_id"]}) AS row_sum
                FROM transport_report AS tr
                LEFT JOIN transport_report_row AS trr ON trr.report_id = tr.id  
                LEFT JOIN transport AS t ON t.id = tr.transport_id 
                LEFT JOIN b_user AS bu ON bu.ID = tr.user_id 
                WHERE {$where}
                GROUP BY tr.id";

        $result = [];

        $stmt = $this->DB->Query($sql);

        while ($row = $stmt->Fetch()) {
            $row["row_sum"] = round($row["row_sum"], 2);
            $result[] = $row;
        }

        return $result;
    }

    public function getProjectMonth($projectId)
    {
        $sql = "SELECT *, DATE_FORMAT(date, '%Y-%m') AS month_date,
                DATE_FORMAT(date, '%m.%Y') AS month_year_point, 
                DATE_FORMAT(date, '%m') AS month, DATE_FORMAT(date, '%Y') AS year 
                FROM osk_project_month 
                WHERE project_id = {$projectId}
                ORDER BY date ASC";

        $result = [];

        $stmt = $this->DB->Query($sql);

        while ($row = $stmt->Fetch()) {
            $row["month_title"] = StringHelper::getMonthTitle(intval($row["month"]));
            $result[] = $row;
        }

        return $result;
    }

    public function getSecondmentList($projectId, $date = "")
    {
        $where = "opc.project_id = {$projectId} AND ";

        if ($date) {
            $where .= "DATE_FORMAT(s.date_end, '%Y-%m') = '{$date}' AND ";
        } else {
            $date = 1;
        }

        $where .= 1;

        $result = [];

        $stmt = $this->DB->Query("
                    SELECT s.*, DATE_FORMAT(s.date_end, '%d.%m.%Y') AS date_end,
                           CONCAT(b_u.LAST_NAME, ' ', LEFT(b_u.NAME, 1), ' ', LEFT(b_u.SECOND_NAME, 1)) fio    
                    FROM secondment AS s 
                    LEFT JOIN b_user AS b_u ON s.user_id = b_u.ID
                    LEFT JOIN b_crm_company AS crm_c ON crm_c.ID = s.company_id
                    LEFT JOIN osk_project_company AS opc ON opc.company_id = s.company_id
                    WHERE {$where}
        ");

        while ($row = $stmt->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getOverheadList($projectId, $date = "")
    {
        $where = "project_id = {$projectId} AND ";

        if ($date) {
            $where .= "DATE_FORMAT(oo.date, '%Y-%m') = '{$date}' AND ";
        } else {
            $date = 1;
        }

        $where .= 1;

        $result = [];

        $stmt = $this->DB->Query("
                    SELECT oo.*, DATE_FORMAT(oo.date, '%d.%m.%Y') AS date  
                    FROM osk_overhead AS oo 
                    WHERE {$where}
        ");

        while ($row = $stmt->Fetch()) {
            $row["sum"] = round($row["sum"], 2);
            $result[] = $row;
        }

        return $result;
    }

    public function addDate($data)
    {
        $updateData = [];

        foreach ($data as $param => $value) {
            $updateData[$param] = $this->quoteStr($this->DB->ForSql(trim($value)));
        }

        return $this->DB->Insert('osk_project_month', $updateData);
    }

    public function updateMonthProject($data, $id, $date)
    {
        $sqlData = $this->prepearTableData('osk_project_month', $data);
        $where = "WHERE project_id = {$id} AND date = '{$date}'";
        return $this->DB->Update('osk_project_month', $sqlData, $where);
    }

    public function insertProject($data)
    {
        $sqlData = $this->prepearTableData('osk_project', $data);
        return $this->DB->Insert('osk_project', $sqlData);
    }

    public function updateProject($data, $id)
    {
        $sqlData = $this->prepearTableData('osk_project', $data);
        $where = "WHERE id = {$id}";
        return $this->DB->Update('osk_project', $sqlData, $where);
    }

    public function getList()
    {
        $result = [];

        $sql = "SELECT op.*,
                    (SELECT 
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', opc.id,
                                'company_id', crm_c.ID,
                                'title', crm_c.TITLE
                            )
                        )
                     FROM osk_project_company AS opc
                     LEFT JOIN b_crm_company AS crm_c ON crm_c.ID = opc.company_id  
                     WHERE opc.project_id = op.id
                    ) AS company_list
                FROM osk_project AS op";

        $stmt = $this->DB->Query($sql);

        while ($row = $stmt->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function addCompany($data)
    {
        $sqlData = $this->prepearTableData('osk_project_company', $data);
        return $this->DB->Insert('osk_project_company', $sqlData);
    }

    public function deleteCompany($id)
    {
        return $this->DB->query("DELETE FROM osk_project_company WHERE id = {$id}");
    }

    public function getFotSum($projectId, $date = "")
    {
        $where = "guh.project_id = {$projectId} AND ";

        if ($date) {
            $where .= "DATE_FORMAT(guh.date, '%Y-%m') = '{$date}' AND ";
        }

        $where .= 1;

        $result = [];

        $stmt = $this->DB->Query("
                    SELECT SUM(guh.hours) * gu.sum AS full_sum
                    FROM gantt_user_hours AS guh
                    LEFT JOIN gantt_users AS gu ON gu.id = guh.user_id
                    WHERE {$where}
        ");

        while ($row = $stmt->Fetch()) {
            $row["full_sum"] = round($row["sum"], 2);
            $result[] = $row;
        }

        return $result;
    }
}