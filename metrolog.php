<?

  require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");

  $APPLICATION->SetTitle("Панель метролога");
  include $_SERVER['DOCUMENT_ROOT'] . '/include/function.php';
  include $_SERVER['DOCUMENT_ROOT'] . '/CDB.php';

  $db = new CDB();

  if(!empty($_GET['laba'])) {
    $laba = $_GET['laba'];
    $res = $db->select('SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage` = {?}', array($laba));
  }else{
    $res = $db->select('SELECT * FROM `ba_oborud`');
  }

  $poverka_time = 5184000;

  // $countEq = count($db->select('SELECT COUNT(*) FROM `ba_oborud`'));
  $countEq = count($res);
  $countDeadCheck = 0;
  $countChechMetr = 0;

  $countConv = $db->select("SELECT COUNT(*) FROM `ba_oborud` WHERE LONG_STORAGE = 1")[0]['COUNT(*)'];

  if(!empty($_GET['laba']) || $_GET['laba'] == 0) {
    $location_laba = "window.location.assign('/metrolog.php?laba='+this.options[this.selectedIndex].value)";
  }else{
    $location_laba = "window.location.assign('/metrolog.php?laba='". $_GET['laba'] .")";
  }
?>

<table style="float:right; width: 100%;">
  <tr>
  <td><a href="../oborud.php?ID=0">Добавить новое оборудование</a>  |  <a href="/protocol_generator/oborud_card2.php?ID=0">Скачать карточки</a></td>

  <td>
    <select class="oborud" name="place_of_installation_or_storage" id="laba" style="float: right; width: 40%; margin-right: 5px;">
        <option value="0" selected>Все лаборатории</option>
        <option value="9999" <?= ($_GET['laba'] == '9999' ? 'selected' : '') ?> >Вне лаборатории</option>
        <?
        $db_laba = new CDB();
        $res_db_laba = $db_laba->select('SELECT * FROM `ba_laba`');

        foreach($res_db_laba as $key => $value) {
          echo('<option ');
          if($_GET['laba'] == $value['ID']) {
            echo('selected');
          }
          echo(" value='". $value['ID'] ."' style='font-weight:bold' >". $value['NAME'] ."</option>");
        }
	echo "<option value='7' style='font-weight:bold' " . ((!empty($_GET['laba']) && (int)$_GET['laba'] === 7) ? 'selected' : '') . ">Отдел СМК и метрологии</option>";
        ?>
      </select>
  </td>


  </tr>

  <tr>
    <td ><a href="/protocol_generator/metr_request.php">Скачать заявку на поверку</a>  |  <a href="/protocol_generator/passport_rc.php?metrolog=1">Скачать формы 2-5</a></td>

    <td><a id="labaDoc" href="" style="float: right;" >Сформировать этикетки по лаборотории</a></td>
  </tr>
</table>

<br>
<br>
<br>
<hr>

<h3>Оборудование с истёкшим сроком проверки</h3>
<table id="oborud1">
  <thead>
    <tr>
      <th></th>
      <th>Наименование</th>
      <th>Тип</th>
      <th>Заводской номер</th>
      <th>Инвертарный номер</th>
      <th>Проверка "ДО"</th>
    </tr>
    <tr>
      <th></th>
      <th><input type="text" name="name_filter" id="name_filter" value=""></th>
      <th><input type="text" name="type_filter" id="type_filter" value=""></th>
      <th><input type="text" name="factory_number_filter" id="factory_number_filter" value=""></th>
      <th></th>
      <th></th>
    </tr>
  </thead>
  <tbody>

  <?
    $color = 'fbb';
    $c = 2;
    $title = 'Истёк срок проверки';
    $sort = 'poverka_alarm';

      foreach($res as $key => $value) {
        if($value['NAME']) {

          $poverka = strtotime($value['POVERKA']) - time();
		  if( ($poverka <= 0) && !$value['NO_METR_CONTROL'] && !is_null($value['NO_METR_CONTROL']) && empty($value['SPISANIE']) && empty($value['LONG_STORAGE']) ) {
          //if( ($poverka <= 0) && !$value['NO_METR_CONTROL'] && !is_null($value['NO_METR_CONTROL'])) {

            $countDeadCheck++;
          ?>
            <tr>
              <td style="background-color: #<?= $color ?>; color: #<?= $color ?>" title="<?= $title ?>"><label><?= $c ?></label></td>
              <td> <a href="/oborud.php?ID=<?= $value['ID']?>"><?= $value['OBJECT'] ?></a> </td>
              <td><?= $value['TYPE_OBORUD'] ?></td>
              <td><?= $value['FACTORY_NUMBER'] ?></td>
              <td><?= $value['REG_NUM'] ?></td>
              <td><?= date("d.m.Y", strtotime($value['POVERKA'])) ?></td>
            </tr>
          <?
          }
        }
      }
    ?>


  </tbody>
