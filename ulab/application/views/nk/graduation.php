<!--Градуировочная зависимость-->
<div class="wrapper-card m-auto">
    <header class="header-requirement mb-3">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-2">
                    <a class="nav-link" href="<?=URI?>/nk/graduationList/" title="Вернуться к списку">
                        <i class="fa-solid fa-list"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <form id="graduationModalForm" class="bg-light m-auto p-3"
          action="<?= URI ?>/nk/insertUpdateGraduation/" method="post">

        <?php if ( isset($this->data['id']) && !empty($this->data['id']) ): ?>
            <input type="hidden" value="<?=$this->data['id']?>" name="id">
        <?php endif; ?>

        <div class="graduation" id="graduationWrapper">
            <div class="row">
                <div class="form-group col">
                    <label for="object">Объект строительства</label>
                    <input type="text" class="form-control" id="object" name="form[object]"
                           value="<?= htmlspecialchars($this->data['object'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="form-group col">
                    <label for="concreteClass">Класс бетона</label>
                    <div class="input-group">
                        <span class="input-group-text">B</span>
                        <input type="text" class="form-control number-only" id="concreteClass" name="form[concrete_class]"
                               value="<?= $this->data['concrete_class'] ?>" required>
                    </div>
                </div>
                <div class="form-group col">
                    <label for="measuringDevice">Прибор для замеров</label>
                    <select class="form-select bg-white w-100" id="measuringDevice" name="form[measuring_device]" required>
                        <option value="УКС" <?= 'УКС' === $this->data['measuring_device'] ? 'selected' : '' ?>>УКС</option>
                        <option value="ИПС" <?= 'ИПС' === $this->data['measuring_device'] ? 'selected' : '' ?>>ИПС</option>
                    </select>
                </div>
                <div class="form-group col">
                    <label for="method">Метод</label>
                    <select id="method" name="form[method]" required>
                        <optgroup label="Метод отрыва со скалыванием">
                            <option value="separation_0.04" <?= 'separation_0.04' === $this->data['method'] ? 'selected' : '' ?>>Глубина 48 мм</option>
                            <option value="separation_0.05" <?= 'separation_0.05' === $this->data['method'] ? 'selected' : '' ?>>Глубина 35 мм</option>
                            <option value="separation_0.06" <?= 'separation_0.06' === $this->data['method'] ? 'selected' : '' ?>>Глубина 30 мм</option>
                        </optgroup>
                        <optgroup label="Метод скалывания ребра">
                            <option value="chipping_0.04" <?= 'chipping_0.04' === $this->data['method'] ? 'selected' : '' ?>>Скалывание ребра</option>
                        </optgroup>
                        <optgroup label="Разрушающий метод">
                            <option value="destructive_0.02" <?= 'destructive_0.02' === $this->data['method'] ? 'selected' : '' ?>>Разрушающий</option>
                        </optgroup>
                    </select>
                </div>
                <div class="form-group col">
                    <label for="date">Дата проведения испытаний</label>
                    <input type="date" class="form-control bg-white w-100" id="date"
                           name="form[date]" value="<?= $this->data['date'] ?>" required>
                </div>
                <div class="form-group col">
                    <label for="dayToTest">Срок проведения испытаний</label>
                    <div class="input-group">
                        <input type="text" class="form-control number-only" id="dayToTest" name="form[day_to_test]"
                               value="<?= $this->data['day_to_test'] ?>" required>
                        <span class="input-group-text">суток</span>
                    </div>
                </div>
            </div>

            <div class="calculations-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Расчет для конструкций на объекте строительства класса бетона по прочности на сжатие
                                <span class="tools float-end">
                                <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                                <a href="javascript:;" class="fa fa-chevron-up"></a>
                            </span>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col">
                                        <table class="table text-center align-middle table-bordered">
                                            <thead>
                                            <tr class="align-middle">
                                                <th rowspan="3">Наименование, место расположение и дата бетонирования конструкции</th>
                                                <th colspan="4">Показания СИ</th>
                                                <th rowspan="3">Прочность бетона по градуировочной зависимости, МПа</th>
                                                <th rowspan="3">Условие отбраковки единичных результатов испытаний |RiH-Riф|/S</th>
                                                <th rowspan="3">+/-</th>
                                            </tr>
                                            <tr class="align-middle">
                                                <th colspan="3">Прочность бетона, определенная ударным импульсом (ИПС/УКС), Мпа ( м/с -если УКС)</th>
                                                <th rowspan="2">Прочность бетона на участке методом отрыва со скалыванием, МПа</th>
                                            </tr>
                                            <tr class="align-middle">
                                                <th colspan="2">Единичные значения</th>
                                                <th>Среднее значение на участке</th>
                                            </tr>
                                            </thead>
                                            <tbody class="construction-wrapper">
                                            <tr class="construction-row">
                                                <td>
                                                    <input type="text" class="form-control bg-white" name="form[name][]"
                                                           value="<?= $this->data['measuring']['name'][0] ?>">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control bg-white single-value-1" name="form[single_value_1][]"
                                                           step="any" value="<?= $this->data['measuring']['single_value_1'][0] ?>">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control single-value-2 <?= $this->data['measuring_device'] === 'ИПС' ? 'bg-light-secondary' : 'bg-white' ?>"
                                                           name="form[single_value_2][]" step="any"
                                                           value="<?= $this->data['measuring']['single_value_2'][0] ?>"
                                                        <?= $this->data['measuring_device'] === 'ИПС' ? 'disabled' : '' ?>>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control bg-light-secondary mean" name="form[mean][]"
                                                           step="any" value="<?= $this->data['measuring']['mean'][0] ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control bg-white shear-strength" name="form[shear_strength][]"
                                                           step="any" value="<?= $this->data['measuring']['shear_strength'][0] ?>">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control bg-light-secondary gradation-strength" name="form[gradation_strength][]"
                                                           step="any" value="<?= $this->data['measuring']['gradation_strength'][0] ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control condition <?= $this->data['measuring']['condition'][0] > 2 ? 'bg-danger' : 'bg-light-secondary' ?>" name="form[condition][]"
                                                           step="any" value="<?= $this->data['measuring']['condition'][0] ?>" readonly>
                                                </td>
                                                <td>
                                                    <button class="btn mt-0 btn-square add-construction btn-primary" type="button">
                                                        <i class="fa-solid fa-plus icon-fix"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php if ( isset($this->data['measuring']['mean']) && count($this->data['measuring']['mean']) > 1 ): ?>
                                                <?php for ($i = 1; $i < count($this->data['measuring']['mean']); $i++): ?>
                                                    <tr class="construction-row">
                                                        <td>
                                                            <input type="text" class="form-control bg-white" name="form[name][]"
                                                                   value="<?= $this->data['measuring']['name'][$i] ?>">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control single-value-1 bg-white"
                                                                   name="form[single_value_1][]" step="any"
                                                                   value="<?= $this->data['measuring']['single_value_1'][$i] ?>">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control single-value-2 <?= $this->data['measuring_device'] === 'ИПС' ? 'bg-light-secondary' : 'bg-white' ?>"
                                                                   name="form[single_value_2][]" step="any"
                                                                   value="<?= $this->data['measuring']['single_value_2'][$i] ?>"
                                                                <?= $this->data['measuring_device'] === 'ИПС' ? 'disabled' : '' ?>>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control bg-light-secondary mean" name="form[mean][]"
                                                                   step="any" value="<?= $this->data['measuring']['mean'][$i] ?>" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control bg-white shear-strength" name="form[shear_strength][]"
                                                                   step="any" value="<?= $this->data['measuring']['shear_strength'][$i] ?>">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control bg-light-secondary gradation-strength" name="form[gradation_strength][]"
                                                                   step="any" value="<?= $this->data['measuring']['gradation_strength'][$i] ?>" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control condition <?= $this->data['measuring']['condition'][$i] > 2 ? 'bg-danger' : 'bg-light-secondary' ?>" name="form[condition][]"
                                                                   step="any" value="<?= $this->data['measuring']['condition'][$i] ?>" readonly>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-square del-construction mt-0 btn-danger">
                                                                <i class="fa-solid fa-minus icon-fix"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <table class="table text-center align-middle table-bordered">
                                            <thead>
                                            <tr class="align-middle">
                                                <th colspan="2">Коэффициенты построенной градуировочной зависимости</th>
                                                <th colspan="2">Среднее значение показателя конструкции</th>
                                                <th rowspan="2">Остаточное среднеквадратическое отклонение S (Sт.м.н.), МПа</th>
                                                <th rowspan="2">Rн</th>
                                            </tr>
                                            <tr class="align-middle">
                                                <th>a</th>
                                                <th>b</th>
                                                <th>х, МПа</th>
                                                <th>Rо, МПа</th>
                                            </tr>
                                            </thead>
                                            <tbody class="construction-wrapper">
                                            <tr class="construction-row">
                                                <td>
                                                    <input type="number" class="form-control bg-light-secondary round-a"
                                                           step="any" value="<?= $this->data['measuring']['round_a'] ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control bg-light-secondary" id="round-b" name="form[round_b]"
                                                           step="any" value="<?= $this->data['measuring']['round_b'] ?>" readonly>
                                                    <input type="hidden" class="form-control bg-light-secondary" id="b" name="form[b]"
                                                           step="any" value="<?= $this->data['measuring']['b'] ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control bg-light-secondary" id="x" name="form[x]"
                                                           step="any" value="<?= $this->data['measuring']['x'] ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control bg-light-secondary" id="Ro" name="form[Ro]"
                                                           step="any" value="<?= $this->data['measuring']['Ro'] ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control bg-light-secondary S"
                                                           step="any" value="<?= $this->data['measuring']['S'] ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control bg-light-secondary" id="Rn"
                                                           step="any" value="<?= $this->data['measuring']['Rn_r'] ?>" readonly>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <table class="table text-center align-middle table-bordered">
                                            <thead>
                                            <tr class="align-middle">
                                                <th rowspan="2">Среднеквадратическое отклонение градуировочной зависимости, S/Rср</th>
                                                <th rowspan="2">Коэффициент корреляции, r</th>
                                                <th colspan="2">Требования условий применения градуировочной зависимости и соответствие им построенной градуировочной зависимости</th>
                                            </tr>
                                            <tr class="align-middle">
                                                <th>S/Rср<0,15</th>
                                                <th>r>0,7</th>
                                            </tr>
                                            </thead>
                                            <tbody class="construction-wrapper">
                                            <tr class="construction-row">
                                                <td>
                                                    <input type="number" class="form-control bg-light-secondary" id="SR" name="form[SR]"
                                                           step="any" value="<?= $this->data['measuring']['SR'] ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control bg-light-secondary r"
                                                           step="any" value="<?= $this->data['measuring']['r'] ?>" readonly>
                                                </td>
                                                <td>
                                                    <select class="form-select bg-light-secondary w-100 pointer-events-none reset-select" id="SR015" name="form[SR015]" readonly>
                                                        <option value=""></option>
                                                        <option value="Соответствует" <?= 'Соответствует' === $this->data['measuring']['SR015'] ? 'selected' : '' ?>>Соответствует</option>
                                                        <option value="Не соответствует" <?= 'Не соответствует' === $this->data['measuring']['SR015'] ? 'selected' : '' ?>>Не соответствует</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select bg-light-secondary w-100 pointer-events-none reset-select" id="r07" name="form[r07]" readonly>
                                                        <option value=""></option>
                                                        <option value="Соответствует" <?= 'Соответствует' === $this->data['measuring']['r07'] ? 'selected' : '' ?>>Соответствует</option>
                                                        <option value="Не соответствует" <?= 'Не соответствует' === $this->data['measuring']['r07'] ? 'selected' : '' ?>>Не соответствует</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!--./panel-body-->
                        </div>
                    </div>
                </div>
                <!--./Расчет для конструкций на объекте строительства класса бетона по прочности на сжатие-->

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Расчет коэффициента а
                                <span class="tools float-end">
                                <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                                <a href="javascript:;" class="fa fa-chevron-down"></a>
                            </span>
                            </div>
                            <div class="panel-body panel-hidden">
                                <div class="wrapper-a">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <em>Расчетная часть ниже работает автономно, в этих таблицах ЗАПРЕЩЕНО что либо менять/вводить/править.
                                                Таблицы все проверены и работают корректно, любые изменения повлекут за собой ошибку в расчетах и нарушение работы основной таблицы (слева) с градуировочной зависимостью.</em>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col wrapper-Ri-Rf">
                                            <table class="table text-center align-middle table-bordered">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th>Ri</th>
                                                    <th>Rф</th>
                                                    <th>Разность Ri-Rф</th>
                                                </tr>
                                                </thead>
                                                <tbody class="construction-wrapper">
                                                <tr class="construction-row">
                                                    <td>
                                                        <input type="text" class="form-control bg-white border-0 shadow-none Ri"
                                                               value="<?= $this->data['measuring']['shear_strength'][0] ?>" disabled>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary Rf" name="form[Rf][]"
                                                               step="any" value="<?= $this->data['measuring']['Rf'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary Ri-Rf" name="form[Ri_Rf][]"
                                                               step="any" value="<?= $this->data['measuring']['Ri_Rf'][0] ?>" readonly>
                                                    </td>
                                                </tr>
                                                <?php if ( isset($this->data['measuring']['shear_strength']) && count($this->data['measuring']['shear_strength']) > 1 ): ?>
                                                    <?php for ($i = 1; $i < count($this->data['measuring']['shear_strength']); $i++): ?>
                                                        <tr class="construction-row">
                                                            <td>
                                                                <input type="text" class="form-control bg-white border-0 shadow-none Ri"
                                                                       value="<?= $this->data['measuring']['shear_strength'][$i] ?>" disabled>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary Rf" name="form[Rf][]"
                                                                       step="any" value="<?= $this->data['measuring']['Rf'][$i] ?>" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary Ri-Rf" name="form[Ri_Rf][]"
                                                                       step="any" value="<?= $this->data['measuring']['Ri_Rf'][$i] ?>" readonly>
                                                            </td>
                                                        </tr>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col wrapper-Hi-Hf">
                                            <table class="table text-center align-middle table-bordered">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th>Hi</th>
                                                    <th>Hф</th>
                                                    <th>Разность Hi-Hф</th>
                                                </tr>
                                                </thead>
                                                <tbody class="construction-wrapper">
                                                <tr class="construction-row">
                                                    <td>
                                                        <input type="text" class="form-control bg-white border-0 shadow-none Hi"
                                                               value="<?= $this->data['measuring']['mean'][0] ?>" disabled>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary Hf" name="form[Hf][]"
                                                               step="any" value="<?= $this->data['measuring']['Hf'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary Hi-Hf" name="form[Hi_Hf][]"
                                                               step="any" value="<?= $this->data['measuring']['Hi_Hf'][0] ?>" readonly>
                                                    </td>
                                                </tr>
                                                <?php if ( isset($this->data['measuring']['mean']) && count($this->data['measuring']['mean']) > 1 ): ?>
                                                    <?php for ($i = 1; $i < count($this->data['measuring']['mean']); $i++): ?>
                                                        <tr class="construction-row">
                                                            <td>
                                                                <input type="text" class="form-control bg-white border-0 shadow-none Hi"
                                                                       value="<?= $this->data['measuring']['mean'][$i] ?>" disabled>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary Hf" name="form[Hf][]"
                                                                       step="any" value="<?= $this->data['measuring']['Hf'][$i] ?>" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary Hi-Hf" name="form[Hi_Hf][]"
                                                                       step="any" value="<?= $this->data['measuring']['Hi_Hf'][$i] ?>" readonly>
                                                            </td>
                                                        </tr>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col wrapper-RiRfHiHf">
                                            <table class="table text-center align-middle table-bordered">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th rowspan="2">произв. разностей</th>
                                                    <th rowspan="2">сумма</th>
                                                </tr>
                                                </thead>
                                                <tbody class="construction-wrapper">
                                                <tr class="construction-row">
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary RiRfHiHf" name="form[RiRfHiHf][]"
                                                               step="any" value="<?= $this->data['measuring']['RiRfHiHf'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary sumRiRfHiHf" name="form[sum_RiRfHiHf]"
                                                               step="any" value="<?= $this->data['measuring']['sum_RiRfHiHf'] ?>" readonly>
                                                    </td>
                                                </tr>
                                                <?php if ( isset($this->data['measuring']['shear_strength']) && count($this->data['measuring']['shear_strength']) > 1 ): ?>
                                                    <?php for ($i = 1; $i < count($this->data['measuring']['shear_strength']); $i++): ?>
                                                        <tr class="construction-row">
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary RiRfHiHf" name="form[RiRfHiHf][]"
                                                                       step="any" value="<?= $this->data['measuring']['RiRfHiHf'][$i] ?>" readonly>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col wrapper-HiHf2">
                                            <table class="table text-center align-middle table-bordered">
                                                <caption class="text-center">Знаменатель</caption>
                                                <thead>
                                                <tr class="align-middle">
                                                    <th>(Hi-Hф)^2</th>
                                                    <th>сумма</th>
                                                </tr>
                                                </thead>
                                                <tbody class="construction-wrapper">
                                                <tr class="construction-row">
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary HiHf2" name="form[HiHf2][]"
                                                               step="any" value="<?= $this->data['measuring']['HiHf2'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary sumHiHf2" name="form[sum_HiHf2]"
                                                               step="any" value="<?= $this->data['measuring']['sum_HiHf2'] ?>" readonly>
                                                    </td>
                                                </tr>
                                                <?php if ( isset($this->data['measuring']['mean']) && count($this->data['measuring']['mean']) > 1 ): ?>
                                                    <?php for ($i = 1; $i < count($this->data['measuring']['mean']); $i++): ?>
                                                        <tr class="construction-row">
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary HiHf2" name="form[HiHf2][]"
                                                                       step="any" value="<?= $this->data['measuring']['HiHf2'][$i] ?>" readonly>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col wrapper-RH">
                                            <table class="table text-center align-middle table-bordered">
                                                <caption class="text-center">Числитель</caption>
                                                <thead>
                                                <tr class="align-middle">
                                                    <th>R*H</th>
                                                    <th>сумма</th>
                                                </tr>
                                                </thead>
                                                <tbody class="construction-wrapper">
                                                <tr class="construction-row">
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary RH" name="form[RH][]"
                                                               step="any" value="<?= $this->data['measuring']['RH'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary sumRH" name="form[sum_RH]"
                                                               step="any" value="<?= $this->data['measuring']['sum_RH'] ?>" readonly>
                                                    </td>
                                                </tr>
                                                <?php if ( isset($this->data['measuring']['shear_strength']) && count($this->data['measuring']['shear_strength']) > 1 ): ?>
                                                    <?php for ($i = 1; $i < count($this->data['measuring']['shear_strength']); $i++): ?>
                                                        <tr class="construction-row">
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary RH" name="form[RH][]"
                                                                       step="any" value="<?= $this->data['measuring']['RH'][$i] ?>" readonly>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col">
                                            <table class="table text-center align-middle table-bordered">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th>a</th>
                                                </tr>
                                                </thead>
                                                <tbody class="construction-wrapper">
                                                <tr class="construction-row">
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary round-a" name="form[round_a]"
                                                               step="any" value="<?= $this->data['measuring']['round_a'] ?>" readonly>
                                                        <input type="hidden" class="form-control bg-light-secondary" id="a" name="form[a]"
                                                               step="any" value="<?= $this->data['measuring']['a'] ?>" readonly>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./wrapper-a-->
                            </div>
                            <!--./panel-body-->
                        </div>
                    </div>
                </div>
                <!--./Расчет коэффициента а-->

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Расчет коэффицента S (Sт.м.н.)
                                <span class="tools float-end">
                                <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                                <a href="javascript:;" class="fa fa-chevron-down"></a>
                            </span>
                            </div>
                            <div class="panel-body panel-hidden">
                                <div class="wrapper-S">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <em>Расчетная часть ниже работает автономно, в этих таблицах ЗАПРЕЩЕНО что либо менять/вводить/править.
                                                Таблицы все проверены и работают корректно, любые изменения повлекут за собой ошибку в расчетах и нарушение работы основной таблицы (слева) с градуировочной зависимостью.</em>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-8 wrapper-sum-sqr">
                                            <table class="table text-center align-middle table-bordered">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th>Проч-ь отрывы</th>
                                                    <th>Проч-ь градуировка</th>
                                                    <th>Разность</th>
                                                    <th>Квадрат разности</th>
                                                    <th>Сумма квадратов</th>
                                                </tr>
                                                </thead>
                                                <tbody class="construction-wrapper">
                                                <tr class="construction-row">
                                                    <td>
                                                        <input type="text" class="form-control bg-white border-0 shadow-none shear-strength-S"
                                                               value="<?= $this->data['measuring']['shear_strength'][0] ?>" disabled>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white border-0 shadow-none gradation-strength-S"
                                                               step="any" value="<?= $this->data['measuring']['gradation_strength'][0] ?>" disabled>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary difference-S" name="form[difference_S][]"
                                                               step="any" value="<?= $this->data['measuring']['difference_S'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary sqr-difference-S" name="form[sqr_difference_S][]"
                                                               step="any" value="<?= $this->data['measuring']['sqr_difference_S'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary" id="sumSqrS" name="form[sum_sqr_S][]"
                                                               step="any" value="<?= $this->data['measuring']['sum_sqr_S'][0] ?>" readonly>
                                                    </td>
                                                </tr>
                                                <?php if ( isset($this->data['measuring']['mean']) && count($this->data['measuring']['mean']) > 1 ): ?>
                                                    <?php for ($i = 1; $i < count($this->data['measuring']['mean']); $i++): ?>
                                                        <tr class="construction-row">
                                                            <td>
                                                                <input type="text" class="form-control bg-white border-0 shadow-none shear-strength-S"
                                                                       value="<?= $this->data['measuring']['shear_strength'][$i] ?>" disabled>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control bg-white border-0 shadow-none gradation-strength-S"
                                                                       step="any" value="<?= $this->data['measuring']['gradation_strength'][$i] ?>" disabled>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary difference-S" name="form[difference_S][]"
                                                                       step="any" value="<?= $this->data['measuring']['difference_S'][$i] ?>" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary sqr-difference-S" name="form[sqr_difference_S][]"
                                                                       step="any" value="<?= $this->data['measuring']['sqr_difference_S'][$i] ?>" readonly>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-4">
                                            <table class="table text-center align-middle table-bordered">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th>S</th>
                                                </tr>
                                                </thead>
                                                <tbody class="construction-wrapper">
                                                <tr class="construction-row">
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary" id="S" name="form[S]"
                                                               step="any" value="<?= $this->data['measuring']['S'] ?>" readonly>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./wrapper-S-->
                            </div>
                            <!--./panel-body-->
                        </div>
                    </div>
                </div>
                <!--./Расчет коэффицента S (Sт.м.н.)-->

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Расчет коэффицента r
                                <span class="tools float-end">
                                <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                                <a href="javascript:;" class="fa fa-chevron-down"></a>
                            </span>
                            </div>
                            <div class="panel-body panel-hidden">
                                <div class="wrapper-r">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <em>Расчетная часть ниже работает автономно, в этих таблицах ЗАПРЕЩЕНО что либо менять/вводить/править.
                                                Таблицы все проверены и работают корректно, любые изменения повлекут за собой ошибку в расчетах и нарушение работы основной таблицы (слева) с градуировочной зависимостью.</em>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col wrapper-numerator-r">
                                            <table class="table text-center align-middle table-bordered">
                                                <caption class="text-center caption-top">Числитель</caption>
                                                <thead>
                                                <tr class="align-middle">
                                                    <th>Riн</th>
                                                    <th>Rн</th>
                                                    <th>Riн-Rн</th>
                                                    <th>Riф</th>
                                                    <th>Rф</th>
                                                    <th>Riф-Rф</th>
                                                    <th>Произв.</th>
                                                    <th>Числитель (сумма)</th>
                                                </tr>
                                                </thead>
                                                <tbody class="construction-wrapper">
                                                <tr class="construction-row">
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary Rin-r" name="form[Rin_r][]"
                                                               value="<?= $this->data['measuring']['Rin_r'][0] ?>" step="any" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary Rn-r" id="Rnr" name="form[Rn_r]"
                                                               step="any" value="<?= $this->data['measuring']['Rn_r'] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary RinRn-r" name="form[RinRn_r][]"
                                                               step="any" value="<?= $this->data['measuring']['RinRn_r'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white border-0 shadow-none Rif-r"
                                                               step="any" value="<?= $this->data['measuring']['shear_strength'][0] ?>" disabled>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white border-0 shadow-none" id="Rfr"
                                                               step="any" value="<?= $this->data['measuring']['Ro'] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary RifRf-r" name="form[RifRf_r][]"
                                                               step="any" value="<?= $this->data['measuring']['RifRf_r'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary RinRnRifRf-r" name="form[RinRnRifRf_r][]"
                                                               step="any" value="<?= $this->data['measuring']['RinRnRifRf_r'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary" id="sumRinRnRifRfr" name="form[sum_RinRnRifRf_r][]"
                                                               step="any" value="<?= $this->data['measuring']['sum_RinRnRifRf_r'][0] ?>" readonly>
                                                    </td>
                                                </tr>
                                                <?php if ( isset($this->data['measuring']['shear_strength']) && count($this->data['measuring']['shear_strength']) > 1 ): ?>
                                                    <?php for ($i = 1; $i < count($this->data['measuring']['shear_strength']); $i++): ?>
                                                        <tr class="construction-row">
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary Rin-r" name="form[Rin_r][]"
                                                                       value="<?= $this->data['measuring']['Rin_r'][$i] ?>" step="any" readonly>
                                                            </td>
                                                            <td></td>
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary RinRn-r" name="form[RinRn_r][]"
                                                                       step="any" value="<?= $this->data['measuring']['RinRn_r'][$i] ?>" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control bg-white border-0 shadow-none Rif-r"
                                                                       step="any" value="<?= $this->data['measuring']['shear_strength'][$i] ?>" disabled>
                                                            </td>
                                                            <td></td>
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary RifRf-r" name="form[RifRf_r][]"
                                                                       step="any" value="<?= $this->data['measuring']['RifRf_r'][$i] ?>" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary RinRnRifRf-r" name="form[RinRnRifRf_r][]"
                                                                       step="any" value="<?= $this->data['measuring']['RinRnRifRf_r'][$i] ?>" readonly>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col wrapper-denominator-r">
                                            <table class="table text-center align-middle table-bordered">
                                                <caption class="text-center caption-top">Знаменатель</caption>
                                                <thead>
                                                <tr class="align-middle">
                                                    <th>Riн-Rн</th>
                                                    <th>(Riн-Rн)^2</th>
                                                    <th>Сумма (Riн-Rн)^2</th>
                                                    <th>Корень Суммы (Riн-Rн)^2</th>
                                                    <th>Riф-Rф</th>
                                                    <th>(Riф-Rф)^2</th>
                                                    <th>Сумма (Riф-Rф)^2</th>
                                                    <th>Корень Суммы (Riф-Rф)^2</th>
                                                    <th>Знаменатель</th>
                                                </tr>
                                                </thead>
                                                <tbody class="construction-wrapper">
                                                <tr class="construction-row">
                                                    <td>
                                                        <input type="number" class="form-control bg-white border-0 shadow-none RinRn"
                                                               value="<?= $this->data['measuring']['RinRn_r'][0] ?>" step="any" disabled>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary RinRn2" name="form[RinRn2][]"
                                                               step="any" value="<?= $this->data['measuring']['RinRn2'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary" id="sumRinRn2" name="form[sum_RinRn2]"
                                                               step="any" value="<?= $this->data['measuring']['sum_RinRn2'] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary" id="sqrSumRinRn2" name="form[sqr_SumRinRn2]"
                                                               step="any" value="<?= $this->data['measuring']['sqr_SumRinRn2'] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white border-0 shadow-none RifRf"
                                                               step="any" value="<?= $this->data['measuring']['RifRf_r'][0] ?>" disabled>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary RifRf2" name="form[RifRf2][]"
                                                               step="any" value="<?= $this->data['measuring']['RifRf2'][0] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary" id="sumRifRf2" name="form[sum_RifRf2]"
                                                               step="any" value="<?= $this->data['measuring']['sum_RifRf2'] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary" id="sqrSumRifRf2" name="form[sqr_sumRifRf2]"
                                                               step="any" value="<?= $this->data['measuring']['sqr_sumRifRf2'] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-light-secondary" id="sqrSumRinRn2RifRf2" name="form[sqrSumRinRn2RifRf2]"
                                                               step="any" value="<?= $this->data['measuring']['sqrSumRinRn2RifRf2'] ?>" readonly>
                                                    </td>
                                                </tr>
                                                <?php if ( isset($this->data['measuring']['shear_strength']) && count($this->data['measuring']['shear_strength']) > 1 ): ?>
                                                    <?php for ($i = 1; $i < count($this->data['measuring']['shear_strength']); $i++): ?>
                                                        <tr class="construction-row">
                                                            <td>
                                                                <input type="number" class="form-control bg-white border-0 shadow-none RinRn"
                                                                       value="<?= $this->data['measuring']['RinRn_r'][$i] ?>" step="any" disabled>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary RinRn2" name="form[RinRn2][]"
                                                                       step="any" value="<?= $this->data['measuring']['RinRn2'][$i] ?>" readonly>
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>
                                                                <input type="number" class="form-control bg-white border-0 shadow-none RifRf"
                                                                       step="any" value="<?= $this->data['measuring']['RifRf_r'][$i] ?>" disabled>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control bg-light-secondary RifRf2" name="form[RifRf2][]"
                                                                       step="any" value="<?= $this->data['measuring']['RifRf2'][$i] ?>" readonly>
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <table class="table text-center align-middle table-bordered">
                                            <thead>
                                            <tr class="align-middle">
                                                <th>r</th>
                                            </tr>
                                            </thead>
                                            <tbody class="construction-wrapper">
                                            <tr class="construction-row">
                                                <td>
                                                    <input type="number" class="form-control bg-light-secondary" id="r" name="form[r]"
                                                           step="any" value="<?= $this->data['measuring']['r'] ?>" readonly>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--./wrapper-S-->
                            </div>
                            <!--./panel-body-->
                        </div>
                    </div>
                </div>
                <!--./Расчет коэффицента r-->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Примечание
                            <span class="tools float-end">
                                <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                                <a href="javascript:;" class="fa fa-chevron-down"></a>
                            </span>
                        </div>
                        <div class="panel-body panel-hidden">
                            <div class="wrapper-comment">
                                <div class="row mb-3">
                                    <div class="col">
                                        <lable for="comment">Данные расчетов параметров необходимых для построения градуировочной зависимости:</lable>
                                        <textarea class="form-control mw-100"
                                                  name="form[comment]" style="height: 230px;"><?= $this->data['measuring']['comment'] ?></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <lable for="comment">Примечание</lable>
                                        <textarea class="form-control mw-100"
                                                  name="form[note]" style="height: 80px;"><?= $this->data['measuring']['note'] ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <!--./wrapper-comment-->
                        </div>
                        <!--./panel-body-->
                    </div>
                </div>
            </div>
            <!--./comment-->
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        График
                    </div>
                    <div class="panel-body">
                        <div class="row mb-3">
                            <div class="col">
                                <em>Для построения графика, заполните и рассчитайте данные объекта испытаний</em>
                            </div>
                        </div>
                        <div class="wrapper-chart">
                            <div class="row">
                                <div class="col-8">
                                    <canvas id="myChart"></canvas>
                                    <input type="text" name="chart" hidden="hidden" value="">
                                </div>

                                <div class="col-4 border py-2">
                                    <strong class="d-block text-center">Изменить параметры</strong>
                                    <div class="row">
                                        <div class="form-group col">
                                            <label for="pointRadius">Размер точек</label>
                                            <input type="number" id="pointRadius" class="form-control bg-white w-100"
                                                   name="form[point_radius]" value="<?= $this->data['measuring']['point_radius'] ?? '4' ?>">
                                        </div>
                                    </div>

                                    <strong>Параметры оси Y</strong>
                                    <div class="row">
                                        <div class="form-group col">
                                            <label for="minAxisY">Минимум</label>
                                            <input type="number" id="minAxisY" class="form-control bg-white w-100" step="any"
                                                   name="form[y_min]" value="<?= $this->data['measuring']['y_min'] ?? '' ?>">
                                        </div>
                                        <div class="form-group col">
                                            <label for="maxAxisY">Максимум</label>
                                            <input type="number" id="maxAxisY" class="form-control bg-white w-100" step="any"
                                                   name="form[y_max]" value="<?= $this->data['measuring']['y_max'] ?? '' ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col">
                                            <label for="yMain">Единицы измерения(шаг оси Y)</label>
                                            <input type="number" id="yMain" class="form-control bg-white w-100" step="any"
                                                   name="form[y_main]" value="<?= $this->data['measuring']['y_main'] ?? '' ?>">
                                        </div>
                                    </div>

                                    <strong>Параметры оси X</strong>
                                    <div class="row">
                                        <div class="form-group col">
                                            <label for="minAxisX">Минимум</label>
                                            <input type="number" id="minAxisX" class="form-control bg-white w-100" step="any"
                                                   name="form[x_min]" value="<?= $this->data['measuring']['x_min'] ?? '' ?>">
                                        </div>
                                        <div class="form-group col">
                                            <label for="maxAxisX">Максимум</label>
                                            <input type="number" id="maxAxisX" class="form-control bg-white w-100" step="any"
                                                   name="form[x_max]" value="<?= $this->data['measuring']['x_max'] ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col">
                                            <label for="xMain">Единицы измерения(шаг оси X)</label>
                                            <input type="number" id="xMain" class="form-control bg-white w-100"
                                                   name="form[x_main]" value="<?= $this->data['measuring']['x_main'] ?? '' ?>">
                                        </div>
                                    </div>

                                    <div class="row mt-auto d-none">
                                        <div class="col-auto pe-0">
                                            <button type="button" class="btn btn-primary apply me-2">Применить</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <lable for="comment">Описание градуировочной зависимости</lable>
                                    <textarea class="form-control mw-100"
                                              name="form[description]" style="height: 230px;"><?= $this->data['measuring']['description'] ?></textarea>
                                </div>
                            </div>
                        </div>
                        <!--./wrapper-chart-->
                    </div>
                    <!--./panel-body-->
                </div>
            </div>
        </div>
        <!--./chart-->

        <div class="line-dashed-small"></div>

        <div class="row">
            <div class="col-auto pe-0">
                <button type="button" class="btn btn-primary calculate me-2">Рассчитать</button>
            </div>
            <div class="col-auto ps-0">
                <button type="submit" class="btn btn-primary save">Сохранить</button>
            </div>
        </div>
    </form>
</div>