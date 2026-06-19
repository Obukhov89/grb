<div class="d-flex gap-3" style="width: 100%">
    <div class="col-auto">
        <button type="button" data-js-create-row
                class="btn btn-primary w-100 mw-100 mt-0">
            Добавить
        </button>
    </div>
    <div class="col-auto">
        <button type="button" data-js-show-contractor-handbook class="btn btn-primary w-100 mw-100 mt-0">
            Справочник подрядчиков
        </button>
    </div>
    <div class="col-auto">
        <button type="button" data-js-show-kindofwork-handbook class="btn btn-primary w-100 mw-100 mt-0">
            Справочник Типов работ
        </button>
    </div>
    <div class="col-auto">
        <button type="button" data-js-show-responsibleforacceptance-handbook class="btn btn-primary w-100 mw-100 mt-0">
            Справочник ответственных
        </button>
    </div>
</div>

<div class="scroll mt-3 mb-3" style="position: relative">
    <div class="table-wrap">
        <table id="work-diary-table" class="table table-striped journal" style="min-width: 100%">
            <thead>
            <tr class="table-light">
                <th class="text-center"></th>
                <th class="text-center">№</th>
                <th class="text-center">Дата проверки</th>
                <th class="text-center">Фото</th>
                <th class="text-center">Подрядчик</th>
                <th class="text-center">Проект</th>
                <th class="text-center">Вид работ</th>
                <th class="text-center">Наименование конструктива</th>
                <th class="text-center">Тип работы</th>
                <th class="text-center">В осях/координаты</th>
                <th class="text-center">Результаты приемки</th>
                <th class="text-center">Предписания</th>
                <th class="text-center">Содержание замечаний</th>
                <th class="text-center">Пункт ТР НПА ПД</th>
                <th class="text-center">Дата устранения замечаний</th>
                <th class="text-center">Отметка об устранении замечаний</th>
                <th class="text-center">Примечание</th>
                <th class="text-center">Объем</th>
                <th class="text-center">Ответственный ФИО по приемке</th>
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
                    <input type="date" class="form-control search"/>
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
                    <input type="text" class="form-control search"/>
                </th>
                <th scope="col">
                    <input type="text" class="form-control search" disabled/>
                </th>
                <th scope="col">
                    <input type="text" class="form-control search"/>
                </th>
                <th scope="col">
                    <select class="form-control search">
                        <option value="">Выбрать</option>
                        <option value="1">Принято</option>
                        <option value="2">Не принято</option>
                    </select>
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
                    <input type="date" class="form-control search"/>
                </th>
                <th scope="col">
                    <select class="form-control search">
                        <option value="">Выбрать</option>
                        <option value="2">Да</option>
                        <option value="1">Нет</option>
                    </select>
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
                    <input type="text" class="form-control search" disabled/>
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

<?php include "diary_modals.php" ?>

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