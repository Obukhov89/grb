$(function ($) {

    $('.select2').select2({
        theme: 'bootstrap-5'
    })

    if ($('#notify_leader').length) {
        $.magnificPopup.open({
            items: {
                src: '#notify_leader'
            },
            type: 'inline'
        })
    }

    /*journal requests*/
    let journalRequests = $('#journal_requests').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                // получение значений для селекта со статусом заявки
                const selectElement = document.querySelector('#selectStageGroup');
                // Получаем массив выбранных значений
                const selectedValues = Array.from(selectElement.options)
                    .filter(option => option.selected)
                    .map(option => option.value);

                // console.log(selectedValues); // ["value1", "value2"]

                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
                d.lab = $('#selectLab option:selected').val()
                d.everywhere = $('#filter_everywhere').val()
                d.stageGroup = selectedValues;//$('#selectStageGroup option:selected').val()
                d.certification = $('#selectCertification option:selected').val()
            },
            url : '/ulab/request/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data;
            },
        },
        columns: [
            {
                data: 'stage',
                orderable: false,
                render: function (data, type, item) {
                    // return `<div class="stage rounded ${item['bgStage']}" style="background-color: ${item['bgStage']}" title="${item['titleStage']}"></div>`
                    return `<a class="stage rounded ${item['bgStage']}" data-deal-id="${item['ID_Z']}" title="${item['titleStage']}" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample" style="background-color: ${item['bgStage']};">
                        <div class="rounded" style="width: 32px; height: 32px; background-color: ${item['bgStage']};"></div>
                    </a>`
                }
            },
            {
                data: 'requestTitle',
                class: 'text-nowrap',
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return `<a class="request-link"
                               href="/ulab/request/card/${item.ID_Z}?ACT_NUM=${item['ACT_NUM']}" target="_blank">
                               ${item['REQUEST_TITLE']}${item['certificate']}
                            </a>`
                    }

                    return item.request
                }
            },
            {
                data: 'DATE_CREATE_TIMESTAMP',
                width: 100,
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return item.dateCreateRu
                    }
                    return data;
                },
            },
            {
                data: 'COMPANY_TITLE',
                width: 300,
                // render: $.fn.dataTable.render.ellipsis(55, true)
            },
            {
                data: 'MATERIAL',
                render: $.fn.dataTable.render.ellipsis(40, true)
            },
            {
                data: 'ASSIGNED',
                orderable: false,
                render: $.fn.dataTable.render.ellipsis(40, true)
            },
            {
                data: 'tz',
                render: function (data, type, item) {
                    if ( item['ID_Z'] < 9357 ) {
                        return `<a class="number-tz" href="/ulab/requirement/card_old/${item['b_tz_id']}" target="_blank">
                                ${item['b_tz_id']}
                            </a>`
                    }
                    // if ( item['ID_Z'] >= 9763 ) {
                    //     return `<a class="number-tz" href="/ulab/requirement/card_new/${item['b_tz_id']}" target="_blank">
                    //             ${item['b_tz_id']}
                    //         </a>`
                    // }
                    if(item['b_tz_id']) {
                        return `<a class="number-tz" href="/ulab/requirement/card_new_new/${item['b_tz_id']}" target="_blank">
                                ${item['request']}
                            </a>`
                    } else {
                        return `<a class="number-tz" href="/ulab/requirement/card_new_new/${item['b_tz_id']}" target="_blank">
                                ${item['b_tz_id']}
                            </a>`
                    }
                },
            },
            {
                data: 'DOGOVOR_TABLE',
                //class: 'text-nowrap',
                width: '100px',
                //render: $.fn.dataTable.render.ellipsis(30, true)
            },
            {
                data: 'NUM_ACT_TABLE',
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return `<a class="number-act" href="/ulab/probe/card/${item['ID_Z']}" target="_blank">
                                ${item['NUM_ACT_TABLE']}
                            </a>`
                    }

                    return item.ACT_NUM
                },
            },
            {
                data: 'ACCOUNT',
                render: function (data, type, item) {
                    return item.ACCOUNT
                },
            },
            {
                data: 'price_discount',
                render: $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 })
            },
            {
                data: 'DATE_OPLATA',
                width: 100,
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return item.DATE_OPLATA
                    }

                    return item.dateOplataISO
                },
            },
            {
                data: 'linkName',
                orderable: false,
                render: function (data, type, item) {
                    if (item['is_result_refactoring']) {
                        return `<a class="results-link"
                               href="/ulab/result/${item['ID_Z'] >= item['start_new_area'] ? 'resultCard' : 'card'}/${item['ID_Z']}" target="_blank">
                                ${item['linkName']}
                            </a>`
                    } else {
                        return `<a class="results-link"
                               href="/results_isp.php?ID=${item['ID_Z']}&ID_P=${item['firstProtocolId']}" target="_blank">
                                ${item['linkName']}
                            </a>`
                    }
                },
            },
            {
                data: 'PROTOCOLS',
                orderable: false,
                render: $.fn.dataTable.render.ellipsis(30, true)
                // render: function (data, type, item) {
                //     let i = 0;
                //     let dataProtocol = `<div class="d-flex flex-column">`
                //
                //     if (item['PROTOCOLS']) {
                //         for (const val of item['PROTOCOLS']) {
                //             if ( i === 3 ) { break; }
                //             i++
                //
                //             for (const file of val['FILES']) {
                //                 if (file.indexOf('.pdf') !== -1) {
                //                     dataProtocol += `<a class="protocol-link"
                //                                         href="/protocol_generator/archive/${item['b_id']}${val['YEAR']}/${val['ID']}/${file}"
                //                                         target="_blank">
                //                                         ${val['NUMBER_AND_YEAR']}
                //                                     </a>`
                //                 }
                //             }
                //
                //             if (val['PROTOCOL_OUTSIDE_LIS'] && val['PDF']) {
                //                 dataProtocol += `<a class="protocol-link" href="/pdf/${val['ID']}/${val['PDF']}" target='_blank'
                //                                     target="_blank">
                //                                     ${val['NUMBER_AND_YEAR']}
                //                                 </a>`
                //             }
                //         }
                //     } else {
                //         if(item['NO_BITRIX']) {
                //             dataProtocol += `<a class="protocol-link" href="/pdf/${item['PDF']}" target='_blank'>
                //                                 ${item['PDF'] && item['NUM_P_TABLE'] ? item['NUM_P_TABLE'] : ''}
                //                             </a>`
                //         } else if (item['b_actual_ver']) {
                //             dataProtocol += `<a class="protocol-link" href="/protocol_generator/archive/${item['b_id']}${item['YEAR_ACT']}/${item['b_actual_ver']}.docx?1&1" target='_blank'>
                //                                 ${item['RESULTS'] && item['NUM_P_TABLE'] ? item['NUM_P_TABLE'] : ''}
                //                             </a>`
                //         }
                //     }
                //     dataProtocol += `</div>`
                //
                //     return dataProtocol
                // },
            },
            {
                data: 'MANUFACTURER_TITLE',
                class: 'text-nowrap',
                render: $.fn.dataTable.render.ellipsis(25, true)
            },
            {
                data: 'DEADLINE_TABLE',
                width: 100,
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return `<span class="${item.textColor}">${item.DEADLINE_TABLE}</span>`
                    }

                    return item.deadlineISO
                },
            }
        ],
        createdRow: function(row, data, dataIndex) {
            $(row).find('td:eq(0)').attr('data-lab', data.LABA_ID);
            $('td:eq(10)', row).addClass(data.bgPrice);
            $('td:eq(7)', row).addClass(data.bgOrder);
            $('td:eq(6)', row).addClass(data.bgPdf);
            $(row).addClass(data.bgCheck);
        },
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
        order: [[ 2, "desc" ]],
        colReorder: true,
        dom: 'frtB<"bottom"lip>',
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
        fixedHeader:   true,
    });

    $('#selectCertification').on('change', function () {
        journalRequests.ajax.reload()
        journalRequests.draw()
    })

    journalRequests.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalRequests
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        })
    })
    
    /*journal filters*/
    $('.filter-btn-search').on('click', function () {
        $('#journal_requests_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $('.filter').on('change', function () {

        journalRequests.ajax.reload()
        journalRequests.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $('#journal_requests').width()

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
        container.stop();
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
        container.stop();
    })

    $(document).scroll(function() {
        let positionScroll = $(window).scrollTop(),
            tableScrollBody = container.height()

        if (positionScroll > 265 && positionScroll < tableScrollBody) {
            $('.arrowRight').css('transform',`translateY(${positionScroll-260}px)`);
            $('.arrowLeft').css('transform',`translateY(${positionScroll-250}px)`);
        }
    })

    // /*journal modal window*/
    // if (window.location.pathname === '/ulab/request/list/') {
    //
    //     $.ajax({
    //         url: '/ulab/request/getCheckTzAjax/',
    //         method: 'GET',
    //         dataType: 'json',
    //         success: function (data) {
    //             let content = `<table class="table table-striped table-modal"><tbody>`;
    //
    //             data.forEach(function (item) {
    //                 //TODO: Изменить ссылку на крточку заявки
    //                 content  += `<tr class="align-middle text-nowrap">
    //                                 <td>
    //                                     <span title="${item.REQUEST_TITLE}">${item.cropRequestTitle}</span>
    //                                 </td>
    //                                 <td>
    //                                     <span title="${item.COMPANY_TITLE}">${item.cropCompanyTitle}</span>
    //                                 </td>
    //                                 <td class="${item.bgColor}">
    //                                     <span title="${item.status}">${item.cropStatus}</span>
    //                                 </td>
    //                                 <td>
    //                                     <!--a href="/request_card.php?ID=${item.ID_Z}" target="_blank">
    //                                         <div class="card-icon" title="Карточка заявки"></div>
    //                                     </a-->
    //                                     <a href="/ulab/request/card/${item.ID_Z}" target="_blank">
    //                                         <div class="card-icon" title="Карточка заявки"></div>
    //                                     </a>
    //                                 </td>
    //                             </tr>`;
    //             })
    //
    //             content += `</tbody></table>`;
    //
    //             let modal = $modal({
    //                 title: 'Техническое задание на рассмотрении',
    //                 content: content,
    //                 footerButtons: [
    //                     {
    //                         class: 'btn btn__ok',
    //                         text: 'ОК',
    //                         handler: 'modalHandlerOk'
    //                     }
    //                 ]
    //             });
    //
    //             modal.show();
    //         }
    //     });
    // }

    $('body').on('click', '.accept_probe', function () {
        let idRequest = $(this).data('id'),
            tr = $(this).parents('.probe_row'),
            a = $(this)

        $.ajax({
            url: '/ulab/probe/acceptProbeAjax/',
            method: 'POST',
            data: {
                id: idRequest
            },
            dataType: 'json',
            success: function (data) {
                tr.addClass('table-green')
                a.removeClass('accept_probe')
                a.addClass('remove_accept_probe')
                a.css('color' , 'crimson')
                a.text('Отменить')
            }
        })
    })

    $('body').on('click', '.accept_all', function () {
        let idRequest = $(this).data('id'),
            tr = $(this).parents('.probe_row'),
            a = $(this),
            child = $(`.probe_child_${idRequest}`),
            arrUmtrId = child.find('.accept_probe').map(function () {
                return this.dataset.id
            }).get()

        $.ajax({
            url: '/ulab/probe/acceptProbeAjax/',
            method: 'POST',
            data: {
                id: idRequest,
                arr: arrUmtrId
            },
            dataType: 'json',
            success: function (data) {

                child.find('.accept_probe').addClass('remove_accept_probe')
                child.find('.accept_probe').css('color' , 'crimson')
                child.find('.accept_probe').text('Отменить')
                child.toggleClass('table-green')
                child.find('.accept_probe').removeClass('accept_probe')
                tr.addClass('table-green')
                a.removeClass('accept_all')
                a.addClass('remove_accept_probe_all')
                a.css('color' , 'crimson')
                a.text('Отменить')
            }
        })

        let userId = $('#inp-id-usr').val();
        let dealId = $(this).data('id-z');
    })

    $('body').on('click', '.accept_probe_pay', function () {
        let idRequest = $(this).data('id'),
            tr = $(this).parents('.probe_row_pay'),
            a = $(this)

        $.ajax({
            url: '/ulab/probe/acceptProbeAjax/',
            method: 'POST',
            data: {
                id: idRequest
            },
            dataType: 'json',
            success: function (data) {
                tr.addClass('table-green')
                a.removeClass('accept_probe_pay')
                a.addClass('remove_accept_probe_pay')
                a.css('color' , 'crimson')
                a.text('Отменить')
            }
        })

    })

    $('body').on('click', '.remove_accept_probe', function () {
        let idRequest = $(this).data('id'),
            tr = $(this).parents('.probe_row'),
            a = $(this)

        $.ajax({
            url: '/ulab/probe/removeAcceptProbeAjax/',
            method: 'POST',
            data: {
                id: idRequest
            },
            dataType: 'json',
            success: function (data) {
                tr.removeClass('table-green')
                a.addClass('accept_probe')
                a.removeClass('remove_accept_probe')
                a.css('color' , '')
                a.text('Принять')
            }
        })

    })

    $('body').on('click', '.remove_accept_probe_all', function () {
        let idRequest = $(this).data('id'),
            tr = $(this).parents('.probe_row'),
            a = $(this),
            child = $(`.probe_child_${idRequest}`),
            arrUmtrId = child.find('.remove_accept_probe').map(function () {
                return this.dataset.id
            }).get()

        $.ajax({
            url: '/ulab/probe/removeAcceptProbeAjax/',
            method: 'POST',
            data: {
                id: idRequest,
                arr: arrUmtrId
            },
            dataType: 'json',
            success: function (data) {
                child.find('.remove_accept_probe').css('color' , '')
                child.find('.remove_accept_probe').text('Принять')
                child.find('.remove_accept_probe').addClass('accept_probe')
                child.toggleClass('table-green')
                child.find('.remove_accept_probe').removeClass('remove_accept_probe')
                a.addClass('accept_all')
                a.css('color' , '')
                a.text('Принять все')
                tr.removeClass('table-green')
                a.removeClass('remove_accept_probe_all')
            }
        })

    })

    $('body').on('click', '.remove_accept_probe_pay', function () {
        let idRequest = $(this).data('id'),
            tr = $(this).parents('.probe_row_pay'),
            a = $(this)

        $.ajax({
            url: '/ulab/probe/removeAcceptProbeAjax/',
            method: 'POST',
            data: {
                id: idRequest
            },
            dataType: 'json',
            success: function (data) {
                tr.removeClass('table-green')
                a.addClass('accept_probe_pay')
                a.removeClass('remove_accept_probe_pay')
                a.css('color' , '')
                a.text('Принять')
            }
        })

    })

    $('body').on('click', '.more-probe', function () {
        let reqId = $(this).data('request-id')
        $(`.probe_child_${reqId}`).toggleClass('d-none')
    })

    // $('body').on('click', '.stage', function () {
    //     $.ajax({
    //         url: '/ulab/status/getStatusForDealAjax',
    //         data: {
    //             deal_id: $(this).data('deal-id')
    //         },
    //         method: 'POST',
    //         success: function (json) {
    //             let responseArray = JSON.parse(json)
    //             let body = document.querySelector('.offcanvas-body')
    //
    //             console.log(responseArray)
    //
    //             let mainContainer = ''
    //             body.innerHTML = ''
    //
    //             for(const [key, value] of Object.entries(responseArray.statuses)) {
    //                 let table = ''
    //                 mainContainer = ''
    //
    //                 if(key === responseArray.title) {
    //                     mainContainer +=
    //                         `
    //                         <header class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; background-color: #dffff0;">
    //                             <div class="d-flex gap-2" style="padding: 8px">
    //                                 <div style="width: 30px; height: 30px; border-radius: 20% !important; background-color: ${value[0]['group_color']}"></div>
    //                                 <span>${key}</span>
    //                             </div>
    //                             <span class="tools float-end">
    //                                 <a href="#" class="fa fa-chevron-up"></a>
    //                             </span>
    //                         </header>
    //                         `
    //
    //                     table +=
    //                         `
    //                             <div class="panel-body" style="background-color: #dffff0">
    //                             <table class="table" style="background-color: #dffff0">
    //                         `
    //                 }else if(key === responseArray.next_title) {
    //                     mainContainer +=
    //                         `
    //                         <div style="display: flex; flex-direction: column; margin: 0; align-items: center; justify-content: center">
    //                             <h5 style="color: #FF0000">${responseArray.next_comment}</h5>
    //                         </div>
    //                         <header class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; background-color: #fdffde;">
    //                             <div class="d-flex gap-2" style="padding: 8px">
    //                                 <div style="width: 30px; height: 30px; border-radius: 20% !important; background-color: ${value[0]['group_color']}"></div>
    //                                 <span>${key}</span>
    //                             </div>
    //                             <span class="tools float-end">
    //                                 <a href="#" class="fa fa-chevron-up"></a>
    //                             </span>
    //                         </header>
    //                         `
    //
    //                     table +=
    //                         `
    //                             <div class="panel-body" style="background-color: #fdffde">
    //                             <table class="table" style="background-color: #fdffde">
    //                         `
    //                 }else {
    //                     mainContainer +=
    //                         `
    //                         <header class="panel-heading" style="display: flex; justify-content: space-between; align-items: center">
    //                             <div class="d-flex gap-2" style="padding: 8px">
    //                                 <div style="width: 30px; height: 30px; border-radius: 20% !important; background-color: ${value[0]['group_color']}"></div>
    //                                 <span>${key}</span>
    //                             </div>
    //                             <span class="tools float-end">
    //                                 <a href="#" class="fa fa-chevron-down"></a>
    //                             </span>
    //                         </header>
    //                         `
    //
    //                     table +=
    //                         `
    //                             <div class="panel-body" style="display: none;">
    //                             <table class="table">
    //                         `
    //                 }
    //
    //                 console.log(value)
    //
    //                 table +=
    //                     `
    //                     <thead>
    //                     </thead>
    //                     <tbody>
    //                     `
    //
    //                 for(const [k, v] of Object.entries(value)) {
    //                     table += `<tr>`
    //
    //                     if(v.header_status === "1") {
    //                         table +=
    //                             `
    //                              <td style="font-size: 12px; width: 310px">
    //                                 <div style="display: flex; align-items: center; gap: 5px;">
    //                                     ${v.name} <span class="redStars">*</span>
    //                                 </div>
    //                              </td>
    //                             `
    //                     }else{
    //                         table +=
    //                             `
    //                             <td style="font-size: 12px">${v.name}</td>
    //                             `
    //                     }
    //
    //                     if(v.date) {
    //                         let dateArray = v.date.split(' ')
    //                         let shortDateArray = dateArray[0].split('-')
    //                         let timeArray = dateArray[1].split(':')
    //
    //                         let day = shortDateArray[2];
    //                         let month = shortDateArray[1];
    //                         let year = shortDateArray[0];
    //                         let minutes = timeArray[1];
    //                         let hours = timeArray[0];
    //
    //                         if(day !== '00') {
    //                             if(hours !== '00') {
    //                                 table +=
    //                                     `
    //                                 <td style="font-size: 12px; width: 125px">${day}.${month}.${year} ${hours}:${minutes}</td>
    //                                 <td style="font-size: 12px; width: 115px">
    //                                     <div>
    //                                         <svg width="20px" height="20px" viewBox="0 0 24 24" fill="#7CC504" x="188.5" y="188.5" role="img" style="display:inline-block;vertical-align:middle" xmlns="http://www.w3.org/2000/svg"><g fill="#7CC504"><path fill="#7CC504" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm4.768 9.14a1 1 0 1 0-1.536-1.28l-4.3 5.159l-2.225-2.226a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.475-.067l5-6Z" clip-rule="evenodd"/></g></svg>
    //                                     </div>
    //                                 </td>
    //                                 </tr>
    //                             `
    //                             }else {
    //                                 table +=
    //                                     `
    //                                 <td style="font-size: 12px; width: 125px">${day}.${month}.${date.getFullYear()} --</td>
    //                                 <td style="font-size: 12px; width: 115px">
    //                                     <div>
    //                                         <svg width="20px" height="20px" viewBox="0 0 24 24" fill="#7CC504" x="188.5" y="188.5" role="img" style="display:inline-block;vertical-align:middle" xmlns="http://www.w3.org/2000/svg"><g fill="#7CC504"><path fill="#7CC504" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm4.768 9.14a1 1 0 1 0-1.536-1.28l-4.3 5.159l-2.225-2.226a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.475-.067l5-6Z" clip-rule="evenodd"/></g></svg>
    //                                     </div>
    //                                 </td>
    //                                 </tr>
    //                             `
    //                             }
    //                         }else {
    //                             table +=
    //                                 `
    //                                 <td style="font-size: 12px; width: 125px">--</td>
    //                                 <td style="font-size: 12px; width: 115px">
    //                                     <div>
    //                                         <svg width="20px" height="20px" viewBox="0 0 24 24" fill="#7CC504" x="188.5" y="188.5" role="img" style="display:inline-block;vertical-align:middle" xmlns="http://www.w3.org/2000/svg"><g fill="#7CC504"><path fill="#7CC504" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm4.768 9.14a1 1 0 1 0-1.536-1.28l-4.3 5.159l-2.225-2.226a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.475-.067l5-6Z" clip-rule="evenodd"/></g></svg>
    //                                     </div>
    //                                 </td>
    //                                 </tr>
    //                             `
    //                         }
    //                     }else {
    //                         table +=
    //                             `
    //                             <td style="font-size: 12px; width: 125px">--</td>
    //                             <td style="font-size: 12px; width: 115px">
    //                                 <div>
    //                                     <svg width="20px" height="20px" viewBox="0 0 24 24" fill="#c0c0c0" x="188.5" y="188.5" role="img" style="display:inline-block;vertical-align:middle" xmlns="http://www.w3.org/2000/svg"><g fill="#c0c0c0"><path fill="#c0c0c0" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm4.768 9.14a1 1 0 1 0-1.536-1.28l-4.3 5.159l-2.225-2.226a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.475-.067l5-6Z" clip-rule="evenodd"/></g></svg>
    //                                 </div>
    //                             </td>
    //                             </tr>
    //                             `
    //                     }
    //                 }
    //
    //                 table +=
    //                     `
    //                     </tbody>
    //                     </table>
    //                     </div>
    //                     </div>
    //                     `
    //
    //                 mainContainer += table
    //                 mainContainer = `<div class="panel panel-default">` + mainContainer + `</div>`
    //
    //                 body.innerHTML += mainContainer
    //             }
    //         },
    //         error: function () {
    //             console.log('error')
    //         }
    //     })
    // })
})
