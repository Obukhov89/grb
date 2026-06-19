<header class="header-secondment mb-4">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link link-back" href="/ulab/schemeEditor/index" title="Вернуться назад">
                    <svg class="icon" width="25" height="25">
                        <use xlink:href="<?= URI ?>/assets/images/icons.svg#back"></use>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</header>
<input value="<?= $this->data['scheme']['id'] ?>" id="card_id" type="hidden"/>
<h3>Тип работ: <?= $this->data["scheme"]["work_type"] ?></h3>
<h3>Схема: <?= $this->data['scheme']["name"] ?></h3>

<div class="d-flex justify-content-between gap-3" style="width: 100%">
    <button type="button" data-js-create-docs id="add-entry"
            title="Добавить тип исполнительной документации"
            class="btn btn-primary popup-with-form btn-add-entry mw-100 mt-0">
        Добавить ИД
    </button>
    <form action="/ulab/schemeEditor/deleteScheme" method="POST" id="delete_form">
        <input value="<?= $this->data['scheme']['id'] ?>" name="scheme_id" id="scheme_id" type="hidden"/>
        <button onclick="deleteOSKScheme();" type="button" class="btn btn-danger" data-js-del-scheme id="deleteScheme">
            Удалить
        </button>
    </form>
</div>

<div class="scroll mt-3 mb-3" style="position: relative">
    <div class="table-wrap">
        <table id="table" class="table table-striped journal" style="min-width: 100%">
            <thead>
            <tr class="table-light">
                <th class="text-center">Тип исполнительной документации</th>
                <th class="text-center wd-100"></th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<form method="POST" action="/ulab/schemeEditor/createIDType" id="add-entry-modal-form"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div data-js-title class="title mb-3 h-2">
        Добавить ИД
    </div>

    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col custom-select2" style="width: 600px">
            <select
                    name="type_id"
                    class="form-control h-auto user"
                    id="gost"
                    required
                    style="width: 100%"
            >
                <option value="">Добавить ИД</option>
                <?php foreach ($this->data["id_types"] as $type): ?>
                    <option value="<?= $type["id"] ?>"><?= $type["name"] ?></option>
                <?php endforeach; ?>
            </select>
            <input value="<?= $this->data['scheme']['id'] ?>" name="card_id" type="hidden"/>
            <button data-js-create-id type="button" style="margin: 5px 0px 0px 0px;">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="submit" id="add-entry-modal-btn" class="btn btn-primary">Сохранить</button>
        <button type="button" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>

</form>


<form method="POST" action="/ulab/schemeEditor/createId" id="add-id-modal-form"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div data-js-title class="title mb-3 h-2">
        Добавить ИД в список
    </div>

    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col custom-select2" style="width: 600px">
            <input name="name" type="text" class="form-control" placeholder="Введите название ИД"/>
            <input value="<?= $this->data['scheme']['id'] ?>" name="card_id" type="hidden"/>
        </div>
    </div>
    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="submit" id="add-entry-modal-btn" class="btn btn-primary">Сохранить</button>
        <button type="button" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>

</form>

<form method="POST" action="/ulab/schemeEditor/editIDType" id="edit-id-modal-form"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div data-js-title class="title mb-3 h-2">
        Редактировать тип ИД
    </div>

    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col custom-select2" style="width: 600px">
            <input name="name" type="text" class="form-control" placeholder="Введите название ИД" id="idtype_name"/>
            <input name="type_id" id="type_id" type="hidden"/>
            <input name="card_id" id="card_id" type="hidden"/>
        </div>
    </div>
    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="submit" id="add-entry-modal-btn" class="btn btn-primary">Сохранить</button>
        <button type="button" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>

<script>
    function deleteOSKScheme() {
        let conf = confirm("Удалить схему?");

        if (!conf)
            return;

        $('#delete_form').submit();
    }
</script>