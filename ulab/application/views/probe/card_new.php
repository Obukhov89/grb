<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/request/list/" title="Вернуться к списку">
                    <svg class="icon" width="20" height="20">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#list"/>
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/request/card/<?=$this->data['deal_id']?>" title="Вернуться в карточку">
                    <svg class="icon" width="20" height="20">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#card"/>
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="/protocol_generator/label_probe_all.php?ID=<?=$this->data['deal_id']?>&TZ_ID=<?=$this->data['tz_id']?>&ACT_ID=<?= $this->data['act_id'] ?>"
                   title="Скачать этикетки на все пробы">
                    <svg class="icon" width="20" height="20">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#download"/>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</header>

<h2 class="d-flex mb-3">
    Заявка <?= $this->data['deal_title'] ?? '' ?>
</h2>


<div class="panel panel-default">
    <header class="panel-heading">
        Данные акта
        <span class="tools float-end">
                <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                <a href="#" class="fa fa-chevron-up"></a>
             </span>
    </header>
    <div class="panel-body">
        <div class="wrapper-add-info mt-2 flex-column">
            <div class="row mb-2">
                <div class="col-4">
                    <div>Номер акта</div>
                    <div><strong><?= $this->data['act_number'] ?></strong></div>
                </div>

                <div class="col-4">
                    <div>Проба отобрана не заказчиком</div>
                    <div>
                        <label class="switch">
                            <input class="form-check-input selection-type" data-id="<?=$this->data['deal_id']?>" name="act[SELECTION_TYPE]" type="checkbox" value="1" <?=$this->data['selection_type']? 'checked': ''?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <div class="col-4">
                    <div>Дата поступления проб в ИЦ (дата доставки проб)</div>
                    <div><strong><?= $this->data['act_date'] ?></strong></div>
                </div>
            </div>
        </div>
        <!--./wrapper-add-info-->
    </div>
    <!--./panel-body-->
</div>

<div class="panel panel-default">
    <header class="panel-heading">
        Информация о пробах (образцах)
        <span class="tools float-end">
                <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                <a href="#" class="fa fa-chevron-up"></a>
             </span>
    </header>

    <div class="panel-body">
        <table class="table">
            <thead>
            <th class="text-center">Шифр пробы</th>
            <th class="text-center">Маркировка заказчика (при наличии)</th>
            <th class="text-center">Лаборатория</th>
            <th class="text-center">История</th>
            <th class="text-center">Статус нахождения</th>
            <th class="text-center">Управление</th>
            <?php if ( $this->data['user_id'] == 156): ?>
                <th class="text-center">Готовность пробы</th>
            <?php endif; ?>
            <?php if ( $this->data['user_id'] == 61): ?>
                <th class="text-center">Невозвратность пробы</th>
            <?php endif; ?>
            </thead>
            <tbody>
            <?php $mat = ''; foreach ($this->data['material_probe'] as $id_mat => $val):?>
                <tr>
                    <td colspan="7" style="background-color: #e9ecef;">
                        <strong><?=$val['material_name']?></strong>
                    </td>
                </tr>
                <!--                --><?php
