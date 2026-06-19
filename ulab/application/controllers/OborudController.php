<?php

/**
 *
 * Class OborudController
 */
class OborudController extends Controller
{
    public function index()
    {
        $this->redirect('/request/new/');
    }


    public function list()
    {
        $this->data['title'] = 'Журнал учета оборудования';

        /** @var  Lab $lab*/
        $lab = $this->model('Lab');

        $this->data['lab'] = $lab->getLabaRoom();

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
        $this->addJs("/assets/js/oborud-list.js?v={$r}");

        $this->view('list');
    }

    // закрытие доступа на создание оборудования неугодным пользователям по задаче dv-4839
    public function checkPermission($urlBack=null)
    {
        $allowedUsers = [
            222, // Алексеева
            10, // Апанович
            227, // Усова
            250, // Заводчикова
            33, // Молчанова
            61 // техподдержка
        ];

        /** @var User $user */
        $user = $this->model('User');
        $userId = $user->getCurrentUserId();

        if (!in_array($userId, $allowedUsers)) {
            $this->showErrorMessage("Недостаточно прав для совершения действия. (В рамках задачи https://ulab.niistrom.pro/workgroups/group/55/tasks/task/view/4839/)");
            if (is_null($urlBack)) {
                $url = $_SERVER['HTTP_REFERER'];
                $path = parse_url($url, PHP_URL_PATH);
                $path = str_replace("/ulab",  "", $path);
                $this->redirect($path);
                return;
            }

            $this->redirect($urlBack);
        }
    }

    public function new()
    {
        $this->data['title'] = 'Новое оборудование';

        $this->checkPermission('/oborud/list');

        $this->form();
    }


    public function edit($oborudId)
    {
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');
        /** @var Methods $methodModel */
        $methodModel = $this->model('Methods');

        $this->data['title'] = 'Редактировать оборудование';

        $this->data['id'] = $oborudId;
        $this->data['oborud'] = $oborudModel->getOborudById($oborudId);
        $this->data['status'] = $oborudModel->getStatus($this->data['oborud']);
        $this->data['certificate'] = $oborudModel->getCertificateByOborud($oborudId);
        $this->data['moving'] = $oborudModel->getLastOborudMoving($oborudId);
        $this->data['interchangeable'] = $oborudModel->getInterchangeableOborud($oborudId);
        $this->data['method_list'] = $methodModel->getMethodListByOborudId($oborudId);

        $this->form();
    }


    public function form()
    {
        /** @var Lab $labModel */
        $labModel = $this->model('Lab');
        /** @var User $user */
        $user = $this->model('User');
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $this->data['lab'] = $labModel->getList();

        $this->data['rooms'] = $labModel->getRooms();
        $this->data['lab_room'] = $labModel->getLabaRoom();

        $this->data['users'] = $user->getUsers();

        $this->data['oborud_list'] = $oborudModel->getList();


        $this->data['commissioning_user'] = [
            [
                'id' => 10,
                'short_name' => 'Е. Апанович',
            ],
//            [
//                'id' => 137,
//                'short_name' => 'Е. Байгазова',
//            ],
            [
                'id' => 15,
                'short_name' => 'В. Шингарев',
            ],
            [
                'id' => 13,
                'short_name' => 'Т. Велютич',
            ],
            [
                'id' => 32,
                'short_name' => 'М. Живцов',
            ],
            [
                'id' => 11,
                'short_name' => 'Т. Овчинникова',
            ],
            [
                'id' => 58,
                'short_name' => 'Р. Живцова',
            ],
            [
                'id' => 27,
                'short_name' => 'А. Шишкина',
            ],
            [
                'id' => 95,
                'short_name' => 'Т. Хеленюк',
            ],
            [
                'id' => 123,
                'short_name' => 'Н. Турьева',
            ],
            [
                'id' => 87,
                'short_name' => 'Е. Светогорова',
            ],
            [
                'id' => 156,
                'short_name' => 'Е. Скопина',
            ],
        ];

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");

        $r = rand();
        $this->addJs("/assets/js/oborud-form.js?v={$r}");

        $this->view('form');
    }


