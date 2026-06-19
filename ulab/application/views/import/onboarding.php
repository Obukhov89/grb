<style>
    .header-menu, ul.nav {
        width: 100%;
    }
    .header-menu .nav-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="onboarding-wrapper import">
    <header class="header-requirement mb-4 pt-0">
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
                <?php if (!empty($this->data['onboarding'])): ?>
                    <li class="nav-item ms-auto">
                        <a class="btn btn-gradient popup-with-form" href="<?= URI ?>/import/onboarding/" title="Перейти к созданию нового раздела, отменив текущее редактирование" data-bs-toggle="tooltip">Добавить новый раздел</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item <?=(!empty($this->data['onboarding'])) ? 'ms-3' : 'ms-auto'?>">
                    <div class="col">
                        <button form="submit_form" class="btn btn-gradient" id="submit_btn" type="submit" name="save" data-bs-toggle="tooltip"
                            title="<?= (!empty($this->data['onboarding'])) ? "Сохранить изменения текущего раздела" : "Создать новый раздел с ведёнными данными"?>">
                            <?= (!empty($this->data['onboarding'])) ? "Сохранить" : "Сохранить"?>
                        </button>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <?php if ($this->data['is_may_change']): ?>
        <form class="form-horizontal" method="post" id="submit_form" action="<?= URI ?>/import/insertUpdateOnboarding/">
            <div class="panel panel-default">
                <header class="panel-heading">
                    <?= $this->data['name'] ?>
                    <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
                </header>
                <div class="panel-body">
                    <input id="onboardingId" type="hidden" name="id"
                           value="<?= $this->data['onboarding']['id'] ?? '' ?>">

                    <div class="form-group row">
                        <div class="col">
                            <label for="title" class="col-form-label">Наименование раздела <span
                                        class="redStars">*</span></label>
                            <input type="text" name="form[title]" class="form-control clearable" id="title" placeholder="Введите наименование раздела"
                                   value="<?= $this->data['onboarding']['title'] ?? '' ?>" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col">
                            <label for="linkVideo" class="col-form-label">Ссылка на видео </label>
                            <input type="text" name="form[link_video]" class="form-control clearable" id="linkVideo" placeholder="Введите ссылку на видео"
                                   value="<?= $this->data['onboarding']['link_video'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col">
                            <label for="description" class="col-sm-2 col-form-label">Описание</label>
                            <textarea class="form-control" id="description" name="form[description]" placeholder="Введите описание"
                                      rows="10"><?= $this->data['onboarding']['description'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>

    <div class="panel panel-default">
        <header class="panel-heading">
            Разделы
            <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
        </header>
        <div class="panel-body">
            <div class="accordion" id="accordionExample">
                <?php foreach ($this->data['onboardings'] as $key => $val): ?>
                    <div class="row mb-2">
                        <div class="col">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $key ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse<?= $key ?>" aria-expanded="false"
                                            aria-controls="collapse<?= $key ?>">
                                        <?= $val['title'] ?>
                                    </button>

                                </h2>
                                <div id="collapse<?= $key ?>" class="accordion-collapse collapse"
                                     style="flex-flow: column; justify-content: space-between;"
                                     aria-labelledby="heading<?= $key ?>"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col">
                                                <iframe width="560" height="315" src="<?= $val['link_video'] ?>"
                                                        frameborder="0"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                        allowfullscreen></iframe>

                                                <!--<iframe width="720" height="405" src="<?/*= $val['link_video'] */?>"
                                                        frameBorder="0"
                                                        allow="clipboard-write; autoplay" webkitAllowFullScreen
                                                        mozallowfullscreen allowFullScreen></iframe>-->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <?= $val['description'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($this->data['is_may_change']): ?>
                            <div class="col-auto d-flex" style="align-items: center">
                                <a href="<?= URI ?>/import/onboarding/<?= $val['id'] ?>"
                                   class="btn btn-primary btn-square me-3"
                                   title="Редактировать раздел" data-bs-toggle="tooltip">
                                    <i class="fa-solid fa-pencil icon-fix"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-square onboarding-del" data-id="<?= $val['id'] ?>" title="Удалить" data-bs-toggle="tooltip">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>