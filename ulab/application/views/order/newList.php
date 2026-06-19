<style>
    .table-light {
        position: static !important;
    }
    thead th{background-color:transparent}
    .dtfc-fixed-left {
        background-color: rgb(239, 240, 244);
    }
    th .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: lightgray;
        -webkit-transition: .2s;
        transition: .2s;
        border-radius: 0.25rem;
    }
</style>

<div class="filters mb-4">
    <button id="add-new-dogovor" class="nav-link mb-4 border-5 border-black" style="background-color: #ffffff; width: content-box; padding: 5px" title="Добавить новый договор">
        Создать договор
    </button>

    <div class="row mb-4">
        <div class="col">
            <input type="date" id="inputDateStart" class="form-control filter filter-date-start" value="<?=$this->data['date_start'] ?? '2010-01-01'?>" placeholder="Введите дату начала:">
        </div>

        <div class="col">
            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end" value="<?=date('Y-m-d')?>" placeholder="Введите дату окончания:">
        </div>

        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <select class="form-control filter filter-need-end">
                <option value="0">Все (срок действия)</option>
                <option value="(d.date_end IS NOT NULL AND d.date_end <> '')">Срок действия указан</option>
                <option value="(d.date_end IS NULL OR d.date_end = '')">Срок действия не указан</option>
            </select>
        </div>

        <div class="col">
            <select class="form-control filter filter-need-note">
                <option value="0">Все (особые отметки)</option>
                <option value="(d.special_notes IS NOT NULL AND d.special_notes <> '')">Особые отметки указаны</option>
                <option value="(d.special_notes IS NULL OR d.special_notes = '')">Особые отметки не указаны</option>
            </select>
        </div>

        <div class="col">
            <select class="form-control filter filter-originals">
                <option value="0">Все (оригиналы)</option>
                <option value="1">Подгружены оригиналы</option>
                <option value="2">Не подгружены оригиналы</option>
                <option value="3">Не отправлены оригиналы</option>
            </select>
        </div>
    </div>
</div>

<table id="journal_order" class="table table-striped journal">
    <thead>
        <tr class="table-light">
            <th scope="col" style="background-color: #eff0f4; z-index: 1; " rowspan="2" class="fix-col-1"></th>
            <th scope="col" style="background-color#eff0f4; z-index: 1; width: 100px" rowspan="2" class="fix-col-2">№ договора</th>
            <th scope="col" style="background-color#eff0f4; z-index: 1; width: 100px" rowspan="2" class="fix-col-3">Дата договора</th>
            <th scope="col" style="background-color#eff0f4; z-index: 1; max-width: 400px !important;" rowspan="2" class="fix-col-4">Заказчик</th>
            <th scope="col" style="background-color: #eff0f4; z-index: 1; " rowspan="2" class="fix-col-5">Тип договора</th>
            <th scope="col" style="background-color: #eff0f4; z-index: 1; " rowspan="2" class="fix-col-6">Принадлежность к подразделению</th>
            <th scope="col" style="background-color: #eff0f4; z-index: 1; " rowspan="2" class="fix-col-7">Форма оплаты</th>
            <th scope="col" style="width: 100px" rowspan="2">Стоимость по договору</th>
            <th scope="col" style="width: 200px" colspan="2">Подписанная версия договора</th>
            <th scope="col" style="width: 200px" colspan="2">Подписанная версия доп. соглашения (ДС)</th>
            <th scope="col" style="width: 200px" colspan="2">Подписанная версия протокол разногласий (ПР)</th>
            <th scope="col" style="width: 100px" rowspan="2">Срок действия договора</th>
            <th scope="col" class="text-nowrap" rowspan="2">Особые отметки</th>
        </tr>
        <tr class="header-search">
            <th scope="col" style="width: 100px">скан</th>
            <th scope="col" style="width: 100px">оригинал</th>
            <th scope="col" style="width: 100px">скан</th>
            <th scope="col" style="width: 100px">оригинал</th>
            <th scope="col" style="width: 100px">скан</th>
            <th scope="col" style="width: 100px">оригинал</th>
        </tr>
        <tr class="table-light">
            <th scope="col" class="fix-search-1" style="background-color: #eff0f4; position: sticky; z-index: 1"></th>
            <th scope="col" class="fix-search-2" style="background-color: #eff0f4; position: sticky; z-index: 1">
                <input type="text" class="form-control search">
            </th>
            <th scope="col" class="fix-search-3" style="background-color: #eff0f4; position: sticky; z-index: 1">
                <input type="text" class="form-control search">
            </th>
            <th scope="col" class="fix-search-4" style="background-color: #eff0f4; position: sticky; z-index: 1">
                <input type="text" class="form-control search">
            </th>
            <th scope="col" class="fix-search-5" style="background-color: #eff0f4; position: sticky; z-index: 1">
                <select class="form-select bg-white search">
                    <option value="" selected>Все</option>
                    <?php foreach ($this->data['order_types'] as $value): ?>
                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </th>
            <th scope="col" class="fix-search-6" style="background-color: #eff0f4; position: sticky; z-index: 1">
                <select class="form-select bg-white search">
                    <option value="" selected>Все</option>
                    <?php foreach ($this->data['dogovor_belonging'] as $value): ?>
                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </th>
            <th scope="col" class="fix-search-7" style="background-color: #eff0f4; position: sticky; z-index: 1">
                <select class="form-select bg-white search">
                    <option value="" selected>Все</option>
                    <?php foreach($this->data['payment_types'] as $value): ?>
                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </th>
            <th scope="col">
                <select class="form-select bg-white search">
                    <option value="">Все</option>
                    <option value="1">Указана</option>
                    <option value="2">Не указана</option>
                </select>
            </th>
            <th scope="col">

            </th>
            <th scope="col">

            </th>
            <th scope="col">

            </th>
            <th scope="col">

            </th>
            <th scope="col">

            </th>
            <th scope="col">

            </th>
            <th scope="col">

            </th>
            <th scope="col">
                <select class="form-select bg-white search">
                    <option value="">Все</option>
                    <option value="1">Указаны</option>
                    <option value="2">Не указаны</option>
                </select>
            </th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>


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

