<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/fsa/" title="Вернуться">
                    <i class="fa-solid fa-house"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/fsa/list/" title="Журнал">
                    <i class="fa-solid fa-list"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/fsa/protocol/" title="Протоколы">
                    <i class="fa-regular fa-file-lines icon-big"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<div class="panel panel-default">
    <header class="panel-heading">
        Настройки
        <span class="tools float-end">
        <a href="#" class="fa fa-chevron-up"></a>
     </span>
    </header>
    <div class="panel-body">

        <form class="form-horizontal" method="post" action="<?=URI?>/fsa/updateSettings/">
            <div class="mb-3">
                <label class="form-label mb-1">Ключ API <span class="redStars">*</span></label>
                <input type="text" name="form[api_key]" class="form-control" value="<?=$this->data['form']['api_key'] ?? ''?>" maxlength="256" required>
            </div>

            <div class="mb-3">
                <label class="form-label mb-1">Адрес/IP отправки <span class="redStars">*</span></label>
                <input type="text" name="form[address]" class="form-control" value="<?=$this->data['form']['address'] ?? ''?>" maxlength="64" required>
            </div>

            <div class="line-dashed"></div>

            <div class="mb-3">
                <label class="form-label mb-1">Ид аккредитованного пользователя <span class="redStars">*</span></label>
                <input type="text" name="form[acc_person_user_id]" class="form-control" value="<?=$this->data['form']['acc_person_user_id'] ?? ''?>" maxlength="50" required>
            </div>

            <div class="mb-3">
                <label class="form-label mb-1">Адрес аккредитованной компании (из РАЛ) <span class="redStars">*</span></label>
                <input type="text" name="form[acc_person_address_name]" class="form-control" value="<?=htmlentities($this->data['form']['acc_person_address_name'] ?? '')?>" maxlength="2000" required>
            </div>

            <div class="mb-3">
                <label class="form-label mb-1">Ид адреса аккредитованной компании (из РАЛ) <span class="redStars">*</span></label>
                <input type="number" name="form[acc_person_address_id]" class="form-control appearance-none" value="<?=$this->data['form']['acc_person_address_id'] ?? ''?>" required>
            </div>

            <div class="line-dashed"></div>

            <div class="mb-3">
                <label class="form-label mb-1">Ид пользователя, утвердившего и подписавшего протокол (из РАЛ) <span class="redStars">*</span></label>
                <input type="number" name="form[approved_user_id]" class="form-control appearance-none" value="<?=$this->data['form']['approved_user_id'] ?? ''?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label mb-1">ФИО пользователя, утвердившего и подписавшего протокол <span class="redStars">*</span></label>
                <input type="text" name="form[approved_user_name]" class="form-control" value="<?=$this->data['form']['approved_user_name'] ?? ''?>" maxlength="255" required>
            </div>

<!--            <div class="mb-3">-->
<!--                <label class="form-label mb-1">Значение адреса аккредитованной компании</label>-->
<!--                <input type="text" name="form[acc_person_address_value]" class="form-control" value="--><?//=$this->data['form']['acc_person_address_value'] ?? ''?><!--">-->
<!--            </div>-->

            <div class="line-dashed"></div>

            <button class="btn btn-primary" type="submit">Сохранить</button>
        </form>
    </div>
</div>

