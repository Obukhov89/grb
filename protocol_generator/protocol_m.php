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

function num2word($n, $titles)
{
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
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
$ID = $_GET['ID']; //ID Заявки
$TZ_ID = $_GET['TZ_ID']; //ID Технического задания. В нём хранится ТЗ, результаты исследований и информация о пробах
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
$SOstATKI = unserialize($ba_tz['SOSTAV']);
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
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_ob.docm');
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
  var_dump($numbers);
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
			if(!$ob['CHECKED']) {$oborud_check=0; echo "Протокол не может быть сформирован! Оборудование "  . $ob['OBJECT'] . " в методике " . $gost['GOST']. " " . $gost['SPECIFICATION'] . " не проверено! "; }
            $ob_arr = [
                'Name' => $ob['OBJECT'] . ' ' . $ob['TYPE_OBORUD'] . ', ' . $ob['FACTORY_NUMBER'] . ', ' . $ob['REG_NUM'] . ', ' . date("d.m.Y", strtotime($ob['god_vvoda_expluatation'])),
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
		$YEARS2[] = $res1['GOST_YEAR'];
        $MATCH[] = $gost['MATCH_MANUAL'];
		$accuracy[] =$gost['ACCURACY'];
		$GOST_TYPE[]=$gost['GOST_TYPE'];
		$UNNORM[]=$res1['UNNORM'];
		//echo $gost['ACCURACY'];
		//var_dump($accuracy); 
		
    }
}
$i = 0;
if(!count($oborud)) {$oborud_check=0; echo "Протокол не может быть сформирован! Не указано используемое оборудование! "; }
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
			$res_TU = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $arrDatum['gost_new'][$j])->Fetch();
			
			$ress = $arr_soot[$MATCH_MANUAL[$key - 1][$k][$key_probe]];
			for($l=0; $l<count($results_tz[$key - 1][$k][$key_probe]); $l++){
				$z_pos=strpos($results_tz[$key - 1][$k][$key_probe][$l],$z);
				if ($z_pos) $results_tz[$key - 1][$k][$key_probe][$l] = substr_replace($results_tz[$key - 1][$k][$key_probe][$l], ",", $z_pos, 1);
			}
			$z_pos2 = strpos($SRED[$key - 1][$k][$key_probe]);
			$SRED[$key - 1][$k][$key_probe] = number_format($SRED[$key - 1][$k][$key_probe], $res['ACCURACY'], ',', '');
			
			if($results_tz[$key - 1][$k][$key_probe][0]!=""){
				$results[] = [
					'Object' => $probe_k." (".$probe[$key]['sh_number'][$key_probe-1].")",//$probe[$num+1]['number_probe'][$number]. ' (' . $probe[$num+1]['sh_number'][$number] .')',//$number_probes[$i] . ' (' . $numbers[$i] .')',
					'Specification' => $res['SPECIFICATION'],
					'SpecificationTU' => $res_TU['SPECIFICATION'],
					'Ed' => $res['ED'],
					'EdIndex' => ($res['ED_INDEX'] ? $res['ED_INDEX'] : ''),
					'Normdoc' => $res_TU['GOST']. ' ' . $res_TU['GOST_PUNKT'],//.' '.$res_TU['SPECIFICATION'],
					'Normval' => ($res_TU['GOST_TYPE']=='TU_research') ? $NORM_DESC[$k] : $res_TU['NORM_COMMENT'],//$GOST_VAL[$i],
					'Normdocmet' => ($res['GOST']) ? $res['GOST'].' '.$res['GOST_PUNKT'] : '-',
					'Normdoc_desc' => '',
					'Values' => $results_tz[$key - 1][$k][$key_probe],
					'Value' => $SRED[$key - 1][$k][$key_probe],
					'Result' => $ress
				];
			} $j++;
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
//var_dump($results);

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
	
	if ($res1['GOST_TYPE']=='TU_zern'||$res1['GOST_TYPE']=='TU_prirod'||$res1['GOST_TYPE']=='TU_12801') {
	$resultszern[] = [
		'Object' => $probe_n . ' (' . $probe[1]['sh_number'][$l] .')',
		'Ost25ch' => $OSTATKI[0],
		'Ost125ch' => $OSTATKI[1],
		'Ost063ch' => $OSTATKI[2],
		'Ost0315ch' => $OSTATKI[3],
		'Ost016ch' => $OSTATKI[4],
		'Ost016mch' => $OSTATKI[5],
		'Ost25p' => $OSTATKI[6],
		'Ost125p' => $OSTATKI[7],
		'Ost063p' => $OSTATKI[8],
		'Ost0315p' => $OSTATKI[9],
		'Ost016p' => $OSTATKI[10],
		'Ost016mp' => $OSTATKI[11],
		'Norm80' => $res_n1[0].(($res_n2[0]&&($res_n2[0]!=$res_n1[0])) ? '-'.$res_n2[0] : ''),
		'Norm40' => $res_n1[1].(($res_n2[1]&&($res_n2[1]!=$res_n1[1])) ? '-'.$res_n2[1] : ''),
		'Norm315' => $res_n1[2].(($res_n2[2]&&($res_n2[2]!=$res_n1[2])) ? '-'.$res_n2[2] : ''),
		'Norm224' => $res_n1[3].(($res_n2[3]&&($res_n2[3]!=$res_n1[3])) ? '-'.$res_n2[3] : ''),
		'Norm20' => $res_n1[4].(($res_n2[4]&&($res_n2[4]!=$res_n1[4])) ? '-'.$res_n2[4] : ''),
		'Norm16' => $res_n1[5].(($res_n2[5]&&($res_n2[5]!=$res_n1[5])) ? '-'.$res_n2[5] : ''),
		'Norm15' => $res_n1[6].(($res_n2[6]&&($res_n2[6]!=$res_n1[6])) ? '-'.$res_n2[6] : ''),
		'Norm112' => $res_n1[7].(($res_n2[7]&&($res_n2[7]!=$res_n1[7])) ? '-'.$res_n2[7] : ''),
		'Norm10' => $res_n1[8].(($res_n2[8]&&($res_n2[8]!=$res_n1[8])) ? '-'.$res_n2[8] : ''),
		'Norm8' => $res_n1[9].(($res_n2[9]&&($res_n2[9]!=$res_n1[9])) ? '-'.$res_n2[9] : ''),
		'Norm5' => $res_n1[10].(($res_n2[10]&&($res_n2[10]!=$res_n1[10])) ? '-'.$res_n2[10] : ''),
		'Norm4' => $res_n1[11].(($res_n2[11]&&($res_n2[11]!=$res_n1[11])) ? '-'.$res_n2[11] : ''),
		'Norm25' => $res_n1[12].(($res_n2[12]&&($res_n2[12]!=$res_n1[12])) ? '-'.$res_n2[12] : ''),
		'Norm2' => $res_n1[13].(($res_n2[13]&&($res_n2[13]!=$res_n1[13])) ? '-'.$res_n2[13] : ''),
		'Norm125' => $res_n1[14].(($res_n2[14]&&($res_n2[14]!=$res_n1[14])) ? '-'.$res_n2[14] : ''),
		'Norm1' => $res_n1[15].(($res_n2[15]&&($res_n2[15]!=$res_n1[15])) ? '-'.$res_n2[15] : ''),
		'Norm063' => $res_n1[16].(($res_n2[16]&&($res_n2[16]!=$res_n1[16])) ? '-'.$res_n2[16] : ''),
		'Norm05' => $res_n1[17].(($res_n2[17]&&($res_n2[17]!=$res_n1[17])) ? '-'.$res_n2[17] : ''),
		'Norm0315' => $res_n1[18].(($res_n2[18]&&($res_n2[18]!=$res_n1[18])) ? '-'.$res_n2[18] : ''),
		'Norm025' => $res_n1[19].(($res_n2[19]&&($res_n2[19]!=$res_n1[19])) ? '-'.$res_n2[19] : ''),
		'Norm016' => $res_n1[20].(($res_n2[20]&&($res_n2[20]!=$res_n1[20])) ? '-'.$res_n2[20] : ''),
		'Norm0125' => $res_n1[21].(($res_n2[21]&&($res_n2[21]!=$res_n1[21])) ? '-'.$res_n2[21] : ''),
		'Norm01' => $res_n1[22].(($res_n2[22]&&($res_n2[22]!=$res_n1[22])) ? '-'.$res_n2[22] : ''),
		'Norm0071' => $res_n1[23].(($res_n2[23]&&($res_n2[23]!=$res_n1[23])) ? '-'.$res_n2[23] : ''),
		'Norm0063' => $res_n1[24].(($res_n2[24]&&($res_n2[24]!=$res_n1[24])) ? '-'.$res_n2[24] : ''),
		'Norm005' => $res_n1[25].(($res_n2[25]&&($res_n2[25]!=$res_n1[25])) ? '-'.$res_n2[25] : ''),
		'Normm005' => $res_n1[26].(($res_n2[26]&&($res_n2[26]!=$res_n1[26])) ? '-'.$res_n2[26] : '')
		
		];
		$l=10000; //зерновой состав формируем 1 раз!!!
	}
	elseif ($res1['GOST_TYPE']=='TU_grunt') {
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
	elseif ($res1['GOST_TYPE']=='TU_183') {
		$resultszern[] = [
		'Object' => $probe_n . ' (' . $probe[1]['sh_number'][$l] .')',
		'Ost25ch' => $OSTATKI[0],
		'Ost125ch' => $OSTATKI[1],
		'Ost063ch' => $OSTATKI[2],
		'Ost0315ch' => $OSTATKI[3],
		'Ost016ch' => $OSTATKI[4],
		'Ost016mch' => $OSTATKI[5],
		'Ost25p' => $OSTATKI[6],
		'Ost125p' => $OSTATKI[7],
		'Ost063p' => $OSTATKI[8],
		'Ost0315p' => $OSTATKI[9],
		'Ost016p' => $OSTATKI[10],
		'Ost016mp' => $OSTATKI[11],
		'SOst25ch' => $OSTATKI[0],
		'SOst125ch' => $SOstATKI[1],
		'SOst063ch' => $SOstATKI[2],
		'SOst0315ch' => $SOstATKI[3],
		'SOst016ch' => $SOstATKI[4],
		'SOst016mch' => $SOstATKI[5],
		'SOst25p' => $SOstATKI[6],
		'SOst125p' => $SOstATKI[7],
		'SOst063p' => $SOstATKI[8],
		'SOst0315p' => $SOstATKI[9],
		'SOst016p' => $SOstATKI[10],
		'SOst016mp' => $SOstATKI[11],
		'Norm80' => $res_n1[0].(($res_n2[0]&&($res_n2[0]!=$res_n1[0])) ? '-'.$res_n2[0] : ''),
		'Norm40' => $res_n1[1].(($res_n2[1]&&($res_n2[1]!=$res_n1[1])) ? '-'.$res_n2[1] : ''),
		'Norm315' => $res_n1[2].(($res_n2[2]&&($res_n2[2]!=$res_n1[2])) ? '-'.$res_n2[2] : ''),
		'Norm224' => $res_n1[3].(($res_n2[3]&&($res_n2[3]!=$res_n1[3])) ? '-'.$res_n2[3] : ''),
		'Norm20' => $res_n1[4].(($res_n2[4]&&($res_n2[4]!=$res_n1[4])) ? '-'.$res_n2[4] : ''),
		'Norm16' => $res_n1[5].(($res_n2[5]&&($res_n2[5]!=$res_n1[5])) ? '-'.$res_n2[5] : ''),
		'Norm15' => $res_n1[6].(($res_n2[6]&&($res_n2[6]!=$res_n1[6])) ? '-'.$res_n2[6] : ''),
		'Norm112' => $res_n1[7].(($res_n2[7]&&($res_n2[7]!=$res_n1[7])) ? '-'.$res_n2[7] : ''),
		'Norm10' => $res_n1[8].(($res_n2[8]&&($res_n2[8]!=$res_n1[8])) ? '-'.$res_n2[8] : ''),
		'Norm8' => $res_n1[9].(($res_n2[9]&&($res_n2[9]!=$res_n1[9])) ? '-'.$res_n2[9] : ''),
		'Norm5' => $res_n1[10].(($res_n2[10]&&($res_n2[10]!=$res_n1[10])) ? '-'.$res_n2[10] : ''),
		'Norm4' => $res_n1[11].(($res_n2[11]&&($res_n2[11]!=$res_n1[11])) ? '-'.$res_n2[11] : ''),
		'Norm25' => $res_n1[12].(($res_n2[12]&&($res_n2[12]!=$res_n1[12])) ? '-'.$res_n2[12] : ''),
		'Norm2' => $res_n1[13].(($res_n2[13]&&($res_n2[13]!=$res_n1[13])) ? '-'.$res_n2[13] : ''),
		'Norm125' => $res_n1[14].(($res_n2[14]&&($res_n2[14]!=$res_n1[14])) ? '-'.$res_n2[14] : ''),
		'Norm1' => $res_n1[15].(($res_n2[15]&&($res_n2[15]!=$res_n1[15])) ? '-'.$res_n2[15] : ''),
		'Norm063' => $res_n1[16].(($res_n2[16]&&($res_n2[16]!=$res_n1[16])) ? '-'.$res_n2[16] : ''),
		'Norm05' => $res_n1[17].(($res_n2[17]&&($res_n2[17]!=$res_n1[17])) ? '-'.$res_n2[17] : ''),
		'Norm0315' => $res_n1[18].(($res_n2[18]&&($res_n2[18]!=$res_n1[18])) ? '-'.$res_n2[18] : ''),
		'Norm025' => $res_n1[19].(($res_n2[19]&&($res_n2[19]!=$res_n1[19])) ? '-'.$res_n2[19] : ''),
		'Norm016' => $res_n1[20].(($res_n2[20]&&($res_n2[20]!=$res_n1[20])) ? '-'.$res_n2[20] : ''),
		'Norm0125' => $res_n1[21].(($res_n2[21]&&($res_n2[21]!=$res_n1[21])) ? '-'.$res_n2[21] : ''),
		'Norm01' => $res_n1[22].(($res_n2[22]&&($res_n2[22]!=$res_n1[22])) ? '-'.$res_n2[22] : ''),
		'Norm0071' => $res_n1[23].(($res_n2[23]&&($res_n2[23]!=$res_n1[23])) ? '-'.$res_n2[23] : ''),
		'Norm0063' => $res_n1[24].(($res_n2[24]&&($res_n2[24]!=$res_n1[24])) ? '-'.$res_n2[24] : ''),
		'Norm005' => $res_n1[25].(($res_n2[25]&&($res_n2[25]!=$res_n1[25])) ? '-'.$res_n2[25] : ''),
		'Normm005' => $res_n1[26].(($res_n2[26]&&($res_n2[26]!=$res_n1[26])) ? '-'.$res_n2[26] : '')
		];
		$l=10000;
		}
	//$l++;
	}
	}
}
//var_dump($resultszern);

