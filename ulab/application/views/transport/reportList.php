<div class="filters mb-3">
    <div class="row">
        <div class="col-auto">
            <button type="button" name="add_entry" id="transportModalBtn" data-js-update=""
                    class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">
                Добавить отчет
            </button>
        </div>

<!--        <div class="col col-md-2">-->
<!--            <input type="date" id="inputDateStart" class="form-control filter filter-date-start"-->
<!--                   value="" placeholder="Введите дату начала:">-->
<!--        </div>-->
<!---->
<!--        <div class="col col-md-2">-->
<!--            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end"-->
<!--                   value="" placeholder="Введите дату окончания:">-->
<!--        </div>-->
    </div>


</div>
<!--./filters-->

<input id="currentUser" class="d-none" type="number" value="<?= $this->data["currentUser"] ?>">

<table id="tableJournal" class="table table-striped text-center journal" style="width=100%; min-width: 100%">
    <thead>
    <tr class="table-light align-middle">
        <th class="w80" scope="col">№</th>
        <th class="w100" scope="col">ФИО</th>
        <th class="w150" scope="col">Транспорт</th>
        <th class="w150" scope="col">Номер</th>
        <th class="w80" scope="col"></th>
    </tr>
    <tr class="header-search">
        <th scope="col"><input type="text" class="form-control search"></th>
        <th scope="col"><input type="text" class="form-control search"></th>
        <th scope="col"><input type="text" class="form-control search"></th>
        <th scope="col"><input type="text" class="form-control search"></th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<form id="add-entry-modal-form" class="bg-light mfp-hide col-md-8 m-auto p-3 position-relative">
    <h3>
        Добавить отчет
    </h3>
    <div class="line-dashed-small"></div>

    <div class="mt-1">
        <select name="user_id" class="form-control h-auto object" id="user" aria-hidden="true" style="width: 100%">
            <option value="" selected disabled></option>
            <?php foreach ($this->data['userList'] as $user): ?>
                <option value="<?= $user['ID'] ?>" <?= $user['ID'] === $this->data['currentUser'] ? 'selected' : '' ?>>
                    <?= $user['fio'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mt-3">
        <select name="transport_id" class="form-control h-auto object" id="transport" aria-hidden="true" style="width: 100%">
            <option value="" selected disabled>Выбрать транспорт</option>
            <?php foreach ($this->data['transportList'] as $transport): ?>
                <option value="<?= $transport['id'] ?>" data-js-consumption_rate="<?= $transport['consumption_rate'] ?>">
                    <?= $transport['model'] ?> (<?= $transport['number'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>


    <input type="number" id="report_id" name="report_id" class="form-control d-none" aria-describedby="report_id">

    <div data-js-report-wrap>
        <div class="line-dashed-small"></div>
        <table id="gsm-report-table" class="table table-bordered"
        <thead>
        <tr>
            <th class="text-center align-middle w150">Дата</th>
            <th class="text-center align-middle w100">Время выезда</th>
            <th class="text-center align-middle w100">Время возвращения</th>
            <th class="text-center align-middle w100">Км</th>
            <th class="text-center align-middle w100">ГСМ, л.</th>
            <th class="text-center align-middle w100">Цена, р.</th>
            <th class="text-center align-middle">Объект</th>
            <th class="text-center align-middle w100"></th>
        </tr>
        </thead>

        </table>
        <div class="d-flex justify-content-center">
            <button id="gsm-report-add" type="button" class="btn btn-primary rounded fa-solid fa-plus"></button>
        </div>
    </div>





    <div class="line-dashed-small"></div>

    <button type="button"
            class="btn btn-primary"
            name="stage"
            data-stage="Отменена"
            id="add-entry-modal-btn"
    >
        Отправить
    </button>
</form>

<form id="delete-entry-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <h3>
        Удалить отчет?
    </h3>
    <input type="number" id="delete_transport" name="delete_transport" class="form-control" aria-describedby="delete_transport" style="display: none">
    <input type="text" id="table" value="transport_report" name="table" class="form-control" aria-describedby="table" style="display: none">
    <button type="button"
            class="btn btn-danger"
            name="stage"
            id="delete-entry-modal-btn"
    >Удалить</button>
</form>