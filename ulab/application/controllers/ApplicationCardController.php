<?php

class ApplicationCardController extends Controller
{
    /**
     * route /ulab/applicationCard/index
     * @return void
     */
    public function index()
    {
        global $APPLICATION;

        $rowId = preg_replace('/[^0-9]/', '', $_SERVER["SCRIPT_URL"]);
        if (empty($rowId)) {
            $this->redirect("/execJournal/index");
            return;
        }

        $version = "?v=" . rand();
        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");
        $this->addCSS("/assets/css/contractor.css" . $version);

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
        $this->addJS("/assets/js/toggle.js");
        $this->addJS("/assets/js/select2.min.js" . $version);

        $this->addJs("/assets/js/appcard-list.js" . $version);

        /** @var  ExecJournal $execJournal */
        $execJournal = $this->model('ExecJournal');
        /** @var SchemeEditor $schemeEditor */
        $schemeEditor = $this->model('SchemeEditor');
        $row = $execJournal->getRowById($rowId);
        if (empty($row)) {
            $this->redirect("/execJournal/index");
            return;
        }

        $row['act'] = $execJournal->getHumanReadableAct($row['act']);
        $title = "Карточка заявки " . $row['application_number'];
        if ($row['closed']) {
            $title = '<span class="badge rounded-pill bg-danger">Заявка закрыта</span> ' . $title;
        }

        $APPLICATION->SetTitle($title);

        $this->data['rowId'] = $rowId;
        $this->data['row'] = $row;
        $this->data['work_types'] = $schemeEditor->getDataToJournal([]);

        $this->view('index');
    }

    /**
     * метод, который вызывается аяксом и формирует данные для таблицы
     * route /ulab/applicationCard/getListProcessingAjax
     * @return void
     */
    public function getListProcessingAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $url = $_POST['url'];
        if (preg_match('/\d+$/', $url, $matches)) {
            $lastNumber = $matches[0];
            $rowId = $lastNumber;
        } else {
            $this->redirect("/execJournal/index");
            return;
        }

        /** @var  ApplicationCard $appCard */
        $appCard = $this->model('ApplicationCard');
        $data = $appCard->getDataToJournal($rowId, $_POST['scheme_id']);


        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => count($data),//$recordsTotal,
            "recordsFiltered" => count($data),//$recordsFiltered,
            "data" => $data
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    /*
     * Метод для обновления записи в карточке заявки
     * route /ulab/applicationCard/updateAppCard
     */
    public function updateAppCard()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        /** @var  ApplicationCard $appCard */
        $appCard = $this->model('ApplicationCard');
        $data = $appCard->update($_POST);

        $this->redirect("/applicationCard/index/" . $_POST['contractorId']);
    }

    /**
     * Route: /ulab/applicationCard/closeCard
     * @return void
     */
    public function closeCard()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        if (!$_GET['cardId']) {
            $this->redirect("/execJournal/index");
            return;
        }

        /** @var  ApplicationCard $appCard */
        $appCard = $this->model('ApplicationCard');
        $data = $appCard->closeCard($_GET['cardId']);

        $this->redirect("/applicationCard/index/" . $_GET['cardId']);
    }

    /**
     * Route: /ulab/applicationCard/openCard
     * @return void
     */
    public function openCard()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        if (!$_GET['cardId']) {
            $this->redirect("/execJournal/index");
            return;
        }

        /** @var  ApplicationCard $appCard */
        $appCard = $this->model('ApplicationCard');
        $data = $appCard->openCard($_GET['cardId']);

        $this->redirect("/applicationCard/index/" . $_GET['cardId']);
    }
}