    /**
     *
     */
    public function addOborudMoving()
    {
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $oborudModel->addOborudMoving($_POST['form']);

        if ( isset($_POST['journal_page']) ) {
            $this->redirect("/oborud/movingJournal/{$_POST['form']['oborud_id']}");
        } else {
            $this->redirect("/oborud/edit/{$_POST['form']['oborud_id']}#moving-block");
        }
    }


    /**
     *
     */
    public function movingJournal($oborudId)
    {
        $this->data['title'] = 'Журнала движения оборудования';

        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');
        /** @var User $user */
        $user = $this->model('User');

        $this->data['oborud_list'] = $oborudModel->getList();

        $this->data['users'] = $user->getUsers();

        $this->data['oborud_id'] = $oborudId;

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");

        $this->addCDN('https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js');
        $this->addCDN('https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js');

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
        $this->addJs("/assets/js/oborud-moving.js?v={$r}");

        $this->view('movingJournal');
    }


    /**
     *
     */
    public function getOborudMovingJournal()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $filter = [
            'paginate' => [
                'length' => $_POST['length'], // кол-во строк на страницу
                'start' => $_POST['start'],  // текущая страница
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

        if ( !empty($_POST['oborud_id']) ) {
            $filter['search']['oborud_id'] = $_POST['oborud_id'];
        }

        $data = $oborudModel->getDataToOborudMovingJournal($filter);

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


    public function insertUpdate()
    {
        $this->checkPermission();
        $isEdit = isset($_POST['id']);
        $oborudId = $_POST['id']?? '';

        if ( $isEdit ) {
            $redirect = "/oborud/edit/{$oborudId}";
            $successMsg = 'Оборудование успешно изменено';
        } else {
            $redirect = "/oborud/new/";
            $successMsg = 'Оборудование успешно создано';
        }

//        echo "<pre>";
//        print_r($_POST);
//        echo "</pre>";
//        die();

        // сохраним пост в сессию, что бы при ошибке не заполнять поля заново
        $_SESSION['request_post'] = $_POST['oborud'];

        //// блок проверок

        // Наименование *
        $valid = $this->validateField($_POST['oborud']['OBJECT'], "Наименование");
        if ( !$valid['success'] ) {
            $this->showErrorMessage($valid['error']);
            $this->redirect($redirect);
        }

        // Дата ввода в эксплуатацию *
        $valid = $this->validateField($_POST['oborud']['god_vvoda_expluatation'], "Дата ввода в эксплуатацию");
        if ( !$valid['success'] ) {
            $this->showErrorMessage($valid['error']);
            $this->redirect($redirect);
        }

        // Место установки или хранения *
        $valid = $this->validateField($_POST['oborud']['place_of_installation_or_storage'], "Место установки или хранения");
        if ( !$valid['success'] ) {
            $this->showErrorMessage($valid['error']);
            $this->redirect($redirect);
        }

        //// конец блок проверок

        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        if ( !$isEdit ) { // Добавление
            $oborudId = $oborudModel->insertOborud($_POST['oborud']);

            if (empty($oborudId)) {
                $this->showErrorMessage("Не удалось создать оборудование");
                $this->redirect($redirect);
            }
        } else { // Редактирование
            $oborudModel->updateOborud($oborudId, $_POST['oborud']);

            $oborudModel->updateCertificateArray($oborudId, $_POST['certificate'], $_FILES['certificate']);
        }

        // Описание типа оборудования
        if ( !empty($_FILES['desc_oborud_file']['name']) ) {
            $result = $oborudModel->saveOborudFile($oborudId, $_FILES['desc_oborud_file']);

            if ( !$result['success'] ) {
                $this->showWarningMessage("Не сохранился файл 'Описание типа оборудования'. " . $result['error']);
            } else {
                $oborudModel->updateFieldOborud($oborudId, 'desc_oborud', $result['data']);
            }
        }
        // Фотография оборудования
        if ( !empty($_FILES['photo_oborud_file']['name']) ) {
            $result = $oborudModel->saveOborudFile($oborudId, $_FILES['photo_oborud_file']);

            if ( !$result['success'] ) {
                $this->showWarningMessage("Не сохранился файл 'Фотография оборудования'. " . $result['error']);
            } else {
                $oborudModel->updateFieldOborud($oborudId, 'photo_oborud', $result['data']);
            }
        }
        // Скан-копия документа о праве собственности на оборудование
        if ( !empty($_FILES['property_rights_file']['name']) ) {
            $result = $oborudModel->saveOborudFile($oborudId, $_FILES['property_rights_file']);

            if ( !$result['success'] ) {
                $this->showWarningMessage("Не сохранился файл 'Скан-копия документа'. " . $result['error']);
            } else {
                $oborudModel->updateFieldOborud($oborudId, 'property_rights_pdf', $result['data']);
            }
        }
        // Паспорт / руководство по эксплуатации
        if ( !empty($_FILES['passport_oborud']['name']) ) {
            $result = $oborudModel->saveOborudFile($oborudId, $_FILES['passport_oborud']);

            if ( !$result['success'] ) {
                $this->showWarningMessage("Не сохранился файл 'Скан-копия документа'. " . $result['error']);
            } else {
                $oborudModel->updateFieldOborud($oborudId, 'passport_pdf', $result['data']);
            }
        }

        // Паспорт / руководство по эксплуатации 2
        if ( !empty($_FILES['passport_oborud_2']['name']) ) {
            $result = $oborudModel->saveOborudFile($oborudId, $_FILES['passport_oborud_2']);

            if ( !$result['success'] ) {
                $this->showWarningMessage("Не сохранился файл 'Скан-копия документа'. " . $result['error']);
            } else {
                $oborudModel->updateFieldOborud($oborudId, 'passport_pdf_2', $result['data']);
            }
        }

        // обновляем взаимозаменяемое оборудование
        $oborudModel->updateInterchangeableOborud($oborudId, $_POST['inter']);

        // обновляем таблицу класс точности
        $oborudModel->setPrecisionTable($oborudId, $_POST['precision_table']);

        $this->showSuccessMessage($successMsg);
        unset($_SESSION['request_post']);

        $this->redirect("/oborud/edit/{$oborudId}");
    }


    /**
     * @desc добавляет сертификат оборудованию
     */
    public function addCertificate()
    {
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $oborudModel->addCertificate($_POST['form'], $_FILES['file']);

        $this->showSuccessMessage("Сертификат добавлен");

        $this->redirect("/oborud/edit/{$_POST['form']['oborud_id']}#certificate-block");
    }


    /**
     * @desc списание оборудования
     */
    public function decommissioned()
    {
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $oborudModel->setDecommissioned((int)$_POST['oborud_id'], $_POST['form'], $_POST['change_oborud_id']);

        $this->showSuccessMessage("Оборудование списано");

        $this->redirect("/oborud/edit/{$_POST['oborud_id']}");
    }


    /**
     * @desc списание оборудования
     */
    public function setLongStorage()
    {
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $oborudModel->setLongStorage((int)$_POST['oborud_id'], $_POST['form'], $_POST['change_oborud_id']);

        $this->showSuccessMessage("Оборудование на длительном хранении");

        $this->redirect("/oborud/edit/{$_POST['oborud_id']}");
    }


    /**
     *
     */
    public function getListProcessingAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Oborud $oborud*/
        $oborud = $this->model('Oborud');

        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'      => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if ( isset($column['search']['value']) && $column['search']['value'] !== '' ) {
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
        if ( $_POST['everywhere'] !== '' ) {
            $filter['search']['everywhere'] = $_POST['everywhere'];
        }

        $data = $oborud->getDataToJournal($filter);

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
     * @desc Страница образцов контроля
     */
    public function sampleList()
    {
        /** @var  Lab $lab*/
        $lab = $this->model('Lab');

        $this->data['title'] = 'Образцы контроля';

        $this->data['lab'] = $lab->getLabaRoom();

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");

        $this->addJS("/assets/plugins/DataTables/DataTables-1.11.3/js/jquery.dataTables.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/dataTables.buttons.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.colVis.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.print.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.html5.min.js");
        $this->addJS("/assets/plugins/DataTables/JSZip-2.5.0/jszip.min.js");

        $r = rand();
        $this->addJs("/assets/js/oborud/sample-list.js?v={$r}");

        $this->view('sample_list');
    }

    /**
     * @desc Получение данных для образцов контроля Ajax запросом
     */
    public function getSampleListAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

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

        $data = $oborudModel->getSampleList($filter);

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
     * @desc Получает историю образцов контроля
     */
    public function getSampleHistoryAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $data['info'] = $oborudModel->getSample($_POST['id']);
        $data['history'] = $oborudModel->getSampleHistory($_POST['id']);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Страница создания и обновления образца контроля
     * @param $id
     */
    public function sampleCard($id)
    {
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');
        /** @var Methods $methodsModel */
        $methodsModel = $this->model('Methods');
        /** @var Lab $labModel */
        $labModel = $this->model('Lab');
        /** @var Permission $permissionModel */
        $permissionModel = $this->model('Permission');

        $this->data['title'] = $id ? 'Редактирование образца контроля' : 'Создание образца контроля';

        $sample = $oborudModel->getSample($id);
        $permissionInfo = $permissionModel->getUserPermission($_SESSION['SESS_AUTH']['USER_ID']);

        $this->data['lab_list'] = $labModel->getList();
        $this->data['room_list'] = $labModel->getRoomByLabId($sample['LAB_ID']);
        $this->data['lab_user_list'] = $labModel->getLabAndUser();
        $this->data['components'] = $oborudModel->getComponentsBySampleId($id);
        $this->data['unit_list'] = $methodsModel->getUnitList();
        $this->data['stage'] = $oborudModel->getSampleStage($sample);

        // Проверка на доступ к изменению данных
        $this->data['is_may_change'] = in_array($permissionInfo['id'],  [SMK_PERMISSION, ADMIN_PERMISSION]); // HEAD_IC_PERMISSION

        if (!empty($_SESSION['request_post'])) {
            $this->data['id'] = $id;
            $this->data['sample'] = $_SESSION['request_post']['sample'];

            unset($_SESSION['request_post']);
        } else {
            $this->data['id'] = $id;
            $this->data['sample'] = $sample;
        }

        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $r = rand();
        $this->addJS("/assets/js/oborud/sample-card.js?v={$r}");

        $this->view('sample_card');
    }

    private function validateSample($data, $id)
    {
        $location = "/oborud/sampleCard/{$id}";

        // валидация
        $validName = $this->validateField($data['NAME'], 'Наименование', true);
        if (!$validName['success']) {
            $this->showErrorMessage($validName['error']);
            $this->redirect($location);
        }

        $validType = $this->validateField($data['TYPE'], 'Тип', true);
        if (!$validType['success']) {
            $this->showErrorMessage($validType['error']);
            $this->redirect($location);
        }

        $validNumber = $this->validateField($data['NUMBER'], 'Номер', true);
        if (!$validNumber['success']) {
            $this->showErrorMessage($validNumber['error']);
            $this->redirect($location);
        }

        $validCategory = $this->validateField($data['CATEGORY'], 'Категория', false);
        if (!$validCategory['success']) {
            $this->showErrorMessage($validCategory['error']);
            $this->redirect($location);
        }

        $validRegNum = $this->validateField($data['REG_NUM'], 'Номер в Госреестре', false);
        if (!$validRegNum['success']) {
            $this->showErrorMessage($validRegNum['error']);
            $this->redirect($location);
        }

        $validManufacturer = $this->validateField($data['MANUFACTURER'], 'Изготовитель', true);
        if (!$validManufacturer['success']) {
            $this->showErrorMessage($validManufacturer['error']);
            $this->redirect($location);
        }

        $validPurpose = $this->validateField($data['PURPOSE'], 'Назначение', true);
        if (!$validPurpose['success']) {
            $this->showErrorMessage($validPurpose['error']);
            $this->redirect($location);
        }

        $validPurchaseState = $this->validateField($data['PURCHASE_STATE'], 'Состояние при покупке', false);
        if (!$validPurchaseState['success']) {
            $this->showErrorMessage($validPurchaseState['error']);
            $this->redirect($location);
        }

        $validDop = $this->validateField($data['DOP'], 'Дополнительные сведения', false);
        if (!$validDop['success']) {
            $this->showErrorMessage($validDop['error']);
            $this->redirect($location);
        }

        $validNormDoc = $this->validateField($data['NORM_DOC'], 'Нормативный документ, порядок и условия применения', false);
        if (!$validNormDoc['success']) {
            $this->showErrorMessage($validNormDoc['error']);
            $this->redirect($location);
        }

        $validOwnershipDocument = $this->validateField($data['OWNERSHIP_DOCUMENT'], 'Документ о праве собственности', false);
        if (!$validOwnershipDocument['success']) {
            $this->showErrorMessage($validOwnershipDocument['error']);
            $this->redirect($location);
        }

        $validShelfLife = $this->validateField($data['SHELF_LIFE'], 'Срок годности', false);
        if (!$validShelfLife['success']) {
            $this->showErrorMessage($validShelfLife['error']);
            $this->redirect($location);
        }

        $validManufactureDate = $this->validateDate($data['MANUFACTURE_DATE'], 'Дата выпуска', false);
        if (!$validManufactureDate['success']) {
            $this->showErrorMessage($validManufactureDate['error']);
            $this->redirect($location);
        }

        $validExpiryDate = $this->validateDate($data['EXPIRY_DATE'], 'Годен до', false);
        if (!$validExpiryDate['success']) {
            $this->showErrorMessage($validExpiryDate['error']);
            $this->redirect($location);
        }

        $validComment = $this->validateField($data['COMMENT'], 'Примечание', false);
        if (!$validComment['success']) {
            $this->showErrorMessage($validComment['error']);
            $this->redirect($location);
        }

        $validLabId = $this->validateNumber($data['LAB_ID'], 'Лаборатория', false);
        if (!$validLabId['success']) {
            $this->showErrorMessage($validLabId['error']);
            $this->redirect($location);
        }

        $validRoomId = $this->validateNumber($data['ROOM_ID'], 'Помещение', false);
        if (!$validRoomId['success']) {
            $this->showErrorMessage($validRoomId['error']);
            $this->redirect($location);
        }

        $validAssignedId = $this->validateNumber($data['ASSIGNED_ID'], 'Ответственный', false);
        if (!$validAssignedId['success']) {
            $this->showErrorMessage($validAssignedId['error']);
            $this->redirect($location);
        }
    }

    /**
     * @desc Сохранение/обновление данных образца контроля
     */
    public function sampleInsertUpdate()
    {
        if (empty($_POST)) {
            $this->showErrorMessage("Некорректные данные при сохранении образца контроля");
            $this->redirect("/oborud/sampleList/");
        }

        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $id = (int)$_POST['id'];
        $data = $_POST['sample'] ?? [];

        if (!empty($id)) {
            $location = "/oborud/sampleCard/{$id}";
            $action = "Редактирование образца контроля";
        } else {
            $location = "/oborud/sampleCard/";
            $action = "Создание образца контроля";
        }

        // Сохраним пост в сессию, что бы при ошибке не заполнять поля заново
        $_SESSION['request_post'] = $_POST;

        // Валидация
        $this->validateSample($data, $id);

        if (!empty($id)) {
            $result = $oborudModel->updateSample($id, $data);
            $oborudModel->addHistorySample($id, $action);
        } else {
            $result = $oborudModel->addSample($data);
            $oborudModel->addHistorySample($result, $action);

            $id = (int)$result;
        }

        if (!empty($_FILES['DESCRIPTION_SO_FILE']['name'])) {
            $result = $oborudModel->saveSampleFile($id, $_FILES['DESCRIPTION_SO_FILE'], 'description_so');

            if ( !$result['success'] ) {
                $this->showWarningMessage("Не сохранился файл 'Описание типа СО'. " . $result['error']);
            } else {
                $oborudModel->updateFieldSample($id, 'DESCRIPTION_SO', $result['data']);
            }
        }

        if ( !empty($_FILES['PHOTO_SO_FILE']['name']) ) {
            $result = $oborudModel->saveSampleFile($id, $_FILES['PHOTO_SO_FILE'], 'photo_so');

            if ( !$result['success'] ) {
                $this->showWarningMessage("Не сохранился файл 'Фотография СО'. " . $result['error']);
            } else {
                $oborudModel->updateFieldSample($id, 'PHOTO_SO', $result['data']);
            }
        }

        if ( !empty($_FILES['PROOF_OF_OWNERSHIP_FILE']['name']) ) {
            $result = $oborudModel->saveSampleFile($id, $_FILES['PROOF_OF_OWNERSHIP_FILE'], 'proof_of_ownership');

            if ( !$result['success'] ) {
                $this->showWarningMessage("Не сохранился файл 'Скан-копия документа'. " . $result['error']);
            } else {
                $oborudModel->updateFieldSample($id, 'PROOF_OF_OWNERSHIP', $result['data']);
            }
        }

        if ( !empty($_FILES['MANUAL_OR_PASSPORT_FILE']['name']) ) {
            $result = $oborudModel->saveSampleFile($id, $_FILES['MANUAL_OR_PASSPORT_FILE'], 'manual_or_passport');

            if ( !$result['success'] ) {
                $this->showWarningMessage("Не сохранился файл 'Паспорт/руководство по эксплуатации'. " . $result['error']);
            } else {
                $oborudModel->updateFieldSample($id, 'MANUAL_OR_PASSPORT', $result['data']);
            }
        }

        if ( !empty($_FILES['OWNERSHIP_DOCUMENT_FILE']['name']) ) {
            $result = $oborudModel->saveSampleFile($id, $_FILES['OWNERSHIP_DOCUMENT_FILE'], 'ownership_document');

            if ( !$result['success'] ) {
                $this->showWarningMessage("Не сохранился файл 'Паспорт/руководство по эксплуатации'. " . $result['error']);
            } else {
                $oborudModel->updateFieldSample($id, 'OWNERSHIP_DOCUMENT', $result['data']);
            }
        }

        if (empty($result)) {
            $this->showErrorMessage("Данные не удалось сохранить");
            $this->redirect($location);
        } else {
            unset($_SESSION['request_post']);
            $this->showSuccessMessage("Данные сохранены успешно");
            $this->redirect("/oborud/sampleCard/{$id}");
        }
    }

    /**
     * @desc Получение помещений по выбранным лабораториям
     */
    public function getRoomByLabIdAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Lab $labModel */
        $labModel = $this->model('Lab');

        $response = $labModel->getLabaRoom($_POST['lab']);

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Сделать образец контроля не актуальным
     * @param $id
     */
    public function nonActualSample($id)
    {
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $data['IS_ACTUAL'] = 0;

        $result = $oborudModel->updateSample($id, $data);
        $oborudModel->addHistorySample($id, 'Признание образца контроля не актуальным');

        if (empty($result)) {
            $this->showErrorMessage("Образец контроля не удалось сделать не актуальным");
        } else {
            $this->showSuccessMessage("Образец контроля теперь не актуален");
        }

        $this->redirect("/oborud/sampleCard/{$id}");
    }

    private function validateComponent($data, $id)
    {
        $location = "/oborud/sampleCard/{$id}";

        // Валидация
        $validName = $this->validateField($data['name'], 'Наименование', true);
        if (!$validName['success']) {
            $this->showErrorMessage($validName['error']);
            $this->redirect($location);
        }

        $validCertifiedValue = $this->validateNumber($data['certified_value'], 'Аттестованное значение', true);
        if (!$validCertifiedValue['success']) {
            $this->showErrorMessage($validCertifiedValue['error']);
            $this->redirect($location);
        }

        $validCertifiedUnitId = $this->validateNumber($data['certified_unit_id'], 'Единица аттестованного значения', true);
        if (!$validCertifiedUnitId['success']) {
            $this->showErrorMessage($validCertifiedUnitId['error']);
            $this->redirect($location);
        }

        $validUncertainty = $this->validateNumber($data['uncertainty'], 'Неопределенность', false);
        if (!$validUncertainty['success']) {
            $this->showErrorMessage($validUncertainty['error']);
            $this->redirect($location);
        }

        $validUncertaintyUnitId = $this->validateNumber($data['uncertainty_unit_id'], 'Единица измерения неопределенности', false);
        if (!$validUncertaintyUnitId['success']) {
            $this->showErrorMessage($validUncertaintyUnitId['error']);
            $this->redirect($location);
        }

        $validErrorCharacteristic = $this->validateField($data['error_characteristic'], 'Характеристика погрешности аттестованного значения', false);
        if (!$validErrorCharacteristic['success']) {
            $this->showErrorMessage($validErrorCharacteristic['error']);
            $this->redirect($location);
        }

        $validCharacteristicUnitId = $this->validateNumber($data['characteristic_unit_id'], 'Единица измерения характеристики погрешности аттестованного значения', false);
        if (!$validCharacteristicUnitId['success']) {
            $this->showErrorMessage($validCharacteristicUnitId['error']);
            $this->redirect($location);
        }
    }

    /**
     * @desc Создание данных компонента
     */
    public function componentInsert()
    {
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $sampleId = (int)$_POST['component']['st_sample_id'];
        $componentId = (int)$_POST['component_id'];
        $data = $_POST['component'] ?? [];

        $location = "/oborud/sampleCard/{$sampleId}";
        $action = $componentId ? "Редактирование компонента '{$data['name']}'" : "Создание компонента '{$data['name']}'";

        // Валидация
        $this->validateComponent($data, $sampleId);

        if (!empty($componentId)) {
            $result = $oborudModel->updateComponent($componentId, $data);
            $oborudModel->addHistorySample($sampleId, $action);
        } else {
            $result = $oborudModel->addComponent($data);
            $oborudModel->addHistorySample($sampleId, $action);
        }

        if (empty($result)) {
            $this->showErrorMessage("Компонент не удалось сохранить");
            $this->redirect($location);
        } else {
            unset($_SESSION['request_post']);
            $this->showSuccessMessage("Данные сохранены успешно");
            $this->redirect($location . '#component-block');
        }
    }

    /**
     * Полученние данных компонента Ajax запросом
     */
    public function getComponentAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');
        /** @var Permission $permissionModel */
        $permissionModel = $this->model('Permission');

        $result = $oborudModel->getComponent($_POST['id']);
        $permissionInfo = $permissionModel->getUserPermission($_SESSION['SESS_AUTH']['USER_ID']);

        // Проверка на доступ к изменению данных
        $result['is_may_change'] = in_array($permissionInfo['id'],  [SMK_PERMISSION, ADMIN_PERMISSION]); // HEAD_IC_PERMISSION

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Удалить данные компонента Ajax запросом
     */
    public function removeComponentAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $id = (int)$_POST['id'];
        $sampleId = (int)$_POST['st_sample_id'];
        $action = "Удаление метрологической характеристики '{$_POST['name']}'";

        $result = $oborudModel->removeComponent($id);
        $oborudModel->addHistorySample($sampleId, $action);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

}
