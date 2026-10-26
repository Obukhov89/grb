<style>
    .hud-dropdown .select2-results__options {
        max-height: 700px !important;
    }
</style>
<header class="header-requirement mb-4">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?= URI ?>/request/list/" title="Вернуться к списку">
                    <i class="fa-solid fa-list"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?= URI ?>/request/card/<?= $this->data['deal_id'] ?>"
                   title="Вернуться в карточку">
                    <i class="fa-solid fa-arrow-left-long"></i>
                </a>
            </li>
            <!--            <li class="nav-item me-2">-->
            <!--                <a class="nav-link link-card" href="#">-->
            <!--                    <svg class="icon" width="25" height="25">-->
            <!--                        <use xlink:href="--><? //=URI
            ?><!--/assets/images/icons.svg#card"/>-->
            <!--                    </svg>-->
            <!--                </a>-->
            <!--            </li>-->
            <!--            <li class="nav-item me-2">-->
            <!--                <a class="nav-link link-docs" href="#">-->
            <!--                    <svg class="icon" width="25" height="25">-->
            <!--                        <use xlink:href="--><? //=URI
            ?><!--/assets/images/icons.svg#docs"/>-->
            <!--                    </svg>-->
            <!--                </a>-->
            <!--            </li>-->
            <!--            <li class="nav-item me-2">-->
            <!--                <a class="nav-link link-doc-edit" href="#">-->
            <!--                    <svg class="icon" width="25" height="25">-->
            <!--                        <use xlink:href="--><? //=URI
            ?><!--/assets/images/icons.svg#doc-edit"/>-->
            <!--                    </svg>-->
            <!--                </a>-->
            <!--            </li>-->
            <li class="nav-item me-2">
                <a class="nav-link popup-help" href="/ulab/help/LIMS_Manual_Stand/Technical_spec_int/Tec_spec_int.html"
                   title="Техническая поддержка">
                    <i class="fa-solid fa-question"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>


