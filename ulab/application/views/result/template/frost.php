<!--Морозостойкость-->
<div class="measurement-wrapper" id="frostWrapper">
    <h2 class="d-block mb-2"><?= $this->data['sheet']['name_ru'] ?></h2>

    <em class="d-block mb-2">Все виды бетонов, кроме бетонов дорожных и аэродромных покрытий и бетонов конструкций,
        эксплуатирующихся в минерализованной воде</em>

    <input type="hidden" name="form_data[<?=$this->data['ugtp_id']?>][type]" value="frost">

    <div class="row mb-3">
        <div class="form-group col">
            <label for="mark">Марка</label>
            <select class="form-select w-100" id="mark" name="form_data[<?=$this->data['ugtp_id']?>][mark]">
                <option value="" selected disabled>Выберите марку</option>
                <?php if (!empty($this->data['sheet']['initial_data'])): ?>
                    <?php foreach ($this->data['sheet']['initial_data'] as $key => $val): ?>
                        <option <?= $key === $this->data['measuring']['mark'] ? 'selected' : '' ?> value="<?= $key ?>"
                                                                                                   data-intermediate="<?= $val['intermediate'] ?>"
                                                                                                   data-control="<?= $val['control'] ?>"><?= $val['mark'] ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>

    <div class="wrapper-shadow mb-4 control-wrapper tests-wrapper-0 <?= $this->data['sheet']['initial_data'][$this->data['measuring']['mark']]['control'] ?: 'd-none' ?>">
        <h4 class="text-muted">Контрольные</h4>

        <div class="head-wrapper-0">
            <div class="table-responsive mb-2">
                <table class="table text-center align-middle table-bordered">
                    <thead>
                    <tr class="align-middle">
                        <th>№ образца</th>
                        <th>Масса образца насыщенного, г</th>
                        <th>+/-</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ( isset($this->data['measuring']['mass_before0']) ): ?>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number0][]" step="any"
                                       value="<?= $this->data['measuring']['number0'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before0" name="form_data[<?=$this->data['ugtp_id']?>][mass_before0][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before0'][0] ?>">
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number0][]" step="any"
                                       value="<?= $this->data['measuring']['number0'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before0" name="form_data[<?=$this->data['ugtp_id']?>][mass_before0][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before0'][1] ?>">
                            </td>
                            <td>
                                <button class="btn btn-primary mt-0 add-control btn-square" type="button">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <?php if (isset($this->data['measuring']['mass_before0']) && count($this->data['measuring']['mass_before0']) > 2): ?>
                            <?php for ($i = 2; $i < count($this->data['measuring']['mass_before0']); $i++): ?>
                                <tr class="added-control">
                                    <td>
                                        <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number0][]" step="any"
                                               value="<?= $this->data['measuring']['number0'][$i] ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control bg-white mass-before0" name="form_data[<?=$this->data['ugtp_id']?>][mass_before0][]"
                                               step="any"
                                               value="<?= $this->data['measuring']['mass_before0'][$i] ?>">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger mt-0 delete-control btn-square" type="button">
                                            <i class="fa-solid fa-minus icon-fix"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number0][]" step="any"
                                       value="<?= $this->data['measuring']['number0'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before0" name="form_data[<?=$this->data['ugtp_id']?>][mass_before0][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before0'][0] ?>">
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number0][]" step="any"
                                       value="<?= $this->data['measuring']['number0'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before0" name="form_data[<?=$this->data['ugtp_id']?>][mass_before0][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before0'][1] ?>">
                            </td>
                            <td>
                                <button class="btn btn-primary mt-0 add-control btn-square" type="button">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-control">
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number0][]" step="any"
                                       value="<?= $this->data['measuring']['number0'][2] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before0" name="form_data[<?=$this->data['ugtp_id']?>][mass_before0][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before0'][2] ?>">
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-control btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-control">
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number0][]" step="any"
                                       value="<?= $this->data['measuring']['number0'][3] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before0" name="form_data[<?=$this->data['ugtp_id']?>][mass_before0][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before0'][3] ?>">
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-control btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-control">
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number0][]" step="any"
                                       value="<?= $this->data['measuring']['number0'][4] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before0" name="form_data[<?=$this->data['ugtp_id']?>][mass_before0][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before0'][4] ?>">
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-control btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-control">
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number0][]" step="any"
                                       value="<?= $this->data['measuring']['number0'][5] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before0" name="form_data[<?=$this->data['ugtp_id']?>][mass_before0][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before0'][5] ?>">
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-control btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <button type="submit" class="btn btn-primary save" <?= $this->data['measuring']['mark'] ? '' : 'disabled' ?>>Сохранить
        </button>
    </div>
    <!--./tests-wrapper-0-->

    <div class="wrapper-shadow mb-4 intermediate-wrapper tests-wrapper-1 <?= $this->data['sheet']['initial_data'][$this->data['measuring']['mark']]['intermediate'] ? '' : 'd-none' ?>">
        <h4 class="text-danger">Промежуточные</h4>

        <div class="form-group col">
            <label for="mark">Циклы</label>
            <input type="text" class="form-control w-100 cycle" id="cycleIntermediate"
                   name="form_data[<?=$this->data['ugtp_id']?>][cycle_intermediate]" step="any" value="<?= $this->data['measuring']['cycle_intermediate'] ?>"
                   readonly>
        </div>

        <div class="head-wrapper-1">
            <div class="table-responsive mb-2">
                <table class="table text-center align-middle table-bordered mb-0">
                    <thead>
                    <tr class="align-middle">
                        <th>№ образца</th>
                        <th>Масса образца насыщенного, г</th>
                        <th>Масса образца после испытаний, г</th>
                        <th>Потеря массы, %</th>
                        <th>+/-</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ( isset($this->data['measuring']['mass_before1']) ): ?>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number1][]" step="any"
                                       value="<?= $this->data['measuring']['number1'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before1" name="form_data[<?=$this->data['ugtp_id']?>][mass_before1][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before1'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after1" name="form_data[<?=$this->data['ugtp_id']?>][mass_after1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after1'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss1" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss1'][0] ?>" readonly>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number1][]" step="any"
                                       value="<?= $this->data['measuring']['number1'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before1" name="form_data[<?=$this->data['ugtp_id']?>][mass_before1][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before1'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after1" name="form_data[<?=$this->data['ugtp_id']?>][mass_after1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after1'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss1" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss1'][1] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-primary mt-0 add-intermediate btn-square" type="button">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <?php if (isset($this->data['measuring']['mass_before1']) && count($this->data['measuring']['mass_before1']) > 2): ?>
                            <?php for ($i = 2; $i < count($this->data['measuring']['mass_before1']); $i++): ?>
                                <tr class="added-intermediate">
                                    <td>
                                        <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number1][]" step="any"
                                               value="<?= $this->data['measuring']['number1'][$i] ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control bg-white mass-before1" name="form_data[<?=$this->data['ugtp_id']?>][mass_before1][]"
                                               step="any"
                                               value="<?= $this->data['measuring']['mass_before1'][$i] ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control bg-white mass-after1" name="form_data[<?=$this->data['ugtp_id']?>][mass_after1][]" step="any"
                                               value="<?= $this->data['measuring']['mass_after1'][$i] ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control mass-loss1" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss1][]" step="any"
                                               value="<?= $this->data['measuring']['mass_loss1'][$i] ?>" readonly>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger mt-0 delete-intermediate btn-square" type="button">
                                            <i class="fa-solid fa-minus icon-fix"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number1][]" step="any"
                                       value="<?= $this->data['measuring']['number1'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before1" name="form_data[<?=$this->data['ugtp_id']?>][mass_before1][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before1'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after1" name="form_data[<?=$this->data['ugtp_id']?>][mass_after1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after1'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss1" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss1'][0] ?>" readonly>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number1][]" step="any"
                                       value="<?= $this->data['measuring']['number1'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before1" name="form_data[<?=$this->data['ugtp_id']?>][mass_before1][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before1'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after1" name="form_data[<?=$this->data['ugtp_id']?>][mass_after1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after1'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss1" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss1'][1] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-primary mt-0 add-intermediate btn-square" type="button">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-intermediate">
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number1][]" step="any"
                                       value="<?= $this->data['measuring']['number1'][2] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before1" name="form_data[<?=$this->data['ugtp_id']?>][mass_before1][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before1'][2] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after1" name="form_data[<?=$this->data['ugtp_id']?>][mass_after1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after1'][2] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss1" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss1'][2] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-intermediate btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-intermediate">
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number1][]" step="any"
                                       value="<?= $this->data['measuring']['number1'][3] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before1" name="form_data[<?=$this->data['ugtp_id']?>][mass_before1][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before1'][3] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after1" name="form_data[<?=$this->data['ugtp_id']?>][mass_after1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after1'][3] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss1" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss1'][3] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-intermediate btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-intermediate">
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number1][]" step="any"
                                       value="<?= $this->data['measuring']['number1'][4] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before1" name="form_data[<?=$this->data['ugtp_id']?>][mass_before1][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before1'][4] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after1" name="form_data[<?=$this->data['ugtp_id']?>][mass_after1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after1'][4] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss1" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss1'][4] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-intermediate btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-intermediate">
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number1][]" step="any"
                                       value="<?= $this->data['measuring']['number1'][5] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before1" name="form_data[<?=$this->data['ugtp_id']?>][mass_before1][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before1'][5] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after1" name="form_data[<?=$this->data['ugtp_id']?>][mass_after1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after1'][5] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss1" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss1][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss1'][5] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-intermediate btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="row mb-3">
                <div class="form-group col">
                    <label for="averageMassLoss1">Среднее значение потери массы, %</label>
                    <input type="number" class="form-control" id="averageMassLoss1" name="form_data[<?=$this->data['ugtp_id']?>][average_mass_loss1]"
                           step="any"
                           value="<?= $this->data['measuring']['average_mass_loss1'] ?>" readonly>
                </div>
            </div>
        </div>

        <div class="table-responsive mb-2 main-wrapper-1">
            <table class="table text-center align-middle table-bordered table-fixed">
                <thead>
                <tr class="align-middle">
                    <th>Объект испытаний (шифр проб/образцов в ИЦ)</th>
                    <th>Метод испытаний, число циклов замораживания и оттаивания</th>
                    <th>Определяемые характеристики контрольных образцов</th>
                    <th>Ед. изм.</th>
                    <th>Результаты испытаний контрольных образцов</th>
                    <th>Определяемые характеристики основных образцов</th>
                    <th>Ед. изм.</th>
                    <th>Результаты испытаний основных образцов</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td rowspan="9">
                        <?= $this->data['measuring_property']['material'] ?? '' ?> <?= $this->data['measuring_property']['cipher'] ?? '' ?>
                    </td>
                    <td rowspan="9">
                        <?= $this->data['measuring_property']['reg_doc'] ?: '' ?> <?= $this->data['measuring_property']['clause'] ?: '' ?>
                    </td>
                    <td>
                        Наличие трещин, сколов, шелушения
                    </td>
                    <td>
                        -
                    </td>
                    <td>
                        <input type="text" class="form-control" name="form_data[<?=$this->data['ugtp_id']?>][control_damage1]"
                               value="<?= $this->data['measuring']['control_damage1'] ?: '-' ?>">
                    </td>
                    <td>
                        Наличие трещин, сколов, шелушения
                    </td>
                    <td>
                        -
                    </td>
                    <td>
                        <input type="text"
                               class="form-control bg-white w-100"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_damage1]"
                               value="<?= $this->data['measuring']['main_damage1'] ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        Среднее уменьшение массы образцов
                    </td>
                    <td>
                        %
                    </td>
                    <td>
                        <input type="text" class="form-control w-100" name="form_data[<?=$this->data['ugtp_id']?>][control_mass1]"
                               value="<?= $this->data['measuring']['control_mass1'] ?: '-' ?>">
                    </td>
                    <td>
                        Среднее уменьшение массы образцов
                    </td>
                    <td>
                        %
                    </td>
                    <td>
                        <input type="number" class="form-control w-100 main-mass1" name="form_data[<?=$this->data['ugtp_id']?>][main_mass1]"
                               value="<?= $this->data['measuring']['main_mass1'] ?>" step="0.01" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Прочность при сжатии насыщенных образцов</td>
                    <td>МПа</td>
                    <td>
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength1][0]" step="any"
                               value="<?= $this->data['measuring']['control_strength1']['0'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength1][1]" step="any"
                               value="<?= $this->data['measuring']['control_strength1']['1'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength1][2]" step="any"
                               value="<?= $this->data['measuring']['control_strength1']['2'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength1][3]" step="any"
                               value="<?= $this->data['measuring']['control_strength1']['3'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength1][4]" step="any"
                               value="<?= $this->data['measuring']['control_strength1']['4'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength1][5]" step="any"
                               value="<?= $this->data['measuring']['control_strength1']['5'] ?>">
                    </td>
                    <td>
                        Прочность при сжатии образцов после испытания
                    </td>
                    <td>
                        МПа
                    </td>
                    <td>
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 main_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength1][0]" step="any"
                               value="<?= $this->data['measuring']['main_strength1']['0'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 main_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength1][1]" step="any"
                               value="<?= $this->data['measuring']['main_strength1']['1'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 main_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength1][2]" step="any"
                               value="<?= $this->data['measuring']['main_strength1']['2'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 main_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength1][3]" step="any"
                               value="<?= $this->data['measuring']['main_strength1']['3'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 main_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength1][4]" step="any"
                               value="<?= $this->data['measuring']['main_strength1']['4'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 main_strength1"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength1][5]" step="any"
                               value="<?= $this->data['measuring']['main_strength1']['5'] ?>">
                    </td>
                </tr>
                <tr>
                    <td>Средняя прочность при сжатии насыщенных образцов</td>
                    <td>МПа</td>
                    <td>
                        <input type="number"
                               class="form-control w-100 control_medium1"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_medium1]" step="any"
                               value="<?= $this->data['measuring']['control_medium1'] ?>" readonly>
                    </td>
                    <td>
                        Средняя прочность при сжатии образцов после испытания
                    </td>
                    <td>
                        МПа
                    </td>
                    <td>
                        <input type="number"
                               class="form-control w-100 main_medium1"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_medium1]" step="any"
                               value="<?= $this->data['measuring']['main_medium1'] ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Наиб. разность (Разность max-min), Wm, МПа</td>
                    <td>МПа</td>
                    <td>
                        <input type="number"
                               class="form-control w-100 control_difference1"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_difference1]" step="any"
                               value="<?= $this->data['measuring']['control_difference1'] ?>" readonly>
                    </td>
                    <td>Наиб. разность (Разность max-min), Wm, МПа</td>
                    <td>МПа</td>
                    <td>
                        <input type="number"
                               class="form-control w-100 main_difference1"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_difference1]" step="any"
                               value="<?= $this->data['measuring']['main_difference1'] ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Среднеквадр откл. σ=наиб.разн/Коэффиц</td>
                    <td>-</td>
                    <td>
                        <input type="number"
                               class="form-control w-100 control_rms1"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_rms1]" step="any"
                               value="<?= $this->data['measuring']['control_rms1'] ?>" readonly>
                    </td>
                    <td>Среднеквадр откл. σ=наиб.разн/Коэффиц</td>
                    <td>-</td>
                    <td>
                        <input type="number"
                               class="form-control w-100 main_rms1"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_rms1]" step="any"
                               value="<?= $this->data['measuring']['main_rms1'] ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Коэффициент вариации</td>
                    <td>-</td>
                    <td>
                        <input type="number"
                               class="form-control w-100 control_variation1"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_variation1]" step="any"
                               value="<?= $this->data['measuring']['control_variation1'] ?>" readonly>
                    </td>
                    <td>Коэффициент вариации</td>
                    <td>-</td>
                    <td>
                        <input type="number"
                               class="form-control w-100 main_variation1"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_variation1]" step="any"
                               value="<?= $this->data['measuring']['main_variation1'] ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Нижняя граница доверительного интервала
                        X<sub>min</sub><sup>I</sup></td>
                    <td>МПа</td>
                    <td>
                        <input type="number"
                               class="form-control w-100 control_bottom_line1"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_bottom_line1]" step="any"
                               value="<?= $this->data['measuring']['control_bottom_line1'] ?>" readonly>
                    </td>
                    <td>
                        Нижняя граница доверительного интервала
                        X<sub>min</sub><sup>II</sup>
                    </td>
                    <td>МПа</td>
                    <td>
                        <input type="number"
                               class="form-control w-100 main_bottom_line1"
                               name="form_data[<?=$this->data['ugtp_id']?>][]" step="any"
                               value="<?= $this->data['measuring']['main_bottom_line1'] ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>0,9X<sub>min</sub><sup>I</sup></td>
                    <td>МПа</td>
                    <td>
                        <input type="number"
                               class="form-control w-100 x_min1_0_9"
                               name="form_data[<?=$this->data['ugtp_id']?>][x_min1_0_9]" step="any"
                               value="<?= $this->data['measuring']['x_min1_0_9'] ?>" readonly>
                    </td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td colspan="8">
                        <div class="form-group">
                            <label for="ratio1" class="mb-2">Примечания: Соотношение
                                X<sub>min</sub><sup>II</sup> ≥ 0,9
                                X<sub>min</sub><sup>I</sup></label>
                            <select class="form-select ratio1 ratio disabled bg-light-gray <?= $this->data['measuring']['ratio1'] === '1' ? 'border-secondary' : 'border-danger'?>"
                                    name="form_data[<?=$this->data['ugtp_id']?>][ratio1]" <?= $this->data['sheet']['initial_data'][$this->data['measuring']['mark']]['intermediate'] ? '' : 'disabled' ?>>
                                <option class="no-corresponds1" value="0"
                                    <?= $this->data['measuring']['ratio1'] === '0' ? 'selected' : '' ?>>
                                    не соблюдается
                                </option>
                                <option class="corresponds1" value="1"
                                    <?= $this->data['measuring']['ratio1'] === '1' ? 'selected' : '' ?>>
                                    соблюдается
                                </option>
                            </select>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <button type="button"
                class="btn btn-primary calculate calculate-intermediate me-2" <?= $this->data['measuring']['mark'] ?: 'disabled' ?>
                data-number="1">Рассчитать
        </button>
        <button type="submit" class="btn btn-primary save" <?= $this->data['measuring']['mark'] ?: 'disabled' ?>>Сохранить
        </button>
    </div>
    <!--./intermediate-wrapper-->

    <div class="wrapper-shadow basic-wrapper tests-wrapper-2 <?= $this->data['sheet']['initial_data'][$this->data['measuring']['mark']]['control'] ?: 'd-none' ?>">
        <h4 class="text-muted">Основные</h4>

        <div class="row mb-3">
            <div class="form-group col">
                <label for="mark">Циклы</label>
                <input type="text" class="form-control w-100 cycle" id="cycleControl"
                       name="form_data[<?=$this->data['ugtp_id']?>][cycle_control]" step="any" value="<?= $this->data['measuring']['cycle_control'] ?>"
                       readonly>
            </div>
        </div>

        <div class="head-wrapper-2">
            <div class="table-responsive mb-2">
                <table class="table text-center align-middle table-bordered">
                    <thead>
                    <tr class="align-middle">
                        <th>№ образца</th>
                        <th>Масса образца насыщенного, г</th>
                        <th>Масса образца после испытаний, г</th>
                        <th>Потеря массы, %</th>
                        <th>+/-</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ( isset($this->data['measuring']['mass_before2']) ): ?>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number2][]" step="any"
                                       value="<?= $this->data['measuring']['number2'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before2" name="form_data[<?=$this->data['ugtp_id']?>][mass_before2][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before2'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after2" name="form_data[<?=$this->data['ugtp_id']?>][mass_after2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after2'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss2" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss2'][0] ?>" readonly>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number2][]" step="any"
                                       value="<?= $this->data['measuring']['number2'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before2" name="form_data[<?=$this->data['ugtp_id']?>][mass_before2][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before2'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after2" name="form_data[<?=$this->data['ugtp_id']?>][mass_after2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after2'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss2" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss2'][1] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-primary mt-0 add-control btn-square" type="button">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <?php if (isset($this->data['measuring']['mass_before2']) && count($this->data['measuring']['mass_before2']) > 2): ?>
                            <?php for ($i = 2; $i < count($this->data['measuring']['mass_before2']); $i++): ?>
                                <tr class="added-control">
                                    <td>
                                        <input type="number" class="form-control bg-white" name="form_data[<?=$this->data['ugtp_id']?>][number2][]" step="any"
                                               value="<?= $this->data['measuring']['number2'][$i] ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control bg-white mass-before2" name="form_data[<?=$this->data['ugtp_id']?>][mass_before2][]"
                                               step="any"
                                               value="<?= $this->data['measuring']['mass_before2'][$i] ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control bg-white mass-after2" name="form_data[<?=$this->data['ugtp_id']?>][mass_after2][]" step="any"
                                               value="<?= $this->data['measuring']['mass_after2'][$i] ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control mass-loss2" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss2][]" step="any"
                                               value="<?= $this->data['measuring']['mass_loss2'][$i] ?>" readonly>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger mt-0 delete-control btn-square" type="button">
                                            <i class="fa-solid fa-minus icon-fix"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white number2" name="form_data[<?=$this->data['ugtp_id']?>][number2][]" step="any"
                                       value="<?= $this->data['measuring']['number2'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before2" name="form_data[<?=$this->data['ugtp_id']?>][mass_before2][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before2'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after2" name="form_data[<?=$this->data['ugtp_id']?>][mass_after2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after2'][0] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss2" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss2'][0] ?>" readonly>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-white number2" name="form_data[<?=$this->data['ugtp_id']?>][number2][]" step="any"
                                       value="<?= $this->data['measuring']['number2'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before2" name="form_data[<?=$this->data['ugtp_id']?>][mass_before2][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before2'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after2" name="form_data[<?=$this->data['ugtp_id']?>][mass_after2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after2'][1] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss2" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss2'][1] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-primary mt-0 add-control btn-square" type="button">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-control">
                            <td>
                                <input type="number" class="form-control bg-white number2" name="form_data[<?=$this->data['ugtp_id']?>][number2][]" step="any"
                                       value="<?= $this->data['measuring']['number2'][2] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before2" name="form_data[<?=$this->data['ugtp_id']?>][mass_before2][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before2'][2] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after2" name="form_data[<?=$this->data['ugtp_id']?>][mass_after2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after2'][2] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss2" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss2'][2] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-control btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-control">
                            <td>
                                <input type="number" class="form-control bg-white number2" name="form_data[<?=$this->data['ugtp_id']?>][number2][]" step="any"
                                       value="<?= $this->data['measuring']['number2'][3] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before2" name="form_data[<?=$this->data['ugtp_id']?>][mass_before2][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before2'][3] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after2" name="form_data[<?=$this->data['ugtp_id']?>][mass_after2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after2'][3] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss2" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss2'][3] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-control btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-control">
                            <td>
                                <input type="number" class="form-control bg-white number2" name="form_data[<?=$this->data['ugtp_id']?>][number2][]" step="any"
                                       value="<?= $this->data['measuring']['number2'][4] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before2" name="form_data[<?=$this->data['ugtp_id']?>][mass_before2][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before2'][4] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after2" name="form_data[<?=$this->data['ugtp_id']?>][mass_after2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after2'][4] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss2" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss2'][4] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-control btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="added-control">
                            <td>
                                <input type="number" class="form-control bg-white number2" name="form_data[<?=$this->data['ugtp_id']?>][number2][]" step="any"
                                       value="<?= $this->data['measuring']['number2'][5] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-before2" name="form_data[<?=$this->data['ugtp_id']?>][mass_before2][]"
                                       step="any"
                                       value="<?= $this->data['measuring']['mass_before2'][5] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mass-after2" name="form_data[<?=$this->data['ugtp_id']?>][mass_after2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_after2'][5] ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control mass-loss2" name="form_data[<?=$this->data['ugtp_id']?>][mass_loss2][]" step="any"
                                       value="<?= $this->data['measuring']['mass_loss2'][5] ?>" readonly>
                            </td>
                            <td>
                                <button class="btn btn-danger mt-0 delete-control btn-square" type="button">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="row mb-3">
                <div class="form-group col">
                    <label for="averageMassLoss2">Среднее значение потери массы, %</label>
                    <input type="number" class="form-control" id="averageMassLoss2" name="form_data[<?=$this->data['ugtp_id']?>][average_mass_loss2]"
                           step="any"
                           value="<?= $this->data['measuring']['average_mass_loss2'] ?>" readonly>
                </div>
            </div>
        </div>

        <div class="table-responsive mb-2 main-wrapper-2">
            <table class="table text-center align-middle table-bordered table-fixed">
                <thead>
                <tr class="align-middle">
                    <th>Объект испытаний (шифр проб/образцов в ИЦ)</th>
                    <th>Метод испытаний, число циклов замораживания и оттаивания</th>
                    <th>Определяемые характеристики контрольных образцов</th>
                    <th>Ед. изм.</th>
                    <th>Результаты испытаний контрольных образцов</th>
                    <th>Определяемые характеристики основных образцов</th>
                    <th>Ед. изм.</th>
                    <th>Результаты испытаний основных образцов</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td rowspan="9">
                        <?= $this->data['measuring_property']['material'] ?? '' ?> <?= $this->data['measuring_property']['cipher'] ?? '' ?>
                    </td>
                    <td rowspan="9">
                        <?= $this->data['measuring_property']['reg_doc'] ?: '' ?> <?= $this->data['measuring_property']['clause'] ?: '' ?>
                    </td>
                    <td>
                        Наличие трещин, сколов, шелушения
                    </td>
                    <td>
                        -
                    </td>
                    <td>
                        <input type="text" class="form-control control_damage2" name="form_data[<?=$this->data['ugtp_id']?>][control_damage2]"
                               value="<?= $this->data['measuring']['control_damage2'] ?: '-' ?>">
                    </td>
                    <td>
                        Наличие трещин, сколов, шелушения
                    </td>
                    <td>
                        -
                    </td>
                    <td>
