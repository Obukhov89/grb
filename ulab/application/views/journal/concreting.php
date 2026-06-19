<style>
    .red-status {
        background-color: #d95252;
    }

    .blue-status {
        background-color: #4444f5;
    }

    .yellow-status {
        background-color: #dfdf40;
    }

    .purple-status {
        background-color: #7878da;
    }
</style>

<div class="scroll mt-3 mb-3" style="position: relative">
    <div class="table-wrap">
        <table id="volumes-table" class="table table-striped journal" style="min-width: 100%">
            <thead>
            <tr class="table-light">
                <th class="text-center">Вид работы</th>
                <th class="text-center">в осях</th>
                <th class="text-center">отметка</th>
                <th class="text-center">Дата бетонирования</th>
                <th class="text-center">Класс бетона</th>
                <th class="text-center">7 суток (Дата испытаний)</th>
                <th class="text-center">28 суток (Дата испытаний)</th>
                <th class="text-center">7 суток (Кубы)</th>
                <th class="text-center">28 суток (Кубы)</th>
                <th class="text-center">7 суток (НК)</th>
                <th class="text-center">28 суток (НК)</th>
            </tr>
            </thead>
        </table>
        <div class="line-dashed"></div>
    </div>
</div>

<form id="editСoncreting"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" enctype="multipart/form-data">
    <h3>Редактировать запись</h3>
    <div class="line-dashed-small"></div>
    <input type="number" id="diaryJournal" name="diaryJournal" hidden>
    <input type="text" id="elemType" name="elemType" hidden>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="status" class="col-form-label">Статус</label>
        </div>
        <div class="col-sm-6">
            <select required id="status" name="status"></select>
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between">
        <div class="col-sm-6">
            <label for="protocol" class="col-form-label">№ протокола</label>
        </div>
        <div class="col-sm-6">
            <input required type="text" id="numberProtocol" name="numberProtocol"
                   class="form-control justify-content-between">
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between" data-js-file-upload>
        <div class="col-sm-6">
            <label for="protocol" class="col-form-label">Файл протокола</label>
        </div>
        <div class="col-sm-6">
            <input required type="file" id="protocol" name="protocol" class="form-control justify-content-between">
        </div>
    </div>
    <div class="row g-3 mt-1 align-items-center justify-content-between" data-js-file>
        <div class="col-sm-6">
            <div class="col-form-label">Файл протокола</div>
        </div>
        <div class="col-sm-6" data-js-file-exists>

        </div>
    </div>
    <button type="button" class="btn btn-primary" data-update-concreting>Сохранить</button>
    <button type="button" data-js-close-modal class="btn btn-secondary">Закрыть</button>
    <button style="color: black;" type="button" class="mfp-close">×</button>
</form>
<div class="mt-3">
    <div class="alert alert-primary d-none" role="alert" style="margin: auto;width: 353px;">
    </div>
    <div class="line-dashed-small"></div>
</div>