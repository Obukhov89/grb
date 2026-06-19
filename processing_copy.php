<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DataStorage.php');

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
$db = new CDB();
$newResult = new NewResult();

$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;


$columns = array(
    0 => 'STAGE_NUMBER',
    1 => 'REQUEST_TITLE',
    2 => 'DATE_CREATE_TIMESTAMP',
    3 => 'COMPANY_ID',
    4 => 'DEADLINE_TABLE',
    5 => 'ACCOUNT',
    6 => 'MATERIAL',
    7 => 'ASSIGNED',
    8 => 'NUM_ACT_TABLE',
    9 => 'ID',
    10 => 'DATE_CREATE_TIMESTAMP',
    11 => 'NUM_P_TABLE',
    12 => 'USER_HISTORY'
);


	$user_id = !empty($USER->GetID()) ? (int)$USER->GetID() : '';


$where_condition = $sqlTot = $sqlRec = "";

$where_condition = " WHERE `TYPE_ID` != '3' AND (`ID_Z` > 4318 OR `ID_Z` < 4295) ";


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
    $params_dateOut = date('Y-m-d', strtotime($params['dateOut']) + 86400);
    $where_condition .= ' AND (DATE_CREATE_TIMESTAMP > "'. $params_dateStart .'" AND DATE_CREATE_TIMESTAMP < "'. $params_dateOut .'") ';
}else{

    switch ($params_year) {
        case '2019':
            $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2018-12-31' AND `DATE_CREATE_TIMESTAMP` < '2020-01-01') ";
            break;
        case '2020':
            $where_condition .= " AND (DATE_CREATE_TIMESTAMP > '2019-12-31' AND `DATE_CREATE_TIMESTAMP` < '2021-01-01') ";
            break;
        case '12020':
            $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2019-12-31' AND `DATE_CREATE_TIMESTAMP` < '2020-02-01') ";
            break;
        case '22020':
            $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-01-31' AND `DATE_CREATE_TIMESTAMP` < '2020-03-01') ";
            break;
        case '32020':
            $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-02-29' AND `DATE_CREATE_TIMESTAMP` < '2020-04-01') ";
            break;
        case '42020':
            $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-03-31' AND `DATE_CREATE_TIMESTAMP` < '2020-05-01') ";
            break;
        case '102020':
            $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-10-01' AND `DATE_CREATE_TIMESTAMP` < '2020-11-01') ";
            break;
        case 'all':
            $where_condition .= " AND (`DATE_CREATE_TIMESTAMP` > '2019-01-01' AND `DATE_CREATE_TIMESTAMP` < '2029-12-31') ";
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
    $where_condition .= " OR COMPANY_ID LIKE '%" . $params['search']['value'] . "%' ";
	$where_condition .= " OR DEADLINE_TABLE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR MATERIAL LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR ASSIGNED LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR NUM_ACT_TABLE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR ID LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR DOGOVOR_TABLE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR PRICE LIKE '%" . $params['search']['value'] . "%' ";
	$where_condition .= " OR ACCOUNT LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR NUM_P_TABLE LIKE '%" . $params['search']['value'] . "%' ";
	$where_condition .= " OR MANUFACTURER_TITLE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR USER_HISTORY LIKE '%" . $params['search']['value'] . "%') ";
}

if ($columns[$params['order'][0]['column']] == 'REQUEST_TITLE') {
    
    $sql_query = " SELECT *,CONVERT(substring_index(substring_index(REQUEST_TITLE, '№', -1), '/', 1 ),UNSIGNED INTEGER) AS request FROM `ba_tz` ";
	
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
    
    $column_name = "request";
	
} else if ($columns[$params['order'][0]['column']] == 'NUM_ACT_TABLE') {
    $column_name = "act";
} else if ($columns[$params['order'][0]['column']] == 'NUM_P_TABLE') {
    $column_name = "p";
} else if ($columns[$params['order'][0]['column']] == 'DATE_OPLATA') { 
    
    $column_name = 'CAST(CONCAT(SUBSTRING(' . $columns[$params['order'][0]['column']] . ',7,4), "-", SUBSTRING(' . $columns[$params['order'][0]['column']] . ',4,2), "-", SUBSTRING(' . $columns[$params['order'][0]['column']] . ',1,2)) AS DATE)';
} else if ($columns[$params['order'][0]['column']] == 'DEADLINE_TABLE') { 
    $column_name = 'CAST(CONCAT(SUBSTRING(' . $columns[$params['order'][0]['column']] . ',7,4), "-", SUBSTRING(' . $columns[$params['order'][0]['column']] . ',4,2), "-", SUBSTRING(' . $columns[$params['order'][0]['column']] . ',1,2)) AS DATE)';
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
    $idDeal = !empty($row['ID_Z']) ? (int)$row['ID_Z'] : 0;
    $idTz = !empty($row['ID']) ? (int)$row['ID'] : 0;

    if (CModule::IncludeModule('crm')) {
        
        $deal = CCrmDeal::GetByID(!empty($row['ID_Z']) ? $row['ID_Z'] : '');


    }

	$select_check_tz = null;
	if (!empty($row['ID'])) {
		
		$select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . (int)$row['ID'] . " AND `date_return` IS NULL");
	}

	$selected_rows_count = !empty($select_check_tz->SelectedRowsCount()) ? $select_check_tz->SelectedRowsCount() : ''; 

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

    
    $data[$i]['COMPANY_ID'] = 'ID' . ' ' . $deal['COMPANY_ID'];


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

//    $data[$i]['DOGOVOR_TABLE'] = $row['DOGOVOR_TABLE'] ? $row['DOGOVOR_TABLE'] : '';
//
//		$data[$i]['PRICE'] = $row['PRICE'] ? $row['PRICE'] : '';

//    $data[$i]['COLOR_PRICE'] = ($row['PRICE']&&($row['OPLATA'] >= $row['PRICE'])||!$row['PRICE']) ? '#fff' : '#FFC0CB';
	
//    $data[$i]['DATE_OPLATA'] = $row['DATE_OPLATA'];
	
	//if (!empty($idDeal) && (in_array((int)$idDeal, $newResult->arrIdDeal) || (int)$idDeal > $newResult->idDealВefore)) {
		
	//}

    $result_isp = '';
    if (!$row['PDF']) {
        $str_result_isp = ($row['ID']&&$row['ACT_NUM'])? 'Открыть' : '';
        //$result_isp = "<a href='/results_isp.php?ID=".$row['ID_Z']."' target='_blank'>".$str_result_isp."</a>";
		if (!empty($idDeal) && (in_array((int)$idDeal, $newResult->arrIdDeal) || (int)$idDeal > $newResult->idDealВefore)) {
			$selectedProtocolData = null;
			if (!empty($idTz)) {
				$selectedProtocolData = $db->select("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}", [$idTz]);
			}
			$idFistProtocol = !empty($selectedProtocolData[0]['ID']) ? $selectedProtocolData[0]['ID'] : 0;
			
			$result_isp = "<a href='/results_isp.php?ID=".$row['ID_Z']."&ID_P=".$idFistProtocol."' target='_blank'>".$str_result_isp."</a>";
		} else {
			$result_isp = "<a href='/results_isp.php?ID=".$row['ID_Z']."' target='_blank'>".$str_result_isp."</a>";
		}
		
    } else {
        $result_isp = "Выдан вне ЛИС";
    }
    
    $data[$i]['RESULT_ISP'] = $result_isp;

    $arrayLinkProtocol = [];
    $arrayLinkProtocolPdf = [];

    $protocol = null;
    //if (!empty($idDeal) && in_array((int)$idDeal, $newResult->arrIdDeal) || !empty($idDeal) && $idDeal === 5138  || !empty($idDeal) && $idDeal === 5479 || !empty($idDeal) && $idDeal === 5682 || !empty($idDeal) && $idDeal === 5697 || !empty($idDeal) && $idDeal === 5893) {
    if (!empty($idDeal) && (in_array((int)$idDeal, $newResult->arrIdDeal) || (int)$idDeal > $newResult->idDealВefore)) {
        $selectedProtocolData = $db->select("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}", [$idTz]);

        $countSelectedProtolols = !empty($selectedProtocolData) ? count($selectedProtocolData) : 0;

        if (!empty($countSelectedProtolols)) {

            foreach ($selectedProtocolData as $keyProtocolData => $valProtocolData) {
                $protocolId = !empty($valProtocolData['ID']) ? (int)$valProtocolData['ID'] : 0;
                $numberAndYearProtocol = !empty($valProtocolData['NUMBER_AND_YEAR']) ? $valProtocolData['NUMBER_AND_YEAR'] : '';
                $actualVersionProtocol = !empty($valProtocolData['ACTUAL_VERSION']) ? unserialize($valProtocolData['ACTUAL_VERSION']) : '';
                $protocolYear = !empty($valProtocolData['DATE']) ? dateObjectFormat($valProtocolData['DATE'], "Y") : '';
                $protocolPdf = !empty($valProtocolData['PDF']) ? $valProtocolData['PDF'] : '';
                $protocolOutsideLis = !empty($valProtocolData['PROTOCOL_OUTSIDE_LIS']) ? $valProtocolData['PROTOCOL_OUTSIDE_LIS'] : '';


                if($actualVersionProtocol)
                {
//                    $dir  = '/protocol_generator/archive/' . $row['ID'] . $protocolYear . '/' . $protocolId . '/';
//                    $arrayLinkProtocol[] = "<a href='" . $dir . $actualVersionProtocol . ".docx?1&1' target='_blank' class='protocolLink'>" . $numberAndYearProtocol . "</a>";
//                }
                $dir = 'protocol_generator/archive/' . $row['ID'] . $protocolYear . '/' . $protocolId. '/';
                $skip = array('.', '..','signed.docx', 'forsign.docx','qrNEW.png');
                $files = scandir($dir);
                foreach ($files as $file) {
                    if (strpos($file, '.pdf') !== false) {
                        $arrayLinkProtocol[] = "<a href='$dir$file'>$numberAndYearProtocol</a><br><br>";
                    }
//                        $arrayLinkProtocol[] = "<a href='$dir$file'>$numberAndYearProtocol</a><br><br>";
                  }
                }
                if (!empty($protocolOutsideLis) && !empty($protocolPdf)) {
                    $arrayLinkProtocolPdf[] = "<a href='/pdf/" . $protocolId . "/" . $protocolPdf . "' target='_blank' class='protocolLink'>" . $numberAndYearProtocol . "</a>";
                }

            }


            $arrayLinks = array_merge($arrayLinkProtocol, $arrayLinkProtocolPdf);
            $protocol .= !empty($arrayLinks) ? implode(' ', $arrayLinks) : '';
        }

    } else {
        if(!$row['NO_BITRIX'])
        {


            if($row['ACTUAL_VER'])
            {
                
                $data_protocol = $row['RESULTS'] ? $row['NUM_P_TABLE'] : '';
                $dir  = '/protocol_generator/archive/'.$row['ID'].date("Y", strtotime($row['DATE_ACT'])).'/';
                $protocol = "<a href='".$dir.$row['ACTUAL_VER'].".docx?1&1' target='_blank'>$data_protocol</a>";
            }
        }
        else
        {
            
            $data_protocol = $row['PDF'] ? $row['NUM_P_TABLE'] : '';
            $protocol = "<a href='/pdf/" . $row['PDF'] . "' target='_blank'>" . $data_protocol . "</a>";
        }
    }

    $data[$i]['NUM_P_TABLE'] = $protocol;

	
//	$data[$i]['MANUFACTURER_TITLE'] = !empty($row['MANUFACTURER_TITLE']) ? $row['MANUFACTURER_TITLE'] : '';
	

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
