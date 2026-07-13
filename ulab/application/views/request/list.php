<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link popup-help" href="/ulab/help/LIMS_Manual_Stand/Lists/Request_list/request_list.html" title="ПОМОГИТЕ">
                    <i class="fa-solid fa-question"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<div class="filters mb-4">
    <div class="row">
        <div class="col">
            <input type="date" id="inputDateStart" class="form-control filter filter-date-start" value="<?= $this->data['date_start'] ?? '' ?>" placeholder="Введите дату начала:">
        </div>

        <div class="col">
            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end" value="<?= date('Y-m-d') ?>" placeholder="Введите дату окончания:">
        </div>

        <div class="col">
            <select id="selectStageGroup" class="form-control select2 filter filter-stage-group" multiple>
                <option value='0' style="font-weight: bold" selected>Все статусы</option>
                <?php foreach ($this->data['statuses'] as $key => $value): ?>
                    <?php foreach ($value['statuses_list'] as $k => $v): ?>
                        <option value="<?= $v['status_id'] ?>"><?= $v['status_name'] ?></option>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col">
            <select id="selectLab" class="form-control filter filter-lab">
                <option value='0' selected>Bсе лаборатории</option>
                <?php if ($this->data['lab']): ?>
                    <?php foreach ($this->data['lab'] as $lab): ?>
                        <option value="<?= $lab['DEPARTMENT'] ?? '' ?>"><?= $lab['NAME'] ?? '' ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="col">
            <select id="selectCertification" class="form-control filter filter-certification">
                <option value='all' selected>Все заявки</option>
                <option value='certification'>Только сертификационные (С)</option>
                <option value='regular'>Только обычные</option>
            </select>
        </div>

        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
        </div>
    </div>
</div>

<table id="journal_requests" class="table table-striped journal">
    <thead>
        <tr class="table-light">
            <th scope="col"></th>
            <th scope="col" class="text-nowrap">Заявка</th>
            <th scope="col" class="text-nowrap">Дата</th>
            <th scope="col" class="text-nowrap">Клиент</th>
            <th scope="col" class="text-nowrap">Объект испытаний</th>
            <th scope="col" class="text-nowrap">Ответственный</th>
            <th scope="col" class="text-nowrap">ТЗ</th>
            <th scope="col" class="text-nowrap">Договор</th>
            <th scope="col" class="text-nowrap">Акт ПП</th>
            <th scope="col" class="text-nowrap">Счет</th>
            <th scope="col" class="text-nowrap">Стоимость</th>
            <th scope="col" class="text-nowrap">Дата опл</th>
            <th scope="col" class="text-nowrap">Рез-ты исп</th>
            <th scope="col" class="text-nowrap">Протокол</th>
            <th scope="col" class="text-nowrap">Фото исп</th>
            <th scope="col" class="text-nowrap">Срок до</th>
        </tr>
        <tr class="header-search">
            <th scope="col"></th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <select class="form-control search">
                    <option value=""></option>
                    <option value="n">Не подписано</option>
                    <option value="y">Подписано</option>
                </select>
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <input type="text" class="form-control search" disabled>
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" style="width: 600px !important;">
    <div class="offcanvas-header">
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">

    </div>
</div>

<div class='arrowLeft'>
    <svg class="bi" width="40" height="40">
        <use xlink:href="<?= URI ?>/assets/images/icons.svg#arrow-left" />
    </svg>
</div>
<div class='arrowRight'>
    <svg class="bi" width="40" height="40">
        <use xlink:href="<?= URI ?>/assets/images/icons.svg#arrow-right" />
    </svg>
</div>

<input type="hidden" id="inp-id-usr" value="<?= $this->data['user_id'] ?>">

