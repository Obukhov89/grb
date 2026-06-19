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
//global $DB;
$res = $DB->Query("SELECT * FROM `ba_gost_new` WHERE `IN_OA` = 1 AND `NUM_OA` != 0 ORDER BY `NUM_OA` ASC ");
while ($res1 = $res->Fetch())
{
// $ba_gost['GOST'][$i]=$res1['GOST'];
// $ba_gost['GOST_PUNKT'][$i]=$res1['GOST_PUNKT'];
// $ba_gost['NAME_GOST'][$i]=$res1['NAME_GOST'];
// //$tn_ved = [];
// $ba_gost['SPECIFIFATION'][$i]=$res1['SPECIFIFATION'];
// $ba_gost['NORM1_TEXT'][$i]=$res1['NORM1_TEXT'];
// $ba_gost['NORM2_TEXT'][$i]=$res1['NORM2_TEXT'];
// $i++;
	if(!$res1['NO_EXPORT']) {
	$ba_gost[] = [
	'Num' => $res1['NUM_OA'],
	'Gost' => $res1['GOST'],
	'GostPunkt' => $res1['GOST_PUNKT'],
	'NameGost' => $res1['NAME_GOST'],
	'Specification' => $res1['SPECIFICATION'],
	'Norm1' => $res1['NORM1_TEXT'],
	'Norm2' => $res1['NORM2_TEXT'],
	'OKPD' => $res1['OKPD'],
	'TNVED' => $res1['TNVED'],
	'Ed' => $res1['ED']
	];
	}
 $i++;
}
//var_dump($ba_gost);
$file = new Bitrix\Main\IO\File(__DIR__ . '/../OA.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$document->normalizeContent();
$document->setValues([
   
	

    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $ba_gost,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
	'TableItemNum' => 'Table.Item.Num',
    'TableItemGost' => 'Table.Item.Gost',
    'TableItemGostPunkt' => 'Table.Item.GostPunkt',
    'TableItemNameGost' => 'Table.Item.NameGost',
    'TableItemSpecification' => 'Table.Item.Specification',
	'TableItemNorm1' => 'Table.Item.Norm1',
	'TableItemNorm2' => 'Table.Item.Norm2',
	'TableItemOKPD' => 'Table.Item.OKPD',
	'TableItemTNVED' => 'Table.Item.TNVED',
	'TableItemEd' => 'Table.Item.Ed',
    
	
]

);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
   header('Content-Disposition: attachment;filename="Область аккредитации.docx"');
  header('Cache-Control: max-age=0');
  echo $content; 
die;



