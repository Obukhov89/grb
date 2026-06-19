<?php

/**
 * Class StatisticController
 */
class StatisticController extends Controller
{
    public function reportDiffDayProtocol()
    {
        $this->data['title'] = "Дней оформления протокола";

        /** @var Statistic $statisticModel */
        $statisticModel = $this->model('Statistic');

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
        $this->addJS("/assets/js/statistic/report-diff-day.js?v=" . rand());

        $this->view('report_diff');
    }


    /**
     * @desc Страница конструктора отчётов
     */
    public function reportConstructor()
    {
        $this->data['title'] = "Конструктор статистики";

        /** @var Statistic $statisticModel */
        $statisticModel = $this->model('Statistic');

        $this->data['entities'] = $statisticModel->entities;

        $this->data['user'] = $_SESSION['SESS_AUTH']['USER_ID'];

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

        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');

        $this->addCSS("/assets/plugins/morris-chart/morris.css");
        $this->addJs('/assets/plugins/morris-chart/morris.js');
        $this->addJs('/assets/plugins/morris-chart/raphael-min.js');

        $this->addCDN("https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js");
        $this->addCDN("https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation");

        $this->addJS("/assets/js/statistic/report-constructor.js?v=" . rand());

        $this->view('report_constructor');
    }


    /**
     * @desc использование оборудования
     */
    public function reportOborud()
    {
        $this->data['title'] = "Отчет использования оборудования";

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

        $this->addJS("/assets/js/statistic/report-oborud.js");

        $this->view('report_oborud');
    }


    /**
     * @desc отчет лаба метод кол-во стоимость. завершенных работ
     */
    public function reportMethod()
    {
        $this->data['title'] = "Отчет";

        /** @var Lab $labModel */
        $labModel = $this->model('Lab');

        $this->data['lab_list'] = $labModel->getList();


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

        $this->addJS("/assets/js/statistic/report-method.js");

        $this->view('report_method');
    }


    /**
     * @desc Автоматический отчет из ЛИМС
     */
    public function reportFromLims()
    {
        $this->data['title'] = "Автоматический отчет из ЛИМС";

        /** @var Statistic $statisticModel */
        $statisticModel = $this->model('Statistic');
        /** @var Lab $lab */
        $lab = $this->model('Lab');

        $today = date('Y-m-d');
        $weekAgo = date('Y-m-d', strtotime("-1 week"));

        $this->data['lab'] = $lab->getList();

        $this->data['date_start'] = $_POST['date_start']?? $weekAgo;
        $this->data['date_end'] = $_POST['date_end']?? $today;

        $this->data['data'] = $statisticModel->reportFromLims($this->data['date_start'], $this->data['date_end']);
        $this->data['OZ'] = $statisticModel->reportOZ($this->data['date_start'], $this->data['date_end']);

        $this->data['count_tz_new'] = $statisticModel->getCountTzNew($this->data['date_start'], $this->data['date_end']);
        $this->data['count_tz_in_work'] = $statisticModel->getCountTzInWork($this->data['date_start'], $this->data['date_end']);
        $this->data['count_tz_complete'] = $statisticModel->getCountTzComplete($this->data['date_start'], $this->data['date_end']);

        $dates = $statisticModel->reportFromLims($this->data['date_start'], $this->data['date_end'])[2]['tz'];

        $this->data['current_dates'] = $statisticModel->getDatesForStatistic($dates);

        $this->addCDN("https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js");
        $this->addJS('/assets/js/statistic/statistic-chart.js');

        $this->view('report_from_lims');
    }


    /**
     *
     */
    public function chartStat()
    {
        $this->data['title'] = "График";

        /** @var Statistic $statisticModel */
        $statisticModel = $this->model('Statistic');

        $today = date('Y-m-d');
        $weekAgo = date('Y-m-d', strtotime("-1 week"));

        $this->data['date_start'] = $_POST['date_start']?? $weekAgo;
        $this->data['date_end'] = $_POST['date_end']?? $today;

        $this->data['data'] = $statisticModel->reportFromLims($this->data['date_start'], $this->data['date_end'])[2];

        $this->addCDN("https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js");
        $this->addJS('/assets/js/statistic/statistic-chart.js');

        $this->view('chart_request');
    }


    /**
     *
     */
    public function getChartStatAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Statistic $statisticModel */
        $statisticModel = $this->model('Statistic');

