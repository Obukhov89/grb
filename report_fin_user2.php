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

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

function echoTD ($arr, $arrayDep)
{
	$filter = Array();
	//$arrayDep = array('54','55','56','57','58');
	$usersAll = CUser::GetList(($by='LAST_NAME'), ($order='asc'), $filter);
	while($user = $usersAll->Fetch())
		{
			$userData = CUser::GetByID($user['ID'])->Fetch();
			$userDep = $userData['UF_DEPARTMENT'][0];
			if(in_array($userDep, $arrayDep)) $usersIdDep[$user['ID']] = $userDep;
				
		}
		
	asort($usersIdDep);

	foreach($usersIdDep as $userId => $userDep)
	{
		if($userDep != $dep) 
			echo "<td style='border-left: 1px solid #ccc'>".count($arr[$userId])."</td>";
		else
			echo "<td>".count($arr[$userId])."</td>";
		$dep = $userDep;	
	}
	
	
}
//$emplLastName = "Овчинникова";
//$letter = substr($emplLastName, 3, 1);
$glasArr = ['а', 'е', 'и', 'о', 'у', 'ю', 'я', 'э'];
//echo $letter. "    ".in_array($letter, $glasArr);

?>
<style>
.results_isp_filter, .dataTables_filter { display: none; }
.results_isp_length, .dataTables_length { display: none; }
#gost-table th { background-image: none; }
.sorting_1 { display: none;}
.sorting_asc { display: none;}


</style>

