<?php

class Oz extends Model
{
    public function getListForJournal($filter)
    {
        $result = [];

        $holidays = [
        "2025-01-01",
        "2025-01-02",
        "2025-01-03",
        "2025-01-04",
        "2025-01-05",
        "2025-01-06",
        "2025-01-07",
        "2025-01-08",
        "2025-01-11",
        "2025-01-12",
        "2025-01-18",
        "2025-01-19",
        "2025-01-25",
        "2025-01-26",
        "2025-02-01",
        "2025-02-02",
        "2025-02-08",
        "2025-02-09",
        "2025-02-15",
        "2025-02-16",
        "2025-02-22",
        "2025-02-23",
        "2025-03-01",
        "2025-03-02",
        "2025-03-08",
        "2025-03-09",
        "2025-03-15",
        "2025-03-16",
        "2025-03-22",
        "2025-03-23",
        "2025-03-29",
        "2025-03-30",
        "2025-04-05",
        "2025-04-06",
        "2025-04-12",
        "2025-04-13",
        "2025-04-19",
        "2025-04-20",
        "2025-04-26",
        "2025-04-27",
        "2025-05-01",
        "2025-05-02",
        "2025-05-03",
        "2025-05-04",
        "2025-05-08",
        "2025-05-09",
        "2025-05-10",
        "2025-05-11",
        "2025-05-17",
        "2025-05-18",
        "2025-05-24",
        "2025-05-25",
        "2025-05-31",
        "2025-06-01",
        "2025-06-07",
        "2025-06-08",
        "2025-06-12",
        "2025-06-13",
        "2025-06-14",
        "2025-06-15",
        "2025-06-21",
        "2025-06-22",
        "2025-06-28",
        "2025-06-29",
        "2025-07-05",
        "2025-07-06",
        "2025-07-12",
        "2025-07-13",
        "2025-07-19",
        "2025-07-20",
        "2025-07-26",
        "2025-07-27",
        "2025-08-02",
        "2025-08-03",
        "2025-08-09",
        "2025-08-10",
        "2025-08-16",
        "2025-08-17",
        "2025-08-23",
        "2025-08-24",
        "2025-08-30",
        "2025-08-31",
        "2025-09-06",
        "2025-09-07",
        "2025-09-13",
        "2025-09-14",
        "2025-09-20",
        "2025-09-21",
        "2025-09-27",
        "2025-09-28",
        "2025-10-04",
        "2025-10-05",
        "2025-10-11",
        "2025-10-12",
        "2025-10-18",
        "2025-10-19",
        "2025-10-25",
        "2025-10-26",
        "2025-11-02",
        "2025-11-03",
        "2025-11-04",
        "2025-11-08",
        "2025-11-09",
        "2025-11-15",
        "2025-11-16",
        "2025-11-22",
        "2025-11-23",
        "2025-11-29",
        "2025-11-30",
        "2025-12-06",
        "2025-12-07",
        "2025-12-13",
        "2025-12-14",
        "2025-12-20",
        "2025-12-21",
        "2025-12-27",
        "2025-12-28",
        "2025-12-31"
    ];

        $where = "";

        if($filter['laboratory']) {
            $where .= " AND bl.ID = '{$filter['laboratory']}'";
        }

        if($filter['request']) {
            $where .= " AND bt.REQUEST_TITLE LIKE '%{$filter['request']}%'";
        }

        if($filter['cipher']) {
            $where .= " AND bt.ACT_NUM LIKE '%{$filter['cipher']}%'";
        }

        $where .= " AND 1";

        $response = $this->DB->Query(
            "
            SELECT bt.ACT_NUM as 'probe_cipher', dtc.ID_CONTRACT as 'dogovor', bt.TYPE_ID as 'request_type',
            bt.REQUEST_TITLE as 'request_name', DATE_FORMAT(bt.DATE_SOZD, '%d.%m.%Y') as 'request_date', DATE_FORMAT(bt.DATE_ACT, '%d.%m.%Y') as 'cipher_date',
            bcc.TITLE as 'organization_name', bl.short_name as 'lab_name', ugtp.id as 'ugtp_id',
            DATE_FORMAT(utld.date_start, '%d.%m.%Y') as 'approximate_date', utld.days as 'approximate_days',
            bl.HEAD_ID as 'head_id', bt.ID_Z as 'deal_id', dp.name as 'payment_type'
            FROM ba_tz bt
            LEFT JOIN ulab_material_to_request umtr ON umtr.deal_id = bt.ID_Z
            LEFT JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            LEFT JOIN DEALS_TO_CONTRACTS dtc ON dtc.ID_DEAL = bt.ID_Z
            LEFT JOIN ulab_methods_lab uml ON uml.method_id = ugtp.method_id
            LEFT JOIN ba_laba bl ON bl.ID = uml.lab_id
            LEFT JOIN b_crm_company bcc ON bcc.ID = bt.COMPANY_ID
            LEFT JOIN ulab_tz_lab_duration utld ON utld.deal_id = bt.ID_Z AND utld.lab_id = bl.ID
            LEFT JOIN DOGOVOR d ON d.ID = dtc.ID_CONTRACT
            LEFT JOIN dogovor_payment dp ON dp.id = d.payment_id
            WHERE ((ugtp.id IS NULL AND umtr.id IS NULL) OR (ugtp.id IS NOT NULL AND umtr.id IS NOT NULL))
              AND bt.DATE_CREATE_TIMESTAMP between '2024-07-01' AND NOW()
              AND ugtp.id IS NOT NULL
              {$where}
            GROUP BY bl.ID, bt.ID_Z
            ORDER BY bt.ID DESC
            LIMIT {$filter['start']}, {$filter['length']}
            ");

        while($row = $response->Fetch()) {
            $row['approximate_days_array'] = [];
            $row['fact_days_array'] = [];
            $row['fact_end_day'] = [];

            $row['dogovor'] = $row['dogovor'] ? '1' : '';
            $row['tz'] = $row['ugtp_id'] ? '1' : '';

            $color = '';
            $workColor = '';

            if(!$row['payment_type']) {
                $row['payment_type'] = '100% предоплата';
            }

            switch ($row['lab_name']) {
                case 'ОПП':
                    $color = 'rgba(0, 51, 102, 0.5)';
                    $workColor = 'rgba(0, 51, 102, 1)';
                    break;
                case 'ЛСМ':
                    $color = 'rgba(70, 189, 198, 0.5)';
                    $workColor = 'rgba(70, 189, 198, 1)';
                    break;
                case 'ЛФХИ':
                    $color = 'rgba(249, 132, 229, 0.5)';
                    $workColor = 'rgba(249, 132, 229, 1)';
                    break;
                case 'ЛФМИ':
                    $color = 'rgba(173, 216, 230, 0.5)';
                    $workColor = 'rgba(173, 216, 230, 1)';
                    break;
                case 'ОСК':
                    $color = 'rgba(152, 251, 152, 0.5)';
                    $workColor = 'rgba(152, 251, 152, 1)';
                    break;
                case 'ДСЛ':
                    $color = 'rgba(184,213,173, 0.5)';
                    $workColor = 'rgba(184,213,173, 1)';
                    break;
            }

            if(!empty($row['approximate_date'])) {
                $weekDay = date('Y-m-d', strtotime($row['approximate_date']));

                $row['approximate_days_array'][date('Y-m-d', strtotime($weekDay))] = $color;

                for($i = 1; $i < $row['approximate_days']; $i++) {
                    $nextDay = date('d.m.Y', strtotime($weekDay . "+{$i} day"));

                    $fullDate = date('Y-m-d', strtotime($nextDay));

                    while(in_array($fullDate, $holidays)) {
                        $nextDay = date('d.m.Y', strtotime($nextDay . "+1 day"));

                        $fullDate = date('Y-m-d', strtotime($nextDay));

                        $row['approximate_days']++;
                        $i++;
                    }

                    $row['approximate_days_array'][date('Y-m-d', strtotime($nextDay))] = $color;
                }
            }

            $workDays = $this->getWorkDaysForProbe($row['head_id'], $row['deal_id']);

            if(!$workDays['end_date'] && $workDays['start_date']) {
               $workDays['end_date'] = date('d.m.Y');

               $row['fact_end_day'][date('Y-m-d', strtotime($workDays['end_date']))] = $workDays['complete_percent'];
            }else if($workDays['end_date'] && !$workDays['start_date']) {
                $currentMonth = date('m');
                $currentYear = date('Y');

                $workDays['start_date'] = date('d.m.Y', strtotime("{$currentYear}.{$currentMonth}.01"));

                $row['fact_end_day'][date('Y-m-d', strtotime($workDays['end_date']))] = '■';
            }else if($workDays['end_date'] && $workDays['start_date']) {
                $row['fact_end_day'][date('Y-m-d', strtotime($workDays['end_date']))] = '■';
            }

            if($workDays['end_date'] && $workDays['start_date']) {
                $difference = strtotime($workDays['end_date']) - strtotime($workDays['start_date']);

                $daysCount = $difference / (60*60*24);

                $weekDay = date('Y-m-d', strtotime($workDays['start_date']));

                $row['fact_days_array'][date('Y-m-d', strtotime($weekDay))] = $workColor;

                for($i = 0; $i <= $daysCount; $i++) {
                    $nextDay = date('d.m.Y', strtotime($weekDay . "+{$i} day"));

                    $fullDate = date('Y-m-d', strtotime($nextDay));

                    while(in_array($fullDate, $holidays)) {
                        $nextDay = date('d.m.Y', strtotime($nextDay . "+1 day"));

                        $fullDate = date('Y-m-d', strtotime($nextDay));
                    }

                    $row['fact_days_array'][date('Y-m-d', strtotime($nextDay))] = $workColor;
                }
            }

            switch ($row['request_type']){
                case 'SALE':
                    $row['request_type'] = 'ИЦ';
                    break;
                case 'COMPLEX':
                    $row['request_type'] = 'ОСК';
                    break;
                case '1':
                    $row['request_type'] = 'ВЛК';
                    break;
                case '2':
                    $row['request_type'] = 'МСИ';
                    break;
                case '5':
                    $row['request_type'] = 'АП';
                    break;
                case '4':
                    $row['request_type'] = 'НК';
                    break;
                case '8':
                    $row['request_type'] = 'Н';
                    break;
                case '7':
                    $row['request_type'] = 'ПР';
                    break;
                case '9':
                    $row['request_type'] = 'НИЦ';
                    break;
            }

            $result['data'] []= $row;
        }

        return $result;
    }

    public function getCountForJournal($filter)
    {
        $where = "";

        if($filter['laboratory']) {
            $where .= " AND bl.ID = '{$filter['laboratory']}'";
        }

        if($filter['request']) {
            $where .= " AND bt.REQUEST_TITLE LIKE '%{$filter['request']}%'";
        }

        if($filter['cipher']) {
            $where .= " AND bt.ACT_NUM LIKE '%{$filter['cipher']}%'";
        }

        $where .= " AND 1";

        $result['totalRecords'] = $this->DB->Query("
        SELECT bt.ACT_NUM as 'probe_cipher', dtc.ID_CONTRACT as 'dogovor', bt.TYPE_ID as 'request_type',
            bt.REQUEST_TITLE as 'request_name', DATE_FORMAT(bt.DATE_SOZD, '%d.%m.%Y') as 'request_date', DATE_FORMAT(bt.DATE_ACT, '%d.%m.%Y') as 'cipher_date',
            bcc.TITLE as 'organization_name', bl.short_name as 'lab_name', ugtp.id as 'ugtp_id',
            DATE_FORMAT(utld.date_start, '%d.%m.%Y') as 'approximate_date', utld.days as 'approximate_days' 
            FROM ba_tz bt
            LEFT JOIN ulab_material_to_request umtr ON umtr.deal_id = bt.ID_Z
            LEFT JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            LEFT JOIN DEALS_TO_CONTRACTS dtc ON dtc.ID_DEAL = bt.ID_Z
            LEFT JOIN ulab_methods_lab uml ON uml.method_id = ugtp.method_id
            LEFT JOIN ba_laba bl ON bl.ID = uml.lab_id
            LEFT JOIN b_crm_company bcc ON bcc.ID = bt.COMPANY_ID
            LEFT JOIN ulab_tz_lab_duration utld ON utld.deal_id = bt.ID_Z AND utld.lab_id = bl.ID
            LEFT JOIN DOGOVOR d ON d.ID = dtc.ID_CONTRACT
            LEFT JOIN dogovor_payment dp ON dp.id = d.payment_id
            WHERE (ugtp.id IS NULL AND umtr.id IS NULL) OR (ugtp.id IS NOT NULL AND umtr.id IS NOT NULL) 
              AND bt.DATE_CREATE_TIMESTAMP between '2024-07-01' AND NOW()
              AND ugtp.id IS NOT NULL
              {$where}
            GROUP BY bl.ID, bt.ID_Z
            ORDER BY bt.ID DESC
        ")->SelectedRowsCount();

        return $result;
    }

    public function getWorkDaysForProbe($headId, $dealId)
    {
        $acceptDateArray = [];
        $setActualValueDateArray = [];

        $allCount = 0;
        $completeCount = 0;

        $response = $this->DB->Query(
            "
            SELECT bl.short_name, ptl.accept_date, ugtp.date_set_actual_value, ugtp.actual_value, ugtp.id
            FROM ulab_material_to_request umtr
            INNER JOIN probe_to_lab ptl ON ptl.umtr_id = umtr.id AND ptl.user_id = '{$headId}'
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            INNER JOIN ba_laba bl ON bl.HEAD_ID = '{$headId}'
            INNER JOIN ulab_methods_lab uml ON uml.method_id = ugtp.method_id AND uml.lab_id = bl.ID
            WHERE umtr.deal_id = '{$dealId}'
            "
        );

        while($row = $response->Fetch()) {
            $allCount++;

            if(!$row['accept_date'] && !$row['date_set_actual_value']) continue;

            $acceptYear = date('Y', strtotime($row['accept_date']));

            if($acceptYear != '-0001' && $row['accept_date']) $acceptDateArray []= strtotime($row['accept_date']);
            if($row['date_set_actual_value']) $setActualValueDateArray [] = strtotime($row['date_set_actual_value']);

            if($row['actual_value']) $completeCount++;
        }

        $minDate = !empty($acceptDateArray) ? date('d.m.Y', min($acceptDateArray)) : '';
        $maxDate = !empty($setActualValueDateArray) ? date('d.m.Y', max($setActualValueDateArray)) : '';

        $completePercent = ($completeCount / $allCount) * 100;

        $result =
            [
                'start_date' => $minDate,
                'end_date' => $maxDate,
                'complete_percent' => $completePercent . '%'
            ];

        return $result;
    }
}