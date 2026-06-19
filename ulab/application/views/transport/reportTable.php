
<header class="header-secondment mb-4">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link link-back" href="/ulab/transport/reportList/" title="Вернуться назад">
                    <svg class="icon" width="25" height="25">
                        <use xlink:href="/ulab/assets/images/icons.svg#back"></use>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</header>

<div class="filters mb-3">
    <div class="row">
<!--        <div class="col-auto">-->
<!--            <button type="button" name="add_entry" id="transportModalBtn" data-js-update=""-->
<!--                    class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">-->
<!--                Добавить запись-->
<!--            </button>-->
<!--        </div>-->

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

<div>
    <ul class="list-unstyled">
        <li><strong>ФИО:</strong> <?= $this->data["mainData"]["fio"] ?></li>
        <li><strong>Транспорт:</strong> <?= $this->data["mainData"]["model"] ?></li>
        <li><strong>Номер:</strong> <?= $this->data["mainData"]["number"] ?></li>
        <li><strong>Расход:</strong> <span data-js-consumption-rate><?= $this->data["mainData"]["consumption_rate"] ?></span> </li>
    </ul>
</div>

<div class="d-flex align-items-center gap-3 mb-3">
    <div class="fw-bold">Служебная записка</div>
    <div>
        <input type="date" id="secondment_date_start" value="<?= $this->data["mainData"]["secondment_date_start"] ?? date("Y-m-d") ?>" class="form-control">
    </div>
    <div>
        <input type="date" id="secondment_date_end" value="<?= $this->data["mainData"]["secondment_date_end"] ?? date("Y-m-d") ?>" class="form-control">
    </div>
    <button type="button" data-js-memo-doc="<?= $this->data["reportId"] ?>" class="btn btn-primary">
        Сформировать
    </button>
    <div data-js-file-wrap="memo" class="position-relative <?= $this->data["files"]["memo"] ? "" : "d-none" ?>">
        <a
                class="btn btn-primary position-relative ml-4 fs-16"
                href="<?= $this->data["files"]["memo"]["href"] ?>"
                target="_blank"
                title="<?= $this->data["files"]["memo"]["name"] ?>"
                data-js-file-download
                download
        ><i class="fa-solid fa-file"></i></a>
    </div>
    <div data-js-spinner="memo" class="spinner-grow text-info rounded d-none" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<div class="d-flex align-items-center gap-3 mb-3">
    <div class="fw-bold">Отчет</div>
    <button type="button" data-js-report-doc="<?= $this->data["reportId"] ?>" class="btn btn-primary">
        Сформировать
    </button>
    <div data-js-file-wrap="report" class="position-relative <?= $this->data["files"]["report"] ? "" : "d-none" ?>">
        <a
                class="btn btn-primary position-relative ml-4 fs-16"
                href="<?= $this->data["files"]["report"]["href"] ?>"
                target="_blank"
                title="<?= $this->data["files"]["memo"]["name"] ?>"
                data-js-file-download
        ><i class="fa-solid fa-file"></i></a>
    </div>
    <div data-js-spinner="report" class="spinner-grow text-info rounded d-none" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<div class="d-flex align-items-center gap-3 mb-3">
    <div class="fw-bold">Компенсация</div>
    <button type="button" data-js-compensation-doc="<?= $this->data["reportId"] ?>" class="btn btn-primary">
        Сформировать
    </button>
    <div data-js-file-wrap="compensation" class="position-relative <?= $this->data["files"]["compensation"] ? "" : "d-none" ?>">
        <a
                class="btn btn-primary position-relative ml-4 fs-16"
                href="<?= $this->data["files"]["compensation"]["href"] ?>"
                target="_blank"
                title="<?= $this->data["files"]["compensation"]["name"] ?>"
                data-js-file-download
                download
        ><i class="fa-solid fa-file"></i></a>
    </div>
    <div data-js-spinner="compensation" class="spinner-grow text-info rounded d-none" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<div class="d-flex align-items-center gap-3 mb-3">
    <div class="fw-bold">Маршрутный лист</div>
    <div>
        <input type="date" id="report_date" value="<?= $this->data["mainData"]["report_date"] ?? date("Y-m-d") ?>" class="form-control">
    </div>
    <button type="button" data-js-route-doc="<?= $this->data["reportId"] ?>" class="btn btn-primary">
        Сформировать
    </button>
    <div data-js-file-wrap="route" class="position-relative <?= $this->data["files"]["route"] ? "" : "d-none" ?>">
        <a
                class="btn btn-primary position-relative ml-4 fs-16"
                href="<?= $this->data["files"]["route"]["href"] ?>"
                target="_blank"
                title="<?= $this->data["files"]["route"]["name"] ?>"
                data-js-file-download
        ><i class="fa-solid fa-file"></i></a>
    </div>
    <div data-js-spinner="route" class="spinner-grow text-info rounded d-none" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<div id="msg"></div>

