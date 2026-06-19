<?php

//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);

header("X-XSS-Protection: 1; mode=block");

session_start();

define("PROTOCOL_GENERATOR", $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/');
const PROTOCOL_PATH             = __DIR__ . '/../protocol_generator/';
const APP_PATH                  = __DIR__ . '/application/';
const URI                       = '/ulab';
const UPLOAD_DIR                = __DIR__ . '/upload';
const UPLOAD_URL                = URI . '/upload';
const PROTOCOL_GENERATOR_URL    = '/protocol_generator/';

// добросовестный клиент
const COMPANY_GOOD = 'UF_CRM_1654574670';

//
const CHECK_TZ_NOT_APPROVE = -2; // проверка тз: не утверждено
const CHECK_TZ_NOT_SENT = -1; // проверка тз: не отправлено
const CHECK_TZ_APPROVE = 1; // проверка тз: утверждено
const CHECK_TZ_WAIT = 0; // проверка тз: ожидает вердикт

require_once(APP_PATH . 'init.php');

$app = new App;
