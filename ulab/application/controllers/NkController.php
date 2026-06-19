<?php


/**
 * @desc Неразрушающий контроль
 * Class NkController
 */
class NkController extends Controller
{
    /**
     * route /nk/
     */
    public function index()
    {
        $this->redirect('/request/list/');
    }

    /**
     * @desc Градуировочная зависимость
     */
    public function graduation($id)
    {
        /** @var Nk $nkModel */
        $nkModel = $this->model('Nk');

        $this->data['title'] = empty($id)? 'Создание градуировочной зависимости' : "Редактирование градуировочной зависисмоти";

        $graduation = $nkModel->getGraduation($id);

        if (isset($_SESSION['graduation_post'])) {
            $this->data['id'] = (int)$_SESSION['graduation_post']['id'];
            $this->data['object'] = $_SESSION['graduation_post']['form']['object'] ?? '';
            $this->data['concrete_class'] = $_SESSION['graduation_post']['form']['concrete_class'] ?? '';
            $this->data['measuring_device'] = $_SESSION['graduation_post']['form']['measuring_device'] ?? '';
            $this->data['method'] = $_SESSION['graduation_post']['form']['method'] ?? '';
            $this->data['date'] = $_SESSION['graduation_post']['form']['date'] ?? '';
            $this->data['day_to_test'] = (int)$_SESSION['graduation_post']['form']['day_to_test'] ?: null;
            $this->data['measuring'] = $_SESSION['graduation_post']['form'] ?? [];

            unset($_SESSION['graduation_post']);
        } else {
            $this->data['id'] = (int)$graduation['id'];
            $this->data['object'] = $graduation['object'] ?? '';
            $this->data['concrete_class'] = $graduation['concrete_class'] ?? '';
            $this->data['measuring_device'] = $graduation['measuring_device'] ?? '';
            $this->data['method'] = $graduation['method'] ?? '';
            $this->data['date'] = $graduation['date'] ?: date('Y-m-d');
            $this->data['day_to_test'] = (int)$graduation['day_to_test'] ?: null;
            $this->data['measuring'] = $graduation['data'] ?? [];
        }

        $this->addCDN("https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js");
        $this->addJS('/assets/plugins/chart/plugins/gchartjs-plugin-trendline.js');
        $this->addJs('/assets/js/nk/graduation.js?v=' . rand());

        $this->view('graduation');
    }

    /**
     * @desc Сохранить лист измерения по градуировочной зависимости
     */
    public function insertUpdateGraduation()
    {
        /** @var Nk $nkModel */
        $nkModel = $this->model('Nk');

        $graduationId = (int)$_POST['id'];
        $location   = empty($graduationId)? '/nk/graduation/' : "/nk/graduation/{$graduationId}";
        $successMsg = empty($graduationId)? 'Лист измерения успешно создан' : "Лист измерения успешно изменен";

        $_SESSION['graduation_post'] = $_POST;

        $this->validateGraduation($_POST);

        if ( !empty($graduationId) ) { // редактирование
            $nkModel->updateGraduation($graduationId, $_POST['form']);
        } else { // создание
            $graduationId = $nkModel->addGraduation($_POST['form']);
        }

        $path = UPLOAD_DIR . "/plot/{$graduationId}";
        //$fileName = date('d-m-Y') . '-' . time() . '.png';
        $fileName = date('Y-m-d') . '-' . time() . '.png';

        if (!empty($_POST['chart'])) {
            $nkModel->saveChartImage($path, $fileName, $_POST['chart']);
        }

        if ( empty($graduationId) ) {
            $this->showErrorMessage("Лист измерения не удалось сохранить");
            $this->redirect($location);
        } else {
            unset($_SESSION['graduation_post']);
            $this->showSuccessMessage($successMsg);
            $this->redirect("/nk/graduation/{$graduationId}");
        }
    }

    /**
     * @param $data
     */
    private function validateGraduation($data)
    {
        $location = empty($data['id'])? '/nk/graduation/' : "/nk/graduation/{$data['id']}";

        // валидация
        $validMethod = $this->validateField($data['form']['measuring_device'] ?? '', 'Прибор для замеров', true);
        if (!$validMethod['success']) {
            $this->showErrorMessage($validMethod['error']);
            $this->redirect($location);
        }

        $validDate = $this->validateDate($data['form']['date'] ?? '', 'Дата', true);
        if (!$validDate['success']) {
            $this->showErrorMessage($validDate['error']);
            $this->redirect($location);
        }

        $validDayToTest = $this->validateNumber($data['form']['day_to_test'] ?? '', 'Срок проведения испытаний', true);
        if (!$validDayToTest['success']) {
            $this->showErrorMessage($validDayToTest['error']);
            $this->redirect($location);
        }
    }

    /**
     * @desc Журнал градуировочная зависимость
     */
    public function graduationList()
    {
        $this->data['title'] = 'Журнал листов измерений градуировочной зависимости';
        $this->data['date_start'] = date('Y-m-d', strtotime('-1 year'));
        $this->data['date_end'] = date('Y-m-d');

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
        $this->addJs("/assets/js/nk/graduation-list.js?v={$r}");

        $this->view('graduation_list');
    }

    /**
     * Получить данные для журнала градуировочной зависимости Ajax запросом
     */
    public function getGraduationJournalAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Nk $nkModel */
        $nkModel = $this->model('Nk');


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

        if ( !empty($_POST['dateStart']) ) {
            $filter['search']['dateStart'] = date('Y-m-d', strtotime($_POST['dateStart']));
            $filter['search']['dateEnd'] = date('Y-m-d', strtotime($_POST['dateEnd']));
        }

        $data = $nkModel->getGraduationJournal($filter);

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
     * @desc Получить список градуировочных зависимостей
     */
    public function getGraduationListAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Nk $nkModel */
        $nkModel = $this->model('Nk');

        $response = $nkModel->getGraduationList();

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Получить данные листа измерения градуировочной зависимости
     */
    public function getGraduationAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Nk $nkModel */
        $nkModel = $this->model('Nk');
        /** @var Gost $gostModel */
        $gostModel = $this->model('Gost');

        $response = [];

        if ( !empty($_POST['measurement_id']) && (int)$_POST['measurement_id'] > 0 ) {
            $material = $gostModel->getMaterialByUgtpId($_POST['ugtp_id']);
            $response = $nkModel->getGraduation($_POST['measurement_id']);
            $response += $material;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}