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

$file = new Bitrix\Main\IO\File(__DIR__ . '/../MeasurementProtocol.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

//Запрашиваем данные


$row = $DB->Query("SELECT * FROM `EQUIPMENT_CONTROL` WHERE `ID_EQUIP`=".$_GET['ID'])->Fetch();

$res_ob = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=".$_GET['ID'])->Fetch();

$etalons = unserialize($row['ID_ETALON']);
$params_name = unserialize($row['PARAMS_NAME']);
$params_value = unserialize($row['PARAMS_VALUE']);
$params_accuracy = unserialize($row['PARAMS_ACCURACY']);

$gost = $row['GOST'];
$id_equip = $_GET['ID'];


foreach($params_name as $k => $param)
{
		
	if($param)
	{		
		$p_str = str_replace(",", ".", $params_accuracy[$k]);
		$accur = 1/(float)$p_str;
				
		$results[] = [
			'Param' => $param,
			'Value' => $params_value[$k],
			'Value1' => $params_value[$k]+random_float(0, (float)$p_str, $accur*10),
			'Value2' => $params_value[$k]+random_float(0, (float)$p_str, $accur*10),
			'Value3' => $params_value[$k]+random_float(0, (float)$p_str, $accur*10),
			'ValueS' => $params_value[$k]
		];
	}
}

$res = $DB->Query("INSERT INTO `MEASUREMENT_PROTOCOLS` (`ID_EQUIP`,`NUMBER`,`DATE`) VALUES (" . $_GET['ID'] . ", '1', '" . date("Y-m-d"). "')");

dump($results);
$curDate = date("d.m.Y");
$userr = CUser::GetByID($res_ob['ID_ASSIGN1'])->Fetch();

$document->normalizeContent();
$document->setValues([
	'NUMBER' => '1',
	'YEAR' => date("Y"),
	'EQUIPMENT_NAME' => $res_ob['OBJECT'],
	'REG_NUM_ZAV' => $res_ob['REG_NUM_ZAV'],
	'REG_NUM' => $res_ob['REG_NUM'],
	'GOST' => $gost,
	'DATE' => $curDate,
	'DATE_NEXT' => date("d.m.Y", strtotime("+1 year", strtotime($curDate))),
	'TEMP' => random_float(20, 22, 10),
	'HUM' => random_float(50, 55, 10),
	'ASSIGNED' => getInitials($userr['NAME']).getInitials($userr['SECOND_NAME'])."&nbsp;".$userr['LAST_NAME'],
	
	
	 'Results2' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'Results2ItemParam' => 'Results2.Item.Param',
    'Results2ItemValue' => 'Results2.Item.Value',
	'Results2ItemValue1' => 'Results2.Item.Value1',
	'Results2ItemValue2' => 'Results2.Item.Value2',
	'Results2ItemValue3' => 'Results2.Item.Value3',
	'Results2ItemValueS' => 'Results2.Item.ValueS',
	
	
	
	
      
]);

$result = $document->process();
$content = $document->getContent();
// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Протокол измерений.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;