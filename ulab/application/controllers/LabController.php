<?php


/**
 * @desc Лаборатории
 * Class LabController
 */
class LabController extends Controller
{
    const ADMIN_PERMISSION_ID = 2; // id роли "Админ"
    const HEAD_IC_PERMISSION_ID = 3; // id роли "Руководитель ИЦ"
    const HEAD_LAB_PERMISSION_ID = 4; // id роли "Руководитель лаборатории"

    /**
     * route /lab/
     */
    public function index()
    {
        $this->redirect('/request/list/');
    }

    /**
     * Журнал условий
     */
    public function conditionList()
    {
        $this->data['title'] = 'Журнал условий окружающей среды';

        /** @var Lab $labModel */
        $labModel = $this->model('Lab');
        /** @var Permission $permissionModel */
        $permissionModel = $this->model('Permission');


        if (isset($_SESSION['room_id'])) {
            $this->data['room'] = $_SESSION['room_id'] + 100;
            $this->data['room_id'] = $_SESSION['room_id'];
            unset($_SESSION['room_id']);
        }

        if (!empty($_SESSION['conditions_post'])) {
            $this->data['form'] = $_SESSION['conditions_post']['form'];

            unset($_SESSION['conditions_post']);
        }

        $this->data['current_year'] = date('Y');
        $this->data['prior_year'] = date('Y', strtotime('-1 year'));
        $this->data['date_start'] = date('Y-m-d', strtotime('-1 year'));
        $this->data['date_end'] = date('Y-m-d');

        $permissionInfo = $permissionModel->getUserPermission($_SESSION['SESS_AUTH']['USER_ID']);
        $pressure = $labModel->getPressureByDate($this->data['date_end']);

        $this->data['rooms'] = $labModel->getLabaRoom();
        $this->data['pressure'] = end($pressure)['pressure'] ?? '';
        $this->data['may_edit_pressure'] = in_array($permissionInfo['id'],  [ADMIN_PERMISSION, HEAD_IC_PERMISSION]) || in_array($_SESSION['SESS_AUTH']['USER_ID'], ['10', '222', '215', '227']);
        // Проверка на доступ редактирования данных условий окружающей среды(помещения)
//        echo "<pre>";
//        print_r($permissionInfo);
//        echo "</pre>";
//        die();
        $this->data['is_may_edit'] = in_array($permissionInfo['id'],  [self::ADMIN_PERMISSION_ID, self::HEAD_IC_PERMISSION_ID, self::HEAD_LAB_PERMISSION_ID]) || in_array($_SESSION['SESS_AUTH']['USER_ID'], ['215']);
        $this->data['ttt'] = $permissionInfo;


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
        $this->addJs("/assets/js/conditions-list.js?v={$r}");

        $this->view('condition');
    }

    /**
     * получить данные журнала условий
     */
    public function getJournalConditionAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Lab $labModel */
        $labModel = $this->model('Lab');

