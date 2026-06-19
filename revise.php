<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал Сверки");
?>

<table class="table-revise" id="table_id_revise">
	<thead>
		<tr>
			<th>Договор</th>
			<th>Контрагент</th>
			<th>Сумма по всем заявкам в договоре</th>
			<th>Сумма неоплаченных заявок</th>
			<th>Всего заявок в договоре</th>
			<th>Всего неоплаченных заявок</th>
			<th>Сформировать акт сверки</th>
		</tr>
		<tr>
			<th>
				<input type="text" name="dogovorFilter" id="dogovorFilter" class="input-revise" value="">
			</th>
			<th>
				<input type="text" name="counterpartyFilter" id="counterpartyFilter" class="input-revise" value="">
			</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<!--<th>
				<input type="text" name="allSumFilter" id="allSumFilter" class="input-revise" value="">
			</th>
			<th>
				<input type="text" name="allSumFilter" id="allSumFilter" class="input-revise" value="">
			</th>
			<th>
				<input type="text" name="unpaidSumRequestFilter" id="unpaidSumRequestFilter" class="input-revise" value="">
			</th>
			<th>
				<input type="text" name="allUnpaidSumRequestFilter" id="allUnpaidSumRequestFilter" class="input-revise" value="">
			</th>-->
		</tr>
	</thead>
	<tbody>
	<div id='buttonRightTable' class='center'></div>
		<div id='buttonLeftTable'class='center'></div>
	</tbody>
</table>



<!--Подключаем jquery-->
 <script src="//code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
		crossorigin="anonymous"></script>

<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
<script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/filtering/type-based/html.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.21/dataRender/intl.js"></script>

	<script>
//------------'Серверная обработка' начало-------------//
        $(document).ready(function () {
            table= $('.table-revise').DataTable({
                
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url : "processing_revise.php",  //файл серверной обработки
                    type: "POST",
                },
				//lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "All"]], //Список выбора кол-ва записей для вывода на странице
				"bLengthChange": false, //скроет раскрывающееся меню длины(Список выбора кол-ва записей для вывода на странице)
				pageLength: 50, 
				"bPaginate": false, //Скрывает пагинацию
                "columns": [
                    { "data": "NUMBER" },//Договор (номер договора)
                    { "data": "COMPANY" },//Контрагент (клиент,компания)
                    { "data": "SUM_ALL_PRICE" },//Сумма по всем заявкам в договоре
                    { "data": "SUM_NO_PAYMENT" },//Сумма неоплаченных заявок
                    { "data": "COUNT_REQUEST" },//Всего заявок в договоре
                    { "data": "COUNT_REQUEST_NO_PAYMENT" },//Всего заявок в договоре не оплаченных
                    { "data": "BUTTON_REVISE" },//'Кнопка' Сформировать акт сверки 
                ],
				
                //------------'Серверная обработка' окончание-------------//
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
				"columnDefs": [{ "type": "natural-nohtml", "targets": 0 }, 
				{
				  "targets": 1,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				}
												   
				],
				"order": [[ 0, "desc" ]], //Сортировка по умолчанию по первой колонке "Договор", по убыванию
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
			
			//Поиск по колонке договор
			$('#dogovorFilter').on( 'keyup', function () {
				table
					.columns( 0 )
					.search( this.value )
					.draw();
			});
			
			//Поиск по колонке "Контрагент"
			$('#counterpartyFilter').on( 'keyup', function () {
				table
					.columns( 1 )
					.search( this.value )
					.draw();
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
			
			//Событие на кнопку "АКТ Сверки"
			$('.table-revise').on('click', '.btn-revise', function() {
				
				//ID выбранного договора для АКТа сверки
				let idCurrentContract = $(this).data('idContract') ? $(this).data('idContract') : 0;
				console.log('idCurrentContract', idCurrentContract);
				
				if (idCurrentContract) {
			
					//Отправляем GET запрос , на формирование Акта сверки
					window.open('/protocol_generator/protocol_revise.php?ID_CONTRACT=' + idCurrentContract, '_blank');
				}
				
			});
			
			//Прокрутка таблицы с помощью кнопок
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