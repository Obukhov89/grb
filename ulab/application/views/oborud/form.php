<?php
//if(!in_array($this->data['currentUser']['id'], [61, 10, 17, 222, 227, 250])):
//    ?>
<!--    <style>-->
<!--        .form-control, .select2-selection, .form-check-input, input, .btn, .form-check-label {-->
<!--            user-select: none;-->
<!--            pointer-events: none;-->
<!--            background-color: lightgray !important;-->
<!--        }-->
<!--    </style>-->
<?php
//endif;
//?>

<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="/ulab/oborud/list/" title="Вернуться к списку">
                    <i class="fa-solid fa-list"></i>
                </a>
            </li>
            <?php if (!empty($this->data['id'])): ?>
                <li class="nav-item me-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Скачать
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item" href="/protocol_generator/oborud_card.php?OB_ID=<?=$this->data['id']?>">
                                    Скачать карточку оборудования
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/protocol_generator/oborud_akt_vvoda.php?OB_ID=<?=$this->data['id']?>">
                                    Скачать акт ввода
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/protocol_generator/oborud_akt_sp.php?OB_ID=<?=$this->data['id']?>">
                                    Скачать акт списания
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/protocol_generator/oborud_akt_post.php?OB_ID=<?=$this->data['id']?>">
                                    Скачать акт о постановке на длительное хранение
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/protocol_generator/oborud_akt_ver.php?OB_ID=<?=$this->data['id']?>">
                                    Скачать акт о верификации оборудования
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/protocol_generator/oborud_card_once.php?OB_ID=<?=$this->data['id']?>">
                                    Скачать этикетку оборудования
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            <li class="nav-item me-2">
                <a class="nav-link popup-help" href="/ulab/help/LIMS_Manual_Stand/Equipment_card/Equipment_card.html" title="ПОМОГИТЕ">
                    <i class="fa-solid fa-question"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<?php if (!empty($this->data['id'])): ?>
    <h2 class="d-flex mb-3">
        <div class="stage-block rounded <?=$this->data['status']['bgStage']?> me-1 mt-1" title="<?=$this->data['status']['titleStage']?>"></div>
        <?=$this->data['oborud']['OBJECT']?> <?=$this->data['oborud']['REG_NUM']?>
    </h2>

    <div class="panel panel-default">
        <header class="panel-heading">
            Общее
            <span class="tools float-end">
                    <a href="#" class="fa fa-chevron-up"></a>
                </span>
        </header>
        <div class="panel-body">

            <div class="form-group row">
                <label for="name" class="col-2 col-form-label">
                    В области аккредитации
                </label>
                <label class="col-2 col-form-label">
                    <?=$this->data['oborud']['IN_AREA']? 'Да' : 'Нет'?>
                </label>
                <div class="col-2"></div>

                <label for="name" class="col-2 col-form-label">
                    Списан
                </label>
                <label class="col-2 col-form-label">
                    <?=$this->data['oborud']['is_decommissioned']? 'Да' : 'Нет'?>
                </label>
                <div class="col-2"></div>
            </div>

            <div class="form-group row">
                <label for="name" class="col-2 col-form-label">
                    Подлежит периодическому метрологическому контролю
                </label>
                <label class="col-2 col-form-label">
                    <?=$this->data['oborud']['NO_METR_CONTROL']? 'Нет' : 'Да'?>
                </label>
                <div class="col-2"></div>

                <label for="name" class="col-2 col-form-label">
                    На длительном хранении
                </label>
                <label class="col-2 col-form-label">
                    <?=$this->data['oborud']['LONG_STORAGE']? 'Да' : 'Нет'?>
                </label>
                <div class="col-2"></div>
            </div>

            <div class="form-group row">
                <label for="name" class="col-2 col-form-label">
                    Проверено
                </label>
                <label class="col-2 col-form-label">
                    <?=$this->data['oborud']['CHECKED']? 'Да' : 'Нет'?>
                </label>
                <div class="col-2"></div>
            </div>

        </div>
    </div>
<?php endif; ?>

