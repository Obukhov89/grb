<div class="filters mb-3">
    <div class="row">
        <div class="col-auto">
            <button open-row-modal type="button"
                    class="btn btn-primary w-100 mw-100 mt-0">
                Добавить запись в журнал
            </button>
        </div>

        <div class="col">
            <select class="form-select" id="project_id">
                <?php foreach ($this->data["projects"] as $project): ?>
                    <option value="<?= $project["id"] ?>"
                            <?= $_GET["project_id"] != "" && $_GET["project_id"] == $project["id"] ? "selected" : "" ?>
                    >
                        <?= $project["name"] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col">
            <input type="date" id="date_start" class="form-control filter filter-date-start"
                   value="<?= $_GET["date_start"] ?? '' ?>" placeholder="Введите дату начала:">
        </div>

        <div class="col">
            <input type="date" id="date_end" class="form-control filter filter-date-end"
                   value="<?= $_GET["date_end"] ?? '' ?>" placeholder="Введите дату окончания:">
        </div>

        <div class="col-auto">
            <button id="reset-btn" type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
        </div>

        <div class="col-auto">
            <button id="search-btn" type="button"
                    class="btn btn-primary w-100 mw-100 mt-0">
                Найти
            </button>
        </div>
    </div>
</div>

<div>
    <table id="table" class="table table-striped text-center journal" style="width=100%; min-width: 100%">
        <thead>
            <tr class="table-light align-middle">
                <th class="col-1">#</th>
                <th class="col-4">Сумма</th>
                <th class="col-3" >Дата</th>
                <th class="col-3">Проект</th>
                <th class="col-1"></th>
            </tr>
        </thead>
    </table>
</div>

<form id="row-modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Оплата
    </div>

    <input hidden type="number" name="id">

    <div class="mb-3">
        <label for="sum">Сумма:</label>
        <input class="bg-white" type="number" name="sum">
    </div>

    <div class="mb-3">
        <label for="date">Дата оплаты:</label>
        <input id="date" class="form-control" type="date" name="date" />
    </div>

    <div class="mb-3">
        <select class="form-select" name="project_id">
            <?php for($i = 1; $i < count($this->data["projects"]); $i++): ?>
                <option value="<?= $this->data["projects"][$i]["id"] ?>">
                    <?= $this->data["projects"][$i]["name"] ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" id="update-row" class="btn btn-primary">Сохранить</button>
        <button type="button" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>