<input type="hidden" id="curr_user" value="<?= $this->data['curr_user']?>">

<div class="wrapper-card m-auto">
    <header class="header-result mb-3">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-2">
                    <a class="nav-link link-back disabled"
                       href="<?= URI ?>/request/card/<?= $this->data['deal_id'] ?? '' ?>"
                       title="Вернуться назад">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#back"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-list" href="<?= URI ?>/request/list/" title="Вернуться к списку">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#list"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-card" href="<?= URI ?>/request/card/<?= $this->data['deal_id'] ?? '' ?>"
                       title="Карточка заявки">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#card"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2 disabled">
                    <a class="nav-link link-doc-send disabled" href="#">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-send"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-docs disabled" href="#">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#docs"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-doc-edit disabled" href="#">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-edit"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link popup-help" href="/ulab/help/LIMS_Manual_Stand/Result_card/Result_card.html" title="ПОМОГИТЕ">
                        <i class="fa-solid fa-question"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </header>


    <div class="form form-result" id="formResult">
        <?php if (!empty($this->data['requirement']['tz_id'])): ?>
            <input class="tz-id" type="hidden" name="tz_id" value="<?= $this->data['requirement']['tz_id'] ?>">
        <?php endif; ?>

        <?php if (!empty($this->data['deal_id'])): ?>
            <input class="deal-id" type="hidden" name="deal_id" value="<?= $this->data['deal_id'] ?>">
        <?php endif; ?>

        <?php if (!empty($this->data['selected_protocol'])): ?>
            <input class="protocol-id" type="hidden" name="protocol_id"
                   value="<?= $this->data['selected_protocol'] ?: '' ?>">
        <?php endif; ?>

        <?php if (!empty($this->data['selected'])): ?>
            <input class="selected" type="hidden" name="selected" value="selected">
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <header class="panel-heading">
                        Таблица результатов испытаний
                        <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="#" class="fa fa-chevron-up"></a>
                         </span>
                    </header>
                    <div class="panel-body position-relative overflow-x-auto">
                        <input type="hidden" id="deal_id" value="<?= $this->data['deal_id'] ?>">

                        <div class="row flex-nowrap ">

                            <div class="col-auto">
                                <div class="js-sticky-widget2">
                                    <div class="col-auto mb-3">
                                        <a href="#" class="btn_start btn btn-success w125 btn-sm disabled" title="Начать/возобновить испытание">
                                            <i class="fa-solid fa-play"></i> Старт
                                        </a>
                                    </div>

                                    <div class="col-auto mb-3">
                                        <a href="#" class="btn_pause btn btn-success w125 btn-sm disabled" title="Приостановить испытание">
                                            <i class="fa-solid fa-pause"></i> Пауза
                                        </a>
                                    </div>

                                    <div class="col-auto mb-3">
                                        <a href="#" class="btn_stop btn btn-success w125 btn-sm disabled" title="Завершить испытание">
                                            <i class="fa-solid fa-stop"></i> Завершить
                                        </a>
                                    </div>

                                    <div class="col-auto mb-3 d-none">
                                        <a href="#" class="btn_create_protocol btn btn-primary w125 btn-sm disabled" title="Создать протокол">
                                            <i class="fa-solid fa-plus icon-fix"></i> Протокол
                                        </a>
                                    </div>

                                    <div class="col-auto mb-3">
                                        <a href="#" class="btn_group_measurement_sheet btn btn-primary w125 btn-sm disabled" title="Лист измерений">
                                            <i class="fa-solid fa-calculator"></i> Лист измерений
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="row mb-2">
                                    <div class="col text-center">
                                        Заявки
                                    </div>
                                    <div class="col text-center">
                                        Шифр проб
                                    </div>
                                    <div class="col text-center">
                                        Статус
                                    </div>
                                    <div class="col text-center">
                                        Испытания
                                    </div>
                                    <div class="col text-center">
                                        Объекты испытаний
                                    </div>
                                    <div class="col text-center">
                                        Ответственный
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <input type="text" id="filter-deal_title" class="form-control filter w-auto" value="">
                                    </div>
                                    <div class="col">
                                        <input type="text" id="filter-cipher" class="form-control filter w-auto" value="">
                                    </div>
                                    <div class="col">
                                        <select id="filter-status" class="form-control filter">
                                            <option value="">Все</option>
                                            <option value="1">Не начато</option>
                                            <option value="2">Начато</option>
                                            <option value="3">Завершено</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select id="filter-methods" class="form-control select2 filter" multiple>
                                            <?php foreach ($this->data['tz_methods_list'] as $item): ?>
                                                <option value="<?=$item['id']?>"><?=$item['view_gost']?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select id="filter-material" class="form-control select2 filter" multiple>
                                            <?php foreach ($this->data['tz_material_list'] as $item): ?>
                                                <option value="<?=$item['id']?>"><?=$item['name']?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select id="filter-user" class="form-control select2 filter" multiple>
