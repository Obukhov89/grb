<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/fsa/protocol/<?=$this->data['protocol_id']?>" title="Вернуться">
                    <i class="fa-solid fa-arrow-left-long"></i>
                </a>
            </li>
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
                <a class="nav-link" href="<?=URI?>/fsa/protocol/" title="Протоколы">
                    <i class="fa-regular fa-file-lines icon-big"></i>
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

        <div class="form-group row" id="info" style="display:none">
            <label class="col-sm-2 col-form-label">Информация</label>
            <div class="col-sm-8">
                <div id="info_msg" style="text-align:center;">
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
            <label class="col-sm-2 col-form-label">Файл на подпись</label>
            <div class="col-sm-8">
                <strong><?=$this->data['file_name']?></strong>
                <input type="hidden" name="protocol_id" id="protocol_id" value="<?=$this->data['protocol_xml_id']?>">
                <input type="hidden" name="url_xml" id="url_xml" value="<?=$this->data['url_xml']?>">
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

        <button class="btn btn-primary" type="button" onclick="sigFile()">Подписать файл</button>
    </div>
</div>
