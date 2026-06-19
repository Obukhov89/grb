<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

if (isset($_GET['ID'])) {
    if (!in_array($_SESSION['SESS_AUTH']['USER_ID'], 61)) {
        header('location: /ulab/request/edit/' . $_GET['ID']);
    }
} else {
    if (!in_array($_SESSION['SESS_AUTH']['USER_ID'], 61)) {
        header('location: /ulab/request/new/');
    }
}
if (!empty($_GET['EDIT'])) {
	if (!in_array($_SESSION['SESS_AUTH']['USER_ID'], 61)) {
		header('location: /ulab/request/edit/' . $_GET['EDIT']);
	}
} else {
	if (!in_array($_SESSION['SESS_AUTH']['USER_ID'], 61)) {
		header('location: /ulab/request/new/');
	}
}

$APPLICATION->SetTitle("Формирование заявки на испытания");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
include $_SERVER['DOCUMENT_ROOT']."/Classes/CDB.php";
$db_conn = new CDB();
?>
<link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
<script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous">
</script>
<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
<script>
	$(document).ready(function (){
		$('.gost').select2();
	});
</script>
<script>
	$(document).ready(function ()
	{
		$('.gost2').select2();
	});
</script>
<style type="text/css">
  #tzc, #tzc option {
  width: 500px;
  margin: 0;
  }
</style>

<datalist id="gost">
    <?php
    $ba_gost = $DB->Query("SELECT * FROM `ba_gost`");
    while ($gost = $ba_gost->Fetch()) {
        echo "<option data-type='" . $gost['NORM_TEXT'] . "' " . (($arrDatum['gost'] == $gost['ID']) ? 'selected' : '') . " value='" . $gost['ID'] . ' | ' . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] .  "'></option>";
    }
    ?>
</datalist>

<datalist id="assigneds">
	<?php
	$arrResponsible = [];
	$order = array('LAST_NAME' => 'asc');
	$filter = array("ACTIVE" => 'Y');
	$tmp = 'sort';
	$lab=array(54,55,56,57,58);
	$select = CUser::GetList($order, $tmp, $filter);
	$selected='';
	while ($option = $select->Fetch()) {
		$selected = (($option['ID']==$row['ID_ASSIGN1']) ? 'selected' : '');
		$userData = CUser::GetByID($option['ID'])->Fetch();
		if(in_array($userData['UF_DEPARTMENT'][0], $lab) || $option['ID']==1 || $option['ID']==35 || $option['ID']==62|| $option['ID']==56 || $option['ID']==53 || $option['ID']==9)
		{
			echo "<option data-id='" . $option['ID'] . "'" . $selected . " value='". $option['LAST_NAME'] . ' ' . $option['NAME'] . ', ID '. $option['ID'] . "'></option>";
			$arrResponsible[$option['ID']] = $option['LAST_NAME'] . ' ' . $option['NAME'] . ', ID '. $option['ID'];
		}
	}
	?>
</datalist>

<datalist id="main_assigneds">
	<?php
	$arr_all_id_main_assigned = [
		'9',
		'11',
		'13',
		'15',
		'58',
		'43',
		'1',
		'62',
		'56'
	];
	$arrResponsibleMain = [];
	$order = array('LAST_NAME' => 'asc');
	$filter = array("ACTIVE" => 'Y');
	$tmp = 'sort';
	$lab=array(54,55,56,57,58);
	$select = CUser::GetList($order, $tmp, $filter);
	$selected='';
	while ($option = $select->Fetch()) {
		$selected = (($option['ID']==$row['ID_ASSIGN1']) ? 'selected' : '');
		$userData = CUser::GetByID($option['ID'])->Fetch();
		$a = $userData['ID'] . ' ' . $userData['LAST_NAME'];
		if(in_array($userData['ID'], $arr_all_id_main_assigned))
		{
			echo "<option data-id='" . $option['ID'] . "'" . $selected . " value='". $option['LAST_NAME'] . ' ' . $option['NAME'] . ', ID '. $option['ID'] . "'></option>";
			$arrResponsibleMain[$option['ID']] = $option['LAST_NAME'] . ' ' . $option['NAME'] . ', ID '. $option['ID'];
		}
	}
	?>
</datalist>

<datalist id="materials">
	<?php
	$materialsArr = [];
    $ba_obj = $DB->Query("SELECT * FROM `MATERIALS`");
    while ($obj = $ba_obj->Fetch()) {
        echo "<option data-id='" . $obj['ID'] . "' value='" . $obj['NAME'] .  ', ID '. $obj['ID'] . "'></option>";
	    $materialsArr[$obj['ID']] = $obj['NAME'];
	}
	?>
</datalist>

<?php
$company1='';
$extranet_id='';
$ar_assign = array();
$rs_mat=array();
$newDeal = new CCrmDeal;
$req = new \Bitrix\Crm\EntityRequisite();
$requis=array();
$arCompanys = array();


$rs = $req->getList(["order" => ["ENTITY_ID" => "asc"]]);

while($rss = $rs->Fetch())
	{
		$a = $rss['ENTITY_ID'];
		$requis[$a]['ID'] = $rss['ID'];
		$requis[$a]['RQ_INN'] = $rss['RQ_INN'];
		$requis[$a]['RQ_OGRN'] = $rss['RQ_OGRN'];
		$requis[$a]['RQ_ACCOUNTANT'] = $rss['RQ_ACCOUNTANT'];
		$requis[$a]['RQ_NAME'] = $rss['RQ_NAME'];
		$requis[$a]['RQ_LAST_NAME'] = $rss['RQ_LAST_NAME'];
		$requis[$a]['RQ_PHONE'] = $rss['RQ_PHONE'];
		$requis[$a]['RQ_EMAIL'] = $rss['RQ_EMAIL'];

		$requis[$a]['RQ_KPP'] = !empty($rss['RQ_KPP']) ? $rss['RQ_KPP'] : '';
		$requis[$a]['RQ_COMPANY_REG_DATE'] = !empty($rss['RQ_COMPANY_REG_DATE']) ? $rss['RQ_COMPANY_REG_DATE'] : '';
		$requis[$a]['RQ_COMPANY_FULL_NAME'] = !empty($rss['RQ_COMPANY_FULL_NAME']) ? $rss['RQ_COMPANY_FULL_NAME'] : '';
		$requis[$a]['RQ_DIRECTOR'] = !empty($rss['RQ_DIRECTOR']) ? $rss['RQ_DIRECTOR'] : '';

		
		$requis[$a]['RQ_FIRST_NAME'] = !empty($rss['RQ_FIRST_NAME']) ? $rss['RQ_FIRST_NAME'] : '';
		$requis[$a]['RQ_COMPANY_NAME'] = !empty($rss['RQ_COMPANY_NAME']) ? $rss['RQ_COMPANY_NAME'] : '';
	}

/*$rs = $req->getList(["order" => ["ENTITY_ID" => "asc"]]);

while($rss = $rs->Fetch())
	{

		$a = $rss['ENTITY_ID'];
		$requis[$a]['RQ_INN'] = $rss['RQ_INN'];
		$requis[$a]['RQ_OGRN'] = $rss['RQ_OGRN'];
		$requis[$a]['RQ_ACCOUNTANT'] = $rss['RQ_ACCOUNTANT'];
		$requis[$a]['RQ_NAME'] = $rss['RQ_NAME'];
		$requis[$a]['RQ_LAST_NAME'] = $rss['RQ_LAST_NAME'];
		$requis[$a]['RQ_PHONE'] = $rss['RQ_PHONE'];
		$requis[$a]['RQ_EMAIL'] = $rss['RQ_EMAIL'];
	}*/

if ( \Bitrix\Main\Loader::IncludeModule('crm') )
{
 	$arOrder  = array('ID' => 'ASC');
  	$arFilter = array();
	$arSelect = array();
	$res = CCrmCompany::GetList( $arOrder, $arFilter, $arSelect );
	while( $arCompany = $res->fetch() )
	{
	    $arCompanys[ $arCompany['ID'] ] = $arCompany['TITLE'];
	}
}
?>

<datalist id="clients">
	<?php
    foreach ($arCompanys as $a => $arComp)
	{
        $requisiteId = $requis[$a]['ID'];
		$inn = $requis[$a]['RQ_INN'];
		$ogrn = $requis[$a]['RQ_OGRN'];
		$addr = $requis[$a]['RQ_ACCOUNTANT'];
		$name = $requis[$a]['RQ_NAME']." ".$requis[$a]['RQ_LAST_NAME'];
		$phone = $requis[$a]['RQ_PHONE'];
		$email = $requis[$a]['RQ_EMAIL'];
		
		$kpp = $requis[$a]['RQ_KPP'];
		$position = $requis[$a]['RQ_COMPANY_REG_DATE'];
		$company = $requis[$a]['RQ_COMPANY_FULL_NAME'];
		$director = $requis[$a]['RQ_DIRECTOR'];
		
		$addemail = $requis[$a]['RQ_FIRST_NAME'];
		$mail = $requis[$a]['RQ_COMPANY_NAME'];
		
		echo "<option data-id='" . $a . "' data-requisite='" . $requisiteId . "' data-inn = '".$inn."' data-ogrn = '".$ogrn."' 
		data-addr = '".$addr."' data-name = '".$name."' data-phone = '".$phone."' data-email = '".$email."'  
		data-kpp='".$kpp."' data-position='".$position."' data-company='".$company."' data-director='".$director."' 
		data-addemail='" . $addemail . "' data-mail='".$mail."' value='" . $arComp. ', ИНН '. $inn. ', ID ' . $a ."'></option>";

	}
    ?>
</datalist>
<?php
if ( \Bitrix\Main\Loader::IncludeModule('crm') )
{
 	$arOrder  = array('ID' => 'ASC');
  	$arFilter = array('COMPANY_TYPE' => "1");
	$arSelect = array();
	$res = CCrmCompany::GetList( $arOrder, $arFilter, $arSelect );

	while( $arManuf = $res->fetch() )
	{
		$arManufs[ $arManuf['ID'] ] = $arManuf['TITLE'];
	}
}
?>

<datalist id="manufacturers">
	<?php
    foreach ($arManufs as $a => $arComp)
	{


		echo "<option data-id='" . $a . "' value='" . $arComp. ', ИНН '. $inn. ', ID ' . $a ."'></option>";

	}
    ?>
</datalist>

<?php

