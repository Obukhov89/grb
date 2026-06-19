<?php

/**
 * Страница с ТУ
 * Class TechConditionController
 */
class TechConditionController extends Controller
{
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

    protected function form()
    {
        /** @var Methods $methodsModel */
        $methodsModel = $this->model('Methods');
        /** @var Material $materialModel */
        $materialModel = $this->model('Material');

        $this->data['measured_properties'] = $methodsModel->getMeasuredPropertiesList();
        $this->data['unit_list'] = $methodsModel->getUnitList();
        $this->data['dop_material_list'] = $materialModel->getList(true);

        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $this->addJS("/assets/js/tech-condition/form.js");

        $this->view('form');
    }

    public function index()
    {
        $this->redirect('/techCondition/list/');
    }


    /**
     * @desc Страница создания ТУ
     */
    public function new()
    {
        $this->checkPermission("/techCondition/list/");
        $this->data['title'] = 'Создание ТУ';

        $this->form();
    }


    /**
     * @desc Страница редактирования ТУ
     * @param $id
     */
    public function edit($id)
    {
        $this->data['title'] = 'Редактирование ТУ';

        /** @var TechCondition $tcModel */
        $tcModel = $this->model('TechCondition');

        $this->data['id'] = $id;
        $this->data['form'] = $tcModel->get($id);

        $this->form();
    }


    /**
     * @desc Создание/изменение ТУ с формы
     */
    public function insertUpdate()
    {
        $this->checkPermission();
        /** @var TechCondition $tcModel */
        $tcModel = $this->model('TechCondition');

        $location   = empty($_POST['id'])? '/techCondition/new/' : "/techCondition/edit/{$_POST['id']}";
        $successMsg = empty($_POST['id'])? 'ТУ успешно создано' : "ТУ успешно изменено";

        // сохраним пост в сессию, что бы при ошибке не заполнять поля заново
        $_SESSION['request_post'] = $_POST;

        //// блок проверок

        ///  \блок проверок

        if ( !empty($_POST['id']) ) { // редактирование
            $idTU = $_POST['id'];
            $tcModel->update($_POST['id'], $_POST['form']);
        } else { // создание
            $idTU = $tcModel->add($_POST['form']);
        }

        if ( empty($idTU) ) {
            $this->showErrorMessage("ТУ не удалось сохранить");
            $this->redirect($location);
        } else {
            $this->showSuccessMessage($successMsg);
            $this->redirect("/techCondition/edit/{$idTU}");
        }
    }


    /**
     * @desc Страница журнала ТУ
     */
    public function list()
    {
        $this->data['title'] = 'Журнал ТУ';

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");

        $this->addJS("/assets/plugins/DataTables/DataTables-1.11.3/js/jquery.dataTables.min.js");
        $this->addJS("/assets/plugins/DataTables/ColReorder-1.5.5/js/dataTables.colReorder.min.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/ellipsis.js");

        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $r = rand();
        $this->addJs("/assets/js/tech-condition/list.js?v={$r}");

        $this->view('list');
    }


    /**
     * @desc Копирует ТУ
     * @param $id - ид ту источника
     */
    public function copy($id)
    {
        $this->checkPermission();
        /** @var TechCondition $tcModel */
        $tcModel = $this->model('TechCondition');

        $newId = $tcModel->copy($id);

        if ( !is_numeric($newId) || empty($newId) ) {
            $this->showErrorMessage("ТУ не удалось скопировать");
            $this->redirect("/techCondition/edit/{$id}");
        } else {
            $this->showSuccessMessage("ТУ успешно скопировано");
            $this->redirect("/techCondition/edit/{$newId}");
        }
    }


    /**
     * @desc Получение данных для журнала
     */
    public function getJournalAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var TechCondition $tcModel */
        $tcModel = $this->model('TechCondition');


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

        $data = $tcModel->getJournalList($filter);

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

    public function nonActualMethod($id)
    {
        $this->checkPermission();
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var TechCondition $tcModel */
        $tcModel = $this->model('TechCondition');

        $tcModel->nonActualMethod($id);

        $this->redirect("/ulab/techCondition/edit/{$id}");
    }

    public function actualMethod($id)
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var TechCondition $tcModel */
        $tcModel = $this->model('TechCondition');

        $tcModel->actualMethod($id);

        $this->redirect("/ulab/techCondition/edit/{$id}");
    }
}