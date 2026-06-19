<style>
    .header-menu .nav-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="material-wrapper import">
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
                    <a class="nav-link fa-solid icon-nav fa-list" href="<?= URI ?>/material/list/" style="font-size: 22px;" title="Журнал объектов испытаний" data-bs-toggle="tooltip">
                    </a>
                </li>

                <li class="nav-item me-2">
                    <a class="nav-link fa-solid icon-nav fa-file-import disabled" href="<?= URI ?>/import/materials/" style="font-size: 22px;" title="Импорт объектов испытаний" data-bs-toggle="tooltip">
                    </a>
                </li>

                <?php if (!empty($this->data['material'])): ?>
                    <li class="nav-item ms-auto">
                        <div class="col">
                            <button form="import_mat" type="submit" name="import" class="btn btn-gradient" id="submit_btn">Импортировать</button>
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
                    Внесения сведений о объектах испытаний
                    <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="#" class="fa fa-chevron-up"></a>
                         </span>
                </header>
                <div class="panel-body">
                    <div class="material-import-wrapper mb-4">
                        <div class="signature">Параметры импорта</div>
                        <div class="border p-2 border-bottom-0 bg-white">
                            <em>Скачайте шаблон CSV файла, заполните данные и загрузите
                                файл для импорта данных. Разделитель колонок: точка с запятой.</em>
                        </div>
                        <div class="border p-3 mb-4">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="me-2">Шаблом импортируемого файла</label>
                                    <a class="text-decoration-none"
                                       href="<?= $this->data['template'] ?>"
                                       title="Скачать шаблон для оборудования" download>
                                        Скачать
                                    </a>
                                </div>
                                <div class="col-sm-6">
                                    <label class="me-2">Файл данных (формат CSV) <span class="redStars">*</span></label>
                                    <div class="d-inline-block">
                                        <?php if ($this->data['is_file_exists']): ?>
                                            <form class="form form-upload-csv" method="post"
                                                  action="<?= URI ?>/import/deleteCsv/material"
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
                                                            class="button-del-file button-close button-outline del-csv fa-sharp"
                                                            name="delete_csv"
                                                            title="Удалить csv файл"><a class="delete_svg">x</a></button>
                                                </div>
                                            </form>
                                        <?php else: ?>
                                            <form class="form form-upload-csv" method="post"
                                                  action="<?= URI ?>/import/uploadCsv/material"
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
                    <!--./material-import-wrapper-->

                    <?php if (!empty($this->data['material'])): ?>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered caption-top text-center align-middle">
                                <caption>Пример импортируемых данных</caption>
                                <thead class="align-middle">
                                <tr>
                                    <th scope="col">Наименование объекта испытаний</th>
                                    <th scope="col">Название групп объекта испытаний</th>
                                    <?php for ($i = 0; $i <= 55; $i++): ?>
                                        <th scope="col">Группа объекта испытаний</th>
                                    <?php endfor; ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->data['material'] as $val): ?>
                                    <tr>
                                        <td><?= $val['NAME'] ?></td>
                                        <td><?= $val['GROUP_NAME'] ?></td>
                                        <?php foreach ($val['GROUP_VAL'] as $groupVal): ?>
                                            <td><?= $groupVal ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <form id="import_mat" class="form form-import-csv" method="post"
                              action="<?= URI ?>/import/importCsv/material">
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