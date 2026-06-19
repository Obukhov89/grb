<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
	
$APPLICATION->SetTitle("Формирование заявки на испытания");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

?>
<link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
<script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous">
</script>
<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
<script>
	 $(document).ready(function ()
	 {
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
		if(in_array($userData['UF_DEPARTMENT'][0], $lab) || $option['ID']==1 || $option['ID']==35 || $option['ID']==62)
		{
			echo "<option data-id='" . $option['ID'] . "'" . $selected . " value='". $option['LAST_NAME'] . ' ' . $option['NAME'] . ', ID '. $option['ID'] . "'></option>";
			$arrResponsible[$option['ID']] = $option['LAST_NAME'] . ' ' . $option['NAME'] . ', ID '. $option['ID'];
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
		$params = [
			'filter' => [
				'ENTITY_ID' => $rss['ID'],
				'ENTITY_TYPE_ID' => CCrmOwnerType::Requisite]
		];
		$bankReq = (new \Bitrix\Crm\EntityBankDetail)->getList($params)->fetch();


        $address = Bitrix\Crm\EntityRequisite::getAddresses($rss['ID']);

		
		$a = $rss['ENTITY_ID'];
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
		$requis[$a]['RQ_ACC_NUM'] = !empty($bankReq['RQ_ACC_NUM']) ? $bankReq['RQ_ACC_NUM'] : '';
		$requis[$a]['RQ_COR_ACC_NUM'] = !empty($bankReq['RQ_COR_ACC_NUM']) ? $bankReq['RQ_COR_ACC_NUM'] : '';
		$requis[$a]['RQ_BIK'] = !empty($bankReq['RQ_BIK']) ? $bankReq['RQ_BIK'] : '';
		$requis[$a]['RQ_BANK_NAME'] = !empty($bankReq['RQ_BANK_NAME']) ? $bankReq['RQ_BANK_NAME'] : '';
		

		$requis[$a]['RQ_FIRST_NAME'] = !empty($rss['RQ_FIRST_NAME']) ? $rss['RQ_FIRST_NAME'] : '';
		$requis[$a]['RQ_COMPANY_NAME'] = !empty($rss['RQ_COMPANY_NAME']) ? $rss['RQ_COMPANY_NAME'] : '';

        $requis[$a][1]["ADDRESS_1"] = !empty($address[1]["ADDRESS_1"]) ? $address[1]["ADDRESS_1"] : '';

        $requis[$a][1]["ADDRESS_2"] = !empty($address[1]["ADDRESS_2"]) ? $address[1]["ADDRESS_2"] : '';

		$requis[$a][1]["REGION"] = !empty($address[1]["REGION"]) ? $address[1]["REGION"] : '';

		$requis[$a]['COMMENTS'] = !empty($bankReq['COMMENTS']) ? $bankReq['COMMENTS'] : '';

	
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
		$raschschet = $requis[$a]['RQ_ACC_NUM'];
		$kschet = $requis[$a]['RQ_COR_ACC_NUM'];
		$bik = $requis[$a]['RQ_BIK'];
		$bank = $requis[$a]['RQ_BANK_NAME'];
		

		$addemail = $requis[$a]['RQ_FIRST_NAME'];
		$mail = $requis[$a]['RQ_COMPANY_NAME'];

        $address1 = !empty($requis[$a][1]["ADDRESS_1"]) ? $requis[$a][1]["ADDRESS_1"] : '';

        $basis = !empty($requis[$a][1]["ADDRESS_2"]) ? $requis[$a][1]["ADDRESS_2"] : '';
		

		$ogrnip = !empty($requis[$a][1]["REGION"]) ? $requis[$a][1]["REGION"] : '';

		$lschet = $requis[$a]['COMMENTS'];

		echo "<option data-id='" . $a . "' data-inn = '".$inn."' data-ogrn = '".$ogrn."' data-ogrnip = '".$ogrnip."'
		data-addr = '".$addr."' data-name = '".$name."' data-phone = '".$phone."' 
		data-email = '".$email."'  data-kpp='".$kpp."' data-position='".$position."' 
		data-company='".$company."' data-director='".$director."' data-lschet='".$lschet."'
		data-raschschet='".$raschschet."' data-kschet='".$kschet."' data-bik='".$bik."' 
		data-bank='".$bank."' data-addemail='".$addemail."' data-mail='".$mail."' 
		data-address = '".$address1."' data-basis = '".$basis."' value='" . $arComp. ', ИНН '. $inn. ', ID ' . $a ."'></option>";

		
		/*echo "<option data-id='" . $a . "' data-inn = '".$inn."' data-ogrn = '".$ogrn."' 
		data-addr = '".$addr."' data-name = '".$name."' data-phone = '".$phone."' 
		data-email = '".$email."'  data-kpp='".$kpp."' data-position='".$position."' 
		data-company='".$company."' data-director='".$director."' 
		data-raschschet='".$raschschet."' data-kschet='".$kschet."' data-bik='".$bik."' 
		data-bank='".$bank."' value='" . $arComp. ', ИНН '. $inn. ', ID ' . $a ."'></option>";*/

		

	}
    ?>
</datalist>

<?php
//------------------------------------СОХРАНЕНИЕ ЗАЯВКИ------------------------------------------------------------------
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
	}
	
	if(!$_POST['id_comp'])
	{
		$newComp = new CCrmCompany;
		$nameClient = explode(",",$_POST['client']);
		$arParam=array("TITLE" => $nameClient[0]);
		$newComp = $newComp->Add($arParam);
	}
	
	$deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('>DATE_CREATE' => '01.01.2020 00:00:00', '!TYPE_ID' => '3'));
	$num_request=0;
	while($deal_c = $deals->Fetch())
	{
		//echo $deal_c['DATE_CREATE'];
		$num_request++;
	}
	
	$num_request += 5;

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
		
		if (CModule::IncludeModule('crm')) {
			$deal = new CCrmDeal;
			$arDealUpdate = array(
				"COMPANY_ID" => $_POST['id_comp'] ? $_POST['id_comp'] : $newComp,
				"ASSIGNED_BY_ID" => $ASSIGNED_BY_ID,
				"TYPE_ID" => $_POST['REQ_TYPE'],
				"TITLE" => $TYPE." №".$_POST['ACT_NUM'].getCurrentYearForDocs(),
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
		$fields['ENTITY_ID'] = ($newComp ? $newComp : $reqData[0]['ENTITY_ID']);
		$fields['ENTITY_TYPE_ID'] = ($newComp ? '4' : $reqData[0]['ENTITY_TYPE_ID']);
		$fields['PRESET_ID'] = ($newComp ? "1" : $reqData[0]['PRESET_ID']);
		$fields['NAME'] = ($newComp ? $nameClient[0] : $reqData[0]['NAME']);
		$fields['SORT'] = 500;
		$fields['ACTIVE'] = 'Y';
		$fields['RQ_INN'] = $_POST['INN'];
		$fields['RQ_ACCOUNTANT'] = $_POST['ADDR'];
		$fields['RQ_OGRN'] = $_POST['OGRN'];
		$fields['RQ_EMAIL'] = $_POST['EMAIL'];
		$fields['RQ_PHONE'] = $_POST['PHONE'];
		$fields['RQ_NAME'] = $_POST['CONTACT'];

		$fields['RQ_KPP'] = !empty($_POST['KPP']) ? $_POST['KPP'] : '';
		$fields['RQ_COMPANY_REG_DATE'] = !empty($_POST['Position2']) ? $_POST['Position2'] : '';
		$fields['RQ_COMPANY_FULL_NAME'] = !empty($_POST['CompanyFullName']) ? $_POST['CompanyFullName'] : '';
		$fields['RQ_DIRECTOR'] = !empty($_POST['DirectorFIO']) ? $_POST['DirectorFIO'] : '';
		$fields['RQ_ACC_NUM'] = !empty($_POST['RaschSchet']) ? $_POST['RaschSchet'] : '';
		$fields['RQ_COR_ACC_NUM'] = !empty($_POST['KSchet']) ? $_POST['KSchet'] : '';
		$fields['RQ_BIK'] = !empty($_POST['BIK']) ? $_POST['BIK'] : '';
		$fields['RQ_BANK_NAME'] = !empty($_POST['BankName']) ? $_POST['BankName'] : '';
		

		$fields['RQ_FIRST_NAME'] = !empty($_POST['addEmail']) ? $_POST['addEmail'] : '';
		$fields['RQ_COMPANY_NAME'] = !empty($_POST['mailingAddress']) ? $_POST['mailingAddress'] : '';

        $fields["RQ_ADDR"][1]["ADDRESS_1"] = !empty($_POST["ACTUAL_ADDRESS"]) ? $_POST["ACTUAL_ADDRESS"] : '';

        $fields["RQ_ADDR"][1]["ADDRESS_2"] = !empty($_POST["ACTS_BASIS"]) ? $_POST["ACTS_BASIS"] : '';

		$fields['RQ_ADDR'][1]['REGION'] = !empty($_POST['OGRNIP']) ? $_POST['OGRNIP'] : '';

		$fields['COMMENTS'] = !empty($_POST['l_schet']) ? $_POST['l_schet'] : '';

		
		$req_id = ($newComp ? $newComp : $reqData[0]['ENTITY_ID']);
		

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

		if (!empty($res_select_edit['ID'])) {

			$res_assign_update = $DB->Query("UPDATE `ba_tz` SET `ASSIGNED`='" . $DB->ForSql($str_assigneds) . "', `REQUEST_TITLE`='" . $title_deal_edit . "', `STAGE_ID`='" . $stage_id_deal_edit . "', `STAGE_NUMBER`= " . (int)$c . ", `COMPANY_TITLE`= '" . $company_title_deal_edit . "', `LABA_ID`= '" . $id_laba . "', `TYPE_ID`= '" . $type_id_edited . "', `CHECK_IP`= '" . $check_ip . "' WHERE `ID_Z`=" . $id_deal_edit);
		} else {
			$res_assign_insert = $DB->Query("INSERT INTO `ba_tz` (`ID_Z`, `ASSIGNED`, `REQUEST_TITLE`, `STAGE_NUMBER`, `STAGE_ID`, `COMPANY_TITLE`, `LABA_ID`, `TYPE_ID`, `CHECK_IP`) VALUES ('" . $id_deal_edit . "', '" . $DB->ForSql($str_assigneds) . "', '" . $title_deal_edit . "', '" . $stage_id_deal_edit . "', " . (int)$c . ", '" . $company_title_deal_edit . "', '" . $id_laba . "', '" . $type_id_edited . "', '" . $check_ip . "')");

		}
		

	} 
	else //---------------------СОХРАНЕНИЕ ПРИ СОЗДАНИИ------------------------
	{
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
		$fields['RQ_EMAIL'] = $_POST['EMAIL'];
		$fields['RQ_PHONE'] = $_POST['PHONE'];
		$fields['RQ_NAME'] = $_POST['CONTACT'];

		$fields['RQ_KPP'] = !empty($_POST['KPP']) ? $_POST['KPP'] : '';
		$fields['RQ_COMPANY_REG_DATE'] = !empty($_POST['Position2']) ? $_POST['Position2'] : '';
		$fields['RQ_COMPANY_FULL_NAME'] = !empty($_POST['CompanyFullName']) ? $_POST['CompanyFullName'] : '';
		$fields['RQ_DIRECTOR'] = !empty($_POST['DirectorFIO']) ? $_POST['DirectorFIO'] : '';
		$fields['RQ_ACC_NUM'] = !empty($_POST['RaschSchet']) ? $_POST['RaschSchet'] : '';
		$fields['RQ_COR_ACC_NUM'] = !empty($_POST['KSchet']) ? $_POST['KSchet'] : '';
		$fields['RQ_BIK'] = !empty($_POST['BIK']) ? $_POST['BIK'] : '';
		$fields['RQ_BANK_NAME'] = !empty($_POST['BankName']) ? $_POST['BankName'] : '';
		

		$fields['RQ_FIRST_NAME'] = !empty($_POST['addEmail']) ? $_POST['addEmail'] : '';
		$fields['RQ_COMPANY_NAME'] = !empty($_POST['mailingAddress']) ? $_POST['mailingAddress'] : '';

        $fields["RQ_ADDR"][1]["ADDRESS_1"] = !empty($_POST["ACTUAL_ADDRESS"]) ? $_POST["ACTUAL_ADDRESS"] : '';

        $fields["RQ_ADDR"][1]["ADDRESS_2"] = !empty($_POST["ACTS_BASIS"]) ? $_POST["ACTS_BASIS"] : '';

		$fields['RQ_ADDR'][1]['REGION'] = !empty($_POST['OGRNIP']) ? $_POST['OGRNIP'] : '';

		$fields['COMMENTS'] = !empty($_POST['l_schet']) ? $_POST['l_schet'] : '';

		
		$req_id = ($newComp ? $newComp : $reqData[0]['ENTITY_ID']);
		
		$requisite->deleteByEntity(CCrmOwnerType::Company, $req_id);
		$res = $requisite->add($fields);
		
		$fields['ENTITY_ID'] = $res->getId();
		$fields['ENTITY_TYPE_ID'] = CCrmOwnerType::Requisite;
		$res2 = $bankObj->add($fields);

		$id_deal = ($_GET['EDIT']) ? $dealUp['ID'] : $updDeal['ID'];
		
		foreach($ar_material_id as $key => $ar_m)
		{
			$DB->Query("INSERT INTO `MATERIALS_TO_REQUESTS` (`ID_DEAL`, `ID_MATERIAL`, `NAME_MATERIAL`) VALUES (".(int)$id_deal.", ".(int)$ar_m.",'".$ar_material[$key]."')");
		}


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


		$date_create_deal_new = !empty($deal_new['DATE_CREATE']) ? date("d.m.Y", strtotime($deal_new['DATE_CREATE'])) : '';


		$date_create_datetime_deal_new = !empty($deal_new['DATE_CREATE']) ? date("Y-m-d H:i:s", strtotime($deal_new['DATE_CREATE'])) : '';
		

		$company_title_deal_new = !empty($deal_new['COMPANY_TITLE']) ? $DB->ForSql($deal_new['COMPANY_TITLE']) : '';
		

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

		if (!empty($res_select_new['ID'])) {

			//$res_assign_update = $DB->Query("UPDATE `ba_tz` SET `ASSIGNED`='" . $DB->ForSql($str_assigneds) . "', `REQUEST_TITLE`='" . $title_deal_new . "', `STAGE_ID`='" . $stage_id_deal_new . "', `STAGE_NUMBER`= " . (int)$c . ", `DATE_CREATE`= '" . $date_create_deal_new . "', `COMPANY_TITLE`= '" . $company_title_deal_new . "', `MATERIAL`= '" . $dealMaterials . "', `LABA_ID`= '" . $id_laba . "', `DATE_CREATE_TIMESTAMP`='" . $date_create_datetime_deal_new . "', `TYPE_ID`='" . $type_id_new . "', `CHECK_IP`= '" . $check_ip . "'  WHERE `ID_Z`=" . $id_deal_new);
		} else {
			$res_assign_insert = $DB->Query("INSERT INTO `ba_tz` (`ID_Z`, `ASSIGNED`, `REQUEST_TITLE`, `STAGE_ID`, `STAGE_NUMBER`, `DATE_CREATE`, `COMPANY_TITLE`, `DEADLINE_TABLE`, `MATERIAL`, `LABA_ID`, `DATE_CREATE_TIMESTAMP`, `TYPE_ID`, `CHECK_IP`) VALUES ('" . $id_deal_new . "', '" . $DB->ForSql($str_assigneds) . "', '" . $title_deal_new . "', '" . $stage_id_deal_new . "', " . (int)$c . ", '" . $date_create_deal_new . "', '" . $company_title_deal_new . "', '" . $deadline_title_deal_new . "', '" . $dealMaterials . "', '" . $id_laba . "', '" . $date_create_datetime_deal_new . "', '" . $type_id_new . "', " . (int)$check_ip . ")");

		}
 

		
	} 
}

