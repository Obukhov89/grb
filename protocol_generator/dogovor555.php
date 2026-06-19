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

//-------------'Конвертация pdf' начало-----------------//
require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';
//-------------'Конвертация pdf' окончание-----------------//

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

//--'Договор для ИП' начало--// 
/*$file = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor.docx');
$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor2.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
$document2 = new Bitrix\DocumentGenerator\Body\Docx($file2->getContents());*/

//global $DB;
$ba_tz = null;
if (!empty($_GET['TZ_ID'])) {
	$ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID'])->Fetch();
}

//Если $ba_tz['CHECK_IP'] == 1, то request_new.php отмечен checkbox ИП  
if (!empty($ba_tz['CHECK_IP']) && $ba_tz['CHECK_IP'] == 1) {
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor_ip.docx');
	$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor2_ip.docx');
} else {
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor.docx');
	$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../Dogovor2.docx');
}

$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
$document2 = new Bitrix\DocumentGenerator\Body\Docx($file2->getContents());

//--'Договор для ИП' окончание--//

//Запрашиваем данные
//----------------------'Серверная обработка Журнала заявок' начало---------------------//
//$ID = $_GET['ID']; //ID Заявки
$ID = !empty($_GET['ID']) ? (int)$_GET['ID'] : '';
//----------------------'Серверная обработка Журнала заявок' окончание-																																   
$TZ_ID = $_GET['TZ_ID']; //ID Технического задания. В нём хранится ТЗ, результаты исследований и информация о пробах
$mater = $_GET['mater']; // ID материала, указанного в ТЗ
$probe_ID = $_GET['probe']; // ID пробы, указанного в пробах, заполненных на основе ТЗ

$deal = CCrmDeal::GetByID($ID); //Берём значение сделки
$company = CCrmCompany::GetByID($deal['COMPANY_ID']); //Берём значение компании
$companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch(); //Берём значение компании


// //-------------'Конвертация pdf' начало-----------------//
// $res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
// $dateDogovor = date("d-m-Y_H-i-s");
// $curdate = "Договор_".$res_dog['ID'].'_'.$dateDogovor;
// //-------------'Конвертация pdf' окончание-----------------//
//----------------------------'договор еще не составлен' начало --------------//
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
//----------------------------'договор еще не составлен' начало --------------//
//-------------'Конвертация pdf' начало-----------------//
// $res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
// $curdate = "Договор_".$res_dog['ID'].'_'.$dateDogovor;
// $curdateEng = "Contract_".$res_dog['ID'].'_'.$dateDogovor;
//-------------'Конвертация pdf' окончание-----------------//
$tzId = !empty($TZ_ID) ? (int)$TZ_ID : '';
$id = !empty($ID) ? (int)$ID : '';	

$res_deals = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$ID)->Fetch();
$res_dogovor = null;
if (!empty($res_deals['ID_CONTRACT'])) {
	$res_dogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$res_deals['ID_CONTRACT'])->Fetch();
}

$curdate = null;
$curdateEng = null;
//$pathDogovorNum = null;
/*-----------------'Договор Клиента' начало--------------*/
$pathDogovor = null;
/*-----------------'Договор Клиента' окончание--------------*/
$dogovorNumber = null;
$dogovorDate = null;