<?php if (!empty($this->data['tz_under_consideration']) || !empty($this->data['probe_in_lab'])): ?>
    <div id="notify_leader" class="bg-light mfp-hide col-xl-8 col-lg-9 m-auto p-3 position-relative">
        <div class="row">
            <div class="col">
                <div class="title mb-3 h-2">
                    Технические задания на рассмотрении
                </div>

                <div class="line-dashed-small"></div>

                <div class="body mb-3">
                    <table class="table">
                        <tbody>
                            <?php foreach ($this->data['tz_under_consideration'] as $row): ?>
                                <tr class="text-center">
                                    <td class=""><?= $row['REQUEST_TITLE'] ?></td>
                                    <td class=""><?= $row['COMPANY_TITLE'] ?></td>
                                    <td class="">
                                        <a href="<?= URI ?>/request/card/<?= $row['ID_Z'] ?>">Карточка</a>
                                    </td>
                                    <td class="">
                                        <a href="<?= URI ?>/requirement/card/<?= $row['tz_id'] ?>">ТЗ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col">
                <div class="title mb-3 h-2">
                    Поступившие пробы
                </div>

                <div class="line-dashed-small"></div>
                <div class="body">
                    <table class="table">
                        <tbody>
                            <?php foreach ($this->data['probe_in_lab'] as $k => $res): ?>
                                <?php if (count($res['data']) > 5): ?>
                                    <tr class="text-center probe_row bg-light-secondary">
                                        <td class="">
                                            <a href="<?= URI ?>/request/card/<?= $res['data'][0]['ID_Z'] ?>"><?= $res['data'][0]['REQUEST_TITLE'] ?></a>
                                        </td>
                                        <td>
                                            <a class="more-probe" data-request-id=<?= $k ?>><?= count($res['data']) ?> проб</a>
                                        </td>
                                        <td class=""><?= $res['data'][0]['COMPANY_TITLE'] ?></td>
                                        <td class="">
                                            <?php
                                            echo ($res['sended_count'] ?? '0') . "/" . count($res['data']) . " готовы";
                                            ?>
                                        </td>
                                        <td class=""><a class="accept_all" data-id="<?= $res['data'][0]['ID_Z'] ?>" data-id-z="<?= $res['data'][0]['ID_Z'] ?>">Принять все</a></td>
                                    </tr>
                                    <?php foreach ($res['data'] as $key => $row): ?>
                                        <tr class="text-center probe_child_<?= $k ?> probe_row d-none">
                                            <td class="">
                                                <a href="<?= URI ?>/request/card/<?= $res['data']['ID_Z'] ?>"><?= $row['REQUEST_TITLE'] ?></a>
                                            </td>
                                            <td>
                                                <?= $row['cipher'] ?>
                                            </td>
                                            <td class=""><?= $row['COMPANY_TITLE'] ?></td>
                                            <td class="">
                                                <?php if ($row['date_send']): ?>
                                                    <span style="color: green;"><?= $row['date_send'] ?></span>
                                                <?php else: ?>
                                                    <span style="color: red;">Проба не готова</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="">
                                                <a class="accept_probe" data-id="<?= $row['id'] ?>" data-id-z="<?= $row[0]['ID_Z'] ?>">Принять</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php foreach ($res['data'] as $key => $row): ?>
                                        <tr class="text-center probe_row">
                                            <td class="">
                                                <a href="<?= URI ?>/request/card/<?= $row['ID_Z'] ?>"><?= $row['REQUEST_TITLE'] ?></a>
                                            </td>
                                            <td>
                                                <?= $row['cipher'] ?>
                                            </td>
                                            <td class=""><?= $row['COMPANY_TITLE'] ?></td>
                                            <td class="">
                                                <?php if ($row['date_send']): ?>
                                                    <span style="color: green;"><?= $row['date_send'] ?></span>
                                                <?php else: ?>
                                                    <span style="color: red;">Проба не готова</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="">
                                                <a class="accept_probe" data-id="<?= $row['id'] ?>" data-id-z="<?= $res[0]['ID_Z'] ?>">Принять</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col">
                <div class="title mb-3 h-2">
                    Оплаченные пробы
                </div>

                <div class="line-dashed-small"></div>

                <div class="body">
                    <table class="table">
                        <tbody>
                            <?php foreach ($this->data['probe_in_lab_payed'] as $k => $res): ?>
                                <!--                            --><?php ?>
                                <?php if (count($res['data']) > 5): ?>
                                    <tr class="text-center probe_row bg-light-secondary">
                                        <td class="">
                                            <a href="<?= URI ?>/request/card/<?= $res['data'][0]['ID_Z'] ?>"><?= $res['data'][0]['REQUEST_TITLE'] ?><?= $res['data'][0]['certificate'] ?></a>
                                        </td>
                                        <td>
                                            <a class="more-probe" data-request-id=<?= $k ?>><?= count($res['data']) ?> проб</a>
                                        </td>
                                        <td class=""><?= $res['data'][0]['COMPANY_TITLE'] ?></td>
                                        <td class="">
                                            <?php
                                            echo ($res['sended_count'] ?? '0') . "/" . (count($res['data'])) . " готовы";
                                            ?>
                                        </td>
                                        <td class=""><a class="accept_all" data-id="<?= $res['data'][0]['ID_Z'] ?>">Принять все</a></td>
                                    </tr>
                                    <?php foreach ($res['data'] as $key => $row): ?>
                                        <tr class="text-center probe_child_<?= $k ?> probe_row d-none">
                                            <td class="">
                                                <a href="<?= URI ?>/request/card/<?= $row['ID_Z'] ?>"><?= $row['REQUEST_TITLE'] ?></a>
                                            </td>
                                            <td>
                                                <?= $row['cipher'] ?>
                                            </td>
                                            <td class=""><?= $row['COMPANY_TITLE'] ?></td>
                                            <td class="">
                                                <?php if ($row['date_send']): ?>
                                                    <span style="color: green;"><?= $row['date_send'] ?></span>
                                                <?php else: ?>
                                                    <span style="color: red;">Проба не готова</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="">
                                                <a class="accept_probe" data-id="<?= $row['id'] ?>">Принять</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php foreach ($res['data'] as $key => $row): ?>
                                        <tr class="text-center probe_row">
                                            <td class="">
                                                <a href="<?= URI ?>/request/card/<?= $row['ID_Z'] ?>"><?= $row['REQUEST_TITLE'] ?><?= $row['certificate'] ?></a>
                                            </td>
                                            <td>
                                                <?= $row['cipher'] ?>
                                            </td>
                                            <td class=""><?= $row['COMPANY_TITLE'] ?></td>
                                            <td class="">
                                                <?php if ($row['date_send']): ?>
                                                    <span style="color: green;"><?= $row['date_send'] ?></span>
                                                <?php else: ?>
                                                    <span style="color: red;">Проба не готова</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="">
                                                <a class="accept_probe" data-id="<?= $row['id'] ?>">Принять</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col">
                <div class="title mb-3 h-2">
                    Не выбраны ответственные
                </div>

                <div class="line-dashed-small"></div>

                <div class="body mb-3">
                    <table class="table">
                        <tbody>
                            <?php foreach ($this->data['request_list_not_assigned'] as $row): ?>
                                <tr class="text-center">
                                    <td class="">
                                        <a href="<?= URI ?>/requirement/card/<?= $row['ID'] ?>"><?= $row['REQUEST_TITLE'] ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($this->data['not_sended_tz'])): ?>
    <div id="notify_leader" class="bg-light mfp-hide col-xl-5 col-lg-7 m-auto p-3 position-relative" style="width: 1200px">
        <div class="row">
            <div class="col">
                <div class="title mb-3 h-2">
                    ТЗ не отправлено на подтверждение
                </div>

                <div class="line-dashed-small"></div>

                <div class="body mb-3">
                    <table class="table">
                        <tbody>
                            <?php foreach ($this->data['not_sended_tz'] as $row): ?>
                                <tr class="text-center">
                                    <td class="">
                                        <a href="<?= URI ?>/request/card/<?= $row['ID_Z'] ?>"><?= $row['REQUEST_TITLE'] ?></a>
                                    </td>
                                    <td class=""><?= $row['COMPANY_TITLE'] ?></td>
                                    <td class="">
                                        <a href="<?= URI ?>/requirement/card_new_new/<?= $row['ID'] ?>">ТЗ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col">
                <div class="title mb-3 h-2">
                    ТЗ возвращено на доработку
                </div>

                <div class="line-dashed-small"></div>

                <div class="body mb-3">
                    <table class="table">
                        <tbody>
                            <?php foreach ($this->data['returned_tz'] as $row): ?>
                                <tr class="text-center">
                                    <td class="">
                                        <a href="<?= URI ?>/request/card/<?= $row['ID_Z'] ?>"><?= $row['REQUEST_TITLE'] ?></a>
                                    </td>
                                    <td class=""><?= $row['COMPANY_TITLE'] ?></td>
                                    <td class="">
                                        <a href="<?= URI ?>/requirement/card_new_new/<?= $row['ID'] ?>">ТЗ</a>
                                    </td>
                                </tr>
                                <tr class="">
                                    <td colspan="3">
                                        <div class="d-flex flex-column">
                                            <?php foreach ($row['comments'] as $value): ?>
                                                <span class="text-danger"><?= $value['user']['short_name'] . ': ' . $value['desc'] ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col">
                <div class="title mb-3 h-2">
                    ТЗ подтверждено
                </div>

                <div class="line-dashed-small"></div>

                <div class="body mb-3">
                    <table class="table">
                        <tbody>
                            <?php foreach ($this->data['confirmed_tz'] as $row): ?>
                                <tr class="text-center">
                                    <td class="">
                                        <a href="<?= URI ?>/request/card/<?= $row['ID_Z'] ?>"><?= $row['REQUEST_TITLE'] ?></a>
                                    </td>
                                    <td class=""><?= $row['COMPANY_TITLE'] ?></td>
                                    <td class="">
                                        <a href="<?= URI ?>/requirement/card_new_new/<?= $row['ID'] ?>">ТЗ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="line-dashed"></div>

<!--<a href="/requests_test.php">Вернуться на старый дизайн</a>-->