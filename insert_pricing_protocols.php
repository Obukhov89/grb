<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php');

$db = new CDB();

$queryProtocols = $db->select("SELECT *, PROTOCOLS.ID as protocol_id, ba_tz.ID as ba_tz_id  FROM `PROTOCOLS` INNER JOIN `ba_tz`
ON `PROTOCOLS`.ID_TZ = `ba_tz`.ID");

foreach ($queryProtocols as $key => $val) {
    $results = !empty($val['RESULTS']) ? unserialize($val['RESULTS']) : [];
    $protocolId = $val['protocol_id'] ?? 0;
    $sampleChecked = !empty($val['SAMPLE_CHECKED']) ? unserialize($val['SAMPLE_CHECKED']) : []; //Выбранные пробы
    $arrTz = !empty($val['TZ']) ? unserialize($val['TZ']) : '';

    $sumPricesProtocols = 0;
    foreach ($results as $keyResults => $valResults) {
        if (empty($sampleChecked[$keyResults])) {
            continue;
        }

        $keysSelectedSample = !empty($sampleChecked[$keyResults]) ? array_keys($sampleChecked[$keyResults]) : [];
        $keyCurrentSelectedSample = !empty($keysSelectedSample[0]) ? (int)$keysSelectedSample[0] : 0;

        foreach ($valResults as $kPost => $itemPost) {
            foreach ($itemPost as $keyProbe => $valProbe) {
                if (!in_array($keyProbe, array_keys($sampleChecked[$keyResults][$keyCurrentSelectedSample]))) {
                    continue;
                }

                $sumPricesProtocols += isset($arrTz[$keyResults + 1]['price'][$kPost]) ? (float)$arrTz[$keyResults + 1]['price'][$kPost] : 0;
            }
        }
    }

    if ($sumPricesProtocols && $protocolId) {
        $db->queryExec("UPDATE `PROTOCOLS` set `PROTOCOL_PRICE`={?} WHERE ID={?}", [$sumPricesProtocols, $protocolId]);
    }
    
}
