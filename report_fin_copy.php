<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 25.03.2019
 * Time: 12:35
 */
$start = microtime(true);
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("Отчет о результатах деятельности ИЦ"); 

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

function n_format_money(int $sum)
{
	
	return number_format($sum, 2, ',', '');
	
}


?>
<style>
.results_isp_filter, .dataTables_filter { display: none; }
.results_isp_length, .dataTables_length { display: none; }

</style>

<form id="form_report" method="post">
	<table>
	<tr>
   
	<td><input type="date" class="res" name="date1" value="2021-01-01"></td>
	<td><input type="date" class="res" name="date2" value="2021-12-31"></td>
	<td><input type="submit" value="Сформировать" style="margin:0px;"></td>
  </table>
</form>


<?php

if (!empty($_POST['date1'])&&!empty($_POST['date2'])) {
   

    global $DB;
	$date1= $_POST['date1'];
	$date2= $_POST['date2'];
	$dt1 = date("d.m.Y", strtotime($date1));
	$dt2 = date("d.m.Y", strtotime($date2));
	$datetime1=date("Y-m-d", strtotime($date1));
	$datetime2=date("Y-m-d", strtotime($date2));
	//$lab_name='';
    $lab_name="Все лаборатории";
	
	//echo $diff;
    /*if (!empty($_POST['lab_gost']) && ($_POST['lab_gost'] != 0)) {
		switch ($_POST['lab_gost']) {
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
		
        
    } else {
        $res = $DB->Query("SELECT * FROM `ba_gost`");
			$lab_name="Все лаборатории";
    }*/


    
    ?>	
	<br />
	<p style="font-weight: bold;">Отчет за период с <?= $dt1?> по <?=$dt2 ?> (<?=$lab_name?>)</p>
	<br>
    <hr>
    
    <?php
	$i=0;
	$count_all=0;
	$summ=0;
	$summ_oplata=0; 
	$summ_oplata_new=0; 
	$summ_oplata2=0; 
	$summ_oplata_prot=0; 
	$summ_oplata2_prot=0;
	$summ_oplata_prot_opl=0;
	$summ_oplata_lfmi=0; 
	$summ_oplata2_lfmi=0;
	$summ_oplata_lfhi=0; 
	$summ_oplata2_lfhi=0;
	$summ_oplata_lsm=0; 
	$summ_oplata2_lsm=0;
	$summ_oplata_dsl=0; 
	$summ_oplata2_dsl=0;
	$summ_oplata_osk=0; 
	$summ_oplata2_osk=0;
	
	$summ_oplata_lfmi_new=0; 
	$summ_oplata_lfhi_new=0; 
	$summ_oplata_lsm_new=0; 
	$summ_oplata_dsl_new=0; 
	$summ_oplata_osk_new=0; 
		
	$summ_oplata_lfmi_prot=0; 
	$summ_oplata2_lfmi_prot=0;
	$summ_oplata_lfhi_prot=0; 
	$summ_oplata2_lfhi_prot=0;
	$summ_oplata_lsm_prot=0; 
	$summ_oplata2_lsm_prot=0;
	$summ_oplata_dsl_prot=0; 
	$summ_oplata2_dsl_prot=0;
	$summ_oplata_osk_prot=0; 
	$summ_oplata2_osk_prot=0;
	$summ_oplata_lfmi_prot_opl=0;
	$summ_oplata_lfhi_prot_opl=0;
	$summ_oplata_dsl_prot_opl=0;
	$summ_oplata_lsm_prot_opl=0;
	$summ_oplata_osk_prot_opl=0;
	
	$deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), Array('!TYPE_ID' => '3'));
	
	$deal_arr=array();
	$deal_new=0;
	$deal_probe=0;
	$deal_isp=0;
	$deal_fin=0;
	$deal_inv=0;
	//$deal_inv_money=array();
	//$deal_inv_money2=array();
	$deal_lose=array();
	$deal_new_lose=0;
	$deal_won=0;
	$deal_money=0;
	$deal_money_new=0;
	$deal_money2=0;

	$deal_new_lfhi = 0;
	$deal_new_lfmi=0;
	$deal_new_lsm = 0;
	$deal_new_dsl=0;
	$deal_new_osk = 0;

    $deal_new_won=0;
	$deal_new_won_lfhi=0;
	$deal_new_won_lfmi=0;
	$deal_new_won_lsm=0;
	$deal_new_won_dsl=0;
	$deal_new_won_osk=0;
	
	$deal_probe_lfhi=0;
	$deal_probe_lfmi=0;
	$deal_probe_lsm=0;
	$deal_probe_dsl=0;
	$deal_probe_osk=0;
	
	$deal_isp_lfhi=0;
	$deal_isp_lfmi=0;
	$deal_isp_lsm=0;
	$deal_isp_dsl=0;
	$deal_isp_osk=0;
	
	$deal_fin_lfhi=0;
	$deal_fin_lfmi=0;
	$deal_fin_lsm=0;
	$deal_fin_dsl=0;
	$deal_fin_osk=0;
	
	$deal_won_lfhi=0;
	$deal_won_lfmi=0;
	$deal_won_lsm=0;
	$deal_won_dsl=0;
	$deal_won_osk=0;
	
	$deal_money_lfhi=0;
	$deal_money_lfmi=0;
	$deal_money_lsm=0;
	$deal_money_dsl=0;
	$deal_money_osk=0;
	
	$deal_money_lfhi_new=0;
	$deal_money_lfmi_new=0;
	$deal_money_lsm_new=0;
	$deal_money_dsl_new=0;
	$deal_money_osk_new=0;
	
	$deal_money2_lfhi=0;
	$deal_money2_lfmi=0;
	$deal_money2_lsm=0;
	$deal_money2_dsl=0;
	$deal_money2_osk=0;
	
	$deal_inv_lfhi=0;
	$deal_inv_lfmi=0;
	$deal_inv_lsm=0;
	$deal_inv_dsl=0;
	$deal_inv_osk=0;
	$stageArrLose = ['LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13'];
	
	// $deal_inv_money_lfhi=array();
	// $deal_inv_money_lfmi=array();
	// $deal_inv_money_lsm=array();
	// $deal_inv_money_dsl=array();
	// $deal_inv_money_osk=array();
	
	// $deal_inv_money2_lfhi=array();
	// $deal_inv_money2_lfmi=array();
	// $deal_inv_money2_lsm=array();
	// $deal_inv_money2_dsl=array();
	// $deal_inv_money2_osk=array();
	
	while($deal = $deals->Fetch())
	{
		
		$date_act = '';
		$date_oplata = '';
		$date_protocol = '';
		$date_p = '';	
		//$res_kp = [];
		//$res_dog = [];
		//$res_tz_doc = [];
		$res_acc = [];
		//$res_akt = [];
		//$res_akt_vr = [];
		$date_inv = '';		
		
		$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $deal['ID'])->Fetch();

		$date_create = date("Y-m-d", strtotime($deal['DATE_CREATE']));
		$date_close = date("Y-m-d",strtotime($deal['CLOSEDATE']));
		
		if($res_tz['ID'])
		{
			$date_act = date("Y-m-d",strtotime($res_tz['DATE_ACT']));
			$date_oplata = date("Y-m-d", strtotime($res_tz['DATE_OPLATA']));
			$date_protocol = $res_tz['DATE_P_CORR'] ? $res_tz['DATE_P_CORR'] :$res_tz['DATE_P'];
			$date_p = date("Y-m-d", strtotime($date_protocol));	
			
							//$res_kp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							//$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							//$res_tz_doc = $DB->Query("SELECT * FROM `TZ_DOC` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							//$res_akt = $DB->Query("SELECT * FROM `ACT` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
							//$res_akt_vr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $res_tz['ID'])->Fetch();
			$date_inv = date("Y-m-d",strtotime($res_acc['DATE']));				
		
		}
		
		$userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
		$user_dep = $userr['UF_DEPARTMENT'][0];
		

