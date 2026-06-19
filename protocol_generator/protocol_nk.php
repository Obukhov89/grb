<?php
/**
 * Created by Makaralex92.
 * User: Алексей
 * Date: 07.05.2019
 * Time: 16:11
 */

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpqrcode/qrlib.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

//Запрашиваем данные
$oborud_check=1;
$temp_in_ob_check =1;
$vlag_in_ob_check=1;
$temp_in_gost_check=1;
$vlag_in_gost_check=1;
//$price_check=1;
$ID = $_GET['ID']; //ID Заявки
$TZ_ID = $_GET['TZ_ID']; //ID Технического задания. В нём хранится ТЗ, результаты исследований и информация о пробах
$deal = CCrmDeal::GetByID($ID); //Берём значение сделки
$company = CCrmCompany::GetByID($deal['COMPANY_ID']);
$ispolnitel = '';
$user = CUser::GetByID($deal['ASSIGNED_BY_ID'])->arResult[0];
$y_date = date("Y", strtotime($deal['DATE_CREATE']));
			if ($y_date != '2020') $y_date = "/".substr($y_date, 2);
			else $y_date = "/".$y_date;

//dump($user);

$workpos = $user['WORK_POSITION'];
$ispolnitel = mb_substr($user['NAME'], 0, 1, 'UTF-8') . '. ' . (($user['SECOND_NAME']) ? mb_substr($user['SECOND_NAME'], 0, 1, 'UTF-8') . '. ' : '') . $user['LAST_NAME'];

global $DB;
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . $TZ_ID);
$ba_tz = $res->Fetch();

$date_protocol = $ba_tz['DATE_P_CORR'] ? $ba_tz['DATE_P_CORR'] :$ba_tz['DATE_P'];


$y_date_act = date("Y", strtotime($ba_tz['DATE_ACT']));
					if ($y_date_act != '2020') $y_date_act = "/".substr($y_date_act, 2);
							else $y_date_act = "/".$y_date_act;
					
					$y_date_p = date("Y", strtotime($date_protocol));
					if ($y_date_p != '2020') $y_date_p = "/".substr($y_date_p, 2);
							else $y_date_p = "/".$y_date_p;

$tz = unserialize($ba_tz['TZ']);
//$date1s = unserialize($ba_tz['DATE1']);
//$date2s = unserialize($ba_tz['DATE2']);
$labs = unserialize($ba_tz['LAB']);
$probe = unserialize($ba_tz['PROBE']);
$results_tz = unserialize($ba_tz['RESULTS']);
$type_tz = $ba_tz['TYPE_TZ'];
$verify = unserialize($ba_tz['VERIFY']);
$MATCH_MANUAL = unserialize($ba_tz['MATCH_MANUAL']);
$SRED = unserialize($ba_tz['SRED']);
$OSTATKI = unserialize($ba_tz['ostatki']);
$SOstATKI = unserialize($ba_tz['SOSTAV']);
$NORM_DESC = unserialize($ba_tz['NORM_DESC']);
$description = $ba_tz['DESCRIPTION'];
$metod_otbor = $ba_tz['OSK_METOD_OTBOR_ID'];

$curdate = "Протокол_".($ba_tz['NUM_P_CORR'] ? $ba_tz['NUM_P_CORR'] : $ba_tz['NUM_P'])."_".date("d-m-Y_H-i-s");

$curdateEng = null;
$curdatePdf = null;
//Если присвоен номер протоколу
if (!empty($ba_tz['NUM_P']) && is_numeric($ba_tz['NUM_P'])) {
    $curdateEng = "Protocol_" . $ba_tz['NUM_P'];
    $curdatePdf = "Протокол_" . $ba_tz['NUM_P'];
}
//echo '_______________________'.$type_tz;
//dump($results_tz);
//var_dump($tz[1]['gost']);
$date1 = $ba_tz['DATE1'];
$date2 = $ba_tz['DATE2'];

$dopinfo = $ba_tz['DOPINFO'];
$accuracy=[];

//преобразуем дату к нужному виду
$date1arr = explode('-',$date1);
$date1 = $date1arr[2].'.'.$date1arr[1].'.'.$date1arr[0];
$date2arr = explode('-',$date2);
$date2 = $date2arr[2].'.'.$date2arr[1].'.'.$date2arr[0];

$verify_arr=[];

foreach($verify as $v)
	{
		$user_v = CUser::GetByID($v)->Fetch();
		$verify_arr[] = [
		'WorkPos' => $user_v['WORK_POSITION'],
		'Ispolnitel' => mb_substr($user_v['NAME'], 0, 1, 'UTF-8') . '. ' . (($user_v['SECOND_NAME']) ? mb_substr($user_v['SECOND_NAME'], 0, 1, 'UTF-8') . '. ' : '') . $user_v['LAST_NAME'],
		'IC' => ($user_v['UF_DEPARTMENT'][0] == 58 ? '' : ' ИЦ')
		];
		
	}
// foreach ($date1s as $key1 => $arr) {
    // foreach ($arr as $items) {
        // foreach ($items as $key2 => $item) {
            // if ($item !== NULL)
                // $date1[] = new DateTime($item);
        // }

    // }
    // foreach ($date2s[$key1] as $key2 => $arrs) {
        // foreach ($arrs as $item) {
            // if ($item !== '')
                // $date2[] = new DateTime($item);
        // }
    // }
// }

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
//echo $type_tz;


    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_NK.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());



//Формируем данные
$actualValue = []; //Массив для сбора фиктических значений одного материала
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
foreach ($probe as $prob) {
    foreach ($prob as $key => $item) {
        switch ($key) {
            case 'number_probe':
                $number_probes = array_merge($number_probes, $item);
                break;
            case 'sh_number':
                $numbers = array_merge($numbers, $item);
                break;
        }
    }
}

 // echo'____________________________________________';
 // var_dump($number_probes);
$ch=1; $tz_ob=0;
$temp_p = tofloat($ba_tz['TEMP_O']);
$vlag_p = tofloat($ba_tz['VLAG_O']);

 $res_ob = $DB->Query("SELECT * FROM `TZ_OB_CONNECT` WHERE `ID_TZ` = " . $ID);
		if(intval($res_ob->SelectedRowsCount())>0) {
			$keyOb = 1;
			while($row = $res_ob->Fetch())
									{
										
										$ob = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=".$row['ID_OB'])->Fetch();
										$poverka_time = 5184000;
										$poverka = strtotime($ob['POVERKA']) - time();
										
										$temp_o1 = tofloat($ob['TOO_EX']);
										$temp_o2 = $ob['TOO_EX2'];
										if($temp_p<$temp_o1||$temp_p>$temp_o2) {$temp_in_ob_check=0; echo "Внимание! Температура при проведении испытаний не соответствует условиям эксплуатации оборудования "  . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] . "! <br><br>";}
													
										$vlag_o1 = tofloat($ob['OVV_EX']);
										$vlag_o2 = $ob['OVV_EX2'];
										if($vlag_p<$vlag_o1||$vlag_p>$vlag_o2) {$vlag_in_ob_check=0; echo "Внимание! Влажность при проведении испытаний не соответствует условиям эксплуатации оборудования "   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] . "! <br><br>";}
										//var_dump($ob);
										
										if(!$ob['CHECKED']) {$oborud_check=0; echo "<p style='color:red'>Протокол не может быть сформирован! Оборудование <a href='/oborud.php?ID=".$ob['ID']."'>"  . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] .  "</a> не проверено! </p><br>"; }
										if($poverka<=0) {$oborud_check=0; echo "<p style='color:red'>Протокол не может быть сформирован! Истек срок поверки оборудования <a href='/oborud.php?ID=".$ob['ID']."'>"   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] .  "</a>! </p><br>"; }
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
											'Name' => $ob['OBJECT'] . ' ' . $ob['TYPE_OBORUD'] . ', ' . $ob['FACTORY_NUMBER'],
											'Num' => $keyOb,
											'Regnum' => $ob['REG_NUM'],
										    'Year' => date("Y", strtotime($ob['god_vvoda_expluatation'])),
											'Poverka' =>  date("d.m.Y", strtotime($ob['POVERKA'])),
										];
										
										if (!in_array($ob_arr, $oborud)) {
											$oborud[] = $ob_arr;
										}
										}
			//dump($ob_arr);
										$tz_ob=1;
										$keyOb++;
									}
		}
	

