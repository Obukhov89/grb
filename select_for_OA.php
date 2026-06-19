<? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$key = (int)$_GET['KEY']+1;
?>
	 <tr id="<?= $key ?>" data-gost="0">
                        
                            <td class="tdGost">
                                <input class="obl" type="text" name="equip_gost[<?= $key ?>]" value="" size="75">
							</td>
							<td class="tdGost">
                        
                                <select name="equip[<?= $key ?>]" style="width:350px" class="gost_d methods styleGost"
                                        data-id="<?= $key ?>" >
                        
                                    <option value="" disabled selected>Выберите</option>
                                    <?php
                                    $ba_gost = $DB->Query("SELECT * FROM `ba_oborud` WHERE 1");
									
                                    while ($gost = $ba_gost->Fetch()) {
											
											echo "<option value='" . $gost['ID'] . "' >" . ($gost['TYPE_OBORUD'] ? $gost['TYPE_OBORUD'] : '') . ' ' . $gost['OBJECT'] . ' | ' . $gost['REG_NUM'] . "</option>";
										}
                                    ?>
                                </select>
                                
                            </td>
							<td><?= $ident ?></td>
							<td><input class="obl" type="text" name="comment[<?= $key ?>]" value="Соответствует ГОСТ" size="75"></td>
                           
                            <?php

                       
                                echo "<td class='btn-method-add-del'>
										<input style='margin-left:5px' type='button' class='add2' value='+'>
										<input style='margin-left:5px' type='button' class='del' value='-'>
									</td>";
                       
                            ?>
                        </tr>
                     
<? exit ?>