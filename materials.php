<?php
header("Location: /ulab/material/list/");
/**
 * Created by PhpStorm.
 * User: User
 * Date: 25.03.2019
 * Time: 12:35
 */

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("Перечень исследуемых материалов");
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


if (isset($_POST['ID'])) {
    if (!empty($_POST['ID'])) {
		$group_val = serialize($_POST['GROUP_VAL']);
		$res = $DB->Query("UPDATE `MATERIALS` SET 
        `NAME`='" . $_POST['NAME'] . "', 
        `GROUP_NAME`='" . $_POST['GROUP_NAME'] . "',
		`GROUPS` = '" .$group_val ."'
        WHERE `ID`=" . (int)$_GET['ID']);
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    } else {
        $res = $DB->Query("INSERT INTO `MATERIALS`
        (NAME,GROUP_NAME,GROUPS) VALUES ('" . $_POST['NAME'] . "', '" . $_POST['GROUP_NAME'] . "', '" . $group_val . "')");
        if ($res->result = 1) {
            header("Location: //" . $_SERVER['HTTP_HOST']
                . "/materials.php");
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    }
}

if (isset($_POST['OA'])) {
var_dump($_POST['ch']);
foreach($_POST['ch'] as $key=>$ch)
{
	
	$res = $DB->Query("UPDATE `ba_gost` SET `DOP`='" . $_GET['ID'] . "' WHERE `NUM_OA`=" . (int)$key);	
}
 //(($_POST['LFMI'] == 'on') ? 1 : 0) . ",
}

// Поиск по ID. Вывод формы для создания/редактирования
if (isset($_GET['ID'])) {
    global $DB;
	$group_val=[];
	
    if (!empty($_GET['del'])) {
        $res = $DB->Query("DELETE FROM `MATERIALS` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href=\"/obl_acc.php\">К списку</a>";
        echo "<hr>";
    } else {

        $res = $DB->Query("SELECT * FROM `MATERIALS` WHERE `ID`=" . (int)$_GET['ID']);
        $row = $res->Fetch();
		$group_val = unserialize($row['GROUPS']);
		
		//var_dump($norm_dop2);
        ?>
        <a href="/materials.php">К списку</a>
		
        <?php if ($_GET['ID'] > 0) { ?> |
            <a href="/materials.php?ID=<?= $_GET['ID'] ?>&del=true"
               onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">Удалить</a>
        <?php }  ?> 
        <hr>
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post'>
            <table>
                <tr>
                    <td>Название материала</td>
                    <td><input type="text" name="NAME" value="<?= $row['NAME'] ?>" required></td>
				<tr>
                    <td>Название групп материала</td>
                    <td><input type="text" name="GROUP_NAME" value="<?= $row['GROUP_NAME'] ?>" ></td>
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
        <?php if ($_GET['ID'] > 0) {
			 
			 $res = $DB->Query("SELECT * FROM `ba_gost` ORDER BY `NUM_OA`");
			 echo "<form method='post'><table>";
			 while($row=$res->Fetch())
			 {
				 if($row['NUM_OA']&&!$row['NON_ACTUAL'])
				 echo "<tr><td><input type='checkbox' name='ch[".$row['NUM_OA']."]' ". ($row['DOP']==$_GET['ID'] ? 'checked' : '') ." ></td><td>".$row['NUM_OA']." ".$row['GOST']." ".$row['GOST_PUNKT']." | ". $row['SPECIFICATION']." | ". $row['GOST_OBJECT']."| ". $row['NAME_GOST']."</td><tr>";
			 }
			// var_dump($_POST['GOST_TYPE']);//var_dump($_POST['NORM_DOP']); ?>
			 <input type="hidden" name="OA" value="OA">
			<input type='submit' value='Сохранить'>
			</table></form>
            <hr>
            
            <?php
        }
    }
} else {// Вывод скиска ГОСТов

    global $DB;
	
    
    $res = $DB->Query("SELECT * FROM `MATERIALS`");
    


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    <a href="/materials.php?ID=0">Добавить новый материал</a> 
	
	<br>
	<br>
    <hr>
    <table id="mat_table">
	<thead>
    <!--<tr>-->
		<th></th>
        <th>Название материала</th>
        <!--<th>Нормативный документ на метод испытания</th>
        <th>Нормативная документация</th>-->
        
	</thead>
	<tbody>
    <!--</tr>-->
    <?php
	$i=0;
    while ($row = $res->Fetch()) {
		
		
        echo "<tr>";
		echo "<td></td>";
		echo "<td><a href='/materials.php?ID=" . $row['ID'] . "'>" . $row['NAME'] . "</a></td>";
		
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