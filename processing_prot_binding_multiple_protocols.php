<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

$columns = array(
    0 => 'STAGE_NUMBER',
    1 => 'ATTESTAT', 
    2 => 'NUMBER_AND_YEAR', 
    3 => 'DATE', 
    4 => 'COMPANY_TITLE', 
    5 => 'MATERIAL', 
    6 => 'VERIFY_NAME_LASTNAME',
    7 => 'REQUEST_TITLE', 
    8 => 'ID', 
    9 => 'DOGOVOR_TABLE', 
    10 => 'PRICE', 
    11 => 'ACCOUNT', 
    12 => 'DATE_OPLATA',
    13 => 'DATE_CREATE_TIMESTAMP',
    15 => 'MANUFACTURER_TITLE', 
    16 => 'USER_HISTORY' 
);

$user_id = !empty($USER->GetID()) ? (int)$USER->GetID() : '';

$where_condition = $sqlTot = $sqlRec = "";

$where_condition = " WHERE ba_tz.`TYPE_ID` != '3' AND PROTOCOLS.NUMBER IS NOT NULL ";


$params_stage = !empty($params['stage']) ? $params['stage'] : '';

switch ($params_stage) {
    case '1':
        
        $where_condition .= " AND ba_tz.STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING') AND IF(ACT_NUM, TRUE, FALSE) = FALSE ";
        break;
    case '2':
        
        $where_condition .= " AND ba_tz.STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE') AND IF(ACT_NUM, TRUE, FALSE) = TRUE ";
        break;
    case '3':
        
        $where_condition .= " AND ba_tz.`STAGE_ID`='1' ";
        break;
    case '4':
        
        $where_condition .= " AND ba_tz.STAGE_ID in ('2', '3', '4') ";
        break;
    case '5':
        
        $where_condition .= " AND ba_tz.STAGE_ID in ('5', '6', '7', '8', '9', 'LOSE') ";
        break;
    case '6': 
        
        $where_condition .= " AND ba_tz.`PRICE` IS NOT NULL AND ba_tz.`PRICE` > 0 AND (ba_tz.`OPLATA` = 0 || ba_tz.`OPLATA` IS NULL) AND (ba_tz.`STAGE_ID` NOT IN ('LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13') ) ";
        break;
    case '7':
        
        $where_condition .= " AND (ba_tz.`OPLATA` < ba_tz.`PRICE`) AND (`OPLATA` > 0) ";
        break;
    case '8': 
        
        $where_condition .= " AND (ba_tz.`OPLATA` > ba_tz.`PRICE`) ";
        break;
    case '9':
        
        $where_condition .= " AND (ba_tz.`OPLATA` = ba_tz.`PRICE`) ";
        break;
    case '10':
        
        $where_condition .= " AND ba_tz.STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON') AND IF(ACT_NUM, TRUE, FALSE) = TRUE";
        break;
    case '11':
        
        $where_condition .= " AND ba_tz.`STAGE_ID`='WON' ";
        break;
    case 'all':
        break;
}



$params_year = !empty($params['year']) ? $params['year'] : '';

