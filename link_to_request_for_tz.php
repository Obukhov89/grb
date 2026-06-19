<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

//Массив данных заявок(сделок)
$arrayDataRequests = [];

//Если запрос является выбор всех заявок ИЦ
if (!empty($_POST['request']) && $_POST['request'] == 'allRequestIC') {
	//----------------//
	//ТЗ текущей заявки  
	$idTzLinkToRequest = !empty($_POST['id_tz']) ? $_POST['id_tz'] : '';
	
	$resultsCurrentSelectBaTz = null;
	if (!empty($idTzLinkToRequest)) {
		$resultsCurrentSelectBaTz = $DB->Query('SELECT * FROM `ba_tz_vlk` WHERE `ID`=' . $idTzLinkToRequest)->Fetch();
	}
	
	$resultCurrentTz = !empty($resultsCurrentSelectBaTz['TZ']) ? unserialize($resultsCurrentSelectBaTz['TZ']) : '';


	$arrayCurrentGost = null;
    //перебираем ТЗ и находим все госты у текущей заявки ВЛК
	foreach ($resultCurrentTz as $keyCurrentTz => $arrDatumCurrent) {
		
		$arrayCurrentGost = !empty($arrDatumCurrent['gost']) ? $arrDatumCurrent['gost'] : '';
	
	}
	//----------------//
	
	//Выбираем все заявки(сделки) с типом ИЦ, есть протоколы и выданы в ЛИС 
	$resultsSelectBaTz = $DB->Query('SELECT * FROM `ba_tz` WHERE `TYPE_ID`="SALE" AND `NUM_P` IS NOT NULL AND `NO_BITRIX`!=1');
	
	if (!empty($resultsSelectBaTz) && $resultsSelectBaTz->SelectedRowsCount() > 0) {
		
		//Массив данных заявок(сделок)
		//$arrayDataRequests = [];
		
		$i = 0;
		while($resultSelectBaTz = $resultsSelectBaTz->Fetch()) {
			//------//
			//ТЗ заявок 
			$resultTz = !empty($resultSelectBaTz['TZ']) ? unserialize($resultSelectBaTz['TZ']) : ''; 
	
			$arrayGost = null;
			//перебираем ТЗ и находим все госты у заявки
			foreach ($resultTz as $keyTz => $arrDatum) {
				
				$arrayGost = !empty($arrDatum['gost']) ? $arrDatum['gost'] : '';
			}
			
			//Если нет в ТЗ ГОСТа текущей заявки то пропускаем
			if (!empty($arrayCurrentGost) && !empty($arrayGost) && !array_uintersect($arrayCurrentGost, $arrayGost, "strcasecmp")) {
				continue;
			}
			
			//-------//
			
			$tzId = !empty($resultSelectBaTz['ID']) ? $resultSelectBaTz['ID'] : ''; //ID ТЗ
			$requestTitle = !empty($resultSelectBaTz['REQUEST_TITLE']) ? $resultSelectBaTz['REQUEST_TITLE'] : ''; //Наименование(номер) заявки
			$requestCompany = !empty($resultSelectBaTz['COMPANY_TITLE']) ? $resultSelectBaTz['COMPANY_TITLE'] : ''; //Наименование компании(заказчика)
			
			//-------//
			//Если отсутствует  ID ТЗ заявки то пропускаем
			if (empty($tzId)) {
				continue;
			}
			//-------//
			
			$arrayDataRequests[$i]['tz_id'] = $tzId;
			//$arrayDataRequests['allRequestIC'][$i]['request_title'] = $requestTitle;
			$arrayDataRequests[$i]['request_title'] = $requestTitle;
			$arrayDataRequests[$i]['company_title'] = $requestCompany;
			
			$i++;
		}
		$arrayDataRequests['allRequestIC'] = $arrayDataRequests;
		echo json_encode($arrayDataRequests);
		//echo'<pre>';var_dump($arrayDataRequests);echo'</pre>';
		//echo'<pre>';var_dump($arrayDataRequests['allRequestIC']);echo'</pre>';
	}
	
	
}

