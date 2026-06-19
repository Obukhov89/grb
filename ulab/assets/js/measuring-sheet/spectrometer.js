const repeatabilityLimit = {
    // Оксид магния
    'MgO': [
        [0.1, 0.2, 0.5, 1, 2, 5, 10, 20, 50, 99],
        [0.025, 0.04, 0.05, 0.08, 0.11, 0.18, 0.3, 0.4, 0.6, 0.8]
    ],
    // Оксид натрия
    'Na2O': [
        [0.1, 0.2, 0.5, 1, 2, 5, 10, 20],
        [0.03, 0.04, 0.06, 0.08, 0.11, 0.18, 0.3, 0.4]
    ],
    // Оксид алюминия
    'Al2O3': [
        [0.02, 0.05, 0.1, 0.2, 0.5, 1, 2, 5, 10, 20, 50, 99],
        [0.013, 0.02, 0.03, 0.04, 0.06, 0.09, 0.12, 0.2, 0.3, 0.4, 0.6, 0.9]
    ],
}

$(function () {
    $('.parse-file').change(function () {
        if ( ! window.FileReader ) {
            return alert( 'FileReader API is not supported by your browser.' )
        }

        $('#parse-file_2').prop('disabled', false)

        let file = $(this).prop('files')[0]
        const testName = $(this).data('file')
        const $table = $('.parse-table')

        let reader = new FileReader()

        let fileName = file.name.replace(/\.html$/gi, '')
        $(`.name-${testName}`).val(fileName)

        reader.readAsText( file, 'windows-1251' )

        reader.onload = function() {
            let results = Array.from(reader.result.matchAll(/<pre id="qn_disp" style="display:inline">--+\r\n.*?--+\r\n(.*?)\r\nСумма/gms))

            if ( results[0][1] === undefined ) {
                console.log("Error reg")
            } else {
                let rows = results[0][1].split("\r\n")

                $.each(rows, function (i, item) {
                    let tmp = Array.from(item.matchAll(/^([\w\d]+)\s+([\w\d.]+)/g))

                    if ( tmp[0] !== undefined && tmp[0][1] == 'Al2O3' ) {

                        const $tr = $table.find(`tr[data-element="${tmp[0][1]}"]`)

                        if ( $tr.length === 0 ) {

                            let valLimit = repeatabilityLimit[tmp[0][1]]
                            let delta = ''

                            if ( valLimit !== undefined ) {
                                delta = valLimit[1][valLimit[0].findIndex((el) => el >= tmp[0][2])]
                            }

                            $table.find('tbody').append(
                                `<tr class="tr-results" data-element="${tmp[0][1]}">
                                    <td><input type="text" class="form-control" name="form[results][${i}][element]" value="${tmp[0][1]}" readonly></td>
                                    <td><input type="text" class="form-control result-test-1" name="form[results][${i}][result_test_1]" value="${tmp[0][2]}" readonly></td>
                                    <td><input type="text" class="form-control result-test-2" name="form[results][${i}][result_test_2]" value="" readonly></td>
                                    <td><input type="text" class="form-control result-delta" name="form[results][${i}][result_delta]" value="${delta}" readonly></td>
<!--                                    <td><input type="text" class="form-control result-average" name="form[results][${i}][result_average]" value="" readonly></td>-->
                                    <td><input type="text" class="form-control result-average" name="result_value" value="" readonly></td>
                                </tr>`
                            )
                        } else {
                            $tr.find('.result-test-2').val(tmp[0][2])

                            let result1 = $tr.find('.result-test-1').val()
                            let delta = $tr.find('.result-delta').val()
                            let $resultAverageInput = $tr.find('.result-average')

                            if ( result1 === 'ND' || tmp[0][2] === 'ND' ) {
                                $resultAverageInput.val('ND')
                            } else {
                                $tr.find('.result-test-2').val(tmp[0][2])
                                let abs = Math.abs(parseFloat(result1) - parseFloat(tmp[0][2]))

                                if ( parseFloat(delta) >= abs ) {
                                    $resultAverageInput.val(round((parseFloat(result1) + parseFloat(tmp[0][2])) / 2, 2))
                                } else {
                                    $resultAverageInput.val('ND')
                                }
                            }
                        }
                    }
                })
            }
        }

        reader.onerror = function() {
            console.log(reader.error)
        }
    })

    $('.clear-all').click(function () {
        $('.tr-results').remove()
        $('.name-test-1').val('')
        $('.name-test-2').val('')
        $('.parse-file').val('')
        $('#parse-file_2').prop('disabled', true)
    })
})
