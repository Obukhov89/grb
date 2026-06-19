<?php

/**
 * Модель для работы с договорами
 * Class Order
 */
class Order extends Model {

    /**
     * @param $orderId
     * @return array|false
     */
    public function get($orderId)
    {
        return $this->DB->Query("select * from DOGOVOR where ID = {$orderId}")->Fetch();
    }

    /**
     * @param $filter
     * @return array
     */
    public function getDataToJournal($filter)
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'b.ID',
            'dir' => 'DESC'
        ];
        if ( !empty($filter) ) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if ( !empty($filter['search']) ) {
                // Номер
                if ( isset($filter['search']['NUMBER']) ) {
                    $where .= "d.NUMBER LIKE '%{$filter['search']['NUMBER']}%' AND ";
                }
                // Дата
                if ( isset($filter['search']['DATE']) ) {
                    $where .= "LOCATE('{$filter['search']['DATE']}', DATE_FORMAT(d.DATE, '%d.%m.%Y')) > 0 AND ";
                }
                if ( isset($filter['search']['dateStart']) ) {
                    $where .= "(d.DATE >= '{$filter['search']['dateStart']}' AND d.DATE <= '{$filter['search']['dateEnd']}') AND ";
                }
                // Клиент
                if ( isset($filter['search']['COMPANY_TITLE']) ) {
                    $where .= "b.COMPANY_TITLE LIKE '%{$filter['search']['COMPANY_TITLE']}%' AND ";
                }
                // Клиент
                if ( isset($filter['search']['CONTRACT_TYPE']) ) {
                    $where .= "d.CONTRACT_TYPE LIKE '%{$filter['search']['CONTRACT_TYPE']}%' AND ";
                }
                // Клиент
                if ( isset($filter['search']['linkName2']) ) {
                    if ($filter['search']['linkName2'] == 1) {
                        $where .= "d.PDF is not null AND ";
                    } elseif ($filter['search']['linkName2'] == 2) {
                        $where .= "d.PDF is null AND ";
                    }
                }
                // везде
                if ( isset($filter['search']['everywhere']) ) {
                    $where .=
                        "(
                        d.NUMBER LIKE '%{$filter['search']['everywhere']}%' 
                        OR d.DATE LIKE '%{$filter['search']['everywhere']}%' 
                        OR b.COMPANY_TITLE LIKE '%{$filter['search']['everywhere']}%' 
                        ) AND ";
                }
            }

            // работа с сортировкой
            if ( !empty($filter['order']) ) {
                if ( $filter['order']['dir'] === 'asc' ) {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'NUMBER':
                        $order['by'] = 'd.NUMBER';
                        break;
                    case 'DATE':
                        $order['by'] = 'd.DATE';
                        break;
                    case 'COMPANY_TITLE':
                        $order['by'] = 'b.COMPANY_TITLE';
                        break;
                }
            }

            // работа с пагинацией
            if ( isset($filter['paginate']) ) {
                $offset = 0;
                // количество строк на страницу
                if ( isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0 ) {
                    $length = $filter['paginate']['length'];

                    if ( isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0 ) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT {$offset}, {$length}";
                }
            }
        }
        $where .= "1 ";

        $data = $this->DB->Query(
            "SELECT b.ID b_id, b.STAGE_ID, b.ID_Z, b.COMPANY_TITLE, b.DOGOVOR_TABLE, 
                        d.ID d_id, d.NUMBER, d.DATE, d.PDF, d.ACTUAL_VER, d.IS_ACTION, d.CONTRACT_TYPE 
                    FROM ba_tz b
                    INNER JOIN DOGOVOR d on d.TZ_ID = b.ID 
                    WHERE b.TYPE_ID != '3' AND b.REQUEST_TITLE <> '' AND d.ID <> 2522 AND {$where}
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM ba_tz b
                    INNER JOIN DOGOVOR d on d.TZ_ID = b.ID 
                    WHERE b.TYPE_ID != '3' AND b.REQUEST_TITLE <> '' AND d.ID <> 2522"
        )->Fetch();

        $dataFiltered = $this->DB->Query(
            "SELECT count(*) val
                    FROM ba_tz b
                    INNER JOIN DOGOVOR d on d.TZ_ID = b.ID 
                    WHERE b.TYPE_ID != '3' AND b.REQUEST_TITLE <> '' AND d.ID <> 2522 AND {$where}"
        )->Fetch();

        $result = [];

        while ($row = $data->Fetch()) {

            if ( !empty($row['PDF']) ) {
                $row['titleStage'] = 'Договор подписан';
                $row['bgStage'] = 'bg-green';
            } else {
                $row['titleStage'] = 'Договор не подписан';
                $row['bgStage'] = 'bg-yellow';
                $row['PDF'] = '';
            }

            if (!$row['IS_ACTION']) {
                $row['titleStage'] = 'Договор аннулирован';
                $row['bgStage'] = 'bg-red';
            }

            if ( !empty($row['ACTUAL_VER']) ) {
                if ($row['CONTRACT_TYPE'] == 'Договор') {
                    $row['order_pdf'] = "{$row['d_id']}/{$row['ACTUAL_VER']}.pdf";
                } elseif ($row['CONTRACT_TYPE'] == 'Счет-оферта') {
                    $row['order_pdf'] = "{$row['b_id']}/{$row['ACTUAL_VER']}.pdf";
                }
            } else {
                $row['order_pdf'] = '';
            }


            $row['b_tz_id'] = $row['b_id'];
            $row['DATE'] = date("d.m.Y", strtotime($row['DATE']));

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal['val'];
        $result['recordsFiltered'] = $dataFiltered['val'];



        return $result;
    }

    /**
     * @param $filter
     * @return array
     */
    public function getDataToJournalNew($filter)
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'b.ID',
            'dir' => 'DESC'
        ];
