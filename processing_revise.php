<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

//Подколючаем модуль crm
CModule::IncludeModule('crm');

$selectDataReviseTable = $DB->Query("SELECT ID FROM `REVISE_TABLE`");

//количество выбранных записей при запросе из таблицы `REVISE_TABLE`
$totalDataReviseTable = $selectDataReviseTable->SelectedRowsCount();

//Если в таблице `REVISE_TABLE` нет данных($totalDataReviseTable), формируем данные и записываем в таблицу `REVISE_TABLE`
if (empty($totalDataReviseTable)) {

	//Получаем данные по договору, для дальнейшего формирования данных для таблицы `REVISE_TABLE`, по которой будет формироваться Журнал сверки 
	$queryRecordsAll = $DB->Query("SELECT * FROM `DOGOVOR`");

	//Делает выборку значений полей(строк) в массив
	while ($rowQueryRecords = $queryRecordsAll->Fetch()) {
		$dealList= []; //список ID всех сделок, привязанных к договору
		
		$idDogovor = !empty($rowQueryRecords['ID']) ? (int)$rowQueryRecords['ID'] : 0; //ID договора
		$idCompany = !empty($rowQueryRecords['CLIENT_ID']) ? (int)$rowQueryRecords['CLIENT_ID'] : 0; //ID клиента(компании)
		
		//Находим связь договора и сделок по ID договора
		$resultDealList = null;
		if (!empty($idDogovor)) {
			$resultDealList = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_CONTRACT`=" . (int)$idDogovor);
		}
		
		while($deal = $resultDealList->Fetch())
		{
			$dealList[] = $deal['ID_DEAL'];
		}
		rsort($dealList);
		
		//Сумма по всем заявкам в договоре
		$sumAllPriceDogovor = 0;
		
		//Сумма неоплаченных заявок
		$sumAllNoPaymentDogovor = 0;
		
		//Всего заявок в договоре
		//$countRequest = !empty($dealList) ? count($dealList) : 0;
		$countRequest = 0;
		
		//Всего неоплаченных заявок
		$countRequestNoPayment = 0;
		
		if (!empty($dealList)) {
			foreach ($dealList as $keyDealList => $idDeal) {
				if (!empty($idDeal)) {
					//Находим данные ТЗ где есть номер протокола
					$resultSelectTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `NUM_P` !='номер не присвоен' AND `NUM_P` IS NOT NULL AND `ID_Z`=" . (int)$idDeal)->Fetch();
					
					//Если нет такого ТЗ или номер протокола не присвоен, то пропускаем этерацию, не производим подсчет 
					if (empty($resultSelectTz)) {
						continue;
					}
					
					//Находим стоимость всех работ для договора
					$sumAllPriceDogovor += !empty($resultSelectTz['PRICE']) ? $resultSelectTz['PRICE'] : 0;
					
					//Находим сумму неоплаченных заявок($sumAllNoPaymentDogovor)
					if (isset($resultSelectTz['OPLATA']) && $resultSelectTz['OPLATA'] == null || $resultSelectTz['OPLATA'] == 0) {
						$sumAllNoPaymentDogovor += !empty($resultSelectTz['PRICE']) ? $resultSelectTz['PRICE'] : 0;
						//увеличиваем кол-во неоплаченных заявок
						$countRequestNoPayment++;
					}
					
					//Увеличиваем счетчик кол-ва заявок в договоре
					$countRequest++;
				}
			}
		}
		
		//Получаем данные по компании
		$company = $idCompany ? CCrmCompany::GetByID($idCompany) : ''; //Данные компании

		//if (CModule::IncludeModule('crm')) {
		//    //Находим сделку по ID
		//    $deal = CCrmDeal::GetByID(!empty($row['ID_Z']) ? $row['ID_Z'] : '');
		//}
		
		//Договор (номер договора)
		$number = !empty($rowQueryRecords['NUMBER']) ? $rowQueryRecords['NUMBER'] : '';
		
		//Контрагент (клиент,компания)
		$companyTitle = !empty($company['TITLE']) ? $company['TITLE'] : ''; //Наименование компании
		
		//$DB->Query("INSERT INTO `REVISE_TABLE` (`NUMBER`, `COMPANY`, `SUM_ALL_PRICE`, `SUM_NO_PAYMENT`, `COUNT_REQUEST`, `COUNT_REQUEST_NO_PAYMENT`) VALUES ('" . $number . "', '" . $company . "', " . $sumAllPriceDogovor . ", " . $sumAllNoPaymentDogovor . ", " . $countRequest . ", " . $countRequestNoPayment . ")");
		//Если есть заявки с формированным номером протокола, то записываем данные в таблицу
		if (!empty($countRequest)) {
			$DB->Query("INSERT INTO `REVISE_TABLE` (`ID_CONTRACT`,`NUMBER`, `COMPANY`, `SUM_ALL_PRICE`, `SUM_NO_PAYMENT`, `COUNT_REQUEST`, `COUNT_REQUEST_NO_PAYMENT`) VALUES (" . $idDogovor . ", '" . $number . "', '" . $companyTitle . "', " . $sumAllPriceDogovor . ", " . $sumAllNoPaymentDogovor . ", " . $countRequest . ", " . $countRequestNoPayment . ")");
		}
		
		$i++;
	}
}

$params = $columns = $totalRecords = $data = [];

//Переменные HTTP-запроса, (который по умолчанию содержит данные переменных $_GET, $_POST и $_COOKIE) переданные из DataTable
$params = !empty($_REQUEST) ? $_REQUEST : '';

//Наименование полей из базы данных таблицы для сортировки данных по ним
$columns = [
    0 => 'NUMBER', //Договор (номер договора)
    1 => 'COMPANY', //Контрагент (клиент,компания)
    2 => 'SUM_ALL_PRICE', //Сумма по всем заявкам в договоре
    3 => 'SUM_NO_PAYMENT', //Сумма неоплаченных заявок
    4 => 'COUNT_REQUEST', //Всего заявок в договоре
    5 => 'COUNT_REQUEST_NO_PAYMENT', //Всего заявок в договоре не оплаченных
];

$whereСondition = $sqlTot = $sqlRec = "";

$whereСondition = " WHERE 1";

//Производим поиск в базе данных по отдельным столбцам(полям)
if (!empty($params['columns'])) {
	//Перебираем все поля и смотрим есть ли у них данные для поиска($val_param['search']['value'])
    foreach ($params['columns'] as $key_param => $val_param) {

		if (!empty($val_param['search']['value']) && !empty($val_param['data'])) {
				$whereСondition .= " AND " . $val_param['data'] . " LIKE '%" . $val_param['search']['value'] . "%' ";
		}
    }
}

//Производим поиск в базе данных по общиму поиску.Если были переданы(введены) в поле поиска символы, конкотенируем строку где ищем по этим символам
if (!empty($params['search']['value'])) {
    $whereСondition .= " AND (NUMBER LIKE '%" . $params['search']['value'] . "%' "; //Договор (номер договора)
    //$whereСondition .= " OR COUNT_REQUEST_NO_PAYMENT LIKE '%" . $params['search']['value'] . "%' "; //Всего заявок в договоре не оплаченных
    //$whereСondition .= " OR SUM_ALL_PRICE LIKE '%" . $params['search']['value'] . "%' "; //Сумма по всем заявкам в договоре
	//$whereСondition .= " OR SUM_NO_PAYMENT LIKE '%" . $params['search']['value'] . "%' "; //Сумма неоплаченных заявок
    //$whereСondition .= " OR COUNT_REQUEST LIKE '%" . $params['search']['value'] . "%' "; //Всего заявок в договоре
    $whereСondition .= " OR COMPANY LIKE '%" . $params['search']['value'] . "%') "; //Контрагент (клиент,компания)
}

//Запрос
if ($columns[$params['order'][0]['column']] == 'NUMBER') { //Если сортировка производиться по полю договор, то обрезаем номер, преобразуем в число и производим сортировку по этим номерам 
    $sqlQuery = " SELECT *,CONVERT(substring_index(NUMBER, '/', 1 ),UNSIGNED INTEGER) AS number FROM `REVISE_TABLE` ";
	$columnName = 'number'; //Наиманование поля по которуму будет производиться сортировка, если сортировка производиться по "Договору"
} else { //Запрос для сортировки по остальным полям
    $sqlQuery = " SELECT * FROM `REVISE_TABLE` ";
	$columnName = $columns[$params['order'][0]['column']]; //Наиманование поля по которуму будет производиться сортировка, для остальных полей
}

$sqlTot .= $sqlQuery;
$sqlRec .= $sqlQuery;

if (isset($whereСondition) && $whereСondition != '') {
    $sqlTot .= $whereСondition;
    $sqlRec .= $whereСondition;
}

/*
 Сортируем выборку
 $params['order'][0]['column'] - Смотрим по какому полю у нас установлена сортировка в DataTable: "order": [[ 2, "asc" ]],
$params['order'][0]['dir'] - Смотрим сортировка по убыванию или возростанию,
$params['start'] - ограничение количества результатов(с какого начинаем, Пример: c 0),
$params['length'] - ограничение количества результатов(каким заканчиваем, Пример: до 50) в DataTable "iDisplayLength": 50,
  */

//Если $params['length'] == "-1" то показываеть все записи, иначе ограниченно кол-во записей с установленным лимитом(LIMIT)
/*if (!empty($params['length']) && $params['length'] == "-1") {
    $sqlRec .= " ORDER BY " . $columnName . " " . $params['order'][0]['dir'] . " ";
} else {
    $sqlRec .= " ORDER BY " . $columnName . " " . $params['order'][0]['dir'] . " LIMIT " . $params['start'] . " ," . $params['length'] . " ";
}*/

//Сортируем по полю, выводим все записи(не ограничиваем)
$sqlRec .= " ORDER BY " . $columnName . " " . $params['order'][0]['dir'] . " ";

$queryTot = $DB->Query($sqlTot);

//количество выбранных записей при запросе
$totalRecords = $queryTot->SelectedRowsCount();

$queryRecords = $DB->Query($sqlRec);

//Счетчик кол-ва этераций (кол-во договоров) 
$i = 0;

if (!empty($totalRecords)) {
	//Делает выборку значений полей(строк) в массив
	while ($rowQueryRecords = $queryRecords->Fetch()) {
		
		//ID Договора
		$idContract = !empty($rowQueryRecords['ID_CONTRACT']) ? (int)$rowQueryRecords['ID_CONTRACT'] : 0; //ID договора
		
		//Договор (номер договора)
		//$data[$i]['NUMBER'] = !empty($rowQueryRecords['NUMBER']) ? $rowQueryRecords['NUMBER'] : '';
		$data[$i]['NUMBER'] = !empty($rowQueryRecords['NUMBER']) ? '<a href="/contract_card.php?ID=' . $idContract . '&1" target="_blank">' . $rowQueryRecords['NUMBER'] . '</a>' : '';
		
		//Контрагент (клиент,компания)
		$data[$i]['COMPANY'] = !empty($rowQueryRecords['COMPANY']) ? $rowQueryRecords['COMPANY'] : ''; //Наименование компании
		
		//Сумма по всем заявкам в договоре
		$data[$i]['SUM_ALL_PRICE'] = !empty($rowQueryRecords['SUM_ALL_PRICE']) ? $rowQueryRecords['SUM_ALL_PRICE'] : 0; 
		
		//Сумма неоплаченных заявок
		$data[$i]['SUM_NO_PAYMENT'] = !empty($rowQueryRecords['SUM_NO_PAYMENT']) ? $rowQueryRecords['SUM_NO_PAYMENT'] : 0; 
		
		//Всего заявок в договоре
		$data[$i]['COUNT_REQUEST'] = !empty($rowQueryRecords['COUNT_REQUEST']) ? $rowQueryRecords['COUNT_REQUEST'] : 0; 
		
		//Всего заявок в договоре
		$data[$i]['COUNT_REQUEST_NO_PAYMENT'] = !empty($rowQueryRecords['COUNT_REQUEST_NO_PAYMENT']) ? $rowQueryRecords['COUNT_REQUEST_NO_PAYMENT'] : 0;
		
		//'Кнопка' Сформировать акт сверки 
		$data[$i]['BUTTON_REVISE'] = '<button type="button" data-id-contract="' . $idContract . '" class="btn-revise">Акт сверки</button>';
		
		$i++;
	}
}

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data
);

//Очищаем таблицу от данных
$DB->Query("TRUNCATE TABLE `REVISE_TABLE`");

echo json_encode($json_data);
?>