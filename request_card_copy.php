<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04.04.2019
 * Time: 15:23
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Карточка заявки");

include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";
?>
    <style>

        input[type="button"] {

            margin: 5px 0 0 0px;

        }

        input[type="button"]:disabled {

            background-color: #C0C0C0;
            border: 1px solid #C0C0C0;
            cursor: default;
        }


    </style>
<?php


if (!empty($_POST['oplata']) || $_POST['oplata'] == '0') {
    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
    $row = $res->Fetch();
    $res_op = $DB->Query("UPDATE `ba_tz` SET `OPLATA`=" . $_POST['oplata'] . ", `DATE_OPLATA` = '" . date("d.m.Y", strtotime($_POST['oplata_date'])). "' WHERE ID = " . $row['ID']);

}

if (isset($_POST['comm_bt'])) {

    $res_comm = $DB->Query("SELECT * FROM `COMMENTS` WHERE `ID_REQ`=" . $_GET['ID'])->Fetch();
    if ($res_comm['ID'])
        $DB->Query("UPDATE `COMMENTS` SET `TEXT`='" . $_POST['comment'] . "' WHERE `ID_REQ` = " . $_GET['ID']);
    else
        $DB->Query("INSERT INTO `COMMENTS` (`ID_REQ`,`TEXT`) VALUES ('" . $_GET['ID'] . "', '" . $_POST['comment'] . "')");

}

