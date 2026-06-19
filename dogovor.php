<?php
/**
 * Created by Makaralex92.
 * User: Алексей
 * Date: 25.04.2019
 * Time: 16:01
 */

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;
use  NcJoes\OfficeConverter\OfficeConverter;
use Bitrix\Crm\EntityRequisite;
//use Bitrix\Crm\EntityBankDetail;


require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';


require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/Morphos-master/vendor/wapmorgan/morphos/web/case_conversion.php';
require $_SERVER["DOCUMENT_ROOT"] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';


Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

global $DB, $USER;


$ba_tz = null;
if (!empty($_GET['TZ_ID'])) {
	$ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID'])->Fetch();
}


if (!empty($ba_tz['CHECK_IP']) && $ba_tz['CHECK_IP'] == 1) {
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor_ip.docx');
	$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor2_ip.docx');
} else {
    if(!empty($_GET['ID_C'])) {
        $dogovorSample = $DB->Query(
            "
            SELECT dp.sample
            FROM DOGOVOR d
            LEFT JOIN dogovor_payment dp ON dp.id = d.payment_id
            WHERE d.ID = '{$_GET['ID_C']}}'
            ")->Fetch()['sample'];

        if(empty($dogovorSample)) {
            $file = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor.docx');
            $file2 = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor2.docx');
        }else {
            $file = new Bitrix\Main\IO\File(__DIR__ . "/../{$dogovorSample}.docx");
            $file2 = new Bitrix\Main\IO\File(__DIR__ . "/../{$dogovorSample}2.docx");
        }
    }else {
        $file = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor.docx');
        $file2 = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor2.docx');
    }
}


$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
//$document2 = new Bitrix\DocumentGenerator\Body\Docx($file2->getContents());

$ID = !empty($_GET['ID']) ? (int)$_GET['ID'] : '';
$ID_C = !empty($_GET['ID_C']) ? (int)$_GET['ID_C'] : '';

$TZ_ID = $_GET['TZ_ID'];
$mater = $_GET['mater'];
$probe_ID = $_GET['probe'];

if(!$ID && $ID_C) {
    $deal['COMPANY_ID'] = $DB->Query("SELECT CLIENT_ID FROM DOGOVOR WHERE ID = '{$ID_C}'")->Fetch()['CLIENT_ID'];
}else {
    $deal = CCrmDeal::GetByID($ID);
}

//echo "<pre>";
//var_dump($deal);
//die();

$company = CCrmCompany::GetByID($deal['COMPANY_ID']);
$companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch();

//echo "<pre>";
//print_r($deal);
//echo "</pre>";
//die();


// $res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
// $dateDogovor = date("d-m-Y_H-i-s");
// $curdate = "Договор_".$res_dog['ID'].'_'.$dateDogovor;


// if($res_dog['ID'])
	// $res_dog1 = $DB->Query("UPDATE `DOGOVOR` SET `ACTUAL_VER` ='".$curdate."' WHERE `TZ_ID`=" . $TZ_ID);
// else
	// $res_dog1 = $DB->Query("INSERT INTO `DOGOVOR` (`DATE`, `TZ_ID`, `ACTUAL_VER`) VALUES (NOW(), " . $TZ_ID.", '".$curdate."')" );

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
		// $resContract = $DB->Query("UPDATE `ba_tz` SET `DOGOVOR_NUM` ='".$dogovorNum."' WHERE `ID_Z`=" . $id);
	// }


	// $idDog = $DB->Query("SELECT `ID` FROM `DOGOVOR` ORDER BY `ID` DESC LIMIT 1")->Fetch();

	// if(!empty($idDog['ID']))
	// {
		// $DB->Query("INSERT INTO `DEALS_TO_CONTRACTS` (`ID_CONTRACT`, `ID_DEAL`) VALUES (".(int)$idDog['ID'].", ".(int)$id.")");
	// }

//}


// $res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
// $curdate = "Договор_".$res_dog['ID'].'_'.$dateDogovor;
// $curdateEng = "Contract_".$res_dog['ID'].'_'.$dateDogovor;

$tzId = !empty($TZ_ID) ? (int)$TZ_ID : '';
$id = !empty($ID) ? (int)$ID : '';

$res_deals = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$ID)->Fetch();

