<?php

class JournalController extends Controller
{

    public function concretingJournal()
    {
        global $APPLICATION;
        $APPLICATION->setTitle("Журнал бетонирования");

        $version = "?=" . rand();

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


        $this->addJs("/assets/js/journal/concreting.js" . $version);

        $this->view('concreting');
    }

    public function getConcretingJournal()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $concreting = new Concreting();

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

        $data = $concreting->concretingJournal($filter);

        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];

        unset($data['recordsTotal'], $data['recordsFiltered']);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];


        echo json_encode($jsonData, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    public function editConcreting()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $concreting = new Concreting();
        $data['elemType'] = $_POST['elemType'];
        $data['concreting'] = $concreting->getConcretingById($_POST['diaryJournal']);
        $data['status'] = $concreting->getStatusList();

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    public function getConcretingType()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $concreting = new Concreting();

        $types = $concreting->concretingType();

        echo json_encode($types, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    public function updateConcretingStatus()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $concreting = new Concreting();
        $request = [];
        $file = new File();
        $id = $_POST['diaryJournal'];

        if ($_POST['elemType'] === 'seven_days_cube') {
            $request['sevenDaysCubeProtocol'] = $_POST['numberProtocol'];
            $request['sevenDaysCubeStatus'] = $_POST['status'];
            if (!empty($_FILES)) {
                $name = htmlspecialchars($_FILES[0]["name"]);
                $tmpName = $_FILES[0]["tmp_name"];
                $test = $file->uploadFileServer(
                    $name,
                    $tmpName,
                    "/concrete/{$id}/sevenDaysCube/"
                );
                $request['sevenDaysCubeProtocolUrl'] = "/ulab/upload/concrete/{$id}/sevenDaysCube/" . $name;
            }
        }

        if ($_POST['elemType'] === 'twenty_eigth_days_cube') {
            $request['twentyEigthDaysCubeProtocol'] = $_POST['numberProtocol'];
            $request['twentyEigthDaysCubeStatus'] = $_POST['status'];
            if (!empty($_FILES)) {
                $name = htmlspecialchars($_FILES[0]["name"]);
                $tmpName = $_FILES[0]["tmp_name"];
                $file->uploadFileServer(
                    $name,
                    $tmpName,
                    "/concrete/{$id}/twentyEigthDaysCube/"
                );
                $request['twentyEigthDaysCubeUrl'] = "/ulab/upload/concrete/{$id}/twentyEigthDaysCube/" . $name;
            }
        }
        if ($_POST['elemType'] === 'seven_days_NK') {
            $request['sevenDaysNKProtocol'] = $_POST['numberProtocol'];
            $request['sevenDaysNKStatus'] = $_POST['status'];
            if (!empty($_FILES)) {
                $name = htmlspecialchars($_FILES[0]["name"]);
                $tmpName = $_FILES[0]["tmp_name"];
                $file->uploadFileServer(
                    $name,
                    $tmpName,
                    "/concrete/{$id}/sevenDaysNK/"
                );
                $request['sevenDaysNKProtocolUrl'] = "/ulab/upload/concrete/{$id}/sevenDaysNK/" . $name;
            }
        }
        if ($_POST['elemType'] === 'twenty_eigth_NK') {
            $request['twentyEigthDaysNKProtocol'] = $_POST['numberProtocol'];
            $request['twentyEigthNKStatus'] = $_POST['status'];
            if (!empty($_FILES)) {
                $name = htmlspecialchars($_FILES[0]["name"]);
                $tmpName = $_FILES[0]["tmp_name"];
                $file->uploadFileServer(
                    $name,
                    $tmpName,
                    "/concrete/{$id}/twentyEigthDaysNK/"
                );
                $request['twentyEigthDaysNKProtocolUrl'] = "/ulab/upload/concrete/{$id}/twentyEigthDaysNK/" . $name;
            }
        }

        unset($_POST['diaryJournal']);
        $concreting->updateConcreting($request, $id);

        echo json_encode($test, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    public function updateConcretingType()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        $result['POST'] = $_POST;
        $result['FILES'] = $_FILES;

        $concreting = new Concreting();
        if (!empty($_POST['diaryJournal'])) {
            $id = $_POST['diaryJournal'];
            unset($_POST['diaryJournal']);
            $data = $concreting->updateConcreting($_POST, $id);
        } else {
            $data = $concreting->createConcreting($_POST);
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    public function getStatusBuId()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $concreting = new Concreting();
        $data = $concreting->getStatusBuId($_POST['id']);

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}