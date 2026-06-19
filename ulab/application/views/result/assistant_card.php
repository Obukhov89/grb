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

    <form class="form form-result" id="formResult" method="post" action="<?= URI ?>/result/updateResult/">
        <?php if (!empty($this->data['requirement']['tz_id'])): ?>
            <input class="tz-id" type="hidden" name="tz_id" value="<?= $this->data['requirement']['tz_id'] ?>">
        <?php endif; ?>

        <?php if (!empty($this->data['deal_id'])): ?>
            <input class="deal-id" type="hidden" name="deal_id" value="<?= $this->data['deal_id'] ?>">
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
                                    <th class="clip-padding-box" scope="col" nowrap>Исполнитель</th>
									<th class="clip-padding-box" scope="col" nowrap>Шифр пробы</th>
                                    <th class="clip-padding-box" scope="col" nowrap>Материал</th>
                                    <th class="clip-padding-box" scope="col" nowrap>Испытание</th>
                                    <th class="clip-padding-box" scope="col" nowrap>Помещение</th>
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
                                        <tr class="<?= $val['table_green'] ?>" bgcolor="<?=$val['tester']['user_id'] == $_SESSION['SESS_AUTH']['USER_ID'] ? '#d2ddfa' : ''?>">
                                            <td class="<?= $val['border_row'] ?>">
													<?= $val['tester']['short_name'] ?: 'Не назначен' ?>
                                            </td>
											<td class="<?= $val['border_row'] ?>"><?= $val['cipher'] ?: '' ?></td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <?= $val['m_mame'] ?: '' ?>
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
                                                    <?= $val['tech']['reg_doc'] ?: '-' ?> <?= $val['measured_properties_name'] ?: '' ?>
                                                </a>
                                            </td>
                                            <td class="<?= $val['border_row'] ?>">
                                                <div class="form-text text-start"><?= $val['normative_text'] ?></div>
                                                <?php if ($val['readonly_normative_value']): ?>
                                                    <div class="normative-value w-100 border p-2 like-input text-start bg-light-secondary like-input"
                                                         title="Доступно для редактирования если ТУ выбрано при формировании ТЗ и ТУ не нормируемое и нет номера протокола или есть номер но протокол разблокирован">
                                                        <?= $val['normative_value'] ?>
                                                    </div>
                                                <?php else: ?>
                                                    <input type="text"
                                                           class="form-control normative-value bg-white"
                                                           name="normative_value[<?= $umtr_id ?>][<?= $ugtp_id ?>]"
                                                           value="<?= $val['normative_value'] ?>">
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
                                                    <input <?= $val['actual_value_type'] ?>
                                                            class="me-2 actual-value actual-value-<?= $ugtp_id ?> w-100 border p-2 bg-light-secondary"
                                                            name="actual_value[<?= $umtr_id ?>][<?= $ugtp_id ?>]"
                                                            value="<?= $val['actual_value'] ?>"
                                                            title="Доступно для редактирования если нет номера протокола или есть номер но протокол на редактировании и у методики ф/значение текстом или у методики ф/значение не текстом и нет листа измерения"
                                                            readonly>
                                                <?php else: ?>
                                                    <div class="d-flex actual-value-wrapper mb-1">
                                                        <input <?= $val['actual_value_type'] ?>
                                                                class="me-2 actual-value actual-value-<?= $ugtp_id ?> w-100 border p-2 <?= !empty($val['confirm_oa_readonly']) ? 'bg-light-secondary' : 'bg-white' ?>"
                                                                name="actual_value[<?= $umtr_id ?>][<?= $ugtp_id ?>]"
                                                                value="<?= $val['actual_value'] ?>" <?= !empty($val['confirm_oa_readonly']) ? 'readonly' : '' ?>>
                                                    </div>
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
          action="<?= URI ?>/result/index/" method="post">
        <input type="hidden" id="ugtpId" name="ugtp_id" value="">
    </form>
    <!--./averageModalForm-->

    <form id="roomsModalForm" class="bg-light mfp-hide col-md-6 m-auto p-3 position-relative"
          action="<?= URI ?>/result/index/" method="post">
        <div class="title mb-3 h-2"></div>

        <div class="line-dashed-small"></div>
    </form>
    <!--./roomsModalForm-->
</div>
<!--./wrapper-card-->
