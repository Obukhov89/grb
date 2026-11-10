$(function ($) {
    const $journalMaterial = $('#journal_material_2')
    const dealId = $('#deal_id').val()
    let extraColsVisible = loadExtraColsState()
    let journalDataTable = $journalMaterial.DataTable({
        bAutoWidth: false,
        autoWidth: false,
        fixedColumns: false,
        processing: true,
        serverSide: true,
        bSortCellsTop: true,
        fixedHeader: false,
        colReorder: false,
        responsive: true,
        stateSave: false,
        stateDuration: -1,
        drawCallback: function () {
            applyExtraColsState(extraColsVisible);
        },

        initComplete: function () {
            applyExtraColsState(extraColsVisible);
        },
        ajax: {
            type: 'POST',
            data: function (d) {
                d.deal_id = dealId
            },
            url: '/ulab/probe/getMaterialProbeToRequestAjax2/',
            dataSrc: function (json) {
                const materials = json.data || {}
                console.log(json)
                const rows = []

                for (const [materialId, materialBlock] of Object.entries(materials)) {
                    const probes = materialBlock.probe || {}

                    for (const [probeId, probe] of Object.entries(probes)) {
                        rows.push({
                            material_id: +materialId,
                            id: +probeId,
                            ...probe
                        })
                    }
                }

                return rows
            }
        },
        columns: [
            {
                data: 'cipher',
                orderable: false,
                render: function (data, type, item) {
                    const cipherVal = String(item.cipher ?? '');
                    const cipherDisplay = cipherVal !== '' ? cipherVal : '—';

                    if (type !== 'display') return cipherDisplay;

                    if (parseInt(item.is_in_act, 10) === 0) {
                        return `
        <div class="d-flex flex-column gap-1 align-items-start">
          <strong class="js-cipher-text">${cipherDisplay}</strong>

          <input type="hidden"
                 name="probe[${item.id}][cipher]"
                 class="js-cipher-input"
                 value="">

          <input type="hidden"
                 name="probe[${item.id}][cipher_assigned]"
                 class="js-cipher-assigned"
                 value="0">

          <button type="button"
                  class="btn btn-outline-success btn-sm js-assign-cipher"
                  title="Присвоить шифр по порядковому номеру в списке">
            Присвоить шифр
          </button>

          <span class="text-muted small js-assign-status"></span>
        </div>
      `;
                    }

                    return `
      <div class="form-check">
        <input class="form-check-input filter-method filter-probe-id probe-check material-id-${item.material_id}"
               type="checkbox"
               data-material_id="${item.material_id}"
               value="${item.id}"
               id="proba_name_${item.id}">

        <a href="#" title="Редактировать" data-id="${item.id}" class="edit_probe">
          <strong>${cipherDisplay}</strong>
        </a>
      </div>

      <input type="hidden"
             name="probe[${item.id}][cipher]"
             class="js-cipher-input"
             value="${cipherVal}">
    `;
                }
            },
            {
                data: 'mg_name',
                orderable: false,
                render: function (data, type) {
                    if (data === null || data === undefined) return '-';
                    const s = String(data).trim();
                    if (s === '' || s.toLowerCase() === 'null') return '-';
                    return s;
                }
            },
            {
                data: 'name_for_protocol',
                orderable: false,
                render: function (data, type) {
                    if (data === null || data === undefined) return '-';
                    const s = String(data).trim();
                    if (s === '' || s.toLowerCase() === 'null') return '-';
                    return s;
                }
            },
            {
                data: 'place',
                orderable: false,
                className: 'js-extra-col d-none',
                render: function (data, type, item) {
                    return `<input type="text"
                            class="form-control form-control-sm place-probe" style="width: 440px"
                            name="probe[${item['id']}][place]"
                            value="${item['place']}">
                        `;
                }
            },
            {
                data: 'date_probe',
                orderable: false,
                className: 'js-extra-col d-none',
                render: function (data, type, item) {
                    return `                                <input type="date"
                                       class="form-control form-control-sm date-probe"
                                       name="probe[${item['id']}][date_probe]"
                                       value="${item['date_probe']}">`
                }
            },
            {
                data: 'quarry',
                orderable: false,
                className: 'js-extra-col d-none',
                render: function (quarries, type, item, meta) {

                    const selectedId = item.quarry_id ? String(item.quarry_id) : '';

                    let options = `<option value="">—</option>`;
                    (Array.isArray(quarries) ? quarries : []).forEach(q => {
                        const qId = String(q.ID);
                        const sel = (selectedId !== '' && qId === selectedId) ? ' selected' : '';
                        options += `<option value="${qId}"${sel}>${q.NAME}</option>`;
                    });

                    return `
        <select class="form-select form-select-sm quarry-probe"
                style="width: 97px"
                name="probe[${item.id}][quarry_id]">
          ${options}
        </select>`;
                }
            },
            {
                data: 'lab_name',
                orderable: false,
            },
            {
                data: 'is_in_act',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    if (String(data) === '1' || data === 1) {
                        return `<a href="#" data-id="${item.id}" class="history_probe">
                <i class="fa-regular fa-clock"></i>
              </a>`;
                    }
                    return '';
                }
            },
            {
                data: 'state',
                orderable: false,
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (_data, _type, item) {
                    const id = item.id;
                    const URI = window.APP_URI || '';

                    const isHandOver = item.is_hand_over === true || item.is_hand_over === 1 || item.is_hand_over === '1';

                    if (isHandOver) {
                        if (item.state === 'КМО') {
                            return `<a href="/ulab/probe/transferProbe/${id}">Передать</a>`;
                        }
                        return `<a href="#" class="disabled" onclick="return false;">Передано</a>`;
                    }

                    const st = parseInt(item.user_status, 10);

                    if (st === -1) return '-';
                    if (st === 1) return `<a href="#" class="disabled" onclick="return false;">Принято</a>`;

                    return `<a href="/ulab/probe/takeProbe/${id}">Принять</a>`;
                }
            },
            {
                data: 'lab_info',
                orderable: false,
                className: 'text-center',
                render: function (labs, type, item) {
                    if (type !== 'display') return '';
                    let currentUserId = BX.message("USER_ID");
                    if (currentUserId != 156) return '';
                    if (!Array.isArray(labs) || labs.length === 0) {
                        return '<span class="text-muted"></span>';
                    }

                    const html = labs.map(lab => {
                        const shortName = lab.short_name ?? '';
                        const labId = Number(lab.ID);
                        const probeId = Number(item.id);

                        if (!lab.date_send) {
                            return `
                              <a href="#"
                                 class="send-probe-lab"
                                 data-lab-name="${shortName}"
                                 data-lab-id="${labId}"
                                 data-probe-id="${probeId}">
                                Отметить готовность ${shortName}
                              </a>`;
                        }
                        return `<span>Проба для ${shortName} готова</span>`;
                    }).join('');

                    return `<div class="d-flex flex-column gap-1 align-items-start">${html}</div>`;
                }
            },
            {
                data: 'irrevocable',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    let currentUserId = BX.message("USER_ID");
                    if (currentUserId != 61) return '';

                    const isChecked = String(item.irrevocable) === '1' || item.irrevocable === 1;

                    return `
                  <input class="irrevocable"
                         key="${item.id}"
                         type="checkbox"
                         name="irrevocable"
                         value="${isChecked ? '1' : '0'}"
                         ${isChecked ? 'checked' : ''}>
                `;
                }
            },

        ],
        createdRow: function (row, data, dataIndex) {
            const api = this.api();
            const start = api.page.info().start;
            const rownum = start + dataIndex + 1;

            $(row).attr('data-rownum', rownum);
        },
        drawCallback: function () {
            const api = this.api();

            const rowNodes = api.rows({ page: 'current' }).nodes();
            const rowData  = api.rows({ page: 'current' }).data();

            let lastMaterialId = null;

            rowData.each(function (d, i) {
                if (lastMaterialId !== d.material_id) {
                    $(rowNodes).eq(i).before(`
                    <tr class="material-group-row">
                      <td colspan="100%">
                        <div class="form-check">
                          <input class="form-check-input material-check material-check-name"
                                 type="checkbox"
                                 data-material_id="${d.material_id}"
                                 id="material_name_${d.material_id}">
                          <label class="form-check-label" for="material_name_${d.material_id}">
                            <strong>${d.material_name}</strong>
                          </label>
                        </div>
                      </td>
                    </tr>
                `);
                    lastMaterialId = d.material_id;
                }
            });
        },
        language: dataTablesSettings.language,
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
        pageLength: 25,
        order: [[ 0, "asc" ]],
        dom: 'frt<"bottom"lip>',
        buttons: dataTablesSettings.buttons,
    })
    let $body = $('body')

    // ---------------------------
    // Доп. колонки свернуть/развернуть
    // ---------------------------
    function getExtraColsStorageKey() {
        const $form = $('#act-probes-form')
        const userId = String($form.data('user-id') || '0')
        return 'act_extra_cols_visible_user_' + userId
    }

    $body.on('change', '.material-check', function () {
        let materialId = $(this).data('material_id')
        $body.find(`.material-id-${materialId}`).prop("checked", $(this).prop("checked"))
    })

    $body.on('change', '.material-check-all', function () {
        $body.find(`.material-check-name`).prop("checked", $(this).prop("checked"))
        $body.find(`.probe-check`).prop("checked", $(this).prop("checked"))
    })

    $('#place_all').on('change', function (){
        let place = $(this).val()
        $('.place-probe').val(place).trigger('change')
    })

    $('#date_all').on('change', function (){
        let date = $(this).val()
        $('.date-probe').val(date).trigger('change')
    })

    $('#quarry_all').on('change', function (){
        let quarry = $(this).val()
        $(`.quarry-probe option[value="${quarry}"]`).prop('selected', true).trigger('change');
    })

    function applyExtraColsState(visible) {
        $('.js-extra-col').toggleClass('d-none', !visible)
    }

    function loadExtraColsState() {
        try {
            const key = getExtraColsStorageKey()
            const v = localStorage.getItem(key)
            // по умолчанию: скрыто (как у вас сейчас d-none в шаблоне)
            return v === '1'
        } catch (e) {
            return false
        }
    }

    function saveExtraColsState(visible) {
        try {
            const key = getExtraColsStorageKey()
            localStorage.setItem(key, visible ? '1' : '0')
        } catch (e) {}
    }

