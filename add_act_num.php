<? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require($_SERVER["DOCUMENT_ROOT"] . "/Classes/CDB.php");

function pre($obj) {
	echo('<pre>');
	print_r($obj);
	echo('</pre>');
}
$db = new CDB();
use \Bitrix\Main\Loader;
Loader::IncludeModule("crm");

$actType = $_POST['actType'];
$curYear = date("Y");

$checkUniq = $db->select("SELECT * FROM ACT_BASE WHERE ID_Z =" . (int)$_GET['id_z']);
if (empty($checkUniq)) {
	$db->queryExec("INSERT INTO ACT_BASE SET ID_Z =" . (int)$_GET['id_z']);
}
if ($actType == 2) {
	$res = $db->select("SELECT ID FROM ba_tz WHERE ID_Z =" . (int)$_GET['id_z']);
//	$control_num = $DB->Query("SELECT `act_type` FROM `ACT_BASE` WHERE `act_type` = 2 AND `ACT_DATE` > '{$curYear}-01-01'");
//	$allNum = $control_num->SelectedRowsCount();
	$nums = $db->select("SELECT max(CONVERT(ACT_NUM, UNSIGNED INTEGER )) as max FROM `ACT_BASE` WHERE `act_type` = 2 AND `ACT_DATE` > '{$curYear}-01-01'");
	$num_act = $nums[0]['max']+1;
} else {
//условие увеличения номера акта в зависимости от базы
$res = $db->select("SELECT ID FROM ba_tz WHERE ID_Z =" . (int)$_GET['id_z']);

$nums = $db->select("SELECT max(CONVERT(ACT_NUM, UNSIGNED INTEGER )) as max FROM `ACT_BASE` WHERE `ACT_DATE` > '{$curYear}-01-01'");
$num_act = !empty($checkUniq[0]['ACT_NUM']) ? $checkUniq[0]['ACT_NUM'] : $nums[0]['max']+1;
}




