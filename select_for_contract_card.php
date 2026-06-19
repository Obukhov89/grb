<? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
?>
<tr><td width="250" height="40"><input class="tz" type="number" name="PAYMENT[]" value="" data-id="<?=$_GET['KEY']?>" style='width:95%'></td><td><input class="tz" type="date" name="DATE_PAY[]" value="" data-id="<?=$_GET['KEY']?>" style='width:95%'></td>
		<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='del' value='-'></td>
		</tr>
							
     

<? exit ?>