<form id="form_report" method="post">
	<table>
	<tr>
   
	<td><input type="date" class="res" name="date1" value="<?= $_POST['date1'] ? $_POST['date1'] : '2020-01-01' ?>"></td>
	<td><input type="date" class="res" name="date2" value="<?= $_POST['date1'] ? $_POST['date2'] : '2020-12-31' ?>"></td>
	<td>
	<select class="obl" name="lab_gost" id="lab" style="float: right;">
        <option value="0" selected>Все лаборатории</option>
		<option value="10" selected>Только ИЦ</option>
		<?php
        $select = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID` != '6'");
        while ($option = $select->Fetch()) {
            echo "<option value='" . $option['ID'] . "' ".($_POST['lab_gost']==$option['ID'] ? 'selected' : '').">" . $option['NAME'] . "</option>";
        }
        ?>
    </select>
	</td>
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
	$lab_name='';
	$arrayDep = array();
	
	switch ($_POST['lab_gost']) {
				case 1:
					$arrayDep = array('54');
					break;
				case 2:
					$arrayDep = array('57');
					break;
				case 3:
					$arrayDep = array('56');
					break;
				case 4:
					$arrayDep = array('55');
					break;
				case 5:
					$arrayDep = array('58');
					break;
				case 10:
					$arrayDep = array('54','55','56','57');
					break;
				case 0:
					$arrayDep = array('54','55','56','57','58');
					break;
	}
		
	
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
	$summ_oplata2=0; 
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
	
	$deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), Array('!TYPE_ID' => '3'));
	
	$deal_arr=array();
	$deal_new=array();
	$deal_probe=array();
	$deal_isp=array();
	$deal_fin=array();
	$deal_lose=array();
	$deal_won=array();
	$deal_money=array();
	$deal_money2=array();
	$metodic_use=array();
	
	
	
	while($deal = $deals->Fetch())
	{
		$date_act = '';
		$date_oplata = '';
		$date_protocol = '';
		$date_p = '';	
		
		$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $deal['ID'])->Fetch();
		$TZ = unserialize($res_tz['TZ']);
		
		$date_create = date("Y-m-d", strtotime($deal['DATE_CREATE']));
		$date_close = date("Y-m-d",strtotime($deal['CLOSEDATE']));
		
		if($res_tz['ID'])
		{
			$date_act = date("Y-m-d",strtotime($res_tz['DATE_ACT']));
			$date_oplata = date("Y-m-d", strtotime($res_tz['DATE_OPLATA']));
			$date_protocol = $res_tz['DATE_P_CORR'] ? $res_tz['DATE_P_CORR'] :$res_tz['DATE_P'];
			$date_p = date("Y-m-d", strtotime($date_protocol));	
		}
		
		$userr = Array();
		$userr[0] = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
		$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
        if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
			$k=1;
            foreach ($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign) {
                $userr[$k] = CUser::GetByID((int)$assign)->Fetch();
                $k++;
            }
        }
		
		foreach($userr as $u)
		{
		
		$user_dep = $u['UF_DEPARTMENT'][0];
		
		if(in_array($user_dep, $arrayDep))
		{

//СЧИТАЕМ НОВЫЕ ЗАЯВКИ ЗА ПЕРИОД----------------------------------------------------------------------------------------
		
		if($date_create>=$datetime1&&$date_create<=$datetime2)
		{
			
			$deal_arr[] = $deal['ID'];
			$deal_new[$u['ID']][] = $deal['ID']; 
			
		}
		
//НЕУСПЕШНО И УСПЕШНО ЗАКРЫТЫЕ ЗА ПЕРИОД-----------------------------------------------------------------------------------------
				
		if($date_close>=$datetime1 && $date_close<=$datetime2 && $deal['ASSIGNED_BY_ID']!='1')
		{
			
			$id_stage_lose = array('5', '6', '7', '8', '9', '10', '11', 'LOSE');
			
			if(in_array($deal['STAGE_ID'], $id_stage_lose))
			{
				$deal_lose['all'][] = $deal['ID'];
				$deal_lose[$u['ID']][] = "-";
			}
			
			if($deal['STAGE_ID']=='WON')
			{
				$deal_won[$u['ID']][] = $deal['ID'];
				$deal_won['all'][] = $deal['ID'];
			}
		}
			
//АКТЫ ПРИЕМКИ ПРОБ-----------------------------------------------------------------------------------------		
					
		if($date_act && $date_act>=$datetime1 && $date_act<=$datetime2)
		{
			$deal_probe[$u['ID']][] = $deal['ID'];
			$deal_probe['all'][] = $deal['ID'];
		}

//ПOЛНОСТЬЮ ОПЛАЧЕННЫЕ ЗАЯВКИ И СУММА ОПЛАТ--------------------------------------------------------------------------------------------		
		
		if($res_tz['PRICE'] && $res_tz['PRICE']<=$res_tz['OPLATA'] && $date_oplata>=$datetime1 && $date_oplata<=$datetime2)
		{
			$deal_money[$u['ID']][] = $deal['ID'];
			$deal_money['all'][] = $deal['ID'];
			
			$summ_oplata = $summ_oplata+$res_tz['OPLATA'];
			
		}

//ЧАСТИЧНО ОПЛАЧЕННЫЕ ЗАЯВКИ И СУММА ОПЛАТ--------------------------------------------------------------------------------------------
		
		if($res_tz['PRICE'] && $res_tz['OPLATA'] && $res_tz['PRICE']>$res_tz['OPLATA'] && $date_oplata>=$datetime1 && $date_oplata<=$datetime2)
		{
			$deal_money2[$u['ID']][] = $deal['ID'];
			$deal_money2['all'][] = $deal['ID'];
									
			$summ_oplata2 = $summ_oplata2+$res_tz['OPLATA'];
		}

		
		
//СФОРМИРОВАНО ПРОТОКОЛОВ--------------------------------------------------------------------------------------------		
				
		if($date_p>=$datetime1 && $date_p<=$datetime2 && ($res_tz['DATE_P_CORR'] || $res_tz['DATE_P']))
		{
			$deal_isp[$u['ID']][] = $deal['ID'];
			$deal_isp['all'][] = $deal['ID'];
		}
		
//ВЫДАНО ПРОТОКОЛОВ----------------------------------------------------------------------------------------------------------
		
		$id_stage_protocol = array('2', '4', 'WON');
		
		if($date_p>=$datetime1 && $date_p<=$datetime2 && in_array($deal['STAGE_ID'], $id_stage_protocol) && ($res_tz['DATE_P_CORR'] || $res_tz['DATE_P']))
		{
			$deal_fin[$u['ID']][] = $deal['ID'];
			$deal_fin['all'][] = $deal['ID'];
		}
		
//ИСПОЛЬЗОВАНО МЕТОДИК---------------------------------------------------------------------------------------------------------

		if($date_p>=$datetime1 && $date_p<=$datetime2)
		{
			foreach ($TZ as $key => $tzData) {
				foreach ($tzData['gost'] as $k => $gost) {
					$resGost = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $gost)->Fetch();
					$gostAssigned = unserialize($resGost['ASSIGNED']);
					
					$gostLab = array(0,0,0,0,0);
					if($resGost['LFHI']) $gostLab[0]=54;
					if($resGost['LFMI']) $gostLab[1]=56;
					if($resGost['DSL']) $gostLab[2]=55;
					if($resGost['LSM']) $gostLab[3]=57;
					if($resGost['OSK']) $gostLab[4]=58;
					//$gostAssigneds = unserialize($resGost['ASSIGNED']);
					if(in_array($u['ID'], $gostAssigned)) 
					{
						$metodic_use[$u['ID']][] = $resGost['ID'];
						
					}
					$metodic_use['all'][] = $resGost['ID'];
				}
			}
		}
		}
		
		}
	}//while по всем сделкам
	dump($deal_isp);
	?>
	<table id="gost-table">
	<thead>
    <tr>
		<th></th>
		<th style="display:none"></th>
		<?php
		$filter = Array();
		//$arrayDep = array('54','55','56','57','58');
		$usersAll = CUser::GetList(($by='LAST_NAME'), ($order='asc'), $filter);
		$usersIdDep = array();
		//dump($usersAll);
		while($user = $usersAll->Fetch())
		{
			$userData = CUser::GetByID($user['ID'])->Fetch();
			$userDep = $userData['UF_DEPARTMENT'][0];
			//dump($user);
			if(in_array($userDep, $arrayDep)) $usersIdDep[$user['ID']] = $userDep;//echo "<th>".$user['LAST_NAME']."</th>"; 
		}
		
		asort($usersIdDep);
		//dump($usersIdDep);
		$dep=0;
		$nameDep='';
		foreach($usersIdDep as $userId => $userDep)
		{
			switch($userDep) {
			case 54:
				$nameDep = "ЛФХИ";
				break;
			case 55:
				$nameDep = "ДСЛ";
				break;
			case 56:
				$nameDep = "ЛФМИ";
				break;
			case 57:
				$nameDep = "ЛСМ";
				break;
			case 58:
				$nameDep = "ОСК";
				break;
			}
			
			if($userDep != $dep) echo "<th style='border-left: 1px solid #ccc'><h3>".$nameDep."</h3></th>";
			else echo "<th></th>";
			
			$dep = $userDep;
					
		}
			
		?>
	<tr>
		<th>Показатель<th>
		<!--<th>Всего</th>-->
	<?php
		$dep=0;
		foreach($usersIdDep as $userId => $userDep)
		{
			
			$userData = CUser::GetByID($userId)->Fetch();
			$emplLastName = $userData['LAST_NAME'];
			$letter = substr($emplLastName, 3, 1);
			$emplCutLN = in_array($letter, $glasArr) ? substr($emplLastName, 0, 5) : substr($emplLastName, 0, 4);
			if(strlen($emplCutLN)!=strlen($emplLastName))
				$emplCutLN = $emplCutLN.".";
			if($userDep != $dep) 
				echo "<th style='border-left: 1px solid #ccc'>".$emplCutLN."</th>";
			else
				echo "<th>".$emplCutLN."</th>";
			$dep = $userDep;		
		}
	?>
		
	</thead>
	<tbody>
	<tr><td>Участие в новых заявках</td><td></td><!--<td><?= count($deal_arr) ?></td>-->
	<?php
	echoTD($deal_new, $arrayDep);
	 ?>
	<tr><td>Участие в неуспешных заявках</td><td></td><!--<td><?= count($deal_lose['all']) ?></td>-->
	<?php
	echoTD($deal_lose, $arrayDep);
	 ?>
	<tr><td>Участие в закрытых успешных заявках</td><td></td><!--<td><?= count($deal_won['all']) ?></td>-->
	<?php
	echoTD($deal_won, $arrayDep);
	?>
	<tr><td>Участие в заявках, по которым составлены акты ПП</td><td></td><!--<td><?= count($deal_probe['all']) ?></td>-->
	<?php
	echoTD($deal_probe, $arrayDep);
	?>
	<tr><td>Участие в оплаченных полностью заявках</td><td></td><!--<td><?= count($deal_money['all']) ?></td>-->
	<?php
	echoTD($deal_money, $arrayDep);
	?>
	<!--<tr><td>-- на сумму</td><td></td><td><?= $summ_oplata ?></td><td><?= $summ_oplata_lfmi ?></td><td><?= $summ_oplata_lfhi ?></td><td><?= $summ_oplata_lsm ?></td><td><?= $summ_oplata_dsl ?></td><td><?= $summ_oplata_osk ?></td></tr>
	--><tr><td>Участие в оплаченных частично</td><td></td><!--<td><?= count($deal_money2['all']) ?></td>-->
	<?php
	echoTD($deal_money2, $arrayDep);
	?>
	<!--<tr><td>-- на сумму</td><td></td><td><?= $summ_oplata2 ?></td><td><?= $summ_oplata2_lfmi ?></td><td><?= $summ_oplata2_lfhi ?></td><td><?= $summ_oplata2_lsm ?></td><td><?= $summ_oplata2_dsl ?></td><td><?= $summ_oplata2_osk ?></td></tr>
	<tr><td>Всего оплачено</td><td></td><td><?= $c_deal_money+$c_deal_money2 ?></td><td><?= $c_deal_money_lfmi+$c_deal_money2_lfmi ?></td><td><?= $c_deal_money_lfhi+$c_deal_money2_lfhi ?></td><td><?= $c_deal_money_lsm+$c_deal_money2_lsm ?></td><td><?= $c_deal_money_dsl+$c_deal_money2_dsl ?></td><td><?= $c_deal_money_osk+$c_deal_money2_osk ?></td></tr>
	<tr><td>-- на сумму</td><td></td><td><?= $summ ?></td><td><?= $summ_lfmi ?></td><td><?= $summ_lfhi ?></td><td><?= $summ_lsm ?></td><td><?= $summ_dsl ?></td><td><?= $summ_osk ?></td></tr>
	--><tr><td>Участие в сформированных протоколах</td><td></td><!--<td><?= count($deal_isp['all']) ?></td>-->
	<?php
	echoTD($deal_isp, $arrayDep);
	?>
	<tr><td>Участие в выданных протоколах</td><td></td><!--<td><?= count($deal_fin['all']) ?></td>-->
	<?php
	echoTD($deal_fin, $arrayDep);
	?>
	<tr><td>Использовано методик</td><td></td><!--<td><?= count($metodic_use['all']) ?></td>-->
	<?php
	echoTD($metodic_use, $arrayDep);
	?>
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
				// "columns": [
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false }
					// ],
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