</table>
<br>
<br>

<h3>Оборудование у которого истекает срок проверки</h3>
<table id="oborud2">
  <thead>
    <tr>
      <th></th>
      <th>Наименование</th>
      <th>Тип</th>
      <th>Заводской номер</th>
      <th>Инвертарный номер</th>
      <th>Проверка "ДО"</th>
    </tr>
    <tr>
      <th></th>
      <th><input type="text" name="name_filter" id="name_filter2" value=""></th>
      <th><input type="text" name="type_filter" id="type_filter2" value=""></th>
      <th><input type="text" name="factory_number_filter" id="factory_number_filter2" value=""></th>
      <th></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <!-- <div id='buttonRightTable'></div> -->
    <!-- <div id='buttonLeftTable'></div> -->

    <?
    $color = 'ffa';
    $c = 4;
    $title = 'До истечения срока проверки осталось менее 60 дней!';
    $sort = 'poverka';

      foreach($res as $key => $value) {
      	if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
      		if ($value['REG_NUM'] == '№ 20.4-144' || $value['REG_NUM'] == '№ 20.4-148') {
      			pre($value);
			}
		}
        if($value['NAME']) {

          $now = new DateTime();
          $date = new DateTime($value['POVERKA']);
          $diff = $now->diff($date);
          $diff = $diff->format('%R%a');

          // $poverka = strtotime($value['POVERKA']) - time();

          if( ( $diff > 0 && $diff <= 60 ) && $value['NO_METR_CONTROL'] == '0' ) {
          ?>
            <tr>
              <td style="background-color: #<?= $color ?>; color: #<?= $color ?>" title="<?= $title ?>"><label><?= $c ?></label></td>
              <td> <a href="/oborud.php?ID=<?= $value['ID']?>"><?= $value['OBJECT'] ?></a>  </td>
              <td><?= $value['TYPE_OBORUD'] ?></td>
              <td><?= $value['FACTORY_NUMBER'] ?></td>
              <td><?= $value['REG_NUM'] ?></td>
              <td><?= $date->format('d.m.Y') ?></td>
            </tr>
          <?
          }
        }
      }
    ?>

  </tbody>
</table>
<br>
<br>

<h3>Требует проверки отделом метрологии</h3>
<table id="oborud3">
  <thead>
    <tr>
      <th></th>
      <th>Наименование</th>
      <th>Тип</th>
      <th>Заводской номер</th>
      <th>Инвентарный номер</th>
      <th>Проверка "ДО"</th>
    </tr>
    <tr>
      <th></th>
      <th><input type="text" name="name_filter" id="name_filter3" value=""></th>
      <th><input type="text" name="type_filter" id="type_filter3" value=""></th>
      <th><input type="text" name="factory_number_filter" id="factory_number_filter3" value=""></th>
      <th></th>
      <th></th>
    </tr>
  </thead>
  <tbody>

  <?

    $color = '9999cc';
    $c = 3;
    $title = 'Оборудование не проверено отделом метрологии';
    $sort = 'unchecked';

      foreach($res as $key => $value) {
        if($value['NAME']) {

          $poverka = strtotime($value['POVERKA']) - time();
          $diff = $poverka - $today;

          if( ( $poverka > $poverka_time || !is_null($value['NO_METR_CONTROL']) ) && $value['CHECKED'] == '0' && $value['IN_STOCK'] ) {

            $countChechMetr++;
          ?>
            <tr>
              <td style="background-color: #<?= $color ?>; color: #<?= $color ?>" title="<?= $title ?>"><label><?= $c ?></label></td>
              <td> <a href="/oborud.php?ID=<?= $value['ID'] ?>"><?= $value['OBJECT'] ?></a> </td>
              <td><?= $value['TYPE_OBORUD'] ?></td>
              <td><?= $value['FACTORY_NUMBER'] ?></td>
              <td><?= $value['REG_NUM'] ?></td>
              <td><?= date("d.m.Y", strtotime($value['POVERKA'])) ?></td>
            </tr>
          <?
          }
        }
      }
    ?>

  </tbody>
