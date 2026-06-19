<style>
    table {
        width: 100% !important;
    }
</style>
<div class="container mt-4">
    <form action="/ulab/applicationCard/updateAppCard" method="post" enctype="multipart/form-data" id="app_form">
        <button onclick="$('#app_form').submit();" type="button" class="btn btn-success mb-3">Сохранить</button>
        <a class="btn btn-secondary mb-3" href="/ulab/execJournal/index">Назад</a>
        <?php
        if ($this->data['row']['closed']) { ?>
            <a class="btn btn btn-warning mb-3 float-end"
               href="/ulab/applicationCard/openCard?cardId=<?= $this->data['rowId'] ?>">Возобновить заявку</a>

        <?php } else { ?>
            <a class="btn btn btn-warning mb-3 float-end"
               href="/ulab/applicationCard/closeCard?cardId=<?= $this->data['rowId'] ?>">Закрыть заявку</a>
        <?php } ?>
        <div class="card">
            <div class="card-header">
                Общая информация
            </div>
            <div class="card-body">
                <h5 class="card-title">Место работ</h5>
                <p class="card-text"><?= $this->data['row']['work_place'] ?></p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                Информация о заявке
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="fw-bold mb-0">Подрядчик</p>
                        <p><?= $this->data['row']['company_name'] ?></p>
                        <p class="fw-bold mt-3 mb-0">Наименование работ</p>
                        <p><?= $this->data['row']['content'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="fw-bold mb-0">Акт</p>
                        <p><?= $this->data['row']['act'] ?></p>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <p class="fw-bold mb-0">Номер заявки</p>
                        <p><?= $this->data['row']['application_number'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="fw-bold mb-0">Дата приемочной комиссии</p>
                        <p><?= $this->data['row']['datetime'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="fw-bold mb-0"><label for="work_type_select">Схема ИД</label></p>
                        <select id="work_type_select" class="form-select" name="scheme_id">
                            <option value="0">Выбрать</option>
                            <?php foreach ($this->data['work_types'] as $workType) { ?>
                                <optgroup label="<?= $workType['work_type'] ?>">
                                    <?php foreach ($workType['scheme_list'] as $scheme) { ?>
                                        <option <?php if ($this->data['row']['scheme_id'] == $scheme['id']) echo "selected"; ?>
                                                value="<?= $scheme['id'] ?>"><?= $scheme['name'] ?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="comment" class="fw-bold">Общий комментарий</label>
                    <textarea name="general_comment" style="height: 100%; width: 50%;" class="form-control"
                              id="comment"><?= $this->data['row']['general_comment'] ?? "" ?></textarea>
                </div>
            </div>
        </div>

        <input type="hidden" value="<?= $this->data['rowId'] ?>" name="contractorId"/>
        <div class="card mt-4">
            <div class="card-header">
                Информация о заявке
            </div>
            <div class="card-body">
                <table id="application_card_table" class="table table-striped">
                    <thead>
                    <tr class="table-light">
                        <th scope="col" class="col-md-3">ВИД ИД</th>
                        <th scope="col">Чек-бокс</th>
                        <th scope="col">Фото/Скан</th>
                        <th scope="col">Комментарии</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    <button onclick="$('#app_form').submit();" type="button" class="btn btn-success mt-3">Сохранить</button>
    <a class="btn btn-secondary mt-3" href="/ulab/execJournal/index">Назад</a>
    <?php
    if ($this->data['row']['closed']) { ?>
        <a class="btn btn btn-warning mt-3 float-end"
           href="/ulab/applicationCard/openCard?cardId=<?= $this->data['rowId'] ?>">Возобновить заявку</a>

    <?php } else { ?>
        <a class="btn btn btn-warning mt-3 float-end"
           href="/ulab/applicationCard/closeCard?cardId=<?= $this->data['rowId'] ?>">Закрыть заявку</a>
    <?php } ?>
</div>