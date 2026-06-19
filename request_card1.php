<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04.04.2019
 * Time: 15:23
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DataStorage.php');


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

$db = new CDB();
$newResult = new NewResult();

$redirectMsi = $db ->select("SELECT `TYPE_ID` FROM `ba_tz` where  ID_Z=".$_GET['ID']);
foreach ($redirectMsi as $item)
{
	if ($item['TYPE_ID'] == 2) {
		header('Location: request_card_msi.php?ID='.$_GET['ID'].'&ACT_NUM='.$res_tz['ACT_NUM']);
	}
}
if (!empty($_GET['ID'])) {
	$select_act_base = $DB->Query("SELECT * FROM `ACT_BASE` WHERE `ID_Z`=" . (int)$_GET['ID'])->Fetch();
}
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


    $get_id = !empty($_GET['ID']) ? (int)$_GET['ID'] : '';

    $update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('2') . "', `STAGE_NUMBER`= " . (int)3 . " WHERE `ID_Z`=" . $get_id);

    $deal = CCrmDeal::GetByID((int)$_GET['ID']);

    $currentDate = date("Y-m-d");

    $checkNunP = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID'])->Fetch();

   
	//if ((!$checkNunP['NUM_P'] || $checkNunP['NUM_P'] == 'номер не присвоен') && !empty($get_id) && !in_array((int)$get_id, $newResult->arrIdDeal)) {
	if ((!$checkNunP['NUM_P'] || $checkNunP['NUM_P'] == 'номер не присвоен') && !empty($get_id) && (!in_array((int)$get_id, $newResult->arrIdDeal) || ((int)$get_id <= $newResult->idDealВefore && !in_array((int)$get_id, $newResult->arrIdDeal)))) {
        $yearProtocol = date("Y");

		
		$yDateP = '';

		 if ($yearProtocol != '2020') {
			$yDateP = substr($yearProtocol, 2);
		} else {
			$yDateP = $yearProtocol;
		}
		

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

        //$NumP = max($arrNumP) + 1;
		
		$numberOfPreviouslyAssignedNumbersForProtocols = 1034;
		$countPreviouslyCreatedProtocols = 8;

            $resultCountsProtocolsWithNumber = $db->selectRow("SELECT COUNT(*) AS `NUMBER_RECORDS` FROM `PROTOCOLS` WHERE YEAR(`DATE`) = YEAR(CURDATE()) AND `NUMBER` IS NOT NULL AND `NUMBER` > 0");

            $countsProtocolsWithNumber = !empty($resultCountsProtocolsWithNumber['NUMBER_RECORDS']) ? (int)$resultCountsProtocolsWithNumber['NUMBER_RECORDS'] : 0;

            $NumP = $yearProtocol === '2021' ?
                ($countsProtocolsWithNumber + 1 + $numberOfPreviouslyAssignedNumbersForProtocols - $countPreviouslyCreatedProtocols) :
                ($countsProtocolsWithNumber + 1);

        $num_tz = !empty($NumP) ? $DB->ForSql($NumP . "/" . $yDateP) : '';
		
        $resDateEnd = $DB->Query("UPDATE `ba_tz` SET `dateEnd`='" . date("Y-m-d") . "', `NUM_P`='" . $NumP . "', `NUM_P_TABLE`='" . $num_tz . "', `DATE_P_CORR`='" . date("Y-m-d") . "'
        WHERE `ID_Z`=" . $_GET['ID']);
		
		$insetrProtocolData = 'INSERT INTO `PROTOCOLS` (`ID_TZ`, `NUMBER`, `DATE`) VALUES ({?}, {?}, {?})';
        $arrayInsetrProtocolData = [$checkNunP['ID'], $NumP, $currentDate];

        $resultInsetrProtocolData = $db->queryExec($insetrProtocolData, $arrayInsetrProtocolData);

    } else {
        $resDateEnd = $DB->Query("UPDATE `ba_tz` SET `dateEnd`='" . date("Y-m-d") . "' WHERE `ID_Z`=" . $_GET['ID']);
    }
    
    $date_history_test_end = date('Y-m-d H:i:s');

    $assigned = CUser::GetByID($USER->GetID())->arResult[0];

    $assigned_name = !empty($assigned['NAME']) ? $assigned['NAME'] : '';

    $assigned_lastname = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';

    $assigned_user = $assigned_name.' '.$assigned_lastname;

    $res_history = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
    $ba_tz_history = $res_history->Fetch();

    $ID_P = $ba_tz_history['NUM_P'] ? $ba_tz_history['NUM_P'] : 'номер не присвоен';

    $num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';

    $tz = !empty($ba_tz_history['ID']) ? $ba_tz_history['ID'] : '';

    $res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date_history_test_end . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$tz . "','" . $USER->GetID() . "', 'Завершение испытания', '" . $num_request . "')");


    if ($ba_tz_history['ID'])
    {
        $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$tz. " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
        $history_ass=$history['ASSIGNED'];
        $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
        $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
        if($history_date<$history_date_null) $history_date='';
    }
    
    $assignet_history = $history_ass . " " . $history_date;

    $update_assignet_ba_tz = $DB->Query("UPDATE ba_tz SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . $get_id);

}


if (!empty($_GET['ID'])) {

    $id_get = !empty($_GET['ID']) ? (int)$_GET['ID'] : '';

    $result_select_ba_tz = null;
    if (!empty($id_get)) {
        $result_select_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $id_get)->Fetch();
    }

    
    $tzId = !empty($result_select_ba_tz['ID']) ? $result_select_ba_tz['ID'] : '';
    

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
    

    $deal = CCrmDeal::GetByID((int)$_GET['ID']);
    
    $pathToForsign = ''; 

    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
    $row = $res->Fetch();

   
    $rowId = !empty($row['ID']) ? (int)$row['ID'] : 0;
    $selectedProtocolData = null;
    $countOfSelectedProtolols = null;


    $flagAllGeneratedProtocols = 1;

    $allProbeRequest = !empty($row['PROBE']) ? unserialize($row['PROBE']) : [];

    $allTzRequest = !empty($row['TZ']) ? unserialize($row['TZ']) : [];


    $countAllNumbersProbe = 0;

    $countAllSelectedNumbersProbe = 0;

    foreach ($allProbeRequest as $keyProbeRequest => $valProbeRequest) {

        $currentArrayIdGosts = !empty($allTzRequest[$keyProbeRequest]['gost']) ? $allTzRequest[$keyProbeRequest]['gost'] : [];
        $countIdGosts = !empty($allTzRequest[$keyProbeRequest]['gost']) ? count($allTzRequest[$keyProbeRequest]['gost']) : 0;

        $flagContinue = 0;

        foreach ($currentArrayIdGosts as $keyIdGost => $valIdGost) {

            $resGostData = null;
            if (!empty($valIdGost)) {
                $resGostData = $db->selectRow('SELECT * FROM `ba_gost` WHERE ID={?}', [$valIdGost]);
            }

            if (!empty($resGostData['GOST_TYPE']) && $resGostData['GOST_TYPE'] === 'metodic_otbor' && $countIdGosts < 2) {
                $flagContinue = 1;
            }
        }


        if ($flagContinue) {
            continue;
        }

        $countNumbersProbe = !empty($valProbeRequest['number_probe']) ? count($valProbeRequest['number_probe']) : 0;
        $countAllNumbersProbe += $countNumbersProbe;
    }
   

    if ($row) {
        $res_kp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
        $res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
        $res_tz_doc = $DB->Query("SELECT * FROM `TZ_DOC` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
        $res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $row['ID'])->Fetch();

        $id_invoice = !empty($res_acc['ID']) ? $res_acc['ID'] : '';

        $result_select_tz_to_invoice =  $DB->Query("SELECT * FROM `TZ_TO_INVOICE` WHERE `id_tz`=" . (int)$row['ID'] . " ORDER BY `ID` DESC  LIMIT 1")->Fetch();

        $actual_vel_sum_invoice = null;

        if (!empty($result_select_tz_to_invoice['id_invoice'])) {
          
            $res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `ID`=" . $result_select_tz_to_invoice['id_invoice'])->Fetch();

            $id_sum_invoice = !empty($res_acc['ID']) ? $res_acc['ID'] : '';
        }
   
        if (!empty($id_invoice) && !empty($id_sum_invoice)) {
            $res_acc = $DB->Query("SELECT *,RIGHT(ACTUAL_VER, 19) AS ACTUAL FROM `INVOICE` WHERE `ID`=" . $id_invoice ." || `ID` = " . $id_sum_invoice . " ORDER BY ACTUAL DESC LIMIT 1")->Fetch();
        }
       
        $res_akt = $DB->Query("SELECT * FROM `ACT` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
        $res_akt_vr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $row['ID'])->Fetch();

      
        $result_select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . (int)$row['ID'] . " AND `date_return` IS NULL");
        
        if (!empty($row['ID']) && !empty($row['DATE_ACT']) && isset($row['TYPE_TZ']) && in_array($row['TYPE_TZ'], [33, 34, 35, 36, 37, 38, 39])) {
            
            $pathToForsign = './protocol_generator/archive/' . $row['ID'] . date('Y', strtotime($row['DATE_ACT'])) . '/' . 'forsign.docx';
        }

        //if (!empty($id_get) && in_array((int)$id_get, $newResult->arrIdDeal) || !empty($id_get) && $id_get === 5138 || !empty($id_get) && $id_get === 5479|| !empty($id_get) && $id_get === 5682 || !empty($id_get) && $id_get === 5697 || !empty($id_get) && $id_get === 5893) {
        if (!empty($id_get) && (in_array((int)$id_get, $newResult->arrIdDeal) || (int)$id_get > $newResult->idDealВefore)) {
            $selectedProtocolData = $db->select("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}", [$rowId]);

            $countOfSelectedProtolols = !empty($selectedProtocolData) ? count($selectedProtocolData) : 0;

            if (!empty($countOfSelectedProtolols)) {
                foreach ($selectedProtocolData as $keyProtocolData => $valProtocolData) {

                    $actualVersionCurrentProtocol = !empty($valProtocolData['ACTUAL_VERSION']) ? $valProtocolData['ACTUAL_VERSION'] : '';
                    $numberCurrentProtocol = !empty($valProtocolData['NUMBER']) ? $valProtocolData['NUMBER'] : 0;
                    $pdfCurrentProtocol = !empty($valProtocolData['PDF']) ? $valProtocolData['PDF'] : '';
                    $currentProtocolOutsideLis = !empty($valProtocolData['PROTOCOL_OUTSIDE_LIS']) ? $valProtocolData['PROTOCOL_OUTSIDE_LIS'] : null;
                    $probeCurrentProtocol = !empty($valProtocolData['PROBE']) ? unserialize($valProtocolData['PROBE']) : [];

                    foreach ($probeCurrentProtocol as $keyCurrentProtocol => $valCurrentProtocol) {

                        $countNumberProbeSelectedProtocol = !empty($valCurrentProtocol['number_probe']) ? count($valCurrentProtocol['number_probe']) : 0;
                        //$countAllSelectedNumbersProbe++;
						$countAllSelectedNumbersProbe += $countNumberProbeSelectedProtocol;
                    }

                    if (
                        (empty($actualVersionCurrentProtocol) && empty($numberCurrentProtocol) && empty($pdfCurrentProtocol)) ||
                        (empty($currentProtocolOutsideLis) && empty($actualVersionCurrentProtocol) && !empty($numberCurrentProtocol)) ||
                        (empty($currentProtocolOutsideLis) && !empty($actualVersionCurrentProtocol) && empty($numberCurrentProtocol)) ||
                        (!empty($currentProtocolOutsideLis) && empty($pdfCurrentProtocol))
                    ) {
                        $flagAllGeneratedProtocols = 0;
                    }
                }
            } else {
                $flagAllGeneratedProtocols = 0;
            }
        }

        if (!empty($row['ID']) && !empty($row['SAVE_MAIL'])){
            $unSerialMail = unserialize($row['SAVE_MAIL']);
        }

       
    }

    

    $idFistProtocol = !empty($selectedProtocolData[0]['ID']) ? $selectedProtocolData[0]['ID'] : 0;
    

    $sum_select_check_tz = !empty($result_select_check_tz->SelectedRowsCount()) ? $result_select_check_tz->SelectedRowsCount() : '';

    $typeIdRequest = !empty($row['TYPE_ID']) ? $row['TYPE_ID'] : '';
    
    $confirm = 1;
    if (!empty($sum_select_check_tz)) {
        while($select_check_tz = $result_select_check_tz->Fetch()) {
            
            if (empty($select_check_tz['confirm'])) {
                $confirm = 0;
                break;
            }
        }
    }
  
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

