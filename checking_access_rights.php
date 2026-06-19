<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once 'Classes/Rights.php';

//$currentUserId = $_POST['userId'] ?? null;

$url = $_SERVER['HTTP_REFERER'] ?? '';
//$url = '';
function getNameFile($url) {
    if (empty($url)) {
        return '';
    }
    return basename(parse_url($url, PHP_URL_PATH), '.php');
}

$rights = Rights::access($USER, getNameFile($url));

if ($rights) {
    echo json_encode($rights);
}
