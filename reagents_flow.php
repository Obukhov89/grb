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
$APPLICATION->SetTitle("Журнал параметров расхода реактивов");
?>
<?php


if (!empty($_POST['year_sel'])) {
 header("Location: //" . $_SERVER['HTTP_HOST'] . "/protocol_generator/otchet_msi.php?YEAR=".$_POST['year_sel']);
}

if (isset($_POST['ID_REACT'])) {
    if (!empty($_POST['ID_REACT'])) {
		
		$res_ost = $DB->Query("SELECT * FROM `REACTIVE_OST` WHERE `ID`=".$_POST['ID_REACT'])->Fetch();
		$ostatok = $res_ost['REACT_OST'] + $_POST['PRIKHOD']; 
		$res_ost = $DB->Query("UPDATE `REACTIVE_OST` SET `REACT_OST` =".$ostatok." WHERE `ID`=".$_POST['ID_REACT']);
		
        $res = $DB->Query("INSERT INTO `REACTIVE`
        (ID_REACT, DATE_POSTUP, PRIKHOD, NUMBER_PART, OSTATOK, SROK_GODN) VALUES ('" . $_POST['ID_REACT'] . "', '" . $_POST['DATE_POSTUP'] . "', " . (float)$_POST['PRIKHOD'] . ", '" . $_POST['NUMBER_PART'] . "', '" . $ostatok . "', '" . $_POST['SROK_GODN'] ."')");
        if ($res->result = 1) {
            header("Location: //" . $_SERVER['HTTP_HOST']
                . "/reactive.php?ID_REACT=".$_POST['ID_REACT']);
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    }
}

// Поиск по ID. Вывод формы для создания/редактирования
if (isset($_GET['ID'])) {
    global $DB;
	$group_val=[];
	
	
    if (!empty($_GET['del'])) {
		
		
        $res = $DB->Query("DELETE FROM `REAGENTS_FLOW` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href=\"/reagents_flow.php\">К списку</a>";
        echo "<hr>";
    } else {

       
        ?>
        <a href="/reactive.php">К списку</a>
		
     
        <form method='post'>
            <table id="oborud_table">
			<tbody id="oborud_body">
				
               	<tr>
                    <td>Наименование</td>
                    <td style="width:450px"><select class="obl" name="ID_REACT" required>
                            <option value="" selected disabled="">Выберите реактив</option>
                            <?php
                            $res = $DB->Query("SELECT * FROM `REACTIVE_OST`");
                            while ($value = $res->Fetch()) {
                                echo "<option value='" . $value['ID'] . "'>" . $value['REACT_NAME'] ."</option>";
                            }
                            ?>
                        </select></td>
				<tr>
                   <td>Дата поступления</td><td><input class="gost" type="date"  name="DATE_POSTUP" value="<?=date("Y-m-d")?>" required></td>
				<tr>
                   <td>Приход/расход</td><td><input class="gost" type="number" step="0.0001"  name="PRIKHOD" value="0" required></td>
				<tr>
                   <td>НД, № партии, квалификация </td><td><input class="gost" type="text"  name="NUMBER_PART" value="" required> </td>
				<tr>
                   <td>Срок годности</td><td><input class="gost" type="date"  name="SROK_GODN" value="<?=date("Y-m-d")?>" ></td>
        
		
				</tbody>
            </table>

			
            <input type='submit' value='Сохранить'>
        </form>
       
            <hr>
            
            <?php
        }
    
}
elseif (isset($_GET['ID_REACT'])) 
{?>
 <a href="/reactive.php?ID=0">Добавить запись</a> |  <a href="/reactive_new.php">Добавить реактив</a> | <a href="/reactive.php">К списку</a>
 <br /><br />
 <!--<select onchange="top.location='/reactive.php?ID_REACT='+this.value" class="obl" name="ID_REACT" required>
                            <option value="" selected disabled="">Выберите реактив</option>
                            <?php
                            //$res = $DB->Query("SELECT * FROM `REACTIVE_OST`");
                            //while ($value = $res->Fetch()) {
                           //     echo "<option value='" . $value['ID'] . "'>" . $value['REACT_NAME'] ."</option>";
                            //}
                            ?>
                        </select>
 	-->
	<br>
	
	 <table id="mat_table">
	<thead>
    <!--<tr>-->
		<th>Дата поступления</th>
		<th>Приход</th>
		<th>Расход</th>
		<th>Остаток</th>
        <th>НД № партии квалификация</th>
		<th>Срок годности</th>
		<th></th>
		        
	</thead>
	<tbody>
    <!--</tr>-->
    <?php
	$i=0;
	$res = $DB->Query("SELECT * FROM `REACTIVE` WHERE `ID_REACT`=".$_GET['ID_REACT']);
    while ($row = $res->Fetch()) {
		
							echo "<tr>";
							echo "<td>" . date("d.m.Y", strtotime($row['DATE_POSTUP'])). "</td>";
							echo "<td>" . ($row['PRIKHOD']>0 ? $row['PRIKHOD'] : '') . "</td>";
							echo "<td>". ($row['PRIKHOD']<0 ? $row['PRIKHOD'] : '') ."</td>";
							echo "<td>" . $row['OSTATOK'] . "</td>";
							echo "<td>". $row['NUMBER_PART'] ."</td>";
							echo "<td>". date("d.m.Y", strtotime($row['SROK_GODN'])) ."</td>";
							echo "<td><a href='/reactive.php?ID=".$row['ID']."&del=1'>Удалить</a></td>";
												
    }
	
    echo "</tbody></table>";
    ?>
	<!--<table>
	<tr>
	<td><input type="button" value="Скачать журнал" onclick="window.open ('/protocol_generator/otchet_msi.php?YEAR=' + document.getElementById ('year_sel').value, '_blank')"> за</td><td> <select id="year_sel">
	<option>2019</option>
	<option>2018</option>
	<option>2017</option>
	<option>2016</option>
	</select></td><td> год </td>
	</table>-->
	<?php
//}


} else {// Вывод скиска ГОСТов

    global $DB;
	
    
    $res = $DB->Query("SELECT * FROM `REACTIVE`");
    


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    <a href="/reagents_flow_new.php?ID=0">Добавить запись</a> 
	
	<br>
	<br>
    <hr>

					
    <table id='oborud'>
	<thead>
	<th>№</th>
	<th>Наименование реактива</th>
	<th>Наименование ГОСТ</th>
	<th>Расход</th>
	<th>Ед. изм.</th>
	<th></th>
	</thead>
	<tbody>
	<?php
		$i=1;
		$res = $DB->Query("SELECT * FROM `REAGENTS_FLOW`");
		while ($row = $res->Fetch())
		{
			$reag = $DB->Query("SELECT * FROM `REACTIVE_OST` WHERE `ID`=".$row['ID_REAG'])->Fetch();
			$gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=".$row['ID_GOST'])->Fetch();
			echo "<tr><td>".$i."</td><td><a href='/reactive.php?ID_REACT=".$reag['ID']."'>".$reag['REACT_NAME']."</a></td><td>" . $gost['GOST'] . " " . $gost['GOST_PUNKT'] ." " . $gost['SPECIFICATION'] . "</td>
			<td>".$row['FLOW_MIN']." - ".$row['FLOW_MAX']."</td><td>".$reag['ED']."</td><td><a href='/reagents_flow.php?ID=".$row['ID']."&del=1'>Удалить</a></td></tr>";
			$i++;
		}
	?>
	</tbody>
	</table>
	<?php
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
	
	<script>
        $(document).ready(function () {
            $('#mat_table').DataTable({
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
				"columnDefs": [{ "sType": "ruDate", "targets": 0 }],
				"order": [[ 0, "asc" ]],
				"colReorder": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
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
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>