<?php

/**
 * Пробы
 * Class ProbeController
 */
class ProbeController extends Controller
{
    public function index()
    {
        $this->redirect('/request/list/');
    }


    /**
     * @desc редактирует данные пробы
     */
    public function editProbeInfo()
    {
        /** @var Probe $probeModel */
        $probeModel = $this->model('Probe');

        $dealId = $probeModel->getDealIdByProbe((int)$_POST['id']);

        $probeModel->edit((int)$_POST['id'], $_POST['form']);

        $this->showSuccessMessage("Данные пробы изменены");

        if($dealId == 15554 || $dealId == 16166 || $dealId == 17409) {
            $actId = $probeModel->getActIdByProbeAndDeal($_POST['id'], $dealId);

            $this->redirect("/probe/newCard/{$actId}");
        }else {
            $this->redirect("/probe/card/{$dealId}");
        }
    }


    /**
     * @desc копирует информацию из пробы в пробу
     */
    public function copyProbeInfo()
    {
        /** @var Probe $probeModel */
        $probeModel = $this->model('Probe');

        $probeModel->copyProbeInfo((int)$_POST['probe_id'], (int)$_POST['source_probe_id']);

        $dealId = $probeModel->getDealIdByProbe((int)$_POST['probe_id']);

        $this->showSuccessMessage("Данные пробы скопированы");

        $this->redirect("/probe/card/{$dealId}");
    }


    /**
     * @desc передать пробу
     * @param $umtrId
     */
    public function transferProbe($umtrId)
    {
        /** @var Probe $probeModel */
        $probeModel = $this->model('Probe');

        $probeModel->transferProbe((int)$umtrId);

        $dealId = $probeModel->getDealIdByProbe((int)$umtrId);

        $this->showSuccessMessage("Проба передана");

        $this->redirect("/probe/card/{$dealId}");
    }


    /**
     * @desc принять пробу
     * @param $umtrId
     */
    public function takeProbe($umtrId)
    {
        /** @var Probe $probeModel */
        $probeModel = $this->model('Probe');

        $probeModel->takeProbe((int)$umtrId, $_SESSION['SESS_AUTH']['USER_ID']);

        $dealId = $probeModel->getDealIdByProbe((int)$umtrId);

        $this->showSuccessMessage("Проба принята");

        $this->redirect("/probe/card/{$dealId}");
    }


    /**
     * route /probe/list/
     * @desc список актов приёмки проб
     */
    public function list()
    {
        $this->data['title'] = 'Журнал приёмки проб';

        /** @var Lab $lab */
        $lab = $this->model('Lab');
        /** @var Request $request */
        $request = $this->model('Request');

        $this->data['date_start'] = $request->getDateStart();

        $this->data['lab'] = $lab->getList();

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

        $r = rand();
        $this->addJs("/assets/js/probe-list.js?v={$r}");

        $this->view('list');
    }

	/**
	 * route /probe/listResearcher/
	 * список актов приёмки проб. для лаборантов
	 */
	public function listResearcher()
	{
		$this->data['title'] = 'Журнал приёмки проб';

		/** @var Lab $lab */
		$lab = $this->model('Lab');
		/** @var Request $request */
		$request = $this->model('Request');

		$this->data['date_start'] = $request->getDateStart();

		$this->data['lab'] = $lab->getList();

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

		$r = rand();
		$this->addJs("/assets/js/probe-list-lab.js?v={$r}");

        $this->view('list_lab');
	}