//СЧИТАЕМ НОВЫЕ ЗАЯВКИ ЗА ПЕРИОД----------------------------------------------------------------------------------------
		
		if($date_create>=$datetime1&&$date_create<=$datetime2)
		{

			$deal_new++;
				
			switch ($user_dep) {
				case 54:
					$deal_new_lfhi++;
					break;
				case 57:
					$deal_new_lsm++;
					break;
				case 56:
					$deal_new_lfmi++;
					break;
				case 55:
					$deal_new_dsl++;
					break;
				case 58:
					$deal_new_osk++;
					break;
			}
			
			$id_stage_lose = array('5', '6', '7', '8', '9', '10', '11', 'LOSE');
			
			if(in_array($deal['STAGE_ID'], $id_stage_lose))
				
				$deal_new_lose++;
			
			if($deal['STAGE_ID']=='WON')
			{
				$deal_new_won++;

				switch ($user_dep) {
				case 54:
					$deal_new_won_lfhi++;
					break;
				case 57:
					$deal_new_won_lsm++;
					break;
				case 56:
					$deal_new_won_lfmi++;
					break;
				case 55:
					$deal_new_won_dsl++;
					break;
				case 58:
					$deal_new_won_osk++;
					break;
				}
				
			}
			
			$arrLoseStage = ['LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13'];
		//echo $deal['TITLE']."-".$date_oplata."-".$datetime1."<br>";
			if($res_tz['PRICE'] && $res_tz['PRICE']<=$res_tz['OPLATA'] && $date_oplata>=$datetime1 && $date_oplata<=$datetime2 && !in_array($deal['STAGE_ID'], $arrLoseStage))
			{
				
				$resDeals = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=".$deal['ID'])->Fetch();
				
				
				
				if($resDeals['ID'])
					$resContracts = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=".$resDeals['ID_CONTRACT'])->Fetch();
				
				if(!$resContracts['LONGTERM']||!$resContracts['ID'])
				{	
				
					$deal_money_new++;
					
					
					
					switch ($user_dep) {
						case 54:
							$deal_money_lfhi_new++;
							$summ_oplata_lfhi_new = $summ_oplata_lfhi_new+$res_tz['OPLATA'];
							break;
						case 57:
							$deal_money_lsm_new++;
							$summ_oplata_lsm_new = $summ_oplata_lsm_new+$res_tz['OPLATA'];
							break;
						case 56:
							$deal_money_lfmi_new++;
							$summ_oplata_lfmi_new = $summ_oplata_lfmi_new+$res_tz['OPLATA'];
							break;
						case 55:
							$deal_money_dsl_new++;
							$summ_oplata_dsl_new = $summ_oplata_dsl_new+$res_tz['OPLATA'];
							break;
						case 58:
							$deal_money_osk_new++;
							$summ_oplata_osk_new = $summ_oplata_osk_new+$res_tz['OPLATA'];
							break;
					}
									
					$summ_oplata_new = $summ_oplata_new+$res_tz['OPLATA'];
					
					
				}
			}
			
		}
		
