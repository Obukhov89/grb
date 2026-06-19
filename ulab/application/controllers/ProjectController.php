<?php

/**
 *
 * Class ProjectController
 */
class ProjectController extends Controller
{
    const PROJECT_BG = [
        1 => "bg-sky-blue"
    ];


    public function dashboard($projectId)
    {
        $this->data['title'] = 'Дашборд';

        $project = $this->model('Project');
        $gantt = $this->model('Gantt');

        $this->data["project"] = $project->getDataById($projectId);
        $this->data["project_month"] = $project->getProjectMonth($projectId);
        $this->data["month_exists"] = in_array(date("Y-m" . "-01"), array_column($this->data["project_month"], "date"));


        if ($_GET["date"] && !in_array($_GET["date"] . "-01", array_column($this->data["project_month"], "date"))) {
            $this->redirect("/project/dashboard/{$projectId}");
        }



//        if (!$this->data["month_exists"]) {
//            $this->redirect("/project/dashboard/{$projectId}");
//        }
//        echo "<pre>";
//        var_dump($fot);
//        echo "</pre>";

        $version = "?v=" . rand();
        $this->addCSS("/assets/css/style.css" . $version);
        $this->addCSS("/assets/css/project_dashboard.css" . $version);

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
     //   $this->addCSS("/assets/plugins/DataTables/FixedColumns-4.2.1/css/fixedColumns.css");

        $this->addJS("/assets/plugins/DataTables/DataTables-1.11.3/js/jquery.dataTables.min.js");
        $this->addJS("/assets/plugins/DataTables/ColReorder-1.5.5/js/dataTables.colReorder.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/dataTables.buttons.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.colVis.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.print.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.html5.min.js");
        $this->addJS("/assets/plugins/DataTables/JSZip-2.5.0/jszip.min.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/ellipsis.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/intl.js");
        $this->addJS("/assets/plugins/DataTables/FixedHeader-3.2.0/js/dataTables.fixedHeader.min.js");
        $this->addJS("/assets/plugins/modal/modalWindow.js");
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');
        $this->addJS("/assets/plugins/DataTables/FixedHeader-3.2.0/js/dataTables.fixedHeader.min.js");
     //   $this->addJS("/assets/plugins/DataTables/FixedColumns-4.2.1/js/fixedColumns.js");

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");

        $this->addJS("/assets/plugins/moment/moment-with-locales.min.js");
       // $this->addJS("/assets/js/project/dashboard/main.js" . $version);
        $this->addJS("/assets/js/project/dashboard/project.js" . $version);
        $this->addJS("/assets/js/project/dashboard/secondment.js" . $version);
        $this->addJS("/assets/js/project/dashboard/transport.js" . $version);
        $this->addJS("/assets/js/project/dashboard/overhead.js" . $version);
        $this->view('dashboard');
    }

    public function getDashboardData()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $project = $this->model('Project');

        $data[0] = $project->getDataById($_POST["project_id"], $_POST["date"]);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getFuelReportDataAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $project = $this->model('Project');

        $filter = [
            "project_id" => $_POST["project_id"],
            "date" => $_POST["date"]
        ];

        $data = $project->getFuelReportList($filter);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getSecondmentDataAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $project = $this->model('Project');

        $data = [];

