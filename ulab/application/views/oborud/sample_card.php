<header class="mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/oborud/sampleList/" title="Список образецов контроля">
                    <svg class="icon" width="25" height="25">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#card"/>
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/oborud/sampleCard/" title="Новый образец контроля">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link popup-help" href="<?=URI?>/help/LIMS_Manual_Stand/VLK/Sample_card/Sample_card.html" title="ПОМОГИТЕ">
                    <i class="fa-solid fa-question"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<div class="panel panel-default">
    <header class="panel-heading">
        <?php if (!empty($this->data['id'])): ?>
            <span class="rounded px-3 py-2 me-2 <?=$this->data['stage']['bgStage']?>" title="<?=$this->data['stage']['titleStage']?>"></span>
        <?php endif; ?>
        Образец контроля

        <span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
        </span>
    </header>
    <div class="panel-body">
        <form class="form-horizontal" method="post" action="<?=URI?>/oborud/sampleInsertUpdate/" enctype="multipart/form-data">
            <?php if ( isset($this->data['id']) && !empty($this->data['id']) ): ?>
                <input type="hidden" value="<?=$this->data['id']?>" name="id">
            <?php endif; ?>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Наименование <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <input type="text" name="sample[NAME]" class="form-control" value="<?=$this->data['sample']['NAME'] ?? ''?>" required>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Тип <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <input type="text" name="sample[TYPE]" class="form-control" value="<?=$this->data['sample']['TYPE'] ?? ''?>" required>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Номер <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <input type="text" name="sample[NUMBER]" class="form-control" value="<?=$this->data['sample']['NUMBER'] ?? ''?>" required>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Категория</label>
                <div class="col-sm-8">
                    <input type="text" name="sample[CATEGORY]" class="form-control" value="<?=$this->data['sample']['CATEGORY'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Номер в Госреестре</label>
                <div class="col-sm-8">
                    <input type="text" name="sample[REG_NUM]" class="form-control" value="<?=$this->data['sample']['REG_NUM'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Изготовитель <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <input type="text" name="sample[MANUFACTURER]" class="form-control" value="<?=$this->data['sample']['MANUFACTURER'] ?? ''?>" required>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Назначение <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <textarea name="sample[PURPOSE]" class="form-control" style="height: 80px;"><?=$this->data['sample']['PURPOSE'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Описание типа СО</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="DESCRIPTION_SO_FILE" class="form-control" value="">
                        <input
                                type="text"
                                name="sample[DESCRIPTION_SO]"
                                class="form-control"
                                placeholder="Нет сохраненного файла"
                                value="<?=$this->data['sample']['DESCRIPTION_SO'] ?? ''?>"
                                readonly
                        >
                        <?php if (!empty($this->data['sample']['DESCRIPTION_SO'])): ?>
                            <a
                                    class="btn btn-outline-secondary btn-square btn-icon"
                                    title="Скачать/Открыть"
                                    href="<?=UPLOAD_URL?>/oborud/description_so/<?=$this->data['id']?>/<?=$this->data['sample']['DESCRIPTION_SO']?>"
                            >
                                <i class="fa-regular fa-file-lines"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-square btn-icon delete_file" title="Удалить">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php else: ?>
                            <a class="btn btn-outline-secondary btn-square btn-icon disabled" title="Скачать/Открыть" >
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
                <label class="col-sm-2 col-form-label">Фотография СО</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="PHOTO_SO_FILE" class="form-control" value="">
                        <input
                                type="text"
                                name="sample[PHOTO_SO]"
                                class="form-control"
                                placeholder="Нет сохраненного файла"
                                value="<?=$this->data['sample']['PHOTO_SO'] ?? ''?>"
                                readonly
                        >
                        <?php if (!empty($this->data['sample']['PHOTO_SO'])): ?>
                            <a
                                    class="btn btn-outline-secondary btn-square btn-icon"
                                    title="Скачать/Открыть"
                                    href="<?=UPLOAD_URL?>/oborud/photo_so/<?=$this->data['id']?>/<?=$this->data['sample']['PHOTO_SO']?>"
                            >
                                <i class="fa-regular fa-file-lines"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-square btn-icon delete_file" title="Удалить">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php else: ?>
                            <a class="btn btn-outline-secondary btn-square btn-icon disabled" title="Скачать/Открыть" >
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
                <label class="col-sm-2 col-form-label">Скан-копия документа о праве собственности на СО</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="PROOF_OF_OWNERSHIP_FILE" class="form-control" value="">
                        <input
                                type="text"
                                name="sample[PROOF_OF_OWNERSHIP]"
                                class="form-control"
                                placeholder="Нет сохраненного файла"
                                value="<?=$this->data['sample']['PROOF_OF_OWNERSHIP'] ?? ''?>"
                                readonly
                        >
                        <?php if (!empty($this->data['sample']['PROOF_OF_OWNERSHIP'])): ?>
                            <a
                                    class="btn btn-outline-secondary btn-square btn-icon"
                                    title="Скачать/Открыть"
                                    href="<?=UPLOAD_URL?>/oborud/proof_of_ownership/<?=$this->data['id']?>/<?=$this->data['sample']['PROOF_OF_OWNERSHIP']?>"
                            >
                                <i class="fa-regular fa-file-lines"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-square btn-icon delete_file" title="Удалить">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php else: ?>
                            <a class="btn btn-outline-secondary btn-square btn-icon disabled" title="Скачать/Открыть" >
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
                    <select class="form-control" name="sample[PURCHASE_STATE]">
                        <option value="new" <?=$this->data['sample']['PURCHASE_STATE'] == 'new' ? 'selected' : ''?>>Новое</option>
                        <option value="old" <?=$this->data['sample']['PURCHASE_STATE'] == 'old' ? 'selected' : ''?>>Б/у</option>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Паспорт / руководство по эксплуатации</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="MANUAL_OR_PASSPORT_FILE" class="form-control" value="">
                        <input
                                type="text"
                                name="sample[MANUAL_OR_PASSPORT]"
                                class="form-control"
                                placeholder="Нет сохраненного файла"
                                value="<?=$this->data['sample']['MANUAL_OR_PASSPORT'] ?? ''?>"
                                readonly
                        >
                        <?php if (!empty($this->data['sample']['MANUAL_OR_PASSPORT'])): ?>
                            <a
                                    class="btn btn-outline-secondary btn-square btn-icon"
                                    title="Скачать/Открыть"
                                    href="<?=UPLOAD_URL?>/oborud/manual_or_passport/<?=$this->data['id']?>/<?=$this->data['sample']['MANUAL_OR_PASSPORT']?>"
                            >
                                <i class="fa-regular fa-file-lines"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-square btn-icon delete_file" title="Удалить">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php else: ?>
                            <a class="btn btn-outline-secondary btn-square btn-icon disabled" title="Скачать/Открыть" >
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
                <label class="col-sm-2 col-form-label">Дополнительные сведения</label>
                <div class="col-sm-8">
                    <textarea name="sample[DOP]" class="form-control" style="height: 80px;"><?=$this->data['sample']['DOP'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Нормативный документ, порядок и условия применения</label>
                <div class="col-sm-8">
                    <input type="text" name="sample[NORM_DOC]" class="form-control" value="<?=$this->data['sample']['NORM_DOC'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Документ о праве собственности</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="OWNERSHIP_DOCUMENT_FILE" class="form-control" value="">
                        <input
                                type="text"
                                name="sample[OWNERSHIP_DOCUMENT]"
                                class="form-control"
                                placeholder="Нет сохраненного файла"
                                value="<?=$this->data['sample']['OWNERSHIP_DOCUMENT'] ?? ''?>"
                                readonly
                        >
                        <?php if (!empty($this->data['sample']['OWNERSHIP_DOCUMENT'])): ?>
                            <a
                                    class="btn btn-outline-secondary btn-square btn-icon"
                                    title="Скачать/Открыть"
                                    href="<?=UPLOAD_URL?>/oborud/ownership_document/<?=$this->data['id']?>/<?=$this->data['sample']['OWNERSHIP_DOCUMENT']?>"
                            >
                                <i class="fa-regular fa-file-lines"></i>
                            </a>
                            <a class="btn btn-outline-danger btn-square btn-icon delete_file" title="Удалить">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        <?php else: ?>
                            <a class="btn btn-outline-secondary btn-square btn-icon disabled" title="Скачать/Открыть" >
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
                <label class="col-sm-2 col-form-label">Срок годности</label>
                <div class="col-sm-8">
                    <input type="text" name="sample[SHELF_LIFE]" class="form-control" value="<?=$this->data['sample']['SHELF_LIFE'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Дата выпуска</label>
                <div class="col-sm-8">
                    <input type="date" name="sample[MANUFACTURE_DATE]" class="form-control" value="<?=$this->data['sample']['MANUFACTURE_DATE'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Годен до</label>
                <div class="col-sm-8">
                    <input type="date" name="sample[EXPIRY_DATE]" class="form-control" value="<?=$this->data['sample']['EXPIRY_DATE'] ?? ''?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Срок годности не ограничен</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="checkbox" name="sample[UNLIMITED_EXPIRY]" class="form-check-input" value="1"
                            <?=$this->data['sample']['UNLIMITED_EXPIRY'] == 1 ? 'checked' : ''?>>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Примечание</label>
                <div class="col-sm-8">
                    <textarea name="sample[COMMENT]" class="form-control" style="height: 80px;"><?=$this->data['sample']['COMMENT'] ?? ''?></textarea>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Лаборатория</label>
                <div class="col-sm-8">
                    <select id="select-lab" class="form-control" name="sample[LAB_ID]">
                        <option value="0">Выберите лабораторию</option>
                        <?php foreach ($this->data['lab_list'] as $item): ?>
                            <option value="<?=$item['ID']?>" <?=$item['ID'] == $this->data['sample']['LAB_ID']? 'selected' : ''?>><?=$item['NAME']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Помещение</label>
                <div class="col-sm-8">
                    <select id="select-room" class="form-control select2" name="sample[ROOM_ID]">
                        <option value="0">Выберите помещение</option>
                        <?php if (empty($this->data['room_list'])): ?>
                            <option value="" disabled>Сначала выберите лабораторию</option>
                        <?php endif; ?>

                        <?php foreach ($this->data['room_list'] as $item): ?>
                            <option value="<?=$item['ID']?>" <?=$item['ID'] == $this->data['sample']['ROOM_ID']? 'selected' : ''?>><?=$item['name']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Ответственный</label>
                <div class="col-sm-8">
                    <select id="select-assigned" class="form-control select2" name="sample[ASSIGNED_ID]">
                        <option value="0">Выберите ответственного</option>
                        <?php foreach ($this->data['lab_user_list'] as $lab): ?>
                            <option value="" disabled><strong><?=$lab['short_name']?></strong></option>
                            <?php foreach ($lab['users'] as $user): ?>
                                <option value="<?=$user['id']?>" <?=$user['id'] == $this->data['sample']['ASSIGNED_ID']? 'selected' : ''?>><?=$user['last_name']?> <?=$user['name']?></option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <?php if ($this->data['is_may_change']): ?>
            <div class="line-dashed"></div>

            <div class="d-flex justify-content-between">
                <button class="btn btn-primary save-sample" type="submit">Сохранить</button>


                <?php if (!empty($this->data['id'])): ?>
                    <a class="btn btn-danger non-actual-sample"
                       href="<?=URI?>/oborud/nonActualSample/<?=$this->data['id']?>"
                       title="Отметить образец контроля как неактуальный"
                    >Не актуально</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="panel panel-default" id="component-block">
    <header class="panel-heading">
        Метрологические характеристики
        <span class="tools float-end">
        <a href="#" class="fa fa-chevron-up"></a>
     </span>
    </header>
    <div class="panel-body">
        <?php if ( isset($this->data['id']) && !empty($this->data['id']) ): ?>
            <?php if ($this->data['is_may_change']): ?>
            <button class="btn btn-success popup-with-component" type="button">Добавить</button>

            <div class="line-dashed"></div>
            <?php endif; ?>

            <table class="table table-striped text-center">
                <thead>
                <tr class="table-secondary align-middle">
                    <th scope="col" class="border-0">Наименование</th>
                    <th scope="col" class="border-0 text-nowrap">Аттестованное значение</th>
                    <th scope="col" class="border-0"></th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($this->data['components'])): ?>
                    <tr><td colspan="8">Метрологические характеристики не добавлены</td></tr>
                <?php endif; ?>

                <?php foreach ($this->data['components'] as $component): ?>
                    <tr class="align-middle">
                        <td>
                            <?=$component['name']?>
                        </td>
                        <td>
                            <?=$component['certified_value']?>
                        </td>
                        <td>
                            <?php if ($this->data['is_may_change']): ?>
                            <div class="text-end">
                                <a
                                        href="#" data-component="<?=$component['id']?>"
                                        class="btn btn-success me-1 update-component"
                                        title="Редактировать компонент">
                                    <i class="fa-solid fa-pencil icon-fix"></i>
                                </a>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div>Создайте образец контроля, чтобы добавить метрологические характеристики</div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($this->data['id'])): ?>
