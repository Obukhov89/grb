$(function ($) {
	let body = $('body')

	body.on('change', '.date', function () {
		let dateIn = $('#inputDateStart').val(),
			dateOut = $('#inputDateEnd').val()

		$.ajax({
			method: 'POST',
			url: '/ulab/statistic/getReportByUsersAjax',
			data: {
				dateIn: dateIn,
				dateOut: dateOut
			},
			dataType: 'json',
			success: function(data) {
				let row = '',
					table = $('#journal_users')
				$('#journal_users tbody tr').remove()
				$.each(data, function (i, v) {

					row = `<tr>
								<td>${v.department_name}</td>
								<td>${v.short_name}</td>
								<td class="text-center">${v.new_deal ?? 0}</td>
								<td class="text-center">${v.reject_deal ?? 0}</td>
								<td class="text-center">${v.won_deal ?? 0}</td>
								<td class="text-center">${v.with_act_deal ?? 0}</td>
								<td class="text-center">${v.full_pay_deal ?? 0}</td>
								<td class="text-center">${v.partyally_pay_deal ?? 0}</td>
								<td class="text-center">${v.protocol ?? 0}</td>
								<td class="text-center">${v.protocol_num ?? 0}</td>
							</tr>`
					table.append(row)
				})
			}
		})
	})

	$('.saveProtocolRadiology').on('change', function () {
		let form = $(this).parents('form')
		form.trigger('submit')
	})

	$('.date_radiology').on('blur', function () {
		let id = $(this).parent('td').find('.id').val(),
			date = $(this).val()
		if (date != '') {
			$.ajax({
				method: 'POST',
				url: '/ulab/statistic/setRadiologyDate',
				data: {
					id: id,
					date: date
				},
				dataType: 'json',
				success: function(data) {
					if ( data.success ) {
						showSuccessMessage(`Дата успешно обновлены`)

						journalDataTable.ajax.reload()
						journalDataTable.draw()
					} else {
						showErrorMessage(data.error)
					}
				}
			})
		}
	})

	$('.del_protocol_radiology').on('click', function () {
		let href = $(this).prev('a').attr('href'),
			a = $(this).prev('a'),
			b = $(this),
			br = $(this).next('br')

		$.ajax({
			method: 'POST',
			url: '/ulab/statistic/delRadiologyProtocol',
			data: {
				href: href
			},
			dataType: 'json',
			success: function(data) {
				if ( data.success ) {
					showSuccessMessage(`Протокол успешно удален`)

					a.remove()
					b.remove()
					br.remove()

				} else {
					showErrorMessage(data.error)
				}
			}
		})
	})

	$('.saveProtocolMineralogy').on('change', function () {
		let form = $(this).parents('form')
		form.trigger('submit')
	})

	$('.date_mineralogy').on('blur', function () {
		let id = $(this).parent('td').find('.id').val(),
			date = $(this).val()
		if (date != '') {
			$.ajax({
				method: 'POST',
				url: '/ulab/statistic/setMineralogyDate',
				data: {
					id: id,
					date: date
				},
				dataType: 'json',
				success: function(data) {
					if ( data.success ) {
						showSuccessMessage(`Дата успешно обновлены`)

						journalDataTable.ajax.reload()
						journalDataTable.draw()
					} else {
						showErrorMessage(data.error)
					}
				}
			})
		}
	})

	$('.del_protocol_mineralogy').on('click', function () {
		let href = $(this).prev('a').attr('href'),
			a = $(this).prev('a'),
			b = $(this),
			br = $(this).next('br')

		$.ajax({
			method: 'POST',
			url: '/ulab/statistic/delMineralogyProtocol',
			data: {
				href: href
			},
			dataType: 'json',
			success: function(data) {
				if ( data.success ) {
					showSuccessMessage(`Протокол успешно удален`)

					a.remove()
					b.remove()
					br.remove()

				} else {
					showErrorMessage(data.error)
				}
			}
		})
	})

	body.on('click', '#view-chart', function () {
		let chartForm = $('#chartForm'),
			date = $('input[name=month]').val()

		$.ajax({
			method: 'POST',
			url: '/ulab/statistic/getChartAjax',
			data: {
				month: date
			},
			dataType: 'json',
			success: function (data) {
				console.log(data)
				let name = []
				let won = []
				let progress = []
				$.each(data, function (key, val) {
					won.push(val.won)
					progress.push(val.progress)
					name.push(val.short_name)
				})
				const ctx = $('#myChart');
				new Chart(ctx, {
					type: 'bar',
					data: {
						labels: name,
						datasets: [{
							label: 'Завершенные',
							data: won,
							borderWidth: 1,
							backgroundColor: 'rgba(117, 246, 169, 0.77)',
						},
						{
						label: 'В процессе',
						data: progress,
						borderWidth: 1,
						backgroundColor: 'rgba(233, 235, 190, 0.45)',
						}]
					},
					options: {
						scales: {
							x: {
								stacked: true
							},
							y: {
								beginAtZero: true,
								stacked: true
							}
						}
					}
				});
			}
		})


		chartForm.toggleClass('visually-hidden')
	})
})
