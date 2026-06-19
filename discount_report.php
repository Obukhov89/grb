<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.04.2019
 * Time: 16:52
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Отчет по изменениям цен");
(isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

?>

  
<?php

   
    $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), Array('!TYPE_ID' => '3'));
	$location_year = ""; $location_laba = ""; $location_stage="";

	if(!empty($_GET['laba'])&&empty($_GET['year'])&&empty($_GET['stage'])) {
		$location_year = "window.location.assign('/discount_report.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/discount_report.php?laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/discount_report.php?laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&!empty($_GET['year'])&&empty($_GET['stage'])) {
		$location_year = "window.location.assign('/discount_report.php?year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/discount_report.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/discount_report.php?year=".$_GET['year']."&stage='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&empty($_GET['year'])&&!empty($_GET['stage'])) {
		$location_year = "window.location.assign('/discount_report.php?stage=".$_GET['stage']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/discount_report.php?stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/discount_report.php?stage='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&&empty($_GET['stage'])) {
		$location_year = "window.location.assign('/discount_report.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/discount_report.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/discount_report.php?laba=".$_GET['laba']."&year=".$_GET['year']."&stage='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&empty($_GET['year'])&&!empty($_GET['stage'])) {
		$location_year = "window.location.assign('/discount_report.php?laba=".$_GET['laba']."&stage=".$_GET['stage']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/discount_report.php?stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/discount_report.php?laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&!empty($_GET['year'])&&!empty($_GET['stage'])) {
		$location_year = "window.location.assign('/discount_report.php?stage=".$_GET['stage']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/discount_report.php?year=".$_GET['year']."&stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/discount_report.php?year=".$_GET['year']."&stage='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&&!empty($_GET['stage'])) {
		$location_year = "window.location.assign('/discount_report.php?stage=".$_GET['stage']."&laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/discount_report.php?year=".$_GET['year']."&stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/discount_report.php?year=".$_GET['year']."&laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
	}
	else {
		$location_year = "window.location.assign('/discount_report.php?year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/discount_report.php?laba='+this.options[this.selectedIndex].value)";
		$location_stage = "window.location.assign('/discount_report.php?stage='+this.options[this.selectedIndex].value)";
	}
	//var_dump($_POST);
    ?>
	
		
		
	<table style="float: right;"><tr>
	<td>
	 <select class="oborud" name="stage" id="stage" onchange="<?= $location_stage?>"  style="float: left; width: 100%">
		
        <option value='all' style='font-weight:bold' <?=$_GET['stage'] == 'all' ? 'selected' : ''?> selected>Все стадии</option>
		<option value="1" style='font-weight:bold' <?=$_GET['stage'] == '1' ? 'selected' : ''?>>Пробы не поступили</option>
		<option value='2' style='font-weight:bold' <?=$_GET['stage'] == '2' ? 'selected' : ''?>>Пробы поступили</option>
		<option value='3' style='font-weight:bold' <?=$_GET['stage'] == '3' ? 'selected' : ''?>>Проводятся испытания</option>
		<option value='4' style='font-weight:bold' <?=$_GET['stage'] == '4' ? 'selected' : ''?>>Испытания завершены</option>
		<option value='5' style='font-weight:bold' <?=$_GET['stage'] == '5' ? 'selected' : ''?>>Заявка неуспешна</option>
		<option value='6' style='font-weight:bold' <?=$_GET['stage'] == '6' ? 'selected' : ''?>>Заявка не оплачена</option>
		<option value='7' style='font-weight:bold' <?=$_GET['stage'] == '7' ? 'selected' : ''?>>Заявка оплачена не полностью</option>
		<option value='8' style='font-weight:bold' <?=$_GET['stage'] == '8' ? 'selected' : ''?>>По заявке переплата</option>
		<option value='9' style='font-weight:bold' <?=$_GET['stage'] == '9' ? 'selected' : ''?>>Заявка оплачена полностью</option>
		<option value='10' style='font-weight:bold' <?=$_GET['stage'] == '10' ? 'selected' : ''?>>Все кроме новых и неуспешных</option>
		<option value='11' style='font-weight:bold' <?=$_GET['stage'] == '11' ? 'selected' : ''?>>Успешно завершенные</option>
		       
    </select>
	</td>
	<td>
	 <select class="oborud" name="year_p" id="year" onchange="<?= $location_year?>" style="float: left; width: 100%">
	
		<option value="2020" style='font-weight:bold' <?=$_GET['year'] == '2020' ? 'selected' : ''?> selected>2020</option>
		<option value="12020" style='font-weight:bold' <?=$_GET['year'] == '12020' ? 'selected' : ''?>>Январь 2020</option>
		<option value="22020" style='font-weight:bold' <?=$_GET['year'] == '22020' ? 'selected' : ''?>>Февраль 2020</option>
		<option value="32020" style='font-weight:bold' <?=$_GET['year'] == '32020' ? 'selected' : ''?>>Март 2020</option>
		<option value="42020" style='font-weight:bold' <?=$_GET['year'] == '42020' ? 'selected' : ''?>>Апрель 2020</option>
		<option value="52020" style='font-weight:bold' <?=$_GET['year'] == '52020' ? 'selected' : ''?>>Май 2020</option>
		<option value="62020" style='font-weight:bold' <?=$_GET['year'] == '62020' ? 'selected' : ''?>>Июнь 2020</option>
		<option value="72020" style='font-weight:bold' <?=$_GET['year'] == '72020' ? 'selected' : ''?>>С 1 июня</option>
        <option value='2019' style='font-weight:bold' <?=$_GET['year'] == '2019' ? 'selected' : ''?>>2019</option>
		<option value='all' style='font-weight:bold' <?=$_GET['year'] == 'all' ? 'selected' : ''?> >Все года</option>
		
			
       
    </select>
	</td><td>
	<select class="oborud" name="laba" id="laba" onchange="<?= $location_laba?>" style="float: left; width: 100%">
        <option value="all" selected>Все лаборатории</option>
		
        <?php
        $select = $DB->Query("SELECT * FROM `ba_laba`");
        while ($option = $select->Fetch()) {
			
            echo "<option ";
            if ($_GET['laba'] == $option['ID']) {
                echo "selected";
            }
            echo " value='" . $option['ID'] . "' style='font-weight:bold'>" . $option['NAME'] . "</option>";
			
        }
        ?>
    </select></td>
	<td><input type="button" class="add2" value="Х" onclick="window.location.assign('/discount_report.php')"></input></td>
	</tr></table>
	   
	<br /><br /><br />
    <table id="results_isp">
	<thead>
		<tr>
		
		<th>Заявка</th>
		<th>Клиент</th>
		<th>Ответственный</th>
		<th>Объект<br> испытаний</th>
		<th >Договор</th>
		<?php if(in_array('8', $USER->GetUserGroupArray())) {?><th>Стоимость <br>по прайсу</th><?}?>
		<th>Стоимость <br>по договору</th>
		<th>Стоимость<br> по ТЗ</th>
		<th>Размер <br>скидки <br>отн. прайса, %</th>
		<th>Размер <br>скидки <br>отн. договора, %</th>
		<th>Причина</th>
		<th>Счет</th>
		<th>Комментарий</th>
		<th>Изменения<br> цен</th>
		<!----------------------------------- 'Копировать' правил начало ---------------------------->
		<!--<th></th>-->
		<!----------------------------------- 'Копировать' правил окончание ----------------------->
		</tr>
        <tr>
		
			<th><input type="text" name="requests_filter" id="requests_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="client_filter" id="client_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="assign_filter" id="assign_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="object_filter" id="object_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="contract_filter" id="contract_filter" style="margin:0px" value=""></th>
		<th><input type="text" name="cost_filter" id="cost_filter" style="margin:0px" value="" hidden></th>
			<th></th>
			<th><input type="text" name="change_filter" id="change_filter" style="margin:0px" value="" hidden></th>
			<th style="margin:0px"></th>
			<th style="margin:0px"></th>
			<th style="margin:0px"></th>
			<th style="margin:0px"></th>
			<th style="margin:0px"></th>
			<th style="margin:0px"></th>
		
		</tr>
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
							$id_dep = 58;
							break;
						
					}
				
				switch ($_GET['stage']) {
						case '1':
							$id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING');
							$id_act_num =0;
							break;
						case '2':
							$id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE');
							$id_act_num =1;
							break;
						case '3':
							$id_stage = array('1');
							$id_act_num =1;
							break;
						case '4':
							$id_stage = array('2', '3', '4');
							$id_act_num =1;
							break;
						case '5':
							$id_stage = array('LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15');
							$id_act_num =1;
							break;
						case '10':
							$id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON');
							$id_act_num =1;
							break;
						case '11':
							$id_stage = array('WON');
							$id_act_num =1;
							break;
						case 'all':
							//$id_dep = 53;
							break;
					}
				
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
						case '52020':
							$id_year1=date("Y-m-d", strtotime("2020-04-30"));
							$id_year2=date("Y-m-d", strtotime("2020-06-01"));
							break;
						case '62020':
							$id_year1=date("Y-m-d", strtotime("2020-05-31"));
							$id_year2=date("Y-m-d", strtotime("2020-07-01"));
							break;
						case '72020':
							$id_year1=date("Y-m-d", strtotime("2020-05-31"));
							$id_year2=date("Y-m-d");
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
				if ((!empty($_GET['laba']) && ($user_dep == $id_dep||$dop_assign)&&($_GET['laba']!='6'))||empty($_GET['laba'])||($_GET['laba']=='all')||(($_GET['laba']=='6')&&($user_dep!=$id_dep)))
				{
				
					$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $deal['ID'])->Fetch();
					$res_kp=''; $res_dog=''; $res_acc=''; $res_akt_vr='';
					$priceNoDisc = 0;
					$priceContract = 0;
					$patternCheck=0;
					$reason='';
					$res_contract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . $deal['ID'])->Fetch();	
					//$num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
					if ($res_contract['ID']) 
					{
						$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . $res_contract['ID_CONTRACT'])->Fetch();
						$num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
					}
					//$alarmPrices=array();
				$arr = unserialize($res_tz['TZ']);
				$reason='';
					//dump($arr);
					for ($i = 1; $i <= count($arr); $i++)
						for ($j = 0; $j < count($arr[$i]['gost']); $j++) {
							$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $arr[$i]['gost'][$j])->Fetch();
							$priceNoDisc = $priceNoDisc + ($res['PRICE']) * $arr[$i]['obiem'];
							$resContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . $deal['ID'])->Fetch();
							$resPriceForContract1 = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT`=".(int)$resContract['ID_CONTRACT']." AND `ID_GOST`=".(int)$arr[$i]['gost'][$j]);
							$resPriceForContract = $resPriceForContract1->Fetch();
							$priceContract = $priceContract + ($resPriceForContract['PRICE']) * $arr[$i]['obiem'];
							
							
//------------------------ПРОВЕРКА ПАТТЕРНОВ ЦЕНООБРАЗОВАНИЯ------------------------------------------------------------------------------------------------------
						    
							
							if(($resPriceForContract['ID']&&$resPriceForContract['PRICE']!=$arr[$i]['price'][$j])||($res['ID']&&$res['PRICE']!=$arr[$i]['price'][$j]) )
							{
								//$alarmPrices[] = $arr[$i]['price'][$j];
								if ( (($res['ID']==328 || $res['ID']==133)&&in_array('132', $arr[$i]['gost']))
									|| ($res['ID']==2678) 
									|| ($res['ID']==3019)
									|| ($res['ID']==2875) 
									|| ($res['ID']==3022)
									|| ($res['ID']==3379)
									|| ($res['ID']==3380)									
									|| ($res['ID']==312)	
									|| ($res['ID']==2692)
									|| ($res['ID']==1246)
									|| ($res['ID']==598)
									|| ($res['ID']==976)
									|| ($res['ID']==260)
									|| ($res['ID']==604)
									|| ($res['ID']==673)
									|| ($res['ID']==3277)
									|| ($res['ID']==1132)
									|| ($res['ID']==3058)
									|| ($res['ID']==3229)
									|| ($res['ID']==3544)
									|| ($res['ID']==3666)
									|| ($res['ID']==3667)
									|| ($res['ID']==317)
									|| ($res['ID']==325)
									|| ($res['ID']==2680&&(in_array('3289', $arr[$i]['gost'])||in_array('3136', $arr[$i]['gost'])||in_array('3197', $arr[$i]['gost'])||in_array('3278', $arr[$i]['gost'])||in_array('3347', $arr[$i]['gost']))) 
									|| (($res['ID']==3289||$res['ID']==3136||$res['ID']==3197||$res['ID']==3278||$res['ID']==3347)&&in_array('2680', $arr[$i]['gost'])) 
									|| ($res['ID']==211&&in_array('1442', $arr[$i]['gost']))
									|| ($res['ID']==2563&&in_array('134', $arr[$i]['gost']))
									|| ($res['ID']==3004&&in_array('444', $arr[$i]['gost']))
									|| ($res['ID']==1442&&in_array('211', $arr[$i]['gost']))
									|| ($res['ID']==2635&&in_array('1080', $arr[$i]['gost'])) 
									|| ($res['ID']==2634&&in_array('1065', $arr[$i]['gost']))
									|| ($res['ID']==2636&&in_array('1080', $arr[$i]['gost'])) 
									|| ($res['ID']==2637&&in_array('1080', $arr[$i]['gost'])) 
									|| ($res['ID']==2638&&in_array('1080', $arr[$i]['gost']))
									|| ($res['ID']==1081&&in_array('1080', $arr[$i]['gost'])) 
									|| ($res['ID']==1080&&in_array('1081', $arr[$i]['gost']))
									|| ($res['ID']==328&&in_array('329', $arr[$i]['gost'])) 
									|| ($res['ID']==329&&in_array('328', $arr[$i]['gost']))
									|| ($res['ID']==1005&&in_array('1004', $arr[$i]['gost']))
									|| ($res['ID']==1004&&in_array('1005', $arr[$i]['gost']))
									|| ($res['ID']==2341&&in_array('3058', $arr[$i]['gost']))
									|| ($res['ID']==2341&&in_array('1085', $arr[$i]['gost']))
									|| ($res['ID']==1085&&in_array('2341', $arr[$i]['gost']))
									|| ($res['ID']==3002&&in_array('3003', $arr[$i]['gost']))
									|| ($res['ID']==3003&&in_array('3002', $arr[$i]['gost']))
									|| ($res['ID']==308&&in_array('134', $arr[$i]['gost']))
									|| ($res['ID']==134&&in_array('308', $arr[$i]['gost']))
									|| ($res['ID']==1132&&in_array('2341', $arr[$i]['gost']))
									|| ($res['ID']==2341&&in_array('1132', $arr[$i]['gost']))
									|| ($res['ID']==1080&&count(array_keys($arr[$i]['gost'], '1080')) > 1) 
									|| ($res['ID']==2751&&count(array_keys($arr[$i]['gost'], '2751')) > 1)
									|| ($res['ID']==2753&&count(array_keys($arr[$i]['gost'], '2753')) > 1)
									|| ($res['ID']==64&&count(array_keys($arr[$i]['gost'], '64')) > 1)	
									|| ($res['ID']==1111&&count(array_keys($arr[$i]['gost'], '1111')) > 1)
									|| ($res['ID']==15&&count(array_keys($arr[$i]['gost'], '15')) > 1)
									|| ($res['ID']==9&&count(array_keys($arr[$i]['gost'], '9')) > 1)
									|| ($res['ID']==3&&count(array_keys($arr[$i]['gost'], '3')) > 1)
									|| ($res['ID']==59&&count(array_keys($arr[$i]['gost'], '59')) > 1)
									|| ($res['ID']==2341&&count(array_keys($arr[$i]['gost'], '2341')) > 1)
									|| ($res['ID']==1856&&count(array_keys($arr[$i]['gost'], '1856')) > 1)
									|| ($res['ID']==2370&&in_array('2370', $arr[$i]['gost']))
									|| ($res['ID']==1098&&in_array('1097', $arr[$i]['gost'])&&in_array('1101', $arr[$i]['gost']))
									|| ($res['ID']==1106&&in_array('1104', $arr[$i]['gost'])&&in_array('1101', $arr[$i]['gost']))
									|| ($res['ID']==2701&&in_array('319', $arr[$i]['gost'])&&in_array('320', $arr[$i]['gost']))
									|| ($res['ID']==3133&&in_array('317', $arr[$i]['gost'])&&in_array('320', $arr[$i]['gost']))
									|| ($res['ID']==1114&&in_array('1128', $arr[$i]['gost'])&&in_array('1113', $arr[$i]['gost']))
									|| ($res['ID']==1130&&in_array('1128', $arr[$i]['gost'])&&in_array('1129', $arr[$i]['gost']))
									|| ($res['ID']==882&&in_array('881', $arr[$i]['gost'])&&in_array('880', $arr[$i]['gost']))
									|| ($res['ID']==886&&in_array('884', $arr[$i]['gost'])&&in_array('885', $arr[$i]['gost']))
									|| ($res['ID']==1252&&in_array('1249', $arr[$i]['gost'])&&in_array('1251', $arr[$i]['gost']))
									|| ($res['ID']==59&&in_array('60', $arr[$i]['gost']))
									|| ($res['ID']==3&&in_array('9', $arr[$i]['gost']))
									|| ($res['ID']==354&&in_array('353', $arr[$i]['gost']))
 									|| ($res['ID']==60&&in_array('59', $arr[$i]['gost']))  )
									$patternCheck=1;
								$needPatternArr = [2, 14, 1246, 1252, 1254, 25, 1231, 1114, 1118, 172, 1130, 1132, 3058, 1092, 2997, 1098, 1106, 2655, 66, 1075, 105, 106, 107, 955, 957, 882, 884, 885, 886, 887, 890, 558, 562,
                310, 325, 870, 2775, 2776, 860, 2911, 3021, 438, 592, 3006, 3007, 3008, 590, 2734, 2735, 594, 595, 596, 1896, 669, 467, 468, 3097, 2961, 2962, 2761, 495, 1831, 1832, 601, 2892,
                1841, 611, 612, 622, 3019, 1856, 3022, 625, 627, 2879, 614, 616, 618, 620, 621, 651, 2748, 893, 894, 895, 896, 898, 1154, 1209, 1210, 962, 939, 940, 941, 918, 2777, 910, 911,
                912, 913, 914, 915, 916, 929, 219, 1463, 1508, 2367, 357, 358, 359, 998, 2881, 2875, 3080, 307, 564, 312, 58, 314, 1084, 2372, 1115, 611, 347, 3205, 3217, 763, 441, 2702, 334,
				2692, 339, 340, 341, 342, 1256, 880, 881, 882, 884, 885, 886, 887, 955, 353, 969, 41, 54, 43, 3101, 2659, 53, 45, 44, 50, 49, 56, 46, 40, 3229, 801, 803, 647, 454, 1862, 598,
				6, 128, 1366, 317, 319, 320, 10, 7, 1011, 1012, 2782, 1015, 1017, 1019, 1021, 1023, 1025, 2783, 2784, 331, 2905, 2680, 608, 3541, 3330, 315,14,3022,669,625,172,209,1856,3379,3380,3382,3383,3229];
								
								if($arr[$i]['price'][$j]=="0" || in_array($res['ID'], $needPatternArr))  //если нулевая цена или методика требует паттерна
									$reason = 'Нужен паттерн';
								
							}
							
							
						}
					
					if($res_tz) 
						{
							$res_kp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							//$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							$res_akt_vr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();	
							$res_tz_doc = $DB->Query("SELECT * FROM `TZ_DOC` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							
							
						}
					$resPriceForContract2 = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT`=".(int)$res_contract['ID_CONTRACT']);
					if($res_tz_doc['ID'] && empty($res_tz_doc['SEND_DATE']) && $reason=='' && ($resPriceForContract2->SelectedRowsCount()<1)) 
							{
								$reason = 'Прайс не заведен';
																
							}
					$date_protocol = $res_tz['DATE_P_CORR'] ? $res_tz['DATE_P_CORR'] :$res_tz['DATE_P'];
					
					$date_p = date("Y-m-d", strtotime($deal['DATE_CREATE']));	
				
					if($date_p>$id_year1 && $date_p<$id_year2) {
						
					if($id_stage&&in_array($deal['STAGE_ID'], $id_stage)&&(boolean)$res_tz['ACT_NUM']==(boolean)$id_act_num&&$deal['STAGE_ID']!='5'&&$deal['STAGE_ID']!='1'||!$id_stage||$_GET['stage']=='5'&&in_array($deal['STAGE_ID'], $id_stage)||$_GET['stage']=='3'&&in_array($deal['STAGE_ID'], $id_stage)||$_GET['stage']=='10'&&in_array($deal['STAGE_ID'], $id_stage)&&(boolean)$res_tz['ACT_NUM']==(boolean)$id_act_num||$_GET['stage']=='11'&&in_array($deal['STAGE_ID'], $id_stage)) {
					
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
					
					if($res_tz['ID']) 
					{
						$num_act = ($res_tz['ACT_NUM'] ? $res_tz['ACT_NUM'].$y_date_act : '');//$deal['ID'].$y_date_act);
						//$num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
					}
					else 
					{
						$num_act=''; $num_dog='';
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
					
					$arrLoseStage = ['LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15'];
					
					$color_date='';
					$res_comm = $DB->Query("SELECT * FROM `COMMENTS` WHERE `ID_REQ`=" . $deal['ID'])->Fetch();
					if (date('Y-m-d') > date("Y-m-d", strtotime($deadlineDate))&&$deal['STAGE_ID']!='2'&&$deal['STAGE_ID']!='4'&&$deal['STAGE_ID']!='WON'&&$deal['STAGE_ID']!='LOSE'&&$deal['STAGE_ID']!='5'&&$deal['STAGE_ID']!='6'&&$deal['STAGE_ID']!='7'&&$deal['STAGE_ID']!='8'&&$deal['STAGE_ID']!='9') $color_date='red';
					
					if( $res_tz['PRICE'] && ((((int)$priceNoDisc!=(int)$res_tz['PRICE'])&&$res_dog['LONGTERM']!=1&&!$patternCheck&&($date_p>"2020-06-01"))||(((int)$priceContract!=(int)$res_tz['PRICE'])&&!$patternCheck&&$priceContract&&!$res_tz['OPLATA']))) {
					?>
					<tr>
						
						<td><a href='/request_card.php?ID=<?= $deal['ID'] ?>&ACT_NUM=<?=$res_tz['ACT_NUM']?>' target="_blank"><?= $deal['TITLE'] ?></a></td>
						<td><?= $deal['COMPANY_TITLE']." ".($res_dog['LONGTERM'] ? '' : '')  ?></td>
						<td><?= $res_tz['ASSIGNED'] ?></td>
						<td><?= $dealMaterials//$deal['UF_CRM_1560745341'][0] ? $deal['UF_CRM_1560745341'][0] : implode(', ', $deal['UF_CRM_1569151529509']) ?></td>
						<td ><?= ($res_tz['DOGOVOR_NUM'] ? $res_tz['DOGOVOR_NUM'] : '') ?></td>
						<td style="text-align:right"><?=  $priceNoDisc ?></td>
						<td style="text-align:right"><?=  $priceContract&&is_numeric($priceContract) ? $priceContract : '-'  ?></td>
						<td style="text-align:right"><?= $res_tz['PRICE'] ? $res_tz['PRICE'] : '' ?></td>
						<td style="color:red; text-align:right"><?= $priceNoDisc&&is_numeric($priceNoDisc) ?  number_format((($priceNoDisc-$res_tz['PRICE'])/$priceNoDisc)*100, 1, '.', '') : '-' ?></td>
						<td style="color:red; text-align:right"><?=  $priceContract&&is_numeric($priceContract) ? number_format((($priceContract-$res_tz['PRICE'])/$priceContract)*100, 1, '.', '') : '-' ?></td>
						<td><?php echo $reason; /*dump($alarmPrices);*/ ?></td>
						<td><?= $res_acc['ID'] ? 'Есть' : 'Нет' ?></td>
						<td><?= $res_comm['TEXT'] ?></td>
						<td><a href="/discount_rep_card.php?ID=<?= $res_tz['ID'] ?>" target='_blank'>Просмотреть</a></td>
					</tr>
				<?php  
					}
					}
					}
				}
				}
				//substr($deal['UF_CRM_1570765979554'],0,-1)
				} ?>
			</tbody></table>

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
	<script type="text/javascript" src="/jszip/intl.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
	<script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/filtering/type-based/html.js"></script>
	<!--<script src="https://cdn.datatables.net/plug-ins/1.10.21/dataRender/intl.js"></script>-->
	
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
				"columnDefs": [{ "type": "natural-nohtml", "targets": 0 },
				{
					"type": "natural-nohtml", "targets": 5, 
					"render": $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 }) }, 
				{
					"type": "natural-nohtml", "targets": 6, 
					"render": $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 }) }, 
				{ 
					"type": "natural-nohtml", "targets": 7, 
					"render": $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 }) }, 
				// { 
					// "type": "natural-nohtml", "targets": 7, 
					// "render": $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 }) }, 
				{ 
					"type": "natural-nohtml", "targets": 8, 
					"render": $.fn.dataTable.render.intlNumber('ru-RU', { maximumFractionDigits: 1 }) }, 
				{ 
					"type": "natural-nohtml", "targets": 9, 
					"render": $.fn.dataTable.render.intlNumber('ru-RU', { maximumFractionDigits: 1 }) }, 
				{
				  "targets": 1,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 3,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 4,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 12,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				}
				
				],
				"order": [[ 0, "desc" ]],
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });

			$('#requests_filter').on( 'keyup', function () {
				table
					.columns( 0 )
					.search( this.value )
					.draw();
			} );
			
			$('#client_filter').on( 'keyup', function () {
				table
					.columns( 1 )
					.search( this.value )
					.draw();
			} );
			$('#assign_filter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			} );
			
			$('#object_filter').on( 'keyup', function () {
				table
					.columns( 3 )
					.search( this.value )
					.draw();
			} );
			
			$('#contract_filter').on( 'keyup', function () {
				table
					.columns( 4 )
					.search( this.value )
					.draw();
			} );
						
			$('#cost_filter').on( 'keyup', function () {
				table
					.columns( 5 )
					.search( this.value )
					.draw();
			} );
			
			$('#change_filter').on( 'keyup', function () {
				table
					.columns( 6 )
					.search( this.value )
					.draw();
			} );
        });
		
		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
		"ruDate-asc": function ( a, b ) {
			if(a=='') a='01.01.1970';
			if(b=='') b='01.01.1970';
			
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
			if(a=='') a='01.01.1970';
			if(b=='') b='01.01.1970';
			
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
		
		// $('.price_dog').each(function(i,elem) {
			// console.log($(this).html());
			// // if ($(this).html()) {
				// // alert("Остановлено на " + i + "-м пункте списка.");
				// // return false;
			// // } else {
				// // alert(i + ': ' + $(elem).text());
			// // }
		// });

	});
	
	</script>
	
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>