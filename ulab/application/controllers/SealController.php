<?php

class SealController extends Controller
{
    public function index()
    {
        global $APPLICATION;
        $APPLICATION->setTitle("Реестр протоколов по уплотнению");

        $version = rand();
        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
        $this->addCSS("/assets/css/contractor.css?v={$version}");

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
        $this->addJS('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');
        $this->addJs("/assets/js/seal/seal-journal.js?v={$version}");
        $this->addJS("/assets/js/gantt/moment.min.js?v={$version}");
        $this->addJS("/assets/js/toggle.js?v={$version}");
        $this->addJS("/assets/js/select2.min.js?v={$version}");

        $sealJournal = new SealJournal();
        $this->data['creation_data']['dates'] = $sealJournal->getDatesFromOSKDiary();
        $this->data['creation_data']['coordinates'] = $sealJournal->getCoordinatesFromOSKDiary();

        $this->view();
    }

    protected function collectFilter(array &$filter): void
    {
        foreach ($_POST['columns'] as $column) {
            if (!empty($column['search']['value'])) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if (isset($_POST['order']) && !empty($_POST['columns'])) {
            $filter['order']['by'] = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        if (!empty($_POST['dateStart'])) {
            $filter['search']['dateStart'] = date('Y-m-d', strtotime($_POST['dateStart']));
            $filter['search']['dateEnd'] = date('Y-m-d', strtotime($_POST['dateEnd']));
        }
        if (!empty($_POST['stage'])) {
            $filter['search']['stage'] = $_POST['stage'];
        }
        if (!empty($_POST['lab'])) {
            $filter['search']['lab'] = $_POST['lab'];
        }
        if (!empty($_POST['everywhere'])) {
            $filter['search']['everywhere'] = $_POST['everywhere'];
        }
    }

    public function getListProcessingAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $sealJournal = new SealJournal();

        $filter = [
            'paginate' => [
                'length' => $_POST['length'], // кол-во строк на страницу
                'start' => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        $this->collectFilter($filter);
        $data = $sealJournal->getDataToJournal($filter);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $data['recordsTotal'],
            "recordsFiltered" => $data['recordsFiltered'],
            "data" => $data['data']
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    public function create()
    {
        $sealJournal = new SealJournal();
        $sealJournal->create($_POST, $_FILES);


        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function delete()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $sealJournal = new SealJournal();
        $sealJournal->delete($_POST['id']);


        echo json_encode(['error' => false], JSON_UNESCAPED_UNICODE);
    }

    public function getJournalById()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $sealJournal = new SealJournal();
        $data = $sealJournal->getJournalById($_POST['id']);


        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function edit()
    {
        $sealJournal = new SealJournal();
        $sealJournal->edit($_POST, $_FILES);


        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
}