//Изменил !empty($_POST['MATERIAL']) на !empty($_POST['client'])
if (!empty($_POST['client']))
{

	$ar = array();
	for($i=0; $i<count($_POST['ASSIGNED']); $i++)
	{
		$ar = explode('ID', $_POST['ASSIGNED'][$i]);
		$ar_assign[$i] = (int)$ar[1];
	}

	for($i=0; $i<count($_POST['MATERIAL']); $i++)
	{
		$mat_ar = explode(', ID', $_POST['MATERIAL'][$i]);
		if ($mat_ar[1])
		{
			$ar_material_id[$i] = (int)$mat_ar[1];
			$ar_material[$i] = $mat_ar[0];
		}
		else
		{
			$ar_material[$i] = $_POST['MATERIAL'][$i];
			$ar_material_id[$i] = 0;
		}
	}

	switch ($_POST['REQ_TYPE']) {
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
	case '4':
        $TYPE="НК";
        break;
	}

	if(!$_POST['id_comp'])
	{
		$newComp = new CCrmCompany;
		$nameClient = explode(",", $_POST['client']);
		// $arParam=array("TITLE" => $nameClient[0]);
		$arParam = array(
			"TITLE" => $nameClient[0],
			'UF_CRM_1619000406913' => $_POST['addEmail1'],
			'UF_CRM_1619000436326' => $_POST['addEmail2'],
			'UF_CRM_1619000459372' => $_POST['addEmail3']
		);
		$newComp = $newComp->Add($arParam);
	}
	$curYear = date("Y");
	$deals = CCrmDeal::GetList(['DATE_CREATE' => 'DESC'], ['>DATE_CREATE' => "01.01.{$curYear} 00:00:00", '!TYPE_ID' => '1']);
	$num_request=0;
	while($deal_c = $deals->Fetch())
	{
		$num_request++;
	}

	$num_request += 22;

	$arFields = Array(
		//"TITLE" => $TYPE." №".$_POST['ACT_NUM'].getCurrentYearForDocs(),
		"TITLE" => $TYPE." №".$num_request.getCurrentYearForDocs(),
		"COMPANY_ID" => $_POST['id_comp'] ? $_POST['id_comp'] : $newComp,
		"TYPE_ID" => $_POST['REQ_TYPE'],
		"ASSIGNED_BY_ID" => $ar_assign[0],
	);
	$ASSIGNED_BY_ID = $ar_assign[0];
	$ar_assign2 = array_shift($ar_assign);

	if ($_GET['EDIT']) //---------------------СОХРАНЕНИЕ ПРИ РЕДАКТИРОВАНИИ------------------------
	{

		$dealUp = CCrmDeal::GetByID($_GET['EDIT']);

		$yearCreate = !empty($dealUp['DATE_CREATE']) ? date('Y', strtotime($dealUp['DATE_CREATE'])) : ''; 
		$getYearCreate = (int)$yearCreate%10 ? "/".substr($yearCreate, -2) : "/".$yearCreate;

		if (CModule::IncludeModule('crm')) {
			$deal = new CCrmDeal;
			$arDealUpdate = array(
				"COMPANY_ID" => $_POST['id_comp'] ? $_POST['id_comp'] : $newComp,
				"ASSIGNED_BY_ID" => $ASSIGNED_BY_ID,
				"TYPE_ID" => $_POST['REQ_TYPE'],
				//"TITLE" => $TYPE." №".$_POST['ACT_NUM'].getCurrentYearForDocs(),
				"TITLE" => $TYPE." №".$_POST['ACT_NUM'].$getYearCreate,
				'STAGE_ID' => 'PREPARATION',
				'UF_CRM_1571643970' => $ar_assign
			);
			$resUpd = $deal->Update($dealUp['ID'],
				$arDealUpdate,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}

		$updDeal = CCrmDeal::GetByID($dealUp['ID']);
		$requisite = new \Bitrix\Crm\EntityRequisite();
		
		$bankObj = new \Bitrix\Crm\EntityBankDetail();
		
		$rs = $requisite->getList([
			"filter" => ["ENTITY_ID" => $updDeal['COMPANY_ID'], "ENTITY_TYPE_ID" => CCrmOwnerType::Company]
		]);
		$reqData = $rs->fetchAll();

		if (!$newComp && empty($reqData[0]['ENTITY_ID'])) {
			$newComp = $_POST['id_comp'];
			$nameClient = explode(",",$_POST['client']);
		}

		
		$name_company = null;
		
		if (!$newComp && !empty($_POST['client']) && !empty($reqData[0]['ENTITY_ID'])) {
			$arr_company = explode(",",$_POST['client']);

			$req_data_name = !empty($reqData[0]['NAME']) ? $reqData[0]['NAME'] : '';
			$name_company = !empty($arr_company[0]) ? $arr_company[0] : $req_data_name;

			if (CModule::IncludeModule('crm')) {
				$company_obj = new CCrmCompany;
				$arr_company_update = array(
					"TITLE" => $name_company,
				);

				
				$res_update_company = $company_obj->Update($reqData[0]['ENTITY_ID'],
					$arr_company_update,
					true,
					true,
					array()
					//array('DISABLE_USER_FIELD_CHECK' => true)
				);
			}
		}

		$fields['ENTITY_ID'] = ($newComp ? $newComp : $reqData[0]['ENTITY_ID']);
		$fields['ENTITY_TYPE_ID'] = ($newComp ? '4' : $reqData[0]['ENTITY_TYPE_ID']);
		$fields['PRESET_ID'] = ($newComp ? "1" : $reqData[0]['PRESET_ID']);
		
		//$fields['NAME'] = ($newComp ? $nameClient[0] : $reqData[0]['NAME']);
		$fields['NAME'] = ($newComp ? $nameClient[0] : $name_company); 
		
		$fields['SORT'] = 500;
		$fields['ACTIVE'] = 'Y';
		$fields['RQ_INN'] = $_POST['INN'];
		$fields['RQ_ACCOUNTANT'] = $_POST['ADDR'];
		$fields['RQ_OGRN'] = $_POST['OGRN'];
		$fields['RQ_EMAIL'] = $_POST['POST_MAIL'];
		$fields['RQ_PHONE'] = $_POST['PHONE'];
		$fields['RQ_NAME'] = $_POST['CONTACT'];
		
		$fields['RQ_KPP'] = !empty($_POST['KPP']) ? $_POST['KPP'] : '';
		$tmp_pos = $_POST['Position2'];
		// проверка на длину
		if(strlen($tmp_pos) >= 30 || empty($tmp_pos)) {
			$fields['RQ_COMPANY_REG_DATE'] = '';
		}else{
			$fields['RQ_COMPANY_REG_DATE'] = $tmp_pos;
		}
		// $fields['RQ_COMPANY_REG_DATE'] = !empty($_POST['PositionGenitive']) ? $_POST['PositionGenitive'] : '';//Должность руководителя в родительном падеже
		// $fields['RQ_COMPANY_REG_DATE'] = !empty($_POST['Position2']) ? $_POST['Position2'] : '';//Должность руководителя
		$fields['RQ_COMPANY_FULL_NAME'] = !empty($_POST['CompanyFullName']) ? $_POST['CompanyFullName'] : '';//Полное наименование организации в реквизитах
		$fields['RQ_DIRECTOR'] = !empty($_POST['DirectorFIO']) ? $_POST['DirectorFIO'] : '';
		$fields['RQ_ACC_NUM'] = !empty($_POST['RaschSchet']) ? $_POST['RaschSchet'] : '';
		$fields['RQ_COR_ACC_NUM'] = !empty($_POST['KSchet']) ? $_POST['KSchet'] : '';
		$fields['RQ_BIK'] = !empty($_POST['BIK']) ? $_POST['BIK'] : '';
		$fields['RQ_BANK_NAME'] = !empty($_POST['BankName']) ? $_POST['BankName'] : '';

		
		$fields['RQ_FIRST_NAME'] = !empty($_POST['EMAIL']) ? $_POST['EMAIL'] : '';
		$fields['UF_CRM_1619000406913'] = !empty($_POST['addEmail1']) ? $_POST['addEmail1'] : '';
		$fields['UF_CRM_1619000436326'] = !empty($_POST['addEmail2']) ? $_POST['addEmail2'] : '';
		$fields['UF_CRM_1619000459372'] = !empty($_POST['addEmail3']) ? $_POST['addEmail3'] : '';
		$fields['RQ_COMPANY_NAME'] = !empty($_POST['mailingAddress']) ? $_POST['mailingAddress'] : ''; 
		
        $fields["RQ_ADDR"][1]["ADDRESS_1"] = !empty($_POST["ACTUAL_ADDRESS"]) ? $_POST["ACTUAL_ADDRESS"] : '';
        
        $fields["RQ_ADDR"][1]["ADDRESS_2"] = !empty($_POST["ACTS_BASIS"]) ? $_POST["ACTS_BASIS"] : '';
        
		$fields['RQ_ADDR'][1]['REGION'] = !empty($_POST['OGRNIP']) ? $_POST['OGRNIP'] : ''; 
		
		$fields['COMMENTS'] = !empty($_POST['l_schet']) ? $_POST['l_schet'] : '';
		

		$req_id = ($newComp ? $newComp : $reqData[0]['ENTITY_ID']);
		//echo'<pre>';var_dump('$fields', $fields);
		
		$requisite->deleteByEntity(CCrmOwnerType::Company, $req_id);
		$res = $requisite->add($fields);

		$fields['ENTITY_ID'] = $res->getId();
		$fields['ENTITY_TYPE_ID'] = CCrmOwnerType::Requisite;
		$res2 = $bankObj->add($fields);

		
		$deal_edited = CCrmDeal::GetByID(!empty($_GET['EDIT']) ? $_GET['EDIT'] : '');
		
		$id_deal_edit = !empty($deal_edited['ID']) ? (int)$deal_edited['ID'] : '';

		$res_select_edit = $DB->Query("SELECT * FROM `ba_tz` WHERE ID_Z = " . (int)$id_deal_edit)->Fetch();

		
		switch ($deal_edited['STAGE_ID']) {
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
		if ($res_select_edit['ACT_NUM']&&($deal_edited['STAGE_ID']=='NEW'||$deal_edited['STAGE_ID']=='PREPARATION'||$deal_edited['STAGE_ID']=='PREPAYMENT_INVOICE'||$deal_edited['STAGE_ID']=='EXECUTING')) {
			$c=1;
		}
		if ($res_select_edit['PRICE']&&$res_select_edit['RESULTS']&&(!$res_select_edit['OPLATA']||$res_select_edit['OPLATA']<$res_select_edit['PRICE'])&&$deal_edited['STAGE_ID']=='2') {
			$c=5;
		}

		
		$assigned_by_name = !empty($deal_edited['ASSIGNED_BY_NAME']) ? $deal_edited['ASSIGNED_BY_NAME'] : '';

		
		$assigned_by_last_name = !empty($deal_edited['ASSIGNED_BY_LAST_NAME']) ? $deal_edited['ASSIGNED_BY_LAST_NAME'] : '';

		$str_assigneds = getInitials($assigned_by_name) ." ". $assigned_by_last_name;

		
		$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal_edited['ID']);

		
		if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {

			foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
			{
				$assign_user = CUser::GetByID((int)$assign)->Fetch();
				$str_assigneds .= ", ".getInitials($assign_user['NAME'])." ".$assign_user['LAST_NAME'];
			}
		}

		
		$title_deal_edit = !empty($deal_edited['TITLE']) ? $DB->ForSql($deal_edited['TITLE']) : '';

		
		$stage_id_deal_edit = !empty($deal_edited['STAGE_ID']) ? $DB->ForSql($deal_edited['STAGE_ID']) : '';

		
		$company_title_deal_edit = !empty($deal_edited['COMPANY_TITLE']) ? $DB->ForSql($deal_edited['COMPANY_TITLE']) : '';

		
		$userr = CUser::GetByID($deal_edited['ASSIGNED_BY_ID'])->Fetch();
		$id_laba = '';
		if (!empty($userr['UF_DEPARTMENT'][0])) {
			
			$id_laba = $userr['UF_DEPARTMENT'][0];
		}

		
		$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal_edited['ID']);
		if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {

			foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
			{
				$assign_user = CUser::GetByID((int)$assign)->Fetch();
				if($assign_user['UF_DEPARTMENT'][0])
				{
					
					$id_laba .= ',' . $assign_user['UF_DEPARTMENT'][0];
				}
			}
		}

		
		$type_id_edited = !empty($deal_edited["TYPE_ID"]) ?  $DB->ForSql($deal_edited["TYPE_ID"]) : '';

		
		$check_ip = ((!empty($_POST['check_ip']) && $_POST['check_ip'] == 'on') ? 1 : '');
		
		$manufacturers_new = null;
		$manufacturer_title = null;
		if (!empty($_POST['MANUFACTURERS'][0])) {

			$manufacturers_arr = !empty($_POST['MANUFACTURERS'][0]) ?  explode(', ID ', $_POST['MANUFACTURERS'][0]) : '';

			$manufacturers = !empty($manufacturers_arr[1]) ? $DB->ForSql($manufacturers_arr[1]) : '';


			$manufacturer_data = $requisite->getList(["filter" => ["ENTITY_ID" => $manufacturers, "ENTITY_TYPE_ID" => CCrmOwnerType::Company]])->fetchAll();

			$manufacturer_title = !empty($manufacturer_data[0]['NAME']) ? $manufacturer_data[0]['NAME'] : '';
		}
		
			$dogovor_num = !empty($_POST['DOGOVOR_NUM']) ? $DB->ForSql($_POST['DOGOVOR_NUM']) : '';
			$dogovor_num_arr = !empty($_POST['DOGOVOR_NUM']) ? explode(', ID', $_POST['DOGOVOR_NUM']) : '';
			$id_dogovor_num = !empty($dogovor_num_arr[1]) ? (int)$dogovor_num_arr[1] : '';
			$dogovor_name = !empty($dogovor_num_arr[0]) ? $dogovor_num_arr[0] : '';

			if (!empty($id_dogovor_num) && !empty($id_deal_edit)) {
				$DB->Query("DELETE FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL` = " . $id_deal_edit);
				$DB->Query("INSERT INTO `DEALS_TO_CONTRACTS` (`ID_DEAL`, `ID_CONTRACT`) VALUES (" . $id_deal_edit . "," . $id_dogovor_num . ")");
			} elseif(empty($id_dogovor_num) && !empty($id_deal_edit)) {
				$DB->Query("DELETE FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL` = " . $id_deal_edit);
			}
		
		if(!empty($_POST['saveMail'])){
			$mail=[];
			$cheked=[];
			foreach ($_POST['saveMail'] as $key => $value) {
				//$mail[$key]=$_POST['addEmail1'][$key];
				$cheked[$key]=$_POST['saveMail'][$key];
			};

			$updateBa="UPDATE `ba_tz` SET `SAVE_MAIL` = {?} WHERE `ID_Z`= {?}";
			$mailArr= array(serialize($_POST['addEmail1']),$id_deal_edit);

			$updateBaCh="UPDATE `ba_tz` SET `CHECK_MAIL` = {?} WHERE `ID_Z`= {?}";
			$checkArr= array(serialize($cheked),$id_deal_edit);

			$dbMail=$db_conn->queryExec($updateBa,$mailArr);
			$dbCheck=$db_conn->queryExec($updateBaCh,$checkArr);

		};

		
		if (!empty($res_select_edit['ID'])) {

			$posit_short = (!empty($_REQUEST['Position2']) ? $_REQUEST['Position2'] : '');
			$posit_full = (!empty($_REQUEST['PositionGenitive']) ? $_REQUEST['PositionGenitive'] : '');

			
			$res_pos_l = $db_conn->select("SELECT POSIT_LEADS_ID from ba_tz where ba_tz.ID_Z = {?}", array($id_deal_edit));
			
			if(empty($res_pos_l[0]['POSIT_LEADS_ID'])) {
				$l_id = $db_conn->select("SHOW TABLE STATUS LIKE 'POSIT_LEADS'");
				$id = $l_id[0]['Auto_increment'];

				$arr_posit = array($posit_short, $posit_full);
				$sqlTot = "INSERT INTO `POSIT_LEADS` (POSIT_SHORT, POSIT_FULL) VALUES({?}, {?})";
				$ins_posit = $db_conn->queryExec($sqlTot, $arr_posit);

			}else{
			
				$id = $res_pos_l[0]['POSIT_LEADS_ID'];

				$arr_posit = array($posit_short, $posit_full, $id);
				$sqlTot = "UPDATE `POSIT_LEADS` SET POSIT_SHORT = {?}, POSIT_FULL = {?} WHERE ID = {?}";
				$ins_posit = $db_conn->queryExec($sqlTot, $arr_posit);
			}
			
			// без POSIT_LEADS_ID
			// $res_assign_update = $DB->Query("UPDATE `ba_tz` SET `ASSIGNED`='" . $DB->ForSql($str_assigneds) . "', `REQUEST_TITLE`='" . $title_deal_edit . "', `STAGE_ID`='" . $stage_id_deal_edit . "', `STAGE_NUMBER`= " . (int)$c . ", `COMPANY_TITLE`= '" . $company_title_deal_edit . "', `LABA_ID`= '" . $id_laba . "', `TYPE_ID`= '" . $type_id_edited . "', `CHECK_IP`= '" . $check_ip . "', `MANUFACTURER`='" . $manufacturers . "', `MANUFACTURER_TITLE`='" . $manufacturer_title . "', `DOGOVOR_NUM`='" . $dogovor_num . "', `DOGOVOR_TABLE`='" . $dogovor_name . "' WHERE `ID_Z`=" . $id_deal_edit);

			$res_assign_update = $DB->Query("UPDATE `ba_tz` SET `ASSIGNED`='" . $DB->ForSql($str_assigneds) . "', `REQUEST_TITLE`='" . $title_deal_edit . "', `STAGE_ID`='" . $stage_id_deal_edit . "', `STAGE_NUMBER`= " . (int)$c . ", `COMPANY_TITLE`= '" . $company_title_deal_edit . "', `LABA_ID`= '" . $id_laba . "', `TYPE_ID`= '" . $type_id_edited . "', `CHECK_IP`= '" . $check_ip . "', `MANUFACTURER`='" . $manufacturers . "', `MANUFACTURER_TITLE`='" . $manufacturer_title . "', `DOGOVOR_NUM`='" . $dogovor_num . "', `POSIT_LEADS_ID` = '" . $id . "', `DOGOVOR_TABLE`='" . $dogovor_name . "' WHERE `ID_Z`=" . $id_deal_edit);
		} else {

			
			$res_pos_l = $db_conn->select("SHOW TABLE STATUS LIKE 'POSIT_LEADS'");
			$last_id_posit = $res_pos_l[0]['Auto_increment'];

			$posit_short = (!empty($_REQUEST['Position2']) ? $_REQUEST['Position2'] : '');
			$posit_full = (!empty($_REQUEST['PositionGenitive']) ? $_REQUEST['PositionGenitive'] : '');

			$arr_posit = array($posit_short, $posit_full);
			$sqlTot = "INSERT INTO `POSIT_LEADS` (POSIT_SHORT, POSIT_FULL) VALUES({?}, {?})";
			$ins_posit = $db_conn->queryExec($sqlTot, $arr_posit);
			

			$res_assign_insert = $DB->Query("INSERT INTO `ba_tz` (`ID_Z`, `ASSIGNED`, `REQUEST_TITLE`, `STAGE_NUMBER`, `STAGE_ID`, `COMPANY_TITLE`, `LABA_ID`, `TYPE_ID`, `CHECK_IP`, `MANUFACTURER`, `MANUFACTURER_TITLE`, `DOGOVOR_NUM`, `DOGOVOR_TABLE`) VALUES ('" . $id_deal_edit . "', '" . $DB->ForSql($str_assigneds) . "', '" . $title_deal_edit . "', '" . $stage_id_deal_edit . "', " . (int)$c . ", '" . $company_title_deal_edit . "', '" . $id_laba . "', '" . $type_id_edited . "', '" . $check_ip . "', '" . $manufacturers . "', '" . $manufacturer_title . "', '" . $dogovor_num . "', '" . $dogovor_name . "')");

			//$res_assign_insert = $DB->Query("INSERT INTO `ba_tz` (`ID_Z`, `ASSIGNED`, `REQUEST_TITLE`, `STAGE_NUMBER`, `STAGE_ID`, `COMPANY_TITLE`, `LABA_ID`, `TYPE_ID`, `CHECK_IP`, `MANUFACTURER`, `MANUFACTURER_TITLE`, `DOGOVOR_NUM`, `DOGOVOR_TABLE`) VALUES ('" . $id_deal_edit . "', '" . $DB->ForSql($str_assigneds) . "', '" . $title_deal_edit . "', '" . $stage_id_deal_edit . "', " . (int)$c . ", '" . $company_title_deal_edit . "', '" . $id_laba . "', '" . $type_id_edited . "', '" . $check_ip . "', '" . $manufacturers . "', '" . $manufacturer_title . "', '" . $dogovor_num . "', '" . $dogovor_name . "')");
			
		}

		
	}
	else //---------------------СОХРАНЕНИЕ ПРИ СОЗДАНИИ------------------------
	{

		if(!empty($_POST['saveMail'])){
			$mail=[];
			$cheked=[];
			foreach ($_POST['saveMail'] as $key => $value) {
				 //$mail[$key]=$_POST['addEmail1'][$key];
				$cheked[$key]=$_POST['saveMail'][$key];
			};

			$updateBa="UPDATE `ba_tz` SET `SAVE_MAIL` = {?} WHERE `ID_Z`= {?}";
			$mailArr= array(serialize($_POST['addEmail1']),$id_deal_edit);

			$updateBaCh="UPDATE `ba_tz` SET `CHECK_MAIL` = {?} WHERE `ID_Z`= {?}";
			$checkArr= array(serialize($cheked),$id_deal_edit);

			$dbMail=$db_conn->queryExec($updateBa,$mailArr);
			$dbCheck=$db_conn->queryExec($updateBaCh,$checkArr);

		};
		$count_deals=1;

		if (!empty($_POST['few_deals']))
		{
			$count_deals=count($ar_material_id);

		}
		//echo $count_deals;

		for($d=0; $d<$count_deals; $d++)
		{

			if($d>0)
			{
				// $ar_assign = array();
				dump($ar_assign);
				// $rs_mat=array();
				$newDeal = new CCrmDeal;
				// $arCompanys = array();
				$num_request += 1;

				$arFields = Array(
					//"TITLE" => $TYPE." №".$_POST['ACT_NUM'].getCurrentYearForDocs(),
					"TITLE" => $TYPE." №".$num_request.getCurrentYearForDocs(),
					"COMPANY_ID" => $_POST['id_comp'] ? $_POST['id_comp'] : $newComp,
					"TYPE_ID" => $_POST['REQ_TYPE'],
					"ASSIGNED_BY_ID" => $ASSIGNED_BY_ID,
				);
				// $ASSIGNED_BY_ID = $ar_assign[0];
				// $ar_assign2 = array_shift($ar_assign);
			}

			$newDeal=$newDeal->Add($arFields);

			if (CModule::IncludeModule('crm')) {
					$deal = new CCrmDeal;
					$arDealUpdate = array('STAGE_ID' => 'PREPARATION', 'UF_CRM_1571643970' => $ar_assign);
					$resUpd = $deal->Update($newDeal,
						$arDealUpdate,
						true,
						true,
						array('DISABLE_USER_FIELD_CHECK' => true)
					);
				}

			$updDeal = CCrmDeal::GetByID($newDeal);

			if(!empty($_POST['saveMail'])){
				$mail=[];
				$cheked=[];
				foreach ($_POST['saveMail'] as $key => $value) {
					//$mail[$key]=$_POST['addEmail1'][$key];
					$cheked[$key]=$_POST['saveMail'][$key];
				};

				$updateBa="UPDATE `ba_tz` SET `SAVE_MAIL` = {?} WHERE `ID_Z`= {?}";
				$mailArr= array(serialize($_POST['addEmail1']),$id_deal_edit);

				$updateBaCh="UPDATE `ba_tz` SET `CHECK_MAIL` = {?} WHERE `ID_Z`= {?}";
				$checkArr= array(serialize($cheked),$id_deal_edit);

				$dbMail=$db_conn->queryExec($updateBa,$mailArr);
				$dbCheck=$db_conn->queryExec($updateBaCh,$checkArr);

			};
			$requisite = new \Bitrix\Crm\EntityRequisite();

			$bankObj = new \Bitrix\Crm\EntityBankDetail();


			$rs = $requisite->getList([
				"filter" => ["ENTITY_ID" => $updDeal['COMPANY_ID'], "ENTITY_TYPE_ID" => CCrmOwnerType::Company]
			]);
			$reqData = $rs->fetchAll();

			if (!$newComp && empty($reqData[0]['ENTITY_ID'])) {
				$newComp = $_POST['id_comp'];
				$nameClient = explode(",",$_POST['client']);
			}

			$fields['ENTITY_ID'] = ($newComp ? $newComp : $reqData[0]['ENTITY_ID']);
			$fields['ENTITY_TYPE_ID'] = ($newComp ? '4' : $reqData[0]['ENTITY_TYPE_ID']);
			$fields['PRESET_ID'] = ($newComp ? "1" : $reqData[0]['PRESET_ID']);
			$fields['NAME'] = ($newComp ? $nameClient[0] : $reqData[0]['NAME']);
			$fields['SORT'] = 500;
			$fields['ACTIVE'] = 'Y';
			$fields['RQ_INN'] = $_POST['INN'];
			$fields['RQ_ACCOUNTANT'] = $_POST['ADDR'];
			$fields['RQ_OGRN'] = $_POST['OGRN'];
			$fields['RQ_EMAIL'] = $_POST['POST_MAIL'];
			$fields['RQ_PHONE'] = $_POST['PHONE'];
			$fields['RQ_NAME'] = $_POST['CONTACT'];

			$fields['RQ_KPP'] = !empty($_POST['KPP']) ? $_POST['KPP'] : '';
			// $fields['RQ_COMPANY_REG_DATE'] = !empty($_POST['Position2']) ? $_POST['Position2'] : '';

			$tmp_pos = $_POST['Position2'];

			if(strlen($tmp_pos) >= 30 || empty($tmp_pos)) {
				$fields['RQ_COMPANY_REG_DATE'] = '';
			}else{
				$fields['RQ_COMPANY_REG_DATE'] = $tmp_pos;
			}
			// $fields['RQ_COMPANY_REG_DATE'] = !empty($_POST['PositionGenitive']) ? $_POST['PositionGenitive'] : '';
			$fields['RQ_COMPANY_FULL_NAME'] = !empty($_POST['CompanyFullName']) ? $_POST['CompanyFullName'] : '';
			$fields['RQ_DIRECTOR'] = !empty($_POST['DirectorFIO']) ? $_POST['DirectorFIO'] : '';
			$fields['RQ_ACC_NUM'] = !empty($_POST['RaschSchet']) ? $_POST['RaschSchet'] : '';
			$fields['RQ_COR_ACC_NUM'] = !empty($_POST['KSchet']) ? $_POST['KSchet'] : '';
			$fields['RQ_BIK'] = !empty($_POST['BIK']) ? $_POST['BIK'] : '';
			$fields['RQ_BANK_NAME'] = !empty($_POST['BankName']) ? $_POST['BankName'] : '';


			$fields['RQ_FIRST_NAME'] = !empty($_POST['EMAIL']) ? $_POST['EMAIL'] : '';
			$fields['RQ_COMPANY_NAME'] = !empty($_POST['mailingAddress']) ? $_POST['mailingAddress'] : '';



			$fields["RQ_ADDR"][1]["ADDRESS_1"] = !empty($_POST["ACTUAL_ADDRESS"]) ? $_POST["ACTUAL_ADDRESS"] : '';//Фактический адрес


			$fields["RQ_ADDR"][1]["ADDRESS_2"] = !empty($_POST["ACTS_BASIS"]) ? $_POST["ACTS_BASIS"] : '';//Действует на основании



			$fields['RQ_ADDR'][1]['REGION'] = !empty($_POST['OGRNIP']) ? $_POST['OGRNIP'] : ''; //ОГРНИП



			$fields['COMMENTS'] = !empty($_POST['l_schet']) ? $_POST['l_schet'] : '';//Лицевой счет


			$req_id = ($newComp ? $newComp : $reqData[0]['ENTITY_ID']);

			$requisite->deleteByEntity(CCrmOwnerType::Company, $req_id);
			$res = $requisite->add($fields);


			$rs2 = $requisite->getList([
				"filter" => ["ENTITY_ID" => $updDeal['COMPANY_ID'], "ENTITY_TYPE_ID" => CCrmOwnerType::Company]
			]);
			$reqData2 = $rs2->fetchAll();



			//$fields['ENTITY_ID'] = $res->getId();

			$fields['ENTITY_ID'] = !empty($reqData2[0]["ID"]) ? $reqData2[0]["ID"] : $res->getId();

			$fields['ENTITY_TYPE_ID'] = CCrmOwnerType::Requisite;
			$res2 = $bankObj->add($fields);

			$id_deal = ($_GET['EDIT']) ? $dealUp['ID'] : $updDeal['ID'];

			if($count_deals==1)
			{
				foreach($ar_material_id as $key => $ar_m)
					{
						$DB->Query("INSERT INTO `MATERIALS_TO_REQUESTS` (`ID_DEAL`, `ID_MATERIAL`, `NAME_MATERIAL`, `OBIEM`) VALUES ({$id_deal}, {$ar_m}, '{$ar_material[$key]}', {$_POST['OBIEM'][$key]})");
					}
			}
			else
				$DB->Query("INSERT INTO `MATERIALS_TO_REQUESTS` (`ID_DEAL`, `ID_MATERIAL`, `NAME_MATERIAL`, `OBIEM`) VALUES ({$id_deal}, {$ar_material_id[$d]}, '{$ar_material[$d]}', {$_POST['OBIEM'][$d]})");


			$deal_new = !empty($updDeal) ? $updDeal : '';
			//$deal_edited = CCrmDeal::GetByID(!empty($updDeal['ID']) ? $updDeal['ID'] : '');


			$id_deal_new = !empty($deal_new['ID']) ? (int)$deal_new['ID'] : '';

			$res_select_new = $DB->Query("SELECT * FROM `ba_tz` WHERE ID_Z = " . (int)$id_deal_new)->Fetch();


			switch (!empty($deal_new['STAGE_ID']) ? $deal_new['STAGE_ID'] : '') {
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
			if ($res_select_new['ACT_NUM'] && ($deal_new['STAGE_ID']=='NEW' || $deal_new['STAGE_ID']=='PREPARATION' || $deal_new['STAGE_ID']=='PREPAYMENT_INVOICE' || $deal_new['STAGE_ID']=='EXECUTING')) {
				$c=1;
			}
			if ($res_select_new['PRICE'] && $res_select_new['RESULTS'] && (!$res_select_new['OPLATA'] || $res_select_new['OPLATA'] < $res_select_new['PRICE']) && $deal_new['STAGE_ID']=='2') {
				$c=5;
			}


			$assigned_by_name = !empty($deal_new['ASSIGNED_BY_NAME']) ? $deal_new['ASSIGNED_BY_NAME'] : '';


			$assigned_by_last_name = !empty($deal_new['ASSIGNED_BY_LAST_NAME']) ? $deal_new['ASSIGNED_BY_LAST_NAME'] : '';

			$str_assigneds = getInitials($assigned_by_name) ." ". $assigned_by_last_name;


			$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal_new['ID']);


			if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {

				foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
				{
					$assign_user = CUser::GetByID((int)$assign)->Fetch();
					$str_assigneds .= ", ".getInitials($assign_user['NAME'])." ".$assign_user['LAST_NAME'];
				}
			}



			$title_deal_new = !empty($deal_new['TITLE']) ? $DB->ForSql($deal_new['TITLE']) : '';


			$stage_id_deal_new = !empty($deal_new['STAGE_ID']) ? $DB->ForSql($deal_new['STAGE_ID']) : '';


			//$date_create_deal_new = !empty($deal_new['DATE_CREATE']) ? date('Y-m-d H:i:s', strtotime($deal_new['DATE_CREATE'])) : '';
			//$date_create_deal_new = !empty($deal_new['DATE_CREATE']) ? date("d.m.Y H:i:s", strtotime($deal_new['DATE_CREATE'])) : '';
			$date_create_deal_new = !empty($deal_new['DATE_CREATE']) ? date("d.m.Y", strtotime($deal_new['DATE_CREATE'])) : '';


			$date_create_datetime_deal_new = !empty($deal_new['DATE_CREATE']) ? date("Y-m-d H:i:s", strtotime($deal_new['DATE_CREATE'])) : '';


			$company_title_deal_new = !empty($deal_new['COMPANY_TITLE']) ? $DB->ForSql($deal_new['COMPANY_TITLE']) : '';


			//$deadlineDate = ($deal_edited['UF_CRM_1577124965971'] ? $deal_edited['UF_CRM_1577124965971'] : ruDate($row['DEADLINE']));
			$deadlineDate = ($deal_new['UF_CRM_1577124965971'] ? $deal_new['UF_CRM_1577124965971'] : "01.01.1970");
			//if ($deadlineDate=="01.01.1970") $deadlineDate=date('d.m.Y H:i:s', strtotime($deal_new['DATE_CREATE'])+ 86400*3);
			if ($deadlineDate=="01.01.1970") $deadlineDate=date('d.m.Y', strtotime($deal_new['DATE_CREATE'])+ 86400*3);
			$deadline_title_deal_new = !empty($deadlineDate) ? $DB->ForSql($deadlineDate) : '';


			$mater_arr=array();
			$mater_arr_id=array();
			$res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=" . $id_deal_new);
			while($mater=$res_mat->Fetch())
			{
				$mater_arr[]=$mater['NAME_MATERIAL'];
				$mater_arr_id[]=$mater['ID_MATERIAL'];
			}
			$dealMaterials = ($deal_new['UF_CRM_1576411922643'] ? $deal_new['UF_CRM_1576411922643'] : implode(', ', $deal_new['UF_CRM_1569151529509']));
			$dealMaterials = ($dealMaterials  ? $dealMaterials : implode(', ', $mater_arr));


			$userr = CUser::GetByID($deal_new['ASSIGNED_BY_ID'])->Fetch();
			$id_laba = '';
			if (!empty($userr['UF_DEPARTMENT'][0])) {

				$id_laba = $userr['UF_DEPARTMENT'][0];
			}


			$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal_new['ID']);
			if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {

				foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
				{
					$assign_user = CUser::GetByID((int)$assign)->Fetch();
					if($assign_user['UF_DEPARTMENT'][0])
					{

						$id_laba .= ',' . $assign_user['UF_DEPARTMENT'][0];
					}
				}
			}


			$type_id_new = !empty($deal_new["TYPE_ID"]) ?  $DB->ForSql($deal_new["TYPE_ID"]) : '';


			$check_ip = ((!empty($_POST['check_ip']) && $_POST['check_ip'] == 'on') ? 1 : '');



			$manufacturers_new = null;
			$manufacturer_title = null;
			if (!empty($_POST['MANUFACTURERS'][0])) {

				$manufacturers_arr_new = !empty($_POST['MANUFACTURERS'][0]) ?  explode(', ID ', $_POST['MANUFACTURERS'][0]) : '';

				$manufacturers_new = !empty($manufacturers_arr_new[1]) ? $DB->ForSql($manufacturers_arr_new[1]) : '';


				$manufacturer_data = $requisite->getList(["filter" => ["ENTITY_ID" => $manufacturers_new, "ENTITY_TYPE_ID" => CCrmOwnerType::Company]])->fetchAll();


				$manufacturer_title = !empty($manufacturer_data[0]['NAME']) ? $manufacturer_data[0]['NAME'] : '';
			}



				$dogovor_num = !empty($_POST['DOGOVOR_NUM']) ? $DB->ForSql($_POST['DOGOVOR_NUM']) : '';
				$dogovor_num_arr = !empty($_POST['DOGOVOR_NUM']) ? explode(', ID', $_POST['DOGOVOR_NUM']) : '';
				$id_dogovor_num = !empty($dogovor_num_arr[1]) ? (int)$dogovor_num_arr[1] : '';
				$dogovor_name = !empty($dogovor_num_arr[0]) ? $dogovor_num_arr[0] : '';

				if (!empty($id_dogovor_num) && !empty($id_deal_new)) {
					$DB->Query("INSERT INTO `DEALS_TO_CONTRACTS` (`ID_DEAL`, `ID_CONTRACT`) VALUES (" . $id_deal_new . "," . $id_dogovor_num . ")");
				}



			if (!empty($res_select_new['ID'])) {
				$posit_short = (!empty($_REQUEST['Position2']) ? $_REQUEST['Position2'] : '');
				$posit_full = (!empty($_REQUEST['PositionGenitive']) ? $_REQUEST['PositionGenitive'] : '');


				$res_pos_l = $db_conn->select("SELECT POSIT_DEALDS_ID from ba_tz where ba_tz.ID_Z = {?}'", array($id_deal_edit));

				if(empty($res_pos_l[0]['POSIT_LEADS_ID'])) {
					$id = $db_conn->select("SHOW TABLE STATUS LIKE 'POSIT_LEADS'");
					$last_id_posit = $res_pos_l[0]['Auto_increment'];

					$arr_posit = array($posit_short, $posit_full);
					$sqlTot = "INSERT INTO `POSIT_LEADS` (POSIT_SHORT, POSIT_FULL) VALUES({?}, {?})";
					$ins_posit = $db_conn->queryExec($sqlTot, $arr_posit);

				}else{

					$id = $res_pos_l[0]['ID'];

					$arr_posit = array($posit_short, $posit_full, $id);
					$sqlTot = "UPDATE `POSIT_LEADS` SET POSIT_SHORT = {?}, POSIT_FULL = {?} WHERE ID = {?}";
					$ins_posit = $db_conn->queryExec($sqlTot, $arr_posit);
				}


				$res_assign_update = $DB->Query("UPDATE `ba_tz` SET `ASSIGNED`='" . $DB->ForSql($str_assigneds) . "', `REQUEST_TITLE`='" . $title_deal_new . "', `STAGE_ID`='" . $stage_id_deal_new . "', `STAGE_NUMBER`= " . (int)$c . ", `DATE_CREATE`= '" . $date_create_deal_new . "', `COMPANY_TITLE`= '" . $company_title_deal_new . "', `MATERIAL`= '" . $dealMaterials . "', `LABA_ID`= '" . $id_laba . "', `DATE_CREATE_TIMESTAMP`='" . $date_create_datetime_deal_new . "', `TYPE_ID`='" . $type_id_new . "', `POSIT_LEADS_ID` = '" . $id . "', `CHECK_IP`= '" . $check_ip . "'  WHERE `ID_Z`=" . $id_deal_new);

				// $res_assign_update = $DB->Query("UPDATE `ba_tz` SET `ASSIGNED`='" . $DB->ForSql($str_assigneds) . "', `REQUEST_TITLE`='" . $title_deal_new . "', `STAGE_ID`='" . $stage_id_deal_new . "', `STAGE_NUMBER`= " . (int)$c . ", `DATE_CREATE`= '" . $date_create_deal_new . "', `COMPANY_TITLE`= '" . $company_title_deal_new . "', `MATERIAL`= '" . $dealMaterials . "', `LABA_ID`= '" . $id_laba . "', `DATE_CREATE_TIMESTAMP`='" . $date_create_datetime_deal_new . "', `TYPE_ID`='" . $type_id_new . "', `CHECK_IP`= '" . $check_ip . "'  WHERE `ID_Z`=" . $id_deal_new);
			} else {

				$res_pos_l = $db_conn->select("SHOW TABLE STATUS LIKE 'POSIT_LEADS'");
				$last_id_posit = $res_pos_l[0]['Auto_increment'];

				$posit_short = (!empty($_REQUEST['Position2']) ? $_REQUEST['Position2'] : '');
				$posit_full = (!empty($_REQUEST['PositionGenitive']) ? $_REQUEST['PositionGenitive'] : '');

				$arr_posit = array($posit_short, $posit_full);
				$sqlTot = "INSERT INTO `POSIT_LEADS` (POSIT_SHORT, POSIT_FULL) VALUES({?}, {?})";
				$ins_posit = $db_conn->queryExec($sqlTot, $arr_posit);
				$mailArr= serialize($_POST['addEmail1']);
				$res_assign_insert = $DB->Query("INSERT INTO `ba_tz` (`ID_Z`, `ASSIGNED`, `REQUEST_TITLE`, `STAGE_ID`, `STAGE_NUMBER`, `DATE_CREATE`, `COMPANY_TITLE`, `DEADLINE_TABLE`, `MATERIAL`, `LABA_ID`, `DATE_CREATE_TIMESTAMP`, `TYPE_ID`, `CHECK_IP`, `MANUFACTURER`, `MANUFACTURER_TITLE`, `DOGOVOR_NUM`, `DOGOVOR_TABLE`, `POSIT_LEADS_ID`,`SAVE_MAIL`) VALUES ('" . $id_deal_new . "', '" . $DB->ForSql($str_assigneds) . "', '" . $title_deal_new . "', '" . $stage_id_deal_new . "', " . (int)$c . ", '" . $date_create_deal_new . "', '" . $company_title_deal_new . "', '" . $deadline_title_deal_new . "', '" . $dealMaterials . "', '" . $id_laba . "', '" . $date_create_datetime_deal_new . "', '" . $type_id_new . "', " . (int)$check_ip . ", '" . $manufacturers_new . "', '" . $manufacturer_title . "', '" . $dogovor_num . "', '" . $dogovor_name . "', '" . $last_id_posit . "','" . $mailArr .  "')");
				// $res_assign_insert = $DB->Query("INSERT INTO `ba_tz` (`ID_Z`, `ASSIGNED`, `REQUEST_TITLE`, `STAGE_ID`, `STAGE_NUMBER`, `DATE_CREATE`, `COMPANY_TITLE`, `DEADLINE_TABLE`, `MATERIAL`, `LABA_ID`, `DATE_CREATE_TIMESTAMP`, `TYPE_ID`, `CHECK_IP`, `MANUFACTURER`, `MANUFACTURER_TITLE`, `DOGOVOR_NUM`, `DOGOVOR_TABLE`) VALUES ('" . $id_deal_new . "', '" . $DB->ForSql($str_assigneds) . "', '" . $title_deal_new . "', '" . $stage_id_deal_new . "', " . (int)$c . ", '" . $date_create_deal_new . "', '" . $company_title_deal_new . "', '" . $deadline_title_deal_new . "', '" . $dealMaterials . "', '" . $id_laba . "', '" . $date_create_datetime_deal_new . "', '" . $type_id_new . "', " . (int)$check_ip . ", '" . $manufacturers_new . "', '" . $manufacturer_title . "', '" . $dogovor_num . "', '" . $dogovor_name . "')");

			}


		}
	}
}

