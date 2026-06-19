$(document).ready(function() {
    // Функция для добавления новой строки
    const $body = $('body');
    const ugtpId = $('#ugtpId').val()
    function addRow() {
        const newRow = `
                <tr>
                    <td><input type="text" name="form_data[${ugtpId}][measuring][object_tested][]" class="form-control form-control-sm" ></td>
                    <td><input type="text" name="form_data[${ugtpId}][measuring][characteristic][]" class="form-control form-control-sm" ></td>
                    <td><input type="text" name="form_data[${ugtpId}][measuring][unit][]" class="form-control form-control-sm" value="%" readonly></td>
                    <td><input type="text" name="form_data[${ugtpId}][measuring][normative_document][]" class="form-control form-control-sm" readonly></td>
                    <td><input type="text" name="form_data[${ugtpId}][measuring][characteristic_name][]" class="form-control form-control-sm" 
                    value="Величина осадки слоя грунта, от его начальной мощности" readonly></td>
                    <td><input type="text" name="form_data[${ugtpId}][measuring][normative_value][]" class="form-control form-control-sm" 
                    value="Не менее 10-12" readonly></td>
                    <td><input type="text" name="form_data[${ugtpId}][measuring][test_method_document][]" class="form-control form-control-sm"
                               value="СТО НОСТРОЙ 2.25.26-2011 (п. 7.4)" readonly></td>
                    <td><input type="text" name="form_data[${ugtpId}][measuring][test_results][]" class="form-control form-control-sm" ></td>
                    <td>
                        <select name="form_data[${ugtpId}][measuring][compliance][]" class="form-select form-select-sm">
                            <option value="Соответствует">Соответствует</option>
                            <option value="Не соответствует">Не соответствует</option>
                        </select>
                    </td>
                    <td><button type="button" class="btn mt-0 btn-square btn-primary add-row">+</button></td>
                    <td><button type="button" class="btn mt-0 btn-square btn-danger remove-row">-</button></td>
                </tr>
            `;
        return newRow;
    }

    // Обработчик клика по кнопке добавления строки
    $(document).on('click', '.add-row', function() {
        $(this).closest('tr').after(addRow());
    });

    $(document).on('click', '.remove-row', function() {
        if ($('#resultsTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            alert("Нельзя удалить последнюю строку!");
        }
    });
});