foreach ($tz as $items) {
    foreach ($items['gost'] as $k => $item) {
		
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
        $gost = $ba_gost->Fetch();
		
		
		$temp_g = unserialize($gost['TEMP']);
		$vlag_g = unserialize($gost['VLAG']);
		if($temp_p<tofloat($temp_g[0])||$temp_p>tofloat($temp_g[1])) {$temp_in_gost_check=0; echo "Внимание! Температура при проведении испытаний не соответствует условиям в методике " . $gost['GOST']. " " . $gost['SPECIFICATION'] . "! <br><br>";}
		if($vlag_p<tofloat($vlag_g[0])||$vlag_p>tofloat($vlag_g[1])) {$vlag_in_gost_check=0; echo "Внимание! Влажность при проведении испытаний не соответствует условиям в методике " . $gost['GOST']. " " . $gost['SPECIFICATION'] . "! <br><br>";}
	    //echo $temp_g;
	   
	   if(!$tz_ob) {
		$ba_connection = $DB->Query("SELECT * FROM `ba_connection` WHERE `GOST_ID`=" . $gost['ID']);

        while ($oborud_id = $ba_connection->Fetch()) {
            $ba_oborud = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . $oborud_id['OBORUD_ID']);
            $ob = $ba_oborud->Fetch();
			$poverka_time = 5184000;
			$poverka = strtotime($ob['POVERKA']) - time();
			 
			$temp_o1 = tofloat($ob['TOO_EX']);
			$temp_o2 = $ob['TOO_EX2'];
			if($temp_p<$temp_o1||$temp_p>$temp_o2) {$temp_in_ob_check=0; echo "Внимание! Температура при проведении испытаний не соответствует условиям эксплуатации оборудования "  . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] . "! <br><br>";}
												
			$vlag_o1 = tofloat($ob['OVV_EX']);
			$vlag_o2 = $ob['OVV_EX2'];
			if($vlag_p<$vlag_o1||$vlag_p>$vlag_o2) {$vlag_in_ob_check=0; echo "Внимание! Влажность при проведении испытаний не соответствует условиям эксплуатации оборудования "   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] . "! <br><br>";}
			
			if(!$ob['CHECKED']) {$oborud_check=0; echo "<p style='color:red'>Протокол не может быть сформирован! Оборудование <a href='/oborud.php?ID=".$ob['ID']."'>"   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] .  "</a> не проверено! </p><br>"; }
			if($poverka<=0) {$oborud_check=0; echo "<p style='color:red'>Протокол не может быть сформирован! Истек срок поверки оборудования <a href='/oborud.php?ID=".$ob['ID']."'>"   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] .  "</a>! </p><br>"; }
			if ($ob['IDENT'] != "VO"&&$ob['IDENT'] != "TS"&&$ob['IDENT'] != "REACT"){
            $res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$ob['ID']);
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
										//	'Class' => $ob['сlass_precision_and_accuracy'],
										//    'Predel' => $ob['measuring_range'],
											'Poverka' => $certificateName . ' до ' . date("d.m.Y", strtotime($ob['POVERKA'])),
										];
			
            if (!in_array($ob_arr, $oborud)) {
                $oborud[] = $ob_arr;
            }
			}
			//var_dump($ob['CHECKED']);
        }
	   }
        $ress = '';
		$res1 = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $items['gost_new'][$k])->Fetch();
		
        $SPECIFICATION[] = $gost['SPECIFICATION'];
		$SPECIFICATION_TU[] = $res1['SPECIFICATION'];
        $GOST_VAL[] = $res1['NORM_COMMENT'];//"От " . $items['norm_1'][$k] . " до " . $items['norm_2'][$k] . " " . $items['ed_izm'][$k];
        $GOST_PUNKT[] = $gost['GOST_PUNKT'];
		$GOST_PUNKT_TU[] = $res1['GOST_PUNKT'];
		$GOST_DOC[] = $res1['GOST'];//$items['gost_new'][$k];
        $GOST_DOC_DESC[] =  $NORM_DESC[$k];//($res1['GOST_TYPE']=='TU_research' ? $NORM_DESC : $res1['NAME_GOST']);//$items['gost_new_desc'][$k];
		$GOST_DOC_DESC_TU[] = $res1['NAME_GOST'];
		$GOST_DOC_DESC_MET[] = $gost['NAME_GOST'];
        if ($items['gost_new'][$k] != '-') $GOST_DOC_PLUS_DESC[] = $items['gost_new'][$k] . " - " . $items['gost_new_desc'][$k];
        $GOST2[] = $gost['GOST'];
        $ED[] = $gost['ED'];//$items['ed_izm'][$k];
		$ED_INDEX[] = $gost['ED_INDEX'];
        $NORM1[] = $res1['NORM1'];//$items['norm_1'][$k];
        $NORM2[] = $res1['NORM2'];//$items['norm_2'][$k];
        $IN_OUT[] = 1;
		$YEARS[] = $gost['GOST_YEAR'];
		$CHECKED[$ch] = $gost['CHECKED'];
		$YEARS2[] = $res1['GOST_YEAR'];
        $MATCH[] = $gost['MATCH_MANUAL'];
		$accuracy[] =$gost['ACCURACY'];
		$GOST_TYPE[]=$gost['GOST_TYPE'];
		$UNNORM[]=$res1['UNNORM'];
		//echo $gost['ACCURACY'];
		//var_dump($accuracy); 
		$ch++;
    }
}
$i = 0;
if(!count($oborud)) {$oborud_check=0; echo "<p style='color:red'>Протокол не может быть сформирован! Не указано используемое оборудование! </p><br>"; }
$key_check = array_search("0", $CHECKED); 
 // var_dump ($CHECKED);
//  echo $tz_ob;
 // var_dump ($GOST2);
if($key_check) {echo "<p style='color:red'>Протокол не может быть сформирован! Используемая методика <a href='/obl_acc.php?ID=".$gost['ID']."'>".$GOST2[$key_check-1].' '.$GOST_PUNKT[$key_check-1]."</a> не проверена отделом метрологии! </p><br>";}

$date_check=1;

$date_result = $ba_tz['DATE_RESULTS'] ? $ba_tz['DATE_RESULTS'] : $ba_tz['DATE_P'];

if(right_date($date_result)>right_date($ba_tz['DATE_P_CORR'])) {
	$date_check=0;
	echo "<p style='color:red'>Протокол не может быть сформирован! Указанная дата протокола меньше даты формирования результатов испытаний!</p><br>";
}

$resultCoincedCoeff = null;
//Получаем данные расчета коэффициента совпадения
if (!empty($ba_tz['COIN_COEFF'])) {
	$resultCoincedCoeff = $DB->Query("SELECT * FROM `COINCED_COEFF` WHERE `ID`=" . (int)$ba_tz['COIN_COEFF'])->Fetch();
}