        if ($_POST["project_id"]) {
            $data = $project->getSecondmentList($_POST["project_id"], $_POST["date"]);
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getOverheadDataAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $project = $this->model('Project');

        $data = [];

        if ($_POST["project_id"]) {
            $data = $project->getOverheadList($_POST["project_id"], $_POST["date"]);
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function addDate()
    {
        $project = $this->model('Project');

        if ($_POST["project_id"] && $_POST["date"]) {
            $data = [
                "project_id" => $_POST["project_id"],
                "date" => $_POST["date"] . "-01",
                "plan_expenses" => $_POST["plan_expenses"]
            ];

            $monthArr = $project->getProjectMonth($_POST["project_id"]);
            $monthExists = false;

            $location = "/project/dashboard/{$_POST["project_id"]}";

            if (!empty($monthArr)) {
                $monthList = array_column($monthArr, "date");
                $monthExists = in_array($data["date"], $monthList);
            }

            if ($monthExists) {
                $this->showErrorMessage("Этот месяц уже существует");
                $this->redirect($location);
            }

            $id = $project->addDate($data);


            if ($id) {
                $this->showSuccessMessage("Создание успешно!");
                $location .= "/?date={$_POST["date"]}";
            } else {
                $this->showErrorMessage("Не удалось создать");
            }

            $this->redirect($location);
        }
    }

    public function updateProjectAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $project = $this->model('Project');

        $id = $_POST["project_id"];

        $data = [
            "plan_expenses" => $_POST["plan_expenses"]
        ];

        if ($id) {
            $project->updateProject($data, $id);
        }

        echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
    }

    public function updateMonthProjectAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $project = $this->model('Project');

        $id = $_POST["project_id"];
        $date = $_POST["date"] . "-01";

        $data = [
            "plan_expenses" => $_POST["plan_expenses"]
        ];

        if ($id) {
           $project->updateMonthProject($data, $id, $date);
        }

        echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
    }

    public function journal()
    {
        $this->data['title'] = 'Журнал проектов';

        $version = "?v=" . rand();
        $this->addCSS("/assets/css/style.css" . $version);
        $this->addCSS("/assets/css/project_dashboard.css" . $version);

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
        //   $this->addCSS("/assets/plugins/DataTables/FixedColumns-4.2.1/css/fixedColumns.css");

        $this->addJS("/assets/plugins/DataTables/DataTables-1.11.3/js/jquery.dataTables.min.js");
        $this->addJS("/assets/plugins/DataTables/ColReorder-1.5.5/js/dataTables.colReorder.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/dataTables.buttons.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.colVis.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.print.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.html5.min.js");
        $this->addJS("/assets/plugins/DataTables/JSZip-2.5.0/jszip.min.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/ellipsis.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/intl.js");
        $this->addJS("/assets/plugins/DataTables/FixedHeader-3.2.0/js/dataTables.fixedHeader.min.js");
        $this->addJS("/assets/plugins/modal/modalWindow.js");
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');
        $this->addJS("/assets/plugins/DataTables/FixedHeader-3.2.0/js/dataTables.fixedHeader.min.js");
        //   $this->addJS("/assets/plugins/DataTables/FixedColumns-4.2.1/js/fixedColumns.js");

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");

        $this->addJS("/assets/plugins/moment/moment-with-locales.min.js");
        // $this->addJS("/assets/js/project/dashboard/main.js" . $version);
        $this->addJS("/assets/js/project/journal.js" . $version);

        $this->view('journal');
    }

    public function getListAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $project = $this->model('Project');

        $data = $project->getList();

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function insertUpdateAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $project = $this->model('Project');

        $id = $_POST["id"];

        $data = [
            "name" => $_POST["name"],
            "plan_expenses" => $_POST["plan_expenses"],
        ];

        if (isset($id) && !empty($id)) {
            $project->updateProject($data, $id);
        } else {
            $project->insertProject($data);
        }

        // echo json_encode($id, JSON_UNESCAPED_UNICODE);
        echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
    }

    public function getCompanyListAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $company = $this->model('Company');

        $result = [];
        $title = $_POST["title"];

        if ($title) {
            $result = $company->getListByTitle($title);
        }

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function addProjectCompanyAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $project = $this->model('Project');

        $data = [
            "project_id" => $_POST["id"],
            "company_id" => $_POST["company_id"]
        ];

        if ($data["project_id"] && $data["company_id"]) {
            $project->addCompany($data);
        }

        echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
    }

    public function deleteProjectCompanyAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $project = $this->model('Project');

        $id = $_POST["company_id"];

        if ($id) {
            $project->deleteCompany($id);
        }

        echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
    }
}