<div class="filters mb-3">
    <div class="row">
        <div class="col-auto">
            <button type="button" name="add_entry"
                    class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">
                Добавить запись в журнал
            </button>
        </div>

<!--        <div class="col-auto filter-search" id="filter_search">-->
<!--            <button class="filter-btn-search">-->
<!--                <svg class="bi" width="20" height="20">-->
<!--                    <use xlink:href="--><?//= URI ?><!--/assets/images/icons.svg#icon-search"/>-->
<!--                </svg>-->
<!--            </button>-->
<!--            <div id="journal_requests_filter" class="dataTables_filter">-->
<!--                <label>-->
<!--                    <input id="filter_everywhere" type="search" class="form-control filter" placeholder="Поиск..."-->
<!--                           aria-controls="journal_requests">-->
<!--                </label>-->
<!--            </div>-->
<!--        </div>-->

        <div class="col-auto">
          <button
              type="button"
              id="stage-filter-btn"
              class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0"
          >
            Стадии
          </button>
        </div>

        <div class="col">
            <input type="date" id="inputDateStart" class="form-control filter filter-date-start"
                   value="<?= $this->data['date_start'] ?? '' ?>" placeholder="Введите дату начала:">
        </div>

        <div class="col">
            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end"
                   value="<?= $this->data['date_end'] ?? '' ?>" placeholder="Введите дату окончания:">
        </div>

        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
        </div>
    </div>

    <input type="text" name="stage-filter" id="stage-filter" style="display: none">
    <div id="stage-filter-list" class="mt-3" style="display: none">
      <table style="width: 400px; border-collapse: separate; border-spacing: 5px">
        <tr>
          <td class="text-center border rounded border-powder-blue px-3 py-2">
              Новая
          </td>
          <td class="text-center"><input type="checkbox" data-js-stage="Новая"></td>
        </tr>
        <tr>
          <td class="text-center border rounded border-gold px-3 py-2">
            Ожидает подтверждения</td>
          <td class="text-center"><input type="checkbox" data-js-stage="Ожидает подтверждения"></td>
        </tr>
        <tr>
          <td class="text-center border rounded border-orange px-3 py-2">
            Нужна доработка</td>
          <td class="text-center"><input type="checkbox" data-js-stage="Нужна доработка"></td>
        </tr>
        <tr>
          <td class="text-center border rounded border-red px-3 py-2">
            Отклонена</td>
          <td class="text-center"><input type="checkbox" data-js-stage="Отклонена"></td>
        </tr>
        <tr>
          <td class="text-center border rounded border-deep-sky-blue px-3 py-2">
            Подготовка приказа и СЗ</td>
          <td class="text-center"><input type="checkbox" data-js-stage="Подготовка приказа и СЗ"></td>
        </tr>
        <tr>
          <td class="text-center border rounded border-dodger-blue px-3 py-2">
            Согласована</td>
          <td class="text-center"><input type="checkbox" data-js-stage="Согласована"></td>
        </tr>
        <tr>
          <td class="text-center border rounded border-royal-blue px-3 py-2">
            В командировке</td>
          <td class="text-center"><input type="checkbox" data-js-stage="В командировке"></td>
        </tr>
        <tr>
          <td class="text-center border rounded border-blue px-3 py-2">
            Подготовка отчета</td>
          <td class="text-center"><input type="checkbox" data-js-stage="Подготовка отчета"></td>
        </tr>
        <tr>
          <td class="text-center border rounded border-medium-blue px-3 py-2">
            Проверка отчета</td>
          <td class="text-center"><input type="checkbox" data-js-stage="Проверка отчета"></td>
        </tr>
        <tr>
          <td class="text-center border rounded border-dark-blue px-3 py-2">
            Отчет подтвержден</td>
          <td class="text-center"><input type="checkbox" data-js-stage="Отчет подтвержден"></td>
        </tr>
        <tr>
          <td class="text-center border rounded border-green px-3 py-2">
            Завершена</td>
          <td class="text-center"><input type="checkbox" data-js-stage="Завершена"></td>
        </tr>
        <tr>
          <td class="text-center border rounded border-red px-3 py-2">
            Отменена</td>
          <td class="text-center"><input type="checkbox" data-js-stage="Отменена"></td>
        </tr>
      </table>
    </div>
