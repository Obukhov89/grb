<?php


/**
 * Страница ВЛК
 * Class VlkController
 */
class VlkController extends Controller
{
    /**
     * route /vlk/
     */
    public function index()
    {
        $this->redirect('/vlk/list/');
    }

    /**
     * @desc Журнал методик и образцов контроля с метрологическими характеристиками
     */
    public function methodComponentList()
    {
        $this->data['title'] = 'Журнал методик и образцов контроля с метрологическими характеристиками';

        /** @var  Lab $lab*/
        $lab = $this->model('Lab');
        /** @var Methods $methodsModel */
        $methodsModel = $this->model('Methods');
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $this->data['lab'] = $lab->getLabaRoom();
        $this->data['methods'] = $methodsModel->getList();
        $this->data['components'] = $oborudModel->getValidComponents();

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");

        $this->addJS("/assets/plugins/DataTables/DataTables-1.11.3/js/jquery.dataTables.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/dataTables.buttons.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.colVis.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.print.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.html5.min.js");
        $this->addJS("/assets/plugins/DataTables/JSZip-2.5.0/jszip.min.js");

        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $r = rand();
        $this->addJs("/assets/js/vlk/method-component.js?v={$r}");

        $this->view('method_component');
    }

    /**
     * @desc Получение данных для журнала методов и образцов контроля с метрологическими характеристиками Ajax запросом
     */
    public function getMethodListAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');


        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'      => $_POST['start'],  // текущая страница
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

        if ( !empty($_POST['stage']) ) {
            $filter['search']['stage'] = $_POST['stage'];
        }

        if ( !empty($_POST['lab']) ) {
            $filter['search']['lab'] = $_POST['lab'];
        }

        $data = $vlkModel->getMethodList($filter);

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
     * @desc Получение данных образцов контроля c метрологической характеристикой для методики Ajax запросом
     */
    public function getComponentsByMethodAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');

        $components = $vlkModel->getComponentsByMethod((int)$_POST['method_id']);

