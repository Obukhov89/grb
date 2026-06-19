<?php header("Location: /ulab/gost/listPrice/"); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Формирование прайса");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

if (!empty($_GET['ID_CONTRACT'])) {
	if (!empty($_POST['gost'])) {
		//dump($_POST);
		$DB->Query("UPDATE `PRICE_FOR_CONTRACTS` SET `PRICE`=".tofloat($_POST['price_new'][0])." WHERE `ID_CONTRACT`=".(int)$_GET['ID_CONTRACT']." AND `ID_GOST` =".$_POST['gost']);	  
	}

	$idContract = (int)$_GET['ID_CONTRACT'];

	$res_oa = $DB->Query(
	        "SELECT g.NON_ACTUAL,g.GOST_TYPE,g.ID,g.IN_OA,g.GOST,g.GOST_PUNKT,g.SPECIFICATION, p.PRICE 
                FROM `ba_gost` g, `PRICE_FOR_CONTRACTS` p 
                WHERE g.ID = p.ID_GOST AND p.ID_CONTRACT = {$idContract}
                ORDER BY p.`PRICE` ASC");

	$changeArr = [2, 14, 1246, 1252, 1254, 25, 1231, 1114, 1118, 172, 1130, 1132, 3058, 1092, 2997, 1098, 1106, 2655, 66, 1075, 105, 106, 107, 955, 957, 882, 884, 885, 886, 887, 890, 558, 562,
	310, 325, 870, 2775, 2776, 860, 2911, 3021, 438, 592, 3006, 3007, 3008, 590, 2734, 2735, 594, 595, 596, 1896, 669, 467, 468, 3097, 2961, 2962, 2761, 495, 1831, 1832, 601, 2892,
	1841, 611, 612, 622, 3019, 1856, 3022, 625, 627, 2879, 614, 616, 618, 620, 621, 651, 2748, 893, 894, 895, 896, 898, 1154, 1209, 1210, 962, 939, 940, 941, 918, 2777, 910, 911,
	912, 913, 914, 915, 916, 929, 189, 219, 1463, 1508, 2367, 357, 358, 359, 998, 2881, 2875, 3080, 307, 564, 312, 58, 314, 1084, 2372, 1115, 611];
	
	$resContract = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=".(int)$_GET['ID_CONTRACT'])->Fetch();	
	echo "<b>Договор ".$resContract['NUMBER']." от ".ruDate($resContract['DATE'])."</b><br><br>";
	?>
	<table id="results_isp" style="border: 1px">
	<thead>
	<tr>
	<th>ID</th>
	<th>ОА</th>
	<th>Тип</th>
	<th>ГОСТ</th>
	<th>Характеристика</th>
	<th>Цена</th>
	<th>Новая цена</th>
	<th></th>
	<th>Редактирование</th>
	<tr>
	<th></th>
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

    while ($gost = $res_oa->Fetch()) {
        //$resPriceForContract = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT`=" . (int)$_GET['ID_CONTRACT'] . " AND `ID_GOST`=" . (int)$gost['ID'])->Fetch();
        if (!$gost['NON_ACTUAL'] && $gost['GOST_TYPE'] != 'TU_standart') {//  && $gost['GOST_TYPE']!='TU_group'){// && $gost['PRICE']==0 && $gost['GOST_TYPE']!= 'metodic_otbor'){
            $changeFlag = in_array((int)$gost['ID'], $changeArr) ? 'Возможно' : '';
            $inOA = $gost['IN_OA'] ? 'OA' : '';
            $typeGost = (($gost['GOST_TYPE'] == 'TU_standart'/*||$gost['GOST_TYPE']=='TU_research'||$gost['GOST_TYPE']=='TU_group'*/) ? 'ТУ' : '');
            echo "<form method='post'><tr id=" . $gost['ID'] . "><td> ID: " . $gost['ID'] . "</td><td style='width:100px'>" . $inOA . "</td><td style='width:60px'>" . $typeGost . "</td><td style='width:250px'><b>" . $gost['GOST'] . " " . $gost['GOST_PUNKT'] . "</b></td><td style='width: 400px'>" . $gost['SPECIFICATION'] . "</td><td> цена: " . $gost['PRICE'] .
                "</td><td><input type='text' value='" . $gost['ID'] . "' name='gost' hidden><input type='number' value='' class='price_new' style='width:100%; margin: 11px 0 0 0px'><input type='hidden' value='" . $_GET['ID_CONTRACT'] . "' class='id_dog'></td><td><input type='button' name='bt' value='Сохранить'></td><td>" . $changeFlag . "</td></tr></form>";
        }
    }
	?>
	</tbody>
	</table>
	<?	
	
	
} // если прайс к договору
else
{

	if (!empty($_POST['gost']))
	{
		//dump($_POST);
		$DB->Query("UPDATE `ba_gost` SET `PRICE`=".tofloat($_POST['price_new'][0])." WHERE `ID`=".$_POST['gost']);	  
	}

	$res_oa = $DB->Query("SELECT * FROM `ba_gost` WHERE `NUM_OA_NEW` != 0 AND `NON_ACTUAL` != 1 ORDER BY `PRICE` ASC");

	$changeArr = [2, 14, 1246, 1252, 1254, 25, 1231, 1114, 1118, 172, 1130, 1132, 3058, 1092, 2997, 1098, 1106, 2655, 66, 1075, 105, 106, 107, 955, 957, 882, 884, 885, 886, 887, 890, 558, 562,
	310, 325, 870, 2775, 2776, 860, 2911, 3021, 438, 592, 3006, 3007, 3008, 590, 2734, 2735, 594, 595, 596, 1896, 669, 467, 468, 3097, 2961, 2962, 2761, 495, 1831, 1832, 601, 2892,
	1841, 611, 612, 622, 3019, 1856, 3022, 625, 627, 2879, 614, 616, 618, 620, 621, 651, 2748, 893, 894, 895, 896, 898, 1154, 1209, 1210, 962, 939, 940, 941, 918, 2777, 910, 911,
	912, 913, 914, 915, 916, 929, 189, 219, 1463, 1508, 2367, 357, 358, 359, 998, 2881, 2875, 3080, 307, 564, 312, 58, 314, 1084, 2372, 1115, 611];
	?>
	<table id="results_isp" style="border: 1px">
	<thead>
	<tr>
	<th>ID</th>
	<th>ОА</th>
	<th>Тип</th>
	<th>ГОСТ</th>
	<th>Характеристика</th>
	<th>Цена</th>
	<th>Новая цена</th>
	<th></th>
	<th>Редактирование</th>
	<tr>
	<th></th>
	<th></th>
	<th></th>
	<th><input type="text" name="gost_filter" id="gost_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="spec_filter" id="spec_filter" style="margin:0px" value=""></th>
	<th></th>
	<th></th>
	<th></th>
	<th></th>
	</thead>
	<tbody>
	<?

	while ($gost = $res_oa->Fetch()) {
		if (!$gost['NON_ACTUAL'] && $gost['GOST_TYPE']!='TU_standart') {// && $gost['GOST_TYPE']!='TU_group'){// && $gost['PRICE']==0 && $gost['GOST_TYPE']!= 'metodic_otbor'){
		$changeFlag = in_array((int)$gost['ID'], $changeArr) ? 'Возможно' : '';
		$inOA = $gost['IN_OA'] ? 'OA' : '';
		$typeGost = (($gost['GOST_TYPE']=='TU_standart') ? 'ТУ' : '');
		echo "<form method='post'><tr id=".$gost['ID']."><td> ID: ".$gost['ID']."</td><td style='width:100px'>".$inOA."</td><td style='width:60px'>".$typeGost."</td><td style='width:250px'><b>".$gost['GOST']." ".$gost['GOST_PUNKT']."</b></td><td style='width: 400px'>".$gost['SPECIFICATION']."</td><td> цена: ".$gost['PRICE'].
		"</td><td><input type='text' value='".$gost['ID']."' name='gost' hidden><input type='number' value='' class='price_new' style='width:100%; margin: 11px 0 0 0px'><input type='hidden' value='0' class='id_dog'></td><td><input type='button' name='bt' value='Сохранить'></td><td>".$changeFlag."</td></tr></form>";
		}
	}
	?>
	</tbody>
	</table>
	<?
} //если  основной прайс
	?>
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
					.columns( 3 )
					.search( this.value )
					.draw();
			} );

			$('#spec_filter').on( 'keyup', function () {
				table
					.columns( 4 )
					.search( this.value )
					.draw();
			} );
        });
		
		
    </script>     
			
<script>
$('input[name=bt]').on("click", function () {
				
			 
				tr = $(this).parent().parent();
				tr_dataid = tr.attr('id');
				price_inp = tr.find('.price_new');
				price = price_inp[0].value;
				id_dog_inp = tr.find('.id_dog');
				id_dog = id_dog_inp[0].value;
				console.log(tr_dataid);
				console.log(id_dog);
				if(id_dog=='0')
				{
					$.ajax({

					  url: 'price_upd_go.php?gost='+tr_dataid+'&price='+price,
		
					  success: function(data) {
						alert('Цена изменена');
					  }
					
					});
				}
				else
				{
					$.ajax({

					  url: 'price_upd_go.php?gost='+tr_dataid+'&price='+price+'&contract='+id_dog,
		
					  success: function(data) {
						alert('Цена изменена');
					  }
					
					});
				}
		
			
		});
</script>
<?

	
 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
