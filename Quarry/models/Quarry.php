<?php
namespace Quarry\models;
require_once($_SERVER['DOCUMENT_ROOT'] . '/Helper/DBDateFormat.php'); //Подключаем класс с методо преобразования даты в нужный формат
use Helper\DBHelper;


class Quarry {

    protected static $columns = [
        0 => 'STAGE_NUMBER',
        1 => 'REQUEST_TITLE',
        2 => 'DATE_CREATE_TIMESTAMP',
        3 => 'COMPANY_TITLE',
        4 => 'DEADLINE_TABLE',
        5 => 'MATERIAL',
        6 => 'ASSIGNED',
        7 => 'NAME',
        8 => 'NUM_ACT_TABLE',
        9 => 'ID',
        10 => 'DOGOVOR_TABLE',
        11 => 'PRICE',
        12 => 'NUM_P_TABLE',
        ];
    protected $db;
    protected $params;
    protected $totalRecords;
    protected $queryRecords;
    protected $data = [];

    function __construct($db)
    {
        $this->db = $db;
    }

    protected function queryDataTable(array $params = []) {
        $this->params = $params;

        $whereCondition = $sqlTot = $sqlRec = "";
        $whereCondition = " WHERE `TYPE_ID` != '3' AND (`ID_Z` > 4318 OR `ID_Z` < 4295) ";

        $paramsStage = $this->params['stage'] ?? '';

        switch ($paramsStage) {
            case '1':
                $whereCondition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING') AND IF(ACT_NUM, TRUE, FALSE) = FALSE ";
                break;
            case '2':
                $whereCondition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE') AND IF(ACT_NUM, TRUE, FALSE) = TRUE ";
                break;
            case '3':
                $whereCondition .= " AND `STAGE_ID`='1' ";
                break;
            case '4':
                $whereCondition .= " AND STAGE_ID in ('2', '3', '4') ";
                break;
            case '5':
                $whereCondition .= " AND STAGE_ID in ('5', '6', '7', '8', '9', 'LOSE') ";
                break;
            case '6':
                $whereCondition .= " AND `PRICE` IS NOT NULL AND `PRICE` > 0 AND (`OPLATA` = 0 || `OPLATA` IS NULL) AND (`STAGE_ID` NOT IN ('LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13') ) ";
                break;
            case '7':
                $whereCondition .= " AND (`OPLATA` < `PRICE`) AND (`OPLATA` > 0) ";
                break;
            case '8':
                $whereCondition .= " AND (`OPLATA` > `PRICE`) ";
                break;
            case '9':
                $whereCondition .= " AND (`OPLATA` = `PRICE`) ";
                break;
            case '10':
                $whereCondition .= " AND STAGE_ID in ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON') AND IF(ACT_NUM, TRUE, FALSE) = TRUE";
                break;
            case '11':
                $whereCondition .= " AND `STAGE_ID`='WON' ";
                break;
            case 'all':
                break;
        }

        $paramsYear = $this->params ?? '';

        if(!empty($this->params['dateStart']) && !empty($this->params['dateOut'])) {
            $paramsDateStart = $this->params['dateStart'];
            $paramsDateOut = date('Y-m-d', strtotime($this->params['dateOut']) + 86400);
            $whereCondition .= ' AND (DATE_CREATE_TIMESTAMP > "'. $paramsDateStart .'" AND DATE_CREATE_TIMESTAMP < "'. $paramsDateOut .'") ';
        }else{

            switch ($paramsYear) {
                case '2019':
                    $whereCondition .= " AND (`DATE_CREATE_TIMESTAMP` > '2018-12-31' AND `DATE_CREATE_TIMESTAMP` < '2020-01-01') ";
                    break;
                case '2020':
                    $whereCondition .= " AND (DATE_CREATE_TIMESTAMP > '2019-12-31' AND `DATE_CREATE_TIMESTAMP` < '2021-01-01') ";
                    break;
                case '12020':
                    $whereCondition .= " AND (`DATE_CREATE_TIMESTAMP` > '2019-12-31' AND `DATE_CREATE_TIMESTAMP` < '2020-02-01') ";
                    break;
                case '22020':
                    $whereCondition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-01-31' AND `DATE_CREATE_TIMESTAMP` < '2020-03-01') ";
                    break;
                case '32020':
                    $whereCondition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-02-29' AND `DATE_CREATE_TIMESTAMP` < '2020-04-01') ";
                    break;
                case '42020':
                    $whereCondition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-03-31' AND `DATE_CREATE_TIMESTAMP` < '2020-05-01') ";
                    break;
                case '102020':
                    $whereCondition .= " AND (`DATE_CREATE_TIMESTAMP` > '2020-10-01' AND `DATE_CREATE_TIMESTAMP` < '2020-11-01') ";
                    break;
                case 'all':
                    $whereCondition .= " AND (`DATE_CREATE_TIMESTAMP` > '2019-01-01' AND `DATE_CREATE_TIMESTAMP` < '2029-12-31') ";
                    break;

            }
        }

        $paramsLaba = $this->params['laba'] ?? '';

        switch ($paramsLaba) {
            case '1':
                $idDep = 54;
                $whereCondition .= " AND (`LABA_ID` LIKE '" . $idDep ."' OR `LABA_ID` LIKE '" . $idDep .",%' OR `LABA_ID` LIKE '%," . $idDep . "' OR `LABA_ID` LIKE '%," . $idDep . ",%') ";
                break;
            case '2':
                $idDep = 57;
                $whereCondition .= " AND (`LABA_ID` LIKE '" . $idDep ."' OR `LABA_ID` LIKE '" . $idDep .",%' OR `LABA_ID` LIKE '%," . $idDep . "' OR `LABA_ID` LIKE '%," . $idDep . ",%') ";
                break;
            case '3':
                $idDep = 56;
                $whereCondition .= " AND (`LABA_ID` LIKE '" . $idDep ."' OR `LABA_ID` LIKE '" . $idDep .",%' OR `LABA_ID` LIKE '%," . $idDep . "' OR `LABA_ID` LIKE '%," . $idDep . ",%') ";
                break;
            case '4':
                $idDep = 55;
                $whereCondition .= " AND (`LABA_ID` LIKE '" . $idDep ."' OR `LABA_ID` LIKE '" . $idDep .",%' OR `LABA_ID` LIKE '%," . $idDep . "' OR `LABA_ID` LIKE '%," . $idDep . ",%') ";
                break;
            case '5':
                $idDep = 58;
                $whereCondition .= " AND (`LABA_ID` LIKE '" . $idDep ."' OR `LABA_ID` LIKE '" . $idDep .",%' OR `LABA_ID` LIKE '%," . $idDep . "' OR `LABA_ID` LIKE '%," . $idDep . ",%') ";
                break;
            case '6':
                $idDep = 53;
                $whereCondition .= " AND (`LABA_ID` LIKE '" . $idDep ."' OR `LABA_ID` LIKE '" . $idDep .",%' OR `LABA_ID` LIKE '%," . $idDep . "' OR `LABA_ID` LIKE '%," . $idDep . ",%') ";
                break;

        }

        if (!empty($this->params['columns'])) {
            foreach ($this->params['columns'] as $keyParam => $valParam) {

                if (!empty($valParam['search']['value']) && !empty($valParam['data']) && $valParam['data'] == 'STAGE_NUMBER' && $valParam['search']['value'] == 'checked') {
                    $whereCondition .= " AND `MANUFACTURER_TITLE` IS NOT NULL AND `MANUFACTURER_TITLE`!='' ";
                } elseif (!empty($valParam['data']) && !empty($valParam['search']['value'])) {
                    $whereCondition .= " AND " . $valParam['data'] . " LIKE '%" . $valParam['search']['value'] . "%' ";
                }
            }
        }

        if (!empty($this->params['search']['value'])) {
            $whereCondition .= " AND (REQUEST_TITLE LIKE '%" . $this->params['search']['value'] . "%' ";
            $whereCondition .= " OR DATE_CREATE LIKE '%" . $this->params['search']['value'] . "%' ";
            $whereCondition .= " OR COMPANY_TITLE LIKE '%" . $this->params['search']['value'] . "%' ";
            $whereCondition .= " OR DEADLINE_TABLE LIKE '%" . $this->params['search']['value'] . "%' ";
            $whereCondition .= " OR MATERIAL LIKE '%" . $this->params['search']['value'] . "%' ";
            $whereCondition .= " OR ASSIGNED LIKE '%" . $this->params['search']['value'] . "%' ";
            $whereCondition .= " OR NAME LIKE '%" . $this->params['search']['value'] . "%' ";
            $whereCondition .= " OR NUM_ACT_TABLE LIKE '%" . $this->params['search']['value'] . "%' ";
            $whereCondition .= " OR ID LIKE '%" . $this->params['search']['value'] . "%' ";
            $whereCondition .= " OR DOGOVOR_TABLE LIKE '%" . $this->params['search']['value'] . "%' ";
            $whereCondition .= " OR PRICE LIKE '%" . $this->params['search']['value'] . "%' ";
            $whereCondition .= " OR NUM_P_TABLE LIKE '%" . $this->params['search']['value'] . "%') ";
        }

        if ($this->params && self::$columns[$this->params['order'][0]['column']] == 'REQUEST_TITLE') {
            $sqlQuery = "SELECT *,CONVERT(substring_index(substring_index(REQUEST_TITLE, '№', -1), '/', 1 ),UNSIGNED INTEGER) AS request, ba_tz.ID as ba_tz_id, Quarry.ID as quarry_id FROM `ba_tz` LEFT JOIN `Quarry` ON ba_tz.QUARRY_ID=Quarry.ID";
        } else if ($this->params && self::$columns[$this->params['order'][0]['column']] == 'NUM_ACT_TABLE') {
            $sqlQuery = "SELECT *,CONVERT(substring_index(NUM_ACT_TABLE, '/', 1 ),UNSIGNED INTEGER) AS act, ba_tz.ID as ba_tz_id, Quarry.ID as quarry_id FROM `ba_tz` LEFT JOIN `Quarry` ON ba_tz.QUARRY_ID=Quarry.ID";
        } else if ($this->params && self::$columns[$this->params['order'][0]['column']] == 'NUM_P_TABLE') {
            $sqlQuery = "SELECT *,CONVERT(substring_index(NUM_P_TABLE, '/', 1 ),UNSIGNED INTEGER) AS p, ba_tz.ID as ba_tz_id, Quarry.ID as quarry_id FROM `ba_tz` LEFT JOIN `Quarry` ON ba_tz.QUARRY_ID=Quarry.ID";
        } else {
            $sqlQuery = "SELECT *, ba_tz.ID as ba_tz_id, Quarry.ID as quarry_id FROM `ba_tz` LEFT JOIN `Quarry` ON ba_tz.QUARRY_ID=Quarry.ID";
        }

        $sqlTot .= $sqlQuery;
        $sqlRec .= $sqlQuery;

        if (isset($whereCondition) && $whereCondition != '') {
            $sqlTot .= $whereCondition;
            $sqlRec .= $whereCondition;
        }

        if ($this->params && self::$columns[$params['order'][0]['column']] == 'REQUEST_TITLE') {
            $columnName = "request";
        } else if ($this->params && self::$columns[$params['order'][0]['column']] == 'NUM_ACT_TABLE') {
            $columnName = "act";
        } else if ($this->params && self::$columns[$params['order'][0]['column']] == 'NUM_P_TABLE') {
            $columnName = "p";
        } else if ($this->params && self::$columns[$params['order'][0]['column']] == 'DATE_OPLATA') {
            $columnName = 'CAST(CONCAT(SUBSTRING(' . self::$columns[$params['order'][0]['column']] . ',7,4), "-", SUBSTRING(' . self::$columns[$params['order'][0]['column']] . ',4,2), "-", SUBSTRING(' . self::$columns[$params['order'][0]['column']] . ',1,2)) AS DATE)';
        } else if ($this->params && self::$columns[$params['order'][0]['column']] == 'DEADLINE_TABLE') {
            $columnName = 'CAST(CONCAT(SUBSTRING(' . self::$columns[$params['order'][0]['column']] . ',7,4), "-", SUBSTRING(' . self::$columns[$params['order'][0]['column']] . ',4,2), "-", SUBSTRING(' . self::$columns[$params['order'][0]['column']] . ',1,2)) AS DATE)';
        } else {
            $columnName = self::$columns[$params['order'][0]['column']];
        }

        if (!empty($this->params['length']) && $this->params['length'] == "-1") {
            $sqlRec .= " ORDER BY " . $columnName . " " . $this->params['order'][0]['dir'] . " ";
        } elseif ($this->params) {
            $sqlRec .= " ORDER BY " . $columnName . " " . $this->params['order'][0]['dir'] . " LIMIT " . $this->params['start'] . " ," . $this->params['length'] . " ";
        }

        $queryTot = $this->db->select($sqlTot);
        $this->totalRecords = count($queryTot);

        $this->queryRecords = $this->db->select($sqlRec);

    }

