<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

defined('B_PROLOG_INCLUDED') || die;

// Основной код.
CJSCore::RegisterExt(
    'custom_stuff',
    array(
        'js' => '/local/js/custom_stuff.js',
        'lang' => '/local/lang/' . LANGUAGE_ID . '/custom_stuff.js.php',
        'css' => '/local/css/custom_stuff.css',
        'rel' => array('ajax')
    )
);

CJSCore::Init('custom_stuff');


// Запускаем что надо.

$asset = Asset::getInstance();

$asset->addString('<script>BX.ready(function () { BX.CustomStuff.selectOfSelect(); });</script>');
