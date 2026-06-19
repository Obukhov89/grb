<ul class="nav">
    <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="scheme_edit.php">
            <button type="button" class="btn btn-outline-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-arrow-left"
                     viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"></path>
                </svg>
            </button>
        </a>
    </li>
</ul>
<div class="panel panel-default">
    <header class="panel-heading">
        Создание схемы
        <span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
         </span>
    </header>
    <div class="panel-body">
        <form class="form-horizontal" id="formTestSchemes" method="post"
              action="<?= URI ?>/import/insertUpdateSchemes/">
        <!--<form action="" method="post">-->
            <div class="form-group">
                <label for="name_scheme" class="form-label">Имя схемы:</label>
                <input class="form-control w-50" type="text" placeholder="Назовите схему" name="name_scheme" required>
            </div>
            <div class="form-group">
                <input type="checkbox" class="btn-check" id="united_method" name="united_methods"
                       value="1" <?= $data['united_method'] == 1 ? 'checked' : '' ?>>
                <label class="btn btn-outline-primary col-sm-2" for="united_method">Объединение методик</label>
            </div>
            <div class="form-group row <?= $data['united_method'] == 1 ? '' : 'visually-hidden' ?>  price_for_scheme">
                <div class="col-sm-4">
                    <div class="input-group">
                        <input type="float" min="0" step="0.01" class="form-control"
                               name="price_scheme" <?= $data['united_method'] == 1 ? '' : 'disabled' ?>
                               value="<?= $data['price'] ?>">
                        <span class="input-group-text">руб</span>
                    </div>
                </div>
            </div>
            <div class="form-group row <?= $data['united_method'] == 1 ? '' : 'visually-hidden' ?> unit_for_scheme"
                 style="padding-left: 0.7rem">
                <select name="unit_scheme" class="form-select" style="width: 32%">
                    <?php foreach ($this->data['units'] as $unit): ?>
                        <option value="<?= $unit['id'] ?>"><?= $unit['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Выберите методику для добавления в схему:</label>
                <br>
                <select id="select-gost" class="form-control">
                    <option value="0" selected disabled>Выберите</option>
                    <?php foreach ($this->data['gosts'] as $gost): ?>
                        <option value="<?= $gost['ID'] ?>" data-id="<?= $gost['ID'] ?>"
                                data-gost="<?= $gost['GOST'] ?>"
                                data-spec="<?= $gost['SPECIFICATION'] ?>"><?= $gost['GOST'] ?>
                            || <?= $gost['SPECIFICATION'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <table class="table table-hover w-50" id="table-gost">
                    <thead>
                    <tr>
                        <th scope="col">ГОСТ</th>
                        <th scope="col">Определяемая характеристика</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</div>