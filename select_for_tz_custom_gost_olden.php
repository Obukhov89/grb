<? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

//--'Договор в request_new' начало--//
					$select_ba_tz = null;
					if (!empty($_GET['ID_TZ'])) {
						$select_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['ID_TZ'])->Fetch();
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
?>
	<!--'Нумерация материалов в ТЗ' начало-->
    <tr class="tr-gost" id="<?= $_GET['KEY'] ?>">
	<!--'Нумерация материалов в ТЗ' окончание-->
        <td class="tdGost">
            <input class="styleGostNew" type="text" name="gost1[<?= $_GET['KEY'] ?>][]" data-id="<?= $_GET['KEY']?>" value="" placeholder="Напишите новую методику" required>
        </td>
        <td class="method">
            <a class='methodsLink' target='_blank'>
                <div class='methodsLinkPng' title='Методика'></div>
            </a>
        </td>
        <!----------------'При выборе материала, добавление методик' окончание------------------>
        <td class="tdGost">
            <!--'Нумерация материалов в ТЗ' начало-->
            <select name="gost_new1[<?= $_GET['KEY'] ?>][]" style="width:350px" class="gost_d2 conditions styleGost"
                    data-id="<?= $_GET['KEY'] ?>">
            <!--'Нумерация материалов в ТЗ' окончание-->
                <option value="2522" selected>-- | --</option>
                <?php
                $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                while ($gost = $ba_gost->Fetch()) {
                    echo "<option data-type='" . $gost['NORM_TEXT'] . "' value='" . $gost['ID'] . "'>" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                }
                ?>
            </select></td>
        <!----------------'При выборе материала, добавление методик' начало--------------------->
        <td class="price-method"><input class="oborud" type="number" name="price1[]" value=""
                                        required></input></td>
        <!----------------'При выборе материала, добавление методик' окончание------------------>
        <td><input type="checkbox" class="dash"></td>

        <!-- -------------------'При выборе материала, добавление методик' начало-------- -->
        <?php
        echo "<td class='btn-method-add-del' style='min-width: 80px;'>
										<input style='margin-left:5px' type='button' class='add3' value='+'>
										<input style='margin-left:5px' type='button' class='add2' value='+'>
										<input style='margin-left:5px' type='button' class='del' value='-'>
									</td>";

        /*-------------------'При выборе материала, добавление методик' окончание--------*/

        ?>
    </tr>


<? exit ?>
