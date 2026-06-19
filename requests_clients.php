<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.04.2019
 * Time: 16:52
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал заявок по клиентам");
(isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

?>

  
<?php

$req = new \Bitrix\Crm\EntityRequisite();
$requis=array();
$allPrice=0;
$rs = $req->getList(["order" => ["ENTITY_ID" => "asc"]]);

while($rss = $rs->Fetch())
	{
		$params = [
			'filter' => [
				'ENTITY_ID' => $rss['ID'],
				'ENTITY_TYPE_ID' => CCrmOwnerType::Requisite]
		];
		$bankReq = (new \Bitrix\Crm\EntityBankDetail)->getList($params)->fetch();
			
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
		
		//-------------'Дополнительные поля E-mail Адрес' начало ------------//
		$requis[$a]['RQ_FIRST_NAME'] = !empty($rss['RQ_FIRST_NAME']) ? $rss['RQ_FIRST_NAME'] : '';//E-mail дополнительный
		$requis[$a]['RQ_COMPANY_NAME'] = !empty($rss['RQ_COMPANY_NAME']) ? $rss['RQ_COMPANY_NAME'] : '';//Почтовый адрес
		//---------------'Дополнительные поля E-mail Адрес' окончание -----------//
	
	}

    
    

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
	<option data-id="0" value="Все" ></option>
	<?php
    foreach ($arCompanys as $a => $arComp)
	{
		$inn = $requis[$a]['RQ_INN'];
		$ogrn = $requis[$a]['RQ_OGRN'];
		$addr = $requis[$a]['RQ_ACCOUNTANT'];
		$name = $requis[$a]['RQ_NAME']." ".$requis[$a]['RQ_LAST_NAME'];
		$phone = $requis[$a]['RQ_PHONE'];
		$email = $requis[$a]['RQ_EMAIL'];
		//---------------------------------'Дополнительные поля' начало -------------------------//
		$kpp = $requis[$a]['RQ_KPP'];
		$position = $requis[$a]['RQ_COMPANY_REG_DATE'];
		$company = $requis[$a]['RQ_COMPANY_FULL_NAME'];
		$director = $requis[$a]['RQ_DIRECTOR'];
		$raschschet = $requis[$a]['RQ_ACC_NUM'];
		$kschet = $requis[$a]['RQ_COR_ACC_NUM'];
		$bik = $requis[$a]['RQ_BIK'];
		$bank = $requis[$a]['RQ_BANK_NAME'];
		
		//-------------'Дополнительные поля E-mail Адрес' начало ------------//
		$addemail = $requis[$a]['RQ_FIRST_NAME'];//E-mail дополнительный
		$mail = $requis[$a]['RQ_COMPANY_NAME'];//Почтовый адрес
		
		echo "<option data-id='" . $a . "' data-inn = '".$inn."' data-ogrn = '".$ogrn."' 
		data-addr = '".$addr."' data-name = '".$name."' data-phone = '".$phone."' 
		data-email = '".$email."'  data-kpp='".$kpp."' data-position='".$position."' 
		data-company='".$company."' data-director='".$director."' 
		data-raschschet='".$raschschet."' data-kschet='".$kschet."' data-bik='".$bik."' 
		data-bank='".$bank."' data-addemail='".$addemail."' data-mail='".$mail."' 
		value='" . $arComp. ', ИНН '. $inn. ', ID ' . $a ."' ".($_POST['id_comp']==$a ? 'selected' : '')."></option>";
		
		/*echo "<option data-id='" . $a . "' data-inn = '".$inn."' data-ogrn = '".$ogrn."' 
		data-addr = '".$addr."' data-name = '".$name."' data-phone = '".$phone."' 
		data-email = '".$email."'  data-kpp='".$kpp."' data-position='".$position."' 
		data-company='".$company."' data-director='".$director."' 
		data-raschschet='".$raschschet."' data-kschet='".$kschet."' data-bik='".$bik."' 
		data-bank='".$bank."' value='" . $arComp. ', ИНН '. $inn. ', ID ' . $a ."'></option>";*/
		//---------------'Дополнительные поля E-mail Адрес' окончание -----------//
		
	//---------------------------------'Дополнительные поля' окончание --------------------//
	}
	
    $idCompany='';
	
	if (!empty($_POST['id_comp'])) 
	{
	
		$idCompany = $_POST['id_comp'];
		$deals = CCrmDeal::GetList(
		$arOrder = Array('DATE_CREATE' => 'DESC'), 
		$arFilter = Array('COMPANY_ID' => $idCompany)
		);
  
	}
	else
	{
		$deals = CCrmDeal::GetList($arOrder = Array('DATE_CREATE' => 'DESC'), 
		$arFilter = Array('COMPANY_ID' => 1));
	}
	
    ?>
</datalist>
		
	<form method="POST" id="company">	
	<?php 
	if ( \Bitrix\Main\Loader::IncludeModule('crm') )
	{
		$arOrder  = array('ID' => 'ASC');
		$arFilter = array('ID' => $_POST['id_comp']);
		$arSelect = array();
		$resCompany = CCrmCompany::GetList( $arOrder, $arFilter, $arSelect )->Fetch();
		// while( $arCompany = $res->fetch() )
		// {
			// $arCompanys[ $arCompany['ID'] ] = $arCompany['TITLE'];
		// }
	}
	?>
	<table style="float: right;"><tr>
	<td width="500px">
	<input class="tz" style="width:100%" multiple list="clients" type="text" name="client"  value='<?= $resCompany['TITLE'] ? $resCompany['TITLE'] : 'Выберите' ?>'><input name="id_comp" hidden value="<?= $_POST['id_comp'] ?>">
	</td>
	<td>
	<input type="submit" style= "margin:0px" class="add2" value="Найти" ></input>
	</td>
	</tr></table>
	<br>
	</form>
	   
	<br /><br /><br />
    <table id="results_isp">
	<thead>
		<tr>
		<th></th>
		<th>Клиент</th>
        <th>Заявка</th>
		<th>Дата</th>
		<th>Сумма</th>
		<th>ТЗ</th>
		<th>КП</th>
		<th>КП отпр.</th>
		<th>Договор</th>
		<th>Дог. отпр.</th>
		<th>ТЗ (Пр. к дог.)</th>
		<th>ТЗ отпр.</th>
		<th>Счет</th>
		<th>Счет отпр.</th>
		<th>Акт ПП</th>
	
		<th>Протокол</th>
		<th>Исп. завершены</th>
		<th>Акт ВР</th>
		<th>Акт ВР отпр.</th>
		<!----------------------------------- 'Копировать' правил начало ---------------------------->
		
		<!----------------------------------- 'Копировать' правил окончание ----------------------->
		</tr>
       <!-- <tr>
			<th style="margin:0px"></th>
			<th><input type="text" name="client_filter" id="client_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="requests_filter" id="requests_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="dates_filter" id="dates_filter" style="margin:0px" value=""></th>
			
			<th><input type="text" name="deadline_filter" id="deadline_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="object_filter" id="object_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="responsible_filter" id="responsible_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="act_filter" id="act_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="num_tz_filter" id="num_tz_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="contract_filter" id="contract_filter" style="margin:0px" value=""></th>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><th><input type="text" name="cost_filter" id="cost_filter" style="margin:0px" value=""></th><?}?>
			<th style="margin:0px"></th>
			<th><input type="text" name="protocol_filter" id="protocol_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="change_filter" id="change_filter" style="margin:0px" value=""></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>

		</tr>-->
	</thead>
	<tbody>
		<div id='buttonRightTable' class='center'></div>
		<div id='buttonLeftTable'class='center'></div>
	
        <?php
			
			while ($deal = $deals->Fetch()) { 
			
			
			
				switch ($_GET['laba']) {
						case '1':
							$id_dep = 54;
							break;
						case '2':
							$id_dep = 57;
							break;
						case '3':
							$id_dep = 56;
							break;
						case '4':
							$id_dep = 55;
							break;
						case '5':
							$id_dep = 58;
							break;
						case '6':
							$id_dep = 53;
							break;
						
					}
				
				$id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON');
				$id_act_num =1;
							
				switch ($_GET['year']) {
						case '2019':
							$id_year1=date("Y-m-d", strtotime("2018-12-31"));
							$id_year2=date("Y-m-d", strtotime("2020-01-01"));
							break;
						case '2020':
							$id_year1=date("Y-m-d", strtotime("2019-12-31"));
							$id_year2=date("Y-m-d", strtotime("2021-01-01"));
							break;
						case '12020':
							$id_year1=date("Y-m-d", strtotime("2019-12-31"));
							$id_year2=date("Y-m-d", strtotime("2020-02-01"));
							break;
						case '22020':
							$id_year1=date("Y-m-d", strtotime("2020-01-31"));
							$id_year2=date("Y-m-d", strtotime("2020-03-01"));
							break;
						case '32020':
							$id_year1=date("Y-m-d", strtotime("2020-02-29"));
							$id_year2=date("Y-m-d", strtotime("2020-04-01"));
							break;
						case '42020':
							$id_year1=date("Y-m-d", strtotime("2020-03-31"));
							$id_year2=date("Y-m-d", strtotime("2020-05-01"));
							break;
						case 'all':
							$id_year1=date("Y-m-d", strtotime("2019-01-01"));
							$id_year2=date("Y-m-d", strtotime("2029-12-31"));
							break;
						
					}
			if(empty($_GET['year']))
			{
				$id_year1=date("Y-m-d", strtotime("2020-01-01"));
				$id_year2=date("Y-m-d", strtotime("2021-01-01"));
			}
				
				$userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
				$user_dep = $userr['UF_DEPARTMENT'][0];
				$dop_assign=0;
				$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
							if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
								
								foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
								{
									$assign_user = CUser::GetByID((int)$assign)->Fetch();
									if($assign_user['UF_DEPARTMENT'][0]==$id_dep)
									{$dop_assign=1;}
								}
							}
				
				//echo $user_dep;
				if ((!empty($_GET['laba']) && ($user_dep == $id_dep||$dop_assign))||empty($_GET['laba'])||($_GET['laba']=='all'))
				{
				
					$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $deal['ID'])->Fetch();
					$res_kp=''; $res_dog=''; $res_acc=''; $res_akt_vr=''; $res_tz_doc=''; $res_akt='';
					
					if($res_tz) 
						{
							$res_kp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							$res_tz_doc = $DB->Query("SELECT * FROM `TZ_DOC` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							$res_akt = $DB->Query("SELECT * FROM `ACT` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							$res_akt_vr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							
							
						}
					
					$date_protocol = $res_tz['DATE_P_CORR'] ? $res_tz['DATE_P_CORR'] :$res_tz['DATE_P'];
					
					$date_p = date("Y-m-d", strtotime($deal['DATE_CREATE']));	
				
					if($date_p>$id_year1 && $date_p<$id_year2) {
						
					if($id_stage&&in_array($deal['STAGE_ID'], $id_stage)&&(boolean)$res_tz['ACT_NUM']==(boolean)$id_act_num	) {
					
					if($_GET['stage']=='6'&&$res_tz['PRICE']&&!$res_tz['OPLATA']||$_GET['stage']=='7'&&$res_tz['PRICE']&&$res_tz['OPLATA']&&$res_tz['OPLATA']<$res_tz['PRICE']||$_GET['stage']=='8'&&$res_tz['PRICE']&&$res_tz['OPLATA']&&$res_tz['OPLATA']>$res_tz['PRICE']||$_GET['stage']=='9'&&$res_tz['PRICE']&&$res_tz['OPLATA']&&$res_tz['OPLATA']==$res_tz['PRICE']||!$_GET['stage']||$_GET['stage']=='4'||$_GET['stage']=='5'||$_GET['stage']=='3'||$_GET['stage']=='2'||$_GET['stage']=='1'||$_GET['stage']=='10'||$_GET['stage']=='11'||$_GET['stage']=='all') {
					$y_date_act = date("Y", strtotime($res_tz['DATE_ACT']));
					if ($y_date_act != '2020') $y_date_act = "/".substr($y_date_act, 2);
							else $y_date_act = "/".$y_date_act;
					
					$y_date_p = date("Y", strtotime($date_protocol));
					if ($y_date_p != '2020') $y_date_p = "/".substr($y_date_p, 2);
							else $y_date_p = "/".$y_date_p;
					
					if($res_tz['ID']) {
						if ($res_tz['NUM_P'] === 'номер не присвоен') {
							$num_tz = 'номер не присвоен';
						} else {
							$num_tz = $res_tz['NUM_P'] ? $res_tz['NUM_P'].$y_date_p : '';
						}
					}
					else $num_tz='';
					$color=''; $c=0; $title='';
					$history_ass='';
					$history_date='';
					
					if ($res_tz['ID']) 
					{	
						$history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . $deal['ID']. " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
						$history_ass=$history['ASSIGNED'];
						$history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
						$history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
						if($history_date<$history_date_null) $history_date='';
					}
					//var_dump($history);
					switch ($deal['STAGE_ID']) {
						case 'NEW':
							$color='#00B0F0';
							$c=0;
							$title='Испытания еще не проводились. Пробы не получены.';
							break;
						case 'PREPARATION':
							$color='#00B0F0';
							$c=0;
							$title='Испытания еще не проводились. Пробы не получены.';
							break;
						case 'PREPAYMENT_INVOICE':
							$color='#00B0F0';
							$c=0;
							$title='Испытания еще не проводились. Пробы не получены.';
							break;
						case 'EXECUTING':
							$color='#00B0F0';
							$c=0;
							$title='Испытания еще не проводились. Пробы не получены.';
							break;
						case 'FINAL_INVOICE':
							$color='#FFFF00';
							$c=1;
							$title='Пробы получены. Проводятся испытания';
							break;
						case '1':
							$color='#FFFF00';
							$c=2;
							$title='Пробы получены. Проводятся испытания.';
							break;
						case '2':
							$color='#833ea3';
							$c=3;
							$title='Испытания в лаборатории завершены. Оплата получена или не требуется.';
							break;
						case '4':
							$color='#19ff19';
							$c=4;
							$title='Акты ВР отправлены заказчику.';
							break;
						// case '3':
							// $color='green';
							// $c=5;
							// $title='Протокол выдан';
							// break;
						case 'WON':
							$color='#70AD47';
							$c=6;
							$title='Акты ВР получены. Заявка успешно завершена.';
							break;
						case 'LOSE':
							$color='red';
							$c=7;
							$title='Испытания не проведены. Заявка прекращена.';
							break;
						case '7':
							$color='red';
							$c=9;
							$title='Заявка прекращена. Не проводим подобные испытания.';
							break;
						case '6':
							$color='red';
							$c=9;
							$title='Заявка прекращена. Заказчик не вышел на связь.';
							break;
						case '5':
							$color='red';
							$c=9;
							$title='Заявка прекращена. Заказчика не устроила цена.';
							break;
						case '8':
							$color='red';
							$c=9;
							$title='Заявка прекращена. Создана другая заявка.';
							break;
						case '9':
							$color='red';
							$c=9;
							$title='Заявка прекращена. Заказчик выбрал лабораторию в своем городе.';
							break;
						case '10':
							$color='red';
							$c=9;
							$title='Заявка прекращена. Заказчик решил не проводить испытания.';
							break;
						case '11':
							$color='red';
							$c=9;
							$title='Заявка прекращена. Отказались сами в связи с высокой загруженностью.';
							break;
						case '12':
							$color='red';
							$c=9;
							$title='Заявка прекращена. Судебная экспертиза.';
							break;
						case '13':
							$color='red';
							$c=9;
							$title='Заявка прекращена. Участие в тендере.';
							break;
					}
					if ($res_tz['ACT_NUM']&&($deal['STAGE_ID']=='NEW'||$deal['STAGE_ID']=='PREPARATION'||$deal['STAGE_ID']=='PREPAYMENT_INVOICE'||$deal['STAGE_ID']=='EXECUTING')) {
							$color='yellow';
							$c=1;
							$title='Протокол не выдан. Испытания еще не проводились. Пробы получены';
					}
					if ($res_tz['PRICE']&&$res_tz['RESULTS']&&(!$res_tz['OPLATA']||$res_tz['OPLATA']<$res_tz['PRICE'])&&$deal['STAGE_ID']=='2') {
							$color='#9b2d30';
							$c=5;
							$title='Испытания в лаборатории завершены. Оплата не поступила.';
					}
					// if (CModule::IncludeModule('crm')) {
						// $deal = new CCrmDeal;
						// $arDealUpdate = array('UF_CRM_1570518000868' => $num_tz);
						// $resUpd = $deal->Update((int)$_GET['ID'],
							// $arDealUpdate,
							// true,
							// true,
							// array('DISABLE_USER_FIELD_CHECK' => true)
						// );
					// }
					//var_dump($res_tz);
					//var_dump($deal);
					//echo $deal['SOURCE_DESCRIPTION'];
					$companyName = $USER_FIELD_MANAGER->GetUserFields('CRM_COMPANY', $deal['COMPANY_ID']);
					//$companyDiscount = $companyName['UF_CRM_1582914274353']['VALUE'];
					$res_contract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . $deal['ID'])->Fetch();	
					//$num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
					if ($res_contract['ID']) 
					{
						$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . $res_contract['ID_CONTRACT'])->Fetch();
						$num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
					}
					if($res_tz['ID']) 
					{
						$num_act = ($res_tz['ACT_NUM'] ? '+' : '-');//$deal['ID'].$y_date_act);
						//$num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
					}
					else 
					{
						$num_act='-'; $num_dog='-';
					}
					$deadlineDate = ($deal['UF_CRM_1577124965971'] ? $deal['UF_CRM_1577124965971'] : ruDate($res_tz['DEADLINE']));
					if ($deadlineDate=="01.01.1970") $deadlineDate=date('d.m.Y', strtotime($deal['DATE_CREATE'])+ 86400*3);
					$mater_arr=array();
					$mater_arr_id=array();
					
					$res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=".$deal['ID']);
						while($mater=$res_mat->Fetch())
						{
							$mater_arr[]=$mater['NAME_MATERIAL'];
							$mater_arr_id[]=$mater['ID_MATERIAL'];
						}
					
					$dealMaterials = ($deal['UF_CRM_1576411922643'] ? $deal['UF_CRM_1576411922643'] : implode(', ', $deal['UF_CRM_1569151529509']));
					$dealMaterials = ($dealMaterials  ? $dealMaterials : implode(', ', $mater_arr));
					
					$color_date='';
					
					
					if (date('Y-m-d') > date("Y-m-d", strtotime($deadlineDate))&&$deal['STAGE_ID']!='2'&&$deal['STAGE_ID']!='4'&&$deal['STAGE_ID']!='WON'&&$deal['STAGE_ID']!='LOSE'&&$deal['STAGE_ID']!='5'&&$deal['STAGE_ID']!='6'&&$deal['STAGE_ID']!='7'&&$deal['STAGE_ID']!='8'&&$deal['STAGE_ID']!='9') $color_date='red';
					?>
					<tr>
						<td style="background-color: <?=$color?>; color: <?=$color?>" title="<?=$title?>"><?= $c ?></td>
						
						<td><?= $deal['COMPANY_TITLE'] ?></td>
						
						<td><a href='/request_card.php?ID=<?= $deal['ID'] ?>&ACT_NUM=<?=$res_tz['ACT_NUM']?>' target="_blank"><?= $deal['TITLE'] ?></a></td>
						
						<td><?= date("d.m.Y", strtotime($deal['DATE_CREATE'])) ?> </td>
						
						<td style="text-align:right"><?php  if($res_tz['PRICE']) 
									   echo $res_tz['PRICE'];//number_format($res_tz['PRICE'], 2, ',', ''); 
								   else
									   echo '';
						$allPrice = $allPrice+$res_tz['PRICE'];  	
						?></td>
						
						<td style="background-color: <?= $res_tz['ID'] ? '#70AD47' : ''?>"><?= $res_tz['ID'] ? '+' : '-' ?></a></td>
						
						<td style="background-color: <?= $res_kp['ID'] ? '#70AD47' : ''?>"><?=$res_kp['ID'] ? '+' : '-'?></td>
						
						<td style="background-color: <?= $res_kp['SEND_DATE'] ? '#70AD47' : ''?>"><?=$res_kp['SEND_DATE'] ? date('d.m.Y', strtotime($res_kp['SEND_DATE'])) : '-'?></td>
						
						<td style="background-color: <?= $res_tz['DOGOVOR_NUM'] ? '#70AD47' : ''?>"><?= ($res_tz['DOGOVOR_NUM'] ? '+' : '-') ?></td>
						
						<td style="background-color: <?= $res_dog['SEND_DATE'] ? '#70AD47' : ''?>"><?=$res_dog['SEND_DATE'] ? date('d.m.Y', strtotime($res_dog['SEND_DATE'])) : '-'?></td>
						
						<td style="background-color: <?= $res_tz_doc['ID'] ? '#70AD47' : ''?>"><?=$res_tz_doc['ID'] ? '+' : '-'?></td>
						
						<td style="background-color: <?= $res_tz_doc['SEND_DATE'] ? '#70AD47' : ''?>"><?=$res_tz_doc['SEND_DATE'] ? date('d.m.Y', strtotime($res_tz_doc['SEND_DATE'])) : '-'?></td>
						
						<td style="background-color: <?= $res_acc['ID'] ? '#70AD47' : ''?>"><?=$res_acc['ID'] ? '+' : '-'?></td>
						
						<td style="background-color: <?= $res_acc['SEND_DATE'] ? '#70AD47' : ''?>"><?=$res_acc['SEND_DATE'] ? date('d.m.Y', strtotime($res_acc['SEND_DATE'])) : '-'?></td>
												
						<td style="background-color: <?= $res_tz['ACT_NUM'] ? '#70AD47' : ''?>"><?= $num_act ?></td>
						
						<td style="background-color: <?= (($res_tz['ACTUAL_VER']&&$res_tz['NUM_P']!='номер не присвоен')||($res_tz['NUM_P']!='номер не присвоен'&& $res_tz['PDF'])) ? '#70AD47' : ''?>">
						<?=	(($res_tz['ACTUAL_VER']&&$res_tz['NUM_P']!='номер не присвоен')||($res_tz['NUM_P']!='номер не присвоен'&& $res_tz['PDF'])) ? '+' : '-'  ?>
						</td>
						
						<td style="background-color: <?= $res_tz['dateEnd'] ? '#70AD47' : ''?>"><?= $res_tz['dateEnd'] ? ruDate($res_tz['dateEnd']) : '-' ?></td>
						
						<td style="background-color: <?= $res_akt_vr['ID'] ? '#70AD47' : ''?>"><?=$res_akt_vr['ID'] ? '+' : '-'?></td>
						
						<td style="background-color: <?= $res_akt_vr['SEND_DATE'] ? '#70AD47' : ''?>"><?=$res_akt_vr['SEND_DATE'] ? date('d.m.Y', strtotime($res_akt_vr['SEND_DATE'])) : '-'?></td>
						<!----------------------------------- 'Копировать' правил начало ---------------------------->
						<!--<td><//?php if($res_tz['ID']) {?><a href="/tz_copy.php?ID=<//?=$res_tz['ID']?>&ID_DEAL=<//?=$deal['ID']?>" target="_blank">Копировать</a><//?php } ?></td>-->
						<!----------------------------------- 'Копировать' правил окончание ----------------------->
					</tr>
				<?php  
					}
					}
				}
				}
				//substr($deal['UF_CRM_1570765979554'],0,-1)
				} ?>
			</tbody></table>
