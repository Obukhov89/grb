<form action="clearbd.php" method="post">
	<label>Clear base WHERE ID_Z</label>
	<input type="number" name="clear">
	<input type="submit" title="Стереть данные">
</form>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/Classes/CDB.php");
$db = new CDB();

$actBase = "UPDATE `ACT_BASE` SET  `ACT_NUM`= 0, `ACT_DATE`= NULL, `PLACE_PROBE`= NULL, `DATE_PROBE`= NULL, `SELECTION_TYPE`= NULL, `PROBE_PROIZV`= NULL WHERE `ID_Z` =" . (int)$_POST['clear'];
$ba_tz = "UPDATE `ba_tz` SET `ACT_NUM`= NULL, `PROBE`=NULL, `DATE_ACT`= NULL, `NUM_ACT_TABLE`= NULL WHERE `ID_Z`=" . (int)$_POST['clear'];

if (!empty($_POST['clear'])) {
	$act_base_clear = $db->queryExec("$actBase");
	$ba_tz_clear = $db->queryExec("$ba_tz");

//	if ($act_base_clear == 1 && $ba_tz_clear == 1){
//		echo "УСПЕХ!!!!!!";
//		} else {
//		echo "ОШИБКА!!!";
//
//	}
}
?>
