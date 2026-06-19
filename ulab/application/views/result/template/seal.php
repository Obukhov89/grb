<div class="measurement-wrapper" id="grainWrapper">
    <h3 class="mb-3">Относительное уплотнение СТО НОСТРОЙ 2.25.26-2011 (п. 7.4)</h3>
    <input type="hidden" name="form_data[<?= $this->data['ugtp_id'] ?>][type]" value="seal">
    <input type="hidden" name="form_data[<?= $this->data['ugtp_id'] ?>][method_id]"
           value="<?= $this->data['measuring_property']['method_id'] ?>">
    <input id="ugtpId" type="hidden" name="form_data[<?= $this->data['ugtp_id'] ?>][ugtp_id]"
           value="<?= $this->data['ugtp_id'] ?>">

    <div class="line-dashed-small"></div>

    <table id="resultsTable" class="table table-bordered">
        <thead>
        <tr>
            <th rowspan="2">Объект испытаний (номер контролируемых участков)</th>
            <th rowspan="2">Определяемая характеристика (показатель)</th>
            <th rowspan="2">Ед. изм.</th>
            <th colspan="3">Требования нормативных документов (раздел, пункт)</th>
            <th rowspan="2">Документ, устанавливающий правила и методы исследований (испытаний) и измерений (раздел,
                пункт)
            </th>
            <th rowspan="2">Результаты испытаний (измерений)</th>
            <th rowspan="2">Соответствие характеристики (показателя) требованиям нормативной (проектной) документации
            </th>
            <th rowspan="2" colspan="2"></th>
        </tr>
        <tr>
            <th>Нормативная (проектная) документация (раздел, пункт)</th>
            <th>Наименование характеристики (показателя)</th>
            <th>Нормативное значение характеристики (показателя)</th>
        </tr>
        <tr>
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5</th>
            <th>6</th>
            <th>7</th>
            <th>8</th>
            <th>9</th>
            <th colspan="2"></th>
        </tr>
        </thead>
        <tbody id="tableBody">
        <?php if (empty($this->data['measuring'])): ?>
            <tr>
                <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][object_tested][]"
                           class="form-control form-control-sm"></td>
                <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][characteristic][]"
                           class="form-control form-control-sm" value="Относительное уплотнение (осадка по рейке)" readonly></td>
                <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][unit][]"
                           class="form-control form-control-sm" value="%" readonly></td>
                <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][normative_document][]"
                           class="form-control form-control-sm"></td>
                <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][characteristic_name][]"
                           class="form-control form-control-sm" value="Величина осадки слоя грунта, от его начальной мощности" readonly></td>
                <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][normative_value][]"
                           class="form-control form-control-sm" value="Не менее 10-12" readonly></td>
                <td><input type="text"
                           name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][test_method_document][]"
                           class="form-control form-control-sm"
                           value="СТО НОСТРОЙ 2.25.26-2011 (п. 7.4)" readonly></td>
                <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][test_results][]"
                           class="form-control form-control-sm"></td>
                <td>
                    <select name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][compliance][]"
                            class="form-select form-select-sm">
                        <option value="Соответствует">Соответствует</option>
                        <option value="Не соответствует">Не соответствует</option>
                    </select>
                </td>
                <td colspan="2">
                    <button type="button" class="btn mt-0 btn-square btn-primary add-row">+</button>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($this->data['measuring']['measuring']['unit'] as $k => $val): ?>
                <tr>
                    <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][object_tested][]"
                               class="form-control form-control-sm"
                               value="<?= $this->data['measuring']['measuring']['object_tested'][$k] ?>"></td>
                    <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][characteristic][]"
                               class="form-control form-control-sm"
                               value="<?= $this->data['measuring']['measuring']['characteristic'][$k] ?>"></td>
                    <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][unit][]"
                               class="form-control form-control-sm" value="%" readonly></td>
                    <td><input type="text"
                               name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][normative_document][]"
                               class="form-control form-control-sm"
                               value="<?= $this->data['measuring']['measuring']['normative_document'][$k] ?>"></td>
                    <td><input type="text"
                               name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][characteristic_name][]"
                               class="form-control form-control-sm"
                               value="<?= $this->data['measuring']['measuring']['characteristic_name'][$k] ?>"></td>
                    <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][normative_value][]"
                               class="form-control form-control-sm"
                               value="<?= $this->data['measuring']['measuring']['normative_value'][$k] ?>"></td>
                    <td><input type="text"
                               name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][test_method_document][]"
                               class="form-control form-control-sm"
                               value="СТО НОСТРОЙ 2.25.26-2011 (п. 7.4)" readonly></td>
                    <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][test_results][]"
                               class="form-control form-control-sm"
                               value="<?= $this->data['measuring']['measuring']['test_results'][$k] ?>"></td>
                    <td>
                        <select name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][compliance][]"
                                class="form-select form-select-sm">
                            <option <?= $this->data['measuring']['measuring']['compliance'][$k] == "Соответствует" ? 'selected' : '' ?>
                                    value="Соответствует">Соответствует
                            </option>
                            <option <?= $this->data['measuring']['measuring']['compliance'][$k] == "Не соответствует" ? 'selected' : '' ?>
                                    value="Не соответствует">Не соответствует
                            </option>
                        </select>
                    </td>
                    <td <?= $k == 0 ? 'colspan="2"' : '' ?>>
                        <button type="button" class="btn mt-0 btn-square btn-primary add-row">+</button>
                    </td>
                    <?php if ($k > 0): ?>
                        <td>
                            <button type="button" class="btn mt-0 btn-square btn-danger remove-row">-</button>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tr>
        </tbody>
    </table>

    <div class="line-dashed-small"></div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Схема участков</label>
        <div class="col-sm-8">
            <div class="input-group">
                <input type="file" name="form_data[<?=$this->data['ugtp_id']?>][drawing_file]" class="form-control" value="">
                <input
                        type="text"
                        name="form_data[<?=$this->data['ugtp_id']?>][drawing]"
                        class="form-control do-not-clean"
                        placeholder="Нет сохраненного файла"
                        value="<?=$this->data['measuring']['drawing'] ?? ''?>"
                        readonly>
                <?php if (!empty($this->data['measuring']['drawing'])): ?>
                    <a class="btn btn-outline-secondary btn-square btn-icon"
                       title="Скачать/Открыть" target="_blank"
                       href="<?=UPLOAD_URL?>/result/measurement/<?=$this->data['ugtp_id']?>/<?=$this->data['measuring']['drawing']?>">
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
            <small class="form-text text-muted">Загрузите изображение в формате .jpg, .jpeg или .png.</small>
        </div>
        <div class="col-sm-2"></div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary save-average">Сохранить</button>
</div>
