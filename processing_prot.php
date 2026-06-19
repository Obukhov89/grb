<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';


include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;


$columns = array(
    0 => 'STAGE_NUMBER',
	1 => 'ATTESTAT',
    2 => 'NUM_P_TABLE',
    3 => 'DATE_P_CORR',
    4 => 'COMPANY_TITLE',
    5 => 'MATERIAL',
    6 => 'ASSIGNED',
	7 => 'REQUEST_TITLE',
	8 => 'ID',
	9 => 'DOGOVOR_TABLE',
	10 => 'PRICE',
	11 => 'ACCOUNT',
	12 => 'DATE_OPLATA',
    13 => 'DATE_CREATE_TIMESTAMP',

    14 => 'NUM_P_TABLE',
	15 => 'MANUFACTURER_TITLE',
    16 => 'USER_HISTORY'
);

$user_id = !empty($USER->GetID()) ? (int)$USER->GetID() : '';

$where_condition = $sqlTot = $sqlRec = "";

$where_condition = " WHERE `TYPE_ID` != '3' AND !ISNULL(`NUM_P`) ";

$params_stage = !empty($params['stage']) ? $params['stage'] : '';

switch ($params_stage) {
    case '1':
        $where_condition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING') AND IF(ACT_NUM, TRUE, FALSE) = FALSE ";
        break;
    case '2':
		$where_condition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE') AND IF(ACT_NUM, TRUE, FALSE) = TRUE ";
        break;
    case '3':
        $where_condition .= " AND `STAGE_ID`='1' ";
        break;
    case '4':
        $where_condition .= " AND STAGE_ID in ('2', '3', '4') ";
        break;
    case '5':
        $where_condition .= " AND STAGE_ID in ('5', '6', '7', '8', '9', 'LOSE') ";
        break;
    case '10':
		$where_condition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON') AND IF(ACT_NUM, TRUE, FALSE) = TRUE";
        break;
    case '11':
        $where_condition .= " AND `STAGE_ID`='WON' ";
        break;
    case 'all':
        break;
}


switch ($params_stage) {
    case '6':
    $where_condition .= " AND `PRICE` IS NOT NULL AND `PRICE` > 0 AND (`OPLATA` = 0 || `OPLATA` IS NULL) AND (`STAGE_ID` NOT IN ('LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13') ) ";
        break;
    case '7':
		$where_condition .= " AND (`OPLATA` < `PRICE`) AND (`OPLATA` > 0) ";
        break;
    case '8':
    $where_condition .= " AND (`OPLATA` > `PRICE`) ";
        break;
    case '9':
    $where_condition .= " AND (`OPLATA` = `PRICE`) ";
        break;
}


$params_year = !empty($params['year']) ? $params['year'] : '';

if(!empty($params['dateStart']) && !empty($params['dateOut'])) {
    $params_dateStart = $params['dateStart'];
    $params_dateOut = $params['dateOut'];

    $where_condition .= ' AND (DATE_P_CORR > "' . $params_dateStart . '" AND DATE_P_CORR <= "' . $params_dateOut . '")';
}else{
    switch ($params_year) {
        case '2019':
            $where_condition .= " AND (`DATE_P_CORR` > '2018-12-31' AND `DATE_P_CORR` < '2020-01-01') ";
            break;
        case '2020':
            $where_condition .= " AND (DATE_P_CORR > '2019-12-31' AND `DATE_P_CORR` < '2021-01-01') ";
            break;
        case '12020':
            $where_condition .= " AND (`DATE_P_CORR` > '2019-12-31' AND `DATE_P_CORR` < '2020-02-01') ";
            break;
        case '22020':
            $where_condition .= " AND (`DATE_P_CORR` > '2020-01-31' AND `DATE_P_CORR` < '2020-03-01') ";
            break;
        case '32020':
            $where_condition .= " AND (`DATE_P_CORR` > '2020-02-29' AND `DATE_P_CORR` < '2020-04-01') ";
            break;
        case '42020':
            $where_condition .= " AND (`DATE_P_CORR` > '2020-03-31' AND `DATE_P_CORR` < '2020-05-01') ";
            break;
        case 'all':
            $where_condition .= " AND (`DATE_P_CORR` > '2019-01-01' AND `DATE_P_CORR` < '2029-12-31') ";
            break;

    }
}

