<?php

class GanttController extends Controller
{
    /**
     * route: /ulab/gantt/index
     * @return void
     */
    public function index()
    {
        global $APPLICATION;
//        $APPLICATION->RestartBuffer();
//        $APPLICATION->setTitle("Протоколирование рабочего времени");
        $APPLICATION->setTitle("ПроРВа (Протоколирование рабочего времени)");

        $version = "?v=" . rand();

        $this->addJS("/assets/js/jquery-3.5.1.min.js" . $version);
        $this->addJS("/assets/js/gantt/gantt.js" . $version);
        $this->addJS("/assets/js/gantt/moment.min.js" . $version);
        $this->addJS("/assets/js/gantt/daterangepicker.min.js" . $version);
        $this->addJS("/assets/plugins/modal/modalWindow.js");
        $this->addJS('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
        $this->addCSS("/assets/css/gantt.css" . $version);
        $this->addCSS("/assets/css/daterangepicker.css" . $version);

        $filter = [
            'user_filter' => $_GET['user_filter'],
            'project_filter' => $_GET['project_filter'],
            'filter_dates' => $_GET['filter_dates'],
        ];

        $validatedDates = $this->validateAndSplitDates($filter['filter_dates']);
        if (!$validatedDates) {
            $filter['filter_dates'] = $this->getDateFilterStub();

            $currentDate = new DateTime();
            $endDate = clone $currentDate;
            $startDate = clone $currentDate;
            $interval = new DateInterval('P14D');

            $endDate->add(new DateInterval('P7D'));
            $startDate->sub($interval);

            $this->data['calendar'] = $this->createCalendarByDateRange($startDate->format('d.m.Y'), $endDate->format('d.m.Y'));
        } else {
            $this->data['calendar'] = $this->createCalendarByDateRange($validatedDates[0], $validatedDates[1]);
        }


        $this->data['current_date'] = $this->getCurrentDate();
        $this->data['filter'] = $filter;
        if ($_GET['action'] == "reset") {
            $this->data['filter'] = [];
            $filter = [];
            unset($_GET['user_filter']);
            unset($_GET['project_filter']);
            $this->data['filter']['filter_dates'] = $this->getDateFilterStub();
        }

        $gantt = new Gantt();
        $this->data['table']['users'] = $gantt->getUsers();
        $this->data['production_calendar'] = $gantt->getProductionCalendar();

        if (is_null($_GET['VIEW_MODE']) || $_GET['VIEW_MODE'] == "1") {
            $projects = $gantt->getProjectsInfo($filter);
            $this->data['table']['projects'] = $projects;

            $this->data['VIEW_MODE'] = 1;
            $this->view("index");

            return;
        }

        $this->data['VIEW_MODE'] = 2;
        $this->data['table']['table_info'] = $gantt->getTableInfo($filter);

        $this->view("index");
    }

    public function validateAndSplitDates(?string $dateRange): ?array
    {
        if (!$dateRange) {
            return null;
        }

        $dates = explode(' - ', $dateRange);

        if (count($dates) !== 2) {
            return null;
        }

        foreach ($dates as $date) {
            $dateTime = DateTime::createFromFormat('d.m.Y', $date);
            if ($dateTime === false) {
                return null;
            }
        }

        return $dates;
    }

    /**
     * заглушка для фильтра с диапазом дат
     * @return string
     */
    public function getDateFilterStub(): string
    {
        $currentDate = new DateTime();
        $endDate = clone $currentDate;
        $startDate = clone $currentDate;


        $interval = new DateInterval('P14D');
        $endDate->add(new DateInterval('P7D'));

        $startDate->sub($interval);

        return $startDate->format('d.m.Y') . " - " . $endDate->format('d.m.Y');
    }

    /**
     *  method: post
     *  route: /ulab/gantt/collectTimeLineInfo
     * @return void
     */
    public function collectTimeLineInfo()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $gantt = new Gantt();

        $response = [];
//        $projects = $gantt->getProjectsInfo();
        $projects = $gantt->getAllMembers();
        $response['data'] = $projects;

        echo json_encode($response);
    }

