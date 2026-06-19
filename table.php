<?php
//require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

require($_SERVER["DOCUMENT_ROOT"] . "/Classes/CDB.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DataStorage.php');

include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";

$db = new CDB();
?>
<form action="" method="post">
	<label style="margin: 20px;">БАЗА</label><input type="text" name="base">
	<label style="margin: 20px;">СТОЛБЕЦ</label><input type="text" name="column">
	<label style="margin: 20px;">ПРИЗНАК</label><input type="text" name="condition">
	<label style="margin: 20px;">ЗНАЧЕНИЕ</label><input type="number" name="number">
	<input type="submit" title="Внести критерий">
</form>
<?php
//var_dump($_POST);
if (!empty($_POST['column'])) {$col = $_POST['column'];} else {$col = '*';}
$sql1 = "SELECT " . $col . " FROM " . $_POST['base'];
if (!empty($_POST['condition'])) {
	$sql1 .= " WHERE " . $_POST['condition'] . " = " . $_POST['number'];
}
$row = $db->select($sql1);
//var_dump($row);
foreach ($row as $arr) {
//function $viewTableMysql ()
//{
	echo "<table border style='border-collapse: collapse; text-align: center; '><tr>";
	foreach ($arr as $key => $elem) {
		echo "<td style='padding: 5px;'>$key</td>";
		$va[] = $elem;
	}
//	var_dump($elem);
	echo '</tr>';
	echo '<tr>';
	foreach ($va as $val) {

		echo "<td style='padding: 5px;'>$val</td>";
	}
	echo '</tr><br>';
}

?>
</table>
<br>
<br>
<br>
<br>
<?php //var_dump($arr);?>
<!--"SELECT " . $col . " FROM " . $_POST['base'] .   " WHERE " . $_POST['condition'] . " = " . $_POST['number']-->
