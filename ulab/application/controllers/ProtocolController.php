<?php

use  NcJoes\OfficeConverter\OfficeConverter;

/**
 * Контроллер для протоколов
 * Class ProtocolController
 */
Class ProtocolController extends Controller
{
    /**
     * route /protocol/
     */
    public function index()
    {
        $this->redirect('/request/new/');
    }


    /**
     * @param string $protocolId
     */
    public function sig($protocolId = '')
    {
        $this->data['title'] = 'Электронная подпись';

        /** @var  Protocol $protocolModel */
        $protocolModel = $this->model('Protocol');

        $protocolInfo = $protocolModel->getProtocolById($protocolId);

        $this->data['protocol_name'] = $protocolInfo['NUMBER_AND_YEAR'];
        $this->data['protocol_id'] = $protocolId;

        $this->data['today'] = date('d.m.Y');
        $this->data['file_name'] = $protocolInfo['pdf_name'];
        $this->data['user_id'] = $_SESSION['SESS_AUTH']['USER_ID'];
        $this->data['outside_lis'] = $protocolInfo['PROTOCOL_OUTSIDE_LIS'];
        $this->data['outside_lis_path_pdf'] = '';
        if ( $protocolInfo['PROTOCOL_OUTSIDE_LIS'] == 1 ) {
            $this->data['outside_lis_path_pdf'] = $protocolInfo['outside_lis_protocol_path'] . $protocolInfo['pdf_name'];
            $this->data['new_pdf_path'] = $protocolInfo['new_pdf_path'] . $protocolInfo['pdf_name'];

            if ( empty($protocolInfo['pdf_name']) ) {
                $this->data['disable_btn'] = true;
                $this->showErrorMessage("Не прикреплён файл для подписи. Протокол вне ЛИС.");
            }
        }

        $this->data['deal_id'] = $protocolInfo['DEAL_ID'];

        $this->addJS("/assets/plugins/EDS/cadesplugin_api.js");
        $this->addJS("/assets/plugins/EDS/Code.js");

        $this->addJS("/assets/plugins/pdf-lib/pdf-lib-1.4.0.js");

        $this->addJS("/assets/js/protocol-sig.js?v=" . rand());

        $this->view('sig');
    }


    /**
     * route /protocol/list/
     */
    public function list()
    {
        $this->data['title'] = 'Журнал "Регистрация протоколов и отчётов"';

        /** @var Lab $lab */
        $lab = $this->model('Lab');
        /** @var Request $request */
        $request = $this->model('Request');

        $this->data['lab'] = $lab->getList();
        $this->data['date_start'] = $request->getDateStart();

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
        $this->addJS("/assets/plugins/modal/modalWindow.js");

        $r = rand();
        $this->addJs("/assets/js/protocol-list.js?v={$r}");

        $this->view('list');
    }


    /**
     *
     */
    public function getListProcessingAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Protocol $protocol */
        $protocol = $this->model('Protocol');

        $filter = [
            'paginate' => [
                'length'    => $_POST['length'], // кол-во строк на страницу
                'start'     => $_POST['start'],  // текущая страница
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
        if ( !empty($_POST['stage']) ) {
            $filter['search']['stage'] = $_POST['stage'];
        }
        if ( !empty($_POST['lab']) ) {
            $filter['search']['lab'] = $_POST['lab'];
        }
        if ( !empty($_POST['send']) ) {
            $filter['search']['send'] = $_POST['send'];
        }
        if ( !empty($_POST['everywhere']) ) {
            $filter['search']['everywhere'] = $_POST['everywhere'];
        }

        $data = $protocol->getDataToJournal($filter);

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


    public function validateProtocolAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Protocol $protocolModel */
        $protocolModel = $this->model('Protocol');

		if ($_SESSION['SESS_AUTH']['USER_ID'] == 61 || !in_array($_POST['protocol_id'], EXCEPTION_PROTOCOLS) ) {
        	$result = $protocolModel->validateProtocol($_POST['protocol_id']);
		}

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }


    /**
     *
     */
    public function saveSigAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Protocol $protocolModel */
        $protocolModel = $this->model('Protocol');

        $result = $protocolModel->saveSig($_POST['id'], $_POST['file_name'], $_POST['sign']);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }


    /**
     *
     */
    public function sigAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var DocumentGenerator $generatorModel */
        $generatorModel = $this->model('DocumentGenerator');

        $result = $generatorModel->sigProtocol($_POST['protocol_id']);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function getProtocolTypeAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Protocol $protocolModel */
        $protocolModel = $this->model('Protocol');

        $data = $protocolModel->getProtocolType($_POST['protocol_id']);

        echo json_encode(['type_id' => $data], JSON_UNESCAPED_UNICODE);
    }
}
