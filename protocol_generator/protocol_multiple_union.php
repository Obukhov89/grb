<?php
/**
 * Created by Makaralex92.
 * User: Алексей
 * Date: 07.05.2019
 * Time: 16:11
 */

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;
use  NcJoes\OfficeConverter\OfficeConverter;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpqrcode/qrlib.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';

require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php');

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

$queryString = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';

function console_log($data, $info = 'RESULT'){
    if(is_array($data) || is_object($data)){
        echo("<script>console.log('" . $info . ": ". json_encode(print_r($data, true)) ."');</script>");
    } else {
        echo("<script>console.log('" . $info . ": ".$data."');</script>");
    }
}

$dbc = new CDB();


$oborud_check=1;
$oborudToGostCheck=1;
$temp_in_ob_check =1;
$vlag_in_ob_check=1;
$temp_in_gost_check=1;
$vlag_in_gost_check=1;
$invoice_check=1;
$paymentCheck = 1;
$ID = $_GET['ID'];
$TZ_ID = $_GET['TZ_ID'];
$deal = CCrmDeal::GetByID($ID);
$company = CCrmCompany::GetByID($deal['COMPANY_ID']);
$ispolnitel = '';
$user = CUser::GetByID($deal['ASSIGNED_BY_ID'])->arResult[0];
$y_date = date("Y", strtotime($deal['DATE_CREATE']));
			if ($y_date != '2020') $y_date = "/".substr($y_date, 2);
			else $y_date = "/".$y_date;

$workpos = $user['WORK_POSITION'];
$ispolnitel = mb_substr($user['NAME'], 0, 1, 'UTF-8') . '. ' . (($user['SECOND_NAME']) ? mb_substr($user['SECOND_NAME'], 0, 1, 'UTF-8') . '. ' : '') . $user['LAST_NAME'];

global $DB;
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . $TZ_ID);
$ba_tz = $res->Fetch();


$protocolId = !empty($_GET['PROTOCOL_ID']) ? $_GET['PROTOCOL_ID'] : '';


$сurrentDataProtocol = null;
if (!empty($protocolId)) {
    $queryCurrentDataProtocol = "SELECT * FROM `PROTOCOLS` WHERE ID={?}";
    $arrDataProtocol = [$protocolId];

    $сurrentDataProtocol = $dbc->selectRow($queryCurrentDataProtocol, $arrDataProtocol);
}


$protocolNum = !empty($сurrentDataProtocol['NUMBER']) ? (int)$сurrentDataProtocol['NUMBER'] : null;

$protocolDateUpdatedAt = !empty($сurrentDataProtocol['UPDATED_AT']) ? dateObjectFormat($сurrentDataProtocol['UPDATED_AT'], "Y-m-d") : null;

$protocolYearUpdatedAt = !empty($сurrentDataProtocol['UPDATED_AT']) ? dateObjectFormat($сurrentDataProtocol['UPDATED_AT'], "Y") : '';

$protocolDate = !empty($сurrentDataProtocol['DATE']) ? dateObjectFormat($сurrentDataProtocol['DATE'], "Y-m-d") : $protocolDateUpdatedAt;

$protocolYear = !empty($сurrentDataProtocol['DATE']) ? dateObjectFormat($сurrentDataProtocol['DATE'], "Y") : $protocolYearUpdatedAt;


$selectedProba = !empty($сurrentDataProtocol['SAMPLE_CHECKED']) ? unserialize($сurrentDataProtocol['SAMPLE_CHECKED']) : '';

$protocolOutsideLis = !empty($сurrentDataProtocol['PROTOCOL_OUTSIDE_LIS']) ? $сurrentDataProtocol['PROTOCOL_OUTSIDE_LIS'] : null;

$verify = !empty($сurrentDataProtocol['VERIFY']) ? unserialize($сurrentDataProtocol['VERIFY']) : '';


$type_tz = isset($сurrentDataProtocol['PROTOCOL_TYPE']) ? $сurrentDataProtocol['PROTOCOL_TYPE'] : null;


$OSTATKI = !empty($сurrentDataProtocol['ostatki']) ? unserialize($сurrentDataProtocol['ostatki']) : [];
$SOstATKI = !empty($сurrentDataProtocol['SOSTAV']) ? unserialize($сurrentDataProtocol['SOSTAV']) : [];
$group_mat_value = isset($сurrentDataProtocol['GROUP_MAT']) ? $сurrentDataProtocol['GROUP_MAT'] : '';

$dopinfo = isset($сurrentDataProtocol['DOP_INFO']) ? $сurrentDataProtocol['DOP_INFO'] : '';

$date1 = !empty($сurrentDataProtocol['DATE_BEGIN']) ? dateObjectFormat($сurrentDataProtocol['DATE_BEGIN'], "Y-m-d") : null;
$date2 = !empty($сurrentDataProtocol['DATE_END']) ? dateObjectFormat($сurrentDataProtocol['DATE_END'], "Y-m-d") : null;

$temp_p = isset($сurrentDataProtocol['TEMP_O']) ? tofloat($сurrentDataProtocol['TEMP_O']) : null;
$temp_p2 = isset($сurrentDataProtocol['TEMP_TO_O']) ? tofloat($сurrentDataProtocol['TEMP_TO_O']) : null;
$vlag_p = isset($сurrentDataProtocol['VLAG_O']) ? tofloat($сurrentDataProtocol['VLAG_O']) : null;
$vlag_p2 = isset($сurrentDataProtocol['VLAG_TO_O']) ? tofloat($сurrentDataProtocol['VLAG_TO_O']) : null;


$selectionType = isset($сurrentDataProtocol['SELECTION_TYPE']) ? (int)$сurrentDataProtocol['SELECTION_TYPE'] : null;
$inAttestatDiapason = isset($сurrentDataProtocol['IN_ATTESTAT_DIAPASON']) ? (int)$сurrentDataProtocol['IN_ATTESTAT_DIAPASON'] : null;
$noCompliance = isset($сurrentDataProtocol['NO_COMPLIANCE']) ? (int)$сurrentDataProtocol['NO_COMPLIANCE'] : null;
$attestatInProtocol = isset($сurrentDataProtocol['ATTESTAT_IN_PROTOCOL']) ? (int)$сurrentDataProtocol['ATTESTAT_IN_PROTOCOL'] : null;
$description = !empty($сurrentDataProtocol['DESCRIPTION']) ? $сurrentDataProtocol['DESCRIPTION'] : '';
$probeCurrentProtocol = !empty($сurrentDataProtocol['PROBE']) ? unserialize($сurrentDataProtocol['PROBE']) : [];

$object = !empty($сurrentDataProtocol['OBJECT']) ? $сurrentDataProtocol['OBJECT'] : '';
$temperatureBefore = !empty($сurrentDataProtocol['TEMPERATURE_BEFORE']) ? json_decode($сurrentDataProtocol['TEMPERATURE_BEFORE'], JSON_FORCE_OBJECT) : [];
$temperatureAfter = !empty($сurrentDataProtocol['TEMPERATURE_AFTER']) ? json_decode($сurrentDataProtocol['TEMPERATURE_AFTER'], JSON_FORCE_OBJECT) : [];
$humidityBefore = !empty($сurrentDataProtocol['HUMIDITY_BEFORE']) ? json_decode($сurrentDataProtocol['HUMIDITY_BEFORE'], JSON_FORCE_OBJECT) : [];
$humidityAfter = !empty($сurrentDataProtocol['HUMIDITY_AFTER']) ? json_decode($сurrentDataProtocol['HUMIDITY_AFTER'], JSON_FORCE_OBJECT) : [];
$temperatureHumidityCheck = !empty($сurrentDataProtocol['TEMPERATURE_HUMIDITY_CHEK']) ? json_decode($сurrentDataProtocol['TEMPERATURE_HUMIDITY_CHEK'], JSON_FORCE_OBJECT) : [];

//$resFrost = $DB->Query("SELECT * FROM `b_frost` WHERE `tz_id`=" . $TZ_ID);
$resFrost = $DB->Query("SELECT * FROM `b_frost` WHERE `tz_id`=" . $TZ_ID . " AND `protocol_id`=" . $protocolId);
$rowFrost = $resFrost->Fetch();



$y_date_act = date("Y", strtotime($ba_tz['DATE_ACT']));
					if ($y_date_act != '2020') $y_date_act = "/".substr($y_date_act, 2);
							else $y_date_act = "/".$y_date_act;
					

                    $y_date_p = date("Y", strtotime($protocolDate));
                    if ($y_date_p != '2020') $y_date_p = "/".substr($y_date_p, 2);
                    else $y_date_p = "/".$y_date_p;

$tz = unserialize($ba_tz['TZ']);
$labs = unserialize($ba_tz['LAB']);
$probe = unserialize($ba_tz['PROBE']);
$results_tz = unserialize($ba_tz['RESULTS']);

$MATCH_MANUAL = unserialize($ba_tz['MATCH_MANUAL']);
$SRED = unserialize($ba_tz['SRED']);

$NORM_DESC = unserialize($ba_tz['NORM_DESC']);

$metod_otbor = $ba_tz['OSK_METOD_OTBOR_ID'];

$description = sup_sub_index_italic($description); 				   

$curdate = "Протокол_".($protocolNum ? $protocolNum : '')."_".date("d-m-Y_H-i-s");

$curdateEng = null;
$curdatePdf = null;

if (!empty($protocolNum) && is_numeric($protocolNum)) {

    $curdateEng = "Protocol_" . $protocolNum;
    $curdatePdf = "Протокол_" . $protocolNum;
}

$insert_str = !empty($ba_tz['INSERT_STR']) ? $ba_tz['INSERT_STR'] : '';
$sum_str = !empty($ba_tz['SUM_STR']) ? $ba_tz['SUM_STR'] : '';

$accuracy=[];

$controlDamage = !empty($rowFrost['control_damage']) ? str_replace('.', ',', $rowFrost['control_damage']) : '';
$mainDamage = !empty($rowFrost['main_damage']) ? str_replace('.', ',', $rowFrost['main_damage']) : '';
$controlMass = !empty($rowFrost['control_mass']) ? str_replace('.', ',', $rowFrost['control_mass']) : '';

$mainMass = !empty($rowFrost['main_mass']) ? str_replace('.', ',', number_format($rowFrost['main_mass'],2)) : '';
$controlStrength = !empty($rowFrost['control_strength']) ? unserialize($rowFrost['control_strength']) : '';
$mainStrength = !empty($rowFrost['main_strength']) ? unserialize($rowFrost['main_strength']) : '';

$controlMedium = !empty($rowFrost['control_medium']) ? str_replace('.', ',', number_format($rowFrost['control_medium'],1)) : '';

$mainMedium = !empty($rowFrost['main_medium']) ? str_replace('.', ',', number_format($rowFrost['main_medium'],1)) : '';

$controlBottomLine = !empty($rowFrost['control_bottom_line']) ? str_replace('.', ',', number_format($rowFrost['control_bottom_line'],2)) : '';

$mainBottomLine = !empty($rowFrost['main_bottom_line']) ? str_replace('.', ',', number_format($rowFrost['main_bottom_line'],2)) : '';
$ratio = !empty($rowFrost['ratio']) ? str_replace('.', ',', $rowFrost['ratio']) : '';
$tested = null;
$match = null;

if ($ratio == 'соблюдается') {
    $tested = 'выдержали';
    $match = 'соответствуют';
} else if ('не соблюдается') {
    $tested = 'не выдержали';
    $match = 'не соответствуют';
}


$date1arr = explode('-',$date1);
$date1 = $date1arr[2].'.'.$date1arr[1].'.'.$date1arr[0];
$date2arr = explode('-',$date2);
$date2 = $date2arr[2].'.'.$date2arr[1].'.'.$date2arr[0];

$verify_arr=[];

foreach($verify as $kye_v => $v)
	{
		$user_v = CUser::GetByID($v)->Fetch();
		
		$IspolnitelName2 = '';

		$IspolnitelName = mb_substr($user_v['NAME'], 0, 1, 'UTF-8') . '. ' . (($user_v['SECOND_NAME']) ? mb_substr($user_v['SECOND_NAME'], 0, 1, 'UTF-8') . '. ' : '') . $user_v['LAST_NAME'];

		if (!empty($type_tz) && in_array($type_tz, [33, 34, 35, 36, 37, 38, 39])) {

			$currentDate = date('H:i:s');

			$dateOfSignatureRandom = date('H:i:s', (int)strtotime($currentDate) + (int)rand(60, 300));
			

			$dateOfSignature = !empty($kye_v) ? $dateOfSignatureRandom : $currentDate;
			

			$IspolnitelName2 = 'подписано ' . $IspolnitelName;

		}
		
		$verify_arr[] = [
		'WorkPos' => $user_v['WORK_POSITION'],
		'Ispolnitel' => mb_substr($user_v['NAME'], 0, 1, 'UTF-8') . '. ' . (($user_v['SECOND_NAME']) ? mb_substr($user_v['SECOND_NAME'], 0, 1, 'UTF-8') . '. ' : '') . $user_v['LAST_NAME'],
		'Ispolnitel2' => $IspolnitelName2,
		'IC' => ($user_v['UF_DEPARTMENT'][0] == 58 ? '' : ' ИЦ')
		];
		
	}

	$numberObject = 0;
	$thermalConductivityDatasetArray = [];
	$makros15 = '';
	$makros16 = '';
	

	if($type_tz == 37 && !empty($ba_tz['ID'])) {

		$thermalConductivityDataset = $DB->Query("SElECT * FROM `PROTOCOL_THERMAL_CONDUCTIVITY` WHERE `tz_id`=" . (int)$ba_tz['ID']);

		if (!empty($thermalConductivityDataset)) {
			while ($thermalConductivityData = $thermalConductivityDataset->Fetch()) {
				
				$makros1 = isset($thermalConductivityData['makros1']) ? $thermalConductivityData['makros1'] : '';
				$makros2 = isset($thermalConductivityData['makros2']) ? $thermalConductivityData['makros2'] : '';
				$makros3 = isset($thermalConductivityData['makros3']) ? $thermalConductivityData['makros3'] : '';
				$makros4 = isset($thermalConductivityData['makros4']) ? $thermalConductivityData['makros4'] : '';
				$makros5 = isset($thermalConductivityData['makros5']) ? $thermalConductivityData['makros5'] : '';
				$makros6 = isset($thermalConductivityData['makros6']) ? $thermalConductivityData['makros6'] : '';
				$makros7 = isset($thermalConductivityData['makros7']) ? $thermalConductivityData['makros7'] : '';
				$makros8 = isset($thermalConductivityData['makros8']) ? $thermalConductivityData['makros8'] : '';
				$makros9 = isset($thermalConductivityData['makros9']) ? $thermalConductivityData['makros9'] : '';
				$makros10 = isset($thermalConductivityData['makros10']) ? $thermalConductivityData['makros10'] : '';
				$makros11 = isset($thermalConductivityData['makros11']) ? $thermalConductivityData['makros11'] : '';
				$makros12 = isset($thermalConductivityData['makros12']) ? $thermalConductivityData['makros12'] : '';
				$makros13 = isset($thermalConductivityData['makros13']) ? $thermalConductivityData['makros13'] : '';
				$makros14 = isset($thermalConductivityData['makros14']) ? $thermalConductivityData['makros14'] : '';
				
				if (empty($numberObject)) {
					$makros15 = !empty($thermalConductivityData['makros15']) ? $thermalConductivityData['makros15'] : '';
					$makros16 = !empty($thermalConductivityData['makros16']) ? $thermalConductivityData['makros16'] : '';
				}
				
				
				$thermalConductivityDatasetArray[] = [
					'Makros1' => $makros1,
					'Makros2' => $makros2,
					'Makros3' => $makros3,
					'Makros4' => $makros4,
					'Makros5' => $makros5,
					'Makros6' => $makros6,
					'Makros7' => $makros7,
					'Makros8' => $makros8,
					'Makros9' => $makros9,
					'Makros10' => $makros10,
					'Makros11' => $makros11,
					'Makros12' => $makros12,
					'Makros13' => $makros13,
					'Makros14' => $makros14
				];
				
				$numberObject++;
			}
		}
	}