//--Стадия испытаний--//

if (!empty($row['STAGE_ID'])) {

	$select_check_tz = null;

	if (!empty($row['ID'])) {

		$select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . (int)$row['ID'] . " AND `date_return` IS NULL");
	}

	$selected_rows_count = !empty($select_check_tz->SelectedRowsCount()) ? $select_check_tz->SelectedRowsCount() : '';

	switch ($row['STAGE_ID']) {
		case 'NEW':
			$colorStaqe = '#00B0F0';
			$c = 0;
			$titleStage = 'Испытания еще не проводились. Пробы не получены.';
			break;
		case 'PREPARATION':
			$colorStaqe = '#00B0F0';
			$c = 0;
			$titleStage = 'Испытания еще не проводились. Пробы не получены.';
			break;
		case 'PREPAYMENT_INVOICE':
			$colorStaqe = '#00B0F0';
			$c = 0;
			$titleStage = 'Испытания еще не проводились. Пробы не получены.';
			break;
		case 'EXECUTING':
			$colorStaqe = '#00B0F0';
			$c = 0;
			$titleStage = 'Испытания еще не проводились. Пробы не получены.';
			break;
		case 'FINAL_INVOICE':
			$colorStaqe = '#FFFF00';
			$c = 1;
			$titleStage = 'Пробы получены. Проводятся испытания';
			break;
		case '1':
			$colorStaqe = '#FFFF00';
			$c = 2;
			$titleStage = 'Пробы получены. Проводятся испытания.';
			break;
		case '2':
			$colorStaqe = '#833ea3';
			$c = 3;
			$titleStage = 'Испытания в лаборатории завершены. Оплата получена или не требуется.';
			break;
		case '4':
			$colorStaqe = '#19ff19';
			$c = 4;
			$titleStage = 'Акты ВР отправлены заказчику.';
			break;
		case '3':
			$colorStaqe = 'green';
			$c = 5;
			$titleStage = 'Протокол выдан';
			break;
		case 'WON':
			$colorStaqe = '#70AD47';
			$c = 6;
			$titleStage = 'Акты ВР получены. Заявка успешно завершена.';
			break;
		case 'LOSE':
			$colorStaqe = 'red';
			$c = 7;
			$titleStage = 'Испытания не проведены. Заявка прекращена.';
			break;
		case '7':
			$colorStaqe = 'red';
			$c = 9;
			$titleStage = 'Заявка прекращена. Не проводим подобные испытания.';
			break;
		case '6':
			$colorStaqe = 'red';
			$c = 9;
			$titleStage = 'Заявка прекращена. Заказчик не вышел на связь.';
			break;
		case '5':
			$colorStaqe = 'red';
			$c = 9;
			$titleStage = 'Заявка прекращена. Заказчика не устроила цена.';
			break;
		case '8':
			$colorStaqe = 'red';
			$c = 9;
			$titleStage = 'Заявка прекращена. Создана другая заявка.';
			break;
		case '9':
			$colorStaqe = 'red';
			$c = 9;
			$titleStage = 'Заявка прекращена. Заказчик выбрал лабораторию в своем городе.';
			break;
		case '10':
			$colorStaqe = 'red';
			$c = 9;
			$titleStage = 'Заявка прекращена. Заказчик решил не проводить испытания.';
			break;
		case '11':
			$colorStaqe = 'red';
			$c = 9;
			$titleStage = 'Заявка прекращена. Отказались сами в связи с высокой загруженностью.';
			break;
		case '12':
			$colorStaqe = 'red';
			$c = 9;
			$titleStage = 'Заявка прекращена. Судебная экспертиза.';
			break;
		case '13':
			$colorStaqe = 'red';
			$c = 9;
			$titleStage = 'Заявка прекращена. Участие в тендере.';
			break;
	}