if (!empty($_GET['AFTERPAY'])) {
    $DB->Query("UPDATE `ba_tz` SET `AFTERPAY`=" . ($_GET['AFTERPAY'] == 'N' ? 0 : 1) . " WHERE `ID` = " . $_GET['TZ_ID']);
}
//echo'<pre>';var_dump($_POST);die;
if (isset($_POST['btnTestEnd'])) {
    if (CModule::IncludeModule('crm')) {
        $deal1 = new CCrmDeal;
        $arDealUpdate = array('STAGE_ID' => '2');
        $resUpd = $deal1->Update((int)$_GET['ID'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    }
	
	 //----------------------'Серверная обработка Журнала заявок' начало---------------------//
    $get_id = !empty($_GET['ID']) ? (int)$_GET['ID'] : '';
	//--'Правки по вводу данных в журнал заявок' начало--//
        //$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос.
        //Обновляем стадию изменения в ba_tz
        $update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('2') . "', `STAGE_NUMBER`= " . (int)3 . " WHERE `ID_Z`=" . $get_id);
        //----------------------'Серверная обработка Журнала заявок' окончание---------------------//
		//--'Правки по вводу данных в журнал заявок' окончание--//

    $deal = CCrmDeal::GetByID((int)$_GET['ID']);

    $checkNunP = $DB->Query("SELECT `NUM_P` FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID'])->Fetch();

    if (!$checkNunP['NUM_P'] || $checkNunP['NUM_P'] == 'номер не присвоен') {
        //Исправил explode('-', date("Y-m-d")) на date("Y")
        $yearProtocol = date("Y");

        $resNunP = $DB->Query("SELECT * FROM `ba_tz`");
        $arrNumP = [];
        while ($rowNumP = $resNunP->Fetch()) {
            $foundYearProtocol = date("Y", strtotime($rowNumP['DATE_P_CORR']));
            if ($yearProtocol == $foundYearProtocol) {
                if (is_numeric($rowNumP["NUM_P"])) {
                    $arrNumP[] = $rowNumP["NUM_P"];
                }
            }
        }
        $NumP = max($arrNumP) + 1;
		
		//----------------------'Серверная обработка Журнала заявок' начало---------------------//
        //Колонка "Протокол" в журнале заявок
        //$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос
        $num_tz = !empty($NumP) ? $DB->ForSql($NumP . "/" . $yearProtocol) : '';
        //Добавил `DATE_P_CORR`='".date("Y-m-d")."'
        $resDateEnd = $DB->Query("UPDATE `ba_tz` SET `dateEnd`='" . date("Y-m-d") . "', `NUM_P`='" . $NumP . "', `NUM_P_TABLE`='" . $num_tz . "', `DATE_P_CORR`='" . date("Y-m-d") . "'
        WHERE `ID_Z`=" . $_GET['ID']);
         
        //Добавил `DATE_P_CORR`='".date("Y-m-d")."'
        //$resDateEnd = $DB->Query("UPDATE `ba_tz` SET `dateEnd`='" . date("Y-m-d") . "', `NUM_P`='" . $NumP . "', `DATE_P_CORR`='" . date("Y-m-d") . "'
		//WHERE `ID_Z`=" . $_GET['ID']);
		//----------------------'Серверная обработка Журнала заявок' окончание---------------------//
    } else {
        $resDateEnd = $DB->Query("UPDATE `ba_tz` SET `dateEnd`='" . date("Y-m-d") . "' WHERE `ID_Z`=" . $_GET['ID']);
    }
 //-------------'Сохранение истории' начало-----------------//
    $date_history_test_end = date('Y-m-d H:i:s');

    $assigned = CUser::GetByID($USER->GetID())->arResult[0];
    //Имя пользователя
    $assigned_name = !empty($assigned['NAME']) ? $assigned['NAME'] : '';
    //Фамилия пользователя
    $assigned_lastname = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';
    //Пользователь
    $assigned_user = $assigned_name.' '.$assigned_lastname;

    $res_history = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
    $ba_tz_history = $res_history->Fetch();
    //Номер протокола
    $ID_P = $ba_tz_history['NUM_P'] ? $ba_tz_history['NUM_P'] : 'номер не присвоен';
    //Номер заявки
    $num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';
	
	//----------------------'Серверная обработка Журнала заявок' начало---------------------//
	//---------'Изменение номера ТЗ' начало ------//
    //ТЗ
    $tz = !empty($ba_tz_history['ID']) ? $ba_tz_history['ID'] : '';
	
	$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date_history_test_end . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$tz . "','" . $USER->GetID() . "', 'Завершение испытания', '" . $num_request . "')");
	
    //$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date_history_test_end . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$ID . "','" . $USER->GetID() . "', 'Завершение испытания', '" . $num_request . "')");
    
	 //---------'Изменение номера ТЗ' окончание ------//
	//-------------'Сохранение истории' окончание-----------------//
	
        if ($ba_tz_history['ID'])
        {
            $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$tz. " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
            $history_ass=$history['ASSIGNED'];
            $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
            $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
            if($history_date<$history_date_null) $history_date='';
        }
        //Колонка "последнее изменение"
        $assignet_history = $history_ass . " " . $history_date;

        $update_assignet_ba_tz = $DB->Query("UPDATE ba_tz SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . $get_id);

    //----------------------'Серверная обработка Журнала заявок' окончание---------------------//
}																						  


if (!empty($_GET['ID'])) {

//--'Правки по вводу данных в журнал заявок' начало--//
	$id_get = !empty($_GET['ID']) ? (int)$_GET['ID'] : '';
//--'Правки по вводу данных в журнал заявок' окончание--//

//--'Проверка стадии АКТа ВР' начало--//
	$result_select_ba_tz = null;
	if (!empty($id_get)) {
		$result_select_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $id_get)->Fetch();
	}

	$result_select_akt_vr = null;
	if (!empty($result_select_ba_tz['ID'])) {
		$result_select_akt_vr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $result_select_ba_tz['ID'])->Fetch();
	}

    if ($_GET['STAGE'] == '1') {
        if (CModule::IncludeModule('crm')) {
            $deal1 = new CCrmDeal;
            $arDealUpdate = array('STAGE_ID' => 'PREPARATION');
            $resUpd = $deal1->Update((int)$_GET['ID'],
                $arDealUpdate,
                true,
                true,
                array('DISABLE_USER_FIELD_CHECK' => true)
            );
        }
		
        $update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('PREPARATION') . "', `STAGE_NUMBER`= " . (int)0 . " WHERE `ID_Z`=" . $id_get);
		
    } elseif ($_GET['STAGE'] == '0') {
		
		$stage_id = !empty($result_select_akt_vr['SEND_DATE']) ? '4' : '2';
		$stage_number = !empty($result_select_akt_vr['SEND_DATE']) ? (int)4 : (int)3;
		
        if (CModule::IncludeModule('crm')) {
            $deal1 = new CCrmDeal;
            //$arDealUpdate = array('STAGE_ID' => '2');
            $arDealUpdate = array('STAGE_ID' => $stage_id);
            $resUpd = $deal1->Update((int)$_GET['ID'],
                $arDealUpdate,
                true,
                true,
                array('DISABLE_USER_FIELD_CHECK' => true)
            );
        } 
		
        $update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql($stage_id) . "', `STAGE_NUMBER`= " . $stage_number . " WHERE `ID_Z`=" . $id_get);
    }
	//--'Проверка стадии АКТа ВР' окончание--//

    $deal = CCrmDeal::GetByID((int)$_GET['ID']);
    //$mater_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', (int)$_GET['ID']);
	
	//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
	//Находим ID договора по сделки
	//if (!empty($_GET['ID'])) {
	//	$resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$_GET['ID'])->Fetch();
	//}
	
	//if (!empty($resDealsContract['ID'])) {
		//Находим договор по ID договора
	//	$resDog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$resDealsContract['ID_CONTRACT'])->Fetch();
	//} 
	//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//

    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
    $row = $res->Fetch();

    if ($row) {
        $res_kp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
        $res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
        $res_tz_doc = $DB->Query("SELECT * FROM `TZ_DOC` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
		//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
        //$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
		
		//Если счет,(единично созданный в карточке заявки) существует выведит его
		//счета,(единично созданный в карточке заявки)
		$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
		
		//ID счета, одного(единичного созданного в карточке заявки)
		$id_invoice = !empty($res_acc['ID']) ? $res_acc['ID'] : '';
		
		//Если общий счет существует выведит его
		//Находим ID общего счета по ТЗ в таблице TZ_TO_INVOICE связывающей счета и ТЗ 
		$result_select_tz_to_invoice =  $DB->Query("SELECT * FROM `TZ_TO_INVOICE` WHERE `id_tz`=" . (int)$row['ID'] . " ORDER BY `ID` DESC  LIMIT 1")->Fetch();
		
		$actual_vel_sum_invoice = null;
		
		if (!empty($result_select_tz_to_invoice['id_invoice'])) {
			//Общий счет 
			$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `ID`=" . $result_select_tz_to_invoice['id_invoice'])->Fetch();
			
			//ID общего счета созданного в карточке договора
			$id_sum_invoice = !empty($res_acc['ID']) ? $res_acc['ID'] : '';
		}
		
		//Если существуют оба счета, счет (единично созданный в карточке заявки) и общий счет, находим их по ID и сортируем по ACTUAL_VER, берем последний созданый
		if (!empty($id_invoice) && !empty($id_sum_invoice)) {
			$res_acc = $DB->Query("SELECT *,RIGHT(ACTUAL_VER, 19) AS ACTUAL FROM `INVOICE` WHERE `ID`=" . $id_invoice ." || `ID` = " . $id_sum_invoice . " ORDER BY ACTUAL DESC LIMIT 1")->Fetch();
		}
		//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//
        $res_akt = $DB->Query("SELECT * FROM `ACT` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
        $res_akt_vr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
		
		//--'Техническое задание утверждено руководителем лаборатории' начало--//
		//$result_select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . (int)$row['ID']);
		//Проверяем была ли передпна заявка на проверку и ТЗ не вернули с расмотрения
		$result_select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . (int)$row['ID'] . " AND `date_return` IS NULL");
		//--'Техническое задание утверждено руководителем лаборатории' начало--//
    }
	
	//--'Техническое задание утверждено руководителем лаборатории' начало--//
	//количество выбранных записей при запросе
	$sum_select_check_tz = !empty($result_select_check_tz->SelectedRowsCount()) ? $result_select_check_tz->SelectedRowsCount() : '';
	
	//--Заявка ВЛК начало--//
	//Тип SALE-ИЦ, COMPLEX-ОСК, 1-ВЛК, 2-МСИ, 3-Дорожная
	//Тип выбранной заявки 
	$typeIdRequest = !empty($row['TYPE_ID']) ? $row['TYPE_ID'] : '';
	//echo'<pre>';var_dump($typeIdRequest);echo'</pre>';
	//--Заявка ВЛК окончание--//
	$confirm = 1;
	if (!empty($sum_select_check_tz)) {
		while($select_check_tz = $result_select_check_tz->Fetch()) {
			//Проверяем если есть руководитель который не проверил ТЗ присваиваем 0
			if (empty($select_check_tz['confirm'])) {
				$confirm = 0;
				break;
			}
		}
	}
	//--'Техническое задание утверждено руководителем лаборатории' начало--//
	
	//--'Не соответствие стадий в таблице заявок' начало--//
	if (!empty($row['STAGE_ID'])) {
		 switch ($row['STAGE_ID']) {
			case 'WON':
				$color='#70AD47';
				$title='Акты ВР получены. Заявка успешно завершена.';
				break;
			case 'LOSE':
				$color='red';
				$title='Испытания не проведены. Заявка прекращена.';
				break;
			case '7':
				$color='red';
				$title='Заявка прекращена. Не проводим подобные испытания.';
				break;
			case '6':
				$color='red';
				$title='Заявка прекращена. Заказчик не вышел на связь.';
				break;
			case '5':
				$color='red';
				$title='Заявка прекращена. Заказчика не устроила цена.';
				break;
			case '8':
				$color='red';
				$title='Заявка прекращена. Создана другая заявка.';
				break;
			case '9':
				$color='red';
				$title='Заявка прекращена. Заказчик выбрал лабораторию в своем городе.';
				break;
			case '10':
				$color='red';
				$title='Заявка прекращена. Заказчик решил не проводить испытания.';
				break;
			case '11':
				$color='red';
				$title='Заявка прекращена. Отказались сами в связи с высокой загруженностью.';
				break;
			case '12':
				$color='red';
				$title='Заявка прекращена. Судебная экспертиза.';
				break;
			case '13':
				$color='red';
				$title='Заявка прекращена. Участие в тендере.';
				break;
		}
	} 
	//--'Не соответствие стадий в таблице заявок' окончание--//

    $company = CCrmCompany::GetByID($deal['COMPANY_ID']); //Берём значение компании
    $companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch(); //Берём значение компании
    //$comEmail = CCrmFieldMulti::GetList(array(), array('TYPE_ID' => 'EMAIL', 'ELEMENT_ID' => $deal['COMPANY_ID']))->Fetch();
    //$comCont = CCrmFieldMulti::GetList(array(), array('ELEMENT_ID' => $deal['COMPANY_ID']))->Fetch();
    //$email = $comEmail['VALUE'];

    $req = new \Bitrix\Crm\EntityRequisite();
    $rs = $req->getList([
        "filter" => [
            "ENTITY_ID" => $deal['COMPANY_ID'],
        ]
    ]);


    $requis = $rs->Fetch();
    $email = $requis['RQ_EMAIL'];
    //$name_isp = 'Маргарита Черкозьянова';//$deal['ASSIGNED_BY_NAME'].' '.$deal['ASSIGNED_BY_LAST_NAME'];
     $assigned2 = CUser::GetByID($USER->GetID())->arResult[0];
    //Имя пользователя
    $assigned_name2 = !empty($assigned2['NAME']) ? $assigned2['NAME'] : '';
    //Фамилия пользователя
    $assigned_lastname2 = !empty($assigned2['LAST_NAME']) ? $assigned2['LAST_NAME'] : '';
    //Пользователь
    $assigned_user2 = $assigned_name2.' '.$assigned_lastname2;
    $name_isp =  $assigned_user2;//'Маргарита Печерская';

    $date_protocol = $row['DATE_P_CORR'] ? $row['DATE_P_CORR'] : $row['DATE_P'];
    // $deal = CCrmDeal::GetByID($row['ID_Z']);
    $y_date_act = date("Y", strtotime($row['DATE_ACT']));
    if ($y_date_act != '2020') $y_date_act = "/" . substr($y_date_act, 2);
    else $y_date_act = "/" . $y_date_act;

    $y_date_p = date("Y", strtotime($date_protocol));
    if ($y_date_p != '2020') $y_date_p = "/" . substr($y_date_p, 2);
    else $y_date_p = "/" . $y_date_p;

    $userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();

    $date_op = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
    $date_oplata = $date_op['UF_CRM_1579541506559']['VALUE'] ? date("d.m.Y", strtotime($date_op['UF_CRM_1579541506559']['VALUE'])) : "Не отправлено";

	//--'Техническое задание утверждено руководителем лаборатории' начало--// 
	//Главный ответственный
	$id_main_assigned = !empty($deal['ASSIGNED_BY_ID']) ? $deal['ASSIGNED_BY_ID'] : '';
	
	//Дополнительные ответственные
	$arr_id_additional_assigned = !empty($date_op['UF_CRM_1571643970']['VALUE']) ? $date_op['UF_CRM_1571643970']['VALUE'] : '';

	$all_id_assigned = null;
							
	if (!empty($arr_id_additional_assigned) && !empty($id_main_assigned)) {
			$all_id_assigned = $arr_id_additional_assigned;
			array_push($all_id_assigned, (int)$id_main_assigned, 1); 
	} else {
		//$all_id_assigned = [];
		//$all_id_assigned[] =  (int)$id_main_assigned;  
		$all_id_assigned =  [(int)$id_main_assigned, 1];  
	}
	
	//$all_id_assigned = [1];
	
	//--'Техническое задание утверждено руководителем лаборатории' окончание--//

    $symbReplace = array("/", " ");
    $dealTitle = str_replace($symbReplace, "_", $deal['TITLE']);
	
    echo "<div>
			<div class='return disable' title='Вернуться назад' disabled></div>";
    if (empty($_GET['PL'])) {
        $finDeal = 1;
        if ($deal['STAGE_ID'] == 'NEW' || $deal['STAGE_ID'] == 'PREPARATION' || $deal['STAGE_ID'] == 'FINAL_INVOICE' || $deal['STAGE_ID'] == 'PREPAYMENT_INVOICE' || $deal['STAGE_ID'] == 'EXECUTING' || $deal['STAGE_ID'] == '1') $finDeal = 0;

        ?>
        <a href="/requests_test.php">
            <div class='list' title='Вернуться к списку'></div><!--Вернуться к списку заявок--></a>
			<!--'Техническое задание утверждено руководителем лаборатории' начало-->
    <?php } else { ?> <a href="/planerka">Вернуться в планерку</a><?php }
    if (in_array('8', $USER->GetUserGroupArray()) && !$finDeal && (in_array($USER->GetID() , $all_id_assigned) || ($USER->GetID() == 62) || ($USER->GetID() == 35) || ($USER->GetID() == 43))) { ?> <!--|--> 
	<a href="/request_new.php?EDIT=<?= $_GET['ID'] ?>">
            <div class='edit' title='Редактировать'></div><!--Редактировать заявку--></a><? } ?>
			<!--'Техническое задание утверждено руководителем лаборатории' окончание-->
    <div class='card disable' title='Карточка заявки'></div>
    <div class='download disable' title='Скачать'></div>  

    <?php if ($row['ID']) { ?>
    <a href="/tz_copy.php?ID=<?= $row['ID'] ?>&ID_DEAL=<?= $_GET['ID'] ?>" target="_blank">
        <div class='copy' title='Скопировать'></div>
    </a>
<?php } ?>
    <div class='remove disable' title='Удалить'></div>
    <div class='add disable' title='Добавить'></div>
    <div class='report disable' title='Отчёт'></div>
    <div class='further disable' title='Далее'></div>
	<!--'Экспорт' начало-->
	<?php 
	$arr_stage = ['NEW','PREPARATION','PREPAYMENT_INVOICE','EXECUTING','FINAL_INVOICE'];
	if (!empty($row['STAGE_ID']) && in_array($row['STAGE_ID'], $arr_stage)) { 
	
	?>
		<!--<a href="/export.php?ID=<?= $row['ID'] ?>&ID_DEAL=<?= $_GET['ID'] ?>" target="_blank">-->
		<!--'Правки по импортированию заявки' начало-->
			<div class='export' title='Импортировать'></div>
		<!--'Правки по импортированию заявки' окончание-->
		<!--</a>-->
	<?php } ?>
	<!--'Экспорт' окончание-->

    </div>

    <h3><?php //(in_array('8', $USER->GetUserGroupArray())&&$deal['STAGE_ID']!='2'&&$deal['STAGE_ID']!='4'&&$deal['STAGE_ID']!='WON') ||
        if (($USER->GetID() == '1') || ($USER->GetID() == '7')|| ($USER->GetID() == '35')|| ($USER->GetID() == '62')) { ?>
        <a href='/crm/deal/details/<?= $deal['ID'] ?>/'><?
            } ?>Заявка <?= $deal['TITLE'] ?>
            от <?= $deal['DATE_CREATE'] ? date("d.m.Y", strtotime($deal['DATE_CREATE'])) : '' ?><?php if (($USER->GetID() == '1') || ($USER->GetID() == '7')|| ($USER->GetID() == '35')|| ($USER->GetID() == '62') ){ ?></a><?
     } ?>
        | <?= $deal['COMPANY_TITLE'] ?>
        | <?php echo getInitials($deal['ASSIGNED_BY_NAME']) . " " . $deal['ASSIGNED_BY_LAST_NAME'];
        $assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
        if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {

            foreach ($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign) {
                $assign_user = CUser::GetByID((int)$assign)->Fetch();
                echo ", " . getInitials($assign_user['NAME']) . " " . $assign_user['LAST_NAME'];
            }
        }
        ?></h3>
<br/>
    <b>Контактное лицо:</b> <?= $requis['RQ_NAME'] ?>
<br/>
    <b>Е-mail:</b> <?= $requis['RQ_EMAIL'] ?>
<br/>
    <b>Телефон:</b> <?= $requis['RQ_PHONE'] ?>
<br/>
<br/>
    <table id="r_card">
        <thead>

        <th>Тип документа</th>
        <th>Номер документа</th>
        <th>Дата документа</th>
        <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
            <th>Дата отправки клиенту</th><?
        } ?>
        <th>Операции</th>

        <!--	<th>Методика</th>
            <th>Цена</th>-->
        </thead>
			<!----------------------'Серверная обработка Журнала заявок' начало--------------------->
        <!--<tr style="background-color: #<//?= $row['ID'] ? 'd1ffca' : 'fff' ?>;">-->
		<tr style="background-color: #<?= $row['ID'] && $row['TZ'] ? 'd1ffca' : 'fff' ?>;">
            <td><b>Техническое задание</b></td>
            <!--<td><//?php if ($row['ID']) echo "<a href='/tz_show.php?ID=" . $row['ID'] . "'>" . $row['ID'] . "</a>"; else echo 'Не сформировано'; ?></td>-->
			<td><?php if ($row['ID'] && $row['TZ']) echo ($deal['TYPE_ID']!= 4 ? "<a href='/tz_show.php?ID=" . $row['ID'] . "'>" . $row['ID'] . "</a>" : 
			"<a href='/tz_show_nk.php?ID=" . $row['ID'] . "'>" . $row['ID'] . "</a>"); else echo 'Не сформировано'; ?></td>
            <!--<td><//?= $row['DATE_SOZD'] ? date("d.m.Y", strtotime($row['DATE_SOZD'])) : '' ?></td>-->
			<td><?= $row['DATE_SOZD'] && $row['TZ'] ? date("d.m.Y", strtotime($row['DATE_SOZD'])) : '' ?></td>
            <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                <td style="background-color: #FFF;"><?= $date_oplata ?></td><?
            } ?>
            <td style="background-color: #FFF;">

                <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
				<!--' Блокирование отправки писем без email' начало-->
                    <input type="button" title="Отправить клиенту бланк заявки" class="add send"
                           onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TYPE=1&EMAIL=<?= $email ?>&NAME=<?= $name_isp ?>&TITLE=<?= $dealTitle ?>', '_blank')"
                           <?= $row['ID'] && $row['TZ'] || (strpos($email, '@') == false) ? 'disabled' : '' ?>>
						   <!--' Блокирование отправки писем без email' окончание-->
                <?
                } 
				if($deal['TYPE_ID']!= 4) { ?>

                <input type="button" title='Сформировать' class="add toForm"
                       onclick="window.open ('/tz.php?ID=<?= $deal['ID'] ?>', '_self')"
                       <?= $row['ID'] && $row['TZ'] ? 'disabled' : '' ?>>
				<? } else {?>
				<input type="button" title='Сформировать' class="add toForm"
                       onclick="window.open ('/tz_nk.php?ID=<?= $deal['ID'] ?>', '_self')"
                       <?= $row['ID'] && $row['TZ'] ? 'disabled' : '' ?>>
				<? } ?>
            </td>
			<!----------------------'Серверная обработка Журнала заявок' окончание--------------------->
        </tr>
        <?php if (in_array('8', $USER->GetUserGroupArray()) && $typeIdRequest != '1') { ?>
            <tr style="background-color: #<?= $res_kp['ID'] ? 'd1ffca' : 'fff' ?>;">
            <td><b>Коммерческое предложение</b></td>
            <td><?= $res_kp['ID'] ? $res_kp['ID'] : 'Не сформировано' ?></td>
            <td> <?= $res_kp['DATE'] ? date("d.m.Y", strtotime($res_kp['DATE'])) : '' ?></td>
            <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                <td
                style="background-color: #<?= threeDayAfterSending($res_kp['SEND_DATE']) && !$res_dog['SEND_DATE'] ? 'FF0000' : 'FFF' ?>; "><?= $res_kp['SEND_DATE'] ? date('d.m.Y', strtotime($res_kp['SEND_DATE'])) : 'Не отправлено' ?></td><? } ?>
            <td style="background-color: #FFF;">
			<!--'Техническое задание утверждено руководителем лаборатории' начало-->
			<!--'Договор в request_new' начало-->
			<!--(!empty($sum_select_check_tz) && empty($confirm))- если ТЗ на проверки и не все Руководители работ проверили-->
                <input type="button" title="Сформировать" id='reload_kp' class="add toForm reload"
                       onclick="window.open ('/protocol_generator/kp.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>', '_blank')"
                       <?= !$row['TZ']|| $res_akt['ID'] || ($finDeal && $row['dateEnd']) || !empty($row['TAKEN_ID_DEAL']) || (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
                <!--'Договор в request_new' окончание-->
				<!--'Техническое задание утверждено руководителем лаборатории' окончание-->
				<!--' Блокирование отправки писем без email' начало-->
				<input type="button" title="Отправить клиенту" class="add send"
                       onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=2&EMAIL=<?= $email ?>&NAME=<?= $name_isp ?>&ATTACH=<?= $res_kp['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>', '_blank')"
                       <?= !$res_kp['ID'] || $res_akt['ID'] || (strpos($email, '@') == false) ? 'disabled' : '' ?>> 
					   <!--' Блокирование отправки писем без email' окончание-->
            </td>
            </tr>

            <?php

			//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
            if (!empty($_GET['ID'])) {
                $resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$_GET['ID'])->Fetch();
            }
			//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//
            //echo'<pre>';var_dump($resDealsContract);die;
            ?>
            <!--<tr style="background-color: #<//?= $res_dog['ID'] || !empty($row['DOGOVOR_NUM'] ) && !empty($resDealsContract) ? 'd1ffca' : 'fff' ?>;">-->
            <tr style="background-color: #<?= !empty($resDealsContract) ? 'd1ffca' : 'fff' ?>;">

                <!--<td><b>Договор</b></td><td> <//?php $name_dog = ($res_dog['NUMBER'] ? $res_dog['NUMBER'] : $res_dog['ID']); echo $name_dog ? "<a href='/contract_card.php?ID=".$res_dog['ID']."'>".$name_dog."</a>" : "Не сформирован";   ?></td>-->
                <td>
                    <b>Договор</b>
                </td>
                <td>
                    <?php
                    if (!empty($resDealsContract)) {
						//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
                        $dogovorArr = explode('ID', $row['DOGOVOR_NUM']);
                        $dogovorId = !empty($dogovorArr[1]) ? $dogovorArr[1] : '';
                        $resDog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$dogovorId)->Fetch();
						//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
                        $dogovorNumber = !empty($resDog['NUMBER']) ? $resDog['NUMBER'] : $dogovorId;
                        echo (!empty($dogovorNumber)) ? "<a href='/contract_card.php?ID=" . $resDog['ID'] . "'>"
                            . $dogovorNumber . "</a>" : '';
                    } else {
                        echo "Не сформирован";
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if (!empty($resDealsContract)) {
						//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
                        $dogovorArr = explode('ID', $row['DOGOVOR_NUM']);
                        $dogovorId = !empty($dogovorArr[1]) ? $dogovorArr[1] : '';
                        $resDog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$dogovorId)->Fetch();
						//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
                        echo !empty($resDog['DATE']) ? date('d.m.Y', strtotime($resDog['DATE'])) : '';
                    }
					//echo $res_dog['ID'];
                    ?>
                </td>
                <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                    <td
                    style="background-color: #<?= threeDayAfterSending($res_dog['SEND_DATE']) && !$res_acc['SEND_DATE'] ? 'FF0000' : 'FFF' ?>;"><?= $res_dog['SEND_DATE'] ? date('d.m.Y', strtotime($res_dog['SEND_DATE'])) : 'Не отправлялся из ЛИС' ?></td><? } ?>
                <td style="background-color: #FFF;">
                    <input type="button" title="Сформировать" class="add toForm reload"
                           onclick="window.open ('/protocol_generator/dogovor.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>', '_blank')"
                           <?= !$row['ID'] || $res_akt['ID'] || ($finDeal && $row['dateEnd']) || !empty($row['DOGOVOR_NUM'])  || ( ($USER->GetID() != 62) && ($USER->GetID() != 35) && ($USER->GetID() != 43) && ($USER->GetID() != 1)) ? 'disabled' : '' ?>>
                      <!--' Блокирование отправки писем без email' начало-->
					 <!--'Техническое задание утверждено руководителем лаборатории' начало-->
					<input type="button" title="Отправить клиенту" class="add send"
                           onclick="window.open ('/mail.php?ID=<?= $resDog['ID']/*$deal['ID']*/ ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=3&EMAIL=<?= $email ?>&NAME=<?= $name_isp ?>&ATTACH=<?= $res_dog['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>', '_blank')"
                           <?= (!$resDog['ID'] || (!empty($sum_select_check_tz) && empty($confirm)) || (strpos($email, '@') == false))   ? 'disabled' : '' ?>>
						   <!--' Блокирование отправки писем без email' окончание-->
                   <!--'Техническое задание утверждено руководителем лаборатории' окончание-->
                    <!--'Чекбокс для отметки выбранного письма'--> 
                    <input type="checkbox" name="check_dogovor" class="check-dogovor check-mail"
                           <?= !$resDog['ID'] ? 'disabled' : 'checked' ?>>
                </td>
            </tr>
            <tr style="background-color: #<?= $res_tz_doc['ID'] ? 'd1ffca' : 'fff' ?>;">
                <td><b>Прил. к договору (ТЗ)</b></td>
                <td><?= $res_tz_doc['ID'] ? $res_tz_doc['TZ_ID'] : 'Не сформировано' ?></td>
                <td> <?= $res_tz_doc['DATE'] ? date("d.m.Y", strtotime($res_tz_doc['DATE'])) : '' ?></td>
                <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                    <td
                    style="background-color: #<?= threeDayAfterSending($res_tz_doc['SEND_DATE']) && !$res_acc['SEND_DATE'] ? 'FF0000' : 'FFF' ?>; "><?= $res_tz_doc['SEND_DATE'] ? date('d.m.Y', strtotime($res_tz_doc['SEND_DATE'])) : 'Не отправлено' ?></td><? } ?>

                <td class='annexСontract' style="background-color: #FFF; position: relative;">
				<!--'Техническое задание утверждено руководителем лаборатории' начало-->
                    <input type="button" title="Сформировать" class="add toForm reload"
                           onclick="window.open ('/protocol_generator/tz_doc.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>', '_blank')"
                           <?= !$row['TZ'] || ($finDeal && $row['dateEnd']) || (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
                     <!--' Блокирование отправки писем без email' начало-->
					<!--'Техническое задание утверждено руководителем лаборатории' начало--> 
                    <input type="button" title="Отправить клиенту" class="add send"
                           onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=6&EMAIL=<?= $email ?>&NAME=<?= $name_isp ?>&ATTACH=<?= $res_tz_doc['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>', '_blank')"
                           <?= !$res_kp['ID']  || !$res_tz_doc['ID'] || (strpos($email, '@') == false) ? 'disabled' : '' ?>>
                    <!--'Чекбокс для отметки выбранного письма'-->
					 <!--' Блокирование отправки писем без email' начало-->
                    <input type="checkbox" name="check_tz" class="check-tz check-mail" <?= !$res_kp['ID'] || $res_akt['ID'] || !$res_tz_doc['ID'] ? 'disabled' : 'checked' ?>>
                    <!-----------------'Отправка писам Договор, Счет, ТЗ' окончание------------------->
                    <div class='wrapperOverall'>
                        <!--'Фигурная скобка'-->
                        <div class='bracket'></div>
                        <!-----------------'Отправка писам Договор, Счет, ТЗ' начало------------------->
                        <!--'Кнопка отправлени общего письма'-->
                        <input type="button" class='btnOverall'>
                        <!-----------------'Отправка писам Договор, Счет, ТЗ' окончание------------------->
                    </div>
                </td>
            </tr>
            </tr>
        <tr style="background-color: #<?= $res_acc['ID'] ? 'd1ffca' : 'fff' ?>;">
            <td><b>Счет</b></td>
			<!-------------'Формирование общего счета в pdf и отправка' начало----------------->
            <td><?= $res_acc['ID'] ? $res_acc['ID'] : "Не сформирован" ?><br><?= (!empty($res_acc['ID']) && $res_acc['ID'] == $id_sum_invoice) ? '(Общий счёт)' : '' ?></td>
			<!-------------'Формирование общего счета в pdf и отправка' окончание----------------->
            <td><?= $res_acc['DATE'] ? date("d.m.Y", strtotime($res_acc['DATE'])) : '' ?></td>
            <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                <td
                style="background-color: #<?= threeDayAfterSending($res_acc['SEND_DATE']) && !$row['DATE_ACT'] ? 'FF0000' : 'FFF' ?>;"><?= $res_acc['SEND_DATE'] ? date('d.m.Y', strtotime($res_acc['SEND_DATE'])) : 'Не отправлен' ?></td><? } ?>
            <td style="background-color: #FFF;">
			 <!--'Техническое задание утверждено руководителем лаборатории' начало-->
			<!-------------'Договор в request_new' начало----------------->
                <input type="button" title="Сформировать" class="add toForm reload"
                       onclick="window.open ('/protocol_generator/account.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>', '_blank')"
                       <?= !$row['ID'] || !$row['DOGOVOR_NUM'] || $res_akt['ID'] || ($finDeal && $row['dateEnd'])  || !empty($row['TAKEN_ID_DEAL']) || (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
                <!--' Блокирование отправки писем без email' начало-->
				<!---------------'Договор в request_new' окончание------------------->
				 <!--'Техническое задание утверждено руководителем лаборатории' окончание-->
				<input type="button" title="Отправить клиенту" class="add send"
                       onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=4&EMAIL=<?= $email ?>&NAME=<?= $name_isp ?>&ATTACH=<?= $res_acc['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>', '_blank')"
                       <?= !$res_acc['ID'] || $res_akt['ID'] || (!empty($res_acc['ID']) && $res_acc['ID'] == $id_sum_invoice || (strpos($email, '@') == false) ) ? 'disabled' : '' ?>>
                
                <!--'Чекбокс для отметки выбранного письма'-->
				<!--' Блокирование отправки писем без email' начало-->
                <input type="checkbox" name="check_account" class="check-account check-mail" <?= !$res_acc['ID'] || $res_akt['ID'] ? 'disabled' : 'checked' ?>>
                <!-----------------'Отправка писам Договор, Счет, ТЗ' окончание------------------->
            </td>
            </tr><?php }
        if (in_array('8', $USER->GetUserGroupArray())&& $typeIdRequest != '1') { ?>
        <tr class='payment'
            style="background-color: #<?= $row['OPLATA'] && ($row['OPLATA'] == $row['PRICE']) ? 'd1ffca' : 'fff' ?>;">
            <td><b>Оплата</b></td>
            <td><?php if ($row['OPLATA'] > $row['PRICE']) echo "Переплата";
                elseif ($row['OPLATA'] && ($row['OPLATA'] == $row['PRICE'])) echo "Оплачено полностью";
                elseif (!$row['OPLATA']) echo "Не оплачено";
                else echo "Оплачено частично";
                ?></td>
            <td><?= !$row['DATE_OPLATA']||$row['DATE_OPLATA']=='01.01.1970' ?  '' : date("d.m.Y", strtotime($row['DATE_OPLATA']))  ?></td>
            <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                <td style="background-color: #FFF;">-</td><?
            } ?>
            <td class='paymentTransactions' style="background-color: #FFF;"><?php if ($row['ID']) { ?>
                    <form method="POST" id="op">
					<!--'Нулевая оплата' начало-->
                    <input type="number" step="0.01" class="oborud" id="oplata_id" name="oplata" value="<?= $row['OPLATA'] ?>">
					<!--'Нулевая оплата' окончание-->
					<input type="date"  class="oborud" name="oplata_date" value="<?= !$row['DATE_OPLATA']||$row['DATE_OPLATA']=='01.01.1970' ?  date("Y-m-d") : date("Y-m-d", strtotime($row['DATE_OPLATA'])) ?>">
                    <!--'Техническое задание утверждено руководителем лаборатории' начало-->
					<input type="submit" title="Сформировать" class="toForm" value='' id="oplata_bt" <?= (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
                    <input type="button" title="Заполнить данные по оплате" class="fill" id='fillPayment' <?= (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
					<!--'Техническое задание утверждено руководителем лаборатории' окончание-->
                    <br/><br/>Остаток: <?php $ost = (float)$row['PRICE'] - (float)$row['PRICE'] * (float)$dataDiscountVal / 100 - (float)$row['OPLATA'];
                    echo $ost; ?> руб.

                    </form><?php } else { ?><input type="number" step="0.01" class="oborud" name="oplata"
                                                   value="<?= $row['OPLATA'] ?>" disabled>
                    <input type="button" title="Сформировать" id="oplata_bt" class="add toForm reload" disabled>

                    <input type="button" title="Заполнить данные по оплате" class="fill" id='fillPayment' disabled>

                <?
                } ?>
            </td>
            </tr><?
        } ?>
        <tr class='actNum' style="background-color: #<?= $row['ACT_NUM'] ? 'd1ffca' : 'fff' ?>;">
            <td><b>Акт приемки проб</b></td>
            <td> <?php if ($row['ACT_NUM']) echo "<a href='/probe.php?ID=" . $deal['ID'] . "'>" . $row['ACT_NUM'] . $y_date_act . " </a>"; else echo 'Не сформирован'; ?></td>
            <td><?= $row['ACT_NUM'] ? date("d.m.Y", strtotime($row['DATE_ACT'])) : '' ?></td>
            <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                <td style="background-color: #FFF;">-</td><?
            } ?>
            <td style="background-color: #FFF;">
                <input type="button" title="Сформировать" class="add toForm reload" id='actNumberBtn' disabled>
                <!--<input type="button" title="Сформировать" class="add toForm" id='actNumberBtn'<?= ($row['ACT_NUM'] || !$row['ID'] || ($finDeal && $row['dateEnd'])) ? 'disabled' : '' ?>>-->
				<!--'Техническое задание утверждено руководителем лаборатории' начало-->
                <input type="button" title="Заполнить данные акта" class="fill" id='fillBtn'
                       <?= ($row['ACT_NUM'] || !$row['ID'] || ($finDeal && $row['dateEnd'])) || (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
				<!--'Техническое задание утверждено руководителем лаборатории' окончание-->
            </td>
        </tr>
        <tr style="background-color: #<?= $row['RESULTS'] ? 'd1ffca' : 'fff' ?>;">
            <td><b>Результаты испытаний</b></td>
            <td><?php if ($row['RESULTS']) echo ( $deal['TYPE_ID']!= 4 ? "<a href='/results_isp.php?ID=" . $deal['ID'] . "'>" . $row['ID'] . " </a>"
			: "<a href='/results_isp_nk.php?ID=" . $deal['ID'] . "'>" . $row['ID'] . " </a>"); else echo 'Не внесены'; ?></td>
            <td><?php $date_results = '';
                if ($row['DATE_RESULTS']) $date_results = date("d.m.Y", strtotime($row['DATE_RESULTS']));
                elseif ($row['DATE_P']) $date_results = date("d.m.Y", strtotime($row['DATE_P']));
                // $date_res = $date_results ? $date_results : '';
                echo $date_results;
                ?></td>
            <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                <td style="background-color: #FFF;">-</td><?
            } ?>
            <td style="background-color: #FFF;">
			<? if($deal['TYPE_ID']!= 4) { ?>
                <input type="button" title="Внести результаты" class="add contribute"
                       onclick="window.open ('/results_isp.php?ID=<?= $deal['ID'] ?>', '_self')"
                       <?= (!$row['ACT_NUM'] || $row['RESULTS'] || !$row['ID'] || ($finDeal && $row['dateEnd'])) ? 'disabled' : '' ?>>
			<? } else { ?>
				<input type="button" title="Внести результаты" class="add contribute"
                       onclick="window.open ('/results_isp_nk.php?ID=<?= $deal['ID'] ?>', '_self')"
                       <?= (!$row['ACT_NUM'] || $row['RESULTS'] || !$row['ID'] || ($finDeal && $row['dateEnd'])) ? 'disabled' : '' ?>>
			<? } ?>
            </td>
        </tr>
