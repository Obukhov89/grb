<?php

class ReactiveController extends Controller
{
//    private string $nameModel = 'Reactive';

    public function list()
    {
        $this->data['title'] = 'Реактивы';

        /** @var Request $request */
        $request = $this->model('Request');

        /** @var  Recipe $usedModel */
        $usedModel = $this->model('Reactive');

		/** @var  Recipe $usedModel */
		$LabModel = $this->model('Lab');

        $this->data['date_start'] = $request->getDateStart();
        $this->data['aggregate'] = $usedModel->getFromSQL('aggregate');
        $this->data['pure'] = $usedModel->getFromSQL('pure');
        $this->data['reactive'] = $usedModel->getFromSQL('reactive');
        $this->data['full_reactive'] = $usedModel->getFromSQL('full_reactive_name');
		$this->data['laboratory'] = $LabModel->getLabList();
        $this->data['unit_of_measurement'] = $usedModel->getFromSQL('unit_of_measurement');

        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addJs("/assets/plugins/select2/dist/js/select2.min.js");

        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");

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

        $this->addJS("/assets/plugins/DataTables/Responsive-2.4.0/dataTables.responsive.js");

        $this->addJs("/assets/js/reactive-journal.js");

        $this->view('list');
    }

	public function list_reactive()
	{
		$this->data['title'] = 'Журнал реактивов';

		/** @var Request $request */
		$request = $this->model('Request');

		/** @var  Recipe $usedModel */
		$usedModel = $this->model('Reactive');
		/** @var  Recipe $usedModel */
		$LabModel = $this->model('Lab');

		$this->data['date_start'] = $request->getDateStart();
		$this->data['aggregate'] = $usedModel->getFromSQL('aggregate');
		$this->data['pure'] = $usedModel->getFromSQL('pure');
		$this->data['reactive'] = $usedModel->getFromSQL('reactive');
		$this->data['full_reactive'] = $usedModel->getFromSQL('full_reactive_name');
		$this->data['unit_of_measurement'] = $usedModel->getFromSQL('unit_of_measurement');
		$this->data['laboratory'] = $LabModel->getLabList();

		$this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
		$this->addJs("/assets/plugins/select2/dist/js/select2.min.js");

		$this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
		$this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

		$this->addCSS("/assets/plugins/DataTables/datatables.min.css");
		$this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
		$this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");

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

		$this->addJS("/assets/plugins/DataTables/Responsive-2.4.0/dataTables.responsive.js");

		$this->addJs("/assets/js/reactive-journal2.js");

		$this->view('list_reactive');
	}

    public function getListProcessingAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Reactive $usedModel */
        $usedModel = $this->model('Reactive');

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
        $data = $usedModel->getList($filter);

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

    public function getListReactiveProcessingAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Reactive $usedModel */
        $usedModel = $this->model('Reactive');

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


        $data = $usedModel->getList($filter, 'getListReactive');

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

    public function addReactiveAndReceive()
    {
        $successMsg = 'Реактив успешно сохранен';
        $unsuccessfulMsg = 'Не удалось сохранить реактив';

		/** @var Reactive $usedModel */
        $usedModel = $this->model('Reactive');

        $newAdd['receive'] = $_POST['receive'];
        /*$reactiveName = $usedModel->getByID('reactiveName', $newAdd['reactive']['id_reactive_model']);*/

//        if ($usedModel->checkData($newAdd['reactive'], 'reactiveNumber')) {
//            $this->showErrorMessage("Уже существует реактив с таким номером");
//            $this->redirect('/reactive/list/');
//        }
//        if ($usedModel->checkData($newAdd['reactive'], 'reactivePure')) {
//            $this->showErrorMessage("Уже существует реактив  с такой квалификацией");
//            $this->redirect('/reactive/list/');
//        }

        $isAdd = $usedModel->addToSQL($newAdd, 'reactiveAndReceive');

        if (!$isAdd) {
            $this->showErrorMessage($unsuccessfulMsg);
            $this->redirect('/reactive/list/');
        }

        $this->showSuccessMessage($successMsg);
        $this->redirect('/reactive/list/');
    }


    public function addReactiveModel()
    {
        $successMsg = 'Реактив успешно сохранен';
        $unsuccessfulMsg = 'Не удалось сохранить реактив';

		/** @var Reactive $usedModel */
        $usedModel = $this->model('Reactive');

        $newAdd = $_POST['toSQL'];

//        if ($usedModel->checkData($newAdd)) {
//            $this->showErrorMessage('Уже существует реактив с таким именем');
//            $this->redirect('/reactive/list/');
//        }

        $isAdd = $usedModel->addToSQL($newAdd);

        if (!$isAdd) {
            $this->showErrorMessage($unsuccessfulMsg);
            $this->redirect('/reactive/list_reactive/');
        }

        $this->showSuccessMessage($successMsg);
        $this->redirect('/reactive/list_reactive/');
    }

	public function upadateReactiveModel()
	{
		$successMsg = 'Реактив успешно сохранен';
		$unsuccessfulMsg = 'Не удалось сохранить реактив';

		/** @var Reactive $usedModel */
		$usedModel = $this->model('Reactive');

		$updData = $_POST['toSQL'];

		$isUpd = $usedModel->updToSQL($updData);

		if (!$isUpd) {
			$this->showErrorMessage($unsuccessfulMsg);
			$this->redirect('/reactive/list_reactive/');
		}

		$this->showSuccessMessage($successMsg);
		$this->redirect('/reactive/list_reactive/');
	}

	/**
	 *
	 */
    public function addReceiveReactive()
    {
        $successMsg = 'Реактив успешно сохранен';
        $unsuccessfulMsg = 'Не удалось сохранить реактив';
        $usedModel = $this->model('Reactive');

        $newAdd['receive'] = $_POST['receive'];
        $isAdd = $usedModel->addToSQL($newAdd);

        if (!$isAdd) {
            $this->showErrorMessage($unsuccessfulMsg);
            $this->redirect('/reactive/list/');
        }

        $this->showSuccessMessage($successMsg);
        $this->redirect('/reactive/list/');
    }

    /**
	 *
	 */
	public function deleteReceiveReactive()
	{
		/** @var Reactive $usedModel */
		$usedModel = $this->model('Reactive');

		$id = $_POST['id-reactive'];

		$usedModel->deleteReactive($id);

		$this->showErrorMessage('Реактив успешно удален');
		$this->redirect('/reactive/list_reactive/');
	}


    public function getReactiveInfoAjax()
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var Reactive $usedModel */
		$usedModel = $this->model('Reactive');

		$id = $_POST['id'];

		$data = $usedModel->getReactiveInfo($id);

		echo $data;
	}

	public function getBatchControlAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Reactive $usedModel */
        $usedModel = $this->model('Reactive');

        $id = $_POST['id'];

        $usedModel->getBatchControl($id);
    }
}
