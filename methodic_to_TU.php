<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Связь методик и ТУ");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

// if (!empty($_POST['gost']))
// {
	// //dump($_POST);
	// $DB->Query("UPDATE `ba_gost` SET `PRICE`=".tofloat($_POST['price_new'][0])." WHERE `ID`=".$_POST['gost']);	  
// }

$res_oa = $DB->Query("SELECT * FROM `ba_gost` ORDER BY `ID` ASC LIMIT 500");	
if (!empty($_POST['gost']))
{
	dump($_POST);
	//$DB->Query("UPDATE `ba_gost` SET `ID_TU`=".($_POST['price_new'][0])." WHERE `ID`=".$_POST['gost']);	  
}

?>
 <link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
<script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>

<style type="text/css">
#results_isp td, th 
{
	border: 1px solid #ccc;
}
</style>
  


<table id="results_isp">
<thead>
<tr>
<th>ID</th>
<th>ОА</th>
<th>Тип</th>
<th>ГОСТ</th>
<th>Характеристика</th>
<th>ТУ</th>
<th></th>
<tr>
<th></th>
<th></th>
<th></th>
<th><input type="text" name="gost_filter" id="gost_filter" style="margin:0px" value=""></th>
<th></th>
<th></th>
<th></th>
</thead>
<tbody>
<?
while($gost = $res_oa->Fetch())
{ 
	if(!$gost['NON_ACTUAL'] && $gost['GOST_TYPE']!='TU_standart' && $gost['GOST_TYPE']!='TU_research' && $gost['GOST_TYPE']!='TU_group'){// && $gost['PRICE']==0 && $gost['GOST_TYPE']!= 'metodic_otbor'){
	$changeFlag = in_array((int)$gost['ID'], $changeArr) ? 'Возможно редактирование' : '';
	$inOA = $gost['IN_OA'] ? 'OA' : '';
	$typeGost = (($gost['GOST_TYPE']=='TU_standart'||$gost['GOST_TYPE']=='TU_research'||$gost['GOST_TYPE']=='TU_group') ? 'ТУ' : ''); ?>

	<tr id="<?= $gost['ID'] ?>">
		<td> <?= $gost['ID'] ?></td>
		<td style='width:100px'><?= $inOA ?></td>
		<td style='width:60px'><?= $typeGost ?></td>
		<td style='width:250px'><b><?= $gost['GOST']." ".$gost['GOST_PUNKT'] ?></b></td>
		<td style='width: 400px'><?= $gost['SPECIFICATION'] ?></td>
		<td class="tdGost"><input type='text' value='<?= $gost['ID'] ?>' hidden>
		<select name='gost_new' id='gost_new' style='width:350px' class='gost2 styleGost' >
								<option value='2522' selected>-- | --</option><?php
								$ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1 AND `IN_OA` != 1");
									while ($res_tu = $ba_gost->Fetch()) {
										echo "<option data-type='" . $res_tu['NORM_TEXT'] . "'  value='" . $res_tu['ID'] .  "' " .($gost['ID_TU']==$res_tu['ID'] ? 'selected' : '') . ">" . $res_tu['GOST'] .' '. $res_tu['GOST_PUNKT'] . ' | ' . $res_tu['SPECIFICATION'] .  "</option>";
									}
									?>
							</select>
		</td>
		<td><input type='button' name='bt' value='Сохранить'></td></tr>
	
		<?
	}
}
?>
</tbody>
</table>


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
        });
		
		
    </script>     
			
<script>
$('input[name=bt]').on("click", function () {
				
			 
				tr = $(this).parent().parent();
				tr_dataid = tr.attr('id');
				tu_inp = tr.find('#gost_new');
				tu = tu_inp[0].value;
				// console.log(tr_dataid);
				// console.log(tu);
				$.ajax({

				  url: 'tu_upd_go.php?gost='+tr_dataid+'&tu='+tu,
	
				  success: function(data) {
					alert('OK');
				  }
				
				});
				
		
			
		});
</script>
    <script>
        $(document).ready(function () {
            <!--'При наведении мыши на списки методики, подключаем select2'-->
            /*$('#form_tz').on('mouseenter', '.styleGost', function () {
                $(this).select2();
            });*/

            $('#results_isp').on('mouseenter', '.tdGost', function () {
                //console.log('mouseenter this', $(this));
                $(this).find('.styleGost').select2();
            });

            $('#results_isp').on('mouseleave', '.tdGost', function () {
                var focusedName = document.activeElement.className;

                if (focusedName == 'select2-search__field') {
                    return true;
                }

                $(".styleGost.select2-hidden-accessible").select2('destroy');
            });

            <!--'После выбора методики отлючаем select2'-->
            $('#results_isp').on('change', '.styleGost', function (i, obj) {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    setTimeout(function () {
                        $(".styleGost.select2-hidden-accessible").select2('destroy');
                    }, 0);
                }
            });

            <!--'При попадании в фокус элемента, проверяем, если элемент не является списком методик, то отключаем select2 у оставшихся подключенных методик'-->
            $('#results_isp').on('focusin input', function (event) {
                var targetElement = event.target;
                if (!$(targetElement).is(".select2-selection")) {
                    $(".styleGost.select2-hidden-accessible").select2('destroy');
                }
            });

        });
    </script>
<?

	
 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>