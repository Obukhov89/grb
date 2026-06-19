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



// Поиск по ID. Вывод формы для создания/редактирования
if (isset($_GET['ID'])) {
    
    
} else {// Вывод скиска ГОСТов

    global $DB;
	
    
    $res = $DB->Query("SELECT * FROM `HISTORY_OA` ORDER BY `ID` DESC");
    


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    
	
	<br>
	<br>
    <hr>
    <table id="mat_table">
	<thead>
    <tr>
		
        <th>Элемент</th>
		<th>Дата</th>
        <th>Пользователь</th>
		</tr>
		<tr>
        <th><input type="text" name="name_filter" id="name_filter" value=""></th><th><input type="text" name="date_filter" id="date_filter" value=""></th><th><input type="text" name="os_filter" id="os_filter" value=""></th>
		</tr>
	</thead>
	<tbody>
    
    <?php
	$i=0;
    while ($row = $res->Fetch()) {
		
		$res_user = CUser::GetByID($row['USER_ID'])->Fetch();
		$history_date=date("d.m.Y H:i:s", strtotime($row['TIME']));
		if($row['TYPE']==1) { 
			$link = "/oborud.php?ID=".$row['ID_ELEM']; 
			$res_name = $DB->Query("SELECT * FROM `ba_oborud` WHERE ID=".$row['ID_ELEM'])->Fetch();
			$name = $res_name['OBJECT']. " ". $res_name['TYPE_OBORUD']. " ". $res_name['REG_NUM'];
			
		}
		else {
			$link="/obl_acc.php?ID=".$row['ID_ELEM'];
			$res_name = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=".$row['ID_ELEM'])->Fetch();
			$name=$res_name['GOST']." ".$res_name['GOST_PUNKT']." ".$res_name['SPECIFICATION'];
		}
        if($name!='  '){
		echo "<tr>";
		echo "<td><a href='".$link."'>".$name."</td>";
		echo "<td>" . $history_date . "</td>";
		echo "<td>" . $res_user['NAME']." ". $res_user['LAST_NAME']. "</td>";
		}
    }
	
    echo "</tbody></table>";

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
	
<script>
        $(document).ready(function () {
           table= $('#mat_table').DataTable({
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
				"columnDefs": [{ "sType": "ruDate", "targets": 1 }],
				"order": [[ 1, "asc" ]],
				"colReorder": true,
				"dom": 'Blfrtip',
				"bSortCellsTop": true,
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
			
		
			$('#os_filter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			} );
			$('#name_filter').on( 'keyup', function () {
				table
					.columns( 0 )
					.search( this.value )
					.draw();
			} );
			$('#date_filter').on( 'keyup', function () {
				table
					.columns( 1 )
					.search( this.value )
					.draw();
			} );
        });
		
		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
		"ruDate-asc": function ( a, b ) {
			var ruTimeDatea = $.trim(a).split(' ');
			var ruTimeDateb = $.trim(b).split(' ');
			var ruDatea = $.trim(ruTimeDatea[0]).split('.');
			var ruDateb = $.trim(ruTimeDateb[0]).split('.');
			var ruTimea = $.trim(ruTimeDatea[1]).split(':');
			var ruTimeb = $.trim(ruTimeDateb[1]).split(':');
			
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
					if(ruDatea[0]*1 == ruDateb[0]*1) 
						{
						if(ruTimea[0]*1 < ruTimeb[0]*1)
								return 1;
						if(ruTimea[0]*1 > ruTimeb[0]*1)
								return -1;
						if(ruTimea[0]*1 == ruTimeb[0]*1) 
							{
							if(ruTimea[1]*1 < ruTimeb[1]*1)
									return 1;
							if(ruTimea[1]*1 > ruTimeb[1]*1)
									return -1;
							if(ruTimea[1]*1 == ruTimeb[1]*1) 
								{
								if(ruTimea[2]*1 < ruTimeb[2]*1)
									return 1;
								if(ruTimea[2]*1 > ruTimeb[2]*1)
									return -1;
								if(ruTimea[2]*1 == ruTimeb[2]*1)
									return 0;
								} 
							}
						}
					}
				}				
		},
 
		"ruDate-desc": function ( a, b ) {
			var ruTimeDatea = $.trim(a).split(' ');
			var ruTimeDateb = $.trim(b).split(' ');
			var ruDatea = $.trim(ruTimeDatea[0]).split('.');
			var ruDateb = $.trim(ruTimeDateb[0]).split('.');
			var ruTimea = $.trim(ruTimeDatea[1]).split(':');
			var ruTimeb = $.trim(ruTimeDateb[1]).split(':');
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
					if(ruDatea[0]*1 == ruDateb[0]*1)
						{
						if(ruTimea[0]*1 < ruTimeb[0]*1)
							return -1;
						if(ruTimea[0]*1 > ruTimeb[0]*1)
							return 1;
						if(ruTimea[0]*1 == ruTimeb[0]*1)
							{
							if(ruTimea[1]*1 < ruTimeb[1]*1)
								return -1;
							if(ruTimea[1]*1 > ruTimeb[1]*1)
								return 1;
							if(ruTimea[1]*1 == ruTimeb[1]*1)
								{
								if(ruTimea[2]*1 < ruTimeb[2]*1)
									return -1;
								if(ruTimea[2]*1 > ruTimeb[2]*1)
									return 1;
								}
								else
									return 0;
							}
						}
					}
				}
		}
		} );
		
    </script>    
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>