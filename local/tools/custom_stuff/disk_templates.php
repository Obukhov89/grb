<?php
define('PUBLIC_AJAX_MODE', true);
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

check_bitrix_sessid() || die;

$APPLICATION->ShowAjaxHead();
?>

<div class="disk-templates-title">
    Или создайте из шаблона:
</div>

<div class="disk-templates">
    <a class="template" onclick="BX.CustomStuff.helloWorld();">
        <span class="bx-file-icon-container-small bx-disk-file-icon icon-doc"></span>
        Шаблон документа 1
    </a>
    <a class="template">
        <span class="bx-file-icon-container-small bx-disk-file-icon icon-doc"></span>
        Пример шаблона
    </a>
    <a class="template">
        <span class="bx-file-icon-container-small bx-disk-file-icon icon-spr"></span>
        Еще шаблон
    </a>
    <a class="template">
        <span class="bx-file-icon-container-small bx-disk-file-icon icon-doc"></span>
        Шаблон документа 1
    </a>
    <a class="template">
        <span class="bx-file-icon-container-small bx-disk-file-icon icon-doc"></span>
        Пример шаблона
    </a>
    <a class="template">
        <span class="bx-file-icon-container-small bx-disk-file-icon icon-spr"></span>
        Еще шаблон
    </a>
    <a class="template">
        <span class="bx-file-icon-container-small bx-disk-file-icon icon-doc"></span>
        Шаблон документа 1
    </a>
    <a class="template">
        <span class="bx-file-icon-container-small bx-disk-file-icon icon-doc"></span>
        Пример шаблона
    </a>
    <a class="template">
        <span class="bx-file-icon-container-small bx-disk-file-icon icon-spr"></span>
        Еще шаблон
    </a>
</div>

<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
