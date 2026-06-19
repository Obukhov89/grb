<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 26.04.2019
 * Time: 9:38
 */


use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

if(!empty($_GET['metrolog'])){
	$of_metrolog = $_GET['metrolog'];
}
//$file = new Bitrix\Main\IO\File(__DIR__ . '/../PasportResearchCenter.docx');
//$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$fileForm2 = new Bitrix\Main\IO\File(__DIR__ . '/../Form2.docx');
$documentForm2 = new Bitrix\DocumentGenerator\Body\Docx($fileForm2->getContents());

$fileForm3 = new Bitrix\Main\IO\File(__DIR__ . '/../Form3.docx');
$documentForm3 = new Bitrix\DocumentGenerator\Body\Docx($fileForm3->getContents());

$fileForm4 = new Bitrix\Main\IO\File(__DIR__ . '/../Form4.docx');
$documentForm4 = new Bitrix\DocumentGenerator\Body\Docx($fileForm4->getContents());

$fileForm5 = new Bitrix\Main\IO\File(__DIR__ . '/../Form5.docx');
$documentForm5 = new Bitrix\DocumentGenerator\Body\Docx($fileForm5->getContents());

//$fileForm6 = new Bitrix\Main\IO\File(__DIR__ . '/../Form6.docx');
//$documentForm6 = new Bitrix\DocumentGenerator\Body\Docx($fileForm6->getContents());

//Запрашиваем данные

