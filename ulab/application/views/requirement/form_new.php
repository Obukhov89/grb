<header class="header-requirement mb-4">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?= URI ?>/request/card/<?=$this->data['deal_id']?>" title="Вернуться в карточку">
                    <i class="fa-solid fa-arrow-left-long"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link href="<?= URI ?>/request/list/" title="Вернуться в журнал заявок">
                    <i class="fa-solid fa-list"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link link-card" href="#">
                    <svg class="icon" width="25" height="25">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#card"/>
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link link-docs" href="#">
                    <svg class="icon" width="25" height="25">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#docs"/>
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link link-doc-edit" href="#">
                    <svg class="icon" width="25" height="25">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#doc-edit"/>
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link popup-help" href="/ulab/help/LIMS_Manual_Stand/Technical_spec_int/Tec_spec_int.html" title="ПОМОГИТЕ">
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


    <form class="form form-requirement" id="form_requirement" method="post" action="<?=URI?>/requirement/updateTz/">

        <input type="hidden" id="tz_id" name="tz_id" value="<?= $this->data['tz_id'] ?>">
        <input type="hidden" id="deal_id" name="deal_id" value="<?= $this->data['deal_id'] ?>">
        <input type="hidden" id="clear_confirm" name="clear_confirm" value="0">


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
                                    <?php if ( !empty($this->data['contract_number']) ): ?>
                                        <?= $this->data['contract_type'] ?> №<?= $this->data['contract_number'] ?> от <?= $this->data['contract_date'] ?>
                                    <?php else: ?>
                                        Договор еще не составлен
                                    <?php endif; ?>
                                </strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
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
                    <div class="row">
                        <div class="form-group">
                            <label class="form-label mb-1" for="day_to_test">Срок проведения испытаний</label>
                            <div class="input-group mb-3">
                                <input type="number" class="clear_confirm_change form-control number-only day-to-test" id="day_to_test" name="tz[DAY_TO_TEST]" value="<?= $this->data['tz']['DAY_TO_TEST']?? 20 ?>" aria-describedby="basic-addon2">
                                <select class="input-group-text col-3 clear_confirm_change" id="basic-addon2" name="tz[type_of_day]">
                                    <option value="work_day" <?=$this->data['tz']['type_of_day'] == 'work_day' ? 'selected' : ''?>>рабочих дней</option>
                                    <option value="day" <?=$this->data['tz']['type_of_day'] == 'day' ? 'selected' : ''?>>календарных дней</option>
                                    <option value="month" <?=$this->data['tz']['type_of_day'] == 'month' ? 'selected' : ''?>>месяц(ев)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Испытания проводятся
                    </label>
                    <div class="col-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[tests_for]" id="inlineRadio1" value="own" <?=$this->data['tz']['tests_for'] == 'own' || empty($this->data['tz']['tests_for']) ? 'checked' : ''?>>
                            <label class="form-check-label" for="inlineRadio1">для собственных нужд</label>
                        </div>
                    </div>
                    <div class="col-6 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[tests_for]" id="inlineRadio3" value="certification" <?=$this->data['tz']['tests_for'] == 'certification'  || $this->data['tz']['TAKEN_SERT_ISP'] ? 'checked' : ''?>>
                            <label class="form-check-label" for="inlineRadio3">для предоставления в орган по сертификации (сертификация, декларация продукции)</label>
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Указывать нормативную документацию на объект испытаний
                    </label>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[is_indicate_compliance]" id="inlineRadio21" value="0" <?=$this->data['tz']['is_indicate_compliance'] == 0 ? 'checked' : ''?>>
                            <label class="form-check-label" for="inlineRadio21">нет</label>
                        </div>
                    </div>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[is_indicate_compliance]" id="inlineRadio22" value="1" <?=$this->data['tz']['is_indicate_compliance'] == 1 ? 'checked' : ''?>>
                            <label class="form-check-label" for="inlineRadio22">да (указать)</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <input class="form-control" type="text" id="compliance" maxlength="255" name="tz[compliance]" value="<?=$this->data['tz']['compliance']?? ''?>" placeholder="ГОСТ ТУ" <?=$this->data['tz']['is_indicate_compliance'] != 1 ? 'disabled' : '' ?>>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Результаты испытаний (исследований) оформить
                    </label>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[presented_as]" id="inlineRadio31" value="protocol" <?=$this->data['tz']['presented_as'] == 'protocol' || empty($this->data['tz']['presented_as']) ? 'checked' : ''?>>
                            <label class="form-check-label" for="inlineRadio31">протокол испытаний по ГОСТ Р 58973</label>
                        </div>
                    </div>
                    <div class="col-sm-2 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tz[presented_as]" id="inlineRadio32" value="another" <?=$this->data['tz']['presented_as'] == 'another' ? 'checked' : ''?>>
                            <label class="form-check-label" for="inlineRadio32">протокол испытаний по форме Заказчика</label>
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Необходимые сведения и документы для проведения испытаний (см. п.2.2.4)
                    </label>
                    <div class="col-sm-8 pt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="tz[add_info][]" id="inlineRadio41" value="passport" <?=in_array('passport', $this->data['tz']['add_info']) ? 'checked' : ''?>>
                            <label class="form-check-label" for="inlineRadio41">особые сведения о физических, химических характеристиках продукции (состав, направления формования и т.д.)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="tz[add_info][]" id="inlineRadio42" value="tu" <?=in_array('tu', $this->data['tz']['add_info']) ? 'checked' : ''?>>
                            <label class="form-check-label" for="inlineRadio42">ТУ на производство продукции</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="tz[add_info][]" id="inlineRadio43" value="consist" <?=in_array('consist', $this->data['tz']['add_info']) ? 'checked' : ''?>>
                            <label class="form-check-label" for="inlineRadio43">документ о качестве продукции</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="tz[add_info][]" id="inlineRadio44" value="another" <?=in_array('another', $this->data['tz']['add_info']) ? 'checked' : ''?>>
                            <label class="form-check-label" for="inlineRadio44">иное</label>
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
                <!--./wrapper-info-header-->
            </div>
            <!--./panel-body-->
        </div>



        <div class="panel panel-default">
            <header class="panel-heading">
                Дополнительная информация
                <span class="tools float-end">
                    <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                    <a href="#" class="fa fa-chevron-up"></a>
                 </span>
            </header>
            <div class="panel-body">
                <div class="wrapper-add-info mt-2 flex-column">
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label class="form-label mb-1" for="infoObject">Объект строительства</label>
                            <textarea class="form-control mw-100" name="tz[OBJECT]"><?= $this->data['tz']['OBJECT'] ?></textarea>
                        </div>

                        <div class="form-group col-sm-6">
                            <label class="form-label mb-1" for="commentKp">Комментарий к КП</label>
                            <textarea class="form-control mw-100 comment-kp" id="commentKp" name="tz[COMMENT_KP]" placeholder="Введите текст"><?= $this->data['tz']['COMMENT_KP'] ?></textarea>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="form-group col-sm-6">
                            <label class="form-label mb-1" for="commentTz">Комментарий к ТЗ</label>
                            <textarea class="form-control mw-100 comment-requirement clear_confirm_change" id="commentTz" name="tz[COMMENT_TZ]" placeholder="Введите текст"><?= $this->data['tz']['COMMENT_TZ'] ?></textarea>
                        </div>

                        <div class="form-group col-sm-6 row">
                            <?php if ($this->data['requirement']['creation_stage'] !== 'new'): ?>
                                <div class="col-sm-9">
                                    <label class="form-label mb-1">Заявка учтена</label>
                                    <select class="form-control select2" name="tz[TAKEN_ID_DEAL]">
                                        <option value="">Нет</option>
                                        <?php foreach ($this->data['requests_to_company'] as $request): ?>
                                            <option value="<?= $request['ID_Z'] ?>" <?= $this->data['tz']['TAKEN_ID_DEAL'] == $request['ID_Z'] ? 'selected' : '' ?>>
                                                Заявка <?= $request['REQUEST_TITLE'] ?>, <?= $request['COMPANY_TITLE'] ?>, от <?= $request['DATE_CREATE'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
<!--                            <div class="col-sm-3">-->
<!--                                <label class="form-label mb-1">Серт. испытания</label>-->
<!--                                <div class="d-flex align-items-center taken-request-wrapper">-->
<!--                                    <div>-->
<!--                                        <label class="switch">-->
<!--                                            <input class="form-check-input clear_confirm_change" name="tz[TAKEN_SERT_ISP]" type="checkbox" value="1"-->
<!--                                                <?//= $this->data['tz']['TAKEN_SERT_ISP'] == 1 ? 'checked' : '' ?>>-->
<!--                                            <span class="slider"></span>-->
<!--                                        </label>-->
<!--                                    </div>-->
<!--                                    <input type="hidden" class="hidden-taken">-->
<!--                                </div>-->
<!--                            </div>-->
                        </div>
                    </div>
                </div>
                <!--./wrapper-add-info-->
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

                <div class="accordion accordion-flush mb-3 material-block-group" id="accordionFlushGroup">

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading-group">
                            <div class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapse-group" aria-expanded="false" aria-controls="flush-collapse-group">
                                Групповые действия
                            </div>
                        </h2>
                        <div id="flush-collapse-group" class="accordion-collapse collapse" aria-labelledby="flush-heading" data-bs-parent="#accordionFlushGroup">

                            <div class="accordion-body">
                                <div class="row mb-3 d-none">
                                    <div class="col">
                                        <label class="form-label mb-1">Групповое применение схемы</label>
                                        <div class="input-group">
                                            <select class="form-control select2" name="" id="">
                                                <option value="1">Нет схемы / ручной ввод</option>
                                            </select>

                                            <button type="button" class="btn btn-primary disabled group-button">Применить</button>
                                        </div>
                                    </div>
                                </div>


                                <label class="form-label mb-1">Групповое добавление испытания</label>

                                <div class="row">
                                    <div class="col-4">
                                        <label class="form-label mb-1">Методика испытаний</label>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label mb-1">Нормативная документация</label>
                                    </div>
                                    <div class="col-2">
                                        <label class="form-label mb-1">Исполнитель</label>
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-1">Цена</label>
                                    </div>
                                </div>

                                <div class="row justify-content-between mb-3 method-block">
                                    <div class="col-4">
                                        <div class="input-group">
                                            <select class="form-control select2 method-select" name="">
                                                <option value=""></option>
                                                <?php foreach ($this->data['method_list'] as $method): ?>
                                                    <option
                                                        <?=isset($method['date_color'])? 'data-color="'.$method['date_color'].'"' : ''?>
                                                            value="<?=$method['id']?>"
                                                    ><?=$method['view_gost']?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <a class="btn btn-outline-secondary method-link disabled" target="_blank" title="Перейти в методику" href="">
                                                <i class="fa-solid fa-right-to-bracket"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="input-group">
                                            <select class="form-control select2 tu-select" name="">
                                                <option value="">--</option>
                                                <?php foreach ($this->data['condition_list'] as $condition): ?>
                                                    <option value="<?=$condition['id']?>"><?=$condition['view_name']?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <a class="btn btn-outline-secondary tu-link disabled" target="_blank" title="Перейти в ТУ" href="">
                                                <i class="fa-solid fa-right-to-bracket"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <select class="form-control user-select" name="">
                                            <option value="">Исполнитель</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <div class="input-group">
                                            <input class="form-control price-input" type="number" min="0" step="0.01" value="0">
                                            <span class="input-group-text">₽</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button
                                                class="btn btn-primary mt-0 btn-square float-end disabled group-button add-group-method"
                                                type="button"
                                        >
                                            <i class="fa-solid fa-plus icon-fix"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row justify-content-end mb-3">

                    <div class="col-auto">
                        <button type="button" class="btn btn-primary btn-square collapse-all-material" title="Свернуть все материалы">
                            <i class="fa-solid fa-angles-up"></i>
                        </button>
                    </div>

                    <div class="col-auto">
                        <button type="button" class="btn btn-primary btn-square expand-all-material" title="Развернуть все материалы">
                            <i class="fa-solid fa-angles-down"></i>
                        </button>
                    </div>

                </div>

                <div class="material-container">

                    <div class="accordion mb-3 material-block" id="accordionFlush">

                        <?php $materialNumber = 0; ?>
                        <?php foreach ($this->data['material_probe_list'] as $materialId => $material): ?>
                            <div class="accordion-item material-item" data-number-material="<?=$materialNumber?>" data-material_id="<?=$materialId?>" data-price="<?=$material['price']?>">
                                <h2 class="accordion-header" id="flush-heading<?=$materialNumber?>">
                                    <div class="accordion-button ps-0 collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?=$materialNumber?>" aria-expanded="false" aria-controls="flush-collapse<?=$materialNumber?>">
                                        <input class="form-check-input ms-3 me-3 material-check" type="checkbox" data-bs-toggle="collapse" data-bs-target="#qq" disabled>
                                        <?=$material['material_name']?><span class="ms-3 msg-change-material"></span>
                                        <input type="hidden" name="material[<?=$materialId?>][curr_material]" value="<?=$materialId?>">
                                    </div>
                                </h2>
                                <div id="flush-collapse<?=$materialNumber?>" class="accordion-collapse collapse empty-data" aria-labelledby="flush-heading<?=$materialNumber?>">
                                    <div class="accordion-body">

                                        <div class="row justify-content-end mb-3">
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-success btn-square add-probe" title="Добавить пробу">
                                                    <i class="fa-solid fa-plus icon-fix"></i>
                                                </button>
                                            </div>

                                            <div class="col-auto">
                                                <button type="button" class="btn btn-primary btn-square expand-all" title="Свернуть все пробы">
                                                    <i class="fa-solid fa-angles-up"></i>
                                                </button>
                                            </div>

                                            <div class="col-auto">
                                                <button type="button" class="btn btn-primary btn-square collapse-all" title="Развернуть все пробы">
                                                    <i class="fa-solid fa-angles-down"></i>
                                                </button>
                                            </div>

                                            <div class="col-auto">
                                                <button type="button" class="btn btn-danger btn-square delete-material" title="Удалить объект испытаний и все пробы">
                                                    <i class="fa-solid fa-minus icon-fix"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="line-dashed"></div>

                                        <div class="row mb-3">
                                            <div class="col">
                                                <label class="form-label mb-1">Сменить объект испытаний на</label>
                                                <select class="form-control select2 change-material clear_confirm_change" name="material_id[<?=$materialId?>]">
                                                    <option value="" disabled>Выбрать объект испытаний</option>
                                                    <?php foreach ($this->data['material_list'] as $item): ?>
                                                        <option <?=!empty($item['GROUPS'])? 'data-color="#53a8c9"' : ''?> value="<?=$item['ID']?>" <?=($item['ID'] == $materialId)? 'selected' : ''?>><?=$item['NAME']?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="line-dashed"></div>

                                        <div class="accordion probe-block" id="accordionPanelsStayOpen<?=$materialNumber?>">

                                            <?php $material['probe'] = []; $probeNumber = 0; ?>
                                            <?php foreach ($material['probe'] as $probeId => $probe): ?>
                                                <div class="accordion-item probe-item" data-probe_number="<?=$probeNumber?>" data-probe_id="<?=$probeId?>" data-price="<?=$probe['price']?>">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?=$materialNumber?>-<?=$probeNumber?>">
                                                        <div class="accordion-button ps-0 collapsed bg-pele-green" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?=$materialNumber?>-<?=$probeNumber?>" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?=$materialId?>-<?=$probeNumber?>">
                                                            <input class="form-check-input ms-3 me-3 probe-check" type="checkbox" data-bs-toggle="collapse" data-bs-target="#qq">
                                                            <?=$probe['cipher']?>
                                                        </div>
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?=$materialNumber?>-<?=$probeNumber?>" class="accordion-collapse collapse empty-data" aria-labelledby="panelsStayOpen-heading<?=$materialNumber?>-<?=$probeNumber?>">
                                                        <div class="accordion-body">

                                                            <div class="row justify-content-end mb-3">
                                                                <div class="col-auto">
                                                                    <button type="button" class="btn btn-success btn-square add-method-to-probe" title="Добавить испытание">
                                                                        <i class="fa-solid fa-plus icon-fix"></i>
                                                                    </button>
                                                                </div>

                                                                <div class="col-auto">
                                                                    <button type="button" class="btn btn-success btn-square copy-probe" title="Скопировать пробу и все испытания данной пробы">
                                                                        <i class="fa-regular fa-copy icon-fix"></i>
                                                                    </button>
                                                                </div>

                                                                <div class="col-auto">
                                                                    <button type="button" class="btn btn-danger btn-square del-permanent-probe" title="Удалить пробу и испытания данной пробы">
                                                                        <i class="fa-solid fa-minus icon-fix"></i>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="line-dashed"></div>

                                                            <div class="row mb-3">

                                                                <input type="hidden" name="material[<?=$materialId?>][probe][<?=$probeId?>][probe_number]" value="<?=$probeNumber?>" class="probe-number-input">
                                                                <input type="hidden" name="material[<?=$materialId?>][probe][<?=$probeId?>][material_number]" value="<?=$materialNumber?>" class="material-number-input">

                                                                <div class="col">
                                                                    <label class="form-label mb-1">Группа объекта испытаний</label>
                                                                    <select class="form-control select2 clear_confirm_change" name="material[<?=$materialId?>][probe][<?=$probeId?>][group]">
                                                                        <option value="">Без группы</option>
                                                                        <?php foreach ($material['groups'] as $group): ?>
                                                                            <?php if (empty($group)) { continue; } ?>
                                                                            <option value="<?=$group?>" <?=$probe['group'] == $group? 'selected': ''?>><?=$group?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>

                                                                <div class="col">
                                                                    <label class="form-label mb-1">Маркировка заказчика (информация об объекте испытания)</label>
                                                                    <input type="text" name="material[<?=$materialId?>][probe][<?=$probeId?>][name_for_protocol]" class="form-control" value="<?=$probe['name_for_protocol']?>">
                                                                </div>

                                                                <div class="col d-none">
                                                                    <label class="form-label mb-1">Схема</label>
                                                                    <div class="input-group">
                                                                        <select class="form-control select2" id="">
                                                                            <option value="1">Нет схемы / ручной ввод</option>
                                                                        </select>
                                                                        <button type="button" class="btn btn-primary">Применить</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="line-dashed"></div>

                                                            <div class="row">
                                                                <div class="col-4">
                                                                    <label class="form-label mb-1">Методика испытаний</label>
                                                                </div>
                                                                <div class="col-4">
                                                                    <label class="form-label mb-1">Нормативная документация</label>
                                                                </div>
                                                                <div class="col-2">
                                                                    <label class="form-label mb-1">Исполнитель</label>
                                                                </div>
                                                                <div class="col">
                                                                    <label class="form-label mb-1">Цена</label>
                                                                </div>
                                                            </div>

                                                            <div class="method-container">
                                                                <?php foreach ($probe['method'] as $methodKey => $probeMethod): ?>
                                                                    <div class="row justify-content-between method-block mb-2" data-gost_number="<?=$probeMethod['gost_number']?>">
                                                                        <input type="hidden" class="gost-number-input" name="probe[<?=$probeId?>][method][<?=$probeMethod['ugtp_id']?>][gost_number]" value="<?=$probeMethod['gost_number']?>">
                                                                        <div class="col-4">
                                                                            <div class="input-group">
                                                                                <select class="form-control select2 method-select clear_confirm_change" name="material[<?=$materialId?>][probe][<?=$probeId?>][method][<?=$probeMethod['ugtp_id']?>][new_method_id]">
                                                                                    <option value=""></option>
                                                                                    <?php foreach ($this->data['method_list'] as $method): ?>
                                                                                        <option
                                                                                            <?=isset($method['date_color'])? 'data-color="'.$method['date_color'].'"' : ''?>
                                                                                                value="<?=$method['id']?>"
                                                                                            <?=$method['id'] == $probeMethod['id']? 'selected': ''?>
                                                                                        ><?=$method['view_gost']?></option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                                <a class="btn btn-outline-secondary method-link <?=$probeMethod['id'] > 0? '' : 'disabled'?>" target="_blank" title="Перейти в методику" href="/ulab/gost/method/<?=$probeMethod['id']?>">
                                                                                    <i class="fa-solid fa-right-to-bracket"></i>
                                                                                </a>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-4">
                                                                            <div class="input-group">
                                                                                <select class="form-control select2 tu-select clear_confirm_change" name="material[<?=$materialId?>][probe][<?=$probeId?>][method][<?=$probeMethod['ugtp_id']?>][tech_condition_id]">
                                                                                    <option value="">--</option>
                                                                                    <?php foreach ($this->data['condition_list'] as $condition): ?>
                                                                                        <option value="<?=$condition['id']?>" <?=$condition['id'] == $probe['condition'][$methodKey]? 'selected': ''?>><?=$condition['view_name']?></option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                                <a class="btn btn-outline-secondary tu-link <?=$probe['condition'][$methodKey] > 0? '' : 'disabled'?>" target="_blank" title="Перейти в ТУ" href="/ulab/techCondition/edit/<?=$probe['condition'][$methodKey]?>">
                                                                                    <i class="fa-solid fa-right-to-bracket"></i>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <select class="form-control user-select" name="material[<?=$materialId?>][probe][<?=$probeId?>][method][<?=$probeMethod['ugtp_id']?>][assigned_id]">
                                                                                <option value="">Исполнитель</option>
                                                                                <?php foreach ($probeMethod['assigned'] as $assigned): ?>
                                                                                    <option value="<?=$assigned['user_id']?>" <?=$assigned['user_id'] == $probeMethod['assigned_id']? 'selected': ''?>><?=$assigned['short_name']?></option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col">
                                                                            <div class="input-group">
                                                                                <input class="form-control price-input clear_confirm_change" type="number" min="0" step="0.01" name="material[<?=$materialId?>][probe][<?=$probeId?>][method][<?=$probeMethod['ugtp_id']?>][price]" value="<?=(float)$probeMethod['price']?? 0?>">
                                                                                <span class="input-group-text">₽</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                            <button
                                                                                    class="btn btn-danger mt-0 del-permanent-material-gost btn-square float-end clear_confirm_change"
                                                                                    data-gtp_id="<?=$probeMethod['ugtp_id']?>"
                                                                                    type="button"
                                                                            >
                                                                                <i class="fa-solid fa-minus icon-fix"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $probeNumber++ ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $materialNumber++; ?>
                        <?php endforeach; ?>
                    </div>

                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text">Объект испытаний</span>
                    <select class="form-control select2" id="new-material">
                        <option value="">Выбрать объект испытаний</option>
                        <?php foreach ($this->data['material_list'] as $material): ?>
                            <option value="<?=$material['ID']?>"><?=$material['NAME']?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="input-group-text">Кол-во проб</span>
                    <input type="number" class="form-control" min="1" id="new-count-probe" value="1">
                    <button type="button" class="btn btn-primary add-new-material">Добавить</button>
                </div>

                <div class="line-dashed"></div>

                <div class="wrapper-discount bg-light-secondary p-2">
                    <div class="row justify-content-end">
                        <div class="col-auto d-flex flex-column">
                            <label class="form-label mb-1">Итого</label>
                            <span class="total mt-2"><?= $this->data['tz']['price_ru'] ?></span>
                            <input id="price-total" type="hidden" name="tz[PRICE]" value="<?= $this->data['tz']['PRICE'] ?>">
                            <input id="price_discount" type="hidden" name="tz[price_discount]" value="<?= $this->data['tz']['price_discount'] ?? $this->data['tz']['PRICE'] ?>">
                        </div>

                        <div class="form-group col-auto">
                            <label class="form-label" for="input_discount">Скидка</label>
                            <div class="input-group">
                                <input name="tz[DISCOUNT]" type="number" class="form-control bg-white discount-input clear_confirm_change" min="0" value="<?= $this->data['tz']['DISCOUNT']?? '0' ?>">
                                <select name="tz[discount_type]" class="form-control bg-white discount-type clear_confirm_change">
                                    <option value="percent" <?=$this->data['tz']['discount_type'] == 'percent'? 'selected' : ''?>>%</option>
                                    <option value="rub" <?=$this->data['tz']['discount_type'] == 'rub'? 'selected' : ''?>>₽</option>
                                </select>
                                <button type="button" class="btn btn-primary discount-apply">Применить</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                Подтверждение ТЗ
                <span class="tools float-end">
                    <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                    <a href="javascript:;" class="fa fa-chevron-up"></a>
                </span>
            </div>
            <div class="panel-body">
                <?php if (!empty($this->data['lab_head']['user'])): ?>
                    <?php foreach ($this->data['lab_head']['user'] as $user): ?>
                        <div class="head-user-block <?=$user['user_id'] == $this->data['curr_user']? 'curr_user' : ''?>">
                            <?php if ( $user['is_confirm'] == CHECK_TZ_NOT_SENT && $this->data['check_state'] != CHECK_TZ_NOT_SENT ): ?>
                                <span class="icon" title="ТЗ не отправлено">
                                    <i class="fa-solid fa-minus"></i>
                                </span>
                            <?php elseif ( $user['is_confirm'] == CHECK_TZ_NOT_SENT ): ?>
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

                            <span class="<?=$user['user_id'] == $this->data['curr_user']? 'fw-bold' : ''?>"><?=$user['short_name'];?></span>
                        </div>
                    <?php endforeach; ?>


                    <?php if (!empty($this->data['lab_head']['user']) && $this->data['check_state'] == CHECK_TZ_APPROVE): ?>
                        <div class="mt-1">
                            <label class="form-label text-green fw-bold">Техническое задание утверждено.</label>
                        </div>
                    <?php endif;?>


                    <div class="line-dashed"></div>

                    <?php if ($this->data['lab_head']['is_curr_user']): ?>
                        <?php if ($this->data['check_state'] == CHECK_TZ_NOT_SENT): ?>
                            <button type="button"
                                    class="btn btn-primary sent_approve_tz <?=$this->data['lab_head']['check_state'] == CHECK_TZ_NOT_SENT ? '': 'disable'?>"
                            ><i class="fa-regular fa-paper-plane"></i> Передать и утвердить</button>
                        <?php else: ?>
                            <button type="button"
                                    class="btn btn-success me-3 approve_tz
                                    <?=$this->data['check_state'] == CHECK_TZ_WAIT && $this->data['lab_head']['curr_user_status'] != 1? '': 'disable'?>"
                            ><i class="fa-regular fa-circle-check"></i> Утвердить</button>
                            <a href="#return-modal-form"
                               class="btn btn-danger me-3 not_approve_tz popup-with-form
                                    <?=$this->data['check_state'] == CHECK_TZ_WAIT && $this->data['lab_head']['curr_user_status'] != 1? '': 'disable'?>"
                            ><i class="fa-regular fa-circle-xmark"></i> Вернуть</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="#send-modal-form"
                           class="btn btn-primary popup-with-form <?=$this->data['check_state'] == CHECK_TZ_NOT_SENT? '': 'disable'?>"
                        ><i class="fa-regular fa-paper-plane"></i> Передать</a>
                    <?php endif; ?>

                <?php else: ?>
                    <span class="fw-bold">Сохраните техническое задание</span>
                <?php endif; ?>
            </div>
            <!--./panel-body-->
        </div>

        <?php if ($this->data['is_disable_save']): ?>
            <button class="form-control btn btn-primary mw-100 disabled" type="button">Сохранить</button>
        <?php else: ?>
            <?php if (!empty($this->data['lab_head']['user']) && $this->data['check_state'] != CHECK_TZ_NOT_SENT): ?>
                <label class="form-label text-red">Техническое задание на проверке. При нажатии "Сохранить" отзовет проверку</label>
                <button class="form-control btn btn-primary mw-100 save" id="save" name="save" onclick="return confirm('Техническое задание на проверке. При нажатии Сохранить отзовет проверку! Продолжить?')" type="submit">Сохранить</button>
            <?php else: ?>
                <button class="form-control btn btn-primary mw-100 save" id="save" name="save" type="submit">Сохранить</button>
            <?php endif;?>
        <?php endif;?>

	</form>
    <!--./form-requirement-->
</div>
<!--./wrapper-requirement-->

<div id="return-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Причина возврата
    </div>

    <div class="line-dashed-small"></div>

    <textarea class="form-control" id="desc_return" rows="5" required></textarea>

    <div class="line-dashed-small"></div>

    <button type="button" class="btn btn-primary not_approve_tz_btn">Отправить</button>
</div>

<form id="send-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/requirement/confirmTzSent/" method="post">
    <div class="title mb-3 h-2">
        Отправка руководителям на проверку
    </div>

    <div class="line-dashed-small"></div>

    <?php if (!empty($this->data['lab_head']['user'])): ?>
        <?php foreach ($this->data['lab_head']['user'] as $user): ?>
            <div class="head-user-block form-check <?=$user['user_id'] == $this->data['curr_user']? 'curr_user' : ''?>">
                <input id="send_user_<?=$user['user_id']?>" type="checkbox" name="users[]" class="form-check-input" value="<?=$user['user_id']?>" checked>

                <label class="form-check-label" for="send_user_<?=$user['user_id']?>">
                    <span class="<?=$user['user_id'] == $this->data['curr_user']? 'fw-bold' : ''?>"><?=$user['short_name'];?></span>
                </label>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($this->data['lab_head']['user']) && $this->data['check_state'] == CHECK_TZ_APPROVE): ?>
            <div class="mt-1">
                <label class="form-label text-green fw-bold">Техническое задание утверждено.</label>
            </div>
        <?php endif;?>

    <?php else: ?>
        <span class="fw-bold">Сохраните техническое задание</span>
    <?php endif; ?>

    <input name="deal_id" value="<?=$this->data['deal_id']?>" type="hidden">
    <input name="tz_id" value="<?=$this->data['tz_id']?>" type="hidden">

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary"><i class="fa-regular fa-paper-plane"></i> Передать</button>
</form>
