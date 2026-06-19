<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

//Находим все поля без номеров заявок
$result_select_history = $DB->Query("SELECT * from HISTORY WHERE REQUEST IS NULL");

while ($row_history = $result_select_history->Fetch()) {
	
	//Если тип поля равен 'Сохранение результатов испытаний', тогда находим ID ТЗ($result_select_ba_tz['ID']) и записываем в 'TZ_ID' HISTORY
	if (isset($row_history['TYPE']) && $row_history['TYPE'] == 'Сохранение результатов испытаний') {
		
		if (isset($row_history['TZ_ID']) && $row_history['TZ_ID'] != 0) {
			
			$result_select_ba_tz = $DB->Query("SELECT * from `ba_tz` WHERE `ID_Z`=" . $row_history['TZ_ID'])->Fetch();
			
			if (!empty($result_select_ba_tz['ID']) && !empty($row_history['ID'])) {
				//Сохраняет копию TZ_ID
				//$result_update_history = $DB->Query("UPDATE `HISTORY` SET `ID_TZ_TEST`='" . (int)$row_history['TZ_ID'] . "' WHERE `ID`=" . $row_history['ID']);
				//Новое значение TZ_ID
				//$result_update_history = $DB->Query("UPDATE `HISTORY` SET `TZ_ID`='" . (int)$result_select_ba_tz['ID'] . "' WHERE `ID`=" . $row_history['ID']);
			}
		}
		
		//Если 'TZ_ID' HISTORY равно 0, то записывем 0
		if (isset($row_history['TZ_ID']) && $row_history['TZ_ID'] == 0) {
			if (!empty($row_history['ID'])) {
				//Сохраняет копию TZ_ID
				//$result_update_history = $DB->Query("UPDATE `HISTORY` SET `ID_TZ_TEST`='" . (int)$row_history['TZ_ID'] . "' WHERE `ID`=" . $row_history['ID']);
				//Новое значение TZ_ID
				//$result_update_history = $DB->Query("UPDATE `HISTORY` SET `TZ_ID`='" . (int)0 . "' WHERE `ID`=" . $row_history['ID']);
			}
		}
	}
}

//Находим все поля с номерами заявок
$result_select_history2 = $DB->Query("SELECT * from HISTORY WHERE REQUEST IS NOT NULL");

while ($row_history2 = $result_select_history2->Fetch()) {
	//Находим номера заявок
	if (!empty($row_history2['REQUEST'])) { 
		if (CModule::IncludeModule('crm')) {
			$arFilter = array();
			$arFilter["%TITLE" ] = $row_history2['REQUEST'];
			
			//Находим сделки по номерам заявок
			$deals = CCrmDeal::GetListEx($arOrder = array(), $arFilter);
			
			//$deal = $deal = $deals->Fetch();
			$deal = $deals->Fetch();
			
			//Получаем ID заявки($deal['ID']) по которой находим ID ТЗ ($result_select_ba_tz2['ID']) и записываем в 'TZ_ID' HISTORY
			if (!empty($deal['ID'])) {
				$result_select_ba_tz2 = $DB->Query("SELECT * from `ba_tz` WHERE `ID_Z`=" . (int)$deal['ID'])->Fetch();
				if (!empty($result_select_ba_tz2['ID']) && !empty($row_history2['ID']) && $row_history2['TZ_ID'] != 0) { 
					//Сохраняет копию TZ_ID
					//$result_update_history = $DB->Query("UPDATE `HISTORY` SET `ID_TZ_TEST`='" . (int)$result_select_ba_tz2['ID'] . "' WHERE `ID`=" . $row_history2['ID']);
					//Новое значение TZ_ID
					$result_update_history = $DB->Query("UPDATE `HISTORY` SET `TZ_ID`='" . (int)$result_select_ba_tz2['ID'] . "' WHERE `ID`=" . $row_history2['ID']);
				}
			}
			
			//Если 'TZ_ID' HISTORY равно 0, то записывем 0
			if (isset($row_history2['TZ_ID']) && $row_history2['TZ_ID'] == 0) {
				if (!empty($row_history2['ID'])) {
					//Сохраняет копию TZ_ID
					//$result_update_history2 = $DB->Query("UPDATE `HISTORY` SET `ID_TZ_TEST`='" . (int)0 . "' WHERE `ID`=" . $row_history2['ID']);
					//Новое значение TZ_ID
					$result_update_history2 = $DB->Query("UPDATE `HISTORY` SET `TZ_ID`='" . (int)0 . "' WHERE `ID`=" . $row_history2['ID']);
				}
			}
			
		}
		
	}
}
?>