<div class="wrapper-requirement m-auto">
    <h2 class="d-flex mb-3">
        Заявка <?= $this->data['deal_title'] ?? '' ?>
    </h2>


    <form class="form form-requirement" id="form_requirement" method="post" action="<?= URI ?>/requirement/updateTz/">

        <input type="hidden" id="tz_id" name="tz_id" value="<?= $this->data['tz_id'] ?>">
        <input type="hidden" id="deal_id" name="deal_id" value="<?= $this->data['deal_id'] ?>">
        <input type="hidden" id="clear_confirm" name="clear_confirm" value="0">
        <input type="hidden" id="deal_type_id" value="<?= $this->data['tz']['TYPE_ID'] ?>">
        <input type="hidden" id="user_groups" value='<?= $this->data['user_groups'] ?>'>


        <div class="panel panel-default">
            <header class="panel-heading">
                Общая информация
                <span class="tools float-end">
                    <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                    <a href="#" class="fa fa-chevron-up"></a>
                </span>
            </header>
            <div class="panel-body">
                <div class="wrapper-info-header">

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <label class="form-label mb-1">Основание для проведения испытаний</label>
                            <div>
                                <strong>
                                    <?php if (!empty($this->data['contract_number'])): ?>
                                        <?= $this->data['contract_type'] ?> №<?= $this->data['contract_number'] ?> от <?= $this->data['contract_date'] ?>
                                    <?php else: ?>
                                        Договор еще не составлен
                                    <?php endif; ?>
                                </strong>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label mb-1">Заказчик</label>
                            <div class="formation-protocol-reason">
                                <strong>
                                    <?= $this->data['company_title'] ?>
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <!-- <div class="form-group">
                                <label class="form-label mb-1" for="day_to_test">Срок проведения испытаний</label>
                                <div class="input-group mb-3">
                                    <input
                                        type="<?= $this->data['tz']['type_of_day'] == 'custom' ? 'date' : 'number' ?>"
                                        class="clear_confirm_change form-control <?= $this->data['tz']['type_of_day'] == 'custom' ? '' : 'number-only' ?> day-to-test"
                                        id="day_to_test"
                                        name="tz[DAY_TO_TEST]"
                                        value="<?= $this->data['tz']['DAY_TO_TEST'] ?>"
                                        aria-describedby="basic-addon2" readonly>
                                    <select class="input-group-text col-3 clear_confirm_change type_of_day" id="basic-addon2" name="tz[type_of_day]">
                                        <option value="work_day" <?= $this->data['tz']['type_of_day'] == 'work_day' ? 'selected' : '' ?>>рабочих дней</option>
                                        <option value="day" <?= $this->data['tz']['type_of_day'] == 'day' ? 'selected' : '' ?>>дней</option>
                                        <option value="month" <?= $this->data['tz']['type_of_day'] == 'month' ? 'selected' : '' ?>>месяц(ев)</option>
                                        <option value="custom" <?= $this->data['tz']['type_of_day'] == 'custom' ? 'selected' : '' ?>>своя дата</option>
                                    </select>
                                </div>
                            </div> -->
                        </div>
                        <div class="col-sm-6 d-flex flex-column justify-content-center">
                            <label class="form-label mb-1">Основание для формирования протокола</label>
                            <div class="formation-protocol-reason">
                                <strong>
                                    <?php if (!empty($this->data['act_number'])): ?>
                                        Акт приемки/передачи проб № <?= $this->data['act_number'] ?> от <?= $this->data['act_date'] ?>
                                    <?php else: ?>
                                        Пробы не поступили
                                    <?php endif; ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                <!--./wrapper-info-header-->
                <!-- 
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Испытания проводятся
                    </label>
                    <div class="col-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input " type="radio" name="tz[tests_for]" id="inlineRadio1" value="own" <?= $this->data['tz']['tests_for'] == 'own' || empty($this->data['tz']['tests_for']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio1">для собственных нужд</label>
                        </div>
                    </div>
                    <div class="col-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input " type="radio" name="tz[tests_for]" id="inlineRadio3" value="certification" <?= $this->data['tz']['tests_for'] == 'certification' || $this->data['tz']['TAKEN_SERT_ISP'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio3">для сертификации</label>
                        </div>
                    </div>
                    <div class="col-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input " type="radio" name="tz[tests_for]" id="inlineRadio55" value="declaration" <?= $this->data['tz']['tests_for'] == 'declaration' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio55">для декларации</label>
                        </div>
                    </div>
                    <div class="col-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input " type="radio" name="tz[tests_for]" id="inlineRadio56" value="forensic" <?= $this->data['tz']['tests_for'] == 'forensic' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio56">для проведения судебной экспертизы</label>
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div> -->

                <!-- <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Указывать нормативную документацию на объект испытаний
                    </label>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[is_indicate_compliance]" id="inlineRadio21" value="0" <?= $this->data['tz']['is_indicate_compliance'] == 0 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio21">нет</label>
                        </div>
                    </div>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[is_indicate_compliance]" id="inlineRadio22" value="1" <?= $this->data['tz']['is_indicate_compliance'] == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio22">да (указать)</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <input class="form-control" type="text" id="compliance" maxlength="255" name="tz[compliance]" value="<?= $this->data['tz']['compliance'] ?? '' ?>" placeholder="ГОСТ ТУ" <?= $this->data['tz']['is_indicate_compliance'] != 1 ? 'disabled' : '' ?>>
                    </div>
                    <div class="col-sm-2"></div>
                    <div class="col-sm-2"></div>
                    <div class="col-sm-2"></div>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[is_indicate_compliance]" id="inlineRadio60" value="2" <?= $this->data['tz']['is_indicate_compliance'] == 2 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio60">да (указать экземпляр ТУ, СТО)</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <input class="form-control" type="text" id="tu_sto" maxlength="255" name="tz[tu_sto]" value="<?= $this->data['tz']['tu_sto'] ?? '' ?>" placeholder="ТУ СТО" <?= $this->data['tz']['is_indicate_compliance'] != 2 ? 'disabled' : '' ?>>
                    </div>
                </div> -->

                <!-- <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Результаты испытаний (исследований) оформить
                    </label>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[presented_as]" id="inlineRadio31" value="protocol" <?= $this->data['tz']['presented_as'] == 'protocol' || empty($this->data['tz']['presented_as']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio31">ПИ с ЭЦП</label>
                        </div>
                    </div>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[presented_as]" id="inlineRadio32" value="another" <?= $this->data['tz']['presented_as'] == 'another' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio32">ПИ на бумажном носителе</label>
                        </div>
                    </div>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[presented_as]" id="inlineRadio33" value="no" <?= $this->data['tz']['presented_as'] == 'no' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio33">Без выдачи протокола</label>
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div> -->
                <!-- 
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Выдача протокола(ов) испытаний
                    </label>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[protocol_given]" id="inlineRadio50" value="fact" <?= $this->data['tz']['protocol_given'] == 'fact' || empty($this->data['tz']['protocol_given']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio50">по факту завершения испытаний в лаборатории (поэтапно)</label>
                        </div>
                    </div>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[protocol_given]" id="inlineRadio51" value="all" <?= $this->data['tz']['protocol_given'] == 'all' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio51">по окончанию всего комплекса испытаний (КПИ)</label>
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div> -->

                <!-- <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Необходимые сведения и документы для проведения испытаний (см. п.2.2.4)
                    </label>
                    <div class="col-sm-8 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="tz[add_info][]" id="inlineRadio41" value="passport" <?= in_array('passport', $this->data['tz']['add_info']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio41">особые сведения о физических, химических характеристиках продукции (состав, направления формования и т.д.)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="tz[add_info][]" id="inlineRadio43" value="consist" <?= in_array('consist', $this->data['tz']['add_info']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inlineRadio43">документ о качестве продукции</label>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-check-label" for="inlineRadio54">Иное</label>
                            <input class="form-control" type="text" placeholder="Иное" name="tz[req_inf]" id="inlineRadio54" value="<?= $this->data['tz']['req_inf'] ?>" style="width: 500px !important; min-width: 500px !important;">
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div> -->

                <!-- <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Планируемая дата доставки проб в ИЦ
                    </label>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-control" type="text" name="tz[probes_to_ic]" id="inlineRadio52" value="<?= $this->data['tz']['probes_to_ic'] ?>" style="min-width: 500px !important;">
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div> -->

                <!-- <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Необходимые дополнительные услуги
                    </label>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-control" type="text" name="tz[additional_services]" id="inlineRadio53" value="<?= $this->data['tz']['additional_services'] ?>" style="min-width: 500px !important;">
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div> -->
                <!-- 
                <div class="form-group">
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="sent_out_lims"
                            name="tz[sent_out_lims]"
                            value="1"
                            <?= !empty($this->data['tz']['sent_out_lims']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="sent_out_lims">Отправлено вне ЛИМС</label>
                    </div>
                </div> -->
            </div>
            <!--./panel-body-->
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                Объект испытаний
                <span class="tools float-end">
            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
            <a href="javascript:;" class="fa fa-chevron-up"></a>
        </span>
            </div>
            <div class="panel-body">
                <div class="row flex-nowrap">

                    <div class="col-auto">
                        <div class="js-sticky-widget2">
                            <div class="col-auto mb-3">
                                <a href="#add-material-modal-form" class="popup-with-form btn btn-success w125 btn-sm">
                                    <i class="fa-solid fa-plus icon-fix"></i> Объект
                                </a>
                            </div>

                            <div class="col-auto mb-3">
                                <a href="#add-probe-modal-form" class="popup-with-form btn btn-success w125 btn-sm">
                                    <i class="fa-solid fa-plus icon-fix"></i> Пробу
                                </a>
                            </div>

                            <div class="col-auto mb-3">
                                <a href="#edit-group-probe-modal-form"
                                   class="btn-group-edit popup-with-form btn btn-success w125 btn-sm disabled">
                                    <i class="fa-solid fa-pencil"></i> Группы
                                </a>
                            </div>

                            <div class="col-auto mb-3">
                                <a href="#replace-method-modal-form"
                                   class="btn-replace-method popup-with-form btn btn-success w125 btn-sm disabled">
                                    <i class="fa-solid fa-right-left"></i> Методики
                                </a>
                            </div>

                            <div class="col-auto mb-3">
                                <a href="#import-probes-from-xlsx"
                                   class="btn-import-probes popup-with-form btn btn-success w125 btn-sm disabled">
                                    <svg width="17px" height="17px" viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg" stroke="#000000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                           stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M9.29289 1.29289C9.48043 1.10536 9.73478 1 10 1H18C19.6569 1 21 2.34315 21 4V9C21 9.55228 20.5523 10 20 10C19.4477 10 19 9.55228 19 9V4C19 3.44772 18.5523 3 18 3H11V8C11 8.55228 10.5523 9 10 9H5V20C5 20.5523 5.44772 21 6 21H7C7.55228 21 8 21.4477 8 22C8 22.5523 7.55228 23 7 23H6C4.34315 23 3 21.6569 3 20V8C3 7.73478 3.10536 7.48043 3.29289 7.29289L9.29289 1.29289ZM6.41421 7H9V4.41421L6.41421 7ZM19 12C19.5523 12 20 12.4477 20 13V19H23C23.5523 19 24 19.4477 24 20C24 20.5523 23.5523 21 23 21H19C18.4477 21 18 20.5523 18 20V13C18 12.4477 18.4477 12 19 12ZM11.8137 12.4188C11.4927 11.9693 10.8682 11.8653 10.4188 12.1863C9.96935 12.5073 9.86526 13.1318 10.1863 13.5812L12.2711 16.5L10.1863 19.4188C9.86526 19.8682 9.96935 20.4927 10.4188 20.8137C10.8682 21.1347 11.4927 21.0307 11.8137 20.5812L13.5 18.2205L15.1863 20.5812C15.5073 21.0307 16.1318 21.1347 16.5812 20.8137C17.0307 20.4927 17.1347 19.8682 16.8137 19.4188L14.7289 16.5L16.8137 13.5812C17.1347 13.1318 17.0307 12.5073 16.5812 12.1863C16.1318 11.8653 15.5073 11.9693 15.1863 12.4188L13.5 14.7795L11.8137 12.4188Z"
                                                  fill="#ffffff"></path>
                                        </g>
                                    </svg>
                                    Импорт
                                </a>
                            </div>

                            <div class="col-auto mb-3">
                                <a href="#delete-method-modal-form"
                                   class="btn-delete-method popup-with-form btn btn-danger w125 btn-sm disabled">
                                    <i class="fa-solid fa-trash"></i> Методики
                                </a>
                            </div>

                            <div class="col-auto mb-3">
                                <a href=""
                                   class="btn-delete-probes popup-with-form btn btn-danger w125 btn-sm disabled">
                                    <i class="fa-solid fa-trash"></i> Пробы
                                </a>
                            </div>

                            <div class="col-auto mb-3">
                                <a href="#add-methods-modal-form"
                                   class="btn-add-methods popup-with-form btn btn-primary w125 btn-sm disabled">
                                    <i class="fa-solid fa-plus icon-fix"></i> Испытания
                                </a>
                            </div>

                            <div class="col-auto mb-3">
                                <a href="#add-to-link-deal-form"
                                   class="btn-link-deal popup-with-form btn btn-primary w125 btn-sm disabled">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Передать в
                                </a>
                            </div>

                            <div class="col-auto mb-3">
                                <select id="group-type" class="form-control" style="width: 125px">
                                    <option value="1" <?= $this->data['group_type'] == 1 ? 'selected' : '' ?>>
                                        Группировка по материалу
                                    </option>
                                    <option value="2" <?= $this->data['group_type'] == 2 ? 'selected' : '' ?>>
                                        Группировка по маркировке заказчика
                                    </option>
                                    <option value="3" <?= $this->data['group_type'] == 3 ? 'selected' : '' ?>>
                                        Группировка по материалу + маркировке заказчика
                                    </option>
                                    <option value="4" <?= $this->data['group_type'] == 4 ? 'selected' : '' ?>>
                                        Группировка по группе материала
                                    </option>
                                    <option value="5" <?= $this->data['group_type'] == 5 ? 'selected' : '' ?>>
                                        Группировка по материалу + группе + маркировке заказчика
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Показать: </span>
                                    <select id="filter-material" class="form-control select2 filter">
                                        <option value="">Все объекты испытаний</option>
                                        <?php foreach ($this->data['tz_material_list'] as $id => $materialName): ?>
                                            <option value="<?= $materialName['id'] ?>"><?= $materialName['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <label for="filter-group" class="input-group-text">Группы: </label>
                                    <select id="filter-group" class="form-control select2 filter">
                                        <option value="">Все группы</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" value="<?= $this->data['isLabHeader'] ?>" id="isLabHeader">
                        <table id="journal_material_2"
                               class="table table-striped journal table-hover table-sm table-light">
                            <thead>
                            <tr>
                                <th scope="col">Объект испытаний</th>
                                <th scope="col"></th>
                                <th scope="col">Проба</th>
                                <th scope="col">Группа</th>
                                <th scope="col">Маркировка заказчика (информация об объекте испытания)</th>
                                <th scope="col">Кол-во методик</th>
                                <th scope="col"></th>
                            </tr>
                            <tr class="header-search">
                                <th scope="col"></th>
                                <th scope="col" colspan="6">
                                    <input id="filter-cipher" type="text" class="form-control search filter"
                                           placeholder="Фильтр по шифру">
                                </th>
                            </tr>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="line-dashed"></div>

                <div class="wrapper-discount bg-light-secondary p-2"
                     style="<?= $this->data['isLabHeader'] ? 'display: none;' : '' ?>">
                    <div class="row justify-content-end">
                        <div class="col-auto d-flex flex-column">
                            <div>
                                <label class="form-label">Итого</label>
                            </div>
                            <div>
                        <span id="str_total" class="total">
                            <?= $this->data['tz']['price_discount'] ?> руб.
                        </span>
                            </div>
                            <input id="price-total" type="hidden" name="tz[PRICE]"
                                   value="<?= $this->data['tz']['PRICE'] ?>">
                            <input id="price_discount" type="hidden" name="tz[price_discount]"
                                   value="<?= $this->data['tz']['price_discount'] ?? $this->data['tz']['PRICE'] ?>">
                            <!-- Новое поле для хранения базовой цены БЕЗ НДС -->
                            <input id="base-price-without-nds" type="hidden" value="<?= $this->data['tz']['PRICE'] ?>">
                            <input type="hidden" name="tz[is_nds]" value="<?= $this->data['tz']['is_nds'] ?? '0' ?>">
                            <input type="hidden" name="tz[is_nds_7]"
                                   value="<?= $this->data['tz']['is_nds_7'] ?? '0' ?>">
                        </div>

                        <div class="col-auto flex-column is_nds_block <?= ($this->data['tz']['is_nds'] || $this->data['tz']['is_nds'] == '') ? 'd-flex' : 'd-none' ?>">
                            <div>
                                <label class="form-label">НДС</label>
                            </div>
                            <div class="is_nds_block">
                        <span id="str_total_nds" class="total">
                            <?php
                            $ndsMultiplier = 0;
                            if (($this->data['tz']['is_nds'] ?? 0) && !($this->data['tz']['is_nds_7'] ?? 0)) {
                                $ndsMultiplier = 0.05;
                            } elseif (($this->data['tz']['is_nds'] ?? 0) && ($this->data['tz']['is_nds_7'] ?? 0)) {
                                $ndsMultiplier = 0.07;
                            }
                            echo number_format((float)$this->data['tz']['price_discount'] * $ndsMultiplier, 2, ',', '');
                            ?> руб.
                        </span>
                            </div>
                        </div>

                        <div class="col-auto d-flex flex-column is_nds_block">
                            <div>
                                <label class="form-label mb-1">%</label>
                            </div>
                            <div>
                                <div class="d-flex align-items-center taken-request-wrapper">
                                    <div>
                                        <select class="form-control nds-percent-select w100" name="">
                                            <option value="7" <?= (($this->data['tz']['is_nds'] ?? 0) && ($this->data['tz']['is_nds_7'] ?? 0)) ? 'selected' : '' ?>>
                                                7%
                                            </option>
                                            <option value="5" <?= (($this->data['tz']['is_nds'] ?? 0) && !($this->data['tz']['is_nds_7'] ?? 0)) ? 'selected' : '' ?>>
                                                5%
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-auto d-flex flex-column">
                            <div>
                                <label class="form-label mb-1">НДС</label>
                            </div>
                            <div>
                                <div class="d-flex align-items-center taken-request-wrapper">
                                    <div>
                                        <label class="switch">
                                            <input class="form-check-input nds" name="tz[is_nds]"
                                                   type="checkbox" <?= ($this->data['tz']['is_nds'] || $this->data['tz']['is_nds'] == '') ? 'checked' : '' ?> />
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-auto d-flex flex-column">
                            <div>
                                <label class="form-label" for="input_discount">Скидка</label>
                            </div>
                            <div>
                                <div class="input-group">
                                    <input name="tz[DISCOUNT]" type="number"
                                           class="form-control bg-white discount-input" min="0" step="0.01"
                                           value="<?= $this->data['tz']['DISCOUNT'] ?? '0' ?>">
                                    <select name="tz[discount_type]" class="form-control bg-white discount-type">
                                        <option value="percent" <?= $this->data['tz']['discount_type'] == 'percent' ? 'selected' : '' ?>>
                                            %
                                        </option>
                                        <option value="rub" <?= $this->data['tz']['discount_type'] == 'rub' ? 'selected' : '' ?>>
                                            ₽
                                        </option>
                                    </select>
                                    <button type="button" class="btn btn-primary discount-apply">Применить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                Назначение исполнителей
                <span class="tools float-end">
            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
            <a href="javascript:;" class="fa fa-chevron-up"></a>
        </span>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col">
                        <?php if (!empty($this->data['lab_head']['user'])): ?>
                            <?php foreach ($this->data['lab_head']['user'] as $user): ?>
                                <div class="head-user-block <?= $user['user_id'] == $this->data['curr_user'] ? 'curr_user' : '' ?>">
                                    <?php if ($user['is_confirm'] == CHECK_TZ_NOT_SENT && $this->data['check_state'] != CHECK_TZ_NOT_SENT): ?>
                                        <span class="icon" title="ТЗ не отправлено">
                                    <i class="fa-solid fa-minus"></i>
                                </span>
                                    <?php elseif ($user['is_confirm'] == CHECK_TZ_NOT_SENT): ?>
                                        <span class="icon" title="ТЗ не отправлено">
                                    <i class="fa-regular fa-paper-plane"></i>
                                </span>
                                    <?php elseif ($user['is_confirm'] == CHECK_TZ_APPROVE): ?>
                                        <span class="text-green icon" title="ТЗ потверждено">
                                    <i class="fa-regular fa-circle-check"></i>
                                </span>
                                    <?php elseif ($user['is_confirm'] == CHECK_TZ_NOT_APPROVE): ?>
                                        <span class="text-red icon" title="ТЗ не потверждено">
                                    <i class="fa-regular fa-circle-xmark"></i>
                                </span>
                                    <?php else: ?>
                                        <span class="icon" title="Ожидание проверки">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                </span>
                                    <?php endif; ?>

                                    <span class="<?= $user['user_id'] == $this->data['curr_user'] ? 'fw-bold' : '' ?>"><?= $user['short_name'] ?></span>
                                    <span><i><?= $user['duration_str']; ?></i></span>

                                    <?php if ($user['is_confirm'] == CHECK_TZ_NOT_APPROVE): ?>
                                        <span>
                                    <button type="button" class="btn btn-danger delete-canceled-lab"
                                            data-user-id="<?= $user['user_id'] ?>">Удалить</button>
                                </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>


                            <?php if (!empty($this->data['lab_head']['user']) && $this->data['check_state'] == CHECK_TZ_APPROVE): ?>
                                <div class="mt-1">
                                    <label class="form-label text-green fw-bold">Исполнители назначены.</label>
                                </div>
                            <?php endif; ?>

                            <div class="mt-3">
                                <?php if ($this->data['lab_head']['is_curr_user']): ?>
                                    <?php if ($this->data['check_state'] == CHECK_TZ_NOT_SENT): ?>
                                        <button type="button"
                                                class="aa btn btn-primary sent_approve_tz <?= $this->data['lab_head']['check_state'] == CHECK_TZ_NOT_SENT ? 'disable' : '' ?>">
                                            <i class="fa-regular fa-paper-plane"></i> Назначить
                                        </button>
                                    <?php else: ?>
                                        <button type="button"
                                                class="bb btn btn-success me-3 approve_tz
                                                <?= ($this->data['check_state'] == CHECK_TZ_WAIT || $this->data['check_state'] == CHECK_TZ_NOT_APPROVE) && $this->data['lab_head']['curr_user_status'] != 1 ? '' : 'disable' ?>">
                                            <i class="fa-regular fa-circle-check"></i> Назначить
                                        </button>
                                        <a href="#return-modal-form"
                                           class="btn btn-danger me-3 not_approve_tz popup-with-form
                                                <?= ($this->data['check_state'] == CHECK_TZ_WAIT || $this->data['check_state'] == CHECK_TZ_NOT_APPROVE) && $this->data['lab_head']['curr_user_status'] != 1 ? '' : 'disable' ?>"><i
                                                    class="fa-regular fa-circle-xmark"></i> Вернуть</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="#send-modal-form"
                                       class="btn btn-primary popup-with-form <?= ($this->data['check_state'] == CHECK_TZ_NOT_SENT || !empty($this->data['cancel_lab_head'])) ? '' : 'disable' ?>"><i
                                                class="fa-regular fa-paper-plane"></i> Передать</a>
                                <?php endif; ?>
                            </div>

                        <?php else: ?>
                            <span class="fw-bold">Сохраните техническое задание</span>
                        <?php endif; ?>
                    </div>
                    <?php if (in_array($this->data['curr_user'], array_column($this->data['lab_head']['user'], 'user_id'))): ?>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">Ориентировочная дата начала <span
                                            class="redStars">*</span></label>

                                <input type="date" id="date-start-leader" name="form_leader[date_start]"
                                       class="form-control"
                                       value="<?= $this->data['form_leader']['date_start'] ?? '' ?>"
                                       onkeydown="return false" required min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="">
                                <label class="form-label">Количество рабочих дней <span
                                            class="redStars">*</span></label>

                                <input type="hidden" id="leader-id" name="form_leader[user_id]"
                                       value="<?= $this->data['curr_user'] ?>">
                                <input type="number" id="days-leader" name="form_leader[days]" class="form-control"
                                       min="0" step="1" value="<?= $this->data['form_leader']['days'] ?? '1' ?>"
                                       required>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($this->data['confirm_not_approve_history'])): ?>
                        <div class="col">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center" scope="col" colspan="3">Не одобренное подтверждение</th>
                                    <th class="text-center" scope="col">Исправлено</th>
                                </tr>
                                <tr>
                                    <th class="text-center">
                                        Дата
                                    </th>
                                    <th class="text-center">
                                        Руководитель
                                    </th>
                                    <th class="text-center">
                                        Комментарий
                                    </th>
                                    <th>

                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php foreach ($this->data['confirm_not_approve_history'] as $item): ?>
                                    <tr>
                                        <td>
                                            <?= $item['date_return'] ?>
                                        </td>
                                        <td>
                                            <?= $item['user_return_name'] ?>
                                        </td>
                                        <td>
                                            <?= $item['desc_return'] ?>
                                        </td>
                                        <td class="text-center check_fix_container">
                                            <?php if ($item['is_fixed']): ?>
                                                <?= $item['date_fixed'] ?> <?= $item['user_fix_name'] ?>
                                            <?php else: ?>
                                                <!--                                                    <a href="#" class="check_fix" data-id="--><? //=$item['id']
                                                ?><!--" data-tz_id="--><? //=$item['tz_id']
                                                ?><!--" data-leader="--><? //=$item['user_return']
                                                ?><!--">Отправить на согласование</a>-->
                                                --
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!--./panel-body-->
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-10">
                        <textarea style="min-width: 100%" form="createComment" name="comment" class="form-control"
                                  placeholder="Комментарий" required><?= $this->data['comment'] ?></textarea>
                        <input type="hidden" form="createComment" name="oldComment"
                               value="<?= $this->data['comment'] ?>">
                        <input name="tz_id" form="createComment" value="<?= $this->data['tz_id'] ?>" type="hidden">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary m-0" form="createComment" type="submit" id="button-addon2">
                            Добавить
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                История изменений сроков
                <span class="tools float-end">
            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
            <a href="javascript:;" class="fa fa-chevron-up"></a>
        </span>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col">
                        <?php if (!empty($this->data['changes_history_duration'])): ?>
                            <div class="changes-list">
                                <?php foreach ($this->data['changes_history_duration'] as $change2): ?>
                                    <div class="change-item mb-2 pb-2 border-bottom">
                                        <div class="row">
                                            <div class="col" style="min-width: 100px;">
                                                <?= date('d.m.Y', strtotime($change2['date_start'])) ?>
                                            </div>
                                            <div class="col" style="min-width: 150px;">
                                                <?= $change2['user_short'] ?>
                                            </div>
                                            <div class="col">
                                                <?= $change2['days'] . ' д.' ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>История изменений отсутствует</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!--./panel-body-->
        </div>
        <div class="panel panel-default">
            <header class="panel-heading">
                История изменений комментариев
                <span class="tools float-end">
            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
            <a href="javascript:;" class="fa fa-chevron-up"></a>
        </span>
            </header>
            <div class="panel-body">
                <div class="row mb-2">
                    <div class="col" style="font-weight: bold">Старый комментарий</div>
                    <div class="col" style="font-weight: bold">Новый комментарий</div>
                    <div class="col" style="font-weight: bold">Пользователь</div>
                    <div class="col" style="font-weight: bold">Дата изменения</div>
                </div>
                <?php foreach ($this->data['comment_history'] as $value): ?>
                    <div class="row mb-2 <?= $value['bg'] ?>">
                        <div class="col"><?= $value['old_comment'] ?></div>
                        <div class="col"><?= $value['new_comment'] ?></div>
                        <div class="col"><?= $value['user_short'] ?></div>
                        <div class="col"><?= $value['date'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($this->data['is_disable_save']): ?>
            <button class="form-control btn btn-primary mw-100 disabled" type="button">Сохранить</button>
        <?php else: ?>
            <!--            --><?php //if (!empty($this->data['lab_head']['user']) && $this->data['check_state'] != CHECK_TZ_NOT_SENT):
            ?>
            <!--                <label class="form-label text-red">Техническое задание на проверке. При нажатии "Сохранить" отзовет проверку</label>-->
            <!--                <button class="form-control btn btn-primary mw-100 save" id="save" name="save" onclick="return confirm('Техническое задание на проверке. При нажатии Сохранить отзовет проверку! Продолжить?')" type="submit">Сохранить</button>-->
            <!--            --><?php //else:
            ?>
            <button class="form-control btn btn-primary mw-100 save" id="save" name="save" type="submit">Сохранить
            </button>
            <!--            --><?php //endif;
            ?>
        <?php endif; ?>

    </form>
    <!--./form-requirement-->
</div>
<!--./wrapper-requirement-->

<form id="createComment" action="<?= URI ?>/request/addCommentFromTz/<?= $this->data['deal_id'] ?>" method="post">
</form>

<form id="edit-group-probe-modal-form" class="bg-light mfp-hide col-6 m-auto p-3 position-relative" method="post"
      action="<?= URI ?>/requirement/editProbe/">
    <div class="title mb-3 h-2">
        Групповое редактирование проб
    </div>

    <div class="line-dashed-small"></div>

    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
    <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">
    <input class="probe-id-list" name="probe_id" value="" type="hidden">

    <div class="row mb-3">
        <label for="inputEmail3" class="col col-form-label">Выбрано проб: <span
                    class="count-selected-probe"></span></label>
    </div>

    <div class="mb-3">
        <label class="form-label"><strong>Маркировка заказчика (информация об объекте испытания)</strong></label>

        <div class="input-group">
            <div class="input-group-text">
                <input class="form-check-input mt-0 disabled_name_for_protocol" type="checkbox">
            </div>
            <input type="text" name="form[name_for_protocol]" class="form-control name_for_protocol" disabled>
        </div>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input is_only_name_for_protocol" id="is_only_name_for_protocol"
               value="1" name="form[is_only_name_for_protocol]">

        <label class="form-check-label" for="is_only_name_for_protocol">Выводить только маркировку заказчика</label>
    </div>

    <!--	<div class="line-dashed-small"></div>-->

    <!--    <div class="mb-3">-->
    <!--        <label class="form-label"><strong>Группа объекта испытаний</strong></label>-->
    <!---->
    <!--		<div class="block-group-probe-group">-->
    <!---->
    <!--		</div>-->
    <!---->
    <!--        <select class="form-control select2 select_group" name="form[group]" disabled>-->
    <!--            <option value="">Без группы</option>-->
    <!--        </select>-->
    <!--    </div>-->

    <div class="line-dashed-small"></div>

    <div class="mb-3">
        <label class="form-label"><strong>Карьер</strong></label>

        <div class="input-group">
            <div class="input-group-text">
                <input class="form-check-input mt-0 disabled_quarry_id" type="checkbox">
            </div>
            <select class="form-control select2 quarry_id" name="form[quarry_id]" disabled>
                <option value="">Нет карьера</option>
                <?php foreach ($this->data['quarry_list'] as $item): ?>
                    <option value="<?= $item['ID'] ?>"><?= $item['NAME'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="block-group-probe-method-line line-dashed-small"></div>

    <div class="block-group-probe-method">

    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex justify-content-between">
        <button type="submit" name="button" value="save" class="btn btn-primary">Сохранить</button>

        <button type="submit" name="button" value="delete" class="btn btn-danger delete-probe-btn">Удалить</button>
    </div>
</form>


<form id="edit-probe-modal-form" class="bg-light mfp-hide col-6 m-auto p-3 position-relative" method="post"
      action="<?= URI ?>/requirement/editProbe">
    <div class="title mb-3 h-2">
        Редактирование пробы
    </div>

    <div class="line-dashed-small"></div>

    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
    <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">
    <input name="probe_id" class="probe_id" value="" type="hidden">

    <div class="mb-3">
        <label class="form-label">Маркировка заказчика (информация об объекте испытания)</label>

        <input type="text" name="form[name_for_protocol]" class="form-control name_for_protocol">
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input is_only_name_for_protocol" id="is_only_name_for_protocol2"
               value="1" name="form[is_only_name_for_protocol]">

        <label class="form-check-label" for="is_only_name_for_protocol2">Выводить только маркировку заказчика</label>
    </div>

    <div class="mb-3">
        <label class="form-label">Группа объекта испытаний</label>

        <select class="form-control select2 select_group clear_confirm_change" name="form[group]">
            <option value="">Без группы</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Карьер</label>

        <select class="form-control select2 clear_confirm_change quarry_id" name="form[quarry_id]">
            <option value="">Нет карьера</option>
            <?php foreach ($this->data['quarry_list'] as $item): ?>
                <option value="<?= $item['ID'] ?>"><?= $item['NAME'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex justify-content-between">
        <button type="submit" name="button" value="save" class="btn btn-primary">Сохранить</button>

        <button type="submit" name="button" value="delete" class="btn btn-danger delete-probe-btn">Удалить</button>
    </div>
</form>


<form id="edit-material-modal-form" class="bg-light mfp-hide col-6 m-auto p-3 position-relative" method="post"
      action="<?= URI ?>/requirement/editMaterial">
    <div class="title mb-3 h-2">
        Редактирование объекта испытаний
    </div>

    <div class="line-dashed-small"></div>

    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
    <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">
    <input name="old_material_id" class="old_material_id" value="" type="hidden">

    <div class="mb-3">
        <label class="form-label">Объект испытаний <span class="redStars">*</span></label>

        <select class="form-control select2 material_id" name="material_id" required>
            <?php foreach ($this->data['material_list'] as $material): ?>
                <option value="<?= $material['ID'] ?>"><?= $material['NAME'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" name="button" value="save" class="btn btn-primary">Сохранить</button>
</form>

<form id="edit-material-info-form" class="bg-light mfp-hide col-6 m-auto p-3 position-relative" method="post"
      action="<?= URI ?>/requirement/setDopInfo">
    <div class="title mb-3 h-2">
        Информация о пробе
    </div>

    <div class="line-dashed-small"></div>

    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
    <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">

    <div class="mb-3" id="dopInfoContainer">

    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" name="button" value="save" class="btn btn-primary">Сохранить</button>
</form>


<form id="add-methods-modal-form" class="bg-light mfp-hide col-12 m-auto p-3 position-relative" method="post"
      action="<?= URI ?>/requirement/addMethodsToProbe/">
    <div class="title mb-3 h-2">
        Добавление методик
    </div>

    <div class="line-dashed-small"></div>

    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
    <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">
    <input class="probe-id-list" name="probe_id_list" value="" type="hidden">

    <div class="row mb-3">
        <label for="inputEmail3" class="col col-form-label">Выбрано проб: <span
                    class="count-selected-probe"></span></label>
    </div>

    <div class="mb-3">
        <label class="form-label">Схема</label>
        <div class="input-group">
            <select class="form-control select2" id="select-scheme">
                <option value="">Нет схемы / ручной ввод</option>
            </select>
            <button type="button" class="btn btn-primary" id="apply-scheme">Применить</button>
        </div>
    </div>

    <!--    <div class="row">-->
    <!--        <div class="col-3">-->
    <!--            <label class="form-label mb-1">Методика испытаний <span class="redStars">*</span></label>-->
    <!--        </div>-->
    <!--        <div class="col-2">-->
    <!--            <label class="form-label mb-1">Нормативный документ</label>-->
    <!--        </div>-->
    <!--        <div class="col-2">-->
    <!--            <label class="form-label mb-1">Единицы измерения</label>-->
    <!--        </div>-->
    <!--        <div class="col-2">-->
    <!--            <label class="form-label mb-1">Допустимое значение</label>-->
    <!--        </div>-->
    <!--        <div class="col-2">-->
    <!--            <label class="form-label mb-1">Исполнитель</label>-->
    <!--        </div>-->
    <!--        <div class="col">-->
    <!--            <label class="form-label mb-1">Цена</label>-->
    <!--        </div>-->
    <!--    </div>-->

    <div class="method-container mb-3">

    </div>

    <div class="row justify-content-center">
        <div class="col-auto">
            <button type="button" class="btn btn-success w150 add-new-method">
                <i class="fa-solid fa-plus icon-fix"></i> Методику
            </button>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Добавить</button>
</form>


<form id="add-material-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" method="post"
      action="<?= URI ?>/requirement/addMaterialToTz/">
    <div class="title mb-3 h-2">
        Добавление объекта испытаний
    </div>

    <div class="line-dashed-small"></div>

    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
    <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">

    <div class="mb-3">
        <label class="form-label">Объект испытаний <span class="redStars">*</span></label>

        <select class="form-control select2" name="material_id" required>
            <option value="">Выбрать объект испытаний</option>
            <?php foreach ($this->data['material_list'] as $material): ?>
                <option value="<?= $material['ID'] ?>"><?= $material['NAME'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Количество проб <span class="redStars">*</span></label>

        <input type="number" class="form-control bg-white" name="number" min="1" value="1" required>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Добавить</button>
</form>


<form id="add-comment-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" method="post"
      action="<?= URI ?>/requirement/setCommentGtp/">
    <div class="title mb-3 h-2">
        Добавление комментария к испытанию
    </div>

    <div class="line-dashed-small"></div>

    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
    <input name="ugtp_id" class="ugtp_id" value="" type="hidden">
    <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">

    <div class="mb-3">
        <label class="form-label">Комментарий</label>

        <textarea name="form[comment]" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Очередность выдачи протоколов</label>

        <input type="text" class="form-control" name="form[sequence]">
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Добавить</button>
</form>


<form id="add-probe-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" method="post"
      action="<?= URI ?>/requirement/addProbeToMaterial/">
    <div class="title mb-3 h-2">
        Добавление проб
    </div>

    <div class="line-dashed-small"></div>

    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
    <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">

    <div class="mb-3">
        <label class="form-label">Объект испытаний (мультивыбор) <span class="redStars">*</span></label>

        <?php $selected = count($this->data['tz_material_list']) == 1 ? 'selected' : '' ?>
        <select class="form-control select2" name="material_id[]" multiple required>
            <option value="" disabled>Выбрать объект испытаний</option>
            <?php foreach ($this->data['tz_material_list'] as $id => $item): ?>
                <option value="<?= $item['id'] ?>" <?= $selected ?>><?= $item['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Количество проб <span class="redStars">*</span></label>

        <input type="number" name="number" class="form-control bg-white" min="1" value="1" required>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Добавить</button>
</form>


<form id="add-to-link-deal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" method="post"
      action="<?= URI ?>/requirement/copyProbeToDeal">
    <div class="title mb-3 h-2">
        Добавление проб в учтенную заявку
    </div>

    <div class="line-dashed-small"></div>

    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
    <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">
    <input class="probe-id-list" name="probe_id_list" value="" type="hidden">

    <div class="row mb-3">
        <label for="inputEmail3" class="col col-form-label">Выбрано проб: <span
                    class="count-selected-probe"></span></label>
    </div>

    <div class="mb-3">
        <label class="form-label">Дочерние заявки <span class="redStars">*</span></label>

        <?php if (!empty($this->data['taken_deal_list'])): ?>
            <select class="form-control select2" name="child_deal_id" required>
                <?php foreach ($this->data['taken_deal_list'] as $item): ?>
                    <option value="<?= $item['id'] ?>" <?= count($this->data['taken_deal_list']) == 1 ? 'selected' : '' ?>><?= $item['title'] ?></option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <div>
                Нет дочерних заявок
            </div>
        <?php endif; ?>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Добавить</button>
</form>


<div id="return-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Причина возврата
    </div>

    <div class="line-dashed-small"></div>

    <textarea class="form-control" id="desc_return" rows="5" required></textarea>

    <div class="line-dashed-small"></div>

    <button type="button" class="btn btn-primary not_approve_tz_btn">Отправить</button>
</div>


<form id="send-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
      action="/ulab/requirement/<?= (!empty($this->data['lab_head']['user']) && $this->data['check_state'] == CHECK_TZ_NOT_SENT) ? 'confirmTzSent' : 'confirmTzSentForCanceled' ?>/"
      method="post">
    <div class="title mb-3 h-2">
        Отправка руководителям на утверждение исполнителей
    </div>

    <div class="line-dashed-small"></div>

    <?php if (!empty($this->data['lab_head']['user']) && $this->data['check_state'] == CHECK_TZ_NOT_SENT): ?>
        <?php foreach ($this->data['lab_head']['user'] as $user): ?>
            <div class="head-user-block form-check <?= $user['user_id'] == $this->data['curr_user'] ? 'curr_user' : '' ?>">
                <input id="send_user_<?= $user['user_id'] ?>" type="checkbox" name="users[]" class="form-check-input"
                       value="<?= $user['user_id'] ?>" checked>

                <label class="form-check-label" for="send_user_<?= $user['user_id'] ?>">
                    <span class="<?= $user['user_id'] == $this->data['curr_user'] ? 'fw-bold' : '' ?>"><?= $user['short_name']; ?></span>
                </label>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($this->data['lab_head']['user']) && $this->data['check_state'] == CHECK_TZ_APPROVE): ?>
            <div class="mt-1 confirmed-tz">
                <label class="form-label text-green fw-bold">Техническое задание утверждено.</label>
            </div>
        <?php endif; ?>

    <?php elseif (!empty($this->data['cancel_lab_head'])): ?>
        <?php foreach ($this->data['cancel_lab_head'] as $user): ?>
            <div class="head-user-block form-check <?= $user['ID'] == $this->data['curr_user'] ? 'curr_user' : '' ?>">
                <input id="send_user_<?= $user['ID'] ?>" type="checkbox" name="users[]" class="form-check-input"
                       value="<?= $user['ID'] ?>" checked>

                <label class="form-check-label" for="send_user_<?= $user['user_id'] ?>">
                    <span class="<?= $user['ID'] == $this->data['curr_user'] ? 'fw-bold' : '' ?>"><?= $user['short_name']; ?></span>
                </label>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <span class="fw-bold">Сохраните техническое задание</span>
    <?php endif; ?>

    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
    <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary"><i class="fa-regular fa-paper-plane"></i> Передать</button>
</form>

<form id="replace-method-modal-form" class="bg-light mfp-hide col-6 m-auto p-3 position-relative"
      action="/ulab/requirement/replaceMethodsForProbes/" method="post">
    <div class="title mb-3 h-2">
        Замена методики
    </div>

    <div class="line-dashed-small"></div>

    <div class="col mb-3">
        <label for="old-method">Старая методика</label>
        <div class="input-group">
            <select id="old-method" class="form-control select2 tu-select" name="old_method"></select>
        </div>
    </div>

    <div class="col">
        <div class="mb-3">
            <label for="new-method">Новая методика</label>
            <div class="input-group">
                <select id="new-method" class="form-control select2 tu-select" name="method[id]">
                    <option value="">--</option>

                    <?php foreach ($this->data['method_list'] as $method): ?>
                        <?php
                        if ($method['is_actual'] == 0) {
                            $dataColor = 'data-color="#F00"';
                        } elseif ($method['in_field'] == 0) {
                            $dataColor = 'data-color="#060060"';
                        } elseif ($method['is_confirm'] == 0) {
                            $dataColor = 'data-color="#dfdf11"';
                        } else {
                            $dataColor = '';
                        }
                        ?>
                        <option value="<?= $method['id'] ?>" <?= $dataColor ?>><?= $method['view_gost'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="method-characteristics-block d-none">
            <div class="method-characteristics-form mb-3"></div>
        </div>
    </div>

    <input type="hidden" name="tz_id" value="<?= $this->data['tz_id'] ?>">
    <input type="hidden" id="deal_id" name="deal_id" value="<?= $this->data['deal_id'] ?>">

    <div id="probe-ids-container"></div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Заменить</button>
</form>

<form id="import-probes-from-xlsx" class="bg-light mfp-hide col-6 m-auto p-3 position-relative"
      action="/ulab/requirement/importProbesFromExcel" enctype="multipart/form-data" method="post">
    <div class="title mb-3 h-2">
        Импортирование маркировки заказчика
    </div>

    <div class="line-dashed-small"></div>

    <div class="col mb-3">
        <label for="excel-file">Excel файл заказчика</label>
        <input type="file" class="form-control" id="excel-file" name="file"
               accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
               required>
    </div>

    <div class="col mb-3">
        <label for="from-letter">Интервал импорта (укажите с какой ячейки до какой необходимо импортировать
            данные)</label>

        <div class="d-flex flex-column gap-2">
            <div class="input-group">
                <span style="margin: auto 5px auto 0;">От</span>
                <input type="text" class="form-control" id="from-letter" value="A" name="from-letter"
                       style="margin-right: 5px" placeholder="Столбец от" required>
                <span style="margin: auto">:</span>
                <input type="number" class="form-control" id="from-digit" value="4" name="from-digit"
                       style="margin-left: 5px" placeholder="Строка от" required>
            </div>

            <div class="input-group">
                <span style="margin: auto 5px auto 0;">До</span>
                <input type="text" class="form-control" id="to-letter" value="E" name="to-letter"
                       style="margin-right: 5px" placeholder="Столбец до" required>
                <span style="margin: auto">:</span>
                <input type="number" class="form-control" id="to-digit" value="10" name="to-digit"
                       style="margin-left: 5px" placeholder="Строка до" required>
            </div>
        </div>
    </div>

    <div class="col mb-3">
        <label for="format-cipher">Формат маркировки заказчика (укажите как должны присваиваться номера столбцов в
            фигурных скобках)</label>
        <input type="text" class="form-control" id="format-cipher" name="format_cipher"
               value="{1} {2} (от {3} до {4}, длина {5})" required>
    </div>

    <input type="hidden" name="tz_id" value="<?= $this->data['tz_id'] ?>">
    <input type="hidden" id="deal_id" name="deal_id" value="<?= $this->data['deal_id'] ?>">

    <div id="probe-ids-container"></div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Заменить</button>
</form>


<form id="delete-method-modal-form" class="bg-light mfp-hide col-6 m-auto p-3 position-relative"
      action="/ulab/requirement/deleteMethodsFromProbe/" method="post">
    <div class="title mb-3 h-2">
        Удаление методики
    </div>

    <div class="line-dashed-small"></div>

    <div class="col mb-3">
        <label for="method-to-delete">Методика для удаления</label>
        <div class="input-group">
            <select id="method-to-delete" class="form-control select2 tu-select" name="method_id"></select>
        </div>
    </div>

    <div id="delete-ids-container"></div>

    <input type="hidden" name="tz_id" value="<?= $this->data['tz_id'] ?>">

    <button type="submit" class="btn btn-danger">Удалить</button>
</form>

<form action="/ulab/requirement/copyProbeMassiveAjax/" class="bg-light mfp-hide col-6 m-auto p-3 position-relative"
      method="post" id="copy-count-modal-form">
    <div class="title mb-3 h-2">
        Копирование пробы
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" class="count_umtr_id" name="umtr_id" value="">

    <input type="number" name="count" class="form-control bg-white mb-3" min="1" value="1" required>

    <button type="submit" class="btn btn-primary">Скопировать</button>
</form>