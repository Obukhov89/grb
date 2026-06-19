<? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';?>
<div class='formActPP'>
	<label>Номер акта ПП:</label>
	<input type="number" class="actField" name="actNumber" value="" required><br>
	<label>Дата акта ПП:</label>
	<input class="actField" type="date" name="DATE_ACT" value="<?= date("Y-m-d") ?>" required><br>
	<label>Место отбора проб:</label>
	<input class="actField" type="text" name="PLACE_PROBE" value=""><br>
	<label>Дата отбора проб:</label>
	<input class="actField" type="date" name="DATE_PROBE" value="<?//= date("Y-m-d") ?>" size="20"><br>
</div>
<? exit ?>