<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <?php if ( !empty($this->data['oborud_id']) ): ?>
                <li class="nav-item me-2">
                    <a class="nav-link" href="<?=URI?>/oborud/edit/<?=$this->data['oborud_id']?>" title="Вернуться к оборудованию">
                        <i class="fa-solid fa-arrow-left-long"></i>
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item me-2">
                <a class="nav-link popup-with-form" href="#add-moving-modal-form" title="Добавить перемещения">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<div class="filters mb-4">
    <div class="row">
        <div class="col">
            <select id="selectOborud" class="form-control filter filter-oborud select2">
                <option value="">Всё оборудование</option>
                <?php foreach ($this->data['oborud_list'] as $item): ?>
                    <option value="<?=$item['ID']?>" <?=$this->data['oborud_id'] == $item['ID']? 'selected': ''?>><?=$item['view_name']?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
        </div>
    </div>
</div>

<table id="journal_moving" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap">Оборудование</th>
        <th scope="col" class="text-nowrap">Место перемещения</th>
        <th scope="col" class="text-nowrap">Дата</th>
        <th scope="col" class="">Комплектность оборудования</th>
        <th scope="col" class="">Отсутствие дефектов, повреждений</th>
        <th scope="col" class="">Паспорт</th>
        <th scope="col" class="">Руководство по эксплуатации</th>
        <th scope="col" class="">Документы о поверке/калибровке/аттестации/протокол измерения</th>
        <th scope="col" class="">Работоспособность</th>
        <th scope="col" class="">Комментарий</th>
        <th scope="col" class="">Ответственный за перемещение</th>
        <th scope="col" class="">Ответственный за получение</th>
        <th scope="col">Получение подтверждено</th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>


<form id="add-moving-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/oborud/addOborudMoving/" method="post" enctype="multipart/form-data">
    <div class="title mb-3 h-2">
        Добавление перемещения
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" name="journal_page" value="1">

    <div class="mb-3">
        <label class="form-label">Оборудование <span class="redStars">*</span></label>
        <select class="form-control select2" name="form[oborud_id]" required>
            <option value="">Не выбрано</option>
            <?php foreach ($this->data['oborud_list'] as $item): ?>
                <option value="<?=$item['ID']?>" <?=$this->data['oborud_id'] == $item['ID']? 'selected': ''?>><?=$item['view_name']?></option>
            <?php endforeach; ?>
        </select>
    </div>

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
        <select class="form-control" name="form[responsible_user_id]" required>
            <option value="">Не выбран</option>
            <?php foreach ($this->data['users'] as $user): ?>
                <option value="<?=$user['ID']?>" <?=$_SESSION['SESS_AUTH']['USER_ID'] == $user['ID']? 'selected': ''?>><?=$user['NAME']?> <?=$user['LAST_NAME']?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Ответственный за получение</label>
        <select class="form-control" name="form[receiver_user_id]">
            <option value="">Не выбран</option>
            <?php foreach ($this->data['users'] as $user): ?>
                <option value="<?=$user['ID']?>"><?=$user['NAME']?> <?=$user['LAST_NAME']?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>