//$res_ob_io = $DB->Query("SELECT * FROM `ba_oborud` WHERE `IDENT`='IO' AND `roomnumber` != '9' AND `roomnumber` != '10' AND `roomnumber` != '11' ORDER BY `roomnumber`");
$res_ob_io = $DB->Query("SELECT bo.*, r.`ID` as room_id, r.`NAME` as room_name, r.`NUMBER` as r_number, bvc.`name` as bvc_name_f,
       					 group_concat(concat(bvc.`name`, ', от ', date_format(bvc.`date_start`, '%d.%m.%Y'), ' до ', date_format(bvc.`date_end`, '%d.%m.%Y')) SEPARATOR ', ' ) as bvc_name,
                         group_concat(concat('<w:hyperlink r:id=',bvc.link_fgis, '><w:r><w:t>',bvc.link_fgis,'</w:t></w:r></w:hyperlink>') SEPARATOR ', ' ) as bvc_link
						 FROM `ba_oborud` as bo
						 left join `ROOMS` as r ON bo.`roomnumber` = r.`ID`
						 left join `ba_oborud_certificate` as bvc ON bvc.`oborud_id` = bo.`ID` and bvc.is_actual = 1
						 WHERE IDENT='IO' 
						 	AND bo.roomnumber != '9' 
						 	AND bo.roomnumber != '10' 
						 	AND bo.roomnumber != '11'						 	 
						 	AND bo.REG_NUM NOT LIKE '%В%'
						 	AND `LONG_STORAGE` = '0'						 	
						 	AND bo.`is_decommissioned` = 0
						 	AND IN_AREA = 1
						 	AND (`DATE_SP` = '' OR `DATE_SP` IS NULL) 
						 group by bo.ID
						 ORDER BY r.`NUMBER` ASC");

$res_ob_si = $DB->Query("SELECT bo.*, r.`ID` as room_id, r.`NAME` as room_name,
							r.`NUMBER` as r_number, bvc.`name` as bvc_name_f,
       						group_concat(concat(bvc.`name`, ', от ', date_format(bvc.`date_start`, '%d.%m.%Y'), ' до ', date_format(bvc.`date_end`, '%d.%m.%Y')) SEPARATOR ', ' ) as bvc_name,
       						group_concat(concat('<w:hyperlink r:id=',bvc.link_fgis, '><w:r><w:t>',bvc.link_fgis,'</w:t></w:r></w:hyperlink>') SEPARATOR ', ' ) as bvc_link
							FROM `ba_oborud` as bo
						        left join `ROOMS` as r ON bo.`roomnumber` = r.`ID`
						        left join `ba_oborud_certificate` as bvc ON bvc.`oborud_id` = bo.`ID` and bvc.is_actual = 1
							WHERE `IDENT`='SI' 
								AND `roomnumber` != '9' 
						 		AND `roomnumber` != '10' 
						 		AND `roomnumber` != '11'
								AND `REG_NUM` NOT LIKE '%В%'
								AND `LONG_STORAGE` = '0'	
								AND bo.`is_decommissioned` = 0
								AND `IN_AREA` = 1 
								AND (`DATE_SP` = '' OR `DATE_SP` IS NULL) 
								group by bo.ID
						 	ORDER BY r.`NUMBER` ASC");
//$res_ob_si = $DB->Query("SELECT bo.`NAME`, bo.`POVERKA`, bo.`roomnumber`, r.`ID` as room_id, r.`NAME` as room_name,
//							r.`NUMBER` as r_number, bvc.`name_f` as bvc_name_f, bo.`certificate_of_verification`,
//							bo.`OBJECT`, bo.`TYPE_OBORUD`,
//							FROM `ba_oborud` as bo, `ROOMS` as r, `b_verification_certificate` as bvc
//							WHERE `IDENT`='SI'
//								AND `roomnumber` != '9'
//						 		AND `roomnumber` != '10'
//						 		AND `roomnumber` != '11'
//								AND bo.`roomnumber` = r.`ID`
//								AND bvc.`id_oborud` = bo.`ID`
//								AND `REG_NUM` NOT LIKE '%В%'
//								AND `LONG_STORAGE` = '0'
//								AND `SPISANIE` = ''
//								AND (`DATE_SP` = '' OR `DATE_SP` IS NULL)
//						 ORDER BY r.`NUMBER` ASC");
$res_ob_vo = $DB->Query("SELECT bo.*, r.`ID` as room_id, r.`NAME` as room_name,
							r.`NUMBER` as r_number, bvc.`name` as bvc_name_f,
							group_concat(concat(bvc.`name`, ', от ', date_format(bvc.`date_start`, '%d.%m.%Y'), ' до ', date_format(bvc.`date_end`, '%d.%m.%Y')) SEPARATOR ', ' ) as bvc_name,
							group_concat(concat('<w:hyperlink r:id=',bvc.link_fgis, '><w:r><w:t>',bvc.link_fgis,'</w:t></w:r></w:hyperlink>') SEPARATOR ', ' ) as bvc_link
							FROM `ba_oborud` as bo
						        left join `ROOMS` as r ON bo.`roomnumber` = r.`ID`
						        left join `ba_oborud_certificate` as bvc ON bvc.`oborud_id` = bo.`ID` and bvc.is_actual = 1
							WHERE `IDENT`='VO' 
								AND `roomnumber` != '9' 
						 		AND `roomnumber` != '10' 
						 		AND `roomnumber` != '11' 
								AND `REG_NUM` NOT LIKE '%В%'
								AND `LONG_STORAGE` = '0'									
								AND bo.`is_decommissioned` = 0
								AND `IN_AREA` = 1
								AND (`DATE_SP` = '' OR `DATE_SP` IS NULL) 
							group by bo.ID		
						 ORDER BY r.`NUMBER` ASC");
$res_rooms = $DB->Query("SELECT * FROM `ROOMS` WHERE `ID` != 9 AND `ID` != 10 AND `ID` != 11 AND `ID` != 13 AND `ID` != 14 AND `ID` != 19 AND `ID` != 20 AND `ID` != 5");
$res_sts = $DB->Query("SELECT * FROM `ST_SAMPLE` WHERE 1");

$si=1;
while($sql = $res_ob_si->Fetch())
{$poverka = strtotime($sql['POVERKA']) - time();
//	if($sql['SPISANIE']=='' && !$sql['LONG_STORAGE']){
//	if(($poverka > 0  || $sql['NO_METR_CONTROL'] != 0)){
	$room='';
	if ($sql['room_id'])
		$room = $sql['room_name'] . ' ' . ($sql['r_number'] ? $sql['r_number']:'');
//	$res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$sql['ID']);
//	$row_certificate = $res_certificate_select->Fetch();
	$certificateName_arr = unserialize($sql['bvc_name_f']);
	$cert_arr=array();

	foreach($certificateName_arr as $cName)
		{
			$cert_arr[]=$cName;
		}
	$ob_v = ($sql['certificate_of_verification'] ? $sql['certificate_of_verification'] : '-');
	$certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v;

$results2[] = [
	'NUM' => $si,
	'CHAR' => $sql['NAME'],
    'Labaratory' => $select['NAME'] ? $select['NAME'] : '',
    'NAME' => $sql['OBJECT'],
	'TYPE' => $sql['TYPE_OBORUD'],
	'GOSREESTR' => $sql['GOSREESTR'],
	'MANUFACTURER' => $sql['manufacturer'],
	'IDENT' => $ident,
	'TECH' => $sql['TECH'],
	'OVV_EX' => "от ".$sql['OVV_EX']." до ".$sql['OVV_EX2'],
	'TOO_EX' => "от ".$sql['TOO_EX']." до ".$sql['TOO_EX2'],
	'AD_EX' => "от ".$sql['AD_EX']." до ".$sql['AD_EX2'],
	'OVV_UH' => $sql['OVV_UH'],
	'TOO_UH' => $sql['TOO_UH'],
	'AD_UH' => $sql['AD_UH'],
	'REG_NUM_ZAV' => $sql['FACTORY_NUMBER'],
	'YEAR' => $sql['YEAR'],
	'EXPLUATATION' => ($sql['god_vvoda_expluatation']!="1970-01-01" ? date("d.m.Y", strtotime($sql['god_vvoda_expluatation'])) : "-") ,
	'REG_NUM' => $sql['REG_NUM'],
	'REG_NUM_RC' => $sql['REG_NUM_RC'],
	'COND' => $cond,
	'PLACE' => $room,
	'METROLOG' => $metr_control,	//$sql['POVERKA'],
	'DOCUMENT' => $sql['DOCUMENT'],
	'PO_NAME' => $sql['PO_NAME'],
	'PO_ID' => $sql['PO_ID'],
	'NAPR' => $sql['NAPR'],
	'FREQ' => $sql['FREQ'],
	'ASSIGNED' => $assigned,
	'DIAP' => $sql['measuring_range'],
	'PREC' => $sql['сlass_precision_and_accuracy'],
	'METR_CONTROL' => $metr_control,
//	'SERT' => $certificateName,
	'SERT' => $sql['bvc_name'],
	'POVERKA_LAST' => ($sql['POVERKA_LAST'] ? date("d.m.Y", strtotime($sql['POVERKA_LAST'])) : "-") ,
	'INTERVAL' => $sql['MC_INTERVAL'],
	'property_rights' => $sql['property_rights'],
//	'property_rights' => 'Инвентаризационная опись №1 от 15.08.2022 г.',
	'NOTE' => $sql['NOTE'],
	'date_now' => date('d.m.Y'),
	'LINK' => $sql['bvc_link']

];
$si++;
//	}
}

$io=1;
while($sql = $res_ob_io->Fetch())
{	$poverka = strtotime($sql['POVERKA']) - time();
//	if($sql['SPISANIE']==''&&!$sql['LONG_STORAGE']){
//	if(($poverka > 0  || $sql['NO_METR_CONTROL'] != 0)){
//	$room='';
//	if($sql['roomnumber'])
//		$res_room = $DB->Query("SELECT * FROM `ROOMS` WHERE `ID`=".$sql['roomnumber'])->Fetch();
//	if ($res_room['ID'])
//		$room = $res_room['NAME'] . ' ' . ($res_room['NUMBER'] ? $res_room['NUMBER']:'');
//	$res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$sql['ID']);
//	$row_certificate = $res_certificate_select->Fetch();
//	$certificateName_arr = unserialize($row_certificate['name_f']);
//	$cert_arr=array();
//
//	foreach($certificateName_arr as $cName)
//		{
//			$cert_arr[]=$cName;
//		}
//	$ob_v = ($sql['certificate_of_verification'] ? $sql['certificate_of_verification'] : '-');
//	$certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v;
	$room='';
	if ($sql['room_id'])
		$room = $sql['room_name'] . ' ' . ($sql['r_number'] ? $sql['r_number']:'');
//	$res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$sql['ID']);
//	$row_certificate = $res_certificate_select->Fetch();
	$certificateName_arr = unserialize($sql['bvc_name_f']);
	$cert_arr=array();

	foreach($certificateName_arr as $cName)
	{
		$cert_arr[]=$cName;
	}
	$ob_v = ($sql['certificate_of_verification'] ? $sql['certificate_of_verification'] : '-');
	$certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v;

$results3[] = [
	'NUM' => $io,
	'CHAR' => $sql['NAME'],
    'Labaratory' => $select['NAME'] ? $select['NAME'] : '',
    'NAME' => $sql['OBJECT'],
	'TYPE' => $sql['TYPE_OBORUD'],
	'GOSREESTR' => $sql['GOSREESTR'],
	'MANUFACTURER' => $sql['manufacturer'],
	'IDENT' => $ident,
	'TECH' => $sql['TECH'],
	'OVV_EX' => "от ".$sql['OVV_EX']." до ".$sql['OVV_EX2'],
	'TOO_EX' => "от ".$sql['TOO_EX']." до ".$sql['TOO_EX2'],
	'AD_EX' => "от ".$sql['AD_EX']." до ".$sql['AD_EX2'],
	'OVV_UH' => $sql['OVV_UH'],
	'TOO_UH' => $sql['TOO_UH'],
	'AD_UH' => $sql['AD_UH'],
	'REG_NUM_ZAV' => $sql['FACTORY_NUMBER'],
	'YEAR' => $sql['YEAR'],
	'EXPLUATATION' => ($sql['god_vvoda_expluatation']!="1970-01-01" ? date("d.m.Y", strtotime($sql['god_vvoda_expluatation'])) : "-") ,
	'REG_NUM' => $sql['REG_NUM'],
	'REG_NUM_RC' => $sql['REG_NUM_RC'],
	'COND' => $cond,
	'PLACE' => $room,
	'METROLOG' => $metr_control,	//$sql['POVERKA'],
	'DOCUMENT' => $sql['DOCUMENT'],
	'PO_NAME' => $sql['PO_NAME'],
	'PO_ID' => $sql['PO_ID'],
	'NAPR' => $sql['NAPR'],
	'FREQ' => $sql['FREQ'],
	'ASSIGNED' => $assigned,
	'DIAP' => $sql['measuring_range'],
	'PREC' => $sql['сlass_precision_and_accuracy'],
	'METR_CONTROL' => $metr_control,
//	'SERT' => $certificateName,
	'SERT' => $sql['bvc_name'],
	'POVERKA_LAST' => ($sql['POVERKA_LAST'] ? date("d.m.Y", strtotime($sql['POVERKA_LAST'])) : "-") ,
	'INTERVAL' => $sql['MC_INTERVAL'],
	'property_rights' => $sql['property_rights'],
//	'property_rights' => 'Инвентаризационная опись №1 от 15.08.2022 г.',
	'NOTE' => $sql['NOTE'],
	'OB_GROUP' => $sql['OBJECT_GROUP'],
	'date_now' => date('d.m.Y'),
];
$io++;
//	}
}

$vo=1;
while($sql = $res_ob_vo->Fetch())
{
	pre($sql);
	$poverka = strtotime($sql['POVERKA']) - time();
////	if($sql['SPISANIE']==''&&!$sql['LONG_STORAGE']){
//	if(($poverka > 0 || $sql['NO_METR_CONTROL'] != 0)){
	$room='';
//	$res_room = ''; //Обнуляем ID
//	if($sql['roomnumber']&&is_numeric($sql['roomnumber']))
//		$res_room = $DB->Query("SELECT * FROM `ROOMS` WHERE `ID`=".$sql['roomnumber'])->Fetch();
//	if ($res_room['ID'])
//		$room = $res_room['NAME'] . ' ' . ($res_room['NUMBER'] ? $res_room['NUMBER']:'');
	$room='';
	if ($sql['room_id'])
		$room = $sql['room_name'] . ' ' . ($sql['r_number'] ? $sql['r_number']:'');
	//Если кабинеты не указаны то не выводим
//	if (empty($room)) {
//		continue;
//	}
	
$results4[] = [
	'NUM' => $vo,
	'CHAR' => $sql['NAME'],
    'Labaratory' => $select['NAME'] ? $select['NAME'] : '',
    'NAME' => $sql['OBJECT'],
	'TYPE' => $sql['TYPE_OBORUD'],
	'GOSREESTR' => $sql['GOSREESTR'],
	'MANUFACTURER' => $sql['manufacturer'],
	'IDENT' => $ident,
	'TECH' => $sql['TECH'],
	'OVV_EX' => "от ".$sql['OVV_EX']." до ".$sql['OVV_EX2'],
	'TOO_EX' => "от ".$sql['TOO_EX']." до ".$sql['TOO_EX2'],
	'AD_EX' => "от ".$sql['AD_EX']." до ".$sql['AD_EX2'],
	'OVV_UH' => $sql['OVV_UH'],
	'TOO_UH' => $sql['TOO_UH'],
	'AD_UH' => $sql['AD_UH'],
	'REG_NUM_ZAV' => $sql['FACTORY_NUMBER'],
	'YEAR' => $sql['YEAR'],
	'EXPLUATATION' => ($sql['god_vvoda_expluatation']!="1970-01-01" ? date("d.m.Y", strtotime($sql['god_vvoda_expluatation'])) : "-") ,
	'REG_NUM' => $sql['REG_NUM'],
	'REG_NUM_RC' => $sql['REG_NUM_RC'],
	'COND' => $cond,
	'PLACE' => $room,
	'METROLOG' => $metr_control,	//$sql['POVERKA'],
	'DOCUMENT' => $sql['DOCUMENT'],
	'PO_NAME' => $sql['PO_NAME'],
	'PO_ID' => $sql['PO_ID'],
	'NAPR' => $sql['NAPR'],
	'FREQ' => $sql['FREQ'],
	'ASSIGNED' => $assigned,
	'DIAP' => $sql['measuring_range'],
	'PREC' => $sql['сlass_precision_and_accuracy'],
	'METR_CONTROL' => $metr_control,
	'SERT' => $sql['bvc_name'],
//	'SERT' => $certificateName,
	'POVERKA_LAST' => ($sql['POVERKA_LAST'] ? date("d.m.Y", strtotime($sql['POVERKA_LAST'])) : "-") ,
	'INTERVAL' => $sql['MC_INTERVAL'],
	'property_rights' => $sql['property_rights'],
//	'property_rights' => 'Инвентаризационная опись №1 от 15.08.2022 г.',
	'NOTE' => $sql['NOTE'],
	'date_now' => date('d.m.Y'),
];
$vo++;
//	}
}
//die;
$ss=1;
while($sql = $res_sts->Fetch())
{

$results5[] = [
	'NUM' => $ss,
	'NAME' => $sql['NAME'],
	'TYPE' => $sql['TYPE'],
	'NUMBER' => $sql['NUMBER'],
	'MANUFACTURER' => $sql['MANUFACTURER'],
	'CATEGORY' => $sql['CATEGORY'],
	'PURPOSE' => $sql['PURPOSE'],
	'MASS_FRACTION' => $sql['MASS_FRACTION'],
	'INACCURACY' => $sql['INACCURACY'],
	'DOP' => $sql['DOP'],
	'NORM_DOC' => $sql['NORM_DOC'],
	'SHELF_LIFE' => $sql['SHELF_LIFE'],
	'DATE' => $sql['DATE'],
	'COMMENT' => $sql['COMMENT'],
	'date_now' => date('d.m.Y'),

];
$ss++;
}

$r=1;
while($sql = $res_rooms->Fetch())
{
//dump($sql);
$results6[] = [
	'NUM' => $r,
	'AREA' => $sql['AREA'],
	'TYPE' => !$sql['SPEC'] ? 'Специальное' : 'Приспособленное',
	'PURPOSE' => $sql['PURPOSE'],
	'PARAMS' => $sql['PARAMS'],
	'EQUIP' => $sql['SPEC_EQUIP'],
	'DOCS' => $sql['DOCS'],
	'COMMENT' => $sql['COMMENT'],
	//-- Вывод даты и вспомогательного оборудования в формах, добавление PLACEMENT --//
	'PLACEMENT' => $sql['PLACEMENT'],
	'date_now' => date('d.m.Y'),
	//-- ./Вывод даты и вспомогательного оборудования в формах, добавление PLACEMENT --//

];
$r++;
}


/*$document->normalizeContent();
$document->setValues([
	 'Results3' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results3,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'Results3ItemOB_ID' => 'Results3.Item.OB_ID',
    'Results3ItemLabaratory' => 'Results3.Item.Labaratory',
	'Results3ItemNAME' => 'Results3.Item.NAME',
	'Results3ItemTYPE' => 'Results3.Item.TYPE',
	'Results3ItemGOSREESTR' => 'Results3.Item.GOSREESTR',
	'Results3ItemMANUFACTURER' => 'Results3.Item.MANUFACTURER',
	'Results3ItemIDENT' => 'Results3.Item.IDENT',
	'Results3ItemTECH' => 'Results3.Item.TECH',
	'Results3ItemOVV_EX' => 'Results3.Item.OVV_EX',
	'Results3ItemTOO_EX' => 'Results3.Item.TOO_EX',
	'Results3ItemAD_EX' => 'Results3.Item.AD_EX',
	'Results3ItemOVV_UH' => 'Results3.Item.OVV_UH',
	'Results3ItemTOO_UH' => 'Results3.Item.TOO_UH',
	'Results3ItemAD_UH' => 'Results3.Item.AD_UH',
	'Results3ItemREG_NUM_ZAV' => 'Results3.Item.REG_NUM_ZAV',
	'Results3ItemYEAR' => 'Results3.Item.YEAR',
	'Results3ItemEXPLUATATION' => 'Results3.Item.EXPLUATATION',
	'Results3ItemREG_NUM' => 'Results3.Item.REG_NUM',
	'Results3ItemREG_NUM_RC' => 'Results3.Item.REG_NUM_RC',
	'Results3ItemCOND' => 'Results3.Item.COND',
	'Results3ItemPLACE' => 'Results3.Item.PLACE',
	'Results3ItemMETROLOG' => 'Results3.Item.METROLOG',
	'Results3ItemDOCUMENT' => 'Results3.Item.DOCUMENT',
	'Results3ItemPO_NAME' => 'Results3.Item.PO_NAME',
	'Results3ItemPO_ID' => 'Results3.Item.PO_ID',
	'Results3ItemNAPR' => 'Results3.Item.NAPR',
	'Results3ItemFREQ' => 'Results3.Item.FREQ',
	'Results3ItemASSIGNED' => 'Results3.Item.ASSIGNED',
	'Results3ItemDIAP' => 'Results3.Item.DIAP',
	'Results3ItemDIAP' => 'Results3.Item.DIAP',
	'Results3ItemPREC' => 'Results3.Item.PREC',
	'Results3ItemMETR_CONTROL' => 'Results3.Item.METR_CONTROL',
	'Results3ItemSERT' => 'Results3.Item.SERT',
	'Results3ItemPOVERKA_LAST' => 'Results3.Item.POVERKA_LAST',
	'Results3ItemINTERVAL' => 'Results3.Item.INTERVAL',
	'Results3ItemNOTE' => 'Results3.Item.NOTE',
	'Results3ItemCHAR' => 'Results3.Item.CHAR',
	'Results3ItemNUM' => 'Results3.Item.NUM',
	'Results3Itemproperty_rights' => 'Results3.Item.property_rights',
	'Results3ItemOB_GROUP' => 'Results3.Item.OB_GROUP',

	 'Results2' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results2,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'Results2ItemOB_ID' => 'Results2.Item.OB_ID',
    'Results2ItemLabaratory' => 'Results2.Item.Labaratory',
	'Results2ItemNAME' => 'Results2.Item.NAME',
	'Results2ItemTYPE' => 'Results2.Item.TYPE',
	'Results2ItemGOSREESTR' => 'Results2.Item.GOSREESTR',
	'Results2ItemMANUFACTURER' => 'Results2.Item.MANUFACTURER',
	'Results2ItemIDENT' => 'Results2.Item.IDENT',
	'Results2ItemTECH' => 'Results2.Item.TECH',
	'Results2ItemOVV_EX' => 'Results2.Item.OVV_EX',
	'Results2ItemTOO_EX' => 'Results2.Item.TOO_EX',
	'Results2ItemAD_EX' => 'Results2.Item.AD_EX',
	'Results2ItemOVV_UH' => 'Results2.Item.OVV_UH',
	'Results2ItemTOO_UH' => 'Results2.Item.TOO_UH',
	'Results2ItemAD_UH' => 'Results2.Item.AD_UH',
	'Results2ItemREG_NUM_ZAV' => 'Results2.Item.REG_NUM_ZAV',
	'Results2ItemYEAR' => 'Results2.Item.YEAR',
	'Results2ItemEXPLUATATION' => 'Results2.Item.EXPLUATATION',
	'Results2ItemREG_NUM' => 'Results2.Item.REG_NUM',
	'Results2ItemREG_NUM_RC' => 'Results2.Item.REG_NUM_RC',
	'Results2ItemCOND' => 'Results2.Item.COND',
	'Results2ItemPLACE' => 'Results2.Item.PLACE',
	'Results2ItemMETROLOG' => 'Results2.Item.METROLOG',
	'Results2ItemDOCUMENT' => 'Results2.Item.DOCUMENT',
	'Results2ItemPO_NAME' => 'Results2.Item.PO_NAME',
	'Results2ItemPO_ID' => 'Results2.Item.PO_ID',
	'Results2ItemNAPR' => 'Results2.Item.NAPR',
	'Results2ItemFREQ' => 'Results2.Item.FREQ',
	'Results2ItemASSIGNED' => 'Results2.Item.ASSIGNED',
	'Results2ItemDIAP' => 'Results2.Item.DIAP',
	'Results2ItemDIAP' => 'Results2.Item.DIAP',
	'Results2ItemPREC' => 'Results2.Item.PREC',
	'Results2ItemMETR_CONTROL' => 'Results2.Item.METR_CONTROL',
	'Results2ItemSERT' => 'Results2.Item.SERT',
	'Results2ItemPOVERKA_LAST' => 'Results2.Item.POVERKA_LAST',
	'Results2ItemINTERVAL' => 'Results2.Item.INTERVAL',
	'Results2ItemNOTE' => 'Results2.Item.NOTE',
	'Results2ItemCHAR' => 'Results2.Item.CHAR',
	'Results2ItemNUM' => 'Results2.Item.NUM',
	'Results2Itemproperty_rights' => 'Results2.Item.property_rights',

	 'Results4' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results4,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'Results4ItemOB_ID' => 'Results4.Item.OB_ID',
    'Results4ItemLabaratory' => 'Results4.Item.Labaratory',
	'Results4ItemNAME' => 'Results4.Item.NAME',
	'Results4ItemTYPE' => 'Results4.Item.TYPE',
	'Results4ItemGOSREESTR' => 'Results4.Item.GOSREESTR',
	'Results4ItemMANUFACTURER' => 'Results4.Item.MANUFACTURER',
	'Results4ItemIDENT' => 'Results4.Item.IDENT',
	'Results4ItemTECH' => 'Results4.Item.TECH',
	'Results4ItemOVV_EX' => 'Results4.Item.OVV_EX',
	'Results4ItemTOO_EX' => 'Results4.Item.TOO_EX',
	'Results4ItemAD_EX' => 'Results4.Item.AD_EX',
	'Results4ItemOVV_UH' => 'Results4.Item.OVV_UH',
	'Results4ItemTOO_UH' => 'Results4.Item.TOO_UH',
	'Results4ItemAD_UH' => 'Results4.Item.AD_UH',
	'Results4ItemREG_NUM_ZAV' => 'Results4.Item.REG_NUM_ZAV',
	'Results4ItemYEAR' => 'Results4.Item.YEAR',
	'Results4ItemEXPLUATATION' => 'Results4.Item.EXPLUATATION',
	'Results4ItemREG_NUM' => 'Results4.Item.REG_NUM',
	'Results4ItemREG_NUM_RC' => 'Results4.Item.REG_NUM_RC',
	'Results4ItemCOND' => 'Results4.Item.COND',
	'Results4ItemPLACE' => 'Results4.Item.PLACE',
	'Results4ItemMETROLOG' => 'Results4.Item.METROLOG',
	'Results4ItemDOCUMENT' => 'Results4.Item.DOCUMENT',
	'Results4ItemPO_NAME' => 'Results4.Item.PO_NAME',
	'Results4ItemPO_ID' => 'Results4.Item.PO_ID',
	'Results4ItemNAPR' => 'Results4.Item.NAPR',
	'Results4ItemFREQ' => 'Results4.Item.FREQ',
	'Results4ItemASSIGNED' => 'Results4.Item.ASSIGNED',
	'Results4ItemDIAP' => 'Results4.Item.DIAP',
	'Results4ItemDIAP' => 'Results4.Item.DIAP',
	'Results4ItemPREC' => 'Results4.Item.PREC',
	'Results4ItemMETR_CONTROL' => 'Results4.Item.METR_CONTROL',
	'Results4ItemSERT' => 'Results4.Item.SERT',
	'Results4ItemPOVERKA_LAST' => 'Results4.Item.POVERKA_LAST',
	'Results4ItemINTERVAL' => 'Results4.Item.INTERVAL',
	'Results4ItemNOTE' => 'Results4.Item.NOTE',
	'Results4ItemCHAR' => 'Results4.Item.CHAR',
	'Results4ItemNUM' => 'Results4.Item.NUM',
	'Results4Itemproperty_rights' => 'Results4.Item.property_rights',

	'Results5' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results5,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'Results5ItemNUM' => 'Results5.Item.NUM',
    'Results5ItemCATEGORY' => 'Results5.Item.CATEGORY',
	'Results5ItemNAME' => 'Results5.Item.NAME',
	'Results5ItemTYPE' => 'Results5.Item.TYPE',
	'Results5ItemNUMBER' => 'Results5.Item.NUMBER',
	'Results5ItemMANUFACTURER' => 'Results5.Item.MANUFACTURER',
	'Results5ItemPURPOSE' => 'Results5.Item.PURPOSE',
	'Results5ItemMASS_FRACTION' => 'Results5.Item.MASS_FRACTION',
	'Results5ItemINACCURACY' => 'Results5.Item.INACCURACY',
	'Results5ItemDOP' => 'Results5.Item.DOP',
	'Results5ItemNORM_DOC' => 'Results5.Item.NORM_DOC',
	'Results5ItemSHELF_LIFE' => 'Results5.Item.SHELF_LIFE',
	'Results5ItemDATE' => 'Results5.Item.DATE',
	'Results5ItemCOMMENT' => 'Results5.Item.COMMENT',

	'Results6' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results6,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'Results6ItemNUM' => 'Results6.Item.NUM',
    'Results6ItemAREA' => 'Results6.Item.AREA',
	'Results6ItemPARAMS' => 'Results6.Item.PARAMS',
	'Results6ItemTYPE' => 'Results6.Item.TYPE',
	'Results6ItemPURPOSE' => 'Results6.Item.PURPOSE',
	'Results6ItemDOCS' => 'Results6.Item.DOCS',
	'Results6ItemEQUIP' => 'Results6.Item.EQUIP',
	'Results6ItemCOMMENT' => 'Results6.Item.COMMENT'

]);

//dump($results6);

$result = $document->process();
$content = $document->getContent();
// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Паспорт ИЦ.docx"');
header('Cache-Control: max-age=0');
echo $content;*/

function file_force_contents( $fullPath, $contents, $flags = 0 ){
	$parts = explode( '/', $fullPath );
	array_pop( $parts );
	$dir = implode( '/', $parts );

	if( !is_dir( $dir ) )
		mkdir( $dir, 0777, true );

	file_put_contents( $fullPath, $contents, $flags );
}

//$arrayDodument = [$documentForm2, $documentForm3, $documentForm4, $documentForm5, $documentForm6];
$arrayDodument = [$documentForm2, $documentForm3, $documentForm4, $documentForm5];

foreach ($arrayDodument as $key => $document) {
	pre($key);
	$document->normalizeContent();
	$document->setValues([
		 'Results3' => new DocumentGenerator\DataProvider\ArrayDataProvider(
			$results3,
			[
				'ITEM_NAME' => 'Item',
				'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
			]
		),
		'Results3ItemOB_ID' => 'Results3.Item.OB_ID',
		'Results3ItemLabaratory' => 'Results3.Item.Labaratory',
		'Results3ItemNAME' => 'Results3.Item.NAME',
		'Results3ItemTYPE' => 'Results3.Item.TYPE',
		'Results3ItemGOSREESTR' => 'Results3.Item.GOSREESTR',
		'Results3ItemMANUFACTURER' => 'Results3.Item.MANUFACTURER',
		'Results3ItemIDENT' => 'Results3.Item.IDENT',
		'Results3ItemTECH' => 'Results3.Item.TECH',
		'Results3ItemOVV_EX' => 'Results3.Item.OVV_EX',
		'Results3ItemTOO_EX' => 'Results3.Item.TOO_EX',
		'Results3ItemAD_EX' => 'Results3.Item.AD_EX',
		'Results3ItemOVV_UH' => 'Results3.Item.OVV_UH',
		'Results3ItemTOO_UH' => 'Results3.Item.TOO_UH',
		'Results3ItemAD_UH' => 'Results3.Item.AD_UH',
		'Results3ItemREG_NUM_ZAV' => 'Results3.Item.REG_NUM_ZAV',
		'Results3ItemYEAR' => 'Results3.Item.YEAR',
		'Results3ItemEXPLUATATION' => 'Results3.Item.EXPLUATATION',
		'Results3ItemREG_NUM' => 'Results3.Item.REG_NUM',
		'Results3ItemREG_NUM_RC' => 'Results3.Item.REG_NUM_RC',
		'Results3ItemCOND' => 'Results3.Item.COND',
		'Results3ItemPLACE' => 'Results3.Item.PLACE',
		'Results3ItemMETROLOG' => 'Results3.Item.METROLOG',
		'Results3ItemDOCUMENT' => 'Results3.Item.DOCUMENT',
		'Results3ItemPO_NAME' => 'Results3.Item.PO_NAME',
		'Results3ItemPO_ID' => 'Results3.Item.PO_ID',
		'Results3ItemNAPR' => 'Results3.Item.NAPR',
		'Results3ItemFREQ' => 'Results3.Item.FREQ',
		'Results3ItemASSIGNED' => 'Results3.Item.ASSIGNED',
		'Results3ItemDIAP' => 'Results3.Item.DIAP',
		'Results3ItemDIAP' => 'Results3.Item.DIAP',
		'Results3ItemPREC' => 'Results3.Item.PREC',
		'Results3ItemMETR_CONTROL' => 'Results3.Item.METR_CONTROL',
		'Results3ItemSERT' => 'Results3.Item.SERT',
		'Results3ItemPOVERKA_LAST' => 'Results3.Item.POVERKA_LAST',
		'Results3ItemINTERVAL' => 'Results3.Item.INTERVAL',
		'Results3ItemNOTE' => 'Results3.Item.NOTE',
		'Results3ItemCHAR' => 'Results3.Item.CHAR',
		'Results3ItemNUM' => 'Results3.Item.NUM',
		'Results3Itemproperty_rights' => 'Results3.Item.property_rights',
		'Results3ItemOB_GROUP' => 'Results3.Item.OB_GROUP',

		 'Results2' => new DocumentGenerator\DataProvider\ArrayDataProvider(
			$results2,
			[
				'ITEM_NAME' => 'Item',
				'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
			]
		),
		'Results2ItemOB_ID' => 'Results2.Item.OB_ID',
		'Results2ItemLabaratory' => 'Results2.Item.Labaratory',
		'Results2ItemNAME' => 'Results2.Item.NAME',
		'Results2ItemTYPE' => 'Results2.Item.TYPE',
		'Results2ItemGOSREESTR' => 'Results2.Item.GOSREESTR',
		'Results2ItemMANUFACTURER' => 'Results2.Item.MANUFACTURER',
		'Results2ItemIDENT' => 'Results2.Item.IDENT',
		'Results2ItemTECH' => 'Results2.Item.TECH',
		'Results2ItemOVV_EX' => 'Results2.Item.OVV_EX',
		'Results2ItemTOO_EX' => 'Results2.Item.TOO_EX',
		'Results2ItemAD_EX' => 'Results2.Item.AD_EX',
		'Results2ItemOVV_UH' => 'Results2.Item.OVV_UH',
		'Results2ItemTOO_UH' => 'Results2.Item.TOO_UH',
		'Results2ItemAD_UH' => 'Results2.Item.AD_UH',
		'Results2ItemREG_NUM_ZAV' => 'Results2.Item.REG_NUM_ZAV',
		'Results2ItemYEAR' => 'Results2.Item.YEAR',
		'Results2ItemEXPLUATATION' => 'Results2.Item.EXPLUATATION',
		'Results2ItemREG_NUM' => 'Results2.Item.REG_NUM',
		'Results2ItemREG_NUM_RC' => 'Results2.Item.REG_NUM_RC',
		'Results2ItemCOND' => 'Results2.Item.COND',
		'Results2ItemPLACE' => 'Results2.Item.PLACE',
		'Results2ItemMETROLOG' => 'Results2.Item.METROLOG',
		'Results2ItemDOCUMENT' => 'Results2.Item.DOCUMENT',
		'Results2ItemPO_NAME' => 'Results2.Item.PO_NAME',
		'Results2ItemPO_ID' => 'Results2.Item.PO_ID',
		'Results2ItemNAPR' => 'Results2.Item.NAPR',
		'Results2ItemFREQ' => 'Results2.Item.FREQ',
		'Results2ItemASSIGNED' => 'Results2.Item.ASSIGNED',
		'Results2ItemLINK' => 'Results2.Item.LINK',
		'Results2ItemDIAP' => 'Results2.Item.DIAP',
		'Results2ItemDIAP' => 'Results2.Item.DIAP',
		'Results2ItemPREC' => 'Results2.Item.PREC',
		'Results2ItemMETR_CONTROL' => 'Results2.Item.METR_CONTROL',
		'Results2ItemSERT' => 'Results2.Item.SERT',
		'Results2ItemPOVERKA_LAST' => 'Results2.Item.POVERKA_LAST',
		'Results2ItemINTERVAL' => 'Results2.Item.INTERVAL',
		'Results2ItemNOTE' => 'Results2.Item.NOTE',
		'Results2ItemCHAR' => 'Results2.Item.CHAR',
		'Results2ItemNUM' => 'Results2.Item.NUM',
		'Results2Itemproperty_rights' => 'Results2.Item.property_rights',

		 'Results4' => new DocumentGenerator\DataProvider\ArrayDataProvider(
			$results4,
			[
				'ITEM_NAME' => 'Item',
				'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
			]
		),
		'Results4ItemOB_ID' => 'Results4.Item.OB_ID',
		'Results4ItemLabaratory' => 'Results4.Item.Labaratory',
		'Results4ItemNAME' => 'Results4.Item.NAME',
		'Results4ItemTYPE' => 'Results4.Item.TYPE',
		'Results4ItemGOSREESTR' => 'Results4.Item.GOSREESTR',
		'Results4ItemMANUFACTURER' => 'Results4.Item.MANUFACTURER',
		'Results4ItemIDENT' => 'Results4.Item.IDENT',
		'Results4ItemTECH' => 'Results4.Item.TECH',
		'Results4ItemOVV_EX' => 'Results4.Item.OVV_EX',
		'Results4ItemTOO_EX' => 'Results4.Item.TOO_EX',
		'Results4ItemAD_EX' => 'Results4.Item.AD_EX',
		'Results4ItemOVV_UH' => 'Results4.Item.OVV_UH',
		'Results4ItemTOO_UH' => 'Results4.Item.TOO_UH',
		'Results4ItemAD_UH' => 'Results4.Item.AD_UH',
		'Results4ItemREG_NUM_ZAV' => 'Results4.Item.REG_NUM_ZAV',
		'Results4ItemYEAR' => 'Results4.Item.YEAR',
		'Results4ItemEXPLUATATION' => 'Results4.Item.EXPLUATATION',
		'Results4ItemREG_NUM' => 'Results4.Item.REG_NUM',
		'Results4ItemREG_NUM_RC' => 'Results4.Item.REG_NUM_RC',
		'Results4ItemCOND' => 'Results4.Item.COND',
		'Results4ItemPLACE' => 'Results4.Item.PLACE',
		'Results4ItemMETROLOG' => 'Results4.Item.METROLOG',
		'Results4ItemDOCUMENT' => 'Results4.Item.DOCUMENT',
		'Results4ItemPO_NAME' => 'Results4.Item.PO_NAME',
		'Results4ItemPO_ID' => 'Results4.Item.PO_ID',
		'Results4ItemNAPR' => 'Results4.Item.NAPR',
		'Results4ItemFREQ' => 'Results4.Item.FREQ',
		'Results4ItemASSIGNED' => 'Results4.Item.ASSIGNED',
		'Results4ItemDIAP' => 'Results4.Item.DIAP',
		'Results4ItemDIAP' => 'Results4.Item.DIAP',
		'Results4ItemPREC' => 'Results4.Item.PREC',
		'Results4ItemMETR_CONTROL' => 'Results4.Item.METR_CONTROL',
		'Results4ItemSERT' => 'Results4.Item.SERT',
		'Results4ItemPOVERKA_LAST' => 'Results4.Item.POVERKA_LAST',
		'Results4ItemINTERVAL' => 'Results4.Item.INTERVAL',
		'Results4ItemNOTE' => 'Results4.Item.NOTE',
		'Results4ItemCHAR' => 'Results4.Item.CHAR',
		'Results4ItemNUM' => 'Results4.Item.NUM',
		'Results4Itemproperty_rights' => 'Results4.Item.property_rights',

		'Results5' => new DocumentGenerator\DataProvider\ArrayDataProvider(
			$results5,
			[
				'ITEM_NAME' => 'Item',
				'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
			]
		),
		'Results5ItemNUM' => 'Results5.Item.NUM',
		'Results5ItemCATEGORY' => 'Results5.Item.CATEGORY',
		'Results5ItemNAME' => 'Results5.Item.NAME',
		'Results5ItemTYPE' => 'Results5.Item.TYPE',
		'Results5ItemNUMBER' => 'Results5.Item.NUMBER',
		'Results5ItemMANUFACTURER' => 'Results5.Item.MANUFACTURER',
		'Results5ItemPURPOSE' => 'Results5.Item.PURPOSE',
		'Results5ItemMASS_FRACTION' => 'Results5.Item.MASS_FRACTION',
		'Results5ItemINACCURACY' => 'Results5.Item.INACCURACY',
		'Results5ItemDOP' => 'Results5.Item.DOP',
		'Results5ItemNORM_DOC' => 'Results5.Item.NORM_DOC',
		'Results5ItemSHELF_LIFE' => 'Results5.Item.SHELF_LIFE',
		'Results5ItemDATE' => 'Results5.Item.DATE',
		'Results5ItemCOMMENT' => 'Results5.Item.COMMENT',

//		'Results6' => new DocumentGenerator\DataProvider\ArrayDataProvider(
//			$results6,
//			[
//				'ITEM_NAME' => 'Item',
//				'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
//			]
//		),
//		'Results6ItemNUM' => 'Results6.Item.NUM',
//		'Results6ItemAREA' => 'Results6.Item.AREA',
//		'Results6ItemPARAMS' => 'Results6.Item.PARAMS',
//		'Results6ItemTYPE' => 'Results6.Item.TYPE',
//		'Results6ItemPURPOSE' => 'Results6.Item.PURPOSE',
//		'Results6ItemDOCS' => 'Results6.Item.DOCS',
//		'Results6ItemEQUIP' => 'Results6.Item.EQUIP',
//		'Results6ItemCOMMENT' => 'Results6.Item.COMMENT',
//		//-- Вывод даты и вспомогательного оборудования в формах, добавление PLACEMENT --//
//		'Results6ItemPLACEMENT' => 'Results6.Item.PLACEMENT'
//		//-- ./Вывод даты и вспомогательного оборудования в формах, добавление PLACEMENT --//

	]);

	//dump($results6);

	$result = $document->process();
	$content = $document->getContent();

	$newpath = "archive_passport_rc/Паспорт ИЦ №" . ($key + 2) . ".docx";
	// $newpath = "archive_passport_rc/Паспорт ИЦ №" . ($key + 1) . ".docx";

	file_force_contents($newpath, $content);
}

$error = "";

$file_folder = "archive_passport_rc/"; // папка с файлами

if(extension_loaded('zip'))
{

	// проверяем выбранные файлы
	$zip = new ZipArchive(); // подгружаем библиотеку zip
	$zip_name = time().".zip"; // имя файла

	if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE)
	{

		$error .= "* К сожалению, создание ZIP-архива в данный момент не удалось";
	}

	//Получить список файлов и каталогов, расположенных по указанному пути
	$dirFile    = 'archive_passport_rc/';
	$filesPasspotsRc = scandir($dirFile);

	foreach($filesPasspotsRc as $file)
	{

		if ($file == '.' || $file == '..') {
			continue;
		}

		if($of_metrolog){
			if($file == 'Паспорт ИЦ №6.docx'){
				continue;
			}
		}

		$zip->addFile($file_folder.$file); // добавляем файлы в zip архив
	}
	$zip->close();

	if(file_exists($zip_name))
	{
		// отдаём файл на скачивание
		header('Content-type: application/zip');
		header('Content-Disposition: attachment; filename="'.$zip_name.'"');
		readfile($zip_name);
		// удаляем zip файл если он существует
		unlink($zip_name);
	}

} else {
	$error .= "* У вас нет расширения ZIP";
}

die;
