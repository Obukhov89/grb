<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Формирование технического задания");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
include $_SERVER["DOCUMENT_ROOT"]."/Classes/CDB.php";
$DB_Conn = new CDB();

$deal = CCrmDeal::GetByID($_GET['ID_DEAL']);
$newDeal = new CCrmDeal;


// кладём также ACT_NUM
// $res_tz = $DB->Query("INSERT INTO `ba_tz` (TZ, OBJECT, MATCH_MANUAL, TEMP, VLAG, OBSHEE, TEMP_O, VLAG_O, DATE1, DATE2, LAB, PROBE,
// DATE_SOZD, TYPE_TZ, ostatki, NO_COMPLIANCE, NORM_DESC, MATCH_RES, DOPINFO, DOGOVOR_NUM, ACT_NUM,
// PRICE, DESCRIPTION, DATE_ACT, FILE, EXTRANET_ID, MATCH_P, CHECK_NORM, ID_OBJECT, PLACE_ISP,
// CONTROL_LIST, ACC_NUM, ID_Z, ACC_DATE, ACTUAL_FOR_OBJECT, GROUP_MAT, SOSTAV, VLK, USER_VLK, OSK_METOD_OTBOR_ID,
// ACTUAL_VER, NO_BITRIX, ASSIGNED, COMPANY_TITLE,
// LABA_ID, DEADLINE_TABLE, NUM_P_TABLE, NUM_ACT_TABLE, DOGOVOR_TABLE, MATERIAL, TYPE_ID) SELECT TZ, OBJECT,
// MATCH_MANUAL, TEMP, VLAG, OBSHEE, TEMP_O, VLAG_O, DATE1, DATE2, LAB, PROBE,
// DATE_SOZD, TYPE_TZ, ostatki, NO_COMPLIANCE, NORM_DESC, MATCH_RES, DOPINFO, DOGOVOR_NUM, ACT_NUM,
// PRICE, DESCRIPTION,  DATE_ACT, FILE, EXTRANET_ID, MATCH_P, CHECK_NORM, ID_OBJECT, PLACE_ISP,
// CONTROL_LIST, ACC_NUM, ID_Z, ACC_DATE, ACTUAL_FOR_OBJECT, GROUP_MAT, SOSTAV, VLK, USER_VLK, OSK_METOD_OTBOR_ID,
// ACTUAL_VER, NO_BITRIX, ASSIGNED, COMPANY_TITLE,
// LABA_ID, DEADLINE_TABLE, NUM_P_TABLE, NUM_ACT_TABLE, DOGOVOR_TABLE, MATERIAL, TYPE_ID FROM `ba_tz` WHERE ID=".$_GET['ID']);

// без ACT_NUM т.к при копировании заявки, основание для проб другие, сами пробы остаются и перебираются ниже с удалением из них sh_number и даты пробы
$res_tz = $DB->Query("INSERT INTO `ba_tz` (TZ, OBJECT, MATCH_MANUAL, TEMP, VLAG, OBSHEE, TEMP_O, VLAG_O, DATE1, DATE2, LAB, PROBE,
DATE_SOZD, TYPE_TZ, ostatki, NO_COMPLIANCE, NORM_DESC, MATCH_RES, DOPINFO, DOGOVOR_NUM,
PRICE, DESCRIPTION, FILE, EXTRANET_ID, MATCH_P, CHECK_NORM, ID_OBJECT, PLACE_ISP,
CONTROL_LIST, ACC_NUM, ID_Z, ACC_DATE, ACTUAL_FOR_OBJECT, GROUP_MAT, SOSTAV, VLK, USER_VLK, OSK_METOD_OTBOR_ID,
NO_BITRIX, ASSIGNED, COMPANY_TITLE,
LABA_ID, DEADLINE_TABLE, DOGOVOR_TABLE, MATERIAL, TYPE_ID) SELECT TZ, OBJECT,
MATCH_MANUAL, TEMP, VLAG, OBSHEE, TEMP_O, VLAG_O, DATE1, DATE2, LAB, PROBE,
DATE_SOZD, TYPE_TZ, ostatki, NO_COMPLIANCE, NORM_DESC, MATCH_RES, DOPINFO, DOGOVOR_NUM,
PRICE, DESCRIPTION, FILE, EXTRANET_ID, MATCH_P, CHECK_NORM, ID_OBJECT, PLACE_ISP,
CONTROL_LIST, ACC_NUM, ID_Z, ACC_DATE, ACTUAL_FOR_OBJECT, GROUP_MAT, SOSTAV, VLK, USER_VLK, OSK_METOD_OTBOR_ID,
NO_BITRIX, ASSIGNED, COMPANY_TITLE,
LABA_ID, DEADLINE_TABLE, DOGOVOR_TABLE, MATERIAL, TYPE_ID FROM `ba_tz` WHERE ID=".$_GET['ID']);

 if ($res_tz) echo "Скопировано! <a href='/requests_test.php'> Вернуться к журналу</a>";

 

 switch ($deal['TYPE_ID']) {
    case 'SALE':
        $TYPE="ИЦ";
        break;
    case 'COMPLEX':
        $TYPE="ОСК";
        break;
    case '1':
        $TYPE="ВЛК";
        break;
	case '2':
        $TYPE="МСИ";
        break;
 }
	$curYear = date("Y");
	$deals = CCrmDeal::GetList(['DATE_CREATE' => 'DESC'], ['>DATE_CREATE' => "01.01.{$curYear} 00:00:00", '!TYPE_ID' => '1']);
	$num_request=0;
	while($deal_c = $deals->Fetch())
	{
		$num_request++;
	}
	$num_request += 22;
	$title = $TYPE. " №".($num_request)."/22";

 $arFields = Array(
    "TITLE" => $title,
	"COMPANY_ID" => $deal['COMPANY_ID'],
	"TYPE_ID" => $deal['TYPE_ID'],
	"ASSIGNED_BY_ID" => $deal['ASSIGNED_BY_ID'],

);