$res_dogovor = null;
if (!empty($res_deals['ID_CONTRACT'])) {
    $res_dogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$res_deals['ID_CONTRACT'])->Fetch();
}else if(!empty($_GET['ID_C'])) {
    $res_dogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$_GET['ID_C'])->Fetch();
}

$curdate = null;
$curdateEng = null;
//$pathDogovorNum = null;

$pathDogovor = null;

$dogovorNumber = null;
$dogovorDate = null;


$str_type = '';

if (!empty($res_dogovor['NUMBER'])) {
	$timeDogovor = date("d-m-Y_H-i-s");

	$dogovorDate = $res_dogovor['DATE'];

	$arNumberDogovor = !empty($res_dogovor['NUMBER']) ? explode('/', $res_dogovor['NUMBER']) : '';
	$numDogovor = !empty($arNumberDogovor[0]) ? $arNumberDogovor[0] : '';
	//$curdate = "Договор_".$res_dogovor['NUMBER'].'_'. $timeDogovor;
	//$curdateEng = "Contract_".$res_dogovor['NUMBER'].'_'. $timeDogovor;
	$curdate = "Договор_".$numDogovor.'_'. $timeDogovor;
	$curdateEng = "Contract_".$numDogovor.'_'. $timeDogovor;
	//$pathDogovorNum = $res_dogovor['NUMBER'];

	$pathDogovor = $res_dogovor['ID'];

	//$pathDogovorNum = $res_dogovor['ID'];
    if($res_dogovor['custom_number']){
        $dogovorNumber = $res_dogovor['number_client'];
    }else {
        $dogovorNumber = $res_dogovor['NUMBER'];
    }

    if(!empty($tzId)) {
        $res_dog1 = $DB->Query("UPDATE `DOGOVOR` SET `ACTUAL_VER` ='" . $curdate . "' WHERE `TZ_ID`=" . $tzId);
    }else {
        $res_dog1 = $DB->Query("UPDATE `DOGOVOR` SET `ACTUAL_VER` ='" . $curdate . "' WHERE `ID`=" . $_GET['ID_C']);
    }

    $str_type = 'Обновление договора';

} else {
    $maxNumber = $DB->Query("SELECT SUBSTRING_INDEX(NUMBER, '/', 1) as num FROM `DOGOVOR` WHERE (`CLIENT_NUMBER`='0' OR `CLIENT_NUMBER` IS NULL) AND CONTRACT_TYPE = 'Договор'  ORDER BY `ID` DESC LIMIT 1")->Fetch()['num'];
    $curYear = substr(date("Y"), -2);
//    $arrNumberDogovor = !empty($maxDogovor['NUMBER']) ? explode('/',$maxDogovor['NUMBER']) : '';
//    $maxNumberDogovor = !empty($arrNumberDogovor[0])&&$arrNumberDogovor[1]==$curYear ? (int)$arrNumberDogovor[0] : '';
    $numberDogovor = ($maxNumber<9 ? '0' : '').($maxNumber + 1) . '/'.$curYear;

    $res_dog1 = $DB->Query("INSERT INTO `DOGOVOR` (`DATE`, `TZ_ID`, `CLIENT_ID`, `type_id`, `dogovor_type_id`) VALUES (NOW(), " . $tzId. ", ".$deal['COMPANY_ID']. ", 1, 1)" );
    //$idDogovor = $DB->Query("SELECT `ID` FROM `DOGOVOR` ORDER BY `ID` DESC LIMIT 1")->Fetch();
    
    $str_type = 'Формирование договора';

    $idDogovor = $DB->Query("SELECT `ID` FROM `DOGOVOR` WHERE `type_id` = 1 OR `dogovor_type_id` = 1 ORDER BY `ID` DESC LIMIT 1")->Fetch();

	if (!empty($idDogovor['ID'])){
		$dateDogovor = date("d-m-Y_H-i-s");
		$dogovorDate = !empty($dateDogovor) ? explode('_',$dateDogovor)[0] : '';

		$arNumberDogovor = !empty($numberDogovor) ? explode('/', $numberDogovor) : '';
		$numDogovor = !empty($arNumberDogovor[0]) ? $arNumberDogovor[0] : '';

		$curdate = "Договор_". $numDogovor .'_'. $dateDogovor;
		$curdateEng = "Contract_". $numDogovor .'_'. $dateDogovor;
		$pathDogovor = $idDogovor['ID'];
		$dogovorNumber = $numberDogovor;

		$updateDogovor = $DB->Query("UPDATE `DOGOVOR` SET `NUMBER` ='". $numberDogovor ."', `ACTUAL_VER` ='".$curdate."' WHERE `ID`=" . (int)$idDogovor['ID']);

		$resDog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$idDogovor['ID'])->Fetch();
		$resDogovorNum = addNumContract($tzId, $id, $resDog);
		$dogovorNum = !empty($resDogovorNum) ? $resDogovorNum : '';

        $dogovor_table_arr = !empty($dogovorNum) ? explode(',', $dogovorNum) : '';
        $dogovor_table = !empty($dogovor_table_arr[0]) ? $dogovor_table_arr[0] : '';
		$updateBaTz = $DB->Query("UPDATE `ba_tz` SET `DOGOVOR_NUM` ='".$dogovorNum."', `DOGOVOR_TABLE` ='". $dogovor_table ."' WHERE `ID_Z`=" . $id);

        $insertDeals = $DB->Query("INSERT INTO `DEALS_TO_CONTRACTS` (`ID_CONTRACT`, `ID_DEAL`) VALUES (".(int)$idDogovor['ID'].", ".(int)$id.")");
	}
}

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




