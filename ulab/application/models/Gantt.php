<?php

class Gantt extends Model
{
    /**
     *  здесь собирается коллекция для вьюшки, где главным элементом является проект
     *  и от проекта уже идет список участников
     * @param array $filter
     * @return array
     */
    public function getProjectsInfo(array $filter): array
    {
        $where = "";
        if (!empty(trim($filter['project_filter']))) {
            $where = "WHERE name like '%{$filter['project_filter']}%'";
        }
        $sql = "SELECT * FROM osk_project $where;";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($row = $data->Fetch()) {
            $projectId = $row['id'];

            $members = $this->getMembers($projectId, $filter);


            $result[] = [
                'id' => $projectId,
                'project_name' => $row['name'],
                'color1' => "#d7d7da", // $row['color1'],
                'color2' => "#d7d7da",//$row['color2'],
                'members' => $members,
            ];
        }

        return $result;
    }

    public function getMembers(int $projectId, array $filter): array
    {
        $where = "";
        if (!empty(trim($filter['user_filter']))) {
            $where = " and gu.name like '%{$filter['user_filter']}%'";
        }
        $membersSql = "SELECT distinct gu.*, gup.project_id
                        FROM gantt_user_projects gup
                        JOIN gantt_users gu ON gup.user_id = gu.id
                        WHERE gup.project_id = $projectId $where;";

        $membersData = $this->DB->Query($membersSql);
        $members = [];
        while ($membersRow = $membersData->Fetch()) {
            $members[] = new GanttUser($membersRow);
        }

        return $members;
    }

    public function getAllMembers(): array
    {
        $membersSql = "SELECT distinct gu.*, gup.project_id
                        FROM gantt_user_projects gup
                        JOIN gantt_users gu ON gup.user_id = gu.id;";

        $membersData = $this->DB->Query($membersSql);
        $members = [];
        while ($membersRow = $membersData->Fetch()) {
            $members[] = new GanttUser($membersRow);
        }

        return $members;
    }

    public function createUser(array $data, $salary): int
    {
        $data = $this->prepareDataForSqlQuery($data);
        $userId = $this->DB->Insert("gantt_users", $data);

        $salaryData = [
            'salary' => $salary,
            'user_id' => $userId,
            'created_at' => date('Y-m-d'),
        ];
//        $this->DB->Insert("gantt_user_salaries", $this->prepareDataForSqlQuery($salaryData));

        $this->createUserSalary($userId, $salary);

        return intval($userId);
    }

    public function createUserSalary($userId, $salary)
    {
        $data = [
            'salary' => $salary,
            'user_id' => $userId,
            'salary_date' => date('Y-m'),
        ];

        return $this->DB->Insert("gantt_salaries", $this->prepareDataForSqlQuery($data));
    }

