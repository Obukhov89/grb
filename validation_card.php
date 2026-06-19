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
$APPLICATION->SetTitle("Отчеты о валидации и верификации методики");
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
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
 header("Location: //" . $_SERVER['HTTP_HOST'] . "/protocol_generator/otchet_vlk.php?YEAR=".$_POST['year_sel']);
}

if (isset($_POST['ID'])) {
			$ba_gost = $DB->Query("select um.*, ug.reg_doc, ug.description, ug.year, ug.materials  
						from `ulab_methods` as um 
						join `ulab_gost` as ug on ug.id = um.gost_id
						where um.`id`=".$_GET['ID'])->Fetch();
		//	dump($gost)
			$temp_in_gost_check=1;
			$vlag_in_gost_check=1;
			$temp_p = $temp_p2 = tofloat($_POST['TEMP']);
			$vlag_p = $vlag_p2 = tofloat($_POST['VLAG']);
			$temp_g = unserialize($gost['TEMP']);
			$vlag_g = unserialize($gost['VLAG']);
			
			/*-------------------'Не нормируется' начало---------------------*/
			//if($temp_p<(float)$temp_g[0]||$temp_p2>tofloat($temp_g[1])) {$temp_in_gost_check=0; echo "Внимание! Температура при проведении испытаний не соответствует условиям в методике <a href='/obl_acc.php?ID=".$gost['ID']."'>" . $gost['GOST']. " " . $gost['SPECIFICATION'] . "</a>! <br><br>";}
			if (($temp_p<(float)$ba_gost['cond_temp_1']||$temp_p2>tofloat($ba_gost['cond_temp_2'])) && empty($ba_gost['is_not_cond_temp']))
			{
				$temp_in_gost_check=0; 
				echo "Внимание! Температура при проведении испытаний не соответствует условиям в методике <a href='/ulab/gost/method/".$ba_gost['id']."'>" . $ba_gost['reg_doc']. " " . $ba_gost['name'] . "</a>! <br><br>";
				
			}
			
			//if($vlag_p<(float)$vlag_g[0]||$vlag_p2>tofloat($vlag_g[1])) {$vlag_in_gost_check=0; echo "Внимание! Влажность при проведении испытаний не соответствует условиям в методике <a href='/obl_acc.php?ID=".$gost['ID']."'>" . $gost['GOST']. " " . $gost['SPECIFICATION'] . "</a>! <br><br>";}
			if(($vlag_p<(float)$ba_gost['cond_wet_1']||$vlag_p2>(float)$ba_gost['cond_wet_2']) && empty($ba_gost['is_not_cond_wet']))
			{
				$vlag_in_gost_check=0; 
				echo "Внимание! Влажность при проведении испытаний не соответствует условиям в методике <a href='/ulab/gost/method/".$ba_gost['id']."'>" . $ba_gost['reg_doc']. " " . $ba_gost['name'] . "</a>! <br><br>";
				
			}
			//echo $vlag_g[0];
		   /*-------------------'Не нормируется' окончание-------------------*/
		
		
       
		if($vlag_in_gost_check&&$temp_in_gost_check)
		{
			$res = $DB->Query("INSERT INTO `OA_VV`
			(DATE, TEMP, VLAG, PRESS, CONTR_LIST, ID_GOST, VALIDATION, lab) VALUES ('" . $_POST['DATE'] . "', '" . $_POST['TEMP'] . "', '" . $_POST['VLAG'] . "', '" . $_POST['PRESS'] . "', '" .$_POST['CONTR_LIST']."', ".(int)$_GET['ID'].", ".($_POST['VALIDATION'] == 'on' ? 1 : 0).", ".$_POST['lab'].")");
			if ($res->result = 1) {
				header("Location: //" . $_SERVER['HTTP_HOST']
					. "/validation_card.php?ID=".$_GET['ID']);
				echo "<h3>Успешно</h3>";
			}
			else 
			{
				echo "<h3>Ошибка</h3>";
			}
		}
		else
		{}
    //}
}
if (!empty($_GET['del'])) {
		 $res = $DB->Query("DELETE FROM `OA_VV` WHERE `ID`=" . (int)$_GET['ID_V'])->Fetch();
         if ($res->result = 1) {
             echo "<h3>Успешно</h3>";
         } else {
             echo "<h3>Ошибка</h3>";
         }
         echo "<br><a href='ulab/gost/list'>К списку</a>";
         echo "<hr>";
       
    }
// Поиск по ID. Вывод формы для создания/редактирования
if (!empty($_GET['new'])) {
    global $DB;
	$group_val=[];
	
   

       
        ?>
        <a href="/ulab/gost/method/<?= $_GET['ID'] ?>">Назад</a>
		
        
        <hr>
        <h2>Добавление отчета</h2>
        <form method='post'>
            <table id="oborud_table">
			<tbody id="oborud_body">
	
                
				<tr>
                   <td>Дата</td><td><input class="oborud" type="date"  name="DATE" value="" ></td>
				</tr>
				<tr>
                   <td>Лаборатория</td>
					<td>
						<select name="lab">
							<option value="54">ЛФХИ</option>
							<option value="57">ЛСМ</option>
							<option value="56">ЛФМИ</option>
							<option value="55">ДСЛ</option>
							<option value="58">ОСК</option>
						</select>
					</td>
				</tr>
				<tr>
                   <td>Температура</td><td><input class="oborud" type="text"  name="TEMP" value="" ></td>
				</tr>
				<tr>
                   <td>Влажность</td><td><input class="oborud" type="text"  name="VLAG" value="" ></td>
				</tr>
				<tr>
                   <td>Давление</td><td><input class="oborud" type="text"  name="PRESS" value="" ></td>
				</tr>
				<tr>
                   <td>Контрольный лист</td><td><input class="oborud" type="text"  name="CONTR_LIST" value="" ></td>
				</tr>
                <tr>
                   <td>Валидация</td>
                   <td><input type="checkbox" name="VALIDATION"></td>
				</tr>
				
                
				</tbody>
            </table>
            <input type="hidden" name="ID" value="<?= $_GET['ID'] ?>">
			
            <input type='submit' value='Сохранить'>
        </form>
        <?php if ($_GET['ID'] > 0) {// var_dump($_POST['GOST_TYPE']);//var_dump($_POST['NORM_DOP']); ?>
            <hr>
            
            <?php
        }
    
} else {// Вывод скиска ГОСТов

    global $DB;
	
    
    $res = $DB->Query("SELECT * FROM `OA_VV` WHERE `ID_GOST`=".$_GET['ID']);
    


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    <a href="/validation_card.php?ID=<?=$_GET['ID']?>&new=true">Добавить отчет</a> | <a href="/ulab/gost/method/<?= $_GET['ID'] ?>">Назад</a>
	
	<br>
	<br>
    <hr>
    <table id="mat_table">
	<thead>
    <!--<tr>-->
		<th>№</th>
		<th>Дата</th>
		<th>Температура</th>
        <th>Влажность</th>
		<th>Давление</th>
		<th>Протокол/контр. лист</th>
		<th></th>
        <!--<th>Нормативный документ на метод испытания</th>
        <th>Нормативная документация</th>-->
        
	</thead>
	<tbody>
    <!--</tr>-->
    <?php
	$i=0;
	//$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=");
    while ($row = $res->Fetch()) {
		
		
        echo "<tr>";
		echo "<td><a href='/protocol_generator/validation.php?ID_V=". $row['ID'] . "'>" . $row['ID'] . "</td>";
		echo "<td>" . date("d.m.Y", strtotime($row['DATE'])) . "</td>";
		echo "<td>". $row['TEMP']."</td>";
		echo "<td>" . $row['VLAG'] . "</td>";
		echo "<td>" . $row['PRESS'] .  "</td>";
		echo "<td>" . $row['CONTR_LIST'] . "</td>";?>
		<td><a href='/validation_card.php?ID=<?=$row['ID_GOST']?>&ID_V=<?=$row['ID']?>&del=true' onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">Удалить</a></td>
        <?php }   
		
		
    
	
    echo "</tbody></table>";
    ?>
	<!--<table>
	<tr>
	<td><input type="button" value="Скачать отчет" onclick="window.open ('/protocol_generator/otchet_vlk.php?YEAR=' + document.getElementById ('year_sel').value, '_blank')"> за</td><td> <select id="year_sel">
	<option>2019</option>
	<option>2018</option>
	<option>2017</option>
	<option>2016</option>
	</select></td><td> год </td>
	</table>-->
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
