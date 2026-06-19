<? 
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php'); 
require_once ($_SERVER['DOCUMENT_ROOT'] . '/include/function.php');
$APPLICATION->SetTitle("Внутрилабораторный контроль");
?>
<?php


//Текущий месяц и год
$currentMonthYear = date('m.Y');
//$currentMonthYear = '11.2019';

//$sqlAlldataVlk = !empty($currentMonthYear) ? 'SELECT * FROM `VLK` WHERE `DATE`=' . $DB->ForSql($currentMonthYear) : '';

//$sqlAlldataVlk = 'SELECT * FROM `VLK` WHERE `DATE`=' . $DB->ForSql($currentMonthYear);
//Выбираем записи по текущий дате и которых нет ID ТЗ
$sqlAlldataVlk = 'SELECT * FROM `VLK` WHERE `TZ_ID` IS NULL';// AND `DATE`=' . $DB->ForSql($currentMonthYear);

//Получаем все данные из таблтцы `ba_tz_vlk`
//$selectAllBaTzVlk = $DB->Query("SELECT * FROM `ba_tz_vlk`");

//Находим все записи по текущему месяцу и году 
$resultAlldataVlk = !empty($sqlAlldataVlk) ? $DB->Query($sqlAlldataVlk) : '';

if (!empty($resultAlldataVlk)) {
	//Счетчик созданых заявок(сделок)
	$countСreatedRequest = 0;
	while($resultDataVlk = $resultAlldataVlk->Fetch()) {
		
		//ID поля таблицы VLK
		$vlkId = !empty($resultDataVlk['ID']) ? (int)$resultDataVlk['ID'] : '';
		
		//ID ответственного
		$userId = !empty($resultDataVlk['USER_ID']) ? (int)$resultDataVlk['USER_ID'] : '';
		
		//ID ГОСТа
		$guestId = !empty($resultDataVlk['OA_ID']) ? (int)$resultDataVlk['OA_ID'] : '';
		
		//Находим данные для этого ГОСТа
		$resultSelectBaGuest = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $guestId)->Fetch();
		
		//Цена ГОСта(методики)
		$priceresultSelectBaGuest = !empty($resultSelectBaGuest['PRICE']) ? $resultSelectBaGuest['PRICE'] : '';
		
		//Создаем масив данных для материала и его методики
		$dataArrayMaterialGuest = [
			1 => [
				'mater' => '',
				'gost' => [
								0 => $guestId
							],
				'obiem' => '1',
				'gost_new' => [
									0 => '2522'
							],
				'price' => [
								0 => $priceresultSelectBaGuest
							 ]
			]
		];
		
		$dataArrayMaterialGuest = serialize($dataArrayMaterialGuest);
	
		//Находим все нужные нам заявки(сделки) 
		//$requests = CCrmDeal::GetList(['DATE_CREATE' => 'DESC'], ['>DATE_CREATE' => '01.01.2020 00:00:00', '!TYPE_ID' => '3']);
		
		/*$numberRequests = 0;
		//Считаем кол-во заявок(сделок) 
		while($request = $requests->Fetch())
		{
			$numberRequests++;
		}
		
		//Номер заявки
		$numberRequest = $numberRequests + 1;
		
		$arrayFields = [
			"TITLE" => 'ВЛК №' . $numberRequest . getCurrentYearForDocs(), //Заголовок(номер) заявки
			"TYPE_ID" => '1', //Тип заявки ВЛК
			"ASSIGNED_BY_ID" => $userId, //ID ответственного
		];
		
		//Создаем экземпляр новой заявки(сделки)
		$newRequest = new CCrmDeal;
		
		//Создаем новую заявку и добавляем данные
		$newRequest = $newRequest->Add($arrayFields);
		
		//Обновляем стадию, если отсутствует модуль 'crm', то подключаем его
		if (CModule::IncludeModule('crm')) {
			$requestUpdate = new CCrmDeal;
			$arrayFieldsUpdateRequest = ['STAGE_ID' => 'PREPARATION'];
			$resultUpdateRequest = $requestUpdate->Update($newRequest,
				$arrayFieldsUpdateRequest,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}
		
		//находим данные созданной Заявки(сделки) по ID
		$dataNewRequest = CCrmDeal::GetByID($newRequest); 
		
		//ID Заявки(сделки)
		$idNewRequest = !empty($dataNewRequest['ID']) ? (int)$dataNewRequest['ID'] : '';*/
		
		//Имя главного ответственного
		//$assignedByName = !empty($dataNewRequest['ASSIGNED_BY_NAME']) ? $dataNewRequest['ASSIGNED_BY_NAME'] : '';
		
		//Фамилия главного ответственного
		//$assignedByLastName = !empty($dataNewRequest['ASSIGNED_BY_LAST_NAME']) ? $dataNewRequest['ASSIGNED_BY_LAST_NAME'] : '';
		
		//$assignedNameLastName = getInitials($assignedByName) ." ". $assignedByLastName;
		
		//$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос
		//Заявка(Номер заявки)
		//$titleNewRequest = !empty($dataNewRequest['TITLE']) ? $DB->ForSql($dataNewRequest['TITLE']) : '';
		
		
		//стадия
		//$stageIdNewRequest = !empty($dataNewRequest['STAGE_ID']) ? $DB->ForSql($dataNewRequest['STAGE_ID']) : '';
		$stageIdNewRequest = 'PREPARATION';
		
		//Дата (дата создания)
		//$dateCreateNewRequest = !empty($dataNewRequest['DATE_CREATE']) ? $DB->ForSql(date("d.m.Y", strtotime($dataNewRequest['DATE_CREATE']))) : '';
		$dateCreateNewRequest = date("d.m.Y");
		
		
		//Поиск полей из списка дат в журнале заявок
		//$dateCreateDatetimeNewRequest = !empty($dataNewRequest['DATE_CREATE']) ? $DB->ForSql(date("Y-m-d H:i:s", strtotime($dataNewRequest['DATE_CREATE']))) : '';
		$dateCreateDatetimeNewRequest = $DB->ForSql(date("Y-m-d H:i:s"));
		
		//Колонка "Крайний срок"
		/*$deadlineDate = ($dataNewRequest['UF_CRM_1577124965971'] ? $dataNewRequest['UF_CRM_1577124965971'] : "01.01.1970");
		if ($deadlineDate=="01.01.1970") $deadlineDate=date('d.m.Y', strtotime($dataNewRequest['DATE_CREATE'])+ 86400*3);
		$deadlineTableleNewRequest = !empty($deadlineDate) ? $DB->ForSql($deadlineDate) : '';*/
		$deadlineDate = date('d.m.Y', strtotime($dateCreateNewRequest)+ 86400*3);
		$deadlineTableleNewRequest = !empty($deadlineDate) ? $DB->ForSql($deadlineDate) : '';
		//echo'<pre>';var_dump($deadlineTableleNewRequest);echo'</pre>';die();
		
		//Лабораториии, Находим к какой лаборатории относиться главный ответсвенный
		//$userr = CUser::GetByID($dataNewRequest['ASSIGNED_BY_ID'])->Fetch();
		$userr = CUser::GetByID($userId)->Fetch();
		
		//Имя ответственного
		$assignedByName = !empty($userr['NAME']) ? $userr['NAME'] : '';
		
		//Фамилия главного ответственного
		$assignedByLastName = !empty($userr['LAST_NAME']) ? $userr['LAST_NAME'] : '';
		
		$assignedNameLastName = getInitials($assignedByName) ." ". $assignedByLastName;
		
		$idLaba = '';
		if (!empty($userr['UF_DEPARTMENT'][0])) {
			//ID лаборатории к которой относиться главный ответственный
			$idLaba = (int)$userr['UF_DEPARTMENT'][0];
		}
		
		//Тип SALE-ИЦ, COMPLEX-ОСК, 1-ВЛК, 2-МСИ, 3-Дорожная
		$typeIdNew = '1';
		
		/*$resultInsertNewTZ = $DB->Query("INSERT INTO `ba_tz` (`ID_Z`, `TZ`, `ASSIGNED`, `REQUEST_TITLE`, `STAGE_ID`, `STAGE_NUMBER`, 
		`DATE_CREATE`, `DEADLINE_TABLE`, `LABA_ID`, `DATE_CREATE_TIMESTAMP`, `TYPE_ID`) 
		VALUES ('" . $idNewRequest . "', '" . $DB->ForSql($dataArrayMaterialGuest) . "', '" . $DB->ForSql($assignedNameLastName) . "', '" . $titleNewRequest . "', 
		'" . $stageIdNewRequest . "', " . (int)0 . ", '" . $dateCreateNewRequest . "', '" . $deadlineTableleNewRequest . "', 
		'" . $idLaba . "', '" . $dateCreateDatetimeNewRequest . "', '" . $typeIdNew . "')");*/
		
		$resultInsertNewTZ = $DB->Query("INSERT INTO `ba_tz_vlk` (`TZ`, `ASSIGNED`, `STAGE_ID`, `STAGE_NUMBER`, 
		`DATE_CREATE`, `DEADLINE_TABLE`, `LABA_ID`, `DATE_CREATE_TIMESTAMP`, `TYPE_ID`) 
		VALUES ('" . $DB->ForSql($dataArrayMaterialGuest) . "', '" . $DB->ForSql($assignedNameLastName) . "', 
		'" . $stageIdNewRequest . "', " . (int)0 . ", '" . $dateCreateNewRequest . "', '" . $deadlineTableleNewRequest . "',
		'" . $idLaba . "', '" . $dateCreateDatetimeNewRequest . "', '" . $typeIdNew . "')");
		/*echo'<pre>';var_dump('$dataArrayMaterialGuest',$dataArrayMaterialGuest);echo'</pre>';
		echo'<pre>';var_dump('$assignedNameLastName',$assignedNameLastName);echo'</pre>';
		echo'<pre>';var_dump('$stageIdNewRequest',$stageIdNewRequest);echo'</pre>';
		echo'<pre>';var_dump('$dateCreateNewRequest',$dateCreateNewRequest);echo'</pre>';
		echo'<pre>';var_dump('$idLaba',$idLaba);echo'</pre>';
		echo'<pre>';var_dump('$dateCreateDatetimeNewRequest',$dateCreateDatetimeNewRequest);echo'</pre>';
		echo'<pre>';var_dump('$typeIdNew',$typeIdNew);echo'</pre>';*/
		//Находим последние добавленные данные
		$resultSelectNewTz = $DB->Query('SELECT * FROM `ba_tz_vlk` ORDER BY ID DESC LIMIT 1')->Fetch();
		
		$resuttUpdateVlk = null;
		if (!empty($resultSelectNewTz['ID'])) {
			
			//$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос
			//Заявка(Номер заявки)
			$titleNewRequest = 'ВЛК №' . $resultSelectNewTz['ID'] . getCurrentYearForDocs();
			
			//Записываем ID созданного ТЗ в таблицу VLK для выбранной записи 
			$resuttUpdateVlk = $DB->Query('UPDATE `VLK` SET `TZ_ID`=' . (int)$resultSelectNewTz['ID'] . ' WHERE `ID`=' . $vlkId);
			
			$resuttUpdateBaTzVlk = $DB->Query('UPDATE `ba_tz_vlk` SET `REQUEST_TITLE`="' . $DB->ForSql($titleNewRequest) . '" WHERE `ID`=' . (int)$resultSelectNewTz['ID']);
		}
		
		//Если заявка создана увеличиваем счетчик
		if (!empty($resuttUpdateVlk) && $resuttUpdateVlk->result) {
			$countСreatedRequest++;
		}
	}
	
	if (!empty($countСreatedRequest) && $countСreatedRequest > 1) {
		echo 'Заявки созданы!';
	} else if (!empty($countСreatedRequest) && $countСreatedRequest == 1) {
		echo 'Заявка создана!';
	}
}


