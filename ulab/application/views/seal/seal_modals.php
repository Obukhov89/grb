<!-- добавление записи в журнал -->
<form method="post" action="/ulab/seal/create" id="create_row"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Добавить</h3>
    <div class="line-dashed-small"></div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="date_field_input" class="col-form-label">Дата</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="date_field" id="date_field_input">
                <option value="0">Выбрать</option>
                <?php foreach ($this->data['creation_data']['dates'] as $date) { ?>
                    <option value="<?= $date['inspection_date'] ?>"><?= $date['inspection_date'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="coordinates_input" class="col-form-label">В осях/координаты</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="coordinates" id="coordinates_input">
                <option value="0">Выбрать</option>
                <?php foreach ($this->data['creation_data']['coordinates'] as $coordinates) { ?>
                    <option value="<?= $coordinates['coordinates'] ?>"><?= $coordinates['coordinates'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="remark_input" class="col-form-label">Отметка</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="remark_input" name="remark" class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="protocol_input" class="col-form-label">Протокол</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="protocol_input" name="protocol" class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="photo_input" class="col-form-label">Фото протокола</label>
        </div>
        <div class="col-sm-6">
            <input required type="file" accept="image/*" id="photo_input" name="photo"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>

<form method="post" action="/ulab/seal/edit" id="edit_row"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Редактирование</h3>
    <div class="line-dashed-small"></div>
    <input type="hidden" name="row_id" id="row_id" />


    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="date_field_input" class="col-form-label">Дата</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="date_field" id="date_field_input">
                <option value="0">Выбрать</option>
                <?php foreach ($this->data['creation_data']['dates'] as $date) { ?>
                    <option value="<?= $date['inspection_date'] ?>"><?= $date['inspection_date'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="coordinates_input" class="col-form-label">В осях/координаты</label>
        </div>
        <div class="col-sm-6">
            <select required class="form-control justify-content-between" name="coordinates" id="coordinates_input">
                <option value="0">Выбрать</option>
                <?php foreach ($this->data['creation_data']['coordinates'] as $coordinates) { ?>
                    <option value="<?= $coordinates['coordinates'] ?>"><?= $coordinates['coordinates'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="remark_input" class="col-form-label">Отметка</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="remark_input" name="remark" class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="protocol_input" class="col-form-label">Протокол</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="protocol_input" name="protocol" class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="photo_input" class="col-form-label">Фото</label>
        </div>
        <div class="col-sm-6">
            <div class="block">
                <a id="photo_link" href="" target="_blank"></a>
                <div class="image" style="z-index: 9999;">
                    <img id="photo_path" src="" width="300" height="400" alt="Фото">
                </div>
            </div>
            <input type="file" accept="image/*" id="photo_input" name="photo"
                   class="form-control justify-content-between"/>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button title="Close (Esc)" style="color: black;" type="button" class="mfp-close">×</button>
</form>