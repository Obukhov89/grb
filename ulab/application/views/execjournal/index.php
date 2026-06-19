<style>
    .reseted_button {
        width: 3rem;
        height: 3rem;
        padding: 0;
        border: none;
        font: inherit;
        color: inherit;
        background-color: transparent;
        cursor: pointer;
    }
</style>
<a class="link-dark" href="<?= $this->data['instruction_link'] ?>">Описание модуля</a>
<table id="journal_protocol" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap"></th>
        <th scope="col" class="text-nowrap">Статус</th>
        <th scope="col" class="text-nowrap">№ заявки</th>
        <th scope="col" class="text-nowrap">Дата приемочной комиссии</th>
        <th scope="col" class="text-nowrap">Наименование работ</th>
        <th scope="col" class="text-nowrap">Место работ</th>
        <th scope="col" class="text-nowrap">Проект</th>
        <th scope="col" class="text-nowrap">Акт</th>
        <th scope="col" class="text-nowrap">Исполнительная схема</th>
        <th scope="col" class="text-nowrap">Применяемые материалы</th>
        <th scope="col" class="text-nowrap">Паспорт/документ о качестве</th>
        <th scope="col" class="text-nowrap">АВК на применяемые материалы</th>
        <th scope="col" class="text-nowrap">Протоколы/ Заключения/ Акты</th>
        <th scope="col" class="text-nowrap">Объемы</th>
        <th scope="col" class="text-nowrap">Сумма</th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search" disabled/>
        </th>
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
            <input type="text" class="form-control search"/>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled/>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled/>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled/>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled/>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled/>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled/>
        </th>
        <th scope="col">
            <input type="text" class="form-control search"/>
        </th>
        <th scope="col">
            <input type="text" class="form-control search"/>
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
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
</div

<div class="line-dashed"></div>


<form method="post" action="/ulab/execJournal/updateJournalRow" id="edit_journal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <h3>Редактирование <span id="app_number"></span></h3>
    <input type="hidden" name="row_id" id="row_id" />
    <div class="line-dashed-small"></div>

    <div class="row g-3 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="project" class="col-form-label">Проект</label>
        </div>
        <div class="col-9">
            <input type="text" id="project" name="project" class="form-control"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-auto">
            <label for="act" class="col-form-label">Акт</label>
        </div>
        <div class="col-9">
            <select class="form-control" name="act" id="act">
                <option value="0">Выбрать</option>
                <option value="1">АОСР</option>
                <option value="2">АООК</option>
                <option value="3">Не требуется</option>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center">
        <div class="col-auto">
            <label for="executive_scheme" class="col-form-label">Исполнительная схема</label>
        </div>
        <div class="col-9" style="width: min-content; margin: 20px 0 0 0px; scale: 1.4;">
            <input type="checkbox" id="executive_scheme" name="executive_scheme" class="form-check" />
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center">
        <div class="col-auto">
            <label for="materials_used" class="col-form-label">Применяемые материалы</label>
        </div>
        <div class="col-9" style="width: min-content; margin: 20px 0 0 0px; scale: 1.4;">
            <input type="checkbox" id="materials_used" name="materials_used" class="form-check"/>
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center">
        <div class="col-auto">
            <label for="quality_document" class="col-form-label">Паспорт/документ о качестве</label>
        </div>
        <div class="col-9" style="width: min-content; margin: 20px 0 0 0px; scale: 1.4;">
            <input type="checkbox" id="quality_document" name="quality_document" class="form-check"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center">
        <div class="col-auto">
            <label for="avk_for_materials" class="col-form-label">АВК на применяемые материалы</label>
        </div>
        <div class="col-9" style="width: min-content; margin: 20px 0 0 0px; scale: 1.4;">
            <input type="checkbox" id="avk_for_materials" name="avk_for_materials" class="form-check"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center">
        <div class="col-auto">
            <label for="protocols_conclusions_acts" class="col-form-label">Протоколы/ Заключения/ Акты</label>
        </div>
        <div class="col-9" style="width: min-content; margin: 20px 0 0 0px; scale: 1.4;">
            <input type="checkbox" id="protocols_conclusions_acts" name="protocols_conclusions_acts"
                   class="form-check"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center">
        <div class="col-auto">
            <label for="volumes" class="col-form-label">Объемы</label>
        </div>
        <div class="col-9">
            <input type="text" id="volumes" name="volumes" class="form-control"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center">
        <div class="col-auto">
            <label for="summa" class="col-form-label">Сумма</label>
        </div>
        <div class="col-9">
            <input type="number" id="summa" name="summa" class="form-control"/>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
</form>
