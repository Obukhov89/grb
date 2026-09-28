<style>
    .file-upload-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .file-upload {
        display: flex;
        align-items: center;

        height: 38px;
        flex: 1;

        border: 1px solid #d9dfe5;
        background: #fff;
        cursor: pointer;
    }

    .file-upload input[type="file"] {
        /*display: none;*/
    }

    .file-upload__button {
        display: flex;
        align-items: center;

        height: 100%;
        padding: 0 15px;

        background: #f5f6f7;
        border-right: 1px solid #d9dfe5;
    }

    .file-upload__name {
        padding: 0 12px;

        color: #777;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<?php if (isset($this->data['request']['id'])): ?>
    <header class="header-requirement mb-3">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-2">
                    <a class="nav-link" href="<?= URI ?>/request/list/" title="Вернуться к списку">
                        <i class="fa-solid fa-list"></i>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="<?= URI ?>/request/card/<?= $this->data['request']['id'] ?>"
                       title="Вернуться в карточку">
                        <svg class="icon" width="20" height="20">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#card"/>
                        </svg>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <h2 class="d-flex mb-3">
        <?php if ($this->data['request']['id'] < DEAL_NEW_STATUS): ?>
            <div class="stage-block rounded <?= $this->data['stage']['color'] ?> me-1 mt-1"
                 title="<?= $this->data['stage']['title'] ?>"></div>
        <?php else: ?>
            <a class="stage-block rounded me-1 mt-1" title="<?= $this->data['status_list']['title'] ?>"
               style="background-color: <?= $this->data['status_list']['color'] ?>"></a>
        <?php endif; ?>
        Заявка <?= $this->data['deal_title'] ?>
    </h2>
<?php endif; ?>

<?php if (isset($this->data['request']['id']) && empty($this->data['request']['id'])): ?>
<form action="<?= URI ?>/import/uploadRequest/" method="POST" enctype="multipart/form-data">
    <div class="panel panel-default">
        <header class="panel-heading">
            ИМПОРТ ИЗ ФАЙЛА

            <span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
        </span>
        </header>
        <div class="panel-body file-upload-row">

            <label class="file-upload">
                <input
                        type="file"
                        name="import_file"
                        id="import_file"
                >
            </label>

            <button type="submit" class="btn btn-primary">
                Загрузить
            </button>

        </div>
    </div>
</form>
<?php endif; ?>

<?php if (isset($this->data['request']['id']) && !empty($this->data['request']['id'])): ?>

<form class="form-horizontal" method="post" id="create_request_form" action="<?= URI ?>/request/insertUpdate/">


    <div class="panel panel-default">
        <header class="panel-heading">
            Реквизиты
            <span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
         </span>
        </header>
        <div class="panel-body">
            <?php if (isset($this->data['request']['id']) && !empty($this->data['request']['id'])): ?>
                <input type="hidden" value="<?= $this->data['request']['id'] ?>" name="id">
            <?php endif; ?>
            <div class="form-group row">
                <label for="company" class="col-sm-2 col-form-label">Клиент <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <input id="company" class="form-control" list="company_list" type="text" name="company"
                           value="<?= $this->data['request']['company']['TITLE'] ?? $this->data['request']['company'] ?? '' ?>"
                           autocomplete="off" required>
                    <input type="hidden" name="company_id" id="company-hidden"
                           value="<?= $this->data['request']['company']['ID'] ?? $this->data['request']['company_id'] ?? '' ?>">
                    <datalist id="company_list">
                        <?php if (isset($this->data['companies'])): ?>
                            <?php foreach ($this->data['companies'] as $company): ?>
                                <option data-value="<?= $company['ID'] ?>"><?= $company['TITLE'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </datalist>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">ИП</label>
                <div class="col-sm-8">
                    <input class="form-check-input check-ip" type="checkbox" name="check_ip"
                           value="1" <?= $this->data['request']['check_ip'] == 1 ? 'checked' : '' ?>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Полное наименование компании</label>
                <div class="col-sm-8">
                    <input type="text" name="CompanyFullName" class="form-control clearable"
                           value="<?= $this->data['request']['CompanyFullName'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">ИНН</label>
                <div class="col-sm-8">
                    <input type="number" name="INN" class="form-control appearance-none clearable"
                           value="<?= $this->data['request']['INN'] ?? '' ?>">
                    <div id="innHelp" class="form-text"></div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">КПП</label>
                <div class="col-sm-8">
                    <input type="text" name="KPP" class="form-control number-only clearable"
                           value="<?= $this->data['request']['KPP'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label for="ogrnip" class="col-sm-2 col-form-label">ОГРНИП</label>
                <div class="col-sm-8">
                    <input type="text" name="OGRNIP" id="ogrnip"
                           class="form-control number-only clearable" <?= $this->data['request']['check_ip'] == 1 ? '' : 'disabled' ?>
                           value="<?= $this->data['request']['OGRNIP'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">ОГРН</label>
                <div class="col-sm-8">
                    <input type="text" name="OGRN" class="form-control number-only clearable"
                           value="<?= $this->data['request']['OGRN'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Адрес</label>
                <div class="col-sm-8">
                    <input type="text" name="ADDR" class="form-control clearable"
                           value="<?= $this->data['request']['ADDR'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Фактический адрес</label>
                <div class="col-sm-8">
                    <input type="text" id="actual_address" name="ACTUAL_ADDRESS" class="form-control clearable"
                           value="<?= $this->data['request']['ACTUAL_ADDRESS'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Почтовый адрес</label>
                <div class="col-sm-8">
                    <input type="text" name="mailingAddress" class="form-control clearable"
                           value="<?= $this->data['request']['mailingAddress'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">E-mail для договора</label>
                <div class="col-sm-8">
                    <input type="email" name="EMAIL" class="form-control clearable" placeholder="_@_._"
                           value="<?= $this->data['request']['EMAIL'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">E-mail <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <input type="email" name="POST_MAIL" class="form-control clearable" placeholder="_@_._" required
                           value="<?= $this->data['request']['POST_MAIL'] ?? '' ?>">
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-primary add_email btn-add-del" type="button">
                        <i class="fa-solid fa-plus icon-fix"></i>
                    </button>
                </div>
            </div>

            <?php if (isset($this->data['request']['addEmail'])): ?>
                <?php foreach ($this->data['request']['addEmail'] as $i => $email): ?>
                    <div class="form-group row added_mail">
                        <label class="col-sm-2 col-form-label">Дополнительный E-mail <?= ($i + 1) ?></label>
                        <div class="col-sm-8">
                            <input type="email" name="addEmail[]" class="form-control" placeholder="_@_._"
                                   value="<?= $email ?>">
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-danger remove_this btn-add-del" type="button">-</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Телефон <span class="redStars">*</span></label>
                <div class="col-sm-8">
                    <input type="text" name="PHONE" class="form-control clearable" required
                           value="<?= $this->data['request']['PHONE'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Контактное лицо</label>
                <div class="col-sm-8">
                    <input type="text" name="CONTACT" class="form-control clearable" maxlength="150"
                           value="<?= $this->data['request']['CONTACT'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Должность руководителя</label>
                <div class="col-sm-8">
                    <input type="text" name="Position2" class="form-control clearable"
                           value="<?= $this->data['request']['Position2'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Должность руководителя в родительном падеже</label>
                <div class="col-sm-8">
                    <input type="text" name="PositionGenitive" class="form-control clearable"
                           value="<?= $this->data['request']['PositionGenitive'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">ФИО руководителя</label>
                <div class="col-sm-8">
                    <input type="text" name="DirectorFIO" class="form-control clearable"
                           value="<?= $this->data['request']['DirectorFIO'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Действует на основании</label>
                <div class="col-sm-8">
                    <input type="text" name="ACTS_BASIS" class="form-control"
                           value="<?= $this->data['request']['ACTS_BASIS'] ?? 'Устава' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Расчетный счет</label>
                <div class="col-sm-8">
                    <input type="text" name="RaschSchet" class="form-control clearable"
                           value="<?= $this->data['request']['RaschSchet'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Кор. счёт</label>
                <div class="col-sm-8">
                    <input type="text" name="KSchet" class="form-control clearable"
                           value="<?= $this->data['request']['KSchet'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Лицевой счёт</label>
                <div class="col-sm-8">
                    <input type="text" name="l_schet" class="form-control clearable"
                           value="<?= $this->data['request']['l_schet'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">БИК</label>
                <div class="col-sm-8">
                    <input type="text" name="BIK" class="form-control clearable"
                           value="<?= $this->data['request']['BIK'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Наименование банка</label>
                <div class="col-sm-8">
                    <input type="text" name="BankName" class="form-control clearable"
                           value="<?= $this->data['request']['BankName'] ?? '' ?>">
                </div>
                <div class="col-sm-2"></div>
            </div>

        </div>
    </div>


<!--    <div class="panel panel-default">-->
<!--        <header class="panel-heading">-->
<!--            Данные заявки-->
<!--            <span class="tools float-end">-->
<!--            <a href="#" class="fa fa-chevron-up"></a>-->
<!--         </span>-->
<!--        </header>-->
<!--        <div class="panel-body">-->
<!--            <div class="form-group row">-->
<!--                <label class="col-sm-2 col-form-label">Основание для проведения испытаний</label>-->
<!--                <div class="col-sm-8">-->
<!--                    <div class="input-group">-->
<!--                        <input type="radio" class="btn-check" id="order" name="order_type" autocomplete="off"-->
<!--                               value="1" --><?php //= $this->data['request']['order_type'] == 1 || empty($this->data['request']['order_type']) ? 'checked' : '' ?>
<!--                        <label class="btn btn-outline-primary" for="order">Договор</label>-->
<!--                        <input type="radio" class="btn-check" id="offer-invoice" name="order_type" autocomplete="off"-->
<!--                               value="2" --><?php //= $this->data['request']['order_type'] == 2 ? 'checked' : '' ?>
<!--                        <label class="btn btn-outline-primary" for="offer-invoice">Счет-оферта</label>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="col-sm-2"></div>-->
<!--            </div>-->
<!---->
<!--            <div class="form-group row order-type ">-->
<!--                <label class="col-sm-2 col-form-label">Выбрать документ-основание:</label>-->
<!--                <div class="col-sm-8">-->
<!--                    <select class="form-control" name="NUM_DOGOVOR">-->
<!--                        <option value="--><?php //= $this->data['request']['DOGOVOR_NUM'] ?? '' ?><!--">Новый договор</option>-->
<!--                        --><?php //foreach ($this->data['contracts'] as $contract): ?>
<!--                            <option value="--><?php //= $contract['ID'] ?><!--" --><?php //= $this->data['request']['DOGOVOR_NUM'] == $contract['ID'] ? 'selected' : '' ?>
<!--                                --><?php //= $contract['CONTRACT_TYPE'] ?? 'Договор' ?>
<!--                                №--><?php //= $contract['number_client'] ? $contract['number_client'] : $contract['NUMBER'] ?>
<!--                                от --><?php //= $contract['DATE'] ?><!----><?php //= $contract['endless_text'] ?>
<!--                            </option>-->
<!--                        --><?php //endforeach; ?>
<!--                    </select>-->
<!--                </div>-->
<!--                <div class="col-sm-2"></div>-->
<!--            </div>-->
<!---->
<!--            <div class="form-group row">-->
<!--                <label class="col-sm-2 col-form-label">Тип заявки <span class="redStars">*</span></label>-->
<!--                <div class="col-sm-8">-->
<!--                    <select class="form-control" name="REQ_TYPE" required>-->
<!--                        <option value="SALE" --><?php //= (isset($this->data['request']['REQ_TYPE']) && $this->data['request']['REQ_TYPE'] === 'SALE') ? 'selected' : '' ?>
<!--                            ИЦ-->
<!--                        </option>-->
<!--                        <option value="COMPLEX" --><?php //= (isset($this->data['request']['REQ_TYPE']) && $this->data['request']['REQ_TYPE'] === 'COMPLEX') ? 'selected' : '' ?>
<!--                            ОСК-->
<!--                        </option>-->
<!--                        <option value="1" --><?php //= (isset($this->data['request']['REQ_TYPE']) && $this->data['request']['REQ_TYPE'] == '1') ? 'selected' : '' ?>
<!--                            ВЛК-->
<!--                        </option>-->
<!--                        <option value="2" --><?php //= (isset($this->data['request']['REQ_TYPE']) && $this->data['request']['REQ_TYPE'] == '2') ? 'selected' : '' ?>
<!--                            МСИ-->
<!--                        </option>-->
<!--                        <option value="5" --><?php //= (isset($this->data['request']['REQ_TYPE']) && $this->data['request']['REQ_TYPE'] == '5') ? 'selected' : '' ?>
<!--                            АП-->
<!--                        </option>-->
<!--                        <option value="4" --><?php //= (isset($this->data['request']['REQ_TYPE']) && $this->data['request']['REQ_TYPE'] == '4') ? 'selected' : '' ?>
<!--                            НК-->
<!--                        </option>-->
<!--                        <option value="8" --><?php //= (isset($this->data['request']['REQ_TYPE']) && $this->data['request']['REQ_TYPE'] == '8') ? 'selected' : '' ?>
<!--                            Н-->
<!--                        </option>-->
<!--                        <option value="7" --><?php //= (isset($this->data['request']['REQ_TYPE']) && $this->data['request']['REQ_TYPE'] == '7') ? 'selected' : '' ?>
<!--                            ПР-->
<!--                        </option>-->
<!--                        <option value="9" --><?php //= (isset($this->data['request']['REQ_TYPE']) && $this->data['request']['REQ_TYPE'] == '9') ? 'selected' : '' ?>
<!--                            НИЦ-->
<!--                        </option>-->
<!--                    </select>-->
<!--                </div>-->
<!--                <div class="col-sm-2"></div>-->
<!--            </div>-->
<!---->
<!--            <div class="form-group row">-->
<!--                <label class="col-sm-2 col-form-label">Главный Ответственный <span class="redStars">*</span></label>-->
<!--                <div class="col-sm-8">-->
<!--                    <input-->
<!--                            id="assigned0"-->
<!--                            class="form-control"-->
<!--                            type="text"-->
<!--                            list="assigneds_main"-->
<!--                            name="ASSIGNED[]"-->
<!--                            size="20"-->
<!--                            value="--><?php //= $this->data['request']['assign'][0]['user_name'] ?? '' ?><!--"-->
<!--                            autocomplete="off"-->
<!--                            required-->
<!--                    >-->
<!--                    <input name="id_assign[]" id="assigned0-hidden" type="hidden" class="assigned_id"-->
<!--                           value="--><?php //= $this->data['request']['assign'][0]['user_id'] ?? '' ?><!--">-->
<!--                    <datalist id="assigneds_main">-->
<!--                        --><?php //if (isset($this->data['clients_main'])): ?>
<!--                            --><?php //foreach ($this->data['clients_main'] as $client): ?>
<!--                                <option data-value="--><?php //= $client['ID'] ?><!--">--><?php //= $client['LAST_NAME'] ?><!-- --><?php //= $client['NAME'] ?><!--</option>-->
<!--                            --><?php //endforeach; ?>
<!--                        --><?php //endif; ?>
<!--                    </datalist>-->
<!--                    <datalist id="assigneds">-->
<!--                        --><?php //if (isset($this->data['clients'])): ?>
<!--                            --><?php //foreach ($this->data['clients'] as $client): ?>
<!--                                <option data-value="--><?php //= $client['ID'] ?><!--">--><?php //= $client['LAST_NAME'] ?><!-- --><?php //= $client['NAME'] ?><!--</option>-->
<!--                            --><?php //endforeach; ?>
<!--                        --><?php //endif; ?>
<!--                    </datalist>-->
<!--                </div>-->
<!--                <div class="col-sm-2">-->
<!--                    <button class="btn btn-primary add_assigned btn-add-del" type="button">-->
<!--                        <i class="fa-solid fa-plus icon-fix"></i>-->
<!--                    </button>-->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--            --><?php //if (count($this->data['request']['assign']) > 1): ?>
<!--                --><?php //for ($i = 1; $i < count($this->data['request']['assign']); $i++): ?>
<!--                    <div class="form-group row added_assigned">-->
<!--                        <label class="col-sm-2 col-form-label">Ответственный</label>-->
<!--                        <div class="col-sm-8">-->
<!--                            <input-->
<!--                                    id="assigned--><?php //= $i ?><!--"-->
<!--                                    class="form-control"-->
<!--                                    type="text"-->
<!--                                    list="assigneds"-->
<!--                                    name="ASSIGNED[]"-->
<!--                                    size="20"-->
<!--                                    value="--><?php //= $this->data['request']['assign'][$i]['user_name'] ?><!--"-->
<!--                                    required-->
<!--                            >-->
<!--                            <input name="id_assign[]" id="assigned--><?php //= $i ?><!---hidden" class="assigned_id" type="hidden"-->
<!--                                   value="--><?php //= $this->data['request']['assign'][$i]['user_id'] ?><!--">-->
<!--                        </div>-->
<!--                        <div class="col-sm-2">-->
<!--                            <button class="btn btn-danger remove_this btn-add-del" type="button">-->
<!--                                <i class="fa-solid fa-minus icon-fix"></i>-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                --><?php //endfor; ?>
<!--            --><?php //endif; ?>
<!--        </div>-->
<!--    </div>-->

    <button class="btn btn-primary block-after-click" type="submit" id="save_button" name="save"
            value="<?= $this->data['request']['save'] ?? '' ?>">Сохранить
    </button>

</form>
<?php endif;?>
<script>
    document.getElementById("create_request_form").addEventListener("submit", function () {
        document.getElementById("save_button").disabled = true;
    });

    document.getElementById('import_file').addEventListener('change', function () {
        alert(this.files[0].name);
    });
</script>