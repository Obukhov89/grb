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

    <input type="hidden" id="user-id-input" value="<?= $this->data['user_id'] ?>">

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
                                                    <span title="Отсутвуют прикреплённые пробы у протокола">
                                                        <input class="form-check-input scale-1_5" type="radio"
                                                               value="" disabled>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ( empty($val['INVALID']) ): ?>
                                                    <a href="<?= URI ?>/result/card_new/<?= $this->data['deal_id'] ?>?protocol_id=<?= $val['ID'] ?><?= $val['selected_probe'] ? '&selected' : '' ?>"
                                                       class="text-decoration-none text-nowrap fw-bold">
                                                        <?= $val['view_number'] ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?= $val['view_number'] ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $val['date_ru'] ?></td>
                                            <td>
                                                <?php if($val['protocol_info']):?>
                                                    <span title="Для доступа к данным протокола проверте выбран ли протокол, протокол является действительным и у протокола отсутсвует номер или есть номер но протокол разблокирован, и к протоколу прикреплены пробы">
                                                        <svg class="icon icon-disabled" width="35" height="35">
                                                            <use xlink:href="<?=URI?>/assets/images/icons.svg#edit"/>
                                                        </svg>
                                                    </span>
                                                <?php else:?>
                                                    <button type="button"
                                                            class="btn bg-transparent border-0 mt-0 p-0 protocol-information"
                                                            data-protocol="<?= $val['ID'] ?>" title="Информация по протоколу">
                                                        <svg class="icon" width="35" height="35">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#edit"/>
                                                        </svg>
                                                    </button>
                                                <?php endif;?>
                                            </td>
                                            <td>
                                                <?php if (!empty($val['PROTOCOL_OUTSIDE_LIS'])): ?>
                                                    <svg class="icon" width="30" height="30">
                                                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#check-circle"/>
                                                    </svg>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($val['PROTOCOL_OUTSIDE_LIS'])): ?>
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

                                                            <?php if (!empty($this->data['selected'])): ?>
                                                                <input class="selected" type="hidden" name="selected" value="selected">
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
                                                    <span title="Для загрузки PDF-версии, в протоколе отмете 'Протокол выдается вне ЛИС'">
                                                        <svg class="icon icon-disabled" width="30" height="30">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#upload"/>
                                                        </svg>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($val['is_create_protocol']): ?>
                                                    <span title="Для формирования протокола проверте выбран ли протокол и является ли протокол действительным, выбраны ли пробы для протокола. <?= $val['probe_count'] ? '' : 'Внимание! У протокола отсутвую прикреплённые пробы' ?>">
                                                        <svg class="icon icon-disabled" width="35" height="35">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#form"/>
                                                        </svg>
                                                    </span>
                                                <?php else: ?>
                                                    <a class="no-decoration me-1 <?= $val['validation_class'] ?>"
                                                       data-protocol_id="<?=$val['ID']?>"
                                                       href="/ulab/generator/ProtocolDocument/<?= $val['ID'] ?>"
                                                       title="Сформировать">
                                                        <svg class="icon" width="35" height="35">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#form"/>
                                                        </svg>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($val['doc_send']): ?>
                                                    <span title="Для скачивания протокола проверте был ли сформирован протокол, является ли протокол действительным и не является протоколом выданным в не ЛИС. <?= !empty($val['file']['file']) ? 'Внимание! Файл для скачивания отсутствует.' : '' ?>">
                                                        <svg class="icon icon-disabled" width="30" height="30">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-send"/>
                                                        </svg>
                                                    </span>
                                                <?php else: ?>
                                                    <a class="doc-send"
                                                       href="<?= $val['file']['dir'] ?><?= $val['file']['file'] ?>"
                                                       title="<?= $val['file']['file'] ?>" download>
                                                        <svg class="icon" width="30" height="30">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#doc-send"/>
                                                        </svg>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($val['add_protocol_number'] && $this->data['deal_type'] != 'COMPLEX'): ?>
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
                                                                data-protocol_id="<?=$val['ID']?>"
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
                        <div class="trial-results-wrapper table-responsive mb-4">
                            <table class="table table-hover text-center align-middle" id="trialResultsTable">
                                <thead>
                                <tr class="table-secondary align-middle">
                                    <th class="clip-padding-box" scope="col" nowrap>
                                        <label class="switch">
                                            <input class="form-check-input all-checkbox-prob" type="checkbox"
                                                   name="all_checkbox_prob"
                                                <?= $this->data['all_checkbox_prob'] ? 'checked' : '' ?>>
                                            <span class="slider"></span>
                                        </label>
                                    </th>
                                    <th class="clip-padding-box" scope="col" nowrap>Материал</th>
                                    <th class="clip-padding-box" scope="col" nowrap>
                                        Испытание
                                    </th>
                                    <th class="clip-padding-box" scope="col" nowrap>
                                        Помещение
                                    </th>
                                    <th class="clip-padding-box" scope="col" nowrap>Опред. хар-ки</th>
                                    <th class="clip-padding-box" scope="col" nowrap>Лист<br> измерения</th>
                                    <th class="clip-padding-box" scope="col" nowrap>Ед. изм.</th>
                                    <th class="clip-padding-box" scope="col" nowrap>ТУ</th>
                                    <th class="clip-padding-box" scope="col" nowrap>Нормативное значение</th>
                                    <th class="clip-padding-box" scope="col" nowrap>Методика</th>
                                    <th class="clip-padding-box" scope="col" nowrap>Фактическое значение</th>
                                    <th class="clip-padding-box" scope="col" nowrap>Соответствие требованиям</th>
                                    <th class="clip-padding-box" scope="col" nowrap>В ОА</th>
                                    <th class="clip-padding-box" scope="col" nowrap>Номер протокола</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->data['material_gost'] as $umtr_id => $data): ?>
                                    <?php $i = 0; ?>
                                    <?php foreach ($data as $ugtp_id => $val): ?>

                                        <tr class="<?= $val['table_green'] ?>">
                                            <td class="<?= $val['border_row'] ?>">
                                                <?php if ($i === 0): ?>
                                                    <?php if ($val['probe_selected']): ?>
                                                        <span title="Открепить пробы возможно если выбран протокол к которому прикреплена проба и нет номера протокола или есть номер но протокол разблокирован">
                                                            <label class="switch checkbox-disabled">
                                                                <input class="form-check-input" type="checkbox"
                                                                    <?= !empty($val['protocol_id']) ? 'checked' : '' ?> disabled>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </span>
                                                    <?php else: ?>
                                                        <label class="switch">
                                                            <input class="form-check-input probe-checkbox"
                                                                   name="probe_checkbox[<?= $umtr_id ?>]" type="checkbox"
                                                                <?= !empty($val['protocol_id']) ? 'checked' : '' ?>>
                                                            <span class="slider"></span>
                                                        </label>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <?= $val['m_mame'] ?: '' ?> <?= $val['cipher'] ?: '' ?>
                                            </td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <?php if (!$val['start_trials']['state']): ?>
                                                    <?php if ($val['trial']): ?>
                                                        <span title="Начало испытания доступно если нет номера протокола или есть номер но протокол разблокирован">
                                                            <svg class="icon icon-disabled fill-success icon-start" width="40" height="40">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#start"/>
                                                        </svg>
                                                        </span>
                                                    <?php else: ?>
                                                        <button type="button"
                                                                class="btn bg-transparent border-0 mt-0 p-0 btn-start-stop btn-start"
                                                                data-ugtp="<?= $ugtp_id ?>"
                                                                data-protocol="<?= $val['protocol_id'] ?>" title="Начать испытание">
                                                            <svg class="icon fill-success icon-start" width="40" height="40">
                                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#start"/>
                                                            </svg>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php elseif ($val['start_trials']['state'] === 'pause'): ?>
                                                    <?php if ($val['trial']): ?>
                                                        <span title="Возобновление испытания доступно если нет номера протокола или есть номер но протокол разблокирован">
                                                            <svg class="icon icon-disabled icon-start" width="40" height="40">
                                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#start-pause"/>
                                                            </svg>
                                                        </span>
                                                    <?php else: ?>
                                                        <button type="button"
                                                                class="btn bg-transparent border-0 mt-0 p-0 btn-start-stop btn-start"
                                                                data-ugtp="<?= $ugtp_id ?>"
                                                                data-protocol="<?= $val['protocol_id'] ?>" title="Возобновить испытание">
                                                            <svg class="icon icon-start" width="40" height="40">
                                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#start-pause"/>
                                                            </svg>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php elseif ($val['start_trials']['state'] === 'start'): ?>
                                                    <?php if ($val['trial']): ?>
                                                        <span title="Приостановка и завершение испытания доступно если нет номера протокола или есть номер но протокол разблокирован">
                                                            <div class="d-flex align-items-end">
                                                                <svg class="icon icon-disabled me-2" width="40" height="40">
                                                                    <use xlink:href="<?= URI ?>/assets/images/icons.svg#pause"/>
                                                                </svg>
                                                                <svg class="icon icon-disabled fill-danger" width="40" height="40">
                                                                    <use xlink:href="<?= URI ?>/assets/images/icons.svg#stop"/>
                                                                </svg>
                                                            </div>
                                                        </span>
                                                    <?php else: ?>
                                                        <div class="d-flex align-items-end">
                                                            <button type="button"
                                                                    class="btn bg-transparent border-0 mt-0 me-2 p-0 btn-start-stop btn-pause"
                                                                    data-ugtp="<?= $ugtp_id ?>" data-protocol="<?= $val['protocol_id'] ?>"
                                                                    title="Приостановить испытание">
                                                                <svg class="icon" width="40" height="40">
                                                                    <use xlink:href="<?= URI ?>/assets/images/icons.svg#pause"/>
                                                                </svg>
                                                            </button>
                                                            <button type="button"
                                                                    class="btn bg-transparent border-0 mt-0 p-0 btn-start-stop btn-stop"
                                                                    data-ugtp="<?= $ugtp_id ?>" data-protocol="<?= $val['protocol_id'] ?>"
                                                                    title="Завершить испытание">
                                                                <svg class="icon fill-danger" width="40" height="40">
                                                                    <use xlink:href="<?= URI ?>/assets/images/icons.svg#stop"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php elseif ($val['start_trials']['state'] === 'complete'): ?>
                                                    Испытание завершено
                                                <?php endif; ?>
                                            </td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <?= $val['rooms_name'] ?: 'не выбрано' ?>
                                            </td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <?= $val['um_name'] ?: '' ?>
                                            </td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <?php if ( empty($val['measurement']['name']) ): ?>
                                                    <div title="<?= $val['sheet_title'] ?>">
                                                        <i class="fa-solid fa-calculator font-size-35 icon-disabled"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <button type="button"
                                                            class="btn bg-transparent border-0 mt-0 p-0 measurement-sheet"
                                                            data-measurement="<?= $val['measurement']['id'] ?>"
                                                            data-ugtp="<?= $ugtp_id ?>"
                                                            data-method="<?= $val['ugtp_method_id'] ?>" title="Лист измерения">
                                                        <i class="fa-solid fa-calculator font-size-35"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <?= $val['units'] ?: '' ?>
                                            </td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <a href="/ulab/techCondition/edit/<?= $val['conditions_id'] ?>"
                                                   class="text-decoration-none">
                                                    <?= $val['tech']['reg_doc'] ?: '-' ?> <?= $val['tech']['measured_properties_name'] ?: '' ?>
                                                </a>
                                            </td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <div class="form-text text-start"><?= $val['normative_text'] ?></div>
                                                <?php if ($val['readonly_normative_value']): ?>
                                                    <div class="normative-value w-100 border p-2 like-input text-start bg-light-secondary like-input"
                                                         title="Доступно для редактирования если ТУ выбрано при формировании ТЗ и ТУ не нормируемое и нет номера протокола или есть номер но протокол разблокирован">
                                                        <?= htmlentities($val['normative_value']) ?>
                                                    </div>
                                                <?php else: ?>
                                                    <input type="text"
                                                           class="form-control normative-value bg-white"
                                                           name="normative_value[<?= $umtr_id ?>][<?= $ugtp_id ?>]"
                                                           value="<?= htmlentities($val['normative_value']) ?>">
                                                <?php endif; ?>
                                                <div class="form-text text-start"><?= $val['normative_message'] ?></div>
                                            </td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <a href="/ulab/gost/method/<?= $val['ugtp_method_id'] ?>"
                                                   class="text-decoration-none">
                                                    <?= $val['g_reg_doc'] ?: '' ?> <?= $val['clause'] ?: '' ?>
                                                </a>
                                            </td>
                                            <td class="td-actual-value <?= $val['border_row'] ?>">
                                                <div class="form-text text-danger text-start"><?= $val['out_range'] ?></div>
                                                <?php if ($val['readonly_actual_value']): ?>
                                                    <?php if($val['actual_value_type'] != 'select'): ?>
                                                        <input <?= $val['actual_value_type'] ?>
                                                                class="me-2 actual-value actual-value-<?= $ugtp_id ?> w-100 border p-2 bg-light-secondary"
                                                                name="actual_value[<?= $umtr_id ?>][<?= $ugtp_id ?>]"
                                                                value="<?= $val['actual_value'] ?>"
                                                                title="Доступно для редактирования если нет номера протокола или есть номер но протокол на редактировании и у методики ф/значение текстом или у методики ф/значение не текстом и нет листа измерения"
                                                                readonly>
                                                    <?php else: ?>
                                                        <select class="me-2 actual-value actual-value-<?= $ugtp_id ?> w-100 border p-2 bg-light-secondary"
                                                                name="actual_value[<?= $umtr_id ?>][<?= $ugtp_id ?>]"
                                                                title="Доступно для редактирования если нет номера протокола или есть номер но протокол на редактировании и у методики ф/значение текстом или у методики ф/значение не текстом и нет листа измерения"
                                                                readonly>
                                                            <?php foreach ($val['actual_facts_select'] as $key => $value): ?>
                                                                <option value="<?= $value ?>" <?= $val['actual_value'] == $value ? 'selected' : '' ?>><?= $value ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php if($val['actual_value_type'] != 'select'): ?>
                                                        <div class="d-flex actual-value-wrapper mb-1">
                                                            <input <?= $val['actual_value_type'] ?>
                                                                    class="me-2 actual-value actual-value-<?= $ugtp_id ?> w-100 border p-2 <?= !empty($val['confirm_oa_readonly']) ? 'bg-light-secondary' : 'bg-white' ?>"
                                                                    name="actual_value[<?= $umtr_id ?>][<?= $ugtp_id ?>]"
                                                                    value="<?= $val['actual_value'] ?>" <?= !empty($val['confirm_oa_readonly']) ? 'readonly' : '' ?>>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="d-flex actual-value-wrapper mb-1">
                                                            <select class="me-2 actual-value actual-value-<?= $ugtp_id ?> w-100 border p-2 <?= !empty($val['confirm_oa_readonly']) ? 'bg-light-secondary' : 'bg-white' ?>"
                                                                    name="actual_value[<?= $umtr_id ?>][<?= $ugtp_id ?>]"
                                                                <?= !empty($val['confirm_oa_readonly']) ? 'readonly' : '' ?>
                                                            >
                                                                <?php foreach ($val['actual_facts_select'] as $key => $value): ?>
                                                                    <option value="<?= $value ?>" <?= $val['actual_value'] == $value ? 'selected' : '' ?>><?= $value ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    <?php endif; ?>

                                                <?php endif; ?>
                                                <div class="form-text text-start"><?= $val['actual_value_message'] ?></div>
                                            </td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <?php if ($val['readonly_match']): ?>
                                                    <div class="form-control match w-100 p-2 text-start bg-light-secondary text-start like-input <?= $val['match_message'] ? 'is-invalid' : '' ?>"
                                                         aria-describedby="match_<?= $umtr_id ?>_<?= $ugtp_id ?>"
                                                         title="Доступно для редактирования если нет номера протокола или есть номер но протокол на редактировании и ТУ не нормируемое или в ТУ ручное управление 'соотв/не соотв' или фактических значений более 1">
                                                        <?= $val['match_view'] ?: '' ?>
                                                    </div>
                                                    <div id="match_<?= $umtr_id ?>_<?= $ugtp_id ?>"
                                                         class="invalid-feedback">
                                                        <?= $val['match_message'] ?: '' ?>
                                                    </div>
                                                <?php else: ?>
                                                    <select class="form-select match <?= $val['match_message'] ? 'is-invalid' : '' ?>"
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
                                                <?php endif; ?>
                                                <div class="form-text"><?= $val['match_text'] ?></div>
                                            </td>
                                            <?php if ($val['confirm_oa']): ?>
                                                <?php if ($this->data['may_confirm_oa']): ?>
                                                    <td class="<?= $val['border_row'] ?> cursor-pointer"
                                                        title="Для формирования протокола с аттестатом подтвердите что значение находиться в ОА. Подтверждение доступно роли СМК и Админ. Диапазон: <?= $val['range_ao'] ?>">
                                                        <div class="wrapper-confirm-oa">
                                                            <i class="fa-regular fa-circle-question icon-big is-confirm-oa"></i>
                                                            <label class="switch confirm-oa-switch d-none">
                                                                <input class="form-check-input probe-checkbox confirm-oa-elem"
                                                                       name="is_confirm_oa[<?= $umtr_id ?>][<?= $ugtp_id ?>]"
                                                                       type="checkbox" value="1"
                                                                    <?= !empty($val['is_confirm_oa']) ? 'checked' : '' ?> disabled>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                <?php else: ?>
                                                    <td class="<?= $val['border_row'] ?> cursor-pointer"
                                                        title="Для формирования протокола с аттестатом подтвердите что значение находиться в ОА. Подтверждение доступно роли СМК и Админ. Диапазон: <?= $val['range_ao'] ?>">
                                                        <i class="fa-regular fa-circle-question icon-big icon-disabled"></i>
                                                    </td>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <td class="<?= $val['border_row'] ?> cursor-pointer <?= $val['in_field'] ? 'text-green' : 'text-red' ?>"
                                                    title="Диапазон: <?= $val['range_ao'] ?>">
                                                    <?= $val['in_field'] ?
                                                        '<i class="fa-regular fa-circle-check icon-big"></i>' :
                                                        '<i class="fa-regular fa-circle-xmark icon-big"></i>' ?>
                                                </td>
                                            <?php endif; ?>
                                            <td class="<?= $val['border_row'] ?>">
                                                <?php if (!empty($val['protocol']['ID']) && empty($val['protocol']['INVALID'])): ?>
                                                    <a href="<?= URI ?>/result/card_new/<?= $this->data['deal_id'] ?>?protocol_id=<?= $val['protocol']['ID'] ?><?= $val['selected_probe'] ? '&selected' : '' ?>"
                                                       class="text-decoration-none text-nowrap fw-bold">
                                                        <?= $val['protocol']['NUMBER'] ?: 'Номер не присвоен' ?>
                                                    </a>
                                                <?php endif; ?>
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
		  action="<?= URI ?>/result/saveMeasurementData/" method="post" enctype="multipart/form-data">
		<input type="hidden" name="deal_id" value="<?=$this->data['deal_id']?>">
        <input type="hidden" id="ugtpId" name="ugtp_id" value="">
        <input type="hidden" name="direct" value="card_new">
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
                                <select class="form-select w-100 d-none" name="protocol[id_template]">
                                    <?php foreach ($this->data['template_list'] as $item): ?>
                                        <option <?= $this->data['protocol']['id_template'] === $item['id'] ? 'selected' : '' ?>
                                                value="<?=$item['id']?>"><?=$item['name']?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select class="form-select protocol-type w-100 select2" name="protocol[PROTOCOL_TYPE]">
                                    <option value='2'>Стандартный</option>
