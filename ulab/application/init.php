<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

spl_autoload_register(function ($class) {
    if ( file_exists(APP_PATH . "controllers/{$class}.php") ) {
        include APP_PATH . "controllers/{$class}.php";
    } else {
        include APP_PATH . "models/{$class}.php";
    }
});

if ( isset($_SESSION['SESS_AUTH']['CONTEXT']) ) {
    $context = json_decode($_SESSION['SESS_AUTH']['CONTEXT'], true);
    $_SESSION['SESS_AUTH']['USER_ID'] = $context['userId'];
}

if (!function_exists('array_key_first')) {
	function array_key_first(array $arr) {
		foreach($arr as $key => $unused) {
			return $key;
		}
		return NULL;
	}
}

require_once(APP_PATH . 'config/config.php');

require_once(APP_PATH . 'core/App.php');
require_once(APP_PATH . 'core/Controller.php');

require($_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php");
require_once $_SERVER['DOCUMENT_ROOT'] . "/phpqrcode/qrlib.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/protocol_generator/Morphos-master/vendor/autoload.php";
require($_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php');

require_once(APP_PATH . '/include/StringHelper.php');

require_once(APP_PATH . 'core/Registry.php');
require_once(APP_PATH . 'core/Model.php');