//НЕУСПЕШНО И УСПЕШНО ЗАКРЫТЫЕ ЗА ПЕРИОД-----------------------------------------------------------------------------------------
				
		if($date_close>=$datetime1 && $date_close<=$datetime2 && $deal['ASSIGNED_BY_ID']!='1')
		{
			
			$id_stage_lose = array('5', '6', '7', '8', '9', '10', '11', 'LOSE');
			
			if(in_array($deal['STAGE_ID'], $id_stage_lose))
				
				$deal_lose++;
			
			if($deal['STAGE_ID']=='WON')
			{
				$deal_won++;
				
				switch ($user_dep) {
				case 54:
					$deal_won_lfhi++;
					break;
				case 57:
					$deal_won_lsm++;
					break;
				case 56:
					$deal_won_lfmi++;
					break;
				case 55:
					$deal_won_dsl++;
					break;
				case 58:
					$deal_won_osk++;
					break;
				}
				
			}
		}
			
//АКТЫ ПРИЕМКИ ПРОБ-----------------------------------------------------------------------------------------		
					
		if($res_tz['DATE_ACT'] && $date_act>=$datetime1 && $date_act<=$datetime2)
		{
			$deal_probe++;
			
			switch ($user_dep) {
				case 54:
					$deal_probe_lfhi++;
					break;
				case 57:
					$deal_probe_lsm++;
					break;
				case 56:
					$deal_probe_lfmi++;
					break;
				case 55:
					$deal_probe_dsl++;
					break;
				case 58:
					$deal_probe_osk++;
					break;
			}
		
		}