if(!empty($params['dateStart']) && !empty($params['dateOut'])) {
    $params_dateStart = $params['dateStart'];
    $params_dateOut = $params['dateOut'];

    
    $where_condition .= ' AND (DATE > "' . $params_dateStart . '" AND DATE <= "' . $params_dateOut . '")';
}else{
    switch ($params_year) {
        case '2019':
            
            $where_condition .= " AND (`DATE` > '2018-12-31' AND `DATE` < '2020-01-01') ";
            break;
        case '2020':
            
            $where_condition .= " AND (DATE > '2019-12-31' AND `DATE` < '2021-01-01') ";
            break;
        case '12020':
            
            $where_condition .= " AND (`DATE` > '2019-12-31' AND `DATE` < '2020-02-01') ";
            break;
        case '22020':
            
            $where_condition .= " AND (`DATE` > '2020-01-31' AND `DATE` < '2020-03-01') ";
            break;
        case '32020':
            
            $where_condition .= " AND (`DATE` > '2020-02-29' AND `DATE` < '2020-04-01') ";
            break;
        case '42020':
            
            $where_condition .= " AND (`DATE` > '2020-03-31' AND `DATE` < '2020-05-01') ";
            break;
        case 'all':
            
            $where_condition .= " AND (`DATE` > '2019-01-01' AND `DATE` < '2029-12-31') ";
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
        
        } elseif (!empty($val_param['search']['value']) && $val_param['data'] == 'DATE') { 
            $where_condition .= " AND DATE_FORMAT(" . $val_param['data'] . ", '%d.%m.%Y') LIKE '%" . $val_param['search']['value'] . "%' ";
        
        } elseif (!empty($val_param['search']['value']) && $val_param['data'] == 'MATERIAL') { 
            $where_condition .= " AND JSON_SEARCH(JSON_EXTRACT(PROBE_JSON, '$**.number_probe'), 'one', '%" . $val_param['search']['value'] . "%') IS NOT NULL";
        } elseif (!empty($val_param['search']['value']) && $val_param['data'] == 'ID') {
            $where_condition .= " AND ID_TZ LIKE '%" . $val_param['search']['value'] . "%' ";
        
        } elseif (!empty($val_param['search']['value'])) {
            $where_condition .= " AND " . $val_param['data'] . " LIKE '%" . $val_param['search']['value'] . "%' ";
        }
    }
}


if (!empty($params['search']['value'])) {
    $where_condition .= " AND (ba_tz.REQUEST_TITLE LIKE '%" . $params['search']['value'] . "%' "; 
    
    $where_condition .= " OR DATE_FORMAT(PROTOCOLS.DATE, '%d.%m.%Y') LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR COMPANY_TITLE LIKE '%" . $params['search']['value'] . "%' "; 
    
    $where_condition .= " OR JSON_SEARCH(JSON_EXTRACT(PROTOCOLS.PROBE_JSON, '$**.number_probe'), 'one', '%" . $params['search']['value'] . "%') IS NOT NULL";
    $where_condition .= " OR PROTOCOLS.VERIFY_NAME_LASTNAME LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR PROTOCOLS.ID_TZ LIKE '%" . $params['search']['value'] . "%' "; 
    $where_condition .= " OR ba_tz.DOGOVOR_TABLE LIKE '%" . $params['search']['value'] . "%' "; 
    $where_condition .= " OR ba_tz.PRICE LIKE '%" . $params['search']['value'] . "%' "; 
    $where_condition .= " OR ba_tz.ACCOUNT LIKE '%" . $params['search']['value'] . "%' "; 
    $where_condition .= " OR ba_tz.DATE_OPLATA LIKE '%" . $params['search']['value'] . "%' "; 
    $where_condition .= " OR ba_tz.ATTESTAT LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR PROTOCOLS.NUMBER_AND_YEAR LIKE '%" . $params['search']['value'] . "%' "; 
    $where_condition .= " OR ba_tz.MANUFACTURER_TITLE LIKE '%" . $params['search']['value'] . "%' "; 
    $where_condition .= " OR ba_tz.USER_HISTORY LIKE '%" . $params['search']['value'] . "%') ";
}

