<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04.04.2019
 * Time: 15:23
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Карточка заявки");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
?>
 <style>
 
 input[type="button"]{
    
    margin: 5px 0 0 0px;
   
}

input[type="button"]:disabled{
    
   background-color: #C0C0C0;
   border: 1px solid  #C0C0C0;
   cursor: default;   
}



</style> 
<?php

if (!empty($_POST['oplata'])||$_POST['oplata']=='0') {
	 $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
    $row = $res->Fetch();
	$res_op = $DB->Query("UPDATE `ba_tz` SET `OPLATA`=" . $_POST['oplata'] . ", `DATE_OPLATA` = '".date("d.m.Y")."' WHERE ID = " . $row['ID']);

	}
	
if (!empty($_POST['comment'])) {
	
	 
	 $res_comm = $DB->Query("SELECT * FROM `COMMENTS` WHERE `ID_REQ`=".$_GET['ID'])->Fetch();
	 if ($res_comm['ID'])  
	 $DB->Query("UPDATE `COMMENTS` SET `TEXT`='" . $_POST['comment'] . "' WHERE `ID_REQ` = " . $_GET['ID']);
	 else
	 $DB->Query("INSERT INTO `COMMENTS` (`ID_REQ`,`TEXT`) VALUES ('".$_GET['ID']."', '" . $_POST['comment'] . "')");

	}	


