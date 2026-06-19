<?php

/**
 * Договор контроллер
 * Class OrderController
 */
Class OrderController extends Controller
{
    /**
     * route /order/
     */
    public function index()
    {
        $this->redirect('/request/new/');
    }


    /**
     * route /order/newList/
     */
    public function list()
    {
        $this->data['title'] = 'Журнал договоров';

        /** @var Request $request */
        $request = $this->model('Request');
        /** @var Order $order */
        $order = $this->model('Order');

        $this->data['date_start'] = $request->getDateStart();
        $this->data['order_types'] = $order->getOrderTypes();

        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');

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

        $this->addJs('/assets/js/order-list.js');
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $this->view('list');
    }


    /**
     * route /order/newList/
     */
    public function newList()
    {
        $this->data['title'] = 'Журнал договоров';

        /** @var Request $request */
        $request = $this->model('Request');
        /** @var Order $order */
        $order = $this->model('Order');

        $this->data['date_start'] = $request->getDateStart();
        $this->data['order_types'] = $order->getOrderTypes();
        $this->data['payment_types'] = $order->getPaymentTypes();
        $this->data['dogovor_belonging'] = $order->getBelongingList();

        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/fixedColumns/css/fixedColumns.dataTables.min.css");

        $this->addJS("/assets/plugins/DataTables/DataTables-1.11.3/js/jquery.dataTables.min.js");
        $this->addJS("/assets/plugins/DataTables/ColReorder-1.5.5/js/dataTables.colReorder.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/dataTables.buttons.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.colVis.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.print.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.html5.min.js");
        $this->addJS("/assets/plugins/DataTables/JSZip-2.5.0/jszip.min.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/ellipsis.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/intl.js");
        $this->addJS("/assets/plugins/DataTables/fixedColumns/js/dataTables.fixedColumns.min.js");
        $this->addJS("/assets/plugins/DataTables/FixedHeader-3.2.0/js/dataTables.fixedHeader.min.js");
        $this->addJS("/assets/plugins/modal/modalWindow.js");



        $this->addJs('/assets/js/order-list-new.js');
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $this->view('newList');
    }


    /**
     *
     */
    public function getListProcessingAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $order */
        $order = $this->model('Order');

        $filter = [
            'paginate' => [
                'length'    => $_POST['length'], // кол-во строк на страницу
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

//        $order->pre($_POST, true);

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            if($_POST['order'][0]['column'] == 1) {
                $filter['order']['by'] = 'ID';
            }else {
                $filter['order']['by'] = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            }
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        if($_POST['need_date_end']) {
            $filter['search']['need_date_end'] = $_POST['need_date_end'];
        }

        if($_POST['need_note']) {
            $filter['search']['need_note'] = $_POST['need_note'];
        }

        if($_POST['originals']) {
            $filter['search']['originals'] = $_POST['originals'];
        }

        if ( !empty($_POST['dateStart']) ) {
            $filter['search']['dateStart'] = date('Y-m-d', strtotime($_POST['dateStart'])) . ' 00:00:00';
            $filter['search']['dateEnd'] = date('Y-m-d', strtotime($_POST['dateEnd'])) . ' 23:59:59';
        }
        if ( !empty($_POST['everywhere']) ) {
            $filter['search']['everywhere'] = $_POST['everywhere'];
        }
//        $order->pre($filter, true);
        $data = $order->getDataToJournalNew($filter);

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

    public function card($contractID)
    {
        if (empty($contractID)) {
            $this->redirect('/request/list/');
        }

        $contractID = (int) $contractID;

        /** @var Request $request */
        $request = $this->model('Request');
        /** @var Company $company */
        $company = $this->model('Company');
        /** @var Order $order */
        $order = $this->model('Order');

        $this->data['title'] = "Карточка договора";

        $contract = $order->getContractById($contractID);
        $requisite = $company->getRequisiteByCompanyId($contract['CLIENT_ID']);
        $company = $company->getById($contract['CLIENT_ID']);
        $requestByOrder = $order->getDealToContractByContractId($contractID);
        $paymentOnContract = $order->getPaymentOnContract($contractID);
        $dealInfo = $request->getDealIdByTzId((int)$contract['TZ_ID']);

        $this->data['payment_types'] = $order->getPaymentTypes();

        $totalCost = 0;
        $SumPayment = 0;
        foreach ($requestByOrder as $item) {
            $SumPayment += $item['OPLATA'];
//            if ($item['status'] == 'work') {
            if ($item['ID_DEAL'] == $contract['head_request'] || $item['status'] == 'lose') continue;

            $totalCost += $item['price_discount'];
//            }
        }

        $contractRequestCoast = 0;

        foreach ($requestByOrder as $item) {
            if ($item['ID_DEAL'] == $contract['head_request'] || $item['status'] == 'lose') continue;
            $contractRequestCoast += $request->getTzByDealId($item['ID_DEAL'])['price_discount'];
        }

        $this->data['documents'] = $order->getDocsToDogovor($contractID);

//        $order->pre($this->data['documents']);

        $this->data['history'] = $order->getFinanceHistory($contractID);
        $this->data['dogovor_belonging'] = $order->getBelongingList();

        $this->data['order_types'] = $order->getOrderTypes();

        $this->data['order']['belonging_id'] = $contract['belonging_id'];
        $this->data['order']['additional_count'] = $contract['additional_count'];
        $this->data['order']['dogovor_type_id'] = $contract['dogovor_type_id'];
        $this->data['order']['custom_number'] = $contract['custom_number'];
        $this->data['order']['dogovor_type_id'] = $contract['dogovor_type_id'];
        $this->data['order']['need_additional'] = $contract['need_additional'];
        $this->data['order']['need_protocol'] = $contract['need_protocol'];
        $this->data['order']['additional_count'] = $contract['additional_count'];
        $this->data['order']['special_notes'] = $contract['special_notes'];
        $this->data['order']['number_client'] = $contract['number_client'];
        $this->data['order']['dogovor_price'] = $contract['dogovor_price'];
        $this->data['order']['date_end'] = $contract['date_end'];
        $this->data['order']['company'] = $company['TITLE'];
        $this->data['order']['number'] = $contract['NUMBER'];
        $this->data['order']['type'] = $contract['CONTRACT_TYPE'];
        $this->data['order']['pdf'] = $contract['PDF'];
        $this->data['order']['actual_ver'] = $contract['ACTUAL_VER'];
        $this->data['order']['id'] = $contract['ID'];
        $this->data['order']['action'] = $contract['IS_ACTION'];
        $this->data['order']['longterm'] = $contract['LONGTERM'];
        $this->data['order']['head_request'] = $contract['head_request'];
        $this->data['order']['client_number'] = $contract['CLIENT_NUMBER'];
        $this->data['order']['flow_date'] = $contract['FLOW_DATE'];
        $this->data['order']['summ'] = $contract['SUMM'];
        $this->data['order']['finance'] = $contract['finance'];
        $this->data['order']['date'] = date('Y-m-d', strtotime($contract['DATE']));
        $this->data['client']['phone'] = $requisite['RQ_PHONE'];
        $this->data['client']['email'] = $requisite['RQ_EMAIL'];
        $this->data['client']['contact'] = $requisite['RQ_NAME'];
        $this->data['client']['price'] = $order->getClientPrice($contractID);
        $this->data['request'] = $requestByOrder;
        $this->data['order']['payment']['sum'] = $paymentOnContract['data'];
        $this->data['Cost'] = StringHelper::priceFormatRus($totalCost);
        $this->data['cost_contract'] = StringHelper::priceFormatRus($contractRequestCoast);
        $this->data['debt_contract'] = StringHelper::priceFormatRus($contract['SUMM'] - $contractRequestCoast);
        $this->data['Payment'] = StringHelper::priceFormatRus($paymentOnContract['paymentSum']);
        $this->data['Debt_Dog'] = StringHelper::priceFormatRus($contract['SUMM'] - $paymentOnContract['paymentSum']);
        $this->data['Debt_Dog_modal'] = $contract['SUMM'] - $paymentOnContract['paymentSum'];
        $this->data['Debt'] = StringHelper::priceFormatRus($totalCost - $SumPayment);
        $this->data['dealID'] = $dealInfo;
        $this->data['tz_id'] = $contract['TZ_ID'];
        $this->data['payment_type'] = $contract['payment_id'];

        $this->data['company']['id']               = htmlentities($contract['CLIENT_ID']);
        $this->data['company']['TITLE']               = htmlentities($requisite['NAME']);
        $this->data['company']['INN']               = htmlentities($requisite['RQ_INN']);
        $this->data['company']['OGRNIP']            = htmlentities($requisite['RQ_OGRNIP']);
        $this->data['company']['OGRN']              = htmlentities($requisite['RQ_OGRN']);
        $this->data['company']['ADDR']              = htmlentities($requisite['RQ_ACCOUNTANT']);
        $this->data['company']['ACTUAL_ADDRESS']    = htmlentities($requisite['address'][1]['ADDRESS_1']);
        $this->data['company']['mailingAddress']    = htmlentities($requisite['RQ_COMPANY_NAME']);
        $this->data['company']['EMAIL']             = htmlentities($requisite['RQ_FIRST_NAME']);
        $this->data['company']['POST_MAIL']         = htmlentities($requisite['RQ_EMAIL']);
        $this->data['company']['PHONE']             = htmlentities($requisite['RQ_PHONE']);
        $this->data['company']['CONTACT']           = htmlentities($requisite['RQ_NAME']);
        $this->data['company']['KPP']               = htmlentities($requisite['RQ_KPP']);
        $this->data['company']['Position2']         = htmlentities($requisite['RQ_COMPANY_REG_DATE']);
        $this->data['company']['DirectorFIO']       = htmlentities($requisite['RQ_DIRECTOR']);
        $this->data['company']['RaschSchet']        = htmlentities($requisite['RQ_ACC_NUM']);
        $this->data['company']['KSchet']            = htmlentities($requisite['RQ_COR_ACC_NUM']);
        $this->data['company']['l_schet']           = htmlentities($requisite['COMMENTS']);
        $this->data['company']['BankName']          = htmlentities($requisite['RQ_BANK_NAME']);
        $this->data['company']['CompanyFullName']   = htmlentities($requisite['RQ_COMPANY_FULL_NAME']);
        $this->data['company']['BIK']               = htmlentities($requisite['RQ_BIK']);
        $this->data['company']['ACTS_BASIS'] = !empty($requisite['address'][1]['ADDRESS_2'])
            ? htmlentities($requisite['address'][1]['ADDRESS_2']) : null;

        $this->data['is_show_finance'] = in_array($_SESSION["SESS_AUTH"]["USER_ID"], [88, 25]);

        $this->data['user_id'] = $_SESSION['SESS_AUTH']['USER_ID'];

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

        $r = rand();
        $this->addJs("/assets/js/order.js?v={$r}");

        $this->view('card');
    }


    public function getJournalRequestAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');


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

        if ( !empty($_POST['order_id']) ) {
            $filter['search']['order_id'] = $_POST['order_id'];
        }
        if ( $_POST['stage'] !== '' ) {
            $filter['search']['stage'] = $_POST['stage'];
        }

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        $data = $orderModel->getDataJournalRequest($filter);

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
     *  оплатить заявку из счета
     */
    public function addPayment()
    {
        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $result = $orderModel->addPay((int) $_POST['orderId'], (int) $_POST['deal_id'], (float) $_POST['money']);

        if ( !$result['success'] ) {
            $this->showErrorMessage($result['error']);
        } else {
            $this->showSuccessMessage("Заявка оплачена на сумму: {$_POST['money']}");
        }

        $this->redirect('/order/card/' . $_POST['orderId']);
    }


    /**
     * добавить денег на счет
     */
    public function addFinance()
    {
        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $orderModel->addFinance((int) $_POST['orderId'], (float) $_POST['money']);

        $this->showSuccessMessage("На счет добавлена сумма: {$_POST['money']}");

        $this->redirect('/order/card/' . $_POST['orderId']);
    }


    public function changeOrder()
    {
        /** @var Request $request */
        $request = $this->model('Request');
        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $orderID = $_POST['orderID'];



        $data['NUMBER'] = $_POST["lims_number"];
        $data['DATE'] = $_POST['DATE'];
        $data['belonging_id'] = $_POST['belonging_id'];

        $data['CONTRACT_TYPE'] = $_POST['CONTRACT_TYPE'];
        $data['custom_number'] = $_POST['custom_number'] == 'on' ? 1 : 0;

        $data['dogovor_type_id'] = $_POST['dogovor_type_id'];
        $data['type_id'] = $_POST['dogovor_type_id'];

        if($data['custom_number']) {
            $data['number_client'] = $_POST['NUMBER'];
        }

        $orderModel->setOrderById($orderID, $data);
        $location = '/order/card/' . $orderID;
        $this->redirect($location);
    }

    public function creatOrderPrice($orderID)
    {
        /** @var Order $order */
        $order = $this->model('Order');

        $location = '/order/card/' . $orderID;

        $res = $order->setClientPrice($orderID);

        if ($res) {
            $this->showSuccessMessage('Прайс успешно добавлен');
            $this->redirect($location);
        }
    }

    public function setOplata()
    {
        /** @var Order $order */
        $order = $this->model('Order');

        $orderID = $_POST['order_id'];

        $location = '/ulab/order/card/' . $orderID;

        $data = [
            'ID_CONTRACT' => $orderID,
            'PAY_SUMM' => $_POST['pay'],
            'PAY_DATE' => $_POST['payDate'],
        ];

        $result = $order->setOplata($data);

        if ($result) {
            $this->showSuccessMessage('Оплата успешно добавлена');
            $this->redirect($location);
        } else {
            $this->showErrorMessage('Не удалось добавить оплату');
            $this->redirect($location);
        }
    }


    /**
     * @desc Загрузить pdf ТЗ
     */
    public function uploadTzDocPdfAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        if ( !empty($_POST['tz_id']) ) {
            $resultUpload = $orderModel->uploadTzDocPdf($_FILES["file"], $_POST['tz_id']);
        }

        if ( !empty($_POST['dogovor_id']) ) {
            $resultUpload = $orderModel->uploadPdf($_FILES["file"]);
        }

        if ( $resultUpload['success'] ) {
            if ( !empty($_POST['doc_id']) ) {
                $orderModel->saveTzDocPdf($resultUpload['data'], $_POST['doc_id']);
            }
            if ( !empty($_POST['dogovor_id']) ) {
                $orderModel->saveDogovorPdf($resultUpload['data'], $_POST['dogovor_id']);
            }

            $this->showSuccessMessage("Файл загружен");
        } else {
            $this->showErrorMessage($resultUpload['error']);
        }

        echo json_encode(['success' => $resultUpload], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Загрузить pdf ТЗ
     */
    public function uploadDogovorDocs()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        if ( !empty($_POST['dogovor_id']) ) {
            $resultUpload = $orderModel->uploadDogovorDoc($_FILES["file"], $_POST['type_id'], $_POST['dogovor_id']);
        }

        if ( $resultUpload['success'] ) {
            if ( !empty($_POST['dogovor_id']) ) {
                $orderModel->saveDocToDogovor($resultUpload['data'], $_POST);
            }

            $this->showSuccessMessage("Файл загружен");
        } else {
            $this->showErrorMessage($resultUpload['error']);
        }

        echo json_encode(['success' => $resultUpload], JSON_UNESCAPED_UNICODE);
    }

    public function cancelOrder($orderId)
    {
        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $orderModel->orderCancelByOrderId($orderId);

        $result = $orderModel->getContractById($orderId)['IS_ACTION'];

        $location = '/ulab/order/card/' . $orderId;

        if ($result) {
            $this->showSuccessMessage("Договор восстановлен");

        } else {
            $this->showErrorMessage("Договор аннулирован");
        }
        $this->redirect($location);
    }

    public function delTzDocAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $id = $_POST['id'];

        $result = $orderModel->deletePdfTZ($id);

        if ($result) {
            $this->showSuccessMessage("Файл успешно удален");
        } else {
            $this->showErrorMessage("Файл не удален");
        }

        echo json_encode(['succes' => $result], JSON_UNESCAPED_UNICODE);
    }

    public function untieOrder($orderId)
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $orderModel->untieOrder($orderId);

        $location = '/ulab/order/card/' . $orderId;

        $this->showErrorMessage("PDF Версия успешно отвязана");

        $this->redirect($location);
    }

    public function getAllClientsAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Company $company */
        $company = $this->model('Company');

        $companies = $company->getList();

        echo json_encode($companies, JSON_UNESCAPED_UNICODE);
    }

    public function getNextDogovorNumberAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $result =
            [
                'number' => $orderModel->getCurrentNumber()
            ];

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function createNewDogovor()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $orderModel->createNewDogovor($_POST['form']);

        $this->redirect('/ulab/order/newList/');
    }

    public function setPaymentTypeAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $data =
            [
                'dogovor_id' => $_POST['id'],
                'payment_id' => $_POST['payment_id']
            ];

        $orderModel->setPaymentType($data);
    }

    public function setDogovorPrice()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $data =
            [
                'dogovor_id' => $_POST['id'],
                'dogovor_price' => $_POST['dogovor_price']
            ];

        $orderModel->setDogovorPrice($data);
    }

    public function setDogovorEnd()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $data =
            [
                'dogovor_id' => $_POST['id'],
                'date_end' => $_POST['date_end']
            ];

        $orderModel->setDogovorDate($data);
    }

    public function setSpecialNotes()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $data =
            [
                'dogovor_id' => $_POST['id'],
                'special_notes' => $_POST['special_notes']
            ];

        $orderModel->setSpecialNotes($data);
    }

    public function removeDoc()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $orderModel->removeDocId($_POST['id']);
    }

    public function changeNeedlDocForDogovor()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $orderModel->changeNeedlDoc($_POST['type_id'], $_POST['new_value'], $_POST['dogovor_id']);
    }

    public function sendDocToClient()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $orderModel->sendToClient($_POST);
    }

    public function deleteSendedDocs()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $orderModel->deleteSended($_POST['document_id']);
    }

    public function deleteScanDogovor()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        $orderModel->deleteScanDogovor($_POST['dogovor_id']);

        return json_encode(['message' => 'Success!'], JSON_UNESCAPED_UNICODE);
    }

    public function updateClientData()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Order $orderModel */
        $orderModel = $this->model('Order');

        //$orderModel->pre($_POST, true);

        $orderModel->updateClientData($_POST);

        $this->redirect("/ulab/order/card/{$_POST['orderID']}");
    }
}
