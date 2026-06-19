<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Формирование ОА");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

if(!empty($_POST['n_oa']))
{
	
	// dump($_POST["accuracy"]);
	foreach($_POST["n_oa"] as $key => $n_oa)
	{
		$DB->Query("UPDATE `ba_gost_new` SET `NUM_OA` = ".$n_oa.", `ACCURACY` = ".$_POST["accuracy"][$key].",
		`NORM1` = ".$_POST["norm1"][$key].", `NORM2` = ".$_POST["norm2"][$key].",
		`ED` = '".$_POST["ed"][$key]."' WHERE `ID`=".$key);
	}
	
}

	$res_oa = $DB->Query("SELECT * FROM `ba_gost_new` ORDER BY `NUM_OA` ASC");	

	?>
	<form method='post'>
	<table id="results_isp" style="border: 1px">
	<thead>
	<tr>
	<th>ID</th>
	<th>ОА</th>
	<th>ГОСТ</th>
	<th>Характеристика</th>
	<th>Кол-во знаков</th>
	<th>Норма 1</th>
	<th>Норма 2</th>
	<th>Ед. изм.</th>
	<tr>
	<th></th>
	<th></th>
	<th><input type="text" name="gost_filter" id="gost_filter" style="margin:0px" value=""></th>
	<th></th>
	<th></th>
	<th></th>
	<th></th>
	<th></th>

	</thead>
	<tbody>
	
	<?

	while($gost = $res_oa->Fetch())
	{ 
		if($gost['NUM_OA']){
					
			echo "<tr id=".$gost['ID']."><td> ID: ".$gost['ID']."</td><td style='width:100px'><input type='number' value='".$gost['NUM_OA']."' class='num_oa' style='width:100%; margin: 11px 0 0 0px' name='n_oa[".$gost['ID']."]'></td><td style='width:250px'><a href='/obl_acc_new.php?ID=".$gost['ID']."' target='_blank'><b>".$gost['GOST']." ".$gost['GOST_PUNKT']."</b></a></td><td style='width: 400px'>".$gost['SPECIFICATION']."</td>
			<td><input type='text' value='".$gost['ID']."' name='gost' hidden><input type='number' value='".$gost['ACCURACY']."' class='sign_count' name='accuracy[".$gost['ID']."]' style='width:100%; margin: 11px 0 0 0px'></td>
			<td><input type='number' step='0.01'  value='".$gost['NORM1']."' class='norm1' name='norm1[".$gost['ID']."]'  style='width:100%; margin: 11px 0 0 0px'></td>
			<td><input type='number' step='0.01'  value='".$gost['NORM2']."' class='norm2' name='norm2[".$gost['ID']."]' style='width:100%; margin: 11px 0 0 0px'></td>
			<td><input type='text' value='".$gost['ED']."' class='ed' name='ed[".$gost['ID']."]' style='width:100%; margin: 11px 0 0 0px'></td>
			</tr>";
		}
	}
	?>
	<!--<input type='button' name='bt' value='Сохранить'>-->
	
	</tbody>
	</table>
<input type="submit" value="ОКОКОКОКОК">
	</form>
<script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/fixed.css"/>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/filtering/type-based/html.js"></script>
	
	
	
	<script>
        $(document).ready(function () {
            table = $('#results_isp').DataTable({
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
				"order": [[ 0, "asc" ]],
				"iDisplayLength": 50,
				"sort": false,
				"paginate": true,
				"dom": 'Blfrtip',
				"info": false,
				"buttons": [ 'copy', 'excel', 'print' ]
				
            });
			$('#gost_filter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			} );
        });
		
		
    </script>     
			
<script>
$('input[name=bt]').on("click", function () {
				
			 
				tr = $(this).parent().parent();
				tr_dataid = tr.attr('id');
				sign_inp = tr.find('.sign_count');
				sign = sign_inp[0].value;
				norm1_inp = tr.find('.norm1');
				norm1 = norm1_inp[0].value;
				norm2_inp = tr.find('.norm2');
				norm2 = norm1_inp[0].value;
				num_inp = tr.find('.num_oa');
				num_oa = num_inp[0].value;
				ed_inp = tr.find('.ed');
				ed = ed_inp[0].value;
				
				 console.log(tr_dataid);
				// console.log(id_dog);
				
					$.ajax({

					  url: 'oa_upd_go.php?gost='+tr_dataid+'&sign='+sign+'&norm1='+norm1+'&norm2='+norm2+'&ed='+ed+'&num='+num_oa,
		
					  success: function(data) {
						alert('OK');
					  }
					
					});
				
		
			
		});
</script>
<?

	
 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>