$prochnPM = !empty($resultCoincedCoeff['PROCHN_PM']) ? unserialize($resultCoincedCoeff['PROCHN_PM']) : 0; //прочность Прямым методом
$prochnKM = !empty($resultCoincedCoeff['PROCHN_KM']) ? unserialize($resultCoincedCoeff['PROCHN_KM']) : 0; //прочность Косвенным методом
$prochnName = !empty($resultCoincedCoeff['NAME']) ? unserialize($resultCoincedCoeff['NAME']) : 0; //Наименование, месторасположение и дата бетонирования конструкции
$prochnChast = !empty($resultCoincedCoeff['PROCHN_CHAST']) ? unserialize($resultCoincedCoeff['PROCHN_CHAST']) : 0; //Частное значение Rос/Rкосв
//echo'<pre>';var_dump('$prochn',$prochnPM,$prochnKM,$prochnName,$prochnChast);echo'</pre>';die();
//Собираем массив значений прочности Прямым методом и Косвенным методом
$prochn = [];
if (isset($prochnPM)) {
	foreach($prochnPM as $keyProchnPM => $valProchnPM) {
		$prochn[] = [
			'prochnPM' => isset($valProchnPM) ? $valProchnPM : 0,
			'prochnKM' => isset($prochnKM[$keyProchnPM]) ? $prochnKM[$keyProchnPM] : 0,
			'prochnName' => isset($prochnName[$keyProchnPM]) ? $prochnName[$keyProchnPM] : 0,
			'prochnChast' => isset($prochnChast[$keyProchnPM]) ? number_format($prochnChast[$keyProchnPM], 2, ',', ' ') : 0
		];
	}
}

//echo'<pre>';var_dump('$ba_tz[COIN_COEFF_VAL]',(double)$ba_tz['COIN_COEFF_VAL']);echo'</pre>';
//$a = (double)$ba_tz['COIN_COEFF_VAL'];
//echo'<pre>';var_dump('$ba_tz[COIN_COEFF_VAL]22222222',number_format($a, 2, '.', ' '));echo'</pre>';

//Коэффициент совпадения
$coinCoeffVal = !empty($ba_tz['COIN_COEFF_VAL']) ? number_format($ba_tz['COIN_COEFF_VAL'], 2, ',', ' ') : ''; 


//echo right_date($ba_tz['DATE_P_CORR']);
//$k=1;
// var_dump($results_tz);
// echo "________________________________";
// var_dump($number_probes);
$num=1;

//$allFactValue=array();