$lab = [];
foreach ($labs as $items) {
    foreach ($items as $item) {
        foreach ($item as $lab_id) {
            $res = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID`=" . $lab_id);
            $ba_laba = $res->Fetch();
            if (!in_array($ba_laba['NAME'], $lab))
                $lab[] = $ba_laba['NAME'];

        }
    }
}
$lab = implode(", ", $lab);


if ($type_tz == 0) 
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob.docx');

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
} 
elseif ($type_tz == 5)
{
    if ($TZ_ID == 6534) {
        $file = new Bitrix\Main\IO\File(__DIR__ . '/../new_templates/Protocol_ob_zern.docx');
    } else {
        $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern.docx');
    }

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 6)
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_grunt.docx');

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
}
elseif($type_tz == 7)
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern2.docx');

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
}
elseif($type_tz == 8)
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_asfalt12801.docx');

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
}
elseif($type_tz == 9)
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_asfaltPNST183.docx'); 

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
}
elseif($type_tz == 1||$type_tz == 2||$type_tz == 3||$type_tz == 4) 
{
    if ($TZ_ID == 6644) {
        $file = new Bitrix\Main\IO\File(__DIR__ . '/../new_templates/Protocol2_ob.docx');
    } else {
        $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol2_ob.docx');
    }

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 10) 
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_osk.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 11) 
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_osk_road.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 12) 
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_osk_road_pnst184.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 13) 
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_osk_road_pesok.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 14) 
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_osk_road_sred.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 15) 
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_shps.docx');

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 16)
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_asfaltPNST183_2.docx'); 

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
}
elseif($type_tz == 17) 
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_osk_road_sheben.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}	
elseif($type_tz == 18) 
{

    if ($TZ_ID == 6534) {
        if ( ! empty($group_mat_value) && ($group_mat_value == '94-2' || $group_mat_value == '94-3' || $group_mat_value == '94-4' || $group_mat_value == '94-5' || $group_mat_value == '94-6' || $group_mat_value == '94-7')) {

            $file = new Bitrix\Main\IO\File(__DIR__ . '/../new_templates/Protocol_ob_zern_sheb_shlak_20_40.docx');
        } else {

            $file = new Bitrix\Main\IO\File(__DIR__ . '/../new_templates/Protocol_ob_zern_sheb_shlak.docx');
        }
    } else {
        if ( ! empty($group_mat_value) && ($group_mat_value == '94-2' || $group_mat_value == '94-3' || $group_mat_value == '94-4' || $group_mat_value == '94-5' || $group_mat_value == '94-6' || $group_mat_value == '94-7')) {

            $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern_sheb_shlak_20_40.docx');
        } else {

            $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern_sheb_shlak.docx');
        }
    }

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 19) 
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern_sheb.docx');

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}			   
elseif($type_tz == 20)
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_osk_density_grunt.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif ($type_tz == 21)
{
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern_№2.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 22)
{
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_grunt_№2.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
}
elseif($type_tz == 23)
{
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern2_№2.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
}
elseif($type_tz == 24)
{
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_asfalt12801_№2.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
}
elseif($type_tz == 25)
{ 
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_asfaltPNST183_2_№2.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
}	
elseif($type_tz == 26)
{ 
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_asfaltPNST183_№2.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
}
elseif($type_tz == 27) 
{
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern_sheb_№2.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 28) 
{
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_shps_№2.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}
elseif($type_tz == 29) 
{

	if (!empty($group_mat_value) && $group_mat_value == '94-5') {
		$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern_sheb_shlak_20_40_№2.docx');
	} else {
		$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern_sheb_shlak_№2.docx');
	}
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
} elseif ($type_tz == 30) {
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_frost.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
} elseif ($type_tz == 31) {
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_metric_method.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
} elseif($type_tz == 32) {
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_asfalt12801_31015.docx');

    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
} elseif($type_tz == 33) {
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_short_simple.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());	
} elseif($type_tz == 34) { 
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_short.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
} elseif($type_tz == 35) {
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern_short_simple.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
} elseif($type_tz == 36) { 
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern2_short_simple.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
} elseif($type_tz == 37) {
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_short_thermal_conductivity.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
} elseif($type_tz == 38) { 
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern_sheb_shlak_20_40_№2_simple.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents()); 
} elseif($type_tz == 39) { 
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern_sheb_shlak_20_40_simple.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents()); 
} elseif($type_tz == 40) { 
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_shps_25607new.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents()); 
}



$oborud = [];
$results = [];
$resultszern = [];
$number_probes = [];
$numbers = [];
$ob_arr = [];
$arr_soot = [
    0 => 'Не соответствует',
    1 => 'Соответствует',
    2 => '-',
	3 => 'Не нормируется'
];


foreach ($probe as $keyCount => $prob) {


    if (empty($selectedProba[$keyCount - 1])) {
        continue;
    }


    $arrayGostKeySelectedSample = !empty($selectedProba[$keyCount - 1]) ? array_keys($selectedProba[$keyCount - 1]) : [];

    $gostKeySelectedSample = !empty($arrayGostKeySelectedSample[0]) ? (int)$arrayGostKeySelectedSample[0] : 0;

    foreach ($prob as $key => $item) {

        $arrayNumberProbe = [];
        $arrayShNumber = [];

        switch ($key) {
            case 'number_probe':

                foreach ($item as $keyProbe => $nameProbe) {



                    if (!in_array($keyProbe + 1, array_keys($selectedProba[$keyCount - 1][$gostKeySelectedSample]))) {
                        continue;
                    }

                    $arrayNumberProbe[$keyProbe] = $nameProbe;

                }

                $number_probes = array_merge($number_probes, $arrayNumberProbe);

                break;
            case 'sh_number':

                foreach ($item as $keyProbe => $nameProbe) {


                    if (!in_array($keyProbe + 1, array_keys($selectedProba[$keyCount - 1][$gostKeySelectedSample]))) {
                        continue;
                    }

                    $arrayShNumber[$keyProbe] = $nameProbe;
                }

                $numbers = array_merge($numbers, $arrayShNumber);

                break;
        }
    }
}


$ch=1; $tz_ob=0;

if ($ID < getIdDealAfterWhichOborudConnectedGost()) {
    $res_ob = $DB->Query("SELECT * FROM `TZ_OB_CONNECT` WHERE `ID_TZ` = " . $ID . " AND `PROTOCOL_ID`=" . $protocolId);
    if (intval($res_ob->SelectedRowsCount()) > 0) {
        while ($row = $res_ob->Fetch()) {

            $ob = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . $row['ID_OB'])->Fetch();
            $poverka_time = 5184000;

										$poverka = strtotime($ob['POVERKA']) - strtotime($date2);//time();

										$temp_o1 = (float)$ob['TOO_EX'];
										$temp_o2 = $ob['TOO_EX2'];

										
										 if (($temp_p < $temp_o1 || $temp_p2 > $temp_o2) && empty($ob['TEMPERATURE'])) {$temp_in_ob_check=0; echo "Внимание! Температура при проведении испытаний не соответствует условиям эксплуатации оборудования <a href='/oborud.php?ID=".$ob['ID']."'>"  . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] . "</a>! <br><br>";}

										$vlag_o1 = (float)$ob['OVV_EX'];
										$vlag_o2 = $ob['OVV_EX2'];

										
										if (($vlag_p < $vlag_o1 || $vlag_p2 > $vlag_o2) && empty($ob['HUMIDITY'])) {$vlag_in_ob_check=0; echo "Внимание! Влажность при проведении испытаний не соответствует условиям эксплуатации оборудования <a href='/oborud.php?ID=".$ob['ID']."'>"   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] . "</a>! <br><br>";}

										if(!$ob['CHECKED']) {$oborud_check=0; echo "<p style='color:red'>Протокол не может быть сформирован! Оборудование <a href='/oborud.php?ID=".$ob['ID']."'>"  . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] .  "</a> не проверено! </p><br>"; }
										if($poverka<=0&&$ob['IDENT'] != "OOPP"&&$ob['IDENT'] != "VO") {$oborud_check=0; echo "<p style='color:red'>Протокол не может быть сформирован! Истек срок поверки оборудования <a href='/oborud.php?ID=".$ob['ID']."'>"   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] .  "</a>! </p><br>"; }
										if ($ob['IDENT'] != "VO"&&$ob['IDENT'] != "TS"&&$ob['IDENT'] != "REACT"){
										$res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$row['ID_OB']);
										$row_certificate = $res_certificate_select->Fetch();
										$certificateName_arr = unserialize($row_certificate['name_f']);
										$cert_arr=array();
										foreach($certificateName_arr as $cName)
										{
											$cert_arr[]=$cName;
										}
										$ob_v = ($ob['certificate_of_verification'] ? $ob['certificate_of_verification'] : '');
										$certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v;
										$ob_arr = [
											'Name' => $ob['OBJECT'] . ' ' . $ob['TYPE_OBORUD'] . ', ' . $ob['FACTORY_NUMBER'] . ', ' . $ob['REG_NUM'] . ', ' . date("d.m.Y", strtotime($ob['god_vvoda_expluatation'])),

											'Poverka' => $certificateName . ' до ' . date("d.m.Y", strtotime($ob['POVERKA'])),
										];
										
										if (!in_array($ob_arr, $oborud)) {
											$oborud[] = $ob_arr;
										}
										}

            $tz_ob = 1;

        }
    }
}

foreach ($tz as $keyItems => $items) {
	

    if (empty($selectedProba[$keyItems - 1])) {
        continue;
    }

    foreach ($items['gost'] as $k => $item) {
		
        if($item) 
		{
			$ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
			$gost = $ba_gost->Fetch();

			if (date('d.m.Y', strtotime($сurrentDataProtocol['DATE_END'])) >= date('d.m.Y',  strtotime('11.10.2021'))) {
				if (($gost['NUM_OA_NEW'] == 0)&&($gost['IN_OA'] == 1)) {
					echo "<p style='color: red'> Протокол не может быть сформирован! Используемая методика относится к старой области аккредитации!</p><br>";
					exit();
				}
			}

			$norm_dop = unserialize($gost["NORM_DOP"]);


			$arr_group_mat = !empty($group_mat_value) ? explode('-', $group_mat_value) : '';


			$key_group_mat = !empty($arr_group_mat[1]) ? $arr_group_mat[1] : ''; 
			
			$arr_mark_material = [
				'0' => 'F(1)25',
				'1' => 'F(1)35',
				'2' => 'F(1)50',
				'3' => 'F(1)75',
				'4' => 'F(1)100',
				'5' => 'F(1)150',
				'6' => 'F(1)200',
				'7' => 'F(1)300',
				'8' => 'F(1)400',
				'9' => 'F(1)500',
				'10' => 'F(1)600',
				'11' => 'F(1)800',
				'12' => 'F(1)1000',
				'13' => 'F(2)75',
				'14' => 'F(2)100',
				'15' => 'F(2)150',
				'16' => 'F(2)200',
				'17' => 'F(2)300',
				'18' => 'F(2)400',
				'19' => 'F(2)500',
				'20' => 'F(2)600',
				'21' => 'F(2)800',
				'22' => 'F(2)1000',
			];

			$mark_material = !empty($arr_mark_material[$key_group_mat]) ? $arr_mark_material[$key_group_mat] : '';


			$round = !empty($norm_dop[$key_group_mat][1]) ? $norm_dop[$key_group_mat][1] : ''; 

			$temp_g = unserialize($gost['TEMP']);
			$vlag_g = unserialize($gost['VLAG']);

            if ($ID < getIdDealAfterWhichOborudConnectedGost()) {
                if (($temp_p < (float)$temp_g[0] || $temp_p2 > tofloat($temp_g[1])) && empty($gost['TEMPERATURE'])) {
                    $temp_in_gost_check = 0;
                    echo "Внимание! Температура при проведении испытаний не соответствует условиям в методике <a href='/obl_acc.php?ID=" . $gost['ID'] . "'>" . $gost['GOST'] . " " . $gost['SPECIFICATION'] . "</a>! <br><br>";
                }
                //if (($temp_p<(float)$temp_g[0]||$temp_p2>tofloat($temp_g[1])) && empty($gost['TEMPERATURE']) && ($USER->GetID()!='11')) {$temp_in_gost_check=0; echo "Внимание! Температура при проведении испытаний не соответствует условиям в методике <a href='/obl_acc.php?ID=".$gost['ID']."'>" . $gost['GOST']. " " . $gost['SPECIFICATION'] . "</a>! <br><br>";}


                if (($vlag_p < (float)$vlag_g[0] || $vlag_p2 > (float)$vlag_g[1]) && empty($gost['HUMIDITY'])) {
                    $vlag_in_gost_check = 0;
                    echo "Внимание! Влажность при проведении испытаний не соответствует условиям в методике <a href='/obl_acc.php?ID=" . $gost['ID'] . "'>" . $gost['GOST'] . " " . $gost['SPECIFICATION'] . "</a>! <br><br>";
                }
                //if(($vlag_p<(float)$vlag_g[0]||$vlag_p2>(float)$vlag_g[1]) && empty($gost['HUMIDITY']) && ($USER->GetID()!='11')) {$vlag_in_gost_check=0; echo "Внимание! Влажность при проведении испытаний не соответствует условиям в методике <a href='/obl_acc.php?ID=".$gost['ID']."'>" . $gost['GOST']. " " . $gost['SPECIFICATION'] . "</a>! <br><br>";}

                if ( ! $tz_ob && $gost['ID']) {
                    $ba_connection = $DB->Query("SELECT * FROM `ba_connection` WHERE `GOST_ID`=" . $gost['ID']);

                    while ($oborud_id = $ba_connection->Fetch()) {
                        $ba_oborud = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . $oborud_id['OBORUD_ID']);
                        $ob = $ba_oborud->Fetch();
                        $poverka_time = 5184000;

                        $poverka = strtotime($ob['POVERKA']) - strtotime($date2);//time();

                        $temp_o1 = tofloat($ob['TOO_EX']);
                        $temp_o2 = $ob['TOO_EX2'];


                        if (($temp_p < $temp_o1 || $temp_p2 > $temp_o2) && empty($ob['TEMPERATURE'])) {
                            $temp_in_ob_check = 0;
                            echo "Внимание! Температура при проведении испытаний не соответствует условиям эксплуатации оборудования <a href='/oborud.php?ID=" . $ob['ID'] . "'>" . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD'] . " " . $ob['REG_NUM'] . "</a>! <br><br>";
                        }

                        $vlag_o1 = tofloat($ob['OVV_EX']);
                        $vlag_o2 = $ob['OVV_EX2'];

                        if (($vlag_p < $vlag_o1 || $vlag_p2 > $vlag_o2) && empty($ob['HUMIDITY'])) {
                            $vlag_in_ob_check = 0;
                            echo "Внимание! Влажность при проведении испытаний не соответствует условиям эксплуатации оборудования <a href='/oborud.php?ID=" . $ob['ID'] . "'>" . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD'] . " " . $ob['REG_NUM'] . "</a>! <br><br>";
                        }


                        if ( ! $ob['CHECKED']) {
                            $oborud_check = 0;
                            echo "<p style='color:red'>Протокол не может быть сформирован! Оборудование <a href='/oborud.php?ID=" . $ob['ID'] . "'>" . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD'] . " " . $ob['REG_NUM'] . "</a> не проверено! </p><br>";
                        }
                        if ($poverka <= 0 && $ob['IDENT'] != "OOPP" && $ob['IDENT'] != "VO") {
                            $oborud_check = 0;
                            echo "<p style='color:red'>Протокол не может быть сформирован! Истек срок поверки оборудования <a href='/oborud.php?ID=" . $ob['ID'] . "'>" . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD'] . " " . $ob['REG_NUM'] . "</a>! </p><br>";
                        }
                        if ($ob['IDENT'] != "VO" && $ob['IDENT'] != "TS" && $ob['IDENT'] != "REACT") {
                            $res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$ob['ID']);
                            $row_certificate = $res_certificate_select->Fetch();
                            $certificateName_arr = unserialize($row_certificate['name_f']);
                            $cert_arr = array();
                            foreach ($certificateName_arr as $cName) {
                                $cert_arr[] = $cName;
                            }
                            $ob_v = ($ob['certificate_of_verification'] ? $ob['certificate_of_verification'] : '');
                            $certificateName = $cert_arr[count($cert_arr) - 1] ? $cert_arr[count($cert_arr) - 1] : $ob_v;
                            $ob_arr = [
                                'Name' => $ob['OBJECT'] . ' ' . $ob['TYPE_OBORUD'] . ', ' . $ob['FACTORY_NUMBER'] . ', ' . $ob['REG_NUM'] . ', ' . date("d.m.Y",
                                        strtotime($ob['god_vvoda_expluatation'])),

                                'Poverka' => $certificateName . ' до ' . date("d.m.Y", strtotime($ob['POVERKA'])),
                            ];

                            if ( ! in_array($ob_arr, $oborud)) {
                                $oborud[] = $ob_arr;
                            }
                        }

                    }
                }
            }
			$ress = '';
			$res1 = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $items['gost_new'][$k])->Fetch();
			
			$SPECIFICATION[] = $gost['SPECIFICATION'];
			$SPECIFICATION_TU[] = $res1['SPECIFICATION'];
			$GOST_ID[] = $gost['ID'];
			$GOST_VAL[] = $res1['NORM_COMMENT'];
			$GOST_PUNKT[] = $gost['GOST_PUNKT'];
			$GOST_PUNKT_TU[] = $res1['GOST_PUNKT'];
			$GOST_DOC[] = $res1['GOST'];
			$GOST_DOC_DESC[] =  $NORM_DESC[$k];
			$GOST_DOC_DESC_TU[] = $res1['NAME_GOST'];
			$GOST_DOC_DESC_MET[] = $gost['NAME_GOST'];
			if ($items['gost_new'][$k] != '-') $GOST_DOC_PLUS_DESC[] = $items['gost_new'][$k] . " - " . $items['gost_new_desc'][$k];
			$GOST2[] = $gost['GOST'];
			$ED[] = $gost['ED'];
			$ED_INDEX[] = $gost['ED_INDEX'];
			$NORM1[] = $res1['NORM1'];
			$NORM2[] = $res1['NORM2'];
			$IN_OUT[] = 1;
			$YEARS[] = $gost['GOST_YEAR'];
			$CHECKED[$ch] = $gost['CHECKED'];
			$YEARS2[] = $res1['GOST_YEAR'];
			$MATCH[] = $gost['MATCH_MANUAL'];
			$accuracy[] =$gost['ACCURACY'];
			$GOST_TYPE[]=$gost['GOST_TYPE'];
			$UNNORM[]=$res1['UNNORM'];

			$ch++;
		}
	}
}

$i = 0;
if ($ID < getIdDealAfterWhichOborudConnectedGost()) {
    if ( ! count($oborud)) {
        $oborud_check = 0;
        echo "<p style='color:red'>Протокол не может быть сформирован! Не указано используемое оборудование! </p><br>";
    }
}
$key_check = array_search("0", $CHECKED);

if($key_check) {echo "<p style='color:red'>Протокол не может быть сформирован! Используемая методика <a href='/obl_acc.php?ID=".$GOST_ID[$key_check-1]."'>".$GOST2[$key_check-1].' '.$GOST_PUNKT[$key_check-1]."</a> не проверена отделом метрологии! </p><br>";}

$date_check=1;


$date_result = !empty($ba_tz['DATE_RESULTS']) ? $ba_tz['DATE_RESULTS'] : '';


//if((right_date($date_result)>right_date($protocolDate)) && !empty($protocolNum)) {
if((right_date($date_result)>right_date($protocolDate)) && !empty($protocolNum)&&($USER->GetID() != '31')) {
    $date_check=0;
	echo "<p style='color:red'>Протокол не может быть сформирован! Указанная дата протокола меньше даты формирования результатов испытаний!</p><br>";
}

$wbr = '';

if($insert_str) {

    for ($i = 0; $i < $sum_str; $i++) {

        $wbr .= '<w:br/>';

    }

}

$firstStr = 0;


$arr_gost_not_metodic_otbor = [];

$arr_gost_not_result = [];
$arrTemperature = $arrHumidity = $arrTemperatureBefore = $arrTemperatureAfter = $arrHumidityBefore = $arrHumidityAfter = [];

foreach ($tz as $key => $arrDatum) {


	if (empty($selectedProba[$key - 1])) {
        continue;
    }


	$arrayGostKeySelectedProba = !empty($selectedProba[$key - 1]) ? array_keys($selectedProba[$key - 1]) : [];

	$gostKeySelectedProba = !empty($arrayGostKeySelectedProba[0]) ? (int)$arrayGostKeySelectedProba[0] : 0;

	foreach($probe[$key]['number_probe'] as $key_probe => $probe_k){



        if (!in_array($key_probe + 1, array_keys($selectedProba[$key - 1][$gostKeySelectedProba]))) {
            continue;
        }

		$key_probe = $key_probe+1; $j=0; $line_num=0;
		$z="."; $z_pos=0;

		$gost_arr = (!empty($arrDatum['gost']) ?  $arrDatum['gost'] : '');
		

		foreach ($gost_arr as $k_gost => $item_gost)
		{
			if (!empty($item_gost)) {

				$select_ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID` = " . (int)$item_gost)->Fetch();
		

				if (!empty($select_ba_gost['GOST_TYPE']) && $select_ba_gost['GOST_TYPE'] == 'metodic_otbor') {
					$arr_gost_not_metodic_otbor[$k_gost] = $item_gost;
				}
				

				if (!empty($select_ba_gost['GOST_TYPE']) && $select_ba_gost['GOST_TYPE'] != 'metodic_otbor' && $results_tz[$key - 1][$k_gost][$key_probe][0] == "") {
					$arr_gost_not_result[$k_gost] = $item_gost;
				}
			}
		}
		

		$count_metodic = count($arr_gost_not_metodic_otbor);
		

		$count_metodic_not_result = count($arr_gost_not_result);

		foreach ($arrDatum['gost'] as $k => $item){

			$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
			$res_TU = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $arrDatum['gost_new'][$k])->Fetch();


            if ($ID >= getIdDealAfterWhichOborudConnectedGost()) {
                $gostTemperatures = ! empty($res['TEMP']) ? unserialize($res['TEMP']) : [];
                $gostTemperatureBefore = (float)$gostTemperatures[0] ?? 0;
                $gostTemperatureAfter = (float)$gostTemperatures[1] ?? 0;
                $gostHumiditys = ! empty($res['VLAG']) ? unserialize($res['VLAG']) : [];
                $gostHumidityBefore = (float)$gostHumiditys[0] ?? 0;
                $gostHumidityAfter = (float)$gostHumiditys[1] ?? 0;

                $currentTemperatureBefore = (float)$temperatureBefore[$key - 1][$k][$key_probe][0] ?? 0;
                $currentTemperatureAfter = (float)$temperatureAfter[$key - 1][$k][$key_probe][0] ?? 0;
                $currentHumidityBefore = (float)$humidityBefore[$key - 1][$k][$key_probe][0] ?? 0;
                $currentHumidityAfter = (float)$humidityAfter[$key - 1][$k][$key_probe][0] ?? 0;

                if (!empty($temperatureHumidityCheck[$key - 1][$k][$key_probe][0])) {
                    /*$arrTemperatureBefore[] = $currentTemperatureBefore;
                    $arrTemperatureAfter[] = $currentTemperatureAfter;
                    $arrHumidityBefore[] = $currentHumidityBefore;
                    $arrHumidityAfter[] = $currentHumidityAfter;*/

                    $arrTemperature[] = $currentTemperatureBefore == $currentTemperatureAfter ? number_format($currentTemperatureBefore, 1, ',', '')." " : "(".number_format($currentTemperatureBefore, 1, ',', '')." - ".number_format($currentTemperatureAfter, 1, ',', '').") ";
                    $arrHumidity[] = $currentHumidityBefore == $currentHumidityAfter ? number_format($currentHumidityBefore, 1, ',', '')." " : "(".number_format($currentHumidityBefore, 1, ',', '')." - ".number_format($currentHumidityAfter, 1, ',', '').") ";
                } else {
                    /*$arrTemperatureBefore[] = $temp_p;
                    $arrTemperatureAfter[] = $temp_p2;
                    $arrHumidityBefore[] = $vlag_p;
                    $arrHumidityAfter[] = $vlag_p2;*/

                    $arrTemperature[] = $temp_p == $temp_p2 ? number_format($temp_p, 1, ',', '')." " : "(".number_format($temp_p, 1, ',', '')." - ".number_format($temp_p2, 1, ',', '').") ";
                    $arrHumidity[] = $vlag_p == $vlag_p2 ? number_format($vlag_p, 1, ',', '')." " : "(".number_format($vlag_p, 1, ',', '')." - ".number_format($vlag_p2, 1, ',', '').") ";
                }

                if ( ! empty($temperatureHumidityCheck[$key - 1][$k][$key_probe][0])) {
                    if (($currentTemperatureBefore < $gostTemperatureBefore || $currentTemperatureAfter > tofloat($gostTemperatureAfter)) && empty($res['TEMPERATURE'])) {
                        $temp_in_gost_check = 0;
                        echo "Внимание! Температура при проведении испытаний не соответствует условиям в методике <a href='/obl_acc.php?ID=" . $res['ID'] . "'>" . $res['GOST'] . " " . $res['SPECIFICATION'] . "</a>! <br><br>";
                    }

                    if (($currentHumidityBefore < $gostHumidityBefore || $currentHumidityAfter > $gostHumidityAfter) && empty($res['HUMIDITY'])) {
                        $vlag_in_gost_check = 0;
                        echo "Внимание! Влажность при проведении испытаний не соответствует условиям в методике <a href='/obl_acc.php?ID=" . $res['ID'] . "'>" . $res['GOST'] . " " . $res['SPECIFICATION'] . "</a>! <br><br>";
                    }
                } else {

                    if (($temp_p < $gostTemperatureBefore || $temp_p2 > tofloat($gostTemperatureAfter)) && empty($res['TEMPERATURE'])) {
                        $temp_in_gost_check = 0;
                        echo "Внимание! Температура при проведении испытаний не соответствует условиям в методике <a href='/obl_acc.php?ID=" . $res['ID'] . "'>" . $res['GOST'] . " " . $res['SPECIFICATION'] . "</a>! <br><br>";
                    }

                    if (($vlag_p < $gostHumidityBefore || $vlag_p2 > $gostHumidityAfter) && empty($res['HUMIDITY'])) {
                        $vlag_in_gost_check = 0;
                        echo "Внимание! Влажность при проведении испытаний не соответствует условиям в методике <a href='/obl_acc.php?ID=" . $res['ID'] . "'>" . $res['GOST'] . " " . $res['SPECIFICATION'] . "</a>! <br><br>";
                    }
                }

                //Оборудование не привязанное к ГОСТу
                $resOborudGostIsNull = $DB->Query("SELECT * FROM `TZ_OB_CONNECT` WHERE `ID_TZ` = " . $ID . " AND `PROTOCOL_ID`=" . $protocolId . " AND `ID_GOST`=0");
                if (intval($resOborudGostIsNull->SelectedRowsCount()) > 0) {
                    while ($rowOborudGostIsNull = $resOborudGostIsNull->Fetch()) {
                        if (empty($rowOborudGostIsNull['ID_OB'])) {
                            continue;
                        }
                        $baOborud = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . $rowOborudGostIsNull['ID_OB'])->Fetch();

                        $oborudToGostCheck = 0;
                        echo "Внимание! Оборудование <a href='/oborud.php?ID=" . $baOborud['ID'] . "'>" . $baOborud['OBJECT'] . " " . $baOborud['TYPE_OBORUD'] . " " . $baOborud['REG_NUM'] . "</a> не привязано к госту! <br>";
                    }
                }

                //$res_ob = $DB->Query("SELECT * FROM `TZ_OB_CONNECT` WHERE `ID_TZ` = " . $ID . " AND `PROTOCOL_ID`=" . $protocolId . " AND `ID_GOST`=" . $item);
                $res_ob = $DB->Query("SELECT * FROM `TZ_OB_CONNECT` WHERE `ID_TZ` = " . $ID . " AND `PROTOCOL_ID`=" . $protocolId);
                if (intval($res_ob->SelectedRowsCount()) > 0) {
                    while ($row = $res_ob->Fetch()) {

                        //Если не привязан к госту то выводим сообщение
                        if (empty($row['ID_GOST'])) {
                            $oborudToGostCheck = 0;
                            echo "Внимание! Оборудование не привязано к госту <a href='/obl_acc.php?ID=" . $res['ID'] . "'>" . $res['GOST'] . " " . $res['SPECIFICATION'] . "</a>! <br><br>";
                            continue;
                        }

                        $ob = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . $row['ID_OB'])->Fetch();
                        $poverka_time = 5184000;

                        $poverka = strtotime($ob['POVERKA']) - strtotime($date2);//time();

                        $temp_o1 = (float)$ob['TOO_EX'];
                        $temp_o2 = $ob['TOO_EX2'];

                        $vlag_o1 = (float)$ob['OVV_EX'];
                        $vlag_o2 = $ob['OVV_EX2'];

                        //Если темпиратуру нужно проверить по методике и гост привязан к выбранным методикам
                        //if ( ! empty($temperatureHumidityCheck[$key - 1][$k][$key_probe][0])) {
                        if ( ! empty($temperatureHumidityCheck[$key - 1][$k][$key_probe][0]) && $item == $row['ID_GOST']) {

                            if (($currentTemperatureBefore < $temp_o1 || $currentTemperatureAfter > $temp_o2) && empty($ob['TEMPERATURE'])) {
                                $temp_in_ob_check = 0;
                                echo "Внимание! Температура при проведении испытаний не соответствует условиям эксплуатации оборудования <a href='/oborud.php?ID=" . $ob['ID'] . "'>" . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD'] . " " . $ob['REG_NUM'] . "</a>! <br><br>";
                            }

                            if (($currentHumidityBefore < $vlag_o1 || $currentHumidityAfter > $vlag_o2) && empty($ob['HUMIDITY'])) {
                                $vlag_in_ob_check = 0;
                                echo "Внимание! Влажность при проведении испытаний не соответствует условиям эксплуатации оборудования <a href='/oborud.php?ID=" . $ob['ID'] . "'>" . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD'] . " " . $ob['REG_NUM'] . "</a>! <br><br>";
                            }
                        } else {
                            if (($temp_p < $temp_o1 || $temp_p2 > $temp_o2) && empty($ob['TEMPERATURE'])) {
                                $temp_in_ob_check = 0;
                                echo "Внимание! Температура при проведении испытаний не соответствует условиям эксплуатации оборудования <a href='/oborud.php?ID=" . $ob['ID'] . "'>" . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD'] . " " . $ob['REG_NUM'] . "</a>! <br><br>";
                            }

                            if (($vlag_p < $vlag_o1 || $vlag_p2 > $vlag_o2) && empty($ob['HUMIDITY'])) {
                                $vlag_in_ob_check = 0;
                                echo "Внимание! Влажность при проведении испытаний не соответствует условиям эксплуатации оборудования <a href='/oborud.php?ID=" . $ob['ID'] . "'>" . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD'] . " " . $ob['REG_NUM'] . "</a>! <br><br>";
                            }
                        }

                        if ( ! $ob['CHECKED']) {
                            $oborud_check = 0;
                            echo "<p style='color:red'>Протокол не может быть сформирован! Оборудование <a href='/oborud.php?ID=" . $ob['ID'] . "'>" . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD'] . " " . $ob['REG_NUM'] . "</a> не проверено! </p><br>";
                        }
                        if ($poverka <= 0 && $ob['IDENT'] != "OOPP" && $ob['IDENT'] != "VO") {
                            $oborud_check = 0;
                            echo "<p style='color:red'>Протокол не может быть сформирован! Истек срок поверки оборудования <a href='/oborud.php?ID=" . $ob['ID'] . "'>" . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD'] . " " . $ob['REG_NUM'] . "</a>! </p><br>";
                        }
                        if ($ob['IDENT'] != "VO" && $ob['IDENT'] != "TS" && $ob['IDENT'] != "REACT") {
                            $res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$row['ID_OB']);
                            $row_certificate = $res_certificate_select->Fetch();
                            $certificateName_arr = unserialize($row_certificate['name_f']);
                            $cert_arr = array();
                            foreach ($certificateName_arr as $cName) {
                                $cert_arr[] = $cName;
                            }
                            $ob_v = ($ob['certificate_of_verification'] ? $ob['certificate_of_verification'] : '');
                            $certificateName = $cert_arr[count($cert_arr) - 1] ? $cert_arr[count($cert_arr) - 1] : $ob_v;
                            $ob_arr = [
                                'Name' => $ob['OBJECT'] . ' ' . $ob['TYPE_OBORUD'] . ', ' . $ob['FACTORY_NUMBER'] . ', ' . $ob['REG_NUM'] . ', ' . date("d.m.Y",
                                        strtotime($ob['god_vvoda_expluatation'])),

                                'Poverka' => $certificateName . ' до ' . date("d.m.Y", strtotime($ob['POVERKA'])),
                            ];

                            if ( ! in_array($ob_arr, $oborud)) {
                                $oborud[] = $ob_arr;
                            }
                        }
                        //TODO: Проверить нужно или нет
                        //$tz_ob = 1;
                    }
                }
            }


            $gostYear = !empty($res['GOST_YEAR']) ? (int)$res['GOST_YEAR'] : '';
			$normdocmet = '';
		

			if (!empty($type_tz) && in_array($type_tz, [33, 34, 35, 36, 37, 38, 39])) {
				$normdocmet = ($res['GOST']) ? $res['GOST'] . '-' . $gostYear . ' ' . $res['GOST_PUNKT'] : '-';
			} else {
				$normdocmet = ($res['GOST']) ? $res['GOST'].' '.$res['GOST_PUNKT'] : '-';
			}
			
			$ress = $arr_soot[$MATCH_MANUAL[$key - 1][$k][$key_probe]];
			if(!$res['RES_TEXT'])
			{
				for($l=0; $l<count($results_tz[$key - 1][$k][$key_probe]); $l++){
					$z_pos=strpos($results_tz[$key - 1][$k][$key_probe][$l],$z);


					if ($z_pos) $results_tz[$key - 1][$k][$key_probe][$l] = str_replace('.', ',', $results_tz[$key - 1][$k][$key_probe][$l]);

				}
			}
			$z_pos2 = strpos($SRED[$key - 1][$k][$key_probe]);
			$SRED[$key - 1][$k][$key_probe] = number_format($SRED[$key - 1][$k][$key_probe], $res['ACCURACY'], ',', '');
			$norm_description = (count($tz) >1 ? $NORM_DESC[$key-1][$key_probe][$k] : $NORM_DESC[$key_probe][$k]);
			$prod_arr = explode(', ID',$probe_k);
			$prod = $prod_arr[0];
			$obj='';
           $br = '';
			$obj_density_grunt = '';				 


			if ($type_tz == 30 && $results_tz[$key - 1][$k][$key_probe][0] == "") {
				$obj = $prod . " (" . $probe[$key]['sh_number'][$key_probe - 1] . ")";
				$results[] = [
					'Object' => $obj,
					'Normdocmet' => $normdocmet,
				];
			}  

            $line = '';
			if($results_tz[$key - 1][$k][$key_probe][0]!=""){
				/*if (!$j) {
                    $obj = $prod . " (" . $probe[$key]['sh_number'][$key_probe - 1] . ")";
					$obj_density_grunt = !empty($prod) ? $prod : ''; 								  

                }

				if($line_num==count($arrDatum['gost'])-1 - $count_metodic - $count_metodic_not_result) {$line = "______________";}*/

                $obj = $prod . " (" . $probe[$key]['sh_number'][$key_probe - 1] . ")";
                $obj_density_grunt = !empty($prod) ? $prod : '';

                $line = "______________";

				$res_ed = !empty($res['ED']) ? $res['ED'] : '';


				$normative_value = ($res_TU['GOST_TYPE']=='TU_research') ? $norm_description : $res_TU['NORM_COMMENT'];

				$results[] = [
					'Object' => $obj,
					'LotNumber' => $obj_density_grunt,				   
					'Specification' => $res['SPECIFICATION'],
					'SpecificationTU' => $res_TU['SPECIFICATION'],

					'Ed' => $res_ed,
					'EdIndex' => ($res['ED_INDEX'] ? $res['ED_INDEX'] : ''),
					'Normdoc' => $res_TU['GOST']. ' ' . $res_TU['GOST_PUNKT'],

					'Normval' => $normative_value,

					'Normdocmet' => $normdocmet,
					'Normdoc_desc' => '',
					'Values' => $results_tz[$key - 1][$k][$key_probe],
					'Value' => ($res['GOST_TYPE'] == "TU_sred"||$res['GOST_TYPE'] == "TU_sred3"||$res['GOST_TYPE'] == "TU_sred2"||$res['GOST_TYPE'] == "TU_sred4"||$res['GOST_TYPE'] == "TU_sred5") ? $SRED[$key - 1][$k][$key_probe] : "-",
					'Result' => $ress,

                    'Line' => $line,
                    'Br' => $br

				];
				$j++;
				$firstStr++;

				$line_num++;
			} 

		} 
	}


	$arr_gost_not_metodic_otbor = [];
	

	$arr_gost_not_result = [];

}