	/**
	 * @param $dealId
	 */
	public function card( $dealId )
	{
		$dealId = (int) $dealId;

		/** @var User $user */
		$user = $this->model('User');
		/** @var Requirement $requirement */
		$requirement = $this->model('Requirement');
		/** @var Probe $probeModel */
		$probeModel = $this->model('Probe');
		/** @var Material $materialModel */
		$materialModel = $this->model('Material');


		$this->data['title'] = "Карточка пробы";

		$this->data['material_probe'] = $materialModel->getMaterialProbeToRequest($dealId);

//        echo "<pre>";
//        print_r($this->data['material_probe']);
//        echo "</pre>";
//        die();

        $this->data['probe_in_act'] = $probeModel->getProbeInAct($dealId);
        $this->data['probe_in_act2'] = $probeModel->getProbeInAct2($dealId);
        $this->data['quarry'] = $requirement->getQuarry();

		$currUser = $user->getCurrentUser();


		$tzId = $requirement->getTzIdByDealId($dealId);
		$tzData = $requirement->getTzByTzId((int)$tzId);
        $actBase = $requirement->getActBase($dealId);

		$this->data['deal_id']  = $dealId;
		$this->data['tz_id']    = $tzId;
		$this->data['deal_title'] = $tzData['REQUEST_TITLE'];
		$this->data['selection_type'] = $tzData['SELECTION_TYPE'];
        $this->data['act_number'] = $actBase['ACT_NUM'];
        $this->data['act_date'] = $actBase['date_ru'];

		$probe = $probeModel->getProbeByDealId($dealId);

		// отображаем кнопку Передать если менеджер
        $this->data['is_hand_over'] = in_array(GROUP_MANAGER_ID, $currUser['groups']);
        $this->data['user_id'] = $_SESSION['SESS_AUTH']['USER_ID'];

		$this->data['probe'] = $probe;
		$this->data['test'] = $probe;

        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");
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
        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');

        $r = rand();
        $this->addJs("/assets/js/probe-card.js?v={$r}");

		$this->view('card');
	}

	/**
	 * @param $dealId
	 */
	public function newCard( $actId )
	{
		$actId = (int) $actId;

		/** @var User $user */
		$user = $this->model('User');
		/** @var Requirement $requirement */
		$requirement = $this->model('Requirement');
		/** @var Probe $probeModel */
		$probeModel = $this->model('Probe');
		/** @var Material $materialModel */
		$materialModel = $this->model('Material');


		$this->data['title'] = "Карточка пробы";

        $this->data['quarry'] = $requirement->getQuarry();

		$currUser = $user->getCurrentUser();


		$tzId = $requirement->getTzIdByActId($actId);
		$dealId = $requirement->getDealIdByActId($actId);
		$tzData = $requirement->getTzByTzId((int)$tzId);
        $actBase = $requirement->getActBaseWithActId($actId);

		$this->data['deal_id']  = $dealId;
        $this->data['act_id']   = $actId;
		$this->data['tz_id']    = $tzId;
		$this->data['deal_title'] = $tzData['REQUEST_TITLE'];
		$this->data['selection_type'] = $tzData['SELECTION_TYPE'];
        $this->data['act_number'] = $actBase['ACT_NUM'];
        $this->data['act_date'] = $actBase['date_ru'];

        $this->data['material_probe'] = $materialModel->getMaterialProbeToRequestWithActNum($dealId, $actBase['ACT_NUM']);

        $this->data['probe_in_act'] = $probeModel->getProbeInAct($dealId);

		$probe = $probeModel->getProbeByDealId($dealId);

		// отображаем кнопку Передать если менеджер
        $this->data['is_hand_over'] = in_array(GROUP_MANAGER_ID, $currUser['groups']);
        $this->data['user_id'] = $_SESSION['SESS_AUTH']['USER_ID'];

		$this->data['probe'] = $probe;
		$this->data['test'] = $probe;

        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");
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
        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');

        $r = rand();
        $this->addJs("/assets/js/probe-card.js?v={$r}");

		$this->view('card');
	}


