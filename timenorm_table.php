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
$APPLICATION->SetTitle("Нормочасы и длительность испытаний"); 

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

if(!empty($_POST['timenorm'])||!empty($_POST['timegost']))
{

	
	
	foreach($_POST["timenorm"] as $id => $timeNorm)
	{
		if($timeNorm)
			$DB->Query("UPDATE `ba_gost` SET `TIME_NORM` = ".$timeNorm." WHERE `ID`=".$id);
	}
	
	foreach($_POST["timegost"] as $id => $timeGost)
	{
		
		$timeGostSer = serialize($timeGost);
		
		if($timeGost[0]||$timeGost[1])
			$DB->Query("UPDATE `ba_gost` SET `TIME_NORM_GOST` = '".$timeGostSer."' WHERE `ID`=".$id);
	}
	
	
}


?>
<style>
.results_isp_filter, .dataTables_filter { display: none; }
.results_isp_length, .dataTables_length { display: none; }
#gost-table th { background-image: none; }
.sorting { background-image: none; }
input[type="number"]{
width: 80%;
}

</style>


	<form method='post'>
    <input type='submit' name='bt' value='Сохранить'>
	<br />
	<table id="gost-table">
	<thead>
   
	<tr>
		
		<th>ГОСТ<th>
		<th>Характеристика</th>
		<th>Нормочасы</th>
		<th>Длительность по ГОСТ, дн.</th>
		<th>Длительность по ГОСТ, ч.</th>
	
	<tr>
		<th><input type="text" name="gost_filter" id="gost_filter" style="margin:0px" value=""><th>
		<th><th>
		<th></th>
		<th></th>
		
	
	</thead>
	<tbody>
	<?php 
	
	$res_oa = $DB->Query("SELECT * FROM `ba_gost` ORDER BY `NUM_OA` DESC");	
	
	while($gost = $res_oa->Fetch())
	{ 
		if(!$gost['NON_ACTUAL'])
		{
			$timeNorm = $gost['TIME_NORM'];
			$timeGost = unserialize($gost['TIME_NORM_GOST']);
						
			echo "<tr id=".$gost['ID'].">
			<td ><b>".$gost['GOST']." ".$gost['GOST_PUNKT']."</b></td><td></td><td >".$gost['SPECIFICATION']."</td>
			<td><input type='number' name='timenorm[".$gost['ID']."]' step='0.1' value=".$timeNorm."></td>
			<td><input type='number' name='timegost[".$gost['ID']."][0]' step='0.1' value=".$timeGost[0]."></td>
			<td ><input type='number' name='timegost[".$gost['ID']."][1]' step='0.1' value=".$timeGost[1]."></td>
			</tr>";
		}
	}
?>
	
	</tbody>
	
	</table>
	</form>


	
	<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/fixed.css"/>
	
	
	
    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<!--<script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>-->
	<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/filtering/type-based/html.js"></script>
	
	
	
	<script>
        $(document).ready(function () {
            table = $('#gost-table').DataTable({
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
				"fixedHeader": {
					header: true,
					footer: false
				},
				"order": [[ 1, "asc" ]],
				"iDisplayLength": 50,
				"sort": false,
				"paginate": false,
				"dom": 'Blfrtip',
				"info": false,
				"buttons": [ 'copy', 'excel', 'print' ]
				
            });
			$('#gost_filter').on( 'keyup', function () {
				table
					.columns( 0 )
					.search( this.value )
					.draw();
			} );
        });
		
		
    </script>     
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>