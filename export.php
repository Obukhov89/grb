<?php 
 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
 //--'Правки по импортированию заявки' начало--//
 $deal_title_export = null;
 
 //Експортируемая сделка
 if (CModule::IncludeModule('crm') && $_GET['ID_DEAL_EXPORT']) {
	$deal = CCrmDeal::GetByID($_GET['ID_DEAL_EXPORT']);
	
	$deal_title_export = !empty($deal['TITLE']) ? $deal['TITLE'] : '';
 }
 //--'Правки по импортированию заявки' окончание--//
 if (!empty($_GET['ID_TZ']) && !empty($_GET['ID_DEAL_EXPORT'])) {
	
	if (!empty($_GET['ID_TZ'])) {
		$res_select_import = $DB->Query("SELECT * FROM `ba_tz` WHERE ID = " . (int)$_GET['ID_TZ'])->Fetch();
	}
	
	/*$str_query = "UPDATE `ba_tz` t1, `ba_tz` t2 SET t1.TZ=t2.TZ, t1.OBJECT=t2.OBJECT, t1.MATCH_MANUAL=t2.MATCH_MANUAL,
	t1.TEMP=t2.TEMP,t1.VLAG=t2.VLAG, t1.OBSHEE=t2.OBSHEE, t1.TEMP_O=t2.TEMP_O, t1.VLAG_O=t2.VLAG_O,
	t1.DATE1=t2.DATE1, t1.DATE2=t2.DATE2, t1.LAB=t2.LAB, t1.PROBE=t2.PROBE, t1.DATE_SOZD=t2.DATE_SOZD,
	t1.TYPE_TZ=t2.TYPE_TZ, t1.ostatki=t2.ostatki, t1.NO_COMPLIANCE=t2.NO_COMPLIANCE, t1.NORM_DESC=t2.NORM_DESC,
	t1.MATCH_RES=t2.MATCH_RES, t1.DOPINFO=t2.DOPINFO, t1.DOGOVOR_NUM=t2.DOGOVOR_NUM, t1.DESCRIPTION=t2.DESCRIPTION,
	t1.FILE=t2.FILE,t1.EXTRANET_ID=t2.EXTRANET_ID, t1.MATCH_P=t2.MATCH_P, t1.CHECK_NORM=t2.CHECK_NORM, t1.ID_OBJECT=t2.ID_OBJECT, 
	t1.PLACE_ISP=t2.PLACE_ISP, t1.CONTROL_LIST=t2.CONTROL_LIST, t1.ACC_NUM=t2.ACC_NUM, t1.ACC_DATE=t2.ACC_DATE, 
	t1.ACTUAL_FOR_OBJECT=t2.ACTUAL_FOR_OBJECT, t1.GROUP_MAT=t2.GROUP_MAT, t1.SOSTAV=t2.SOSTAV,
	t1.VLK=t2.VLK, t1.USER_VLK=t2.USER_VLK, t1.OSK_METOD_OTBOR_ID=t2.OSK_METOD_OTBOR_ID, t1.ACTUAL_VER=t2.ACTUAL_VER, 
	t1.NO_BITRIX=t2.NO_BITRIX, t1.ASSIGNED=t2.ASSIGNED, t1.COMPANY_TITLE=t2.COMPANY_TITLE, t1.LABA_ID=t2.LABA_ID, 
	t1.DEADLINE_TABLE=t2.DEADLINE_TABLE, t1.NUM_P_TABLE=t2.NUM_P_TABLE, t1.DOGOVOR_TABLE=t2.DOGOVOR_TABLE, t1.MATERIAL=t2.MATERIAL,
	t1.TYPE_ID=t2.TYPE_ID";*/
	
	$str_query = "UPDATE `ba_tz` t1, `ba_tz` t2 SET t1.TZ=t2.TZ, t1.OBJECT=t2.OBJECT, t1.MATCH_MANUAL=t2.MATCH_MANUAL,
	t1.OBSHEE=t2.OBSHEE, t1.LAB=t2.LAB, t1.PROBE=t2.PROBE, t1.DATE_SOZD=t2.DATE_SOZD,
	t1.MATCH_RES=t2.MATCH_RES, t1.DOGOVOR_NUM=t2.DOGOVOR_NUM, t1.DESCRIPTION=t2.DESCRIPTION,
	t1.FILE=t2.FILE,t1.EXTRANET_ID=t2.EXTRANET_ID, t1.MATCH_P=t2.MATCH_P, t1.ID_OBJECT=t2.ID_OBJECT, 
	t1.PLACE_ISP=t2.PLACE_ISP, t1.CONTROL_LIST=t2.CONTROL_LIST, t1.ACC_NUM=t2.ACC_NUM, t1.ACC_DATE=t2.ACC_DATE, 
	t1.VLK=t2.VLK, t1.USER_VLK=t2.USER_VLK, t1.OSK_METOD_OTBOR_ID=t2.OSK_METOD_OTBOR_ID, t1.ACTUAL_VER=t2.ACTUAL_VER, 
	t1.ASSIGNED=t2.ASSIGNED, t1.COMPANY_TITLE=t2.COMPANY_TITLE, t1.LABA_ID=t2.LABA_ID, 
	t1.DOGOVOR_TABLE=t2.DOGOVOR_TABLE, t1.MATERIAL=t2.MATERIAL, t1.TYPE_ID=t2.TYPE_ID";
	
	//Если нет Акта приемки проб то копируем у экспортируемой заявки, если акт есть то оставляем его
	if (empty($res_select_import['ACT_NUM'])) {
		$str_query .= ", t1.ACT_NUM=t2.ACT_NUM, t1.DATE_ACT=t2.DATE_ACT, t1.NUM_ACT_TABLE=t2.NUM_ACT_TABLE";
	}
	
	$res_tz = $DB->Query($str_query . " WHERE t1.ID=" . (int)$_GET['ID_TZ'] . " AND t2.ID_Z=" . (int)$_GET['ID_DEAL_EXPORT']);
 }
 
