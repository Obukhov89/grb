<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

CModule::IncludeModule("crm");

if (!empty($_POST['arr_check_id'])) {
    $arr_act_vr = [];
    $arr_act_id = [];
    foreach ($_POST['arr_check_id'] as $keyDeals => $valDeals) {
        $deal_request = CCrmDeal::GetByID($valDeals);

        $ba_tz_arr = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $valDeals)->Fetch();
        $ba_tz_id = !empty($ba_tz_arr['ID']) ? $ba_tz_arr['ID'] : '';

        $res_act_vr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $ba_tz_id)->Fetch();
        //Если акты выполненых работ существует, записываем сделку в масив
        if (!empty($res_act_vr)) {
            $arr_act_vr[$keyDeals] = !empty($deal_request['TITLE']) ? $deal_request['TITLE'] : '';
            $arr_act_id[] = $valDeals;
        }
    }

    //Возвращаем заявки, где акты выполненых работ уже существуют
    $dataRequests = [];
    $dataRequests['id'] = $arr_act_id;
    $dataRequests['number'] = $arr_act_vr;

    echo json_encode($dataRequests);
}
?>