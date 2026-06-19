<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

$params = $columns = $totalRecords = $data = array();

$params = !empty($_REQUEST) ? $_REQUEST : '';


$columns = array(
    0 => 'STAGE_NUMBER',
    1 => 'ACCOUNT',
    2 => 'DATE',
    3 => 'PRICE',
    4 => 'COMPANY_TITLE',
    5 => 'MATERIAL',
    6 => 'ASSIGNED',
    7 => 'DOGOVOR_TABLE',
    8 => 'REQUEST_TITLE',
    9 => 'ACT_VR',
    10 => 'DATE_ACT_VR',
	11 => 'SEND_DATE_ACT_VR'
);

$user_id = !empty($USER->GetID()) ? (int)$USER->GetID() : '';

$where_condition = $sqlTot = $sqlRec = "";


$where_condition = " WHERE ba_tz.TYPE_ID != '3' ";


$params_stage = isset($params['stage']) ? $params['stage'] : '';


$stage = "'NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '4', 'WON'";
switch ($params_stage) {
    case '0':
		$where_condition .= " AND ba_tz.PRICE IS NOT NULL AND ba_tz.PRICE > 0 AND (ba_tz.OPLATA = 0 || ba_tz.OPLATA IS NULL) AND (ba_tz.STAGE_ID IN (" . $stage . ") ) "; ;
        break;
    case '1':
		$where_condition .= " AND (ba_tz.STAGE_ID IN (" . $stage . ") ) AND ba_tz.PRICE IS NOT NULL AND ba_tz.OPLATA IS NOT NULL  AND (ba_tz.OPLATA > 0) AND (ba_tz.PRICE > 0) AND (ba_tz.PRICE > ba_tz.OPLATA) ";
        break;
    case '2':
    $where_condition .= " AND ba_tz.PRICE IS NOT NULL AND ba_tz.OPLATA IS NOT NULL AND (ba_tz.OPLATA = ba_tz.PRICE) AND (ba_tz.STAGE_ID IN (" . $stage . ") ) ";
        break;
	case '3':
    $where_condition .= " AND (AKT_VR.NUMBER != '' ) AND (AKT_VR.NUMBER != 'Ждем ответ от 1С' ) AND AKT_VR.SEND_DATE IS NULL ";
        break;
    case 'all':
        break;
}


$params_year = !empty($params['year']) ? $params['year'] : '';

if(!empty($params['dateStart'])&&!empty($params['dateOut'])) {
    $params_dateStart = $params['dateStart'];
    $params_dateOut = $params['dateOut'];
    $where_condition .= ' AND (INVOICE.DATE > "'. $params_dateStart .'" AND INVOICE.DATE < "' . $params_dateOut .'") ';
}else{
    switch ($params_year) {
        case '2019':
            $where_condition .= " AND (INVOICE.DATE > '2018-12-31' AND INVOICE.DATE < '2020-01-01') ";
            break;
        case '2020':
            $where_condition .= " AND (INVOICE.DATE > '2019-12-31' AND INVOICE.DATE < '2021-01-01') ";
            break;
        case '12020':
            $where_condition .= " AND (INVOICE.DATE > '2019-12-31' AND INVOICE.DATE < '2020-02-01') ";
            break;
        case '22020':
            $where_condition .= " AND (INVOICE.DATE > '2020-01-31' AND INVOICE.DATE < '2020-03-01') ";
            break;
        case '32020':
            $where_condition .= " AND (INVOICE.DATE > '2020-02-29' AND INVOICE.DATE < '2020-04-01') ";
            break;
        case '42020':
            $where_condition .= " AND (INVOICE.DATE > '2020-03-31' AND INVOICE.DATE < '2020-05-01') ";
            break;
        case 'all':
            $where_condition .= " AND (INVOICE.DATE > '2019-01-01' AND INVOICE.DATE < '2029-12-31') ";
            break;
    }
}


$params_laba = !empty($params['laba']) ? $params['laba'] : '';

