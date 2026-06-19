<style>
    .header-menu, ul.nav {
        width: 100%;
    }

    .header-menu .nav-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .select2-container .select2-selection--single {
        padding: 0.25rem 0.15rem;
        height: auto;
        border-radius: 0.25rem;
        box-sizing: border-box;
        min-width: 100%;
        border: var(--bs-border-width) solid var(--bs-border-color);
    }
    #workarea input.form-control, #workarea select.form-control, .mfp-content .form-control {
        min-width: auto;
    }
</style>

<div class="company-info-wrapper import">
    <header class="header-requirement mb-3 pt-0">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-3">
                    <a class="nav-link fa-solid icon-nav fa-arrow-left disabled" id="back-button" style="font-size: 22px;" title="Назад" data-bs-toggle="tooltip">
                    </a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link fa-solid icon-nav fa-rectangle-list" href="<?= URI ?>/import/list" style="font-size: 22px;" title="Профиль лаборатории" data-bs-toggle="tooltip">
                    </a>
                </li>

                <li class="nav-item ms-auto">
                    <div class="col">
                        <button form="infocomp" class="btn btn-gradient" id="submit_btn" type="submit" name="save">Сохранить</button>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <form class="form-horizontal" id="infocomp" method="post" action="<?= URI ?>/import/insertUpdateInfo/">
        <div class="panel panel-default">
            <header class="panel-heading">
                Реквизиты
                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
            </header>
            <div class="panel-body">
                <div class="form-group row align-items-center">
                    <label for="title" class="col-sm-2 col-form-label">Компания(лаборатория) <span
                                class="redStars">*</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="form[title]" class="form-control clearable" id="title" placeholder="Введите название компании"
                               value="<?= $this->data['form']['title'] ?? '' ?>">
                        <input type="hidden" name="id" value="<?= $this->data['form']['id'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">ИП</label>
                    <div class="col-sm-8">
                        <input class="form-check-input check-ip" type="checkbox" name="form[ip]" placeholder="Введите ИП"
                               value="1" <?= $this->data['form']['ip'] == 1 ? 'checked' : '' ?>>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Департамент</label>
                    <div class="col-sm-8">
                        <textarea type="text" name="form[department]" class="form-control clearable" placeholder="Введите департамент"
                        ><?= $this->data['form']['department'] ?? '' ?></textarea>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Полное наименование компании</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[company_full_name]" class="form-control clearable" placeholder="Введите полное наименование компании"
                               value="<?= $this->data['form']['company_full_name'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Сайт</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[website]" class="form-control clearable" placeholder="Введите сайт"
                               value="<?= $this->data['form']['website'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Номер записи</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[ross_number]" class="form-control clearable" placeholder="Введите номер записи"
                               value="<?= $this->data['form']['ross_number'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">ИНН</label>
                    <div class="col-sm-8">
                        <input type="number" name="form[inn]" class="form-control appearance-none clearable" placeholder="Введите ИНН"
                               value="<?= $this->data['form']['inn'] ?? '' ?>">
                        <div id="innHelp" class="form-text"></div>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">КПП</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[kpp]" id="kpp" class="form-control number-only clearable" <?= $this->data['form']['ip'] == 0 ? '' : 'disabled' ?> placeholder="Введите КПП"
                               value="<?= $this->data['form']['kpp'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="ogrnip" class="col-sm-2 col-form-label">ОГРНИП</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[ogrnip]" id="ogrnip" placeholder="Введите ОГРНИП"
                               class="form-control number-only clearable" <?= $this->data['form']['ip'] == 1 ? '' : 'disabled' ?>
                               value="<?= $this->data['form']['ogrnip'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">ОГРН</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[ogrn]" id="ogrn" class="form-control number-only clearable" <?= $this->data['form']['ip'] == 0 ? '' : 'disabled' ?> placeholder="Введите ОГРН"
                               value="<?= $this->data['form']['ogrn'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Город (для договора)</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[city]" class="form-control clearable" placeholder="Введите город"
                               value="<?= $this->data['form']['city'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Адрес</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[addr]" class="form-control clearable" placeholder="Введите адрес"
                               value="<?= $this->data['form']['addr'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Фактический адрес</label>
                    <div class="col-sm-8">
                        <input type="text" id="actual_address" name="form[actual_address]" placeholder="Введите фактический адрес"
                               class="form-control clearable"
                               value="<?= $this->data['form']['actual_address'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Почтовый адрес</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[mailing_address]" class="form-control clearable" placeholder="Введите почтовый адрес"
                               value="<?= $this->data['form']['mailing_address'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">E-mail для договора</label>
                    <div class="col-sm-8">
                        <input type="email" name="form[email]" class="form-control clearable" placeholder="_@_._"
                               value="<?= $this->data['form']['email'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">E-mail <span class="redStars">*</span></label>
                    <div class="col-sm-8">
                        <input type="email" name="form[post_mail]" class="form-control clearable" placeholder="_@_._"
                               required
                               value="<?= $this->data['form']['post_mail'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-primary add-email btn-add-del" type="button">
                            <i class="fa-solid fa-plus icon-fix"></i>
                        </button>
                    </div>
                </div>

                <?php if (isset($this->data['form']['add_email'])): ?>
                    <?php foreach ($this->data['form']['add_email'] as $i => $email): ?>
                        <div class="form-group row align-items-center added_mail">
                            <label class="col-sm-2 col-form-label">Дополнительный E-mail <?= ($i + 1) ?></label>
                            <div class="col-sm-8">
                                <input type="email" name="form[add_email][]" class="form-control" placeholder="_@_._"
                                       value="<?= $email ?>">
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-danger remove_this btn-add-del" type="button">-</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Телефон <span class="redStars">*</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="form[phone]" class="form-control clearable" required placeholder="Введите телефон"
                               value="<?= $this->data['form']['phone'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Контактное лицо</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[contact_person]" class="form-control clearable" placeholder="Введите контактное лицо"
                               value="<?= $this->data['form']['contact_person'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Расчетный счёт</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[bank_account]" class="form-control clearable" placeholder="Введите расчетный счёт"
                               value="<?= $this->data['form']['bank_account'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Кор. счёт</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[correspondent_account]" class="form-control clearable" placeholder="Введите кор. счёт"
                               value="<?= $this->data['form']['correspondent_account'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Лицевой счёт</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[personal_account]" class="form-control clearable" placeholder="Введите лицевой счёт"
                               value="<?= $this->data['form']['personal_account'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">БИК</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[bik]" class="form-control clearable" placeholder="Введите БИК"
                               value="<?= $this->data['form']['bik'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">ОКПО</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[okpo]" class="form-control clearable" placeholder="Введите ОКПО"
                               value="<?= $this->data['form']['okpo'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">ОКВЭД</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[okved]" class="form-control clearable" placeholder="Введите ОКВЭД"
                               value="<?= $this->data['form']['okved'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">ОКОНХ</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[okonh]" class="form-control clearable" placeholder="Введите ОКОНХ"
                               value="<?= $this->data['form']['okonh'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">ТОФК</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[tofk]" class="form-control clearable" placeholder="Введите ТОФК"
                               value="<?= $this->data['form']['tofk'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">OKTMO</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[oktmo]" class="form-control clearable" placeholder="Введите ОКТМО"
                               value="<?= $this->data['form']['oktmo'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">КБК</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[kbk]" class="form-control clearable" placeholder="Введите КБК"
                               value="<?= $this->data['form']['kbk'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>
                
                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Получатель</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[recipient]" class="form-control clearable" placeholder="Введите получателя"
                               value="<?= $this->data['form']['recipient'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-2 col-form-label">Наименование банка</label>
                    <div class="col-sm-8">
                        <input type="text" name="form[bank_name]" class="form-control clearable" placeholder="Введите наименование банка"
                               value="<?= $this->data['form']['bank_name'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            </div>
        </div>
    </form>

    <div class="panel panel-default">
        <header class="panel-heading">
            Логотип
            <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
        </header>
        <div class="panel-body">
            <div class="d-flex align-items-center">
                <label class="me-4">Файл данных (формат PNG)</label>
                <div class="">
                    <?php if ($this->data['is_file_exists']): ?>
                        <div class="row file-preview-container">
                            <div class="col-2 file-preview-block d-flex flex-column align-items-center justify-content-center">
                                <div class="file-preview-img">
                                    <img src="<?= URI ?>/upload/import/<?= $this->data['file'] ?>?v=<?= rand() ?>" alt="ico" width="190">
                                </div>
                                <div class="file-preview-back flex-column">
                                    <button type="button" class="btn btn-danger remove-file" data-file="<?= $this->data['file'] ?>">
                                        Удалить
                                    </button>
                                    <a download class="btn btn-success"
                                       href="<?= URI ?>/upload/import/<?= $this->data['file'] ?>">Скачать</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <label class="upload-png cursor-pointer"
                               title="Загрузить PNG-версию">
                            <svg class="icon" width="30" height="30">
                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#upload"/>
                            </svg>
                            <input class="d-none" id="uploadPng" type="file" name="upload_png">
                        </label>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->
</div>