$idEDIT = $updDeal['ID'] ? $updDeal['ID'] : $_GET['EDIT'];

if (!empty($_GET['EDIT'])) {
	echo "<div>
			<div class='return disable' title='Вернуться назад'></div>
			<div class='edit disable' title='Редактировать'></div>
			<div class='list disable' title='Вернуться к списку'></div>
			<a href='/request_card.php?ID=". $idEDIT ."'>
				<div class='card' title='Карточка заявки'></div>
			</a>
			<div class='download disable' title='Скачать'></div>
			<div class='copy disable' title='Скопировать'></div>
			<div class='remove disable' title='Удалить'></div>
			<div class='add disable' title='Добавить'></div>
			<div class='report disable' title='Отчёт'></div>
			<div class='further disable' title='Далее'></div>
		</div>
		<br><br>";
}

?>
<!--====================================РЕДАКТИРОВАНИЕ ЗАЯВКИ=========================================================================-->
<?php
if (!empty($_GET['EDIT']))
{
    $arRequisite = new \Bitrix\Crm\EntityRequisite();
	$deal = CCrmDeal::GetByID($_GET['EDIT']);
	$dbRes = $arRequisite->getList(["filter" => ["ENTITY_ID" => $deal['COMPANY_ID'], "ENTITY_TYPE_ID" => CCrmOwnerType::Company]]);
	$req = $dbRes->fetchAll();
	//echo'<pre>';var_dump('$req', $req);

	$text_pos = $db_conn->select("SELECT POSIT_LEADS.* FROM POSIT_LEADS, ba_tz WHERE ba_tz.POSIT_LEADS_ID = POSIT_LEADS.ID AND ba_tz.ID_Z = {?}", array($_GET['EDIT']));
	$pos_s = $text_pos[0]['POSIT_SHORT'];
	$pos_f = $text_pos[0]['POSIT_FULL'];

	if(empty($pos_f)) {
		$pos_s = $req[0]['RQ_COMPANY_REG_DATE'];
		$pos_f = '';
	}

	$mailCom = $db_conn->select("SELECT `SAVE_MAIL` FROM `ba_tz` WHERE `ID_Z`={?}",array($_GET['EDIT']));
	$mailCheck = $db_conn->select("SELECT `CHECK_MAIL` FROM `ba_tz` WHERE `ID_Z`= {?}",array($_GET['EDIT']));

	$pos_mail= unserialize($mailCom[0]['SAVE_MAIL']);
	$pos_check = unserialize($mailCheck[0]['CHECK_MAIL']);
	$data_request = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
	$materials_request = $data_request['UF_CRM_1569151529509']['VALUE'];
	$arrTitle = explode(' ', $deal['TITLE']);
	$arrTitleSlash = explode('/', $arrTitle[1]);
	$num = str_replace('№', '', $arrTitleSlash[0]);

	$rs = $arRequisite->getList([
		"filter" => [
			"ENTITY_ID" => $deal['COMPANY_ID'],
			"ENTITY_TYPE_ID" => CCrmOwnerType::Company,
		]
	]);
	$rows = $rs->fetchAll();
	//echo '<pre>';var_dump($rows);
	foreach ($rows as $companyReq) {
		$params = [
			'filter' => [
				'ENTITY_ID' => $companyReq['ID'],
				'ENTITY_TYPE_ID' => CCrmOwnerType::Requisite]
		];
		$bankReq = (new \Bitrix\Crm\EntityBankDetail)->getList($params)->fetch();



        $address = Bitrix\Crm\EntityRequisite::getAddresses($companyReq['ID']);

	}



	$company_id = !empty($deal['COMPANY_ID']) ? (int)$deal['COMPANY_ID'] : '';



	switch ($deal["TYPE_ID"]) {
    case 'SALE':
        $typeReqest="ИЦ";
        break;
    case 'COMPLEX':
        $typeReqest="ОСК";
        break;
    case '1':
        $typeReqest="ВЛК";
        break;
	case '2':
        $typeReqest="МСИ";
        break;
	case '4':
        $typeReqest="НК";
        break;
	}




		$result_select_ba_ts = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z` = " . (int)$_GET['EDIT'])->Fetch();




    $manufacturer_id = !empty($result_select_ba_ts['MANUFACTURER']) ? $result_select_ba_ts['MANUFACTURER'] : '';

	$manufacturer_value = '';
	if (!empty($manufacturer_id)) {

		$manufacturer_data = $arRequisite->getList(["filter" => ["ENTITY_ID" => $manufacturer_id, "ENTITY_TYPE_ID" => CCrmOwnerType::Company]])->fetchAll();

		$manufacturer_name = (!empty($manufacturer_data[0]['NAME']) ? $manufacturer_data[0]['NAME'] : '');  //Наименование компании
		$manufacturer_iin = (!empty($manufacturer_data[0]["RQ_INN"]) ? $manufacturer_data[0]["RQ_INN"] : ''); //ИИН компании
		$manufacturer_value = $manufacturer_name . ', ИНН '. $manufacturer_iin . ', ID ' . $manufacturer_id;
	}




	$select_check_tz = null;
	if (!empty($result_select_ba_ts['ID'])) {
		$select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $result_select_ba_ts['ID'] ." AND `date_return` IS NULL");
	}

	$selected_rows_count = !empty($select_check_tz->SelectedRowsCount()) ? $select_check_tz->SelectedRowsCount() : ''; //количество выбранных записей



?>
	<form id="form_tz" method="post">
        <table id="tz">
		<tbody name="new">

    	<tr><td width="250" height="40">Клиент <span class='redStars'>*<span></td><td width="450"><input class="tz" multiple list="clients" type="text" name="client"  value='<?= $deal['COMPANY_TITLE'].",ИНН ".$req[0]["RQ_INN"].", ID ".$deal['COMPANY_ID'] ?>' required><input name="id_comp" hidden value='<?= $deal['COMPANY_ID'] ?>'></td></tr>

		<tr><td width="250" height="40">ИП</td><td><input type="checkbox" name="check_ip" class="check-ip" <?= !empty($result_select_ba_ts["CHECK_IP"]) ? "checked" : "" ?>></td></tr>

		<tr><td width="250" height="40">Полное наименование компании</td><td><input class="tz" type="text" name="CompanyFullName" value='<?= $req[0]['RQ_COMPANY_FULL_NAME'] ?>'></td></tr>
		<tr><td width="250" height="40">ИНН <span class='redStars'><span></td><td><input class="tz" name="INN"  value='<?= $req[0]["RQ_INN"] ?>'></td></tr>

		<tr><td width="250" height="40">ОГРНИП</td><td><input type="text" class="tz" id="ogrnip" name="OGRNIP" value='<?= !empty($address[1]["REGION"]) ? $address[1]["REGION"] : "" ?>' <?= !empty($address[1]["REGION"]) ? "" : "disabled" ?>></td></tr>

		<tr><td width="250" height="40">ОГРН <span class='redStars'><span></td><td><input class="tz" type="text" name="OGRN" value='<?= $req[0]["RQ_OGRN"] ?>' ></td></tr>
		<tr><td width="250" height="40">Адрес <span class='redStars'><span></td><td><input class="tz" name="ADDR"   value='<?= $req[0]["RQ_ACCOUNTANT"] ?>'></td></tr>

        <tr>
            <td width="250" height="40">Фактический адрес</td>
            <td><input class="tz" id='actual_address' name="ACTUAL_ADDRESS"   value='<?= $address[1]["ADDRESS_1"] ?>'></td>
        </tr>


		<tr>
			<td width='250' height='40'>Почтовый адрес</td>
			<td>
				<input class='tz' name='mailingAddress' value='<?= $req[0]["RQ_COMPANY_NAME"]?>'>
			</td>
		</tr>

		<tr><td width="250" height="40">E-mail для договора: <span class='redStars'>*<span></td><td><input class="tz" type="text" name="EMAIL" value='<?= $req[0]["RQ_FIRST_NAME"] ?>'></td></tr>

<!-- $req[0]["RQ_FIRST_NAME"] -->
		<tr>
			<td width='250' height='40'>E-mail:<span class='redStars'>*<span></td>
			<td>
			<input class='tz' type='text' name='POST_MAIL' value='<?= $req[0]["RQ_EMAIL"] ?>'placeholder="_@_._" required>
				<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='addMailMain' name="addDopEmail" value='+' onclick="viewMail()"></td>
			</td>
		</tr>

		<tr id='mailDop' style='display:none'><td width='250' height='40'>Дополнительный E-mail1</td>
			<td>
			<input class='tz' type='text' name='addEmail1[1]' placeholder="_@_._" value='<?= $pos_mail[1]?>'>
			<td style='border-bottom:0px'><input style='margin-left:16px' type='checkbox' name='saveMail[1]' <?= !empty($pos_check[1]) ? 'checked' : '' ?> ></td>
			</td>
		</tr>
		<tr id='mailDop2' style='display:none'><td width='250' height='40'>Дополнительный E-mail2</td>
			<td>
			<input class='tz' type='text' name='addEmail1[2]'  placeholder="_@_._"value='<?=$pos_mail[2]?>'>
			<td style='border-bottom:0px'><input style='margin-left:16px' type='checkbox' name='saveMail[2]' <?= !empty($pos_check[2]) ? 'checked' : '' ?> ></td>
			</td>
		</tr>
		<tr id='mailDop3' style='display:none'><td width='250' height='40'>Дополнительный E-mail3</td>
			<td>
			<input class='tz' type='text' name='addEmail1[3]'  placeholder="_@_._" value='<?=$pos_mail[3]?>'>
			<td style='border-bottom:0px'><input style='margin-left:16px' type='checkbox' name='saveMail[3]' <?= !empty($pos_check[3]) ? 'checked' : '' ?> ></td>
			</td>
		</tr>

		<tr><td width="250" height="40">Телефон <span class='redStars'>*<span></td><td><input class="tz" name="PHONE"  value='<?= $req[0]["RQ_PHONE"] ?>' required></td></tr>


		<tr><td width="250" height="40">Контактное лицо</td><td><input class="tz" type="text" name="CONTACT" value='<?= $req[0]["RQ_NAME"] ?>'></td></tr>
		<tr><td width="250" height="40">КПП</td><td><input class="tz" type="text" name="KPP" value='<?= $req[0]['RQ_KPP'] ?>'></td></tr>
		<!-- <tr><td width="250" height="40">Должность руководителя</td><td><input class="tz" type="text" name="Position2" value='<?= $req[0]['RQ_COMPANY_REG_DATE'] ?>'></td></tr> -->

		<tr><td width="250" height="40">Должность руководителя</td><td><input class="tz" type="text" name="Position2" value='<?= $pos_s ?>'></td></tr>
		<tr><td width="250" height="40">Должность руководителя в родительном падеже</td><td><input class="tz" type="text" name="PositionGenitive" value='<?= $pos_f ?>'></td></tr>

		<tr><td width="250" height="40">ФИО руководителя</td><td><input class="tz" type="text" name="DirectorFIO" value='<?= $req[0]['RQ_DIRECTOR'] ?>'></td></tr>
		<tr><td width="250" height="40">Расчетный счет</td><td><input class="tz" type="text" name="RaschSchet" value='<?= $bankReq['RQ_ACC_NUM'] ?>'></td></tr>
		<tr><td width="250" height="40">Кор. счёт</td><td><input class="tz" type="text" name="KSchet" value='<?= $bankReq['RQ_COR_ACC_NUM'] ?>'></td></tr>
		<!--'Лицевой счет для договора' начало-->
		<tr><td width="250" height="40">Лицевой счёт</td><td><input class="tz" type="text" name="l_schet" value='<?= $bankReq['COMMENTS'] ?>'></td></tr>
		<!--'Лицевой счет для договора' окончание-->
		<tr><td width="250" height="40">БИК</td><td><input class="tz" type="text" name="BIK" value='<?= $bankReq['RQ_BIK'] ?>'></td></tr>
		<tr><td width="250" height="40">Наименование банка</td><td><input class="tz" type="text" name="BankName" value='<?= $bankReq['RQ_BANK_NAME'] ?>'></td></tr>

        <tr>
            <td width="250" height="40">Действует на основании</td>
            <td><input class="tz" type="text" name="ACTS_BASIS" value='<?= !empty($address[1]["ADDRESS_2"]) ? $address[1]["ADDRESS_2"] : 'Устава' ?>'></td>
        </tr>
		 <tr>
                <td width="250" height="40">Основание для проведения испытаний</td>
                <td>
                    <?php

                    if (!empty($result_select_ba_ts['ID_Z'])) {
                        $resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$result_select_ba_ts['ID_Z'])->Fetch();
                    }

                    ?>

                    <select class="tz check_dogovor" name="DOGOVOR_NUM" id="dogovor">

                        <?php
                        echo '<option></option>';
                        if (!empty($company_id)) {
							$ba_contr = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `CLIENT_ID` = " . $company_id);// WHERE `CLIENT_ID`=".$dealForTZ['COMPANY_ID']);
						}
						if(!empty($ba_contr)) {
                        while ($obj = $ba_contr->Fetch()) {
                            $idContract = null;

                            $selectedContract = (!empty($resDealsContract['ID_CONTRACT']) && $resDealsContract['ID_CONTRACT'] == $obj['ID']) ? 'selected' : '';

                            $objId = !empty($obj['ID']) ? $obj['ID'] : '';
                            $numberContract = !empty($obj['NUMBER']) ? $obj['NUMBER'] : $objId;

                            echo "<option data-id='" . $objId . "' " . $selectedContract . ">
								". ($obj['CONTRACT_TYPE'] ? $obj['CONTRACT_TYPE'] : 'Договор') ." №" . $numberContract . " от " . ruDate($obj['DATE']) . ", ID" . $objId . "
							</option>";
                        }
						}
                        ?>
                    </select>
                    <input class="tzHiddenСontract" type="text" value="" disabled>
                    <input type="hidden" name='dogovorNumHidden' value="">
                    <!--<input class='tzNoСontract' type='checkbox' name='noСontract'
                           <?= empty($resDealsContract['ID_CONTRACT']) ? 'checked' : '' ?>>Договор еще не составлен -->
                </td>
            </tr>
            <tr>



		<tr><td width="250" height="40">Тип заявки <span class='redStars'>*<span></td><td><select class="tz" name="REQ_TYPE" required>
		 <option value="SALE" <?= ($typeReqest == 'ИЦ') ? 'selected' : ''?>>ИЦ</option>
		 <option value="COMPLEX" <?= ($typeReqest == 'ОСК') ? 'selected' : ''?>>ОСК</option>
		 <option value="1" <?= ($typeReqest == 'ВЛК') ? 'selected' : ''?>>ВЛК</option>
		 <option value="2" <?= ($typeReqest == 'МСИ') ? 'selected' : ''?>>МСИ</option>
		 <option value="4" <?= ($typeReqest == 'НК') ? 'selected' : ''?>>НК</option>
		 </select>
		</td></tr>
		<tr><td width="250" height="40">Номер заявки <span class='redStars'>*<span></td><td><input class="tz" type="number" name="ACT_NUM" value='<?= $num ?>' required></td></tr>
		<?php
		$materialsReq = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = ". $_GET['EDIT']);
		$key_material=0;
		if($materialsReq->SelectedRowsCount()<1) {
		foreach($materials_request as $material) {
		 ?>
		<tr id="1"><td width="250" height="40">Материал для исследования </td>
		<td>
			<input class="tz" list="materials" type="text" name="MATERIAL[]"
			value='<?= $material ?>' disabled >
		<!--</td>
			<?php  if($key_material == 0) : ?>
			<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='add' value='+'></td>
			<?php else : ?>
			<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='del' value='-'></td>
			<?php endif; $key_material++; ?>
		</tr>-->
		<?php }
		}
		else
		{

		while ($material = $materialsReq->Fetch()) :
		  ?>
		<tr id="1"><td width="250" height="40">Материал для исследования </td>
		<td>
			<input class="tz" list="materials" type="text" name="MATERIAL[]"
			value='<?= $material['NAME_MATERIAL'] . ($material['ID_MATERIAL'] ? ', ID '.$material['ID_MATERIAL'] : '') ?>' disabled >
		<!--</td>
			<?php  if($key_material == 0) : ?>
			<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='add' value='+'></td>
			<?php else : ?>
			<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='del' value='-'></td>
			<?php endif; $key_material++; ?>
		</tr>-->
		<?php endwhile; }?>
		<!--<tr><td width="250" height="40">Производитель материала </td>
		<td><input class="tz" type="text" list="manufacturers" name="MANUFACTURERS[]" value='<?/*= $manufacturer_value */?>' size="20"><input name="id_manufacturer" hidden value='<?/*= $result_select_ba_ts['MANUFACTURER'] */?>'></td></tr>-->

		<tr id="1"><td width="250" height="40">Главный Ответственный <span class='redStars'>*<span></td><td><input id="first_assigned" class="tz" type="text" list="main_assigneds" name="ASSIGNED[]" value='<?= $arrResponsible[$deal["ASSIGNED_BY_ID"]] ?>' size="20" <?= !empty($selected_rows_count) ? 'readonly' : '' ?> required><input name="id_assign[]" hidden value=''></td>
		<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='add2' value='+'></td>
		</tr>

		<?php if(isset($data_request['UF_CRM_1571643970']['VALUE']) && !empty($data_request['UF_CRM_1571643970']['VALUE'])) {
					foreach($data_request['UF_CRM_1571643970']['VALUE'] as $otherResponsible) {
		?>

		<tr id="1"><td width="250" height="40">Ответственный </td><td><input class="tz" type="text" list="assigneds" name="ASSIGNED[]" value='<?= $arrResponsible[$otherResponsible] ?>' size="20" required><input name="id_assign[]" hidden value=''></td>
		<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='del' value='-'></td>
		</tr>

		<?php
				}
			}
		?>
		<!--<tr><td width="250" height="40">Срок выдачи протокола</td><td><input class="tz" type="date" name="DEADLINE" value="<?=date('Y-m-d')?>" size="20"></td></tr>-->

		</tbody>
		</table>

		<br />

        <input class="tz id-z" type="hidden" name="id_tz" value="<?= $deal['ID'] ?>">

        <input type="submit" value="Сохранить" id="new_tz">
    </form>

<?php
}
else
{

?>
<!--====================================СОЗДАНИЕ НОВОЙ ЗАЯВКИ=========================================================================-->
    <form id="form_tz" method="post">
        <table id="tz">
		<tbody name="new">
        <?php

		if(!empty($_POST['MATERIAL'])) {
			header('Location: request_card.php?ID='. $idEDIT);
			exit();
		}
//			echo "<h3>Заявка успешно создана</h3><br><a href='/request_card.php?ID=". $idEDIT ."' target='_blank'>
//				Перейти к созданной заявке
//			</a><br /></br>";


		$deal = CCrmDeal::GetByID($_GET['ID']);
		$mater_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $ID);
		$mater_arr = $mater_arr['UF_CRM_1569151529509']['VALUE'];


        ?>
		<?php
		$curYear = date("Y");
		$deals = CCrmDeal::GetList(['DATE_CREATE' => 'DESC'], ['>DATE_CREATE' => "01.01.{$curYear} 00:00:00", '!TYPE_ID' => '3']);
		$num_request=0;
		while($deal_c = $deals->Fetch())
		{
			//echo $deal_c['DATE_CREATE'];
			$num_request++;
		}


			?>

        <tr><td width="250" height="40">Клиент <span class='redStars'>*<span></td><td width="450"><input class="tz" multiple list="clients" type="text" name="client"  value="" required><input name="id_comp" hidden value=""></td></tr>

		<tr><td width="250" height="40">ИП</td><td><input type="checkbox" name="check_ip" class="check-ip"></td></tr>

		<tr><td width="250" height="40">Полное наименование компании</td><td><input class="tz" type="text" name="CompanyFullName" value=""></td></tr>
		<tr><td width="250" height="40">ИНН <span class='redStars'><span></td><td><input class="tz" name="INN" ></textarea></td></tr>

		<tr><td width="250" height="40">ОГРНИП</td><td><input type="text" class="tz" id="ogrnip" name="OGRNIP" disabled></td></tr>

		<tr><td width="250" height="40">ОГРН <span class='redStars'><span></td><td><input class="tz" type="text" name="OGRN" value=""  ></td></tr>
		<tr><td width="250" height="40">Адрес <span class='redStars'><span></td><td><input class="tz" name="ADDR"  ></textarea></td></tr>

        <tr>
            <td width="250" height="40">Фактический адрес</td>
            <td><input class="tz" id='actual_address' name="ACTUAL_ADDRESS"></textarea></td>
        </tr>


		<tr>
			<td width="250" height="40">Почтовый адрес</td>
			<td>
				<input class='tz' name='mailingAddress' value=''>
			</td>
		</tr>

		<tr><td width="250" height="40">E-mail для договора: <span class='redStars'>*<span></td><td><input class="tz" type="text" name="EMAIL" value=""></td></tr>

		<tr>
			<td width='250' height='40'>E-mail:<span class='redStars'>*<span></td>
			<td>
				<input class='tz' type='text' name='POST_MAIL' value='' required>


				<td style='border-bottom:0px'>
				<input style='margin-left:5px' type='button' class='addMailMain' name="addDopEmail" value='+' onclick="viewMail()">
			</td>
		</tr>
		<tr id='mailDop' style='display:none'><td width='250' height='40'>Дополнительный E-mail1</td>
			<td>
			<input class='tz' type='text' name='addEmail1[1]' placeholder="_@_._" value=''>
			<td style='border-bottom:0px'><input style='margin-left:16px' type='checkbox' name='saveMail[1]'></td>
			</td>
		</tr>
		<tr id='mailDop2' style='display:none'><td width='250' height='40'>Дополнительный E-mail2</td>
			<td>
			<input class='tz' type='text' name='addEmail1[2]'  placeholder="_@_._"value=''>
			<td style='border-bottom:0px'><input style='margin-left:16px' type='checkbox' name='saveMail[2]'></td>
			</td>
		</tr>
		<tr id='mailDop3' style='display:none'><td width='250' height='40'>Дополнительный E-mail3</td>
			<td>
			<input class='tz' type='text' name='addEmail1[3]'  placeholder="_@_._" value=''>
			<td style='border-bottom:0px'><input style='margin-left:16px' type='checkbox' name='saveMail[3]'></td>
			</td>
		</tr>

		<tr><td width="250" height="40">Телефон <span class='redStars'>*<span></td><td><input class="tz" name="PHONE"  required></textarea></td></tr>


		<tr><td width="250" height="40">Контактное лицо</td><td><input class="tz" type="text" name="CONTACT" value=""></td></tr>
		<tr><td width="250" height="40">КПП</td><td><input class="tz" type="text" name="KPP" value=""></td></tr>
		<tr><td width="250" height="40">Должность руководителя</td><td><input class="tz" type="text" name="Position2" value=""></td></tr>
		<tr><td width="250" height="40">Должность руководителя в родительном падеже</td><td><input class="tz" type="text" name="PositionGenitive" value=""></td></tr>

		<tr><td width="250" height="40">ФИО руководителя</td><td><input class="tz" type="text" name="DirectorFIO" value=""></td></tr>
		<tr><td width="250" height="40">Расчетный счет</td><td><input class="tz" type="text" name="RaschSchet" value=""></td></tr>
		<tr><td width="250" height="40">Кор. счёт</td><td><input class="tz" type="text" name="KSchet" value=""></td></tr>

		<tr><td width="250" height="40">Лицевой счёт</td><td><input class="tz" type="text" name="l_schet" value=''></td></tr>

		<tr><td width="250" height="40">БИК</td><td><input class="tz" type="text" name="BIK" value=""></td></tr>
		<tr><td width="250" height="40">Наименование банка</td><td><input class="tz" type="text" name="BankName" value=""></td></tr>

        <tr>
            <td width="250" height="40">Действует на основании</td>
            <td><input class="tz" type="text" name="ACTS_BASIS" value='Устава'></td>
        </tr>
		<tr>
                <td width="250" height="40">Основание для проведения испытаний</td>
                <td>
					<!--'Договор в request_new' начало-->
                    <select class="tz" name="DOGOVOR_NUM" id="dogovor">
					<!--'Договор в request_new' начало-->
                        <?php
                        echo '<option></option>';
                        $ba_contr = $DB->Query("SELECT * FROM `DOGOVOR`");// WHERE `CLIENT_ID`=".$dealForTZ['COMPANY_ID']);
                        while ($obj = $ba_contr->Fetch()) {
                            $objId = !empty($obj['ID']) ? $obj['ID'] : '';
                            $numberContract = !empty($obj['NUMBER']) ? $obj['NUMBER'] : $objId;

                             echo "<option data-id='" . $objId . "' " . $selectedContract . ">
								". ($obj['CONTRACT_TYPE'] ? $obj['CONTRACT_TYPE'] : 'Договор') ." №" . $numberContract . " от " . ruDate($obj['DATE']) . ", ID" . $objId . "
							</option>";
                        }
                        ?>
                    </select>
                    <input class="tzHiddenСontract" type="text" value="" disabled>
                    <input type="hidden" name='dogovorNumHidden' value="">
                 <!--   <input class='tzNoСontract' type='checkbox' name='noСontract'
                           <?= (isset($row['DOGOVOR_NUM']) && $row['DOGOVOR_NUM'] == '0') ? 'checked' : '' ?>>Договор
                    еще не составлен-->
                </td>
            </tr>



		<tr><td width="250" height="40">Тип заявки <span class='redStars'>*<span></td><td><select class="tz" name="REQ_TYPE" required>
		 <option value="SALE" <?=$row['CONTROL_LIST']=='0' ? 'selected' : ''?>>ИЦ</option>
		 <option value="COMPLEX" <?=$row['CONTROL_LIST']=='0' ? 'selected' : ''?>>ОСК</option>
		 <option value="1" <?=$row['CONTROL_LIST']=='0' ? 'selected' : ''?>>ВЛК</option>
		 <option value="2" <?=$row['CONTROL_LIST']=='1' ? 'selected' : ''?>>МСИ</option>
		 <option value="4" >НК</option>
		 </select>
		</td></tr>
		<tr><td width="250" height="40">Номер заявки <span class='redStars'>*<span></td><td><input class="tz" type="number" name="ACT_NUM" value="<?=$num_request+1?>" required></td></tr>
		<tr id="1"><td width="250" height="40">Материал для исследования <span class='redStars'>*<span></td><td><input class="tz" list="materials" type="text" name="MATERIAL[]" value="" required ></td>
		<td><input class="tz" type='number' name="OBIEM[]" min='1' value="1" required value=""></td>
		<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='add' value='+'></td>
		</tr>
		<!--<tr><td width="250" height="40">Производитель материала </td>
		<td><input class="tz" type="text" list="manufacturers" name="MANUFACTURERS[]" value='' size="20"><input name="id_manufacturer[]" hidden value=''></td></tr>-->

		<tr id="1"><td width="250" height="40">Главный Ответственный <span class='redStars'>*<span></td><td><input id="first_assigned" class="tz" type="text" list="main_assigneds" name="ASSIGNED[]" value="" size="20" required><input name="id_assign[]" hidden value=""></td>
		<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='add2' value='+'></td>
		</tr>
		<tr><td width="250" height="40">Создать заявку для каждого материала отдельно</td><td><input type="checkbox" name="few_deals" class="few_deals"></td></tr>


		<!--<tr><td width="250" height="40">Срок выдачи протокола</td><td><input class="tz" type="date" name="DEADLINE" value="<?=date('Y-m-d')?>" size="20"></td></tr>-->
		</tbody>
		</table>

		<br />

        <input class="tz" type="hidden" name="id_tz" value="<?= $row['ID'] ?>">

        <input type="submit" value="Сохранить" id="new_tz">

    </form>


<?php } (isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';?>
	<script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.js'></script>
<!--<script src='/local/js/inputmask.js'></script>-->
<script src='/local/js/inputmask/dist/jquery.inputmask.js'></script>

   <script>


        function gost() {
            $(".gost").change(function () {
                td = $(this).parent();
                tr = $(this).parent().parent();
                id = $(this).data('id');


                td.append("<p><label>"  + $('#gost option[value=' + $(this).val() + ']').text() +
                    "<input class='g_met' name='gost[" + id + "][]' type='hidden' value='" + $(this).val() +
                    "'></label>"+"&nbsp;"+"<a style='float: right;' href='#' onclick='del(this)'>X</a></p>");

					$(this).val('');//}

            })
        }
		function gost_new() {
            $(".gost_new").change(function () {
                td = $(this).parent();
                tr = $(this).parent().parent();
                id = $(this).data('id');

				td.append("<p><label>" + $('#gost_new option[value=' + $(this).val() + ']').text() +
                    "<input class='g_new' name='gost_new[" + id + "][]' type='hidden' value='" + $(this).val() +
                    "'></label>"+"&nbsp;"+"<a style='float: right;' href='#' onclick='del(this)'>X</a></p>");

					$(this).val('');// }

            })
        }

        function add(mater = '') {
            if (!isNaN($('.gost:last').data('id'))) {
                last_gost = $('.gost:last').data('id');
            } else {
                last_gost = 0;
            }

            $("#tz_body").append("<tr data-name='row_" + (last_gost + 1) + "'>" +
                "<td data-name='gost' valign='top'>      <input list='gost' class='gost' data-id='" + (last_gost + 1) + "'></td>" +
                "<td data-name='gost_new' valign='top'>  <input list='gost_new' class='gost_new' data-id='" + (last_gost + 1) + "'></td>"  +
				"<td></td>"+
                // "<td data-name='gost_new_desc'>  </td>" +
                // "<td data-name='norm_1'>    </td>" +
                // "<td data-name='norm_2'>    </td>" +
                // "<td data-name='ed_izm'>    </td>" +
                "<td data-name='a_href'>    <a href='#' onclick='$(this).parent().parent().remove()'>Удалить</a></td></tr>");
            gost(); gost_new();

        }
        $(document).ready(function () {
            $("#add").click(function () {
                add();
            })


            $("select[name=id_deal]").change(function () {
                optionSelected = $("option:selected", this);
                maters = optionSelected.data('mater').split(';');
                $.each(maters, function (index, value) {
                    if (value) {
                        console.log("INDEX: " + index + " VALUE: " + value);
                        add(value);
                    }
                });
            })

            gost(); gost_new();
        })


    </script>

<script>
		function viewMail() {
			document.getElementById("mailDop").style.display = "table-row";
			document.getElementById("mailDop2").style.display = "table-row";
			document.getElementById("mailDop3").style.display = "table-row";
		}
	</script>

	<script>
	$(document).ready(function () {

	$('input[name=INN]').inputmask("[9|-]{1,15}");
	$('input[name=OGRN]').inputmask("[9|-]{1,13}");

	$('input[name=PHONE]').inputmask({
	  mask: '*{30}',
	  placeholder: '',
	  definitions: {
				'*': {
					validator: "[0-9/./(/)-]",
				}
			}
	});


	/*
	$("input[name=EMAIL]").inputmask("email");

	$('#new_tz').on('click', function(event) {
		var mail = $("input[name=EMAIL]").val();
		var at = mail.indexOf("@");
		var point = mail.indexOf(".");
		var beforeAt = mail.slice(0, at);
		var afterAt = mail.slice(at + 1, point);
		var afterPoint = mail.slice(point + 1);
		var afterAtSymbol = mail.charAt(at + 1);
		var beforeAtSymbol = mail.charAt(at - 1);
		var afterPointSymbol = mail.charAt(point + 1);
		if ((beforeAt.length == 1 && beforeAtSymbol == '_') || (afterAt.length == 1 && afterAtSymbol == '_') || (afterPoint.length == 1 && afterPointSymbol == '_')) {
			event.preventDefault();
			alert('Заполните полностью email');
		}
	});*/

	//
	$("input[name=EMAIL]").attr('placeholder','_@_._');
	/*$('#new_tz').on('click', function(event) {
		var pattern = /^([a-z0-9_\.-])+@[a-z0-9-\.]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
		  if(!pattern.test($("input[name=EMAIL]").val().toLowerCase())){
			event.preventDefault();
			alert('Заполните полностью email');
		}
	});*/

	$("input[name=addEmail]").attr('placeholder','_@_._');

	$('#new_tz').on('click', function(event) {
		var eventTarget = event.target;
		//var formTz =  $(eventTarget).closest('#form_tz');
		var position2 = $('input[name=Position2]');
		var addEmail = $('input[name=addEmail]');
		var pattern = /^([a-z0-9_\.-])+@[a-z0-9-\.]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;

		/*if(!pattern.test($("input[name=EMAIL]").val().toLowerCase()))
		{
			event.preventDefault();
			alert('Заполните полностью email');
			return true;
		}*/

		//
		/*if(!pattern.test($("input[name=addEmail]").val().toLowerCase()))
		{
			event.preventDefault();
			alert('Заполните полностью дополнительный email');
			return true;
		}*/

		// if ($(position2).val().length > 30)
		// {
		// 	event.preventDefault();
		// 	alert('Должность руководителя количество введенных символов превышает допустимое');
		// 	return true;
		// }

		if ($(addEmail).val().length > 50)
		{
			event.preventDefault();
			alert('Дополнительный E-mail количество введенных символов превышает допустимое');
			return true;
		}


	});



	$("tbody").on("click", ".del", function () {
		  $(this).parent().parent().remove();
		});

	$("tbody[name=new]").on("click", ".add2", function () {
		   tbody = $(this).parent().parent().parent();
			tr = $(this).parent().parent();
			tr_dataid = tr.attr('id');
			console.log(tr_dataid);
			$.ajax({

			  url: 'select_for_newrequest2.php?KEY='+tr_dataid,

			  success: function(data) {

				tr.after(data);

			  }

			});

	});

	$("tbody[name=new]").on("click", ".add", function () {
		   table = $(this).parent().parent().parent().parent();
		   tbody = $(this).parent().parent().parent();
			tr = $(this).parent().parent();
			tr_dataid = tr.attr('id');
			console.log(tr_dataid);
			$.ajax({

			  url: 'select_for_newrequest.php?KEY='+tr_dataid,

			  success: function(data) {

				tr.after(data);

			  }

			});



	});

	$("tbody [name=new]").on("click", ".addMail", function () {
            tbody = $(this).parent().parent().parent();
            tr = $(this).parent().parent();
            tr_dataid = tr.attr('id');
            console.log(tr_dataid);
            $.ajax({

                url: 'select_for_newrequest3.php?KEY='+tr_dataid,

                success: function(data) {

                    tr.after(data);

                }

            });

        });

	$("input[name=client]").on("input", function() {
		$("input[name='id_comp']").val('');
	});


    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

	$("input[name=client]").on("change", function() {

		arr= $(this).val().split(',');
		//console.log(arr)
		arr_inn=arr[2].slice(4);
		client_id= $('#clients option[data-id='+arr_inn+']').data('id');
        requisiteId= $('#clients option[data-id='+arr_inn+']').data('requisite');
		client_inn= $('#clients option[data-id='+arr_inn+']').data('inn');
		client_ogrn= $('#clients option[data-id='+arr_inn+']').data('ogrn');
		client_addr= $('#clients option[data-id='+arr_inn+']').data('addr');
		client_name= $('#clients option[data-id='+arr_inn+']').data('name');
		client_phone= $('#clients option[data-id='+arr_inn+']').data('phone');
		client_email= $('#clients option[data-id='+arr_inn+']').data('email');

		client_kpp = $('#clients option[data-id='+arr_inn+']').data('kpp');
		client_position = $('#clients option[data-id='+arr_inn+']').data('position');
		client_genitive_position = $('#clients option[data-id='+arr_inn+']').data('position');
		client_company = $('#clients option[data-id='+arr_inn+']').data('company');
		client_director = $('#clients option[data-id='+arr_inn+']').data('director');

        var client_raschschet = '';
        var client_kschet = '';
        var client_bik = '';
        var client_bank = '';


		client_addemail = $('#clients option[data-id='+arr_inn+']').data('addEmail');
		client_mail = $('#clients option[data-id='+arr_inn+']').data('mail');

        var client_address = ''
        var client_basis = '';
        var client_ogrnip = '';
		var client_lschet = '';



        $.ajax({
            url: 'get_entity_bank_detail.php',
            method: 'post',
            async: false,
            data: {'requisiteId': requisiteId},
            success: function (entityBankDetail) {

                entityBankDetail = isJson(entityBankDetail) ? JSON.parse(entityBankDetail) : [];


                client_raschschet = entityBankDetail['RQ_ACC_NUM'] ? entityBankDetail['RQ_ACC_NUM'] : ''
                client_kschet = entityBankDetail['RQ_COR_ACC_NUM'] ? entityBankDetail['RQ_COR_ACC_NUM'] : '';
                client_bik = entityBankDetail['RQ_BIK'] ? entityBankDetail['RQ_BIK'] : '';
                client_bank = entityBankDetail['RQ_BANK_NAME'] ? entityBankDetail['RQ_BANK_NAME'] : '';
                client_address = entityBankDetail['ADDRESS_1'] ? entityBankDetail['ADDRESS_1'] : '';
                client_basis = entityBankDetail['ADDRESS_2'] ? entityBankDetail['ADDRESS_2'] : '';
                client_ogrnip = entityBankDetail['REGION'] ? entityBankDetail['REGION'] : '';
                client_lschet = entityBankDetail['COMMENTS'] ? entityBankDetail['COMMENTS'] : '';
            }
        });

		tbody = $(this).parent().parent().parent();
		id_input = tbody.find("input[name=id_comp]");
		inn_input = tbody.find("input[name=INN]");
		ogrn_input = tbody.find("input[name=OGRN]");
		addr_input = tbody.find("input[name=ADDR]");
		name_input = tbody.find("input[name=CONTACT]");
		phone_input = tbody.find("input[name=PHONE]");
		email_input = tbody.find("input[name=POST_MAIL]");

		kpp_input = tbody.find("input[name=KPP]");
		position_input= tbody.find("input[name=Position2]");
		position_genitive_input= tbody.find("input[name=PositionGenitive]");
		company_input = tbody.find("input[name=CompanyFullName]");
		director_input = tbody.find("input[name=DirectorFIO]");
		raschschet_input = tbody.find("input[name=RaschSchet]");
		kschet_input = tbody.find("input[name=KSchet]");
		bik_input = tbody.find("input[name=BIK]");
		bank_input = tbody.find("input[name=BankName]");

		addemail_input = tbody.find("input[name=EMAIL]");
		mail_input= tbody.find("input[name=mailingAddress]");


        address_input = tbody.find("input[name=ACTUAL_ADDRESS]");


        basis_input = tbody.find("input[name=ACTS_BASIS]");


		ogrnip_input = tbody.find("input[name=OGRNIP]");


		lschet_input = tbody.find("input[name=l_schet]");



		id_input[0].value = client_id;
		inn_input[0].value = client_inn;
		ogrn_input[0].value = client_ogrn;
		addr_input[0].value = client_addr;
		 name_input[0].value = client_name;
		phone_input[0].value = client_phone;
		email_input[0].value = client_email;
		//console.log(client_id);

		kpp_input[0].value = client_kpp;
		position_input[0].value = client_position;
		position_genitive_input[0].value = client_genitive_position;
		company_input[0].value = client_company;
		director_input[0].value = client_director;
		 raschschet_input[0].value = client_raschschet;
		kschet_input[0].value = client_kschet;
		bik_input[0].value = client_bik;
		bank_input[0].value = client_bank;

		if(!!client_addemail){
			addemail_input[0].value = client_addemail;
		}else{
			addemail_input[0].value = '';
		}
		mail_input[0].value = client_mail;


        address_input[0].value = client_address;


        basis_input[0].value = client_basis ? client_basis : 'Устава';


		ogrnip_input[0].value = client_ogrnip;


		lschet_input[0].value = client_lschet;




		if (client_id) {

			var dogovorNum = $('#dogovor');

			$.ajax({
				method: 'POST',
				url: 'add_dogovor_client.php',
				data: {id_client: client_id},
				dataType: "json",
				success: function(dataDogovor) {

					 if (dogovorNum.find('option').length) {
						 dogovorNum.find('option').remove();
					 }


					$(dogovorNum).append('<option data-id=""></option>');

					if(dataDogovor) {
						$.each(dataDogovor, function(index, dogovor) {

							var idDogovor = dogovor['id_dogovor'] ? dogovor['id_dogovor'] : '';
							var typeDogovor = dogovor['type_dogovor'] ? dogovor['type_dogovor'] : '';
							var numberDogovor = dogovor['number_dogovor'] ? dogovor['number_dogovor'] : '';
							var dateDogovor = dogovor['date_dogovor'] ? dogovor['date_dogovor'] : '';


							$(dogovorNum).append('<option data-id=' + idDogovor + '>' + typeDogovor + ' №' + numberDogovor + ' от ' + dateDogovor + ', ID' + idDogovor + '</option>');
						});

					}
				}

			});
		}


	});



	$('#form_tz').on('change', '.check_dogovor', function(event) {
		var idZ = $('.id-z').val();
		var dogovorNum = $('.check_dogovor option:selected').val();

		$.ajax({
			method: 'POST',
			url: 'check_dogovor.php',
			data: {id_z: idZ, dogovor_num: dogovorNum},
			success: function(checkDogovor) {

				if (checkDogovor) {
					var resultConfirm = confirm('Текущии цены могут не соответствовать выбранному договору!Продолжить?');


					if (!resultConfirm) {

						$('.check_dogovor option').each(function (indexOption, option) {
							if ($(option).data('id') == checkDogovor) {
								$(option).prop('selected', true);
							}
						});
					}
				}
			}
		});
	});



	$("input[name=ASSIGNED]").on("change", function() {

		arr= $(this).val().split(',');
		arr_id=arr[1].slice(4);
		assign_id= $('#assigneds option[data-id='+arr_id+']').data('id');
		tbody = $(this).parent().parent().parent();
		id_input = tbody.find("input[name=id_assign]");
		id_input[0].value = assign_id;
		//console.log(id_input[0].value);
		});

		$("input[name=MANUFACTURERS]").on("change", function() {

		arr= $(this).val().split('ID ');
		arr_id=arr[1];
		assign_id= $('#manufacturers option[data-id='+arr_id+']').data('id');
		tbody = $(this).parent().parent().parent();
		id_input = tbody.find("input[name=id_manufacturer]");
		id_input[0].value = assign_id;
		//console.log(id_input[0].value);
		});

		$("tbody[name=tz_body]").on("change", ".gost_d", function() {
			optionSelected = $("option:selected", this);
			price = optionSelected.data('price');
			tr = $(this).parent().parent();
			price_input = tr.find(".oborud");
			price_input[0].value = price;
		//	console.log(price);
		});


		$('.check-ip').on('change', function() {
			var checkIp = $('.check-ip');
			var ogrnip = $('#ogrnip');

			if ($('.check-ip').prop('checked')) {
				$('#ogrnip').prop('disabled', false);
			} else {
				$('#ogrnip').prop('disabled', true);
			}
		});





		$('#form_tz').on('submit', function(event) {
			var idRequest = $('.id-z').val() ? $('.id-z').val() : ''; //id заявки

			var arrMainAssigned = $('#first_assigned').val() ? $('#first_assigned').val().split(', ID') : '';

			var idMainAssigned = arrMainAssigned[1] ? $.trim(arrMainAssigned[1]) : '';
			var _this = this;

			if (idMainAssigned) {
			event.preventDefault();

				$.ajax({
					method: 'POST',
					url: 'check_user.php',
					//data: {id_main_assigned: idMainAssigned},
					data: {id_main_assigned: idMainAssigned, id_request: idRequest},
					success: function(dataUser) {

						if (!dataUser) {
							_this.submit();
						} else {
							alert(dataUser);
						}
					}
				});
			}

		});
		
	})
 </script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
