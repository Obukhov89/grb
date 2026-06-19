<div class="d-flex gap-3" style="width: 100%">
    <div class="col-auto">
        <button type="button" data-create-volume
                class="btn btn-primary w-100 mw-100 mt-0">
            Добавить
        </button>
    </div>
</div>
<input type="hidden" name="countColumns" value="<?= count($this->data['columnsData']) ?>" id="countColumns">
<div class="scroll mt-3 mb-3" style="position: relative">
    <div class="table-wrap">
        <table id="volumes-table" class="table table-striped journal" style="min-width: 100%">
            <thead>
            <tr class="table-light">
                <th class="text-center"></th>
                <th class="text-center">№ раздела</th>
                <th class="text-center">Вид работ</th>
                <th class="text-center">Наименование конструктива</th>
                <th class="text-center">Ед. изм.</th>
                <th class="text-center">Объем по проекту</th>
                <th class="text-center">Выполненый объем по ИД</th>
                <th class="text-center">Остаток на текущий месяц</th>
                <th class="text-center">Процент от общего выполнения (ИД)</th>
                <?
                  foreach ($this->data['columnsData'] as $columnName) {
                      echo '<th class="text-center" data-js-column="' . $columnName . '">' . $columnName . '</th>';
                  }
                ?>
            </tr>
            </thead>
        </table>
        <div class="line-dashed"></div>
    </div>
</div>

<form id="createVolume"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Создать новую запись</h3>
    <div class="line-dashed-small"></div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="sectionNumber" class="col-form-label">Номер раздела</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="sectionNumber" name="sectionNumber" class="form-control justify-content-between">
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="workType" class="col-form-label">Вид работ</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="workType" name="workType" class="form-control justify-content-between">
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="structureName" class="col-form-label">Наименование конструктива</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="structureName" name="structureName" class="form-control justify-content-between">
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="unit" class="col-form-label">Единица измерения</label>
        </div>
        <div class="col-sm-6">
            <select required name="unit" id="unit"></select>
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="projectVolumes" class="col-form-label">Объемы по проекту</label>
        </div>
        <div class="col-sm-6">
            <input required type="number" id="projectVolumes" name="projectVolumes" class="form-control justify-content-between" step="0.01" pattern="\d+(\.\d{1,2})?">
        </div>
    </div>
    <div class="mt-3">
        <div class="alert alert-primary d-none" role="alert" style="    margin: auto;width: 353px;">
        </div>
        <div class="line-dashed-small"></div>
    </div>

    <button type="button" class="btn btn-primary" data-store-volume>Сохранить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button style="color: black;" type="button" class="mfp-close">×</button>
</form>

<form id="editVolume"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Редактировать запись</h3>
    <div class="line-dashed-small"></div>
    <input type="number" id="id" name="id" hidden>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="sectionNumber" class="col-form-label">Номер раздела</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="sectionNumber" name="sectionNumber" class="form-control justify-content-between">
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="workType" class="col-form-label">Вид работ</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="workType" name="workType" class="form-control justify-content-between">
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="structureName" class="col-form-label">Наименование конструктива</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="structureName" name="structureName" class="form-control justify-content-between">
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="unitName" class="col-form-label">Единица измерения</label>
        </div>
        <div class="col-sm-6">
            <select required name="unit" id="unitName"></select>
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="projectVolumes" class="col-form-label">Объемы по проекту</label>
        </div>
        <div class="col-sm-6">
            <input required type="number" id="projectVolumes" name="projectVolumes" class="form-control justify-content-between" step="0.01" pattern="\d+(\.\d{1,2})?">
        </div>
    </div>
    <div class="mt-3">
        <div class="alert alert-primary d-none" role="alert" style="    margin: auto;width: 353px;">
        </div>
        <div class="line-dashed-small"></div>
    </div>

    <button type="button" class="btn btn-primary" data-update-volume>Сохранить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button style="color: black;" type="button" class="mfp-close">×</button>
</form>