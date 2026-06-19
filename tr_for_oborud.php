<? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
?>

				<tr class='trCertificate'>
                    <td></td>
                    <td><textarea class="oborud" 
                                name="certificate[][name]"><?= $row['certificate_of_verification'] ?></textarea>
                    </td>
					<td><input name="certificate[][file]" type="file" style='margin-left:15px'></td>
					<td><input style='margin-left:5px' type='button' class='del' value='-'></td>
				<tr>
     

<? exit ?>