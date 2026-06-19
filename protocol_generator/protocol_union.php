<?php
/**
 * Created by Makaralex92.
 * User: Алексей
 * Date: 07.05.2019
 * Time: 16:11
 */

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

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
//echo '_______________________'.$type_tz;
//var_dump($probe);
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

if ($type_tz == 0) 
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_nounion.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
} 
elseif ($type_tz == 5)
{
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob_zern.docx');
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
//	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_osk_road_pnst184.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
		
}
elseif($type_tz == 1||$type_tz == 2||$type_tz == 3||$type_tz == 4) 
{
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol2_ob.docx');
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


//Формируем данные

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
$temp_p2 = tofloat($ba_tz['TEMP_TO_O']);
$vlag_p2 = tofloat($ba_tz['VLAG_TO_O']);

 $res_ob = $DB->Query("SELECT * FROM `TZ_OB_CONNECT` WHERE `ID_TZ` = " . $ID);
		if(intval($res_ob->SelectedRowsCount())>0) {
			while($row = $res_ob->Fetch())
									{
										
										$ob = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=".$row['ID_OB'])->Fetch();
										$poverka_time = 5184000;
										$poverka = strtotime($ob['POVERKA']) - time();
										
										$temp_o1 = tofloat($ob['TOO_EX']);
										$temp_o2 = $ob['TOO_EX2'];
										if($temp_p<$temp_o1||$temp_p>$temp_o2) {$temp_in_ob_check=0; echo "Протокол не может быть сформирован! Температура при проведении испытаний не соответствует условиям эксплуатации оборудования "  . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] . "! <br>";}
													
										$vlag_o1 = tofloat($ob['OVV_EX']);
										$vlag_o2 = $ob['OVV_EX2'];
										if($vlag_p<$vlag_o1||$vlag_p>$vlag_o2) {$vlag_in_ob_check=0; echo "Протокол не может быть сформирован! Влажность при проведении испытаний не соответствует условиям эксплуатации оборудования "   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] . "! <br>";}
										//var_dump($ob);
										
										if(!$ob['CHECKED']) {$oborud_check=0; echo "Протокол не может быть сформирован! Оборудование "  . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] .  " не проверено! </br>"; }
										if($poverka<=0) {$oborud_check=0; echo "Протокол не может быть сформирован! Истек срок поверки оборудования "   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] .  "! <br/>"; }
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
										//	'Class' => $ob['сlass_precision_and_accuracy'],
										//    'Predel' => $ob['measuring_range'],
											'Poverka' => $certificateName . ' до ' . date("d.m.Y", strtotime($ob['POVERKA'])),
										];
										
										if (!in_array($ob_arr, $oborud)) {
											$oborud[] = $ob_arr;
										}
										}
			//dump($ob_arr);
										$tz_ob=1;
										
									}
		}
	

foreach ($tz as $items) {
    foreach ($items['gost'] as $k => $item) {
		
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
        $gost = $ba_gost->Fetch();
		
		
		$temp_g = unserialize($gost['TEMP']);
		$vlag_g = unserialize($gost['VLAG']);
		if($temp_p<tofloat($temp_g[0])&&$temp_p>tofloat($temp_g[1])) {$temp_in_gost_check=0; echo "Протокол не может быть сформирован! Температура при проведении испытаний не соответствует условиям в методике " . $gost['GOST']. " " . $gost['SPECIFICATION'] . "! <br>";}
		if($vlag_p<tofloat($vlag_g[0])&&$vlag_p>tofloat($vlag_g[1])) {$vlag_in_gost_check=0; echo "Протокол не может быть сформирован! Влажность при проведении испытаний не соответствует условиям в методике " . $gost['GOST']. " " . $gost['SPECIFICATION'] . "! <br>";}
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
			if($temp_p<$temp_o1||$temp_p>$temp_o2) {$temp_in_ob_check=0; echo "Протокол не может быть сформирован! Температура при проведении испытаний не соответствует условиям эксплуатации оборудования "  . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] . "! <br>";}
												
			$vlag_o1 = tofloat($ob['OVV_EX']);
			$vlag_o2 = $ob['OVV_EX2'];
			if($vlag_p<$vlag_o1||$vlag_p>$vlag_o2) {$vlag_in_ob_check=0; echo "Протокол не может быть сформирован! Влажность при проведении испытаний не соответствует условиям эксплуатации оборудования "   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] . "! <br>";}
			
			if(!$ob['CHECKED']) {$oborud_check=0; echo "Протокол не может быть сформирован! Оборудование "   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] .  " не проверено! </br>"; }
			if($poverka<=0) {$oborud_check=0; echo "Протокол не может быть сформирован! Истек срок поверки оборудования "   . $ob['OBJECT'] . " " . $ob['TYPE_OBORUD']. " " . $ob['REG_NUM'] .  "! <br/>"; }
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
if(!count($oborud)) {$oborud_check=0; echo "Протокол не может быть сформирован! Не указано используемое оборудование! "; }
$key_check = array_search("0", $CHECKED); 
 // var_dump ($CHECKED);
