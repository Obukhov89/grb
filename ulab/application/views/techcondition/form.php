<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/techCondition/list/" title="Вернуться к списку">
                    <i class="fa-solid fa-list"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/techCondition/new/" title="Новое ТУ">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link disable-after-click" href="<?=URI?>/techCondition/copy/<?=$this->data['form']['id']?>" title="Скопировать ТУ">
                    <i class="fa-regular fa-copy icon-fix"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<input type="hidden" value="<?= $this->data['currentUser']['id'] ?>">

<?php
//if(!in_array($this->data['currentUser']['id'], [61, 10, 17, 222, 227, 250])):
//?>
<!--    <style>-->
<!--        .form-control, .select2-selection, .form-check-input, input, button, .form-check-label {-->
<!--            user-select: none;-->
<!--            pointer-events: none;-->
<!--            background-color: lightgray !important;-->
<!--        }-->
<!--    </style>-->
<?php
//endif;
//?>

<form class="form-horizontal" method="post" action="<?=URI?>/techCondition/insertUpdate/">
    <div class="panel panel-default">
        <header class="panel-heading">
            Основные характеристики
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <input id="method_id" type="hidden" value="<?=$this->data['form']['id']?>" name="id">

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Номер документа <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <input type="text" name="form[reg_doc]" class="form-control" value="<?=$this->data['form']['reg_doc'] ?? ''?>" maxlength="255" required>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Пункт</label>
                <div class="col-sm-8">
                    <input type="text" name="form[clause]" class="form-control" value="<?=$this->data['form']['clause'] ?? ''?>" maxlength="255">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Год</label>
                <div class="col-sm-8">
                    <input type="number" name="form[year]" class="form-control appearance-none" value="<?=$this->data['form']['year'] ?? ''?>" maxlength="4" max="3000">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Наименование ТУ <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <textarea name="form[name]" class="form-control" style="height: 80px;" required><?=$this->data['form']['name'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Определяемая характеристика / показатель</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="form[measured_properties_name]" value="<?=$this->data['form']['measured_properties_name'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

        </div>
    </div>


    <div class="panel panel-default">
        <header class="panel-heading">
            Единицы измерения и нормы
            <span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
        </span>
        </header>
        <div class="panel-body">

            <div class="result-block">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Единица измерения</label>
                    <div class="col-sm-8">
                        <select class="form-control select2" name="form[unit_id]" required>
                            <option value="">Выбрать</option>
                            <?php foreach ($this->data['unit_list'] as $unit): ?>
                                <option value="<?=$unit['id']?>" <?=$this->data['form']['unit_id'] == $unit['id'] ? 'selected' : ''?>><?=htmlentities($unit['unit_rus'])?> | <?=htmlentities($unit['name'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <div class="col-sm-2">
                            <a class="btn btn-square btn-outline-secondary" href="/ulab/reference/unitList/" title="Журнал единиц измерения">
                                <i class="fa-solid fa-list icon-fix-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Знаков после запятой</label>
                    <div class="col-sm-8">
                        <input type="number" class="form-control" name="form[decimal_places]" value="<?=$this->data['form']['decimal_places'] ?? '0'?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Диапазон определения</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-text text-def-1"><?=$this->data['form']['definition_range_type'] == 2? 'до' : 'от'?></span>
                            <input type="number" class="form-control" step="0.001" name="form[definition_range_1]" value="<?=round($this->data['form']['definition_range_1'], $this->data['form']['decimal_places'])?>">
                            <span class="input-group-text text-def-2"><?=$this->data['form']['definition_range_type'] == 2? 'от' : 'до'?></span>
                            <input type="number" class="form-control" step="0.001" name="form[definition_range_2]" value="<?=round($this->data['form']['definition_range_2'], $this->data['form']['decimal_places'])?>">
                            <div class="input-group-text">
                                <input
                                        id="r1"
                                        class="form-check-input mt-0 me-1"
                                        type="radio" value="1"
                                        name="form[definition_range_type]"
                                    <?=($this->data['form']['definition_range_type'] == 1 || empty($this->data['form']['definition_range_type']))? 'checked' : ''?>
                                >
                                <label class="form-check-label" for="r1">
                                    Внутренний диапазон
                                </label>
                            </div>
                            <div class="input-group-text">
                                <input id="r2" class="form-check-input mt-0 me-1" type="radio" value="2" name="form[definition_range_type]" <?=$this->data['form']['definition_range_type'] == 2? 'checked' : ''?>>
                                <label class="form-check-label" for="r2">
                                    Внешний диапазон
                                </label>
                            </div>
                            <div class="input-group-text">
                                <input id="r3" class="form-check-input mt-0 me-1" type="radio" value="3" name="form[definition_range_type]" <?=$this->data['form']['definition_range_type'] == 3? 'checked' : ''?>>
                                <label class="form-check-label" for="r3">
                                    Не нормируется
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Текст нормативного значения по ГОСТу</label>
                    <div class="col-sm-8">
                        <div class="input-group norm-comment-group">
                            <div class="input-group-text">
                                <input id="ch1" class="form-check-input mt-0 me-1 is_output_check" type="checkbox" value="1" name="form[is_output]"
                                    <?=$this->data['form']['is_output'] == 1 ? 'checked' : ''?>>
                                <label class="form-check-label" for="ch1">
                                    Выводить в протокол
                                </label>
                            </div>
                            <input type="text" class="form-control <?=$this->data['form']['is_output'] == 1? '' : 'd-none'?> norm_comment_elem" name="form[norm_comment]"
                                   value="<?=$this->data['form']['norm_comment'] ?? '-'?>">
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <!--<div class="form-group row">
                    <label class="col-sm-2 col-form-label">Нормы текстом?</label>
                    <div class="col-sm-8">
                        <input
                                class="form-check-input"
                                type="checkbox"
                                value="1"
                                name="form[is_text_norm]"
                            <?/*=$this->data['form']['is_text_norm'] == 1? 'checked' : ''*/?>
                        >
                    </div>
                    <div class="col-sm-2"></div>
                </div>-->

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Ручное управление "соотв/не соотв"?</label>
                    <div class="col-sm-8">
                        <input
                                class="form-check-input"
                                type="checkbox"
                                value="1"
                                name="form[is_manual]"
                            <?=$this->data['form']['is_manual'] == 1? 'checked' : ''?>
                        >
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            </div>

            <div class="line-dashed"></div>

            <button class="btn btn-success add-result d-none" type="button">Добавить результат</button>
        </div>
    </div>

    <div class="panel panel-default">
        <header class="panel-heading">
            Типы и марки материала
            <span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
        </span>
        </header>
        <div class="panel-body">

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">МАТЕРИАЛ</label>
                <div class="col-sm-8">
                    <select class="form-control select2" name="form[dop_material]">
                        <option value="">Выбрать</option>
                        <?php foreach ($this->data['dop_material_list'] as $item): ?>
                            <option value="<?=$item['ID']?>" <?=$this->data['form']['dop_material'] == $item['ID'] ? 'selected' : ''?>><?=htmlentities($item['NAME'])?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                </div>
            </div>

            <div class="material-block">

                <div class="form-group row group-norm">
                    <label class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-text">Группа материала</span>
                            <input type="text" class="form-control" name="form[dop_v][0]" value="<?=$this->data['form']['dop_value'][0]?? ''?>">
                            <span class="input-group-text">Нормы от</span>
                            <input type="number" class="form-control" step="0.001" name="form[dop_n][0][0]" value="<?=$this->data['form']['dop_norm'][0][0]?? ''?>">
                            <span class="input-group-text">Нормы до</span>
                            <input type="number" class="form-control" step="0.001" name="form[dop_n][0][1]" value="<?=$this->data['form']['dop_norm'][0][1]?? ''?>">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <button id="add_group_norm" type="button" class="btn btn-primary btn-square" title="Добавить">
                            <i class="fa-solid fa-plus icon-fix"></i>
                        </button>
                    </div>
                </div>

                    <?php for ($i = 1; $i < count($this->data['form']['dop_value']); $i++): ?>
                        <div class="form-group row group-norm">
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-text">Группа материала</span>
                                    <input type="text" class="form-control" name="form[dop_v][<?=$i?>]" value="<?=$this->data['form']['dop_value'][$i]?? ''?>">
                                    <span class="input-group-text">Нормы от</span>
                                    <input type="number" class="form-control" step="0.001" name="form[dop_n][<?=$i?>][0]" value="<?=$this->data['form']['dop_norm'][$i][0]?? ''?>">
                                    <span class="input-group-text">Нормы до</span>
                                    <input type="number" class="form-control" step="0.001" name="form[dop_n][<?=$i?>][1]" value="<?=$this->data['form']['dop_norm'][$i][1]?? ''?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger btn-square remove_this" title="Удалить">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </div>
                        </div>
                    <? endfor; ?>

            </div>
        </div>
    </div>

    <button class="btn btn-primary me-3" type="submit" title="Сохранение ТУ">Сохранить</button>

    <?php if ( in_array($_SESSION['SESS_AUTH']['USER_ID'], [61, 10, 17, 222, 227])): ?>
<!--        <a class="btn btn-success me-3" href="--><?//=URI?><!--/techCondition/confirmMethod/--><?//=$this->data['form']['id']?><!--">Проверено</a>-->

        <?php if($this->data['form']['is_actual']): ?>
            <a class="btn btn-danger me-3" href="<?=URI?>/techCondition/nonActualMethod/<?=$this->data['form']['id']?>">Не актуально</a>
        <?php else: ?>
            <a class="btn btn-success me-3" href="<?=URI?>/techCondition/actualMethod/<?=$this->data['form']['id']?>">Актуально</a>
        <?php endif; ?>
    <?php endif; ?>
</form>