if ($ID >= getIdDealAfterWhichOborudConnectedGost()) {
    if ( ! count($oborud)) {
        $oborud_check = 0;
        echo "<p style='color:red'>Протокол не может быть сформирован! Не указано используемое оборудование! </p><br>";
    }
}


foreach ($results_tz as $num => $tz_resaults) {


    if (empty($selectedProba[$num])) {
        continue;
    }


    $arrayKeyGostSelectedProba = !empty($selectedProba[$num]) ? array_keys($selectedProba[$num]) : [];

    $keyGostSelectedProba = !empty($arrayKeyGostSelectedProba[0]) ? (int)$arrayKeyGostSelectedProba[0] : 0;

    foreach ($tz_resaults as $number => $tz_resault) {


        if ($number + 1 != key($selectedProba[$num][$keyGostSelectedProba])) {
            continue;
        }

		$z=".";
		$z_pos=0;

        foreach ($tz_resault as $kk => $nu) {

            $ress = $arr_soot[$MATCH_MANUAL[$num][$number][$kk]];

			for($j=0; $j<count($nu); $j++){
			$z_pos=strpos($nu[$j],$z);
			if ($z_pos) $nu[$j] = substr_replace($nu[$j], ",", $z_pos, 1);
			}
			$z_pos2 = strpos($SRED[$num][$number][$kk],$z);


			$SRED[$num][$number][$kk] = number_format($SRED[$num][$number][$kk], $accuracy[$i], ',', '');

		}
$i++;
    }

}