<br>
Всего на сумму: <b> <?= number_format($allPrice, 2, ',', '')?> руб.</b>

    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script>
        $(".add").click(function () {
            td = $(this).parent().parent();
            keys = $(this).data("keys").split("-");
            td.append('<div style="display:flex"><input class="res" "margin-left:5px" type="number" step="0.001" name="results[' + keys[0] + '][' + keys[1] + '][' + keys[2] + '][]"  ><input style="margin-left:5px" type="button" value="- " onclick="$(this).parent().remove();"></div>');
        })
    </script>
	<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"/>-->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>
	
    
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
	<script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/filtering/type-based/html.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.21/dataRender/intl.js"></script>
	
	<script>
        $(document).ready(function () {
            table= $('#results_isp').DataTable({
                "language":{
                    "processing": "Подождите...",
                    "search": "Поиск:",
                    "lengthMenu": "Показать _MENU_ записей",
                    "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                    "infoEmpty": "Записи с 0 до 0 из 0 записей",
                    "infoFiltered": "(отфильтровано из _MAX_ записей)",
                    "infoPostFix": "",
                    "loadingRecords": "Загрузка записей...",
                    "zeroRecords": "Записи отсутствуют.",
                    "emptyTable": "В таблице отсутствуют данные",
                    "paginate": {
                        "first": "Первая",
                        "previous": "Предыдущая",
                        "next": "Следующая",
                        "last": "Последняя"
                    },
					"buttons": {
						"copy": "",
						'colvis': "",
						'print': "",
						"excel": ""
					},
                    "aria": {
                        "sortAscending": ": активировать для сортировки столбца по возрастанию",
                        "sortDescending": ": активировать для сортировки столбца по убыванию"
                    }
                },
				
				"iDisplayLength": 50,
				"columnDefs": [{ "type": "natural-nohtml", "targets": 2 }, { "sType": "ruDate", "targets": 3 },{ "sType": "ruDate", "targets": 7 },
				{ "sType": "ruDate", "targets": 9 }, { "sType": "ruDate", "targets": 11 }, { "sType": "ruDate", "targets": 13 }, { "sType": "ruDate", "targets": 16 },
				{
				  "targets": 1,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 4,
				  "render": $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 })
				}
				// {
				  // "targets": 9,
				  // "render": $.fn.dataTable.render.ellipsis( 30, true )
				// }
				],
				"order": [[ 1, "asc" ]],
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });

			// $('#requests_filter').on( 'keyup', function () {
				// table
					// .columns( 2 )
					// .search( this.value )
					// .draw();
			// } );
			
			// $('#dates_filter').on( 'keyup', function () {
				// table
					// .columns( 3 )
					// .search( this.value )
					// .draw();
			// } );
			
			// $('#client_filter').on( 'keyup', function () {
				// table
					// .columns( 1 )
					// .search( this.value )
					// .draw();
			// } );
			
			// $('#deadline_filter').on( 'keyup', function () {
				// table
					// .columns( 4 )
					// .search( this.value )
					// .draw();
			// } );
			
			// $('#object_filter').on( 'keyup', function () {
				// table
					// .columns( 5 )
					// .search( this.value )
					// .draw();
			// } );
			
			// $('#responsible_filter').on( 'keyup', function () {
				// table
					// .columns( 6 )
					// .search( this.value )
					// .draw();
			// } );
			
			// $('#act_filter').on( 'keyup', function () {
				// table
					// .columns( 7 )
					// .search( this.value )
					// .draw();
			// } );
			
			// $('#num_tz_filter').on( 'keyup', function () {
				// table
					// .columns( 8 )
					// .search( this.value )
					// .draw();
			// } );
			
			// $('#contract_filter').on( 'keyup', function () {
				// table
					// .columns( 9 )
					// .search( this.value )
					// .draw();
			// } );
			
			// $('#cost_filter').on( 'keyup', function () {
				// table
					// .columns( 10 )
					// .search( this.value )
					// .draw();
			// } );
			
			// $('#protocol_filter').on( 'keyup', function () {
				// table
					// .columns( 12 )
					// .search( this.value )
					// .draw();
			// } );
			
			// $('#change_filter').on( 'keyup', function () {
				// table
					// .columns( 13 )
					// .search( this.value )
					// .draw();
			// } );
        });
		
		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
		"ruDate-asc": function ( a, b ) {
			if(a=='-') a='01.01.2000';
			if(b=='-') b='01.01.2000';
			var ruDatea = $.trim(a).split('.');
			var ruDateb = $.trim(b).split('.');

			if(ruDatea[2]*1 < ruDateb[2]*1)
				return 1;
			if(ruDatea[2]*1 > ruDateb[2]*1)
				return -1;
			if(ruDatea[2]*1 == ruDateb[2]*1)
				{
				if(ruDatea[1]*1 < ruDateb[1]*1)
					return 1;
				if(ruDatea[1]*1 > ruDateb[1]*1)
					return -1;
				if(ruDatea[1]*1 == ruDateb[1]*1)
					{
					if(ruDatea[0]*1 < ruDateb[0]*1)
						return 1;
					if(ruDatea[0]*1 > ruDateb[0]*1)
						return -1;
					}
					else
						return 0;
				}				
		},
 
		"ruDate-desc": function ( a, b ) {
			if(a=='-') a='01.01.2000';
			if(b=='-') b='01.01.2000';
			
			var ruDatea = $.trim(a).split('.');
			var ruDateb = $.trim(b).split('.');
			
			if(ruDatea[2]*1 < ruDateb[2]*1)
				return -1;
			if(ruDatea[2]*1 > ruDateb[2]*1)
				return 1;
			if(ruDatea[2]*1 == ruDateb[2]*1)
				{
				if(ruDatea[1]*1 < ruDateb[1]*1)
					return -1;
				if(ruDatea[1]*1 > ruDateb[1]*1)
					return 1;
				if(ruDatea[1]*1 == ruDateb[1]*1)
					{
					if(ruDatea[0]*1 < ruDateb[0]*1)
						return -1;
					if(ruDatea[0]*1 > ruDateb[0]*1)
						return 1;
					}
					else
						return 0;
				}
		}
	});
		
    </script>
	<script>
	//Прокрутка таблицы с помощью кнопок
	$(document).ready(function () {
		
		var container = $('.workarea-content-paddings'),
			scroll = $('.dataTable.no-footer').width(),
			topTable = $('.workarea-content-paddings').offset().top,
			topTableBody = $('.dataTable.no-footer > tbody').offset().top,
			sumHeaderHeight = topTableBody - topTable;
		
			$('#buttonRightTable').css('top', sumHeaderHeight + "px");
			$('#buttonLeftTable').css('top', sumHeaderHeight + "px");
			
		$('#buttonRightTable').hover(function() {
			container.animate({
				'scrollLeft': scroll
			},{duration: 4000, queue: false});
		}, function(){
			container.stop();
		});
		$('#buttonLeftTable').hover(function() {
			container.animate({
				'scrollLeft': -scroll
			},{duration: 4000, queue: false});
		}, function(){
			container.stop();
		});

		$(document).scroll(function() {
			var positionScroll = $(window).scrollTop();
			if (positionScroll > 250 && positionScroll < 2550) {
				$('#buttonRightTable').css('transform','translateY(' + (positionScroll-250) +  'px' + ')');
				$('#buttonLeftTable').css('transform','translateY(' + (positionScroll-250) +  'px' + ')');
			}
		});

	});
	
	</script>
	<script>
	$("input[name=client]").on("input", function() {
		$("input[name='id_comp']").val('');
	});
	
	
	$("input[name=client]").on("change", function() {

		arr= $(this).val().split(',');
		
		arr_inn=arr[2].slice(4);
		client_id= $('#clients option[data-id='+arr_inn+']').data('id'); 
		
		tbody = $(this).parent().parent().parent();
		id_input = tbody.find("input[name=id_comp]");
		console.log(id_input);
		id_input[0].value = client_id;
		console.log(id_input[0].value);
		
	});
	</script>
	
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>