//ПOЛНОСТЬЮ ОПЛАЧЕННЫЕ ЗАЯВКИ И СУММА ОПЛАТ--------------------------------------------------------------------------------------------		
		$arrLoseStage = ['LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13'];
		//echo $deal['TITLE']."-".$date_oplata."-".$datetime1."<br>";
		if($res_tz['PRICE'] && $res_tz['PRICE']<=$res_tz['OPLATA'] && $date_oplata>=$datetime1 && $date_oplata<=$datetime2 && !in_array($deal['STAGE_ID'], $arrLoseStage))
		{
			
			$resDeals = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=".$deal['ID'])->Fetch();
			
			
			
			if($resDeals['ID'])
				$resContracts = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=".$resDeals['ID_CONTRACT'])->Fetch();
			
			if(!$resContracts['LONGTERM']||!$resContracts['ID'])
			{	
			
				$deal_money++;
				
				
				
				switch ($user_dep) {
					case 54:
						$deal_money_lfhi++;
						$summ_oplata_lfhi = $summ_oplata_lfhi+$res_tz['OPLATA'];
						break;
					case 57:
						$deal_money_lsm++;
						$summ_oplata_lsm = $summ_oplata_lsm+$res_tz['OPLATA'];
						break;
					case 56:
						$deal_money_lfmi++;
						$summ_oplata_lfmi = $summ_oplata_lfmi+$res_tz['OPLATA'];
						break;
					case 55:
						$deal_money_dsl++;
						$summ_oplata_dsl = $summ_oplata_dsl+$res_tz['OPLATA'];
						break;
					case 58:
						$deal_money_osk++;
						$summ_oplata_osk = $summ_oplata_osk+$res_tz['OPLATA'];
						break;
				}
								
				$summ_oplata = $summ_oplata+$res_tz['OPLATA'];
				
				
			}
			
		}

//ЧАСТИЧНО ОПЛАЧЕННЫЕ ЗАЯВКИ И СУММА ОПЛАТ--------------------------------------------------------------------------------------------
		
		if($res_tz['PRICE'] && $res_tz['OPLATA'] && $res_tz['PRICE']>$res_tz['OPLATA'] && $date_oplata>=$datetime1 && $date_oplata<=$datetime2)
		{
			$deal_money2++;
			
			switch ($user_dep) {
				case 54:
					$deal_money2_lfhi++;
					$summ_oplata2_lfhi = $summ_oplata2_lfhi+$res_tz['OPLATA'];
					break;
				case 57:
					$deal_money2_lsm++;
					$summ_oplata2_lsm = $summ_oplata2_lsm+$res_tz['OPLATA'];
					break;
				case 56:
					$deal_money2_lfmi++;
					$summ_oplata2_lfmi = $summ_oplata2_lfmi+$res_tz['OPLATA'];
					break;
				case 55:
					$deal_money2_dsl++;
					$summ_oplata2_dsl = $summ_oplata2_dsl+$res_tz['OPLATA'];
					break;
				case 58:
					$deal_money2_osk++;
					$summ_oplata2_osk = $summ_oplata2_osk+$res_tz['OPLATA'];
					break;
			}
							
			$summ_oplata2 = $summ_oplata2+$res_tz['OPLATA'];
			//echo $deal['ID']. "-". $deal['TITLE']." - ".$res_tz['OPLATA']." - ".$res_tz['DATE_OPLATA']." - ".$deal['COMPANY_TITLE']." - ".$summ_oplata."<br>";
		}

		
		
//СФОРМИРОВАНО ПРОТОКОЛОВ--------------------------------------------------------------------------------------------		
				
		if($date_p>=$datetime1 && $date_p<=$datetime2 && ($res_tz['DATE_P_CORR'] || $res_tz['DATE_P']))// && !in_array($deal['STAGE_ID'], $stageArrLose))
		{
			$deal_isp++;
			
			switch ($user_dep) {
				case 54:
					$deal_isp_lfhi++;
					$summ_oplata_lfhi_prot = $summ_oplata_lfhi_prot+$res_tz['PRICE'];
					break;
				case 57:
					$deal_isp_lsm++;
					$summ_oplata_lsm_prot = $summ_oplata_lsm_prot+$res_tz['PRICE'];
					break;
				case 56:
					$deal_isp_lfmi++;
					$summ_oplata_lfmi_prot = $summ_oplata_lfmi_prot+$res_tz['PRICE'];
					break;
				case 55:
					$deal_isp_dsl++;
					$summ_oplata_dsl_prot = $summ_oplata_dsl_prot+$res_tz['PRICE'];
					break;
				case 58:
					$deal_isp_osk++;
					$summ_oplata_osk_prot = $summ_oplata_osk_prot+$res_tz['PRICE'];
					break;
			}
			
			$summ_oplata_prot = $summ_oplata_prot+$res_tz['PRICE'];
		}
		
