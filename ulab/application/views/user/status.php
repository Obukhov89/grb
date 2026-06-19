<style>
    .toast-save-permission {
        min-width: 500px;
    }
    .user-edit {
        text-decoration: none;
    }
    .arrowRight {
        top: 232px;
    }

    .arrowLeft {
        top: 173px;
    }
    .select2-container .select2-selection--single {
        padding: 0.25rem 0.15rem;
        height: auto;
        border-radius: 0.25rem;
        box-sizing: border-box;
        min-width: 100%;
        border: var(--bs-border-width) solid var(--bs-border-color);
    }
    .select2-results__option.select2-results__option--selectable {

    }
</style>

<header class="header-requirement mb-3 pt-0">
    <nav class="header-menu w-100">
        <ul class="nav w-100">
            <li class="nav-item me-3">
                <a class="nav-link fa-solid icon-nav fa-arrow-left disabled" id="back-button" style="font-size: 22px;" title="Назад" data-bs-toggle="tooltip">
                </a>
            </li>
            <li class="nav-item me-3">
                <a class="nav-link fa-solid icon-nav fa-rectangle-list" href="<?= URI ?>/import/list" style="font-size: 22px;" title="Профиль лаборатории" data-bs-toggle="tooltip">
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link fa-solid icon-nav fa-user" style="font-size: 22px; margin: 2px 0 0 1px;" href="<?=URI?>/user/list/" title="Пользователи" data-bs-toggle="tooltip">
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link fa-solid icon-nav fa-exchange disabled" style="font-size: 22px; margin: 2px 0 0 1px;" href="<?=URI?>/user/status/" title="Статусы (вы здесь)" data-bs-toggle="tooltip">
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link fa-solid fa-cog icon-nav" style="font-size: 22px; margin: 2px 0 0 1px;" href="<?=URI?>/permission/list/" title="Роли" data-bs-toggle="tooltip">
                </a>
            </li>
<!--            <li class="nav-item me-5">-->
<!--                <a class="nav-link fa-solid fa-user-tie icon-nav" style="font-size: 22px; margin: 2px 0 0 1px;" href="--><?php //=URI?><!--/import/companyEmployees/" title="Руководители" data-bs-toggle="tooltip">-->
<!--                </a>-->
<!--            </li>-->

            <li class="nav-item ms-auto">
                <button class="btn btn-gradient users-update-status-trigger disabled" title="Обновить статусы и/или замены" data-bs-toggle="tooltip">Сохранить изменения</button>
            </li>
        </ul>
    </nav>
</header>

<table id="journal_users" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap">ФИО</th>
        <th scope="col" class="text-nowrap">Статус</th>
        <th scope="col" class="text-nowrap">Замена</th>
        <th scope="col" class="text-nowrap">Должность</th>
        <th scope="col" class="text-nowrap">Заметка</th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search" placeholder="Введите часть имени или фамилии">
        </th>
        <th scope="col">
            <select class="form-control search">
                <option value="-1">Любой статус</option>
                <?php foreach ($this->data['statuses'] as $key => $value): ?>
                    <option value="<?=$key?>"><?=$value?></option>
                <?php endforeach; ?>
            </select>
        </th>
        <th scope="col">
            <select class="form-control search">
                <option value="-1">Показать всех</option>
                <option value="1">Только с заменой</option>
                <option value="2">Только без замены</option>
            </select>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" placeholder="Введите должность">
        </th>
        <th scope="col">

        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="line-dashed"></div>


<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast toast-save-permission w-175" role="alert" aria-live="assertive" aria-atomic="true"  data-bs-delay="7000">
        <div class="toast-header">
            <strong class="me-auto">Внимание!</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Не забудьте нажать на кнопку "Сохранить изменения" вверху страницы.
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="display: none" id="ajax-loading-message">
    <div class="toast show" style="width: 400px;">
        <div class="toast-header">
            <strong class="me-auto">Пожалуйста, подождите, данные загружаются...</strong>
        </div>
    </div>
</div>