//--'Правки по импортированию заявки' начало--//
$deal_title_import = null;

//Находим импортируемую заявку
if (CModule::IncludeModule('crm') && $_GET['ID_DEAL']) {
	$newDeal = CCrmDeal::GetByID($_GET['ID_DEAL']);
	
	$deal_title_import = !empty($newDeal['TITLE']) ? $newDeal['TITLE'] : '';
 }
//--'Правки по импортированию заявки' начало--//
 
if (!empty($_GET['ID_DEAL_EXPORT'])) {
	$object_isp = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $_GET['ID_DEAL_EXPORT']);
}

//Масив ID дополнительных ответственных (экспортируемые)
$assign_arr = $object_isp['UF_CRM_1571643970']['VALUE'];

$object_isp = $object_isp['UF_CRM_1569151529509']['VALUE']; 

//Находим все экспортируемые материалы
if (!empty($_GET['ID_DEAL_EXPORT'])) {
	$materialsReq = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = ". $_GET['ID_DEAL_EXPORT']);
}

//Удаляем все материалы у импортируемой заявки и записываем новые значения с экспортированиой заявки
if (!empty($newDeal['ID'])) {
	$result_delete_material = $DB->Query("delete from `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = " . (int)$newDeal['ID']);
}
while($materialArr = $materialsReq->Fetch())
{
	
	if($materialArr['ID']) {
		$DB->Query("INSERT INTO `MATERIALS_TO_REQUESTS` (`ID_DEAL`, `ID_MATERIAL`, `NAME_MATERIAL`) VALUES (".(int)$newDeal['ID'].", ".(int)$materialArr['ID_MATERIAL'].", '".$materialArr['NAME_MATERIAL']."')");
	}
}

//Если заявка на стадии до результатов испытаний то записываем соответствующую стадию, если стадия после Акта приемки проб то присваиваем 'FINAL_INVOICE'
$arr_stage = ['NEW','PREPARATION','PREPAYMENT_INVOICE','EXECUTING','FINAL_INVOICE'];
$STAGE_ID = !empty($deal['STAGE_ID']) &&  in_array($deal['STAGE_ID'], $arr_stage) ? $deal['STAGE_ID'] : 'FINAL_INVOICE';

if (CModule::IncludeModule('crm')) {
	$deal2 = new CCrmDeal;
	$arDealUpdate = array("COMPANY_ID" => $deal['COMPANY_ID'],"ASSIGNED_BY_ID" => $deal['ASSIGNED_BY_ID'], 'STAGE_ID' => $STAGE_ID , 'UF_CRM_1569151529509' => $object_isp, 'UF_CRM_1571643970' => $assign_arr);
	$resUpd = $deal2->Update($newDeal['ID'],
		$arDealUpdate,
		true,
		true,
		array('DISABLE_USER_FIELD_CHECK' => true)
	);
}   
	
if (!empty($_GET['ID_DEAL_EXPORT'])) {
	$res_select_edit = $DB->Query("SELECT * FROM `ba_tz` WHERE ID_Z = " . (int)$_GET['ID_DEAL_EXPORT'])->Fetch();
}

