<header class="header-secondment mb-4">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link link-back" href="/ulab/contractor/journal/" title="Вернуться в журнал">
                    <svg class="icon" width="25" height="25">
                        <use xlink:href="/ulab/assets/images/icons.svg#back"></use>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</header>
<div class="col-4">
    <form enctype="multipart/form-data" method="post" action="/ulab/contractor/updateUser">
        <input type="number" name="id" value="<?= $this->data["card"]["id"] ?>" hidden>
        <div class="mb-3">
            <input type="text" class="form-control" id="fio" name="fio" placeholder="ФИО" value="<?= htmlspecialchars($this->data["card"]["fio"]) ?>">
        </div>
        <div class="mb-3">
            <input type="text" class="form-control" id="phone" name="phone" placeholder="Телефон" value="<?= htmlspecialchars($this->data["card"]["phone"]) ?>">
        </div>
        <div class="mb-3">
            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Название компании" value="<?= htmlspecialchars($this->data["card"]["company_name"]) ?>">
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
</div>