if(!empty($ID)) {
    $update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('EXECUTING') . "' WHERE `ID_Z`=" . $ID);
}

$comPhones = CCrmFieldMulti::GetList(array(), array('TYPE_ID' => 'PHONE', 'ELEMENT_ID' => $deal['COMPANY_ID']));
$comEmails = CCrmFieldMulti::GetList(array(), array('TYPE_ID' => 'EMAIL', 'ELEMENT_ID' => $deal['COMPANY_ID']));
while ($Phone = $comPhones->Fetch()) {
    $arMultiFields['PHONE'][] = $Phone['VALUE'];
};
while ($Email = $comEmails->Fetch()) {
    $arMultiFields['EMAIL'][] = $Email['VALUE'];
};

$idForSql = $deal['COMPANY_ID'];

if(!empty($_GET['ID_C']) && empty($idForSql)) {
    $idForSql = $DB->Query("SELECT CLIENT_ID FROM DOGOVOR WHERE ID = '{$_GET['ID_C']}'")->Fetch()['CLIENT_ID'];
}

$arRequisite = new \Bitrix\Crm\EntityRequisite();
$dbRes = $arRequisite->getList(["filter" => ["ENTITY_ID" => $idForSql, "ENTITY_TYPE_ID" => CCrmOwnerType::Company]]);

$req = $dbRes->fetch();

//echo "<pre>";
//print_r($req);
//echo "</pre>";
//die();
$impName = $req['RQ_COMPANY_REG_DATE'];

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



    $actual_address = Bitrix\Crm\EntityRequisite::getAddresses($companyReq['ID']);

}


$actsBasis = !empty($actual_address[1]["ADDRESS_2"]) ? $actual_address[1]["ADDRESS_2"] : '';//Действует на основании


$actsBasis = mb_strtoupper_first($actsBasis, $encoding = 'UTF8');

$onBasis = "Устава";
$qqq = EntityRequisite::getAddresses($req['ID']);
if ( !empty($qqq) && !empty($qqq[1]['ADDRESS_2']) ) {
    $onBasis = $qqq[1]['ADDRESS_2'];
}

