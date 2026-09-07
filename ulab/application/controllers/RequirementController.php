	<?php

    /**
     * Класс контроллер для Техзадания
     * Class RequirementController
     */
    class RequirementController extends Controller
    {
        const ALLOWED_MATERIAL_ID = [1, 2, 6, 7, 8, 11, 15, 94, 95, 101];
        const SAMPLES_ACCEPTANCE_ACT = 0;
        const ALLOWED_EDIT_BY_USERS = [1, 86, 7, 234, 9, 17, 100, 11, 35, 33, 13, 32, 15, 43, 69, 53, 115, 36, 56, 58, 62, 16, 61, 83, 68, 59, 45, 60, 82, 92, 28, 12, 67, 101, 105, 229, 220]; //TODO: Исправиль костыль прав
        const FORBIDDEN_CREATE_NEW_GOST = [56];
        const LABORATORY_HEAD = [11, 13, 32, 15, 58, 43];


        public function index()
        {
            $this->redirect('/request/list/');
        }

        /**
         * для связки со старыми заявками
         * route /requirement/card/{$tzId}
         * @param $tzId
         * страница технического задания
         */
        public function card_old($tzId)
        {
            if (empty($tzId)) {
                $this->redirect('/request/list/');
            }

            $this->data['title'] = 'Формирование технического задания';


            /** @var Request $request */
            $request = $this->model('Request');
            /** @var Material $material */
            $material = $this->model('Material');
            /** @var Requirement $requirement */
            $requirement = $this->model('Requirement');
            /** @var Gost $gost */
            $gost = $this->model('Gost');
            /** @var User $user */
            $user = $this->model('User');


            $tz = $requirement->getTzByTzId((int)$tzId);

            if (empty($tz)) {
                $this->showErrorMessage("Технического задания с ИД {$tzId} не существует");
                $this->redirect('/request/list/');
            }

            $deal = $request->getDealById($tz['ID_Z']);

            if (empty($deal)) {
                $this->showErrorMessage("Заявки с ИД {$tz['ID_Z']} не существует");
                $this->redirect('/request/list/');
            }


            $this->data['tz'] = $tz;
            $materialData = $requirement->getMaterialProbeGostToRequest($tz['ID_Z']);
            $this->data['test'] = $materialData;
            $this->data['assign_method'] = $gost->getAssignedByGostList($materialData['methods']);
            $this->data['gosts_group'] = $materialData['gosts_group'];
            $this->data['price'] = $materialData['price'];
            $gostList = $gost->getListAndPricesByDealId($tz['ID_Z']);
            $this->data['select_gosts'] = $gostList;
            $this->data['select_tu_gosts'] = $gost->getListAndPricesByDealId($tz['ID_Z'], true);

            $this->data['contract'] = $requirement->getContractByDealId($tz['ID_Z']);
            $this->data['objects'] = $requirement->getObjects();
            $this->data['quarry'] = $requirement->getQuarry();
            $this->data['select_materials'] = $material->getList();
            $actBase = $requirement->getActBase($tz['ID_Z']);
            $this->data['requests_to_company'] = $requirement->getRequestsToCompany($deal['ID'], $deal['COMPANY_ID']);
            $this->data['taken_request'] = $requirement->getTakenRequest($tz['TAKEN_ID_DEAL']);
            $currentUserId = $user->getCurrentUserId();

            $this->data['is_block_delete_material'] = !empty($tz['RESULTS']);

            // Не соответствие методики и руководителей
            $this->data['unconfirmed_tz'] = $requirement->getUnconfirmedTz((int)$tzId);
            $this->data['confirmed_tz'] = $requirement->getConfirmedTz((int)$tzId);
            $this->data['is_confirm'] = $requirement->isConfirm((int)$tzId);
            $this->data['is_confirmTz'] = $requirement->isConfirmTz((int)$tzId);

            $this->data['lab_leaders_tz'] = $requirement->getLabLeadersTZ((int)$tzId);
            $this->data['check_tz'] = $requirement->getCheckTzByIdTz((int)$tzId);
            $this->data['assigned'] = $user->getAssignedByDealId((int)$tz['ID_Z']);
            $this->data['laboratory_head'] = self::LABORATORY_HEAD;
            $this->data['assigned_id'] = array_merge(array_column($this->data['assigned'], 'user_id'), ['1', '62', '17']); //1, 62 TODO: исправить харкод


            $materialsId = array_column($materialData['material'], 'id');
            $requiredSelectQuarry = '';

            if (!empty($materialsId) && array_intersect($materialsId, self::ALLOWED_MATERIAL_ID)) {
                $requiredSelectQuarry = 'required';
            }

            $this->data['required_quarry'] = $requiredSelectQuarry;


            $this->data['contract_number'] = $this->data['contract']['NUMBER'] ?? '';
            $this->data['contract_date'] = $this->data['contract']['DATE'] ?? '';
            $this->data['contract_type'] = $this->data['contract']['CONTRACT_TYPE'] ?? 'Договор';
            $this->data['deal_title'] = $tz['REQUEST_TITLE'];
            $this->data['name_file_create'] = $this->data['tz']['CONTROL_LIST'] ? 'control_list' : 'probe';
            $this->data['title_file_create'] = $this->data['tz']['CONTROL_LIST'] ? 'Создать контрольный лист' : 'Создать акт приемки проб';
            $this->data['act_number'] = ! empty($actBase['ACT_NUM']) ? $actBase['ACT_NUM'] : '';
            $this->data['act_date'] = ! empty($actBase['ACT_DATE']) && $actBase['ACT_DATE'] !== '0000-00-00' ?
                date('d.m.Y', strtotime($actBase['ACT_DATE'])) : '';
            $this->data['probe_place'] = ! empty($actBase['PLACE_PROBE']) ? $actBase['PLACE_PROBE'] : 'Пробы не поступили';
            $this->data['probe_made'] = ! empty($actBase['PROBE_PROIZV']) ? $actBase['PROBE_PROIZV'] : 'Пробы не поступили';
            $this->data['probe_date'] = ! empty($actBase['DATE_PROBE']) && $actBase['DATE_PROBE'] !== '0000-00-00' ?
                date('d.m.Y', strtotime($actBase['DATE_PROBE'])) :
                'Пробы не поступили';
            $this->data['type_id'] = $tz['TYPE_ID'];
            $this->data['sum_price'] = $tz['PRICE'] ?? 0;
            $this->data['save'] = in_array($currentUserId, self::ALLOWED_EDIT_BY_USERS);
            //TODO: Временный костыль, блокировка редактирования ТЗ после внесения данных в результаты испытаний
            $this->data['is_may_change'] = true; //$deal['ID'] < 8846 || empty($requirement->getCountFilledResultData($deal['ID'])["count_umtr"]);


            if (isset($_SESSION['requirement_post'])) {
                $this->data['requirement'] = $_SESSION['requirement_post'];

                unset($_SESSION['requirement_post']);
            } else {
                $this->data['requirement'] = [];

                $this->data['requirement']['material'] = $materialData['material'];
                $this->data['requirement']['amount'] = $materialData['amount'];
                $this->data['requirement']['methods'] = $materialData['methods'];
                $this->data['requirement']['conditions'] = $materialData['conditions'];
                $this->data['requirement']['assign_method'] = $materialData['assigned'];
                $this->data['requirement']['price'] = $materialData['price'];
                $this->data['requirement']['tz_id'] = $tzId;
                $this->data['requirement']['deal_id'] = $tz['ID_Z'];
                $this->data['requirement']['DESCRIPTION'] = $tz['DESCRIPTION'];
                $this->data['requirement']['OBJECT'] = $tz['OBJECT'];
                $this->data['requirement']['QUARRY_ID'] = $tz['QUARRY_ID'];
                $this->data['requirement']['DEADLINE'] = $tz['DEADLINE'] ?? date('Y-m-d');
                $this->data['requirement']['DAY_TO_TEST'] = $tz['DAY_TO_TEST'];
                $this->data['requirement']['type_of_day'] = $tz['type_of_day'];
                $this->data['requirement']['COMMENT_KP'] = $tz['COMMENT_KP'];
                $this->data['requirement']['COMMENT_TZ'] = $tz['COMMENT_TZ'];
                $this->data['requirement']['DISCOUNT'] = !empty($tz['DISCOUNT']) ? $tz['DISCOUNT'] : '';
                $this->data['requirement']['taken_certificate'] = $tz['TAKEN_SERT_ISP'];
            }

            $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

            $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');
            $this->addJs('/assets/js/requirement.js?v=' . rand());

            $this->view('form-old');
        }


        /**
         * route /requirement/card/{$tzId}
         * @param $tzId
         * страница технического задания
         */
        public function card($tzId)
        {
            if (empty($tzId)) {
                $this->redirect('/request/list/');
            }

            $this->data['title'] = 'Формирование технического задания';


            /** @var Request $request */
            $request = $this->model('Request');
            /** @var Material $material */
            $material = $this->model('Material');
            /** @var Requirement $requirement */
            $requirement = $this->model('Requirement');
            /** @var Gost $gost */
            $gost = $this->model('Gost');
            /** @var User $user */
            $user = $this->model('User');
            /** @var TechCondition $tcModel */
            $tcModel = $this->model('TechCondition');


            $tz = $requirement->getTzByTzId((int)$tzId);

            if (empty($tz)) {
                $this->showErrorMessage("Технического задания с ИД {$tzId} не существует");
                $this->redirect('/request/list/');
            }

            $deal = $request->getDealById($tz['ID_Z']);

            if (empty($deal)) {
                $this->showErrorMessage("Заявки с ИД {$tz['ID_Z']} не существует");
                $this->redirect('/request/list/');
            }

            $this->redirect("/requirement/card_new/{$tzId}");

            // if (!in_array($tz['ID_Z'], [10305, 10577, 10795, 10912, 11060, 11074, 11154, 11303, 11304, 11404, 11497, 11660, 11716, 11693])) {

            // }


            $this->data['tz'] = $tz;
            $materialData = $requirement->getUlabMaterialProbeGostToRequest($tz['ID_Z']);
            $this->data['test'] = $materialData;
            $this->data['assign_method'] = $gost->getUlabAssignedByGostList($materialData['methods']);
            $this->data['gosts_group'] = $materialData['gosts_group'];
            $this->data['price'] = $materialData['price'];

            $this->data['select_gosts'] = $gost->getUlabGostAndPrice();
            $this->data['select_tu_gosts'] = $tcModel->getList();

            $this->data['contract'] = $requirement->getContractByDealId($tz['ID_Z']);
            $this->data['objects'] = $requirement->getObjects();
            $this->data['quarry'] = $requirement->getQuarry();
            $this->data['select_materials'] = $material->getList();
            $actBase = $requirement->getActBase($tz['ID_Z']);
            $this->data['requests_to_company'] = $requirement->getRequestsToCompany($deal['ID'], $deal['COMPANY_ID']);
            $this->data['taken_request'] = $requirement->getTakenRequest($tz['TAKEN_ID_DEAL']);
            $currentUserId = $user->getCurrentUserId();

            $this->data['user_id'] = $_SESSION['SESS_AUTH']['USER_ID'];

            $this->data['is_block_delete_material'] = !empty($tz['RESULTS']);

            // Не соответствие методики и руководителей
            $this->data['unconfirmed_tz'] = $requirement->getUnconfirmedTz((int)$tzId);
            $this->data['confirmed_tz'] = $requirement->getConfirmedTz((int)$tzId);
            $this->data['is_confirm'] = $requirement->isConfirm((int)$tzId);
            $this->data['is_confirmTz'] = $requirement->isConfirmTz((int)$tzId);

            $this->data['lab_leaders_tz'] = $requirement->getLabLeadersTZ((int)$tzId);
            $this->data['check_tz'] = $requirement->getCheckTzByIdTz((int)$tzId);
            $this->data['assigned'] = $user->getAssignedByDealId((int)$tz['ID_Z']);
            $this->data['laboratory_head'] = self::LABORATORY_HEAD;
            $this->data['assigned_id'] = array_merge(array_column($this->data['assigned'], 'user_id'), ['1', '62', '17']); //1, 62 TODO: исправить харкод


            $materialsId = array_column($materialData['material'], 'id');
            $requiredSelectQuarry = '';

            if (!empty($materialsId) && array_intersect($materialsId, self::ALLOWED_MATERIAL_ID)) {
                $requiredSelectQuarry = 'required';
            }

            $this->data['required_quarry'] = $requiredSelectQuarry;


            $this->data['contract_number'] = $this->data['contract']['NUMBER'] ?? '';
            $this->data['contract_date'] = $this->data['contract']['DATE'] ?? '';
            $this->data['contract_type'] = $this->data['contract']['CONTRACT_TYPE'] ?? 'Договор';
            $this->data['deal_title'] = $tz['REQUEST_TITLE'];
            $this->data['name_file_create'] = $this->data['tz']['CONTROL_LIST'] ? 'control_list' : 'probe';
            $this->data['title_file_create'] = $this->data['tz']['CONTROL_LIST'] ? 'Создать контрольный лист' : 'Создать акт приемки проб';
            $this->data['act_number'] = ! empty($actBase['ACT_NUM']) ? $actBase['ACT_NUM'] : '';
            $this->data['act_date'] = ! empty($actBase['ACT_DATE']) && $actBase['ACT_DATE'] !== '0000-00-00' ?
                date('d.m.Y', strtotime($actBase['ACT_DATE'])) : '';
            $this->data['probe_place'] = ! empty($actBase['PLACE_PROBE']) ? $actBase['PLACE_PROBE'] : 'Пробы не поступили';
            $this->data['probe_made'] = ! empty($actBase['PROBE_PROIZV']) ? $actBase['PROBE_PROIZV'] : 'Пробы не поступили';
            $this->data['probe_date'] = ! empty($actBase['DATE_PROBE']) && $actBase['DATE_PROBE'] !== '0000-00-00' ?
                date('d.m.Y', strtotime($actBase['DATE_PROBE'])) :
                'Пробы не поступили';
            $this->data['type_id'] = $tz['TYPE_ID'];
            $this->data['sum_price'] = $tz['PRICE'] ?? 0;
            $this->data['save'] = in_array($currentUserId, self::ALLOWED_EDIT_BY_USERS);
            //TODO: Временный костыль, блокировка редактирования ТЗ после внесения данных в результаты испытаний
            $this->data['is_may_change'] = true; //$deal['ID'] < 8846 || empty($requirement->getCountFilledResultData($deal['ID'])["count_umtr"]);


            if (isset($_SESSION['requirement_post'])) {
                $this->data['requirement'] = $_SESSION['requirement_post'];

                unset($_SESSION['requirement_post']);
            } else {
                $this->data['requirement'] = [];

                $this->data['requirement']['material'] = $materialData['material'];
                $this->data['requirement']['amount'] = $materialData['amount'];
                $this->data['requirement']['methods'] = $materialData['methods'];
                $this->data['requirement']['conditions'] = $materialData['conditions'];
                $this->data['requirement']['assign_method'] = $materialData['assigned'];
                $this->data['requirement']['price'] = $materialData['price'];
                $this->data['requirement']['tz_id'] = $tzId;
                $this->data['requirement']['deal_id'] = $tz['ID_Z'];
                $this->data['requirement']['DESCRIPTION'] = $tz['DESCRIPTION'];
                $this->data['requirement']['OBJECT'] = $tz['OBJECT'];
                $this->data['requirement']['QUARRY_ID'] = $tz['QUARRY_ID'];
                $this->data['requirement']['DEADLINE'] = $tz['DEADLINE'] ?? date('Y-m-d');
                $this->data['requirement']['DAY_TO_TEST'] = $tz['DAY_TO_TEST'];
                $this->data['requirement']['type_of_day'] = $tz['type_of_day'];
                $this->data['requirement']['COMMENT_KP'] = $tz['COMMENT_KP'];
                $this->data['requirement']['COMMENT_TZ'] = $tz['COMMENT_TZ'];
                $this->data['requirement']['DISCOUNT'] = !empty($tz['DISCOUNT']) ? $tz['DISCOUNT'] : '';
                $this->data['requirement']['taken_certificate'] = $tz['TAKEN_SERT_ISP'];
            }

            $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

            $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');
            $this->addJs('/assets/js/requirement.js?v=' . rand());

            $this->view('form');
        }


        /**
         * route /requirement/card_new/{$tzId}
         * @param $tzId
         * страница технического задания
         */
        public function card_new($tzId)
        {
            if (empty($tzId)) {
                $this->redirect('/request/list/');
            }

            //        if ($_SESSION['SESS_AUTH']['USER_ID'] != 61) {
            $this->redirect("/requirement/card_new_new/{$tzId}");
            //        }



            $this->data['title'] = 'Формирование технического задания';

            /** @var Request $request */
            $request = $this->model('Request');
            /** @var Material $materialModel */
            $materialModel = $this->model('Material');
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var TechCondition $tcModel */
            $tcModel = $this->model('TechCondition');
            /** @var Methods $methodsModel */
            $methodsModel = $this->model('Methods');

            //// получение данных
            $tzData = $requirementModel->getTzByTzId((int)$tzId);

            $dealId = (int) $tzData['ID_Z'];

            if (empty($tzData)) {
                $this->showErrorMessage("Технического задания с ИД {$tzId} не существует");
                $this->redirect('/request/list/');
            }

            $dealData = $request->getDealById($dealId);

            if (empty($dealData)) {
                $this->showErrorMessage("Заявки с ИД {$dealId} не существует");
                $this->redirect('/request/list/');
            }


            $contractData = $requirementModel->getContractByDealId($dealId);
            $actData = $requirementModel->getActBase($dealId);

            //// заполнение селектов
            // Объект строительства
            $this->data['objects'] = $requirementModel->getObjects();
            // Методики
            $this->data['method_list'] = $methodsModel->getList();
            // ТУ
            $this->data['condition_list'] = $tcModel->getList();
            // Материалы (объект испытаний)
            $this->data['material_list'] = $materialModel->getList();
            // Заявка учтена
            $this->data['requests_to_company'] = $requirementModel->getRequestsToCompany($dealId, $dealData['COMPANY_ID']);

            //// общая информация
            // Основание для проведения испытаний (договор)
            $this->data['contract_number'] = $contractData['NUMBER'] ?? '';
            $this->data['contract_date'] = $contractData['DATE'] ?? '';
            $this->data['contract_type'] = $contractData['CONTRACT_TYPE'] ?? 'Договор';
            $this->data['deal_id'] = $dealId;
            $this->data['tz_id'] = $tzId;
            $this->data['curr_user'] = $_SESSION['SESS_AUTH']['USER_ID'];
            $this->data['deal_title'] = $tzData['REQUEST_TITLE'];

            // Основание для формирования протокола (акт приемки проб)
            $this->data['act_number'] = $actData['ACT_NUM'] ?? '';
            $this->data['act_date'] = ! empty($actData['ACT_DATE']) && $actData['ACT_DATE'] !== '0000-00-00' ?
                date('d.m.Y', strtotime($actData['ACT_DATE'])) : '';

            // Описание объекта
            $this->data['tz'] = $tzData;

            //// доп информация
            $this->data['probe_place'] = ! empty($actData['PLACE_PROBE']) ? $actData['PLACE_PROBE'] : 'Пробы не поступили';
            $this->data['probe_made'] = ! empty($actData['PROBE_PROIZV']) ? $actData['PROBE_PROIZV'] : 'Пробы не поступили';
            $this->data['probe_date'] = ! empty($actData['DATE_PROBE']) && $actData['DATE_PROBE'] !== '0000-00-00' ?
                date('d.m.Y', strtotime($actData['DATE_PROBE'])) :
                'Пробы не поступили';


            // блокировать кнопку сохранения, если есть акты вр
            $actVr = $requirementModel->getActVr($tzId);
            $this->data['is_disable_save'] = !empty($actVr) && !in_array($_SESSION['SESS_AUTH']['USER_ID'], [1, 61, 9]);


            //// материалы
            $this->data['material_probe_list'] = $materialModel->getMaterialProbeToRequest($dealId);
            $this->data['lab_head'] = $requirementModel->getLabHead($dealId);

            // статус проверенности ТЗ
            $this->data['check_state'] = $requirementModel->getStateConfirm($dealId);

            if (!isset($this->data['check_state']) || $this->data['check_state'] == CHECK_TZ_NOT_SENT) {
                $this->showWarningMessage("Техническое задание еще не передано для проверки руководителям лабораторий");
            } elseif ($this->data['check_state'] == CHECK_TZ_NOT_APPROVE) {
                $desc = $requirementModel->getDescConfirmTzNotApprove($tzId);
                $msg = '';
                foreach ($desc as $row) {
                    $msg .= "<br><b>{$row['date_return']} {$row['user_name']}</b>: {$row['desc_return']}";
                }
                $this->showErrorMessage("Техническое задание было возвращено после проверки руководителем лаборатории.{$msg}");
            } elseif ($this->data['check_state'] == CHECK_TZ_WAIT) {
                $this->showWarningMessage("Техническое задание было передано для проверки руководителям лабораторий");
            } else {
                $this->showSuccessMessage("Техническое задание было одобрено руководителями лабораторий. Изменения сбросят все подтверждения.");
            }

            $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
            $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");

            $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');

            $this->addJs('/assets/js/requirement_new.js?v=' . rand());

            $this->view('form_new');
        }


        /**
         * route /requirement/card_new_new/{$tzId}
         * @param $tzId
         * страница технического задания
         */
        public function card_new_new($tzId)
        {
            if (empty($tzId)) {
                $this->redirect('/request/list/');
            }

            global $USER;

            $this->data['title'] = 'Формирование заявки на испытания';

            /** @var Request $request */
            $request = $this->model('Request');
            /** @var Material $materialModel */
            $materialModel = $this->model('Material');
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var TechCondition $tcModel */
            $tcModel = $this->model('TechCondition');
            /** @var Methods $methodsModel */
            $methodsModel = $this->model('Methods');
            /** @var Quarry $quarryModel */
            $quarryModel = $this->model('Quarry');
            $userModel = new User();

            //// получение данных
            $tzData = $requirementModel->getTzByTzId((int)$tzId);

            $dealId = (int) $tzData['ID_Z'];

            $this->data['company_title'] = $tzData['COMPANY_TITLE'];

            $this->data['deal_id'] = $dealId;
            $this->data['comment'] = $request->getComment($dealId);
            $this->data['comment_history'] = $request->getCommentsHistoryByDealId($dealId);
            $this->data['changes_history_duration'] = $request->getChangesHistoryDurationByDealId($dealId);

            if (empty($tzData)) {
                $this->showErrorMessage("Технического задания с ИД {$tzId} не существует");
                $this->redirect('/request/list/');
            }

            $dealData = $request->getDealById($dealId);

            if (empty($dealData)) {
                $this->showErrorMessage("Заявки с ИД {$dealId} не существует");
                $this->redirect('/request/list/');
            }


            $contractData = $requirementModel->getContractByDealId($dealId);
            $actData = $requirementModel->getActBase($dealId);

            $this->data['group_type'] = $tzData['group_type'];

            //// заполнение селектов
            // Объект строительства
            $this->data['objects'] = $requirementModel->getObjects();
            // Методики
            $this->data['method_list'] = $methodsModel->getList();
            // ТУ
            $this->data['condition_list'] = $tcModel->getList();
            // Материалы (объект испытаний)
            $this->data['material_list'] = $materialModel->getList(true);
            // Заявка учтена
            $this->data['requests_to_company'] = $requirementModel->getRequestsToCompany($dealId, $dealData['COMPANY_ID']);

            $this->data['taken_deal_list'] = $request->getDealByTakenDeal($dealId);

            //// общая информация
            // Основание для проведения испытаний (договор)
            $this->data['contract_number'] = ($contractData['custom_number'] ? $contractData['number_client'] : $contractData['NUMBER']) ?? '';
            $this->data['contract_date'] = $contractData['DATE'] ?? '';
            $this->data['contract_type'] = $contractData['CONTRACT_TYPE'] ?? 'Договор';
            $this->data['deal_id'] = $dealId;
            $this->data['tz_id'] = $tzId;
            $this->data['curr_user'] = $_SESSION['SESS_AUTH']['USER_ID'];
            $this->data['deal_title'] = $tzData['REQUEST_TITLE'];

            $this->data['user_groups'] = json_encode($USER->GetUserGroupArray(), JSON_UNESCAPED_UNICODE);

            // Основание для формирования протокола (акт приемки проб)
            $this->data['act_number'] = $actData['ACT_NUM'] ?? '';
            $this->data['act_date'] = ! empty($actData['ACT_DATE']) && $actData['ACT_DATE'] !== '0000-00-00' ?
                date('d.m.Y', strtotime($actData['ACT_DATE'])) : '';

            // Описание объекта
            if (empty($tzData['price_discount'])) {
                $tzData['price_discount'] = $this->data['tz']['PRICE'];
            }

            $this->data['tz'] = $tzData;

            //// доп информация
            $this->data['probe_place'] = ! empty($actData['PLACE_PROBE']) ? $actData['PLACE_PROBE'] : 'Пробы не поступили';
            $this->data['probe_made'] = ! empty($actData['PROBE_PROIZV']) ? $actData['PROBE_PROIZV'] : 'Пробы не поступили';
            $this->data['probe_date'] = ! empty($actData['DATE_PROBE']) && $actData['DATE_PROBE'] !== '0000-00-00' ?
                date('d.m.Y', strtotime($actData['DATE_PROBE'])) :
                'Пробы не поступили';

            $duration = $requirementModel->getTzDuration($dealId);

            if ( /*empty($this->data['tz']['DAY_TO_TEST']) &&*/$duration > 0) {
                $this->data['tz']['DAY_TO_TEST'] = $duration;
            }

            $this->data['quarry_list'] = $quarryModel->getList();

            //// материалы
            $materialsTz = $requirementModel->getMaterialFromTz($dealId);
            //        $this->data['material_probe_list'] = $materialModel->getMaterialProbeToRequest($dealId);
            $this->data['tz_material_list'] = $materialsTz; //array_column($this->data['material_probe_list'], 'material_name', 'material_id');
            $this->data['lab_head'] = $requirementModel->getLabHead($dealId);


            //        $requirementModel->pre($this->data['lab_head'], true);
            //

            // статус проверенности ТЗ
            $this->data['check_state'] = $requirementModel->getStateConfirm($dealId);

            $this->data['form_leader'] = $requirementModel->getTzLabDuration($dealId);
            $this->data['confirm_not_approve_history'] = $requirementModel->getConfirmTzNotApproveHistory($tzId);

            $this->data['cancel_lab_head'] = $requirementModel->getCancelLabHead($dealId);

            if (!isset($this->data['check_state']) || $this->data['check_state'] == CHECK_TZ_NOT_SENT) {
                $this->showWarningMessage("Техническое задание еще не передано для проверки руководителям лабораторий");
            } elseif ($this->data['check_state'] == CHECK_TZ_NOT_APPROVE) {
                $desc = $requirementModel->getDescConfirmTzNotApprove($tzId);
                $msg = '';
                foreach ($desc as $row) {
                    $msg .= "<br><b>{$row['date_return']} {$row['user_name']}</b>: {$row['desc_return']}";
                }
                $this->showErrorMessage("Техническое задание было возвращено после проверки руководителем лаборатории.{$msg}");
            } elseif ($this->data['check_state'] == CHECK_TZ_WAIT) {
                $this->showWarningMessage("Техническое задание было передано для проверки руководителям лабораторий");
            } else {
                $this->showSuccessMessage("Техническое задание было одобрено руководителями лабораторий. Изменения сбросят все подтверждения.");
            }

            $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
            $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");

            $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');

            $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
            $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");
            $this->addCSS("/assets/plugins/DataTables/rowreorder/rowReorder.dataTables.css");

            $this->addJS("/assets/plugins/DataTables/DataTables-1.11.3/js/jquery.dataTables.min.js");
            $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/dataTables.buttons.min.js");
            $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.colVis.min.js");
            $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.print.min.js");
            $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.html5.min.js");
            $this->addJS("/assets/plugins/DataTables/JSZip-2.5.0/jszip.min.js");
            $this->addJS("/assets/plugins/DataTables/dataRender/ellipsis.js");
            $this->addJS("/assets/plugins/DataTables/dataRender/intl.js");
            $this->addJS("/assets/plugins/DataTables/FixedHeader-3.2.0/js/dataTables.fixedHeader.min.js");
            $this->addJS("/assets/plugins/DataTables/rowreorder/dataTables.rowReorder.js");
            $this->addJS("/assets/plugins/DataTables/rowreorder/rowReorder.dataTables.js");

            $this->addJS("/assets/plugins/sticksy/sticksy.min.js");

            $this->addJs('/assets/js/requirement_new_new.js?v=' . rand());
            $this->view('form_new_new');
        }


        /**
         * @deprecated
         */
        public function insertUpdate()
        {
            //TODO: Временные массивы данных, сохраняет сериализованные данные, для работы остальных скриптов до их рефакторинга
            //start
            $arrData = [];
            $probe = [];
            $key_p = 1;
            //end
            $tzId = (int)$_POST['tz_id'] ?? '';
            $materialDataList = [];
            $methodsId = [];
            $sumPrice = 0;

            if (empty($tzId)) {
                $this->redirect('/request/list/');
            }


            /** @var Requirement $requirement */
            $requirement = $this->model('Requirement');
            $tz = $requirement->getTzByTzId($tzId);

            if (empty($tz)) {
                $this->showErrorMessage("Технического задания с ИД {$tzId} не существует");
                $this->redirect('/request/list/');
            }

            $_SESSION['requirement_post'] = $_POST;

            $location = $tz['ID_Z'] >= DEAL_START_NEW_AREA ? "/requirement/card/{$tzId}" : "/requirement/card_old/{$tzId}";

            $successMsg = 'Техническое задание успешно изменено';


            foreach ($_POST['material'] as $key => $material) {
                $valid = $this->validateField($material['name'], 'Материал', true, '65000');

                if (!$valid['success']) {
                    $this->showErrorMessage($valid['error']);
                    $this->redirect($location);
                }

                foreach ($_POST['methods'][$key] as $methods) {
                    $valid = $this->validateField($methods['name'], 'Методика');

                    if (!$valid['success']) {
                        $this->showErrorMessage($valid['error']);
                        $this->redirect($location);
                    }
                }
            }

            foreach ($_POST['amount'] as $item) {
                $valid = $this->validateNumber($item, 'Количество проб/образцов', true);

                if (!$valid['success']) {
                    $this->showErrorMessage($valid['error']);
                    $this->redirect($location);
                }
            }


            /** @var Material $material */
            $material = $this->model('Material');
            /** @var Request $request */
            $request = $this->model('Request');
            /** @var Gost $gost */
            $gost = $this->model('Gost');
            /** @var User $user */
            $user = $this->model('User');


            //TODO: Временное получение данных сделки, для сохранения данных в сериалезованном виде, для работы остальных скриптов до их рефакторинга
            $deal = $request->getDealById($tz['ID_Z']);
            if (empty($deal)) {
                $this->showErrorMessage("Заявки с ИД {$tz['ID_Z']} не существует");
                $this->redirect('/request/list/');
            }
            //start
            $actBase = $requirement->getActBaseByDealId($deal['ID']);
            //end

            //TODO: Временный костыль, блокировка редактирования ТЗ после внесения данных в результаты испытаний
            $this->data['is_may_change'] = $deal['ID'] < 8846 || empty($requirement->getCountFilledResultData($deal['ID'])["count_umtr"]);

            $currentUserId = $user->getCurrentUserId();
            $currentUser = $user->getCurrentUser();
            $lastHistory = $requirement->getLastHistory($tzId);


            $historyAssigned = $lastHistory['ASSIGNED'] ?? '';
            $historyDate = date("d.m.Y H:i:s", strtotime($lastHistory['DATE']));
            $historyDateNull = date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
            if ($historyDate < $historyDateNull) $historyDate = '';

            $newAssignedToRequest = [];

            foreach ($_POST['material'] as $key => $item) {

                if (empty($_POST['amount'][$key])) {
                    continue;
                }

                if (empty($item['id'])) {
                    $resultAddMaterial = $material->add($item['name']);

                    if (empty($resultAddMaterial)) {
                        $this->showErrorMessage("Не удалось создать материал '{$item['name']}'");
                        $this->redirect($location);
                    } else {
                        $item['id'] = $resultAddMaterial;
                    }
                }

                foreach ($_POST['methods'][$key] as $k => $i) {

                    if (!empty($_POST['assign_method'][$key][$k])) {
                        $newAssignedToRequest[] = $_POST['assign_method'][$key][$k];
                    }

                    if (empty($i['id'])) {
                        if (true/*in_array($currentUserId, self::FORBIDDEN_CREATE_NEW_GOST)*/) {
                            //                        $this->showErrorMessage('Вам закрыт доступ на создание новых ГОСТов!');
                            $this->showErrorMessage('Не существует такого ГОСТа. Выбирайте из списка!');
                            $this->redirect($location);
                        }

                        $price = $_POST['price'][$key][$k] ?? '';
                        $lab = $_POST['labs'][$key][$k] ?? '';

                        $data = [
                            'GOST_TYPE' => 'metodic_otbor',
                            'SPECIFICATION' => $i['name'],
                            'PRICE' => $price,
                            'ED' => '-',
                            'NAME_GOST' => '-',
                            'NORM_COMMENT' => '-',
                            'ACCURACY' => 0,
                            'GOST' => '-',
                            'NORM1' => '-',
                            'NORM2' => '-',
                            'NON_ACTUAL' => 0,
                            'NUM_OA_NEW' => 1
                        ];

                        if (empty($lab)) {
                            $this->showErrorMessage("Не удалось создать гост '{$item['name']}' не указана лаборатория");
                            $this->redirect($location);
                        }

                        $resultAddGost = $gost->add($data);

                        if (empty($resultAddGost)) {
                            $this->showErrorMessage("Не удалось создать гост '{$item['name']}'");
                            $this->redirect($location);
                        } else {
                            $_POST['methods'][$key][$k]['id'] = $resultAddGost;
                        }

                        $dataUpdate = [
                            $lab => 1,
                        ];

                        $gost->update($resultAddGost, $dataUpdate);
                    }

                    $methodsId[] = $_POST['methods'][$key][$k]['id'];


                    //TODO: Временное сохранение данных, сохраняет сериализованные данные, для работы остальных скриптов до их рефакторинга
                    //start
                    $arrData[$key][$item['name']][$key]['gost'][] = $_POST['methods'][$key][$k]['id'];
                    $arrData[$key][$item['name']][$key]['price'][] = $_POST['price'][$key][$k];
                    $arrData[$key][$item['name']][$key]['gost_new'][] = $_POST['conditions'][$key][$k]['id'];
                    $arrData[$key][$item['name']][$key]['obiem'] = $_POST['amount'][$key];
                    //end
                }


                //TODO: Временное сохранение данных, сохраняет сериализованные данные, для работы остальных скриптов до их рефакторинга
                //start
                $probe[$key_p] = array(
                    "zakaz_man" => $deal['COMPANY_ID'],
                    "number_probe" => [],
                    "probe_proizv" => $actBase['PROBE_PROIZV'],
                    "number" => $actBase['ACT_NUM'],
                    "dat" => '',
                );
                //TODO: Временно для корресктного вывода проб

                if (isset($actBase['ACT_NUM']) && $actBase['ACT_NUM'] != '0') {
                    $requirement->savingShNumbersToActBase($deal['ID'], $actBase['ACT_NUM']);
                    $sh = json_decode($actBase['PROBE']);

                    $dateProbe = !empty($actBase['DATE_PROBE']) ? date("d.m.Y", strtotime($actBase['DATE_PROBE'])) : '-';
                    $placeProbe = !empty($actBase['PLACE_PROBE']) ? $_POST['PLACE_PROBE'] : '-';
                    $probe[$key_p]["mesto_data"] = $placeProbe . '; ' . $dateProbe;
                }

                for ($l = 0; $l < (int)$_POST['amount'][$key]; $l++) {

                    $probe[$key_p]["number_probe"][$l] = $item['name'];
                    if (!empty($actBase['ACT_NUM']) && $actBase['ACT_NUM'] != 0) {
                        $probe[$key_p]["sh_number"][$l] = $sh[$key][$l];
                        //                    $probe[$key_p]["sh_number"][$l] = $actBase['PROBE'][$key][$l];
                    }
                }
                $key_p++;
                //end


                $item['gosts'] = [
                    'gost_method' => $_POST['methods'][$key] ?? [],
                    'gost_conditions' => $_POST['conditions'][$key] ?? [],
                    'price' => $_POST['price'][$key] ?? [],
                    'assigned' => $_POST['assign_method'][$key] ?? []
                ];

                $materialDataList[$key] = $item;

                $sumPrice += array_sum($_POST['price'][$key]) * $_POST['amount'][$key];
            }

            $newAssignedToRequest = array_unique($newAssignedToRequest);

            $request->addAssignedToRequest($tz['ID_Z'], $newAssignedToRequest);

            $methodsNotInOA = $requirement->getMethodsNotInOA($methodsId);

            $dataTz = [
                'DESCRIPTION' => $_POST['DESCRIPTION'] ?? '',
                'OBJECT' => $_POST['OBJECT'] ? htmlentities($_POST['OBJECT'], ENT_QUOTES, 'UTF-8') : '',
                'DEADLINE' => $_POST['DEADLINE'] ?? date('Y-m-d'),
                'DEADLINE_TABLE' => !empty($_POST['DEADLINE']) ? date('d.m.Y', strtotime($_POST['DEADLINE'])) : date('Y-m-d'),
                'DAY_TO_TEST' => $_POST['DAY_TO_TEST'] ?? '',
                'type_of_day' => $_POST['type_of_day'] ?? '',
                'COMMENT_KP' => $_POST['COMMENT_KP'] ?? '',
                'COMMENT_TZ' => $_POST['COMMENT_TZ'] ?? '',
                'PRICE' => $sumPrice,
                'ATTESTAT' => $methodsNotInOA ? 0 : 1,
                'CONTROL_LIST' => self::SAMPLES_ACCEPTANCE_ACT,
                'USER_HISTORY' => $historyAssigned . ' ' . $historyDate
            ];

            if (!empty($_POST['QUARRY_ID'])) {
                $dataTz['QUARRY_ID'] = $_POST['QUARRY_ID'];
            }

            if (!empty($_POST['taken_certificate'])) {
                $dataTz['TAKEN_SERT_ISP'] = $_POST['taken_certificate'] ? 1 : 0;
            }

            if (!empty($_POST['hidden_is_discount'])) {
                $dataTz['DISCOUNT'] = $_POST['DISCOUNT'];
            }

            $requirement->updateTzByIdTz($tzId, $dataTz);


            $historyData = [
                'DATE' => date('Y-m-d H:i:s'),
                'ASSIGNED' => "{$currentUser['NAME']} {$currentUser['LAST_NAME']}",
                'PROT_NUM' => $tz['NUM_P'] ?? 'номер не присвоен',
                'TZ_ID' => $tzId,
                'USER_ID' => $currentUser['ID'],
                'TYPE' => 'Сохранение технического задания',
                'REQUEST' => $tz['REQUEST_TITLE']
            ];

            //TODO: Доделать сохранение INSERT INTO `PODGOTOVKA`

            $materialProbeGost = $requirement->updateMaterialProbeGostToRequest($tz['ID_Z'], $materialDataList, $_POST['amount']);
            $invoice = $requirement->getInvoice((int)$tzId);
            $requirement->saveHistory($historyData);
            $request->updateStageDeal($tz['ID_Z'], 'PREPARATION');

            // собирает шифры для проб в заявке
            $material->fillCipher($tz['ID_Z']);
            //собирает шифры для проб в заявке, для таблицы ulab_material_to_request
            $material->addCipher($tz['ID_Z']);

            if (!empty($materialProbeGost['error'])) {
                $this->showErrorMessage($materialProbeGost['error']);
                $this->redirect($location);
            }



            //TODO: Временный метод, сохраняет сериализованные данные, для работы остальных скриптов до их рефакторинга
            $requirement->savingSerializedData($tzId, $arrData, $probe);


            if (!empty($invoice)) {
                $this->showWarningMessage("Внимание! По заявке уже выставлен счет! Не забудьте переформировать его, если стоимость работ изменилась!");
            }

            if (!empty($materialProbeGost['success'])) {
                unset($_SESSION['requirement_post']);
                $this->showSuccessMessage($successMsg);
                $this->redirect($location);
            }
        }


        /**
         * обновление формы. новое тз
         */
        public function updateTz()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var Order $orderModel */
            $orderModel = $this->model('Order');
            /** @var Status $statusModel $tzId */
            $statusModel = $this->model('Status');
            /** @var User $user */
            $user = $this->model('User');
            /** @var Request $request */
            $request = $this->model('Request');

            $tzId = $_POST['tz_id'];
            $dealId = $_POST['deal_id'];
            $dataTz = $_POST['tz'];
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$tzId}");
            }
            $requirementModel->updateTzByIdTz($tzId, $dataTz);

            if (!empty($_POST['tz']['TAKEN_ID_DEAL'])) {
                $orderModel->changeOrderByHeadRequest($_POST['tz']['TAKEN_ID_DEAL'], $dealId);
            }

            $requirementModel->updateMaterial($dealId, $_POST['material_id']);
            $requirementModel->updateProbeMethod($dealId, $_POST['material']);
            $requirementModel->updateProbeMethodTest($dealId, $_POST['probes']);
            $requirementModel->updateSentOutLims($dealId, $dataTz);

            $requirementModel->updateAssigned($dealId);

            $mtrArray = $requirementModel->getMtrToDeal($dealId);

            if (count($mtrArray) > 0) {
                $requirementModel->confirmTzClear($tzId);
            }

            $assignedIds = $requirementModel->getAssignedGostToProbe($dealId);

            if (!empty($_POST['form_leader'])) {
                $requirementModel->setTzLabDuration($dealId, $_POST['form_leader']);
            }
            $currentUser = $user->getCurrentUser();
            $dealName = $request->getDealById($dealId);

            $historyData = [
                'DATE' => date('Y-m-d H:i:s'),
                'ASSIGNED' => "{$currentUser['NAME']} {$currentUser['LAST_NAME']}",
                'TZ_ID' => $tzId,
                'USER_ID' => $currentUser['ID'],
                'TYPE' => 'Сохранение технического задания.' . ' Цена после сохранения ' . $_POST['tz']['price_discount'],
                'REQUEST' => $dealName['TITLE']
            ];

            $requirementModel->saveHistory($historyData);

            $this->showSuccessMessage("Техническое задание успешно изменено");
            $this->redirect("/requirement/card_new/{$tzId}");
        }


        /**
         *
         */
        public function copyProbeToDeal()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var Requirement $requirementModel */
            $request = $this->model('Request');
            $dealId = $requirementModel->getDealIdByTzId($_POST['tz_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$_POST['tz_id']}");
            }
            $probeIdList = explode(',', $_POST['probe_id_list']);

            foreach ($probeIdList as $umtrId) {
                $requirementModel->copyProbeToDeal($umtrId, $_POST['child_deal_id']);
            }

            $this->showSuccessMessage("Добавлено");
            $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
        }


        /**
         * route /requirement/show/{tzId}
         * @param $tzId
         */
        public function show($tzId)
        {
            if (empty($tzId)) {
                $this->redirect('/request/list/');
            }

            $this->data['title'] = 'Техническое задание';

            $this->view('show');
        }


        /**
         * @desc добавление материала в новом тз
         */
        public function addMaterialToTz()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            $request = $this->model('Request');
            $dealId = $requirementModel->getDealIdByTzId($_POST['tz_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$_POST['tz_id']}");
            }
            $requirementModel->addMaterialToTz((int)$_POST['deal_id'], (int)$_POST['material_id'], (int)$_POST['number']);

            $this->showSuccessMessage("Добавлен объект испытаний");
            $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
        }


        /**
         * @desc добавление материала в новом тз
         */
        public function addProbeToMaterial()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            $request = $this->model('Request');
            $dealId = $requirementModel->getDealIdByTzId($_POST['tz_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$_POST['tz_id']}");
            }

            $requirementModel->addProbeToMaterial((int)$_POST['deal_id'], (array)$_POST['material_id'], (int)$_POST['number']);

            $this->showSuccessMessage("Добавлены пробы");
            $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
        }


        /**
         *
         */
        public function setCommentGtp()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $requirementModel->setCommentGtp((int)$_POST['ugtp_id'], (array)$_POST['form']);

            $this->showSuccessMessage("Добавлен комментарий");
            $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
        }


        /**
         * @desc добавление методик выбранным пробам в новом тз
         */
        public function addMethodsToProbe()
        {
            if (empty($_POST['probe_id_list'])) {
                $this->showErrorMessage("Не выбраны пробы");
                $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
            }

            if (empty($_POST['form'])) {
                $this->showErrorMessage("Нет методик для добавления");
                $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
            }

            $probeIdList = explode(',', $_POST['probe_id_list']);

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var Requirement $requirementModel */
            $request = $this->model('Request');
            $resultModel = $this->model('Result');
            $dealId = $requirementModel->getDealIdByTzId($_POST['tz_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            // if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
            //     $this->showErrorMessage("Запрещено");
            //     $this->redirect("/requirement/card_new/{$_POST['tz_id']}");
            // }
            // добавляем методики
            $requirementModel->addMethodsToProbe($probeIdList, $_POST['form']);

            // заполняем подтверждение методики
            $requirementModel->checkHaveLeader($_POST['deal_id'], $_POST['form']);

            $resultModel->setInfoToApprove($_POST['form'], $_POST['tz_id']);

            // обновляем цены у заявки
            $requirementModel->updatePrice($_POST['deal_id']);

            $showWarningMessage = $requirementModel->checkDogovorPrice($_POST['deal_id']);

            if ($showWarningMessage) {
                $this->showErrorMessage('Внимание! Сумма ТЗ превышает сумму договора!');
            }

            $this->showSuccessMessage("Добавлены методики");
            $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
        }


        /**
         * @desc редактирует или удаляет пробу
         */
        public function editProbe()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var Requirement $requirementModel */
            $request = $this->model('Request');
            $dealId = $requirementModel->getDealIdByTzId($_POST['tz_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$_POST['tz_id']}");
            }
            $probeIdList = explode(',', $_POST['probe_id']);

            if ($_POST['button'] === 'save') {
                foreach ($probeIdList as $probeId) {
                    $requirementModel->updateProbeInfo((int)$probeId, $_POST['form']);

                    $requirementModel->setGroupMaterialGroup((int)$probeId, $_POST['mg']);

                    $requirementModel->setProbeMethod($probeId, $_POST['deal_id'], $_POST['form']);

                    $requirementModel->setGroupAssignedProbeMethod((int)$probeId, $_POST['ga']);
                }


                $this->showSuccessMessage("Проба обновлена");
            } else if ($_POST['button'] === 'delete') {
                $allSuccess = true;
                $errors = [];
                foreach ($probeIdList as $probeId) {
                    $result = $requirementModel->deleteProbe((int)$_POST['deal_id'], (int)$probeId);

                    if (!$result['success']) {
                        $errors[] = $result['error'];
                        $allSuccess = false;
                    }
                }

                if ($allSuccess) {
                    $this->showSuccessMessage("Проба удалена");
                } else {
                    $str = implode('; ', $errors);
                    $this->showErrorMessage("Некоторые пробы не удалены: {$str}");
                }
            }

            $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
        }


        /**
         *
         */
        public function editMaterial()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var Requirement $requirementModel */
            $request = $this->model('Request');
            $dealId = $requirementModel->getDealIdByTzId($_POST['tz_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$_POST['tz_id']}");
            }
            $requirementModel->updateMaterialTz((int)$_POST['deal_id'], (int)$_POST['old_material_id'], (int)$_POST['material_id']);

            $this->showSuccessMessage("Объект испытаний обновлен");
            $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
        }


        /**
         *
         */
        public function confirmTzSent()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var Status $statusModel */
            $statusModel = $this->model('Status');

            $tzId = (int)$_POST['tz_id'];
            $dealId = (int)$_POST['deal_id'];

            if (empty($_POST['users'])) {
                $this->showErrorMessage("Техническое задание не отправлено на проверку. Не выбраны руководители.");
                $this->redirect("/requirement/card_new/{$tzId}");
            }

            $requirementModel->confirmTzSentUsers($dealId, $_POST['users']);

            $this->showSuccessMessage("Техническое задание отправлено на проверку");
            $this->redirect("/requirement/card_new/{$tzId}");
        }


        /**
         *
         */
        public function confirmTzSentForCanceled()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $tzId = (int)$_POST['tz_id'];
            $dealId = (int)$_POST['deal_id'];

            if (empty($_POST['users'])) {
                $this->showErrorMessage("Техническое задание не отправлено на проверку. Не выбраны руководители.");
                $this->redirect("/requirement/card_new/{$tzId}");
            }

            $requirementModel->confirmTzSentUsersForCanceled($dealId, $_POST['users']);

            $this->showSuccessMessage("Техническое задание отправлено на проверку");
            $this->redirect("/requirement/card_new/{$tzId}");
        }


        public function confirmTzSentAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $requirementModel->confirmTzSent($_POST['deal_id']);
        }


        public function confirmTzApproveAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $dataForTzLabDuration = [
                'dealId' => $_POST['deal_id'],
                'dateLeader' => $_POST['date_leader'],
                'daysLeader' => $_POST['days_leader'],
                'leaderId' => $_POST['leader_id'],
            ];

            $requirementModel->confirmTzApprove($_POST['tz_id'], $_SESSION['SESS_AUTH']['USER_ID'], $dataForTzLabDuration);
        }


        public function confirmTzSentApproveAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $requirementModel->confirmTzSent($_POST['deal_id']);

            $requirementModel->confirmTzApprove($_POST['tz_id'], $_SESSION['SESS_AUTH']['USER_ID']);
        }


        public function confirmTzNotApproveAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $requirementModel->confirmTzNotApprove($_POST['tz_id'], $_SESSION['SESS_AUTH']['USER_ID'], $_POST['desc']);
        }


        public function confirmTzClearAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $requirementModel->confirmTzNotApprove($_POST['tz_id'], $_SESSION['SESS_AUTH']['USER_ID']);
        }


        public function getMethodListAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            $response = [];

            if (!empty($_POST['gost']) && !empty($_POST['deal_id'])) {
                /** @var Requirement $requirement */
                $requirement = $this->model('Requirement');

                $response = $requirement->getGostsByName($_POST['gost'], $_POST['deal_id']);
            }

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        public function getGostsGroupAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            $response = [];

            if (!empty($_POST['id'])) {
                /** @var Requirement $requirement */
                $requirement = $this->model('Requirement');

                $response = $requirement->getGostsByMaterialId((int)$_POST['id']);
            }

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        public function isConfirmMethodAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            $response = [];

            if (!empty($_POST['id']) && !empty($_POST['methods_id'])) {
                /** @var Requirement $requirement */
                $requirement = $this->model('Requirement');
                /** @var User $user */
                $user = $this->model('User');


                $dealId = $requirement->getDealIdByTzId((int)$_POST['id']);
                $labsToMethod = $requirement->getLabsByMethodsId($_POST['methods_id']);
                $assigned = $user->getAssignedByDealId($dealId);

                $department = array_column($assigned, 'department');

                $labsToUser = array_map(function ($item) {
                    foreach ($item as $value) {
                        return $value;
                    }
                }, $department);

                foreach ($labsToMethod as $key => $value) {
                    if (empty($value)) {
                        $response['not_match'][$key] = 'Методика не привязана ни к одной лаборатории';
                    }

                    //is $labsToUser empty ??? -> !empty($value)
                    $isConfirmLabs = array_intersect($labsToUser, $value);

                    if (empty($isConfirmLabs) && !empty($value)) {
                        $usersByLab = $requirement->getUserByLabId($value);

                        $notMatch = array_keys($usersByLab);

                        $response['not_match'][$key] = $notMatch;

                        foreach ($usersByLab as $k => $v) {
                            $response['users_by_lab'][$k] = $v;
                        }
                    }
                }
            }

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        public function getTuForGostAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Gost $gost */
            $gost = $this->model('Gost');

            $response = [];
            $idGost = $_POST['id'];

            $arrTU = $gost->getTuByGostID($idGost);

            $tu = json_decode($arrTU['ID_TU'], true);

            foreach ($tu as $item) {

                $tuForOption = $gost->getGostForOption($item);

                $response[] = [
                    'ID' => $tuForOption['ID'],
                    'NORM_TEXT' => $tuForOption['NORM_TEXT'],
                    'GOST' => $tuForOption['GOST'],
                    'SPECIFICATION' => $tuForOption['SPECIFICATION'],
                    'view_gost' => $tuForOption['view_gost']
                ];
            }

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        public function getAssignedByGostIdAjax()
        {
            /** @var Gost $gost */
            $gost = $this->model('Gost');

            $gostId = $_POST['id'];

            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            $assigned = $gost->getAssignedByGostID($gostId);

            echo json_encode($assigned, JSON_UNESCAPED_UNICODE);
        }

        public function getUlabAssignedByGostIdAjax()
        {
            /** @var Gost $gost */
            $gost = $this->model('Gost');

            $gostId = $_POST['id'];

            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            $assigned = $gost->getUlabAssignedByGostID($gostId);

            echo json_encode($assigned, JSON_UNESCAPED_UNICODE);
        }


        public function deletePermanentMaterialAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $requirementModel->deleteMaterial($_POST['deal_id'], $_POST['material_id'], $_POST['mtr_id'], $_POST['number']);
        }


        public function deletePermanentMaterialGostAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $requirementModel->deleteMaterialGost($_POST['gtp_id'], $_POST['deal_id'], $_POST['material_id'], $_POST['numberGost'], $_POST['number']);
        }


        public function getMethodsAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Methods $methodsModel */
            $methodsModel = $this->model('Methods');

            $result = $methodsModel->getList();

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        public function getMethodDataAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Methods $methodsModel */
            $methodsModel = $this->model('Methods');

            $result = $methodsModel->get($_POST['id']);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        /**
         * Получить все уникальные методики, связанные с заданными пробами
         *
         * @return void
         */
        public function getMethodsByProbeListAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            $probeIdList = $_GET['probe_id_list'];

            if (empty($probeIdList)) {
                echo '';
            }

            /** @var Methods $methodsModel */
            $methodsModel = $this->model('Methods');

            $result = $methodsModel->getMethodsByProbeList($probeIdList);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        public function getTechCondListAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var TechCondition $tcModel */
            $tcModel = $this->model('TechCondition');

            $result = $tcModel->getList();

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        public function getQuarryAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Quarry $quarryModel */
            $quarryModel = $this->model('Quarry');

            $result = $quarryModel->getList();

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        /**
         *
         */
        public function deleteProbeMethodAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $result = $requirementModel->deleteProbeMethod($_POST['tz_id'], $_POST['id']);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        /**
         *
         */
        public function deleteProbeAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $result = $requirementModel->deleteProbe($_POST['deal_id'], $_POST['id']);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        /**
         *
         */
        public function deleteMaterial()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $result = $requirementModel->deleteMaterialNew($_POST['deal_id'], $_POST['id']);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        /**
         *
         */
        public function getProbeMethodsAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Material $materialModel */
            $materialModel = $this->model('Material');

            $result = $materialModel->getMaterialProbeToRequest((int)$_POST['deal_id'], '', (int)$_POST['probe_id']);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        /**
         *
         */
        public function getMaterialProbesAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Material $materialModel */
            $materialModel = $this->model('Material');

            $result = $materialModel->getMaterialProbeToRequest((int)$_POST['deal_id'], (int)$_POST['material_id']);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        /**
         *
         */
        public function getMaterialProbeJournalAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $filter = [
                'paginate' => [
                    'length' => $_POST['length'], // кол-во строк на страницу
                    'start' => $_POST['start'],  // текущая страница
                ],
                'search' => [],
                'order' => []
            ];

            foreach ($_POST['columns'] as $column) {
                if ($column['search']['value'] !== '') {
                    $filter['search'][$column['data']] = $column['search']['value'];
                }
            }

            if (!empty($_POST['material_id'])) {
                $filter['search']['material_id'] = $_POST['material_id'];
            }

            if (!empty($_POST['group_id'])) {
                $filter['search']['group_id'] = $_POST['group_id'];
            }

            if ($_POST['cipher'] !== '') {
                $filter['search']['cipher'] = $_POST['cipher'];
            }

            if (isset($_POST['order']) && !empty($_POST['columns'])) {
                $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
                $filter['order']['dir'] = $_POST['order'][0]['dir'];
            }

            $data = $requirementModel->getMaterialProbeJournal($_POST['deal_id'], $filter);

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


        /**
         *
         */
        public function getMethodJournalAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $filter = [
                'paginate' => [
                    'length' => $_POST['length'], // кол-во строк на страницу
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

            if (!empty($_POST['probe_id'])) {
                $filter['search']['probe_id'] = $_POST['probe_id'];
            }

            if (isset($_POST['order']) && !empty($_POST['columns'])) {
                $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
                $filter['order']['dir'] = $_POST['order'][0]['dir'];
            }

            $data = $requirementModel->getMethodJournal($_POST['deal_id'], $filter);

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


        /**
         *
         */
        public function deleteMethodAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var Request $request */
            $request = $this->model('Request');
            $dealId = $requirementModel->getDealIdByTzId($_POST['tz_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$_POST['tz_id']}");
            }
            $result = $requirementModel->deleteProbeMethod((int)$_POST['tz_id'], (int)$_POST['id']);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        /**
         *
         */
        public function changeGostNumberAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $requirementModel->changeGostNumber($_POST['data']);

            echo json_encode([], JSON_UNESCAPED_UNICODE);
        }


        /**
         *
         */
        public function updateMethodAjax()
        {

            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var User $user */
            $user = $this->model('User');
            /** @var Request $request */
            $request = $this->model('Request');
            $dealId = $requirementModel->getDealIdByTzId($_POST['tz_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$_POST['tz_id']}");
            }
            $arr = [
                'assigned' => [
                    'assigned_id' => $_POST['val'],
                ],
                'tu' => [
                    'tech_condition_string' => "'{$_POST['val']}'",
                ],
                'method' => [
                    'new_method_id' => $_POST['val'],
                    'method_id' => $_POST['val'],
                ],
                'price' => [
                    'price' => $_POST['val'],
                ],
            ];

            $result = $requirementModel->updateProbeGost((int)$_POST['tz_id'], (int)$_POST['id'], $arr[$_POST['field']]);

            $currentUser = $user->getCurrentUser();
            $dealId = $request->getDealIdByTzId($_POST['tz_id']);
            $dealName = $request->getDealById($dealId);


            $historyData = [
                'DATE' => date('Y-m-d H:i:s'),
                'ASSIGNED' => "{$currentUser['NAME']} {$currentUser['LAST_NAME']}",
                'TZ_ID' => $_POST['tz_id'],
                'USER_ID' => $currentUser['ID'],
                'TYPE' => "Изменены данные в техническом задании: {$_POST['field']}: {$_POST['lastVal']} => {{$_POST['val']}}",
                'REQUEST' => $dealName['TITLE']
            ];

            $requirementModel->saveHistory($historyData);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        /**
         *
         */
        public function getGroupProbeMethodAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');


            $result = $requirementModel->getGroupProbeMethod($_POST['probe_id_list']);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        /**
         *
         */
        public function copyProbeAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var Requirement $requirementModel */
            $request = $this->model('Request');
            $probeModel = $this->model('Probe');
            $dealId = $probeModel->getDealIdByProbe($_POST['umtr_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$_POST['tz_id']}");
            }
            $result = $requirementModel->copyProbe($_POST['umtr_id']);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        /**
         *
         */
        public function copyProbeMassiveAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var Requirement $requirementModel */
            $request = $this->model('Request');
            $probeModel = $this->model('Probe');
            $dealId = $probeModel->getDealIdByProbe($_POST['umtr_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$requestData['ID']}");
            }
            $tzId = $requirementModel->copyMassiveProbe($_POST['umtr_id'], $_POST['count']);

            $this->redirect("/ulab/requirement/card_new_new/{$tzId}");
        }


        public function setCheckFixedAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $result = $requirementModel->setConfirmTzFix((int) $_POST['id'], (int) $_POST['tz_id'], (int) $_POST['leader']);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }


        /**
         *
         */
        public function setCommentGtpAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $params = [];
            parse_str($_POST['form'], $params);

            $requirementModel->setCommentGtp((int)$_POST['ugtp_id'], (array)$params['form']);
        }

        public function getCommentGtpAjax()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            echo json_encode($requirementModel->getCommentGtp($_POST['ugtp_id']), JSON_UNESCAPED_UNICODE);
        }

        public function updateGroupType()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $data =
                [
                    'tz_id' => $_POST['tz_id'],
                    'group_type' => $_POST['group_type']
                ];

            $requirementModel->updateGroupType($data);
        }

        public function replaceMethodsForProbes()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var Requirement $requirementModel */
            $request = $this->model('Request');
            $dealId = $requirementModel->getDealIdByTzId($_POST['tz_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$_POST['tz_id']}");
            }
            $probeIdList = $_POST['probe_id'];
            $oldMethodId = $_POST['old_method'];
            $newMethodData = $_POST['method'];

            if (empty($probeIdList) || empty($oldMethodId) || empty($newMethodData)) {
                $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
            }

            foreach ($probeIdList as $probeId) {
                $requirementModel->replaceMethodListNew($probeId, $oldMethodId, $newMethodData);
            }

            $this->showSuccessMessage("Пробы обновлены");

            $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
        }

        /**
         * Удаляет все методики из пробы
         */
        public function deleteMethodsFromProbe()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');
            /** @var Request $request */
            $request = $this->model('Request');
            $dealId = $requirementModel->getDealIdByTzId($_POST['tz_id']);
            $requestData = $request->getTzByDealId($dealId);
            $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            if ($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
                $this->showErrorMessage("Запрещено");
                $this->redirect("/requirement/card_new/{$_POST['tz_id']}");
            }
            $probeIdList = $_POST['probe_id'];
            $methodId = $_POST['method_id'];

            if (empty($probeIdList) || empty($methodId)) {
                $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
            }

            foreach ($probeIdList as $probeId) {
                $requirementModel->deleteMethodsFromProbe($probeId, $methodId);
            }

            $this->showSuccessMessage("Пробы обновлены");

            $this->redirect("/requirement/card_new_new/{$_POST['tz_id']}");
        }

        public function deleteMassiveProbesAjax()
        {
            global $APPLICATION;
            /** @var Requirement $requirementModel */
            $request = $this->model('Request');
            $probeModel = $this->model('Probe');
            //$dealId = $probeModel->getDealIdByProbe($_POST['umtr']);
            //        $requestData = $request->getTzByDealId($dealId);
            //        $this->data['is_end_test'] = $requestData['STAGE_ID'] == 2 || $requestData['STAGE_ID'] == 4 || $requestData['STAGE_ID'] == 'WON';
            //        if($this->data['is_end_test'] && $_SESSION['SESS_AUTH']['USER_ID'] != 222 && $_SESSION['SESS_AUTH']['USER_ID'] != 212) {
            //            $this->showErrorMessage("Запрещено");
            //            $this->redirect("/requirement/card_new/{$requestData['ID']}");
            //        }
            $APPLICATION->RestartBuffer();

            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $data = json_decode($_POST['umtr'], JSON_UNESCAPED_UNICODE);

            $requirementModel->deleteMassiveProbes($data);

            echo json_encode(['message' => 'success'], JSON_UNESCAPED_UNICODE);
        }

        public function deleteCanceledLabAjax()
        {
            /** @var Requirement $requirementModel */
            $requirementModel = $this->model('Requirement');

            $tzId = $_POST['tzId'];
            $leaderId = $_POST['leaderId'];

            $requirementModel->deleteCanceledLab($tzId, $leaderId);

            echo json_encode(['message' => 'success'], JSON_UNESCAPED_UNICODE);
        }

        public function importProbesFromExcel()
        {
            global $APPLICATION;

            $APPLICATION->RestartBuffer();

            $requirementModel = $this->model("Requirement");

            $data =
                [
                    "dealId" => $_POST['deal_id'],
                    "fileExcel" => $_POST['file'],
                    "from" => strtoupper($_POST['from-letter']) . $_POST['from-digit'],
                    "to" => strtoupper($_POST['to-letter']) . $_POST['to-digit'],
                    "formatCipher" => $_POST['format_cipher'],
                    "ugtpId" => $_POST['ugtp']
                ];

            $requirementModel->importProbesFromExcel($data);

            $this->redirect("/ulab/requirement/card_new_new/{$_POST['tz_id']}");
        }
    }