foreach ($tz as $key => $arrDatum) {
	
	foreach($probe[$key]['number_probe'] as $key_probe => $probe_k){
		
	//	dump($probe[$key]);
		$key_probe = $key_probe+1; $j=0; 
		$z="."; $z_pos=0;
		
		//Сумма значений Фактическое единичное значение характеристики (по участкам конструкции)
		$sumActualInitValue = 0;
		//Массив значений с учетом коэффициента сопадения
		$valueGivenСoinCoeffValArray = [];
		
		//Прочность на сжатие
		$compressiveStrength = 0;
		
		//Фактический класс бетона
		$actualClassConcrete = 0;
	
		foreach ($arrDatum['gost'] as $k => $item){
			 
			$calibrDepA = $ba_tz['CALIBR_DEP_A'];
			$calibrDepB = $ba_tz['CALIBR_DEP_B'];
			$coinCoeff = $ba_tz['COIN_COEFF_VAL'];
			$varCoeff = $ba_tz['VAR_KOEFF'];
			$standartDeviation = $ba_tz['CONCRETE_TYPE'];
			$reqStrengthKoeffArr = [];
			$valueAfterCalibrDepArr = []; //массив значений после применения данных калибровочной зависимости
			$factAverageStrength = 0; //Rm, фактическая сред. плотность
			$factConcreteClass = 0; //B, фактический класс бетона
			$procentConcreteClass = 0; //B, % фактического класса бетона
			$factConcreteClassString = '';
			$concreteClass = (float)$probe[$key]['concreteClass'][$key_probe-1];
			$reqStrengthKoeffArr = [
				'1' => 1.07,
				'2' => 1.07,
				'3' => 1.07,
				'4' => 1.07,
				'5' => 1.07,
				'6' => 1.07,
				'7' => 1.08,
				'8' => 1.09,
				'9' => 1.11,
				'10' => 1.14,
				'11' => 1.18,
				'12' => 1.23,
				'13' => 1.28,
				'14' => 1.33,
				'15' => 1.38,
				'16' => 1.43
				];
								
			foreach($results_tz[$key - 1][$k][$key_probe] as $val)
			{
				if($coinCoeff)
					$valueAfterCalibrDepArr[] = $val*$coinCoeff;
				else
					{
						$valueAfterCalibrDepArr[] = $val*$calibrDepA + $calibrDepB;
				//		$allFactValue[] = $val*$calibrDepA + $calibrDepB;
					}
			}
			
		//	dump($valueAfterCalibrDepArr);
			
			$factAverageStrength = round(array_sum($valueAfterCalibrDepArr)/6, 1);
		
			
			
			if($coinCoeff) 
			{
				$factConcreteClass = $factAverageStrength*0.8;
				$factConcreteClassString = "B".number_format($factConcreteClass, 1, ',', '');
			}
			else
			{
				$factConcreteClass = $factAverageStrength*$reqStrengthKoeffArr[$varCoeff];				
				$factConcreteClassString = "B".number_format($factAverageStrength*$reqStrengthKoeffArr[$varCoeff], 1, ',', '');
			}	
									
			$procentConcreteClass = round($factConcreteClass/$concreteClass*100, 0);
			
		//if($tz_key)
			$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
			$res_TU = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $arrDatum['gost_new'][$j])->Fetch();
			
			$ress = $arr_soot[$MATCH_MANUAL[$key - 1][$k][$key_probe]];
			if(!$res['RES_TEXT'])
			{
				for($l=0; $l<count($results_tz[$key - 1][$k][$key_probe]); $l++){
					$z_pos=strpos($results_tz[$key - 1][$k][$key_probe][$l],$z);
					if ($z_pos) $results_tz[$key - 1][$k][$key_probe][$l] = substr_replace($results_tz[$key - 1][$k][$key_probe][$l], ",", $z_pos, 1);
				}
			}
			$z_pos2 = strpos($SRED[$key - 1][$k][$key_probe]);
			$SRED[$key - 1][$k][$key_probe] = number_format($SRED[$key - 1][$k][$key_probe], $res['ACCURACY'], ',', '');
			$norm_description = (count($tz) >1 ? $NORM_DESC[$key-1][$key_probe][$k] : $NORM_DESC[$key_probe][$k]);
			$prod_arr = explode(', ID',$probe_k);
			$prod = $prod_arr[0];
			$obj='';
			$datetime1 = date_create(date("Y-m-d", strtotime($probe[$key]['concreteDate'][$key_probe-1])));
			$datetime2 = date_create(date("Y-m-d",strtotime($date2)));
			$interval = date_diff($datetime1, $datetime2);
			$concrAge = $interval->format('%a');
			// $interval = date_diff($datetime1, $datetime2);
			// $interval->format('%R%a дней');
			//echo'<pre>';var_dump('$item',$item);echo'</pre>';
			
			if($results_tz[$key - 1][$k][$key_probe][0]!=""){
				
				/*if (!$j) {
                    $obj = $prod . " (" . $probe[$key]['sh_number'][$key_probe - 1] . ")";
					$obj_density_grunt = !empty($prod) ? $prod : ''; 								  
                    //elseif($j==count($arrDatum['gost'])-1) $obj="______________";
                }
				//Если есть методики одбора проб(изг. образцов)($count_metodic) и методики с незаполнеными результатами($count_metodic_not_result) то отнимаем их
				if($line_num==count($arrDatum['gost'])-1 - $count_metodic - $count_metodic_not_result) {$line = "______________";}*/
			
				//Строка Фактическое единичное значение характеристики (по участкам конструкции)
				$strActualValue = '';
				
				//Строка соответствия или не соответствия (Соответствие характеристики требованиям нормативной (проектной) документации)
				$conformity = ''; 
				
				//Проверяем если ID ГОСТа == '608', то производим расчеты 
				//if (!empty($results_tz[$key - 1][$k][$key_probe]) && count($results_tz[$key - 1][$k][$key_probe]) == 8) {
				if (!empty($results_tz[$key - 1][$k][$key_probe]) && !empty($item) && $item == '608') {
					
					//Перибираем массив фактических значений (Фактическое единичное значение характеристики (по участкам конструкции))
					foreach($results_tz[$key - 1][$k][$key_probe] as $keyActualValue => $actualValue) {
						//Записываем каждое Фактическое единичное значение характеристики (по участкам конструкции) для вставки в шаблон
						//$arrayActualValue[$keyActualValue] = !empty($actualValue . $keyActualValue) ? $actualValue . $keyActualValue : 0;
						
						/*$strActualValue .= !empty($actualValue) ? $actualValue : 0;
						
							//Если не последний элемент массива , то добавляем перенос строки
							if (end($results_tz[$key - 1][$k][$key_probe]) != $actualValue) {
							$strActualValue .= '
';
							}*/
						
						//Заменяем запятую на точу
						$actualValue = str_replace(',', '.', $actualValue);
						//Находим сумму значений массива
						//$sumActualInitValue += (double)$actualValue;
						//$sumActualInitValue += (double)$actualValue;
						
						//Находим Значение с учетом коэффициента сопадения
						$valueGivenСoinCoeffValue = ((double)$actualValue * number_format(str_replace(',', '.', $coinCoeffVal), 2, '.', ' '));
						
						//Находим сумму значений с учетом коэффициента сопадения
						$sumActualInitValue += (double)$valueGivenСoinCoeffValue;
						
						//Записываем в массив все Значения с учетом коэффициента сопадения
						$valueGivenСoinCoeffValArray[] = $valueGivenСoinCoeffValue;
						
						$strActualValue .= !empty($valueGivenСoinCoeffValue) ? $valueGivenСoinCoeffValue : 0;
						
						//Если не последний элемент массива , то добавляем перенос строки
						if (str_replace(',', '.', end($results_tz[$key - 1][$k][$key_probe])) != $actualValue) {
						$strActualValue .= '
';
						}
						
					}
					
					//Находим Прочность на сжатие, округляем до десятых и оставляем 1 цифру после запятой
					$compressiveStrength = !empty($sumActualInitValue) ? number_format(round(($sumActualInitValue / 8), 1), 1) : 0;
					
					//Sm
					$sM = 0;
					
					if (!empty($valueGivenСoinCoeffValArray)) {
						$averageValueAndValueGivenСoinCoeffVal = 0;
						
						//Перебираем Значение с учетом коэффициента сопадения
						foreach($valueGivenСoinCoeffValArray as $keyGivenСoinCoeffVal => $valueGivenСoinCoeffVal) {
							//Производим расчеты со средним значением и Значением с учетом коэффициента совпадения
							//pow(..., 2) - возводим в степень
							$averageValueAndValueGivenСoinCoeffVal += pow(($valueGivenСoinCoeffVal - $compressiveStrength), 2); 
							
						}
						
						//Расчеты со средним значением и Значением с учетом коэффициента совпадения / 7
						$averageValueAndValueGivenСoinCoeffVal7 = $averageValueAndValueGivenСoinCoeffVal / 7;
						
						//Sm
						$sM = sqrt($averageValueAndValueGivenСoinCoeffVal7);
						
					}
					
					//Коэффициент вариации
					//$vM = ($sM * 100) / $compressiveStrength;
					$vM = number_format((($sM * 100) / $compressiveStrength), 2);
					
					//Находим Фактический класс бетона, округляем до десятых и оставляем 1 цифру после запятой 
					$actualClassConcrete = !empty($compressiveStrength) ? number_format(round(($compressiveStrength * 0.8), 1), 1) : 0;
				
					
					//Фактический класс бетона Нормативное значение 
					$concreteClassNormativeValue = !empty($ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE']) ? $ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE'] : 0;
					
					//Соответствие или не соответствие
					$conformity = (!empty($concreteClassNormativeValue) && !empty($actualClassConcrete) && $actualClassConcrete < $concreteClassNormativeValue) ? 'не соответствует' : 'соответствует';
					//$conformity = (!empty($vM) && $vM > 7.5) ? 'не соответствует' : 'соответствует';
					
				}
				
				//if (!$j)
					$obj = $prod." (".$probe[$key]['sh_number'][$key_probe-1].")";
				//$key_probe - количество проб
				//$k - номер по счету ГОСТа (в Материале)
				//$key - номер по счету Материала ([$key - 1] - начинаются с 0)
				$results[] = [
					'Object' => $obj,//(!$j ? $probe_k." (".$probe[$key]['sh_number'][$key_probe-1].")" : '______________'),//$probe[$num+1]['number_probe'][$number]. ' (' . $probe[$num+1]['sh_number'][$number] .')',//$number_probes[$i] . ' (' . $numbers[$i] .')',
					'Specification' => $res['SPECIFICATION'],
					'SpecificationTU' => $res_TU['SPECIFICATION'], //Определяемая характеристика
					'Ed' => $res['ED'], //Единицы измерения 
					'EdIndex' => ($res['ED_INDEX'] ? $res['ED_INDEX'] : ''),
					'Normdoc' => $res_TU['GOST']. ' ' . $res_TU['GOST_PUNKT'],//Нормативная (проектная) документация(раздел, пункт)//.' '.$res_TU['SPECIFICATION'],
					'Normval' => ($res_TU['GOST_TYPE']=='TU_research') ? $norm_description : $res_TU['NORM_COMMENT'],//$GOST_VAL[$i],
					'Normdocmet' => ($res['GOST']) ? $res['GOST'].' '.$res['GOST_PUNKT'] : '-', //ГОСТ наименование (Пример: ГОСТ 17.5.4.02 п.4.1)
					'Normdoc_desc' => '',
					'ValuesRi' => $valueAfterCalibrDepArr,
					'ValueRm' => number_format($factAverageStrength, 1, ',', ''),
					'ConcreteFactClass' => $factConcreteClassString,
					'ConcreteDate' => date("d.m.Y", strtotime($probe[$key]['concreteDate'][$key_probe-1])),
					'Construction' => $probe[$key]['construction'][$key_probe-1],
					'Axis' => $probe[$key]['axis'][$key_probe-1],
					'Point' => $probe[$key]['point'][$key_probe-1],
					'ConcreteClass' => $probe[$key]['concreteClass'][$key_probe-1],
					'ConcreteAge' => $concrAge,
					'ResearchDate' => (string)$date2==(string)$date1 ? $date1 : $date1.'-'.$date2,
					'Procent' => $procentConcreteClass,
					'Num' => $num,
					'Values' => $results_tz[$key - 1][$k][$key_probe],
					'Value' => $SRED[$key - 1][$k][$key_probe],
					//'Result' => $ress,
					'Result' => '-',
					'Line' => '',
					
					//Фактическое единичное значение характеристики (по участкам конструкции)
					//'ActualValue' => !empty($strActualValue) ? $strActualValue : 0,
					'ActualValue' => !empty($strActualValue) ? str_replace('.', ',', $strActualValue) : 0,
					
					//Фактическое значение характеристики конструкции //Прочность на сжатие
					//'actualValueDesignCharacteristic' => !empty($compressiveStrength) ? $compressiveStrength : 0,
					'actualValueDesignCharacteristic' => !empty($compressiveStrength) ? str_replace('.', ',', $compressiveStrength) : 0,
					
					//Прочность на сжатие
					//'CompressiveStrength' => !empty($compressiveStrength) ? $compressiveStrength : 0,
							
					//Фактический класс бетона
					//'ActualClassConcrete' => !empty($actualClassConcrete) ? $actualClassConcrete : 0,
					
					//Фактический класс бетона Нормативное значение 
					//'ConcreteClassNormativeValue' => '-',
					'ConcreteClassNormativeValue' => 'Не нормируется',
					
					//Коэффициент вариации
					//'Vm' => !empty($vM) ? '(Кв=' . $vM . ')' : 0,
				];
				
				if (!empty($item) && $item == '608') {
					$results[] = [
						'Normdocmet' => 'ГОСТ 18105 п. 8.4.4', //ГОСТ, Наименование 
						//'Specification' => 'Фактический класс бетона ', //Определяемая характеристика
						'Specification' => 'Класс бетона по прочности на сжатие', //Определяемая характеристика
						'Ed' => 'МПа', //Единицы измерения 
						//'Normdoc' => 'Проект производства работ',
						'Normdoc' => 'Рабочая документация',
						'ActualValue' => '-',
						//Фактический класс бетона
						'ActualClassConcrete' => !empty($actualClassConcrete) ? $actualClassConcrete : 0,
						//Фактическое значение характеристики конструкции //Фактический класс бетона
						//'actualValueDesignCharacteristic' => !empty($actualClassConcrete) ? 'B' . $actualClassConcrete : 0,
						'actualValueDesignCharacteristic' => !empty($actualClassConcrete) ? 'B' . str_replace('.', ',', $actualClassConcrete) : 0,
						'Result' => $conformity,
						'Line' => '_____________________',
						//Фактический класс бетона Нормативное значение 
						//'ConcreteClassNormativeValue' => !empty($ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE']) ? 'B' . $ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE'] : 0,
						'ConcreteClassNormativeValue' => !empty($ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE']) ? 'B' . str_replace('.', ',', $ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE']) : 0,
						//'Vm' => '',
					];
				}
				
			} $j++;
	
		} $num++;
		
	}
}
//dump($results);