<!--                                            <option value="">Все</option>-->
<!--                                            <option value="-1">Не назначен</option>-->
                                            <?php foreach ($this->data['user_list'] as $item): ?>
                                                <option value="<?=$item['ID']?>" <?=$_SESSION['SESS_AUTH']['USER_ID'] == $item['ID']? 'selected': ''?>><?=$item['user_name']?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col d-none">
                                        Кол-во заявок: <span class="count_deal"></span>
                                    </div>
                                    <div class="col">
                                        Кол-во испытаний: <span class="count_method"></span>
                                    </div>
                                    <div class="col"></div>
                                    <div class="col"></div>
                                    <div class="col"></div>
                                </div>

                                <table class="table -table-striped journal table-hover table-sm table-light row-border" id="journal_methods" style="font-size: 14px">
                                    <thead>
                                    <tr class="table-secondary align-middle">
                                        <th scope="col">Заявка</th>
                                        <th scope="col">Материал Проба</th>
                                        <th scope="col" class="text-nowrap">
                                            <label class=""><input class="form-check-input all-check" type="checkbox"> Всё</label>
                                        </th>
                                        <th scope="col">Методика</th>
                                        <th scope="col"></th>
                                        <th scope="col">Испытание</th>
                                        <th scope="col">Лист<br> измерения</th>
                                        <th scope="col">Ед. изм.</th>
                                        <th scope="col">ТУ</th>
                                        <th scope="col">Нормативное значение</th>
                                        <th scope="col">Фактическое значение</th>
                                        <th scope="col">Соответствие требованиям</th>
                                        <th scope="col">В ОА</th>
                                        <th scope="col">Ответственный</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--./panel-body-->
                </div>
            </div>
        </div>
    </div>
    <!--./form-result-->

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->

    <form id="measurementModalForm" class="bg-light mfp-hide col-md-10 m-auto p-3 position-relative"
          action="<?= URI ?>/result/saveMeasurementData/" method="post">

        <input type="hidden" name="deal_id" value="<?=$this->data['deal_id']?>">

        <div class="measurement_content">

        </div>
    </form>
    <!--./averageModalForm-->

    <form id="roomsModalForm" class="bg-light mfp-hide col-md-6 m-auto p-3 position-relative"
          action="<?= URI ?>/result/index/" method="post">
        <div class="title mb-3 h-2"></div>

        <div class="line-dashed-small"></div>
    </form>
    <!--./roomsModalForm-->


    <form id="gost_room_form" action="/ulab/result/saveRoomStart/" method="post" class="bg-light mfp-hide col-md-5 m-auto p-3 position-relative">
        <div class="title mb-3 h-2">
            Помещение
        </div>

        <div class="line-dashed-small"></div>

        <div class="gost_room_container">

        </div>

        <button type="submit" class="btn btn-primary">Старт</button>
    </form>
</div>
<!--./wrapper-card-->
