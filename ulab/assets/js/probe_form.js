let quarryList = null

/**
 *
 * @param quarryList
 * @param defaultId
 * @returns {string}
 */
function getHtmlOptionsQuarry(quarryList, defaultId = 0) {
	let options = '<option value="">--/--</option>'

	$.each(quarryList, function (i, item) {
		let selected = ``

		if ( item.ID == defaultId ) {
			selected = 'selected'
		}

		options += `<option value="${item.ID}" ${selected}>${item.NAME}</option>`
	})

	return options
}

$(function ($) {

	$.ajax({
		method: 'POST',
		async: false,
		url: '/ulab/requirement/getQuarryAjax',
		dataType: 'json',
		success: function (data) {
			quarryList = data
		}
	})

	const $journal = $('#journal_material_2')

	$('.select2').select2({
		theme: 'bootstrap-5',
	})

	$('#place_all').on('change', function (){
		let place = $(this).val()
		$('.place').val(place).trigger('change')
	})

	$('#date_all').on('change', function (){
		let date = $(this).val()
		$('.date').val(date).trigger('change')
	})

	$('#quarry_all').on('change', function (){
		let quarry = $(this).val()
		$(`.quarry option[value="${quarry}"]`).prop('selected', true).trigger('change');
	})

	let journalDataTable = $journal.DataTable({
		bAutoWidth: false,
		autoWidth: false,
		fixedColumns: false,
		processing: true,
		serverSide: true,
		bSortCellsTop: true,
		scrollX: true,
		fixedHeader: false,
		colReorder: true,
		stateSave: true,
		stateDuration: -1,
		ajax: {
			type : 'POST',
			data: function ( d ) {
				d.deal_id = $('#deal_id').val()
			},
			url : '/ulab/probe/getMaterialProbeToRequestAjax/',
			dataSrc: function (json) {
				return json.data
			}
		},
		columns: [
			{
				data: 'umtr_id',
				orderable: false,
				render: function(data, type, item) {
					return `
					<div>
						<label class="switch">
							<input class="form-check-input" name="act[probe][umtr_id][]" type="checkbox" value="${item.umtr_id}">
							<span class="slider"></span>
						</label>
					</div>
					`
				}
			},
			{
				data: 'n',
				orderable: false,
			},
			{
				data: 'material_name',
			},
			{
				data: 'name_for_protocol',
				className: 'save-cell name_for_protocol',
				render: function (data, type, item) {
					return `<input type="text" class="form-control" name="act[probe][${item.id}][name_for_protocol]" value="${item.name_for_protocol.replace(/"/g, '&quot;')}">`
				}
			},
			{
				data: 'place',
				className: 'save-cell place',
				render: function (data, type, item) {
					return `<input type="text" class="form-control place" name="act[probe][${item.id}][place]" value="${item.place.replace(/"/g, '&quot;')}">`
				}
			},
			{
				data: 'date_probe',
				className: 'save-cell date_probe',
				render: function (data, type, item) {
					return `<input type="date" class="form-control date" name="act[probe][${item.id}][date_probe]" value="${item.date_probe}">`
				}
			},
			{
				data: 'quarry_id',
				className: 'save-cell quarry_id',
				render: function (data, type, item) {
					return `<select class="form-control select2" name="act[probe][${item.id}][quarry_id]">` + getHtmlOptionsQuarry(quarryList, item.quarry_id) + `</select>`
				}
			},
		],
		language: dataTablesSettings.language,
		lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
		pageLength: 25,
		order: [[ 1, "asc" ]],
		dom: 'frtB<"bottom"lip>',
		buttons: dataTablesSettings.buttons,
	}).on('draw', function() {
		$(this).find('.select2').select2({
			theme: 'bootstrap-5',
		})
	}).on('change', 'td.save-cell select, td.save-cell input', function () {
		let $thisCell = $(this).closest('td')
		let $thisRow = $(this).closest('tr')
		let val = $(this).val()
		let data = journalDataTable.row($thisRow.closest('tr')).data()
		let cellName = ''

		console.log(data)

		if ( $thisCell.hasClass('name_for_protocol') ) {
			cellName = 'name_for_protocol'
		} else if ( $thisCell.hasClass('place') ) {
			cellName = 'place'
		} else if ( $thisCell.hasClass('date_probe') ) {
			cellName = 'date_probe'
		} else if ( $thisCell.hasClass('quarry_id') ) {
			cellName = 'quarry_id'
		} else {
			return false
		}

		$.ajax({
			url: "/ulab/probe/updateProbeAjax/",
			data: {
				"id": data.id,
				"val": val,
				"field": cellName,
			},
			dataType: "json",
			method: "POST",
			success: function (json) {
				if ( json.success ) {
					journalDataTable.ajax.reload()
					journalDataTable.draw()
				} else {
					alert(`При обновлении возникла ошибка: ${json.error}`)
				}
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
		$('#journal_filter').addClass('is-open')
		$('.filter-btn-search').hide()
	})

	$('.filter').on('change', function () {
		journalDataTable.ajax.reload()
		journalDataTable.draw()
	})

	$('.filter-btn-reset').on('click', function () {
		location.reload()
	})
})
