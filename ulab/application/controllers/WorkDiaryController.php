<?php

/**
 * related tables: osk_diary_users
 */
class WorkDiaryController extends Controller
{
    /**
     * route: /ulab/workJournal/index
     * @return void
     */
    public function index()
    {
        global $APPLICATION;
        $APPLICATION->setTitle("Дневник ОСК");

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
        $this->addJs("/assets/js/workdiary/work-diary.js?v={$version}");
        $this->addJs("/assets/js/workdiary/contractor-handbook.js?v={$version}");
        $this->addJs("/assets/js/workdiary/kindofwork-handbook.js?v={$version}");
        $this->addJs("/assets/js/workdiary/responsibleforacceptance-handbook.js?v={$version}");
        $this->addJS("/assets/js/gantt/moment.min.js?v={$version}");
        $this->addJS("/assets/js/toggle.js?v={$version}");
        $this->addJS("/assets/js/select2.min.js?v={$version}");

        $diary = new WorkDiary();
        $this->data['creation_data']['contractors_handbook'] = $diary->getContractorHandbook()['data'];
        $this->data['creation_data']['kind_of_work_handbook'] = $diary->getKindOfWorkHandbook()['data'];
        $this->data['creation_data']['responsible_for_acceptance_handbook'] = $diary->getResponsibleForAcceptanceHandbook()['data'];
        $this->data['creation_data']['project_data'] = $diary->getProjectData();

        $this->view();
    }

    public function getListProcessingAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $diary = new WorkDiary();

        $filter = [
            'paginate' => [
                'length' => $_POST['length'], // кол-во строк на страницу
                'start' => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        $this->collectFilter($filter);
        $data = $diary->getDataToJournal($filter);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $data['recordsTotal'],
            "recordsFiltered" => $data['recordsFiltered'],
            "data" => $data['data']
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
     * Метод для создания записи в журнале
     * route /ulab/workJournal/create
     */
    public function create()
    {
        $diary = new WorkDiary();
        $diary->create($_POST, $_FILES);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function getContractorsList()
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

        $diary = new WorkDiary();
        $response = $diary->getContractorHandbook($filter);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $response['total'],
            "recordsFiltered" => $response['filtered'],
            "data" => $response['data']
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    public function createContractor()
    {
        $diary = new WorkDiary();
        $diary->createContractor($_POST);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
    public function deleteContractor()
    {
        $diary = new WorkDiary();
        $contractorId = $_POST['id'];
        $diary->deleteContractor($contractorId);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function editContractor()
    {
        $diary = new WorkDiary();
        $diary->editContractor($_POST);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    /* типы работ справочник */
    public function getKindOfWorkList()
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

        $diary = new WorkDiary();
        $response = $diary->getKindOfWorkHandbook($filter);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $response['total'],
            "recordsFiltered" => $response['filtered'],
            "data" => $response['data']
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    public function deleteKindOfWork()
    {
        $diary = new WorkDiary();
        $diary->deleteKindOfWork($_POST['id']);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function createKindOfWork()
    {
        $diary = new WorkDiary();
        $diary->createKindOfWork($_POST);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function editKindOfWork()
    {
        $diary = new WorkDiary();
        $diary->editKindOfWork($_POST);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    /* Ответственные по приемке спрвочник */
    public function getResponsibleForAcceptanceList()
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

        $diary = new WorkDiary();
        $response = $diary->getResponsibleForAcceptanceHandbook($filter);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $response['total'],
            "recordsFiltered" => $response['filtered'],
            "data" => $response['data']
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    public function createResponsibleForAcceptance()
    {
        $diary = new WorkDiary();
        $diary->createResponsibleForAcceptance($_POST);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function editResponsibleForAcceptance()
    {
        $diary = new WorkDiary();
        $diary->editResponsibleForAcceptance($_POST);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function deleteResponsibleForAcceptance()
    {
        $diary = new WorkDiary();
        $diary->deleteResponsibleForAcceptance($_POST['id']);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function getWorkType()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();


        $diary = new WorkDiary();
        $response = $diary->getWorkType($_POST['project_name']);

        $jsonData = [
            "data" => $response
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    public function getConstructiveName()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $diary = new WorkDiary();
        $response = $diary->getConstructiveName($_POST['project_name'], $_POST['work_type']);

        $jsonData = [
            "data" => $response
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    public function deleteJournal()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();


        $diary = new WorkDiary();
        $diary->delete($_POST['id']);

        echo json_encode(['status' => 200], JSON_UNESCAPED_UNICODE);
    }

    public function getJournalById()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();


        $diary = new WorkDiary();
        $response = $diary->getJournalById($_POST['id']);

        $data = [
            'data' => $response
        ];

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Редактирование журнала
     * @return void
     */
    public function edit()
    {
        $diary = new WorkDiary();
        $diary->edit($_POST, $_FILES);
        
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
}