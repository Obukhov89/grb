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

//-------------'Конвертация pdf' начало-----------------//
require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';
//-------------'Конвертация pdf' окончание-----------------//
Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

function num2word($n, $titles)
{
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
}

function num2str($num) {
	$nul='ноль';
	$ten=array(
		array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
		array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
	);
	$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	$unit=array( // Units
		array('копейка' ,'копейки' ,'копеек',	 1),
		array('рубль'   ,'рубля'   ,'рублей'    ,0),
		array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
		array('миллион' ,'миллиона','миллионов' ,0),
		array('миллиард','милиарда','миллиардов',0),
	);
	//
	list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
	$out = array();
	if (intval($rub)>0) {
		foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
			if (!intval($v)) continue;
			$uk = sizeof($unit)-$uk-1; // unit key
			$gender = $unit[$uk][3];
			list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
			// mega-logic
			$out[] = $hundred[$i1]; # 1xx-9xx
			if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
			else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
			// units without rub & kop
			if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
		} //foreach
	}
	else $out[] = $nul;
	$out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
	$out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
	return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

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
/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
	$n = abs(intval($n)) % 100;
	if ($n>10 && $n<20) return $f5;
	$n = $n % 10;
	if ($n>1 && $n<5) return $f2;
	if ($n==1) return $f1;
	return $f5;
}


function dump($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}

//Запрашиваем данные
$oborud_check=1;
//$price_check=1;
//-------------'Сохранение истории' начало-----------------//
//$ID = $_GET['ID']; //ID Заявки
$ID = !empty($_GET['ID']) ? $_GET['ID'] : ''; //ID Заявки
//-------------'Сохранение истории' окончание-----------------//																						  
$TZ_ID = !empty($_GET['TZ_ID']) ? $_GET['TZ_ID'] : ''; //ID Технического задания. В нём хранится ТЗ, результаты исследований и информация о пробах
$deal = CCrmDeal::GetByID($ID); //Берём значение сделки
$company = CCrmCompany::GetByID($deal['COMPANY_ID']);
$ispolnitel = '';
$user = CUser::GetByID($deal['ASSIGNED_BY_ID'])->arResult[0];
//dump($user);
$workpos = $user['WORK_POSITION'];
$ispolnitel = mb_substr($user['NAME'], 0, 1, 'UTF-8') . '. ' . (($user['SECOND_NAME']) ? mb_substr($user['SECOND_NAME'], 0, 1, 'UTF-8') . '. ' : '') . $user['LAST_NAME'];

global $DB;
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . $TZ_ID);
$ba_tz = $res->Fetch();
$tz = unserialize($ba_tz['TZ']);
//$date1s = unserialize($ba_tz['DATE1']);
//$date2s = unserialize($ba_tz['DATE2']);
$labs = unserialize($ba_tz['LAB']);
$probe = unserialize($ba_tz['PROBE']);
$results_tz = unserialize($ba_tz['RESULTS']);
$type_tz = $ba_tz['TYPE_TZ'];
$MATCH_MANUAL = unserialize($ba_tz['MATCH_MANUAL']);
$SRED = unserialize($ba_tz['SRED']);
$OSTATKI = unserialize($ba_tz['ostatki']);
$NORM_DESC = unserialize($ba_tz['NORM_DESC']);
$description = $ba_tz['DESCRIPTION'];
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

$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
if (!empty($res_acc['ID_Z'])) {
	$result_select_deals_to_contract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . $res_acc['ID_Z'])->Fetch();
}
//Дата сохранения счета в истории
$date = date('Y-m-d H:i:s');
//$dateAcc = date("d-m-Y_H-i-s");
$dateAcc = date('d-m-Y_H-i-s', strtotime($date));

if($res_acc) { 
	$curdate = "Счет_".$res_acc['ID'].'_'.$dateAcc;

	$curdateEng = "Account_".$res_acc['ID'].'_'.$dateAcc;
	//$accountNum = $resInv['ID'];
}
$str_type = '';
//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//
																															  