$idEDIT = $updDeal['ID'] ? $updDeal['ID'] : $_GET['EDIT'];

if (!empty($_GET['EDIT'])) {
	echo "<div>
			<div class='return disable' title='Вернуться назад'></div>
			<div class='edit disable' title='Редактировать'></div>
			<div class='list disable' title='Вернуться к списку'></div>
			<a href='/request_card.php?ID=". $idEDIT ."' target='_blank'>
				<div class='card' title='Карточка заявки'></div><!--Перейти в карточку заявки-->
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
	
	foreach ($rows as $companyReq) {
		$params = [
			'filter' => [
				'ENTITY_ID' => $companyReq['ID'],
				'ENTITY_TYPE_ID' => CCrmOwnerType::Requisite]
		];
		$bankReq = (new \Bitrix\Crm\EntityBankDetail)->getList($params)->fetch();


        $address = Bitrix\Crm\EntityRequisite::getAddresses($companyReq['ID']);

	}

	
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
	}
	

		$result_select_ba_ts = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z` = " . (int)$_GET['EDIT'])->Fetch();

?>
	<form id="form_tz" method="post">
        <table id="tz"> 
		<tbody name="new">

    	<tr><td width="250" height="40">Клиент <span class='redStars'>*<span></td><td width="450"><input class="tz" multiple list="clients" type="text" name="client"  value='<?= $req[0]["NAME"].",ИНН ".$req[0]["RQ_INN"].", ID ".$deal['COMPANY_ID'] ?>' required><input name="id_comp" hidden value='<?= $deal['COMPANY_ID'] ?>'></td></tr>

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

		<tr><td width="250" height="40">E-mail <span class='redStars'>*<span></td><td><input class="tz" type="text" name="EMAIL" value='<?= $req[0]["RQ_EMAIL"] ?>' required ></td></tr>

		<tr>
			<td width='250' height='40'>Дополнительный E-mail</td>
			<td>
				<input class='tz' type='text' name='addEmail' value='<?= $req[0]['RQ_FIRST_NAME']?>'>
			</td>
		</tr>

		<tr><td width="250" height="40">Телефон <span class='redStars'>*<span></td><td><input class="tz" name="PHONE"  value='<?= $req[0]["RQ_PHONE"] ?>' required></td></tr>


		<tr><td width="250" height="40">Контактное лицо</td><td><input class="tz" type="text" name="CONTACT" value='<?= $req[0]["RQ_NAME"] ?>'></td></tr>
		<tr><td width="250" height="40">КПП</td><td><input class="tz" type="text" name="KPP" value='<?= $req[0]['RQ_KPP'] ?>'></td></tr>
		<tr><td width="250" height="40">Должность руководителя</td><td><input class="tz" type="text" name="Position2" value='<?= $req[0]['RQ_COMPANY_REG_DATE'] ?>'></td></tr>

		<tr><td width="250" height="40">ФИО руководителя</td><td><input class="tz" type="text" name="DirectorFIO" value='<?= $req[0]['RQ_DIRECTOR'] ?>'></td></tr>
		<tr><td width="250" height="40">Расчетный счет</td><td><input class="tz" type="text" name="RaschSchet" value='<?= $bankReq['RQ_ACC_NUM'] ?>'></td></tr>
		<tr><td width="250" height="40">Кор. счёт</td><td><input class="tz" type="text" name="KSchet" value='<?= $bankReq['RQ_COR_ACC_NUM'] ?>'></td></tr>

		<tr><td width="250" height="40">Лицевой счёт</td><td><input class="tz" type="text" name="l_schet" value='<?= $bankReq['COMMENTS'] ?>'></td></tr>

		<tr><td width="250" height="40">БИК</td><td><input class="tz" type="text" name="BIK" value='<?= $bankReq['RQ_BIK'] ?>'></td></tr>
		<tr><td width="250" height="40">Наименование банка</td><td><input class="tz" type="text" name="BankName" value='<?= $bankReq['RQ_BANK_NAME'] ?>'></td></tr>

        <tr>
            <td width="250" height="40">Действует на основании</td>
            <td><input class="tz" type="text" name="ACTS_BASIS" value='<?= !empty($address[1]["ADDRESS_2"]) ? $address[1]["ADDRESS_2"] : 'Устава' ?>'></td>
        </tr>

		<tr><td width="250" height="40">Тип заявки <span class='redStars'>*<span></td><td><select class="tz" name="REQ_TYPE" required>
		 <option value="SALE" <?= ($typeReqest == 'ИЦ') ? 'selected' : ''?>>ИЦ</option>
		 <option value="COMPLEX" <?= ($typeReqest == 'ОСК') ? 'selected' : ''?>>ОСК</option>
		 <option value="1" <?= ($typeReqest == 'ВЛК') ? 'selected' : ''?>>ВЛК</option>
		 <option value="2" <?= ($typeReqest == 'МСИ') ? 'selected' : ''?>>МСИ</option>
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
		<tr id="1"><td width="250" height="40">Ответственный <span class='redStars'>*<span></td><td><input class="tz" type="text" list="assigneds" name="ASSIGNED[]" value='<?= $arrResponsible[$deal["ASSIGNED_BY_ID"]] ?>' size="20" required><input name="id_assign[]" hidden value=''></td>
		<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='add2' value='+'></td>
		</tr>
		<?php if(isset($data_request['UF_CRM_1571643970']['VALUE']) && !empty($data_request['UF_CRM_1571643970']['VALUE'])) {
					foreach($data_request['UF_CRM_1571643970']['VALUE'] as $otherResponsible) {
		?>
		<tr id="1"><td width="250" height="40">Ответственный <span class='redStars'>*<span></td><td><input class="tz" type="text" list="assigneds" name="ASSIGNED[]" value='<?= $arrResponsible[$otherResponsible] ?>' size="20" required><input name="id_assign[]" hidden value=''></td>
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
		
        <input class="tz" type="hidden" name="id_tz" value="<?= $deal['ID'] ?>">
		
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

		if(!empty($_POST['MATERIAL'])) echo "<h3>Заявка успешно создана</h3><br><a href='/request_card.php?ID=". $idEDIT ."' target='_blank'>
				Перейти к созданной заявке
			</a><br /></br>";

		
		$deal = CCrmDeal::GetByID($_GET['ID']);
		$mater_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $ID);
		$mater_arr = $mater_arr['UF_CRM_1569151529509']['VALUE'];
		
		
        ?>
		<?php
        $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('>DATE_CREATE' => '01.01.2020 00:00:00', '!TYPE_ID' => '3'));
		$num_request=0;
		while($deal_c = $deals->Fetch())
		{
			//echo $deal_c['DATE_CREATE'];
			$num_request++;
		}
		
			$rs = $req->getList([
					"filter" => [
						"ENTITY_ID" => '267',
					]
				]);
				$requis = $rs->fetch();
				$inn = $requis['RQ_INN'];
				$ogrn = $requis['RQ_OGRN'];
				$addr = $requis['RQ_ACCOUNTANT'];
				$name = $requis['RQ_NAME']." ".$requis['RQ_LAST_NAME'];
				$phone = $requis['RQ_PHONE'];
				$email = $requis['RQ_EMAIL'];
       

		 
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

		<tr><td width="250" height="40">E-mail <span class='redStars'>*<span></td><td><input class="tz" type="text" name="EMAIL" value="" required ></td></tr>

		<tr>
			<td width='250' height='40'>Дополнительный E-mail</td>
			<td>
				<input class='tz' type='text' name='addEmail' value=''>
			</td>
		</tr>

		<tr><td width="250" height="40">Телефон <span class='redStars'>*<span></td><td><input class="tz" name="PHONE"  required></textarea></td></tr>


		<tr><td width="250" height="40">Контактное лицо</td><td><input class="tz" type="text" name="CONTACT" value=""></td></tr>
		<tr><td width="250" height="40">КПП</td><td><input class="tz" type="text" name="KPP" value=""></td></tr>
		<tr><td width="250" height="40">Должность руководителя</td><td><input class="tz" type="text" name="Position2" value=""></td></tr>
	
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

		<tr><td width="250" height="40">Тип заявки <span class='redStars'>*<span></td><td><select class="tz" name="REQ_TYPE" required>
		 <option value="SALE" <?=$row['CONTROL_LIST']=='0' ? 'selected' : ''?>>ИЦ</option>
		 <option value="COMPLEX" <?=$row['CONTROL_LIST']=='0' ? 'selected' : ''?>>ОСК</option>
		 <option value="1" <?=$row['CONTROL_LIST']=='0' ? 'selected' : ''?>>ВЛК</option>
		 <option value="2" <?=$row['CONTROL_LIST']=='1' ? 'selected' : ''?>>МСИ</option>
		 </select>
		</td></tr>
		<tr><td width="250" height="40">Номер заявки <span class='redStars'>*<span></td><td><input class="tz" type="number" name="ACT_NUM" value="<?=$num_request+5?>" required></td></tr>
		<tr id="1"><td width="250" height="40">Материал для исследования <span class='redStars'>*<span></td><td><input class="tz" list="materials" type="text" name="MATERIAL[]" value="" required ></td>
		<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='add' value='+'></td>
		</tr>
		<tr id="1"><td width="250" height="40">Ответственный <span class='redStars'>*<span></td><td><input class="tz" type="text" list="assigneds" name="ASSIGNED[]" value="" size="20" required><input name="id_assign[]" hidden value=""></td>
		<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='add2' value='+'></td>
		</tr>

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
                "<tr>
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
		var position2 = $('input[name=Position2]');addEmail
		var addEmail = $('input[name=addEmail]');
		var pattern = /^([a-z0-9_\.-])+@[a-z0-9-\.]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
		
		/*if(!pattern.test($("input[name=EMAIL]").val().toLowerCase()))
		{
			event.preventDefault();
			alert('Заполните полностью email');
			return true;
		}*/
		

		/*if(!pattern.test($("input[name=addEmail]").val().toLowerCase()))
		{
			event.preventDefault();
			alert('Заполните полностью дополнительный email');
			return true;
		}*/

		if ($(position2).val().length > 30) 
		{
			event.preventDefault();
			alert('Должность руководителя количество введенных символов превышает допустимое');
			return true;
		}
		
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
	
	$("input[name=client]").on("input", function() {
		$("input[name='id_comp']").val('');
	});
	
	
	$("input[name=client]").on("change", function() {

		arr= $(this).val().split(',');
		//console.log(arr)
		arr_inn=arr[2].slice(4);
		client_id= $('#clients option[data-id='+arr_inn+']').data('id'); 
		client_inn= $('#clients option[data-id='+arr_inn+']').data('inn'); 
		client_ogrn= $('#clients option[data-id='+arr_inn+']').data('ogrn');
		client_addr= $('#clients option[data-id='+arr_inn+']').data('addr');
		client_name= $('#clients option[data-id='+arr_inn+']').data('name'); 
		client_phone= $('#clients option[data-id='+arr_inn+']').data('phone');
		client_email= $('#clients option[data-id='+arr_inn+']').data('email');

		client_kpp = $('#clients option[data-id='+arr_inn+']').data('kpp'); 
		client_position = $('#clients option[data-id='+arr_inn+']').data('position'); 
		client_company = $('#clients option[data-id='+arr_inn+']').data('company');
		client_director = $('#clients option[data-id='+arr_inn+']').data('director');
		client_raschschet = $('#clients option[data-id='+arr_inn+']').data('raschschet'); 
		client_kschet = $('#clients option[data-id='+arr_inn+']').data('kschet');
		client_bik= $('#clients option[data-id='+arr_inn+']').data('bik');
		client_bank = $('#clients option[data-id='+arr_inn+']').data('bank');

		client_addemail = $('#clients option[data-id='+arr_inn+']').data('addemail');
		client_mail = $('#clients option[data-id='+arr_inn+']').data('mail');

        client_address= $('#clients option[data-id='+arr_inn+']').data('address');

        client_basis = $('#clients option[data-id='+arr_inn+']').data('basis');

		client_ogrnip= $('#clients option[data-id='+arr_inn+']').data('ogrnip');

		client_lschet= $('#clients option[data-id='+arr_inn+']').data('lschet');

		tbody = $(this).parent().parent().parent();
		id_input = tbody.find("input[name=id_comp]");
		inn_input = tbody.find("input[name=INN]");
		ogrn_input = tbody.find("input[name=OGRN]");
		addr_input = tbody.find("input[name=ADDR]");
		name_input = tbody.find("input[name=CONTACT]");
		phone_input = tbody.find("input[name=PHONE]");
		email_input = tbody.find("input[name=EMAIL]");

		kpp_input = tbody.find("input[name=KPP]");
		position_input= tbody.find("input[name=Position2]");
		company_input = tbody.find("input[name=CompanyFullName]");
		director_input = tbody.find("input[name=DirectorFIO]");
		raschschet_input = tbody.find("input[name=RaschSchet]");
		kschet_input = tbody.find("input[name=KSchet]");
		bik_input = tbody.find("input[name=BIK]");
		bank_input = tbody.find("input[name=BankName]");

		addemail_input = tbody.find("input[name=addEmail]");
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
		company_input[0].value = client_company;
		director_input[0].value = client_director;
		 raschschet_input[0].value = client_raschschet;
		kschet_input[0].value = client_kschet;
		bik_input[0].value = client_bik;
		bank_input[0].value = client_bank;

		addemail_input[0].value = client_addemail;
		mail_input[0].value = client_mail;

        address_input[0].value = client_address;

        basis_input[0].value = client_basis ? client_basis : 'Устава';

		ogrnip_input[0].value = client_ogrnip;

		lschet_input[0].value = client_lschet;

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
		
	
	})
 </script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>