foreach ($probe as $keyCount => $prob) {
	
	if (empty($selectedProba[$keyCount - 1])) {
        continue;
    }


    $arrayGostKeySelectedSample = !empty($selectedProba[$keyCount - 1]) ? array_keys($selectedProba[$keyCount - 1]) : [];

    $gostKeySelectedSample = !empty($arrayGostKeySelectedSample[0]) ? (int)$arrayGostKeySelectedSample[0] : 0;


foreach ($probe[$keyCount]['number_probe'] as $pn => $probe_n)
{
	
	if (!in_array($pn + 1, array_keys($selectedProba[$keyCount - 1][$gostKeySelectedSample]))) {
		continue;
	}

    
	for($l=0; $l<count($tz[$keyCount]['gost']); $l++) {

	
	if ($tz[$keyCount]['gost'][$l]) {
		
	
	$res1 = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $tz[$keyCount]['gost'][$l])->Fetch();


	$res_group_mat = $DB->Query("SELECT `NORM1`, `NORM2`, `NAME`, `TYPE_MAT_ID` FROM `ZERN` WHERE `TYPE_MAT_ID`='" . $group_mat_value . "'")->Fetch();


	$res_n1 = unserialize($res_group_mat['NORM1']);
	$res_n2 = unserialize($res_group_mat['NORM2']);
	$prod_arr = explode(', ID',$probe_n);
			$prod = $prod_arr[0];
			
			
	if ($res1['GOST_TYPE']=='TU_zern'||$res1['GOST_TYPE']=='TU_prirod'||$res1['GOST_TYPE']=='TU_12801') {
	$accurasy1 = $res1['GOST_TYPE']=='TU_prirod'? 1 : 2;	
	$resultszern[] = [
		//'Object' => $prod . ' (' . $probe[1]['sh_number'][0] .')',
		'Object' => $prod . ' (' . $probe[$keyCount]['sh_number'][$pn] .')',
		'Ost25ch' => number_format($OSTATKI[0], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Ost125ch' => number_format($OSTATKI[1], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Ost063ch' => number_format($OSTATKI[2], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Ost0315ch' => number_format($OSTATKI[3], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Ost016ch' => number_format($OSTATKI[4], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Ost016mch' => number_format($OSTATKI[5], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Ost25p' => number_format($OSTATKI[6], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Ost125p' => number_format($OSTATKI[7], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Ost063p' => number_format($OSTATKI[8], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Ost0315p' => number_format($OSTATKI[9], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Ost016p' => number_format($OSTATKI[10], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Ost016mp' => number_format($OSTATKI[11], ($res1['GOST_TYPE']=='TU_12801' ? 1 : $accurasy1), ',', ''),
		'Norm200' => $res_n1[0].(($res_n2[0]&&($res_n2[0]!=$res_n1[0])) ? '-'.$res_n2[0] : ''),
		'Norm120' => $res_n1[1].(($res_n2[1]&&($res_n2[1]!=$res_n1[1])) ? '-'.$res_n2[1] : ''),
		'Norm100' => $res_n1[2].(($res_n2[2]&&($res_n2[2]!=$res_n1[2])) ? '-'.$res_n2[2] : ''),
		'Norm80' => $res_n1[3].(($res_n2[3]&&($res_n2[3]!=$res_n1[3])) ? '-'.$res_n2[3] : ''),
		'Norm60' => $res_n1[4].(($res_n2[4]&&($res_n2[4]!=$res_n1[4])) ? '-'.$res_n2[4] : ''),
		'Norm40' => $res_n1[5].(($res_n2[5]&&($res_n2[5]!=$res_n1[5])) ? '-'.$res_n2[5] : ''),
		'Norm315' => $res_n1[6].(($res_n2[6]&&($res_n2[6]!=$res_n1[6])) ? '-'.$res_n2[6] : ''),
		'Norm224' => $res_n1[7].(($res_n2[7]&&($res_n2[7]!=$res_n1[7])) ? '-'.$res_n2[7] : ''),
		'Norm20' => $res_n1[8].(($res_n2[8]&&($res_n2[8]!=$res_n1[8])) ? '-'.$res_n2[8] : ''),
		'Norm16' => $res_n1[9].(($res_n2[9]&&($res_n2[9]!=$res_n1[9])) ? '-'.$res_n2[9] : ''),
		'Norm15' => $res_n1[10].(($res_n2[10]&&($res_n2[10]!=$res_n1[10])) ? '-'.$res_n2[10] : ''),
		'Norm112' => $res_n1[11].(($res_n2[11]&&($res_n2[11]!=$res_n1[11])) ? '-'.$res_n2[11] : ''),
		'Norm10' => $res_n1[12].(($res_n2[12]&&($res_n2[12]!=$res_n1[12])) ? '-'.$res_n2[12] : ''),
		'Norm8' => $res_n1[13].(($res_n2[13]&&($res_n2[13]!=$res_n1[13])) ? '-'.$res_n2[13] : ''),
		'Norm5' => $res_n1[14].(($res_n2[14]&&($res_n2[14]!=$res_n1[14])) ? '-'.$res_n2[14] : ''),
		'Norm4' => $res_n1[15].(($res_n2[15]&&($res_n2[15]!=$res_n1[15])) ? '-'.$res_n2[15] : ''),
		'Norm25' => $res_n1[16].(($res_n2[16]&&($res_n2[16]!=$res_n1[16])) ? '-'.$res_n2[16] : ''),
		'Norm2' => $res_n1[17].(($res_n2[17]&&($res_n2[17]!=$res_n1[17])) ? '-'.$res_n2[17] : ''),
		'Norm125' => $res_n1[18].(($res_n2[18]&&($res_n2[18]!=$res_n1[18])) ? '-'.$res_n2[18] : ''),
		'Norm1' => $res_n1[19].(($res_n2[19]&&($res_n2[19]!=$res_n1[19])) ? '-'.$res_n2[19] : ''),
		'Norm063' => $res_n1[20].(($res_n2[20]&&($res_n2[20]!=$res_n1[20])) ? '-'.$res_n2[20] : ''),
		'Norm05' => $res_n1[21].(($res_n2[21]&&($res_n2[21]!=$res_n1[21])) ? '-'.$res_n2[21] : ''),
		'Norm0315' => $res_n1[22].(($res_n2[22]&&($res_n2[22]!=$res_n1[22])) ? '-'.$res_n2[22] : ''),
		'Norm025' => $res_n1[23].(($res_n2[23]&&($res_n2[23]!=$res_n1[23])) ? '-'.$res_n2[23] : ''),
		'Norm016' => $res_n1[24].(($res_n2[24]&&($res_n2[24]!=$res_n1[24])) ? '-'.$res_n2[24] : ''),
		'Norm0125' => $res_n1[25].(($res_n2[25]&&($res_n2[25]!=$res_n1[25])) ? '-'.$res_n2[25] : ''),
		'Norm01' => $res_n1[26].(($res_n2[26]&&($res_n2[26]!=$res_n1[26])) ? '-'.$res_n2[26] : ''),
		'Norm0071' => $res_n1[27].(($res_n2[27]&&($res_n2[27]!=$res_n1[27])) ? '-'.$res_n2[27] : ''),
		'Norm0063' => $res_n1[28].(($res_n2[28]&&($res_n2[28]!=$res_n1[28])) ? '-'.$res_n2[28] : ''),
		'Norm005' => $res_n1[29].(($res_n2[29]&&($res_n2[29]!=$res_n1[29])) ? '-'.$res_n2[29] : ''),
		'Normm005' => $res_n1[30].(($res_n2[30]&&($res_n2[30]!=$res_n1[30])) ? '-'.$res_n2[30] : '')
		
		];
		$l=10000;
	}

		elseif ($res1['GOST_TYPE']=='TU_grunt' || $res1['GOST_TYPE']=='TU_metric_method') {

		$resultszern[] = [
		'Object' => $prod . ' (' . $probe[$keyCount]['sh_number'][$pn] .')',

		'Ost100ch' => number_format($OSTATKI[21], 1, ',', ''),
		'Ost60ch' => number_format($OSTATKI[20], 1, ',', ''),
		'Ost40ch' => number_format($OSTATKI[19], 1, ',', ''),
		'Ost20ch' => number_format($OSTATKI[18], 1, ',', ''),

		'Ost10ch' => number_format($OSTATKI[0], 1, ',', ''),
		'Ost5ch' => number_format($OSTATKI[1], 1, ',', ''),
		'Ost2ch' => number_format($OSTATKI[2], 1, ',', ''),
		'Ost1ch' => number_format($OSTATKI[3], 1, ',', ''),
		'Ost05ch' => number_format($OSTATKI[4], 1, ',', ''),
		'Ost025ch' => number_format($OSTATKI[5], 1, ',', ''),
		'Ost01ch' => number_format($OSTATKI[6], 1, ',', ''),
		'Ost005ch' => number_format($OSTATKI[7], 1, ',', ''),
		'Ost005mch' => number_format($OSTATKI[8], 1, ',', ''),

		'Ost001ch' => number_format($OSTATKI[26], 1, ',', ''),
		'Ost0002ch' => number_format($OSTATKI[27], 1, ',', ''),
		'Ost0002mch' => number_format($OSTATKI[28], 1, ',', ''),

		'Ost100p' => number_format($OSTATKI[25], 1, ',', ''),
		'Ost60p' => number_format($OSTATKI[24], 1, ',', ''),
		'Ost40p' => number_format($OSTATKI[23], 1, ',', ''),
		'Ost20p' => number_format($OSTATKI[22], 1, ',', ''),

		'Ost10p' => number_format($OSTATKI[9], 1, ',', ''),
		'Ost5p' => number_format($OSTATKI[10], 1, ',', ''),
		'Ost2p' => number_format($OSTATKI[11], 1, ',', ''),
		'Ost1p' => number_format($OSTATKI[12], 1, ',', ''),
		'Ost05p' => number_format($OSTATKI[13], 1, ',', ''),
		'Ost025p' => number_format($OSTATKI[14], 1, ',', ''),
		'Ost01p' => number_format($OSTATKI[15], 1, ',', ''),
		'Ost005p' => number_format($OSTATKI[16], 1, ',', ''),
		'Ost005mp' => number_format($OSTATKI[17], 1, ',', ''),

		'Ost001p' => number_format($OSTATKI[29], 1, ',', ''),
		'Ost0002p' => number_format($OSTATKI[30], 1, ',', ''),
		'Ost0002mp' => number_format($OSTATKI[31], 1, ',', ''),

		];
		$l=10000;
		}
	elseif ($res1['GOST_TYPE']=='TU_183'||$res1['GOST_TYPE']=='TU_184') {
		$resultszern[] = [
		'Object' => $prod . ' (' . $probe[$keyCount]['sh_number'][$pn] .')',
		'Ost25ch' => number_format($OSTATKI[0], 2, ',', ''),
		'Ost125ch' => number_format($OSTATKI[1], 2, ',', ''),
		'Ost063ch' => number_format($OSTATKI[2], 2, ',', ''),
		'Ost0315ch' => number_format($OSTATKI[3], 2, ',', ''),
		'Ost016ch' => number_format($OSTATKI[4], 2, ',', ''),
		'Ost016mch' => number_format($OSTATKI[5], 2, ',', ''),
		'Ost25p' => number_format($OSTATKI[6], 2, ',', ''),
		'Ost125p' => number_format($OSTATKI[7], 2, ',', ''),
		'Ost063p' => number_format($OSTATKI[8], 2, ',', ''),
		'Ost0315p' => number_format($OSTATKI[9], 2, ',', ''),
		'Ost016p' => number_format($OSTATKI[10], 2, ',', ''),
		'Ost016mp' => number_format($OSTATKI[11], 2, ',', ''),
		'Ost111' => number_format($OSTATKI[12], 2, ',', ''),
		'SOst25ch' => $SOstATKI[0] ? number_format($OSTATKI[0], 2, ',', '') : '-',
		'SOst125ch' => $SOstATKI[1] ? number_format($SOstATKI[1], 2, ',', '') : '-',
		'SOst063ch' => $SOstATKI[2] ? number_format($SOstATKI[2], 2, ',', '') : '-',
		'SOst0315ch' => $SOstATKI[3] ? number_format($SOstATKI[3], 2, ',', '') : '-',
		'SOst016ch' => $SOstATKI[4] ? number_format($SOstATKI[4], 2, ',', '') : '-',
		'SOst016mch' => $SOstATKI[5] ? number_format($SOstATKI[5], 2, ',', '') : '-',
		'SOst25p' => $SOstATKI[6] ? number_format($SOstATKI[6], 2, ',', '') : '-',
		'SOst125p' => $SOstATKI[7] ? number_format($SOstATKI[7], 2, ',', '') : '-',
		'SOst063p' => $SOstATKI[8] ? number_format($SOstATKI[8], 2, ',', '') : '-',
		'SOst0315p' => $SOstATKI[9] ? number_format($SOstATKI[9], 2, ',', '') : '-',
		'SOst016p' => $SOstATKI[10] ? number_format($SOstATKI[10], 2, ',', '') : '-',
		'SOst016mp' => $SOstATKI[11] ? number_format($SOstATKI[11], 2, ',', '') : '-',
		'SOst111' => $SOstATKI[12] ? number_format($SOstATKI[12], 2, ',', '') : '-',
		'Norm200' => $res_n1[0] ? $res_n1[0].(($res_n2[0]&&($res_n2[0]!=$res_n1[0])) ? '-'.$res_n2[0] : '') : '-',
		'Norm120' => $res_n1[1].(($res_n2[1]&&($res_n2[1]!=$res_n1[1])) ? '-'.$res_n2[1] : ''),
		'Norm100' => $res_n1[2].(($res_n2[2]&&($res_n2[2]!=$res_n1[2])) ? '-'.$res_n2[2] : ''),
		'Norm80' => $res_n1[3].(($res_n2[3]&&($res_n2[3]!=$res_n1[3])) ? '-'.$res_n2[3] : ''),
		'Norm60' => $res_n1[4].(($res_n2[4]&&($res_n2[4]!=$res_n1[4])) ? '-'.$res_n2[4] : ''),
		'Norm40' => $res_n1[5].(($res_n2[5]&&($res_n2[5]!=$res_n1[5])) ? '-'.$res_n2[5] : ''),
		'Norm315' => $res_n1[6] ? $res_n1[6].(($res_n2[6]&&($res_n2[6]!=$res_n1[6])) ? '-'.$res_n2[6] : '') : '-',
		'Norm224' => $res_n1[7] ? $res_n1[7].(($res_n2[7]&&($res_n2[7]!=$res_n1[7])) ? '-'.$res_n2[7] : '') : '-',
		'Norm20' => $res_n1[8].(($res_n2[8]&&($res_n2[8]!=$res_n1[8])) ? '-'.$res_n2[8] : ''),
		'Norm16' => $res_n1[9] ? $res_n1[9].(($res_n2[9]&&($res_n2[9]!=$res_n1[9])) ? '-'.$res_n2[9] : '') : '-',
		'Norm15' => $res_n1[10].(($res_n2[10]&&($res_n2[10]!=$res_n1[10])) ? '-'.$res_n2[10] : ''),
		'Norm112' => $res_n1[11] ? $res_n1[11].(($res_n2[11]&&($res_n2[11]!=$res_n1[11])) ? '-'.$res_n2[11] : '') : '-',
		'Norm10' => $res_n1[12].(($res_n2[12]&&($res_n2[12]!=$res_n1[12])) ? '-'.$res_n2[12] : ''),
		'Norm8' => $res_n1[13] ? $res_n1[13].(($res_n2[13]&&($res_n2[13]!=$res_n1[13])) ? '-'.$res_n2[13] : '') : '-',
		'Norm5' => $res_n1[31] ? $res_n1[31].(($res_n2[31]&&($res_n2[31]!=$res_n1[31])) ? '-'.$res_n2[31] : '') : '-',
		'Norm4' => $res_n1[15] ? $res_n1[15].(($res_n2[15]&&($res_n2[15]!=$res_n1[15])) ? '-'.$res_n2[15] : '') : '-',
		'Norm25' => $res_n1[16].(($res_n2[16]&&($res_n2[16]!=$res_n1[16])) ? '-'.$res_n2[16] : ''),
		'Norm2' => $res_n1[17] ? $res_n1[17].(($res_n2[17]&&($res_n2[17]!=$res_n1[17])) ? '-'.$res_n2[17] : '') : '-',
		'Norm125' => $res_n1[18].(($res_n2[18]&&($res_n2[18]!=$res_n1[18])) ? '-'.$res_n2[18] : ''),
		'Norm1' => $res_n1[19] ? $res_n1[19].(($res_n2[19]&&($res_n2[19]!=$res_n1[19])) ? '-'.$res_n2[19] : '') : '-',
		'Norm063' => $res_n1[20].(($res_n2[20]&&($res_n2[20]!=$res_n1[20])) ? '-'.$res_n2[20] : ''),
		'Norm05' => $res_n1[21] ? $res_n1[21].(($res_n2[21]&&($res_n2[21]!=$res_n1[21])) ? '-'.$res_n2[21] : '') : '-',
		'Norm0315' => $res_n1[22].(($res_n2[22]&&($res_n2[22]!=$res_n1[22])) ? '-'.$res_n2[22] : ''),
		'Norm025' => $res_n1[23] ? $res_n1[23].(($res_n2[23]&&($res_n2[23]!=$res_n1[23])) ? '-'.$res_n2[23] : '') : '-',
		'Norm016' => $res_n1[24].(($res_n2[24]&&($res_n2[24]!=$res_n1[24])) ? '-'.$res_n2[24] : ''),
		'Norm0125' => $res_n1[25] ? $res_n1[25].(($res_n2[25]&&($res_n2[25]!=$res_n1[25])) ? '-'.$res_n2[25] : '') : '-',
		'Norm01' => $res_n1[26].(($res_n2[26]&&($res_n2[26]!=$res_n1[26])) ? '-'.$res_n2[26] : ''),
		'Norm0071' => $res_n1[27].(($res_n2[27]&&($res_n2[27]!=$res_n1[27])) ? '-'.$res_n2[27] : ''),
		'Norm0063' => $res_n1[28] ? $res_n1[28].(($res_n2[28]&&($res_n2[28]!=$res_n1[28])) ? '-'.$res_n2[28] : '') : '-',
		'Norm005' => $res_n1[29].(($res_n2[29]&&($res_n2[29]!=$res_n1[29])) ? '-'.$res_n2[29] : ''),
		'Normm005' => $res_n1[30].(($res_n2[30]&&($res_n2[30]!=$res_n1[30])) ? '-'.$res_n2[30] : '')
		];
		$l=10000;
		}
		elseif ($res1['GOST_TYPE']=='TU_shps') {
		if($res1['ID']==4051)
		$resultszern[] = [
		'Object' => $prod . ' (' . $probe[$keyCount]['sh_number'][$pn] .')',
		'NameMat' => $res_group_mat['NAME'],
		'Ost315p' => number_format($OSTATKI[0], 2, ',', ''),
		'Ost224p' => number_format($OSTATKI[1], 2, ',', ''),
		'Ost16p' => number_format($OSTATKI[2], 2, ',', ''),
		'Ost8p' => number_format($OSTATKI[3], 2, ',', ''),
		'Ost4p' => number_format($OSTATKI[4], 2, ',', ''),
		'Ost2p' => number_format($OSTATKI[5], 2, ',', ''),
		'Ost1p' => number_format($OSTATKI[6], 2, ',', ''),
		'Ost05p' => number_format($OSTATKI[7], 2, ',', ''),
		'Ost025p' => number_format($OSTATKI[8], 2, ',', ''),
		'Ost0125p' => number_format($OSTATKI[9], 2, ',', ''),
		'Ost0063p' => number_format($OSTATKI[10], 2, ',', ''),
		'Ostm0063p' => number_format($OSTATKI[11], 2, ',', ''),
		'Ost315ch' => number_format($OSTATKI[12], 2, ',', ''),
		'Ost224ch' => number_format($OSTATKI[13], 2, ',', ''),
		'Ost16ch' => number_format($OSTATKI[14], 2, ',', ''),
		'Ost8ch' => number_format($OSTATKI[15], 2, ',', ''),
		'Ost4ch' => number_format($OSTATKI[16], 2, ',', ''),
		'Ost2ch' => number_format($OSTATKI[17], 2, ',', ''),
		'Ost1ch' => number_format($OSTATKI[18], 2, ',', ''),
		'Ost05ch' => number_format($OSTATKI[19], 2, ',', ''),
		'Ost025ch' => number_format($OSTATKI[20], 2, ',', ''),
		'Ost0125ch' => number_format($OSTATKI[21], 2, ',', ''),
		'Ost0063ch' => number_format($OSTATKI[22], 2, ',', ''),
		'Ostm0063ch' => number_format($OSTATKI[23], 2, ',', ''),
		'Norm200' => $res_n1[0].(($res_n2[0]&&($res_n2[0]!=$res_n1[0])) ? '-'.$res_n2[0] : ''),
		'Norm120' => $res_n1[1].(($res_n2[1]&&($res_n2[1]!=$res_n1[1])) ? '-'.$res_n2[1] : ''),
		'Norm100' => $res_n1[2].(($res_n2[2]&&($res_n2[2]!=$res_n1[2])) ? '-'.$res_n2[2] : ''),
		'Norm80' => $res_n1[3].(($res_n2[3]&&($res_n2[3]!=$res_n1[3])) ? '-'.$res_n2[3] : ''),
		'Norm60' => $res_n1[4].(($res_n2[4]&&($res_n2[4]!=$res_n1[4])) ? '-'.$res_n2[4] : ''),
		'Norm40' => $res_n1[5].(($res_n2[5]&&($res_n2[5]!=$res_n1[5])) ? '-'.$res_n2[5] : ''),
		'Norm315' => $res_n1[6].(($res_n2[6]&&($res_n2[6]!=$res_n1[6])) ? '-'.$res_n2[6] : ''),
		'Norm224' => $res_n1[7].(($res_n2[7]&&($res_n2[7]!=$res_n1[7])) ? '-'.$res_n2[7] : ''),
		'Norm20' => $res_n1[8].(($res_n2[8]&&($res_n2[8]!=$res_n1[8])) ? '-'.$res_n2[8] : ''),
		'Norm16' => $res_n1[9].(($res_n2[9]&&($res_n2[9]!=$res_n1[9])) ? '-'.$res_n2[9] : ''),
		'Norm15' => $res_n1[10].(($res_n2[10]&&($res_n2[10]!=$res_n1[10])) ? '-'.$res_n2[10] : ''),
		'Norm112' => $res_n1[11].(($res_n2[11]&&($res_n2[11]!=$res_n1[11])) ? '-'.$res_n2[11] : ''),
		'Norm10' => $res_n1[12].(($res_n2[12]&&($res_n2[12]!=$res_n1[12])) ? '-'.$res_n2[12] : ''),
		'Norm8' => $res_n1[13].(($res_n2[13]&&($res_n2[13]!=$res_n1[13])) ? '-'.$res_n2[13] : ''),
		'Norm5' => $res_n1[14].(($res_n2[14]&&($res_n2[14]!=$res_n1[14])) ? '-'.$res_n2[14] : ''),
		'Norm4' => $res_n1[15].(($res_n2[15]&&($res_n2[15]!=$res_n1[15])) ? '-'.$res_n2[15] : ''),
		'Norm25' => $res_n1[16].(($res_n2[16]&&($res_n2[16]!=$res_n1[16])) ? '-'.$res_n2[16] : ''),
		'Norm2' => $res_n1[17].(($res_n2[17]&&($res_n2[17]!=$res_n1[17])) ? '-'.$res_n2[17] : ''),
		'Norm125' => $res_n1[18].(($res_n2[18]&&($res_n2[18]!=$res_n1[18])) ? '-'.$res_n2[18] : ''),
		'Norm1' => $res_n1[19].(($res_n2[19]&&($res_n2[19]!=$res_n1[19])) ? '-'.$res_n2[19] : ''),
		'Norm063' => $res_n1[20].(($res_n2[20]&&($res_n2[20]!=$res_n1[20])) ? '-'.$res_n2[20] : ''),
		'Norm05' => $res_n1[21].(($res_n2[21]&&($res_n2[21]!=$res_n1[21])) ? '-'.$res_n2[21] : ''),
		'Norm0315' => $res_n1[22].(($res_n2[22]&&($res_n2[22]!=$res_n1[22])) ? '-'.$res_n2[22] : ''),
		'Norm025' => $res_n1[23].(($res_n2[23]&&($res_n2[23]!=$res_n1[23])) ? '-'.$res_n2[23] : ''),
		'Norm016' => $res_n1[24].(($res_n2[24]&&($res_n2[24]!=$res_n1[24])) ? '-'.$res_n2[24] : ''),
		'Norm0125' => $res_n1[25].(($res_n2[25]&&($res_n2[25]!=$res_n1[25])) ? '-'.$res_n2[25] : ''),
		'Norm01' => $res_n1[26].(($res_n2[26]&&($res_n2[26]!=$res_n1[26])) ? '-'.$res_n2[26] : ''),
		'Norm0071' => $res_n1[27].(($res_n2[27]&&($res_n2[27]!=$res_n1[27])) ? '-'.$res_n2[27] : ''),
		'Norm0063' => $res_n1[28].(($res_n2[28]&&($res_n2[28]!=$res_n1[28])) ? '-'.$res_n2[28] : ''),
		'Norm005' => $res_n1[29].(($res_n2[29]&&($res_n2[29]!=$res_n1[29])) ? '-'.$res_n2[29] : ''),
		'Normm005' => $res_n1[30].(($res_n2[30]&&($res_n2[30]!=$res_n1[30])) ? '-'.$res_n2[30] : '')
		];
		else 
		$resultszern[] = [
		'Object' => $prod . ' (' . $probe[$keyCount]['sh_number'][$pn] .')',
		'NameMat' => $res_group_mat['NAME'],
		'Ost120p' => number_format($OSTATKI[0], 2, ',', ''),
		'Ost80p' => number_format($OSTATKI[1], 2, ',', ''),
		'Ost40p' => number_format($OSTATKI[2], 2, ',', ''),
		'Ost20p' => number_format($OSTATKI[3], 2, ',', ''),
		'Ost10p' => number_format($OSTATKI[4], 2, ',', ''),
		'Ost5p' => number_format($OSTATKI[5], 2, ',', ''),
		'Ost25p' => number_format($OSTATKI[6], 2, ',', ''),
		'Ost063p' => number_format($OSTATKI[7], 2, ',', ''),
		'Ost016p' => number_format($OSTATKI[8], 2, ',', ''),
		'Ost005p' => number_format($OSTATKI[9], 2, ',', ''),
		'Ost120ch' => number_format($OSTATKI[10], 2, ',', ''),
		'Ost80ch' => number_format($OSTATKI[11], 2, ',', ''),
		'Ost40ch' => number_format($OSTATKI[12], 2, ',', ''),
		'Ost20ch' => number_format($OSTATKI[13], 2, ',', ''),
		'Ost10ch' => number_format($OSTATKI[14], 2, ',', ''),
		'Ost5ch' => number_format($OSTATKI[15], 2, ',', ''),
		'Ost25ch' => number_format($OSTATKI[16], 2, ',', ''),
		'Ost063ch' => number_format($OSTATKI[17], 2, ',', ''),
		'Ost016ch' => number_format($OSTATKI[18], 2, ',', ''),
		'Ost005ch' => number_format($OSTATKI[19], 2, ',', ''),
		'Norm200' => $res_n1[0].(($res_n2[0]&&($res_n2[0]!=$res_n1[0])) ? '-'.$res_n2[0] : ''),
		'Norm120' => $res_n1[1].(($res_n2[1]&&($res_n2[1]!=$res_n1[1])) ? '-'.$res_n2[1] : ''),
		'Norm100' => $res_n1[2].(($res_n2[2]&&($res_n2[2]!=$res_n1[2])) ? '-'.$res_n2[2] : ''),
		'Norm80' => $res_n1[3].(($res_n2[3]&&($res_n2[3]!=$res_n1[3])) ? '-'.$res_n2[3] : ''),
		'Norm60' => $res_n1[4].(($res_n2[4]&&($res_n2[4]!=$res_n1[4])) ? '-'.$res_n2[4] : ''),
		'Norm40' => $res_n1[5].(($res_n2[5]&&($res_n2[5]!=$res_n1[5])) ? '-'.$res_n2[5] : ''),
		'Norm315' => $res_n1[6].(($res_n2[6]&&($res_n2[6]!=$res_n1[6])) ? '-'.$res_n2[6] : ''),
		'Norm224' => $res_n1[7].(($res_n2[7]&&($res_n2[7]!=$res_n1[7])) ? '-'.$res_n2[7] : ''),
		'Norm20' => $res_n1[8].(($res_n2[8]&&($res_n2[8]!=$res_n1[8])) ? '-'.$res_n2[8] : ''),
		'Norm16' => $res_n1[9].(($res_n2[9]&&($res_n2[9]!=$res_n1[9])) ? '-'.$res_n2[9] : ''),
		'Norm15' => $res_n1[10].(($res_n2[10]&&($res_n2[10]!=$res_n1[10])) ? '-'.$res_n2[10] : ''),
		'Norm112' => $res_n1[11].(($res_n2[11]&&($res_n2[11]!=$res_n1[11])) ? '-'.$res_n2[11] : ''),
		'Norm10' => $res_n1[12].(($res_n2[12]&&($res_n2[12]!=$res_n1[12])) ? '-'.$res_n2[12] : ''),
		'Norm8' => $res_n1[13].(($res_n2[13]&&($res_n2[13]!=$res_n1[13])) ? '-'.$res_n2[13] : ''),
		'Norm5' => $res_n1[14].(($res_n2[14]&&($res_n2[14]!=$res_n1[14])) ? '-'.$res_n2[14] : ''),
		'Norm4' => $res_n1[15].(($res_n2[15]&&($res_n2[15]!=$res_n1[15])) ? '-'.$res_n2[15] : ''),
		'Norm25' => $res_n1[16].(($res_n2[16]&&($res_n2[16]!=$res_n1[16])) ? '-'.$res_n2[16] : ''),
		'Norm2' => $res_n1[17].(($res_n2[17]&&($res_n2[17]!=$res_n1[17])) ? '-'.$res_n2[17] : ''),
		'Norm125' => $res_n1[18].(($res_n2[18]&&($res_n2[18]!=$res_n1[18])) ? '-'.$res_n2[18] : ''),
		'Norm1' => $res_n1[19].(($res_n2[19]&&($res_n2[19]!=$res_n1[19])) ? '-'.$res_n2[19] : ''),
		'Norm063' => $res_n1[20].(($res_n2[20]&&($res_n2[20]!=$res_n1[20])) ? '-'.$res_n2[20] : ''),
		'Norm05' => $res_n1[21].(($res_n2[21]&&($res_n2[21]!=$res_n1[21])) ? '-'.$res_n2[21] : ''),
		'Norm0315' => $res_n1[22].(($res_n2[22]&&($res_n2[22]!=$res_n1[22])) ? '-'.$res_n2[22] : ''),
		'Norm025' => $res_n1[23].(($res_n2[23]&&($res_n2[23]!=$res_n1[23])) ? '-'.$res_n2[23] : ''),
		'Norm016' => $res_n1[24].(($res_n2[24]&&($res_n2[24]!=$res_n1[24])) ? '-'.$res_n2[24] : ''),
		'Norm0125' => $res_n1[25].(($res_n2[25]&&($res_n2[25]!=$res_n1[25])) ? '-'.$res_n2[25] : ''),
		'Norm01' => $res_n1[26].(($res_n2[26]&&($res_n2[26]!=$res_n1[26])) ? '-'.$res_n2[26] : ''),
		'Norm0071' => $res_n1[27].(($res_n2[27]&&($res_n2[27]!=$res_n1[27])) ? '-'.$res_n2[27] : ''),
		'Norm0063' => $res_n1[28].(($res_n2[28]&&($res_n2[28]!=$res_n1[28])) ? '-'.$res_n2[28] : ''),
		'Norm005' => $res_n1[29].(($res_n2[29]&&($res_n2[29]!=$res_n1[29])) ? '-'.$res_n2[29] : ''),
		'Normm005' => $res_n1[30].(($res_n2[30]&&($res_n2[30]!=$res_n1[30])) ? '-'.$res_n2[30] : '')
		];
		$l=10000;
		}
	elseif ($res1['GOST_TYPE']=='sheb_shlak') {
								 
			$sita = [];
			foreach($res_n1 as $key => $norm)
			{
				if ($norm!="")
					$sita[] = $key;
			}
			 
			$shebenArr = [
				14 => 5,
				36 => 7.5,
				12 => 10,
				35 => 12.5,
				10 => 15,
				8 => 20,
				37 => 25,
				39 => 30,
				5 => 40,
				38 => 50,
				42 => 55,
				41 => 70,
				40 => 87.5,
				46 => 3,
				18 => 1.25,
				16 => 2.5,
				47 => 17.5,
				4 => 60,
				3 => 80,
				34 => 90,
				2 => 100
				
			];
			$sitaName =[];
			foreach($sita as $key=>$sito)
			{
				$sitaName[$key] = $shebenArr[$sito];			
			}
			rsort($sitaName);
				
			$sitaNorm = [];
			foreach($sitaName as $sName)
			{
				foreach($shebenArr as $key=>$sArr)
				{
					if($sArr == $sName) $sitaNorm[]=$key;
				}
			}



			$arr_str = [
				'94-0' => ' для щебня фракции, от 3 до 10 мм',
				'94-1' => ' для щебня фракции, от 5 до 10 мм',
				'94-2' => ' для щебня фракции, св. 10 до 15 мм',
				'94-3' => ' для щебня фракции, св. 10 до 20 мм',
				'94-4' => ' для щебня фракции, св. 15 до 20 мм',
				'94-5' => ' для щебня фракции, св. 20 до 40 мм',
				'94-6' => ' для щебня фракции, св. 40 до 70 мм',
				'94-7' => ' для щебня фракции, св. 40 до 80 мм',
				'94-8' => ' для щебня смеси фракции, от 3 до 20 мм',
				'94-9' => ' для щебня смеси фракции, от 5 до 20 мм',
				'103-0' => ' для щебня фракции от 3 до 10 мм',
				'103-1' => ' для щебня фракции от 5 до 10 мм',
				'103-2' => ' для щебня фракции св. 5 до 15 мм',
				'103-3' => ' для щебня фракции от 10 до 15 мм',
				'103-4' => ' для щебня фракции от 10 до 20 мм',
				'103-5' => ' для щебня фракции от 15 до 20 мм',
				'103-6' => ' для щебня фракции св. 20 до 40 мм',
				'103-7' => ' для щебня фракции св. 40 до 70 мм',
				'103-8' => ' для щебня фракции св. 70 до 120 мм',
				'103-9' => ' для щебня смеси фракции от 5 до 20 мм',
			];

			$name_mat = !empty($arr_str[$res_group_mat['TYPE_MAT_ID']]) ? $arr_str[$res_group_mat['TYPE_MAT_ID']] : '';

			$resultszern[] = [
			'Object' => $prod . ' (' . $probe[$keyCount]['sh_number'][$pn] .')',

			'NameMat' => $name_mat,

			'125D' => number_format($sitaName[0], 1, ',', ''),
			'D' => number_format($sitaName[1], 1, ',', ''),
			'05D_d' => number_format($sitaName[2], 1, ',', ''),
			'd' =>  number_format($sitaName[3], 1, ',', ''),

			'd_2' => !empty($sitaName[4]) && $sitaName[4] == 1.25 ? number_format($sitaName[4], 2, ',', '') : number_format($sitaName[4], 1, ',', ''),

			'Ost125D' => number_format($OSTATKI[0], 2, ',', ''),
			'OstD' => number_format($OSTATKI[1], 2, ',', ''),
			'Ost05D_d' => number_format($OSTATKI[2], 2, ',', ''),
			'Ostd' => number_format($OSTATKI[3], 2, ',', ''),
			'Ostd_2' => number_format($OSTATKI[4], 2, ',', ''),
			'Ostmd' => number_format($OSTATKI[5], 2, ',', ''),
			'POst125D' => number_format($OSTATKI[6], 2, ',', ''),
			'POstD' => number_format($OSTATKI[7], 2, ',', ''),
			'POst05D_d' => number_format($OSTATKI[8], 2, ',', ''),
			'POstd' => number_format($OSTATKI[9], 2, ',', ''),
			'POstd_2' => number_format($OSTATKI[10], 2, ',', ''),
			'POstmd' => number_format($OSTATKI[11], 2, ',', ''),
			'Norm125D' => $res_n1[$sitaNorm[0]].(($res_n2[$sitaNorm[0]]&&($res_n2[$sitaNorm[0]]!=$res_n1[$sitaNorm[0]])) ? '-'.$res_n2[$sitaNorm[0]] : ''),
			'NormD' => $res_n1[$sitaNorm[1]].(($res_n2[$sitaNorm[1]]&&($res_n2[$sitaNorm[1]]!=$res_n1[$sitaNorm[1]])) ? '-'.$res_n2[$sitaNorm[1]] : ''),
			'Norm05D_d' => $res_n1[$sitaNorm[2]].(($res_n2[$sitaNorm[2]]&&($res_n2[$sitaNorm[2]]!=$res_n1[$sitaNorm[2]])) ? '-'.$res_n2[$sitaNorm[2]] : ''),
			'Normd' => $res_n1[$sitaNorm[3]].(($res_n2[$sitaNorm[3]]&&($res_n2[$sitaNorm[3]]!=$res_n1[$sitaNorm[3]])) ? '-'.$res_n2[$sitaNorm[3]] : ''),
			'Normd_2' => $res_n1[$sitaNorm[4]].(($res_n2[$sitaNorm[4]]&&($res_n2[$sitaNorm[4]]!=$res_n1[$sitaNorm[4]])) ? '-'.$res_n2[$sitaNorm[4]] : ''),
			'Normmd' => $res_n1[$sitaNorm[5]].(($res_n2[$sitaNorm[5]]&&($res_n2[$sitaNorm[5]]!=$res_n1[$sitaNorm[5]])) ? '-'.$res_n2[$sitaNorm[5]] : '')
			
			
			];
			$l=10000;
		}
		elseif ($res1['GOST_TYPE']=='sheb'||$res1['GOST_TYPE']=='zern_sheb') {
			$sita = [];
			foreach ($res_n1 as $key => $norm) {
				if ($norm != "")
					$sita[] = $key;
			}

			$shebenArr = [
				17 => 2,
				15 => 4,
				31 => 5.6,
				13 => 8,
				11 => 11.2,
				9 => 16,
				7 => 22.4,
				6 => 31.5,
				32 => 45,
				33 => 63,
				34 => 90,
				45 => 2.8,
				43 => 126,
				44 => 180
			];
			$sitaName = [];
			foreach ($sita as $key => $sito) {
				$sitaName[$key] = $shebenArr[$sito];
			}
			rsort($sitaName);


			$sitaNorm = [];
			foreach ($sitaName as $sName) {
				foreach ($shebenArr as $key => $sArr) {
					if ($sArr == $sName) $sitaNorm[] = $key;
				}
			}

			if (($res1['ID'] == 5305) && (($type_tz == 16) || ($type_tz == 9))) {
				$resultszern[] = [
					'Object' => $prod . ' (' . $probe[1]['sh_number'][0] . ')',
					'Ost25ch' => number_format($OSTATKI[0], 2, ',', ''),
					'Ost125ch' => number_format($OSTATKI[1], 2, ',', ''),
					'Ost063ch' => number_format($OSTATKI[2], 2, ',', ''),
					'Ost0315ch' => number_format($OSTATKI[3], 2, ',', ''),
					'Ost016ch' => number_format($OSTATKI[4], 2, ',', ''),
					'Ost016mch' => number_format($OSTATKI[5], 2, ',', ''),
					'Ost25p' => number_format($OSTATKI[6], 2, ',', ''),
					'Ost125p' => number_format($OSTATKI[7], 2, ',', ''),
					'Ost063p' => number_format($OSTATKI[8], 2, ',', ''),
					'Ost0315p' => number_format($OSTATKI[9], 2, ',', ''),
					'Ost016p' => number_format($OSTATKI[10], 2, ',', ''),
					'Ost016mp' => number_format($OSTATKI[11], 2, ',', ''),
					'Ost111' => number_format($OSTATKI[12], 2, ',', ''),
					'SOst25ch' => $SOstATKI[0] ? number_format($OSTATKI[0], 2, ',', '') : '-',
					'SOst125ch' => $SOstATKI[1] ? number_format($SOstATKI[1], 2, ',', '') : '-',
					'SOst063ch' => $SOstATKI[2] ? number_format($SOstATKI[2], 2, ',', '') : '-',
					'SOst0315ch' => $SOstATKI[3] ? number_format($SOstATKI[3], 2, ',', '') : '-',
					'SOst016ch' => $SOstATKI[4] ? number_format($SOstATKI[4], 2, ',', '') : '-',
					'SOst016mch' => $SOstATKI[5] ? number_format($SOstATKI[5], 2, ',', '') : '-',
					'SOst25p' => $SOstATKI[6] ? number_format($SOstATKI[6], 2, ',', '') : '-',
					'SOst125p' => $SOstATKI[7] ? number_format($SOstATKI[7], 2, ',', '') : '-',
					'SOst063p' => $SOstATKI[8] ? number_format($SOstATKI[8], 2, ',', '') : '-',
					'SOst0315p' => $SOstATKI[9] ? number_format($SOstATKI[9], 2, ',', '') : '-',
					'SOst016p' => $SOstATKI[10] ? number_format($SOstATKI[10], 2, ',', '') : '-',
					'SOst016mp' => $SOstATKI[11] ? number_format($SOstATKI[11], 2, ',', '') : '-',
					'SOst111' => $SOstATKI[12] ? number_format($SOstATKI[12], 2, ',', '') : '-',
					'Norm200' => $res_n1[0] ? $res_n1[0] . (($res_n2[0] && ($res_n2[0] != $res_n1[0])) ? '-' . $res_n2[0] : '') : '-',
					'Norm120' => $res_n1[1] . (($res_n2[1] && ($res_n2[1] != $res_n1[1])) ? '-' . $res_n2[1] : ''),
					'Norm100' => $res_n1[2] . (($res_n2[2] && ($res_n2[2] != $res_n1[2])) ? '-' . $res_n2[2] : ''),
					'Norm80' => $res_n1[3] . (($res_n2[3] && ($res_n2[3] != $res_n1[3])) ? '-' . $res_n2[3] : ''),
					'Norm60' => $res_n1[4] . (($res_n2[4] && ($res_n2[4] != $res_n1[4])) ? '-' . $res_n2[4] : ''),
					'Norm40' => $res_n1[5] . (($res_n2[5] && ($res_n2[5] != $res_n1[5])) ? '-' . $res_n2[5] : ''),
					'Norm315' => $res_n1[6] ? $res_n1[6] . (($res_n2[6] && ($res_n2[6] != $res_n1[6])) ? '-' . $res_n2[6] : '') : '-',
					'Norm224' => $res_n1[7] ? $res_n1[7] . (($res_n2[7] && ($res_n2[7] != $res_n1[7])) ? '-' . $res_n2[7] : '') : '-',
					'Norm20' => $res_n1[8] . (($res_n2[8] && ($res_n2[8] != $res_n1[8])) ? '-' . $res_n2[8] : ''),
					'Norm16' => $res_n1[9] ? $res_n1[9] . (($res_n2[9] && ($res_n2[9] != $res_n1[9])) ? '-' . $res_n2[9] : '') : '-',
					'Norm15' => $res_n1[10] . (($res_n2[10] && ($res_n2[10] != $res_n1[10])) ? '-' . $res_n2[10] : ''),
					'Norm112' => $res_n1[11] ? $res_n1[11] . (($res_n2[11] && ($res_n2[11] != $res_n1[11])) ? '-' . $res_n2[11] : '') : '-',
					'Norm10' => $res_n1[12] . (($res_n2[12] && ($res_n2[12] != $res_n1[12])) ? '-' . $res_n2[12] : ''),
					'Norm8' => $res_n1[13] ? $res_n1[13] . (($res_n2[13] && ($res_n2[13] != $res_n1[13])) ? '-' . $res_n2[13] : '') : '-',
					'Norm5' => $res_n1[31] ? $res_n1[31] . (($res_n2[31] && ($res_n2[31] != $res_n1[31])) ? '-' . $res_n2[31] : '') : '-',
					'Norm4' => $res_n1[15] ? $res_n1[15] . (($res_n2[15] && ($res_n2[15] != $res_n1[15])) ? '-' . $res_n2[15] : '') : '-',
					'Norm25' => $res_n1[16] . (($res_n2[16] && ($res_n2[16] != $res_n1[16])) ? '-' . $res_n2[16] : ''),
					'Norm2' => $res_n1[17] ? $res_n1[17] . (($res_n2[17] && ($res_n2[17] != $res_n1[17])) ? '-' . $res_n2[17] : '') : '-',
					'Norm125' => $res_n1[18] . (($res_n2[18] && ($res_n2[18] != $res_n1[18])) ? '-' . $res_n2[18] : ''),
					'Norm1' => $res_n1[19] ? $res_n1[19] . (($res_n2[19] && ($res_n2[19] != $res_n1[19])) ? '-' . $res_n2[19] : '') : '-',
					'Norm063' => $res_n1[20] . (($res_n2[20] && ($res_n2[20] != $res_n1[20])) ? '-' . $res_n2[20] : ''),
					'Norm05' => $res_n1[21] ? $res_n1[21] . (($res_n2[21] && ($res_n2[21] != $res_n1[21])) ? '-' . $res_n2[21] : '') : '-',
					'Norm0315' => $res_n1[22] . (($res_n2[22] && ($res_n2[22] != $res_n1[22])) ? '-' . $res_n2[22] : ''),
					'Norm025' => $res_n1[23] ? $res_n1[23] . (($res_n2[23] && ($res_n2[23] != $res_n1[23])) ? '-' . $res_n2[23] : '') : '-',
					'Norm016' => $res_n1[24] . (($res_n2[24] && ($res_n2[24] != $res_n1[24])) ? '-' . $res_n2[24] : ''),
					'Norm0125' => $res_n1[25] ? $res_n1[25] . (($res_n2[25] && ($res_n2[25] != $res_n1[25])) ? '-' . $res_n2[25] : '') : '-',
					'Norm01' => $res_n1[26] . (($res_n2[26] && ($res_n2[26] != $res_n1[26])) ? '-' . $res_n2[26] : ''),
					'Norm0071' => $res_n1[27] . (($res_n2[27] && ($res_n2[27] != $res_n1[27])) ? '-' . $res_n2[27] : ''),
					'Norm0063' => $res_n1[28] ? $res_n1[28] . (($res_n2[28] && ($res_n2[28] != $res_n1[28])) ? '-' . $res_n2[28] : '') : '-',
					'Norm005' => $res_n1[29] . (($res_n2[29] && ($res_n2[29] != $res_n1[29])) ? '-' . $res_n2[29] : ''),
					'Normm005' => $res_n1[30] . (($res_n2[30] && ($res_n2[30] != $res_n1[30])) ? '-' . $res_n2[30] : '')
				];
			} else {
				$resultszern[] = [
					'Object' => $prod . ' (' . $probe[$keyCount]['sh_number'][$pn] . ')',
					'NameMat' => $res_group_mat['NAME'],
					'2D' => $sitaName[0],
					'14D' => $sitaName[1],
					'D' => $sitaName[2],
					'D14' => count($sitaName) > 5 ? $sitaName[3] : '',
					'd' => count($sitaName) > 5 ? $sitaName[4] : $sitaName[3],
					'd_2' => count($sitaName) > 5 ? $sitaName[5] : $sitaName[4],
					'Ost2D' => number_format($OSTATKI[0], 1, ',', ''),
					'Ost14D' => number_format($OSTATKI[1], 1, ',', ''),
					'OstD' => number_format($OSTATKI[2], 1, ',', ''),
					'OstD14' => number_format($OSTATKI[3], 1, ',', ''),
					'Ostd' => number_format($OSTATKI[4], 1, ',', ''),
					'Ostd_2' => number_format($OSTATKI[5], 1, ',', ''),
					'Norm2D' => $res_n1[$sitaNorm[0]] . (($res_n2[$sitaNorm[0]] && ($res_n2[$sitaNorm[0]] != $res_n1[$sitaNorm[0]])) ? '-' . $res_n2[$sitaNorm[0]] : ''),
					'Norm14D' => $res_n1[$sitaNorm[1]] . (($res_n2[$sitaNorm[1]] && ($res_n2[$sitaNorm[1]] != $res_n1[$sitaNorm[1]])) ? '-' . $res_n2[$sitaNorm[1]] : ''),
					'NormD' => $res_n1[$sitaNorm[2]] . (($res_n2[$sitaNorm[2]] && ($res_n2[$sitaNorm[2]] != $res_n1[$sitaNorm[2]])) ? '-' . $res_n2[$sitaNorm[2]] : ''),
					'NormD14' => count($sitaName) > 5 ? $res_n1[$sitaNorm[3]] . (($res_n2[$sitaNorm[3]] && ($res_n2[$sitaNorm[3]] != $res_n1[$sitaNorm[3]])) ? '-' . $res_n2[$sitaNorm[3]] : '') : '- ',
					'Normd' => count($sitaName) > 5 ? $res_n1[$sitaNorm[4]] . (($res_n2[$sitaNorm[4]] && ($res_n2[$sitaNorm[4]] != $res_n1[$sitaNorm[4]])) ? '-' . $res_n2[$sitaNorm[4]] : '') : $res_n1[$sitaNorm[3]] . (($res_n2[$sitaNorm[3]] && ($res_n2[$sitaNorm[3]] != $res_n1[$sitaNorm[3]])) ? '-' . $res_n2[$sitaNorm[3]] : ''),
					'Normd_2' => count($sitaName) > 5 ? $res_n1[$sitaNorm[5]] . (($res_n2[$sitaNorm[5]] && ($res_n2[$sitaNorm[5]] != $res_n1[$sitaNorm[5]])) ? '-' . $res_n2[$sitaNorm[5]] : '') : $res_n1[$sitaNorm[4]] . (($res_n2[$sitaNorm[4]] && ($res_n2[$sitaNorm[4]] != $res_n1[$sitaNorm[4]])) ? '-' . $res_n2[$sitaNorm[4]] : ''),

				];
			}
				$l = 10000;
			}

	}
	}
}

}


	if($type_tz == 37) { 
		$number_probe = (count($number_probes)>1 ? count($number_probes) . " " . num2word(count($number_probes), ['проба', 'образца', 'образцов']) . ": " : ""); //. implode(", ", $number_probes);
	} else {
		$number_probe = (count($number_probes)>1 ? count($number_probes) . " " . num2word(count($number_probes), ['проба', 'пробы/образца', 'проб/образцов']) . ": " : ""); //. implode(", ", $number_probes);
	}

	
for($i=0; $i<count($number_probes); $i++)
{
	$probesValue = explode(', ID', $number_probes[$i]);

	$number_probe = $number_probe . $probesValue[0] . " (" . $numbers[$i] . ")" . ((count($number_probes)-(int)$i)==1 ? "" : ", ");

}


$j=0;
$GOST_DOC3 = [];
for ($i=0; $i<count($GOST2); $i++) {

  $years='';
  if($YEARS[$i]) $years='-'.$YEARS[$i];
  $years2='';
  if($YEARS2[$i]) $years2='-'.$YEARS2[$i];
  if($GOST2[$i]!='-'&&$GOST_TYPE[$i]!='metodic_otbor')
  $SPECIFICATION_GOST[$i] = $GOST2[$i].$years.' '.$GOST_DOC_DESC_MET[$i];

if($GOST_DOC[$i]=='--'||$GOST_DOC[$i]==''||$UNNORM[$i]==1) { }
  else {
  $GOST_DOC3[$j] = $GOST_DOC[$i].$years2.' '.$GOST_DOC_DESC_TU[$i];
  $j++;
  }
}

$SPECIFICATION = implode(", ", array_unique($SPECIFICATION));
$SPECIFICATION_GOST = implode(", ", array_unique($SPECIFICATION_GOST));


if (!$noCompliance)
{
 $GOST_DOC = implode(", ", array_unique($GOST_DOC3));

 $GOST_DOC = 'и оценить на соответствие требованиям '.$GOST_DOC;
}
else
 $GOST_DOC='';

$GOST = implode(", ", array_unique($GOST_DOC_PLUS_DESC));
for ($i=0; $i<count($results); $i++) {
 $results[$i]['Values'] = implode("                             ", $results[$i]['Values']);	
}



$req = new \Bitrix\Crm\EntityRequisite();
$rs = $req->getList([
    "filter" => [
        "ENTITY_ID" => $deal['COMPANY_ID'],
    ]
]);
$requis = $rs->fetch();

$assigned = CUser::GetByID($USER->GetID())->arResult[0];
$assigned_user = $assigned['NAME'].' '.$assigned['LAST_NAME'];



$dogovorNumber = explode(', ID', $ba_tz['DOGOVOR_NUM']);
$dogovor_num = ($dogovorNumber[0] ? $dogovorNumber[0] : '-');
//if($ba_tz['NO_CONTRACT']) $dogovor_num = "Акт приемки проб №".$ba_tz['ACT_NUM'].$y_date_act." от ".ruDate($ba_tz['DATE_ACT']);
if($сurrentDataProtocol['NO_CONTRACT']) $dogovor_num = "Акт приемки проб №".$ba_tz['ACT_NUM'].$y_date_act." от ".ruDate($ba_tz['DATE_ACT']);


$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';


$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';

$arr_num_request = !empty($num_request) ? explode(' ', $num_request) : '';

$str_num_request = !empty($arr_num_request[1]) ? ' (Заявка ' . $arr_num_request[1] . ')' : ''; 

$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . date('Y-m-d H:i:s') . "','" . $assigned_user . "','" . $protocolNum . "','" . $TZ_ID . "','" . $USER->GetID() . "', 'Скачивание протокола', '" . $num_request . "')");

if($metod_otbor) {
	$res_otbor = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=".$metod_otbor)->Fetch();
	$metod_otbor2 = $res_otbor['GOST']. " " . $res_otbor['SPECIFICATION'];
	}



//$point = $selectionType ? "." : ",";
//$selectionType = $selectionType ? '' : '– пробы отобраны и доставлены в ИЦ заказчиком.';
$point = $ba_tz['SELECTION_TYPE'] ? "." : ",";
$selectionType = $ba_tz['SELECTION_TYPE'] ? '' : '– пробы отобраны и доставлены в ИЦ заказчиком.';
if ($ID < getIdDealAfterWhichOborudConnectedGost()) {
    console_log('false', 'false');
    $tIspit = $temp_p==$temp_p2 ? number_format($temp_p, 1, ',', '')." " : "(".number_format($temp_p, 1, ',', '')." - ".number_format($temp_p2, 1, ',', '').") ";
    $vIspit = $vlag_p==$vlag_p2 ? number_format($vlag_p, 1, ',', '') : "(".number_format($vlag_p, 1, ',', '')." - ".number_format($vlag_p2, 1, ',', '').")";
} else {
    $tIspit = implode('; ', array_unique($arrTemperature));
    $vIspit = implode('; ', array_unique($arrHumidity));
}

$document->normalizeContent();

$document->setValues([

    'nProtocol' => $protocolNum.$y_date_p,
	'A' => ($ba_tz['ATTESTAT'] && $inAttestatDiapason ? 'A' : ''),
	'Attestat' => ($attestatInProtocol ? ' Уникальный номер записи об аккредитации в реестре аккредитованных лиц RA.RU 21ЧЦ49' : ''),
	'Attestat2' =>($attestatInProtocol ? '' : ''),
    'dProtocol' => ($protocolDate ? date("d.m.Y", strtotime($protocolDate)) : date("d.m.Y")),
	'nDogovor' => $dogovor_num . $str_num_request,
    'dDorovor' => '',
    'nZakazchik' => $deal['COMPANY_TITLE'],
    'innZakazchik' => $requis['RQ_INN'],
    'ogrnZakazchik' => $requis['RQ_OGRN'],
    'aZakazchik' => $requis['RQ_ACCOUNTANT'],
    'dProbe' => ($ba_tz['DATE_ACT'] ? date("d.m.Y", strtotime($ba_tz['DATE_ACT'])) : end($probe)['dat']),
	'mestoSboraProbe' => (string)end($probeCurrentProtocol)['mesto_data'],
	'oStroit' => $object,
    'oIspit' => $number_probe,
    'nProbe' => $number,
    'nProbe_reg' => !empty($type_tz == 37) ? $ba_tz['ACT_NUM'] . '-1' . $y_date_act : $ba_tz['ACT_NUM'].$y_date_act,
	'celIspit' => $SPECIFICATION,
    'metodIspit' => $SPECIFICATION_GOST,
	'normDoc' => $GOST_DOC,
    'mIspit' => $lab,
    'd1Ispit' => $date1,
    'd2Ispit' => ((string)$date2==(string)$date1 ? '' : '-'.(string)$date2),
    //'tIspit' => $temp_p==$temp_p2 ? number_format($temp_p, 1, ',', '')." " : "(".number_format($temp_p, 1, ',', '')." - ".number_format($temp_p2, 1, ',', '').") ",
    //'vIspit' => $vlag_p==$vlag_p2 ? number_format($vlag_p, 1, ',', '') : "(".number_format($vlag_p, 1, ',', '')." - ".number_format($vlag_p2, 1, ',', '').")",
    'tIspit' => $tIspit,
    'vIspit' => $vIspit,
    'dopInfo' => $dopinfo,
	'Description' => $description,
	'metodOtbor' => $metod_otbor2,
	'placeIspit' => $probe['mesto_data'],
	'ISP' => (count($verify) > 1 ? 'Исполнители' : 'Исполнитель'),
	'Point' => $point,
	'OtborProb' => $selectionType,
	'Verify' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $verify_arr,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'VerifyItemWorkPos' => 'Verify.Item.WorkPos',
	'VerifyItemIspolnitel' => 'Verify.Item.Ispolnitel',
	'VerifyItemIspolnitel2' => 'Verify.Item.Ispolnitel2',
	'VerifyItemIC' => 'Verify.Item.IC',
   
    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $oborud,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),

        'TableItemName' => ($type_tz == 20) ? $ob_arr['Name'] : 'Table.Item.Name',

        'TableItemPoverka' => ($type_tz == 20) ? $ob_arr['Poverka'] : 'Table.Item.Poverka',
    
	'Resultszern' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $resultszern,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'ResultszernItemObject' => 'Resultszern.Item.Object',
	'ResultszernItemNameMat' => 'Resultszern.Item.NameMat',
	'ResultszernItem2D' => 'Resultszern.Item.2D',
	'ResultszernItem14D' => 'Resultszern.Item.14D',
	'ResultszernItemD' => 'Resultszern.Item.D',
	'ResultszernItemD14' => 'Resultszern.Item.D14',
	'ResultszernItemd' => 'Resultszern.Item.d',
	'ResultszernItemd_2' => 'Resultszern.Item.d_2',
	'ResultszernItemOst2D' => 'Resultszern.Item.Ost2D',
	'ResultszernItemOst14D' => 'Resultszern.Item.Ost14D',
	'ResultszernItemOstD' => 'Resultszern.Item.OstD',
	'ResultszernItemOstD14' => 'Resultszern.Item.OstD14',
	'ResultszernItemOstd' => 'Resultszern.Item.Ostd',
	'ResultszernItemOstd_2' => 'Resultszern.Item.Ostd_2',
	'ResultszernItem125D' => 'Resultszern.Item.125D',
	'ResultszernItem05D_d' => 'Resultszern.Item.05D_d',
	'ResultszernItemmd' => 'Resultszern.Item.md',
	'ResultszernItemOst25ch' => 'Resultszern.Item.Ost25ch',
	'ResultszernItemOst200p' => 'Resultszern.Item.Ost200p',
	'ResultszernItemOst200ch' => 'Resultszern.Item.Ost200ch',
	'ResultszernItemOst120p' => 'Resultszern.Item.Ost120p',
	'ResultszernItemOst120ch' => 'Resultszern.Item.Ost120ch',
	'ResultszernItemOst100p' => 'Resultszern.Item.Ost100p',
	'ResultszernItemOst100ch' => 'Resultszern.Item.Ost100ch',
	'ResultszernItemOst80p' => 'Resultszern.Item.Ost80p',
	'ResultszernItemOst80ch' => 'Resultszern.Item.Ost80ch',
	'ResultszernItemOst60p' => 'Resultszern.Item.Ost60p',
	'ResultszernItemOst60ch' => 'Resultszern.Item.Ost60ch',
	'ResultszernItemOst40p' => 'Resultszern.Item.Ost40p',
	'ResultszernItemOst40ch' => 'Resultszern.Item.Ost40ch',
	'ResultszernItemOst20p' => 'Resultszern.Item.Ost20p',
	'ResultszernItemOst20ch' => 'Resultszern.Item.Ost20ch',
	'ResultszernItemOst125D' => 'Resultszern.Item.Ost125D',
	'ResultszernItemOst05D_d' => 'Resultszern.Item.Ost05D_d',
	'ResultszernItemOstmd' => 'Resultszern.Item.Ostmd',
	'ResultszernItemPOst125D' => 'Resultszern.Item.POst125D',
	'ResultszernItemPOstD' => 'Resultszern.Item.POstD',
	'ResultszernItemPOst05D_d' => 'Resultszern.Item.POst05D_d',
	'ResultszernItemPOstd' => 'Resultszern.Item.POstd',
	'ResultszernItemPOstd_2' => 'Resultszern.Item.POstd_2',
	'ResultszernItemPOstmd' => 'Resultszern.Item.POstmd',
	'ResultszernItemOst125ch' => 'Resultszern.Item.Ost125ch',
	'ResultszernItemOst063ch' => 'Resultszern.Item.Ost063ch',
	'ResultszernItemOst0315ch' => 'Resultszern.Item.Ost0315ch',
	'ResultszernItemOst016ch' => 'Resultszern.Item.Ost016ch',
	'ResultszernItemOst016mch' => 'Resultszern.Item.Ost016mch',
	'ResultszernItemOst25p' => 'Resultszern.Item.Ost25p',
	'ResultszernItemOst125p' => 'Resultszern.Item.Ost125p',
	'ResultszernItemOst063p' => 'Resultszern.Item.Ost063p',
	'ResultszernItemOst0315p' => 'Resultszern.Item.Ost0315p',
	'ResultszernItemOst016p' => 'Resultszern.Item.Ost016p',
	'ResultszernItemOst016mp' => 'Resultszern.Item.Ost016mp',
	'ResultszernItemOst10ch' => 'Resultszern.Item.Ost10ch',
	'ResultszernItemOst5ch' => 'Resultszern.Item.Ost5ch',
	'ResultszernItemOst2ch' => 'Resultszern.Item.Ost2ch',
	'ResultszernItemOst1ch' => 'Resultszern.Item.Ost1ch',
	'ResultszernItemOst05ch' => 'Resultszern.Item.Ost05ch',
	'ResultszernItemOst025ch' => 'Resultszern.Item.Ost025ch',
	'ResultszernItemOst01ch' => 'Resultszern.Item.Ost01ch',
	'ResultszernItemOst005ch' => 'Resultszern.Item.Ost005ch',
	'ResultszernItemOst005mch' => 'Resultszern.Item.Ost005mch',
	'ResultszernItemOst001ch' => 'Resultszern.Item.Ost001ch',
	'ResultszernItemOst0002ch' => 'Resultszern.Item.Ost0002ch',
	'ResultszernItemOst0002mch' => 'Resultszern.Item.Ost0002mch',
	'ResultszernItemOst10p' => 'Resultszern.Item.Ost10p',
	'ResultszernItemOst5p' => 'Resultszern.Item.Ost5p',
	'ResultszernItemOst2p' => 'Resultszern.Item.Ost2p',
	'ResultszernItemOst1p' => 'Resultszern.Item.Ost1p',
	'ResultszernItemOst05p' => 'Resultszern.Item.Ost05p',
	'ResultszernItemOst025p' => 'Resultszern.Item.Ost025p',
	'ResultszernItemOst01p' => 'Resultszern.Item.Ost01p',
	'ResultszernItemOst005p' => 'Resultszern.Item.Ost005p',
	'ResultszernItemOst005mp' => 'Resultszern.Item.Ost005mp',
	'ResultszernItemOst315p' => 'Resultszern.Item.Ost315p',
	'ResultszernItemOst224p' => 'Resultszern.Item.Ost224p',
	'ResultszernItemOst16p' => 'Resultszern.Item.Ost16p',
	'ResultszernItemOst8p' => 'Resultszern.Item.Ost8p',
	'ResultszernItemOst4p' => 'Resultszern.Item.Ost4p',
	'ResultszernItemOst025p' => 'Resultszern.Item.Ost025p',
	'ResultszernItemOst0125p' => 'Resultszern.Item.Ost0125p',
	'ResultszernItemOst0063p' => 'Resultszern.Item.Ost0063p',
	'ResultszernItemOstm0063p' => 'Resultszern.Item.Ostm0063p',
	'ResultszernItemOst315ch' => 'Resultszern.Item.Ost315ch',
	'ResultszernItemOst224ch' => 'Resultszern.Item.Ost224ch',
	'ResultszernItemOst16ch' => 'Resultszern.Item.Ost16ch',
	'ResultszernItemOst8ch' => 'Resultszern.Item.Ost8ch',
	'ResultszernItemOst4ch' => 'Resultszern.Item.Ost4ch',
	'ResultszernItemOst025ch' => 'Resultszern.Item.Ost025ch',
	'ResultszernItemOst0125ch' => 'Resultszern.Item.Ost0125ch',
	'ResultszernItemOst0063ch' => 'Resultszern.Item.Ost0063ch',
	'ResultszernItemOstm0063ch' => 'Resultszern.Item.Ostm0063ch',
	'ResultszernItemOst001p' => 'Resultszern.Item.Ost001p',
	'ResultszernItemOst0002p' => 'Resultszern.Item.Ost0002p',
	'ResultszernItemOst0002mp' => 'Resultszern.Item.Ost0002mp',
	'ResultszernItemOst111' => 'Resultszern.Item.Ost111',
	'ResultszernItemSOst25ch' => 'Resultszern.Item.SOst25ch',
	'ResultszernItemSOst125ch' => 'Resultszern.Item.SOst125ch',
	'ResultszernItemSOst063ch' => 'Resultszern.Item.SOst063ch',
	'ResultszernItemSOst0315ch' => 'Resultszern.Item.SOst0315ch',
	'ResultszernItemSOst016ch' => 'Resultszern.Item.SOst016ch',
	'ResultszernItemSOst016mch' => 'Resultszern.Item.SOst016mch',
	'ResultszernItemSOst25p' => 'Resultszern.Item.SOst25p',
	'ResultszernItemSOst125p' => 'Resultszern.Item.SOst125p',
	'ResultszernItemSOst063p' => 'Resultszern.Item.SOst063p',
	'ResultszernItemSOst0315p' => 'Resultszern.Item.SOst0315p',
	'ResultszernItemSOst016p' => 'Resultszern.Item.SOst016p',
	'ResultszernItemSOst016mp' => 'Resultszern.Item.SOst016mp',
	'ResultszernItemSOst10ch' => 'Resultszern.Item.SOst10ch',
	'ResultszernItemSOst5ch' => 'Resultszern.Item.SOst5ch',
	'ResultszernItemSOst2ch' => 'Resultszern.Item.SOst2ch',
	'ResultszernItemSOst1ch' => 'Resultszern.Item.SOst1ch',
	'ResultszernItemSOst05ch' => 'Resultszern.Item.SOst05ch',
	'ResultszernItemSOst025ch' => 'Resultszern.Item.SOst025ch',
	'ResultszernItemSOst01ch' => 'Resultszern.Item.SOst01ch',
	'ResultszernItemSOst005ch' => 'Resultszern.Item.SOst005ch',
	'ResultszernItemSOst005mch' => 'Resultszern.Item.SOst005mch',
	'ResultszernItemSOst10p' => 'Resultszern.Item.SOst10p',
	'ResultszernItemSOst5p' => 'Resultszern.Item.SOst5p',
	'ResultszernItemSOst2p' => 'Resultszern.Item.SOst2p',
	'ResultszernItemSOst1p' => 'Resultszern.Item.SOst1p',
	'ResultszernItemSOst05p' => 'Resultszern.Item.SOst05p',
	'ResultszernItemSOst025p' => 'Resultszern.Item.SOst025p',
	'ResultszernItemSOst01p' => 'Resultszern.Item.SOst01p',
	'ResultszernItemSOst005p' => 'Resultszern.Item.SOst005p',
	'ResultszernItemSOst005mp' => 'Resultszern.Item.SOst005mp',
	'ResultszernItemSOst111' => 'Resultszern.Item.SOst111',
	'ResultszernItemNorm200' => 'Resultszern.Item.Norm200',
	'ResultszernItemNorm120' => 'Resultszern.Item.Norm120',
	'ResultszernItemNorm80' => 'Resultszern.Item.Norm80',
	'ResultszernItemNorm40' => 'Resultszern.Item.Norm40',
	'ResultszernItemNorm315' => 'Resultszern.Item.Norm315',
	'ResultszernItemNorm224' => 'Resultszern.Item.Norm224',
	'ResultszernItemNorm20' => 'Resultszern.Item.Norm20',
	'ResultszernItemNorm16' => 'Resultszern.Item.Norm16',
	'ResultszernItemNorm15' => 'Resultszern.Item.Norm15',
	'ResultszernItemNorm112' => 'Resultszern.Item.Norm112',
	'ResultszernItemNorm10' => 'Resultszern.Item.Norm10',
	'ResultszernItemNorm8' => 'Resultszern.Item.Norm8',
	'ResultszernItemNorm5' => 'Resultszern.Item.Norm5',
	'ResultszernItemNorm4' => 'Resultszern.Item.Norm4',
	'ResultszernItemNorm25' => 'Resultszern.Item.Norm25',
	'ResultszernItemNorm2' => 'Resultszern.Item.Norm2',
	'ResultszernItemNorm125' => 'Resultszern.Item.Norm125',
	'ResultszernItemNorm1' => 'Resultszern.Item.Norm1',
	'ResultszernItemNorm063' => 'Resultszern.Item.Norm063',
	'ResultszernItemNorm05' => 'Resultszern.Item.Norm05',
	'ResultszernItemNorm0315' => 'Resultszern.Item.Norm0315',
	'ResultszernItemNorm025' => 'Resultszern.Item.Norm025',
	'ResultszernItemNorm016' => 'Resultszern.Item.Norm016',
	'ResultszernItemNorm0125' => 'Resultszern.Item.Norm0125',
	'ResultszernItemNorm01' => 'Resultszern.Item.Norm01',
	'ResultszernItemNorm0071' => 'Resultszern.Item.Norm0071',
	'ResultszernItemNorm0063' => 'Resultszern.Item.Norm0063',
	'ResultszernItemNorm005' => 'Resultszern.Item.Norm005',
	'ResultszernItemNormm005' => 'Resultszern.Item.Normm005',
	'ResultszernItemNorm2D' => 'Resultszern.Item.Norm2D',
	'ResultszernItemNorm14D' => 'Resultszern.Item.Norm14D',
	'ResultszernItemNormD' => 'Resultszern.Item.NormD',
	'ResultszernItemNormD14' => 'Resultszern.Item.NormD14',
	'ResultszernItemNormd' => 'Resultszern.Item.Normd',
	'ResultszernItemNormd_2' => 'Resultszern.Item.Normd_2',		
	'ResultszernItemNormmd' => 'Resultszern.Item.Normmd',
	'ResultszernItemNorm125D' => 'Resultszern.Item.Norm125D',
	'ResultszernItemNorm05D_d' => 'Resultszern.Item.Norm05D_d',	
	
    'Results' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'ResultsItemObject' => 'Results.Item.Object',
	'ResultsItemLotNumber' => 'Results.Item.LotNumber', 												 
    'ResultsItemSpecification' => 'Results.Item.Specification',
	'ResultsItemSpecificationTU' => 'Results.Item.SpecificationTU',
    'ResultsItemEd' => 'Results.Item.Ed',
	'ResultsItemEdIndex' => 'Results.Item.EdIndex',
    'ResultsItemNormdoc' => 'Results.Item.Normdoc',
    'ResultsItemNormval' => 'Results.Item.Normval',
    'ResultsItemNormdocmet' => 'Results.Item.Normdocmet',
	'ResultsItemValues' => 'Results.Item.Values',
    'ResultsItemValue' => 'Results.Item.Value',
    'ResultsItemResult' => 'Results.Item.Result',
    'ResultsItemLine' => 'Results.Item.Line',
    'ResultsItemBr' => 'Results.Item.Br',

        'controlDamage' => $controlDamage,
        'mainDamage' => $mainDamage,
        'controlMass' => $controlMass,
        'mainMass' => $mainMass,
        'controlStrength1' => !empty($controlStrength[0]) ? str_replace('.', ',', $controlStrength[0]) : '',//
        'controlStrength2' => !empty($controlStrength[1]) ? str_replace('.', ',', $controlStrength[1]) : '',
        'controlStrength3' => !empty($controlStrength[2]) ? str_replace('.', ',', $controlStrength[2]) : '',
        'controlStrength4' => !empty($controlStrength[3]) ? str_replace('.', ',', $controlStrength[3]) : '',
        'controlStrength5' => !empty($controlStrength[4]) ? str_replace('.', ',', $controlStrength[4]) : '',
        'controlStrength6' => !empty($controlStrength[5]) ? str_replace('.', ',', $controlStrength[5]) : '',
        'mainStrength1' => !empty($mainStrength[0]) ? str_replace('.', ',', $mainStrength[0]) : '',//
        'mainStrength2' => !empty($mainStrength[1]) ? str_replace('.', ',', $mainStrength[1]) : '',
        'mainStrength3' => !empty($mainStrength[2]) ? str_replace('.', ',', $mainStrength[2]) : '',
        'mainStrength4' => !empty($mainStrength[3]) ? str_replace('.', ',', $mainStrength[3]) : '',
        'mainStrength5' => !empty($mainStrength[4]) ? str_replace('.', ',', $mainStrength[4]) : '',
        'mainStrength6' => !empty($mainStrength[5]) ? str_replace('.', ',', $mainStrength[5]) : '',
        'controlMedium' => $controlMedium,
        'mainMedium' => $mainMedium,
        'controlBottomLine' => $controlBottomLine,
        'mainBottomLine' => $mainBottomLine,
        'ratio' => $ratio,
        'tested' => $tested,
        'match' => $match,	
		'mark' => $mark_material,
		'round' => $round . " циклов",

		'ThermalConductivity' => new DocumentGenerator\DataProvider\ArrayDataProvider(
			$thermalConductivityDatasetArray,
			[
				'ITEM_NAME' => 'Item',
				'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
			]
		),
		'makros1' => 'ThermalConductivity.Item.Makros1',
		'makros2' => 'ThermalConductivity.Item.Makros2',
		'makros3' => 'ThermalConductivity.Item.Makros3',
		'makros4' => 'ThermalConductivity.Item.Makros4',
		'makros5' => 'ThermalConductivity.Item.Makros5',
		'makros6' => 'ThermalConductivity.Item.Makros6',
		'makros7' => 'ThermalConductivity.Item.Makros7',
		'makros8' => 'ThermalConductivity.Item.Makros8',
		'makros9' => 'ThermalConductivity.Item.Makros9',
		'makros10' => 'ThermalConductivity.Item.Makros10',
		'makros11' => 'ThermalConductivity.Item.Makros11',
		'makros12' => 'ThermalConductivity.Item.Makros12',
		'makros13' => 'ThermalConductivity.Item.Makros13',
		'makros14' => 'ThermalConductivity.Item.Makros14',
		'makro15' => $makros15,
		'makro16' => $makros16,

]

);


$result = $document->process();
$content = $document->getContent();

$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();

$res_deals_contract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$ba_tz['ID_Z'])->Fetch();
if($res_deals_contract['ID_CONTRACT'])
{
	$res_acc2 = $DB->Query("SELECT * FROM `INVOICE` WHERE `CONTRACT_ID`=" . $res_deals_contract['ID_CONTRACT'])->Fetch();	
		if (!empty($res_deals_contract['ID_CONTRACT'])) {
		
			$res_gogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$res_deals_contract['ID_CONTRACT'])->Fetch();
		
		}
	
}
else
{

	//if($USER->GetID()!='1' && $USER->GetID()!='9')
    if($USER->GetID()!='1' && $USER->GetID()!='7')
	{
	 echo "Протокол не может быть сформирован - в заявке не указан договор!";
	 die;
	}
}
if ($ba_tz['TYPE_ID'] != 2) {
if(!$res_acc['ID'] && !$res_acc2['ID']  && !$ba_tz['TAKEN_ID_DEAL'] && ($USER->GetID()!='13'))
{
	$invoice_check = 0;
	echo "Протокол не может быть сформирован - по заявке не выставлен счет!";
}
}

if ($ID >= getIdDealAfterWhichOborudConnectedGost()) {
    $conditions = $oborud_check&&($key_check==FALSE)&&$date_check&&$invoice_check&&$temp_in_ob_check&&$vlag_in_ob_check&&$temp_in_gost_check&&$vlag_in_gost_check&&$oborudToGostCheck;
} else {
    $conditions = $oborud_check&&($key_check==FALSE)&&$date_check&&$invoice_check&&$temp_in_ob_check&&$vlag_in_ob_check&&$temp_in_gost_check&&$vlag_in_gost_check;
}


if(($conditions)||($USER->GetID()=='43')||($USER->GetID()=='10')||($USER->GetID()=='9')||($USER->GetID()=='1')||($USER->GetID()=='7')) {  //&&$temp_in_ob_check&&$vlag_in_ob_check&&$temp_in_gost_check&&$vlag_in_gost_check)

    if (!empty($protocolId)) {


        $newpath = "archive/" . $TZ_ID . $protocolYear . "/" . $protocolId . "/" . $curdate . ".docx";
        $newpath2 = "archive/" . $TZ_ID . $protocolYear . "/" . $protocolId . "/forsign.docx";
		$actualVer = $curdate;
        $actualVer = !empty($actualVer) ? serialize($actualVer) : '';
        $interimPath = "interim_archive_prot/" . $TZ_ID . $protocolYear . "/" . $protocolId. "/" . $curdateEng . ".docx";
        $outputFile = "Протокол №" . $protocolNum . " от " . date("d.m.Y", strtotime($protocolDate)) . ".pdf";
        $outputPath = "archive/" . $TZ_ID . $protocolYear . "/" . $protocolId . "/";

        if (!empty($actualVer)) {
            $res_av = $DB->Query("UPDATE `PROTOCOLS` SET `ACTUAL_VERSION`= '" . $actualVer . "' WHERE `ID`=" . $protocolId);
        }

        function file_force_contents($fullPath, $contents, $flags = 0)
        {
            $parts = explode('/', $fullPath);
            array_pop($parts);
            $dir = implode('/', $parts);

            if (!is_dir($dir))
                mkdir($dir, 0777, true);

            file_put_contents($fullPath, $contents, $flags);
        }

        file_force_contents($newpath, $content);


        if (in_array($type_tz, [33, 34, 35, 36, 37, 38, 39]) && !empty($protocolNum) && is_numeric($protocolNum)) {
            file_force_contents($newpath2, $content);
        }

		$pathDocCurdate = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $newpath;
		
		if (!in_array($type_tz, [33, 34, 35, 36, 37, 38, 39]) && !empty($protocolNum) && is_numeric($protocolNum)) {

			$qrPath = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/archive/' . $TZ_ID . $protocolYear . "/" . $protocolId . '/qrNEW.png';

			QRcode::png("https://niistrom.pro/check/index.php?NUMP=" . $protocolNum . "&DATE=" . date('d.m.Y', strtotime($protocolDate)), $qrPath);

			addQrCode($qrPath, $pathDocCurdate);

		}

        if (!in_array($type_tz, [33, 34, 35, 36, 37, 38, 39]) && empty($protocolOutsideLis) && !empty($protocolNum) && is_numeric($protocolNum)) {
            file_force_contents($interimPath, $content);
			
			$pathDocCurdateEng = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $interimPath;

			addQrCode($qrPath, $pathDocCurdateEng);

            $fullPathDocx = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $interimPath;
            $fullPathPDF = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $outputPath;

            $converter = new  OfficeConverter($fullPathDocx, $fullPathPDF);
            $converter->convertTo($outputFile);
        }

	
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        header('Content-Disposition: attachment;filename="Протокол № ' . $protocolNum . ' от ' . date("d.m.Y") . '.docx"');
        header('Cache-Control: max-age=0');
        
		echo file_get_contents($pathDocCurdate);
    }
}
else echo "<br><br><a href='/results_isp.php?ID=".$ID."&ID_P=".$protocolId."'>Вернуться к результатам испытаний</a>";
die;