switch ($params_laba) {
    case '1':
        $id_dep = 54;
        $where_condition .= " AND (ba_tz.LABA_ID LIKE '" . $id_dep ."' OR ba_tz.LABA_ID LIKE '" . $id_dep .",%' OR ba_tz.LABA_ID LIKE '%," . $id_dep . "' OR ba_tz.LABA_ID LIKE '%," . $id_dep . ",%') ";
        break;
    case '2':
        $id_dep = 57;
        $where_condition .= " AND (ba_tz.LABA_ID LIKE '" . $id_dep ."' OR ba_tz.LABA_ID LIKE '" . $id_dep .",%' OR ba_tz.LABA_ID LIKE '%," . $id_dep . "' OR ba_tz.LABA_ID LIKE '%," . $id_dep . ",%') ";
        break;
    case '3':
        $id_dep = 56;
        $where_condition .= " AND (ba_tz.LABA_ID LIKE '" . $id_dep ."' OR ba_tz.LABA_ID LIKE '" . $id_dep .",%' OR ba_tz.LABA_ID LIKE '%," . $id_dep . "' OR ba_tz.LABA_ID LIKE '%," . $id_dep . ",%') ";
        break;
    case '4':
        $id_dep = 55;
        $where_condition .= " AND (ba_tz.LABA_ID LIKE '" . $id_dep ."' OR ba_tz.LABA_ID LIKE '" . $id_dep .",%' OR ba_tz.LABA_ID LIKE '%," . $id_dep . "' OR ba_tz.LABA_ID LIKE '%," . $id_dep . ",%') ";
        break;
    case '5':
        $id_dep = 58;
        $where_condition .= " AND (ba_tz.LABA_ID LIKE '" . $id_dep ."' OR ba_tz.LABA_ID LIKE '" . $id_dep .",%' OR ba_tz.LABA_ID LIKE '%," . $id_dep . "' OR ba_tz.LABA_ID LIKE '%," . $id_dep . ",%') ";
        break;
    case '6':
        $id_dep = 53;
        $where_condition .= " AND (ba_tz.LABA_ID LIKE '" . $id_dep ."' OR ba_tz.LABA_ID LIKE '" . $id_dep .",%' OR ba_tz.LABA_ID LIKE '%," . $id_dep . "' OR ba_tz.LABA_ID LIKE '%," . $id_dep . ",%') ";
        break;

}


if (!empty($params['columns'])) {
    foreach ($params['columns'] as $key_param => $val_param) {

			if (!empty($val_param['search']['value']) && !empty($val_param['data']) && $val_param['data'] == 'DATE') { //Поиск по колонке "Дата" счета
				$where_condition .= " AND DATE_FORMAT(INVOICE." . $val_param['data'] . ", '%d.%m.%Y') LIKE '%" . $val_param['search']['value'] . "%' ";
			} elseif (!empty($val_param['search']['value']) && !empty($val_param['data']) && $val_param['data'] == 'ACT_VR') { //Поиск номера Акта ВР по колонке "АКТ ВР"
				$where_condition .= " AND AKT_VR.NUMBER LIKE '%" . $val_param['search']['value'] . "%' ";
			} elseif (!empty($val_param['search']['value']) && !empty($val_param['data']) && $val_param['data'] == 'DATE_ACT_VR') { //Поиск даты Акта ВР по колонке "ДАТА АКТ ВР"
				$where_condition .= " AND DATE_FORMAT(AKT_VR.DATE, '%d.%m.%Y') LIKE '%" . $val_param['search']['value'] . "%' ";
			} elseif (!empty($val_param['search']['value'])) { //Поиск по остальным полям
				$where_condition .= " AND " . $val_param['data'] . " LIKE '%" . $val_param['search']['value'] . "%' ";
			}
    }
}


if (!empty($params['search']['value'])) {
    $where_condition .= " AND (ACCOUNT LIKE '%" . $params['search']['value'] . "%' "; //Счет
    $where_condition .= " OR DATE_FORMAT(INVOICE.DATE, '%d.%m.%Y') LIKE '%" . $params['search']['value'] . "%' "; //Дата
	$where_condition .= " OR PRICE LIKE '%" . $params['search']['value'] . "%' "; //Сумма
    $where_condition .= " OR COMPANY_TITLE LIKE '%" . $params['search']['value'] . "%' "; //Контрагент
	$where_condition .= " OR MATERIAL LIKE '%" . $params['search']['value'] . "%' "; //Наименование материала
	$where_condition .= " OR ASSIGNED LIKE '%" . $params['search']['value'] . "%' "; //Ответственный
	$where_condition .= " OR DOGOVOR_TABLE LIKE '%" . $params['search']['value'] . "%' "; //Договор
	$where_condition .= " OR AKT_VR.NUMBER LIKE '%" . $params['search']['value'] . "%' "; //Акт ВР
    $where_condition .= " OR DATE_FORMAT(AKT_VR.DATE, '%d.%m.%Y') LIKE '%" . $params['search']['value'] . "%') "; //Дата Акт ВР
}

$sql_query = " SELECT ba_tz.*,INVOICE.DATE AS invoice_date,AKT_VR.DATE AS act_vr_date,AKT_VR.SEND_DATE AS act_vr_send_date,AKT_VR.NUMBER,ba_tz.ID as ID FROM `ba_tz` INNER JOIN `INVOICE` ON ba_tz.ID=INVOICE.TZ_ID INNER JOIN `AKT_VR` ON ba_tz.ID=AKT_VR.TZ_ID";


