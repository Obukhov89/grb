<style>
    .header-menu .nav-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="mail-wrapper import">
    <header class="header-requirement mb-3 pt-0">
        <nav class="header-menu w-100">
            <ul class="nav w-100">
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
                        <button form="form_mail" class="btn btn-gradient" id="submit_btn" type="submit" name="save">
                            Сохранить
                        </button>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <form class="form-horizontal" id="form_mail" method="post" action="<?= URI ?>/import/insertUpdateMail/">
        <div class="panel panel-default">
            <header class="panel-heading">
                Почта
                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
            </header>
            <div class="panel-body">
                <div class="signature">Исходящая почта</div>

                <input id="mailId" type="hidden" name="id" value="<?= $this->data['form']['id'] ?? '' ?>">

                <div class="form-group row">
                    <label for="smtpHost" class="col-sm-2 col-form-label">Адрес почтового сервера <span
                                class="redStars">*</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="form[smtp_host]" class="form-control clearable" id="smtpHost"
                               value="<?= $this->data['form']['smtp_host'] ?? '' ?>">
                        <div class="form-text">
                            хост или IP-адрес вашего smtp-сервера (пример: smtp.yandex.ru )
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label for="smtpPort" class="col-sm-2 col-form-label">Порт <span class="redStars">*</span></label>
                    <div class="col-sm-8">
                        <input type="number" name="form[smtp_port]" class="form-control appearance-none clearable"
                               id="smtpPort" value="<?= $this->data['form']['smtp_port'] ?? 465 ?>" required>
                        <div class="form-text">
                            порт по умолчанию — 465 , но некоторые smtp-серверы используют собственный порт (пример: 587
                            )
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Использовать защищенное соединение</label>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-1">
                                <label class="switch d-inline-block align-bottom">
                                    <input class="form-check-input" id="smtpCheckSecured"
                                           name="form[smtp_check_secured]"
                                           type="checkbox"
                                        <?= $this->data['form']['smtp_check_secured'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="col">
                                <select id="smtpSecured"
                                        class="form-select <?= $this->data['form']['smtp_check_secured'] ? '' : 'bg-gainsboro-gray' ?>"
                                        name="form[smtp_secured]" <?= $this->data['form']['smtp_check_secured'] ? '' : 'disabled' ?>>
                                    <option value="ssl" <?= $this->data['form']['smtp_secured'] === 'ssl' ? 'selected' : '' ?>>
                                        ssl
                                    </option>
                                    <option value="tsl" <?= $this->data['form']['smtp_secured'] === 'tsl' ? 'selected' : '' ?>>
                                        tsl
                                    </option>
                                </select>
                                <div class="form-text">
                                    если smtp-серверу требуется защищенное соединение (ssl, tsl)
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row mb-3">
                    <label class="col-sm-2 col-form-label">Использовать аутентификацию</label>
                    <div class="col-sm-8">
                        <div class="row mb-3">
                            <div class="col-1">
                                <label class="switch d-inline-block align-bottom">
                                    <input class="form-check-input" id="smtpAuthentication"
                                           name="form[smtp_authentication]"
                                           type="checkbox"
                                        <?= $this->data['form']['smtp_authentication'] ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="col">
                                <input type="text" name="form[smtp_login]" class="form-control clearable" id="smtpLogin"
                                       value="<?= $this->data['form']['smtp_login'] ?? '' ?>"
                                       placeholder="введите login"
                                    <?= $this->data['form']['smtp_authentication'] ? '' : 'disabled' ?>>
                                <div class="form-text">
                                    login требуется, если установлен флажок «Использовать аутентификацию» (например
                                    account@mail.ru )
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1"></div>
                            <div class="col">
                                <input type="password" name="form[smtp_password]" class="form-control clearable"
                                       id="smtpPassword"
                                       value="<?= $this->data['form']['smtp_password'] ?? '' ?>"
                                       placeholder="введите пароль"
                                    <?= $this->data['form']['smtp_authentication'] ? '' : 'disabled' ?>>
                                <div class="form-text">
                                    пароль требуется, если установлен флажок «Использовать аутентификацию»
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1"></div>
                            <div class="col">
                                <label class="switch d-inline-block align-bottom">
                                    <input class="form-check-input" id="viewPassword" type="checkbox">
                                    <span class="slider"></span>
                                </label>
                                <div class="form-text">
                                    показать пароль
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Электронная почта</label>
                    <div class="col-sm-8">
                        <input type="email" name="form[email]" class="form-control clearable" placeholder="_@_._"
                               value="<?= $this->data['form']['email'] ?? '' ?>" required>
                        <div class="form-text">
                            адрес электронной почты (пример: account@mail.ru )
                        </div>
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
                                    <img src="<?= URI ?>/upload/import/mail/<?= $this->data['file'] ?>?v=<?= rand() ?>"
                                         alt="ico" width="190">
                                </div>
                                <div class="file-preview-back flex-column">
                                    <button type="button" class="btn btn-danger remove-file"
                                            data-file="<?= $this->data['file'] ?>">
                                        Удалить
                                    </button>
                                    <a download class="btn btn-success"
                                       href="<?= URI ?>/upload/import/mail/<?= $this->data['file'] ?>">Скачать</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <label class="upload-logo cursor-pointer"
                               title="Загрузить PNG-версию">
                            <svg class="icon" width="30" height="30">
                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#upload"/>
                            </svg>
                            <input class="d-none" id="uploadLogo" type="file" name="upload_logo">
                        </label>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <header class="panel-heading">
            Тест и проверка
            <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
        </header>
        <div class="panel-body">
            <div class="message <?= empty($this->data['form']['id']) ? '' : 'd-none' ?>">
                Сохраните данные исходящей почты, чтобы отправить тестовое письмо
            </div>
            <div id="sendMailTest" class="<?= empty($this->data['form']['id']) ? 'd-none' : '' ?>">
                <div class="signature">Входящая почта</div>

                <div class="border p-2 border-bottom-0 bg-white">
                    <em>Внимание! Перед отправкой тестового письма убедитесь
                        что данные исходящей почты заполнены правильно и сохранены</em>
                </div>
                <div class="border p-3 mb-4">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Электронная почта</label>
                        <div class="col-sm-8">
                            <input type="email" name="form[email_to]" class="form-control clearable" id="emailTo" placeholder="_@_._"
                                   value="<?= $this->data['form']['email_to'] ?? '' ?>" required>
                            <div class="form-text">
                                тестовое письмо будет отправлено на этот адрес (например, account@mail.ru)
                            </div>
                        </div>
                        <div class="col-sm-2"></div>
                    </div>
                </div>

                <button class="btn btn-primary btn-processing d-none" type="button" disabled>Обработка ...</button>
                <button class="btn btn-primary mt-0" id="send" type="button" name="send">Отправить</button>
                <span class="ms-2" id="processing"></span>
            </div>
        </div>
    </div>

    <div id="alert_modal" class="bg-light mfp-hide col-md-6 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->
</div>