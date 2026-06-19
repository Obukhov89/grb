<div class="wrapper-requirement m-auto">
    <div style="display: none">
    <pre>
    <?print_r($this->data['requirement']);?>
        </pre>
    </div>
    <header class="header-requirement mb-4">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-2">
                    <a class="nav-link link-back" href="<?= URI ?>/request/card/<?= $this->data['requirement']['deal_id'] ?? '' ?>" title="Вернуться назад">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?=URI?>/assets/images/icons.svg#back"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-list">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?=URI?>/assets/images/icons.svg#list"/>
                        </svg>
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
            </ul>
        </nav>
    </header>

    <h2 class="d-flex mb-3">
        Заявка <?= $this->data['deal_title'] ?? '' ?>
    </h2>

    <form class="form form-requirement" id="form_requirement" method="post" action="<?=URI?>/requirement/insertUpdate/">
        <?php if (!empty($this->data['requirement']['tz_id'])): ?>
            <input class="tz-id" type="hidden" name="tz_id" value="<?= $this->data['requirement']['tz_id'] ?>">
        <?php endif; ?>

        <?php if (!empty($this->data['requirement']['deal_id'])): ?>
            <input class="deal-id" type="hidden" name="deal_id" value="<?= $this->data['requirement']['deal_id'] ?>">
        <?php endif; ?>

        <?php if (!empty($_SESSION['SESS_AUTH']['USER_ID'])): ?>
            <input class="user-id" type="hidden" name="user_id" value="<?= $_SESSION['SESS_AUTH']['USER_ID'] ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
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
                                    <div>Основание для проведения испытаний</div>
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
                                    <div>Основание для формирования протокола</div>
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
                                    <div class="col">
                                        <lable for="infoDescription">Описание объекта <span class='redStars'>*<span></lable>
                                        <textarea class="form-control mw-100 info-description" id="infoDescription" name="DESCRIPTION" required><?= $this->data['requirement']['DESCRIPTION'] ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--./wrapper-info-header-->
                    </div>
                    <!--./panel-body-->
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Дополнительная информация
                        <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="javascript:;" class="fa fa-chevron-down"></a>
                        </span>
                    </div>
                    <div class="panel-body panel-hidden">
                        <div class="wrapper-add-info mt-2 flex-column">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <lable for="infoObject">Объект строительства</lable>
                                    <input type="text" class="form-control object" name="OBJECT" list="objects" value="<?= $this->data['requirement']['OBJECT'] ?>">
                                    <datalist id="objects">
                                        <?php foreach ($this->data['objects'] as $object): ?>
                                            <option><?= $object['NAME'] ?></option>
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                                <div class="form-group col-sm-6">
                                    <lable for="day_to_test">Срок проведения испытаний</lable>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control number-only day-to-test" id="day_to_test" name="DAY_TO_TEST" value="<?= $this->data['requirement']['DAY_TO_TEST'] ?>" aria-describedby="basic-addon2">
                                        <select class="input-group-text col-3" id="basic-addon2" name="type_of_day">
                                            <option value="day" <?=$this->data['requirement']['type_of_day'] == 'day' ? 'selected' : ''?>>дней</option>
                                            <option value="work_day" <?=$this->data['requirement']['type_of_day'] == 'work_day' ? 'selected' : ''?>>рабочих дней</option>
                                            <option value="month" <?=$this->data['requirement']['type_of_day'] == 'month' ? 'selected' : ''?>>месяц(ев)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="form-group col-sm-6">
                                    <lable for="commentKp">Комментарий к КП</lable>
                                    <textarea class="form-control mw-100 comment-kp" id="commentKp" name="COMMENT_KP" placeholder="Введите текст"><?= $this->data['requirement']['COMMENT_KP'] ?></textarea>
                                </div>
                                <div class="form-group col-sm-6">
                                    <lable for="commentTz">Комментарий к ТЗ</lable>
                                    <textarea class="form-control mw-100 comment-requirement" id="commentTz" name="COMMENT_TZ" placeholder="Введите текст"><?= $this->data['requirement']['COMMENT_TZ'] ?></textarea>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="form-group col-sm-6">
                                    <lable for="protocolDeadline">Срок выдачи протокола</lable>
                                    <input type="date" class="form-control protocol-deadline bg-white w-100" id="protocolDeadline" name="DEADLINE" value="<?= $this->data['requirement']['DEADLINE'] ?>">
                                </div>
                                <div class="form-group col-sm-6 row">
                                    <?php if ($this->data['requirement']['creation_stage'] !== 'new' && $this->data['tz'] !== '1'): ?>
                                        <div class="col-sm-3">
                                            <div>Заявка учтена</div>
                                            <div class="d-flex align-items-center taken-request-wrapper">
                                                <div>
                                                    <label class="switch">
                                                        <input class="form-check-input taken" name="taken" type="checkbox" value=""
                                                            <?= !empty($this->data['taken_request']) ? 'checked' : '' ?>>
                                                        <span class="slider taken-popup popup-with-form"></span>
                                                    </label>
                                                </div>
                                                <span class="taken-request ms-2">
                                                    <?php if (!empty($this->data['taken_request'])): ?>
                                                        Заявка <?= $this->data['taken_request']['REQUEST_TITLE'] ?>, <?= $this->data['taken_request']['COMPANY_TITLE'] ?>, от <?= $this->data['taken_request']['DATE_CREATE'] ?>
                                                    <?php endif; ?>
                                                </span>
                                                <input type="hidden" class="hidden-taken">
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-sm-3">
                                        <div>Серт. испытания</div>
                                        <div class="d-flex align-items-center taken-request-wrapper">
                                            <div>
                                                <label class="switch">
                                                    <input class="form-check-input" name="taken_certificate" type="checkbox"
                                                        <?= $this->data['requirement']['taken_certificate'] ? 'checked' : '' ?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <input type="hidden" class="hidden-taken">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <div>Место отбора</div>
                                    <div class="place">
                                        <strong><?= $this->data['probe_place'] ?></strong>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div>Дата отбора проб</div>
                                    <div class="sampling-date">
                                        <strong><?= $this->data['probe_date'] ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <div>Кем отобраны пробы</div>
                                    <div>
                                        <strong><?= $this->data['probe_made'] ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--./wrapper-add-info-->
                    </div>
                    <!--./panel-body-->
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Материалы
                        <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="javascript:;" class="fa fa-chevron-up"></a>
                        </span>
                    </div>
                    <div class="panel-body">
                        <div class="wrapper-materials mb-4">
                            <datalist id="methods_list">
                                <?php foreach ($this->data['select_gosts'] as $gost): ?>
                                    <option data-price="<?= $gost['PRICE'] ?>"
                                            data-value="<?= $gost['ID'] ?>"
                                            data-type="<?= $gost['NORM_TEXT'] ?>"><?= $gost['view_gost'] ?></option>
                                <?php endforeach; ?>
                            </datalist>

                            <datalist id="conditions_list">
                                <option data-value="2522">-- | --</option>
                                <?php foreach ($this->data['select_tu_gosts'] as $gost): ?>
                                    <option data-type="<?= $gost['NORM_TEXT'] ?>"
                                            data-value="<?= $gost['ID'] ?>"><?= $gost['view_gost'] ?></option>
                                <?php endforeach; ?>
                            </datalist>

                            <datalist id="materials">
                                <?php if (isset($this->data['select_materials'])): ?>
                                    <?php foreach ($this->data['select_materials'] as $val): ?>
                                        <option data-value="<?=$val['ID']?>"><?=$val['NAME']?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </datalist>

                            <?php if (!empty($this->data['requirement']['material'])): ?>
                                <?php foreach ($this->data['requirement']['material'] as $key => $material): ?>
                                    <div class="wrapper-material mb-5">
                                        <div class="row g-3 align-items-center mb-2 mt-0 material-data">
                                            <div class="col-auto">
                                                <span class="material-number" data-material-number="<?= $key ?>"><?= $key + 1 ?></span>
                                            </div>
                                            <div class="col-auto col-material-name">
                                                <button type="button" class="material-name p-2 border-0" id="material<?= $key ?>-view"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseMaterial<?= $key ?>" aria-expanded="false"
                                                        aria-controls="collapseMaterial<?= $key ?>"><?= $material['name'] ?></button>
                                            </div>
                                            <div class="col-auto">|</div>
                                            <div class="col-auto">
                                                <label for="inputMaterialCount" class="col-form-label">образцов</label>
                                            </div>
                                            <div class="col-auto me-auto">
                                                <span class="form-control border-0" id="amount<?= $key ?>-view"><?= $this->data['requirement']['amount'][$key] ?? 1 ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" name="add_material" class="btn btn-primary btn-add-material w-100 mw-100">Добавить материал</button>
                                            </div>
                                            <div class="col-auto">
                                                <button
                                                        type="button"
                                                        name="del_material"
                                                        data-deal_id="<?=$this->data['requirement']['deal_id']?>"
                                                        data-material_id="<?=$material['id']?>"
                                                        data-mtr_id="<?=$material['mtr_id']?>"
                                                        data-number="<?=$key + 1?>"
                                                        class="btn btn-danger del-permanent-material <?=$this->data['is_block_delete_material']? 'disabled': ''?>"
                                                >
                                                    Удалить материал
                                                </button>
                                            </div>
                                        </div>

                                        <div class="wrapper-add-material mb-3 mt-3 flex-column collapse" id="collapseMaterial<?= $key ?>">
                                            <div class="row">
                                                <div class="form-group col-sm-6">
                                                    <lable for="material">Материал <span class='redStars'>*<span></lable>
                                                    <input type="hidden" id="material<?= $key ?>-hidden" name="material[<?= $key ?>][id]" value="<?= $material['id'] ?>">
                                                    <input type="hidden" id="mtr<?= $key ?>-hidden" name="material[<?= $key ?>][mtr_id]" value="<?= $material['mtr_id'] ?>">
                                                    <input class="form-control material" id="material<?= $key ?>" type="text" name="material[<?= $key ?>][name]"
                                                           list="materials" value="<?= $material['name'] ?>" autocomplete="off" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-6 amount">
                                                    <lable for="amount">Количество проб/образцов <span class='redStars'>*<span></lable>
                                                    <input class="form-control number-only material-count mw-100" id="amount<?= $key ?>" type='text'
                                                           name='amount[<?= $key ?>]' value="<?= $this->data['requirement']['amount'][$key] ?? 1 ?>" required>
                                                </div>
                                            </div>

                                            <div class="row wrapper-gost align-items-end">
                                                <div class="form-group col-sm-6">
                                                    <lable for="gostToMaterial">Гост</lable>
                                                    <div class="row align-items-center wrapper-items-gost">
                                                        <div class="col">
                                                            <select class="form-select gosts-group" id="gostToMaterial">
                                                                <option value="" selected disabled></option>
                                                                <?php foreach ($this->data['gosts_group'][$key] as $gost): ?>
                                                                    <option value="<?= $gost['GOST'] ?>"><?= $gost['GOST'] ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-auto">
                                                    <button type="button" class="btn btn-primary add-gost mb-0">
                                                        <svg class="icon" width="15" height="15">
                                                            <use xlink:href="/ulab/assets/images/icons.svg#add"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!--./wrapper-add-material-->

                                        <table class="table table-gosts">
                                            <thead>
                                            <tr class="table-secondary align-middle">
                                                <td scope="col" class="col-methods">Методики испытаний</td>
                                                <td scope="col" class="col-methods-link"></td>
                                                <td scope="col" class="col-conditions">Тех. условия</td>
                                                <td scope="col" class="col-price">Исполнитель</td>
                                                <td scope="col" class="col-price">Цена</td>
                                                <td scope="col" class="col-no-price">Без цены</td>
                                                <td scope="col" class="col-not-match"></td>
                                                <td scope="col" class="col-btn-methods">
                                                    <button type="button" class="btn btn-danger del-gosts btn-square" name="del_gosts">
                                                        <i class="fa-solid fa-minus icon-fix"></i>
                                                    </button>
                                                </td>
                                                <td scope="col" class="col-btn-methods"></td>
                                            </tr>
                                            </thead>
                                            <tbody class="tbody-gost">
                                            <tr class="align-middle tr-gost">
                                                <td class="tdGost">
                                                    <input
                                                            class="form-control methods <?=$this->data['requirement']['methods'][$key][0]['is_old']? 'is-invalid' : ''?>"
                                                            id="material<?=$key?>-methods0"
                                                            list="methods_list"
                                                            type="text"
                                                            name="methods[<?=$key?>][0][name]"
                                                            data-price="<?=$this->data['price'][$key][0]?>"
                                                            value="<?=$this->data['requirement']['methods'][$key][0]['name'] ?? ''?>"
                                                            autocomplete="off"
                                                            required
                                                    >
                                                    <?php if ($this->data['requirement']['methods'][$key][0]['is_old']): ?>
                                                        <div class="invalid-feedback">
                                                            Внимание! Старая область!
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="hidden" name="methods[<?= $key ?>][0][id]" id="material<?= $key ?>-methods0-hidden" class="methods-id" value="<?= $this->data['requirement']['methods'][$key][0]['id'] ?? '' ?>">
                                                </td>
                                                <td class="text-center td-method-link">
                                                    <a class="link-tab method-link" target='_blank' href="/obl_acc.php?ID=<?= $this->data['requirement']['methods'][$key][0]['id'] ?? '' ?>">
                                                        <svg class="icon" width="35" height="35">
                                                            <use xlink:href="/ulab/assets/images/icons.svg#tab"/>
                                                        </svg>
                                                    </a>
                                                </td>
                                                <td class="tdGost">
                                                    <input class="form-control conditions" id="material<?= $key ?>-conditions0" list="conditions_list" type="text" name="conditions[<?= $key ?>][0][name]" value="<?= $this->data['requirement']['conditions'][$key][0]['name'] ?? '-- | --' ?>" autocomplete="off" required>
                                                    <input type="hidden" name="conditions[<?= $key ?>][0][id]" id="material<?= $key ?>-conditions0-hidden" class="conditions_id" value="<?= $this->data['requirement']['conditions'][$key][0]['id'] ?? '2522' ?>">
                                                </td>
                                                <td class="tdGost">
                                                    <select class="form-select assign_method" id="assign_method<?= $key ?>-unit0" name="assign_method[<?= $key ?>][0]">
                                                        <option value="">Выбрать сотрудника</option>
                                                        <?php foreach ($this->data['assign_method'][$this->data['requirement']['methods'][$key][0]['id']] as $unit):?>
                                                            <option value="<?=$unit['id']?>" <?=$this->data['requirement']['assign_method'][$key][0] == $unit['id'] ? 'selected' : ''?>><?=$unit['name']?></option>
                                                        <?php endforeach;?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control price <?= $this->data['requirement']['price'][$key][0] ? '' : 'd-none' ?>"
                                                           name="price[<?= $key ?>][0]" value="<?= $this->data['requirement']['price'][$key][0] ?? '' ?>" step="0.01" required>
                                                    <div class="form-control text-center dash <?= $this->data['requirement']['price'][$key][0] ? 'd-none' : '' ?>">
                                                        <svg class="icon" width="15" height="15">
                                                            <use xlink:href="/ulab/assets/images/icons.svg#del"/>
                                                        </svg>
                                                    </div>
                                                </td>
                                                <td>
                                                    <label class="switch">
                                                        <input type="checkbox" class="form-check-input mt-0 no-price" name="no_price" <?= $this->data['requirement']['price'][$key][0] ? '' : 'checked' ?>>
                                                        <span class="slider"></span>
                                                    </label>
                                                </td>
                                                <td class="td-not-match"></td>
                                                <td>
                                                    <button class="btn btn-primary mt-0 add-methods btn-square" type="button">
                                                        <i class="fa-solid fa-plus icon-fix"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn shadow-none">
                                                        <svg class="icon" width="15" height="15">
                                                            <use xlink:href="#"></use>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php if (!empty($this->data['requirement']['methods'][$key]) && count($this->data['requirement']['methods'][$key]) > 1): ?>
                                                <?php for ($i = 1; $i < count($this->data['requirement']['methods'][$key]); $i++): ?>
                                                    <tr class="align-middle tr-gost">
                                                        <td class="tdGost">
                                                            <input
                                                                    class="form-control methods <?=$this->data['requirement']['methods'][$key][$i]['is_old']? 'is-invalid' : ''?>"
                                                                    id="material<?= $key ?>-methods<?=$i?>"
                                                                    list="methods_list"
                                                                    type="text"
                                                                    name="methods[<?=$key?>][<?=$i?>][name]"
                                                                    data-price="<?=$this->data['price'][$key][$i]?>"
                                                                    value="<?=$this->data['requirement']['methods'][$key][$i]['name'] ?? ''?>"
                                                                    autocomplete="off"
                                                                    required
                                                            >
                                                            <?php if ($this->data['requirement']['methods'][$key][0]['is_old']): ?>
                                                                <div class="invalid-feedback">
                                                                    Внимание! Старая область!
                                                                </div>
                                                            <?php endif; ?>
                                                            <input type="hidden" name="methods[<?= $key ?>][<?= $i ?>][id]" id="material<?= $key ?>-methods<?= $i ?>-hidden" class="methods-id" value="<?= $this->data['requirement']['methods'][$key][$i]['id'] ?? '' ?>">
                                                        </td>
                                                        <td class="text-center td-method-link">
                                                            <a class="link-tab method-link" target="_blank" href="/obl_acc.php?ID=<?= $this->data['requirement']['methods'][$key][$i]['id'] ?? '' ?>">
                                                                <svg class="icon" width="35" height="35">
                                                                    <use xlink:href="/ulab/assets/images/icons.svg#tab"/>
                                                                </svg>
                                                            </a>
                                                        </td>
                                                        <td class="tdGost">
                                                            <input class="form-control conditions" id="material<?= $key ?>-conditions<?= $i ?>" list="conditions_list" type="text" name="conditions[<?= $key ?>][<?= $i ?>][name]" value="<?= $this->data['requirement']['conditions'][$key][$i]['name'] ?? '-- | --' ?>" autocomplete="off" required>
                                                            <input type="hidden" name="conditions[<?= $key ?>][<?= $i ?>][id]" id="material<?= $key ?>-conditions<?= $i ?>-hidden" class="conditions_id" value="<?= $this->data['requirement']['conditions'][$key][$i]['id'] ?? '2522' ?>">
                                                        </td>
                                                        <td class="tdGost">
                                                            <select class="form-select assign_method" id="assign_method<?= $key ?>-unit<?= $i ?>" name="assign_method[<?= $key ?>][<?= $i ?>]">
                                                                <option value="">Выбрать сотрудника</option>
                                                                <?php foreach ($this->data['assign_method'][$this->data['requirement']['methods'][$key][$i]['id']] as $unit):?>
                                                                    <option value="<?=$unit['id']?>" <?=$this->data['requirement']['assign_method'][$key][$i] == $unit['id'] ? 'selected' : ''?>><?=$unit['name']?></option>
                                                                <?php endforeach;?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control price <?= $this->data['requirement']['price'][$key][$i] ? '' : 'd-none' ?>"
                                                                   name="price[<?= $key ?>][<?= $i ?>]" value="<?= $this->data['requirement']['price'][$key][$i] ?? '' ?>" step="0.01" required>
                                                            <div class="form-control text-center dash <?= $this->data['requirement']['price'][$key][$i] ? 'd-none' : '' ?>">
                                                                <svg class="icon" width="15" height="15">
                                                                    <use xlink:href="/ulab/assets/images/icons.svg#del"/>
                                                                </svg>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <label class="switch">
                                                                <input type="checkbox" class="form-check-input mt-0 no-price" name="no_price" <?= $this->data['requirement']['price'][$key][$i] ? '' : 'checked' ?>>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </td>
                                                        <td class="td-not-match"></td>
                                                        <td>
                                                            <button class="btn btn-primary mt-0 add-methods btn-square" type="button">
                                                                <i class="fa-solid fa-plus icon-fix"></i>
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <button
                                                                    class="btn btn-danger mt-0 del-permanent-material-gost btn-square"
                                                                    data-gtp_id="<?=$this->data['requirement']['methods'][$key][$i]['gtp_id']?>"
                                                                    data-number_gost="<?=$i+1?>"
                                                                    data-deal_id="<?=$this->data['requirement']['deal_id']?>"
                                                                    data-material_id="<?=$material['id']?>"
                                                                    data-mtr_id="<?=$material['mtr_id']?>"
                                                                    data-number="<?=$key + 1?>"
                                                                    type="button"
                                                            >
                                                                <i class="fa-solid fa-minus icon-fix"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <div class="row wrapper-buttons">
                                <div class="col-sm-3 col-add-material <?= empty($this->data['requirement']['material']) ? 'd-block' : 'd-none' ?>">
                                    <button type="button" class="btn btn-primary btn-add-material w-100 mw-100 ms-0">Добавить материал</button>
                                </div>
                            </div>

                            <div class="wrapper-discount bg-light-secondary">
                                <div class="row g-3 justify-content-end py-2 px-4 mt-3">
                                    <div class="col-auto">
                                        <?php if (false): ?>
                                            <span class="errorDiscount d-block">Для добавления скидки составьте техническое задание!</span><!--TODO: Временный класс d-block-->
                                        <? endif; ?>
                                    </div>
                                    <div class="d-flex flex-column col-auto">
                                        <span>Итого</span>
                                        <span class="mt-1 total"><?= $this->data['sum_price'] ?> ₽</span>
                                    </div>
                                    <div class="form-group col-auto">
                                        <lable for="input_discount">Скидка</lable>
                                        <div class="d-flex">
                                            <input type="number" class="m-0 me-2 discount h-auto border-0" id="input_discount"
                                                   name="DISCOUNT" step="0.01" value="<?= $this->data['requirement']['DISCOUNT'] ?>">
                                            <input class="hidden-is-discount" type="hidden" name="hidden_is_discount" value="">
                                            <!--<input type="hidden" name="hiddenNotDiscount" value="">-->

                                            <button type="button" name="btn_discount" class="btn btn-primary btn-discount">Применить скидку</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--./wrapper-materials-->
                    </div>
                    <!--./panel-body-->
                </div>
            </div>
        </div>

        <?php if (!empty($this->data['requirement']['methods'])): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Подтверждение ТЗ
                            <span class="tools float-end">
                                <a href="javascript:;" class="fa fa-chevron-up"></a>
                            </span>
                        </div>
                        <div class="panel-body">
                            <div class="confirm">
                                <div class="row mb-2">
                                    <div class="col-auto">
                                        <table class="table table-confirm mb-0">
                                            <tbody>
                                            <?php foreach ($this->data['assigned'] as $key => $value): ?>
                                                <?php
                                                if ((array_search($value['user_id'], $this->data['laboratory_head']) === false)) {
                                                    continue;
                                                }
                                                ?>
                                                <tr>
                                                    <th scope="row" class="text-success">
                                                        <?= !empty($this->data['check_tz'][$value['user_id']]['confirm']) ? '&#128504;' : ''?>
                                                    </th>
                                                    <td><?= $value["short_name"] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto footer-confirm">
                                        <?php if ($this->data['is_confirmTz']): ?>
                                            <strong>Техническое задание утверждено!</strong>
                                        <?php else: ?>
                                            <?php if (empty($this->data['lab_leaders_tz'])): ?>
                                                <button type="button" class="btn btn-primary btn-transfer" <?= empty($this->data['requirement']['methods']) && !empty($this->data['check_tz']) || !in_array($_SESSION['SESS_AUTH']['USER_ID'], $this->data['assigned_id']) ? 'disabled' : '' ?>>Передать</button>
                                            <?php else: ?>
                                                <?php if (!in_array($_SESSION['SESS_AUTH']['USER_ID'], $this->data['lab_leaders_tz'])): ?>
                                                    <strong>Заявка передана на рассмотрение!</strong>
                                                <?php else: ?>
                                                    <?php if (!empty($this->data['check_tz'][$_SESSION['SESS_AUTH']['USER_ID']]['confirm'])): ?>
                                                        <strong>Утверждено Вами!</strong>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-primary btn-approve me-2">Утвердить</button>
                                                        <button type="button" class="btn btn-danger btn-no-transfer"
                                                            <?= !empty($this->data['check_tz'][$_SESSION['SESS_AUTH']['USER_ID']]['date_return']) ||
                                                            !empty($this->data['check_tz'][$_SESSION['SESS_AUTH']['USER_ID']]['confirm']) ? 'disabled' : '' ?>>
                                                            Вернуть
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--./panel-body-->
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div id="modal-form-add-lab" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
            <div class="title mb-3 h-2">
                Выберите лабораторию для методики
            </div>

            <div class="line-dashed-small"></div>

            <div class="mb-3 content-add-lab"></div>

            <div class="line-dashed-small"></div>

            <button type="button" class="btn btn-primary btn-add-lab" id="btn_add_lab">Добавить</button>
        </div>


        <?php /*if ( $this->data['save'] ): */?>
        <?php if ( $this->data['save'] && $this->data['is_may_change'] ): ?>
            <button class="form-control btn btn-primary mw-100 save" id="save" name="save" type="submit">Сохранить</button>
        <?php else: ?>
            <?php if (!$this->data['is_may_change']): ?>
                <span class="text-danger">Редактирование запрещено, сохранены данные результатов испытания</span>
            <?php endif; ?>
            <div class="form-control text-center text-uppercase bg-light-secondary mw-100">Сохранить</div>
        <?php endif; ?>

        <div class="line-dashed"></div>
        <?php if (in_array($_SESSION['SESS_AUTH']['USER_ID'], [7, 61])):?>
            <a href="/tz.php?<?= !empty($this->data['requirement']['methods']) ? 'EDIT' : 'ID' ?>=<?= !empty($this->data['requirement']['methods']) ? $this->data['requirement']['tz_id'] : $this->data['requirement']['deal_id'] ?>">Вернуться на старый дизайн</a>
        <?php endif;?>
    </form>
    <!--./form-requirement-->

    <form id="taken-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/request/" method="post">
        <div class="title mb-3 h-2">
            Данные заявок
        </div>

        <div class="line-dashed-small"></div>

        <div class="mb-3">
            <label class="form-label">Выберите заявку для учета</label>
            <input type="text" class="form-control taken-requests" name="taken_requests" list="taken_requests">
            <datalist id="taken_requests">
                <?php foreach ($this->data['requests_to_company'] as $request): ?>
                    <option data-id="<?= $request['ID_Z'] ?>">Заявка <?= $request['REQUEST_TITLE'] ?>, <?= $request['COMPANY_TITLE'] ?>, от <?= $request['DATE_CREATE'] ?></option>
                <?php endforeach; ?>
            </datalist>
        </div>

        <input name="tz_id" type="hidden" value="<?= $this->data['requirement']['tz_id'] ?>">

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
    <!--./taken-modal-form-->

    <form id="modal_form_not_match" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/request/" method="post">
        <div class="title mb-3 h-2">
            Внимание
        </div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 content-not-match">
        </div>

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary btn-add-assigned">Отправить</button>
    </form>
    <!--./modal-form-not-match-->

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->
</div>
<!--./wrapper-requirement-->