$params_laba = !empty($params['laba']) ? $params['laba'] : '';

switch ($params_laba) {
    case '1':
        $id_dep = 54;
        $where_condition .= " AND (`LABA_ID` LIKE '" . $id_dep ."' OR `LABA_ID` LIKE '" . $id_dep .",%' OR `LABA_ID` LIKE '%," . $id_dep . "' OR `LABA_ID` LIKE '%," . $id_dep . ",%') ";
        break;
    case '2':
        $id_dep = 57;
        $where_condition .= " AND (`LABA_ID` LIKE '" . $id_dep ."' OR `LABA_ID` LIKE '" . $id_dep .",%' OR `LABA_ID` LIKE '%," . $id_dep . "' OR `LABA_ID` LIKE '%," . $id_dep . ",%') ";
        break;
    case '3':
        $id_dep = 56;
        $where_condition .= " AND (`LABA_ID` LIKE '" . $id_dep ."' OR `LABA_ID` LIKE '" . $id_dep .",%' OR `LABA_ID` LIKE '%," . $id_dep . "' OR `LABA_ID` LIKE '%," . $id_dep . ",%') ";
        break;
    case '4':
        $id_dep = 55;
        $where_condition .= " AND (`LABA_ID` LIKE '" . $id_dep ."' OR `LABA_ID` LIKE '" . $id_dep .",%' OR `LABA_ID` LIKE '%," . $id_dep . "' OR `LABA_ID` LIKE '%," . $id_dep . ",%') ";
        break;
    case '5':
        $id_dep = 58;
        $where_condition .= " AND (`LABA_ID` LIKE '" . $id_dep ."' OR `LABA_ID` LIKE '" . $id_dep .",%' OR `LABA_ID` LIKE '%," . $id_dep . "' OR `LABA_ID` LIKE '%," . $id_dep . ",%') ";
        break;
    case '6':
        $id_dep = 53;
        $where_condition .= " AND (`LABA_ID` LIKE '" . $id_dep ."' OR `LABA_ID` LIKE '" . $id_dep .",%' OR `LABA_ID` LIKE '%," . $id_dep . "' OR `LABA_ID` LIKE '%," . $id_dep . ",%') ";
        break;

}

if (!empty($params['columns'])) {
    foreach ($params['columns'] as $key_param => $val_param) {

		if (!empty($val_param['search']['value']) && !empty($val_param['data']) && $val_param['data'] == 'STAGE_NUMBER' && $val_param['search']['value'] == 'checked') { //если чекбокс "Производитель" в журнале заявок checked
			$where_condition .= " AND `MANUFACTURER_TITLE` IS NOT NULL AND `MANUFACTURER_TITLE`!='' ";
		} elseif (!empty($val_param['search']['value']) && $val_param['data'] == 'DATE_P_CORR') { //Если поиск производим по колонке Дата
			$where_condition .= " AND DATE_FORMAT(" . $val_param['data'] . ", '%d.%m.%Y') LIKE '%" . $val_param['search']['value'] . "%' ";
		} elseif (!empty($val_param['search']['value'])) {
			$where_condition .= " AND " . $val_param['data'] . " LIKE '%" . $val_param['search']['value'] . "%' ";
		}
    }
}

if (!empty($params['search']['value'])) {
	$where_condition .= " AND (REQUEST_TITLE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR DATE_P_CORR LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR COMPANY_TITLE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR MATERIAL LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR ASSIGNED LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR ID LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR DOGOVOR_TABLE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR PRICE LIKE '%" . $params['search']['value'] . "%' ";
	$where_condition .= " OR ACCOUNT LIKE '%" . $params['search']['value'] . "%' ";
	$where_condition .= " OR DATE_OPLATA LIKE '%" . $params['search']['value'] . "%' ";
	$where_condition .= " OR ATTESTAT LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR NUM_P_TABLE LIKE '%" . $params['search']['value'] . "%' ";
	$where_condition .= " OR MANUFACTURER_TITLE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR USER_HISTORY LIKE '%" . $params['search']['value'] . "%') ";
}

