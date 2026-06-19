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
    <tr class="align-middle tr-gost" id="<?= $_GET['KEY'] ?>">
	<!--'Нумерация материалов в ТЗ' окончание-->
        <td class="tdGost">
            <!------------------'Подключение select2 к методикам' начало----------------->
            <!--<select name="gost[<?/*= $_GET['KEY'] */?>][]" style="width:350px" class="gost_d methods styleGost"
                    data-id="<?/*= $_GET['KEY'] */?>" required>-->
            <select name="gost[<?= $_GET['KEY'] ?>][]" class="gost_d methods styleGost"
                    data-id="<?= $_GET['KEY'] ?>" required>
            <!------------------'Подключение select2 к методикам' окончание----------------->
                <option value="" disabled selected>Выберите</option>
                <?php
                $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE (`NUM_OA_NEW` != '0' OR `IN_OA` = '0') AND `NON_ACTUAL` != 1");
                while ($gost = $ba_gost->Fetch()) {
					//--'Договор в request_new' начало--//
										$gost_id = !empty($gost['ID']) ? $gost['ID'] : ''; //ID ГОСТа

										$gost_price = !empty($gost['PRICE']) ? $gost['PRICE'] : '';

										if (!empty($result_select_dogovor['ID']) && !empty($result_select_dogovor['LONGTERM'])) {

											$price_gost_longterm = null;
											if (!empty($gost_id)) {
												$price_gost_longterm = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT` = " . (int)$result_select_dogovor['ID'] . " AND `ID_GOST` = " . (int)$gost_id)->Fetch();
											}

											$gost_price = isset($price_gost_longterm['PRICE']) ? $price_gost_longterm['PRICE'] : $gost_price;
										}

                    //echo "<option data-price='" . $gost['PRICE'] . "' data-type='" . $gost['NORM_TEXT'] . "' value='" . $gost['ID'] . "'>" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                    echo "<option data-price='" .$gost_price . "' data-type='" . $gost['NORM_TEXT'] . "'  data-new='" . $gost['NUM_OA_NEW'] ."' value='" . $gost_id . "'>" . $gost['GOST'] . '-' . $gost['GOST_YEAR'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                 //--'Договор в request_new' окончание--//
				}
                ?>
            </select>
            <!----------------'При выборе материала, добавление методик' начало--------------------->
            <!--<a class='methodsLink'  target='_blank'><div class='methodsLinkPng' title='Методика'></div></a>-->
        </td>
        <td class="text-center method">
            <a class="link-tab methodsLink" target='_blank'>
                <svg class="icon" width="35" height="35">
                    <use xlink:href="/ulab/assets/images/icons.svg#tab"/>
                </svg>
            </a>
        </td>
        <!----------------'При выборе материала, добавление методик' окончание------------------>
        <td class="tdGost">
            <!--'Нумерация материалов в ТЗ' начало-->
            <!--<select name="gost_new[<?/*= $_GET['KEY'] */?>][]" style="width:350px" class="gost_d2 conditions styleGost"
                    data-id="<?/*= $_GET['KEY'] */?>">-->
            <select name="gost_new[<?= $_GET['KEY'] ?>][]" class="gost_d2 conditions styleGost"
                    data-id="<?= $_GET['KEY'] ?>">
            <!--'Нумерация материалов в ТЗ' окончание-->
                <option value="2522" selected>-- | --</option>
                <?php
                $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                while ($gost = $ba_gost->Fetch()) {
                    echo "<option data-type='" . $gost['NORM_TEXT'] . "' value='" . $gost['ID'] . "'>" . $gost['GOST'] . '-' . $gost['GOST_YEAR'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                }
                ?>
            </select></td>
        <td class="price-method">
            <input type="text" class="form-control number-only price oborud" name="price[<?= $_GET['KEY'] ?>][]"
                   value="" required>
        </td>

        <td>
            <input type="checkbox" class="form-check-input mt-0 dash">
        </td>
        <!--<td></td>-->

        <td>
            <button class="btn btn-success mt-0 bg-dark-green add3" type="button" <?= $attrDisabled ?>>
                <svg class="icon" width="15" height="15">
                    <use xlink:href="/ulab/assets/images/icons.svg#add"/>
                </svg>
            </button>
        </td>
        <td>
            <button class="btn btn-primary mt-0 add2" type="button" <?= $attrDisabled ?>>
                <svg class="icon" width="15" height="15">
                    <use xlink:href="/ulab/assets/images/icons.svg#add"/>
                </svg>
            </button>
        </td>
        <td class='btn-method-add-del'>
            <button class="btn btn-danger mt-0 del" type="button" <?= $attrDisabled ?>>
                <svg class="icon" width="15" height="15">
                    <use xlink:href="/ulab/assets/images/icons.svg#del"/>
                </svg>
            </button>
        </td>
    </tr>


<? exit ?>
