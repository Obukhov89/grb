<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 25.03.2019
 * Time: 12:35
 */

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("Отчет о результатах деятельности ИЦ"); 
?>
<style>
.results_isp_filter, .dataTables_filter { display: none; }
.results_isp_length, .dataTables_length { display: none; }

</style>
<?php
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

function lab_report($user_dep)
{
	switch ($user_dep) {
				case 1:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `LFHI` = 1");
					$lab_name="ЛФХИ";
					$id_dep = 54;
					break;
				case 2:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `LSM` = 1");
					$lab_name="ЛСМ";
					$id_dep = 57;
					break;
				case 3:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `LFMI` = 1");
					$lab_name="ЛФМИ";
					$id_dep = 56;
					break;
				case 4:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `DSL` = 1");
					$lab_name="ДСЛ";
					$id_dep = 55;
					break;
				case 5:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `OSK` = 1");
					$lab_name="ОСК";
					$id_dep = 58;
					break;
				case 6:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `OSK` = 0");
					$lab_name="Только ИЦ";
					$id_dep = 53;
					break;
				
			}
}

$norm_dop=[];
$val_dop=[];
$location_year = "window.location.assign('/report_fin_day.php?year='+this.options[this.selectedIndex].value)";
?>
<form id="form_report" method="post">
	<table>
	<tr>
    
	<td><select class="oborud" name="year_p" id="year" onchange="<?= $location_year?>" style="float: left; width: 100%">
		
		<option value="" style='font-weight:bold'  selected>Выберите месяц</option>
		<option value="sep" style='font-weight:bold' <?=$_GET['year'] == 'sep' ? 'selected' : ''?>>Сентябрь 2022</option>
		<option value="aug" style='font-weight:bold' <?=$_GET['year'] == 'aug' ? 'selected' : ''?>>Август 2022</option>
		<option value="jul" style='font-weight:bold' <?=$_GET['year'] == 'jul' ? 'selected' : ''?>>Июль 2022</option>
		<option value="jun" style='font-weight:bold' <?=$_GET['year'] == 'jun' ? 'selected' : ''?>>Июнь 2022</option>
		<option value="may" style='font-weight:bold' <?=$_GET['year'] == 'may' ? 'selected' : ''?>>Май 2022</option>
		<option value="apr" style='font-weight:bold' <?=$_GET['year'] == 'apr' ? 'selected' : ''?>>Апрель 2022</option>
		<option value="mar" style='font-weight:bold' <?=$_GET['year'] == 'mar' ? 'selected' : ''?>>Март 2022</option>
		<option value="feb" style='font-weight:bold' <?=$_GET['year'] == 'feb' ? 'selected' : ''?> >Февраль 2022</option>
		<option value="jan" style='font-weight:bold' <?=$_GET['year'] == 'jan' ? 'selected' : ''?>>Январь 2022</option>
		
		
    </select></td>
	
  </table>
</form>


<?php