//-------------'Сохранение истории' начало-----------------//
$str_type = '';
//-------------'Сохранение истории' окончание-----------------//																					
if (!empty($res_dogovor['NUMBER'])) {
	$timeDogovor = date("d-m-Y_H-i-s");
	//$dateDogovor = $res_dogovor['DATE'];
	$dogovorDate = $res_dogovor['DATE'];
	/*-----------------'Договор Клиента' начало--------------*/
	$arNumberDogovor = !empty($res_dogovor['NUMBER']) ? explode('/', $res_dogovor['NUMBER']) : '';
	$numDogovor = !empty($arNumberDogovor[0]) ? $arNumberDogovor[0] : '';
	//$curdate = "Договор_".$res_dogovor['NUMBER'].'_'. $timeDogovor;
	//$curdateEng = "Contract_".$res_dogovor['NUMBER'].'_'. $timeDogovor;
	$curdate = "Договор_".$numDogovor.'_'. $timeDogovor;
	$curdateEng = "Contract_".$numDogovor.'_'. $timeDogovor;
	//$pathDogovorNum = $res_dogovor['NUMBER'];
	
	$pathDogovor = $res_dogovor['ID'];
	/*-----------------'Договор Клиента' окончание--------------*/
	//$pathDogovorNum = $res_dogovor['ID'];
	$dogovorNumber = $res_dogovor['NUMBER'];
	
	$res_dog1 = $DB->Query("UPDATE `DOGOVOR` SET `ACTUAL_VER` ='".$curdate."' WHERE `TZ_ID`=" . $tzId);
    //-------------'Сохранение истории' начало-----------------//
    $str_type = 'Обновление договора';
//-------------'Сохранение истории' окончание-----------------//
} else {
	$res_dog1 = $DB->Query("INSERT INTO `DOGOVOR` (`DATE`, `TZ_ID`, `CLIENT_ID`) VALUES (NOW(), " . $tzId. ", ".$deal['COMPANY_ID'].")" );
	//$idDogovor = $DB->Query("SELECT `ID` FROM `DOGOVOR` ORDER BY `ID` DESC LIMIT 1")->Fetch();
	/*-----------------'Договор Клиента' начало--------------*/
    //-------------'Сохранение истории' начало-----------------//
    $str_type = 'Формирование договора';
//-------------'Сохранение истории' окончание-----------------//
	$idDogovor = $DB->Query("SELECT `ID` FROM `DOGOVOR` ORDER BY `ID` DESC LIMIT 1")->Fetch();
	
	//$maxDogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `CLIENT_NUMBER`!='1' ORDER BY `ID` DESC LIMIT 1")->Fetch();
	$maxDogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `CLIENT_NUMBER`='0' OR `CLIENT_NUMBER` IS NULL ORDER BY `ID` DESC LIMIT 1,1")->Fetch();
	
	$arrNumberDogovor = !empty($maxDogovor['NUMBER']) ? explode('/',$maxDogovor['NUMBER']) : '';
	$maxNumberDogovor = !empty($arrNumberDogovor[0]) ? (int)$arrNumberDogovor[0] : '';
	$numberDogovor = ($maxNumberDogovor + 1) . '/2020';
	/*-----------------'Договор Клиента' окончание--------------*/
	
	if (!empty($idDogovor['ID'])){
		$dateDogovor = date("d-m-Y_H-i-s");
		$dogovorDate = !empty($dateDogovor) ? explode('_',$dateDogovor)[0] : '';
		//$pathDogovorNum =  (string)((int)$idDogovor['ID']-13);
		//$curdate = "Договор_".$pathDogovorNum.'_'. $dateDogovor;
		//$curdateEng = "Contract_".$pathDogovorNum.'_'. $dateDogovor;
		/*$dogovorNumber = (string)((int)$idDogovor['ID']-13);
		$curdate = "Договор_".$dogovorNumber.'_'. $dateDogovor;
		$curdateEng = "Contract_".$dogovorNumber.'_'. $dateDogovor;
		$pathDogovorNum = $idDogovor['ID'];
				
		$updateDogovor = $DB->Query("UPDATE `DOGOVOR` SET `NUMBER` ='".((int)$idDogovor['ID']-13)."', `ACTUAL_VER` ='".$curdate."' WHERE `ID`=" . (int)$idDogovor['ID']);*/
		/*-----------------'Договор Клиента' начало--------------*/
		$arNumberDogovor = !empty($numberDogovor) ? explode('/', $numberDogovor) : '';
		$numDogovor = !empty($arNumberDogovor[0]) ? $arNumberDogovor[0] : '';
		//$curdate = "Договор_". $numberDogovor .'_'. $dateDogovor;
		//$curdateEng = "Contract_". $numberDogovor .'_'. $dateDogovor;
		$curdate = "Договор_". $numDogovor .'_'. $dateDogovor;
		$curdateEng = "Contract_". $numDogovor .'_'. $dateDogovor;
		$pathDogovor = $idDogovor['ID'];
		$dogovorNumber = $numberDogovor;
		
		$updateDogovor = $DB->Query("UPDATE `DOGOVOR` SET `NUMBER` ='". $numberDogovor ."', `ACTUAL_VER` ='".$curdate."' WHERE `ID`=" . (int)$idDogovor['ID']);
		/*-----------------'Договор Клиента' окончание--------------*/
		
		$resDog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$idDogovor['ID'])->Fetch();
		$resDogovorNum = addNumContract($tzId, $id, $resDog);
		$dogovorNum = !empty($resDogovorNum) ? $resDogovorNum : '';
       //----------------------'Серверная обработка Журнала заявок' начало---------------------//
        $dogovor_table_arr = !empty($dogovorNum) ? explode(',', $dogovorNum) : '';
        $dogovor_table = !empty($dogovor_table_arr[0]) ? $dogovor_table_arr[0] : '';
		$updateBaTz = $DB->Query("UPDATE `ba_tz` SET `DOGOVOR_NUM` ='".$dogovorNum."', `DOGOVOR_TABLE` ='". $dogovor_table ."' WHERE `ID_Z`=" . $id);
        //----------------------'Серверная обработка Журнала заявок' окончание---------------------//
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

//----------------------'Серверная обработка Журнала заявок' начало---------------------//
//$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос.
//Обновляем стадию изменения в ba_tz
$update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('EXECUTING') . "' WHERE `ID_Z`=" . $ID);
//----------------------'Серверная обработка Журнала заявок' окончание---------------------//   
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

    //---------------------------------'Фактический Адрес' начало -------------------------//
    //принимает ИД реквизита, на выходе отдает массив с адресами фактическими
    $actual_address = Bitrix\Crm\EntityRequisite::getAddresses($companyReq['ID']);
    //---------------------------------'Фактический Адрес' окончание -------------------------//
}

