<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("Журнал партии");

$res = $DB->Query("SELECT * FROM `REACT_SUPPLY` as rs WHERE `id`={$_GET['ID']}")->Fetch();
if (!empty($_POST)) {
    $sum = !empty($_POST['supply_total']) ? $_POST['supply_total'] : '';
	$income = !empty($_POST['income']) ? $_POST['income'] : '0';
	$expense = !empty($_POST['expense']) ? $_POST['expense'] : '0';
	$DB->Query("INSERT INTO `supply_rem` SET `date` = NOW(), `income` = {$income}, `expense` = {$expense}, `id_supply` = {$_GET['ID']}");

	$total_sum = $sum + $income - $expense;
    $DB->Query("UPDATE `REACT_SUPPLY` SET `balance` = {$total_sum} WHERE `id`={$_GET['ID']}");
	header('Location:'.$_SERVER['REQUEST_URI']);
}

?>
<style>
    .number {
        text-align: center;
    }
    .inp_supply {
        margin: auto;
    }
    .count-td {
        font-weight: bold!important;
        text-align: center;
    }
</style>
<div class="return" onclick="window.history.back()"></div>
<div class="download" onclick="window.open ('/protocol_generator/supple.php?ID=<?= $_GET['ID'] ?>', '_blank')"></div>
<input type="hidden" name="unit_hidden" value="<?= $res['unit'] ?>">
<input type="hidden" name="id" value="<?= $_GET['ID'] ?>">

<h2>Журнал партии №<?=$res['number']?> от <?= date("d.m.Y", strtotime($res['date_create']))?></h2>

<!--<form method="post">-->
<!--	<label>Приход/Расход</label><input type="number" name="income" value=""><input type="submit" class="btn" value="ok">-->
<!--</form>-->
<table id="mat_table">
	<thead>
    <tr>
		<th>Дата изменения</th>
		<th class="number">Приход, <?= $res['unit']?></th>
		<th class="number">Расход, <?= $res['unit']?></th>
		<th class="number">Остаток партии, <?= $res['unit']?></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
    <!--</tr>-->
	<form method="post" id="sub" onsubmit="return Сompare();">
	<tr>
		<td><label for="income">Приход/Расход</label></td>
		<td><input type="number" name="income" class="inp_supply" value=""></td>
		<td><input type="number" name="expense" class="inp_supply" value=""></td>
		<td><input type="submit" class="btn sbm_supply" style="align-items: center" value="OK"></td>
		<td></td>
	</tr>
    <input type="hidden" name="supply_total">
	</form>
    <?php
	$res = $DB->Query("SELECT * FROM `supply_rem` WHERE `id_supply`={$_GET['ID']}");
	while ($row = $res->Fetch()):
   ?>
		<tr>
			<td><?= date('d.m.Y', strtotime($row['date']))?></td>
			<td class="number"><?= $row['income']?></td>
			<td class="number"><?= $row['expense']?></td>
			<td></td>
			<td></td>
		</tr>
	<?php endwhile;?>
		<tr>
			<td></td>
			<td class="count-td count-3"></td>
			<td class="count-td count-4"></td>
			<td class="count-td count-5"></td>
			<td></td>
 	 </tbody>
 </table>

<script
		src="//code.jquery.com/jquery-3.3.1.min.js"
		integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
		crossorigin="anonymous"></script>
<script>
    var total = ''
    var unit = $('input[name=unit_hidden]').val()
	$(document).ready(function () {
		function col_sum(table, col) {
			let sum = 0, tr = table.find("tr"); // Все tr внутри table.
			for (let i = 0; i < tr.length - 1; i++) {
				let td = $(tr[i]).find("td"); // Все td внутри строки; th не учитываем.
				if (!td.length || !td[col - 1]) continue;
				// В tr нет td, или есть, но такого столбика не существует.

				sum += Number(td[col - 1].textContent) || 0;
				// Если в textContent не число, Number вернет NaN, возьмется 0 вместо него.
			}

			return sum;
		}

		let income = col_sum($('#mat_table'), 2)
		let expense = col_sum($('#mat_table'), 3)
		$('.count-3').text(col_sum($('#mat_table'), 2)+ ' ' +unit)
		$('.count-4').text(col_sum($('#mat_table'), 3)+ ' ' +unit)
		total = income-expense
		$('.count-5').text('Остаток в партии: ' +total+ ' ' +unit)
        $('input[NAME=supply_total]').val(total)
	});
	//
	function Сompare() {
		let ex = $('input[NAME=expense]').val()
		if (ex != '' && ex > total) {
			alert('Количество списываемого больше остатка. Можно списать ' +total+ ' ' +unit)
			return false;
		}

		return true;
	}

</script>
