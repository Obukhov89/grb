<? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

?>
    <div class='wrapperMaterial'>
		<br/><br/>
        <table id="tz">
			<!--'Нумерация материалов в ТЗ' начало-->
            <tr class="tr-material">
			<!--'Нумерация материалов в ТЗ' окончание-->
                <!----------------'При выборе материала, добавление методик' начало--------------------->
                <?php
                $number_material = (!empty($_GET['NUM'])) ? ((int)$_GET['NUM'] + 1) : $_GET['KEY'];

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
                <td class="number-material"><?= $number_material ?></td>
                <!----------------'При выборе материала, добавление методик' окончание------------------>
                <td style="font-weight:bold">Материал</td>
                <td><input class="tz" type='text' name='mater[<?= $_GET['KEY'] ?>]' list="materials" required value=""></td>
                <td style="font-weight:bold">Кол-во проб/образцов</td>
                <!----------------'При выборе материала, добавление методик' начало--------------------->
                <td class="amount">
                    <input class="tz" type='number' name='obiem[<?= $_GET['KEY'] ?>]' min='1' value="1" required value="">
                </td>
                <td class="btn-add-del">
                    <input type="button" value="+" id="new_material">
                    <input type="button" value="-" name="delMaterial" class='delMaterial'>
                </td>
                <!----------------'Удаление методики' начало------------------->
                <td>
                    <!--'Кнопка удаление методики'-->
                    <input type="button" value="Удалить методики" name="delMetodic" class='delMetodic'>
                </td>
                <!----------------'Удаление методики' окончание------------------->
            </tr>
            <tr class="tr-select-gost">
                <td></td>
                <td class="abbreviation-gost">гост</td>
                <td>
                    <select class="gost-select" name="gost-select[<?= !empty($_GET['KEY']) ? $_GET['KEY'] : ''; ?>][]" data-key="<?= !empty($_GET['KEY']) ? $_GET['KEY'] : ''; ?>">
                    </select>
                    <input type="hidden" class="hidden-id-gost" value=''>
                </td>
                <td class='wrapperBtnGost'>
                    <input type='button' class='add-gost' value='+'>
                </td>
            </tr>
            <!-------------------'При выборе материала, добавление методик' окончание-------->
        </table>
        <br/>
        <table id="tz">
            <thead>
            <th>Методика испытаний</th>
            <!--------------'При выборе материала, добавление методик' начало----------->
            <th></th>
            <!--------------'При выборе материала, добавление методик' окончание--------->
            <th>Тех.условия</th>
            <th>Цена</th>
            <th>Без цены</th>
            <th></th>
            </thead>
            <tbody id="tz_body" name="tz_body">
      <!--'Нумерация материалов в ТЗ' начало-->
            <tr class="tr-gost" id="<?= $_GET['KEY'] ?>" data-gost="0">
            <!--'Нумерация материалов в ТЗ' окончание-->
                 <td class="tdGost">
                    <!------------------'Подключение select2 к методикам' начало----------------->
                    <select name="gost[<?= $_GET['KEY'] ?>][]" style="width:350px" class="gost methods styleGost" data-id="<?= $_GET['KEY'] ?>"
                            required>
                    <!------------------'Подключение select2 к методикам' окончание----------------->
                        <option value="" disabled selected>Выберите</option>
                        <?php
                        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
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

							//echo "<option data-price='" . $gost['PRICE'] . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "' >" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
							echo "<option data-price='" . $gost_price . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost_id . "' >" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                         //--'Договор в request_new' окончание--//
                        }
                        ?>
                    </select>
                    <!-------------------'При выборе материала, добавление методик' начало-------->
					<!--<a class='methodsLink'  target='_blank'><div class='methodsLinkPng' title='Методика'></div></a>-->
                </td>
                <td class="method">
                    <a class='methodsLink' target='_blank'>
                        <div class='methodsLinkPng' title='Методика'></div>
                    </a>
                </td>
                <!-------------------'При выборе материала, добавление методик' окончание-------->
                <td class="tdGost">
                   <!--'Нумерация материалов в ТЗ' начало-->
                    <select name="gost_new[<?= $_GET['KEY'] ?>][]" style="width:350px" class="gost2 conditions styleGost" data-id="<?= $_GET['KEY'] ?>">
                    <!--'Нумерация материалов в ТЗ' окончание-->
                                 <option value="2522" selected>-- | --</option>
                        <?php
                        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                        while ($gost = $ba_gost->Fetch()) {
                            echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "'>" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                        }
                        ?>
                    </select></td>
                <td><input name="price[<?= $_GET['KEY'] ?>][]" class="oborud" type="number" value="" required></input></td>
                <td><input type="checkbox" class="dash"></td>
                <?php
                echo "<td class='btn-method-add-del'>
                <input style='margin-left:5px' type='button' class='add3' value='+'>
                <input style='margin-left:5px' type='button' class='add2' value='+'>
                <label class='label_del2'><input style='margin-left:5px' type='button' class='del2' value='<  >' > <div class='helper'>Данная кнопка позволяет добавить на 1-ю позицию возможность внесения новой методики. Внимание, данное действие отменить нельзя!</div> </label>
                </td>";
                ?>
            </tr>
            </tbody>
        </table>
    </div>

<? exit ?>