//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
if($res_acc) {
//$res_acc = $DB->Query("UPDATE `INVOICE` SET `DATE`= NOW(),`ACTUAL_VER` ='" . $curdate . "' WHERE `TZ_ID`=" . $TZ_ID);
	$res_acc = $DB->Query("UPDATE `INVOICE` SET `ACTUAL_VER` ='" . $curdate . "' WHERE `TZ_ID`=" . $TZ_ID); //При обновлении дата не обновляется
    $str_type = 'Обновление счета';
} else {
	//Создаем новый счет
    //$res_acc = $DB->Query("INSERT INTO `INVOICE` (`DATE`, `TZ_ID`, `ACTUAL_VER`) VALUES (NOW(), " . $TZ_ID . ", '" . $curdate . "')");
	$res_acc = $DB->Query("INSERT INTO `INVOICE` (`DATE`, `TZ_ID`) VALUES (NOW(), " . $TZ_ID . ")");
    $str_type = 'Формирование счета';
	
	//Находим последний добавленный счет
	$result_select_invoice = $DB->Query("SELECT * FROM `INVOICE` ORDER BY ID DESC LIMIT 1")->Fetch();
	
	//ID, номер счета
	$id_invoice = !empty($result_select_invoice['ID']) ? (int)$result_select_invoice['ID'] : '';
	
	//Создаем актуальную версию
	$curdate = "Счет_" . $id_invoice . '_'.$dateAcc;
	$curdateEng = "Account_" . $id_invoice . '_'.$dateAcc;
	
	if (!empty($id_invoice)) {
		//Добавляем в счет актуальную версию
		$DB->Query("UPDATE `INVOICE` SET `ACTUAL_VER` ='" . $curdate . "' WHERE `ID`=" . $id_invoice);
	}
}
//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//
//-------------'Сохранение истории' окончание-----------------//
$resInv = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();

//--'Формирование счета для 1С' начало--//

//Номер счета
$nember_account_1c = (!empty($resInv['ID']) ?  $resInv['ID'] : '');

//--'Вывод номера счета в журнале заявок' начало--//
	//Записываем счет в ba_tz
	$upate_account_ba_tz = $DB->Query("UPDATE `ba_tz` SET `ACCOUNT` = " . (int)$nember_account_1c . " WHERE `ID`=" . (int)$TZ_ID);
//--'Вывод номера счета в журнале заявок' окончание--//

//Дата создания счета
$date_account_1c = (!empty($resInv['DATE']) ?  date("d.m.Y", strtotime($resInv['DATE'])) : '');

//Сумма счета
$price_account_1c = !empty($ba_tz['PRICE']) ?  $ba_tz['PRICE'] : '';

//--'Формирование счета для 1С' окончание--//
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
$file = new Bitrix\Main\IO\File(__DIR__ . '/../Account.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../Account2.docx');
$document2 = new Bitrix\DocumentGenerator\Body\Docx($file2->getContents());



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
 // var_dump($numbers);
 // echo'____________________________________________';
 // var_dump($number_probes);

