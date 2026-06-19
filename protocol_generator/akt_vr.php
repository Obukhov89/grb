(<?php
/**
 * Created by Makaralex92.
 * User: Алексей
 * Date: 07.05.2019
 * Time: 16:11
 */

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';

require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DataStorage.php');
/*require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/Rights.php';
if (!Rights::check($USER, basename(__FILE__, '.php'))) {
    $url = $_SERVER['HTTP_REFERER'] ?? 'http://172.16.255.2/requests_test.php';
    header('location:' . $url);
    exit();
}*/
Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
$db = new CDB();
$newResult = new NewResult();

//function dateObjectFormat($obj, $format) {
//    $date = null;
//
//    if (!empty($obj) && is_object($obj) && method_exists($obj,'format')) {
//
//        if (!empty($format)) {
//            $date = $obj->format($format);
//        } else {
//            $date = $obj->format('Y-m-d H:i:s');
//        }
//
//        return $date;
//    }
//
//    if (!empty($obj) && is_object($obj) && !method_exists($obj,'format')) {
//        $date = new DateTime($obj);
//        $date = $date->getTimestamp();
//
//
//        $date = !empty($format) ? date($format, $date) : date('Y-m-d H:i:s', $date);
//
//        return $date;
//    }
//
//
//    //$date = !empty($obj) && !is_object($obj) ? $obj : null;
//	if (!empty($obj) && !is_object($obj)) {
//
//		$date = !empty($format) ? date($format, strtotime($obj)) : date('Y-m-d H:i:s', strtotime($obj));
//
//	}
//
//    return $date;
//}
//
//function num2word($n, $titles)
//{
//    $cases = array(2, 0, 1, 1, 1, 2);
//    return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
//}
//
//function num2str($num) {
//	$nul='ноль';
//	$ten=array(
//		array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
//		array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
//	);
//	$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
//	$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
//	$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
//	$unit=array( // Units
//		array('копейка' ,'копейки' ,'копеек',	 1),
//		array('рубль'   ,'рубля'   ,'рублей'    ,0),
//		array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
//		array('миллион' ,'миллиона','миллионов' ,0),
//		array('миллиард','милиарда','миллиардов',0),
//	);
//	//
//	list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
//	$out = array();
//	if (intval($rub)>0) {
//		foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
//			if (!intval($v)) continue;
//			$uk = sizeof($unit)-$uk-1; // unit key
//			$gender = $unit[$uk][3];
//			list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
//			// mega-logic
//			$out[] = $hundred[$i1]; # 1xx-9xx
//			if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
//			else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
//			// units without rub & kop
//			if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
//		} //foreach
//	}
//	else $out[] = $nul;
//	$out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
//	$out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
//	return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
//}

if (!function_exists('mb_ucfirst') && extension_loaded('mbstring'))
{
	/**
	 * mb_ucfirst - преобразует первый символ в верхний регистр
	 * @param string $str - строка
	 * @param string $encoding - кодировка, по-умолчанию UTF-8
	 * @return string
	 */
	function mb_ucfirst($str, $encoding='UTF-8')
	{
		$str = mb_ereg_replace('^[\ ]+', '', $str);
		$str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
			   mb_substr($str, 1, mb_strlen($str), $encoding);
		return $str;
	}
}
///**
// * Склоняем словоформу
// * @author runcore
// */
//function morph($n, $f1, $f2, $f5) {
//	$n = abs(intval($n)) % 100;
//	if ($n>10 && $n<20) return $f5;
//	$n = $n % 10;
//	if ($n>1 && $n<5) return $f2;
//	if ($n==1) return $f1;
//	return $f5;
//}


//Запрашиваем данные
$oborud_check=1;
//$price_check=1;
$ID = $_GET['ID']; 
$TZ_ID = $_GET['TZ_ID']; 
$AKT_NUM = $_GET['NUM'];
$AKT_DATE = $_GET['DATE'];
$deal = CCrmDeal::GetByID($ID); 
$company = CCrmCompany::GetByID($deal['COMPANY_ID']);
$ispolnitel = '';
$user = CUser::GetByID($deal['ASSIGNED_BY_ID'])->arResult[0];
$workpos = $user['WORK_POSITION'];
$ispolnitel = mb_substr($user['NAME'], 0, 1, 'UTF-8') . '. ' . (($user['SECOND_NAME']) ? mb_substr($user['SECOND_NAME'], 0, 1, 'UTF-8') . '. ' : '') . $user['LAST_NAME'];
$accMail = $_GET['ACCMAIL'] ?? '';

if (!empty($accMail)) {
	$DB->Query("UPDATE `ba_tz` SET `acc_email` = '{$accMail}' WHERE `ID` = {$TZ_ID}");
}
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

$selectedProtocolData = null;

//if (!empty($ID) && in_array((int)$ID, $newResult->arrIdDeal) || !empty($ID) && $ID === 5138  || !empty($ID) && $ID === 5479 || !empty($ID) && $ID === 5682 || !empty($ID) && $ID === 5697 || !empty($ID) && $ID === 5893) {
if (!empty($ID) && (in_array((int)$ID, $newResult->arrIdDeal) || (int)$ID > $newResult->idDealВefore)) {
    $selectedProtocolData = $db->select("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}", [$TZ_ID]);
}

$countOfSelectedProtolols = !empty($selectedProtocolData) ? count($selectedProtocolData) : 0;

$arNumbersDateProtocolsForActVR = [];
$arrayNumbersDateProtocolsFor1C = [];
if ($countOfSelectedProtolols) {
    foreach ($selectedProtocolData as $keyProtocolData => $valProtocolData) {
        $protocolDate = !empty($valProtocolData['DATE']) ? dateObjectFormat($valProtocolData['DATE'], 'd.m.Y') : null;
        $protocolYear = !empty($valProtocolData['DATE']) ? dateObjectFormat($valProtocolData['DATE'], 'y') : null;
        $protocolNumber = !empty($valProtocolData['NUMBER']) ? $valProtocolData['NUMBER'] : null;

        if ($protocolNumber && $protocolDate) {

            $arrayNumbersDateProtocolsFor1C[] = $protocolNumber . " от " . $protocolDate;

            $arNumbersDateProtocolsForActVR[] = $protocolNumber . '/' . $protocolYear. " от " . $protocolDate . " г.";
        }
    }
}

$numbersDateProtocolsForActVR = !empty($arNumbersDateProtocolsForActVR) ? implode(', ', $arNumbersDateProtocolsForActVR) : '';


global $DB;
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . $TZ_ID);
$ba_tz = $res->Fetch();
$tz = unserialize($ba_tz['TZ']);

