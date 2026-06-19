<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10.04.2019
 * Time: 15:36
 */


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DataStorage.php');

$APPLICATION->SetTitle("Журнал приемки проб");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

$db = new CDB();
$newResult = new NewResult();

define('RESULT_REFACTORING_START_ID', 8846);

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
if (!empty($_POST['zakaz_man'])) {
	$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
	$sql = $res->Fetch();
	$dateProbeArr = $_POST['date_probe'];
	$probe = unserialize($sql['PROBE']);
	$proizv_prob = $probe[1]['probe_proizv'];

	
    $probe[$_GET['mater']] = array(
        "zakaz_man" => $_POST['zakaz_man'],
        "number_probe" => $_POST['number_probe'],
        "mesto_data" => $_POST['mesto_data'],
        "probe_proizv" => $proizv_prob,
        "number" => $_POST['number'],
        "sh_number" => $_POST['sh_number'],
        "dat" => $_POST['dat'],
    );


	$DB->Query("DELETE FROM `DATES_FOR_PROBES` WHERE TZ_ID = " . (int)$_GET['TZ_ID']);
	foreach($dateProbeArr as $key => $dateProbe)
	{
		$DB->Query("INSERT INTO `DATES_FOR_PROBES` (`PROBE`, `DATE`, `TZ_ID`) VALUES ('".$probe[$_GET['mater']]['sh_number'][$key]."', '".$dateProbe."', ".(int)$_GET['TZ_ID'].")");
	}

    $probe = serialize($probe);
    $res = $DB->Query("UPDATE `ba_tz` SET `PROBE`='" . $probe . "' WHERE ID = " . (int)$_GET['TZ_ID']);


	$deal1 = CCrmDeal::GetByID((int)$_GET['ID']);
	if ($deal1['STAGE_ID']!='1'&&$deal1['STAGE_ID']!='2'&&$deal1['STAGE_ID']!='4'&&$deal1['STAGE_ID']!='WON') {
		if (CModule::IncludeModule('crm')) {
			$deal = new CCrmDeal;
			$arDealUpdate = array('STAGE_ID' => 'FINAL_INVOICE');
			$resUpd = $deal->Update((int)$_GET['ID'],
				$arDealUpdate,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}

		
		
		if (!empty($_GET['TZ_ID'])) {
			$res_assign_update = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('FINAL_INVOICE') . "', `STAGE_NUMBER`= " . (int)1 . " WHERE `ID`=" . (int)$_GET['TZ_ID']);
		}
		
	}
}

