<header class="header-requirement mb-3">
    <!--card-->
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?= URI ?>/request/list/" title="Вернуться к списку">
                    <svg class="icon" width="20" height="20">
                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#list" />
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?= URI ?>/request/edit/<?= $this->data['deal_id'] ?>" title="Редактировать заявку">
                    <svg class="icon" width="20" height="20">
                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#edit" />
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link disable-after-click <?= empty($this->data['request']['ID']) ? 'disabled' : '' ?>" href="<?= URI ?>/request/copy/<?= $this->data['deal_id'] ?>" title="Скопировать">
                    <svg class="icon" width="20" height="20">
                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#docs" />
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <!--            	<a class="nav-link popup-help" href="#help-form" title="ПОМОГИТЕ">-->
                <a class="nav-link popup-help" href="/ulab/help/LIMS_Manual_Stand/Request_card/Request_card.html" title="Техническая поддержка">
                    <i class="fa-solid fa-question"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<h2 class="d-flex mb-3">
    <?php if ($this->data['deal_id'] < DEAL_NEW_STATUS): ?>
        <div class="stage-block rounded <?= $this->data['stage']['color'] ?> me-1 mt-1" title="<?= $this->data['stage']['title'] ?>"></div>
    <?php else: ?>
        <a class="stage-block rounded me-1 mt-1" title="<?= $this->data['status_list']['title'] ?>" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample" style="background-color: <?= $this->data['status_list']['color'] ?>"></a>
    <?php endif; ?>
    Заявка <?= $this->data['deal_title'] ?>
</h2>