<?php
		//--Заявка ВЛК начало--//
		//--Если заявка является ВЛК($typeIdRequest != '1'), то скрываем Протокол-->
		//if (in_array('8', $USER->GetUserGroupArray())) { 
		if ($typeIdRequest != '1') :
		//--Заявка ВЛК окончание--// ?>
         <tr style="background-color: #<?= $row['NUM_P'] ? 'd1ffca' : 'fff' ?>;">
            <td><b>Протокол</b></td>
            <td>
                <?php
                if ($row['NUM_P'] === 'номер не присвоен') {
                    echo $row['NUM_P'];
                } else {
                    echo $row['NUM_P'] ? $row['NUM_P'] . $y_date_p : 'Не сформирован';
                }
                ?></td>
            <td><?= $date_protocol ? date("d.m.Y", strtotime($date_protocol)) : '' ?></td>
            <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                <td
                style="background-color: #<?= threeDayAfterSending($row['PROTOCOL_SEND_DATE']) && !$res_akt_vr['ID'] ? 'FF0000' : 'FFF' ?>;"><?= $row['PROTOCOL_SEND_DATE'] ? date('d.m.Y', strtotime($row['PROTOCOL_SEND_DATE'])) : 'Не отправлен' ?></td><?
            } ?>
            <td style="background-color: #FFF;">
			<!--' Блокирование отправки писем без email' начало-->
                <input type="button" title="СФОРМИРОВАТЬ" class="add toForm reload"
                       onclick="window.open ('/protocol_generator/protocol.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>', '_blank')"
                       <?= !$row['NUM_P'] || $deal['STAGE_ID'] == '4' || $deal['STAGE_ID'] == 'WON' ? 'disabled' : '' ?>>
                <?php if (in_array('8', $USER->GetUserGroupArray())) { ?><input type="button" title="Отправить клиенту"
                                                                                class="add send"
                                                                                onclick="window.open ('/mail.php?ID=<?= $row['NUM_P'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=5&EMAIL=<?= $email ?>&NAME=<?= $name_isp ?>&ATTACH=<?= $row['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>', '_blank')"
                    <?= 1 || !$row['NUM_P'] || !$row['ACTUAL_VER'] || !$row['OPLATA'] || ($finDeal && $row['dateEnd']) || (strpos($email, '@') == false) ? 'disabled' : '' ?>><?
                } ?> <!--' Блокирование отправки писем без email' начало--><!-- временно функция отправки заблокирована-->
            </td>
        </tr>
		<?php endif; ?>
        <form method='POST' id='testEnd'>
            <tr class='testsEnd'
                style="background-color: #<?= ($deal['STAGE_ID'] == 2 || $deal['STAGE_ID'] == 4 || $deal['STAGE_ID'] == 'WON') ? 'd1ffca' : 'fff' ?>;">
                <td><b>Завершение испытаний</b></td>
                <td><?= $deal['STAGE_ID'] == ($deal['STAGE_ID'] == 2 || $deal['STAGE_ID'] == 4 || $deal['STAGE_ID'] == 'WON') ? 'Завершено' : 'Не завершено' ?></td>
                <td><?= $row['dateEnd'] ? ruDate($row['dateEnd']) : '' ?></td>
                <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                    <td style="background-color: #FFF;">-</td><?
                } ?>
                <td style="background-color: #FFF;">
                    <input type="submit" title="Завершить" value='' class="add complete reload" name='btnTestEnd'
                           <?= (!$row['PDF'] && !$row['ACTUAL_VER']) ? 'disabled' : '' ?>>
                </td>
            </tr>
        </form>
        <?php if (in_array('8', $USER->GetUserGroupArray()) && $typeIdRequest != '1') { ?>
            <tr class="aktVR" style="background-color: #<?= $res_akt_vr['ID'] ? 'd1ffca' : 'fff' ?>;">
                <td><b>Акт выполненных работ</b></td>
                <td><?php
                    if ($res_akt_vr['ID']) {
                        $actNumber = $res_akt_vr['NUMBER'] ? $res_akt_vr['NUMBER'] : $res_akt_vr['ID'];
                    } else {
                        $actNumber = 'Не сформирован';
                    }
                    echo $actNumber;
                    ?></td>
                <td><?= $res_akt_vr['DATE'] ? date("d.m.Y", strtotime($res_akt_vr['DATE'])) : '' ?></td>
                <td style="background-color: #<?= threeDayAfterSending($res_akt_vr['SEND_DATE']) ? 'FF0000' : 'FFF' ?>;"><?= $res_akt_vr['SEND_DATE'] ? date('d.m.Y', strtotime($res_akt_vr['SEND_DATE'])) : 'Не отправлен' ?></td>
                <td style="background-color: #FFF;">
					<!--'Серверная обработка Журнала заявок' начало-->
                    <!--<input type="number" step="1" class="oborud" id="akt_vr_num" value="<//?= $res_akt_vr['NUMBER'] ?>">-->
                    <!--<input type="date" class="oborud" id="akt_vr_date" value="<//?= $res_akt_vr['DATE'] ?>">-->
					<input type="number" step="1" class="oborud" id="akt_vr_num" value="<?= $res_akt_vr['NUMBER'] ?>">
                    <input type="date" class="oborud" id="akt_vr_date" value="<?= $res_akt_vr['DATE'] ?>">
					<!--'Серверная обработка Журнала заявок' окончание-->
                    <input id="deal_id" value="<?= $deal['ID'] ?>" hidden><input id="tz_id" value="<?= $row['ID'] ?>"
                                                                                 hidden>
				 <!--'Запрет на оправление АКТаВР при отсутствии Счета' начало-->
                    <input type="button" title="Сформировать" id="akt_vr_bt" class="add toForm"
                           <?= (!$res_acc['ID'] || (!empty($res_acc['ID']) && $res_acc['ID'] == $id_sum_invoice) || !$row['NUM_P'] || ($row['NO_BITRIX'] && !$row['PDF']) || (!$row['NO_BITRIX'] && !$row['ACTUAL_VER']))&&($USER->GetID() != '25') ? 'disabled' : '' ?>>
				 <!--'Запрет на оправление АКТаВР при отсутствии Счета' окончание-->
				 <!--' Блокирование отправки писем без email' начало-->
					<input type="button" title="Отправить клиенту" class="add send"
                           onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=8&EMAIL=<?= $email ?>&NAME=<?= $name_isp ?>&ATTACH=<?= $res_akt_vr['ACTUAL_VER'] ?>', '_blank')"
                           <?= !$res_akt_vr['ID'] || (strpos($email, '@') == false) ? 'disabled' : '' ?>> 
				   <!--' Блокирование отправки писем без email' окончание-->

                </td>
            </tr>
        <?
        } ?>


        <!--    </table>-->
        <?php
        }
        $res_comm = $DB->Query("SELECT * FROM `COMMENTS` WHERE `ID_REQ`=" . $deal['ID'])->Fetch();

        ?>
        </tbody></table><br/><br/>
    <form method="POST" id="comm">
        <table width="600px">
            <tr>
                <td>Комментарий</td>
                <td>
                    <!--<div style="display:flex; padding-left:5px">-->
                    <textarea class="tz" name="comment" size="200"><?= $res_comm['TEXT'] ?></textarea></td>
                <td><input type="submit" style="margin:0 0 0 10px" value="Добавить" name="comm_bt"></td>
            </tr>
        </table>
    </form>
    <!--<br /><br />
	Оплата после выдачи протокола <input type="checkbox" name="AFTERPAY" <?= !$row['AFTERPAY'] ? '' : 'checked' ?> <?= ($USER->GetID() == '1') || ($USER->GetID() == '10') || ($USER->GetID() == '8' || ($USER->GetID() == '9')) ? '' : 'disabled' ?> onclick="window.open ('/request_card.php?ID=<?= $deal['ID'] ?>&AFTERPAY=<?= $row['AFTERPAY'] ? 'N' : 'Y' ?>&TZ_ID=<?= $row['ID'] ?>')">-->
	<!--'Правки по вводу данных в журнал заявок' начало-->
	<br /><br />
	<!--Работы по заявке завершены <input type="checkbox" name="FIN" <//?= ($deal['STAGE_ID'] != '2' && $deal['STAGE_ID'] != '3' && $deal['STAGE_ID'] != '4' && $deal['STAGE_ID'] != 'WON') ? '' : 'checked' ?> <//?= ($USER->GetID() == '1') || ($USER->GetID() == '10') || ($USER->GetID() == '11') ? '' : 'disabled' ?> onclick="window.open ('/request_card.php?ID=<//?= $deal['ID'] ?>&STAGE=<//?= ($deal['STAGE_ID'] != '2' && $deal['STAGE_ID'] != '3' && $deal['STAGE_ID'] != '4' && $deal['STAGE_ID'] != 'WON') ? '0' : '1' ?>')">-->
	<?php
	$get_act_num = !empty($_GET['ACT_NUM']) ? $_GET['ACT_NUM'] : '';
	$get_name_act_num = !empty($_GET['ACT_NUM']) ? "&ACT_NUM=" : '';
	?>
	Работы в лаборатории завершены <input type="checkbox" name="FIN" <?= ($deal['STAGE_ID'] != '2' && $deal['STAGE_ID'] != '3' && $deal['STAGE_ID'] != '4' && $deal['STAGE_ID'] != 'WON') ? '' : 'checked' ?> <?= ($USER->GetID() == '1') || ($USER->GetID() == '10') || ($USER->GetID() == '35') || ($USER->GetID() == '62') || ($USER->GetID() == '11') || ($USER->GetID() == '58') || ($USER->GetID() == '34') || ($USER->GetID() == '43')? '' : 'disabled' ?> onclick="window.location.assign ('/request_card.php?ID=<?= $deal['ID'] ?><?= $get_name_act_num ?><?= $get_act_num ?>&STAGE=<?= ($deal['STAGE_ID'] != '2' && $deal['STAGE_ID'] != '3' && $deal['STAGE_ID'] != '4' && $deal['STAGE_ID'] != 'WON') ? '0' : '1' ?>')">
	<br/><br/>
	<!--'Не соответствие стадий в таблице заявок' начало--> 
	<button class="finish-deal" <?= ($USER->GetID() == '1') || ($USER->GetID() == '43')  || ($USER->GetID() == '10') || ($USER->GetID() == '35') || ($USER->GetID() == '62')  || ($USER->GetID() == '11') || ($USER->GetID() == '13')||($USER->GetID() == '32')|| ($USER->GetID() == '58') || ($USER->GetID() == '25') ? '' : 'disabled' ?>>Завершить заявку</button>
	<input type="text" class="text-finish-deal" style="<?= !empty($color) ? 'color:'.$color : ''?>" value="<?= !empty($title) ? $title : '' ?>">
	<br></br> 
	<!--'Не соответствие стадий в таблице заявок' окончание-->
	<!--'Правки по вводу данных в журнал заявок' окончание-->