//ВЫДАНО ПРОТОКОЛОВ----------------------------------------------------------------------------------------------------------
		
		$id_stage_protocol = array('2', '4', 'WON');
		
		if($date_p>=$datetime1 && $date_p<=$datetime2 && in_array($deal['STAGE_ID'], $id_stage_protocol) && ($res_tz['DATE_P_CORR'] || $res_tz['DATE_P']))
		{
			$deal_fin++;
			
			switch ($user_dep) {
				case 54:
					$deal_fin_lfhi++;
					$summ_oplata2_lfhi_prot = $summ_oplata2_lfhi_prot+$res_tz['PRICE'];
					break;
				case 57:
					$deal_fin_lsm++;
					$summ_oplata2_lsm_prot = $summ_oplata2_lsm_prot+$res_tz['PRICE'];
					break;
				case 56:
					$deal_fin_lfmi++;
					$summ_oplata2_lfmi_prot = $summ_oplata2_lfmi_prot+$res_tz['PRICE'];
					break;
				case 55:
					$deal_fin_dsl++;
					$summ_oplata2_dsl_prot = $summ_oplata2_dsl_prot+$res_tz['PRICE'];
					break;
				case 58:
					$deal_fin_osk++;
					$summ_oplata2_osk_prot = $summ_oplata2_osk_prot+$res_tz['PRICE'];
					break;
			}
			
			$summ_oplata2_prot = $summ_oplata2_prot+$res_tz['PRICE'];
			if(!$res_tz['OPLATA']) $summ_oplata_prot_opl=$summ_oplata_prot_opl+$res_tz['PRICE'];
		}
		
