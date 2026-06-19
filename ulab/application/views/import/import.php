<style>
 .import-wrapper .list-group {
    border-radius: 10px;
}
 .form-group {
    margin-bottom: 0;
 }
 .list-group .col {
    display: flex;
    justify-content: end;
 }
 .form-group .btn {
     max-width: 300px;
     text-transform: none;
 }
 .btn-gradient.warning {
     border: 2px solid #ff9407;
     color: #ff9c07;
 }
  .btn-gradient.warning:hover {
     border: 2px solid #96612d;
     color: #96612d;
 }
  .panel-body .list-group-item:first-child {
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
  }
.panel-body .list-group-item:last-child {
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
}
</style>

<div class="import-wrapper">
    <ul class="list-group list-group-flush list-group-separate">

         <div class="panel panel-default">
            <header class="panel-heading">
                ОБЩИЕ НАСТРОЙКИ
                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
            </header>
            <div class="panel-body">

                <li class="list-group-item ">
                    <div class="form-group row mb-0 align-items-center">
                        <label class="col-sm-4 col-form-label">Общие сведения о лаборатории</label>
                        <div class="col">
                            <a href="<?= URI ?>/import/companyInfo/" class="btn btn-gradient mt-0 w-100 rounded"
                                title="Внесение и управление реквизитами лаборатории" data-bs-placement="left" data-bs-toggle="tooltip">
                                Редактировать
                            </a>
                        </div>
                    </div>
                </li>


                 <li class="list-group-item  ">
                    <div class="form-group row mb-0 align-items-center">
                        <label class="col-sm-4 col-form-label">Отделы и помещения</label>
                        <div class="col">
                            <?php if (!empty($this->data['company_info'])): ?>
                                <a href="<?= URI ?>/import/lab/" class="btn btn-gradient mt-0 w-100 rounded"
                                    title="Внесение отделов, Назначение начальников отделов, Внесение помещений" data-bs-placement="left" data-bs-toggle="tooltip">
                                    Редактировать
                                </a>
                            <?php else: ?>
                                <div class="btn btn-gradient disabled mt-0 w-100 rounded"
                                     title='Для управление кадрами заполните данные в разделе "Общие сведения и лаборатории"' data-bs-placement="left" data-bs-toggle="tooltip">
                                    Редактировать
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>

                <li class="list-group-item ">
                    <div class="form-group row mb-0 align-items-center">
                        <label class="col-sm-4 col-form-label">Управление кадрами</label>
                        <div class="col">
                            <a href="<?= URI ?>/user/list/" class="btn btn-gradient mt-0 w-100 rounded"
                               title="Внесение сотрудников, Внесение сведений о руководстве и бухгалтере, Назначение ролей сотрудникам, Замены, Настройка и создание ролей и доступов" data-bs-placement="left" data-bs-toggle="tooltip">
                                Редактировать
                            </a>
                        </div>
                    </div>
                </li>

            </div>
         </div>

         <div class="panel panel-default">
            <header class="panel-heading">
                СПРАВОЧНИКИ
                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
            </header>
            <div class="panel-body">

                <li class="list-group-item ">
                    <div class="form-group row mb-0 align-items-center">
                        <label class="col-sm-4 col-form-label">Единицы измерений</label>
                        <div class="col">
                            <a href="<?= URI ?>/reference/unitList/" class="btn btn-gradient mt-0 w-100 rounded"
                               title="Журнал единиц измерений" data-bs-placement="left" data-bs-toggle="tooltip">
                                Редактировать
                            </a>
                        </div>
                    </div>
                </li>
                <li class="list-group-item  ">
                    <div class="form-group row mb-0 align-items-center">
                        <label class="col-sm-4 col-form-label">Определяемые характеристики</label>
                        <div class="col">
                            <a href="<?= URI ?>/reference/measuredPropertiesList/" class="btn btn-gradient mt-0 w-100 rounded"
                              title="Журнал определяемых характеристик" data-bs-placement="left" data-bs-toggle="tooltip">
                                Редактировать
                            </a>
                        </div>
                    </div>
                </li>

                <li class="list-group-item  ">
                    <div class="form-group row mb-0 align-items-center">
                        <label class="col-sm-4 col-form-label">Оборудование</label>
                        <?php if (isset($this->data['oborud_success'])): ?>
                            <div class="col">
                                <div class="alert alert-success mb-0 p-2 text-center" role="alert">
                                    <?= $this->data['oborud_success'] ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="<?= isset($this->data['oborud_success']) ? 'col-auto' : 'col' ?>">
                            <a href="<?= URI ?>/oborud/list/" class="btn btn-gradient mt-0 w-100 rounded"
                             title="Журнал оборудования, Импорт и экспорт" data-bs-placement="left" data-bs-toggle="tooltip">
                                Редактировать
                            </a>
                        </div>
                    </div>
                </li>

                <li class="list-group-item  ">
                    <div class="form-group row mb-0 align-items-center">
                        <label class="col-sm-4 col-form-label">Область аккредитации</label>
                        <?php if (isset($this->data['methods_success'])): ?>
                            <div class="col">
                                <div class="alert alert-success mb-0 p-2 text-center" role="alert">
                                    <?= $this->data['methods_success'] ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="<?= isset($this->data['methods_success']) ? 'col-auto' : 'col' ?>">
                            <a href="<?= URI ?>/gost/list/" class="btn btn-gradient mt-0 w-100 rounded"
                             title="Журнал областей аккредитации, Импорт и экспорт" data-bs-placement="left" data-bs-toggle="tooltip">
                                Редактировать
                            </a>
                        </div>
                    </div>
                </li>

                <li class="list-group-item  ">
                    <div class="form-group row mb-0 align-items-center">
                        <label class="col-sm-4 col-form-label">Нормативная документация</label>
                        <div class="col">
                            <a href="<?= URI ?>/techCondition/list/" class="btn btn-gradient mt-0 w-100 rounded"
                               title="Журнал НД, Импорт и экспорт" data-bs-placement="left" data-bs-toggle="tooltip">
                                Редактировать
                            </a>
                        </div>
                    </div>
                </li>

                <li class="list-group-item  ">
                    <div class="form-group row mb-0 align-items-center">
                        <label class="col-sm-4 col-form-label">Объекты испытаний</label>
                        <?php if (isset($this->data['material_success'])): ?>
                            <div class="col">
                                <div class="alert alert-success mb-0 p-2 text-center" role="alert">
                                    <?= $this->data['material_success'] ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="<?= isset($this->data['material_success']) ? 'col-auto' : 'col' ?>">
                            <a href="<?= URI ?>/material/list/" class="btn btn-gradient mt-0 w-100 rounded"
                              title="Журнал объектов испытаний" data-bs-placement="left" data-bs-toggle="tooltip">
                                Редактировать
                            </a>
                        </div>
                    </div>
                </li>

                <li class="list-group-item  ">
                    <div class="form-group row mb-0 align-items-center">
                        <label class="col-sm-4 col-form-label">Прайс</label>
                        <div class="col">
                           <a href="<?= URI ?>/gost/listPrice/" class="btn btn-gradient mt-0 w-100 rounded">
                                Редактировать
                            </a>
                        </div>
                    </div>
                </li>

