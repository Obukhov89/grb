<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
	
	$resultSelectBaTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `DATE_P_CORR` > '2020-12-31'");
	
	$countSelectedRows = $resultSelectBaTz->SelectedRowsCount();
	
	if (!empty($countSelectedRows)) {
		while($resultBaTz = $resultSelectBaTz->Fetch()) {
			$idTz = !empty($resultBaTz['ID']) ? $resultBaTz['ID'] : '';
			$arrayNumPTable = !empty($resultBaTz['NUM_P_TABLE']) ? explode('/', $resultBaTz['NUM_P_TABLE']) : '';
			
			$yearPTable = !empty($arrayNumPTable[1]) ? $arrayNumPTable[1] : '';
			
			$numPTable = !empty($arrayNumPTable[0]) ? $arrayNumPTable[0] : '';
			
			if (!empty($yearPTable) && strlen($yearPTable) == 4) {
				$yearPTableNewFormat = substr($yearPTable, 2);
				
				$updateNumPTable = $DB->Query("UPDATE `ba_tz` SET `NUM_P_TABLE`='" . $numPTable . "/" . $yearPTableNewFormat . "' WHERE `ID`=" . $idTz);
			}
			
		}
	}
?>