        $today = date('Y-m-d');
        $weekAgo = date('Y-m-d', strtotime("-1 week"));

        $this->data['date_start'] = $_POST['date_start'] ?? $weekAgo;
        $this->data['date_end'] = $_POST['date_end'] ?? $today;

        echo json_encode($statisticModel->getTzInWork($this->data['date_start'], $this->data['date_end'], $_POST['lab_id']));
    }


	public function reportFinUser()
	{
		/** @var Request $request */
		$request = $this->model('Request');
		/** @var User $user */
		$user = $this->model('User');
		/** @var Material $material */
		$material = $this->model('Material');
		/** @var Order $order */
		$order = $this->model('Order');
		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');

		$this->data['title'] = "Отчет";

		$this->data['a'] = $statistic->getReportByUsers('2022-9-1', '2022-9-30');

		$this->addJs("/assets/js/statistic.js");

		$this->view('reportFinUser');
	}

	public function headerReport()
	{

//	    header("location: https://ulab.niistrom.pro/ulab/statistic/reportConstructor");
		/** @var Request $request */
		$request = $this->model('Request');
		/** @var User $user */
		$user = $this->model('User');
		/** @var Material $material */
		$material = $this->model('Material');
		/** @var Order $order */
		$order = $this->model('Order');
		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');



		$this->data['title'] = "Отчет за период";

		if (empty($_POST['month'])) {
			$monthReport = '2023-01';//date('Y-m');
		} else {
			$monthReport = $_POST['month'];
		}

		if (strtotime($monthReport) <= 1682881200) {
			$data['Header'] = $statistic->getStatisticHeaderByMonth($monthReport);
			$data['Staff'] = $statistic->getStatisticStaffByMonth($monthReport);
			$data['Finance'] = $statistic->getStatisticFinanceByMonth($monthReport);
			$data['MFC'] = $statistic->getStatisticMFCByMonth($monthReport);
			$data['Year'] = $statistic->getStatisticByYear($monthReport);
			$this->data['protocols'] = $data['Header'];
			$this->data['statistic_date'] = $monthReport;
			$this->data['Staff']['lfhi'] = $data['Staff']['user'][54];
			$this->data['Staff']['dsl'] = $data['Staff']['user'][55];
			$this->data['Staff']['lfmi'] = $data['Staff']['user'][56];
			$this->data['Staff']['lsm'] = $data['Staff']['user'][57];
			$this->data['Staff']['test'] = $data['Staff']['test'];
			$this->data['MFC'] = $data['MFC'];
			$this->data['Finance'] = $data['Finance'];
			$this->data['Year'] = $data['Year'];
			$this->data['test'] = $this->data['protocols'];
		} else {
			$data['Header'] = $statistic->getStatisticHeaderByMonthNew($monthReport);

			$data['Staff'] = $statistic->getStatisticStaffByMonthNew($monthReport);
			$data['Finance'] = $statistic->getStatisticFinanceByMonthNew($monthReport);
			$data['MFC'] = $statistic->getStatisticMFCByMonthNew($monthReport);
			$data['Year'] = $statistic->getStatisticByYearNew($monthReport);
			$this->data['protocols'] = $data['Header'];
			$this->data['statistic_date'] = $monthReport;
			$this->data['Staff']['lfhi'] = $data['Staff']['user'][54];
			$this->data['Staff']['dsl'] = $data['Staff']['user'][55];
			$this->data['Staff']['lfmi'] = $data['Staff']['user'][56];
			$this->data['Staff']['lsm'] = $data['Staff']['user'][57];
			$this->data['Staff']['test'] = $data['Staff']['test'];
			$this->data['MFC'] = $data['MFC'];
			$this->data['Finance'] = $data['Finance'];
			$this->data['Year'] = $data['Year'];
		}


		$this->addCDN("https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js");
		$this->addJs("/assets/js/statistic.js");

		$this->view('headerReport');
	}

	public function headerReportByUser($user_id)
	{
		/** @var Request $request */
		$request = $this->model('Request');
		/** @var User $user */
		$user = $this->model('User');
		/** @var Material $material */
		$material = $this->model('Material');
		/** @var Order $order */
		$order = $this->model('Order');
		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');

		$this->data['title'] = "Отчет за период по сотруднику";
		$date1 = strtotime('2022-11');
		$date2 = strtotime(date('Y-m'));

		for($i = $date1; $i <= $date2; $i = strtotime('+1 month', $i)) {
			$monthReport = date('Y-m', $i);
			$dateRep = date('Ym', strtotime($monthReport));
			$monthRep = date('m', strtotime($monthReport));
			$yearRep = date('Y', strtotime($monthReport));

			$data['Header'] = $statistic->getStatisticHeaderByMonth($monthReport);
			$data['Staff'] = $statistic->getStatisticStaffByMonth($monthReport);
			$data['Finance'] = $statistic->getStatisticFinanceByMonth($monthReport);
			if (!empty($data['Staff']['user'][54][$user_id])) {
				$this->data['Staff'][$dateRep] = $data['Staff']['user'][54][$user_id];
				$this->data['Staff'][$dateRep]['ruDate'] = $yearRep . ' ' . StringHelper::getMonthTitle($monthRep);
			}
			if (!empty($data['Staff']['user'][55][$user_id])) {
				$this->data['Staff'][$dateRep] = $data['Staff']['user'][55][$user_id];
				$this->data['Staff'][$dateRep]['ruDate'] = $yearRep . ' ' . StringHelper::getMonthTitle($monthRep);
			}
			if (!empty($data['Staff']['user'][56][$user_id])) {
				$this->data['Staff'][$dateRep] = $data['Staff']['user'][56][$user_id];
				$this->data['Staff'][$dateRep]['ruDate'] = $yearRep . ' ' . StringHelper::getMonthTitle($monthRep);
			}
			if (!empty($data['Staff']['user'][57][$user_id])) {
				$this->data['Staff'][$dateRep] = $data['Staff']['user'][57][$user_id];
				$this->data['Staff'][$dateRep]['ruDate'] = $yearRep . ' ' . StringHelper::getMonthTitle($monthRep);
			}
		}

		$this->data['User'] = $user->getUserData($user_id);

		$this->addJs("/assets/js/statistic.js");

		$this->view('headerReportByUser');
	}

	/**
	 *
	 */
	public function radiologistsList()
	{
		/** @var Request $request */
		$request = $this->model('Request');
		/** @var User $user */
		$user = $this->model('User');
		/** @var Material $material */
		$material = $this->model('Material');
		/** @var Order $order */
		$order = $this->model('Order');
		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');

		$this->data['title'] = "Журнал радиологии";

		$dir = $_SERVER['DOCUMENT_ROOT'] . '/archiveRadiologyProtocol/';

		$this->data['radiologists_request'] = $statistic->getRadiologyRequest();

		foreach ($statistic->getRadiologyRequestNew() as $item) {
			array_push($this->data['radiologists_request'], $item);
		}
//
//
//		echo '<pre>';
//		print_r($this->data['radiologists_request']);
//		exit();

		$this->addJs("/assets/js/statistic.js");

		$this->view('radiologists_request');

	}

	/**
	 *
	 */
	public function mineralList()
	{
		/** @var Request $request */
		$request = $this->model('Request');
		/** @var User $user */
		$user = $this->model('User');
		/** @var Material $material */
		$material = $this->model('Material');
		/** @var Order $order */
		$order = $this->model('Order');
		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');

		$this->data['title'] = "Журнал минералогии";

		$dir = $_SERVER['DOCUMENT_ROOT'] . '/archiveRadiologyProtocol/';

		$this->data['minerals_request'] = $statistic->getMineralogyRequest();

		$this->addJs("/assets/js/statistic.js");

		$this->view('minerals_request');

	}

	/**
	 *
	 */
	public function getReportByUsersAjax()
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		$response = [];

		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');

		$dateIn = $_POST['dateIn'];
		$dateOut = $_POST['dateOut'];

		$res = $statistic->getReportByUsers($dateIn, $dateOut);

		echo json_encode($res, JSON_UNESCAPED_UNICODE);
	}

	/**
	 *
	 */
	public function uploadFile($id)
	{
		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');

		$dir = $_SERVER['DOCUMENT_ROOT'] . '/archiveRadiologyProtocol/' . $id;

		for ($i = 0; $i < count($_FILES['protocols']['name']); $i++) {
			$filename = $_FILES['protocols']['name'][$i];
			$tmp = $_FILES['protocols']['tmp_name'][$i];

			$pattern = "/\.(pdf)$/i";
			if ( !preg_match($pattern, $filename) ) {
				$this->showErrorMessage('Неверное расширение файла. Загрузите pdf ' . $filename);
				$this->redirect("/statistic/radiologistsList");
			}

			$file = $statistic->saveFile($dir, $filename, $tmp);

			if (!$file) {
				$this->showErrorMessage('Не удалось загрузить файл' . $file['data']);
				$this->redirect("/statistic/radiologistsList");
			}
		}

		$this->showSuccessMessage('Файлы успешно загружены');
		$this->redirect("/statistic/radiologistsList");
	}

	public function setRadiologyDate()
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');

		$id = $_POST['id'];
		$date = $_POST['date'];

		$setDate = $statistic->setRadiologyDate($id, $date);

		if ($setDate == 0) {
			return false;
		}

		$response = $this->response(true);

		echo json_encode($response, JSON_UNESCAPED_UNICODE);

	}

	public function delRadiologyProtocol()
	{
		$response = '';
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');

		$href = $_POST['href'];

		$delete = $statistic->deleteProtocolRadiology($href);

		if ($delete) {
			$response = $this->response(true);
		}

		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}


	/**
	 *
	 */
	public function uploadFileMineralogy($id)
	{
		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');

		$dir = $_SERVER['DOCUMENT_ROOT'] . '/archiveMineralogyProtocol/' . $id;

		for ($i = 0; $i < count($_FILES['protocols']['name']); $i++) {
			$filename = $_FILES['protocols']['name'][$i];
			$tmp = $_FILES['protocols']['tmp_name'][$i];

			$pattern = "/\.(pdf)$/i";
			if ( !preg_match($pattern, $filename) ) {
				$this->showErrorMessage('Неверное расширение файла. Загрузите pdf ' . $filename);
				$this->redirect("/statistic/mineralList");
			}

			$file = $statistic->saveFile($dir, $filename, $tmp);

			if (!$file) {
				$this->showErrorMessage('Не удалось загрузить файл' . $file['data']);
				$this->redirect("/statistic/mineralList");
			}
		}

		$this->showSuccessMessage('Файлы успешно загружены');
		$this->redirect("/statistic/mineralList");
	}


	public function setMineralogyDate()
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');

		$id = $_POST['id'];
		$date = $_POST['date'];

		$setDate = $statistic->setMinerallogyDate($id, $date);

		if ($setDate == 0) {
			return false;
		}

		$response = $this->response(true);

		echo json_encode($response, JSON_UNESCAPED_UNICODE);

	}


	public function delMineralogyProtocol()
	{
		$response = '';
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');

		$href = $_POST['href'];

		$delete = $statistic->deleteProtocolMinerallogy($href);

		if ($delete) {
			$response = $this->response(true);
		}

		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}


	public function getChartAjax()
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var Statistic $statistic */
		$statistic = $this->model('Statistic');


		if (empty($_POST['month'])) {
			$monthReport = date('Y-m');
		} else {
			$monthReport = $_POST['month'];
		}

		$response = $statistic->getStatisticStaffByMonthForChart($monthReport);

		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}


    /**
     * @desc Получение данных для журнала отчет лаба метод кол-во стоимость. завершенных работ
     */
    public function getJournalReportMethodAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Statistic $statisticModel */
        $statisticModel = $this->model('Statistic');


        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'     => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if ( $column['search']['value'] !== '' ) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        if ( !empty($_POST['dateStart']) ) {
            $filter['search']['dateStart'] = date('Y-m-d', strtotime($_POST['dateStart'])) . ' 00:00:00';
            $filter['search']['dateEnd'] = date('Y-m-d', strtotime($_POST['dateEnd'])) . ' 23:59:59';
        }
        if ( !empty($_POST['stage']) ) {
            $filter['search']['stage'] = $_POST['stage'];
        }
        if ( !empty($_POST['lab']) ) {
            $filter['search']['lab'] = $_POST['lab'];
        }

        $data = $statisticModel->getJournalReportMethodList($filter);

        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];
        $sql = $data['sql'];

        unset($data['recordsTotal']);
        unset($data['recordsFiltered']);
        unset($data['sql']);

        $jsonData = [
            "draw" => $_POST['draw'],
            "sql" => $sql,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }


    /**
     * @desc Получение данных для журнала оборудования
     */
    public function getJournalOborudAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Statistic $statisticModel */
        $statisticModel = $this->model('Statistic');


        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'     => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if ( $column['search']['value'] !== '' ) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        if ( !empty($_POST['dateStart']) ) {
            $filter['search']['dateStart'] = date('Y-m-d', strtotime($_POST['dateStart'])) . ' 00:00:00';
            $filter['search']['dateEnd'] = date('Y-m-d', strtotime($_POST['dateEnd'])) . ' 23:59:59';
        }

        $data = $statisticModel->getJournalReportOborudList($filter);

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
     * @desc Получение сущности для конструктора журнала
     */
    public function getStatisticEntityAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Statistic $statisticModel */
        $statisticModel = $this->model('Statistic');

        $result = $statisticModel->getStatisticEntity($_POST['entity'], $_POST['id']);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Получение колонок для конструктора журнала
     */
    public function getStatisticColumnAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Statistic $statisticModel */
        $statisticModel = $this->model('Statistic');

        $result = $statisticModel->getColumnsEntity($_POST['entity'], $_POST['column']);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }


    /**
     * @desc Получение данных для конструктора журнала
     */
    public function getStatisticConstructorJournal()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Statistic $statisticModel */
        $statisticModel = $this->model('Statistic');


        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'     => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if ( $column['search']['value'] !== '' ) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        if ( !empty($_POST['dateStart']) ) {
            $filter['search']['dateStart'] = date('Y-m-d', strtotime($_POST['dateStart'])) . ' 00:00:00';
            $filter['search']['dateEnd'] = date('Y-m-d', strtotime($_POST['dateEnd'])) . ' 23:59:59';
        }

        $filter['entity']['key'] = $_POST['entity'];
        $filter['entity']['column'] = $_POST['column'];

        $data = $statisticModel->getStatisticConstructorJournal($filter);

        $chart = $data['chart'];
        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];

        unset($data['chart']);
        unset($data['recordsTotal']);
        unset($data['recordsFiltered']);

        $jsonData = [
            "chart" => $chart,
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

	public function getStatisticTrialMethod()
	{
		/** @var Statistic $statisticModel */
		$statisticModel = $this->model('Statistic');

		$statisticModel->newFuncTest();
	}

    public function getStatisticEntityForMonthAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Statistic $statisticModel */
        $statisticModel = $this->model('Statistic');

        $result = $statisticModel->getStatisticEntityForMonth($_POST['id'], $_POST['month'], $_POST['entity']);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function tz_status_list()
    {
        $this->data['title'] = 'Статистика по заявкам за 2024 год';

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

        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $r = rand();
        $this->addJs("/assets/js/statistic/tz_status_list.js?v={$r}");

        $this->addCSS("/assets/plugins/stu-sdk-sigcaptx/demoButtons.css");

        $this->addJS("/assets/plugins/stu-sdk-sigcaptx/BigInt.js");
        $this->addJS("/assets/plugins/stu-sdk-sigcaptx/sjcl.js");
        $this->addJS("/assets/plugins/stu-sdk-sigcaptx/demoButtons_encryption.js");
        $this->addJS("/assets/plugins/stu-sdk-sigcaptx/q.js");
        $this->addJS("/assets/plugins/stu-sdk-sigcaptx/wgssStuSdk.js");
        $this->addJS("/assets/plugins/stu-sdk-sigcaptx/sign.js");

        $this->addCSS("/assets/plugins/dropzone/css/basic.css");
        $this->addCSS("/assets/plugins/dropzone/dropzone3.css");
        $this->addJS("/assets/plugins/dropzone/dropzone3.js");

        $this->view('tz_status_list');
    }

    public function getListTzStatuses()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Statistic $statistic*/
        $statistic = $this->model('Statistic');

        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'      => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if ( isset($column['search']['value']) && $column['search']['value'] != '' ) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        if ( !empty($_POST['stageGroup']) ) {
            $filter['search']['stageGroup'] = $_POST['stageGroup'];
        }else {
            $filter['search']['stageGroup'] = 0;
        }

        $data = $statistic->getTzStat($filter);

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


    public function getDiffDayProtocolJournalAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Statistic $statistic*/
        $statistic = $this->model('Statistic');

        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'      => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if ( isset($column['search']['value']) && $column['search']['value'] != '' ) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }



        $data = $statistic->getDiffDayProtocolJournal($filter);

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

    public function reportOzChartAjax()
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var Statistic $statisticModel */
		$statisticModel = $this->model('Statistic');

		echo json_encode($statisticModel->reportOZ($_POST['date_start'], $_POST['date_end']), JSON_UNESCAPED_UNICODE);
	}
}