<div class="line-dashed"></div>
<a href="/standart_sample.php?ID=<?=$this->data['id']?>">Вернуться на старый дизайн</a>
<?php endif; ?>

<form id="component-modal-form" class="bg-light mfp-hide col-md-6 m-auto p-3 position-relative"
      action="<?=URI?>/oborud/componentInsert/" method="post">
    <div class="title mb-3 h-2">
        Данные метрологической характеристики
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" id="stSampleId" name="component[st_sample_id]" value="<?=$this->data['id']?>">
    <input type="hidden" name="component_id" value="" id="componentId">

    <div class="mb-3">
        <label class="form-label mb-1">Наименование <span class="redStars">*</span></label>
        <input type="text" class="form-control" id="name" name="component[name]" value="" maxlength="64" required>
    </div>

    <div class="row align-items-end mb-3">
        <div class="col">
            <label class="form-label mb-1">Аттестованное значение <span class="redStars">*</span></label>
            <input type="number" name="component[certified_value]" class="form-control bg-white" id="certifiedValue" value="" step="any" required>
        </div>
        <div class="col">
            <label class="form-label mb-1">Единица измерения <span class="redStars">*</span></label>
            <select class="form-control select2" id="certifiedUnitId" name="component[certified_unit_id]" required>
                <option value="0">Выбрать</option>
                <?php foreach ($this->data['unit_list'] as $unit): ?>
                    <option value="<?=$unit['id']?>" <?=$this->data['component']['certified_unit_id'] == $unit['id'] ? 'selected' : ''?>><?=htmlentities($unit['unit_rus'])?> | <?=htmlentities($unit['name'])?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row align-items-end mb-3">
        <div class="col">
            <label class="form-label mb-1">Неопределенность</label>
            <input type="number" name="component[uncertainty]" class="form-control bg-white" id="uncertainty" value="" step="any">
        </div>
        <div class="col">
            <label class="form-label mb-1">Единица измерения</label>
            <select class="form-control select2" id="uncertaintyUnitId" name="component[uncertainty_unit_id]">
                <option value="0">Выбрать</option>
                <?php foreach ($this->data['unit_list'] as $unit): ?>
                    <option value="<?=$unit['id']?>" <?=$this->data['component']['uncertainty_unit_id'] == $unit['id'] ? 'selected' : ''?>><?=htmlentities($unit['unit_rus'])?> | <?=htmlentities($unit['name'])?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row align-items-end mb-3">
        <div class="col">
            <label class="form-label mb-1">Характеристика погрешности аттестованного значения</label>
            <input type="number" name="component[error_characteristic]" class="form-control bg-white" id="errorCharacteristic" value="" step="any">
        </div>
        <div class="col">
            <label class="form-label mb-1">Единица измерения</label>
            <select class="form-control select2" id="characteristicUnitId" name="component[characteristic_unit_id]">
                <option value="0">Выбрать</option>
                <?php foreach ($this->data['unit_list'] as $unit): ?>
                    <option value="<?=$unit['id']?>" <?=$this->data['component']['characteristic_unit_id'] == $unit['id'] ? 'selected' : ''?>><?=htmlentities($unit['unit_rus'])?> | <?=htmlentities($unit['name'])?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if ($this->data['is_may_change']): ?>
    <div class="line-dashed-small"></div>
    <button type="submit" class="btn btn-primary save-component">Сохранить</button>
    <?php endif; ?>
</form>

<div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2 alert-title"></div>

    <div class="line-dashed-small"></div>

    <div class="mb-3 alert-content"></div>
</div>
<!--./alert_modal-->