<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" style="width: 600px !important;">
    <div class="offcanvas-header">
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php foreach ($this->data['status_list']['statuses'] as $key => $value): ?>
            <div class="panel panel-default">

                <?php if ($key == $this->data['status_list']['title']): ?>
                    <header class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; background-color: #dffff0;">
                        <div class="d-flex gap-2" style="background-color: #dffff0; padding: 8px">
                            <div style="width: 30px; height: 30px; border-radius: 20% !important; background-color: <?= $value[0]['group_color'] ?>"></div>
                            <span><?= $key ?></span>
                        </div>
                        <span class="tools float-end">
                            <a href="#" class="fa fa-chevron-up"></a>
                        </span>
                    </header>
                <?php elseif ($key == $this->data['status_list']['next_title']): ?>
                    <div style="display: flex; flex-direction: column; margin: 0; align-items: center; justify-content: center">
                        <h5 style="color: #FF0000"><?= $this->data['status_list']['next_comment'] ?></h5>
                    </div>
                    <header class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; background-color: #fdffde;">
                        <div class="d-flex gap-2" style="padding: 8px">
                            <div style="width: 30px; height: 30px; border-radius: 20% !important; background-color: <?= $value[0]['group_color'] ?>"></div>
                            <span><?= $key ?></span>
                        </div>
                        <span class="tools float-end">
                            <a href="#" class="fa fa-chevron-up"></a>
                        </span>
                    </header>
                <?php else: ?>
                    <header class="panel-heading" style="display: flex; justify-content: space-between; align-items: center">
                        <div class="d-flex gap-2" style="padding: 8px">
                            <div style="width: 30px; height: 30px; border-radius: 20% !important; background-color: <?= $value[0]['group_color'] ?>"></div>
                            <span><?= $key ?></span>
                        </div>
                        <span class="tools float-end">
                            <a href="#" class="fa fa-chevron-down"></a>
                        </span>
                    </header>
                <?php endif; ?>

                <?php if ($key == $this->data['status_list']['title']): ?>
                    <div class="panel-body" style="background-color: #dffff0">
                        <table class="table" style="background-color: #dffff0">
                        <?php elseif ($key == $this->data['status_list']['next_title']): ?>
                            <div class="panel-body" style="background-color: #fdffde">
                                <table class="table" style="background-color: #fdffde">
                                <?php else: ?>
                                    <div class="panel-body" style="display: none;">
                                        <table class="table">
                                        <?php endif; ?>

                                        <thead>

                                        </thead>

                                        <tbody>

                                            <?php foreach ($value as $statusValue): ?>
                                                <tr>
                                                    <?php if ($statusValue['header_status']): ?>
                                                        <td style="font-size: 12px; width: 310px">
                                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                                <?= $statusValue['name'] ?> <span class="redStars">*</span>
                                                            </div>
                                                        </td>
                                                    <?php else: ?>
                                                        <td style="font-size: 12px; width: 310px"><?= $statusValue['name'] ?></td>
                                                    <?php endif; ?>

                                                    <?php if ($statusValue['date'] && $statusValue['date'] != '0000-00-00 00:00:00'): ?>
                                                        <?php if (explode(' ', $statusValue['date'])[1] != '00:00:00'): ?>
                                                            <td style="font-size: 12px; width: 125px"><?= date('d.m.Y H:i', strtotime($statusValue['date'])) ?></td>
                                                        <?php else: ?>
                                                            <td style="font-size: 12px; width: 125px"><?= date('d.m.Y --', strtotime($statusValue['date'])) ?></td>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <td style="font-size: 12px; width: 125px">--</td>
                                                    <?php endif; ?>

                                                    <?php if ($statusValue['date']): ?>
                                                        <td style="font-size: 12px; width: 115px">
                                                            <div>
                                                                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="#7CC504" x="188.5" y="188.5" role="img" style="display:inline-block;vertical-align:middle" xmlns="http://www.w3.org/2000/svg">
                                                                    <g fill="#7CC504">
                                                                        <path fill="#7CC504" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm4.768 9.14a1 1 0 1 0-1.536-1.28l-4.3 5.159l-2.225-2.226a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.475-.067l5-6Z" clip-rule="evenodd" />
                                                                    </g>
                                                                </svg>
                                                            </div>
                                                        </td>
                                                    <?php else: ?>
                                                        <td style="font-size: 12px; width: 115px">
                                                            <div>
                                                                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="#c0c0c0" x="188.5" y="188.5" role="img" style="display:inline-block;vertical-align:middle" xmlns="http://www.w3.org/2000/svg">
                                                                    <g fill="#c0c0c0">
                                                                        <path fill="#c0c0c0" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm4.768 9.14a1 1 0 1 0-1.536-1.28l-4.3 5.159l-2.225-2.226a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.475-.067l5-6Z" clip-rule="evenodd" />
                                                                    </g>
                                                                </svg>
                                                            </div>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>

                                        </tbody>

                                        </table>
                                    </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
            </div>

            <div class="panel panel-default">
                <header class="panel-heading">
                    Общая информация
                    <span class="tools float-end">
                        <a href="#" class="fa fa-chevron-up"></a>
                    </span>
                </header>
                <div class="panel-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <div>Общая стоимость</div>
                            <div><strong><?= number_format($this->data['payment']['price'], 2, ',', '') ?> руб.</strong></div>
                        </div>
                        <div class="col-4">
                            <div>Ответственный</div>
                            <div><strong><?= $this->data['assigned'] ?></strong></div>
                        </div>
                        <div class="col-4">
                            <div>
                                Заказчик
                                <?php if ($this->data['is_good_company']): ?>
                                    <img src="/ulab/assets/images/confirmed.png" width="25" height="25" alt="confirm" title="Добросовестный плательщик">
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if ($this->data['is_managers']): ?>
                                    <a href="http://172.16.255.2/crm/company/details/<?= $this->data['company_id'] ?>/" class="text-decoration-none">
                                        <strong><?= $this->data['company_title'] ?></strong>
                                    </a>
                                <?php else: ?>
                                    <strong><?= $this->data['company_title'] ?></strong>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div>Контактное лицо</div>
                            <div><strong><?= $this->data['contact'] ?></strong></div>
                        </div>
                        <div class="col-4">
                            <div>Телефон</div>
                            <div><strong><?= $this->data['phone'] ?></strong></div>
                        </div>
                        <div class="col-4">
                            <div>Почта</div>
                            <div><strong><?= $this->data['head_email'] ?></strong></div>
                        </div>
                    </div>
                    <?php if (!empty($this->data['taken'])): ?>
                        <div class="row">
                            <div class="col-4">
                                <div>Заявка учтена в </div>
                                <div><strong><?= $this->data['taken'] ?></strong></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <input type="hidden" id="inp-id-usr" value="<?= $this->data['user_id'] ?>">

            <div class="panel panel-default">
                <header class="panel-heading">
                    Документы
                    <span class="tools float-end">
                        <a href="#" class="fa fa-chevron-up"></a>
                    </span>
                </header>
                <div class="panel-body">
                    <table class="table align-middle table-card">
                        <thead>
                            <tr class="table-light">
                                <th scope="col" style="width: 25%">Тип документа</th>
                                <th scope="col">Номер</th>
                                <th scope="col">Дата</th>
                                <th scope="col">Дата отправки</th>
                                <th scope="col" colspan="6">Операции</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="<?= $this->data['tz']['check'] ? 'table-green' : '' ?>">
                                <td><strong>Заявка на испытания</strong></td>
                                <td>
                                    <?php if ($this->data['tz']['check']): ?>
                                        <a href="<?= $this->data['is_deal_nk'] ? '/tz_show_nk.php?ID=' . $this->data['request']['ID'] : $this->data['tz']['tz_link'] ?>">
                                            <?= $this->data['request-number'] ?? $this->data['tz']['number'] ?>
                                        </a>
                                    <?php else: ?>
                                        Не сформировано
                                    <?php endif; ?>
                                </td>
                                <td><?= $this->data['tz']['date'] ?></td>
                                <td><?= $this->data['tz']['date_send'] ?></td>
                                <td class="w30">
                                    <?php if ($this->data['tz']['check']): ?>
                                        <a class="no-decoration me-1 disabled" href="#" title="Сформировано">
                                            <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="no-decoration me-1" href="<?= URI . '/requirement/card_new/' . $this->data['tz_id'] ?>" title="Сформировать">
                                            <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="w30">
                                    <a class="no-decoration me-1 popup-mail"
                                        data-id="<?= $this->data['deal_id'] ?>"
                                        data-type="1"
                                        data-title="<?= $this->data['deal_title'] ?>"
                                        href="#email-check"
                                        title="Отправить клиенту бланк заявки">
                                        <svg class="icon" width="35" height="35">
                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#mail" />
                                        </svg>
                                    </a>
                                </td>
                                <td class="w30"></td>
                                <td class="w30"></td>
                                <td class="w30"></td>
                                <td class="w30"></td>
                                <td class="w30"></td>
                            </tr>

                            <tr class="<?= $this->data['proposal']['check'] ? 'table-green' : '' ?>">
                                <td><strong>Коммерческое предложение</strong></td>
                                <td>
                                    <?= $this->data['proposal']['number'] ?>
                                </td>
                                <td>
                                    <?= $this->data['proposal']['date'] ?>
                                </td>
                                <td>
                                    <?= $this->data['proposal']['date_send'] ?>
                                </td>
                                <td>
                                    <a class="no-decoration me-1 disabled" href="#" title="Сформировать">
                                        <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                    </a>
                                    <!-- <?php if ($this->data['proposal']['is_disable_form']): ?>
                                        <a class="no-decoration me-1 disabled" href="#" title="Сформировать">
                                            <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="no-decoration me-1 reload" data-href="<?= $this->data['proposal']['link'] ?>" title="Сформировать">
                                            <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                        </a>
                                    <?php endif; ?> -->
                                </td>
                                <td>
                                    <a class="no-decoration me-1 disabled" href="#" title="Отправить клиенту">
                                        <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                    </a>
                                    <!-- <?php if ($this->data['proposal']['is_disable_mail']): ?>
                                        <a class="no-decoration me-1 disabled" href="#" title="Отправить клиенту">
                                            <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="no-decoration me-1 popup-mail"
                                            data-id="<?= $this->data['deal_id'] ?>"
                                            data-type="2"
                                            data-title="<?= $this->data['deal_title'] ?>"
                                            data-attach="<?= $this->data['proposal']['attach'] ?>"
                                            href="#email-check"
                                            title="Отправить клиенту">
                                            <svg class="icon" width="35" height="35">
                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#mail" />
                                            </svg>
                                        </a>
                                    <?php endif; ?> -->
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr class="<?= $this->data['order']['check'] ?>">
                                <td><strong>Договор</strong></td>
                                <td>
                                    <?php if ($this->data['order']['is_generated']): ?>
                                        <a href="/ulab/order/card/<?= $this->data['order']['id'] ?>"><?= $this->data['order']['number'] ?></a>
                                    <?php else: ?>
                                        Не сформирован
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $this->data['order']['date'] ?>
                                </td>
                                <td>
                                    <?= $this->data['order']['date_send'] ?>
                                </td>
                                <td>
                                    <a class="no-decoration me-1 disabled" href="#" title="Сформировать">
                                        <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                    </a>
                                    <!-- <?php if ($this->data['order']['is_disable_form']): ?>
                                        <a class="no-decoration me-1 disabled" href="#" title="Сформировать">
                                            <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="no-decoration me-1 reloadS" data-href="/protocol_generator/dogovor.php?ID=<?= $this->data['deal_id'] ?>&TZ_ID=<?= $this->data['tz_id'] ?>" title="Сформировать">
                                            <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                        </a>
                                    <?php endif; ?> -->
                                </td>
                                <td>
                                    <a class="no-decoration disabled me-1" href="" title="Отправить клиенту">
                                        <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                    </a>
                                    <!-- <?php if (!$this->data['order']['is_disable_mail']): ?>
                                        <a class="no-decoration me-1 popup-mail"
                                            data-id="<?= $this->data['order']['id'] ?>"
                                            data-type="3"
                                            data-title="<?= $this->data['deal_title'] ?>"
                                            data-attach="<?= $this->data['order']['attach'] ?>"
                                            href="#email-check"
                                            title="Отправить клиенту">
                                            <svg class="icon me-1" width="35" height="35">
                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#mail" />
                                            </svg>
                                        </a>
                                    <?php else: ?>
                                        <a class="no-decoration disabled me-1" href="" title="Отправить клиенту">
                                            <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                        </a>
                                    <?php endif; ?> -->
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" data-text="Договор" class="form-check-input check-mail" <?= $this->data['order']['check'] ? 'checked' : 'disabled' ?>>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr class="<?= $this->data['attach']['check'] ?>">
                                <td><strong>Прил. к договору (ТЗ)</strong></td>
                                <td>
                                    <?= $this->data['attach']['number'] ?>
                                </td>
                                <td>
                                    <?= $this->data['attach']['date'] ?>
                                </td>
                                <td>
                                    <?= $this->data['attach']['date_send'] ?>
                                </td>
                                <td>
                                    <a class="no-decoration me-1 disabled" href="#" title="Сформировать">
                                        <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                    </a>
                                    <!-- <?php if (
                                                $this->data['deal_id'] == 15844 || $this->data['deal_id'] == 15572 || $this->data['deal_id'] == 16777
                                                || $this->data['deal_id'] == 16821 || $this->data['deal_id'] == 17003 || $this->data['deal_id'] == 17056
                                                || $this->data['deal_id'] == 16641 || $this->data['deal_id'] == 16633 || $this->data['deal_id'] == 16738
                                                || $this->data['deal_id'] == 17237 || $this->data['deal_id'] == 16682 || $this->data['deal_id'] == 17393
                                                || $this->data['deal_id'] == 17460 || $this->data['deal_id'] == 17484 || $this->data['deal_id'] == 17670
                                                || $this->data['deal_id'] == 18047 || $this->data['deal_id'] == 18059 || $this->data['deal_id'] == 17691
                                                || $this->data['deal_id'] == 18104 || $this->data['deal_id'] == 18012 || $this->data['deal_id'] == 17581
                                                || $this->data['deal_id'] == 17713 || $this->data['deal_id'] == 18151 || $this->data['deal_id'] == 18222
                                                || $this->data['deal_id'] == 18383 || $this->data['deal_id'] == 18252 || $this->data['deal_id'] == 18494
                                                || $this->data['deal_id'] == 18083 || $this->data['deal_id'] == 18518 || $this->data['deal_id'] == 18441
                                                || $this->data['deal_id'] == 18691 || $this->data['deal_id'] == 18589 || $this->data['deal_id'] == 18820
                                                || $this->data['deal_id'] == 18414 || $this->data['deal_id'] == 18886
                                            ): ?>
                                        <a class="no-decoration me-1 reload" data-href="<?= $this->data['attach']['link'] ?>" title="Сформировать">
                                            <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                        </a>
                                    <?php else: ?>
                                        <?php if ($this->data['attach']['is_disable_form'] && !$this->data['attach']['is_disable_form_for_rostovskaya']): ?>
                                            <a class="no-decoration me-1 disabled" href="#" title="Сформировать">
                                                <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                            </a>
                                        <?php else: ?>
                                            <a class="no-decoration me-1 reload" data-href="<?= $this->data['attach']['link'] ?>" title="Сформировать">
                                                <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?> -->

                                </td>
                                <td>
                                    <a class="no-decoration disabled me-1" href="#" title="Отправить клиенту">
                                        <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                    </a>

                                    <!-- <?php if ($this->data['attach']['is_disable_mail']): ?>
                                        <a class="no-decoration disabled me-1" href="#" title="Отправить клиенту">
                                            <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="no-decoration me-1 popup-mail"
                                            data-id="<?= $this->data['deal_id'] ?>" data-type="6"
                                            data-title="<?= $this->data['deal_title'] ?>"
                                            data-attach="<?= $this->data['attach']['actual_ver'] ?>"
                                            href="#email-check"
                                            title="Отправить клиенту">
                                            <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                        </a>
                                    <?php endif; ?> -->
                                </td>
                                <td class="text-end">
                                    <input type="checkbox" class="form-check-input check-need-sended" title="Не требуется отправка" <?= $this->data['not_need_send'] ? 'checked' : '' ?>>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" data-text="ТЗ" class="form-check-input check-mail" <?= $this->data['attach']['check'] ? 'checked' : 'disabled' ?>>
                                </td>
                                <td></td>
                                <td></td>
                                <td>
                                    <a class="no-decoration <?= ($this->data['attach']['check'] || $this->data['order']['check'] || $this->data['invoice']['check']) ? '' : 'disabled' ?> me-1 popup-group-mail"
                                        href="#group-email-check"
                                        title="Отправить клиенту"
                                        data-order-id="<?= $this->data['doc_id'] ?>"
                                        data-deal-id="<?= $this->data['deal_id'] ?>"
                                        data-tz-id="<?= $this->data['tz_id'] ?>"
                                        data-email="<?= $this->data['email'] ?>"
                                        data-name="<?= $this->data['main_assigned'] ?>"
                                        data-attach1="<?= $this->data['attach1'] ?>"
                                        data-attach2="<?= $this->data['attach2'] ?>"
                                        data-attach3="<?= $this->data['attach3'] ?>"
                                        data-title="<?= $this->data['deal_title'] ?>">
                                        <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                    </a>
                                </td>
                            </tr>

                            <tr class="<?= $this->data['invoice']['check'] ? 'table-green' : '' ?>">
                                <td><strong>Счет</strong></td>
                                <td><?= $this->data['invoice']['number'] ?></td>
                                <td><?= $this->data['invoice']['date'] ?></td>
                                <td><?= $this->data['invoice']['date_send'] ?></td>
                                <td>
                                    <a class="no-decoration disabled me-1" href="" title="Сформировать">
                                        <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                    </a>
                                    <!-- <?php if (
                                                $this->data['deal_id'] == 15554 || $this->data['deal_id'] == 16166 || $this->data['deal_id'] == 17003
                                                || $this->data['deal_id'] == 17056 || $this->data['deal_id'] == 16633 || $this->data['deal_id'] == 12866
                                                || $this->data['deal_id'] == 17252 || $this->data['deal_id'] == 16946 || $this->data['deal_id'] == 17393
                                                || $this->data['deal_id'] == 17409 || $this->data['deal_id'] == 14389 || $this->data['deal_id'] == 17460
                                                || $this->data['deal_id'] == 17670 || $this->data['deal_id'] == 17613 || $this->data['deal_id'] == 17391
                                                || $this->data['deal_id'] == 17707 || $this->data['deal_id'] == 17158 || $this->data['deal_id'] == 17144
                                                || $this->data['deal_id'] == 18047 || $this->data['deal_id'] == 18059 || $this->data['deal_id'] == 17691
                                                || $this->data['deal_id'] == 18104 || $this->data['deal_id'] == 18012 || $this->data['deal_id'] == 17581
                                                || $this->data['deal_id'] == 17713 || $this->data['deal_id'] == 18151 || $this->data['deal_id'] == 18222
                                                || $this->data['deal_id'] == 18383 || $this->data['deal_id'] == 18494 || $this->data['deal_id'] == 18518
                                                || $this->data['deal_id'] == 18441 || $this->data['deal_id'] == 18691 || $this->data['deal_id'] == 18589
                                                || $this->data['deal_id'] == 18724 || $this->data['deal_id'] == 18820 || $this->data['deal_id'] == 18414
                                                || $this->data['deal_id'] == 18886
                                            ): ?>
                                        <a class="no-decoration me-1 reloadS" data-href="/ulab/generator/invoiceDoc/<?= $this->data['deal_id'] ?>" title="Сформировать">
                                            <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                        </a>
                                    <?php else: ?>
                                        <?php if ($this->data['invoice']['is_disable_form'] && !in_array($_SESSION['SESS_AUTH']['USER_ID'], [61, 88, 83, 212, 123]) && !$this->data['invoice']['is_disable_form_for_rostovskaya']): ?>
                                            <a class="no-decoration disabled me-1" href="" title="Сформировать">
                                                <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                            </a>
                                        <?php else: ?>
                                            <a class="no-decoration me-1 reloadS" data-href="<?= $this->data['invoice']['link'] ?>" title="Сформировать">
                                                <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?> -->
                                </td>
                                <td>
                                    <a class="no-decoration disabled me-1" href="" title="Отправить клиенту">
                                        <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                    </a>
                                    <!-- <?php if ($this->data['invoice']['is_disable_mail']): ?>
                                        <a class="no-decoration disabled me-1" href="" title="Отправить клиенту">
                                            <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="no-decoration me-1 popup-mail"
                                            data-id="<?= $this->data['deal_id'] ?>" data-type="4"
                                            data-title="<?= $this->data['deal_title'] ?>"
                                            data-attach="<?= $this->data['invoice']['attach'] ?>"
                                            href="#email-check"
                                            title="Отправить клиенту">
                                            <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                        </a>
                                    <?php endif; ?> -->
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" data-text="Счет" class="form-check-input check-mail" <?= $this->data['invoice']['check'] ? 'checked' : 'disabled' ?>>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr class="<?= $this->data['payment']['check'] ? 'table-green' : '' ?>">
                                <td><strong>Оплата</strong></td>
                                <td><?= $this->data['payment']['status'] ?></td>
                                <td><?= $this->data['payment']['datePayment'] ?></td>
                                <td>--</td>
                                <td>
                                    <a class="no-decoration me-1 popup-with-form <?= $this->data['payment']['check'] && $_SESSION['SESS_AUTH']['USER_ID'] != 88 ? 'disabled' : '' ?>" href="#pay-modal-form" title="Заполнить данные по оплате">
                                        <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                    </a>
                                </td>
                                <td colspan="4">Остаток:<br><?= number_format($this->data['payment']['surcharge'], 2, ',', '') ?> ₽</td>
                                <td></td>
                                <td></td>
                            </tr>

                            <?php if ($this->data['deal_id'] != 15554 && $this->data['deal_id'] != 16166 && $this->data['deal_id'] != 17409): ?>
                                <tr class="<?= $this->data['sample']['check'] ? 'table-green' : '' ?>">
                                    <td><strong>Акт приемки проб</strong></td>
                                    <td>
                                        <?php if ($this->data['sample']['check']): ?>
                                            <a href="/ulab/probe/card/<?= $this->data['deal_id'] ?>"><?= $this->data['sample']['number'] ?></a>
                                        <?php else: ?>
                                            Не сформировано
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $this->data['sample']['date'] ?>
                                    </td>
                                    <td>
                                        <?= $this->data['sample']['date_send'] ?>
                                    </td>
                                    <td>
                                        <?php if ($this->data['sample']['is_disable_form']): ?>
                                            <a class="no-decoration disabled me-1 popup-with-form" href="#" title="Заполнить данные акта">
                                                <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                            </a>
                                        <?php else: ?>
                                            <a class="no-decoration me-1" href="/ulab/probe/new/<?= $this->data['deal_id'] ?>" title="Заполнить данные акта">
                                                <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                            </a>
                                            <!-- <?php if (DEAL_NEW_TZ < $this->data['deal_id'] || $this->data['deal_id'] == 9735): ?>

                                            <?php else: ?>
                                                <a class="no-decoration me-1 popup-with-form" href="#act-probe-modal-form" title="Заполнить данные акта">
                                                    <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                                </a>
                                            <?php endif; ?> -->
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a class="no-decoration me-1" href="<?= $this->data['sample']['link'] ?>" title="Скачать акт приемки проб">
                                            <i class="fa-light fa-file-export text-black font-size-35"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($this->data['sample']['check']): ?>
                                            <a class="no-decoration me-1" href="#" id="sign-btn" onclick="tabletStart(<?= $this->data['deal_id'] ?>); return false;" title="Расписаться">
                                                <i class="fa-light fa-file-signature text-black font-size-35"></i>
                                            </a>
                                        <?php else: ?>
                                            <a class="no-decoration disabled me-1" href="#" id="sign-btn" onclick="tabletStart(<?= $this->data['deal_id'] ?>); return false;" title="Расписаться">
                                                <i class="fa-light fa-file-signature text-black font-size-35"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($this->data['sample_array'] as $key => $value): ?>
                                    <tr class="table-green">
                                        <td><strong>Акт приемки проб</strong></td>
                                        <td>
                                            <a href="/ulab/probe/newCard/<?= $value['ID'] ?>"><?= $value['ACT_NUM'] ?></a>
                                        </td>
                                        <td>
                                            <?= date("d.m.Y", strtotime($value['ACT_DATE'])) ?>
                                        </td>
                                        <td>
                                            <?= $this->data['sample']['date_send'] ?? "--" ?>
                                        </td>
                                        <td>
                                            <?php if ($this->data['sample']['is_disable_form']): ?>
                                                <a class="no-decoration disabled me-1 popup-with-form" href="#" title="Заполнить данные акта">
                                                    <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                                </a>
                                            <?php else: ?>
                                                <a class="no-decoration me-1" href="/ulab/probe/new/<?= $this->data['deal_id'] ?>" title="Заполнить данные акта">
                                                    <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                                </a>
                                                <!-- <?php if (DEAL_NEW_TZ < $this->data['deal_id'] || $this->data['deal_id'] == 9735): ?>

                                                <?php else: ?>
                                                    <a class="no-decoration me-1 popup-with-form" href="#act-probe-modal-form" title="Заполнить данные акта">
                                                        <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                                    </a>
                                                <?php endif; ?> -->
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a class="no-decoration me-1" href="/ulab/generator/actSampleDocumentNew/<?= $value['ID'] ?>" title="Скачать акт приемки проб">
                                                <i class="fa-light fa-file-export text-black font-size-35"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a class="no-decoration me-1" href="#" id="sign-btn" onclick="tabletStart(<?= $this->data['deal_id'] ?>, <?= $value['ID'] ?>); return false;" title="Расписаться">
                                                <i class="fa-light fa-file-signature text-black font-size-35"></i>
                                            </a>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td><strong>Акт приемки проб</strong></td>
                                    <td>
                                        Не сформировано
                                    </td>
                                    <td>
                                        --
                                    </td>
                                    <td>
                                        --
                                    </td>
                                    <td>
                                        <?php if ($this->data['sample']['is_disable_form']): ?>
                                            <a class="no-decoration disabled me-1 popup-with-form" href="#" title="Заполнить данные акта">
                                                <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                            </a>
                                        <?php else: ?>
                                            <a class="no-decoration me-1" href="/ulab/probe/new/<?= $this->data['deal_id'] ?>" title="Заполнить данные акта">
                                                <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                            </a>
                                            <!-- <?php if (DEAL_NEW_TZ < $this->data['deal_id'] || $this->data['deal_id'] == 9735): ?>

                                            <?php else: ?>
                                                <a class="no-decoration me-1 popup-with-form" href="#act-probe-modal-form" title="Заполнить данные акта">
                                                    <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                                </a>
                                            <?php endif; ?> -->
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a class="no-decoration me-1" href="<?= $this->data['sample']['link'] ?>" title="Скачать акт приемки проб">
                                            <i class="fa-light fa-file-export text-black font-size-35"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a class="no-decoration disabled me-1" href="#" id="sign-btn" onclick="tabletStart(<?= $this->data['deal_id'] ?>); return false;" title="Расписаться">
                                            <i class="fa-light fa-file-signature text-black font-size-35"></i>
                                        </a>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php endif; ?>

                            <tr class="<?= $this->data['results']['check'] ? 'table-green' : '' ?>">
                                <td><strong>Результаты испытаний</strong></td>
                                <td>
                                    <?php if ($this->data['results']['check']): ?>
                                        <a href="<?= $this->data['is_deal_nk'] ? '/results_isp_nk.php?ID=' . $this->data['deal_id'] : URI . '/result/card_new' . '/' . $this->data['deal_id'] ?>"><?= $this->data['deal_id'] ?></a>
                                        <!--TODO: Для новых лабораторий удалить else или добавить if если производится рефакторинг результатов испытаний и $this->data['deal_id'] > $this->data['result_refactoring_start_id'], так же убрать или добавить из контроллера $this->data['result_refactoring_start_id'] RequestController.php-->
                                        <!-- <?php if ($this->data['deal_id'] > $this->data['result_refactoring_start_id']): ?>

                                        <?php else: ?>
                                            <a href="/results_isp<?= $this->data['is_deal_nk'] ? '_nk' : '' ?>.php?ID=<?= $this->data['deal_id'] ?>"><?= $this->data['deal_id'] ?></a>
                                        <?php endif; ?> -->
                                    <?php else: ?>
                                        Не внесены
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $this->data['results']['date'] ?>
                                </td>
                                <td>--</td>
                                <td>
                                    <!--TODO: Для новых лабораторий удалить else или добавить if если производится рефакторинг результатов испытаний и $this->data['deal_id'] > $this->data['result_refactoring_start_id'], так же убрать или добавить из контроллера $this->data['result_refactoring_start_id'] RequestController.php-->
                                    <?php if (!$this->data['results']['is_disabled']) : ?>
                                        <a class="no-decoration me-1" href="<?= URI . '/result/card_new' . '/' . $this->data['deal_id'] ?>" title="Внести результаты">
                                            <i class="fa-light fa-file-import text-black font-size-35"></i>
                                        </a>
                                        <!-- <?php if ($this->data['deal_id'] > $this->data['result_refactoring_start_id']): ?>

                                        <?php else: ?>
                                            <a class="no-decoration me-1 asd" href="/results_isp.php?ID=<?= $this->data['deal_id'] ?>" title="Внести результаты">
                                                <i class="fa-light fa-file-import text-black font-size-35"></i>
                                            </a>
                                        <?php endif; ?> -->
                                    <?php else: ?>
                                        <a class="no-decoration me-1 disabled" href="#" title="Внести результаты">
                                            <i class="fa-light fa-file-import text-black font-size-35"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <?php if (empty($this->data['protocol'])): ?>
                                <tr>
                                    <td><strong>Протокол</strong></td>
                                    <td>
                                        Не сформирован
                                    </td>
                                    <td>--</td>
                                    <td>
                                        Не отправлен
                                    </td>
                                    <td>
                                        <a class="no-decoration disabled me-1" href="" title="Сформировать">
                                            <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a class="no-decoration disabled me-1" href="" title="Отправить клиенту">
                                            <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a class="no-decoration disabled me-1" href="" title="ЭЦП">
                                            <svg class="icon" width="35" height="35">
                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#ecp" />
                                            </svg>
                                        </a>
                                    </td>
                                    <td>
                                        <a class="no-decoration disabled me-1" href="" title="Создать XML файл протокола">
                                            <i class="fa-light fa-file-code text-black font-size-35"></i>
                                        </a>
                                    </td>
                                    <td></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($this->data['protocol'] as $key => $protocol): ?>
                                    <tr class="<?= $protocol['check'] ? 'table-green' : '' ?>">
                                        <td><strong>Протокол</strong></td>
                                        <td>
                                            <?= $protocol['title'] ?>
                                        </td>
                                        <td>
                                            <?= $protocol['date'] ?>
                                        </td>
                                        <td>
                                            <?= $protocol['date_send'] ?>
                                        </td>
                                        <td>
                                            <?php if ($protocol['is_disable_form']): ?>
                                                <a class="no-decoration disabled me-1" href="#" title="Сформировать">
                                                    <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                                </a>
                                            <?php else: ?>
                                                <a class="no-decoration me-1 validate-protocol"
                                                    data-protocol_id="<?= $protocol['id'] ?>"
                                                    href="<?= $protocol['link'] ?>"
                                                    title="Сформировать">
                                                    <i class="text-black fa-light fa-file-circle-plus font-size-35"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($protocol['is_disable_mail']): ?>
                                                <a class="no-decoration disabled me-1" href="#" title="<?= $protocol['why_disable_mail'] ?>">
                                                    <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                                </a>
                                            <?php else: ?>
                                                <a class="no-decoration me-1 popup-mail"
                                                    data-id="<?= $protocol['number'] ?>" data-type="5"
                                                    data-title="<?= $this->data['deal_title'] ?>"
                                                    data-attach="<?= $protocol['actual_version'] ?>"
                                                    data-sig="<?= $protocol['sig'] ?>"
                                                    data-pdf="<?= $protocol['pdf'] ?>"
                                                    data-year="<?= $protocol['year'] ?>"
                                                    data-id_p="<?= $protocol['id'] ?>"
                                                    data-ol="<?= $protocol['outLis'] ?>"
                                                    href="#email-check"
                                                    title="Отправить клиенту">
                                                    <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($protocol['is_disable_mail']): ?>
                                            <td></td>
                                        <?php else: ?>
                                            <td class="text-center">
                                                <input type="checkbox" form="group-protocol-email-check" name="PROTOCOLS[]" value="<?= $protocol['pdf'] ? $protocol['pdf'] : '--' ?>|N<?= $protocol['sig'] ? $protocol['sig'] : '--' ?>|N<?= $protocol['outLis'] ? $protocol['outLis'] : '--' ?>|N<?= $protocol['year'] ? $protocol['year'] : '--' ?>|N<?= $protocol['id'] ? $protocol['id'] : '--' ?>" class="form-check-input check-protocol-mail">
                                            </td>
                                        <?php endif; ?>
                                        <?php if ($key == $this->data['protocol_center']): ?>
                                            <td>
                                                <a class="no-decoration <?= ($this->data['attach']['check'] || $this->data['order']['check'] || $this->data['invoice']['check']) ? '' : 'disabled' ?> me-1 popup-group-protocol-mail"
                                                    href="#group-protocol-email-check"
                                                    title="Отправить клиенту"
                                                    data-email="<?= $this->data['email'] ?>"
                                                    data-name="<?= $this->data['main_assigned'] ?>"
                                                    data-title="<?= $this->data['deal_title'] ?>"
                                                    data-order-id="<?= $this->data['doc_id'] ?>"
                                                    data-deal-id="<?= $this->data['deal_id'] ?>"
                                                    data-tz-id="<?= $this->data['tz_id'] ?>">
                                                    <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                                </a>
                                            </td>
                                        <?php else: ?>
                                            <td></td>
                                        <?php endif; ?>
                                        <td>
                                            <?php if (!$protocol['is_enable_ecp']): ?>
                                                <a class="no-decoration disabled me-1" href="#" title="ЭЦП">
                                                    <svg class="icon" width="35" height="35">
                                                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#ecp" />
                                                    </svg>
                                                </a>
                                            <?php else: ?>
                                                <a class="no-decoration me-1"
                                                    href="/ulab/protocol/sig/<?= $protocol['id'] ?>"
                                                    title="ЭЦП">
                                                    <svg class="icon" width="35" height="35">
                                                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#ecp" />
                                                    </svg>
                                                </a>
                                            <?php endif; ?>

                                        </td>
                                        <td>
                                            <a class="no-decoration me-1" href="<?= URI ?>/fsa/protocol/<?= $protocol['id'] ?>" title="Страница создания XML протокола">
                                                <img src="<?= URI ?>/assets/images/xml_icon_2.png" alt="xml" width="35">
                                            </a>
                                        </td>
                                        <td></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <tr class="<?= !empty($this->data['is_end_test']) ? 'table-green' : '' ?>">
                                <td><strong>Завершение испытаний</strong></td>
                                <td><?= $this->data['is_end_test'] ? 'Завершено' : 'Не завершено' ?></td>
                                <td><?= $this->data['complete']['date'] ?></td>
                                <td>--</td>
                                <td>
                                    <?php if ($this->data['is_end_test']): ?>
                                        <?php if ($this->data['complete']['may_return'] || $this->data['deal_id'] == 18851): ?>
                                            <a class="no-decoration me-1" href="<?= URI ?>/request/setStage/<?= $this->data['deal_id'] ?>?stage=0" title="Вернуть испытание на стадию формирования ТЗ">
                                                <i class="fa-light fa-lock text-black font-size-35"></i>
                                            </a>
                                        <?php else: ?>
                                            <span title="Испытание завершено">
                                                <a class="no-decoration disabled me-1" href="#">
                                                    <i class="fa-light fa-lock text-black font-size-35"></i>
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                    <?php elseif ($this->data['complete']['is_disabled'] && $this->data['deal_id'] != 18309): ?>
                                        <span title="Отсутсвует протокол или недостаточно прав, невозможно завершить испытание">
                                            <a class="no-decoration me-1 disabled" href="#">
                                                <i class="fa-light fa-lock-open text-black font-size-35"></i>
                                            </a>
                                        </span>
                                    <?php elseif ($this->data['complete']['may_complete']): ?>
                                        <a class="no-decoration me-1" href="<?= URI ?>/request/complete/<?= $this->data['deal_id'] ?>" title="Завершить испытание">
                                            <i class="fa-light fa-lock-open text-black font-size-35"></i>
                                        </a>
                                    <?php else: ?>
                                        <span title="Завершить испытание невозможно, не для всех проб сформирован протокол или присвоен номер">
                                            <a class="no-decoration me-1 disabled" href="#">
                                                <i class="fa-light fa-lock-open text-black font-size-35"></i>
                                            </a>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <!-- <tr class="<?= $this->data['act_complete']['check'] ? 'table-green' : '' ?>">
                                <td><strong>УПД</strong></td>
                                <td>
                                    <?php if ($this->data['act_complete']['check']): ?>
                                        <?= $this->data['act_complete']['number'] ?>
                                    <?php else: ?>
                                        Не сформирован
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $this->data['act_complete']['date'] ?>
                                </td>
                                <td>
                                    <?= $this->data['act_complete']['date_send'] ?>
                                </td>
                                <td>
                                    <?php if ($this->data['act_complete']['is_disable_form']): ?>
                                        <span title="Заполнить данные акта возможно только на стадии 'Испытания завершины'(Работы в лаборатории завершены)">
                                            <a class="no-decoration me-1 popup-with-form disabled" href="#act-work-modal-form">
                                                <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                            </a>
                                        </span>
                                    <?php else: ?>
                                        <a class="no-decoration me-1 popup-with-form" href="#act-work-modal-form" title="Заполнить данные акта">
                                            <i class="fa-light fa-file-pen text-black font-size-35"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($this->data['act_complete']['is_disable_mail']): ?>
                                        <a class="no-decoration disabled me-1" href="#" title="Отправить клиенту">
                                            <i class="fa-regular fa-envelope-open text-black font-size-35"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="no-decoration me-1 popup-mail"
                                            data-id="<?= $this->data['deal_id'] ?>"
                                            data-type="8"
                                            data-attach="<?= $this->data['act_complete']['attach'] ?>"
                                            href="#email-check"
                                            title="Отправить клиенту">
                                            <svg class="icon" width="35" height="35">
                                                <use xlink:href="<?= URI ?>/assets/images/icons.svg#mail" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($this->data['UPD']['date_request'] || !$this->data['is_end_test']): ?>
                                        <a class="no-decoration me-1 disabled">
                                            Запросить УПД
                                        </a>
                                    <?php else: ?>
                                        <a class="no-decoration me-1 popup-with-form" href="#request-upd-modal-form">
                                            Запросить УПД
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $this->data['UPD']['date_request'] ?>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <form action="<?= URI ?>/request/addComment/<?= $this->data['deal_id'] ?>" method="post">
                        <div class="row">
                            <div class="col-10">
                                <textarea style="min-width: 100%" name="comment" class="form-control" placeholder="Комментарий" required><?= $this->data['comment'] ?></textarea>
                                <input type="hidden" name="oldComment" value="<?= $this->data['comment'] ?>">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary m-0" type="submit" id="button-addon2">Добавить</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel panel-default">
                <header class="panel-heading">
                    История изменений комментариев
                    <span class="tools float-end">
                        <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                        <a href="javascript:;" class="fa fa-chevron-up"></a>
                    </span>
                </header>
                <div class="panel-body">
                    <div class="row mb-2">
                        <div class="col" style="font-weight: bold">Старый комментарий</div>
                        <div class="col" style="font-weight: bold">Новый комментарий</div>
                        <div class="col" style="font-weight: bold">Пользователь</div>
                        <div class="col" style="font-weight: bold">Дата изменения</div>
                    </div>
                    <?php foreach ($this->data['comment_history'] as $value): ?>
                        <div class="row mb-2 <?= $value['bg'] ?>">
                            <div class="col"><?= $value['old_comment'] ?></div>
                            <div class="col"><?= $value['new_comment'] ?></div>
                            <div class="col"><?= $value['user_short'] ?></div>
                            <div class="col"><?= $value['date'] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <a id="close_app" class="btn btn-primary popup-with-form" href="#finish-modal-form">Завершить заявку</a>

            <div class="line-dashed"></div>

            <div class="panel panel-default">
                <header class="panel-heading">
                    Документы клиента
                    <span class="tools float-end">
                        <a href="#" class="fa fa-chevron-down"></a>
                    </span>
                </header>
                <div class="panel-body" style="display: none;">
                    <div class="row file-preview-container">
                        <?php foreach ($this->data['user_files'] as $file): ?>
                            <div class="col-2 file-preview-block d-flex flex-column">
                                <div class="file-preview-img">
                                    <img src="<?= $file['img'] ?>" alt="ico" width="90">
                                </div>
                                <div class="file-preview-title align-center">
                                    <a class="text-decoration-none" href="/ulab/upload/request/<?= $this->data['deal_id'] ?>/<?= $file['name'] ?>" target="_blank"><?= $file['name'] ?></a>
                                </div>
                                <?php if ($this->data['is_managers']): ?>
                                    <div class="file-preview-back flex-column">
                                        <a class="btn btn-danger" href="/ulab/request/deleteFile/<?= $this->data['deal_id'] ?>?file=<?= $file['name'] ?>">Удалить</a>
                                        <a class="btn btn-primary btn-show-file" data-file="<?= $file['name'] ?>" href="#">Просмотр</a>
                                        <a download class="btn btn-success" href="/ulab/upload/request/<?= $this->data['deal_id'] ?>/<?= $file['name'] ?>">Скачать</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="line-dashed"></div>

                    <div class="dropzone-msg">

                    </div>

                    <div>
                        <form id="dropzone-example" class="dropzone dz-clickable" action="/ulab/request/uploadUserFileAjax/<?= $this->data['deal_id'] ?>">
                            <div class="dropzone-previews"></div>
                            <div class="dz-default dz-message"><span>Drop files here to upload</span></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <header class="panel-heading">
                    Технические документы
                    <span class="tools float-end">
                        <a href="#" class="fa fa-chevron-down"></a>
                    </span>
                </header>
                <div class="panel-body" style="display: none;">
                    <div class="row file-preview-container">
                        <?php foreach ($this->data['tech_files'] as $file): ?>
                            <div class="col-2 file-preview-block d-flex flex-column">
                                <div class="file-preview-img">
                                    <img src="<?= $file['img'] ?>" alt="ico" width="90">
                                </div>
                                <div class="file-preview-title align-center">
                                    <a class="text-decoration-none" href="/ulab/upload/request_tech/<?= $this->data['deal_id'] ?>/<?= $file['name'] ?>" target="_blank"><?= $file['name'] ?></a>
                                </div>
                                <?php if ($this->data['is_managers']): ?>
                                    <div class="file-preview-back flex-column">
                                        <a class="btn btn-danger" href="/ulab/request/deleteTechFile/<?= $this->data['deal_id'] ?>?file=<?= $file['name'] ?>">Удалить</a>
                                        <a download class="btn btn-success" href="/ulab/upload/request_tech/<?= $this->data['deal_id'] ?>/<?= $file['name'] ?>">Скачать</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="line-dashed"></div>

                    <div class="dropzone-msg">

                    </div>

                    <div>
                        <form id="dropzone-example" class="dropzone dz-clickable" action="/ulab/request/uploadTechFileAjax/<?= $this->data['deal_id'] ?>">
                            <div class="dropzone-previews"></div>
                            <div class="dz-default dz-message"><span>Drop files here to upload</span></div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="panel panel-default">
                <header class="panel-heading">
                    Списки версий
                    <span class="tools float-end">
                        <a href="#" class="fa fa-chevron-down"></a>
                    </span>
                </header>
                <div class="panel-body" style="display: none;">
                    <table class="table align-middle table-card">
                        <thead>
                            <tr class="table-light">
                                <th scope="col">Протокол</th>
                                <th scope="col">КП</th>
                                <th scope="col">Договор</th>
                                <th scope="col">Прил. к договору (тз)</th>
                                <th scope="col">Счет</th>
                                <th scope="col">УПД</th>
                                <th scope="col">Фото испытаний
                                    <form id="target" enctype="multipart/form-data" action="/ulab/request/dwnImage/<?= $this->data['deal_id'] ?>" method="POST">
                                        <label title="Загрузить протокол" id="image-svg" for="file" class=""></label>
                                        <input id="file" type="file" name="photo" onchange="form.submit()" />
                                    </form>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="align-top">
                                    <?php foreach ($this->data['file']['protocol'] as $item): ?>
                                        <span><strong>Данные протокола №<?= $item['number'] ?></strong></span>
                                        <br>
                                        <?php foreach ($item['files'] as $file): ?>
                                            <a target="_blank" href="<?= $item['dir'] ?><?= $file ?>?v=<?= rand() ?>"><?= $file ?></a><br>
                                        <?php endforeach; ?>
                                        <br>
                                    <?php endforeach; ?>
                                </td>
                                <td class="align-top">
                                    <?php foreach ($this->data['file']['kp']['files'] as $item): ?>
                                        <a target="_blank" href="<?= $this->data['file']['kp']['dir'] ?><?= $item ?>"><?= $item ?></a><br>
                                    <?php endforeach; ?>
                                </td>
                                <td class="align-top">
                                    <?php foreach ($this->data['file']['order']['files'] as $item): ?>
                                        <a target="_blank"
                                            href="<?= $this->data['file']['order']['dir'] . $item['original'] ?>">
                                            <?= $item['display'] ?>
                                        </a><br>
                                    <?php endforeach; ?>
                                </td>
                                <td class="align-top">
                                    <?php foreach ($this->data['file']['tz']['files'] as $item): ?>
                                        <a target="_blank" href="<?= $this->data['file']['tz']['dir'] ?><?= $item ?>"><?= $item ?></a><br>
                                    <?php endforeach; ?>
                                </td>
                                <td class="align-top">
                                    <?php foreach ($this->data['file']['invoice']['files'] as $item): ?>
                                        <a target="_blank" href="<?= $this->data['file']['invoice']['dir'] ?><?= $item ?>"><?= $item ?></a><br>
                                    <?php endforeach; ?>
                                </td>
                                <td class="align-top">
                                    <?php foreach ($this->data['file']['act']['files'] as $item): ?>
                                        <a target="_blank" href="<?= $this->data['file']['act']['dir'] ?><?= $item ?>"><?= $item ?></a><br>
                                    <?php endforeach; ?>
                                </td>
                                <td class="align-top">
                                    <?php foreach ($this->data['file']['photo']['files'] as $item): ?>
                                        <a target="_blank" href="<?= $this->data['file']['photo']['dir'] ?><?= $item ?>"><?= $item ?></a><br>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="line-dashed"></div>


            <form id="act-probe-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
                action="<?= URI ?>/probe/insertUpdateActProbe/<?= $this->data['deal_id'] ?>" method="post">
                <div class="title mb-3 h-2">
                    Данные акта приемки проб
                </div>

                <div class="line-dashed-small"></div>

                <input type="hidden" name="act[act_id]" value="<?= $this->data['sample']['act_id'] ?>">
                <input type="hidden" name="act[deal_id]" value="<?= $this->data['deal_id'] ?>">
                <input type="hidden" name="act[tz_id]" value="<?= $this->data['tz_id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Тип</label>
                    <select type="date" class="form-control" name="act[actType]">
                        <option value="1" <?= $this->data['sample']['act_type'] == 1 ? 'selected' : '' ?>>Акт приемки проб</option>
                        <option value="2" <?= $this->data['sample']['act_type'] == 2 ? 'selected' : '' ?>>Контрольный лист</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Дата акта ПП</label>
                    <input type="date" class="form-control" name="act[actDate]" value="<?= $this->data['sample']['date_act'] ?: date('Y-m-d') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Место отбора проб</label>
                    <input type="text" class="form-control" name="act[samplePlace]" value="<?= htmlentities($this->data['sample']['place_probe']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Дата отбора проб</label>
                    <input type="date" class="form-control" name="act[sampleDate]" value="<?= $this->data['sample']['date_probe'] ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Описание объекта</label>
                    <input type="text" class="form-control" name="act[description]" value="<?= $this->data['sample']['description'] ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Привязка к карьеру</label>
                    <select type="text" class="form-control" name="act[quarry]">
                        <option value=''>Выберите карьер</option>
                        <?php foreach ($this->data['quarry'] as $key => $value): ?>
                            <option value='<?= $value['ID'] ?>' <?= $this->data['sample']['quarry_id'] == $value['ID'] ? 'selected' : '' ?>><?= $value['NAME'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input act-manual-edit" name='act[exampleCheck1]' id="exampleCheck1" <?= $this->data['sample']['selection_type'] == 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="exampleCheck1">Пробы отобраны не заказчиком</label>
                </div>
                <div class="<?= $this->data['sample']['selection_type'] != 1 ? 'visually-hidden' : '' ?> act-manual-block">
                    <div class="mb-3">
                        <label class="form-label">Отбор проб произвел</label>
                        <input type="text" class="form-control" name="act[sampleMaker]" value="<?= $this->data['sample']['PROBE_PROIZV'] ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Пробы предоставил</label>
                    <input type="text" class="form-control" name="act[deliveryman]" value="<?= $this->data['sample']['deliveryman'] ?>">
                </div>

                <div class="line-dashed-small"></div>

                <button type="submit" class="btn btn-primary submit-act-probe">Сохранить</button>
            </form>

            <form id="act-work-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/generator/actVR/" method="post">
                <div class="title mb-3 h-2">
                    Данные УПД
                    <div class="line-dashed-small"></div>

                    <div class="mb-3">
                        <label class="form-label">Номер акта</label>
                        <input type="number" name="actNumber" step="1" class="form-control" <?= $this->data['act_vr']['NUMBER'] ? '' : 'readonly' ?> value="<?= $this->data['act_vr']['NUMBER'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Дата акта</label>
                        <input type="date" name="actDate" class="form-control" value="<?= $this->data['act_vr']['DATE'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">К платежно-расчетному документу</label>
                        <input type="text" name="paydocs" class="form-control" value="<?= $this->data['act_vr']['paydocs'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Руководитель</label>
                        <select name="lead" class="form-control" required>
                            <!--            <option value="1">Ахтямов Э.Р.</option>-->
                            <option value="2">Габдрахманова А.А.</option>
                            <!--            <option value="3">Молчанова Е.Н.</option>-->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email отправки:</label>
                        <input type="text" name="Email" list="mail_list" class="form-control" value="<?= $this->data['act_complete']['email'] ?>">
                        <datalist id="mail_list">
                            <?php foreach ($this->data['list_email'] as $email): ?>
                                <option value="<?= $email ?>"><?= $email ?></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
                    <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">

                    <div class="line-dashed-small"></div>

                    <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>

            <form id="request-upd-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/request/requestUPD/" method="post">
                <div class="title mb-3 h-2">
                    Запрос УПД
                    <div class="line-dashed-small"></div>

                    <div class="mb-3">
                        <label for="request-upd-message-text" class="form-label">Введите сопроводительный текст:</label>
                        <textarea name="text_upd" id="request-upd-message-text" rows="5"
                            placeholder="Ваш текст..."></textarea>
                    </div>

                    <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">

                    <div class="line-dashed-small"></div>

                    <button type="submit" class="btn btn-primary">Запросить</button>
            </form>

            <form id="pay-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/request/setOplata/" method="post">
                <div class="title mb-3 h-2">
                    Данные оплаты
                </div>

                <div class="line-dashed-small"></div>

                <div class="mb-3">
                    <label class="form-label">Оплата</label>
                    <input type="number" name="pay" step="0.01" max="<?= $this->data['payment']['surcharge'] ?>" class="form-control" value="<?= $this->data['payment']['surcharge'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Дата оплаты</label>
                    <input type="date" name="payDate" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>

                <input name="deal_id" value="<?= $this->data['deal_id'] ?>" type="hidden">
                <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">
                <input name="surcharge" value="<?= $this->data['payment']['surcharge'] ?>" type="hidden">

                <div class="line-dashed-small"></div>

                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>

            <form id="email-check" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/mail.php"
                method="get">
                <div class="title mb-3 h-2">
                    Сопроводительный текст к письму
                </div>
                <div class="line-dashed-small"></div>
                <div class="mb-3">
                    <label for="additional-message-text" class="form-label">Введите сопроводительный текст:</label>
                    <textarea name="BODY" id="additional-message-text" rows="5"
                        placeholder="Ваш текст..."></textarea>
                </div>

                <?php
                $hideMailList = count($this->data['mail_list']) < 1 ? 'd-none' : '';
                ?>
                <div class="mail-list <?= $hideMailList ?>">
                    <div class="mb-3">
                        <input type="checkbox" name="EMAIL[]" value='<?= $this->data['email'] ?>' checked>
                        <label class="form-label"><?= $this->data['email'] ?></label>
                    </div>
                    <?php foreach ($this->data['mail_list'] as $key => $email): ?>
                        <div class="mb-3">
                            <input type="checkbox" name="EMAIL[]" value='<?= $email ?>'>
                            <label class="form-label"><?= $email ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <input name="TZ_ID" value="<?= $this->data['tz_id'] ?>" type="hidden">
                <input name="NAME" value="<?= $this->data['user']['name'] ?>" type="hidden">
                <input name="ID" id="ID" value="" type="hidden">
                <input name="ATTACH" id="ATTACH" value="" type="hidden">
                <input name="TITLE" id="TITLE" value="" type="hidden">
                <input name="SIG" id="SIG" value="" type="hidden">
                <input name="PDF" id="PDF" value="" type="hidden">
                <input name="YEAR" id="YEAR" value="" type="hidden">
                <input name="ID_P" id="ID_P" value="" type="hidden">
                <input name="TYPE" id="TYPE" value="" type="hidden">
                <input name="OL" id="OL" value="" type="hidden">
                <div class="line-dashed-small"></div>

                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>

            <form id="group-email-check" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/mail.php"
                method="get">
                <div class="title mb-3 h-2">
                    Сопроводительный текст к письму
                </div>
                <div class="line-dashed-small"></div>
                <div class="mb-3">
                    <label for="group-additional-message-text" class="form-label">Введите сопроводительный текст:</label>
                    <textarea name="BODY" id="group-additional-message-text" rows="5"
                        placeholder="Ваш текст..."></textarea>
                </div>

                <?php
                $hideMailList = count($this->data['mail_list']) < 1 ? 'd-none' : '';
                ?>
                <div class="mail-list <?= $hideMailList ?>">
                    <div class="mb-3">
                        <input type="checkbox" name="EMAIL[]" value='<?= $this->data['email'] ?>' checked>
                        <label class="form-label"><?= $this->data['email'] ?></label>
                    </div>
                    <?php foreach ($this->data['mail_list'] as $key => $email): ?>
                        <div class="mb-3">
                            <input type="checkbox" name="EMAIL[]" value='<?= $email ?>'>
                            <label class="form-label"><?= $email ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <input name="ID" id="GROUP-ID" value="" type="hidden">
                <input name="ID2" id="GROUP-ID2" value="" type="hidden">
                <input name="TZ_ID" id="GROUP-TZ_ID" value="" type="hidden">
                <input name="NAME" id="GROUP-NAME" value="" type="hidden">
                <input name="ATTACH1" id="GROUP-ATTACH1" value="" type="hidden">
                <input name="ATTACH2" id="GROUP-ATTACH2" value="" type="hidden">
                <input name="ATTACH3" id="GROUP-ATTACH3" value="" type="hidden">
                <input name="TITLE" id="GROUP-TITLE" value="" type="hidden">
                <input name="TYPE" value="7" type="hidden">

                <div class="line-dashed-small"></div>

                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>

            <form id="group-protocol-email-check" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/mail.php"
                method="get">
                <div class="title mb-3 h-2">
                    Сопроводительный текст к письму
                </div>
                <div class="line-dashed-small"></div>
                <div class="mb-3">
                    <label for="group-additional-message-text" class="form-label">Введите сопроводительный текст:</label>
                    <textarea name="BODY" id="group-protocol-additional-message-text" rows="5"
                        placeholder="Ваш текст..."></textarea>
                </div>

                <?php
                $hideMailList = count($this->data['mail_list']) < 1 ? 'd-none' : '';
                ?>
                <div class="mail-list <?= $hideMailList ?>">
                    <div class="mb-3">
                        <input type="checkbox" name="EMAIL[]" value='<?= $this->data['email'] ?>' checked>
                        <label class="form-label"><?= $this->data['email'] ?></label>
                    </div>
                    <?php foreach ($this->data['mail_list'] as $key => $email): ?>
                        <div class="mb-3">
                            <input type="checkbox" name="EMAIL[]" value='<?= $email ?>'>
                            <label class="form-label"><?= $email ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <input name="ID" id="PROTOCOL-GROUP-ID" value="" type="hidden">
                <input name="ID2" id="PROTOCOL-GROUP-ID2" value="" type="hidden">
                <input name="TZ_ID" id="PROTOCOL-GROUP-TZ_ID" value="" type="hidden">
                <input name="NAME" id="PROTOCOL-GROUP-NAME" value="" type="hidden">
                <input name="TITLE" id="PROTOCOL-GROUP-TITLE" value="" type="hidden">
                <input name="TYPE" value="12" type="hidden">

                <input type="hidden" id="PROTOCOL-PROTOCOLS" name="documents">

                <div class="line-dashed-small"></div>

                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>

            <div id="finish-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
                <div class="title mb-3 h-2">
                    Выберите результат, с которым будет закрыта сделка
                </div>

                <div class="line-dashed-small"></div>

                <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">

                <div class="row mb-1">
                    <div class="col"><button data-stage="4" style="min-width: 100%" class="akt-finish btn btn-primary">Акты отправлены</button></div>
                    <div class="col">
                        <a href="#finish-modal-form-2" style="min-width: 100%" class="deal-lost btn btn-primary popup-with-form">Сделка проиграна</a>
                    </div>
                </div>

                <div class="mb-1">
                    <button data-stage="WON" style="min-width: 100%" class="akt-finish btn btn-primary">Акты получены, сделка завершена</button>
                </div>

                <div>
                    <button data-stage="WON-NO-ACT" style="min-width: 100%" class="akt-finish btn btn-primary">Акты не требуются, заявка учтена в основной</button>
                </div>
            </div>

            <div id="finish-modal-form-2" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
                <div class="title mb-3 h-2">
                    Закрытие сделки
                </div>

                <div class="line-dashed-small"></div>

                <input name="tz_id" value="<?= $this->data['tz_id'] ?>" type="hidden">

                <div class="mb-1">
                    <button data-stage="LOSE" data-text="Сделка не состоялась" style="min-width: 100%" class="akt-finish btn btn-primary">Сделка не состоялась</button>
                </div>
                <div class="mb-1">
                    <button data-stage="5" data-text="Не устроила цена" style="min-width: 100%" class="akt-finish btn btn-primary">Не устроила цена</button>
                </div>
                <div class="mb-1">
                    <button data-stage="6" data-text="Заказчик не выходит на связь" style="min-width: 100%" class="akt-finish btn btn-primary">Заказчик не выходит на связь</button>
                </div>
                <div class="mb-1">
                    <button data-stage="7" data-text="Не можем провести испытания" style="min-width: 100%" class="akt-finish btn btn-primary">Не можем провести испытания</button>
                </div>
                <div class="mb-1">
                    <button data-stage="8" data-text="Истек срок заявки (60 дней)" style="min-width: 100%" class="akt-finish btn btn-primary">Истек срок заявки (60 дней)</button>
                </div>
                <div class="mb-1">
                    <button data-stage="9" data-text="Заказчик отказался" style="min-width: 100%" class="akt-finish btn btn-primary">Заказчик отказался</button>
                </div>
                <div class="mb-1">
                    <button data-stage="10" data-text="Не устроили сроки" style="min-width: 100%" class="akt-finish btn btn-primary">Не устроили сроки</button>
                </div>
                <div class="mb-1">
                    <button style="min-width: 100%" class="open-lose-comment btn btn-primary">Другое (комментарий)</button>
                </div>
                <div class="mb-1 d-flex flex-column lose-com-block d-none">
                    <textarea class="form-control lose-comment mb-1"></textarea>
                    <button style="min-width: 50%; margin: 0 auto" data-stage="11" class="akt-finish btn btn-primary">Отправить</button>
                </div>
            </div>

            <?php if ($this->data['lose_comment']): ?>
                <div id="notify_lose" class="bg-light mfp-hide col-xl-8 col-lg-9 m-auto p-3 position-relative">
                    <div class="row">
                        <div class="col">
                            <div class="title mb-3 h-2">
                                Внимание сделка проиграна
                            </div>

                            <div class="line-dashed-small"></div>

                            <div class="body mb-3 row">
                                <div>Причина: <?= $this->data['lose_comment'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div id="show_document" class="bg-light mfp-hide col-xl-8 col-lg-9 m-auto p-3 position-relative">
                <div class="modal-dialog modal-dialog-centered modal-document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="documentModalLabel">Просмотр документа</h5>
                        </div>
                        <div class="modal-body d-flex justify-content-center">
                            <iframe src="" id="document-viewer" width='67%' height='900px'></iframe>
                        </div>
                    </div>
                </div>
            </div>