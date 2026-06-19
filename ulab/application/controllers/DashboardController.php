<?php

class DashboardController extends Controller
{
    public function volumes()
    {
        global $APPLICATION;
        $APPLICATION->setTitle("Дашборд объемы");

        $volumes = new Volumes();
        $version = "?v=" . rand();

        $columnsData = $volumes->getDateWorkJournal();
        $this->data['columnsData'] = explode(',', $columnsData[0]['dates']);

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
        $this->addJS("/assets/plugins/modal/modalWindow.js");
        $this->addJS('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');
        $this->addJs("/assets/js/dashboard/volumes.js{$version}");

        $this->view('volumes');
    }

    public function indexVolumes()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $volumes = new Volumes();

        $filter = [
            'paginate' => [
                'length' => $_POST['length'],
                'start' => $_POST['start'],
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
        if (isset($_POST['searchElem']) && !empty($_POST['searchElem'])) {
            $filter['search']['searchElem'] = $_POST['searchElem'];
        }

        $data['dashboard'] = $volumes->getVolume($filter);

        $data['recordsTotal'] = $data['dashboard']['recordsTotal'];
        $data['recordsFiltered'] = $data['dashboard']['recordsFiltered'];
        $data['workJournal'] = $volumes->getWorkJournal();

        unset($data['dashboard']['recordsTotal'], $data['dashboard']['recordsFiltered']);

//        $jsonData = [
//            "draw" => $_POST['draw'],
//            "recordsTotal" => $recordsTotal,
//            "recordsFiltered" => $recordsFiltered,
//            "data" => $data,
//        ];


        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    public function createVolume()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $volumes = new Volumes();

        echo json_encode($volumes->create(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    public function storeVolumes()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $volumes = new Volumes();
        $volumes->store($_POST);

        echo json_encode($_POST, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    public function editVolumes()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $volumes = new Volumes();
        $data = $volumes->edit($_POST['id']);

        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    public function updateVolumes()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $volumes = new Volumes();
        $id = $_POST['id'];
        unset($_POST['id']);
        $data = $volumes->update($_POST, $id);

        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    public function getUnits()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $volumes = new Volumes();

        echo json_encode($volumes->units(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}