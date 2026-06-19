$(function ($) {
    /*journal requests*/
    let journalRequests = $('#journal_requests').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.stageGroup = $('#selectStageGroup option:selected').val()
            },
            url : '/ulab/statistic/getListTzStatuses/',
            dataSrc: function (json) {
                return json.data;
            },
        },
        columns: [
            {
                data: 'stage',
                orderable: false,
                render: function (data, type, item) {
                    return `<a class="stage rounded ${item['bgStage']}" data-deal-id="${item['id']}" title="${item['titleStage']}" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample" style="background-color: ${item['bgStage']};">
                        <div class="rounded" style="width: 32px; height: 32px; background-color: ${item['bgStage']};"></div>
                    </a>`
                }
            },
            {
                data: 'REQUEST_TITLE',
                class: 'text-nowrap',
                render: function (data, type, item) {
                    return `<a class="request-link"
                           href="/ulab/request/card/${item['id']}" target="_blank">
                           ${item['REQUEST_TITLE']}
                        </a>`
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

    journalRequests.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalRequests
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        })
    })

    $('.filter').on('change', function () {

        journalRequests.ajax.reload()
        journalRequests.draw()
    })

    $('body').on('click', '.stage', function () {
        $.ajax({
            url: '/ulab/status/getStatusForDealAjax',
            data: {
                deal_id: $(this).data('deal-id')
            },
            method: 'POST',
            success: function (json) {
                let responseArray = JSON.parse(json)
                let body = document.querySelector('.offcanvas-body')

                console.log(responseArray)

                let mainContainer = ''
                body.innerHTML = ''

                for(const [key, value] of Object.entries(responseArray.statuses)) {
                    let table = ''
                    mainContainer = ''

                    if(key === responseArray.title) {
                        mainContainer +=
                            `
                            <header class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; background-color: #dffff0;">
                                <div class="d-flex gap-2" style="padding: 8px">
                                    <div style="width: 30px; height: 30px; border-radius: 20% !important; background-color: ${value[0]['group_color']}"></div>
                                    <span>${key}</span>
                                </div>
                                <span class="tools float-end">
                                    <a href="#" class="fa fa-chevron-up"></a>
                                </span>
                            </header>
                            `

                        table +=
                            `
                                <div class="panel-body" style="background-color: #dffff0">
                                <table class="table" style="background-color: #dffff0">
                            `
                    }else if(key === responseArray.next_title) {
                        mainContainer +=
                            `
                            <div style="display: flex; flex-direction: column; margin: 0; align-items: center; justify-content: center">
                                <h5 style="color: #FF0000">${responseArray.next_comment}</h5>
                            </div>
                            <header class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; background-color: #fdffde;">
                                <div class="d-flex gap-2" style="padding: 8px">
                                    <div style="width: 30px; height: 30px; border-radius: 20% !important; background-color: ${value[0]['group_color']}"></div>
                                    <span>${key}</span>
                                </div>
                                <span class="tools float-end">
                                    <a href="#" class="fa fa-chevron-up"></a>
                                </span>
                            </header>   
                            `

                        table +=
                            `
                                <div class="panel-body" style="background-color: #fdffde">
                                <table class="table" style="background-color: #fdffde">
                            `
                    }else {
                        mainContainer +=
                            `
                            <header class="panel-heading" style="display: flex; justify-content: space-between; align-items: center">
                                <div class="d-flex gap-2" style="padding: 8px">
                                    <div style="width: 30px; height: 30px; border-radius: 20% !important; background-color: ${value[0]['group_color']}"></div>
                                    <span>${key}</span>
                                </div>
                                <span class="tools float-end">
                                    <a href="#" class="fa fa-chevron-down"></a>
                                </span>
                            </header>
                            `

                        table +=
                            `
                                <div class="panel-body" style="display: none;">
                                <table class="table">
                            `
                    }

                    console.log(value)

                    table +=
                        `
                        <thead>
                        </thead>
                        <tbody>
                        `

                    for(const [k, v] of Object.entries(value)) {
                        table += `<tr>`

                        if(v.header_status === "1") {
                            table +=
                                `
                                 <td style="font-size: 12px; width: 310px">
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        ${v.name} <span class="redStars">*</span>
                                    </div>
                                 </td>
                                `
                        }else{
                            table +=
                                `
                                <td style="font-size: 12px">${v.name}</td>
                                `
                        }

                        if(v.date) {
                            let date = new Date(v.date)

                            let day = '0';
                            let month = '0';
                            let hours = '0';
                            let minutes = '0';

                            if(date.getDay() < 10) day += date.getDay(); else day = date.getDay()
                            if(date.getMonth() < 10) month += date.getMonth(); else month = date.getMonth()
                            if(date.getHours() < 10) hours += date.getHours(); else hours = date.getHours()
                            if(date.getMinutes() < 10) minutes += date.getMinutes(); else minutes = date.getMinutes()

                            if(!isNaN(date.getDay())) {
                                if(!isNaN(date.getHours())) {
                                    table +=
                                        `
                                    <td style="font-size: 12px; width: 125px">${day}.${month}.${date.getFullYear()} ${hours}:${minutes}</td>
                                    <td style="font-size: 12px; width: 115px">
                                        <div>
                                            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="#7CC504" x="188.5" y="188.5" role="img" style="display:inline-block;vertical-align:middle" xmlns="http://www.w3.org/2000/svg"><g fill="#7CC504"><path fill="#7CC504" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm4.768 9.14a1 1 0 1 0-1.536-1.28l-4.3 5.159l-2.225-2.226a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.475-.067l5-6Z" clip-rule="evenodd"/></g></svg>
                                        </div>
                                    </td>
                                    </tr>
                                `
                                }else {
                                    table +=
                                        `
                                    <td style="font-size: 12px; width: 125px">${day}.${month}.${date.getFullYear()} --</td>
                                    <td style="font-size: 12px; width: 115px">
                                        <div>
                                            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="#7CC504" x="188.5" y="188.5" role="img" style="display:inline-block;vertical-align:middle" xmlns="http://www.w3.org/2000/svg"><g fill="#7CC504"><path fill="#7CC504" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm4.768 9.14a1 1 0 1 0-1.536-1.28l-4.3 5.159l-2.225-2.226a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.475-.067l5-6Z" clip-rule="evenodd"/></g></svg>
                                        </div>
                                    </td>
                                    </tr>
                                `
                                }
                            }else {
                                table +=
                                    `
                                    <td style="font-size: 12px; width: 125px">--</td>
                                    <td style="font-size: 12px; width: 115px">
                                        <div>
                                            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="#7CC504" x="188.5" y="188.5" role="img" style="display:inline-block;vertical-align:middle" xmlns="http://www.w3.org/2000/svg"><g fill="#7CC504"><path fill="#7CC504" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm4.768 9.14a1 1 0 1 0-1.536-1.28l-4.3 5.159l-2.225-2.226a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.475-.067l5-6Z" clip-rule="evenodd"/></g></svg>
                                        </div>
                                    </td>
                                    </tr>
                                `
                            }
                        }else {
                            table +=
                                `
                                <td style="font-size: 12px; width: 125px">--</td>
                                <td style="font-size: 12px; width: 115px">
                                    <div>
                                        <svg width="20px" height="20px" viewBox="0 0 24 24" fill="#c0c0c0" x="188.5" y="188.5" role="img" style="display:inline-block;vertical-align:middle" xmlns="http://www.w3.org/2000/svg"><g fill="#c0c0c0"><path fill="#c0c0c0" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm4.768 9.14a1 1 0 1 0-1.536-1.28l-4.3 5.159l-2.225-2.226a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.475-.067l5-6Z" clip-rule="evenodd"/></g></svg>
                                    </div>
                                </td>
                                </tr>
                                `
                        }
                    }

                    table +=
                        `
                        </tbody>
                        </table>
                        </div>
                        </div>
                        `

                    mainContainer += table
                    mainContainer = `<div class="panel panel-default">` + mainContainer + `</div>`

                    body.innerHTML += mainContainer
                }
            },
            error: function () {
                console.log('error')
            }
        })
    })
})