<div class="line-dashed"></div>

<form id="new_dogovor" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/order/createNewDogovor" method="post" enctype="multipart/form-data">
    <div class="title mb-3 h-2">
        Создание договора
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" name="form[custom_number]" step="any" value="0" id="custom_number" required>
    <input type="hidden" name="form[next_number]" step="any" value="0" id="next_number" required>

    <div class="mb-3">
        <label class="form-label" for="number">Номер</label>
        <input type="text" class="form-control w-100 bg-white" id="number" name="form[number]" required>
    </div>

    <div class="mb-3">
        <label class="form-label" for="type">Тип договора</label>
        <select class="form-control w-100 bg-white" id="type" name="form[type]" step="any" required>
            <?php foreach ($this->data['order_types'] as $value): ?>
                <?php if(!$value['needle_to_list']): continue; endif; ?>
                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label" for="belonging">Принадлежность</label>
        <select class="form-control w-100 bg-white" id="belonging" name="form[belonging]" step="any" required>
            <?php foreach ($this->data['dogovor_belonging'] as $value): ?>
                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label" for="client_id">Контрагент</label>
        <div class="row">
            <div class="col-sm-10">
                <select class="form-control w-100 bg-white js-example-basic-single client-select select2 col-sm-1" id="client_id" name="form[client_id]" step="any" required>

                </select>
            </div>
            <button type="button" class="btn btn-primary rounded col-sm-1 visible-create" data-js-toggle-company style="margin-top: 0px">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>

        <div class="form-create" style="margin-left: 10px; display: none; width: 90%">
            <h6>Создать компанию</h6>
            <div >
                <table id="company" style="width: 100%">
                    <tbody id="company_body">

                    <tr>
                        <td>ИНН</td>
                        <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
                        <td>
                            <input id="inn" type="number" name="inn" style="width: 100%">
                            <div id="innHelp" class="form-text"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>Сокращенное наименование</td>
                        <td>
                            <input class="tz" type="text" name="company_name" value="" style="width: 100%">
                        </td>
                    </tr>
                    <tr>
                        <td>Полное наименование</td>
                        <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
                        <td>
                            <input class="tz" type="text" name="CompanyFullName" style="width: 100%">
                        </td>
                    </tr>
                    <tr>
                        <td>Адрес</td>
                        <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
                        <td>
                            <input class="tz" type="text" name="ADDR" style="width: 100%">
                        </td>
                    </tr>
                    <tr>
                        <td>ОГРН</td>
                        <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
                        <td>
                            <input type="number" name="OGRN" style="width: 100%">
                        </td>
                    </tr>
                    <tr>
                        <td>КПП</td>
                        <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
                        <td>
                            <input type="number" name="KPP" style="width: 100%">
                        </td>
                    </tr>
                    <tr>
                        <td>ФИО руководителя</td>
                        <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
                        <td>
                            <input class="tz" type="text" name="DirectorFIO" style="width: 100%">
                        </td>
                    </tr>
                    <tr>
                        <td>Должность руководителя</td>
                        <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
                        <td>
                            <input class="tz" type="text" name="Position2" style="width: 100%">
                        </td>
                    </tr>



                    </tbody>
                </table>
                <input type="hidden" name="ID" value="">
                <!--                    <input type="button" id="add" value="Добавить координаты ">-->
                <!--                    <input type="button" class="btn btn-primary" id="save" value="Сохранить">-->
                <button type="button" class="btn btn-primary" id="saveCompany" >Сохранить</button>
            </div>

        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" for="assigned_id">Подписант</label>
        <select class="form-control w-100 bg-white" id="assigned_id" name="form[assigned_id]" step="any" required>
            <option value="Ахтямов Рашид Якубович">Ахтямов Р.Я.</option>
            <option value="Ахтямов Эльдар Рашидович">Ахтямов Э.Р.</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Создать</button>
</form>