foreach ($results_tz as $num => $tz_resaults) {
	
    foreach ($tz_resaults as $number => $tz_resault) {
		$z=".";
		$z_pos=0;
		//var_dump($tz_resaults);
        foreach ($tz_resault as $kk => $nu) {
		//var_dump($nu) ;	
			// if($MATCH[$i]) 
			// {
            $ress = $arr_soot[$MATCH_MANUAL[$num][$number][$kk]];
			
			//$GOST_VAL[$i] = ($NORM2[$i]!='' ? $NORM1[$i].', '.$NORM2[$i] : $NORM1[$i]);
			// } 
			// else
			// {
			 
			 // if (($SRED[$num][$number][$kk] >= $NORM1[$i]) && ($SRED[$num][$number][$kk] <= $NORM2[$i])) $ress = 'Соответствует';
			 // else $ress = 'Не соответствует';  
			 
			// }
			for($j=0; $j<count($nu); $j++){
			$z_pos=strpos($nu[$j],$z);
			if ($z_pos) $nu[$j] = substr_replace($nu[$j], ",", $z_pos, 1);
			}
			$z_pos2 = strpos($SRED[$num][$number][$kk],$z);
			//echo $SRED[$num][$number][$kk].'-----';
			
			//if ($z_pos2) $SRED[$num][$number][$kk] = substr_replace($SRED[$num][$number][$kk], ",", $z_pos2, 1);
			$SRED[$num][$number][$kk] = number_format($SRED[$num][$number][$kk], $accuracy[$i], ',', '');
			//echo $accuracy[$i].'-----';
		//echo $tz[$k]['gost'][$i].'---';
			// if ($tz[1]['gost'][$i]) {
		
			// $res2 = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $tz[1]['gost'][$i])->Fetch();	//если исследуем зерновой состав - не выводим строку в таблице 3 протокола
		
			// if ($res2['GOST_TYPE']!='TU_zern'&&$res2['GOST_TYPE']!='TU_grunt'&&$res2['GOST_TYPE']!='TU_prirod'){
			if($nu[0]!="") {
            // $results[] = [
                // 'Object' => $number_probes[$num + $kk - 1] . ' (' . $numbers[$num + $kk - 1] .')',
                // 'Specification' => $SPECIFICATION[$i],
				// 'SpecificationTU' => $SPECIFICATION_TU[$i],
                // 'Ed' => $ED[$i],
				// 'EdIndex' => ($ED_INDEX[$i] ? $ED_INDEX[$i] : ''),
                // 'Normdoc' => $GOST_DOC[$i].' '.$GOST_PUNKT_TU[$i],
                // 'Normval' => $GOST_DOC_DESC[$i],//$GOST_VAL[$i],
                // 'Normdocmet' => $GOST2[$i].' '.$GOST_PUNKT[$i],
				// 'Normdoc_desc' => $GOST_DOC_DESC[$i],
				// 'Values' => $nu,
                // 'Value' => (($GOST_TYPE[$i]=="TU_sred"||$GOST_TYPE[$i]=="TU_sred2"||$GOST_TYPE[$i]=="TU_sred3"||$GOST_TYPE[$i]=="TU_sred4") ? $SRED[$num][$number][$kk] : "-"),
                // 'Result' => $ress
            // ];
			 }
			// }
		

		}
$i++;
    }
	//$k++;
}
//echo "<pre>";
//dump($probe);

