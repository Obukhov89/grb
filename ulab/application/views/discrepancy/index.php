<div class="d-flex gap-3" style="width: 100%">
    <div class="col-auto">
        <button type="button" data-js-create id="add-entry"
                class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">
            Добавить запись
        </button>
    </div>
    <div class="col-auto">
        <button type="button" data-js-create-responsible
                class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">
            Добавить ответственного
        </button>
    </div>
    <div class="col-auto">
        <button type="button" data-js-import-excel
                class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">
            импорт excel
        </button>
    </div>
</div>

<div class="scroll mt-3 mb-3" style="position: relative">
    <div class="table-wrap">
        <table id="discrepancy_table" class="table table-striped journal" style="min-width: 100%">
            <thead>
            <tr class="table-light">
                <th class="text-center">№</th>
                <th class="text-center">Дата</th>
                <th class="text-center">Ответственный</th>
                <th class="text-center">Краткое содержание</th>
                <th class="text-center"> Отметка об устранении</th>
                <th class="text-center"></th>
            </tr>

            <tr class="table-light">
                <th scope="col">
                    <input type="text" class="form-control search" disabled/>
                </th>
                <th scope="col">
                    <input type="text" class="form-control search"/>
                </th>
                <th scope="col">
                    <input type="text" class="form-control search"/>
                </th>
                <th scope="col">
                    <input type="text" class="form-control search"/>
                </th>
                <th scope="col">
                    <input type="text" class="form-control search"/>
                </th>
                <th>
                    <input type="text" class="form-control search" disabled/>
                </th>
            </tr>
            </thead>
        </table>
    </div>
</div>


<form method="post" action="/ulab/discrepancy/create" id="create_row"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <h3>Добавить</h3>
    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label for="responsible">Ответственный</label>
            <select required name="responsible" id="responsible" class="form-control">
                <option value="">Выбрать</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="summary">Краткое содержание заявки</label>
<!--            <input name="summary" id="summary" type="text" class="form-control" />-->
            <textarea required name="summary" id="summary" type="text" class="form-control"></textarea>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="fix_number">Отметка об устранении: №</label>
            <input name="fix_number" id="fix_number" type="number" min="0" step="1" class="form-control" />
        </div>
        <div class="col">
            <label for="fix_date">Дата</label>
            <input name="fix_date" id="fix_date" type="date" class="form-control" />
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>

<form method="post" action="/ulab/discrepancy/addResponsible" id="create_responsible"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <h3>Добавить ответственного</h3>
    <div class="line-dashed-small"></div>


    <div class="row mb-3">
        <div class="col">
            <label for="fio">ФИО</label>
            <input name="fio" id="fio" type="text" class="form-control" />
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button onclick="submitCreateForm('create_responsible');" type="button" class="btn btn-primary">Сохранить</button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>

<!--редактирование журнала-->
<form id="edit-row-modal" action="/ulab/discrepancy/edit" method="POST"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <h3>Редактирование журнала</h3>
    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label for="responsible">Ответственный</label>
            <select required name="responsible" id="responsible" class="form-control">
                <option value="">Выбрать</option>
            </select>
            <input name="row_id" type="hidden" id="row_id" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="summary">Краткое содержание заявки</label>
            <textarea required name="summary" id="summary" type="text" class="form-control"></textarea>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="fix_number">Отметка об устранении: №</label>
            <input name="fix_number" id="fix_number" type="number" min="0" step="1" class="form-control" />
        </div>
        <div class="col">
            <label for="fix_date">Дата</label>
            <input name="fix_date" id="fix_date" type="date" class="form-control" />
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="submit"
                class="btn btn-primary">Сохранить
        </button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary"
                style="margin-left: 5px">Закрыть
        </button>
    </div>
</form>

<form method="POST" action="/ulab/discrepancy/import" id="excel-import-modal"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <div class="title mb-3 h-2">
        Импорт excel
    </div>
    <div class="line-dashed-small"></div>

    <div class="form-group">
        <label for="excel-file">Выберите файл Excel:</label>
        <input type="file" class="form-control-file" id="excel-file" name="excel-file" accept=".xlsx, .xls" />
    </div>
    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" onclick="submitCreateForm('excel-import-modal');" id="add-scheme-modal-btn" class="btn btn-primary">Сохранить</button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>

<script>
    function submitCreateForm(formId) {
        const form = $(`#${formId}`);
        const inputs = document.querySelectorAll(`#${formId} input, #${formId} select, #${formId} textarea`);

        let error = false;
        inputs.forEach(input => {
            if (input.value.trim() === '') {
                error = true;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!error) {
            form.submit();
            return;
        }
    }
</script>