if ($row['ACT_NUM']&&($deal['STAGE_ID']=='NEW'||$deal['STAGE_ID']=='PREPARATION'||$deal['STAGE_ID']=='PREPAYMENT_INVOICE'||$deal['STAGE_ID']=='EXECUTING')) {
        $colorStaqe ='yellow';
        $titleStage='Протокол не выдан. Испытания еще не проводились. Пробы получены';
    }
    if ($row['PRICE']&&$row['RESULTS']&&(!$row['OPLATA']||$row['OPLATA']<$row['PRICE'])&&$deal['STAGE_ID']=='2') {
        $colorStaqe='#9b2d30';
        $titleStage='Испытания в лаборатории завершены. Оплата не поступила.';
    }

	$data[$i]['COLOR_CHECK_TZ'] = '';

	$data[$i]['COLOR_TZ_CHECKED'] = '';

	$confirm = 1;

	if (!empty($selected_rows_count)) {

		while($check_tz = $select_check_tz->Fetch()) {


			if (!empty($check_tz['leader']) && $check_tz['leader'] == $user_id && empty($check_tz['confirm'])) {

				$data[$i]['COLOR_CHECK_TZ'] = 'mango_class';
			}


			if (empty($check_tz['confirm'])) {
				$confirm = 0;
			}

			if(!empty($check_tz['confirm']) && empty($res_kp['ID']) && in_array($user_id, [35,62])) {
				$data[$i]['COLOR_TZ_CHECKED'] = 'green_class';
			}

		}

	}

	if (!empty($selected_rows_count) && empty($confirm)) {
		$color = '#060060';
		$title = 'Заявка на стадии проверки ТЗ';
	}
}
//--Стадия испытаний.Окончание--//
    

    $company = CCrmCompany::GetByID($deal['COMPANY_ID']); 
    $companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch(); 
    
    $req = new \Bitrix\Crm\EntityRequisite();
    $rs = $req->getList([
        "filter" => [
            "ENTITY_ID" => $deal['COMPANY_ID'],
        ]
    ]);


    $requis = $rs->Fetch();
    $email = $requis['RQ_EMAIL'];
    $email1 = $unSerialMail[0];
    $email2 = $unSerialMail[1];
    $email3 = $unSerialMail[2];
    
    $assigned2 = CUser::GetByID($USER->GetID())->arResult[0];
    
    $assigned_name2 = !empty($assigned2['NAME']) ? $assigned2['NAME'] : '';
    
    $assigned_lastname2 = !empty($assigned2['LAST_NAME']) ? $assigned2['LAST_NAME'] : '';
    
    $assigned_user2 = $assigned_name2.' '.$assigned_lastname2;
    $name_isp =  $assigned_user2;

    $date_protocol = $row['DATE_P_CORR'] ? $row['DATE_P_CORR'] : $row['DATE_P'];
    
    $y_date_act = date("Y", strtotime($row['DATE_ACT']));
    if ($y_date_act != '2020') $y_date_act = "/" . substr($y_date_act, 2);
    else $y_date_act = "/" . $y_date_act;

    $y_date_p = date("Y", strtotime($date_protocol));
    if ($y_date_p != '2020') $y_date_p = "/" . substr($y_date_p, 2);
    else $y_date_p = "/" . $y_date_p;

    $userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();

    $date_op = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
    $date_oplata = $date_op['UF_CRM_1579541506559']['VALUE'] ? date("d.m.Y", strtotime($date_op['UF_CRM_1579541506559']['VALUE'])) : "Не отправлено";

    $id_main_assigned = !empty($deal['ASSIGNED_BY_ID']) ? $deal['ASSIGNED_BY_ID'] : '';

    $arr_id_additional_assigned = !empty($date_op['UF_CRM_1571643970']['VALUE']) ? $date_op['UF_CRM_1571643970']['VALUE'] : '';

    $all_id_assigned = null;

    if (!empty($arr_id_additional_assigned) && !empty($id_main_assigned)) {
        $all_id_assigned = $arr_id_additional_assigned;
        array_push($all_id_assigned, (int)$id_main_assigned, 1);
    } else {
       
        $all_id_assigned =  [(int)$id_main_assigned, 1];
    }

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
        
    <?php } else { ?> <a href="/planerka">Вернуться в планерку</a><?php }
    if (in_array('8', $USER->GetUserGroupArray()) && !$finDeal && (in_array($USER->GetID() , $all_id_assigned) || ($USER->GetID() == 62) || ($USER->GetID() == 35) || ($USER->GetID() == 43) || ($USER->GetID() == 7) || ($USER->GetID() == 9) || ($USER->GetID() == 60)|| ($USER->GetID() == 56) || ($USER->GetID() == 61))) { ?> <!--|-->
    <a href="/request_new.php?EDIT=<?= $_GET['ID'] ?>">
            <div class='edit' title='Редактировать'></div><!--Редактировать заявку--></a><? } ?>
    
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
    
    <?php
    $arr_stage = ['NEW','PREPARATION','PREPAYMENT_INVOICE','EXECUTING','FINAL_INVOICE'];
    if (!empty($row['STAGE_ID']) && in_array($row['STAGE_ID'], $arr_stage)) {

        ?>
        <!--<a href="/export.php?ID=<?= $row['ID'] ?>&ID_DEAL=<?= $_GET['ID'] ?>" target="_blank">-->
        
        <div class='export' title='Импортировать'></div>
        
    <?php } ?>
    

    </div>
