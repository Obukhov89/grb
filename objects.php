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
$APPLICATION->SetTitle("Объекты строительства");
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

$group_val=[];
?>
    <datalist id="gost">
        <?php
        $ba_gost = CCrmCompany::GetList();//$DB->Query("SELECT * FROM `ba_gost`");
        while ($gost = $ba_gost->Fetch()) {
           // echo "<option data-type='" . $gost['NORM_TEXT'] . "' " . (($_GET['ID'] == $gost['ID']) ? 'selected' : '') . " value='" . $gost['GOST'].' '. $gost['GOST_PUNKT'] . "'>" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
		   echo "<option   value='" . $gost['ID'] . "'>". $gost['TITLE']  . "</option>";
        }
        ?>
    </datalist>
<?php

if (isset($_POST['ID'])) {
    if (!empty($_POST['ID'])) {
		$coord = array();
		for($a=0; $a<count($_POST['coord']); $a++)
		{
			$_POST['coord'][$a][0] = (float)$_POST['coord'][$a][0];
			$_POST['coord'][$a][1] = (float)$_POST['coord'][$a][1];
		}
	
		$coord[] = $_POST['coord'];
		$coord_s = serialize($coord);
		//var_dump($coord);
		$res = $DB->Query("UPDATE `DEV_OBJECTS` SET 
        `NAME`='" . $_POST['NAME'] . "', 
        `ID_COMPANY`='" . $_POST['ID_COMPANY'] . "',
		`COORD` = '" .$coord_s ."'
        WHERE `ID`=" . (int)$_GET['ID']);
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    } else {
		$coord[] = serialize($_POST['coord']);
        $res = $DB->Query("INSERT INTO `DEV_OBJECTS`
        (NAME,ID_COMPANY,COORD) VALUES ('" . $_POST['NAME'] . "', '" . $_POST['ID_COMPANY'] . "', '" . $coord . "')");
        if ($res->result = 1) {
            header("Location: //" . $_SERVER['HTTP_HOST']
                . "/objects.php");
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
                   <td>Координаты</td><td><input class="res" type="number" step ="0.000001" name="coord[0][0]" value="<?= (float)$coord[0][0][0] ?>" data-id="0"><input class="res" type="number" step ="0.000001" name="coord[0][1]" value="<?= (float)$coord[0][0][1] ?>" data-id="0"></td>
				   <tr>
                   <td>Координаты</td><td><input class="res" type="number" step ="0.000001" name="coord[1][0]" value="<?= (float)$coord[0][1][0] ?>" data-id="1"><input class="res" type="number" step ="0.000001" name="coord[1][1]" value="<?= (float)$coord[0][1][1] ?>" data-id="1"></td>
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
	
    
    $res = $DB->Query("SELECT * FROM `DEV_OBJECTS`");
    


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    <a href="/objects.php?ID=0">Добавить новый объект</a> 
	
	<br>
	<br>
    <hr>
    <table id="mat_table">
	<thead>
    <!--<tr>-->
		<th></th>
        <th>Название объекта</th>
		<th>Клиент</th>
        <!--<th>Нормативный документ на метод испытания</th>
        <th>Нормативная документация</th>-->
        
	</thead>
	<tbody>
    <!--</tr>-->
    <?php
	$i=0;
    while ($row = $res->Fetch()) {
		
		$res_comp = CCrmCompany::GetByID($row['ID_COMPANY']);
        echo "<tr>";
		echo "<td></td>";
		echo "<td><a href='/objects.php?ID=" . $row['ID'] . "'>" . $row['NAME'] . "</a></td>";
		echo "<td><a href='//http://172.16.255.2/crm/company/details/".$res_comp['ID'] . "'>" . $res_comp['TITLE'] . "</a></td>";
		
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
				"order": [[ 1, "asc" ]],
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