<!--                                    <option value='0'>Стандартный</option>-->
                                    <option value="1">Стандартный (ЭЦП)</option>
                                    <option value="3">Стандартный НК схема "B"</option>
                                    <option value="4">Стандартный НК "B" без схемы участков</option>
                                    <option value="5">Стандартный НК схема "Г"</option>
                                    <option value='33'>Упрощенный (ЭЦП)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label for="verify">Подпись в протоколе</label>
                                <select class="form-select verify w-100 select2" name="protocol[VERIFY][]" multiple="multiple"></select>
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
                                <input type="date" class="form-control date-begin <?=$this->data['is_deal_osk'] ? '' : 'date-trials'?>"
                                       name="protocol[DATE_BEGIN]" value=""
                                       title="Редактирование доступно Администраторам и Руководителям ИЦ">
                            </div>
                            <div class="col">
                                <lable for="dateEnd">Дата окончания</lable>
                                <input type="date" class="form-control date-end <?=$this->data['is_deal_osk'] ? '' : 'date-trials'?>"
                                       name="protocol[DATE_END]" value=""
                                       title="Редактирование доступно Администраторам и Руководителям ИЦ">
                            </div>
                        </div>
                        <div class="form-group row conditions-wrapper">
                            <div class="col">
                                <lable>Температура</lable>
                                <div class="row">
                                    <div class="col">
                                        <input type="number" class="form-control w-100 temp1"
                                               name="protocol[TEMP_O]" step="any" placeholder="От" value=""
                                               title="Редактирование доступно Администраторам и Руководителям ИЦ">
                                    </div>
                                    <div class="col">
                                        <input type="number" class="form-control w-100 temp2"
                                               name="protocol[TEMP_TO_O]" step="any" placeholder="До" value=""
                                               title="Редактирование доступно Администраторам и Руководителям ИЦ">
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <lable>Влажность</lable>
                                <div class="row">
                                    <div class="col">
                                        <input type="number" class="form-control w-100 wet1"
                                               name="protocol[VLAG_O]" step="any" placeholder="От" value=""
                                               title="Редактирование доступно Администраторам и Руководителям ИЦ">
                                    </div>
                                    <div class="col">
                                        <input type="number" class="form-control w-100 wet2"
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
                    <div class="col bg-white border border-light-gray px-3 py-3 me-3">
                        <strong class="d-block mb-3">Информация об оборудовании</strong>

                        <div class="row align-items-end min-h-180">
                            <div class="form-group col">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Используемое оборудование</span>
                                    <button type="button" class="bg-transparent border-0 revert-default">
                                        <ins>Вернуть по умолчанию</ins>
                                    </button>
                                </div>
                                <select class="form-select min-h-180 equipment-used" name="equipment_used" multiple="multiple"></select>
                            </div>
                        </div>

                        <input type="hidden" id="equipmentIds" name="oborud[equipment_ids]" value="">

                        <div class="row">
                            <div class="form-group col">
                                <select class="form-select equipment select2" id="equipment" name="equipment"></select>
                            </div>
                        </div>
                    </div>
                    <div class="col bg-white border border-light-gray px-3 py-3">
                        <strong class="d-block mb-3">Данные объекта испытаний</strong>

                        <div class="row">
                            <div class="form-group col">
                                <lable for="objectDescription">Описание объекта</lable>
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
                                <textarea class="form-control mw-100 place-probe"
                                          id="placeProbe" name="protocol[PLACE_PROBE]"></textarea>
                            </div>
                            <div class="form-group col">
                                <lable for="dateProbe">Дата отбора проб</lable>
                                <input type="date" class="form-control date-probe" name="protocol[DATE_PROBE]" value="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col">
                                <lable for="additionalInformation">Дополнительная информация</lable>
                                <textarea class="form-control mw-100 additional-information"
                                          id="additionalInformation" name="protocol[DOP_INFO]"></textarea>
                            </div>
                        </div>

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
                                <label class="switch">
                                    <input class="form-check-input attestat-in-protocol"
                                           name="protocol[ATTESTAT_IN_PROTOCOL]" type="checkbox" value="1">
                                    <span class="slider"></span>
                                </label>