</table>
<br>
<br>

<h3>Статистика по оборудованию</h3>
<table id="oborud">
  <thead>
      <th></th>
      <th>Статус оборудования</th>
      <th>Кол-во единиц</th>
  </thead>
  <tbody>
    <tr>
      <td style="background-color: #00ccff; color: #00ccff"></td>
      <td>Всего единиц оборудования</td>
      <td> <?= $countEq ?> </td>
    </tr>
      <td style="background-color: #FBBBB7; color: #FBBBB7"></td>
      <td>Истёк срок проверки</td>
      <td> <?= $countDeadCheck ?> </td>
    </tr>
    <tr>
      <td style="background-color: #999AC7; color: #999AC7"></td>
      <td>Требует проверки отделом метрологии</td>
      <td> <?= $countChechMetr ?> </td>
    </tr>
    <tr>
      <td style="background-color: #CACBC8; color: #CACBC8"></td>
      <td>На консервации</td>
      <td> <?= $countConv ?> </td>
    </tr>
  </tbody>
</table>

<?
  // ! script начало ==========================
?>

<link rel="stylesheet" type="text/css" href="/DataTables/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
    <!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"/>-->
<link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
<link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>
<script
  src="//code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
<script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" language="javascript"
    src="/jszip/jquery.dataTables.customSortandFilters.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>

<?
  // ! script конец ==========================
?>