if (!empty($ba_tz['CHECK_IP']) && $ba_tz['CHECK_IP'] == 1) {
	$ogrnip = !empty($actual_address[1]["REGION"]) ? $actual_address[1]["REGION"] : '';//ОГРНИП
}


//echo "<pre>";
//dump($bankReq);
//exit;

$address = Bitrix\Crm\EntityRequisite::getAddresses($deal['COMPANY_ID']);

global $DB;
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
$ba_tz = $res->Fetch();


$date_save = date('Y-m-d H:i:s');
$assigned = CUser::GetByID($USER->GetID())->arResult[0];

$assigned_name = !empty($assigned['NAME']) ? $assigned['NAME'] : '';

$assigned_lastname = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';

$assigned_user = $assigned_name.' '.$assigned_lastname;

$ID_P = $ba_tz['NUM_P'] ? $ba_tz['NUM_P'] : 'номер не присвоен';
$NDS_TYPE = $ba_tz['is_nds_7'] ? '7' : '5';

$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';

/*$str_type = '';
if (isset($_GET['ID_C'])) {
    $str_type = 'Скачивание договора';
} else {
    $str_type = 'Формирование договора';
}*/



$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date_save . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$tzId . "','" . $USER->GetID() . "', '" . $str_type . "', '" . $num_request . "')");



