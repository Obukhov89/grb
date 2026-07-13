<?php

/**
 * Получить переменную окружения с дефолтом
 */
function env($key, $default = null)
{
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return $value;
}

// множитель НДС (price * NDS_MULTIPLIER == НДС)
// 5% = (5 / 105) = 0.04761904761
// 10% = (10 / 110) = 0.0909090909
define("NDS_MULTIPLIER", (float) env('NDS_MULTIPLIER', 0.05));
define("IN_NDS_MULTIPLIER", (float) env('IN_NDS_MULTIPLIER', 0.04761904761));


define("TYPE_DEAL_NK", env('TYPE_DEAL_NK', '4')); // Тип заявки НК

define("LFHI", (int) env('LFHI', 1)); // Лаборатория физико-химических испытаний

define("ADMIN_PERMISSION", (int)env('ADMIN_PERMISSION', 2)); // id роли "Админ"
define("HEAD_LAB_PERMISSION", (int)env('HEAD_LAB_PERMISSION', 4)); // id роли "Руководитель лаборатории"
define("HEAD_IC_PERMISSION", (int)env('HEAD_IC_PERMISSION', 3)); // id роли "Руководитель ИЦ"
define("LAB_PERMISSION", (int)env('LAB_PERMISSION', 6)); // id роли "Лаборант"
define("SMK_PERMISSION", (int)env('SMK_PERMISSION', 9)); // id роли "СМК"

define("PROTOCOL_TYPE_STANDARD", (int)env('PROTOCOL_TYPE_STANDARD', 0)); // Тип протокола "Стандартный"

define('USER_ADMIN', array_map('intval', explode(',', env('USER_ADMIN', '61,1,7'))));

define("DEAL_START_NEW_AREA", (int)env('DEAL_START_NEW_AREA', 0)); // id сделки старта начала новой области
define("DEAL_START_STOP_TESTS", (int)env('DEAL_START_STOP_TESTS', 0)); // id сделки старта начала использования кнопок "Начать" и "Завершить" испытание
define("DEAL_NEW_RESULT", (int)env('DEAL_NEW_RESULT', 0)); // id сделки старта обновлённой версии результатов

define("DEAL_NEW_TZ", (int)env('DEAL_NEW_TZ', 0)); // id сделки старта новой версии ТЗ
define("DEAL_NEW_NK", (int)env('DEAL_NEW_NK', 12947)); // id сделки старта новой версии НК
define("DEAL_NEW_STATUS", (int)env('DEAL_NEW_STATUS', 0)); // id сделки старта новой версии статусов


// приёмка user_id 62 83
// группа ид 23
define("GROUP_MANAGER_ID", (int)env('GROUP_MANAGER_ID', 23));

define('HOLIDAYS', array_map('trim', explode(',', env('HOLIDAYS'))));

define('EXCEPTION_PROTOCOLS', array_map('intval', explode(',', env('EXCEPTION_PROTOCOLS'))));