        $filter = [
            'paginate' => [
                'length' => $_POST['length'],  // кол-во строк на страницу
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

        if (isset($_POST['order']) && !empty($_POST['columns'])) {
            $filter['order']['by'] = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        if ( !empty($_POST['dateStart']) ) {
            $filter['search']['dateStart'] = date('Y-m-d', strtotime($_POST['dateStart'])) . ' 00:00:00';
            $filter['search']['dateEnd'] = date('Y-m-d', strtotime($_POST['dateEnd'])) . ' 23:59:59';
        }

        if (!empty($_POST['room'])) {
            $filter['search']['room'] = $_POST['room'];
        }


        $data = $labModel->getJournalCondition($filter);

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

    public function updateDop()
    {
        $labModel = $this->model('Lab');

        $location = "/lab/conditionList/";

        $data =
            [
                'voltage' => $_POST['voltage'],
                'frequency' => $_POST['frequency']
            ];

        $labModel->updateDop($_POST['id'], $data);

        $this->redirect($location);
    }

    public function insertUpdate()
    {
        /** @var Lab $labModel */
        $labModel = $this->model('Lab');
        /** @var Methods $methodsModel */
        $methodsModel = $this->model('Methods');
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');

        $location = "/lab/conditionList/";

        if (empty($_POST['form']['room_id']) && $_POST['form']['room_id'] < 0) {
            $this->showErrorMessage('Не указан, или указан неверно ИД комнаты');
            $this->redirect($location);
        }


        $userId = $_SESSION['SESS_AUTH']['USER_ID'];
        $roomId = $_POST['form']['room_id'] - 100;
        $arrWarning = [];
        $isMethodMatch = 1;
        $isOborudMatch = 1;

        $_SESSION['conditions_post'] = $_POST;
        $_SESSION['room_id'] = $roomId;


        $methods = $methodsModel->getMethodsByRoom($roomId);
        $oboruds = $oborudModel->getOborudByRoom($roomId);


        //Проверка соответствия текущих условий и условий методик
        foreach ($methods as $data) {
            if (($_POST['form']['temp'] < $data['cond_temp_1'] || $_POST['form']['temp'] > $data['cond_temp_2']) &&
                empty($data['is_not_cond_temp'])) {
                $isMethodMatch = 0;
                $arrWarning['method_temp'][] =
                    "&bull; <a href='" . URI . "/gost/method/{$data['id']}' target='_blank'>{$data['reg_doc']} {$data['name']}</a>";
            }

            if (($_POST['form']['humidity'] < $data['cond_wet_1'] || $_POST['form']['humidity'] > $data['cond_wet_2']) &&
                empty($data['is_not_cond_wet'])) {
                $isMethodMatch = 0;
                $arrWarning['method_humidity'][] =
                    "&bull; <a href='" . URI . "/gost/method/{$data['id']}' target='_blank'>{$data['reg_doc']} {$data['name']}</a>";
            }

            //if (($_POST['form']['pressure'] < $data['cond_pressure_1'] || $_POST['form']['pressure'] > $data['cond_pressure_2']) &&
            //    empty($data['is_not_cond_pressure'])) {
            //    $isMethodMatch = 0;
            //    $arrWarning['method_pressure'][] =
            //        "&bull; <a href='" . URI . "/gost/method/{$data['id']}' target='_blank'>{$data['reg_doc']} {$data['name']}</a>";
            //}
        }

        //Проверка соответствия текущих условий и условий эксплуатации оборудования
        foreach ($oboruds as $data) {
            if (($_POST['form']['temp'] < $data['TOO_EX'] || $_POST['form']['temp'] > $data['TOO_EX2']) &&
                empty($data['TEMPERATURE'])) {
                $isOborudMatch = 0;
                $arrWarning['oborud_temp'][] =
                    "&bull; <a href='" . URI . "/oborud/edit/{$data['ID']}' target='_blank'>{$data['OBJECT']} {$data['TYPE_OBORUD']} {$data['REG_NUM']}</a>";
            }

            if (($_POST['form']['humidity'] < $data['OVV_EX'] || $_POST['form']['humidity'] > $data['OVV_EX2']) &&
                empty($data['HUMIDITY'])) {
                $isOborudMatch = 0;
                $arrWarning['oborud_humidity'][] =
                    "&bull; <a href='" . URI . "/oborud/edit/{$data['ID']}' target='_blank'>{$data['OBJECT']} {$data['TYPE_OBORUD']} {$data['REG_NUM']}</a>";
            }

            //if (($_POST['form']['pressure'] < $data['AD_EX'] || $_POST['form']['pressure'] > $data['AD_EX2']) &&
            //    empty($data['PRESSURE'])) {
            //    $isOborudMatch = 0;
            //    $arrWarning['oborud_pressure'][] =
            //        "&bull; <a href='" . URI . "/oborud/edit/{$data['ID']}' target='_blank'>{$data['OBJECT']} {$data['TYPE_OBORUD']} {$data['REG_NUM']}</a>";
            //}
        }

        if (!empty($arrWarning)) {
            $msgWarning = '';
            if (!empty($arrWarning['method_temp'])) {
                $msgWarning .= "<strong class='d-block'>Температура в помещении не соответствует условиям методики</strong>";
                $msgWarning .= implode('<br>', $arrWarning['method_temp']);
            }

            if (!empty($arrWarning['method_humidity'])) {
                $msgWarning .= "<strong class='d-block'>Влажность в помещении не соответствует условиям методики</strong>";
                $msgWarning .= implode('<br>', $arrWarning['method_humidity']);
            }

            //if (!empty($arrWarning['method_pressure'])) {
            //    $msgWarning .= "<strong class='d-block'>Давление в помещении не соответствует условиям методики</strong>";
            //    $msgWarning .= implode('<br>', $arrWarning['method_pressure']);
            //}

            if (!empty($arrWarning['oborud_temp'])) {
                $msgWarning .= "<strong class='d-block'>Температура в помещении не соответствует условиям эксплуатации оборудования</strong>";
                $msgWarning .= implode('<br>', $arrWarning['oborud_temp']);
            }

            if (!empty($arrWarning['oborud_humidity'])) {
                $msgWarning .= "<strong class='d-block'>Влажность в помещении не соответствует условиям эксплуатации оборудования</strong>";
                $msgWarning .= implode('<br>', $arrWarning['oborud_humidity']);
            }

            //if (!empty($arrWarning['oborud_pressure'])) {
            //    $msgWarning .= "<strong class='d-block'>Давление в помещении не соответствует условиям эксплуатации оборудования</strong>";
            //    $msgWarning .= implode('<br>', $arrWarning['oborud_pressure']);
            //}

            $this->showWarningMessage($msgWarning);
        }


        $_POST['form']['user_id'] = $userId;
        $_POST['form']['room_id'] = $roomId;
        $_POST['form']['is_method_match'] = $isMethodMatch;
        $_POST['form']['is_oborud_match'] = $isOborudMatch;

        //if (!empty($_POST['form']['date'])) {
        //    $date = new DateTime($_POST['form']['date']);
        //    $date->setTime(date('H'), date('i'), date('s'));
        //    $_POST['form']['updated_at'] = $date->format("Y-m-d H:i:s");
        //}

        if (!empty($_POST['form']['date'])) {
            $_POST['form']['updated_at'] = $_POST['form']['date'] ?: date('Y-m-d H:i');
        }

        if (!empty($_POST['id'])) {
            $result = $labModel->updateConditions($_POST['id'], $_POST['form']);
        } else {
            //if (!empty($_POST['form']['date'])) {
            //    $date = new DateTime($_POST['form']['date']);
            //    $date->setTime(date('H'), date('i'), date('s'));
            //    $_POST['form']['created_at'] = $date->format("Y-m-d H:i:s");
            //}
            if (!empty($_POST['form']['date'])) {
                $_POST['form']['created_at'] = $_POST['form']['date'] ?: date('Y-m-d H:i');
            }
            
            $result = $labModel->addConditions($_POST['form']);
        }

        if (empty($result)) {
            $this->showErrorMessage('Данные условий не удалось сохранить');
        } else {
            $this->showSuccessMessage('Данные условий сохранены успешно');
            unset($_SESSION['conditions_post']);
        }

        $this->redirect($location);
    }

    /**
     * @desc Сохранить/обновить данные атмосферного давления
     */
    public function pressureInsertUpdate()
    {
        /** @var Lab $labModel */
        $labModel = $this->model('Lab');

        $location = "/lab/conditionList/";
        $userId = $_SESSION['SESS_AUTH']['USER_ID'];

        $_SESSION['pressure_post'] = $_POST;
        $_POST['form']['user_id'] = $userId;

        if ( !empty($_POST['form']['date']) ) {
            $_POST['form']['updated_at'] = $_POST['form']['date'] ?: date('Y-m-d H:i');
        }

        if (!empty($_POST['pressure_id'])) {
            $result = $labModel->updatePressure($_POST['pressure_id'], $_POST['form']);
        } else {
            if (!empty($_POST['form']['date'])) {
                $_POST['form']['created_at'] = $_POST['form']['date'] ?: date('Y-m-d H:i');
            }
            $result = $labModel->addPressure($_POST['form']);
        }

        if ( empty($result) ) {
            $this->showErrorMessage('Данные атмосферного давления не удалось сохранить');
        } else {
            $this->showSuccessMessage('Данные атмосферного давления сохранены успешно');
            unset($_SESSION['pressure_post']);
        }

        $this->redirect($location);
    }

    /**
     * @desc Полученние данных атмосферного давления по дате
     */
    public function getPressureByDateAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Lab $labModel */
        $labModel = $this->model('Lab');

        $curDate = $_POST['datetime'] ? date('Y-m-d', strtotime($_POST['datetime'])): '';

        $result = $labModel->getPressureByDate($curDate);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * полученние данных условий по id
     */
    public function getConditionDataAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Lab $labModel */
        $labModel = $this->model('Lab');

        $result = $labModel->getConditionById($_POST['id']);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * удалить данные условия
     */
    public function removeConditionAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Lab $labModel */
        $labModel = $this->model('Lab');

        $response = [
            'success' => false,
            'error' => [
                'message' => "Не удалось удалить данные условия, не указан или указан неверно ИД условий",
            ]
        ];

        if (!empty($_POST['id']) && $_POST['id'] > 0) {
            $labModel->removeConditionById($_POST['id']);

            $this->showSuccessMessage("Данные условия удалены");

            $response = [
                'success' => true
            ];
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * получить не соответсвие темпиратуры, влажности уловию методики и оборудования
     */
    public function getNotConformityAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Lab $labModel */
        $labModel = $this->model('Lab');
        /** @var Methods $methodsModel */
        $methodsModel = $this->model('Methods');
        /** @var Oborud $oborudModel */
        $oborudModel = $this->model('Oborud');


        if (empty($_POST['id']) && $_POST['id'] < 0) {
            $response = [
                'success' => false,
                'error' => [
                    'message' => "Ошибка. Не указан или указан неверно ИД условий",
                ]
            ];

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            return;
        }


        $response = [
            'success' => true
        ];

        $msgWarning = '';
        $arrWarning = [];


        $condition = $labModel->getConditionById($_POST['id']);
        $roomId = $condition['room_id'] - 100;
        $methods = $methodsModel->getMethodsByRoom($roomId);
        $oboruds = $oborudModel->getOborudByRoom($roomId);


        //Проверка соответствия текущих условий и условий методик
        foreach ($methods as $data) {
            if (($condition['temp'] < $data['cond_temp_1'] || $condition['temp'] > $data['cond_temp_2']) &&
                empty($data['is_not_cond_temp'])) {
                $arrWarning['method_temp'][] =
                    "&bull; <a href='" . URI . "/gost/method/{$data['id']}' target='_blank'>{$data['reg_doc']} {$data['name']}</a>";
            }

            if (($condition['humidity'] < $data['cond_wet_1'] || $condition['humidity'] > $data['cond_wet_2']) &&
                empty($data['is_not_cond_wet'])) {
                $arrWarning['method_humidity'][] =
                    "&bull; <a href='" . URI . "/gost/method/{$data['id']}' target='_blank'>{$data['reg_doc']} {$data['name']}</a>";
            }

            //if (($condition['pressure'] < $data['cond_pressure_1'] || $condition['pressure'] > $data['cond_pressure_2']) &&
            //    empty($data['is_not_cond_pressure'])) {
            //    $arrWarning['method_pressure'][] =
            //        "&bull; <a href='" . URI . "/gost/method/{$data['id']}' target='_blank'>{$data['reg_doc']} {$data['name']}</a>";
            //}
        }

        //Проверка соответствия текущих условий и условий эксплуатации оборудования
        foreach ($oboruds as $data) {
            if (($condition['temp'] < $data['TOO_EX'] || $condition['temp'] > $data['TOO_EX2']) &&
                empty($data['TEMPERATURE'])) {
                $arrWarning['oborud_temp'][] =
                    "&bull; <a href='" . URI . "/oborud/edit/{$data['ID']}' target='_blank'>{$data['OBJECT']} {$data['TYPE_OBORUD']} {$data['REG_NUM']}</a>";
            }

            if (($condition['humidity'] < $data['OVV_EX'] || $condition['humidity'] > $data['OVV_EX2']) &&
                empty($data['HUMIDITY'])) {
                $arrWarning['oborud_humidity'][] =
                    "&bull; <a href='" . URI . "/oborud/edit/{$data['ID']}' target='_blank'>{$data['OBJECT']} {$data['TYPE_OBORUD']} {$data['REG_NUM']}</a>";
            }

            //if (($condition['pressure'] < $data['AD_EX'] || $condition['pressure'] > $data['AD_EX2']) &&
            //    empty($data['PRESSURE'])) {
            //    $arrWarning['oborud_pressure'][] =
            //        "&bull; <a href='" . URI . "/oborud/edit/{$data['ID']}' target='_blank'>{$data['OBJECT']} {$data['TYPE_OBORUD']} {$data['REG_NUM']}</a>";
            //}
        }

        if (!empty($arrWarning)) {
            if (!empty($arrWarning['method_temp'])) {
                $msgWarning .= "<strong class='d-block'>Температура в помещении не соответствует условиям методики</strong>";
                $msgWarning .= implode('<br>', $arrWarning['method_temp']);
            }

            if (!empty($arrWarning['method_humidity'])) {
                $msgWarning .= "<strong class='d-block'>Влажность в помещении не соответствует условиям методики</strong>";
                $msgWarning .= implode('<br>', $arrWarning['method_humidity']);
            }

            //if (!empty($arrWarning['method_pressure'])) {
            //    $msgWarning .= "<strong class='d-block'>Давление в помещении не соответствует условиям методики</strong>";
            //    $msgWarning .= implode('<br>', $arrWarning['method_pressure']);
            //}

            if (!empty($arrWarning['oborud_temp'])) {
                $msgWarning .= "<strong class='d-block'>Температура в помещении не соответствует условиям эксплуатации оборудования</strong>";
                $msgWarning .= implode('<br>', $arrWarning['oborud_temp']);
            }

            if (!empty($arrWarning['oborud_humidity'])) {
                $msgWarning .= "<strong class='d-block'>Влажность в помещении не соответствует условиям эксплуатации оборудования</strong>";
                $msgWarning .= implode('<br>', $arrWarning['oborud_humidity']);
            }

            //if (!empty($arrWarning['oborud_pressure'])) {
            //    $msgWarning .= "<strong class='d-block'>Давление в помещении не соответствует условиям эксплуатации оборудования</strong>";
            //    $msgWarning .= implode('<br>', $arrWarning['oborud_pressure']);
            //}

            $response = [
                'success' => false,
                'error' => [
                    'message' => $msgWarning,
                ]
            ];
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * получить средние значение условий
     */
    public function getMeanConditionsAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Lab $labModel */
        $labModel = $this->model('Lab');


        $response = [
            'success' => false,
            'error' => [
                'message' => "Не удалось получить средние значение условий, не указан или указан неверно ИД помещения",
            ]
        ];


        if (!empty($_POST['room_id']) && $_POST['room_id'] > 0) {
            $result = $labModel->getMeanConditions($_POST['room_id']);

            if (!empty($result)) {

                $response = [
                    'success' => true,
                    'data' => $result
                ];
            } else {
                $response = [
                    'success' => false,
                    'error' => [
                        'message' => "Не удалось получить средние значение условий",
                    ]
                ];
            }
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function getLabList()
	{
		$result = [];
		$res = $this->DB->Query("SELECT * FROM ba_laba");

		while ($row = $res->Fetch()) {
			$result[] = $row;
		}

		return $result;
	}

	public function getListAlt($id_bitrix = true, $is_short = false): array
	{
		$laboratories = $this->DB->Query("
            SELECT BL.*, DC.id_bitrix_dept as DEPARTMENT
            FROM ba_laba as BL, department_connection as DC
            WHERE DC.id_ulab_dept = BL.ID
        ");

		while ($row = $laboratories->Fetch()) {
			if ($id_bitrix)
				$results[$row['DEPARTMENT']] = $row['NAME'];
			else
				$results[$row['ID']] = $row['NAME'];

		}
		if ($is_short) {
			foreach ($results as $id => $name) {
				$name = str_replace("Отдел ", "", $name);
				$name = str_replace("-", " ", $name);
				$name = preg_replace("\([^\)]+\)", "", $name);
				$name = preg_replace("`\s\w{1,2}\s`u", " ", $name);
				$words = explode(" ", $name);
				foreach ($words as $key => $word) {
					$words[$key] = mb_strtoupper(mb_substr($word, 0, 1));
				}
				$name = implode("", $words);
				$results[$id] = $name;
			}
		}

		return $results ?? [];
	}

	public function getScaleBarAjax()
    {
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var Lab $labModel */
		$labModel = $this->model('Lab');

		$result = $labModel->getScaleBar();

		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}

	public function setScaleBarAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Lab $labModel */
        $labModel = $this->model('Lab');

        $labModel->pre($_POST);

        $result = $labModel->setScaleBar($_POST['id'], $_POST['value']);
    }
}
