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

function dump($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}
$ba_gost=[];
$i=1;
$res_oaid=array('0');
$res_result=array();
$res_month=array();
$res_year=array();
$type_control='';
$type_oborud='';
$doc_name='';
$year=$_GET['YEAR'];
switch($_GET['TYPE']) {
							case '1':
								$type_control='POVERKA';
								$type_oborud='SI';
								$doc_name='Поверки средств измерений';
								break;
							case '2':
								$type_control='CALIBR';
								$type_oborud='SI';
								$doc_name='Калибровки средств измерений';
								break;
							case '3':
								$type_control='ATTESTATION';
								$type_oborud='IO';
								$doc_name='Аттестации испытательного оборудования';
								break;
							case '4':
								$type_control='TECH_CHAR';
								$type_oborud='TS';
								$doc_name='Проведения измерений значений нормированных параметров технических средств (вспомогательное оборудование)';
								break;

					}
global $DB;
//$res = $DB->Query("SELECT * FROM `ba_oborud` where `IDENT`='".$type_oborud."' AND `METR_CONTROL`='".$type_control."'");
$res = $DB->Query("SELECT * FROM `ba_oborud` where `IDENT`='".$type_oborud."' AND `METR_CONTROL`='".$type_control."' AND `roomnumber` != '9' AND `roomnumber` != '10' AND `roomnumber` != '11' AND (LONG_STORAGE = 0 or LONG_STORAGE is NULL) AND (SPISANIE = '' or SPISANIE is null) AND `REG_NUM` NOT LIKE '%В%'");
$k=1;

while ($res1 = $res->Fetch())
{
	if(empty($sql['SPISANIE']) && empty($sql['LONG_STORAGE'])){

		$date_poverka = date("Y", strtotime($res1['POVERKA']." -45 days"));
		$d1= (int)$_GET['YEAR'];
		$d2 = (int)$date_poverka;
		//echo $d1." ".$d2;
		if($d1==$d2) {
			//var_dump($res1);
			$place='';
			$res_place = $DB->Query("SELECT * FROM `ba_laba`");
									while ($value = $res_place->Fetch()) {

										if ($res1['place_of_installation_or_storage'] == $value['ID']) {
										 $place = $value['NAME'];
										} else if ($res1['place_of_installation_or_storage'] == 7) {
										 $place = 'Отдел СМК и метрологии';
										}
									}

			$res_year[] = [
				'num' => $k,
				'object' => $res1['OBJECT'],
				'type_oborud' => $res1['TYPE_OBORUD'],
				'gosreestr' => $res1['GOSREESTR'],
				'reg_num' => $res1['REG_NUM'],
				'factory_number' => $res1['FACTORY_NUMBER'],
				'year' => $res1['YEAR'],
				'class' => $res1['сlass_precision_and_accuracy'],
				'measuring_range' => $res1['measuring_range'],
				'mc_interval' => $res1['MC_INTERVAL'],
				'poverka_last' => $res1['POVERKA_LAST'],
				'poverka_place' => $res1['POVERKA_PLACE'],
				'poverka' => date("m.Y", strtotime($res1['POVERKA']." -2 month")),
				'Place_of_installation_or_storage' => $place,

			];

			$k++;
		}

	}
}
//var_dump($res_year);
$file = new Bitrix\Main\IO\File(__DIR__ . '/../poverkaSI.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$document->normalizeContent();
$document->setValues([
	'YEAR_GRAPH' => $_GET['YEAR'],
	'DOC_NAME' => $doc_name,

    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $res_year,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
	'TableItemNUM' => 'Table.Item.NUM',
    'TableItemGOSREESTR' => 'Table.Item.GOSREESTR',
    'TableItemREG_NUM' => 'Table.Item.REG_NUM',
    'TableItemFACTORY_NUMBER' => 'Table.Item.FACTORY_NUMBER',
	'TableItemOBJECT' => 'Table.Item.OBJECT',
    'TableItemTYPE_OBORUD' => 'Table.Item.TYPE_OBORUD',
    'TableItemYEAR' => 'Table.Item.YEAR',
    'TableItemClass' => 'Table.Item.Class',
	'TableItemMeasuring_range' => 'Table.Item.Measuring_range',
	'TableItemMC_INTERVAL' => 'Table.Item.MC_INTERVAL',
	'TableItemPOVERKA_LAST' => 'Table.Item.POVERKA_LAST',
	'TableItemPOVERKA_PLACE' => 'Table.Item.POVERKA_PLACE',
	'TableItemPOVERKA' => 'Table.Item.POVERKA',
	'TableItemPlace_of_installation_or_storage' => 'Table.Item.Place_of_installation_or_storage',

]

);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
   header('Content-Disposition: attachment;filename="План графика поверки.docx"');
  header('Cache-Control: max-age=0');
  echo $content;
die;