<?php 
	//--Заявка ВЛК начало--//
	//--Если заявка является ВЛК($typeIdRequest != '1'), то скрываем Протокол--//
	if ($typeIdRequest != '1') :
	//--Заявка ВЛК окончание--// ?>
    Протокол в ОА <input type="checkbox" name="OA" <?= !$row['ATTESTAT'] ? '' : 'checked' ?> disabled>
    <br/><br/>

    <h3>Списки версий</h3>
    <table id="r_card">
        <thead>
        <th>Протокол</th>
        <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
            <th>КП</th>
            <th>Договор</th>
            <th>Прил. к договору (ТЗ)</th>
            <th>Счет</th>
            <th>Акт ВР</th><? } ?>
        </thead>
        <tbody>
        <tr>
            <td valign="top">

                <?php
                if ($row['NUM_P']) {
                    $dir = 'protocol_generator/archive/' . $row['ID'] . date("Y", strtotime($row['DATE_ACT'])) . '/';
                    $skip = array('.', '..');
                    $files = scandir($dir);
                    foreach ($files as $file) {
                        if (!in_array($file, $skip))
                            echo "<a href='$dir$file'>$file</a><br><br>";
                    }
                } ?>
            </td>
            <td valign="top">
                <?php
                if ($res_kp['ID']) {
                    $dir = 'protocol_generator/archive_kp/' . $deal['ID'] . '/';
                    $skip = array('.', '..');
                    $files = scandir($dir);
                    foreach ($files as $file) {
                        if (!in_array($file, $skip))
                            echo "<a href='$dir$file'>$file</a><br><br>";
                    }
                }
                ?>
            </td>
            <td valign="top">
                <?php
                $getId = !empty($_GET['ID']) ? $_GET['ID'] : '';

                $res_deals = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$getId)->Fetch();
                $res_dogovor = null;

                if (!empty($res_deals['ID_CONTRACT'])) {
                    $res_dogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$res_deals['ID_CONTRACT'])->Fetch();
                }
                $resDogovorNumber = $res_dogovor['ID'];

                if ($resDogovorNumber) {
                    //$dir  = 'protocol_generator/archive_dog/'.$deal['ID'].'/';
                    $dir = 'protocol_generator/archive_dog/' . $resDogovorNumber . '/';
                    $skip = array('.', '..');
                    $files = scandir($dir);
                    foreach ($files as $file) {
                        if (!in_array($file, $skip))
                            echo "<a href='$dir$file'>$file</a><br><br>";
                    }
                } ?>
            </td>
            <td valign="top">
                <?php
                if ($res_kp['ID']) {
                    $dir = 'protocol_generator/archive_tz/' . $row['ID'] . '/';
                    $skip = array('.', '..');
                    $files = scandir($dir);
                    foreach ($files as $file) {
                        if (!in_array($file, $skip))
                            echo "<a href='$dir$file'>$file</a><br><br>";
                    }
                }
                ?>
            </td>
            <td valign="top">
                <?php
                if ($res_acc['ID']) {
                    //$dir  = 'protocol_generator/archive_acc/'.$res_acc['ID'].'/';
                    $dir = 'protocol_generator/archive_acc/' . $deal['ID'] . '/';
                    $skip = array('.', '..');
                    $files = scandir($dir);
                    foreach ($files as $file) {
                        if (!in_array($file, $skip))
                            echo "<a href='$dir$file'>$file</a><br><br>";
                    }
                } ?>
            </td>
            <td valign="top">
                <?php  
                if ($res_akt_vr['ID']) {
                    //$dir  = 'protocol_generator/archive_acc/'.$res_acc['ID'].'/';
                    $dir = 'protocol_generator/archive_aktvr/' . $deal['ID'] . '/';
                    $skip = array('.', '..');
                    $files = scandir($dir);
                    foreach ($files as $file) {
                        if (!in_array($file, $skip))
                            echo "<a href='$dir$file'>$file</a><br><br>";
                    }
                } ?>
            </td>
        </tr>
        </tbody>
    </table>
	<!--Заявка ВЛК начало-->
	<?php endif; ?>
	<!--Заявка ВЛК окончание-->
	
