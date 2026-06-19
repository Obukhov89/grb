<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Интерфейс метролога");
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php"; 

$res = $DB->Query("SELECT * FROM `ba_oborud`");
?>

  <table id='oborud'>
    <thead>
    <tr>
        <th></th>
        <th>Измеряемая харак-ка</th>
        <th>Наименование</th>
        <th>Тип</th>
        <th>Идент.</th>
        <th>Заводской номер</th>
        <th>Инв. номер</th>
        <th>Ввод в экспл.</th>
        <th>Диапазон измерения</th>
        <th>Класс точностити</th>
        <th>Свид-во о поверке</th>
        <th>Право собственности</th>
        <th>Место установки</th>
        <th>Поверка</th>
        <th>В области акк.</th>
        <th>В наличии</th>
        <th>Проверено</th>
        <th>Производитель</th>
        <th>Примечание</th>
    </tr>
    <tr>
        <th></th>
        <th><input type="text" name="characteristic_filter" id="characteristic_filter" value=""></th>
        <th><input type="text" name="name_filter" id="name_filter" value=""></th>
        <th><input type="text" name="type_filter" id="type_filter" value=""></th>
        <th><input type="text" name="os_filter" id="os_filter" style="width:50px" value=""></th>
        <th><input type="text" name="factory_number_filter" id="factory_number_filter" value=""></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    </thead>

    <tbody>
    <div id='buttonRightTable'></div>
    <div id='buttonLeftTable'></div>
    <?php
    $poverka_time = 5184000;
    $c = 1;
    $title = '';
    $sort = '';
    while ($row = $res->Fetch()) {
        $poverka = strtotime($row['POVERKA']) - time();
        if ($poverka < $poverka_time) {
            
			$color = 'ffa';
			$c = 4;
			$title = 'До истечения срока поверки осталось менее 60 дней!';
			$sort = 'poverka';

            //echo $row['IN_STOCK'];
            ?>
            <tr>
                <td style="background-color: #<?= $color ?>; color: #<?= $color ?>" title="<?= $title ?>">
                    <label><?= $c ?></label></td>
                <td><?= $row['NAME'] ?></td>
                <td><a href='/oborud.php?ID=<?= $row['ID'] ?>'><?= $row['OBJECT'] ?></a></td>
                <td><?= $row['TYPE_OBORUD'] ?></td>
                <td><?php $ident = "";
                    switch ($row['IDENT']) {
                        case 'SI':
                            $ident = "СИ";
                            break;
                        case 'IO':
                            $ident = "ИО";
                            break;
                        case 'VO':
                            $ident = "ВО";
                            break;
                        case 'TS':
                            $ident = "ТС";
                            break;
                        case 'SO':
                            $ident = "ТС";
                            break;
                        case 'REACT':
                            $ident = "Реактивы";
                            break;
                        case 'OOPP':
                            $ident = "ООПП";
                            break;
                    }
                    echo $ident;
                    ?></td>
                <td><?= $row['FACTORY_NUMBER'] ?></td>
                <td><?= $row['REG_NUM'] ?></td>
                <td><?= date("d.m.Y", strtotime($row['god_vvoda_expluatation'])) ?></td>
                <td><?= $row['measuring_range'] ?></td>
                <td><?= $row['сlass_precision_and_accuracy'] ?></td>
                <td><?= $row['certificate_of_verification'] ?></td>
                <td><?= $row['property_rights'] ?></td>
                <td>
                    <?php
                    $result = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID`=" . $row['place_of_installation_or_storage'])->Fetch();
                    echo $result['NAME'];
                    ?>
                </td>
                <td><?= date("d.m.Y", strtotime($row['POVERKA'])) ?></td>
                <td><?= ($row['IN_AREA']) ? 'В области' : 'Не в области' ?></td>
                <td><?= ($row['IN_STOCK']) ? 'В наличии' : 'Нет в наличии' ?></td>
                <td><?= ($row['CHECKED']) ? 'Да' : 'Нет' ?></td>
                <td><?= $row['manufacturer'] ?></td>
                <td><?= $row['note'] ?></td>
            </tr>
            <?php
        }
    }
    echo "</tbody></table>";

}

?>
    <table>
        <tr>
            <td> План-график
            </td>
            <td><select id="type_sel">
                    <option value="1">поверки СИ</option>
                    <option value="2">калибровки СИ</option>
                    <option value="3">аттестации ИО</option>
                    <option value="4">проведения измерений ТС</option>
                </select></td>
            <td>
                на
            </td>
            <td><select id="year_sel">
                    <option>2022</option>
                    <option>2021</option>
                    <option>2020</option>
                    <option>2019</option>
                </select></td>
            <td> год</td>
            <td><input type="button" value="Скачать"
                       onclick="window.open ('/protocol_generator/plan_graphic.php?YEAR=' + document.getElementById ('year_sel').value +'&TYPE='+document.getElementById ('type_sel').value, '_blank')">
            </td>
    </table>
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

    <!--<script type="text/javascript" src="DataTables/datatables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/dataTables.bootstrap4.min.css"/>
    <link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/buttons/1.3.1/css/buttons.bootstrap4.min.css"/>
    <script type="text/javascript" src="http://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="http://cdn.datatables.net/buttons/1.3.1/js/buttons.bootstrap4.min.js"></script>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="http://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
    <script type="text/javascript" src="http://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
    <script type="text/javascript" src="http://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>-->
    <script>
        $(document).ready(function () {
            table = $('#oborud').DataTable({
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
                "iDisplayLength": 50,
                "order": [[2, "asc"]],
                "colReorder": true,
                "bSortCellsTop": true,
                "dom": 'Blfrtip',
                // "sScrollX": '100%',
                //"scrollY": 500,
                //"scrollX": true,
                "buttons": ['copy', 'excel', 'colvis', 'print'],
                "columnDefs": [
                    {
                        "targets": 1,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 2,
                        "render": $.fn.dataTable.render.ellipsis(60, true)
                    },
                    {
                        "targets": 5,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 8,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {"sType": "ruDate", "targets": 7},
                    {
                        "targets": 9,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 10,
                        "render": $.fn.dataTable.render.ellipsis(40, true)
                    },
                    {
                        "targets": 11,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 12,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {"sType": "ruDate", "targets": 13}
                ]

            });

            $('#characteristic_filter').on('keyup', function () {
                table
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#name_filter').on('keyup', function () {
                table
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#type_filter').on('keyup', function () {
                table
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#os_filter').on('keyup', function () {
                table
                    .columns(4)
                    .search(this.value)
                    .draw();
            });

            $('#factory_number_filter').on('keyup', function () {
                table
                    .columns(5)
                    .search(this.value)
                    .draw();
            });
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


    </script>
    <script>
        window.onload = function () {

            var status = document.getElementById("status").value;

            if (status != "1" && status != "8" && status != "9" && status != "10" && status != "11" && status != "12" && status != "13" && status != "14" && status != "15" && status != "16" && status != "17" && status != "21" && status != "22" && status != "24" && status != "32" && status != "34" && status != "43" && status != "58") {
                var input = document.querySelectorAll("input");
                var select = document.querySelectorAll("select");
                var textarea = document.querySelectorAll("textarea");
                for (var i = 0, cnt = input.length; i < cnt; i++) {
                    input[i].disabled = true;
                }
                for (var i = 0, cnt = select.length; i < cnt; i++) {
                    select[i].disabled = true;
                }
                for (var i = 0, cnt = textarea.length; i < cnt; i++) {
                    textarea[i].disabled = true;
                }

            }

        };


    </script>

    <script>
        $(document).ready(function () {
            $("#oborud_table").on("click", ".del", function () {
				var tr = $(this).closest('tr');
				var wrapperDel = tr.find('td:nth-child(3) > div.wrapperDel');
				var trHidden = tr.next('.trHidden');
				var pathFileDel = wrapperDel.find('a').attr('href');
				trHidden.find('.hiddenDel').val(pathFileDel);
				
                $(this).closest('tr').remove();
            });

            $("#oborud_table").on("click", ".add2", function () {
                //tr = $(this).closest('tr');
                var trLast = $('tr.trCertificate:last');

                $.ajax({
                    url: 'tr_for_oborud.php',
                    success: function (data) {
                        //tr.after(data);
                        trLast.after(data);
                    }
                });
            });
        });
    </script>

    <script>
        //Прокрутка таблицы с помощью кнопок
        $(document).ready(function () {

            var container = $('.workarea-content-paddings'),
                scroll = $('.dataTable.no-footer').width(),
                topTable = $('.workarea-content-paddings').offset().top,
                topTableBody = $('.dataTable.no-footer > tbody').offset().top,
                sumHeaderHeight = topTableBody - topTable;

            $('#buttonRightTable').css('top', sumHeaderHeight + "px");
            $('#buttonLeftTable').css('top', sumHeaderHeight + "px");

            $('#buttonRightTable').hover(function () {
                container.animate({
                    'scrollLeft': scroll
                }, {duration: 4000, queue: false});
            }, function () {
                container.stop();
            });
            $('#buttonLeftTable').hover(function () {
                container.animate({
                    'scrollLeft': -scroll
                }, {duration: 4000, queue: false});
            }, function () {
                container.stop();
            });

            $(document).scroll(function () {
                var positionScroll = $(window).scrollTop();
                if (positionScroll > 250 && positionScroll < 2550) {
                    $('#buttonRightTable').css('transform', 'translateY(' + (positionScroll - 250) + 'px' + ')');
                    $('#buttonLeftTable').css('transform', 'translateY(' + (positionScroll - 250) + 'px' + ')');
                }
            });

            if ($(window).outerWidth(true) < 1083 && $(window).outerWidth(true) > 410) {
                $('#buttonRightTable').css('right', (1083 - $(window).outerWidth(true)) + "px");
            } else {
                $('#buttonRightTable').css('right', 0 + "px");
            }

            $(window).resize(function () {
                if ($(window).outerWidth(true) < 1083 && $(window).outerWidth(true) > 410) {
                    $('#buttonRightTable').css('right', (1083 - $(window).outerWidth(true)) + "px");
                } else {
                    $('#buttonRightTable').css('right', 0 + "px");
                }
            });

        });
    </script>
    <script>
      $(document).ready(function () {
            $("#oborud_table").on("click", ".delFile", function () {
                $(this).closest('.wrapperDel').css('display', 'none');
                $(this).closest('td').find('input[type=file]').css('display', 'block');
            });
			
			$("#oborud_table").on("click", ".delFile", function () {
				var tr = $(this).closest('tr');
				var wrapperDel = $(this).closest('.wrapperDel');
				var trHidden = tr.next('.trHidden');
				var pathFileDel = wrapperDel.find('a').attr('href');
				trHidden.find('.hiddenDel').val(pathFileDel);
            });
        });
    </script>
	<?php
 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>