</div>
<!--./filters-->


<table id="secondmentJournal" class="table table-striped text-center journal" style="width=100%; min-width: 100%">
    <thead>
    <tr class="table-light align-middle">
        <th scope="col"><i class="fa-solid fa-eye"></i></th>
        <th scope="col">Стадия</th>
        <th scope="col">Заявка</th>
        <th scope="col">Сотрудник</th>
        <th scope="col">Населенный пункт</th>
        <th scope="col">Объект</th>
        <th scope="col">Проект</th>
        <th scope="col">Оборудование</th>
        <th scope="col">Дата начала командировки</th>
        <th scope="col">Дата окончания командировки</th>
        <th scope="col">Запланированные затраты</th>
        <th scope="col">Фактические затраты</th>
        <th scope="col">Перерасход %</th>

    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search" disabled>
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
        <th scope="col"></th>
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
          <input type="text" class="form-control search">
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>


<svg width="0" height="0" class="hidden">
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="arrow-left">
        <path d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm11.5 5.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
    </symbol>
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="arrow-right">
        <path d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4.5 5.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
    </symbol>
</svg>


<div class='arrowLeft'>
    <svg class="bi" width="40" height="40">
        <use xlink:href="#arrow-left"/>
    </svg>
</div>
<div class='arrowRight'>
    <svg class="bi" width="40" height="40">
        <use xlink:href="#arrow-right"/>
    </svg>
</div>