	/**
	 * @param $dealId
	 */
	public function new( $dealId )
	{
		$dealId = (int) $dealId;

		/** @var Request $request */
		$request = $this->model('Request');
		/** @var User $user */
		$user = $this->model('User');
		/** @var Requirement $requirement */
		$requirement = $this->model('Requirement');
		/** @var Probe $probeModel */
		$probeModel = $this->model('Probe');
		/** @var Material $materialModel */
		$materialModel = $this->model('Material');
		/** @var Quarry $quarryModel */
		$quarryModel = $this->model('Quarry');

		$this->data['title'] = "Создание акта приемка-передачи (образцов)";

		$tzId = $requirement->getTzIdByDealId($dealId);
		$deal = $request->getDealById($dealId);
		$tzData = $requirement->getTzByTzId((int)$tzId);

		$this->data['deal_id']  = $dealId;
		$this->data['tz_id']    = $tzId;
		$this->data['deal_title'] = $tzData['REQUEST_TITLE'];
		$this->data['new_act_number'] = $probeModel->getNewActNumber();
		$this->data['company_name'] = $deal['COMPANY_TITLE'];

		// Карьеры
		$this->data['quarry_list'] = $quarryModel->getList();

		//Пробу принял
		$this->data['current_user'] = $user->getUserShortById($_SESSION['SESS_AUTH']['USER_ID']);

		//Договор
		$contractData = $requirement->getContractByDealId($dealId);
		$this->data['contract_number'] = $contractData['NUMBER'] ?? '';
		$this->data['contract_date'] = $contractData['DATE'] ?? '';
		$this->data['contract_type'] = $contractData['CONTRACT_TYPE'] ?? 'Договор';


		$probe = $probeModel->getProbeByDealId($dealId);

		$this->data['probe'] = $probe;
		$this->data['test'] = $probe;

		$this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
		$this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");
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
		$this->addJs('/assets/plugins/select2/dist/js/select2.min.js');

		$this->addJs('/assets/js/probe_form.js');

		$this->view('form');
	}


    /**
     *
     */
    public function getListAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Request $request*/
        $request = $this->model('Request');

        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'      => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if ( !empty($column['search']['value']) ) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }
        if ( !empty($_POST['dateStart']) ) {
            $filter['search']['dateStart'] = date('Y-m-d', strtotime($_POST['dateStart']));
            $filter['search']['dateEnd'] = date('Y-m-d', strtotime($_POST['dateEnd']));
        }
        if ( !empty($_POST['lab']) ) {
            $filter['search']['lab'] = $_POST['lab'];
        }
        if ( !empty($_POST['stageGroup']) ) {
            $filter['search']['stage'] = $_POST['stageGroup'];
        }
        if ( !empty($_POST['selectIrrevocable']) ) {
            $filter['search']['selectIrrevocable'] = $_POST['selectIrrevocable'];
        }
        if ( !empty($_POST['selectAcceptProbe']) ) {
            $filter['search']['selectAcceptProbe'] = $_POST['selectAcceptProbe'];
        }