<div class="mt-3 mb-3"><h2>Поездки</h2></div>

<table id="tableJournal" class="table table-striped text-center journal" style="width=100%; min-width: 100%">
    <thead>
    <tr class="table-light align-middle">
        <th class="w120" scope="col">Дата</th>
        <th class="w100" scope="col">Время выезда</th>
        <th class="w100" scope="col">Время возвр.</th>
        <th class="w80" scope="col">Км</th>
        <th class="w100" scope="col">ГСМ,&nbsp;л.</th>
        <th class="w80" scope="col">Цена,&nbsp;р.</th>
        <th class="w80" scope="col">Сумма,&nbsp;р.</th>
        <th scope="col">Объект</th>
        <th class="w100">Проект</th>
        <th class="w150">Цель<br>поездки</th>
        <th class="w100">Организация</th>
        <th class="w80" scope="col"></th>
    </tr>
    <tr class="header-search">
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
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="row mt-3 mb-3">
    <div class="col-auto">
        <button type="button" name="add_entry" id="transportModalBtn" data-js-update=""
                class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">
            Добавить поездку
        </button>
    </div>
</div>


<div class="mt-3 mb-3"><h2>Чеки</h2></div>

<table id="tableCheck" class="table table-striped text-center journal" style="width=100%; min-width: 100%">
    <thead>
    <tr class="table-light align-middle">
        <th class="w100" scope="col">Дата</th>
        <th class="w120" scope="col">№ чека</th>
        <th class="w100" scope="col">Сумма</th>
        <th class="" scope="col">Место</th>
        <th class="w80" scope="col"></th>
    </tr>
<!--    <tr class="header-search">-->
<!--        <th scope="col"><input type="text" class="form-control search"></th>-->
<!--        <th scope="col"><input type="text" class="form-control search"></th>-->
<!--        <th scope="col"><input type="text" class="form-control search"></th>-->
<!--        <th scope="col"><input type="text" class="form-control search"></th>-->
<!--        <th scope="col"></th>-->
<!--    </tr>-->
    </thead>
    <tbody>
    </tbody>
</table>

<div class="row mt-3 mb-3">
    <div class="col-auto">
        <button type="button" data-js-update-check=""
                class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">
            Добавить Чек
        </button>
    </div>
</div>

<form id="add-check-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <input type="number" id="report_check_id" hidden>

    <h3>
        Добавить чек
    </h3>
    <div class="line-dashed-small"></div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="check_number" class="col-form-label">№ чека</label>
        </div>
        <div class="col-8">
            <input type="text" id="check_number" class="form-control">
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="check_place" class="col-form-label">Место</label>
        </div>
        <div class="col-8">
            <input type="text" id="check_place" class="form-control">
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="check_date" class="col-form-label">Дата</label>
        </div>
        <div class="col-8">
            <input type="date" id="check_date" class="form-control" aria-describedby="model" data-js>
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="check_sum" class="col-form-label">Сумма</label>
        </div>
        <div class="col-8">
            <input type="number" id="check_sum" class="form-control">
        </div>
    </div>
    <div class="line-dashed-small"></div>

    <button type="button"
            class="btn btn-primary"
            name="stage"
            data-stage="Отменена"
            id="add-check-modal-btn"
    >
        Отправить
    </button>
