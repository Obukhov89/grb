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
$APPLICATION->SetTitle("История изменений");
?>

  
<?php

// if (isset($_GET['ID'])) {
    // if (!empty($_GET['ID'])) {
		// $coord = array();
		// for($a=0; $a<count($_POST['coord']); $a++)
		// {
			// $_POST['coord'][$a][0] = (float)$_POST['coord'][$a][0];
			// $_POST['coord'][$a][1] = (float)$_POST['coord'][$a][1];
		// }
	
		// $coord[] = $_POST['coord'];
		// $coord_s = serialize($coord);
		// //var_dump($coord);
		// $res = $DB->Query("UPDATE `DEV_OBJECTS` SET 
        // `NAME`='" . $_POST['NAME'] . "', 
        // `ID_COMPANY`='" . $_POST['ID_COMPANY'] . "',
		// `COORD` = '" .$coord_s ."'
        // WHERE `ID`=" . (int)$_GET['ID']);
        // if ($res->result = 1) {
            // echo "<h3>Успешно</h3>";
        // } else {
            // echo "<h3>Ошибка</h3>";
        // }
    // } else {
		// $coord[] = serialize($_POST['coord']);
        // $res = $DB->Query("INSERT INTO `DEV_OBJECTS`
        // (NAME,ID_COMPANY,COORD) VALUES ('" . $_POST['NAME'] . "', '" . $_POST['ID_COMPANY'] . "', '" . $coord . "')");
        // if ($res->result = 1) {
            // header("Location: //" . $_SERVER['HTTP_HOST']
                // . "/objects.php");
            // echo "<h3>Успешно</h3>";
        // } else {
            // echo "<h3>Ошибка</h3>";
        // }
    // }
// }

