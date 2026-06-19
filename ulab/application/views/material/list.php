<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link popup-with-form" href="#material-modal-form" title="Добавить материал">
                    <i class="fa-solid fa-plus icon-fix"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<table id="journal_material" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col">Наименование материала</th>
        <th scope="col">Активный</th>
<!--        <th scope="col">Удалить</th>-->
    </tr>
    <tr class="header-search">
        <td>
            <input type="text" class="form-control search">
        </td>
        <td>
            <select class="form-control search">
                <option value="">Все</option>
                <option value="1">Активные</option>
                <option value="0">Не активные</option>
            </select>
        </td>
<!--        <td></td>-->
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>


<form id="material-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="<?=URI?>/material/add/" method="post">
    <div class="title mb-3 h-2">
        Добавление материала
    </div>

    <div class="line-dashed-small"></div>

    <div class="mb-3 col">
        <label class="form-label">Название <span class="redStars">*</span></label>
        <input type="text" class="form-control" name="name" maxlength="255" value="" required>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary disable-after-click">Сохранить</button>
</form>