    protected function getCurrentDate(): array
    {
        $currentDate = new DateTime();
        $timezone = new DateTimeZone('Asia/Yekaterinburg');
        $currentDate->setTimezone($timezone);
//        $currentDate->setTimezone(new DateTimeZone('GMT+5'));

        $currentMonth = $currentDate->format('F');
        $currentMonthNumber = $currentDate->format('n');
        $currentYear = $currentDate->format('Y');
        $currentDay = $currentDate->format('j');

        return [
            'month' => [
                'name' => $currentMonth,
                'number' => $currentMonthNumber,
            ],
            'year' => $currentYear,
            'day' => $currentDay,
            'full_date' => $currentDate->format('Y-m-d'),
        ];
    }

    protected function createCalendarByDateRange(string $dateFrom, string $dateTo): array
    {
        $startDate = DateTime::createFromFormat('d.m.Y', $dateFrom);
        $endDate = DateTime::createFromFormat('d.m.Y', $dateTo);

        $calendar = [];

        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $monthName = $currentDate->format('F');
            $monthNumber = $currentDate->format('n');
            $year = $currentDate->format('Y');
            $dayOfWeek = $currentDate->format('N');
            $key = $monthName . "_" . $year;
            if (!isset($calendar[$key])) {
                $calendar[$key] = [
                    'local_name' => strftime("%B", $currentDate->getTimestamp()),
                    'days' => [],
                    'month_number' => $monthNumber,
                    'year' => $year,
                ];
            }

            $isWeekend = ($dayOfWeek >= 6) ? 1 : 0;

            $calendar[$key]['days'][] = [
                'day' => $currentDate->format('j'),
                'is_weekend' => $isWeekend,
            ];

            $currentDate->modify('+1 day');
        }