$mail = $accMail;
$labs = unserialize($ba_tz['LAB']);
$probe = unserialize($ba_tz['PROBE']);
$results_tz = unserialize($ba_tz['RESULTS']);
$type_tz = $ba_tz['TYPE_TZ'];
$MATCH_MANUAL = unserialize($ba_tz['MATCH_MANUAL']);
$SRED = unserialize($ba_tz['SRED']);
$OSTATKI = unserialize($ba_tz['ostatki']);
$NORM_DESC = unserialize($ba_tz['NORM_DESC']);
$description = $ba_tz['DESCRIPTION'];

$date1 = $ba_tz['DATE1'];
$date2 = $ba_tz['DATE2'];

$dopinfo = $ba_tz['DOPINFO'];
$accuracy=[];


$date1arr = explode('-',$date1);
$date1 = $date1arr[2].'.'.$date1arr[1].'.'.$date1arr[0];
$date2arr = explode('-',$date2);
$date2 = $date2arr[2].'.'.$date2arr[1].'.'.$date2arr[0];

$res_acc = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
$dateAcc = date("d-m-Y_H-i-s");
$curdate = "Акт_".($res_acc['NUMBER'] ? $res_acc['NUMBER'] : $_GET['NUM']) .'_'.($dateAcc ? $dateAcc : $_GET['DATE']);
$curdateEng = "Act_".($res_acc['NUMBER'] ? $res_acc['NUMBER'] : $_GET['NUM']) .'_'.($dateAcc ? $dateAcc : $_GET['DATE']);