        echo json_encode($components, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Создание связи методики и метрологической характеристики
     */
    public function insertMethodComponent()
    {
        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');

        $data = $_POST['form'] ?? [];

        $location = "/vlk/methodComponentList/";

        // Валидация
        $validMethodId = $this->validateNumber($data['method_id'], 'Методика', true);
        if (!$validMethodId['success']) {
            $this->showErrorMessage($validMethodId['error']);
            $this->redirect($location);
        }

        $validComponentId = $this->validateNumber($data['component_id'], 'Образец контроля с метрологической характеристикой', true);
        if (!$validComponentId['success']) {
            $this->showErrorMessage($validComponentId['error']);
            $this->redirect($location);
        }

        // Проверяем существует ли связь
        $relationExists = $vlkModel->hasMethodComponentRelation($data['method_id'], $data['component_id']);
        if ($relationExists) {
            $this->showErrorMessage("Данные не удалось сохранить, выбранная метрологическая характеристика уже добавлен для методики");
            $this->redirect($location);
            die();
        }

        $result = $vlkModel->addMethodComponent($data);

        if (empty($result)) {
            $this->showErrorMessage("Данные не удалось сохранить");
            $this->redirect($location);
        } else {
            $this->showSuccessMessage("Данные сохранены успешно");
            $this->redirect($location);
        }
    }

    /**
     * Открепить данные метрологической характеристики
     */
    public function deleteMethodComponentAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');

        $umcId = (int)$_POST['umc_id'];

        if (empty($umcId) || $umcId < 0) {
            $response = [
                'success' => false,
                'error' => [
                    'message' => "Не удалось открепить метрологическую характеристику, не указаны или указаны неверно ИД",
                ]
            ];

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            return false;
        }

        $result = $vlkModel->deleteMethodComponent($umcId);

        if (empty($result)) {
            $response = [
                'success' => false,
                'error' => "Не удалось открепить метрологическую характеристику",
            ];
        } else {
            $response = [
                'success' => true,
            ];

            $this->showSuccessMessage("Метрологическая характеристика откреплёна");
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Получает историю связи метода и метрологической характеристики
     */
    public function getMethodComponentHistoryAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');

        $umcId = (int)$_POST['umc_id'];

        $result = $vlkModel->getMethodComponent($umcId);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Страница результатов измерений
     * @param $umcId - ulab_method_component
     */
    public function measuring($umcId)
    {
        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');
        /** @var Methods $methodsModel */
        $methodsModel = $this->model('Methods');
        /** @var Permission $permissionModel */
        $permissionModel = $this->model('Permission');

        $this->data['title'] = 'Результаты измерений';

        if (empty($umcId) || $umcId < 0) {
            $this->showErrorMessage("Не указан, или указан неверно ИД связи методики и метрологической характеристики");
            $this->redirect('/vlk/methodComponentList/');
        }

        $umc = $vlkModel->getMethodComponent($umcId);
        if (empty($umc)) {
            $this->showErrorMessage("Связи методики и метрологической характеристики с ИД {$umcId} не существует");
            $this->redirect('/vlk/methodComponentList/');
        }

        $component = $oborudModel->getComponent($umc['component_id']);
        $methodComponent = $vlkModel->getMethodComponent($umcId);
        $permissionInfo = $permissionModel->getUserPermission($_SESSION['SESS_AUTH']['USER_ID']);
        $uncertainty = $methodsModel->getUncertainty((int)$umc['method_id']);

        $methodId = (int)$umc['method_id'];
        $stSampleId = (int)$component['st_sample_id'];
        $measuringCount = (int)$methodComponent['measuring_count'];

        $this->data['umc_id'] = $umcId;
        $this->data['component'] = $component;
        $this->data['measuring_count'] = $measuringCount;

        $this->data['sample'] = $oborudModel->getSample($stSampleId);
        $this->data['method'] = $methodsModel->get($methodId);
        $this->data['measuring'] = $vlkModel->getVlkMeasuring($umcId);
        $this->data['accuracy_control'] = $methodsModel->findUncertaintyData($uncertainty, $component['certified_value']);

        // Проверка на доступ к редактированию
        $this->data['is_can_edit'] = in_array($permissionInfo['id'],  [ADMIN_PERMISSION, HEAD_IC_PERMISSION]);

        if (!empty($_SESSION['request_post'])) {
            unset($_SESSION['request_post']);
        }

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");

        $this->addJS("/assets/plugins/DataTables/DataTables-1.11.3/js/jquery.dataTables.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/dataTables.buttons.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.colVis.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.print.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.html5.min.js");
        $this->addJS("/assets/plugins/DataTables/JSZip-2.5.0/jszip.min.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/ellipsis.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/intl.js");

        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $this->addCDN("https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js");
        $this->addCDN("https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation");

        $r = rand();
        $this->addJS("/assets/js/vlk/measuring.js?v={$r}");

        $this->view('measuring');
    }

    /**
     * @desc Сохранить количество результатов параллельных измерений
     * @param $umcId - ulab_method_component
     */
    public function saveMeasuringCounts($umcId)
    {
        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');

        $_SESSION['request_post'] = $_POST;

        $umcId = (int)$umcId;
        $measuringCount = (int)$_POST['measuring_count'];

        $location = "/vlk/measuring/{$umcId}";

        if (empty($umcId) || $umcId < 0) {
            $this->showErrorMessage("Не указан, или указан неверно ИД связи методики и метрологической характеристики");
            $this->redirect('/vlk/methodComponentList/');
        }

        if (empty($measuringCount) || $measuringCount < 0) {
            $this->showErrorMessage('Не заполнено или не верно заполнено поле "Количество результатов параллельных измерений"');
            $this->redirect($location);
        }

        $methodComponent = $vlkModel->getMethodComponent($umcId);
        if (!empty($methodComponent['measuring_count'])) {
            $this->showErrorMessage("Количество результатов параллельных измерений для выбранной методики и метрологической характеристики уже существует");
            $this->redirect($location);
        }

        $data = ['measuring_count' => $measuringCount];
        $result = $vlkModel->editMethodComponent($umcId, $data);

        if (empty($result)) {
            $this->showErrorMessage("Данные не удалось сохранить");
            $this->redirect($location);
        } else {
            unset($_SESSION['request_post']);
            $this->showSuccessMessage("Данные сохранены успешно");
            $this->redirect($location);
        }
    }

    /**
     * @desc Добавить/обновить данные измерений ВЛК
     */
    public function insertUpdateMeasuring()
    {
        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');

        $data = $_POST['measuring'] ?? [];
        $umcId = (int)$data['umc_id']; // id Связь методик и метрологической характеристики (ulab_method_component)
        $uvmId = (int)$_POST['uvm_id']; // id Результаты измерения ВЛК (ulab_vlk_measuring)

        if (empty($umcId) || $umcId < 0) {
            $this->showErrorMessage("Не указан, или указан неверно ИД связи методики и метрологической характеристики");
            $this->redirect('/vlk/methodComponentList/');
        }

        $location = "/vlk/measuring/{$umcId}";
        $block = '#measuringBlock';
        $action = $uvmId ? "Редактирование результатов измерения" : "Создание результатов измерения";

        $validDate = $this->validateDate($data['date'], 'Дата', true);
        if (!$validDate['success']) {
            $this->showErrorMessage($validDate['error']);
            $this->redirect($location);
        }

        foreach ($data['result'] as $key => $item) {
            $num = $key + 1;
            $validResult = $this->validateNumber($item, "Результат {$num}-го измерения", true);
            if (!$validResult['success']) {
                $this->showErrorMessage($validResult['error']);
                $this->redirect($location);
            }
        }

        if (!empty($uvmId)) {
            $result = $vlkModel->editVlkMeasuring($uvmId, $data);
            $vlkModel->addHistoryMeasuring($uvmId, $action);
        } else {
            $result = $vlkModel->addVlkMeasuring($data);
            $vlkModel->addHistoryMeasuring($result, $action);
        }

        if (empty($result)) {
            $this->showErrorMessage("Данные измерений не удалось сохранить");
            $this->redirect($location);
        } else {
            unset($_SESSION['request_post']);
            $this->showSuccessMessage("Данные измерений сохранены успешно");
            $this->redirect($location . $block);
        }
    }

    /**
     * @desc Получить данные измерений ВЛК по id Ajax запросом
     */
    public function getVlkMeasuringByIdAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');

        $result = $vlkModel->getVlkMeasuringById($_POST['uvm_id']);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Удалить данные измерений ВЛК по id Ajax запросом
     */
    public function delVlkMeasuringAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');

        $uvmId = (int)$_POST['uvm_id'];

        if (empty($uvmId) || $uvmId < 0) {
            $response = [
                'success' => false,
                'error' => "Не удалось удалить данные измерений, не указан или указан неверно ИД",
            ];

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            return false;
        }

        $vlkModel->addHistoryMeasuring($uvmId, "Удаление данных измерений");
        $result = $vlkModel->softDelVlkMeasuring($uvmId);

        if (empty($result)) {
            $response = [
                'success' => false,
                'error' => "Не удалось удалить данные измерений",
            ];
        } else {
            $response = [
                'success' => true,
            ];

            $this->showSuccessMessage("Данные измерений удалены");
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Получение данных для журнала измерений ВЛК Ajax запросом
     */
    public function getVlkMeasuringAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');


        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'      => $_POST['start'],  // текущая страница
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

        if ( !empty($_POST['umc_id']) ) {
            $filter['search']['umc_id'] = $_POST['umc_id'];
        }

        $data = $vlkModel->getVlkMeasuringList($filter);

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
     * @desc Получает историю измерений ВЛК Ajax запросом
     */
    public function getHistoryMeasuringAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Vlk $vlkModel*/
        $vlkModel = $this->model('Vlk');

        $uvmId = (int)$_POST['id'];

        $data['history'] = $vlkModel->getHistoryMeasuring($uvmId);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}