//Если запрос является выбранная заявока ИЦ для импорта данных в ВЛК
if (!empty($_POST['id_tz']) && !empty($_POST['request']) && $_POST['request'] == 'selectedRequestIC') {
	//Получаем данные выбранной заявки для импорта данных
	$selectBaTz = $DB->Query('SELECT * FROM `ba_tz` WHERE `ID`=' . (int)$_POST['id_tz'])->Fetch();
	$description = !empty($selectBaTz['DESCRIPTION']) ? $selectBaTz['DESCRIPTION'] : ''; //Описание объекта испытаний
	$actNum = !empty($selectBaTz['ACT_NUM']) ? $selectBaTz['ACT_NUM'] : ''; //Номер акта приемки проб
	$dateAct = !empty($selectBaTz['DATE_ACT']) ? $selectBaTz['DATE_ACT'] : ''; //Дата акта приемки проб
	
	$probeArray = !empty($selectBaTz['PROBE']) ? unserialize($selectBaTz['PROBE']) : '';
    $probePlace = !empty($probeArray[1]['mesto_data']) ? explode("; ", $probeArray[1]['mesto_data']) : '';
    $samplingLocation = !empty($probePlace[0]) ? $probePlace[0] : ''; //Место отбора проб
    $samplingDate = ((!empty($probePlace[1]) && $probePlace[1] != ' Не указана')|| (!empty($probePlace[1]) && $probePlace[1] != '-') || (!empty($probePlace[1]) && $probePlace[1] == ' -') || (!empty($probePlace[1]) && $probePlace[1] == '01.01.1970')) ? date('Y-m-d', strtotime($probePlace[1])) : ''; //Дата отбора проб
	
	
	$tzRequest = !empty($selectBaTz['TZ']) ? unserialize($selectBaTz['TZ']) : '';
	
	//Количество материалов в поле TZ таблицы ba_tz
	$countMaterialsInRequest = !empty($tzRequest) ? count($tzRequest) : '';
	
	$arrayMaterials = [];
	//Массив данных акта приемки проб
	$arrayDataAkt = [];
	
	$arrayDataAkt['description'] = $description; //Добавляем Описание объекта испытаний
	$arrayDataAkt['actNum'] = $actNum; //Добавляем Номер акта приемки проб
	$arrayDataAkt['dateAct'] = $dateAct; //Добавляем Дата акта приемки проб
	$arrayDataAkt['samplingLocation'] = $samplingLocation; //Добавляем Место отбора проб
	$arrayDataAkt['samplingDate'] = $samplingDate; //Добавляем Дата отбора проб
	//echo'<pre>';var_dump($selectBaTz);die();
	//Находим все материалы ля выбранной заявки(сделки)
	$resultMaterialsToRequest = null;
	if (!empty($selectBaTz['ID_Z'])) {
		$resultMaterialsToRequest = $DB->Query('SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=' . (int)$selectBaTz['ID_Z']);
	}

	
	//Если есть материал в таблице MATERIALS_TO_REQUESTS
	if (!empty($resultMaterialsToRequest) && $resultMaterialsToRequest->SelectedRowsCount() > 0) {
		while($resultMaterialToRequest = $resultMaterialsToRequest->Fetch()) {
			$idMaterial = !empty($resultMaterialToRequest['ID_MATERIAL']) ? ', ID ' . $resultMaterialToRequest['ID_MATERIAL'] : '';
			$nameMaterial = !empty($resultMaterialToRequest['NAME_MATERIAL']) ? $resultMaterialToRequest['NAME_MATERIAL'] : '';
			//$arrayMaterials[] = $nameMaterial . ', ID ' . $idMaterial;
			$arrayMaterials[] = $nameMaterial . $idMaterial;
			//echo'<pre>';var_dump($resultMaterialToRequest);echo'</pre>';
		}
		
	} else if (!empty($countMaterialsInRequest) && $countMaterialsInRequest > 0) { //Если отсутствует материал в таблице MATERIALS_TO_REQUESTS, проверяем в таблице ba_tz в поле TZ
		foreach($tzRequest as $keyTzArray => $valTzArray) {
			$material = !empty($valTzArray['mater']) ? $valTzArray['mater'] : '';
			
			if (!empty($material)) {
				$arrayMaterials[] = $material;
			}
		}
	} 
	
	//Если массив с матиралами не пустой возвращаем массив материалов для вывода списка материалов и выбора 
	if (!empty($arrayMaterials)) {
		$arrayDataRequests['material'] = $arrayMaterials;
		$arrayDataRequests['act'] = $arrayDataAkt;
		echo json_encode($arrayDataRequests);
		exit();
	}
	
	
	//echo'<pre>';var_dump($tzRequest);echo'</pre>';
}
?>