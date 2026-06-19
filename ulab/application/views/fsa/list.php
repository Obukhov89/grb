<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/fsa/" title="Вернуться">
                    <i class="fa-solid fa-house"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/fsa/settings/" title="Настройки">
                    <i class="fa-solid fa-gear"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/fsa/protocol/" title="Протоколы">
                    <i class="fa-regular fa-file-lines icon-big"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<div class="filters mb-3">
    <div class="row">
        <div class="col-auto filter-search" id="filter_search">
            <button class="filter-btn-search">
                <svg class="bi" width="20" height="20">
                    <use xlink:href="<?=URI?>/assets/images/icons.svg#icon-search"/>
                </svg>
            </button>
            <div id="journal_filter" class="dataTables_filter">
                <label>
                    <input id="filter_everywhere" type="search" class="form-control filter" placeholder="Поиск..." aria-controls="journal_requests">
                </label>
            </div>
        </div>
        <div class="col">
            <input type="date" id="inputDateStart" class="form-control filter filter-date-start" value="<?=$this->data['date_start'] ?? '2010-01-01'?>" placeholder="Введите дату начала:">
        </div>

        <div class="col">
            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end" value="<?=date('Y-m-d')?>" placeholder="Введите дату окончания:">
        </div>

        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
        </div>
    </div>
</div>

<table id="journal_fsa" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap">GUID</th>
        <th scope="col" class="text-nowrap">Дата</th>
        <th scope="col" class="text-nowrap">Метод</th>
        <th scope="col" class="text-nowrap">XML</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>