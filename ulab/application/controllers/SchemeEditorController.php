<?php

class SchemeEditorController extends Controller
{
    /**
     * type = 1 должен открывать паспортизацию,
     * а type = 2 откроет входной контроль
     * route: /ulab/schemeEditor/index
     * @return void
     */
    public function index()
    {
        $this->data['title'] = "Редактор схем";

        $version = "?v=" . rand();
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

        $this->addJs("/assets/js/schemeEditor.js" . $version);

        $this->view();
    }

    /**
     * метод, который вызывается аяксом и формирует данные для таблицы
     * route /ulab/schemeEditor/getListProcessingAjax
     * @return void
     */
    public function getListProcessingAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        /** @var SchemeEditor $schemeEditor */
        $schemeEditor = $this->model('SchemeEditor');

        $filter = [
            'paginate' => [
                'length' => $_POST['length'], // кол-во строк на страницу
                'start' => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        $this->collectFilter($filter);

        $data = $schemeEditor->getDataToJournal($filter);

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
     * метод для создания записи в редакторе схем
     * route /ulab/schemeEditor/create
     * @return void
     */
    public function create()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $workType = $_POST['work_type'];
        $object = $_POST['object'];


        /** @var SchemeEditor $schemeEditor */
        $schemeEditor = $this->model('SchemeEditor');
        $schemeEditor->createRow($workType, $object);

        $this->redirect('/schemeEditor/index');
    }

    /**
     * метод для создания записи в редакторе схем
     * route /ulab/schemeEditor/create
     * @return void
     */
    public function createScheme()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $attributes = ['name' => $_POST['name'], 'work_type_id' => $_POST['work_type_id'],];

        /** @var SchemeCard $schemeCard */
        $schemeCard = $this->model('SchemeCard');
        $schemeCard->createScheme($attributes);

        $this->redirect('/schemeEditor/index');
    }

    /**
     * метод для редактирования строки в редакторе схем
     * route /ulab/schemeEditor/edit
     * @return void
     */
    public function edit()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $attributes = ['work_type' => $_POST['work_type'], 'object' => $_POST['object'], 'work_type_id' => $_POST['work_type_id'],];

        /** @var SchemeEditor $schemeEditor */
        $schemeEditor = $this->model('SchemeEditor');
        $schemeEditor->editScheme($attributes);

        $this->redirect('/schemeEditor/index');
    }

    /**
     * Метод для удаления записи из редактора схем
     * route /ulab/schemeEditor/deleteSchemeEditorItem
     * @return void
     */
    public function deleteSchemeEditorItem()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $workTypeId = $_POST['worktype_id'];

        $response = ['error' => false, 'message' => "",];

        if (is_null($workTypeId)) {
            $response['error'] = true;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            return;
        }

        /** @var SchemeEditor $schemeEditor */
        $schemeEditor = $this->model('SchemeEditor');
        $schemeEditor->delete($workTypeId);

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Метод для отображения карточки схемы
     * route /ulab/schemeEditor/card/{id}
     * @return void
     */
    public function card()
    {
        global $APPLICATION;
        $APPLICATION->setTitle("Карточка схемы");

        $version = "?v=" . rand();
        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");

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
        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');


        $this->addJs("/assets/js/schemeEditorCard.js" . $version);

        $rowId = preg_replace('/[^0-9]/', '', $_SERVER["SCRIPT_URL"]);
        if (empty($rowId)) {
            $this->redirect("/schemeEditor/index");
            return;
        }

        $schemeCard = new SchemeCard();

        $schemeData = $schemeCard->getSchemeCardData($rowId);
        if (empty($schemeData)) {
            $this->redirect("/schemeEditor/index");
            return;
        }

        $this->data['scheme'] = $schemeData;
        $this->data['id_types'] = $schemeCard->getIDTypes();

        $this->view('scheme_card');
    }

    /**
     * Создание типа типа исполнительной документации для селекта и редирект обратно
     * @return void
     */
    public function createId()
    {
        $schemeCard = new SchemeCard();
        $schemeCard->createID($_POST['name']);


        $this->redirect("/schemeEditor/card/" . $_POST['card_id']);
    }

    /**
     * Создание типа документации в карточке схемы
     * route: /ulab/schemeEditor/createIDType
     * @return void
     */
    public function createIDType()
    {
        $attributes = ['type_id' => $_POST['type_id'], 'card_id' => $_POST['card_id'],];

        $schemeCard = new SchemeCard();
        $schemeCard->createIDType($attributes);

        $this->redirect("/schemeEditor/card/" . $_POST['card_id']);
    }

    /**
     * route: /ulab/schemeEditor/getIDTypesList
     * @return void
     */
    function getIDTypesList()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $filter = [
            'paginate' => [
                'length' => $_POST['length'], // кол-во строк на страницу
                'start' => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        $this->collectFilter($filter);

        $schemeCard = new SchemeCard();
        $data = $schemeCard->getAllSchemeCardData($_POST['card_id'], $filter);

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

    /**
     * route: /ulab/schemeEditor/deleteIDType
     * @return void
     */
    public function deleteIDType()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $schemeTypeId = $_POST['scheme_type_id'];

        $response = ['error' => false, 'message' => "",];

        if (is_null($schemeTypeId)) {
            $response['error'] = true;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            return;
        }

        $schemeCard = new SchemeCard();
        $schemeCard->deleteIDType($schemeTypeId);

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * route: /ulab/schemeEditor/editIDType
     * @return void
     */
    public function editIDType()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $attributes = ['name' => $_POST['name'], 'type_id' => $_POST['type_id'],];

        if (is_null($attributes['type_id'])) {
            $this->redirect("/schemeEditor/card/" . $_POST['card_id']);
            return;
        }

        $schemeCard = new SchemeCard();
        $schemeCard->editIDType($attributes);

        $this->redirect("/schemeEditor/card/" . $_POST['card_id']);
    }

    /**
     * route: /ulab/schemeEditor/deleteScheme
     * @return void
     */
    public function deleteScheme()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        if (is_null($_POST['scheme_id'])) {
            $this->redirect('/schemeEditor/index');
            return;
        }

        $attributes = ['scheme_id' => $_POST['scheme_id'],];

        $schemeCard = new SchemeCard();
        $schemeCard->deleteScheme($attributes);

        $this->redirect('/schemeEditor/index');
    }
}