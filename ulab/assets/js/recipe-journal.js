$(function ($) {
    /*recipe journal*/
    let recipeJournal = $('#recipe_journal').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
                // d.stage = $('#selectStage option:selected').val()
                // d.lab = $('#selectLab option:selected').val()
                d.everywhere = $('#filter_everywhere').val()
                d.param = $('#param').val()
            },
            url : '/ulab/recipe/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data
            },
        },
        columns: [
            {
                data: 'name_recipe'
            },
            {
                data: 'concentration'
            },
            {
                data: 'GOST'
            },
            {
                data: 'reactives'
            },
            {
                data: 'solvent'
            },
            {
                data: 'quantity_solution'
            },
            {
                data: 'storage_life_in_day'
            },

        ],
        'columnDefs': [{
            'targets': [0,1,2,3,4,5,6],
            'orderable': false,
        }],
        language:{
            processing: 'Подождите...',
            search: '',
            searchPlaceholder: "Поиск...",
            lengthMenu: 'Отображать _MENU_  ',
            info: 'Записи с _START_ до _END_ из _TOTAL_ записей',
            infoEmpty: 'Записи с 0 до 0 из 0 записей',
            infoFiltered: '(отфильтровано из _MAX_ записей)',
            infoPostFix: '',
            loadingRecords: 'Загрузка записей...',
            zeroRecords: 'Записи отсутствуют.',
            emptyTable: 'В таблице отсутствуют данные',
            paginate: {
                first: 'Первая',
                previous: 'Предыдущая',
                next: 'Следующая',
                last: 'Последняя'
            },
            buttons: {
                colvis: '',
                copy: '',
                excel: '',
                print: ''
            },
            aria: {
                sortAscending: ': активировать для сортировки столбца по возрастанию',
                sortDescending: ': активировать для сортировки столбца по убыванию'
            }
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
        pageLength: 25,
        order: [],
        colReorder: true,
        dom: 'fBrt<"bottom"lip>',
        buttons: [
            {
                extend: 'colvis',
                titleAttr: 'Выбрать'
            },
            {
                extend: 'copy',
                titleAttr: 'Копировать',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'excel',
                titleAttr: 'excel',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'print',
                titleAttr: 'Печать',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            }
        ],
        bSortCellsTop: true,
        scrollX:       true,
        fixedHeader:   false,
    })

    recipeJournal.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            recipeJournal
                .column( $(this).parent().index() )
                .search( this.value )
                .draw()
        })
    })

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $('#recipe_journal').width()

    $('.btnRightTable, .arrowRight').hover(function() {
            container.animate(
                {
                    scrollLeft: scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function() {
            container.stop()
        })

    $('.btnLeftTable, .arrowLeft').hover(function() {
            container.animate(
                {
                    scrollLeft: -scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function() {
            container.stop()
        })

    let $body = $("body")
    let $containerScroll = $body.find('.dataTables_scroll')
    let $thead = $('.journal thead tr:first-child')

    $(document).scroll(function() {
        let positionScroll = $(window).scrollTop(),
            tableScrollBody = container.height(),
            positionTop = $containerScroll.offset().top

        if ( positionScroll >= positionTop ) {
            $thead.attr('style', 'position:fixed;top:0;z-index:99')
        } else {
            $thead.attr('style', '')
        }

        if (positionScroll > 265 && positionScroll < tableScrollBody) {
            $('.arrowRight').css('transform',`translateY(${positionScroll-260}px)`)
            $('.arrowLeft').css('transform',`translateY(${positionScroll-250}px)`)
        }
    })

/*    $("body").on("click", function(){
        $("span.select2-selection").css("height", "38px")
        $("span.select2-selection").css("border", "1px solid #ccc")
    })*/
    $("body").on('click', '.add-reactive', function () {

        $.each($('.select-reactive'), function () {
            $(this).select2('destroy');
        });
        let cloneReactive = $('.reactives[data-id = "1"]').clone()
        let countReactive = $('.reactives').length
        let countReactivePlusOne = countReactive + 1
        cloneReactive.attr('data-id', countReactivePlusOne)
        cloneReactive.find('select').attr('name',`reactives[reactive`+countReactivePlusOne+`][id_reactive]`)
        cloneReactive.find('input').attr('name',`reactives[reactive`+countReactivePlusOne+`][quantitya_reactive]`)
        $(`.reactives[data-id="${countReactive}"]`).after(cloneReactive)
        $.each($('.select-reactive'), function () {
            $(this).select2();
        });
    })
    $("body").on('click', '.del-reactive', function () {
        let countReactive = $('.reactives').length
        if(countReactive>1) $(`.reactives[data-id="${countReactive}"]`).remove()
        else alert("Нельзя удалить единственный реактив")
    })
    /** modal */
    $('.popup-first').magnificPopup({
        items: {
            src: '#add-entry-modal-form-first',
            type: 'inline'
        },
        fixedContentPos: false
    })
    $('.popup-second').magnificPopup({
        items: {
            src: '#add-entry-modal-form-second',
            type: 'inline'
        },
        fixedContentPos: false
    })

    $('.select-doc').select2({
        placeholder: 'Выбирете документ',
        width: '100%',
    })
    $('.select-reactive').select2({
        placeholder: 'Выбирете реактив',
        width: '100%',
    })
    $('.select-solution').select2({
        placeholder: 'Выбирете растворитель',
        width: '100%',
    })
    $('.select-recipe').select2({
        placeholder: 'Выбирете рецепт',
        width: '100%',
    })

    /** journal filters */
    $('.filter-btn-search').on('click', function () {
        $('#journal_requests_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $('.filter').on('change', function () {
        recipeJournal.ajax.reload()
        recipeJournal.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

    //Временно сохранить
   /* $('.search:first').on('input', function () {
        let val = $(this).val()
        $('#param').val(val)
    })*/
})
