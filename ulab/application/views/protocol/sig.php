<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/request/card/<?=$this->data['deal_id']?>" title="Вернуться в карточку">
                    <i class="fa-solid fa-arrow-left-long"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<div class="panel panel-default">
    <header class="panel-heading">
        Электронная цифровая подпись
        <span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
        </span>
    </header>
    <div class="panel-body">

        <div class="form-group row">
            <label class="col-sm-2 --col-form-label">Протокол</label>
            <div class="col-sm-8">
                <?=$this->data['protocol_name']?>
            </div>
            <div class="col-sm-2"></div>
        </div>

        <div class="form-group row" id="info" style="display:none">
            <label class="col-sm-2 col-form-label">Информация</label>
            <div class="col-sm-8">
                <div id="info_msg" style="text-align:center;">
                    <a href="https://cryptopro.ru/sites/default/files/products/cades/demopage/cades_bes_sample.html" target="_blank">Проверить работоспособность плагина</a>
                    <br>
                    <a href="https://docs.cryptopro.ru/cades/plugin/plugin-installation-windows" target="_blank">Инструкция установки плагина</a>
                    <br>
                    <span id="PlugInEnabledTxt">Плагин не загружен</span>
                    <br>
                    <span id="PlugInVersionTxt" lang="ru"> </span>
                    <span id="CSPVersionTxt" lang="ru"> </span>
                    <br>
                    <span id="CSPNameTxt" lang="ru"> </span>
                </div>
                <div id="boxdiv" style="display:none">
                    <span id="errorarea">
                        У вас отсутствуют личные сертификаты. Вы можете
                        <a href="#" onClick="Common_RetrieveCertificate();" style="color:#0837ff"> получить</a>
                        сертификат от тестового УЦ, предварительно установив
                        <a href="https://testca.cryptopro.ru/certsrv/certnew.cer?ReqID=CACert&Renewal=1&Enc=bin"
                           style="color:#0837ff">корневой сертификат тестового УЦ</a>
                        в доверенные.
                    </span>
                </div>
            </div>
            <div class="col-sm-2"></div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Сертификат</label>
            <div class="col-sm-8">
                <div id="item_border" name="CertListBoxToHide">
                    <select size="4" name="CertListBox" id="CertListBox" class="form-control"  readonly>
                    </select>
                </div>
            </div>
            <div class="col-sm-2"></div>
        </div>

        <div class="form-group row" id="cert_info" style="display:none">
            <label class="col-sm-2 col-form-label">Информация о сертификате</label>
            <div class="col-sm-8 pt-2">
                <p class="info_field" id="subject"></p>
                <p class="info_field" id="issuer"></p>
                <p class="info_field" id="from"></p>
                <p class="info_field" id="till"></p>
                <p class="info_field" id="provname"></p>
                <p class="info_field" id="privateKeyLink"></p>
                <p class="info_field" id="algorithm"></p>
                <p class="info_field" id="status"></p>
                <p class="info_field" id="location"></p>
            </div>
            <div class="col-sm-2"></div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 --col-form-label">Файл на подпись</label>
            <div class="col-sm-8">
                <strong><span id="place_file_name"><?=$this->data['file_name']?></span></strong>
                <input type="hidden" name="protocol_id" id="protocol_id" value="<?=$this->data['protocol_id']?>">
                <input type="hidden" name="file_name" id="file_name" value="<?=$this->data['file_name']?>">
                <input type="hidden" name="outside_lis" id="outside_lis" value="<?=$this->data['outside_lis']?>">
                <input type="hidden" name="outside_lis_path_pdf" id="outside_lis_path_pdf" value="<?=$this->data['outside_lis_path_pdf']?>">
                <input type="hidden" name="new_path_pdf" id="new_pdf_path" value="<?=$this->data['new_pdf_path']?>">
                <input type="hidden" name="user_id" id="user_id" value="<?=$this->data['user_id']?>">
                <input type="hidden" name="controller_url" id="controller_url" value="/ulab/protocol/saveSigAjax/">
                <input type="hidden" name="url_file" id="url_file" value="<?=$this->data['url_file']?>">
                <input type="hidden" name="today" id="today" value="<?=$this->data['today']?>">
            </div>
            <div class="col-sm-2"></div>
        </div>

        <div id="item_border" style="display: none">
            <div class="res_l"></div>
            <textarea id='res' class="form-control" readonly><?=$this->data['file_base64']?></textarea>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Результат</label>
            <div class="col-sm-8">
                <div id="info_msg" name="SignatureTitle"></div>
                <div id="SignatureTxtBox"></div>
            </div>
            <div class="col-sm-2"></div>
        </div>

        <div class="line-dashed"></div>

        <button id="btn_sig_file" class="btn btn-primary <?=$this->data['disable_btn']? 'disabled': ''?>" type="button">Подписать файл</button>
    </div>
</div>
