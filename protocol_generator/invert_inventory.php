<?php
/*
 * Файл формирования инвентарной описи
 */
//echo'<pre>';var_dump('$results', $results);echo'</pre>';die(); 
use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

//require_once($_SERVER['DOCUMENT_ROOT'] . '/testDB/CDB.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CDB.php');

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

$file = new Bitrix\Main\IO\File(__DIR__ . '/../Invert_invetory.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$db = new CDB();

//$sql = 'SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage` = {?} ORDER BY `REG_NUM`';
//$sql = 'SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage` = {?} AND `LONG_STORAGE` != 1 AND (`SPISANIE` IS NULL OR `SPISANIE` = "") ORDER BY `REG_NUM`';
//Выбераем оборудование НЕ списаное , НЕ на консервации, и в наличии
$sql = 'SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage` = {?} AND `LONG_STORAGE` != 1 AND (`SPISANIE` IS NULL OR `SPISANIE` = "") AND `IN_STOCK` != 0 ORDER BY `REG_NUM`';

//Масиив с ID лабораторий
$arrayOfLaboratories = [
	'LFHI' => 1,
	'DSL' => 4,
	'LFMI' => 3,
	'LSM' => 2, 
	'OSK' => 5
];

$arrayWagonRoomId = ['9', '10', '11'];

$counter = 0;
foreach ($arrayOfLaboratories as $nameOfLaboratories => $idOfLaboratories) {
	
	//Находим массив данных по оборудыванию для каждой лаборатории 
	$resultBaOborud = $db->select($sql, [$idOfLaboratories]);
	
	if (!empty($resultBaOborud) && is_array($resultBaOborud)) {
	
		foreach($resultBaOborud as $keyBaOborud => $valBaOborud) {
			//ID помещения 
			$roomId = !empty($valBaOborud['roomnumber']) ? $valBaOborud['roomnumber'] : '';
			
			//Если помещение "Вагон", то не записываем данные в инвентарную опись
			if (in_array($roomId, $arrayWagonRoomId)) {
				continue; 
			}
			
			//Увеличиваем счетчик кол-ва оборудования (начинаем запись с единицы)
			$counter++;
			
			//Наименование оборудования
			$name = !empty($valBaOborud['OBJECT']) ? $valBaOborud['OBJECT'] : $valBaOborud['OBJECT'];
			//Инвентарный номер
			$regNum = !empty($valBaOborud['REG_NUM']) ? $valBaOborud['REG_NUM'] : $valBaOborud['REG_NUM'];
			//Заводской номер
			$factoryNumber = !empty($valBaOborud['FACTORY_NUMBER']) ? $valBaOborud['FACTORY_NUMBER'] : $valBaOborud['FACTORY_NUMBER'];
			//Тип оборудования
			$typeOborud = !empty($valBaOborud['TYPE_OBORUD']) ? $valBaOborud['TYPE_OBORUD'] : '';
			
			//Собираем массив данных для записи в шаблон 
			$results[$nameOfLaboratories][] = [
				'NAME' => $name,
				'REG_NUM' => $regNum,
				'FACTORY_NUMBER' => $factoryNumber,
				'COUNTER' => $counter,
				'TYPE_OBORUD' => $typeOborud,
			];
		}
	}
}

$document->normalizeContent();

//Добавляем данные в шаблон
$document->setValues([
	'ResultsLFHI' => new DocumentGenerator\DataProvider\ArrayDataProvider(
		$results['LFHI'],
		[
			'ITEM_NAME' => 'Item',
			'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
		]
	),
	'ResultsLFHIItemName' => 'ResultsLFHI.Item.NAME', //Наименование оборудования ЛФХИ
	'ResultsLFHIItemRegNum' => 'ResultsLFHI.Item.REG_NUM', //Инвентарный номер оборудования ЛФХИ
	'ResultsLFHIItemFactoryNumber' => 'ResultsLFHI.Item.FACTORY_NUMBER', //Заводской номер оборудования ЛФХИ
	'ResultsLFHIItemCounter' => 'ResultsLFHI.Item.COUNTER', //Порядковый номер оборудования ЛФХИ
	'ResultsLFHIItemType' => 'ResultsLFHI.Item.TYPE_OBORUD', //Тип оборудования ЛФХИ
	
	'ResultsLSM' => new DocumentGenerator\DataProvider\ArrayDataProvider(
		$results['LSM'],
		[
			'ITEM_NAME' => 'Item',
			'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
		]
	),
	'ResultsLSMItemName' => 'ResultsLSM.Item.NAME', //Наименование оборудования ЛСМ
	'ResultsLSMItemRegNum' => 'ResultsLSM.Item.REG_NUM', //Инвентарный номер оборудования ЛСМ
	'ResultsLSMItemFactoryNumber' => 'ResultsLSM.Item.FACTORY_NUMBER', //Заводской номер оборудования ЛСМ
	'ResultsLSMItemCounter' => 'ResultsLSM.Item.COUNTER', //Порядковый номер оборудования ЛСМ
	'ResultsLSMItemType' => 'ResultsLSM.Item.TYPE_OBORUD', //Тип оборудования ЛСМ
	
	'ResultsLFMI' => new DocumentGenerator\DataProvider\ArrayDataProvider(
		$results['LFMI'],
		[
			'ITEM_NAME' => 'Item',
			'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
		]
	),
	'ResultsLFMIItemName' => 'ResultsLFMI.Item.NAME', //Наименование оборудования ЛФМИ
	'ResultsLFMIItemRegNum' => 'ResultsLFMI.Item.REG_NUM', //Инвентарный номер оборудования ЛФМИ
	'ResultsLFMIItemFactoryNumber' => 'ResultsLFMI.Item.FACTORY_NUMBER', //Заводской номер оборудования ЛФМИ
	'ResultsLFMIItemCounter' => 'ResultsLFMI.Item.COUNTER', //Порядковый номер оборудования ЛФМИ
	'ResultsLFMIItemType' => 'ResultsLFMI.Item.TYPE_OBORUD', //Тип оборудования ЛФМИ
	
	'ResultsDSL' => new DocumentGenerator\DataProvider\ArrayDataProvider(
		$results['DSL'],
		[
			'ITEM_NAME' => 'Item',
			'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
		]
	),
	'ResultsDSLItemName' => 'ResultsDSL.Item.NAME', //Наименование оборудования ДСЛ
	'ResultsDSLItemRegNum' => 'ResultsDSL.Item.REG_NUM', //Инвентарный номер оборудования ДСЛ
	'ResultsDSLItemFactoryNumber' => 'ResultsDSL.Item.FACTORY_NUMBER', //Заводской номер оборудования ДСЛ
	'ResultsDSLItemCounter' => 'ResultsDSL.Item.COUNTER', //Порядковый номер оборудования ДСЛ
	'ResultsDSLItemType' => 'ResultsDSL.Item.TYPE_OBORUD', //Тип оборудования ДСЛ
	
	'ResultsOSK' => new DocumentGenerator\DataProvider\ArrayDataProvider(
		$results['OSK'],
		[
			'ITEM_NAME' => 'Item',
			'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
		]
	),
	'ResultsOSKItemName' => 'ResultsOSK.Item.NAME', //Наименование оборудования ОСК
	'ResultsOSKItemRegNum' => 'ResultsOSK.Item.REG_NUM', //Инвентарный номер оборудования ОСК
	'ResultsOSKItemFactoryNumber' => 'ResultsOSK.Item.FACTORY_NUMBER', //Заводской номер оборудования ОСК
	'ResultsOSKItemCounter' => 'ResultsOSK.Item.COUNTER', //Порядковый номер оборудования ОСК
	'ResultsOSKItemType' => 'ResultsOSK.Item.TYPE_OBORUD', //Тип оборудования ОСК
]);
	
$result = $document->process();
$content = $document->getContent();

$path = 'archive_invert_inventory/Инвентарная опись.docx';

function file_force_contents( $fullPath, $contents, $flags = 0 ){
	$parts = explode( '/', $fullPath );
	array_pop( $parts );
	$dir = implode( '/', $parts );

	if( !is_dir( $dir ) )
		mkdir( $dir, 0777, true );

	file_put_contents( $fullPath, $contents, $flags );
}

file_force_contents($path, $content);

echo 'Инвентарная опись сформирована!';

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Инвентарная опись.docx"');
header('Cache-Control: max-age=0');
echo $content;

die;

?>