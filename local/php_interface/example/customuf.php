<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\EventManager;

/*$eventManager = EventManager::getInstance();
$eventManager->addEventHandlerCompatible('main', 'onCrmDealAdd', 'titleEdit');
$eventManager->addEventHandlerCompatible('main', 'onCrmDealUpdate', 'titleEdit');

function logFile($var, $clear = FALSE, $path = NULL)
{
    if ($var) {
        $date = '====== ' . date('Y-m-d H:i:s') . " =====\n";
        $result = $var;
        if (is_array($var) || is_object($var)) {
            $result = print_r($var, 1);
        }
        $result .= "\n";
        if (!$path) $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/mylog.txt';
        if ($clear) file_put_contents($path, '');
        @error_log($date . $result, 3, $path);
        return true;
    }
    return false;
}

function titleEdit(){
    logFile($_REQUEST);
}*/