if (!empty($_GET['ID'])) {
	

	if ($_GET['STAGE']=='1') {
		if (CModule::IncludeModule('crm')) {
			$deal1 = new CCrmDeal;
			$arDealUpdate = array('STAGE_ID' => 'PREPARATION');
			$resUpd = $deal1->Update((int)$_GET['ID'],
				$arDealUpdate,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}
	}
	elseif($_GET['STAGE']=='0')
		{
		if (CModule::IncludeModule('crm')) {
			$deal1 = new CCrmDeal;
			$arDealUpdate = array('STAGE_ID' => '2');
			$resUpd = $deal1->Update((int)$_GET['ID'],
				$arDealUpdate,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}
	}
	
	
		$deal = CCrmDeal::GetByID((int)$_GET['ID']);
		//$mater_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', (int)$_GET['ID']);
		//dump($mater_arr);
	
	
    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
    $row = $res->Fetch();
	if($row) 
	{
		$res_kp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
		$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
		$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
		$res_akt = $DB->Query("SELECT * FROM `ACT` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
		$res_akt_vr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
	}
	
	$company = CCrmCompany::GetByID($deal['COMPANY_ID']); //Берём значение компании
	$companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch(); //Берём значение компании
	//$comEmail = CCrmFieldMulti::GetList(array(), array('TYPE_ID' => 'EMAIL', 'ELEMENT_ID' => $deal['COMPANY_ID']))->Fetch();
	//$comCont = CCrmFieldMulti::GetList(array(), array('ELEMENT_ID' => $deal['COMPANY_ID']))->Fetch();
	//$email = $comEmail['VALUE'];
	
	
	$req = new \Bitrix\Crm\EntityRequisite();
	$rs = $req->getList([
					"filter" => [
						"ENTITY_ID" => $deal['COMPANY_ID'],
					]
				]);
			
				
	$requis = $rs->Fetch();
	$email = $requis['RQ_EMAIL'];
	$name_isp = 'Маргарита Черкозьянова';//$deal['ASSIGNED_BY_NAME'].' '.$deal['ASSIGNED_BY_LAST_NAME'];
	//$color = 'd1ffca';
	$date_protocol = $row['DATE_P_CORR'] ? $row['DATE_P_CORR'] :$row['DATE_P'];
   // $deal = CCrmDeal::GetByID($row['ID_Z']);
   $y_date_act = date("Y", strtotime($row['DATE_ACT']));
	if ($y_date_act != '2020') $y_date_act = "/".substr($y_date_act, 2);
			else $y_date_act = "/".$y_date_act;
	
	$y_date_p = date("Y", strtotime($date_protocol));
	if ($y_date_p != '2020') $y_date_p = "/".substr($y_date_p, 2);
			else $y_date_p = "/".$y_date_p;
   
   $userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
   
    $date_op = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
	$date_oplata = $date_op['UF_CRM_1579541506559']['VALUE'] ? date("d.m.Y", strtotime($date_op['UF_CRM_1579541506559']['VALUE'])) : "Не отправлено"; 
  
  
   //dump($userr);
  //echo $userr['UF_DEPARTMENT'][0];
  if(empty($_GET['PL'])) {
    ?>
 
		<a href="/requests_ID.php">Вернуться к списку заявок</a>
  <?php } else {?> <a href="/planerka">Вернуться в планерку</a><?php } ?>
		<br /><br />
    <h3><?php if(in_array('8', $USER->GetUserGroupArray())) {?><a href='/crm/deal/details/<?= $deal['ID'] ?>/'><?}?>Заявка <?= $deal['TITLE'] ?> от <?= $deal['DATE_CREATE'] ? date("d.m.Y",strtotime($deal['DATE_CREATE'])) : '' ?><?php if(in_array('8', $USER->GetUserGroupArray())) {?></a><?}?>
        | Клиент ID <?= $deal['COMPANY_ID'] ?> | <?php echo getInitials($deal['ASSIGNED_BY_NAME'])." ".$deal['ASSIGNED_BY_LAST_NAME'];
						$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
						if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
								
								foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
								{
									$assign_user = CUser::GetByID((int)$assign)->Fetch();
									echo ", ".getInitials($assign_user['NAME'])." ".$assign_user['LAST_NAME'];
								}
							}						
						?></h3>
		<br />
		
		<br />
    <table id="r_card" >
        <thead>
		
        <th>Тип документа</th>
		<th>Номер документа</th>
        <th>Дата документа</th>
		<?php if(in_array('8', $USER->GetUserGroupArray())) {?><th>Дата отправки клиенту </th><?}?>
		<th>Операции	</th>
		
	<!--	<th>Методика</th>
		<th>Цена</th>-->
        </thead>
		
			
			<tr style="background-color: #<?=$row['ID'] ? 'd1ffca' : 'fff' ?>;">
			<td><b>Техническое задание</b></td><td><?php if($row['ID']) echo  "<a href='/tz_show.php?ID=" . $row['ID']."'>".  $row['ID']. "</a>"; else echo 'Не сформировано';?></td>
			<td><?= $row['DATE_SOZD'] ? date("d.m.Y",strtotime($row['DATE_SOZD'])) : '' ?></td>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><td style="background-color: #FFF;"><?=$date_oplata?></td><?}?>
			<td style="background-color: #FFF;">
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><input type="button" value="Отправить клиенту бланк" class="add" onclick="window.open ('/mail.php?ID=<?=$deal['ID']?>&TYPE=1&EMAIL=<?=$email?>&NAME=<?=$name_isp?>', '_blank')" <?= $row['ID'] ? 'disabled' : ''?>><?}?>
			<input type="button" value="Сформировать" class="add" onclick="window.open ('/tz.php?ID=<?= $deal['ID'] ?>', '_blank')" <?= $row['ID'] ? 'disabled' : ''?>>
			</td>
            </tr>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><tr style="background-color: #<?= $res_kp['ID'] ? 'd1ffca' : 'fff' ?>;">
           	<td><b>Коммерческое предложение</b></td><td><?=$res_kp['ID'] ? $res_kp['ID'] : 'Не сформировано'?></td>
			<td> <?= $res_kp['DATE'] ? date("d.m.Y",strtotime($res_kp['DATE'])) : '' ?></td>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><td style="background-color: #<?= threeDayAfterSending($res_kp['SEND_DATE'])&&!$res_dog['SEND_DATE'] ? 'FF0000' : 'FFF'?>; "><?=$res_kp['SEND_DATE'] ? date('d.m.Y', strtotime($res_kp['SEND_DATE'])) : 'Не отправлено'?></td><?}?>
			<td style="background-color: #FFF;">
			<input type="button" value="Сформировать" class="add" onclick="window.open ('/protocol_generator/kp.php?ID=<?=$deal['ID']?>&TZ_ID=<?=$row['ID']?>', '_blank')" <?= !$row['ID']||$res_akt['ID'] ? 'disabled' : ''?>>
			<input type="button" value="Отправить клиенту" class="add" onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?=$row['ID']?>&TYPE=2&EMAIL=<?=$email?>&NAME=<?=$name_isp?>&ATTACH=<?=$res_kp['ACTUAL_VER']?>', '_blank')" <?= !$res_kp['ID']||$res_akt['ID'] ? 'disabled' : ''?>>
			</td>
            </tr>
			<tr style="background-color: #<?= $res_dog['ID'] ? 'd1ffca' : 'fff' ?>;">
           	<td><b>Договор</b></td><td> <?php $name_dog = ($res_dog['NUMBER'] ? $res_dog['NUMBER'] : $res_dog['ID']); echo $name_dog ? $name_dog : "Не сформирован";   ?></td>
			<td><?=  $res_dog['DATE'] ? date("d.m.Y",strtotime($res_dog['DATE'])) : ''?></td>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><td style="background-color: #<?= threeDayAfterSending($res_dog['SEND_DATE'])&&!$res_acc['SEND_DATE'] ? 'FF0000' : 'FFF'?>;"><?=$res_dog['SEND_DATE'] ? date('d.m.Y', strtotime($res_dog['SEND_DATE'])) : 'Не отправлен'?></td><?}?>
			<td style="background-color: #FFF;">
			<input type="button" value="Сформировать" class="add" onclick="window.open ('/protocol_generator/dogovor.php?ID=<?=$deal['ID']?>&TZ_ID=<?=$row['ID']?>', '_blank')" <?= !$row['ID']||$res_akt['ID'] ? 'disabled' : ''?>>
			<input type="button" value="Отправить клиенту" class="add" onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?=$row['ID']?>&TYPE=3&EMAIL=<?=$email?>&NAME=<?=$name_isp?>&ATTACH=<?=$res_dog['ACTUAL_VER']?>', '_blank')" <?= !$res_dog['ID']||$res_akt['ID'] ? 'disabled' : ''?>>
			</td>
            </tr>
            <tr style="background-color: #<?= $res_acc['ID'] ? 'd1ffca' : 'fff' ?>;">
            <td><b>Счет</b></td><td><?= $res_acc['ID'] ? $res_acc['ID'] : "Не сформирован"  ?></td>
			<td><?= $res_acc['DATE'] ? date("d.m.Y",strtotime($res_acc['DATE'])) : ''  ?></td>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><td style="background-color: #<?= threeDayAfterSending($res_acc['SEND_DATE'])&&!$row['DATE_ACT'] ? 'FF0000' : 'FFF'?>;"><?=$res_acc['SEND_DATE'] ? date('d.m.Y', strtotime($res_acc['SEND_DATE'])) : 'Не отправлен'?></td><?}?>
			<td style="background-color: #FFF;">
			<input type="button" value="Сформировать" class="add" onclick="window.open ('/protocol_generator/account.php?ID=<?=$deal['ID']?>&TZ_ID=<?=$row['ID']?>', '_blank')" <?= !$row['ID']||$res_akt['ID'] ? 'disabled' : ''?>>
			<input type="button" value="Отправить клиенту" class="add" onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?=$row['ID']?>&TYPE=4&EMAIL=<?=$email?>&NAME=<?=$name_isp?>&ATTACH=<?=$res_acc['ACTUAL_VER']?>', '_blank')" <?= !$res_acc['ID']||$res_akt['ID'] ? 'disabled' : ''?>>
			</td>
            </tr><?php } if(in_array('8', $USER->GetUserGroupArray())) {?>
			<tr style="background-color: #<?= $row['OPLATA']&&($row['OPLATA']== $row['PRICE']) ? 'd1ffca' : 'fff' ?>;">
            <td><b>Оплата</b></td><td><?php if($row['OPLATA']>$row['PRICE']) echo "Переплата";
			elseif($row['OPLATA']&&($row['OPLATA']== $row['PRICE'])) echo "Оплачено полностью";
			elseif(!$row['OPLATA']) echo "Не оплачено";
			else echo "Оплачено частично";
			?></td>
			<td><?= $row['DATE_OPLATA'] ? date("d.m.Y",strtotime($row['DATE_OPLATA'])) : ''  ?></td>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><td style="background-color: #FFF;">-</td><?}?>
			<td style="background-color: #FFF;"><?php if($row['ID']) {?>
			<form method="POST" id="op">
			<input type="number" step="0.01" class="oborud" name="oplata" value="<?=$row['OPLATA']?>">
			<input type="submit" value="Сформировать" id="oplata_bt" >
			Остаток: <?php $ost = (float)$row['PRICE'] - (float)$row['OPLATA']; echo $ost; ?> руб. 
			</form><?php } else {?><input type="number" step="0.01" class="oborud" name="oplata" value="<?=$row['OPLATA']?>" disabled>
			<input type="button" value="Сформировать" id="oplata_bt" class="add" disabled><?}?>
			</td>
            </tr><?}?>
			<tr style="background-color: #<?= $row['ACT_NUM'] ? 'd1ffca' : 'fff' ?>;">
           	<td><b>Акт приемки проб</b></td><td> <?php if($row['ACT_NUM']) echo  "<a href='/probe.php?ID=" . $deal['ID']."'>".  $row['ACT_NUM'].$y_date_act." </a>"; else echo 'Не сформирован';?></td>
			<td><?= $row['ACT_NUM'] ? date("d.m.Y",strtotime($row['DATE_ACT'])) : '' ?></td>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><td style="background-color: #FFF;">-</td><?}?>
			<td style="background-color: #FFF;">
			<input type="button" value="Сформировать" class="add" onclick="window.open ('/protocol_generator/', '_blank')" <?= ($row['ACT_NUM']||!$row['ID']) ? 'disabled' : ''?>>
			<!--<input type="button" value="Отправить клиенту" class="add" onclick="window.open ('/protocol_generator/', '_blank')" <?= !$row['ACT_NUM']||$res_akt['ID'] ? 'disabled' : ''?>>-->
			</td>
            </tr>
			<tr style="background-color: #<?= $row['RESULTS'] ? 'd1ffca' : 'fff' ?>;">
            <td><b>Результаты испытаний</b></td><td><?php if($row['RESULTS']) echo "<a href='//172.16.255.2/results_isp.php?ID=".$deal['ID']."'>". $row['ID']." </a>"; else echo 'Не внесены'; ?></td>
			<td><?= $row['DATE_P'] ? date("d.m.Y", strtotime($row['DATE_P'])) : '' ?></td>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><td style="background-color: #FFF;">-</td><?}?>
			<td style="background-color: #FFF;">
			<input type="button" value="Внести результаты" class="add" onclick="window.open ('/results_isp.php?ID=<?= $deal['ID'] ?>', '_blank')" <?= ($row['RESULTS']||!$row['ID']) ? 'disabled' : ''?>>
			</td>
            </tr>
			<tr style="background-color: #<?= $row['NUM_P'] ? 'd1ffca' : 'fff' ?>;">
            <td><b>Протокол</b></td><td><?= $row['NUM_P'] ? $row['NUM_P'].$y_date_p : 'Не сформирован' ?></td>
			<td><?= $date_protocol ? date("d.m.Y", strtotime($date_protocol)) : '' ?></td>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><td style="background-color: #<?= threeDayAfterSending($row['PROTOCOL_SEND_DATE'])&&!$res_akt_vr['ID'] ? 'FF0000' : 'FFF'?>;"><?=$row['PROTOCOL_SEND_DATE'] ? date('d.m.Y', strtotime($row['PROTOCOL_SEND_DATE'])) : 'Не отправлен'?></td><?}?>
			<td style="background-color: #FFF;">
			<input type="button" value="Скачать" class="add" onclick="window.open ('/protocol_generator/protocol.php?ID=<?=$deal['ID']?>&TZ_ID=<?=$row['ID']?>', '_blank')" <?= !$row['NUM_P'] ? 'disabled' : ''?>>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><input type="button" value="Отправить клиенту" class="add" onclick="window.open ('/mail.php?ID=<?= $row['NUM_P'] ?>&TZ_ID=<?=$row['ID']?>&TYPE=5&EMAIL=<?=$email?>&NAME=<?=$name_isp?>&ATTACH=<?=$row['ACTUAL_VER']?>', '_blank')" <?= !$row['NUM_P']||!$row['ACTUAL_VER']||!$row['OPLATA'] ? 'disabled' : ''?>><?}?>
			</td>
            </tr>
			<tr style="background-color: #<?= $res_akt_vr['ID'] ? 'd1ffca' : 'fff' ?>;">
			<td><b>Акт выполненных работ</b></td><td><?= $res_akt_vr['ID'] ? $res_akt_vr['ID'] : 'Не сформирован'  ?></td>
			<td><?= $res_akt_vr['DATE'] ? date("d.m.Y",strtotime($res_akt_vr['DATE'])) : '' ?></td>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?><td style="background-color: #<?= threeDayAfterSending($res_akt['SEND_DATE']) ? 'FF0000' : 'FFF'?>;"><?=$res_akt['SEND_DATE'] ? date('d.m.Y', strtotime($res_akt['SEND_DATE'])) : 'Не отправлен'?></td><?}?>
			<td style="background-color: #FFF;">
			<input type="button" value="Сформировать" class="add" onclick="window.open ('/protocol_generator/akt_vr.php?ID=<?=$deal['ID']?>&TZ_ID=<?=$row['ID']?>', '_blank')" <?= !$row['NUM_P']||!$row['OPLATA'] ? 'disabled' : ''?>> 
			<input type="button" value="Отправить клиенту" class="add" onclick="window.open ('/mail.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?=$row['ID']?>&TYPE=6&EMAIL=<?=$email?>&NAME=<?=$name_isp?>&ATTACH=<?=$res_akt_vr['ID']?>', '_blank')" <?= 'disabled' //!$res_akt_vr['ID'] ? 'disabled' : ''?>>
			</td>
            </tr>
			
          
        