$REQUEST_TITLE = $title;


$newDeal=$newDeal->Add($arFields);
$object_isp = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $_GET['ID_DEAL']);

$assign_arr = $object_isp['UF_CRM_1571643970']['VALUE'];


$object_isp = $object_isp['UF_CRM_1569151529509']['VALUE'];

$materialsReq = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = ". $_GET['ID_DEAL']);

while($materialArr = $materialsReq->Fetch())
{

	if($materialArr['ID'])
		$DB->Query("INSERT INTO `MATERIALS_TO_REQUESTS` (`ID_DEAL`, `ID_MATERIAL`, `NAME_MATERIAL`) VALUES (".(int)$newDeal.", ".(int)$materialArr['ID_MATERIAL'].", '".$materialArr['NAME_MATERIAL']."')");
}

	if (CModule::IncludeModule('crm')) {
			$deal2 = new CCrmDeal;
			
			$arDealUpdate = array('STAGE_ID' => 'PREPARATION', 'UF_CRM_1569151529509' => $object_isp, 'UF_CRM_1571643970' => $assign_arr);
			
			$resUpd = $deal2->Update($newDeal,
				$arDealUpdate,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}

$STAGE_ID = 'PREPARATION';
//номер стадии
$STAGE_NUMBER = 0;

$deal_up = CCrmDeal::GetByID($newDeal);


$DATE_CREATE = !empty($deal_up['DATE_CREATE']) ? date("d.m.Y", strtotime($deal_up['DATE_CREATE'])) : '';

$DEADLINE_TABLE = date('d.m.Y', strtotime($deal_up['DATE_CREATE'])+ 86400*3);

$DATE_CREATE_TIMESTAMP = !empty($deal_up['DATE_CREATE']) ? date("Y-m-d H:i:s", strtotime($deal_up['DATE_CREATE'])) : '';

$res_dtc = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL` = ". $_GET['ID_DEAL'])->Fetch();
if($res_dtc['ID']) $DB->Query("INSERT INTO `DEALS_TO_CONTRACTS` (`ID_DEAL`, `ID_CONTRACT`) VALUES (".(int)$newDeal.", ".(int)$res_dtc['ID_CONTRACT'].")");

$res_tz2 = $DB->Query("UPDATE `ba_tz` SET `ID_Z`=".(int)$newDeal.", `REQUEST_TITLE`='".$REQUEST_TITLE."', `STAGE_ID`='".$STAGE_ID."', `STAGE_NUMBER`=".$STAGE_NUMBER.", `DATE_CREATE`='".$DATE_CREATE."', `DEADLINE_TABLE`='".$DEADLINE_TABLE."', `DATE_CREATE_TIMESTAMP`='".$DATE_CREATE_TIMESTAMP."' WHERE ID_Z=".$_GET['ID_DEAL']." AND ID!=".$_GET['ID']);


$res_ba_tz = $DB_Conn->select("SELECT * from ba_tz WHERE ID_Z = {?}", array((int)$newDeal));
$probe = unserialize($res_ba_tz[0]['PROBE']);

// перебираем с изменением значений
// поэтому передаем $value с сылкой &
// и unset обязателен т.к значение остаётся и после foreach
foreach ($probe as $key => &$value) {
	$value['number'] = '';
	$value['mesto_data'] = $value['PLACE_PROBE'] . '; ' .'-';
	foreach($value['sh_number'] as $k_key => &$v_val){
		$v_val = '';
	}
	unset($v_val);
}
unset($value);

// и обновляем probe с изменёнными значениями
$update_prob = $DB_Conn->queryExec("UPDATE ba_tz SET PROBE = {?} WHERE ID_Z = {?}", array(serialize($probe), (int)$newDeal));

 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
