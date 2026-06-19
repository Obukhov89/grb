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
//========================== начало 'ФИО и ДОЛЖНОСТЬ родитетьный падеж' ==================================//
//подключаем библиотеку падежей
require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/Morphos-master/vendor/wapmorgan/morphos/web/case_conversion.php';
//========================== окончание 'ФИО и ДОЛЖНОСТЬ родитетьный падеж' ===============================//

//-------------'Конвертация pdf'-----------------//
require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';


Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

$file = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

//Запрашиваем данные
$ID = $_GET['ID']; //ID Заявки
$TZ_ID = $_GET['TZ_ID']; //ID Технического задания. В нём хранится ТЗ, результаты исследований и информация о пробах
$mater = $_GET['mater']; // ID материала, указанного в ТЗ
$probe_ID = $_GET['probe']; // ID пробы, указанного в пробах, заполненных на основе ТЗ

$deal = CCrmDeal::GetByID($ID); //Берём значение сделки
$company = CCrmCompany::GetByID($deal['COMPANY_ID']); //Берём значение компании
$companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch(); //Берём значение компании



$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
$curdate = "Договор_".$res_dog['ID'].'_'.date("d-m-Y_H-i-s");
$curdateEng = "Contract_".$res_dog['ID'].'_'.date("d-m-Y_H-i-s");
//----------------------------'договор еще не составлен' начало --------------//
/*if($res_dog) 
	$res_dog1 = $DB->Query("UPDATE `DOGOVOR` SET `ACTUAL_VER` ='".$curdate."' WHERE `TZ_ID`=" . $TZ_ID);
else
	$res_dog1 = $DB->Query("INSERT INTO `DOGOVOR` (`DATE`, `TZ_ID`, `ACTUAL_VER`) VALUES (NOW(), " . $TZ_ID.", '".$curdate."')" );*/

/*if($res_dog) {
	$res_dog1 = $DB->Query("UPDATE `DOGOVOR` SET `ACTUAL_VER` ='".$curdate."' WHERE `TZ_ID`=" . $TZ_ID);
	
	$tzId = !empty($TZ_ID) ? (int)$TZ_ID : '';
	$id = !empty($ID) ? (int)$ID : '';
	
	$resDog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $tzId)->Fetch();
	
	$dogovorNum = addNumContract($tzId, $id, $resDog);
	
	if (!empty($dogovorNum)) {
		$resContract = $DB->Query("UPDATE `ba_tz` SET `DOGOVOR_NUM` ='".$dogovorNum."' WHERE `ID_Z`=" . $id);
	}

} else {*/
	//$res_dog1 = $DB->Query("INSERT INTO `DOGOVOR` (`DATE`, `TZ_ID`, `ACTUAL_VER`) VALUES (NOW(), " . $TZ_ID.", '".$curdate."')" );
	
	// $tzId = !empty($TZ_ID) ? (int)$TZ_ID : '';
	// $id = !empty($ID) ? (int)$ID : '';	
	// //echo'<pre>';var_dump($res_dog1->DB->db_Conn->insert_id);die;
	// $resDog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . $res_dog1->DB->db_Conn->insert_id)->Fetch();
	
	// $dogovorNum = addNumContract($tzId, $id, $resDog);
	
	// if (!empty($dogovorNum)) {
		// //$resContract = $DB->Query("UPDATE `ba_tz` SET `DOGOVOR_NUM` ='".$dogovorNum."' WHERE `ID_Z`=" . $id);
	// }
	
	
	// //$idDog = $DB->Query("SELECT `ID` FROM `DOGOVOR` ORDER BY `ID` DESC LIMIT 1")->Fetch();
	
	// if(!empty($idDog['ID'])) 
	// {
	// //	$DB->Query("INSERT INTO `DEALS_TO_CONTRACTS` (`ID_CONTRACT`, `ID_DEAL`) VALUES (".(int)$idDog['ID'].", ".(int)$id.")");
	// }