if ($columns[$params['order'][0]['column']] == 'REQUEST_TITLE') {
    
    $sql_query = " SELECT *,CONVERT(substring_index(substring_index(REQUEST_TITLE, '№', -1), '/', 1 ),UNSIGNED INTEGER) AS request,
        PROTOCOLS.ID as protocol_id, ba_tz.ID as ba_tz_id, PROTOCOLS.PDF as protocol_pdf, ba_tz.PDF as ba_tz_pdf,
        PROTOCOLS.PROBE as protocol_probe, ba_tz.PROBE as ba_tz_probe, PROTOCOLS.VERIFY as protocol_verify, ba_tz.VERIFY as ba_tz_verify, 
        JSON_EXTRACT(PROTOCOLS.PROBE_JSON, '$**.number_probe') as number_probe FROM `PROTOCOLS` INNER JOIN `ba_tz`
        ON `PROTOCOLS`.ID_TZ = `ba_tz`.ID";

} else if ($columns[$params['order'][0]['column']] == 'NUMBER_AND_YEAR') {
    
    $sql_query = " SELECT *,CONVERT(substring_index(PROTOCOLS.NUMBER_AND_YEAR, '/', 1 ),UNSIGNED INTEGER) AS p, 
        PROTOCOLS.ID as protocol_id, ba_tz.ID as ba_tz_id, PROTOCOLS.PDF as protocol_pdf, ba_tz.PDF as ba_tz_pdf,
        PROTOCOLS.PROBE as protocol_probe, ba_tz.PROBE as ba_tz_probe, PROTOCOLS.VERIFY as protocol_verify, ba_tz.VERIFY as ba_tz_verify,
        JSON_EXTRACT(PROTOCOLS.PROBE_JSON, '$**.number_probe') as number_probe FROM `PROTOCOLS` INNER JOIN `ba_tz`
        ON `PROTOCOLS`.ID_TZ = `ba_tz`.ID";

} else {
    
    $sql_query = " SELECT *, PROTOCOLS.ID as protocol_id, ba_tz.ID as ba_tz_id, PROTOCOLS.PDF as protocol_pdf, ba_tz.PDF as ba_tz_pdf,
        PROTOCOLS.PROBE as protocol_probe, ba_tz.PROBE as ba_tz_probe, PROTOCOLS.VERIFY as protocol_verify, ba_tz.VERIFY as ba_tz_verify,
        JSON_EXTRACT(PROTOCOLS.PROBE_JSON, '$**.number_probe') as number_probe FROM `PROTOCOLS` INNER JOIN `ba_tz`
        ON `PROTOCOLS`.ID_TZ = `ba_tz`.ID";
}

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

