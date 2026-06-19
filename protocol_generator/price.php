<?php
/**
 * Created by Makaralex92.
 * User: Алексей
 * Date: 25.04.2019
 * Time: 16:01
 */

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

//-------------'Конвертация pdf' начало-----------------//
require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
//-------------'Конвертация pdf' окончание-----------------//
Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

//Текущий Пользователь
$user_id = !empty($USER->GetID()) ? (int)$USER->GetID() : '';

$file = new Bitrix\Main\IO\File(__DIR__ . '/../Price2.docx');
/*--------------'KP2''Изменение КП'----------------*/
$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../Price.docx');

$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
/*--------------'KP2''Изменение КП'----------------*/
$document2 = new Bitrix\DocumentGenerator\Body\Docx($file2->getContents());

// function num2word($n, $titles)
// {
    // $cases = array(2, 0, 1, 1, 1, 2);
    // return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
// } 


$tz_arr = explode('_', $_GET['mat']);
dump($tz_arr);
$res_oa = $DB->Query("SELECT * FROM `ba_gost` WHERE `DOP` IN (".implode(',',$tz_arr).") ORDER BY `DOP` ASC");
$resArr=[];
while ($gost = $res_oa->Fetch()) 
{
	$resArr[] = $gost;
}	
$results = [];
$prod_prev='';
//while ($gost = $res_oa->Fetch()) {
for($i=0; $i<count($resArr); $i++) {
    $probe = [];
    $PRICE = [];
	
	$resMat = $DB->Query("SELECT * FROM `MATERIALS` WHERE `ID`=".$resArr[$i]['DOP'])->Fetch();
	$prod = $resMat['NAME'];
	if($i<(count($resArr)-1)) 
	{	
		$resMat_next = $DB->Query("SELECT * FROM `MATERIALS` WHERE `ID`=".$resArr[$i+1]['DOP'])->Fetch();
		
		$prod_next = $resMat_next['NAME'];
		$line='';
        if($prod != $prod_next) 
		{
			$line = '______________________';
		}
		
	}
	else
	{
		$line='';	
		
	}
	if($prod == $prod_prev) 
	{
		$prod ='';
	}
	
		$probe = $resArr[$i]['SPECIFICATION'];
		$gostName = $resArr[$i]['GOST']. ' ' . $resArr[$i]['GOST_PUNKT'];
        $PRICE = $resArr[$i]['PRICE'];
		
		
		
		$results[] = [
        'Prod' => $prod,
        'Spec' => $probe,
		'Gost' => $gostName,
        'Price' => ($PRICE ? number_format($PRICE, 2, ',', '') : '-') ,
		'Line' => $line,
        /*--------------------'Изменение КП' окончание--------------------*/
		
    ];
	
	$prod_prev = $resMat['NAME'];
    
}

// dump($results);
// die;

//Заполняем данные
$document->normalizeContent();
$document->setValues([




    'Pres' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'PresItemProd' => 'Pres.Item.Prod',
    'PresItemSpec' => 'Pres.Item.Spec',
    'PresItemPrice' => 'Pres.Item.Price',
	'PresItemGost' => 'Pres.Item.Gost',
	'PresItemLine' => 'Pres.Item.Line',
    /*--------------------'Изменение КП' окончание--------------------*/
]);


$result = $document->process();
$content = $document->getContent();

//Сохраняем данные о сформированном прасе $user_id
$queryInsertSendPrice = "INSERT INTO `SEND_PRICE` (`DATE`, `USER_ID`) VALUES ('".date("Y-m-d")."', " . $user_id . ")";
$DB->Query($queryInsertSendPrice);
//$DB->Query("INSERT INTO `SEND_PRICE` (`DATE`) VALUES ('".date("Y-m-d")."')");

$maxId = $DB->Query("SELECT MAX(`ID`) FROM `SEND_PRICE`")->Fetch();
$ID = $maxId['MAX(`ID`)'];
// dump($maxId);
echo $ID;
// die;
$newpath = "archive_price/".$ID.".docx";
//-------------'Конвертация pdf' начало-----------------//
$interimPath = "interim_archive_price/".$ID.".docx";
$outputFile = $ID.".pdf";
$outputPath = "archive_price/";
//-------------'Конвертация pdf' окончание-----------------//
function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );
   
    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );
   
    file_put_contents( $fullPath, $contents, $flags );
}


//file_force_contents($newpath, $content);
//-------------'Конвертация pdf' начало-----------------//
$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_price/';
if( !is_dir( $newDirectory ) ) {
	mkdir($newDirectory, 0777, true);
}

file_force_contents($interimPath, $content);

$fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$interimPath;
$fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$outputPath;

// если вы используете composer, просто используйте это
use  NcJoes\OfficeConverter\OfficeConverter;

$converter  =  new  OfficeConverter($fullPathDocx, $fullPathPDF);//, $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/');
$converter -> convertTo($outputFile); // генерирует pdf файл в том же каталоге, что и test-file.docx
//-------------'Конвертация pdf' окончание-----------------//

 /*--------------------'KP2''Изменение КП' начало--------------------*/
$document2->normalizeContent();
$document2->setValues([
   


    'Pres' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'PresItemProd' => 'Pres.Item.Prod',
    'PresItemSpec' => 'Pres.Item.Spec',
    'PresItemPrice' => 'Pres.Item.Price',
    'PresItemGost' => 'Pres.Item.Gost',
    'PresItemLine' => 'Pres.Item.Line',
]);


$result2 = $document2->process();
$content2 = $document2->getContent();
 /*--------------------'KP2''Изменение КП' окончание--------------------*/

// вывод непосредственно в браузер 
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Прайс №'.$ID.' от '.date('d.m.Y').'.docx"');
header('Cache-Control: max-age=0');
//echo $content;
/*--------------------'Изменение КП' начало--------------------*/
echo $content2;
/*--------------------'Изменение КП' окончание--------------------*/
die;