//установить проверку успешнго присвоения номера акта
if (!empty($num_act) &&  !empty($_GET['id_z'])) {
	$dateAct = !empty($_POST['actDate']) ? $_POST['actDate'] : '';

	$placeProbe = !empty($_POST['samplePlace']) ? $_POST['samplePlace'] : '';

	$dateProbe = $_POST['sampleDate'] ?? '';

	$probe_proizv = !empty($_POST['sampleMaker']) ? $_POST['sampleMaker'] : 'Заказчик';
	$selectionType = $_POST['exampleCheck1'] ? '1' : '';

	$quarry = !empty($_POST['quarry']) ? $_POST['quarry'] : 'NULL' ;

	$deliveryman = !empty($_POST['deliveryman']) ? $_POST['deliveryman'] : '' ;

	$y_date_act = '';
	if (!empty($dateAct)) {
		$y_date_act = date("Y", strtotime($dateAct));
		if ($y_date_act != '2020') {
			$y_date_act = "/" . substr($y_date_act, 2);
		} else {
			$y_date_act = "/" . $y_date_act;
		}
	}

	$num_act_table = $num_act . $y_date_act;

//внести акт в новую базу
	$DB->Query(
		"UPDATE `ACT_BASE` SET 
			`ACT_NUM`='{$num_act}', 
			`ACT_DATE`='{$dateAct}', 
			`ID_TZ` = {$res[0]['ID']},
			`PLACE_PROBE`='{$placeProbe}', 
			`DATE_PROBE`='{$dateProbe}', 
			`PROBE_PROIZV`='{$probe_proizv}', 
			`deliveryman` = '{$deliveryman}', 
			`act_type` = '{$actType}' 
		WHERE `ID_Z` = '{$_GET['id_z']}'");


	$where = "mtr.ID_DEAL = {$_GET['id_z']}";

	$stmpMtr = $DB->Query(
		"SELECT 
                        @row_number:=CASE
                            WHEN @deal = mtr.ID_DEAL THEN @row_number + 1
                            ELSE 1
                        END AS num,
                        @deal:=mtr.ID_DEAL as ID_DEAL, ptm.id AS ptm_id, mtr.ID mtr_id, mtr.ID_MATERIAL,
                        ab.ACT_NUM, ab.ACT_DATE 
                    FROM 
                        MATERIALS_TO_REQUESTS AS mtr, ACT_BASE AS ab, 
                        probe_to_materials as ptm, 
                        (SELECT @deal:=0,@row_number:=0) as t
                    WHERE 
                        mtr.ID_DEAL = ab.ID_Z AND ptm.material_request_id = mtr.ID AND {$where}
                    ORDER BY 
                        mtr.ID_DEAL, mtr.ID, ptm.id"
	);

	while ($row = $stmpMtr->Fetch()) {
		$date = strtotime($row['ACT_DATE']);
		$year = date("Y", $date)%10 ? substr(date("Y", $date), -2) : date("Y", $date);

		$cipher = $row['ACT_NUM'] . '.' . $row['num'] . '/' . $year;

		$date = [
			'cipher' => "'{$cipher}'"
		];

		$DB->Update("probe_to_materials", $date, "WHERE id = {$row['ptm_id']}");
	}


	//собирает шифры для проб в заявке, для таблицы ulab_material_to_request
	$umtrWhere = "umtr.deal_id = {$_GET['id_z']}";

	$umtr = $DB->Query(
		"SELECT
               @row_number:=CASE
                WHEN @deal = umtr.deal_id THEN @row_number + 1
                ELSE 1
            END AS num,
              @deal:=umtr.deal_id as deal_id, umtr.id umtr_id, umtr.material_id, ab.ACT_NUM, ab.ACT_DATE, @row_number, @deal
            FROM 
              ulab_material_to_request AS umtr, 
              ACT_BASE AS ab, 
              (SELECT @deal:=0,@row_number:=0) as t
            WHERE 
              umtr.deal_id = ab.ID_Z AND {$umtrWhere} 
            ORDER BY 
              umtr.deal_id, umtr.id"
	);

	while ($row = $umtr->Fetch()) {
		$date = strtotime($row['ACT_DATE']);
		$year = date("Y", $date)%10 ? substr(date("Y", $date), -2) : date("Y", $date);

		$cipher = $row['ACT_NUM'] . '.' . $row['num'] . '/' . $year;

		$date = [
			'cipher' => "'{$cipher}'"
		];

		$DB->Update("ulab_material_to_request", $date, "WHERE id = {$row['umtr_id']}");
	}


	$actNum = $db->selectRow("SELECT * FROM ACT_BASE WHERE ID_Z={$_GET['id_z']}");

	$resUp = $DB->Query("UPDATE `ba_tz` SET `ACT_NUM`='{$num_act}', `DATE_ACT`='{$dateAct}', `NUM_ACT_TABLE`='{$num_act_table}', `PROBE_IN_LAB`='1', `DESCRIPTION` = '{$_POST['DESCRIPTION']}', `SELECTION_TYPE` = '{$selectionType}' ,`QUARRY_ID`= {$quarry} WHERE `ID_Z`={$_GET['id_z']}");

	$resAct = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['id_z'])->Fetch();

	$deal = CCrmDeal::GetByID((int)$_GET['id_z']); //Берём значение сделки

	$date_history = date('Y-m-d H:i:s');

	$assigned = CUser::GetByID($USER->GetID())->arResult[0];

	$assigned_name = !empty($assigned['NAME']) ? $assigned['NAME'] : '';

	$assigned_lastname = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';

	$assigned_user = $assigned_name . ' ' . $assigned_lastname;

	$ID_P = $resAct['NUM_P'] ? $resAct['NUM_P'] : 'номер не присвоен';


	$tz = !empty($resAct['ID']) ? $resAct['ID'] : '';


	$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';

	$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date_history . "','" . $assigned_user . "','" . $ID_P . "','" . (int)$tz . "','" . $USER->GetID() . "', 'Формирование АКТа приемки проб', '" . $num_request . "')");



	$id_z = !empty($resAct['ID_Z']) ? (int)$resAct['ID_Z'] : '';
	if ($resAct['ID']) {
		$history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$tz . " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
		$history_ass = $history['ASSIGNED'];
		$history_date = date("d.m.Y H:i:s", strtotime($history['DATE']));
		$history_date_null = date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
		if ($history_date < $history_date_null) $history_date = '';


		$assignet_history = $history_ass . " " . $history_date;



		$update_assignet_ba_tz = $DB->Query("UPDATE `ba_tz` SET `USER_HISTORY` = '" . $assignet_history . "', `STAGE_ID`='FINAL_INVOICE', `STAGE_NUMBER`= " . (int)1 . " WHERE `ID_Z` = " . $id_z);

	}