<!--'Не соответствие стадий в таблице заявок' начало-->
<div class="window-finish-deal">
	<p>Выберите результат, с которым будет закрыта сделка.</p>
	<div class="wrapper-btn-stage">
		<button class="akt-sended btn-stage">Акты отправлены</button>
		<button class="akt-received btn-stage">Акты получены, сделка завершена</button>
		<button class="deal-lost btn-stage">Сделка проиграна</button>
		<button class="cancel btn-stage">Отменить</button>
	</div>
</div>


<!--'Модальное окно "Сделка проиграна"'-->
<div class="window-deal-lost">
	<p>Закрытие сделки:</p>
	<ul class="stages">
		<li class="wrapper-stage">
			<input id="STAGE_LOSE" type="radio" name="stage_id" class="stage" value='LOSE'>
			<label for="STAGE_LOSE">Сделка не состоялась</label>
		</li>
		<li class="wrapper-stage">
			<input id="STAGE_5" type="radio" name="stage_id" class="stage" value="5">
			<label for="STAGE_5">Не устроила цена</label>
		</li>
		<li class="wrapper-stage">
			<input id="STAGE_6" type="radio" name="stage_id" class="stage" value="6">
			<label for="STAGE_6">Заказчик не выходит на связь</label>
		</li>
		<li class="wrapper-stage">
			<input id="STAGE_7" type="radio" name="stage_id" class="stage" value="7">
			<label for="STAGE_7">Не проводим подобные испытания</label>
		</li>
		<li class="wrapper-stage">
			<input id="STAGE_8" type="radio" name="stage_id" class="stage" value="8">
			<label for="STAGE_8">Создана другая заявка по данному запросу</label>
		</li>
		<li class="wrapper-stage">
			<input id="STAGE_9" type="radio" name="stage_id" class="stage" value="9">
			<label for="STAGE_9">Заказчик выбрал лабораторию в своем городе</label>
		</li>
		<li class="wrapper-stage"> 
			<input id="STAGE_10" type="radio" name="stage_id" class="stage" value="10">
			<label for="STAGE_10">Заказчик решил не проводить испытания</label>
		</li>
		<li class="wrapper-stage">
			<input id="STAGE_11" type="radio" name="stage_id" class="stage" value="11">
			<label for="STAGE_11">Отказались сами - большая загруженность</label>
		</li>
		<li class="wrapper-stage">
			<input id="STAGE_12" type="radio" name="stage_id" class="stage" value="12">
			<label for="STAGE_12">Судебная экспертиза</label>
		</li>
		<li class="wrapper-stage">
			<input id="STAGE_13" type="radio" name="stage_id" class="stage" value="13">
			<label for="STAGE_13">Участие в тендере</label>
		</li>
	</ul>
	<div class="wrapper-btn-deal-lost">
		<button class="save-stage btn-stage">Сохранить</button>
		<button class="cancel btn-stage">Отменить</button>
	</div>