        return $calendar;
    }

    protected function createCalendar(): array
    {

        $currentDate = new DateTime();
        $timezone = new DateTimeZone('Asia/Yekaterinburg');
//        $currentDate->setTimezone($timezone);
        $endDate = clone $currentDate;
        $startDate = clone $currentDate;


        $interval = new DateInterval('P14D');
        $endDate->add(new DateInterval('P7D'));


        $calendar = [];

        $startDate->sub($interval);

        while ($startDate <= $endDate) {
            $monthName = $startDate->format('F');
            $monthNumber = $startDate->format('n');
            $year = $startDate->format('Y');

            if (!isset($calendar[$monthName])) {
                $calendar[$monthName] = [
                    'local_name' => strftime("%B", $startDate->getTimestamp()),
                    'days' => [],
                    'month_number' => $monthNumber,
                    'year' => $year,
                    'full_date' => $startDate->format('Y-m-d'),
                ];
            }

            $calendar[$monthName]['days'][] = $startDate->format('j');

            $startDate->add(new DateInterval('P1D'));
        }

        return $calendar;
    }

    /**
     * route: /ulab/gantt/addUser
     * method: POST
     * @return void
     */
    public function addUser()
    {
        $attributes = [
            'name' => $_POST['name'],
            'position' => $_POST['position'],
//            'salary' => $_POST['salary'],
        ];

        $salary = $_POST['salary'];

        $gantt = new Gantt();
        $gantt->createUser($attributes, $salary);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    /**
     * route: /ulab/gantt/addProject
     * method: POST
     * @return void
     */
    public function addProject()
    {
        header("Location: " . $_SERVER['HTTP_REFERER']);
//        return;
//        $attributes = [
//            'project_name' => $_POST['project_name'],
//            'color1' => $_POST['color1'],
//            'color2' => $_POST['color2'],
//        ];
//
//        $gantt = new Gantt();
//        $gantt->createProject($attributes);
//
//        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    /**
     * route: /ulab/gantt/getUser
     * method: POST
     * @return void
     */
    public function getUser()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $response = [
            'error' => false,
            'message' => '',
            'data' => [],
        ];

        if (empty($_POST['id'])) {
            $response['error'] = true;
            $response['message'] = "Отсутствует идентификатор пользователя";


            echo json_encode($response);
            return;
        }

        $gantt = new Gantt();
        $data = $gantt->getUser($_POST['id'], empty($_POST['project_id']) ? null : $_POST['project_id']);

        $response['data'] = $data;

        echo json_encode($response);
    }

    /**
     * route: /ulab/gantt/getProject
     * method: POST
     * @return void
     */
    public function getProject()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $response = [
            'error' => false,
            'message' => '',
            'data' => [],
        ];

        if (empty($_POST['id'])) {
            $response['error'] = true;
            $response['message'] = "Отсутствует идентификатор проекта";


            echo json_encode($response);
            return;
        }

        $gantt = new Gantt();
        $data = $gantt->getProject(intval($_POST['id']), intval($_POST['user_id']));

        $response['data'] = $data;

        echo json_encode($response);
    }

    /**
     * route: /ulab/gantt/editUser
     * method: POST
     * @return void
     */
    public function editUser()
    {
        $attributes = [
            'name' => $_POST['name'],
            'position' => $_POST['position'],
//            'salary' => $_POST['salary'],
        ];
        $dates = $_POST['dates'];
        $userId = $_POST['user_id'];
        $salary = $_POST['salary'];

        $gantt = new Gantt();
        $gantt->editUser($attributes, $dates, $userId, $salary);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    /**
     * route: /ulab/gantt/editProject
     * method: POST
     * @return void
     */
    public function editProject()
    {

        $attributes = [
            'project_name' => $_POST['project_name'],
        ];
        $projectId = $_POST['project_id'];
        $memberId = $_POST['project_member_id'];
        $dates = $_POST['dates'];

        $gantt = new Gantt();

        $gantt->editProject($attributes, $projectId, $dates);
        if (!empty($memberId) && intval($memberId) != -1) {
            $gantt->connectUserToProject($memberId, $projectId);
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    /**
     * route: /ulab/gantt/editUserSalary
     * method: POST
     * @return void
     */
    public function editUserSalary()
    {
        $attributes = [
            'data' => $_POST,
        ];

        $gantt = new Gantt();
        $gantt->editUserSalary($attributes);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    /**
     * route: /ulab/gantt/addHours
     * method: POST
     * @return void
     */
    public function addHours()
    {
        $attributes = [
            'date' => $_POST['project_sql_date'],
            'hours' => $_POST['project_hours'],
            'project_id' => $_POST['project_id'],
            'user_id' => $_POST['user_id'],
            'timeline_id' => $_POST['timeline_id'],
        ];

        $gantt = new Gantt();
        $gantt->addHours($attributes);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function editHours()
    {
        $attributes = [
            'date' => $_POST['project_sql_date'],
            'hours' => $_POST['project_hours'],
            'project_id' => $_POST['project_id'],
            'user_id' => $_POST['user_id'],
            'timeline_id' => $_POST['timeline_id'],
        ];

        $gantt = new Gantt();
        if ($attributes['hours'] == '0' || strlen(trim($attributes['hours'])) == 0) {
            $gantt->deleteHours($attributes);
        } else {
            $gantt->editHours($attributes);
        }


        header("Location: " . $_SERVER['HTTP_REFERER']);
    }


    public function editHoursByRange()
    {
        $attributes = [
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'hours' => $_POST['hours'],
            'project_id' => $_POST['project_id'],
            'user_id' => $_POST['user_id'],
            'timeline_id' => $_POST['timeline_id'],
        ];

        $gantt = new Gantt();
        $gantt->editHoursByRange($attributes);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function editProductionCalendar()
    {
        $attributes = $_POST;

        $gantt = new Gantt();
        $gantt->updateProductionCalendar($attributes['work_hours']);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function getUserInfo()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $userId = $_POST['user_id'];
        if (!$userId) {
            echo json_encode([]);
            return;
        }

        $gantt = new Gantt();
        $response = [];
        $response['data'] = $gantt->getGanttUserInfo($userId);

        echo json_encode($response);
    }

    public function getUserSalaryInfo()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $userId = $_POST['user_id'];
        if (!$userId) {
            echo json_encode([]);

            return;
        }

        $gantt = new Gantt();
        $response = [];
        $response['data'] = $gantt->getGanttUserSalaryInfo($userId);

        echo json_encode($response);
    }

    public function getFOTData()
    {
        $projectId = 1;
        $date = "2024-04";

        $gantt = new Gantt();
        $gantt->getFOTData($projectId, $date);

        die();
    }

    public function getEmployeeList()
    {
        $projectId = 1;
        $date = "2024-04";

        $gantt = new Gantt();
        $gantt->getEmployeeList($projectId, $date);

        die();
    }
}