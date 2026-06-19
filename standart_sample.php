<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05.04.2019
 * Time: 12:12
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Стандартные образцы");

?>
<style>
    /* #oborud th, #oborud td {
        border:1px solid
    } */
</style>
<?php
if(isset($_POST['ID'])){
    if(!empty($_POST['ID'])){
        $res = $DB->Query("UPDATE `ST_SAMPLE` SET 
        `TYPE`='".$_POST['TYPE']."',
		`NUMBER`='".$_POST['NUMBER']."',
		`NAME`='".$_POST['NAME']."',
		`CATEGORY`='".$_POST['CATEGORY']."',
		`MANUFACTURER`='".$_POST['MANUFACTURER']."',
		`MASS_FRACTION`='".$_POST['MASS_FRACTION']."',
		`INACCURACY`='".$_POST['INACCURACY']."',
		`DOP`='".$_POST['DOP']."',
		`PURPOSE`='".$_POST['PURPOSE']."',
		`NORM_DOC`='".$_POST['NORM_DOC']."',
		`SHELF_LIFE`='".$_POST['SHELF_LIFE']."',
		`DATE`='".$_POST['DATE']."',
		`COMMENT`='".$_POST['COMMENT']."'
        WHERE `ID`=".(int)$_GET['ID']);
        if($res->result = 1){
            echo "<h3>Успешно</h3>";
        }else{
            echo "<h3>Ошибка</h3>";
        }
    }else{
        $res = $DB->Query("INSERT INTO `ST_SAMPLE`
        (`TYPE`, `NUMBER`, `NAME`, `CATEGORY`, `MANUFACTURER`, `MASS_FRACTION`, `INACCURACY`, `DOP`, `PURPOSE`, `NORM_DOC`, `SHELF_LIFE`, `DATE`, `COMMENT`)
        VALUES
        ('".$_POST['TYPE']."','".$_POST['NUMBER']."','".$_POST['NAME']."','".$_POST['CATEGORY']."','".$_POST['MANUFACTURER']."','".$_POST['MASS_FRACTION']."','".$_POST['INACCURACY']."',
		'".$_POST['DOP']."','".$_POST['PURPOSE']."','".$_POST['NORM_DOC']."','".$_POST['SHELF_LIFE']."','".$_POST['DATE']."','".$_POST['COMMENT']."')");
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
        $res = $DB->Query("DELETE FROM `ST_SAMPLE` WHERE `ID`=".(int)$_GET['ID'])->Fetch();
        if($res->result = 1){
            echo "<h3>Успешно</h3>";
        }else{
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href=\"/standart_sample.php\">К списку</a>";
    }else {

        $res = $DB->Query("SELECT * FROM `ST_SAMPLE` WHERE `ID`=" . (int)$_GET['ID']);
        $row = $res->Fetch();
        ?>
        <a href="/standart_sample.php">К списку</a>
        <?php if($_GET['ID']>0){?> |
            <a href="/standart_sample.php?ID=<?= $_GET['ID'] ?>&del=true" onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">Удалить</a>
        <?php } ?>
        <hr>
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post'>
            <table>
                
					
				<tr>
                    <td>Номер</td>
                    <td  style="width:500px"><input type="text" class="oborud" name="NUMBER" value="<?= $row['NUMBER'] ? $row['NUMBER']:'' ?>"></td>
				<tr>
                    <td>Наименование</td>
                    <td><textarea name="NAME" value="<?= $row['NAME'] ?>" required><?= $row['NAME'] ?></textarea></td>
				<tr>
                    <td>ТИП</td>
                    <td><input type="text" class="oborud" name="TYPE" value="<?= $row['TYPE'] ?>" ></td>
				<tr>
                    <td>Категория</td>
                    <td><input type="text" class="oborud" name="CATEGORY" value="<?= $row['CATEGORY'] ?>" ></td>
				<tr>
                    <td>Изготовитель</td>
                    <td><input type="text" class="oborud" name="MANUFACTURER" value="<?= $row['MANUFACTURER'] ?>" required></td>
				<tr>
                    <td>Назначение</td>
                    <td><textarea name="PURPOSE" value="<?= $row['PURPOSE'] ?>" required><?= $row['PURPOSE'] ?></textarea></td>
				<tr>
                    <td>Массовые доли компонентов</td>
                    <td><textarea name="MASS_FRACTION" value="<?= $row['MASS_FRACTION'] ?>" required><?= $row['MASS_FRACTION'] ?></textarea></td>
				<tr>
                    <td>Погрешность</td>
                    <td><textarea name="INACCURACY" value="<?= $row['INACCURACY'] ?>" required><?= $row['INACCURACY'] ?></textarea></td>
				<tr>
                    <td>Доп. сведения</td>
                    <td><textarea name="DOP" value="<?= $row['DOP'] ?>" required><?= $row['DOP'] ?></textarea></td>
				<tr>
                    <td>Нормативный докмуент</td>
                    <td><textarea name="NORM_DOC" value="<?= $row['NORM_DOC'] ?>" required><?= $row['NORM_DOC'] ?></textarea></td>
				<tr>
                    <td>Срок годности</td>
                    <td><textarea name="SHELF_LIFE" value="<?= $row['SHELF_LIFE'] ?>" required><?= $row['SHELF_LIFE'] ?></textarea></td>
				<tr>
                    <td>Дата выпуска</td>
                    <td><input type="text" name="DATE" value="<?= $row['DATE'] ? $row['DATE'] : '' ?>"></td>
				
				<tr>
                    <td>Примечание</td>
                    <td><textarea name="COMMENT" value="<?= $row['COMMENT'] ?>" ><?= $row['COMMENT'] ?></textarea></td>
            </table>
            <input type="hidden" name="ID" value="<?= $row['ID'] ?>">
            <input type='submit' value='Сохранить'>
        </form>
        <?php
    }
}else{// Вывод скиска

global $DB;

$res = $DB->Query("SELECT * FROM `ST_SAMPLE`");
?>
<a href="/standart_sample.php?ID=0">Добавить СО</a>
<hr>
<table id='oborud'>
<th>Наименование</th>
<?php
    while ($row = $res->Fetch()) {
        ?>
        <tr>
            <td><a href='/standart_sample.php?ID=<?= $row['ID'] ?>'> <?= $row['NAME'] ?> <?= $row['NUMBER'] ? $row['NUMBER']:''?></a></td>
    <?php
    }
    echo "</table>";
}
?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>
