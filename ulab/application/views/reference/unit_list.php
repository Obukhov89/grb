<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <?php if ($this->data['method_id']): ?>
                <li class="nav-item me-2">
                    <a class="nav-link" href="<?=URI?>/gost/method/<?=$this->data['method_id']?>" title="Вернуться в методику">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item me-2">
                <button class="btn btn-outline-secondary sync-data" type="button" title="Синхронизация данных с ФСА">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    Синхронизировать
                </button>
            </li>
        </ul>
    </nav>
</header>

<div class="panel panel-default">
    <header class="panel-heading">
        Единици измерения
        <span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
        </span>
    </header>
    <div class="panel-body">
        <table id="journal" class="table table-striped journal">
            <thead>
            <tr class="table-light">
                <th scope="col"></th>
                <th scope="col">ИД ФСА</th>
                <th scope="col">Единица</th>
                <th scope="col">Название</th>
                <th scope="col">Используется</th>
            </tr>
            <tr class="header-search">
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col">
                    <input type="text" class="form-control search">
                </th>
                <th scope="col">
                    <input type="text" class="form-control search">
                </th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>