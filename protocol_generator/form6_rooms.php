<?php

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

global $APPLICATION;
global $DB;

$idRoom = implode(',',$_GET['ROOMS']);

$APPLICATION->RestartBuffer();

$fileForm6 = new Bitrix\Main\IO\File(__DIR__ . '/../Form6.docx');
$documentForm6 = new Bitrix\DocumentGenerator\Body\Docx($fileForm6->getContents());

$res_rooms = $DB->Query("SELECT * FROM `ROOMS` WHERE `ID` IN ({$idRoom}) ORDER BY `NUMBER`");
$r = 1;
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
		'PLACEMENT' => $sql['PLACEMENT']
		//-- ./Вывод даты и вспомогательного оборудования в формах, добавление PLACEMENT --//

	];
	$r++;
}

$documentForm6->normalizeContent();
$documentForm6->setValues([
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
	'Results6ItemCOMMENT' => 'Results6.Item.COMMENT',
	//-- Вывод даты и вспомогательного оборудования в формах, добавление PLACEMENT --//
	'Results6ItemPLACEMENT' => 'Results6.Item.PLACEMENT'
	//-- ./Вывод даты и вспомогательного оборудования в формах, добавление PLACEMENT --//
]);


$result = $documentForm6->process();
$content = $documentForm6->getContent();

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Форма-6.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;
