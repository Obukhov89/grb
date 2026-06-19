<style>
    .header-menu .nav-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="oborud-wrapper import">
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
                    <a class="nav-link icon-nav fa-solid fa-list" href="<?=URI?>/oborud/list/" style="font-size: 22px;" title="Список оборудования" data-bs-toggle="tooltip">
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link icon-nav fa-solid fa-file-import disabled" href="<?=URI?>/import/oborud/" style="font-size: 22px;" title="Импорт оборудования" data-bs-toggle="tooltip">
                    </a>
                </li>
                <li class="nav-item me-5">
                    <a class="nav-link icon-nav fa-solid fa-link" href="<?=URI?>/import/oborudToRoom/" style="font-size: 22px;" title="Привязка оборудования к помещениям" data-bs-toggle="tooltip">
                    </a>
                </li>

                <?php if (!empty($this->data['oborud'])): ?>
                    <li class="nav-item ms-auto">
                        <div class="col">
                            <button form="import_oborud" type="submit" name="import" class="btn btn-gradient" id="submit_btn">Импортировать</button>
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
                    Внесения сведений об оборудовании
                    <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="#" class="fa fa-chevron-up"></a>
                         </span>
                </header>
                <div class="panel-body">
                    <div class="oborud-import-wrapper mb-4">
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
                                           href="<?=URI?>/import/exportOborud"
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
                                                  action="<?= URI ?>/import/deleteCsv/oborud"
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
                                                  action="<?= URI ?>/import/uploadCsv/oborud"
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
                    <!--./oborud-import-wrapper-->

                    <?php if (!empty($this->data['oborud'])): ?>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered caption-top text-center align-middle">
                                <caption>Пример импортируемых данных</caption>
                                <thead class="align-middle">
                                <tr>
                                    <th scope="col">№ п/ п</th>
                                    <th scope="col">Наименование оборудования</th>
                                    <th scope="col">Тип оборудования</th>
                                    <th scope="col">Идентификация оборудования</th>
                                    <th scope="col">Страна, производитель</th>
                                    <th scope="col">Инвентарный номер</th>
                                    <th scope="col">Заводской номер</th>
                                    <th scope="col">Год выпуска</th>
                                    <th scope="col">Дата ввода в эксплуатацию</th>
                                    <th scope="col">Регистрационный номер в ИЦ</th>
                                    <th scope="col">Температура окружающего воздуха</th>
                                    <th scope="col">Относительная влажность воздуха, %</th>
                                    <th scope="col">Атм. давление (кПа)</th>
                                    <th scope="col">Напряжение (В)</th>
                                    <th scope="col">Частота (Гц)</th>
                                    <th scope="col">Технические данные</th>
                                    <th scope="col">Дата последней поверки</th>
                                    <th scope="col">Дата окончания поверки</th>
                                    <th scope="col">Свидетельство о поверке</th>
                                    <th scope="col">Межповерочный интервал</th>
                                    <th scope="col">Межкалибровочный интервал</th>
                                    <th scope="col">Помещение</th>
                                    <th scope="col">Ответственные лица</th>
                                    <th scope="col">Связи с объектами испытаний</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->data['oborud'] as $val): ?>
                                    <tr>
                                        <td><?= $val['equipment_general']['number'] ?></td>
                                        <td><?= $val['equipment_general']['name'] ?></td>
                                        <td><?php
                                            $equipmentTypeId = $val['equipment_general']['id_equipment_type'];
                                            switch ($equipmentTypeId) {
                                                case 1:
                                                    $type = 'Средство измерения';
                                                    break;
                                                case 2:
                                                    $type = 'Испытательное оборудование';
                                                    break;
                                                case 3:
                                                    $type = 'Вспомогательное оборудование';
                                                    break;
                                                case 4:
                                                    $type = 'Государственный стандартный образец';
                                                    break;
                                                case 5:
                                                    $type = 'Музейный штаммы';
                                                    break;
                                                default:
                                                    $type = 'Средство измерения';
                                                    break;
                                            }
                                            echo $type;
                                        ?></td>
                                        <td><?= $val['equipment_general']['iac_rosaccreditation_record_id'] ?></td>
                                        <td><?= $val['equipment_organisations_vendor']['name'] ? $val['equipment_organisations_vendor']['country'] . ' | ' . $val['equipment_organisations_vendor']['name'] : $val['equipment_organisations_vendor']['country'] ?></td>
                                        <td><?= $val['equipment_general']['inventory_number'] ?></td>
                                        <td><?= $val['equipment_general']['factory_number'] ?></td>
                                        <td><?= $val['equipment_general']['manufacture_date'] ?></td>
                                        <td><?= $val['equipment_general']['commissioning_date'] ?></td>
                                        <td><?= $val['equipment_state_register']['state_register_number'] ?></td>
                                        <td>от <?= $val['equipment_conditions_atmospheric_operating']['temperature_from'] ?? 0 ?> до <?= $val['equipment_conditions_atmospheric_operating']['temperature_to'] ?? 0 ?></td>
                                        <td>от <?= $val['equipment_conditions_atmospheric_operating']['humidity_from'] ?? 0 ?> до <?= $val['equipment_conditions_atmospheric_operating']['humidity_to'] ?? 0 ?></td>
                                        <td>от <?= $val['equipment_conditions_atmospheric_operating']['pressure_from'] ?? 0 ?> до <?= $val['equipment_conditions_atmospheric_operating']['pressure_to'] ?? 0 ?></td>
                                        <td>от <?= $val['equipment_conditions_electric']['voltage_from'] ?? 0 ?> до <?= $val['equipment_conditions_electric']['voltage_to'] ?? 0 ?></td>
                                        <td>от <?= $val['equipment_conditions_electric']['frequency_from'] ?? 0 ?> до <?= $val['equipment_conditions_electric']['frequency_to'] ?? 0 ?></td>
                                        <td><?= $val['equipment_general']['technical_data'] ?></td>
                                        <td><?= $val['equipment_si']['verification_date'] ?></td>
                                        <td><?= $val['equipment_si']['verification_expire_date'] ?></td>
                                        <td><?= $val['equipment_si']['verification_certificate_number'] ?></td>
                                        <td><?= $val['equipment_si']['verification_interval'] ?></td>
                                        <td><?= $val['equipment_si']['calibration_interval'] ?></td>
                                        <td><?= $val['temp']['storage_room'] ?? $val['equipment_general']['id_storage_room'] ?></td>
                                        <td><?= implode(', ', $val['equipment_to_users']['id_user'] ?? []) ?></td>
                                        <td><?= count($val['temp']['methods'] ?? []) ?? 0 ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <form id="import_oborud" class="form form-import-csv" method="post"
                              action="<?= URI ?>/import/importCsv/oborud">
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