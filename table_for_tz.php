<? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

?>
    <div class='wrapperMaterial w-100 bg-transparent'>
		<!--<br/><br/>-->
        <div id="tz">
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

            <div class="row mb-4 align-items-center">
                <div class="col-auto">
                    <span class="material-title">Материалы</span>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary btn-modal-material" id="new_material">
                        <svg class="icon" width="15" height="15">
                            <use xlink:href="/ulab/assets/images/icons.svg#add"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="row g-3 align-items-center material-data">
                <div class="col-auto">
                    <span class="number-material"><?= $number_material ?></span>
                </div>
                <div class="col-auto col-material-name bg-light p-2">
                    <span class="material-name text-primary text-decoration-underline">Материал не выбран</span>
                </div>
                <div class="col-auto">|</div>
                <div class="col-auto">
                    <label for="inputMaterialCount" class="col-form-label">образцов</label>
                </div>
                <div class="col-auto me-auto">
                    <span id="inputMaterialCount" class="form-control border-0 bg-light">1</span>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary delMaterial" name="delMaterial">Удалить материал</button>
                </div>
            </div>

            <div class="modal-material mb-4">
                <!--<h2 class="title-material">Новый материал</h2>-->
                <div class="row">
                    <div class="form-group col-sm-6">
                        <lable for="newMater">Материал</lable>
                        <input class="form-control tz" id="newMater" type='text' name='mater[<?= $_GET['KEY'] ?>]'
                               list="materials" value="" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-6 amount">
                        <lable for="newObiem">Количество проб/образцов</lable>
                        <input class="form-control number-only material-count mw-100 tz" id="newObiem" type='text'
                               name='obiem[<?= $_GET['KEY'] ?>]' value="1" required>
                    </div>
                </div>

                <div class="row row-gost tr-select-gost">
                    <div class="form-group col-sm-6">
                        <lable for="gostToMaterial">Гост</lable>
                        <div class="row align-items-center wrapper-items-gost">
                            <div class="col">
                                <select class="form-select gost-select" id="gostToMaterial"
                                        name="gost-select[<?= !empty($_GET['KEY']) ? $_GET['KEY'] : ''; ?>][]"
                                        data-key="<?= !empty($_GET['KEY']) ? $_GET['KEY'] : ''; ?>">
                                </select>
                                <input type="hidden" class="hidden-id-gost" value=''>
                            </div>
                            <!--<div class='wrapperBtnGost'>-->
                                <div class="col-auto">
                                    <button type="button" class="btn btn-primary add-gost">
                                        <svg class="icon" width="15" height="15">
                                            <use xlink:href="/ulab/assets/images/icons.svg#add"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="col-auto col-del-gost">
                                    <button type="button" class="btn shadow-none">
                                        <svg class="icon" width="15" height="15">
                                            <use xlink:href="#"></use>
                                        </svg>
                                    </button>
                                </div>
                            <!--</div>-->
                        </div>
                    </div>
                </div>

            </div>
            <!--./<div class="modal-material mb-4">-->

        </div>
        <!--<div id="tz">-->

        <br/>
        <table id="tz" class="table table-gosts mb-5">
            <thead>
            <tr class="table-secondary align-middle">
                <td scope="col" class="col-methods">Методики испытаний</td>
                <td></td>
                <td scope="col" class="col-conditions">Тех. условия</td>
                <td scope="col" class="col-price">Цена</td>
                <td scope="col" class="col-no-price">Без цены</td>
                <!--<th scope="col" class="col-check-all">
                    <input type="checkbox" class="form-check-input gost-check-all" name="gost_check_all">
                </th>-->
                <td scope="col" class="col-btn-methods">
                    <button type="button" class="btn btn-danger gost-del-all m-1 delMetodic" type="button" name="delMetodic">
                        <svg class="icon" width="15" height="15">
                            <use xlink:href="/ulab/assets/images/icons.svg#del"/>
                        </svg>
                    </button>
                </td>
                <td class="col-btn-methods"></td>
                <td class="col-btn-methods"></td>
            </tr>
            </thead>
            <tbody id="tz_body" name="tz_body">
      <!--'Нумерация материалов в ТЗ' начало-->
            <tr class="align-middle tr-gost" id="<?= $_GET['KEY'] ?>" data-gost="0">
            <!--'Нумерация материалов в ТЗ' окончание-->
                 <td class="tdGost">
                    <!------------------'Подключение select2 к методикам' начало----------------->
                    <!--<select name="gost[<?/*= $_GET['KEY'] */?>][]" style="width:350px" class="gost methods styleGost" data-id="<?/*= $_GET['KEY'] */?>"
                            required>-->
                    <select name="gost[<?= $_GET['KEY'] ?>][]" class="gost methods styleGost" data-id="<?= $_GET['KEY'] ?>"
                            required>
                    <!------------------'Подключение select2 к методикам' окончание----------------->
                        <option value="" disabled selected>Выберите</option>
                        <?php
                        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE (`NUM_OA_NEW` != '0' OR `IN_OA` = '0') AND `GOST_TYPE` = 'metodic' AND `GOST_TYPE` = 'metodic_otbor' AND `NON_ACTUAL` != 1");
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
                <td class="text-center method">
                    <a class="link-tab methodsLink" target='_blank'>
                        <svg class="icon" width="35" height="35">
                            <use xlink:href="/ulab/assets/images/icons.svg#tab"/>
                        </svg>
                    </a>
                </td>
                <!-------------------'При выборе материала, добавление методик' окончание-------->
                <td class="tdGost">
                   <!--'Нумерация материалов в ТЗ' начало-->
                    <!--<select name="gost_new[<?/*= $_GET['KEY'] */?>][]" style="width:350px" class="gost2 conditions styleGost" data-id="<?/*= $_GET['KEY'] */?>">-->
                    <select name="gost_new[<?= $_GET['KEY'] ?>][]" class="gost2 conditions styleGost" data-id="<?= $_GET['KEY'] ?>">
                    <!--'Нумерация материалов в ТЗ' окончание-->
                                 <option value="2522" selected>-- | --</option>
                        <?php
                        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1 AND `GOST_TYPE` != 'metodic' AND `GOST_TYPE` != 'metodic_otbor'");
                        while ($gost = $ba_gost->Fetch()) {
                            echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "'>" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                        }
                        ?>
                    </select></td>
                <td>
                    <input type="text" class="form-control number-only price oborud" name="price[<?= $_GET['KEY'] ?>][]"
                           value="" required>
                </td>
                <td>
                    <input type="checkbox" class="form-check-input mt-0 dash">
                </td>
                <!--<td></td>-->
                <td>
                    <button class="btn btn-success mt-0 bg-dark-green add3" type="button">
                        <svg class="icon" width="15" height="15">
                            <use xlink:href="/ulab/assets/images/icons.svg#add"/>
                        </svg>
                    </button>
                </td>
                <td>
                    <button class="btn btn-primary mt-0 add2" type="button">
                        <svg class="icon" width="15" height="15">
                            <use xlink:href="/ulab/assets/images/icons.svg#add"/>
                        </svg>
                    </button>
                </td>
                <td class="btn-method-add-del">
                    <label class='label_del2'>
                        <button class="btn btn-success mt-0 bg-dark-green del2" type="button">
                            <svg class="icon" width="15" height="15">
                                <use xlink:href="/ulab/assets/images/icons.svg#sorting"/>
                            </svg>
                        </button>
                        <div class='helper'>Данная кнопка позволяет добавить на 1-ю позицию возможность внесения новой методики. Внимание, данное действие отменить нельзя!</div>
                    </label>
                </td>

            </tr>
            </tbody>
        </table>
    </div>
<!--./<div class='wrapperMaterial w-100 bg-transparent'>-->

<? exit ?>