//  echo $tz_ob;
 // var_dump ($GOST2);
if($key_check) {echo "Протокол не может быть сформирован! Используемая методика ".$GOST2[$key_check-1].' '.$GOST_PUNKT[$key_check-1]." не проверена отделом метрологии! ";}

$date_check=1;



if((right_date($date_result)>right_date($ba_tz['DATE_P_CORR']))&&$ba_tz['NUM_P']&&$ba_tz['NUM_P']!='номер не присвоен') {
	$date_check=0;
	echo "<p style='color:red'>Протокол не может быть сформирован! Указанная дата протокола меньше даты формирования результатов испытаний!</p><br>";
}

//echo right_date($ba_tz['DATE_P_CORR']);
//$k=1;
// var_dump($results_tz);
// echo "________________________________";
// var_dump($number_probes);

foreach ($tz as $key => $arrDatum) {
	foreach($probe[$key]['number_probe'] as $key_probe => $probe_k){
		$key_probe = $key_probe+1; $j=0; 
		$z="."; $z_pos=0;
		foreach ($arrDatum['gost'] as $k => $item){
		//if($tz_key)
			$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
			$res_TU = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $arrDatum['gost_new'][$k])->Fetch();
			
			$ress = $arr_soot[$MATCH_MANUAL[$key - 1][$k][$key_probe]];
			for($l=0; $l<count($results_tz[$key - 1][$k][$key_probe]); $l++){
				$z_pos=strpos($results_tz[$key - 1][$k][$key_probe][$l],$z);
				if ($z_pos) $results_tz[$key - 1][$k][$key_probe][$l] = substr_replace($results_tz[$key - 1][$k][$key_probe][$l], ",", $z_pos, 1);
			}
			$z_pos2 = strpos($SRED[$key - 1][$k][$key_probe]);
			$SRED[$key - 1][$k][$key_probe] = number_format($SRED[$key - 1][$k][$key_probe], $res['ACCURACY'], ',', '');
			$norm_description = (count($tz) >1 ? $NORM_DESC[$key-1][$key_probe][$k] : $NORM_DESC[$key_probe][$k]);
			$prod_arr = explode(', ID',$probe_k);
			$prod = $prod_arr[0];
			$obj='';
			if($results_tz[$key - 1][$k][$key_probe][0]!=""){
				//if (!$j)
					$obj = $prod." (".$probe[$key]['sh_number'][$key_probe-1].")";
				//elseif($j==count($arrDatum['gost'])-1) $obj="______________";
				$results[] = [
					'Object' => $obj,//(!$j ? $probe_k." (".$probe[$key]['sh_number'][$key_probe-1].")" : '______________'),//$probe[$num+1]['number_probe'][$number]. ' (' . $probe[$num+1]['sh_number'][$number] .')',//$number_probes[$i] . ' (' . $numbers[$i] .')',
					'Specification' => $res['SPECIFICATION'],
					'SpecificationTU' => $res_TU['SPECIFICATION'],
					'Ed' => $res['ED'],
					'EdIndex' => ($res['ED_INDEX'] ? $res['ED_INDEX'] : ''),
					'Normdoc' => $res_TU['GOST']. ' ' . $res_TU['GOST_PUNKT'],//.' '.$res_TU['SPECIFICATION'],
					'Normval' => ($res_TU['GOST_TYPE']=='TU_research') ? $norm_description : $res_TU['NORM_COMMENT'],//$GOST_VAL[$i],
					'Normdocmet' => ($res['GOST']) ? $res['GOST'].' '.$res['GOST_PUNKT'] : '-',
					'Normdoc_desc' => '',
					'Values' => $results_tz[$key - 1][$k][$key_probe],
					'Value' => $SRED[$key - 1][$k][$key_probe],
					'Result' => $ress
				];
				$j++;
			} 
		} 
	}
}
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
	elseif ($res1['GOST_TYPE']=='TU_shps') {
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
		}
	//$l++;
	}
	}
}
//var_dump($resultszern);
echo $res1['GOST_TYPE'];

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
$dogovor_num = ($dogovorNumber[0] ? $dogovorNumber[0] : 'Договор №'.$ID);
if($ba_tz['NO_CONTRACT']) $dogovor_num = "Акт приемки проб №".$ba_tz['ACT_NUM'].$y_date_act." от ".ruDate($ba_tz['DATE_ACT']);

//Номер заявки
$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';

//--'Вставка №Заявки в шаблоны' начало--//
//Номер заявки
$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';

$arr_num_request = !empty($num_request) ? explode(' ', $num_request) : '';