if ($columns[$params['order'][0]['column']] == 'REQUEST_TITLE') {
    
    $column_name = "request";
    
} else if ($columns[$params['order'][0]['column']] == 'NUMBER_AND_YEAR') {
    $column_name = "p";
} else if ($columns[$params['order'][0]['column']] == 'ID') {
    $column_name = "PROTOCOLS.ID_TZ";
} else if ($columns[$params['order'][0]['column']] == 'MATERIAL') {
    $column_name = "number_probe";
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
    
    $protocolId = !empty($row['protocol_id']) ? (int)$row['protocol_id'] : 0;
    $numP = !empty($row['NUMBER']) ? $row['NUMBER'] : ''; 
    $typeTz = !empty($row['PROTOCOL_TYPE']) ? $row['PROTOCOL_TYPE'] : '';
    $protocolPrice = (float)$row['PROTOCOL_PRICE'] ?? 0;
    $numberAndYear = !empty($row['NUMBER_AND_YEAR']) ? $row['NUMBER_AND_YEAR'] : 'Номер не присвоен'; 
    $idTz = !empty($row['ba_tz_id']) ? $row['ba_tz_id'] : 0; 
    $yearProtocol = !empty($row['DATE']) ? date("Y", strtotime($row['DATE'])) : ''; 
    $nameProtocolPdf = !empty($row['protocol_pdf']) ? $row['protocol_pdf'] : ''; 
    $idDeal = !empty($row['ID_Z']) ? $row['ID_Z'] : 0;
    $arrTz = !empty($row['TZ']) ? unserialize($row['TZ']) : [];
    
    $protocolProbe = !empty($row['PROBE_JSON']) ? json_decode($row['PROBE_JSON'], true) : [];

    $protocolVerify = !empty($row['protocol_verify']) ? unserialize($row['protocol_verify']) : [];

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


    
    $data[$i]['STAGE_NUMBER'] = isset($row['STAGE_NUMBER']) ? $row['STAGE_NUMBER'] : null;

    $data[$i]['COLOR'] = $color;
    
    $data[$i]['HOVER'] = $title;

    $data[$i]['REQUEST_TITLE'] = "<a href='/request_card.php?ID=". $row['ID_Z'] ."&ACT_NUM=".$row['ACT_NUM']."' target='_blank'>".$row['REQUEST_TITLE']."</a>";


    
    $data[$i]['DATE'] = !empty($row['DATE']) ? ruDate($row['DATE']) : '';

    $data[$i]['COMPANY_TITLE'] = !empty($row['COMPANY_TITLE']) ? $row['COMPANY_TITLE'] : '';

    $deadlineDate = (!empty($row['DEADLINE_TABLE']) ? date('d.m.Y', strtotime($row['DEADLINE_TABLE'])) : '');
    $color_date='';
    if (date('Y-m-d') > date("Y-m-d", strtotime($deadlineDate))&&$row['STAGE_ID']!='2'&&$row['STAGE_ID']!='4'&&$row['STAGE_ID']!='WON'&&$row['STAGE_ID']!='LOSE'&&$row['STAGE_ID']!='5'&&$row['STAGE_ID']!='6'&&$row['STAGE_ID']!='7'&&$row['STAGE_ID']!='8'&&$row['STAGE_ID']!='9') $color_date='red';


    $data[$i]['COLOR_DATE'] = $color_date;

    $arrayMaterialsUsed = [];
    foreach ($arrTz as $keyTz => $valTz){
        if (!empty($protocolProbe[$keyTz])) {
            $arrNameMaterial = !empty($valTz['mater']) ? explode(',', $valTz['mater']) : '';
            if (!empty($arrNameMaterial[0])) {
                $arrayMaterialsUsed[] = $arrNameMaterial[0];
            }
        }
    }

	$attestat = $DB->Query("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ` = {$row['ID']}")->Fetch();

    $materialsUsed = !empty($arrayMaterialsUsed) ? implode(', ', $arrayMaterialsUsed) : '';

    $data[$i]['MATERIAL'] = $materialsUsed;

    
    $assigneds = !empty($row['VERIFY_NAME_LASTNAME']) ? $row['VERIFY_NAME_LASTNAME'] : '';

    $data[$i]['VERIFY_NAME_LASTNAME'] = $assigneds;

    //$data[$i]['ATTESTAT'] = !empty($row['ATTESTAT_IN_PROTOCOL']) ? 'A' : '';
//    $data[$i]['ATTESTAT'] = !empty($row['ATTESTAT']) ? 'A' : '';
	$data[$i]['ATTESTAT'] = ($attestat['IN_ATTESTAT_DIAPASON'] == 1) ? 'A' : '';

    $tz = !empty($row['TZ']) ? $idTz : '';

    $data[$i]['ID'] = "<a href='/tz_show.php?ID=".$idTz."' target='_blank'>". $tz ."</a>";

    $data[$i]['DOGOVOR_TABLE'] = $row['DOGOVOR_TABLE'] ? $row['DOGOVOR_TABLE'] : '';

    //$data[$i]['PRICE'] = $row['PRICE'] ? $row['PRICE'] : '';
    $data[$i]['PRICE'] = $protocolPrice;

    $data[$i]['COLOR_PRICE'] = ($row['PRICE']&&($row['OPLATA'] >= $row['PRICE'])||!$row['PRICE']) ? '#fff' : '#FFC0CB';

    $data[$i]['DATE_OPLATA'] = $row['DATE_OPLATA'];

    $result_isp = '';
   
    if (empty($row['protocol_pdf'])) {
        
        $str_result_isp = ($idTz && $row['ACT_NUM'])? 'Открыть' : '';
        
        $result_isp = "<a href='/results_isp.php?ID=" . $idDeal . "&ID_P=" . $protocolId . "' target='_blank'>" . $str_result_isp . "</a>";
    } else {
        $result_isp = "Выдан вне ЛИС";
    }
    
    $data[$i]['RESULT_ISP'] = $result_isp;

    $protocol = null;
    if(empty($row['PROTOCOL_OUTSIDE_LIS']))
    {

        if(!empty($row['ACTUAL_VERSION']))
        {
           
            $directory = !empty($idTz) && !empty($yearProtocol) && !empty($protocolId) ? $idTz . $yearProtocol . '/' . $protocolId : '';
           
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

        $protocol = !empty($row['protocol_pdf']) ? "<a href='/pdf/" . $protocolId . "/" . $nameProtocolPdf . "' target='_blank'>PDF</a>" : '';

    }

    $resultNumPTable = !empty($numberAndYear) && in_array($typeTz, [33, 34, 35, 36, 37, 38, 39]) ? $numberAndYear . ' ' . 'У' : $numberAndYear;

    $data[$i]['NUMBER_AND_YEAR'] = !empty($resultNumPTable) ? $resultNumPTable : $numberAndYear;

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