if ($columns[$params['order'][0]['column']] == 'REQUEST_TITLE') {
    $sql_query = " SELECT *,CONVERT(substring_index(substring_index(REQUEST_TITLE, '№', -1), '/', 1 ),UNSIGNED INTEGER) AS request FROM `ba_tz` ";
} else if ($columns[$params['order'][0]['column']] == 'NUM_ACT_TABLE') {
    //$sql_query = " SELECT *,CONVERT(substring_index(NUM_ACT_TABLE, '/', 1 ),UNSIGNED INTEGER) AS act FROM `ba_tz` ";
} else if ($columns[$params['order'][0]['column']] == 'NUM_P_TABLE') {
    $sql_query = " SELECT *,CONVERT(substring_index(NUM_P_TABLE, '/', 1 ),UNSIGNED INTEGER) AS p FROM `ba_tz` ";
} else {
    $sql_query = " SELECT * FROM `ba_tz` ";
}

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

if ($columns[$params['order'][0]['column']] == 'REQUEST_TITLE') {
    $column_name = "request";
} else if ($columns[$params['order'][0]['column']] == 'NUM_ACT_TABLE') {
    //$column_name = "act";
} else if ($columns[$params['order'][0]['column']] == 'NUM_P_TABLE') {
    $column_name = "p";
} else {
    $column_name = $columns[$params['order'][0]['column']];
}

if (!empty($params['length']) && $params['length'] == "-1") {
    $sqlRec .= " ORDER BY " . $column_name . " " . $params['order'][0]['dir'] . " ";
} else {
    $sqlRec .= " ORDER BY " . $column_name . " " . $params['order'][0]['dir'] . " LIMIT " . $params['start'] . " ," . $params['length'] . " ";
}

$queryTot = $DB->Query($sqlTot);

$totalRecords = $queryTot->SelectedRowsCount();

$queryRecords = $DB->Query($sqlRec);

$i = 0;

