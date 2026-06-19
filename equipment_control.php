<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 28.03.2019
 * Time: 10:12
 */

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); 
   include $_SERVER["DOCUMENT_ROOT"]."/include/function.php"; 
?>
<?php
$APPLICATION->SetTitle("Оборудование");
?>
    <!-- <style>
         
 
         #oborud_table {
             border: 1px solid grey
         }
     </style>-->
<?php
if (isset($_POST['GOST'])) {
	$r = $DB->Query("SELECT * FROM `EQUIPMENT_CONTROL` WHERE `ID_EQUIP`=".$_GET['ID'])->Fetch();
	
    if (!empty($_POST['GOST'])&&$r['ID']>=0) {
        $res = $DB->Query("UPDATE `EQUIPMENT_CONTROL` SET 
        `GOST`='" . $_POST['GOST'] . "', 
        `ID_ETALON`='" . serialize($_POST['ID_ETALON']) . "', 
        `PARAMS_NAME`='" . serialize($_POST['PARAMS_NAME'])  . "', 
		`PARAMS_VALUE`='" . serialize($_POST['PARAMS_VALUE'])  . "', 
		`PARAMS_ACCURACY`='" . serialize($_POST['PARAMS_ACCURACY'])  . "'
		
        WHERE `ID_EQUIP`=" . (int)$_GET['ID']);
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
	}
	else
	{
	  
		$res = $DB->Query("INSERT INTO `EQUIPMENT_CONTROL`
        (`GOST`,`ID_ETALON`,`PARAMS_NAME`,`PARAMS_VALUE`,`PARAMS_ACCURACY`,`ID_EQUIP`)
        VALUES
        ('" . $_POST['GOST'] . "', '" . serialize($_POST['ID_ETALON']) . "', '" . serialize($_POST['PARAMS_NAME']) . "', '" . serialize($_POST['PARAMS_VALUE']) . "', '" . serialize($_POST['PARAMS_ACCURACY']) . "', " . (int)$_GET['ID'] . ")");
        /*-------------------'Не нормируется' окончание-------------------*/
		
		if ($res->result = 1) {
            echo "<h3>Успешно</h3><br>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
	
	}  
}

// Поиск по ID. Вывод формы для создания/редактирования
if (!empty($_GET['ID'])) {
	$row= $DB->Query("SELECT * FROM `EQUIPMENT_CONTROL` WHERE `ID_EQUIP`=".$_GET['ID'])->Fetch();
	$etalons = unserialize($row['ID_ETALON']);
	$params_name = unserialize($row['PARAMS_NAME']);
	$params_value = unserialize($row['PARAMS_VALUE']);
	$params_accuracy = unserialize($row['PARAMS_ACCURACY']);
?>
        <form method='post' enctype="multipart/form-data">
            <table id="oborud_table">
                
                <tr>
                    <td>ГОСТ</td>
                    <td><input class="oborud" type="text" name="GOST" value="<?= $row['GOST'] ?>" required></td>
                <tr>
                    <td>Эталоны</td>
                    <td><select class="obl2" name="ID_ETALON[]" multiple="multiple">
						
						<?php
						
						$select = $DB->Query("SELECT * FROM `ba_oborud` WHERE 1");
						$selected='';
						while ($option = $select->Fetch()) {
							$selected = ((in_array($option['ID'], $etalons)) ? 'selected' : '');
							echo "<option value='" . $option['ID'] . "'" . $selected . ">" . $option['OBJECT'] . ' ' . $option['REG_NUM'] . "</option>";
						}
						?>
					</select></td>
				
                <tr>
                    <td>Дата измерений</td>
                    <td><input class="oborud" type="date" name="DATE"
                               value="<?= $row['DATE']!='1970-01-01' ? $row['DATE'] : '' ?>">
                    </td>
					
				<tr>
                    <td>Наименование параметра</td>
                    <td><input class="oborud" type="text" name="PARAMS_NAME[]" value="<?= $params_name[0] ?>"></td>
					<td>Значение</td>
                    <td><input class="oborud" type="text" name="PARAMS_VALUE[]" value="<?= $params_value[0] ?>"></td>
					<td>Точность</td>
                    <td><input class="oborud" type="text" name="PARAMS_ACCURACY[]" value="<?= $params_accuracy[0] ?>"></td>
				
				<tr>
                    <td>Наименование параметра</td>
                    <td><input class="oborud" type="text" name="PARAMS_NAME[]" value="<?= $params_name[1] ?>"></td>
					<td>Значение</td>
                    <td><input class="oborud" type="text" name="PARAMS_VALUE[]" value="<?= $params_value[1] ?>"></td>
					<td>Точность</td>
                    <td><input class="oborud" type="text" name="PARAMS_ACCURACY[]" value="<?= $params_accuracy[1] ?>"></td>
				
				<tr>
                    <td>Наименование параметра</td>
                    <td><input class="oborud" type="text" name="PARAMS_NAME[]" value="<?= $params_name[2] ?>"></td>
					<td>Значение</td>
                    <td><input class="oborud" type="text" name="PARAMS_VALUE[]" value="<?= $params_value[2] ?>"></td>
					<td>Точность</td>
                    <td><input class="oborud" type="text" name="PARAMS_ACCURACY[]" value="<?= $params_accuracy[2] ?>"></td>
				
				<tr>
                    <td>Наименование параметра</td>
                    <td><input class="oborud" type="text" name="PARAMS_NAME[]" value="<?= $params_name[3] ?>"></td>
					<td>Значение</td>
                    <td><input class="oborud" type="text" name="PARAMS_VALUE[]" value="<?= $params_value[3] ?>"></td>
					<td>Точность</td>
                    <td><input class="oborud" type="text" name="PARAMS_ACCURACY[]" value="<?= $params_accuracy[3] ?>"></td>
               
            </table>

            <input type="hidden" name="ID" value="<?= $row['ID'] ?>">
            <input id='status' type='hidden' value='<?= $USER->GetID() ?>'></input>
            <input type='submit' value='Сохранить'><br/><br/>
        </form>
		<a href="/protocol_generator/mes_protocol.php?ID=<?= $row['ID_EQUIP']?>">Сформировать протокол измерений</a>
        <?php
    }
 else {}?>

  
  
    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>

	<link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
	<script>
	 $(document).ready(function () {
            
			$('.obl2').select2({
				placeholder: "Выберите"
				
			});
			
	 });
	</script>
    
   
  

   
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>