//СФОРМИРОВАНО СЧЕТОВ----------------------------------------------------------------------------------------------------------
		
		//$id_stage_protocol = array('2', '4', 'WON');
		
		if($res_acc['DATE'] && $date_inv>=$datetime1 && $date_inv<=$datetime2)
		{
			$deal_inv++;
			
			switch ($user_dep) {
				case 54:
					$deal_inv_lfhi++;
					break;
				case 57:
					$deal_inv_lsm++;
					break;
				case 56:
					$deal_inv_lfmi++;
					break;
				case 55:
					$deal_inv_dsl++;
					break;
				case 58:
					$deal_inv_osk++;
					break;
			}
		}
			
	}//while по всем сделкам
	
	//СУММА ВСЕХ ОПЛАТ--------------------------------------------------------------------------------------------		
	
	$resPayments = $DB->Query("SELECT * FROM `PAYMENTS_TO_CONTRACTS` WHERE `PAY_DATE`>='".$datetime1."' AND `PAY_DATE`<='".$datetime2."'");
	$summ_oplata_all = $summ_oplata;
	while($resPay = $resPayments->Fetch())
	{
		$summ_oplata_all = $summ_oplata_all + $resPay['PAY_SUMM'];
		//$resContracts = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=".$resPay['ID_CONTRACT'])->Fetch();
		//echo $resPay['PAY_SUMM']." - ".$resPay['ID_CONTRACT']." - ".$resContracts['NUMBER']." - ".$summ_oplata."<br>";
	}
	
	
	$summ = $summ_oplata+$summ_oplata2;	
	$summ_all = $summ_oplata_all+$summ_oplata2;	
	$summ_lfmi = $summ_oplata_lfmi+$summ_oplata2_lfmi;
	$summ_lfhi = $summ_oplata_lfhi+$summ_oplata2_lfhi;
	$summ_lsm = $summ_oplata_lsm+$summ_oplata2_lsm;
	$summ_dsl = $summ_oplata_dsl+$summ_oplata2_dsl;
	$summ_osk = $summ_oplata_osk+$summ_oplata2_osk;
	
	$c_deal_arr = nul_change(count($deal_arr));
	$c_deal_new = nul_change($deal_new);
	$c_deal_isp = nul_change($deal_isp);
	$c_deal_fin = nul_change($deal_fin);
	$c_deal_lose= nul_change(count($deal_lose));
	$c_deal_new_lose= nul_change($deal_new_lose);
	$c_deal_won = nul_change($deal_won);
	$c_deal_new_won = nul_change($deal_new_won);
	$c_deal_inv = nul_change($deal_inv);
	$c_deal_probe = nul_change($deal_probe);
	$c_deal_money = nul_change($deal_money);
	$c_deal_money_new = nul_change($deal_money_new);
	$c_deal_money2 = nul_change($deal_money2);

	$c_deal_new_lfhi = nul_change($deal_new_lfhi);
	$c_deal_new_lfmi = nul_change($deal_new_lfmi);
	$c_deal_new_lsm = nul_change($deal_new_lsm);
	$c_deal_new_dsl = nul_change($deal_new_dsl);
	$c_deal_new_osk = nul_change($deal_new_osk);

	$c_deal_new_won_lfhi = nul_change($deal_new_won_lfhi);
	$c_deal_new_won_lfmi = nul_change($deal_new_won_lfmi);
	$c_deal_new_won_lsm = nul_change($deal_new_won_lsm);
	$c_deal_new_won_dsl = nul_change($deal_new_won_dsl);
	$c_deal_new_won_osk = nul_change($deal_new_won_osk);
	
	$c_deal_probe_lfhi = nul_change($deal_probe_lfhi);
	$c_deal_probe_lfmi = nul_change($deal_probe_lfmi);
	$c_deal_probe_lsm = nul_change($deal_probe_lsm);
	$c_deal_probe_dsl = nul_change($deal_probe_dsl);
	$c_deal_probe_osk = nul_change($deal_probe_osk);
	
	$c_deal_isp_lfhi = nul_change($deal_isp_lfhi);
	$c_deal_isp_lfmi = nul_change($deal_isp_lfmi);
	$c_deal_isp_lsm = nul_change($deal_isp_lsm);
	$c_deal_isp_dsl = nul_change($deal_isp_dsl);
	$c_deal_isp_osk = nul_change($deal_isp_osk);
	
	$c_deal_fin_lfhi = nul_change($deal_fin_lfhi);
	$c_deal_fin_lfmi = nul_change($deal_fin_lfmi);
	$c_deal_fin_lsm = nul_change($deal_fin_lsm);
	$c_deal_fin_dsl = nul_change($deal_fin_dsl);
	$c_deal_fin_osk = nul_change($deal_fin_osk);
	
	$c_deal_won_lfhi = nul_change($deal_won_lfhi);
	$c_deal_won_lfmi = nul_change($deal_won_lfmi);
	$c_deal_won_lsm = nul_change($deal_won_lsm);
	$c_deal_won_dsl = nul_change($deal_won_dsl);
	$c_deal_won_osk = nul_change($deal_won_osk);
	
	$c_deal_inv_lfhi = nul_change($deal_inv_lfhi);
	$c_deal_inv_lfmi = nul_change($deal_inv_lfmi);
	$c_deal_inv_lsm = nul_change($deal_inv_lsm);
	$c_deal_inv_dsl = nul_change($deal_inv_dsl);
	$c_deal_inv_osk = nul_change($deal_inv_osk);
	
	$c_deal_money_lfhi = nul_change($deal_money_lfhi);
	$c_deal_money_lfmi = nul_change($deal_money_lfmi);
	$c_deal_money_lsm = nul_change($deal_money_lsm);
	$c_deal_money_dsl = nul_change($deal_money_dsl);
	$c_deal_money_osk= nul_change($deal_money_osk);
	
	$c_deal_money_lfhi_new = nul_change($deal_money_lfhi_new);
	$c_deal_money_lfmi_new = nul_change($deal_money_lfmi_new);
	$c_deal_money_lsm_new = nul_change($deal_money_lsm_new);
	$c_deal_money_dsl_new = nul_change($deal_money_dsl_new);
	$c_deal_money_osk_new = nul_change($deal_money_osk_new);
	
	$c_deal_money2_lfhi = nul_change($deal_money2_lfhi);
	$c_deal_money2_lfmi = nul_change($deal_money2_lfmi);
	$c_deal_money2_lsm = nul_change($deal_money2_lsm);
	$c_deal_money2_dsl = nul_change($deal_money2_dsl);
	$c_deal_money2_osk = nul_change($deal_money2_osk);
    echo 'Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.';
    ?>
	<table id="gost-table">
	<thead>
    <tr>
		<th>Показатель (период <?= $dt1?> по <?=$dt2 ?>)<th>
		<th>Всего</th>
		<th>ЛФМИ</th>
		<th>ЛФХИ</th>
		<th>ЛСМ</th>
		<th>ДСЛ</th>
		<th>ОСК</th>
	</thead>
	<tbody>
	<tr><td><b>Новые заявки</b></td><td></td><td><?= $c_deal_new ?></td><td><?= $c_deal_new_lfmi ?></td><td><?= $c_deal_new_lfhi ?></td><td><?= $c_deal_new_lsm ?></td><td><?= $c_deal_new_dsl ?></td><td><?= $c_deal_new_osk ?></td></tr>
	<tr><td>-- из них закрыты как несупешные</td><td></td><td><?= $c_deal_new_lose ?></td><td>--</td><td>--</td><td>--</td><td>--</td><td>--</td></tr>
	<tr><td>-- из них закрыты как успешные</td><td></td><td><?= $c_deal_new_won ?></td><td><?= $c_deal_new_won_lfmi ?></td><td><?= $c_deal_new_won_lfhi ?></td><td><?= $c_deal_new_won_lsm ?></td><td><?= $c_deal_new_won_dsl ?></td><td><?= $c_deal_new_won_osk ?></td></tr>
	<tr><td>-- из них оплачено полностью</td><td></td><td><?= $c_deal_money_new ?></td><td><?= $c_deal_money_lfmi_new ?></td><td><?= $c_deal_money_lfhi_new ?></td><td><?= $c_deal_money_lsm_new ?></td><td><?= $c_deal_money_dsl_new ?></td><td><?= $c_deal_money_osk_new ?></td></tr>
	<tr><td>-- из них оплачено полностью на сумму </td><td></td><td><?= n_format_money($summ_oplata_new) ?></td><td><?= n_format_money($summ_oplata_lfmi_new) ?></td><td><?= n_format_money($summ_oplata_lfhi_new) ?></td><td><?= n_format_money($summ_oplata_lsm_new) ?></td><td><?= n_format_money($summ_oplata_dsl_new) ?></td><td><?= n_format_money($summ_oplata_osk_new) ?></td></tr>
	<tr><td><b>Закрыты как несупешные</b></td><td></td><td><?= $c_deal_lose ?></td><td>--</td><td>--</td><td>--</td><td>--</td><td>--</td></tr>
	<tr><td><b>Закрыты как успешные</b></td><td></td><td><?= $c_deal_won ?></td><td><?= $c_deal_won_lfmi ?></td><td><?= $c_deal_won_lfhi ?></td><td><?= $c_deal_won_lsm ?></td><td><?= $c_deal_won_dsl ?></td><td><?= $c_deal_won_osk ?></td></tr>
	<tr><td><b>Заявки с актами ПП</b></td><td></td><td><?= $c_deal_probe ?></td><td><?= $c_deal_probe_lfmi ?></td><td><?= $c_deal_probe_lfhi ?></td><td><?= $c_deal_probe_lsm ?></td><td><?= $c_deal_probe_dsl ?></td><td><?= $c_deal_probe_osk ?></td></tr>
	<tr><td><b>Оплачено полностью</b></td><td></td><td><?= $c_deal_money ?></td><td><?= $c_deal_money_lfmi ?></td><td><?= $c_deal_money_lfhi ?></td><td><?= $c_deal_money_lsm ?></td><td><?= $c_deal_money_dsl ?></td><td><?= $c_deal_money_osk ?></td></tr>
	<tr><td>-- на сумму </td><td></td><td><?= n_format_money($summ_oplata) ?></td><td><?= n_format_money($summ_oplata_lfmi) ?></td><td><?= n_format_money($summ_oplata_lfhi) ?></td><td><?= n_format_money($summ_oplata_lsm) ?></td><td><?= n_format_money($summ_oplata_dsl) ?></td><td><?= n_format_money($summ_oplata_osk) ?></td></tr>
	<tr><td>-- ВСЕГО с учетом долгосрочных договоров</td><td></td><td><?= n_format_money($summ_oplata_all) ?></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td><b>Оплачено частично</b></td><td></td><td><?= $c_deal_money2 ?></td><td><?= $c_deal_money2_lfmi ?></td><td><?= $c_deal_money2_lfhi ?></td><td><?= $c_deal_money2_lsm ?></td><td><?= $c_deal_money2_dsl ?></td><td><?= $c_deal_money2_osk ?></td></tr>
	<tr><td>-- на сумму </td><td></td><td><?= n_format_money($summ_oplata2) ?></td><td><?= n_format_money($summ_oplata2_lfmi) ?></td><td><?= n_format_money($summ_oplata2_lfhi) ?></td><td><?= n_format_money($summ_oplata2_lsm) ?></td><td><?= n_format_money($summ_oplata2_dsl) ?></td><td><?= n_format_money($summ_oplata2_osk) ?></td></tr>
	<tr><td><b>Всего оплачено</b></td><td></td><td><?= $c_deal_money+$c_deal_money2 ?></td><td><?= $c_deal_money_lfmi+$c_deal_money2_lfmi ?></td><td><?= $c_deal_money_lfhi+$c_deal_money2_lfhi ?></td><td><?= $c_deal_money_lsm+$c_deal_money2_lsm ?></td><td><?= $c_deal_money_dsl+$c_deal_money2_dsl ?></td><td><?= $c_deal_money_osk+$c_deal_money2_osk ?></td></tr>
	<tr><td>-- на сумму </td><td></td><td><?= n_format_money($summ) ?></td><td><?= n_format_money($summ_lfmi) ?></td><td><?= n_format_money($summ_lfhi) ?></td><td><?= n_format_money($summ_lsm) ?></td><td><?= n_format_money($summ_dsl) ?></td><td><?= n_format_money($summ_osk) ?></td></tr>
	<tr><td>-- ВСЕГО с учетом долгосрочных договоров</td><td></td><td><?= n_format_money($summ_all) ?></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td><b>Сформировано протоколов</b></td><td></td><td><?= $c_deal_isp ?></td><td><?= $c_deal_isp_lfmi ?></td><td><?= $c_deal_isp_lfhi ?></td><td><?= $c_deal_isp_lsm ?></td><td><?= $c_deal_isp_dsl ?></td><td><?= $c_deal_isp_osk ?></td></tr>
	<tr><td>-- на сумму </td><td></td><td><?= n_format_money($summ_oplata_prot) ?></td><td><?= n_format_money($summ_oplata_lfmi_prot) ?></td><td><?= n_format_money($summ_oplata_lfhi_prot) ?></td><td><?= n_format_money($summ_oplata_lsm_prot) ?></td><td><?= n_format_money($summ_oplata_dsl_prot) ?></td><td><?= n_format_money($summ_oplata_osk_prot) ?></td></tr>
	<tr><td><b>Выдано протоколов</b></td><td></td><td><?= $c_deal_fin ?></td><td><?= $c_deal_fin_lfmi ?></td><td><?= $c_deal_fin_lfhi ?></td><td><?= $c_deal_fin_lsm ?></td><td><?= $c_deal_fin_dsl ?></td><td><?= $c_deal_fin_osk ?></td></tr>
	<tr><td>-- на сумму </td><td></td><td><?= n_format_money($summ_oplata2_prot) ?></td><td><?= n_format_money($summ_oplata2_lfmi_prot) ?></td><td><?= n_format_money($summ_oplata2_lfhi_prot) ?></td><td><?= n_format_money($summ_oplata2_lsm_prot) ?></td><td><?= n_format_money($summ_oplata2_dsl_prot) ?></td><td><?= n_format_money($summ_oplata2_osk_prot) ?></td></tr>
	<tr><td>-- из них не оплачено на сумму</td><td></td><td><?= n_format_money($summ_oplata_prot_opl) ?></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td><b>Сформировано счетов</b></td><td></td><td><?= $c_deal_inv ?></td><td><?= $c_deal_inv_lfmi ?></td><td><?= $c_deal_inv_lfhi ?></td><td><?= $c_deal_inv_lsm ?></td><td><?= $c_deal_inv_dsl ?></td><td><?= $c_deal_inv_osk ?></td></tr>
	</tbody></table>
<? }//общий IF ?>	

	
	<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	
	
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
						'colvis': "",
						'print': "",
						"excel": ""
					},
                    "aria": {
                        "sortAscending": ": активировать для сортировки столбца по возрастанию",
                        "sortDescending": ": активировать для сортировки столбца по убыванию"
                    }
                },
				"columns": [
					{"orderable": false },
					{"orderable": false },
					{"orderable": false },
					{"orderable": false },
					{"orderable": false },
					{"orderable": false },
					{"orderable": false },
					{"orderable": false }
					],
				"order": [[ 1, "asc" ]],
				"iDisplayLength": 50,
				//"sort":"false",
				"paginate": false,
				"dom": 'Blfrtip',
				"info": false,
				"buttons": [ 'copy', 'excel', 'print' ]
				
            });
        });
		
		
    </script>    
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>