$number_probe = (count($number_probes)>1 ? count($number_probes) . " " . num2word(count($number_probes), ['проба', 'пробы/образца', 'проб/образцов']) . ": " : ""); //. implode(", ", $number_probes);
for($i=0; $i<count($number_probes); $i++)
{
	$number_probe = $number_probe . $number_probes[$i] . " (" . $numbers[$i] . ")" . ((count($number_probes)-(int)$i)==1 ? "." : ", ");
	//echo $numbers[$i]."__";
}

//$number = implode(", ", $numbers);

$j=0;
$GOST_DOC3 = [];
for ($i=0; $i<count($tz[1]['gost']); $i++) {
  //$strGOST=strpos($GOST2[$i], "п"); //отрезаем пункты гостов для протокола
  //$GOST2[$i]=substr($GOST2[$i], 0, $strGOST);
  $years='';
  if($YEARS[$i]) $years='-'.$YEARS[$i];
  $years2='';
  if($YEARS2[$i]) $years2='-'.$YEARS2[$i];
  if($GOST2[$i]!='-')
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
$dogovor_num = ($ba_tz['DOGOVOR_NUM'] ? $ba_tz['DOGOVOR_NUM'] : 'Договор №'.$ID);
$res = $DB->Query("UPDATE `ba_tz` SET `DATE_P` ='" . date("Y-m-d H:i:s") . "' WHERE ID = " . (int)$TZ_ID);
//$res_history = $DB->Query("UPDATE `HISTORY` SET `DATE` ='" . date("Y-m-d H:i:s") . "', `TYPE`='1', `` WHERE ID = " . (int)$TZ_ID);
$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`) VALUES ('" . date('Y-m-d H:i:s') . "','" . $assigned_user . "','" . $ID_P . "','" . $TZ_ID . "','" . $USER->GetID() . "', 'Скачивание протокола')");

        // if ($res->result = 1) {
            // echo "<h3>Успешно</h3>";
        // } else {
            // echo "<h3>Ошибка</h3>";
        // }
// //exit;
//var_dump($ba_tz['DOGOVOR_NUM']);
//var_dump($requis);
//echo $company['UF_CRM_1567958673619'];
//Заполняем данные
$document->normalizeContent();
//$document->setFields(['PNG' => ['TYPE' => 'IMAGE'],]);
$document->setValues([
    'nProtocol' => $ID_P,
    'dProtocol' => ($ba_tz['DATE_P_CORR'] ? date("d.m.Y", strtotime($ba_tz['DATE_P_CORR'])) : date("d.m.Y")),
    'nDogovor' => $dogovor_num,
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

//var_dump($rrr);
$result = $document->process();
$content = $document->getContent();
//$document->save($filename = '1.docx', $storage = null);
$curdate = date("d-m-Y_H-i-s");
$newpath = "archive/".$ID_P."/".$curdate."_".$assigned_user.".docm";

function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );
   
    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );
   
    file_put_contents( $fullPath, $contents, $flags );
}


file_force_contents($newpath, $content);

 // if (CModule::IncludeModule('crm')&&$deal['TYPE_ID']!='COMPLEX') {
        // $deal = new CCrmDeal;
        // $arDealUpdate = array('STAGE_ID' => '2');
        // $resUpd = $deal->Update((int)$_GET['ID'],
            // $arDealUpdate,
            // true,
            // true,
            // array('DISABLE_USER_FIELD_CHECK' => true)
        // );
    // }
// вывод непосредственно в браузер
//echo "ВЕДУТСЯ ТЕХНИЧЕСКИЕ РАБОТЫ! ПОПРОБУЙТЕ СФОРМИРОВАТЬ ПРОТОКОЛ ПОЗЖЕ!";

if($oborud_check||($USER->GetID()=='1')||($USER->GetID()=='10')){
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment;filename="Протокол № ' . $ID_P . ' от ' . date("d.m.Y") . '.docm"');
	header('Cache-Control: max-age=0');
	echo $content; 
}
die;