//}
//----------------------------'договор еще не составлен' начало --------------//
if (CModule::IncludeModule('crm')) {
        $deal1 = new CCrmDeal;
        $arDealUpdate = array('STAGE_ID' => 'EXECUTING',  'UF_CRM_1562503473' =>  $_SERVER["SERVER_NAME"] . "/protocol_generator/dogovor.php?ID=" . $ID . '&TZ_ID=' . $TZ_ID);
        $resUpd = $deal1->Update((int)$_GET['ID'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    }

$comPhones = CCrmFieldMulti::GetList(array(), array('TYPE_ID' => 'PHONE', 'ELEMENT_ID' => $deal['COMPANY_ID']));
$comEmails = CCrmFieldMulti::GetList(array(), array('TYPE_ID' => 'EMAIL', 'ELEMENT_ID' => $deal['COMPANY_ID']));
while ($Phone = $comPhones->Fetch()) {
    $arMultiFields['PHONE'][] = $Phone['VALUE'];
};
while ($Email = $comEmails->Fetch()) {
    $arMultiFields['EMAIL'][] = $Email['VALUE'];
};

$arRequisite = new \Bitrix\Crm\EntityRequisite();
$dbRes = $arRequisite->getList(["filter" => ["ENTITY_ID" => $deal['COMPANY_ID'], "ENTITY_TYPE_ID" => CCrmOwnerType::Company, 'PRESET_ID' => 1]]);
$req = $dbRes->fetch();

$rs = $arRequisite->getList([
    "filter" => [
        "ENTITY_ID" => $deal['COMPANY_ID'],
        "ENTITY_TYPE_ID" => CCrmOwnerType::Company,
    ]
]);
$rows = $rs->fetchAll();

foreach ($rows as $companyReq) {
    $params = [
        'filter' => [
            'ENTITY_ID' => $companyReq['ID'],
            'ENTITY_TYPE_ID' => CCrmOwnerType::Requisite]
    ];
    $bankReq = (new \Bitrix\Crm\EntityBankDetail)->getList($params)->fetch();
}

//echo "<pre>";
//dump($bankReq);
//exit;

$address = Bitrix\Crm\EntityRequisite::getAddresses($deal['COMPANY_ID']);

global $DB;
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
$ba_tz = $res->Fetch();

//Формируем данные
$tz_arr = [];
$results = [];
foreach (unserialize($ba_tz['TZ']) as $item) {
    $probe = [];
    $PRICE = [];
	$i=0;
    foreach ($item['gost'] as $items) {
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $items);
        $gost = $ba_gost->Fetch();
        $probe = $gost['SPECIFICATION'];
        $PRICE[] = $gost['PRICE'];
		$prod = '';
		if(!$i) 
		{
			$prod_arr = explode(', ID',$item['mater']);
			$prod = $prod_arr[0];
		}
		elseif($i==count($item['gost'])-1) $prod = '___________________';
		$results[] = [
        'Prod' => $prod,
        'Spec' => $probe,
        'Price' => ($item['price'][$i] ? $item['price'][$i] : '-'),
		];
		$i++;
    }
    $tz_arr[] = [
        'Name' => $item['mater'],
        'Spec' => implode(", ", $probe),
        'V' => $item['obiem'] . " " . num2word($item['obiem'], ["проба", "пробы", "проб"]),
    ];
    // $results[] = [
        // 'Prod' => $item['mater'],
        // 'Spec' => implode(", ", $probe),
        // 'Price' => implode(", ", $PRICE),
    // ];
}

$FIO = explode(" ", $req['RQ_DIRECTOR']);
$directorName = getInitials($FIO[1]);
$directorSecName = getInitials($FIO[2]);
$directorFIO = $FIO[0]." ".$directorName.$directorSecName;

//========================== начало 'ФИО и ДОЛЖНОСТЬ родитетьный падеж' ==================================//
//меняем падеж
$fioDerector = $req['RQ_DIRECTOR']; 
$position = $req['RQ_COMPANY_REG_DATE'];

$fioDerectorGenitive = null;
if (!empty($fioDerector)){
	$arrfioDerector = conversion($fioDerector);
	
	if ($arrfioDerector !== false) {
		foreach (array(morphos\Russian\Cases::RODIT => 'Родительный') as $case => $name) {
			$fioDerectorGenitive = $arrfioDerector[$case];
		}	
	}
}

$positionGenitive = null;
if (!empty($position)){
	$arrPosition = conversion($position);
	
	if ($arrPosition !== false) {
		foreach (array(morphos\Russian\Cases::RODIT => 'Родительный') as $case => $name) {
			$positionGenitive = $arrPosition[$case];
		}	
	}
}

$positionGenitive = !empty($positionGenitive) ? $positionGenitive : null;
$fioDerectorGenitive = !empty($fioDerectorGenitive) ? $fioDerectorGenitive : null;

$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();

//Заполняем данные
$document->normalizeContent();
$document->setValues([

    'Year' => date('Y'),

    'NumberDogovor' => $res_dog['NUMBER'] ? $res_dog['NUMBER'] : $res_dog['ID'],
    'DateDogovor' => date('d.m.Y', strtotime($deal['DATE_CREATE'])),
    'Company' => $deal['COMPANY_TITLE'],
	'Position' => $positionGenitive, 
	'Position2' => $req['RQ_COMPANY_REG_DATE'],
	'FIOCompany' => $fioDerectorGenitive,
    //'FIOCompany' => $req['RQ_DIRECTOR'],
    'CompanyFullName' => $req['RQ_COMPANY_FULL_NAME'],
	'DirectorFIO' => $directorFIO,
//========================== окончание 'ФИО и ДОЛЖНОСТЬ родитетьный падеж' ==================================//
    'UAdres' => $req['RQ_ACCOUNTANT'],//$address[6]["ADDRESS_1"] . ',' . $address[6]["CITY"] . ',' . $address[6]["PROVINCE"],
    'phone' => $req['RQ_PHONE'],
    'Email' => $req['RQ_EMAIL'],

    'OGRN' => $req['RQ_OGRN'],
    'INN' => $req['RQ_INN'],
    'KPP' => $req['RQ_KPP'],

    'RaschSchet' => $bankReq['RQ_ACC_NUM'],
    'KSchet' => $bankReq['RQ_COR_ACC_NUM'],
    'BIK' => $bankReq['RQ_BIK'],
	'BankName' => $bankReq['RQ_BANK_NAME'],

    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $tz_arr,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'TableItemName' => 'Table.Item.Name',
    'TableItemSpec' => 'Table.Item.Spec',
    'TableItemV' => 'Table.Item.V',


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
]);

$result = $document->process();
$content = $document->getContent();

$res_dog2 = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
$newpath = "archive_dog/".$ID."/".$curdate.".docx";
//-------------'Конвертация pdf'-----------------//
$interimPath = "interim_archive_dog/".$ID."/".$curdateEng.".docx";
$outputFile = $curdate.".pdf";
$outputPath = "archive_dog/".$ID."/";

function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );
   
    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );
   
    file_put_contents( $fullPath, $contents, $flags );
}

