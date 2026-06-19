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
$APPLICATION->SetTitle("Добавление реактива");
?>
<?php

if(!empty($_POST['NAME']))

{		
	if($_GET['ID'])
	{
		$res = $DB->Query("UPDATE `REACTIVE_OST` SET `REACT_NAME`='" . $_POST['NAME'] . "', `REACT_OST`=" . $_POST['PRIKHOD'] . ", `ID_LAB`=" . $_POST['lab_gost'] . " WHERE `ID`=".$_GET['ID']);
        if ($res->result = 1) {
        echo "<h3>Реактив успешно обновлен</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
		
	}
	else
	{
        $res = $DB->Query("INSERT INTO `REACTIVE_OST`
        (REACT_NAME, REACT_OST, ID_LAB) VALUES ('" . $_POST['NAME'] . "', " . $_POST['PRIKHOD'] . ", " . $_POST['lab_gost'] . ")");
        if ($res->result = 1) {
        echo "<h3>Реактив успешно добавлен</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
	}
		
}
// Поиск по ID. Вывод формы для создания/редактирования

if ($_GET['ID'])
{
	$res = $DB->Query("SELECT * FROM `REACTIVE_OST` WHERE `ID` = ".$_GET['ID'])->Fetch();
	?>
	
	<a href="/reactive.php">К списку реактивов</a>
		
     
        <form method='post'>
            <table id="oborud_table">
			<tbody id="oborud_body">

				<tr>
                   <td>Наименование реактива</td><td  style="width:350px;"><input class="oborud" type="text"  name="NAME" value="<?= $res['REACT_NAME'] ?>" required></td>
				</tr>
				<tr>
                   <td>Входящий остаток</td><td><input class="oborud" type="number" step="0.0001"  name="PRIKHOD" value="<?= $res['REACT_OST'] ?>" required></td>
				</tr>
				<tr>
					<td>Лаборатория</td><td>
					<select class="obl" name="lab_gost" id="lab" style="float: right;">
						
						<?php
						$select = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID` != '6'");
						while ($option = $select->Fetch()) {
							echo "<option value='" . $option['ID'] . "' ".($option['ID']==$res['ID_LAB'] ? 'selected' : '').">" . $option['NAME'] . "</option>";
						}
						?>
					</select>
					</td>
				</tr>
				</tbody>
            </table>

			
            <input type='submit' value='Сохранить'>
        </form>
	
	<?
}
else
{
?> 
       
      
        <a href="/reactive.php">К списку реактивов</a>
		
     
        <form method='post'>
            <table id="oborud_table">
			<tbody id="oborud_body">

				<tr>
                   <td>Наименование реактива</td><td  style="width:350px;"><input class="oborud" type="text"  name="NAME" value="" required></td>
				<tr>
                   <td>Входящий остаток</td><td><input class="oborud" type="number" step="0.0001"  name="PRIKHOD" value="0" required></td>
				<tr>
					<td>Лаборатория</td><td>
					<select class="obl" name="lab_gost" id="lab" style="float: right;">
						
						<?php
						$select = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID` != '6'");
						while ($option = $select->Fetch()) {
							echo "<option value='" . $option['ID'] . "'>" . $option['NAME'] . "</option>";
						}
						?>
					</select>
					</td>
		
				</tbody>
            </table>

			
            <input type='submit' value='Сохранить'>
        </form>

  
		 
            
     
<?php
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