$str_num_request = !empty($arr_num_request[1]) ? ' (Заявка ' . $arr_num_request[1] . ')' : ''; 

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
// //exit;
//var_dump($ba_tz['DOGOVOR_NUM']);
//dump($resultszern);
//echo $company['UF_CRM_1567958673619'];
//Заполняем данные
$document->normalizeContent();
//$document->setFields(['PNG' => ['TYPE' => 'IMAGE'],]);
$document->setValues([
    'nProtocol' => $ID_P.$y_date_p,
	'A' => ($ba_tz['ATTESTAT']&&$ba_tz['IN_ATTESTAT_DIAPASON'] ? 'A' : ''),
	'Attestat' => ($ba_tz['ATTESTAT_IN_PROTOCOL'] ? ' Аттестат аккредитации RA.RU.21ЧЦ49' : ''),
	'Attestat2' =>($ba_tz['ATTESTAT_IN_PROTOCOL'] ? 'Дата внесения в реестр аккредитованных лиц 19 октября 2017' : ''),
    'dProtocol' => ($ba_tz['DATE_P_CORR'] ? date("d.m.Y", strtotime($ba_tz['DATE_P_CORR'])) : date("d.m.Y")),
	'nDogovor' => $dogovor_num . $str_num_request,
    'dDorovor' => ($ba_tz['DOGOVOR_NUM'] ? '' : ' от '.$deal['BEGINDATE']),
    'nZakazchik' => $deal['COMPANY_TITLE'],
    'innZakazchik' => $requis['RQ_INN'],
    'ogrnZakazchik' => $requis['RQ_OGRN'],
    'aZakazchik' => $requis['RQ_ACCOUNTANT'],
    'dProbe' => ($ba_tz['DATE_ACT'] ? date("d.m.Y", strtotime($ba_tz['DATE_ACT'])) : end($probe)['dat']),
    'mestoSboraProbe' => (string)end($probe)['mesto_data'],
    'oStroit' => $ba_tz['OBJECT'],
    'oIspit' => $number_probe,
    'nProbe' => $number,
	'nProbe_reg' => $ba_tz['ACT_NUM'].$y_date_act,//$y_date_act=='/2020' ? substr($numbers[0],0,-7).$y_date_act : substr($numbers[0],0,-5).$y_date_act ,
    'celIspit' => $SPECIFICATION,
    'metodIspit' => $SPECIFICATION_GOST,
	'normDoc' => $GOST_DOC,
    'mIspit' => $lab,
    'd1Ispit' => $date1,
    'd2Ispit' => ((string)$date2==(string)$date1 ? '' : '-'.(string)$date2),
    'tIspit' => $ba_tz['TEMP_O']==$ba_tz['TEMP_TO_O'] ? number_format($ba_tz['TEMP_O'], 1, ',', '')." " : "(".number_format($ba_tz['TEMP_O'], 1, ',', '')." - ".number_format($ba_tz['TEMP_TO_O'], 1, ',', '').") ",
    'vIspit' => $ba_tz['VLAG_O']==$ba_tz['VLAG_TO_O'] ? number_format($ba_tz['VLAG_O'], 1, ',', '') : "(".number_format($ba_tz['VLAG_O'], 1, ',', '')." - ".number_format($ba_tz['VLAG_TO_O'], 1, ',', '').")",
	//'workPos' => $workpos,
	'dopInfo' => $dopinfo,
	'Description' => $description,
    //'Ispolnitel' => $ispolnitel,
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
	'VerifyItemIC' => 'Verify.Item.IC',
   
    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $oborud,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'TableItemName' => 'Table.Item.Name',
   // 'TableItemClass' => 'Table.Item.Class',
   // 'TableItemPredel' => 'Table.Item.Predel',
    'TableItemPoverka' => 'Table.Item.Poverka',
    
	'Resultszern' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $resultszern,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'ResultszernItemObject' => 'Resultszern.Item.Object',
	'ResultszernItemOst25ch' => 'Resultszern.Item.Ost25ch',
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
	
	

    'Results' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'ResultsItemObject' => 'Results.Item.Object',
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
	
	
]

);


$result = $document->process();
$content = $document->getContent();
//$document->save($filename = '1.docx', $storage = null);

if(($oborud_check&&$key_check==FALSE&&($date_check||$USER->GetID()=='58')&&$temp_in_ob_check&&$vlag_in_ob_check&&$temp_in_gost_check&&$vlag_in_gost_check)||($USER->GetID()=='10')||($USER->GetID()=='10')||($USER->GetID()=='9')||($USER->GetID()=='7')){  //&&$temp_in_ob_check&&$vlag_in_ob_check&&$temp_in_gost_check&&$vlag_in_gost_check)

$newpath = "archive/".$TZ_ID.date("Y", strtotime($ba_tz['DATE_ACT']))."/".$curdate.".docx";

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

// вывод непосредственно в браузер
//echo "ВЕДУТСЯ ТЕХНИЧЕСКИЕ РАБОТЫ! ПОПРОБУЙТЕ СФОРМИРОВАТЬ ПРОТОКОЛ ПОЗЖЕ!";

	header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	header('Content-Disposition: attachment;filename="Протокол № ' . $ID_P . ' от ' . date("d.m.Y") . '.docx"');
	header('Cache-Control: max-age=0');
	echo $content; 
}
else echo "<br><br><a href='/results_isp.php?ID=".$ID."'>Вернуться к результатам испытаний</a>";
die;