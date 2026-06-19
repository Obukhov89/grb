$(document).ready(function () {

    $('#period1').datepicker({
        regional: ['ru'],
        dateFormat: 'dd.mm.yy',
        showOtherMonths: true,
        flag: false,
        onSelect: function(dataText) {
            setTimeout($.proxy(function() {
                $('#period2').datepicker("show");
            }, this), 10);
        }
    });
    $('#period2').datepicker({
        regional: ['ru'],
        dateFormat: 'dd.mm.yy',
        showOtherMonths: true,
        onClose: function() {
            $(this).focus();
        }
    });

    function getServerDate(s) {
        let zeroStr = '0';
        let sDate = '';
        let date = s.split('.');
        let newDate = new Date(date[2], date[1]-1, date[0]);

        if((newDate.getFullYear() % 100) < 10){
            sDate += zeroStr;
        }
        sDate += (newDate.getFullYear() + '-');
        if((newDate.getMonth() + 1) < 10){
            sDate += zeroStr;
        }
        sDate += ((newDate.getMonth() + 1) + '-');

        if(newDate.getDate() < 10) {
            sDate += zeroStr;
        }
        sDate += newDate.getDate();

        return sDate;
    }


    let stage = $('#stage option:selected').val();
    let laba = $('#laba option:selected').val();
    let dateStart = getServerDate($('#period1').val());
    let dateOut = getServerDate($('#period2').val());
    
    $('#period2, #stage, #laba').on('change', function (event) {

        let s = $('#period1').val();
        let e = $('#period2').val();
        let sDate = getServerDate(s);
        let eDate = getServerDate(e);
        let str = 'career_log.php?dateStart='+sDate+'&dateOut='+eDate;
        dateStart = sDate;
        dateOut = eDate;

        stage = $('#stage option:selected').val();
        let strStage = '&stage='+stage;

        laba = $('#laba option:selected').val();
        let strlaba = '&laba='+laba;

        if(stage != 'all'){
            str += strStage;
        }
        if(laba != 'all'){
            str += strlaba;
        }
        window.location.assign(str);

        console.log('s', s);
        console.log('e', e);

    });

    function reloadWindow () {
        setTimeout(function() {


            var getActNum = '<?= $_GET["ACT_NUM"] ?>' ? '<?= $_GET["ACT_NUM"] ?>' : '';
            var getId = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';

            window.location.assign("/request_card.php?ID=" + getId + "&ACT_NUM=" + getActNum);

        }, 1500);
    }

    //Подключаем и настраиваем DataTable
    let table= $('#table-carrer').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            //url : "/Quarry/CarrerController.php",  //файл серверной обработки
            url : "/Quarry/CarrerController.php?action=getData",  //файл серверной обработки
            type: "POST",
            data: {
                stage: stage,
                laba: laba,
                dateStart: dateStart,
                dateOut: dateOut,
            },
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "All"]], //Селект с выбором какое кол-во записей отображать в таблице
        pageLength: 50, //количество строк на странице
        "createdRow": function ( row, data, index ) {
            //Первая колонка
            $(row).children(':nth-child(1)').css({'background-color': data['COLOR'], 'font-size':0});
            $(row).children(':nth-child(1)').attr('title', data['HOVER']);
            //Стоимость
            $(row).children(':nth-child(12)').css('background-color', data['COLOR_PRICE']);
        },
        "columns": [
            { "data": "STAGE_NUMBER" }, //Номер стадии Заявки
            { "data": "REQUEST_TITLE" }, // Заявка (Наименование заявки ИЦ №1/21)
            { "data": "DATE_CREATE" }, // Дата (Дата создания заявки)
            { "data": "COMPANY_TITLE" }, //Клиент (Наименование компании)
            { "data": "DEADLINE_TABLE" }, //Крайний срок
            { "data": "MATERIAL" }, //Объект испытаний (Наименование используемых материалов)
            { "data": "ASSIGNED" }, //Ответственный
            { "data": "NAME" }, //Карьер
            { "data": "NUM_ACT_TABLE" }, //Акт ПП (Номер акта полученных проб 426/21)
            { "data": "ID" }, // ТЗ (Номер,ID технического задания)
            { "data": "DOGOVOR_TABLE" }, //Договор (Номер и дата договора)
            { "data": "PRICE" }, //Стоимость
            { "data": "NUM_P_TABLE" }, //Протокол (Номер и год проткола 1/21)
        ],
        "language":{ //Описание
            "processing": "Подождите...", //Сообщение при загрузке страницы
            "search": "Поиск:",
            "lengthMenu": "Показать _MENU_ записей", //Описание с селектом который вибирает какое кол-во записей отображать в таблице
            "info": "Записи с _START_ до _END_ из _TOTAL_ записей", //Информация с низу таблицы о кол-ве записей
            "infoEmpty": "Записи с 0 до 0 из 0 записей",
            "infoFiltered": "(отфильтровано из _MAX_ записей)",
            "infoPostFix": "",
            "loadingRecords": "Загрузка записей...",
            "zeroRecords": "Записи отсутствуют.",
            "emptyTable": "В таблице отсутствуют данные", //Если нет данных отображает надпись в центре таблицы
            "paginate": { //Описание кнопок пагинации
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
        "columnDefs": [
            /*{ "type": "natural-nohtml", "targets": 0 },*/
            { "type": "natural-nohtml", "targets": 1 }, // Заявка (Наименование заявки ИЦ №1/21)
            { "sType": "ruDate", "targets": 2 }, // Дата (Дата создания заявки)
            {
                "targets": 3, //Клиент (Наименование компании)
                "render": $.fn.dataTable.render.ellipsis( 50, true )
            },
            { "sType": "ruDate", "targets": 4 }, //Крайний срок
            {
                "targets": 5, //Объект испытаний (Наименование используемых материалов)
                "render": $.fn.dataTable.render.ellipsis( 50, true )
            },
            {
                "targets": 6, //Ответственный
                "render": $.fn.dataTable.render.ellipsis( 50, true )
            },
            { "type": "natural-nohtml", "targets": 8 }, //Акт ПП (Номер акта полученных проб 426/21)
            {
                "targets": 10, //Договор (Номер и дата договора)
                "render": $.fn.dataTable.render.ellipsis( 30, true )
            },
            { "type": "natural-nohtml", "targets": 11, "render": $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 }) }, //Стоимость
            { "type": "natural-nohtml", "targets": 12 }, //Протокол (Номер и год проткола 1/21)
        ],
        "order": [[ 2, "desc" ]],
        "colReorder": true,
        "bSortCellsTop": true,
        "dom": 'Blfrtip',
        "buttons": [ 'copy', 'excel', 'colvis', 'print' ],
        //"aoColumnDefs": [{"bVisible": false, 'aTargets': [4]}]

    });

    $('#manufacture').on( 'click', function () {
        if ($('#manufacture').prop('checked')) {
            table
                .columns( 0 )
                .search( 'checked' )
                .draw();
        } else {
            table
                .columns( 0 )
                .search( '' )
                .draw();
        }

    });

    //Поиск Заявки
    $('.request').on( 'keyup', function () {
        table
            .columns( 1 )
            .search( this.value )
            .draw();
    });

    //Поиск Даты
    $('.date').on( 'keyup', function () {
        table
            .columns( 2 )
            .search( this.value )
            .draw();
    });

    //Поиск Клиента
    $('.client').on( 'keyup', function () {
        table
            .columns( 3 )
            .search( this.value )
            .draw();
    });

    //Поиск Крайний срок
    $('.deadline').on( 'keyup', function () {
        table
            .columns( 4 )
            .search( this.value )
            .draw();
    });

    //Поиск Объект испытаний
    $('.object').on( 'keyup', function () {
        table
            .columns( 5 )
            .search( this.value )
            .draw();
    });

    //Поиск Ответственного
    $('.responsible').on( 'keyup', function () {
        table
            .columns( 6 )
            .search( this.value )
            .draw();
    });

    //Поиск Карьера
    $('.quarry').on( 'keyup', function () {
        table
            .columns( 7 )
            .search( this.value )
            .draw();
    });

    //Поиск Акт ПП
    $('.act').on( 'keyup', function () {
        table
            .columns( 8 )
            .search( this.value )
            .draw();
    });

    //Поиск ТЗ
    $('.tz_id').on( 'keyup', function () {
        table
            .columns( 9 )
            .search( this.value )
            .draw();
    });

    //Поиск Договора
    $('.contract').on( 'keyup', function () {
        table
            .columns( 10 )
            .search( this.value )
            .draw();
    });

    //Поиск Стоимость
    $('.cost').on( 'keyup', function () {
        table
            .columns( 11 )
            .search( this.value )
            .draw();
    });

    //Поиск Пролокола
    $('.protocol').on( 'keyup', function () {
        table
            .columns( 12 )
            .search( this.value )
            .draw();
    });

    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
        "ruDate-asc": function ( a, b ) {
            var ruDatea = $.trim(a).split('.');
            var ruDateb = $.trim(b).split('.');

            if(ruDatea[2]*1 < ruDateb[2]*1)
                return 1;
            if(ruDatea[2]*1 > ruDateb[2]*1)
                return -1;
            if(ruDatea[2]*1 == ruDateb[2]*1)
            {
                if(ruDatea[1]*1 < ruDateb[1]*1)
                    return 1;
                if(ruDatea[1]*1 > ruDateb[1]*1)
                    return -1;
                if(ruDatea[1]*1 == ruDateb[1]*1)
                {
                    if(ruDatea[0]*1 < ruDateb[0]*1)
                        return 1;
                    if(ruDatea[0]*1 > ruDateb[0]*1)
                        return -1;
                }
                else
                    return 0;
            }
        },

        "ruDate-desc": function ( a, b ) {
            var ruDatea = $.trim(a).split('.');
            var ruDateb = $.trim(b).split('.');

            if(ruDatea[2]*1 < ruDateb[2]*1)
                return -1;
            if(ruDatea[2]*1 > ruDateb[2]*1)
                return 1;
            if(ruDatea[2]*1 == ruDateb[2]*1)
            {
                if(ruDatea[1]*1 < ruDateb[1]*1)
                    return -1;
                if(ruDatea[1]*1 > ruDateb[1]*1)
                    return 1;
                if(ruDatea[1]*1 == ruDateb[1]*1)
                {
                    if(ruDatea[0]*1 < ruDateb[0]*1)
                        return -1;
                    if(ruDatea[0]*1 > ruDateb[0]*1)
                        return 1;
                }
                else
                    return 0;
            }
        }
    });

});
