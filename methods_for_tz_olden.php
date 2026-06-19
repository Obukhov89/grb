<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
//---------------------------'При выборе ГОСТа, добавление методики'-------------------------//

if (!empty($_POST['id_gost'])) {
    $gost_id = $_POST['id_gost'];
    $key = !empty($_POST['key']) ? (int)$_POST['key'] : 0;

	//--'Договор в request_new' начало--//
	$select_ba_tz = null;
	if (!empty($_POST['id_tz'])) {
		$select_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_POST['id_tz'])->Fetch();
	}

	$res_deal_contract = null;
	if (!empty($select_ba_tz['ID_Z'])) {
		$res_deal_contract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$select_ba_tz['ID_Z'])->Fetch();
	}

	$result_select_dogovor = null;
	if (!empty($res_deal_contract['ID_CONTRACT'])) {
		$result_select_dogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID` = " . (int)$res_deal_contract['ID_CONTRACT'])->Fetch();
	}
	//--'Договор в request_new' окончание--//

    //Получаем все методики привязанные к ГОСТу
    $metodic_list = $DB->Query("SELECT * FROM `ba_gost` WHERE `NUM_OA_NEW` > '0' AND `id_gost`=" . (int)$gost_id);

    if ($metodic_list->SelectedRowsCount()) {

        $g = !empty($_POST['sum_metodic']) ? (int)$_POST['sum_metodic'] : 0;

        while ($metodic = $metodic_list->Fetch()) {

            // $log = date ("Y.m.d G:i:s") . "\n";
            // $log .= print_r ($metodic, 1) . "\n";
            // file_put_contents ('1_LOG.txt', $log, FILE_APPEND);

            if (!$metodic['NON_ACTUAL']) {

				//--'Договор в request_new' начало--//
						$metodic_id = !empty($metodic['ID']) ? $metodic['ID'] : '';

						$metodic_price = !empty($metodic['PRICE']) ? $metodic['PRICE'] : '';

						if (!empty($result_select_dogovor['ID']) && !empty($result_select_dogovor['LONGTERM'])) {

							$price_metodic_longterm = null;
							if (!empty($metodic_id)) {
								$price_metodic_longterm = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT` = " . (int)$result_select_dogovor['ID'] . " AND `ID_GOST` = " . (int)$metodic_id)->Fetch();
							}

							$metodic_price = isset($price_metodic_longterm['PRICE']) ? $price_metodic_longterm['PRICE'] : $metodic_price;
						}
				//--'Договор в request_new' окончание--//
                ?>
				<!--'Нумерация материалов в ТЗ' начало-->
                <tr class="tr-gost" id="<?= $key ?>" data-gost="<?= $gost_id ?>">
				<!--'Нумерация материалов в ТЗ' окончание-->
                    <td class="tdGost">
                        <!------------------'Подключение select2 к методикам' начало----------------->
                        <select name="gost[<?= $key ?>][]" style="width:350px" class="gost methods styleGost"
                                data-id="<?= $key ?>" required>
                        <!------------------'Подключение select2 к методикам' окончание----------------->
                            <option value="" disabled selected>Выберите</option>
                            <?php
                            $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NUM_OA_NEW` > '0' AND `NON_ACTUAL` != 1 AND `id_gost`=" . (int)$_POST['id_gost']);
                            while ($gost = $ba_gost->Fetch()) {
								//--'Договор в request_new' начало--//
										$gost_id = !empty($gost['ID']) ? $gost['ID'] : '';

										$gost_price = !empty($gost['PRICE']) ? $gost['PRICE'] : '';

										if (!empty($result_select_dogovor['ID']) && !empty($result_select_dogovor['LONGTERM'])) {

											$price_gost_longterm = null;
											if (!empty($gost_id)) {
												$price_gost_longterm = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT` = " . (int)$result_select_dogovor['ID'] . " AND `ID_GOST` = " . (int)$gost_id)->Fetch();
											}

											$gost_price = isset($price_gost_longterm['PRICE']) ? $price_gost_longterm['PRICE'] : $gost_price;
										}

                                //echo "<option data-price='" . $gost['PRICE'] . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "' " . ($gost['ID'] == $metodic['ID'] ? 'selected' : '') . ">" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                echo "<option data-price='" . $gost_price . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost_id . "' " . ($gost_id == $metodic['ID'] ? 'selected' : '') . ">" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                            //--'Договор в request_new' окончание--//
							}
                            ?>
                        </select>
                    </td>
                    <td class="method">
                        <?php
                        $metodic_id = !empty($metodic['ID']) ? $metodic['ID'] : '';
                        ?>
                        <a href='/obl_acc.php?ID=<?= $metodic_id ?>' class='methodsLink' target='_blank'>
                            <div class='methodsLinkPng' title='Методика'></div>
                        </a>
                    </td>
                    <td class="tdGost">
                        <!--'Нумерация материалов в ТЗ' начало-->
                        <select name="gost_new[<?= $key ?>][]" style="width:350px" class="gost2 conditions styleGost"
                                data-id="<?= $key ?>">
                        <!--'Нумерация материалов в ТЗ' окончание-->
                            <option value="2522" selected>-- | --</option>
                            <?php
                            //$ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1 AND `id_gost`=" . (int)$_POST['id_gost']);
                            $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NUM_OA_NEW` > '0' AND `NON_ACTUAL` != 1");
                            while ($gost = $ba_gost->Fetch()) {
                                echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "'>" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                            }
                            ?>
                        </select></td>
                    <!----------------'Договор в request_new' начало--------------------->
                    <td class="price-method"><input type="number" name="price[<?= $key ?>][]" class="oborud"
                               value="<?= $metodic_price ?>" required></input></td>
                    <!----------------'Договор в request_new' окончание------------------>
                    <td><input type="checkbox" class="dash"></td>
                    <!----------------'Добавление номера и фона' начало--------------------->
                    <?php
                    if (!$g) {
                        echo "<td class='btn-method-add-del'>
                        <input style='margin-left:5px' type='button' class='add3' value='+'>
                        <input style='margin-left:5px' type='button' class='add2' value='+'>
                        <label class='label_del2'><input style='margin-left:5px' type='button' class='del2' value='<  >' > <div class='helper'>Данная кнопка позволяет добавить на 1-ю позицию возможность внесения новой методики. Внимание, данное действие отменить нельзя!</div> </label>
                        </td>";
                    } else {
                        echo "<td class='btn-method-add-del'>
                            <input style='margin-left:5px' type='button' class='add3' value='+'>
                            <input style='margin-left:5px' type='button' class='add2' value='+'>
                            <input style='margin-left:5px' type='button' class='del' value='-'>
										</td>";
                    }

                    $g++;
                    ?>
                    <!----------------'Добавление номера и фона' окончание------------------>
                </tr>
                <?php
            }
        }

    }

}
exit
?>
