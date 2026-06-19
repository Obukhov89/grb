<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05.04.2019
 * Time: 12:12
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Помещения");

?>
<style>
    /* #oborud th, #oborud td {
        border:1px solid
    } */
</style>
<?php

$notIn = [9,10,11,14,20,28,37];
if(isset($_POST['ID'])){
    if(!empty($_POST['ID'])){
        $res = $DB->Query("UPDATE `ROOMS` SET 
        `LAB_ID`='".$_POST['LAB_ID']."',
		`NUMBER`='".$_POST['NUMBER']."',
		`NAME`='".$_POST['NAME']."',
		`SPEC`='".$_POST['SPEC']."',
		`AREA`='".$_POST['AREA']."',
		`PARAMS`='".$_POST['PARAMS']."',
		`SPEC_EQUIP`='".$_POST['SPEC_EQUIP']."',
		`DOCS`='".$_POST['DOCS']."',
		`PURPOSE`='".$_POST['PURPOSE']."',
		`PLACEMENT`='".$_POST['PLACEMENT']."',
		`COMMENT`='".$_POST['COMMENT']."'
        WHERE `ID`=".(int)$_GET['ID']);
        if($res->result = 1){
            echo "<h3>Успешно</h3>";
        }else{
            echo "<h3>Ошибка</h3>";
        }
    }else{
        $res = $DB->Query("INSERT INTO `ROOMS`
        (`LAB_ID`, `NUMBER`, `NAME`, `SPEC`, `AREA`, `PARAMS`, `SPEC_EQUIP`, `DOCS`, `PURPOSE`, `PLACEMENT`, `COMMENT`)
        VALUES
        ('".$_POST['LAB_ID']."','".$_POST['NUMBER']."','".$_POST['NAME']."','".$_POST['SPEC']."',".$_POST['AREA'].",'".$_POST['PARAMS']."','".$_POST['SPEC_EQUIP']."','".$_POST['DOCS']."','".$_POST['PURPOSE']."','".$_POST['PLACEMENT']."','".$_POST['COMMENT']."')");
        if($res->result = 1){
            echo "<h3>Успешно</h3>";
        }else{
            echo "<h3>Ошибка</h3>";
        }
    }
}

// Поиск по ID. Вывод формы для создания/редактирования
if(isset($_GET['ID'])){
    global $DB;

    if(!empty($_GET['del'])){
        $res = $DB->Query("DELETE FROM `ROOMS` WHERE `ID`=".(int)$_GET['ID'])->Fetch();
        if($res->result = 1){
            echo "<h3>Успешно</h3>";
        }else{
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href=\"/rooms.php\">К списку</a>";
    }else {

        $res = $DB->Query("SELECT * FROM `ROOMS` WHERE `ID`={$_GET['ID']}");
        $row = $res->Fetch();
        ?>
        <a href="/rooms.php">К списку</a>
        <?php if($_GET['ID']>0){?> |
            <a href="/rooms.php?ID=<?= $_GET['ID'] ?>&del=true" onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">Удалить</a>
        <?php } ?>
        <hr>
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post'>
            <table>
                <tr>
                    <td>Лаборатория</td>
                    <td>
                        <select name="LAB_ID">
                            <option value="0" selected disabled="">Выберите</option>
                            <?php
                            $res = $DB->Query("SELECT * FROM `ba_laba`");
                            while ($value = $res->Fetch()) {
                                echo "<option ";
                                if ($row['LAB_ID'] == $value['ID']) {
                                    echo "selected";
                                }
                                echo " value='" . $value['ID'] . "'>" . $value['NAME'] . "</option>";
                            }
                            ?>
                        </select>
                    </td>
					
				<tr>
                    <td>Номер</td>
                    <td><input type="text" name="NUMBER" value="<?= $row['NUMBER'] ? $row['NUMBER']:'' ?>"></td>
				<tr>
                    <td>Название</td>
                    <td><input type="text" name="NAME" value="<?= $row['NAME'] ?>" required></td>
				<tr>
                    <td>ТИП</td>
                    <td>
						<select name="SPEC">
                            <option value="0" selected>Специальное</option>
                            <option value="1" <?= $row['SPEC'] ? 'selected' : '' ?>>Приспособленное</option>
                        </select>
					</td>
				<tr>
                    <td>Назначение</td>
                    <td><textarea name="PURPOSE" value="<?= $row['PURPOSE'] ?>" required><?= $row['PURPOSE'] ?></textarea></td>
				<tr>
                    <td>Площадь</td>
                    <td><input type="number" step="0.01" name="AREA" value="<?= $row['AREA'] ?>" required></td>
				<tr>
                    <td>Контролируемые параметры</td>
                    <td><textarea name="PARAMS" value="<?= $row['PARAMS'] ?>" required><?= $row['PARAMS'] ?></textarea></td>
				<tr>
                    <td>Специальное оборудование</td>
                    <td><textarea name="SPEC_EQUIP" value="<?= $row['SPEC_EQUIP'] ?>" required><?= $row['SPEC_EQUIP'] ?></textarea></td>
				<tr>
                    <td>Право собственности</td>
                    <td><textarea name="DOCS" value="<?= $row['DOCS'] ?>" required><?= $row['DOCS'] ?></textarea></td>
				<tr>
                    <td>Место нахождения</td>
                    <td><textarea name="PLACEMENT" value="<?= $row['PLACEMENT'] ?>" required><?= $row['PLACEMENT'] ?></textarea></td>
				<tr>
                    <td>Примечание</td>
                    <td><textarea name="COMMENT" value="<?= $row['COMMENT'] ?>"><?= $row['COMMENT'] ?></textarea></td>
            </table>
            <input type="hidden" name="ID" value="<?= $row['ID'] ?>">
            <input type='submit' value='Сохранить'>
        </form>
        <?php
    }
}else{// Вывод скиска

global $DB;

$res = $DB->Query("SELECT * FROM `ROOMS` ORDER BY `NUMBER`");
?>
<a href="/rooms.php?ID=0">Добавить помещение</a> | <a href="#" onclick="form6()">Скачать форму 6</a>
<hr>
<table id='oborud'>
<th>Наименование</th>
<?php
    while ($row = $res->Fetch()) {
        ?>
        <tr>
            <td><a href='/rooms.php?ID=<?= $row['ID'] ?>'> <?= $row['NAME'] ?> <?= $row['NUMBER'] ? $row['NUMBER']:''?></a></td><td><input type="checkbox" class="ch" value="<?= $row['ID'] ?>" <?= !in_array($row['ID'], $notIn) ? 'checked' : ''?>></td>
    <?php
    }
    echo "</table>";
}
?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>
<script src="/ulab/assets/js/jquery-3.5.1.min.js" type="text/javascript"></script>
<script>
	function form6() {
		let checked = $('.ch:checked')
		let strRoom = ''
		checked.each(function () {
			let what = $(this).val()
			strRoom += `&ROOMS[]=${what}`
		})
		window.open(`/protocol_generator/form6_rooms.php?id=1${strRoom}`, "_blank");
		}
</script>