while ($row = $queryRecords->Fetch()) {
    if (CModule::IncludeModule('crm')) {

        $deal = CCrmDeal::GetByID(!empty($row['ID_Z']) ? $row['ID_Z'] : '');
    }

	$select_check_tz = null;
	if (!empty($row['ID'])) {
		$select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . (int)$row['ID'] . " AND `date_return` IS NULL");
	}

	$selected_rows_count = !empty($select_check_tz->SelectedRowsCount()) ? $select_check_tz->SelectedRowsCount() : ''; //количество выбранных записей

	$res_kp = null;
	if (!empty($row['ID'])) {
		$res_kp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
	}

    $stage_id = !empty($row['STAGE_ID']) ? $row['STAGE_ID'] : '';

    switch ($stage_id) {
        case 'NEW':
            $color='#00B0F0';
            $title='Испытания еще не проводились. Пробы не получены.';
            break;
        case 'PREPARATION':
            $color='#00B0F0';
            $title='Испытания еще не проводились. Пробы не получены.';
            break;
        case 'PREPAYMENT_INVOICE':
            $color='#00B0F0';
            $title='Испытания еще не проводились. Пробы не получены.';
            break;
        case 'EXECUTING':
            $color='#00B0F0';
            $title='Испытания еще не проводились. Пробы не получены.';
            break;
        case 'FINAL_INVOICE':
            $color='#FFFF00';
            $title='Пробы получены. Проводятся испытания';
            break;
        case '1':
            $color='#FFFF00';
            $title='Пробы получены. Проводятся испытания.';
            break;
        case '2':
            $color='#833ea3';
            $title='Испытания в лаборатории завершены. Оплата получена или не требуется.';
            break;
        case '4':
            $color='#19ff19';
            $title='Акты ВР отправлены заказчику.';
            break;
        // case '3':
        // $color='green';
        // $c=5;
        // $title='Протокол выдан';
        // break;
        case 'WON':
            $color='#70AD47';
            $title='Акты ВР получены. Заявка успешно завершена.';
            break;
        case 'LOSE':
            $color='red';
            $title='Испытания не проведены. Заявка прекращена.';
            break;
        case '7':
            $color='red';
            $title='Заявка прекращена. Не проводим подобные испытания.';
            break;
        case '6':
            $color='red';
            $title='Заявка прекращена. Заказчик не вышел на связь.';
            break;
        case '5':
            $color='red';
            $title='Заявка прекращена. Заказчика не устроила цена.';
            break;
        case '8':
            $color='red';
            $title='Заявка прекращена. Создана другая заявка.';
            break;
        case '9':
            $color='red';
            $title='Заявка прекращена. Заказчик выбрал лабораторию в своем городе.';
            break;
        case '10':
            $color='red';
            $title='Заявка прекращена. Заказчик решил не проводить испытания.';
            break;
        case '11':
            $color='red';
            $title='Заявка прекращена. Отказались сами в связи с высокой загруженностью.';
            break;
        case '12':
            $color='red';
            $title='Заявка прекращена. Судебная экспертиза.';
            break;
        case '13':
            $color='red';
            $title='Заявка прекращена. Участие в тендере.';
            break;
    }
    if ($row['ACT_NUM']&&($deal['STAGE_ID']=='NEW'||$deal['STAGE_ID']=='PREPARATION'||$deal['STAGE_ID']=='PREPAYMENT_INVOICE'||$deal['STAGE_ID']=='EXECUTING')) {
        $color='yellow';
        $title='Протокол не выдан. Испытания еще не проводились. Пробы получены';
    }
    if ($row['PRICE']&&$row['RESULTS']&&(!$row['OPLATA']||$row['OPLATA']<$row['PRICE'])&&$deal['STAGE_ID']=='2') {
        $color='#9b2d30';
        $title='Испытания в лаборатории завершены. Оплата не поступила.';
    }

	$data[$i]['COLOR_CHECK_TZ'] = '';

	$data[$i]['COLOR_TZ_CHECKED'] = '';

	$confirm = 1;

	if (!empty($selected_rows_count)) {

		while($check_tz = $select_check_tz->Fetch()) {

			if (!empty($check_tz['leader']) && $check_tz['leader'] == $user_id && empty($check_tz['confirm'])) {

				$data[$i]['COLOR_CHECK_TZ'] = 'mango_class';
			}

			if (empty($check_tz['confirm'])) {
				$confirm = 0;
			}

			if(!empty($check_tz['confirm']) && empty($res_kp['ID']) && in_array($user_id, [35,62])) {
				$data[$i]['COLOR_TZ_CHECKED'] = 'green_class';
			}



		}

	}

	if (!empty($selected_rows_count) && empty($confirm)) {
		$color = '#060060';
		$title = 'Заявка на стадии проверки ТЗ';
	}

	$attestat = $DB->Query("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ` = {$row['ID']}")->Fetch();

    $data[$i]['STAGE_NUMBER'] = $row['STAGE_NUMBER'];

    $data[$i]['COLOR'] = $color;

    $data[$i]['HOVER'] = $title;

    $data[$i]['REQUEST_TITLE'] = "<a href='/request_card.php?ID=". $row['ID_Z'] ."&ACT_NUM=".$row['ACT_NUM']."' target='_blank'>".$row['REQUEST_TITLE']."</a>";

    $data[$i]['DATE_P_CORR'] = ruDate($row['DATE_P_CORR']);

    $data[$i]['COMPANY_TITLE'] = $row['COMPANY_TITLE'];

    $deadlineDate = (!empty($row['DEADLINE_TABLE']) ? date('d.m.Y', strtotime($row['DEADLINE_TABLE'])) : '');
    $color_date='';
    if (date('Y-m-d') > date("Y-m-d", strtotime($deadlineDate))&&$row['STAGE_ID']!='2'&&$row['STAGE_ID']!='4'&&$row['STAGE_ID']!='WON'&&$row['STAGE_ID']!='LOSE'&&$row['STAGE_ID']!='5'&&$row['STAGE_ID']!='6'&&$row['STAGE_ID']!='7'&&$row['STAGE_ID']!='8'&&$row['STAGE_ID']!='9') $color_date='red';


    $data[$i]['COLOR_DATE'] = $color_date;

    $data[$i]['MATERIAL'] = !empty($row['MATERIAL']) ? $row['MATERIAL'] : '';

	$assigned = !empty($row['ASSIGNED']) ? strtok($row['ASSIGNED'], ",") : '';

	$assigned_strong = "<strong>" . $assigned . "</strong>";

	$data[$i]['ASSIGNED'] = str_replace($assigned, $assigned_strong, $row['ASSIGNED']);

