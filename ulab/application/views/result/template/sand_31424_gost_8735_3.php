<?php
/**
 * Лист измерения для журнала Песок ГОСТ 31424
 * Определение зернового состава и модуля крупности ГОСТ 8735-88 п.3
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/production_laboratory/osk/view/template/template_header.php');

$APPLICATION->AddHeadScript('/production_laboratory/assets/js/measuring-sheet-sand-31424.js');
$APPLICATION->SetTitle('Определение зернового состава и модуля крупности ГОСТ 8735-88 п.3');

$gost = 'sand_31424_gost_8735_3';
$tableName = 'PL_SAND_GOST_8735_FINENESS_MODULE';
$jurnal = 'jurnal_sand_31424.php';
$tzId = (int)$_GET['ID_TZ'];

$messageError = '';
$messageSuccess = '';

if (empty($tzId)) {
    redirect("/production_laboratory/osk/breakstone/{$jurnal}");
}

$tz = getTzByTzId($tzId);

if ( empty($tz) ) {
    //showErrorMessage("Технического задания с ИД {$tzId} не существует");
    //exit;
    redirect("/production_laboratory/osk/breakstone/{$jurnal}");
}

function insertUpdate($tzId, $tableName)
{
    global $DB;

    $_SESSION['post_data'] = $_POST;

    if (empty($tzId)) {
        return [
            'error' => "Не удалось сохранить данные листа измерения, отсутствует ID заявки",
        ];
    }

    $totalMass = $_POST['total_mass'] ?? '';
    $massOnSieve = $_POST['mass_on_sieve'] ?? '';
    $finenessModule = $_POST['fineness_module'] ?? '';
    $privateRemainder = $_POST['private_remainder'] ?? [];
    $privateRemainderByMass = $_POST['private_remainder_by_mass'] ?? [];
    $totalRemainderByMass = $_POST['total_remainder_by_mass'] ?? [];


    $arrPrivateRemainder = getDataWithChangedKeyToSave($privateRemainder, 'private_remainder');
    $arrPrivateRemainderByMass = getDataWithChangedKeyToSave($privateRemainderByMass, 'private_remainder_by_mass');
    $arrTotalRemainderByMass = getDataWithChangedKeyToSave($totalRemainderByMass, 'total_remainder_by_mass');

    $data = [
        'tz_id' => $tzId,
        'total_mass' => $totalMass,
        'mass_on_sieve' => $massOnSieve,
        'fineness_module' => $finenessModule
    ];

    $data = array_merge($data, $arrPrivateRemainder, $arrPrivateRemainderByMass, $arrTotalRemainderByMass);
    
    $tableDataByTzId = getTableDataByTzId($tzId, $tableName);

    if ( empty($tableDataByTzId) ) {
        $result = insertDataIntoTable($tableName, $data);
    } else {
        $result = updateDataInTableByTzId($tzId, $tableName, $data);
    }

    if (empty($result)) {
        return [
            'error' => "Не удалось сохранить данные листа измерения",
        ];
    }

    return [
        'success' => true
    ];
}

if (!empty($_POST) && isset($_POST['fineness_modulus_save'])) {
    $successMsg = 'Лист изменения успешно сохранен';

    $response = insertUpdate($tzId, $tableName);

    if (!empty($response['error'])) {
        showErrorMessage($response['error']);
    }

    if (!empty($response['success'])) {
        unset($_SESSION['post_data']);
        showSuccessMessage($successMsg);
    }
}

if (!empty($_GET['ID_TZ'])) {
    $plSand8735FinenessModule = getTableDataByTzId($tzId, $tableName);

    if (isset($_SESSION['post_data'])) {
        $plSand8735FinenessModule['total_mass'] = $_SESSION['post_data']['total_mass'] ?? '';
        $plSand8735FinenessModule['mass_on_sieve'] = $_SESSION['post_data']['mass_on_sieve'] ?? '';
        $plSand8735FinenessModule['fineness_module'] = $_SESSION['post_data']['fineness_module'] ?? '';
        $privateRemainder = $_SESSION['post_data']['private_remainder'] ?? [];
        $privateRemainderByMass = $_SESSION['post_data']['private_remainder_by_mass'] ?? [];
        $totalRemainderByMass = $_SESSION['post_data']['total_remainder_by_mass'] ?? [];

        $plSand8735FinenessModule = getDataWithChangedKeyToShow($privateRemainder, 'private_remainder', $plSand8735FinenessModule);
        $plSand8735FinenessModule = getDataWithChangedKeyToShow($privateRemainderByMass, 'private_remainder_by_mass', $plSand8735FinenessModule);
        $plSand8735FinenessModule = getDataWithChangedKeyToShow($totalRemainderByMass, 'total_remainder_by_mass', $plSand8735FinenessModule);

        unset($_SESSION['post_data']);
    }

    $plSand8735FinenessModule = changeNumberFormat($plSand8735FinenessModule);
}

if ( isset($_SESSION['message_danger']) ) {
    $messageError = $_SESSION['message_danger'];
    unset($_SESSION['message_danger']);
}

if ( isset($_SESSION['message_success']) ) {
    $messageSuccess = $_SESSION['message_success'];
    unset($_SESSION['message_success']);
}


require_once($_SERVER['DOCUMENT_ROOT'] . '/production_laboratory/osk/view/template/template_view.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>
