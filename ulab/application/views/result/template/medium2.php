<div class="measurement-wrapper" id="averageWrapper">
    <h3 class="mb-3">Методика с расчетом по 2 средним значениям</h3>

    <!--name="method_type" наименавание для всех типов метода одинаково-->
    <input type="hidden" id="methodType" name="method_type" value="TU_sred5">
	<input type="hidden" id="methodType" name="type" value="sred">

    <div class="form-group row">
        <div class="col-auto">
            <input class="form-check-input border-radius-0" type="checkbox" value="1" name="is_single_values"
                <?=$this->data['measuring']['is_single_values'] == 1? 'checked' : ''?>>
        </div>
        <label class="col col-form-label ps-0">Выводить единичные значения в протокол?</label>
    </div>

    <div class="mb-3">
        <label for="actualValue" class="form-label mb-1">Фактическое значение</label>
        <div class="td-actual-value">
            <div class="d-flex actual-value-wrapper mb-1">
                <input type="number" class="me-2 actual-value w-100 border p-2 bg-white"
                       name="actual_value[]" step="any"
                       value="<?= $this->data['measuring']['actual_value'][0] ?>" required>
                <button class="btn mt-0 btn-square add-value-actual btn-primary" type="button">
                    <i class="fa-solid fa-plus icon-fix"></i>
                </button>
            </div>
            <?php if ( !empty($this->data['measuring']['actual_value']) && count($this->data['measuring']['actual_value']) > 1 ): ?>
                <?php for ($i = 1; $i < count($this->data['measuring']['actual_value']); $i++): ?>
                    <div class="d-flex actual-value-wrapper mb-1">
                        <input type="number" class="me-2 actual-value w-100 bg-white"
                               name="actual_value[]" step="any"
                               value="<?= $this->data['measuring']['actual_value'][$i] ?>" required>
                        <button type="button" class="btn btn-square del-value-actual mt-0 btn-danger">
                            <i class="fa-solid fa-minus icon-fix"></i>
                        </button>
                    </div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="averageValue" class="form-label mb-1">Среднее значение</label>
            <!--name="result_value" наименавание инпута результат которого хотим видеть в результатах испытаний "Фактическое значение" в поле для всех одинаково-->
            <input type="number" class="me-2 w-100 border p-2 bg-white" id="averageValue" name="result_value"
                   step="any" value="<?= $this->data['measuring']['result_value'] ?>">
        </div>
        <div class="col">
            <label for="decimalPlaces" class="form-label mb-1">Знаков после запятой</label>
            <input type="number" class="me-2 w-100 border p-2 bg-white" id="decimalPlaces" name="decimal_places"
                   step="1" value="<?= $this->data['measuring_property']['decimal'] ?>">
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary calculate-average me-2">Рассчитать</button>
    <button type="submit" class="btn btn-primary save-average">Сохранить</button>
</div>
