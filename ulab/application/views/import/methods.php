<style>
    .header-menu .nav-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="methods-wrapper import">
    <header class="header-requirement mb-4 pt-0">
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

                <li class="nav-item me-2">
                    <a class="nav-link fa-solid icon-nav fa-list" href="<?= URI ?>/gost/list/" style="font-size: 22px;" title="Список областей аккредитации" data-bs-toggle="tooltip">
                    </a>
                </li>

                <li class="nav-item me-2">
                    <a class="nav-link fa-solid icon-nav fa-file-import disabled" href="<?= URI ?>/import/methods/" style="font-size: 22px;" title="Импорт областей аккредитации и методик" data-bs-toggle="tooltip">
                    </a>
                </li>

                <li class="nav-item me-2">
                    <a class="nav-link fa-solid icon-nav fa-link" href="<?= URI ?>/import/oborudToMethod/" style="font-size: 22px;" title="Привязка оборудования к методикам" data-bs-toggle="tooltip">
                    </a>
                </li>

                <li class="nav-item me-5">
                    <a class="nav-link fa-solid icon-nav fa-link" href="<?= URI ?>/import/labUserToMethod/" style="font-size: 22px;" title="Привязка отделов и сотрудников к методикам" data-bs-toggle="tooltip">
                    </a>
                </li>

                <?php if (!empty($this->data['methods'])): ?>
                    <li class="nav-item ms-auto">
                        <div class="col">
                            <button form="import_method" type="submit" name="import" class="btn btn-gradient" id="submit_btn">Импортировать</button>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <header class="panel-heading">
                    Внесения сведений об областях аккредитации и методиках
                    <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="#" class="fa fa-chevron-up"></a>
                         </span>
                </header>
                <div class="panel-body">
                    <div class="methods-import-wrapper mb-4">
                        <div class="signature">Параметры импорта</div>
                        <div class="border p-2 border-bottom-0 bg-white">
                            <em>Скачайте шаблон CSV файла, заполните данные и загрузите
                                файл для импорта данных. Разделитель колонок: точка с запятой.</em>
                        </div>
                        <div class="border p-3 mb-4">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div>
                                       <label class="me-2">Шаблон импортируемого файла</label>
                                        <a class="text-decoration-none"
                                           href="<?= $this->data['template'] ?>"
                                           title="Скачать шаблон для оборудования" download>
                                            Скачать
                                        </a>
                                    </div>
                                    <div>
                                       <label class="me-2">Файл с данными из системы</label>
                                        <a class="text-decoration-none"
                                           href="<?=URI?>/import/exportMethods"
                                           title="Скачать файл с данными из системы" download>
                                            Скачать
                                        </a>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <label class="me-2">Файл данных (формат CSV) <span class="redStars">*</span></label>
                                    <div class="d-inline-block">
                                        <?php if ($this->data['is_file_exists']): ?>
                                            <form class="form form-upload-csv" method="post"
                                                  action="<?= URI ?>/import/deleteCsv/methods"
                                                  enctype="multipart/form-data">
                                                <div class="position-relative d-inline-block">
                                                    <a class="a_svg" href="<?= $this->data['file'] ?>"
                                                       target="_blank"
                                                       title="<?= $this->data['file'] ?>">
                                                        <svg class="icon" width="30" height="30">
                                                            <use xlink:href="<?= URI ?>/assets/images/icons.svg#csv_file"/>
                                                        </svg>
                                                    </a>
                                                    <button type="submit"
                                                            class="button-del-file button-close button-outline del-csv"
                                                            name="delete_csv"
                                                            title="Удалить csv файл"><a class="delete_svg">x</a></button>
                                                </div>
                                            </form>
                                        <?php else: ?>
                                            <form class="form form-upload-csv" method="post"
                                                  action="<?= URI ?>/import/uploadCsv/methods"
                                                  enctype="multipart/form-data">
                                                <label class="upload-csv cursor-pointer"
                                                       title="Загрузить CSV-версию">
                                                    <svg class="icon" width="30" height="30">
                                                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#upload"/>
                                                    </svg>
                                                    <input class="d-none" id="uploadCsv" type="file"
                                                           name="upload_csv" onchange="form.submit()">
                                                </label>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--./methods-import-wrapper-->

                    <?php if (!empty($this->data['methods'])): ?>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered caption-top text-center align-middle">
                                <caption>Пример импортируемых данных</caption>
                                <thead class="align-middle">
                                <tr>
                                    <th scope="col">№ п/ п</th>
                                    <!--ГОСТ-->
                                    <th scope="col">Номер документа</th>
                                    <th scope="col">Год</th>
                                    <th scope="col">Наименование документа</th>
                                    <th scope="col">Наименование объекта</th>
                                    <th scope="col">Код ТН ВЭД ЕАЭС</th>
                                    <th scope="col">Код ОКПД 2</th>
                                    <!--Методики-->
                                    <!--Основные характеристики-->
                                    <th scope="col">Пункт документа</th>
                                    <th scope="col">Определяемая характеристика/показатель</th>
                                    <th scope="col">Номер в OA</th>
                                    <!--Единицы измерения и нормы-->
                                    <th scope="col">Диапазон определения ОТ</th>
                                    <th scope="col">Диапазон определения ДО</th>
                                    <th scope="col">Диапазон</th>
                                    <th scope="col">Нормы текстом?</th>
                                    <th scope="col">Факт. значения текстом?</th>
                                    <!--Условия применения-->
                                    <th scope="col">Температура (°С) ОТ</th>
                                    <th scope="col">Температура (°С) ДО</th>
                                    <th scope="col">Температура не нормируется</th>
                                    <th scope="col">Влажность (%) ОТ</th>
                                    <th scope="col">Влажность (%) ДО</th>
                                    <th scope="col">Влажность не нормируется</th>
                                    <th scope="col">Атм. давление (КПа) ОТ</th>
                                    <th scope="col">Атм. давление (КПа) ДО</th>
                                    <th scope="col">Атм. давление не нормируется</th>
                                    <!--Дополнительные характеристики-->
                                    <th scope="col">Стоимость (руб.)</th>
                                    <!--Контроль-->
                                    <th scope="col">В области аккредитации?</th>
                                    <th scope="col">Расширенная область?</th>
                                    <th scope="col">Оборудование</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->data['methods'] as $val): ?>
                                    <tr>
                                        <td><?= $val['num_oa'] ?></td>
                                        <td><?= $val['gost']['reg_doc'] ?></td>
                                        <td><?= $val['gost']['year'] ?></td>
                                        <td><?= $val['gost']['description'] ?></td>
                                        <td><?= $val['gost']['materials'] ?></td>
                                        <td><?= $val['gost']['code_eaes'] ?></td>
                                        <td><?= $val['gost']['code_okpd2'] ?></td>
                                        <td><?= $val['clause'] ?></td>
                                        <td><?= $val['name'] ?></td>
                                        <td><?= $val['num_oa'] ?></td>
                                        <td><?= $val['definition_range_1'] ?></td>
                                        <td><?= $val['definition_range_2'] ?></td>
                                        <td>
                                            <?php if ($val['definition_range_type'] === 1): ?>
                                                внутренний диапазон
                                            <?php elseif ($val['definition_range_type'] === 2): ?>
                                                внешний диапазон
                                            <?php else: ?>
                                                не нормируется
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input class="form-check-input"
                                                   type="checkbox" disabled <?= $val['is_text_norm'] ? 'checked' : '' ?>>
                                        </td>
                                        <td>
                                            <input class="form-check-input"
                                                   type="checkbox" disabled <?= $val['is_text_fact'] ? 'checked' : '' ?>>
                                        </td>
                                        <td><?= $val['cond_temp_1'] ?></td>
                                        <td><?= $val['cond_temp_2'] ?></td>
                                        <td>
                                            <input class="form-check-input"
                                                   type="checkbox" disabled <?= $val['is_not_cond_temp'] ? 'checked' : '' ?>>
                                        </td>
                                        <td><?= $val['cond_wet_1'] ?></td>
                                        <td><?= $val['cond_wet_2'] ?></td>
                                        <td>
                                            <input class="form-check-input"
                                                   type="checkbox" disabled <?= $val['is_not_cond_wet'] ? 'checked' : '' ?>>
                                        </td>
                                        <td><?= $val['cond_pressure_1'] ?></td>
                                        <td><?= $val['cond_pressure_2'] ?></td>
                                        <td>
                                            <input class="form-check-input"
                                                   type="checkbox" disabled <?= $val['is_not_cond_pressure'] ? 'checked' : '' ?>>
                                        </td>
                                        <td><?= $val['price'] ?></td>
                                        <td>
                                            <input class="form-check-input"
                                                   type="checkbox" disabled <?= $val['in_field'] ? 'checked' : '' ?>>
                                        </td>
                                        <td>
                                            <input class="form-check-input"
                                                   type="checkbox" disabled <?= $val['is_extended_field'] ? 'checked' : '' ?>>
                                        </td>
                                        <td><?= $val['oborud'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <form id="import_method" class="form form-import-csv" method="post"
                              action="<?= URI ?>/import/importCsv/methods">
                            <div class="row">
                                <div class="col">
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
                <!--./panel-body-->
            </div>
        </div>
    </div>
</div>