<!-- добавление масок и прочего -->
<!-- и dataTable -->
<script>
  $(document).ready(function() {

  // ! FIX jquery warning - passive event handler ==
    jQuery.event.special.touchstart = {
      setup: function( _, ns, handle ) {
        this.addEventListener('touchstart', handle, { passive: !ns.includes('noPreventDefault') });
      }
    };
    jQuery.event.special.touchmove = {
      setup: function( _, ns, handle ) {
          this.addEventListener('touchmove', handle, { passive: !ns.includes('noPreventDefault') });
      }
    };
    // ! ==

    let laba = $('#laba option:selected').val();

    $('#laba').on('change', function() {
      laba = $('option:selected', this).val();
      let str = 'metrolog.php?laba='+laba;

      if(laba == 0){
        window.location.assign('metrolog.php');
      }else{
        window.location.assign(str);
      }

    });

    $('#labaDoc').on('click', function(event) {
      if(laba == 0) {
        alert('Сначала выберите лабораторию');
        event.preventDefault();
        return false;
      }
      $(this).attr('href', "/protocol_generator/oborud_card_all_laba.php?laba="+laba);
    })

    table1 = $('#oborud1').DataTable({
      "sDom": 'Bfrtlip',
      "language": {
        "processing": "Подождите...",
        "search": "Поиск:",
        "lengthMenu": "Показать _MENU_ записей",
        "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
        "infoEmpty": "Записи с 0 до 0 из 0 записей",
        "infoFiltered": "(отфильтровано из _MAX_ записей)",
        "infoPostFix": "",
        "loadingRecords": "Загрузка записей...",
        "zeroRecords": "Записи отсутствуют.",
        "emptyTable": "В таблице отсутствуют данные",
        "paginate": {
            "first": "Первая",
            "previous": "Предыдущая",
            "next": "Следующая",
            "last": "Последняя"
        },
        "buttons": {
          "copy": "",
          'colvis': "",
          'print': "",
          "excel": ""
        },
        "aria": {
          "sortAscending": ": активировать для сортировки столбца по возрастанию",
          "sortDescending": ": активировать для сортировки столбца по убыванию"
        }
      },
        "iDisplayLength": 10,
        "order": [[5, "desc"]],
        "colReorder": true,
        "bSortCellsTop": true,
        "dom": 'Blfrtip',
        // "sScrollX": '100%',
        //"scrollY": 500,
        // "scrollX": true,
        "bAutoWidth": true,
        "buttons": ['copy', 'excel', 'colvis', 'print'],
        // "aoColumns": [{"sType": "date"}]
        // "columnDefs": [
        //   {
        //     "targets": 1,
        //     "render": $.fn.dataTable.render.ellipsis(50, true)
        //   },
          // {"sType": "ruDate", "targets": 1},
          // {"sType": "ruDate", "targets": 5},
        // ]
    });

    table2 = $('#oborud2').DataTable({
      "sDom": 'Bfrtlip',
      "language": {
        "processing": "Подождите...",
        "search": "Поиск:",
        "lengthMenu": "Показать _MENU_ записей",
        "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
        "infoEmpty": "Записи с 0 до 0 из 0 записей",
        "infoFiltered": "(отфильтровано из _MAX_ записей)",
        "infoPostFix": "",
        "loadingRecords": "Загрузка записей...",
        "zeroRecords": "Записи отсутствуют.",
        "emptyTable": "В таблице отсутствуют данные",
        "paginate": {
            "first": "Первая",
            "previous": "Предыдущая",
            "next": "Следующая",
            "last": "Последняя"
        },
        "buttons": {
          "copy": "",
          'colvis': "",
          'print': "",
          "excel": ""
        },
        "aria": {
          "sortAscending": ": активировать для сортировки столбца по возрастанию",
          "sortDescending": ": активировать для сортировки столбца по убыванию"
        }
      },
        "iDisplayLength": 10,
        "order": [[5, "desc"]],
        "colReorder": true,
        "bSortCellsTop": true,
        "dom": 'Blfrtip',
        // "sScrollX": '100%',
        //"scrollY": 500,
        // "scrollX": true,
        "bAutoWidth": true,
        "buttons": ['copy', 'excel', 'colvis', 'print'],
        // "columnDefs": [
        //   {
        //     "targets": 1,
        //     "render": $.fn.dataTable.render.ellipsis(50, true)
        //   },
          // {"sType": "ruDate", "targets": 1},
          // {"sType": "ruDate", "targets": 5},
        // ]
    });

    table3 = $('#oborud3').DataTable({
      "sDom": 'Bfrtlip',
      "language": {
        "processing": "Подождите...",
        "search": "Поиск:",
        "lengthMenu": "Показать _MENU_ записей",
        "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
        "infoEmpty": "Записи с 0 до 0 из 0 записей",
        "infoFiltered": "(отфильтровано из _MAX_ записей)",
        "infoPostFix": "",
        "loadingRecords": "Загрузка записей...",
        "zeroRecords": "Записи отсутствуют.",
        "emptyTable": "В таблице отсутствуют данные",
        "paginate": {
            "first": "Первая",
            "previous": "Предыдущая",
            "next": "Следующая",
            "last": "Последняя"
        },
        "buttons": {
          "copy": "",
          'colvis': "",
          'print': "",
          "excel": ""
        },
        "aria": {
          "sortAscending": ": активировать для сортировки столбца по возрастанию",
          "sortDescending": ": активировать для сортировки столбца по убыванию"
        }
      },
        "iDisplayLength": 10,
        "order": [[5, "desc"]],
        "colReorder": true,
        "bSortCellsTop": true,
        "dom": 'Blfrtip',
        // "sScrollX": '100%',
        //"scrollY": 500,
        // "scrollX": true,
        "bAutoWidth": true,
        "buttons": ['copy', 'excel', 'colvis', 'print'],
        // "columnDefs": [
        //   {
        //     "targets": 1,
        //     "render": $.fn.dataTable.render.ellipsis(50, true)
        //   },
          // {"sType": "ruDate", "targets": 1},
          // {"sType": "ruDate", "targets": 5},
        // ]
    });
    // в таблицу со сроком до 60 дней сортировка по asc
    let oborud = $('#oborud2').dataTable().fnSort([ [5, "asc"] ]);

    $('#name_filter').on('keyup', function () {
        table1
            .columns(1)
            .search(this.value)
            .draw();
    });
    $('#name_filter2').on('keyup', function () {
        table2
            .columns(1)
            .search(this.value)
            .draw();
    });
    $('#name_filter3').on('keyup', function () {
        table3
            .columns(1)
            .search(this.value)
            .draw();
    });

    $('#type_filter').on('keyup', function () {
        table1
            .columns(2)
            .search(this.value)
            .draw();
    });
    $('#type_filter2').on('keyup', function () {
        table2
            .columns(2)
            .search(this.value)
            .draw();
    });
    $('#type_filter3').on('keyup', function () {
        table3
            .columns(2)
            .search(this.value)
            .draw();
    });

    $('#factory_number_filter').on('keyup', function () {
        table1
            .columns(3)
            .search(this.value)
            .draw();
    });

    $('#factory_number_filter2').on('keyup', function () {
        table2
            .columns(3)
            .search(this.value)
            .draw();
    });

    $('#factory_number_filter3').on('keyup', function () {
        table3
            .columns(3)
            .search(this.value)
            .draw();
    });

    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
      "ruDate-asc": function (a, b) {

          var ruDatea = $.trim(a).split('.');
          var ruDateb = $.trim(b).split('.');

          if (ruDatea[2] * 1 < ruDateb[2] * 1)
              return 1;
          if (ruDatea[2] * 1 > ruDateb[2] * 1)
              return -1;
          if (ruDatea[2] * 1 == ruDateb[2] * 1) {
              if (ruDatea[1] * 1 < ruDateb[1] * 1)
                  return 1;
              if (ruDatea[1] * 1 > ruDateb[1] * 1)
                  return -1;
              if (ruDatea[1] * 1 == ruDateb[1] * 1) {
                  if (ruDatea[0] * 1 < ruDateb[0] * 1)
                      return 1;
                  if (ruDatea[0] * 1 > ruDateb[0] * 1)
                      return -1;
              } else
                  return 0;
          }
      },

      "ruDate-desc": function (a, b) {
        var ruDatea = $.trim(a).split('.');
        var ruDateb = $.trim(b).split('.');

        if (ruDatea[2] * 1 < ruDateb[2] * 1)
            return -1;
        if (ruDatea[2] * 1 > ruDateb[2] * 1)
            return 1;
        if (ruDatea[2] * 1 == ruDateb[2] * 1) {
            if (ruDatea[1] * 1 < ruDateb[1] * 1)
                return -1;
            if (ruDatea[1] * 1 > ruDateb[1] * 1)
                return 1;
            if (ruDatea[1] * 1 == ruDateb[1] * 1) {
                if (ruDatea[0] * 1 < ruDateb[0] * 1)
                    return -1;
                if (ruDatea[0] * 1 > ruDateb[0] * 1)
                    return 1;
            } else
                return 0;
        }
      }
    });
  })
</script>

<script>
  $(document).ready(function() {
    let container = $('.workarea-content-paddings'),
        scroll = $('.dataTable.no-footer').width(),
        topTable = $('.workarea-content-paddings').offset().top,
        topTableBody = $('.dataTable.no-footer > tbody').offset().top,
        sumHeaderHeight = topTableBody - topTable;

    $('#buttonRightTable').css('top', sumHeaderHeight + 'px');
    $('#buttonLeftTable').css('top', sumHeaderHeight + 'px');

  });
</script>

<?php
  require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>