<form class="form-horizontal" id="create_oborud_form" method="post" action="<?=URI?>/oborud/insertUpdate/" enctype="multipart/form-data">
    <?php if ( isset($this->data['id']) ): ?>
        <input type="hidden" value="<?=$this->data['id']?>" name="id">
    <?php endif; ?>
    <div class="panel panel-default">
        <header class="panel-heading">
            Основные характеристики
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">

            <div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">
                    Наименование испытаний, определяемая хар-ка или назначение
                </label>
                <div class="col-sm-8">
                    <textarea id="name" class="form-control like-input" name="oborud[NAME]"><?=$this->data['oborud']['NAME'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Наименование <span class="redStars">*</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="oborud[OBJECT]" value="<?=$this->data['oborud']['OBJECT'] ?? ''?>" required>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label for="type" class="col-sm-2 col-form-label">
                    Тип оборудования
                </label>
                <div class="col-sm-8">
                    <textarea id="type" class="form-control like-input" name="oborud[TYPE_OBORUD]"><?=$this->data['oborud']['TYPE_OBORUD'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    № по Государственному реестру РФ
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[GOSREESTR]" class="form-control" value="<?=$this->data['oborud']['GOSREESTR'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Идентификация оборудования
                </label>
                <div class="col-sm-8">
                    <select id="select-ident" class="form-control" name="oborud[IDENT]">
                        <option value="VO" <?=$this->data['oborud']['ident_en'] == 'VO' ? 'selected' : ''?>>ВО</option>
                        <option value="SI" <?=$this->data['oborud']['ident_en'] == 'SI' ? 'selected' : ''?>>СИ</option>
                        <option value="IO" <?=$this->data['oborud']['ident_en'] == 'IO' ? 'selected' : ''?>>ИО</option>
                        <option value="SO" <?=$this->data['oborud']['ident_en'] == 'SO' ? 'selected' : ''?>>CO</option>
                        <option value="KO" <?=$this->data['oborud']['ident_en'] == 'KO' ? 'selected' : ''?>>КО</option>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Группа испытываемых объектов
                </label>
                <div class="col-sm-8">
                    <textarea id="type" class="form-control like-input" name="oborud[OBJECT_GROUP]"><?=$this->data['oborud']['OBJECT_GROUP'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Страна, производитель
                </label>
                <div class="col-sm-8">
                    <textarea id="type" class="form-control like-input" name="oborud[manufacturer]"><?=$this->data['oborud']['manufacturer'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Инвентарный номер
                </label>
                <div class="col-sm-8">
                    <?php if (in_array($_SESSION['SESS_AUTH']['USER_ID'], [61, 10, 32])): ?>
                        <input type="text" name="oborud[REG_NUM]" class="form-control" value="<?=$this->data['oborud']['REG_NUM'] ?? '(сформируется автоматически)'?>">
                    <?php else: ?>
                        <input type="text" name="" class="form-control" value="<?=$this->data['oborud']['REG_NUM'] ?? '(сформируется автоматически)'?>" readonly>
                    <?php endif; ?>
                </div>
                <div class="col-sm-2"></div>
            </div>
            <?php if ( !empty($this->data['id']) && in_array($_SESSION['SESS_AUTH']['USER_ID'], [61, 10, 32])): ?>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Порядковый номер
                    </label>
                    <div class="col-sm-8">
                        <input type="number" name="oborud[number]" class="form-control" value="<?=$this->data['oborud']['number'] ?? '1'?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            <?php endif; ?>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Вагон
                </label>
                <div class="col-sm-8 pt-2">
                    <input type="checkbox" name="oborud[is_vagon]" class="form-check-input" value="1" <?=$this->data['oborud']['is_vagon'] == 1 ? 'checked' : ''?>>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Заводской номер
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[FACTORY_NUMBER]" class="form-control" value="<?=$this->data['oborud']['FACTORY_NUMBER'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Год выпуска
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[YEAR]" class="form-control" value="<?=$this->data['oborud']['YEAR'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Дата ввода в эксплуатацию <span class="redStars">*</span>
                </label>
                <div class="col-sm-8">
                    <input type="date" name="oborud[god_vvoda_expluatation]" class="form-control" value="<?=$this->data['oborud']['god_vvoda_expluatation'] ?? date('Y-m-d')?>" required>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Описание типа оборудования
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="desc_oborud_file" class="form-control" value="">
                        <input
                                type="text"
                                name="oborud[desc_oborud]"
                                class="form-control"
                                placeholder="Нет сохраненного файла"
                                value="<?=$this->data['oborud']['desc_oborud'] ?? ''?>"
                                readonly
                        >
                        <?php if (!empty($this->data['oborud']['desc_oborud'])): ?>
                            <?php if ( file_exists($_SERVER['DOCUMENT_ROOT'] . "/file_oborud/{$this->data['id']}/{$this->data['oborud']['desc_oborud']}") ): ?>
                                <a
                                        class="btn btn-outline-secondary btn-square-2 btn-icon"
                                        title="Скачать/Открыть"

                                        href="/file_oborud/<?=$this->data['id']?>/<?=$this->data['oborud']['desc_oborud']?>"
                                >
                                    <i class="fa-regular fa-file-lines"></i>
                                </a>
                            <?php else: ?>
                                <a
                                        class="btn btn-outline-secondary btn-square-2 btn-icon"
                                        title="Скачать/Открыть"

                                        href="/file_oborud/<?=$this->data['id']?>/description_oborud/<?=$this->data['oborud']['desc_oborud']?>"
                                >
                                    <i class="fa-regular fa-file-lines"></i>
                                </a>
                            <?php endif; ?>
                            <a class="btn btn-outline-danger btn-square btn-icon delete_file" style="border-color: #ced4da;"title="Удалить">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php else: ?>
                            <a class="btn btn-outline-secondary btn-square-2 btn-icon disabled" title="Скачать/Открыть" >
                                <i class="fa-regular fa-file-lines"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-square btn-icon disabled" title="Удалить" >
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Фотография оборудования
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="photo_oborud_file" class="form-control">
                        <input type="text" name="oborud[photo_oborud]" class="form-control" placeholder="Нет сохраненного файла" value="<?=$this->data['oborud']['photo_oborud'] ?? ''?>" readonly>
                        <?php if (!empty($this->data['oborud']['photo_oborud'])): ?>
                            <?php if (  file_exists($_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $this->data['id'] . '/' . $this->data['oborud']['photo_oborud']) ): ?>
                                <a
                                        class="btn btn-outline-secondary btn-square-2 btn-icon"
                                        title="Скачать/Открыть"
                                        href="/file_oborud/<?=$this->data['id']?>/<?=$this->data['oborud']['photo_oborud']?>"
                                >
                                    <i class="fa-regular fa-file-lines"></i>
                                </a>
                            <?php else: ?>
                                <a
                                        class="btn btn-outline-secondary btn-square-2 btn-icon"
                                        title="Скачать/Открыть"
                                        href="/file_oborud/<?=$this->data['oborud']['photo_oborud']?>"
                                >
                                    <i class="fa-regular fa-file-lines"></i>
                                </a>
                            <?php endif; ?>
                            <a class="btn btn-outline-danger btn-square btn-icon delete_file" style="border-color: #ced4da;" title="Удалить">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php else: ?>
                            <a class="btn btn-outline-secondary btn-square-2 btn-icon disabled" title="Скачать/Открыть">
                                <i class="fa-regular fa-file-lines"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-square btn-icon disabled" title="Удалить">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Скан-копия документа о праве собственности на оборудование
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="property_rights_file" class="form-control">
                        <input type="text" name="oborud[property_rights_pdf]" class="form-control" placeholder="Нет сохраненного файла" value="<?=$this->data['oborud']['property_rights_pdf'] ?? ''?>" readonly>
                        <?php if (!empty($this->data['oborud']['property_rights_pdf'])): ?>
                            <?php if (  file_exists($_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $this->data['id'] . '/' . $this->data['oborud']['property_rights_pdf']) ): ?>
                                <a
                                        class="btn btn-outline-secondary btn-square-2 btn-icon"
                                        title="Скачать/Открыть"

                                        href="/file_oborud/<?=$this->data['id']?>/<?=$this->data['oborud']['property_rights_pdf']?>"
                                >
                                    <i class="fa-regular fa-file-lines"></i>
                                </a>
                            <?php else: ?>
                                <a
                                        class="btn btn-outline-secondary btn-square-2 btn-icon"
                                        title="Скачать/Открыть"

                                        href="/file_oborud/<?=$this->data['oborud']['property_rights_pdf']?>"
                                >
                                    <i class="fa-regular fa-file-lines"></i>
                                </a>
                            <?php endif; ?>
                            <a class="btn btn-outline-danger btn-square btn-icon delete_file" style="border-color: #ced4da;"title="Удалить">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php else: ?>
                            <a class="btn btn-outline-secondary btn-square-2 btn-icon disabled" title="Скачать/Открыть" >
                                <i class="fa-regular fa-file-lines"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-square btn-icon disabled" title="Удалить" >
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Состояние при покупке
                </label>
                <div class="col-sm-8">
                    <select class="form-control" name="oborud[COND]">
                        <option value="NEW" <?=$this->data['oborud']['COND'] == 'NEW' ? 'selected' : ''?>>Новое</option>
                        <option value="OLD" <?=$this->data['oborud']['COND'] == 'OLD' ? 'selected' : ''?>>Б/у</option>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Паспорт / руководство по эксплуатации
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="passport_oborud" class="form-control">
                        <input type="text" name="oborud[passport_pdf]" class="form-control" placeholder="Нет сохраненного файла" value="<?=$this->data['oborud']['passport_pdf'] ?? ''?>" readonly>
                        <?php if (!empty($this->data['oborud']['passport_pdf'])): ?>
                            <?php if (  file_exists($_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $this->data['id'] . '/' . $this->data['oborud']['passport_pdf']) ): ?>
                                <a
                                        class="btn btn-outline-secondary btn-square-2 btn-icon"
                                        title="Скачать/Открыть"

                                        href="/file_oborud/<?=$this->data['id']?>/<?=$this->data['oborud']['passport_pdf']?>"
                                >
                                    <i class="fa-regular fa-file-lines"></i>
                                </a>
                            <?php else: ?>
                                <a
                                        class="btn btn-outline-secondary btn-square-2 btn-icon"
                                        title="Скачать/Открыть"

                                        href="/file_oborud/<?=$this->data['id']?>/passport/<?=$this->data['oborud']['passport_pdf']?>"
                                >
                                    <i class="fa-regular fa-file-lines"></i>
                                </a>
                            <?php endif; ?>
                            <a class="btn btn-outline-danger btn-square btn-icon delete_file" style="border-color: #ced4da;" title="Удалить">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php else: ?>
                            <a class="btn btn-outline-secondary btn-square-2 btn-icon disabled" title="Скачать/Открыть" >
                                <i class="fa-regular fa-file-lines"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-square btn-icon disabled" title="Удалить" >
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Паспорт / руководство по эксплуатации 2
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="passport_oborud_2" class="form-control">
                        <input type="text" name="oborud[passport_pdf_2]" class="form-control" placeholder="Нет сохраненного файла" value="<?=$this->data['oborud']['passport_pdf_2'] ?? ''?>" readonly>
                        <?php if (!empty($this->data['oborud']['passport_pdf_2'])): ?>
                            <?php if (  file_exists($_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $this->data['id'] . '/' . $this->data['oborud']['passport_pdf_2']) ): ?>
                                <a
                                        class="btn btn-outline-secondary btn-square-2 btn-icon"
                                        title="Скачать/Открыть"

                                        href="/file_oborud/<?=$this->data['id']?>/<?=$this->data['oborud']['passport_pdf_2']?>"
                                >
                                    <i class="fa-regular fa-file-lines"></i>
                                </a>
                            <?php else: ?>
                                <a
                                        class="btn btn-outline-secondary btn-square-2 btn-icon"
                                        title="Скачать/Открыть"

                                        href="/file_oborud/<?=$this->data['id']?>/passport/<?=$this->data['oborud']['passport_pdf_2']?>"
                                >
                                    <i class="fa-regular fa-file-lines"></i>
                                </a>
                            <?php endif; ?>
                            <a class="btn btn-outline-danger btn-square btn-icon delete_file" style="border-color: #ced4da;" title="Удалить">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php else: ?>
                            <a class="btn btn-outline-secondary btn-square-2 btn-icon disabled" title="Скачать/Открыть" >
                                <i class="fa-regular fa-file-lines"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-square btn-icon disabled" title="Удалить" >
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Ид ФСА
                </label>
                <div class="col-sm-8">
                    <input type="number" name="oborud[id_fsa]" class="form-control" value="<?=$this->data['oborud']['id_fsa']?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    В области аккредитации
                </label>
                <div class="col-sm-8 pt-2">
                    <input type="checkbox" name="oborud[IN_AREA]" class="form-check-input <?=in_array($_SESSION["SESS_AUTH"]["USER_ID"], [10, 222])? '' : 'disabled'?>" value="1" <?=$this->data['oborud']['IN_AREA'] == 1 ? 'checked' : ''?>>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Проверено
                </label>
                <div class="col-sm-8 pt-2">
                    <input type="checkbox" name="oborud[CHECKED]" class="form-check-input " value="1" <?=$this->data['oborud']['CHECKED'] == 1 ? 'checked' : ''?>>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <header class="panel-heading">
            Условия эксплуатации
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Относительная влажность воздуха, %
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-text">от</span>
                        <input type="text" class="form-control" name="oborud[OVV_EX]" value="<?=$this->data['oborud']['OVV_EX'] ?? ''?>">
                        <span class="input-group-text">до</span>
                        <input type="text" class="form-control" name="oborud[OVV_EX2]" value="<?=$this->data['oborud']['OVV_EX2'] ?? ''?>">
                        <div class="input-group-text">
                            <input id="ch1" class="form-check-input mt-0 me-1" type="checkbox" value="1" name="oborud[HUMIDITY]" <?=$this->data['oborud']['HUMIDITY'] == 1 ? 'checked' : ''?>>
                            <label class="form-check-label" for="ch1">
                                Не нормируется
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Температура окружающего воздуха, ºС
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-text">от</span>
                        <input type="text" class="form-control" name="oborud[TOO_EX]" value="<?=$this->data['oborud']['TOO_EX'] ?? ''?>">
                        <span class="input-group-text">до</span>
                        <input type="text" class="form-control" name="oborud[TOO_EX2]" value="<?=$this->data['oborud']['TOO_EX2'] ?? ''?>">
                        <div class="input-group-text">
                            <input id="ch2" class="form-check-input mt-0 me-1" type="checkbox" value="1" name="oborud[TEMPERATURE]" <?=$this->data['oborud']['TEMPERATURE'] == 1 ? 'checked' : ''?>>
                            <label class="form-check-label" for="ch2">
                                Не нормируется
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Атмосферное давление, кПа
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-text">от</span>
                        <input type="text" class="form-control" name="oborud[AD_EX]" value="<?=$this->data['oborud']['AD_EX'] ?? ''?>">
                        <span class="input-group-text">до</span>
                        <input type="text" class="form-control" name="oborud[AD_EX2]" value="<?=$this->data['oborud']['AD_EX2'] ?? ''?>">
                        <div class="input-group-text">
                            <input id="ch3" class="form-check-input mt-0 me-1" type="checkbox" value="1" name="oborud[PRESSURE]" <?=$this->data['oborud']['PRESSURE'] == 1 ? 'checked' : ''?>>
                            <label class="form-check-label" for="ch3">
                                Не нормируется
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Напряжение питающей сети, В
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[NAPR]" class="form-control" value="<?=$this->data['oborud']['NAPR'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Частота, Гц
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[FREQ]" class="form-control" value="<?=$this->data['oborud']['FREQ'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <header class="panel-heading">
            Условия хранения
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Относительная влажность воздуха, %
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[OVV_UH]" class="form-control" value="<?=$this->data['oborud']['OVV_UH'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Температура окружающего воздуха, ºС
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[TOO_UH]" class="form-control" value="<?=$this->data['oborud']['TOO_UH'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Атмосферное давление, кПа
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[AD_UH]" class="form-control" value="<?=$this->data['oborud']['AD_UH'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <header class="panel-heading">
            Технические и метрологические характеристики
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Технические характеристики
                </label>
                <div class="col-sm-8">
                    <textarea class="form-control like-input" name="oborud[TECH]"><?=$this->data['oborud']['TECH'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Диапазон измерения
                </label>
                <div class="col-sm-8">
                    <textarea class="form-control like-input" name="oborud[measuring_range]"><?=$this->data['oborud']['measuring_range'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Класс точности и (разряд), погрешность
                </label>
                <div class="col-sm-8">
                    <textarea class="form-control like-input" name="oborud[сlass_precision_and_accuracy]"><?=$this->data['oborud']['сlass_precision_and_accuracy'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row precision-block-title">
                <label class="col-sm-2 col-form-label">

                </label>
                <div class="col-sm-8">
                    <table id="precision_table" class="table">
                        <thead>
                        <tr>
                            <th scope="col">Наименование показателя/характеристики</th>
                            <th scope="col">Ед. изм.</th>
                            <th scope="col">Диапазон измерения</th>
                            <th scope="col">ПГ</th>
                            <th scope="col">Ед. изм.</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody class="precision_table--container">
                        <tr class="precision_table--block" data-number-row="0">
                            <td>
                                <input type="text" class="form-control" name="precision_table[0][name]" value="<?=$this->data['oborud']['precision_table'][0]['name']?? ''?>">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="precision_table[0][unit1]" value="<?=$this->data['oborud']['precision_table'][0]['unit1']?? ''?>">
                            </td>
                            <td class="precision_table--range-container">
                                <div class="input-group precision_table--range-start-block">
                                    <span class="input-group-text">от</span>
                                    <input type="text" class="form-control" name="precision_table[0][ot][]" value="<?=$this->data['oborud']['precision_table'][0]['ot'][0]?? ''?>">
                                    <span class="input-group-text">до</span>
                                    <input type="text" class="form-control" name="precision_table[0][do][]" value="<?=$this->data['oborud']['precision_table'][0]['do'][0]?? ''?>">
                                    <button type="button" class="btn btn-success btn-square add-range" title="Добавить">
                                        <i class="fa-solid fa-plus icon-fix"></i>
                                    </button>
                                </div>

                                <?php for ($j = 1; $j < count($this->data['oborud']['precision_table'][0]['ot']); $j++): ?>
                                    <div class="input-group precision_table--range-block pt-2">
                                        <span class="input-group-text">от</span>
                                        <input type="text" class="form-control" name="precision_table[0][ot][]" value="<?=$this->data['oborud']['precision_table'][0]['ot'][$j]?? ''?>">
                                        <span class="input-group-text">до</span>
                                        <input type="text" class="form-control" name="precision_table[0][do][]" value="<?=$this->data['oborud']['precision_table'][0]['do'][$j]?? ''?>">
                                        <button type="button" class="btn btn-danger btn-square delete-range" title="Удалить">
                                            <i class="fa-solid fa-minus icon-fix"></i>
                                        </button>
                                    </div>
                                <?php endfor; ?>
                            </td>
                            <td>
                                <div class="precision_table--pg-block">
                                    <input type="text" class="form-control" name="precision_table[0][pg][]" value="<?=$this->data['oborud']['precision_table'][0]['pg'][0]?? $this->data['oborud']['precision_table'][0]['pg']?? ''?>">
                                </div>
                            </td>
                            <td>
                                <div class="precision_table--unit2-block">
                                    <input type="text" class="form-control" name="precision_table[0][unit2][]" value="<?=$this->data['oborud']['precision_table'][0]['unit2'][0]?? $this->data['oborud']['precision_table'][0]['unit2']?? ''?>">
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-success btn-square add-precision" title="Добавить">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </td>
                        </tr>

                        <?php for ($i = 1; $i < count($this->data['oborud']['precision_table']); $i++): ?>
                            <tr class="precision_table--block" data-number-row="<?=$i?>">
                                <td>
                                    <input type="text" class="form-control" name="precision_table[<?=$i?>][name]" value="<?=$this->data['oborud']['precision_table'][$i]['name']?? ''?>">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="precision_table[<?=$i?>][unit1]" value="<?=$this->data['oborud']['precision_table'][$i]['unit1']?? ''?>">
                                </td>
                                <td class="precision_table--range-container">
                                    <div class="input-group precision_table--range-start-block">
                                        <span class="input-group-text">от</span>
                                        <input type="text" class="form-control" name="precision_table[<?=$i?>][ot][]" value="<?=$this->data['oborud']['precision_table'][$i]['ot'][0]?? ''?>">
                                        <span class="input-group-text">до</span>
                                        <input type="text" class="form-control" name="precision_table[<?=$i?>][do][]" value="<?=$this->data['oborud']['precision_table'][$i]['do'][0]?? ''?>">
                                        <button type="button" class="btn btn-success btn-square add-range" title="Добавить">
                                            <i class="fa-solid fa-plus icon-fix"></i>
                                        </button>
                                    </div>

                                    <?php for ($j = 1; $j < count($this->data['oborud']['precision_table'][$i]['ot']); $j++): ?>
                                        <div class="input-group precision_table--range-block pt-2">
                                            <span class="input-group-text">от</span>
                                            <input type="text" class="form-control" name="precision_table[<?=$i?>][ot][]" value="<?=$this->data['oborud']['precision_table'][$i]['ot'][$j]?? ''?>">
                                            <span class="input-group-text">до</span>
                                            <input type="text" class="form-control" name="precision_table[<?=$i?>][do][]" value="<?=$this->data['oborud']['precision_table'][$i]['do'][$j]?? ''?>">
                                            <button type="button" class="btn btn-danger btn-square delete-range" title="Удалить">
                                                <i class="fa-solid fa-minus icon-fix"></i>
                                            </button>
                                        </div>
                                    <?php endfor; ?>
                                </td>
                                <td>
                                    <div class="input-group precision_table--pg-block">
                                        <input type="text" class="form-control" name="precision_table[<?=$i?>][pg][]" value="<?=$this->data['oborud']['precision_table'][$i]['pg'][0]?? $this->data['oborud']['precision_table'][$i]['pg']?? ''?>">
                                    </div>
                                    <?php for ($j = 1; $j < count($this->data['oborud']['precision_table'][$i]['pg']); $j++): ?>
                                        <div class="precision_table--pg-block pt-2">
                                            <input type="text" class="form-control" name="precision_table[<?=$i?>][pg][]" value="<?=$this->data['oborud']['precision_table'][$i]['pg'][$j]?? ''?>">
                                        </div>
                                    <?php endfor; ?>
                                </td>
                                <td>
                                    <div class="input-group precision_table--unit2-block">
                                        <input type="text" class="form-control" name="precision_table[<?=$i?>][unit2][]" value="<?=$this->data['oborud']['precision_table'][$i]['unit2'][0]?? $this->data['oborud']['precision_table'][$i]['unit2']?? ''?>">
                                    </div>
                                    <?php for ($j = 1; $j < count($this->data['oborud']['precision_table'][$i]['unit2']); $j++): ?>
                                        <div class="precision_table--unit2-block pt-2">
                                            <input type="text" class="form-control" name="precision_table[<?=$i?>][unit2][]" value="<?=$this->data['oborud']['precision_table'][$i]['unit2'][$j]?? ''?>">
                                        </div>
                                    <?php endfor; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-square delete-precision" title="Удалить">
                                        <i class="fa-solid fa-minus icon-fix"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-2">

                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Закон распределения
                </label>
                <div class="col-sm-8">
                    <select class="form-control" name="oborud[distribution]">
                        <option value="">Не выбран</option>
                        <option value="uniform" <?=$this->data['oborud']['distribution'] == 'uniform' ? 'selected' : ''?>>равномерный α = √3</option>
                        <option value="triangular" <?=$this->data['oborud']['distribution'] == 'triangular' ? 'selected' : ''?>>треугольный α = √6</option>
                        <option value="arcsine" <?=$this->data['oborud']['distribution'] == 'arcsine' ? 'selected' : ''?>>арксинусный α = √2</option>
                        <option value="normal" <?=$this->data['oborud']['distribution'] == 'normal' ? 'selected' : ''?>>нормальный α = 2</option>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>

    <div id="certificate-block" class="panel panel-default">
        <header class="panel-heading">
            Метрологический контроль
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <?php if (!empty($this->data['id'])): ?>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-8">
                        <a href="#add-certificate-modal-form" class="popup-with-form btn btn-success">Добавить документ</a>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Оборудование в поверке/калибровке
                    </label>
                    <div class="col-sm-8 pt-2">
                        <input type="checkbox" name="oborud[IN_POVERKA]" class="form-check-input" value="1" <?=$this->data['oborud']['IN_POVERKA']? 'checked': ''?>>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
                <?php foreach ($this->data['certificate'] as $row): ?>
                    <div class="line-dashed"></div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">
                            Актуальный документ
                        </label>
                        <div class="col-sm-8 pt-2">
                            <input type="checkbox" name="certificate[<?=$row['id']?>][is_actual]" class="form-check-input" value="1" <?=$row['is_actual']? 'checked': ''?>>
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">
                            Дата документа
                        </label>
                        <div class="col-sm-8">
                            <input type="date" name="certificate[<?=$row['id']?>][date_start]" class="form-control" value="<?=$row['date_start'] ?? ''?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">
                            Срок действия
                        </label>
                        <div class="col-sm-8">
                            <input type="date" name="certificate[<?=$row['id']?>][date_end]" class="form-control" value="<?=$row['date_end'] ?? ''?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">
                            Номер документа
                        </label>
                        <div class="col-sm-8">
                            <input type="text" name="certificate[<?=$row['id']?>][name]" class="form-control" value="<?=$row['name'] ?? ''?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">
                            Файл
                        </label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="file" name="certificate[<?=$row['id']?>]" class="form-control" value="">
                                <input type="text" name="certificate[<?=$row['id']?>][file]" class="form-control" placeholder="Нет сохраненного файла" value="<?=$row['file'] ?? ''?>" readonly>
                                <?php if (!empty($row['file'])): ?>
                                    <?php if ( file_exists($_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $this->data['id'] . '/' . $row['file']) ): ?>
                                        <a
                                                class="btn btn-outline-secondary btn-square-2 btn-icon"
                                                title="Скачать/Открыть"

                                                href="/file_oborud/<?=$this->data['id']?>/<?=$row['file']?>"
                                        >
                                            <i class="fa-regular fa-file-lines"></i>
                                        </a>
                                    <?php else: ?>
                                        <a
                                                class="btn btn-outline-secondary btn-square-2 btn-icon"
                                                title="Скачать/Открыть"

                                                href="/file_oborud/<?=$row['file']?>"
                                        >
                                            <i class="fa-regular fa-file-lines"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a class="btn btn-outline-danger btn-square btn-icon delete_file" style="border-color: #ced4da;"title="Удалить">
                                        <i class="fa-solid fa-xmark"></i>
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-outline-secondary btn-square-2 btn-icon disabled" title="Скачать/Открыть" >
                                        <i class="fa-regular fa-file-lines"></i>
                                    </a>
                                    <a class="btn btn-outline-danger btn-square btn-icon disabled" title="Удалить" >
                                        <i class="fa-solid fa-xmark"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">
                            Ссылка на ФГИС Аршин
                        </label>
                        <div class="col-sm-8">
                            <input type="text" name="certificate[<?=$row['id']?>][link_fgis]" maxlength="255" class="form-control" value="<?=$row['link_fgis'] ?? ''?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">
                            Аттестованные значения
                        </label>
                        <div class="col-sm-8">
                            <input type="text" name="certificate[<?=$row['id']?>][certified_values]" maxlength="255" class="form-control" value="<?=$row['certified_values'] ?? ''?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>
                <?php endforeach;?>
            <?php else: ?>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-8">
                        Для добавления сертификата, сохраните оборудование
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            <?php endif; ?>

            <div class="line-dashed"></div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Периодичность аттестации/поверки/калибровки
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[MC_INTERVAL]" class="form-control" value="<?=$this->data['oborud']['MC_INTERVAL'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Вид метрологического контроля
                </label>
                <div class="col-sm-8">
                    <select class="form-control" name="oborud[METR_CONTROL]">
                        <option value="ATTESTATION" <?=$this->data['oborud']['METR_CONTROL'] == 'ATTESTATION' ? 'selected' : ''?>>Аттестация</option>
<!--                        <option value="CALIBR" --><?//=$this->data['oborud']['METR_CONTROL'] == 'CALIBR' ? 'selected' : ''?><!-->Калибровка</option>-->
                        <option value="POVERKA" <?=$this->data['oborud']['METR_CONTROL'] == 'POVERKA' || $this->data['oborud']['METR_CONTROL'] == 'CALIBR' ? 'selected' : ''?>>Поверка/Калибровка</option>
                        <option value="TECH_CHAR" <?=$this->data['oborud']['METR_CONTROL'] == 'TECH_CHAR' ? 'selected' : ''?>>Проверка тех. харктеристик</option>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Контролирующая организация
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[POVERKA_PLACE]" class="form-control" value="<?=$this->data['oborud']['POVERKA_PLACE'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Не подлежит периодическому контролю
                </label>
                <div class="col-sm-8 pt-2">
                    <input type="checkbox" name="oborud[NO_METR_CONTROL]" class="form-check-input" value="1" <?=$this->data['oborud']['NO_METR_CONTROL'] == 1 ? 'checked' : ''?>>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>

    <div class="panel panel-default" id="moving-block">
        <header class="panel-heading">
            Местонахождение и собственность
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Право собственности
                </label>
                <div class="col-sm-8">
                    <textarea class="form-control like-input" name="oborud[property_rights]"><?=$this->data['oborud']['property_rights'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Подразделение ИЦ <span class="redStars">*</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control select2" name="oborud[place_of_installation_or_storage]" required>
                        <option value="">Выберите</option>
                        <?php if ($this->data['lab']): ?>
                            <?php foreach ($this->data['lab'] as $lab): ?>
                                <option value="<?=$lab['ID']?>" <?=$this->data['oborud']['place_of_installation_or_storage'] == $lab['ID'] ? 'selected' : ''?>><?=$lab['NAME'] ?? ''?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Место установки или хранения
                </label>
                <div class="col-sm-8">
                    <select class="form-control select2" name="oborud[roomnumber]">
                        <option value="">Выберите</option>
                        <?php foreach ($this->data['lab_room'] as $item): ?>
                            <?php if ($item['id'] < 100): ?>
                                <option value="" style="font-weight:bold" disabled><?=$item['name']?></option>
                            <?php else: ?>
                                <option value="<?=$item['id'] - 100?>" <?=$this->data['oborud']['roomnumber'] == ($item['id'] - 100) ? 'selected' : ''?>> -- <?=$item['name']?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
<!--                        --><?php //if ($this->data['rooms']): ?>
<!--                            --><?php //foreach ($this->data['rooms'] as $room): ?>
<!--                                <option value="--><?//=$room['ID']?><!--" --><?//=$this->data['oborud']['roomnumber'] == $room['ID'] ? 'selected' : ''?><!-->--><?//=$room['NAME']?><!-- --><?//=$room['NUMBER']?><!--</option>-->
<!--                            --><?php //endforeach; ?>
<!--                        --><?php //endif; ?>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Переносное оборудование
                </label>
                <div class="col-sm-8 pt-2">
                    <input type="checkbox" name="oborud[is_portable]" class="form-check-input" value="1" <?=$this->data['oborud']['is_portable'] == 1 ? 'checked' : ''?>>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="line-dashed"></div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-8">
                    <?php if ($this->data['id']): ?>
                        <a href="#add-moving-modal-form" class="popup-with-form btn btn-success">Добавить перемещение</a>
                    <?php else: ?>
                        Добавить перемещение можно будет после создания оборудования
                    <?php endif; ?>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <?php if ($this->data['id']): ?>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Место перемещения
                    </label>
                    <div class="col-sm-8">
                        <input class="form-control" placeholder="Не перемещался" value="<?=$this->data['moving']['place'] ?? ''?>" readonly>
                    </div>
                    <div class="col-sm-2">
                        <a class="btn btn-square btn-outline-secondary" href="/ulab/oborud/movingJournal/<?=$this->data['id']?>" title="Журнал перемещений">
                            <i class="fa-solid fa-list icon-fix-2"></i>
                        </a>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Ответственный за перемещение
                    </label>
                    <div class="col-sm-8">
                        <select class="form-control" disabled>
                            <option value="">Не выбран</option>
                            <?php foreach ($this->data['users'] as $user): ?>
                                <option value="<?=$user['ID']?>" <?=$this->data['moving']['responsible_user_id'] == $user['ID'] ? 'selected' : ''?>><?=$user['NAME']?> <?=$user['LAST_NAME']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Ответственный за получение
                    </label>
                    <div class="col-sm-8">
                        <select class="form-control" disabled>
                            <option value="">Не выбран</option>
                            <?php foreach ($this->data['users'] as $user): ?>
                                <option value="<?=$user['ID']?>" <?=$this->data['moving']['receiver_user_id'] == $user['ID'] ? 'selected' : ''?>><?=$user['NAME']?> <?=$user['LAST_NAME']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel panel-default">
        <header class="panel-heading">
            Ввод в эксплуатацию и списание
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Первичный ввод в эксплуатацию произвел
                </label>
                <div class="col-sm-8">
                    <select class="form-control select2" name="oborud[ASSIGNED]">
                        <option value="">Выберите</option>
                        <?php foreach ($this->data['commissioning_user'] as $user): ?>
                            <option value="<?=$user['id']?>" <?=$this->data['oborud']['ASSIGNED'] == $user['id'] ? 'selected' : ''?>><?=$user['short_name']?></option>
                        <?php endforeach; ?>
                        <?php
                            if ( !empty($this->data['oborud']['ASSIGNED'])
                                && !in_array($this->data['oborud']['ASSIGNED'], array_column($this->data["commissioning_user"], "id"))
                            ):
                        ?>
                            <option value="<?=$user['id']?>" data-color="#F00" selected>Неизвестный пользователь</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="line-dashed"></div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Дата приказа о вводе в экспл.
                </label>
                <div class="col-sm-8">
                    <input type="date" name="oborud[DATE_PRIKAZ]" class="form-control" value="<?=$this->data['oborud']['DATE_PRIKAZ'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="line-dashed"></div>

            <?php if ( !empty($this->data['oborud']['LONG_STORAGE']) ): ?>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        На длительном хранении
                    </label>
                    <div class="col-sm-8 pt-2">
                        <input type="checkbox" name="oborud[LONG_STORAGE]" class="form-check-input" value="1" <?=!empty($this->data['oborud']['LONG_STORAGE'])? 'checked' : ''?>>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Дата постановки на длительное хранение
                    </label>
                    <div class="col-sm-8">
                        <input type="date" name="oborud[LONG_STORAGE_DATE]" class="form-control" value="<?=$this->data['oborud']['LONG_STORAGE_DATE'] ?? ''?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            <?php else: ?>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-8">
                        <a href="#long-storage-modal-form" class="popup-with-form btn btn-danger">На длительное хранение</a>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            <?php endif; ?>

            <div class="line-dashed"></div>

            <?php if ($this->data['oborud']['is_decommissioned']): ?>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Основание для списания
                    </label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?=$this->data['oborud']['SPISANIE'] ?? ''?>" readonly>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">
                        Дата списания
                    </label>
                    <div class="col-sm-8">
                        <input type="date" class="form-control" value="<?=$this->data['oborud']['DATE_SP'] ?? ''?>" readonly>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            <?php else: ?>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-8">
                        <a href="#decommissioned-modal-form" class="popup-with-form btn btn-danger">Списать оборудование</a>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel panel-default">
        <header class="panel-heading">
            Ответственные лица
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Ответственный сотрудник
                </label>
                <div class="col-sm-8">
                    <select class="form-control select2" name="oborud[ID_ASSIGN1]">
                        <option value="">Не указан</option>
                        <?php foreach ($this->data['users'] as $user): ?>
                            <option value="<?=$user['ID']?>" <?=$this->data['oborud']['ID_ASSIGN1'] == $user['ID'] ? 'selected' : ''?>><?=$user['NAME']?> <?=$user['LAST_NAME']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Доп. ответственный сотрудник
                </label>
                <div class="col-sm-8">
                    <select class="form-control select2" name="oborud[ID_ASSIGN2]">
                        <option value="">Не указан</option>
                        <?php foreach ($this->data['users'] as $user): ?>
                            <option value="<?=$user['ID']?>" <?=$this->data['oborud']['ID_ASSIGN2'] == $user['ID'] ? 'selected' : ''?>><?=$user['NAME']?> <?=$user['LAST_NAME']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <header class="panel-heading">
            Программное обеспечение
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Наименование ПО
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[PO_NAME]" class="form-control" value="<?=$this->data['oborud']['PO_NAME'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Идент. номер версии ПО
                </label>
                <div class="col-sm-8">
                    <input type="text" name="oborud[PO_ID]" class="form-control" value="<?=$this->data['oborud']['PO_ID'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <header class="panel-heading">
            Дополнительная информация
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Наличие документа (паспорт, рук-во)
                </label>
                <div class="col-sm-8">
                    <textarea class="form-control like-input" name="oborud[DOCUMENT]"><?=$this->data['oborud']['DOCUMENT'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Примечание
                </label>
                <div class="col-sm-8">
                    <textarea class="form-control like-input" name="oborud[note]"><?=$this->data['oborud']['note'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <header class="panel-heading">
            Взаимозаменяемое оборудование
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <div class="form-group row head-inter-oborud border-bottom pb-3">
                <label class="col-sm-2 col-form-label">
                    Оборудование
                </label>
                <div class="col-sm-8">
                    <select class="form-control select2">
                        <option value="">Не выбрано</option>
                        <?php foreach ($this->data['oborud_list'] as $item): ?>
                            <option value="<?=$item['ID']?>" <?=$this->data['oborud_id'] == $item['ID']? 'selected': ''?>><?=$item['view_name']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-success btn-square add-inter-oborud" title="Добавить оборудование">
                        <i class="fa-solid fa-plus icon-fix"></i>
                    </button>
                </div>
            </div>

            <?php foreach ($this->data['interchangeable'] as $item): ?>
                <div id="inter-oborud<?=$item['ID']?>" class="form-group row block-inter-oborud" data-id="<?=$item['ID']?>">
                    <label class="col-sm-2 col-form-label">

                    </label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" class="form-control" value="<?=$item['view_name']?>">
                            <a class="btn btn-outline-secondary" target="_blank" title="Перейти в оборудование" href="/ulab/oborud/edit/<?=$item['ID']?>">
                                <i class="fa-solid fa-right-to-bracket"></i>
                            </a>
                        </div>
                        <input type="hidden" name="inter[]" class="form-control" value="<?=$item['ID']?>">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-danger btn-square delete-inter-oborud" title="Отвязать оборудование">
                            <i class="fa-solid fa-minus icon-fix"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($this->data['id']): ?>
    <div class="panel panel-default">
        <header class="panel-heading">
            В методиках
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <?php foreach ($this->data['method_list'] as $method): ?>
                <a href="/ulab/gost/method/<?=$method['id']?>" target="_blank"><?=$method['view_gost']?></a><br>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <button class="btn btn-primary" id="save_button" type="submit" name="save">Сохранить</button>
</form>
<script>
    document.getElementById("create_oborud_form").addEventListener("submit", function () {
        document.getElementById("save_button").disabled = true;
    });
</script>



<form id="decommissioned-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/oborud/decommissioned/" method="post">
    <div class="title mb-3 h-2">
        Списание оборудования
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" name="oborud_id" value="<?=$this->data['id']?>">

    <div class="mb-3">
        <label class="form-label">Основание для списания</label>
        <input type="text" name="form[SPISANIE]" class="form-control" value="" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Дата списания</label>
        <input type="date" name="form[DATE_SP]" class="form-control" value="<?=date('Y-m-d')?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Заменить списанное оборудование в методиках на</label>
        <select class="form-control select2" name="change_oborud_id">
            <option value="">Не выбрано</option>
            <?php foreach ($this->data['oborud_list'] as $item): ?>
                <option value="<?=$item['ID']?>" <?=$this->data['oborud_id'] == $item['ID']? 'selected': ''?>><?=$item['view_name']?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>

<form id="long-storage-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/oborud/setLongStorage/" method="post">
    <div class="title mb-3 h-2">
        Постановка на длительное хранение
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" name="oborud_id" value="<?=$this->data['id']?>">

    <div class="mb-3">
        <label class="form-label">Дата</label>
        <input type="date" name="form[LONG_STORAGE_DATE]" class="form-control" value="<?=date('Y-m-d')?>" required>
        <input type="hidden" name="form[LONG_STORAGE]" class="form-check-input" value="1">
    </div>

    <div class="mb-3">
        <label class="form-label">Заменить оборудование в методиках на</label>
        <select class="form-control select2" name="change_oborud_id">
            <option value="">Не выбрано</option>
            <?php foreach ($this->data['oborud_list'] as $item): ?>
                <option value="<?=$item['ID']?>" <?=$this->data['oborud_id'] == $item['ID']? 'selected': ''?>><?=$item['view_name']?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>

<form id="add-certificate-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/oborud/addCertificate/" method="post" enctype="multipart/form-data">
    <div class="title mb-3 h-2">
        Добавление документа
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" name="form[oborud_id]" value="<?=$this->data['id']?>">

    <div class="mb-3 form-check">
        <input type="checkbox" name="form[is_actual]" class="form-check-input" id="exampleCheck1" value="1" checked>
        <label class="form-check-label" for="exampleCheck1">Актуальный документ</label>
    </div>

    <div class="mb-3">
        <label class="form-label">Дата документа</label>
        <input type="date" name="form[date_start]" class="form-control" value="<?=date('Y-m-d')?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Срок действия</label>
        <input type="date" name="form[date_end]" class="form-control" value="<?=date('Y-m-d', strtotime("+1 year"))?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Номер документа</label>
        <input type="text" name="form[name]" class="form-control" value="">
    </div>

    <div class="mb-3">
        <label class="form-label">Файл</label>
        <input type="file" name="file" class="form-control" value="">
    </div>

    <div class="mb-3">
        <label class="form-label">Ссылка на ФГИС Аршин</label>
        <input type="text" name="form[link_fgis]" maxlength="255" class="form-control" value="">
    </div>

    <div class="mb-3">
        <label class="form-label">Аттестованные значения</label>
        <input type="text" name="form[certified_values]" class="form-control" maxlength="255" value="">
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>


<form id="add-moving-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/oborud/addOborudMoving/" method="post" enctype="multipart/form-data">
    <div class="title mb-3 h-2">
        Добавление перемещения
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" name="form[oborud_id]" value="<?=$this->data['id']?>">

    <div class="mb-3 form-check">
        <input type="checkbox" name="form[is_return]" class="form-check-input" id="is_return_check" value="1">
        <label class="form-check-label" for="is_return_check">Оборудование возвращено</label>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" name="form[is_return]" class="form-check-input" id="is_new_check" value="1">
        <label class="form-check-label" for="is_new_check">Оборудование куплено</label>
    </div>

    <div class="mb-3" id="place-moving-block">
        <label class="form-label">Место перемещения <span class="redStars">*</span></label>
        <input type="text" name="form[place]" class="form-control" value="" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Комплектность оборудования</label>
        <textarea type="text" name="form[completeness]" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Отсутствие дефектов, повреждений</label>
        <textarea type="text" name="form[no_defects]" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Паспорт</label>
        <textarea type="text" name="form[passport]" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Руководство по эксплуатации</label>
        <textarea type="text" name="form[manual]" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Документы о поверке/калибровке/аттестации/протокол измерения</label>
        <textarea type="text" name="form[documents]" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Работоспособность</label>
        <textarea type="text" name="form[performance]" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Примечание</label>
        <textarea type="text" name="form[comment]" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Ответственный за перемещение <span class="redStars">*</span></label>
        <select class="form-control select2" name="form[responsible_user_id]" required>
            <option value="">Не выбран</option>
            <?php foreach ($this->data['users'] as $user): ?>
                <option value="<?=$user['ID']?>" <?=$_SESSION['SESS_AUTH']['USER_ID'] == $user['ID']? 'selected': ''?>><?=$user['NAME']?> <?=$user['LAST_NAME']?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Ответственный за получение</label>
        <select class="form-control select2" name="form[receiver_user_id]">
            <option value="">Не выбран</option>
            <?php foreach ($this->data['users'] as $user): ?>
                <option value="<?=$user['ID']?>"><?=$user['NAME']?> <?=$user['LAST_NAME']?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>