$str_type = '';
if ($res_acc) {
	$res_acc = $DB->Query("UPDATE `AKT_VR` SET `ACTUAL_VER` ='".$curdate."', `NUMBER`='".$AKT_NUM."', `DATE` = '".($AKT_DATE ? $AKT_DATE : '')."' WHERE `TZ_ID`=" . $TZ_ID);
    $str_type = 'Формирование счета';
} else {
	$res_acc = $DB->Query("INSERT INTO `AKT_VR` (`DATE`, `TZ_ID`, `ACTUAL_VER`, `NUMBER`) VALUES ('".($AKT_DATE ? $AKT_DATE : '')."', " . $TZ_ID.", '".$curdate."', '". ($AKT_NUM ? $AKT_NUM : 'Ждем ответ от 1С')."')" );
	$str_type = 'Формирование счета';
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

if(!empty($_GET['LEAD'])){
	$lead = $_GET['LEAD'];
	if($lead == '1') {
		// руководитель Ахтямов Э.Р.
		$file = new Bitrix\Main\IO\File(__DIR__ . '/../akt_vr.docx');
		$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

		$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../akt_vr2.docx');
		$document2 = new Bitrix\DocumentGenerator\Body\Docx($file2->getContents());
	}elseif($lead == '2'){
		// руководитель Габдрахманова А.А.
		$file = new Bitrix\Main\IO\File(__DIR__ . '/../akt_vr_g_a_a.docx');
		$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

		$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../akt_vr2_g_a_a.docx');
		$document2 = new Bitrix\DocumentGenerator\Body\Docx($file2->getContents());

	}elseif($lead == '3'){
		// руководитель Молчанова Е.Н.
		$file = new Bitrix\Main\IO\File(__DIR__ . '/../akt_vr_m_e_n.docx');
		$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

		$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../akt_vr2_m_e_n.docx');
		$document2 = new Bitrix\DocumentGenerator\Body\Docx($file2->getContents());
	}
}


$oborud = [];
$results = [];
$resultszern = [];
$number_probes = [];
$numbers = [];
$ob_arr = [];
//$arr_soot = [
//    0 => 'Не соответствует',
//    1 => 'Соответствует',
//    2 => '-',
//	3 => 'Не нормируется'
//];
//foreach ($probe as $prob) {
//    foreach ($prob as $key => $item) {
//        switch ($key) {
//            case 'number_probe':
//                $number_probes = array_merge($number_probes, $item);
//                break;
//            case 'sh_number':
//                $numbers = array_merge($numbers, $item);
//                break;
//        }
//    }
//}
//
//foreach ($tz as $items) {
//    foreach ($items['gost'] as $k => $item) {
//
//        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
//        $gost = $ba_gost->Fetch();
//
//		$ba_connection = $DB->Query("SELECT * FROM `ba_connection` WHERE `GOST_ID`=" . $gost['ID']);
//
//        while ($oborud_id = $ba_connection->Fetch()) {
//            $ba_oborud = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . $oborud_id['OBORUD_ID']);
//            $ob = $ba_oborud->Fetch();
//
//
//            $ob_arr = [
//                'Name' => $ob['OBJECT'] . ' ' . $ob['TYPE_OBORUD'] . ', ' . $ob['REG_NUM'] . ', ' . $ob['FACTORY_NUMBER'] . ', ' . date("d.m.Y", strtotime($ob['god_vvoda_expluatation'])),
//				'Class' => $ob['сlass_precision_and_accuracy'],
//                'Predel' => $ob['measuring_range'],
//                'Poverka' => (($ob['certificate_of_verification']) ? $ob['certificate_of_verification'] : '') . ' до ' . date("d.m.Y", strtotime($ob['POVERKA'])),
//
//            ];
//
//            if (!in_array($ob_arr, $oborud)) {
//                $oborud[] = $ob_arr;
//            }
//        }
//
//        $ress = '';
//		$res1 = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $items['gost_new'][$k])->Fetch();
//
//        $SPECIFICATION[] = $gost['SPECIFICATION'];
//		$SPECIFICATION_TU[] = $res1['SPECIFICATION'];
//        $GOST_VAL[] = $res1['NORM_COMMENT'];//"От " . $items['norm_1'][$k] . " до " . $items['norm_2'][$k] . " " . $items['ed_izm'][$k];
//        $GOST_PUNKT[] = $gost['GOST_PUNKT'];
//		$GOST_PUNKT_TU[] = $res1['GOST_PUNKT'];
//		$GOST_DOC[] = $res1['GOST'];//$items['gost_new'][$k];
//        $GOST_DOC_DESC[] =  $NORM_DESC[$k];//($res1['GOST_TYPE']=='TU_research' ? $NORM_DESC : $res1['NAME_GOST']);//$items['gost_new_desc'][$k];
//		$GOST_DOC_DESC_TU[] = $res1['NAME_GOST'];
//		$GOST_DOC_DESC_MET[] = $gost['NAME_GOST'];
//        if ($items['gost_new'][$k] != '-') $GOST_DOC_PLUS_DESC[] = $items['gost_new'][$k] . " - " . $items['gost_new_desc'][$k];
//        $GOST2[] = $gost['GOST'];
//        $ED[] = $gost['ED'];//$items['ed_izm'][$k];
//		$ED_INDEX[] = $gost['ED_INDEX'];
//        $NORM1[] = $res1['NORM1'];//$items['norm_1'][$k];
//        $NORM2[] = $res1['NORM2'];//$items['norm_2'][$k];
//        $IN_OUT[] = 1;
//		$YEARS[] = $gost['GOST_YEAR'];
//        $MATCH[] = $gost['MATCH_MANUAL'];
//		$accuracy[] =$gost['ACCURACY'];
//		$GOST_TYPE[]=$gost['GOST_TYPE'];
//		//echo $gost['ACCURACY'];
//		//var_dump($accuracy);
//
//    }
//}
//$i = 0;
//
//
//$t=0;
//foreach ($tz as $key => $arrDatum) {
//
//		foreach ($arrDatum['gost'] as $k => $item){
//
//			$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
//
//			$price = number_format($arrDatum['price'][$k], 2, ',', '');
//			$sum = number_format((int)$arrDatum['price'][$k]*(int)$arrDatum['obiem'], 2, ',', '');
//
//				$results[] = [
//					'Object' => $probe_k." ".$probe[$key]['sh_number'][$key_probe-1],//$probe[$num+1]['number_probe'][$number]. ' (' . $probe[$num+1]['sh_number'][$number] .')',//$number_probes[$i] . ' (' . $numbers[$i] .')',
//					'Specification' => $res['SPECIFICATION'],
//					'Price' => $price,//$arrDatum['price'][$k],
//					'Sum' => $sum,//(int)$arrDatum['price'][$k]*(int)$arrDatum['obiem'],
//					'Obiem' => $arrDatum['obiem'],
//					'NUM' => $t+1
//				];
//			$t++;
//		}
//
//}

foreach ($results_tz as $num => $tz_resaults) {

    foreach ($tz_resaults as $number => $tz_resault) {
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
			


		}
$i++;
    }
	
}

