<div class="d-flex gap-3" style="width: 100%">
    <div class="col-auto">
        <button type="button" data-js-update="" data-js-manufacturer="<?= $this->data["manufacturer"] ?>" id="add-entry"
                class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">
            Добавить вид работ
        </button>
    </div>
</div>

<div class="scroll mt-3 mb-3" style="position: relative">
    <div class="table-wrap">
        <table id="table" class="table table-striped journal" style="min-width: 100%">
            <thead>
            <tr class="table-light">
                <th class="text-center">Тип работ</th>
                <th class="text-center">Объект</th>
                <th class="text-center">Схемы</th>
                <th class="text-center"></th>
            </tr>

            <tr class="table-light">
                <th scope="col">
                    <input type="text" class="form-control search"/>
                </th>
                <th scope="col">
                    <input type="text" class="form-control search"/>
                </th>
                <th scope="col">
                    <input type="text" class="form-control search"/>
                </th>
                <th></th>

            </tr>
            </thead>
        </table>
    </div>
</div>


<form method="post" action="/ulab/schemeEditor/create" id="create_work_type"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <h3>Добавить тип работ</h3>
    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label for="material_name">Тип работ</label>
            <input name="work_type" type="text" class="form-control" placeholder="Введите тип работ"/>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="manufacturer">Объект</label>
            <input name="object" type="text" class="form-control" placeholder="Введите объект"/>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button onclick="submitCreateForm('create_work_type');" type="button" class="btn btn-primary">Сохранить</button>
        <button type="button" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>

<form method="POST" action="/ulab/schemeEditor/createScheme" id="add-scheme-modal-form"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Добавить схему
    </div>
    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col">
            <input name="work_type_id" type="hidden" data-js-add-modal-worktype-id/>
            <input name="name" type="text" data-js class="form-control" placeholder="Введите название схемы"/>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" onclick="submitCreateForm('add-scheme-modal-form');" id="add-scheme-modal-btn" class="btn btn-primary">Сохранить</button>
        <button type="button" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>

<form method="POST" action="/ulab/schemeEditor/edit" id="edit-scheme-modal-form"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Редактирование
    </div>
    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col">
            <input name="work_type_id" type="hidden" data-js-edit-modal-worktype-id />
            <input id="edit_work_type" name="work_type" type="text" data-js class="form-control" placeholder="Введите название типа работ" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <input id="edit_object" name="object" type="text" data-js class="form-control" placeholder="Введите название объекта" />
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" onclick="submitCreateForm('edit-scheme-modal-form');" id="add-scheme-modal-btn" class="btn btn-primary">Сохранить</button>
        <button type="button" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>


<script>
    function submitCreateForm(formId) {
        let form = $(`#${formId}`);

        let inputs = $(`#${formId} input[type='text']`);

        let error = false;
        for (let elem of inputs) {
            if ($(elem).val().trim().length === 0) {
                $(elem).addClass('is-invalid');
                error = true;
            } else {
                $(elem).removeClass('is-invalid');
            }
        }

        if (!error) {
            form.submit();
            return;
        }
    }
</script>