<div class="filters mb-3">
    <div class="row">
        <div class="col-auto">
            <button type="button" name="add_entry" class="btn btn-primary popup-with-form btn-add-entry w-100 mw-100 mt-0">
                Добавить раствор
            </button>
        </div>

    </div>
</div>
<!--./filters-->

<table id="solution_journal" class="table table-striped text-center">
    <thead>
    <tr class="table-light align-middle">
        <th scope="col">Наименование раствора</th>
        <th scope="col">Название рецепта</th>
        <th scope="col">Дата приготовления</th>
        <th scope="col">Ответственный</th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search ">
        </th>
        <th scope="col">
            <input type="text" class="form-control search"disabled>
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

<form id="add-entry-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
      action="/ulab/solution/addSolution/" method="post">
    <div class="title mb-3 h-2">
        Данные регистрации
    </div>

    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label" for="nameSolution">Название рецепта</label>
            <input type="text" name="name_solution" class="form-control name-solution" id="nameSolution"
                   value="" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Выберите рецепт <span class="redStars">*</span></label>
            <select name="id_model_recipe" class="form-control  select-recipe h-auto" required>
                <option value="" selected disabled></option>
                <?php foreach ($this->data['model_recipe'] as $val): ?>
                    <option value="<?= $val['id'] ?? '' ?>"><?= $val['name_recipe'] ?? '' ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <lable for="datePreparation">Дата приготовления раствора</lable>
            <input type="date" class="form-control probe-date bg-white" id="datePreparation" name="date_preparation"
                   value="<?= date('Y-m-d') ?>" required>
        </div>
    </div>


    <button type="submit" class="btn btn-primary">Отправить</button>
</form>
<!--./add-entry-modal-form-->