<table>
	<tr>
		<td>
			<div style="width: 40px; height: 40px; font-size: 35px;text-align: center;color: red;<?= !empty($colorStaqe) ? 'background:'. $colorStaqe : ''?>" title="<?= !empty($titleStage) ? $titleStage : '' ?>"><?= ($row['TAKEN_SERT_ISP'] == '1') ? 'C' : ''?></div>
		</td>
	<td>
    <h3><?php //(in_array('8', $USER->GetUserGroupArray())&&$deal['STAGE_ID']!='2'&&$deal['STAGE_ID']!='4'&&$deal['STAGE_ID']!='WON') ||
        if (($USER->GetID() == '1') || ($USER->GetID() == '7')|| ($USER->GetID() == '35')|| ($USER->GetID() == '62') || ($USER->GetID() == '9')) { ?>
        <a href='/crm/deal/details/<?= $deal['ID'] ?>/'><?
            } ?>Заявка <?= $deal['TITLE'] ?>
            от <?= $deal['DATE_CREATE'] ? date("d.m.Y", strtotime($deal['DATE_CREATE'])) : '' ?><?php if (($USER->GetID() == '1') || ($USER->GetID() == '7')|| ($USER->GetID() == '35')|| ($USER->GetID() == '62') || ($USER->GetID() == '9') ){ ?></a><?
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

	</td>
	</tr>
</table>
    <br/>
<?php if (in_array('8', $USER->GetUserGroupArray()) && $typeIdRequest != '1') { ?><p>Общая
	сумма: <b><?php if (isset($row['PRICE']) && !empty($row['PRICE'])) echo number_format($row['PRICE'], 2, ',', '') . " руб. "/*.((isset($row['DISCOUNT']) && !empty($row['DISCOUNT'])) ? '(С учетом скидки '.$row['DISCOUNT'].'%)' : '')*/
	; else echo "-";
	?></b></p><?
}
?>
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
                
                <input type="button" title="Отправить клиенту бланк заявки" class="add send"
                       onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TYPE=1&EMAIL=<?= $email ?>&EMAIL1=<?=$email1?>&EMAIL2=<?=$email2?>&EMAIL3=<?=$email3?>&NAME=<?= $name_isp ?>&TITLE=<?= $dealTitle ?>', '_blank')"
                       <?= $row['ID'] && $row['TZ'] || (strpos($email, '@') == false) ? 'disabled' : '' ?>>
                
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
                
                <input type="button" title="Сформировать" id='reload_kp' class="add toForm reload"
                       onclick="window.open ('/protocol_generator/kp.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>', '_blank')"
                       <?= !$row['TZ']|| $res_akt['ID'] || ($finDeal && $row['dateEnd']) || !empty($row['TAKEN_ID_DEAL']) || (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
                
                <input type="button" title="Отправить клиенту" class="add send"
                       onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=2&EMAIL=<?= $email ?>&EMAIL1=<?=$email1?>&EMAIL2=<?=$email2?>&EMAIL3=<?=$email3?>&NAME=<?= $name_isp ?>&ATTACH=<?= $res_kp['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>', '_blank')"
                       <?= !$res_kp['ID'] || $res_akt['ID'] || (strpos($email, '@') == false) ? 'disabled' : '' ?>>
                
            </td>
        </tr>

        <?php

        
        if (!empty($_GET['ID'])) {
            $resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$_GET['ID'])->Fetch();
        }
        
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
                    
                    $dogovorArr = explode('ID', $row['DOGOVOR_NUM']);
                    $dogovorId = !empty($dogovorArr[1]) ? $dogovorArr[1] : '';
                    $resDog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$dogovorId)->Fetch();
                    
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
                    
                    $dogovorArr = explode('ID', $row['DOGOVOR_NUM']);
                    $dogovorId = !empty($dogovorArr[1]) ? $dogovorArr[1] : '';
                    $resDog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$dogovorId)->Fetch();
                    
                    echo !empty($resDog['DATE']) ? date('d.m.Y', strtotime($resDog['DATE'])) : '';
                }
                
                ?>
            </td>
            <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                <td
                style="background-color: #<?= threeDayAfterSending($res_dog['SEND_DATE']) && !$res_acc['SEND_DATE'] ? 'FF0000' : 'FFF' ?>;"><?= $res_dog['SEND_DATE'] ? date('d.m.Y', strtotime($res_dog['SEND_DATE'])) : 'Не отправлялся из ЛИС' ?></td><? } ?>
            <td style="background-color: #FFF;">
                <input type="button" title="Сформировать" class="add toForm reload"
                       onclick="window.open ('/protocol_generator/dogovor.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>', '_blank')"
                       <?= !$row['ID'] || $res_akt['ID'] || ($finDeal && $row['dateEnd']) || !empty($row['DOGOVOR_NUM'])  || ( ($USER->GetID() != 62) && ($USER->GetID() != 56) && ($USER->GetID() != 9) && ($USER->GetID() != 35) && ($USER->GetID() != 43) && ($USER->GetID() != 1)) ? 'disabled' : '' ?>>
                
                <input type="button" title="Отправить клиенту" class="add send"
                       onclick="window.open ('/mail.php?ID=<?= $resDog['ID']/*$deal['ID']*/ ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=3&EMAIL=<?= $email ?>&EMAIL1=<?=$email1?>&EMAIL2=<?=$email2?>&EMAIL3=<?=$email3?>&NAME=<?= $name_isp ?>&ATTACH=<?= $res_dog['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>', '_blank')"
                       <?= (!$resDog['ID'] || (!empty($sum_select_check_tz) && empty($confirm)) || (strpos($email, '@') == false))   ? 'disabled' : '' ?>>
                
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
                
                <input type="button" title="Сформировать" class="add toForm reload"
                       onclick="window.open ('/protocol_generator/tz_doc.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>', '_blank')"
                       <?= !$row['TZ'] || ($finDeal && $row['dateEnd']) || (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
                
                <input type="button" title="Отправить клиенту" class="add send"
                       onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=6&EMAIL=<?= $email ?>&EMAIL1=<?=$email1?>&EMAIL2=<?=$email2?>&EMAIL3=<?=$email3?>&NAME=<?= $name_isp ?>&ATTACH=<?= $res_tz_doc['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>', '_blank')"
                       <?= !$res_kp['ID']  || !$res_tz_doc['ID'] || (strpos($email, '@') == false) ? 'disabled' : '' ?>>
                
                <input type="checkbox" name="check_tz" class="check-tz check-mail" <?= !$res_kp['ID'] || $res_akt['ID'] || !$res_tz_doc['ID'] ? 'disabled' : 'checked' ?>>
                
                <div class='wrapperOverall'>
                    
                    <div class='bracket'></div>
                    
                    <input type="button" class='btnOverall'>
                </div>
            </td>
        </tr>
        </tr>
    <tr style="background-color: #<?= $res_acc['ID'] ? 'd1ffca' : 'fff' ?>;">
        <td><b>Счет</b></td>
        <?php
        
        $invNumber = $row['ACCOUNT'];

        ?>
        
        <td><?= $res_acc['ID'] ? $invNumber : "Не сформирован" ?><br><?= (!empty($res_acc['ID']) && $res_acc['ID'] == $id_sum_invoice) ? '(Общий счёт)' : '' ?></td>
        
        <td><?= $res_acc['DATE'] ? date("d.m.Y", strtotime($res_acc['DATE'])) : '' ?></td>
        <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
            <td
            style="background-color: #<?= threeDayAfterSending($res_acc['SEND_DATE']) && !$row['DATE_ACT'] ? 'FF0000' : 'FFF' ?>;"><?= $res_acc['SEND_DATE'] ? date('d.m.Y', strtotime($res_acc['SEND_DATE'])) : 'Не отправлен' ?></td><? } ?>
        <td style="background-color: #FFF;">
           
            <input type="button" title="Сформировать" class="add toForm reload"
                   onclick="window.open ('/protocol_generator/account.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>', '_blank')"
                   <?= !$row['ID'] || !$row['DOGOVOR_NUM'] || $res_akt['ID'] || ($finDeal && $row['dateEnd'])  || !empty($row['TAKEN_ID_DEAL']) || (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
           
            <input type="button" title="Отправить клиенту" class="add send"
                   onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=4&EMAIL=<?= $email ?>&EMAIL1=<?=$email1?>&EMAIL2=<?=$email2?>&EMAIL3=<?=$email3?>&NAME=<?= $name_isp ?>&ATTACH=<?= $res_acc['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>', '_blank')"
                   <?= !$res_acc['ID'] || $res_akt['ID'] || (!empty($res_acc['ID']) && $res_acc['ID'] == $id_sum_invoice || (strpos($email, '@') == false) ) ? 'disabled' : '' ?>>

            
            <input type="checkbox" name="check_account" class="check-account check-mail" <?= !$res_acc['ID'] || $res_akt['ID'] ? 'disabled' : 'checked' ?>>
            
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
                
                <input type="number" step="0.01" class="oborud" id="oplata_id" name="oplata" value="<?= $row['OPLATA'] ?>">
                
                <input type="date"  class="oborud" name="oplata_date" value="<?= !$row['DATE_OPLATA']||$row['DATE_OPLATA']=='01.01.1970' ?  date("Y-m-d") : date("Y-m-d", strtotime($row['DATE_OPLATA'])) ?>">
                
                <input type="submit" title="Сформировать" class="toForm" value='' id="oplata_bt" <?= (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
                <input type="button" title="Заполнить данные по оплате" class="fill" id='fillPayment' <?= (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
                
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
			<td> <?php if ($row['ACT_NUM']) echo "<a href='/probe.php?ID=" . $deal['ID'] . "'>" . $numerAct . $y_date_act . " </a>"; else echo 'Не сформирован'; ?></td>
			<td><?= $row['ACT_NUM'] ? date("d.m.Y", strtotime($row['DATE_ACT'])) : '' ?></td>
			<?php if (in_array('8', $USER->GetUserGroupArray())) {
				?>
			<td class="" data-item="3" style="background-color: #FFF;">-</td><?
			} ?>
			<td style="background-color: #FFF;">
				<input type="button" style="margin-left: 0px" title="Заполнить данные акта" class="fill popup-with-form" href="#act-probe-modal-form" id='fillBtn' data-item="23"
					<?= ($row['ACT_NUM'] || !$row['ID'] || ($finDeal && $row['dateEnd'])) || (!empty($sum_select_check_tz) && empty($confirm)) ? 'disabled' : '' ?>>
				<input type="button" class="add download" title="Скачать общий протокол" style="margin: 0px 10px; vertical-align: middle" onclick="window.open ('/protocol_generator/probe_all.php?ID=<?= $_GET['ID'] ?>')">
			</td>
		</tr>
    <tr style="background-color: #<?= $row['RESULTS'] ? 'd1ffca' : 'fff' ?>;">
        
        <?php
		//if (!empty($id_get) && in_array((int)$id_get, $newResult->arrIdDeal) || !empty($id_get) && $id_get === 5138  || !empty($id_get) && $id_get === 5479 || !empty($id_get) && $id_get === 5682 || !empty($id_get) && $id_get === 5697 || !empty($id_get) && $id_get === 5893) {
		if (!empty($id_get) && (in_array((int)$id_get, $newResult->arrIdDeal) || (int)$id_get > $newResult->idDealВefore)) {
			/*$linkResultsIsp = !empty($idFistProtocol) ?
				'/results_isp_binding_multiple_protocols.php?ID=' . $deal['ID'] . '&ID_P=' . $idFistProtocol :
				'/results_isp_binding_multiple_protocols.php?ID=' . $deal['ID'];*/
			$linkResultsIsp = !empty($idFistProtocol) ?
				'/results_isp.php?ID=' . $deal['ID'] . '&ID_P=' . $idFistProtocol :
				'/results_isp.php?ID=' . $deal['ID'];
		} else {
			$linkResultsIsp = '/results_isp.php?ID=' . $deal['ID'];
		}

        ?>
        
        <td><b>Результаты испытаний</b></td>
        
        <!--<td>< ?php if ($row['RESULTS']) echo ( $deal['TYPE_ID']!= 4 ? "<a href='/results_isp.php?ID=" . $deal['ID'] . "'>" . $row['ID'] . " </a>"
                : "<a href='/results_isp_nk.php?ID=" . $deal['ID'] . "'>" . $row['ID'] . " </a>"); else echo 'Не внесены'; ?></td>-->

        <td><?php if ($row['RESULTS']) echo ( $deal['TYPE_ID']!= 4 ? "<a href='" . $linkResultsIsp . "'>" . $row['ID'] . " </a>"
                : "<a href='/results_isp_nk.php?ID=" . $deal['ID'] . "'>" . $row['ID'] . " </a>"); else echo 'Не внесены'; ?></td>
        
        <td><?php $date_results = '';
            if ($row['DATE_RESULTS']) $date_results = date("d.m.Y", strtotime($row['DATE_RESULTS']));
            elseif ($row['DATE_P']) $date_results = date("d.m.Y", strtotime($row['DATE_P']));
            
            echo $date_results;
            ?></td>
        <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
            <td style="background-color: #FFF;">-</td><?
        } ?>
        <td style="background-color: #FFF;">
            
            <? if($deal['TYPE_ID']!= 4) { ?>
                <!--<input type="button" title="Внести результаты" class="add contribute"
                       onclick="window.open ('/results_isp.php?ID=< ?= $deal['ID'] ?>', '_self')"
                       < ?= (!$row['ACT_NUM'] || $row['RESULTS'] || !$row['ID'] || ($finDeal && $row['dateEnd'])) ? 'disabled' : '' ?>>-->

                <input type="button" title="Внести результаты" class="add contribute"
                       onclick="window.open ('<?= $linkResultsIsp ?>', '_self')"
                       <?= (!empty($countOfSelectedProtolols) || !$row['ACT_NUM'] || $row['RESULTS'] || !$row['ID'] || ($finDeal && $row['dateEnd'])) ? 'disabled' : '' ?>>
            <? } else { ?>
                <input type="button" title="Внести результаты" class="add contribute"
                       onclick="window.open ('/results_isp_nk.php?ID=<?= $deal['ID'] ?>', '_self')"
                       <?= (!$row['ACT_NUM'] || $row['RESULTS'] || !$row['ID'] || ($finDeal && $row['dateEnd'])) ? 'disabled' : '' ?>>
            <? } ?>
        </td>
    </tr>
    <?php

    if ($typeIdRequest != '1') :
       

        //if ((!empty($id_get) && in_array((int)$id_get, $newResult->arrIdDeal) || !empty($id_get) && $id_get === 5138 || !empty($id_get) && $id_get === 5479|| !empty($id_get) && $id_get === 5682 || !empty($id_get) && $id_get === 5697 || !empty($id_get) && $id_get === 5893) && !empty($countOfSelectedProtolols)) {
        if ((!empty($id_get) && (in_array((int)$id_get, $newResult->arrIdDeal) || (int)$id_get > $newResult->idDealВefore)) && !empty($countOfSelectedProtolols)) {

            foreach ($selectedProtocolData as $keyProtocolData => $protocolData) {
                $protocolId = !empty($protocolData['ID']) ? (int)$protocolData['ID'] : 0;
                $currentProtocolNumber = !empty($protocolData['NUMBER']) ? $protocolData['NUMBER'] : null;

                $protocolOutsideLis = !empty($protocolData['PROTOCOL_OUTSIDE_LIS']) ? $protocolData['PROTOCOL_OUTSIDE_LIS'] : null;
                $actualVersionProtocol = !empty($protocolData['ACTUAL_VERSION']) ? unserialize($protocolData['ACTUAL_VERSION']) : '';


                $currentProtocolDate = !empty($protocolData['DATE']) ? dateObjectFormat($protocolData['DATE'], "Y-m-d") : null;
                $currentProtocolYear = !empty($protocolData['DATE']) ? dateObjectFormat($protocolData['DATE'], "Y") : null;
                $formattedProtocolDate = !empty($protocolData['DATE']) ? dateObjectFormat($protocolData['DATE'], "d.m.Y") : '';

                $currentProtocolUpdateAt = !empty($protocolData['UPDATED_AT']) ? dateObjectFormat($protocolData['UPDATED_AT'], "Y-m-d") : null;
                $currentProtocolType = !empty($protocolData['PROTOCOL_TYPE']) ? $protocolData['PROTOCOL_TYPE'] : null;

                $protocolDateDisplay = !empty($currentProtocolDate) ? $currentProtocolDate : $currentProtocolUpdateAt;

                $protocolSendDatetime = !empty($protocolData['PROTOCOL_SEND_DATETIME']) ?
                    dateObjectFormat($protocolData['PROTOCOL_SEND_DATETIME'], "Y-m-d")
                    : null;
                $formattedProtocolSendDatetime = !empty($protocolData['PROTOCOL_SEND_DATETIME']) ?
                    dateObjectFormat($protocolData['PROTOCOL_SEND_DATETIME'], "d.m.Y")
                    : 'Не отправлен';

                $formatYearOfProtocol = !empty($currentProtocolDate) ? formatYearOfProtocol($currentProtocolDate) : ''; 

                if (!empty($protocolId) && !empty($row['ID']) && !empty($currentProtocolYear) && isset($currentProtocolType) && in_array($currentProtocolType, [33, 34, 35, 36, 37, 38, 39])) {

                    $pathToForsign = './protocol_generator/archive/' . $row['ID'] . $currentProtocolYear . '/' . $protocolId . '/' . 'forsign.docx';
                }
                ?>
        <tr style="background-color: #<?= !empty($currentProtocolNumber) ? 'd1ffca' : 'fff' ?>;">
            <td><b>Протокол</b></td>
            <td>
                <?php echo !empty($currentProtocolNumber) ? $currentProtocolNumber . "/" . $formatYearOfProtocol : 'Не сформирован'; ?>
            </td>
            <td>
                
                <?= $formattedProtocolDate ?>
            </td>
            <?php if (in_array('8', $USER->GetUserGroupArray())) { ?>
                <td style="background-color: #<?= threeDayAfterSending($protocolSendDatetime) && empty($res_akt_vr['ID']) ? 'FF0000' : 'FFF' ?>;">
                    <?= $formattedProtocolSendDatetime ?>
                </td>
            <? } ?>
            <td style="background-color: #FFF;">
                <?php

                $dir = null;
                $protocolLocationDirectory = null;
                if (!empty($row['ID']) && !empty($currentProtocolYear) && !empty($protocolId)) {
                    $protocolLocationDirectory = __DIR__ . '/protocol_generator/archive/' . $row['ID'] . $currentProtocolYear . '/' . $protocolId;
                }


                $arrayFilesPdf = glob($protocolLocationDirectory . '/*.pdf', GLOB_BRACE);

                $filesPdfName = !empty($arrayFilesPdf[0]) ? array_pop(explode(DIRECTORY_SEPARATOR, $arrayFilesPdf[0])) : '';

                $arrayFilesSig = glob($protocolLocationDirectory . '/*.sig', GLOB_BRACE);
                $filesSigName = !empty($arrayFilesSig[0]) ? basename($arrayFilesSig[0]) : '';


                $parametersForFormingProtocol = (!empty($id_get) && !empty($tzId) && !empty($protocolId)) ? "/protocol_generator/protocol_multiple_protocols.php?ID=" . $id_get . "&TZ_ID=" . $tzId . "&PROTOCOL_ID=" . $protocolId  : '';

                ?>

                <input type="button" title="СФОРМИРОВАТЬ" class="add toForm reload"
                       onclick="window.open ('<?= $parametersForFormingProtocol ?>', '_blank')"
                       <?= !empty($protocolOutsideLis) || empty($currentProtocolNumber) || $deal['STAGE_ID'] == '4' || $deal['STAGE_ID'] == 'WON' ? 'disabled' : '' ?>>

                <?php /*if(!empty($currentProtocolNumber) && !empty($protocolDateDisplay))
                            {
                                echo "<a href = 'wordtest.php?NUMP=".$currentProtocolNumber."&DATE=".date("d.m.Y", strtotime($protocolDateDisplay))."&ID=".$tzId."'><input type='button' class='subscribe'></a>";
                            }*/

                if (!empty($protocolId) && !empty($currentProtocolNumber) && !empty($currentProtocolType) && in_array($currentProtocolType, [33, 34, 35, 36, 37, 38, 39]) && file_exists($pathToForsign) && empty($protocolOutsideLis)) {
                    //echo "<a href = '/2/1/cades_bes_file.php?ID=".$row['ID']."&DATE=".$formattedProtocolDate."&NUMP=".$currentProtocolNumber."&ID_P=".$protocolId."'><input type='button' class='subscribe'></a>";
                    echo "<a href = '/1/1/cades_bes_file.php?ID=".$row['ID']."&DATE=".$formattedProtocolDate."&NUMP=".$currentProtocolNumber."&ID_P=".$protocolId."'><input type='button' class='subscribe'></a>";
                } else {
                    echo "<input type='button' class='subscribe' disabled style='filter: grayscale(100%);'>";
                }

                if (in_array('8', $USER->GetUserGroupArray())) {
					
                    if (!empty($filesPdfName) && !empty($filesSigName)) {
                        ?>
                        <input type="button" title="Отправить клиенту"
                               class="add send"
                               onclick="window.open ('/mail.php?ID=<?= $currentProtocolNumber ?>&TZ_ID=<?= $tzId ?>&TYPE=5&EMAIL=<?= $email ?>&NAME=<?= $name_isp ?>&ATTACH=<?= $actualVersionProtocol ?>&TITLE=<?= $dealTitle ?>&SIG=<?= $filesSigName ?>&PDF=<?= $filesPdfName ?>&YEAR=<?= $currentProtocolYear ?>&ID_P=<?= $protocolId ?>', '_blank')"
                               <?= empty($currentProtocolNumber) || !$actualVersionProtocol || (!$row['OPLATA']&&!$row['TAKEN_ID_DEAL']&&$USER->GetID()!='9') || (strpos($email, '@') == false) ? 'disabled' : '' ?>>


                        <?
                    } else {
                        ?>
                        <input type="button" title="Отправить клиенту"
                               class="add send"
                               onclick="alert('Протокол не подписан и не может быть отправлен!')"
                               <?= 1 || empty($currentProtocolNumber) || !$actualVersionProtocol || !$row['OPLATA'] || (strpos($email, '@') == false) ? 'disabled' : '' ?>>
                        <?php
                    }
                }
                ?> 
            </td>
        </tr>
                <?php
            }
        } else {
            

            ?>

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
                
                <?php

                $yearProtocol = date("Y", strtotime($row['DATE_ACT']));


                $dir = null;
                if (!empty($row['ID'])) {
                    $dir = __DIR__ . '/protocol_generator/archive/' . $row['ID'] . $yearProtocol;
                }


                $filesPdf = glob($dir . '/*.pdf', GLOB_BRACE);

                $filesPdfName = !empty($filesPdf[0]) ? array_pop(explode(DIRECTORY_SEPARATOR, $filesPdf[0])) : '';

                $filesSig = glob($dir . '/*.sig', GLOB_BRACE);
                $filesSigName = !empty($filesSig[0]) ? basename($filesSig[0]) : '';

                ?>
                <input type="hidden" name="" value="">

                <input type="button" title="СФОРМИРОВАТЬ" class="add toForm reload"
                       onclick="window.open ('/protocol_generator/protocol.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>', '_blank')"
                       <?= !$row['NUM_P'] || $deal['STAGE_ID'] == '4' || $deal['STAGE_ID'] == 'WON' ? 'disabled' : '' ?>>

                <!--< ?php if($row['NUM_P']&&$row['NUM_P']!="Не сформирован") echo "<a href = '/1/1/cades_bes_file.php?ID=".$row['ID']."&DATE=".date("d.m.Y", strtotime($date_protocol))."&NUMP=".$row['NUM_P']."'><input type='button' class='subscribe'></a>"; ?>-->
                <?php

                //if (!empty($row['NUM_P'] && $row['NUM_P']!="Не сформирован" && $row['NUM_P']!="номер не присвоен" && $row['TYPE_TZ']) && in_array($row['TYPE_TZ'], [33, 34, 35, 36, 37])) {
                if (!empty($row['NUM_P'] && $row['NUM_P']!="Не сформирован"  && $row['NUM_P']!="номер не присвоен" && $row['TYPE_TZ']) && in_array($row['TYPE_TZ'], [33, 34, 35, 36, 37, 38, 39]) && file_exists($pathToForsign) && empty($row['NO_BITRIX'])) {
                    echo "<a href = '/1/1/cades_bes_file.php?ID=".$row['ID']."&DATE=".date("d.m.Y", strtotime($date_protocol))."&NUMP=".$row['NUM_P']."'><input type='button' class='subscribe'></a>";
                } else {
                    echo "<input type='button' class='subscribe' disabled style='filter: grayscale(100%);'>";
                }
                ?>

                <?php if (in_array('8', $USER->GetUserGroupArray())) {
					
                    if (!empty($filesPdfName) && !empty($filesSigName)) {
                        ?>
                        <input type="button" title="Отправить клиенту"
                               class="add send"
                               onclick="window.open ('/mail.php?ID=<?= $row['NUM_P'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=5&EMAIL=<?= $email ?>&EMAIL1=<?=$email1?>&EMAIL2=<?=$email2?>&EMAIL3=<?=$email3?>&NAME=<?= $name_isp ?>&ATTACH=<?= $row['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>&SIG=<?= $filesSigName ?>&PDF=<?= $filesPdfName ?>&YEAR=<?= $yearProtocol ?>', '_blank')"
                               <?=  !$row['NUM_P'] || !$row['ACTUAL_VER'] || (!$row['OPLATA']&&!$row['TAKEN_ID_DEAL']&&$USER->GetID()!='9') || (strpos($email, '@') == false) ? 'disabled' : '' ?>>


                        <?
                    } else {
                        ?>
                        <input type="button" title="Отправить клиенту"
                               class="add send"
                               onclick="alert('Протокол не подписан и не может быть отправлен!')"
                               <?= !$row['NUM_P'] || !$row['ACTUAL_VER'] || (!$row['OPLATA']&&!$row['TAKEN_ID_DEAL']&&$USER->GetID()!='9') || (strpos($email, '@') == false) ? 'disabled' : '' ?>>
                        <?php
                    }
                }
                ?>

            </td>
        </tr>
    <?php
            
        }
       
    endif;
    ?>
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
                <!--<input type="submit" title="Завершить" value='' class="add complete reload" name='btnTestEnd'
                       < ?= (!$row['PDF'] && !$row['ACTUAL_VER']) ? 'disabled' : '' ?>>-->
                <?php 
					//if (!empty($id_get) && in_array((int)$id_get, $newResult->arrIdDeal) || !empty($id_get) && $id_get === 5138  || !empty($id_get) && $id_get === 5479|| !empty($id_get) && $id_get === 5682 || !empty($id_get) && $id_get === 5697 || !empty($id_get) && $id_get === 5893) { 
					if (!empty($id_get) && (in_array((int)$id_get, $newResult->arrIdDeal) || (int)$id_get > $newResult->idDealВefore)) { 
				?>
                    <input type="submit" title="Завершить" value='' class="add complete reload" name='btnTestEnd'
                           <?= empty($flagAllGeneratedProtocols) || empty($countAllSelectedNumbersProbe) || (!empty($countAllSelectedNumbersProbe) && $countAllNumbersProbe !== $countAllSelectedNumbersProbe) ? 'disabled' : '' ?>>

                <?php } else { ?>
                    <input type="submit" title="Завершить" value='' class="add complete reload" name='btnTestEnd'
                           <?= (!$row['PDF'] && !$row['ACTUAL_VER']) ? 'disabled' : '' ?>>

                <?php } ?>
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
            <td style="background-color: #FFF;  position:relative;">

                <input type="number" step="1" class="oborud" id="akt_vr_num" value="<?= $res_akt_vr['NUMBER'] ?>">
                <input type="date" class="oborud" id="akt_vr_date" value="<?= $res_akt_vr['DATE'] ?>">

                <input id="deal_id" value="<?= $deal['ID'] ?>" hidden><input id="tz_id" value="<?= $row['ID'] ?>"
                                                                             hidden>
                <?php 
				//if (!empty($id_get) && in_array((int)$id_get, $newResult->arrIdDeal) || !empty($id_get) && $id_get === 5138  || !empty($id_get) && $id_get === 5479|| !empty($id_get) && $id_get === 5682 || !empty($id_get) && $id_get === 5697 || !empty($id_get) && $id_get === 5893) { 
				if (!empty($id_get) && (in_array((int)$id_get, $newResult->arrIdDeal) || (int)$id_get > $newResult->idDealВefore)) { 
				?>
                <input type="button" title="Сформировать" id="akt_vr_bt" class="add toForm"
                       <?= (empty($res_acc['ANSWER_1C']) || !$res_acc['ID'] || (!empty($res_acc['ID']) && $res_acc['ID'] == $id_sum_invoice) || empty($flagAllGeneratedProtocols) || empty($countAllSelectedNumbersProbe) || (!empty($countAllSelectedNumbersProbe) && $countAllNumbersProbe !== $countAllSelectedNumbersProbe))&&($USER->GetID() != '25') ? 'disabled' : '' ?>>
                <?php } else { ?>
                <input type="button" title="Сформировать" id="akt_vr_bt" class="add toForm"
                       <?= (empty($res_acc['ANSWER_1C']) || !$res_acc['ID'] || (!empty($res_acc['ID']) && $res_acc['ID'] == $id_sum_invoice) || !$row['NUM_P'] || ($row['NO_BITRIX'] && !$row['PDF']) || (!$row['NO_BITRIX'] && !$row['ACTUAL_VER']))&&($USER->GetID() != '25') ? 'disabled' : '' ?>>
                <?php } ?>

                <input type="button" title="Отправить клиенту" class="add send"
                       onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=8&EMAIL=<?= $email ?>&EMAIL1=<?=$email1?>&EMAIL2=<?=$email2?>&EMAIL3=<?=$email3?>&NAME=<?= $name_isp ?>&ATTACH=<?= $res_akt_vr['ACTUAL_VER'] ?>', '_blank')"
                       <?= !$res_akt_vr['ID'] || (strpos($email, '@') == false) ? 'disabled' : '' ?>>

                    <div class="wrepperSelLead">
                        <p>Выберите руководителя:</p>
                        <input class="select_lead_for_akt_vr" type="radio" name="lead_akt_vr" value='1'> Ахтямов Э.Р.
                        <br>
                        <input class="select_lead_for_akt_vr" type="radio" name="lead_akt_vr" value='2'> Габдрахманова А.А.
                        <br><br>
                        <input type="button" title="Сформировать" id="akt_vr_bt_lead" value="Сформировать">
                    </div>
                    

            </td>
        </tr>
        <?
    } ?>


    
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
    
    <br /><br />
    <!--Работы по заявке завершены <input type="checkbox" name="FIN" <//?= ($deal['STAGE_ID'] != '2' && $deal['STAGE_ID'] != '3' && $deal['STAGE_ID'] != '4' && $deal['STAGE_ID'] != 'WON') ? '' : 'checked' ?> <//?= ($USER->GetID() == '1') || ($USER->GetID() == '10') || ($USER->GetID() == '11') ? '' : 'disabled' ?> onclick="window.open ('/request_card.php?ID=<//?= $deal['ID'] ?>&STAGE=<//?= ($deal['STAGE_ID'] != '2' && $deal['STAGE_ID'] != '3' && $deal['STAGE_ID'] != '4' && $deal['STAGE_ID'] != 'WON') ? '0' : '1' ?>')">-->
<?php
$get_act_num = !empty($_GET['ACT_NUM']) ? $_GET['ACT_NUM'] : '';
$get_name_act_num = !empty($_GET['ACT_NUM']) ? "&ACT_NUM=" : '';
?>
    Работы в лаборатории завершены <input type="checkbox" name="FIN" <?= ($deal['STAGE_ID'] != '2' && $deal['STAGE_ID'] != '3' && $deal['STAGE_ID'] != '4' && $deal['STAGE_ID'] != 'WON') ? '' : 'checked' ?> <?= ($USER->GetID() == '1') || ($USER->GetID() == '10') || ($USER->GetID() == '35') || ($USER->GetID() == '62') || ($USER->GetID() == '9') || ($USER->GetID() == '11') || ($USER->GetID() == '58') || ($USER->GetID() == '34') || ($USER->GetID() == '43') || ($USER->GetID() == '60') ? '' : 'disabled' ?> onclick="window.location.assign ('/request_card.php?ID=<?= $deal['ID'] ?><?= $get_name_act_num ?><?= $get_act_num ?>&STAGE=<?= ($deal['STAGE_ID'] != '2' && $deal['STAGE_ID'] != '3' && $deal['STAGE_ID'] != '4' && $deal['STAGE_ID'] != 'WON') ? '0' : '1' ?>')">
    <br/><br/>
    
    <button class="finish-deal" <?= ($USER->GetID() == '1') || ($USER->GetID() == '43') || ($USER->GetID() == '10') || ($USER->GetID() == '35') || ($USER->GetID() == '62') || ($USER->GetID() == '9') || ($USER->GetID() == '11') || ($USER->GetID() == '13')||($USER->GetID() == '32')|| ($USER->GetID() == '58') || ($USER->GetID() == '25') || ($USER->GetID() == '34') || ($USER->GetID() == '56') ? '' : 'disabled' ?>>Завершить заявку</button>
    <input type="text" class="text-finish-deal" style="<?= !empty($color) ? 'color:'.$color : ''?>" value="<?= !empty($title) ? $title : '' ?>">
    <br></br>

    <!-- <button class="send_end-point" data-send="BuildDocs">Отправить в BuildDocs</button>
	<button class="send_end-point" data-send="SKID">Отправить в СКИД</button> -->

    <script src="//code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script>
	
	    $('.send_end-point').on('click', function(event){
	        
            var docId = '<?php echo $row['ID']; ?>';
            var dateAct = '<?php echo $row['DATE_ACT']; ?>';
            var id_G = '<?php echo $_GET['ID']; ?>';
            var nump = '<?php echo $row['NUM_P']; ?>';


            var sendPartner = event.target.getAttribute('data-send');

            if(sendPartner == 'SKID'){
                //alert('Отправить в СКИД');
                sendPartner = "SKID";
            }
            else{
                //alert('Отправить в BuildDocs');
                sendPartner = "BuildDocs";
            }
	        var responseText = $.ajax({
    			type: 'post',
    			url: '/extranet/ajax/send_endpoint.php',
    			dataType: 'json',
    			data: {
    			    "docId": docId,
    			    "sendPartner": sendPartner,
                    "id_G": id_G,
                    "nump": nump
    			}
    		}).responseText;
	    });
	</script>
   
<?php

if ($typeIdRequest != '1') :
     ?>
    Протокол в ОА <input type="checkbox" name="OA" <?= !$row['ATTESTAT'] ? '' : 'checked' ?> disabled>
    <br/><br/>
    
    <?php //if($row['NUM_P']&&$row['NUM_P']!="Не сформирован") echo "<a href = 'wordtest.php?NUMP=".$row['NUM_P']."&DATE=".date("d.m.Y", strtotime($date_protocol))."&ID=".$row['ID']."'>Подписать</a>"; ?>
    <?php //if($row['NUM_P']&&$row['NUM_P']!="Не сформирован") echo "<a href = 'wordtest.php?NUMP=".$row['NUM_P']."&DATE=".date("d.m.Y", strtotime($date_protocol))."&ID=".$row['ID']."'><button type='button' class='subscribe'></button></a>"; ?>
    
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
				
				if ((!empty($id_get) && (in_array((int)$id_get, $newResult->arrIdDeal) || (int)$id_get > $newResult->idDealВefore)) && !empty($countOfSelectedProtolols)) {
                //if ($countOfSelectedProtolols) {
                    foreach ($selectedProtocolData as $keyProtocolData => $valProtocolData) {
                        $currentProtocolId = !empty($valProtocolData['ID']) ? $valProtocolData['ID'] : null;
                        $currentProtocolNum = !empty($valProtocolData['NUMBER']) ? $valProtocolData['NUMBER'] : null;
                        $currentProtocolOutsideLis = !empty($valProtocolData['PROTOCOL_OUTSIDE_LIS']) ? $valProtocolData['PROTOCOL_OUTSIDE_LIS'] : 0;

                        if ($currentProtocolNum && !$currentProtocolOutsideLis) {
                            echo "<span><strong>Данные протокола № " . $currentProtocolNum . ":</strong></span><br><br>";
                        
                            $dir = 'protocol_generator/archive/' . $row['ID'] . date("Y", strtotime($row['DATE_ACT'])) . '/' . $currentProtocolId . '/';
                            $skip = array('.', '..','signed.docx', 'forsign.docx','qrNEW.png');
                            $files = scandir($dir);
							$log = date("Y.m.d G:i:s") . "\n";
							$log .= print_r($dir, 1). "\n";
							file_put_contents(getcwd() . '/doc123.txt', $log);

                            foreach ($files as $file) {
                                if (!in_array($file, $skip))
                                    echo "<a href='$dir$file'>$file</a><br><br>";
                            }
                        } else if ($currentProtocolNum && $currentProtocolOutsideLis) {
                            echo "<span><strong>Данные протокола № " . $currentProtocolNum . ":</strong></span><br><br>";
                            $dir = 'pdf/' . $currentProtocolId . '/';
                            $files = scandir($dir);
                            $skip = array('.', '..');
                            foreach ($files as $file) {
                                if (!in_array($file, $skip))
                                    echo "<a href='$dir$file'>$file</a><br><br>";
                            }
                        }
                    }
                } else {
					if ($row['NUM_P']) {
						$dir = 'protocol_generator/archive/' . $row['ID'] . date("Y", strtotime($row['DATE_ACT'])) . '/';
						$skip = array('.', '..','signed.docx', 'forsign.docx','qrNEW.png');
						$files = scandir($dir);
						$log = date("Y.m.d G:i:s") . "\n";
						$log .= print_r($dir, 1). "\n";
						file_put_contents(getcwd() . '/doc1222.txt', $log);
						foreach ($files as $file) {
							if (!in_array($file, $skip))
								echo "<a href='$dir$file'>$file</a><br><br>";
						}
					}
				}

				?>
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
                    
                    $dir = 'protocol_generator/archive_dog/' . $resDogovorNumber . '/';
                    $skip = array('.', '..');
                    $files = scandir($dir);


                    foreach ($files as $file) {
                        // Append 13.09.2021
                        $file = str_replace("Contract", "Договор", $file);
                        //..................
                        if (!in_array($file, $skip))
                            echo "<a href='$dir$file'>$file</a><br><br>";
                    }
                } $log = date("Y.m.d G:i:s") . "\n";
				$log .= print_r($getId, 1). "\n";
				file_put_contents(getcwd() . '/LOGdogovor.txt', $log);?>
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
    
