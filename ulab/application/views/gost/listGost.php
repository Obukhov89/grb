<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/gost/new/" title="Новый ГОСТ">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </li>

            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/gost/list/" title="Журнал области аккредитации">
                    <i class="fa-solid fa-list"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<table id="journal_gost" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap">Номер документа</th>
        <th scope="col" class="text-nowrap">Наименование документа</th>
        <th scope="col" class="text-nowrap">Год</th>
        <th scope="col" class="text-nowrap">Наименование объекта</th>
        <th scope="col">Кол-во методик</th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