// применяем сохранённое состояние сразу при загрузке
    applyExtraColsState(loadExtraColsState())

    $body.on('click', '#toggle-extra-cols', function () {
        const isCurrentlyVisible = !$('.js-extra-col').first().hasClass('d-none')
        const nextVisible = !isCurrentlyVisible

        applyExtraColsState(nextVisible)
        saveExtraColsState(nextVisible)

        return false
    })

    // ---------------------------
    // Присвоить шифр по порядковому номеру в списке
    // cipher = ACT_NUM . '.' . rownum . '/' . yearPart
    // yearPart: как у вас в PHP:
    //   если year % 10 != 0 => последние 2 цифры
    //   иначе => полный год (например 2020, 2030)
    // ---------------------------
    function parseActYear(actDateStr) {
        // actDateStr может быть "YYYY-MM-DD" или "DD.MM.YYYY" или любая строка, где есть год
        if (!actDateStr) return null

        // YYYY-MM-DD
        if (/^\d{4}-\d{2}-\d{2}/.test(actDateStr)) {
            const y = parseInt(actDateStr.slice(0, 4), 10)
            return isNaN(y) ? null : y
        }

        // DD.MM.YYYY
        if (/^\d{2}\.\d{2}\.\d{4}$/.test(actDateStr)) {
            const parts = actDateStr.split('.')
            const y = parseInt(parts[2], 10)
            return isNaN(y) ? null : y
        }

        // fallback: ищем 4 цифры года
        const m = actDateStr.match(/(\d{4})/)
        if (m && m[1]) {
            const y = parseInt(m[1], 10)
            return isNaN(y) ? null : y
        }

        return null
    }

    function makeYearPart(year) {
        if (!year) return ''
        return (year % 10 !== 0) ? String(year).slice(-2) : String(year)
    }

    $body.on('click', '.js-assign-cipher', function () {
        const $form = $('#act-probes-form')
        const actNum = String($form.data('act-num') || '').trim()
        const actDate = String($form.data('act-date') || '').trim()

        if (!actNum) {
            alert('Не найден номер акта (act_number).')
            return false
        }

        const year = parseActYear(actDate)
        if (!year) {
            alert('Не удалось определить год из даты акта: ' + actDate)
            return false
        }

        const yearPart = makeYearPart(year)

        const $tr = $(this).closest('tr')
        const rownum = parseInt($tr.data('rownum'), 10)

        if (!rownum || rownum <= 0) {
            alert('Не найден порядковый номер строки (rownum).')
            return false
        }

        const cipher = actNum + '.' + (rownum) + '/' + yearPart

        $tr.find('.js-cipher-input').val(cipher)
        $tr.find('.js-cipher-assigned').val(1)   // <-- ВАЖНО
        $tr.find('.js-cipher-text').text(cipher)
        $tr.find('.js-assign-status').text('Назначено: ' + cipher)

        return false
    })

    // ---------------------------
    // Ваш текущий функционал (оставлен как есть)
    // ---------------------------

    $body.on('click', '.edit_probe', function () {
        const id = $(this).data('id')
        const $form = $('#edit-modal-form')

        $.ajax({
            url: "/ulab/probe/getAjax/",
            data: {"id": id},
            dataType: "json",
            method: "POST",
            success: function (data) {
                console.log(data)
                $form.find('.probe_id').val(id)
                $form.find('.name_for_protocol').val(data.name_for_protocol)
                $form.find('.probe_place').val(data.place)
                $form.find('.probe_date').val(data.date_probe)
                $form.find('.probe_quarry_id').val(data.quarry_id)

                $.magnificPopup.open({
                    items: {
                        src: '#edit-modal-form',
                        type: 'inline',
                    },
                    fixedContentPos: false,
                })
            }
        })

        return false
    })

    $body.on('change', '.selection-type', function () {
        const id = $(this).data('id')
        let val = $(this).prop('checked')

        $.ajax({
            url: "/ulab/probe/changeSelectionTypeAjax/",
            data: {"id": id, "checked": val},
            dataType: "json",
            method: "POST",
            success: function (data) {
                console.log(data)
            }
        })

        return false
    })

    $body.on('click', '.history_probe', function () {
        const id = $(this).data('id')
        const $form = $('#history-modal-form')

        $form.find('.cipher').empty()
        $form.find('.history-info').empty()

        $.ajax({
            url: "/ulab/probe/getHistoryAjax/",
            data: {"id": id},
            dataType: "json",
            method: "POST",
            success: function (data) {
                console.log(data)

                $form.find('.cipher').text(data.info.cipher)

                let html = ``

                $.each(data.history, function (i, item) {
                    html +=
                        `<div class="row">
                            <div class="col">${item.date}</div>
                            <div class="col">${item.action}</div>
                            <div class="col">${item.short_name}</div>
                        </div>`
                })

                if (html === '') {
                    html = `У пробы еще нет истории`
                }

                $form.find('.history-info').html(html)

                $.magnificPopup.open({
                    items: {
                        src: '#history-modal-form',
                        type: 'inline',
                    },
                    fixedContentPos: false,
                })
            }
        })

        return false
    })

    $body.on('click', '.send-probe-lab', function () {
        let probeId = $(this).data('probe-id')
        let labId = $(this).data('lab-id')
        let labName = $(this).data('lab-name')
        let currentApprove = $(this)

        $.ajax({
            url: `/ulab/probe/sendProbe/${probeId}`,
            data: { 'lab_id': labId },
            dataType: "json",
            method: "POST",
            success: function (data) {
                // при желании можно обработать success
            },
            error: function (e) {
                currentApprove.replaceWith(function () {
                    return $('<span>').html(`Проба для ${labName} готова`)
                })
            }
        })
    })

    $body.on('click', '.irrevocable', function () {
        let val = 0
        if ($(this).prop('checked')) {
            val = 1
        }

        $.ajax({
            url: "/ulab/probe/updateProbeAjax/",
            data: {
                "id": $(this).attr('key'),
                "val": val,
                "field": 'irrevocable',
            },
            dataType: "json",
            method: "POST",
            success: function (json) {
                console.log(json)
            },
            error: function (json) {
                console.log(json.error)
            }
        })
    })
    journalDataTable.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalDataTable
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

        journalDataTable.ajax.reload()
        journalDataTable.draw()
    })

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $('#journal_material_2').width()

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

    $(document).scroll(function () {
        let maxScroll = $(document).height() - $(window).height();
        let scrollTop = $(window).scrollTop();

        let bottomInt = (10 + maxScroll - scrollTop - 100) + "px";
        let bottomText = (10 + maxScroll - scrollTop - 100);

        if (scrollTop >= maxScroll - 100) {
            $(".btn-group-toolbar").css("bottom", "10px")
        } else {
            $(".btn-group-toolbar").css("bottom", bottomText)
        }

    })
})