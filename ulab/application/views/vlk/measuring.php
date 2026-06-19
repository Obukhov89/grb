<!-- Результаты измерений ВЛК -->
<div class="wrapper-card m-auto">
    <header class="mb-3">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-2">
                    <a class="nav-link" href="<?=URI?>/vlk/methodComponentList/" title="Журнал методик и образцов контроля с метрологическими характеристиками">
                        <i class="fa-solid fa-list"></i>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="<?=URI?>/oborud/sampleList/" title="Список образецов контроля">
                        <svg class="icon" width="25" height="25">
                            <use xlink:href="<?=URI?>/assets/images/icons.svg#card"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link popup-help" href="<?=URI?>/help/LIMS_Manual_Stand/VLK/Measuring_card/Measuring.html" title="ПОМОГИТЕ">
                        <i class="fa-solid fa-question"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <div class="panel panel-default">
        <header class="panel-heading">
            Общая информация
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
             </span>
        </header>
        <div class="panel-body">
            <div class="extras-info-wrapper">
                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label">Обозначение ОК</label>
                        <div>
                            <strong>
                                <a href="<?=URI?>/oborud/sampleCard/<?=$this->data['sample']['ID']?>" class="text-decoration-none">
                                    <?=$this->data['sample']['NUMBER']?>
                                </a>
                            </strong>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Элемент</label>
                        <div><strong><?=$this->data['component']['name']?></strong></div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Аттестованное значение</label>
                        <div><strong><?=$this->data['component']['certified_value']?></strong></div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Погрешность</label>
                        <div><strong><?=$this->data['component']['error_characteristic']?></strong></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label class="form-label">Методика</label>
                        <div>
                            <strong>
                                <a href="<?=URI?>/gost/method/<?=$this->data['method']['id']?>" class="text-decoration-none">
                                    <?=$this->data['method']['view_gost_for_protocol']?>
                                </a>
                            </strong>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Rл</label>
                        <div><strong><?=$this->data['accuracy_control']['Rl']?></strong></div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">r</label>
                        <div><strong><?=$this->data['accuracy_control']['r']?></strong></div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Кт</label>
                        <div><strong><?=$this->data['accuracy_control']['Kt']?></strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default" id="controlBlock">
        <header class="panel-heading">
            Контроль
            <span class="tools float-end">
                <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                <a href="javascript:;" class="fa fa-chevron-down"></a>
            </span>
        </header>
        <div class="panel-body panel-hidden">
            <?php if(empty($this->data['measuring'])): ?>
                <div>Для построения КК Шухарта сохраните данные измерений</div>
            <?php else: ?>
                <div id="controlWrapper">
                    <div class="row mb-3">
                        <div class="col">
                            <em>
                                Выберите необходимый контроль(алгоритм проведения контрольных процедур) и диапазон измерений для построения КК Шухарта
                                (Внимание! Убедитесь что выбрано необходимое минимальное кол-во результатов контрольных процедур L, необходимых для достоверной оценки)
                            </em>
                        </div>
                    </div>

                    <form id="controlForm" method="post" action="<?= URI ?>/vlk/methodComponentList/">
                        <div class="border px-3 py-3">
                            <input type="hidden" class="form-control bg-light-secondary" name="umc_id" value="<?= $this->data['umc_id'] ?>" readonly>

                            <div class="control-wrapper">
                                <label>Выберите алгоритм проведения контрольных процедур:</label>
                                <div class="border p-3">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input control" type="radio" id="repetitionControl"
                                                       name="control" value="repetition">
                                                <label class="form-check-label" for="repetitionControl">Контроль повторяемости</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input control" type="radio" id="precisionControl"
                                                       name="control" value="precision">
                                                <label class="form-check-label" for="precisionControl">Контроль прецизионности</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input control" type="radio" id="deviationControl"
                                                       name="control" value="deviation">
                                                <label class="form-check-label" for="deviationControl">Контроль погрешности с применением ОК</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row create-wrapper mt-3 d-none">
                                <div class="date-wrapper">
                                    <label>Выберите временной диапазон:</label>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <input type="date" id="dateStart" class="form-control bg-transparent w-100 mw-100 control-date" name="date_start"
                                                   value="<?=date('Y-m-d')?>" placeholder="Введите дату начала:">
                                        </div>
                                        <div class="col">
                                            <input type="date" id="dateEnd" class="form-control bg-transparent w-100 mw-100 control-date" name="date_end"
                                                   value="<?=date('Y-m-d')?>" placeholder="Введите дату окончания:">
                                        </div>
                                    </div>
                                </div>

                                <div class="col">
                                    <button class="btn btn-primary create-shukhert-chart" type="submit">
                                        Создать КК Шухарта <i class="fa-solid fa-arrows-rotate"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="chart-wrapper">
                        <!-- Статус предсказуемости -->
                        <div id="predictionStatus"></div>

                        <!-- График контрольных карт Шухарта -->
                        <canvas id="shukhertChart" class="mt-4 d-none"></canvas>

                        <!-- Таблица результатов расчётов -->
                        <div id="resultTable"></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel panel-default" id="measuringBlock">
        <header class="panel-heading">
            Таблица результатов измерений
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
             </span>
        </header>
        <div class="panel-body">
            <div class="row">
                <div class="col">
                    <?php if(empty($this->data['measuring_count'])): ?>
                        <div class="row mb-3">
                            <div class="col">
                                <em>Создайте таблицу для внесения результатов измерений и сохраните</em>
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center alert-dismissible fade show" role="alert">
                            <div>
                                <em>Внимание! После сохранения "Количество результатов параллельных определений", данные нельзя будет изменить</em>
                            </div>
                        </div>

                        <form class="form-horizontal" id="measuringForm" method="post" action="<?=URI?>/vlk/saveMeasuringCounts/<?=$this->data['umc_id']?>">
                            <div class="row">
                                <label for="measuringCount">Количество результатов параллельных определений</label>
                                <div class="col form-group">
                                    <input type="text" name="measuring_count" class="form-control number-only" id="measuringCount"
                                           value="1" required>
                                </div>
                                <div class="col-auto form-group">
                                    <button class="btn btn-primary new-table" type="button">Создать таблицу</button>
                                </div>
                            </div>

                            <div class="table-responsive d-none" id="measuringWrapper">
                                <table class="table table-bordered text-center" id="measuringTable">
                                    <thead>
                                    <tr class="table-secondary align-middle">
                                        <th scope="col">№</th>
                                        <th scope="col">Дата</th>
                                        <th scope="col" class="head-measuring-0">Результат 1-го измерения</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th scope="row">1</th>
                                        <td>
                                            Дата
                                        </td>
                                        <td class="col-measuring-0">
                                            Результат измерения
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <button class="btn btn-primary mb-2 save-new-table" type="submit">
                                    Сохранить <i class="fa-solid fa-arrows-rotate"></i>
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <input id="measuringCount" type="hidden" value="<?=$this->data['measuring_count']?>">

                        <div class="row justify-content-start mb-3">
                            <div class="col-auto">
                                <button type="button" class="btn btn-success btn-square add-measuring" title="Добавить новое измерение">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </div>
                        </div>

                        <table id="journalMeasuring" class="table table-striped text-center align-middle journal w-100">
                            <thead>
                            <tr class="table-light align-middle">
                                <th scope="col">История</th>
                                <th scope="col">№</th>
                                <th scope="col" class="measuring-date">Дата</th>
                                <?php for($i = 0; $i < $this->data['measuring_count']; $i++): ?>
                                    <th scope="col">Результат <?=$i+1?>-го измерения</th>
                                <?php endfor; ?>
                            </tr>
                            <tr class="header-search">
                                <th scope="col">
                                    <input type="text" class="form-control bg-light-secondary" readonly>
                                </th>
                                <th scope="col">
                                    <input type="text" class="form-control bg-light-secondary" readonly>
                                </th>
                                <th scope="col">
                                    <input type="text" class="form-control search">
                                </th>
                                <?php for($i = 0; $i < $this->data['measuring_count']; $i++): ?>
                                    <th scope="col">
                                        <input type="text" class="form-control bg-light-secondary" readonly>
                                    </th>
                                <?php endfor; ?>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->

    <form class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" id="measuringModalForm"
          action="/ulab/vlk/insertUpdateMeasuring/" method="post">
        <input id="umcId" type="hidden" name="measuring[umc_id]" value="<?= $this->data['umc_id'] ?>">
        <input id="uvmId" type="hidden" name="uvm_id" value="">

        <div class="title mb-3 h-2">
            Данные
        </div>

        <div class="line-dashed-small"></div>

        <div class="mb-3">
            <label class="form-label" for="date">Дата</label>
            <input type="date" class="form-control w-100 bg-white" id="date" name="measuring[date]" step="any"
                   value="<?= date('Y-m-d') ?>"
                   required>
        </div>

        <?php for($i = 0; $i < $this->data['measuring_count']; $i++): ?>
            <div class="mb-3">
                <label class="form-label">Результат <?=$i+1?>-го измерения</label>
                <input type="number" name="measuring[result][]" class="form-control bg-white result"
                       value="" step="any" required>
            </div>
        <?php endfor; ?>

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary save-measuring">
            Отправить <i class="fa-solid fa-arrows-rotate"></i>
        </button>
    </form>
    <!--./measuringModalForm-->

    <div id="history-modal-form" class="bg-light mfp-hide col-md-5 m-auto p-3 position-relative">
        <div class="title mb-3 h-2"></div>

        <div class="line-dashed-small"></div>

        <div class="history-info"></div>
    </div>
</div>