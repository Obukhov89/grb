<header class="header-requirement mb-3">
    <div class="row">
        <div class="col-2">
            <nav class="header-menu">
                <ul class="nav">
                    <li class="nav-item me-1">
                        <button class="btn" id="add-zern" title="Добавить новое сито">
                            <svg class="icon" width="20" height="20">
                                <use xlink:href="<?=URI?>/assets/images/icons.svg#add"/>
                            </svg>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<table id="journal_grain" class="table table-striped journal">
    <thead>
        <tr class="table-light">
            <th scope="col" class="text-nowrap">Название материала</th>
        </tr>
        <tr class="header-search">
            <th scope="col">
                <input type="text" class="form-control search">
            </th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<form id="new_zern" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/grain/addZern" method="post" enctype="multipart/form-data">
    <div class="title mb-3 h-2">
        Добавить сита
    </div>

    <div class="line-dashed-small"></div>

    <div class="mb-3">
        <label class="form-label" for="name">Название</label>
        <input type="text" class="form-control w-100 bg-white" id="name" name="name" step="any" required>
    </div>

    <div class="mb-3">
        <label class="form-label" for="gost">ГОСТ</label>
        <select class="form-control w-100 bg-white js-example-basic-single client-select select2" id="gost" name="gost" step="any" required>

        </select>
    </div>

    <div class="mb-3">
        <label class="form-label" for="group">Группа материала</label>
        <select class="form-control w-100 bg-white js-example-basic-single client-select select2" id="group" name="group" step="any" required>

        </select>
    </div>

    <button type="submit" class="btn btn-primary">Создать</button>
</form>