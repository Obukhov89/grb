<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';


include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

$columns = array(
    0 => 'STAGE_NUMBER',
    1 => 'REQUEST_TITLE',
    //2 => 'DATE_CREATE',
    2 => 'DATE_CREATE_TIMESTAMP',
    3 => 'COMPANY_TITLE',
    4 => 'DEADLINE',
    5 => 'MATERIAL',
    6 => 'ASSIGNED',
    7 => 'NUM_ACT_TABLE',
    8 => 'ID',
    9 => 'DOGOVOR_TABLE', 
    10 => 'PRICE',
	11 => 'ACCOUNT', 
	12 => 'DATE_OPLATA',
    13 => 'DATE_CREATE_TIMESTAMP',
    14 => 'NUM_P_TABLE',
	15 => 'MANUFACTURER_TITLE',
    16 => 'USER_HISTORY',
);


	$user_id = !empty($USER->GetID()) ? (int)$USER->GetID() : ''; 


$where_condition = $sqlTot = $sqlRec = "";

$where_condition = " WHERE `TYPE_ID` != '3' ";

$params_stage = !empty($params['stage']) ? $params['stage'] : '';

switch ($params_stage) {
    case '1':

        $where_condition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING') AND IF(ACT_NUM, TRUE, FALSE) = FALSE ";
        //$id_act_num =0;
        break;
    case '2':
        //$id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE');
        //$where_condition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE') ";
		$where_condition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE') AND IF(ACT_NUM, TRUE, FALSE) = TRUE ";
        //$id_act_num =1;
        break;
    case '3':
        //$id_stage = array('1');
        $where_condition .= " AND `STAGE_ID`='1' ";
        //$id_act_num =1;
        break;
    case '4':
        //$id_stage = array('2', '3', '4');
        //$where_condition .= " AND `STAGE_ID`=('2' || '3' || '4') ";
        //$where_condition .= " AND (`STAGE_ID`='2' OR `STAGE_ID`='3' OR `STAGE_ID`='4') ";
        $where_condition .= " AND STAGE_ID in ('2', '3', '4') ";
        //$id_act_num =1;
        break;
    case '5':
        //$id_stage = array('5', '6', '7', '8', '9', 'LOSE');
        $where_condition .= " AND STAGE_ID in ('5', '6', '7', '8', '9', 'LOSE') ";
        //$id_act_num =1;
        break;
    case '10':
        //$id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON');$where_co
        //$where_condition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON') ";
		$where_condition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON') AND IF(ACT_NUM, TRUE, FALSE) = TRUE";
        //$id_act_num =1;
        break;
    case '11':
        //$id_stage = array('WON');
        $where_condition .= " AND `STAGE_ID`='WON' ";
        //$id_act_num =1;
        break;
	case 'plan':
		$where_condition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1') ";
		break;
    case 'all':
        //$id_dep = 53;
        break;
	case '':
		$where_condition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1') "; 
		break;
}