</div>
<!--'Не соответствие стадий в таблице заявок' окончание-->

<!--'Экспорт' начало-->
<!--'Модульное окно для экспорта заявок'-->
<div class="wrepper-modal-export">
	<div class="modal-export">
	<p>Введите номер экспортируемой заявки</p>
	<datalist id="select-export">
		<?php
			if (CModule::IncludeModule('crm')) {
				//Вывод списка сделок
				$deals_export = CCrmDeal::GetListEx(array('DATE_CREATE' => 'DESC'));
				
				$deal_id = !empty($deal['ID']) ? $deal['ID'] : ''; //ID текущей заявки(сделки)
				
				while($deal_export = $deals_export->Fetch()) {
					$number_id = !empty($deal_export['ID']) ? $deal_export['ID'] : ''; //ID заявки(сделки)
					//Проверяем если ID текущауй сделки(заявки) в которую импортируем равна = ID из списка, то не выводим в список экспортируемых (пропускаем)
					if (!empty($deal_id) && !empty($number_id) && $deal_id == $number_id) {
						continue;
					}
					$number_deal = !empty($deal_export['TITLE']) ? $deal_export['TITLE'] : ''; //Номер заявка(сделки)
					$company_deal = !empty($deal_export['COMPANY_TITLE']) ? $deal_export['COMPANY_TITLE'] : ''; //Компания(клиент)
					$begin_date_deal = !empty($deal_export['BEGINDATE']) ? $deal_export['BEGINDATE'] : ''; //дата начала
					echo '<option data-id="' . $number_id . '">Заявка ' . $number_deal . ', ' . $company_deal . ', от ' . $begin_date_deal . '</option>';
				}
			}
		?> 
	</datalist>
	<input type="text" class="export-input" multiple list="select-export" required>
	
	<div class="wrapper_export_btn">
	<!--'Правки по импортированию заявки' начало-->
		<button class="btn-export btn-export-copy">Импортирование заявки</button>
	<!--'Правки по импортированию заявки' окончание-->
		<!--<a href="/export.php?ID=<?= !empty($row['ID']) ? $row['ID'] : '' ?>&ID_DEAL=<?= !empty($_GET['ID']) ? $_GET['ID'] : '' ?>" class="btn-export 
		" target="_blank">
			Экспортировать заявку
		</a>-->
		<button class="btn-export btn-export-close">Закрыть</button>
	</div>
	</div>
