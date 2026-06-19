<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/include/function.php");

$db = new CDB();

$idTZ = 5502;

//echo'<pre>';var_dump('$idTZ', $idTZ);echo'</pre>';

$resultProtocol = null;
if (!empty($idTZ)) {
	
	//echo'<pre>';var_dump('$idTZ2222', $idTZ);echo'</pre>';

	$resultProtocol = $db->select("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}", [$idTZ]);
}
 
//echo'<pre>';var_dump('$resultProtocol', $resultProtocol);echo'</pre>';

$countResultProtocol = !empty($resultProtocol) ? count($resultProtocol) : 0;

//echo'<pre>';var_dump('$countResultProtocol', $countResultProtocol);echo'</pre>';

$matchAll = [];
$sredAll = [];

if (!empty($countResultProtocol)) {
	
	foreach($resultProtocol as $keyProtocol => $valProtocol) {
		$match = !empty($valProtocol['MATCH_MANUAL']) ? unserialize($valProtocol['MATCH_MANUAL']) : '';
		$sr = !empty($valProtocol['SRED']) ? unserialize($valProtocol['SRED']) : '';
		
		echo'<pre>';var_dump('ID', $valProtocol['ID']);echo'</pre>';
		echo'<pre>';var_dump('$match', $match);echo'</pre>';
		echo'<pre>';var_dump('$sr', $sr);echo'</pre>';
		echo'================================================================';
		
		foreach ($match as $keyMaterial => $dataMaterial) {
			foreach ($dataMaterial as $keyGost => $dataGost) {
				foreach ($dataGost as $keySample => $dataSample) {
					$matchAll[$keyMaterial][$keyGost][$keySample] = $dataSample;
					$sredAll[$keyMaterial][$keyGost][$keySample] = isset($sr[$keyMaterial][$keyGost][$keySample]) ?
						$sr[$keyMaterial][$keyGost][$keySample] : null;
				}
			}
		}
	
	}

    /*foreach ($matchManualBaTz as $keyMaterial => $dataMaterial) {
        foreach ($dataMaterial as $keyGost => $dataGost) {
            foreach ($dataGost as $keySample => $dataSample) {
                $matchAll[$keyMaterial][$keyGost][$keySample] = $dataSample;
                $sredAll[$keyMaterial][$keyGost][$keySample] = isset($sredBaTz[$keyMaterial][$keyGost][$keySample]) ?
                    $sr[$keyMaterial][$keyGost][$keySample] : null;
            }
        }
    }*/
	
}


echo'<pre>';var_dump('$matchAll', $matchAll);echo'</pre>';
echo'<pre>';var_dump('$sredAll', $sredAll);echo'</pre>';

    $matchAll = !empty($matchAll) ? serialize($matchAll) : '';
    $sredAll = !empty($sredAll) ? serialize($sredAll) : '';
	
	if (!empty($idTZ)) {
		$updateBaTzData = $DB->Query("UPDATE `ba_tz` SET `MATCH_MANUAL`='" . $matchAll . "', `SRED`='" . $sredAll . "' WHERE ID = " . (int)$idTZ);
	}
	
	
	$resultBaTz = null;
	if (!empty($idTZ)) {
		
		echo'<pre>';var_dump('$id3333', $idTZ);echo'</pre>';

		$resultBaTz = $db->selectRow("SELECT * FROM `ba_tz` WHERE `ID`={?}", [$idTZ]);
	}
	
//echo'<pre>';var_dump('$resultBaTz', $resultBaTz);echo'</pre>';

		$matchBaTz = !empty($resultBaTz['MATCH_MANUAL']) ? unserialize($resultBaTz['MATCH_MANUAL']) : '';
		$srBaTz = !empty($resultBaTz['SRED']) ? unserialize($resultBaTz['SRED']) : '';
		
	echo'<pre>';var_dump('$matchBaTz', $matchBaTz);echo'</pre>';
	echo'<pre>';var_dump('$srBaTz', $srBaTz);echo'</pre>';
	

?>