<!--                <li class="list-group-item ">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">Конфигурационные справочники</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->

            </div>
         </div>

<!--         <div class="panel panel-default">-->
<!--            <header class="panel-heading">-->
<!--                ФОРМЫ И ДОКУМЕНТЫ-->
<!--                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>-->
<!--            </header>-->
<!--            <div class="panel-body">-->
<!--                <li class="list-group-item ">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">Конструктор форм</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li class="list-group-item  ">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">Назначения форм</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li class="list-group-item ">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">Нумерация</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!--            </div>-->
<!--         </div>-->


<!--         <div class="panel panel-default">-->
<!--            <header class="panel-heading">-->
<!--                ИНТЕГРАЦИИ-->
<!--                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>-->
<!--            </header>-->
<!--            <div class="panel-body">-->
<!--                --><?php //if ($this->data['fsa_installed']): ?>
<!--                <li class="list-group-item">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">ФГИС Росаккредитация</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!--                --><?php //endif; ?>
<!--                <li class="list-group-item">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">1С: Бухгалтерия</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!--            </div>-->
<!--         </div>-->

<!--         <div class="panel panel-default">-->
<!--            <header class="panel-heading">-->
<!--                СИСТЕМНЫЕ НАСТРОЙКИ-->
<!--                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>-->
<!--            </header>-->
<!--            <div class="panel-body">-->
<!--                <li class="list-group-item">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">История</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li class="list-group-item">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">Провайдер электронной почты</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li class="list-group-item">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">Электронные подписи</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li class="list-group-item">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">Резервное копирование</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li class="list-group-item">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">Уведомления</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!--            </div>-->
<!--         </div>-->

<!--         <div class="panel panel-default">-->
<!--            <header class="panel-heading">-->
<!--                ПОМОЩЬ-->
<!--                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>-->
<!--            </header>-->
<!--            <div class="panel-body">-->
<!---->
<!--            <li class="list-group-item">-->
<!--                    <div class="form-group row mb-0 align-items-center ">-->
<!--                        <label class="col-sm-4 col-form-label">Инструкции к ULAB</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!---->
<!--                <li class="list-group-item  ">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">Частые вопросы и ответы</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!---->
<!--                <li class="list-group-item ">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">Лабораторная база знаний</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li class="list-group-item ">-->
<!--                    <div class="form-group row mb-0 align-items-center">-->
<!--                        <label class="col-sm-4 col-form-label">Помощь</label>-->
<!--                        <div class="col">-->
<!--                            <div class="btn btn-gradient disabled mt-0 w-100 rounded" title='Данный раздел находится в разработке.' data-bs-placement="left" data-bs-toggle="tooltip">-->
<!--                                Недоступно-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
<!---->
<!--            </div>-->
<!--         </div>-->
    </ul>
</div>