//------------------------------'Действует на основании' начало------------------------//
$actsBasis = !empty($actual_address[1]["ADDRESS_2"]) ? $actual_address[1]["ADDRESS_2"] : '';//Действует на основании

//преобразуем певую букву в должности в верхний регистр
$actsBasis = mb_strtoupper_first($actsBasis, $encoding = 'UTF8');
//---------------------------------'Действует на основании' окончание -----------------//

//--'Договор для ИП' начало--// 
//Если $ba_tz['CHECK_IP'] == 1, то request_new.php отмечен checkbox ИП  
if (!empty($ba_tz['CHECK_IP']) && $ba_tz['CHECK_IP'] == 1) {
	$ogrnip = !empty($actual_address[1]["REGION"]) ? $actual_address[1]["REGION"] : '';//ОГРНИП
} 
//--'Договор для ИП' окончание--//

//echo "<pre>";
//dump($bankReq);
//exit;

$address = Bitrix\Crm\EntityRequisite::getAddresses($deal['COMPANY_ID']);

global $DB;
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
$ba_tz = $res->Fetch();
//-------------'Сохранение истории' начало-----------------//
//Дата сохранения договора в историю
$date_save = date('Y-m-d H:i:s');
$assigned = CUser::GetByID($USER->GetID())->arResult[0];
//Имя пользователя
$assigned_name = !empty($assigned['NAME']) ? $assigned['NAME'] : '';
//Фамилия пользователя
$assigned_lastname = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';
//Пользователь
$assigned_user = $assigned_name.' '.$assigned_lastname;
//Номер протокола
$ID_P = $ba_tz['NUM_P'] ? $ba_tz['NUM_P'] : 'номер не присвоен';
$NDS_TYPE = $ba_tz['is_nds_7'] ? '7' : '5';
//Номер заявки
$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';

