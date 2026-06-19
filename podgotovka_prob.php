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
$APPLICATION->SetTitle("Журнал подготовки проб");
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
		
		$value = serialize($_POST['VALUE']);
		var_dump($_POST['VALUE']);
		$res = $DB->Query("UPDATE `PODGOTOVKA` SET 
        `START_MASS`='" . $_POST['START_MASS'] . "', 
        `SRED`='" . $_POST['SRED'] . "',
		`TEMP`='" . $_POST['TEMP'] . "',
		`VALUE`='" . $value . "',
		`COMMENT` = '". $_POST['COMMENT'] ."'
        WHERE `ID`=" . (int)$_GET['ID']);
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    } else {
		$value = serialize($_POST['VALUE']);
        $res = $DB->Query("INSERT INTO `PODGOTOVKA`
        (START_MASS, SRED, TEMP, VALUE, COMMENT) VALUES ('" . $_POST['START_MASS'] . "', '" . $_POST['SRED'] . "', '" . $_POST['TEMP'] . "', '" . $value . "', '" . $_POST['COMMENT'] ."')");
        if ($res->result = 1) {
            header("Location: //" . $_SERVER['HTTP_HOST']
                . "/podgotovka_prob.php");
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
        $res = $DB->Query("DELETE FROM `PODGOTOVKA` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href=\"/obl_acc.php\">К списку</a>";
        echo "<hr>";
    } else {

        $res = $DB->Query("SELECT * FROM `PODGOTOVKA` WHERE `ID`=" . (int)$_GET['ID']);
        $row = $res->Fetch();
		$value = unserialize($row['VALUE']);
		//$deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('TYPE_ID' => '1'));
		//var_dump($value);
        ?>
        <a href="/podgotovka_prob.php">К списку</a>
		
        <?php if ($_GET['ID'] > 0) { ?>      <!-- |
      <a href="/vlk.php?ID=<?= $_GET['ID'] ?>&del=true"
               onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">Удалить</a>-->
        <?php }  ?> 
        <hr>
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post'>
            <table id="oborud_table">
			<tbody id="oborud_body">
	
               	<tr>
                    <td>Масса аналитической пробы</td>
                    <td style="width:350px"><input type="text" class="gost" name="START_MASS" value="<?=$row['START_MASS']?>"></td>
				<tr>
                   <td>Степень измельчения</td><td><input class="gost" type="text"  name="SRED" value="<?=$row['SRED']?>" ></td>
				<tr>
                   <td>Температура просушки</td><td><input class="gost" type="text"  name="TEMP" value="<?=$row['TEMP']?>" ></td>
				<tr>
                   <td>Масса после 1 высушивания</td><td><input class="gost" type="number" step="0.0001"  name="VALUE[0]" value="<?=$value[0]?>" ></td>
				<tr>
                   <td>Масса после 2 высушивания</td><td><input class="gost" type="number" step="0.0001"  name="VALUE[1]" value="<?=$value[1]?>" ></td>
				<tr>
                   <td>Масса после 3 высушивания</td><td><input class="gost" type="number" step="0.0001"  name="VALUE[2]" value="<?=$value[2]?>" ></td>
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
	
    
    $res = $DB->Query("SELECT * FROM `PODGOTOVKA`");
    


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    <a href="/podgotovka_prob.php?ID=0">Добавить новое испытание</a> 
	
	<br>
	<br>
    <hr>
    <table id="mat_table">
	<thead>
    <!--<tr>-->
		<th>№</th>
		<th>Шифр пробы</th>
		<th>Описание пробы</th>
		<th>НД на МИ</th>
        <th>НД на пробоподготовку</th>
		<th>Масса аналит. пробы</th>
		<th>Степень измельчения</th>
		<th>Темп-ра сушки</th>
		<th>Масса после 1 сушки</th>
		<th>Масса после 2 сушки</th>
		<th>Масса после 3 сушки</th>
		<th>Разность масс</th>
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
		
		
		//ЗДЕСЬ ПЕРЕДЕЛАТЬ!!! У НАС В ТАБЛИЦЕ УЖЕ ЕСТЬ ЧАСТЬ ДАННЫХ!!!
		
		$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=".$row['TZ_ID'])->Fetch();
		$laba_id_arr = explode(',', $res_tz['LABA_ID']);
		$laba_id = '54';
		
		$probe = unserialize($res_tz['PROBE']);
		$tz = unserialize($res_tz['TZ']);
		$value = unserialize($row['VALUE']);
		// foreach($tz as $t => $tz_mater)
		// {
			// $fl_otbor='';
			// foreach($tz_mater['gost'] as $g => $tz_gost)
			// {
				// $res_g = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=".$tz_gost)->Fetch();
				// if ($res_g['GOST_TYPE']=='metodic_otbor') $fl_otbor=$res_g['GOST'];
				// //var_dump($tz); echo "<br />$fl_otbor<br />";
			// }
			
			// if($fl_otbor)
			// {
				// //echo "<br />$fl_otbor<br />$t<br />";
				// //var_dump($probe);
				// //foreach($probe as $p => $probe_tz)
				// //{		
					// foreach($tz_mater['gost'] as $g => $tz_gost)
					// {
						$res_g = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=".$row['GOST_ID'])->Fetch();
						$res_op = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=".$row['GOST_OTBOR_ID'])->Fetch();
						if(in_array($laba_id, $laba_id_arr))
						{
							$delta = (float)$value[2] - (float)$value[1];
							echo "<tr>";
							echo "<td><a href='/podgotovka_prob.php?ID=" . $row['ID'].  "'>" . $row['ID']. "</a></td>";
							echo "<td>" . $row['NUM_PROBE'] . "</td>";
							echo "<td>". $row['PROBE_NAME'] ."</td>";
							echo "<td>" . $res_g['GOST'] . "</td>";
							echo "<td>". $res_op['GOST'] ."</td>";
							echo "<td>". $row['START_MASS'] ."</td>";
							echo "<td>". $row['SRED'] ."</td>";
							echo "<td>". $row['TEMP'] ."</td>";
							echo "<td>". $value[0] ."</td>";
							echo "<td>". $value[1] ."</td>";
							echo "<td>". $value[2] ."</td>";
							echo "<td>". $delta ."</td>";
							echo "<td>" . $row['COMMENT'] . "</td>";
							// echo "<td>" . $row['COMMENT']. "</td>";
						}
						// $i++;
					// }
				// //}
			// }
		// }
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