//	$data[$i]['ATTESTAT'] = $row['ATTESTAT'] ? 'A' : '';
	$data[$i]['ATTESTAT'] = ($attestat['IN_ATTESTAT_DIAPASON'] == 1) ? 'A' : '';

    $tz = !empty($row['TZ']) ? $row['ID'] : '';


    $data[$i]['ID'] = "<a href='/tz_show.php?ID=".$row['ID']."' target='_blank'>". $tz ."</a>";

    $data[$i]['DOGOVOR_TABLE'] = $row['DOGOVOR_TABLE'] ? $row['DOGOVOR_TABLE'] : '';

		$data[$i]['PRICE'] = $row['PRICE'] ? $row['PRICE'] : '';

    $data[$i]['COLOR_PRICE'] = ($row['PRICE']&&($row['OPLATA'] >= $row['PRICE'])||!$row['PRICE']) ? '#fff' : '#FFC0CB';

    $data[$i]['DATE_OPLATA'] = $row['DATE_OPLATA'];

    $result_isp = '';
    if (!$row['PDF']) {
        $str_result_isp = ($row['ID']&&$row['ACT_NUM'])? 'Открыть' : '';
        $result_isp = "<a href='/results_isp.php?ID=".$row['ID_Z']."' target='_blank'>".$str_result_isp."</a>";
    } else {
        $result_isp = "Выдан вне ЛИС";
    }

    $data[$i]['RESULT_ISP'] = $result_isp;

    $protocol = null;
    if(!$row['NO_BITRIX'])
    {

		if($row['ACTUAL_VER'])
        {

			$idTz = !empty($row['ID']) ? $row['ID'] : '';
            $dateAct = !empty($row['DATE_ACT']) ? date("Y", strtotime($row['DATE_ACT'])) : '';
            $directory = !empty($idTz) && !empty($dateAct) ? $idTz . $dateAct : '';

            $typeTz = isset($row['TYPE_TZ']) ? $row['TYPE_TZ'] : '';

            $numP = !empty($row['NUM_P']) ? $row['NUM_P'] : '';

			if (!empty($directory) && !empty($numP) && is_numeric($numP)) {

                $pathToPdfProtocol = current(glob('./protocol_generator/archive/' . $directory . '/*.pdf'));

				$pathToSigFile = './protocol_generator/archive/' . $directory . '/' . $numP . '.sig'; 

				if (isset($typeTz) && in_array($typeTz, [33, 34, 35, 36, 37, 38, 39]) && file_exists($pathToSigFile) && file_exists($pathToPdfProtocol)) { 
					$protocol = "<a href='" .$pathToPdfProtocol . "' target='_blank'>PDF</a>";
				} elseif (isset($typeTz) && !in_array($typeTz, [33, 34, 35, 36, 37, 38, 39]) && file_exists($pathToPdfProtocol)) { //Если тип заявки НЕ упрощенный и есть файл pdf
					$protocol = "<a href='" .$pathToPdfProtocol . "' target='_blank'>PDF</a>";
				}
            }
        }
    }
    else
    {

        $data_protocol = $row['PDF'] ? $row['NUM_P_TABLE'] : '';

		
		$protocol = !empty($row['PDF']) ? "<a href='/pdf/" . $row['PDF'] . "' target='_blank'>PDF</a>" : '';
    }
	
	$typeTz = !empty($row['TYPE_TZ']) ? $row['TYPE_TZ'] : '';
    $numPTable = !empty($row['NUM_P_TABLE']) ? $row['NUM_P_TABLE'] : '';


    $resultNumPTable = !empty($numPTable) && in_array($typeTz, [33, 34, 35, 36, 37, 38, 39]) ? $numPTable . ' ' . 'У' : $numPTable;

	$data[$i]['NUM_P_TABLE'] = $resultNumPTable;
	 
	$data[$i]['PROTOCOL'] = $protocol;

	$data[$i]['MANUFACTURER_TITLE'] = !empty($row['MANUFACTURER_TITLE']) ? $row['MANUFACTURER_TITLE'] : '';

    $history_ass='';
    $history_date='';

    $data[$i]['USER_HISTORY'] = $row['USER_HISTORY'];

	$data[$i]['ACCOUNT'] = !empty($row['ACCOUNT']) ? $row['ACCOUNT'] : '';
	

    $i++;

}


$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data
);

echo json_encode($json_data);


?>