        $data = $request->getDatatoJournalActProbe($filter);

        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];

        unset($data['recordsTotal']);
        unset($data['recordsFiltered']);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }


    /**
     * @desc Создание нового акта приёмки проб
     */
    public function insertUpdateActProbe($dealId)
    {
        /** @var  Probe $probeModel */
        $probeModel = $this->model('Probe');

        $probeModel->insertUpdateActProbe($_POST['act']);

        $this->showSuccessMessage('Акт сформирован');
        $this->redirect("/request/card/{$dealId}");
    }

    public function saveActChanges()
    {
        $dealId = (int)($_POST['deal_id'] ?? 0);
        $this->data['user_id'] = $_SESSION['SESS_AUTH']['USER_ID'];
        if($_SESSION['SESS_AUTH']['USER_ID'] != 212 && $_SESSION['SESS_AUTH']['USER_ID'] != 220
            && $_SESSION['SESS_AUTH']['USER_ID'] != 234 && $_SESSION['SESS_AUTH']['USER_ID'] != 83
            && $_SESSION['SESS_AUTH']['USER_ID'] != 61) {
            $this->showErrorMessage("Запрещено");
            $this->redirect("/probe/card/{$dealId}");
        }
        /** @var Probe $probeModel */
        $probeModel = $this->model('Probe');

        $rows = $_POST['probe'] ?? []; // probe[umtr_id][cipher|date_probe|place|quarry_id]

        $count = $probeModel->saveActProbeChanges($dealId, $rows);

        $this->showSuccessMessage("Изменения сохранены (обновлено: {$count})");
        $this->redirect("/probe/card/{$dealId}");
    }

	/**
	 * @desc Создание нового акта приёмки проб
	 */
	public function insertUpdateActProbeNew($dealId)
	{
		/** @var  Probe $probeModel */
		$probeModel = $this->model('Probe');
        /** @var Status $statusModel */
        $statusModel = $this->model('Status');

        if(count($_POST['act']['probe']['umtr_id']) <= 0) {
            $this->showErrorMessage('Акт не сформирован, выберите хотябы одну пробу!');
            $this->redirect("/probe/new/{$dealId}");
        }

        $probeModel->insertUpdateActProbeNew($_POST['act']);

		$this->showSuccessMessage('Акт сформирован');
		$this->redirect("/request/card/{$dealId}");
	}


	public function acceptProbeAjax()
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var  Probe $probeModel */
		$probeModel = $this->model('Probe');
		/** @var  Status $statusModel */
		$statusModel = $this->model('Status');
		/** @var  Request $requestModel */
        $requestModel = $this->model('Request');

		$idReq = (int)$_POST['id'];
        $dealId = '';

        echo json_encode(['result' => $_POST], JSON_UNESCAPED_UNICODE);

		if ($_POST['arr']) {
            $dealId = $idReq;

			foreach ($_POST['arr'] as $idUmtr) {
				$probeModel->acceptProbe($idUmtr);
			}
		} else {
            $dealId = $requestModel->getDealIdByProbeToLab($idReq);

			$probeModel->acceptProbe($idReq);
		}

        $data =
            [
                'user_id' => $_SESSION['SESS_AUTH']['USER_ID'],
                'status_id' => 3,
                'date' => date('Y-m-d H:i:s')
            ];

        $statusModel->setNewTzStatus($dealId, $data);

		echo json_encode(['accept'=>'true'], JSON_UNESCAPED_UNICODE);
	}


	public function removeAcceptProbeAjax()
	{

		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var  Probe $probeModel */
		$probeModel = $this->model('Probe');

		$idReq = (int)$_POST['id'];

		if ($_POST['arr']) {
			foreach ($_POST['arr'] as $idUmtr) {
				$probeModel->removeAcceptProbe($idUmtr);
			}
		} else {
			$probeModel->removeAcceptProbe($idReq);
		}


		echo json_encode(['accept'=>'true'], JSON_UNESCAPED_UNICODE);
	}


	public function test123()
	{
		$id = 9385;
		/** @var  Probe $probeModel */
		$probeModel = $this->model('Probe');

		$probeModel->getLabHeaderByUmtrId(32194);

	}


	public function getAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Probe $probeModel */
        $probeModel = $this->model('Probe');

        $data = $probeModel->get($_POST['id']);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    /**
     * @desc Получает историю пробы
     */
    public function getHistoryAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Probe $probeModel */
        $probeModel = $this->model('Probe');

        $data['info'] = $probeModel->get($_POST['id']);
        $data['history'] = $probeModel->getHistory($_POST['id']);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function changeSelectionTypeAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Probe $probeModel */
        $probeModel = $this->model('Probe');

        $id = $_POST['id'];
        $prop = $_POST['checked'];

        $probeModel->setSelectionType($id, $prop);
    }


    /**
     *
     */
    public function getMaterialProbeToRequestAjax()
	{
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Probe $probeModel */
        $probeModel = $this->model('Probe');

        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'      => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if ( !empty($column['search']['value']) ) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }
        if ( !empty($_POST['deal_id']) ) {
            $filter['search']['deal_id'] = $_POST['deal_id'];
        }

        $data = $probeModel->getProbeJournal($filter);

        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];

        unset($data['recordsTotal']);
        unset($data['recordsFiltered']);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
	}

    public function getMaterialProbeToRequestAjax2()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Probe $probeModel */
        $probeModel = $this->model('Probe');

        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'      => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if ( !empty($column['search']['value']) ) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }
        if ( !empty($_POST['deal_id']) ) {
            $filter['search']['deal_id'] = $_POST['deal_id'];
        }

        $data = $probeModel->getProbeJournal2($filter);

        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];

        unset($data['recordsTotal']);
        unset($data['recordsFiltered']);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }


    /**
     *
     */
    public function updateProbeAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Probe $probeModel */
        $probeModel = $this->model('Probe');
        if ($_POST['field'] != 'irrevocable') {
            $probeModel->pre($_POST);
        }
        $data[$_POST['field']] = $_POST['val'];

        $probeModel->updateProbeData($_POST['id'], $data);
    }

    /**
     *
     */
    public function sendProbe($probeId)
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Probe $probeModel */
        $probeModel = $this->model('Probe');

        $probeModel->sendProbe($probeId, $_POST['lab_id']);

        $this->showSuccessMessage('Проба успешно отмечена успешной');
    }
}
