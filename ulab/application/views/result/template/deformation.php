<div class="measurement-wrapper" id="deformationWrapper">
    <h3 class="mb-3">Относительная деформация ГОСТ 8269.0 п.4.22.3</h3>
    <input type="hidden" name="form_data[<?= $this->data['ugtp_id'] ?>][type]" value="deformation">
    <input type="hidden" name="form_data[<?= $this->data['ugtp_id'] ?>][method_id]"
           value="<?= $this->data['measuring_property']['method_id'] ?? '' ?>">
    <input id="ugtpId" type="hidden" name="form_data[<?= $this->data['ugtp_id'] ?>][ugtp_id]"
           value="<?= $this->data['ugtp_id'] ?>">

    <div class="line-dashed-small"></div>

    <!-- Основная таблица с измерениями -->
    <table id="deformationTable" class="table table-bordered">
        <thead>
        <tr>
            <th>№ измерения</th>
            <th>Относительная деформация каждого образца (%)</th>
            <th>Отличие 11-го результата испытаний от 3-х предшествующих (%)</th>
        </tr>
        </thead>
        <tbody id="deformationBody">
        <?php
        // Проверяем, есть ли сохраненные данные
        $savedData = $this->data['measuring']['deformation_values'] ?? [];

        // Создаем 11 строк для измерений
        for ($i = 1; $i <= 11; $i++):
            $savedValue = $savedData[$i]['value'] ?? '';
            $savedDifference = $savedData[$i]['difference'] ?? '';
            $readonly = ($i >= 8 && $i <= 10) ? 'readonly' : ''; // Строки 8-10 с авторасчетом
            ?>
            <tr data-measurement="<?= $i ?>">
                <td><?= $i ?></td>
                <td>
                    <input type="number"
                           name="form_data[<?= $this->data['ugtp_id'] ?>][deformation_values][<?= $i ?>][value]"
                           class="form-control form-control-sm deformation-input"
                           data-measurement="<?= $i ?>"
                           value="<?= htmlspecialchars($savedValue) ?>"
                           step="0.001"
                        <?= $i == 11 ? 'required' : '' ?>>
                </td>
                <td>
                    <input type="text"
                           name="form_data[<?= $this->data['ugtp_id'] ?>][deformation_values][<?= $i ?>][difference]"
                           class="form-control form-control-sm difference-input"
                           data-measurement="<?= $i ?>"
                           value="-"
                        <?= $readonly ?>
                        <?= ($i >= 8 && $i <= 10) ? 'placeholder="Авторасчет"' : '' ?>>
                </td>
            </tr>
        <?php endfor; ?>
        </tbody>
    </table>

    <!-- Блок с заключением (автоматическое, не редактируемое) -->
    <div class="card mb-3" id="conclusionBlock">
        <div class="card-body">
            <div class="form-group">
                <label><strong>Заключение:</strong></label>
                <div id="conclusionText" class="p-3 rounded" style="background-color: #e9ecef; min-height: 60px;">
                    <?php
                    // Выводим сохраненное заключение или плейсхолдер
                    $conclusion = $this->data['measuring']['conclusion'] ?? 'Заключение будет сформировано автоматически после ввода данных';
                    echo nl2br(htmlspecialchars($conclusion));
                    ?>
                </div>
                <input type="hidden" name="form_data[<?= $this->data['ugtp_id'] ?>][com]"
                       id="conclusionHidden" value="<?= htmlspecialchars($conclusion) ?>">
            </div>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary save-deformation">Сохранить</button>
</div>