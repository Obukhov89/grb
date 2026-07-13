<style>
    .note-editable ul li:before {
        content: "- ";
    }

    .note-editable ul li {
        list-style-type: none;
    }

    .min-wdt-700 {
        min-width: 700px !important;
        font-weight: bold;
    }
</style>
<div class="wrapper-card m-auto">
    <header class="header-result mb-3">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-2">
                    <a class="nav-link link-back disabled"
                        href="<?= URI ?>/request/card/<?= $this->data['deal_id'] ?? '' ?>"
                        title="Вернуться назад">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#back" />
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-list" href="<?= URI ?>/request/list/" title="Вернуться к списку">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#list" />
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-card" href="<?= URI ?>/request/card/<?= $this->data['deal_id'] ?? '' ?>"
                        title="Карточка заявки">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#card" />
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2 disabled">
                    <a class="nav-link link-doc-send disabled" href="#">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-send" />
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-docs disabled" href="#">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#docs" />
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-doc-edit disabled" href="#">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-edit" />
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link popup-help" href="/ulab/help/LIMS_Manual_Stand/Result_card/Result_card.html" title="ПОМОГИТЕ">
                        <i class="fa-solid fa-question"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <h2 class="d-flex mb-3">
        Заявка <?= $this->data['deal_title'] ?? '' ?>
    </h2>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <header class="panel-heading">
                    Общая информация
                    <span class="tools float-end">
                        <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                        <a href="#" class="fa fa-chevron-up"></a>
                    </span>
                </header>
                <div class="panel-body">
                    <div class="wrapper-info-header">

                        <div class="row">
                            <div class="col-sm-6">
                                <div>Основание для проведения испытаний</div>
                                <div>
                                    <strong>
                                        <?php if (!empty($this->data['contract_number'])): ?>
                                            <?= $this->data['contract_type'] ?> №<?= $this->data['contract_number'] ?> от <?= $this->data['contract_date'] ?>
                                        <?php else: ?>
                                            Договор еще не составлен
                                        <?php endif; ?>
                                    </strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div>Основание для формирования протокола</div>
                                <div class="formation-protocol-reason">
                                    <strong>
                                        <?php if (!empty($this->data['act_number'])): ?>
                                            Акт приемки/передачи проб № <?= $this->data['act_number'] ?> от <?= $this->data['act_date'] ?>
                                        <?php else: ?>
                                            Пробы не поступили
                                        <?php endif; ?>
                                    </strong>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">
                                Испытания проводятся
                            </label>
                            <div class="col-sm-2 pt-2">
                                <div class="form-check form-check-inline min-wdt-700">
                                    <p><?= $this->data['tz_data']['tests_for'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">
                                Указывать нормативную документацию на объект испытаний
                            </label>
                            <div class="col-sm-2 pt-2">
                                <div class="form-check form-check-inline min-wdt-700">
                                    <p><?= $this->data['tz_data']['is_indicate_compliance'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">
                                Результаты испытаний (исследований) оформить
                            </label>
                            <div class="col-sm-2 pt-2">
                                <div class="form-check form-check-inline min-wdt-700">
                                    <p><?= $this->data['tz_data']['presented_as'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">
                                Выдача протокола(ов) испытаний
                            </label>
                            <div class="col-sm-2 pt-2">
                                <div class="form-check form-check-inline min-wdt-700">
                                    <p><?= $this->data['tz_data']['protocol_given'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">
                                Необходимые сведения и документы для проведения испытаний (см. п.2.2.4)
                            </label>
                            <div class="col-sm-2 pt-2">
                                <div class="form-check form-check-inline min-wdt-700">
                                    <p><?= $this->data['tz_data']['add_info'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">
                                Планируемая дата доставки проб в ИЦ
                            </label>
                            <div class="col-sm-2 pt-2">
                                <div class="form-check form-check-inline min-wdt-700">
                                    <p><?= $this->data['tz_data']['probes_to_ic'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">
                                Необходимые дополнительные услуги
                            </label>
                            <div class="col-sm-2 pt-2">
                                <div class="form-check form-check-inline min-wdt-700">
                                    <p><?= $this->data['tz_data']['additional_services'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">
                                Местоположение
                            </label>
                            <div class="col-sm-2 pt-2">
                                <div class="form-check form-check-inline min-wdt-700">
                                    <p><?= $this->data['tz_data']['location'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--./wrapper-info-header-->
                </div>
                <!--./panel-body-->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <header class="panel-heading">
                    Таблица созданных протоколов
                    <span class="tools float-end">
                        <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                        <a href="#" class="fa fa-chevron-up"></a>
                    </span>
                </header>
                <div class="panel-body">
                    <div class="protocols-wrapper">
                        <input class="selected_protocol_id" type="hidden" value="<?= $_GET['protocol_id'] ?? '' ?>">
                        <?php if (!empty($this->data['protocols'])): ?>
                            <div class="table-responsive mb-2">
                                <table class="table text-center table-hover align-middle">
                                    <thead>
                                        <tr class="table-secondary align-middle">
                                            <th class="border-0">
                                                Выбранные пробы
                                                <br>
                                                <input class="form-check-input selected-probe scale-1_5 cursor-pointer mt-2"
                                                    type="radio" name="selected_probe" value="" title="Все пробы"
                                                    <?= $this->data['selected'] ? '' : 'checked' ?>>
                                            </th>
                                            <th class="border-0">Номер протокола</th>
                                            <th class="border-0">Дата протокола</th>
                                            <th class="border-0">Информация по протоколу</th>
                                            <th class="border-0">Вне ЛИС</th>
                                            <th class="border-0">PDF-версия</th>
                                            <th class="border-0">Сформировать протокол</th>
                                            <th class="border-0">Скачать протокол</th>
                                            <th class="border-0">Присвоить номер</th>
                                            <th class="border-0">Удалить протокол</th>
                                            <?php if ($this->data['is_may_unlock']): ?>
                                                <th class="border-0">Разблокировать</th>
                                            <?php endif; ?>
                                            <th class="border-0">Протокол недействителен</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($this->data['protocols'] as $val): ?>
                                            <tr class="<?= $val['table_green'] ?>">
                                                <td>

                                                    <?php if ($val['probe_count']): ?>
                                                        <input class="form-check-input selected-probe scale-1_5 cursor-pointer"
                                                            type="radio" name="selected_probe"
                                                            value="<?= $val['ID'] ?>" <?= $val['selected_probe'] ?>>
                                                    <?php else: ?>
                                                        <span title="Отсутствуют прикреплённые пробы у протокола">
                                                            <input class="form-check-input scale-1_5" type="radio"
                                                                value="" disabled>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (empty($val['INVALID'])): ?>
                                                        <a href="<?= URI ?>/result/card_oati/<?= $this->data['deal_id'] ?>?protocol_id=<?= $val['ID'] ?><?= $val['selected_probe'] ? '&selected' : '' ?>"
                                                            class="text-decoration-none text-nowrap fw-bold">
                                                            <?= $val['view_number'] ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <?= $val['view_number'] ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $val['date_ru'] ?></td>
                                                <td>
                                                    <?php if ($val['protocol_info']): ?>
                                                        <span title="Для доступа к данным протокола проверте выбран ли протокол, протокол является действительным и у протокола отсутсвует номер или есть номер но протокол разблокирован, и к протоколу прикреплены пробы">
                                                            <svg class="icon icon-disabled" width="35" height="35">
                                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#edit" />
                                                            </svg>
                                                        </span>
                                                    <?php else: ?>
                                                        <button type="button"
                                                            class="btn bg-transparent border-0 mt-0 p-0 protocol-information"
                                                            data-protocol="<?= $val['ID'] ?>" title="Информация по протоколу">
                                                            <svg class="icon" width="35" height="35">
                                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#edit" />
                                                            </svg>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($val['PROTOCOL_OUTSIDE_LIS'])): ?>
                                                        <svg class="icon" width="30" height="30">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#check-circle" />
                                                        </svg>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <!--                                                --><?php //if ( !empty($val['PROTOCOL_OUTSIDE_LIS'])): 
                                                                                                            ?>
                                                    <?php if (!empty($val['file']['file'])): ?>
                                                        <form class="form form-upload-pdf" method="post"
                                                            action="<?= URI ?>/result/deletePdf/<?= $val['ID'] ?>"
                                                            enctype="multipart/form-data">
                                                            <?php if (!empty($this->data['deal_id'])): ?>
                                                                <input class="deal-id" type="hidden" name="deal_id"
                                                                    value="<?= $this->data['deal_id'] ?>">
                                                            <?php endif; ?>

                                                            <?php if (!empty($this->data['tz_id'])): ?>
                                                                <input class="tz-id" type="hidden" name="tz_id"
                                                                    value="<?= $this->data['tz_id'] ?>">
                                                            <?php endif; ?>

                                                            <?php if (!empty($val['file']['file'])): ?>
                                                                <input class="file" type="hidden" name="file"
                                                                    value="<?= $val['file']['file'] ?>">
                                                            <?php endif; ?>

                                                            <?php if (!empty($this->data['selected'])): ?>
                                                                <input class="selected" type="hidden" name="selected" value="selected">
                                                            <?php endif; ?>

                                                            <div class="position-relative d-inline-block">
                                                                <a href="<?= $val['file']['dir'] ?><?= $val['file']['file'] ?>"
                                                                    target="_blank"
                                                                    title="<?= $val['file']['file'] ?>">
                                                                    <svg class="icon" width="30" height="30">
                                                                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#pdf_file" />
                                                                    </svg>
                                                                </a>
                                                                <?php if (!empty($val['PDF'])): ?>
                                                                    <button type="submit"
                                                                        class="button-del-file button-close button-outline"
                                                                        name="delete_pdf"
                                                                        title="Удалить pdf файл"></button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </form>
                                                    <?php else: ?>
                                                        <form class="form form-upload-pdf" method="post"
                                                            action="<?= URI ?>/result/uploadPdf/<?= $val['ID'] ?>"
                                                            enctype="multipart/form-data">
                                                            <?php if (!empty($this->data['deal_id'])): ?>
                                                                <input class="deal-id" type="hidden" name="deal_id"
                                                                    value="<?= $this->data['deal_id'] ?>">
                                                            <?php endif; ?>

                                                            <?php if (!empty($this->data['tz_id'])): ?>
                                                                <input class="tz-id" type="hidden" name="tz_id"
                                                                    value="<?= $this->data['tz_id'] ?>">
                                                            <?php endif; ?>

                                                            <?php if (!empty($this->data['selected'])): ?>
                                                                <input class="selected" type="hidden" name="selected" value="selected">
                                                            <?php endif; ?>

                                                            <label class="upload-pdf cursor-pointer"
                                                                title="Загрузить PDF-версию">
                                                                <svg class="icon" width="30" height="30">
                                                                    <use xlink:href="<?= URI ?>/assets/images/icons.svg#upload" />
                                                                </svg>
                                                                <input class="d-none" id="uploadPdf" type="file"
                                                                    name="upload_pdf" onchange="form.submit()">
                                                            </label>
                                                        </form>
                                                    <?php endif; ?>
                                                    <!--                                                --><?php //else: 
                                                                                                            ?>
                                                    <!--                                                    <span title="Для загрузки PDF-версии, в протоколе отмете 'Протокол выдается вне ЛИС'">-->
                                                    <!--                                                        <svg class="icon icon-disabled" width="30" height="30">-->
                                                    <!--                                                            <use xlink:href="--><? //= URI 
                                                                                                                                        ?><!--/assets/images/icons.svg#upload"/>-->
                                                    <!--                                                        </svg>-->
                                                    <!--                                                    </span>-->
                                                    <!--                                                --><?php //endif; 
                                                                                                            ?>
                                                </td>
                                                <td>
                                                    <?php if ($val['is_create_protocol']): ?>
                                                        <span title="Для формирования протокола проверте выбран ли протокол и является ли протокол действительным, выбраны ли пробы для протокола. <?= $val['probe_count'] ? '' : 'Внимание! У протокола отсутвую прикреплённые пробы' ?>">
                                                            <svg class="icon icon-disabled" width="35" height="35">
                                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#form" />
                                                            </svg>
                                                        </span>
                                                    <?php else: ?>
                                                        <a class="no-decoration me-1 <?= $val['validation_class'] ?>"
                                                            data-protocol_id="<?= $val['ID'] ?>"
                                                            href="/ulab/generator/ProtocolDocument/<?= $val['ID'] ?>"
                                                            title="Сформировать">
                                                            <svg class="icon" width="35" height="35">
                                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#form" />
                                                            </svg>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($val['doc_send']): ?>
                                                        <span title="Для скачивания протокола проверте был ли сформирован протокол, является ли протокол действительным и не является протоколом выданным в не ЛИС. <?= !empty($val['file']['file']) ? 'Внимание! Файл для скачивания отсутствует.' : '' ?>">
                                                            <svg class="icon icon-disabled" width="30" height="30">
                                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-send" />
                                                            </svg>
                                                        </span>
                                                    <?php else: ?>
                                                        <a class="doc-send"
                                                            href="<?= $val['file']['dir'] ?><?= $val['file']['file'] ?>"
                                                            title="<?= $val['file']['file'] ?>" download>
                                                            <svg class="icon" width="30" height="30">
                                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-send" />
                                                            </svg>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($val['add_protocol_number']): ?>
                                                        <button type="button"
                                                            class="btn border-light-gray bg-light-gray text-nowrap mt-0 text-white"
                                                            title="Для присвоения номера протоколу проверте не присвоин ли ему уже номер, выбран ли протокол и является ли протокол действительным, сохранена дата начала и окончания испытания, сформирован ли протокол если он не выдавался в не ЛИС, выбраны ли пробы для протокола. <?= $val['probe_count'] ? '' : 'Внимание! У протокола отсутвую прикреплённые пробы' ?>">
                                                            Присвоить номер
                                                        </button>
                                                    <?php else: ?>
                                                        <form class="form form-create-protocol" method="post"
                                                            action="<?= URI ?>/result/addProtocolNumber/<?= $val['ID'] ?>">
                                                            <?php if (!empty($this->data['deal_id'])): ?>
                                                                <input class="deal-id" type="hidden" name="deal_id"
                                                                    value="<?= $this->data['deal_id'] ?>">
                                                            <?php endif; ?>

                                                            <?php if (!empty($this->data['tz_id'])): ?>
                                                                <input class="tz-id" type="hidden" name="tz_id"
                                                                    value="<?= $this->data['tz_id'] ?>">
                                                            <?php endif; ?>

                                                            <?php if (!empty($this->data['selected_protocol'])): ?>
                                                                <input class="selected-protocol-id" type="hidden"
                                                                    name="selected_protocol"
                                                                    value="<?= $this->data['selected_protocol'] ?>">
                                                            <?php endif; ?>

                                                            <?php if (!empty($this->data['selected'])): ?>
                                                                <input class="selected" type="hidden" name="selected" value="selected">
                                                            <?php endif; ?>

                                                            <button type="submit"
                                                                class="btn btn-primary add-protocol-number text-nowrap mt-0 <?= $val['validation_class'] ?>"
                                                                data-protocol_id="<?= $val['ID'] ?>"
                                                                name="add_protocol_number">
                                                                Присвоить номер
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($val['delete_protocol']): ?>
                                                        <button class="btn border-light-gray bg-light-gray mt-0 fill-white btn-square"
                                                            type="button" title="Для удаления протокола проверте выбран ли протокол, протокол является действительным и у протокола отсутсвует номер">
                                                            <i class="fa-solid fa-minus icon-fix"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <form class="form form-create-protocol" method="post"
                                                            action="<?= URI ?>/result/deleteProtocol/<?= $val['ID'] ?>">
                                                            <?php if (!empty($this->data['deal_id'])): ?>
                                                                <input class="deal-id" type="hidden" name="deal_id"
                                                                    value="<?= $this->data['deal_id'] ?>">
                                                            <?php endif; ?>

                                                            <?php if (!empty($this->data['tz_id'])): ?>
                                                                <input class="tz-id" type="hidden" name="tz_id"
                                                                    value="<?= $this->data['tz_id'] ?>">
                                                            <?php endif; ?>
                                                            <button class="btn btn-danger mt-0 delete-protocol btn-square"
                                                                type="submit"
                                                                name="delete_protocol[<?= $val['ID'] ?>]">
                                                                <i class="fa-solid fa-minus icon-fix"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                                <?php if ($this->data['is_may_unlock']): ?>
                                                    <td>
                                                        <?php if ($val['edit_results']): ?>
                                                            <span title="Для разблокировки протокола проверте выбран ли протокол и является ли протокол действительным">
                                                                <label class="switch d-inline-block <?= $val['EDIT_RESULTS'] ? 'checkbox-disabled' : '' ?>">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        <?= $val['EDIT_RESULTS'] ? 'checked' : '' ?> disabled>
                                                                    <span class="slider pe-none <?= $val['EDIT_RESULTS'] ? '' : 'bg-light-gray' ?>"></span>
                                                                </label>
                                                            </span>
                                                        <?php else: ?>
                                                            <form class="form form-create-protocol" method="post"
                                                                action="<?= URI ?>/result/editResults/<?= $val['ID'] ?>">
                                                                <?php if (!empty($this->data['deal_id'])): ?>
                                                                    <input class="deal-id" type="hidden" name="deal_id"
                                                                        value="<?= $this->data['deal_id'] ?>">
                                                                <?php endif; ?>

                                                                <?php if (!empty($this->data['tz_id'])): ?>
                                                                    <input class="tz-id" type="hidden" name="tz_id"
                                                                        value="<?= $this->data['tz_id'] ?>">
                                                                <?php endif; ?>

                                                                <?php if (!empty($this->data['selected'])): ?>
                                                                    <input class="selected" type="hidden" name="selected" value="selected">
                                                                <?php endif; ?>

                                                                <label class="switch d-inline-block">
                                                                    <input class="form-check-input edit-results"
                                                                        name="edit_results"
                                                                        type="checkbox"
                                                                        <?= $val['EDIT_RESULTS'] ? 'checked' : '' ?>
                                                                        onchange="form.submit()">
                                                                    <span class="slider"></span>
                                                                </label>
                                                            </form>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endif; ?>
                                                <td>
                                                    <?php if ($this->data['is_may_invalid']): ?>
                                                        <?php if ($val['protocol_is_invalid']): ?>
                                                            <span title="Для признания протокола недействительным проверте выбран ли протокол, является ли протокол действительным и есть номер у протокола">
                                                                <label class="switch d-inline-block <?= $val['INVALID'] ? 'checkbox-disabled' : '' ?>">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        <?= $val['INVALID'] ? 'checked' : '' ?> disabled>
                                                                    <span class="slider pe-none <?= $val['INVALID'] ? '' : 'bg-light-gray' ?>"></span>
                                                                </label>
                                                            </span>
                                                        <?php else: ?>
                                                            <form class="form form-create-protocol" method="post"
                                                                action="<?= URI ?>/result/protocolIsInvalid/<?= $val['ID'] ?>">
                                                                <?php if (!empty($this->data['deal_id'])): ?>
                                                                    <input class="deal-id" type="hidden" name="deal_id"
                                                                        value="<?= $this->data['deal_id'] ?>">
                                                                <?php endif; ?>

                                                                <?php if (!empty($this->data['tz_id'])): ?>
                                                                    <input class="tz-id" type="hidden" name="tz_id"
                                                                        value="<?= $this->data['tz_id'] ?>">
                                                                <?php endif; ?>
                                                                <label class="switch d-inline-block <?= $val['INVALID'] ? 'checkbox-disabled' : '' ?>">
                                                                    <input class="form-check-input protocol-is-invalid"
                                                                        name="protocol_is_invalid"
                                                                        type="checkbox"
                                                                        <?= $val['INVALID'] ? 'checked' : '' ?>>
                                                                    <span class="slider"></span>
                                                                </label>
                                                            </form>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <div title="Признать протокол недействительным могут Администраторы и Руководители ИЦ">
                                                            <?= $val['INVALID'] == 1 ? 'Да' : 'Нет' ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!--./protocols-wrapper-->

                    <form class="form form-create-protocol" method="post" action="<?= URI ?>/result/createProtocol/">
                        <?php if (!empty($this->data['deal_id'])): ?>
                            <input class="deal-id" type="hidden" name="deal_id" value="<?= $this->data['deal_id'] ?>">
                        <?php endif; ?>

                        <?php if (!empty($this->data['tz_id'])): ?>
                            <input class="tz-id" type="hidden" name="tz_id" value="<?= $this->data['tz_id'] ?>">
                        <?php endif; ?>
                        <?php if (empty($this->data['disabledGen'])): ?>
                            <div class="row protocol-button-wrapper">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary btn-create-protocol"
                                        name="btn_create_protocol">
                                        Создать протокол
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row protocol-button-wrapper">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary btn-create-protocol"
                                        name="btn_create_protocol" disabled>
                                        Создать протокол
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </form>
                    <!--./form-create-protocol-->
                </div>
                <!--./panel-body-->
            </div>
        </div>
    </div>


    <form class="form form-result" id="formResult" method="post" action="<?= URI ?>/result/updateResult/">
        <?php if (!empty($this->data['requirement']['tz_id'])): ?>
            <input class="tz-id" type="hidden" name="tz_id" value="<?= $this->data['requirement']['tz_id'] ?>">
        <?php endif; ?>

        <?php if (!empty($this->data['deal_id'])): ?>
            <input class="deal-id" type="hidden" name="deal_id" value="<?= $this->data['deal_id'] ?>">
        <?php endif; ?>

        <?php if (!empty($this->data['selected_protocol'])): ?>
            <input class="protocol-id" type="hidden" name="protocol_id"
                value="<?= $this->data['selected_protocol'] ?: '' ?>">
        <?php endif; ?>

        <?php if (!empty($this->data['selected'])): ?>
            <input class="selected" type="hidden" name="selected" value="selected">
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <header class="panel-heading">
                        Таблица результатов испытаний
                        <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="#" class="fa fa-chevron-up"></a>
                        </span>
                    </header>
                    <div class="panel-body position-relative">
                        <input type="hidden" id="deal_id" value="<?= $this->data['deal_id'] ?>">

                        <div class="row flex-nowrap">

                            <div class="col-auto">
                                <div class="js-sticky-widget2">
                                    <div class="col-auto mb-3">
                                        <a href="#" class="btn-flex btn_start btn btn-success w125 btn-sm disabled" title="Начать/возобновить испытание">
                                            <i class="fa-solid fa-play"></i> <span>Старт</span>
                                        </a>
                                    </div>

                                    <div class="col-auto mb-3">
                                        <a href="#" class="btn-flex btn_pause btn btn-success w125 btn-sm disabled" title="Приостановить испытание">
                                            <i class="fa-solid fa-pause"></i> <span>Пауза</span>
                                        </a>
                                    </div>

                                    <div class="col-auto mb-3">
                                        <a href="#" class="btn-flex btn_stop btn btn-success w125 btn-sm disabled" title="Завершить испытание">
                                            <i class="fa-solid fa-stop"></i> <span>Завершить</span>
                                        </a>
                                    </div>

                                    <div class="col-auto mb-3 d-none">
                                        <a href="#" class="btn-flex btn_create_protocol btn btn-primary w125 btn-sm disabled" title="Создать протокол">
                                            <i class="fa-solid fa-plus icon-fix"></i> <span>Протокол</span>
                                        </a>
                                    </div>

                                    <div class="col-auto mb-3">
                                        <a href="#" class="btn-flex btn_group_measurement_sheet btn btn-primary w125 btn-sm disabled" title="Лист измерений">
                                            <i class="fa-solid fa-calculator"></i> <span>Лист измерений</span>
                                        </a>
                                    </div>

                                    <div class="col-auto mb-3">
                                        <a href="#unbound-protocol-form" class="btn-flex btn_unbound_protocol popup-with-form btn btn-primary w125 btn-sm disabled" title="Отвязать протокол">
                                            <i class="fa-regular fa-file-excel"></i> <span>Отвязать протокол</span>
                                        </a>
                                    </div>

                                    <div class="col-auto mb-3">
                                        <button type="submit" class="btn btn-primary w125 btn-sm" title="Сохранить результаты">Сохранить</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="row mb-2">
                                    <div class="col text-center">
                                        Испытания
                                    </div>
                                    <div class="col text-center">
                                        Объекты испытаний
                                    </div>
                                    <div class="col text-center">
                                        Пробы
                                    </div>
                                    <div class="col text-center">
                                        Лаборатория
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">Показать: </span>
                                            <select id="filter-methods" class="form-control select2 filter" multiple>
                                                <?php foreach ($this->data['tz_methods_list'] as $item): ?>
                                                    <option value="<?= $item['id'] ?>"><?= $item['view_gost'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <select id="filter-material" class="form-control select2 filter" multiple>
                                            <?php foreach ($this->data['tz_material_list'] as $item): ?>
                                                <option value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select id="filter-probe" class="form-control select2 filter" multiple>
                                            <?php $mid = 0;
                                            foreach ($this->data['tz_probe_list'] as $item): ?>
                                                <?php if ($mid !== $item['material_id']): $mid = $item['material_id']; ?>
                                                    <option disabled><b><?= $item['material_name'] ?></b></option>
                                                <?php endif; ?>
                                                <option value="<?= $item['id'] ?>"><?= $item['cipher'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select id="filter-lab" class="form-control select2 filter" multiple>
                                            <?php foreach ($this->data['tz_lab_list'] as $item): ?>
                                                <option value="<?= $item['ID'] ?>"><?= $item['short_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <table class="table -table-striped journal table-hover table-sm table-light row-border" id="journal_methods" style="font-size: 14px">
                                    <thead>
                                        <tr class="table-secondary align-middle">
                                            <th scope="col">Методика</th>
                                            <th scope="col" class="text-nowrap">
                                                <label class=""><input class="form-check-input all-check" type="checkbox"> Всё</label>
                                            </th>
                                            <th scope="col">Материал</th>
                                            <th scope="col">Проба</th>
                                            <th scope="col">Испытание</th>
                                            <th scope="col">Лист<br> измерения</th>
                                            <th scope="col">Ед. изм.</th>
                                            <th scope="col">ТУ</th>
                                            <th scope="col">Нормативное значение</th>
                                            <th scope="col">Фактическое значение</th>
                                            <th scope="col">Примечание</th>
                                            <th scope="col">Соответствие требованиям</th>
                                            <th scope="col">В ОА</th>
                                            <th scope="col">Номер протокола</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <!--./panel-body-->
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <button type="submit" class="btn btn-primary save" form="formResult" name="save">
                    Сохранить
                </button>
            </div>
        </div>
    </form>
    <!--./form-result-->

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->

    <form id="measurementModalForm" class="bg-light mfp-hide col-md-10 m-auto p-3 position-relative"
        action="<?= URI ?>/result/saveMeasurementData/" method="post">

        <input type="hidden" name="deal_id" value="<?= $this->data['deal_id'] ?>">
        <input type="hidden" name="direct" value="card_oati">

        <div class="measurement_content">

        </div>
    </form>
    <!-- ./ugtpDescriptionModalForm   -->
    <form id="descriptionModalForm" class="bg-light mfp-hide col-md-10 m-auto p-3 position-relative"
        action="<?= URI ?>/result/saveDescription/" method="post">

        <input type="hidden" name="ugtp_id" id="description_ugtp_id">
        <input type="hidden" name="deal_id" value="<?= $this->data['deal_id'] ?>">

        <lable for="description_text">Примечание</lable>
        <input type="text" class="form-control" id="description_text" name="description">

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
    <!--./averageModalForm-->

    <form id="roomsModalForm" class="bg-light mfp-hide col-md-6 m-auto p-3 position-relative"
        action="<?= URI ?>/result/index/" method="post">
        <div class="title mb-3 h-2"></div>

        <div class="line-dashed-small"></div>
    </form>
    <!--./roomsModalForm-->

    <form id="protocolInformation" class="bg-light mfp-hide col-md-10 m-auto p-3 position-relative"
        action="/ulab/result/updateProtocol/" method="post">
        <div class="title mb-3 h-2"></div>
        <div class="line-dashed-small"></div>

        <div class="information-wrapper mx-3">
            <div class="row mb-3">
                <div class="col bg-white border border-light-gray px-3 py-3 me-3">
                    <strong class="d-block mb-3">Общая информация</strong>

                    <div class="form-group row">
                        <div class="col">
                            <label for="protocolType">Тип протокола</label>
                            <select class="form-select w-100 protocol-template d-none" name="protocol[id_template]">
                                <?php foreach ($this->data['template_list'] as $item): ?>
                                    <option <?= $this->data['protocol']['id_template'] === $item['id'] ? 'selected' : '' ?>
                                        value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select protocol-type w-100" name="protocol[PROTOCOL_TYPE]">
                                <!-- <option value='0'>Стандартный</option>
                                <option value="1">Стандартный с ЭЦП</option>
                                <option value='33'>Упрощенный</option>
                                <option value="43">Форма заказчика</option>
                                <option value="44">Форма заказчика с ЭЦП</option>
                                <option value="3">Стандартный НК схема "B"</option>
                                <option value="60">Форма протокола Стандартный НК схема В ЭЦП</option>
                                <option value="4">Стандартный НК "B" без схемы участков</option>
                                <option value="5">Стандартный НК схема "Г"</option>
                                <option value="55">Упрощенный ОСК-1 (ГОСТ 10181 п.4.2.)</option>
                                <option value="56">Упрощенный ОСК-2 (ГОСТ 10180 п.7.2.)</option>
                                <option value="57">Упрощенный ОСК-1 (ЭЦП)</option>
                                <option value="58">Упрощенный ОСК-2 (ЭЦП)</option>
                                <option value="2" <?= $_SESSION['SESS_AUTH']['USER_ID'] == 61 ? '' : 'class="d-none"' ?>>Тестовый! Не нажимать</option> -->
                                <option value="62">Протокол ILAC</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label for="verify">Подпись в протоколе</label>
                            <select class="form-select verify w-100 select2" name="protocol[VERIFY][]" multiple="multiple"></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6">
                            <label for="user-create-protocol">Протокол оформил</label>
                            <select class="form-select user-create-protocol select2" name="protocol[user_create_protocol]"></select>
                        </div>
                        <div class="col-6 norm-control" style="display: none">
                            <label for="user-norm-protocol">Нормоконтроль</label>
                            <select class="form-select user-norm-protocol select2" name="protocol[user_norm_protocol]"></select>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <label class="switch">
                                    <input class="form-check-input no-evaluate" name="protocol[NO_COMPLIANCE]"
                                        type="checkbox" value="1">
                                    <span class="slider"></span>
                                </label>
                                <div class="ms-2">Не оцен. на соотв. нормам</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col bg-white border border-light-gray px-3 py-3">
                    <strong class="d-block mb-3">Информация об испытаниях</strong>

                    <div class="form-group row">
                        <div class="col">
                            <lable for="dateBegin">Дата начала</lable>
                            <input type="date" class="form-control date-begin <?= $this->data['is_check'] ? 'date-trials' : '' ?>"
                                name="protocol[DATE_BEGIN]" value=""
                                title="Редактирование доступно Администраторам и Руководителям ИЦ">
                        </div>
                        <div class="col">
                            <lable for="dateEnd">Дата окончания</lable>
                            <input type="date" class="form-control date-end <?= $this->data['is_check'] ? 'date-trials' : '' ?>"
                                name="protocol[DATE_END]" value=""
                                title="Редактирование доступно Администраторам и Руководителям ИЦ">
                        </div>
                    </div>
                    <div class="form-group row conditions-wrapper">
                        <div class="col">
                            <lable>Температура</lable>
                            <div class="row">
                                <div class="col">
                                    <input type="number" class="form-control condition_input w-100 temp1"
                                        name="protocol[TEMP_O]" step="any" placeholder="От" value=""
                                        title="Редактирование доступно Администраторам и Руководителям ИЦ">
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control condition_input w-100 temp2"
                                        name="protocol[TEMP_TO_O]" step="any" placeholder="До" value=""
                                        title="Редактирование доступно Администраторам и Руководителям ИЦ">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <lable>Влажность</lable>
                            <div class="row">
                                <div class="col">
                                    <input type="number" class="form-control condition_input w-100 wet1"
                                        name="protocol[VLAG_O]" step="any" placeholder="От" value=""
                                        title="Редактирование доступно Администраторам и Руководителям ИЦ">
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control condition_input w-100 wet2"
                                        name="protocol[VLAG_TO_O]" step="any" placeholder="До" value=""
                                        title="Редактирование доступно Администраторам и Руководителям ИЦ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($this->data['may_edit_conditions']): ?>
                        <div class="form-group row">
                            <div class="col mb-0">
                                <div>Изменить дату испытаний</div>
                                <div class="d-flex align-items-center">
                                    <label class="switch mt-2">
                                        <input class="form-check-input change-trials-date"
                                            name="protocol[CHANGE_TRIALS_DATE]" type="checkbox" value="1">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col mb-0">
                                <div>Изменить условия испытаний</div>
                                <div class="d-flex align-items-center">
                                    <label class="switch mt-2">
                                        <input class="form-check-input change-trials-conditions"
                                            name="protocol[CHANGE_TRIALS_CONDITIONS]" type="checkbox" value="1">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="line-dashed"></div>
                    <div class="form-group row mb-0">
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <label class="switch">
                                    <input class="form-check-input output-in-protocol"
                                        name="protocol[OUTPUT_IN_PROTOCOL]" type="checkbox" value="1">
                                    <span class="slider"></span>
                                </label>
                                <div class="ms-2">Вывод условий в протокол</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col bg-white border border-light-gray px-3 py-3 me-3 js-equipment-info">
                    <strong class="d-block mb-3">Информация об оборудовании</strong>

                    <div class="row align-items-end min-h-180">
                        <div class="form-group col">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Используемое оборудование</span>
                                <button type="button" class="bg-transparent border-0 revert-default">
                                    <ins>Вернуть по умолчанию</ins>
                                </button>
                            </div>
                            <div class="d-flex">
                                <select class="form-select min-h-250 equipment-used" name="equipment_used" multiple="multiple" style="width: 100%;">

                                </select>
                                <div class="equipment-used-remove-list" style="display:none; padding-top: 7px;"></div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="equipmentIds" name="oborud[equipment_ids]" value="">
                    <?php if (in_array($_SESSION['SESS_AUTH']['USER_ID'], [61, 10, 17, 222, 227, 115])): ?>
                        <div class="row">
                            <div class="form-group col">
                                <select class="form-select equipment select2" id="equipment" name="">
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="certs-wrapper col bg-white border border-light-gray px-3 py-3" style="display: none;">
                        <div class="row align-items-end">
                            <div class="form-group col">
                                <div id="equipmentList">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col bg-white border border-light-gray px-3 py-3">
                    <strong class="d-block mb-3">Данные объекта испытаний</strong>

                    <div class="row">
                        <div class="form-group col">
                            <lable for="objectDescription">Описание объекта</lable>
                            <lable class="osk2" style="margin-left: 20px; display: none">Возраст</lable>
                            <div class="d-flex">
                                <div class="form-check osk1" style="display: none;">
                                    <input class="form-check-input" type="radio" name="objectProbePreset"
                                        data-full="БСТ В45 П4 F(1)200 W12 ГОСТ 7473-2010.\nПроизводитель: ООО «БЕТОТЕК БЕТОН».\nДокумент о качестве бетонной смеси №... от ...">
                                    <lable class="form-check-label" for="op1">П4</lable>
                                </div>
                                <div class="form-check osk1" style="margin-left: 10px; display: none;">
                                    <input class="form-check-input" type="radio" name="objectProbePreset"
                                        data-full="БСТ В45 П3 F(1)200 W12 ГОСТ 7473-2010.\nПроизводитель: ООО «БЕТОТЕК БЕТОН».\nДокумент о качестве бетонной смеси №... от ...">
                                    <lable class="form-check-label" for="op2">П3</lable>
                                </div>
                                <div class="form-check osk2" style="display: none;">
                                    <input class="form-check-input" type="radio" name="descObjectProbePreset"
                                        data-full="Бетонные образцы-кубы с размером ребра 100 мм в количестве двух штук.\nПроектный класс бетона по прочности на сжатие – В45.\nТребуемая прочность бетона на сжатие в проектном возрасте – ... МПа.\nДокументы о качестве бетонной смеси, партии № от ... ООО &quot;БЕТОТЕК БЕТОН&quot;\nВозраст образцов-кубов – 7/28 сутки. Акт отбора проб (образцов) б/н от ...">
                                    <lable class="form-check-label">2</lable>
                                </div>
                                <div class="form-check osk2" style="margin-left: 10px; display: none;">
                                    <input class="form-check-input" type="radio" name="descObjectProbePreset"
                                        data-full="Бетонные образцы-кубы с размером ребра 100 мм в количестве четырех штук.\nПроектный класс бетона по прочности на сжатие – В45.\nТребуемая прочность бетона на сжатие в проектном возрасте – ... МПа.\nДокументы о качестве бетонной смеси, партии № от ... ООО &quot;БЕТОТЕК БЕТОН&quot;\nВозраст образцов-кубов – 7/28 сутки. Акт отбора проб (образцов) б/н от ...">
                                    <lable class="form-check-label">4</lable>
                                </div>
                                <div class="form-check osk2" style="margin-left: 87px; display: none;">
                                    <input class="form-check-input" type="radio" name="dayObjectProbePreset"
                                        data-full="7">
                                    <lable class="form-check-label">7</lable>
                                </div>
                                <div class="form-check osk2" style="margin-left: 10px; display: none;">
                                    <input class="form-check-input" type="radio" name="dayObjectProbePreset"
                                        data-full="28">
                                    <lable class="form-check-label">28</lable>
                                </div>
                            </div>
                            <textarea class="form-control mw-100 object-description"
                                id="objectDescription" name="protocol[DESCRIPTION]"></textarea>
                        </div>
                        <div class="form-group col">
                            <lable for="object">Объект строительства</lable>
                            <textarea class="form-control mw-100 object" id="object" name="protocol[OBJECT]"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <lable for="placeProbe">Место отбора проб</lable>
                            <div class="d-flex">
                                <div class="form-check osk1 osk2 osk12" style="display: none;">
                                    <input class="form-check-input" type="radio" name="placeProbePreset"
                                        data-full="Промзона, ул. Монтажников 11.\nБлоки левых колец обделки AL, BL, CL, DL, EL, FL, KL">
                                    <lable class="form-check-label">Левые кольца</lable>
                                </div>
                                <div class="form-check ml-2 osk1 osk2 osk12" style="margin-left: 10px; display: none;">
                                    <input class="form-check-input" type="radio" name="placeProbePreset"
                                        data-full="Промзона, ул. Монтажников 11.\nБлоки правых колец обделки AR, BR, CR, DR, ER, FR, KR ...">
                                    <lable class="form-check-label">Правые кольца</lable>
                                </div>
                            </div>
                            <textarea class="form-control mw-100 place-probe"
                                id="placeProbe" name="protocol[PLACE_PROBE]"></textarea>
                        </div>
                        <div class="form-group col">
                            <lable for="dateProbe">Дата отбора проб</lable>
                            <input type="date" class="form-control date-probe" name="protocol[DATE_PROBE]" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col osk1" style="display: none;">
                            <lable for="osk1Name">Наименование показателя</lable>
                            <div class="d-flex">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="OSK1NamePreset"
                                        data-full="П2">
                                    <lable class="form-check-label">П2</lable>
                                </div>
                                <div class="form-check" style="margin-left: 10px;">
                                    <input class="form-check-input" type="radio" name="OSK1NamePreset"
                                        data-full="П3">
                                    <lable class="form-check-label">П3</lable>
                                </div>
                                <div class="form-check" style="margin-left: 10px;">
                                    <input class="form-check-input" type="radio" name="OSK1NamePreset"
                                        data-full="П4">
                                    <lable class="form-check-label">П4</lable>
                                </div>
                                <div class="form-check" style="margin-left: 10px;">
                                    <input class="form-check-input" type="radio" name="OSK1NamePreset"
                                        data-full="П5">
                                    <lable class="form-check-label">П5</lable>
                                </div>
                            </div>
                            <input type="text" class="form-control osk-1-name" id="osk1Name"
                                name="protocol[osk_1_name]">
                        </div>
                        <div class="form-group col osk1" style="display: none;">
                            <lable for="osk1Norm">Нормативное значение показателя</lable>
                            <div class="d-flex">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="OSK1NormPreset"
                                        data-full="(5-9) ±2">
                                    <lable class="form-check-label">(5-9) ±2</lable>
                                </div>
                                <div class="form-check" style="margin-left: 10px;">
                                    <input class="form-check-input" type="radio" name="OSK1NormPreset"
                                        data-full="(10-15) ±2">
                                    <lable class="form-check-label">(10-15) ±2</lable>
                                </div>
                                <div class="form-check" style="margin-left: 10px;">
                                    <input class="form-check-input" type="radio" name="OSK1NormPreset"
                                        data-full="(16-20) ±2">
                                    <lable class="form-check-label">(16-20) ±2</lable>
                                </div>
                                <div class="form-check" style="margin-left: 10px;">
                                    <input class="form-check-input" type="radio" name="OSK1NormPreset"
                                        data-full="(более 20) ±2">
                                    <lable class="form-check-label">(более 20) ±2</lable>
                                </div>
                            </div>
                            <input type="text" class="form-control osk-1-norm" id="osk1Norm"
                                name="protocol[osk_1_norm]">
                        </div>
                        <div class="form-group col osk2" style="display: none;">
                            <lable>Нормативное значение показателя</lable>
                            <input type="number" step="0.1" class="form-control osk-2-norm" id="osk2Norm"
                                name="protocol[osk_2_norm]">
                        </div>
                        <div class="form-group col osk2" style="display: none;">
                            <lable>Фактическое значение показателя</lable>
                            <input type="number" step="0.1" class="form-control osk-2-fact" id="osk2Fact"
                                name="protocol[osk_2_fact]">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col osk1" style="display: none;">
                            <lable for="additionalInformation">Место проведения испытаний</lable>
                            <textarea class="form-control mw-100 osk-1-place"
                                id="osk1Place" name="protocol[osk_1_place]"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <lable for="additionalInformation">Дополнительная информация</lable>
                            <textarea class="form-control mw-100 additional-information"
                                id="additionalInformation" name="protocol[DOP_INFO]"></textarea>
                        </div>
                    </div>
                    <?php if ($this->data['dopinfo2']): ?>
                        <div class="row">
                            <div class="form-group col">
                                <lable for="additionalInformation">Дополнительная информация 2</lable>
                                <textarea class="form-control mw-100 additional-information2"
                                    id="additionalInformation2" name="protocol[DOP_INFO2]"></textarea>
                            </div>
                        </div>
                    <?php endif; ?>

                    <strong class="d-block mb-3">Дополнительная информация</strong>

                    <div class="row">
                        <div class="col">
                            <div>Протокол выдается вне ЛИС</div>
                            <label class="switch">
                                <input class="form-check-input protocol-outside-lis"
                                    name="protocol[PROTOCOL_OUTSIDE_LIS]" type="checkbox" value="1">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="col">
                            <div>C аттестатом аккредитации</div>
                            <?php if (in_array($this->data['user_id'], [10, 227])): ?>
                                <label class="switch">
                                    <input class="form-check-input attestat-in-protocol"
                                        name="protocol[ATTESTAT_IN_PROTOCOL]" type="checkbox" value="1">
                                    <span class="slider"></span>
                                </label>
                            <?php else: ?>
                                <label class="switch disabled" title="Нет доступа к изменению">
                                    <input class="form-check-input attestat-in-protocol"
                                        name="protocol[ATTESTAT_IN_PROTOCOL]" type="checkbox" value="1" disabled>
                                    <span class="slider"></span>
                                </label>
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <div>Не выводить автоматические примечания</div>
                            <label class="switch">
                                <input class="form-check-input automatic-comments"
                                    name="protocol[automatic_comments]" type="checkbox" value="1">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div>Определяемые характеристики</div>
                            <label class="switch">
                                <input class="form-check-input determined-characteristics"
                                    name="protocol[determined_characteristics]" type="checkbox" value="1">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input name="protocol_id" value="" type="hidden">
        <input name="selected" value="" type="hidden">
        <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
        <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
    <!--./protocolInformation-->


    <form id="gost_room_form" action="/ulab/result/saveRoomStart/" method="post" class="bg-light mfp-hide col-md-5 m-auto p-3 position-relative">
        <div class="title mb-3 h-2">
            Помещение
        </div>

        <div class="line-dashed-small"></div>

        <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">

        <div class="gost_room_container">

        </div>

        <button type="submit" class="btn btn-primary">Старт</button>
    </form>


    <form id="unbound-protocol-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" method="post" action="<?= URI ?>/result/unboundProtocol">
        <div class="title mb-3 h-2">
            Отвязать пробы от протокола
        </div>

        <div class="line-dashed-small"></div>

        <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
        <input class="probe-id-list" name="probe_id_list" value="" type="hidden">

        <div class="row mb-3">
            <label for="inputEmail3" class="col col-form-label">Выбрано проб: <span class="count-selected-probe"></span></label>
        </div>

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary">Отвязать</button>
    </form>
</div>
<!--./wrapper-card-->