    protected function collectingData($cDeal, $newResult) {
        $i = 0;

        foreach ($this->queryRecords as $key => $row) {

            $idDeal = $row['ID_Z'] ?? 0;
            $idTz = $row['ba_tz_id'] ?? 0;
            $color='';
            $title='';

            $deal = $cDeal->GetByID($idDeal);

            switch ($row['STAGE_ID']) {
                case 'NEW':
                case 'PREPARATION':
                case 'PREPAYMENT_INVOICE':
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

            if ($row['ACT_NUM'] && ($deal['STAGE_ID'] === 'NEW' || $deal['STAGE_ID'] === 'PREPARATION' ||
                    $deal['STAGE_ID'] === 'PREPAYMENT_INVOICE' || $deal['STAGE_ID'] === 'EXECUTING')) {
                $color='yellow';
                $title='Протокол не выдан. Испытания еще не проводились. Пробы получены';
            }

            if ($row['PRICE'] && $row['RESULTS'] && (!$row['OPLATA'] || $row['OPLATA'] < $row['PRICE'])
                && $deal['STAGE_ID'] === '2') {
                $color='#9b2d30';
                $title='Испытания в лаборатории завершены. Оплата не поступила.';
            }

            $this->data[$i]['STAGE_NUMBER'] = $row['STAGE_NUMBER'];
            $this->data[$i]['COLOR'] = $color;
            $this->data[$i]['HOVER'] = $title;

            $this->data[$i]['REQUEST_TITLE'] = "<a href='/request_card.php?ID={$idDeal}&ACT_NUM={$row['ACT_NUM']}' target='_blank'>{$row['REQUEST_TITLE']}</a>";

            $this->data[$i]['DATE_CREATE'] = $row['DATE_CREATE'];

            $this->data[$i]['COMPANY_TITLE'] = $row['COMPANY_TITLE'];

            $deadlineDate = (!empty($row['DEADLINE']) ? date('d.m.Y', strtotime($row['DEADLINE'])) : '');
            $colorDate = '';
            if (CURRENT_DATE > $row['DEADLINE'] &&
                $row['STAGE_ID'] !== ('2' || '4' || 'WON' || 'LOSE' || '5' || '6' || '7' || '8' || '9')) {
                $colorDate='red';
            }

            $this->data[$i]['DEADLINE_TABLE'] = $deal['TYPE_ID'] !== 'COMPLEX' ? "<span style='color: ".$colorDate."'>" . $deadlineDate . "</span>" : '';

            $this->data[$i]['MATERIAL'] = $row['MATERIAL'];

            $assigned = !empty($row['ASSIGNED']) ? strtok($row['ASSIGNED'], ",") : '';

            $assignedStrong = "<strong>" . $assigned . "</strong>";

            $this->data[$i]['ASSIGNED'] = str_replace($assigned, $assignedStrong, $row['ASSIGNED']);

            $selectedQuarryData = $this->db->select("SELECT * FROM `Quarry` WHERE `ID`={?}", [$row['QUARRY_ID']]);

            $this->data[$i]['NAME'] = $selectedQuarryData[0]['NAME'] ?? '';

            $this->data[$i]['NUM_ACT_TABLE'] = !empty($row['NUM_ACT_TABLE']) ?
                "<span><a href='/probe.php?ID=" . $idDeal . "&1' target='_blank'>" . $row['NUM_ACT_TABLE'] . "</a></span>" : '';

            $this->data[$i]['ID'] = !empty($row['TZ']) ? "<a href='/tz_show.php?ID=" . $idTz . "' target='_blank'>". $idTz ."</a>" : '';

            $this->data[$i]['DOGOVOR_TABLE'] = $row['DOGOVOR_TABLE'];

            $this->data[$i]['PRICE'] = $row['PRICE'];

            $this->data[$i]['COLOR_PRICE'] = ($row['PRICE'] && ($row['OPLATA'] >= $row['PRICE']) || !$row['PRICE']) ? '#fff' : '#FFC0CB';

            $arrayLinkProtocol = [];
            $arrayLinkProtocolPdf = [];
            $protocol = '';
            if (!empty($idDeal) && (in_array((int)$idDeal, $newResult->arrIdDeal) || (int)$idDeal > $newResult->idDealВefore)) {

                $selectedProtocolData = $this->db->select("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}", [$idTz]);

                foreach ($selectedProtocolData as $keyProtocolData => $valProtocolData) {
                    $actualVersionProtocol = !empty($valProtocolData['ACTUAL_VERSION']) ? unserialize($valProtocolData['ACTUAL_VERSION']) : '';
                    //$protocolYear = !empty($valProtocolData['DATE']) ? $this->dateObjectFormat($valProtocolData['DATE'], "Y") : '';
                    $protocolYear = !empty($valProtocolData['DATE']) ? DBHelper::dateFormat($valProtocolData['DATE'], "Y") : '';


                    if($actualVersionProtocol)
                    {
                        $dir  = '/protocol_generator/archive/' . $idTz . $protocolYear . '/' . $valProtocolData['ID'] . '/';
                        $arrayLinkProtocol[] = "<a class='protocolLink' href='" . $dir . $actualVersionProtocol . ".docx?1&1' target='_blank'>" . $valProtocolData['NUMBER_AND_YEAR'] . "</a>";
                    }


                    if (!empty($valProtocolData['PROTOCOL_OUTSIDE_LIS']) && !empty($valProtocolData['PDF'])) {
                        $arrayLinkProtocolPdf[] = "<a class='protocolLink' href='/pdf/" . $valProtocolData['ID'] . "/" . $valProtocolData['PDF'] . "' target='_blank'>" . $valProtocolData['NUMBER_AND_YEAR'] . "</a>";
                    }

                }


                $arrayLinks = array_merge($arrayLinkProtocol, $arrayLinkProtocolPdf);
                $protocol .= !empty($arrayLinks) ? implode(', ', $arrayLinks) : '';

            } else if(!$row['NO_BITRIX'] && $row['ACTUAL_VER']) {

                $dataProtocol = $row['RESULTS'] ? $row['NUM_P_TABLE'] : '';
                $dir  = '/protocol_generator/archive/' . $idTz . date("Y", strtotime($row['DATE_ACT'])) . '/';
                $protocol = "<a href='" . $dir.$row['ACTUAL_VER'] . ".docx?1&1' target='_blank'>$dataProtocol</a>";

            }
            else if($row['NO_BITRIX'] && $row['PDF']) {

                $protocol = "<a href='/pdf/" . $row['PDF'] . "' target='_blank'>" . $row['NUM_P_TABLE'] . "</a>";
            }

            $this->data[$i]['NUM_P_TABLE'] = $protocol;

            $i++;

        }

    }

    public function selectDataTable($params, $cDeal, $newResult) {
        $this->queryDataTable($params);

        $this->collectingData($cDeal, $newResult);

        $jsonData = [
            "draw" => intval($this->params['draw']),
            "recordsTotal" => intval($this->totalRecords),
            "recordsFiltered" => intval($this->totalRecords),
            "data" => $this->data
        ];

        return $jsonData;
    }

};