<!--                        <input type="text"-->
<!--                               class="form-control bg-white w-100 main_damage2"-->
<!--                               name="form_data[--><?php //=$this->data['ugtp_id']?><!--][main_damage2]"-->
<!--                               value="--><?php //= $this->data['measuring']['main_damage2'] ?><!--">-->
                        <select class="form-control bg-white w-100 main_damage2"
                                name="form_data[<?=$this->data['ugtp_id']?>][main_damage2]">
                            <option value="Отсутствуют" <?= $this->data['measuring']['main_damage2'] == 'Отсутствуют' ? 'selected' : '' ?>>Отсутствуют</option>
                            <option value="Наличие трещин" <?= $this->data['measuring']['main_damage2'] == 'Наличие трещин' ? 'selected' : '' ?>>Наличие трещин</option>
                            <option value="Наличие сколов" <?= $this->data['measuring']['main_damage2'] == 'Наличие сколов' ? 'selected' : '' ?>>Наличие сколов</option>
                            <option value="Наличие шелушения" <?= $this->data['measuring']['main_damage2'] == 'Наличие шелушения' ? 'selected' : '' ?>>Наличие шелушения</option>
                            <option value="Наличие трещин и сколов" <?= $this->data['measuring']['main_damage2'] == 'Наличие трещин и сколов' ? 'selected' : '' ?>>Наличие трещин и сколов</option>
                            <option value="Наличие трещин и шелушения" <?= $this->data['measuring']['main_damage2'] == 'Наличие трещин и шелушения' ? 'selected' : '' ?>>Наличие трещин и шелушения</option>
                            <option value="Наличие сколов и шелушения" <?= $this->data['measuring']['main_damage2'] == 'Наличие сколов и шелушения' ? 'selected' : '' ?>>Наличие сколов и шелушения</option>
                            <option value="Наличие трещин, сколов и шелушения" <?= $this->data['measuring']['main_damage2'] == 'Наличие трещин, сколов и шелушения' ? 'selected' : '' ?>>Наличие трещин, сколов и шелушения</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        Среднее уменьшение массы образцов
                    </td>
                    <td>
                        %
                    </td>
                    <td>
                        <input type="text"
                               class="form-control w-100 control_mass2" name="form_data[<?=$this->data['ugtp_id']?>][control_mass2]"
                               value="<?= $this->data['measuring']['control_mass2'] ?: '-' ?>">
                    </td>
                    <td>
                        Среднее уменьшение массы образцов
                    </td>
                    <td>
                        %
                    </td>
                    <td>
                        <input type="text" class="form-control w-100 main-mass2" name="form_data[<?=$this->data['ugtp_id']?>][main_mass2]"
                               value="<?= $this->data['measuring']['main_mass2'] ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Прочность при сжатии насыщенных образцов</td>
                    <td>МПа</td>
                    <td>
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength2][0]" step="any"
                               value="<?= $this->data['measuring']['control_strength2']['0'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength2][1]" step="any"
                               value="<?= $this->data['measuring']['control_strength2']['1'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength2][2]" step="any"
                               value="<?= $this->data['measuring']['control_strength2']['2'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength2][3]" step="any"
                               value="<?= $this->data['measuring']['control_strength2']['3'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength2][4]" step="any"
                               value="<?= $this->data['measuring']['control_strength2']['4'] ?>">
                        <input type="number"
                               class="form-control bg-white w-100 mb-1 control_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][control_strength2][5]" step="any"
                               value="<?= $this->data['measuring']['control_strength2']['5'] ?>">
                    </td>
                    <td>
                        Прочность при сжатии образцов после испытания
                    </td>
                    <td>
                        МПа
                    </td>
                    <td>
                        <input type="text"
                               class="form-control bg-white w-100 mb-1 main_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength2][0]"
                               value="<?= $this->data['measuring']['main_strength2']['0'] ?>">
                        <input type="text"
                               class="form-control bg-white w-100 mb-1 main_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength2][1]"
                               value="<?= $this->data['measuring']['main_strength2']['1'] ?>">
                        <input type="text"
                               class="form-control bg-white w-100 mb-1 main_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength2][2]"
                               value="<?= $this->data['measuring']['main_strength2']['2'] ?>">
                        <input type="text"
                               class="form-control bg-white w-100 mb-1 main_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength2][3]"
                               value="<?= $this->data['measuring']['main_strength2']['3'] ?>">
                        <input type="text"
                               class="form-control bg-white w-100 mb-1 main_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength2][4]"
                               value="<?= $this->data['measuring']['main_strength2']['4'] ?>">
                        <input type="text"
                               class="form-control bg-white w-100 mb-1 main_strength2"
                               name="form_data[<?=$this->data['ugtp_id']?>][main_strength2][5]"
                               value="<?= $this->data['measuring']['main_strength2']['5'] ?>">
                    </td>
                </tr>
                <tr>
                    <td>Средняя прочность при сжатии насыщенных образцов</td>
                    <td>МПа</td>
                    <td>
                        <input type="number" class="form-control w-100 control_medium2" name="form_data[<?=$this->data['ugtp_id']?>][control_medium2]" step="any"
                               value="<?= $this->data['measuring']['control_medium2'] ?>" readonly>
                    </td>
                    <td>
                        Средняя прочность при сжатии образцов после испытания
                    </td>
                    <td>
                        МПа
                    </td>
                    <td>
                        <input type="text" class="form-control w-100 main_medium2" name="form_data[<?=$this->data['ugtp_id']?>][main_medium2]"
                               value="<?= $this->data['measuring']['main_medium2'] ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Наиб. разность (Разность max-min), Wm, МПа</td>
                    <td>МПа</td>
                    <td>
                        <input type="number" class="form-control w-100 control_difference2" name="form_data[<?=$this->data['ugtp_id']?>][control_difference2]" step="any"
                               value="<?= $this->data['measuring']['control_difference2'] ?>" readonly>
                    </td>
                    <td>Наиб. разность (Разность max-min), Wm, МПа</td>
                    <td>МПа</td>
                    <td>
                        <input type="text" class="form-control w-100 main_difference2" name="form_data[<?=$this->data['ugtp_id']?>][main_difference2]"
                               value="<?= $this->data['measuring']['main_difference2'] ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Среднеквадр откл. σ=наиб.разн/Коэффиц</td>
                    <td>-</td>
                    <td>
                        <input type="number" class="form-control w-100 control_rms2" name="form_data[<?=$this->data['ugtp_id']?>][control_rms2]" step="any"
                               value="<?= $this->data['measuring']['control_rms2'] ?>" readonly>
                    </td>
                    <td>Среднеквадр откл. σ=наиб.разн/Коэффиц</td>
                    <td>-</td>
                    <td>
                        <input type="text" class="form-control w-100 main_rms2" name="form_data[<?=$this->data['ugtp_id']?>][main_rms2]"
                               value="<?= $this->data['measuring']['main_rms2'] ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Коэффициент вариации</td>
                    <td>-</td>
                    <td>
                        <input type="number" class="form-control w-100 control_variation2" name="form_data[<?=$this->data['ugtp_id']?>][control_variation2]" step="any"
                               value="<?= $this->data['measuring']['control_variation2'] ?>" readonly>
                    </td>
                    <td>Коэффициент вариации</td>
                    <td>-</td>
                    <td>
                        <input type="text" class="form-control w-100 main_variation2" name="form_data[<?=$this->data['ugtp_id']?>][main_variation2]"
                               value="<?= $this->data['measuring']['main_variation2'] ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Нижняя граница доверительного интервала
                        X<sub>min</sub><sup>I</sup></td>
                    <td>МПа</td>
                    <td>
                        <input type="number" class="form-control w-100 control_bottom_line2" name="form_data[<?=$this->data['ugtp_id']?>][control_bottom_line2]" step="any"
                               value="<?= $this->data['measuring']['control_bottom_line2'] ?>" readonly>
                    </td>
                    <td>
                        Нижняя граница доверительного интервала
                        X<sub>min</sub><sup>II</sup>
                    </td>
                    <td>МПа</td>
                    <td>
                        <input type="text" class="form-control w-100 main_bottom_line2" name="form_data[<?=$this->data['ugtp_id']?>][main_bottom_line2]"
                               value="<?= $this->data['measuring']['main_bottom_line2'] ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td>0,9X<sub>min</sub><sup>I</sup></td>
                    <td>МПа</td>
                    <td>
                        <input type="number"
                               class="form-control w-100 x_min2_0_9"
                               name="form_data[<?=$this->data['ugtp_id']?>][x_min2_0_9]" step="any"
                               value="<?= $this->data['measuring']['x_min2_0_9'] ?>" readonly>
                    </td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td colspan="8">
                        <div class="form-group">
                            <label for="ratio2" class="mb-2">Примечания: Соотношение
                                X<sub>min</sub><sup>II</sup> ≥ 0,9
                                X<sub>min</sub><sup>I</sup></label>
                            <textarea class="form-control com" name="form_data[<?=$this->data['ugtp_id']?>][com]"><?= $this->data['measuring']['com'] ?></textarea>
                            <br>
                            <select class="form-select ratio2 ratio disabled bg-light-gray <?= $this->data['measuring']['ratio2'] === '1' ? 'border-secondary' : 'border-danger'?>" name="form_data[<?=$this->data['ugtp_id']?>][ratio2]">
                                <option value="2"
                                    <?= $this->data['measuring']['ratio2'] === '0' ? 'selected' : '' ?>>
                                    не соблюдается
                                </option>
                                <option value="1"
                                    <?= $this->data['measuring']['ratio2'] === '1' ? 'selected' : '' ?>>
                                    соблюдается
                                </option>
                            </select>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <button type="button"
                class="btn btn-primary calculate calculate-control me-2" <?= $this->data['measuring']['mark'] ? '' : 'disabled' ?>
                data-number="2">Рассчитать
        </button>
        <button type="submit" class="btn btn-primary save" <?= $this->data['measuring']['mark'] ? '' : 'disabled' ?>>Сохранить
        </button>
    </div>
    <!--./basic-wrapper-->
</div>
<!--./measurement-wrapper-->