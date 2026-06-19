<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";
?>
<link rel="stylesheet" type="text/css" href="/ulab/assets/css/bootstrap.min.css">
<style>
	.date-control {
		position: relative;
		flex: 1 1 auto;
		width: 1%;
		min-width: 0;
	}

	.input-group {
		position: relative;
		display: flex;
		flex-wrap: wrap;
		align-items: stretch;
		width: 50%;
	}
</style>
<div class="input-group mb-3">
	<span class="input-group-text">С</span>
	<input class="date date-control" id="from" type="date" name="from" value="<?= date('Y-m-01') ?>">
	<span class="input-group-text">ПО</span>
	<input class="date date-control" id="to" type="date" name="to" value="<?= date('Y-m-d') ?>">
</div>

<!--<label for="from">С</label>-->
<!--<input class="date" id="from" type="date" name="from" value="--><?//= date('Y-m-01') ?><!--">-->
<!--<label for="to">ДО</label>-->
<!--<input class="date" id="to" type="date" name="to" value="--><?//= date('Y-m-d') ?><!--">-->
<!--<button>Save</button>-->

<table class="table table-striped table-hover">
	<th></th>
	<th>кол-во за период/новых(завершенных)</th>
	<th>Шлыкова</th>
	<th>Голикова</th>
	<th>Булатова</th>
	<tr>
		<td>Заявок</td>
		<td id="request"></td>
		<td id="req_shlykova"></td>
		<td id="req_golikova"></td>
		<td id="req_bulatova"></td>
	</tr>
	<tr>
		<td>Всего проб принято</td>
		<td id="probes"></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Клиентов</td>
		<td id="clients"></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Новых договоров</td>
		<td id="dogovor-td"></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</table>
<script
		src="//code.jquery.com/jquery-3.3.1.min.js"
		integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
		crossorigin="anonymous"></script>

<script>
	function get(from, to) {
		$.ajax({
			method: 'post',
			url: 'ajax_stat_manager.php',
			data: {'from': from, 'to': to},
			success: function (data) {
				let val = JSON.parse(data)
				console.log(val)
				$('#request').html(val['request']);
				$('#req_shlykova').html(val['req_shlykova']);
				$('#req_golikova').html(val['req_golikova']);
				$('#req_bulatova').html(val['req_bulatova']);
				$('#clients').html(val['client']);
				$('#dogovor-td').html(val['dogovor']);
				$('#probes').html(val['probe']);
			}
		})
	}

	$(document).ready(function () {
		let from = $('#from').val();
		let to = $('#to').val();
		get(from, to);
	})
	$('.date').on('input', function () {
		let from = $('#from').val();
		let to = $('#to').val();
		get(from, to);
	})
</script>