foreach ($probe[1]['number_probe'] as $pn => $probe_n)
{
	
	for($l=0; $l<count($tz[1]['gost']); $l++) {
	
	if ($tz[1]['gost'][$l]) {

	$res1 = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $tz[1]['gost'][$l])->Fetch();
	if ($res1['GOST_TYPE']=='TU_zern'||$res1['GOST_TYPE']=='TU_prirod'||$res1['GOST_TYPE']=='TU_12801') {
	$resultszern[] = [
		'Object' => $probe_n . ' (' . $probe[1]['sh_number'][$l] .')',
		'Ost25ch' => $OSTATKI[$l+1][0],
		'Ost125ch' => $OSTATKI[$l+1][1],
		'Ost063ch' => $OSTATKI[$l+1][2],
		'Ost0315ch' => $OSTATKI[$l+1][3],
		'Ost016ch' => $OSTATKI[$l+1][4],
		'Ost016mch' => $OSTATKI[$l+1][5],
		'Ost25p' => $OSTATKI[$l+1][6],
		'Ost125p' => $OSTATKI[$l+1][7],
		'Ost063p' => $OSTATKI[$l+1][8],
		'Ost0315p' => $OSTATKI[$l+1][9],
		'Ost016p' => $OSTATKI[$l+1][10],
		'Ost016mp' => $OSTATKI[$l+1][11]
		];
		$l=10000; //зерновой состав формируем 1 раз!!!
	}
	else

	{
		if ($res1['GOST_TYPE']=='TU_grunt') {
		$resultszern[] = [
		'Object' => $probe_n . ' (' . $probe[1]['sh_number'][$l] .')',
		'Ost10ch' => $OSTATKI[0],
		'Ost5ch' => $OSTATKI[1],
		'Ost2ch' => $OSTATKI[2],
		'Ost1ch' => $OSTATKI[3],
		'Ost05ch' => $OSTATKI[4],
		'Ost025ch' => $OSTATKI[5],
		'Ost01ch' => $OSTATKI[6],
		'Ost005ch' => $OSTATKI[7],
		'Ost005mch' => $OSTATKI[8],
		'Ost10p' => $OSTATKI[9],
		'Ost5p' => $OSTATKI[10],
		'Ost2p' => $OSTATKI[11],
		'Ost1p' => $OSTATKI[12],
		'Ost05p' => $OSTATKI[13],
		'Ost025p' => $OSTATKI[14],
		'Ost01p' => $OSTATKI[15],
		'Ost005p' => $OSTATKI[16],
		'Ost005mp' => $OSTATKI[17]
		];
		$l=10000;
		}
	}
	//$l++;
	}
	}
}


