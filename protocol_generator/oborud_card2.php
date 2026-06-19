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

$file = new Bitrix\Main\IO\File(__DIR__ . '/../oborud_card2.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

//Запрашиваем данные
$room='';
$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE 1");
$i=0;
while($sql = $res->Fetch())
{
//if($i>10) break;
if($sql['place_of_installation_or_storage']) $select = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID`=" . $sql['place_of_installation_or_storage'])->Fetch();
//if($sql['roomnumber']) $res_room = $DB->Query("SELECT * FROM `ROOMS` WHERE `ID`=".$sql['roomnumber'])->Fetch();
//if ($res_room['NUMBER']) $room = $res_room['NAME'] . ' ' . ($res_room['NUMBER'] ? $res_room['NUMBER']:'');
$room='';
$res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$sql['ID']);
$row_certificate = $res_certificate_select->Fetch();
$certificateName_arr = unserialize($row_certificate['name_f']);
$cert_arr=array();
//var_dump($cert_arr);
foreach($certificateName_arr as $cName)
	{
		$cert_arr[]=$cName;
	}
$ob_v = ($sql['certificate_of_verification'] ? $sql['certificate_of_verification'] : '-');
$certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v;


//$tz = unserialize($sql['TZ']);//[$_GET['mater']];
//$probe = unserialize($sql['PROBE']);//[$_GET['mater']];
//var_dump($sql);
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
//echo $cond;
//Заполняем данные
$oborud[] = [
 'OB_ID' => $sql['card_id'],
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
	'SERT' => $certificateName,
	'POVERKA_LAST' => ($sql['POVERKA_LAST'] ? date("d.m.Y", strtotime($sql['POVERKA_LAST'])) : "-") ,
	'INTERVAL' => $sql['MC_INTERVAL']
];
$i++;
}

$document->normalizeContent();
$document->setValues([
	 'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $oborud,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'TableItemOB_ID' => 'Table.Item.OB_ID',
    'TableItemLabaratory' => 'Table.Item.Labaratory',
	'TableItemNAME' => 'Table.Item.NAME',
	'TableItemTYPE' => 'Table.Item.TYPE',
	'TableItemGOSREESTR' => 'Table.Item.GOSREESTR',
	'TableItemMANUFACTURER' => 'Table.Item.MANUFACTURER',
	'TableItemIDENT' => 'Table.Item.IDENT',
	'TableItemTECH' => 'Table.Item.TECH',
	'TableItemOVV_EX' => 'Table.Item.OVV_EX',
	'TableItemTOO_EX' => 'Table.Item.TOO_EX',
	'TableItemAD_EX' => 'Table.Item.AD_EX',
	'TableItemOVV_UH' => 'Table.Item.OVV_UH',
	'TableItemTOO_UH' => 'Table.Item.TOO_UH',
	'TableItemAD_UH' => 'Table.Item.AD_UH',
	'TableItemREG_NUM_ZAV' => 'Table.Item.REG_NUM_ZAV',
	'TableItemYEAR' => 'Table.Item.YEAR',
	'TableItemEXPLUATATION' => 'Table.Item.EXPLUATATION',
	'TableItemREG_NUM' => 'Table.Item.REG_NUM',
	'TableItemREG_NUM_RC' => 'Table.Item.REG_NUM_RC',
	'TableItemCOND' => 'Table.Item.COND',
	'TableItemPLACE' => 'Table.Item.PLACE',
	'TableItemMETROLOG' => 'Table.Item.METROLOG',
	'TableItemDOCUMENT' => 'Table.Item.DOCUMENT',
	'TableItemPO_NAME' => 'Table.Item.PO_NAME',
	'TableItemPO_ID' => 'Table.Item.PO_ID',
	'TableItemNAPR' => 'Table.Item.NAPR',
	'TableItemFREQ' => 'Table.Item.FREQ',
	'TableItemASSIGNED' => 'Table.Item.ASSIGNED',
	'TableItemDIAP' => 'Table.Item.DIAP',
	'TableItemDIAP' => 'Table.Item.DIAP',
	'TableItemPREC' => 'Table.Item.PREC',
	'TableItemMETR_CONTROL' => 'Table.Item.METR_CONTROL',
	'TableItemSERT' => 'Table.Item.SERT',
	'TableItemPOVERKA_LAST' => 'Table.Item.POVERKA_LAST',
	'TableItemINTERVAL' => 'Table.Item.INTERVAL'
      
]);

$result = $document->process();
$content = $document->getContent();
// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Карточки оборудования.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;