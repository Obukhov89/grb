<?php if (!$this->data["month_exists"]): ?>
    <form action="/ulab/project/addDate" method="post">
        <h2 class="mb-2">Введите сумму за январь</h2>

        <input hidden type="number" name="project_id" value="<?= $this->data["project"]["id"] ?>">
        <input hidden type="month" name="date" value="<?= date("Y-m") ?>">
        <div class="mb-2">
            <input type="number" name="plan_expenses" placeholder="Сумма">
        </div>

        <div class="d-flex">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
<?php endif; ?>


<input hidden type="number" id="project_id" value="<?= $this->data["project"]["id"] ?>">

<div>
    <div class="timeline-wrap">
        <div class="timeline-group d-flex" style="height: 40px">
            <a class="no-decoration timeline-elem <?= !isset($_GET["date"]) ? "selected-date" : "" ?>" href="<?= URI ?>/project/dashboard/<?= $this->data["project"]["id"] ?>">Все</a>
            <?php foreach ($this->data["project_month"] as $month): ?>
                <a class="no-decoration timeline-elem <?= $month["month_date"] === $_GET["date"] ? "selected-date" : "" ?>" href="<?= URI ?>/project/dashboard/<?= $this->data["project"]["id"] ?>/?date=<?= $month["month_date"] ?>"
                >
                    <?= $month["month_year_point"] ?>
                </a>
            <?php endforeach; ?>
            <i open-date-modal class="fa-solid fa-plus text-secondary cursor-pointer" style="font-size: 35px; margin-left: 5px"></i>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <h2 class="fw-bold">Проект - <?= $this->data["project"]["name"] ?></h2>
        <i <?= $_GET["date"]
            ? "open-month-project-modal"
            : "open-project-modal" ?>
           class="fa-solid fa-pen cursor-pointer text-dark"
        ></i>
    </div>

<!---->
<!--    <input type="number" id="secondmentTableSum">-->
<!--    <input type="number" id="transportTableSum">-->

    <table id="projectTable" class="table table-striped journal mb-3">
        <thead>
        <tr>
            <th scope="col" class="text-center">План сумма</th>
            <th scope="col" class="text-center">Общая сумма</th>
            <th scope="col" class="text-center">Рентабельность</th>
            <th scope="col" class="text-center">ФОТ</th>
            <th scope="col" class="text-center">Накладные расходы</th>
            <th scope="col" class="text-center">Командировки</th>
            <th scope="col" class="text-center">Бензин</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <h2>Список командировок</h2>
    <table id="secondmentTable" class="table table-striped journal mb-3">
        <thead>
        <tr>
            <th scope="col" class="wd-40 text-center">#</th>
            <th scope="col" class="wd-120 text-center">Дата</th>
            <th scope="col" class="wd-400 text-center">ФИО</th>
            <th scope="col" class="text-center">Планновые расходы</th>
            <th scope="col" class="text-center">Фактические расхды</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <h2>Отчеты по бензину</h2>
    <table id="transportTable" class="table table-striped journal mb-3">
        <thead>
        <tr>
            <th scope="col" class="wd-40 text-center">#</th>
            <th scope="col" class="wd-400 text-center">ФИО</th>
            <th scope="col" class="wd-400 text-center">Дата</th>
            <th scope="col" class="wd-120 text-center">Транспорт</th>
            <th scope="col" class="text-center">Сумма</th>
        </tr>
        </thead>
    </table>
    <h2>Накладные расходы</h2>
    <table id="overheadTable" class="table table-striped journal mb-3">
        <thead>
        <tr>
            <th scope="col" class="col-1 text-center">#</th>
            <th scope="col" class="text-center">Сумма</th>
            <th scope="col" class="text-center">Дата</th>
        </tr>
        </thead>
    </table>
</div>

<form id="add-date-modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
      action="/ulab/project/addDate" method="post">
    <div class="title mb-3 h-2">
        Данные регистрации
    </div>

    <div class="line-dashed-small"></div>

    <input hidden type="number" name="project_id" value="<?= $this->data["project"]["id"] ?>">

    <div class="row mb-3">
        <div class="col" style="width: 400px">
            <label for="date">Месяц отчета:</label>

            <input type="month" class="form-control" name="date" min="2018-03" value="<?= date("Y-m") ?>" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="plan_expenses">Сумма:</label>
            <input type="number" class="bg-white" name="plan_expenses">
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="submit" id="add-date" class="btn btn-primary">Сохранить</button>
        <button type="button" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>

<form id="project-modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Изменить
    </div>

    <div class="row mb-3">
        <div class="d-flex align-items-center">
            <label class="col-5" for="plan_expenses">Сумма:</label>
            <input class="col-7" type="number" class="bg-white" name="plan_expenses">
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" id="save-project" class="btn btn-primary">Сохранить</button>
        <button type="button" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>


<form id="month-project-modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Изменить
    </div>

    <div class="row mb-3">
        <div class="d-flex align-items-center">
            <label class="col-5" for="month_plan_expenses">Сумма за месяц:</label>
            <input class="col-7 bg-white" type="number" name="month_plan_expenses">
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" id="save-month-project" class="btn btn-primary">Сохранить</button>
        <button type="button" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>