//$l=0;
foreach ($probe[1]['number_probe'] as $pn => $probe_n)
{
	//var_dump($tz);
	
	for($l=0; $l<count($tz[1]['gost']); $l++) {
	//echo $tz[1]['gost'][$l];
	if ($tz[1]['gost'][$l]) {
		
	$res1 = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $tz[1]['gost'][$l])->Fetch();
	$res_group_mat = $DB->Query("SELECT `NORM1`, `NORM2` FROM `ZERN` WHERE `TYPE_MAT_ID`='" . $ba_tz['GROUP_MAT'] . "'")->Fetch(); 
	$res_n1 = unserialize($res_group_mat['NORM1']);
	$res_n2 = unserialize($res_group_mat['NORM2']);
	$prod_arr = explode(', ID',$probe_n);
			$prod = $prod_arr[0];
	
	if ($res1['GOST_TYPE']=='TU_zern'||$res1['GOST_TYPE']=='TU_prirod'||$res1['GOST_TYPE']=='TU_12801') {
		
	$resultszern[] = [
		'Object' => $prod . ' (' . $probe[1]['sh_number'][0] .')',
		'Ost25ch' => number_format($OSTATKI[0], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
		'Ost125ch' => number_format($OSTATKI[1], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
		'Ost063ch' => number_format($OSTATKI[2], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
		'Ost0315ch' => number_format($OSTATKI[3], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
		'Ost016ch' => number_format($OSTATKI[4], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
		'Ost016mch' => number_format($OSTATKI[5], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
		'Ost25p' => number_format($OSTATKI[6], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
		'Ost125p' => number_format($OSTATKI[7], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
		'Ost063p' => number_format($OSTATKI[8], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
		'Ost0315p' => number_format($OSTATKI[9], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
		'Ost016p' => number_format($OSTATKI[10], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
		'Ost016mp' => number_format($OSTATKI[11], ($res1['GOST_TYPE']=='TU_12801' ? 1 : 2), ',', ''),
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
		$l=10000; //зерновой состав формируем 1 раз!!!
	}
	elseif ($res1['GOST_TYPE']=='TU_grunt') {
		$resultszern[] = [
		'Object' => $prod . ' (' . $probe[1]['sh_number'][0] .')',
	
		'Ost10ch' => number_format($OSTATKI[0], 1, ',', ''),
		'Ost5ch' => number_format($OSTATKI[1], 1, ',', ''),
		'Ost2ch' => number_format($OSTATKI[2], 1, ',', ''),
		'Ost1ch' => number_format($OSTATKI[3], 1, ',', ''),
		'Ost05ch' => number_format($OSTATKI[4], 1, ',', ''),
		'Ost025ch' => number_format($OSTATKI[5], 1, ',', ''),
		'Ost01ch' => number_format($OSTATKI[6], 1, ',', ''),
		'Ost005ch' => number_format($OSTATKI[7], 1, ',', ''),
		'Ost005mch' => number_format($OSTATKI[8], 1, ',', ''),
		'Ost10p' => number_format($OSTATKI[9], 1, ',', ''),
		'Ost5p' => number_format($OSTATKI[10], 1, ',', ''),
		'Ost2p' => number_format($OSTATKI[11], 1, ',', ''),
		'Ost1p' => number_format($OSTATKI[12], 1, ',', ''),
		'Ost05p' => number_format($OSTATKI[13], 1, ',', ''),
		'Ost025p' => number_format($OSTATKI[14], 1, ',', ''),
		'Ost01p' => number_format($OSTATKI[15], 1, ',', ''),
		'Ost005p' => number_format($OSTATKI[16], 1, ',', ''),
		'Ost005mp' => number_format($OSTATKI[17], 1, ',', '')
		];
		$l=10000;
		}
	elseif ($res1['GOST_TYPE']=='TU_183') {
		$resultszern[] = [
		'Object' => $prod . ' (' . $probe[1]['sh_number'][0] .')',
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
	/*elseif ($res1['GOST_TYPE']=='TU_shps') {
		$resultszern[] = [
		'Object' => $prod . ' (' . $probe[1]['sh_number'][0] .')',
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
		}*/
	elseif ($res1['GOST_TYPE']=='TU_shps') {
			$sita = [];
			foreach($res_n1 as $key => $norm)
			{
				if ($norm!="")
					$sita[] = $key;
			}
		
			$shebenArr = [  //массив соответствия индекса  сита в зерновом составе и значения для фракций щебня
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
			
			
			$resultszern[] = [
			'Object' => $prod . ' (' . $probe[1]['sh_number'][0] .')',
			'NameMat' => $res_group_mat['NAME'],
			'125D' => $sitaName[0],
			'D' => $sitaName[1],
			'05D_d' => $sitaName[2],
			'd' =>  $sitaName[3],
			'Ost125D' => number_format($OSTATKI[0], 2, ',', ''),
			'OstD' => number_format($OSTATKI[1], 2, ',', ''),
			'Ost05D_d' => number_format($OSTATKI[2], 2, ',', ''),
			'Ostd' => number_format($OSTATKI[3], 2, ',', ''),
			'Ostmd' => number_format($OSTATKI[4], 2, ',', ''),
			'POst125D' => number_format($OSTATKI[5], 2, ',', ''),
			'POstD' => number_format($OSTATKI[6], 2, ',', ''),
			'POst05D_d' => number_format($OSTATKI[7], 2, ',', ''),
			'POstd' => number_format($OSTATKI[8], 2, ',', ''),
			'POstmd' => number_format($OSTATKI[9], 2, ',', ''),
			'Norm125D' => $res_n1[$sitaNorm[0]].(($res_n2[$sitaNorm[0]]&&($res_n2[$sitaNorm[0]]!=$res_n1[$sitaNorm[0]])) ? '-'.$res_n2[$sitaNorm[0]] : ''),
			'NormD' => $res_n1[$sitaNorm[1]].(($res_n2[$sitaNorm[1]]&&($res_n2[$sitaNorm[1]]!=$res_n1[$sitaNorm[1]])) ? '-'.$res_n2[$sitaNorm[1]] : ''),
			'Norm05D_d' => $res_n1[$sitaNorm[2]].(($res_n2[$sitaNorm[2]]&&($res_n2[$sitaNorm[2]]!=$res_n1[$sitaNorm[2]])) ? '-'.$res_n2[$sitaNorm[2]] : ''),
			'Normd' => $res_n1[$sitaNorm[3]].(($res_n2[$sitaNorm[3]]&&($res_n2[$sitaNorm[3]]!=$res_n1[$sitaNorm[3]])) ? '-'.$res_n2[$sitaNorm[3]] : '')
			
			
			];
			dump($resultszern);
			$l=10000;
			
		}
	//$l++;
	}
	}
}
//var_dump($resultszern);
//echo $res1['GOST_TYPE'];

$number_probe = (count($number_probes)>1 ? count($number_probes) . " " . num2word(count($number_probes), ['проба', 'пробы/образца', 'проб/образцов']) . ": " : ""); //. implode(", ", $number_probes);
for($i=0; $i<count($number_probes); $i++)
{
	$probesValue = explode(', ID', $number_probes[$i]);
	$number_probe = $number_probe . $probesValue[0] . " (" . $numbers[$i] . ")" . ((count($number_probes)-(int)$i)==1 ? "." : ", ");
	//echo $numbers[$i]."__";
}

//$number = implode(", ", $numbers);

$j=0;
$GOST_DOC3 = [];
for ($i=0; $i<count($GOST2); $i++) {
  //$strGOST=strpos($GOST2[$i], "п"); //отрезаем пункты гостов для протокола
  //$GOST2[$i]=substr($GOST2[$i], 0, $strGOST);
  $years='';
  if($YEARS[$i]) $years='-'.$YEARS[$i];
  $years2='';
  if($YEARS2[$i]) $years2='-'.$YEARS2[$i];
  if($GOST2[$i]!='-'&&$GOST_TYPE[$i]!='metodic_otbor')
  $SPECIFICATION_GOST[$i] = $GOST2[$i].$years.' '.$GOST_DOC_DESC_MET[$i];
  //echo $i.'='.$GOST_DOC[$i].'  -----';
if($GOST_DOC[$i]=='--'||$GOST_DOC[$i]==''||$UNNORM[$i]==1) { }
  else {
  $GOST_DOC3[$j] = $GOST_DOC[$i].$years2.' '.$GOST_DOC_DESC_TU[$i];
  $j++;
  }
}

$SPECIFICATION = implode(", ", array_unique($SPECIFICATION));
$SPECIFICATION_GOST = implode(", ", array_unique($SPECIFICATION_GOST));
//$METOD_OTBOR=

if (!$ba_tz['NO_COMPLIANCE']) 
{ 
 $GOST_DOC = implode(", ", array_unique($GOST_DOC3));
// echo $GOST_DOC;
 $GOST_DOC = 'и оценить на соответствие требованиям '.$GOST_DOC;
}
else
 $GOST_DOC='';

$GOST = implode(", ", array_unique($GOST_DOC_PLUS_DESC));

for ($i=0; $i<count($results); $i++) {
 $results[$i]['Values'] = implode("                             ", $results[$i]['Values']);	
 $results[$i]['ValuesRi'] = implode("                             ", $results[$i]['ValuesRi']);
}

$req = new \Bitrix\Crm\EntityRequisite();
$rs = $req->getList([
    "filter" => [
        "ENTITY_ID" => $deal['COMPANY_ID'],
    ]
]);
$requis = $rs->fetch();

// $addr = new \Bitrix\Crm\RequisiteAddress();
// $ad = $addr->getList([
    // "filter" => [
        // "ENTITY_ID" => $deal['COMPANY_ID'],
    // ]
// ]);
// $address = $ad->fetch();
$assigned = CUser::GetByID($USER->GetID())->arResult[0];
$assigned_user = $assigned['NAME'].' '.$assigned['LAST_NAME'];
$ID_P = $ba_tz['NUM_P'] ? $ba_tz['NUM_P'] : (int)$ID+485;
$dogovorNumber = explode(', ID', $ba_tz['DOGOVOR_NUM']);
$dogovor_num = ($dogovorNumber[0] ? $dogovorNumber[0] : '-');
if($ba_tz['NO_CONTRACT']) $dogovor_num = "Акт приемки проб №".$ba_tz['ACT_NUM'].$y_date_act." от ".ruDate($ba_tz['DATE_ACT']);

$res = $DB->Query("UPDATE `ba_tz` SET `DATE_P` ='" . date("Y-m-d H:i:s") . "' WHERE ID = " . (int)$TZ_ID);
//$res_history = $DB->Query("UPDATE `HISTORY` SET `DATE` ='" . date("Y-m-d H:i:s") . "', `TYPE`='1', `` WHERE ID = " . (int)$TZ_ID);
$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`) VALUES ('" . date('Y-m-d H:i:s') . "','" . $assigned_user . "','" . $ID_P . "','" . $TZ_ID . "','" . $USER->GetID() . "', 'Скачивание протокола')");

if($metod_otbor) {
	$res_otbor = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=".$metod_otbor)->Fetch();
	$metod_otbor2 = $res_otbor['GOST']. " " . $res_otbor['SPECIFICATION'];
	}
        // if ($res->result = 1) {
            // echo "<h3>Успешно</h3>";
        // } else {
            // echo "<h3>Ошибка</h3>";
        // }
$point = $ba_tz['SELECTION_TYPE'] ? "." : ",";
$selectionType = $ba_tz['SELECTION_TYPE'] ? '' : '– пробы отобраны и доставлены в ИЦ заказчиком.';

//echo'<pre>';var_dump('$results',$results);echo'</pre>';die();
//echo'<pre>';var_dump('$$ba_tz[CONCRETE_CLASS_NORMATIVE_VALUE]',$ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE']);echo'</pre>';die(); 

//Заполняем данные
$document->normalizeContent();

//$document->setFields(['PNG' => ['TYPE' => 'IMAGE'],]);
$document->setValues([
    'nProtocol' => $ID_P.$y_date_p,
	'A' => (!$ba_tz['ATTESTAT'] ? '' : 'A'),
	//'Attestat' => '',//(!$ba_tz['ATTESTAT'] ? '' : ' Аттестат аккредитации RA.RU.21ЧЦ49'),
	//'Attestat2' => '',//(!$ba_tz['ATTESTAT'] ? '' : 'Дата внесения в реестр аккредитованных лиц 19 октября 2017'),
    'dProtocol' => ($ba_tz['DATE_P_CORR'] ? date("d.m.Y", strtotime($ba_tz['DATE_P_CORR'])) : date("d.m.Y")),
    'nDogovor' => $dogovor_num,
    'dDorovor' => '',
    'nZakazchik' => $deal['COMPANY_TITLE'],
    'innZakazchik' => $requis['RQ_INN'],
    'ogrnZakazchik' => $requis['RQ_OGRN'],
    'aZakazchik' => $requis['RQ_ACCOUNTANT'],
    'dProbe' => ($ba_tz['DATE_ACT'] ? date("d.m.Y", strtotime($ba_tz['DATE_ACT'])) : end($probe)['dat']),
    'mestoSboraProbe' => (string)end($probe)['mesto_data'],
    'oStroit' => $ba_tz['OBJECT'],
    'oIspit' => $number_probe,
    /*'nProbe' => $number,*/
	'nProbe_reg' => $ba_tz['ACT_NUM'].$y_date_act,//$y_date_act=='/2020' ? substr($numbers[0],0,-7).$y_date_act : substr($numbers[0],0,-5).$y_date_act ,
    //'celIspit' => $SPECIFICATION,
    'metodIspit' => $SPECIFICATION_GOST,
	'normDoc' => $GOST_DOC,
    //'mIspit' => $lab,
    'd1Ispit' => $date1,
    'd2Ispit' => ((string)$date2==(string)$date1 ? '' : '-'.(string)$date2),
    'tIspit' => $ba_tz['TEMP_O'],
    'vIspit' => $ba_tz['VLAG_O'],
	//'workPos' => $workpos,
	//'dopInfo' => $dopinfo,
	'Description' => $description,
    //'Ispolnitel' => $ispolnitel,
	/*'metodOtbor' => $metod_otbor2,
	'placeIspit' => $probe['mesto_data'],*/
	'ISP' => (count($verify) > 1 ? 'Исполнители' : 'Исполнитель'),
	/*'Point' => $point,
	'OtborProb' => $selectionType,*/
	
		
	'Verify' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $verify_arr,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'VerifyItemWorkPos' => 'Verify.Item.WorkPos',
	'VerifyItemIspolnitel' => 'Verify.Item.Ispolnitel',
	'VerifyItemIC' => 'Verify.Item.IC',
   
    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $oborud,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'TableItemName' => 'Table.Item.Name',
    //'TableItemNum' => 'Table.Item.Num',
    //'TableItemYear' => 'Table.Item.Year',
    'TableItemPoverka' => 'Table.Item.Poverka',
	/*'TableItemRegnum' => 'Table.Item.Regnum',
    
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
	'ResultszernItem125D' => 'Resultszern.Item.125D',
	'ResultszernItem05D_d' => 'Resultszern.Item.05D_d',
	'ResultszernItemmd' => 'Resultszern.Item.md',
	'ResultszernItemd_2' => 'Resultszern.Item.d_2',
	'ResultszernItemOst2D' => 'Resultszern.Item.Ost2D',
	'ResultszernItemOst14D' => 'Resultszern.Item.Ost14D',
	'ResultszernItemOstD' => 'Resultszern.Item.OstD',
	'ResultszernItemOstD14' => 'Resultszern.Item.OstD14',
	'ResultszernItemOstd' => 'Resultszern.Item.Ostd',
	'ResultszernItemOstd_2' => 'Resultszern.Item.Ostd_2',
	'ResultszernItemOst125D' => 'Resultszern.Item.Ost125D',
	'ResultszernItemOst05D_d' => 'Resultszern.Item.Ost05D_d',
	'ResultszernItemOstd' => 'Resultszern.Item.Ostd',
	'ResultszernItemOstmd' => 'Resultszern.Item.Ostmd',
	
	'ResultszernItemPOst125D' => 'Resultszern.Item.POst125D',
	'ResultszernItemPOstD' => 'Resultszern.Item.POstD',
	'ResultszernItemPOst05D_d' => 'Resultszern.Item.POst05D_d',
	'ResultszernItemPOstd' => 'Resultszern.Item.POstd',
	'ResultszernItemPOstmd' => 'Resultszern.Item.POstmd',
	
	
	'ResultszernItemOst200p' => 'Resultszern.Item.Ost200p',
	'ResultszernItemOst200ch' => 'Resultszern.Item.Ost200ch',
	'ResultszernItemOst120p' => 'Resultszern.Item.Ost120p',
	'ResultszernItemOst120ch' => 'Resultszern.Item.Ost120ch',
	'ResultszernItemOst80p' => 'Resultszern.Item.Ost80p',
	'ResultszernItemOst80ch' => 'Resultszern.Item.Ost80ch',
	'ResultszernItemOst40p' => 'Resultszern.Item.Ost40p',
	'ResultszernItemOst40ch' => 'Resultszern.Item.Ost40ch',
	'ResultszernItemOst20p' => 'Resultszern.Item.Ost20p',
	'ResultszernItemOst20ch' => 'Resultszern.Item.Ost20ch',
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
	'ResultszernItemOst10p' => 'Resultszern.Item.Ost10p',
	'ResultszernItemOst5p' => 'Resultszern.Item.Ost5p',
	'ResultszernItemOst2p' => 'Resultszern.Item.Ost2p',
	'ResultszernItemOst1p' => 'Resultszern.Item.Ost1p',
	'ResultszernItemOst05p' => 'Resultszern.Item.Ost05p',
	'ResultszernItemOst025p' => 'Resultszern.Item.Ost025p',
	'ResultszernItemOst01p' => 'Resultszern.Item.Ost01p',
	'ResultszernItemOst005p' => 'Resultszern.Item.Ost005p',
	'ResultszernItemOst005mp' => 'Resultszern.Item.Ost005mp',
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
	'ResultszernItemNorm125D' => 'Resultszern.Item.Norm125D',
	'ResultszernItemNorm05D_d' => 'Resultszern.Item.Norm05D_d',*/
	
	

    'Results' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'ResultsItemObject' => 'Results.Item.Object',
    'ResultsItemSpecification' => 'Results.Item.Specification',
	/*'ResultsItemSpecificationTU' => 'Results.Item.SpecificationTU',*/
    'ResultsItemEd' => 'Results.Item.Ed', //Единицы измерения
	/*'ResultsItemEdIndex' => 'Results.Item.EdIndex',*/
    'ResultsItemNormdoc' => 'Results.Item.Normdoc',
    /*'ResultsItemNormval' => 'Results.Item.Normval',*/
    'ResultsItemNormdocmet' => 'Results.Item.Normdocmet',
	/*'ResultsItemValues' => 'Results.Item.Values',
	'ResultsItemValuesRi' => 'Results.Item.ValuesRi',
    'ResultsItemValue' => 'Results.Item.Value',
	'ResultsItemValueRm' => 'Results.Item.ValueRm',
	'ResultsItemConcreteFactClass' => 'Results.Item.ConcreteFactClass',
	'ResultsItemProcent' => 'Results.Item.Procent',*/
    'ResultsItemResult' => 'Results.Item.Result',
	
	/*'ResultsItemConcreteDate' => 'Results.Item.ConcreteDate',
	'ResultsItemConstruction' => 'Results.Item.Construction',
    'ResultsItemAxis' => 'Results.Item.Axis',
	'ResultsItemPoint' => 'Results.Item.Point',
	'ResultsItemConcreteClass' => 'Results.Item.ConcreteClass',
    'ResultsItemResearchDate' => 'Results.Item.ResearchDate',
	'ResultsItemConcreteAge' => 'Results.Item.ConcreteAge',
	'ResultsItemNum' => 'Results.Item.Num',*/
	
	/*
	'Normdocmet' => 'ГОСТ 18105 п. 8.4.4', //ГОСТ, Наименование 
						'ActualValue' => '-',
						//Фактический класс бетона
						'ActualClassConcrete' => !empty($actualClassConcrete) ? $actualClassConcrete : 0,
						//Фактическое значение характеристики конструкции //Фактический класс бетона
						$actualValueDesignCharacteristic => !empty($actualClassConcrete) ? $actualClassConcrete : 0,
						//Фактический класс бетона Нормативное значение 
						'ConcreteClassNormativeValue' => !empty($ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE']) 
	*/
	
	//Фактическое единичное значение характеристики (по участкам конструкции)
	'ActualValue' => 'Results.Item.ActualValue',
	
	//Прочность на сжатие
	//Фактический класс бетона
	'actualValueDesignCharacteristic' => 'Results.Item.actualValueDesignCharacteristic',
	
	//Линия разграничения колонок
	'ResultsItemLine' => 'Results.Item.Line',
	
	//Фактическое единичное значение характеристики (по участкам конструкции)
	//'ActualValue0' => !empty($arrayActualValue[0]) ? $arrayActualValue[0] : 0,
	//'ActualValue1' => !empty($arrayActualValue[1]) ? $arrayActualValue[1] : 0,
	//'ActualValue2' => !empty($arrayActualValue[2]) ? $arrayActualValue[2] : 0,
	//'ActualValue3' => !empty($arrayActualValue[3]) ? $arrayActualValue[3] : 0,
	//'ActualValue4' => !empty($arrayActualValue[4]) ? $arrayActualValue[4] : 0,
	//'ActualValue5' => !empty($arrayActualValue[5]) ? $arrayActualValue[5] : 0,
	//'ActualValue6' => !empty($arrayActualValue[6]) ? $arrayActualValue[6] : 0,
	//'ActualValue7' => !empty($arrayActualValue[7]) ? $arrayActualValue[7] : 0,
	
	//Прочность на сжатие
	//'CompressiveStrength' => !empty($compressiveStrength) ? $compressiveStrength : 0,
			
	//Фактический класс бетона
	//'ActualClassConcrete' => !empty($actualClassConcrete) ? $actualClassConcrete : 0,
	
	//Фактический класс бетона Нормативное значение 
	//'ConcreteClassNormativeValue' => !empty($ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE']) ? $ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE'] : 0
	'ConcreteClassNormativeValue' => 'Results.Item.ConcreteClassNormativeValue',
	
	//Коэффициент вариации
	//'ResultsItemVm' => 'Results.Item.Vm', 
	
	//Фактический класс бетона Нормативное значение (Для записи в примечание один раз)
	'ConcreteClassNormative' => !empty($ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE']) ? 'B' . $ba_tz['CONCRETE_CLASS_NORMATIVE_VALUE'] : 0,
	
	//Коэффициент совпадения
	'coinCoeffVall' => !empty($coinCoeffVal) ? $coinCoeffVal : 0,
	
	'Prochn' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $prochn,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'ProchnItemProchPM' => 'Prochn.Item.prochnPM',
    'ProchnItemProchKM' => 'Prochn.Item.prochnKM',
    'ProchnItemProchName' => 'Prochn.Item.prochnName',
    'ProchnItemProchChast' => 'Prochn.Item.prochnChast'
	 
]

);

/*
/Собираем массив значений прочности Прямым методом и Косвенным методом
$prochn = [];
if (isset($prochnPM)) {
	foreach($prochnPM as $keyProchnPM => $valProchnPM) {
		$prochn[] = [
			'prochnPM' => isset($valProchnPM) ? $valProchnPM : 0,
			'prochnKM' => isset($prochnKM[$keyProchnPM]) ? $prochnKM[$keyProchnPM] : 0,
			'prochnName' => isset($prochnName[$keyProchnPM]) ? $prochnName[$keyProchnPM] : 0,
			'prochnChast' => isset($prochnChast[$keyProchnPM]) ? $prochnChast[$keyProchnPM] : 0
		];
	}
}
*/

$result = $document->process();
$content = $document->getContent();


if(($oborud_check&&$key_check==FALSE&&$date_check)||($USER->GetID()=='61')||($USER->GetID()=='1')||($USER->GetID()=='9')){  //&&$temp_in_ob_check&&$vlag_in_ob_check&&$temp_in_gost_check&&$vlag_in_gost_check)

$newpath = "archive/".$TZ_ID.date("Y", strtotime($ba_tz['DATE_ACT']))."/".$curdate.".docx";

$newpath2 = "archive/".$TZ_ID.date("Y", strtotime($ba_tz['DATE_ACT']))."/forsign.docx";
$interimPath = "interim_archive_prot/" . $TZ_ID.date("Y", strtotime($ba_tz['DATE_ACT'])) . "/" . $curdateEng.".docx";

$outputFile = "Протокол №" . $ID_P . " от ".date("d.m.Y", strtotime($date_protocol)).".pdf";

$outputPath = "archive/" . $TZ_ID.date("Y", strtotime($ba_tz['DATE_ACT'])) . "/";

$res_av = $DB->Query("UPDATE `ba_tz` SET `ACTUAL_VER`= '".$curdate."' WHERE `ID`=" . $TZ_ID);

function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );
   
    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );
   
    file_put_contents( $fullPath, $contents, $flags );
}

file_force_contents($newpath, $content);

if (in_array($type_tz, [33, 34, 35, 36, 37, 38, 39]) && !empty($ba_tz['NUM_P']) && is_numeric($ba_tz['NUM_P'])) {
    file_force_contents($newpath2, $content);
}

$pathDocCurdate = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $newpath;

if (!in_array($type_tz, [33, 34, 35, 36, 37, 38, 39]) && !empty($ba_tz['NUM_P']) && is_numeric($ba_tz['NUM_P'])) {

    $qrPath = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/archive/' . $TZ_ID . date("Y", strtotime($ba_tz['DATE_ACT'])) . '/qrNEW.png';

    QRcode::png("https://niistrom.pro/check/index.php?NUMP=" . $ID_P . "&DATE=" . date('d.m.Y', strtotime($date_protocol)), $qrPath);

    addQrCode($qrPath, $pathDocCurdate);

}

if (!in_array($type_tz, [33, 34, 35, 36, 37, 38, 39]) && empty($ba_tz['NO_BITRIX']) && !empty($ba_tz['NUM_P']) && is_numeric($ba_tz['NUM_P'])) {
    file_force_contents($interimPath, $content);

    $pathDocCurdateEng = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $interimPath;

    addQrCode($qrPath, $pathDocCurdateEng);

    $fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$interimPath;
    $fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$outputPath;

    $converter  =  new  OfficeConverter($fullPathDocx, $fullPathPDF);
    $converter -> convertTo($outputFile);
}


// вывод непосредственно в браузер
//echo "ВЕДУТСЯ ТЕХНИЧЕСКИЕ РАБОТЫ! ПОПРОБУЙТЕ СФОРМИРОВАТЬ ПРОТОКОЛ ПОЗЖЕ!";

	header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	header('Content-Disposition: attachment;filename="Протокол № ' . $ID_P . ' от ' . date("d.m.Y") . '.docx"');
	header('Cache-Control: max-age=0');
	//echo $content;
    echo file_get_contents($pathDocCurdate);
}
else echo "<br><br><a href='/results_isp.php?ID=".$ID."'>Вернуться к результатам испытаний</a>";
die;