</form>


<form id="add-entry-modal-form" class="bg-light mfp-hide col-md-5 m-auto p-3 position-relative">
    <input hidden type="number" name="report_id" id="reportId" value="<?= $this->data["reportId"]  ?>">

    <h3>
        Добавить запись
    </h3>
    <div class="line-dashed-small"></div>
    <div class="row mt-1 g-3">
        <div class="col">
            <select class="form-select" name="project_id" id="project_id" style="min-width: 100%">
                <?php foreach ($this->data["projectList"] as $project): ?>
                    <option value="<?= $project["id"] ?>"><?= $project["name"] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="model" class="col-form-label">Дата</label>
        </div>
        <div class="col-8">
            <input type="date" id="date" name="date" class="form-control" aria-describedby="model" data-js>
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="time_start" class="col-form-label">Время выезда</label>
        </div>
        <div class="col-8">
            <input type="time" id="time_start" name="time_start" class="form-control" aria-describedby="time_start" data-js>
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="time_end" class="col-form-label">Время возвращения</label>
        </div>
        <div class="col-8">
            <input type="time" id="time_end" name="time_end" class="form-control" aria-describedby="time_end" data-js>
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="km" class="col-form-label">Км</label>
        </div>
        <div class="col-8">
            <input type="number" data-js-km id="km" name="km" class="form-control" aria-describedby="km" data-js>
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="gsm" class="col-form-label">ГСМ, л.</label>
        </div>
        <div class="col-8">
            <input type="number" data-js-gsm id="gsm" name="gsm" class="form-control" aria-describedby="gsm" data-js>
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="price" class="col-form-label">Цена, р.</label>
        </div>
        <div class="col-8">
            <input type="number" id="price" name="price" class="form-control" aria-describedby="price" data-js>
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="place" class="col-form-label">Объект</label>
        </div>
        <div class="col-8">
            <input type="text" id="place" name="place" class="form-control" aria-describedby="place" data-js>
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="purpose" class="col-form-label">Цель поездки</label>
        </div>
        <div class="col-8">
            <input type="text" id="purpose" name="purpose" class="form-control" aria-describedby="purpose" data-js>
        </div>
    </div>
    <div class="row mt-1 g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="company" class="col-form-label">Организация</label>
        </div>
        <div class="col-8">
            <input type="text" id="company" name="company" class="form-control" aria-describedby="company" data-js>
        </div>
    </div>


    <input type="number" id="report_id" name="report_row_id" class="form-control d-none" aria-describedby="report_row_id">

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
        Удалить поездку?
    </h3>
    <input type="number" id="delete_transport" name="delete_transport" class="form-control d-none" aria-describedby="delete_transport">
    <input type="text" id="table" value="transport_report_row" name="table" class="form-control d-none" aria-describedby="table">
    <button type="button"
            class="btn btn-danger"
            name="stage"
            id="delete-entry-modal-btn"
    >Удалить</button>
</form>

<form id="copy-entry-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <h3 class="mb-2">
        Скопировать поезду?
    </h3>
    <input type="hidden" id="id_copy_transport" name="id_copy_transport" class="form-control d-none" aria-describedby="id_copy_transport">
    <input type="number" id="copy_transport" name="copy_transport" class="form-control mb-2" aria-describedby="copy_transport">
    <button type="button"
            class="btn btn-primary"
            name="stage"
            id="copy-entry-modal-btn"
    >Скопировать</button>
</form>

<form id="delete-check-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <h3>
        Удалить чек?
    </h3>
    <input type="number" id="delete_check" class="form-control d-none">
    <button type="button"
            class="btn btn-danger"
            name="stage"
            id="delete-check-modal-btn"
    >Удалить</button>
</form>