<form id="add-entry-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
      action="/ulab/secondment/insertUpdateInfo/" method="post">
    <div class="title mb-3 h-2">
        Данные регистрации
    </div>

    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col" style="width: 400px">
            <label for="user">ФИО <span class="redStars">*</span></label>
            <select
                name="user_id"
                class="form-control h-auto user"
                id="user"
                required
                style="width: 100%"
                <?= $this->data['check_user'] ? "" : "readonly" ?>
            >
                <option value="" selected disabled></option>
                <?php foreach ($this->data['users'] as $val): ?>
                    <option value="<?= $val['ID'] ?>" <?= $val['ID'] === $this->data['user_id'] ? 'selected' : '' ?>>
                        <?= $val['LAST_NAME'] ?> <?= $val['NAME'] ?> <?= $val['SECOND_NAME'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>


    <div class="row mb-3">
        <div class="col">
            <label for="company">Клиент <span class="redStars">*</span></label>
            <div class="row">
                <div class="col-sm-10">
                    <select id="company" name="company_id" data-js-company-list  id="company_list" style="width: 100%">
                        <option data-value=""></option>
                        <?php if (isset($this->data['companies'])): ?>
                            <?php foreach ($this->data['companies'] as $company): ?>
                                <option value="<?= $company['ID'] ?>"><?= $company['TITLE'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <button type="button" class="btn btn-primary rounded col-sm-1" data-js-toggle-company style="margin-top: 0px">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>

            <div data-js-form-company style="margin-left: 10px; display: none; width: 90%">
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
                            <td>Название</td>
                            <td>
                                <input class="tz" type="text" name="company_name" value="" style="width: 100%">
                            </td>
                        </tr>
                        <tr>
                            <td>Полное название</td>
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

    </div>




    <div class="row mb-3">
        <div class="col">
            <label for="object">Объект <span class="redStars">*</span></label>
            <div class="row" style="min-width: 100%">
                <div class="col-sm-10">
                    <select required name="object_id" class="form-control h-auto object" id="object" aria-hidden="true" style="width: 100%">
                        <option value="" selected disabled></option>
                        <?php foreach ($this->data['objects'] as $val): ?>
                            <option value="<?= $val['ID'] ?>" <?= $val['ID'] === $this->data['object_id'] ? 'selected' : '' ?>>
                                <?= $val['NAME'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="button" class="btn btn-primary rounded col-sm-1 col-md-offset-2" data-js-toggle-object><i class="fa-solid fa-plus"></i></button>
            </div>

            <div data-js-form-object style="margin-left: 10px; display: none; width: 90%">
                <h6>Создать объект</h6>
                <div >
                    <table id="obj">
                        <tbody id="obj_body">
                        <tr>
                            <td>Название</td>
                            <td>
                                <input class="tz" type="text" name="NAME" value="" style="width: 100%">
                            </td>
                        </tr>
                        <tr>
                            <td>Клиент</td>
                            <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
                            <td>
                                <select list="gost" class="res gost" data-js-clients name="ID_COMPANY" style="width: 100%">
                                    <option value=""></option>
                                    <?php foreach ($this->data["companyList"] as $company): ?>
                                        <option value="<?= $company["id"] ?>"><?= $company["title"] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Город</td>
                            <!--            <td><input list="gost" class="gost" name="ID_COMPANY" value=""></td>-->
                            <td>
                                <select data-js-cities name="CITY" style="width: 100%" id="city">
                                    <option value=""></option>
                                    <option value="111"></option>
                                    <?php foreach ($this->data["cityList"] as $city): ?>
                                        <option value="<?= $city["id"] ?>"><?= $city["name"] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Километраж</td>
                            <td><input type="number" name="KM" style="width: 100%"></td>
                        </tr>
                        <tr>
                            <td>Координаты</td>
                            <td>
                                <input data-js-coords class="res" type="number" step="0.000001" name="coord[0][0]" value="0" data-id="0">
                                <input data-js-coords class="res" type="number" step="0.000001" name="coord[0][1]" value="0" data-id="0">
                            </td>
                        </tr>
                        <tr>
                            <td>Координаты</td>
                            <td>
                                <input data-js-coords class="res" type="number" step="0.000001" name="coord[1][0]" value="0" data-id="1">
                                <input data-js-coords class="res" type="number" step="0.000001" name="coord[1][1]" value="0" data-id="1">
                            </td>
                        </tr>

                        </tbody>
                    </table>
                    <input type="hidden" name="ID" value="">
<!--                    <input type="button" id="add" value="Добавить координаты ">-->
<!--                    <input type="button" class="btn btn-primary" id="save" value="Сохранить">-->
                    <button type="button" class="btn btn-primary" id="save" >Сохранить</button>
                </div>

            </div>
        </div>
    </div>

<!--    <div class="row mb-3">-->
<!--        <div class="col">-->
<!--            <select class="form-select" id="project_id" name="project_id">-->
<!--                --><?php //foreach ($this->data["projects"] as $project): ?>
<!--                    <option value="--><?//= $project["id"] ?><!--">-->
<!--                        --><?//= $project["name"] ?>
<!--                    </option>-->
<!--                --><?php //endforeach; ?>
<!--            </select>-->
<!--        </div>-->
<!--    </div>-->

    <div class="row mb-3">
        <div class="col">
            <lable for="dateBeginning">Дата начала командировки <span class="redStars">*</span></lable>
            <input type="date" class="form-control date-begin bg-white" id="dateBeginning" name="date_begin"
                   value="<?= $this->data['date_begin'] ?: date('Y-m-d') ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <lable for="dateEnding">Дата окончания командировки <span class="redStars">*</span></lable>
            <input type="date" class="form-control date-ending bg-white" id="dateEnding" name="date_end"
                   value="<?= $this->data['date_begin'] ?: date('Y-m-d') ?>" required>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary" id="save_button">Отправить</button>
</form>

<form id="project-modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Выбрать проект
    </div>

    <input hidden type="number" name="id">

    <div class="mb-3">
        <select class="form-select" name="project_id">
            <?php foreach ($this->data["projects"] as $project): ?>
                <option value="<?= $project["id"] ?>"><?= $project["name"] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" id="update-project" class="btn btn-primary">Сохранить</button>
        <button type="button" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>
<!--./add-entry-modal-form-->
