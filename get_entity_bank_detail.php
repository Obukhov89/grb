<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
use Bitrix\Main\Loader;
Loader::IncludeModule("crm");

$requisiteId = $_POST['requisiteId'] ?? null; 
$requisite = [];

/*if (empty($requisiteId)) {
    return [];
}*/
if (!empty($requisiteId)) {

    $params = [
        'filter' => [
            'ENTITY_ID' => $requisiteId,
            'ENTITY_TYPE_ID' => CCrmOwnerType::Requisite
        ]
    ];

    $bankReq = (new \Bitrix\Crm\EntityBankDetail)->getList($params)->fetch();

    $requisite['RQ_ACC_NUM'] = ! empty($bankReq['RQ_ACC_NUM']) ? $bankReq['RQ_ACC_NUM'] : '';
    $requisite['RQ_COR_ACC_NUM'] = ! empty($bankReq['RQ_COR_ACC_NUM']) ? $bankReq['RQ_COR_ACC_NUM'] : '';
    $requisite['RQ_BIK'] = ! empty($bankReq['RQ_BIK']) ? $bankReq['RQ_BIK'] : '';
    $requisite['RQ_BANK_NAME'] = ! empty($bankReq['RQ_BANK_NAME']) ? $bankReq['RQ_BANK_NAME'] : '';
    $requisite['COMMENTS'] = ! empty($bankReq['COMMENTS']) ? $bankReq['COMMENTS'] : '';

    $address = Bitrix\Crm\EntityRequisite::getAddresses($requisiteId);

    $requisite["ADDRESS_1"] = ! empty($address[1]["ADDRESS_1"]) ? $address[1]["ADDRESS_1"] : '';
    $requisite["ADDRESS_2"] = ! empty($address[1]["ADDRESS_2"]) ? $address[1]["ADDRESS_2"] : '';
    $requisite["REGION"] = ! empty($address[1]["REGION"]) ? $address[1]["REGION"] : '';

    echo json_encode($requisite);

}