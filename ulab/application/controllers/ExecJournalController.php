<?php

/**
 * Контроллер для журнала исполнительной документации
 * class ExecJournalController
 */
class ExecJournalController extends Controller
{
    /**
     * route /ulab/execJournal/index
     * @return void
     */
    public function index()
    {
        $this->data['title'] = "Журнал исполнительной документации";

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

        $r = rand();
        $this->addJs("/assets/js/execjournal-list.js?v={$r}");
        $this->syncRows();

        $this->data['instruction_link'] = $this->getInstructionFileLink();

        $this->view('index');
    }

    public function getInstructionFileLink(): string
    {
        $file = new File();
        $driver = \Bitrix\Disk\Driver::getInstance();
        $securityContext = $driver->getFakeSecurityContext();
        $factoryFolder = $file->findFactoryStorageFolder();
        $row = [];
        $fileName = "Описание журнала исполнительной документации.pdf";

        if (!is_null($factoryFolder)) {
            $fileArr = $factoryFolder->getChildren($securityContext);
            foreach ($fileArr as $i => $fileItem) {
                if ($fileItem["NAME"] !== $fileName) {
                    continue;
                }

                $row['bitrix_cert'][$i]['test']['id'] = $fileItem["ID"];
                $row['bitrix_cert'][$i]['test']['name'] = $fileItem["NAME"];

                return "https://" . $_SERVER["SERVER_NAME"]
                    . "/disk/showFile/" . $fileItem["ID"] . "/?&ncc=1&filename=$" . $fileItem["NAME"];
            }
        }

        return "#";
    }

    /**
     * метод для синхронизации записей таблиц tg_contractor и osk_executive_documentation
     * @return void
     */
    public function syncRows()
    {
        /** @var  ExecJournal $execJournal */
        $execJournal = $this->model('ExecJournal');
        $execJournal->synchronizeRows();
    }

    /**
     * Метод для редактирования записи в журнале
     * route /ulab/execJournal/editJournal
     * @return void
     */
    public function editJournal()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        /** @var  ExecJournal $execJournal */
        $execJournal = $this->model('ExecJournal');
        $response = $execJournal->editJournal($_POST['id'], $_POST['name'], $_POST['value']);

        echo json_encode($response);
    }

    /**
     * метод, который вызывается аяксом и формирует данные для таблицы
     * route /ulab/execJournal/getListProcessingAjax
     * @return void
     */
    public function getListProcessingAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        /** @var  ExecJournal $execJournal */
        $execJournal = $this->model('ExecJournal');

        $filter = [
            'paginate' => [
                'length'    => $_POST['length'], // кол-во строк на страницу
                'start'     => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        $this->collectFilter($filter);

        $data = $execJournal->getDataToJournal($filter);

        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];
        unset($data['recordsTotal']);
        unset($data['recordsFiltered']);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
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

    /**
     * метод, который возвращает строку из журнала исп. документации по айди
     * route /ulab/execJournal/getJournalRow
     * @return void
     */
    public function getJournalRow()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        /** @var  ExecJournal $execJournal */
        $execJournal = $this->model('ExecJournal');

        $row = $execJournal->getRowById($_POST['rowId']);

        echo json_encode($row, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Метод для обновления записи журнала
     * route /ulab/execJournal/updateJournalRow
     * @return void
     */
    public function updateJournalRow()
    {
        /** @var  ExecJournal $execJournal */
        $execJournal = $this->model('ExecJournal');
        $execJournal->updateRow($_POST);

        $this->redirect('/ulab/execJournal/index');
    }
}