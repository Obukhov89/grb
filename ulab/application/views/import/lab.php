<?php
$permissions = $this->data['permissions'];
?>

<style>
    .header-menu, ul.nav {
        width: 100%;
    }

    .header-menu .nav-item {
        display: flex;
        align-items: center;
        justify-content: center;
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
    #workarea input.form-control, #workarea select.form-control, .mfp-content .form-control {
        min-width: auto;
    }
</style>

<header class="header-requirement mb-3 pt-0">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-3">
                <a class="nav-link fa-solid icon-nav fa-arrow-left disabled" id="back-button" style="font-size: 22px;" title="Назад" data-bs-toggle="tooltip">
                </a>
            </li>
            <li class="nav-item me-3">
                <a class="nav-link fa-solid icon-nav fa-rectangle-list" href="<?= URI ?>/import/list" style="font-size: 22px;" title="Профиль лаборатории" data-bs-toggle="tooltip">
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link fa-solid icon-nav fa-flask disabled" href="<?=URI?>/import/lab/" title="Отделы" data-bs-toggle="tooltip" style="font-size: 22px; margin: 2px 0 0 1px;">
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link fa-solid icon-nav fa-door-closed" href="<?=URI?>/import/rooms/" title="Помещения" data-bs-toggle="tooltip" style="font-size: 22px; margin: 2px 0 0 1px;">
                </a>
            </li>
             <li class="nav-item ms-auto">
                <button class="btn btn-gradient popup-with-form rounded" type="button" title="Добавить новый отдел" data-bs-toggle="tooltip" style="text-transform:none;">Добавить отдел</button>
            </li>
        </ul>
    </nav>
</header>


<table id="journal_labs" class="table table-striped journal" style="width: 100%">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap">Наименование</th>
        <th scope="col" class="text-nowrap">Начальник</th>
        <th scope="col" class="text-nowrap"></th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search" placeholder="Введите название отдела">
        </th>
        <th scope="col">
            <select class="form-control search">
                <option value="-1">Все</option>
                <option value="-2">Не указан</option>
                <option value="-3">Указан</option>
                <?php
                foreach ($this->data['dept_head'] as $dept) {
                    echo '<option value="' . $dept['ID_HEAD_USER'] . '">' . $dept['FULL_NAME'] . '</option>';
                }
                ?>
            </select>
        </th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="display: none" id="ajax-loading-message">
    <div class="toast show" style="width: 400px;">
        <div class="toast-header">
            <strong class="me-auto">Пожалуйста, подождите, данные загружаются...</strong>
        </div>
    </div>
</div>


 <form id="lab-modal-form" class="bg-light mfp-hide col-md-5 m-auto p-3 position-relative"
          action="<?= URI ?>/import/insertUpdateDepartment/" method="post">
        <div class="title mb-3 h-2">
            Добавление отделения
        </div>

        <div class="line-dashed-small"></div>

        <input type="hidden" id="deptId" name="form_dept[ID]" value="">

        <div class="mb-3">
            <label class="form-label mb-1">Наименование</label>
            <input type="text" class="form-control" id="name" name="form_dept[NAME]" step="1" required
                   value="<?= $this->data['form_dept']['NAME'] ?? '' ?>" placeholder="Введите наименования отдела">
        </div>

         <div class="mb-3">
            <label class="form-label mb-1 d-flex flex-column" for="head_id">Начальник отдела</label>
            <select name="form_dept[HEAD_ID]" id="head_id" class="form-control section head_id" style="width: 100%" required>
                <option value="-1">Не указан</option>
                <?php
                    foreach ($this->data['users'] as $user) {
                        $userId = $user['ID'];

                        echo '<option value="' . $userId . '">';
                        echo $user['LAST_NAME'] . ' ' . $user['NAME'];
                        echo '</option>';
                    }
                ?>
            </select>
        </div>

         <div class="mb-3">
            <label class="form-label mb-1">Роль начальника</label>
           <select class="form-control" id="parent_role" name="form_dept[HEAD_ROLE_ID]" disabled="disabled">
                <option value="-1" disabled>Выберите роль</option>
                <?php foreach ($permissions as $permission): ?>
                    <?php if ($permission['view_name'] != 'default'): ?>
                         <option value="<?=$permission['id']?>">
                             <?=$permission['name']?>
                         </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary form-button">Сохранить</button>
    </form>
    <!--./room-modal-form-->

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->
</div>