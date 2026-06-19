<div class="filters mb-3">
    <div class="row">
        <div class="col-auto">
            <button type="button" name="add_entry" id="transportModalBtn" data-js-update=""
                    class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">
                Добавить транспорт
            </button>
        </div>
    </div>
</div>
<!--./filters-->


<table id="transportJournal" class="table table-striped text-center journal" style="width=100%; min-width: 100%">
    <thead>
    <tr class="table-light align-middle">
        <th scope="col">#</th>
        <th scope="col">Модель</th>
        <th scope="col">Номер</th>
        <th scope="col">Владелец</th>
        <th scope="col">Топливо</th>
        <th scope="col">Расход</th>
        <th scope="col">Личный</th>
        <th scope="col"></th>

    </tr>
    <tr class="header-search">
        <th scope="col">

        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
        <th scope="col">
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<form id="add-entry-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <h3>
        Добавить транспорт
    </h3>
    <input type="number" id="transport_id" name="transport_id" class="form-control" aria-describedby="transport_id" style="display: none">
    <div class="line-dashed-small"></div>
    <div class="row g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="model" class="col-form-label">Модель</label>
        </div>
        <div class="col-9">
            <input type="text" id="model" name="model" class="form-control" aria-describedby="model" data-js>
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="number" class="col-form-label">Номер</label>
        </div>
        <div class="col-9">
            <input type="text" id="number" name="number" class="form-control" aria-describedby="number" data-js>
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="owner" class="col-form-label">Владелец</label>
        </div>
        <div class="col-9">
            <input type="text" id="owner" name="owner_name" class="form-control" aria-describedby="owner" data-js>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="fuel" class="col-form-label">Топливо</label>
        </div>
        <div class="col-9">
            <select class="form-control" name="fuel_id" id="fuel" data-js>
                <option value=""></option>
                <option value="1">ДТ</option>
                <option value="2">АИ-92</option>
                <option value="3">АИ-95</option>
                <option value="4">АИ-98</option>
                <option value="5">АИ-100</option>
                <option value="6">КПГ</option>
                <option value="7">СУГ</option>
            </select>
        </div>

    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="consumption_rate" class="col-form-label">Расход</label>
        </div>
        <div class="col-9">
            <input type="text" id="consumption_rate" name="consumption_rate" class="form-control" aria-describedby="consumption_rate" data-js>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-auto">
            <div class="form-check">
                <label class="form-check-label">Личный транспорт</label>
                <input class="form-check-input" id="personal" type="checkbox" name="personal" value="1">
            </div>
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
        Удалить транспорт?
    </h3>
    <input type="number" id="delete_transport" name="delete_transport" class="form-control" aria-describedby="delete_transport" style="display: none">
    <button type="button"
            class="btn btn-danger"
            name="stage"
            id="delete-entry-modal-btn"
    >Удалить</button>
</form>

<!--<svg width="0" height="0" class="hidden">-->
<!--    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="arrow-left">-->
<!--        <path d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm11.5 5.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>-->
<!--    </symbol>-->
<!--    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="arrow-right">-->
<!--        <path d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4.5 5.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>-->
<!--    </symbol>-->
<!--</svg>-->

<!---->
<!--<div class='arrowLeft'>-->
<!--    <svg class="bi" width="40" height="40">-->
<!--        <use xlink:href="#arrow-left"/>-->
<!--    </svg>-->
<!--</div>-->
<!--<div class='arrowRight'>-->
<!--    <svg class="bi" width="40" height="40">-->
<!--        <use xlink:href="#arrow-right"/>-->
<!--    </svg>-->
<!--</div>-->


<!--./add-entry-modal-form-->