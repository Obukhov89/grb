<?php

//require_once "/home/bitrix/www/protocol_generator/Morphos-master/vendor/autoload.php";

use \Bitrix\Main\Loader;


/**
 * Контроллер командировок
 * Class SecondmentController
 */
class SecondmentController extends Controller
{

    //  const USERS_CREATE_SECONDMENT = [84, 1, 8, 77, 98, 99, 76, 7, 26, 27, 43];

    // const USERS_CONFIRM_SECONDMENT = [84, 26, 27, 43, 8, 1, 77, 7]; //Подтвердить, Вернуть на доработку, Отклонить командировку
   // const USERS_PREPARE_DOCUMENTS = [1, 98, 99, 68]; //пользователи подготавливают документы "приказ и служебное задание"(уведомление) (25 - Екатерина Горшкова, 57 - Анастасия Кокозашвили, 89 - Полина Сербинова, 88 - Анастасия Серебренникова)
  //  const USERS_CONFIRM_REPORT = [84, 26, 27, 43, 8, 1, 99, 77, 7]; //пользователи подтверждающие отчёт

  //  const USERS_SAVE_FILES = [84, 98, 99, 43, 8, 1 ,77]; //пользователи сохраняют приказ и СЗ
  //  const USER_NOTIFICATION_OVERSPENDING = 8; //уведомление пользователя о перерасходе средств //8

 //   private $usersSaveInfo = [84, 26, 27, 43, 8, 1, 77, 7, 76]; //пользователи + выбранный пользователь, которые могут сохранять общую информацию
  //  private $usersSaveSecondment = [84, 26, 27, 43, 8, 1, 77, 7, 76, 99]; //пользователи + выбранный пользователь, которые могут сохранять общую информацию

  //  private $usersChangeReportPreparation = [84, 26, 27, 43, 8, 1, 99, 77, 1]; //пользователи меняют стадию на "Подготовка итчёта" или "В комендеровке"
  //  private $usersSaveReport = [84, 26, 27, 43, 8, 1, 99, 77, 7]; //пользователи сохраняют отчёт
    //private $usersSendApprove = [7, 1, 43]; //пользователи которые могут отправить на согласование + выбранный пользователь

    const USERS_ROOT = [84, 1, 8, 77, 98, 99, 76, 7, 80, 109, 219, 233, 61, 123, 83, 269, 88];
    public $usersSaveSecondment = [61, 212, 84, 1, 8, 77, 98, 99, 76, 7,
        26, 27, 29, 43, 45,32, 33, 73, 69, 18, 80, 109,
        219, 233, 123, 83]; //пользователи + выбранный пользователь, которые могут сохранять общую информацию
    const USERS_VERIFY_REPORT = [48, 8, 99, 219, 233]; //Пользователи проверяют отчёт(уведомление)

    // Структура управления (Начальник - подчиненный) устарело
//    const MANAGEMENT_STRUCTURE = [
//        26 => [30, 60, 45, 36, 28, 59, 67, 82, 105, 68, 18, 69],
//        27 => [32, 29, 39, 92, 102, 103, 104, 18, 69, 13, 111, 160],
//        43 => [30, 60, 45, 36, 28, 59, 67, 82, 105, 68, 26, 18, 69, 114, 115, 116, 95, 124, 8, 134, 135, 136, 218, 53],
//        33 => [73, 58, 12],
//        69 => [69],
//        18 => [...[18], ...[22, 23]],
//        9 => [212]
//      //  84 => [1]
//   //     68 => [32, 29, 39, 92, 102, 103, 104]
//        //    1 => [75, 12]
//    ];

    const DEPT_ARR = [
        58 => ["manager_id" => 26, "name" => "ОСК"],
        66 => ["manager_id" => 27, "name" => "ОСК-Д"],
        67 => ["manager_id" => 43, "name" => "ОСК-О"],
        53 => ["manager_id" => 33, "name" => "Администрация ИЦ"],
        72 => ["manager_id" => 222, "name" => "ОК"],
    ];

    static array $managementStructure = [
        26 => [30, 60, 45, 36, 28, 59, 67, 82, 105, 68, 18, 69],
        27 => [32, 29, 39, 92, 102, 103, 104, 18, 69, 13, 111, 160],
        43 => [30, 60, 45, 36, 28, 59, 67, 68, 82, 105, 68, 26, 18, 69, 114, 115, 116, 95, 124, 8, 134, 135, 136, 104, 162, 163, 53, 213, 214, 218, 223, 123, 224, 66, 238],
        123 => [30, 60, 45, 36, 28, 59, 67, 68, 82, 105, 68, 26, 18, 69, 114, 115, 116, 95, 124, 8, 134, 135, 136, 104, 162, 163, 53, 213, 214, 218, 223, 123, 224, 66, 238],
//        123 => [30, 60, 45, 36, 28, 59, 67, 68, 82, 105, 68, 26, 18, 69, 114, 115, 116, 95, 124, 8, 134, 135, 136, 104, 162, 163, 53, 213, 214, 218, 223, 123, 224],
        33 => [73, 58, 12],
        69 => [69],
        18 => [18],
        9 => [33],
		222 => [10, 239]
    ];

    static array $test = [1, 2 ,3];

//    public function __construct()
//    {
//        $secondment = $this->model('Secondment');
//
//        foreach (self::DEPT_ARR as $id => $dept) {
//            self::$managementStructure[$dept["manager_id"]] =  array_unique(array_merge(self::$managementStructure[$dept["manager_id"]], $secondment->getDeptUsers($id, $dept["manager_id"])));
//        }
//    }

    public static function getManagementStructure()
    {
        $secondment = new Secondment();

        foreach (self::DEPT_ARR as $id => $dept) {
            self::$managementStructure[$dept["manager_id"]] =  array_unique(array_merge(self::$managementStructure[$dept["manager_id"]], $secondment->getDeptUsers($id, $dept["manager_id"])));
        }

        return self::$managementStructure;
    }

    const DAILY_ALLOWANCE = 700; // Суточные

    // Файлы
    const SECONDMENT_FILE_CATEGORIES = [
        "ticket_payment" => "ticket_payment",
        "fuel_payment" => "fuel_payment",
        "fuel_payment_object" => "fuel_payment_object",
        "service_assignment" => "service_assignment",
        "signed_service_assignment" => "signed_service_assignment",
        "edict" => "edict",
        "signed_edict" => "signed_edict",
        "per_diem" => "per_diem",
        "accommodation" => "accommodation",
        "ticket_payment_fact" => "ticket_payment_fact",
        "fuel_payment_fact" => "fuel_payment_fact",
        "service_assignment_fact" => "service_assignment_fact",
        "fuel_payment_object_fact" => "fuel_payment_object_fact",
        "service_assignment_object_fact" => "service_assignment_object_fact",
        "edict_fact" => "edict_fact",
        "per_diem_fact" => "per_diem_fact",
        "accommodation_fact" => "accommodation_fact",
        "compensation" => "compensation",
        "memo_doc" => "memo_doc",
        "waybill" => "waybill"
    ];



    const STAGES = [
        "Новая",
        "Ожидает подтверждения",
        "Отклонена", "Нужна доработка",
        "Подготовка приказа и СЗ",
        "Согласована",
        "В командировке",
        "Подготовка отчета",
        "Проверка отчета",
        "Отчет подтвержден",
        "Завершена",
        "Отменена"
    ];

    /**
     * route /secondment/
     */
    public function index()
    {
        $this->redirect('/secondment/list');
    }

    /**
     * @desc Журнал командировок
     * journal
     * route /secondment/list/
     */
    public function list()
    {
        $this->data['title'] = 'Журнал командировок';

        /** @var Urer $user */
        $user = $this->model('User');
        /** @var Secondment $secondment */
        $secondment = $this->model('Secondment');
        /** @var Company $company */
        $company = $this->model('Company');
        /** @var Viewer $viewer */
        $viewer = $this->model('Viewer');
        /** @var Project $project */
        $project = $this->model('Project');

        $this->data["companyList"] = $secondment->getCompanyList();
        //$this->data["cityList"] = $secondment->getCityArr();

        $this->data['projects'] = array_merge([["id" => 0, "name" => "Без проекта"]], $project->getList());

        $this->data['users'] = $user->getUsersForSecondment();
        $this->data['date_start'] = $secondment->getDateStart();
        $this->data['date_end'] = $secondment->getDateEnd();
        $this->data['settlements'] = $secondment->getSettlementsData();
        $this->data['companies'] = $company->getList();
        $this->data['user_id'] = $user->getCurrentUser()["ID"];

       // $this->data['check_user'] = in_array($this->data['user_id'], self::USERS_ROOT) + key_exists($this->data['user_id'], self::MANAGEMENT_STRUCTURE);
        $this->data['check_user'] = in_array($this->data['user_id'], self::USERS_ROOT) + key_exists($this->data['user_id'], $this->getManagementStructure());

        $this->data['users_create_secondment'] = $this->usersSaveSecondment;

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

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
        $this->addJS('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $version = rand();
        $this->addJs('/assets/js/object.js?v=' . $version);
        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");

        $this->addCSS("/assets/css/object.css");
        $this->addCSS("/assets/css/secondment_card.css");

        $this->addJs("/assets/js/journals/secondment-list.js?v=" . rand());

        echo "<pre>";
        if ($_GET["admin"]) {
            var_dump($this->data["user_id"]);
        }
       // var_dump(count($this->data['companies']));
        //var_dump(in_array($this->data['user_id'], $this->data['users_create_secondment']));
       // var_dump(join(",", self::MANAGEMENT_STRUCTURE[1]));
        echo "</pre>";

        $this->view('list');
    }

    public function getListProcessingAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Secondment $secondment */
        $secondment = $this->model('Secondment');
        /** @var User $user */
        $user = $this->model('User');
        /** @var Viewer $viewer */
        $viewer = $this->model('Viewer');

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

        if (!empty($_POST['dateStart'])) {
            $filter['search']['dateStart'] = date('Y-m-d', strtotime($_POST['dateStart']));
            $filter['search']['dateEnd'] = date('Y-m-d', strtotime($_POST['dateEnd']));
        }

        if (!empty($_POST['everywhere'])) {
            $filter['search']['everywhere'] = $_POST['everywhere'];
        }

        if (!empty($_POST['stage_filter'])) {
            $filter['search']['stage_filter'] = $_POST['stage_filter'];
        }

       // $userId = $_SESSION['SESS_AUTH']['USER_ID'];
        $userId = $user->getCurrentUser()["ID"];

        if (in_array($userId, self::USERS_ROOT)) {

      //  } else if (key_exists($userId, self::MANAGEMENT_STRUCTURE)) {
        } else if (key_exists($userId, $this->getManagementStructure())) {
            //$userArr = self::MANAGEMENT_STRUCTURE[$userId];
            $userArr = $this->getManagementStructure()[$userId];
            $userArr[] = $userId;

            $filter["managerAccess"] = join(",",  $userArr);
        } else {
            $filter["managerAccess"] = $userId;
        }


        $data = $secondment->getDataToSecondmentJournal($filter);


        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];
        $test = $data['test'];

