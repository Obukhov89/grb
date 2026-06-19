<?php
defined('B_PROLOG_INCLUDED') || die;

require(__DIR__ . '/example/customuf.php');
require(__DIR__ . '/example/js.php');

function secondmentChange()
{
    global $DB;

    $arFields = ["stage" => "'В командировке'"];

    $DB->Update("secondment", $arFields, "WHERE stage = 'Согласована' AND date_begin <= '". date("Y-m-d") . "'");

    return "secondmentChange();";
}

