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
//global $DB;
$res = $DB->Query("SELECT * FROM `VLK` ORDER BY `OA_ID` DESC");
$k=0;
$fl_res=1;
while ($res1 = $res->Fetch())
{
	
	$year = substr($res1['DATE'], -4);
	$month = substr($res1['DATE'], 0, -5);
	echo $year.'--';  echo $res1['OA_ID'].'---';
	if ($year == $_GET['YEAR']&&$res_oaid[$k]!=$res1['OA_ID']) {
		
		$k++;
		$res_oaid[$k] = $res1['OA_ID'];
		$res_result[$k] = $res1['RESULT'];
		$res_month[$k] = $month;
		echo '!!!--'; echo $res_oaid[$k].'---';
	}
	else
	{
		if ($res_result[$k]) $res_result[$k] = $res1['RESULT'];
		
	}
// $i++;
}
var_dump($res_oaid);

for($r=1; $r<count($res_oaid); $r++)
{
		
	$res_oa = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=".(int)$res_oaid[$r])->Fetch();
	 $res_vlk = ($res_result[$r] ? '+' : '-');	
		$res_year[] = [
		'ID' => $res_oa['ID'],
		'Gost' => $res_oa['GOST'].' '.$res_oa['NAME_GOST'],
		'Punkt' => $res_oa['GOST_PUNKT'],
		'Object' => $res_oa['GOST_OBJECT'],
		'Specification' => $res_oa['SPECIFICATION'],
		'Month' => $res_month[$r],
		'01' => ($res_month[$r]=='01' ? $res_vlk : ''),
		'02' => ($res_month[$r]=='02' ? $res_vlk : ''),
		'03' => ($res_month[$r]=='03' ? $res_vlk : ''),
		'04' => ($res_month[$r]=='04' ? $res_vlk : ''),
		'05' => ($res_month[$r]=='05' ? $res_vlk : ''),
		'06' => ($res_month[$r]=='06' ? $res_vlk : ''),
		'07' => ($res_month[$r]=='07' ? $res_vlk : ''),
		'08' => ($res_month[$r]=='08' ? $res_vlk : ''),
		'09' => ($res_month[$r]=='09' ? $res_vlk : ''),
		'10' => ($res_month[$r]=='10' ? $res_vlk : ''),
		'11' => ($res_month[$r]=='11' ? $res_vlk : ''),
		'12' => ($res_month[$r]=='12' ? $res_vlk : ''),
	];
	
}


$file = new Bitrix\Main\IO\File(__DIR__ . '/../otchet_vlk.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$document->normalizeContent();
$document->setValues([
   
	

    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $res_year,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
	//'TableItemNum' => 'Table.Item.Num',
    'TableItemGost' => 'Table.Item.Gost',
    'TableItemPunkt' => 'Table.Item.Punkt',
    'TableItemObject' => 'Table.Item.Object',
    'TableItemSpecification' => 'Table.Item.Specification',
	'TableItem01' => 'Table.Item.01',
	'TableItem02' => 'Table.Item.02',
	'TableItem03' => 'Table.Item.03',
	'TableItem04' => 'Table.Item.04',
	'TableItem05' => 'Table.Item.05',
	'TableItem06' => 'Table.Item.06',
	'TableItem07' => 'Table.Item.07',
	'TableItem08' => 'Table.Item.08',
	'TableItem09' => 'Table.Item.09',
	'TableItem10' => 'Table.Item.10',
	'TableItem11' => 'Table.Item.11',
	'TableItem12' => 'Table.Item.12',
	// 'TableItemResult' => 'Table.Item.Norm1',
	// 'TableItemNorm2' => 'Table.Item.Norm2',
	// 'TableItemEd' => 'Table.Item.Ed',
    
	
]

);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
   header('Content-Disposition: attachment;filename="Отчет ВЛК.docx"');
  header('Cache-Control: max-age=0');
  echo $content; 
die;