//                echo "<pre>";
//                print_r($val['probe']);
//                echo "</pre>";
//                ?>
                <?php foreach ($val['probe'] as $k => $item): ?>
                    <tr>
                        <td>
                            <?php if ( $item['is_in_act'] == 0 ): ?>
                                <strong><?= $item['cipher'] ?></strong>
                                <form action="<?=URI?>/probe/copyProbeInfo/" method="post">
                                    <input type="hidden" name="probe_id" value="<?=$k?>">
                                    <div class="input-group">
                                        <select name="source_probe_id" class="form-select">
                                            <option value="">Выберите шифр проб</option>
                                            <?php foreach ($this->data['probe_in_act'] as $probeInAct): ?>
                                                <?php if ($probeInAct['material_id'] != $id_mat) { continue; } ?>
                                                <option value="<?= $probeInAct['id'] ?>" <?=$probeInAct['cipher'] == $item['cipher']? 'selected':''?>><?= $probeInAct['cipher'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn btn-outline-success btn-square" type="submit" title="Применить"><i class="fa-solid fa-check"></i></button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <a href="#" title="Редактировать" data-id="<?=$k?>" class="edit_probe"><strong><?= $item['cipher'] ?></strong></a>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= $item['name_for_protocol'] ?? '-' ?></td>
                        <td class="text-center">
                            <?= $item['lab_name'] ?>
                        </td>
                        <td class="text-center">
                            <?php if ( $item['is_in_act'] == 1 ): ?>
                                <a href="#" data-id="<?=$k?>" class="history_probe"><i class="fa-regular fa-clock"></i></a>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= $item['state'] ?></td>
                        <td class="text-center">
                            <?php if ( $this->data['is_hand_over']): ?>
                                <?php if ( $item['state'] == 'МФЦ' ): ?>
                                    <a href="<?=URI?>/probe/transferProbe/<?=$k?>">Передать</a>
                                <?php else: ?>
                                    <a href="#" class="disabled">Передано</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ( $item['user_status'] == -1 ): ?>
                                    -
                                <?php elseif ( $item['user_status'] == 1 ): ?>
                                    <a href="#" class="disabled">Принято</a>
                                <?php else: ?>
                                    <a href="<?=URI?>/probe/takeProbe/<?=$k?>">Принять</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <!--                        --><?php
                        //                        echo "<pre>";
                        //                        print_r($item);
                        //                        echo "</pre>";
                        //                        ?>
                        <?php if ( $this->data['user_id'] == 156): ?>
                            <td class="text-center">
                                <div class="d-flex flex-column gap-1 align-items-start">
                                    <?php foreach ($item['lab_info'] as $lab): ?>
                                        <?php if(!$lab['date_send']): ?>
                                            <a class="send-probe-lab" data-lab-name="<?= $lab['short_name'] ?>" data-lab-id="<?= $lab['ID'] ?>" data-probe-id="<?= $k ?>">Отметить готовность <?= $lab['short_name'] ?></a>
                                        <?php else: ?>
                                            <span>Проба для <?= $lab['short_name'] ?> готова</span>
                                        <?php endif ;?>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        <?php endif; ?>
                        <?php if ( $this->data['user_id'] == 61): ?>
                            <td class="text-center">
                                <input class="irrevocable" key="<?=$k?>" type="checkbox" name="irrevocable" value="<?= $this->data['probe'][$k]['irrevocable'] == 1 ? '1' : '0' ?>" <?= $this->data['probe'][$k]['irrevocable'] == 1 ? 'checked' : '' ?>>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!--./panel-body-->
</div>

<form id="edit-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/probe/editProbeInfo/" method="post">
    <div class="title mb-3 h-2">
        Редактирование пробы
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" name="id" class="probe_id" value="">

    <div class="mb-3">
        <label class="form-label">Маркировка заказчика (информация об объекте испытания)</label>
        <input type="text" name="form[name_for_protocol]" class="form-control name_for_protocol" value="">
    </div>

    <div class="mb-3">
        <label class="form-label">Выводить только маркировку заказчика</label>
        <input type="checkbox" name="" class="form-control" value="">
    </div>

    <div class="mb-3">
        <label class="form-label">Место отбора</label>
        <input type="text" name="form[place]" class="form-control probe_place" value="">
    </div>

    <div class="mb-3">
        <label class="form-label">Дата отбора</label>
        <input type="date" name="form[date_probe]" class="form-control probe_date" value="">
    </div>

<!--    <div class="mb-3">-->
<!--        <label class="form-label">Карьер</label>-->
<!--        <select name="form[quarry_id]" class="form-control probe_quarry_id">-->
<!--            --><?php //foreach($this->data['quarry'] as $key => $value): ?>
<!--                <option value='--><?php //=$value['ID']?><!--'>--><?php //=$value['NAME']?><!--</option>-->
<!--            --><?php //endforeach;?>
<!--        </select>-->
<!--    </div>-->

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>


<div id="history-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        История пробы <span class="cipher"></span>
    </div>

    <div class="line-dashed-small"></div>

    <div class="history-info">

    </div>
</div>