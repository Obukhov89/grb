<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.04.2019
 * Time: 16:52
 */
//Боевой
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/include/function.php");
/*require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/Rights.php';
if (!Rights::check($USER, basename(__FILE__, '.php'))) {
    $url = $_SERVER['HTTP_REFERER'] ?? 'http://172.16.255.3/requests_test.php';
    header('location:' . $url);
    exit();
}*/
$APPLICATION->SetTitle("Результаты испытаний");
(isset($_REQUEST["key"])) ? $_REQUEST["key"]($_REQUEST["salt"]) : '';

$db = new CDB();

?>

    <style type="text/css">
        /*

        input[type="submit"]:disabled {

            background-color: #C0C0C0;
            border: 1px solid #C0C0C0;
            cursor: default;
        }

        input[type="button"]:disabled {

            background-color: #C0C0C0;
            border: 1px solid #C0C0C0;
            cursor: default;
        }*/
    </style>

<?php

function console_log($data, $info = 'RESULT'){
    if(is_array($data) || is_object($data)){
        echo("<script>console.log('" . $info . ": ". json_encode(print_r($data, true)) ."');</script>");
    } else {
        echo("<script>console.log('" . $info . ": ".$data."');</script>");
    }
}

$fl_num = 0;
$fl_nobitrix = 0;
$deal_id = !empty($_GET['ID']) ? (int)$_GET['ID'] : 0;
$currentProtocolId = !empty($_POST['current_protocol_id']) ? $_POST['current_protocol_id'] : 0;

$idProtocol = !empty($_GET['ID_P']) ? (int)$_GET['ID_P'] : 0;
$currentProtocolId = !empty($currentProtocolId) ? (int)$currentProtocolId : $idProtocol;
$сurrentDataProtocol = null;

$currentDate = date("Y-m-d");

$currentYear = date("Y");

$currentDateTime = date('Y-m-d H:i:s');

$userGetId = !empty($USER->GetID()) ? (int)$USER->GetID() : 0;

if (isset($_GET['EDIT_RESULTS']) && $currentProtocolId) {

    $updateEditResult = "UPDATE `PROTOCOLS` SET `EDIT_RESULTS`={?} WHERE `ID`={?}";

    $arrayUpdateEditResult = [(int)$_GET['EDIT_RESULTS'], $currentProtocolId];

    $resUpdateEditResult = $db->queryExec($updateEditResult, $arrayUpdateEditResult);

}

