<form action="/ulab/material/addNewSchemeMethods/" method="post">
    <div class="form-group row mb-3">
        <label class="col-2 col-form-label">Материал</label>
        <div class="col-8">
            <select class="form-control select2 material_select" name="material_id" required>
                <option value="">Выбрать материал</option>
                <?php foreach ($this->data['material_list'] as $item): ?>
                    <option value="<?=$item['id']?>" <?=$this->data['material_id'] == $item['id']? 'selected' : ''?>><?=$item['NAME']?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="form-group row mb-3">
        <label class="col-2 col-form-label">ГОСТ ТУ</label>
        <div class="col-8">
            <select class="form-control select2 tu_select" name="scheme_id" required>
                <option value="" selected>Выберете материал</option>
            </select>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="form-group row mb-3">
        <label class="col-2 col-form-label">Методики (мультивыбор)</label>
        <div class="col-8">
            <div class="input-group">
                <select class="form-control select2 method_select" name="methods[]" multiple required>
                    <?php foreach ($this->data['gost_list'] as $item):
                        $dataColor = '';
                        if ( $item['is_actual'] == 0 ) {
                            $dataColor = 'data-color="#F00"';
                        } else if ( $item['in_field'] == 0 ) {
                            $dataColor = 'data-color="#060060"';
                        } else if ( $item['is_confirm'] == 0 ) {
                            $dataColor = 'data-color="#dfdf11"';
                        }
                        ?>
                        <option value="<?=$item['id']?>" <?=$dataColor?>><?=$item['view_gost']?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-outline-secondary" type="submit">Добавить</button>
            </div>
        </div>
        <div class="col-2"></div>
    </div>
</form>

<table class="table table_gost">
    <thead>
    <tr>
        <th>ГОСТ</th>
        <th class="col-2">Стоимость, р</th>
        <th class="col-2"></th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3">Не выбрано ТУ</td>
        </tr>
    </tbody>
</table>