<?php endif; ?>
    
    <div class="window-finish-deal">
        <p>Выберите результат, с которым будет закрыта сделка.</p>
        <div class="wrapper-btn-stage">
            <button class="akt-sended btn-stage">Акты отправлены</button>
            <button class="akt-received btn-stage">Акты получены, сделка завершена</button>
            <button class="deal-lost btn-stage">Сделка проиграна</button>
            <button class="cancel btn-stage">Отменить</button>
        </div>
    </div>


    
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
    
    <div class="wrepper-modal-export">
    <div class="modal-export">
        <p>Введите номер экспортируемой заявки</p>
        <datalist id="select-export">
            <?php
            if (CModule::IncludeModule('crm')) {
                
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
		<?php
		$quarryRes = $db->select("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = {$_GET['ID']}");
		$dopusc_mater_id = [1, 2, 6, 7, 8, 11, 94, 95, 101];
		foreach ($quarryRes as $elem) {
			if (in_array($elem['ID_MATERIAL'], $dopusc_mater_id)) {
				$required_select_quarry = 'required';
				break;
			} else {
				$classQuarry = 'visually-hidden';
			}
		}
		?>
		<form id="act-probe-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="add_act_num.php?id_z=<?=$_GET['ID']?>" method="post">
			<div class="title mb-3 h-2">
				Данные акта приемки проб
			</div>

			<div class="line-dashed-small"></div>

			<!--	<div class="mb-3">-->
			<!--		<label class="form-label">Номер акта ПП</label>-->
			<!--		<input type="text" class="form-control" name="actNumber" required>-->
			<!--	</div>-->

			<div class="mb-3">
				<label class="form-label">Дата акта ПП</label>
				<input type="date" class="form-control" name="actDate" value="<?=date('Y-m-d')?>" readonly>
			</div>

			<div class="mb-3">
				<label class="form-label">Место отбора проб</label>
				<input type="text" class="form-control" name="samplePlace">
			</div>

			<div class="mb-3">
				<label class="form-label">Дата отбора проб</label>
				<input type="date" class="form-control" name="sampleDate">
			</div>
			<div class="mb-3">
				<label class="form-label">Описание объекта</label>
				<input type="text" class="form-control" name="DESCRIPTION" value="<?=$row['DESCRIPTION']?>">
			</div>
			<div class="mb-3 <?=$classQuarry?>">
				<label class="form-label">Привязка к карьеру</label>
				<select type="text" class="form-control" name="quarry" <?=$required_select_quarry?>>
					<?
					$quarDB = new CDB();
					$quarList = $quarDB->select('SELECT * FROM `Quarry`');
					if (empty($row['QUARRY_ID'])) {
						echo("<option value=''>Выберите карьер</option>");
					}
					foreach($quarList as $key => $value) {
						echo("<option value='{$value['ID']}'>{$value['NAME']}</option>");
					}
					?>
				</select>
			</div>
			<div class="mb-3 form-check">
				<input type="checkbox" class="form-check-input" id="exampleCheck1">
				<label class="form-check-label" for="exampleCheck1">Пробы отобраны не заказчиком</label>
			</div>
			<div class="visually-hidden act-manual-block">
				<div class="mb-3">
					<label class="form-label">Отбор проб произвел</label>
					<input type="text" class="form-control" name="sampleMaker">
				</div>
			</div>
			<div class="line-dashed-small"></div>

			<button type="submit" class="btn btn-primary submit-act-probe">Отправить</button>
			<!--			<button title="Close (Esc)" type="button" class="mfp-close">×</button>-->
		</form>

    <link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
    <link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
    
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>
	<link href="ulab/assets/plugins/magnific-popup/magnific-popup.css" rel="stylesheet">

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
	<script src="ulab/assets/plugins/magnific-popup/jquery.magnific-popup.min.js"></script>


    <script>
        $(document).ready(function () {
			$('.popup-with-form').magnificPopup({
				type: 'inline',
				closeBtnInside:true,
				fixedContentPos: false
			})

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

		//Действия с чекбоксом формы
		$('#exampleCheck1').on('click', function(e) {
			let checkbox = $('#exampleCheck1');
			$('.act-manual-block').toggleClass('visually-hidden');
		});

        
        function reloadWindow () {
            setTimeout(function() {
                

                var getActNum = '<?= $_GET["ACT_NUM"] ?>' ? '<?= $_GET["ACT_NUM"] ?>' : '';
                var getId = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';
                
                //window.location.assign("/request_card_binding_multiple_protocols.php?ID=" + getId + "&ACT_NUM=" + getActNum);
                window.location.assign("/request_card.php?ID=" + getId + "&ACT_NUM=" + getActNum);
                
            }, 1500);
        }
        
        function reload() {
            var getActNum = '<?= $_GET["ACT_NUM"] ?>' ? '<?= $_GET["ACT_NUM"] ?>' : '';
            var getId = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';
            
            window.location.assign("/request_card.php?ID=" + getId + "&ACT_NUM=" + getActNum);
        }
        

        $('.aktVR').on('click', '#akt_vr_bt', function (event) {

            var lead = $('input[name=lead_akt_vr]:checked').val(); //получаем значение выбранного руководителя
            var aktNumInp = $('#akt_vr_num');
            var aktDateInp = $('#akt_vr_date');
            var dealIdInp = $('#deal_id');
            var tzIdInp = $('#tz_id');
            var aktNum = aktNumInp[0].value;
            var aktDate = aktDateInp[0].value;
            var dealId = dealIdInp[0].value;
            var tzId = tzIdInp[0].value;

           
			if (aktDate == '' && aktNum == '') {
				//alert('Для формирования акта выполненых работ введите номер!');
			}

           
            $('.wrepperSelLead').css('display', 'block');
            
        });
        $('.aktVR').on('click', '#akt_vr_bt_lead', function(event) {
            var lead = $('input[name=lead_akt_vr]:checked').val(); //получаем значение выбранного руководителя
            var aktNumInp = $('#akt_vr_num');
            var aktDateInp = $('#akt_vr_date');
            var dealIdInp = $('#deal_id');
            var tzIdInp = $('#tz_id');
            var aktNum = aktNumInp[0].value;
            var aktDate = aktDateInp[0].value;
            var dealId = dealIdInp[0].value;
            var tzId = tzIdInp[0].value;

           
			if (aktDate == '' && aktNum == '') {
				//alert('Для формирования акта выполненых работ введите номер!');
			}

            if(!lead && $('.wrepperSelLead').css('display') == 'block'){
                alert('Пожалуйста, сначала выберите руководителя');
            }else{
                
                if($('.wrepperSelLead').css('display') == 'block'){
                    
                    if (aktNum === '' && aktDate === '' || aktNum !== '' && aktDate !== '') {
                       
                        //window.open('/protocol_generator/akt_vr_binding_multiple_protocols.php?ID=' + dealId + '&TZ_ID=' + tzId + '&NUM=' + aktNum + '&DATE=' + aktDate + '&LEAD=' + lead, '_blank');
                        window.open('/protocol_generator/akt_vr.php?ID=' + dealId + '&TZ_ID=' + tzId + '&NUM=' + aktNum + '&DATE=' + aktDate + '&LEAD=' + lead, '_blank');
                        reloadWindow();
                    }
                }
            }
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

        if (!$('.check-dogovor').prop('checked') && !$('.check-tz').prop('checked') && !$('.check-account').prop('checked')) {
            $('.btnOverall').prop('disabled', true);
        }

        
        if ($('.check-dogovor').prop('checked') || $('.check-tz').prop('checked') || $('.check-account').prop('checked')) {
            $('.btnOverall').prop('disabled', false);
        }

        $('#r_card').on('click','.check-mail', function (event) {
            
            if (!$('.check-dogovor').prop('checked') && !$('.check-tz').prop('checked') && !$('.check-account').prop('checked')) {
                $('.btnOverall').prop('disabled', true);
            }

            
            if ($('.check-dogovor').prop('checked') || $('.check-tz').prop('checked') || $('.check-account').prop('checked')) {
                $('.btnOverall').prop('disabled', false);
            }
        });

        $('#r_card').on('click','.btnOverall', function (event) {
            var strMail = '';
            $('.check-mail:checked').each(function (indCheckMail, valCheckMail) {
                
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
            window.open ("/mail.php?ID=<?= $resDog['ID'] ?>&ID2=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>&TYPE=7&EMAIL=<?= $email ?>&EMAIL1=<?=$email1?>&EMAIL2=<?=$email2?>&EMAIL3=<?=$email3?>&NAME=<?= $name_isp ?>&ATTACH1=<?= $resDog['ACTUAL_VER'] ?>&ATTACH2=<?= $res_acc['ACTUAL_VER'] ?>&ATTACH3=<?= $res_tz_doc['ACTUAL_VER'] ?>&TITLE=<?= $dealTitle ?>" + strMail, "_blank");
        });
        

        
        $('.reload').click(function() {
            
            reloadWindow();
        });
       
        $('.finish-deal').on('click', function() {
            $('.window-finish-deal').css('display', 'block');
        });

        
        $('.akt-received').on('click', function() {
            
            $('.window-finish-deal').css('display', 'none');

            var tz_id = $('#tz_id') ? $('#tz_id').val() : '';

            if (tz_id) {
                
                $.ajax({
                    method: 'POST',
                    url: 'update_stage_id.php',
                    data: {
                        satge_id: 'WON',
                        tz_id: tz_id,
                    },
                    success: function(textContent) {
                        
                        if (textContent) {
                          
                            reload();
                        }
                    }
                });
            }
        });

        
        $('.akt-sended').on('click', function() {
           
            $('.window-finish-deal').css('display', 'none');

            var tz_id = $('#tz_id') ? $('#tz_id').val() : '';

            if (tz_id) {
                
                $.ajax({
                    method: 'POST',
                    url: 'update_stage_id.php',
                    data: {
                        satge_id: '4',
                        tz_id: tz_id,
                    },
                    success: function(textContent) {
                        
                        if (textContent) {
                           
                            reload();
                        }
                    }
                });
            }
        });

        
        $('.deal-lost').on('click', function() {
            
            $('.window-finish-deal').css('display', 'none');

           
            $('.window-deal-lost').css('display', 'block');
        });

        
        $('.cancel').on('click', function() {

            if ($('.window-finish-deal').css('display') == 'block') {
                
                $('.window-finish-deal').css('display', 'none');
            }

            if ($('.window-deal-lost').css('display') == 'block') {
                
                $('.window-deal-lost').css('display', 'none');
            }

        });

        
        $('.save-stage').on('click', function() {
            
            var stageIdChecked = $('input[name=stage_id]:checked').val() ? $('input[name=stage_id]:checked').val() : '';

            var tz_id = $('#tz_id') ? $('#tz_id').val() : '';

            if (tz_id) {
                
                $.ajax({
                    method: 'POST',
                    url: 'update_stage_id.php',
                    data: {
                        satge_id: stageIdChecked,
                        tz_id: tz_id,
                    },
                    success: function(textContent) {
                        
                        if (textContent) {
                           
                            reload();
                        }
                    }
                });
            }

            $('.window-deal-lost').css('display', 'none');

        });

        $('#oplata_bt').on('click', function(event) {
            
            if ($('#oplata_id').val() <= 0 || $('#oplata_id').val() == '') {
                event.preventDefault();
                alert('Данная оплата не может быть сформирована!Оплата не должна быть меньше или равна 0');
            }
        });
        
        $('.btn-export-close').on('click', function() {
            $('.wrepper-modal-export').css('display', 'none');
        });

        
        $('.export').on('click', function() {
            $('.wrepper-modal-export').css('display', 'flex');
        });


        $('.btn-export-copy').on('click', function(event) {
            var dealIdExport = $('#deal_id').val() ? $('#deal_id').val() : ''; //id сделки
            var tzIdInpExport = $('#tz_id').val() ? $('#tz_id').val() : ''; // id ТЗ
            var valExportInput = $('.export-input').val() ? $('.export-input').val() : ''; //Значение выбраной сделки для экспорта

            if ($('.export-input').val() == '') {
                alert("Выберите заявку которую хотите экспортировать!");
                return false;
            }
            
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
        
    </script>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
