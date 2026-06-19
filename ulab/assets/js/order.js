$(function ($) {
	$('#LONGTERM').change(function () {
		if ($(this).prop('checked')) {
			$('#payment').removeClass('visually-hidden')
			$('#contract_summ').prop('disabled', false)
		} else {
			$('#payment').addClass('visually-hidden')
			$('#contract_summ').prop('disabled', true)
		}
	})

	$('#CLIENT_NUMBER').change(function () {
		if ($(this).prop('checked')) {
			$('input[name=CONTRACT_TYPE]').prop('readonly', false)
			$('input[name=NUMBER]').prop('readonly', false)
			$('input[name=DATE]').prop('readonly', false)
		} else {
			$('input[name=CONTRACT_TYPE]').prop('readonly', true)
			$('input[name=NUMBER]').prop('readonly', true)
			$('input[name=DATE]').prop('readonly', true)
		}
	})

	$('body').find('.popup-with-form').magnificPopup({
		type: 'inline',
		closeBtnInside:true,
		fixedContentPos: false
	})

	$('.upload_pdf').change(function () {
		let docId = $(this).data('tz_doc_id')
		let dogovorId = $(this).data('dogovor_id')
		let tzId = $(this).data('tz_id')
		let file = $(this)[0].files[0]

		let formData = new FormData()

		if ( tzId !== undefined ) {
			formData.append("tz_id", tzId)
		}
		if ( docId !== undefined ) {
			formData.append("doc_id", docId)
		}
		if ( dogovorId !== undefined ) {
			formData.append("dogovor_id", dogovorId)
		}
		formData.append("file", file, file.name)
		formData.append("upload_file", true)


		$.ajax({
			url: '/ulab/order/uploadTzDocPdfAjax/',
			data: formData,
			method: 'POST',
			contentType: false,
			processData: false,
			cache: false,
			async: false,
			success: function (json) {
				location.reload()
			},
			error: function () {
				console.log('error')
			}
		})
	})

	$('body').on('change', '.dog-type',function () {
		console.log($('.dog-type'))

		$('.dog-type').attr('checked', false)

		console.log($('.dog-type'))

		$(this).attr('checked', true)
	})

	$('.upload-document').change(function () {
		let dogovorId = $(this).data('dogovor_id')
		let typeId = $(this).data('type_id')
		let isScan = $(this).data('is_scan')
		let file = $(this)[0].files[0]

		let formData = new FormData()

		formData.append("dogovor_id", dogovorId)
		formData.append("type_id", typeId)
		formData.append("is_scan", isScan)
		formData.append("file", file, file.name)
		formData.append("upload_file", true)

		if(typeId === 3 && isScan === 0) {
			$.ajax({
				url: '/ulab/order/deleteScanDogovor/',
				data: {
					dogovor_id: dogovorId
				},
				method: 'POST',
				success: function (json) {
					$.ajax({
						url: '/ulab/order/uploadDogovorDocs/',
						data: formData,
						method: 'POST',
						contentType: false,
						processData: false,
						cache: false,
						async: false,
						success: function (json) {
							location.reload()
						},
						error: function () {
							console.log('Upload error')
						}
					})
				},
				error: function () {
					console.log('Delete error')

					location.reload()
				}
			})
		}else {
			$.ajax({
				url: '/ulab/order/uploadDogovorDocs/',
				data: formData,
				method: 'POST',
				contentType: false,
				processData: false,
				cache: false,
				async: false,
				success: function (json) {
					location.reload()
				},
				error: function () {
					console.log('error')
				}
			})
		}
	})


	let journalDataTable = $('#journal_order').DataTable({
		destroy : true,
		retrieve: true,
		bAutoWidth: false,
		autoWidth: false,
		fixedColumns: false,
		processing: true,
		serverSide: true,
		bSortCellsTop: true,
		scrollX: true,
		fixedHeader: false,
		colReorder: true,
		ajax: {
			type : 'POST',
			data: function ( d ) {
				d.stage = $('#selectStage option:selected').val()
				d.order_id = $('#input_order_id').val()
			},
			url : '/ulab/order/getJournalRequestAjax/',
			dataSrc: function (json) {
				return json.data
			},
		},
		columns: [
			{
				data: 'requestTitle',
				class: 'text-nowrap',
				render: function (data, type, item) {
					return `<a class="request-link"
						   href="/ulab/request/card/${item.ID_Z}" target="_blank">
						   ${item['REQUEST_TITLE']}
						</a>`
				}
			},
			{
				data: 'date',
				width: 100,
			},
			{
				data: 'price_discount',
			},
			{
				data: 'ACCOUNT',
			},
			{
				data: 'ACTUAL_VER',
				orderable: false,
				render: function (data, type, item) {
					if ( !item['ACTUAL_VER'] ) {
						return `-`
					} else {
						return `<a href="/protocol_generator/archive_tz/${item['ID']}/${item['ACTUAL_VER']}.pdf">${item['ID']}</a>`
					}
				}
			},
			{
				data: 'tz_pdf',
				orderable: false,
				render: function (data, type, item) {
					if ( !item['tz_pdf'] ) {
						return `<label class="upload-pdf cursor-pointer nav-link link-card"
									title="Загрузить PDF-версию">
								<svg class="icon" width="25" height="25">
									<use xlink:href="/ulab/assets/images/icons.svg#upload"/>
								</svg>
							
								<input class="d-none upload_pdf" data-tz_id="${item['ID']}"
									data-tz_doc_id="${item['tz_doc_id']}" type="file" name="upload_pdf">
								</label>`
					} else {
						return `
								<a href="/protocol_generator/archive_tz/${item['ID']}/${item['tz_pdf']}">${item['tz_pdf']}</a>  
								<a data-tz_doc_id="${item['tz_doc_id']}" class="del-tz-pdf"><i class="fa-solid fa-xmark"></i></a>`
					}
				}

			},
			{
				data: 'DISCOUNT',
				render: function (data, type, item) {
					if ( !item['DISCOUNT'] ) {
						return `-`
					}

					return `${item['DISCOUNT']} %`
				}
			},
			{
				data: 'OPLATA',
			},
			{
				data: 'button',
				orderable: false,
				render: function (data, type, item) {
					if ( !item['ACCOUNT'] ) {
						return `<a href="#" class="btn btn-primary disabled">Нет счета</a>`
					}
					const p = parseFloat(item['price_discount']).toFixed(2)
					const o = parseFloat(item['OPLATA']).toFixed(2)
					if ( p === o ) {
						return `<a href="#" class="btn btn-primary disabled">Оплачено</a>`
					}
					if ( p < o ) {
						return `<a href="#" class="btn btn-primary disabled">Переплата</a>`
					}

					if ( !item['is_show_finance'] ) {
						return `<a href="#" class="btn btn-primary disabled">Нет прав</a>`
					}

					return `<a href="#add-payment" data-deal_id="${item['ID_DEAL']}" data-pay="${item['OPLATA']}" data-price="${item['price_discount']}" class="btn btn-primary popup-with-form">Оплатить</a>`
				}
			},
		],
		language: dataTablesSettings.language,
		lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
		pageLength: 25,
		order: [ [ 0, 'asc' ] ],
		dom: 'frtB<"bottom"lip>',
		buttons: dataTablesSettings.buttons,
	})


	journalDataTable.on('click', '.del-tz-pdf', function () {
		let docId = $(this).data('tz_doc_id')
		if (confirm('Вы действительно желаете удалить загруженный файл?')) {

			$.ajax({
				url: '/ulab/order/delTzDocAjax/',
				data: {
					id: docId
				},
				method: 'POST',
				success: function (json) {
					journalDataTable.ajax.reload()
					journalDataTable.draw()
				},
				error: function () {
					console.log('error')
				}
			})
		}
	})


	journalDataTable.on('change', '.upload_pdf', function () {
		console.log('ok')
		let docId = $(this).data('tz_doc_id')
		let dogovorId = $(this).data('dogovor_id')
		let tzId = $(this).data('tz_id')
		let file = $(this)[0].files[0]

		let formData = new FormData()

		if ( tzId !== undefined ) {
			formData.append("tz_id", tzId)
		}
		if ( docId !== undefined && docId !== null ) {
			formData.append("doc_id", docId)
		}
		if ( dogovorId !== undefined ) {
			formData.append("dogovor_id", dogovorId)
		}
		formData.append("file", file, file.name)
		formData.append("upload_file", true)


		$.ajax({
			url: '/ulab/order/uploadTzDocPdfAjax/',
			data: formData,
			method: 'POST',
			contentType: false,
			processData: false,
			cache: false,
			async: false,
			success: function (json) {
				console.log('ok2')
				journalDataTable.ajax.reload()
				journalDataTable.draw()
			},
			error: function () {
				console.log('error')
			}
		})
	})


	journalDataTable.on('click', '.popup-with-form', function () {
		const dealId = $(this).data('deal_id')
		const pay = $(this).data('pay')
		const price = $(this).data('price')
		const need = parseFloat(price) - parseFloat(pay)
		const finance = parseFloat($('#finance').val())

		$.magnificPopup.open({
			items: {
				src: '#add-payment',
			},
			type: 'inline',
			closeBtnInside: true,
			fixedContentPos: false,
			callbacks: {
				beforeOpen: function() {
					if ( !isNaN(need) ) {
						$('#add-payment').find('#deal_id_input').val(dealId)
						$('#add-payment').find('#need-input').val(need)
						$('#add-payment').find('#money-input').val(Math.min(need, finance))
					}
				}
			}
		})
	})

	journalDataTable.columns().every( function () {
		$(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
			journalDataTable
				.column( $(this).parent().index() )
				.search( this.value )
				.draw()
		})
	})

	$('.filter').on('change', function () {
		journalDataTable.ajax.reload()
		journalDataTable.draw()
	})

	$('#payment_select').on('change', function () {
		let payment_id = $(this).val()
		let id = $('#input_order_id').val()

		$.ajax({
			url: '/ulab/order/setPaymentTypeAjax/',
			data: {
				id: id,
				payment_id: payment_id
			},
			method: 'POST',
			success: function (json) {
			},
			error: function () {
			}
		})
	})

	$('#dogovor_price').on('change', function () {
		let dogovor_price = $(this).val()
		let id = $('#input_order_id').val()

		$.ajax({
			url: '/ulab/order/setDogovorPrice/',
			data: {
				id: id,
				dogovor_price: dogovor_price
			},
			method: 'POST',
			success: function (json) {
			},
			error: function () {
			}
		})
	})

	$('#date-end').on('change', function () {
		let date_end = $(this).val()
		let id = $('#input_order_id').val()

		$.ajax({
			url: '/ulab/order/setDogovorEnd/',
			data: {
				id: id,
				date_end: date_end
			},
			method: 'POST',
			success: function (json) {
			},
			error: function () {
			}
		})
	})

	$('#special-notes').on('change', function () {
		let special_notes = $(this).val()
		let id = $('#input_order_id').val()

		$.ajax({
			url: '/ulab/order/setSpecialNotes/',
			data: {
				id: id,
				special_notes: special_notes
			},
			method: 'POST',
			success: function (json) {
			},
			error: function () {
			}
		})
	})

	$('#date-end').on('click', function () {
		$(this).attr('type', 'date')
	})

	$('#date-end').on('blur', function () {
		let dateValue = $(this).val()

		if(!dateValue) {
			$(this).attr('type', 'text')
		}
	})

	$('#open-doc-modal').on('click', function () {
		$.magnificPopup.open({
			items: {
				src: '#doc-modal-form',
			},
			type: 'inline',
			closeBtnInside: true,
			fixedContentPos: false
		})
	})

	$('.pages a').on('click', function (e) {
		e.preventDefault()

		$(this).tab('show')
	})

	$('.delete-document').on('click', function () {
		let docId = $(this).data('document_id')

		$.ajax({
			url: '/ulab/order/removeDoc/',
			data: {
				id: docId,
			},
			method: 'POST',
			success: function (json) {
				location.reload()
			},
			error: function () {
				location.reload()
			}
		})
	})

	$('.change-need').on('click', function () {
		let typeId = $(this).data('type_id')
		let value = $(this).data('need_doc')

		let id = $('#input_order_id').val()

		let blockButtons = $(this).parent().parent().find('.block-buttons')
		let blockCalc = $(this).parent().parent().find('.calc-count')

		let newValue = 0

		if(value === 1) {
			$(this).removeClass('nav-fill-active')
			$(this).addClass('nav-outline-active')

			blockButtons.addClass('visually-hidden')
			blockCalc.addClass('visually-hidden')
		}else {
			newValue = 1

			$(this).removeClass('nav-outline-active')
			$(this).addClass('nav-fill-active')

			blockButtons.removeClass('visually-hidden')
			blockCalc.removeClass('visually-hidden')
		}

		$(this).data('need_doc', newValue)

		$.ajax({
			url: '/ulab/order/changeNeedlDocForDogovor/',
			data: {
				type_id: typeId,
				new_value: newValue,
				dogovor_id: id,
			},
			method: 'POST',
			success: function (json) {
			},
			error: function () {
			}
		})
	})

	$('.change-count-additional').on('click', function () {
		let action = $(this).data('count_plus')
		let currentValue = $('.additional-count').val()

		let expression = currentValue + '+' + action
		let result = eval(expression)

		if(Number(result) <= 0) {
			result = 1
		}

		$('.additional-count').val(result)
	})

	$('.sended-doc').on('click', function () {
		let dogovorId = $(this).data('dogovor_id')
		let typeId = $(this).data('type_id')
		let isScan = $(this).data('is_scan')

		$.ajax({
			url: '/ulab/order/sendDocToClient/',
			data: {
				dogovor_id: dogovorId,
				is_scan: isScan,
				type_id: typeId,
			},
			method: 'POST',
			success: function (json) {
				location.reload()
			},
			error: function () {
				location.reload()
			}
		})
	})

	$('.delete-send-document').on('click', function () {
		let documentId = $(this).data('document_id')

		$.ajax({
			url: '/ulab/order/deleteSendedDocs/',
			data: {
				document_id: documentId
			},
			method: 'POST',
			success: function (json) {
				location.reload()
			},
			error: function () {
				location.reload()
			}
		})
	})

	$('#is_custom').on('change', function () {
		let val = $(this).is(':checked')

		if(val) {
			$('.NUMBER_EDIT').removeAttr('readonly')
			$('.DATE_EDIT').removeAttr('readonly')
		}else {
			$('.NUMBER_EDIT').attr('readonly', 'true')
			$('.DATE_EDIT').attr('readonly', 'true')
		}
	})

	$(".type-dogovor").on('change', function () {
		let currentName = $(this).data('jsName')

		if(currentName === 'Договор клиента') {
			$('.CONTRACT_TYPE').removeAttr('readonly')
		}else {
			$('.CONTRACT_TYPE').attr('readonly', 'true')
		}

		$('.CONTRACT_TYPE').val(currentName)
	})
})
