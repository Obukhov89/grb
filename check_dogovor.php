<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
	
	if (!empty($_POST['id_z'])) {
		
		$result_select_ba_tz = $DB->Query("SELECT * FROM `ba_tz` where `ID_Z` = " . (int)$_POST['id_z'])->Fetch();
		
		$tz = !empty($result_select_ba_tz['TZ']) ? $result_select_ba_tz['TZ'] : ''; 
		
		$select_deals_to_contract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL` = " . (int)$_POST['id_z'])->Fetch();
		$id_dogovor_old = !empty($select_deals_to_contract['ID_CONTRACT']) ? $select_deals_to_contract['ID_CONTRACT'] : '';
		
		$dogovor_arr = !empty($_POST['dogovor_num']) ? explode(', ID', $_POST['dogovor_num']) : '';
		$id_dogovor_new = !empty($dogovor_arr[1]) ? (int)$dogovor_arr[1] : ''; 
		
		if (isset($id_dogovor_old) && isset($id_dogovor_new) && !empty($tz) && $id_dogovor_old != $id_dogovor_new) { 
			echo $id_dogovor_old;    
		}  
		
	}
?>