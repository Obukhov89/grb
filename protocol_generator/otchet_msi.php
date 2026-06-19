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
$res = $DB->Query("SELECT * FROM `MSI` ORDER BY `ID` DESC");
$k=1;
$fl_res=1;
 while ($res1 = $res->Fetch())
 {
	
	 $year1 = date("Y", strtotime($res1['DATE1']));
	 $month1 = date("m", strtotime($res1['DATE1']));
	 
	 $year2 = date("Y", strtotime($res1['DATE2']));
	 $month2 = date("m", strtotime($res1['DATE2']));
	
	//echo $year1.'--';  echo $month1.'---';
	if ($year1 == $_GET['YEAR']) {
		
		// $k++;
		// $res_oaid[$k] = $res1['OA_ID'];
		// $res_result[$k] = $res1['RESULT'];
		// $res_month[$k] = $month;
		// echo '!!!--'; echo $res_oaid[$k].'---';
	// }
	// else
	// {
		// if ($res_result[$k]) $res_result[$k] = $res1['RESULT'];
		
	// }
// // $i++;
// }


// for($r=1; $r<count($res_oaid); $r++)
// {
		
	$res_oa = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=".(int)$res1['GOST_ID'])->Fetch();
	$done = ($res1['DONE'] ? '+' : '-');
	$corr = ($res1['CORRECTION'] ? '+' : '-');
		$res_year[] = [
		'Num' => $k,
		'Gost' => $res_oa['GOST'],
		'NameGost' => $res_oa['NAME_GOST'],
		'Date' => ($res1['DATE1']==$res1['DATE2'] ? $month1.'.'.$year1 : $month1.'.'.$year1.'-'.$month2.'.'.$year2),
		'Object' => $res1['OBJECT'],
		'Specification' => $res_oa['SPECIFICATION'],
		'MsiLab' => $res1['MSILAB'],
		'Done' => $done,
		'Corr' => $corr,
		'Comment' => $res1['COMMENT']
		
	];
	$k++;
}
}
//var_dump($res_year);

$file = new Bitrix\Main\IO\File(__DIR__ . '/../msi.docx');
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
	'TableItemNum' => 'Table.Item.Num',
    'TableItemGost' => 'Table.Item.Gost',
    'TableItemNameGost' => 'Table.Item.NameGost',
    'TableItemObject' => 'Table.Item.Object',
    'TableItemSpecification' => 'Table.Item.Specification',
	'TableItemDate' => 'Table.Item.Date',
	'TableItemMsiLab' => 'Table.Item.MsiLab',
	'TableItemDone' => 'Table.Item.Done',
	'TableItemCorr' => 'Table.Item.Corr',
	'TableItemComment' => 'Table.Item.Comment',
	
    
	
]

);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Отчет МСИ.docx"');
header('Cache-Control: max-age=0');
echo $content; 
die;



