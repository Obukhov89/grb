<!--Прочность на сжатие ГОСТ 17624 п. 7 схема "В"-->
<div class="measurement-wrapper" id="concreteStrength">
    <div class="actual_class_18105_8_4_1">
        <h2 class="d-block mb-2"><?= $this->data['sheet']['name_ru'] ?></h2>

        <em class="d-block mb-3">
            Для выбора данных расчёта откройте лист измерения необходимой методике, расчитайте и сохраните данные.
            Далее выберите данные расчтёта текущего объекта испытания которые хотите применить к текущей методике "<?=$this->data['sheet']['name_ru']?>"
        </em>

        <input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][sheet_name_ru]"
               value="<?= $this->data['sheet']['name_ru'] ?>" readonly>
        <input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][type]" value="actual_class_18105_8_4_1">

        <div class="row mb-3">
            <div class="form-group col">
                <label for="designCalculation">Данные расчёта обьекта испытаний</label>
                <select class="form-select w-100 do-not-clean" id="designCalculation" name="form_data[<?=$this->data['ugtp_id']?>][design_calculation]">
                    <?php if (empty($this->data['measuring_property']['methods'])): ?>
                        <option value="" disabled>Отсутсвуют данные расчёта текущего объекта испытаний</option>
                    <?php endif; ?>
                    <?php if (!empty($this->data['measuring_property']['methods'])): ?>
                        <option value="" disabled selected>Выберите данные расчёта текущего объекта испытаний</option>
                        <?php foreach ($this->data['measuring_property']['methods'] as $key => $val): ?>
                            <option value="<?= $val['ugtp_id'] ?>"
                                <?= $val['ugtp_id'] === $this->data['measuring']['design_calculation'] ? 'selected' : '' ?>><?= $val['name_for_protocol'] ?> (<?= $val['name'] ?>)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div id="calculationDataWrapper">
        </div>
        <!--./calculationDataWrapper-->
    </div>
</div>
<!--./measurement-wrapper-->