/*$str_type = '';
if (isset($_GET['ID_C'])) {
    $str_type = 'Скачивание договора';
} else {
    $str_type = 'Формирование договора';
}*/

//---------'Изменение номера ТЗ' начало ------//
//Сохранение договора в истории
$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date_save . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$tzId . "','" . $USER->GetID() . "', '" . $str_type . "', '" . $num_request . "')");
//---------'Изменение номера ТЗ' окончание ------//
//-------------'Сохранение истории' окончание-----------------//

if ($ba_tz['ID'])
{
    $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$tzId . " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
    $history_ass=$history['ASSIGNED'];
    $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
    $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
    if($history_date<$history_date_null) $history_date='';

    //Колонка "последнее изменение"
    $assignet_history = $history_ass . " " . $history_date;

    $update_assignet_ba_tz = $DB->Query("UPDATE `ba_tz` SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . $ID);
}
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
/*if (!empty($position)){
	$arrPosition = conversion($position);
	
	if ($arrPosition !== false) {
		foreach (array(morphos\Russian\Cases::RODIT => 'Родительный') as $case => $name) {
			$positionGenitive = $arrPosition[$case];
		}	
	}
}*/

//Существительное одущевленное, Дательный падеж, склоняем должность
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

//Существительное одущевленное, Дательный падеж, склоняем должность
//$animate = 'on'; //Одущевленное существительное
$animate = NULL; //не одущевленное существительное
$positionItem = '';
if (!empty($position)){
    //Разбиваем должность на массив
    $countPosition = explode(' ', $position);

    //массив с окончаниями родительного падежа
    $arrRodit = ['a', 'я', 'и', 'ы'];
    //Склоняем каждое слово отдельно
    foreach ($countPosition as $key => $valuePosition) {

        //Проверяем если второе слово(должности) в родительном падеже (сравниваем по окончаниям),то его не склоняем
        if ($key != 0 && in_array(substr($valuePosition, -1), $arrRodit)) {
            $positionItem .= $valuePosition. ' ';
            continue;
        }

        $arrPosition = morphos\Russian\NounDeclension::getCases($valuePosition, $animate);

        if ($arrPosition !== false) {
            foreach (array(morphos\Russian\Cases::RODIT => 'Родительный') as $case => $name) {

                if ($key == 0) {
                    //преобразуем певую букву в должности в верхний регистр
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

//Строка должности
if(!empty($req['RQ_LAST_NAME']))
$positionGenitive = $req['RQ_LAST_NAME'];
else
$positionGenitive = !empty($positionItem) ? trim($positionItem) : null;

$fioDerectorGenitive = !empty($fioDerectorGenitive) ? $fioDerectorGenitive : null;

$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch(); 
echo $bankReq['COMMENTS'];
//Заполняем данные
$document->normalizeContent();
$document->setValues([

    'Year' => date('Y'),

 //'NumberDogovor' => $res_dog['NUMBER'] ? $res_dog['NUMBER'] : $res_dog['ID'],
	'NumberDogovor' => $dogovorNumber,
    //'DateDogovor' => date('d.m.Y', strtotime($deal['DATE_CREATE'])),
	'DateDogovor' => date('d.m.Y', strtotime($dogovorDate)),
    'Company' => $deal['COMPANY_TITLE'],
	'Position' => $positionGenitive, 
	'Position2' => $req['RQ_COMPANY_REG_DATE'],
	'FIOCompany' => $fioDerectorGenitive,
    'NDS_TYPE' => $NDS_TYPE,
	//--'Договор для ИП' начало--//
	'FIOCompanyIP' => $fioDerector,
	//--'Договор для ИП' окончание--//
    //'FIOCompany' => $req['RQ_DIRECTOR'],
    'CompanyFullName' => $req['RQ_COMPANY_FULL_NAME'],
	'DirectorFIO' => $directorFIO,
//========================== окончание 'ФИО и ДОЛЖНОСТЬ родитетьный падеж' ==================================//
    'UAdres' => $req['RQ_ACCOUNTANT'],//$address[6]["ADDRESS_1"] . ',' . $address[6]["CITY"] . ',' . $address[6]["PROVINCE"],
    'phone' => $req['RQ_PHONE'],
    //'Email' => $req['RQ_EMAIL'],
	//Если есть Дополнительный email выводим его, иначе выводи основной email
	'Email' => !empty($req['RQ_FIRST_NAME']) ? $req['RQ_FIRST_NAME'] : $req['RQ_EMAIL'],
    'mailingAddress' => !empty($req['RQ_COMPANY_NAME']) ? $req['RQ_COMPANY_NAME'] : '',//Почтовый адрес
    'OGRN' => $req['RQ_OGRN'],
	//--'Договор для ИП' начало--// 
	'ogrnip' => $ogrnip,
	//--'Договор для ИП' окончание--//
    'INN' => $req['RQ_INN'],
    'KPP' => $req['RQ_KPP'],
	'LS' => $bankReq['COMMENTS'],//($bankReq['COMMENTS'] && $bankReq['COMMENTS'] != '') ? "Лицевой счет: ".$bankReq['COMMENTS'] : "",
    'RaschSchet' => $bankReq['RQ_ACC_NUM'],
    'KSchet' => $bankReq['RQ_COR_ACC_NUM'],
    'BIK' => $bankReq['RQ_BIK'],
	'BankName' => $bankReq['RQ_BANK_NAME'],
    //---------------------------------'Фактический Адрес' начало -------------------------//
    'ActualAddress' => !empty($actual_address[1]["ADDRESS_1"]) ? $actual_address[1]["ADDRESS_1"] : '',//Фактический Адрес
    //---------------------------------'Фактический Адрес' окончание -------------------------//
    //------------------------------'Действует на основании' начало------------------------//
    'ActsBasis' => $actsBasis,//Действует на основании
    //---------------------------------'Действует на основании' окончание -----------------//

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
//-------------'Конвертация pdf' начало-----------------//
/*$newpath = "archive_dog/".$ID."/".$curdate.".docx";

$interimPath = "interim_archive_dog/".$ID."/".$curdate.".docx";
$outputFile = $curdate.".pdf";
$outputPath = "interim_archive_dog/".$ID."/";*/

//$newpath = "archive_dog/".$ID."/".$curdate.".docx";
/*$newpath = "archive_dog/".$pathDogovorNum."/".$curdate.".docx";
//-------------'Конвертация pdf'-----------------//
//$interimPath = "interim_archive_dog/".$ID."/".$curdateEng.".docx";
$interimPath = "interim_archive_dog/".$pathDogovorNum."/".$curdateEng.".docx";
$outputFile = $curdate.".pdf";
//$outputPath = "archive_dog/".$ID."/";
$outputPath = "archive_dog/".$pathDogovorNum."/";*/
/*-----------------'Договор Клиента' начало--------------*/

$newpath = "archive_dog/".$pathDogovor."/".$curdate.".docx";

//-------------'Конвертация pdf'-----------------//
$interimPath = "interim_archive_dog/".$pathDogovor."/".$curdateEng.".docx";

$outputFile = $curdate.".pdf";

$outputPath = "archive_dog/".$pathDogovor."/";
/*-----------------'Договор Клиента' окончание--------------*/

//-------------'Конвертация pdf' окончание-----------------//
function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );
   
    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );
   
    file_put_contents( $fullPath, $contents, $flags );
}

file_force_contents($newpath, $content);
//-------------'Конвертация pdf' начало-----------------//
/*file_force_contents($interimPath, $content);


$converter  =  new NcJoes\OfficeConverter\OfficeConverter( './interim_archive_dog/1162/Договор_81_02-04-2020_12-38-48.docx' ); 
$converter->convertTo( 'output-file.pdf' );*/
//$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_dog/'.$pathDogovorNum;
$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_dog/'.$pathDogovor;
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
$document2->normalizeContent();
$document2->setValues([

    'Year' => date('Y'),

 //'NumberDogovor' => $res_dog['NUMBER'] ? $res_dog['NUMBER'] : $res_dog['ID'],
	'NumberDogovor' => $dogovorNumber,
    //'DateDogovor' => date('d.m.Y', strtotime($deal['DATE_CREATE'])),
	'DateDogovor' => date('d.m.Y', strtotime($dogovorDate)),
    'Company' => $deal['COMPANY_TITLE'],
	'Position' => $positionGenitive, 
	'Position2' => $req['RQ_COMPANY_REG_DATE'],
	'FIOCompany' => $fioDerectorGenitive,
	//--'Договор для ИП' начало--// 
	'FIOCompanyIP' => $fioDerector,
	//--'Договор для ИП' окончание--//
    //'FIOCompany' => $req['RQ_DIRECTOR'],
    'CompanyFullName' => $req['RQ_COMPANY_FULL_NAME'],
	'DirectorFIO' => $directorFIO,
//========================== окончание 'ФИО и ДОЛЖНОСТЬ родитетьный падеж' ==================================//
    'UAdres' => $req['RQ_ACCOUNTANT'],//$address[6]["ADDRESS_1"] . ',' . $address[6]["CITY"] . ',' . $address[6]["PROVINCE"],
    'phone' => $req['RQ_PHONE'],
    //'Email' => $req['RQ_EMAIL'],
	//Если есть Дополнительный email выводим его, иначе выводи основной email
	'Email' => !empty($req['RQ_FIRST_NAME']) ? $req['RQ_FIRST_NAME'] : $req['RQ_EMAIL'],
    //---------------------------------'Фактический Адрес' начало -------------------------//
    'mailingAddress' => !empty($req['RQ_COMPANY_NAME']) ? $req['RQ_COMPANY_NAME'] : '',//Почтовый адрес
    //---------------------------------'Фактический Адрес' окончание -------------------------//
    'OGRN' => $req['RQ_OGRN'],
	//--'Договор для ИП' начало--// 
	'ogrnip' => $ogrnip,//ОГРНИП
	//--'Договор для ИП' окончание--//
    'INN' => $req['RQ_INN'],
    'KPP' => $req['RQ_KPP'],
    'LS' => $bankReq['COMMENTS'],//($bankReq['COMMENTS'] && $bankReq['COMMENTS'] != '') ? "Лицевой счет: ".$bankReq['COMMENTS'] : "",
    'RaschSchet' => $bankReq['RQ_ACC_NUM'],
    'KSchet' => $bankReq['RQ_COR_ACC_NUM'],
    'BIK' => $bankReq['RQ_BIK'],
	'BankName' => $bankReq['RQ_BANK_NAME'],
    //---------------------------------'Фактический Адрес' начало -------------------------//
    'ActualAddress' => !empty($actual_address[1]["ADDRESS_1"]) ? $actual_address[1]["ADDRESS_1"] : '',//Фактический Адрес
    //---------------------------------'Фактический Адрес' окончание -------------------------//
    //------------------------------'Действует на основании' начало------------------------//
    'ActsBasis' => $actsBasis,//Действует на основании
    //---------------------------------'Действует на основании' окончание -----------------//

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

$result2 = $document2->process();
$content2 = $document2->getContent();


// вывод непосредственно в браузер
// header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
// header('Content-Disposition: attachment;filename="Dogovor.docx"');
// header('Cache-Control: max-age=0');
// echo $content2;
die;