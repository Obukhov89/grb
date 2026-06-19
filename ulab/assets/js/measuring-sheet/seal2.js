$(document).ready(function() {
    // Функция для добавления новой строки
    const $body = $('body');
    const ugtpId = $('#ugtpId').val()
    function addRow() {
        const newRow = `
                <tr>
                <td rowspan="2" class="align-content-center"><input type="text" name="form_data[${ugtpId}][measuring][object_tested][]" class="form-control form-control-sm"></td>
                <td><input type="text" name="form_data[${ugtpId}][measuring][characteristic][modul][]" class="form-control form-control-sm" value="Модуль упругости" readonly></td>
                <td><input type="text" name="form_data[${ugtpId}][measuring][unit][modul][]" class="form-control form-control-sm" value="МН/м²" readonly></td>
                <td><input type="text" name="form_data[${ugtpId}][measuring][normative_value][modul][]" class="form-control form-control-sm" value="-" readonly></td>
                <td rowspan="2" class="align-content-center"><input type="text" name="form_data[${ugtpId}][measuring][test_method_document][]" class="form-control form-control-sm"
                value="Руководство по эксплуатации 7360-028-2010 РЭ" readonly></td>
                <td><input type="text" name="form_data[${ugtpId}][measuring][test_results][modul][]" class="form-control form-control-sm"></td>
                <td rowspan="2" class="align-content-center">
                    <select name="form_data[${ugtpId}][measuring][compliance][]" class="form-select form-select-sm">
                        <option value="Соответствует">Соответствует</option>
                        <option value="Не соответствует">Не соответствует</option>
                    </select>
                </td>
                <td rowspan="2" class="align-content-center">
                    <button type="button" class="btn mt-0 btn-square btn-primary add-row">+</button>
                </td>
                <td rowspan="2" class="align-content-center">
                    <button type="button" class="btn mt-0 btn-square btn-danger remove-row">-</button>
                </td>
            </tr>
            <tr>
                <td><input type="text" name="form_data[${ugtpId}][measuring][characteristic][coef][]" class="form-control form-control-sm" value="Коэффициент уплотнения" readonly></td>
                <td><input type="text" name="form_data[${ugtpId}][measuring][unit][coef][]" class="form-control form-control-sm" value="-" readonly></td>
                <td><input type="text" name="form_data[${ugtpId}][measuring][normative_value][coef][]" class="form-control form-control-sm"></td>
                <td><input type="text" name="form_data[${ugtpId}][measuring][test_results][coef][]" class="form-control form-control-sm"></td>
            </tr>
            `;
        return newRow;
    }

    // Обработчик клика по кнопке добавления строки
    $(document).on('click', '.add-row', function() {
        const $table = $('#resultsTable');
        $table.find('tbody').append(addRow());
    });

    $(document).on('click', '.remove-row', function() {
        try {
            const $rows = $('#resultsTable tbody tr');
            if ($rows.length > 2) {
                $(this).closest('tr').next('tr').remove();
                $(this).closest('tr').remove();
            } else {
                alert("Нельзя удалить последнюю пару строк!");
            }
        } catch (error) {
            console.error('Ошибка при удалении строки:', error);
        }
    });
});
