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
$APPLICATION->SetTitle("Зерновые составы");
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
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost`");
		
        while ($gost = $ba_gost->Fetch()) {
			$res_zern = $DB->Query("SELECT * FROM `ZERN` WHERE `GOST_ID`=".$gost['ID'])->Fetch();
			//echo $res_zern['GOST_ID'];
            echo "<option data-type='" . $gost['NORM_TEXT'] . "' " . ($res_zern['ID']==$_GET['ID'] ? 'selected' : '') . " value='" . $gost['ID']. "'>" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
        }
        ?>
    </datalist>
<?php

if (isset($_POST['ID'])) {
    if (!empty($_POST['ID'])) {
		$norm_1 = serialize($_POST['NORM1']);
		$norm_2 = serialize($_POST['NORM2']);
		$name = $_POST['NAME'];
		$res = $DB->Query("UPDATE `ZERN` SET 
        `GOST_ID`='" . $_POST['gost1'] . "', 
        `NORM1`='" . $norm_1 . "',
		`NORM2`='" . $norm_2 . "',
		`NAME`='" . $name . "',
		`TYPE_MAT_ID` = '" .$_POST['group_mat'] ."'
        WHERE `ID`=" . (int)$_GET['ID']);
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    } else {
		$norm_1 = serialize($_POST['NORM1']);
		$norm_2 = serialize($_POST['NORM2']);
		$name = $_POST['NAME'];
        $res = $DB->Query("INSERT INTO `ZERN`
        (GOST_ID,NORM1, NORM2, TYPE_MAT_ID, NAME) VALUES ('" . $_POST['gost1'] . "', '" . $norm_1 . "', '" . $norm_2 . "','" . $_POST['group_mat'] . "', '" .$name. "')");
        if ($res->result = 1) {
            header("Location: //" . $_SERVER['HTTP_HOST']
                . "/zern.php");
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
	// $select_group = $DB->Query("SELECT `GROUPS` FROM `MATERIALS` WHERE `ID`='4'")->Fetch();
			// $select_groups = unserialize($select_group['GROUPS']);
	// var_dump($select_groups);
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

        $res = $DB->Query("SELECT * FROM `ZERN` WHERE `ID`=" . (int)$_GET['ID']);
        $row = $res->Fetch();
		$group_val = unserialize($row['GROUPS']);
		$norm1 = unserialize($row['NORM1']);
		$norm2 = unserialize($row['NORM2']);
		
		//var_dump($norm1);
        ?>
        <a href="/zern.php">К списку</a>
		
        <?php if ($_GET['ID'] > 0) { ?> |
            <a href="/zern.php?ID=<?= $_GET['ID'] ?>&del=true"
               onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">Удалить</a>
        <?php }  ?> 
        <hr>
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post'>
            <table>
                <tr><td>Название</td><td><input type="text" name="NAME" class="gost" value="<?= $row['NAME'] ?>"></td><tr>
				<tr>
                    <td>ГОСТ</td>
                    <td><!--<input class="tz" multiple list="gost" type="text" name="gost1"  value="-">-->
					
					<select name="gost1" style="width:350px" class="gost" >
									<option value="" disabled selected>Выберите</option>
									<?php
										$ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
										while ($gost = $ba_gost->Fetch()) {
											$res_zern = $DB->Query("SELECT * FROM `ZERN` WHERE `GOST_ID`=".$gost['ID'])->Fetch();
											echo "<option data-price='" . $gost['PRICE'] . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] .  "' ".($res_zern['ID']==$_GET['ID'] ? 'selected' : '').">" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] .  "</option>";
										}
										?>
								</select>
					</td>
				<tr>
                    <td>Группа материала</td>
                    <td><select class="gost" name="group_mat">
        <option value="0" selected>Выбрать</option>
        <?php
        $select = $DB->Query("SELECT * FROM `MATERIALS`");
		$select_groups = array();
        while ($option = $select->Fetch()) {
			$select_group = $DB->Query("SELECT `GROUPS` FROM `MATERIALS` WHERE `ID`=".$option['ID'])->Fetch();
			$select_groups = unserialize($select_group['GROUPS']);
            echo "<option value='" . $option['ID'] . "'>" . $option['NAME'] . "</option>";
			foreach ($select_groups as $s => $group)
				
			{  
				echo "<option value='" . $option['ID'] .'-'.$s. "' ".($option['ID'].'-'.$s==$row['TYPE_MAT_ID'] ? 'selected' : '')."> -- ". $group  . "</option>";
			}
        }
		
        ?>
    </select></td>
				   <tr>
				   <td>Сито 200 мм<input type="text" name="NORM1[0]" value="<?= $norm1[0] ?>"><input type="text" name="NORM2[0]" value="<?= $norm2[0] ?>"></td>
				   <tr>
                   <td>Сито 180 мм<input type="text" name="NORM1[44]" value="<?= $norm1[44] ?>"><input type="text" name="NORM2[44]" value="<?= $norm2[44] ?>"></td>
				    <tr>
                   <td>Сито 126 мм<input type="text" name="NORM1[43]" value="<?= $norm1[43] ?>"><input type="text" name="NORM2[43]" value="<?= $norm2[43] ?>"></td>
					<tr>
                   <td>Сито 150 мм<input type="text" name="NORM1[57]" value="<?= $norm1[57] ?>"><input type="text" name="NORM2[57]" value="<?= $norm2[57] ?>"></td>
					<tr>
						<td>Сито 125 мм<input type="text" name="NORM1[50]" value="<?= $norm1[50] ?>"><input type="text" name="NORM2[50]" value="<?= $norm2[50] ?>"></td>
					<tr>
                   <td>Сито 120 мм<input type="text" name="NORM1[1]" value="<?= $norm1[1] ?>"><input type="text" name="NORM2[1]" value="<?= $norm2[1] ?>"></td>
				   <tr>
                   <td>Сито 105 мм<input type="text" name="NORM1[58]" value="<?= $norm1[58] ?>"><input type="text" name="NORM2[58]" value="<?= $norm2[58] ?>"></td>
				   <tr>
                   <td>Сито 100 мм<input type="text" name="NORM1[2]" value="<?= $norm1[2] ?>"><input type="text" name="NORM2[2]" value="<?= $norm2[2] ?>"></td>
				   <tr>
                   <td>Сито 90 мм<input type="text" name="NORM1[34]" value="<?= $norm1[34] ?>"><input type="text" name="NORM2[34]" value="<?= $norm2[34] ?>"></td>
				   <tr>
				   <td>Сито 87,5 мм<input type="text" name="NORM1[40]" value="<?= $norm1[40] ?>"><input type="text" name="NORM2[40]" value="<?= $norm2[40] ?>"></td>
				   <tr>
                   <td>Сито 80 мм<input type="text" name="NORM1[3]" value="<?= $norm1[3] ?>"><input type="text" name="NORM2[3]" value="<?= $norm2[3] ?>"></td>
				   <tr>
                   <td>Сито 70 мм<input type="text" name="NORM1[41]" value="<?= $norm1[41] ?>"><input type="text" name="NORM2[41]" value="<?= $norm2[41] ?>"></td>
				   <tr>
                   <td>Сито 63 мм<input type="text" name="NORM1[33]" value="<?= $norm1[33] ?>"><input type="text" name="NORM2[33]" value="<?= $norm2[33] ?>"></td>
				   <tr>
                   <td>Сито 60 мм<input type="text" name="NORM1[4]" value="<?= $norm1[4] ?>"><input type="text" name="NORM2[4]" value="<?= $norm2[4] ?>"></td>
				   <tr>
                   <td>Сито 55 мм<input type="text" name="NORM1[42]" value="<?= $norm1[42] ?>"><input type="text" name="NORM2[42]" value="<?= $norm2[42] ?>"></td>
				   <tr>
                   <td>Сито 50 мм<input type="text" name="NORM1[38]" value="<?= $norm1[38] ?>"><input type="text" name="NORM2[38]" value="<?= $norm2[38] ?>"></td>
				   <tr>
				   <td>Сито 45 мм<input type="text" name="NORM1[32]" value="<?= $norm1[32] ?>"><input type="text" name="NORM2[32]" value="<?= $norm2[32] ?>"></td>
				   <tr>
                   <td>Сито 40 мм<input type="text" name="NORM1[5]" value="<?= $norm1[5] ?>"><input type="text" name="NORM2[5]" value="<?= $norm2[5] ?>"></td>
				   <tr>
                   <td>Сито 35 мм<input type="text" name="NORM1[59]" value="<?= $norm1[59] ?>"><input type="text" name="NORM2[59]" value="<?= $norm2[59] ?>"></td>
				   <tr>
					<tr>
                   <td>Сито 31,5 мм<input type="text" name="NORM1[6]" value="<?= $norm1[6] ?>"><input type="text" name="NORM2[6]" value="<?= $norm2[6] ?>"></td>
				   <tr>
				   <td>Сито 30 мм<input type="text" name="NORM1[39]" value="<?= $norm1[39] ?>"><input type="text" name="NORM2[39]" value="<?= $norm2[39] ?>"></td>
				   <tr>
                   <td>Сито 25 мм<input type="text" name="NORM1[37]" value="<?= $norm1[37] ?>"><input type="text" name="NORM2[37]" value="<?= $norm2[37] ?>"></td>
				   <tr>
                   <td>Сито 22,4 мм<input type="text" name="NORM1[7]" value="<?= $norm1[7] ?>"><input type="text" name="NORM2[7]" value="<?= $norm2[7] ?>"></td>
				   <tr>
                   <td>Сито 20 мм<input type="text" name="NORM1[8]" value="<?= $norm1[8] ?>"><input type="text" name="NORM2[8]" value="<?= $norm2[8] ?>"></td>
				<tr>
					<td>Сито менее 20 мм<input type="text" name="NORM1[74]" value="<?= $norm1[74] ?>"><input
								type="text" name="NORM2[74]" value="<?= $norm2[74] ?>"></td>
				<tr>
                   <td>Сито 17,5 мм<input type="text" name="NORM1[47]" value="<?= $norm1[47] ?>"><input type="text" name="NORM2[47]" value="<?= $norm2[47] ?>"></td>
				   <tr>
                   <td>Сито 16 мм<input type="text" name="NORM1[9]" value="<?= $norm1[9] ?>"><input type="text" name="NORM2[9]" value="<?= $norm2[9] ?>"></td>
				   <tr>
                   <td>Сито 15 мм<input type="text" name="NORM1[10]" value="<?= $norm1[10] ?>"><input type="text" name="NORM2[10]" value="<?= $norm2[10] ?>"></td>
				   <tr>
				   <td>Сито 12,5 мм<input type="text" name="NORM1[35]" value="<?= $norm1[35] ?>"><input type="text" name="NORM2[35]" value="<?= $norm2[35] ?>"></td>
				   <tr>
                   <td>Сито 11,2 мм<input type="text" name="NORM1[11]" value="<?= $norm1[11] ?>"><input type="text" name="NORM2[11]" value="<?= $norm2[11] ?>"></td>
				   <tr>
                   <td>Сито 10 мм<input type="text" name="NORM1[12]" value="<?= $norm1[12] ?>"><input type="text" name="NORM2[12]" value="<?= $norm2[12] ?>"></td>
				<tr>
					<td>Сито менее 10 мм<input type="text" name="NORM1[73]" value="<?= $norm1[73] ?>"><input
								type="text" name="NORM2[73]" value="<?= $norm2[73] ?>"></td>
				<tr>
                   <td>Сито 8 мм<input type="text" name="NORM1[13]" value="<?= $norm1[13] ?>"><input type="text" name="NORM2[13]" value="<?= $norm2[13] ?>"></td>
				   <tr>
				   <td>Сито 7,5 мм<input type="text" name="NORM1[36]" value="<?= $norm1[36] ?>"><input type="text" name="NORM2[36]" value="<?= $norm2[36] ?>"></td>
				   <tr>
				   <td>Сито 5,6 мм<input type="text" name="NORM1[31]" value="<?= $norm1[31] ?>"><input type="text" name="NORM2[31]" value="<?= $norm2[31] ?>"></td>
				   <tr>
                   <td>Сито 5 мм<input type="text" name="NORM1[14]" value="<?= $norm1[14] ?>"><input type="text" name="NORM2[14]" value="<?= $norm2[14] ?>"></td>
				<tr>
					<td>Сито менее 5 мм<input type="text" name="NORM1[72]" value="<?= $norm1[72] ?>"><input
								type="text" name="NORM2[72]" value="<?= $norm2[72] ?>"></td>
				<tr>
                   <td>Сито 4 мм<input type="text" name="NORM1[15]" value="<?= $norm1[15] ?>"><input type="text" name="NORM2[15]" value="<?= $norm2[15] ?>"></td>
				   <tr>
                   <td>Сито 3 мм<input type="text" name="NORM1[46]" value="<?= $norm1[46] ?>"><input type="text" name="NORM2[46]" value="<?= $norm2[46] ?>"></td>
				   <tr>
                   <td>Сито 2,8 мм<input type="text" name="NORM1[45]" value="<?= $norm1[45] ?>"><input type="text" name="NORM2[45]" value="<?= $norm2[45] ?>"></td>
				   <tr>
                   <td>Сито 2,5 мм<input type="text" name="NORM1[16]" value="<?= $norm1[16] ?>"><input type="text" name="NORM2[16]" value="<?= $norm2[16] ?>"></td>
				<tr>
					<td>Сито менее 2,5 мм<input type="text" name="NORM1[71]" value="<?= $norm1[71] ?>"><input
								type="text" name="NORM2[71]" value="<?= $norm2[71] ?>"></td>
				<tr>
                   <td>Сито 2 мм<input type="text" name="NORM1[17]" value="<?= $norm1[17] ?>"><input type="text" name="NORM2[17]" value="<?= $norm2[17] ?>"></td>
				<tr>
					<td>Сито 1,6 мм<input type="text" name="NORM1[70]" value="<?= $norm1[70] ?>"><input
								type="text" name="NORM2[70]" value="<?= $norm2[70] ?>"></td>
				<tr>
                   <td>Сито 1,25 мм<input type="text" name="NORM1[18]" value="<?= $norm1[18] ?>"><input type="text" name="NORM2[18]" value="<?= $norm2[18] ?>"></td>

				<tr>
                   <td>Сито 1 мм<input type="text" name="NORM1[19]" value="<?= $norm1[19] ?>"><input type="text" name="NORM2[19]" value="<?= $norm2[19] ?>"></td>
				<tr>
					<td>Сито менее 1 мм<input type="text" name="NORM1[66]" value="<?= $norm1[66] ?>"><input
								type="text" name="NORM2[66]" value="<?= $norm2[66] ?>"></td>
				<tr>
                   <td>Сито 0,63 мм<input type="text" name="NORM1[20]" value="<?= $norm1[20] ?>"><input type="text" name="NORM2[20]" value="<?= $norm2[20] ?>"></td>
				   <tr>
                   <td>Сито 0,5 мм<input type="text" name="NORM1[21]" value="<?= $norm1[21] ?>"><input type="text" name="NORM2[21]" value="<?= $norm2[21] ?>"></td>
				   <tr>
                   <td>Сито 0,315 мм<input type="text" name="NORM1[22]" value="<?= $norm1[22] ?>"><input type="text" name="NORM2[22]" value="<?= $norm2[22] ?>"></td>
				   <tr>
                   <td>Сито 0,25 мм<input type="text" name="NORM1[23]" value="<?= $norm1[23] ?>"><input type="text" name="NORM2[23]" value="<?= $norm2[23] ?>"></td>
				   <tr>
                   <td>Сито 0,16 мм<input type="text" name="NORM1[24]" value="<?= $norm1[24] ?>"><input type="text" name="NORM2[24]" value="<?= $norm2[24] ?>"></td>
				   <tr>
                   <td>Сито 0,125 мм<input type="text" name="NORM1[25]" value="<?= $norm1[25] ?>"><input type="text" name="NORM2[25]" value="<?= $norm2[25] ?>"></td>
				   <tr>
                   <td>Сито 0,1 мм<input type="text" name="NORM1[26]" value="<?= $norm1[26] ?>"><input type="text" name="NORM2[26]" value="<?= $norm2[26] ?>"></td>
				   <tr>
                   <td>Сито 0,071 мм<input type="text" name="NORM1[27]" value="<?= $norm1[27] ?>"><input type="text" name="NORM2[27]" value="<?= $norm2[27] ?>"></td>
				   <tr>
                   <td>Сито 0,063 мм<input type="text" name="NORM1[28]" value="<?= $norm1[28] ?>"><input type="text" name="NORM2[28]" value="<?= $norm2[28] ?>"></td>
				   <tr>
					<td>Сито 0,05 мм<input type="text" name="NORM1[29]" value="<?= $norm1[29] ?>"><input type="text"
																										 name="NORM2[29]"
																										 value="<?= $norm2[29] ?>">
					</td>
					<tr>
						<td>Сито 0,010 мм<input type="text" name="NORM1[75]" value="<?= $norm1[75] ?>"><input
									type="text" name="NORM2[75]" value="<?= $norm2[75] ?>"></td>
					<tr>
						<td>Сито 0,005 мм<input type="text" name="NORM1[76]" value="<?= $norm1[76] ?>"><input
									type="text" name="NORM2[76]" value="<?= $norm2[76] ?>"></td>
                <tr>
                    <td>Сито 0,001 мм<input type="text" name="NORM1[77]" value="<?= $norm1[77] ?>"><input
                                type="text" name="NORM2[77]" value="<?= $norm2[77] ?>"></td>
                <tr>
                    <td>Сито менее 0,001 мм<input type="text" name="NORM1[78]" value="<?= $norm1[78] ?>"><input
                                type="text" name="NORM2[78]" value="<?= $norm2[78] ?>"></td>
					<tr>
						<td>Сито 0,80 мм<input type="text" name="NORM1[67]" value="<?= $norm1[67] ?>"><input
									type="text" name="NORM2[67]" value="<?= $norm2[67] ?>"></td>
					<tr>
						<td>Сито 0,40 мм<input type="text" name="NORM1[68]" value="<?= $norm1[68] ?>"><input
									type="text" name="NORM2[68]" value="<?= $norm2[68] ?>"></td>
					<tr>
						<td>Сито 0,20 мм<input type="text" name="NORM1[69]" value="<?= $norm1[69] ?>"><input
									type="text" name="NORM2[69]" value="<?= $norm2[69] ?>"></td>
				<tr>
					<td>Сито менее 0,16 мм<input type="text" name="NORM1[48]" value="<?= $norm1[48] ?>"><input
								type="text" name="NORM2[48]" value="<?= $norm2[48] ?>"></td>
				<tr>
					<td>Сито менее 0,125 мм<input type="text" name="NORM1[49]" value="<?= $norm1[49] ?>"><input
								type="text" name="NORM2[49]" value="<?= $norm2[49] ?>"></td>
				<tr>
					<td>Сито менее 0,05 мм<input type="text" name="NORM1[30]" value="<?= $norm1[30] ?>"><input
								type="text" name="NORM2[30]" value="<?= $norm2[30] ?>"></td>
				<tr>
					<td>Сито 56 мкм<input type="text" name="NORM1[51]" value="<?= $norm1[51] ?>"><input
								type="text" name="NORM2[51]" value="<?= $norm2[51] ?>"></td>
				<tr>
					<td>Сито 20 мкм<input type="text" name="NORM1[52]" value="<?= $norm1[52] ?>"><input
								type="text" name="NORM2[52]" value="<?= $norm2[52] ?>"></td>
				<tr>
					<td>Сито 10 мкм<input type="text" name="NORM1[53]" value="<?= $norm1[53] ?>"><input
								type="text" name="NORM2[53]" value="<?= $norm2[53] ?>"></td>
				<tr>
					<td>Сито 5 мкм<input type="text" name="NORM1[54]" value="<?= $norm1[54] ?>"><input
								type="text" name="NORM2[54]" value="<?= $norm2[54] ?>"></td>
				<tr>
					<td>Сито 2 мкм<input type="text" name="NORM1[55]" value="<?= $norm1[55] ?>"><input
								type="text" name="NORM2[55]" value="<?= $norm2[55] ?>"></td>
				<tr>
					<td>Сито менее 2 мкм<input type="text" name="NORM1[56]" value="<?= $norm1[56] ?>"><input
								type="text" name="NORM2[56]" value="<?= $norm2[56] ?>"></td>
				<tr>
					<td>Сито 1000 мкм<input type="text" name="NORM1[60]" value="<?= $norm1[60] ?>"><input
								type="text" name="NORM2[60]" value="<?= $norm2[60] ?>"></td>
				<tr>
					<td>Сито 850 мкм<input type="text" name="NORM1[61]" value="<?= $norm1[61] ?>"><input
								type="text" name="NORM2[61]" value="<?= $norm2[61] ?>"></td>
				<tr>
					<td>Сито 700 мкм<input type="text" name="NORM1[62]" value="<?= $norm1[62] ?>"><input
								type="text" name="NORM2[62]" value="<?= $norm2[62] ?>"></td>
				<tr>
					<td>Сито 600 мкм<input type="text" name="NORM1[63]" value="<?= $norm1[63] ?>"><input
								type="text" name="NORM2[63]" value="<?= $norm2[63] ?>"></td>
				<tr>
					<td>Сито 500 мкм<input type="text" name="NORM1[64]" value="<?= $norm1[64] ?>"><input
								type="text" name="NORM2[64]" value="<?= $norm2[64] ?>"></td>
				<tr>
					<td>Сито менее 500 мкм<input type="text" name="NORM1[65]" value="<?= $norm1[65] ?>"><input
								type="text" name="NORM2[65]" value="<?= $norm2[65] ?>"></td>
				<tr>
					<td>Дополнительно</td>
				   
            </table>
            <input type="hidden" name="ID" value="<?= $row['ID'] ?>">
            <input type='submit' value='Сохранить'>
        </form>
        <?php if ($_GET['ID'] > 0) {// var_dump($_POST['GOST_TYPE']);//var_dump($_POST['NORM_DOP']); ?>
            <hr>
            
            <?php
        }
    }
} else {// Вывод скиска ГОСТов

    global $DB;
	
    
    $res = $DB->Query("SELECT * FROM `ZERN`");
    


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    <a href="/zern.php?ID=0">Добавить новый материал</a> 
	
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
		echo "<td><a href='/zern.php?ID=" . $row['ID'] . "'>" . ( $row['NAME'] ? $row['NAME'] : $row['ID'] )  . "</a></td>";
		
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
	<link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
	<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
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

		$('select[name=gost1]').select2({
			placeholder: "Выберите"

		});
		
    </script>    
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
