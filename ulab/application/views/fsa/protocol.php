<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/fsa/" title="Вернуться">
                    <i class="fa-solid fa-house"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/fsa/settings/" title="Настройки">
                    <i class="fa-solid fa-gear"></i>
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

<form class="form-horizontal" method="post" action="<?=URI?>/fsa/createXMLProtocol/">
    <div class="panel panel-default">
        <header class="panel-heading">
            Протокол
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Номер протокола</label>
                <div class="col-sm-8">
                    <select class="form-control select2" name="protocol_id" required>
                        <option value=""></option>
                        <?php foreach ($this->data['protocol_list'] as $item): ?>
                            <option value="<?=$item['ID']?>" <?=$this->data['protocol_id'] == $item['ID']? 'selected' : ''?>><?=$item['name']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary">Создать XML</button>
                </div>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <header class="panel-heading">
            Созданные XML
            <span class="tools float-end">
                <a href="#" class="fa fa-chevron-up"></a>
            </span>
        </header>
        <div class="panel-body">
            <input type="hidden" class="filter" id="protocol_id" value="<?=$this->data['protocol_id']?>">
            <table id="journal_xml" class="table table-striped journal">
                <thead>
                <tr class="table-light">
                    <th scope="col">Дата</th>
                    <th scope="col">XML</th>
                    <th scope="col">Файл ЭЦП</th>
                    <th scope="col"></th>
                </tr>
<!--                <tr class="header-search">-->
<!--                    <th scope="col"></th>-->
<!--                    <th scope="col"></th>-->
<!--                    <th scope="col"></th>-->
<!--                    <th scope="col"></th>-->
<!--                </tr>-->
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</form>