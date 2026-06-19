<div class="d-flex gap-3" style="width: 100%">
    <div class="col-auto">
        <button type="button" data-js-create-row
                class="btn btn-primary w-100 mw-100 mt-0">
            Добавить
        </button>
    </div>
</div>

<div class="scroll mt-3 mb-3" style="position: relative">
    <div class="table-wrap">
        <table id="seal-table" class="table table-striped journal" style="min-width: 100%">
            <thead>
            <tr class="table-light">
                <th class="text-center">№</th>
                <th class="text-center">Дата</th>
                <th class="text-center">В осях/координаты</th>
                <th class="text-center">Отметка</th>
                <th class="text-center">Протокол</th>
                <th class="text-center">Файл протокола</th>
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
                <th scope="col">
                    <input disabled type="text" class="form-control search"/>
                </th>
                <th scope="col">
                    <input disabled type="text" class="form-control search"/>
                </th>
            </tr>
            </thead>
        </table>

        <div class='arrowLeft'>
            <svg class="bi" width="40" height="40">
                <use xlink:href="<?= URI ?>/assets/images/icons.svg#arrow-left"/>
            </svg>
        </div>
        <div class='arrowRight'>
            <svg class="bi" width="40" height="40">
                <use xlink:href="<?= URI ?>/assets/images/icons.svg#arrow-right"/>
            </svg>
        </div>

        <div class="line-dashed"></div>
    </div>
</div>

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


<?php include "seal_modals.php"; ?>