switch ($params_stage) {
    case '6':
    //$where_condition .= " AND `PRICE` IS NOT NULL AND `PRICE` > 0 AND (`OPLATA` = 0 || `OPLATA` IS NULL) "; 
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

switch ($params_year) {
    case '2019':
        //$id_year1=date("Y-m-d", strtotime("2018-12-31"));
        //$id_year2=date("Y-m-d", strtotime("2020-01-01"));
        //$where_condition .= " AND (DATE_CREATE >= '01.01.2019 00:00:00' AND DATE_CREATE < '01.01.2019 59:59:59') ";
        //$where_condition .= " AND (`DATE_CREATE` BETWEEN '01.01.2019 00:00:00' AND '31.12.2019 59:59:59') ";
        $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2018-12-31' AND `DATE_CREATE_TIMESTAMP` < '2020-01-01') ";
        break;
    case '2020':
        //$id_year1=date("Y-m-d", strtotime("2019-12-31"));
        //$id_year2=date("Y-m-d", strtotime("2021-01-01"));
        //$id_year1="2019-12-31";
        //$id_year2="2021-01-01";
        //$where_condition .= " AND (DATE_CREATE >= '01.01.2020 00:00:00' AND DATE_CREATE < '01.01.2021 00:00:00') ";
        //$where_condition .= " AND (`DATE_CREATE` BETWEEN '01.01.2020 00:00:00' AND '31.12.2020 59:59:59') ";
        $where_condition .= " AND (DATE_CREATE_TIMESTAMP > '2019-12-31' AND `DATE_CREATE_TIMESTAMP` < '2021-01-01') ";
        break;
    case '12020':
        //$id_year1=date("Y-m-d", strtotime("2019-12-31"));
        //$id_year2=date("Y-m-d", strtotime("2020-02-01"));
        //$where_condition .= " AND (`DATE_CREATE` BETWEEN '01.01.2020 00:00:00' AND '31.01.2020 59:59:59') ";
        $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2019-12-31' AND `DATE_CREATE_TIMESTAMP` < '2020-02-01') ";
        break;
    case '22020':
        //$id_year1=date("Y-m-d", strtotime("2020-01-31"));
        //$id_year2=date("Y-m-d", strtotime("2020-03-01"));
        //$where_condition .= " AND (`DATE_CREATE` BETWEEN '01.02.2020 00:00:00' AND '29.02.2020 59:59:59') ";
        $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-01-31' AND `DATE_CREATE_TIMESTAMP` < '2020-03-01') ";
        break;
    case '32020':
        //$id_year1=date("Y-m-d", strtotime("2020-02-29"));
        //$id_year2=date("Y-m-d", strtotime("2020-04-01"));
        //$where_condition .= " AND (`DATE_CREATE` BETWEEN '01.03.2020 00:00:00' AND '31.03.2020 59:59:59') ";
        $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-02-29' AND `DATE_CREATE_TIMESTAMP` < '2020-04-01') ";
        break;
    case '42020':
        //$id_year1=date("Y-m-d", strtotime("2020-03-31"));
        //$id_year2=date("Y-m-d", strtotime("2020-05-01"));
        //$where_condition .= " AND (`DATE_CREATE` BETWEEN '01.04.2020 00:00:00' AND '30.04.2020 59:59:59') ";
        $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-03-31' AND `DATE_CREATE_TIMESTAMP` < '2020-05-01') ";
        break;
	case '102020':
        //$id_year1=date("Y-m-d", strtotime("2020-03-31"));
        //$id_year2=date("Y-m-d", strtotime("2020-05-01"));
        //$where_condition .= " AND (`DATE_CREATE` BETWEEN '01.04.2020 00:00:00' AND '30.04.2020 59:59:59') ";
        $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-10-01' AND `DATE_CREATE_TIMESTAMP` < '2020-11-01') ";
        break;
    case 'all':
        //$id_year1=date("Y-m-d", strtotime("2019-01-01"));
        //$id_year2=date("Y-m-d", strtotime("2029-12-31"));
        //$where_condition .= " AND (`DATE_CREATE` BETWEEN '01.01.2020 00:00:00' AND '31.12.2029 59:59:59') ";
        $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2019-01-01' AND `DATE_CREATE_TIMESTAMP` < '2029-12-31') ";
        break;
    
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

			if (!empty($val_param['search']['value']) && !empty($val_param['data']) && $val_param['data'] == 'STAGE_NUMBER' && $val_param['search']['value'] == 'checked') {


				$where_condition .= " AND `MANUFACTURER_TITLE` IS NOT NULL AND `MANUFACTURER_TITLE`!='' ";
			} elseif (!empty($val_param['search']['value'])) {
				$where_condition .= " AND " . $val_param['data'] . " LIKE '%" . $val_param['search']['value'] . "%' ";
			}

    }
}


if (!empty($params['search']['value'])) {
    $where_condition .= " AND (REQUEST_TITLE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR DATE_CREATE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR COMPANY_TITLE LIKE '%" . $params['search']['value'] . "%' ";
	//--'Поиск в журнале заявок' начало--//
    //$where_condition .= " OR DEADLINE LIKE '%" . $params['search']['value'] . "%' ";
	$where_condition .= " OR DEADLINE_TABLE LIKE '%" . $params['search']['value'] . "%' ";
	//--'Поиск в журнале заявок' окончание--//
    $where_condition .= " OR MATERIAL LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR ASSIGNED LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR NUM_ACT_TABLE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR ID LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR DOGOVOR_TABLE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR PRICE LIKE '%" . $params['search']['value'] . "%' ";
	//--'Вывод номера счета в журнале заявок' начало--//
	$where_condition .= " OR ACCOUNT LIKE '%" . $params['search']['value'] . "%' ";
	//--'Вывод номера счета в журнале заявок' окончание--//
    $where_condition .= " OR NUM_P_TABLE LIKE '%" . $params['search']['value'] . "%' ";
	//-'Производитель' начало--//
	$where_condition .= " OR MANUFACTURER_TITLE LIKE '%" . $params['search']['value'] . "%' ";
	//-'Производитель' окончание--//
    $where_condition .= " OR USER_HISTORY LIKE '%" . $params['search']['value'] . "%') ";
}