if (!empty($_GET['TZ_ID'])) {
	$deal = CCrmDeal::GetList(array('DATE_CREATE' => 'DESC'), array('ID' => (int)$_GET['ID']))->Fetch();

	$edit = true;

	$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
	$sql = $res->Fetch();
	$tz = unserialize($sql['TZ'])[$_GET['mater']];
	$probe = unserialize($sql['PROBE'])[$_GET['mater']];

	$materials = $deal['UF_CRM_1569151529509'];

	$y_date = date("Y", strtotime($sql['DATE_ACT']));
	if ($y_date != '2020') $y_date = "/" . substr($y_date, 2);
	else $y_date = "/" . $y_date;

	$probes = [];
	$arrProbe = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` mtr, `probe_to_materials` ptm WHERE mtr.`ID` = ptm.`material_request_id` AND mtr.`ID_DEAL`= {$_GET['ID']} AND mtr.`ID_MATERIAL` = {$_GET['materID']} AND ptm.`material_request_id` = {$_GET['mrId']}");
	while ($arrProbes = $arrProbe->Fetch()) {
		$probes[] = [
				'material' => $arrProbes['NAME_MATERIAL'],
				'numProbe' => $arrProbes['cipher']
		];
	}
	$companys = CCrmCompany::GetList();

	?>
	<?php if ($edit): ?>
		<form method="post">
	<?php endif;


	$probe_num = '';
	if (!empty($sql['ACT_NUM']))
		$probe_num = $sql['ACT_NUM'] . $y_date;
	else
		$probe_num = (!empty($probe['number'])) ? $probe['number'] : $sql['ID'] . $y_date;
	?>
	<table id="probe">
		<tr>
			<th colspan="2">Регистрационный номер в ИЦ
				<u><?= $probe_num ?></u>
				Дата поступления в ИЦ
				<u><?= (!empty($probe['dat']) ? date("d.m.Y", strtotime($probe['dat'])) : date("d.m.Y")) ?></u></th>
			<input type="hidden" name="number"
				   value="<?= $probe_num ?>">
			<input type="hidden" name="dat"
				   value="<?= (!empty($probe['dat']) ? date("d.m.Y", strtotime($probe['dat'])) : date("d.m.Y")) ?>">
		</tr>
		<tr>
			<td>
				<label>Заказчик: <?= in_array('1', $USER->GetUserGroupArray()) ? $deal['COMPANY_TITLE'] : "Нет доступа" ?>
					<input type="hidden" name="zakaz_man" value="<?= $deal['COMPANY_ID'] ?>">
				</label>
			</td>
		</tr>
	</table>
	<br/>
	<table id="probe">
		<thead>
		<tr>
			<th>Шифр в ИЦ</th>
			<th>Наименование пробы (образцов)</th>
			<th>Дата отбора</th>
		</tr>
		</thead>
<!--		--><?php
//		$k = 0;
//		for ($i = (int)$_GET['num_probe'] + 1; $i <= (int)$_GET['num_probe'] + (int)$tz['obiem']; $i++) {
//			$probe_sh = '';
//			if (!empty($sql['ACT_NUM']))
//				$probe_sh = $sql['ACT_NUM'] . "." . $i . $y_date;
//			else
//				$probe_sh = (!empty($probe['sh_number'][$k])) ? $probe['sh_number'][$k] : $sql['ID'] . "." . $i;
			?>

			<tbody>
			<?php foreach ($probes as $prob):?>
		<tr>
			<td width="250px">
				<!--<label><?//= $probe_sh ?></label>-->
				<input type="text" class="tz" name="sh_number[]" value="<?=$prob['numProbe']?>">
			</td>
			<td>

<!--				--><?php
//				$materArr = explode(', ID', $probe['number_probe'][$k]);
//				$mater = !empty($materArr[0]) ? $materArr[0] : '';
//				?>
				<!--<label><input type="text" class="tz" name="number_probe[]" size="70"
							  value='<//?= ($probe['number_probe'][$k] ? $probe['number_probe'][$k] : $materials[$_GET['mater'] - 1]) ?>'></label>-->
<!--				<label><input type="text" class="tz" name="number_probe[]" size="70"-->
<!--							  value='--><?//= ($mater ? $mater : $materials[$_GET['mater'] - 1]) ?><!--'>-->
							 <input type="text" class="tz" name="number_probe[]" size="70"
							  value='<?=$prob['material']?>'>

			</td>
			<td>
				<?php $resDate = $DB->Query("SELECT * FROM `DATES_FOR_PROBES` WHERE `PROBE`='" . $probe['sh_number'][$k] . "'")->Fetch();
				if (!$resDate['ID']) {
					$datePr = explode("; ", $probe['mesto_data']);
					$dateProbe = right_date($datePr[1]);
				} else
					$dateProbe = $resDate['DATE'];
				?>
				<input type="date" style="width:100%" class="tz" name="date_probe[]"
					   value="<?= $dateProbe ?>">
			</td>
		</tr>
			</tbody>
<!--			--><?php //$k++;
//		} ?>
	<?php endforeach;?>
	</table>
	<br/><br/>

	<br/>

	<table id="probe">
		<thead>
		<tr>
			<th>ДОПОЛНИТЕЛЬНЫЕ ХАРАКТЕРИСТИКИ</th>
			<th>
			</th>
		</tr>
		</thead>
		<tr>
			<td>

				Место и дата отбора проб (образцов)
			</td>
			<td><input type="text" class="tz" name="mesto_data" size="70"
					   value='<?= $probe['mesto_data'] ?>'>

			</td>
		</tr>
		<!-- переехало в тз -->
		<!-- <tr>
            <td>
                Отбор проб произвёл </td><td><input type="text" class="tz" name="probe_proizv" size="70"
                                                   value="<?= $probe['probe_proizv'] ?>">
            </td>
        </tr> -->
		<tr>
			<td>
				Определяемые показатели:
			</td>
			<td>
				<?php
				$gosts = [];
				foreach ($tz['gost'] as $item) {
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
					$gost = $res->Fetch();
					$gosts[] = $gost['SPECIFICATION'];
				}
				echo implode(', ', $gosts);
				?>

			</td>
		</tr>
	</table>
	<?php if ($edit): ?>
		<input type="submit" value="Сохранить">
	<?php endif; ?>
	<?php if ($edit): ?>
		</form>
	<?php endif; ?>
	<br/>
	<a href="/probe.php?ID=<?= $_GET['ID'] ?>">Назад</a> |
	<a href="/protocol_generator/probe.php?ID=<?= $_GET['ID'] ?>&TZ_ID=<?= $_GET['TZ_ID'] ?>&mater=<?= $_GET['materID'] ?>">Скачать</a> |
	<!--<a href="/results_isp.php?ID=< ?= $_GET['ID'] ?>">Перейти к испытаниям</a>-->
	<?php
	//if (!empty($_GET['ID']) && in_array((int)$_GET['ID'], $newResult->arrIdDeal) || !empty($_GET['ID']) && (int)$_GET['ID'] === 5138  || !empty($_GET['ID']) && (int)$_GET['ID'] === 5479 || !empty($_GET['ID']) && (int)$_GET['ID'] === 5697 || !empty($_GET['ID']) && (int)$_GET['ID'] === 5893) {
	if (!empty($_GET['ID']) && (in_array((int)$_GET['ID'], $newResult->arrIdDeal) || (int)$_GET['ID'] > $newResult->idDealВefore)) {
		$selectedProtocolData = null;
		if (!empty($sql['ID'])) {
			$selectedProtocolData = $db->select("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}", [$sql['ID']]);
		}
		$idFistProtocol = !empty($selectedProtocolData[0]['ID']) ? $selectedProtocolData[0]['ID'] : 0;
		?>

        <?php if ($_GET['ID'] > RESULT_REFACTORING_START_ID): ?>
            <a href="/ulab/result/card/<?= $_GET['ID'] ?>?protocol_id=<?= $idFistProtocol ?>">Перейти к испытаниям</a>
        <?php else: ?>
            <a href="/results_isp.php?ID=<?= $_GET['ID'] ?>&ID_P=<?= $idFistProtocol ?>">Перейти к испытаниям</a>
        <?php endif; ?>
	<?php } else { ?>
        <?php if ($_GET['ID'] > RESULT_REFACTORING_START_ID): ?>
            <a href="/ulab/result/card/<?= $_GET['ID'] ?>">Перейти к испытаниям</a>
        <?php else: ?>
            <a href="/results_isp.php?ID=<?= $_GET['ID'] ?>">Перейти к испытаниям</a>
        <?php endif; ?>

		<?php
	}
} elseif (!empty($_GET['ID'])) {
	$deal1 = CCrmDeal::GetByID((int)$_GET['ID']);
	if ($deal1['STAGE_ID'] != '1' && $deal1['STAGE_ID'] != '2' && $deal1['STAGE_ID'] != '3' && $deal1['STAGE_ID'] != '4' && $deal1['STAGE_ID'] != 'WON') {
		if (CModule::IncludeModule('crm')) {
			$deal = new CCrmDeal;
			$arDealUpdate = array('STAGE_ID' => 'FINAL_INVOICE');
			$resUpd = $deal->Update((int)$_GET['ID'],
				$arDealUpdate,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}


		//$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос
		//Обновляем стадию в таблице ba_tz
		$res_assign_update = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('FINAL_INVOICE') . "', `STAGE_NUMBER`= " . (int)1 . " WHERE `ID_Z`=" . (int)$_GET['ID']);

	}

	$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
	$sql = $res->Fetch();
	$arr = unserialize($sql['TZ']);
	$probe = unserialize($sql['PROBE']);
	?>

	<div>
		<div class='return disable' title='Вернуться назад'></div>
		<div class='edit1 disable' title='Редактировать'></div>
		<a href="/requests_test.php">
			<div class='list' title='Вернуться к списку'></div><!--Список заявок-->
		</a>
		<a href="/ulab/request/card/<?= $deal1['ID'] ?>">
			<div class='card' title='Карточка заявки'></div><!--Карточка заявки-->
		</a>
		<a href="/protocol_generator/label_probe_all.php?ID=<?= $_GET['ID'] ?>&TZ_ID=<?= $sql['ID'] ?>">
			<div class='download' title='Скачать этикетки на все пробы'></div>
		</a>
		<div class='copy disable' title='Скопировать'></div>
		<div class='remove disable' title='Удалить'></div>
		<div class='add disable' title='Добавить'></div>
		<div class='report disable' title='Отчёт'></div>
		<div class='further disable' title='Далее'></div>
	</div>

	<?php

	//if (in_array('1',$USER->GetUserGroupArray()))  {echo  " | <a href='//172.16.255.2/crm/deal/details/". $_GET['ID'] . "/'>Сделка</a>"; } else {};
	$status = '';
	if (($deal1['STAGE_ID'] == '2' || $deal1['STAGE_ID'] == '3' || $deal1['STAGE_ID'] == '4' || $deal1['STAGE_ID'] == 'WON')) {
		$status = 'Выдан';
	} elseif ($deal1['TYPE_ID'] == 'COMPLEX') {
		$status = 'OCK';
	} else {
		$status = 'Не выдан';
	}

	?>
	<br/><br/>
	<form method="post">
		<table id="probe">
			<thead>
			<th>Наименование материала</th>
			<th>Наименование показателя</th>
			<th>Объём работ</th>
			<th>Проба принята <br>в лабораторию</th>
			<th>Функции</th>
			</thead>
			<?php
			$deal = CCrmDeal::GetList(array('DATE_CREATE' => 'DESC'), array('ID' => $_GET['ID']), array("UF_CRM_1554700718621"))->Fetch();
			preg_match('/[a-z\.\/\?_A-Z-]*=([0-9]*)/', $deal['UF_CRM_1554700718621'], $output_array);

			$data = [];
			$data = [];
			//			if (empty($probe)) {
			$probeinf = $db->select("SELECT mtr.`NAME_MATERIAL`, mtr.`ID_MATERIAL`, mtr.`OBIEM`, ptm.`material_request_id`, 
										(SELECT GROUP_CONCAT('<p>', `SPECIFICATION` SEPARATOR '</p>') 
										FROM `ba_gost` bg, `gost_to_probe` gtp
										WHERE bg.`ID` = gtp.`gost_method` AND gtp.`probe_id` = ptm.`ID`
										) gtp 
									FROM `MATERIALS_TO_REQUESTS` mtr, `probe_to_materials` ptm 
									WHERE mtr.`ID` = ptm.`material_request_id` AND `ID_DEAL`= {$_GET['ID']}
									GROUP BY ptm.`material_request_id`");
			$rowspan = count($probeinf);
			$k = 1;
			foreach ($probeinf as $key => $elem):?>
				<tr>
					<td><?= $elem['NAME_MATERIAL'] ?></td>
					<td><?=$elem['gtp']?></td>
					<td><?= $elem['OBIEM'] ?></td>
					<?php if ($key == 0): ?>
						<td rowspan="<?=$rowspan?>" style="text-align: -webkit-center;">
							<input type="checkbox" id="probe_in_lab"
								   class="bigbox" <?= ($sql['PROBE_IN_LAB'] == 2) ? 'checked disabled' : '' ?>
						</td>
					<?php endif; ?>
					<td><input type="button" title="Изменить" class="add edit1" style="display: inline-block"
							   onclick="window.open ('/probe.php?ID=<?= $_GET['ID'] ?>&TZ_ID=<?= $sql['ID'] ?>&mater=<?= $k ?>&materID=<?= $elem['ID_MATERIAL'] ?>&mrId=<?= $elem['material_request_id'] ?>')" <?= ($status == 'Выдан') ? 'disabled' : '' ?>>

						<div class='download downloadOborud' title='Скачать' style="position: relative">
							<ul class='downloadMenu right-30'>
								<li>
									<a href="/protocol_generator/probe.php?ID=<?= $_GET['ID'] ?>&TZ_ID=<?= $sql['ID'] ?>&mater=<?= $elem['ID_MATERIAL'] ?>">
										Скачать Акт приема проб
									</a>
								</li>
								<li>
									<a href="/protocol_generator/label_probe.php?ID=<?= $_GET['ID'] ?>&TZ_ID=<?= $sql['ID'] ?>&mater=<?= $elem['ID_MATERIAL'] ?>">
										Скачать этикетки на пробы
									</a>
								</li>
								<li>
									<a href="/protocol_generator/probe_vnutr.php?ID=<?= $_GET['ID'] ?>&TZ_ID=<?= $sql['ID'] ?>&mater=<?= $key ?>">
										Скачать акт передачи в ИЦ
									</a>
								</li>
							</ul>
						</div>
						<input type="button" title="Изменить" class="add edit1" style="display: inline-block"
							   onclick="window.open ('/probe.php?ID=<?= $_GET['ID'] ?>&TZ_ID=<?= $sql['ID'] ?>&mater=<?= $k ?>&materID=<?= $elem['ID_MATERIAL'] ?>&mrId=<?= $elem['material_request_id'] ?>')" <?= ($status == 'Выдан') ? 'disabled' : '' ?>>
						<!--				  <input type="button" title="Скачать" class="add download" onclick="window.open ('/protocol_generator/probe.php?ID=-->
						<? /*= $_GET['ID'] ?>/&TZ_ID=<?////= $sql['ID'] ?>////&mater=<?////= $key */
						?><!--')" >-->
						<!--<input type="button" title="Скачать акт передачи в ИЦ" class="add downloadAct" onclick="window.open ('/protocol_generator/probe_vnutr.php?ID=-->
						<? /*= $_GET['ID'] ?>////&TZ_ID=<?= $sql['ID'] ?>&mater=<?////= $key */
						?><!--')" >-->
					</td>
				</tr>
			<?php $k++;
            endforeach;

			//			} else {
			//				foreach ($arr as $key => $arrDatum) {
			//					$data[] = $arrDatum['mater'];
			//				}
			//				$a = array_count_values($data);
			//				$b = count($data);
			//				$last_gost = '';
			//				$num_probe = 0;
			//				foreach ($arr as $key => $arrDatum) {
			//					?>
			<!--					<tr>-->
			<!--						<td>--><?//= $arrDatum['mater'] ?><!--</td>-->
			<!--						<td>-->
			<!--							--><?php
			//							foreach ($arrDatum['gost'] as $item) {
			//								$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
			//								echo "<p>" . $res['SPECIFICATION'] . "</p>";
			//							}
			//							?>
			<!--						</td>-->
			<!--						<td>--><?//= $arrDatum['obiem'] ?><!--</td>-->
			<!--						--><?php //if ($key == 1): ?>
			<!--							<td rowspan="--><?//= $b ?><!--" style="text-align: -webkit-center;">-->
			<!--								<input type="checkbox" id="probe_in_lab"-->
			<!--									   class="bigbox" --><?//= ($sql['PROBE_IN_LAB'] == 2) ? 'checked disabled' : '' ?>
			<!--							</td>-->
			<!--						--><?php //endif; ?>
			<!--						<td><input type="button" title="Изменить" class="add edit1"-->
			<!--								   onclick="window.open ('/probe.php?ID=--><?//= $_GET['ID'] ?><!--&TZ_ID=<?//= $sql['ID'] ?>//&mater=<?//= $key ?>//&num_probe=<?//= $num_probe ?>//')" <?//= ($status == 'Выдан') ? 'disabled' : '' ?><!-->
			<!---->
			<!--							<div class='download downloadOborud' title='Скачать' style="position: relative">-->
			<!--								<ul class='downloadMenu right-30'>-->
			<!--									<li>-->
			<!--										<a href="/protocol_generator/probe.php?ID=--><?//= $_GET['ID'] ?><!--&TZ_ID=--><?//= $sql['ID'] ?><!--&mater=--><?//= $key ?><!--">-->
			<!--											Скачать Акт приема проб-->
			<!--										</a>-->
			<!--									</li>-->
			<!--									<li>-->
			<!--										<a href="/protocol_generator/label_probe.php?ID=--><?//= $_GET['ID'] ?><!--&TZ_ID=--><?//= $sql['ID'] ?><!--&mater=--><?//= $key ?><!--">-->
			<!--											Скачать этикетки на пробы-->
			<!--										</a>-->
			<!--									</li>-->
			<!--									<li>-->
			<!--										<a href="/protocol_generator/probe_vnutr.php?ID=--><?//= $_GET['ID'] ?><!--&TZ_ID=--><?//= $sql['ID'] ?><!--&mater=--><?//= $key ?><!--">-->
			<!--											Скачать акт передачи в ИЦ-->
			<!--										</a>-->
			<!--									</li>-->
			<!--								</ul>-->
			<!--							</div>-->
			<!--											  <input type="button" title="Скачать" class="add download" onclick="window.open ('/protocol_generator/probe.php?ID=-->
			<!--							--><?// $_GET['ID'] ?><!--&TZ_ID=<?////= $sql['ID'] ?>////&mater=<?////= $key */ */?>')" >-->
			<!--							<input type="button" title="Скачать акт передачи в ИЦ" class="add downloadAct" onclick="window.open ('/protocol_generator/probe_vnutr.php?ID=-->
			<?//= $_GET['ID'] ?><!--&TZ_ID=--><?//= $sql['ID'] ?><!--&mater=<?//= $key ?>')" >-->
			<!--						</td>-->
			<!--					</tr>-->
			<!--					--><?php
			//					$num_probe = $num_probe + (int)$arrDatum['obiem'];
			//				}
			//			}
			?>

		</table>
		<input type="hidden" name="ID" value="<?= (int)$output_array[1] ?>">
		<!--        <input type="submit" value="Сохранить изменения">-->
	</form>
	<br/>
	<!--<input type="button" value="Перейти к испытаниям" class="add" onclick="window.open ('/results_isp.php?ID=< ?= $_GET['ID'] ?>')" < ?=($status == 'Выдан') ? 'disabled' : ''?> >-->
	<?php
	//if (!empty($_GET['ID']) && in_array((int)$_GET['ID'], $newResult->arrIdDeal) || !empty($_GET['ID']) && (int)$_GET['ID'] === 5138  || !empty($_GET['ID']) && (int)$_GET['ID'] === 5479 || !empty($_GET['ID']) && (int)$_GET['ID'] === 5697 || !empty($_GET['ID']) && (int)$_GET['ID'] === 5893) {
	if (!empty($_GET['ID']) && (in_array((int)$_GET['ID'], $newResult->arrIdDeal) || (int)$_GET['ID'] > $newResult->idDealВefore)) {
		$selectedProtocolData = null;
		if (!empty($sql['ID'])) {
			$selectedProtocolData = $db->select("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}", [$sql['ID']]);
		}
		$idFistProtocol = !empty($selectedProtocolData[0]['ID']) ? $selectedProtocolData[0]['ID'] : 0;
		?>

        <?php if ($_GET['ID'] > RESULT_REFACTORING_START_ID): ?>
            <input type="button" value="Перейти к испытаниям" class="add"
                   onclick="window.open ('/ulab/result/card/<?= $_GET['ID'] ?>?protocol_id=<?= $idFistProtocol ?>')" <?= ($status == 'Выдан') ? 'disabled' : '' ?> >
        <?php else: ?>
            <input type="button" value="Перейти к испытаниям" class="add"
                   onclick="window.open ('/results_isp.php?ID=<?= $_GET['ID'] ?>&ID_P=<?= $idFistProtocol ?>')" <?= ($status == 'Выдан') ? 'disabled' : '' ?> >
        <?php endif; ?>

	<?php } else { ?>
        <?php if ($_GET['ID'] > RESULT_REFACTORING_START_ID): ?>
            <input type="button" value="Перейти к испытаниям" class="add"
                   onclick="window.open ('/ulab/result/card/<?= $_GET['ID'] ?>')" <?= ($status == 'Выдан') ? 'disabled' : '' ?> >
        <?php else: ?>
            <input type="button" value="Перейти к испытаниям" class="add"
                   onclick="window.open ('/results_isp.php?ID=<?= $_GET['ID'] ?>')" <?= ($status == 'Выдан') ? 'disabled' : '' ?> >
        <?php endif; ?>

		<?php
	}
} else {
	header('Location: /probe_test.php');
	die();

	$deals = CCrmDeal::GetList(array('DATE_CREATE' => 'DESC', 'STAGE_ID' => 'DESC'));
	// $order = array('sort' => 'asc');
	// $tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
	// $rsUsers = CUser::GetList($order, $tmp)->Fetch();


	?>
	<table id="probe">
		<thead>
		<th>Номер акта</th>
		<th>Основание</th>
		<th>Дата поступления</th>
		<th>Наименование заявителя</th>
		<th>Наименование продукции</th>
		<!--<th>Шифр образца (пробы)</th>-->
		<th>Ответственный</th>
		<!--<th>Наименование лаборатории</th>-->
		<th>Функции</th>
		</thead>
		<tbody>
		<?php
		$res_probe = $DB->Query("SELECT * FROM `ba_probes` ORDER BY `ID`")->Fetch();
		while ($deal = $deals->Fetch()) {

			$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $deal['ID'])->Fetch();
			$num_dog = "Договор №" . $deal['ID'] . " от " . date('d.m.Y', strtotime($deal['DATE_CREATE']));
			?>

			<tr>
				<td style="color:<?= $color ?>;"><a
							href="/probe_card.php?ID=<?= $deal['ID'] ?>&ACT_NUM=<?= $res_tz['ACT_NUM'] ?>"><?= ($res_tz['ACT_NUM'] ? $res_tz['ACT_NUM'] : $deal['ID']) ?>
				</td>
				<td style="color:<?= $color ?>;"><?= ($res_tz['DOGOVOR_NUM'] ? $res_tz['DOGOVOR_NUM'] : $num_dog) ?></td>
				<td style="color:<?= $color ?>;"><?= $res_tz['DATE_ACT'] ? date("d.m.Y", strtotime($res_tz['DATE_ACT'])) : date("d.m.Y", strtotime($deal['DATE_CREATE'])) ?></td>
				<td style="color:<?= $color ?>;"><?= $deal['COMPANY_TITLE'] ?></td>
				<td style="color:<?= $color ?>;"><?= $deal['UF_CRM_1560745341'][0] ? $deal['UF_CRM_1560745341'][0] : implode(', ', $deal['UF_CRM_1569151529509']) ?></td>
				<!--<td style="color:<? //= $color ?>;"><? //= $res_probe['NAME'] ?></td>-->
				<td style="color:<?= $color ?>;"><?= $deal['ASSIGNED_BY_LAST_NAME'] ?> <?= $deal['ASSIGNED_BY_NAME'] ?></td>
				<!--<td style="color:<? //= $color ?>;"><? //= $res_probe['NAME'] ?></td> -->
				<td><?php
					if (in_array('1', $USER->GetUserGroupArray())) {
						echo " <a href='/crm/deal/details/" . $deal['ID'] . "/'>Сделка</a> | ";
					} else {
					};
					?>
					<a href="/probe.php?ID=<?= $deal['ID'] ?>">Открыть</a>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php
}
(isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';
?>
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
<!--	<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>-->
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>

	<script>
        $(document).ready(function () {
            $('#probe').DataTable({
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
				"colReorder": true,
				"columnDefs": [{ "type": "natural-nohtml", "targets": 0 },{ "sType": "ruDate", "targets": 2 },{ "type": "num", "targets": 6 },
				{
				  "targets": 4,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				}
				],
				"order": [[ 0, "desc" ]],
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

	
	$(document).ready(function () {
		var addEdit = $('.add.edit1');

		addEdit.each(function(index, value) {
			if ($(value).prop('disabled')) {
				$(value).css('filter', 'grayscale(100%)');
			}
		});

		$('#probe_in_lab').on('change', function() {
				$.ajax({
					method: 'POST',
					url: '/probe_in_lab.php',
					data: {ID: <?= $_GET['ID']?>,
						value: document.getElementById('probe_in_lab').checked? 1 : 0,
					},
					success: function() {
						alert ('ПРОБЫ ПРИНЯТЫ. МОЖНО ПРИСТУПАТЬ К ИСПЫТАНИЯМ.')
					}
				});
			});
	});
	

    </script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
