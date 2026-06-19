<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php');
include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";

$idZ = !empty($_POST['id_z']) ? (int)$_POST['id_z'] : 0;

$db = new CDB();

if (!empty($idZ)) {
	
	$currentProtocolId = !empty($_POST['current_protocol_id']) ? (int)$_POST['current_protocol_id'] : 0; //ID выбранного протокола 
	$check = !empty($_POST['check']) ? $_POST['check'] : '';
	
	$quantitySamplesWithoutNumber = !empty($_POST['quantity_samples_without_number']) ? (int)$_POST['quantity_samples_without_number'] : 0;
	
	$arraySelectedSampleData = !empty($_POST['array_selected_sample_data']) ? $_POST['array_selected_sample_data'] : [];
	
	$nonconformityCounter = 0;
	
	$formattedArraySelectedSampleData = [];
	
	if (!empty($arraySelectedSampleData)) {
		foreach ($arraySelectedSampleData as $keySelectedSampleData => $valSelectedSampleData) {
			$firstKey = isset($valSelectedSampleData[0]) ? (int)$valSelectedSampleData[0] : null;
			$secondKey = isset($valSelectedSampleData[1]) ? (int)$valSelectedSampleData[1] : null;
			$thirdKey = isset($valSelectedSampleData[2]) ? (int)$valSelectedSampleData[2] : null;
			$fourthKey = isset($valSelectedSampleData[3]) ? (int)$valSelectedSampleData[3] : null;
			
			if (isset($firstKey) && isset($secondKey) && isset($thirdKey) && isset($thirdKey)) {
				$formattedArraySelectedSampleData[$firstKey][$secondKey][$thirdKey][$fourthKey] = 'on';
			}
		}
	}
	
	$сurrentDataProtocol = null;
    if (!empty($currentProtocolId)) {
        $queryCurrentDataProtocol = "SELECT * FROM `PROTOCOLS` WHERE ID={?}";
        $arrDataProtocol = [$currentProtocolId];
        $сurrentDataProtocol = $db->selectRow($queryCurrentDataProtocol, $arrDataProtocol);
    }
	
	
	$currentProtocolNumber = !empty($сurrentDataProtocol['NUMBER']) ? (int)$сurrentDataProtocol['NUMBER'] : null;
	
	$currentProtocolActualVersion = !empty($сurrentDataProtocol['ACTUAL_VERSION']) ? $сurrentDataProtocol['ACTUAL_VERSION'] : '';
	
	$currentProtocolDate = !empty($сurrentDataProtocol['DATE']) ? dateObjectFormat($сurrentDataProtocol['DATE'], 'Y-m-d') : null;
	
	$sampleChecked = !empty($сurrentDataProtocol['SAMPLE_CHECKED']) ? unserialize($сurrentDataProtocol['SAMPLE_CHECKED']) : [];
   
	$dateBeginProtocol = !empty($сurrentDataProtocol['DATE_BEGIN']) ? $сurrentDataProtocol['DATE_BEGIN'] : '';
	$dateEndProtocol = !empty($сurrentDataProtocol['DATE_END']) ? $сurrentDataProtocol['DATE_END'] : '';

	$quantitySampleChecked = 0;
	
	
	if (!empty($check) && $check === 'formation') {
		echo $currentProtocolActualVersion;
	}
	
	
	if (!empty($check) && $check === 'count') {
		
		
		
		
		foreach ($sampleChecked as $keyMaterial => $valMaterial) {
			
			if (empty($formattedArraySelectedSampleData[$keyMaterial])) {
				$nonconformityCounter++;
			}
			foreach ($valMaterial as $keyGost => $valGost) {
				
				if (empty($formattedArraySelectedSampleData[$keyMaterial][$keyGost])) {
					$nonconformityCounter++;
				}
				foreach ($valGost as $keySample => $valSample) {
					
					if (empty($formattedArraySelectedSampleData[$keyMaterial][$keyGost][$keySample])) {
						$nonconformityCounter++; 
					}
					foreach ($valSample as $key => $val) {
						
						
						$quantitySampleChecked++;
						
						if (empty($formattedArraySelectedSampleData[$keyMaterial][$keyGost][$keySample][$key])) {
							$nonconformityCounter++;
						}
					}
				}
			}
		}
		
		
		if ($quantitySamplesWithoutNumber !== $quantitySampleChecked) {
			$nonconformityCounter++;
		}
		
		echo $nonconformityCounter++;
	}

//    if (!empty($check) && $check === 'dateProbe') {
//
//        $arrayModalText = [];
//
//        if (empty($dateBeginProtocol)) {
//            $arrayModalText[] = 'Не заполнена дата начала испытаний';
//        }
//
//
//        if (empty($dateEndProtocol)) {
//            $arrayModalText[] = 'Не заполнена дата окончания испытаний';
//        }
//
//        if (!empty($arrayModalText)) {
//            $arrayModalText[] = 'заполните данные и сохраните изменения.';
//        }
//
//        $modalText = implode(', ', $arrayModalText);
//
//        echo $modalText;
//
//    }
	
}
?>
