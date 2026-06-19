<div class="filters mb-3">
    <div class="row">
        <div class="col-auto">
            <div class="two-button">
                <button type="button" name="add_entry"
                        class="btn btn-primary popup-first btn-add-entry w-100 mw-100 mt-0 btn-reactive">
                    Добавить рецепт
                </button>
                <button type="button" name="add_entry"
                        class="btn btn-primary popup-second btn-add-entry w-100 mw-100 mt-0 btn-reactive">
                    Раствор как реактив
                </button>
            </div>
        </div>
    </div>
</div>
<!--./filters-->

<table id="recipe_journal" class="table table-striped text-center">
    <thead>
    <tr class="table-light align-middle">
        <th scope="col">Имя</th>
        <th scope="col">Концентрация</th>
        <th scope="col">Нормативный документ</th>
        <th scope="col">Реактивы</th>
        <th scope="col">Растворитель</th>
        <th scope="col">К-во раствора</th>
        <th scope="col">Срок годности</th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" id="param" class="form-control search" value="<?= $this->data['param'] ?>">
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled>
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>


<svg width="0" height="0" class="hidden">
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="arrow-left">
        <path d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm11.5 5.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
    </symbol>
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="arrow-right">
        <path d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4.5 5.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
    </symbol>
</svg>

<form id="add-entry-modal-form-first" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
      action="/ulab/recipe/addModelRecipe/" method="post">
    <div class="title mb-3 h-2">
        Рецепт
    </div>
    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label class="form-label" for="nameRecipe">Название рецепта</label>
            <input type="text" name="name_recipe" class="form-control name-recipe" id="nameRecipe"
                   value="" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Нормативный документ </label>
            <select name="id_doc" class="form-control bg-white select-doc" required>
                <option value="" selected disabled></option>
                <?php
                foreach ($this->data['doc'] as $val): ?>
                    <option value="<?= $val['ID'] ?? '' ?>"><?= $val['GOST'] . " " . $val['SPECIFICATION'] ?></option>
                <?php
                endforeach; ?>
            </select>
        </div>
    </div>
    <div class="line-dashed-small"></div>
    <div>
        <button type="button" class="btn btn-secondary add-reactive">Добавить реактив</button>
        <button type="button" class="btn btn-secondary del-reactive">Удалить реактив</button>
    </div>
    <div class="line-dashed-red"></div>
    <div class="reactives" data-id="1">
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Реактив</label>
                <select name="reactives[reactive1][id_reactive]" class="form-control select-reactive" required>
                    <option value="" selected disabled></option>
                    <?php
                    foreach ($this->data['reactive'] as $val): ?>
                        <option value="<?= $val['ID'] ?? '' ?>"><?= $val['REACT_NAME'] ?? '' ?></option>
                    <?php
                    endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Количество, г </label>
                <input type="number" name="reactives[reactive1][quantity_reactive]" step="0.01" min="0" max=""
                       class="form-control bg-white"
                       value="" required>
            </div>
        </div>
        <div class="line-dashed-red"></div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Растворитель</label>
            <select name="solvent[id_reactive]" class="form-control select-solution" required>
                <option value="" selected disabled></option>
                <?php
                foreach ($this->data['reactive'] as $val): ?>
                    <option value="<?= $val['ID'] ?? '' ?>"><?= $val['REACT_NAME'] ?? '' ?></option>
                <?php
                endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Объем растворителя, мл </label>
            <input type="number" name="solvent[quantity_reactive]" step="0.01" min="0" max="10000"
                   class="form-control bg-white"
                   value="" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Объем раствора, мл </label>
            <input type="number" name="quantity_solution" step="1" min="0" max="10000" class="form-control bg-white"
                   value="" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Концентрация </label>
            <input type="number" name="concentration" step="1" min="0" max="10000" class="form-control bg-white"
                   value="">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Срок годности, сутки </label>
            <input type="number" name="storage_life_in_day" step="1" min="0" max="10000" class="form-control bg-white"
                   value="" required>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Отправить</button>
</form>

<form id="add-entry-modal-form-second" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
      action="/ulab/recipe/addSolutionAsReactive/" method="post">
    <div class="title mb-3 h-2">
        Раствор как реактив
    </div>

    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Название реактива</label>
            <input type="text" name="reactive_model[name]" class="form-control name-recipe"
                   value="" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Рецепт</label>
            <select name="recipe[id_recipe_model]" class="form-control select-recipe h-auto" required>
                <option value="" selected disabled></option>
                <?php
                foreach ($this->data['recipe'] as $val): ?>
                    <option value="<?= $val['id'] ?? '' ?>"><?= $val["name_recipe"] . " C= " . $val["concentration"] ?></option>
                <?php
                endforeach; ?>
            </select>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
</form>

<!--./add-entry-modal-form-->