//        $this->pre($filter['search']);
        if ( !empty($filter) ) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if ( !empty($filter['search']) ) {
                if(isset($filter['search']['payment_type'])) {
                    if($filter['search']['payment_type'] == 1) {
                        $where .= "(d.payment_id = '{$filter['search']['payment_type']}' OR d.payment_id IS NULL OR d.payment_id = '') AND ";
                    }else {
                        $where .= "d.payment_id = '{$filter['search']['payment_type']}' AND ";
                    }
                }
                if(isset($filter['search']['price_dogovor'])) {
                    if($filter['search']['price_dogovor'] == 1) {
                        $where .= "(d.dogovor_price > 0) AND ";
                    }else {
                        $where .= "(d.dogovor_price <= 0 OR d.dogovor_price = '' OR d.dogovor_price IS NULL) AND ";
                    }

                }
                if(isset($filter['search']['special_notes'])) {
                    if($filter['search']['special_notes'] == 1) {
                        $where .= "(d.special_notes <> '' AND d.special_notes IS NOT NULL) AND ";
                    }else {
                        $where .= "(d.special_notes = '' OR d.special_notes IS NULL) AND ";
                    }

                }
                if(isset($filter['search']['belonging'])) {
                    if($filter['search']['belonging'] == 1) {
                        $where .= "(d.belonging_id = '{$filter['search']['belonging']}' OR d.belonging_id IS NULL OR d.belonging_id = '') AND ";
                    }else {
                        $where .= "d.belonging_id = '{$filter['search']['belonging']}' AND ";
                    }
                }
                if(isset($filter['search']['dogovor_end'])) {
                    $where .= "DATE_FORMAT(d.date_end, '%d.%m.%Y') LIKE '%{$filter['search']['dogovor_end']}%' AND ";
                }
                // Номер
                if ( isset($filter['search']['NUMBER']) ) {
                    $where .= "d.NUMBER LIKE '%{$filter['search']['NUMBER']}%' AND ";
                }
                // Дата
                if ( isset($filter['search']['DATE']) ) {
                    $where .= "LOCATE('{$filter['search']['DATE']}', DATE_FORMAT(d.DATE, '%d.%m.%Y')) > 0 AND ";
                }
                if ( isset($filter['search']['dateStart']) ) {
                    $where .= "(d.DATE >= '{$filter['search']['dateStart']}' AND d.DATE <= '{$filter['search']['dateEnd']}') AND ";
                }
                // Клиент
                if ( isset($filter['search']['COMPANY_TITLE']) ) {
                    $where .= "bcm.TITLE LIKE '%{$filter['search']['COMPANY_TITLE']}%' AND ";
                }
                // Клиент
                if ( isset($filter['search']['type_dogovor']) ) {
                    $where .= "(dt.id = '{$filter['search']['type_dogovor']}' OR d.type_id = '{$filter['search']['type_dogovor']}') AND ";
                }
                // Клиент
                if ( isset($filter['search']['linkName2']) ) {
                    if ($filter['search']['linkName2'] == 1) {
                        $where .= "d.PDF is not null AND ";
                    } elseif ($filter['search']['linkName2'] == 2) {
                        $where .= "d.PDF is null AND ";
                    }
                }
                if(isset($filter['search']['need_date_end'])) {
                    $where .= $filter['search']['need_date_end'] . " AND ";
                }
                if(isset($filter['search']['need_note'])) {
                    $where .= $filter['search']['need_note'] . " AND ";
                }
                if(isset($filter['search']['originals'])) {
                    if($filter['search']['originals'] == 1) {
                        $where .= "(
                                    (d.need_additional = 0 OR EXISTS (
                                        SELECT 1 FROM dogovor_documents 
                                        WHERE dogovor_id = d.ID AND type_id = 1 AND is_scan = 0
                                    ))
                                    AND
                                    (d.need_protocol = 0 OR EXISTS (
                                        SELECT 1 FROM dogovor_documents 
                                        WHERE dogovor_id = d.ID AND type_id = 2 AND is_scan = 0
                                    ))
                                    AND
                                    EXISTS (
                                        SELECT 1 FROM dogovor_documents 
                                        WHERE dogovor_id = d.ID AND type_id = 3 AND is_scan = 0
                                    )
                                ) AND ";
                    } else if($filter['search']['originals'] == 2) {
                        $where .= "(
                                    (d.need_additional = 1 AND NOT EXISTS (
                                        SELECT 1 FROM dogovor_documents 
                                        WHERE dogovor_id = d.ID AND type_id = 1 AND is_scan = 0
                                    ))
                                    OR
                                    (d.need_protocol = 1 AND NOT EXISTS (
                                        SELECT 1 FROM dogovor_documents 
                                        WHERE dogovor_id = d.ID AND type_id = 2 AND is_scan = 0
                                    ))
                                    OR
                                    NOT EXISTS (
                                        SELECT 1 FROM dogovor_documents 
                                        WHERE dogovor_id = d.ID AND type_id = 3 AND is_scan = 0
                                    )
                                ) AND ";
                    } else if($filter['search']['originals'] == 3) {
                        $where .= "(
                                    (d.need_additional = 0 OR NOT EXISTS (
                                        SELECT 1 FROM dogovor_document_send 
                                        WHERE dogovor_id = d.ID AND type_id = 1 AND is_scan = 0
                                    ))
                                    AND
                                    (d.need_protocol = 0 OR NOT EXISTS (
                                        SELECT 1 FROM dogovor_document_send 
                                        WHERE dogovor_id = d.ID AND type_id = 2 AND is_scan = 0
                                    ))
                                    AND
                                    NOT EXISTS (
                                        SELECT 1 FROM dogovor_document_send 
                                        WHERE dogovor_id = d.ID AND type_id = 3 AND is_scan = 0
                                    )
                                ) AND ";
                    }
                }
                // везде
                if ( isset($filter['search']['everywhere']) ) {
                    $where .=
                        "(
                        d.NUMBER LIKE '%{$filter['search']['everywhere']}%' 
                        OR d.DATE LIKE '%{$filter['search']['everywhere']}%' 
                        OR bcm.TITLE LIKE '%{$filter['search']['everywhere']}%' 
                        ) AND ";
                }
            }

            // работа с сортировкой
            if ( !empty($filter['order']) ) {
                if ( $filter['order']['dir'] === 'asc' ) {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'NUMBER':
                        $order['by'] = 'd.NUMBER';
                        break;
                    case 'DATE':
                        $order['by'] = 'd.DATE';
                        break;
                    case 'COMPANY_TITLE':
                        $order['by'] = 'bcm.TITLE';
                        break;
                    case 'ID':
                        $order['by'] = 'd.ID';
                        break;
                }
            }

            // работа с пагинацией
            if ( isset($filter['paginate']) ) {
                $offset = 0;
                // количество строк на страницу
                if ( isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0 ) {
                    $length = $filter['paginate']['length'];

                    if ( isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0 ) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT {$offset}, {$length}";
                }
            }
        }
        $where .= "1 ";

        $data = $this->DB->Query(
            "SELECT b.ID b_id, b.STAGE_ID, b.ID_Z, bcm.TITLE as 'COMPANY_TITLE', b.DOGOVOR_TABLE, 
                        d.ID d_id, d.NUMBER, d.DATE, d.PDF, d.ACTUAL_VER, d.IS_ACTION, d.CONTRACT_TYPE, d.CLIENT_ID, d.custom_number
                        , d.number_client, dp.name as 'payment_type', d.dogovor_price as 'price_dogovor', DATE_FORMAT(d.date_end, '%d.%m.%Y') as 'dogovor_end', d.special_notes,
                          d.need_additional, d.need_protocol, d.additional_count, d.IS_ACTION as 'null_doogovor', dt.name as 'type_dogovor', db.name as 'belonging',
                          dd.id as 'check_original'
                        FROM DOGOVOR d
                        LEFT JOIN ba_tz b ON d.TZ_ID = b.ID
                        LEFT JOIN b_crm_company bcm ON bcm.ID = d.CLIENT_ID
                        LEFT JOIN dogovor_payment dp ON dp.id = d.payment_id
                        LEFT JOIN dogovor_types dt ON dt.id = d.dogovor_type_id
                        LEFT JOIN dogovor_belonging db ON db.id = d.belonging_id
                        LEFT JOIN dogovor_documents dd ON dd.dogovor_id = d.ID AND dd.type_id = 3 AND dd.is_scan = 0
                        WHERE (b.TYPE_ID != '3' OR d.add_in_journal = 1) AND (b.REQUEST_TITLE <> '' OR d.add_in_journal = 1) AND d.ID <> 2522 AND {$where}
                        ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM DOGOVOR d
                    LEFT JOIN ba_tz b ON d.TZ_ID = b.ID
                    LEFT JOIN b_crm_company bcm ON bcm.ID = d.CLIENT_ID
                    LEFT JOIN dogovor_payment dp ON dp.id = d.payment_id
                    LEFT JOIN dogovor_types dt ON dt.id = d.dogovor_type_id
                    LEFT JOIN dogovor_belonging db ON db.id = d.belonging_id
                    LEFT JOIN dogovor_documents dd ON dd.dogovor_id = d.ID AND dd.type_id = 3 AND dd.is_scan = 0
                    WHERE (b.TYPE_ID != '3' OR d.add_in_journal = 1) AND (b.REQUEST_TITLE <> '' OR d.add_in_journal = 1) AND d.ID <> 2522"
        )->Fetch();

        $dataFiltered = $this->DB->Query(
            "SELECT count(*) val
                    FROM DOGOVOR d
                    LEFT JOIN ba_tz b ON d.TZ_ID = b.ID
                    LEFT JOIN b_crm_company bcm ON bcm.ID = d.CLIENT_ID
                    LEFT JOIN dogovor_payment dp ON dp.id = d.payment_id
                    LEFT JOIN dogovor_types dt ON dt.id = d.dogovor_type_id
                    LEFT JOIN dogovor_belonging db ON db.id = d.belonging_id
                    LEFT JOIN dogovor_documents dd ON dd.dogovor_id = d.ID AND dd.type_id = 3 AND dd.is_scan = 0
                    WHERE (b.TYPE_ID != '3' OR d.add_in_journal = 1) AND (b.REQUEST_TITLE <> '' OR d.add_in_journal = 1) AND d.ID <> 2522 AND {$where}"
        )->Fetch();

        $result = [];

        while ($row = $data->Fetch()) {

            if(!$row['payment_type']) $row['payment_type'] = '100% предоплата';

            if(!$row['price_dogovor']) $row['price_dogovor'] = '0.00';

            if(!$row['type_dogovor']) $row['type_dogovor'] = $row['CONTRACT_TYPE'];

            if(!$row['dogovor_end']) {
                $row['dogovor_end'] = '--';
            }else {
                $currentDate = strtotime(date('d.m.Y'));

                $endDate = strtotime($row['dogovor_end']);
                $weekLastOne = strtotime(date('d.m.Y', strtotime($row['dogovor_end']. " - 7 day")));

                if($currentDate >= $weekLastOne && $currentDate <= $endDate) {
                    $row['dogovor_end_color'] = "#d94e5e";
                }
            }

            if(!$row['special_notes']) $row['special_notes'] = '--';

            if ( !empty($row['check_original']) ) {
                $row['titleStage'] = 'Договор подписан';
                $row['bgStage'] = 'bg-green';
            } else {
                $row['titleStage'] = 'Договор не подписан';
                $row['bgStage'] = 'bg-yellow';
                $row['PDF'] = '';
            }

            if (!$row['IS_ACTION']) {
                $row['titleStage'] = 'Договор аннулирован';
                $row['bgStage'] = 'bg-red';
            }

            if ( !empty($row['ACTUAL_VER']) ) {
                if ($row['CONTRACT_TYPE'] == 'Договор') {
                    $row['order_pdf'] = "{$row['d_id']}/{$row['ACTUAL_VER']}.pdf";
                } elseif ($row['CONTRACT_TYPE'] == 'Счет-оферта') {
                    $row['order_pdf'] = "{$row['b_id']}/{$row['ACTUAL_VER']}.pdf";
                }
            } else {
                $row['order_pdf'] = '';
            }

            if(!$row['COMPANY_TITLE']) {
                $row['COMPANY_TITLE'] = $this->DB->Query("SELECT COMPANY_TITLE FROM ba_tz WHERE ID_Z = '{$row['ID_Z']}'")->Fetch()['COMPANY_TITLE'];
            }

            if($row['custom_number']) {
                $row['NUMBER'] = $row['number_client'];
            }

            $row['b_tz_id'] = $row['b_id'];
            $row['DATE'] = date("d.m.Y", strtotime($row['DATE']));


            $row['document_additional'] = [];
            if($row['need_additional']) {
                $row['document_additional'] = $this->getDocumentsInfo($row['d_id'], 1, $row['additional_count'] ?? 1);
            }

            $row['document_protocol'] = [];
            if($row['need_protocol']) {
                $row['document_protocol'] = $this->getDocumentsInfo($row['d_id'], 2);
            }

            $row['document_dogovor'] = $this->getDocumentsInfo($row['d_id'], 3);

            $result[] = $row;
        }



        $result['recordsTotal'] = $dataTotal['val'];
        $result['recordsFiltered'] = $dataFiltered['val'];



        return $result;
    }

    public function getDocumentsInfo($dogovorId, $typeId, $count = 0)
    {
        $result = [];
        $sendArray = [];

        if($count) {
            $response = $this->DB->Query("SELECT * FROM dogovor_documents WHERE dogovor_id = '{$dogovorId}' AND type_id = '{$typeId}'");
            $sendResponse = $this->DB->Query("SELECT * FROM dogovor_document_send WHERE dogovor_id = '{$dogovorId}' AND type_id = '{$typeId}'");

            while ($row = $response->Fetch()) {
                if($row['is_scan']) {
                    $result['scan']['data'][] = date('d.m.Y', strtotime($row['date']));
                }else {
                    $result['original']['data'][] = date('d.m.Y', strtotime($row['date']));
                }
            }

            while($row = $sendResponse->Fetch()) {
                if($row['is_scan']) {
                    $sendArray['scan']['data'][] = date('d.m.Y', strtotime($row['date']));
                }else {
                    $sendArray['original']['data'][] = date('d.m.Y', strtotime($row['date']));
                }
            }

            if(count($result['scan']['data']) >= $count) {
                $result['scan']['bg-color'] = '#fff2cc';
            }else {
                $result['scan']['bg-color'] = '#deeaf6';
            }

            if(count($result['original']['data']) >= $count) {
                $result['original']['bg-color'] = '#e2efd9';
            }else {
                $result['original']['bg-color'] = '#deeaf6';
            }

            if(count($sendArray['scan']['data']) > count($result['scan']['data'])) {
                $result['scan']['bg-color'] = '#deeaf6';

                $result['scan']['data'] = array_merge($result['scan']['data'] ?? [], $sendArray['scan']['data'] ?? []);
            }

            if(count($sendArray['original']['data']) > count($result['original']['data'])) {
                $result['original']['bg-color'] = '#deeaf6';

                $result['original']['data'] = array_merge($result['original']['data'] ?? [], $sendArray['original']['data'] ?? []);
            }

            return $result;
        }

        $response = $this->DB->Query("SELECT * FROM dogovor_documents WHERE dogovor_id = '{$dogovorId}' AND type_id = '{$typeId}'");
        $sendResponse = $this->DB->Query("SELECT * FROM dogovor_document_send WHERE dogovor_id = '{$dogovorId}' AND type_id = '{$typeId}'");

        while ($row = $response->Fetch()) {
            if($row['is_scan']) {
                $result['scan']['data'][] = date('d.m.Y', strtotime($row['date']));
            }else {
                $result['original']['data'][] = date('d.m.Y', strtotime($row['date']));
            }
        }

        while($row = $sendResponse->Fetch()) {
            if($row['is_scan']) {
                $sendArray['scan']['data'][] = date('d.m.Y', strtotime($row['date']));
            }else {
                $sendArray['original']['data'][] = date('d.m.Y', strtotime($row['date']));
            }
        }

        if(count($result['scan']['data']) >= 1) {
            $result['scan']['bg-color'] = '#fff2cc';
        }else {
            $result['scan']['bg-color'] = '#deeaf6';
        }

        if(count($result['original']['data']) >= 1) {
            $result['original']['bg-color'] = '#e2efd9';
        }else {
            $result['original']['bg-color'] = '#deeaf6';
        }

        if(count($sendArray['scan']['data']) > count($result['scan']['data'])) {
            $result['scan']['bg-color'] = '#deeaf6';

            $result['scan']['data'] = array_merge($result['scan']['data'] ?? [], $sendArray['scan']['data'] ?? []);
        }

        if(count($sendArray['original']['data']) > count($result['original']['data'])) {
            $result['original']['bg-color'] = '#deeaf6';

            $result['original']['data'] = array_merge($result['original']['data'] ?? [], $sendArray['original']['data'] ?? []);
        }

        return $result;
    }

    /**
     * @param $row - ba_tz
     * @return string[]
     */
    public function getStage($row)
    {
        switch ($row['STAGE_ID']) {
            case 'NEW':
            case 'PREPARATION':
            case 'PREPAYMENT_INVOICE':
            case 'EXECUTING':
                $bgColor = 'bg-light-blue';
                $title = 'Испытания еще не проводились. Пробы не получены.';
                break;
            case 'FINAL_INVOICE':
                $bgColor = 'bg-yellow';
                $title = 'Пробы получены. Проводятся испытания';
                break;
            case '1':
                $bgColor = 'bg-yellow';
                $title = 'Пробы получены. Проводятся испытания.';
                break;
            case '2':
                $bgColor = 'bg-purple';
                $title = 'Испытания в лаборатории завершены. Оплата получена или не требуется.';
                break;
            case '4':
                $bgColor = 'bg-light-green';
                $title = 'УПД отправлены заказчику.';
                break;
            case 'WON':
                $bgColor = 'bg-green';
                $title = 'УПД получены. Заявка успешно завершена.';
                break;
            case 'LOSE':
                $bgColor = 'bg-red';
                $title = 'Испытания не проведены. Заявка прекращена.';
                break;
            case '7':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Не проводим подобные испытания.';
                break;
            case '6':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Заказчик не вышел на связь.';
                break;
            case '5':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Заказчика не устроила цена.';
                break;
            case '8':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Создана другая заявка.';
                break;
            case '9':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Заказчик выбрал лабораторию в своем городе.';
                break;
            case '10':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Заказчик решил не проводить испытания.';
                break;
            case '11':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Отказались сами в связи с высокой загруженностью.';
                break;
            case '12':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Судебная экспертиза.';
                break;
            case '13':
                $bgColor = 'bg-red';
                $title = 'Заявка прекращена. Участие в тендере.';
                break;
            default:
                $bgColor = 'bg-red';
                $title = '';
                break;
        }

        if (
            $row['ACT_NUM']
            && in_array($row['STAGE_ID'], ['NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING'])
        ) {
            $bgColor='bg-yellow';
            $title = 'Протокол не выдан. Испытания еще не проводились. Пробы получены';
        }
        if (
            $row['PRICE'] && $row['RESULTS']
            && (!$row['OPLATA'] || $row['OPLATA'] < $row['PRICE'])
            && $row['STAGE_ID'] === '2'
        ) {
            $bgColor = 'bg-dark-red';
            $title = 'Испытания в лаборатории завершены. Оплата не поступила.';
        }

        $confirm = empty($row['confirm'])? 0 : 1;

        if (!empty($row['c_count']) && empty($confirm)) {
            $bgColor = 'bg-dark-blue';
            $title = 'Заявка на стадии проверки ТЗ';
        }

        return [
            'title' => $title,
            'color' => $bgColor,
        ];
    }


    /**
     * @param int $dealId
     */
    public function deleteContractFromRequest(int $dealId)
    {
        $this->DB->Query("DELETE FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL` = {$dealId}");
    }


    /**
     * @param int $orderId
     * @param float $money
     */
    public function setAddedFinanceHistory(int $orderId, float $money)
    {
        $data = [
            'dogovor_id' => $orderId,
            'action' => 'добавлено',
            'money' => $money,
            'user_id' => $_SESSION['SESS_AUTH']['USER_ID'],
            'datetime' => date('Y-m-d H:i:s'),
        ];

        $dateSql = $this->prepearTableData('ulab_history_finance_order', $data);

        $this->DB->Insert('ulab_history_finance_order', $dateSql);
    }


    public function setDebitedFinanceHistory(int $orderId, float $money, int $dealId)
    {
        $data = [
            'dogovor_id' => $orderId,
            'deal_id' => $dealId,
            'action' => 'списано',
            'money' => $money,
            'user_id' => $_SESSION['SESS_AUTH']['USER_ID'],
            'datetime' => date('Y-m-d H:i:s'),
        ];

        $dateSql = $this->prepearTableData('ulab_history_finance_order', $data);

        $this->DB->Insert('ulab_history_finance_order', $dateSql);
    }


    /**
     * @param int $orderId
     * @return array
     */
    public function getFinanceHistory(int $orderId)
    {
        $userModel = new User();

        $result = [];

        $sql = $this->DB->Query(
            "select fo.*, tz.REQUEST_TITLE from ulab_history_finance_order as fo
                    left join ba_tz as tz on tz.ID_Z = fo.deal_id
                    where fo.dogovor_id = {$orderId}"
        );

        while ($row = $sql->Fetch()) {
            $user = $userModel->getUserData($row['user_id']);
            $row['user_name'] = $user['short_name'];
            $row['date'] = date('d.m.Y', strtotime($row['datetime']));

            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param int $orderId
     * @param int $dealId
     * @param float $money
     * @return array|bool[]
     */
    public function addPay(int $orderId, int $dealId, float $money)
    {
        $requestModel = new Request();

        $requestInfo = $requestModel->getTzByDealId($dealId);
        $orderInfo = $this->get($orderId);

        if ( $money > $orderInfo['finance'] ) {
            return [
                'success' => false,
                'error' => "Недостаточно средств на счете"
            ];
        }

        $summ = $requestInfo['price_discount'] - $requestInfo['OPLATA'];
        if ( $money > $summ ) {
            return [
                'success' => false,
                'error' => "Переплата. Сумма: {$money}. Необходимо: {$summ}/ {$requestInfo['price_discount']}"
            ];
        }

        if ( $money < 0 ) {
            return [
                'success' => false,
                'error' => "Невозможно"
            ];
        }

        $this->DB->Query("update ba_tz set `OPLATA` = `OPLATA` + {$money} where ID_Z = {$dealId}");
        $this->DB->Query("update DOGOVOR set `finance` = `finance` - {$money} where ID = {$orderId}");

        $this->setDebitedFinanceHistory($orderId, $money, $dealId);

        return [
            'success' => true,
        ];
    }


    /**
     * @param int $orderId
     * @param float $money
     */
    public function addFinance(int $orderId, float $money)
    {
        $result = $this->DB->Query("update DOGOVOR set `finance` = `finance` + {$money} where id = {$orderId}");

        if ( $result !== false ) {
            $this->setAddedFinanceHistory($orderId, $money);
        }
    }


    /**
     * @param int $orderId
     * @return array|false
     */
    public function getContractById(int $orderId)
    {
        return $this->DB->Query("SELECT *, CONCAT(CONTRACT_TYPE, ' №', NUMBER, ' от ', DATE_FORMAT(`DATE`, '%d.%m.%Y')) AS cont FROM `DOGOVOR` WHERE ID = {$orderId}")->Fetch();
    }

    /**
     * @param $dealId
     * @return array|false
     */
    public function getContractDealByDealId($dealId)
    {
        return $this->DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL` = {$dealId}")->Fetch();
    }


    /**
     * @param int $dealId
     * @param $contractId
     */
    public function setContractToRequest(int $dealId, $contractId)
    {
        $data = [
            'ID_DEAL' => $dealId,
            'ID_CONTRACT' => $contractId,
        ];

        $this->DB->Insert('DEALS_TO_CONTRACTS', $data);
    }


    public function getDataJournalRequest(array $filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'b.ID',
            'dir' => 'DESC'
        ];

        if ( !empty($filter) ) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if ( !empty($filter['search']) ) {
                // Заявка
                if ( isset($filter['search']['requestTitle']) ) {
                    $where .= "b.REQUEST_TITLE LIKE '%{$filter['search']['requestTitle']}%' AND ";
                }
                if ( isset($filter['search']['order_id']) ) {
                    $where .= "dtc.`ID_CONTRACT` = '{$filter['search']['order_id']}' AND ";
                }

                $stageArr = [
                    "b.OPLATA < b.PRICE AND ",
                    "b.OPLATA >= b.PRICE AND "
                ];
                // стадии
                if ( isset($filter['search']['stage']) ) {
                    $where .= $stageArr[$filter['search']['stage']];
                }
            }

            // работа с сортировкой
            if ( !empty($filter['order']) ) {
                if ( $filter['order']['dir'] === 'asc' ) {
                    $order['dir'] = 'ASC';
                }

            }

            // работа с пагинацией
            if ( isset($filter['paginate']) ) {
                $offset = 0;
                // количество строк на страницу
                if ( isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0 ) {
                    $length = $filter['paginate']['length'];

                    if ( isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0 ) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT {$offset}, {$length}";
                }
            }
        }
        $where .= "1 ";

        $result = [];

        $data = $this->DB->Query(
            "SELECT 
                        dtc.`ID_DEAL`, dtc.ID_CONTRACT, 
                        b.`REQUEST_TITLE`, b.`DATE_CREATE`, b.`PRICE`, b.ACCOUNT, b.DISCOUNT, b.price_discount, b.OPLATA, b.STAGE_ID, b.`ID`, b.discount_type, b.ID_Z,
                        tz.ACTUAL_VER, tz.ID as tz_doc_id, tz.pdf as tz_pdf
                    FROM `DEALS_TO_CONTRACTS` dtc, `ba_tz` b
                    LEFT JOIN TZ_DOC tz ON b.ID = tz.TZ_ID
                    WHERE b.ID_Z = dtc.`ID_DEAL` and {$where}
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT b.ID val
                    FROM `DEALS_TO_CONTRACTS` dtc, `ba_tz` b
                    LEFT JOIN TZ_DOC tz ON b.ID = tz.TZ_ID
                    WHERE b.ID_Z = dtc.`ID_DEAL` AND dtc.`ID_CONTRACT` = '{$filter['search']['order_id']}'"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT b.ID val
                    FROM `DEALS_TO_CONTRACTS` dtc, `ba_tz` b
                    LEFT JOIN TZ_DOC tz ON b.ID = tz.TZ_ID
                    WHERE b.ID_Z = dtc.`ID_DEAL` AND {$where}"
        )->SelectedRowsCount();

        $i = 0;
        while ($row = $data->Fetch()) {
            $row['num'] = ++$i;
            $row['is_show_finance'] = in_array($_SESSION["SESS_AUTH"]["USER_ID"], [88, 25, 33]);

            $row['date'] = StringHelper::dateRu($row['DATE_CREATE']);

            $price = 0;
            if ( !empty($row['PRICE']) ) {
                $price = (float)$row['PRICE'];

                if ( $row['discount_type'] == 'percent' ) {
                    $price -= $price * $row['DISCOUNT'] / 100;
                } elseif ( $row['discount_type'] == 'rub' ) {
                    $price -= $row['DISCOUNT'];
                }

                $row['PRICE'] = StringHelper::priceFormatRus($row['PRICE']);
            } else {
                $row['PRICE'] = '--';
            }

            if ( $price < 0 ) {
                $price = 0;
            }

            $row['price_discount'] = $price;
            $row['price_ru'] = StringHelper::priceFormatRus($price);

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


    /**
     * @param $orderId
     * @return array
     */
    public function getDealToContractByContractId($orderId)
    {
        $request = new Request();
        $result = [];
        $stageArrLose = ['LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13'];

        $res = $this->DB->Query(
            "SELECT 
                    dtc.`ID_DEAL`, 
                    b.`REQUEST_TITLE`, b.`DATE_CREATE`, b.`PRICE`, b.ACCOUNT, b.DISCOUNT, b.OPLATA, b.STAGE_ID, b.`ID`, b.discount_type,
                    tz.ACTUAL_VER, tz.ID as tz_doc_id, tz.pdf as tz_pdf
                FROM `DEALS_TO_CONTRACTS` dtc, `ba_tz` b
                LEFT JOIN TZ_DOC tz ON b.ID = tz.TZ_ID
                WHERE dtc.`ID_CONTRACT` = {$orderId} AND b.ID_Z = dtc.`ID_DEAL`");

        while ($row = $res->Fetch()) {
            $row['STAGE_ID'] = $request->getStageRequestById($row['ID_DEAL']);
            if (in_array($row['STAGE_ID'], $stageArrLose)) {
                $row['status'] = 'lose';
            } elseif ($row['STAGE_ID'] == 'WON') {
                $row['status'] = 'won';
            } else {
                $row['status'] = 'work';
            }

            $price = 0;
            if ( !empty($row['PRICE']) ) {
                $price = (float)$row['PRICE'];

                if ( $row['discount_type'] == 'percent' ) {
                    $price -= $price * $row['DISCOUNT'] / 100;
                    $row['discount_type'] = '%';
                } elseif ( $row['discount_type'] == 'rub' ) {
                    $price -= $row['DISCOUNT'];
                    $row['discount_type'] = '';
                }

                $row['PRICE'] = StringHelper::priceFormatRus($row['PRICE']);
            } else {
                $row['PRICE'] = '₽';
            }

            if ( $price < 0 ) {
                $price = 0;
            }

            $row['price_discount'] = $price;

            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param $orderID
     * @param $data
     * @return mixed
     */
    public function setOrderById($orderID, $data)
    {
        $data['CONTRACT_TYPE'] = $this->quoteStr($this->DB->ForSql($data['CONTRACT_TYPE']));
        $data['NUMBER'] = $this->quoteStr($this->DB->ForSql($data['NUMBER']));
        $data['number_client'] = $this->quoteStr($this->DB->ForSql($data['number_client']));
        $data['DATE'] = $this->quoteStr($this->DB->ForSql($data['DATE']));
        $upd = $this->DB->Update('DOGOVOR', $data, 'WHERE ID=' . $orderID);

        return $upd;
    }

    /**
     * @param $contractID
     * @return mixed
     */
    public function getClientPrice($contractID)
    {
        $res = $this->DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT`= {$contractID}")->Fetch();

        return $res;
    }

    /**
     * @param $contractID
     * @return mixed
     */
    public function setClientPrice($contractID)
    {
        $res_oa = $this->DB->Query("SELECT * FROM `ba_gost` ORDER BY `PRICE` ASC");
        while($gost = $res_oa->Fetch())
        {
            if(!$gost['NON_ACTUAL'] && $gost['GOST_TYPE']!='TU_standart' && $gost['GOST_TYPE']!='TU_research' && $gost['GOST_TYPE']!='TU_group')
            {
                $this->DB->Query("INSERT INTO `PRICE_FOR_CONTRACTS` SET 
										`ID_CONTRACT` = {$contractID},
										`ID_GOST` = {$gost['ID']},
										`PRICE` = {$gost['PRICE']}");
            }

            $res = $this->DB->LastId();
        }

        return $res;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setOplata($data)
    {
        $data['PAY_DATE'] = $this->quoteStr($this->DB->ForSql($data['PAY_DATE']));
        return $this->DB->Insert('PAYMENTS_TO_CONTRACTS', $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function getPaymentOnContract($id)
    {
        $result = [];
        $sum = 0;
        $res = $this->DB->Query("SELECT PAY_DATE, PAY_SUMM FROM `PAYMENTS_TO_CONTRACTS` WHERE `ID_CONTRACT` = {$id}");

        while ($row = $res->Fetch()) {
            $sum += $row['PAY_SUMM'];
            $row['PAY_SUMM'] = StringHelper::priceFormatRus($row['PAY_SUMM']);
            $row['PAY_DATE'] = date('d.m.Y', strtotime($row['PAY_DATE']));
            $result['data'][] = $row;
        }

        $result['paymentSum'] = $sum;

        return $result;
    }


    public function uploadTzDocPdf($file, $tzId)
    {
        return $this->saveFile(PROTOCOL_PATH . "archive_tz/{$tzId}", $file["name"], $file["tmp_name"]);
    }


    public function uploadPdf($file)
    {
        return $this->saveFile($_SERVER["DOCUMENT_ROOT"] . "/pdf", $file["name"], $file["tmp_name"]);
    }


    public function uploadDogovorDoc($file, $typeId, $dogovorId)
    {
        $directory = $this->DB->Query("SELECT save_directory FROM dogovor_document_types WHERE id = $typeId")->Fetch()['save_directory'];

        return $this->saveFile($_SERVER["DOCUMENT_ROOT"] . $directory . "/{$dogovorId}", $file["name"], $file["tmp_name"]);
    }


    public function saveTzDocPdf($fileName, $tzDocId)
    {
        $this->DB->Update('TZ_DOC', ['pdf' => $this->quoteStr($this->DB->ForSql($fileName))], "WHERE ID = {$tzDocId}");
    }


    public function saveDogovorPdf($fileName, $dogovorId)
    {
        $this->DB->Update('DOGOVOR', ['PDF' => $this->quoteStr($this->DB->ForSql($fileName))], "WHERE ID = {$dogovorId}");
    }


    public function saveDocToDogovor($fileName, $data)
    {
        $currentDate = date('Y-m-d');

        $this->DB->Query("INSERT INTO dogovor_documents(actual_version, type_id, dogovor_id, is_scan, date)
                            VALUES('{$fileName}', '{$data['type_id']}', '{$data['dogovor_id']}', '{$data['is_scan']}', '{$currentDate}')");
    }

    /**
     * @param $dealId
     * @return false|string
     */
    public function getOrderByDealId($dealId)
    {
        global $DB;
        $result = $DB->Query("SELECT DOGOVOR_NUM FROM `ba_tz` WHERE ID_Z = {$dealId} ")->Fetch();

        if (!empty($result)) {
            $idDogovorArr = explode('ID', $result['DOGOVOR_NUM']);

            if (isset($idDogovorArr[1])) {
                $idDogovor = $idDogovorArr[1];
            } else {
                $idDogovor = $idDogovorArr[0];
            }
        } else {
            return false;
        }


        $contract = $DB->Query("SELECT * FROM `DOGOVOR` WHERE ID = '{$idDogovor}'")->Fetch();
        if (!empty($contract)) {

            if($contract['custom_number']) {
                return $contract['CONTRACT_TYPE'] . ' №' . $contract['number_client'] . ' от ' . date('d.m.Y', strtotime($contract['DATE']));
            }else {
                return $contract['CONTRACT_TYPE'] . ' №' . $contract['NUMBER'] . ' от ' . date('d.m.Y', strtotime($contract['DATE']));
            }
        } else {
            return false;
        }

    }

    /**
     * @param $dealId
     * @param $tzId
     * @return false|string
     */
    public function getOrderInvoiceByDealId($dealId, $tzId)
    {
        global $DB;
        $result = $DB->Query("SELECT DOGOVOR_NUM FROM `ba_tz` WHERE ID_Z = {$dealId} ")->Fetch();

        if ( !empty($result) ) {
            $idDogovorArr = explode('ID', $result['DOGOVOR_NUM']);

            if (isset($idDogovorArr[1])) {
                $idDogovor = $idDogovorArr[1];
            } else {
                $idDogovor = $idDogovorArr[0];
            }
        } else {
            return false;
        }

        $contract = $DB->Query("SELECT * FROM `DOGOVOR` WHERE ID = '{$idDogovor}'")->Fetch();
        if (!empty($contract)) {
            $invoice = $DB->Query("SELECT * FROM `INVOICE` WHERE TZ_ID = '{$tzId}' ORDER BY id DESC")->Fetch();
            if ($contract['custom_number']) {
                if (!empty($invoice)) {
                    return $contract['CONTRACT_TYPE'] . ' №' . $contract['number_client'] . ' от ' . date('d.m.Y', strtotime($invoice['DATE']));
                }
                return $contract['CONTRACT_TYPE'] . ' №' . $contract['number_client'] . ' от ' . date('d.m.Y', strtotime($contract['DATE']));
            } else {
                if (!empty($invoice)) {
                    return $contract['CONTRACT_TYPE'] . ' №' . $contract['NUMBER'] . ' от ' . date('d.m.Y', strtotime($invoice['DATE']));
                }
                return $contract['CONTRACT_TYPE'] . ' №' . $contract['NUMBER'] . ' от ' . date('d.m.Y', strtotime($contract['DATE']));
            }
        } else {
            return false;
        }

    }

    public function changeOrderByHeadRequest($idHeadRequest, $dealId)
    {
        $order = $this->getContractDealByDealId($idHeadRequest);

        $orderChild = $this->getContractDealByDealId($dealId);

        $dataOrderDealsToContract = [
            'ID_CONTRACT' => $order['ID_CONTRACT'],
            'ID_DEAL' => $dealId
        ];

        $HeaderBa_tzData = $this->DB->Query("SELECT DOGOVOR_NUM, CONTRACT_ID, NO_CONTRACT, DOGOVOR_TABLE FROM ba_tz WHERE ID_Z = {$idHeadRequest}")->Fetch();

        $dataOrderBa_tz = $this->prepearTableData('ba_tz', $HeaderBa_tzData);

        $this->DB->Update('ba_tz', $dataOrderBa_tz, "where ID_Z = $dealId");
        if (!empty($orderChild)) {
            $this->DB->Update('DEALS_TO_CONTRACTS', $dataOrderDealsToContract, "where ID_DEAL = $dealId");
        } else {
            $this->DB->Insert('DEALS_TO_CONTRACTS', $dataOrderDealsToContract);
        }
    }

    public function orderCancelByOrderId($id)
    {
        $order = $this->DB->Query("UPDATE DOGOVOR SET `IS_ACTION` = !`IS_ACTION` WHERE ID = {$id}");
    }

    public function deletePdfTZ($id)
    {
        $tz = $this->DB->Query("SELECT * FROM TZ_DOC WHERE ID = {$id}")->Fetch();

        if (unlink($_SERVER["DOCUMENT_ROOT"] . "/protocol_generator/archive_tz/{$tz['TZ_ID']}/{$tz['pdf']}")) {
            $this->DB->Query("UPDATE TZ_DOC SET pdf = '' WHERE ID = {$id}");

            return true;
        }

        return false;
    }

    /**
     * @return int|mixed
     */
    public function getCurrentNumber()
    {
        $order = $this->DB->Query("SELECT SUBSTRING_INDEX(NUMBER, '/', 1) as num FROM `DOGOVOR` WHERE (`CLIENT_NUMBER`='0' OR `CLIENT_NUMBER` IS NULL) AND CONTRACT_TYPE = 'Договор'  ORDER BY `ID` DESC LIMIT 1")->Fetch();

        return $order['num']+1;
    }

    /**
     * @return int|mixed
     */
    public function getCurrentAccountNumber()
    {
        $curYear = date('Y');
        $invoice = $this->DB->Query("SELECT MAX(NUMBER) as num FROM `INVOICE` WHERE year(DATE) = {$curYear} ORDER BY `ID` DESC LIMIT 1")->Fetch();

        return $invoice['num']+1;
    }

    public function getCurrentNumberInvoiceOffer()
    {
        $curYear = date('Y');
        if ($curYear == '2026') {
            $order = $this->DB->Query("SELECT SUBSTRING_INDEX(NUMBER, '/', 1) as num 
                                        FROM `DOGOVOR` 
                                        WHERE (`CLIENT_NUMBER`='0' OR `CLIENT_NUMBER` IS NULL) 
                                        AND type_id = 2
                                        AND DATE > '2026-02-02'
                                        ORDER BY `ID` DESC 
                                        LIMIT 1")->Fetch();
        } else {
            $order = $this->DB->Query("SELECT SUBSTRING_INDEX(NUMBER, '/', 1) as num FROM `DOGOVOR` WHERE (`CLIENT_NUMBER`='0' OR `CLIENT_NUMBER` IS NULL) AND type_id = 2 AND YEAR(DATE) = '{$curYear}' ORDER BY `ID` DESC LIMIT 1")->Fetch();
        }
        if(!$order['num']) {
            return '01';
        }

        if($order['num'] < 9) {
            return '0' . ((int)$order['num'] + 1);
        }

        return $order['num']+1;
    }

    public function untieOrder($orderId)
    {
        $this->DB->Query("UPDATE DOGOVOR SET PDF = '' WHERE ID = '{$orderId}'");
    }

    public function getOrderTypes()
    {
        $result = [];

        $response = $this->DB->Query("SELECT * FROM dogovor_types");

        while($row = $response->Fetch()){
            $result []= $row;
        }

        return $result;
    }

    public function createNewDogovor($data)
    {
        $date = date('Y-m-d H:i:s');

        $currentYear = date('y');
        $currentNumber = $data['number'] . '/' . $currentYear;

        $type = "";

        if($data['type'] == 2) {
            $type = 'Счет-оферта';
        }else {
            $type = 'Договор';
        }

        if($data['custom_number']) {
            $currentNumber = $data['next_number'] . '/' . $currentYear;

            $this->DB->Query(
                "
            INSERT INTO DOGOVOR(NUMBER, DATE, CLIENT_ID, IS_ACTION, CONTRACT_TYPE, type_id, custom_number, dogovor_type_id, signatory, add_in_journal, number_client, belonging_id)
            VALUES('{$currentNumber}', '{$date}', '{$data['client_id']}', '1', '{$type}'
            , '{$data['type']}', '{$data['custom_number']}', '{$data['type']}', '{$data['assigned_id']}', '1', '{$data['number']}', '{$data['belonging']}')
            "
            );
        }else {
            $this->DB->Query(
                "
            INSERT INTO DOGOVOR(NUMBER, DATE, CLIENT_ID, IS_ACTION, CONTRACT_TYPE, type_id, custom_number, dogovor_type_id, signatory, add_in_journal, belonging_id)
            VALUES('{$currentNumber}', '{$date}', '{$data['client_id']}', '1', '{$type}'
            , '{$data['type']}', '{$data['custom_number']}', '{$data['type']}', '{$data['assigned_id']}', '1', '{$data['belonging']}')
            "
            );
        }
    }

    public function getPaymentTypes()
    {
        $result = [];

        $response = $this->DB->Query("SELECT * FROM dogovor_payment");

        while($row = $response->Fetch()) {
            $result []= $row;
        }

        return $result;
    }

    public function setPaymentType($data)
    {
        $this->DB->Query("UPDATE DOGOVOR SET payment_id = '{$data['payment_id']}' WHERE ID = '{$data['dogovor_id']}'");
    }

    public function setDogovorPrice($data)
    {
        $this->DB->Query("UPDATE DOGOVOR SET dogovor_price = '{$data['dogovor_price']}' WHERE ID = '{$data['dogovor_id']}'");
    }

    public function setDogovorDate($data)
    {
        if ($data['date_end'] == null) {
            $this->DB->Query("UPDATE DOGOVOR SET date_end = NULL WHERE ID = '{$data['dogovor_id']}'");
        } else {
            $this->DB->Query("UPDATE DOGOVOR SET date_end = '{$data['date_end']}' WHERE ID = '{$data['dogovor_id']}'");
        }
    }

    public function setSpecialNotes($data)
    {
        $this->DB->Query("UPDATE DOGOVOR SET special_notes = '{$data['special_notes']}' WHERE ID = '{$data['dogovor_id']}'");
    }

    public function getDocsToDogovor($dogovorId)
    {
        $result = [];

        $response = $this->DB->Query(
            "
            SELECT dd.is_scan, ddt.id, ddt.name, dd.actual_version, dd.dogovor_id, ddt.save_directory, DATE_FORMAT(dd.date, '%d.%m.%Y') as 'date',
            dd.id as 'document_id'
            FROM dogovor_documents dd 
            INNER JOIN dogovor_document_types ddt ON ddt.id = dd.type_id
            WHERE dd.dogovor_id = '{$dogovorId}'
            "
        );

        while($row = $response->Fetch()) {
            if($row['is_scan']) {
                $row['type_format'] = 'Скан';
                $result[$row['id']]['has_scan'] = true;
            }else {
                $row['type_format'] = 'Оригинал';
                $result[$row['id']]['has_original'] = true;
            }

            $row['exist_file'] = file_exists($_SERVER["DOCUMENT_ROOT"] . $row['save_directory'] . '/' . $row['dogovor_id'] . '/' . $row['actual_version']);


            $result[$row['id']]['docs'][] = $row;
        }

        $result[1]['send'] = $this->getSendedDocsForDogovor($dogovorId, 1);
        $result[2]['send'] = $this->getSendedDocsForDogovor($dogovorId, 2);
        $result[3]['send'] = $this->getSendedDocsForDogovor($dogovorId, 3);

        return $result;
    }

    public function getSendedDocsForDogovor($dogovorId, $typeId)
    {
        $result = [];

        $response = $this->DB->Query(
            "
            SELECT dds.is_scan, ddt.id, ddt.name, dds.dogovor_id, ddt.save_directory, DATE_FORMAT(dds.date, '%d.%m.%Y') as 'date',
            dds.id as 'document_id'
            FROM dogovor_document_send dds
            INNER JOIN dogovor_document_types ddt ON ddt.id = dds.type_id
            WHERE dds.dogovor_id = '{$dogovorId}' AND dds.type_id = '{$typeId}'  
            "
        );

        while($row = $response->Fetch()) {
            if($row['is_scan']) {
                $row['type_format'] = 'Скан';
            }else {
                $row['type_format'] = 'Оригинал';
            }

            $result[] = $row;
        }

        return $result;
    }

    public function removeDocId($docId)
    {
        $data = $this->DB->Query(
            "
            SELECT ddt.save_directory, dd.actual_version, dd.dogovor_id
            FROM dogovor_documents dd
            INNER JOIN dogovor_document_types ddt ON ddt.id = dd.type_id
            WHERE dd.id = '{$docId}'
            "
        )->Fetch();

        unlink($_SERVER["DOCUMENT_ROOT"] . $data['save_directory'] . "/" . $data['dogovor_id'] . "/" . $data['actual_version']);

        $this->DB->Query("DELETE FROM dogovor_documents WHERE id = '{$docId}'");
    }

    public function changeNeedlDoc($typeId, $newValue, $dogovorId)
    {
        $typeNameInDB = $typeId == 1 ? 'need_additional' : 'need_protocol';

        $this->DB->Query(
            "
            UPDATE DOGOVOR SET {$typeNameInDB} = '{$newValue}' WHERE ID = '{$dogovorId}'
            "
        );
    }

    public function sendToClient($data)
    {
        $currentDate = date('Y-m-d');
        $currentUser = $_SESSION['SESS_AUTH']['USER_ID'];

        $this->DB->Query("INSERT INTO dogovor_document_send(type_id, dogovor_id, date, user_id, is_scan)
                            VALUES('{$data['type_id']}', '{$data['dogovor_id']}', '{$currentDate}'
                                , '{$currentUser}', '{$data['is_scan']}')");
    }

    public function deleteSended($document_id)
    {
        $this->DB->Query("DELETE FROM dogovor_document_send WHERE id = '{$document_id}'");
    }

    public function deleteScanDogovor($dogovor_id)
    {
        $actualVersion = $this->DB->Query("SELECT actual_version FROM dogovor_documents WHERE dogovor_id = '{$dogovor_id}' AND type_id = 3 AND is_scan = 1")->Fetch()['actual_version'];

        unlink("/protocol_generator/archive_dogovor/{$actualVersion}");

//        $this->pre($dogovor_id, true);

        $this->DB->Query("DELETE FROM dogovor_documents WHERE dogovor_id = '{$dogovor_id}' AND type_id = 3 AND is_scan = 1");
    }

    public function getBelongingList()
    {
        $result = [];

        $response = $this->DB->Query(
            "
            SELECT * 
            FROM dogovor_belonging  
            "
        );

        while($row = $response->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function updateClientData($data)
    {
        $company = new Company();
        $this->DB->Query(
            "
            UPDATE b_crm_requisite
            SET NAME = '{$data['company']}',
                RQ_COMPANY_FULL_NAME = '{$data['CompanyFullName']}',
                RQ_INN = '{$data['INN']}',
                RQ_KPP = '{$data['KPP']}',
                RQ_OGRN = '{$data['OGRN']}',
                RQ_ACCOUNTANT = '{$data['ADDR']}',
                RQ_EMAIL = '{$data['POST_MAIL']}',
                RQ_PHONE = '{$data['PHONE']}',
                RQ_NAME = '{$data['CONTACT']}',
                RQ_COMPANY_REG_DATE = '{$data['Position2']}',
                RQ_DIRECTOR = '{$data['DirectorFIO']}'
            WHERE ENTITY_ID = '{$data['id']}'
            "
        );
        $dataBank = [
            'ENTITY_ID'                 => $data['id'],
            'ENTITY_TYPE_ID'            => CCrmOwnerType::Company,
            'PRESET_ID'                 => 1,
            'NAME'                      => $data['company'],
            'SORT'                      => 500,
            'ACTIVE'                    => 'Y',
            'RQ_INN'                    => $data['INN'],
            'RQ_ACCOUNTANT'             => $data['ADDR'],
            'RQ_OGRN'                   => $data['OGRN'],
            'RQ_OGRNIP'                 => $data['OGRNIP'],
            'RQ_EMAIL'                  => $data['POST_MAIL'],
            'RQ_PHONE'                  => $data['PHONE'],
            'RQ_NAME'                   => $data['CONTACT'],
            'RQ_KPP'                    => $data['KPP'],
            'RQ_COMPANY_REG_DATE'       => $data['Position2'],
            'RQ_COMPANY_FULL_NAME'      => $data['CompanyFullName'],
            'RQ_DIRECTOR'               => $data['DirectorFIO'],
            'RQ_ACC_NUM'                => $data['RaschSchet'],
            'RQ_COR_ACC_NUM'            => $data['KSchet'],
            'RQ_BIK'                    => $data['BIK'],
            'RQ_BANK_NAME'              => $data['BankName'],
            'RQ_FIRST_NAME'             => $data['EMAIL'],
            'RQ_COMPANY_NAME'           => $data['mailingAddress'],
            'RQ_ADDR'                   => [
                1 => [
                    'ADDRESS_1' => $data["ACTUAL_ADDRESS"],
                    'ADDRESS_2' => $data['ACTS_BASIS'],
                    'REGION'    => $data['OGRNIP']
                ]
            ],
            'COMMENTS'                  => $data['l_schet'],
        ];
        $o = $this->DB->Query("SELECT * FROM `b_crm_bank_detail` WHERE `ENTITY_ID` = {$data['id']}")->Fetch();
        $company->setRequisiteByCompanyId($data['id'], $dataBank);
    }
}