//В соответствии со стадией присваиваем номер для сортировки поля стадии в Журнале заявок
switch ($STAGE_ID) {
	case 'NEW':
		$c=0;
		break;
	case 'PREPARATION':
		$c=0;
		break;
	case 'PREPAYMENT_INVOICE':
		$c=0;
		break;
	case 'EXECUTING':
		$c=0;
		break;
	case 'FINAL_INVOICE':
		$c=1;
		break;
	case '1':
		$c=2;
		break;
	case '2':
		$c=3;
		break;
	case '4':
		$c=4;
		break;
	// case '3':
	// $color='green';
	// $c=5;
	// $title='Протокол выдан';
	// break;
	case 'WON':
		$c=6;
		break;
	case 'LOSE':
		$c=7;
		break;
	case '7':
		$c=9;
		break;
	case '6':
		$c=9;
		break;
	case '5':
		$c=9;
		break;
	case '8':
		$c=9;
		break;
	case '9':
		$c=9;
		break;
	case '10':
		$c=9;
		break;
	case '11':
		$c=9;
		break;
	case '12':
		$c=9;
		break;
	case '13':
		$c=9;
		break;
}
if ($res_select_edit['ACT_NUM']&&($deal['STAGE_ID']=='NEW'||$deal['STAGE_ID']=='PREPARATION'||$deal['STAGE_ID']=='PREPAYMENT_INVOICE'||$deal['STAGE_ID']=='EXECUTING')) {
	$c=1;
}
if ($res_select_edit['PRICE']&&$res_select_edit['RESULTS']&&(!$res_select_edit['OPLATA']||$res_select_edit['OPLATA']<$res_select_edit['PRICE'])&&$deal['STAGE_ID']=='2') {
	$c=5;
}

//номер стадии 
$STAGE_NUMBER = !empty($c) ? $c : 0;	

//Колонка "Дата" (дата создания)
$DATE_CREATE = !empty($newDeal['DATE_CREATE']) ? date("d.m.Y", strtotime($newDeal['DATE_CREATE'])) : '';
//Колонка "Крайний срок"
$DEADLINE = !empty($newDeal['DATE_CREATE']) ? date('Y-m-d', strtotime($newDeal['DATE_CREATE'])+ 86400*3) : '';
$DEADLINE_TABLE = !empty($newDeal['DATE_CREATE']) ? date('d.m.Y', strtotime($newDeal['DATE_CREATE'])+ 86400*3) : '';

//Поиск полей из списка дат в журнале заявок
$DATE_CREATE_TIMESTAMP = !empty($newDeal['DATE_CREATE']) ? date("Y-m-d H:i:s", strtotime($newDeal['DATE_CREATE'])) : '';


//Получаем данные по договору у экспортируемой заявки
if (!empty($_GET['ID_DEAL_EXPORT'])) {
	$res_dtc = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL` = ". $_GET['ID_DEAL_EXPORT'])->Fetch();
}

if(!empty($res_dtc['ID_CONTRACT']) && !empty($newDeal['ID'])) {
	$DB->Query("INSERT INTO `DEALS_TO_CONTRACTS` (`ID_DEAL`, `ID_CONTRACT`) VALUES (".(int)$newDeal['ID'].", ".(int)$res_dtc['ID_CONTRACT'].")");
}
	
if (!empty($_GET['ID_TZ'])) {
	$res_tz2 = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='".$STAGE_ID."', `STAGE_NUMBER`=".$STAGE_NUMBER.", `DATE_CREATE`='".$DATE_CREATE."',`DEADLINE`='".$DEADLINE."', `DEADLINE_TABLE`='".$DEADLINE_TABLE."', `DATE_CREATE_TIMESTAMP`='".$DATE_CREATE_TIMESTAMP."' WHERE `ID`=" . (int)$_GET['ID_TZ']);	 
}
//--'Правки по импортированию заявки' начало--//
if ($res_tz) {
	 if (!empty($_GET['ID_DEAL'])) {
		 //echo "Заявка скопирована! <a href='/request_card.php?ID=" . $_GET['ID_DEAL'] . "'> Вернуться к заявке</a><br/>";
		 echo "Информации из заявки " . $deal_title_export . " успешно импортирова!, <a href='/request_card.php?ID=" . $_GET['ID_DEAL'] . "'> перейти к заявке " . $deal_title_import . "</a><br/>";
	 }
 } else {
	 echo "Заявка не импортирована!";
 }
 //--'Правки по импортированию заявки' окончание--//

 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); 
?>