// Поиск по ID. Вывод формы для создания/редактирования
if (!empty($_GET['year'])) {

    global $DB;
	
	switch ($_GET['year']) {
						case 'jul':
							$datetime1=date("Y-m-d", strtotime("2022-06-30"));
							$datetime2=date("Y-m-d", strtotime("2022-08-01"));
							$day_count = 31;
							$month = ' июль';
							break;
						case 'jun':
							$datetime1=date("Y-m-d", strtotime("2022-05-31"));
							$datetime2=date("Y-m-d", strtotime("2022-07-01"));
							$day_count = 30;
							$month = ' июнь';
							break;
						case 'may':
							$datetime1=date("Y-m-d", strtotime("2022-04-30"));
							$datetime2=date("Y-m-d", strtotime("2022-06-01"));
							$day_count = 31;
							$month = ' май';
							break;
						case 'apr':
							$datetime1=date("Y-m-d", strtotime("2022-03-31"));
							$datetime2=date("Y-m-d", strtotime("2022-05-01"));
							$day_count = 30;
							$month = ' апр.';
							break;
						case 'mar':
							$datetime1=date("Y-m-d", strtotime("2022-02-29"));
							$datetime2=date("Y-m-d", strtotime("2022-04-01"));
							$day_count = 31;
							$month = ' мар.';
							break;
						case 'feb':
							$datetime1=date("Y-m-d", strtotime("2022-01-31"));
							$datetime2=date("Y-m-d", strtotime("2022-03-01"));
							$day_count = 29;
							$month = ' фев.';
							break;
						case 'jan':
							$datetime1=date("Y-m-d", strtotime("2021-12-31"));
							$datetime2=date("Y-m-d", strtotime("2022-02-01"));
							$day_count = 31;
							$month = ' янв.';
							break;
						case 'aug':
							$datetime1=date("Y-m-d", strtotime("2022-07-31"));
							$datetime2=date("Y-m-d", strtotime("2022-09-01"));
							$day_count = 31;
							$month = ' авг.';
							break;
						case 'sep':
							$datetime1=date("Y-m-d", strtotime("2022-08-31"));
							$datetime2=date("Y-m-d", strtotime("2022-10-01"));
							$day_count = 30;
							$month = ' сен.';
							break;
						
						
					}
    ?>	
	<br />
	<!--<p style="font-weight: bold;">Отчет за период с <?= $dt1?> по <?=$dt2 ?> (<?=$lab_name?>)</p>-->
	<br>
    <hr>
    
    <?php
	$i=0;
	$count_all=0;
	$deals = $DB->Query("SELECT * FROM `ba_tz`");//CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), Array('!TYPE_ID' => '3'));
	$deal_new=array();
	$deal_new_lfhi=array();
	$deal_new_lfmi=array();
	$deal_new_lsm=array();
	$deal_new_dsl=array();
	$deal_new_osk=array();
	
	$deal_probe_lfhi=array();
	$deal_probe_lfmi=array();
	$deal_probe_lsm=array();
	$deal_probe_dsl=array();
	$deal_probe_osk=array();
	
	$deal_isp_lfhi=array();
	$deal_isp_lfmi=array();
	$deal_isp_lsm=array();
	$deal_isp_dsl=array();
	$deal_isp_osk=array();
	
	$deal_fin_lfhi=array();
	$deal_fin_lfmi=array();
	$deal_fin_lsm=array();
	$deal_fin_dsl=array();
	$deal_fin_osk=array();
	
	$summ_oplata=0; $summ_oplata2=0;  $date_count = $datetime2-$datetime1;
	while($deal = $deals->Fetch())
	{
		$date_create = date("Y-m-d", strtotime($deal['DATE_CREATE']));
		$date_day = date("d", strtotime($deal['DATE_CREATE']));
		$date_day = (int)$date_day;
		
		// $userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
		// $user_dep = $userr['UF_DEPARTMENT'][0];
		$userr = explode(",", $deal['LABA_ID']);
		$user_dep = $userr[0];
		//$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $deal['ID'])->Fetch();
		if($deal['ID']) $res_akt = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $deal['ID'])->Fetch();
		
		$id_stage_new = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING');
		$id_stage_probe = array('FINAL_INVOICE');
		$id_stage_isp = array('1');
		$id_stage_fin = array('2', '4', 'WON');
				
		$date_probe=date("Y-m-d",strtotime($deal['DATE_ACT']));
		$date_prot=date("Y-m-d",strtotime($deal['DATE_P_CORR']));
		if($res_akt['ID']) $date_aktvr=date("Y-m-d",strtotime($res_akt['DATE']));
		
		if($date_create>$datetime1&&$date_create<$datetime2)
		{
			
				
				
				//if(in_array($deal['STAGE_ID'], $id_stage_new)&&!$deal['ACT_NUM']) {
					$deal_new[$date_day][]=$deal['ID'];
					switch ($user_dep) {
					case 54:
						$deal_new_lfhi[$date_day][] = $deal['ID'];
						break;
					case 57:
						$deal_new_lsm[$date_day][] = $deal['ID'];
						break;
					case 56:
						$deal_new_lfmi[$date_day][] = $deal['ID'];
						break;
					case 55:
						$deal_new_dsl[$date_day][] = $deal['ID'];
						break;
					case 58:
						$deal_new_osk[$date_day][] = $deal['ID'];
						break;
								
					}
				//}
				
				
				$id_stage_lose = array('5', '6', '7', '8', '9', '10', 'LOSE');
				if(in_array($deal['STAGE_ID'], $id_stage_lose))
				$deal_arr_lose[] = $deal['ID'];
			//}
		}
		if($deal['DATE_ACT']&&$date_probe>$datetime1&&$date_probe<$datetime2) {
					$date_act = date("d", strtotime($deal['DATE_ACT']));
					$date_act = (int)$date_act;
					$deal_probe[$date_act][]=$deal['ID'];
					switch ($user_dep) {
					case 54:
						$deal_probe_lfhi[$date_act][] = $deal['ID'];
						break;
					case 57:
						$deal_probe_lsm[$date_act][] = $deal['ID'];
						break;
					case 56:
						$deal_probe_lfmi[$date_act][] = $deal['ID'];
						break;
					case 55:
						$deal_probe_dsl[$date_act][] = $deal['ID'];
						break;
					case 58:
						$deal_probe_osk[$date_act][] = $deal['ID'];
						break;
								
					}
				}
				if($deal['DATE_P_CORR']&&$date_prot>$datetime1&&$date_prot<$datetime2) {
					$date_isp = date("d", strtotime($deal['DATE_P_CORR']));
					$date_isp = (int)$date_isp;
					$deal_isp[$date_isp][]=$deal['ID'];
					switch ($user_dep) {
					case 54:
						$deal_isp_lfhi[$date_isp][] = $deal['ID'];
						break;
					case 57:
						$deal_isp_lsm[$date_isp][] = $deal['ID'];
						break;
					case 56:
						$deal_isp_lfmi[$date_isp][] = $deal['ID'];
						break;
					case 55:
						$deal_isp_dsl[$date_isp][] = $deal['ID'];
						break;
					case 58:
						$deal_isp_osk[$date_isp][] = $deal['ID'];
						break;
								
					}
				}
				if($res_akt['ID']&&$date_aktvr>$datetime1&&$date_aktvr<$datetime2) {
					$date_fin = date("d", strtotime($res_akt['DATE']));
					$date_fin = (int)$date_fin;
					$deal_fin[$date_fin][]=$deal['ID'];
					switch ($user_dep) {
					case 54:
						$deal_fin_lfhi[$date_fin][] = $deal['ID'];
						break;
					case 57:
						$deal_fin_lsm[$date_fin][] = $deal['ID'];
						break;
					case 56:
						$deal_fin_lfmi[$date_fin][] = $deal['ID'];
						break;
					case 55:
						$deal_fin_dsl[$date_fin][] = $deal['ID'];
						break;
					case 58:
						$deal_fin_osk[$date_fin][] = $deal['ID'];
						break;
								
					}
				}	
			
		$date_act = date("Y-m-d",strtotime($deal['DATE_ACT']));
			
			
		if($date_act>=$datetime1 && $date_act<=$datetime2)
		{
			// $id_stage_act = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE');
			// if(in_array($deal['STAGE_ID'], $id_stage_act)&&$deal['ACT_NUM'])
		//	if ((!empty($_POST['lab_gost']) && ($user_dep == $id_dep))||$_POST['lab_gost']=='all'||!$_POST['lab_gost'])
		//	{
				$deal_arr_act[] = $deal['ID'];
		//	}
		}	
		
			$date_oplata = date("Y-m-d", strtotime($deal['DATE_OPLATA']));
			
		if($deal['PRICE'] && $deal['PRICE']<=$deal['OPLATA'] && $date_oplata>=$datetime1 && $date_oplata<=$datetime2)
		{
			//if ((!empty($_POST['lab_gost']) && ($user_dep == $id_dep))||$_POST['lab_gost']=='all'||!$_POST['lab_gost'])
			//{
				$deal_arr_nomoney[] = $deal['ID'];
				$summ_oplata = $summ_oplata+$deal['OPLATA'];
			//}
		}
		
		if($deal['PRICE'] && $deal['OPLATA'] && $deal['PRICE']>$deal['OPLATA'] && $date_oplata>=$datetime1 && $date_oplata<=$datetime2)
		{
			//if ((!empty($_POST['lab_gost']) && ($user_dep == $id_dep))||$_POST['lab_gost']=='all'||!$_POST['lab_gost'])
			//{
				$deal_arr_nomoney2[] = $deal['ID'];
				$summ_oplata2 = $summ_oplata2+$deal['OPLATA'];
			//}
		}
		
			$date_close = date("Y-m-d",strtotime($deal['CLOSEDATE']));
			
		if($date_close>=$datetime1 && $date_close<=$datetime2)
		{
			//if ((!empty($_POST['lab_gost']) && ($user_dep == $id_dep))||$_POST['lab_gost']=='all'||!$_POST['lab_gost'])
			//{
				$id_stage_won = array('WON');
				if(in_array($deal['STAGE_ID'], $id_stage_won))
				$deal_arr_won[] = $deal['ID'];
			//}
		}
		
			$id_stage_isp = array('1');
			$id_stage_fin_isp = array('2');
			$id_stage_actvr = array('4');
			
			
			
			$date_protocol = $deal['DATE_P_CORR'] ? $deal['DATE_P_CORR'] :$deal['DATE_P'];
			$date_p = date("Y-m-d", strtotime($date_protocol));	
//echo  "CORR=".$deal['DATE_P_CORR'].", P=".$deal['DATE_P']."<br>";
		
		if($date_p>=$datetime1 && $date_p<=$datetime2 &&$deal['DATE_P_CORR'] &&$deal['DATE_P'])
		{
			//if ((!empty($_POST['lab_gost']) && ($user_dep == $id_dep))||$_POST['lab_gost']=='all'||!$_POST['lab_gost'])
			//{
				$deal_arr_fin_isp[] = $deal['ID'];
			//}
		}
		$id_stage_protocol = array('2', '4', 'WON');
		if($date_p>=$datetime1 && $date_p<=$datetime2 && in_array($deal['STAGE_ID'], $id_stage_protocol)&&$deal['DATE_P_CORR'] &&$deal['DATE_P'])
		{
			//if ((!empty($_POST['lab_gost']) && ($user_dep == $id_dep))||$_POST['lab_gost']=='all'||!$_POST['lab_gost'])
			//{	
				$deal_arr_prot[] = $deal['ID']; 
			//}
		}
			
			// if(in_array($deal['STAGE_ID'], $id_stage_isp))
				// $deal_arr_isp[] = $deal['ID'];
			
			// if(in_array($deal['STAGE_ID'], $id_stage_actvr))
				// $deal_arr_actvr[] = $deal['ID'];
			
				
			
			
			
		}
	// echo "<pre />";
	// var_dump($deal_isp);
	//echo 'DATECOUNT='.$date_count;
	
	$count_all = count($deal_arr);
	$count_lab = count($deal_arr_lab);
	$count_new = count($deal_arr_new);
	$count_act = count($deal_arr_act);
	//$count_isp = count($deal_arr_isp);
	$count_fin_isp = count($deal_arr_fin_isp);
	//$count_actvr = count($deal_arr_actvr);
	$count_won = count($deal_arr_won);
	$count_lose = count($deal_arr_lose);
	$count_nomoney = count($deal_arr_nomoney);
	$count_nomoney2 = count($deal_arr_nomoney2);
	$count_prot = count($deal_arr_prot);
	
	$summ = $summ_oplata+$summ_oplata2;
	// echo "Новых заявок за период: ".$count_all."<br>";
	// echo " - из них закрытых неуспешно: ".$count_lose."<br><br>";
	// //echo "Заявок без проб (новых): ".$count_new."<br>";
	// echo "Составлено актов приемки проб: ".$count_act."<br><br>";
	// echo "Оплачены полностью за период: ".$count_nomoney." на общую сумму ".$summ_oplata." руб.<br><br>";
	// echo "Оплачены частично: ".$count_nomoney2." на общую сумму ".$summ_oplata2." руб.<br><br>";
	// echo "Всего оплачено на сумму ".$summ." руб.<br><br>";
	// echo "Сформировано протоколов: ".$count_fin_isp."<br><br>";
	// echo "Выдано протоколов (оплата получена и испытания завершены): ".$count_prot."<br><br>";
	// echo "Получено актов выполненных работ: ".$count_won."<br><br>";
	
	
	//echo "Всего заявок по выбранной лаборатории: ".$count_lab."<br><br>";
	
	
	
	// echo "Заявок на стадии испытаний: ".$count_isp."<br>";
	// echo "Заявок, испытания по которым завершены: ".$count_fin_isp."<br>";
	// echo "Заявок в стадии ожидания акта ВР: ".$count_actvr."<br>";
	
     }?>
	<table id="gost-table">
	<thead>
    <tr>
		<th></th>
		<th style="font-size:large">ИЦ</th><th></th><th></th><th></th>
		<th style="font-size:large">ЛФМИ</th><th></th><th></th><th></th>
		<th style="font-size:large">ЛФХИ</th><th></th><th></th><th></th>
		<th style="font-size:large">ЛСМ</th><th></th><th></th><th></th>
		<th style="font-size:large">ДСЛ</th><th></th><th></th><th></th>
		<th style="font-size:large">ОСК</th><th></th><th></th><th></th>
	</tr>
	<tr>
		<th></th>
		<th>Нов.</th><th>Пробы</th><th>Исп.</th><th>Заверш.</th>
		<th>Нов.</th><th>Пробы</th><th>Исп.</th><th>Заверш.</th>
		<th>Нов.</th><th>Пробы</th><th>Исп.</th><th>Заверш.</th>
		<th>Нов.</th><th>Пробы</th><th>Исп.</th><th>Заверш.</th>
		<th>Нов.</th><th>Пробы</th><th>Исп.</th><th>Заверш.</th>
		<th>Нов.</th><th>Пробы</th><th>Исп.</th><th>Заверш.</th>
		
	</tr>
	</thead>
	<tbody>
	<?php 
	$count_all=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
	for($d=1; $d<=$day_count; $d++)
	{
		echo "<tr>
		<td style='font-weight:bold'>".$d.$month."</td>
		<td>".nul_change(count($deal_new[$d]))."</td>
		<td>".nul_change(count($deal_probe[$d]))."</td>
		<td>".nul_change(count($deal_isp[$d]))."</td>
		<td>".nul_change(count($deal_fin[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_new_lfhi[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_probe_lfhi[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_isp_lfhi[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_fin_lfhi[$d]))."</td>
		<td>".nul_change(count($deal_new_lfmi[$d]))."</td>
		<td>".nul_change(count($deal_probe_lfmi[$d]))."</td>
		<td>".nul_change(count($deal_isp_lfmi[$d]))."</td>
		<td>".nul_change(count($deal_fin_lfmi[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_new_lsm[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_probe_lsm[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_isp_lsm[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_fin_lsm[$d]))."</td>
		<td>".nul_change(count($deal_new_dsl[$d]))."</td>
		<td>".nul_change(count($deal_probe_dsl[$d]))."</td>
		<td>".nul_change(count($deal_isp_dsl[$d]))."</td>
		<td>".nul_change(count($deal_fin_dsl[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_new_osk[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_probe_osk[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_isp_osk[$d]))."</td>
		<td style='background-color:#f2f2f2'>".nul_change(count($deal_fin_osk[$d]))."</td>
		
		</tr>";
		$count_all[0]=$count_all[0]+count($deal_new[$d]);
		$count_all[1]=$count_all[1]+count($deal_probe[$d]);
		$count_all[2]=$count_all[2]+count($deal_isp[$d]);
		$count_all[3]=$count_all[3]+count($deal_fin[$d]);
		$count_all[4]=$count_all[4]+count($deal_new_lfhi[$d]);
		$count_all[5]=$count_all[5]+count($deal_probe_lfhi[$d]);
		$count_all[6]=$count_all[6]+count($deal_isp_lfhi[$d]);
		$count_all[7]=$count_all[7]+count($deal_fin_lfhi[$d]);
		$count_all[8]=$count_all[8]+count($deal_new_lfmi[$d]);
		$count_all[9]=$count_all[9]+count($deal_probe_lfmi[$d]);
		$count_all[10]=$count_all[10]+count($deal_isp_lfmi[$d]);
		$count_all[11]=$count_all[11]+count($deal_fin_lfmi[$d]);
		$count_all[12]=$count_all[12]+count($deal_new_lsm[$d]);
		$count_all[13]=$count_all[13]+count($deal_probe_lsm[$d]);
		$count_all[14]=$count_all[14]+count($deal_isp_lsm[$d]);
		$count_all[15]=$count_all[15]+count($deal_fin_lsm[$d]);
		$count_all[16]=$count_all[16]+count($deal_new_dsl[$d]);
		$count_all[17]=$count_all[17]+count($deal_probe_dsl[$d]);
		$count_all[18]=$count_all[18]+count($deal_isp_dsl[$d]);
		$count_all[19]=$count_all[19]+count($deal_fin_dsl[$d]);
		$count_all[20]=$count_all[20]+count($deal_new_osk[$d]);
		$count_all[21]=$count_all[21]+count($deal_probe_osk[$d]);
		$count_all[22]=$count_all[22]+count($deal_isp_osk[$d]);
		$count_all[23]=$count_all[23]+count($deal_fin_osk[$d]);
		
		
	}
	echo "<tr>
		<td style='font-weight:bold'>ИТОГО</td>
		<td>".$count_all[0]."</td>
		<td>".$count_all[1]."</td>
		<td>".$count_all[2]."</td>
		<td>".$count_all[3]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[4]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[5]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[6]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[7]."</td>
		<td>".$count_all[8]."</td>
		<td>".$count_all[9]."</td>
		<td>".$count_all[10]."</td>
		<td>".$count_all[11]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[12]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[13]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[14]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[15]."</td>
		<td>".$count_all[16]."</td>
		<td>".$count_all[17]."</td>
		<td>".$count_all[18]."</td>
		<td>".$count_all[19]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[20]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[21]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[22]."</td>
		<td style='background-color:#f2f2f2'>".$count_all[23]."</td>
		
		</tr>";

	?>
	
    </tbody></table>
	

	<script>
        var s = document.getElementById("second");
        var ids = document.getElementById("ids_oborud");
        document.getElementById("first").onchange = function () {
            s.options[s.options.length] = new Option(this.options[this.selectedIndex].text, this.value);
            ids.value = JSON.stringify(Array.prototype.slice.call(JSON.parse(ids.value)).concat(this.value));
        }

        s.onchange = function () {
            arr = Array.prototype.slice.call(JSON.parse(ids.value));
            var index = arr.indexOf(this.value);
            if (index !== -1) arr.splice(index, 1);
            ids.value = JSON.stringify(arr);

            s.removeChild(s.options[s.selectedIndex]);

        }
    </script>    
<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"/>-->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>
	
    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
	<script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>
	
	<script>
        $(document).ready(function () {
            $('#gost-table').DataTable({
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
						//'colvis': "",
						'print': "",
						"excel": ""
					},
                    "aria": {
                        "sortAscending": ": активировать для сортировки столбца по возрастанию",
                        "sortDescending": ": активировать для сортировки столбца по убыванию"
                    }
                },
				"columnDefs": [
				{ "type": "natural-nohtml", "targets": 0},
				],
				"iDisplayLength": 50,
				"order": [[ 0, "asc" ]],
				"sorting":"false";
				//"colReorder": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'print' ]
				
            });
        });
		
		
    </script>    
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
