<?php

/**
 * @desc Управление персоналом
 * Class UserController
 */
class UserController extends Controller
{
    /**
     * @return void
     * @hide true
     */
    public function index()
    {
        $this->redirect('/request/new/');
    }

    /**
    * @return void
     * @hide true
     */
    public function getStatusListAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        /** @var User $userModel */
        $userModel = $this->model('User');

        $info = $userModel->getStatusList();

        echo json_encode($info, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Страница со списком пользователей
     */
    public function list()
    {
        $this->model('Permission')->checkPermission(__FUNCTION__, __FILE__);

        $this->data['title'] = 'Пользователи';

        /** @var  User $user */
        $user = $this->model('User');
        /** @var  Permission $permissionModel */
        $permissionModel = $this->model('Permission');

        $this->data['role_list'] = $permissionModel->getPermission();
        $positionList = [];
        $departmentList = [];

        $users = $user->getUsers();

        foreach ($users as $us) {
            if ( !empty($us['DEPARTMENT_NAME']) && !in_array($us['DEPARTMENT_NAME'], $departmentList) ) {
                $departmentList[] = $us['DEPARTMENT_NAME'];
            }
            if ( !empty($us['WORK_POSITION']) && !in_array($us['WORK_POSITION'], $positionList) ) {
                $positionList[] = $us['WORK_POSITION'];
            }
		}

        $this->data['position_list'] = $positionList;
        $this->data['department_list'] = $departmentList;

        $this->data['department_all'] = $user->getDepartmentsList();

        $this->addCSS("/assets/plugins/select2/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");
        $this->addJs("/assets/plugins/select2/js/select2.min.js");

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
        $this->addJs("/assets/js/user/users-list.js?v={$r}");
        $this->addJs("/assets/js/user/users-form.js");

        $this->view('list');
    }

    /**
     * @desc Страница со статусами
     */
    public function status()
    {
        $this->model('Permission')->checkPermission(__FUNCTION__, __FILE__);

        $this->data['title'] = 'Статусы сотрудников';

        /** @var User $userModel */
        $userModel = $this->model('User');

        $this->data['statuses'] = $userModel->getStatusList();

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

        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');


        $r = rand();
        $this->addJs("/assets/js/user/status-list.js");

        $this->view('status');
    }

    /**
    * @return void
     * @hide true
     */
    public function getUsersForStatusAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

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

        /** @var User $userModel */
        $userModel = $this->model('User');

        $data = $userModel->getUsersForStatusJournal($filter);

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
    * @return void
     * @hide true
     */
    public function getUsersListAjax() {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        /** @var User $userModel */
        $userModel = $this->model('User');

        $data = $userModel->getAllUsersList();

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Обновлять роль у пользователя
     * @hide true
     */
    public function updateStatus()
    {
        /** @var  User $userModel */
        $userModel = $this->model('User');

        $userModel->updateStatus($_POST['user_id'], $_POST['status_id']);

        $this->showSuccessMessage("Статусы успешно обновлены");

        $this->redirect("/user/status/");
    }

    /**
     * @desc Обновлять роль у пользователя
     * @hide true
     */
    public function updateReplacement()
    {
        /** @var  User $userModel */
        $userModel = $this->model('User');

        $userModel->updateReplacement($_POST['user_id'], $_POST['replacementId']);

        $this->showSuccessMessage("Статусы успешно обновлены");

        $this->redirect("/user/status/");
    }

    /**
     * Принимает на вход массив
     * @desc Обновлять статус у нескольких пользователей сразу
     * @hide true
     */
    public function updateUsersStatus()
    {
        /** @var  User $userModel */
        $userModel = $this->model('User');

        $usersData = $_POST['array_update_users'];
        foreach ($usersData as $userData) {
              $userId = $userData['user_id'];
              $statusId = $userData['status_id'];
              $userModel->updateStatus($userId, $statusId);
        }

        $this->showSuccessMessage("Статусы успешно обновлены");
        //$this->redirect("/permission/users/");
    }

    /**
     * Принимает на вход массив
     * @desc Обновлять замены у нескольких пользователей сразу
     * @hide true
     */
    public function updateUsersReplacement()
    {
        /** @var  User $userModel */
        $userModel = $this->model('User');

        $usersData = $_POST['array_update_users'];
        foreach ($usersData as $userData) {
              $userId = $userData['user_id'];
              $replacementId = $userData['replacement_id'];
              $userModel->updateReplacement($userId, $replacementId);
        }

        $this->showSuccessMessage("Статусы успешно обновлены");
        //$this->redirect("/permission/users/");
    }

    /**
    * @return void
     * @hide true
     */
    public function updateUsersNote()
    {
        /** @var  User $userModel */
        $userModel = $this->model('User');

        $usersData = $_POST['array_update_users'];
        foreach ($usersData as $userData) {
              $userId = $userData['user_id'];
              $text = $userData['replacement_text'];
              $userModel->updateNote($userId, $text);
        }

        $this->showSuccessMessage("Статусы успешно обновлены");
        //$this->redirect("/permission/users/");
    }

        /**
    * @return void
     * @hide true
     */
    public function updateUsersJob()
    {
        /** @var  User $userModel */
        $userModel = $this->model('User');

        $usersData = $_POST['array_update_users'];
        foreach ($usersData as $userData) {
              $userId = $userData['user_id'];
              $text = $userData['job_title'];
              $userModel->updateJob($userId, $text);
        }

        $this->showSuccessMessage("Статусы успешно обновлены");
        //$this->redirect("/permission/users/");
    }

    /**
     * @hide
     */
    public function getUserHomeAjax() {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        /** @var  User $userModel */
        $userModel = $this->model('User');
        $data = $userModel->getUserHomePage() ?? "/ulab/request/new";
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}