foreach ($tz as $items) {
    foreach ($items['gost'] as $k => $item) {
		
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
        $gost = $ba_gost->Fetch();
        //echo $k.'='.$gost['ID'].'  ';
		$ba_connection = $DB->Query("SELECT * FROM `ba_connection` WHERE `GOST_ID`=" . $gost['ID']);

        while ($oborud_id = $ba_connection->Fetch()) {
            $ba_oborud = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . $oborud_id['OBORUD_ID']);
            $ob = $ba_oborud->Fetch();
			//var_dump($ob['CHECKED']);
		//	if(!$ob['CHECKED']) {$oborud_check=0; echo "Протокол не может быть сформирован! Оборудование "  . $ob['OBJECT'] . " в методике " . $gost['GOST']. " " . $gost['SPECIFICATION'] . " не проверено! "; }
            $ob_arr = [
                'Name' => $ob['OBJECT'] . ' ' . $ob['TYPE_OBORUD'] . ', ' . $ob['REG_NUM'] . ', ' . $ob['FACTORY_NUMBER'] . ', ' . date("d.m.Y", strtotime($ob['god_vvoda_expluatation'])),
				'Class' => $ob['сlass_precision_and_accuracy'],
                'Predel' => $ob['measuring_range'],
                'Poverka' => (($ob['certificate_of_verification']) ? $ob['certificate_of_verification'] : '') . ' до ' . date("d.m.Y", strtotime($ob['POVERKA'])),
				
            ];

            if (!in_array($ob_arr, $oborud)) {
                $oborud[] = $ob_arr;
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
        $MATCH[] = $gost['MATCH_MANUAL'];
		$accuracy[] =$gost['ACCURACY'];
		$GOST_TYPE[]=$gost['GOST_TYPE'];
		//echo $gost['ACCURACY'];
		//var_dump($accuracy); 
		
    }
}
$i = 0;
//if(!count($oborud)) {$oborud_check=0; echo "Протокол не может быть сформирован! Не указано используемое оборудование! "; }
//$k=1;
// var_dump($results_tz);
// echo "________________________________";

$t=0;
$changeArr = ['2688', '3197', '2370', '211', '1437', '15', '886', '882', '178', '2701', '9', '60', '3', '59', '2688', '3136', '2341', '1080', '1081', '320', '3133', '134',
'2635', '2636', '2637', '2638', '1076', '1082', '1083', '1075', '3003', '3004', '329', '1442', '3289', '28', '1114', '3336', '2680', '3289', '1252', '354', '3446', '2735',
'2261', '1031', '2678', '2634'];

$checkPrice=0;
//dump($tz);
foreach ($tz as $key => $arrDatum) {
	// foreach($probe[$key]['number_probe'] as $key_probe => $probe_k){
		// $key_probe = $key_probe+1; $j=0; 
		// $z="."; $z_pos=0;
		foreach ($arrDatum['gost'] as $k => $item){
		
			$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
		
			$price = number_format($arrDatum['price'][$k], 2, ',', '');
			$sum = number_format((int)$arrDatum['price'][$k]*(int)$arrDatum['obiem'], 2, ',', '');
			if((int)$price) {
				$results[] = [
					'Object' => $probe_k." ".$probe[$key]['sh_number'][$key_probe-1],//$probe[$num+1]['number_probe'][$number]. ' (' . $probe[$num+1]['sh_number'][$number] .')',//$number_probes[$i] . ' (' . $numbers[$i] .')',
					'Specification' => $res['SPECIFICATION']. " " . $res['GOST'],
					'Price' => $price,//$arrDatum['price'][$k],
					'Sum' => $sum,//(int)$arrDatum['price'][$k]*(int)$arrDatum['obiem'],
					'Obiem' => $arrDatum['obiem'],
					'NUM' => $t+1
			
				];
			$t++;
			}
			elseif(!in_array($res['ID'], $changeArr))
			{
				$checkPrice=1;
			}
			echo $checkPrice." ";
		} 
	
}
//var_dump($results);
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
//var_dump($results);

//$l=0;
foreach ($probe[1]['number_probe'] as $pn => $probe_n)
{
	//var_dump($tz);
	
	for($l=0; $l<count($tz[1]['gost']); $l++) {
	//echo $tz[1]['gost'][$l];
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
//var_dump($resultszern);

$number_probe = (count($number_probes)>1 ? count($number_probes) . " " . num2word(count($number_probes), ['проба', 'пробы', 'проб']) . ": " : ""); //. implode(", ", $number_probes);
for($i=0; $i<count($number_probes); $i++)
{
	$number_probe = $number_probe . $number_probes[$i] . " (" . $numbers[$i] . ")" . ((count($number_probes)-(int)$i)==1 ? "." : ", ");
	//echo $numbers[$i]."__";
}

//$number = implode(", ", $numbers);

$j=0;
$GOST_DOC3 = [];
for ($i=0; $i<count($results); $i++) {
  //$strGOST=strpos($GOST2[$i], "п"); //отрезаем пункты гостов для протокола
  //$GOST2[$i]=substr($GOST2[$i], 0, $strGOST);
  $years='';
  if($YEARS[$i]) $years='-'.$YEARS[$i];
  $SPECIFICATION_GOST[$i] = $GOST2[$i].$years.' '.$GOST_DOC_DESC_MET[$i];
  //echo $i.'='.$GOST_DOC[$i].'  -----';
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

//--'Формирование счета для 1С' начало--//
   //ИИН заказчика
   $iin_user_1c = !empty($requis['RQ_INN']) ? $requis['RQ_INN'] : '';
	//echo'<pre>';var_dump('$iin_user_1c', $iin_user_1c);

	//--'Передача Наименование компании в 1С' начало--//
    //Наименование клиента(компания)
	$company_title = !empty($deal['COMPANY_TITLE']) ? $deal['COMPANY_TITLE'] : '';
	//--'Передача Наименование компании в 1С' окончание--//
	
	
	//--'Добавление реквизитов договора в 1С' начало--//
	//Находим ID договора по сделке
	$res_deals_contract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$ba_tz['ID_Z'])->Fetch();
	//$result_select_deals_to_contract
	
	//Находим договор по ID
	if (!empty($res_deals_contract['ID_CONTRACT'])) {
	//if (!empty($result_select_deals_to_contract['ID_CONTRACT'])) {
		$res_gogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$res_deals_contract['ID_CONTRACT'])->Fetch();
		//$res_gogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$result_select_deals_to_contract['ID_CONTRACT'])->Fetch();
	}
	
	//Номер договора
	$number_dogovor_1c = !empty($res_gogovor['NUMBER']) ? $res_gogovor['NUMBER'] : '';
	
	//Дата договора
	$date_dogovor_1c = !empty($res_gogovor['DATE']) ? date("d.m.Y", strtotime($res_gogovor['DATE'])) : '';
	//--'Добавление реквизитов договора в 1С' окончание--//

   //массив с данными о счете
   $arr_data_1c = [
		 "fl" => 0,
		 "number" => $nember_account_1c, //Номер счета
		 "date" => $date_account_1c, //Дата создания счета
		 "inn" => $iin_user_1c, //ИИН заказчика
		 //--'Передача Наименование компании в 1С' начало--//
		 "summ" => $price_account_1c, //Сумма счет
		"title" => $company_title, //Наименование клиента(компания)
		//--'Передача Наименование компании в 1С' окончание--//
		//--'Добавление реквизитов договора в 1С' начало--//
		 "n_dogovor" => $number_dogovor_1c, //Номер договора
		 "d_dogovor" => $date_dogovor_1c //Дата договора
		 //--'Добавление реквизитов договора в 1С' окончание--//
	];
	
	/*$arr_data_1c = [
		"fl" => 0,
		 "number" => 174,
		 "date" => "13.05.2020",
		 "inn" => "7447171717",
		 "summ" => 3456
	];*/
	
	
	$res_select_requests_for_1c = $DB->Query("SELECT * FROM `REQUESTS_FOR_1C` WHERE `ID` = 1")->Fetch();

    //Если счет формируем первый раз то записываем данные в таблицу
	if (!empty($str_type) && $str_type == 'Формирование счета') {
		//Проверяем если в поле 'REQ' есть масив с данными счетов, то добавляем к ним новые данные счета.Иначе добавляем новый массив с данными
		if (!empty($res_select_requests_for_1c['REQ'])) {
			$res2_select_requests_for_1c = $DB->Query("SELECT * FROM `REQUESTS_FOR_1C` WHERE `ID` = 1")->Fetch();
			$arrReq = !empty($res2_select_requests_for_1c['REQ']) ? unserialize($res2_select_requests_for_1c['REQ']) : '';
			
			
			if (isset($arrReq['requests'])) {
				array_push($arrReq['requests'], $arr_data_1c);
				//$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос
				//$res2_update_requests_for_1c = $DB->Query("UPDATE `REQUESTS_FOR_1C` SET `REQ`='" . $DB->ForSql(serialize($arrReq)) . "' WHERE `ID` = 1");
			}
			
		} else {
			$arrReq['requests'] = [];
			array_push($arrReq['requests'], $arr_data_1c);
			
			//$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос
			//$res_update_requests_for_1c = $DB->Query("UPDATE `REQUESTS_FOR_1C` SET `REQ`='" . $DB->ForSql(serialize($arrReq)) . "' WHERE `ID` = 1");
		}
	}
	
	//$res_select_requests_for = $DB->Query("SELECT * FROM `REQUESTS_FOR_1C` WHERE `ID` = 1")->Fetch();
	//$arr_req = unserialize($res_select_requests_for['REQ']);
	//echo'<pre>';var_dump('$$arr_req', $arr_req);die;
	
//--'Формирование счета для 1С' окончание--//
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
$dogovor_num = ($ba_tz['DOGOVOR_NUM'] ? $ba_tz['DOGOVOR_NUM'] : 'Договор №'.$ID);
$dogovor_num = explode(', ID', $dogovor_num);
//-------------'Сохранение истории' начало-----------------//
//Номер заявки
$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';
$ID_P_HISTORY = $ba_tz['NUM_P'] ? $ba_tz['NUM_P'] : 'номер не присвоен';
$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date . "','" . $assigned_user . "','" . $ID_P_HISTORY  . "','" . (int)$TZ_ID . "','" . $USER->GetID() . "', '" . $str_type . "', '" . $num_request . "')");
//-------------'Сохранение истории' окончание-----------------//																					  
//$res = $DB->Query("UPDATE `ba_tz` SET `DATE_P` ='" . date("Y-m-d H:i:s") . "' WHERE ID = " . (int)$TZ_ID);
//$res_history = $DB->Query("UPDATE `HISTORY` SET `DATE` ='" . date("Y-m-d H:i:s") . "', `TYPE`='1', `` WHERE ID = " . (int)$TZ_ID);
//$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`) VALUES ('" . date('Y-m-d H:i:s') . "','" . $assigned_user . "','" . $ID_P . "','" . $TZ_ID . "','" . $USER->GetID() . "', 'Скачивание протокола')");

        // if ($res->result = 1) {
            // echo "<h3>Успешно</h3>";
        // } else {
            // echo "<h3>Ошибка</h3>";
        // }
// //exit;
//var_dump($ba_tz);
//var_dump($requis);
//echo $company['UF_CRM_1567958673619'];
if ($ba_tz['ID'])
{
    $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$TZ_ID . " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
    $history_ass=$history['ASSIGNED'];
    $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
    $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
    if($history_date<$history_date_null) $history_date='';

    //Колонка "последнее изменение"
    $assignet_history = $history_ass . " " . $history_date;

    $update_assignet_ba_tz = $DB->Query("UPDATE `ba_tz` SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . (int)$ID);
}
//Заполняем данные
$document->normalizeContent();
//$document->setFields(['PNG' => ['TYPE' => 'IMAGE'],]);
$document->setValues([
    'nProtocol' => $ID_P,
    'dProtocol' => ($ba_tz['DATE_P_CORR'] ? date("d.m.Y", strtotime($ba_tz['DATE_P_CORR'])) : date("d.m.Y")),
    /*--------------Номер заявки начало----------------*/
	'nRequest' => (!empty($deal['TITLE']) ? ' Заявка ' . $deal['TITLE'] : ''),
    /*--------------Номер заявки окончание----------------*/
	'nDogovor' => $dogovor_num[0],
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
	'SumAll' => number_format($ba_tz['PRICE'], 2, ',', ''),
	'SumAllWord' => mb_ucfirst(num2str($ba_tz['PRICE'])),
	'AllName' => $t,
	'NUM_ACC' => $resInv['ID'],
	'DATE_ACC' =>  date("d.m.Y", strtotime($resInv['DATE'])),
	

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

//var_dump($rrr);
$result = $document->process();
$content = $document->getContent();
//$document->save($filename = '1.docx', $storage = null);
//$curdate = date("d-m-Y_H-i-s");
//$res_acc2 = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();

//-------------'Конвертация pdf' начало-----------------//
$newpath = "archive_acc/".$ID."/".$curdate.".docx";
//$newpath = "archive_acc/".$accountNum."/".$curdate.".docx";

$interimPath = "interim_archive_acc/".$ID."/".$curdateEng.".docx";
//$interimPath = "interim_archive_acc/".$accountNum."/".$curdateEng.".docx";
$outputFile = $curdate.".pdf";
$outputPath = "archive_acc/".$ID."/";
//$outputPath = "archive_acc/".$accountNum."/";
//-------------'Конвертация pdf' окончание-----------------//
function file_force_contents( $fullPath, $contents, $flags = 0 ){
		$parts = explode( '/', $fullPath );
		array_pop( $parts );
		$dir = implode( '/', $parts );
	   
		if( !is_dir( $dir ) )
			mkdir( $dir, 0777, true );
	   
		file_put_contents( $fullPath, $contents, $flags );
	}
	
	use  NcJoes\OfficeConverter\OfficeConverter;

 if(!$checkPrice)
 {
	

	//file_force_contents($newpath, $content);
	//-------------'Конвертация pdf' начало-----------------//
	//$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_acc/'.$accountNum;
	$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_acc/'.$ID;
	if( !is_dir( $newDirectory ) ) {
		mkdir($newDirectory, 0777, true);
	}
	file_force_contents($interimPath, $content);

	$fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$interimPath;
	$fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$outputPath;

	// если вы используете composer, просто используйте это


	$converter  =  new  OfficeConverter($fullPathDocx, $fullPathPDF);//, $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/');
	$converter -> convertTo($outputFile); // генерирует pdf файл в том же каталоге, что и test-file.docx
	//-------------'Конвертация pdf' окончание-----------------//
	//file_force_contents($newpath, $content);
	// вывод непосредственно в браузер
	//echo "ВЕДУТСЯ ТЕХНИЧЕСКИЕ РАБОТЫ! ПОПРОБУЙТЕ СФОРМИРОВАТЬ ПРОТОКОЛ ПОЗЖЕ!";
	
	$document2->normalizeContent();
//$document->setFields(['PNG' => ['TYPE' => 'IMAGE'],]);
$document2->setValues([
    'nProtocol' => $ID_P,
    'dProtocol' => ($ba_tz['DATE_P_CORR'] ? date("d.m.Y", strtotime($ba_tz['DATE_P_CORR'])) : date("d.m.Y")),
    /*--------------Номер заявки начало----------------*/
	'nRequest' => (!empty($deal['TITLE']) ? ' Заявка ' . $deal['TITLE'] : ''),
    /*--------------Номер заявки окончание----------------*/
	'nDogovor' => $dogovor_num[0],
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
	'SumAll' => number_format($ba_tz['PRICE'], 2, ',', ''),
	'SumAllWord' => mb_ucfirst(num2str($ba_tz['PRICE'])),
	'AllName' => $t,
	'NUM_ACC' => $resInv['ID'],
	'DATE_ACC' => date("d.m.Y", strtotime($resInv['DATE'])),
	

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

//var_dump($rrr);
$result2 = $document2->process();
$content2 = $document2->getContent();
	
	

	if($oborud_check||($USER->GetID()=='1')||($USER->GetID()=='10')){
		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		header('Content-Disposition: attachment;filename="Счет № ' . $resInv['ID'] . ' от ' . date("d.m.Y") . '.docx"');
		header('Cache-Control: max-age=0');
		echo $content2; 
	}
 }
else
	echo " Счет не может быт сформирован! В техническом задании присутствуют методики с неуказанной стоимостью.";
die;