if ($columns[$params['order'][0]['column']] == 'REQUEST_TITLE') {
    //SELECT CONVERT(substring_index(substring_index(REQUEST_TITLE, '№', -1), '/', 1 ),UNSIGNED INTEGER) AS request FROM `ba_tz` ORDER BY REQUEST DESC;
    $sql_query = " SELECT *,CONVERT(substring_index(substring_index(REQUEST_TITLE, '№', -1), '/', 1 ),UNSIGNED INTEGER) AS request FROM `ba_tz` ";
	//$sql_query = " SELECT * FROM `ba_tz` ";
} else if ($columns[$params['order'][0]['column']] == 'NUM_ACT_TABLE') {
    $sql_query = " SELECT *,CONVERT(substring_index(NUM_ACT_TABLE, '/', 1 ),UNSIGNED INTEGER) AS act FROM `ba_tz` ";
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
    //SELECT CONVERT(substring_index(substring_index(REQUEST_TITLE, '№', -1), '/', 1 ),UNSIGNED INTEGER) AS request FROM `ba_tz` ORDER BY REQUEST DESC;
    $column_name = "request";
	//$column_name = $columns[$params['order'][0]['column']];
} else if ($columns[$params['order'][0]['column']] == 'NUM_ACT_TABLE') {
    $column_name = "act";
} else if ($columns[$params['order'][0]['column']] == 'NUM_P_TABLE') {
    $column_name = "p";
} else if ($columns[$params['order'][0]['column']] == 'DATE_OPLATA') {
    $column_name = 'DATE_FORMAT(' . $columns[$params['order'][0]['column']] . ', "%d.%m.%Y")';
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
		//$select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . (int)$row['ID']);
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

    $data[$i]['STAGE_NUMBER'] = $row['STAGE_NUMBER'];

    $data[$i]['COLOR'] = $color;

    $data[$i]['HOVER'] = $title;
    


    $data[$i]['REQUEST_TITLE'] = "<a href='/request_card.php?ID=". $row['ID_Z'] ."&ACT_NUM=".$row['ACT_NUM']."' target='_blank'>".$row['REQUEST_TITLE']."</a>";
                        


    $data[$i]['DATE_CREATE'] = $row['DATE_CREATE'];
    

    $data[$i]['COMPANY_TITLE'] = $row['COMPANY_TITLE'];


    $deadlineDate = (!empty($row['DEADLINE_TABLE']) ? date('d.m.Y', strtotime($row['DEADLINE_TABLE'])) : '');
    $color_date='';
    if (date('Y-m-d') > date("Y-m-d", strtotime($deadlineDate))&&$row['STAGE_ID']!='2'&&$row['STAGE_ID']!='4'&&$row['STAGE_ID']!='WON'&&$row['STAGE_ID']!='LOSE'&&$row['STAGE_ID']!='5'&&$row['STAGE_ID']!='6'&&$row['STAGE_ID']!='7'&&$row['STAGE_ID']!='8'&&$row['STAGE_ID']!='9') $color_date='red';
    $data[$i]['DEADLINE_TABLE'] = $deal['TYPE_ID']!= "COMPLEX" ? "<span style='color: ".$color_date."'>".$deadlineDate."</span>" : '';

    $data[$i]['COLOR_DATE'] = $color_date;



    $data[$i]['MATERIAL'] = !empty($row['MATERIAL']) ? $row['MATERIAL'] : '';




	$assigned = !empty($row['ASSIGNED']) ? strtok($row['ASSIGNED'], ",") : '';

	$assigned_strong = "<strong>" . $assigned . "</strong>";

	$data[$i]['ASSIGNED'] = str_replace($assigned, $assigned_strong, $row['ASSIGNED']);


    $num_act = !empty($row['NUM_ACT_TABLE']) ? $row['NUM_ACT_TABLE'] : '';


    $data[$i]['NUM_ACT_TABLE'] = "<span style='color: ".$color."'><a href='/probe.php?ID=".$row['ID_Z']."&1' target='_blank'>".$num_act."</a></span>";


    $tz = !empty($row['TZ']) ? $row['ID'] : '';

    $data[$i]['ID'] = "<a href='/tz_show.php?ID=".$row['ID']."' target='_blank'>". $tz ."</a>";


    $data[$i]['DOGOVOR_TABLE'] = $row['DOGOVOR_TABLE'] ? $row['DOGOVOR_TABLE'] : '';


		$data[$i]['PRICE'] = $row['PRICE'] ? $row['PRICE'] : ''; 
	


    $data[$i]['COLOR_PRICE'] = '#fff' ;

    $data[$i]['DATE_OPLATA'] = $row['DATE_OPLATA'];

    $result_isp = '';
    if (!$row['PDF']) {
        //$result_isp = $res_tz['ID']&&$res_tz['ACT_NUM'] ? 'Открыть' : '';
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
            //$protocol = $res_tz['RESULTS'] ? $num_tz : '';
            $data_protocol = $row['RESULTS'] ? $row['NUM_P_TABLE'] : '';
            $dir  = '/protocol_generator/archive/'.$row['ID'].date("Y", strtotime($row['DATE_ACT'])).'/';
            $protocol = "<a href='".$dir.$row['ACTUAL_VER'].".docx?1&1' target='_blank'>$data_protocol</a>";
        }
    }
    else
    {
        //$protocol = $res_tz['PDF'] ? $num_tz : '';
        $data_protocol = $row['PDF'] ? $row['NUM_P_TABLE'] : '';
        $protocol = "<a href='/pdf/" . $row['PDF'] . "' target='_blank'>" . $data_protocol . "</a>";
    }

    $data[$i]['NUM_P_TABLE'] = $protocol;
	

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

