	<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.04.2019
 * Time: 16:52
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал заявок");
(isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

?>


<?php
if (!empty($_POST['ID'])) {

    $results = serialize($_POST['results']);
	$ostatki = serialize($_POST['ostatki']);
	$match = serialize($_POST['match']);
    $sred = serialize($_POST['sr']);
    $temp = serialize($_POST['temp']);
    $vlag = serialize($_POST['vlag']);
	$norm_descript = serialize($_POST['norm_description']);
    $date1 = $_POST['DATE_BEGIN'];
    $date2 = $_POST['DATE_END'];
	$DOPINFO = $_POST['DOP_INFO'];
	$NUM_P = $_POST['NUM_P'];
    $lab = serialize($_POST['lab']);
	$sostav = serialize($_POST['sostav']);
    $OBSHEE = 1;
	$NO_COMPLIANCE = ($_POST['NO_COMPLIANCE'] == 'on') ? 1 : 0;
	$ACTUAL_FOR_OBJECT = ($_POST['ACTUAL_FOR_OBJECT'] == 'on') ? 1 : 0;
	$check_norm=1;
	for($i=0; $i<count($_POST['match']); $i++)
				for($j=0; $j<count($_POST['match'][$i]); $j++)
					for($k=1; $k<=count($_POST['match'][$i][$j]); $k++)
						{
							if ($_POST['match'][$i][$j][$k]=="0") $check_norm = 0;
							//echo $_POST['match'][$i][$j][$k];
						}
	if($ACTUAL_FOR_OBJECT)	$check_norm=1;

	switch ($_POST['sredType']) {
    case 'simple':
        $TYPE_TZ=0;
        break;
    case 'allvalue':
        $TYPE_TZ=1;
        break;
    case '2max':
        $TYPE_TZ=2;
        break;
	case '3max':
        $TYPE_TZ=3;
        break;
	case '4max':
        $TYPE_TZ=4;
        break;
	case 'zern':
        $TYPE_TZ=5;
		$ostatki = serialize($_POST['ostatki']);
		break;
	case 'grunt':
        $TYPE_TZ=6;
        $ostatki = serialize($_POST['ostatki3']);
		break;
	case 'prirod':
        $TYPE_TZ=7;
		$ostatki = serialize($_POST['ostatki4']);
        break;
	case 'tu_12801':
        $TYPE_TZ=8;
		$ostatki = serialize($_POST['ostatki2']);
        break;
	case 'tu_183':
        $TYPE_TZ=9;
		$ostatki = serialize($_POST['ostatki5']);
        break;
	case 'osk1':
        $TYPE_TZ=10;
		break;
	case 'osk2':
        $TYPE_TZ=11;
		break;
}
	$res_act = $DB->Query("SELECT `ID` FROM `ba_tz` WHERE `NUM_P`='" . $NUM_P."' && `ID`!= '" . $_POST['ID']."'")->Fetch();


	// $norm_group = array('80'=>'0', '40'=>'0', '31.5'=>'0', '22.4'=>'0', '20'=>'0', '16'=>'0', '15'=>'0', '11.2'=>'0', '10'=>'0', '8'=>'0', '5'=>'0',
	// '4'=>'0', '2.5'=>'0', '2'=>'0', '1.25'=>'0', '1'=>'0', '0.63'=>'0', '0.5'=>'0', '0.315'=>'0', '0.25'=>'0', '0.16'=>'0', '0.125'=>'0', '0.1'=>'0', '0.071'=>'0', '0.063'=>'0', '0.05'=>'0', '<0.05'=>'0');

	$fl_num_p=0;
//	if($res_act&&$NUM_P) { echo "<script>alert(\"Такой номер протокола уже используется!.\");</script>";	$fl_num_p=1;}
    $TEMP_O = $_POST['TEMP_O'];
    $VLAG_O = $_POST['VLAG_O'];
	$assigned = CUser::GetByID($USER->GetID())->arResult[0];
	$assigned_user = $assigned['NAME'].' '.$assigned['LAST_NAME'];
	$ID_P = $_POST['NUM_P'] ? $_POST['NUM_P'] : (int)$_GET['ID']+485;
	//$dogovor_num = ($ba_tz['DOGOVOR_NUM'] ? $ba_tz['DOGOVOR_NUM'] : 'Договор №'.$ID);
	$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`) VALUES ('" . date('Y-m-d H:i:s') . "','" . $assigned_user . "','" . $ID_P  . "','" . $_GET['ID'] . "','" . $USER->GetID() . "', 'Сохранение результатов испытаний')");


    if (!$fl_num_p) $res = $DB->Query("UPDATE `ba_tz` SET `RESULTS`='" . $results . "', `ostatki`='" . $ostatki . "',`OBSHEE`='" . $OBSHEE . "',`NO_COMPLIANCE`='" . $NO_COMPLIANCE . "',`TYPE_TZ`='" . $TYPE_TZ . "',`TEMP_O`='" . $TEMP_O . "',`VLAG_O`='" . $VLAG_O . "',`TEMP`='" . $temp . "',`VLAG`='" . $vlag . "',`LAB`='" . $lab . "',`DATE1`='" . $date1 . "',`DATE2`='" . $date2 . "',`MATCH_MANUAL`='" . $match . "',`NORM_DESC`='" . $norm_descript . "',`SRED`='" . $sred . "', `DOPINFO`='" . $DOPINFO . "', `NUM_P`='" . $NUM_P . "', `DATE_P_CORR`='" . $_POST['DATE_P_CORR'] . "', `CHECK_NORM`=" . (int)$check_norm . ",`ACTUAL_FOR_OBJECT`='" . $ACTUAL_FOR_OBJECT . "',`GROUP_MAT`='" . $_POST['group_mat'] . "',`SOSTAV`='" . $sostav . "' WHERE ID = " . (int)$_POST['ID']);
    $deal2 = CCrmDeal::GetByID($_GET['ID']);

	//echo $check_norm;

	//var_dump($_POST['match']);
    if (CModule::IncludeModule('crm')&&$deal2['STAGE_ID']!='2'&&$deal2['STAGE_ID']!='3'&&$deal2['STAGE_ID']!='4'&&$deal2['STAGE_ID']!='WON'&&$deal2['TYPE_ID']!='COMPLEX') {
        $deal = new CCrmDeal;
		$num_p = (int)$_GET['ID']+485;
        $arDealUpdate = array('UF_CRM_1554719008992' => $_SERVER["SERVER_NAME"] . "/results_isp.php?ID=" . (int)$_GET['ID'], 'STAGE_ID' => '1', 'UF_CRM_1570518000868' => ($_POST['NUM_P']) ? $_POST['NUM_P'].$y_date : $num_p.$y_date);
        $resUpd = $deal->Update((int)$_GET['ID'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    }
}

if (!empty($_GET['ID'])) {



} else {


	$deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'));
	$location_year = ""; $location_laba = ""; $location_stage="";
	$location_yearPeriod1 = ""; $location_yearPeriod2 = "";

	// ! modifed by vsatonin
	// ? else constuction get-parametres -- new parametrs: dateStart, dateOut
	if(!empty($_GET['laba'])&&empty($_GET['stage'])&&empty($_GET['dateStart'])&&empty($_GET['dateOut']) ) {
		// * done ========
		$location_laba = "window.location.assign('/requests_test.php?laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/requests_test.php?laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
		$location_yearPeriod2 = "window.location.assign('/requests_test.php?laba='+this.options[this.selectedIndex].value&dateStart=".$_GET['dateStart']."&dateOut=".$_GET['dateOut'].")";
	}
	elseif(empty($_GET['laba'])&&!empty($_GET['stage'])&&empty($_GET['dateStart'])&&empty($_GET['dateOut'])) {
		// * done ========
		$location_laba = "window.location.assign('/requests_test.php?stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/requests_test.php?stage='+this.options[this.selectedIndex].value)";
		$location_yearPeriod2 = "window.location.assign('/requests_test.php?laba='+this.options[this.selectedIndex].value&dateStart=".$_GET['dateStart']."&dateOut=".$_GET['dateOut'].")";
	}
	// elseif(empty($_GET['laba'])&&empty($_GET['stage'])&&!empty($_GET['dateStart'])&&!empty($_GET['dateOut'])) {
	// 	echo("<script>console.log('тут');</script>");
	// 	$location_laba = "window.location.assign('/requests_test.php?laba='+this.options[this.selectedIndex].value&dateStart=".$_GET['dateStart']."&dateOut=".$_GET['dateOut'].")";
	// 	$location_stage = "window.location.assign('/requests_test.php?stage='+this.options[this.selectedIndex].value&dateStart=".$_GET['dateStart']."&dateOut=".$_GET['dateOut'].")";
	// 	$location_yearPeriod2 = "window.location.assign('/requests_test.php?dateStart=".$_GET['dateStart']."&dateOut=".$_GET['dateOut'].")";
	// }
	// elseif(!empty($_GET['laba'])&&empty($_GET['stage'])&&!empty($_GET['dateStart'])&&!empty($_GET['dateOut'])) {
	// 	$tmp1 = $_GET['dateStart'];
	// 	$tmp2 = $_GET['dateOut'];

	// 	$location_laba = "window.location.assign('/requests_test.php?dateStart={$tmp1}&dateOut={$tmp2}&laba='+this.options[this.selectedIndex].)";
	// 	$location_stage= "window.location.assign('/requests_test.php?laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value&dateStart=".$_GET['dateStart']."&dateOut=".$_GET['dateOut'].")";
	// 	// echo("<script>console.log('тут');</script>");
	// }
	// elseif(empty($_GET['laba'])&&!empty($_GET['stage'])&&!empty($_GET['dateStart'])&&!empty($_GET['dateOut'])) {
	// 	$location_laba = "window.location.assign('/requests_test.php?laba='+this.options[this.selectedIndex].value&stage=".$_GET['stage']."&dateStart=".$_GET['dateStart']."&dateOut=".$_GET['dateOut'].")";
	// 	$location_stage= "window.location.assign('/requests_test.php?stage='+this.options[this.selectedIndex].value)";
	// 	$location_yearPeriod2 = "window.location.assign('/requests_test.php?stage='+this.options[this.selectedIndex].value&dateStart=".$_GET['dateStart']."&dateOut=".$_GET['dateOut'].")";
	// }
	// elseif(!empty($_GET['laba'])&&!empty($_GET['stage'])&&!empty($_GET['dateStart'])&&!empty($_GET['dateOut'])) {
	// 	$location_laba = "window.location.assign('/requests_test.php?year=".$_GET['year']."&stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_stage= "window.location.assign('/requests_test.php?year=".$_GET['year']."&laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
	// 	$location_yearPeriod2 = "window.location.assign('/requests_test.php?stage=".$_GET['stage']."&laba=".$_GET['laba']."&dateStart='+this.options[this.selectedIndex].value)";
	// }
	else {
		// $location_year = "window.location.assign('/requests_test.php?year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/requests_test.php?laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/requests_test.php?stage='+this.options[this.selectedIndex].value)";

		$location_yearPeriod1 = (!empty($_GET['dateStart'])) ? $_GET['dateStart'] : date('01.m.Y');
		$location_yearPeriod2 = (!empty($_GET['dateStart'])) ? $_GET['dateStart'] : date('t.m.Y');

		// echo("<script>console.log(document.getElementById('period1').value)</script>");

	}

	// // !!! ==========
	// if(!empty($_GET['laba'])&&empty($_GET['year'])&&empty($_GET['stage'])) {
	// 	$location_year = "window.location.assign('/requests_test.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/requests_test.php?laba='+this.options[this.selectedIndex].value)";
	// 	$location_stage= "window.location.assign('/requests_test.php?laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
	// }
	// elseif(empty($_GET['laba'])&&!empty($_GET['year'])&&empty($_GET['stage'])) {
	// 	$location_year = "window.location.assign('/requests_test.php?year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/requests_test.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_stage= "window.location.assign('/requests_test.php?year=".$_GET['year']."&stage='+this.options[this.selectedIndex].value)";
	// }
	// elseif(empty($_GET['laba'])&&empty($_GET['year'])&&!empty($_GET['stage'])) {
	// 	$location_year = "window.location.assign('/requests_test.php?stage=".$_GET['stage']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/requests_test.php?stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_stage= "window.location.assign('/requests_test.php?stage='+this.options[this.selectedIndex].value)";
	// }
	// elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&&empty($_GET['stage'])) {
	// 	$location_year = "window.location.assign('/requests_test.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/requests_test.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_stage= "window.location.assign('/requests_test.php?laba=".$_GET['laba']."&year=".$_GET['year']."&stage='+this.options[this.selectedIndex].value)";
	// }
	// elseif(!empty($_GET['laba'])&&empty($_GET['year'])&&!empty($_GET['stage'])) {
	// 	$location_year = "window.location.assign('/requests_test.php?laba=".$_GET['laba']."&stage=".$_GET['stage']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/requests_test.php?stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_stage= "window.location.assign('/requests_test.php?laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
	// }
	// elseif(empty($_GET['laba'])&&!empty($_GET['year'])&&!empty($_GET['stage'])) {
	// 	$location_year = "window.location.assign('/requests_test.php?stage=".$_GET['stage']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/requests_test.php?year=".$_GET['year']."&stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_stage= "window.location.assign('/requests_test.php?year=".$_GET['year']."&stage='+this.options[this.selectedIndex].value)";
	// }
	// elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&&!empty($_GET['stage'])) {
	// 	$location_year = "window.location.assign('/requests_test.php?stage=".$_GET['stage']."&laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/requests_test.php?year=".$_GET['year']."&stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
	// 	$location_stage= "window.location.assign('/requests_test.php?year=".$_GET['year']."&laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
	// }
	// else {
	// 	$location_year = "window.location.assign('/requests_test.php?year='+this.options[this.selectedIndex].value)";
	// 	$location_laba = "window.location.assign('/requests_test.php?laba='+this.options[this.selectedIndex].value)";
	// 	$location_stage = "window.location.assign('/requests_test.php?stage='+this.options[this.selectedIndex].value)";
	// }
	?>
<div class="filter-bar">
	<table >
	<tr>
		<td>
		<label class="icon_date">
			<input class="periodYear" id="period1" type="text" title="Выберите начало отрезка времени" value="<?= ((!empty($_GET['dateStart'])) ? date('d.m.Y', strtotime($_GET['dateStart'])) : '01.01.2020' )?>" required >
		</label>
		</td>

		<td>
			<label class="icon_date">
				<input class="periodYear" id="period2" type="text" title="Выберите конец отрезка времени" value="<?= ((!empty($_GET['dateOut'])) ? date('d.m.Y', strtotime($_GET['dateOut'])) : date('t.m.Y') )?>" required >
			</label>
		</td>

	<td>
	<select class="oborud" name="stage" id="stage" onchange="<?= $location_stage?>">
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

		<!-- <select class="oborud" name="year_p" id="year" onchange="<?= $location_year?>" style="float: left; width: 100%">
			<option value="2020" style='font-weight:bold' <?=$_GET['year'] == '2020' ? 'selected' : ''?> selected>2020</option>
			<option value="12020" style='font-weight:bold' <?=$_GET['year'] == '12020' ? 'selected' : ''?>>Январь 2020</option>
			<option value="22020" style='font-weight:bold' <?=$_GET['year'] == '22020' ? 'selected' : ''?>>Февраль 2020</option>
			<option value="32020" style='font-weight:bold' <?=$_GET['year'] == '32020' ? 'selected' : ''?>>Март 2020</option>
			<option value="42020" style='font-weight:bold' <?=$_GET['year'] == '42020' ? 'selected' : ''?>>Апрель 2020</option>
			<option value="102020" style='font-weight:bold' <?=$_GET['year'] == '102020' ? 'selected' : ''?>>Октябрь 2020</option>
			<option value='2019' style='font-weight:bold' <?=$_GET['year'] == '2019' ? 'selected' : ''?>>2019</option>
			<option value='all' style='font-weight:bold' <?=$_GET['year'] == 'all' ? 'selected' : ''?> >Все года</option>
		</select> -->

	<td>
		<select class="oborud" name="laba" id="laba" onchange="<?= $location_laba?>">
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
		</select>
	</td>

	<td><input type="button" class="add2" value="Х" onclick="window.location.assign('/requests_test.php')"></input></td>
<!--	<td><input type="checkbox" name="manufacture" id="manufacture"> Производитель</td>-->
	</tr>
	</table>
</div>

	<br /><br /><br />
    <table id="results_isp">
	<thead>
		<tr>
		<th></th>
        <th>Заявка</th>
		<th>Дата</th>
		<th>Клиент</th>
		<th>Крайний срок</th>
		<th>Объект испытаний</th>
		<th>Ответственный</th>
		<th>Акт ПП</th>
		<th>ТЗ</th>
		<!--<th>КП</th>-->
		<th >Договор&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
		<?php if(in_array('8', $USER->GetUserGroupArray())) {?><th>Стоимость</th><?}?>
		<!--'Вывод номера счета в журнале заявок' начало-->
		<th>Счет</th>
		<!--'Вывод номера счета в журнале заявок' окончание-->
		<th>Дата оплаты</th>
		<th>Рез-ты испытаний</th>
		<th>Протокол</th>
		<th>Производитель</th>
		<th>Последнее изменение</th>
		<!----------------------------------- 'Копировать' правил начало ---------------------------->
		<!--<th></th>-->
		<!----------------------------------- 'Копировать' правил окончание ----------------------->
		</tr>
        <tr>
			<th style="margin:0px"></th>
			<th><input type="text" name="requests_filter" id="requests_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="dates_filter" id="dates_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="client_filter" id="client_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="deadline_filter" id="deadline_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="object_filter" id="object_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="responsible_filter" id="responsible_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="act_filter" id="act_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="num_tz_filter" id="num_tz_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="contract_filter" id="contract_filter" style="margin:0px" value=""></th>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><th><input type="text" name="cost_filter" id="cost_filter" style="margin:0px" value=""></th><?}?>
			<!--'Вывод номера счета в журнале заявок' начало-->
			<th><input type="text" name="inv_filter" id="inv_filter" style="margin:0px" value=""></th>
			<!--'Вывод номера счета в журнале заявок' окончание-->
			<th style="margin:0px"></th>
			<th style="margin:0px"></th>
			<th><input type="text" name="protocol_filter" id="protocol_filter" style="margin:0px" value=""></th>
				<!--'Производитель' начало-->
			<th>
				<input type="text" name="manufacturer_filter" id="manufacturer_filter">
			</th>
			<!--'Производитель' окончание-->
			<th><input type="text" name="change_filter" id="change_filter" style="margin:0px" value=""></th>
			<!----------------------------------- 'Копировать' правил начало ------------------------->
			<!--<th style="margin:0px"></th>-->
			<!----------------------------------- 'Копировать' правил окончание ----------------------->
		</tr>
	</thead>
	<tbody>
		<div id='buttonRightTable' class='center'></div>
		<div id='buttonLeftTable'class='center'></div>

        <?php

			// while ($deal = $deals->Fetch()) {


				// switch ($_GET['laba']) {
						// case '1':
							// $id_dep = 54;
							// break;
						// case '2':
							// $id_dep = 57;
							// break;
						// case '3':
							// $id_dep = 56;
							// break;
						// case '4':
							// $id_dep = 55;
							// break;
						// case '5':
							// $id_dep = 58;
							// break;
						// case '6':
							// $id_dep = 53;
							// break;

					// }

				// switch ($_GET['stage']) {
						// case '1':
							// $id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING');
							// $id_act_num =0;
							// break;
						// case '2':
							// $id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE');
							// $id_act_num =1;
							// break;
						// case '3':
							// $id_stage = array('1');
							// $id_act_num =1;
							// break;
						// case '4':
							// $id_stage = array('2', '3', '4');
							// $id_act_num =1;
							// break;
						// case '5':
							// $id_stage = array('5', '6', '10', '8', '9', 'LOSE');
							// $id_act_num =1;
							// break;
						// case '10':
							// $id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON');
							// $id_act_num =1;
							// break;
						// case '11':
							// $id_stage = array('WON');
							// $id_act_num =1;
							// break;
						// case 'all':
							// //$id_dep = 53;
							// break;
					// }

				// switch ($_GET['year']) {
						// case '2019':
							// $id_year1=date("Y-m-d", strtotime("2018-12-31"));
							// $id_year2=date("Y-m-d", strtotime("2020-01-01"));
							// break;
						// case '2020':
							// $id_year1=date("Y-m-d", strtotime("2019-12-31"));
							// $id_year2=date("Y-m-d", strtotime("2021-01-01"));
							// break;
						// case '12020':
							// $id_year1=date("Y-m-d", strtotime("2019-12-31"));
							// $id_year2=date("Y-m-d", strtotime("2020-02-01"));
							// break;
						// case '22020':
							// $id_year1=date("Y-m-d", strtotime("2020-01-31"));
							// $id_year2=date("Y-m-d", strtotime("2020-03-01"));
							// break;
						// case '32020':
							// $id_year1=date("Y-m-d", strtotime("2020-02-29"));
							// $id_year2=date("Y-m-d", strtotime("2020-04-01"));
							// break;
						// case '42020':
							// $id_year1=date("Y-m-d", strtotime("2020-03-31"));
							// $id_year2=date("Y-m-d", strtotime("2020-05-01"));
							// break;
						// case 'all':
							// $id_year1=date("Y-m-d", strtotime("2019-01-01"));
							// $id_year2=date("Y-m-d", strtotime("2029-12-31"));
							// break;

					// }
			// if(empty($_GET['year']))
			// {
				// $id_year1=date("Y-m-d", strtotime("2020-01-01"));
				// $id_year2=date("Y-m-d", strtotime("2021-01-01"));
			// }

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

				// //echo $user_dep;
				// if ((!empty($_GET['laba']) && ($user_dep == $id_dep||$dop_assign))||empty($_GET['laba'])||($_GET['laba']=='all'))
				// {

					// $res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $deal['ID'])->Fetch();
					// $res_kp=''; $res_dog=''; $res_acc=''; $res_akt_vr='';

					// if($res_tz)
						// {
							// $res_kp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							// //$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							// $res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							// $res_akt_vr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();


						// }

					// $date_protocol = $res_tz['DATE_P_CORR'] ? $res_tz['DATE_P_CORR'] :$res_tz['DATE_P'];

					// $date_p = date("Y-m-d", strtotime($deal['DATE_CREATE']));

					// if($date_p>$id_year1 && $date_p<$id_year2) {

					// if($id_stage&&in_array($deal['STAGE_ID'], $id_stage)&&(boolean)$res_tz['ACT_NUM']==(boolean)$id_act_num&&$deal['STAGE_ID']!='5'&&$deal['STAGE_ID']!='1'||!$id_stage||$_GET['stage']=='5'&&in_array($deal['STAGE_ID'], $id_stage)||$_GET['stage']=='3'&&in_array($deal['STAGE_ID'], $id_stage)||$_GET['stage']=='10'&&in_array($deal['STAGE_ID'], $id_stage)&&(boolean)$res_tz['ACT_NUM']==(boolean)$id_act_num||$_GET['stage']=='11'&&in_array($deal['STAGE_ID'], $id_stage)) {

					// if($_GET['stage']=='6'&&$res_tz['PRICE']&&!$res_tz['OPLATA']||$_GET['stage']=='7'&&$res_tz['PRICE']&&$res_tz['OPLATA']&&$res_tz['OPLATA']<$res_tz['PRICE']||$_GET['stage']=='8'&&$res_tz['PRICE']&&$res_tz['OPLATA']&&$res_tz['OPLATA']>$res_tz['PRICE']||$_GET['stage']=='9'&&$res_tz['PRICE']&&$res_tz['OPLATA']&&$res_tz['OPLATA']==$res_tz['PRICE']||!$_GET['stage']||$_GET['stage']=='4'||$_GET['stage']=='5'||$_GET['stage']=='3'||$_GET['stage']=='2'||$_GET['stage']=='1'||$_GET['stage']=='10'||$_GET['stage']=='11'||$_GET['stage']=='all') {
					// $y_date_act = date("Y", strtotime($res_tz['DATE_ACT']));
					// if ($y_date_act != '2020') $y_date_act = "/".substr($y_date_act, 2);
							// else $y_date_act = "/".$y_date_act;

					// $y_date_p = date("Y", strtotime($date_protocol));
					// if ($y_date_p != '2020') $y_date_p = "/".substr($y_date_p, 2);
							// else $y_date_p = "/".$y_date_p;

					// if($res_tz['ID']) {
						// if ($res_tz['NUM_P'] === 'номер не присвоен') {
							// $num_tz = 'номер не присвоен';
						// } else {
							// $num_tz = $res_tz['NUM_P'] ? $res_tz['NUM_P'].$y_date_p : '';
						// }
					// }
					// else $num_tz='';
					// $color=''; $c=0; $title='';
					// $history_ass='';
					// $history_date='';

					// if ($res_tz['ID'])
					// {
						// $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . $deal['ID']. " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
						// $history_ass=$history['ASSIGNED'];
						// $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
						// $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
						// if($history_date<$history_date_null) $history_date='';
					// }
					// //var_dump($history);
					// switch ($deal['STAGE_ID']) {
						// case 'NEW':
							// $color='#00B0F0';
							// $c=0;
							// $title='Испытания еще не проводились. Пробы не получены.';
							// break;
						// case 'PREPARATION':
							// $color='#00B0F0';
							// $c=0;
							// $title='Испытания еще не проводились. Пробы не получены.';
							// break;
						// case 'PREPAYMENT_INVOICE':
							// $color='#00B0F0';
							// $c=0;
							// $title='Испытания еще не проводились. Пробы не получены.';
							// break;
						// case 'EXECUTING':
							// $color='#00B0F0';
							// $c=0;
							// $title='Испытания еще не проводились. Пробы не получены.';
							// break;
						// case 'FINAL_INVOICE':
							// $color='#FFFF00';
							// $c=1;
							// $title='Пробы получены. Проводятся испытания';
							// break;
						// case '1':
							// $color='#FFFF00';
							// $c=2;
							// $title='Пробы получены. Проводятся испытания.';
							// break;
						// case '2':
							// $color='#833ea3';
							// $c=3;
							// $title='Испытания в лаборатории завершены. Оплата получена или не требуется.';
							// break;
						// case '4':
							// $color='#19ff19';
							// $c=4;
							// $title='Акты ВР отправлены заказчику.';
							// break;
						// // case '3':
							// // $color='green';
							// // $c=5;
							// // $title='Протокол выдан';
							// // break;
						// case 'WON':
							// $color='#70AD47';
							// $c=6;
							// $title='Акты ВР получены. Заявка успешно завершена.';
							// break;
						// case 'LOSE':
							// $color='red';
							// $c=7;
							// $title='Испытания не проведены. Заявка прекращена.';
							// break;
						// case '7':
							// $color='red';
							// $c=9;
							// $title='Заявка прекращена. Не проводим подобные испытания.';
							// break;
						// case '6':
							// $color='red';
							// $c=9;
							// $title='Заявка прекращена. Заказчик не вышел на связь.';
							// break;
						// case '5':
							// $color='red';
							// $c=9;
							// $title='Заявка прекращена. Заказчика не устроила цена.';
							// break;
						// case '8':
							// $color='red';
							// $c=9;
							// $title='Заявка прекращена. Создана другая заявка.';
							// break;
						// case '9':
							// $color='red';
							// $c=9;
							// $title='Заявка прекращена. Заказчик выбрал лабораторию в своем городе.';
							// break;
						// case '10':
							// $color='red';
							// $c=9;
							// $title='Заявка прекращена. Заказчик решил не проводить испытания.';
							// break;
						// case '11':
							// $color='red';
							// $c=9;
							// $title='Заявка прекращена. Отказались сами в связи с высокой загруженностью.';
							// break;
						// case '12':
							// $color='red';
							// $c=9;
							// $title='Заявка прекращена. Судебная экспертиза.';
							// break;
						// case '13':
							// $color='red';
							// $c=9;
							// $title='Заявка прекращена. Участие в тендере.';
							// break;
					// }
					// if ($res_tz['ACT_NUM']&&($deal['STAGE_ID']=='NEW'||$deal['STAGE_ID']=='PREPARATION'||$deal['STAGE_ID']=='PREPAYMENT_INVOICE'||$deal['STAGE_ID']=='EXECUTING')) {
							// $color='yellow';
							// $c=1;
							// $title='Протокол не выдан. Испытания еще не проводились. Пробы получены';
					// }
					// if ($res_tz['PRICE']&&$res_tz['RESULTS']&&(!$res_tz['OPLATA']||$res_tz['OPLATA']<$res_tz['PRICE'])&&$deal['STAGE_ID']=='2') {
							// $color='#9b2d30';
							// $c=5;
							// $title='Испытания в лаборатории завершены. Оплата не поступила.';
					// }
					// // if (CModule::IncludeModule('crm')) {
						// // $deal = new CCrmDeal;
						// // $arDealUpdate = array('UF_CRM_1570518000868' => $num_tz);
						// // $resUpd = $deal->Update((int)$_GET['ID'],
							// // $arDealUpdate,
							// // true,
							// // true,
							// // array('DISABLE_USER_FIELD_CHECK' => true)
						// // );
					// // }
					// //var_dump($res_tz);
					// //var_dump($deal);
					// //echo $deal['SOURCE_DESCRIPTION'];
					// $companyName = $USER_FIELD_MANAGER->GetUserFields('CRM_COMPANY', $deal['COMPANY_ID']);
					// //$companyDiscount = $companyName['UF_CRM_1582914274353']['VALUE'];
					// $res_contract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . $deal['ID'])->Fetch();
					// //$num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
					// if ($res_contract['ID'])
					// {
						// $res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . $res_contract['ID_CONTRACT'])->Fetch();
						// $num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
					// }
					// if($res_tz['ID'])
					// {
						// $num_act = ($res_tz['ACT_NUM'] ? $res_tz['ACT_NUM'].$y_date_act : '');//$deal['ID'].$y_date_act);
						// //$num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
					// }
					// else
					// {
						// $num_act=''; $num_dog='';
					// }
					// $deadlineDate = ($deal['UF_CRM_1577124965971'] ? $deal['UF_CRM_1577124965971'] : ruDate($res_tz['DEADLINE']));
					// if ($deadlineDate=="01.01.1970") $deadlineDate=date('d.m.Y', strtotime($deal['DATE_CREATE'])+ 86400*3);
					// $mater_arr=array();
					// $mater_arr_id=array();

					// $res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=".$deal['ID']);
						// while($mater=$res_mat->Fetch())
						// {
							// $mater_arr[]=$mater['NAME_MATERIAL'];
							// $mater_arr_id[]=$mater['ID_MATERIAL'];
						// }

					// $dealMaterials = ($deal['UF_CRM_1576411922643'] ? $deal['UF_CRM_1576411922643'] : implode(', ', $deal['UF_CRM_1569151529509']));
					// $dealMaterials = ($dealMaterials  ? $dealMaterials : implode(', ', $mater_arr));

					// $color_date='';

					// if (date('Y-m-d') > date("Y-m-d", strtotime($deadlineDate))&&$deal['STAGE_ID']!='2'&&$deal['STAGE_ID']!='4'&&$deal['STAGE_ID']!='WON'&&$deal['STAGE_ID']!='LOSE'&&$deal['STAGE_ID']!='5'&&$deal['STAGE_ID']!='6'&&$deal['STAGE_ID']!='7'&&$deal['STAGE_ID']!='8'&&$deal['STAGE_ID']!='9') $color_date='red';
					// ?>
					<!-- <tr>
						<td style="background-color: <?/*=$color*/?>; color: <?/*=$color*/?>" title="<?/*=$title*/?>"><?/*= $c */?></td>
						<td><a href='/request_card.php?ID=<?/*= $deal['ID'] */?>&ACT_NUM=<?/*=$res_tz['ACT_NUM']*/?>' target="_blank">
						<?/*= $deal['TITLE'] */?></a></td>
						<td><?/*= date("d.m.Y", strtotime($deal['DATE_CREATE'])) */?> </td>
						<td><?/*= $deal['COMPANY_TITLE'] */?></td>
						<td style="color:<?/*= $color_date */?>;"><?/*= $deal['TYPE_ID']!= "COMPLEX" ? $deadlineDate : '' */?></td>
						<td><?/*= $dealMaterials */?></td>
						<td><?php /*echo "<b>".getInitials($deal['ASSIGNED_BY_NAME'])." ".$deal['ASSIGNED_BY_LAST_NAME']."</b>";
						if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {

								foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
								{
									$assign_user = CUser::GetByID((int)$assign)->Fetch();
									echo ", ".getInitials($assign_user['NAME'])."&nbsp;".$assign_user['LAST_NAME'];
								}
							}
						*/?>
						</td>
						<td style="color:<?/*= $color */?>;"><a href="/probe.php?ID=<?/*=$deal['ID']*/?>&1" target="_blank"><?/*= $num_act */?></td>
						<td><a href="/tz_show.php?ID=<?/*= $res_tz['ID'] */?>" target="_blank"><?/*= $res_tz['ID'] */?></a></td>
						<td ><?/*= ($res_tz['DOGOVOR_NUM'] ? $res_tz['DOGOVOR_NUM'] : '') */?></td>
						<?php /*if(in_array('8', $USER->GetUserGroupArray())) {*/?><td style="background-color: #<?/*= ( $res_tz['PRICE']&&($res_tz['OPLATA'] == $res_tz['PRICE'])||!$res_tz['PRICE'] ? 'fff' : 'FFC0CB') */?>; text-align:right"><?/*= $res_tz['PRICE'] ? $res_tz['PRICE'] : '' */?></td> <?/*}*/?>
						<td><?/*= $res_tz['DATE_OPLATA'] */?></td>
						<td><?php /*if (!$res_tz['PDF']) { */?><a href="/results_isp.php?ID=<?/*= $deal['ID'] */?>" target="_blank"><?/*=$res_tz['ID']&&$res_tz['ACT_NUM'] ? 'Открыть' : ''*/?></a><?php /*} else echo "Выдан вне ЛИС";*/?></td>
						<td><?php /*if(!$res_tz['NO_BITRIX'])
						{
							if($res_tz['ACTUAL_VER']&&$res_tz['NUM_P']!='номер не присвоен')
							{
								$dir  = '/protocol_generator/archive/'.$res_tz['ID'].date("Y", strtotime($res_tz['DATE_ACT'])).'/';*/?>
								<a href="<?/*= $dir.$res_tz['ACTUAL_VER'] */?>.docx?1&1" target="_blank"><?/*= $res_tz['RESULTS'] ? $num_tz : ''  */?> </a>
								<?php
/*							}
						}
						else
						{ */?>
						<a href="/pdf/<?/*= $res_tz['PDF'] */?>" target="_blank"><?/*= $res_tz['PDF'] ? $num_tz : ''   */?> </a>
						<?php /*} */?>
					</td>

						<td><?/*= $history_ass */?> <?/*=$history_date*/?></td>-->

                        <!--<td style="background-color: <?/*=$color*/?>; color: <?/*=$color*/?>" title="<?/*=$title*/?>"><?/*= $c */?></td>
                        <td><a href='/request_card.php?ID=<?/*= $deal['ID'] */?>&ACT_NUM=<?/*=$res_tz['ACT_NUM']*/?>' target="_blank">
                                <?/*= $deal['TITLE'] */?></a></td>
                        <td><?/*= date("d.m.Y", strtotime($deal['DATE_CREATE'])) */?> </td>
                        <td><?/*= $deal['COMPANY_TITLE'] */?></td>
                        <td style="color:<?/*= $color_date */?>;"><?/*= $deal['TYPE_ID']!= "COMPLEX" ? $deadlineDate : '' */?></td>
                        <td><?/*= $dealMaterials */?></td>
                        <td><?php /*echo "<b>".getInitials($deal['ASSIGNED_BY_NAME'])." ".$deal['ASSIGNED_BY_LAST_NAME']."</b>";
                            if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {

                                foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
                                {
                                    $assign_user = CUser::GetByID((int)$assign)->Fetch();
                                    echo ", ".getInitials($assign_user['NAME'])."&nbsp;".$assign_user['LAST_NAME'];
                                }
                            }
                            */?>
                        </td>
                        <td style="color:<?/*= $color */?>;"><a href="/probe.php?ID=<?/*=$deal['ID']*/?>&1" target="_blank"><?/*= $num_act */?></td>
                        <td><a href="/tz_show.php?ID=<?/*= $res_tz['ID'] */?>" target="_blank"><?/*= $res_tz['ID'] */?></a></td>
                        <td ><?/*= ($res_tz['DOGOVOR_NUM'] ? $res_tz['DOGOVOR_NUM'] : '') */?></td>
                        <?php /*if(in_array('8', $USER->GetUserGroupArray())) {*/?><td style="background-color: #<?/*= ( $res_tz['PRICE']&&($res_tz['OPLATA'] == $res_tz['PRICE'])||!$res_tz['PRICE'] ? 'fff' : 'FFC0CB') */?>; text-align:right"><?/*= $res_tz['PRICE'] ? $res_tz['PRICE'] : '' */?></td> <?/*}*/?>
                        <td><?/*= $res_tz['DATE_OPLATA'] */?></td>
                        <td><?php /*if (!$res_tz['PDF']) { */?><a href="/results_isp.php?ID=<?/*= $deal['ID'] */?>" target="_blank"><?/*=$res_tz['ID']&&$res_tz['ACT_NUM'] ? 'Открыть' : ''*/?></a><?php /*} else echo "Выдан вне ЛИС";*/?></td>
                        <td><?php /*if(!$res_tz['NO_BITRIX'])
                            {
                                if($res_tz['ACTUAL_VER']&&$res_tz['NUM_P']!='номер не присвоен')
                                {
                                    $dir  = '/protocol_generator/archive/'.$res_tz['ID'].date("Y", strtotime($res_tz['DATE_ACT'])).'/';*/?>
                                    <a href="<?/*= $dir.$res_tz['ACTUAL_VER'] */?>.docx?1&1" target="_blank"><?/*= $res_tz['RESULTS'] ? $num_tz : ''  */?> </a>
                                    <?php
/*                                }
                            }
                            else
                            { */?>
                                <a href="/pdf/<?/*= $res_tz['PDF'] */?>" target="_blank"><?/*= $res_tz['PDF'] ? $num_tz : ''   */?> </a>
                            <?php /*} */?>
                        </td>

                        <td><?/*= $history_ass */?> <?/*=$history_date*/?></td>
					</tr>-->
				<?php
					// }
					// }
				// }
				// }
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

	<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></scrip>
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

	<!-- собственный календарь при выборе отрезка времени -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="/vendor/jquery_localization-ui/localization.js"></script>
	<script src="modal/modal.js"></script>
	<?php if(in_array('23', $USER->GetUserGroupArray())) {?><script src="modal/modal_w.js"></script><?}?>
	<?php if(in_array('25', $USER->GetUserGroupArray()) || ($USER->GetID() == 58) ) {?><script src="modal/modal_a.js"></script><?}?>
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
		onClose: function() {
			$(this).focus();
		}
	});

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
			var dateStart, dateOut;
	//--'Производитель' начало--//
			var manufacture;


			stage = $('#stage option:selected').val();


			year = $('#year option:selected').val();


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

			$('#period2, #stage, #laba').on('change', function() {

				let s = $('#period1').val();
				let e = $('#period2').val();

				let stage = $('#stage option:selected').val();
				let strStage = '&stage='+stage;

				let laba = $('#laba option:selected').val();
				let strlaba = '&laba='+laba;

				let sDate = getServerDate(s);
				let eDate = getServerDate(e);
				let str = 'requests_test.php?dateStart='+sDate+'&dateOut='+eDate;
				dateStart = sDate;
				dateOut = eDate;

				if(stage != 'all'){
					str += strStage;
				}
				if(laba != 'all'){
					str += strlaba;
				}
				window.location.assign(str);
			})

            table= $('#results_isp').DataTable({

                "processing": true,
                "serverSide": true,
                "ajax": {
                    url : "processing_debag.php",
                    type: "POST",
										data: {
											stage: stage,
											// year: year,
											laba: laba,
											manufacture: manufacture,
											dateStart: dateStart,
											dateOut: dateOut,
										},
                },
				lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "All"]],
				pageLength: 50,

                "createdRow": function ( row, data, index ) {

                    $(row).children(':nth-child(1)').css({'background-color': data['COLOR'], 'font-size':0});
                    $(row).children(':nth-child(1)').attr('title', data['HOVER']);

                    $(row).children(':nth-child(11)').css('background-color', data['COLOR_PRICE']);

					if(data['COLOR_CHECK_TZ'] ==  'mango_class'){
						$(row).addClass('mango-class');
					}

                },
                "columns": [
                    { "data": "STAGE_NUMBER" },
                    { "data": "REQUEST_TITLE" },
                    { "data": "DATE_CREATE" },
                    { "data": "COMPANY_TITLE" },
                    { "data": "DEADLINE_TABLE" },
                    { "data": "MATERIAL" },
                    { "data": "ASSIGNED" },
                    { "data": "NUM_ACT_TABLE" },
                    { "data": "ID" },
                    { "data": "DOGOVOR_TABLE" },
                    { "data": "PRICE" },

					{ "data": "ACCOUNT" },

                    { "data": "DATE_OPLATA" },
                    { "data": "RESULT_ISP" },
                    { "data": "NUM_P_TABLE" },
					{ "data": "MANUFACTURER_TITLE" },
                    { "data": "USER_HISTORY" }
                ],


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
				"columnDefs": [{ "type": "natural-nohtml", "targets": 1 }, { "sType": "ruDate", "targets": 2 },{ "sType": "ruDate", "targets": 4 },{ "type": "natural-nohtml", "targets": 7 },
				{ "type": "natural-nohtml", "targets": 10, "render": $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 }) }, { "sType": "ruDate", "targets": 12 },
				{ "type": "natural-nohtml", "targets": 13 }, { "type": "natural-nohtml", "targets": 14 },
				{
				  "targets": 3,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 5,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 6,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 9,
				  "render": $.fn.dataTable.render.ellipsis( 30, true )

				},
				{
				  "targets": 16,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},

				],

				"order": [[ 2, "desc" ]],
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]

            });
			$('#manufacture').on( 'click', function () {
				if ($('#manufacture').prop('checked')) {
					table
					.columns( 0 )
					.search( 'checked' )
					.draw();
				} else {
					table
					.columns( 0 )
					.search( '' )
					.draw();
				}

			} );

			$('#requests_filter').on( 'keyup', function () {
				table
					.columns( 1 )
					.search( this.value )
					.draw();
			} );

			$('#dates_filter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			} );

			$('#client_filter').on( 'keyup', function () {
				table
					.columns( 3 )
					.search( this.value )
					.draw();
			} );

			$('#deadline_filter').on( 'keyup', function () {
				table
					.columns( 4 )
					.search( this.value )
					.draw();
			} );

			$('#object_filter').on( 'keyup', function () {
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
			} );

			$('#act_filter').on( 'keyup', function () {
				table
					.columns( 7 )
					.search( this.value )
					.draw();
			} );

			$('#num_tz_filter').on( 'keyup', function () {
				table
					.columns( 8 )
					.search( this.value )
					.draw();
			} );

			$('#contract_filter').on( 'keyup', function () {
				table
					.columns( 9 )
					.search( this.value )
					.draw();
			} );

			$('#cost_filter').on( 'keyup', function () {
				table
					.columns( 10 )
					.search( this.value )
					.draw();
			} );


			$('#inv_filter').on( 'keyup', function () {
				table
					.columns( 11 )
					.search( this.value )
					.draw();
			} );


			$('#protocol_filter').on( 'keyup', function () {
				table
					//.columns( 13 )
					.columns( 14 )
					.search( this.value )
					.draw();
			} );

			$('#manufacturer_filter').on('keyup', function() {
				table
					.columns(15)
					.search(this.value)
					.draw();
			});
			$('#change_filter').on( 'keyup', function () {
				table
					//.columns( 14 )
					.columns( 16 )
					.search( this.value )
					.draw();
			} );

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

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