$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}


 if ($columns[$params['order'][0]['column']] == 'ACT_VR') {
    $column_name = "NUMBER";
} else if ($columns[$params['order'][0]['column']] == 'DATE_ACT_VR') {
    $column_name = "act_vr_date";
} else if ($columns[$params['order'][0]['column']] == 'SEND_DATE_ACT_VR') {
    $column_name = "act_vr_send_date";																		   
} else if ($columns[$params['order'][0]['column']] == 'DATE') {
    $column_name = "invoice_date";
} else {
    $column_name = $columns[$params['order'][0]['column']];
}

if (!empty($params['length']) && $params['length'] == "-1") {
    $sqlRec .= " ORDER BY ba_tz." . $column_name . " " . $params['order'][0]['dir'] . " ";
} else {
    $sqlRec .= " ORDER BY " . $column_name . " " . $params['order'][0]['dir'] . " LIMIT " . $params['start'] . " ," . $params['length'] . " ";
}

$queryTot = $DB->Query($sqlTot);


$totalRecords = $queryTot->SelectedRowsCount();

$queryRecords = $DB->Query($sqlRec);


$id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '4', 'WON');

$i = 0;

while ($row = $queryRecords->Fetch()) {

    if (CModule::IncludeModule('crm')) {

        $deal = CCrmDeal::GetByID(!empty($row['ID_Z']) ? $row['ID_Z'] : '');
    }

	$tz_id = !empty($row['ID']) ? $row['ID'] : ''; //id ТЗ
	$id_z = !empty($row['ID_Z']) ? $row['ID_Z'] : ''; //id сделки(заявки)


	$res_invoice = null;
	if (!empty($row['ID'])) {
		$res_invoice = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
	}


	$res_act_vr = null;
	if (!empty($row['ID'])) {
		$res_act_vr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $row['ID'])->Fetch();
	}

    $stage_id = !empty($row['STAGE_ID']) ? $row['STAGE_ID'] : '';


	if(in_array($stage_id, $id_stage) && !empty($row['PRICE']) && empty($row['OPLATA']))
	{
		$color='red';
		$title='Счет не оплачен';
	}

	elseif(in_array($stage_id, $id_stage) && !empty($row['PRICE']) && !empty($row['OPLATA'])
	&& $row['PRICE'] > $row['OPLATA'])
	{
		$color='#FFC0CB';
		$title='Счет оплачен не полностью';
	}

	elseif(in_array($stage_id, $id_stage) && !empty($row['PRICE']) && !empty($row['OPLATA'])
	&& $row['PRICE'] == $row['OPLATA'])
	{
		$color='green';
		$title='Счет оплачен полностью';
	}
	else
	{
		$color='grey';
		$title='Заявка неуспешна';
	}


    $data[$i]['STAGE_NUMBER'] = $row['STAGE_NUMBER'];

    $data[$i]['COLOR'] = $color;

    $data[$i]['HOVER'] = $title;


    $data[$i]['REQUEST_TITLE'] = "<a href='/request_card.php?ID=". $row['ID_Z'] ."&ACT_NUM=".$row['ACT_NUM']."' target='_blank'>Перейти</a>";


    $data[$i]['COMPANY_TITLE'] = $row['COMPANY_TITLE'];


    $data[$i]['MATERIAL'] = !empty($row['MATERIAL']) ? $row['MATERIAL'] : '';


	$assigned = !empty($row['ASSIGNED']) ? strtok($row['ASSIGNED'], ",") : '';

	$assigned_strong = "<strong>" . $assigned . "</strong>";

	$data[$i]['ASSIGNED'] = str_replace($assigned, $assigned_strong, $row['ASSIGNED']);


    $tz = !empty($row['TZ']) ? $row['ID'] : '';


    $data[$i]['DOGOVOR_TABLE'] = $row['DOGOVOR_TABLE'] ? $row['DOGOVOR_TABLE'] : '';


	$data[$i]['PRICE'] = $row['PRICE'] ? $row['PRICE'] : '';


    $data[$i]['COLOR_PRICE'] = ($row['PRICE']&&($row['OPLATA'] >= $row['PRICE'])||!$row['PRICE']) ? '#fff' : '#FFC0CB';


    $history_ass='';
    $history_date='';


	$dir_price  = "protocol_generator/account.php?ID=". $id_z ."&TZ_ID=". $tz_id;
	$data[$i]['ACCOUNT'] = !empty($row['ACCOUNT']) ? "<a href='".$dir_price."'>" . $row['ACCOUNT'] . "</a>" : '';


	$data[$i]['DATE'] = !empty($row['invoice_date']) ? date('d.m.Y', strtotime($row['invoice_date'])) : '';


	$data[$i]['ACT_VR'] = !empty($row['NUMBER']) ? $row['NUMBER'] : '';

	
	$data[$i]['DATE_ACT_VR'] = !empty($row['act_vr_date']) ? date('d.m.Y', strtotime($row['act_vr_date'])) : '';
	$data[$i]['SEND_DATE_ACT_VR'] = !empty($row['act_vr_send_date']) ? date('d.m.Y', strtotime($row['act_vr_send_date'])) : '';																														

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
