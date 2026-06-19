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
$APPLICATION->SetTitle("Межлабораторные сравнительные испытания");
?>
<?php
function checkbox_verify($_name)
// Выполняет: проверку checkbox
{
$result=0;

if (isset($_REQUEST[$_name]))
{ if ($_REQUEST[$_name]=='on') {$result=1;}
}
return $result;
}


?>
   <datalist id="gost">
        <?php
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost`");
        while ($gost = $ba_gost->Fetch()) {
            echo "<option data-type='" . $gost['NORM_TEXT'] . "' " . (($arrDatum['gost'] == $gost['ID']) ? 'selected' : '') . " value='" . $gost['ID'] . "'>" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
        }
        ?>
    </datalist>
<?php
if (!empty($_POST['year_sel'])) {
 header("Location: //" . $_SERVER['HTTP_HOST'] . "/protocol_generator/otchet_msi.php?YEAR=".$_POST['year_sel']);
}

if (isset($_POST['ID'])) {
    if (!empty($_POST['ID'])) {
		
		$res = $DB->Query("UPDATE `MSI` SET 
        `GOST_ID`=" . (int)$_POST['GOST_ID'] . ", 
        `OBJECT`='" . $_POST['OBJECT'] . "',
		`MSILAB`='" . $_POST['MSILAB'] . "',
		`DATE1`='" . $_POST['DATE1'] . "',
		`DATE2`='" . $_POST['DATE2'] . "',
		`DONE`=" . (($_POST['DONE'] == 'on') ? 1 : 0) . ", 
		`CORRECTION`=" . (($_POST['CORRECTION'] == 'on') ? 1 : 0) . ", 
		`COMMENT` = '". $_POST['COMMENT'] ."'
        WHERE `ID`=" . (int)$_GET['ID']);
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    } else {
		
        $res = $DB->Query("INSERT INTO `MSI`
        (GOST_ID, OBJECT, MSILAB, DATE1, DATE2, DONE, CORRECTION, COMMENT) VALUES (" . (int)$_POST['GOST_ID'] . ", '" . $_POST['OBJECT'] . "', '" . $_POST['MSILAB'] . "', '" . $_POST['DATE1'] . "', '" . $_POST['DATE2'] . "', " . (($_POST['DONE'] == 'on') ? 1 : 0) . ", " . (($_POST['CORRECTION'] == 'on') ? 1 : 0) . ", '" . $_POST['COMMENT'] ."')");
        if ($res->result = 1) {
            header("Location: //" . $_SERVER['HTTP_HOST']
                . "/msi.php");
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
        $res = $DB->Query("DELETE FROM `ZERN` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href=\"/obl_acc.php\">К списку</a>";
        echo "<hr>";
    } else {

        $res = $DB->Query("SELECT * FROM `MSI` WHERE `ID`=" . (int)$_GET['ID']);
        $row = $res->Fetch();
		
		//$deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('TYPE_ID' => '1'));
		//var_dump($coord);
        ?>
        <a href="/msi.php">К списку</a>
		
        <?php if ($_GET['ID'] > 0) { ?> |
            <a href="/vlk.php?ID=<?= $_GET['ID'] ?>&del=true"
               onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">Удалить</a>
        <?php }  ?> 
        <hr>
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post'>
            <table id="oborud_table">
			<tbody id="oborud_body">
	
               	<tr>
                    <td>Методика испытаний</td>
                    <td style="width:350px"><input list="gost" class="gost" name="GOST_ID" value="<?=$row['GOST_ID']?>"></td>
				<tr>
                   <td>Объект испытаний</td><td><input class="gost" type="text"  name="OBJECT" value="<?=$row['OBJECT']?>" ></td>
				<tr>
                   <td>Дата начала</td><td><input class="gost" type="date"  name="DATE1" value="<?=$row['DATE1']?>" ></td>
				<tr>
                   <td>Дата окончания</td><td><input class="gost" type="date"  name="DATE2" value="<?=$row['DATE2']?>" ></td>
				<tr>
                   <td>Лаборатория МСИ</td><td><input class="gost" type="text"  name="MSILAB" value="<?=$row['MSILAB']?>" ></td>
				<tr>
				    <td>Выполнено</td>
                    <td><input type="checkbox" name="DONE" <?= ($row['DONE'] == 1) ? 'checked' : '' ?>></td>  
				<tr>
				    <td>Скорректировано</td>
                    <td><input type="checkbox" name="CORRECTION" <?= ($row['CORRECTION'] == 1) ? 'checked' : '' ?>></td>  
				<tr>
                   <td>Примечание</td><td><input class="gost" type="text"  name="COMMENT" value="<?=$row['COMMENT']?>" ></td>
        
		
				</tbody>
            </table>
            <input type="hidden" name="ID" value="<?= $row['ID'] ?>">
			
            <input type='submit' value='Сохранить'>
        </form>
        <?php if ($_GET['ID'] > 0) {// var_dump($_POST['GOST_TYPE']);//var_dump($_POST['NORM_DOP']); ?>
            <hr>
            
            <?php
        }
    }
} else {// Вывод скиска ГОСТов

    global $DB;
	
    
    $res = $DB->Query("SELECT * FROM `MSI`");
    


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    <a href="/msi.php?ID=0">Добавить новое испытание</a> 
	
	<br>
	<br>
    <hr>
    <table id="mat_table">
	<thead>
    <!--<tr>-->
		<th>№</th>
		
		<th>Методика</th>
        <th>Объект испытаний</th>
		<th>Период проведения</th>
		<th>Лаборатория</th>
		<th>Выполнено</th>
		<th>Скорректировано</th>
		<th>Примечание</th>
        <!--<th>Нормативный документ на метод испытания</th>
        <th>Нормативная документация</th>-->
        
	</thead>
	<tbody>
    <!--</tr>-->
    <?php
	$i=0;
	//$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=");
    while ($row = $res->Fetch()) {
		
		
		$res_oa = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=".$row['GOST_ID'])->Fetch();
				
        echo "<tr>";
		echo "<td><a href='/msi.php?ID=" . $row['ID'] . "'>" . $row['ID'] . "</a></td>";
		echo "<td>" . $res_oa['GOST'] .' '. $res_oa['GOST_PUNKT'] . ' | ' . $res_oa['SPECIFICATION'] . "</td>";
		echo "<td>". $row['OBJECT']."</td>";
		echo "<td>" . ($row['DATE1']==$row['DATE2'] ? date("m.Y", strtotime($row['DATE1'])) : date("m.Y", strtotime($row['DATE1'])).' - '.date("m.Y", strtotime($row['DATE2'])) ) . "</td>";
		echo "<td>". $row['MSILAB']."</td>";
		echo "<td>" . ($row['DONE'] ? 'Да' : 'Нет') . "</td>";
		echo "<td>" . ($row['CORRECTION'] ? 'Да' : 'Нет') . "</td>";
		echo "<td>" . $row['COMMENT']. "</td>";
		
    }
	
    echo "</tbody></table>";
    ?>
	<table>
	<tr>
	<td><input type="button" value="Скачать отчет" onclick="window.open ('/protocol_generator/otchet_msi.php?YEAR=' + document.getElementById ('year_sel').value, '_blank')"> за</td><td> <select id="year_sel">
	<option>2019</option>
	<option>2018</option>
	<option>2017</option>
	<option>2016</option>
	</select></td><td> год </td>
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
				"order": [[ 0, "asc" ]],
				"colReorder": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
        });
		   function add(mater = '') {
            if (!isNaN($('.coord:last').data('id'))) {
                last_gost = $('.coord:last').data('id');
            } else {
                last_gost = 1;
            }
			console.log(last_gost);
            $("#obj_body").append("<tr data-name='row_" + (last_gost + 1) + "'>" +
                "<td data-name='coord'>     <input type='number' step ='0.000001' class='coord' name='coord[" + (last_gost + 1) + "][0]' value='" + Number(mater) + "' size='10' data-id='" + (last_gost + 1) + "'><input type='number' step ='0.000001' name='coord[" + (last_gost + 1) + "][1]'  size='10'></td>"   +
                // "<td data-name='gost_new_desc'>  </td>" +
                // "<td data-name='norm_1'>    </td>" +
                // "<td data-name='norm_2'>    </td>" +
                // "<td data-name='ed_izm'>    </td>" +
                "<td data-name='a_href'>    <a href='#' onclick='$(this).parent().parent().remove()'>Удалить</a></td>");
            
			
        }
        $(document).ready(function () {
            $("#add").click(function () {
                add();
            })
            // $("select[name=id_deal]").change(function () {
                // optionSelected = $("option:selected", this);
                // maters = optionSelected.data('mater').split(';');
                // $.each(maters, function (index, value) {
                    // if (value) {
                        // console.log("INDEX: " + index + " VALUE: " + value);
                        // add(value);
                    // }
                // });
            // })
			
            // gost(); gost_new();
        })
		
    </script>    
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>