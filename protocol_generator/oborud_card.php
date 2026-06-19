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


$file = new Bitrix\Main\IO\File(__DIR__ . '/../oborud_card.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

//Запрашиваем данные
$room='';
$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . (int)$_GET['OB_ID']);
$nameRoom='';
$sql = $res->Fetch();
if($sql['place_of_installation_or_storage'])
{
	$select = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID`=" . $sql['place_of_installation_or_storage'])->Fetch();
	$nameRoom = $sql['place_of_installation_or_storage']!=7 ? $select['NAME'] : "Отдел СМК и метрологии";
}
if($sql['roomnumber']) $res_room = $DB->Query("SELECT * FROM `ROOMS` WHERE `ID`=".$sql['roomnumber'])->Fetch();
if ($res_room['ID'])
	$room = $res_room['NAME'] . ' ' . ($res_room['NUMBER'] ? $res_room['NUMBER']:'');
$res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$sql['ID']);
$row_certificate = $res_certificate_select->Fetch();
$certificateName_arr = unserialize($row_certificate['name_f']);
// получаем дату старта поверки с 2020
$dateStartCertificate = unserialize($row_certificate['date_start_verification']);

$poverka_arr = [];
// перебираем даты
foreach ($dateStartCertificate as $k_key => $v_val) {
	if(!empty($v_val)){
		$tmp_date = new DateTime($v_val);
		$tmp_date_f = $tmp_date->format('d.m.Y');
	// год 2019 и ниже
		if($tmp_date->format('Y') < '2020') continue;
	}else{
		$tmp_date_f = ' - ';
	}

	$poverka_arr[$k_key] = [
		'StartVer' => $tmp_date_f,
	];
}

// перебираем сертификаты поверки
foreach($certificateName_arr as $k_key1 => $cName){
	$cert_arr[] = $cName;
	if(!empty($poverka_arr[$k_key1]['StartVer'])){
		$poverka_arr[$k_key1]['Cert'] = $cName;
	}
}

$ob_v = ($sql['certificate_of_verification'] ? $sql['certificate_of_verification'] : '-');
// $certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v;
$ttmp = ($sql['POVERKA_LAST'] ? date("d.m.Y", strtotime($sql['POVERKA_LAST'])) : "-");
// $certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v; // выводит предпоследний
// $certificateName = $cert_arr[0] ? $cert_arr[0] : $ob_v; // выводит первый

if($ttmp != '-'){
	$tmp_date1 = new DateTime($ttmp);
	$key_certif = array_search($tmp_date1->format('Y-m-d'), $dateStartCertificate);
}

if(!empty($key_certif)) {
	$certificateName = $cert_arr[$key_certif] ? $cert_arr[$key_certif] : $ob_v; // выводит по индексу акиуального
}else{
	$certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v; // выводит предпоследний
}

// смотрим есть ли в массиве такие значения, которые мы положили первыми, то есть изначальными
// $keys = array_column($poverka_arr, 'StartVer');
// $found_key = array_search($ttmp, $keys);
// $key = array_search($ttmp, array_column($poverka_arr, 'StartVer', 'id'));
$found = array_search($ttmp, array_map(function($val){return $val['StartVer'];}, $poverka_arr));
// если нашли текущий в массиве, то не вставляем
if($found !== false) unset($poverka_arr[$found]);

//$tz = unserialize($sql['TZ']);//[$_GET['mater']];
//$probe = unserialize($sql['PROBE']);//[$_GET['mater']];
var_dump($sql);
//$companys = CCrmCompany::GetList();
$ident='';
switch ($sql['IDENT']) {
	case 'IO':
		$ident='ИО';
		break;
	case 'VO':
		$ident='ВО';
		break;
	case 'TS':
		$ident='ТС';
		break;
	case 'SI':
		$ident='СИ';
		break;
	case 'SO':
		$ident='СО';
		break;
	case 'REACT':
		$ident='реактив';
		break;
	case 'OOPP':
		$ident='ООПП';
		break;
}

// если си или ио, то выводим наш poverka_arr
// иначе удаляем весь массив
// тогда ничего не выведется
if($ident == 'СИ' || $ident == 'ИО') {
}else{
	$poverka_arr = [];
}

switch ($sql['METR_CONTROL']) {
	case 'CALIBR':
		$metr_control='Калибровка';
		break;
	case 'ATTESTATION':
		$metr_control='Аттестация';
		break;
	case 'POVERKA':
		$metr_control='Поверка';
		break;
	case 'TECH_CHAR':
		$metr_control='Проверка тех. харктеристик';
		break;
}


$assign_id = CUser::GetByID($sql['ID_ASSIGN1'])->Fetch();
$assigned="";
if($assign_id['ID']) $assigned = $assign_id['NAME']." ".$assign_id['LAST_NAME'];
$cond="";
if ($sql['COND']=="NEW") $cond="Новый"; else $cond="Бывший в употреблении";

$expluatation = '';
if($sql['IDENT']=='VO')
	$expluatation = ($sql['god_vvoda_expluatation']!="1970-01-01" ? date("Y", strtotime($sql['god_vvoda_expluatation'])) : "-");
else
	$expluatation = ($sql['god_vvoda_expluatation']!="1970-01-01" ? date("d.m.Y", strtotime($sql['god_vvoda_expluatation'])) : "-");

//echo $cond;
//Заполняем данные
$document->normalizeContent();
$document->setValues([
	'OB_ID' => $sql['card_id'],
	'Labaratory' => $nameRoom!='' ? $nameRoom : '',
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
	'EXPLUATATION' => $expluatation,
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
	'INTERVAL' => $sql['MC_INTERVAL'],

	'SERT' => $certificateName,
	'POVERKA_LAST' => ($sql['POVERKA_LAST'] ? date("d.m.Y", strtotime($sql['POVERKA_LAST'])) : "-") ,

	// поверка берётся не текущая, а все начиная с 2020 из новой таблицы b_verification_certificate
	// берётся дата начала (date_start_verification) и сертификат name_f
	// все поля сериализованные, поэтому в генераторе проходимся через Table ArrayDataprovider
	'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
		$poverka_arr,
		[
			'ITEM_NAME' => 'Item',
			'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
		]),
		'TableItemStartVer' => 'Table.Item.StartVer',
		'TableItemCert' => 'Table.Item.Cert',

]);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Карточка оборудования №'.$sql['ID'].'.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;