    public function prepareDataForSqlQuery(array $data): array
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        return $data;
    }

    public function createProject(array $data): int
    {
        $data = $this->prepareDataForSqlQuery($data);
        $result = $this->DB->Insert("gantt_projects", $data);

        return intval($result);
    }

    public function getUsers(): array
    {
        $query = "SELECT * FROM gantt_users ORDER BY name ASC;";
//        $query = "SELECT gu.*, gus.salary, gus.tax, gus.grey_salary, gus.active FROM gantt_users as gu
//                    join gantt_user_salaries gus on gus.user_id = gu.id
//                    having gus.active = 1
//                    ORDER BY gu.name ASC;";
//        $query = "SELECT gu.*, gus.salary, gus.tax, gus.grey_salary, gus.active
//          FROM gantt_users AS gu
//          LEFT JOIN gantt_user_salaries AS gus ON gus.user_id = gu.id
//          WHERE (gus.active = 1 OR gus.active IS NULL);";
        $data = $this->DB->Query($query);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getUser(int $userId, int $projectId = null)
    {
        $query = "SELECT * FROM gantt_users where id = $userId;";
//        $query = "SELECT gu.*, gus.salary, gus.tax, gus.grey_salary, gus.active
//          FROM gantt_users AS gu
//          LEFT JOIN gantt_user_salaries AS gus ON gus.user_id = gu.id
//          WHERE gu.id = $userId AND (gus.active = 1 OR gus.active IS NULL);";
        $user = $this->DB->Query($query)->Fetch();

        if (!$user) {
            return [];
        }

        $user['sum'] = $user['salary'] + $user['grey_salary'] + $user['tax'];

        if (!is_null($projectId)) {
            $user['timelines'] = [];
            $query = "SELECT * FROM gantt_user_projects WHERE user_id = $userId AND project_id = $projectId ORDER BY ID;";
            $data = $this->DB->Query($query);
            while ($row = $data->Fetch()) {
                $user['timelines'][] = $row;
            }
        }

        return $user;
    }

    public function getProject(int $projectId, int $userId = null)
    {
        $query = "SELECT * FROM osk_project WHERE id = $projectId;";
        $project = $this->DB->Query($query)->Fetch();

        if (!$project) {
            return [];
        }

        $project['project_name'] = $project['name'];

        if (!is_null($userId)) {
            $project['timelines'] = [];
            $query = "SELECT * FROM gantt_user_projects WHERE user_id = $userId AND project_id = $projectId ORDER BY ID;";
            $data = $this->DB->Query($query);
            while ($row = $data->Fetch()) {
                $project['timelines'][] = $row;
            }
        }

        return $project;
    }

    public function editUser(array $data, $dates, int $userId, $salary): int
    {

        $result = $this->DB->Update("gantt_users", $this->prepareDataForSqlQuery($data), "WHERE id = $userId");

        if (empty($dates)) {
            return intval($result);
        }

        foreach ($dates as $date) {
            $attrs = [
                'start_date' => $date['start_date'],
                'end_date' => empty($date['end_date']) ? null : $date['end_date'],
            ];

            if (new DateTime($attrs['start_date']) > new DateTime($attrs['end_date']) && !is_null($attrs['end_date'])) {
                continue;
            }

//            $this->DB->Update("gantt_user_projects", $this->prepareDataForSqlQuery($attrs), "WHERE id = {$date['row_id']}");
            $attrs = $this->prepareDataForSqlQuery($attrs);
            $endDate = empty($date['end_date']) ? "`end_date` = NULL" : "`end_date` = " . $attrs['end_date'];
            $this->DB->Query("UPDATE `gantt_user_projects` SET `start_date` = {$attrs['start_date']}, $endDate WHERE `id` = '{$date['row_id']}';");
        }

        return intval($result);
    }

    public function editProject(array $data, int $projectId, $dates): int
    {
//        $result = $this->DB->Update("gantt_projects", $this->prepareDataForSqlQuery($data), "WHERE id = $projectId");

        if (empty($dates)) {
            return 1;
        }

        foreach ($dates as $date) {
            $attrs = [
                'start_date' => $date['start_date'],
                'end_date' => empty($date['end_date']) ? null : $date['end_date'],
            ];

            if (new DateTime($attrs['start_date']) > new DateTime($attrs['end_date']) && !is_null($attrs['end_date'])) {
                continue;
            }
            
            //            $this->DB->Update("gantt_user_projects", $this->prepareDataForSqlQuery($attrs), "WHERE id = {$date['row_id']}");
            $attrs = $this->prepareDataForSqlQuery($attrs);
            $endDate = empty($date['end_date']) ? "`end_date` = NULL" : "`end_date` = " . $attrs['end_date'];
            $this->DB->Query("UPDATE `gantt_user_projects` SET `start_date` = {$attrs['start_date']}, $endDate WHERE `id` = '{$date['row_id']}';");

        }

        return 1;
    }

    public function connectUserToProject(int $userId, int $projectId): int
    {
        $date = new DateTime();
        $timezone = new DateTimeZone('Asia/Yekaterinburg');
        $date->setTimezone($timezone);
        $date = $date->format('Y-m-d');
        $this->closePrevProjects($userId, $date);

        $data = [
            'user_id' => $userId,
            'project_id' => $projectId,
            'start_date' => $date,
        ];
        $result = $this->DB->Insert("gantt_user_projects", $this->prepareDataForSqlQuery($data));

        return intval($result);
    }

    private function closePrevProjects(int $userId, string $dateTime)
    {
        $sql = "SELECT * FROM gantt_user_projects WHERE user_id = $userId AND end_date IS NULL;";
        $data = $this->DB->Query($sql);
//        if (count(explode(" ", $dateTime)) == 1) {
//            $dateTime .= " 00:00:00";
//        }

        while ($row = $data->Fetch()) {
            $row['end_date'] = $dateTime;
            $rowId = $row['id'];
            unset($row['id']);

            $this->DB->Update("gantt_user_projects", $this->prepareDataForSqlQuery($row), "WHERE id={$rowId}");
        }
    }

    /**
     * Здесь собираю инфу для вьюшки index2
     * @param array $filter
     * @return array
     */
    public function getTableInfo(array $filter): array
    {
        $where = "";
        if (!empty(trim($filter['user_filter']))) {
            $where = "WHERE name like '%{$filter['user_filter']}%'";
        }
        $sql = "SELECT * FROM gantt_users $where;";
        $data = $this->DB->Query($sql);

        $result = [];
        while ($user = $data->Fetch()) {
            $result[] = new GanttMember($user, $filter);
        }

        return $result;
    }

    /**
     * чтобы пересчитать сумму:
     * UPDATE gantt_users
     * SET sum = COALESCE(salary, 0) + COALESCE(tax, 0) + COALESCE(grey_salary, 0);
     * @param $attributes
     * @param $userId
     * @return mixed
     */
    public function editUserSalary($attributes)
    {
        $data = $attributes['data']['salary'];
        foreach ($data as $salaryData) {
            $salary = $salaryData['salary'] ?? 0;
            $tax = $salaryData['tax'] ?? 0;
            $greySalary = $salaryData['grey_salary'] ?? 0;
            $rowId = $salaryData['salary_id'];

            $this->DB->Update('gantt_salaries', $this->prepareDataForSqlQuery([
                'salary' => $salary,
                'tax' => $tax,
                'grey_salary' => $greySalary,
            ]), "WHERE id = {$rowId}");
        }

        return 1;
    }

    public function getTimeLine(int $id)
    {
        $query = "SELECT * FROM gantt_user_projects WHERE id = {$id}";

        return $this->DB->Query($query)->Fetch();
    }

    public function addHours(array $attributes)
    {
        $timeLine = $this->getTimeLine($attributes['timeline_id']);
        if (!$timeLine) {
            return;
        }

        $endDate = DateTime::createFromFormat('Y-m-d', explode(" ", $timeLine['end_date'])[0]);
        $startDate = DateTime::createFromFormat('Y-m-d', explode(" ", $timeLine['start_date'])[0]);
        $selectedDate = DateTime::createFromFormat('Y-m-d', $attributes['date']);

        /*нельзя поставить часы в дату после окончания и до начала таймлайна. Дату можно редактировать только в рамках таймлайна*/
        if (($endDate && $endDate < $selectedDate) || $startDate > $selectedDate) {
            return;
        }

        $this->DB->Insert('gantt_user_hours', $this->prepareDataForSqlQuery($attributes));
    }

    public function editHours(array $attributes)
    {
        $date = date($attributes['date']);
        $this->DB->Update('gantt_user_hours', ['hours' => $attributes['hours']], "where user_id = {$attributes['user_id']} and project_id = {$attributes['project_id']} and timeline_id = {$attributes['timeline_id']} and date = '$date 00:00:00'");
    }

    public function isHoursExists(array $attributes)
    {
        $date = date($attributes['date']);
        $query = $this->DB->Query("SELECT * FROM `gantt_user_hours` where user_id = {$attributes['user_id']} and project_id = {$attributes['project_id']} and timeline_id = {$attributes['timeline_id']} and date = '$date 00:00:00'");

        return $query->Fetch();
    }

    public function deleteHours(array $attributes)
    {
        $date = date($attributes['date']);
        $this->DB->Query("DELETE FROM `gantt_user_hours` WHERE user_id = {$attributes['user_id']} and project_id = {$attributes['project_id']} and timeline_id = {$attributes['timeline_id']} and date = '$date 00:00:00'");
    }

    public function editHoursByRange(array $attributes)
    {
        $startDate = DateTime::createFromFormat('Y-m-d', $attributes['start_date']);
        $endDate = DateTime::createFromFormat('Y-m-d', $attributes['end_date']);

        $attrs = [
            'hours' => $attributes['hours'],
            'project_id' => $attributes['project_id'],
            'user_id' => $attributes['user_id'],
            'timeline_id' => $attributes['timeline_id'],
        ];


        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $isWeekend = ($currentDate->format('N') >= 6) ? 1 : 0;

            if ($isWeekend) {
                $currentDate->modify('+1 day');
                continue;
            }

            $dateToSave = $currentDate->format('Y-m-d');
            $attrs['date'] = $dateToSave;

            if ($attributes['hours'] == '0' || strlen(trim($attributes['hours'])) == 0) {
                $this->deleteHours($attrs);
            } else if ($this->isHoursExists($attrs)) {
                $this->editHours($attrs);
            } else {
                $this->addHours($attrs);
            }

            $currentDate->modify('+1 day');
        }

        return true;
    }

    public function getProductionCalendar(): array
    {
        $query = "SELECT * FROM `gantt_production_calendar`;";
        $data = $this->DB->Query($query);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function updateProductionCalendar(array $attrs)
    {
        foreach ($attrs as $key => $value) {
            $this->DB->Update("gantt_production_calendar",
                $this->prepareDataForSqlQuery(['work_hours_count' => $value]),
                "WHERE id = {$key}"
            );
        }
    }

    protected function getHoursInfoByUserId(int $userId, string $date=null): array
    {
        $whereDate = " and 1=1";
        if (!is_null($date)) {
            $whereDate = " and salary_date = '$date' ";
        }
//        $query = "SELECT MONTH(date) AS month, SUM(hours) AS user_work_hours, gpc.month_name, gpc.work_hours_count FROM gantt_user_hours join gantt_production_calendar as gpc on MONTH(date) = gpc.month_number where user_id = {$userId} GROUP BY MONTH(date) ORDER BY MONTH(date);";
        $query = "SELECT MONTH(date) AS month,
                YEAR(date) AS salary_year,
                SUM(hours) AS user_work_hours,
                gpc.month_name,
                gpc.work_hours_count,
                gs.salary_date,
                gs.salary,
                gs.tax,
                gs.grey_salary,
                gantt_user_hours.user_id
                FROM gantt_user_hours
                join gantt_production_calendar as gpc on MONTH(date) = gpc.month_number
                join gantt_salaries as gs on CONCAT(YEAR(date), '-',LPAD(MONTH(date), 2, '0')) = gs.salary_date
                and gantt_user_hours.user_id = gs.user_id
                where gantt_user_hours.user_id = {$userId} {$whereDate}
                GROUP BY MONTH(date)
                ORDER BY MONTH(date);";
        $data = $this->DB->Query($query);

        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getGanttUserInfo(int $userId, string $date=null): array
    {
        $hoursInfo = $this->getHoursInfoByUserId($userId, $date);

        foreach ($hoursInfo as $key=>$info)
        {
            $userSalary = $info['salary'] + $info['tax'] + $info['grey_salary'];

            $avgHourRate = round($userSalary / $info['work_hours_count'], 2);
            $salaryByHours = round($avgHourRate * $info['user_work_hours'], 2);
            $hoursInfo[$key]['avg_hour_rate'] = $avgHourRate;
            $hoursInfo[$key]['salary_by_hours'] = $salaryByHours;
        }

        return $hoursInfo;
    }

    public function getGanttUserSalaryInfo(int $userId): array
    {
        $query = "SELECT * FROM `gantt_salaries` where user_id = $userId order by salary_date";
        $data = $this->DB->Query($query);

        $result = [];
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        while ($row = $data->Fetch()) {
            $dateString = $row['salary_date'];
            $date = strtotime($dateString);

            $row['formatted_date'] = strftime("%B %Y", $date);
            $row['sum'] = $row['grey_salary'] + $row['tax'] + $row['salary'];
            $result[] = $row;
        }

        return $result;
    }

    public function getFOTData(int $projectId, string $date=null)
    {
        $whereDate = " and 1=1";
        if (!is_null($date)) {
            $whereDate = " and DATE_FORMAT(date, '%Y-%m') = '$date'";
        }

        $query = "SELECT 
    gantt_user_hours.user_id,
    project_id,
    DATE_FORMAT(date, '%Y-%m') AS sum_date,
    SUM(hours) AS user_work_hours,
gantt_salaries.user_id as salaries_user_id,
gantt_salaries.salary as salary,
gantt_salaries.tax as tax,
gantt_salaries.grey_salary as grey_salary,
gpc.month_name,
gpc.work_hours_count
FROM gantt_user_hours 
join gantt_salaries on DATE_FORMAT(`gantt_user_hours`.`date`, '%Y-%m') = gantt_salaries.salary_date 
and gantt_salaries.user_id = gantt_user_hours.user_id

join gantt_production_calendar as gpc on MONTH(`gantt_user_hours`.`date`) = gpc.month_number
WHERE
    project_id = $projectId
$whereDate
GROUP BY 
    gantt_user_hours.user_id,
    project_id,
    DATE_FORMAT(date, '%Y-%m')
ORDER BY 
    gantt_user_hours.user_id,
    project_id,
    sum_date;";

        $date = $this->DB->Query($query);

        $summa = 0;
        while ($row = $date->Fetch()) {
            $salarySum = $row['salary'] + $row['tax'] + $row['grey_salary'];

            $avgHourRate = round($salarySum / $row['work_hours_count'], 2);
            $salaryByHours = round($avgHourRate * $row['user_work_hours'], 2);

            $summa += $salaryByHours;
        }

        return $summa;
    }

    public function getUserIdsFromProject(int $projectId, string $date=null): array
    {
        $query = "SELECT guh.id, 
                    guh.user_id, guh.project_id,
                    gu.name
                    FROM `gantt_user_hours` as guh
                    join gantt_users as gu on gu.id = guh.user_id
                    where project_id = $projectId group by user_id;";
        if (!is_null($date)) {
            $like = "$date%";
            $query = "SELECT guh.id, 
                    guh.user_id, guh.project_id,
                    gu.name
                    FROM `gantt_user_hours` as guh
                    join gantt_users as gu on gu.id = guh.user_id
                    where project_id = $projectId and date like '{$like}'
                    group by user_id;";
        }

        $data = $this->DB->Query($query);
        $result = [];
        while ($row = $data->Fetch()) {
            $result[] = ['user_id' => $row['user_id'], 'name' => $row['name']];
        }

        return $result;
    }

    public function getEmployeeList(int $projectId, string $date=null): array
    {
        $userIds = $this->getUserIdsFromProject($projectId, $date);

        $result = [];
        foreach ($userIds as $ids) {
            $info = $this->getGanttUserInfo($ids['user_id'], $date);
            $salaryValues = array_column($info, 'salary_by_hours');
            $sum = array_sum($salaryValues);
            $result[] = [
                'summa' => $sum,
                'user_id' => $ids['user_id'],
                'name' => $ids['name'],
            ];
        }

        return $result;
    }
}