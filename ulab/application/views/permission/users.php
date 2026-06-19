<!--<div>-->
<!--	<pre>-->
<!--		--><?php //print_r($this->data['test']);?>
<!--	</pre>-->
<!--</div>-->

<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/permission/list/" title="Роли">
                    <svg class="icon" width="20" height="20">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#list"/>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</header>

<nav class="mb-3">
    <div class="row">
        <div class="col">
            <input type="text" class="form-control filter filter-fio" placeholder="ФИО">
        </div>
        <div class="col">
            <select  class="form-control filter filter-dep">
                <option value="">Выбрать отдел</option>
                <option value="Отдел не указан">Отдел не указан</option>
                <?php foreach ($this->data['department_list'] as $row): ?>
                    <option value="<?=$row?>"><?=$row?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <select  class="form-control filter filter-pos">
                <option value="">Выбрать должность</option>
                <option value="Должность не указана">Должность не указана</option>
                <?php foreach ($this->data['position_list'] as $row): ?>
                    <option value="<?=$row?>"><?=$row?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <select  class="form-control filter filter-role">
                <option value="">Выбрать роль</option>
                <?php foreach ($this->data['role_list'] as $permission): ?>
                    <option value="<?=$permission['id']?>"><?=$permission['name']?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <a href="<?=URI?>/permission/users/" class="btn btn-outline-secondary filter-btn-reset">Сбросить</a>
        </div>
    </div>
</nav>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php foreach ($this->data['users'] as $user): ?>
<!--    <form action="--><?//=URI?><!--/permission/updateUser/" method="post">-->
        <div class="col">
            <form class="card" action="<?=URI?>/permission/updateUser/" method="post">
                <div class="card-body" style="min-height: 80px;">
                    <input type="hidden" name="user_id" value="<?=$user['id']?>">
                    <h5 class="card-title"><?=$user['FIO']?></h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><?=$user['department']?></li>
                    <li class="list-group-item"><?=$user['position']?></li>
                    <li class="list-group-item">
                        <select name="role_id" id="role_id" class="form-control">
                            <?php foreach ($this->data['role_list'] as $permission): ?>
                                <option value="<?=$permission['id']?>" <?=$permission['id'] == $user['role']? 'selected': ''?>><?=$permission['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>
                </ul>
                <div class="card-body">
                    <button class="btn btn-primary float-end" type="submit">Применить</button>
                </div>
            </form>
        </div>
<!--    </form>-->
    <?php endforeach; ?>
</div>
