<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.04.2019
 * Time: 16:52
 */
 
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Результаты испытаний");
(isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

?>
<style type="text/css">
  #tzc, #tzc option {
 width: 500px;
  margin: 0;

  }
  input[type="submit"]:disabled{
    
   background-color: #C0C0C0;
   border: 1px solid  #C0C0C0;
   cursor: default;   
}

input[type="button"]:disabled{
    
   background-color: #C0C0C0;
   border: 1px solid  #C0C0C0;
   cursor: default;   
}

</style>
  
<?php
$fl_num=0;

   
if(!$fl_num) {
    
    $deals  = $DB->Query("SELECT * FROM `ba_tz`");//CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), Array('!TYPE_ID' => '3'));
	
	$location_year = ""; $location_laba = ""; $location_protocol = "";
	if(!empty($_GET['laba'])&&empty($_GET['year'])&&empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&!empty($_GET['year'])&&empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?year=".$_GET['year']."&protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&empty($_GET['year'])&&!empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?protocol=".$_GET['protocol']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?protocol=".$_GET['protocol']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&&empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&year=".$_GET['year']."&protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&empty($_GET['year'])&&!empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&protocol=".$_GET['protocol']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?protocol=".$_GET['protocol']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&!empty($_GET['year'])&&!empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?protocol=".$_GET['protocol']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?year=".$_GET['year']."&protocol=".$_GET['protocol']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?year=".$_GET['year']."&protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&&!empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?protocol=".$_GET['protocol']."&laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?year=".$_GET['year']."&protocol=".$_GET['protocol']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?year=".$_GET['year']."&laba=".$_GET['laba']."&protocol='+this.options[this.selectedIndex].value)";
	}
	else {
		$location_year = "window.location.assign('/results_isp.php?year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?laba='+this.options[this.selectedIndex].value)";
		$location_protocol = "window.location.assign('/results_isp.php?protocol='+this.options[this.selectedIndex].value)";
	}
	
    ?>
	 <table style="float: right;"><tr>
	 <td>
		<select class="oborud" name="protocol" id="protocol" onchange="<?= $location_protocol?>"  style="float: left; width: 100%">
		
			<option value='all' style='font-weight:bold' <?=$_GET['protocol'] == 'all' ? 'selected' : ''?> selected>Все стадии</option>
			<option value="1" style='font-weight:bold' <?=$_GET['protocol'] == '1' ? 'selected' : ''?>>Испытания еще не проводились</option>
			<option value='2' style='font-weight:bold' <?=$_GET['protocol'] == '2' ? 'selected' : ''?>>Проводятся испытания</option>
			<option value='3' style='font-weight:bold' <?=$_GET['protocol'] == '3' ? 'selected' : ''?>>Испытания прошли, протокол выдан</option>
		<!--	<option value='4' style='font-weight:bold' <?=$_GET['protocol'] == '4' ? 'selected' : ''?>>Заявка прекращена</option>-->
		</select>
	 </td>
	 <td>
	 <select class="oborud" name="place_of_installation_or_storage" id="laba"
            onchange="<?= $location_year?>"
            style="float: left; width: 100%">
		<option value="" selected>Выберите год</option>
        <option value='all' style='font-weight:bold' <?=$_GET['year'] == 'all' ? 'selected' : ''?>>Все года</option>;
		<option value="2020" style='font-weight:bold' <?=$_GET['year'] == '2020' ? 'selected' : ''?>>2020</option>
		<option value='2019' style='font-weight:bold' <?=$_GET['year'] == '2019' ? 'selected' : ''?>>2019</option>;
			
       
    </select>
	</td><td>
	<select class="oborud" name="year_p" id="year"
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
    </select></td>
	<td><input type="button" class="add2" value="Х" onclick="window.location.assign('/results_isp.php')"></input></td>
	<td><input type="checkbox" name="a_filter" id="a_filter" value="">С аттестатом</td>
	</tr></table>
	<br /><br /><br />
    <table id="results_isp">
	<thead>
	<tr>
		<th></th>
		<th>ОА</th>
        <th>№</th>
		<th>Дата</th>
		<th>Клиент</th>
		<th>Объект испытаний</th>
		<th>Ответственный</th>
		<th>№ ТЗ</th>
		<th>Стоимость</th>
		<th>Заявка</th>
		<th>Рез-ты испытаний</th>
		<th>DOC/PDF</th>
		<th>Последнее изменение</th>
		<th>Соот./не соот.</th>
    </tr>
	<tr>
	<th style="margin:0px"></th>
	<th style="margin:0px"></th>
	<th><input type="text" name="num_filter" id="num_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="date_filter" id="date_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="customer_filter" id="customer_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="object_filter" id="object_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="assigned_filter" id="assigned_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="tz_filter" id="tz_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="cost_filter" id="cost_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="request_filter" id="request_filter" style="margin:0px" value=""></th>
	<th></th>
	<th></th>
	<th></th>
	<th></th>
	
	</tr>
	</thead>
	<tbody>
		<div id='buttonRightTable' class='center'></div>
		<div id='buttonLeftTable' class='center'></div>
	
        <?php
			
			while ($deal = $deals->Fetch()) { 
			$id_protocol = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING','FINAL_INVOICE', '1', '2', '3', '4', 'WON');
				switch ($_GET['protocol']) {
						case '1':
							$id_protocol = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING','FINAL_INVOICE');
							break;
						case '2':
							$id_protocol = array('1');
							break;	
						case '3':
							$id_protocol = array('2', '3', '4', 'WON');
							break;
						case '4':
							$id_protocol = array('LOSE');
							break;
						case 'all':
							$id_protocol = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING','FINAL_INVOICE', '1', '2', '3', '4', 'WON');
							break;
				}
				
			// if($id_protocol && !in_array($deal['STAGE_ID'], $id_protocol)) {
				// continue;
			// }
			
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
				$id_year1=date("Y-m-d", strtotime("2019-11-30"));
				$id_year2=date("Y-m-d", strtotime("2021-01-01"));
			}
			$userr = "";//CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
				$user_dep ="";// $userr['UF_DEPARTMENT'][0];
			
			$dop_assign=0;
				$assign_arr = [];//$USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
							// if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
								
								// foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
								// {
									// $assign_user = CUser::GetByID((int)$assign)->Fetch();
									// if($assign_user['UF_DEPARTMENT'][0]==$id_dep)
									// {$dop_assign=1;}
								// }
							// }
				
			
			//$deal = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . $deal['ID'])->Fetch();
			$A='';
			if($deal['ATTESTAT']&&$deal['IN_ATTESTAT_DIAPASON']) $A='A';
			if($deal['DATE_P']||$deal['PDF']|| $deal['DATE_P_CORR']) {
			if ((!empty($_GET['laba']) && ($user_dep == $id_dep||$dop_assign))||empty($_GET['laba']))
				{
				$date_protocol = $deal['DATE_P_CORR'] ? $deal['DATE_P_CORR'] :$deal['DATE_P'];
				$date_p = date("Y-m-d",strtotime($date_protocol));	
				if($date_p>$id_year1 && $date_p<$id_year2)
				{
					//echo $date_p."--";	
					//$date_protocol = $deal['DATE_P_CORR'] ? $deal['DATE_P_CORR'] :$deal['DATE_P'];
						
						$y_date_act = date("Y", strtotime($deal['DATE_ACT']));
						if ($y_date_act != '2020') $y_date_act = "/".substr($y_date_act, 2);
								else $y_date_act = "/".$y_date_act;
						
						$y_date_p = date("Y", strtotime($date_protocol));
						if ($y_date_p != '2020') $y_date_p = "/".substr($y_date_p, 2);
								else $y_date_p = "/".$y_date_p;
								
				if ($deal['NUM_P'] === 'номер не присвоен') {
					$num_tz = 'номер не присвоен';
				} else {
					$num_tz = $deal['NUM_P'] ? $deal['NUM_P'].$y_date_p : 'номер не присвоен';
				}		
				
				//$num_tz = $deal['NUM_P'] ? $deal['NUM_P'] : (int)$deal['ID']+485;
				$color=''; $c=0; $title='';
				$history_ass='';
				$history_date='';
				if ($deal['ID']) 
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
						$color='yellow';
						$c=1;
						$title='Протокол не выдан. Испытания еще не проводились.';
						break;
					case 'PREPARATION':
						$color='yellow';
						$c=1;
						$title='Протокол не выдан. Испытания еще не проводились.';
						break;
					case 'PREPAYMENT_INVOICE':
						$color='yellow';
						$c=1;
						$title='Протокол не выдан. Испытания еще не проводились.';
						break;
					case 'EXECUTING':
						$color='orange';
						$c=1;
						$title='Протокол не выдан. Проводятся испытания.';
						break;
					case 'FINAL_INVOICE':
						$color='orange';
						$c=1;
						$title='Протокол не выдан. Проводятся испытания.';
						break;
					case '1':
						$color='orange';
						$c=2;
						$title='Протокол не выдан. Проводятся испытания.';
						break;
					case '2':
						$color='green';
						$c=3;
						$title='Протокол выдан';
						break;
					case '4':
						$color='green';
						$c=3;
						$title='Протокол выдан';
						break;
					case '3':
						$color='green';
						$c=3;
						$title='Протокол выдан';
						break;
					case 'WON':
						$color='green';
						$c=3;
						$title='Протокол выдан';
						break;
					case '5':
					case '6':
					case '7':
					case '8':
					case '9':
					case '10':
					case '11':
					case '12':
					case 'LOSE':
						$color='red';
						$c=4;
						$title='Испытания не проведены. Заявка прекращена.';
						break;
				}
				
				$mater_arr=[];
				$mater_arr_id=[];
								
				// $res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=".$deal['ID']);
						// while($mater=$res_mat->Fetch())
						// {
							// $mater_arr[]=$mater['NAME_MATERIAL'];
							// $mater_arr_id[]=$mater['ID_MATERIAL'];
						// }
					
					$dealMaterials = "";//($deal['UF_CRM_1576411922643'] ? $deal['UF_CRM_1576411922643'] : implode(', ', $deal['UF_CRM_1569151529509']));
					$dealMaterials = "";//($deal['UF_CRM_1569151529509'][0] ? $dealMaterials : implode(', ', $mater_arr));
						$color2='green';
						$tu = 'Соотв.';
						$tz = unserialize($deal['TZ']);
						$tz_mater = $tz[1]['mater'];
						for($i=2; $i<=count($tz); $i++)
						{
							$tz_mater = $tz_mater . "; " . $tz[$i]['mater'];
						}
						$results = unserialize($deal['MATCH_MANUAL']);
						//var_dump($results);
						// for($i=0; $i<count($results); $i++)
							// for($j=0; $j<count($results[$i]); $j++)
								// for($k=1; $k<=count($results[$i][$j]); $k++)
									// {
										// if ($results[$i][$j][$k]=="0") {$tu = 'Не соотв.'; $color2='red';}
									// }
						//var_dump($tz);
						//$tz_mater = implode('; ', $tz[])
						if ($deal['ACTUAL_FOR_OBJECT']) { $tu = 'Архивный'; $color='grey';}
						
					$probe = unserialize($deal['PROBE']);
					$probePlace = $probe[1]['mesto_data'];
				?>
				<tr>
					<td style="background-color: <?=$color?>; color: <?=$color?>" title="<?=$title?>"><?= $c ?></td>
					<td ><?= $A ?></td>
					<td><?= $num_tz?> </td>
					<td><?= ($deal['DATE_P_CORR'] ? date("d.m.Y", strtotime($deal['DATE_P_CORR'])) : date("d.m.Y", strtotime($deal['DATE_P']))) ?> </td>
					<td><?= $deal['COMPANY_TITLE'] ?></td>
					<td><?= $dealMaterials//$deal['UF_CRM_1576411922643'] ? $deal['UF_CRM_1576411922643'] : implode(', ', $deal['UF_CRM_1569151529509']) ?></td>
					<td><?php echo "<b>".getInitials($deal['ASSIGNED_BY_NAME'])." ".$deal['ASSIGNED_BY_LAST_NAME']."</b>";
						// if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
								// $assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
								// foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
								// {
									// $assign_user = CUser::GetByID((int)$assign)->Fetch();
									// echo ", ".getInitials($assign_user['NAME'])."&nbsp;".$assign_user['LAST_NAME'];
								// }
							// }						
						?>
						</td>
					<td><a href="/tz_show.php?ID=<?= $deal['ID'] ?>" target="_blank"><?= $deal['ID'] ?></a></td>
					<td><a href='/protocol_generator/account.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?=$deal['ID']?>'><?= number_format($deal['PRICE'], 2, ',', '') ?></a></td> 
					<td><a href='/request_card.php?ID=<?= $deal['ID'] ?>&ACT_NUM=<?=$deal['ACT_NUM']?>' target="_blank"><?= $deal['TITLE'] ?></a></td>
					<td><a href="/results_isp.php?ID=<?= $deal['ID'] ?>">Открыть</a></td>
					<td><?php if(!$deal['NO_BITRIX'])
						{ 
							
								
								if($deal['ACTUAL_VER']&&$deal['NUM_P']!='номер не присвоен'&&$deal['NUM_P'])
							{
								$dir  = '/protocol_generator/archive/'.$deal['ID'].date("Y", strtotime($deal['DATE_ACT'])).'/';?>
								<a href="<?= $dir.$deal['ACTUAL_VER'] ?>.docx" target="_blank">Скачать</a>
								<?php
							}
						} 
						else 
						{ ?>
						<a href="/pdf/<?= $deal['PDF'] ?>" target="_blank"><?= $deal['PDF'] ? 'Скачать' : ''  ?> </a>
						<?php } ?>
					</td>
					<td><?= $history_ass ?> <?=$history_date?></td>
					<td style="color:<?=$color2?>"><?= $tu?></td>
				</tr>
			<?php  }
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
   
	<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"/>-->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>
	
    
	<!---'Правки протоколов' зерновой состав грунта начало---->
	<!--'Подключаем текстовый редактор'-->
    <script type="text/javascript" src="/tinymce/tinymce.min.js"></script>
	<!--'Настраиваем текстовый редактор'-->
    
	    </script>
    <!---'Правки протоколов' зерновой состав грунта окончание---->
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
	<script type="text/javascript" language="javascript" src="/jszip/jquery.dataTables.customSortandFilters.js"></script>
	<link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
	<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
	<script>
	 $(document).ready(function () {
            
			$('.oborud_change').select2();
			$('.verify_change').select2();
	 });
	</script>
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
				"columnDefs": [{ "type": "natural", "targets": 2 },{ "sType": "ruDate", "targets": 3 },{ "type": "natural-nohtml", "targets": 7 }, 
				{
				  "targets": 5,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 4,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 3,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 6,
				  "render": $.fn.dataTable.render.ellipsis( 60, true )
				}
				],
				"order": [[ 3, "asc" ]],
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
			
			$('#a_filter').on( 'click', function () {
				if ($('#a_filter').prop("checked")) {
					table
					.columns( 1 )
					.search( 'A' )
					.draw();
				} else {
					table
					.columns( 1 )
					.search( '' )
					.draw();
				}
				
			} );
			
			$('#num_filter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			} );
			
			$('#date_filter').on( 'keyup', function () {
				table
					.columns( 3 )
					.search( this.value )
					.draw();
			} );
			
			$('#customer_filter').on( 'keyup', function () {
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
			$('#assigned_filter').on( 'keyup', function () {
				table
					.columns( 6 )
					.search( this.value )
					.draw();
			} );
			
			$('#tz_filter').on( 'keyup', function () {
				table
					.columns( 7 )
					.search( this.value )
					.draw();
			} );
			
			$('#cost_filter').on( 'keyup', function () {
				table
					.columns( 8 )
					.search( this.value )
					.draw();
			} );
			
			$('#request_filter').on( 'keyup', function () {
				table
					.columns( 9 )
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
										
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>