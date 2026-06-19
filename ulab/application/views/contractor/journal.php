<div class="filters mb-3">
    <div class="row">
        <div class="col-auto">
            <button type="button" data-js-update
                    class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">
                Добавить
            </button>
        </div>
    </div>
</div>

<div class="wrap fs-12">
    <div class="table-wrap">
        <table id="tableJournal" class="table table-striped text-center journal" style="width=100%; min-width: 100%">
            <thead>
            <tr class="table-light align-middle">
                <th class="w40" scope="col"></th>
                <th class="w40" scope="col">№</th>
                <th class="w100" scope="col">Дата приемочной комиссии</th>
                <th class="w150" scope="col">Погода, температура, осадки</th>
                <th class="w150" scope="col">Описание предъявляемых работ подрядчика</th>
                <th class="w40" scope="col"></th>
                <th class="w150" scope="col">Место работ</th>
                <th class="w80" scope="col">Констр.</th>
                <th class="w150" scope="col">Название организации</th>
                <th class="w150" scope="col">№ участка</th>
                <th class="w150" scope="col">Заказчик</th>
                <th class="w150" scope="col">Телефон</th>
                <th class="w120" scope="col">Подобъект (здание, блок)</th>
                <th class="w150" scope="col">Описание работ инженера по строительному контролю</th>
                <th class="w150" scope="col">Ответственный</th>
                <th class="w80" scope="col">Чеклист</th>
                <th class="w80" scope="col">АОК</th>
                <th class="w150" scope="col">Акт несоответствия</th>
                <th class="w150" scope="col">Примечание</th>
                <th class="w150" scope="col">Результат приемочного контроля</th>
                <th class="w80" scope="col"></th>
            </tr>
            <tr class="header-search">
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"><input type="text" class="form-control search"></th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <form id="add-entry-modal-form" class="bg-light mfp-hide col-8 m-auto p-3 position-relative">
        <h3>
            Изменить запись
        </h3>
        <div class="line-dashed-small"></div>

        <input type="number" id="row_id" name="row_id" class="form-control" aria-describedby="row_id" hidden>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="assigned_completed" class="col-form-label">Ответственный (Дата, время, ФИО)</label>
            </div>
            <div class="col-8">
                <input type="text" id="assigned_completed" name="assigned_completed" class="form-control">
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="weather" class="col-form-label">Погода</label>
            </div>
            <div class="col-8">
                <input type="text" id="weather" name="weather" class="form-control">
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="job_desc" class="col-form-label">Описание работ</label>
            </div>
            <div class="col-8">
                <input type="text" id="job_desc" name="job_desc" class="form-control">
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="aok" class="col-form-label">АОК</label>
            </div>
            <div class="col-8">
                <select name="aok" class="form-control h-auto object" id="aok" aria-hidden="true" style="width: 100%">
                    <option value="" selected disabled></option>
                    <option value="1">Да</option>
                    <option value="0">Нет</option>
                </select>
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="act" class="col-form-label">Акт несоответствия / предписание</label>
            </div>
            <div class="col-8">
                <input type="text" id="act" name="act" class="form-control">
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="comment" class="col-form-label">Примечание</label>
            </div>
            <div class="col-8">
                <input type="text" id="comment" name="comment" class="form-control">
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="result" class="col-form-label">Результат приемочного контроля</label>
            </div>
            <div class="col-8">
                <select name="result" class="form-control h-auto object" id="result" aria-hidden="true" style="width: 100%">
                    <option value="" selected></option>
                    <?php foreach ( $this->data["result_stages"] as $stage): ?>
                        <option value="<?= $stage["id"] ?>"><?= $stage["name"] ?></option>
                    <?php endforeach; ?>
                </select>
<!--                <input type="text" id="result" name="result" class="form-control">-->
            </div>


        </div>

        <div class="line-dashed-small"></div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="weather" class="col-form-label fw-bold">Поля из телеграмм</label>
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="tg_id" class="col-form-label">Заказчик</label>
            </div>
            <div class="col-8">
                <select name="tg_id" class="form-control h-auto object" id="tg_id" aria-hidden="true" style="width: 100%">
                    <?php foreach ($this->data["user_list"] as $user): ?>
                        <option value="<?= $user["tg_id"] ?>" >
                            <?= $user["fio"] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="act" class="col-form-label">№ участка</label>
            </div>
            <div class="col-8">
                <input type="text" id="area_number" name="area_number" class="form-control">
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="act" class="col-form-label">Дата и время</label>
            </div>
            <div class="col-8">
                <input type="text" id="datetime" name="datetime" class="form-control">
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="act" class="col-form-label">Место работ</label>
            </div>
            <div class="col-8">
                <input type="text" id="work_place" name="work_place" class="form-control">
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="act" class="col-form-label">Содержание заявки</label>
            </div>
            <div class="col-8">
                <input type="text" id="content" name="content" class="form-control">
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="work_object" class="col-form-label">Объект работ</label>
            </div>
            <div class="col-8">
                <input type="text" id="work_object" name="work_object" class="form-control">
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="act" class="col-form-label">Конструктив</label>
            </div>
            <div class="col-8">
                <input type="text" id="constructive" name="constructive" class="form-control">
            </div>
        </div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-auto">
                <label for="act" class="col-form-label">Чеклист</label>
            </div>
            <div class="col-8">
                <input type="text" id="checklist" name="checklist" class="form-control">
            </div>
        </div>

        <div class="line-dashed-small"></div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" id="add-entry-modal-btn">Сохранить</button>
            <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
        </div>
    </form>

    <form id="status-modal" class="bg-light mfp-hide col-8 m-auto p-3 position-relative">
        <h3>
            Изменить статус
        </h3>
        <div class="line-dashed-small"></div>

        <div class="row mt-1 g-3 align-items-center justify-content-between">
            <div class="col-8">
                <select name="status" class="form-control h-auto object" id="status" aria-hidden="true" style="width: 100%">
                    <option value="0">Новая</option>
                    <option value="1">В работе</option>
                    <option value="2">Завершена</option>
                </select>
            </div>
        </div>

        <div class="line-dashed-small"></div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" id="save-status">Сохранить</button>
            <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
        </div>
    </form>


    <div class='arrowLeft'>
        <svg class="bi" width="40" height="40">
            <use xlink:href="<?=URI?>/assets/images/icons.svg#arrow-left"/>
        </svg>
    </div>
    <div class='arrowRight'>
        <svg class="bi" width="40" height="40">
            <use xlink:href="<?=URI?>/assets/images/icons.svg#arrow-right"/>
        </svg>
    </div>
</div>

