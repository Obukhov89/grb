<?php

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); 
  include $_SERVER["DOCUMENT_ROOT"]."/include/function.php"; ?>
<?php
$APPLICATION->SetTitle("Перечень типов групп исследуемых материалов");
?>
<style>
input[type="text"]
{
	display: block;
	width: 400px;
	
	height: 40px;
	padding: 8px 10px 8px 10px;
	border: 1px solid #ccc;
	-webkit-border-radius: 2px;
	-moz-border-radius: 2px;
	border-radius: 2px;
	background: #f9f9f9;
	-moz-appearance: none;
	-webkit-appearance: none;
	appearance: normal;
	outline: none;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	-webkit-transition: border-color ease-in-out 0.5s;
	-moz-transition: border-color ease-in-out 0.5s;
	transition: border-color ease-in-out 0.5s;
}
</style>
<?php


$group_val=[];


if (isset($_POST['ID'])) {
    if (!empty($_POST['ID'])) {
		$group_val = serialize($_POST['GROUP_VAL']);
		$res = $DB->Query("UPDATE `MATERIAL_TYPES` SET 
        `NAME`='" . $_POST['NAME'] . "', 
        `ID_MAT`='" . $_POST['group_mat'] . "',
		`GROUPS` = '" .$group_val ."'
		WHERE `ID`=" . (int)$_GET['ID']);
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    } else {
        $res = $DB->Query("INSERT INTO `MATERIAL_TYPES`
        (NAME, ID_MAT, GROUPS) VALUES ('" . $_POST['NAME'] . "', '" . $_POST['group_mat'] . "', '" . $group_val . "')");
        if ($res->result = 1) {
            header("Location: //" . $_SERVER['HTTP_HOST']
                . "/material_types.php");
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
        $res = $DB->Query("DELETE FROM `MATERIAL_TYPES` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href=\"/material_types.php\">К списку</a>";
        echo "<hr>";
    } else {

        $res = $DB->Query("SELECT * FROM `MATERIAL_TYPES` WHERE `ID`=" . (int)$_GET['ID']);
        $row = $res->Fetch();
		$group_val = unserialize($row['GROUPS']);
		
		//var_dump($norm_dop2);
        ?>
        <a href="/material_types.php">К списку</a>
		
        <?php if ($_GET['ID'] > 0) { ?> |
            <a href="/material_types.php?ID=<?= $_GET['ID'] ?>&del=true"
               onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">Удалить</a>
        <?php }  ?> 
        <hr>
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post'>
        <table>
        <tr>
			<td>Название группы материала</td>
            <td><input type="text" name="NAME" value="<?= $row['NAME'] ?>" required></td>
		<tr>
			<td width="250" height="35">Материал </td>
			<td>
				<select class="tz" name="group_mat">
				<option value="0" selected>Выбрать</option>
				<?php
				$select = $DB->Query("SELECT * FROM `MATERIALS`");
				$select_groups = array();
				$gr_mat = explode('-', $sql['GROUP_MAT']);
				
				while ($option = $select->Fetch()) 
				{
					echo "<option value='" . $option['ID'] . "' ".($option['ID']==$row['ID_MAT'] ? 'selected' : '')."  >" . $option['NAME'] . "</option>";
				}
				
				?>
				</select></td></tr>
			<tr>
                   <td>Группа материала</td><td><input type="text" name="GROUP_VAL[0]" value="<?= $group_val[0] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[1]" value="<?= $group_val[1] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[2]" value="<?= $group_val[2] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[3]" value="<?= $group_val[3] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[4]" value="<?= $group_val[4] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[5]" value="<?= $group_val[5] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[6]" value="<?= $group_val[6] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[7]" value="<?= $group_val[7] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[8]" value="<?= $group_val[8] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[9]" value="<?= $group_val[9] ?>"></td>
				<tr>
                   <td>Группа материала</td><td><input type="text" name="GROUP_VAL[10]" value="<?= $group_val[10] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[11]" value="<?= $group_val[11] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[12]" value="<?= $group_val[12] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[13]" value="<?= $group_val[13] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[14]" value="<?= $group_val[14] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[15]" value="<?= $group_val[15] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[16]" value="<?= $group_val[16] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[17]" value="<?= $group_val[17] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[18]" value="<?= $group_val[18] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[19]" value="<?= $group_val[19] ?>"></td>
				<tr>
                   <td>Группа материала</td><td><input type="text" name="GROUP_VAL[20]" value="<?= $group_val[20] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[21]" value="<?= $group_val[21] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[22]" value="<?= $group_val[22] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[23]" value="<?= $group_val[23] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[24]" value="<?= $group_val[24] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[25]" value="<?= $group_val[25] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[26]" value="<?= $group_val[26] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[27]" value="<?= $group_val[27] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[28]" value="<?= $group_val[28] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[29]" value="<?= $group_val[29] ?>"></td>
				<tr>
                   <td>Группа материала</td><td><input type="text" name="GROUP_VAL[30]" value="<?= $group_val[30] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[31]" value="<?= $group_val[31] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[32]" value="<?= $group_val[32] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[33]" value="<?= $group_val[33] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[34]" value="<?= $group_val[34] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[35]" value="<?= $group_val[35] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[36]" value="<?= $group_val[36] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[37]" value="<?= $group_val[37] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[38]" value="<?= $group_val[38] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[39]" value="<?= $group_val[39] ?>"></td>
				<tr>
                   <td>Группа материала</td><td><input type="text" name="GROUP_VAL[40]" value="<?= $group_val[40] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[41]" value="<?= $group_val[41] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[42]" value="<?= $group_val[42] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[43]" value="<?= $group_val[43] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[44]" value="<?= $group_val[44] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[45]" value="<?= $group_val[45] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[46]" value="<?= $group_val[46] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[47]" value="<?= $group_val[47] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[48]" value="<?= $group_val[48] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[49]" value="<?= $group_val[49] ?>"></td>
				<tr>
                   <td>Группа материала</td><td><input type="text" name="GROUP_VAL[50]" value="<?= $group_val[50] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[51]" value="<?= $group_val[51] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[52]" value="<?= $group_val[52] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[53]" value="<?= $group_val[53] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[54]" value="<?= $group_val[54] ?>"></td>
				<tr>
					<td>Группа материала</td><td><input type="text" name="GROUP_VAL[55]" value="<?= $group_val[55] ?>"></td>
				
            </table>
            <input type="hidden" name="ID" value="<?= $row['ID'] ?>">
            <input type='submit' value='Сохранить'>
        </form>
        <?php 
    }
} else {// Вывод скиска ГОСТов

    global $DB;
	
    
    $res = $DB->Query("SELECT * FROM `MATERIAL_TYPES`");
    


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    <a href="/material_types.php?ID=0">Добавить тип группы материала</a> 
	
	<br>
	<br>
    <hr>
    <table id="mat_table">
	<thead>
    <!--<tr>-->
		<th></th>
        <th>Название типа группы материала</th>
        <th>Материал</th>
        <!--<th>Нормативная документация</th>-->
        
	</thead>
	<tbody>
    <!--</tr>-->
    <?php
	$i=0;
    while ($row = $res->Fetch()) {
		
		$res_mat = $DB->Query("SELECT * FROM `MATERIALS` WHERE ID=".$row['ID_MAT'])->Fetch();
        echo "<tr>";
		echo "<td></td>";
		echo "<td><a href='/material_types.php?ID=" . $row['ID'] . "'>" . $row['NAME'] . "</a></td>";
		echo "<td><a href='/materials.php?ID=" . $res_mat['ID'] . "'>" . $res_mat['NAME'] . "</a></td>";
		
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
		
		
    </script>    
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>