//	if (!empty($actNum['ACT_NUM']) && $actNum['ACT_NUM'] != 0) {
		$y_date = null;
		if (!empty($actNum['ACT_DATE'])) {
			$y_date = date("Y", strtotime($actNum['ACT_DATE']));
			if ($y_date != '2020') $y_date = "/" . substr($y_date, 2);
			else $y_date = "/" . $y_date;
		}

		$y_date = !empty($y_date) ? $y_date : '';

		$probeEdit = !empty($resAct['PROBE']) ? unserialize($resAct['PROBE']) : '';

		$sh=1;
		if (!empty($probeEdit)) {
			foreach ($probeEdit as $keyProbe => $valProbe) {

				$shNumber = !empty($probeEdit[$keyProbe]['sh_number'][0]) ? explode('.', $probeEdit[$keyProbe]['sh_number'][0])[0] : '';

				if (!empty($probeEdit[$keyProbe]['sh_number']) && $shNumber != 0 && $shNumber != '') {
					continue;
				}
				$probeEdit[$keyProbe]['number'] = $resAct['ACT_NUM'];
				$probeEdit[$keyProbe]['mesto_data'] = $placeProbe . '; ' . $dateProbe;
				$probeEdit[$keyProbe]['sh_number'] = [];

				if (!empty($probeEdit[$keyProbe]['number_probe'])) {
					foreach ($probeEdit[$keyProbe]['number_probe'] as $keyNumber => $valNumber) {
						$probeEdit[$keyProbe]["sh_number"][$keyNumber] = $resAct['ACT_NUM'] . '.'
							. (int)($sh) . $y_date;

						$sh++;

					}
				}

			}
		} else {
			$probe_arr = [];
			$res_sh_num = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=" . (int)$_GET['id_z']);
			$h = 0;
			while ($probeinform = $res_sh_num->Fetch()) {
				$z = 0;
				$NumProbe = [];
				for ($z = 0; $z < $probeinform['OBIEM']; $z++) {
					$NumProbe[$z] = $actNum['ACT_NUM'] . '.' . $sh . $y_date;
					$sh++;
					$probe_arr[$h] = $NumProbe;
					$DB->Query("INSERT INTO `probe_to_materials` SET `material_request_id` = {$probeinform['ID']}, `cipher` = '{$NumProbe[$z]}'");
				}
				$h++;
			}
			$probe = json_encode($probe_arr);
			$DB->Query("UPDATE `ACT_BASE` SET `PROBE`= '{$probe}'
			WHERE `ID_Z`=" . (int)$_GET['id_z']);
		}
			$probe = !empty($probeEdit) ? serialize($probeEdit) : '';
			$DB->Query("UPDATE `ba_tz` SET `PROBE`='" . $probe . "'
			WHERE `ID_Z`=" . (int)$_GET['id_z']);
//	}
}
Header('Location: /ulab/request/card/' . $resAct['ID_Z'] . '?ACT_NUM=' . $num_act);
?>
