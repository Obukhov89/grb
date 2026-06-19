<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.04.2019
 * Time: 16:52
 */

header('location: /ulab/protocol/list/');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал протоколов");
(isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

?>


<?php
if (!empty($_POST['ID'])) {

    $results = serialize($_POST['results']);
	$ostatki = serialize($_POST['ostatki']);
	$match = serialize($_POST['match']);
    $sred = serialize($_POST['sr']);
    $temp = serialize($_POST['temp']);
    $vlag = serialize($_POST['vlag']);
	$norm_descript = serialize($_POST['norm_description']);
    $date1 = $_POST['DATE_BEGIN'];
    $date2 = $_POST['DATE_END'];
	$DOPINFO = $_POST['DOP_INFO'];
	$NUM_P = $_POST['NUM_P'];
    $lab = serialize($_POST['lab']);
	$sostav = serialize($_POST['sostav']);
    $OBSHEE = 1;
	$NO_COMPLIANCE = ($_POST['NO_COMPLIANCE'] == 'on') ? 1 : 0;
	$ACTUAL_FOR_OBJECT = ($_POST['ACTUAL_FOR_OBJECT'] == 'on') ? 1 : 0;
	$check_norm=1;
	for($i=0; $i<count($_POST['match']); $i++)
				for($j=0; $j<count($_POST['match'][$i]); $j++)
					for($k=1; $k<=count($_POST['match'][$i][$j]); $k++)
						{
							if ($_POST['match'][$i][$j][$k]=="0") $check_norm = 0;
							
						}
	if($ACTUAL_FOR_OBJECT)	$check_norm=1;

	switch ($_POST['sredType']) {
    case 'simple':
        $TYPE_TZ=0;
        break;
    case 'allvalue':
        $TYPE_TZ=1;
        break;
    case '2max':
        $TYPE_TZ=2;
        break;
	case '3max':
        $TYPE_TZ=3;
        break;
	case '4max':
        $TYPE_TZ=4;
        break;
	case 'zern':
        $TYPE_TZ=5;
		$ostatki = serialize($_POST['ostatki']);
		break;
	case 'grunt':
        $TYPE_TZ=6;
        $ostatki = serialize($_POST['ostatki3']);
		break;
	case 'prirod':
        $TYPE_TZ=7;
		$ostatki = serialize($_POST['ostatki4']);
        break;
	case 'tu_12801':
        $TYPE_TZ=8;
		$ostatki = serialize($_POST['ostatki2']);
        break;
	case 'tu_183':
        $TYPE_TZ=9;
		$ostatki = serialize($_POST['ostatki5']);
        break;
	case 'osk1':
        $TYPE_TZ=10;
		break;
	case 'osk2':
        $TYPE_TZ=11;
		break;
}
	$res_act = $DB->Query("SELECT `ID` FROM `ba_tz` WHERE `NUM_P`='" . $NUM_P."' && `ID`!= '" . $_POST['ID']."'")->Fetch();


	$fl_num_p=0;

    $TEMP_O = $_POST['TEMP_O'];
    $VLAG_O = $_POST['VLAG_O'];
	$assigned = CUser::GetByID($USER->GetID())->arResult[0];
	$assigned_user = $assigned['NAME'].' '.$assigned['LAST_NAME'];
	$ID_P = $_POST['NUM_P'] ? $_POST['NUM_P'] : (int)$_GET['ID']+485;
	
	$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`) VALUES ('" . date('Y-m-d H:i:s') . "','" . $assigned_user . "','" . $ID_P  . "','" . $_GET['ID'] . "','" . $USER->GetID() . "', 'Сохранение результатов испытаний')");


    if (!$fl_num_p) $res = $DB->Query("UPDATE `ba_tz` SET `RESULTS`='" . $results . "', `ostatki`='" . $ostatki . "',`OBSHEE`='" . $OBSHEE . "',`NO_COMPLIANCE`='" . $NO_COMPLIANCE . "',`TYPE_TZ`='" . $TYPE_TZ . "',`TEMP_O`='" . $TEMP_O . "',`VLAG_O`='" . $VLAG_O . "',`TEMP`='" . $temp . "',`VLAG`='" . $vlag . "',`LAB`='" . $lab . "',`DATE1`='" . $date1 . "',`DATE2`='" . $date2 . "',`MATCH_MANUAL`='" . $match . "',`NORM_DESC`='" . $norm_descript . "',`SRED`='" . $sred . "', `DOPINFO`='" . $DOPINFO . "', `NUM_P`='" . $NUM_P . "', `DATE_P_CORR`='" . $_POST['DATE_P_CORR'] . "', `CHECK_NORM`=" . (int)$check_norm . ",`ACTUAL_FOR_OBJECT`='" . $ACTUAL_FOR_OBJECT . "',`GROUP_MAT`='" . $_POST['group_mat'] . "',`SOSTAV`='" . $sostav . "' WHERE ID = " . (int)$_POST['ID']);
    $deal2 = CCrmDeal::GetByID($_GET['ID']);

	
    if (CModule::IncludeModule('crm')&&$deal2['STAGE_ID']!='2'&&$deal2['STAGE_ID']!='3'&&$deal2['STAGE_ID']!='4'&&$deal2['STAGE_ID']!='WON'&&$deal2['TYPE_ID']!='COMPLEX') {
        $deal = new CCrmDeal;
		$num_p = (int)$_GET['ID']+485;
        $arDealUpdate = array('UF_CRM_1554719008992' => $_SERVER["SERVER_NAME"] . "/results_isp.php?ID=" . (int)$_GET['ID'], 'STAGE_ID' => '1', 'UF_CRM_1570518000868' => ($_POST['NUM_P']) ? $_POST['NUM_P'].$y_date : $num_p.$y_date);
        $resUpd = $deal->Update((int)$_GET['ID'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    }
}

if (!empty($_GET['ID'])) {

} else {

    $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'));
		$location_year = ""; $location_laba = ""; $location_stage="";

		
	if(!empty($_GET['laba'])&&empty($_GET['stage'])&&empty($_GET['dateStart'])&&empty($_GET['dateOut']) ) {
		
		$location_laba = "window.location.assign('/protocols_test_binding_multiple_protocols.php?laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/protocols_test_binding_multiple_protocols.php?laba=".$_GET['laba']."&stage='+this.options[this.selectedIndex].value)";
		$location_yearPeriod2 = "window.location.assign('/protocols_test_binding_multiple_protocols.php?laba='+this.options[this.selectedIndex].value&dateStart=".$_GET['dateStart']."&dateOut=".$_GET['dateOut'].")";
	}
	elseif(empty($_GET['laba'])&&!empty($_GET['stage'])&&empty($_GET['dateStart'])&&empty($_GET['dateOut'])) {
		
		$location_laba = "window.location.assign('/protocols_test_binding_multiple_protocols.php?stage=".$_GET['stage']."&laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/protocols_test_binding_multiple_protocols.php?stage='+this.options[this.selectedIndex].value)";
		$location_yearPeriod2 = "window.location.assign('/protocols_test_binding_multiple_protocols.php?laba='+this.options[this.selectedIndex].value&dateStart=".$_GET['dateStart']."&dateOut=".$_GET['dateOut'].")";
	}
	else {
		$location_laba = "window.location.assign('/protocols_test_binding_multiple_protocols.php?laba='+this.options[this.selectedIndex].value)";
		$location_stage= "window.location.assign('/protocols_test_binding_multiple_protocols.php?stage='+this.options[this.selectedIndex].value)";

		$location_yearPeriod1 = (!empty($_GET['dateStart'])) ? $_GET['dateStart'] : date('Y-m-01');
		$location_yearPeriod2 = (!empty($_GET['dateStart'])) ? $_GET['dateStart'] : date('Y-m-t');
	}

    ?>



	<table style="float: right;"><tr>

	<td>
	
    <input class="periodYear" id="period1" type="text" title="Выберите начало отрезка времени" value="<?= ((!empty($_GET['dateStart'])) ? date('d.m.Y', strtotime($_GET['dateStart'])) : date('01.01.Y') ) ?>" required >
    </td>
	<td>
	<input class="periodYear" id="period2" type="text" title="Выберите конец отрезка времени" value="<?= ((!empty($_GET['dateOut'])) ? date('d.m.Y', strtotime($_GET['dateOut'])) : date('t.m.Y') )?>" required >
	</td>

	<td>
	<select class="oborud" name="stage" id="stage" onchange="<?= $location_stage?>"  style="float: left; width: 100%">

        <option value='all' style='font-weight:bold' <?=$_GET['stage'] == 'all' ? 'selected' : ''?> selected>Все стадии</option>
		<option value="1" style='font-weight:bold' <?=$_GET['stage'] == '1' ? 'selected' : ''?>>Пробы не поступили</option>
		<option value='2' style='font-weight:bold' <?=$_GET['stage'] == '2' ? 'selected' : ''?>>Пробы поступили</option>
		<option value='3' style='font-weight:bold' <?=$_GET['stage'] == '3' ? 'selected' : ''?>>Проводятся испытания</option>
		<option value='4' style='font-weight:bold' <?=$_GET['stage'] == '4' ? 'selected' : ''?>>Испытания завершены</option>
		<option value='5' style='font-weight:bold' <?=$_GET['stage'] == '5' ? 'selected' : ''?>>Заявка неуспешна</option>
		<option value='6' style='font-weight:bold' <?=$_GET['stage'] == '6' ? 'selected' : ''?>>Заявка не оплачена</option>
		<option value='7' style='font-weight:bold' <?=$_GET['stage'] == '7' ? 'selected' : ''?>>Заявка оплачена не полностью</option>
		<option value='8' style='font-weight:bold' <?=$_GET['stage'] == '8' ? 'selected' : ''?>>По заявке переплата</option>
		<option value='9' style='font-weight:bold' <?=$_GET['stage'] == '9' ? 'selected' : ''?>>Заявка оплачена полностью</option>
		<option value='10' style='font-weight:bold' <?=$_GET['stage'] == '10' ? 'selected' : ''?>>Все кроме новых и неуспешных</option>
		<option value='11' style='font-weight:bold' <?=$_GET['stage'] == '11' ? 'selected' : ''?>>Успешно завершенные</option>

    </select>
	</td>

	<td>
	<select class="oborud" name="laba" id="laba" onchange="<?= $location_laba?>" style="float: left; width: 100%">
        <option value="all" selected>Все лаборатории</option>

        <?php
        $select = $DB->Query("SELECT * FROM `ba_laba`");
        while ($option = $select->Fetch()) {

            echo "<option ";
            if ($_GET['laba'] == $option['ID']) {
                echo "selected";
            }
            echo " value='" . $option['ID'] . "' style='font-weight:bold'>" . $option['NAME'] . "</option>";

        }
        ?>
    </select></td>
	<td><input type="button" class="add2" value="Х" onclick="window.location.assign('/requests_test.php')"></input></td>
	 <td>
		<input type="checkbox" name="manufacture" id="manufacture"> Производитель
	</td>
	</tr></table>

	<br /><br /><br />
    <table id="results_isp">
	<thead>
		<tr>
		<th></th>
		<th>OA</th>
        <th>Протокол</th>
		<th>Дата</th>
		<th>Клиент</th>

		<th>Объект испытаний</th>
		<th>Ответственный</th>
		<th>Заявка</th>
		<th>ТЗ</th>
		<!--<th>КП</th>-->
		<th >Договор&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
		<?php if(in_array('8', $USER->GetUserGroupArray())) {?><th>Стоимость</th><?}?>
		
		<th>Счет</th>
		
		<th>Дата оплаты</th>
		<th>Рез-ты испытаний</th>

		<th>DOC/PDF</th>
		<th>Производитель</th>
		<th>Последнее изменение</th>
		
		<!--<th></th>-->
		
		</tr>
        <tr>
			<th style="margin:0px"></th>
			<th style="margin:0px"></th>
			<th>
				<input type="text" name="protocol_filter" id="protocol_filter" style="margin:0px" value="">
			</th>
			<th>
				<input type="text" name="dates_filter" id="dates_filter" style="margin:0px" value="">
			</th>
			<th>
				<input type="text" name="client_filter" id="client_filter" style="margin:0px" value="">
			</th>
           
			<th>
				<input type="text" name="object_filter" id="object_filter" style="margin:0px" value="">
			</th>
            
			<th>
				<input type="text" name="assigned_filter" id="assigned_filter" style="margin:0px" value="">
			</th>
			<th>
				<input type="text" name="requests_filter" id="requests_filter" style="margin:0px" value="">
			</th>
			<th>
				<input type="text" name="tz_filter" id="tz_filter" style="margin:0px" value="">
			</th>
			<th>
				<input type="text" name="dogovor_filter" id="dogovor_filter" style="margin:0px" value="">
			</th>
			<?php if(in_array('8', $USER->GetUserGroupArray())) {?>
				<th>
					<input type="text" name="cost_filter" id="cost_filter" style="margin:0px" value="">
				</th>
			<?}?>
			<th>
				<input type="text" name="account_filter" id="account_filter" style="margin:0px" value="">
			</th>
			<th>
				<input type="text" name="date_payment_filter" id="date_payment_filter" style="margin:0px" value="">
			</th>
			<th style="margin:0px"></th>

			<th style="margin:0px"></th>
			<th>
				<input type="text" name="manufacturer_filter" id="manufacturer_filter">
			</th>
			<th>
				<input type="text" name="last_changes_filter" id="last_changes_filter" style="margin:0px" value="">
			</th>
		</tr>
	</thead>
	<tbody>
		<div id='buttonRightTable' class='center'></div>
		<div id='buttonLeftTable'class='center'></div>

				<?php } ?>
			</tbody></table>

	<form method="get" action="/protocol_generator/protocols_journal.php">
		<table>
			<tr>
				<td>Скачать журнал протоколов за:</td>
				<td>
					<select name="year" class="mx-2 my-0">
						<option selected value="2022">2022</option>
						<option value="2021">2021</option>
					</select>
				</td>
				<td><button class="btn mx-2 my-0">Скачать</button></td>
			</tr>
		</table>
	</form>

    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script>
        $(".add").click(function () {
            td = $(this).parent().parent();
            keys = $(this).data("keys").split("-");
            td.append('<div style="display:flex"><input class="res" "margin-left:5px" type="number" step="0.001" name="results[' + keys[0] + '][' + keys[1] + '][' + keys[2] + '][]"  ><input style="margin-left:5px" type="button" value="- " onclick="$(this).parent().remove();"></div>');
        })
    </script>
	<link rel="stylesheet" type="text/css" href="/ulab/assets/css/bootstrap.min.css" />
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


	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="/vendor/jquery_localization-ui/localization.js"></script>

	<script>

	$('#period1').datepicker({
		regional: ['ru'],
		dateFormat: 'dd.mm.yy',
		showOtherMonths: true,
		flag: false,
		onSelect: function(dataText) {
			setTimeout($.proxy(function() {
				$('#period2').datepicker("show");
			}, this), 10);
		}
	});
	$('#period2').datepicker({
		regional: ['ru'],
		dateFormat: 'dd.mm.yy',
		showOtherMonths: true,
	});

	function getServerDate(s) {
			let zeroStr = '0';
			let sDate = '';
			let date = s.split('.');
			let newDate = new Date(date[2], date[1]-1, date[0]);

			if((newDate.getFullYear() % 100) < 10){
				sDate += zeroStr;
			}
			sDate += (newDate.getFullYear() + '-');
			if((newDate.getMonth() + 1) < 10){
				sDate += zeroStr;
			}
			sDate += ((newDate.getMonth() + 1) + '-');

			if(newDate.getDate() < 10) {
				sDate += zeroStr;
			}
			sDate += newDate.getDate();

			return sDate;
		}
	</script>

	<script>

        $(document).ready(function () {
			var stage;
			var year;
			var laba;

			
			let dateStart, dateOut;
	
			var manufacture;
			
			stage = $('#stage option:selected').val();

			year = $('#year option:selected').val();

			laba = $('#laba option:selected').val();

			dateStart = getServerDate($('#period1').val());
			dateOut = getServerDate($('#period2').val());

			$('#stage').on('change', function() {
				stage = $("option:selected", this).val();
			});

			$('#year').on('change', function() {
				year = $("option:selected", this).val();
			});

			$('#laba').on('change', function() {
				laba = $("option:selected", this).val();
			});

			$('#period2, #stage, #laba').on('change', function() {
				let s = $('#period1').val();
				let e = $('#period2').val();

				if(s == '' && e == ''){
					alert('Укажите дату начала и окончания в сортировке');
					event.preventDafault();
					return fasle;
				}

				let stage = $('#stage option:selected').val();
				let strStage = '&stage='+stage;

				let laba = $('#laba option:selected').val();
				let strlaba = '&laba='+laba;

				let sDate = getServerDate(s);
				let eDate = getServerDate(e);
				let str = 'protocols_test_binding_multiple_protocols.php?dateStart='+sDate+'&dateOut='+eDate;
				dateStart = sDate;
				dateOut = eDate;

				if(stage != 'all'){
					str += strStage;
				}
				if(laba != 'all'){
					str += strlaba;
				}
				window.location.assign(str);
			});

            table= $('#results_isp').DataTable({

                "processing": true,
                "serverSide": true,
                "ajax": {
                    url : "processing_prot_binding_multiple_protocols.php",  
                    type: "POST",
										data: {
											stage: stage,
											// year: year,
											dateStart,
											dateOut,
											laba: laba,
											manufacture: manufacture},
                },
				lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "All"]],
				pageLength: 50,
				
                "createdRow": function ( row, data, index ) {
                   
                    $(row).children(':nth-child(1)').css({'background-color': data['COLOR'], 'font-size':0});
                    $(row).children(':nth-child(1)').attr('title', data['HOVER']);
                    
                    $(row).children(':nth-child(11)').css('background-color', data['COLOR_PRICE']);
					
					if(data['COLOR_CHECK_TZ'] ==  'mango_class'){
						$(row).addClass('mango-class'); 
					}

                },
                "columns": [
                    { "data": "STAGE_NUMBER" },
					{ "data": "ATTESTAT" }, 
					{ "data": "NUMBER_AND_YEAR" }, 
					{ "data": "DATE" },
					{ "data": "COMPANY_TITLE" }, 
                    { "data": "MATERIAL" },
                    { "data": "VERIFY_NAME_LASTNAME" }, 
					{ "data": "REQUEST_TITLE" }, 
                    { "data": "ID" },
                    { "data": "DOGOVOR_TABLE" },
                    { "data": "PRICE" }, 
					{ "data": "ACCOUNT" }, 
                    { "data": "DATE_OPLATA" }, 
                    { "data": "RESULT_ISP" }, 

                    { "data": "PROTOCOL" }, 
					{ "data": "MANUFACTURER_TITLE" }, 
                    { "data": "USER_HISTORY" } 
                ],

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
				"columnDefs": [
					{ "orderable": false, "targets": [ 0, 13, 14 ] }, 
					{ "type": "natural-nohtml", "targets": 2 },
					{ "sType": "ruDate", "targets": 3 },
					{
					  "targets": 4,
					  "render": $.fn.dataTable.render.ellipsis( 50, true )
					},
					{
					  "targets": 5,
					  "render": $.fn.dataTable.render.ellipsis( 50, true )
					},
					{
					  "targets": 6,
					  "render": $.fn.dataTable.render.ellipsis( 50, true )
					},
					{ "type": "natural-nohtml", "targets": 7 },
					{ "type": "natural-nohtml", "targets": 8 },
					{ "type": "natural-nohtml", "targets": 10, "render": $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 }) },
					{ "type": "natural-nohtml", "targets": 12 },

					{ "type": "natural-nohtml", "targets": 14 },

					{
					  "targets": 9,
					  "render": $.fn.dataTable.render.ellipsis( 30, true )
					},
					{
					  "targets": 16,
					  "render": $.fn.dataTable.render.ellipsis( 50, true )
					},
				],
				"order": [[ 2, "desc" ]], 
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]

            });
			$('#manufacture').on( 'click', function () {
				if ($('#manufacture').prop('checked')) {
					table
					.columns( 0 )
					.search( 'checked' )
					.draw();
				} else {
					table
					.columns( 0 )
					.search( '' )
					.draw();
				}

			} );

			
			$('#protocol_filter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			});

			
			$('#dates_filter').on( 'keyup', function () {
				table
					.columns( 3 )
					.search( this.value )
					.draw();
			});

			
			$('#client_filter').on( 'keyup', function () {
				table
					.columns( 4 )
					.search( this.value )
					.draw();
			});

			
			$('#object_filter').on( 'keyup', function () {
				table
					.columns( 5 )
					.search( this.value )
					.draw();
			});

			
			$('#assigned_filter').on( 'keyup', function () {
				table
					.columns( 6 )
					.search( this.value )
					.draw();
			});

			
			$('#requests_filter').on( 'keyup', function () {
				table
					.columns( 7 )
					.search( this.value )
					.draw();
			});

			
			$('#tz_filter').on( 'keyup', function () {
				table
					.columns( 8 )
					.search( this.value )
					.draw();
			});

			
			$('#dogovor_filter').on( 'keyup', function () {
				table
					.columns( 9 )
					.search( this.value )
					.draw();
			});

			$('#cost_filter').on( 'keyup', function () {
				table
					.columns( 10 )
					.search( this.value )
					.draw();
			});

			
			$('#account_filter').on( 'keyup', function () {
				table
					.columns( 11 )
					.search( this.value )
					.draw();
			});

			
			$('#date_payment_filter').on( 'keyup', function () {
				table
					.columns( 12 )
					.search( this.value )
					.draw();
			});

			
			$('#manufacturer_filter').on('keyup', function() {
				table
					.columns(15)
					.search(this.value)
					.draw();
			});

			
			$('#last_changes_filter').on( 'keyup', function () {
				table
					.columns( 16 )
					.search( this.value )
					.draw();
			});
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