<div>
<!--'Экспорт' окончание-->

    <link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
    <link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
    <!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"/>-->
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>

    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#tz').DataTable({
                "language": {
                    "processing": "Подождите...",
                    "search": "Поиск:",
                    "lengthMenu": "Показать _MENU_ записей",
                    "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                    "infoEmpty": "Записи с 0 до 0 из 0 записей",
                    "infoFiltered": "(отфильтровано из _MAX_ записей)",
                    "infoPostFix": "",
                    "loadingRecords": "Загрузка записей...",
                    "zeroRecords": "Записи отсутствуют.",
                    "emptyTable": "В таблице отсутствуют данные",
                    "paginate": {
                        "first": "Первая",
                        "previous": "Предыдущая",
                        "next": "Следующая",
                        "last": "Последняя"
                    },
                    "buttons": {
                        "copy": "",
                        'colvis': "",
                        'print': "",
                        "excel": ""
                    },
                    "aria": {
                        "sortAscending": ": активировать для сортировки столбца по возрастанию",
                        "sortDescending": ": активировать для сортировки столбца по убыванию"
                    }
                },
                "iDisplayLength": 50,
                "order": [[2, "desc"]],
                "columnDefs": [{"type": "num", "targets": 2}, {"sType": "ruDate", "targets": 1}
                ],
                "colReorder": true,
                "dom": 'Blfrtip',
                "buttons": ['copy', 'excel', 'colvis', 'print']

            });
        });

        jQuery.extend(jQuery.fn.dataTableExt.oSort, {
            "ruDate-asc": function (a, b) {
                var ruDatea = $.trim(a).split('.');
                var ruDateb = $.trim(b).split('.');

                if (ruDatea[2] * 1 < ruDateb[2] * 1)
                    return 1;
                if (ruDatea[2] * 1 > ruDateb[2] * 1)
                    return -1;
                if (ruDatea[2] * 1 == ruDateb[2] * 1) {
                    if (ruDatea[1] * 1 < ruDateb[1] * 1)
                        return 1;
                    if (ruDatea[1] * 1 > ruDateb[1] * 1)
                        return -1;
                    if (ruDatea[1] * 1 == ruDateb[1] * 1) {
                        if (ruDatea[0] * 1 < ruDateb[0] * 1)
                            return 1;
                        if (ruDatea[0] * 1 > ruDateb[0] * 1)
                            return -1;
                    } else
                        return 0;
                }
            },

            "ruDate-desc": function (a, b) {
                var ruDatea = $.trim(a).split('.');
                var ruDateb = $.trim(b).split('.');

                if (ruDatea[2] * 1 < ruDateb[2] * 1)
                    return -1;
                if (ruDatea[2] * 1 > ruDateb[2] * 1)
                    return 1;
                if (ruDatea[2] * 1 == ruDateb[2] * 1) {
                    if (ruDatea[1] * 1 < ruDateb[1] * 1)
                        return -1;
                    if (ruDatea[1] * 1 > ruDateb[1] * 1)
                        return 1;
                    if (ruDatea[1] * 1 == ruDateb[1] * 1) {
                        if (ruDatea[0] * 1 < ruDateb[0] * 1)
                            return -1;
                        if (ruDatea[0] * 1 > ruDateb[0] * 1)
                            return 1;
                    } else
                        return 0;
                }
            }
        });


        $(document).ready(function () {
            $('.testsEnd').find('td:eq(1)').html();
            if ($('.testsEnd').find('td:eq(1)').html() == 'Завершено') {
                $('input[name=btnTestEnd]').prop('disabled', true);
            }
			
			
        });

        $(document).ready(function () {
            var toForm = $('.toForm');
            var send = $('.send');
            var contribute = $('.contribute');
            var complete = $('.complete');

            toForm.each(function (index, value) {
                if ($(value).prop('disabled')) {
                    $(value).css('filter', 'grayscale(100%)');
                }
            });

            send.each(function (index, value) {
                if ($(value).prop('disabled')) {
                    $(value).css('filter', 'grayscale(100%)');
                }
            });

            if ($('.contribute').prop('disabled')) {
                $('.contribute').css('filter', 'grayscale(100%)');
            }

            if ($('.complete').prop('disabled')) {
                $('.complete').css('filter', 'grayscale(100%)');
            }

            if ($('.btnOverall').prop('disabled')) {
                $('.btnOverall').css('filter', 'grayscale(100%)');
            }

        });

        var clickNumber = 0;
        $(document).ready(function () {
            $('#actNumberBtn').on('click', function (event) {
					console.log('$(input[name=actNumber]).val()',$('input[name=actNumber]').val());  
                if ($('input[name=actNumber]').val() === '') {
                    event.preventDefault();
                    alert('Для формирования акта приемки проб введите номер!');
                } else {
					console.log('1111111111');
                    var actNumber = $('input[name=actNumber]').val();
                    var dateAct = $('input[name=DATE_ACT]').val();
                    var placeProbe = $('input[name=PLACE_PROBE]').val();
                    var dateProbe = $('input[name=DATE_PROBE]').val();

                    if (clickNumber > 0) {
                        var valDateProbe = $('input[name="DATE_PROBE"]').val();
                        var valActNum = $('input[name="actNumber"]').val();
                        var valDateAct = $('input[name="DATE_ACT"]').val();

                        if (!valDateProbe && valActNum && valDateAct) {
                            var saveDateProbe = confirm('Дата отбора проб, Не указана.Хотите продолжить?');
                            if (saveDateProbe) {
                                $.ajax({
                                    method: "POST",
                                    url: "add_act_num.php",
                                    data: {
                                        act: actNumber,
                                        id_z: '<?= !empty($row["ID_Z"]) ? $row["ID_Z"] : '' ?>',
                                        date_act: dateAct,
                                        place_probe: placeProbe,
                                        date_probe: dateProbe
                                    },
                                    success: function (data) {
										//console.log(data);
                                        if (data) {
                                            $('input[name=actNumber]').prop("disabled", true);
                                            $('input[name=DATE_ACT]').prop("disabled", true);
                                            $('input[name=PLACE_PROBE]').prop("disabled", true);
                                            $('input[name=DATE_PROBE]').prop("disabled", true);
                                            window.open('/probe.php?ID=<?=$row["ID_Z"]?>', '_blank');
                                        }
                                    }
                                });
                            }
                        } else {
                            console.log(dateProbe);
                            $.ajax({
                                method: "POST",
                                url: "add_act_num.php",
                                data: {
                                    act: actNumber,
                                    id_z: '<?= !empty($row["ID_Z"]) ? $row["ID_Z"] : '' ?>',
                                    date_act: dateAct,
                                    place_probe: placeProbe,
                                    date_probe: dateProbe
                                },
                                success: function (data) {

                                    if (data) {
                                        $('input[name=actNumber]').prop("disabled", true);
                                        $('input[name=DATE_ACT]').prop("disabled", true);
                                        $('input[name=PLACE_PROBE]').prop("disabled", true);
                                        $('input[name=DATE_PROBE]').prop("disabled", true);
                                        window.open('/probe.php?ID=<?=$row["ID_Z"]?>', '_blank');
                                    }
                                }
                            });
                        }
                    }

                }
            });
        });
		
		//--'Запрет на оправление АКТаВР при отсутствии Счета' начало--//
		function reloadWindow () {
			setTimeout(function() {
				//--'Изменение TZ_ID в истории' начало--//
                //window.location.reload(true); //Перезагрузить текущую страницу, без использования кэша
				var getActNum = '<?= $_GET["ACT_NUM"] ?>' ? '<?= $_GET["ACT_NUM"] ?>' : '';
                var getId = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';
				//запускает загрузку и отображение нового документа по указанному URL
				window.location.assign("/request_card.php?ID=" + getId + "&ACT_NUM=" + getActNum);
				//--'Изменение TZ_ID в истории' окончание--//
            }, 1500);
		}
		//--'Запрет на оправление АКТаВР при отсутствии Счета' окончание--//
		
		//--'Не соответствие стадий в таблице заявок' начало--//
		function reload() {
				var getActNum = '<?= $_GET["ACT_NUM"] ?>' ? '<?= $_GET["ACT_NUM"] ?>' : '';
                var getId = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';
				//запускает загрузку и отображение нового документа по указанному URL
				window.location.assign("/request_card.php?ID=" + getId + "&ACT_NUM=" + getActNum);
		}
		//--'Не соответствие стадий в таблице заявок' окончание--//

        $('.actNum').on('click', '#fillBtn', function (event) {

            var actNum = $('.actNum');
            var actNumberBtn = $('#actNumberBtn');

            if (clickNumber === 0) {
                $.ajax({
                    url: 'form_for_request_card.php?ID=<?= $_GET['ID'] ?>',
                    success: function (data) {
                        actNumberBtn.prop("disabled", false);
                        actNumberBtn.prop("style", "");
                        actNumberBtn.before(data);
                    }

                });
            }

            if (clickNumber > 0) {
                $('.formActPP').toggle();
            }
            clickNumber++;
        });

        $('.aktVR').on('click', '#akt_vr_bt', function (event) {

            var aktNumInp = $('#akt_vr_num');
            var aktDateInp = $('#akt_vr_date');
            var dealIdInp = $('#deal_id');
            var tzIdInp = $('#tz_id');
            var aktNum = aktNumInp[0].value;
            var aktDate = aktDateInp[0].value;
            var dealId = dealIdInp[0].value;
            var tzId = tzIdInp[0].value;
           
            //var actNumberBtn = $('#actNumberBtn');
            //document.location.href =
			//--'Запрет на оправление АКТаВР при отсутствии Счета' начало--//
			if (aktDate === '' && aktNum !== '') {
				alert('Для формирования акта выполненых работ введите номер!');
			} 
			
			if (aktNum === '' && aktDate === '' || aktNum !== '' && aktDate !== '') {
				window.open('/protocol_generator/akt_vr.php?ID=' + dealId + '&TZ_ID=' + tzId + '&NUM=' + aktNum + '&DATE=' + aktDate, '_blank');
				reloadWindow();
			}
            //--'Запрет на оправление АКТаВР при отсутствии Счета' окончание--//
			// if (clickNumber === 0) {
            // $.ajax({
            // url: 'form_for_request_card.php?ID=<?= $_GET['ID'] ?>',
            // success: function(data) {
            // actNumberBtn.prop( "disabled", false );
            // actNumberBtn.prop( "style", "" );
            // actNumberBtn.before(data);
            // }

            // });
            // }

            // if (clickNumber > 0) {
            // $('.formActPP').toggle();
            // }
            // clickNumber++;
        });

        $('.paymentTransactions').on('click', '#fillPayment', function (event) {
            var inputPayment = $('.paymentTransactions').find('input[name=oplata]');
            if ($(inputPayment).is(':hidden')) {
                $(inputPayment).css('display', 'block');
                // $element не виден
            } else {
                $(inputPayment).css('display', 'none');
            }
			var inputPayment = $('.paymentTransactions').find('input[name=oplata_date]');
            if ($(inputPayment).is(':hidden')) {
                $(inputPayment).css('display', 'block');
                // $element не виден
            } else {
                $(inputPayment).css('display', 'none');
            }
        });

        /*-----------------'Отправка писам Договор, Счет, ТЗ' начало-------------------*/
        //если не выбрано ни одно письмо(нет выбраных чебоксов), блокируем кнопку отправки писем Договор, Счет, ТЗ
        if (!$('.check-dogovor').prop('checked') && !$('.check-tz').prop('checked') && !$('.check-account').prop('checked')) {
            $('.btnOverall').prop('disabled', true);
        }

        //Если выбрано хоть одно письмо, разблокируем кнопку отправки писем Договор, Счет, ТЗ
        if ($('.check-dogovor').prop('checked') || $('.check-tz').prop('checked') || $('.check-account').prop('checked')) {
            $('.btnOverall').prop('disabled', false);
        }

        $('#r_card').on('click','.check-mail', function (event) {
            //если не выбрано ни одно письмо(нет выбраных чебоксов), блокируем кнопку отправки писем Договор, Счет, ТЗ
            if (!$('.check-dogovor').prop('checked') && !$('.check-tz').prop('checked') && !$('.check-account').prop('checked')) {
                $('.btnOverall').prop('disabled', true);
            }

            //Если выбрано хоть одно письмо разблокируем кнопку отправки писем Договор, Счет, ТЗ
            if ($('.check-dogovor').prop('checked') || $('.check-tz').prop('checked') || $('.check-account').prop('checked')) {
                $('.btnOverall').prop('disabled', false);
            }
        });

        $('#r_card').on('click','.btnOverall', function (event) {
            var strMail = '';
            $('.check-mail:checked').each(function (indCheckMail, valCheckMail) {
                //выбранные письма Договор, Счет, ТЗ(чекбоксы) добавляем в строку отправки
                if($(this).hasClass('check-dogovor')) {
                    strMail += '&CHECK[]=Договор';
                }

                if($(this).hasClass('check-tz')) {
                    strMail += '&CHECK[]=ТЗ';
                }

                if($(this).hasClass('check-account')) {
                    strMail += '&CHECK[]=Счет';
                }
            });

            //Отправка писем
            window.open ("/mail.php?ID=<?= $resDog['ID'] ?>&ID2=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=7&EMAIL=<?= $email ?>&NAME=<?= $name_isp ?>&ATTACH1=<?= $resDog['ACTUAL_VER'] ?>&ATTACH2=<?= $res_acc['ACTUAL_VER'] ?>&ATTACH3=<?= $res_tz_doc['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>" + strMail, "_blank");
        });
        /*-----------------'Отправка писам Договор, Счет, ТЗ' окончание----------------*/

		//--'Запрет на оправление АКТаВР при отсутствии Счета' начало--//
		$('.reload').click(function() {
            //setTimeout(function() {
				//--'Изменение TZ_ID в истории' начало--//
                //window.location.reload(true); //Перезагрузить текущую страницу, без использования кэша
				//var getActNum = '<?= $_GET["ACT_NUM"] ?>' ? '<?= $_GET["ACT_NUM"] ?>' : '';
                //var getId = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';
				//запускает загрузку и отображение нового документа по указанному URL
				//window.location.assign("/request_card.php?ID=" + getId + "&ACT_NUM=" + getActNum);
				//--'Изменение TZ_ID в истории' окончание--//
            //}, 1500);
			
			reloadWindow();
        });
		//--'Запрет на оправление АКТаВР при отсутствии Счета' окончание--//

		//--'Не соответствие стадий в таблице заявок' начало--//
		//'Кнопка завершить сделку'
		$('.finish-deal').on('click', function() {
			$('.window-finish-deal').css('display', 'block');
		});
		
		//Кнопка Акты получены, сделка завершена
		$('.akt-received').on('click', function() {
			//Скрываем модальное окно
			$('.window-finish-deal').css('display', 'none');
			
			var tz_id = $('#tz_id') ? $('#tz_id').val() : '';
	 
			if (tz_id) {
				//Отправляем ajax запрос для изменения стадии сделки
				$.ajax({
					method: 'POST',
					url: 'update_stage_id.php',
					data: {
						satge_id: 'WON',
						tz_id: tz_id, 
					},
					success: function(textContent) {
						//Если обновление прошло успешно, выводим сообщение
						if (textContent) {
							//$('.text-finish-deal').val(textContent).css('color', 'green');
							
							//запускает загрузку и отображение нового документа(перезагружаем)
							reload();
						}
					}
				});
			}
		});
		
		//Кнопка Акты получены, сделка завершена
		$('.akt-sended').on('click', function() {
			//Скрываем модальное окно
			$('.window-finish-deal').css('display', 'none');
			
			var tz_id = $('#tz_id') ? $('#tz_id').val() : '';
	 
			if (tz_id) {
				//Отправляем ajax запрос для изменения стадии сделки
				$.ajax({
					method: 'POST',
					url: 'update_stage_id.php',
					data: {
						satge_id: '4',
						tz_id: tz_id, 
					},
					success: function(textContent) {
						//Если обновление прошло успешно, выводим сообщение
						if (textContent) {
							//$('.text-finish-deal').val(textContent).css('color', 'green');
							
							//запускает загрузку и отображение нового документа(перезагружаем)
							reload();
						}
					}
				});
			}
		});
		
		//Кнопка Сделка проиграна
		$('.deal-lost').on('click', function() {
			//Скрываем модальное окно выбора завершения сделки
			$('.window-finish-deal').css('display', 'none');
			
			//Показываем модальное окно "Сделка проиграна" (список, в связи с чем)
			$('.window-deal-lost').css('display', 'block');
		});
		
		//Кнопка Отменить
		$('.cancel').on('click', function() {
			
			if ($('.window-finish-deal').css('display') == 'block') {
				//Скрываем модальное окно выбора завершения сделки
				$('.window-finish-deal').css('display', 'none');
			}
			
			if ($('.window-deal-lost').css('display') == 'block') {
				//Скрываем модальное окно "Сделка проиграна" (список, в связи с чем)
				$('.window-deal-lost').css('display', 'none');
			} 
			
		});
		
		//Кнопка Сохранить (проигранную сделку)
		$('.save-stage').on('click', function() {
			//Находим выбранную стадию проигранной сделки
			var stageIdChecked = $('input[name=stage_id]:checked').val() ? $('input[name=stage_id]:checked').val() : '';
			
			var tz_id = $('#tz_id') ? $('#tz_id').val() : ''; 
			 
			if (tz_id) { 
				//Отправляем ajax запрос для изменения стадии сделки
				$.ajax({
					method: 'POST',
					url: 'update_stage_id.php',
					data: {
						satge_id: stageIdChecked,
						tz_id: tz_id,
					},
					success: function(textContent) {
						//Если обновление прошло успешно, выводим сообщение
						if (textContent) {
							//$('.text-finish-deal').val(textContent).css('color', 'red');
							
							//запускает загрузку и отображение нового документа(перезагружаем)
							reload();
						}
					}
				}); 
			}
			
			$('.window-deal-lost').css('display', 'none');
			
		});
		//--'Не соответствие стадий в таблице заявок' окончание--//
		//--'Нулевая оплата' начало--//
		$('#oplata_bt').on('click', function(event) {
			//Если оплата равно 0 или меньше 0 не даем сформировать 
			if ($('#oplata_id').val() <= 0 || $('#oplata_id').val() == '') {
				event.preventDefault();
				alert('Данная оплата не может быть сформирована!Оплата не должна быть меньше или равна 0');
			}
		});
		//--'Нулевая оплата' окончание--//
		//--'Экспорт' начало--//
		//Закрываем модульное окно, прекращаем экспортирование
		$('.btn-export-close').on('click', function() {
			$('.wrepper-modal-export').css('display', 'none');
		});
		
		//Открываем модульное окно экспорта
		$('.export').on('click', function() {
			$('.wrepper-modal-export').css('display', 'flex');
		});
		
		
		/*$('.btn-export-copy').on('click', function(event) {
			event.preventDefault();
			var dealIdExport = $('#deal_id').val();
			var tzIdInpExport = $('#tz_id').val();
			var idRequestExport = $('.export-input option:selected').data('id');
			var idRequestExport2 = $('.export-input option').data('id');
			 console.log(idRequestExport);
			 console.log(idRequestExport2);
		});
		
		$('.export-input').on('change',function(){
		   var dataId = $(this).find(':selected').data('id');
		   //console.log(dataId);
		    //console.log(1111);//$('export-input option:selected')
		});*/
		
		//Экспортируем выбранную заявку
		$('.btn-export-copy').on('click', function(event) {
			var dealIdExport = $('#deal_id').val() ? $('#deal_id').val() : ''; //id сделки
			var tzIdInpExport = $('#tz_id').val() ? $('#tz_id').val() : ''; // id ТЗ
			var valExportInput = $('.export-input').val() ? $('.export-input').val() : ''; //Значение выбраной сделки для экспорта
			
			if ($('.export-input').val() == '') {
				alert("Выберите заявку которую хотите экспортировать!");
				return false;
			}
			//находим ID выбраной сделки для экспорта
			var idDealExport = $('#select-export option').filter(function() {
				return this.value == valExportInput;
			}).data('id');
			
			var idDealExport = idDealExport ? idDealExport : ''; //ID сделки для экспорта

			if (idDealExport) {
				window.open("./export.php?ID_DEAL=" + dealIdExport + "&ID_TZ=" + tzIdInpExport + "&ID_DEAL_EXPORT=" + idDealExport, target="_self");
			} else {
				alert("Выберите заявку которую хотите экспортировать!");
				return false;
			}
		});
		//--'Экспорт' окончание--//
    </script>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>