<!--                                --><?php //if ($this->data['is_adds_certificate']): ?>
<!--                                    <label class="switch">-->
<!--                                        <input class="form-check-input attestat-in-protocol" name="protocol[ATTESTAT_IN_PROTOCOL]"-->
<!--                                               type="checkbox" value="1">-->
<!--                                        <span class="slider"></span>-->
<!--                                    </label>-->
<!--                                --><?php //else: ?>
<!--                                    <span title="К выдачи протокола с аттестатом аккредитации имеет доступ пользователи с ролью 'Админ' и 'Руководитель ИЦ'">-->
<!--                                        <label class="switch checkbox-disabled">-->
<!--                                            <input class="form-check-input attestat-in-protocol" type="checkbox" disabled>-->
<!--                                            <span class="slider"></span>-->
<!--                                        </label>-->
<!--                                    </span>-->
<!--                                --><?php //endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input name="protocol_id" value="" type="hidden">
            <input name="selected" value="" type="hidden">
            <input name="deal_id" value="<?=$this->data['deal_id']?>" type="hidden">
            <input name="tz_id" value="<?=$this->data['tz_id']?>" type="hidden">

            <div class="line-dashed-small"></div>

            <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
    <!--./protocolInformation-->

    <div id="loadingModal" class="loading-modal d-none">
        <div class="loading-spinner">
            <i class="fa fa-spinner fa-spin"></i>
        </div>
    </div>
    <!--./loading-modal-->
</div>
<!--./wrapper-card-->