file_force_contents($newpath, $content);
//-------------'Конвертация pdf'-----------------//
file_force_contents($interimPath, $content);


//$converter  =  new NcJoes\OfficeConverter\OfficeConverter( './interim_archive_dog/1162/Договор_81_02-04-2020_12-38-48.docx' ); 
//$converter->convertTo( 'output-file.pdf' ); 
//$fullPathDocx = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/'.$interimPath;
$fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$interimPath;
$fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$outputPath;
echo $fullPathDocx."<br>";
echo $fullPathPDF;

// если вы используете composer, просто используйте это
use  NcJoes\OfficeConverter\OfficeConverter;

$converter  =  new  OfficeConverter($fullPathDocx, $fullPathPDF);//, $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/');


$converter -> convertTo($outputFile); // генерирует pdf файл в том же каталоге, что и test-file.docx
//$converter -> convertTo ( ' output-file.html ' ); // генерирует HTML-файл в той же директории, что и test-file.docx

// чтобы указать выходной каталог, укажите его в качестве второго аргумента для конструктора
//$converter  =  new  OfficeConverter ( ' test-file.docx ' , ' path-to-outdir ' );

/*	
$converter  =  new  NcJoes\OfficeConverter\OfficeConverter('./interim_archive_dog/1162/Договор_81_02-04-2020_12-38-48.docx'); 
$converter -> convertTo ( 'output-file.pdf' ); // генерирует pdf файл в том же каталоге, что и test-file.docx 
$converter -> convertTo ( 'output-file.html' ); // генерирует HTML-файл в той же директории, что и test-file.docx

// чтобы указать выходной каталог, укажите его как второй аргумент для конструктора 
$converter  =  new  NcJoes\OfficeConverter\OfficeConverter('./interim_archive_dog/1162/Договор_81_02-04-2020_12-38-48.docx', './');
	*/
//echo'<pre>';var_dump($interimPath,$outputFile,$outputPath);die;
/*function conversionPdf($interimPath, $outputFile, $outputPath) {
	$converter  =  new  OfficeConverter ( 'test-file.docx' ); 
	$converter -> convertTo ( 'output-file.pdf' ); // генерирует pdf файл в том же каталоге, что и test-file.docx 
	$converter -> convertTo ( 'output-file.html' ); // генерирует HTML-файл в той же директории, что и test-file.docx

	// чтобы указать выходной каталог, укажите его как второй аргумент для конструктора 
	//$converter  =  new  OfficeConverter ( 'test-file.docx' , ' ./' );
	
	$converter  =  new  NcJoes\OfficeConverter\OfficeConverter ($interimPath); 
	$converter -> convertTo ($outputFile.".pdf"); // генерирует pdf файл в том же каталоге, что и test-file.docx 
	//$converter -> convertTo ( 'output-file.html' ); // генерирует HTML-файл в той же директории, что и test-file.docx

	// чтобы указать выходной каталог, укажите его как второй аргумент для конструктора 
	$converter  =  new  NcJoes\OfficeConverter\OfficeConverter ($interimPath, $outputPath);
}

conversionPdf($interimPath, $outputFile, $outputPath);*/

// вывод непосредственно в браузер
// header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
// header('Content-Disposition: attachment;filename="Dogovor.docx"');
// header('Cache-Control: max-age=0');
// echo $content;
die;