<!--    </table>-->
			<?php
} 
$res_comm = $DB->Query("SELECT * FROM `COMMENTS` WHERE `ID_REQ`=".$deal['ID'])->Fetch();
?>
    </tbody></table><br /><br />
	Комментарий <form method="POST" id="comm">
							<div style="display:flex; padding-left:5px">
							<input type="text" class="oborud" name="comment" style="width:350px; height:40px; margin-bottom:0px;" value="<?=$res_comm['TEXT']?>">
							<input type="submit" style="margin:0 0 0 10px" value="Добавить" id="comm_bt" ></div>
							
						</form>
	<br /><br />
	Работы по заявке завершены <input type="checkbox" name="FIN" <?= ($deal['STAGE_ID']!='2'&&$deal['STAGE_ID']!='3'&&$deal['STAGE_ID']!='4'&&$deal['STAGE_ID']!='WON') ? '' : 'checked' ?> <?= ($USER->GetID()=='1')||($USER->GetID()=='10')||($USER->GetID()=='11') ? '' : 'disabled'?> onclick="window.open ('/request_card.php?ID=<?=$deal['ID']?>&STAGE=<?=($deal['STAGE_ID']!='2'&&$deal['STAGE_ID']!='3'&&$deal['STAGE_ID']!='4'&&$deal['STAGE_ID']!='WON') ? '0' : '1'?>')">
	<br /><br />
	Протокол в ОА <input type="checkbox" name="OA" <?= !$row['ATTESTAT'] ? '' : 'checked' ?> disabled >
	<br /><br />
	
	<h3>Списки версий</h3>
	<table  id="r_card">
	<thead>
	<th>Протокол</th>
	<?php if(in_array('8', $USER->GetUserGroupArray())) {?><th>КП</th>
	<th>Договор</th>
	<th>Счет</th><?}?>
	</thead>
	<tbody>
	<tr><td valign="top">
	
	<?php 
	if($row['NUM_P']){
	$dir  = 'protocol_generator/archive/'.$row['NUM_P'].'/';
	$skip = array('.', '..');
	$files = scandir($dir);
	foreach($files as $file) {
    if(!in_array($file, $skip))
        echo "<a href='$dir$file'>$file</a><br><br>";
	}
	}?>
	</td><td valign="top">
	<?php 
	if($res_kp['ID']){
	$dir  = 'protocol_generator/archive_kp/'.$deal['ID'].'/';
	$skip = array('.', '..');
	$files = scandir($dir);
	foreach($files as $file) {
    if(!in_array($file, $skip))
        echo "<a href='$dir$file'>$file</a><br><br>";
	}
	}
	?>
	</td>
	<td valign="top">
	<?php 
	if($res_dog['ID']){
	$dir  = 'protocol_generator/archive_dog/'.$deal['ID'].'/';
	$skip = array('.', '..');
	$files = scandir($dir);
	foreach($files as $file) {
    if(!in_array($file, $skip))
        echo "<a href='$dir$file'>$file</a><br><br>";
	}
	}?>
	</td>
	<td valign="top">
	<?php 
	if($res_acc['ID']){
	$dir  = 'protocol_generator/archive_acc/'.$deal['ID'].'/';
	$skip = array('.', '..');
	$files = scandir($dir);
	foreach($files as $file) {
    if(!in_array($file, $skip))
        echo "<a href='$dir$file'>$file</a><br><br>";
	}
	}?>
	</td></tr>
	</tbody></table>
	
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
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
	<script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
	
	<script>
        $(document).ready(function () {
            $('#tz').DataTable({
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
				"order": [[ 2, "desc" ]],
				"columnDefs": [{ "type": "num", "targets": 2 },{ "sType": "ruDate", "targets": 1 }
				],
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
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>