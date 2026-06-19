<?php

/**
 *
 * Class OverheadController
 */
class OverheadController extends Controller
{
    public function journal()
    {
        $this->data['title'] = 'Журнал накладных расходов';

        $overhead = $this->model('Overhead');
        $project = $this->model('Project');

        $this->data['projects'] = array_merge(
            [
                ["id" => "", "name" => "Все проекты"],
                ["id" => 0, "name" => "Без проекта"]
            ],
            $project->getList()
        );

//        $this->data["result_stages"] = $contractor->getContractorResult();
//        $this->data["user_list"] = $contractor->getTgUserList();

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

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
        $this->addJS('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");

        $this->addCSS("/assets/css/style.css?v=" . rand());

        $this->addJs("/assets/js/overhead/journal.js?v=" . rand());

        $this->view('journal');
    }

    public function getJournalAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $overhead = $this->model('Overhead');

        $filter = [
            'paginate' => [
                'length' => $_POST['length'],  // кол-во строк на страницу
                'start' => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if (!empty($column['search']['value'])) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if (isset($_POST['order']) && !empty($_POST['columns'])) {
            $filter['order']['by'] = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        if (!empty($_POST['date_start'])) {
            $filter['search']['date_start'] = $_POST['date_start'];
        }

        if (!empty($_POST['date_end'])) {
            $filter['search']['date_end'] = $_POST['date_end'];
        }

        if ($_POST['project_id'] != "") {
            $filter['search']['project_id'] = $_POST['project_id'];
        }

       // $userId = $_SESSION['SESS_AUTH']['USER_ID'];

        $data = $overhead->getJournal($filter);

        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];

        unset($data['recordsTotal']);
        unset($data['recordsFiltered']);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "project_bg" => ProjectController::PROJECT_BG,
            "data" => $data,
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    public function insertUpdateAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $overhead = $this->model('Overhead');

        $data = [
            "project_id" => $_POST["project_id"],
            "sum" => $_POST["sum"],
            "date" => $_POST["date"],
        ];

        if (isset($_POST["id"]) && !empty($_POST["id"])) {
            $overhead->updateRow($data, $_POST["id"]);
        } else {
            $overhead->insertRow($data);
        }

        // echo json_encode($id, JSON_UNESCAPED_UNICODE);
        echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
    }

    public function delete()
    {

    }
}