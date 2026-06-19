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
if(isset($_POST['ID'])){
    if(!empty($_POST['ID'])){
        $res = $DB->Query("UPDATE `ROOMS` SET 
        `NAME`='".$_POST['NUMBER']."',
		`LAB_ID`='".$_POST['NUM_ROOM']."'
        WHERE `ID`=".(int)$_GET['ID']);
        if($res->result = 1){
            echo "<h3>Успешно</h3>";
        }else{
            echo "<h3>Ошибка</h3>";
        }
    }else{
        $res = $DB->Query("INSERT INTO `ba_laba`
        (`NAME`, `NUM_ROOM`)
        VALUES
        ('".$_POST['NAME']."','".$_POST['NUM_ROOM']."')");
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
        $res = $DB->Query("DELETE FROM `ba_laba` WHERE `ID`=".(int)$_GET['ID'])->Fetch();
        if($res->result = 1){
            echo "<h3>Успешно</h3>";
        }else{
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href=\"/laboratory.php\">К списку</a>";
    }else {

        $res = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID`=" . (int)$_GET['ID']);
        $row = $res->Fetch();
        ?>
        <a href="/laboratory.php">К списку</a>
        <?php if($_GET['ID']>0){?> |
            <a href="/laboratory.php?ID=<?= $_GET['ID'] ?>&del=true" onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">Удалить</a>
        <?php } ?>
        <hr>
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post'>
            <table>
                <tr>
                    <td>Наименование</td>
                    <td><input type="text" name="NAME" value="<?= $row['NAME'] ?>"></td>
				<tr>
                    <td>Номер кабинета</td>
                    <td><input type="text" name="NUM_ROOM" value="<?= $row['NUM_ROOM'] ?>" disabled></td>
            </table>
            <input type="hidden" name="ID" value="<?= $row['ID'] ?>">
            <input type='submit' value='Сохранить'>
        </form>
        <?php
    }
}else{// Вывод скиска

global $DB;

$res = $DB->Query("SELECT * FROM `ba_laba`");
?>
<a href="/laboratory.php?ID=0">Добавить новую лабораторию</a>
<hr>
<table id='oborud'>
<th>Наименование</th>
<?php
    while ($row = $res->Fetch()) {
        ?>
        <tr>
            <td><a href='/laboratory.php?ID=<?= $row['ID'] ?>'><?= $row['NAME'] ?></a></td>
    <?php
    }
    echo "</table>";
}
?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>
