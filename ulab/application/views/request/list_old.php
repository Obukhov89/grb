<div class="filters mb-4">
    <div class="row">
        <div class="col">
            <input type="date" id="inputDateStart" class="form-control filter filter-date-start" value="<?= $this->data['date_start'] ?? '' ?>" placeholder="Введите дату начала:">
        </div>

        <div class="col">
            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end" value="<?= date('Y-m-d') ?>" placeholder="Введите дату окончания:">
        </div>

        <div class="col">
            <select id="selectStage" class="form-control filter filter-stage">
                <option value='0' selected>Все стадии</option>
                <option value="1">Пробы не поступили</option>
                <option value='2'>Пробы поступили</option>
                <option value='12'>Пробы не приняты</option>
                <option value='3'>Проводятся испытания</option>
                <option value='4'>Испытания завершены</option>
                <option value='5'>Заявка неуспешна</option>
                <option value='6'>Заявка не оплачена</option>
                <option value='7'>Заявка оплачена не полностью</option>
                <option value='8'>По заявке переплата</option>
                <option value='9'>Заявка оплачена полностью</option>
                <option value='10'>Все кроме новых и неуспешных</option>
                <option value='11'>Успешно завершенные</option>
                <option value='wait_won'>Ожидают завершения</option>
                <option value='wait_lose'>Ожидают закрытия</option>
                <option value='for_meating'>Для собрания</option>
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
    <th scope="col" class="text-nowrap">Cрок до</th>
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

<div class='arrowLeft'>
    <svg class="bi" width="40" height="40">
        <use xlink:href="<?=URI?>/assets/images/icons.svg#arrow-left"/>
    </svg>
</div>
<div class='arrowRight'>
    <svg class="bi" width="40" height="40">
        <use xlink:href="<?=URI?>/assets/images/icons.svg#arrow-right"/>
    </svg>
</div>

<?php if ( !empty($this->data['tz_under_consideration']) || !empty($this->data['probe_in_lab']) ): ?>
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
                                <td class=""><?=$row['REQUEST_TITLE']?></td>
                                <td class=""><?=$row['COMPANY_TITLE']?></td>
                                <td class="">
                                    <a href="<?=URI?>/request/card/<?=$row['ID_Z']?>">Карточка</a>
                                </td>
                                <td class="">
                                    <a href="<?=URI?>/requirement/card/<?=$row['tz_id']?>">ТЗ</a>
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
							<?php if (count($res) > 5): ?>
								<tr class="text-center probe_row bg-light-secondary">
									<td class="">
										<a href="<?=URI?>/request/card/<?=$res[0]['ID_Z']?>"><?=$res[0]['REQUEST_TITLE']?></a>
									</td>
									<td>
										<a class="more-probe" data-request-id = <?=$k?>><?=count($res)?> проб</a>
									</td>
									<td class=""><?=$res[0]['COMPANY_TITLE']?></td>
									<td class=""><a class="accept_all" data-id="<?=$res[0]['ID_Z']?>">Принять все</a></td>
								</tr>
								<?php foreach ($res as $row): ?>
									<tr class="text-center probe_child_<?=$k?> probe_row d-none">
										<td class="">
											<a href="<?=URI?>/request/card/<?=$row['ID_Z']?>"><?=$row['REQUEST_TITLE']?></a>
										</td>
										<td>
											<?=$row['cipher']?>
										</td>
										<td class=""><?=$row['COMPANY_TITLE']?></td>
										<td class="">
											<a class="accept_probe" data-id="<?=$row['id']?>">Принять</a>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<?php foreach ($res as $row): ?>
									<tr class="text-center probe_row">
										<td class="">
											<a href="<?=URI?>/request/card/<?=$row['ID_Z']?>"><?=$row['REQUEST_TITLE']?></a>
										</td>
										<td>
											<?=$row['cipher']?>
										</td>
										<td class=""><?=$row['COMPANY_TITLE']?></td>
										<td class="">
											<a class="accept_probe" data-id="<?=$row['id']?>">Принять</a>
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
							<?php if (count($res) > 5): ?>
								<tr class="text-center probe_row bg-light-secondary">
									<td class="">
										<a href="<?=URI?>/request/card/<?=$res[0]['ID_Z']?>"><?=$res[0]['REQUEST_TITLE']?></a>
									</td>
									<td>
										<a class="more-probe" data-request-id = <?=$k?>><?=count($res)?> проб</a>
									</td>
									<td class=""><?=$res[0]['COMPANY_TITLE']?></td>
									<td class=""><a class="accept_all" data-id="<?=$res[0]['ID_Z']?>">Принять все</a></td>
								</tr>
								<?php foreach ($res as $row): ?>
									<tr class="text-center probe_child_<?=$k?> probe_row d-none">
										<td class="">
											<a href="<?=URI?>/request/card/<?=$row['ID_Z']?>"><?=$row['REQUEST_TITLE']?></a>
										</td>
										<td>
											<?=$row['cipher']?>
										</td>
										<td class=""><?=$row['COMPANY_TITLE']?></td>
										<td class="">
											<a class="accept_probe" data-id="<?=$row['id']?>">Принять</a>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<?php foreach ($res as $row): ?>
									<tr class="text-center probe_row">
										<td class="">
											<a href="<?=URI?>/request/card/<?=$row['ID_Z']?>"><?=$row['REQUEST_TITLE']?></a>
										</td>
										<td>
											<?=$row['cipher']?>
										</td>
										<td class=""><?=$row['COMPANY_TITLE']?></td>
										<td class="">
											<a class="accept_probe" data-id="<?=$row['id']?>">Принять</a>
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
                                    <a href="<?=URI?>/requirement/card/<?=$row['ID']?>"><?=$row['REQUEST_TITLE']?></a>
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

<?php if ( !empty($this->data['confirm_not_account']) ): ?>
    <div id="notify_leader" class="bg-light mfp-hide col-xl-5 col-lg-7 m-auto p-3 position-relative">
        <div class="row">
            <div class="col">
                <div class="title mb-3 h-2">
                    Технические задание подтверждено
                </div>

                <div class="line-dashed-small"></div>

                <div class="body mb-3">
                    <table class="table">
                        <tbody>
                        <?php foreach ($this->data['confirm_not_account'] as $row): ?>
                            <tr class="text-center">
                                <td class="">
                                    <a href="<?=URI?>/request/card/<?=$row['ID_Z']?>"><?=$row['REQUEST_TITLE']?></a>
                                </td>
                                <td class=""><?=$row['COMPANY_TITLE']?></td>
                                <td class="">
                                    <a href="<?=URI?>/requirement/card/<?=$row['tz_id']?>">ТЗ</a>
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

