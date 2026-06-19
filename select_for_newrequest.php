<? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
?>
<tr><td width="250" height="40"></td><td><input class="tz" list="materials" type="text" name="MATERIAL[]" value="" data-id="<?=$_GET['KEY']?>" required ></td>
		<td><input class="tz" type='number' name="OBIEM[]" min='1' value="1" data-id="<?=$_GET['KEY']?>" required value=""></td>
		<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='del' value='-'></td>
		</tr>
							
     

<? exit ?>