if ($ba_tz['ID'])
{
    $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$tzId . " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
    $history_ass=$history['ASSIGNED'];
    $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
    $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
    if($history_date<$history_date_null) $history_date='';


    $assignet_history = $history_ass . " " . $history_date;

    $update_assignet_ba_tz = $DB->Query("UPDATE `ba_tz` SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . $ID);
}

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


// ! v2 если есть в базе, берём оттуда, иначе склоняем сами (табличка POSIT_LEADS есть поле в родительном падеже - POSIT_FULL)

if(!empty($_GET['TZ_ID'])) {
    $posit_list = $DB->Query("SELECT `POSIT_LEADS` FROM `ba_tz` WHERE `ID` = {$_GET['TZ_ID']}")->Fetch();
}

//echo "<pre>";
//print_r($posit_list);
//echo "</pre>";
//die();

if (!empty($posit_list['POSIT_LEADS'])){

    $position = $req['RQ_COMPANY_REG_DATE'];

    $fioDerector = $req['RQ_DIRECTOR'];

//    echo "<pre>";
//    print_r($posit_list);
//    echo "</pre>";
//    die();

    $positionGenitive = $posit_list['POSIT_LEADS'];

    $fioDerectorGenitive = null;
    if (!empty($fioDerector)){
        $arrfioDerector = conversion($fioDerector);
        if ($arrfioDerector !== false) {
            foreach (array(morphos\Russian\Cases::RODIT => 'Родительный') as $case => $name) {
                $fioDerectorGenitive = $arrfioDerector[$case];
            }
        }
    }
//    if (!empty($position)){
//        $arrPosition = conversion($position);
//        if ($arrPosition !== false) {
//            foreach (array(morphos\Russian\Cases::RODIT => 'Родительный') as $case => $name) {
//                $positionGenitive = $arrPosition[$case];
//            }
//        }
//    }
} else {

    $position = $req['RQ_COMPANY_REG_DATE'];

    $fioDerector = $req['RQ_DIRECTOR'];
    $position = $req['RQ_COMPANY_REG_DATE'];

    $fioDerectorGenitive = null;
    if (!empty($fioDerector)) {
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
}

//echo "<pre>";
//print_r($arrPosition);
//echo "</pre>";
//die();

/*$animate = 'on';
if (!empty($position)){
    $arrPosition = morphos\Russian\NounDeclension::getCases($position, $animate);

    if ($arrPosition !== false) {
        foreach (array(morphos\Russian\Cases::RODIT => 'Родительный') as $case => $name) {
            $positionGenitive = $arrPosition[$case];
        }
    }
}
$positionGenitive = !empty($positionGenitive) ? $positionGenitive : null;*/



$animate = NULL;
$positionItem = '';
if (!empty($position)){

    $countPosition = explode(' ', $position);


    $arrRodit = ['a', 'я', 'и', 'ы'];

    foreach ($countPosition as $key => $valuePosition) {


        if ($key != 0 && in_array(substr($valuePosition, -1), $arrRodit)) {
            $positionItem .= $valuePosition. ' ';
            continue;
        }

        $arrPosition = morphos\Russian\NounDeclension::getCases($valuePosition, $animate);

        if ($arrPosition !== false) {
            foreach (array(morphos\Russian\Cases::RODIT => 'Родительный') as $case => $name) {

                if ($key == 0) {

                    /*function mb_strtoupper_first($str, $encoding = 'UTF8')
                    {
                        return
                            mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
                            mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
                    }*/

                    $arrPosition[$case] = mb_strtoupper_first($arrPosition[$case], $encoding = 'UTF8');
                }

                $positionItem .= $arrPosition[$case] . ' ';
            }
        }
    }
}


//if(!empty($req['RQ_LAST_NAME']))
//$positionGenitive = $req['RQ_LAST_NAME'];
//else
//$positionGenitive = !empty($positionItem) ? trim($positionItem) : null;
//}


$fioDerectorGenitive = !empty($fioDerectorGenitive) ? $fioDerectorGenitive : null;

if(!empty($TZ_ID)) {
    $res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
}else {
    $res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . $_GET['ID_C'])->Fetch();
}
$LSS = $bankReq['COMMENTS'];

$contractEndDate = "31 декабря 2024 г.";
if($res_dogovor['FLOW_DATE'])
{
	$_monthsList = array(".01." => "января", ".02." => "февраля",
	".03." => "марта", ".04." => "апреля", ".05." => "мая", ".06." => "июня",
	".07." => "июля", ".08." => "августа", ".09." => "сентября",
	".10." => "октября", ".11." => "ноября", ".12." => "декабря");
	$contractEndDate = date('d.m.Y', strtotime("+11 months"));
	$_mD = date(".m.", strtotime("+11 months")); //для замены
	$contractEndDate = str_replace($_mD, " ".$_monthsList[$_mD]." ", $contractEndDate);

	//echo $contractEndDate;
}
//
//echo "<pre>";
//print_r($deal);
//echo "</pre>";
//die();
//

// Приводим всю строку к нижнему регистру
$lowercaseString = mb_strtolower($positionGenitive, 'UTF-8');

// Делаем первую букву строки заглавной
$positionGenitive = mb_convert_case(mb_substr($lowercaseString, 0, 1, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') .
    mb_substr($lowercaseString, 1, null, 'UTF-8');



$companyName = $deal['COMPANY_TITLE'];



if(empty($deal['COMPANY_TITLE'])) {
    $companyName = $DB->Query("SELECT TITLE FROM b_crm_company WHERE ID = '{$req['ENTITY_ID']}'")->Fetch()['TITLE'];
}

$document->normalizeContent();
$document->setValues([

    'Year' => date('Y'),

 //'NumberDogovor' => $res_dog['NUMBER'] ? $res_dog['NUMBER'] : $res_dog['ID'],
	'NumberDogovor' => $dogovorNumber,
	'NumberDogovorS' => substr($dogovorNumber, 0, 8),
    //'DateDogovor' => date('d.m.Y', strtotime($deal['DATE_CREATE'])),
	'DateDogovor' => date('d.m.Y', strtotime($dogovorDate)),
	'contractEndDate' => $contractEndDate,
    'Company' => $companyName,
	'Position' => $positionGenitive,
    'Position2' => $position,
    'PositionIMP' => $impName,
	// 'Position2' => $req['RQ_COMPANY_REG_DATE'],
	'FIOCompany' => $fioDerectorGenitive,
	'onBasis' => $onBasis,
    'NDS_TYPE' => $NDS_TYPE,

	'FIOCompanyIP' => $fioDerector,
	'FIOCompanyIP2' => $fioDerector,

    //'FIOCompany' => $req['RQ_DIRECTOR'],
    'CompanyFullName' => $req['RQ_COMPANY_FULL_NAME'],
	'DirectorFIO' => $directorFIO,

    'UAdres' => $req['RQ_ACCOUNTANT'],//$address[6]["ADDRESS_1"] . ',' . $address[6]["CITY"] . ',' . $address[6]["PROVINCE"],
    'phone' => $req['RQ_PHONE'],
    //'Email' => $req['RQ_EMAIL'],

	'Email' => !empty($req['RQ_FIRST_NAME']) ? $req['RQ_FIRST_NAME'] : $req['RQ_EMAIL'],
    'mailingAddress' => !empty($req['RQ_COMPANY_NAME']) ? $req['RQ_COMPANY_NAME'] : '',//Почтовый адрес
    'OGRN' => $req['RQ_OGRN'],

	'ogrnip' => $ogrnip,

    'INN' => $req['RQ_INN'],
    'KPP' => $req['RQ_KPP'],
	'LSS' => ($bankReq['COMMENTS'] && $bankReq['COMMENTS'] != '') ? "Л/c ".$LSS : "",
    'RaschSchet' => $bankReq['RQ_ACC_NUM'],
    'KSchet' => $bankReq['RQ_COR_ACC_NUM'],
    'BIK' => $bankReq['RQ_BIK'],
	'BankName' => $bankReq['RQ_BANK_NAME'],

    'ActualAddress' => !empty($actual_address[1]["ADDRESS_1"]) ? $actual_address[1]["ADDRESS_1"] : '',//Фактический Адрес


    'ActsBasis' => $actsBasis,


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

//$res_dog2 = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();

/*$newpath = "archive_dog/".$ID."/".$curdate.".docx";

$interimPath = "interim_archive_dog/".$ID."/".$curdate.".docx";
$outputFile = $curdate.".pdf";
$outputPath = "interim_archive_dog/".$ID."/";*/

//$newpath = "archive_dog/".$ID."/".$curdate.".docx";
/*$newpath = "archive_dog/".$pathDogovorNum."/".$curdate.".docx";

//$interimPath = "interim_archive_dog/".$ID."/".$curdateEng.".docx";
$interimPath = "interim_archive_dog/".$pathDogovorNum."/".$curdateEng.".docx";
$outputFile = $curdate.".pdf";
//$outputPath = "archive_dog/".$ID."/";
$outputPath = "archive_dog/".$pathDogovorNum."/";*/


$newpath = "archive_dog/".$pathDogovor."/".$curdate.".docx";


$interimPath = "interim_archive_dog/".$pathDogovor."/".$curdateEng.".docx";
$interimPath1 = "interim_archive_dog/".$pathDogovor."/".$curdateEng."1.docx";

$outputFile = $curdate.".pdf";

$outputPath = "archive_dog/".$pathDogovor."/";



function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );

    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );

    file_put_contents( $fullPath, $contents, $flags );
}



/*file_force_contents($interimPath, $content);


$converter  =  new NcJoes\OfficeConverter\OfficeConverter( './interim_archive_dog/1162/Договор_81_02-04-2020_12-38-48.docx' );
$converter->convertTo( 'output-file.pdf' );*/
//$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_dog/'.$pathDogovorNum;
$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_dog/'.$pathDogovor;
//if( !is_dir( $newDirectory ) ) {
//	mkdir($newDirectory, 0777, true);
//}

// зачем?
//file_force_contents($interimPath, $content);


$fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$newpath;
$fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$outputPath;

file_force_contents($fullPathDocx, $content);

//$document2->normalizeContent();
//$document2->setValues([
//
//    'Year' => date('Y'),
//
// //'NumberDogovor' => $res_dog['NUMBER'] ? $res_dog['NUMBER'] : $res_dog['ID'],
//	'NumberDogovor' => $dogovorNumber,
//	'NumberDogovorS' => substr($dogovorNumber, 0, 8),
//    //'DateDogovor' => date('d.m.Y', strtotime($deal['DATE_CREATE'])),
//	'DateDogovor' => date('d.m.Y', strtotime($dogovorDate)),
//	'contractEndDate' => $contractEndDate,
//    'Company' => $deal['COMPANY_TITLE'],
//	'Position' => $positionGenitive,
//	// 'Position2' => $req['RQ_COMPANY_REG_DATE'],
//	'Position2' => $position,
//	'FIOCompany' => $fioDerectorGenitive,
//
//	'FIOCompanyIP' => $fioDerector,
//    'FIOCompanyIP2' => $fioDerector,
//
//    //'FIOCompany' => $req['RQ_DIRECTOR'],
//    'CompanyFullName' => $req['RQ_COMPANY_FULL_NAME'],
//	'DirectorFIO' => $directorFIO,
//
//    'UAdres' => $req['RQ_ACCOUNTANT'],//$address[6]["ADDRESS_1"] . ',' . $address[6]["CITY"] . ',' . $address[6]["PROVINCE"],
//    'phone' => $req['RQ_PHONE'],
//    //'Email' => $req['RQ_EMAIL'],
//
//	'Email' => !empty($req['RQ_FIRST_NAME']) ? $req['RQ_FIRST_NAME'] : '-',
//
//    'mailingAddress' => !empty($req['RQ_COMPANY_NAME']) ? $req['RQ_COMPANY_NAME'] : '',
//
//    'OGRN' => $req['RQ_OGRN'],
//
//	'ogrnip' => $ogrnip,
//
//    'INN' => $req['RQ_INN'],
//    'KPP' => $req['RQ_KPP'],
//    'LSS' => ($bankReq['COMMENTS'] && $bankReq['COMMENTS'] != '') ? "Л/c ".$LSS : "",
//    'RaschSchet' => $bankReq['RQ_ACC_NUM'],
//    'KSchet' => $bankReq['RQ_COR_ACC_NUM'],
//    'BIK' => $bankReq['RQ_BIK'],
//	'BankName' => $bankReq['RQ_BANK_NAME'],
//
//    'ActualAddress' => !empty($actual_address[1]["ADDRESS_1"]) ? $actual_address[1]["ADDRESS_1"] : '',
//
//
//    'ActsBasis' => $actsBasis,
//
//
//    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
//        $tz_arr,
//        [
//            'ITEM_NAME' => 'Item',
//            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
//        ]
//    ),
//    'TableItemName' => 'Table.Item.Name',
//    'TableItemSpec' => 'Table.Item.Spec',
//    'TableItemV' => 'Table.Item.V',
//
//
//    'Pres' => new DocumentGenerator\DataProvider\ArrayDataProvider(
//        $results,
//        [
//            'ITEM_NAME' => 'Item',
//            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
//        ]
//    ),
//    'PresItemProd' => 'Pres.Item.Prod',
//    'PresItemSpec' => 'Pres.Item.Spec',
//    'PresItemPrice' => 'Pres.Item.Price',
//]);
//
//$result2 = $document2->process();
//$content2 = $document2->getContent();

//if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
//$generator = new Picqer\Barcode\BarcodeGeneratorPNG();


//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/barcode.png', $generator->getBarcode("Contract{$dogovorNumber}-bitrix202", $generator::TYPE_CODE_128_B));

$templateProc = new PhpOffice\PhpWord\Template($fullPathDocx);
//$templateProc->setImageValue('image1.png', $_SERVER['DOCUMENT_ROOT'] . '/barcode.png');
$templateProc->saveAs($fullPathDocx);
//	$templateProc->saveAs($_SERVER['DOCUMENT_ROOT'] . '/testDog1.docx');
//	$templateProc->save();
//}


$c = file_get_contents($fullPathDocx);

file_force_contents($newDirectory . '/1.docx', $c);

$converter = new OfficeConverter($newDirectory . '/1.docx', $newDirectory);//, $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/');
//$converter->convertTo($outputFile); // генерирует pdf файл в том же каталоге, что и test-file.docx
$converter->convertTo($curdate.'.pdf'); // генерирует pdf файл в том же каталоге, что и test-file.docx

unlink($newDirectory . '/1.docx');

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="'.$curdate.'.docx"');
header('Cache-Control: max-age=0');
echo $c;
die;
