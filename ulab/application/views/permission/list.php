
<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/permission/users/" title="Пользователи">
                    <svg class="icon" width="20" height="20">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#list"/>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</header>

<div class="row">
    <div class="col-6">
        <h2>Роли</h2>
        <div class="list-group">
            <?php foreach ($this->data['permission_list'] as $permission): ?>
                <a href="<?=URI?>/permission/list/<?=$permission['id']?>" class="list-group-item list-group-item-action <?=$permission['id']==$this->data['role_id']? 'list-group-item-primary': ''?>" data-role_id="<?=$permission['id']?>"><?=$permission['name']?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-6">
        <h2>Доступы</h2>

        <form action="<?=URI?>/permission/updateRole/" method="post">
            <div class="bd-example mb-3">
                <fieldset>
                    <div class="mb-3">
                        <label for="disabledTextInput1" class="form-label">Название</label>
                        <input type="text" id="disabledTextInput" class="form-control" value="<?=$this->data['role_info']['name']?>" disabled readonly>
                    </div>
                    <div class="mb-3">
                        <label for="disabledTextInput2" class="form-label">Описание</label>
                        <textarea id="disabledTextInput2" class="form-control" disabled><?=$this->data['role_info']['description']?></textarea>
                    </div>
                    <div>
                        <label for="textInput" class="form-label">Домашняя страница <span class="redStars">*</span></label>
                        <input type="text" id="textInput" class="form-control" name="home_page" value="<?=$this->data['role_info']['home_page']?>" required>
                        <div id="passwordHelpBlock" class="form-text">
                            Начальная страница, на которую будет выкидывать при ошибках в доступе. Например: "/request/list/". Не забудьте выдать доступ к этой странице
                        </div>
                    </div>
                </fieldset>
            </div>

            <ul class="list-group mb-4">
                <?php foreach ($this->data['controller_method_list'] as $controller): ?>
                    <li class="list-group-item">

                        <input
                                id="controller-<?=$controller['name']?>"
                                class="form-check-input me-1 controller-name"
                                type="checkbox"
                                <?=$this->data['role_info']['permission'] == 'all'? 'disabled' : ''?>
                        >
                        <label for="controller-<?=$controller['name']?>">
                            <strong><?=$controller['name']?></strong> <span class="annotation-desc"><?=$controller['desc']?></span>
                        </label>

                        <ul class="list-group mt-1">
                            <?php foreach ($controller['methods'] as $method): ?>
                                <li class="list-group-item">
                                    <?php if ( $this->data['role_info']['permission'] == 'all' ): ?>
                                        <input
                                                id="method-<?=$controller['name']?>-<?=$method['name']?>"
                                                class="form-check-input me-1 method-name disabled"
                                                type="checkbox"
                                                name="permission"
                                                value="all"
                                                checked
                                        >
                                    <?php else: ?>
                                        <input
                                                id="method-<?=$controller['name']?>-<?=$method['name']?>"
                                                class="form-check-input me-1 method-name"
                                                type="checkbox"
                                                name="permission[<?=$controller['name']?>][<?=$method['name']?>]"
                                                value="1"
                                            <?=isset($this->data['role_info']['permission'][$controller['name']][$method['name']])? 'checked' : ''?>
                                        >
                                    <?php endif; ?>
                                    <label for="method-<?=$controller['name']?>-<?=$method['name']?>">
                                        <?=$method['name']?> <span class="annotation-desc"><?=$method['desc']?></span>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>

            <input type="hidden" name="role_id" value="<?=$this->data['role_id']?>">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </form>
    </div>
</div>


