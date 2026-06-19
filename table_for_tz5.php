<? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

?>
    <div class='wrapperMaterial'>
		<br/><br/>
        <table id="tz">
            <tr>
                <!----------------'При выборе материала, добавление методик' начало--------------------->
                <?php
                $number_material = (!empty($_GET['NUM'])) ? ((int)$_GET['NUM'] + 1) : $_GET['KEY'];
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
            <!-------------------'При выборе материала, добавление методик' начало-------->
            <tr id="<?= $_GET['KEY'] ?>" data-gost="0">
            <!-------------------'При выборе материала, добавление методик' окончание-------->
                <td>
                    <select name="gost[<?= $_GET['KEY'] ?>][]" style="width:350px" class="gost methods" data-id="<?= $_GET['KEY'] ?>"
                            required>
                        <option value="" disabled selected>Выберите</option>
                        <?php
                        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                        while ($gost = $ba_gost->Fetch()) {
                            echo "<option data-price='" . $gost['PRICE'] . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "' >" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
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
                <td>
                    <select name="gost_new[<?= $_GET['KEY'] ?>][]" style="width:350px" class="gost2" data-id="<?= $_GET['KEY'] ?>">
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
                echo "<td><input style='margin-left:5px' type='button' class='add2' value='+'></td>";
                ?>
            </tr>
            </tbody>
        </table>
    </div>

<? exit ?>