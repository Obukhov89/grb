<div style="display: none;">
    <pre>
        <?print_r($this->data['protocol'])?>
    </pre>
</div>

<div class="wrapper-card m-auto">
    <header class="header-result mb-3">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-2">
                    <a class="nav-link link-back disabled"
                       href="<?= URI ?>/request/card/<?= $this->data['deal_id'] ?? '' ?>"
                       title="Вернуться назад">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#back"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-list" href="<?= URI ?>/request/list/" title="Вернуться к списку">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#list"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-card" href="<?= URI ?>/request/card/<?= $this->data['deal_id'] ?? '' ?>"
                       title="Карточка заявки">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#card"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2 disabled">
                    <a class="nav-link link-doc-send disabled" href="#">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-send"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-docs disabled" href="#">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#docs"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link link-doc-edit disabled" href="#">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-edit"/>
                        </svg>
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
                        <?php if (!empty($this->data['protocols'])): ?>
                            <div class="table-responsive mb-2">
                                <table class="table text-center table-hover align-middle">
                                    <thead>
                                    <tr class="table-secondary align-middle">
                                        <th class="border-0">Номер протокола</th>
                                        <th class="border-0">Дата протокола</th>
                                        <th class="border-0">Вне ЛИС</th>
                                        <th class="border-0">PDF-версия</th>
                                        <th class="border-0">Сформировать протокол</th>
                                        <th class="border-0">Скачать протокол</th>
                                        <th class="border-0">Скачать без объед. ячеек</th>
                                        <th class="border-0">Присвоить номер</th>
                                        <th class="border-0">Удалить протокол</th>
                                        <th class="border-0">Разблокировать</th>
                                        <th class="border-0">Протокол недействителен</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($this->data['protocols'] as $val): ?>
                                        <tr class="<?= $val['table_green'] ?>">
                                            <td>
                                                <?php if (empty($val['upi_action'])): ?>
                                                    <a href="<?= URI ?>/result/card/<?= $this->data['deal_id'] ?>?protocol_id=<?= $val['ID'] ?>"
                                                       class="text-dark text-decoration-none text-nowrap fw-bold">
                                                        <?= $val['view_number'] ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?= $val['view_number'] ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $val['date_ru'] ?></td>
                                            <td>
                                                <?php if (!empty($val['PROTOCOL_OUTSIDE_LIS'])): ?>
                                                    <svg class="icon" width="30" height="30">
                                                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#check-circle"/>
                                                    </svg>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($val['PROTOCOL_OUTSIDE_LIS'])): ?>
                                                    <?php if (!empty($this->data['file'][$val['ID']]['file'])): ?>
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

                                                            <?php if (!empty($this->data['file'][$val['ID']]['file'])): ?>
                                                                <input class="file" type="hidden" name="file"
                                                                       value="<?= $this->data['file'][$val['ID']]['file'] ?>">
                                                            <?php endif; ?>

                                                            <div class="position-relative d-inline-block">
                                                                <a href="<?= $this->data['file'][$val['ID']]['dir'] ?><?= $this->data['file'][$val['ID']]['file'] ?>"
                                                                   target="_blank"
                                                                   title="<?= $this->data['file'][$val['ID']]['file'] ?>">
                                                                    <svg class="icon" width="30" height="30">
                                                                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#pdf_file"/>
                                                                    </svg>
                                                                </a>
                                                                <button type="submit"
                                                                        class="button-del-file button-close button-outline"
                                                                        name="delete_pdf"
                                                                        title="Удалить pdf файл"></button>
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

                                                            <label class="upload-pdf cursor-pointer"
                                                                   title="Загрузить PDF-версию">
                                                                <svg class="icon" width="30" height="30">
                                                                    <use xlink:href="<?= URI ?>/assets/images/icons.svg#upload"/>
                                                                </svg>
                                                                <input class="d-none" id="uploadPdf" type="file"
                                                                       name="upload_pdf" onchange="form.submit()">
                                                            </label>
                                                        </form>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <label class="upload-pdf icon-disabled">
                                                        <svg class="icon" width="30" height="30">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#upload"/>
                                                        </svg>
                                                    </label>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($val['is_create_protocol']): ?>
                                                    <a class="no-decoration me-1 validate-protocol"
                                                       data-protocol_id="<?=$val['ID']?>"
                                                       href="/protocol_generator/protocol_multiple_protocols.php?ID=<?= $this->data['deal_id'] ?>&TZ_ID=<?= $this->data['tz_id'] ?>&PROTOCOL_ID=<?= $val['ID'] ?>"
                                                       title="Сформировать">
                                                        <svg class="icon" width="35" height="35">
                                                            <use xlink:href="<?=URI?>/assets/images/icons.svg#form"/>
                                                        </svg>
                                                    </a>
                                                <?php else: ?>
                                                    <a class="no-decoration icon-disabled me-1" href="#" title="Сформировать">
                                                        <svg class="icon" width="35" height="35">
                                                            <use xlink:href="<?=URI?>/assets/images/icons.svg#form"/>
                                                        </svg>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($val['doc_send']): ?>
                                                    <a class="doc-send"
                                                       href="<?= $this->data['file'][$val['ID']]['dir'] ?><?= $this->data['file'][$val['ID']]['file'] ?>"
                                                       title="<?= $this->data['file'][$val['ID']]['file'] ?>" download>
                                                        <svg class="icon" width="30" height="30">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-send"/>
                                                        </svg>
                                                    </a>
                                                <?php else: ?>
                                                    <svg class="icon icon-disabled" width="30" height="30">
                                                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-send"/>
                                                    </svg>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($val['not_unite']): ?>
                                                    <a class="not-unite"
                                                       href="/protocol_generator/protocol_multiple_union.php?ID=<?= $this->data['deal_id'] ?>&TZ_ID=<?= $this->data['tz_id'] ?>&PROTOCOL_ID=<?= $val['ID'] ?>"
                                                       title="Сформировать без объединения ячеек">
                                                        <svg class="icon" width="30" height="30">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#not-unite"/>
                                                        </svg>
                                                    </a>
                                                <?php else: ?>
                                                    <svg class="icon icon-disabled" width="30" height="30">
                                                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#not-unite"/>
                                                    </svg>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($val['add_protocol_number']):?>
                                                    <button type="button"
                                                            class="btn border-light-gray bg-light-gray text-nowrap mt-0 text-white">
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

                                                        <?php if (!empty($this->data['selected_protocol_id'])): ?>
                                                            <input class="selected-protocol-id" type="hidden"
                                                                   name="selected_protocol_id"
                                                                   value="<?= $this->data['selected_protocol_id'] ?>">
                                                        <?php endif; ?>
                                                        <button type="submit"
                                                                class="btn btn-primary add-protocol-number text-nowrap mt-0 validate-protocol"
                                                                data-protocol_id="<?=$val['ID']?>"
                                                                name="add_protocol_number">
                                                            Присвоить номер
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($val['delete_protocol']): ?>
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
                                                <?php else: ?>
                                                    <button class="btn border-light-gray bg-light-gray mt-0 fill-white btn-square"
                                                            type="button">
                                                        <i class="fa-solid fa-minus icon-fix"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($val['edit_results']): ?>
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
                                                        <label class="switch d-inline-block">
                                                            <input class="form-check-input edit-results"
                                                                   name="edit_results"
                                                                   type="checkbox"
                                                                <?= $val['EDIT_RESULTS'] ? 'checked' : '' ?>
                                                                   onchange="form.submit()">
                                                            <span class="slider"></span>
                                                        </label>
                                                    </form>
                                                <?php else: ?>
                                                    <label class="switch d-inline-block <?= $val['EDIT_RESULTS'] ? 'checkbox-disabled' : '' ?>">
                                                        <input class="form-check-input"
                                                               name="edit_results"
                                                               type="checkbox"
                                                            <?= $val['EDIT_RESULTS'] ? 'checked' : '' ?> disabled>
                                                        <span class="slider"></span>
                                                    </label>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($val['protocol_is_invalid']): ?>
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
                                                        <label class="switch d-inline-block <?= $val['upi_action'] ? 'checkbox-disabled' : '' ?>">
                                                            <input class="form-check-input protocol-is-invalid"
                                                                   name="protocol_is_invalid"
                                                                   type="checkbox"
                                                                <?= $val['upi_action'] ? 'checked' : '' ?>>
                                                            <span class="slider"></span>
                                                        </label>
                                                    </form>
                                                <?php else: ?>
                                                    <label class="switch d-inline-block <?= $val['upi_action'] ? 'checkbox-disabled' : '' ?>">
                                                        <input class="form-check-input protocol-is-invalid"
                                                               name="protocol_is_invalid"
                                                               type="checkbox"
                                                            <?= $val['upi_action'] ? 'checked' : '' ?>
                                                               disabled>
                                                        <span class="slider"></span>
                                                    </label>
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
                        <div class="row protocol-button-wrapper">
                            <div class="col">
                                <button type="submit" class="btn btn-primary btn-create-protocol"
                                        name="btn_create_protocol">
                                    Создать протокол
                                </button>
                            </div>
                        </div>
                    </form>
                    <!--./form-create-protocol-->
                </div>
                <!--./panel-body-->
            </div>
        </div>
    </div>


    <form class="form form-result" id="formResult" method="post" action="<?= URI ?>/result/insertUpdate/">
        <?php if (!empty($this->data['requirement']['tz_id'])): ?>
            <input class="tz-id" type="hidden" name="tz_id" value="<?= $this->data['requirement']['tz_id'] ?>">
        <?php endif; ?>

        <?php if (!empty($this->data['deal_id'])): ?>
            <input class="deal-id" type="hidden" name="deal_id" value="<?= $this->data['deal_id'] ?>">
        <?php endif; ?>

        <?php if (!empty($_SESSION['SESS_AUTH']['USER_ID'])): ?>
            <input class="user-id" type="hidden" name="user_id" value="<?= $_SESSION['SESS_AUTH']['USER_ID'] ?>">
        <?php endif; ?>

        <?php if (!empty($this->data['selected_protocol_id'])): ?>
            <input class="protocol-id" type="hidden" name="protocol_id"
                   value="<?= $this->data['selected_protocol_id'] ?: '' ?>">
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
                        <div class="trial-results-wrapper table-responsive mb-4">
                            <table class="table table-hover text-center" id="trialResultsTable">
                                <thead>
                                <tr class="table-secondary align-middle">
                                    <th scope="col" nowrap>
                                        <label class="switch">
                                            <input class="form-check-input all-checkbox-prob" type="checkbox"
                                                   name="all_checkbox_prob"
                                                <?= $this->data['all_checkbox_prob'] ? 'checked' : '' ?>>
                                            <span class="slider"></span>
                                        </label>
                                    </th>
                                    <th scope="col" nowrap>Материал</th>
                                    <th scope="col" nowrap>Опред. хар-ки</th>
                                    <th scope="col" nowrap>Ед. изм.</th>
                                    <th scope="col" nowrap>Технические условия</th>
                                    <th scope="col" nowrap>Нормативное значение</th>
                                    <th scope="col" nowrap>Методика</th>
                                    <th scope="col" nowrap>Фактическое значение</th>
                                    <th scope="col" nowrap>Среднее значение</th>
                                    <th scope="col" nowrap>Соответствие требованиям</th>
                                    <th scope="col" nowrap>В ОА</th>
                                    <th scope="col" nowrap>Номер протокола</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->data['material_gost'] as $umtr_id => $data): ?>
                                    <?php $i = 0; ?>
                                    <?php foreach ($data as $ugtp_id => $val): ?>
                                        <tr class="<?= $val['table_green'] ?>">
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?>">
                                                <?php if ($i === 0): ?>
                                                    <label class="switch <?= $val['probe_selected'] ?>">
                                                        <input class="form-check-input probe-checkbox"
                                                               name="probe_checkbox[<?= $umtr_id ?>]"
                                                               type="checkbox"
                                                            <?= !empty($val['protocol_id']) ? 'checked' : '' ?>
                                                            <?= !empty($val['probe_selected']) ? 'disabled' : '' ?>>
                                                        <span class="slider"></span>
                                                    </label>
                                                <?php endif; ?>
                                            </td>
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?>">
                                                <?= $val['m_mame'] ?: '' ?> <?= $val['cipher'] ?: '' ?>
                                            </td>
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?>">
                                                <?= $val['bgm_specification'] ?: '' ?>
                                            </td>
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?>">
                                                <?= $val['units'] ?: '' ?>
                                            </td>
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?>">
                                                <?= $val['bgc_gost'] ?: '' ?><br> <?= $val['bgc_specification'] ?: '' ?>
                                            </td>
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?>">
                                                <input type="text"
                                                       class="form-control normative-value <?= $val['readonly_normative_value'] ? 'bg-light-secondary' : 'bg-white' ?>"
                                                       name="normative_value[<?= $umtr_id ?>][<?= $ugtp_id ?>]"
                                                       value="<?= $val['view_normative_value'] ?? '' ?>"
                                                    <?= $val['readonly_normative_value'] ? 'readonly' : '' ?>>
                                            </td>
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?>">
                                                <a href="/obl_acc.php?ID=<?= $val['method_id'] ?>"
                                                   class="text-decoration-none">
                                                    <?= $val['bgm_gost'] ?: '' ?> <?= $val['bgm_punkt'] ?: '' ?>
                                                </a>
                                            </td>
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?> td-actual-value">
                                                <div class="d-flex actual-value-wrapper mb-1">
                                                    <input <?= $val['actual_value_type'] ?>
                                                            class="me-2 actual-value w-100 border p-2 <?= $val['is_save_info'] ? 'bg-white' : 'bg-light-secondary' ?>"
                                                            name="actual_value[<?= $umtr_id ?>][<?= $ugtp_id ?>][]"
                                                            value="<?= $val['out_range'] ? $val['out_range'] : $val['actual_value'][0] ?>"
                                                        <?= $val['is_save_info'] ? '' : 'readonly' ?>>
                                                    <button class="btn mt-0 btn-square add-value-actual <?= $val['is_save_info'] ? 'btn-primary' : 'btn-secondary' ?>"
                                                            type="button"
                                                        <?= $val['is_save_info'] ? '' : 'disabled' ?>>
                                                        <i class="fa-solid fa-plus icon-fix"></i>
                                                    </button>
                                                </div>
                                                <?php if (!empty($val['actual_value']) && count($val['actual_value']) > 1): ?>
                                                    <?php for ($j = 1; $j < count($val['actual_value']); $j++): ?>
                                                        <div class="d-flex actual-value-wrapper mb-1">
                                                            <input type="number"
                                                                   class="me-2 actual-value w-100 <?= $val['is_save_info'] ? 'bg-white' : 'bg-light-secondary' ?>"
                                                                   name="actual_value[<?= $umtr_id ?>][<?= $ugtp_id ?>][]"
                                                                   step="any"
                                                                   value="<?= $val['actual_value'][$j] ?? '' ?>"
                                                                <?= $val['is_save_info'] ? '' : 'readonly' ?>>
                                                            <button type="button"
                                                                    class="btn btn-square del-value-actual mt-0 <?= $val['is_save_info'] ? 'btn-danger' : 'btn-secondary' ?>"
                                                                <?= $val['is_save_info'] ? '' : 'disabled' ?>>
                                                                <i class="fa-solid fa-minus icon-fix"></i>
                                                            </button>
                                                        </div>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?>">
                                                <?= $val['view_average_value'] ?>
                                            </td>
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?>">
                                                <?php if ($val['bgc_match_manual']): ?>
                                                    <select class="form-select match <?= $val['is_save_info'] ? '' : 'disabled' ?> <?= $val['match_message'] ? 'is-invalid' : '' ?>"
                                                            name="match[<?= $umtr_id ?>][<?= $ugtp_id ?>]"

                                                            aria-describedby="match_<?= $umtr_id ?>_<?= $ugtp_id ?>">
                                                        <option value="0"
                                                            <?= (int)$val['match'] === 0 ? 'selected' : '' ?>>
                                                            Не соответствует
                                                        </option>
                                                        <option value="1"
                                                            <?= (int)$val['match'] === 1 ? 'selected' : '' ?>>
                                                            Соответствует
                                                        </option>
                                                        <option value="2"
                                                            <?= (int)$val['match'] === 2 ? 'selected' : '' ?>>
                                                            -
                                                        </option>
                                                        <option value="3"
                                                            <?= (int)$val['match'] === 3 ? 'selected' : '' ?>>
                                                            Не нормируется
                                                        </option>
                                                    </select>
                                                    <div id="match_<?= $umtr_id ?>_<?= $ugtp_id ?>"
                                                         class="invalid-feedback">
                                                        <?= $val['match_message'] ?: '' ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="<?= $val['match_message'] ? 'is-invalid' : '' ?>"
                                                         aria-describedby="match_<?= $umtr_id ?>_<?= $ugtp_id ?>">
                                                        <?= $val['match_view'] ?: '' ?>
                                                    </div>
                                                    <div id="match_<?= $umtr_id ?>_<?= $ugtp_id ?>"
                                                         class="invalid-feedback">
                                                        <?= $val['match_message'] ?: '' ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?> cursor-pointer"
                                                title="<?= $val['range_title'] ?>">
                                                <?= $val['bgm_in_oa'] ? '+' : '-' ?>
                                            </td>
                                            <td class="<?= $i === 0 ? 'probe-border-top' : '' ?>">
                                                <?= $val['p_number'] ?>
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class='arrowLeft'>
                                <svg class="bi" width="40" height="40">
                                    <use xlink:href="<?= URI ?>/assets/images/icons.svg#arrow-left"/>
                                </svg>
                            </div>
                            <div class='arrowRight'>
                                <svg class="bi" width="40" height="40">
                                    <use xlink:href="<?= URI ?>/assets/images/icons.svg#arrow-right"/>
                                </svg>
                            </div>
                        </div>
                        <!--./trial-results-wrapper-->
                    </div>
                    <!--./panel-body-->
                </div>
            </div>
        </div>


        <?php if (!empty($this->data['selected_protocol_id'])): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <header class="panel-heading">
                            Информация по протоколу № <?= $this->data['protocol']['view_number'] ?: '' ?>
                            <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="#"
                               class="fa fa-chevron-up"></a>
                         </span>
                        </header>
                        <div class="panel-body">
                            <div class="protocol-information-wrapper mx-3">
                                <div class="row mb-3">
                                    <div class="col wrapper-shadow me-3">
                                        <strong class="d-block mb-3">Общая информация</strong>

                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label for="protocolType">Тип протокола</label>
                                                <select class="form-select protocol-type w-100" name="protocol_type"
                                                    <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                                    <option <?= $this->data['result']['protocol_type'] === 0 ? 'selected' : '' ?>
                                                            value='simple'>
                                                        Стандартный
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 1 ? 'selected' : '' ?>
                                                            value='allvalue'>
                                                        Средние значения
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 5 ? 'selected' : '' ?>
                                                            value='zern'>
                                                        Зерновой состав песка, нерудных СМ
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 21 ? 'selected' : '' ?>
                                                            value='zern_№2'>
                                                        Зерновой состав песка, нерудных СМ (только зерновой состав)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 6 ? 'selected' : '' ?>
                                                            value='grunt'>Зерновой состав грунта
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 22 ? 'selected' : '' ?>
                                                            value='grunt_№2'>Зерновой состав грунта (только зерновой
                                                        состав)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 7 ? 'selected' : '' ?>
                                                            value='prirod'>Зерновой состав природного песка
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 23 ? 'selected' : '' ?>
                                                            value='prirod_№2'>Зерновой состав природного песка (только
                                                        зерновой состав)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 8 ? 'selected' : '' ?>
                                                            value='tu_12801'>Зерновой состав ГОСТ 12801
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 24 ? 'selected' : '' ?>
                                                            value='tu_12801_№2'>Зерновой состав ГОСТ
                                                        12801 (только зерновой состав)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 32 ? 'selected' : '' ?>
                                                            value='gost31015'>Зерновой состав ГОСТ 12801 (ГОСТ 31015)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 16 ? 'selected' : '' ?>
                                                            value='tu_183_2'>Зерновой состав ПНСТ 183
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 25 ? 'selected' : '' ?>
                                                            value='tu_183_2_№2'>Зерновой состав ПНСТ 183 (только
                                                        зерновой
                                                        состав)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 9 ? 'selected' : '' ?>
                                                            value='tu_183'>Зерновой состав ПНСТ 184
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 26 ? 'selected' : '' ?>
                                                            value='tu_183_№2'>Зерновой состав ПНСТ
                                                        184 (только зерновой состав)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 19 ? 'selected' : '' ?>
                                                            value='zern_sheb'>Зерновой состав
                                                        щебня
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 27 ? 'selected' : '' ?>
                                                            value='zern_sheb_№2'>Зерновой состав щебня (только зерновой
                                                        состав)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 42 ? 'selected' : '' ?>
                                                            value='zern_sheb_smes'>Зерновой состав
                                                        щебня смешанные фракции
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 10 ? 'selected' : '' ?>
                                                            value='osk1'>ОСК щебень
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 11 ? 'selected' : '' ?>
                                                            value='osk2'>ОСК дороги
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 12 ? 'selected' : '' ?>
                                                            value='osk3'>ОСК ПНСТ 183, 184
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 13 ? 'selected' : '' ?>
                                                            value='osk4'>ОСК песок
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 14 ? 'selected' : '' ?>
                                                            value='osk_sred'>ОСК средние значения
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 15 ? 'selected' : '' ?>
                                                            value='shps'>Зерновой состав ЩПС
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 28 ? 'selected' : '' ?>
                                                            value='shps_№2'>
                                                        Зерновой состав ЩПС (только зерновой состав)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 17 ? 'selected' : '' ?>
                                                            value='sheb'>Зерновой состав щебня ОСК
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 18 ? 'selected' : '' ?>
                                                            value='sheb_shlak'>Зерновой состав щебня (ГОСТ 8267)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 29 ? 'selected' : '' ?>
                                                            value='sheb_shlak_№2'>Зерновой состав
                                                        щебня (ГОСТ 8267) (только зерновой состав)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 20 ? 'selected' : '' ?>
                                                            value='density_grunt'>ОСК режущее кольцо
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 30 ? 'selected' : '' ?>
                                                            value='frost_resistance'>Морозостойкость
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 31 ? 'selected' : '' ?>
                                                            value='metric_method'>Зерновой состав
                                                        грунта (ареометрическим метод)
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 33 ? 'selected' : '' ?>
                                                            value='shortcut'>Упрощенный
                                                    </option>
                                                    <option <?= $this->data['result']['protocol_type'] === 34 ? 'selected' : '' ?>
                                                            value='shortcut_mean'>Упрощенный среднее значение
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="materialGroup">Группа материала</label>
                                                <select class="form-select material-group" name="GROUP_MAT"
                                                    <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                                    <option value="0" selected>Выбрать</option>

                                                    <?php foreach ($this->data['materials'] as $val): ?>
                                                        <optgroup label="<?= $val['NAME'] ?>">
                                                            <?php foreach ($val['GROUPS'] as $key => $group): ?>
                                                                <?php
                                                                if (empty($group)) {
                                                                    continue;
                                                                }
                                                                ?>
                                                                <option value="<?= $val['ID'] ?>-<?= $key ?>"
                                                                    <?= $this->data['result']['GROUP_MAT'] === "{$val['ID']}-{$key}" ? 'selected' : '' ?>>
                                                                    <?= $group ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </optgroup>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label for="verify">Подпись в протоколе</label>
                                                <select class="form-select verify w-100" name="VERIFY[]"
                                                        multiple="multiple" <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                                    <?php foreach ($this->data['assigned'] as $val): ?>
                                                        <option value="<?= $val['user_id'] ?>"
                                                            <?= in_array($val['user_id'], $this->data['result']['VERIFY']) ? 'selected' : '' ?>><?= $val['user_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div>Не оцен. на соотв. нормам</div>
                                                <?php if ($this->data['is_save_info']): ?>
                                                    <div class="d-flex align-items-center no-evaluate">
                                                        <label class="switch mt-2">
                                                            <input class="form-check-input no-evaluate" name="NO_COMPLIANCE"
                                                                   type="checkbox"
                                                                <?= !empty($this->data['result']['NO_COMPLIANCE']) ? 'checked' : '' ?>
                                                                <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                                            <span class="slider"></span>
                                                        </label>
                                                    </div>
                                                <?php else: ?>
                                                    <label class="switch d-inline-block <?= $this->data['result']['NO_COMPLIANCE'] ? 'checkbox-disabled' : '' ?>">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                            <?= $this->data['result']['NO_COMPLIANCE'] ? 'checked' : '' ?>
                                                               disabled>
                                                        <span class="slider"></span>
                                                    </label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col wrapper-shadow">
                                        <strong class="d-block mb-3">Информация об испытаниях</strong>

                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <lable for="dateBegin">Дата начала</lable>
                                                <input type="date" class="form-control date-begin"
                                                       name="DATE_BEGIN"
                                                       value="<?= $this->data['result']['DATE_BEGIN'] ?>"
                                                    <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <lable for="dateEnd">Дата окончания</lable>
                                                <input type="date" class="form-control date-end"
                                                       name="DATE_END" value="<?= $this->data['result']['DATE_END'] ?>"
                                                    <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <lable>Температура</lable>
                                                <div class="row">
                                                    <div class="col">
                                                        <input type="number" class="form-control w-100"
                                                               name="TEMP_O" step="any" placeholder="От"
                                                               value="<?= $this->data['result']['TEMP_O'] ?>"
                                                            <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                                    </div>
                                                    <div class="col">
                                                        <input type="number" class="form-control w-100"
                                                               name="TEMP_TO_O" step="any" placeholder="До"
                                                               value="<?= $this->data['result']['TEMP_TO_O'] ?>"
                                                            <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <lable>Влажность</lable>
                                                <div class="row">
                                                    <div class="col">
                                                        <input type="number" class="form-control w-100"
                                                               name="VLAG_O" step="any" placeholder="От"
                                                               value="<?= $this->data['result']['VLAG_O'] ?>"
                                                            <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                                    </div>
                                                    <div class="col">
                                                        <input type="number" class="form-control w-100"
                                                               name="VLAG_TO_O" step="any" placeholder="До"
                                                               value="<?= $this->data['result']['VLAG_TO_O'] ?>"
                                                            <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col wrapper-shadow me-3">
                                        <strong class="d-block mb-3">Информация об оборудовании</strong>

                                        <div class="row align-items-end min-h-180">
                                            <div class="form-group col">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span>
                                                        Используемое оборудование
                                                    </span>
                                                    <?php if ($this->data['is_save_info']): ?>
                                                        <button type="button" class="bg-transparent border-0 revert-default"
                                                                data-protocol-id="<?= $this->data['selected_protocol_id'] ?>">
                                                            <ins>Вернуть по умолчанию</ins>
                                                        </button>
                                                    <?php else: ?>
                                                        <ins>Вернуть по умолчанию</ins>
                                                    <?php endif; ?>
                                                </div>
                                                <select class="form-select min-h-180 equipment-used"
                                                        name="equipment_used"
                                                        multiple="multiple" <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                                    <?php if (!empty($this->data['tz_ob_connect'])): ?>
                                                        <?php foreach ($this->data['tz_ob_connect'] as $val): ?>
                                                            <option value="<?= $val['b_o_id'] ?>" class="<?= $val['bg_color'] ?>">
                                                                <?= $val['TYPE_OBORUD'] ?: '' ?> <?= $val['OBJECT'] ?: '' ?>
                                                                , инв.
                                                                номер <?= $val['REG_NUM'] ?: '' ?><?= $val['GOST'] ?: '' ?>
                                                                , <?= $val['GOST_PUNKT'] ?: '' ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <?php foreach ($this->data['oboruds_to_gosts'] as $val): ?>
                                                            <option value="<?= $val['b_o_id'] ?>" class="<?= $val['bg_color'] ?>">
                                                                <?= $val['TYPE_OBORUD'] ?: '' ?> <?= $val['OBJECT'] ?: '' ?>
                                                                , инв.
                                                                номер <?= $val['REG_NUM'] ?: '' ?><?= $val['GOST'] ?: '' ?>
                                                                , <?= $val['GOST_PUNKT'] ?: '' ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <input type="hidden" id="equipmentIds" name="equipment_ids"
                                               value="<?= $this->data['view_equipment_ids'] ?>">

                                        <div class="row">
                                            <div class="form-group col">
                                                <select class="form-select equipment" id="equipment" name="equipment"
                                                    <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                                    <?php foreach ($this->data['oboruds'] as $val): ?>
                                                        <option value="<?= $val['b_o_id'] ?>"
                                                                data-gost="<?= $val['b_g_id'] ?>">
                                                            <?= $val['TYPE_OBORUD'] ?: '' ?> <?= $val['OBJECT'] ?: '' ?>
                                                            , инв.
                                                            номер <?= $val['REG_NUM'] ?: '' ?><?= $val['GOST'] ?: '' ?>
                                                            , <?= $val['GOST_PUNKT'] ?: '' ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col wrapper-shadow pb-4">
                                        <strong class="d-block mb-3">Данные объекта испытаний</strong>

                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <lable for="objectDescription">Описание объекта</lable>
                                                <textarea class="form-control mw-100 object-description"
                                                          id="objectDescription"
                                                          name="DESCRIPTION"
                                                    <?= $this->data['is_save_info'] ? '' : 'disabled' ?>><?= $this->data['result']['DESCRIPTION'] ?></textarea>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <lable for="object">Объект строительства</lable>
                                                <textarea class="form-control mw-100 object"
                                                          id="object"
                                                          name="OBJECT"
                                                    <?= $this->data['is_save_info'] ? '' : 'disabled' ?>><?= $this->data['result']['OBJECT'] ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <lable for="placeProbe">Место отбора проб</lable>
                                                <textarea class="form-control mw-100 place-probe"
                                                          id="placeProbe"
                                                          name="PLACE_PROBE"
                                                    <?= $this->data['is_save_info'] ? '' : 'disabled' ?>><?= $this->data['result']['PLACE_PROBE'] ?></textarea>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <lable for="dateProbe">Дата отбора проб</lable>
                                                <input type="date" class="form-control date-probe"
                                                       name="DATE_PROBE"
                                                       value="<?= $this->data['result']['DATE_PROBE'] ?>"
                                                    <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col">
                                                <lable for="additionalInformation">Дополнительная информация</lable>
                                                <textarea class="form-control mw-100 additional-information"
                                                          id="additionalInformation"
                                                          name="DOP_INFO"
                                                    <?= $this->data['is_save_info'] ? '' : 'disabled' ?>><?= $this->data['result']['DOP_INFO'] ?></textarea>
                                            </div>
                                        </div>

                                        <strong class="d-block mb-3">Дополнительная информация</strong>

                                        <div class="row">
                                            <div class="col-6">
                                                <div>Протокол выдается вне ЛИС</div>
                                                <?php if ($this->data['is_save_info']): ?>
                                                    <label class="switch">
                                                        <input class="form-check-input protocol-outside-lis"
                                                               name="PROTOCOL_OUTSIDE_LIS"
                                                               type="checkbox"
                                                            <?= $this->data['result']['PROTOCOL_OUTSIDE_LIS'] ? 'checked' : '' ?>
                                                            <?= $this->data['is_save_info'] ? '' : 'disabled' ?>>
                                                        <span class="slider"></span>
                                                    </label>
                                                <?php else: ?>
                                                    <label class="switch d-inline-block <?= $this->data['result']['PROTOCOL_OUTSIDE_LIS'] ? 'checkbox-disabled' : '' ?>">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                            <?= $this->data['result']['PROTOCOL_OUTSIDE_LIS'] ? 'checked' : '' ?>
                                                               disabled>
                                                        <span class="slider"></span>
                                                    </label>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-6">
                                                <div>C аттестатом аккредитации</div>
                                                <?php if ($this->data['is_adds_certificate']): ?>
                                                    <label class="switch <?= $this->data['is_adds_certificate'] ? '' : 'checkbox-disabled' ?>">
                                                        <input class="form-check-input" name="ATTESTAT_IN_PROTOCOL"
                                                               type="checkbox"
                                                            <?= $this->data['result']['ATTESTAT_IN_PROTOCOL'] ? 'checked' : '' ?>
                                                            <?= $this->data['is_adds_certificate'] ? '' : 'disabled' ?>>
                                                        <span class="slider"></span>
                                                    </label>
                                                <?php else: ?>
                                                    <label class="switch d-inline-block <?= $this->data['result']['ATTESTAT_IN_PROTOCOL'] ? 'checkbox-disabled' : '' ?>">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                            <?= $this->data['result']['ATTESTAT_IN_PROTOCOL'] ? 'checked' : '' ?>
                                                               disabled>
                                                        <span class="slider"></span>
                                                    </label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!--Песок строительный--->
                                <div class="row mt-3 grain-composition zern zern_№2 zern_short_simple <?= in_array($this->data['result']['protocol_type'], [5, 21]) ? '' : 'd-none' ?>">
                                    <div class="col wrapper-shadow">
                                        <strong class="d-block mb-3">Зерновой состав</strong>

                                        <div class="table-responsive mb-2">
                                            <table class="table text-center align-middle table-bordered table-fixed">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th rowspan="3">Объект испытаний (шифр пробы/образца в ИЦ)</th>
                                                    <th rowspan="3">Наименование остатка</th>
                                                    <th colspan="5">Остатки на сите (размер сита в мм), % по массе
                                                    </th>
                                                    <th rowspan="2">Проход через сито 0,16, % по массе</th>
                                                </tr>
                                                <tr class="align-middle">
                                                    <th>2,5</th>
                                                    <th>1,25</th>
                                                    <th>0,63</th>
                                                    <th>0,315</th>
                                                    <th>0,16</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td rowspan="2">
                                                        <?= $this->data['first_material_name'] . ' ' . $this->data['first_cipher'] ?>
                                                    </td>
                                                    <td>
                                                        Частные
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white ostatki w-100"
                                                               name="ostatki[0]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['0'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki w-100"
                                                               name="ostatki[1]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['1'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki w-100"
                                                               name="ostatki[2]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['2'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki w-100"
                                                               name="ostatki[3]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['3'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki w-100"
                                                               name="ostatki[4]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['4'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki w-100"
                                                               name="ostatki[5]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['5'] ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Полные
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki w-100"
                                                               name="ostatki[6]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['6'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki w-100"
                                                               name="ostatki[7]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['7'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki w-100"
                                                               name="ostatki[8]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['8'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki w-100"
                                                               name="ostatki[9]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['9'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki w-100"
                                                               name="ostatki[10]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['10'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki w-100"
                                                               name="ostatki[11]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['11'] ?>">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./zern zern_№2 zern_short_simple-->

                                <!--Асфальт. Зерновой состав мин. части ГОСТ 12801, 9128--->
                                <div class="row mt-3 grain-composition tu_12801_№2 tu_12801 gost31015 <?= in_array($this->data['result']['protocol_type'], [8, 24, 32]) ? '' : 'd-none' ?>">
                                    <div class="col wrapper-shadow">
                                        <strong class="d-block mb-3">Зерновой состав</strong>

                                        <div class="table-responsive mb-2">
                                            <table class="table text-center align-middle table-bordered table-fixed">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th rowspan="2"></th>
                                                    <th colspan="11">Зерновой состав (содержание зерен размером мельче,
                                                        мм), % по массе
                                                    </th>
                                                </tr>
                                                <tr class="align-middle">
                                                    <th>40</th>
                                                    <th>20</th>
                                                    <th>15</th>
                                                    <th>10</th>
                                                    <th>5</th>
                                                    <th>2,5</th>
                                                    <th>1,25</th>
                                                    <th>0,63</th>
                                                    <th>0,315</th>
                                                    <th>0,16</th>
                                                    <th>0,071</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        Фактический состав
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki2 w-100"
                                                               name="ostatki2[11]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['11'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki2 w-100"
                                                               name="ostatki2[0]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['0'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki2 w-100"
                                                               name="ostatki2[1]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['1'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki2 w-100"
                                                               name="ostatki2[2]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['2'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki2 w-100"
                                                               name="ostatki2[3]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['3'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki2 w-100"
                                                               name="ostatki2[4]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['4'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki2 w-100"
                                                               name="ostatki2[5]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['5'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki2 w-100"
                                                               name="ostatki2[6]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['6'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki2 w-100"
                                                               name="ostatki2[7]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['7'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki2 w-100"
                                                               name="ostatki2[8]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['8'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white ostatki2 w-100"
                                                               name="ostatki2[9]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['9'] ?>">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./tu_12801_№2 tu_12801 gost31015-->

                                <!--Грунт--->
                                <div class="row mt-3 grain-composition grunt grunt_№2 <?= in_array($this->data['result']['protocol_type'], [6, 22]) ? '' : 'd-none' ?>">
                                    <div class="col wrapper-shadow">
                                        <strong class="d-block mb-3">Зерновой состав</strong>
                                        <div class="table-responsive mb-2">
                                            <table class="table text-center align-middle table-bordered table-fixed">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th rowspan="2">Наименование остатка</th>
                                                    <th colspan="13">Остатки на сите, (размер сита в мм), % по массе
                                                    </th>
                                                </tr>
                                                <tr class="align-middle">
                                                    <th>100</th>
                                                    <th>60</th>
                                                    <th>40</th>
                                                    <th>20</th>
                                                    <th>10</th>
                                                    <th>5</th>
                                                    <th>2</th>
                                                    <th>1</th>
                                                    <th>0,5</th>
                                                    <th>0,25</th>
                                                    <th>0,1</th>
                                                    <th>0,05</th>
                                                    <th>Менее 0,05</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        Частный остаток
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white w-100"
                                                               name="ostatki3[21]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['21'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[20]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['20'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[19]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['19'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[18]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['18'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[0]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['0'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[1]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['1'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[2]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['2'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[3]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['3'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[4]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['4'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[5]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['5'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[6]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['6'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[7]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['7'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[8]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['8'] ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Полный остаток
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[25]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['25'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[24]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['24'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[23]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['23'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[22]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['22'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[9]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['9'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[10]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['10'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[11]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['11'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[12]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['12'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[13]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['13'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[14]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['14'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[15]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['15'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[16]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['16'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki3[17]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['17'] ?>">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./grunt grunt_№2-->

                                <!--Песок природны--->
                                <div class="row mt-3 grain-composition prirod prirod_№2 prirod_short_simple <?= in_array($this->data['result']['protocol_type'], [7, 23, 36]) ? '' : 'd-none' ?>">
                                    <div class="col wrapper-shadow">
                                        <strong class="d-block mb-3">Зерновой состав</strong>

                                        <div class="table-responsive mb-2">
                                            <table class="table text-center align-middle table-bordered table-fixed">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th rowspan="3">Объект испытаний (шифр пробы/образца в ИЦ)</th>
                                                    <th colspan="10">Остатки на сите (размер сита в мм), % по массе
                                                    </th>
                                                    <th rowspan="2" colspan="2">Количество песка на поддоне, % по
                                                        массе
                                                    </th>
                                                </tr>
                                                <tr class="align-middle">
                                                    <th colspan="2">2,0</th>
                                                    <th colspan="2">1,0</th>
                                                    <th colspan="2">0,5</th>
                                                    <th colspan="2">0,25</th>
                                                    <th colspan="2">0,125</th>
                                                </tr>
                                                <tr class="align-middle">
                                                    <th>Частные</th>
                                                    <th>Полные</th>
                                                    <th>Частные</th>
                                                    <th>Полные</th>
                                                    <th>Частные</th>
                                                    <th>Полные</th>
                                                    <th>Частные</th>
                                                    <th>Полные</th>
                                                    <th>Частные</th>
                                                    <th>Полные</th>
                                                    <th>Частные</th>
                                                    <th>Полные</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td rowspan="2">
                                                        <?= $this->data['first_material_name'] . ' (' . $probe[1]['sh_number'][0] . ')' ?>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white w-100"
                                                               name="ostatki4[0]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['0'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki4[6]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['6'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki4[1]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['1'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki4[7]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['7'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki4[2]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['2'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki4[8]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['8'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki4[3]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['3'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki4[9]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['9'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki4[4]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['4'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki4[10]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['10'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki4[5]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['5'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki4[11]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['11'] ?>">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./prirod prirod_№2 prirod_short_simple-->

                                <!--Гранулометрический состав минеральной части смеси ПНСТ 183(TODO: 184?)--->
                                <div class="row mt-3 grain-composition tu_183 tu_183_№2 <?= in_array($this->data['result']['protocol_type'], [9, 26]) ? '' : 'd-none' ?>">
                                    <div class="col wrapper-shadow">
                                        <strong class="d-block mb-3">Зерновой состав</strong>

                                        <div class="table-responsive mb-2">
                                            <table class="table text-center align-middle table-bordered table-fixed">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th rowspan="2">Сита по ГОСТ Р 58406.2</th>
                                                    <th colspan="13">Зерновой состав (содержание зерен размером мельче,
                                                        мм), % по массе
                                                    </th>
                                                </tr>
                                                <tr class="align-middle">
                                                    <th>31,5</th>
                                                    <th>22,4</th>
                                                    <th>16,0</th>
                                                    <th>11,2</th>
                                                    <th>8,0</th>
                                                    <th>5,6</th>
                                                    <th>4,0</th>
                                                    <th>2,0</th>
                                                    <th>1,0</th>
                                                    <th>0,5</th>
                                                    <th>0,25</th>
                                                    <th>0,125</th>
                                                    <th>0,063</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        Фактический состав
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white w-100"
                                                               name="ostatki5[0]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['0'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[1]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['1'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[2]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['2'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[3]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['3'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[4]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['4'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[5]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['5'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[6]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['6'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[7]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['7'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[8]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['8'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[9]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['9'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[10]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['10'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[11]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['11'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki5[12]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['12'] ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Состав по данным заказчика
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white w-100"
                                                               name="sostav[0]" step="any"
                                                               value="<?= $this->data['result']['sostav']['0'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[1]" step="any"
                                                               value="<?= $this->data['result']['sostav']['1'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[2]" step="any"
                                                               value="<?= $this->data['result']['sostav']['2'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[3]" step="any"
                                                               value="<?= $this->data['result']['sostav']['3'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[4]" step="any"
                                                               value="<?= $this->data['result']['sostav']['4'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[5]" step="any"
                                                               value="<?= $this->data['result']['sostav']['5'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[6]" step="any"
                                                               value="<?= $this->data['result']['sostav']['6'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[7]" step="any"
                                                               value="<?= $this->data['result']['sostav']['7'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[8]" step="any"
                                                               value="<?= $this->data['result']['sostav']['8'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[9]" step="any"
                                                               value="<?= $this->data['result']['sostav']['9'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[10]" step="any"
                                                               value="<?= $this->data['result']['sostav']['10'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[11]" step="any"
                                                               value="<?= $this->data['result']['sostav']['11'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="sostav[12]" step="any"
                                                               value="<?= $this->data['result']['sostav']['12'] ?>">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./tu_183 tu_183_№2-->

                                <!--Щебеночно-песочная смесь-->
                                <div class="row mt-3 grain-composition shps shps_№2 <?= in_array($this->data['result']['protocol_type'], [15, 28]) ? '' : 'd-none' ?>">
                                    <div class="col wrapper-shadow">
                                        <strong class="d-block mb-3">Зерновой состав</strong>

                                        <div class="table-responsive mb-2">
                                            <table class="table text-center align-middle table-bordered table-fixed">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th rowspan="2">Объект испытаний (шифрпробы/образца в ИЦ)</th>
                                                    <th rowspan="2">Наименова-ние остатка</th>
                                                    <th colspan="10">Остатки на сите, (размер сита в мм), % по массе
                                                    </th>
                                                </tr>
                                                <tr class="align-middle">
                                                    <th>120</th>
                                                    <th>80</th>
                                                    <th>40</th>
                                                    <th>20</th>
                                                    <th>10</th>
                                                    <th>5</th>
                                                    <th>2,5</th>
                                                    <th>0,63</th>
                                                    <th>0,16</th>
                                                    <th>0,05</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td rowspan="2">
                                                        <?= $this->data['first_material_name'] . ' ' . $this->data['first_cipher'] ?>
                                                    </td>
                                                    <td>
                                                        Частный остаток
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white w-100"
                                                               name="ostatki6[0]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['0'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[1]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['1'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[2]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['2'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[3]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['3'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[4]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['4'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[5]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['5'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[6]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['6'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[7]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['7'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[8]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['8'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[9]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['9'] ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Полный остаток
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white w-100"
                                                               name="ostatki6[10]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['10'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[11]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['11'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[12]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['12'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[13]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['13'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[14]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['14'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[15]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['15'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[16]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['16'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[17]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['17'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[18]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['18'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki6[19]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['19'] ?>">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./shps shps_№2-->

                                <!--Щебень-->
                                <div class="row mt-3 grain-composition sheb zern_sheb zern_sheb_№2 zern_sheb_smes <?= in_array($this->data['result']['protocol_type'], [17, 19, 27, 42]) ? '' : 'd-none' ?>">
                                    <div class="col wrapper-shadow">
                                        <strong class="d-block mb-3">Зерновой состав</strong>

                                        <div class="table-responsive mb-2">
                                            <table class="table text-center align-middle table-bordered table-fixed">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th rowspan="2">Объект испытаний (шифрпробы/образца в ИЦ)</th>
                                                    <th colspan="6">Проходы через сито, (размер ячейки сита в мм), % по
                                                        массе
                                                    </th>
                                                </tr>
                                                <tr class="align-middle">
                                                    <th>2D</th>
                                                    <th>1.4D</th>
                                                    <th>D</th>
                                                    <th>D/1.4</th>
                                                    <th>d</th>
                                                    <th>d/2</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td rowspan="2">
                                                        <?= $this->data['first_material_name'] . ' ' . $this->data['first_cipher'] ?>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white w-100"
                                                               name="ostatki7[0]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['0'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki7[1]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['1'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki7[2]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['2'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki7[3]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['3'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki7[4]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['4'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki7[5]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['5'] ?>">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./sheb zern_sheb zern_sheb_№2 zern_sheb_smes-->

                                <!--Щебень шлаковый-->
                                <div class="row mt-3 grain-composition sheb_shlak sheb_shlak_№2 <?= in_array($this->data['result']['protocol_type'], [18, 29]) ? '' : 'd-none' ?>">
                                    <div class="col wrapper-shadow">
                                        <strong class="d-block mb-3">Зерновой состав</strong>

                                        <div class="table-responsive mb-2">
                                            <table class="table text-center align-middle table-bordered table-fixed">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th rowspan="2">Остатки на ситах</th>
                                                    <th colspan="6">Диаметр отверстий контрольных сит, мм
                                                    </th>
                                                </tr>
                                                <tr class="align-middle">
                                                    <th>1,25D</th>
                                                    <th>D</th>
                                                    <th>0,5(D+d)</th>
                                                    <th>d</th>
                                                    <th>d/2</th>
                                                    <th>Менее d</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        Частные остатки на ситах %
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white w-100"
                                                               name="ostatki8[0]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['0'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki8[1]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['1'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki8[2]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['2'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki8[3]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['3'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki8[4]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['4'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki8[5]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['5'] ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Полные остатки на ситах %
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white w-100"
                                                               name="ostatki8[6]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['6'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki8[7]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['7'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki8[8]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['8'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki8[9]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['9'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki8[10]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['10'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki8[11]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['11'] ?>">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./sheb_shlak sheb_shlak_№2-->

                                <!--Морозостойкость-->
                                <div class="row mt-3 grain-composition frost_resistance <?= in_array($this->data['result']['protocol_type'], [30]) ? '' : 'd-none' ?>">
                                    <div class="col wrapper-shadow">
                                        <strong class="d-block mb-3">Зерновой состав</strong>

                                        <div class="table-responsive mb-2">
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
                                                    <td rowspan="5">
                                                        <?= $this->data['first_material_name'] . ' ' . $this->data['first_cipher'] ?>
                                                    </td>
                                                    <td rowspan="5">
                                                        <?= $this->data['first_bgm_gost'] ?: '' ?> <?= $this->data['first_bgm_punkt'] ?: '' ?>
                                                    </td>
                                                    <td>
                                                        Наличие трещин, сколов, шелушения
                                                    </td>
                                                    <td>
                                                        -
                                                    </td>
                                                    <td>
                                                        <input type="text"
                                                               class="form-control"
                                                               name="control_damage"
                                                               value="<?= $this->data['frost']['control_damage'] ?>">
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
                                                               name="main_damage"
                                                               value="<?= $this->data['frost']['main_damage'] ?>">
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
                                                               class="form-control bg-white w-100"
                                                               name="control_mass"
                                                               value="<?= $this->data['frost']['control_mass'] ?>">
                                                    </td>
                                                    <td>
                                                        Среднее уменьшение массы образцов
                                                    </td>
                                                    <td>
                                                        %
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="main_mass"
                                                               value="<?= $this->data['frost']['main_mass'] ?>" step="0.01">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Прочность при сжатии насыщенных образцов</td>
                                                    <td>МПа</td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="control_strength[0]" step="any"
                                                               value="<?= $this->data['frost']['control_strength']['0'] ?>">
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="control_strength[1]" step="any"
                                                               value="<?= $this->data['frost']['control_strength']['1'] ?>">
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="control_strength[2]" step="any"
                                                               value="<?= $this->data['frost']['control_strength']['2'] ?>">
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="control_strength[3]" step="any"
                                                               value="<?= $this->data['frost']['control_strength']['3'] ?>">
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="control_strength[4]" step="any"
                                                               value="<?= $this->data['frost']['control_strength']['4'] ?>">
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="control_strength[5]" step="any"
                                                               value="<?= $this->data['frost']['control_strength']['5'] ?>">
                                                    </td>
                                                    <td>
                                                        Прочность при сжатии образцов после испытания
                                                    </td>
                                                    <td>
                                                        МПа
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="main_strength[0]" step="any"
                                                               value="<?= $this->data['frost']['main_strength']['0'] ?>">
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="main_strength[1]" step="any"
                                                               value="<?= $this->data['frost']['main_strength']['1'] ?>">
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="main_strength[2]" step="any"
                                                               value="<?= $this->data['frost']['main_strength']['2'] ?>">
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="main_strength[3]" step="any"
                                                               value="<?= $this->data['frost']['main_strength']['3'] ?>">
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="main_strength[4]" step="any"
                                                               value="<?= $this->data['frost']['main_strength']['4'] ?>">
                                                        <input type="number"
                                                               class="form-control bg-white w-100 mb-1"
                                                               name="main_strength[5]" step="any"
                                                               value="<?= $this->data['frost']['main_strength']['5'] ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Средняя прочность при сжатии насыщенных образцов</td>
                                                    <td>МПа</td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="control_medium" step="any"
                                                               value="<?= $this->data['frost']['control_medium'] ?>">
                                                    </td>
                                                    <td>
                                                        Средняя прочность при сжатии образцов после испытания
                                                    </td>
                                                    <td>
                                                        МПа
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="main_medium" step="any"
                                                               value="<?= $this->data['frost']['main_medium'] ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Нижняя граница доверительного интервала
                                                        X<sub>min</sub><sup>I</sup></td>
                                                    <td>-</td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="control_bottom_line" step="any"
                                                               value="<?= $this->data['frost']['control_bottom_line'] ?>">
                                                    </td>
                                                    <td>
                                                        Нижняя граница доверительного интервала X<sub>min</sub><sup>II</sup>
                                                    </td>
                                                    <td>
                                                        -
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="main_bottom_line" step="any"
                                                               value="<?= $this->data['frost']['main_bottom_line'] ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8">
                                                        <div class="form-group">
                                                            <label for="ratio" class="mb-2">Примечания: Соотношение X<sub>min</sub><sup>II</sup> ≥ 0,9 X<sub>min</sub><sup>I</sup></label>
                                                            <select class="form-select ratio" name="ratio">
                                                                <option value="соблюдается"
                                                                    <?= $this->data['frost']['ratio'] == 'соблюдается' ? 'selected' : '' ?>>
                                                                    соблюдается
                                                                </option>
                                                                <option value="не соблюдается"
                                                                    <?= $this->data['frost']['ratio'] == 'не соблюдается' ? 'selected' : '' ?>>
                                                                    не соблюдается
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./frost_resistance-->

                                <!--Ареометрический метод-->
                                <div class="row mt-3 grain-composition metric_method <?= in_array($this->data['result']['protocol_type'], [31]) ? '' : 'd-none' ?>">
                                    <div class="col wrapper-shadow">
                                        <strong class="d-block mb-3">Зерновой состав</strong>

                                        <div class="table-responsive mb-2">
                                            <table class="table text-center align-middle table-bordered table-fixed">
                                                <thead>
                                                <tr class="align-middle">
                                                    <th rowspan="2">Остатки на ситах</th>
                                                    <th colspan="11">Диаметр отверстий контрольных сит, мм
                                                    </th>
                                                </tr>
                                                <tr class="align-middle">
                                                    <th>10</th>
                                                    <th>5</th>
                                                    <th>2</th>
                                                    <th>1</th>
                                                    <th>0,5</th>
                                                    <th>0,25</th>
                                                    <th>0,1</th>
                                                    <th>0,05</th>
                                                    <th>0,01</th>
                                                    <th>0,002</th>
                                                    <th>менее 0,002</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        Частные остатки на ситах %
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white w-100"
                                                               name="ostatki9[0]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['0'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[1]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['1'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[2]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['2'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[3]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['3'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[4]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['4'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[5]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['5'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[6]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['6'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[7]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['7'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[26]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['26'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[27]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['27'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[28]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['28'] ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Полные остатки на ситах %
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control bg-white w-100"
                                                               name="ostatki9[9]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['9'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[10]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['10'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[11]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['1'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[12]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['12'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[13]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['13'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[14]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['14'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[15]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['15'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[16]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['16'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[29]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['29'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[30]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['30'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               class="form-control bg-white w-100"
                                                               name="ostatki9[31]" step="any"
                                                               value="<?= $this->data['result']['ostatki']['31'] ?>">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--./-->
                            </div>
                        </div>
                        <!--./panel-body-->
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col">
                <?php if ($this->data['is_save_info']): ?>
                    <button type="submit" class="btn btn-primary save" form="formResult" name="save">
                        Сохранить
                    </button>
                <?php else: ?>
                    <button type="button" class="btn border-light-gray bg-light-gray text-white">
                        Сохранить
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </form>
    <!--./form-result-->

<!--    --><?php //if ($this->data['is_may_view']): ?>
<!--    <div class="line-dashed"></div>-->
<!---->
<!--    <a href="/results_isp.php?ID=--><?//= $this->data['deal_id'] ?><!--&ID_P=--><?//= $this->data['selected_protocol_id'] ?><!--">Вернуться-->
<!--        на старый дизайн</a>-->
<!--    --><?php //endif; ?>

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->
</div>
<!--./wrapper-card-->
