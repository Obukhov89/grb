<?php
include_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle('Журнал карьеров');

$stage = $_GET['stage'] ?? '';
?>
    <table style="float: right;">
        <tr>
            <td>
                <label class="icon_date">
                    <input class="periodYear" id="period1" type="text" title="Выберите начало отрезка времени" value="<?= ((!empty($_GET['dateStart'])) ? date('d.m.Y', strtotime($_GET['dateStart'])) : '01.12.2020' )?>" required >
                </label>
            </td>

            <td>
                <label class="icon_date">
                    <input class="periodYear" id="period2" type="text" title="Выберите конец отрезка времени" value="<?= ((!empty($_GET['dateOut'])) ? date('d.m.Y', strtotime($_GET['dateOut'])) : date('t.m.Y') )?>" required >
                </label>
            </td>

            <td>
                <select class="stage-selection" name="stage" id="stage"  style="float: left; width: 100%">
                    <option value='all' style='font-weight:bold' <?= $stage === 'all' ? 'selected' : ''?> selected>Все стадии</option>
                    <option value="1" style='font-weight:bold' <?= $stage === '1' ? 'selected' : ''?>>Пробы не поступили</option>
                    <option value='2' style='font-weight:bold' <?= $stage === '2' ? 'selected' : ''?>>Пробы поступили</option>
                    <option value='3' style='font-weight:bold' <?= $stage === '3' ? 'selected' : ''?>>Проводятся испытания</option>
                    <option value='4' style='font-weight:bold' <?= $stage === '4' ? 'selected' : ''?>>Испытания завершены</option>
                    <option value='5' style='font-weight:bold' <?= $stage === '5' ? 'selected' : ''?>>Заявка неуспешна</option>
                    <option value='6' style='font-weight:bold' <?= $stage === '6' ? 'selected' : ''?>>Заявка не оплачена</option>
                    <option value='7' style='font-weight:bold' <?= $stage === '7' ? 'selected' : ''?>>Заявка оплачена не полностью</option>
                    <option value='8' style='font-weight:bold' <?= $stage === '8' ? 'selected' : ''?>>По заявке переплата</option>
                    <option value='9' style='font-weight:bold' <?= $stage === '9' ? 'selected' : ''?>>Заявка оплачена полностью</option>
                    <option value='10' style='font-weight:bold' <?= $stage === '10' ? 'selected' : ''?>>Все кроме новых и неуспешных</option>
                    <option value='11' style='font-weight:bold' <?= $stage === '11' ? 'selected' : ''?>>Успешно завершенные</option>
                </select>
            </td>
            <td>
                <select class="oborud" name="laba" id="laba" onchange="<?= $location_laba?>" style="float: left; width: 100%">
                    <option value="all" selected>Все лаборатории</option>

                    <?php
                    $select = $DB->Query("SELECT * FROM `ba_laba`");
                    while ($option = $select->Fetch()) {

                        echo "<option ";
                        if ($_GET['laba'] == $option['ID']) {
                            echo "selected";
                        }
                        echo " value='" . $option['ID'] . "' style='font-weight:bold'>" . $option['NAME'] . "</option>";

                    }
                    ?>
                </select>
            </td>

            <td><input type="button" class="add2" value="Х" onclick="window.location.assign('/Quarry/view/career_log.php')"></input></td>
            <td><input type="checkbox" name="manufacture" id="manufacture"> Производитель</td>
        </tr>
    </table>

<table class="table-all" id="table-carrer">
    <thead>
        <tr>
            <th></th>
            <th>Заявка</th>
            <th>Дата</th>
            <th>Клиент</th>
            <th>Крайний срок</th>
            <th>Объект испытаний</th>
            <th>Ответственный</th>
            <th>Карьер</th>
            <th>Акт ПП</th>
            <th>ТЗ</th>
            <th>Договор</th>
            <th>Стоимость</th>
            <th>Протокол</th>
        </tr>
        <tr>
            <th></th>
            <th>
                <input class="request" type="text" placeholder="Поиск Заявки">
            </th>
            <th>
                <input class="date" type="text" placeholder="Поиск Даты">
            </th>
            <th>
                <input class="client" type="text" placeholder="Поиск Клиента">
            </th>
            <th>
                <input class="deadline" type="text" placeholder="Поиск Крайний срок">
            </th>
            <th>
                <input class="object" type="text" placeholder="Поиск Объект испытанийы">
            </th>
            <th>
                <input class="responsible" type="text" placeholder="Поиск Ответственного">
            </th>
            <th>
                <input class="quarry" type="text" placeholder="Поиск Карьера">
            </th>
            <th>
                <input class="act" type="text" placeholder="Поиск Акт ПП">
            </th>
            <th>
                <input class="tz_id" type="text" placeholder="Поиск ТЗ">
            </th>
            <th>
                <input class="contract" type="text" placeholder="Поиск Договора">
            </th>
            <th>
                <input class="cost" type="text" placeholder="Поиск Стоимость">
            </th>
            <th>
                <input class="protocol" type="text" placeholder="Поиск Пролокола">
            </th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
<!--<script src="./js/jquery.js"></script>-->

<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script>
    /*$(".add").click(function () {
        td = $(this).parent().parent();
        keys = $(this).data("keys").split("-");
        td.append('<div style="display:flex"><input class="res" "margin-left:5px" type="number" step="0.001" name="results[' + keys[0] + '][' + keys[1] + '][' + keys[2] + '][]"  ><input style="margin-left:5px" type="button" value="- " onclick="$(this).parent().remove();"></div>');
    });*/
</script>
<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></scrip>
<script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
<script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/filtering/type-based/html.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.21/dataRender/intl.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="/vendor/jquery_localization-ui/localization.js"></script>

<script src="./js/careerLog.js"></script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>