<div class="measurement-wrapper" id="grainWrapper">
    <h3 class="mb-3">Коэффициент уплотнения ПДУ-МГ4</h3>
    <input type="hidden" name="form_data[<?= $this->data['ugtp_id'] ?>][type]" value="seal">
    <input type="hidden" name="form_data[<?= $this->data['ugtp_id'] ?>][method_id]"
           value="<?= $this->data['measuring_property']['method_id'] ?>">
    <input id="ugtpId" type="hidden" name="form_data[<?= $this->data['ugtp_id'] ?>][ugtp_id]"
           value="<?= $this->data['ugtp_id'] ?>">

    <div class="line-dashed-small"></div>

    <table id="resultsTable" class="table table-bordered">
        <thead>
        <tr>
            <th class="text-center">Номер контролируемых пунктов</th>
            <th class="text-center">Определяемая характеристика (показатель)</th>
            <th class="text-center">Ед. изм.</th>
            <th class="text-center">Требуемое по проекту значение коэффициента уплотнения грунта</th>
            <th class="text-center">Документ, устанавливающий правила и методы исследований (испытаний) и измерений</th>
            <th class="text-center">Результаты исследований (испытаний) и измерений</th>
            <th class="text-center">Отметка о соответствии</th>
            <th class="text-center" colspan="2"></th>
        </tr>
        <tr>
            <th class="text-center">1</th>
            <th class="text-center">2</th>
            <th class="text-center">3</th>
            <th class="text-center">4</th>
            <th class="text-center">5</th>
            <th class="text-center">6</th>
            <th class="text-center">7</th>
            <th colspan="2"></th>
        </tr>
        </thead>
        <tbody id="tableBody">
        <?php if (empty($this->data['measuring'])): ?>
            <tr>
                <td rowspan="2" class="align-content-center"><input type="text"
                                       name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][object_tested][]"
                                       class="form-control form-control-sm"></td>
                <td><input type="text"
                           name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][characteristic][modul][]"
                           class="form-control form-control-sm" value="Модуль упругости" readonly></td>
                <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][unit][modul][]"
                           class="form-control form-control-sm" value="МН/м²" readonly></td>
                <td><input type="text"
                           name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][normative_value][modul][]"
                           class="form-control form-control-sm" value="-" readonly></td>
                <td rowspan="2" class="align-content-center"><input type="text"
                                       name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][test_method_document][]"
                                       class="form-control form-control-sm" value="Руководство по эксплуатации 7360-028-2010 РЭ" readonly></td>
                <td><input type="text"
                           name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][test_results][modul][]"
                           class="form-control form-control-sm"></td>
                <td rowspan="2" class="align-content-center">
                    <select name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][compliance][]"
                            class="form-select form-select-sm">
                        <option value="Соответствует">Соответствует</option>
                        <option value="Не соответствует">Не соответствует</option>
                    </select>
                </td>
                <td colspan="2" rowspan="2" class="text-center align-middle">
                    <button type="button" class="btn mt-0 btn-square btn-primary add-row">+</button>
                </td>
            </tr>
            <tr>
                <td><input type="text"
                           name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][characteristic][coef][]"
                           class="form-control form-control-sm" value="Коэффициент уплотнения" readonly></td>
                <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][unit][coef][]"
                           class="form-control form-control-sm" value="-" readonly></td>
                <td><input type="text"
                           name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][normative_value][coef][]"
                           class="form-control form-control-sm"></td>
                <td><input type="text"
                           name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][test_results][coef][]"
                           class="form-control form-control-sm"></td>
            </tr>
        <?php else: ?>
            <?php foreach ($this->data['measuring']['measuring']['unit']['modul'] as $k => $val): ?>
                <tr>
                    <td rowspan="2" class="align-content-center"><input type="text"
                                           name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][object_tested][]"
                                           class="form-control form-control-sm"
                                           value="<?= $this->data['measuring']['measuring']['object_tested'][$k] ?>">
                    </td>
                    <td><input type="text"
                               name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][characteristic][modul][]"
                               class="form-control form-control-sm" value="Модуль упругости" readonly></td>
                    <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][unit][modul][]"
                               class="form-control form-control-sm" value="МН/м²" readonly></td>
                    <td><input type="text"
                               name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][normative_value][modul][]"
                               class="form-control form-control-sm" value="-" readonly></td>
                    <td rowspan="2" class="align-content-center"><input type="text"
                                           name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][test_method_document][]"
                                           class="form-control form-control-sm"
                                           value="<?= $this->data['measuring']['measuring']['test_method_document'][$k] ?>">
                    </td>
                    <td><input type="text"
                               name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][test_results][modul][]"
                               class="form-control form-control-sm"
                               value="<?= $this->data['measuring']['measuring']['test_results']['modul'][$k] ?>"></td>
                    <td rowspan="2" class="align-content-center">
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
                    <td <?= $k == 0 ? 'colspan="2"' : '' ?> rowspan="2" class="text-center align-middle">
                        <button type="button" class="btn mt-0 btn-square btn-primary add-row">+</button>
                    </td>
                    <?php if ($k > 0): ?>
                        <td rowspan="2" class="text-center align-middle">
                            <button type="button" class="btn mt-0 btn-square btn-danger remove-row">-</button>
                        </td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td><input type="text"
                               name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][characteristic][coef][]"
                               class="form-control form-control-sm" value="Коэффициент уплотнения" readonly></td>
                    <td><input type="text" name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][unit][coef][]"
                               class="form-control form-control-sm" value="-" readonly></td>
                    <td><input type="text"
                               name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][normative_value][coef][]"
                               class="form-control form-control-sm"
                               value="<?= $this->data['measuring']['measuring']['normative_value']['coef'][$k] ?>"></td>
                    <td><input type="text"
                               name="form_data[<?= $this->data['ugtp_id'] ?>][measuring][test_results][coef][]"
                               class="form-control form-control-sm"
                               value="<?= $this->data['measuring']['measuring']['test_results']['coef'][$k] ?>"></td>
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
