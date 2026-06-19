<div class="measurement-wrapper" id="averageWrapper">
    <h3 class="mb-3">Методика с расчетом среднего значения по 2 наиб.</h3>

    <!--name="method_type" наименавание для всех типов метода одинаково-->
    <!--<input type="hidden" id="methodType" name="method_type" value="TU_sred2">-->
    <input type="hidden" id="methodType" name="type" value="sred">

    <div class="form-group row">
        <div class="col-auto">
			<input type="hidden" value="0" name="form_data[<?=$this->data['ugtp_id']?>][is_single_values]">
            <input class="form-check-input border-radius-0" type="checkbox" value="1" name="form_data[<?=$this->data['ugtp_id']?>][is_single_values]"
                <?=$this->data['measuring']['is_single_values'] == 1? 'checked' : ''?>>
        </div>
        <label class="col col-form-label ps-0">Выводить единичные значения в протокол?</label>
    </div>

    <div class="mb-3">
        <label for="actualValue" class="form-label mb-1">Фактическое значение</label>
        <div class="td-actual-value">
            <input type="number" class="me-2 mb-2 actual-value w-100 border p-2 bg-white"
                   name="form_data[<?=$this->data['ugtp_id']?>][actual_value][0]" step="any"
                   value="<?= $this->data['measuring']['actual_value'][0] ?>" required>
            <input type="number" class="me-2 mb-2 actual-value w-100 border p-2 bg-white"
                   name="form_data[<?=$this->data['ugtp_id']?>][actual_value][1]" step="any"
                   value="<?= $this->data['measuring']['actual_value'][1] ?>" required>
            <input type="number" class="me-2 actual-value w-100 border p-2 bg-white"
                   name="form_data[<?=$this->data['ugtp_id']?>][actual_value][2]" step="any"
                   value="<?= $this->data['measuring']['actual_value'][2] ?>" required>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="averageValue" class="form-label mb-1">Среднее значение</label>
            <!--name="result_value" наименавание инпута результат которого хотим видеть в результатах испытаний "Фактическое значение" в поле для всех одинаково-->
            <input type="number" class="me-2 w-100 border p-2 bg-white" id="averageValue" name="form_data[<?=$this->data['ugtp_id']?>][result_value]"
                   step="any" value="<?= $this->data['measuring']['result_value'] ?>">
        </div>
        <div class="col">
            <label for="decimalPlaces" class="form-label mb-1">Знаков после запятой</label>
            <input type="number" class="me-2 w-100 border p-2 bg-white" id="decimalPlaces" name="form_data[<?=$this->data['ugtp_id']?>][[decimal_places]"
                   step="1" value="<?= $this->data['measuring_property']['decimal'] ?>">
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary calculate-average me-2">Рассчитать</button>
    <button type="submit" class="btn btn-primary save-average">Сохранить</button>
</div>