        unset($data['recordsTotal']);
        unset($data['recordsFiltered']);
        unset($data['test']);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
            "test" => $test,
            "isAdmin" => in_array($userId, self::USERS_ROOT)
        ];


        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    /**
     * route /secondment/insertUpdateInfo/
     */
    public function insertUpdateInfo()
    {

//        setlocale(LC_ALL, 'ru_RU.utf8');

        /** @var Secondment $secondment */
        $secondment = $this->model('Secondment');

        /** @var User $user */
        $user = $this->model('User');
        /** @var Viewer $viewer */
        $viewer = $this->model('Viewer');
        /** @var ObjectTest $object */
        $object = $this->model('ObjectTest');

        $secondmentId = (int)$_POST['secondment_id'] ?? null;
        $location = empty($secondmentId) ? '/secondment/list/' : "/secondment/card/{$secondmentId}";
        $successMsg = empty($secondmentId) ? 'Заявка успешно создана' : "Заявка успешно изменена";

        $_SESSION['secondment_post'] = $_POST;

        $currentUserId = $user->getCurrentUserId();

        $viewer->deleteView($secondmentId, "secondmentCard");

//

        //Общая информация
        if (isset($_POST['user_id'])) {
            $valid = $this->validateNumber($_POST['user_id'], 'Сотрудник', true);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['settlement_id'])) {
            $valid = $this->validateNumber($_POST['settlement_id'], 'Населенный пункт', false);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['object_id'])) {
            $valid = $this->validateNumber($_POST['object_id'], 'Объект', true);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

//        if (isset($_POST['contract_id'])) {
//            $valid = $this->validateNumber($_POST['contract_id'], 'Договор', false);
//            if (!$valid['success']) {
//                $this->showErrorMessage($valid['error']);
//                $this->redirect($location);
//            }
//        }

        if (isset($_POST['date_begin'])) {
            $valid = $this->validateDate($_POST['date_begin'], 'Дата начала командировки', true);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['date_end'])) {
            $valid = $this->validateDate($_POST['date_end'], 'Дата окончания командировки', true);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['total_days'])) {
            $valid = $this->validateNumber($_POST['total_days'], 'Всего дней', false);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['transport'])) {
            $valid = $this->validateField($_POST['transport'], 'Транспорт', false, 0);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['ticket_price'])) {
            $valid = $this->validateNumber($_POST['ticket_price'], 'Стоимость билетов', false);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['gasoline_consumption'])) {
            $valid = $this->validateNumber($_POST['gasoline_consumption'], 'Расход бензина', false);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['per_diem'])) {
            $valid = $this->validateNumber($_POST['per_diem'], 'Суточные', false);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['accommodation'])) {
            $valid = $this->validateNumber($_POST['accommodation'], 'Проживание', false);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['planned_expenses'])) {
            $valid = $this->validateNumber($_POST['planned_expenses'], 'Запланированные затраты(Итого)', false);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['company_id'])) {
            $valid = $this->validateNumber($_POST['company_id'], "Клиент");
            if (!$valid['success']) {
                $this->showErrorMessage("Не выбран Клиент");
                $this->redirect($location);
            }
        }



        if (!empty($secondmentId)) { // редактирование
            $secondmentData = $secondment->getSecondmentDataById($secondmentId);

            if (empty($secondmentData)) {
                $this->showErrorMessage("Карточка командировки с ИД {$secondmentId} не существует");
                $this->redirect($location);
            }

            if (!empty($secondmentData['stage']) && !in_array($secondmentData['stage'],
                    ['Новая', 'Нужна доработка'])) {
                $this->showErrorMessage("Запрещено сохранение данных общей информации на стадии - {$secondmentData['stage']}");
                $this->redirect("/secondment/card/{$secondmentId}");
            }



            $data = [
                'user_id' => $_POST['user_id'],
                'settlement_id' => $_POST['settlement_id'],
                'object_id' => $_POST['object_id'],
                'company_id' => $_POST['company_id'],
                'contract_id' => $_POST['contract_id'],
                'contract_type' => $_POST['contract_type'],
                'contract' => $_POST['contract'],
                'date_begin' => $_POST['date_begin'],
                'date_end' => $_POST['date_end'],
                'total_days' => $_POST['total_days'],
                'vehicle_id' => $_POST['transport'],
                'content' => $_POST['content'],
                'comment' => $_POST['comment'],
                'ticket_price' => $_POST['ticket_price'],
                'comment_ticket_price' => $_POST['comment_ticket_price'],
                'gasoline_consumption' => $_POST['gasoline_consumption'],
                'comment_gasoline_consumption' => $_POST['comment_gasoline_consumption'],
                'gasoline_consumption_object' => $_POST['gasoline_consumption_object'],
                'comment_gasoline_consumption_object' => $_POST['comment_gasoline_consumption_object'],
                'per_diem' => $_POST['per_diem'],
                'comment_per_diem' => $_POST['comment_per_diem'],
                'accommodation' => $_POST['accommodation'],
                'comment_accommodation' => $_POST['comment_accommodation'],
                'planned_expenses' => $_POST['planned_expenses'],
                'comment_planned_expenses' => $_POST['comment_planned_expenses']

            ];

            $objectId = intval($_POST['object_id']);

            $objectData = [
                "CITY_ID" => $_POST['settlement_id'],
                "ID_COMPANY" => $_POST['company_id'],
                "KM" => $_POST['kilometer']
            ];

            for ($i = 0; $i < count($_POST["other"]); $i++) {
                $otherId = $_POST["other_id"][$i];

                $otherData = [
                    "secondment_id" => $secondmentId,
                    "sum" => $_POST["other"][$i],
                    "comment" => $_POST["comment_other"][$i]
                ];

                if (is_null($otherId)) {
                    $secondment->create($otherData, 'secondment_other');
                } else {
                    $secondment->update($otherData, 'secondment_other', $otherId);


                }

                for ($j = 0; $j < count($_FILES["other"]["name"][$i]); $j++) {

                    $dir = "upload/secondment/other/{$secondmentId}/{$otherId}";
                    $secondment->saveFile($dir, $_FILES["other"]["name"][$i][$j], $_FILES["other"]["tmp_name"][$i][$j]);
                    echo $_FILES[$i]["name"][$j] . PHP_EOL;
                }
            }




            if (!empty($_POST["file_payment_delete"])) {
                $pathArr = explode(",", $_POST["file_payment_delete"]);
                foreach ($pathArr as $path) {
                    if ($path != "") {
                        unlink($_SERVER["DOCUMENT_ROOT"] . $path);
                    }
                }
            }

            foreach ($_FILES as $category => $files) {
                for ($i = 0; $i < count($files["name"]); $i++) {
                    if (in_array($category, self::SECONDMENT_FILE_CATEGORIES)) {
                        $dir = "upload/secondment/$category/{$secondmentId}";
                        $secondment->saveFile($dir, $files["name"][$i], $files["tmp_name"][$i]);
                    }
                }
            }

            $secondmentUpdate = $secondment->update($data, 'secondment', $secondmentId);
            $objectUpdate = $object->update($objectData, 'DEV_OBJECTS', $objectId);

            $oborudData = [];
            $secondment->delete("secondment_oborud", "secondment_id = {$secondmentId}");

            if (!empty($_POST["oborud"])) {
                foreach ($_POST["oborud"] as $oborud) {
                    $oborudData = [
                        "secondment_id" => $secondmentId,
                        "oborud_id" => $oborud
                    ];

                    $secondment->create($oborudData, 'secondment_oborud');
                }
            }


            if ($secondmentUpdate === 1) {
                unset($_SESSION['secondment_post']);
                $this->showSuccessMessage($successMsg);

                $this->redirect("/secondment/card/{$secondmentId}");
            }

            if ($secondmentUpdate !== 1) {
                $this->showErrorMessage("Не удалось обновить данные командировки");
                $this->redirect($location);
            }
        } else {

            $data = [
                'user_id' => $_POST['user_id'],
                'settlement_id' => $_POST['settlement_id'],
                'company_id' => $_POST['company_id'],
                'object_id' => $_POST['object_id'],
                'date_begin' => $_POST['date_begin'],
                'date_end' => $_POST['date_end'],
                'project_id' => $_POST['project_id'] ?? 0,
                'creator_user_id' => $currentUserId
            ];
            
            $secondmentId = $secondment->create($data, 'secondment');

            if ($secondmentId) {
                $this->redirect("/secondment/card/{$secondmentId}");
            }

            if (empty($secondmentId)) {
                $this->showErrorMessage("Не удалось создать заявку на командировку");
                $this->redirect($location);
            }

            if (!empty($secondmentId)) {
                unset($_SESSION['secondment_post']);
                $this->showSuccessMessage($successMsg);
                $this->redirect($location);
            }
        }
    }

    /**
     * route /secondment/insertUpdateDocuments/
     */
    public function insertUpdateDocuments()
    {
        setlocale(LC_ALL, 'ru_RU.utf8');
        $location = "/secondment/list/";
        $successMsg = '';
        $errorMsg = '';

        /** @var Secondment $secondment */
        $secondment = $this->model('Secondment');
        /** @var Viewer $viewer */
        $viewer = $this->model('Secondment');

        $secondmentId = (int)$_POST['secondment_id'] ?? null;

        $viewer->deleteView($secondmentId, "secondmentCard");

        if (!empty($_POST["file_delete"])) {
            $pathArr = explode(",", $_POST["file_delete"]);
            foreach ($pathArr as $path) {
                if ($path != "") {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $path);
                }
            }
        }


        if (!empty($secondmentId)) {
            foreach ($_FILES as $key => $file) {
                if (!empty($file['tmp_name'])) {

                    $edict = $secondment->saveAnyFile("secondment/{$key}/{$secondmentId}", $file, $file["name"]);

                    if (!empty($edict['success'])) {
                        $successMsg .= 'Приказ успешно загружен.';
                    }

                    if (!empty($edict['error'])) {
                        $errorMsg .= $edict['error'];
                    }
                }
            }
        }

        $this->redirect("/secondment/card/{$secondmentId}");

    }

    /**
     * route /secondment/insertUpdateFiles/
     */
    public function insertUpdateFiles()
    {
        setlocale(LC_ALL, 'ru_RU.utf8');
        $location = "/secondment/list/";
        $successMsg = '';
        $errorMsg = '';

        /** @var Secondment $secondment */
        $secondment = $this->model('Secondment');
        /** @var  Request $request */
        $request = $this->model('Request');
        /** @var Urer $user */
        $user = $this->model('User');
        /** @var Viewer $viewer */
        $viewer = $this->model('Viewer');

        $secondmentId = (int)$_POST['secondment_id'] ?? null;
        $dirEdict = "/home/bitrix/www/ulab/upload/secondment/edict/{$secondmentId}/";
        $dirServiceAssignment = "/home/bitrix/www/ulab/upload/secondment/service_assignment/{$secondmentId}/";

        $viewer->deleteView($secondmentId, "secondmentCard");

        $currentUserId = $user->getCurrentUserId();

        $data = [
           "edict_number" => $_POST["edict_number"]
        ];

        $secondment->update($data, "secondment", $secondmentId);

        if (!empty($_POST["file_delete"])) {
            $pathArr = explode(",", $_POST["file_delete"]);
            foreach ($pathArr as $path) {
                if ($path != "") {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $path);
                }
            }
        }


        if (!empty($secondmentId)) {
            $secondmentData = $secondment->getSecondmentDataById($secondmentId);

            if (empty($secondmentData)) {
                $this->showErrorMessage("Карточка командировки с ИД {$secondmentId} не существует");
                $this->redirect($location);
            }

            foreach ($_FILES as $key => $file) {
                if (isset($file['name']) && is_array($file['name'])) {
                    foreach ($file['name'] as $index => $fileName) {
                        if ($file['error'][$index] === 4 || empty($file['tmp_name'][$index])) {
                            continue;
                        }
                        $normalizedFile = [
                            'name' => $file['name'][$index],
                            'type' => $file['type'][$index],
                            'tmp_name' => $file['tmp_name'][$index],
                            'error' => $file['error'][$index],
                            'size' => $file['size'][$index]
                        ];

                        $result = $secondment->saveAnyFile("secondment/{$key}/{$secondmentId}", $normalizedFile, $fileName);

                        if (!empty($result['success'])) {
                            $successMsg .= "Файл {$fileName} успешно загружен. ";
                        }

                        if (!empty($result['error'])) {
                            $errorMsg .= $result['error'] . ' ';
                        }
                    }
                } else {
                    if ($file['error'] === 4 || empty($file['tmp_name'])) {
                        continue;
                    }

                    $result = $secondment->saveAnyFile("secondment/{$key}/{$secondmentId}", $file, $file['name']);

                    if (!empty($result['success'])) {
                        $successMsg .= "Файл {$file['name']} успешно загружен. ";
                    }

                    if (!empty($result['error'])) {
                        $errorMsg .= $result['error'] . ' ';
                    }
                }
            }

//            if (!empty($_FILES['edict']['tmp_name'])) {
//                $edict = $secondment->saveAnyFile("secondment/edict/{$secondmentId}", $_FILES['edict'], $_FILES['edict']["name"]);
//
//                if (!empty($edict['success'])) {
//                    $successMsg .= 'Приказ успешно загружен.';
//                }
//
//                if (!empty($edict['error'])) {
//                    $errorMsg .= $edict['error'];
//                }
//            }
//
//
//            if (!empty($_FILES['service_assignment']['tmp_name'])) {
//                $serviceAssignment = $secondment->saveAnyFile("secondment/service_assignment/{$secondmentId}", $_FILES['service_assignment'], 'service_assignment');
//
//                if (!empty($serviceAssignment['success'])) {
//                    $successMsg .= 'Служебное задание успешно загружено.';
//                }
//
//                if (!empty($serviceAssignment['error'])) {
//                    $errorMsg .= $serviceAssignment['error'];
//                }
//            }

            $filesEdict = $request->getFilesFromDir($dirEdict);
            $filesServiceAssignment = $request->getFilesFromDir($dirServiceAssignment);
            if ((isset($_POST['stage_ready']) && !empty($secondmentId) && !empty($filesEdict)
                && !empty($filesServiceAssignment)) || isset($_POST["no_edict2"])) {
                $data = [
                    'stage' => 'Согласована'
                ];

                $response = $secondment->update($data, 'secondment', $secondmentId);

                if ($response === 1) {
                    $successMsg .= 'Стадия успешно изменена.';

                    $message = 'Подготовиться к командировке ';

                    $notify = [
                        "NOTIFY_TITLE" => $secondmentData['title'],
                        "TO_USER_ID" => $secondmentData['user_id'],
                        "FROM_USER_ID" => $currentUserId,
                        "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                        "NOTIFY_MESSAGE" => "{$message}<a href='{URI}/ulab/secondment/card/{$secondmentData['s_id']}'>
                            {$secondmentData['title']}
                        </a>",
                    ];
                    CIMNotify::Add($notify);
                }
            }

            if (!empty($successMsg)) {
                $this->showSuccessMessage($successMsg);
                $this->redirect("/secondment/card/{$secondmentId}");
            }

            if (!empty($errorMsg)) {
                $this->showErrorMessage($errorMsg);
                $this->redirect("/secondment/card/{$secondmentId}");
            }

            $this->showSuccessMessage("Успешно изменено!");
            $this->redirect("/secondment/card/{$secondmentId}");

        }
    }

    /**
     * route /secondment/insertUpdateReport/
     */
    public function insertUpdateReport()
    {
       // setlocale(LC_ALL, 'ru_RU.utf8');
        $location = "/secondment/list/";
        $successMsg = 'Отчет успешно сохранён';
        $errorMsg = '';

        /** @var Secondment $secondment */
        $secondment = $this->model('Secondment');
        /** @var  Request $request */
        $request = $this->model('Request');
        /** @var Urer $user */
        $user = $this->model('User');
        /** @var Viewer $viewer */
        $viewer = $this->model('Viewer');
        $transport = $this->model('Transport');


        $_SESSION['secondment_post'] = $_POST;

        $secondmentId = (int)$_POST['secondment_id'] ?? null;
        $currentUserId = $user->getCurrentUserId();


        $viewer->deleteView($secondmentId, "secondmentCard");

        //Отчет
        if (isset($_POST['total_spent'])) {
            $valid = $this->validateNumber($_POST['total_spent'], 'Всего потрачено', false);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['comment'])) {
            $valid = $this->validateField($_POST['comment'], 'Комментарий сотрудника', false, 0);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['memo'])) {
            $valid = $this->validateField($_POST['memo'], 'Служебная записка', false, 0);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }

        if (isset($_POST['overspending'])) {
            $valid = $this->validateNumber($_POST['overspending'], 'Перерасход', false);
            if (!$valid['success']) {
                $this->showErrorMessage($valid['error']);
                $this->redirect($location);
            }
        }


        if (!empty($secondmentId)) { // редактирование
            $secondmentData = $secondment->getSecondmentDataById($secondmentId);
            $transportData = [];
            if (!empty($secondmentData["vehicle_id"])) {
                $transportData = $transport->getTransportById($secondmentData["vehicle_id"]);
            }


            if (empty($secondmentData)) {
                $this->showErrorMessage("Карточка командировки с ИД {$secondmentId} не существует");
                $this->redirect($location);
            }

            if (!empty($secondmentData['stage']) && !in_array($secondmentData['stage'],
                    ['Подготовка отчета', 'Затраты не подтверждены'])) {
                $this->showErrorMessage("Запрещено сохранение данных отчёта на стадии - {$secondmentData['stage']}");
                $this->redirect("/secondment/card/{$secondmentId}");
            }

            $data = [
                'total_spent' => $_POST['total_spent'],
                'comment' => $_POST['comment'],
                'memo' => $_POST['memo'],
                'overspending' => $_POST['overspending'],
                "ticket_price_fact" => $_POST['ticket_price_fact'],
                "comment_ticket_price_fact" => $_POST['comment_ticket_price_fact'],
                "gasoline_consumption_fact" => $_POST['gasoline_consumption_fact'],
                "comment_gasoline_consumption_fact" => $_POST['comment_gasoline_consumption_fact'],
                "gasoline_consumption_object_fact" => $_POST['gasoline_consumption_object_fact'],
                "comment_gasoline_consumption_object_fact" => $_POST['comment_gasoline_consumption_object_fact'],
                "per_diem_fact" => $_POST['per_diem_fact'],
                "comment_per_diem_fact" => $_POST['comment_per_diem_fact'],
                "accommodation_fact" => $_POST['accommodation_fact'],
                "comment_accommodation_fact" => $_POST['comment_accommodation_fact'],

            ];

            $compensationSum = (floatval($_POST["gasoline_consumption_fact"]) + floatval($_POST["gasoline_consumption_object_fact"])) / 2;
            $compensationData = [
                "sum" => $compensationSum,
                "secondment_id" => $secondmentId

            ];

           // $secondment->create($compensationData, "secondment_compensations");
            if ($transportData["personal"] == 1) {
                $secondment->insertUpdateCompensation($compensationData["sum"], $compensationData["secondment_id"]);
            }


            $secondmentUpdate = $secondment->update($data, 'secondment', $secondmentId);

//            $secondment->pre($_POST, true);

            for ($i = 0; $i < count($_POST["additional"]); $i++) {
                $additionalId = $_POST["additional_id"][$i];

                $additionalData = [
                    "secondment_id" => $secondmentId,
                    "sum" => $_POST['additional'][$i],
                    "comment" => $_POST['comment_additional'][$i]
                ];

                if (is_null($additionalId)) {
                    $secondment->create($additionalData, 'secondment_additional');
                } else {
                    $secondment->update($additionalData, 'secondment_additional', $additionalId);
                }

                for ($j = 0; $j < count($_FILES["additional"]["name"][$i]); $j++) {
                    $dir = "upload/secondment/additional/{$secondmentId}/{$additionalId}";
                    $secondment->saveFile($dir, $_FILES["additional"]["name"][$i][$j], $_FILES["additional"]["tmp_name"][$i][$j]);
                    echo $_FILES[$i]["name"][$j] . PHP_EOL;
                }
            }




            for ($i = 0; $i < count($_POST["other_fact"]); $i++) {
                $otherId = $_POST["other_id"][$i];

                $otherData = [
                    "secondment_id" => $secondmentId,
                    "sum_fact" => $_POST["other_fact"][$i],
                    "comment_fact" => $_POST["comment_other_fact"][$i]
                ];


                $secondment->update($otherData, 'secondment_other', $otherId);

                //   $secondment->update($otherData, 'secondment_other', $otherId);

                for ($j = 0; $j < count($_FILES["other_fact"]["name"][$i]); $j++) {

                    $dir = "upload/secondment/other_fact/{$secondmentId}/{$otherId}";
                    $secondment->saveFile($dir, $_FILES["other_fact"]["name"][$i][$j], $_FILES["other_fact"]["tmp_name"][$i][$j]);
                    echo $_FILES[$i]["name"][$j] . PHP_EOL;
                }
            }


            if (!empty($_POST["file_payment_delete"])) {
                $pathArr = explode("\n", $_POST["file_payment_delete"]);
                foreach ($pathArr as $path) {
                    if ($path != "") {
                        unlink($_SERVER["DOCUMENT_ROOT"] . $path);
                    }
                }
            }

            if (!empty($_POST["file_delete"])) {
                $pathArr = explode("\n", $_POST["file_delete"]);
                foreach ($pathArr as $path) {
                    if ($path != "") {
                        unlink($_SERVER["DOCUMENT_ROOT"] . $path);
                    }
                }
            }

            setlocale(LC_ALL, 'ru_RU.utf8');
            foreach ($_FILES as $category => $files) {
                for ($i = 0; $i < count($files["name"]); $i++) {
                    if (in_array($category, self::SECONDMENT_FILE_CATEGORIES)) {
                        $dir = "upload/secondment/$category/{$secondmentId}";
                      //  $secondment->saveFile($dir, $files["name"][$i], $files["tmp_name"][$i]);
                        $secondment->saveFile($dir, $files["name"][$i], $files["tmp_name"][$i]);
                    }
                }
            }

            if ($secondmentUpdate === 1) {
                unset($_SESSION['secondment_post']);
                $this->showSuccessMessage($successMsg);
                $this->redirect("/secondment/card/{$secondmentId}");
            }

            if ($secondmentUpdate !== 1) {
                $this->showErrorMessage("Не удалось обновить данные отчёта");
                $this->redirect($location);
            }


            echo "<pre>";
            var_dump($compensationSum);
            var_dump($_POST);


            echo "</pre>";

        }
    }

    public function deleteCardAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        /** @var  Request $request */
        $secondment = $this->model('Secondment');

        $location = "/secondment/list/";

        $secondmentId = intval($_POST['secondment_id']);

        $secondmentCard = $secondment->getSecondmentDataById($secondmentId);

        if ($secondmentCard["stage"] == "Новая") {
            $data = [
                'del' => 1
            ];

            $secondment->update($data, 'secondment', $secondmentId);
            $this->showSuccessMessage("Заявка успешно удалена!");

            echo json_encode(1);
        } else {

            echo json_encode("Ошибка! Заявку можно удалить только на стадии \"Новая\"");
        }


    }

    /**
     * business trip card
     * route /secondment/card/
     * @param int $id
     */
    public function card(int $id)
    {
        $location = "/secondment/list/";

        if (empty($id) || $id < 0) {
            $this->redirect($location);
        }


        /** @var Secondment $secondment */
        $secondment = $this->model('Secondment');
        /** @var Urer $user */
        $user = $this->model('User');
        /** @var Company $company */
        $company = $this->model('Company');
        /** @var Request $request */
        $request = $this->model('Request');
        /** @var Transport $transport */
        $transport = $this->model('Transport');
        /** @var Viewer $viewer */
        $viewer = $this->model('Viewer');
        /** @var Oborud $oborud */
        $oborud = $this->model('Oborud');

        $this->data['title'] = 'Карточка командировки';
        $this->data['secondment_id'] = $id;

        $this->data['secondment'] = $secondment->getSecondmentDataById($id);


        if (empty($this->data['secondment']) || $this->data['secondment']['del'] == 1) {
            $this->showErrorMessage("Заявки с ИД {$id} не существует");
            $this->redirect($location);
        }


        $this->data['stage_name'] = $this->data['secondment']['stage'] ?: 'Стадия отсутствует';
        $objectId = $this->data['secondment']['object_id'] ?? null;
        $companyId = $this->data['secondment']['company_id'] ?? null;
        $this->data['user_id'] = $this->data['secondment']['user_id'] ?? null;

        $this->data["stage_list"] = self::STAGES;
        $this->data["users_root"] = self::USERS_ROOT;



        $currentUserId = $user->getCurrentUserId();
        $this->data['users'] = $user->getUsersForSecondment();
       // $this->data['settlements'] = $secondment->getSettlementsData();
        $this->data['objects'] = $secondment->getObjectDataByCompanyId($companyId);
        $this->data['object'] = isset($objectId) ? $secondment->getObjectDataById($objectId) : null;
        $this->data['company'] = $company->getCompanyDataByCompanyId($companyId);
        $arFilter = ["=ID" => $companyId];
      //  $this->data['company'] = $company->getList($arFilter)[0];
     //   $this->data['organization'] = $company->getCompanyDataByCompanyId($companyId);
        $this->data['contracts'] = $request->getContractsByCompanyId($companyId);

        $this->data['secondmentContracts'] = $secondment->getContractsByCompanyId($companyId);

        $this->data['companies'] = $company->getList();
        $selectedUser = $user->getUserData($this->data['user_id']);
        $stageColor = $secondment->getStageColor($this->data['stage_name']);
        $this->data["vehicles"] = $transport->getTransportList();
        $this->data["fuel_types"] = $transport->getFuelTypes();

        $vehicleId = $this->data['secondment']['vehicle_id'];
        $fuelPrice = $this->data["vehicles"][$vehicleId]["price"];
        $km = $this->data['object']['KM'];
        $fuelConsumption = $this->data["vehicles"][$vehicleId]["consumption_rate"];

        $this->data["archiveList"] = $secondment->getArchiveListBySecondmentId($id);

       // $this->data["fuel_consumption"] = round($fuelConsumption / 100 * $km * $fuelPrice, 2);

        $this->data["compensationItem"] = $secondment->getCompensationBySecondmentId($id);

        $this->data['stage_border_color'] = $stageColor['border_color'] ?? '';
        $this->data['title'] = $this->data['secondment']['title'] ?? '';
        $this->data['work_position'] = $selectedUser['WORK_POSITION'] ?? '';
     //   $this->data['settlement_id'] = $this->data['object']['CITY_ID'] ?? null;

        $this->data['settlement_title'] = $this->data['object']['settlement'] ?? null;
        $this->data['other_fields'] = $secondment->getOtherFieldsById($id);
        $this->data['additional_fields'] = $secondment->getAdditionalFieldsById($id);


        $this->data['object_id'] = $this->data['secondment']['object_id'] ?? null;
        // TODO у обьекта или города создать километраж
        $this->data['kilometer'] = $this->data['object']['KM'] ?? null;
        $this->data['date_begin'] = $this->data['secondment']['date_begin'] ?: date('Y-m-d');
        $this->data['date_end'] = $this->data['secondment']['date_end'] ?: date('Y-m-d');
        $dateDiff = date_diff(new DateTime($this->data['date_begin']), new DateTime($this->data['date_end']))->days;
        $this->data['total_days'] = $this->data['secondment']['total_days'] ?: $dateDiff + 1;
        $this->data['contract_id'] = $this->data['secondment']['contract_id'] ?? null;
        $this->data['content'] = $this->data['secondment']['content'] ?? '';
        $this->data['ticket_price'] = trim($this->data['secondment']['ticket_price']) ?: null;
        $this->data['comment_ticket_price'] = $this->data['secondment']['comment_ticket_price'] ?: '';
        $this->data['gasoline_consumption'] = $this->data['secondment']['gasoline_consumption'] ?: null;
        $this->data['comment_gasoline_consumption'] = $this->data['secondment']['comment_gasoline_consumption'] ?: '';
        $this->data['gasoline_consumption_object'] = $this->data['secondment']['gasoline_consumption_object'] ?: null;
        $this->data['comment_gasoline_consumption_object'] = $this->data['secondment']['comment_gasoline_consumption_object'] ?: '';
    //    $this->data['per_diem'] = $this->data['secondment']['per_diem'] ?: null;
        if ($this->data['total_days'] == 1) {
            $this->data['per_diem'] = 0;
        } else {
            $this->data['per_diem'] = $this->data['total_days'] * self::DAILY_ALLOWANCE;
        }

        $this->data['comment_per_diem'] = $this->data['secondment']['comment_per_diem'] ?: '';
        $this->data['accommodation'] = $this->data['secondment']['accommodation'] ?: null;
        $this->data['comment_accommodation'] = $this->data['secondment']['comment_accommodation'] ?: '';
        $this->data['other'] = $this->data['secondment']['other'] ?: null;
        $this->data['comment_other'] = $this->data['secondment']['comment_other'] ?: '';
        $this->data['planned_expenses'] = $this->data['secondment']['planned_expenses'] ?: null;
        $this->data['comment_planned_expenses'] = $this->data['secondment']['comment_planned_expenses'] ?: '';
        $this->data['total_spent'] = $this->data['secondment']['total_spent'] ?: null;
        $this->data['vehicle_id'] = $this->data['secondment']['vehicle_id'] ?: null;
        $this->data['comment'] = $this->data['secondment']['comment'] ?? '';
        $this->data['memo'] = $this->data['secondment']['memo'] ?: '';
        $this->data['overspending'] = $this->data['secondment']['overspending'] ?: null;


        $this->data["archiveSum"] = 0;

        foreach ($this->data["archiveList"] as $index => $archive) {
            if ($index !== count($this->data["archiveList"]) - 1) {
                $this->data["archiveList"][$index]["extraPayment"] = $this->data["archiveList"][$index + 1]["planned_expenses"] - $this->data["archiveList"][$index]["planned_expenses"];
            } else {
                $this->data["archiveList"][$index]["extraPayment"] = $this->data['planned_expenses'] - $archive["planned_expenses"];
            }

            $this->data["archiveSum"] += $this->data["archiveList"][$index]["extraPayment"];

        }

        $secondmentOborud = $oborud->getListBySecondmentId($id);
        $this->data["secondmentOborudArr"] = array_keys($secondmentOborud);
       // $this->data["oborudList"] = $oborud->getList();


        $confirmReport = $secondment->getConfirmRejectReport($id);
        $this->data['confirmation_current_user'] = $secondment->getConfirmationCurrentUser($id, $currentUserId);

        $this->data['users_confirmed_report'] = [];
        if (!empty($confirmReport)) {
            foreach ($confirmReport as $key => $val) {
                if (empty($val['user_id'])) {
                    continue;
                }

                $userData = $user->getUserData($val['user_id']);

                if (empty($userData['short_name'])) {
                    continue;
                }

                $this->data['users_confirmed_report'][$key]['action'] = $val['action'] ?? 0;
                $this->data['users_confirmed_report'][$key]['short_name'] = $userData['short_name'] ?: '';
                $this->data['users_confirmed_report'][$key]['date'] = $val['created_at'] ? date('d.m.Y', strtotime($val['created_at'])) : '';
            }
        }

        $userId = $user->getCurrentUser()["ID"];

        $employeeId = $this->data['user_id'];
        $this->data['is_confirm_secondment'] = in_array($userId, $this->usersSaveSecondment); // $this->canHeadApprove($employeeId, $currentUserId);
        $this->data['is_confirm_report'] = in_array($userId, $this->usersSaveSecondment);

        if (!empty($this->data['user_id'])) {
            $this->usersSaveSecondment[] = $this->data['user_id'];
        }

        $this->data['is_save_info'] = in_array($userId, $this->usersSaveSecondment);
        $this->data['is_save_secondment'] = in_array($userId, $this->usersSaveSecondment);
        $this->data['is_save_report'] = in_array($userId, $this->usersSaveSecondment);
        $this->data['is_may_save_files'] = in_array($userId, $this->usersSaveSecondment);
        $this->data['is_can_prepare_report'] = in_array($userId, $this->usersSaveSecondment);


        foreach (self::SECONDMENT_FILE_CATEGORIES as $category) {
            $this->data['file'][$category]['files'] = [];
            $dirEdict = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/secondment/{$category}/{$id}/";
            $pathEdict = "/ulab/upload/secondment/{$category}/{$id}/";
            $filesEdict = $request->getFilesFromDir($dirEdict);

            if (!empty($filesEdict)) {
                foreach ($filesEdict as $index => $fileEdict) {
                    if (in_array($category, ["edict", "service_assignment", "signed_edict", "signed_service_assignment"])) {
                        $this->data[$category][$index]["dir"] = $pathEdict;
                        $this->data[$category][$index]["file"] = $fileEdict;
                    } else {
                        $this->data[$category]['dir'] = $pathEdict;
                        $this->data[$category]['file'] = $fileEdict;
                    }

                }
            }
        }


//        // Загрузить приказ
//        $this->data['file']['edict']['files'] = [];
//        $dirEdict = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/secondment/edict/{$id}/";
//        $pathEdict = "/ulab/upload/secondment/edict/{$id}/";
//        $filesEdict = $request->getFilesFromDir($dirEdict);
//        $fileEdict = end($filesEdict);
//
//        $this->data['edict']['dir'] = $pathEdict;
//        $this->data['edict']['file'] = $fileEdict;
//
//        // Загрузить служебное задание
//        $this->data['file']['service_assignment']['files'] = [];
//        $dirServiceAssignment = "/home/bitrix/www/ulab/upload/secondment/service_assignment/{$id}/";
//        $pathServiceAssignment = "/ulab/upload/secondment/service_assignment/{$id}/";
//        $filesServiceAssignment = $request->getFilesFromDir($dirServiceAssignment);
//        $fileServiceAssignment = end($filesServiceAssignment);
//
//        $this->data['service_assignment']['dir'] = $pathServiceAssignment;
//        $this->data['service_assignment']['file'] = $fileServiceAssignment;


        // Загрузить служебную записку
//        $this->data['file']['memo']['files'] = [];
//        $dirServiceAssignment  = "/home/bitrix/www/ulab/upload/secondment/memo/{$id}/";
//        $pathServiceAssignment = "/ulab/upload/secondment/memo/{$id}/";
//        $filesServiceAssignment = $request->getFilesFromDir($dirServiceAssignment);
//        $fileServiceAssignment = end($filesServiceAssignment);
//
//        $this->data['memo']['dir'] = $pathServiceAssignment;
//        $this->data['memo']['file'] = $fileServiceAssignment;

     //   $fileArr = $request->getFilesFromDir(UPLOAD_DIR . "/secondment/ticket_payment/{$id}");;

        foreach (self::SECONDMENT_FILE_CATEGORIES as $category) {
            $this->data["fileArr"][$category] = $request->getFilesFromDir(UPLOAD_DIR . "/secondment/{$category}/{$id}");
        }

        foreach ($this->data['other_fields'] as $index => $field) {
            $this->data["fileArr"]["other"][$index] = $request->getFilesFromDir(UPLOAD_DIR . "/secondment/other/{$id}/" . $field["id"]);
            $this->data["fileArr"]["other_fact"][$index] = $request->getFilesFromDir(UPLOAD_DIR . "/secondment/other_fact/{$id}/" . $field["id"]);
        }

        foreach ($this->data['additional_fields'] as $index => $field) {
            $this->data["fileArr"]["additional"][$index] = $request->getFilesFromDir(UPLOAD_DIR . "/secondment/additional/{$id}/" . $field["id"]);
        }



        // Прикрепление счета на оплату билетов
        $userFiles = $request->getFilesFromDir(UPLOAD_DIR . "/secondment/ticket_payment/{$id}");

        $this->data['ticket_payment'] = [];
        foreach ($userFiles as $file) {
            $imgLinc = URI . '/assets/images/unknown.png';
            $patternImg = "/\.(png|jpg|jpeg)$/i";
            if (preg_match($patternImg, $file)) {
                $imgLinc = URI . "/upload/secondment/ticket_payment/{$id}/{$file}";
            }
            $patternPdf = "/\.(pdf)$/i";
            if (preg_match($patternPdf, $file)) {
                $imgLinc = URI . "/assets/images/pdf.png";
            }

            $this->data['ticket_payment'][] = [
                'name' => $file,
                'img' => $imgLinc
            ];
        }

        // Прикрепление чеков для отчета
        $checksFiles = $request->getFilesFromDir(UPLOAD_DIR . "/secondment/checks/{$id}");

        $this->data['checks_files'] = [];
        foreach ($checksFiles as $file) {
            $imgLinc = URI . '/assets/images/unknown.png';
            $patternImg = "/\.(png|jpg|jpeg)$/i";
            if (preg_match($patternImg, $file)) {
                $imgLinc = URI . "/upload/secondment/checks/{$id}/{$file}";
            }
            $patternPdf = "/\.(pdf)$/i";
            if (preg_match($patternPdf, $file)) {
                $imgLinc = URI . "/assets/images/pdf.png";
            }

            $this->data['checks_files'][] = [
                'name' => $file,
                'img' => $imgLinc
            ];
        }


        $this->data["manager_id_list"] = array_keys($this->getManagementStructure());

        $this->data["is_manager"] = in_array($userId, $this->data["manager_id_list"]);
        $this->data["is_root"] = in_array($userId, self::USERS_ROOT) || in_array($userId, [123]);

        // Добавляет запись "просмотрено"
//        $viewer->insertUpdateView($currentUserId, $id, "secondmentCard");

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");

        $this->addCSS("/assets/plugins/dropzone/css/basic.css");
        $this->addCSS("/assets/plugins/dropzone/dropzone3.css");
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

        $this->addJS("/assets/plugins/DataTables/DataTables-1.11.3/js/jquery.dataTables.min.js");
        $this->addJS("/assets/plugins/DataTables/ColReorder-1.5.5/js/dataTables.colReorder.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/dataTables.buttons.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.colVis.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.print.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.html5.min.js");
        $this->addJS("/assets/plugins/DataTables/JSZip-2.5.0/jszip.min.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/ellipsis.js");
        $this->addJS("/assets/plugins/pdf-lib/pdf-lib-1.4.0.js");

        $this->addCSS("/assets/css/object.css?v=" . rand());
        $this->addCSS("/assets/css/secondment_card.css?v=" . rand());

        $this->addJS("/assets/plugins/dropzone/dropzone3.js");
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');
       // $this->addJs("/assets/js/methods.js?v=" . rand());
        $this->addJs("/assets/js/secondment-card.js?v=" . rand());
        $this->addJs("/assets/js/journals/secondment-list.js?v=" . rand());

        $this->view('card');
    }

    /**
     * Проверяет, может ли руководитель подтвердить заявку сотрудника.
     *
     *  Логика:
     *  - У каждого отдела есть список ID подразделений и ID руководителя.
     *  - Если департамент сотрудника входит в список департаментов отдела,
     *    и ID текущего пользователя совпадает с ID руководителя этого отдела —
     *    возвращается true.
     *  - В остальных случаях возвращается false.
     *
     * @param $employeeId
     * @param $currentUserId
     * @return bool
     */
    public function canHeadApprove($employeeId, $currentUserId): bool
    {
        // карта соответствий отделов и руководителей
        $departments = [
            'ОСК' => [
                'departments' => [58, 66, 67], // оск имеет 3 отдела в битре
                'head_id' => 43, // Лаврентьева
            ],
            'ИЦ' => [
                'departments' => [79, 72, 74], // ИЦ, ОК, ОПС
                'head_id' => 33, // Молчанова
            ],
            'НИЦ' => [
                'departments' => [68], // НИЦ
                'head_id' => 53, // Ахмедьянов
            ],
        ];

        // Получаем данные сотрудника
        $employee = CUser::GetByID($employeeId)->Fetch();
        if (!$employee || empty($employee['UF_DEPARTMENT'])) {
            return false;
        }

        // получаем департамент выбранного юзера
        $employeeDepartment = is_array($employee['UF_DEPARTMENT'])
            ? $employee['UF_DEPARTMENT'][0]
            : $employee['UF_DEPARTMENT'];

        // проверяем, если департамент пользователя есть в списке и авторизованный пользователь - руководитель отдела -
        // даем возможность подтверждать заявку
        foreach ($departments as $department) {
            if (in_array($employeeDepartment, $department['departments'])
                && $currentUserId == $department['head_id']) {
                return true;
            }
        }

        // если ни к какому отделу выше сотрудник не пренадлежит, даем возможность
        // всем руководителям подтвердить заявку сотрудника
        foreach ($departments as $department) {
            if ($currentUserId == $department['head_id']) {
                return true;
            }
        }

        // иначе нихуя не даем
        return false;
    }

    /**
     * @param int $id
     */
    public function uploadTicketPaymentFileAjax(int $id)
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Request $request */
        $request = $this->model('Request');

        if (isset($_FILES['file'])) {
            $response = $request->saveAnyFile("secondment/ticket_payment/{$id}", $_FILES['file']);

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
    }

    public function uploadChecksFilesAjax(int $id)
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Request $request */
        $request = $this->model('Request');

        if (isset($_FILES['file'])) {
            $response = $request->saveAnyFile("secondment/checks/{$id}", $_FILES['file']);

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * @param $id
     */
    public function deleteTicketPaymentFile()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        $id = $_POST['id'];
        $file = $_POST['file'];

        if (empty($id) || empty($file)) {
            $this->redirect("/secondment/card/{$id}");
        }


        /** @var Request $request */
        $request = $this->model('Request');
        $file = str_replace('/ulab/', '', $file);
        unlink($file);

        return json_decode('1');
    }

    /**
     * @param $id
     */
    public function deleteChecksFile($id)
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        if (empty($id) || empty($_GET['file'])) {
            $this->redirect("/secondment/card/{$id}");
        }

        $file = $_GET['file'];

        /** @var Request $request */
        $request = $this->model('Request');

        $path = "secondment/checks/{$id}/{$file}";

        $request->deleteUploadedFile($path);

        $this->showSuccessMessage("Файл '{$file}' удален");

        $this->redirect("/secondment/card/{$id}");
    }

    /**
     * получает должность сотрудника
     * возвращает строку
     */
    public function getWorkPositionAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $response = '';

        if (!empty($_POST['user_id'])) {
            /** @var User $user */
            $user = $this->model('User');

            $curUser = $user->getUserData($_POST['user_id']);
            $response = $curUser['WORK_POSITION'] ?: '';
        }

        echo $response;
    }

    public function getObjectsAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $response = [];

        if (!empty($_POST['company_id'])) {
            /** @var Secondment $secondment */
            $secondment = $this->model('Secondment');

            $response = $secondment->getObjectDataByCompanyId($_POST['company_id']);
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }


    public function updateStageAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Secondment $secondment */
        $secondment = $this->model('Secondment');
        /** @var User $user */
        $user = $this->model('User');
        /** @var Viewer $viewer */
        $viewer = $this->model('Viewer');

        $currentUserId = $user->getCurrentUserId();

        $stage = $_POST['stage'] ?? '';
        $secondmentId = (int)$_POST['secondment_id'] ?? null;
        $improvementReason = $_POST["improvement_reason"];

        $viewer->deleteView($secondmentId, "secondmentCard");

        if (!empty($stage) && !empty($secondmentId)) {
            $secondmentData = $secondment->getSecondmentDataById($secondmentId);

            if (empty($secondmentData)) {
                $this->showErrorMessage("Заявки с ИД {$secondmentId} не существует");

                $response = [
                    'success' => false,
                    'error' => [
                        'message' => "Заявки с ИД {$secondmentId} не существует"
                    ]
                ];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                return;
            }

            if ($stage === 'Отчет подтвержден') {
                $result = $secondment->setConfirmRejectReport($secondmentId, $currentUserId, 1);
                $confirmReport = $secondment->getConfirmReport($secondmentId);

                if (empty($result)) {
                    $this->showErrorMessage('Не удалось подтвердить отчёт');

                    $response = [
                        'success' => false,
                        'error' => [
                            'message' => 'Не удалось подтвердить отчёт',
                        ]
                    ];

                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    return;
                }

                if (count($confirmReport) < 2) {
                    $this->showSuccessMessage('Отчет подтверждён');

                    $response = [
                        'success' => true
                    ];

                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    return;
                } else {
                    $stage = 'Завершена';
                }
            }

            if ($stage === 'Отчет не подтвержден') {
                $result = $secondment->setConfirmRejectReport($secondmentId, $currentUserId);

                if (!empty($result)) {
                    $stage = 'Подготовка отчета';
                } else {
                    $this->showErrorMessage('Не удалось отклонить отчёт');

                    $response = [
                        'success' => false,
                        'error' => [
                            'message' => 'Не удалось отклонить отчёт',
                        ]
                    ];

                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    return;
                }
            }


            $data = [
                'stage' => $stage
            ];

            if ($improvementReason != "") {
                $data["improvement_reason"] = $improvementReason;
            }

            $result = $secondment->update($data, 'secondment', $secondmentId);

            if ($result === 1) {
                $this->showSuccessMessage('Стадия успешно изменена');

                $response = [
                    'success' => true
                ];

                switch ($stage) {
                    case 'Подготовка приказа и СЗ':
                        $action = 'подтверждена';
                        break;
                    case 'Нужна доработка':
                        $action = 'возвращена на доработку';
                        break;
                    case 'Отклонена':
                        $action = 'отклонена';
                        break;
                    case 'Отчет подтвержден':
                        $action = 'отчет подтвержден';
                        break;
                    case 'Затраты не подтверждены':
                        $action = 'затраты не подтверждены';
                        break;
                    default:
                        $action = '';
                }

                //сохраняет данные пользователя при стадии - "Подготовка приказа и СЗ", "Нужна доработка", "Отклонена"
                if (in_array($stage, ['Подготовка приказа и СЗ', 'Нужна доработка', 'Отклонена'])) {
                    $data = [
                        'secondment_id' => $secondmentId,
                        'user_id' => $currentUserId,
                        'action' => $action
                    ];

                    $secondment->create($data, 'confirm_secondment');
                }


                //отправляет уведомление
                if (in_array($stage, ['Нужна доработка', 'Подготовка отчета', 'Отчет не подтвержден'])) {
                    switch ($stage) {
                        case 'Нужна доработка':
                            $message = 'Командировка не согласована ';
                            break;
                        case 'Подготовка отчета':
                            $message = 'Подготовить отчет о командировке ';
                            break;
                        case 'Отчет не подтвержден':
                            $message = 'Отчет не подтвержден ';
                            break;
                        default:
                            $message = '';
                    }

                    $notify = [
                        "NOTIFY_TITLE" => $secondmentData['title'],
                        "TO_USER_ID" => $secondmentData['user_id'],
                        "FROM_USER_ID" => $currentUserId,
                        "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                        "NOTIFY_MESSAGE" => "{$message}<a href='{URI}/ulab/secondment/card/{$secondmentData['s_id']}'>
                            {$secondmentData['title']}
                        </a>",
                    ];
                    CIMNotify::Add($notify);
                }

                //отправляет уведомление пользователям для проверки отчёта
                if ($stage === 'Проверка отчета') {
                    switch ($stage) {
                        case 'Проверка отчета':
                            $message = 'Отчёт готов к проверке ';
                            break;
                        default:
                            $message = '';
                    }

                    foreach (self::USERS_VERIFY_REPORT as $userId) {
                        $notify = [
                            "NOTIFY_TITLE" => $secondmentData['title'],
                            "TO_USER_ID" => $userId,
                            "FROM_USER_ID" => $currentUserId,
                            "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                            "NOTIFY_MESSAGE" => "{$message}<a href='{URI}/secondment/card/{$secondmentData['s_id']}'>
                        {$secondmentData['title']}
                    </a>",
                        ];
                        CIMNotify::Add($notify);
                    }
                }
            } else {
                $this->showErrorMessage('Не удалось изменить стадию');

                $response = [
                    'success' => false,
                    'error' => [
                        'message' => 'Не удалось изменить стадию'
                    ]
                ];
            }

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            return;
        }
        else {
            $this->showErrorMessage('Не указан, или указан неверно ИД заявки или стадии');

            $response = [
                'success' => false,
                'error' => [
                    'message' => 'Не удалось изменить стадию'
                ]
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    /**
     * document generation
     * route /secondment/document/
     */
    public function document()
    {
        echo 'document generation';
    }

    public function getSettlementsAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $secondment = $this->model('Secondment');

        $result = $secondment->getCityArr($_POST['searchTerm']);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
       // echo json_encode([["id" => 1, "text" => "test"], ["id" =>2, "text" => "errr"]]);
    }

    public function changeStage()
    {
        $secondment = $this->model('Secondment');
        $viewer = $this->model('Viewer');

        $secondmentId = $_POST["secondment_id"];

        $viewer->deleteView($secondmentId, "secondmentCard");

        $result = $secondment->getCityArr($_POST['searchTerm']);

        $data = [
            "stage" => $_POST["stage"]
        ];

        $secondment->update($data, 'secondment', intval($secondmentId));

        $this->redirect('/secondment/card/' . $secondmentId);
    }

    // Добавить договор
    public function addContractAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $secondment = $this->model('Secondment');

        $data = [
            "client_id" => $_POST["client_id"],
            "name" => $_FILES["contract"]["name"],
            "number" => $_POST["number"]
        ];

        $id = $secondment->create($data, 'contracts');

        $dir = "upload/contracts/{$id}/";

        $secondment->saveFile($dir, $_FILES["contract"]["name"], $_FILES["contract"]["tmp_name"]);

        $data["id"] = $id;

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    // Добавить компанию
    public function addCompanyAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $company = $this->model('Company');

        $companyId = $company->add($_POST["company_name"]);

        $data = [
            "id" => $companyId,
            "name" => $_POST["company_name"]
        ];

        $dataBank = [
            'ENTITY_ID'                 => $companyId,
            'ENTITY_TYPE_ID'            => CCrmOwnerType::Company,
            'NAME'                      => $_POST["company_name"],
            'SORT'                      => 500,
            'ACTIVE'                    => 'Y',
            'RQ_INN'                    => $_POST['inn'],
            'RQ_ACCOUNTANT'             => $_POST['ADDR'],
            'RQ_OGRN'                   => $_POST['OGRN'],
            'RQ_KPP'                    => $_POST['KPP'],
            'RQ_COMPANY_REG_DATE'       => $_POST['Position2'],
            'RQ_COMPANY_FULL_NAME'      => $_POST['CompanyFullName'],
            'RQ_DIRECTOR'               => $_POST['DirectorFIO']
        ];

        $company->setRequisiteByCompanyId($companyId, $dataBank);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function changeStageAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $secondment = $this->model('Secondment');

        $data["stage"] = $_POST["stage"];

        if (!empty($_POST["cancel_comment"])) {
            $data["cancel_comment"] = $_POST["cancel_comment"];
        }

        $secondment->update($data, 'secondment', intval($_POST["secondment_id"]));

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    // Продлить командировку
    public function extend()
    {
      //  setlocale(LC_ALL, 'ru_RU.utf8');
//        global $APPLICATION;
//
//        $APPLICATION->RestartBuffer();

        $secondment = $this->model('Secondment');
        $viewer = $this->model('Viewer');
        $user = $this->model('User');

        $secondmentId = $_POST["secondment_id"];


        $userId = $user->getCurrentUser()["ID"];
        $secondmentDataBd =  $secondment->getSecondmentDataById($secondmentId);
        $secondmentOtherDataBd =  $secondment->getOtherFieldsById($secondmentId);

        $viewer->deleteView($secondmentId, "secondmentCard");

        $archiveData = [
            "secondment_id" => $secondmentId,
            "json_data" => $_POST["json_data"],
            "user_id" => $userId,
            "created_at" => date('Y-m-d H:i:s')
        ];

        $archiveId = $secondment->create($archiveData, 'secondment_archive');

        //$archiveId = 5;

        foreach (self::SECONDMENT_FILE_CATEGORIES as $category) {
            $originalDir = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/secondment/{$category}/{$secondmentId}";
            $dir = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/secondment/archive/{$category}/{$archiveId}";

            if ( !is_dir($dir) ) {
                $mkdirResult = mkdir($dir, 0766, true);

                if ( !$mkdirResult ) {
                    return [
                        'success' => false,
                        'error' => [
                            'message' => "Ошибка! Не удалось создать папку. {$dir}",
                        ]
                    ];
                }
            }

            $filesArr = $secondment->getFilesFromDir($originalDir);

            foreach ($filesArr as $file) {
                copy($originalDir . "/" . $file, $dir . "/" . $file);
            }
        }

        // Копирование прочего
        foreach ($_POST["other"] as $other) {
            $otherId = $other["id"];
            $originalDir = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/secondment/other/{$secondmentId}/{$otherId}";
            $dir = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/secondment/archive/other/{$archiveId}/{$otherId}";

            if ( !is_dir($dir) ) {
                $mkdirResult = mkdir($dir, 0766, true);

                if ( !$mkdirResult ) {
                    return [
                        'success' => false,
                        'error' => [
                            'message' => "Ошибка! Не удалось создать папку. {$dir}",
                        ]
                    ];
                }
            }

            $filesArr = $secondment->getFilesFromDir($originalDir);

            foreach ($filesArr as $file) {
                copy($originalDir . "/" . $file, $dir . "/" . $file);
            }
        }


        // Сохранение новых данных

        $secondmentData = [];
        $otherData = [];



        $otherSum = 0;

        for ($i = 0; $i < count($_POST["other"]); $i++) {
            $otherId = $_POST["other_id"][$i];

            $otherData = [
                "secondment_id" => $secondmentId,
                "sum" => $_POST["other"][$i] + $secondmentOtherDataBd[$i]["sum"],
                "comment" => $_POST["comment_other"][$i]
            ];

            $otherSum += $_POST["other"][$i] + $secondmentOtherDataBd[$i]["sum"];

            if (is_null($otherId)) {
                $otherId = $secondment->create($otherData, 'secondment_other');
                $test = $otherId;
            } else {
                $secondment->update($otherData, 'secondment_other', $otherId);
            }

            for ($j = 0; $j < count($_FILES["other"]["name"][$i]); $j++) {
                $dir = "upload/secondment/other/{$secondmentId}/{$otherId}";
                $secondment->saveFile($dir, $_FILES["other"]["name"][$i][$j], $_FILES["other"]["tmp_name"][$i][$j]);
            }
        }

        $secondmentData = [
            "date_begin" => $_POST["date_begin"],
            "date_end" => $_POST["date_end"],
            "total_days" => $_POST["total_days"],
            'ticket_price' => $_POST['ticket_price'] + $secondmentDataBd['ticket_price'],
            'comment_ticket_price' => $_POST['comment_ticket_price'],
            'gasoline_consumption' => $_POST['gasoline_consumption'] + $secondmentDataBd['gasoline_consumption'],
            'comment_gasoline_consumption' => $_POST['comment_gasoline_consumption'],
            'gasoline_consumption_object' => $_POST['gasoline_consumption_object'] + $secondmentDataBd['gasoline_consumption_object'],
            'comment_gasoline_consumption_object' => $_POST['comment_gasoline_consumption_object'],
            'per_diem' => $_POST['per_diem'],
            'comment_per_diem' => $_POST['comment_per_diem'],
            'accommodation' => $_POST['accommodation'] + $secondmentDataBd['accommodation'],
            'comment_accommodation' => $_POST['comment_accommodation'],
            'planned_expenses' => $_POST['ticket_price'] + $secondmentDataBd['ticket_price'] +
                $_POST['gasoline_consumption'] + $secondmentDataBd['gasoline_consumption'] +
                $_POST['gasoline_consumption_object'] + $secondmentDataBd['gasoline_consumption_object'] +
                $_POST['per_diem'] + $_POST['accommodation'] + $secondmentDataBd['accommodation'] + $otherSum,
            "stage" => "Подготовка приказа и СЗ"
        ];

        if (!empty($_POST["file_payment_delete"])) {
            $pathArr = explode(",", $_POST["file_payment_delete"]);
            foreach ($pathArr as $path) {
                if ($path != "") {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $path);
                }
            }
        }

        foreach ($_FILES as $category => $files) {
            for ($i = 0; $i < count($files["name"]); $i++) {
                if (in_array($category, self::SECONDMENT_FILE_CATEGORIES)) {
                    $dir = "upload/secondment/$category/{$secondmentId}";
                    $secondment->saveFile($dir, $files["name"][$i], $files["tmp_name"][$i]);
                }
            }
        }

        $secondment->update($secondmentData, 'secondment', intval($_POST["secondment_id"]));


        echo "<pre>";

        var_dump($_POST);
        var_dump($_FILES);
        echo "</pre>";

        $this->redirect("/ulab/secondment/card/{$secondmentId}");
      //  echo json_encode($secondmentData, JSON_UNESCAPED_UNICODE);
     //   echo json_encode($_FILES, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc Журнал командировок
     * journal
     * route /secondment/list/
     */
    public function archiveCard(int $id)
    {
        $location = "/secondment/list/";

        if (empty($id) || $id < 0) {
            $this->redirect($location);
        }

        /** @var Secondment $secondment */
        $secondment = $this->model('Secondment');

        $data = $secondment->getArchiveCard($id);

        $this->data["id"] = $id;
        $this->data["user_id"] = $data["user_id"];
        $this->data["created_at"] = $data["created_at"];
        $this->data["secondment_id"] = $data["secondment_id"];
        $this->data = array_merge($this->data, json_decode($data["json_data"], JSON_UNESCAPED_UNICODE));

        foreach (self::SECONDMENT_FILE_CATEGORIES as $category) {
            $this->data["fileArr"][$category] = $secondment->getFilesFromDir($_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/secondment/archive/{$category}/{$id}");
        }

        foreach ($this->data['other'] as $index => $field) {
            $this->data["fileArr"]["other"][$index] = $secondment->getFilesFromDir(UPLOAD_DIR . "/secondment/archive/other/{$id}/" . $field["id"]);
        }


        echo "<pre>";
        //var_dump($this->data);
        echo "</pre>";

        $this->addCSS("/assets/css/secondment_card.css?v=" . rand());

        $this->addJs("/assets/js/secondment-card.js?v=" . rand());

        $this->view('archiveCard');

    }

    public function generateCompensationAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Secondment $secondment */
        $secondment = $this->model('Secondment');
        /** @var Urer $user */
        $user = $this->model('User');
        /** @var Company $company */
        $company = $this->model('Company');
        /** @var Request $request */
        $request = $this->model('Request');
        /** @var Transport $transport */
        $transport = $this->model('Transport');
        /** @var Viewer $viewer */

        $id = $_POST["secondment_id"];

        if (!empty($_POST["file_delete"])) {
            $pathArr = explode(",", $_POST["file_delete"]);
            foreach ($pathArr as $path) {
                if ($path != "") {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $path);
                }
            }
        }

        $secondmentData = $secondment->getSecondmentDataById($id);
        $transportData = $transport->getTransportById($secondmentData["vehicle_id"]);
        $compensationData = $secondment->getCompensationBySecondmentId($id);
        $userData = $user->getUserData($secondmentData['user_id']);

        $fields["id"] = $secondmentData["id"];
        $fields["transport_model"] = $transportData["model"];
        $fields["transport_number"] = $transportData["number"];
        $fields["compensation"] = $compensationData["sum"];
        $fields["compensation_text"] = morphos\Russian\MoneySpeller::spell($fields["compensation"], morphos\Russian\MoneySpeller::RUBLE);
        $fields["fio"] = $userData["LAST_NAME"] . " "
            . mb_substr($userData["NAME"], 0, 1)
            . "." . mb_substr($userData["SECOND_NAME"], 0, 1) . ".";
        $fields["work_position"] = $userData["WORK_POSITION"];
        $fields["date"] = date("d.m.Y");
     //   $fields["currency_text"] = StringHelper::numDeclension($fields["compensation"], ['рубль', 'рубля', 'рублей']);
     //   $fields["currency_text"] = morphos\Russian\MoneySpeller::spell($fields["compensation"], morphos\Russian\MoneySpeller::RUBLE);

        $docTempalate = new \PhpOffice\PhpWord\TemplateProcessor('./upload/docTemplates/fuelCompensationTemplate.docx');
        $docTempalate->setValues($fields);
        $outputFile = "./upload/secondment/compensation/{$id}/" .  trim($fields["fio"]) . "№ " . $fields["id"] ." от " . $fields["date"] .".docx";
        $dir = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/secondment/compensation/{$id}/";
        if ( !is_dir($dir) ) {
            $mkdirResult = mkdir($dir, 0766, true);
        }
        $docTempalate->saveAs($outputFile);

        $href = "/ulab/upload/secondment/compensation/{$id}/" . trim($fields["fio"]) . "№ " . $fields["id"] ." от " . $fields["date"] .".docx";

        echo json_encode(["href" => $href], JSON_UNESCAPED_UNICODE);
    }

    public function testCard()
    {
        /** @var Request $request */
        $request = $this->model('Request');

      //  $result = $request->getCountDeal();

        $result = morphos\Russian\LastNamesInflection::getCase('Мухина', 'родительный');

        var_dump($result );



    }

    public function createMemoDocAjax()
    {

        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Secondment $secondment */
        $secondment = $this->model('Secondment');
        /** @var Secondment $transport */
        $transport = $this->model('Transport');
        /** @var Secondment $user */
        $user = $this->model('User');
        /** @var Secondment $file */
        $file = $this->model('File');

        $transportText = "";
        $id = intval($_POST["secondment_id"]);

        $secondmentData = $secondment->getSecondmentDataById($id);
        $transportData = $transport->getTransportById($secondmentData["vehicle_id"]);
        $userData = $user->getUserById($secondmentData["user_id"]);


        $fullSum = 0;

        if (isset($_POST["reportText"])) {
            foreach ($_POST["reportText"] as $item) {
                $date = date_format(date_create($item['travel_date']), "d.m.Y");
              //  $currencyText = StringHelper::numDeclension($item['travel_sum'], ["рубль", "рубля", "рублей"]);
                $sumText = StringHelper::setTextMoneyFormat($item['travel_sum']);
                $transportText .= "- Кассовый чек № {$item['check_number']} с {$item['destination']} от {$date} на сумму {$sumText};<w:br/>";
                $fullSum += floatval($item["travel_sum"]);
            }
        }

//        $fullSum = number_format($fullSum, 2, '.', '');
//
//        $fullSumArr = explode(".", $fullSum);
//        $fullSumRub = $fullSumArr[0];
//        $fullSumCop = $fullSumArr[1];

       // $fields["fullSumText"] = $fullSumRub . " " . StringHelper::numDeclension($fullSumRub, ["рубль", "рубля", "рублей"]) . " " . $fullSumCop . " коп.";
        $fields["fullSumText"] = StringHelper::setTextMoneyFormat($fullSum);
        $fields["id"] = $id;
        $fields["transportText"] = $transportText;

        $fields["lastname"] = $userData["LAST_NAME"];
        $fields["genitiveLastname"] = morphos\Russian\LastNamesInflection::getCase($userData["LAST_NAME"], 'родительный');
        $fields["initials"] = mb_substr($userData["NAME"], 0, 1) . "." . mb_substr($userData["SECOND_NAME"], 0, 1) . ".";

        $fields["dateStart"] = date("d.m.Y", strtotime($secondmentData["date_begin"]));
        $fields["dateEnd"] = date("d.m.Y", strtotime($secondmentData["date_end"]));

        $fields["transportModel"] = $transportData["model"];
        $fields["transportNumber"] = $transportData["number"];

        $dateStartMonth = date("m",strtotime($secondmentData["date_begin"]));
        $dateEndMonth = date("m",strtotime($secondmentData["date_end"]));

        if ($dateStartMonth == $dateEndMonth) {
            $fields["monthPeriod"] = StringHelper::getMonthTitle($dateStartMonth);
        } else {
            $fields["monthPeriod"] = StringHelper::getMonthTitle($dateStartMonth) . "-" . StringHelper::getMonthTitle($dateEndMonth);
        }

     //   $fields["fullSumText"] = "1085 рублей 88 коп.";

        $fields["todayTextDate"] = date("d.m.Y");

        $docTempalate = new \PhpOffice\PhpWord\TemplateProcessor('./upload/docTemplates/memoDocTemplate.docx');
        $dirName = "/upload/secondment/memo_doc";

       // $file->createWordDoc($docTempalate, $dirName, $fields);

        $docTempalate = new \PhpOffice\PhpWord\TemplateProcessor('./upload/docTemplates/memoDocTemplate.docx');


        $tableArr = array_values($_POST["gsmText"]);

        $fullSum = 0;

        for ($i = 0; $i < count($tableArr); $i++) {
            $tableArr[$i]["n"] = $i + 1;
            $tableArr[$i]["sum"] = $tableArr[$i]["price"] * $tableArr[$i]["gsm"];
            $fullSum += $tableArr[$i]["sum"];
        }

//        if ($id === 2) {
//            $fullSum = 100.2;
//        }

        $fields["fullSumText"] = StringHelper::setTextMoneyFormat($fullSum);
        $docTempalate->setValues($fields);

//        $tableArr = [
//            ['n' => 1, 'km' => '40', 'gsm' => '50', 'price' => '100', 'sum' => '250', 'object' => "«УралНИИстром», Сталеваров 5, кор.2  - Свердловский тракт, 38- «УралНИИстром», Сталеваров 5, кор.2"],
//            ['n' => 2, 'km' => '50', 'gsm' => '60', 'price' => '200', 'sum' => '350', 'object' => "«УралНИИстром», Сталеваров 5, кор.2  - Свердловский тракт, 38- «УралНИИстром», Сталеваров 5, кор.2"]
//        ];

        $docTempalate->cloneRowAndSetValues('n', $tableArr);

        $lastname = $fields["lastname"];
        $initials = $fields["initials"];

        $outputFile = "./upload/secondment/memo_doc/{$id}/Служебная записка №{$id} ({$lastname}).docx";
        $dir = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/secondment/memo_doc/{$id}/";
        if ( !is_dir($dir) ) {
            $mkdirResult = mkdir($dir, 0766, true);
        }
        $docTempalate->saveAs($outputFile);

        $name = $userData["NAME"];

        echo json_encode([
            "post" => $_POST,
            "transportText" => $transportText,
            "data" => [$secondmentData, $transportData, $userData],
            "test" => $outputFile,

        ], JSON_UNESCAPED_UNICODE);
    }

    public function test()
    {
        var_dump("tee");

        $secondment = $this->model('Secondment');

        $data = [

        ];
        $secondment->update($data, 'secondment', $secondmentId);

        echo "<pre>";
        var_dump($this->getManagementStructure());
      //  var_dump(self::MANAGEMENT_STRUCTURE[18]);

        echo "</pre>";

    }

    public function bytePdfToServerAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $byteFile = $_POST["file"];
        $path = $_POST["path"];

        $file = $this->model("Secondment");

        $file->byteFileToServer($byteFile, $path);

        echo json_encode(true);
    }

    public function dashboard()
    {
        $version = rand();
        $this->addCSS("/assets/css/style.css" . $version);
      //  $this->addCSS("/assets/css/shipmentCard.css" . $version);

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
        $this->addCSS("/assets/plugins/DataTables/FixedColumns-4.2.1/css/fixedColumns.css");

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
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');
        $this->addJS("/assets/plugins/DataTables/FixedHeader-3.2.0/js/dataTables.fixedHeader.min.js");
        $this->addJS("/assets/plugins/DataTables/FixedColumns-4.2.1/js/fixedColumns.js");

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");

     //   $this->addJS("/assets/js/laboratory/dashboard.js" . $version);

        $this->view('dashboard');
    }

    public function updateProjectAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $secondment = $this->model('Secondment');

        $id = $_POST["id"];

        $data = [
            "project_id" => $_POST["project_id"],
        ];

        if (isset($id) && !empty($id)) {
            $secondment->updateRow($data, $id);
        }

        // echo json_encode($id, JSON_UNESCAPED_UNICODE);
        echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
    }

    public function addOrderAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $secondment = $this->model('Secondment');

        $secondment->addOrder($_POST);
        echo json_encode(true);
    }

}