// Поиск по ID. Вывод формы для создания/редактирования
if (isset($_GET['ID'])) {
    global $DB;
	$group_val=[];
	
    if (!empty($_GET['del'])) {
        $res = $DB->Query("DELETE FROM `ZERN` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href=\"/obl_acc.php\">К списку</a>";
        echo "<hr>";
    } else {

        $res = $DB->Query("SELECT * FROM `DEV_OBJECTS` WHERE `ID`=" . (int)$_GET['ID']);
        $row = $res->Fetch();
		$coord = unserialize($row['COORD']);
		
		//var_dump($coord);
        ?>
        <a href="/zern.php">К списку</a>
		
        <?php if ($_GET['ID'] > 0) { ?> |
            <a href="/zern.php?ID=<?= $_GET['ID'] ?>&del=true"
               onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">Удалить</a>
        <?php }  ?> 
        <hr>
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post'>
            <table id="obj">
			<tbody id="obj_body">
	
                <tr>
                    <td>Название</td>
                    <td><input class="tz" type="text" name="NAME" value="<?= $row['NAME'] ?>" >
					</td>
				<tr>
                    <td>Клиент</td>
                    <td><input list="gost" class="gost" name="ID_COMPANY" value="<?=$row['ID_COMPANY']?>"></td>
				<tr>
                   <td>Координаты</td><td><input class="res" type="number" step ="0.000001" name="coord[0][0]" value="<?= (float)$coord[0][0][0] ?>"><input class="res" type="number" step ="0.000001" name="coord[0][1]" value="<?= (float)$coord[0][0][1] ?>"></td>
				   <tr>
                   <td>Координаты</td><td><input class="res" type="number" step ="0.000001" name="coord[1][0]" value="<?= (float)$coord[0][1][0] ?>"><input class="res" type="number" step ="0.000001" name="coord[1][1]" value="<?= (float)$coord[0][1][1] ?>"></td>
				</tr>
                  
				</tbody>
            </table>
            <input type="hidden" name="ID" value="<?= $row['ID'] ?>">
			<input type="button" id="add" value="Добавить координаты ">
            <input type='submit' value='Сохранить'>
        </form>
        <?php if ($_GET['ID'] > 0) {// var_dump($_POST['GOST_TYPE']);//var_dump($_POST['NORM_DOP']); ?>
            <hr>
            
            <?php
        }
    }
} else {// Вывод скиска ГОСТов

    global $DB;
	
    
    $res = $DB->Query("SELECT * FROM `HISTORY` ORDER BY `ID` DESC");
    


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    
	
	<br>
	<br>
    <hr>
    <table id="mat_table">
	<thead>
    <tr>
		
		 <th>Заявка</th>																					  
        <th>Протокол</th>
		<th>ТЗ</th>
		<th>Дата</th>
        <th>Тип изменений</th>
        <th>Пользователь</th>
		</tr>
		<tr>
		 <th><input type="text" name="request_filter" id="request_filter" value=""></th>
        <th><input type="text" name="name_filter" id="name_filter" value=""></th>
		 <th><input type="text" name="tz_filter" id="tz_filter" value=""></th>
            <th><input type="text" name="date_filter" id="date_filter" value=""></th>
            <th><input type="text" name="type_filter" id="type_filter" value=""></th>
            <th><input type="text" name="os_filter" id="os_filter" value=""></th>																 
		</tr>
	</thead>
	<tbody>
    
    <?php
//------------'Серверная обработка' начало-------------//
	/*$i=0;
    while ($row = $res->Fetch()) {
        //-------------'Сохранение истории' начало-----------------//
        $tz_id = !empty($row['TZ_ID']) ? $row['TZ_ID'] : '';
        $res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$tz_id)->Fetch();
        $act_num = !empty($res_tz['ACT_NUM']) ? $res_tz['ACT_NUM'] : 0;
        //-------------'Сохранение истории' окончание--------------//
		//$res_comp = CCrmCompany::GetByID($row['ID_COMPANY']);
		$history_date=date("d.m.Y H:i:s", strtotime($row['DATE']));
        echo "<tr>";
        //-------------'Сохранение истории' начало-----------------//
        echo "<td>".($row['REQUEST'] ? "<a href='/request_card.php?ID=".$row['TZ_ID']."&ACT_NUM=".$act_num."' target='_blank'>".$row['REQUEST']."</a>" : '')."</td>";
        //-------------'Сохранение истории' окончание--------------//
		echo "<td>".($row['PROT_NUM'] ? $row['PROT_NUM'] : 'Не сформирован')."</td>";
		echo "<td><a href='/tz_show.php?ID=".$row['TZ_ID']."'>".$row['TZ_ID']."</a></td>";
		echo "<td>" . $history_date . "</td>";
		echo "<td>" . $row['TYPE'] . "</td>";
		echo "<td>" . $row['ASSIGNED'] . "</td>";

    }
 
							

    echo "</tbody></table>";*/
    //------------'Серверная обработка' окончание-------------//
    echo "</table>";																					   
}
?>
	<script>
        var s = document.getElementById("second");
        var ids = document.getElementById("ids_oborud");
        document.getElementById("first").onchange = function () {
            s.options[s.options.length] = new Option(this.options[this.selectedIndex].text, this.value);
            ids.value = JSON.stringify(Array.prototype.slice.call(JSON.parse(ids.value)).concat(this.value));
        }

        s.onchange = function () {
            arr = Array.prototype.slice.call(JSON.parse(ids.value));
            var index = arr.indexOf(this.value);
            if (index !== -1) arr.splice(index, 1);
            ids.value = JSON.stringify(arr);

            s.removeChild(s.options[s.selectedIndex]);

        }
    </script>    
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
	<script type="text/javascript" language="javascript" src="/jszip/jquery.dataTables.customSortandFilters.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>
	<script>
        $(document).ready(function () {
           table= $('#mat_table').DataTable({
               //------------'Серверная обработка' начало-------------//
               "processing": true,
               "serverSide": true,
               "ajax": {
                   url : "server_processing.php",
                   type: "POST",
               },
               "columns": [
                   { "data": "REQUEST" },
                   { "data": "PROT_NUM" },
                   { "data": "TZ_ID" },
                   { "data": "DATE" },
                   { "data": "TYPE" },
                   { "data": "ASSIGNED" }
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
				"order": [[ 3, "desc" ]],
					"columnDefs": [{ "type": "natural-nohtml", "targets": 1 },{ "sType": "ruDate", "targets": 3 },
                    {
                        "targets": 4,
                        "render": $.fn.dataTable.render.ellipsis( 50, true )
                    }
                ],
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
			$('#os_filter').on( 'keyup', function () {
				table
					.columns( 5 )
					.search( this.value )
					.draw();
			} );
	  $('#type_filter').on( 'keyup', function () {
                table
                    .columns( 4 )
                    .search( this.value )
                    .draw();
            } );		
			$('#name_filter').on( 'keyup', function () {
				table
					.columns( 1 )
					.search( this.value )
					.draw();
			} );
			$('#date_filter').on( 'keyup', function () {
				table
					.columns( 3 )
					.search( this.value )
					.draw();
			} );
			$('#tz_filter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			} );
		//-------------'Сохранение истории' начало-----------------//
            //Окно поиска заявок
            $('#request_filter').on( 'keyup', function () {
                table
                    .columns( 0 )
                    .search( this.value )
                    .draw();
            } );
            //-------------'Сохранение истории' окончание-----------------//																					  
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
		} ); 
		
    </script>    
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>