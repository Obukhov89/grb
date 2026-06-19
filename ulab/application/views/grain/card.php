<?php
    $arrSize = ["200", "300"];
?>

<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-1">
                <a class="nav-link link-icon" href="<?=URI?>/grain/list/" title="Вернуться к списку">
                    <svg class="icon" width="25" height="25">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#list"/>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</header>

<form id="grain-form" action="<?=URI?>/grain/updateGrainList/<?= $this->data['grain_list_id'] ?>" method="POST">
    <div class="tab b-none mb-3">
        <input checked id="tab-btn-1" name="tab-btn" type="radio">
        <label for="tab-btn-1">Общая информация</label>
        <input id="tab-btn-2" name="tab-btn" type="radio">
        <label for="tab-btn-2">Информация о ситах</label>

        <!-- Таб 1 -->
        <div class="tab-content" id="content-1">
            <label for="grain_name" class="mt-2">Название листа в результатах испытаний</label>
            <div class="input-container">
                <input class="form-control mb-10" id="grain_name"
                       type="text" name="grain_list_name"
                       value="<?= $this->data['grain']['name'] ?>"
                >
            </div>

            <label for="grain_gost" class="mt-2">ГОСТ зернового состава</label>
            <div class="input-container">
                <select class="grain__gost_list" id="grain_gost"
                        name="grain_list_gost">
                    <option value="0" selected>Выберите ГОСТ</option>
                    <?php foreach ($this->data['grain_list_gost'] as $item): ?>
                        <option value="<?= $item['gost_id'] ?>" <?= ($item['grain_list_id'] == $this->data['grain_list_id'] ? "selected" : "") ?>>
                            <?= $item['gost'] . ' ' . $item['gost_point'] . ' | ' . $item['gost_specification'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Таб 2-3 -->
        <?php $inc = 2; ?>
        <?php foreach($this->data['grain_seave_size'] as $typeSeave): ?>
            <div class="tab-content" id="content-<?= $inc ?>">
                <div class="flex-between">
                    <?php $typeSeaveSign = 0; ?>
                    <?php foreach($typeSeave['seave_type'] as $item): ?>
                        <div>
                            <h4><?= $item['type_to_title'] ?></h4>

                            <button id="add-new-sieve" class="btn btn-success mb-2" type="button" title="Добавить новый договор">
                                Добавить сита
                            </button>

                            <table class="table table-bordered">
                                <tbody>
                                    <tr class="text-center">
                                        <td>Размер</td>
                                        <td>От</td>
                                        <td>До</td>
                                    </tr>
                                    <?php foreach($item['values'] as $v): ?>
                                        <tr class="text-center align-middle">
                                            <td>Сита <?= $v['title'] ?></td>
                                            <td>
                                                <input class="form-control" type="number" step="any"
                                                       name="grain[<?= $typeSeave['seave_main_type'] ?>][<?= $item['type_to_input'] ?>][seave_values__from][<?= $v['id']?>]"
                                                       value="<?= $this->data['grain']['data'][$typeSeave['seave_main_type']][$arrSize[$typeSeaveSign]]['seave_values__from'][$v['id']] ?>"
                                                >
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" step="any"
                                                       name="grain[<?= $typeSeave['seave_main_type'] ?>][<?= $item['type_to_input'] ?>][seave_values__to][<?= $v['id']?>]"
                                                       value="<?= $this->data['grain']['data'][$typeSeave['seave_main_type']][$arrSize[$typeSeaveSign]]['seave_values__to'][$v['id']] ?>"
                                                >
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div> 
                    <?php $typeSeaveSign++; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php $inc++; ?>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>

<form id="new_sieve" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/grain/addSieve" method="post" enctype="multipart/form-data">
    <div class="title mb-3 h-2">
        Добавление сита
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" value="<?= $this->data['grain_list_id'] ?>" name="zern_id">

    <div class="mb-3">
        <label class="form-label" for="number">Размер</label>
        <input type="number" class="form-control w-100 bg-white" id="number" name="size" step="any" required>
    </div>

    <div class="mb-3">
        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="lower">
        <label class="form-check-label" for="flexSwitchCheckDefault">Менее</label>
    </div>

    <div class="mb-3">
        <label class="form-label" for="number">Ед. измерения</label>
        <input type="text" class="form-control w-100 bg-white" id="number" name="meas" step="any" required>
    </div>

    <button type="submit" class="btn btn-primary">Создать</button>
</form>