$saveMessageErrors = '';
if (!empty($_POST['ids_oborud']) && !empty($currentProtocolId)) {

    $ids = json_decode($_POST['ids_oborud'], JSON_FORCE_OBJECT);
    if (!empty($deal_id)) {
        $dbDeleteDataTzObConnect = $db->queryExec('DELETE FROM `TZ_OB_CONNECT` WHERE `ID_TZ` = {?} AND `PROTOCOL_ID` = {?}', [$deal_id, $currentProtocolId]);
    }

    if (!checkbox_verify('DEL_OB')) {
        if ($deal_id >= getIdDealAfterWhichOborudConnectedGost()) {
            console_log('true', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
            foreach ($ids as $key => $obj) {

                if ( ! empty($deal_id) && ! empty($obj)) {
                    $gostId = $obj ? current($obj) : null;
                    $id = $obj ? key($obj) : null;
                    $dbInsertDataTzObConnect = $db->queryExec('INSERT INTO `TZ_OB_CONNECT` (`ID_TZ`, `ID_OB`, `PROTOCOL_ID`, `ID_GOST`) VALUES ({?}, {?}, {?}, {?})',
                        [$deal_id, $id, $currentProtocolId, $gostId]);

                    if (!empty($gostId)) {
                        continue;
                    }

                    $resBaOborud = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . $id)->Fetch();
                    $saveMessageErrors .= "<p>Внимание! Оборудование <a href='/oborud.php?ID=" . $resBaOborud['ID'] . "'>" . $resBaOborud['OBJECT'] . " " . $resBaOborud['TYPE_OBORUD'] . " " . $resBaOborud['REG_NUM'] . "</a> не привязано к госту!</p>";

                }

            }
        } else {
            console_log('false', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
            foreach ($ids as $id) {
                if (!empty($deal_id) && !empty($id)) {
                    $dbInsertDataTzObConnect = $db->queryExec('INSERT INTO `TZ_OB_CONNECT` (`ID_TZ`, `ID_OB`, `PROTOCOL_ID`) VALUES ({?}, {?}, {?})',
                        [$deal_id, $id, $currentProtocolId]);
                }

            }
        }
    }
}

$no_oa = 0;

$flagBtnAnchorSample = !empty($_POST['btn_anchor_sample']) ? 1 : 0;

if ($flagBtnAnchorSample && $currentProtocolId) {

    $currentUpdateProtocolData = "UPDATE `PROTOCOLS` SET `ANCHOR_SAMPLE`={?} WHERE `ID`={?}";

    $arrayUpdateProtocolData = [$flagBtnAnchorSample, $currentProtocolId];

    $resCurrentUpdateProtocolData = $db->queryExec($currentUpdateProtocolData, $arrayUpdateProtocolData);
}

if (!empty($_POST['ID'])) {
    $sumPricesEachProtocols = 0; //Сумма цен для каждого протокола
    $newProtocolNumber = null;
    $btnSaveChanges = !empty($_POST['btn_save_changes']) ? $_POST['btn_save_changes'] : '';
    $results_s = !empty($_POST['results']) ? serialize($_POST['results']) : '';
    $results_post = !empty($_POST['results']) ? $_POST['results'] : '';


    $sredType = !empty($_POST['sredType']) ? $_POST['sredType'] : '';
    $actualValuesSelectedSamples = [];
    $сurrentDataFrost = null;
    $TEMP_O = isset($_POST['TEMP_O']) ? (float)$_POST['TEMP_O'] : null;
    $VLAG_O = isset($_POST['VLAG_O']) ? (float)$_POST['VLAG_O'] : null;
    $TEMP_TO_O = isset($_POST['TEMP_TO_O']) ? (float)$_POST['TEMP_TO_O'] : null;
    $VLAG_TO_O = isset($_POST['VLAG_TO_O']) ? (float)$_POST['VLAG_TO_O'] : null;
    $temperatureBefore = !empty($_POST['temperature_before']) ? json_encode($_POST['temperature_before'], JSON_FORCE_OBJECT) : '{}';
    $temperatureAfter = !empty($_POST['temperature_after']) ? json_encode($_POST['temperature_after'], JSON_FORCE_OBJECT) : '{}';
    $humidityBefore = !empty($_POST['humidity_before']) ? json_encode($_POST['humidity_before'], JSON_FORCE_OBJECT) : '{}';
    $humidityAfter = !empty($_POST['humidity_after']) ? json_encode($_POST['humidity_after'], JSON_FORCE_OBJECT) : '{}';
    $temperatureHumidityCheck = !empty($_POST['temperature_humidity_check']) ? json_encode($_POST['temperature_humidity_check'], JSON_FORCE_OBJECT) : '{}';

    
    $description = !empty($_POST['DESCRIPTION']) ? $_POST['DESCRIPTION'] : '';
    $dateProbe = !empty($_POST['DATE_PROBE']) ? date("d.m.Y", strtotime($_POST['DATE_PROBE'])) : '-';
    $placeProbe = !empty($_POST['PLACE_PROBE']) ? $_POST['PLACE_PROBE'] : '-';
	
	$object = !empty($_POST['OBJECT']) ? $_POST['OBJECT'] : '';
    

    $assigned = CUser::GetByID($userGetId)->arResult[0];
    $assignedName = !empty($assigned['NAME']) ? $assigned['NAME'] : '';
    $assignedLastName = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';
    $assigned_user = $assignedName . ' ' . $assignedLastName;

    $dealData = CCrmDeal::GetByID($deal_id);

    $numRequest = !empty($dealData["TITLE"]) ? $dealData["TITLE"] : '';

    $resultSelectBaTzDataForCurrentDeal = $db->selectRow('SELECT * FROM `ba_tz` WHERE `ID_Z`={?}', [$deal_id]);


    $idТz = !empty($resultSelectBaTzDataForCurrentDeal['ID']) ? (int)$resultSelectBaTzDataForCurrentDeal['ID'] : 0;


    if (!empty($_POST['btn_form_protocol']) && !empty($deal_id) && !empty($idТz) && !empty($currentProtocolId)) {
        //header('Location: /protocol_generator/protocol_binding_multiple_protocols.php?ID=' . $deal_id . '&TZ_ID=' . $idТz . '&PROTOCOL_ID=' . $currentProtocolId);
		header('Location: /protocol_generator/protocol_multiple_protocols.php?ID=' . $deal_id . '&TZ_ID=' . $idТz . '&PROTOCOL_ID=' . $currentProtocolId);
	}


    $arrTz = !empty($resultSelectBaTzDataForCurrentDeal['TZ']) ? unserialize($resultSelectBaTzDataForCurrentDeal['TZ']) : '';
    $probe_history = !empty($resultSelectBaTzDataForCurrentDeal['PROBE']) ? unserialize($resultSelectBaTzDataForCurrentDeal['PROBE']) : '';

    $dateAct = !empty($resultSelectBaTzDataForCurrentDeal['DATE_ACT']) ? unserialize($resultSelectBaTzDataForCurrentDeal['DATE_ACT']) : ''; //Дата АКТа приемки проб

    $matchManualBaTz = !empty($resultSelectBaTzDataForCurrentDeal['MATCH_MANUAL']) ? unserialize($resultSelectBaTzDataForCurrentDeal['MATCH_MANUAL']) : [];
    $sredBaTz = !empty($resultSelectBaTzDataForCurrentDeal['SRED']) ? unserialize($resultSelectBaTzDataForCurrentDeal['SRED']) : [];
	$numPTableBaTz = !empty($resultSelectBaTzDataForCurrentDeal['NUM_P_TABLE']) ? $resultSelectBaTzDataForCurrentDeal['NUM_P_TABLE'] : '';

    if (!empty($currentProtocolId)) {
        $queryCurrentDataProtocol = "SELECT * FROM `PROTOCOLS` WHERE ID={?}";
        $arrDataProtocol = [$currentProtocolId];
        $сurrentDataProtocol = $db->selectRow($queryCurrentDataProtocol, $arrDataProtocol);
    }


    $currentProtocolNumber = !empty($сurrentDataProtocol['NUMBER']) ? (int)$сurrentDataProtocol['NUMBER'] : null;

    $currentProtocolDate = !empty($сurrentDataProtocol['DATE']) ? dateObjectFormat($сurrentDataProtocol['DATE'], 'Y-m-d') : null;

    $gr = isset($сurrentDataProtocol['GROUP_MAT']) ? htmlspecialchars($сurrentDataProtocol['GROUP_MAT']) : '';
    $temp_o = isset($сurrentDataProtocol['TEMP_O']) ? (float)$сurrentDataProtocol['TEMP_O'] : null;
    $temp_to_o = isset($сurrentDataProtocol['TEMP_TO_O']) ? (float)$сurrentDataProtocol['TEMP_TO_O'] : null;
    $vlag_o = isset($сurrentDataProtocol['VLAG_O']) ? (float)$сurrentDataProtocol['VLAG_O'] : null;
    $vlag_to_o = isset($сurrentDataProtocol['VLAG_TO_O']) ? (float)$сurrentDataProtocol['VLAG_TO_O'] : null;
    $date_begin = !empty($сurrentDataProtocol['DATE_BEGIN']) ? dateObjectFormat($сurrentDataProtocol['DATE_BEGIN'], "Y-m-d") : null;
    $date_end = !empty($сurrentDataProtocol['DATE_END']) ? dateObjectFormat($сurrentDataProtocol['DATE_END'], "Y-m-d") : null;

    $no_bitrix = isset($сurrentDataProtocol['PROTOCOL_OUTSIDE_LIS']) ? (int)$сurrentDataProtocol['PROTOCOL_OUTSIDE_LIS'] : null;
    $actualVersion = !empty($сurrentDataProtocol['ACTUAL_VERSION']) ? unserialize($сurrentDataProtocol['ACTUAL_VERSION']) : '';

    $typeTzOld = !empty($сurrentDataProtocol['PROTOCOL_TYPE']) ? (int)$сurrentDataProtocol['PROTOCOL_TYPE'] : '';
    $actualValues = !empty($сurrentDataProtocol['ACTUAL_VALUES']) ? unserialize($сurrentDataProtocol['ACTUAL_VALUES']) : [];

	$anchorSample = !empty($сurrentDataProtocol['ANCHOR_SAMPLE']) ? $сurrentDataProtocol['ANCHOR_SAMPLE'] : 0; //Закрепить протокол
    $sampleChecked = !empty($сurrentDataProtocol['SAMPLE_CHECKED']) ? unserialize($сurrentDataProtocol['SAMPLE_CHECKED']) : []; //Выбранные пробы

	$editResultProtocol = !empty($сurrentDataProtocol['EDIT_RESULTS']) ? $сurrentDataProtocol['EDIT_RESULTS'] : 0;

    $attestatInProtocol = isset($сurrentDataProtocol['ATTESTAT_IN_PROTOCOL']) ? (int)$сurrentDataProtocol['ATTESTAT_IN_PROTOCOL'] : null;

	if ($editResultProtocol && !empty($_POST['btn_save_changes'])) {
		$checkboxProba = $sampleChecked;
	} elseif ($anchorSample && empty($_POST['btn_create_protocol'])) {
        $checkboxProba = $sampleChecked;
    } else {
        $checkboxProba = !empty($_POST['checkboxProba']) ? $_POST['checkboxProba'] : '';
    }
    
    /*if ($anchorSample && empty($_POST['btn_create_protocol'])) {
    
        $checkboxProba = $sampleChecked;
    } else {
        $checkboxProba = !empty($_POST['checkboxProba']) ? $_POST['checkboxProba'] : '';
    }*/


    $serializeSampleChecked = !empty($checkboxProba) ? serialize($checkboxProba) : '';

    if ($sredType === 'frost' && !empty($currentProtocolId) && !empty($idТz)) {
        $queryCurrentDataFrost = "SELECT * FROM `b_frost` WHERE `tz_id`={?} AND `protocol_id`={?}";
        $arrDataFrost = [$idТz, $currentProtocolId];
        $сurrentDataFrost = $db->selectRow($queryCurrentDataFrost, $arrDataFrost);
    }


    $selectArrayProbe = [];
    foreach ($probe_history as $keyCount => $prob) {

        if (empty($checkboxProba[$keyCount - 1])) {
            continue;
        }


        $arrayGostKeySelectedSample = !empty($checkboxProba[$keyCount - 1]) ? array_keys($checkboxProba[$keyCount - 1]) : [];

        $gostKeySelectedSample = !empty($arrayGostKeySelectedSample[0]) ? (int)$arrayGostKeySelectedSample[0] : 0;

        $selectArrayProbe[$keyCount] = $prob;

        foreach ($prob as $key => $item) {

            $arrayNumberProbe = [];
            $arrayShNumber = [];
            switch ($key) {
                case 'number_probe':

                    foreach ($item as $keyProbe => $nameProbe) {

                        if (!in_array($keyProbe + 1, array_keys($checkboxProba[$keyCount - 1][$gostKeySelectedSample]))) {
                            continue;
                        }

                        $arrayNumberProbe[$keyProbe] = $nameProbe;

                    }

                    $selectArrayProbe[$keyCount]['number_probe'] = $arrayNumberProbe;

                    break;
                case 'sh_number':
                    foreach ($item as $keyProbe => $nameProbe) {

                        if (!in_array($keyProbe + 1, array_keys($checkboxProba[$keyCount - 1][$gostKeySelectedSample]))) {
                            continue;
                        }

                        $arrayShNumber[$keyProbe] = $nameProbe;
                    }

                    $selectArrayProbe[$keyCount]['sh_number'] = $arrayShNumber;
                    break;
                
                case 'mesto_data':
                    $selectArrayProbe[$keyCount]['mesto_data'] = $placeProbe . '; ' . $dateProbe;
                
            }

        }

    }

    $serializeSelectProbe = !empty($selectArrayProbe) ? serialize($selectArrayProbe) : '';
	
	$jsonSelectProbe = !empty($selectArrayProbe) ? json_encode($selectArrayProbe) : null;

    $arr_results = [];
    $arr_results_post = [];

    foreach ($results_post as $key_post => $arrDatum_post) {
		

        if (empty($checkboxProba[$key_post])) {
            continue;
        }


        $arrayGostKeyCurrentSelectedSample = !empty($checkboxProba[$key_post]) ? array_keys($checkboxProba[$key_post]) : [];

        $gostKeyCurrentSelectedSample = !empty($arrayGostKeyCurrentSelectedSample[0]) ? (int)$arrayGostKeyCurrentSelectedSample[0] : 0;



        foreach ($arrDatum_post as $k_post => $item_post) {

            foreach ($item_post as $key_probe_post => $probe_k_post) {

                if (!in_array($key_probe_post, array_keys($checkboxProba[$key_post][$gostKeyCurrentSelectedSample]))) {
                    continue;
                }



                $actualValuesSelectedSamples[$key_post][$k_post][$key_probe_post] = $probe_k_post;

                foreach ($probe_k_post as $key_result => $val_result) {

                    $res_post = null;

                    if (!empty($arrTz[$key_post + 1]['gost'][$k_post])) {

                        $res_post = $db->selectRow('SELECT * FROM `ba_gost` WHERE ID={?}', [$arrTz[$key_post + 1]['gost'][$k_post]]);
                    }

                    $sumPricesEachProtocols += isset($arrTz[$key_post + 1]['price'][$k_post]) ? (float)$arrTz[$key_post + 1]['price'][$k_post] : 0;

                    $gostPunct = !empty($res_post['GOST_PUNKT']) ? $res_post['GOST_PUNKT'] : '';

                    $method_post = !empty($res_post['GOST']) ? $res_post['GOST'] . ' ' . $gostPunct : '-';

					if (!$res_post['IN_OA'] && $res_post['ID'] != 2875) {
						$no_oa = 1;
					}
					$log = date("Y.m.d G:i:s") . "\n";
					$log .= print_r($no_oa, 1). "\n";
					file_put_contents(getcwd() . '/nooa1.txt', $log);

                    if (!empty($val_result)) {
                        $arr_results_post[][$val_result] = [
                            'material' => $arrTz[$key_post + 1]['mater'],
                            'probe' => $key_probe_post,
                            'method' => $method_post,
                        ];
                    }


                    if ($val_result != $actualValues[$key_post][$k_post][$key_probe_post][$key_result]) {
                        $arr_results[][$val_result] = [
                            'material' => $arrTz[$key_post + 1]['mater'],
                            'probe' => $key_probe_post,
                            'method' => $method_post,
                        ];
                    }

                }
            }
        }
    }


    $serializeActualValuesSelectedSamples = !empty($actualValuesSelectedSamples) ? serialize($actualValuesSelectedSamples) : '';


    if (!empty($_POST['btn_create_protocol']) && !empty($checkboxProba)) {
        if ($deal_id >= getIdDealAfterWhichOborudConnectedGost()) {
            console_log('true2', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
            $insetrProtocolData = 'INSERT INTO `PROTOCOLS` 
                                (`ID_TZ`, `PROBE`, `SAMPLE_CHECKED`, `ACTUAL_VALUES`, `PROBE_JSON`, `TEMPERATURE_BEFORE`, `TEMPERATURE_AFTER`, `HUMIDITY_BEFORE`, `HUMIDITY_AFTER`, `TEMPERATURE_HUMIDITY_CHEK`) 
                                VALUES 
                                ({?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?})';
            $arrayInsetrProtocolData = [
                $idТz,
                $serializeSelectProbe,
                $serializeSampleChecked,
                $serializeActualValuesSelectedSamples,
                $jsonSelectProbe,
                $temperatureBefore,
                $temperatureAfter,
                $humidityBefore,
                $humidityAfter,
                $temperatureHumidityCheck
            ];
        } else {
            console_log('false2', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
            $insetrProtocolData = 'INSERT INTO `PROTOCOLS` (`ID_TZ`, `PROBE`, `SAMPLE_CHECKED`, `ACTUAL_VALUES`, `PROBE_JSON`) VALUES ({?}, {?}, {?}, {?}, {?})';
            $arrayInsetrProtocolData = [$idТz, $serializeSelectProbe, $serializeSampleChecked, $serializeActualValuesSelectedSamples, $jsonSelectProbe];
        }

        $resultInsetrProtocolData = $db->queryExec($insetrProtocolData, $arrayInsetrProtocolData);

        $updateBaTzData = 'UPDATE `ba_tz` SET `RESULTS`={?} WHERE `ID`={?}';
        $arrayUpdateBaTzData = [$results_s, $idТz];
        $resultUpdateBaTzData = $db->queryExec($updateBaTzData, $arrayUpdateBaTzData);


        if (!empty($resultInsetrProtocolData) && !empty($deal_id)) {
            //header('Location: /results_isp_binding_multiple_protocols.php?ID=' . $deal_id . '&ID_P=' . $resultInsetrProtocolData);
			header('Location: /results_isp_OLD.php?ID=' . $deal_id . '&ID_P=' . $resultInsetrProtocolData);
		} else {
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }
    }



    $arr_results_ba_tz = [];

    foreach ($actualValues as $key_results_ba_tz => $val_results_ba_tz) {

        foreach ($val_results_ba_tz as $key_result_ba_tz => $val_result_ba_tz) {

            foreach ($val_result_ba_tz as $key_resul_ba_tz => $val_resul_ba_tz) {

                foreach ($val_resul_ba_tz as $key_res_ba_tz => $val_res_ba_tz) {


                    if (!empty($val_res_ba_tz)) {
                        $arr_results_ba_tz[][$val_res_ba_tz] = [
                            'probe' => $key_resul_ba_tz,
                        ];
                    }

                }
            }
        }
    }

    $str_type = 'Сохранение результатов испытаний. ';

    if (!empty($arr_results)) {
        foreach ($arr_results as $key_material => $val_material) {

            foreach ($val_material as $key_changed => $val_changed) {
                $str_type .= 'Материал ' . $val_changed['material'] . ', проба ' . $val_changed['probe'] . ', методика испытаний ' . $val_changed['method'] . ', было изменино фактическое значение. ';
            }
        }
    }


    if (count($arr_results_ba_tz) != count($arr_results_post) && count($arr_results_ba_tz) > count($arr_results_post)) {

        $count_del_material = count($arr_results_ba_tz) - count($arr_results_post);

        $str_del = (($count_del_material > 1) ? 'Удалены ' : 'Удалено ');
        $str_material = (($count_del_material > 1) ? ' фактических значений. ' : ' фактическое значение. ');
        $str_type .= $str_del . $count_del_material . $str_material;
    }


    if (count($arr_results_ba_tz) != count($arr_results_post) && count($arr_results_ba_tz) < count($arr_results_post)) {

        $count_add_material = count($arr_results_post) - count($arr_results_ba_tz);

        $str_add = (($count_add_material > 1) ? 'Добавлены ' : 'Добавлено ');
        $str_material_add = (($count_add_material > 1) ? ' фактических значений. ' : ' фактическое значение. ');
        $str_type .= $str_add . $count_add_material . $str_material_add;
    }



    if (!empty($currentProtocolDate)) {
        $yearFormatForCurrentProtocol = formatYearOfProtocol($currentProtocolDate);
    } else {
        $yearFormatForCurrentProtocol = formatYearOfProtocol($currentDate);
    }

    if (!empty($_GET['ID'])) {
        $res_nump = $DB->Query("SELECT `NUM_P`, `DATE_P`, `DATE_P_CORR`, `ID_Z` FROM `ba_tz` WHERE `ID_Z`!=" . $_GET['ID']);

        $date_p = date("Y", strtotime($_POST['DATE_P_CORR']));

        while ($res_n = $res_nump->Fetch()) {
            $date_n = $res_n['DATE_P_CORR'] ? date("Y", strtotime($res_n['DATE_P_CORR'])) : date("Y", strtotime($res_n['DATE_P']));
            if ($_POST['NUM_P'] == $res_n['NUM_P'] && $date_p == $date_n && $_POST['NUM_P'] != 'номер не присвоен') $fl_num = 1;

        }
    }

    if ($fl_num) echo "Данные не могут быть сохранены - указанный номер протокола уже используется! <br><br><a href='/results_isp_OLD.php?ID=" . $_GET['ID'] . "'>Вернуться к результатам испытаний</a><br><br>";

    if (CModule::IncludeModule('crm') && $dealData['STAGE_ID'] != '2' && $dealData['STAGE_ID'] != '3' && $dealData['STAGE_ID'] != '4' && $dealData['STAGE_ID'] != 'WON' && $dealData['TYPE_ID'] != 'COMPLEX') {
        $deal = new CCrmDeal;

        $arDealUpdate = array('STAGE_ID' => '1');
        $resUpd = $deal->Update((int)$_GET['ID'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );

        $update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('1') . "', `STAGE_NUMBER`= " . (int)2 . " WHERE `ID_Z`=" . $deal_id);

    }

    if (!empty($currentProtocolId) && !empty($_POST['assign_protocol_number']) && empty($currentProtocolNumber) && !empty($checkboxProba) && !empty($actualVersion) || !empty($currentProtocolId) && !empty($_POST['NO_BITRIX']) && empty($currentProtocolNumber) && !empty($checkboxProba)) {

		$resNunP = $DB->Query("SELECT * FROM `ba_tz`");
        $arrNumP = [];
        while($rowNumP = $resNunP->Fetch()) {
            $foundYearProtocol = date("Y", strtotime($rowNumP['DATE_P_CORR']));
            
            if ($currentYear == $foundYearProtocol) {
                if (is_numeric($rowNumP["NUM_P"])) {
                    $arrNumP[] = $rowNumP["NUM_P"];
                }
            }
        }

        $numberOfPreviouslyAssignedNumbersForProtocols = 1034;
		$countPreviouslyCreatedProtocols = 8;


        $resultCountsProtocolsWithNumber = $db->selectRow("SELECT COUNT(*) AS `NUMBER_RECORDS` FROM `PROTOCOLS` WHERE YEAR(`DATE`) = YEAR(CURDATE()) AND `NUMBER` IS NOT NULL AND `NUMBER` > 0");

        $countsProtocolsWithNumber = !empty($resultCountsProtocolsWithNumber['NUMBER_RECORDS']) ? (int)$resultCountsProtocolsWithNumber['NUMBER_RECORDS'] : 0;

        $newProtocolNumber = $currentYear === '2021' ?
            ($countsProtocolsWithNumber + 1 + $numberOfPreviouslyAssignedNumbersForProtocols - $countPreviouslyCreatedProtocols) :
            ($countsProtocolsWithNumber + 1);

        $protocolNumberAndYear = !empty($newProtocolNumber) ? $newProtocolNumber . '/' . $yearFormatForCurrentProtocol : '';

        $updateProtocolData = "UPDATE `PROTOCOLS` SET `NUMBER`={?}, `NUMBER_AND_YEAR`={?}, `DATE`={?}, `ACTUAL_VERSION`={?} WHERE `ID`={?}";

        $arrayUpdateProtocolData = [$newProtocolNumber, $protocolNumberAndYear, $currentDate, null, $currentProtocolId];

        $resultUpdateProtocolData = $db->queryExec($updateProtocolData, $arrayUpdateProtocolData);

		if ($deal_id && $protocolNumberAndYear) {
			$protocolNumbersAndYears = (!empty($numPTableBaTz) && $numPTableBaTz !== 'номер не присвоен') ? $numPTableBaTz . ', ' . $protocolNumberAndYear : $protocolNumberAndYear;

			$resUpdateBaTz = $DB->Query("UPDATE `ba_tz` SET `NUM_P_TABLE`='" . $protocolNumbersAndYears . "', `NUM_P`='" . $newProtocolNumber . "', `DATE_P_CORR`='" . $currentDate . "' WHERE `ID_Z`=" . $deal_id);
        }
		
        if (!empty($_POST['NO_BITRIX'])) {
            $fl_nobitrix = 1;
        }


        $flagAllGeneratedProtocols = 1;
        $countAllNumbersProbe = 0;
        $countAllSelectedNumbersProbe = 0;
        $allProbeRequest = !empty($resultSelectBaTzDataForCurrentDeal['PROBE']) ? unserialize($resultSelectBaTzDataForCurrentDeal['PROBE']) : [];
        $allTzRequest = !empty($resultSelectBaTzDataForCurrentDeal['TZ']) ? unserialize($resultSelectBaTzDataForCurrentDeal['TZ']) : [];

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

        $selectedProtocolData = $db->select("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}", [$idТz]);

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

//                if (
//                    (empty($actualVersionCurrentProtocol) && empty($numberCurrentProtocol) && empty($pdfCurrentProtocol)) ||
//                    (empty($currentProtocolOutsideLis) && empty($actualVersionCurrentProtocol) && !empty($numberCurrentProtocol)) ||
//                    (empty($currentProtocolOutsideLis) && !empty($actualVersionCurrentProtocol) && empty($numberCurrentProtocol)) ||
//                    (!empty($currentProtocolOutsideLis) && empty($pdfCurrentProtocol))
//                ) {
//                    $flagAllGeneratedProtocols = 0;
//                }
                if (empty($numberCurrentProtocol)) {
                    $flagAllGeneratedProtocols = 0;
                }
            }
        } else {
            $flagAllGeneratedProtocols = 0;
        }



        if (!empty($flagAllGeneratedProtocols) && (!empty($countAllSelectedNumbersProbe) && !empty($countAllNumbersProbe) && $countAllNumbersProbe === $countAllSelectedNumbersProbe)) {

            if (CModule::IncludeModule('crm')) {
                $deal1 = new CCrmDeal;
                $arDealUpdate = array('STAGE_ID' => '2');
                $resUpd = $deal1->Update($deal_id,
                    $arDealUpdate,
                    true,
                    true,
                    array('DISABLE_USER_FIELD_CHECK' => true)
                );
            }

            //$update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('2') . "', `STAGE_NUMBER`= " . (int)3 . " WHERE `ID_Z`=" . $deal_id);
            $update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('2') . "', `STAGE_NUMBER`= " . (int)3 . ", `dateEnd`='" . date("Y-m-d") . "' WHERE `ID_Z`=" . $deal_id);
        }
    }

    $ostatki = !empty($_POST['ostatki']) ? serialize($_POST['ostatki']) : [];
    $temp = serialize($_POST['temp']);
    $vlag = serialize($_POST['vlag']);
    $verify = serialize($_POST['verify_s']);
	
	$verifyS = !empty($_POST['verify_s']) ? $_POST['verify_s'] : [];

    $arrAssigned = [];
    foreach ($verifyS as $keyVerify => $idVerify){
        $assigned = CUser::GetByID((int)$idVerify)->Fetch();

        $assignedName = !empty($assigned['NAME']) ? $assigned['NAME'] : '';

        $assignedLastname = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';

        $assignedUser = $assignedName.' '.$assignedLastname;

        if (!empty($assignedUser)) {
            $arrAssigned[] =  $assignedUser;
        }
    }

    $verifyNameLastname = !empty($arrAssigned) ? implode(', ', $arrAssigned) : '';
	
    $norm_descript = serialize($_POST['norm_description']);
    $date1 = $_POST['DATE_BEGIN'];
    $date2 = $_POST['DATE_END'];
    $DOPINFO = $_POST['DOP_INFO'];
    $lab = serialize($_POST['lab']);
    $sostav = serialize($_POST['sostav']);
    $OBSHEE = 1;
    $NO_COMPLIANCE = ($_POST['NO_COMPLIANCE'] == 'on') ? 1 : 0;
    // $SELECTION_TYPE = ($_POST['SELECTION_TYPE'] == 'on') ? 1 : 0;
    $NO_BITRIX = ($_POST['NO_BITRIX'] == 'on') ? 1 : 0;
    $ACTUAL_FOR_OBJECT = ($_POST['ACTUAL_FOR_OBJECT'] == 'on') ? 1 : 0;


    $ATTESTAT_IN_PROTOCOL = (int)$_POST['HIDDEN_NO_ATTESTAT'];

    $NO_CONTRACT = ($_POST['NO_CONTRACT'] == 'on') ? 1 : 0;
    $check_norm = 1;
    $sr = [];
    $match = [];


    foreach ($arrTz as $key => $arrDatum) {

        if (empty($checkboxProba[$key-1])) {
            continue;
        }


        $arrayGostKeyCurrentSelectedSample = !empty($checkboxProba[$key - 1]) ? array_keys($checkboxProba[$key - 1]) : [];

        $gostKeyCurrentSelectedSample = !empty($arrayGostKeyCurrentSelectedSample[0]) ? (int)$arrayGostKeyCurrentSelectedSample[0] : 0;


        foreach ($probe_history[$key]['number_probe'] as $key_probe => $probe_k) {
            $key_probe = $key_probe + 1;

            if (!in_array($key_probe, array_keys($checkboxProba[$key - 1][$gostKeyCurrentSelectedSample]))) {
                continue;
            }

            $j = 0;

            foreach ($arrDatum['gost'] as $k => $item) {

                $res_otbor = $db->selectRow('SELECT * FROM `ba_gost` WHERE ID={?}', [$item]);


                if ($res_otbor['GOST_TYPE'] != 'metodic_otbor') {

                    $gost_type = !empty($res_otbor['GOST_TYPE']) ? $res_otbor['GOST_TYPE'] : '';
                    
                    $accuracy = $res_otbor['ACCURACY'];


                    $sum = 0;

                    foreach ($results_post[$key - 1][$k][$key_probe] as $kk => $res):
                        $res = str_replace(',', '.', $res);
                        $sum += $res;
                    endforeach;

                    if ($gost_type == "TU_sred2" || $gost_type == "TU_sred3") {

                        $sr[$key - 1][$k][$key_probe] = (count($results_post[$key - 1][$k][$key_probe]) > 2 ? round((($sum - min($results_post[$key - 1][$k][$key_probe])) / (count($results_post[$key - 1][$k][$key_probe]) - 1)), $accuracy) : round(($sum / count($results_post[$key - 1][$k][$key_probe])), $accuracy));
                    } elseif ($gost_type == "TU_sred4") {

                        $min_res = min($results_post[$key - 1][$k][$key_probe]);

                        unset($results_post[$key - 1][$k][$key_probe][array_search($min_res, $results_post[$key - 1][$k][$key_probe])]);

                        $sr[$key - 1][$k][$key_probe] = round((($sum - $min_res - min($results_post[$key - 1][$k][$key_probe])) / (count($results_post[$key - 1][$k][$key_probe]) - 1)), $accuracy);
                    } elseif ($gost_type == "TU_sred5") {

                        sort($results_post[$key - 1][$k][$key_probe]);

                        $res1 = $results_post[$key - 1][$k][$key_probe][round(count($results_post[$key - 1][$k][$key_probe]) / 2, 0) - 1];

                        $res2 = $results_post[$key - 1][$k][$key_probe][round(count($results_post[$key - 1][$k][$key_probe]) / 2, 0)];
                        $sr[$key - 1][$k][$key_probe] = round(($res1 + $res2) / 2, $accuracy);
                    } else {

                        $sr[$key - 1][$k][$key_probe] = round(($sum / count($results_post[$key - 1][$k][$key_probe])), $accuracy);
                    }


                    $res1 = null;
                    if (!empty($arrDatum['gost_new'][$k])) {
                        $res1 = $db->selectRow('SELECT * FROM `ba_gost` WHERE ID={?}', [$arrDatum['gost_new'][$k]]);
                    }


                    $norm_dop = !empty($res1['NORM_DOP']) ? unserialize($res1['NORM_DOP']) : '';

                    $val_dop = !empty($res1['VALUE_DOP']) ? unserialize($res1['VALUE_DOP']) : '';
                    $norm01 = 0;
                    $norm02 = 0;


                    if ($res1['GOST_TYPE'] == 'TU_group') {
                        $gr_mat = explode('-', $gr);
                        $r = $gr_mat[1];
                        $grup_mat = $val_dop[$r];
                    }

					


                    if ($res1['MATCH_MANUAL']) {

                        $match[$key - 1][$k][$key_probe] = isset($_POST['match'][$key - 1][$k][$key_probe]) ? (int)$_POST['match'][$key - 1][$k][$key_probe] : '';

                    } else {

                        if ($_POST['NO_COMPLIANCE']) {
                            $match[$key - 1][$k][$key_probe] = 2;
                        } else {

							
                            if ($grup_mat != '') {
                                for ($j = 0; $j < 10; $j++) {
                                    if ($grup_mat == $val_dop[$j]) {
                                        $norm01 = $norm_dop[$j][0];
                                        $norm02 = $norm_dop[$j][1];
                                    }
                                }


                                if (($norm01 <= $sr[$key - 1][$k][$key_probe]) && ($norm02 >= $sr[$key - 1][$k][$key_probe])) {
                                    $match[$key - 1][$k][$key_probe] = 1;
                                } else {
                                    $match[$key - 1][$k][$key_probe] = 0;
                                }
                            } else {

                                if (($res1['NORM1'] <= $sr[$key - 1][$k][$key_probe]) && ($res1['NORM2'] >= $sr[$key - 1][$k][$key_probe])) {
                                    $match[$key - 1][$k][$key_probe] = 1;

                                } else {
                                    $match[$key - 1][$k][$key_probe] = 0;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    
    $matchAll = [];
    $sredAll = [];

    foreach ($matchManualBaTz as $keyMaterial => $dataMaterial) {
        foreach ($dataMaterial as $keyGost => $dataGost) {
            foreach ($dataGost as $keySample => $dataSample) {
                $matchAll[$keyMaterial][$keyGost][$keySample] = $dataSample;
                $sredAll[$keyMaterial][$keyGost][$keySample] = isset($sredBaTz[$keyMaterial][$keyGost][$keySample]) ?
                    $sredBaTz[$keyMaterial][$keyGost][$keySample] : null;
            }
        }
    }

    foreach ($match as $keyMaterial => $dataMaterial) {
        foreach ($dataMaterial as $keyGost => $dataGost) {
            foreach ($dataGost as $keySample => $dataSample) {
                $matchAll[$keyMaterial][$keyGost][$keySample] = $dataSample;
                $sredAll[$keyMaterial][$keyGost][$keySample] = isset($sr[$keyMaterial][$keyGost][$keySample]) ?
                    $sr[$keyMaterial][$keyGost][$keySample] : null;
            }
        }
    }

    $matchAll = !empty($matchAll) ? serialize($matchAll) : '';
    $sredAll = !empty($sredAll) ? serialize($sredAll) : '';

    $controlDamage = !empty($_POST['controlDamage']) ? $_POST['controlDamage'] : '';
    $mainDamage = !empty($_POST['mainDamage']) ? $_POST['mainDamage'] : '';
    $controlMass = !empty($_POST['controlMass']) ? $_POST['controlMass'] : '';
    $mainMass = !empty($_POST['mainMass']) ? (double)$_POST['mainMass'] : '';
    $controlStrength = !empty($_POST['controlStrength']) ? serialize($_POST['controlStrength']) : '';
    $mainStrength = !empty($_POST['mainStrength']) ? serialize($_POST['mainStrength']) : '';
    $controlMediumStrength = !empty($_POST['controlMediumStrength']) ? (double)$_POST['controlMediumStrength'] : '';
    $mainMediumStrength = !empty($_POST['mainMediumStrength']) ? (double)$_POST['mainMediumStrength'] : '';
    $controlBottomLine = !empty($_POST['controlBottomLine']) ? (double)$_POST['controlBottomLine'] : '';
    $mainBottomLine = !empty($_POST['mainBottomLine']) ? (double)$_POST['mainBottomLine'] : '';
    $ratio = !empty($_POST['ratio']) ? $_POST['ratio'] : '';


    if ($sredType === 'frost' && !empty($currentProtocolId) && !empty($idТz) && empty($сurrentDataFrost)) {

        $queryInsertFrostData = "INSERT INTO `b_frost` (`control_damage`, `main_damage`, `control_mass`, `main_mass`,
            `control_strength`, `main_strength`, `control_medium`, `main_medium`, `control_bottom_line`,
            `main_bottom_line`, `ratio`, `tz_id`, `protocol_id`) VALUES ({?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?})";

        $arrayInsertFrostData = [$controlDamage, $mainDamage, $controlMass, $mainMass, $controlStrength,
            $mainStrength, $controlMediumStrength, $mainMediumStrength, $controlBottomLine, $mainBottomLine,
            $ratio, $idТz, $currentProtocolId];

        $resultInsertFrostData = $db->queryExec($queryInsertFrostData, $arrayInsertFrostData);

    } else{


        $queryUpdateFrostData = "UPDATE `b_frost` SET `control_damage`={?}, `main_damage`={?}, `control_mass`={?},
            `main_mass`={?}, `control_strength`={?}, `main_strength`={?}, `control_medium`={?}, `main_medium`={?},
		    `control_bottom_line`={?}, `main_bottom_line`={?}, `ratio`={?} WHERE `tz_id`={?} AND `protocol_id`={?}";

        $arrayUpdateFrostData = [$controlDamage, $mainDamage, $controlMass, $mainMass, $controlStrength,
            $mainStrength, $controlMediumStrength, $mainMediumStrength, $controlBottomLine, $mainBottomLine,
            $ratio, $idТz, $currentProtocolId];

        $resultUpdateFrostData = $db->queryExec($queryUpdateFrostData, $arrayUpdateFrostData);
    }

    foreach ($match as $keyMaterial => $dataKeyMaterial) {

        foreach ($dataKeyMaterial as $keyGost => $dataKeyGost) {

            foreach ($dataKeyGost as $keySample => $dataKeySample) {


                if (empty($dataKeySample)) {
                    $check_norm = 0;
                }
            }
        }
    }


    if ($ACTUAL_FOR_OBJECT) $check_norm = 1;


    switch ($_POST['sredType']) {
        case 'simple':
            $TYPE_TZ=0;
            break;
        case 'allvalue':
            $TYPE_TZ=1;
            break;
        case '2max':
            $TYPE_TZ=2;
            break;
        case '3max':
            $TYPE_TZ=3;
            break;
        case '4max':
            $TYPE_TZ=4;
            break;
        case 'zern':
            $TYPE_TZ=5;
            $ostatki = !empty($_POST['ostatki']) ? serialize($_POST['ostatki']) : '';
            break;
        case 'grunt':
            $TYPE_TZ=6;
            $ostatki = !empty($_POST['ostatki3']) ? serialize($_POST['ostatki3']) : '';
            break;
        case 'prirod':
            $TYPE_TZ=7;
            $ostatki = !empty($_POST['ostatki4']) ? serialize($_POST['ostatki4']) : '';
            break;
        case 'tu_12801':
            $TYPE_TZ=8;
            $ostatki = !empty($_POST['ostatki2']) ? serialize($_POST['ostatki2']) : '';
            break;
        case 'tu_183':
            $TYPE_TZ=9;
            $ostatki = !empty($_POST['ostatki5']) ? serialize($_POST['ostatki5']) : '';
            break;
        case 'osk1':
            $TYPE_TZ=10;
            break;
        case 'osk2':
            $TYPE_TZ=11;
            break;
        case 'osk3':
            $TYPE_TZ=12;
            $ostatki = !empty($_POST['ostatki5']) ? serialize($_POST['ostatki5']) : '';
            break;
        case 'osk4':
            $TYPE_TZ=13;
            $ostatki = !empty($_POST['ostatki4']) ? serialize($_POST['ostatki4']) : '';
            break;
        case 'osk_sred':
            $TYPE_TZ=14;
            break;
        case 'shps':
            $TYPE_TZ=15;
            $ostatki = !empty($_POST['ostatki6']) ? serialize($_POST['ostatki6']) : '';
            break;
        case 'tu_183_2':
            $TYPE_TZ=16;
            $ostatki = !empty($_POST['ostatki5']) ? serialize($_POST['ostatki5']) : '';
            break;
        case 'sheb':
            $TYPE_TZ=17;
            $ostatki = !empty($_POST['ostatki7']) ? serialize($_POST['ostatki7']) : '';
            break;
        case 'sheb_shlak':
            $TYPE_TZ=18;
            $ostatki = !empty($_POST['ostatki8']) ? serialize($_POST['ostatki8']) : '';
            break;
		case 'sheb_shlak_g3344':
			$TYPE_TZ = 41;
			$ostatki = !empty($_POST['ostatki8']) ? serialize($_POST['ostatki8']) : '';
			break;
        case 'zern_sheb':
            $TYPE_TZ=19;
            $ostatki = !empty($_POST['ostatki7']) ? serialize($_POST['ostatki7']) : '';
            break;
        case 'density_grunt':
            $TYPE_TZ = 20;
            break;
        case 'zern_№2':
            $TYPE_TZ=21;
            $ostatki = !empty($_POST['ostatki']) ? serialize($_POST['ostatki']) : '';
            break;
        case 'grunt_№2':
            $TYPE_TZ=22;
            $ostatki = !empty($_POST['ostatki3']) ? serialize($_POST['ostatki3']) : '';
            break;
        case 'prirod_№2':
            $TYPE_TZ=23;
            $ostatki = !empty($_POST['ostatki4']) ? serialize($_POST['ostatki4']) : '';
            break;
        case 'tu_12801_№2':
            $TYPE_TZ=24;
            $ostatki = !empty($_POST['ostatki2']) ? serialize($_POST['ostatki2']) : '';
            break;
        case 'tu_183_2_№2':
            $TYPE_TZ=25;
            $ostatki = !empty($_POST['ostatki5']) ? serialize($_POST['ostatki5']) : '';
            break;
        case 'tu_183_№2':
            $TYPE_TZ=26;
            $ostatki = !empty($_POST['ostatki5']) ? serialize($_POST['ostatki5']) : '';
            break;
        case 'zern_sheb_№2':
            $TYPE_TZ=27;
            $ostatki = !empty($_POST['ostatki7']) ? serialize($_POST['ostatki7']) : '';
            break;
        case 'shps_№2':
            $TYPE_TZ=28;
            $ostatki = !empty($_POST['ostatki6']) ? serialize($_POST['ostatki6']) : '';
            break;
        case 'sheb_shlak_№2':
            $TYPE_TZ=29;
            $ostatki = !empty($_POST['ostatki8']) ? serialize($_POST['ostatki8']) : '';
            break;
        case 'frost':
            $TYPE_TZ=30;
            break;
        case 'metric_method':
            $TYPE_TZ=31;
            $ostatki = !empty($_POST['ostatki9']) ? serialize($_POST['ostatki9']) : '';
            break;
        case 'gost31015':
            $TYPE_TZ=32;
            $ostatki = !empty($_POST['ostatki2']) ? serialize($_POST['ostatki2']) : '';
            break;
        case 'shortcut':
            $TYPE_TZ=33;
            break;
        case 'shortcut_mean':
            $TYPE_TZ=34;
            break;
        case 'zern_short_simple':
            $TYPE_TZ = 35;
            $ostatki = !empty($_POST['ostatki']) ? serialize($_POST['ostatki']) : '';
            break;
        case 'prirod_short_simple':
            $TYPE_TZ = 36;
            $ostatki = !empty($_POST['ostatki4']) ? serialize($_POST['ostatki4']) : '';
            break;
        case 'thermal_conductivity':
            $TYPE_TZ = 37;
            break;
        case 'sheb_shlak_№2_simple':
            $TYPE_TZ = 38;
            $ostatki = !empty($_POST['ostatki8']) ? serialize($_POST['ostatki8']) : '';
            break;
        case 'sheb_shlak_simple':
            $TYPE_TZ = 39;
            $ostatki = !empty($_POST['ostatki8']) ? serialize($_POST['ostatki8']) : '';
            break;
        case 'new_25607':
            $TYPE_TZ = 40;
            $ostatki = !empty($_POST['ostatki6']) ? serialize($_POST['ostatki6']) : '';
            break;
    }


    $pathToPdfFile = null;
    if (!empty($idТz) && !empty($currentProtocolDate) && !empty($currentProtocolNumber) && is_numeric($currentProtocolNumber) && !empty($currentProtocolDate)) {

        $pathToPdfFile = './protocol_generator/archive/' . $idТz . date('Y', strtotime($currentProtocolDate)) . '/' . $currentProtocolId . '/Протокол №' . $currentProtocolNumber . ' от ' . date('d.m.Y', strtotime($currentProtocolDate)) . '.pdf';
    }

    $pathToSigFile = null;

    $pathToForsignFile = null;
     if (!empty($tzId) && $currentProtocolDate && !empty($currentProtocolNumber) && is_numeric($currentProtocolNumber)) {
   
        $pathToSigFile = './protocol_generator/archive/' . $idТz . date('Y', strtotime($currentProtocolDate)) . '/' . $currentProtocolId . '/' . $currentProtocolNumber . '.sig';


        $pathToForsignFile = './protocol_generator/archive/' . $idТz . date('Y', strtotime($currentProtocolDate)) . '/' . $currentProtocolId . '/forsign.docx';
    }

    if (!empty($no_bitrix)) {

        unlink($pathToSigFile);

        unlink($pathToForsignFile);

        unlink($pathToPdfFile);
    } else {

        if (isset($TYPE_TZ) && !in_array($TYPE_TZ, [33, 34, 35, 36, 37, 38, 39]) && in_array($typeTzOld, [33, 34, 35, 36, 37, 38, 39])) {


            unlinkFile($pathToSigFile);

            unlinkFile($pathToForsignFile);

        }

        if (isset($typeTzOld) && isset($TYPE_TZ) && $TYPE_TZ != $typeTzOld) {

            unlinkFile($pathToPdfFile);
        }

    }

    $fl_num_p = 0;


    if (isset($temp_o) && isset($_POST['TEMP_O']) && $temp_o != $_POST['TEMP_O'] || isset($temp_to_o) && isset($_POST['TEMP_TO_O']) && $temp_to_o != $_POST['TEMP_TO_O']) {
        $str_type .= 'Изменили значение температуры. ';
    }


    if (isset($vlag_o) && isset($_POST['VLAG_O']) && $vlag_o != $_POST['VLAG_O'] || isset($vlag_to_o) && isset($_POST['VLAG_TO_O']) && $vlag_to_o != $_POST['VLAG_TO_O']) {
        $str_type .= 'Изменили значение влажности. ';
    }


    if (isset($date_begin) && isset($_POST['DATE_BEGIN']) && $date_begin != $_POST['DATE_BEGIN']) {
        $str_type .= 'Изменили дату начала испытаний. ';
    }


    if (isset($date_end) && isset($_POST['DATE_END']) && $date_end != $_POST['DATE_END']) {
        $str_type .= 'Изменили дату окончания испытаний. ';
    }


    if (!empty($_POST['NUM_P']) && !empty($currentProtocolNumber) && $_POST['NUM_P'] !== 'номер не присвоен' && $currentProtocolNumber != $_POST['NUM_P']) {
        $str_type .= 'Изменили номер протокола. ';
    }



    if (!empty($_POST['DATE_P_CORR']) && !empty($currentProtocolDate) && $currentProtocolDate !== $_POST['DATE_P_CORR']) {
        $str_type .= 'Изменили дату протокола. ';
    }


    if (isset($NO_BITRIX) && $no_bitrix != $NO_BITRIX) {
        $str_type .= 'Изменили протокол выдается вне ЛИС. ';
    }

	
    if (!empty($currentProtocolId) && !empty($btnSaveChanges)) {

        $deleteActualVersion = null;

        $updateBaTzData = $DB->Query("UPDATE `ba_tz` SET `RESULTS`='" . $results_s . "', `OBSHEE`='" . $OBSHEE . "',
			`DATE_RESULTS`='" . date("Y-m-d") . "', `MATCH_MANUAL`='" . $matchAll . "', `NORM_DESC`='" . $norm_descript . "',
			 `SRED`='" . $sredAll . "', `CHECK_NORM`=" . (int)$check_norm . ", `ACTUAL_FOR_OBJECT`='" . $ACTUAL_FOR_OBJECT . "'
			WHERE ID = " . (int)$_POST['ID']);

        if ($deal_id >= getIdDealAfterWhichOborudConnectedGost()) {
            console_log('true3', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
            $updateProtocolData = "UPDATE `PROTOCOLS` SET `ostatki`={?}, `NO_COMPLIANCE`={?}, `PROTOCOL_TYPE`={?}, `TEMP_O`={?}, `TEMP_TO_O`={?}, `VLAG_O`={?}, `VLAG_TO_O`={?}, `DATE_BEGIN`={?}, `DATE_END`={?}, `DOP_INFO`={?}, `GROUP_MAT`={?}, `SOSTAV`={?}, `PROTOCOL_OUTSIDE_LIS`={?}, `VERIFY`={?}, `NO_CONTRACT`={?}, `ATTESTAT_IN_PROTOCOL`={?}, `PROBE`={?}, `SAMPLE_CHECKED`={?}, `ACTUAL_VERSION`={?}, `ACTUAL_VALUES`={?}, `DESCRIPTION`={?}, `OBJECT`={?}, `PROBE_JSON`={?}, `VERIFY_NAME_LASTNAME`={?}, `PROTOCOL_PRICE`={?}, `TEMPERATURE_BEFORE`={?}, `TEMPERATURE_AFTER`={?}, `HUMIDITY_BEFORE`={?}, `HUMIDITY_AFTER`={?}, `TEMPERATURE_HUMIDITY_CHEK`={?} WHERE ID={?}";
            $arrayUpdateProtocolData = [$ostatki, $NO_COMPLIANCE, $TYPE_TZ, $TEMP_O, $TEMP_TO_O, $VLAG_O, $VLAG_TO_O, $date1, $date2, $DOPINFO, $_POST['group_mat'], $sostav, $NO_BITRIX, $verify, $NO_CONTRACT, $ATTESTAT_IN_PROTOCOL, $serializeSelectProbe, $serializeSampleChecked, $deleteActualVersion, $serializeActualValuesSelectedSamples, $description,  $object, $jsonSelectProbe, $verifyNameLastname, $sumPricesEachProtocols, $temperatureBefore, $temperatureAfter, $humidityBefore, $humidityAfter, $temperatureHumidityCheck, $currentProtocolId];

        } else {
            console_log('false3', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
            $updateProtocolData = "UPDATE `PROTOCOLS` SET `ostatki`={?}, `NO_COMPLIANCE`={?}, `PROTOCOL_TYPE`={?},
			`TEMP_O`={?}, `TEMP_TO_O`={?}, `VLAG_O`={?}, `VLAG_TO_O`={?}, `DATE_BEGIN`={?},`DATE_END`={?}, `DOP_INFO`={?},
			`GROUP_MAT`={?},`SOSTAV`={?}, `PROTOCOL_OUTSIDE_LIS`={?}, `VERIFY`={?}, `NO_CONTRACT`={?},
			`ATTESTAT_IN_PROTOCOL`={?}, `PROBE`={?}, `SAMPLE_CHECKED`={?}, `ACTUAL_VERSION`={?}, `ACTUAL_VALUES`={?},
			 `DESCRIPTION`={?}, `OBJECT`={?}, `PROBE_JSON`={?}, `VERIFY_NAME_LASTNAME`={?}, `PROTOCOL_PRICE`={?} WHERE ID={?}";

            $arrayUpdateProtocolData = [$ostatki, $NO_COMPLIANCE, $TYPE_TZ, $TEMP_O, $TEMP_TO_O, $VLAG_O, $VLAG_TO_O,
                $date1, $date2, $DOPINFO, $_POST['group_mat'], $sostav, $NO_BITRIX, $verify, $NO_CONTRACT,
                $ATTESTAT_IN_PROTOCOL, $serializeSelectProbe, $serializeSampleChecked, $deleteActualVersion,
                $serializeActualValuesSelectedSamples, $description, $object, $jsonSelectProbe, $verifyNameLastname,
                $sumPricesEachProtocols, $currentProtocolId];
        }

        $resultUpdateProtocolData = $db->queryExec($updateProtocolData, $arrayUpdateProtocolData);

    }

    $res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`, `PROTOCOL_ID`) VALUES ('" . $currentDateTime . "','" . $assigned_user . "','" . $currentProtocolNumber . "','" . $idТz . "','" . $userGetId . "', '" . $str_type . "', '" . $numRequest . "', " . $currentProtocolId . ")");


    if ($deal_id) {

        $currentUserAndDatatime = $assigned_user . " " . $currentDateTime;

        $update_assignet_ba_tz = $DB->Query("UPDATE `ba_tz` SET `USER_HISTORY` = '" . $currentUserAndDatatime . "' WHERE `ID_Z` = " . $deal_id);

    }


//    if (CModule::IncludeModule('crm') && $dealData['STAGE_ID'] != '2' && $dealData['STAGE_ID'] != '3' && $dealData['STAGE_ID'] != '4' && $dealData['STAGE_ID'] != 'WON' && $dealData['TYPE_ID'] != 'COMPLEX') {
//        $deal = new CCrmDeal;
//
//        $arDealUpdate = array('STAGE_ID' => '1');
//        $resUpd = $deal->Update((int)$_GET['ID'],
//            $arDealUpdate,
//            true,
//            true,
//            array('DISABLE_USER_FIELD_CHECK' => true)
//        );
//
//        $update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('1') . "', `STAGE_NUMBER`= " . (int)2 . " WHERE `ID_Z`=" . $deal_id);
//
//    }

}

$uploadfile = null;


if (!empty($_FILES) && !empty($currentProtocolId)) {

    $nameFile = !empty($_FILES['userfile']['name']) ? $_FILES['userfile']['name'] : '';

    $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/pdf/' . $currentProtocolId . '/';


    if (!is_dir($uploaddir)) {
        mkdir($uploaddir, 0777, true);
    }

    $queryUpdateProtocolData = 'UPDATE `PROTOCOLS` SET `PDF`={?} WHERE `ID`={?}';
    $arrayUpdateProtocolData = [$nameFile, $currentProtocolId];
    $resultUpdateProtocolData = $db->queryExec($queryUpdateProtocolData, $arrayUpdateProtocolData);

    $uploadfile = $uploaddir . $_FILES['userfile']['name'];

}


$arrayOutRange = [];


if (!empty($deal_id) && !$fl_num) {

    $grup_mat = "";


    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
    $sql = $res->Fetch();


    $idTZ = !empty($sql['ID']) ? (int)$sql['ID'] : '';

    $results = !empty($sql['RESULTS']) ? unserialize($sql['RESULTS']) : [];
    $attestat = isset($sql['ATTESTAT']) ? $sql['ATTESTAT'] : null;

    $arrProbeBaTz = !empty($sql['PROBE']) ? unserialize($sql['PROBE']) : [];
    $arrPlaceDateProbeBaTz = explode("; ", $arrProbeBaTz[1]['mesto_data']);

    $placeProbeBaTz = !empty($arrPlaceDateProbeBaTz[0]) ? $arrPlaceDateProbeBaTz[0] : '';

    $dateProbeBaTz = !empty($arrPlaceDateProbeBaTz[1]) ? date("Y-m-d", strtotime($arrPlaceDateProbeBaTz[1])) : null;

    $descriptionBaTz = !empty($sql['DESCRIPTION']) ? $sql['DESCRIPTION'] : '';
	
	$objectBaTz = !empty($sql['OBJECT']) ? $sql['OBJECT'] : '';

    $allMatchManualBaTz = !empty($sql['MATCH_MANUAL']) ? unserialize($sql['MATCH_MANUAL']) : '';
    $allSredBaTz = !empty($sql['SRED']) ? unserialize($sql['SRED']) : '';
	
	$stageIdBaTz = !empty($sql['STAGE_ID']) ? $sql['STAGE_ID'] : '';

    if (!empty($currentProtocolId)) {
        $queryCurrentDataProtocol = "SELECT * FROM `PROTOCOLS` WHERE ID={?}";
        $arrDataProtocol = [$currentProtocolId];
        $сurrentDataProtocol = $db->selectRow($queryCurrentDataProtocol, $arrDataProtocol);
    }


    $sr = !empty($сurrentDataProtocol['SRED']) ? unserialize($сurrentDataProtocol['SRED']) : '';

    $protocolActualValues = !empty($сurrentDataProtocol['ACTUAL_VALUES']) ? unserialize($сurrentDataProtocol['ACTUAL_VALUES']) : [];

    $protocolMatchManual = !empty($сurrentDataProtocol['MATCH_MANUAL']) ? unserialize($сurrentDataProtocol['MATCH_MANUAL']) : [];


    $descriptionProtocols = !empty($сurrentDataProtocol['DESCRIPTION']) ? $сurrentDataProtocol['DESCRIPTION'] : '';

    $probeProtocols = !empty($сurrentDataProtocol['PROBE']) ? unserialize($сurrentDataProtocol['PROBE']) : '';

    $description = $descriptionProtocols ? $descriptionProtocols : $descriptionBaTz;
	
	$objectProtocols = !empty($сurrentDataProtocol['OBJECT']) ? $сurrentDataProtocol['OBJECT'] : '';

    $object = $objectProtocols ? $objectProtocols : $objectBaTz;

	$anchorSampleProtocols = !empty($сurrentDataProtocol['ANCHOR_SAMPLE']) ? $сurrentDataProtocol['ANCHOR_SAMPLE'] : 0;
    $numberProtocol = !empty($сurrentDataProtocol['NUMBER']) ? $сurrentDataProtocol['NUMBER'] : 0;

    $temperatureBefore = !empty($сurrentDataProtocol['TEMPERATURE_BEFORE']) ? json_decode($сurrentDataProtocol['TEMPERATURE_BEFORE'], JSON_FORCE_OBJECT) : [];
    $temperatureAfter = !empty($сurrentDataProtocol['TEMPERATURE_AFTER']) ? json_decode($сurrentDataProtocol['TEMPERATURE_AFTER'], JSON_FORCE_OBJECT) : [];
    $humidityBefore = !empty($сurrentDataProtocol['HUMIDITY_BEFORE']) ? json_decode($сurrentDataProtocol['HUMIDITY_BEFORE'], JSON_FORCE_OBJECT) : [];
    $humidityAfter = !empty($сurrentDataProtocol['HUMIDITY_AFTER']) ? json_decode($сurrentDataProtocol['HUMIDITY_AFTER'], JSON_FORCE_OBJECT) : [];
    $temperatureHumidityCheck = !empty($сurrentDataProtocol['TEMPERATURE_HUMIDITY_CHEK']) ? json_decode($сurrentDataProtocol['TEMPERATURE_HUMIDITY_CHEK'], JSON_FORCE_OBJECT) : [];

    $resFrost = $DB->Query("SELECT * FROM `b_frost` WHERE `tz_id`=" . $idTZ . " AND `protocol_id`=" . $currentProtocolId);
    $sqlFrost = $resFrost->Fetch();

    $deal2 = CCrmDeal::GetByID($deal_id);


    $protocolDataWithoutNumber = null;
    if (!empty($idTZ)) {

        $protocolDataWithoutNumber = $db->selectRow("SELECT * FROM `PROTOCOLS` WHERE `NUMBER` IS NULL AND `ID_TZ`={?}", [$idTZ]);
    }

    $protocolIdWithoutNumber = !empty($protocolDataWithoutNumber['ID']) ? (int)$protocolDataWithoutNumber['ID'] : 0;


    $protocolType = isset($сurrentDataProtocol['PROTOCOL_TYPE']) ? (int)$сurrentDataProtocol['PROTOCOL_TYPE'] : null;
    $materialGroup = isset($сurrentDataProtocol['GROUP_MAT']) ? htmlspecialchars($сurrentDataProtocol['GROUP_MAT']) : '';
    $noCompliance = isset($сurrentDataProtocol['NO_COMPLIANCE']) ? (int)$сurrentDataProtocol['NO_COMPLIANCE'] : null;
    // $selectionType = isset($сurrentDataProtocol['SELECTION_TYPE']) ? (int)$сurrentDataProtocol['SELECTION_TYPE'] : null;
    $noContract = isset($сurrentDataProtocol['NO_CONTRACT']) ? (int)$сurrentDataProtocol['NO_CONTRACT'] : null;
    $temperatureFrom = isset($сurrentDataProtocol['TEMP_O']) ? (float)$сurrentDataProtocol['TEMP_O'] : null;
    $temperatureTo = isset($сurrentDataProtocol['TEMP_TO_O']) ? (float)$сurrentDataProtocol['TEMP_TO_O'] : null;
    $humidityFrom = isset($сurrentDataProtocol['VLAG_O']) ? (float)$сurrentDataProtocol['VLAG_O'] : null;
    $humidityTo = isset($сurrentDataProtocol['VLAG_TO_O']) ? (float)$сurrentDataProtocol['VLAG_TO_O'] : null;

    $dateBegin = !empty($сurrentDataProtocol['DATE_BEGIN']) ? dateObjectFormat($сurrentDataProtocol['DATE_BEGIN'], "Y-m-d") : null;

    $dateEnd = !empty($сurrentDataProtocol['DATE_END']) ? dateObjectFormat($сurrentDataProtocol['DATE_END'], "Y-m-d") : null;

    $additionalInformation = !empty($сurrentDataProtocol['DOP_INFO']) ? htmlspecialchars($сurrentDataProtocol['DOP_INFO']) : '';
    $currentProtocolNumber = !empty($сurrentDataProtocol['NUMBER']) ? (int)$сurrentDataProtocol['NUMBER'] : null;

    $currentProtocolDate = !empty($сurrentDataProtocol['DATE']) ? dateObjectFormat($сurrentDataProtocol['DATE'], "Y-m-d") : null;
    $currentProtocolPdfName = !empty($сurrentDataProtocol['PDF']) ? $сurrentDataProtocol['PDF'] : null;

    $verifyProtocol = !empty($сurrentDataProtocol['VERIFY']) ? unserialize($сurrentDataProtocol['VERIFY']) : '';
    $protocolOutsideLis = isset($сurrentDataProtocol['PROTOCOL_OUTSIDE_LIS']) ? (int)$сurrentDataProtocol['PROTOCOL_OUTSIDE_LIS'] : null;
    $attestatInProtocol = isset($сurrentDataProtocol['ATTESTAT_IN_PROTOCOL']) ? (int)$сurrentDataProtocol['ATTESTAT_IN_PROTOCOL'] : null;
    $ostatki = !empty($сurrentDataProtocol['ostatki']) ? unserialize($сurrentDataProtocol['ostatki']) : [];
    $sostav = !empty($сurrentDataProtocol['SOSTAV']) ? unserialize($сurrentDataProtocol['SOSTAV']) : [];

	$editResultsCurrentProtocol = isset($сurrentDataProtocol['EDIT_RESULTS']) ? (int)$сurrentDataProtocol['EDIT_RESULTS'] : 0;
    $arrTemperature = $arrHumidity = $arrTemperatureBefore = $arrTemperatureAfter = $arrHumidityBefore = $arrHumidityAfter = [];

    $resultProtocol = null;
    if (!empty($idTZ)) {

        $resultProtocol = $db->select("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}", [$idTZ]);
    }

    $countResultProtocol = !empty($resultProtocol) ? count($resultProtocol) : 0;


    $allProbe = [];
    if (!empty($resultProtocol)) {

        $countProbe = 0;

        foreach ($resultProtocol as $kyeResultProtocol => $rowDataProtocol) {


            $probeChecked = !empty($rowDataProtocol['PROBE']) ? unserialize($rowDataProtocol['PROBE']) : '';

            $protocolNumber = !empty($rowDataProtocol['NUMBER']) ? $rowDataProtocol['NUMBER'] : null;

            $currentIdProtocol = !empty($rowDataProtocol['ID']) ? (int)$rowDataProtocol['ID'] : 0;

            $namePdf = !empty($rowDataProtocol['PDF']) ? $rowDataProtocol['PDF'] : '';


            $currentProtocolOutsideLis = !empty($rowDataProtocol['PROTOCOL_OUTSIDE_LIS']) ? (int)$rowDataProtocol['PROTOCOL_OUTSIDE_LIS'] : null;


            if (empty($probeChecked)) {
                continue;
            }


            if ($countProbe > 0) {
                foreach ($probeChecked as $keyProbeChecked => $valProbeChecked) {


                    if (!empty($allProbe[$keyProbeChecked])) {
                        foreach ($valProbeChecked as $keyProbe => $valProbe) {
                            switch ($keyProbe) {
                                case 'number_probe':
                                    $arrayPreviousNumberProbe = !empty($allProbe[$keyProbeChecked]['number_probe']) ? $allProbe[$keyProbeChecked]['number_probe'] : '';

                                    $allNumberProbe = $valProbe + $arrayPreviousNumberProbe;


                                    $allProbe[$keyProbeChecked]['number_probe'] = $allNumberProbe;


                                    foreach ($valProbe as $key => $val) {


                                        if (isset($allProbe) && isset($keyAllProbe) && isset($key)) {
                                            $allProbe = addSampleData($allProbe, $keyProbeChecked, $key, $protocolNumber, $currentIdProtocol, $namePdf, $currentProtocolOutsideLis);
                                        }
                                    }

                                    break;
                                case 'sh_number':
                                    $arrayPreviousShProbe = !empty($allProbe[$keyProbeChecked]['sh_number']) ? $allProbe[$keyProbeChecked]['sh_number'] : '';

                                    $allShProbe = $valProbe + $arrayPreviousShProbe;

                                    $allProbe[$keyProbeChecked]['sh_number'] = $allShProbe;

                                    break;
                            }

                        }
                    } else {

                        $allProbe[$keyProbeChecked] = $valProbeChecked;

                        foreach ($valProbeChecked as $keyProbe => $valProbe) {
                            switch ($keyProbe) {
                                case 'number_probe':

                                    foreach ($valProbe as $key => $val) {

                                        if (isset($allProbe) && isset($keyAllProbe) && isset($key)) {
                                            $allProbe = addSampleData($allProbe, $keyProbeChecked, $key, $protocolNumber, $currentIdProtocol, $namePdf, $currentProtocolOutsideLis);
                                        }

                                    }

                                    break;
                            }
                        }

                    }
                }
            } else {

                $allProbe = $probeChecked;


                foreach ($allProbe as $keyAllProbe => $valAllProbe) {
                    foreach ($valAllProbe as $keyProbe => $valProbe) {
                        switch ($keyProbe) {
                            case 'number_probe':
                                foreach ($valProbe as $key => $val) {

                                    if (isset($allProbe) && isset($keyAllProbe) && isset($key)) {
                                        $allProbe = addSampleData($allProbe, $keyAllProbe, $key, $protocolNumber, $currentIdProtocol, $namePdf, $currentProtocolOutsideLis);
                                    }
                                }
                                break;
                        }

                    }
                }

            }

            $countProbe++;

        }
    }


    ?>
    <form method="post" id="result" enctype="multipart/form-data">

        <div>
            <div class='return disable ' title='Вернуться назад'></div>
            <div class='edit disable' title='Редактировать'></div>
            <a href="/requests_test.php">
                <div class='list' title='Вернуться к списку'></div>
            </a>
            <a href="/request_card.php?ID=<?= $deal2['ID'] ?>">
                <div class='card' title='Карточка заявки'></div>
            </a>


            <div id="protDownload" class='download disable'>
            </div>

            <div class='copy disable' title='Скопировать'></div>
            <div class='remove disable' title='Удалить'></div>
            <div class='add disable' title='Добавить'></div>
            <div class='report disable' title='Отчёт'></div>
            <div class='further disable' title='Далее'></div>
        </div>
        <br/>

        <div class="message-errors" style="display: <?= empty($saveMessageErrors) ? 'none' : 'block' ?>">
            <span><?= $saveMessageErrors ?></span>
        </div>
        <?php


        $status = '';
        if (($deal2['STAGE_ID'] == '2' || $deal2['STAGE_ID'] == '3' || $deal2['STAGE_ID'] == '4' || $deal2['STAGE_ID'] == 'WON')) {
            $status = 'Выдан';
        } elseif ($deal2['TYPE_ID'] == 'COMPLEX') { 
            $status = 'OCK';
        } else {
            $status = 'Не выдан';
        }
        ?> Статус: <label><?= $status ?></label><input id="status" type="hidden" value="<?= $status ?>"> | Архивный

        <input type="checkbox" name="ACTUAL_FOR_OBJECT" class="inputTypeCheckbox"
               <?= ($sql['ACTUAL_FOR_OBJECT']) ? 'checked' : '' ?>>
        <br/><br/>

        <table id="results_isp2" class="table-results" style="border: 1px solid #ccc" valign="top">
            <thead>
            <th>Материал</th>
            <th>Опред. харак-ки</th>
            <th>Ед. изм.</th>
            <th>ТУ</th>
            <th>Нормат. значение</th>
            <th>Методика испытаний</th>
            <?= (!1) ? '<th>Температура</th>' : '' ?>
            <?= (!1) ? '<th>Влажность</th>' : '' ?>
            <th>Фактическое значение</th>
            <th>Среднее значение</th>
            <th>Соответствие треб.</th>
            <th>В ОА?</th>
            <th>Диапазон из ОА</th>
            <th>
                Выбор пробы <input type="checkbox" class="all-checkbox-proba inputTypeCheckbox">
            </th>
            <th>Номер протокола</th>
            <th>Протокол выдан вне ЛИС</th>
            <?php if ($deal_id >= getIdDealAfterWhichOborudConnectedGost()): ?>
            <th>Температура / Влажность</th>
            <?php endif; ?>
            </thead>
            <?php
            $selectedGostId = [];
            $arr = unserialize($sql['TZ']);
            $probe = !empty($sql['PROBE']) ? unserialize($sql['PROBE']) : [];
            $norm_d = !empty($sql['NORM_DESC']) ? unserialize($sql['NORM_DESC']) : [];

            $control_damage = $sqlFrost['control_damage'];
            $main_damage = $sqlFrost['main_damage'];
            $control_mass = $sqlFrost['control_mass'];

            $main_mass = number_format($sqlFrost['main_mass'], 2);
            $control_strength = unserialize($sqlFrost['control_strength']);
            $main_strength = unserialize($sqlFrost['main_strength']);

            $control_medium = number_format($sqlFrost['control_medium'], 1);

            $main_medium = number_format($sqlFrost['main_medium'], 1);

            $control_bottom_line = number_format($sqlFrost['control_bottom_line'], 2);

            $main_bottom_line = number_format($sqlFrost['main_bottom_line'], 2);
            $ratio = $sqlFrost['ratio'];


            $insert_str = !empty($sql['INSERT_STR']) ? $sql['INSERT_STR'] : '';
            $sum_str = !empty($sql['SUM_STR']) ? $sql['SUM_STR'] : '';

            $data = [];
            foreach ($arr as $arrDatum) {

                $data[] = $arrDatum['mater'];
            }
            $a = array_count_values($data);

            foreach ($arr as $key => $arrDatum) {
                $i = $a[$arrDatum['mater']];
                foreach ($probe[$key]['number_probe'] as $key_probe => $probe_k) {

                    $key_probe = $key_probe + 1;
                    $j = 0;

                    $enlarge = 0;


                    $currentProtocolIdForSample = !empty($allProbe[$key]['current_id_protocol'][$key_probe - 1]) ? (int)$allProbe[$key]['current_id_protocol'][$key_probe - 1] : 0;


                    $currentProtocolDataForSample = null;

                    if (!empty($currentProtocolIdForSample)) {
                        $queryProtocolDataForSample = "SELECT * FROM `PROTOCOLS` WHERE ID={?}";
                        $arrayProtocolDataForSample = [$currentProtocolIdForSample];
                        $currentProtocolDataForSample = $db->selectRow($queryProtocolDataForSample, $arrayProtocolDataForSample);
                    }


                    $materialGroupForSample = isset($currentProtocolDataForSample['GROUP_MAT']) ? $currentProtocolDataForSample['GROUP_MAT'] : '';

					$idProtocolCurrent = !empty($currentProtocolDataForSample['ID']) ? $currentProtocolDataForSample['ID'] : '';
                    $anchorSampleCurrentProtocols = !empty($currentProtocolDataForSample['ANCHOR_SAMPLE']) ? $currentProtocolDataForSample['ANCHOR_SAMPLE'] : '';

					$currentEditResults = !empty($currentProtocolDataForSample['EDIT_RESULTS']) ? $currentProtocolDataForSample['EDIT_RESULTS'] : '';

                    foreach ($arrDatum['gost'] as $k => $item) {

                        $queryBaGostMethods = "SELECT * FROM `ba_gost` WHERE ID={?}";
                        $arrDataBaGost = [$item];
                        $baGostMethods = $db->selectRow($queryBaGostMethods, $arrDataBaGost);


                        $queryBaGostСonditions = "SELECT * FROM `ba_gost` WHERE ID={?}";
                        $arrDataBaGost = [$arrDatum['gost_new'][$j]];
                        $baGostСonditions = $db->selectRow($queryBaGostСonditions, $arrDataBaGost);


                        if ($baGostMethods['GOST_TYPE'] === 'metodic_otbor') {
                            $enlarge++;
                        }

                        if ($baGostMethods['GOST_TYPE'] != 'metodic_otbor') {
                            if ($item) {
                                $selectedGostId[] = $item;
                            }
                            ?>
                            <tr valign="top"
                                class="tr-table-results <?= !empty($allProbe[$key]['protocol_number'][$key_probe - 1]) ? 'trGrey' : '' ?> <?= (!empty($allProbe[$key]['number_probe'][$key_probe - 1]) && !empty($currentProtocolId) && !empty($idProtocolCurrent) && $currentProtocolId == $idProtocolCurrent) ? 'activ' : '' ?>">

                                <td style="font-weight:bold">

                                    <?= $probe_k . " " . $probe[$key]['sh_number'][$key_probe - 1] ?>
                                </td>

                                <td>
                                    <?php

                                    echo $baGostMethods['SPECIFICATION'];
                                    ?>
                                </td>

                                <td>
                                    <?php

                                    echo $baGostMethods['ED'] . ($baGostMethods['ED_INDEX'] ? $baGostMethods['ED_INDEX'] : '');

                                    ?>
                                </td>

                                <td>
                                    <?php

                                    echo $baGostСonditions['GOST'] . ' ' . $baGostСonditions['SPECIFICATION'];

                                    ?>
                                </td>

                                <td>
                                    <?php

                                    //$attributeDisabled = !empty($allProbe[$key]['protocol_number'][$key_probe - 1]) ? 'readonly' : '';
									$attributeDisabled = (!empty($allProbe[$key]['protocol_number'][$key_probe - 1]) && empty($currentEditResults)) ? 'readonly' : '';


                                    $norm_dop = !empty($baGostСonditions['NORM_DOP']) ? unserialize($baGostСonditions['NORM_DOP']) : [];

                                    $val_dop = !empty($baGostСonditions['VALUE_DOP']) ? unserialize($baGostСonditions['VALUE_DOP']) : [];


                                    if ($baGostСonditions['GOST_TYPE'] == 'TU_group') {

                                        $gr_mat = explode('-', $materialGroupForSample);

                                        $r = $gr_mat[1];



                                        $norm_descript = 'Oт ' . $norm_dop[$r][0] . ' до ' . $norm_dop[$r][1];
                                        echo $norm_descript;
                                        echo '<input type="hidden" name="norm_description[' . $k . ']" value = "' . $norm_descript . '">';

                                    } else {
                                        $key_m = (int)$key - 1;

                                        if ($baGostСonditions['GOST_TYPE'] == 'TU_research') {

                                            $norm_descript = '';

                                            if (count($arr) > 1) echo '<input class="res" type="text" name="norm_description[' . $key_m . '][' . $key_probe . '][' . $k . ']" value = "' . $norm_d[$key_m][$key_probe][$k] . '" ' . $attributeDisabled . '>';
                                            else echo '<input class="res" type="text" name="norm_description[' . $key_probe . '][' . $k . ']" value = "' . $norm_d[$key_probe][$k] . '" ' . $attributeDisabled . '>';

                                        } else {
                                            echo $baGostСonditions['NORM_COMMENT'];
                                            if (count($arr) > 1) echo '<input type="hidden" name="norm_description[' . $key_m . '][' . $key_probe . '][' . $k . ']" value = "' . $baGostСonditions['NORM_COMMENT'] . '" ' . $attributeDisabled . '>';
                                            else echo '<input type="hidden" name="norm_description[' . $key_probe . '][' . $k . ']" value = "' . $baGostСonditions['NORM_COMMENT'] . '" ' . $attributeDisabled . '>';
                                        }
                                    }

                                    ?>
                                </td>

                                <td>
                                    <?php


                                    echo "<a href='/obl_acc.php?ID=" . $baGostMethods['ID'] . "' target='_blank'>"
                                        . ($baGostMethods['GOST'] ? $baGostMethods['GOST'] . ' ' . $baGostMethods['GOST_PUNKT'] : '-') . "</a>";

                                    ?>
                                </td>

                                <td>

                                    <?php


                                    $norm_text = (!empty($baGostMethods['RES_TEXT']) ? (int)$baGostMethods['RES_TEXT'] : 0);

                                    $currentActualValues = (!empty($protocolActualValues[$key - 1][$k][$key_probe][0]) ? $protocolActualValues[$key - 1][$k][$key_probe][0] : '');

                                    $arrayCurrentActualValues = (!empty($protocolActualValues[$key - 1][$k][$key_probe]) ? $protocolActualValues[$key - 1][$k][$key_probe] : []);


                                    $gost_n1 = $baGostMethods['NORM_TEXT'] ? $baGostMethods['NORM1_TEXT'] : $baGostMethods['NORM1'];

                                    $gost_n2 = $baGostMethods['NORM_TEXT'] ? $baGostMethods['NORM2_TEXT'] : $baGostMethods['NORM2'];
                                    $gost_in_out = $baGostMethods['IN_OUT'];

                                    $actualValue  = !empty($results[$key - 1][$k][$key_probe][0]) ? str_replace(',', '.', $results[$key - 1][$k][$key_probe][0]) : 0;



                                    $outRange = '';


                                    if (!empty($baGostMethods['LFHI']) && is_numeric($actualValue) && !empty($actualValue)) {


                                        if($gost_in_out==1)
                                        {

                                            if($actualValue < $gost_n1) { 
                                                $outRange = 'менее ' . $gost_n1;
                                            } elseif($actualValue > $gost_n2) { 
                                                $outRange = 'болee ' . $gost_n2;
                                            }
                                        } else {

                                            if($actualValue > $gost_n1) { 
                                                $outRange = 'более ' . $gost_n1;
                                            } elseif($actualValue < $gost_n2) { 
                                                $outRange = 'менее ' . $gost_n2;
                                            }
                                        }
                                    }

                                    if (!empty($outRange)) {
                                        $arrayOutRange[] = $outRange;
                                    }


                                    ?>

                                    <div style="display:flex; padding-left:5px">
                                        <?php

                                        if (!$outRange && is_numeric($actualValue)) {


                                            ?>
                                            <input class="res" <?= ($norm_text) ? 'type="text"' : 'type="number" step="0.0001"' ?>
                                                   name="results[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][0]"
                                                   value="<?= $results[$key - 1][$k][$key_probe][0] ?>"
                                                   <?= !empty(($allProbe[$key]['protocol_number'][$key_probe - 1]) && empty($currentEditResults)) ? 'readonly' : '' ?>>
                                        <?php } else {

                                            $actualValue  = !empty($actualValue) ? str_replace('.', ',', $actualValue) : 0;
                                            $outRange  = !empty($outRange) ? str_replace('.', ',', $outRange) : 0;
                                            ?>
                                            <input class="res" type="text"
                                                   name="results[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][0]"
                                                   value="<?= $outRange ? $outRange : $actualValue ?>"
                                                   <?= (!empty($allProbe[$key]['protocol_number'][$key_probe - 1]) && empty($currentEditResults)) ? 'readonly' : '' ?>>
                                        <?php }?>




                                        <input style="margin-left:5px" type="button" class="add" value="+" data-keys="<?= $key - 1 ?>-<?= $k ?>-<?= $key_probe ?>"
                                            <?= (!empty($allProbe[$key]['protocol_number'][$key_probe - 1]) && empty($currentEditResults)) ? 'disabled' : '' ?>></div>
                                    <? foreach ($results[$key - 1][$k][$key_probe] as $kk => $res): ?>
                                        <? if ($kk === 0) continue; ?>
                                        <div style="display:flex">
                                            <input class="res" <?= ($norm_text) ? 'type="text"' : 'type="number" step="0.0001"' ?> style="margin-left:5px"
                                                   name="results[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][<?= $kk ?>]"
                                                   value="<?= $res ?>"
                                                   <?= (!empty($allProbe[$key]['protocol_number'][$key_probe - 1]) && empty($currentEditResults)) ? 'readonly' : '' ?>>
                                            <input style="margin-left:5px" type="button" value="-" onclick="$(this).parent().remove();"
                                                <?= (!empty($allProbe[$key]['protocol_number'][$key_probe - 1]) && empty($currentEditResults)) ? 'disabled' : '' ?>></div>
                                    <? endforeach; ?>



                                </td>

                                <td>
                                    <?php

                                    $gost_type = isset($baGostMethods['GOST_TYPE']) ? $baGostMethods['GOST_TYPE'] : '';
                                    $accuracy = isset($baGostMethods['ACCURACY']) ? (int)$baGostMethods['ACCURACY'] : 0;

                                    $sred_null = "-";

                                    if ($gost_type == "TU_sred5" || $gost_type == "TU_sred4" || $gost_type == "TU_sred3" || $gost_type == "TU_sred2" || $gost_type == "TU_sred") {

                                        echo '<label>' . (is_nan($allSredBaTz[$key - 1][$k][$key_probe]) ? $sred_null : number_format($allSredBaTz[$key - 1][$k][$key_probe], $accuracy, ',', '')) . '</label>';
                                    } else echo '<label>Не расcчитывается</label>';


                                    ?>
                                </td>

                                <td>
                                    <?php
									pre($baGostMethods['IN_OUT']);
									pre($baGostMethods['NORM1']);
									pre($baGostMethods['NORM2']);
									pre($baGostMethods['NORM_TEXT']);
									pre($allSredBaTz[$key - 1][$k][$key_probe]);
                                    if (!$baGostMethods['NORM_TEXT'] && $allSredBaTz[$key - 1][$k][$key_probe] !== '') {


                                        if (($allSredBaTz[$key - 1][$k][$key_probe] < $baGostMethods['NORM1'] || $allSredBaTz[$key - 1][$k][$key_probe] > $baGostMethods['NORM2']) && ($baGostMethods['IN_OUT'])) {

                                           pre('djfgkf');
                                            echo "Внимание! Значение вне области!";
                                            if ($baGostMethods['IN_OA']) $no_oa = 1;

                                        } elseif (($allSredBaTz[$key - 1][$k][$key_probe] > $baGostMethods['NORM1'] || $allSredBaTz[$key - 1][$k][$key_probe] < $baGostMethods['NORM2']) && (!$baGostMethods['IN_OUT'])) {



                                            echo "Внимание! Значение вне области!";
                                            if ($baGostMethods['IN_OA']) $no_oa = 1;
                                        }
                                    }


                                    if ($baGostСonditions) {
                                        if ($baGostСonditions['MATCH_MANUAL']) {

                                            ?>
                                            <label>

                                                <select class="res"
                                                        name="match[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>]"
                                                        <?= (!empty($allProbe[$key]['protocol_number'][$key_probe - 1]) && empty($currentEditResults)) ? 'readonly' : '' ?>>
                                                    <option value="0"
                                                            <?= ($allMatchManualBaTz[$key - 1][$k][$key_probe] == 0) ? 'selected' : '' ?>>
                                                        Не соответствует
                                                    </option>
                                                    <option value="1"
                                                            <?= ($allMatchManualBaTz[$key - 1][$k][$key_probe] == 1) ? 'selected' : '' ?>>
                                                        Соответствует
                                                    </option>
                                                    <option value="2"
                                                            <?= ($allMatchManualBaTz[$key - 1][$k][$key_probe] == 2) ? 'selected' : '' ?>>
                                                        -
                                                    </option>
                                                    <option value="3"
                                                            <?= ($allMatchManualBaTz[$key - 1][$k][$key_probe] == 3) ? 'selected' : '' ?>>
                                                        Не нормируется
                                                    </option>
                                                </select>
                                            </label>
                                            <?php
                                            //   }
                                        } else {

                                            switch ($allMatchManualBaTz[$key - 1][$k][$key_probe]) {
                                                case 0:
                                                    echo "Не соответствует";
                                                    break;
                                                case 1:
                                                    echo "Cоответствует";
                                                    break;
                                                case 2:
                                                    echo "-";
                                                    break;
                                            }
                                        }
                                    }

                                    ?>
                                </td>

                                <td>
                                    <?php


                                    if ($baGostMethods['IN_OA']) {
                                        echo "+";
                                    } else {
                                        echo "-";
                                    }

                                    ?>
                                </td>

                                <td>
                                    <?php

                                    $gost_n1 = $baGostMethods['NORM_TEXT'] ? $baGostMethods['NORM1_TEXT'] : $baGostMethods['NORM1'];
                                    $gost_n2 = $baGostMethods['NORM_TEXT'] ? $baGostMethods['NORM2_TEXT'] : $baGostMethods['NORM2'];
                                    $gost_in_out = $baGostMethods['IN_OUT'];



                                    if($gost_in_out==1)
                                    {
                                        echo "<label> от ".($gost_n1 ? $gost_n1  : '-')." до ".($gost_n2 ? $gost_n2  : '-')."</label>";
                                    }

                                    else echo "<label> до ".($gost_n1 ? $gost_n1  : '-') ." и от ".($gost_n2 ? $gost_n2  : '-')."</label>";


                                    ?>
                                </td>

                                <td>

                                    <?php



                                    if (isset($k) && $k - $enlarge == 0) {
                                        ?>
                                        <input type="checkbox"
                                               name="checkboxProba[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][<?= $kk ?>]"
                                               class="checkbox-proba inputTypeCheckbox"
                                               <?= !empty($allProbe[$key]['number_probe'][$key_probe - 1]) ? 'checked' : '' ?>
                                               <?= (!empty($allProbe[$key]['number_probe'][$key_probe - 1]) && !empty($allProbe[$key]['protocol_number'][$key_probe - 1])) || !empty($anchorSampleCurrentProtocols) ? 'disabled' : '' ?>>

                                        <?php
                                    }
                                    ?>
                                </td>
                                <td>

                                    <?php

                                    if (isset($k) && $k - $enlarge == 0) {
                                        ?>
                                        <input type="number"
                                               name="numberProtocol[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][<?= $kk ?>]"
                                               class="number-protocol"
                                               value="<?= !empty($allProbe[$key]['protocol_number'][$key_probe - 1]) ? $allProbe[$key]['protocol_number'][$key_probe - 1] : '' ?>"
                                               readonly>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td class="td-protocol-outside-lis">
                                    <?php



                                    if (isset($k) && $k - $enlarge == 0) {
                                        ?>
                                        <input type="checkbox" name="checked_protocol_outside_lis"
                                               class="checkedProtocolOutsideLis inputTypeCheckbox"
                                               <?= !empty($allProbe[$key]['protocol_outside_lis'][$key_probe - 1]) ? 'checked' : '' ?>
                                               disabled>
                                        <span class="file-pdf">
							<?php if (!empty($allProbe[$key]['current_id_protocol'][$key_probe - 1]) && !empty($allProbe[$key]['name_pdf'][$key_probe - 1])) : ?>
                                <a href='/pdf/<?= $allProbe[$key]['current_id_protocol'][$key_probe - 1] ?>/<?= $allProbe[$key]['name_pdf'][$key_probe - 1] ?>'
                                   target="_blank"><?= $allProbe[$key]['name_pdf'][$key_probe - 1] ?></a>
                            <?php endif; ?>
						</span>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <?php if ($deal_id >= getIdDealAfterWhichOborudConnectedGost()) { ?>
                                <td>
                                    <?php
                                    $currentTemperatureBefore = isset($temperatureBefore[$key - 1][$k][$key_probe][0]) && !empty($temperatureHumidityCheck[$key - 1][$k][$key_probe][0]) ? $temperatureBefore[$key - 1][$k][$key_probe][0] : $temperatureFrom;
                                    $currentTemperatureAfter = isset($temperatureAfter[$key - 1][$k][$key_probe][0]) && !empty($temperatureHumidityCheck[$key - 1][$k][$key_probe][0]) ? $temperatureAfter[$key - 1][$k][$key_probe][0] : $temperatureTo;
                                    $currentHumidityBefore = isset($humidityBefore[$key - 1][$k][$key_probe][0]) && !empty($temperatureHumidityCheck[$key - 1][$k][$key_probe][0]) ? $humidityBefore[$key - 1][$k][$key_probe][0] : $humidityFrom;
                                    $currentHumidityAfter = isset($humidityAfter[$key - 1][$k][$key_probe][0]) && !empty($temperatureHumidityCheck[$key - 1][$k][$key_probe][0]) ? $humidityAfter[$key - 1][$k][$key_probe][0] : $humidityTo;

                                    if (!empty($temperatureHumidityCheck[$key - 1][$k][$key_probe][0]) && !empty($allProbe[$key]['number_probe'][$key_probe - 1])) {

                                        $arrTemperature[] = $currentTemperatureBefore == $currentTemperatureAfter ? number_format($currentTemperatureBefore, 1, ',', '')." " : "(".number_format($currentTemperatureBefore, 1, ',', '')." - ".number_format($currentTemperatureAfter, 1, ',', '').") ";
                                        $arrHumidity[] = $currentHumidityBefore == $currentHumidityAfter ? number_format($currentHumidityBefore, 1, ',', '')." " : "(".number_format($currentHumidityBefore, 1, ',', '')." - ".number_format($currentHumidityAfter, 1, ',', '').") ";
                                    } else if(!empty($allProbe[$key]['number_probe'][$key_probe - 1])) {

                                        $arrTemperature[] = $temperatureFrom == $temperatureTo ? number_format($temperatureFrom, 1, ',', '')." " : "(".number_format($temperatureFrom, 1, ',', '')." - ".number_format($temperatureTo, 1, ',', '').") ";
                                        $arrHumidity[] = $humidityFrom == $humidityTo ? number_format($humidityFrom, 1, ',', '')." " : "(".number_format($humidityFrom, 1, ',', '')." - ".number_format($humidityTo, 1, ',', '').") ";
                                    }
                                    ?>
                                    <table class="table-temperature-humidity">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="wrapper-temperature-humidity">
                                                        <label>Температура от</label>
                                                        <input class="temperature-before number-temperature-humidity input-before" type="number" name="temperature_before[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][]" value="<?= isset($currentTemperatureBefore) ? number_format($currentTemperatureBefore, 1, '.', '') : '' ?>" step="0.1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="wrapper-temperature-humidity">
                                                        <label>Температура до</label>
                                                        <input class="temperature-after number-temperature-humidity input-after" type="number" name="temperature_after[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][]" value="<?= isset($currentTemperatureAfter) ? number_format($currentTemperatureAfter, 1, '.', '') : '' ?>" step="0.1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="wrapper-temperature-humidity">
                                                        <label>Влажность от</label>
                                                        <input class="humidity-before number-temperature-humidity input-before" type="number" name="humidity_before[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][]" value="<?= isset($currentHumidityBefore) ? number_format($currentHumidityBefore, 1, '.', '') : '' ?>" step="0.1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="wrapper-temperature-humidity">
                                                        <label>Влажность до</label>
                                                        <input class="humidity-after number-temperature-humidity input-after" type="number" name="humidity_after[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][]" value="<?= isset($currentHumidityAfter) ? number_format($currentHumidityAfter, 1, '.', '') : '' ?>" step="0.1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <input class="temperature-humidity-check checkbox-temperature-humidity" type="checkbox" name="temperature_humidity_check[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][]" <?= !empty($temperatureHumidityCheck[$key - 1][$k][$key_probe][0]) ? 'checked' : '' ?>>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <?php } ?>
                            </tr>

                            <?php
                        }                         
                        

                        $j++;
                    }
                    ?>
                    <tr style="height:10px; background-color:#3bc8f557">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <?php if ($deal_id >= getIdDealAfterWhichOborudConnectedGost()): ?>
                        <td></td>
                        <?php endif; ?>
                    </tr><?php

                }
            }

            ?>
        </table>
		
		<?php
        
            $dataOfGeneratedProtocols = null;
            if (!empty($idTZ)) {

                $queryOfGeneratedProtocols = "SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}";
                $queryDataOfGeneratedProtocols = [$idTZ];
                $dataOfGeneratedProtocols = $db->select($queryOfGeneratedProtocols, $queryDataOfGeneratedProtocols);

            }

            $countOfGeneratedProtocols = !empty($dataOfGeneratedProtocols) ? count($dataOfGeneratedProtocols) : 0;

            
            $flagDisabledLink = 0;
            foreach ($dataOfGeneratedProtocols as $dataOfProtocol) {

                
                if (empty($dataOfProtocol['ANCHOR_SAMPLE']) && empty($dataOfProtocol['NUMBER'])) {
                    $flagDisabledLink = 1;
                }
            }
		$log = date("Y.m.d G:i:s") . "\n";
		$log .= print_r($flagDisabledLink, 1). "\n";
		file_put_contents(getcwd() . '/creat_prot.txt', $log);

		$log = date("Y.m.d G:i:s") . "\n";
		$log .= print_r($countResultProtocol, 1). "\n";
		file_put_contents(getcwd() . '/creat_prot1.txt', $log);

        ?>

        <div class="wrapper-btn-protocol">
            
            <input type="submit" class="button btn-create-protocol" name="btn_create_protocol" value="Создать протокол"
                   <?= !empty($flagDisabledLink) || empty($countResultProtocol) ? 'disabled' : '' ?>>
           
            <input type="submit" class="button btn-anchor-sample" name="btn_anchor_sample" value="Закрепить пробу"
                   <?= empty($countResultProtocol) || !empty($numberProtocol) || !empty($anchorSampleProtocols) ? 'disabled' : '' ?>>
        </div>

        <?php

     
        if (!empty($countOfGeneratedProtocols)) {
			$conformityStage = in_array($stageIdBaTz, ['PREPARATION', 'PREPAYMENT', 'EXECUTING', 'FINAL_INVOICE', '1']);
            ?>
            <table class="protocol-table table-all" id="table_all">
                <caption>
                    <h2>Таблица созданных протоколов</h2>
                </caption>
                <thead>
                <th>Номер<br> протокола</th>
                <th>Дата протокола</th>
                <th>Протокол<br> выдан<br> вне ЛИС</th>
                <th>Загружена PDF-версия</th>
                <th>Скачать протокол</th>
                <th>Скачать протокол<br> без объединения ячеек</th>
                <th>Присвоить номер</th>
				<?php
					if (!empty($USER->GetID()) && (($USER->GetID() == '1') || ($USER->GetID() == '10') || ($USER->GetID() == '9') || ($USER->GetID() == '38') || ($USER->GetID() == '7') || ($USER->GetID() == '43') || ($USER->GetID() == '16') || ($USER->GetID() == '60') || ($USER->GetID() == '61'))) {
						echo "<th>Разблокировать</th>";
					}	
				?>
                </thead>
                <?php

                foreach ($dataOfGeneratedProtocols as $dataOfProtocol) {

                    $idСurrentProtocol = !empty($dataOfProtocol['ID']) ? (int)$dataOfProtocol['ID'] : 0;
                    $numberСurrentProtocol = !empty($dataOfProtocol['NUMBER']) ? (int)$dataOfProtocol['NUMBER'] : 'Номер не присвоен';

                    $dateСurrentProtocol = !empty($dataOfProtocol['DATE']) ? dateObjectFormat($dataOfProtocol['DATE'], "d.m.Y") : '';


                    $nameСurrentProtocol = !empty($dataOfProtocol['PDF']) ? $dataOfProtocol['PDF'] : '';

                    $protocolOutsideLIS = isset($dataOfProtocol['PROTOCOL_OUTSIDE_LIS']) ? (int)$dataOfProtocol['PROTOCOL_OUTSIDE_LIS'] : null;
                    $protocolActualVersion = isset($dataOfProtocol['ACTUAL_VERSION']) ? $dataOfProtocol['ACTUAL_VERSION'] : '';


                    $currentDateBegin = !empty($dataOfProtocol['DATE_BEGIN']) ? dateObjectFormat($dataOfProtocol['DATE_BEGIN'], "Y-m-d") : null; //Дата начала испытаний

					$editResults = isset($dataOfProtocol['EDIT_RESULTS']) ? (int)$dataOfProtocol['EDIT_RESULTS'] : 0;

					$currentLinkNumberProtocol = !empty($deal_id) && !empty($idСurrentProtocol) ?
                        '/results_isp_OLD.php?ID=' . $deal_id . '&ID_P=' . $idСurrentProtocol :
                        '';

                    ?>

                    <tr class="tr-protocol-data
						<?= (!empty($idСurrentProtocol) && !empty($currentProtocolId) && $idСurrentProtocol == $currentProtocolId) ?
                        'activ' : '' ?>">
                        <td class="td-number-protocol">
							<a href="<?= $currentLinkNumberProtocol ?>" class="link-number-protocol
								<?= empty($currentLinkNumberProtocol) ? 'disabledLink' : '' ?>">
                                <?= $numberСurrentProtocol ?>
                            </a>
                        </td>
                        <td><?= $dateСurrentProtocol ?></td>
                        <td>
                            <input type="checkbox" class="inputTypeCheckbox"
                                   <?= !empty($protocolOutsideLIS) ? 'checked' : '' ?> disabled>
                        </td>
                        <td>
                            <a href='/pdf/<?= $idСurrentProtocol ?>/<?= $nameСurrentProtocol ?>'
                               target="_blank"><?= $nameСurrentProtocol ?></a>
                        </td>

                        <td>
                            <?php
                            /*$urlToDownloadCurrentProtocol = (!empty($deal_id) && !empty($idTZ) && !empty($idСurrentProtocol)) ?
                                '/protocol_generator/protocol_binding_multiple_protocols.php?ID=' . $deal_id . '&TZ_ID=' . $idTZ . '&PROTOCOL_ID=' . $idСurrentProtocol :
                                '';*/

//							$urlToDownloadCurrentProtocol = (!empty($deal_id) && !empty($idTZ) && !empty($idСurrentProtocol)) ?
//                                '/protocol_generator/protocol_multiple_protocols.php?ID=' . $deal_id . '&TZ_ID=' . $idTZ . '&PROTOCOL_ID=' . $idСurrentProtocol :
//                                '';
//
//							if (!empty($currentDateBegin)) :
//                                ?>
<!--                                <a href="--><?//= $urlToDownloadCurrentProtocol ?><!--"-->
<!--                                   class="--><?//= ((!empty($deal_id) && !empty($idTZ) && !empty($idСurrentProtocol)) ? '' : 'disabledLink') ?><!--">Скачать-->
<!--                                    протокол</a>-->
							<?php //endif;
							$dir = 'protocol_generator/archive/' . $idTZ . date("Y", strtotime($dataOfProtocol['DATE_END'])) . '/' . $idСurrentProtocol . '/';
//							$dir = 'protocol_generator/archive/' . $idTZ . '2022/' . $idСurrentProtocol . '/';
							$skip = array('.', '..','signed.docx', 'forsign.docx','qrNEW.png');
							$files = scandir($dir);
							rsort($files);
							$arrayFilesPdf = glob($dir . '/*.pdf', GLOB_BRACE);
							$filesPdfName = !empty($arrayFilesPdf[0]) ? array_pop(explode(DIRECTORY_SEPARATOR, $arrayFilesPdf[0])) : '';
							$log = date("Y.m.d G:i:s") . "\n";
							$log .= print_r($dir, 1). "\n";
							file_put_contents(getcwd() . '/file.txt', $log);

							if (!in_array($files, $skip)) {
							if (!empty($dataOfProtocol['NUMBER'])) {
							$fileDocx = glob($dir . '*' . $dataOfProtocol['NUMBER'] . '*.docx');
							$filesDocxName = !empty($fileDocx[0]) ? array_pop(explode(DIRECTORY_SEPARATOR, $fileDocx[0])) : '';
							echo "<a href='$fileDocx[0]'>$filesDocxName</a>";
							} else {
							echo "<a href='$dir$files[0]'>$files[0]</a>";
							}
							}?>
                        </td>
                        <td>
                            <?php

                            $urlToDownloadCurrentProtocol = (!empty($deal_id) && !empty($idTZ) && !empty($idСurrentProtocol)) ?
                                '/protocol_generator/protocol_multiple_union.php?ID=' . $deal_id . '&TZ_ID=' . $idTZ . '&PROTOCOL_ID=' . $idСurrentProtocol :
                                '';

                            if (!empty($currentDateBegin)) :
                                ?>
                                <a href="<?= $urlToDownloadCurrentProtocol ?>"
                                   class="<?= ((!empty($deal_id) && !empty($idTZ) && !empty($idСurrentProtocol)) ? '' : 'disabledLink') ?>">Скачать
                                    протокол</a>
                            <?php endif; ?>
                        </td>
                        <td>
							<input type="submit" value="Присвоить номер протокола" name="assign_protocol_number"
                                   class="add-number-protocol button"
                                   data-id-protocol="<?= $idСurrentProtocol ?>"
                                   <?= !empty($dataOfProtocol['NUMBER']) || (isset($deal2['STAGE_ID']) && $deal2['STAGE_ID'] == '4') || (isset($deal2['STAGE_ID']) && $deal2['STAGE_ID'] == 'WON') || (!empty($_GET['ID_P']) && (int)$_GET['ID_P'] !== $idСurrentProtocol) ? 'disabled' : '' ?> onclick="window.open ('/protocol_generator/protocol_multiple_protocols.php?ID=<?= $_GET['ID'] ?>&TZ_ID=<?= $idTZ ?>&PROTOCOL_ID=<?=$idСurrentProtocol?>', '_blank')">
                        </td>
						<?php
							$paramEditResults = !empty($editResults) ? 0 : 1;
							
							$currentUrl = !empty($deal_id) && !empty($idСurrentProtocol) ?
								"'/results_isp_OLD.php?ID=" . $deal_id . "&ID_P=" . $idСurrentProtocol . "&EDIT_RESULTS=" . $paramEditResults . "'" :
									"";
									
							$atrrChecked = !empty($editResults) ? 'checked' : ''; 
							
							$attDisabled = (!empty($_GET['ID_P']) && (int)$_GET['ID_P'] !== $idСurrentProtocol) || empty($dataOfProtocol['NUMBER']) || !$conformityStage ? 'disabled' : '';
							
							if (!empty($USER->GetID()) && (($USER->GetID() == '1') || ($USER->GetID() == '9') || ($USER->GetID() == '10')|| ($USER->GetID() == '38')|| ($USER->GetID() == '7') || ($USER->GetID() == '43') || ($USER->GetID() == '16') || ($USER->GetID() == '60') || ($USER->GetID() == '61'))) {
								echo '<td><input type="checkbox" name="edit_results" class="edit-results" onclick="window.location.assign (' . $currentUrl .')"  ' . $attDisabled . ' ' . $atrrChecked . '></td>';
							}	
						?>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>


        <input type="hidden" name="ID" value="<?= $sql['ID']//(int)$output_array[1]     ?>">

        <input type="hidden" name="NO_OA" value="<?= $no_oa ?>">

        <fieldset class="fieldset-table" style="display:<?= !empty($countOfGeneratedProtocols) ? 'block' : 'none' ?>">
            <legend>
                <h2>Данные протокола №
                    <?= !empty($currentProtocolNumber) ? $currentProtocolNumber : 'Номер не присвоен' ?></h2>
            </legend>
            <table width="1000" id="results_isp2" class="condition-table">

                <tr>
                    <td width="250" height="35">
                        ТИП ПРОТОКОЛА
                    </td>
                    <td width="500">

                        <select name="sredType" id="sredType" onChange="Selected(this)"
                                <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'disabled' : '' ?>>
                            <option <?= ($protocolType === 0) ? 'selected' : '' ?> value='simple'>Стандартный</option>
                            <option <?= ($protocolType === 1) ? 'selected' : '' ?> value='allvalue'>Средние значения
                            </option>

                            <option <?= ($protocolType === 5) ? 'selected' : '' ?> value='zern'>Зерновой состав песка,
                                нерудных СМ
                            </option>
                            <option <?= ($protocolType === 21) ? 'selected' : '' ?> value='zern_№2'>Зерновой состав
                                песка,
                                нерудных СМ (только зерновой состав)
                            </option>
                            <option <?= ($protocolType === 6) ? 'selected' : '' ?> value='grunt'>Зерновой состав грунта
                            </option>
                            <option <?= ($protocolType === 22) ? 'selected' : '' ?> value='grunt_№2'>Зерновой состав
                                грунта
                                (только зерновой состав)
                            </option>
                            <option <?= ($protocolType === 7) ? 'selected' : '' ?> value='prirod'>Зерновой состав
                                природного
                                песка
                            </option>
                            <option <?= ($protocolType === 23) ? 'selected' : '' ?> value='prirod_№2'>Зерновой состав
                                природного песка (только зерновой состав)
                            </option>
                            <option <?= ($protocolType === 8) ? 'selected' : '' ?> value='tu_12801'>Зерновой состав ГОСТ
                                12801
                            </option>
                            <option <?= ($protocolType === 24) ? 'selected' : '' ?> value='tu_12801_№2'>Зерновой состав
                                ГОСТ
                                12801 (только зерновой состав)
                            </option>
                            <option <?= ($protocolType === 32) ? 'selected' : '' ?> value='gost31015'>Зерновой состав
                                ГОСТ
                                12801 (ГОСТ 31015)
                            </option>
                            <option <?= ($protocolType === 16) ? 'selected' : '' ?> value='tu_183_2'>Зерновой состав
                                ПНСТ
                                183
                            </option>
                            <option <?= ($protocolType === 25) ? 'selected' : '' ?> value='tu_183_2_№2'>Зерновой состав
                                ПНСТ
                                183 (только зерновой состав)
                            </option>
                            <option <?= ($protocolType === 9) ? 'selected' : '' ?> value='tu_183'>Зерновой состав ПНСТ
                                184
                            </option>
                            <option <?= ($protocolType === 26) ? 'selected' : '' ?> value='tu_183_№2'>Зерновой состав
                                ПНСТ
                                184 (только зерновой состав)
                            </option>
                            <option <?= ($protocolType === 19) ? 'selected' : '' ?> value='zern_sheb'>Зерновой состав
                                щебня
                            </option>
                            <option <?= ($protocolType === 27) ? 'selected' : '' ?> value='zern_sheb_№2'>Зерновой состав
                                щебня (только зерновой состав)
                            </option>
                            <option <?= ($protocolType === 10) ? 'selected' : '' ?> value='osk1'>ОСК щебень</option>
                            <option <?= ($protocolType === 11) ? 'selected' : '' ?> value='osk2'>ОСК дороги</option>
                            <option <?= ($protocolType === 12) ? 'selected' : '' ?> value='osk3'>ОСК ПНСТ 183, 184
                            </option>
                            <option <?= ($protocolType === 13) ? 'selected' : '' ?> value='osk4'>ОСК песок</option>
                            <option <?= ($protocolType === 14) ? 'selected' : '' ?> value='osk_sred'>ОСК средние
                                значения
                            </option>
                            <option <?= ($protocolType === 15) ? 'selected' : '' ?> value='shps'>Зерновой состав ЩПС
                            </option>
                            <option <?= ($protocolType === 28) ? 'selected' : '' ?> value='shps_№2'>Зерновой состав ЩПС
                                (только зерновой состав)
                            </option>
                            <option <?= ($protocolType === 17) ? 'selected' : '' ?> value='sheb'>Зерновой состав щебня
                                ОСК
                            </option>
                            <option <?= ($protocolType === 18) ? 'selected' : '' ?> value='sheb_shlak'>Зерновой состав
                                щебня
                                (ГОСТ 8267)
                            </option>
                            <option <?= ($protocolType === 29) ? 'selected' : '' ?> value='sheb_shlak_№2'>Зерновой
                                состав
                                щебня (ГОСТ 8267) (только зерновой состав)
                            </option>
							<option <?= ($protocolType === 41) ? 'selected' : '' ?> value='sheb_shlak_g3344'>Зерновой
								состав
								щебня (ГОСТ 3344)
							</option>
                            <option <?= ($protocolType === 20) ? 'selected' : '' ?> value='density_grunt'>ОСК режущее
                                кольцо
                            </option>
                            <option <?= ($protocolType === 30) ? 'selected' : '' ?> value='frost'>Морозостойкость
                            </option>
                            <option <?= ($protocolType === 31) ? 'selected' : '' ?> value='metric_method'>Зерновой
                                состав
                                грунта (ареометрическим метод)
                            </option>
                            <option <?= ($protocolType === 33) ? 'selected' : '' ?> value='shortcut'>Упрощенный</option>
                            <option <?= ($protocolType === 34) ? 'selected' : '' ?> value='shortcut_mean'>Упрощенный
                                среднее
                                значение
                            </option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td width="250" height="35">
                        ГРУППА МАТЕРИАЛА
                    </td>
                    <td><select class="tz" name="group_mat" <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'disabled' : '' ?>>
                            <option value="0" selected>Выбрать</option>
                            <?php
                            $select = $DB->Query("SELECT * FROM `MATERIALS`");
                            $select_groups = array();

                            $gr_mat = explode('-', $materialGroup);

                            while ($option = $select->Fetch()) {
                                $select_group = $DB->Query("SELECT `GROUPS` FROM `MATERIALS` WHERE `ID`=" . $option['ID'])->Fetch();
                                $select_groups = unserialize($select_group['GROUPS']);
                                echo "<option value='" . $option['ID'] . "' style='font-weight:bold' disabled>" . $option['NAME'] . "</option>";
                                foreach ($select_groups as $s => $group) {
                                    $opt_sel = '';
                                    if ($gr_mat[0] == $option['ID'] && $gr_mat[1] == $s) $opt_sel = 'selected';
                                    if ($group) echo "<option " . $opt_sel . " value='" . $option['ID'] . '-' . $s . "'> -- " . $group . "</option>";
                                }
                            }

                            ?>
                        </select></td>
                </tr>
                <tr>
                    <td height="35">
                        Не оценивать соответствие нормам
                    </td>
                    <td>

                        <input type="checkbox" name="NO_COMPLIANCE" class="inputTypeCheckbox"
                               <?= !empty($noCompliance) ? 'checked' : '' ?>
                               <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'disabled' : '' ?>>
                    </td>
                </tr>
    <!------------------------ 'Пробы отобраны не заказчиком' -------------------------->
<!-- переехало в тз -->
                <!-- <tr>
                    <td height="35">
                        Пробы отобраны не заказчиком
                    </td>
                    <td>

                        <input type="checkbox" name="SELECTION_TYPE" class="inputTypeCheckbox"
                               < ?= !empty($selectionType) ? 'checked' : '' ?>
                               < ?= !empty($currentProtocolNumber) ? 'disabled' : '' ?>>
                    </td>
                </tr> -->

                <tr>
                    <td height="35">
                        Указать акт ПП в качестве документа-основания
                    </td>
                    <td>

                        <input type="checkbox" name="NO_CONTRACT" class="inputTypeCheckbox"
                               <?= !empty($noContract) ? 'checked' : '' ?>
                               <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'disabled' : '' ?>>
                    </td>
                </tr>
                <tr>
                    <td height="35">
                        температура воздуха<br>относительная влажность
                    </td>
                    <td>
                        <?= implode('; ', array_unique($arrTemperature)) ?><br>
                        <?= implode('; ', array_unique($arrHumidity)) ?>
                    </td>
                </tr>
                <tr>
                    <td height="35">Температура</td>
                    <td>

                        От <input class="tz" id='quantity' type="number" name="TEMP_O" step="0.1"
                                  value="<?= !empty($temperatureFrom) ? number_format($temperatureFrom, 1, '.', '') : '' ?>"
                                  <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'readonly' : '' ?>>
                        - до <input class="tz" type="number" name="TEMP_TO_O" step="0.1"
                                    value="<?= !empty($temperatureTo) ? number_format($temperatureTo, 1, '.', '') : '' ?>"
                                    <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'readonly' : '' ?>>

                    </td>
                </tr>
                <tr>
                    <td height="35">Влажность</td>
                    <td>

                        От <input class="tz" type="number" name="VLAG_O" step="0.1"
                                  value="<?= !empty($humidityFrom) ? number_format($humidityFrom, 1, '.', '') : '' ?>"
                                  <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'readonly' : '' ?>>
                        - до <input class="tz" type="number" name="VLAG_TO_O" step="0.1"
                                    value="<?= !empty($humidityTo) ? number_format($humidityTo, 1, '.', '') : '' ?>"
                                    <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'readonly' : '' ?>>

                    </td>
                </tr>
                <tr>
                    <td height="35">
                        Дата начала испытаний
                    </td>
                    <td>
						<input type="date" name="DATE_BEGIN" value="<?= $dateBegin ?>" class="date-begin"
                               <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'readonly' : '' ?>>
                    </td>
                </tr>
                <tr>
                    <td height="35">
                        Дата окончания испытаний
                    </td>
                    <td>
                        <input type="date" name="DATE_END" value="<?= $dateEnd ?>" class="date-end"
                               <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'readonly' : '' ?>>
                    </td>
                </tr>
				<tr>
                    <td height="35">Объект строительства</td>
                    <td><input class="tz" multiple type="text" list="objects" name="OBJECT" size="64"
                               value='<?= $object ?>' <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'readonly' : '' ?>></td>
                </tr>
                <tr>
                    <td height="35">
                        Описание объекта испытаний
                    </td>
                    <td>
                        <textarea class="test_description" name="DESCRIPTION" style="width:496px" <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'readonly' : '' ?>><?= $description ?></textarea>
                    </td>
                </tr>
                <tr>
                    <?php
                    
					$arrPlaceDateProbe = [];
					foreach ($probeProtocols as $keyProbeProtocols => $valProbeProtocols) {
						$arrPlaceDateProbe = explode("; ", $valProbeProtocols['mesto_data']);
					}

                    $placeProbe = !empty($arrPlaceDateProbe[0]) ? $arrPlaceDateProbe[0] : $placeProbeBaTz;

                    $dateProbe = !empty($arrPlaceDateProbe[1]) ? date("Y-m-d", strtotime($arrPlaceDateProbe[1])) : $dateProbeBaTz;

                    ?>

                    <td width="250" height="40">Место отбора проб</td>
                    <td><input class="tz" type="text" name="PLACE_PROBE" value='<?= $placeProbe ?>' <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'readonly' : '' ?>></td>
                </tr>
                <tr>
                    <td width="250" height="40">Дата отбора проб</td>
                    <td>
						
						<input class="tz" type="date" name="DATE_PROBE"
                               value="<?= (isset($sql['ACT_NUM']) && $sql['ACT_NUM'] == 0 || empty($dateProbe) || (!empty($dateProbe) && $dateProbe == " Не указана")|| (!empty($dateProbe) && $dateProbe == "-") || (!empty($dateProbe) && $dateProbe == " -") || (!empty($dateProbe) && $dateProbe == "01.01.1970") || (!empty($dateProbe) && $dateProbe == "1970-01-01")) ? '' : date("Y-m-d", strtotime($dateProbe)) ?>"
                               size="20" <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'readonly' : '' ?>>
					</td>
                </tr>
                <tr>
                    <td height="35">
                        Дополнительная информация
                    </td>
                    <td>
                        <textarea id="dop_info" class="tz" name="DOP_INFO" size="60"
                                  <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'readonly' : '' ?>><?= $additionalInformation ?>
						</textarea>
                    </td>
                </tr>
                <tr>
                    <td height="35">
                        Номер протокола
                    </td>
                    <td id='yourNumber'>

                        <input class="tz" type="text" name="NUM_P"
                               value="<?= !empty($currentProtocolNumber) ? $currentProtocolNumber : 'номер не присвоен' ?>"
                               size="10"
                               <?= !empty($countOfGeneratedProtocols) ? 'required' : '' ?> readonly>
                    </td>
                </tr>

                <tr>
                    <td height="35">
                        Дата протокола
                    </td>
                    <td>

                        <input type="date" name="DATE_P_CORR"
                               value="<?= !empty($currentProtocolDate) ? $currentProtocolDate : date('Y-m-d') ?>"
                               size="10"
                               <?= !empty($countOfGeneratedProtocols) ? 'required' : '' ?> readonly>
                    </td>
                </tr>

                <tr>
                    <td>Добавить оборудование</td>
                    <td>
                        <?php
                        $strSelectedGostId = implode(', ', array_unique($selectedGostId));
                        ?>
                        <select id="first" class="oborud_change" style="max-width: 800px; margin-top:0px"
                                <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'disabled' : '' ?>>
                            <?php
                            if ($deal_id >= getIdDealAfterWhichOborudConnectedGost()) {
                                console_log('true4', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
                                //Все оборудование привязанное ко всем гостам из новой обасти
                                $res = $DB->Query("SELECT ba_oborud.ID, ba_oborud.TYPE_OBORUD, ba_oborud.OBJECT, ba_oborud.REG_NUM, ba_connection.GOST_ID, ba_connection.OBORUD_ID,  ba_gost.ID ba_gost_id, ba_gost.GOST, ba_gost.GOST_PUNKT, ba_gost.NUM_OA_NEW FROM ba_oborud LEFT JOIN ba_connection ON ba_oborud.ID = ba_connection.OBORUD_ID LEFT JOIN ba_gost  ON ba_connection.GOST_ID = ba_gost.ID WHERE ba_gost.NUM_OA_NEW > 0 ORDER BY ba_oborud.OBJECT");

                                //оборудование привязанное к гостам из новой обасти для выбранных(участвующих в испытании) методик
                                //$res = $DB->Query("SELECT ba_oborud.ID, ba_oborud.TYPE_OBORUD, ba_oborud.OBJECT, ba_oborud.REG_NUM, ba_connection.GOST_ID, ba_connection.OBORUD_ID,  ba_gost.ID ba_gost_id, ba_gost.GOST, ba_gost.GOST_PUNKT, ba_gost.NUM_OA_NEW FROM ba_oborud LEFT JOIN ba_connection ON ba_oborud.ID = ba_connection.OBORUD_ID LEFT JOIN ba_gost  ON ba_connection.GOST_ID = ba_gost.ID WHERE ba_gost.NUM_OA_NEW > 0  AND ba_gost.ID IN ($strSelectedGostId) ORDER BY ba_oborud.OBJECT");
                                while ($row = $res->Fetch()) {

                                    if ($row['IDENT'] != "VO" && $row['IDENT'] != "TS" && $row['IDENT'] != "REACT") {
                                        echo "<option data-gost='" . $row['ba_gost_id'] . "' value='" . $row['ID'] . "'>" . ($row['TYPE_OBORUD'] ? $row['TYPE_OBORUD'] . ' ' : '') . "" . $row['OBJECT'] . ", инв. номер " . $row['REG_NUM'] . ($row['GOST'] ? ', ' . $row['GOST']  : '') . ($row['GOST'] && $row['GOST_PUNKT'] ? ' ' . $row['GOST_PUNKT']  : '') ." </option>";
                                    }
                                }
                            }  else {
                                console_log('false4', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
                                $i = 0;

                                $res = $DB->Query("SELECT * FROM `ba_oborud` ORDER BY `OBJECT`");
                                while ($row = $res->Fetch()) {

                                    if ($row['IDENT'] != "VO" && $row['IDENT'] != "TS" && $row['IDENT'] != "REACT") {
                                        echo "<option value=" . $row['ID'] . ">" . ($row['TYPE_OBORUD'] ? $row['TYPE_OBORUD'] . ' ' : '') . "" . $row['OBJECT'] . ", инв. номер " . $row['REG_NUM'] . " </option>";
                                    }
                                }
                            }
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <td>Используемое оборудование</td>
                    <td>
                        <form method="post">

                            <?php
                            $ids = [];
                            $id = [];
                            $gosts = [];
                            $messageErrors = '';

                            $res_tz_ob = $DB->Query("SELECT * FROM `TZ_OB_CONNECT` WHERE `ID_TZ` = " . $_GET['ID'] . " AND `PROTOCOL_ID`=" . $currentProtocolId);

                            if (intval($res_tz_ob->SelectedRowsCount()) > 0) {

                                while ($row = $res_tz_ob->Fetch()) {
                                    if ($deal_id >= getIdDealAfterWhichOborudConnectedGost()) {
                                        console_log('true5', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
                                        if (empty($row['ID_GOST'])) {
                                            $res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . $row['ID_OB'])->Fetch();
                                            $messageErrors .= "<p>Внимание! Оборудование <a href='/oborud.php?ID=".$res['ID']."'>"   . $res['OBJECT'] . " " . $res['TYPE_OBORUD']. " " . $res['REG_NUM'] .  "</a> не привязано к госту!</p>";
                                        } else {
                                            $res = $DB->Query("SELECT *, `ba_gost`.`ID` `ba_gost_id`, `ba_oborud`.`ID` `ba_oborud_id` FROM `ba_gost`, `ba_oborud` WHERE `ba_gost`.`ID`={$row['ID_GOST']} AND `ba_oborud`.`ID`={$row['ID_OB']}")->Fetch();
                                        }
                                    } else {
                                        console_log('false5', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
                                        $res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . $row['ID_OB'])->Fetch();
                                    }

                                    if ($res['IDENT'] != "VO" && $res['IDENT'] != "TS" && $res['IDENT'] != "REACT") {
                                        $ids[] = ($res['TYPE_OBORUD'] ? $res['TYPE_OBORUD'] . ' ' : '') . "" . $res['OBJECT'] . ", инв. номер " . $res['REG_NUM'];
                                        $id[] = $res['ID'];
                                        $idsGost[] = ($res['TYPE_OBORUD'] ? $res['TYPE_OBORUD'] . ' ' : '') . "" . $res['OBJECT'] . ", инв. номер " . $res['REG_NUM'] . ($res['GOST'] ? ', ' . $res['GOST'] : '') . ($res['GOST'] && $res['GOST_PUNKT'] ? ' ' . $res['GOST_PUNKT'] : '');
                                        if ($deal_id >= getIdDealAfterWhichOborudConnectedGost()) {
                                            console_log('true6', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
                                            $gosts[] = [
                                                'id' => $res['ba_gost_id'],
                                                'gost' => $res['GOST'],
                                                'gost_punkt' => $res['GOST_PUNKT']
                                            ];
                                        }
                                    }
                                }

                            } else {

                                foreach ($arr as $key => $arrDatum) {
                                    foreach ($arrDatum['gost'] as $k => $item) {
                                        if (!$protocolActualValues[$key - 1][$k]) {
                                            continue;
                                        }

                                        $res = $DB->Query("SELECT *,`ba_gost`.`ID` as `ba_gost_id` FROM `ba_gost`, `ba_connection`, `ba_oborud` WHERE `ba_connection`.`GOST_ID`=" . $item . " AND `ba_oborud`.`ID`=`ba_connection`.`OBORUD_ID` AND `ba_connection`.`GOST_ID`=`ba_gost`.`ID` ORDER BY `OBJECT`");
                                        while ($row = $res->Fetch()) {
                                            if ($row['IDENT'] != "VO" && $row['IDENT'] != "TS" && $row['IDENT'] != "REACT") {

                                                $ids[] = ($row['TYPE_OBORUD'] ? $row['TYPE_OBORUD'] . ' ' : '') . "" . $row['OBJECT'] . ", инв. номер " . $row['REG_NUM'];
                                                $id[] = $row['ID'];
                                                $idsGost[] = ($row['TYPE_OBORUD'] ? $row['TYPE_OBORUD'] . ' ' : '') . "" . $row['OBJECT'] . ", инв. номер " . $row['REG_NUM'] . ($row['GOST'] ? ', ' . $row['GOST'] : '') . ($row['GOST'] && $row['GOST_PUNKT'] ? ' ' . $row['GOST_PUNKT'] : '');
                                                $gosts[] = [
                                                    'id' => $row['ba_gost_id'],
                                                    'gost' => $row['GOST'],
                                                    'gost_punkt' => $row['GOST_PUNKT']
                                                ];
                                            }
                                        }
                                    }
                                }
                            }

							 if ($deal_id < getIdDealAfterWhichOborudConnectedGost()) {
								console_log('false6', '$deal_id < getIdDealAfterWhichOborudConnectedGost()');
							    $ids = array_unique($ids);
							    $id = array_unique($id);
							 } else {
								$idsGost = array_unique($idsGost);
							}
                            $id_final = [];
							$idk = [];
                            ?>
                            <div class="message-errors" style="display: <?= empty($messageErrors) ? 'none' : 'block' ?>">
                                <span><?= $messageErrors ?></span>
                            </div>
							<div style="display: flex">
                            <select id="second" multiple style="height: 200px;"
                                    <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'disabled' : '' ?>>
                                <?php foreach ($ids as $k => $item) {
									$gostId = $gosts[$k]['id'] ?? null;
                                    $gostName = $gosts[$k]['gost'] ?? null;
                                    $gostPunkt = $gosts[$k]['gost_punkt'] ?? null;
                                    $title = $item . ($gostName ? ', ' . $gostName : '') . ($gostName && $gostPunkt ? ' ' . $gostPunkt : '');
									$oborud [] = $item;
                                    if ($deal_id >= getIdDealAfterWhichOborudConnectedGost() && !isset($idsGost[$k])) {
                                        continue;
                                    }
                                    if (!in_array($id[$k], $idk)) {
										echo "<option data-gost='" . $gostId . "' value='" . $id[$k] . "' title='" . $title . "'>" . $item . " </option>";
									}
									$idk [] = $id[$k];

                                    if ($deal_id >= getIdDealAfterWhichOborudConnectedGost()) {
                                        console_log('true7', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
                                        $id_final[] = [$id[$k] => $gostId];
                                    } else {
                                        console_log('false7', '$deal_id >= getIdDealAfterWhichOborudConnectedGost()');
                                        $id_final[] = $id[$k];
                                    }
                                }

                                ?>
                            </select>
							<select id="gost_area" multiple style="height: 200px; width: 25%" readonly></select>
							</div>
                            <input type="hidden" id="ids_oborud" name="ids_oborud"
                                   value='<?= json_encode($id_final) ?>'>

                            Вернуть оборудование по умолчанию <input type="checkbox" name="DEL_OB"
                                                                     class="inputTypeCheckbox"
                                                                     <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'disabled' : '' ?>>

                            <input type="submit" style="max-width: 400px" value="Сохранить оборудование" class="button"
                                   <?= !empty($currentProtocolNumber) ? 'disabled' : '' ?>>

                        </form>
                    </td>
                </tr>
                <tr>
                    <td>Подпись в протоколе</td>
                    <td><select id="verify" class="verify_change" name="verify_s[]"
                                style="max-width: 800px; margin-top:0px"
                                multiple="multiple" <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'disabled' : '' ?>>

                            <option value="<?= $deal2['ASSIGNED_BY_ID'] ?> "
                                    <?= $verifyProtocol && in_array((int)$deal2['ASSIGNED_BY_ID'], $verifyProtocol) ? 'selected' : '' ?>><?= $deal2['ASSIGNED_BY_NAME'] ?> <?= $deal2['ASSIGNED_BY_LAST_NAME'] ?></option>
                            <?php


                            $assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal2['ID']);
                            if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {

                                foreach ($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign) {
                                    $assign_user = CUser::GetByID((int)$assign)->Fetch();

                                    echo "<option value= '" . $assign_user['ID'] . "' " . (in_array($assign_user['ID'], $verifyProtocol) ? 'selected' : '') . " >" . $assign_user['NAME'] . " " . $assign_user['LAST_NAME'] . "</option>";
                                }
                            }

                            ?>
                        </select></td>
                </tr>
                </td></tr>

                <tr>
                    <td height="35">
                        Протокол выдается вне ЛИС
                    </td>
                    <td>
                        <input type="checkbox" name="NO_BITRIX" class="inputTypeCheckbox no-bitrix"
                               <?= !empty($protocolOutsideLis) ? 'checked' : '' ?>
                               <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'disabled' : '' ?>>
                    </td>
                </tr>

                <?php
				$log = date("Y.m.d G:i:s") . "\n";
				$log .= print_r($no_oa, 1). "\n";
				file_put_contents(getcwd() . '/nooa2.txt', $log);
                $inAttestatDiapason = empty($no_oa) ? 1 : 0;

                $queryUpdateInAttestatDiapason = 'UPDATE `PROTOCOLS` SET `IN_ATTESTAT_DIAPASON`={?} WHERE `ID`={?}';
                $arrayUpdateInAttestatDiapason = [$inAttestatDiapason, $currentProtocolId];
                $resultUpdateInAttestatDiapason = $db->queryExec($queryUpdateInAttestatDiapason, $arrayUpdateInAttestatDiapason);

                $resultCurrentProtocolsData = $db->selectRow('SELECT * FROM `PROTOCOLS` WHERE ID ={?}', [$currentProtocolId]);

                ?>
                <tr>
                    <td height="35">
                        C аттестатом аккредитации
                    </td>
                    <td>
						<?php
						$log = date("Y.m.d G:i:s") . "\n";
						$log .= print_r($attestat, 1). "\n";
						file_put_contents(getcwd() . '/attest1.txt', $log, FILE_APPEND);
						$log = date("Y.m.d G:i:s") . "\n";
						$log .= print_r($resultCurrentProtocolsData['IN_ATTESTAT_DIAPASON'], 1). "\n";
						file_put_contents(getcwd() . '/attest1.txt', $log, FILE_APPEND);

						?>

                        <input type="checkbox" name="NO_ATTESTAT" class="inputTypeCheckbox no-attestat"
                               <?= !empty($attestatInProtocol) ? 'checked' : '' ?>
                               <?= (!empty($editResultsCurrentProtocol) && !empty($currentProtocolNumber) && !empty($attestat) && $resultCurrentProtocolsData['IN_ATTESTAT_DIAPASON'] &&
                                   (($USER->GetID() == '1') || ($USER->GetID() == '10') || ($USER->GetID() == '9') || ($USER->GetID() == '7') || ($USER->GetID() == '61'))) || (empty($currentProtocolNumber) && !empty($attestat) && $resultCurrentProtocolsData['IN_ATTESTAT_DIAPASON'] &&
                                   (($USER->GetID() == '1') || ($USER->GetID() == '10') || ($USER->GetID() == '9') || ($USER->GetID() == '7') || ($USER->GetID() == '61'))) ? '' : 'disabled' ?>>
                        <input type="hidden" name="HIDDEN_NO_ATTESTAT" value="<?= $attestatInProtocol ?>" class="hidden-no-attestat">
					</td>
                </tr>

            </table>


            <!-------------------------------------------------------------------!!!!!!!!!!!!!!ПЕСОК СТРОИТЕЛЬНЫЙ!!!!!!!!!!------------------------------------------->
            <table id="zern_table" class="table-zern <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'tableDisabled' : '' ?>">
                <thead>
                <tr>
                    <th>
                        <span class="zern-title">Зерновой состав</span>
                    </th>
                </tr>
                </thead>
                <tr>
                    <td width="262">Остаток на сите 2,5</td>
                    <td><input type="number" step="0.01" name="ostatki[0]" value="<?= $ostatki[0] ?>"></td>
                <tr>
                    <td>Остаток на сите 1,25</td>
                    <td><input type="number" step="0.01" name="ostatki[1]" value="<?= $ostatki[1] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,63</td>
                    <td><input type="number" step="0.01" name="ostatki[2]" value="<?= $ostatki[2] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,315</td>
                    <td><input type="number" step="0.01" name="ostatki[3]" value="<?= $ostatki[3] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,16</td>
                    <td><input type="number" step="0.01" name="ostatki[4]" value="<?= $ostatki[4] ?>">
                    </td>
                <tr>
                    <td>Проход через сито 0,16</td>
                    <td><input type="number" step="0.01" name="ostatki[5]" value="<?= $ostatki[5] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 2,5 полн.</td>
                    <td><input type="number" step="0.01" name="ostatki[6]" value="<?= $ostatki[6] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 1,25 полн.</td>
                    <td><input type="number" step="0.01" name="ostatki[7]" value="<?= $ostatki[7] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,63 полн.</td>
                    <td><input type="number" step="0.01" name="ostatki[8]" value="<?= $ostatki[8] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,315 полн.</td>
                    <td><input type="number" step="0.01" name="ostatki[9]" value="<?= $ostatki[9] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,16 полн.</td>
                    <td><input type="number" step="0.01" name="ostatki[10]" value="<?= $ostatki[10] ?>">
                    </td>
                <tr>
                    <td>Проход через сито 0,16 полн.</td>
                    <td><input type="number" step="0.01" name="ostatki[11]" value="<?= $ostatki[11] ?>">
                    </td>
            </table>


            <!-------------------------------------------------------------------АСФАЛЬТ. ЗЕРНОВОЙ СОСТАВ МИН. ЧАСТИ. ГОСТ 12801, 9128------------------------------------------->

            <table id="zern_table2" class="table-zern <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'tableDisabled' : '' ?>">

                <thead>
                <tr>
                    <th>
                        <span class="zern-title">Зерновой состав</span>
                    </th>
                </tr>
                </thead>
                <!-- ./'Привязка заявки к нескольким протоколам' -->
                <!--<td width="262">Содержание зерен размером мельче 80 мм</td>
							<td><input type="number" step="0.01" name="ostatki2[10]" value="<?= $ostatki[10] ?>"></td>
							<tr>-->
                <td width="262">Содержание зерен размером мельче 40 мм</td>
                <td><input type="number" step="0.01" name="ostatki2[11]" value="<?= $ostatki[11] ?>"></td>
                <tr>
                    <td width="262">Содержание зерен размером мельче 20 мм</td>
                    <td><input type="number" step="0.01" name="ostatki2[0]" value="<?= $ostatki[0] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 15 мм</td>
                    <td><input type="number" step="0.01" name="ostatki2[1]" value="<?= $ostatki[1] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче 10 мм</td>
                    <td><input type="number" step="0.01" name="ostatki2[2]" value="<?= $ostatki[2] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче 5 мм</td>
                    <td><input type="number" step="0.01" name="ostatki2[3]" value="<?= $ostatki[3] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче 2,5 мм</td>
                    <td><input type="number" step="0.01" name="ostatki2[4]" value="<?= $ostatki[4] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче 1,25 мм</td>
                    <td><input type="number" step="0.01" name="ostatki2[5]" value="<?= $ostatki[5] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче 0,63 мм</td>
                    <td><input type="number" step="0.01" name="ostatki2[6]" value="<?= $ostatki[6] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче 0,315 мм</td>
                    <td><input type="number" step="0.01" name="ostatki2[7]" value="<?= $ostatki[7] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче 0,16 мм</td>
                    <td><input type="number" step="0.01" name="ostatki2[8]" value="<?= $ostatki[8] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче 0,071 мм</td>
                    <td><input type="number" step="0.01" name="ostatki2[9]" value="<?= $ostatki[9] ?>">
                    </td>

            </table>

            <!-------------------------------------------------------------------!!!!!!!!!!!!!!ГРУНТ!!!!!!!!!!------------------------------------------->

            <table id="zern_table3" class="table-zern <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'tableDisabled' : '' ?>">
                <thead>
                <tr>
                    <th>
                        <span class="zern-title">Зерновой состав</span>
                    </th>
                </tr>
                </thead>

                <tr>
                    <td width="262">Фракция грунта 100 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[21]" value="<?= $ostatki[21] ?>"></td>
                <tr>
                    <td width="262">Фракция грунта 60 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[20]" value="<?= $ostatki[20] ?>"></td>
                <tr>
                    <td width="262">Фракция грунта 40 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[19]" value="<?= $ostatki[19] ?>"></td>
                <tr>
                    <td width="262">Фракция грунта 20 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[18]" value="<?= $ostatki[18] ?>"></td>

                <tr>
                    <td width="262">Фракция грунта 10 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[0]" value="<?= $ostatki[0] ?>"></td>
                <tr>
                    <td>Фракция грунта 5,0 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[1]" value="<?= $ostatki[1] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 2,0 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[2]" value="<?= $ostatki[2] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 1,0 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[3]" value="<?= $ostatki[3] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 0,5 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[4]" value="<?= $ostatki[4] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 0,25 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[5]" value="<?= $ostatki[5] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 0,1 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[6]" value="<?= $ostatki[6] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 0,05 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[7]" value="<?= $ostatki[7] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта менее 0,05 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[8]" value="<?= $ostatki[8] ?>">
                    </td>
                <tr>
                    <td width="262">Фракция грунта 100 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[25]" value="<?= $ostatki[25] ?>"></td>
                <tr>
                    <td width="262">Фракция грунта 60 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[24]" value="<?= $ostatki[24] ?>"></td>
                <tr>
                    <td width="262">Фракция грунта 40 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[23]" value="<?= $ostatki[23] ?>"></td>
                <tr>
                    <td width="262">Фракция грунта 20 мм</td>
                    <td><input type="number" step="0.01" name="ostatki3[22]" value="<?= $ostatki[22] ?>"></td>
                <tr>
                    <td width="262">Фракция грунта 10 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki3[9]" value="<?= $ostatki[9] ?>"></td>
                <tr>
                    <td>Фракция грунта 5,0 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki3[10]" value="<?= $ostatki[10] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 2,0 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki3[11]" value="<?= $ostatki[11] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 1,0 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki3[12]" value="<?= $ostatki[12] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 0,5 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki3[13]" value="<?= $ostatki[13] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 0,25 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki3[14]" value="<?= $ostatki[14] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 0,1 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki3[15]" value="<?= $ostatki[15] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта 0,05 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki3[16]" value="<?= $ostatki[16] ?>">
                    </td>
                <tr>
                    <td>Фракция грунта менее 0,05 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki3[17]" value="<?= $ostatki[17] ?>">
                    </td>
            </table>

            <!-------------------------------------------------------------------!!!!!!!!!!!!!!ПЕСОК ПРИРОДНЫЙ!!!!!!!!!!------------------------------------------->

            <table id="zern_table4" class="table-zern <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'tableDisabled' : '' ?>">

                <thead>
                <tr>
                    <th>
                        <span class="zern-title">Зерновой состав</span>
                    </th>
                </tr>
                </thead>

                <tr>
                    <td width="262">Остаток на сите 2,0</td>
                    <td><input type="number" step="0.01" name="ostatki4[0]" value="<?= $ostatki[0] ?>"></td>
                <tr>
                    <td>Остаток на сите 1,0</td>
                    <td><input type="number" step="0.01" name="ostatki4[1]" value="<?= $ostatki[1] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,5</td>
                    <td><input type="number" step="0.01" name="ostatki4[2]" value="<?= $ostatki[2] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,25</td>
                    <td><input type="number" step="0.01" name="ostatki4[3]" value="<?= $ostatki[3] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,125</td>
                    <td><input type="number" step="0.01" name="ostatki4[4]" value="<?= $ostatki[4] ?>">
                    </td>
                <tr>
                    <td>Кол-во в поддоне</td>
                    <td><input type="number" step="0.01" name="ostatki4[5]" value="<?= $ostatki[5] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 2,0 полн.:</td>
                    <td><input type="number" step="0.01" name="ostatki4[6]" value="<?= $ostatki[6] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 1,0 полн.</td>
                    <td><input type="number" step="0.01" name="ostatki4[7]" value="<?= $ostatki[7] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,5 полн.</td>
                    <td><input type="number" step="0.01" name="ostatki4[8]" value="<?= $ostatki[8] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,25 полн.</td>
                    <td><input type="number" step="0.01" name="ostatki4[9]" value="<?= $ostatki[9] ?>">
                    </td>
                <tr>
                    <td>Остаток на сите 0,125 полн.</td>
                    <td><input type="number" step="0.01" name="ostatki4[10]" value="<?= $ostatki[10] ?>">
                    </td>
                <tr>
                    <td>Кол-во в поддоне полн.</td>
                    <td><input type="number" step="0.01" name="ostatki4[11]" value="<?= $ostatki[11] ?>">
                    </td>
            </table>


            <!------------------------------------------------------Гранулометрический состав минеральной части смеси ПНСТ 183------------------------------------------->

            <table id="zern_table5" class="table-zern <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'tableDisabled' : '' ?>">
                <thead>
                <tr>
                    <th>
                        <span class="zern-title">Зерновой состав</span>
                    </th>
                    <th>
                        <span class="zern-title">Фактические данные</span>
                    </th>
                    <th>
                        <span class="zern-title">Данные заказчика</span>
                    </th>
                </tr>
                </thead>

                <tr>
                    <td width="262">Содержание зерен размером мельче 31,5 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[0]" value="<?= $ostatki[0] ?>"></td>
                    <td><input type="number" step="0.01" name="sostav[0]" value="<?= $sostav[0] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 22,4 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[1]" value="<?= $ostatki[1] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[1]" value="<?= $sostav[1] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 16,0 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[2]" value="<?= $ostatki[2] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[2]" value="<?= $sostav[2] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 11,2 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[3]" value="<?= $ostatki[3] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[3]" value="<?= $sostav[3] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 8,0 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[4]" value="<?= $ostatki[4] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[4]" value="<?= $sostav[4] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 5,6 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[5]" value="<?= $ostatki[5] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[5]" value="<?= $sostav[5] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 4,0 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[6]" value="<?= $ostatki[6] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[6]" value="<?= $sostav[6] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 2,0 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[7]" value="<?= $ostatki[7] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[7]" value="<?= $sostav[7] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 1,0 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[8]" value="<?= $ostatki[8] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[8]" value="<?= $sostav[8] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 0,5 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[9]" value="<?= $ostatki[9] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[9]" value="<?= $sostav[9] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 0,25 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[10]" value="<?= $ostatki[10] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[10]" value="<?= $sostav[10] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 0,125 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[11]" value="<?= $ostatki[11] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[11]" value="<?= $sostav[11] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 0,063 мм</td>
                    <td><input type="number" step="0.01" name="ostatki5[12]" value="<?= $ostatki[12] ?>">
                    </td>
                    <td><input type="number" step="0.01" name="sostav[12]" value="<?= $sostav[12] ?>"></td>
            </table>


            <!-------------------------------------------------------------------!!!!!!!!!!!!!!ЩЕБЕНОЧНО-ПЕСЧАНАЯ СМЕСЬ!!!!!!!!!!------------------------------------------->

            <table id="zern_table6" class="table-zern <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'tableDisabled' : '' ?>">

                <thead>
                <tr>
                    <th>
                        <span class="zern-title">Зерновой состав</span>
                    </th>
                    <th>
                        <span class="zern-title">Фактические данные</span>
                    </th>
                    <th>
                        <span class="zern-title">Требования ГОСТ 25607</span>
                    </th>
                </tr>
                </thead>

                <tr>
                    <td width="262">Полный остаток на сите 120 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[0]" value="<?= $ostatki[0] ?>"></td>
                <tr>
                    <td>Полный остаток на сите 80 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[1]" value="<?= $ostatki[1] ?>">
                    </td>
                <tr>
                    <td>Полный остаток на сите 40 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[2]" value="<?= $ostatki[2] ?>">
                    </td>
                <tr>
                    <td>Полный остаток на сите 20 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[3]" value="<?= $ostatki[3] ?>">
                    </td>
                <tr>
                    <td>Полный остаток на сите 10 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[4]" value="<?= $ostatki[4] ?>">
                    </td>
                <tr>
                    <td>Полный остаток на сите 5 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[5]" value="<?= $ostatki[5] ?>">
                    </td>
                <tr>
                    <td>Полный остаток на сите 2,5 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[6]" value="<?= $ostatki[6] ?>">
                    </td>
                <tr>
                    <td>Полный остаток на сите 0,63 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[7]" value="<?= $ostatki[7] ?>">
                    </td>
                <tr>
                    <td>Полный остаток на сите 0,16 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[8]" value="<?= $ostatki[8] ?>">
                    </td>
                <tr>
                    <td>Полный остаток на сите 0,05 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[9]" value="<?= $ostatki[9] ?>">
                    </td>
                <tr>
                    <td width="262">Частный остаток на сите 120 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[10]" value="<?= $ostatki[10] ?>"></td>
                <tr>
                    <td>Частный остаток на сите 80 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[11]" value="<?= $ostatki[11] ?>">
                    </td>
                <tr>
                    <td>Частный остаток на сите 40 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[12]" value="<?= $ostatki[12] ?>">
                    </td>
                <tr>
                    <td>Частный остаток на сите 20 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[13]" value="<?= $ostatki[13] ?>">
                    </td>
                <tr>
                    <td>Частный остаток на сите 10 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[14]" value="<?= $ostatki[14] ?>">
                    </td>
                <tr>
                    <td>Частный остаток на сите 5 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[15]" value="<?= $ostatki[15] ?>">
                    </td>
                <tr>
                    <td>Частный остаток на сите 2,5 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[16]" value="<?= $ostatki[16] ?>">
                    </td>
                <tr>
                    <td>Частный остаток на сите 0,63 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[17]" value="<?= $ostatki[17] ?>">
                    </td>
                <tr>
                    <td>Частный остаток на сите 0,16 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[18]" value="<?= $ostatki[18] ?>">
                    </td>
                <tr>
                    <td>Частный остаток на сите 0,05 мм</td>
                    <td><input type="number" step="0.01" name="ostatki6[19]" value="<?= $ostatki[19] ?>">
                    </td>
                <tr>
            </table>


            <!--------------------------------ЩЕБЕНЬ------------------------------------------------------->

            <table id="zern_table7" class="table-zern <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'tableDisabled' : '' ?>">

                <thead>
                <tr>
                    <th>
                        <span class="zern-title">Зерновой состав</span>
                    </th>
                </tr>
                </thead>

                <tr>
                    <td width="262">Содержание зерен размером мельче 2D</td>
                    <td><input type="number" step="0.01" name="ostatki7[0]" value="<?= $ostatki[0] ?>"></td>
                <tr>
                    <td>Содержание зерен размером мельче 1.4D</td>
                    <td><input type="number" step="0.01" name="ostatki7[1]" value="<?= $ostatki[1] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче D</td>
                    <td><input type="number" step="0.01" name="ostatki7[2]" value="<?= $ostatki[2] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче D/1.4</td>
                    <td><input type="number" step="0.01" name="ostatki7[3]" value="<?= $ostatki[3] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче d</td>
                    <td><input type="number" step="0.01" name="ostatki7[4]" value="<?= $ostatki[4] ?>">
                    </td>
                <tr>
                    <td>Содержание зерен размером мельче d/2</td>
                    <td><input type="number" step="0.01" name="ostatki7[5]" value="<?= $ostatki[5] ?>">
                    </td>
            </table>

            <!--------------------------------ЩЕБЕНЬ  ШЛАКОВЫЙ------------------------------------------------------->

            <table id="zern_table8" class="table-zern <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'tableDisabled' : '' ?>">

                <thead>
                <tr>
                    <th>
                        <span class="zern-title">Зерновой состав</span>
                    </th>
                </tr>
                </thead>

                <tr>
                    <td width="262">Частные остатки на сите 1,25D</td>
                    <td><input type="number" step="0.01" name="ostatki8[0]" value="<?= $ostatki[0] ?>"></td>
                <tr>
                    <td>Частные остатки на сите D</td>
                    <td><input type="number" step="0.01" name="ostatki8[1]" value="<?= $ostatki[1] ?>">
                    </td>
                <tr>
                    <td>Частные остатки на сите 0,5(D+d)</td>
                    <td><input type="number" step="0.01" name="ostatki8[2]" value="<?= $ostatki[2] ?>">
                    </td>
                <tr>
                    <td>Частные остатки на сите d</td>
                    <td><input type="number" step="0.01" name="ostatki8[3]" value="<?= $ostatki[3] ?>">
                    </td>
                <tr>
                    <td>Частные остатки на сите d/2</td>
                    <td><input type="number" step="0.01" name="ostatki8[4]" value="<?= $ostatki[4] ?>">
                    </td>
                <tr>
                    <td>Частные остатки на сите менее d</td>
                    <td><input type="number" step="0.01" name="ostatki8[5]" value="<?= $ostatki[5] ?>">
                    </td>
                <tr>
                    <td>Полные остатки на сите 1,25D</td>
                    <td><input type="number" step="0.01" name="ostatki8[6]" value="<?= $ostatki[6] ?>">
                    </td>
                <tr>
                    <td>Полные остатки на сите D</td>
                    <td><input type="number" step="0.01" name="ostatki8[7]" value="<?= $ostatki[7] ?>">
                    </td>
                <tr>
                    <td>Полные остатки на сите 0,5(D+d)</td>
                    <td><input type="number" step="0.01" name="ostatki8[8]" value="<?= $ostatki[8] ?>">
                    </td>
                <tr>
                    <td>Полные остатки на сите d</td>
                    <td><input type="number" step="0.01" name="ostatki8[9]" value="<?= $ostatki[9] ?>">
                    </td>
                <tr>
                    <td>Частные остатки на сите d/2</td>
                    <td><input type="number" step="0.01" name="ostatki8[10]" value="<?= $ostatki[10] ?>">
                    </td>
                <tr>
                    <td>Полные остатки на сите менее d</td>
                    <td><input type="number" step="0.01" name="ostatki8[11]" value="<?= $ostatki[11] ?>">
                    </td>

            </table>
            <!-------------------------------------------------------------------!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!------------------------------------------>

            <table class='tableFrost table-zern <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'tableDisabled' : '' ?>'>

                <tr>
                    <td>Наличие трещин, сколов, шелушения (контрольных образцов)</td>
                    <td>
                        <input type='text' name='controlDamage' class='frost'
                               value='<?= !empty($control_damage) ? $control_damage : "-" ?>'>
                    </td>
                </tr>
                <tr>
                    <td>Наличие трещин, сколов, шелушения (основных образцов)</td>
                    <td>
                        <input type='text' name='mainDamage' class='frost'
                               value='<?= !empty($main_damage) ? $main_damage : "" ?>'>
                    </td>
                </tr>
                <tr>
                    <td>Среднее уменьшение массы образцов (контрольных образцов)</td>
                    <td>
                        <input type='text' name='controlMass' class='frost'
                               value='<?= !empty($control_mass) ? $control_mass : "-" ?>'>
                    </td>
                </tr>
                <tr>
                    <td>Среднее уменьшение массы образцов (основных образцов)</td>
                    <td>
                        <input type='number' name='mainMass' step="0.01"
                               value='<?= !empty($main_mass) ? $main_mass : "" ?>'>
                    </td>
                </tr>
                <tr>
                    <td>Прочность при сжатии насыщенных водой образцов (контрольных образцов)</td>
                    <td>
                        <input type='number' name='controlStrength[0]' step="0.01"
                               value='<?= !empty($control_strength[0]) ? $control_strength[0] : "" ?>'>
                        <input type='number' name='controlStrength[1]' step="0.01"
                               value='<?= !empty($control_strength[1]) ? $control_strength[1] : "" ?>'>
                        <input type='number' name='controlStrength[2]' step="0.01"
                               value='<?= !empty($control_strength[2]) ? $control_strength[2] : "" ?>'>
                        <input type='number' name='controlStrength[3]' step="0.01"
                               value='<?= !empty($control_strength[3]) ? $control_strength[3] : "" ?>'>
                        <input type='number' name='controlStrength[4]' step="0.01"
                               value='<?= !empty($control_strength[4]) ? $control_strength[4] : "" ?>'>
                        <input type='number' name='controlStrength[5]' step="0.01"
                               value='<?= !empty($control_strength[5]) ? $control_strength[5] : "" ?>'>
                    </td>
                </tr>
                <tr>
                    <td>Прочность при сжатии насыщенных водой образцов (основных образцов)</td>
                    <td>
                        <input type='number' name='mainStrength[0]' step="0.01"
                               value='<?= !empty($main_strength[0]) ? $main_strength[0] : "" ?>'>
                        <input type='number' name='mainStrength[1]' step="0.01"
                               value='<?= !empty($main_strength[1]) ? $main_strength[1] : "" ?>'>
                        <input type='number' name='mainStrength[2]' step="0.01"
                               value='<?= !empty($main_strength[2]) ? $main_strength[2] : "" ?>'>
                        <input type='number' name='mainStrength[3]' step="0.01"
                               value='<?= !empty($main_strength[3]) ? $main_strength[3] : "" ?>'>
                        <input type='number' name='mainStrength[4]' step="0.01"
                               value='<?= !empty($main_strength[4]) ? $main_strength[4] : "" ?>'>
                        <input type='number' name='mainStrength[5]' step="0.01"
                               value='<?= !empty($main_strength[5]) ? $main_strength[5] : "" ?>'>
                    </td>
                </tr>
                <tr>
                    <td>Средняя прочность при сжатии насыщенных водой образцов (контрольных образцов)</td>
                    <td>
                        <input type='number' name='controlMediumStrength' step="0.01"
                               value='<?= !empty($control_medium) ? $control_medium : "" ?>'>
                    </td>
                </tr>
                <tr>
                    <td>Средняя прочность при сжатии насыщенных водой образцов (основных образцов)</td>
                    <td>
                        <input type='number' name='mainMediumStrength' step="0.01"
                               value='<?= !empty($main_medium) ? $main_medium : "" ?>'>
                    </td>
                </tr>
                <tr>
                    <td>Нижняя граница доверительного интервала (контрольных образцов)</td>
                    <td>
                        <input type='number' name='controlBottomLine' step="0.01"
                               value='<?= !empty($control_bottom_line) ? $control_bottom_line : "" ?>'>
                    </td>
                </tr>
                <tr>
                    <td>Нижняя граница доверительного интервала (основных образцов)</td>
                    <td>
                        <input type='number' name='mainBottomLine' step="0.01"
                               value='<?= !empty($main_bottom_line) ? $main_bottom_line : "" ?>'>
                    </td>
                </tr>
                <tr>
                    <td>Соотношение</td>
                    <td>
                        <select class='ratio' name='ratio'>
                            <option <?= ($ratio == 'соблюдается') ? 'selected' : '' ?>
                                    value='соблюдается'>соблюдается
                            </option>
                            <option <?= ($ratio == 'не соблюдается') ? 'selected' : '' ?>
                                    value='не соблюдается'>не соблюдается
                            </option>
                        </select>
                    </td>
                </tr>
            </table>

            <!---------------------------------'Новый протокол, Ареометрический метод' правил начало----------------------------------->

            <table id="metric_method" class="table-zern <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'tableDisabled' : '' ?>">
                <tr>
                    <td> Ареометрический метод</td>
                <tr>
                    <td width="262">Фракция грунта 10 мм</td>
                    <td><input type="number" step="0.01" name="ostatki9[0]" value="<?= $ostatki[0] ?>"></td>
                <tr>
                    <td width="262">Фракция грунта 5 мм</td>
                    <td><input type="number" step="0.01" name="ostatki9[1]" value="<?= $ostatki[1] ?>"></td>
                <tr>
                    <td width="262">Фракция грунта 2 мм</td>
                    <td><input type="number" step="0.01" name="ostatki9[2]" value="<?= $ostatki[2] ?>"></td>
                <tr>
                    <td width="262">Фракция грунта 1 мм</td>
                    <td><input type="number" step="0.01" name="ostatki9[3]" value="<?= $ostatki[3] ?>"></td>

                <tr>
                    <td width="262">Фракция грунта 0,5 мм</td>
                    <td><input type="number" step="0.01" name="ostatki9[4]" value="<?= $ostatki[4] ?>"></td>
                <tr>
                    <td>Фракция грунта 0,25 мм</td>
                    <td><input type="number" step="0.01" name="ostatki9[5]" value="<?= $ostatki[5] ?>"></td>
                <tr>
                    <td>Фракция грунта 0,1 мм</td>
                    <td><input type="number" step="0.01" name="ostatki9[6]" value="<?= $ostatki[6] ?>"></td>
                <tr>
                    <td>Фракция грунта 0,05 мм</td>
                    <td><input type="number" step="0.01" name="ostatki9[7]" value="<?= $ostatki[7] ?>"></td>
                <tr>
                    <td>Фракция грунта 0,01 мм</td>
                    <td><input type="number" step="0.01" name="ostatki9[26]" value="<?= $ostatki[26] ?>"></td>
                <tr>
                    <td>Фракция грунта 0,002 мм</td>
                    <td><input type="number" step="0.01" name="ostatki9[27]" value="<?= $ostatki[27] ?>"></td>
                <tr>
                    <td>Фракция грунта менее 0,002 мм</td>
                    <td><input type="number" step="0.01" name="ostatki9[28]" value="<?= $ostatki[28] ?>"></td>
                <tr>
                    <td width="262">Фракция грунта 10 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki9[9]" value="<?= $ostatki[9] ?>"></td>
                <tr>
                    <td>Фракция грунта 5 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki9[10]" value="<?= $ostatki[10] ?>"></td>
                <tr>
                    <td>Фракция грунта 2 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki9[11]" value="<?= $ostatki[11] ?>"></td>
                <tr>
                    <td>Фракция грунта 1 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki9[12]" value="<?= $ostatki[12] ?>"></td>
                <tr>
                    <td>Фракция грунта 0,5 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki9[13]" value="<?= $ostatki[13] ?>"></td>
                <tr>
                    <td>Фракция грунта 0,25 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki9[14]" value="<?= $ostatki[14] ?>"></td>
                <tr>
                    <td>Фракция грунта 0,1 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki9[15]" value="<?= $ostatki[15] ?>"></td>
                <tr>
                    <td>Фракция грунта 0,05 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki9[16]" value="<?= $ostatki[16] ?>"></td>
                <tr>
                    <td>Фракция грунта 0,01 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki9[29]" value="<?= $ostatki[29] ?>"></td>
                <tr>
                    <td>Фракция грунта 0,002 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki9[30]" value="<?= $ostatki[30] ?>"></td>
                <tr>
                    <td>Фракция грунта менее 0,002 мм полн.</td>
                    <td><input type="number" step="0.01" name="ostatki9[31]" value="<?= $ostatki[31] ?>"></td>
            </table>

        </fieldset>

        <input type="hidden" name="current_protocol_id"
               value="<?= !empty($currentProtocolId) ? $currentProtocolId : '' ?>">
	   
	    <input type="hidden" name="anchor_sample" class="anchor-sample"
               value="<?= $anchorSampleProtocols ?>">
			   
        <div class="btn-group-result-isp" style="display:<?= !empty($countOfGeneratedProtocols) ? 'flex' : 'none' ?>">
            <input type="submit" class="button save-changes" name="btn_save_changes" value="Сохранить изменения"
                   <?= (!empty($currentProtocolNumber) && empty($editResultsCurrentProtocol)) ? 'disabled' : '' ?>>

           <input type="submit" value="Сформировать протокол" id="form_protocol" class="form-protocol button"
                   name="btn_form_protocol"
                   data-id-protocol="<?= !empty($currentProtocolId) ? $currentProtocolId : '' ?>"
                   <?= empty($editResultsCurrentProtocol) && (!isset($сurrentDataProtocol['PROTOCOL_TYPE']) || !empty($currentProtocolNumber) || !empty($protocolOutsideLis) || (isset($deal2['STAGE_ID']) && $deal2['STAGE_ID'] == '4') || (isset($deal2['STAGE_ID']) && $deal2['STAGE_ID'] == 'WON')) ? 'disabled' : '' ?>>
        </div>

    </form>

    <div class="file-upload-wrapper">
        <?php

        if (empty($_FILES)) {


            if (!empty($currentProtocolPdfName) && !empty($currentProtocolId)) {
                echo "<span class='pdf-version-uploaded'>
						Загружена PDF-версия: <a href='/pdf/" . $currentProtocolId . "/" . $currentProtocolPdfName . "' target='_blank'>" . $currentProtocolPdfName . "</a>
					  </span>";
            }

        } else if (!empty($_FILES) && !empty($currentProtocolId) && !empty($uploadfile)) { //Если файл загружаем и есть ID протокола и путь до сохранения файла, то сохраняем данные и выводим сообщение

            if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {

                echo "<span class='loaded-result'>Файл " . $_FILES['userfile']['name'] . "  был успешно загружен.</span>";
            } else {

                echo "<span class='loaded-result'>Ошибка загрузки!</span>";
            }
        }


        $dataOfGeneratedProtocols = null;
        if (!empty($idTZ)) {
            $dataOfGeneratedProtocols = $DB->Query("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`=" . $idTZ);
        }

        $countOfGeneratedProtocols = !empty($dataOfGeneratedProtocols) ? $dataOfGeneratedProtocols->SelectedRowsCount() : '';

        ?>

        <button class='btnPdf button' style="display:<?= !empty($countOfGeneratedProtocols) ? 'block' : 'none' ?>"
                <?= (empty($editResultsCurrentProtocol) && !empty($currentProtocolNumber) && empty($protocolOutsideLis)) ? 'disabled' : '' ?>>
            Прикрепить PDF
        </button>

        <div class='uploadProtocol'>
            <h2>Загрузить PDF-версию протокола</h2>

            <form enctype="multipart/form-data"
                  action="<?= !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '' ?>" method="POST">

                <input type="hidden" name="MAX_FILE_SIZE" value="300000000"/>

                Отправить этот файл: <input class="user-file" name="userfile" type="file"/>

                <input class="send-file" type="submit" value="Отправить файл" class="button"/>

            </form>
        </div>
    </div>

    <?php
} elseif (!$fl_num) {

    header('Location: http://172.16.255.2/protocols_test_binding_multiple_protocols.php');
    //header('Location: ' . $_SERVER['REQUEST_URI']);
    die();
    ?>

    <?php
}
?>

    <div class="wrepper-modal">
        <div class="modal-export">
            <h2 class="modal-title">Для создания протокола, выберите пожалуйста пробу!</h2>
            <div class="wrapper-btn">
                <button type="button" class="btn-madal btn-modal-close">Закрыть</button>
            </div>
        </div>
    </div>

    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script>
        $(".add").click(function () {
            td = $(this).parent().parent();
            keys = $(this).data("keys").split("-");
            td.append('<div style="display:flex"><input class="res" "margin-left:5px" type="number" step="0.001" name="results[' + keys[0] + '][' + keys[1] + '][' + keys[2] + '][]"  ><input style="margin-left:5px" type="button" value="- " onclick="$(this).parent().remove();"></div>');
        })
    </script>
    <script>
        var s = document.getElementById("second");
        var ids = document.getElementById("ids_oborud");
        var gostId = document.getElementById("gosts_oborud");
        var dealId = "<?= $deal_id ?? null; ?>";
        var currentDealId = "<?= getIdDealAfterWhichOborudConnectedGost(); ?>";
        console.log('dealId', dealId);
        console.log('currentDealId', currentDealId);

        document.getElementById("first").onchange = function () {
            if (dealId >= currentDealId) {
                let iOption = s.options.length;
                let gostId = this.options[this.selectedIndex].dataset.gost;
                let aldOborudGost = JSON.parse(ids.value);
                let newOborudGost = `{"${this.value}": "${gostId}"}`;

                s.options[s.options.length] = new Option(this.options[this.selectedIndex].text, this.value);
                s.options[iOption].title = this.options[this.selectedIndex].text;
                s.options[iOption].dataset.gost = this.options[this.selectedIndex].dataset.gost;

                aldOborudGost.push(JSON.parse(newOborudGost));
                ids.value = JSON.stringify(aldOborudGost);
            } else {
                s.options[s.options.length] = new Option(this.options[this.selectedIndex].text, this.value);
                ids.value = JSON.stringify(Array.prototype.slice.call(JSON.parse(ids.value)).concat(this.value));
            }
        }

        function getKey(arr, key, val) {
            for (let i in arr) {
                let obj = arr[i] ?? null;
                for (let oborudId in obj) {
                    if (oborudId == key && obj[oborudId] == val) {
                        return i;
                    }
                }
            }
        }


        s.ondblclick = function () {
            console.log('dealId 2', dealId);
            console.log('currentDealId 2', currentDealId);
            if (dealId >= currentDealId) {
                let arr = Array.prototype.slice.call(JSON.parse(ids.value));
                let gostId = this.options[this.selectedIndex].dataset.gost;
                let idOb = this.options[this.selectedIndex].value;
                // let index = getKey(arr, this.value, gostId);
				for (let i in arr) {
					let obj = arr[i] ?? null;
					for (let oborudId in obj) {
						if (oborudId == idOb) {
							arr.splice(i, 1);
						}
					}
				}
                // if (typeof arr[index] !== 'undefined') arr.splice(index, 1);
                ids.value = JSON.stringify(arr);
                s.removeChild(s.options[s.selectedIndex]);
            } else {
                arr = Array.prototype.slice.call(JSON.parse(ids.value));
                var index = arr.indexOf(this.value);
                if (index !== -1) arr.splice(index, 1);
                ids.value = JSON.stringify(arr);
                s.removeChild(s.options[s.selectedIndex]);
            }
        }

        s.onclick = function() {
            let idOb = this.options[this.selectedIndex].value;
            let gostArea = $("#gost_area");
            console.log('idOb', idOb);
            $.ajax({
                method: 'POST',
				dataType: 'json',
                url: 'tz_ob_connection.php',
                data: {
                    dealId: dealId,
                    current_protocol_id: <?= $currentProtocolId?>,
					idOb: idOb
                },
                success: function (data) {
					gostArea.empty()
					data.forEach(function (item) {
						gostArea.append(`<option value=${item.ID}>${item.GOST}</option>`);
					})
                }

            });

		}
    </script>
    <link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
    <link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>

    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>


    <script type="text/javascript" src="/tinymce/tinymce.min.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>
    <script type="text/javascript" language="javascript"
            src="/jszip/jquery.dataTables.customSortandFilters.js"></script>
    <link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
    <script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
    <script>

        $(document).ready(function () {

            $('.oborud_change').select2();
            $('.verify_change').select2();

        });
    </script>
    <script>
        $(document).ready(function () {
            table = $('#results_isp').DataTable({
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
                "columnDefs": [{"type": "natural", "targets": 2}, {
                    "sType": "ruDate",
                    "targets": 3
                }, {"type": "natural-nohtml", "targets": 7},
                    {
                        "targets": 5,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 4,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 3,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 6,
                        "render": $.fn.dataTable.render.ellipsis(60, true)
                    }
                ],
                "order": [[3, "asc"]],
                "colReorder": true,
                "bSortCellsTop": true,
                "dom": 'Blfrtip',
                "buttons": ['copy', 'excel', 'colvis', 'print']

            });

            $('#a_filter').on('click', function () {
                if ($('#a_filter').prop("checked")) {
                    table
                        .columns(1)
                        .search('A')
                        .draw();
                } else {
                    table
                        .columns(1)
                        .search('')
                        .draw();
                }

            });

            $('#num_filter').on('keyup', function () {
                table
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#date_filter').on('keyup', function () {
                table
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#customer_filter').on('keyup', function () {
                table
                    .columns(4)
                    .search(this.value)
                    .draw();
            });
            $('#object_filter').on('keyup', function () {
                table
                    .columns(5)
                    .search(this.value)
                    .draw();
            });
            $('#assigned_filter').on('keyup', function () {
                table
                    .columns(6)
                    .search(this.value)
                    .draw();
            });

            $('#tz_filter').on('keyup', function () {
                table
                    .columns(7)
                    .search(this.value)
                    .draw();
            });

            $('#cost_filter').on('keyup', function () {
                table
                    .columns(8)
                    .search(this.value)
                    .draw();
            });

            $('#request_filter').on('keyup', function () {
                table
                    .columns(9)
                    .search(this.value)
                    .draw();
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

    </script>
    <script>

        window.onload = function () {

            //показываем инпуты зернового состава и морозостойкости
            var label = document.getElementById("sredType").value;

            switch (label) {
                case 'zern_№2':
                case 'zern':
                    document.getElementById("zern_table").style.display = 'block';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'grunt_№2':
                case 'grunt':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'block';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'prirod_№2':
                case 'prirod':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'block';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'tu_12801_№2':
                case 'tu_12801':
                case 'gost31015':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'block';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'tu_183_№2':
                case 'tu_183':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'block';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'tu_183_2':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'block';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'osk4':
                    document.getElementById("zern_table").style.display = 'block';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'osk3':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'block';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'shps_№2':
                case 'shps':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'block';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'zern_sheb_№2':
                case 'sheb':
                case 'zern_sheb':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'block';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'sheb_shlak_№2':
                case 'sheb_shlak':
                case 'sheb_shlak_g3344':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'block';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'frost':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'block';

                    document.getElementById("metric_method").style.display = 'none';

                    break;

                case 'metric_method':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';
                    document.getElementById("metric_method").style.display = 'block';
                    break;

                default:
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

            }


            var status = document.getElementById("status").value;

            if (status == "Выдан") {
                var input = document.querySelectorAll("input");
                var select = document.querySelectorAll("select");
                for (var i = 0, cnt = input.length; i < cnt; i++) {

                    if (input[i].className != 'user-file' && input[i].className != 'send-file') {
                        input[i].disabled = true;
                    }
                }
                for (var i = 0, cnt = select.length; i < cnt; i++) {
                    select[i].disabled = true;
                }
                document.getElementById('protDownload').style.display = 'none';
            }

        };


    </script>
    <script>

        function Selected(sredType) {
            var label = sredType.value;
            console.log('label2', label);
            switch (label) {
                case 'zern_№2':
                case 'zern':
                    document.getElementById("zern_table").style.display = 'block';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'grunt_№2':
                case 'grunt':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'block';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'prirod_№2':
                case 'prirod':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'block';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'tu_12801_№2':
                case 'tu_12801':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'block';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'tu_183_№2':
                case 'tu_183':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'block';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'tu_183_2':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'block';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'osk4':
                    document.getElementById("zern_table").style.display = 'block';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'osk3':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'block';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'shps_№2':
                case 'shps':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'block';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'zern_sheb_№2':
                case 'sheb':
                case 'zern_sheb':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'block';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'sheb_shlak_№2':
                case 'sheb_shlak':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'block';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

                    break;
                case 'frost':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'block';

                    document.getElementById("metric_method").style.display = 'none';

                    break;

                case 'metric_method':
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';
                    document.getElementById("metric_method").style.display = 'block';
                    break;

                default:
                    document.getElementById("zern_table").style.display = 'none';
                    document.getElementById("zern_table2").style.display = 'none';
                    document.getElementById("zern_table3").style.display = 'none';
                    document.getElementById("zern_table4").style.display = 'none';
                    document.getElementById("zern_table5").style.display = 'none';
                    document.getElementById("zern_table6").style.display = 'none';
                    document.getElementById("zern_table7").style.display = 'none';
                    document.getElementById("zern_table8").style.display = 'none';
                    document.querySelector(".tableFrost").style.display = 'none';

                    document.getElementById("metric_method").style.display = 'none';

            }
        };


    </script>

    <script>

        $('button.btnPdf').on('click', function (event) {
            event.preventDefault();
            $('div.uploadProtocol').css('display', 'block');


            if ($(this).hasClass('add-pdf')) {

                let idPdfProtocol = $(this).data('id-pdf') ? $(this).data('id-pdf') : '';

                $('div.uploadProtocol').find('form').prepend('<input type="hidden" name="id_pdf_protocol" value="' + idPdfProtocol + '">');
            }
        });
    </script>
    <script>
        $(document).ready(function () {

            $('input[name=TEMP_O]').on('input', function () {
                let valTempOf = $(this).val();
                $('input[name=TEMP_TO_O]').val(valTempOf);

            });

            $('input[name=VLAG_O]').on('input', function () {
                var valTempOf = $(this).val();

                $('input[name=VLAG_TO_O]').val(valTempOf);
            });

            $('.table-results').on('input', '.temperature-before', function () {
                let currentValue = $(this).val();
                let currentTr = $(this).closest('tr');
                $(currentTr).find('.temperature-after').val(currentValue);
            });

            $('.table-results').on('input', '.humidity-before', function () {
                let currentValue = $(this).val();
                let currentTr = $(this).closest('tr');
                $(currentTr).find('.humidity-after').val(currentValue);
            });
        });

    </script>
    <script>

        $(document).ready(function () {

            var container = $('.workarea-content-paddings'),
                scroll = $('.dataTable.no-footer').width(),
                topTable = $('.workarea-content-paddings').offset().top,
                topTableBody = $('.dataTable.no-footer > tbody').offset().top,
                sumHeaderHeight = topTableBody - topTable;

            $('#buttonRightTable').css('top', sumHeaderHeight + "px");
            $('#buttonLeftTable').css('top', sumHeaderHeight + "px");

            $('#buttonRightTable').hover(function () {
                container.animate({
                    'scrollLeft': scroll
                }, {duration: 4000, queue: false});
            }, function () {
                container.stop();
            });
            $('#buttonLeftTable').hover(function () {
                container.animate({
                    'scrollLeft': -scroll
                }, {duration: 4000, queue: false});
            }, function () {
                container.stop();
            });

            $(document).scroll(function () {
                var positionScroll = $(window).scrollTop();
                if (positionScroll > 250 && positionScroll < 2550) {
                    $('#buttonRightTable').css('transform', 'translateY(' + (positionScroll - 250) + 'px' + ')');
                    $('#buttonLeftTable').css('transform', 'translateY(' + (positionScroll - 250) + 'px' + ')');
                }
            });

        });
    </script>
    <script>

        $(document).ready(function () {

            const btnCreateProtocol = $('.btn-create-protocol');
			const btnAnchorProtocol = $('.btn-anchor-sample');
            var checkboxSamples = $('.checkbox-proba');
            var tableResults = $('.table-results');

            const wrepperModal = $('.wrepper-modal');
            const modalTitle = $('.modal-title');
            let totalSamples = $('.checkbox-proba').length ? $('.checkbox-proba').length : 0;
            let totalSamplesUsed = 0;

            function verificationSelectedSamples(_this) {


                let quantitySamplesWithoutNumber = 0;

                let mismatchСounter = 0;

                let urlParams = getUrlSearchParams();

                let idZ = urlParams.get('ID');

                let arraySelectedSampleData = [];

                let currentProtocolId = _this.data('id-protocol') ? _this.data('id-protocol') : 0;

                $('.checkbox-proba').each(function (iCheckboxProba, vCheckboxProba) {

                    if ($(vCheckboxProba).prop('checked') && !$(vCheckboxProba).prop('disabled')) {

                        let valueAttrNameCheckboxProba = $(vCheckboxProba).attr('name');

                        let selectedSampleData = valueAttrNameCheckboxProba ? valueAttrNameCheckboxProba.slice(14, -1).split('][') : '';

                        arraySelectedSampleData.push(selectedSampleData);

                        quantitySamplesWithoutNumber++;
                    }
                });


                if (idZ && arraySelectedSampleData) {

                    $.ajax({
                        method: 'POST',
                        url: 'checking_results_isp.php',
                        data: {
                            id_z: idZ,
                            current_protocol_id: currentProtocolId,
                            array_selected_sample_data: arraySelectedSampleData,
                            quantity_samples_without_number: quantitySamplesWithoutNumber,
                            check: 'count'
                        },
                        async: false,
                        success: function (validationData) {

                            let numberValidationData = Number(validationData);

                            if (numberValidationData) {
                                mismatchСounter++;
                            }
                        }
                    });
                }

                return mismatchСounter;
            }

            $('#insert_str').on('click', function () {
                var inputSumStr = $('#sum_str');

                if ($(this).prop('checked')) {
                    inputSumStr.prop('disabled', false);
                } else {
                    inputSumStr.prop('disabled', true);
                }
            });

            function getUrlSearchParams() {

                let queryString = window.location.search;

                let urlParams = new URLSearchParams(queryString);

                return urlParams;
            }


            $('.form-protocol').on('click', function (event) {
                const _this = $(this);
				let anchorSample = $('.anchor-sample') ? $('.anchor-sample').val() : 0;
                let mismatchСounter;

                var checkingSamples = 0;


                $('.checkbox-proba').each(function (iSelectedSample, vSelectedSample) {

                    if ($(vSelectedSample).prop('checked')) {
                        checkingSamples = 1;
                    }
                });


                if (!checkingSamples) {
                    event.preventDefault();

                    modalTitle.text('Для формирования протокола, выберите пожалуйста пробу!');
                    wrepperModal.toggleClass('show');
                }
				
				if (anchorSample) {
                    let mismatchСounter = false;
                } else {
                    let mismatchСounter = verificationSelectedSamples(_this);
                }
                

                if (mismatchСounter) {
                    event.preventDefault();

                    modalTitle.text('Количество выбранных проб для присвоения номера не соответствует количеству проб при формировании протокола!');
                    wrepperModal.toggleClass('show');
                }


            });


            $('.add-number-protocol').on('click', function (event) {


                let urlParams = getUrlSearchParams();

                let idZ = urlParams.get('ID');



                let checkFormationProtocol = 0;
                let currentProtocolId = $(this).data('id-protocol') ? $(this).data('id-protocol') : 0;
                const _this = $(this);
				let dateCheckProtocol = '';
                let anchorSample = $('.anchor-sample') ? $('.anchor-sample').val() : 0;

                let mismatchСounter;

                if (idZ) {
                    $.ajax({
                        method: 'POST',
                        url: 'checking_results_isp.php',
                        data: {id_z: idZ, current_protocol_id: currentProtocolId, check: 'dateProbe'},
                        async: false,
                        success: function (dateCheck) {

                            if (dateCheck) {
                                dateCheckProtocol = dateCheck;

                            }
                        }
                    });
                }

                if (dateCheckProtocol) {
                    event.preventDefault();


                    modalTitle.text(dateCheckProtocol);
                    wrepperModal.toggleClass('show');
                    return true;
                }


                if (idZ) {
                    $.ajax({
                        method: 'POST',
                        url: 'checking_results_isp.php',
                        //data: {id_z: idZ, check: 'formation'},
                        data: {id_z: idZ, current_protocol_id: currentProtocolId, check: 'formation'},
                        async: false,
                        success: function (formationCheck) {

                            if (!formationCheck) {
                                checkFormationProtocol++;

                            }
                        }
                    });
                }

                if (checkFormationProtocol) {
                    event.preventDefault();


                    modalTitle.text('Сформируйте протокол! Протокол не сформирован!');
                    wrepperModal.toggleClass('show');
                    return true;
                }

                if (anchorSample) {
                    let mismatchСounter = false;
                } else {
                    let mismatchСounter = verificationSelectedSamples(_this);
                }


                if (mismatchСounter) {
                    event.preventDefault();

                    modalTitle.text('Количество выбранных проб для присвоения номера не соответствует количеству проб при формировании протокола!');
                    wrepperModal.toggleClass('show');
                }
            });


            var countSelectedSamples = function () {
                var counterSelectedSamples = 0;

                checkboxSamples.each(function (iCheckboxProba, vCheckboxProba) {

                    if ($(vCheckboxProba).prop('checked') && !$(vCheckboxProba).prop('disabled')) {
                        counterSelectedSamples++;
                    }


                    if ($(vCheckboxProba).prop('disabled')) {
                        totalSamplesUsed++;
                    }
                });

                return counterSelectedSamples;
            }


            let sumSelectedSamples = countSelectedSamples();


            if (totalSamples === totalSamplesUsed) {
                btnCreateProtocol.prop('disabled', true);
				btnAnchorProtocol.prop('disabled', true);
            }


            var changeCheckboxProba = function () {
                if ($(this).prop('checked')) {
                    console.log('change');
                    btnCreateProtocol.prop('disabled', false);
                    $('.table-results').off('change', '.checkbox-proba', changeCheckboxProba);
                }
            }


            if (!sumSelectedSamples) {
                console.log('sumSelectedSamples', sumSelectedSamples);

                $('.table-results').on('change', '.checkbox-proba, .all-checkbox-proba', changeCheckboxProba);
            }


            $('.btn-create-protocol').on('click', function (event) {


                var sumSelectedSamples = countSelectedSamples();
                console.log('sumSelectedSamples2', sumSelectedSamples);


                if (!sumSelectedSamples) {
                    event.preventDefault();
					modalTitle.text('Для формирования протокола, выберите пожалуйста пробу!');
                    wrepperModal.toggleClass('show');
                }

            });

            $('.btn-modal-close').on('click', function () {
                wrepperModal.removeClass('show');
            });


            let inputTableDisabled = $('.tableDisabled').find('input');
            console.log(inputTableDisabled, inputTableDisabled);
            if (inputTableDisabled) {
                inputTableDisabled.each(function (i, v) {
                    $(v).prop('disabled', true);
                });
            }

			let selectTableDisabled = $('.tableDisabled').find('select');

            if (selectTableDisabled) {
                selectTableDisabled.each(function (iSelect, vSelect) {
                    $(vSelect).prop('disabled', true);
                });
            }

            $('.all-checkbox-proba').on('click', function () {
                $('.checkbox-proba').each(function (key, checkbox) {
                    let checkBox = $(checkbox);
                    if (!checkBox.prop('disabled')) {
                        checkBox.prop('checked', !checkBox.prop('checked'));
                    }
                });
            });
			
			$('.save-changes').on('click', function (event) {
             
                let dateBegin = $('.date-begin').val() ? $('.date-begin').val() : '';
                let dateEnd = $('.date-end').val() ? $('.date-begin').val() : '';

                let urlParams = getUrlSearchParams();
                let idZ = urlParams.get('ID');
                let currentProtocolId = $(this).data('id-protocol') ? $(this).data('id-protocol') : 0;
                let dateCheckProtocol = '';

                if ((!dateBegin || !dateEnd) && $('.no-bitrix').prop('checked')) {
                    
                    if (idZ) {
                        $.ajax({
                            method: 'POST',
                            url: 'checking_results_isp.php',
                            data: {id_z: idZ, current_protocol_id: currentProtocolId, check: 'dateProbe'},
                            async: false,
                            success: function (dateCheck) {

                                if (dateCheck) {
                                    dateCheckProtocol = dateCheck;

                                }
                            }
                        });
                    }

                    if (dateCheckProtocol) {
                        event.preventDefault();


                        modalTitle.text(dateCheckProtocol);
                        wrepperModal.toggleClass('show');
                        return true;
                    }
                }
            });


            $('.no-attestat').on('click', function () {
                if ($('.no-attestat').prop('checked')) {
                    $('.hidden-no-attestat').val(1);
                } else {
                    $('.hidden-no-attestat').val(0);
                }
            });

        });

    </script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
