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
$APPLICATION->SetTitle("Добавление расхода реактива");
?>
<?php

if(!empty($_POST['id_reag']))

{			
        $res = $DB->Query("INSERT INTO `REAGENTS_FLOW`
        (ID_REAG, ID_GOST, FLOW_MIN, FLOW_MAX, ED) VALUES ('" . $_POST['id_reag'] . "', " . $_POST['id_gost'] . ", '" . $_POST['flow_min'] . "', " . $_POST['flow_max'] . ", '" . $_POST['ed'] . "')");
        if ($res->result = 1) {
        echo "<h3>Запись о расходе реактива успешно добавлена</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
		
		
}
// Поиск по ID. Вывод формы для создания/редактирования

       
        ?>
        <a href="/reagents_flow.php">К списку</a>
		
     
        <form method='post'>
            <table id="oborud_table">
			<tbody id="oborud_body">

				<tr>
                   <td>Наименование реактива</td><td  style="width:350px;">
				   <select name="id_reag" style="width:350px" class="gost">
							
										<option value="" disabled selected>Выберите</option>
										<?php
										$ba_reag = $DB->Query("SELECT * FROM `REACTIVE_OST` WHERE 1");
																				
										while ($reag = $ba_reag->Fetch()) {
											
											echo "<option value='" . $reag['ID'] . "'>" . $reag['REACT_NAME'] . "</option>";
										}
										?>
									</select>
				   </td>
				<tr>
                   <td>Наименование ГОСТ</td><td>
				   <select name="id_gost" style="width:350px" class="gost">
							
										<option value="" disabled selected>Выберите</option>
										<?php
										$ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE 1 ORDER BY `GOST`");
																				
										while ($gost = $ba_gost->Fetch()) {
											
											echo "<option value='" . $gost['ID'] . "'>" . $gost['GOST'] . " " . $gost['GOST_PUNKT'] ." " . $gost['SPECIFICATION'] . "</option>";
										}
										?>
									</select>
				   </td>
				   <tr>
                    <td>Расход</td>
					<td>
						<div class='dataTemperature' style="display:flex">от&nbsp; <input class="obl_num" type="number" step="0.1" name="flow_min" value="0.0">&nbsp;до&nbsp; <input class="obl_num" type="number" step="0.1" name="flow_max" value="0.0">
							
						</div>
					</td>
				<tr>
                    <td>Единица измерения</td>
					<td>
						<select name="ed" style="width:350px" class="gost">
							
										<option value="" disabled selected>Выберите</option>
										<option value="кг"  >кг</option>
										<option value="г"  >г</option>
										<option value="л"  >л</option>
										<option value="мл"  >мл</option>
										
										
									</select>
					</td>
				
		
				</tbody>
            </table>

			
            <input type='submit' value='Сохранить'>
        </form>
       
  
		 
            
     
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>