$number_probe = (count($number_probes)>1 ? count($number_probes) . " " . num2word(count($number_probes), ['проба', 'пробы', 'проб']) . ": " : ""); //. implode(", ", $number_probes);
for($i=0; $i<count($number_probes); $i++)
{
	$number_probe = $number_probe . $number_probes[$i] . " (" . $numbers[$i] . ")" . ((count($number_probes)-(int)$i)==1 ? "." : ", ");
	
}



$j=0;
$GOST_DOC3 = [];
for ($i=0; $i<count($results); $i++) {
 
  $years='';
  if($YEARS[$i]) $years='-'.$YEARS[$i];
  $SPECIFICATION_GOST[$i] = $GOST2[$i].$years.' '.$GOST_DOC_DESC_MET[$i];
 
if($GOST_DOC[$i]=='--'||$GOST_DOC[$i]=='') { }
  else {
  $GOST_DOC3[$j] = $GOST_DOC[$i].' '.$GOST_DOC_DESC_TU[$i];
  $j++;
  }
}

$SPECIFICATION = implode(", ", array_unique($SPECIFICATION));
$SPECIFICATION_GOST = implode(", ", array_unique($SPECIFICATION_GOST));
if (!$ba_tz['NO_COMPLIANCE'])
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



    //if (!empty($ID) && in_array((int)$ID, $newResult->arrIdDeal) || !empty($ID) && $ID === 5138  || !empty($ID) && $ID === 5479 || !empty($ID) && $ID === 5682 || !empty($ID) && $ID === 5697) {
	if (!empty($ID) && (in_array((int)$ID, $newResult->arrIdDeal) || (int)$ID > $newResult->idDealВefore)) {

//		$p_number_1c = $arrayNumbersDateProtocolsFor1C;
		$p_number_1c = $arNumbersDateProtocolsForActVR;
    } else {
        
        $p_number = !empty($ba_tz['NUM_P']) ? $ba_tz['NUM_P'] : '';
        $p_date = !empty($ba_tz['DATE_P_CORR']) ? date("d.m.Y", strtotime($ba_tz['DATE_P_CORR'])) : '';
        $p_number_1c = $p_number . " от " . $p_date; 
    }

	$res_inv=$DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID` = {$ba_tz['ID']} order by ID desc")->Fetch();
	
	$iin_user_1c = !empty($requis['RQ_INN']) ? $requis['RQ_INN'] : '';
	//$invNum = (int)$res_inv['ID']-2450;

	$invNum = (int)$ba_tz['ACCOUNT'];
	if(date("Y", strtotime($res_inv['DATE']))=='2020') $invNum = (int)$res_inv['ID'];


	$res_deals_contract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$ba_tz['ID_Z'])->Fetch();

	
	if (!empty($res_deals_contract['ID_CONTRACT'])) {
		$res_gogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$res_deals_contract['ID_CONTRACT'])->Fetch();
	}


	$number_dogovor_1c = !empty($res_gogovor['NUMBER']) ? $res_gogovor['NUMBER'] : '';
    $number_akt_vr_1c = !empty($invNum) ? $invNum : '';

	$date_dogovor_1c = !empty($res_gogovor['DATE']) ? date("d.m.Y", strtotime($res_gogovor['DATE'])) : '';
    $date_aakt_vr_1c = (!empty($res_inv['DATE']) ?  date("d.m.Y", strtotime($res_inv['DATE'])) : '');

	$kpp_1c = !empty($requis['RQ_KPP']) ? $requis['RQ_KPP'] : '';

    $type_dog = '';

	if ($res_gogovor['CONTRACT_TYPE'] === 'Счет-оферта') {
	    $type_dog = 'cчет-оферта ';
    }
	
   $arr_data_1c = [
		 "fl" => 1,
		 "number" => $number_akt_vr_1c,
		 "date" => $date_aakt_vr_1c,
		 "inn" => $iin_user_1c,
		 "p_number" => $p_number_1c,
		 "n_dogovor" => "{$type_dog}{$number_dogovor_1c}",
		 "d_dogovor" => $date_dogovor_1c,
		 "kpp" => $kpp_1c,
	];



//	pre($arr_data_1c);
//	exit();
//   $arr_data_1c = [
//		 "fl" => 1,
//		 "number" => 'Cчет-оферта ' . $number_dogovor_1c,
//		 "date" => $date_aakt_vr_1c,
//		 "inn" => $iin_user_1c,
//		 "p_number" => $p_number_1c,
//		 "n_dogovor" => 'Cчет-оферта ' . $number_dogovor_1c,
//		 "d_dogovor" => $date_dogovor_1c,
//		 "kpp" => $kpp_1c,
//	];


	$res_select_requests_for_1c = $DB->Query("SELECT * FROM `REQUESTS_FOR_1C` WHERE `ID` = 1")->Fetch();
//pre($arr_data_1c);
//exit();
if (!empty($str_type) && $str_type == 'Формирование счета' && empty($_GET['NUM'])) {

    if (!empty($res_select_requests_for_1c['REQ'])) {
        $res2_select_requests_for_1c = $DB->Query("SELECT * FROM `REQUESTS_FOR_1C` WHERE `ID` = 1")->Fetch();
        $arrReq = !empty($res2_select_requests_for_1c['REQ']) ? unserialize($res2_select_requests_for_1c['REQ']) : '';

        if (isset($arrReq['requests'])) {
            array_push($arrReq['requests'], $arr_data_1c);


            $res2_update_requests_for_1c = $DB->Query("UPDATE `REQUESTS_FOR_1C` SET `REQ`='" . $DB->ForSql(serialize($arrReq)) . "' WHERE `ID` = 1");
        }

    } else {
        $arrReq['requests'] = [];
        array_push($arrReq['requests'], $arr_data_1c);
//        if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
//            echo "<pre>";
//            print_r($arrReq);
//            exit();
//        }

        $res_update_requests_for_1c = $DB->Query("UPDATE `REQUESTS_FOR_1C` SET `REQ`='" . $DB->ForSql(serialize($arrReq)) . "' WHERE `ID` = 1");
    }
}


	
$assigned = CUser::GetByID($USER->GetID())->arResult[0];
$assigned_user = $assigned['NAME'].' '.$assigned['LAST_NAME'];
$ID_P = $ba_tz['NUM_P'] ? $ba_tz['NUM_P'] : (int)$ID+485;
$dogovorNumber = explode(', ID', $ba_tz['DOGOVOR_NUM']);

$dogovor_num = getDogovor($ba_tz['ID_Z']);

$y_date = date("Y", strtotime($ba_tz['DATE_P_CORR']));
	if ($y_date != '2020') $y_date = "/".substr($y_date, 2);
	else $y_date = "/".$y_date;


//if (!empty($ID) && in_array((int)$ID, $newResult->arrIdDeal) || !empty($ID) && $ID === 5138  || !empty($ID) && $ID === 5479 || !empty($ID) && $ID === 5682 || !empty($ID) && $ID === 5697) {
if (!empty($ID) && (in_array((int)$ID, $newResult->arrIdDeal) || (int)$ID > $newResult->idDealВefore)) {
	$nProtocol = $numbersDateProtocolsForActVR;
} else {
    $nProtocol = $ba_tz['NUM_P'].$y_date." от ".date("d.m.Y", strtotime($ba_tz['DATE_P_CORR']))." г.";
}

$price = 0;
if ( !empty($ba_tz['PRICE']) ) {
	$price = (float)$ba_tz['PRICE'];

	if ( $ba_tz['discount_type'] == 'percent' ) {
		$price -= $price * $ba_tz['DISCOUNT'] / 100;
	} elseif ( $ba_tz['discount_type'] == 'rub' ) {
		$price -= $ba_tz['DISCOUNT'];
	}

} else {
	$row['PRICE'] = '';
}
//echo '<pre>';
//print_r($price);
//exit();
if ( $price < 0 ) {
	$price = 0;
}

$document->normalizeContent();

$document->setValues([
    'nProtocol' => $nProtocol,
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
    'nProbe' => $number,
	'nProbe_reg' => substr($numbers[0],0,-5)."/19",
    'celIspit' => $SPECIFICATION,
    'metodIspit' => $SPECIFICATION_GOST,
	'normDoc' => $GOST_DOC,
    'mIspit' => $lab,
    'd1Ispit' => $date1,
    'd2Ispit' => ((string)$date2==(string)$date1 ? '' : '-'.(string)$date2),
    'tIspit' => $ba_tz['TEMP_O'],
    'vIspit' => $ba_tz['VLAG_O'],
	'workPos' => $workpos,
	'dopInfo' => $dopinfo,
	'Description' => $description,
    'Ispolnitel' => $ispolnitel,
	'placeIspit' => $probe['mesto_data'],
	'PNG' => 'http://172.16.255.2/code.jpg',
	'SumAll' => number_format($price, 2, ',', ''),
	'nds' => number_format($price * 0.95238095238 * 0.05, 2, ',', ''),
	'SumAllWord' => mb_ucfirst(num2str($price)),
	'NUM_ACC' => $AKT_NUM,
	'DATE_ACC' => date("d.m.Y", strtotime($AKT_DATE)),
	'mail' => $mail,


    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $oborud,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'TableItemName' => 'Table.Item.Name',
    'TableItemClass' => 'Table.Item.Class',
    'TableItemPredel' => 'Table.Item.Predel',
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
    'ResultsItemPrice' => 'Results.Item.Price',
	'ResultsItemSum' => 'Results.Item.Sum',
	'ResultsItemNUM' => 'Results.Item.NUM',
    'ResultsItemObiem' => 'Results.Item.Obiem',
    'ResultsItemNormval' => 'Results.Item.Normval',
    'ResultsItemNormdocmet' => 'Results.Item.Normdocmet',
	'ResultsItemValues' => 'Results.Item.Values',
    'ResultsItemValue' => 'Results.Item.Value',
    'ResultsItemResult' => 'Results.Item.Result',


]

);


$result = $document->process();
$content = $document->getContent();

$newpath = "archive_aktvr/".$ID."/".$curdate.".docx";
$interimPath = "interim_archive_aktvr/".$ID."/".$curdateEng.".docx";
$outputFile = $curdate.".pdf";
$outputPath = "archive_aktvr/".$ID."/";

function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );

    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );

    file_put_contents( $fullPath, $contents, $flags );
}
$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_aktvr/'.$ID;
if( !is_dir( $newDirectory ) ) {
	mkdir($newDirectory, 0777, true);
}
file_force_contents($interimPath, $content);

$fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$interimPath;
$fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$outputPath;


use  NcJoes\OfficeConverter\OfficeConverter;

$converter  =  new  OfficeConverter($fullPathDocx, $fullPathPDF);//, $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/');
$converter -> convertTo($outputFile); // генерирует pdf файл в том же каталоге, что и test-file.docx

// вывод непосредственно в браузер
//echo "ВЕДУТСЯ ТЕХНИЧЕСКИЕ РАБОТЫ! ПОПРОБУЙТЕ СФОРМИРОВАТЬ ПРОТОКОЛ ПОЗЖЕ!";
$document2->normalizeContent();

$document2->setValues([
    'nProtocol' => $nProtocol,
    'dProtocol' => ($ba_tz['DATE_P_CORR'] ? date("d.m.Y", strtotime($ba_tz['DATE_P_CORR'])) : date("d.m.Y")),
    'nDogovor' => $dogovor_num,
	'nRequest' => (!empty($deal['TITLE']) ? ' Заявка ' . $deal['TITLE'] : ''),
    'dDorovor' => '',
    'nZakazchik' => $deal['COMPANY_TITLE'],
    'innZakazchik' => $requis['RQ_INN'],
    'ogrnZakazchik' => $requis['RQ_OGRN'],
    'aZakazchik' => $requis['RQ_ACCOUNTANT'],
    'dProbe' => ($ba_tz['DATE_ACT'] ? date("d.m.Y", strtotime($ba_tz['DATE_ACT'])) : end($probe)['dat']),
    'mestoSboraProbe' => (string)end($probe)['mesto_data'],
    'oStroit' => $ba_tz['OBJECT'],
    'oIspit' => $number_probe,
    'nProbe' => $number,
	'nProbe_reg' => substr($numbers[0],0,-5)."/19",
    'celIspit' => $SPECIFICATION,
    'metodIspit' => $SPECIFICATION_GOST,
	'normDoc' => $GOST_DOC,
    'mIspit' => $lab,
    'd1Ispit' => $date1,
    'd2Ispit' => ((string)$date2==(string)$date1 ? '' : '-'.(string)$date2),
    'tIspit' => $ba_tz['TEMP_O'],
    'vIspit' => $ba_tz['VLAG_O'],
	'workPos' => $workpos,
	'dopInfo' => $dopinfo,
	'Description' => $description,
    'Ispolnitel' => $ispolnitel,
	'placeIspit' => $probe['mesto_data'],
	'PNG' => 'http://172.16.255.2/code.jpg',
    'SumAll' => number_format($price, 2, ',', ''),
        'nds' => number_format($price * 0.95238095238 * 0.05, 2, ',', ''),
	'SumAllWord' => mb_ucfirst(num2str($price)),
	'NUM_ACC' => $AKT_NUM,
	'DATE_ACC' => date("d.m.Y", strtotime($AKT_DATE)),
    'mail' => $mail,


    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $oborud,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'TableItemName' => 'Table.Item.Name',
    'TableItemClass' => 'Table.Item.Class',
    'TableItemPredel' => 'Table.Item.Predel',
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
    'ResultsItemPrice' => 'Results.Item.Price',
	'ResultsItemSum' => 'Results.Item.Sum',
	'ResultsItemNUM' => 'Results.Item.NUM',
    'ResultsItemObiem' => 'Results.Item.Obiem',
    'ResultsItemNormval' => 'Results.Item.Normval',
    'ResultsItemNormdocmet' => 'Results.Item.Normdocmet',
	'ResultsItemValues' => 'Results.Item.Values',
    'ResultsItemValue' => 'Results.Item.Value',
    'ResultsItemResult' => 'Results.Item.Result',


]

);

$result2 = $document2->process();
$content2 = $document2->getContent();
if($oborud_check||($USER->GetID()=='1')||($USER->GetID()=='10')){
	header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	header('Content-Disposition: attachment;filename="Акт № ' . $AKT_NUM . ' от ' . date("d.m.Y", strtotime($AKT_DATE)) . '.docx"');
	header('Cache-Control: max-age=0');
	echo $content2;
}
die;
