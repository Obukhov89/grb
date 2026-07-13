<?php

use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Field;
use NcJoes\OfficeConverter\OfficeConverter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use Mpdf\Mpdf as MpdfLib;
use \Bitrix\Main\Loader;




class DocumentGenerator extends Model
{
    // ширина документа в твипах
    protected const WIDTH_DOC_TWIP = 9780;
    // ширина документа альбомная версия в твипах
    protected const WIDTH_DOC_ALBUM_TWIP = 14428;
    // высота строки в твипах
    protected const ROW_HEIGHT = 557;

    public function __construct()
    {
        parent::__construct();

        Loader::includeModule('documentgenerator');
    }


    /**
     * @param int $sizeInCm
     * @return float
     */
    protected static function centimeterSizeToTwips($sizeInCm = 1)
    {
        return ($sizeInCm * 565.217);
    }


    /**
     * @param int $percent
     * @param bool $isAlbum
     * @return float|int
     */
    protected static function percentToTwips($percent = 100, $isAlbum = false)
    {
        if ($percent <= 0) {
            return 0;
        }

        $width = $isAlbum ? self::WIDTH_DOC_ALBUM_TWIP : self::WIDTH_DOC_TWIP;

        if ($percent >= 100) {
            return $width;
        }

        return $width * ($percent / 100);
    }


    /**
     * * Подготовка данных для коммерческого предложения
     * @param $dealID
     */
    public function commercialOffer($dealID)
    {
        $companyModel = new Company();
        $requestModel = new Request();
        $historyModel = new History();
        $userModel = new User();
        $gostModel = new Gost();

        $companyInformation = $companyModel->getRequisiteByDealId($dealID);

        //        $this->pre(
        //            $companyInformation, true
        //        );

        $dealInformation = $requestModel->getTzByDealId($dealID);
        //		$this->pre($dealInformation);

        $TZ_ID = $dealInformation['ID'];

        $res_kp = $this->DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();

        $date = date('Y-m-d H:i:s');

        $dateKP = date('d-m-Y_H-i-s', strtotime($date));

        $curdate = "КП_" . $dateKP;
        $curdateEng = "KP_" . $dateKP;

        if ($res_kp) {
            $this->DB->Query("UPDATE `KP` SET `DATE`= NOW(), `ACTUAL_VER` ='" . $curdate . "' WHERE `TZ_ID`=" . $TZ_ID);
            $str_type = 'Обновление комерческого предложения';
        } else {
            $this->DB->Query("INSERT INTO `KP` (`DATE`, `TZ_ID`, `ACTUAL_VER`) VALUES (NOW(), " . $TZ_ID . ", '" . $curdate . "')");
            $str_type = 'Формирование комерческого предложения';
        }

        $currentUser = $userModel->getCurrentUser();

        $history = [
            'DATE' => $date,
            'ASSIGNED' => $currentUser['NAME'] . ' ' . $currentUser['LAST_NAME'],
            'TZ_ID' => $TZ_ID,
            'USER_ID' => $currentUser['ID'],
            'TYPE' => $str_type,
            'REQUEST' => $dealInformation['REQUEST_TITLE'],
        ];

        $historyModel->addHistory($history);

        $str_num_request = !empty($dealInformation['REQUEST_TITLE']) ? explode(' ', $dealInformation['REQUEST_TITLE'])[1] : '';

        $res_kp = $this->DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();

        if ($dealInformation['DAY_TO_TEST']) {
            $cur = date('d.m.Y'); //текущая дата
            switch ($dealInformation['type_of_day']) {
                case 'day':
                    $typeDay = StringHelper::num_word($dealInformation['DAY_TO_TEST'], 'календарный день', 'календарных дня', 'календарных дней');
                    $current_day = 'составляет ' . $dealInformation['DAY_TO_TEST'] . ' ' . $typeDay;
                    break;
                case 'work_day':
                    $typeDay = StringHelper::num_word($dealInformation['DAY_TO_TEST'], 'рабочий день', 'рабочих дня', 'рабочих дней');
                    $current_day = 'составляет ' . $dealInformation['DAY_TO_TEST'] . ' ' . $typeDay;
                    break;
                case 'month':
                    $typeDay = StringHelper::num_word($dealInformation['DAY_TO_TEST'], 'месяц', 'месяца', 'месяцев');
                    $current_day = 'составляет ' . $dealInformation['DAY_TO_TEST'] . ' ' . $typeDay;
                    break;
                case 'custom':
                    $typeDay = 'до ' . date('d.m.Y', strtotime($dealInformation['DAY_TO_TEST']));
                    $current_day = $typeDay;
                    break;
                default:
                    $typeDay = '';
            }

            //кол-во пришедших дней

            $day_to_test_text = "Срок проведения испытаний " . $current_day . " с момента поступления проб (образцов) в ИЦ, подписания договора, технической заявки и соблюдения условий оплаты по Договору.";
            $day_to_test_note = 'Срок проведения испытаний является ориентировочным и может быть скорректирован по согласованию сторон.';
        } else {
            $day_to_test_text = '';
            $day_to_test_note = '';
        }
        $discount = '';
        if (!empty($dealInformation['DISCOUNT'])) {
            $discount = "с учетом скидки";
        }

        $stringSum = '';

        if ($dealInformation['is_nds']) {
            if ($dealInformation['is_nds_7']) {
                $stringSum = ucfirst(StringHelper::num2str($dealInformation['price_discount'] * 1.07));
            } else {
                $stringSum = ucfirst(StringHelper::num2str($dealInformation['price_discount'] * 1.05));
            }
        } else {
            $stringSum = StringHelper::num2str($dealInformation['price_discount']);
        }

        $strSumArr = explode(' ', $stringSum);
        $rubIndex = 999999;

        $strEnd = '';
        $strStart = '';

        foreach ($strSumArr as $key => $value) {
            if ($value == 'рублей' || $value == 'рубля' || $value == 'рубль') {
                $rubIndex = $key;

                $strEnd .= $value;
            }

            if ($key > $rubIndex) {
                $strEnd .= ' ' . $value;
            }

            if ($key < $rubIndex) {
                $strStart .= ' ' . $value;
            }
        }

        $strStart = substr($strStart, 1);

        if ($dealInformation['is_nds']) {
            $sumValue = number_format($dealInformation['price_discount'] * 1.05, 2, ',', ' ');
            if ($dealInformation['is_nds_7']) {
                $sumValue = number_format($dealInformation['price_discount'] * 1.07, 2, ',', ' ');
            }
        } else {
            $sumValue = number_format($dealInformation['price_discount'], 2, ',', ' ');
        }

        $CommInformation = [
            'id' => $dealID,
            'num_request' => $str_num_request,
            'date' => $res_kp['DATE'],
            'curDate' => $curdate,
            'curDateEn' => $curdateEng,
            'SUM' => $sumValue,
            'nds' => $dealInformation['is_nds'],
            'nds_7' => $dealInformation['is_nds_7'],
            'NUM_KP' => $res_kp['ID'],
            'NUM_DATE' => date('d.m.Y', strtotime($res_kp['DATE'])),
            'NUM_REQUEST' => !empty($str_num_request) ? $str_num_request : '',
            'CLIENT_NAME' => preg_replace('/"([^"]*)"/', "«$1»", $companyInformation['NAME']),
            //'COMMENT_KP' => !empty($dealInformation['COMMENT_KP']) ? $dealInformation['COMMENT_KP'] : '',
            'DAY_TO_TEST_TEXT' => $day_to_test_text,
            'DAY_TO_TEST_NOTE' => $day_to_test_note . (!empty($dealInformation['COMMENT_KP']) ? ("\n<w:br/>" . $dealInformation['COMMENT_KP']) : ''),
            'discount' => $discount,
            'sum_string' => StringHelper::mb_strtoupper_first($strStart),
            'end_str' => $strEnd
        ];


        $arrGost = $gostModel->getGostMaterialByDealID($dealID);
        $this->createCommercialOfferDocument($CommInformation, $arrGost, $dealID, $dealInformation);
    }

    /**
     * Подготовка данных для приложения к договору
     * @param $dealID
     */
    public function technicalSpecification($dealID)
    {
        $companyModel = new Company();
        $requestModel = new Request();
        $historyModel = new History();
        $userModel = new User();
        $gostModel = new Gost();
        $orderModel = new Order();
        $requirementModel = new Requirement();

        $companyInformation = $companyModel->getRequisiteByDealId($dealID);

        $dealInformation = $requestModel->getTzByDealId($dealID);
        $dateStartIsp = $requestModel->getDateStartIsp($dealID);

        $materialsArray = $requestModel->getMaterialsForDeal($dealID);
        $materialsString = implode(', ', $materialsArray);

        $TZ_ID = $dealInformation['ID'];

        $date = date('Y-m-d H:i:s');

        $dateTZ = date('d-m-Y_H-i-s', strtotime($date));
        $dateStart = date('d.m.Y', strtotime($dealInformation['day_start']));

        $curdate = "ТЗ_" . $dealInformation['request_number'] . "_" . $dateTZ;
        $curdateEng = "TZ_" . $dealInformation['request_number'] . "_" . $dateTZ;

        $res_tz = $this->DB->Query("SELECT * FROM `TZ_DOC` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
        $lastNumber = 1;
        $sqlNumber = $this->DB->Query("SELECT max(`number`) as num FROM `TZ_DOC` WHERE year(`DATE`) = " . date('Y'))->Fetch();
        if (!empty($sqlNumber['num'])) {
            $lastNumber = $sqlNumber['num'] + 1;
        }
        if ($res_tz['ID']) {
            $this->DB->Query("UPDATE `TZ_DOC` SET `DATE`= NOW(), `ACTUAL_VER` ='" . $curdate . "' WHERE `TZ_ID`=" . $TZ_ID);
            $str_type = 'Обновление приложения к договору (ТЗ)';
        } else {
            $this->DB->Query("INSERT INTO `TZ_DOC` (`DATE`, `TZ_ID`, `ACTUAL_VER`, `number`) VALUES (NOW(), " . $TZ_ID . ", '" . $curdate . "', {$dealInformation['request_number']})");
            $str_type = 'Формирование приложения к договору (ТЗ)';
        }

        $currentUser = $userModel->getCurrentUser($dealID);

        $history = [
            'DATE' => $date,
            'ASSIGNED' => $currentUser['NAME'] . ' ' . $currentUser['LAST_NAME'],
            'TZ_ID' => $TZ_ID,
            'USER_ID' => $currentUser['ID'],
            'TYPE' => $str_type,
            'REQUEST' => $dealInformation['REQUEST_TITLE'],
        ];

        $historyModel->addHistory($history);

        if ($dealInformation['DAY_TO_TEST']) {
            $cur = date('d.m.Y'); //текущая дата
            switch ($dealInformation['type_of_day']) {
                case 'day':
                    $typeDay = StringHelper::num_word($dealInformation['DAY_TO_TEST'], 'календарный день', 'календарных дня', 'календарных дней');
                    $current_day = 'составляет ' . $dealInformation['DAY_TO_TEST'] . ' ' . $typeDay;
                    break;
                case 'work_day':
                    $typeDay = StringHelper::num_word($dealInformation['DAY_TO_TEST'], 'рабочий день', 'рабочих дня', 'рабочих дней');
                    $current_day = 'составляет ' . $dealInformation['DAY_TO_TEST'] . ' ' . $typeDay;
                    break;
                case 'month':
                    $typeDay = StringHelper::num_word($dealInformation['DAY_TO_TEST'], 'месяц', 'месяца', 'месяцев');
                    $current_day = 'составляет ' . $dealInformation['DAY_TO_TEST'] . ' ' . $typeDay;
                    break;
                case 'custom':
                    $typeDay = 'до ' . date('d.m.Y', strtotime($dealInformation['DAY_TO_TEST']));
                    $current_day = $typeDay;
                    break;
                default:
                    $typeDay = '';
            }

            //кол-во пришедших дней

            //			$day_to_test_text = "Срок проведения испытаний " . $current_day . " с момента поступления проб (образцов) в ИЦ, подписания договора, технической заявки и оплаты испытаний.";
            $day_to_test_text = "Специалисты готовы приступить к работе с " . $dateStart . " года. Срок проведения испытаний " . $current_day . " при условии поступления проб в Испытательный центр (далее ИЦ), подписания Договора, Технической заявки и оплаты за испытания.";
            $day_to_test_note = 'Срок проведения испытаний является ориентировочным и может быть скорректирован по согласованию сторон.';
        } else {
            $day_to_test_text = '';
            $day_to_test_note = '';
        }

        $monthStart = date('m', strtotime($dateStart));
        $yearStart = date('Y', strtotime($dateStart));
        $dayStart = date('d', strtotime($dateStart));


        $res_tz = $this->DB->Query("SELECT * FROM `TZ_DOC` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
        $str_num_request = !empty($dealInformation['REQUEST_TITLE']) ? explode(' ', $dealInformation['REQUEST_TITLE'])[1] : '';

        $dogovor_num = $orderModel->getOrderByDealId($dealID);
        $dogovor_num = explode('№', $dogovor_num)[1];

        $actProbe = $requirementModel->getActBase($dealID);

        $placeProbe = !empty($actProbe['PLACE_PROBE']) ? trim($actProbe['PLACE_PROBE']) . ';' : '-';
        $dateProbe = !empty($actProbe['DATE_PROBE']) ? date('d.m.Y', strtotime($actProbe['DATE_PROBE'])) : '';

        $placeDateStr = "{$placeProbe} {$dateProbe}";
        if ($dealInformation['is_nds']) {
            if ($dealInformation['is_nds_7']) {
                $sumFull = $dealInformation['price_discount'] * 1.07;

                $sum_nds = $dealInformation['price_discount'] * 0.07 . ' руб.';
                $nds_text = "(НДС 7% " . $sum_nds . ")";
            } else {
                $sumFull = $dealInformation['price_discount'] * 1.05;

                $sum_nds = $dealInformation['price_discount'] * 0.05 . ' руб.';
                $nds_text = "(НДС 5% " . $sum_nds . ")";
            }
        } else {
            $sumFull = $dealInformation['price_discount'];
        }

        $sumPrice = !empty($dealInformation['DISCOUNT']) ? 'с учетом скидки:' . $sumFull : $sumFull;
        //блок информации о заявке
        $test_for = '';
        $compliance = '';
        $protocol_as = '';
        //Испытания проводятся:
        if ($dealInformation['tests_for'] == 'own') {
            $test_for = 'для собственных нужд';
        } elseif ($dealInformation['tests_for'] == 'certification') {
            $test_for = 'для предоставления в орган по сертификации (сертификация, декларация продукции)';
        }

        //Указывать соответствие:
        if ($dealInformation['is_indicate_compliance'] == 0) {
            $compliance = 'нет';
        } elseif ($dealInformation['is_indicate_compliance'] == 1) {
            $compliance = 'да. ' . $dealInformation['compliance'];
        }

        //Результаты испытаний (исследований) оформить:
        if ($dealInformation['presented_as'] == 'protocol') {
            $protocol_as = 'протокол испытаний по ГОСТ Р 58973';
        } elseif ($dealInformation['presented_as'] == 'another') {
            $protocol_as = 'протокол испытаний по форме Заказчика';
        }

        //Необходимые сведения и документы для проведения испытаний
        $addInfoArr = json_decode($dealInformation['add_info']);
        $addInfoParam = [
            'passport' => 'особые сведения о физических, химических характеристиках продукции (состав, направления формования и т.д.)',
            'tu' => 'ТУ на производство продукции',
            'consist' => 'документ о качестве продукции',
            'another' => 'иное',
        ];

        $addInfoParam2 = [];
        foreach ($addInfoArr as $itemInfo) {
            $addInfoParam2[] = $addInfoParam[$itemInfo];
        }
        $addInfoResult = implode('<w:br/>', $addInfoParam2);

        $currentLocation = $dealInformation['location'] ? $dealInformation['location'] : '-';

        $threeArray = [];
        if ($dealInformation['tests_for'] == 'own') {
            $threeArray =
                [
                    '☒',
                    '☐',
                    '☐',
                    '☐',
                ];
        } else if ($dealInformation['tests_for'] == 'certification') {
            $threeArray =
                [
                    '☐',
                    '☒',
                    '☐',
                    '☐',
                ];
        } else if ($dealInformation['tests_for'] == 'declaration') {
            $threeArray =
                [
                    '☐',
                    '☐',
                    '☒',
                    '☐',
                ];
        } else if ($dealInformation['tests_for'] == 'forensic') {
            $threeArray =
                [
                    '☐',
                    '☐',
                    '☐',
                    '☒',
                ];
        } else {
            $threeArray =
                [
                    '☐',
                    '☐',
                    '☐',
                    '☐',
                ];
        }

        $fourArray = [];
        if (strval($dealInformation['is_indicate_compliance']) == '0') {
            $normativeGost = '__________________________';
            $normativeSTO = '_______________________________________________';
            $fourArray =
                [
                    '☐',
                    '☐',
                    '☒',
                ];
        } else if ($dealInformation['is_indicate_compliance'] == 1) {
            $normativeGost = $dealInformation['compliance'] ? $dealInformation['compliance'] : '-';
            $normativeSTO = '_______________________________________________';
            $fourArray =
                [
                    '☒',
                    '☐',
                    '☐',
                ];
        } else if ($dealInformation['is_indicate_compliance'] == 2) {
            $normativeGost = '__________________________';
            $normativeSTO = $dealInformation['tu_sto'] ? $dealInformation['tu_sto'] : '-';
            $fourArray =
                [
                    '☐',
                    '☒',
                    '☐',
                ];
        } else {
            $normativeGost = '__________________________';
            $normativeSTO = '_______________________________________________';
            $fourArray =
                [
                    '☐',
                    '☐',
                    '☐',
                ];
        }

        $fiveArray = [];
        if ($dealInformation['presented_as'] == 'protocol') {
            $fiveArray =
                [
                    '☒',
                    '☐'
                ];
        } else if ($dealInformation['presented_as'] == 'another') {
            $fiveArray =
                [
                    '☐',
                    '☒'
                ];
        } else {
            $fiveArray =
                [
                    '☐',
                    '☐'
                ];
        }

        $sixArray = [];
        if ($dealInformation['protocol_given'] == 'fact') {
            $sixArray =
                [
                    '☒',
                    '☐'
                ];
        } else if ($dealInformation['protocol_given'] == 'all') {
            $sixArray =
                [
                    '☐',
                    '☒'
                ];
        } else {
            $sixArray =
                [
                    '☐',
                    '☐'
                ];
        }

        $sevenArray = ['', '☐', '☐', '☐'];
        $reqInfo = '_____________________________________________';
        $addInfoArray = json_decode($dealInformation['add_info'], JSON_UNESCAPED_UNICODE);

        if (in_array('passport', $addInfoArray)) {
            $sevenArray[1] = '☒';
        }

        if (in_array('consist', $addInfoArray)) {
            $sevenArray[2] = '☒';
        }

        if ($dealInformation['req_inf']) {
            $reqInfo = $dealInformation['req_inf'];
            $sevenArray[3] = '☒';
        }

        $TZInformation = [
            'id' => $TZ_ID,
            'num_request' => !empty($str_num_request) ? $str_num_request . ' от ' . date('d.m.Y', strtotime($res_tz['DATE'])) : '-',
            'nDogovor' => $dogovor_num,
            'nZakazchik' => preg_replace('/"([^"]*)"/', "«$1»", $companyInformation['NAME']),
            'innZakazchik' => $companyInformation['RQ_INN'],
            'ogrnZakazchik' => $companyInformation['RQ_OGRN'],
            'aZakazchik' => $companyInformation['RQ_ACCOUNTANT'],
            'Phone' => 'тел: ' . $companyInformation['RQ_PHONE'],
            'Email' => 'E-mail: ' . $companyInformation['RQ_FIRST_NAME'],
            'oStroit' => !empty($dealInformation['OBJECT']) ? $dealInformation['OBJECT'] : '-',
            'DAY_TO_TEST_TEXT' => $day_to_test_text,
            'DAY_TO_TEST_NOTE' => $day_to_test_note,
            'dProbe' => ($dealInformation['DATE_ACT'] ? date("d.m.Y", strtotime($dealInformation['DATE_ACT'])) : '-'),
            'date' => $res_tz['DATE'],
            'curDate' => $curdate,
            'curDateEn' => $curdateEng,
            'SUM' => $sumPrice,
            'nds' => $dealInformation['is_nds'],
            'nds_7' => $dealInformation['is_nds_7'],
            'NDS_TEXT' => $nds_text,
            'NUM_REQUEST' => !empty($str_num_request) ? 'ПО ЗАЯВКЕ ' . $str_num_request : '-',
            'CLIENT_NAME' => $companyInformation['NAME'],
            'COMMENT_TZ' => !empty($dealInformation['COMMENT_TZ']) ? $dealInformation['COMMENT_TZ'] : '-',
            'mestoSboraProbe' => $placeDateStr,
            'tests_for' => $test_for,
            'compliance' => $compliance,
            'presented_as' => $protocol_as,
            'add_info' => !empty($addInfoResult) ? $addInfoResult : '-',
            'contact' => $companyInformation['RQ_NAME'],
            'phone' => $companyInformation['RQ_PHONE'],
            'mail' => $companyInformation['RQ_EMAIL'],
            'oIspit' => $materialsString ? $materialsString : '-',
            'location' => $currentLocation,
            'threeOne' => $threeArray[0],
            'threeTwo' => $threeArray[1],
            'threeThree' => $threeArray[2],
            'threeFour' => $threeArray[3],
            'fourOne' => $fourArray[0],
            'fourTwo' => $fourArray[1],
            'fourThree' => $fourArray[2],
            'normativeGost' => $normativeGost,
            'normativeSTO' => $normativeSTO,
            'fiveOne' => $fiveArray[0],
            'fiveTwo' => $fiveArray[1],
            'sixOne' => $sixArray[0],
            'sixTwo' => $sixArray[1],
            'sewenTwo' => $sevenArray[1],
            'sewenThree' => $sevenArray[2],
            'sewenFour' => $sevenArray[3],
            'reqInf' => $reqInfo,
            'icProbe' => $dealInformation['probes_to_ic'] ? $dealInformation['probes_to_ic'] : '-',
            'icAddServices' => $dealInformation['additional_services'] ? $dealInformation['additional_services'] : '-',
            'dayStart' => $dayStart ? $dayStart : '__',
            'monthStart' => $monthStart ? $monthStart : '__',
            'yearStart' => $yearStart ? $yearStart : '__',
            'daysCount' => $current_day ? $current_day : '__'
        ];

        $arrGost = $gostModel->getGostMaterialByDealID($dealID);


        $this->createTechnicalSpecificationDocument($TZInformation, $arrGost, $dealID, $dealInformation);
    }

    /**
     * Подготовка данных для приложения к договору
     * @param $dealID
     */
    public function invoiceOfferGenerator($dealID)
    {
        $companyModel = new Company();
        $requestModel = new Request();
        $historyModel = new History();
        $userModel = new User();
        $gostModel = new Gost();
        $orderModel = new Order();
        $requirementModel = new Requirement();
        //		$numerAcc = new CAccountNumber();
        //		$invNum = $numerAcc->getValue();
        //		$this->pre($invNum);

        $companyInformation = $companyModel->getRequisiteByDealId($dealID);

        $dealInformation = $requestModel->getTzByDealId($dealID);

        $TZ_ID = $dealInformation['ID'];

        $date = date('Y-m-d H:i:s');

        $dateTZ = date('d-m-Y_H-i-s', strtotime($date));

        $curdate = "СО_" . $TZ_ID . "_" . $dateTZ;
        $curdateEng = "СО_" . $TZ_ID . "_" . $dateTZ;

        $res_deals = $orderModel->getContractDealByDealId($dealID);

        $id = $res_deals['ID_CONTRACT'];

        if (!empty($res_deals['ID_CONTRACT'])) {

            $res_dogovor = $this->DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`= {$res_deals['ID_CONTRACT']}")->Fetch();
        }

        if ($res_dogovor['ID']) {

            $numberAcc = explode('/', $res_dogovor['NUMBER'])[0];
            $this->DB->Query("UPDATE `DOGOVOR` SET `ACTUAL_VER` ='{$curdateEng}' WHERE `TZ_ID`= {$TZ_ID}");

            $numberAcc = $orderModel->getCurrentAccountNumber();

            $dataInv = [
                'DATE' => $this->quoteStr($date),
                'TZ_ID' => $TZ_ID,
                'ACTUAL_VER' => $this->quoteStr($curdateEng),
                'NUMBER' => $this->quoteStr($numberAcc),
            ];

            $this->DB->Insert('INVOICE', $dataInv);

            $ba_tz = [
                'ACCOUNT' => $numberAcc,
            ];

            $this->DB->Update('ba_tz', $ba_tz, "where ID_Z = {$dealID}");

            $str_type = 'Обновление Счета-оферты';
        } else {
            //		    $this->pre($this->quoteStr($curdateEng));
            $curYear = date('y');
            $number = $orderModel->getCurrentNumberInvoiceOffer(date("Y")) . '/' . $curYear;
            //$this->pre($this->quoteStr($number));
            $numberAcc = $orderModel->getCurrentAccountNumber();
            $curdate = "СО_{$number}_{$dateTZ}";
            $data = [
                'DATE' => $this->quoteStr($date),
                'TZ_ID' => $TZ_ID,
                'CLIENT_ID' => $companyInformation['ID'],
                'ACTUAL_VER' => $this->quoteStr($curdateEng),
                'CONTRACT_TYPE' => $this->quoteStr('Счет-оферта'),
                'NUMBER' => $this->quoteStr($number),
                'DEAL_ID' => $dealID,
                'type_id' => 2,
                'dogovor_type_id' => 2
            ];

            $dataInv = [
                'DATE' => $this->quoteStr($date),
                'TZ_ID' => $TZ_ID,
                'ACTUAL_VER' => $this->quoteStr($curdateEng),
                'NUMBER' => $this->quoteStr($numberAcc),
            ];

            $id = $this->DB->Insert('DOGOVOR', $data);

            $dataOfferInvoice = [
                'dogovor_id' => $id,
                'client_id' => $companyInformation['ID']
            ];

            $this->DB->Insert('InvoiceOffer', $dataOfferInvoice);
            $this->DB->Insert('INVOICE', $dataInv);

            $str_type = 'Формирование Счета-Оферты';
            $invoice = "Счет-оферта №{$number} от " . StringHelper::dateRu($date);

            $ba_tz = [
                'DOGOVOR_NUM' => $id,
                'DOGOVOR_TABLE' => $this->quoteStr($invoice),
                'ACCOUNT' => $numberAcc,
            ];

            $this->DB->Update('ba_tz', $ba_tz, "where ID_Z = {$dealID}");

            $orderModel->setContractToRequest($dealID, $id);
        }

        //работа с 1С

        $iin_user_1c = !empty($companyInformation['RQ_INN']) ? $companyInformation['RQ_INN'] : '';

        $company_title = !empty($companyInformation['NAME']) ? $companyInformation['NAME'] : '';

        if (!$res_dogovor) {
            $id = $res_deals['ID_CONTRACT'];

            if (!empty($res_deals['ID_CONTRACT'])) {
                $res_dogovor = $this->DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`= {$res_deals['ID_CONTRACT']}")->Fetch();
            }
        }

        //        $res_gogovor = $this->DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`= {$id}")->Fetch();

        $number_dogovor_1c = !empty($res_dogovor['NUMBER']) ? 'Cчет-оферта ' . $res_dogovor['NUMBER'] : '';
        //        $number_dogovor_1c = !empty($res_gogovor['NUMBER']) ? $numberAcc : '';

        $date_dogovor_1c = !empty($res_dogovor['DATE']) ? date("d.m.Y", strtotime($res_dogovor['DATE'])) : '';

        $kpp_1c = !empty($companyInformation['RQ_KPP']) ? $companyInformation['RQ_KPP'] : '';

        $price_account_1c = $dealInformation['price_discount'];
        $nds_1c = '';
        if (!empty($dealInformation['is_nds'])) {
            $nds_1c = !empty($dealInformation['is_nds_7']) ? '7' : '5';
        }

        $arr_data_1c = [
            "fl" => 0,
            "number" => $numberAcc,
            "date" => date('d.m.Y', strtotime($date)),
            "inn" => $iin_user_1c,
            "summ" => $price_account_1c,
            "stavka_nds" => $nds_1c,
            "title" => $company_title,
            "n_dogovor" => $number_dogovor_1c,
            "d_dogovor" => $date_dogovor_1c,
            "kpp" => $kpp_1c
        ];


        $res_select_requests_for_1c = $this->DB->Query("SELECT * FROM `REQUESTS_FOR_1C` WHERE `ID` = 1")->Fetch();

        if (!empty($res_select_requests_for_1c['REQ'])) {
            $arrReq = !empty($res_select_requests_for_1c['REQ']) ? unserialize($res_select_requests_for_1c['REQ']) : '';

            $check_true = 1;
            if (isset($arrReq['requests'])) {
                foreach ($arrReq['requests'] as $key_request => $val_request) {

                    if (!empty($val_request['number']) && $number_dogovor_1c == $val_request['number']) {
                        $check_true = 0;
                    }
                }
            }

            if (isset($arrReq['requests']) && !empty($check_true) && $check_true == 1 && !empty($number_dogovor_1c)) {
                array_push($arrReq['requests'], $arr_data_1c);
                $this->DB->Query("UPDATE `REQUESTS_FOR_1C` SET `REQ`='" . serialize($arrReq) . "' WHERE `ID` = 1");
            }
        } else {
            $arrReq['requests'] = [];

            if (!empty($number_dogovor_1c)) {
                array_push($arrReq['requests'], $arr_data_1c);
                //                $this->pre("UPDATE `REQUESTS_FOR_1C` SET `REQ`='" . serialize($arrReq) . "' WHERE `ID` = 1");
                $this->DB->Query("UPDATE `REQUESTS_FOR_1C` SET `REQ`='" . serialize($arrReq) . "' WHERE `ID` = 1");
            }
        }


        //работа с 1С. Конец

        $currentUser = $userModel->getCurrentUser();

        $history = [
            'DATE' => $date,
            'ASSIGNED' => $currentUser['NAME'] . ' ' . $currentUser['LAST_NAME'],
            'TZ_ID' => $TZ_ID,
            'USER_ID' => $currentUser['ID'],
            'TYPE' => $str_type,
            'REQUEST' => $dealInformation['REQUEST_TITLE'],
        ];

        $historyModel->addHistory($history);

        $str_num_request = !empty($dealInformation['REQUEST_TITLE']) ? explode(' ', $dealInformation['REQUEST_TITLE'])[1] : '';

        $res_tz = $this->DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();

        $dogovor_num = $orderModel->getOrderInvoiceByDealId($dealID, $TZ_ID);

        $actProbe = $requirementModel->getActBase($dealID);

        $placeProbe = !empty($actProbe['PLACE_PROBE']) ? trim($actProbe['PLACE_PROBE']) . ';' : '-';
        $dateProbe = !empty($actProbe['DATE_PROBE']) ? date('d.m.Y', strtotime($actProbe['DATE_PROBE'])) : '';

        $placeDateStr = "{$placeProbe} {$dateProbe}";

        if ($dealInformation['DAY_TO_TEST']) {
            $cur = date('d.m.Y'); //текущая дата
            switch ($dealInformation['type_of_day']) {
                case 'day':
                    $typeDay = StringHelper::num_word($dealInformation['DAY_TO_TEST'], 'календарный день', 'календарных дня', 'календарных дней');
                    $current_day = 'составляет ' . $dealInformation['DAY_TO_TEST'] . ' ' . $typeDay;
                    break;
                case 'work_day':
                    $typeDay = StringHelper::num_word($dealInformation['DAY_TO_TEST'], 'рабочий день', 'рабочих дня', 'рабочих дней');
                    $current_day = 'составляет ' . $dealInformation['DAY_TO_TEST'] . ' ' . $typeDay;
                    break;
                case 'month':
                    $typeDay = StringHelper::num_word($dealInformation['DAY_TO_TEST'], 'месяц', 'месяца', 'месяцев');
                    $current_day = 'составляет ' . $dealInformation['DAY_TO_TEST'] . ' ' . $typeDay;
                    break;
                case 'custom':
                    $typeDay = 'до ' . date('d.m.Y', strtotime($dealInformation['DAY_TO_TEST']));
                    $current_day = $typeDay;
                    break;
                default:
                    $typeDay = '';
            }

            //кол-во пришедших дней

            $day_to_test_text = "Срок проведения испытаний " . $current_day . " с момента поступления проб (образцов) в ИЦ, подписания договора, технической заявки и соблюдения условий оплаты по Договору.";
            $day_to_test_note = 'Срок проведения испытаний является ориентировочным и может быть скорректирован по согласованию сторон.';
        } else {
            $day_to_test_text = '';
            $day_to_test_note = '';
        }

        $price_nds = $dealInformation['price_discount'];
        if (!empty($dealInformation['is_nds'])) {
            $price_nds *= !empty($dealInformation['is_nds_7']) ? 1.07 : 1.05;
        }

        $str = StringHelper::num2str($price_nds);

        $first = mb_substr($str, 0, 1, 'UTF-8'); //первая буква
        $last = mb_substr($str, 1); //все кроме первой буквы
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $name1 = $first . $last;

        $stringSum = '';

        if (!empty($dealInformation['is_nds'])) {
            if (!empty($dealInformation['is_nds_7'])) {
                $stringSum = ucfirst(StringHelper::num2str($dealInformation['price_discount'] * 1.07));
            } else {
                $stringSum = ucfirst(StringHelper::num2str($dealInformation['price_discount'] * 1.05));
            }
        } else {
            $stringSum = StringHelper::num2str($dealInformation['price_discount']);
        }

        $strSumArr = explode(' ', $stringSum);
        $rubIndex = 999999;

        $strEnd = '';
        $strStart = '';

        foreach ($strSumArr as $key => $value) {
            if ($value == 'рублей' || $value == 'рубля' || $value == 'рубль') {
                $rubIndex = $key;

                $strEnd .= $value;
            }

            if ($key > $rubIndex) {
                $strEnd .= ' ' . $value;
            }

            if ($key < $rubIndex) {
                $strStart .= ' ' . $value;
            }
        }

        $strStart = substr($strStart, 1);

        $currentDate = date("Y-m-d H:i:s");

        $this->DB->Query("UPDATE DOGOVOR SET DATE = '{$currentDate}' WHERE ID = '{$dealInformation['DOGOVOR_NUM']}'");

        //
        if (!empty($dealInformation['is_nds'])) {
            if (!empty($dealInformation['is_nds_7'])) {
                $end_sum = number_format($dealInformation['price_discount'] * 1.07, 2, ',', ' ');
                $nds_t = '7%';
            } else {
                $end_sum = number_format($dealInformation['price_discount'] * 1.07, 2, ',', ' ');
                $nds_t = '7%';
            }
        } else {
            $end_sum = number_format($dealInformation['price_discount'], 2, ',', ' ');
            $nds_t = '7%';
        }
        $TZInformation = [
            'id' => $id,
            'tz_id' => $TZ_ID,
            'order_id' => $res_tz['ID'],
            'num_request' => !empty($str_num_request) ? $str_num_request : '',
            'nDogovor' => $dogovor_num,
            'nZakazchik' => preg_replace('/"([^"]*)"/', "«$1»", $companyInformation['NAME']),
            'innZakazchik' => $companyInformation['RQ_INN'],
            'ogrnZakazchik' => $companyInformation['RQ_OGRN'],
            'aZakazchik' => $companyInformation['RQ_ACCOUNTANT'],
            'Phone' => 'тел: ' . $companyInformation['RQ_PHONE'],
            'Email' => 'E-mail: ' . $companyInformation['RQ_FIRST_NAME'],
            'oStroit' => $dealInformation['OBJECT'],
            'dTest' => $current_day,
            'dStart' => date('d.m.Y', strtotime($dealInformation['day_start'])),
            'DAY_TO_TEST_TEXT' => $day_to_test_text,
            'DAY_TO_TEST_NOTE' => $day_to_test_note,
            'dProbe' => ($dealInformation['DATE_ACT'] ? date("d.m.Y", strtotime($dealInformation['DATE_ACT'])) : '-'),
            'date' => $res_tz['DATE'],
            'curDate' => $curdate,
            'curDateEn' => $curdateEng,
            'SUM' => number_format($dealInformation['price_discount'], 2, ',', ' '),
            'SUMr' => $dealInformation['price_ru'],
            'nds' => $dealInformation['is_nds'],
            'nds_7' => $dealInformation['is_nds_7'],
            'NUM_REQUEST' => !empty($str_num_request) ? 'ПО ЗАЯВКЕ ' . $str_num_request : '',
            'CLIENT_NAME' => $companyInformation['NAME'],
            'COMMENT_TZ' => !empty($dealInformation['COMMENT_TZ']) ? $dealInformation['COMMENT_TZ'] : '',
            'mestoSboraProbe' => $placeDateStr,
            'SumAllWord' => $name1,
            'end_sum' => $end_sum,
            'str_sum_start' => StringHelper::mb_strtoupper_first($strStart),
            'str_sum_end' => $strEnd
        ];

        if (!empty($dealInformation['is_nds'])) {
            if (!empty($dealInformation['is_nds_7'])) {
                $nds_amount = $dealInformation['price_discount'] * 0.07;
            } else {
                $nds_amount = $dealInformation['price_discount'] * 0.05;
            }
        } else {
            $nds_amount = 0;
        }

        $TZInformation['NDS'] = number_format($nds_amount, 2, ',', ' ');

        if (!empty($dealInformation['is_nds'])) {
            if (!empty($dealInformation['is_nds_7'])) {
                $sum_with_nds = $dealInformation['price_discount'] * 1.07;
            } else {
                $sum_with_nds = $dealInformation['price_discount'] * 1.05;
            }
        } else {
            $sum_with_nds = $dealInformation['price_discount'];
        }

        $TZInformation['SUM_NDS'] = number_format($sum_with_nds, 2, ',', ' ');
        $TZInformation['RES_SUM'] = $TZInformation['SUM_NDS'];
        $TZInformation['NDS_TYPE'] = $nds_t;

        //

        $arrGost = $gostModel->getGostMaterialByDealID($dealID);

        $this->invoiceOfferDocument($TZInformation, $arrGost, $dealID, $dealInformation);
    }

    /**
     * Подготовка данных для счета
     * @param $dealID
     */
    public function invoiceGenerator($dealID)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        $companyModel = new Company();
        $requestModel = new Request();
        $historyModel = new History();
        $userModel = new User();
        $gostModel = new Gost();
        $orderModel = new Order();

        $invNum = $this->DB->Query("SELECT MAX(NUMBER) as num FROM INVOICE WHERE `DATE` >= '2026-01-01' LIMIT 1")->Fetch()['num'] + 1;

        $companyInformation = $companyModel->getRequisiteByDealId($dealID);

        $dealInformation = $requestModel->getTzByDealId($dealID);

        $TZ_ID = $dealInformation['ID'];

        $res_deals = $orderModel->getContractDealByDealId($dealID);

        $id = $res_deals['ID_CONTRACT'];

        //        $numberAcc = $requestModel->getNextInvoiceNumber();

        $arrGost = $gostModel->getGostMaterialByDealID($dealID);

        $res_acc = $this->DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();

        $date = date('Y-m-d H:i:s');

        $dateAcc = date('d-m-Y_H-i-s', strtotime($date));

        $curdate = '';
        $curdateEng = '';

        if ($res_acc) {
            $curdate = "Счет_" . $invNum . '_' . $dateAcc;

            $curdateEng = "Account_" . $invNum . '_' . $dateAcc;
            //$accountNum = $resInv['ID'];
        }
        $str_type = '';

        if ($res_acc) {

            $res_acc = $this->DB->Query("UPDATE `INVOICE` SET `ACTUAL_VER` ='" . $curdate . "', `DATE` = NOW() WHERE `TZ_ID`=" . $TZ_ID);
            $str_type = 'Обновление счета';
        } else {

            $res_acc = $this->DB->Query("INSERT INTO `INVOICE` (`DATE`, `TZ_ID`) VALUES (NOW(), " . $TZ_ID . ")");
            $str_type = 'Формирование счета';

            $result_select_invoice = $this->DB->Query("SELECT * FROM `INVOICE` ORDER BY ID DESC LIMIT 1")->Fetch();
            $invNumber = (int)$result_select_invoice['ID'] - 2450;
            if (date("Y", strtotime($result_select_invoice['DATE'])) == '2020') $invNumber = (int)$result_select_invoice['ID'];

            $id_invoice = !empty($result_select_invoice['ID']) ? $invNum : '';

            $curdate = "Счет_" . $id_invoice . '_' . $dateAcc;
            $curdateEng = "Account_" . $id_invoice . '_' . $dateAcc;

            $idInvoice = !empty($result_select_invoice['ID']) ? (int)$result_select_invoice['ID'] : '';

            if (!empty($idInvoice)) {

                $this->DB->Query("UPDATE `INVOICE` SET `ACTUAL_VER` ='" . $curdate . "' WHERE `ID`=" . $idInvoice);
            }
        }

        //работа с 1С

        $iin_user_1c = !empty($companyInformation['RQ_INN']) ? $companyInformation['RQ_INN'] : '';

        $company_title = !empty($companyInformation['NAME']) ? $companyInformation['NAME'] : '';

        $res_gogovor = $this->DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`= {$id}")->Fetch();

        $number_dogovor_1c = !empty($res_gogovor['NUMBER']) ? $res_gogovor['NUMBER'] : '';

        $date_dogovor_1c = !empty($res_gogovor['DATE']) ? date("d.m.Y", strtotime($res_gogovor['DATE'])) : '';

        $kpp_1c = !empty($companyInformation['RQ_KPP']) ? $companyInformation['RQ_KPP'] : '';

        $price_account_1c = $dealInformation['price_discount'];

        if (!empty($dealInformation['is_nds'])) {
            if (!empty($dealInformation['is_nds_7'])) {
                $nds_1c = '7';
            } else {
                $nds_1c = '5';
            }
        } else {
            $nds_1c = '';
        }

        $resInv = $this->DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();

        $nember_account_1c = (!empty($resInv['ID']) ?  $invNum : '');

        $date_account_1c = (!empty($resInv['DATE']) ?  date("d.m.Y", strtotime($resInv['DATE'])) : '');

        $arr_data_1c = [
            "fl" => 0,
            "number" => $nember_account_1c,
            "date" => $date_account_1c,
            "inn" => $iin_user_1c,
            "summ" => $price_account_1c,
            "stavka_nds" => $nds_1c,
            "title" => $company_title,
            "n_dogovor" => $number_dogovor_1c,
            "d_dogovor" => $date_dogovor_1c,
            "kpp" => $kpp_1c,
        ];

        $res_select_requests_for_1c = $this->DB->Query("SELECT * FROM `REQUESTS_FOR_1C` WHERE `ID` = 1")->Fetch();

        if (!empty($res_select_requests_for_1c['REQ'])) {
            $res2_select_requests_for_1c = $this->DB->Query("SELECT * FROM `REQUESTS_FOR_1C` WHERE `ID` = 1")->Fetch();
            $arrReq = !empty($res2_select_requests_for_1c['REQ']) ? unserialize($res2_select_requests_for_1c['REQ']) : '';

            $check_true = 1;
            if (isset($arrReq['requests'])) {
                foreach ($arrReq['requests'] as $key_request => $val_request) {

                    if (!empty($val_request['number']) && $nember_account_1c == $val_request['number']) {
                        $check_true = 0;
                    }
                }
            }

            if (isset($arrReq['requests']) && !empty($check_true) && $check_true == 1 && !empty($nember_account_1c)) {

                array_push($arrReq['requests'], $arr_data_1c);

                $res2_update_requests_for_1c = $this->DB->Query("UPDATE `REQUESTS_FOR_1C` SET `REQ`='" . $this->DB->ForSql(serialize($arrReq)) . "' WHERE `ID` = 1");
            }
        } else {
            $arrReq['requests'] = [];

            if (!empty($nember_account_1c)) {
                array_push($arrReq['requests'], $arr_data_1c);

                $res_update_requests_for_1c = $this->DB->Query("UPDATE `REQUESTS_FOR_1C` SET `REQ`='" . $this->DB->ForSql(serialize($arrReq)) . "' WHERE `ID` = 1");
            }
        }

        //работа с 1С. Конец

        $this->DB->Query("UPDATE `ba_tz` SET `ACCOUNT` = " . (int)$nember_account_1c . " WHERE `ID`=" . (int)$TZ_ID);

        $this->DB->Query("UPDATE `INVOICE` SET `NUMBER` = " . (int)$nember_account_1c . " WHERE `TZ_ID`=" . (int)$TZ_ID);

        $currentUser = $userModel->getCurrentUser();

        $history = [
            'DATE' => $date,
            'ASSIGNED' => $currentUser['NAME'] . ' ' . $currentUser['LAST_NAME'],
            'TZ_ID' => $TZ_ID,
            'USER_ID' => $currentUser['ID'],
            'TYPE' => $str_type,
            'REQUEST' => $dealInformation['REQUEST_TITLE'],
        ];

        $historyModel->addHistory($history);

        $price_nds = $dealInformation['price_discount'] * 1.05;

        $price_discount = $dealInformation['price_discount'];

        $resCount = count($arrGost);
        if (!empty($dealInformation['is_nds'])) {
            if (!empty($dealInformation['is_nds_7'])) {
                $sum_for_format = $price_discount * 1.07;
            } else {
                $sum_for_format = $price_discount * 1.05;
            }
        } else {
            $sum_for_format = $price_discount;
        }

        $resSum = number_format($sum_for_format, 2, ',', ' ');

        // Расчет суммы для преобразования в строку
        if (!empty($dealInformation['is_nds'])) {
            if (!empty($dealInformation['is_nds_7'])) {
                $sum_for_string = $dealInformation['price_discount'] * 1.07;
            } else {
                $sum_for_string = $dealInformation['price_discount'] * 1.05;
            }
            $stringSum = ucfirst(StringHelper::num2str($sum_for_string));
        } else {
            $stringSum = StringHelper::num2str($dealInformation['price_discount']);
        }

        $strSumArr = explode(' ', $stringSum);

        $strEnd = '';

        foreach ($strSumArr as $key => $value) {
            if ($value == 'рублей' || $value == 'рубля' || $value == 'рубль') {
                $strEnd .= $value;
            }
        }

        if (!empty($dealInformation['is_nds'])) {
            if (!empty($dealInformation['is_nds_7'])) {
                $nds_amount = $price_discount * 0.07;
                $sum_with_nds = $price_discount * 1.07;
                $nds_t = '7%';
            } else {
                $nds_amount = $price_discount * 0.05;
                $sum_with_nds = $price_discount * 1.05;
                $nds_t = '5%';
            }
        } else {
            $nds_amount = 0;
            $sum_with_nds = $price_discount;
            $nds_t = '7%';
        }

        $TZInformation = [
            'NUM_ACC' => $invNum,
            'NDS_TYPE' => $nds_t,
            'DATE_ACC' => date("d.m.Y", strtotime($resInv['DATE'])),
            'nZakazchik' => preg_replace('/"([^"]*)"/', '«$1»', str_replace('&quot;', '"', $dealInformation['COMPANY_TITLE'])),
            'innZakazchik' => $companyInformation['RQ_INN'],
            'ogrnZakazchik' => $companyInformation['RQ_OGRN'],
            'aZakazchik' => $companyInformation['RQ_ACCOUNTANT'],
            'nRequest' => (!empty($deal['TITLE']) ? ' Заявка ' . $deal['TITLE'] : ''),
            'Sum' => number_format($price_discount, 2, ',', ' '),
            'Nds' => !empty($dealInformation['is_nds']) ? number_format($nds_amount, 2, ',', ' ') : '--',
            'Sum_nds' => number_format($sum_with_nds, 2, ',', ' '),
            'countName' => "Всего наименований {$resCount}, на сумму {$resSum} {$strEnd}",
            'SumAllWord' => StringHelper::mb_strtoupper_first(StringHelper::num2str($sum_with_nds))
        ];

        $TZInformation['curdate'] = $curdate;
        $TZInformation['curdateEng'] = $curdateEng;
        $TZInformation['invNum'] = $invNum;

        $this->invoiceDocument($TZInformation, $arrGost, $dealID, $dealInformation);
    }

    public function invoiceDocument($tz, $arrGost, $dealID, $dealInfo)
    {
        $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/Account_updated.docx';
        $template_doc2 = $_SERVER["DOCUMENT_ROOT"] . '/Account_updated2.docx';

        $t = 1;

        $nds_t = '5%';
        $nds_p = 1.05;
        if (!empty($dealInfo['is_nds_7'])) {
            $nds_t = '7%';
            $nds_p = 1.07;
        }
        foreach ($arrGost as $key => $val) {
            if ($dealInfo['group_type'] == 1 || !$dealInfo['group_type']) {
                if (strpos($val['material_name'], 'Песок') !== false || strpos($val['material_name'], 'Щебень') !== false) {
                    $result[$val['material_id']]['material_name'] = $val['original_material_name'];
                } else {
                    $result[$val['material_id']]['material_name'] = $val['material_name'];
                }
                $result[$val['material_id']]['material_id'] = $val['material_id'];
                $result[$val['material_id']]['nfp'] = $val['name_for_protocol'];

                if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['material_id']]['gosts'])) {
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Obiem'] += $val['amount'];
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Sum'] += $val['sum'];
                    $t -= $val['amount'];
                } else {
                    if ($val['nds']) {
                        if (!empty($val['nds_7'])) {
                            $nds_value = number_format($val['price'] * 0.07, 2, ',', ' ');
                            $sum_nds = number_format($val['price'] * 1.07, 2, ',', ' ');
                            $end_sum = number_format($val['price'] * $val['amount'] * 1.07, 2, ',', ' ');
                        } else {
                            $nds_value = number_format($val['price'] * 0.05, 2, ',', ' ');
                            $sum_nds = number_format($val['price'] * 1.05, 2, ',', ' ');
                            $end_sum = number_format($val['price'] * $val['amount'] * 1.05, 2, ',', ' ');
                        }
                    } else {
                        $nds_value = '--';
                        $sum_nds = '--';
                        $end_sum = number_format($val['price'] * $val['amount'], 2, ',', '');
                    }
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                        'Specification' => $val['method_name'] . " " . $val['gost_name'],
                        'Price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'Sum' => $val['sum'],
                        'Obiem' => $val['amount'],
                        'NUM' => $t,
                        'Nds' => $nds_value,
                        'SumNds' => $sum_nds,
                        'EndSum' => $end_sum
                    ];
                }

                $result[$val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 2) {
                $result[$val['name_for_protocol']]['material_name'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol']]['material_id'] = $val['material_id'];
                $result[$val['name_for_protocol']]['nfp'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol']]['is_only_name_for_protocol'] = 1;

                if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['name_for_protocol']]['gosts'])) {
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price']]['Obiem'] += $val['amount'];
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price']]['Sum'] += $val['sum'];
                    $t -= $val['amount'];
                } else {
                    if ($val['nds']) {
                        if (!empty($val['nds_7'])) {
                            $nds_value = number_format($val['price'] * 0.07, 2, ',', ' ');
                            $sum_nds = number_format($val['price'] * 1.07, 2, ',', ' ');
                            $end_sum = number_format($val['price'] * $val['amount'] * 1.07, 2, ',', ' ');
                        } else {
                            $nds_value = number_format($val['price'] * 0.05, 2, ',', ' ');
                            $sum_nds = number_format($val['price'] * 1.05, 2, ',', ' ');
                            $end_sum = number_format($val['price'] * $val['amount'] * 1.05, 2, ',', ' ');
                        }
                    } else {
                        $nds_value = '--';
                        $sum_nds = '--';
                        $end_sum = number_format($val['price'] * $val['amount'], 2, ',', '');
                    }
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                        'Specification' => $val['method_name'] . " " . $val['gost_name'],
                        'Price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'Sum' => $val['sum'],
                        'Obiem' => $val['amount'],
                        'NUM' => $t,
                        'Nds' => $nds_value,
                        'SumNds' => $sum_nds,
                        'EndSum' => $end_sum
                    ];
                }

                $result[$val['name_for_protocol']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 3) {
                $result[$val['name_for_protocol'] . $val['material_id']]['material_name'] = $val['material_name'] . " ({$val['name_for_protocol']})";
                $result[$val['name_for_protocol'] . $val['material_id']]['material_id'] = $val['material_id'];
                $result[$val['name_for_protocol'] . $val['material_id']]['nfp'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = 1;

                if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['name_for_protocol'] . $val['material_id']]['gosts'])) {
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Obiem'] += $val['amount'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Sum'] += $val['sum'];
                    $t -= $val['amount'];
                } else {
                    if ($val['nds']) {
                        if (!empty($val['nds_7'])) {
                            $nds_value = number_format($val['price'] * 0.07, 2, ',', ' ');
                            $sum_nds = number_format($val['price'] * 1.07, 2, ',', ' ');
                            $end_sum = number_format($val['price'] * $val['amount'] * 1.07, 2, ',', ' ');
                        } else {
                            $nds_value = number_format($val['price'] * 0.05, 2, ',', ' ');
                            $sum_nds = number_format($val['price'] * 1.05, 2, ',', ' ');
                            $end_sum = number_format($val['price'] * $val['amount'] * 1.05, 2, ',', ' ');
                        }
                    } else {
                        $nds_value = '--';
                        $sum_nds = '--';
                        $end_sum = number_format($val['price'] * $val['amount'], 2, ',', '');
                    }
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                        'Specification' => $val['method_name'] . " " . $val['gost_name'],
                        'Price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'Sum' => $val['sum'],
                        'Obiem' => $val['amount'],
                        'NUM' => $t,
                        'Nds' => $nds_value,
                        'SumNds' => $sum_nds,
                        'EndSum' => $end_sum
                    ];
                }

                $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 4) {
                if (!$val['group_id']) {
                    $result[$val['material_id']]['material_name'] = $val['material_name'];
                    $result[$val['material_id']]['material_id'] = $val['material_id'];
                    $result[$val['material_id']]['nfp'] = $val['name_for_protocol'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['material_id']]['gosts'])) {
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Obiem'] += $val['amount'];
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Sum'] += $val['sum'];
                        $t -= $val['amount'];
                    } else {
                        if ($val['nds']) {
                            if (!empty($val['nds_7'])) {
                                $nds_value = number_format($val['price'] * 0.07, 2, ',', ' ');
                                $sum_nds = number_format($val['price'] * 1.07, 2, ',', ' ');
                                $end_sum = number_format($val['price'] * $val['amount'] * 1.07, 2, ',', ' ');
                            } else {
                                $nds_value = number_format($val['price'] * 0.05, 2, ',', ' ');
                                $sum_nds = number_format($val['price'] * 1.05, 2, ',', ' ');
                                $end_sum = number_format($val['price'] * $val['amount'] * 1.05, 2, ',', ' ');
                            }
                        } else {
                            $nds_value = '--';
                            $sum_nds = '--';
                            $end_sum = number_format($val['price'] * $val['amount'], 2, ',', '');
                        }
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                            'Specification' => $val['method_name'] . " " . $val['gost_name'],
                            'Price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'Sum' => $val['sum'],
                            'Obiem' => $val['amount'],
                            'NUM' => $t,
                            'Nds' => $nds_value,
                            'SumNds' => $sum_nds,
                            'EndSum' => $end_sum
                        ];
                    }

                    $result[$val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                } else {
                    $result[$val['group_id']]['material_name'] = $val['material_name']; //  . " ({$val['group']})"
                    $result[$val['group_id']]['material_id'] = $val['material_id'];
                    $result[$val['group_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['group_id']]['group'] = $val['group_id'];
                    $result[$val['group_id']]['group_name'] = $val['group'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['group_id']]['gosts'])) {
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Obiem'] += $val['amount'];
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Sum'] += $val['sum'];
                        $t -= $val['amount'];
                    } else {
                        if ($val['nds']) {
                            if (!empty($val['nds_7'])) {
                                $nds_value = number_format($val['price'] * 0.07, 2, ',', ' ');
                                $sum_nds = number_format($val['price'] * 1.07, 2, ',', ' ');
                                $end_sum = number_format($val['price'] * $val['amount'] * 1.07, 2, ',', ' ');
                            } else {
                                $nds_value = number_format($val['price'] * 0.05, 2, ',', ' ');
                                $sum_nds = number_format($val['price'] * 1.05, 2, ',', ' ');
                                $end_sum = number_format($val['price'] * $val['amount'] * 1.05, 2, ',', ' ');
                            }
                        } else {
                            $nds_value = '--';
                            $sum_nds = '--';
                            $end_sum = number_format($val['price'] * $val['amount'], 2, ',', '');
                        }
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                            'Specification' => $val['method_name'] . " " . $val['gost_name'],
                            'Price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'Sum' => $val['sum'],
                            'Obiem' => $val['amount'],
                            'NUM' => $t,
                            'Nds' => $nds_value,
                            'SumNds' => $sum_nds,
                            'EndSum' => $end_sum
                        ];
                    }

                    $result[$val['group_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                }
            } else {
                if (!$val['group_id']) {
                    $result[$val['name_for_protocol'] . $val['material_id']]['material_name'] = $val['material_name'] . ($val['name_for_protocol'] ? " ({$val['name_for_protocol']})" : "");
                    $result[$val['name_for_protocol'] . $val['material_id']]['material_id'] = $val['material_id'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = 1;

                    if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['name_for_protocol'] . $val['material_id']]['gosts'])) {
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Obiem'] += $val['amount'];
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Sum'] += $val['sum'];
                        $t -= $val['amount'];
                    } else {
                        if ($val['nds']) {
                            if (!empty($val['nds_7'])) {
                                $nds_value = number_format($val['price'] * 0.07, 2, ',', ' ');
                                $sum_nds = number_format($val['price'] * 1.07, 2, ',', ' ');
                                $end_sum = number_format($val['price'] * $val['amount'] * 1.07, 2, ',', ' ');
                            } else {
                                $nds_value = number_format($val['price'] * 0.05, 2, ',', ' ');
                                $sum_nds = number_format($val['price'] * 1.05, 2, ',', ' ');
                                $end_sum = number_format($val['price'] * $val['amount'] * 1.05, 2, ',', ' ');
                            }
                        } else {
                            $nds_value = '--';
                            $sum_nds = '--';
                            $end_sum = number_format($val['price'] * $val['amount'], 2, ',', '');
                        }
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                            'Specification' => $val['method_name'] . " " . $val['gost_name'],
                            'Price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'Sum' => $val['sum'],
                            'Obiem' => $val['amount'],
                            'NUM' => $t,
                            'Nds' => $nds_value,
                            'SumNds' => $sum_nds,
                            'EndSum' => $end_sum
                        ];
                    }

                    $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                } else {
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['material_name'] = $val['material_name'] . ($val['name_for_protocol'] ? " ({$val['name_for_protocol']})" : ""); //  . " ({$val['group']})"
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['material_id'] = $val['material_id'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['group'] = $val['group_id'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['group_name'] = $val['group'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'])) {
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Obiem'] += $val['amount'];
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['Sum'] += $val['sum'];
                        $t -= $val['amount'];
                    } else {
                        if ($val['nds']) {
                            if (!empty($val['nds_7'])) {
                                $nds_value = number_format($val['price'] * 0.07, 2, ',', ' ');
                                $sum_nds = number_format($val['price'] * 1.07, 2, ',', ' ');
                                $end_sum = number_format($val['price'] * $val['amount'] * 1.07, 2, ',', ' ');
                            } else {
                                $nds_value = number_format($val['price'] * 0.05, 2, ',', ' ');
                                $sum_nds = number_format($val['price'] * 1.05, 2, ',', ' ');
                                $end_sum = number_format($val['price'] * $val['amount'] * 1.05, 2, ',', ' ');
                            }
                        } else {
                            $nds_value = '--';
                            $sum_nds = '--';
                            $end_sum = number_format($val['price'] * $val['amount'], 2, ',', '');
                        }
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                            'Specification' => $val['method_name'] . " " . $val['gost_name'],
                            'Price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'Sum' => $val['sum'],
                            'Obiem' => $val['amount'],
                            'NUM' => $t,
                            'Nds' => $nds_value,
                            'SumNds' => $sum_nds,
                            'EndSum' => $end_sum
                        ];
                    }

                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                }
            }
            $countM[] = $val['method_id'];
            ++$t;
        }

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $styleTable = array('borderSize' => 3, 'cellMarginLeft' => self::centimeterSizeToTwips(0.1), 'cellMarginRight' => 0, 'borderColor' => '000000', 'cellMarginTop' => self::centimeterSizeToTwips(0.2));
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellVerticalAlign = array('valign' => 'center');

        $cellHCentered = array('align' => 'center');
        $cellHLeft = array('align' => 'left');

        $section = $phpWord->addSection();

        $table = $section->addTable($styleTable);

        $table->addRow(null, array('tblHeader' => true));

        $table->addCell(self::percentToTwips(5, false), $cellRowSpan)->addText('№', array('size' => 9, 'bold' => true, 'name' => 'Arial'), $cellHCentered);
        $table->addCell(self::percentToTwips(33, false), $cellRowSpan)->addText('Товары (работы, услуги)', array('size' => 9, 'bold' => true, 'name' => 'Arial'), $cellHCentered);
        $table->addCell(self::percentToTwips(10, false), $cellRowSpan)->addText('Кол-во', array('size' => 9, 'bold' => true, 'name' => 'Arial'), $cellHCentered);
        $table->addCell(self::percentToTwips(7, false), $cellRowSpan)->addText('Ед. изм.', array('size' => 9, 'bold' => true, 'name' => 'Arial'), $cellHCentered);
        $table->addCell(self::percentToTwips(15, false), $cellRowSpan)->addText('Цена за пробу (образец), руб.', array('size' => 9, 'bold' => true, 'name' => 'Arial'), $cellHCentered);
        $table->addCell(self::percentToTwips(18, false), $cellRowSpan)->addText("Стоимость с НДС {$nds_t}, руб.", array('size' => 9, 'bold' => true, 'name' => 'Arial'), $cellHCentered);
        $table->addCell(self::percentToTwips(20, false), $cellRowSpan)->addText("Стоимость за испытание с НДС {$nds_t}, руб.", array('size' => 9, 'bold' => true, 'name' => 'Arial'), $cellHCentered);
        foreach ($result as $itemMater) {
            //$this->pre($result);
            $table->addRow(600);
            $table->addCell('auto', array('gridSpan' => 7, 'valign' => 'center'))->addText(strtoupper($itemMater['material_name']), array('size' => 10, 5, 'bold' => true), $cellHCentered);

            foreach ($itemMater['gosts'] as $k => $value) {
                $table->addRow();

                $table->addCell(null, $cellVerticalAlign)->addText($value['NUM'], array('size' => 9, 'name' => 'Arial'), $cellHCentered);
                $table->addCell(null, $cellVerticalAlign)->addText($value['Specification'], array('size' => 9, 'name' => 'Arial'), $cellHLeft);
                $table->addCell(null, $cellVerticalAlign)->addText($value['Obiem'], array('size' => 9, 'name' => 'Arial'), $cellHCentered);
                $table->addCell(null, $cellVerticalAlign)->addText('--', array('size' => 9, 'name' => 'Arial'), $cellHCentered);
                $table->addCell(null, $cellVerticalAlign)->addText($value['Price'], array('size' => 9, 'name' => 'Arial'), $cellHCentered);
                $table->addCell(null, $cellVerticalAlign)->addText($value['SumNds'], array('size' => 9, 'name' => 'Arial'), $cellHCentered);
                $table->addCell(null, $cellVerticalAlign)->addText(number_format($value['Sum'] * $nds_p, 2, ',', ' '), array('size' => 9, 'name' => 'Arial'), $cellHCentered);
            }
        }
        $template = new \PhpOffice\PhpWord\TemplateProcessor($template_doc);
        $template2 = new \PhpOffice\PhpWord\TemplateProcessor($template_doc2);

        $interimPath = "interim_archive_acc/" . $dealID . "/" . $tz['curdateEng'] . "wrd.docx";
        $interimPath2 = "interim_archive_acc/" . $dealID . "/" . $tz['curdateEng'] . ".docx";
        $outputFile = $tz['curdate'] . ".pdf";
        $outputPath = "archive_acc/" . $dealID . "/";

        $fullPathDocx = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $interimPath2;
        $fullPathPDF = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $outputPath;

        $file = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $interimPath;
        $file2 = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $interimPath2;

        $newDirectory = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/archive_acc/" . $dealID;
        $newDirectory2 = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/interim_archive_acc/" . $dealID;

        if (!is_dir($newDirectory)) {
            mkdir($newDirectory, 0777, true);
        }

        if (!is_dir($newDirectory2)) {
            mkdir($newDirectory2, 0777, true);
        }

        $template->setValues($tz);
        $template2->setValues($tz);

        $template->setComplexBlock('table_result', $table);
        $template2->setComplexBlock('table_result', $table);

        $template->saveAs($file);
        $template2->saveAs($file2);

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment;filename="Счет № ' . $tz['invNum'] . ' от ' . date("d.m.Y") . '.docx"');
        readfile($file);
        $converter = new OfficeConverter($fullPathDocx, $fullPathPDF);
        $converter->convertTo($outputFile);
    }

    /**
     * Формирование таблицы phpword для Счет-оферты
     * @param $info
     * @param $arrGost
     * @throws \NcJoes\OfficeConverter\OfficeConverterException
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function invoiceOfferDocument($info, $arrGost, $dealId, $dealInfo)
    {
        $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/OfferInvoice.docx';

        $type = 'dog';
        $countM = [];
        $result = [];
        //        $this->pre($arrGost);
        foreach ($arrGost as $key => $val) {
            if ($dealInfo['group_type'] == 1 || !$dealInfo['group_type']) {
                $result[$val['material_id']]['material_name'] = $val['material_name'];
                $result[$val['material_id']]['material_id'] = $val['material_id'];
                $result[$val['material_id']]['nfp'] = $val['name_for_protocol'];

                if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['material_id']]['gosts'])) {
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['amount'] += $val['amount'];
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['sum'] += $val['sum'];
                } else {
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                        'method_name' => $val['method_name'],
                        'gost_name' => $val['gost_name'],
                        'price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'sum' => $val['sum'],
                        'amount' => $val['amount'],
                    ];
                }

                $result[$val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 2) {
                $result[$val['name_for_protocol']]['material_name'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol']]['material_id'] = $val['material_id'];
                $result[$val['name_for_protocol']]['nfp'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol']]['is_only_name_for_protocol'] = 1;

                if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['name_for_protocol']]['gosts'])) {
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price']]['amount'] += $val['amount'];
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price']]['sum'] += $val['sum'];
                } else {
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                        'method_name' => $val['method_name'],
                        'gost_name' => $val['gost_name'],
                        'price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'sum' => $val['sum'],
                        'amount' => $val['amount'],
                    ];
                }

                $result[$val['name_for_protocol']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 3) {
                $result[$val['name_for_protocol'] . $val['material_id']]['material_name'] = $val['material_name'] . " ({$val['name_for_protocol']})";
                $result[$val['name_for_protocol'] . $val['material_id']]['material_id'] = $val['material_id'];
                $result[$val['name_for_protocol'] . $val['material_id']]['nfp'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = 1;

                if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['name_for_protocol'] . $val['material_id']]['gosts'])) {
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['amount'] += $val['amount'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['sum'] += $val['sum'];
                } else {
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                        'method_name' => $val['method_name'],
                        'gost_name' => $val['gost_name'],
                        'price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'sum' => $val['sum'],
                        'amount' => $val['amount'],
                    ];
                }

                $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 4) {
                if (!$val['group_id']) {
                    $result[$val['material_id']]['material_name'] = $val['material_name'];
                    $result[$val['material_id']]['material_id'] = $val['material_id'];
                    $result[$val['material_id']]['nfp'] = $val['name_for_protocol'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['material_id']]['gosts'])) {
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['amount'] += $val['amount'];
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                } else {
                    $result[$val['group_id']]['material_name'] = $val['material_name']; //  . " ({$val['group']})"
                    $result[$val['group_id']]['material_id'] = $val['material_id'];
                    $result[$val['group_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['group_id']]['group'] = $val['group_id'];
                    $result[$val['group_id']]['group_name'] = $val['group'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['group_id']]['gosts'])) {
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['amount'] += $val['amount'];
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['group_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                }
            } else {
                if (!$val['group_id']) {
                    $result[$val['name_for_protocol'] . $val['material_id']]['material_name'] = $val['material_name'] . ($val['name_for_protocol'] ? " ({$val['name_for_protocol']})" : "");
                    $result[$val['name_for_protocol'] . $val['material_id']]['material_id'] = $val['material_id'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = 1;

                    if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['name_for_protocol'] . $val['material_id']]['gosts'])) {
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['amount'] += $val['amount'];
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                } else {
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['material_name'] = $val['material_name'] . ($val['name_for_protocol'] ? " ({$val['name_for_protocol']})" : ""); //  . " ({$val['group']})"
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['material_id'] = $val['material_id'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['group'] = $val['group_id'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['group_name'] = $val['group'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'])) {
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['amount'] += $val['amount'];
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                }
            }
            $countM[] = $val['method_id'];
        }

        $info['countM'] = count($countM);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $template = new \PhpOffice\PhpWord\TemplateProcessor($template_doc);

        $styleTable = array('alignment' => 'center', 'cellMarginLeft' => 0, 'cellMarginRight' => 0, 'borderSize' => 5, 'borderColor' => '000000', 'bottomFromText' => 0);
        $styleTable = array('alignment' => 'center', 'cellMarginLeft' => 0, 'cellMarginRight' => 0, 'borderSize' => 5, 'borderColor' => '000000');
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan2 = array('gridSpan' => 2, 'valign' => 'center');
        $cellColSpan3 = array('gridSpan' => 3, 'valign' => 'center');
        $cellColSpan4 = array('gridSpan' => 4, 'valign' => 'center');
        $cellColSpan6 = array('gridSpan' => 6, 'valign' => 'center');
        $cellColSpan7 = array('gridSpan' => 7, 'valign' => 'center');
        $cellColSpan8 = array('gridSpan' => 8, 'valign' => 'center');

        $cellHCentered = array('align' => 'center');
        $cellAllCentered = array('align' => 'center', 'spaceAfter' => 0);
        $cellAll1Centered = array('align' => 'center', 'valign' => 'center', 'spaceAfter' => 0);
        $cellVCentered = array('valign' => 'center');

        //		$width = 9250;
        $width = 10250;
        $width1 = '100%';

        if ($info['nds']) {
            $count = 7;
            $spanCount = 7;
        } else {
            $count = 6;
            $spanCount = 6;
        }

        $columnWidth = $width / $count;

        //        $width = 10250;
        $width1 = '100%';
        $section = $phpWord->addSection();
        $table = $section->addTable($styleTable);
        $table->addRow(null, array('tblHeader' => true));
        //        $table->addCell($width / $count, $cellRowSpan)->addText('Наименование объекта испытаний (пробы/образца)', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        $table->addCell(self::centimeterSizeToTwips(5.24), $cellRowSpan)->addText('Определяемые характеристики', array('size' => 10, 5, 'bold' => true, 'name' => 'Times New Roman'), $cellHCentered);
        $table->addCell(self::centimeterSizeToTwips(2.5), $cellRowSpan)->addText('НД на метод испытания', array('size' => 10, 5, 'bold' => true, 'name' => 'Times New Roman'), $cellHCentered);
        $table->addCell(self::centimeterSizeToTwips(2), $cellRowSpan)->addText('НД на требования к объекту испытаний', array('size' => 10, 5, 'bold' => true, 'name' => 'Times New Roman'), $cellHCentered);
        $table->addCell(self::centimeterSizeToTwips(2), $cellRowSpan)->addText('Цена за пробу (образец), руб.', array('size' => 10, 5, 'bold' => true, 'name' => 'Times New Roman'), $cellHCentered);
        // if($info['nds']) {
        //if($info['nds_7']) {
        $table->addCell(self::centimeterSizeToTwips(2.27), $cellRowSpan)->addText('Цена за пробу (образец) с НДС (7%), руб.', array('size' => 10, 5, 'bold' => true, 'name' => 'Times New Roman'), $cellHCentered);
        // } else {
        //$table->addCell(self::centimeterSizeToTwips(2.27), $cellRowSpan)->addText('Цена за пробу (образец) с НДС (5%), руб.', array('size' => 10, 5, 'bold' => true, 'name' => 'Times New Roman'), $cellHCentered);
        // }
        // }

        $table->addCell(self::centimeterSizeToTwips(2), $cellRowSpan)->addText('Количество проб (образцов), шт.', array('size' => 10, 5, 'bold' => true, 'name' => 'Times New Roman'), $cellHCentered);

        $lastCellText = 'Стоимость за испытание';
        // if($info['nds']) {
        // if($info['nds_7']) {
        $lastCellText .= ' с учетом НДС (7%)';
        // } else {
        //$lastCellText .= ' с учетом НДС (5%)';
        //}
        //}
        $lastCellText .= ', руб.';

        $table->addCell(self::centimeterSizeToTwips(1.74), $cellRowSpan)->addText($lastCellText, array('size' => 10, 5, 'bold' => true, 'name' => 'Times New Roman'), $cellHCentered);
        $i = 1;
        $sumTotal = 0;
        $mat_id = '';
        $group_id = '';
        $nfp_id = '';
        //		$this->pre($result, true);
        foreach ($result as $itemMater) {
            if ($itemMater['group']) {
                if ($group_id != $itemMater['group']) {
                    if ($itemMater['is_only_name_for_protocol'] == 1) {
                        $obj = $itemMater['nfp'];
                    } else {
                        $obj = $itemMater['material_name'];
                    }
                    $table->addRow(400);
                    $table->addCell($columnWidth, $info['nds'] ? $cellColSpan7 : $cellColSpan6)->addText(strtoupper($obj), array('size' => 10, 'bold' => true, 'name' => 'Times New Roman'), $cellAll1Centered);
                    $group_id = $itemMater['group'];
                }
            } elseif ($itemMater['nfp']) {
                if ($nfp_id != $itemMater['nfp']) {
                    if ($itemMater['is_only_name_for_protocol'] == 1) {
                        $obj = $itemMater['nfp'];
                    } else {
                        $obj = $itemMater['material_name'];
                    }
                    $table->addRow(400);
                    $table->addCell($columnWidth, $info['nds'] ? $cellColSpan7 : $cellColSpan6)->addText(strtoupper($obj), array('size' => 10, 'bold' => true, 'name' => 'Times New Roman'), $cellAll1Centered);
                    $nfp_id = $itemMater['nfp'];
                }
            } else {
                if ($mat_id != $itemMater['material_id']) {
                    if ($itemMater['is_only_name_for_protocol'] == 1) {
                        $obj = $itemMater['nfp'];
                    } else {
                        $obj = $itemMater['material_name'];
                    }
                    $table->addRow(400);
                    $table->addCell($columnWidth, $info['nds'] ? $cellColSpan7 : $cellColSpan6)->addText(strtoupper($obj), array('size' => 10, 'bold' => true, 'name' => 'Times New Roman'), $cellAll1Centered);
                    $mat_id = $itemMater['material_id'];
                }
            }
            foreach ($itemMater['gosts'] as $k => $value) {
                // Расчет НДС в зависимости от типа
                if ($info['nds']) {
                    if ($info['nds_7']) {
                        $priceNds = $value['price'] * 0.07;
                        $ndsMultiplier = 1.07;
                    } else {
                        $priceNds = $value['price'] * 0.05;
                        $ndsMultiplier = 1.05;
                    }
                } else {
                    $priceNds = 0;
                    $ndsMultiplier = 1;
                }

                $table->addRow();
                //    if ($k == 0) {
                //        $obj = $itemMater['material_name'] . (!empty($itemMater['nfp']) ? '<w:br/>(' . $itemMater['nfp'] . ')' : '');
                //
                //        $table->addCell(null, $cellRowSpan)->addText($obj, array('size' => 9), $cellHCentered);
                //    } else {
                //        $table->addCell(null, $cellRowContinue);
                //    }
                $table->addCell(self::centimeterSizeToTwips(5.24))->addText($value['method_name'], array('size' => 10, 'name' => 'Times New Roman'), $cellAllCentered);
                $table->addCell(self::centimeterSizeToTwips(2.5))->addText($value['gost_name'], array('size' => 10, 'name' => 'Times New Roman'), $cellAllCentered);
                $table->addCell(self::centimeterSizeToTwips(2))->addText($value['tech_condition'], array('size' => 10, 'name' => 'Times New Roman'), $cellAllCentered);
                $table->addCell(self::centimeterSizeToTwips(2))->addText(number_format($value['price'], 2, ',', ' '), array('size' => 10, 'name' => 'Times New Roman'), $cellAllCentered);

                //if ($info['nds']) {
                $table->addCell(self::centimeterSizeToTwips(2.27))->addText(number_format($value['price'] + $priceNds, 2, ',', ' '), array('size' => 10, 'name' => 'Times New Roman'), $cellAllCentered);
                //}

                $table->addCell(self::centimeterSizeToTwips(2))->addText($value['amount'], array('size' => 10, 'name' => 'Times New Roman'), $cellHCentered);
                $table->addCell(self::centimeterSizeToTwips(1.74))->addText(number_format($value['sum'] * $ndsMultiplier, 2, ',', ' '), array('size' => 10, 'name' => 'Times New Roman'), $cellAllCentered);
            }
        }
        //		$this->pre(1);

        $this->generateDocumentWithTable($table, $info, $template_doc, $type);
    }

    /**
     * @param $protocolID
     */
    public function protocolGenerator($protocolID)
    {

        $companyModel = new Company();
        $requestModel = new Request();
        $historyModel = new History();
        $userModel = new User();
        $orderModel = new Order();
        $ProtocolModel = new Protocol();
        $ResultModel = new Result();
        $oborudModel = new Oborud();
        $materialModel = new Material();
        $methodModel = new Methods();
        $gostModel = new Gost();
        $labModel = new Lab();
        $requirementModel = new Requirement();
        $nkModel = new Nk;
        $generatorController = new GeneratorController();

        $protocol = $ProtocolModel->getProtocolById($protocolID);
        $order = $orderModel->getOrderByDealId($protocol['DEAL_ID']);
        $dealInformation = $requestModel->getTzByDealId($protocol['DEAL_ID']);
        $companyInformation = $companyModel->getRequisiteByDealId($protocol['DEAL_ID']);
        $probeResults = $ProtocolModel->getDataForDoc((int)$protocolID);

        //Данные для сохранения протокола
        $curdate = "Протокол_" . ($protocol['NUMBER'] ? $protocol['NUMBER'] : '') . "_" . date("d-m-Y_H-i-s");

        $this->DB->Query("UPDATE `PROTOCOLS` SET `ACTUAL_VERSION`= '" . $curdate . "' WHERE `ID`=" . $protocolID);

        //Дата начала и окончания испытаний
        $dateBegin = !empty($protocol['DATE_BEGIN']) ? date('d.m.Y', strtotime($protocol['DATE_BEGIN'])) : '';
        $dateEnd = !empty($protocol['DATE_END']) ? date('d.m.Y', strtotime($protocol['DATE_END'])) : '';

        //Номер заявки
        $requestNum = explode('№', $dealInformation['REQUEST_TITLE'])[1];

        //Дата заявки
        $requestDate = $dealInformation['DATE_CREATE'];

        //Заявка в поле основание
        $requestFor = " (Заявка №{$requestNum} от {$requestDate})";

        //Данные о заказчике
        $nInfo = $companyInformation['RQ_ACCOUNTANT'] . ' / ' . $companyInformation['address'][1]['ADDRESS_1'];

        if (in_array($protocol['PROTOCOL_TYPE'], [3, 4, 5])) {
            $header = $userModel->getUserData(60); //  А. В. Лапков
            $name = trim($header['NAME']);
            $lastName = trim($header['LAST_NAME']);
            $secondName = trim($header['SECOND_NAME']);
            $header['short_name'] = StringHelper::getInitials($name) . ' ' . StringHelper::getInitials($secondName) . ' ' . $lastName;
            $header['work_position'] = $header['WORK_POSITION'];
        } else {
            //Руководитель на подпись
            $header = $userModel->checkHeader(53);
        }


        //Получаем пробы в протоколе
        $count_probe_str = (count($probeResults) > 1 ? count($probeResults) . " " . StringHelper::numDeclension(count($probeResults), ['проба', 'пробы/образца', 'проб/образцов']) . ": " : "");

        //информация об объекте
        $material_probe = [];
        $gostsInProtocol = [];
        $condititionsInProtocol = [];
        $test = [];

        ksort($probeResults, SORT_NUMERIC);
        foreach ($probeResults as $k => $itt) {
            $test[$k] = explode('/', explode('.', $itt['cipher'])[1])[0];
        }
        asort($test, SORT_NUMERIC);
        $ArrayThree = [];
        foreach ($test as $key => $value) {
            $ArrayThree[$key] = $probeResults[$key];
        }

        $probeResults = $ArrayThree;

        $namesForProtocol = implode(', ', array_column($probeResults, 'name_for_protocol'));

        $cipherArray = [];

        $quarryArray = [];
        $needOneQuarry = true;

        foreach ($probeResults as $item) {
            $material_probe[] = $item['material_name'] . ($item['name_for_protocol'] ? ' ' . $item['name_for_protocol'] . ' (' . $item['cipher'] . ')' : ' (' . $item['cipher'] . ')');
            $cipherArray[$item['material_name']][] = $item['cipher'];

            if ($item['quarry']) {
                $quarryArray[$item['quarry']][] = $item['cipher'];
            } else {
                $needOneQuarry = false;
            }

            foreach ($item['gosts']['method'] as $key => $value) {
                if ($value['is_selection'] == 0) {
                    $gostsInProtocol[] = trim($value['reg_doc']) . (!empty($value['year']) ? '-' . trim($value['year']) : '') . ' ' . trim($value['description']);
                }

                //Документ, устанавливающий правила и методы исследований (испытаний) и измерений и ТУ
                $condititionInProtocol = $item['gosts']['condition'][$key];

                if ($condititionInProtocol['reg_doc'] == '-' || $condititionInProtocol['reg_doc'] == '--' || $condititionInProtocol['reg_doc'] == '') {
                } else {
                    $condititionsInProtocol[] = trim($condititionInProtocol['reg_doc']) . (!empty($condititionInProtocol['year']) ? '-' . trim($condititionInProtocol['year']) : '') . ' ' . trim($condititionInProtocol['name']);
                }
            }
        }

        $gostsInProtocolView = ucfirst(implode(', ', array_diff(array_unique($gostsInProtocol), [' '])));

        //		if (!$protocol['NO_COMPLIANCE']) {
        $condititionsInProtocolView = implode(', ', array_diff(array_unique($condititionsInProtocol), [' ']));
        //		} else {
        //			$condititionsInProtocolView = '';
        //		}

        // Вывод всех объектов с шифрами проб

        $allProbeInProtocol = '';

        $resultQuarryArray = [];

        foreach ($quarryArray as $key => $value) {
            if (count($quarryArray) == 1 && $needOneQuarry) {
                $resultQuarryArray[] = $key;
            } else {
                $resultQuarryArray[] = $key . " - " . implode(", ", $value);
            }
        }

        foreach ($cipherArray as $key => $value) {
            for ($i = 0; $i < count($material_probe); $i++) {

                $allProbeInProtocol .= $material_probe[$i] . ((count($value) - (int)$i) == 1 ? "" : ", ");
            }
            $superProbe = implode(', ', $material_probe);
            //            $number_probe = $count_probe_str . $allProbeInProtocol;
            $number_probe = $count_probe_str . $superProbe;
        }


        //Дата протокола
        $protocolDate = !empty($protocol['DATE']) ? date('d.m.Y', strtotime($protocol['DATE'])) : '';

        //Подпись в протоколе
        $verify = !empty($protocol['VERIFY']) ? array_unique(unserialize($protocol['VERIFY'])) : '';

        //Пробы отобраны не заказчиком
        if (in_array($protocol['PROTOCOL_TYPE'], [3, 4, 5])) {
            $selectionType = '';
        } else {
            $selectionType = $dealInformation['SELECTION_TYPE'] ? '' : '– пробы отобраны и доставлены в ИЦ заказчиком;';
        }


        //Продпись в протоколе
        $verifyArr = [];
        $arrVerify = array_unique(unserialize($protocol['VERIFY']));

        foreach ($arrVerify as $userVerify) {
            $user = $userModel->getUserShortById($userVerify);

            //            $this->pre($user);

            $verifyArr[] = [
                'WorkPos' => $user['work_position'],
                'Ispolnitel' => $user['short_name'],
                'Ispolnitel2' => 'подписано ' . $user['short_name'],
                'IC' => ($user['department'] == 58 ? '' : ' ИЦ')
            ];
        }

        $createProtocolUser = $userModel->getUserShortById($protocol['user_create_protocol']);
        $createProtocolUserArr = [
            'WorkPos' => $createProtocolUser['work_position'],
            'Ispolnitel' => $createProtocolUser['short_name'],
            'IC' => ($createProtocolUser['department'] == 58 ? '' : ' ИЦ')
        ];

        $createProtocolUserStr = "";

        if (!empty($createProtocolUserArr['Ispolnitel'])) {
            if ($protocol['PROTOCOL_TYPE'] == 1 || $protocol['PROTOCOL_TYPE'] == 33 || $protocol['PROTOCOL_TYPE'] == 44 || $protocol['PROTOCOL_TYPE'] == 60) {
                $createProtocolUserStr = "Протокол оформил(-а):<w:br/>{$createProtocolUserArr['WorkPos']} ООО «УралНИИстром» {$createProtocolUserArr['Ispolnitel']}";
            } elseif ($protocol['PROTOCOL_TYPE'] == 0) {
                $createProtocolUserStr = "Протокол оформил(-а):<w:br/>{$createProtocolUserArr['WorkPos']} ООО «УралНИИстром»____________________{$createProtocolUserArr['Ispolnitel']}";
            } elseif ($protocol['PROTOCOL_TYPE'] == 55 || $protocol['PROTOCOL_TYPE'] == 56) {
                $createProtocolUserStr = "\n<w:br/>Протокол оформил(-а):<w:br/>{$createProtocolUserArr['WorkPos']} ООО «УралНИИстром»__________{$createProtocolUserArr['Ispolnitel']}";
            } elseif ($protocol['PROTOCOL_TYPE'] == 57 || $protocol['PROTOCOL_TYPE'] == 58) {
                $createProtocolUserStr = "\n<w:br/>Протокол оформил(-а):<w:br/>{$createProtocolUserArr['WorkPos']} ООО «УралНИИстром» {$createProtocolUserArr['Ispolnitel']}";
            } else {
                $createProtocolUserStr = "Протокол оформил(-а):<w:br/>{$createProtocolUserArr['WorkPos']} ООО «УралНИИстром»__________{$createProtocolUserArr['Ispolnitel']}";
            }
        }

        if ($protocol['PROTOCOL_TYPE'] == 0 || $protocol['PROTOCOL_TYPE'] == 43) {
            $createNormProtocolUser = $userModel->getUserShortById($protocol['user_norm_protocol']);
            if (empty($createNormProtocolUser)) {
                $createNormProtocolUserStr = '';
            } else {
                $createNormProtocolUserArr = [
                    'WorkPos' => $createNormProtocolUser['work_position'],
                    'Ispolnitel' => $createNormProtocolUser['short_name'],
                    'IC' => ($createNormProtocolUser['department'] == 58 ? '' : ' ИЦ')
                ];
                $createNormProtocolUserStr = "Нормоконтроль:<w:br/>{$createNormProtocolUserArr['WorkPos']} ООО «УралНИИстром»__________{$createNormProtocolUserArr['Ispolnitel']}";
            }
        } else {
            $createNormProtocolUserStr = '';
        }

        $pressureStart = $labModel->getPressureByDate(date('Y-m-d', strtotime($dateBegin)))[0]['pressure'];
        $pressureEnd = $labModel->getPressureByDate(date('Y-m-d', strtotime($dateEnd)))[0]['pressure'];

        $conditionsArray = $labModel->getConditionByProtocol($protocolID);
        //number_format($pressureStart, 1, ',', '')
        $temperatureProtocol = number_format((($conditionsArray['max_temp'] + $conditionsArray['min_temp']) / 2), 2, ',', '');
        $humidityProtocol = number_format((($conditionsArray['max_humidity'] + $conditionsArray['min_humidity']) / 2), 2, ',', '');
        $pressureProtocol = number_format((($pressureStart + $pressureEnd) / 2), 2, ',', '');

        //		$this->pre($number_probe);
        $order111 = array("\r\n", "\n", "\r");
        $replace111 = '<w:br/>';
        //Таблица информации в протокол
        $comp = !$protocol['NO_COMPLIANCE'] ? "– заключение о соответствии принято на основании документа «Правила принятия решений в ИЦ ООО «УралНИИстром»;" : '';
        //		$this->pre($protocol['NO_COMPLIANCE']);

        $verifyText = '';

        if (empty($verify)) {
            $verifyText = '';
        } else {
            $verifyText = count($verify) > 1 ? 'Исследования и испытания проводили' : 'Исследования и испытания проводил';
        }

        $getActData = $requestModel->getActDataByDealID($protocol['DEAL_ID']);
        $clientTaken = (!$getActData['SELECTION_TYPE'] || $getActData['SELECTION_TYPE'] == 0) ? true : false;

        $determinedCharacteristics = $requestModel->getDeterminedCharacteristicsFromProtocolId($protocolID);
        $determinedCharacteristicsStr = implode(', ', $determinedCharacteristics);

        //        if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
        //            $protocol['DESCRIPTION'] = str_replace("\n", '<w:br/>', htmlspecialchars($protocol['DESCRIPTION']));
        //        }


        //if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
        $text = html_entity_decode($protocol['DESCRIPTION']);
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $text));

        $styles = '<w:i/><w:sz w:val="20"/>';

        $protocolDesc = $lines[0];

        for ($i = 1; $i < count($lines); $i++) {
            if (trim($lines[$i]) !== '') {
                $protocolDesc .= '<w:br/><w:r><w:rPr>' . $styles . '</w:rPr><w:tab/></w:r><w:r><w:rPr>' . $styles . '</w:rPr><w:t>' . $lines[$i] . '</w:t></w:r>';
            } else {
                $protocolDesc .= '<w:br/>';
            }
        }
        //            $pdArray = explode("\n", $protocolDesc);
        //
        //            $protocolDesc = '';
        //
        //            foreach ($pdArray as $value) {
        //                $protocolDesc .= $value . '<w:br/>' . "\n";
        //            }
        //        } else {
        //            $protocolDesc = trim(str_replace(["\r\n", "\n", "\r"], '<w:br/>', html_entity_decode($protocol['DESCRIPTION'])));
        //        }
        //$this->pre($protocolDesc);
        $protocolInformation = [
            'protocol_type' => htmlspecialchars($protocol['PROTOCOL_TYPE']),
            'tz_id' => htmlspecialchars($protocol['ID_TZ']),
            'protocolYear' => htmlspecialchars(date('Y', strtotime($protocol['DATE']))),
            'id' => htmlspecialchars($protocolID),
            'curdate' => htmlspecialchars($curdate),
            'comp' => htmlspecialchars($comp),
            'number' => htmlspecialchars($protocol['NUMBER']),
            'nProtocol' => htmlspecialchars($protocol['NUMBER_AND_YEAR']),
            'A' => ($protocol['IN_ATTESTAT_DIAPASON'] ? 'A' : ''),
            'Attestat' => ($protocol['ATTESTAT_IN_PROTOCOL'] ? ' Уникальный номер записи об аккредитации в реестре аккредитованных лиц RA.RU.21ЧЦ49' : ''),
            'Attestat2' => ($protocol['ATTESTAT_IN_PROTOCOL'] ? '' : ''),
            'dProtocol' => ($protocolDate ? date("d.m.Y", strtotime($protocolDate)) : date("d.m.Y")),
            'nDogovor' => htmlspecialchars($order) . htmlspecialchars($requestFor),
            'nZakazchik' => htmlspecialchars($companyInformation['NAME']),
            'nZakazchikFull' => htmlspecialchars($companyInformation['RQ_COMPANY_FULL_NAME']),
            'innZakazchik' => htmlspecialchars($companyInformation['RQ_INN']),
            'ogrnZakazchik' => htmlspecialchars($companyInformation['RQ_OGRN']),
            'dealNum' => htmlspecialchars($requestNum),
            'aZakazchik' => htmlspecialchars($nInfo),
            'header' => htmlspecialchars($header['short_name']),
            'work_position' => htmlspecialchars($header['work_position']),
            //			'date_sign' => '111',
            'date_sign' => '${date_sign}',
            'dProbe' => $dealInformation['DATE_ACT'] ? date("d.m.Y", strtotime($dealInformation['DATE_ACT'])) : '',
            'nProbe_reg' => htmlspecialchars($dealInformation['NUM_ACT_TABLE']),
            'oStroit' => !empty($protocol['OBJECT']) ? htmlspecialchars($protocol['OBJECT']) : '',
            'oIspit' => htmlspecialchars($number_probe),
            'namesForProtocol' => htmlspecialchars($namesForProtocol),
            'metodIspit' => htmlspecialchars($gostsInProtocolView),
            'normDoc' => !empty($condititionsInProtocolView) ? htmlspecialchars($condititionsInProtocolView) : '-',
            'mIspit' => '',
            'celIspit' => '',
            'd1Ispit' => htmlspecialchars($dateBegin),
            'd2Ispit' => strtotime($dateEnd) == strtotime($dateBegin) ? '' : '-' . date('d.m.Y', strtotime($dateEnd)),
            'tIspit' => $protocol['TEMP_O'] == $protocol['TEMP_TO_O'] ? number_format($protocol['TEMP_O'], 1, ',', '') . " " : number_format($protocol['TEMP_O'], 1, ',', '') . " - " . number_format($protocol['TEMP_TO_O'], 1, ',', ''),
            'vIspit' => $protocol['VLAG_O'] == $protocol['VLAG_TO_O'] ? number_format($protocol['VLAG_O'], 1, ',', '') : number_format($protocol['VLAG_O'], 1, ',', '') . " - " . number_format($protocol['VLAG_TO_O'], 1, ',', ''),
            'pIspit' => $pressureEnd == $pressureStart ? number_format($pressureStart, 1, ',', '') : number_format($pressureStart, 1, ',', '') . " - " . number_format($pressureEnd, 1, ',', ''),
            //			'dopInfo' => trim(html_entity_decode($protocol['DOP_INFO'])),
            //			'dopInfo' => trim(html_entity_decode(str_replace(["\r\n", "\n", "\r"], '<w:br/>', $protocol['DOP_INFO']))),
            'dopInfo1' => html_entity_decode(trim($protocol['DOP_INFO'])),
            'dopInfo2' => html_entity_decode(trim($protocol['DOP_INFO2'])),
            //			'dopInfo' => trim(str_replace(["\r\n", "\n", "\r"], '<w:br/>'. "\n", htmlspecialchars($protocol['DOP_INFO']))),
            'dopInfo' => trim(str_replace(["\r\n", "\n", "\r"], '<w:br/>' . "\n", html_entity_decode($protocol['DOP_INFO']))),
            //			'dopInfo' => trim(html_entity_decode(str_replace(["\r\n", "\n", "\r"], "'&#182;'", $protocol['DOP_INFO']))),
            'Description' => $protocolDesc,
            'Description2' => $protocol['DESCRIPTION'],
            'metodOtbor' => '',
            'ISP' => htmlspecialchars($verifyText),
            'Point' => '',
            'OtborProb' => htmlspecialchars($selectionType),
            'table_verify' => $verifyArr['WorkPos'] . ' ' . $verifyArr['IC'] . ' ООО "УралНИИстром __________' . htmlspecialchars($verifyArr['Ispolnitel']),
            'verify_arr' => htmlspecialchars($verifyArr),
            'mestoSboraProbe' => (!empty($protocol['PLACE_PROBE']) ? htmlspecialchars(trim($protocol['PLACE_PROBE'])) : '-') . '; ' . (!empty($protocol['DATE_PROBE']) && $protocol['DATE_PROBE'] != '0000-00-00' ? date('d.m.Y', strtotime($protocol['DATE_PROBE'])) : '-'),
            'mestoSboraProbe2' => (!empty($protocol['PLACE_PROBE']) ? htmlspecialchars(trim($protocol['PLACE_PROBE'])) : '-'),
            'dateOtborProbe' => (!empty($protocol['DATE_PROBE']) && $protocol['DATE_PROBE'] != '0000-00-00' ? date('d.m.Y', strtotime($protocol['DATE_PROBE'])) : '-'),
            'inputInProtocol' => htmlspecialchars($protocol['OUTPUT_IN_PROTOCOL']),
            'dealTypeId' => htmlspecialchars($dealInformation['TYPE_ID']),
            'automatic_comments' => htmlspecialchars($protocol['automatic_comments']),
            'career' => htmlspecialchars(implode("; ", $resultQuarryArray)),
            'clientTaken' => $clientTaken,
            'determined_characteristics' => $protocol['determined_characteristics'],
            'determined_characteristics_list' => $determinedCharacteristicsStr,
            'temp' => $temperatureProtocol,
            'hum' => $humidityProtocol,
            'press' => $pressureProtocol,
            'create_protocol' => $createProtocolUserStr,
            'norm_protocol' => $createNormProtocolUserStr,
            'osk_1_name' => $protocol['osk_1_name'],
            'osk_1_norm' => $protocol['osk_1_norm'],
            'osk_2_norm' => $protocol['osk_2_norm'],
            'osk_2_fact' => $protocol['osk_2_fact'],
            'osk_1_place' => $protocol['osk_1_place'],
        ];
        //$this->pre($protocolInformation);
        //Таблица оборудования
        $oborudData = $oborudModel->getTzObConnectByProtocolId2($protocolID);
        foreach ($oborudData as &$equipment) {
            if (isset($equipment['certificates']) && is_array($equipment['certificates'])) {
                $equipment['certificates'] = array_filter(
                    $equipment['certificates'],
                    function ($certificate) {
                        return isset($certificate['in_protocol']) && $certificate['in_protocol'] == 1;
                    }
                );

                // Переиндексируем массив
                $equipment['certificates'] = array_values($equipment['certificates']);

                // Если массив certificates пуст - удаляем его
                if (empty($equipment['certificates'])) {
                    unset($equipment['certificates']);
                }
            }
        }
        unset($equipment);

        //Выбор скртификата поверки
        //		foreach ($oborudData as $k => $ob) {
        ////			$certificateOborud = $oborudModel->getCertificateByOborudId($k);
        //			$certificateOborud = $oborudModel->getCertificateByOborud($k);
        //			$oborudData[$k]['certificate'] = $certificateOborud;
        //		}

        //Результаты испытаний
        $results = [];
        $frost = [];
        $deformation = [];
        $tableData = [];

        $needBiggest = false;
        $needSmaller = false;
        $tableZern = [];
        $tableResults = [];

        foreach ($probeResults as $k => $val) {
            $object = $val['material_name'] . ' ' . $val['name_for_protocol'] . ' (' . $val['cipher'] . ')';
            if ($protocolID == 6179) {
                $object = $val['name_for_protocol'] . ' (' . $val['cipher'] . ')';
            } elseif ($protocol['DEAL_ID'] == 14830) {
                $object = $val['material_name'] . ' (' . $val['cipher'] . ')';
            }
            $count_val = 0;
            foreach ($val['gosts']['method'] as $key => $method) {

                if (!$method['is_confirm'] && $method['name'] != 'Подготовка проб') {
                    $_SESSION['message_danger'] = "{$method['name']} вне области, невозможно сформировать протокол!";
                    header("Location: /ulab/result/card_oati/{$protocol['DEAL_ID']}?protocol_id={$protocolID}");
                    exit();
                }

                if ($method['is_selection']) {
                    continue;
                }

                $condition = $val['gosts']['condition'][$key];
                $resultValue = $val['gosts']['result'][$key];

                // В ГОСТе Методике "Факт. значения текстом" И есть ТУ и И ТУ НЕ Ручное управление "соотв/не соотв",
                // то Параметр М-ки(ф/значение текстом) не соотв. параметру в ТУ (управление не вручную), не должно попадать в протокол
                if ($protocol['DEAL_ID'] >= DEAL_NEW_RESULT) {
                    if (!empty($method['is_text_fact']) && !empty($condition['id']) && empty($condition['is_manual'])) {
                        continue;
                    }
                }


                $measuringSheet = $protocol['DEAL_ID'] >= DEAL_NEW_RESULT ?
                    json_decode($val['measuring_sheet'][$key], true) : json_decode($resultValue['measuring_sheet'], true);

                // Если в ТУ "Текст нормативного значения по ГОСТу" выводиться в протокол, то берем нормативное значение "Текст нормативного значения по ГОСТу"
                if (/*$protocol['DEAL_ID'] >= DEAL_NEW_RESULT && */empty($condition['is_manual']) && !empty($condition['is_output'])) {
                    $normval = $condition['norm_comment'];
                } else {
                    $normativeValue = !empty($resultValue['normative_value']) ? trim(htmlspecialchars_decode($resultValue['normative_value'])) : '-';
                    $normval = $protocol['DEAL_ID'] >= DEAL_NEW_RESULT ? $val['normative_val'][$key] : $normativeValue;
                }

                $match_value = [
                    '0' => 'Не соответствует',
                    '1' => 'Соответствует',
                    '2' => '-',
                    '3' => 'Не нормируется',
                ];
                $actualValue = $val['actual_value'][$key];
                // $matchValue = $protocol['DEAL_ID'] >= DEAL_NEW_RESULT ?
                //     $match_value[$val['match'][$key]] : $match_value[$resultValue['match']];

                // $actualValue = $protocol['DEAL_ID'] >= DEAL_NEW_RESULT ?
                //      : json_decode($resultValue['actual_value'], true)[0];

                // $singleValue2 = $protocol['DEAL_ID'] >= DEAL_NEW_RESULT ?
                //     $val['single_value'][$key] : json_decode($resultValue['single_value'], true)[0];

                $ugtpDesc = $val['ugtp_desc'][$key];
                $ugtpDesc2 = [];
                foreach ($val['is_single_values'] as $descKey => $isSingle) {
                    if ($isSingle == 1 && !empty($val['ugtp_desc'][$descKey])) {
                        $ugtpDesc2[] = $val['ugtp_desc'][$descKey];
                    }
                }
                foreach ($val['is_only_single_values'] as $descKey => $isOnlySingle) {
                    if ($isOnlySingle == 1 && !empty($val['ugtp_desc'][$descKey])) {
                        $ugtpDesc2[] = $val['ugtp_desc'][$descKey];
                    }
                }
                $ugtpDesc2 = !empty($ugtpDesc2) ? implode("\n<w:br/>", $ugtpDesc2) : '';

                if (
                    empty($method['is_text_fact']) &&
                    !empty($method['is_range_text']) &&
                    is_numeric($actualValue)
                ) {
                    // Внутренний диапазон
                    if ($method['definition_range_type'] == 1) {
                        if ($actualValue < $method['definition_range_1']) {
                            $method['definition_range_1'] = str_replace('.', ',', $method['definition_range_1']);
                            $actualValue = "{$method['range_text_in']} {$method['definition_range_1']}";
                            $needSmaller = true;
                        } elseif ($actualValue > $method['definition_range_2']) {
                            $method['definition_range_2'] = str_replace('.', ',', $method['definition_range_2']);
                            $actualValue = "{$method['range_text_out']} {$method['definition_range_2']}";
                            $needBiggest = true;
                        }
                    } else if ($method['definition_range_type'] == 2) { // Внешний диапазон
                        if ($actualValue > $method['definition_range_1']) {
                            $method['definition_range_1'] = str_replace('.', ',', $method['definition_range_1']);
                            $actualValue = "{$method['range_text_out']} {$method['definition_range_1']}";
                            $needBiggest = true;
                        } elseif ($actualValue < $method['definition_range_2']) {
                            $method['definition_range_2'] = str_replace('.', ',', $method['definition_range_2']);
                            $actualValue = "{$method['range_text_in']} {$method['definition_range_2']}";
                            $needSmaller = true;
                        }
                    }
                }
                $actualValue = is_numeric($actualValue) ? str_replace('.', ',', $actualValue) : $actualValue;
                $actualValue = htmlentities($actualValue);

                // если есть лист измерений
                if (isset($measuringSheet)) {
                    if ($measuringSheet['type'] == 'grain' && $measuringSheet['checkbox'] != 1) {
                        //                        $this->pre(111);
                        $methodInfo = $methodModel->get($measuringSheet['method_id']);
                        $materialInfo = $gostModel->getMaterialByUgtpId($measuringSheet['ugtp_id']);

                        if ($measuringSheet['fineness_module'] != '') {
                            $results[] = [
                                'Object' => $object,
                                'cipher' => $val['cipher'],
                                'custom_cipher' => $val['name_for_protocol'],
                                'Specification' => 'Модуль крупности',
                                //'Specification' => $method['name'],
                                'SpecificationTU' => !empty($measuringSheet['spec_fineness_module']) ? $measuringSheet['spec_fineness_module'] : '-',
                                'Ed' => '-',
                                //'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                                'Normdoc' => !empty($measuringSheet['tech_fineness_module']) ? $measuringSheet['tech_fineness_module'] : '-',
                                'Normval' => $measuringSheet['norm_fineness_module'],
                                'Normdocmet' => $method['view_gost_for_protocol'],
                                'Normdoc_desc' => '',
                                'Values' => str_replace('.', ',', $measuringSheet['fineness_module']),
                                'Value' => ($condition['type'] == "TU_sred" || $condition['type'] == "TU_sred3" || $condition['type'] == "TU_sred2" || $condition['type'] == "TU_sred4" || $condition['type'] == "TU_sred5") ? $resultValue['average_value'] : "-",
                                'Result' => !empty($measuringSheet['match_fineness_module']) ? (($measuringSheet['match_fineness_module'] == 1) ? 'Соответствует' : (($measuringSheet['match_fineness_module'] == 2) ? 'Не соответствует' : '-')) : '-',
                                'id' => $k,
                                'ugtp_desc' => $ugtpDesc,
                                'ugtp_desc2' => $ugtpDesc2
                            ];
                        }

                        if ($measuringSheet['p063'] != '') {
                            $condition_sand = $gostModel->getCondition(1356);
                            $results[] = [
                                'Object' => $object,
                                'cipher' => $val['cipher'],
                                'custom_cipher' => $val['name_for_protocol'],
                                //'Specification' => $method['name'],
                                'Specification' => 'Полный остаток на сите',
                                'SpecificationTU' => !empty($measuringSheet['spec_p063']) ? $measuringSheet['spec_p063'] : '-',
                                'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                                'Normdoc' => !empty($measuringSheet['tech_p063']) ? $measuringSheet['tech_p063'] : '-',
                                'Normval' => $measuringSheet['norm_p063'],
                                'Normdocmet' => $method['view_gost_for_protocol'],
                                'Normdoc_desc' => '',
                                'Values' => str_replace('.', ',', $measuringSheet['p063']),
                                'Value' => ($condition['type'] == "TU_sred" || $condition['type'] == "TU_sred3" || $condition['type'] == "TU_sred2" || $condition['type'] == "TU_sred4" || $condition['type'] == "TU_sred5") ? $resultValue['average_value'] : "-",
                                'Result' => !empty($measuringSheet['match_p063']) ? (($measuringSheet['match_p063'] == 1) ? 'Соответствует' : (($measuringSheet['match_p063'] == 2) ? 'Не соответствует' : '-')) : '-',
                                'id' => $k,
                                'ugtp_desc' => $ugtpDesc,
                                'ugtp_desc2' => $ugtpDesc2
                            ];
                        }

                        if ($measuringSheet['z10'] != '') {
                            $condition_sand = $gostModel->getCondition(1357);
                            $results[] = [
                                'Object' => $object,
                                'cipher' => $val['cipher'],
                                'custom_cipher' => $val['name_for_protocol'],
                                'Specification' => $method['name'],
                                'SpecificationTU' => !empty($measuringSheet['spec_z10']) ? $measuringSheet['spec_z10'] : '-',
                                'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                                'Normdoc' => !empty($measuringSheet['tech_z10']) ? $measuringSheet['tech_z10'] : '-',
                                'Normval' => $measuringSheet['norm_z10'],
                                'Normdocmet' => $method['view_gost_for_protocol'],
                                'Normdoc_desc' => '',
                                'Values' => str_replace('.', ',', $measuringSheet['z10']),
                                'Value' => ($condition['type'] == "TU_sred" || $condition['type'] == "TU_sred3" || $condition['type'] == "TU_sred2" || $condition['type'] == "TU_sred4" || $condition['type'] == "TU_sred5") ? $resultValue['average_value'] : "-",
                                'Result' => !empty($measuringSheet['match_z10']) ? (($measuringSheet['match_z10'] == 1) ? 'Соответствует' : (($measuringSheet['match_z10'] == 2) ? 'Не соответствует' : '-')) : '-',
                                'id' => $k,
                                'ugtp_desc' => $ugtpDesc,
                                'ugtp_desc2' => $ugtpDesc2
                            ];
                        }

                        if ($measuringSheet['z5'] != '') {
                            $condition_sand = $gostModel->getCondition(1437);
                            $results[] = [
                                'Object' => $object,
                                'cipher' => $val['cipher'],
                                'custom_cipher' => $val['name_for_protocol'],
                                'Specification' => $method['name'],
                                'SpecificationTU' => !empty($measuringSheet['spec_z5']) ? $measuringSheet['spec_z5'] : '-',
                                'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                                'Normdoc' => !empty($measuringSheet['tech_z5']) ? $measuringSheet['tech_z5'] : '-',
                                'Normval' => $measuringSheet['norm_z5'],
                                'Normdocmet' => $method['view_gost_for_protocol'],
                                'Normdoc_desc' => '',
                                'Values' => str_replace('.', ',', $measuringSheet['z5']),
                                'Value' => ($condition['type'] == "TU_sred" || $condition['type'] == "TU_sred3" || $condition['type'] == "TU_sred2" || $condition['type'] == "TU_sred4" || $condition['type'] == "TU_sred5") ? $resultValue['average_value'] : "-",
                                'Result' => !empty($measuringSheet['match_z5']) ? (($measuringSheet['match_z5'] == 1) ? 'Соответствует' : (($measuringSheet['match_z5'] == 2) ? 'Не соответствует' : '-')) : '-',
                                'id' => $k,
                                'ugtp_desc' => $ugtpDesc,
                                'ugtp_desc2' => $ugtpDesc2
                            ];
                        }

                        if ($measuringSheet['z016'] != '') {
                            $condition_sand = $gostModel->getCondition(1438);
                            $results[] = [
                                'Object' => $object,
                                'cipher' => $val['cipher'],
                                'custom_cipher' => $val['name_for_protocol'],
                                'Specification' => $method['name'],
                                'SpecificationTU' => !empty($measuringSheet['spec_z016']) ? $measuringSheet['spec_z016'] : '-',
                                'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                                'Normdoc' => !empty($measuringSheet['tech_z016']) ? $measuringSheet['tech_z016'] : '-',
                                'Normval' => $measuringSheet['norm_z016'],
                                'Normdocmet' => $method['view_gost_for_protocol'],
                                'Normdoc_desc' => '',
                                'Values' => str_replace('.', ',', $measuringSheet['z016']),
                                'Value' => ($condition['type'] == "TU_sred" || $condition['type'] == "TU_sred3" || $condition['type'] == "TU_sred2" || $condition['type'] == "TU_sred4" || $condition['type'] == "TU_sred5") ? $resultValue['average_value'] : "-",
                                'Result' => !empty($measuringSheet['match_z016']) ? (($measuringSheet['match_z016'] == 1) ? 'Соответствует' : (($measuringSheet['match_z016'] == 2) ? 'Не соответствует' : '-')) : '-',
                                'id' => $k,
                                'ugtp_desc' => $ugtpDesc,
                                'ugtp_desc2' => $ugtpDesc2
                            ];
                        }

                        //$this->pre($measuringSheet['in_protocol']);
                        $table = [];
                        foreach ($measuringSheet['in_protocol'] as $key) {
                            if ($measuringSheet['p063'] != '' && in_array($key, [0, 1])) {
                                continue;
                            }
                            $zern = $materialModel->getSieveAndNorm($measuringSheet['zern']);
                            $table = [
                                'id' => $k,
                                'name_table'      => '${table_zern}',
                                'num_table'       => 2,
                                'a'               => [],
                                'p'               => [],
                                'norm'            => [],
                                'title'           => [],
                                'name'            => $zern['NAME'],
                                'a_in_protocol'   => $measuringSheet['a_in_protocol'],
                                'p_in_protocol'   => $measuringSheet['p_in_protocol'],
                                'req_in_protocol' => $measuringSheet['req_in_protocol'],
                                'initial_mass'    => $measuringSheet['initial_mass'],
                                'name_material'   => $object,
                                'cipher'          => $materialInfo['cipher'],
                                'method'          => [
                                    'view' => $methodInfo['view_gost_for_protocol'],
                                    'name' => $methodInfo['mp_name'],
                                    'unit' => $methodInfo['unit_rus']
                                ],
                            ];
                            foreach ($measuringSheet['in_protocol'] as $key) {
                                if ($measuringSheet['p063'] != '' && in_array($key, [0, 1])) {
                                    continue;
                                }
                                $table['a'][]     = str_replace('.', ',', $measuringSheet['a'][$key]);
                                $table['p'][]     = str_replace('.', ',', $measuringSheet['p'][$key]);
                                $table['norm'][]  = $measuringSheet['norm'][$key];
                                $table['title'][] = $measuringSheet['title'][$key];
                            }
                        }
                        $tableZern[] = $table;

                        //						$this->pre($tableZern);
                    } elseif ($measuringSheet['type'] == 'sred') {
                        $eol = '<w:br/>';
                        //						$this->pre($measuringSheet);
                        // Если фактическое значение пустое, то невыводим в протокол
                        if ($actualValue === '' || !isset($actualValue)) {
                            continue;
                        }

                        //                        $this->pre($measuringSheet['actual_value']);

                        if ($measuringSheet['is_only_single_values']) {
                            $tableData['sred_only']['name_table'] = '${table_sred}';

                            $tableData['sred_only'][] = [
                                'Object' => $object,
                                'cipher' => $val['cipher'],
                                'custom_cipher' => $val['name_for_protocol'],
                                'Specification' => $method['name'],
                                'SpecificationTU' => !empty($condition['measured_properties_name']) ? $condition['measured_properties_name'] : '-',
                                'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                                'Normdoc' => !empty($condition['reg_doc']) ? $condition['reg_doc'] . (!empty($condition['year']) ? '-' . $condition['year'] : '') . (!empty($condition['clause']) ? ' ' . $condition['clause'] : '') : '-',
                                'Normval' => $normval,
                                'Normdocmet' => $method['view_gost_for_protocol'],
                                'Normdoc_desc' => '',
                                'SingleValues' => $measuringSheet['actual_value'] ? implode($eol, str_replace('.', ',', $measuringSheet['actual_value'])) : '-',
                                'Value' => "-",
                                'Values' => "-",
                                'Result' => $matchValue,
                                'id' => $k,
                                'ugtp_desc' => $ugtpDesc,
                                'ugtp_desc2' => $ugtpDesc2
                            ];

                            // Данные для упрощённого протокола с единичными и без единичных значений
                            $protocolShort = $protocolInformation['protocol_type'] == 34 || $protocolInformation['protocol_type'] == 33;
                            if ($protocolShort) {
                                $results[] = [
                                    'Object' => $object,
                                    'cipher' => $val['cipher'],
                                    'custom_cipher' => $val['name_for_protocol'],
                                    'Specification' => $method['name'],
                                    'SpecificationTU' => !empty($condition['measured_properties_name']) ? $condition['measured_properties_name'] : '-',
                                    'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                                    'Normdoc' => !empty($condition['reg_doc']) ? $condition['reg_doc'] . (!empty($condition['year']) ? '-' . $condition['year'] : '') . (!empty($condition['clause']) ? ' ' . $condition['clause'] : '') : '-',
                                    'Normval' => $normval,
                                    'Normdocmet' => $method['view_gost_for_protocol'],
                                    'Normdoc_desc' => '',
                                    //                                    'SingleValues' => $measuringSheet['actual_value'] ? implode($eol, str_replace('.', ',', $measuringSheet['actual_value'])) : '-',
                                    'SingleValues' => $measuringSheet['actual_value'] ? implode('  <w:br/>  ', str_replace('.', ',', $measuringSheet['actual_value'])) : '-',
                                    'Value' => "-",
                                    'Values' => "-",
                                    'Result' => $matchValue,
                                    'id' => $k,
                                    'ugtp_desc' => $ugtpDesc,
                                    'ugtp_desc2' => $ugtpDesc2
                                ];
                            }
                        }
                        // Данные для отдельной таблицы с единичными значениями, если отмечен checkbox "Выводить единичные значения в протокол?"
                        else if ($measuringSheet['is_single_values']) {
                            $tableData['sred']['name_table'] = '${table_sred}';

                            $tableData['sred'][] = [
                                'Object' => $object,
                                'cipher' => $val['cipher'],
                                'custom_cipher' => $val['name_for_protocol'],
                                'Specification' => $method['name'],
                                'SpecificationTU' => !empty($condition['measured_properties_name']) ? $condition['measured_properties_name'] : '-',
                                'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                                'Normdoc' => !empty($condition['reg_doc']) ? $condition['reg_doc'] . (!empty($condition['year']) ? '-' . $condition['year'] : '') . (!empty($condition['clause']) ? ' ' . $condition['clause'] : '') : '-',
                                'Normval' => $normval,
                                'Normdocmet' => $method['view_gost_for_protocol'],
                                'Normdoc_desc' => '',
                                'SingleValues' => $measuringSheet['actual_value'] ? implode($eol, str_replace('.', ',', $measuringSheet['actual_value'])) : '-',
                                'Value' => ($condition['type'] == "TU_sred"
                                    || $condition['type'] == "TU_sred3"
                                    || $condition['type'] == "TU_sred2"
                                    || $condition['type'] == "TU_sred4"
                                    || $condition['type'] == "TU_sred5"
                                ) ?
                                    number_format($resultValue['average_value'], $measuringSheet['decimal_places'], ',', '') : "-",
                                'Values' => $actualValue,
                                'Result' => $matchValue,
                                'id' => $k,
                                'ugtp_desc' => $ugtpDesc,
                                'ugtp_desc2' => $ugtpDesc2
                            ];

                            // Данные для упрощённого протокола с единичными и без единичных значений
                            $protocolShort = $protocolInformation['protocol_type'] == 34 || $protocolInformation['protocol_type'] == 33;
                            if ($protocolShort) {
                                $results[] = [
                                    'Object' => $object,
                                    'cipher' => $val['cipher'],
                                    'custom_cipher' => $val['name_for_protocol'],
                                    'Specification' => $method['name'],
                                    'SpecificationTU' => !empty($condition['measured_properties_name']) ? $condition['measured_properties_name'] : '-',
                                    'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                                    'Normdoc' => !empty($condition['reg_doc']) ? $condition['reg_doc'] . (!empty($condition['year']) ? '-' . $condition['year'] : '') . (!empty($condition['clause']) ? ' ' . $condition['clause'] : '') : '-',
                                    'Normval' => $normval,
                                    'Normdocmet' => $method['view_gost_for_protocol'],
                                    'Normdoc_desc' => '',
                                    //                                    'SingleValues' => $measuringSheet['actual_value'] ? implode($eol, str_replace('.', ',', $measuringSheet['actual_value'])) : '-',
                                    'SingleValues' => $measuringSheet['actual_value'] ? implode('  <w:br/>  ', str_replace('.', ',', $measuringSheet['actual_value'])) : '-',
                                    'Value' => ($condition['type'] == "TU_sred"
                                        || $condition['type'] == "TU_sred3"
                                        || $condition['type'] == "TU_sred2"
                                        || $condition['type'] == "TU_sred4"
                                        || $condition['type'] == "TU_sred5"
                                    ) ?
                                        number_format($resultValue['average_value'], $measuringSheet['decimal_places'], ',', '') : "-",
                                    'Values' => $actualValue,
                                    'Result' => $matchValue,
                                    'id' => $k,
                                    'ugtp_desc' => $ugtpDesc,
                                    'ugtp_desc2' => $ugtpDesc2
                                ];
                            }
                            //                            $this->pre($results);
                        } else { // если не отмечен checkbox "Выводить единичные значения в протокол?", данные без единичных значений для вывода в таблицу результатов

                            $results[] = [
                                'Object' => $object,
                                'cipher' => $val['cipher'],
                                'custom_cipher' => $val['name_for_protocol'],
                                'Specification' => $method['name'],
                                'SpecificationTU' => !empty($condition['measured_properties_name']) ? $condition['measured_properties_name'] : '-',
                                'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                                'Normdoc' => !empty($condition['reg_doc']) ? $condition['reg_doc'] . (!empty($condition['year']) ? '-' . $condition['year'] : '') . (!empty($condition['clause']) ? ' ' . $condition['clause'] : '') : '-',
                                'Normval' => $normval,
                                'Normdocmet' => $method['view_gost_for_protocol'],
                                'Normdoc_desc' => '',
                                //'SingleValues' => '-',
                                'Values' => $actualValue,
                                'Value' => ($condition['type'] == "TU_sred" || $condition['type'] == "TU_sred3" || $condition['type'] == "TU_sred2" || $condition['type'] == "TU_sred4" || $condition['type'] == "TU_sred5") ? $resultValue['average_value'] : "-",
                                'Result' => $matchValue,
                                'id' => $k,
                                'ugtp_desc' => $ugtpDesc,
                                'ugtp_desc2' => $ugtpDesc2
                            ];
                        }
                    } elseif ($measuringSheet['type'] == 'frost') { // Морозостойкость
                        $sheet = $ResultModel->getMeasurement($method['measurement_id']);

                        $frost['name_table'] = '${table_frost}';
                        $eol = '<w:br/>';
                        $isIntermediate = isset($measuringSheet['ratio1']) && $measuringSheet['ratio1'] === '0';

                        //Убераем пустые поля, чтобы не выводить в протокол
                        $controlStrength1 = array_diff($measuringSheet['control_strength1'], ['']);
                        $controlStrength2 = array_diff($measuringSheet['control_strength2'], ['']);
                        $mainStrength1 = array_diff($measuringSheet['main_strength1'], ['']);
                        $mainStrength2 = array_diff($measuringSheet['main_strength2'], ['']);

                        $cycle = $isIntermediate ? $measuringSheet['cycle_intermediate'] : $measuringSheet['cycle_control'];
                        $normdocmet = $method['view_gost_for_protocol'] . $eol . $cycle . " циклов";
                        $ed = $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'];
                        $controlDamage = $isIntermediate ? $measuringSheet['control_damage1'] : $measuringSheet['control_damage2'];
                        $mainDamage = $isIntermediate ? $measuringSheet['main_damage1'] : $measuringSheet['main_damage2'];
                        $controlMass = $isIntermediate ? $measuringSheet['control_mass1'] : $measuringSheet['control_mass2'];
                        $mainMass = $isIntermediate ? $measuringSheet['main_mass1'] : $measuringSheet['main_mass2'];
                        //                        $controlStrength = $isIntermediate ? implode($eol, $measuringSheet['control_strength1']) :
                        //                            implode($eol, $measuringSheet['control_strength2']);
                        //                        $mainStrength = $isIntermediate ? implode($eol, $measuringSheet['main_strength1']) :
                        //                            implode($eol, $measuringSheet['main_strength2']);
                        $controlStrength = $isIntermediate ? implode($eol, $controlStrength1) :
                            implode($eol, $controlStrength2);
                        $mainStrength = $isIntermediate ? implode($eol, $mainStrength1) :
                            implode($eol, $mainStrength2);
                        $controlMedium = $isIntermediate ? $measuringSheet['control_medium1'] : $measuringSheet['control_medium2'];
                        $mainMedium = $isIntermediate ? $measuringSheet['main_medium1'] : $measuringSheet['main_medium2'];
                        $controlBottomLine = $isIntermediate ? $measuringSheet['control_bottom_line1'] : $measuringSheet['control_bottom_line2'];
                        $mainBottomLine = $isIntermediate ? $measuringSheet['main_bottom_line1'] : $measuringSheet['main_bottom_line2'];
                        $ratio = $isIntermediate ? $measuringSheet['ratio1'] : $measuringSheet['ratio2'];
                        $com = $measuringSheet['com'];

                        $markName = $sheet['initial_data'][$measuringSheet['mark']]['mark'] ?? '';

                        if ($ratio === '1') {
                            $tested = 'выдержали';
                            $match = 'соответствуют';
                            $ratioName = 'соблюдается';
                        } else {
                            $tested = 'не выдержали';
                            $match = 'не соответствуют';
                            $ratioName = 'не соблюдается';
                        }

                        $frost['Object'] = $object;
                        $frost['Normdocmet'] = $normdocmet;
                        $frost['Ed'] = $ed;
                        $frost['ControlDamage'] = $controlDamage;
                        $frost['MainDamage'] = $mainDamage;
                        $frost['ControlMass'] = $controlMass;
                        $frost['MainMass'] = $mainMass;
                        $frost['ControlStrength'] = $controlStrength;
                        $frost['MainStrength'] = $mainStrength;
                        $frost['ControlMedium'] = $controlMedium;
                        $frost['MainMedium'] = $mainMedium;
                        $frost['ControlBottomLine'] = $controlBottomLine;
                        $frost['MainBottomLine'] = $mainBottomLine;
                        $frost['Ratio'] = $ratioName;
                        $frost['Tested'] = $tested;
                        $frost['Match'] = $match;
                        $frost['Mark'] = $markName;
                        $frost['umtr_id'] = $k;
                        $frost['com'] = $com;
                    } elseif ($measuringSheet['type'] == 'deformation') { // Морозостойкость
                        $deformationValues = $measuringSheet['deformation_values'];
                        $materialInfo = $gostModel->getMaterialByUgtpId($measuringSheet['ugtp_id']);
                        $table = [
                            'id'              => $k,
                            'name_table'      => '${table_deformation}',
                            'Object'          => $object,
                            'Specification'   => $method['name'],
                            'SpecificationTU' => !empty($condition['measured_properties_name']) ? $condition['measured_properties_name'] : '-',
                            'Ed'              => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                            'Normdoc'         => !empty($condition['reg_doc'])
                                ? $condition['reg_doc']
                                . (!empty($condition['year']) ? '-' . $condition['year'] : '')
                                . (!empty($condition['clause']) ? ' ' . $condition['clause'] : '')
                                : '-',
                            'Normval'         => $normval,
                            'Normdocmet'      => $method['view_gost_for_protocol'],
                            'value'           => [],
                            'difference'      => [],
                            'com'             => $measuringSheet['com'] ?? '',
                            'cipher'             => $materialInfo['cipher'] ?? '',
                        ];

                        for ($i = 1; $i <= 11; $i++) {
                            $table['value'][] = $deformationValues[$i]['value'] ?? '';
                            $table['difference'][] = $deformationValues[$i]['difference'] ?? '';
                        }
                        $deformation[] = $table;
                    } elseif ($measuringSheet['type'] == 'asphalt') {
                        //$this->pre($measuringSheet);
                        //$tableZern = [];
                        $methodInfo = $methodModel->get($measuringSheet['method_id']);
                        $materialInfo = $gostModel->getMaterialByUgtpId($measuringSheet['ugtp_id']);
                        $table = [];
                        foreach ($measuringSheet['in_protocol'] as $key) {
                            $zern = $materialModel->getSieveAndNorm($measuringSheet['zern']);
                            $table = [
                                'id' => $k,
                                'name_table'      => '${table_zern}',
                                'type'            => 'asphalt',
                                'num_table'       => 2,
                                'fp'              => [],
                                'a'               => [],
                                'p'               => [],
                                'req'             => [],
                                'norm'            => [],
                                'title'           => [],
                                'name'            => $zern['NAME'],
                                'a_in_protocol'   => $measuringSheet['a_in_protocol'],
                                'p_in_protocol'   => $measuringSheet['p_in_protocol'],
                                'fp_in_protocol'   => $measuringSheet['fp_in_protocol'],
                                'recept_in_protocol' => $measuringSheet['recept_in_protocol'],
                                'r'                => [],
                                'req_in_protocol' => $measuringSheet['req_in_protocol'],
                                'initial_mass'    => $measuringSheet['initial_mass'],
                                'name_material'   => $materialInfo['NAME'],
                                'group_material'   => $materialInfo['group_name'],
                                'cipher'          => $materialInfo['cipher'],
                                'method'          => [
                                    'view' => $methodInfo['view_gost_for_protocol'],
                                    'name' => $methodInfo['mp_name'],
                                    'unit' => $methodInfo['unit_rus']
                                ],
                            ];
                            foreach ($measuringSheet['in_protocol'] as $key) {
                                $table['fp'][]     = str_replace('.', ',', $measuringSheet['fp'][$key]);
                                $table['a'][]     = str_replace('.', ',', $measuringSheet['a'][$key]);
                                $table['p'][]     = str_replace('.', ',', $measuringSheet['p'][$key]);
                                $table['req'][]     = str_replace('.', ',', $measuringSheet['norm'][$key]);
                                $table['r'][]     = str_replace('.', ',', $measuringSheet['r'][$key]);
                                $table['norm'][]  = $measuringSheet['norm'][$key];
                                $table['title'][] = $measuringSheet['title'][$key];
                            }
                            //                            $tableZern['name_table'] = '${table_zern}';
                            //                            $tableZern['type'] = 'asphalt';
                            //                            $tableZern['num_table'] = 2;
                            //                            $tableZern['fp'][] = str_replace('.', ',', $measuringSheet['fp'][$key]);
                            //                            $tableZern['p'][] = str_replace('.', ',', $measuringSheet['p'][$key]);
                            //                            $tableZern['a'][] = str_replace('.', ',', $measuringSheet['a'][$key]);
                            //                            $tableZern['req'][] = str_replace('.', ',', $measuringSheet['norm'][$key]);
                            //                            $tableZern['a_in_protocol'] = $measuringSheet['a_in_protocol'];
                            //                            $tableZern['p_in_protocol'] = $measuringSheet['p_in_protocol'];
                            //                            $tableZern['fp_in_protocol'] = $measuringSheet['fp_in_protocol'];
                            //                            $tableZern['recept_in_protocol'] = $measuringSheet['recept_in_protocol'];
                            //                            $tableZern['req_in_protocol'] = $measuringSheet['req_in_protocol'];
                            //                            $tableZern['r'][] = str_replace('.', ',', $measuringSheet['r'][$key]);
                            //                            $tableZern['norm'][] = $measuringSheet['norm'][$key];
                            //                            $tableZern['title'][] = $measuringSheet['title'][$key];
                            //                            $tableZern['name'] = $zern['NAME'];
                            //                            $tableZern['initial_mass'] = $measuringSheet['initial_mass'];
                            //                            $tableZern['name_material'] = $materialInfo['NAME'];
                            //                            $tableZern['group_material'] = $materialInfo['group_name'];
                            //                            $tableZern['cipher'] = $materialInfo['cipher'];
                            //                            $tableZern['method'] = [
                            //                                'view' => $methodInfo['view_gost_for_protocol'],
                            //                                'name' => $methodInfo['mp_name'],
                            //                                'unit' => $methodInfo['unit_rus']
                            //                            ];
                        }
                        $tableZern[] = $table;
                    } elseif (in_array($measuringSheet['type'], ['concrete_strength_17624_7', 'actual_class_18105_8_4_1'])) {
                        // Проверяем, выбрана ли расчетная документация
                        // (для фактического класса бетона может быть выбрана прочность бетона)
                        $hasDesignCalculation  = !empty($measuringSheet['design_calculation']);
                        if ($hasDesignCalculation) {
                            $measuringSheet = $requirementModel->getGostToProbe($measuringSheet['design_calculation'])['measuring_sheet'];
                        }

                        $twentyEight = 28;
                        $scheme = $measuringSheet['scheme'] ?? '';
                        $measurementId = $measuringSheet['measurement_id'] ?? '';
                        $eol = '<w:br/>';

                        if ($scheme === 'v') {
                            $measurement = $nkModel->getGraduation($measurementId);
                            //							$this->pre($measurement);
                            $dir = UPLOAD_DIR . "/plot/{$measurementId}";
                            $files = $requestModel->getFilesFromDir($dir);
                            $chartName = end($files);

                            $tableData['gradation'][$measurementId] = [
                                'table_name' => '${gradation_' . $measurementId . '}',
                                'plot_name' => '${plot_' . $measurementId . '}',
                                'Names' => implode('; ', array_filter($measurement['data']['name'])),
                                'Mean' => $this->formatNumber($measurement['data']['mean']),
                                'ShearStrength' => $this->formatNumber($measurement['data']['shear_strength']),
                                'GradationStrength' => $this->formatNumber($measurement['data']['gradation_strength']),
                                'Condition' => $this->formatNumber($measurement['data']['condition']),
                                'MeasuringDevice' => $measurement['data']['measuring_device'],
                                'Comment' => str_replace(PHP_EOL, $eol, $measurement['data']['comment']),
                                'Note' => $measurement['data']['note'],
                                'Description' => str_replace(PHP_EOL, $eol, $measurement['data']['description']),
                                'PlotPath' => $dir . '/' . $chartName,
                                //'name_plot' => '${chart_'.$measurementId.'}',
                                //'Chart' => $dir .'/'. $chartName
                            ];
                        } elseif ($scheme === 'g') {
                            $tableData['gradation'][$measurementId] = [
                                'table_name' => '${gradation_' . $measurementId . '}',
                                'plot_name' => '',
                                'NameForProtocol' => $measuringSheet['name_for_protocol'],
                                'DirectMethod' => $this->formatNumber($measuringSheet['direct_method']),
                                'IndirectMethod' => $this->formatNumber($measuringSheet['indirect_method']),
                                'RatioValue' => $this->formatNumber($measuringSheet['ratio_value']),
                                'Description' => str_replace(PHP_EOL, $eol, $measuringSheet['description']),
                                'PlotPath' => '',
                                //'name_plot' => '',
                                //'Chart' => ''
                            ];
                        }

                        if (!$hasDesignCalculation) {
                            $ugtpId = $measuringSheet['ugtp_id'];
                            // Обозначение "Отрыв со скалыванием"
                            $protocolInformation['Designation2'] = $measuringSheet['designation_2'] ?? '';
                            $protocolInformation['Decoding2'] = $measuringSheet['decoding_2'] ?? '';
                            // Обозначение "Косвенный метод"
                            $protocolInformation['Designation1'] = $measuringSheet['designation_1'];
                            $protocolInformation['Decoding1'] = $measuringSheet['decoding_1'];

                            // Схема участков
                            $tableData['drawing'][$ugtpId] = [
                                'table_name' => '${table_drawing_' . $ugtpId . '}',
                                'path' => UPLOAD_DIR . "/result/measurement/{$ugtpId}/{$measuringSheet['drawing']}",
                            ];


                            // Дата бетонирования*
                            $protocolInformation['сoncretingDate'] = date('d.m.Y', strtotime($measuringSheet['сoncreting_date']));
                            // Нормативный документ, устанавливающий требования к объекту испытаний
                            $protocolInformation['normDoc'] = $measuringSheet['cipher'] . '*.';
                            // Адрес места осуществления деятельности
                            $protocolInformation['address'] = $measuringSheet['address'];
                            $protocolInformation['address'] = str_replace('  ', ' ', $protocolInformation['address']);
                            // Условия проведения испытаний: температура воздуха
                            $protocolInformation['tIspit'] = $protocol['TEMP_O'] == $protocol['TEMP_TO_O'] ?
                                $this->formatTemperature(number_format($protocol['TEMP_O'], 1, ',', '')) . " " :
                                $this->formatTemperature(number_format($protocol['TEMP_O'], 1, ',', '')) . " - " . $this->formatTemperature(number_format($protocol['TEMP_TO_O'], 1, ',', ''));
                        }

                        // Нормативная(проектная) документация
                        if ($hasDesignCalculation) {
                            $normdoc = $measuringSheet['cipher'];
                        } else {
                            $normdoc = !empty($condition['reg_doc']) ? $condition['reg_doc'] . (!empty($condition['year']) ? '-' . $condition['year'] : '') : '-';
                        }

                        // Нормативное значение
                        if ($hasDesignCalculation) { // Если есть "Класс бетона", то выводим его иначе нормативное значение
                            $normval = 'B' . $measuringSheet['class'];
                        }

                        // Фактическое значение характеристики конструкции
                        $value = $hasDesignCalculation
                            ? 'B' . abs($measuringSheet['concrete_class'])
                            : $measuringSheet['result_value'];

                        // Соответствие
                        if ($hasDesignCalculation) {
                            $textMatch = (int)$measuringSheet['percent'] > (int)$measuringSheet['class'] ? 'Соответствует ' : 'Не нормируется';
                            $match = (int)$measuringSheet['day_to_test'] < $twentyEight ? "{$measuringSheet['percent']}%" : $textMatch;
                        } else {
                            $match = '-';
                        }

                        $tableData['nk'][$k]['name_table'] = '${table_nk_' . $k . '}';

                        if ($scheme === 'g') {
                            $singleValues = $hasDesignCalculation ? '-' :
                                implode($eol, $this->formatNumber($measuringSheet['adjusted_value']));
                        } else {
                            $singleValues = $hasDesignCalculation ? '-' :
                                implode($eol, $this->formatNumber($measuringSheet['single_values']));
                        }

                        $tableData['nk'][$k][] = [
                            'Object' => $val['name_for_protocol'],
                            'Date' => !empty($measuringSheet['сoncreting_date']) ? date('d.m.Y', strtotime($measuringSheet['сoncreting_date'])) : '',
                            'Specification' => $method['name'],
                            'SpecificationTU' => !empty($condition['measured_properties_name']) ? $condition['measured_properties_name'] : '-',
                            'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                            'Normdoc' => $normdoc,
                            'Normval' => $normval,
                            'Normdocmet' => $method['view_gost_for_protocol'],
                            'Normdoc_desc' => '',
                            'SingleValues' => $singleValues,
                            'Values' => $this->formatNumber($value),
                            'Result' => $match,
                            'Comment' => $measuringSheet['comment'],
                            'id' => $k,
                        ];

                        // Не выводим фактический класс бетона
                        //if ($hasDesignCalculation && $scheme === 'g') {
                        //    array_pop($tableData['nk'][$k]);
                        //}

                    } else {

                        $results[] = [
                            'Object' => $object,
                            'cipher' => $val['cipher'],
                            'custom_cipher' => $val['material_name'],
                            'Specification' => $method['name'],
                            'SpecificationTU' => !empty($condition['measured_properties_name']) ? $condition['measured_properties_name'] : '-',
                            'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                            'Normdoc' => !empty($condition['reg_doc']) ? $condition['reg_doc'] . (!empty($condition['year']) ? '-' . $condition['year'] : '') . (!empty($condition['clause']) ? ' ' . $condition['clause'] : '') : '-',
                            'Normval' => $normval,
                            'Normdocmet' => $method['view_gost_for_protocol'],
                            'Normdoc_desc' => '',
                            'Values' => is_numeric($actualValue) ? round(str_replace(',', '.', $actualValue), $method['decimal_places'], PHP_ROUND_HALF_UP) : $actualValue,
                            'Value' => ($condition['type'] == "TU_sred" || $condition['type'] == "TU_sred3" || $condition['type'] == "TU_sred2" || $condition['type'] == "TU_sred4" || $condition['type'] == "TU_sred5") ? $resultValue['average_value'] : "-",
                            'Result' => $matchValue,
                            'id' => $k,
                            'ugtp_desc' => $ugtpDesc,
                            'ugtp_desc2' => $ugtpDesc2
                        ];
                    }
                } else {

                    if ($actualValue || strval($actualValue) == "0") {
                        $count_val++;
                        if (!empty($val['name_for_protocol'])) {
                            $val['material_name'] = $val['name_for_protocol'];
                        }
                        $results[] = [
                            'Object' => $object,
                            'cipher' => $val['cipher'],
                            'custom_cipher' => $val['material_name'],
                            'Specification' => $method['name'],
                            'SpecificationTU' => !empty($condition['measured_properties_name']) ? $condition['measured_properties_name'] : '-',
                            'Ed' => $method['unit_fsa_id'] == 804 ? '-' : $method['unit_rus'],
                            'Normdoc' => !empty($condition['reg_doc']) ? $condition['reg_doc'] . (!empty($condition['year']) ? '-' . $condition['year'] : '') . (!empty($condition['clause']) ? ' ' . $condition['clause'] : '') : '-',
                            'Normval' => $normval,
                            'Normdocmet' => $method['view_gost_for_protocol'],
                            'method_id' => $method['id'],
                            'Normdoc_desc' => '',
                            'Values' => $actualValue,
                            'SingleValues2' => $singleValue2,
                            'Value' => ($condition['type'] == "TU_sred"
                                || $condition['type'] == "TU_sred3"
                                || $condition['type'] == "TU_sred2"
                                || $condition['type'] == "TU_sred4"
                                || $condition['type'] == "TU_sred5") ?
                                $resultValue['average_value'] : "-",
                            'Result' => $matchValue,
                            'id' => $k,
                            'ugtp_desc' => $ugtpDesc,
                            'ugtp_desc2' => $ugtpDesc2
                        ];
                    }
                }
            }

            if ($protocol['NO_COMPLIANCE']) {
                $results['no_compliance'] = 1;
            } else {
                $results['no_compliance'] = 0;
            }
            $results['count'][$k] = $count_val;
            $results['name_table'] = '${table_result}';

            $groupedByCipher = [];

            foreach ($results as $key => $item) {
                if (!is_array($item)) {
                    continue;
                }

                $cipher = $item['cipher'];
                if (!isset($groupedByCipher[$cipher])) {
                    $groupedByCipher[$cipher] = [];
                }

                $groupedByCipher[$cipher][] = $item;
            }

            $tableResults = array_values($groupedByCipher);
        }
        // die;
        //$this->pre($tableZern);
        if (!$protocolInformation['automatic_comments']) {
            if ($needBiggest && $needSmaller) {
                $protocolInformation['dopInfo'] = "- < - менее нижнего предела обнаружения.\n- > - более верхнего предела обнаружения.\n" . $protocolInformation['dopInfo'];
                $protocolInformation['dopInfo1'] = "- < - менее нижнего предела обнаружения.\n- > - более верхнего предела обнаружения.\n" . $protocolInformation['dopInfo1'];
            } else if ($needBiggest && !$needSmaller) {
                $protocolInformation['dopInfo'] = "- > - более верхнего предела обнаружения.\n" . $protocolInformation['dopInfo'];
                $protocolInformation['dopInfo1'] = "- > - более верхнего предела обнаружения.\n" . $protocolInformation['dopInfo1'];
            } else if (!$needBiggest && $needSmaller) {
                $protocolInformation['dopInfo'] = "- < - менее нижнего предела обнаружения.\n" . $protocolInformation['dopInfo'];
                $protocolInformation['dopInfo1'] = "- < - менее нижнего предела обнаружения.\n" . $protocolInformation['dopInfo1'];
            }
        }

        //        $this->pre($protocolInformation['dopInfo1']);

        $this->createProtocolDocument($protocolInformation, $verifyArr, $oborudData, $results, $tableData, $tableZern, $frost, $deformation, $createProtocolUserStr, $createNormProtocolUserStr, $tableResults);

        //Добавляем историю
        $date = date('Y-m-d H:i:s');
        $currentUser = $userModel->getCurrentUser();

        $history = [
            'DATE' => $date,
            'ASSIGNED' => $currentUser['NAME'] . ' ' . $currentUser['LAST_NAME'],
            'TZ_ID' => $dealInformation['ID'],
            'USER_ID' => $currentUser['ID'],
            'TYPE' => 'Формирование протокола',
            'REQUEST' => $dealInformation['REQUEST_TITLE'],
        ];

        $historyModel->addHistory($history);
    }

    /**
     * Генератор протокола
     * @param $protocolInformation
     * @param $varify
     * @param $oborud
     * @param $results
     * @param $tableData
     * @param $tableZern
     * @param $frost
     */
    public function createProtocolDocument($protocolInformation, $varify, $oborud, $results, $tableData = [], $tableZern = [], $frost = [], $deformation = [], $createProtocolUserStr, $createNormProtocolUserStr, $tableResults)
    {
        //$this->pre($tableResults);
        //        $this->pre($results, false);
        //        $this->pre($protocolInformation);
        $tableAllResult = [];
        $protocolShort = $protocolInformation['protocol_type'] == 34 || $protocolInformation['protocol_type'] == 33;

        $yearProtocol = substr($protocolInformation['protocolYear'], -2);
        $numDeal = str_replace('/', '.', $protocolInformation['dealNum']);
        $att = !empty($protocolInformation['Attestat']) ? ' С' : '';
        $nameProtocol = "ПИ {$protocolInformation['number']}.{$yearProtocol}{$att} {$numDeal}";

        if ($protocolInformation['protocol_type'] == 33) { // упрощенный
            // шаблон упрощенного протокола
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolShort.docx';
            unset($protocolInformation['work_position']);
            unset($protocolInformation['header']);

            $file = new Bitrix\Main\IO\File($template_doc);

            $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

            $document->normalizeContent();

            //            $document->setValues([
            //                'Verify' => new Bitrix\DocumentGenerator\DataProvider\ArrayDataProvider(
            //                    $protocolInformation['verify_arr'],
            //                    [
            //                        'ITEM_NAME' => 'Item',
            //                        'ITEM_PROVIDER' => Bitrix\DocumentGenerator\DataProvider\HashDataProvider::class,
            //                    ]
            //                ),
            //                'VerifyItemWorkPos' => 'Verify.Item.WorkPos',
            //                'VerifyItemIspolnitel' => 'Verify.Item.Ispolnitel',
            //                'VerifyItemIspolnitel2' => 'Verify.Item.Ispolnitel2',
            //                'VerifyItemIC' => 'Verify.Item.IC',
            //            ]);

            $result = $document->process();
            $content = $document->getContent();
        } elseif ($protocolInformation['protocol_type'] == 34) {
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolShort2.docx';

            unset($protocolInformation['work_position']);
            unset($protocolInformation['header']);
        } elseif ($protocolInformation['protocol_type'] == 1) { // стандартный с ЭЦП
            // шаблон протокола с эцп
            if ($protocolInformation['tz_id'] == 15514) {
                $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolStandartECP1.docx';
            } else {
                $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolStandartECP.docx';
            }

            unset($protocolInformation['work_position']);
            unset($protocolInformation['header']);

            //            $file = new Bitrix\Main\IO\File($template_doc);
            //
            //            $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
            //
            //            $document->normalizeContent();
            //
            //            $document->setValues([
            //                'Verify' => new Bitrix\DocumentGenerator\DataProvider\ArrayDataProvider(
            //                    $protocolInformation['verify_arr'],
            //                    [
            //                        'ITEM_NAME' => 'Item',
            //                        'ITEM_PROVIDER' => Bitrix\DocumentGenerator\DataProvider\HashDataProvider::class,
            //                    ]
            //                ),
            //                'VerifyItemWorkPos' => 'Verify.Item.WorkPos',
            //                'VerifyItemIspolnitel' => 'Verify.Item.Ispolnitel',
            //                'VerifyItemIspolnitel2' => 'Verify.Item.Ispolnitel2',
            //                'VerifyItemIC' => 'Verify.Item.IC',
            //            ]);
            //
            //            $result = $document->process();
            //            $content = $document->getContent();
            //временно
        } elseif ($protocolInformation['protocol_type'] == 2) {
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolStandart1.docx';
        } elseif ($protocolInformation['protocol_type'] == 3) {
            // шаблон протокола НК схема "B"
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolStandartNk2.docx';
        } elseif ($protocolInformation['protocol_type'] == 4) {
            // шаблон протокола НК "B" без схемы участков
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolStandartNk4.docx';
        } elseif ($protocolInformation['protocol_type'] == 5) {
            // шаблон протокола НК схема "Г"
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolStandartNk5.docx';
        } elseif ($protocolInformation['protocol_type'] == 44) {
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolStandartZak.docx';
        } elseif ($protocolInformation['protocol_type'] == 55) {
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolOSK1.docx';
        } elseif ($protocolInformation['protocol_type'] == 56) {
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolOSK2.docx';
        } elseif ($protocolInformation['protocol_type'] == 57) {
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolECPOSK1.docx';
        } elseif ($protocolInformation['protocol_type'] == 58) {
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolECPOSK2.docx';
        } elseif ($protocolInformation['protocol_type'] == 60) {
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolStandartNk2ECP.docx';
        } elseif ($protocolInformation['protocol_type'] == 62) {
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/grb_ilac.docx';
        } else {
            // стандартный шаблон
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolStandart1.docx';
        }

        if ($protocolInformation['id'] == 17313) {
            $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/ProtocolShortTest.docx';
        }

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $template = new \PhpOffice\PhpWord\TemplateProcessor($template_doc);

        $newpath = "archive/{$protocolInformation['tz_id']}{$protocolInformation['protocolYear']}/{$protocolInformation['id']}/{$protocolInformation['curdate']}.docx";
        $newpath2 = "archive/{$protocolInformation['tz_id']}{$protocolInformation['protocolYear']}/{$protocolInformation['id']}/forsign.docx";
        $interimPath = "interim_archive_prot/{$protocolInformation['tz_id']}{$protocolInformation['protocolYear']}/{$protocolInformation['id']}/{$protocolInformation['curdate']}.docx";
        $outputFile = "p №" . $protocolInformation['number'] . " от " . date("d.m.Y", strtotime($protocolInformation['dProtocol'])) . ".pdf";
        $outputPath = "archive/{$protocolInformation['tz_id']}{$protocolInformation['protocolYear']}/{$protocolInformation['id']}/";

        $fullPathDocx = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $interimPath;
        $fullPathPDF = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $outputPath;

        $newDirectory = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $outputPath;

        if (!is_dir($newDirectory)) {
            mkdir($newDirectory, 0777, true);
        }

        //TODO: Посмотреть по формату протокола
        $pathDocCurdate = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $newpath;
        $forSign = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $newpath2;

        //Таблица оборудования
        foreach ($oborud as $ob) {
            $strCertOborud = [];
            if (!empty($ob['certificates'])) {
                foreach ($ob['certificates'] as $cert) {
                    $strCertOborud[] = $cert['cert_name'] . ' до ' . date("d.m.Y", strtotime($cert['date_end']));
                }
            } else {
                foreach ($ob['actual_certificate'] as $cert) {
                    $strCertOborud[] = $cert['name'] . ' до ' . date("d.m.Y", strtotime($cert['date_end']));
                }
            }


            $table_ob[] = [
                'Name' => $ob['OBJECT'] . ' ' . $ob['TYPE_OBORUD'] . ', ' . $ob['FACTORY_NUMBER'] . ', '
                    . $ob['REG_NUM'],

                'Poverka' => implode(', ', $strCertOborud),
            ];
        }

        //        $this->pre($table_ob);

        $styleTable = array('borderSize' => 3, 'cellMarginLeft' => 0, 'cellMarginRight' => 0, 'borderColor' => '000000', 'leftFromText' => 0, 'rightFromText' => 0, 'bottomFromText' => 0);
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowBoth = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan2 = array('gridSpan' => 2, 'valign' => 'center');
        $cellColSpan3 = array('gridSpan' => 3, 'valign' => 'center');
        $cellColSpan4 = array('gridSpan' => 4, 'valign' => 'center');
        $cellColSpan6 = array('gridSpan' => 6, 'valign' => 'center');
        $cellColSpan5 = array('gridSpan' => 5, 'valign' => 'center');
        $cellColSpan8 = array('gridSpan' => 8, 'valign' => 'center');
        $cellColSpan9 = array('gridSpan' => 9, 'valign' => 'center');
        $cellColSpan10 = array('gridSpan' => 10, 'valign' => 'center');
        $cellColBothSpan10 = array('gridSpan' => 10, 'align' => 'both', 'valign' => 'center');
        $FontStyle = [
            'size' => 10,
            'name' => 'Times New Roman'
        ];
        $FontStyleTitle = [
            'size' => 10,
            'name' => 'Times New Roman',
            'bold' => true
        ];
        $paragraphStyle = [
            'spaceAfter' => 0,
            'spaceBefore' => 0,
            'space' => ['after' => 0],
            'spacing' => 10,
            'lineHeight' => 1,
            'align' => 'center',
            'indentation' => ['left' => 0, 'right' => 0]
        ];
        $paragraphStyleTableInfo = [
            'spaceAfter' => 0,
            'spaceBefore' => 0,
            'space' => ['after' => 0],
            'spacing' => 10,
            'lineHeight' => 1,
            'indentation' => ['left' => 0, 'right' => 0]
        ];
        $paragraphStyle1 = [
            'spaceAfter' => 0,
            'spaceBefore' => 0,
            'space' => ['after' => 0],
            'spacing' => 10,
            'lineHeight' => 1,
            'indentation' => ['left' => 0, 'right' => 0]
        ];
        $paragraphStyleText = [
            'indentation' => ['left' => 700]
        ];
        $paragraphStyleText1 = [
            'indentation' => ['left' => 825]
        ];

        $cellHCentered = array('align' => 'center');
        $cellAllCentered = array('align' => 'center', 'valign' => 'center');
        $cellBothCentered = array('align' => 'both', 'valign' => 'center');
        $cellVCentered = array('valign' => 'center');

        //Таблица оборудования
        $section = $phpWord->addSection();
        $table_oborud = $section->addTable($styleTable);
        $table_oborud->addRow(null, array('tblHeader' => true));
        $table_oborud->addCell(self::percentToTwips(40))->addText('Наименование, тип (марка), заводской и инвентарный №', array('size' => 10), $cellHCentered);
        $table_oborud->addCell(self::percentToTwips(59))->addText('Сведения о поверке/калибровке/аттестации', array('size' => 10), $cellHCentered);
        foreach ($table_ob as $itemMater) {
            $table_oborud->addRow();
            $table_oborud->addCell()->addText($itemMater['Name'], array('size' => 10), $cellHCentered);
            $table_oborud->addCell(RowSpan)->addText($itemMater['Poverka'], array('size' => 10), $cellHCentered);
        }

        //Зерновой состав общий
        if (!empty($tableZern)) {
            $tableAllZern = [];

            foreach ($tableZern as $k => $tableData) {
                if ($tableData['type'] === 'asphalt') {
                    //$this->pre(1);
                    $countTitle = count($tableData['title']);

                    $section = $phpWord->addSection();
                    $table_zern[$k] = $section->addTable(array('borderSize' => 3, 'borderColor' => '000000'));

                    $table_zern[$k]->addRow(null, array('tblHeader' => true));
                    if (!$protocolShort) {
                        $table_zern[$k]->addCell($countTitle <= 6 ? 2750 : 3200, ['vMerge' => 'restart'])->addText('Объект исследований и испытаний (шифр проб/образцов в ИЦ)', $FontStyle, $paragraphStyle);
                    }
                    $table_zern[$k]->addCell($countTitle <= 6 ? 2300 : 1900, ['vMerge' => 'restart'])->addText('Определяемая характеристика (показатель)', $FontStyle, $paragraphStyle);
                    $table_zern[$k]->addCell(700, ['vMerge' => 'restart'])->addText('Ед. изм.', $FontStyle, $paragraphStyle);
                    $table_zern[$k]->addCell($countTitle <= 6 ? 2100 : 2300, ['vMerge' => 'restart'])->addText('Нормативный документ на метод испытания (раздел, пункт)', $FontStyle, $paragraphStyle);

                    $table_zern[$k]->addCell(($countTitle <= 6 ? 5350 : 6500) / $countTitle, ['gridSpan' => $countTitle, 'valign' => 'center'])->addText('Размер ячейки сита, мм', $FontStyle, $paragraphStyle);

                    $table_zern[$k]->addRow(null, array('tblHeader' => true));

                    if (!$protocolShort) {
                        $table_zern[$k]->addCell($countTitle <= 6 ? 2750 : 2600, $cellRowContinue);
                    }
                    $table_zern[$k]->addCell($countTitle <= 6 ? 2100 : 1800, $cellRowContinue);
                    $table_zern[$k]->addCell(1100, $cellRowContinue);
                    $table_zern[$k]->addCell($countTitle <= 6 ? 2100 : 2000, $cellRowContinue);

                    foreach ($tableData['title'] as $title) {
                        $table_zern[$k]->addCell(($countTitle <= 6 ? 5350 : 6900) / $countTitle)->addText(str_replace(".", ",", $title), $FontStyle, $paragraphStyle);
                    }
                    if ($tableData['a_in_protocol'] == 1) {
                        $table_zern[$k]->addRow(['exactHeight' => 500]);

                        if (!$protocolShort) {
                            $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['name_material'] . ' ' . $tableData['group_material'] . ' (' . $tableData['cipher'] . ')', $FontStyle, $paragraphStyle);
                        }
                        $table_zern[$k]->addCell(null, $cellRowSpan)->addText('Частный остаток на каждом сите', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['method']['unit'], $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['method']['view'], $FontStyle, $paragraphStyle);

                        foreach ($tableData['a'] as $key_a => $fp) {
                            $table_zern[$k]->addCell(null, $cellAllCentered)->addText(str_replace(".", ",", $fp), $FontStyle, $paragraphStyle);
                        }
                    }

                    if ($tableData['p_in_protocol'] == 1) {
                        $table_zern[$k]->addRow(['exactHeight' => 500]);
                        if (!$protocolShort) {
                            if ($tableData['a_in_protocol'] == 1) {
                                $table_zern[$k]->addCell(null, $cellRowContinue);
                            } else {
                                $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['name_material'] . ' (' . $tableData['cipher'] . ')', $FontStyle, $paragraphStyle);
                            }
                        }

                        $table_zern[$k]->addCell(null, $cellRowSpan)->addText('Полный остаток на каждом сите', $FontStyle, $paragraphStyle);

                        if ($tableData['a_in_protocol'] == 1) {
                            $table_zern[$k]->addCell(null, $cellRowContinue);
                        } else {
                            $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['method']['unit'], $FontStyle, $paragraphStyle);
                        }

                        if ($tableData['a_in_protocol'] == 1) {
                            $table_zern[$k]->addCell(null, $cellRowContinue);
                        } else {
                            $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['method']['view'], $FontStyle, $paragraphStyle);
                        }

                        foreach ($tableData['p'] as $key_a => $fp) {
                            $table_zern[$k]->addCell(null, $cellAllCentered)->addText(str_replace(".", ",", $fp), $FontStyle, $paragraphStyle);
                        }
                    }

                    if ($tableData['fp_in_protocol'] == 1) {
                        $table_zern[$k]->addRow(['exactHeight' => 500]);

                        if (!$protocolShort) {
                            if ($tableData['p_in_protocol'] == 1 || $tableData['a_in_protocol'] == 1) {
                                $table_zern[$k]->addCell(null, $cellRowContinue);
                            } else {
                                $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['name_material'] . ' ' . $tableData['group_material'] . ' (' . $tableData['cipher'] . ')', $FontStyle, $paragraphStyle);
                            }
                        }
                        $table_zern[$k]->addCell(null, $cellRowSpan)->addText('Полный проход через сито', $FontStyle, $paragraphStyle);

                        if ($tableData['p_in_protocol'] == 1 || $tableData['a_in_protocol'] == 1) {
                            $table_zern[$k]->addCell(null, $cellRowContinue);
                        } else {
                            $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['method']['unit'], $FontStyle, $paragraphStyle);
                        }

                        if ($tableData['p_in_protocol'] == 1 || $tableData['a_in_protocol'] == 1) {
                            $table_zern[$k]->addCell(null, $cellRowContinue);
                        } else {
                            $table_zern[$k]->addCell(null, $cellAllCentered)->addText($tableData['method']['view'], $FontStyle, $paragraphStyle);
                        }

                        foreach ($tableData['fp'] as $key_a => $fp) {
                            $table_zern[$k]->addCell(null, $cellAllCentered)->addText(str_replace(".", ",", $fp), $FontStyle, $paragraphStyle);
                        }
                    }
                    if ($tableData['recept_in_protocol'] == 1) {
                        $table_zern[$k]->addRow(['exactHeight' => 500]);

                        $table_zern[$k]->addCell(null, $cellRowContinue);
                        $table_zern[$k]->addCell(null, $cellRowContinue);
                        $table_zern[$k]->addCell(null, $cellRowContinue);
                        $table_zern[$k]->addCell(null, $cellAllCentered)->addText('Требования рецепта', $FontStyle, $paragraphStyle);

                        foreach ($tableData['r'] as $key_p => $r) {
                            $table_zern[$k]->addCell(null, $cellAllCentered)->addText(!empty($r) ? str_replace(".", ",", $r) : '-', $FontStyle, $paragraphStyle);
                        }
                    }

                    if ($tableData['req_in_protocol'] == 1) {
                        $table_zern[$k]->addRow(['exactHeight' => 500]);
                        if ($tableData['method']['view'] === 'ГОСТ 33029-2014') {
                            $tableData['method']['view'] = 'ГОСТ 32703-2014';
                        }
                        if ($tableData['recept_in_protocol'] == 1) {
                            if (str_contains($tableData['name'], 'марка')) {
                                $currentMark = explode(' марка ', $tableData['name'])[1];

                                $table_zern[$k]->addCell(null, $protocolShort ? $cellColSpan3 : $cellColSpan4)->addText("Требования {$tableData['method']['view']} к проходам через сито (марка {$currentMark})", $FontStyle, $paragraphStyle);
                            } else if (str_contains($tableData['name'], 'марки')) {
                                $currentMark = explode(' марки ', $tableData['name'])[1];

                                $table_zern[$k]->addCell(null, $protocolShort ? $cellColSpan3 : $cellColSpan4)->addText("Требования {$tableData['method']['view']} к проходам через сито (марка {$currentMark})", $FontStyle, $paragraphStyle);
                            } else {
                                $table_zern[$k]->addCell(null, $protocolShort ? $cellColSpan3 : $cellColSpan4)->addText("Предельно допустимые отклонения от утвержденного рецепта {$tableData['name']}", $FontStyle, $paragraphStyle);
                            }
                        } else {
                            if (str_contains($tableData['name'], 'марка')) {
                                $currentMark = explode(' марка ', $tableData['name'])[1];

                                $table_zern[$k]->addCell(null, $protocolShort ? $cellColSpan3 : $cellColSpan4)->addText("Требования {$tableData['method']['view']} к проходам через сито (марка {$currentMark})", $FontStyle, $paragraphStyle);
                            } else if (str_contains($tableData['name'], 'марки')) {
                                $currentMark = explode(' марки ', $tableData['name'])[1];

                                $table_zern[$k]->addCell(null, $protocolShort ? $cellColSpan3 : $cellColSpan4)->addText("Требования {$tableData['method']['view']} к проходам через сито (марка {$currentMark})", $FontStyle, $paragraphStyle);
                            } else {
                                $table_zern[$k]->addCell(null, $protocolShort ? $cellColSpan3 : $cellColSpan4)->addText("Предельно допустимые отклонения от утвержденного рецепта {$tableData['name']}", $FontStyle, $paragraphStyle);
                            }
                        }

                        foreach ($tableData['norm'] as $key_norm => $norm) {
                            $table_zern[$k]->addCell(null, $cellAllCentered)->addText(str_replace(".", ",", $norm), $FontStyle, $paragraphStyle);
                        }
                    }
                } else {
                    //                $tableAllZern = [];
                    //                foreach ($tableZern as $k => $tableData) {
                    $countTitle = count($tableData['title']);
                    $section = $phpWord->addSection();
                    $table_zern[$k] = $section->addTable(array('borderSize' => 3, 'borderColor' => '000000'));
                    if (!$protocolShort) {
                        $table_zern[$k]->addRow(null, array('tblHeader' => true));
                        $cell = $table_zern[$k]->addCell(
                            self::percentToTwips(15, true),
                            ['vMerge' => 'restart', 'valign' => 'center']
                        );

                        $pCenter = [
                            'align' => 'center',
                            'alignment' => 'center',
                            'spaceBefore' => 0,
                            'spaceAfter' => 0,
                        ];
                        $cell->addText("\u{200B}", ['size' => 1], $pCenter);
                        $cell->addText('Объект исследований', ['size' => 10], $pCenter);
                        $cell->addText('и испытаний', ['size' => 10], $pCenter);
                        $cell->addText('(шифр проб/образцов в ИЦ)', ['size' => 10], $pCenter);
                        //$table_zern[$k]->addCell(self::percentToTwips(15, true), ['vMerge' => 'restart'])->addText('Объект исследований и испытаний (шифр проб/образцов в ИЦ)', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(self::percentToTwips(10, true), ['vMerge' => 'restart'])->addText('Определяемая характеристика (показатель)', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(self::percentToTwips(5, true), ['vMerge' => 'restart'])->addText('Ед. изм.', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(self::percentToTwips(10, true), ['vMerge' => 'restart'])->addText('Нормативный документ на метод испытания (раздел, пункт)', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(self::percentToTwips(60, true), ['gridSpan' => $countTitle, 'valign' => 'center'])->addText('Размер ячейки сита, мм', $FontStyle, $paragraphStyle);

                        $table_zern[$k]->addRow(null, array('tblHeader' => true));
                        $table_zern[$k]->addCell(self::percentToTwips(15, true), $cellRowContinue);
                        $table_zern[$k]->addCell(self::percentToTwips(10, true), $cellRowContinue);
                        $table_zern[$k]->addCell(self::percentToTwips(5, true), $cellRowContinue);
                        $table_zern[$k]->addCell(self::percentToTwips(10, true), $cellRowContinue);

                        foreach ($tableData['title'] as $title) {
                            $table_zern[$k]->addCell(self::percentToTwips(60 / $countTitle, true))->addText(str_replace(".", ",", $title), $FontStyle, $paragraphStyle);
                        }

                        $table_zern[$k]->addRow(['exactHeight' => 500]);
                        $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['name_material'], $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(null, $cellAllCentered)->addText('Частный остаток на каждом сите', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['method']['unit'], $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['method']['view'], $FontStyle, $paragraphStyle);

                        foreach ($tableData['a'] as $key_a => $a) {
                            $table_zern[$k]->addCell(null, $cellAllCentered)->addText(str_replace(".", ",", $a), $FontStyle, $paragraphStyle);
                        }

                        $table_zern[$k]->addRow(['exactHeight' => 500]);
                        $table_zern[$k]->addCell(null, $cellRowContinue);
                        $table_zern[$k]->addCell(null, $cellAllCentered)->addText('Полный остаток на каждом сите', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(null, $cellRowContinue);
                        $table_zern[$k]->addCell(null, $cellRowContinue);

                        foreach ($tableData['p'] as $key_p => $p) {
                            $table_zern[$k]->addCell(null, $cellAllCentered)->addText(str_replace(".", ",", $p), $FontStyle, $paragraphStyle);
                        }

                        if (isset($tableData['req_in_protocol']) && $tableData['req_in_protocol'] == 1) {
                            $table_zern[$k]->addRow(['exactHeight' => 500]);
                            $table_zern[$k]->addCell(null, $cellColSpan4)->addText("Требования {$tableData['name']}", $FontStyle, $paragraphStyle);

                            foreach ($tableData['norm'] as $key_norm => $norm) {
                                $table_zern[$k]->addCell(null, $cellAllCentered)->addText(str_replace(".", ",", $norm), $FontStyle, $paragraphStyle);
                            }
                        }
                    } else {
                        $table_zern[$k]->addRow(null, array('tblHeader' => true));
                        $table_zern[$k]->addCell(1500, ['vMerge' => 'restart'])->addText('Определяемая характеристика (показатель)', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(1500, ['vMerge' => 'restart'])->addText('Ед. изм.', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(1500, ['vMerge' => 'restart'])->addText('Нормативный документ на метод испытания (раздел, пункт)', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(6100, ['gridSpan' => $countTitle, 'valign' => 'center'])->addText('Размер ячейки сита, мм', $FontStyle, $paragraphStyle);

                        $table_zern[$k]->addRow(null, array('tblHeader' => true));
                        $table_zern[$k]->addCell(1500, $cellRowContinue);
                        $table_zern[$k]->addCell(1500, $cellRowContinue);
                        $table_zern[$k]->addCell(1500, $cellRowContinue);

                        foreach ($tableData['title'] as $title) {
                            $table_zern[$k]->addCell(6100 / $countTitle)->addText(str_replace(".", ",", $title), $FontStyle, $paragraphStyle);
                        }

                        $table_zern[$k]->addRow(['exactHeight' => 500]);
                        $table_zern[$k]->addCell(null, $cellAllCentered)->addText('Частный остаток на каждом сите', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['method']['unit'], $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(null, $cellRowSpan)->addText($tableData['method']['view'], $FontStyle, $paragraphStyle);

                        foreach ($tableData['a'] as $key_a => $a) {
                            $table_zern[$k]->addCell(null, $cellAllCentered)->addText(str_replace(".", ",", $a), $FontStyle, $paragraphStyle);
                        }

                        $table_zern[$k]->addRow(['exactHeight' => 500]);
                        $table_zern[$k]->addCell(null, $cellAllCentered)->addText('Полный остаток на каждом сите', $FontStyle, $paragraphStyle);
                        $table_zern[$k]->addCell(null, $cellRowContinue);
                        $table_zern[$k]->addCell(null, $cellRowContinue);

                        foreach ($tableData['p'] as $key_p => $p) {
                            $table_zern[$k]->addCell(null, $cellAllCentered)->addText(str_replace(".", ",", $p), $FontStyle, $paragraphStyle);
                        }

                        if (isset($tableData['req_in_protocol']) && $tableData['req_in_protocol'] == 1) {
                            $table_zern[$k]->addRow(['exactHeight' => 500]);
                            $table_zern[$k]->addCell(null, $cellColSpan3)->addText("Требования {$tableData['name']}", $FontStyle, $paragraphStyle);

                            foreach ($tableData['norm'] as $key_norm => $norm) {
                                $table_zern[$k]->addCell(null, $cellAllCentered)->addText(str_replace(".", ",", $norm), $FontStyle, $paragraphStyle);
                            }
                        }
                    }
                }
                foreach ($tableData as $key => $tableRes) {
                    if ($key === 'name_table') {
                        $tableAllZern[$k] = [
                            $key => $tableRes,
                            'cipher' => $tableData['cipher'] ?? null,
                        ];
                    }
                }
                $tableAllResult[] = $tableData;
            }
        }

        // Морозостойкость
        if (!empty($frost)) {
            $section = $phpWord->addSection();
            $table_frost = $section->addTable($styleTable);
            $table_frost->addRow(null, array('tblHeader' => true));
            $table_frost->addCell(self::percentToTwips(15, true), $cellRowSpan)->addText('Объект исследований и испытаний (шифр проб/образцов в ИЦ)', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(self::percentToTwips(15, true), $cellRowSpan)->addText('Метод испытаний, число циклов замораживания и оттаивания', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(self::percentToTwips(15, true), $cellRowSpan)->addText('Определяемые характеристики контрольных образцов', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(self::percentToTwips(5, true), $cellRowSpan)->addText('Ед. изм.', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(self::percentToTwips(15, true), $cellRowSpan)->addText('Результаты испытаний контрольных образцов', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(self::percentToTwips(15, true), $cellRowSpan)->addText('Определяемые характеристики основных образцов', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(self::percentToTwips(5, true), $cellRowSpan)->addText('Ед. изм.', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(self::percentToTwips(15, true), $cellRowSpan)->addText('Результаты испытаний основных образцов', array('size' => 10), $cellAllCentered);

            $table_frost->addRow();
            $table_frost->addCell(null, $cellRowSpan)->addText($frost['Object'], array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellRowSpan)->addText($frost['Normdocmet'], array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('Наличие трещин, сколов, шелушения', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('-', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['ControlDamage']), array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('Наличие трещин, сколов, шелушения', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('-', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['MainDamage']), array('size' => 10), $cellAllCentered);

            $table_frost->addRow();
            $table_frost->addCell(null, $cellRowContinue);
            $table_frost->addCell(null, $cellRowContinue);
            $table_frost->addCell(null, $cellAllCentered)->addText('Среднее уменьшение массы образцов', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('%', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['ControlMass']), array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('Среднее уменьшение массы образцов', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('%', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['MainMass']), array('size' => 10), $cellAllCentered);
            $frost['ControlStrength'] = str_replace('.', ',', $frost['ControlStrength']);
            $frost['MainStrength'] = str_replace('.', ',', $frost['MainStrength']);
            $table_frost->addRow();
            $table_frost->addCell(null, $cellRowContinue);
            $table_frost->addCell(null, $cellRowContinue);
            $table_frost->addCell(null, $cellAllCentered)->addText('Прочность при сжатии насыщенных образцов', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('МПа', array('size' => 10), $cellAllCentered);
            //$table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['ControlStrength']), array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText(trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>", "<w:br/>"], "\n<w:br/>", $frost['ControlStrength'])), array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('Прочность при сжатии образцов после испытания', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('МПа', array('size' => 10), $cellAllCentered);
            //$table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['MainStrength']), array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText(trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>", "<w:br/>"], "\n<w:br/>", $frost['MainStrength'])), array('size' => 10), $cellAllCentered);

            $table_frost->addRow();
            $table_frost->addCell(null, $cellRowContinue);
            $table_frost->addCell(null, $cellRowContinue);
            $table_frost->addCell(null, $cellAllCentered)->addText('Средняя прочность при сжатии насыщенных образцов', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('МПа', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['ControlMedium']), array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('Средняя прочность при сжатии образцов после испытания', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('МПа', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['MainMedium']), array('size' => 10), $cellAllCentered);

            $table_frost->addRow();
            $table_frost->addCell(null, $cellRowContinue);
            $table_frost->addCell(null, $cellRowContinue);
            $table_frost->addCell(null, $cellAllCentered)->addText('Нижняя граница доверительного интервала XminI', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('МПа', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['ControlBottomLine']), array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('Нижняя граница доверительного интервала XminII', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText('МПа', array('size' => 10), $cellAllCentered);
            $table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['MainBottomLine']), array('size' => 10), $cellAllCentered);

            $table_frost->addRow();
            if ($frost['com']) {
                $table_frost->addCell(null, array('gridSpan' => 8, 'valign' => 'center'))->addText("Примечания: {$frost['com']} <w:br/> Образцы не выдержали испытание на морозостойкость и не соответствуют марке по морозостойкости {$frost['Mark']}", array('size' => 10), $cellAllCentered);
            } else {
                $table_frost->addCell(null, array('gridSpan' => 8, 'valign' => 'center'))->addText("Примечания: Соотношение XminII ≥ 0,9 XminI {$frost['Ratio']} <w:br/> Образцы {$frost['Tested']} испытание на морозостойкость и {$frost['Match']} марке по морозостойкости {$frost['Mark']}", array('size' => 10), $cellAllCentered);
            }

            $tableAllResult[] = $frost;
        }
        if (!empty($deformation)) {
            $tableAllZern = [];

            foreach ($deformation as $k => $tableData) {
                $section = $phpWord->addSection();
                $table_deformation[$k] = $section->addTable($styleTable);
                $table_deformation[$k]->addRow(null, array('tblHeader' => true));
                if (!$protocolShort) {
                    $table_deformation[$k]->addCell(null, $cellRowSpan)->addText('Объект исследований и испытаний (шифр проб/образцов в ИЦ)', $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                }
                $table_deformation[$k]->addCell(null, $cellRowSpan)->addText('Определяемая характеристика (показатель)', $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null, $cellRowSpan)->addText('Ед. изм.', $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null, $cellColSpan3)->addText('Требования нормативных документов', $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null, $cellRowSpan)->addText('Нормативный документ на метод испытания (раздел, пункт)', $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null, $cellRowSpan)->addText('№ измерения', $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null, $cellRowSpan)->addText('Относительная деформация каждого образца', $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null, $cellRowSpan)->addText('Отличие 11-го результата испытаний от 3-х предшествующих', $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);

                $table_deformation[$k]->addRow(null, array('tblHeader' => true));
                if (!$protocolShort) {
                    $table_deformation[$k]->addCell(null, $cellRowContinue);
                }
                $table_deformation[$k]->addCell(null, $cellRowContinue);
                $table_deformation[$k]->addCell(null, $cellRowContinue);
                $table_deformation[$k]->addCell(null)->addText('нормативная (проектная) документация (раздел, пункт)', $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null)->addText('наименование характеристики (показателя)', $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null)->addText('нормативное значение характеристики (показателя)', $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null, $cellRowContinue);
                $table_deformation[$k]->addCell(null, $cellRowContinue);
                $table_deformation[$k]->addCell(null, $cellRowContinue);
                $table_deformation[$k]->addCell(null, $cellRowContinue);

                $table_deformation[$k]->addRow(null, array('tblHeader' => true));
                $table_deformation[$k]->addCell(null)->addText(1, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null)->addText(2, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null)->addText(3, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null)->addText(4, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null)->addText(5, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null)->addText(6, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null)->addText(7, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null)->addText(8, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                $table_deformation[$k]->addCell(null)->addText(9, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                if (!$protocolShort) {
                    $table_deformation[$k]->addCell(null)->addText(10, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                }

                $values = $tableData['value'] ?? [];
                $diffs = $tableData['difference'] ?? [];
                for ($i = 0; $i < 11; $i++) {
                    $table_deformation[$k]->addRow();

                    if ($i === 0) {
                        if (!$protocolShort) {
                            $table_deformation[$k]->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText($tableData['Object'], $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                        }
                        $table_deformation[$k]->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText($tableData['Specification'], $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                        $table_deformation[$k]->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText($tableData['Ed'], $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                        $table_deformation[$k]->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText($tableData['Normdoc'], $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                        $table_deformation[$k]->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText($tableData['SpecificationTU'], $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                        $table_deformation[$k]->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText($tableData['Normval'], $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                        $table_deformation[$k]->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText($tableData['Normdocmet'], $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                    } else {
                        if (!$protocolShort) {
                            $table_deformation[$k]->addCell(null, $cellRowContinue);
                        }
                        $table_deformation[$k]->addCell(null, $cellRowContinue);
                        $table_deformation[$k]->addCell(null, $cellRowContinue);
                        $table_deformation[$k]->addCell(null, $cellRowContinue);
                        $table_deformation[$k]->addCell(null, $cellRowContinue);
                        $table_deformation[$k]->addCell(null, $cellRowContinue);
                        $table_deformation[$k]->addCell(null, $cellRowContinue);
                    }
                    $table_deformation[$k]->addCell(null, $cellAllCentered)->addText(($i + 1), $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                    $val = $values[$i] ?? '-';
                    if (is_numeric($val)) $val = str_replace('.', ',', $val);
                    $table_deformation[$k]->addCell(null, $cellAllCentered)->addText($val, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                    $diff = $diffs[$i] ?? '-';
                    $table_deformation[$k]->addCell(null, $cellAllCentered)->addText($diff, $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                }
                if (!empty($tableData['com'])) {
                    $table_deformation[$k]->addRow();
                    $table_deformation[$k]->addCell(null, ['gridSpan' => $protocolShort ? 9 : 10])
                        ->addText($tableData['com'], $FontStyle, $protocolShort ? $paragraphStyle : $cellAllCentered);
                }
                foreach ($tableData as $key => $tableRes) {
                    if ($key === 'name_table') {
                        $tableAllZern[$k] = [
                            $key => $tableRes,
                            'cipher' => $tableData['cipher'] ?? null,
                        ];
                    }
                }
                $tableAllResult[] = $tableData;
            }
        }
        // НК
        if (!empty($tableData['nk'])) {
            $table_nk = [];
            $tableAllNk = [];
            $section = $phpWord->addSection();
            foreach ($tableData['nk'] as $k => $val) {
                $table_nk[$k] = $section->addTable($styleTable);
                $table_nk[$k]->addRow(null, array('tblHeader' => true));
                $table_nk[$k]->addCell(null, $cellRowSpan)->addText('Наименование и месторасположение конструкции', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell(null, $cellRowSpan)->addText('Дата бетонирования', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell(null, $cellRowSpan)->addText('Определяемые характеристики конструкции', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell(null, $cellRowSpan)->addText('Ед. изм.', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell(null, $cellColSpan2)->addText('Требование к характеристике', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell(null, $cellRowSpan)->addText('Нормативный документ на метод испытания (раздел, пункт)', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell(null, $cellColSpan2)->addText('Результаты испытаний', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell(null, $cellRowSpan)->addText('Соответствие характеристики требованиям нормативной (проектной) документации', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addRow(null, array('tblHeader' => true));
                $table_nk[$k]->addCell(null, $cellRowContinue);
                $table_nk[$k]->addCell(null, $cellRowContinue);
                $table_nk[$k]->addCell(null, $cellRowContinue);
                $table_nk[$k]->addCell(null, $cellRowContinue);
                $table_nk[$k]->addCell(null)->addText('Нормативная (проектная) документация (раздел, пункт)', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell(null)->addText('Нормативное значение', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell(null, $cellRowContinue);
                $table_nk[$k]->addCell(null)->addText('Фактическое единичное значение характеристики (по участкам конструкции)', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell(null)->addText('Фактическое среднее значение характеристики конструкции', array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell(null, $cellRowContinue);
                $table_nk[$k]->addRow(null, array('tblHeader' => true));
                $table_nk[$k]->addCell()->addText(1, array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell()->addText(2, array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell()->addText(3, array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell()->addText(4, array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell()->addText(5, array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell()->addText(6, array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell()->addText(7, array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell()->addText(8, array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell()->addText(9, array('size' => 10), $cellAllCentered);
                $table_nk[$k]->addCell()->addText(10, array('size' => 10), $cellAllCentered);
                $curId = 0;
                $comment = '';
                foreach ($val as $key => $tableRes) {
                    if ($key === 'name_table') {
                        $tableAllNk[$k] = [
                            $key => $tableRes
                        ];
                        continue;
                    }
                    if ($key === 'count') {
                        continue;
                    }
                    $table_nk[$k]->addRow();
                    if ($tableRes['id'] != $curId) {
                        $curId = $tableRes['id'];
                        if (in_array($protocolInformation['protocol_type'], [3, 5, 60])) {
                            $protocolInformation['Description'] = preg_replace('~<w:(?:br|tab)\s*/>~iu', '',  $protocolInformation['Description']);
                            $protocolInformation['Description'] = str_replace(["\r\n", "\n", "\r", "\t", "<w:br/>"], '', $protocolInformation['Description']);
                            $protocolInformation['Description'] = preg_replace('~<w:i\b[^>]*/>~iu', '', $protocolInformation['Description']);
                            $protocolInformation['Description'] = preg_replace('~<w:rPr\b[^>]*>.*?</w:rPr>~isu', '', $protocolInformation['Description']);
                            $protocolInformation['Description'] = preg_replace('~<[^>]+>~', '', $protocolInformation['Description']);
                            $protocolInformation['Description'] = str_replace(' Класс бетона', '. Класс бетона', $protocolInformation['Description']);
                            $table_nk[$k]->addCell(null, $cellRowSpan)->addText($protocolInformation['Description'], array('size' => 10), $cellAllCentered);
                            $table_nk[$k]->addCell(null, $cellRowSpan)->addText($tableRes['Date'], array('size' => 10), $cellAllCentered);
                        } else {
                            $table_nk[$k]->addCell(null, $cellRowSpan)->addText($tableRes['Object'], array('size' => 10), $cellAllCentered);
                            $table_nk[$k]->addCell(null, $cellRowSpan)->addText($tableRes['Date'], array('size' => 10), $cellAllCentered);
                        }
                    } else {
                        $table_nk[$k]->addCell(null, $cellRowContinue);
                        $table_nk[$k]->addCell(null, $cellRowContinue);
                    }
                    if (in_array($protocolInformation['protocol_type'], [60])) {
                        if ($tableRes['Specification'] == 'Фактический класс бетона по прочности') {
                            $tableRes['Specification'] = 'Фактический класс бетона по прочности на сжатие';
                        }
                    }
                    $table_nk[$k]->addCell(null, $cellAllCentered)->addText($tableRes['Specification'], array('size' => 10), $cellAllCentered);
                    $table_nk[$k]->addCell(null, $cellAllCentered)->addText($tableRes['Ed'], array('size' => 10), $cellAllCentered);
                    $table_nk[$k]->addCell(null, $cellAllCentered)->addText($tableRes['Normdoc'], array('size' => 10), $cellAllCentered);
                    $table_nk[$k]->addCell(null, $cellAllCentered)->addText($tableRes['Normval'], array('size' => 10), $cellAllCentered);
                    $table_nk[$k]->addCell(null, $cellAllCentered)->addText($tableRes['Normdocmet'], array('size' => 10), $cellAllCentered);
                    $table_nk[$k]->addCell(null, $cellAllCentered)->addText($tableRes['SingleValues'], array('size' => 10), $cellAllCentered);
                    $table_nk[$k]->addCell(null, $cellAllCentered)->addText($tableRes['Values'], array('size' => 10), $cellAllCentered);
                    $table_nk[$k]->addCell(null, $cellAllCentered)->addText($tableRes['Result'], array('size' => 10), $cellAllCentered);
                    if (!empty($tableRes['Comment'])) {
                        $comment = $tableRes['Comment'];
                    }
                }
                if (!empty($comment)) {
                    $comment = str_replace('по прочности по схеме В (п. 4.5 ГОСТ 18105-2018).', 'по прочности по схеме В (п. 4.5 ГОСТ 18105-2018);', $comment);
                    $comment = trim($comment);
                    $comment = str_replace('Примечание:', "Примечания:\n<w:br/>", $comment);
                    $comment = str_replace(' фактический', "- фактический", $comment);
                    $comment = str_replace(' Среднеквадратическое', "- среднеквадратическое", $comment);
                    $comment = str_replace('бетона, рассчитанное', "бетона Sm, рассчитанное", $comment);
                    $comment = str_replace(' Sm =', "", $comment);
                    $comment = str_replace('%;', "%,", $comment);
                    $comment = str_replace(';', "\n<w:br/>", $comment);
                    $table_nk[$k]->addRow(null, array('tblHeader' => true));
                    $table_nk[$k]->addCell(null, $cellColSpan10)
                        ->addText(
                            $comment,
                            array('size' => 10),
                            [
                                'align' => 'left',
                                'valign' => 'center',
                            ],
                        );
                }
            }


            if ($tableData['gradation']) {
                $section = $phpWord->addSection();
                $gradationTables = [];
                $gradationRepl = [];
                $plotBlocks = [];
                $plotTable = [];
                $plotImages = [];
                $plotCounter = 1;

                if (in_array($protocolInformation['protocol_type'], [3, 4, 60])) { // Схема "В"
                    foreach ($tableData['gradation'] as $k => $val) {
                        $gradationTables[$k] = $section->addTable($styleTable);
                        $gradationTables[$k]->addRow(null, array('tblHeader' => true));
                        $gradationTables[$k]->addCell(null, $cellRowSpan)->addText('Наименование конструкции, дата бетонирования', array('size' => 10), $cellAllCentered);
                        $gradationTables[$k]->addCell(null, $cellRowSpan)->addText($val['MeasuringDevice'] === 'УКС' ? 'Скорость распространения ультразвука, м/с' : 'Прочность бетона на сжатие, ИПС, МПа', array('size' => 10), $cellAllCentered);
                        $gradationTables[$k]->addCell(null, $cellColSpan2)->addText('Прочность, МПа', array('size' => 10), $cellAllCentered);
                        $gradationTables[$k]->addCell(null, $cellRowSpan)->addText('Условие отбраковки |RiH-Riф|/S', array('size' => 10), $cellAllCentered);
                        $gradationTables[$k]->addRow(null, array('tblHeader' => true));
                        $gradationTables[$k]->addCell(null, $cellRowContinue);
                        $gradationTables[$k]->addCell(null, $cellRowContinue);
                        $gradationTables[$k]->addCell(null)->addText('По результатам испытаний методом отрыва со скалыванием', array('size' => 10), $cellAllCentered);
                        $gradationTables[$k]->addCell(null)->addText('По градуировочной зависимости', array('size' => 10), $cellAllCentered);
                        $gradationTables[$k]->addCell(null, $cellRowContinue);
                        $i = 0;
                        foreach ($val['Mean'] as $key => $res) {
                            //$this->pre($val['Mean']);
                            $gradationTables[$k]->addRow();
                            if ($i === 0) {
                                $gradationTables[$k]->addCell(null, $cellRowBoth)->addText($val['Names'], array('size' => 10), $cellBothCentered);
                            } else {
                                $gradationTables[$k]->addCell(null, $cellRowContinue);
                            }
                            $i++;
                            $gradationTables[$k]->addCell(null, $cellAllCentered)->addText($res, array('size' => 10), $cellAllCentered);
                            $gradationTables[$k]->addCell(null, $cellAllCentered)->addText($val['ShearStrength'][$key], array('size' => 10), $cellAllCentered);
                            $gradationTables[$k]->addCell(null, $cellAllCentered)->addText($val['GradationStrength'][$key], array('size' => 10), $cellAllCentered);
                            $gradationTables[$k]->addCell(null, $cellAllCentered)->addText($val['Condition'][$key], array('size' => 10), $cellAllCentered);
                        }

                        $val['Comment'] = preg_replace('/г\.\s*<w:br\/>/', 'г.', $val['Comment']);
                        $val['Note'] = str_replace('формулами представленными', 'формулами, представленными', $val['Note']);

                        if (!empty($val['Comment'])) {
                            $gradationTables[$k]->addRow(null, array('tblHeader' => true));
                            $gradationTables[$k]->addCell(null, $cellColBothSpan10)->addText($val['Comment'], array('size' => 10), array('align' => 'left', 'valign' => 'center'));
                        }
                        if (!empty($val['Note'])) {
                            $gradationTables[$k]->addRow(null, array('tblHeader' => true));
                            $gradationTables[$k]->addCell(null, $cellColBothSpan10)->addText($val['Note'], array('size' => 10), $cellBothCentered);
                        }

                        $gradationRepl[$k] = ['name_table' => $val['table_name']];


                        // График
                        $plotBlocks[$k] = ['plot_name' => $val['plot_name']];
                        $plotTable[$k] = $section->addTable($cellHCentered);
                        $plotTable[$k]->addRow();
                        $plotTable[$k]->addCell()->addText('${plotImages}');
                        $plotTable[$k]->addRow();
                        $plotTable[$k]->addCell()->addText("Рисунок A.{$plotCounter} – Графическое изображение градуировочной зависимости <w:br/>", ['size' => 10], $cellHCentered);
                        $plotTable[$k]->addRow();

                        $cell = $plotTable[$k]->addCell(null, ['valign' => 'center']);

                        // Исходный текст
                        $desc = $val['Description'] ?? '';

                        // 0) Нормализуем переносы ДО regex-замен (иначе правила 5/6 не сработают)
                        $desc = preg_replace('~<w:br\s*/?>~iu', "\n", $desc);     // если вдруг остались Word-теги
                        $desc = str_replace(["\r\n", "\r"], "\n", $desc);

                        // 1) Ваши замены + условия про пустую строку (5/6)
                        $repl = [
                            // 1) уравнение -> шаблон (значения любые)
                            //    (сделал [\s\S]*? вместо .*? чтобы матчило даже если внутри есть переносы)
                            '/Установленная\s+градуировочная\s+зависимость\s+описывается\s+уравнением\s*R\s*=\s*[\s\S]*?,\s*где\s*H\s*[–-]\s*прочность\s+бетона,\s*полученная\s+косвенным\s+методом,\s*МПа\./u'
                            => 'Установленная градуировочная зависимость описывается уравнением R = …  H +  …, где H – прочность бетона, полученная косвенным методом, МПа.',

                            // 2) возраст: "в возрасте 60 суток и более." / "в возрасте 60 суток."
                            '/(в\s+возрасте)\s*\d+\s*суток(?:\s*и\s*более)?\s*\./ui'
                            => '$1 ..  суток.',

                            // 3) класс бетона: "класса В30" -> "класса В.."
                            '/(класса\s*В)\s*\d+/ui'
                            => '$1..',

                            // 4) диапазон прочностей
                            '/(в\s+диапазоне\s+прочностей\s+от)\s*\d+(?:[.,]\d+)?\s*до\s*\d+(?:[.,]\d+)?\s*МПа\./ui'
                            => '$1 …. до …. МПа.',

                            // 5) Убрать пустую строку перед
                            // "Построенная градуировочная зависимость может быть использована"
                            '/(?:\n[ \t\xC2\xA0]*){2,}(?=\s*Построенная\s+градуировочная\s+зависимость\s+может\s+быть\s+использована)/u'
                            => "\n",

                            // 6) Сделать ОДНУ пустую строку после "... суток."
                            // перед "Градуировочная зависимость применима..."
                            '/(суток\.)[ \t\xC2\xA0]*(?:\n[ \t\xC2\xA0]*)*(?=Градуировочная\s+зависимость\s+применима)/u'
                            => "$1\n\n",
                        ];

                        $desc = preg_replace(array_keys($repl), array_values($repl), $desc);
                        $desc = str_replace('Примечание', 'Примечания', $desc);

                        // 2) Нормализация пробелов/пустых строк перед выводом
                        $desc = preg_replace("~[ \t\xC2\xA0]+\n~u", "\n", $desc); // пробелы перед переносом
                        $desc = preg_replace("~\n[ \t\xC2\xA0]+~u", "\n", $desc); // пробелы после переноса
                        $desc = preg_replace("~\n[ \t\xC2\xA0]*\n~u", "\n\n", $desc); // пустые строки -> ровно одна пустая
                        $desc = preg_replace("~\n{3,}~", "\n\n", $desc); // 3+ переносов -> \n\n
                        $desc = trim($desc);

                        // 3) Вывод: строка == абзац, пустая строка == addTextBreak(1)
                        $lines = explode("\n", $desc);

                        $firstPrinted = true;
                        $prevWasEmpty = false;

                        foreach ($lines as $line) {
                            $lineTrim = trim($line, " \t\xC2\xA0");

                            if ($lineTrim === '') {
                                // одна пустая строка между абзацами
                                if (!$firstPrinted && !$prevWasEmpty) {
                                    $cell->addTextBreak(1);
                                    $prevWasEmpty = true;
                                }
                                continue;
                            }

                            $prevWasEmpty = false;

                            $cell->addText($lineTrim, ['size' => 10], [
                                'align'       => 'both',
                                'spaceBefore' => $firstPrinted ? 5 : 0,
                                'spaceAfter'  => 0,
                            ]);

                            $firstPrinted = false;
                        }
                        $plotImages[$k] = $val['PlotPath']; // путь до рисунка графика
                        $plotCounter++;
                    }
                } elseif (in_array($protocolInformation['protocol_type'], [5])) {
                    foreach ($tableData['gradation'] as $k => $val) {
                        $gradationTables[$k] = $section->addTable($styleTable);
                        $gradationTables[$k]->addRow(null, array('tblHeader' => true));
                        $gradationTables[$k]->addCell(null, $cellRowSpan)->addText('Наименование конструкции и дата бетонирования', array('size' => 10), $cellAllCentered);
                        $gradationTables[$k]->addCell(null, $cellColSpan3)->addText('Прочность, МПа', array('size' => 10), $cellAllCentered);
                        $gradationTables[$k]->addCell(null, $cellRowSpan)->addText('Условие отбраковки |RiH-Riф|/S', array('size' => 10), $cellAllCentered);
                        $gradationTables[$k]->addRow(null, array('tblHeader' => true));
                        $gradationTables[$k]->addCell(null, $cellRowContinue);
                        $gradationTables[$k]->addCell(null, $cellColSpan2)->addText('По результатам испытаний методом отрыва со скалыванием', array('size' => 10), $cellAllCentered);
                        $gradationTables[$k]->addCell(null)->addText('По градуировочной зависимости', array('size' => 10), $cellAllCentered);
                        $gradationTables[$k]->addCell(null, $cellRowContinue);
                        $i = 0;
                        foreach ($val['DirectMethod'] as $key => $res) {
                            $gradationTables[$k]->addRow();
                            if ($i === 0) {
                                $gradationTables[$k]->addCell(null, $cellRowSpan)->addText($val['NameForProtocol'], array('size' => 10), $cellAllCentered);
                            } else {
                                $gradationTables[$k]->addCell(null, $cellRowContinue);
                            }
                            $i++;
                            $gradationTables[$k]->addCell(null, $cellAllCentered)->addText("О{$i}", array('size' => 10), $cellAllCentered);
                            $gradationTables[$k]->addCell(null, $cellAllCentered)->addText($res, array('size' => 10), $cellAllCentered);
                            $gradationTables[$k]->addCell(null, $cellAllCentered)->addText($val['IndirectMethod'][$key], array('size' => 10), $cellAllCentered);
                            $gradationTables[$k]->addCell(null, $cellAllCentered)->addText($val['RatioValue'][$key], array('size' => 10), $cellAllCentered);
                        }

                        if (!empty($val['Description'])) {
                            $gradationTables[$k]->addRow(null, array('tblHeader' => true));
                            $gradationTables[$k]->addCell(null, $cellColBothSpan10)->addText($val['Description'], array('size' => 10), $cellBothCentered);
                        }

                        $gradationRepl[$k] = ['name_table' => $val['table_name']];
                    }
                }


                //Рисунок расположения участков проведения испытаний
                $drawingNumber = 1;
                $tableDrawing = [];
                $replacementDrawing = [];
                $section = $phpWord->addSection();
                foreach ($tableData['drawing'] as $k => $val) {
                    if (
                        !str_ends_with($val['path'], '.jpg') && !str_ends_with($val['path'], '.jpeg') &&
                        !str_ends_with($val['path'], '.png')
                    ) {
                        continue;
                    }

                    $tableDrawing[$k] = $section->addTable($cellHCentered);
                    $tableDrawing[$k]->addRow();
                    $tableDrawing[$k]->addCell()->addText('${imageDrawing}');
                    $tableDrawing[$k]->addRow();
                    $tableDrawing[$k]->addCell()->addText("Рисунок Б.{$drawingNumber} – Схема расположения участков проведения испытаний <w:br/>", ['size' => 10], $cellHCentered);

                    $replacementDrawing[$k] = [
                        'numTable' => '',
                        'table_d' => $val['table_name'],
                    ];

                    $drawingNumber++;
                }
            }

            /*if ($tableData['gradation']) {
                $section = $phpWord->addSection();
                $gradation = [];
                $gradationAllNk = [];
                if (in_array($protocolInformation['protocol_type'], [3, 4])) {
                    foreach ($tableData['gradation'] as $k => $val) {
                        $gradation[$k] = $section->addTable($styleTable);
                        $gradation[$k]->addRow(null, array('tblHeader' => true));
                        $gradation[$k]->addCell(null, $cellRowSpan)->addText('Наименование, дата изготовления контрольных образцов-кубов', array('size' => 10), $cellAllCentered);
                        $gradation[$k]->addCell(null, $cellRowSpan)->addText($val['MeasuringDevice'] === 'УКС' ? 'Скорость распространения ультразвука, м/с' : 'Прочность бетона на сжатие, ИПС, МПа', array('size' => 10), $cellAllCentered);
                        $gradation[$k]->addCell(null, $cellColSpan2)->addText('Прочность, МПа', array('size' => 10), $cellAllCentered);
                        $gradation[$k]->addCell(null, $cellRowSpan)->addText('Условие отбраковки |RiH-Riф|/S', array('size' => 10), $cellAllCentered);
                        $gradation[$k]->addRow(null, array('tblHeader' => true));
                        $gradation[$k]->addCell(null, $cellRowContinue);
                        $gradation[$k]->addCell(null, $cellRowContinue);
                        $gradation[$k]->addCell(null)->addText('По результатам испытаний методом отрыва со скалыванием', array('size' => 10), $cellAllCentered);
                        $gradation[$k]->addCell(null)->addText('По градуировочной зависимости', array('size' => 10), $cellAllCentered);
                        $gradation[$k]->addCell(null, $cellRowContinue);
                        $i = 0;
                        foreach ($val['Mean'] as $key => $res) {
                            $gradation[$k]->addRow();
                            if ($i === 0) {
                                $gradation[$k]->addCell(null, $cellRowSpan)->addText($val['Names'], array('size' => 10), $cellAllCentered);
                            } else {
                                $gradation[$k]->addCell(null, $cellRowContinue);
                            }
                            $i++;
                            $gradation[$k]->addCell(null, $cellAllCentered)->addText($res, array('size' => 10), $cellAllCentered);
                            $gradation[$k]->addCell(null, $cellAllCentered)->addText($val['ShearStrength'][$key], array('size' => 10), $cellAllCentered);
                            $gradation[$k]->addCell(null, $cellAllCentered)->addText($val['GradationStrength'][$key], array('size' => 10), $cellAllCentered);
                            $gradation[$k]->addCell(null, $cellAllCentered)->addText($val['Condition'][$key], array('size' => 10), $cellAllCentered);
                        }

                        if (!empty($val['Comment'])) {
                            $gradation[$k]->addRow(null, array('tblHeader' => true));
                            $gradation[$k]->addCell(null, $cellColSpan10)->addText($val['Comment'], array('size' => 10), array('align' => 'left', 'valign' => 'center'));
                        }
                        if (!empty($val['Note'])) {
                            $gradation[$k]->addRow(null, array('tblHeader' => true));
                            $gradation[$k]->addCell(null, $cellColSpan10)->addText($val['Note'], array('size' => 10), array('align' => 'left', 'valign' => 'center'));
                        }

                        $gradationAllNk[$k] = ['name_table' => $val['table_name']];
                    }
                } else {
                    foreach ($tableData['gradation'] as $k => $val) {
                        $gradation[$k] = $section->addTable($styleTable);
                        $gradation[$k]->addRow(null, array('tblHeader' => true));
                        $gradation[$k]->addCell(null, $cellRowSpan)->addText('Наименование, дата изготовления контрольных образцов-кубов', array('size' => 10), $cellAllCentered);
                        $gradation[$k]->addCell(null, $cellColSpan3)->addText('Прочность, МПа', array('size' => 10), $cellAllCentered);
                        $gradation[$k]->addCell(null, $cellRowSpan)->addText('Условие отбраковки |RiH-Riф|/S', array('size' => 10), $cellAllCentered);
                        $gradation[$k]->addRow(null, array('tblHeader' => true));
                        $gradation[$k]->addCell(null, $cellRowContinue);
                        $gradation[$k]->addCell(null, $cellColSpan2)->addText('По результатам испытаний методом отрыва со скалыванием', array('size' => 10), $cellAllCentered);
                        $gradation[$k]->addCell(null)->addText('По градуировочной зависимости', array('size' => 10), $cellAllCentered);
                        $gradation[$k]->addCell(null, $cellRowContinue);
                        $i = 0;
                        foreach ($val['DirectMethod'] as $key => $res) {
                            $gradation[$k]->addRow();
                            if ($i === 0) {
                                $gradation[$k]->addCell(null, $cellRowSpan)->addText($val['NameForProtocol'], array('size' => 10), $cellAllCentered);
                            } else {
                                $gradation[$k]->addCell(null, $cellRowContinue);
                            }
                            $i++;
                            $gradation[$k]->addCell(null, $cellAllCentered)->addText("О{$i}", array('size' => 10), $cellAllCentered);
                            $gradation[$k]->addCell(null, $cellAllCentered)->addText($res, array('size' => 10), $cellAllCentered);
                            $gradation[$k]->addCell(null, $cellAllCentered)->addText($val['IndirectMethod'][$key], array('size' => 10), $cellAllCentered);
                            $gradation[$k]->addCell(null, $cellAllCentered)->addText($val['RatioValue'][$key], array('size' => 10), $cellAllCentered);
                        }

                        if (!empty($val['Description'])) {
                            $gradation[$k]->addRow(null, array('tblHeader' => true));
                            $gradation[$k]->addCell(null, $cellColSpan10)->addText($val['Description'], array('size' => 10), array('align' => 'left', 'valign' => 'center'));
                        }

                        $gradationAllNk[$k] = ['name_table' => $val['table_name']];
                    }
                }


                $plotNumber = 1;
                $plot = [];
                $plotTable = [];
                $chartAllNk = [];
                $section = $phpWord->addSection();
                foreach ($tableData['gradation'] as $k => $val) {
                    $chartAllNk[$k] = [
                        'name_plot' => $val['name_plot'],
                        'chart_search' => $val['chart_search'],
                    ];

                    $plotTable[$k] = $section->addTable($cellHCentered);
                    $plotTable[$k]->addRow();
                    $plotTable[$k]->addCell()->addText('${myImage}');
                    $plotTable[$k]->addRow();
                    $plotTable[$k]->addCell()->addText("Рисунок A.{$plotNumber} – Графическое изображение градуировочной зависимости <w:br/>", ['size' => 10], $cellHCentered);
                    $plotTable[$k]->addRow();
                    $plotTable[$k]->addCell()->addText($val['Description'], ['size' => 10], ['spaceBefore' => 5, 'align' => 'left']);

                    $plot[$k] = $val['Chart'];

                    $plotNumber++;

                }

                $drawingNumber = 1;
                $tableDrawing = [];
                $replacementDrawing = [];
                $section = $phpWord->addSection();
                foreach ($tableData['drawing'] as $k => $val) {
                    if (!str_ends_with($val['path'], '.jpg') && !str_ends_with($val['path'], '.jpeg') &&
                        !str_ends_with($val['path'], '.png')) {
                        continue;
                    }

                    $tableDrawing[$k] = $section->addTable($cellHCentered);
                    $tableDrawing[$k]->addRow();
                    $tableDrawing[$k]->addCell()->addText('${imageDrawing}');
                    $tableDrawing[$k]->addRow();
                    $tableDrawing[$k]->addCell()->addText("Рисунок Б.{$drawingNumber} – Схема расположения участков проведения испытаний <w:br/>", ['size' => 10], $cellHCentered);

                    $replacementDrawing[$k] = [
                        'numTable' => '',
                        'table_d' => $val['table_name'],
                    ];

                    $drawingNumber++;
                }

            }*/
        }
        //		$this->pre($gradationRepl);
        // Расчуёт среднего значения с единичными значениями для стандартного протокола
        // (Данные для отдельной таблицы с единичными значениями, если отмечен checkbox "Выводить единичные значения в протокол?" и Тип протокола не упрощённый)



        if (!empty($tableData['sred_only']) && !$protocolShort) {
            $section = $phpWord->addSection();
            $table_sred = $section->addTable($styleTable);
            $table_sred->addRow(null, array('tblHeader' => true));
            $table_sred->addCell(null, $cellRowSpan)->addText('Объект исследований и испытаний (шифр проб/образцов в ИЦ)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellRowSpan)->addText('Определяемая характеристика (показатель)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellRowSpan)->addText('Ед. изм.', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellColSpan3)->addText('Требования нормативных документов', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellRowSpan)->addText('Документ, устанавливающий правила и методы исследований (испытаний) и измерений (раздел, пункт)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellRowSpan)->addText('Результаты исследований (испытаний) и измерений', array('size' => 10), $cellAllCentered);
            if ($results['no_compliance'] == 0) {
                $table_sred->addCell(null, $cellRowSpan)->addText('Соответствие характеристики (показателя) требованиям нормативной (проектной) документации', array('size' => 10), $cellAllCentered);
            }
            $table_sred->addRow(null, array('tblHeader' => true));
            $table_sred->addCell(null, $cellRowContinue);
            $table_sred->addCell(null, $cellRowContinue);
            $table_sred->addCell(null, $cellRowContinue);
            $table_sred->addCell(null)->addText('нормативная (проектная) документация (раздел, пункт)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null)->addText('наименование характеристики (показателя)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null)->addText('нормативное значение характеристики (показателя)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellRowContinue);
            $table_sred->addCell(null)->addText('Единичное', array('size' => 10), $cellAllCentered);
            //            $table_sred->addCell(null)->addText('Среднее', array('size' => 10), $cellAllCentered);
            if ($results['no_compliance'] == 0) {
                $table_sred->addCell(null, $cellRowContinue);
            }
            $table_sred->addRow(null, array('tblHeader' => true));
            $table_sred->addCell()->addText(1, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(2, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(3, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(4, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(5, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(6, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(7, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(8, array('size' => 10), $cellAllCentered);
            if ($results['no_compliance'] == 0) {
                $table_sred->addCell()->addText(9, array('size' => 10), $cellAllCentered);
            }
            $ugtp_desc_array = [];
            $topIndexesArray = ['⁰', '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹'];

            $topIndexNumber = 0;
            foreach ($tableData['sred_only'] as $key => $tableRes) {

                if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }
                $additionalTopIndex = "";

                if ($tableRes['ugtp_desc2']) {
                    $ugtpDescNotes = explode("\n<w:br/>", $tableRes['ugtp_desc2']);

                    // Берем только ПЕРВОЕ примечание для этой строки
                    $firstNote = trim($ugtpDescNotes[$topIndexNumber]);
                    if ($firstNote) {
                        $topIndexNumber++;

                        $additionalTopIndex = '';
                        $keyString = mb_str_split(strval($topIndexNumber));
                        foreach ($keyString as $keyStringValue) {
                            $additionalTopIndex .= $topIndexesArray[intval($keyStringValue)];
                        }
                        $additionalTopIndex .= '⁾';

                        $ugtp_desc_array[$topIndexNumber] = $firstNote;
                    }
                }
                $curId = 0;
                $table_sred->addRow();
                //                $this->pre($tableRes['Object']);
                if ($tableRes['id'] != $curId) {
                    $curId = $tableRes['id'];
                    $table_sred->addCell(null, $cellRowSpan)->addText($tableRes['Object'], array('size' => 10), $cellAllCentered);
                } else {
                    $table_sred->addCell(null, $cellRowContinue);
                }
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Specification'], array('size' => 10), $cellAllCentered);
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Ed'], array('size' => 10), $cellAllCentered);
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Normdoc'], array('size' => 10), $cellAllCentered);
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['SpecificationTU'], array('size' => 10), $cellAllCentered);
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Normval'], array('size' => 10), $cellAllCentered);
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Normdocmet'], array('size' => 10), $cellAllCentered);
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['SingleValues'] . $additionalTopIndex, array('size' => 10), $cellAllCentered);
                //                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Values'], array('size' => 10), $cellAllCentered);
                if ($results['no_compliance'] == 0) {
                    $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Result'], array('size' => 10), $cellAllCentered);
                }
            }
            $indexesString = "";

            foreach ($ugtp_desc_array as $key => $value) {
                if (!$value) continue;

                $indexesString .= "\n<w:br/>$key. $value";
            }
            if (!empty($protocolInformation['dopInfo2'])) {
                if (substr($protocolInformation['dopInfo2'], -1) === "\n") {
                    $protocolInformation['dopInfo2'] = substr($protocolInformation['dopInfo2'], 0, -1);
                }
                $table_sred->addRow();
                //			$table_result->addCell($results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                $table_sred->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan10 : $cellColSpan9)
                    //				->addText(trim(str_replace(["\r\n", "\n", "\r"], '<w:br/>'. "\n", htmlspecialchars('Примечание: ' . $protocolInformation['dopInfo']))), array('size' => 10));
                    //				->addText(trim(str_replace(["\r\n", "\n", "\r", '<w:br/><w:br/>'], '<w:br/>', htmlspecialchars('Примечание: ' . $protocolInformation['dopInfo']))), array('size' => 10));
                    ->addText("" .
                        trim(str_replace(["\r\n", "\n", "\r", "\n<w:br/><w:br/>"], '<w:br/>', htmlspecialchars($protocolInformation['dopInfo2'])) . $indexesString), array('size' => 10));
            } else if ($indexesString) {
                $table_sred->addRow();
                $table_sred->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan10 : $cellColSpan9)
                    ->addText("" . substr($indexesString, 8), array('size' => 10), $paragraphStyleTableInfo);
            }
        } else if (!empty($tableData['sred']) && !$protocolShort) {
            $section = $phpWord->addSection();
            $table_sred = $section->addTable($styleTable);
            $table_sred->addRow(null, array('tblHeader' => true));
            $table_sred->addCell(null, $cellRowSpan)->addText('Объект исследований и испытаний (шифр проб/образцов в ИЦ)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellRowSpan)->addText('Определяемая характеристика (показатель)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellRowSpan)->addText('Ед. изм.', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellColSpan3)->addText('Требования нормативных документов', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellRowSpan)->addText('Документ, устанавливающий правила и методы исследований (испытаний) и измерений (раздел, пункт)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellColSpan2)->addText('Результаты исследований (испытаний) и измерений', array('size' => 10), $cellAllCentered);
            if ($results['no_compliance'] == 0) {
                $table_sred->addCell(null, $cellRowSpan)->addText('Соответствие характеристики (показателя) требованиям нормативной (проектной) документации', array('size' => 10), $cellAllCentered);
            }
            $table_sred->addRow(null, array('tblHeader' => true));
            $table_sred->addCell(null, $cellRowContinue);
            $table_sred->addCell(null, $cellRowContinue);
            $table_sred->addCell(null, $cellRowContinue);
            $table_sred->addCell(null)->addText('нормативная (проектная) документация (раздел, пункт)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null)->addText('наименование характеристики (показателя)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null)->addText('нормативное значение характеристики (показателя)', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null, $cellRowContinue);
            $table_sred->addCell(null)->addText('Единичное', array('size' => 10), $cellAllCentered);
            $table_sred->addCell(null)->addText('Среднее', array('size' => 10), $cellAllCentered);
            if ($results['no_compliance'] == 0) {
                $table_sred->addCell(null, $cellRowContinue);
            }
            $table_sred->addRow(null, array('tblHeader' => true));
            $table_sred->addCell()->addText(1, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(2, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(3, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(4, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(5, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(6, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(7, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(8, array('size' => 10), $cellAllCentered);
            $table_sred->addCell()->addText(9, array('size' => 10), $cellAllCentered);
            if ($results['no_compliance'] == 0) {
                $table_sred->addCell()->addText(10, array('size' => 10), $cellAllCentered);
            }
            $ugtp_desc_array = [];
            $topIndexesArray = ['⁰', '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹'];

            $curIdObj = '';
            $curIdSpec = '';
            $curIdEd = '';
            $curIdNorm = '';
            $topIndexNumber = 0;
            foreach ($tableData['sred'] as $key => $tableRes) {

                if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }
                $additionalTopIndex = "";

                if ($tableRes['ugtp_desc2']) {
                    $ugtpDescNotes = explode("\n<w:br/>", $tableRes['ugtp_desc2']);

                    // Берем только ПЕРВОЕ примечание для этой строки
                    $firstNote = trim($ugtpDescNotes[$topIndexNumber]);
                    if ($firstNote) {
                        $topIndexNumber++;

                        $additionalTopIndex = '';
                        $keyString = mb_str_split(strval($topIndexNumber));
                        foreach ($keyString as $keyStringValue) {
                            $additionalTopIndex .= $topIndexesArray[intval($keyStringValue)];
                        }
                        $additionalTopIndex .= '⁾';

                        $ugtp_desc_array[$topIndexNumber] = $firstNote;
                    }
                }
                $table_sred->addRow();
                //                $this->pre($tableRes['Object']);
                if ($tableRes['Object'] != $curIdObj) {
                    $table_sred->addCell(null, $cellRowSpan)->addText($tableRes['Object'], array('size' => 10), $cellAllCentered);
                    $curIdObj = $tableRes['Object'];
                } else {
                    $table_sred->addCell(null, $cellRowContinue);
                }
                if ($tableRes['Specification'] != $curIdSpec) {
                    $table_sred->addCell(null, $cellRowSpan)->addText($tableRes['Specification'], array('size' => 10), $cellAllCentered);
                    $curIdSpec = $tableRes['Specification'];
                } else {
                    $table_sred->addCell(null, $cellRowContinue);
                }
                if ($tableRes['Ed'] != $curIdEd) {
                    $table_sred->addCell(null, $cellRowSpan)->addText($tableRes['Ed'], array('size' => 10), $cellAllCentered);
                    $curIdEd = $tableRes['Ed'];
                } else {
                    $table_sred->addCell(null, $cellRowContinue);
                }
                //$table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Specification'], array('size' => 10), $cellAllCentered);
                //$table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Ed'], array('size' => 10), $cellAllCentered);
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Normdoc'], array('size' => 10), $cellAllCentered);
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['SpecificationTU'], array('size' => 10), $cellAllCentered);
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Normval'], array('size' => 10), $cellAllCentered);
                if ($tableRes['Normdocmet'] != $curIdNorm) {
                    $table_sred->addCell(null, $cellRowSpan)->addText($tableRes['Normdocmet'], array('size' => 10), $cellAllCentered);
                    $curIdNorm = $tableRes['Normdocmet'];
                } else {
                    $table_sred->addCell(null, $cellRowContinue);
                }
                //$table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Normdocmet'], array('size' => 10), $cellAllCentered);
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['SingleValues'], array('size' => 10), $cellAllCentered);
                $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Values']  . $additionalTopIndex, array('size' => 10), $cellAllCentered);
                if ($results['no_compliance'] == 0) {
                    $table_sred->addCell(null, $cellAllCentered)->addText($tableRes['Result'], array('size' => 10), $cellAllCentered);
                }
            }
            $indexesString = "";

            foreach ($ugtp_desc_array as $key => $value) {
                if (!$value) continue;

                $indexesString .= "\n<w:br/>$key. $value";
            }
            if (!empty($protocolInformation['dopInfo2'])) {
                if (substr($protocolInformation['dopInfo2'], -1) === "\n") {
                    $protocolInformation['dopInfo2'] = substr($protocolInformation['dopInfo2'], 0, -1);
                }
                $table_sred->addRow();
                //			$table_result->addCell($results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                $table_sred->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan10 : $cellColSpan9)
                    //				->addText(trim(str_replace(["\r\n", "\n", "\r"], '<w:br/>'. "\n", htmlspecialchars('Примечание: ' . $protocolInformation['dopInfo']))), array('size' => 10));
                    //				->addText(trim(str_replace(["\r\n", "\n", "\r", '<w:br/><w:br/>'], '<w:br/>', htmlspecialchars('Примечание: ' . $protocolInformation['dopInfo']))), array('size' => 10));
                    ->addText("" .
                        trim(str_replace(["\r\n", "\n", "\r", "\n<w:br/><w:br/>"], '<w:br/>', htmlspecialchars($protocolInformation['dopInfo2'])) . $indexesString), array('size' => 10));
            } else if ($indexesString) {
                $table_sred->addRow();
                $table_sred->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan10 : $cellColSpan9)
                    ->addText("" . substr($indexesString, 8), array('size' => 10), $paragraphStyleTableInfo);
            }
        }

        //Таблица результатов
        if ($protocolShort) {
            //$this->pre($results);
            if (!empty($frost)) {
                //$this->pre($frost);
                $section = $phpWord->addSection();
                $table_result = $section->addTable($styleTable);
                $table_result->addRow(null, array('tblHeader' => true));
                $table_result->addCell(self::percentToTwips(15), $cellRowSpan)->addText('Метод испытаний, число циклов замораживания и оттаивания', $FontStyle, $cellAllCentered);
                $table_result->addCell(self::percentToTwips(25), $cellRowSpan)->addText('Определяемые характеристики контрольных образцов', $FontStyle, $cellAllCentered);
                $table_result->addCell(self::percentToTwips(5), $cellRowSpan)->addText('Ед. изм.', $FontStyle, $cellAllCentered);
                $table_result->addCell(self::percentToTwips(10), $cellRowSpan)->addText('Результаты испытаний контрольных образцов', $FontStyle, $cellAllCentered);
                $table_result->addCell(self::percentToTwips(25), $cellRowSpan)->addText('Определяемые характеристики основных образцов', $FontStyle, $cellAllCentered);
                $table_result->addCell(self::percentToTwips(5), $cellRowSpan)->addText('Ед. изм.', $FontStyle, $cellAllCentered);
                $table_result->addCell(self::percentToTwips(22), $cellRowSpan)->addText('Результаты испытаний основных образцов', $FontStyle, $cellAllCentered);

                $table_result->addRow();
                $table_result->addCell(null, $cellRowSpan)->addText($frost['Normdocmet'], $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('Наличие трещин, сколов, шелушения', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('-', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['ControlDamage']), $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('Наличие трещин, сколов, шелушения', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('-', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['MainDamage']), $FontStyle, $cellAllCentered);

                $table_result->addRow();
                $table_result->addCell(null, $cellRowContinue);
                $table_result->addCell(null, $cellAllCentered)->addText('Среднее уменьшение массы образцов', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('%', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['ControlMass']), $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('Среднее уменьшение массы образцов', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('%', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['MainMass']), $FontStyle, $cellAllCentered);
                $frost['ControlStrength'] = str_replace('.', ',', $frost['ControlStrength']);
                $frost['MainStrength'] = str_replace('.', ',', $frost['MainStrength']);
                $table_result->addRow();
                $table_result->addCell(null, $cellRowContinue);
                $table_result->addCell(null, $cellAllCentered)->addText('Прочность при сжатии насыщенных образцов', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('МПа', $FontStyle, $cellAllCentered);
                //$table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['ControlStrength']), array('size' => 10), $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText(trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>", "<w:br/>"], "\n<w:br/>", $frost['ControlStrength'])), $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('Прочность при сжатии образцов после испытания', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('МПа', $FontStyle, $cellAllCentered);
                //$table_frost->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['MainStrength']), array('size' => 10), $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText(trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>", "<w:br/>"], "\n<w:br/>", $frost['MainStrength'])), $FontStyle, $cellAllCentered);

                $table_result->addRow();
                $table_result->addCell(null, $cellRowContinue);
                $table_result->addCell(null, $cellAllCentered)->addText('Средняя прочность при сжатии насыщенных образцов', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('МПа', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['ControlMedium']), $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('Средняя прочность при сжатии образцов после испытания', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('МПа', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['MainMedium']), $FontStyle, $cellAllCentered);

                $table_result->addRow();
                $table_result->addCell(null, $cellRowContinue);
                $table_result->addCell(null, $cellAllCentered)->addText('Нижняя граница доверительного интервала XminI', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('МПа', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['ControlBottomLine']), $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('Нижняя граница доверительного интервала XminII', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText('МПа', $FontStyle, $cellAllCentered);
                $table_result->addCell(null, $cellAllCentered)->addText(str_replace('.', ',', $frost['MainBottomLine']), $FontStyle, $cellAllCentered);

                $table_result->addRow();
                if ($frost['com']) {
                    $table_result->addCell(null, array('gridSpan' => 8, 'valign' => 'center'))->addText("Примечания: {$frost['com']} <w:br/> Образцы не выдержали испытание на морозостойкость и не соответствуют марке по морозостойкости {$frost['Mark']}", $FontStyle, $cellAllCentered);
                } else {
                    $table_result->addCell(null, array('gridSpan' => 8, 'valign' => 'center'))->addText("Примечания: Соотношение XminII ≥ 0,9 XminI {$frost['Ratio']} <w:br/> Образцы {$frost['Tested']} испытание на морозостойкость и {$frost['Match']} марке по морозостойкости {$frost['Mark']}", $FontStyle, $cellAllCentered);
                }


                //$tableAllResult[] = $frost;
            } elseif (!empty($tableData['sred_only'])) {
                $section = $phpWord->addSection();
                $table_result = $section->addTable($styleTable);
                $table_result->addRow(null, array('tblHeader' => true));
                $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2600, ['vMerge' => 'restart'])->addText('Определяемая характеристика (показатель)', $FontStyle, $paragraphStyle);
                $table_result->addCell(800, ['vMerge' => 'restart'])->addText('Ед. изм.', $FontStyle, $paragraphStyle);
                $table_result->addCell('auto', $cellColSpan3)->addText('Требования нормативных документов', $FontStyle, $paragraphStyle);
                $table_result->addCell(1800, ['vMerge' => 'restart'])->addText('Нормативный документ на метод испытания (раздел, пункт)', $FontStyle, $paragraphStyle);
                $table_result->addCell('auto', $cellRowContinue)->addText('Фактические значения показателя', $FontStyle, $paragraphStyle);
                if ($results['no_compliance'] == 0) {
                    $table_result->addCell(1400, ['vMerge' => 'restart'])->addText('Соответствие характеристики требованиям нормативной (проектной) документации', $FontStyle, $paragraphStyle);
                }
                $table_result->addRow(null, array('tblHeader' => true));
                $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2600, $cellRowContinue);
                $table_result->addCell(800, $cellRowContinue);
                $table_result->addCell(1400)->addText('нормативная (проектная) документация (раздел, пункт)', $FontStyle, $paragraphStyle);
                $table_result->addCell(1400)->addText('наименование показателя', $FontStyle, $paragraphStyle);
                $table_result->addCell(1200)->addText('нормативное значение показателя', $FontStyle, $paragraphStyle);
                $table_result->addCell(1800, $cellRowContinue);
                $table_result->addCell($results['no_compliance'] == 0 ? 1350 : 1280)->addText('Единичное', $FontStyle, $paragraphStyle);
                //                $table_result->addCell($results['no_compliance'] == 0 ? 800 : 800)->addText('Среднее', $FontStyle, $paragraphStyle);
                if ($results['no_compliance'] == 0) {
                    $table_result->addCell(1400, $cellRowContinue);
                }
                $table_result->addRow(null, array('tblHeader' => true));
                $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2600)->addText(1, $FontStyle, $paragraphStyle);
                $table_result->addCell(800)->addText(2, $FontStyle, $paragraphStyle);
                $table_result->addCell(1400)->addText(3, $FontStyle, $paragraphStyle);
                $table_result->addCell(1400)->addText(4, $FontStyle, $paragraphStyle);
                $table_result->addCell(1200)->addText(5, $FontStyle, $paragraphStyle);
                $table_result->addCell(1800)->addText(6, $FontStyle, $paragraphStyle);
                $table_result->addCell(1800)->addText(7, $FontStyle, $paragraphStyle);
                if ($results['no_compliance'] == 0) {
                    $table_result->addCell(1400)->addText(8, $FontStyle, $paragraphStyle);
                }
                //				$this->pre($results);

                $ugtp_desc_array = [];

                $topIndexesArray = ['⁰', '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹'];

                $topIndexNumber = 0;

                foreach ($results as $key => $tableRes) {
                    if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                        continue;
                    }

                    $additionalTopIndex = "";

                    if ($tableRes['ugtp_desc']) {
                        $topIndexNumber++;

                        $keyString = mb_str_split(strval($topIndexNumber));

                        foreach ($keyString as $keyStringValue) {
                            $additionalTopIndex .= $topIndexesArray[intval($keyStringValue)];
                        }

                        $additionalTopIndex .= '⁾';

                        $ugtp_desc_array[$topIndexNumber] = $tableRes['ugtp_desc'];
                    }

                    $table_result->addRow('auto');
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Specification'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Ed'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normdoc'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['SpecificationTU'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normval'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normdocmet'], $FontStyle, $paragraphStyle);
                    $tableRes['SingleValues'] = preg_replace('/\s+/u', '', $tableRes['SingleValues']);
                    $tableRes['SingleValues'] = trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>", "<w:br/>"], "\n<w:br/>", $tableRes['SingleValues']));
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['SingleValues'] ?? '-', $FontStyle, $paragraphStyle);
                    //                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Values'] . $additionalTopIndex, $FontStyle, $paragraphStyle);
                    if ($results['no_compliance'] == 0) {
                        $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Result'], $FontStyle, $paragraphStyle);
                    }
                    //$this->pre($tableRes);
                }

                $indexesString = "";

                foreach ($ugtp_desc_array as $key => $value) {
                    if (!$value) continue;

                    $indexesString .= "\n<w:br/>$key. $value";
                }

                if (!empty($protocolInformation['dopInfo1'])) {
                    if (substr($protocolInformation['dopInfo1'], -1) === "\n") {
                        $protocolInformation['dopInfo1'] = substr($protocolInformation['dopInfo1'], 0, -1);
                    }
                    $table_result->addRow();
                    $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                        ->addText("" .
                            trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>"], "\n<w:br/>", htmlspecialchars($protocolInformation['dopInfo1']))) . $indexesString, $FontStyle, $paragraphStyleTableInfo);
                } else if ($indexesString) {
                    $table_result->addRow();
                    $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                        ->addText("" . substr($indexesString, 8), $FontStyle, $paragraphStyleTableInfo);
                }
            } else if (!empty($tableData['sred'])) { // Сокращённый протокол с единичными значениями

                $section = $phpWord->addSection();
                $table_result = $section->addTable($styleTable);
                $table_result->addRow(null, array('tblHeader' => true));
                $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2600, ['vMerge' => 'restart'])->addText('Определяемая характеристика (показатель)', $FontStyle, $paragraphStyle);
                $table_result->addCell(800, ['vMerge' => 'restart'])->addText('Ед. изм.', $FontStyle, $paragraphStyle);
                $table_result->addCell('auto', $cellColSpan3)->addText('Требования нормативных документов', $FontStyle, $paragraphStyle);
                $table_result->addCell(1800, ['vMerge' => 'restart'])->addText('Нормативный документ на метод испытания (раздел, пункт)', $FontStyle, $paragraphStyle);
                $table_result->addCell('auto', $cellColSpan2)->addText('Фактические значения показателя', $FontStyle, $paragraphStyle);
                if ($results['no_compliance'] == 0) {
                    $table_result->addCell(1400, ['vMerge' => 'restart'])->addText('Соответствие характеристики требованиям нормативной (проектной) документации', $FontStyle, $paragraphStyle);
                }
                $table_result->addRow(null, array('tblHeader' => true));
                $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2600, $cellRowContinue);
                $table_result->addCell(800, $cellRowContinue);
                $table_result->addCell(1400)->addText('нормативная (проектная) документация (раздел, пункт)', $FontStyle, $paragraphStyle);
                $table_result->addCell(1400)->addText('наименование показателя', $FontStyle, $paragraphStyle);
                $table_result->addCell(1200)->addText('нормативное значение показателя', $FontStyle, $paragraphStyle);
                $table_result->addCell(1800, $cellRowContinue);
                $table_result->addCell($results['no_compliance'] == 0 ? 750 : 480)->addText('Единичное', $FontStyle, $paragraphStyle);
                $table_result->addCell($results['no_compliance'] == 0 ? 800 : 800)->addText('Среднее', $FontStyle, $paragraphStyle);
                if ($results['no_compliance'] == 0) {
                    $table_result->addCell(1400, $cellRowContinue);
                }
                $table_result->addRow(null, array('tblHeader' => true));
                $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2600)->addText(1, $FontStyle, $paragraphStyle);
                $table_result->addCell(800)->addText(2, $FontStyle, $paragraphStyle);
                $table_result->addCell(1400)->addText(3, $FontStyle, $paragraphStyle);
                $table_result->addCell(1400)->addText(4, $FontStyle, $paragraphStyle);
                $table_result->addCell(1200)->addText(5, $FontStyle, $paragraphStyle);
                $table_result->addCell(1800)->addText(6, $FontStyle, $paragraphStyle);
                $table_result->addCell(1800)->addText(7, $FontStyle, $paragraphStyle);
                $table_result->addCell(1800)->addText(8, $FontStyle, $paragraphStyle);
                if ($results['no_compliance'] == 0) {
                    $table_result->addCell(1400)->addText(9, $FontStyle, $paragraphStyle);
                }
                //				$this->pre($results);

                $ugtp_desc_array = [];

                $topIndexesArray = ['⁰', '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹'];

                $topIndexNumber = 0;

                foreach ($results as $key => $tableRes) {
                    if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                        continue;
                    }

                    $additionalTopIndex = "";

                    if ($tableRes['ugtp_desc']) {
                        $topIndexNumber++;

                        $keyString = mb_str_split(strval($topIndexNumber));

                        foreach ($keyString as $keyStringValue) {
                            $additionalTopIndex .= $topIndexesArray[intval($keyStringValue)];
                        }

                        $additionalTopIndex .= '⁾';

                        $ugtp_desc_array[$topIndexNumber] = $tableRes['ugtp_desc'];
                    }
                    $tableRes['SingleValues'] = preg_replace('/\s+/u', '', $tableRes['SingleValues']);
                    $tableRes['SingleValues'] = trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>", "<w:br/>"], "\n<w:br/>", $tableRes['SingleValues']));
                    $table_result->addRow('auto');
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Specification'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Ed'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normdoc'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['SpecificationTU'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normval'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normdocmet'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['SingleValues'] ?? '-', $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Values'] . $additionalTopIndex, $FontStyle, $paragraphStyle);
                    if ($results['no_compliance'] == 0) {
                        $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Result'], $FontStyle, $paragraphStyle);
                    }
                }

                $indexesString = "";

                foreach ($ugtp_desc_array as $key => $value) {
                    if (!$value) continue;

                    $indexesString .= "\n<w:br/>$key. $value";
                }

                if (!empty($protocolInformation['dopInfo1'])) {
                    if (substr($protocolInformation['dopInfo1'], -1) === "\n") {
                        $protocolInformation['dopInfo1'] = substr($protocolInformation['dopInfo1'], 0, -1);
                    }
                    $table_result->addRow();
                    $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                        ->addText("" .
                            trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>"], "\n<w:br/>", htmlspecialchars($protocolInformation['dopInfo1']))) . $indexesString, $FontStyle, $paragraphStyleTableInfo);
                } else if ($indexesString) {
                    $table_result->addRow();
                    $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                        ->addText("" . substr($indexesString, 8), $FontStyle, $paragraphStyleTableInfo);
                }
            } else { // Сокращённый протокол без единичных значений
                $section = $phpWord->addSection();
                $table_result = $section->addTable(array('borderSize' => 3, 'borderColor' => '000000'));
                $table_result->addRow(null, array('tblHeader' => true));
                //				if ($results['no_compliance'] == 1) {
                //					$table_result->addCell(3050, ['vMerge' => 'restart'])->addText('Объект испытаний (шифр проб/образцов в ИЦ)', $FontStyle, $paragraphStyle);
                //				}
                $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2150, ['vMerge' => 'restart'])->addText('Определяемая характеристика (показатель)', $FontStyle, $paragraphStyle);
                $table_result->addCell(800, ['vMerge' => 'restart'])->addText('Ед. изм.', $FontStyle, $paragraphStyle);
                //				if ($results['no_compliance'] == 0) {
                $table_result->addCell(4250, $cellColSpan3)->addText('Требования нормативных документов', $FontStyle, $paragraphStyle);
                //				}
                $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2000, ['vMerge' => 'restart'])->addText('Нормативный документ на метод испытания (раздел, пункт)', $FontStyle, $paragraphStyle);
                $table_result->addCell(1400, ['vMerge' => 'restart'])->addText('Фактические значения показателя', $FontStyle, $paragraphStyle);
                if ($results['no_compliance'] == 0) {
                    $table_result->addCell(1400, ['vMerge' => 'restart'])->addText('Соответствие характеристики требованиям нормативной (проектной) документации', $FontStyle, $paragraphStyle);
                }

                $table_result->addRow(null, array('tblHeader' => true));
                $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2150, $cellRowContinue);
                $table_result->addCell(800, $cellRowContinue);
                //				if ($results['no_compliance'] == 0) {
                $table_result->addCell(1425)->addText('нормативная (проектная) документация (раздел, пункт)', $FontStyle, $paragraphStyle);
                $table_result->addCell(1475)->addText('наименование показателя', $FontStyle, $paragraphStyle);
                $table_result->addCell(1350)->addText('нормативное значение показателя', $FontStyle, $paragraphStyle);
                //				}
                $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2000, $cellRowContinue);
                $table_result->addCell(1400, $cellRowContinue);
                if ($results['no_compliance'] == 0) {
                    $table_result->addCell(1400, $cellRowContinue);
                }

                $table_result->addRow(null, array('tblHeader' => true));
                $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2150)->addText(1, $FontStyle, $paragraphStyle);
                $table_result->addCell(800)->addText(2, $FontStyle, $paragraphStyle);
                $table_result->addCell(1425)->addText(3, $FontStyle, $paragraphStyle);
                $table_result->addCell(1475)->addText(4, $FontStyle, $paragraphStyle);
                $table_result->addCell(1350)->addText(5, $FontStyle, $paragraphStyle);
                //				if ($results['no_compliance'] == 0) {
                $table_result->addCell(2100)->addText(6, $FontStyle, $paragraphStyle);
                $table_result->addCell(1400)->addText(7, $FontStyle, $paragraphStyle);
                if ($results['no_compliance'] == 0) {
                    $table_result->addCell(1400)->addText(8, $FontStyle, $paragraphStyle);
                }

                $curSpec = '';
                $curMeas = '';
                $curNorm = '';
                $curNormDoc = '';

                $ugtp_desc_array = [];

                $topIndexesArray = ['⁰', '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹'];

                $topIndexNumber = 0;
                $prevCol = [
                    'Specification' => null, // 1 столбец
                    'Ed'            => null, // 2 столбец
                    'Normdoc'       => null, // 3 столбец
                    'Normdocmet'    => null, // 6 столбец
                ];
                foreach ($results as $key => $tableRes) {
                    if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                        continue;
                    }
                    $curId = 0;

                    $additionalTopIndex = "";

                    if ($tableRes['ugtp_desc']) {
                        $topIndexNumber++;

                        $keyString = mb_str_split(strval($topIndexNumber));

                        foreach ($keyString as $keyStringValue) {
                            $additionalTopIndex .= $topIndexesArray[intval($keyStringValue)];
                        }

                        $additionalTopIndex .= '⁾';

                        $ugtp_desc_array[$topIndexNumber] = $tableRes['ugtp_desc'];
                    }
                    $tableRes['SingleValues2'] = str_replace(';', "\n<w:br/>", $tableRes['SingleValues2']) . "\n<w:br/>";
                    $table_result->addRow();
                    $sameSpec   = ($tableRes['Specification'] === $prevCol['Specification']);
                    $sameEd     = ($tableRes['Ed']            === $prevCol['Ed']);
                    $sameNormdoc = ($tableRes['Normdoc']       === $prevCol['Normdoc']);
                    $sameMethod = ($tableRes['Normdocmet']    === $prevCol['Normdocmet']);

                    $styleSpec   = array_merge($cellAllCentered, ['vMerge' => $sameSpec    ? 'continue' : 'restart']);
                    $styleEd     = array_merge($cellAllCentered, ['vMerge' => $sameEd      ? 'continue' : 'restart']);
                    $styleNormdoc = array_merge($cellAllCentered, ['vMerge' => $sameNormdoc ? 'continue' : 'restart']);
                    $styleMethod = array_merge($cellAllCentered, ['vMerge' => $sameMethod  ? 'continue' : 'restart']);

                    // 1) Specification (группируем независимо)
                    $cell = $table_result->addCell(null, $styleSpec);
                    if (!$sameSpec) {
                        $cell->addText($tableRes['Specification'], $FontStyle, $paragraphStyle);
                    }

                    // 2) Ed (группируем независимо)
                    $cell = $table_result->addCell(null, $styleEd);
                    if (!$sameEd) {
                        $cell->addText($tableRes['Ed'], $FontStyle, $paragraphStyle);
                    }

                    // 3) Normdoc (группируем независимо)
                    $cell = $table_result->addCell(null, $styleNormdoc);
                    if (!$sameNormdoc) {
                        $cell->addText($tableRes['Normdoc'], $FontStyle, $paragraphStyle);
                    }

                    // 4) SpecificationTU (без группировки)
                    $table_result->addCell(null, $cellAllCentered)
                        ->addText($tableRes['SpecificationTU'], $FontStyle, $paragraphStyle);
                    if (strpos($tableRes['Normval'], 'мелкозернистого') !== false) {
                        $tableRes['Normval'] = str_replace('мелкозернистого', "мелкозернис-\n<w:br/>того", $tableRes['Normval']);
                    }
                    // 5) Normval (без группировки)
                    $table_result->addCell(null, $cellAllCentered)
                        ->addText($tableRes['Normval'], $FontStyle, $paragraphStyle);

                    // 6) Normdocmet (группируем независимо)
                    $cell = $table_result->addCell(null, $styleMethod);
                    if (!$sameMethod) {
                        $cell->addText($tableRes['Normdocmet'], $FontStyle, $paragraphStyle);
                    }

                    // 7) Values (как было)
                    $table_result->addCell(null, $cellAllCentered)
                        ->addText($tableRes['Values'] . $additionalTopIndex, $FontStyle, $paragraphStyle);

                    // 8) Result (если нужен)
                    if ($results['no_compliance'] == 0) {
                        $table_result->addCell(null, $cellAllCentered)
                            ->addText($tableRes['Result'], $FontStyle, $paragraphStyle);
                    }

                    // обновляем "предыдущее" по каждому столбцу
                    $prevCol['Specification'] = $tableRes['Specification'];
                    $prevCol['Ed']            = $tableRes['Ed'];
                    $prevCol['Normdoc']       = $tableRes['Normdoc'];
                    $prevCol['Normdocmet']    = $tableRes['Normdocmet'];
                    //                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Specification'], $FontStyle, $paragraphStyle);
                    //                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Ed'], $FontStyle, $paragraphStyle);
                    ////					if ($results['no_compliance'] == 0) {
                    //                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normdoc'], $FontStyle, $paragraphStyle);
                    //                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['SpecificationTU'], $FontStyle, $paragraphStyle);
                    //                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normval'], $FontStyle, $paragraphStyle);
                    ////					}
                    //                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normdocmet'], $FontStyle, $paragraphStyle);
                    //                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Values'] . $additionalTopIndex, $FontStyle, $paragraphStyle);
                    //                    if ($results['no_compliance'] == 0) {
                    //                        $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Result'], $FontStyle, $paragraphStyle);
                    //                    }
                }

                $indexesString = "";

                foreach ($ugtp_desc_array as $key => $value) {
                    if (!$value) continue;

                    $indexesString .= "\n<w:br/>$key. $value";
                }

                if (!empty($protocolInformation['dopInfo1'])) {
                    if (substr($protocolInformation['dopInfo1'], -1) === "\n") {
                        $protocolInformation['dopInfo1'] = substr($protocolInformation['dopInfo1'], 0, -1);
                    }
                    $table_result->addRow();
                    $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                        ->addText("" .
                            trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>"], "\n<w:br/>", htmlspecialchars($protocolInformation['dopInfo1']))) . $indexesString, $FontStyle, $paragraphStyleTableInfo);
                } else if ($indexesString) {
                    $table_result->addRow();
                    $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                        ->addText("" . substr($indexesString, 8), $FontStyle, $paragraphStyleTableInfo);
                }
                //			}
            }
        } elseif ($protocolInformation['protocol_type'] == 43) { // форма заказчика
            $gosts = [];
            foreach ($results as $key => $tableRes) {
                if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }
                $tmp['Specification'] = $tableRes['Specification'];
                $tmp['Normdocmet'] = $tableRes['Normdocmet'];
                $tmp['Ed'] = $tableRes['Ed'];
                $gosts[$tableRes['method_id']] = $tmp;
            }

            $probeValues = [];
            foreach ($results as $key => $tableRes) {
                if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }
                $probeValues[$tableRes['id']][$tableRes['method_id']] = $tableRes['Values'];
            }

            $probes = [];
            foreach ($results as $key => $tableRes) {
                if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }
                $probes[$tableRes['id']]['cipher'] = $tableRes['cipher'];
                $probes[$tableRes['id']]['custom_cipher'] = $tableRes['custom_cipher'];
            }

            $countMethods = count($gosts);

            $section = $phpWord->addSection();
            $table_result = $section->addTable($styleTable);
            $table_result->addRow(null, array('tblHeader' => true));
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowSpan)->addText('№ пробы в ИЦ', array('size' => 10), $cellAllCentered);
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowSpan)->addText('№ пробы Заказчика*', array('size' => 10), $cellAllCentered);
            $table_result->addCell(self::WIDTH_DOC_ALBUM_TWIP - self::centimeterSizeToTwips(4), array('gridSpan' => $countMethods, 'valign' => 'center'))->addText('Результаты исследований (испытаний) и измерений: определяемая характеристика (показатель), ед. изм., документ, устанавливающий правила и методы исследований (испытаний) и измерений (раздел, пункт)', array('size' => 10), $cellAllCentered);

            $table_result->addRow(null, array('tblHeader' => true));
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowContinue);
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowContinue);
            foreach ($gosts as $key => $tableRes) {
                $table_result->addCell((self::WIDTH_DOC_ALBUM_TWIP - self::centimeterSizeToTwips(4)) / $countMethods, $cellRowSpan)->addText($tableRes['Specification'] . ', ' . $tableRes['Ed'], array('size' => 10), $cellAllCentered);
            }

            $table_result->addRow(null, array('tblHeader' => true));
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowContinue);
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowContinue);
            foreach ($gosts as $key => $tableRes) {
                $table_result->addCell((self::WIDTH_DOC_ALBUM_TWIP - self::centimeterSizeToTwips(4)) / $countMethods, $cellRowSpan)->addText($tableRes['Normdocmet'], array('size' => 10), $cellAllCentered);
            }

            $table_result->addRow(null, array('tblHeader' => true));
            for ($i = 1; $i <= $countMethods + 2; $i++) {
                $table_result->addCell(null, $cellRowSpan)->addText($i, array('size' => 10), $cellAllCentered);
            }

            foreach ($probes as $id => $tableRes) {
                $table_result->addRow();

                $table_result->addCell(null, $cellRowSpan)->addText($tableRes['cipher'], array('size' => 10), $cellAllCentered);
                $table_result->addCell(null, $cellRowSpan)->addText($tableRes['custom_cipher'], array('size' => 10), $cellAllCentered);

                foreach ($gosts as $methodId => $gost) {
                    $table_result->addCell(null, $cellRowSpan)->addText($probeValues[$id][$methodId], array('size' => 10), $cellAllCentered);
                }
            }
            if (!empty($protocolInformation['dopInfo1'])) {
                if (substr($protocolInformation['dopInfo1'], -1) === "\n") {
                    $protocolInformation['dopInfo1'] = substr($protocolInformation['dopInfo1'], 0, -1);
                }
                $table_result->addRow();
                $gridSpan = $results['no_compliance'] != 1 ? $countMethods + 2 : $countMethods + 2;
                $table_result->addCell(null, array('gridSpan' => $gridSpan))
                    ->addText('' . trim(str_replace(["\r\n", "\n", "\r", "\n<w:br/><w:br/>"], '<w:br/>', htmlspecialchars($protocolInformation['dopInfo1']))), $FontStyle);
            }
        } elseif ($protocolInformation['protocol_type'] == 44) { // форма заказчика
            $gosts = [];
            foreach ($results as $key => $tableRes) {
                if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }
                $tmp['Specification'] = $tableRes['Specification'];
                $tmp['Normdocmet'] = $tableRes['Normdocmet'];
                $tmp['Ed'] = $tableRes['Ed'];
                $gosts[$tableRes['method_id']] = $tmp;
            }

            $probeValues = [];
            foreach ($results as $key => $tableRes) {
                if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }
                $probeValues[$tableRes['id']][$tableRes['method_id']] = $tableRes['Values'];
            }

            $probes = [];
            foreach ($results as $key => $tableRes) {
                if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }
                $probes[$tableRes['id']]['cipher'] = $tableRes['cipher'];
                $probes[$tableRes['id']]['custom_cipher'] = $tableRes['custom_cipher'];
            }

            $countMethods = count($gosts);

            $section = $phpWord->addSection();
            $table_result = $section->addTable($styleTable);
            $table_result->addRow(null, array('tblHeader' => true));
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowSpan)->addText('№ пробы в ИЦ', array('size' => 10), $cellAllCentered);
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowSpan)->addText('№ пробы Заказчика*', array('size' => 10), $cellAllCentered);
            $table_result->addCell(self::WIDTH_DOC_ALBUM_TWIP - self::centimeterSizeToTwips(4), array('gridSpan' => $countMethods, 'valign' => 'center'))->addText('Результаты исследований (испытаний) и измерений: определяемая характеристика (показатель), ед. изм., документ, устанавливающий правила и методы исследований (испытаний) и измерений (раздел, пункт)', array('size' => 10), $cellAllCentered);

            $table_result->addRow(null, array('tblHeader' => true));
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowContinue);
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowContinue);
            foreach ($gosts as $key => $tableRes) {
                $table_result->addCell((self::WIDTH_DOC_ALBUM_TWIP - self::centimeterSizeToTwips(4)) / $countMethods, $cellRowSpan)->addText($tableRes['Specification'] . ', ' . $tableRes['Ed'], array('size' => 10), $cellAllCentered);
            }

            $table_result->addRow(null, array('tblHeader' => true));
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowContinue);
            $table_result->addCell(self::centimeterSizeToTwips(2), $cellRowContinue);
            foreach ($gosts as $key => $tableRes) {
                $table_result->addCell((self::WIDTH_DOC_ALBUM_TWIP - self::centimeterSizeToTwips(4)) / $countMethods, $cellRowSpan)->addText($tableRes['Normdocmet'], array('size' => 10), $cellAllCentered);
            }

            $table_result->addRow(null, array('tblHeader' => true));
            for ($i = 1; $i <= $countMethods + 2; $i++) {
                $table_result->addCell(null, $cellRowSpan)->addText($i, array('size' => 10), $cellAllCentered);
            }

            foreach ($probes as $id => $tableRes) {
                $table_result->addRow();

                $table_result->addCell(null, $cellRowSpan)->addText($tableRes['cipher'], array('size' => 10), $cellAllCentered);
                $table_result->addCell(null, $cellRowSpan)->addText($tableRes['custom_cipher'], array('size' => 10), $cellAllCentered);

                foreach ($gosts as $methodId => $gost) {
                    $table_result->addCell(null, $cellRowSpan)->addText($probeValues[$id][$methodId], array('size' => 10), $cellAllCentered);
                }
            }
            if (!empty($protocolInformation['dopInfo1'])) {
                if (substr($protocolInformation['dopInfo1'], -1) === "\n") {
                    $protocolInformation['dopInfo1'] = substr($protocolInformation['dopInfo1'], 0, -1);
                }
                $table_result->addRow();
                $gridSpan = $results['no_compliance'] != 1 ? $countMethods + 2 : $countMethods + 2;
                $table_result->addCell(null, array('gridSpan' => $gridSpan))
                    ->addText('' . trim(str_replace(["\r\n", "\n", "\r", "\n<w:br/><w:br/>"], '<w:br/>', htmlspecialchars($protocolInformation['dopInfo1']))), $FontStyle);
            }
        } elseif ($protocolInformation['protocol_type'] == 55 || $protocolInformation['protocol_type'] == 57) { // форма заказчика
            $section = $phpWord->addSection();
            $table_result = $section->addTable($styleTable);
            $table_result->addRow(null, array('tblHeader' => true));
            //				if ($results['no_compliance'] == 1) {
            //					$table_result->addCell(3050, ['vMerge' => 'restart'])->addText('Объект испытаний (шифр проб/образцов в ИЦ)', $FontStyle, $paragraphStyle);
            //				}
            $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 3050, ['vMerge' => 'restart'])->addText('Определяемая характеристика (показатель)', $FontStyle, $paragraphStyle);
            $table_result->addCell(800, ['vMerge' => 'restart'])->addText('Ед. изм.', $FontStyle, $paragraphStyle);
            //				if ($results['no_compliance'] == 0) {
            $table_result->addCell('auto', $cellColSpan3)->addText('Требования нормативных документов', $FontStyle, $paragraphStyle);
            //				}
            $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2100, ['vMerge' => 'restart'])->addText('Нормативный документ на метод испытания (раздел, пункт)', $FontStyle, $paragraphStyle);
            $table_result->addCell(1400, ['vMerge' => 'restart'])->addText('Фактические значения показателя', $FontStyle, $paragraphStyle);
            if ($results['no_compliance'] == 0) {
                $table_result->addCell(1400, ['vMerge' => 'restart'])->addText('Соответствие характеристики требованиям нормативной (проектной) документации', $FontStyle, $paragraphStyle);
            }

            $table_result->addRow(null, array('tblHeader' => true));
            $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2100, $cellRowContinue);
            $table_result->addCell(800, $cellRowContinue);
            //				if ($results['no_compliance'] == 0) {
            $table_result->addCell(1400)->addText('нормативная (проектная) документация (раздел, пункт)', $FontStyle, $paragraphStyle);
            $table_result->addCell(1400)->addText('наименование показателя', $FontStyle, $paragraphStyle);
            $table_result->addCell(1200)->addText('нормативное значение показателя', $FontStyle, $paragraphStyle);
            //				}
            $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2100, $cellRowContinue);
            $table_result->addCell(1400, $cellRowContinue);
            if ($results['no_compliance'] == 0) {
                $table_result->addCell(1400, $cellRowContinue);
            }

            $table_result->addRow(null, array('tblHeader' => true));
            $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2100)->addText(1, $FontStyle, $paragraphStyle);
            $table_result->addCell(800)->addText(2, $FontStyle, $paragraphStyle);
            $table_result->addCell(1400)->addText(3, $FontStyle, $paragraphStyle);
            $table_result->addCell(1400)->addText(4, $FontStyle, $paragraphStyle);
            $table_result->addCell(1200)->addText(5, $FontStyle, $paragraphStyle);
            //				if ($results['no_compliance'] == 0) {
            $table_result->addCell(2100)->addText(6, $FontStyle, $paragraphStyle);
            $table_result->addCell(1400)->addText(7, $FontStyle, $paragraphStyle);
            if ($results['no_compliance'] == 0) {
                $table_result->addCell(1400)->addText(8, $FontStyle, $paragraphStyle);
            }

            $curSpec = '';
            $curMeas = '';
            $curNorm = '';
            $curNormDoc = '';

            $ugtp_desc_array = [];

            $topIndexesArray = ['⁰', '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹'];

            $topIndexNumber = 0;

            foreach ($results as $key => $tableRes) {
                if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }
                $curId = 0;

                $additionalTopIndex = "";

                if ($tableRes['ugtp_desc']) {
                    $topIndexNumber++;

                    $keyString = mb_str_split(strval($topIndexNumber));

                    foreach ($keyString as $keyStringValue) {
                        $additionalTopIndex .= $topIndexesArray[intval($keyStringValue)];
                    }

                    $additionalTopIndex .= '⁾';

                    $ugtp_desc_array[$topIndexNumber] = $tableRes['ugtp_desc'];
                }

                $table_result->addRow();

                $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Specification'], $FontStyle, $paragraphStyle);
                $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Ed'], $FontStyle, $paragraphStyle);
                //					if ($results['no_compliance'] == 0) {
                $table_result->addCell(null, $cellAllCentered)->addText('ГОСТ 7473-2010 п.5 табл.2', $FontStyle, $paragraphStyle);
                if ($protocolInformation['protocol_type'] == 55) {
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['SpecificationTU'] . "\n<w:br/>" . 'П4' . "\n<w:br/>" . 'П3', $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normval'] . "\n<w:br/>" . '(16-20) ±2' . "\n<w:br/>" . '(10-15) ±2', $FontStyle, $paragraphStyle);
                    //					}
                } else {
                    $table_result->addCell(null, $cellAllCentered)->addText($protocolInformation['osk_1_name'], $FontStyle, $paragraphStyle);
                    $table_result->addCell(null, $cellAllCentered)->addText($protocolInformation['osk_1_norm'], $FontStyle, $paragraphStyle);
                }
                $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normdocmet'], $FontStyle, $paragraphStyle);
                $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Values'], $FontStyle, $paragraphStyle);
                if ($results['no_compliance'] == 0) {
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Result'], $FontStyle, $paragraphStyle);
                }
            }
            $indexesString = "";

            foreach ($ugtp_desc_array as $key => $value) {
                if (!$value) continue;

                $indexesString .= "\n<w:br/>$key. $value";
            }

            if (!empty($protocolInformation['dopInfo1'])) {
                if (substr($protocolInformation['dopInfo1'], -1) === "\n") {
                    $protocolInformation['dopInfo1'] = substr($protocolInformation['dopInfo1'], 0, -1);
                }
                $table_result->addRow();
                $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                    ->addText("" .
                        trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>"], "\n<w:br/>", htmlspecialchars($protocolInformation['dopInfo1']))) . $indexesString, $FontStyle, $paragraphStyleTableInfo);
            } else if ($indexesString) {
                $table_result->addRow();
                $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                    ->addText("" . substr($indexesString, 8), $FontStyle, $paragraphStyleTableInfo);
            }
        } elseif ($protocolInformation['protocol_type'] == 56 || $protocolInformation['protocol_type'] == 58) { // форма заказчика
            $section = $phpWord->addSection();
            $table_result = $section->addTable($styleTable);
            $table_result->addRow(null, array('tblHeader' => true));
            //				if ($results['no_compliance'] == 1) {
            //					$table_result->addCell(3050, ['vMerge' => 'restart'])->addText('Объект испытаний (шифр проб/образцов в ИЦ)', $FontStyle, $paragraphStyle);
            //				}
            $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 3050, ['vMerge' => 'restart'])->addText('Определяемая характеристика (показатель)', $FontStyle, $paragraphStyle);
            $table_result->addCell(800, ['vMerge' => 'restart'])->addText('Ед. изм.', $FontStyle, $paragraphStyle);
            //				if ($results['no_compliance'] == 0) {
            $table_result->addCell('auto', $cellColSpan3)->addText('Требования нормативных документов', $FontStyle, $paragraphStyle);
            //				}
            $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2100, ['vMerge' => 'restart'])->addText('Нормативный документ на метод испытания (раздел, пункт)', $FontStyle, $paragraphStyle);
            $table_result->addCell(1400, ['vMerge' => 'restart'])->addText('Фактические значения показателя', $FontStyle, $paragraphStyle);
            if ($results['no_compliance'] == 0) {
                $table_result->addCell(1400, ['vMerge' => 'restart'])->addText('Соответствие характеристики требованиям нормативной (проектной) документации', $FontStyle, $paragraphStyle);
            }

            $table_result->addRow(null, array('tblHeader' => true));
            $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2100, $cellRowContinue);
            $table_result->addCell(800, $cellRowContinue);
            //				if ($results['no_compliance'] == 0) {
            $table_result->addCell(1400)->addText('нормативная (проектная) документация (раздел, пункт)', $FontStyle, $paragraphStyle);
            $table_result->addCell(1400)->addText('наименование показателя', $FontStyle, $paragraphStyle);
            $table_result->addCell(1200)->addText('нормативное значение показателя', $FontStyle, $paragraphStyle);
            //				}
            $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2100, $cellRowContinue);
            $table_result->addCell(1400, $cellRowContinue);
            if ($results['no_compliance'] == 0) {
                $table_result->addCell(1400, $cellRowContinue);
            }

            $table_result->addRow(null, array('tblHeader' => true));
            $table_result->addCell($results['no_compliance'] == 0 ? 1400 : 2100)->addText(1, $FontStyle, $paragraphStyle);
            $table_result->addCell(800)->addText(2, $FontStyle, $paragraphStyle);
            $table_result->addCell(1400)->addText(3, $FontStyle, $paragraphStyle);
            $table_result->addCell(1400)->addText(4, $FontStyle, $paragraphStyle);
            $table_result->addCell(1200)->addText(5, $FontStyle, $paragraphStyle);
            //				if ($results['no_compliance'] == 0) {
            $table_result->addCell(2100)->addText(6, $FontStyle, $paragraphStyle);
            $table_result->addCell(1400)->addText(7, $FontStyle, $paragraphStyle);
            if ($results['no_compliance'] == 0) {
                $table_result->addCell(1400)->addText(8, $FontStyle, $paragraphStyle);
            }

            $curSpec = '';
            $curMeas = '';
            $curNorm = '';
            $curNormDoc = '';

            $ugtp_desc_array = [];

            $topIndexesArray = ['⁰', '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹'];

            $topIndexNumber = 0;

            foreach ($results as $key => $tableRes) {
                if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }
                $curId = 0;

                $additionalTopIndex = "";

                if ($tableRes['ugtp_desc']) {
                    $topIndexNumber++;

                    $keyString = mb_str_split(strval($topIndexNumber));

                    foreach ($keyString as $keyStringValue) {
                        $additionalTopIndex .= $topIndexesArray[intval($keyStringValue)];
                    }

                    $additionalTopIndex .= '⁾';

                    $ugtp_desc_array[$topIndexNumber] = $tableRes['ugtp_desc'];
                }

                $table_result->addRow();

                $oskSecondProcent = round(((float)$protocolInformation['osk_2_fact'] / (float)$protocolInformation['osk_2_norm']) * 100, 1);
                if (is_infinite($oskSecondProcent)) {
                    $oskSecondProcent = '...';
                }
                $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Specification'], $FontStyle, $paragraphStyle);
                $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Ed'], $FontStyle, $paragraphStyle);
                //					if ($results['no_compliance'] == 0) {
                $table_result->addCell(null, $cellAllCentered)->addText('Документ о качестве', $FontStyle, $paragraphStyle);
                $table_result->addCell(null, $cellAllCentered)->addText('Требуемая прочность на сжатие', $FontStyle, $paragraphStyle);
                $table_result->addCell(null, $cellAllCentered)->addText(str_replace(".", ",", $protocolInformation['osk_2_norm']) ?? "...", $FontStyle, $paragraphStyle);
                //					}
                $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Normdocmet'], $FontStyle, $paragraphStyle);
                $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Values'] ?? "...", $FontStyle, $paragraphStyle);
                if ($results['no_compliance'] == 0) {
                    $table_result->addCell(null, $cellAllCentered)->addText($tableRes['Result'], $FontStyle, $paragraphStyle);
                }
            }

            $indexesString = "";

            foreach ($ugtp_desc_array as $key => $value) {
                if (!$value) continue;

                $indexesString .= "\n<w:br/>$key. $value";
            }

            if (!empty($protocolInformation['dopInfo1'])) {
                if (substr($protocolInformation['dopInfo1'], -1) === "\n") {
                    $protocolInformation['dopInfo1'] = substr($protocolInformation['dopInfo1'], 0, -1);
                }
                $table_result->addRow();
                $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                    ->addText("" .
                        trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>"], "\n<w:br/>", htmlspecialchars($protocolInformation['dopInfo1']))) . $indexesString, $FontStyle, $paragraphStyleTableInfo);
            } else if ($indexesString) {
                $table_result->addRow();
                $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                    ->addText("" . substr($indexesString, 8), $FontStyle, $paragraphStyleTableInfo);
            }
        } elseif (!empty($tableAllZern) && ($protocolInformation['protocol_type'] == 0 || $protocolInformation['protocol_type'] == 1)) { // Не сокращённый и без единичных значений протокол
            $groupedResults = [];
            foreach ($results as $key => $item) {
                if (!is_array($item) || $key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }

                $cipher = $item['cipher'];
                if (!isset($groupedResults[$cipher])) {
                    $groupedResults[$cipher] = [];
                }

                $groupedResults[$cipher][] = $item;
            }
            $cipherOrder = [];
            foreach ($groupedResults as $cipher => $groupItems) {
                $cipherOrder[] = $cipher;
                $section = $phpWord->addSection();
                $table_result[$cipher] = $section->addTable($styleTable);

                $table_result[$cipher]->addRow(1417, array('tblHeader' => true));
                //$table_result[$cipher]->addCell(self::centimeterSizeToTwips(6.9), $cellRowSpan)->addText("\n<w:br/>Объект исследований и испытаний (шифр проб/образцов в ИЦ)", array('size' => 10), $cellAllCentered);
                $cell = $table_result[$cipher]->addCell(
                    self::centimeterSizeToTwips(4.9),
                    ['vMerge' => 'restart', 'valign' => 'center']
                );

                $pCenter = [
                    'align' => 'center',
                    'alignment' => 'center',
                    'spaceBefore' => 0,
                    'spaceAfter' => 0,
                ];
                $cell->addText("\u{200B}", ['size' => 1], $pCenter);
                $cell->addText('Объект исследований', ['size' => 10], $pCenter);
                $cell->addText('и испытаний', ['size' => 10], $pCenter);
                $cell->addText('(шифр проб/образцов в ИЦ)', ['size' => 10], $pCenter);
                $table_result[$cipher]->addCell($results['no_compliance'] != 1 ? self::centimeterSizeToTwips(3.6) : self::centimeterSizeToTwips(6.2), $cellRowSpan)->addText('Определяемая характеристика (показатель)', array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell(self::centimeterSizeToTwips(0.9), $cellRowSpan)->addText('Ед. изм.', array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell(null, $cellColSpan3)->addText('Требования нормативных документов', array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell(self::centimeterSizeToTwips(3), $cellRowSpan)->addText('Документ, устанавливающий правила и методы исследований (испытаний) и измерений (раздел, пункт)', array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell(self::centimeterSizeToTwips(2.7), $cellRowSpan)->addText('Результаты исследований (испытаний) и измерений', array('size' => 10), $cellAllCentered);

                if ($results['no_compliance'] != 1) {
                    $table_result[$cipher]->addCell(self::centimeterSizeToTwips(2.6), $cellRowSpan)->addText('Соответствие характеристики (показателя) требованиям нормативной (проектной) документации', array('size' => 10), $cellAllCentered);
                }

                $table_result[$cipher]->addRow(null, array('tblHeader' => true));
                $table_result[$cipher]->addCell(self::centimeterSizeToTwips(4.9), $cellRowContinue);
                $table_result[$cipher]->addCell($results['no_compliance'] != 1 ? self::centimeterSizeToTwips(3.6) : self::centimeterSizeToTwips(6.2), $cellRowContinue);
                $table_result[$cipher]->addCell(self::centimeterSizeToTwips(0.9), $cellRowContinue);
                $table_result[$cipher]->addCell(self::centimeterSizeToTwips(2.6))->addText('нормативная (проектная) документация (раздел, пункт)', array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell(self::centimeterSizeToTwips(2.6))->addText('наименование характеристики (показателя)', array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell(self::centimeterSizeToTwips(2.6))->addText('нормативное значение характеристики (показателя)', array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell(self::centimeterSizeToTwips(3), $cellRowContinue);
                $table_result[$cipher]->addCell(self::centimeterSizeToTwips(2.7), $cellRowContinue);

                if ($results['no_compliance'] != 1) {
                    $table_result[$cipher]->addCell(self::centimeterSizeToTwips(2.6), $cellRowContinue);
                }

                $table_result[$cipher]->addRow(null, array('tblHeader' => true));
                $table_result[$cipher]->addCell()->addText(1, array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell()->addText(2, array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell()->addText(3, array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell()->addText(4, array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell()->addText(5, array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell()->addText(6, array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell()->addText(7, array('size' => 10), $cellAllCentered);
                $table_result[$cipher]->addCell()->addText(8, array('size' => 10), $cellAllCentered);

                if ($results['no_compliance'] != 1) {
                    $table_result[$cipher]->addCell()->addText(9, array('size' => 10), $cellAllCentered);
                }

                $curId = 0;
                $curSpec = '';
                $curMeas = '';
                $curNorm = '';
                $curNormDoc = '';

                $ugtp_desc_array = [];
                $topIndexesArray = ['⁰', '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹'];
                $topIndexNumber = 0;

                $firstRowObject = true;

                foreach ($groupItems as $item) {
                    $additionalTopIndex = '';

                    if ($item['ugtp_desc']) {
                        $topIndexNumber++;
                        $keyString = mb_str_split(strval($topIndexNumber));

                        foreach ($keyString as $keyStringValue) {
                            $additionalTopIndex .= $topIndexesArray[intval($keyStringValue)];
                        }

                        $additionalTopIndex .= '⁾';
                        $ugtp_desc_array[$topIndexNumber] = $item['ugtp_desc'];
                    }

                    $table_result[$cipher]->addRow();

                    if ($firstRowObject) {
                        $firstRowObject = false;
                        $rowSpan = count($groupItems);
                        $table_result[$cipher]->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText($item['Object'], array('size' => 10), $cellAllCentered);
                    } else {
                        $table_result[$cipher]->addCell(null, ['vMerge' => 'continue']);
                    }

                    if ($item['Specification'] != $curSpec) {
                        $curSpec = $item['Specification'];
                        $table_result[$cipher]->addCell(null, $cellRowSpan)->addText($item['Specification'], array('size' => 10), $cellAllCentered);
                    } else {
                        $table_result[$cipher]->addCell(null, $cellRowContinue);
                    }

                    if ($item['Ed'] != $curMeas) {
                        $curMeas = $item['Ed'];
                        $table_result[$cipher]->addCell(null, $cellRowSpan)->addText($item['Ed'], array('size' => 10), $cellAllCentered);
                    } else {
                        $table_result[$cipher]->addCell(null, $cellRowContinue);
                    }

                    if ($item['Normdoc'] != $curNorm) {
                        $curNorm = $item['Normdoc'];
                        $table_result[$cipher]->addCell(null, $cellRowSpan)->addText($item['Normdoc'], array('size' => 10), $cellAllCentered);
                    } else {
                        $table_result[$cipher]->addCell(null, $cellRowContinue);
                    }

                    $table_result[$cipher]->addCell('auto', $cellAllCentered)->addText($item['SpecificationTU'], array('size' => 10), $cellAllCentered);

                    $table_result[$cipher]->addCell('auto', $cellAllCentered)->addText($item['Normval'], array('size' => 10), $cellAllCentered);

                    if ($item['Normdocmet'] != $curNormDoc) {
                        $curNormDoc = $item['Normdocmet'];
                        $table_result[$cipher]->addCell(null, $cellRowSpan)->addText($item['Normdocmet'], array('size' => 10), $cellAllCentered);
                    } else {
                        $table_result[$cipher]->addCell(null, $cellRowContinue);
                    }

                    $table_result[$cipher]->addCell('auto', $cellAllCentered)->addText($item['Values'] . $additionalTopIndex, array('size' => 10), $cellAllCentered);

                    if ($results['no_compliance'] != 1) {
                        $table_result[$cipher]->addCell('auto', $cellAllCentered)->addText($item['Result'], array('size' => 10), $cellAllCentered);
                    }
                }

                $indexesString = "";
                foreach ($ugtp_desc_array as $key => $value) {
                    if (!$value) continue;
                    $indexesString .= "\n<w:br/>$key. $value";
                }

                if (!empty($protocolInformation['dopInfo1'])) {
                    if (substr($protocolInformation['dopInfo1'], -1) === "\n") {
                        $protocolInformation['dopInfo1'] = substr($protocolInformation['dopInfo1'], 0, -1);
                    }
                    $table_result[$cipher]->addRow();
                    $colspan = $results['no_compliance'] != 1 ? 9 : 8;
                    $table_result[$cipher]->addCell(null, ['gridSpan' => $colspan])
                        ->addText("" . trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>"], "\n<w:br/>", htmlspecialchars($protocolInformation['dopInfo1']))) . $indexesString, $FontStyle, $paragraphStyleTableInfo);
                } else if ($indexesString) {
                    $table_result[$cipher]->addRow();
                    $colspan = $results['no_compliance'] != 1 ? 9 : 8;
                    $table_result[$cipher]->addCell(null, ['gridSpan' => $colspan])
                        ->addText("" . substr($indexesString, 8), $FontStyle, $paragraphStyleTableInfo);
                }
            }
            $tableAllResult = [];
            $complexQueue   = [];

            $zernByCipher = [];
            foreach ($tableAllZern as $i => $zMeta) {
                if (isset($zMeta['cipher']) && isset($table_zern[$i])) {
                    $zernByCipher[$zMeta['cipher']] = $table_zern[$i];
                } elseif (isset($zMeta['cipher']) && isset($table_deformation[$i])) {
                    $zernByCipher[$zMeta['cipher']] = $table_deformation[$i];
                }
            }

            foreach ($cipherOrder as $i => $cipher) {

                $zernPlaceholder = $tableAllZern[$i]['name_table'] ?? '${table_zern}';
                $zernMacro       = preg_replace('/^\$\{|\}$/', '', $zernPlaceholder);

                $zernTable = $zernByCipher[$cipher] ?? null;

                if ($zernTable) {
                    $tableAllResult[] = [
                        'type'      => 'zern',
                        'name_table' => $zernPlaceholder,
                        'zern_i'    => $i,
                    ];
                    $complexQueue[] = [
                        'macro' => $zernMacro,
                        'table' => $zernTable,
                    ];
                }

                if (isset($table_result[$cipher])) {
                    $tableAllResult[] = [
                        'type'       => 'result',
                        'name_table' => '${table_result}',
                        'cipher'     => $cipher,
                    ];
                    $complexQueue[] = [
                        'macro' => 'table_result',
                        'table' => $table_result[$cipher],
                    ];
                }
            }
            //$this->pre($tableAllResult);
        } elseif ($protocolInformation['protocol_type'] == 62) {
            $section = $phpWord->addSection();
            $table_result = $section->addTable($styleTable);

            $table_result->addRow();
            $table_result->addCell(3900)->addText('НАИМЕНОВАНИЕ ОПРЕДЕЛЯЕМЫХ ПОКАЗАТЕЛЕЙ', $FontStyle, $cellAllCentered);
            $table_result->addCell(3000)->addText('НД НА МЕТОДИКУ ИССЛЕДОВАНИЙ', $FontStyle, $cellAllCentered);
            $table_result->addCell(3700)->addText('РЕЗУЛЬТАТЫ ИСПЫТАНИЙ', $FontStyle, $cellAllCentered);

            // echo '<pre>';

            // print_r($results);
            // die;
            foreach ($results as $key => $item) {

                if (!is_numeric($key)) {
                    continue;
                }

                $table_result->addRow();

                // Колонка 1: Наименование показателя, ед. изм.
                $table_result->addCell(3900)->addText($item['Specification'], $FontStyle, $cellAllCentered);

                // Колонка 2: Шифр нормативного документа
                $normdoc = $item['Normdocmet'] ?? '-';
                $table_result->addCell(3000)->addText($normdoc, $FontStyle, $cellAllCentered);
                // Колонка 2: Шифр нормативного документа
                $normdoc = $item['Values'] ?? '-';
                $table_result->addCell(3700)->addText($normdoc, $FontStyle, $cellAllCentered);
            }
        } else {
            $section = $phpWord->addSection();
            $table_result = $section->addTable($styleTable);
            $table_result->addRow(1417, array('tblHeader' => true));
            //$table_result->addCell(self::centimeterSizeToTwips(3.9), $cellRowSpan)->addText('Объект исследований и испытаний (шифр проб/образцов в ИЦ', array('size' => 10), $cellAllCentered);
            $cell = $table_result->addCell(
                self::centimeterSizeToTwips(4.9),
                ['vMerge' => 'restart', 'valign' => 'center']
            );

            $pCenter = [
                'align' => 'center',
                'alignment' => 'center',
                'spaceBefore' => 0,
                'spaceAfter' => 0,
            ];
            $cell->addText("\u{200B}", ['size' => 1], $pCenter);
            $cell->addText('Объект исследований', ['size' => 10], $pCenter);
            $cell->addText('и испытаний', ['size' => 10], $pCenter);
            $cell->addText('(шифр проб/образцов в ИЦ)', ['size' => 10], $pCenter);
            $table_result->addCell($results['no_compliance'] != 1 ? self::centimeterSizeToTwips(3.6) : self::centimeterSizeToTwips(6.2), $cellRowSpan)->addText('Определяемая характеристика (показатель)', array('size' => 10), $cellAllCentered);
            $table_result->addCell(self::centimeterSizeToTwips(0.9), $cellRowSpan)->addText('Ед. изм.', array('size' => 10), $cellAllCentered);
            $table_result->addCell(null, $cellColSpan3)->addText('Требования нормативных документов', array('size' => 10), $cellAllCentered);
            $table_result->addCell(self::centimeterSizeToTwips(4), $cellRowSpan)->addText('Документ, устанавливающий правила и методы исследований (испытаний) и измерений (раздел, пункт)', array('size' => 10), $cellAllCentered);
            $table_result->addCell(self::centimeterSizeToTwips(2.7), $cellRowSpan)->addText('Результаты исследований (испытаний) и измерений', array('size' => 10), $cellAllCentered);
            if ($results['no_compliance'] != 1) {
                $table_result->addCell(self::centimeterSizeToTwips(2.6), $cellRowSpan)->addText('Соответствие характеристики (показателя) требованиям нормативной (проектной) документации', array('size' => 10), $cellAllCentered);
            }
            $table_result->addRow(null, array('tblHeader' => true));
            $table_result->addCell(self::centimeterSizeToTwips(3.9), $cellRowContinue);
            $table_result->addCell($results['no_compliance'] != 1 ? self::centimeterSizeToTwips(3.6) : self::centimeterSizeToTwips(6.2), $cellRowContinue);
            $table_result->addCell(self::centimeterSizeToTwips(0.9), $cellRowContinue);
            $table_result->addCell(self::centimeterSizeToTwips(2.6))->addText('нормативная (проектная) документация (раздел, пункт)', array('size' => 10), $cellAllCentered);
            $table_result->addCell(self::centimeterSizeToTwips(2.6))->addText('наименование характеристики (показателя)', array('size' => 10), $cellAllCentered);
            $table_result->addCell(self::centimeterSizeToTwips(2.6))->addText('нормативное значение характеристики (показателя)', array('size' => 10), $cellAllCentered);
            $table_result->addCell(self::centimeterSizeToTwips(4), $cellRowContinue);
            $table_result->addCell(self::centimeterSizeToTwips(2.7), $cellRowContinue);
            if ($results['no_compliance'] != 1) {
                $table_result->addCell(self::centimeterSizeToTwips(2.6), $cellRowContinue);
            }
            $table_result->addRow(250, array('tblHeader' => true));
            $table_result->addCell()->addText(1, array('size' => 10), $cellAllCentered);
            $table_result->addCell()->addText(2, array('size' => 10), $cellAllCentered);
            $table_result->addCell()->addText(3, array('size' => 10), $cellAllCentered);
            $table_result->addCell()->addText(4, array('size' => 10), $cellAllCentered);
            $table_result->addCell()->addText(5, array('size' => 10), $cellAllCentered);
            $table_result->addCell()->addText(6, array('size' => 10), $cellAllCentered);
            $table_result->addCell()->addText(7, array('size' => 10), $cellAllCentered);
            $table_result->addCell()->addText(8, array('size' => 10), $cellAllCentered);
            if ($results['no_compliance'] != 1) {
                $table_result->addCell()->addText(9, array('size' => 10), $cellAllCentered);
            }
            $curId = 0;

            $curSpec = '';
            $curMeas = '';
            $curNorm = '';
            $curNormDoc = '';

            $ugtp_desc_array = [];

            $topIndexesArray = ['⁰', '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹'];

            $topIndexNumber = 0;

            foreach ($results as $key => $tableRes) {
                if ($key === 'count' || $key === 'name_table' || $key === 'no_compliance') {
                    continue;
                }

                $additionalTopIndex = '';

                if ($tableRes['ugtp_desc']) {
                    $topIndexNumber++;

                    $keyString = mb_str_split(strval($topIndexNumber));

                    foreach ($keyString as $keyStringValue) {
                        $additionalTopIndex .= $topIndexesArray[intval($keyStringValue)];
                    }

                    $additionalTopIndex .= '⁾';

                    $ugtp_desc_array[$topIndexNumber] = $tableRes['ugtp_desc'];
                }

                $table_result->addRow();
                if ($tableRes['id'] != $curId) {
                    $curId = $tableRes['id'];
                    $table_result->addCell(null, $cellRowSpan)->addText($tableRes['Object'], array('size' => 10), $cellAllCentered);
                } else {
                    $table_result->addCell(null, $cellRowContinue);
                }

                if ($tableRes['Specification'] != $curSpec) {
                    $curSpec = $tableRes['Specification'];
                    $table_result->addCell(null, $cellRowSpan)->addText($tableRes['Specification'], array('size' => 10), $cellAllCentered);
                } else {
                    $table_result->addCell(null, $cellRowContinue);
                }

                if ($tableRes['Ed'] != $curMeas) {
                    $curMeas = $tableRes['Ed'];
                    $table_result->addCell(null, $cellRowSpan)->addText($tableRes['Ed'], array('size' => 10), $cellAllCentered);
                } else {
                    $table_result->addCell(null, $cellRowContinue);
                }

                if ($tableRes['Normdoc'] != $curNorm) {
                    $curNorm = $tableRes['Normdoc'];
                    $table_result->addCell(null, $cellRowSpan)->addText($tableRes['Normdoc'], array('size' => 10), $cellAllCentered);
                } else {
                    $table_result->addCell(null, $cellRowContinue);
                }

                $table_result->addCell('auto', $cellAllCentered)->addText($tableRes['SpecificationTU'], array('size' => 10), $cellAllCentered);
                $table_result->addCell('auto', $cellAllCentered)->addText($tableRes['Normval'], array('size' => 10), $cellAllCentered);

                if ($tableRes['Normdocmet'] != $curNormDoc) {
                    $curNormDoc = $tableRes['Normdocmet'];
                    $table_result->addCell(null, $cellRowSpan)->addText($tableRes['Normdocmet'], array('size' => 10), $cellAllCentered);
                } else {
                    $table_result->addCell(null, $cellRowContinue);
                }

                $table_result->addCell('auto', $cellAllCentered)->addText($tableRes['Values'] . $additionalTopIndex, array('size' => 10), $cellAllCentered);
                if ($results['no_compliance'] != 1) {
                    $table_result->addCell('auto', $cellAllCentered)->addText($tableRes['Result'], array('size' => 10), $cellAllCentered);
                }
            }

            $indexesString = "";

            foreach ($ugtp_desc_array as $key => $value) {
                if (!$value) continue;

                $indexesString .= "\n<w:br/>$key. $value";
            }
            if (!empty($protocolInformation['dopInfo1'])) {
                if (substr($protocolInformation['dopInfo1'], -1) === "\n") {
                    $protocolInformation['dopInfo1'] = substr($protocolInformation['dopInfo1'], 0, -1);
                }
                $table_result->addRow();
                $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                    ->addText("" .
                        trim(str_replace(["\r\n", "\n", "\r", "<w:br/><w:br/>"], "\n<w:br/>", htmlspecialchars($protocolInformation['dopInfo1']))) . $indexesString, $FontStyle, $paragraphStyleTableInfo);
            } else if ($indexesString) {
                $table_result->addRow();
                $table_result->addCell(null, $results['no_compliance'] != 1 ? $cellColSpan9 : $cellColSpan8)
                    ->addText("" .
                        substr($indexesString, 8), $FontStyle, $paragraphStyleTableInfo);
            }
        }

        $result2 = $results;

        unset($result2['count']);
        unset($result2['name_table']);
        unset($result2['no_compliance']);

        if (!empty($result2)) {
            if (
                empty($tableAllZern) && ($protocolInformation['protocol_type'] != 0 || $protocolInformation['protocol_type'] != 1)
                || (!empty($tableAllZern)) && $protocolShort
            ) {
                $tableAllResult[] = $results;
            }
        }

        // Если есть при расчёте среднего отмечен чекбокс "Выводить единичные значения в протокол?" и протокол стандартный, еще одну таблицу с единичными значениями.
        if (!empty($tableData['sred_only']) && !$protocolShort) {
            $tableAllResult[] = $tableData['sred_only'];
        } else if (!empty($tableData['sred']) && !$protocolShort) {
            $tableAllResult[] = $tableData['sred'];
        }

        $countTable = count($tableAllResult);
        $replacement = [];
        $replacementPlot = [];
        if ($protocolShort || in_array($protocolInformation['protocol_type'], [55, 56, 57, 58])) {
            $numTable = 1;
        } else {
            $numTable = 2;
        }

        foreach ($tableAllResult as $tab) {
            $replacement[] = [
                'numTable' => $numTable,
                'table_r' => $tab['name_table'],
            ];
            $numTablePre[] = $numTable;
            $numTable++;
        }

        // Неразрушающий контроль
        if (!empty($tableAllNk)) {
            $i = 0;
            foreach ($tableAllNk as $tab) {
                $tableName = $i ? '' : 'Таблица ' . $numTable;

                $replacement[] = [
                    'numTable' => $tableName,
                    'table_r' => $tab['name_table'],
                ];
                $i++;
            }
            $numTablePre[] = $numTable;
            $numTable++;
            $addTableName = 1;

            foreach ($gradationRepl as $k => $tableDataItem) {
                $tableName = 'Таблица А.' . $addTableName;

                $replacementPlot[] = [
                    'numTable' => $tableName,
                    'table_p' => $tableDataItem['name_table'],
                    'plot' => $plotBlocks[$k]['plot_name'] ?? '',
                ];

                $addTableName++;
            }
        }
        $tab4 = '';
        if (in_array($protocolInformation['protocol_type'], [3, 4, 5, 60])) {
            $tab4 .= "– результаты исследований и испытаний относятся только к испытанной конструкции.\n<w:br/>";
        } else {
            $tab4 .= "– результаты исследований и испытаний относятся только к испытанной(ным) пробе(ам)/образцу(цам);\n<w:br/>";
        }

        if (!$results['no_compliance']) {
            $tab4 .= "– заключение о соответствии принято на основании документа «Правила принятия решений в ИЦ ООО «УралНИИстром»;\n<w:br/>";
        }
        if ($protocolInformation['OtborProb'] != '' && $protocolInformation['protocol_type'] != 60) {
            $tab4 .= "– пробы отобраны и доставлены в ИЦ заказчиком;\n<w:br/>";
        }
        $tab4 .= "*– информация предоставлена Заказчиком.";
        $template->setValue('tab4', $tab4);
        if ($protocolInformation['protocol_type'] == 56 || $protocolInformation['protocol_type'] == 58) {
            $template->setValue('oskSecondProcent', str_replace(".", ",", $oskSecondProcent) ?? '...');
        }


        $protocolInformation['numTabStr'] = implode(', ', $numTablePre);
        if (
            !empty($protocolInformation['numTabStr'])
            && $protocolInformation['numTabStr'] !== "2, 3"
            && strpos($protocolInformation['numTabStr'], ',') !== false
        ) {

            $numbers = array_map('intval', array_map('trim', explode(',', $protocolInformation['numTabStr'])));
            $isSequential = range($numbers[0], end($numbers)) == $numbers;

            if ($isSequential && count($numbers) > 1) {
                $protocolInformation['numTabStr'] = $numbers[0] . '-' . end($numbers);
            }
        }
        $template->cloneBlock('block_result', $countTable, true, false, $replacement);

        if (!empty($tableAllZern)) {
            if ($protocolInformation['protocol_type'] == 0 || $protocolInformation['protocol_type'] == 1 && !empty($complexQueue)) {
                foreach ($complexQueue as $q) {
                    $template->setComplexBlock($q['macro'], $q['table']);
                }
            } else {
                foreach ($tableAllZern as $key => $val) {
                    if ($table_deformation) {
                        $template->setComplexBlock($val['name_table'], $table_deformation[$key]);
                    } else {
                        if ($protocolInformation['protocol_type'] == 0 || $protocolInformation['protocol_type'] == 1) {
                            $template->setComplexBlock($val['name_table'], $table_zern[$key]);
                        } else {
                            $template->setComplexBlock($val['name_table'], $table_zern[$key]);
                        }
                    }
                }
            }
        }

        if (!empty($table_sred)) {
            $template->setComplexBlock('table_sred', $table_sred);
        }

        if (!empty($table_frost) && !$protocolShort) {
            $template->setComplexBlock('table_frost', $table_frost);
        } elseif (!empty($table_nk)) {
            $template->cloneBlock('block_plot', $countTable, true, false, $replacementPlot);
            $template->cloneBlock('block_drawing', $countTable, true, false, $replacementDrawing);

            foreach ($tableAllNk as $key => $val) {
                $template->setComplexBlock($val['name_table'], $table_nk[$key]);
            }

            foreach ($gradationRepl as $key => $replData) {
                $template->setComplexBlock($replData['name_table'], $gradationTables[$key]);

                // Вывод графика
                if (!str_ends_with($plotImages[$key], '.png')) {
                    $template->setValue($plotBlocks[$key]['plot_name'], '');
                    $template->setValue('${plotImages}', '');
                } else {
                    $template->setComplexBlock($plotBlocks[$key]['plot_name'], $plotTable[$key]);

                    $template->setImageValue('plotImages', [
                        'path' => $plotImages[$key],
                        'width' => 670,
                        'height' => 400,
                        'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                    ]);
                }
            }

            /*foreach ($gradationAllNk as $key => $val) {
                $template->setComplexBlock($val['name_table'], $gradation[$key]);
            }*/

            // Вывод графика
            /*foreach ($chartAllNk as $key => $val) {
                if (!str_ends_with($plot[$key], '.png')) {
                    $template->setValue($val['name_plot'], '');
                    $template->setValue('${myImage}', '');
                    continue;
                }

                $template->setComplexBlock($val['name_plot'], $plotTable[$key]);

                $template->setImageValue('myImage', [
                    'path' => $plot[$key],
                    'width' => 670,
                    //'width' => 680,
                    'height' => 400,
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                ]);
            }*/

            // Вывод рисунка расположения участков проведения испытаний
            foreach ($replacementDrawing as $k => $rd) {
                $template->setComplexBlock($rd['table_d'], $tableDrawing[$k]);
                $template->setImageValue('imageDrawing', [
                    'path' => $tableData['drawing'][$k]['path'],
                    'width' => 680,
                    'height' => 400,
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                ]);
            }
        } else {
            if (!empty($results)) {
                if (is_array($table_result) && count($table_result) === 1) {
                    $table_result = reset($table_result);
                }
                if (is_array($table_result)) {
                    $tables = $table_result;
                    foreach ($tables as $table) {
                        $template->setComplexBlock("table_result", $table);
                    }
                } else {
                    if (!empty($frost)) {
                        $template->setComplexBlock('table_frost', $table_result);
                    }
                    if ($table_zern[0] && empty($table_result)) {
                        $table_result = $table_zern[0];
                    }
                    $template->setComplexBlock("table_result", $table_result);
                }
            }
        }

        $protocolInformation['num_ps'] = $protocolInformation['inputInProtocol'] ? '8 ' : '7 ';

        //		$template->setComplexBlock('table_result', $table_result);
        $template->setComplexBlock('table_oborud', $table_oborud);

        //Условия проведения испытаний
        $section = $phpWord->addSection();
        if (in_array($protocolInformation['protocol_type'], [60])) {
            $conditionHeader = $protocolInformation['inputInProtocol'] ? "7 Условия проведения исследований и испытаний:" : "";
            $conditionText = $protocolInformation['inputInProtocol'] ? "– температура воздуха: {$protocolInformation['tIspit']}°C <w:br/>– относительная влажность воздуха: {$protocolInformation['vIspit']} %" : "";
        }
        if (in_array($protocolInformation['protocol_type'], [3, 4, 5])) {
            $conditionHeader = $protocolInformation['inputInProtocol'] ? "7 Условия проведения исследований и испытаний:" : "";
            $conditionText = [
                ['Conditions' => "– температура воздуха: {$protocolInformation['tIspit']} °C;"],
                ['Conditions' => "– относительная влажность воздуха: {$protocolInformation['vIspit']} %;"],
            ];
            $conditionText = $protocolInformation['inputInProtocol'] ? $conditionText : [];
        } elseif ($protocolInformation['protocol_type'] != 60) {
            $conditionHeader = $protocolInformation['inputInProtocol'] ? "7 Условия проведения исследований и испытаний:" : "";
            $conditionText = $protocolInformation['inputInProtocol'] ? "– температура воздуха: {$protocolInformation['tIspit']} °C <w:br/>– относительная влажность воздуха: {$protocolInformation['vIspit']} %<w:br/>– атмосферное давление: {$protocolInformation['pIspit']} кПа" : "";
        }

        $conditionsTitle = $section->addText($conditionHeader, $FontStyleTitle, $paragraphStyleText1);
        $template->setComplexBlock('ConditionsTitle', $conditionsTitle);

        if (in_array($protocolInformation['protocol_type'], [3, 4, 5])) {
            $template->cloneRowAndSetValues('Conditions', $conditionText);
        } else {
            $conditionsText = $section->addText($conditionText, $FontStyle, $paragraphStyleText1);
            $template->setComplexBlock('Conditions', $conditionsText);
        }

        //Подписи в протоколе
        $verifyStr = '';

        foreach ($varify as $var) {
            if ($protocolInformation['protocol_type'] == 33 || $protocolInformation['protocol_type'] == 44 || $protocolInformation['protocol_type'] == 57 || $protocolInformation['protocol_type'] == 58 || $protocolInformation['protocol_type'] == 60) {
                $verifyStr .= " {$var['WorkPos']} ООО «УралНИИстром» {$var['Ispolnitel']}\n<w:br/>";
            } elseif ($protocolInformation['protocol_type'] == 0) {
                $verifyStr .= "{$var['WorkPos']} ООО «УралНИИстром»____________________{$var['Ispolnitel']}\n<w:br/>";
            } else {
                $verifyStr .= "{$var['WorkPos']} ООО «УралНИИстром»__________{$var['Ispolnitel']}\n<w:br/>";
            }
        }

        if ($protocolInformation['protocol_type'] === '3') {
            $verifyStr = rtrim($verifyStr, "\n<w:br/>");
        }

        $protocolInformation['verify_protocol'] = $verifyStr;

        $styleTableInfo = array('borderSize' => 3, 'borderColor' => '000000');

        $clientName = '';

        if (!empty($protocolInformation['nZakazchikFull']) && !empty($protocolInformation['nZakazchik'])) {
            $clientName = "{$protocolInformation['nZakazchikFull']} ({$protocolInformation['nZakazchik']})";
        } elseif (!empty($protocolInformation['nZakazchikFull']) && empty($protocolInformation['nZakazchik'])) {
            $clientName = $protocolInformation['nZakazchikFull'];
        } elseif (empty($protocolInformation['nZakazchikFull']) && !empty($protocolInformation['nZakazchik'])) {
            $clientName = $protocolInformation['nZakazchik'];
        } else {
            $clientName = '-';
        }

        $allCharacteristics = [];

        $charResponse = $this->DB->Query("
        SELECT um.name
        FROM ulab_gost_to_probe ugtp
        LEFT JOIN ulab_methods um ON um.id = ugtp.method_id
        WHERE ugtp.protocol_id = '{$protocolInformation['id']}'
        GROUP BY um.name
        ");

        while ($row = $charResponse->Fetch()) {
            $allCharacteristics[] = $row['name'];
        }


        $rowHeight = 557;
        $section = $phpWord->addSection();
        $table_information = $section->addTable($styleTableInfo);

        $table_information->addRow($rowHeight);
        $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Основание для проведения исследований и испытаний', array('size' => 10), array('align' => 'both'));
        $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText($protocolInformation['nDogovor'], array('size' => 10), array('align' => 'both'));
        $table_information->addRow($rowHeight);
        $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Заказчик*', array('size' => 10), array('align' => 'both'));
        $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText($clientName, array('size' => 10), array('align' => 'both'));
        $table_information->addRow($rowHeight);
        $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText("Адрес\xC2\xA0заказчика юридический/фактический*", array('size' => 10), array('align' => 'both'));
        $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText($protocolInformation['aZakazchik'], array('size' => 10), array('align' => 'both'));
        $table_information->addRow($rowHeight);
        $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('ИНН/ОГРН Заказчика*', array('size' => 10), array('align' => 'both'));
        $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("{$protocolInformation['innZakazchik']}/{$protocolInformation['ogrnZakazchik']}", array('size' => 10), array('align' => 'both'));
        if ($protocolInformation['protocol_type'] !== '3' && $protocolInformation['protocol_type'] !== '5' && $protocolInformation['protocol_type'] !== '60') {
            $table_information->addRow($rowHeight);
            $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Дата поступления проб/образцов (№ регистрации в ИЦ)', array('size' => 10), array('align' => 'both'));
            $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("{$protocolInformation['dProbe']} ({$protocolInformation['nProbe_reg']})", array('size' => 10), array('align' => 'both'));
            $table_information->addRow($rowHeight);
            $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Место отбора; дата отбора проб/образцов' . ($protocolInformation['clientTaken'] ? '*' : ''), array('size' => 10), array('align' => 'both'));
            $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("{$protocolInformation['mestoSboraProbe']}", array('size' => 10), array('align' => 'both'));
            if ($protocolInformation['career']) {
                $table_information->addRow($rowHeight);
                $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Наименование карьера*', array('size' => 10), array('align' => 'both'));
                $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("{$protocolInformation['career']}", array('size' => 10), array('align' => 'both'));
            }
        }
        if (!empty($protocolInformation['oStroit'])) {
            //			$this->pre($protocolInformation['namesForProtocol']);
            $table_information->addRow($rowHeight);
            $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Объект строительства*', array('size' => 10), array('align' => 'both'));
            $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("{$protocolInformation['oStroit']}", array('size' => 10), array('align' => 'both'));
        }
        if ($protocolInformation['determined_characteristics'] && $protocolInformation['protocol_type'] == '2') {
            $table_information->addRow($rowHeight);
            $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Определяемые характеристики', array('size' => 10), array('align' => 'both'));
            $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("{$protocolInformation['determined_characteristics_list']}", array('size' => 10), array('align' => 'both'));
        }
        if (in_array($protocolInformation['protocol_type'], [3, 4, 5, 60])) {
            if (in_array($protocolInformation['protocol_type'], [3, 5, 60])) {
                $table_information->addRow($rowHeight);
                $protocolInformation['Description'] = preg_replace('~<w:(?:br|tab)\s*/>~iu', '',  $protocolInformation['Description']);
                $protocolInformation['Description'] = str_replace(["\r\n", "\n", "\r", "\t", "<w:br/>"], '', $protocolInformation['Description']);
                $protocolInformation['Description'] = preg_replace('~<w:i\b[^>]*/>~iu', '', $protocolInformation['Description']);
                $protocolInformation['Description'] = preg_replace('~<w:rPr\b[^>]*>.*?</w:rPr>~isu', '', $protocolInformation['Description']);
                $protocolInformation['Description'] = preg_replace('~<[^>]+>~', '', $protocolInformation['Description']);
                $protocolInformation['Description'] = str_replace(' Класс бетона', '. Класс бетона', $protocolInformation['Description']);

                //$protocolInformation['Description'] = str_replace("градуировочной зависимости.<w:br/>\n", 'градуировочной зависимости.', $protocolInformation['Description']);
                $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Объект исследований и испытаний*', array('size' => 10), array('align' => 'both'));
                $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("{$protocolInformation['Description']}", array('size' => 10, 'italic' => false), array('align' => 'both'));
            } else {
                $table_information->addRow($rowHeight);
                $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Объект исследований и испытаний*', array('size' => 10), array('align' => 'both'));
                $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("{$protocolInformation['namesForProtocol']}", array('size' => 10), array('align' => 'both'));
            }
            $table_information->addRow($rowHeight);
            $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Дата бетонирования*', array('size' => 10), array('align' => 'both'));
            $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("{$protocolInformation['сoncretingDate']}", array('size' => 10), array('align' => 'both'));
        } else {
            $table_information->addRow($rowHeight);
            $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Объект исследований и испытаний* (шифр пробы/образца в ИЦ)', array('size' => 10), array('align' => 'both'));
            $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("{$protocolInformation['oIspit']}", array('size' => 10), array('align' => 'both'));
        }
        $table_information->addRow($rowHeight);
        $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Дата проведения исследований и испытаний', array('size' => 10), $cellVCentered);
        $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("{$protocolInformation['d1Ispit']}{$protocolInformation['d2Ispit']}", array('size' => 10), array('align' => 'both'));
        $table_information->addRow($rowHeight);
        $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Результаты исследований и испытаний', array('size' => 10), $cellVCentered);
        $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("Приведены в таблице(ах) {$protocolInformation['numTabStr']}", array('size' => 10), array('align' => 'both'));
        if (in_array($protocolInformation['protocol_type'], [3, 4, 5, 60])) {
            $table_information->addRow($rowHeight);
            $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Градуировочная зависимость', array('size' => 10), array('align' => 'both'));
            $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("Приведена в Приложении А", array('size' => 10), array('align' => 'both'));
            $table_information->addRow($rowHeight);
            $table_information->addCell(self::percentToTwips(40), $cellBothCentered)->addText('Схема расположения контролируемых участков*', array('size' => 10), array('align' => 'both'));
            $table_information->addCell(self::percentToTwips(60), $cellBothCentered)->addText("Приведена в Приложении Б", array('size' => 10), array('align' => 'both'));
        }

        $section = $phpWord->addSection();
        $table_information_short = $section->addTable($styleTableInfo);

        $table_information_short->addRow();
        $table_information_short->addCell(4540, ['valign' => 'center'])
            ->addText('Основание для проведения исследований и испытаний', $FontStyle, $paragraphStyleTableInfo);

        if (
            $protocolInformation['protocol_type'] !== '55' && $protocolInformation['protocol_type'] !== '56'
            && $protocolInformation['protocol_type'] !== '57' && $protocolInformation['protocol_type'] !== '58'
        ) {
            $table_information_short->addCell(5460, ['valign' => 'center'])
                ->addText($protocolInformation['nDogovor'], $FontStyle, $paragraphStyleTableInfo);
            $table_information_short->addCell(600, ['valign' => 'center'])
                ->addText($protocolInformation['A'], $FontStyle, $paragraphStyleTableInfo);
        } else {
            $table_information_short->addCell(6060, ['valign' => 'center', 'gridSpan' => 2])
                ->addText($protocolInformation['nDogovor'], $FontStyle, $paragraphStyleTableInfo);
        }

        $table_information_short->addRow();
        if (
            $protocolInformation['protocol_type'] !== '55' && $protocolInformation['protocol_type'] !== '56'
            && $protocolInformation['protocol_type'] !== '57'  && $protocolInformation['protocol_type'] !== '58'
        ) {
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Заказчик', $FontStyle, $paragraphStyleTableInfo);
        } else {
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Заказчик*', $FontStyle, $paragraphStyleTableInfo);
        }
        $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText($protocolInformation['nZakazchik'], $FontStyle, $paragraphStyleTableInfo);

        if (
            $protocolInformation['protocol_type'] !== '55' && $protocolInformation['protocol_type'] !== '56'
            && $protocolInformation['protocol_type'] !== '57'  && $protocolInformation['protocol_type'] !== '58'
        ) {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Адрес Заказчика юридический/фактический*', $FontStyle, $paragraphStyleTableInfo);
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText($protocolInformation['aZakazchik'], $FontStyle, $paragraphStyleTableInfo);
        }

        $table_information_short->addRow();
        if (
            $protocolInformation['protocol_type'] !== '55' && $protocolInformation['protocol_type'] !== '56'
            && $protocolInformation['protocol_type'] !== '57'  && $protocolInformation['protocol_type'] !== '58'
        ) {
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('ИНН/ОГРН', $FontStyle, $paragraphStyleTableInfo);
        } else {
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('ИНН/ОГРН Заказчика*', $FontStyle, $paragraphStyleTableInfo);
        }
        $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("{$protocolInformation['innZakazchik']}/{$protocolInformation['ogrnZakazchik']}", $FontStyle, $paragraphStyleTableInfo);

        if (
            $protocolInformation['protocol_type'] !== '55' && $protocolInformation['protocol_type'] !== '56'
            && $protocolInformation['protocol_type'] !== '57' && $protocolInformation['protocol_type'] !== '58'
        ) {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Дата поступления пробы (№ регистрации в ИЦ)', $FontStyle, $paragraphStyleTableInfo);
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("{$protocolInformation['dProbe']} ({$protocolInformation['nProbe_reg']})", $FontStyle, $paragraphStyleTableInfo);
        } elseif ($protocolInformation['protocol_type'] === '56') {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Дата поступления пробы', $FontStyle, $paragraphStyleTableInfo);
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("{$protocolInformation['dProbe']}", $FontStyle, $paragraphStyleTableInfo);
        } elseif ($protocolInformation['protocol_type'] === '58') {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Дата поступления пробы', $FontStyle, $paragraphStyleTableInfo);
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("{$protocolInformation['dateOtborProbe']}", $FontStyle, $paragraphStyleTableInfo);
        }

        if (
            $protocolInformation['protocol_type'] !== '55' && $protocolInformation['protocol_type'] !== '56'
            && $protocolInformation['protocol_type'] !== '57' && $protocolInformation['protocol_type'] !== '58'
        ) {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Место отбора и дата отбора' . ($protocolInformation['clientTaken'] ? '*' : ''), $FontStyle, $paragraphStyleTableInfo);
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("{$protocolInformation['mestoSboraProbe']}", $FontStyle, $paragraphStyleTableInfo);
        } elseif ($protocolInformation['protocol_type'] === '57' || $protocolInformation['protocol_type'] === '58') {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Место и дата отбора' . ($protocolInformation['clientTaken'] ? '*' : ''), $FontStyle, $paragraphStyleTableInfo);
            //$this->pre($protocolInformation['mestoSboraProbe2']);
            $protocolInformation['mestoSboraProbe2'] = str_replace("\n", "\n<w:br/>", $protocolInformation['mestoSboraProbe2']);
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("{$protocolInformation['mestoSboraProbe2']}", $FontStyle, $paragraphStyleTableInfo);
        } else {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Место и дата отбора' . ($protocolInformation['clientTaken'] ? '*' : ''), $FontStyle, $paragraphStyleTableInfo);
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText('Промзона, ул. Монтажников 11.' . "\n<w:br/>" . 'Блоки левых колец обделки AL, BL, CL, DL, EL, FL, KL' . "\n<w:br/>" . 'Блоки правых колец обделки AR, BR, CR, DR, ER, FR, KR ...', $FontStyle, $paragraphStyleTableInfo);
        }

        if ($protocolInformation['career']) {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Наименование карьера*', $FontStyle, $paragraphStyleTableInfo);
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("{$protocolInformation['career']}", $FontStyle, $paragraphStyleTableInfo);
        }

        if (!empty($protocolInformation['oStroit'])) {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Объект строительства*', $FontStyle, $paragraphStyleTableInfo);
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("{$protocolInformation['oStroit']}", $FontStyle, $paragraphStyleTableInfo);
        }

        if (
            $protocolInformation['protocol_type'] !== '55' && $protocolInformation['protocol_type'] !== '56'
            && $protocolInformation['protocol_type'] !== '57' && $protocolInformation['protocol_type'] !== '58'
        ) {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Объект испытаний, маркировка заказчика* (Шифр пробы в ИЦ)', $FontStyle, $paragraphStyleTableInfo);
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("{$protocolInformation['oIspit']}. {$protocolInformation['Description']}", $FontStyle, $paragraphStyleTableInfo);
        } else {
            if ($protocolInformation['protocol_type'] == '55') {
                $table_information_short->addRow();
                $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Объект испытаний, маркировка заказчика* (Шифр пробы в ИЦ)', $FontStyle, $paragraphStyleTableInfo);
                $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText('БСТ В45 П4 F(1)200 W12 ГОСТ 7473-2010.' . "\n<w:br/>" . 'БСТ В45 П3 F(1)200 W12 ГОСТ 7473-2010.' . "\n<w:br/>" . 'Производитель: ООО «БЕТОТЕК БЕТОН».' . "\n<w:br/>" . 'Документ о качестве бетонной смеси №... от ...', $FontStyle, $paragraphStyleTableInfo);
            } elseif ($protocolInformation['protocol_type'] == '57') {
                $protocolInformation['Description2'] = str_replace("\n", "\n<w:br/>", $protocolInformation['Description2']);
                $table_information_short->addRow();
                $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Объект испытаний, маркировка заказчика*', $FontStyle, $paragraphStyleTableInfo);
                $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText($protocolInformation['Description2'], $FontStyle, $paragraphStyleTableInfo);
            } elseif ($protocolInformation['protocol_type'] == '58') {
                $protocolInformation['Description2'] = str_replace("\n", "\n<w:br/>", $protocolInformation['Description2']);
                $table_information_short->addRow();
                $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Информация об объекте испытаний', $FontStyle, $paragraphStyleTableInfo);
                $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText($protocolInformation['Description2'], $FontStyle, $paragraphStyleTableInfo);
            } else {
                $table_information_short->addRow();
                $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Объект испытаний, маркировка заказчика* (Шифр пробы в ИЦ)', $FontStyle, $paragraphStyleTableInfo);
                $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText('Бетонные образцы-кубы с размером ребра 100 мм в количестве двух штук.' . "\n<w:br/>" . 'Проектный класс бетона по прочности на сжатие – В45.' . "\n<w:br/>" . 'Требуемая прочность бетона на сжатие в проектном возрасте – МПа.' . "\n<w:br/>" . 'Документы о качестве бетонной смеси, партии №....' . "\n<w:br/>" . 'Возраст образцов-кубов – 7/28 суток. Акт отбора проб (образцов) б/н. от ...', $FontStyle, $paragraphStyleTableInfo);
            }
        }

        $table_information_short->addRow();
        $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Дата проведения исследований и испытаний', $FontStyle, $paragraphStyleTableInfo);
        $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("{$protocolInformation['d1Ispit']}{$protocolInformation['d2Ispit']}", $FontStyle, $paragraphStyleTableInfo);

        if ($protocolInformation['protocol_type'] !== '55' && $protocolInformation['protocol_type'] !== '56' && $protocolInformation['protocol_type'] !== '57' && $protocolInformation['protocol_type'] !== '58') {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Адрес места осуществления деятельности', $FontStyle, $paragraphStyleTableInfo);
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("ИЦ ООО \"УралНИИстром\", 454047, РОССИЯ, Челябинская обл, г.о. Челябинский, вн. р-н Металлургический, г. Челябинск, ул. Сталеваров, д 5, строение 5", $FontStyle, $paragraphStyleTableInfo);
        } else {
            $table_information_short->addRow();
            $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Место проведения испытаний', $FontStyle, $paragraphStyleTableInfo);
            if ($protocolInformation['protocol_type'] === '57') {
                $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText($protocolInformation['osk_1_place'] ?? 'г. Челябинск, Промзона, ул. Монтажников 11.', $FontStyle, $paragraphStyleTableInfo);
            } else {
                $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("г. Челябинск, Промзона, ул. Монтажников 11.", $FontStyle, $paragraphStyleTableInfo);
            }
        }

        $table_information_short->addRow();
        $table_information_short->addCell(4540, array('valign' => 'center'))->addText('Результаты исследований и испытаний', $FontStyle, $paragraphStyleTableInfo);
        if (!empty($tableAllZern)) {
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("Приведены в таблице(ах) 1, 2", $FontStyle, $paragraphStyleTableInfo);
        } else {
            $table_information_short->addCell(6060, array('valign' => 'center', 'gridSpan' => 2))->addText("Приведены в таблице 1", $FontStyle, $paragraphStyleTableInfo);
        }

        $strCountTable = "таблице(ах) {$protocolInformation['numTabStr']}";
        $template->setValue('countTable', $strCountTable);
        $template->setComplexBlock('table_info', $table_information);
        $template->setComplexBlock('table_info_short', $table_information_short);
        $template->setValues($protocolInformation);
        //		$template->setComplexBlock('tableVeryfy', $table_verify);
        if (in_array($protocolInformation['protocol_type'], [33, 55, 56, 57, 58])) {
            $verifyStr = rtrim($verifyStr, "\n<w:br/>");
            if (!empty($createProtocolUserStr)) {
                $verifyStr .= "\n<w:br/>" . $createProtocolUserStr;
            }
            $template->setValue('WorkPosNew', $verifyStr);
        } elseif ($protocolInformation['protocol_type'] == 1 || $protocolInformation['protocol_type'] == 44) {
            $template->cloneRowAndSetValues('WorkPos', $varify);
        }
        if (in_array($protocolInformation['protocol_type'], [0, 43])) {
            //$createNormProtocolUser = 'Нормоконтроль:' .  "\n<w:br/>". 'Специалист по качеству ООО «УралНИИстром»_____________________ Е.Э. Апанович';
            $template->setValue('norm_protocol', $createNormProtocolUserStr);
        }
        $protocolInformation['resTables'] = "приведены в таблице(ах) {$protocolInformation['numTabStr']}";
        $template->setValues($protocolInformation);

        $template->saveAs($pathDocCurdate);

        $templateTmp = new \PhpOffice\PhpWord\TemplateProcessor($pathDocCurdate);

        $templateTmp->saveAs($forSign);

        if (
            !($protocolInformation['protocol_type'] == 34 || $protocolInformation['protocol_type'] == 33)
            && !empty($protocolInformation['number']) && is_numeric($protocolInformation['number'])
        ) {

            $qrPath = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/archive/{$protocolInformation['tz_id']}{$protocolInformation['protocolYear']}/{$protocolInformation['id']}/qrNEW.png";

            QRcode::png("http://niistrom.pro/check/?NUMP=" . $protocolInformation['number'] . "&DATE=" . date('d.m.Y', strtotime($protocolInformation['dProtocol'])), $qrPath);

            $this->addQrCode($qrPath, $pathDocCurdate);
        }



        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $nameProtocol . '".docx"');
        readfile($pathDocCurdate);

        //        $this->generateProtocol();
    }

    /**
     * Формирование таблицы phpword для КП
     * @param $info
     * @param $arrGost
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function createCommercialOfferDocument($info, $arrGost, $dealId, $dealInfo)
    {
        $result = [];
        //		foreach ($arrGost as $key => $val) {
        //			$result[$val['material_id']]['material_name'] = $val['material_name'];
        //			$result[$val['material_id']]['gosts'][] = [
        //				'method_name' => $val['method_name'],
        //				'gost_name' => $val['gost_name'],
        //				'price' => $val['price'],
        //				'tech_condition' => $val['tech_condition'],
        //				'sum' => $val['sum'],
        //				'amount' => $val['amount'],
        //			];
        //		}
        //
        $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/CommercialOffer.docx';

        $type = 'kp';

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $template = new \PhpOffice\PhpWord\TemplateProcessor($template_doc);
        //
        //		$styleTable = array('borderSize' => 3, 'borderColor' => '000000');
        //		$cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        //		$cellRowContinue = array('vMerge' => 'continue');
        //		$cellColSpan2 = array('gridSpan' => 2, 'valign' => 'center');
        //		$cellColSpan3 = array('gridSpan' => 3, 'valign' => 'center');
        //		$cellColSpan4 = array('gridSpan' => 4, 'valign' => 'center');
        //		$cellColSpan6 = array('gridSpan' => 6, 'valign' => 'center');
        //
        //		$cellHCentered = array('align' => 'center');
        //		$cellVCentered = array('valign' => 'center');
        //
        //		$section = $phpWord->addSection();
        //		$table = $section->addTable($styleTable);
        //		$table->addRow(null, array('tblHeader' => true));
        //		$table->addCell(2000, $cellRowSpan)->addText('Наименование объекта испытаний', array('bold' => false), $cellHCentered);
        //		$table->addCell(500, $cellRowSpan)->addText('Кол-во испытаний', array('bold' => false), $cellHCentered);
        //		$table->addCell(3000, $cellRowSpan)->addText('Определяемые характеристики', array('bold' => false), $cellHCentered);
        //		$table->addCell(2000, $cellRowSpan)->addText('Нормативный документ на метод испытаний', array('bold' => false), $cellHCentered);
        //		$table->addCell(1480, $cellRowSpan)->addText('Стоимость испытания, руб.', array('bold' => false), $cellHCentered);
        //		$table->addCell(1480, $cellRowSpan)->addText('Общая стоимость, руб.', array('bold' => false), $cellHCentered);
        //		$i = 1;
        //		$sumTotal = 0;
        //		foreach ($result as $itemMater) {
        //			foreach ($itemMater['gosts'] as $k => $value) {
        //				$table->addRow();
        //				if ($k == 0) {
        //					$table->addCell(null, $cellRowSpan)->addText($itemMater['material_name'], array('size' => 11), $cellHCentered);
        //				} else {
        //					$table->addCell(null, $cellRowContinue);
        //				}
        //				$table->addCell()->addText($value['amount'], array('size' => 11), $cellHCentered);
        //				$table->addCell()->addText($value['method_name'], array('size' => 11), $cellHCentered);
        //				$table->addCell()->addText($value['gost_name'], array('size' => 11), $cellHCentered);
        //				$table->addCell()->addText($value['price'], array('size' => 11), $cellHCentered);
        //				$table->addCell()->addText($value['sum'], array('size' => 11), $cellHCentered);
        //			}
        //		}

        //        $this->pre($arrGost, true);

        //        $this->pre($arrGost);


        foreach ($arrGost as $key => $val) {
            if ($dealInfo['group_type'] == 1 || !$dealInfo['group_type']) {
                $result[$val['material_id']]['material_name'] = $val['material_name'];
                $result[$val['material_id']]['material_id'] = $val['material_id'];
                $result[$val['material_id']]['nfp'] = $val['name_for_protocol'];

                if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['material_id']]['gosts'])) {
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                } else {
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                        'method_name' => $val['method_name'],
                        'gost_name' => $val['gost_name'],
                        'price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'sum' => $val['sum'],
                        'amount' => $val['amount'],
                    ];
                }

                $result[$val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 2) {
                $result[$val['name_for_protocol']]['material_name'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol']]['material_id'] = $val['material_id'];
                $result[$val['name_for_protocol']]['nfp'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol']]['is_only_name_for_protocol'] = 1;

                if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['name_for_protocol']]['gosts'])) {
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                } else {
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                        'method_name' => $val['method_name'],
                        'gost_name' => $val['gost_name'],
                        'price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'sum' => $val['sum'],
                        'amount' => $val['amount'],
                    ];
                }

                $result[$val['name_for_protocol']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 3) {
                $result[$val['name_for_protocol'] . $val['material_id']]['material_name'] = $val['material_name'] . " ({$val['name_for_protocol']})";
                $result[$val['name_for_protocol'] . $val['material_id']]['material_id'] = $val['material_id'];
                $result[$val['name_for_protocol'] . $val['material_id']]['nfp'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = 1;

                if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['name_for_protocol'] . $val['material_id']]['gosts'])) {
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                } else {
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                        'method_name' => $val['method_name'],
                        'gost_name' => $val['gost_name'],
                        'price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'sum' => $val['sum'],
                        'amount' => $val['amount'],
                    ];
                }

                $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 4) {
                if (!$val['group_id']) {
                    $result[$val['material_id']]['material_name'] = $val['material_name'];
                    $result[$val['material_id']]['material_id'] = $val['material_id'];
                    $result[$val['material_id']]['nfp'] = $val['name_for_protocol'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['material_id']]['gosts'])) {
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                } else {
                    $result[$val['group_id']]['material_name'] = $val['material_name']; //  . " ({$val['group']})"
                    $result[$val['group_id']]['material_id'] = $val['material_id'];
                    $result[$val['group_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['group_id']]['group'] = $val['group_id'];
                    $result[$val['group_id']]['group_name'] = $val['group'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['group_id']]['gosts'])) {
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['group_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                }
            } else {
                if (!$val['group_id']) {
                    $result[$val['name_for_protocol'] . $val['material_id']]['material_name'] = $val['material_name'] . ($val['name_for_protocol'] ? " ({$val['name_for_protocol']})" : "");
                    $result[$val['name_for_protocol'] . $val['material_id']]['material_id'] = $val['material_id'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = 1;

                    if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['name_for_protocol'] . $val['material_id']]['gosts'])) {
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                } else {
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['material_name'] = $val['material_name'] . ($val['name_for_protocol'] ? " ({$val['name_for_protocol']})" : ""); //  . " ({$val['group']})"
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['material_id'] = $val['material_id'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['group'] = $val['group_id'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['group_name'] = $val['group'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'])) {
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                }
            }
        }


        $styleTable = array('borderSize' => 3, 'cellMarginLeft' => 0, 'cellMarginRight' => 0, 'borderColor' => '000000', 'leftFromText' => 0, 'rightFromText' => 0, 'bottomFromText' => 0);
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');

        //        $styleTable = array('alignment' => 'center', 'borderSize' => 5, 'borderColor' => '000000');
        //        $styleTable1 = array('alignment' => 'center', 'borderSize' => 5, 'borderColor' => '000000', 'width' => '100%', 'ident' => 0);
        //        $cellRowSpan = array('vMerge' => 'continue', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan2 = array('gridSpan' => 2, 'valign' => 'center');
        $cellColSpan3 = array('gridSpan' => 3, 'valign' => 'center');
        $cellColSpan4 = array('gridSpan' => 4, 'valign' => 'center');
        $cellColSpan6 = array('gridSpan' => 6, 'valign' => 'center');
        $cellColSpan9 = array('gridSpan' => 9, 'valign' => 'center');
        $cellColSpan8 = array('gridSpan' => 7, 'valign' => 'center');

        $cellHCentered = array('align' => 'center');
        $cellVCentered = array('valign' => 'center');
        $cellHRight = array('align' => 'right', 'valign' => 'center');
        $cellHLeft = array('align' => 'left');

        $width = 9250;
        $width1 = '100%';

        if ($info['nds']) {
            $count = 6;
            $spanCount = 6;
        } else {
            $count = 5;
            $spanCount = 5;
        }

        $section = $phpWord->addSection();

        $table = $section->addTable($styleTable);
        $table->addRow(null, array('tblHeader' => true));
        //        $table->addCell($columnWidth, $cellRowSpan)->addText('Наименование объекта испытаний (пробы/образца)', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        $table->addCell(self::centimeterSizeToTwips(4.74), $cellRowSpan)->addText('Определяемые характеристики', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        $table->addCell(self::centimeterSizeToTwips(2.5), $cellRowSpan)->addText('НД на метод испытания', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        $table->addCell(self::centimeterSizeToTwips(2.61), $cellRowSpan)->addText('НД на требования к объекту испытаний', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        $table->addCell(self::centimeterSizeToTwips(1.89), $cellRowSpan)->addText('Цена за пробу (образец), руб.', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        if ($info['nds']) {
            if ($info['nds_7']) {
                $table->addCell(self::centimeterSizeToTwips(2), $cellRowSpan)->addText('Цена за пробу (образец) с НДС (7%), руб.', array('size' => 9, 5, 'bold' => true), $cellHCentered);
            } else {
                $table->addCell(self::centimeterSizeToTwips(2), $cellRowSpan)->addText('Цена за пробу (образец) с НДС (5%), руб.', array('size' => 9, 5, 'bold' => true), $cellHCentered);
            }
        }
        $table->addCell(self::centimeterSizeToTwips(1.55), $cellRowSpan)->addText('Количество проб (образцов), шт.', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        $lastCellText = 'Стоимость за испытание';
        if ($info['nds']) {
            if ($info['nds_7']) {
                $lastCellText .= ' с учетом НДС (7%)';
            } else {
                $lastCellText .= ' с учетом НДС (5%)';
            }
        }
        $lastCellText .= ', руб.';

        $table->addCell(self::centimeterSizeToTwips(2.45), $cellRowSpan)->addText($lastCellText, array('size' => 9, 5, 'bold' => true), $cellHCentered);
        $i = 1;
        $sumTotal = 0;
        $mat_id = '';
        $group_id = '';
        $nfp_id = '';

        //			$this->pre($result);
        foreach ($result as $itemMater) {
            $table->addRow(600);
            $table->addCell('auto', $info['nds'] ? $cellColSpan8 : $cellColSpan6)->addText(strtoupper($itemMater['material_name']), array('size' => 10, 5, 'bold' => true), $cellHCentered);

            foreach ($itemMater['gosts'] as $k => $value) {

                $table->addRow();

                $table->addCell(self::centimeterSizeToTwips(4.74))->addText($value['method_name'], array('size' => 10), $cellHCentered);
                $table->addCell(self::centimeterSizeToTwips(2.5))->addText($value['gost_name'], array('size' => 10), $cellHCentered);
                $table->addCell(self::centimeterSizeToTwips(2.61))->addText($value['tech_condition'], array('size' => 10), $cellHCentered);
                $table->addCell(self::centimeterSizeToTwips(1.89))->addText(number_format($value['price'], 2, ',', ' '), array('size' => 10), $cellHCentered);
                if ($info['nds']) {
                    if ($info['nds_7']) {
                        $table->addCell(self::centimeterSizeToTwips(2))->addText(number_format($value['price'] * 1.07, 2, ',', ' '), array('size' => 10), $cellHCentered);
                    } else {
                        $table->addCell(self::centimeterSizeToTwips(2))->addText(number_format($value['price'] * 1.05, 2, ',', ' '), array('size' => 10), $cellHCentered);
                    }
                }
                $table->addCell(self::centimeterSizeToTwips(1.55))->addText($value['amount'], array('size' => 10), $cellHCentered);
                if ($info['nds']) {
                    if ($info['nds_7']) {
                        $table->addCell(self::centimeterSizeToTwips(2.45))->addText(number_format($value['sum'] * 1.07, 2, ',', ' '), array('size' => 10), $cellHCentered);
                    } else {
                        $table->addCell(self::centimeterSizeToTwips(2.45))->addText(number_format($value['sum'] * 1.05, 2, ',', ' '), array('size' => 10), $cellHCentered);
                    }
                } else {
                    $table->addCell(self::centimeterSizeToTwips(2.45))->addText(number_format($value['sum'], 2, ',', ' '), array('size' => 10), $cellHCentered);
                }
            }
        }

        $table->addRow(100);
        $table->addCell('auto', array('gridSpan' => $spanCount + 1, 'valign' => 'center', 'borderLeftSize' => 0, 'borderRightSize' => 0, 'borderBottomSize' => 0, 'borderColor' => 'FFFFFF'))->addText('', array('size' => 10), $cellHRight);

        if ($info['nds'] || $dealInfo['DISCOUNT']) {
            $table->addRow();
            $table->addCell('auto', array('gridSpan' => $spanCount, 'valign' => 'center', 'borderSize' => 0, 'borderColor' => 'FFFFFF'))->addText('Итого:', array('size' => 10), $cellHRight);
            $table->addCell('auto', array('valign' => 'center', 'borderSize' => 0, 'borderColor' => 'FFFFFF'))->addText(number_format((float)$dealInfo['PRICE_FL'], 2, ',', ' '), array('size' => 10), $cellHRight);
        }
        if ($dealInfo['DISCOUNT']) {
            $table->addRow();
            $table->addCell('auto', array('gridSpan' => $spanCount, 'valign' => 'center', 'borderSize' => 0, 'borderColor' => 'FFFFFF'))->addText("Итого с учетом скидки {$dealInfo['DISCOUNT']} %:", array('size' => 10), $cellHRight);
            $table->addCell('auto', array('valign' => 'center', 'borderSize' => 0, 'borderColor' => 'FFFFFF'))->addText(number_format((float)explode(' ', $dealInfo['price_ru'])[0], 2, ',', ' '), array('size' => 10), $cellHRight);
        }
        if ($info['nds']) {
            $table->addRow();

            if ($info['nds_7']) {
                $ndsPercent = '7%';
                $ndsValue = $dealInfo['price_discount'] * 0.07;
            } else {
                $ndsPercent = '5%';
                $ndsValue = $dealInfo['price_discount'] * 0.05;
            }

            $table->addCell('auto', array('gridSpan' => $spanCount, 'valign' => 'center', 'borderSize' => 0, 'borderColor' => 'FFFFFF'))->addText('НДС ' . $ndsPercent . ':', array('size' => 10), $cellHRight);
            $table->addCell('auto', array('valign' => 'center', 'borderSize' => 0, 'borderColor' => 'FFFFFF'))->addText(number_format($ndsValue, 2, ',', ' '), array('size' => 10), $cellHRight);
        }
        $table->addRow();
        $totalText = 'Всего к оплате с';
        if ($info['nds']) {
            if ($info['nds_7']) {
                $totalText .= ' НДС 7%:';
            } else {
                $totalText .= ' НДС 5%:';
            }
        }

        if ($info['nds']) {
            if ($info['nds_7']) {
                $totalAmount = number_format($dealInfo['price_discount'] * 1.07, 2, ',', ' ');
            } else {
                $totalAmount = number_format($dealInfo['price_discount'] * 1.05, 2, ',', ' ');
            }
        } else {
            $totalAmount = $dealInfo['price_discount'] . ' руб.';
        }

        $table->addCell('auto', array('gridSpan' => $spanCount, 'valign' => 'center', 'borderSize' => 0, 'borderColor' => 'FFFFFF'))->addText($totalText, array('size' => 10, 'bold' => true), $cellHRight);
        $table->addCell('auto', array('valign' => 'center', 'borderSize' => 0, 'borderColor' => 'FFFFFF'))->addText($totalAmount, array('size' => 10, 'bold' => true), $cellHRight);

        $this->generateDocumentWithTable($table, $info, $template_doc, $type);
    }

    /**
     * Формирование таблицы phpword для Приложения к договору
     * @param $info
     * @param $arrGost
     * @throws \NcJoes\OfficeConverter\OfficeConverterException
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function createTechnicalSpecificationDocument($info, $arrGost, $dealId, $dealInfo)
    {
        $template_doc = $_SERVER["DOCUMENT_ROOT"] . '/TechSpecification.docx';

        // SUDA!!!

        $type = 'tz';
        $result = [];

        $needSequence = false;
        $additionalPx = 2.49;

        //        $this->pre($dealInfo);

        foreach ($arrGost as $key => $val) {
            if ($dealInfo['group_type'] == 1 || !$dealInfo['group_type']) {
                $result[$val['material_id']]['material_name'] = $val['material_name'];
                $result[$val['material_id']]['material_id'] = $val['material_id'];
                $result[$val['material_id']]['nfp'] = $val['name_for_protocol'];

                if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['material_id']]['gosts'])) {
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                } else {
                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                        'method_name' => $val['method_name'],
                        'gost_name' => $val['gost_name'],
                        'price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'sum' => $val['sum'],
                        'amount' => $val['amount'],
                    ];
                }

                $result[$val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 2) {
                $result[$val['name_for_protocol']]['material_name'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol']]['material_id'] = $val['material_id'];
                $result[$val['name_for_protocol']]['nfp'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol']]['is_only_name_for_protocol'] = 1;

                if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['name_for_protocol']]['gosts'])) {
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                } else {
                    $result[$val['name_for_protocol']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                        'method_name' => $val['method_name'],
                        'gost_name' => $val['gost_name'],
                        'price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'sum' => $val['sum'],
                        'amount' => $val['amount'],
                    ];
                }

                $result[$val['name_for_protocol']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 3) {
                $result[$val['name_for_protocol'] . $val['material_id']]['material_name'] = $val['material_name'] . " ({$val['name_for_protocol']})";
                $result[$val['name_for_protocol'] . $val['material_id']]['material_id'] = $val['material_id'];
                $result[$val['name_for_protocol'] . $val['material_id']]['nfp'] = $val['name_for_protocol'];
                $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = 1;

                if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['name_for_protocol'] . $val['material_id']]['gosts'])) {
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                } else {
                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                        'method_name' => $val['method_name'],
                        'gost_name' => $val['gost_name'],
                        'price' => $val['price'],
                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                        'sum' => $val['sum'],
                        'amount' => $val['amount'],
                    ];
                }

                $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            } else if ($dealInfo['group_type'] == 4) {
                if (!$val['group_id']) {
                    $result[$val['material_id']]['material_name'] = $val['material_name'];
                    $result[$val['material_id']]['material_id'] = $val['material_id'];
                    $result[$val['material_id']]['nfp'] = $val['name_for_protocol'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['material_id']]['gosts'])) {
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                } else {
                    $result[$val['group_id']]['material_name'] = $val['material_name']; //  . " ({$val['group']})"
                    $result[$val['group_id']]['material_id'] = $val['material_id'];
                    $result[$val['group_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['group_id']]['group'] = $val['group_id'];
                    $result[$val['group_id']]['group_name'] = $val['group'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['group_id']]['gosts'])) {
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['group_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                }
            } else {
                if (!$val['group_id']) {
                    $result[$val['name_for_protocol'] . $val['material_id']]['material_name'] = $val['material_name'] . ($val['name_for_protocol'] ? " ({$val['name_for_protocol']})" : "");
                    $result[$val['name_for_protocol'] . $val['material_id']]['material_id'] = $val['material_id'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = 1;

                    if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['name_for_protocol'] . $val['material_id']]['gosts'])) {
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                } else {
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['material_name'] = $val['material_name'] . ($val['name_for_protocol'] ? " ({$val['name_for_protocol']})" : ""); //  . " ({$val['group']})"
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['material_id'] = $val['material_id'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['nfp'] = $val['name_for_protocol'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['group'] = $val['group_id'];
                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['group_name'] = $val['group'];

                    if (array_key_exists($val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition'], $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'])) {
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['amount'] += $val['amount'];
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']]['sum'] += $val['sum'];
                    } else {
                        $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price'] . '|' . $val['tech_condition']] = [
                            'method_name' => $val['method_name'],
                            'gost_name' => $val['gost_name'],
                            'price' => $val['price'],
                            'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
                            'sum' => $val['sum'],
                            'amount' => $val['amount'],
                        ];
                    }

                    $result[$val['name_for_protocol'] . $val['material_id'] . $val['group_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
                }
            }

            //            if (!empty($val['group_id'])) {
            //                $result[$val['group_id']]['material_name'] = $val['material_name'];
            //                $result[$val['group_id']]['material_id'] = $val['material_id'];
            //                $result[$val['group_id']]['nfp'] = $val['name_for_protocol'];
            //                $result[$val['group_id']]['group'] = $val['group_id'];
            //                $result[$val['group_id']]['group_name'] = $val['group'];
            //
            //                if(array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['group_id']]['gosts'])) {
            //                    $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['amount'] += $val['amount'];
            //                    $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['sum'] += $val['sum'];
            //                }else {
            //                    $result[$val['group_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
            //                        'method_name' => $val['method_name'],
            //                        'gost_name' => $val['gost_name'],
            //                        'price' => $val['price'],
            //                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
            //                        'sum' => $val['sum'],
            //                        'amount' => $val['amount'],
            //                    ];
            //                }
            //
            //                $result[$val['group_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            //            } else if($val['is_only_name_for_protocol'] == 1) {
            //                $result[$val['name_for_protocol'] . $val['material_id']]['material_name'] = $val['material_name'];
            //                $result[$val['name_for_protocol'] . $val['material_id']]['material_id'] = $val['material_id'];
            //                $result[$val['name_for_protocol'] . $val['material_id']]['nfp'] = $val['name_for_protocol'];
            //                $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = 1;
            //
            //                if(array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['name_for_protocol'] . $val['material_id']]['gosts'])) {
            //                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['amount'] += $val['amount'];
            //                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['sum'] += $val['sum'];
            //                }else {
            //                    $result[$val['name_for_protocol'] . $val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
            //                        'method_name' => $val['method_name'],
            //                        'gost_name' => $val['gost_name'],
            //                        'price' => $val['price'],
            //                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
            //                        'sum' => $val['sum'],
            //                        'amount' => $val['amount'],
            //                    ];
            //                }
            //
            //                $result[$val['name_for_protocol'] . $val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            //            }
            //            else {
            //                $result[$val['material_id']]['material_name'] = $val['material_name'];
            //                $result[$val['material_id']]['material_id'] = $val['material_id'];
            //                $result[$val['material_id']]['nfp'] = $val['name_for_protocol'];
            //
            //                if(array_key_exists($val['method_id'] . '|' . $val['price'], $result[$val['material_id']]['gosts'])) {
            //                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['amount'] += $val['amount'];
            //                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']]['sum'] += $val['sum'];
            //                }else {
            //                    $result[$val['material_id']]['gosts'][$val['method_id'] . '|' . $val['price']] = [
            //                        'method_name' => $val['method_name'],
            //                        'gost_name' => $val['gost_name'],
            //                        'price' => $val['price'],
            //                        'tech_condition' => $val['tech_condition'] ? $val['tech_condition'] : '-',
            //                        'sum' => $val['sum'],
            //                        'amount' => $val['amount'],
            //                    ];
            //                }
            //
            //                $result[$val['material_id']]['is_only_name_for_protocol'] = $val['is_only_name_for_protocol'];
            //            }
        }
        //		$this->pre($result);
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $template = new \PhpOffice\PhpWord\TemplateProcessor($template_doc);

        $styleTable = array('alignment' => 'center', 'borderSize' => 5, 'borderColor' => '000000', 'width' => '100%');
        $styleTable1 = array('alignment' => 'center', 'borderSize' => 5, 'borderColor' => '000000', 'width' => '100%', 'ident' => 0);
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan2 = array('gridSpan' => 2, 'valign' => 'center');
        $cellColSpan3 = array('gridSpan' => 3, 'valign' => 'center');
        $cellColSpan4 = array('gridSpan' => 4, 'valign' => 'center');
        $cellColSpan5 = array('gridSpan' => 5, 'valign' => 'center');
        $cellColSpan6 = array('gridSpan' => 6, 'valign' => 'center');
        $cellColSpan9 = array('gridSpan' => 9, 'valign' => 'center');
        $cellColSpan8 = array('gridSpan' => 8, 'valign' => 'center');
        $cellColSpan7 = array('gridSpan' => 7, 'valign' => 'center');

        $cellHCentered = array('align' => 'center');
        $cellHRight = array('align' => 'right', 'valign' => 'center');
        $cellHLeft = array('align' => 'left', 'valign' => 'center');
        $cellVCentered = array('valign' => 'center');

        $width = 10150;
        $width1 = '100%';

        $section = $phpWord->addSection();

        if ($info['nds']) {
            $count = 7;
            $spanCount = 6;
        } else {
            $count = 6;
            $spanCount = 5;
        }

        if (!$needSequence) {
            $count--;
            $spanCount--;
            $cellColSpan7['gridSpan'] -= 1;
            $cellColSpan8['gridSpan'] -= 1;
            $additionalPx += 2.25;
        }

        $columnWidth = $width / $count;
        $mat_id = '';
        $nfp_id = '';
        $group_id = '';

        //        $this->pre($dealID);

        $table = $section->addTable($styleTable);
        $table->addRow(null, array('tblHeader' => true));
        //        $table->addCell($width / $count, $cellRowSpan)->addText('Наименование объекта испытаний (пробы/образца)', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        $table->addCell(self::centimeterSizeToTwips($additionalPx), $cellRowSpan)->addText('Определяемые характеристики*', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        if ($needSequence) {
            $table->addCell(self::centimeterSizeToTwips(2.25), $cellRowSpan)->addText('Очередность выдачи протоколов', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        }
        $table->addCell(self::centimeterSizeToTwips(4), $cellRowSpan)->addText('Нормативный документ на метод испытания*', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        $table->addCell(self::centimeterSizeToTwips(1.25), $cellRowSpan)->addText('Количество проб (образцов), шт.*', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        $table->addCell(self::centimeterSizeToTwips(2.50), $cellRowSpan)->addText('Цена за пробу (образец, партию), руб.*', array('size' => 9, 5, 'bold' => true), $cellHCentered);
        if ($info['nds']) {
            if ($info['nds_7']) {
                $table->addCell(self::centimeterSizeToTwips(2.25), $cellRowSpan)->addText('Стоимость за пробу (образец, партию) с НДС (7%), руб.*', array('size' => 9, 5, 'bold' => true), $cellHCentered);
            } else {
                $table->addCell(self::centimeterSizeToTwips(2.25), $cellRowSpan)->addText('Стоимость за пробу (образец, партию) с НДС (5%), руб.*', array('size' => 9, 5, 'bold' => true), $cellHCentered);
            }
        }

        $costText = 'Стоимость за испытание';
        if ($info['nds']) {
            if ($info['nds_7']) {
                $costText .= ' с учетом НДС (7%)';
            } else {
                $costText .= ' с учетом НДС (5%)';
            }
        }
        $costText .= ', руб.';

        $table->addCell(self::centimeterSizeToTwips(2.5), $cellRowSpan)->addText($costText, array('size' => 9, 5, 'bold' => true), $cellHCentered);
        $i = 1;
        $sumTotal = 0;
        //		$this->pre($result);
        foreach ($result as $itemMater) {
            //			if ($itemMater['group']) {
            //				if ($group_id != $itemMater['group']) {
            //					if ($itemMater['is_only_name_for_protocol'] == 1) {
            //						$obj = $itemMater['nfp'];
            //					} else {
            //                        if($itemMater['nfp']) {
            //                            $obj = $itemMater['material_name'] . " ({$itemMater['nfp']})";
            //                        }else {
            //                            $obj = $itemMater['material_name'] . " ({$itemMater['nfp']})";
            //                        }
            //					}
            //					$table->addRow(600);
            //					$table->addCell($columnWidth, $info['nds'] ? $cellColSpan8 : $cellColSpan7)->addText(strtoupper($obj), array('size' => 10, 5, 'bold' => true), $cellHCentered);
            //					$group_id = $itemMater['group'];
            //				}
            //			} else if($itemMater['nfp']) {
            //                if ($nfp_id != $itemMater['nfp']) {
            //                    if ($itemMater['is_only_name_for_protocol'] == 1) {
            //                        $obj = $itemMater['nfp'];
            //                    } else {
            //                        if($itemMater['nfp']) {
            //                            $obj = $itemMater['material_name'] . " ({$itemMater['nfp']})";
            //                        }else {
            //                            $obj = $itemMater['material_name'] . " ({$itemMater['nfp']})";
            //                        }
            //                    }
            //                    $table->addRow(600);
            //                    $table->addCell($columnWidth, $info['nds'] ? $cellColSpan8 : $cellColSpan7)->addText(strtoupper($obj), array('size' => 10, 5, 'bold' => true), $cellHCentered);
            //                    $nfp_id = $itemMater['nfp'];
            //                }
            //            }
            //            else {
            //
            //				if ($mat_id != $itemMater['material_id']) {
            //					if ($itemMater['is_only_name_for_protocol'] == 1) {
            //						$obj = $itemMater['nfp'];
            //					} else {
            //                        if($itemMater['nfp']) {
            //                            $obj = $itemMater['material_name'] . " ({$itemMater['nfp']})";
            //                        }else {
            //                            $obj = $itemMater['material_name'] . " ({$itemMater['nfp']})";
            //                        }
            //					}
            //					$table->addRow(600);
            //					$table->addCell($columnWidth, $info['nds'] ? $cellColSpan8 : $cellColSpan7)->addText(strtoupper($obj), array('size' => 10, 5, 'bold' => true), $cellHCentered);
            //					$mat_id = $itemMater['material_id'];
            //				}
            //			}

            //            if ($itemMater['group']) {
            //                if ($group_id != $itemMater['group']) {
            //                    if ($itemMater['is_only_name_for_protocol'] == 1) {
            //                        $obj = $itemMater['nfp'];
            //                    } else {
            //                        $obj = $itemMater['material_name'];
            //                    }
            //                    $table->addRow(600);
            //                    $table->addCell('auto', $info['nds'] ? $cellColSpan8 : $cellColSpan6)->addText(strtoupper($obj), array('size' => 10, 5, 'bold' => true), $cellHCentered);
            //                    $group_id = $itemMater['group'];
            //                }
            //            } else if($itemMater['is_only_name_for_protocol'] == 1) {
            //                $obj = $itemMater['material_name'];
            //
            //                $table->addRow(600);
            //                $table->addCell('auto', $info['nds'] ? $cellColSpan8 : $cellColSpan6)->addText(strtoupper($obj), array('size' => 10, 5, 'bold' => true), $cellHCentered);
            //                $nfp_id = $itemMater['nfp'];
            //            }
            //            else {
            //                if ($mat_id != $itemMater['material_id']) {
            //                    if ($itemMater['is_only_name_for_protocol'] == 1) {
            //                        $obj = $itemMater['nfp'];
            //                    } else {
            //                        $obj = $itemMater['material_name'];
            //                    }
            //                    $table->addRow(600);
            //                    $table->addCell('auto', $info['nds'] ? $cellColSpan8 : $cellColSpan6)->addText(strtoupper($obj), array('size' => 10, 5, 'bold' => true), $cellHCentered);
            //                    $mat_id = $itemMater['material_id'];
            //                }
            //            }

            $table->addRow(600);
            $table->addCell('auto', $info['nds'] ? $cellColSpan7 : $cellColSpan5)->addText(strtoupper($itemMater['material_name']), array('size' => 10, 5, 'bold' => true), $cellHCentered);

            foreach ($itemMater['gosts'] as $k => $value) {
                $table->addRow();

                $table->addCell(self::centimeterSizeToTwips($additionalPx))->addText($value['method_name'], array('size' => 10), $cellHCentered);
                if ($needSequence) {
                    $table->addCell(self::centimeterSizeToTwips(2.25))->addText($value['sequence'], array('size' => 10), $cellHCentered);
                }
                $table->addCell(self::centimeterSizeToTwips(3))->addText($value['gost_name'], array('size' => 10), $cellHCentered);
                $table->addCell(self::centimeterSizeToTwips(1.25))->addText($value['amount'], array('size' => 10), $cellHCentered);
                $table->addCell(self::centimeterSizeToTwips(2.25))->addText(number_format($value['price'], 2, ',', ' '), array('size' => 10), $cellHCentered);
                if ($info['nds']) {
                    if ($info['nds_7']) {
                        $priceWithNds = $value['price'] * 1.07;
                    } else {
                        $priceWithNds = $value['price'] * 1.05;
                    }
                    $table->addCell(self::centimeterSizeToTwips(2.25))->addText(number_format($priceWithNds, 2, ',', ' '), array('size' => 10), $cellHCentered);
                }

                if ($info['nds']) {
                    if ($info['nds_7']) {
                        $sumWithNds = $value['sum'] * 1.07;
                    } else {
                        $sumWithNds = $value['sum'] * 1.05;
                    }
                } else {
                    $sumWithNds = $value['sum'];
                }

                $table->addCell(self::centimeterSizeToTwips(3.5))->addText(number_format($sumWithNds, 2, ',', ' '), array('size' => 10), $cellHCentered);
            }
        }

        if ($info['nds'] || $dealInfo['DISCOUNT']) {
            $table->addRow();
            $table->addCell('auto', array('gridSpan' => $spanCount, 'valign' => 'center'))->addText('Стоимость услуг (работ)', array('size' => 10), $cellHRight);
            $table->addCell('auto', array('valign' => 'center'))->addText(number_format((float)$dealInfo['PRICE_FL'], 2, ',', ' '), array('size' => 10), $cellHRight);
        }
        if ($dealInfo['DISCOUNT']) {
            $table->addRow();
            $table->addCell('auto', array('gridSpan' => $spanCount, 'valign' => 'center'))->addText("Стоимость услуг (работ) с учетом скидки {$dealInfo['DISCOUNT']} %", array('size' => 10), $cellHRight);
            $table->addCell('auto', array('valign' => 'center'))->addText(number_format((float)explode(' ', $dealInfo['price_ru'])[0], 2, ',', ' '), array('size' => 10), $cellHRight);
        }
        if ($info['nds']) {
            $table->addRow();

            if ($info['nds_7']) {
                $ndsPercent = '7%';
                $ndsAmount = $dealInfo['price_discount'] * 0.07;
            } else {
                $ndsPercent = '5%';
                $ndsAmount = $dealInfo['price_discount'] * 0.05;
            }

            $table->addCell('auto', array('gridSpan' => $spanCount, 'valign' => 'center'))->addText('Сумма НДС (' . $ndsPercent . ')', array('size' => 10), $cellHRight);
            $table->addCell('auto', array('valign' => 'center'))->addText(number_format($ndsAmount, 2, ',', ' '), array('size' => 10), $cellHRight);
        }

        $table->addRow();

        $totalText = 'Итоговая стоимость услуг(работ)';
        if ($info['nds']) {
            if ($info['nds_7']) {
                $totalText .= ' с учетом НДС (7%)';
                $totalAmount = number_format($dealInfo['price_discount'] * 1.07, 2, ',', ' ');
            } else {
                $totalText .= ' с учетом НДС (5%)';
                $totalAmount = number_format($dealInfo['price_discount'] * 1.05, 2, ',', ' ');
            }
        } else {
            $totalAmount = $dealInfo['price_discount'] . ' руб.';
        }

        $table->addCell('auto', array('gridSpan' => $spanCount, 'valign' => 'center'))->addText($totalText, array('size' => 10, 'bold' => true), $cellHRight);
        $table->addCell('auto', array('valign' => 'center'))->addText($totalAmount, array('size' => 10, 'bold' => true), $cellHRight);

        $table->addRow();
        $table->addCell('auto', array('gridSpan' => $spanCount + 1, 'valign' => 'center'))->addText('* - для заполнения Заказчиком', array('size' => 10), $cellHLeft);

        $this->generateDocumentWithTable($table, $info, $template_doc, $type);
    }

    /**
     * @param $table
     * @param $info
     * @param $templateDoc
     * @throws \NcJoes\OfficeConverter\OfficeConverterException
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function generateDocumentWithTable($table, $info, $templateDoc, $type)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $template = new \PhpOffice\PhpWord\TemplateProcessor($templateDoc);

        if ($type == 'dog') {
            $info['curDateEn'] = str_replace("СО", "SO", $info['curDateEn']);
        }
        $interimPath = "interim_archive_{$type}/" . $info['id'] . "/" . $info['curDateEn'] . ".docx";
        $interimPathPDF = "interim_archive_{$type}/" . $info['id'] . "/" . $info['curDateEn'] . ".PDF";
        $outputFile = $info['curDate'] . ".pdf";
        $outputPath = "archive_{$type}/" . $info['id'] . "/";



        $fullPathDocx = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $interimPath;
        $fullPathPDF = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $outputPath;

        $file = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $interimPath;
        $filePDF = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $interimPathPDF;

        $newDirectory = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/interim_archive_{$type}/" . $info['id'];

        if (!is_dir($newDirectory)) {
            mkdir($newDirectory, 0777, true);
        }

        $template->setValues($info);

        $template->setComplexBlock('table', $table);

        $template->saveAs($file);

        if ($type === 'kp') {
            $name_file = 'КП';
        } elseif ($type === 'tz') {
            $name_file = 'ТЗ';
        } elseif ($type === 'dog') {
            $name_file = 'CO';
        }

        $fullPathDocx = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $interimPath;
        $fullPathPDF = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/' . $outputPath;

        if (!is_dir($fullPathPDF)) {
            mkdir($fullPathPDF, 0777, true);
        }

        $converter = new OfficeConverter($fullPathDocx, $fullPathPDF);
        $converter->convertTo($outputFile);

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $name_file . ' №' . $info['id'] . ' от ' . date('d.m.Y', strtotime($info['date'])) . '.docx"');
        readfile($file);
    }

    /**
     * @param $qrPath
     * @param $pathDoc
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function addQrCode($qrPath, $pathDoc)
    {
        $newTemplate = new \PhpOffice\PhpWord\Template($pathDoc);
        $newTemplate->setImageValue('image2.png', $qrPath);
        $newTemplate->saveAs($pathDoc); // Сохранение документа
    }

    /**
     * @param $dealID
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function actSampleGenerator($dealID)
    {
        $companyModel = new Company();
        $dealModel = new Request();
        $orderModel = new Order();

        $fileDoc = $_SERVER['DOCUMENT_ROOT'] . '/Act1.docx';
        $signPath = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/archive_client/{$dealID}/sign.png";
        $pathDoc = $_SERVER['DOCUMENT_ROOT'] . '/Act.docx';
        $newTemplate = new \PhpOffice\PhpWord\Template($pathDoc);
        $newTemplate->setImageValue('image1.png', $signPath);
        $newTemplate->saveAs($fileDoc);


        $fileDocx = new Bitrix\Main\IO\File($fileDoc);
        $document = new Bitrix\DocumentGenerator\Body\Docx($fileDocx->getContents());
        $document->normalizeContent();

        $actBase = $this->DB->Query("SELECT * FROM `ACT_BASE` WHERE `ID_Z`= {$dealID}")->Fetch();

        $company = $companyModel->getRequisiteByDealId($dealID);
        $deal = $dealModel->getDealById($dealID);

        $deliveryman = $actBase['deliveryman'];
        $zakaz_man = $deal['COMPANY_TITLE'];
        $num_request = explode(' ', $deal['TITLE'])[1];
        $dogovor = $orderModel->getOrderByDealId($dealID);
        $date = $actBase['ACT_DATE'] ? date("d.m.Y", strtotime($actBase['ACT_DATE'])) : date("d.m.Y");

        if ($dealID < DEAL_START_NEW_AREA) {
            $probe_arr = [];
            $probeinf = $this->DB->Query("SELECT mtr.`NAME_MATERIAL`, mtr.`ID_MATERIAL`, mtr.`OBIEM`, ptm.`cipher`, 
										(SELECT GROUP_CONCAT(`SPECIFICATION` SEPARATOR ', ') FROM `ba_gost` bg, `gost_to_probe` gtp 
										WHERE bg.`ID` = gtp.`gost_method` AND gtp.`probe_id` = ptm.`ID`) gtp 
										FROM `MATERIALS_TO_REQUESTS` mtr, `probe_to_materials` ptm 
										WHERE mtr.`ID` = ptm.`material_request_id` AND `ID_DEAL`={$dealID}");

            while ($arrProbe = $probeinf->Fetch()) {
                $probe_arr[] = [
                    'Number' => $arrProbe['cipher'],
                    'Name' => $arrProbe['NAME_MATERIAL'],
                ];
                $gosts_str[] = $arrProbe['gtp'];
            }
            $gostStr = implode(', ', $gosts_str);
        } else {
            $probeinf = $this->DB->Query("SELECT m.`NAME`, umtr.`material_id`, umtr.`cipher`, um.name, um.`is_selection`
										FROM `ulab_material_to_request` umtr
										LEFT JOIN `ulab_gost_to_probe` ugtp ON umtr.`id` = ugtp.`material_to_request_id`																				
										LEFT JOIN `ulab_methods` um ON ugtp.`new_method_id` = um.`id`																				
										LEFT JOIN `MATERIALS` m ON umtr.`material_id` = m.`ID`
										WHERE umtr.`deal_id`={$dealID}");

            while ($arrProbe = $probeinf->Fetch()) {
                $probe_arr[$arrProbe['cipher']] = [
                    'Number' => $arrProbe['cipher'],
                    'Name' => $arrProbe['NAME'],
                ];
                if ($arrProbe['is_selection'] == 0) {
                    continue;
                }
                $gosts_str[] = $arrProbe['name'];
            }

            $gostStr = implode(', ', array_unique($gosts_str));
        }

        $document->setValues([
            'Number' => $actBase['ACT_NUM'],
            'DogovorNum' => $dogovor,
            'DatePost' => $date,
            'ZakazMan' => $zakaz_man,
            'deliveryman' => $deliveryman,
            'mesto' => $actBase['PLACE_PROBE'],
            'dateProbe' => !empty($actBase['DATE_PROBE']) ? ' от ' . date('d.m.Y', strtotime($actBase['DATE_PROBE'])) : '',
            'probe_proizv' => $actBase['PROBE_PROIZV'],
            'SPECIFICATION' => !empty($gostStr) ? 'Определяемые показатели: ' . $gostStr : '',
            //			'DESCRIPTION' => !empty($sql['DESCRIPTION']) ? $sql['DESCRIPTION'] : '',
            //	'QUARRY' => !empty($sql['QUARRY_ID']) ? 'Карьер: ' . $quarList['NAME'] : '',
            //--'Вставка №Заявки в шаблоны' начало--//
            'num_request' => !empty($num_request) ? 'по заявке ' . $num_request : '',
            'Probe' => new Bitrix\DocumentGenerator\DataProvider\ArrayDataProvider(
                $probe_arr,
                [
                    'ITEM_NAME' => 'Item',
                    'ITEM_PROVIDER' => Bitrix\DocumentGenerator\DataProvider\HashDataProvider::class,
                ]
            ),
            'ProbeItemName' => 'Probe.Item.Name',
            'ProbeItemNumber' => 'Probe.Item.Number',
        ]);

        $result = $document->process();
        $content = $document->getContent();

        // вывод непосредственно в браузер
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header("Content-Disposition: attachment;filename=\"Акт приемки проб {$actBase['ACT_NUM']} от {$date}.docx\"");
        header('Cache-Control: max-age=0');
        echo $content;
    }

    /**
     * @param $dealID
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function actSampleGeneratorNew($dealID)
    {
        $companyModel = new Company();
        $dealModel = new Request();
        $orderModel = new Order();
        $userModel = new User();

        $fileDoc = $_SERVER['DOCUMENT_ROOT'] . '/ActNew.docx';
        $signPath = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/archive_client/{$dealID}/sign.png";
        $signAct = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/archive_client/{$dealID}/Act.docx";

        $pathDoc = $_SERVER['DOCUMENT_ROOT'] . '/ActNew.docx';
        $newTemplate = new \PhpOffice\PhpWord\Template($pathDoc);
        if (file_exists($signPath)) {
            $newTemplate->setImageValue('image1.png', $signPath);
            $newTemplate->saveAs($signAct);
            $fileDoc = $signAct;
        }

        $fileDocx = new Bitrix\Main\IO\File($fileDoc);
        $document = new Bitrix\DocumentGenerator\Body\Docx($fileDocx->getContents());
        $document->normalizeContent();

        $actBase = $this->DB->Query("SELECT * FROM `ACT_BASE` WHERE `ID_Z`= {$dealID}")->Fetch();

        $company = $companyModel->getRequisiteByDealId($dealID);
        $deal = $dealModel->getDealById($dealID);

        $deliveryman = $actBase['deliveryman'];
        $zakaz_man = $deal['COMPANY_TITLE'];
        $num_request = explode(' ', $deal['TITLE'])[1];
        $dogovor = $orderModel->getOrderByDealId($dealID);
        $createrArr = $userModel->getUserShortById($actBase['creater']);
        $createrStr = $createrArr['work_position'] . ' ' . $createrArr['short_name'];

        $date = $actBase['ACT_DATE'] ? date("d.m.Y", strtotime($actBase['ACT_DATE'])) : date("d.m.Y");

        $probeinf = $this->DB->Query("SELECT m.`NAME`, umtr.`material_id`, umtr.`cipher`, umtr.`name_for_protocol`,
       									umtr.`group`, um.name, mg.name as group_name
										FROM `ulab_material_to_request` umtr
										LEFT JOIN `ulab_gost_to_probe` ugtp ON umtr.`id` = ugtp.`material_to_request_id`																				
										LEFT JOIN `materials_groups` mg ON umtr.`group` = mg.`id`																				
										LEFT JOIN `ulab_methods` um ON ugtp.`new_method_id` = um.`id`																				
										LEFT JOIN `MATERIALS` m ON umtr.`material_id` = m.`ID`
										WHERE umtr.`deal_id`={$dealID} and umtr.in_act = 1
										ORDER BY umtr.material_number, umtr.id ");

        while ($arrProbe = $probeinf->Fetch()) {
            $probe_arr[$arrProbe['cipher']] = [
                'Number' => $arrProbe['cipher'],
                'Name' => $arrProbe['NAME'] . ' ' . $arrProbe['group_name'] ?? '',
                'Marker' => $arrProbe['name_for_protocol'],
            ];
            $gosts_str[] = $arrProbe['name'];
        }

        $gostStr = implode(', ', array_unique($gosts_str));

        $document->setValues([
            'Number' => $actBase['ACT_NUM'],
            'DogovorNum' => $dogovor,
            'DatePost' => $date,
            'ZakazMan' => $zakaz_man,
            'deliveryman' => $deliveryman,
            'mesto' => $actBase['PLACE_PROBE'],
            'dateProbe' => !empty($actBase['DATE_PROBE']) ? ' от ' . date('d.m.Y', strtotime($actBase['DATE_PROBE'])) : '',
            'probe_proizv' => $actBase['PROBE_PROIZV'],
            'SPECIFICATION' => !empty($gostStr) ? 'Определяемые показатели: ' . $gostStr : '',
            'creater' => $createrStr,
            //			'DESCRIPTION' => !empty($sql['DESCRIPTION']) ? $sql['DESCRIPTION'] : '',
            //	'QUARRY' => !empty($sql['QUARRY_ID']) ? 'Карьер: ' . $quarList['NAME'] : '',
            //--'Вставка №Заявки в шаблоны' начало--//
            'num_request' => !empty($num_request) ? 'по заявке ' . $num_request : '',
            'Probe' => new Bitrix\DocumentGenerator\DataProvider\ArrayDataProvider(
                $probe_arr,
                [
                    'ITEM_NAME' => 'Item',
                    'ITEM_PROVIDER' => Bitrix\DocumentGenerator\DataProvider\HashDataProvider::class,
                ]
            ),
            'ProbeItemName' => 'Probe.Item.Name',
            'ProbeItemNumber' => 'Probe.Item.Number',
            'ProbeItemMarker' => 'Probe.Item.Marker',
        ]);

        $result = $document->process();
        $content = $document->getContent();

        // вывод непосредственно в браузер
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header("Content-Disposition: attachment;filename=\"Акт приемки проб {$actBase['ACT_NUM']} от {$date}.docx\"");
        header('Cache-Control: max-age=0');
        echo $content;
    }

    /**
     * @param $dealID
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public function actSampleGeneratorNewWithAct($actId, $dealID)
    {
        $companyModel = new Company();
        $dealModel = new Request();
        $orderModel = new Order();
        $userModel = new User();

        $fileDoc = $_SERVER['DOCUMENT_ROOT'] . '/ActNew.docx';
        $signPath = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/archive_client/{$dealID}/{$actId}/sign.png";
        $signAct = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/archive_client/{$dealID}/{$actId}/Act.docx";

        $pathDoc = $_SERVER['DOCUMENT_ROOT'] . '/ActNew.docx';
        $newTemplate = new \PhpOffice\PhpWord\Template($pathDoc);
        if (file_exists($signPath)) {
            $newTemplate->setImageValue('image1.png', $signPath);
            $newTemplate->saveAs($signAct);
            $fileDoc = $signAct;
        }

        $fileDocx = new Bitrix\Main\IO\File($fileDoc);
        $document = new Bitrix\DocumentGenerator\Body\Docx($fileDocx->getContents());
        $document->normalizeContent();

        $actBase = $this->DB->Query("SELECT * FROM `ACT_BASE` WHERE `ID`= {$actId}")->Fetch();

        $company = $companyModel->getRequisiteByDealId($dealID);
        $deal = $dealModel->getDealById($dealID);

        $deliveryman = $actBase['deliveryman'];
        $zakaz_man = $deal['COMPANY_TITLE'];
        $num_request = explode(' ', $deal['TITLE'])[1];
        $dogovor = $orderModel->getOrderByDealId($dealID);
        $createrArr = $userModel->getUserShortById($actBase['creater']);
        $createrStr = $createrArr['work_position'] . ' ' . $createrArr['short_name'];

        $date = $actBase['ACT_DATE'] ? date("d.m.Y", strtotime($actBase['ACT_DATE'])) : date("d.m.Y");

        $probeinf = $this->DB->Query("SELECT m.`NAME`, umtr.`material_id`, umtr.`cipher`, umtr.`name_for_protocol`,
       									umtr.`group`, um.name, mg.name as group_name
										FROM `ulab_material_to_request` umtr
										LEFT JOIN `ulab_gost_to_probe` ugtp ON umtr.`id` = ugtp.`material_to_request_id`																				
										LEFT JOIN `materials_groups` mg ON umtr.`group` = mg.`id`																				
										LEFT JOIN `ulab_methods` um ON ugtp.`new_method_id` = um.`id`																				
										LEFT JOIN `MATERIALS` m ON umtr.`material_id` = m.`ID`
										WHERE umtr.`deal_id`={$dealID} and umtr.in_act = 1 and umtr.cipher LIKE '{$actBase['ACT_NUM']}.%'
										ORDER BY umtr.material_number, umtr.id ");

        while ($arrProbe = $probeinf->Fetch()) {
            $probe_arr[$arrProbe['cipher']] = [
                'Number' => $arrProbe['cipher'],
                'Name' => $arrProbe['NAME'] . ' ' . $arrProbe['group_name'] ?? '',
                'Marker' => $arrProbe['name_for_protocol'],
            ];
            $gosts_str[] = $arrProbe['name'];
        }

        $gostStr = implode(', ', array_unique($gosts_str));

        $document->setValues([
            'Number' => $actBase['ACT_NUM'],
            'DogovorNum' => $dogovor,
            'DatePost' => $date,
            'ZakazMan' => $zakaz_man,
            'deliveryman' => $deliveryman,
            'mesto' => $actBase['PLACE_PROBE'],
            'dateProbe' => !empty($actBase['DATE_PROBE']) ? ' от ' . date('d.m.Y', strtotime($actBase['DATE_PROBE'])) : '',
            'probe_proizv' => $actBase['PROBE_PROIZV'],
            'SPECIFICATION' => !empty($gostStr) ? 'Определяемые показатели: ' . $gostStr : '',
            'creater' => $createrStr,
            //			'DESCRIPTION' => !empty($sql['DESCRIPTION']) ? $sql['DESCRIPTION'] : '',
            //	'QUARRY' => !empty($sql['QUARRY_ID']) ? 'Карьер: ' . $quarList['NAME'] : '',
            //--'Вставка №Заявки в шаблоны' начало--//
            'num_request' => !empty($num_request) ? 'по заявке ' . $num_request : '',
            'Probe' => new Bitrix\DocumentGenerator\DataProvider\ArrayDataProvider(
                $probe_arr,
                [
                    'ITEM_NAME' => 'Item',
                    'ITEM_PROVIDER' => Bitrix\DocumentGenerator\DataProvider\HashDataProvider::class,
                ]
            ),
            'ProbeItemName' => 'Probe.Item.Name',
            'ProbeItemNumber' => 'Probe.Item.Number',
            'ProbeItemMarker' => 'Probe.Item.Marker',
        ]);

        $result = $document->process();
        $content = $document->getContent();

        // вывод непосредственно в браузер
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header("Content-Disposition: attachment;filename=\"Акт приемки проб {$actBase['ACT_NUM']} от {$date}.docx\"");
        header('Cache-Control: max-age=0');
        echo $content;
    }

    /**
     * @param $year
     * @param $type
     * @param null $oa
     * @param null $month
     */
    public function VerificationGraph($year, $type, $oa = null, $month = null)
    {
        $oborudModel = new Oborud();
        //		$spreadsheet = new Spreadsheet();


        switch ($type) {
            case '1':
                $type_control = 'POVERKA';
                $type_oborud = 'SI';
                $doc_name = "План графика поверки, калибровки СИ за {$year} год.docx";
                $pathDoc = $_SERVER['DOCUMENT_ROOT'] . '/poverkaSINew.docx';
                $fileDoc = $_SERVER['DOCUMENT_ROOT'] . '/poverkaSINewTest.docx';
                break;
            case '2':
                $type_control = 'TECH_CHAR';
                $type_oborud = 'VO';
                $doc_name = "План графика проверки ВО за {$year}.docx";
                $pathDoc = $_SERVER['DOCUMENT_ROOT'] . '/poverkaVONew.docx';
                $fileDoc = $_SERVER['DOCUMENT_ROOT'] . '/poverkaVONewTest.docx';
                break;
            case '3':
                $type_control = 'ATTESTATION';
                $type_oborud = 'IO';
                $doc_name = "План графика атестации ИО за {$year}.docx";
                $pathDoc = $_SERVER['DOCUMENT_ROOT'] . '/attestatIONew.docx';
                $fileDoc = $_SERVER['DOCUMENT_ROOT'] . '/attestatIONewTest.docx';
                break;
            case '4':
                $type_control = 'TECH_CHAR';
                $type_oborud = 'TS';
                $doc_name = 'Проведения измерений значений нормированных параметров технических средств (вспомогательное оборудование)';
                break;
        }

        $where = '';

        if ($oa) {
            $where .= "bo.IN_AREA = 1 and ";
        }

        if ($month) {
            $where .= "(boc.date_end - interval 2 month) <= NOW() and ";
        }

        $where .= 1;

        $newTemplate = new \PhpOffice\PhpWord\Template($pathDoc);

        $result = [];
        $k = 1;

        $res = $this->DB->Query("SELECT bo.*, r.NAME as rName, r.Number as rNumber, 
       						boc.date_end as pLast, boc.date_start as pNow FROM `ba_oborud` as bo 
							left join ROOMS as r on bo.roomnumber = r.ID
							left join ba_oborud_certificate as boc on boc.oborud_id = bo.ID and boc.is_actual = 1
							where `IDENT`='{$type_oborud}' AND `METR_CONTROL`='{$type_control}'
                            AND `roomnumber` != '9' AND `roomnumber` != '10' AND `roomnumber` != '11'
                            AND LONG_STORAGE = 0 AND is_decommissioned = 0 and NO_METR_CONTROL != 1
                            /*AND `is_vagon` != 1*/ and year(boc.date_end/* - interval 2 month*/) = {$year} 
							and {$where}
							order by boc.date_end");


        while ($row = $res->fetch()) {
            $result[] = [
                'num' => $k,
                'object' => $row['OBJECT'],
                'type_oborud' => $row['TYPE_OBORUD'],
                'gosreestr' => $row['GOSREESTR'],
                'reg_num' => $row['REG_NUM'],
                'factory_number' => $row['FACTORY_NUMBER'],
                'year' => $row['YEAR'],
                'class' => $row['сlass_precision_and_accuracy'],
                'measuring_range' => $row['measuring_range'],
                'mc_interval' => $row['MC_INTERVAL'],
                'poverka_last' => date('d.m.Y', strtotime($row['pNow'])),
                'poverka_place' => $row['POVERKA_PLACE'],
                'poverka' => date("d.m.Y", strtotime($row['pLast'])),
                'place' => "{$row['rName']} {$row['rNumber']}"
            ];
            $k++;
        }

        $newTemplate->cloneRowAndSetValues('num', $result);
        $newTemplate->setValue('year_graph', $year);
        $newTemplate->saveAs($fileDoc);

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $doc_name . '"');
        readfile($fileDoc);
    }

    /**
     * @param $year
     * @param $type
     * @param null $oa
     * @param null $month
     */
    public function InventoryList($inform, $oa = '0')
    {
        $oborudModel = new Oborud();
        //		$spreadsheet = new Spreadsheet();
        $pathDoc = $_SERVER['DOCUMENT_ROOT'] . '/testBD/inventoryList.docx';
        $fileDoc = $_SERVER['DOCUMENT_ROOT'] . '/testBD/inventoryListDoc.docx';


        $styleTable = array('borderSize' => 3, 'cellMarginLeft' => 0, 'cellMarginRight' => 0, 'borderColor' => '000000', 'leftFromText' => 0, 'rightFromText' => 0, 'bottomFromText' => 0);
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan2 = array('gridSpan' => 2, 'valign' => 'center');
        $cellColSpan3 = array('gridSpan' => 3, 'valign' => 'center');
        $cellColSpan4 = array('gridSpan' => 4, 'valign' => 'center');
        $cellColSpan6 = array('gridSpan' => 6, 'valign' => 'center');
        $cellColSpan5 = array('gridSpan' => 5, 'valign' => 'center');
        $cellColSpan8 = array('gridSpan' => 8, 'valign' => 'center');
        $cellColSpan9 = array('gridSpan' => 9, 'valign' => 'center');
        $FontStyle = [
            'size' => 10,
            'name' => 'Times New Roman'
        ];
        $FontStyleTitle = [
            'size' => 10,
            'name' => 'Times New Roman',
            'bold' => true
        ];
        $paragraphStyle = [
            'spaceAfter' => 0,
            'spaceBefore' => 0,
            'space' => ['after' => 0],
            'spacing' => 10,
            'lineHeight' => 1,
            'align' => 'center',
            'indentation' => ['left' => 0, 'right' => 0]
        ];
        $paragraphStyleText = [
            'indentation' => ['left' => 700]
        ];
        $paragraphStyleText1 = [
            'indentation' => ['left' => 825]
        ];

        $cellHCentered = array('align' => 'center');
        $cellAllCentered = array('align' => 'center', 'valign' => 'center');
        $cellVCentered = array('valign' => 'center');

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $template = new \PhpOffice\PhpWord\Template($pathDoc);

        $result = [];
        $k = 1;

        $info = [
            'date_inv' => date('d.m.Y', strtotime($inform['dateInv'])),
            'dateStart' => date('d.m.Y', strtotime($inform['dateInvStart'])),
            'directive_date' => date('d.m.Y', strtotime($inform['directive_date'])),
            'directive' => $inform['directive'],
            'date' => date('d.m.Y')
        ];

        $in_oa = $oa == 1 ? 'and bo.IN_AREA = 1' : '';


        $res = $this->DB->Query("SELECT bo.id, bo.NAME, bo.IDENT, FACTORY_NUMBER, REG_NUM, OBJECT, TYPE_OBORUD, place_of_installation_or_storage as lab_id, 
										ID_ASSIGN1, bl.short_name,concat(u.LAST_NAME, ' ', left(u.NAME, 1), '.', left(u.SECOND_NAME, 1)) as assigna, u.WORK_POSITION,
										bo.roomnumber,
                                        r.NAME as room_name,
                                        r.NUMBER as room_number
       									from ba_oborud as bo
										left join ba_laba as bl on bo.place_of_installation_or_storage = bl.ID
										left join b_uts_iblock_5_section as lab on lab.VALUE_ID = bl.id_dep
										left join b_user as u on lab.UF_HEAD = u.ID
       									left join ROOMS as r on r.ID = bo.roomnumber
										where `IDENT` IN ('SI','VO','IO') and (SPISANIE is null or SPISANIE = '') and
										LONG_STORAGE = 0 and bo.place_of_installation_or_storage != '' {$in_oa} 
										and bo.is_vagon = 0
 										ORDER BY `place_of_installation_or_storage`");
        $i = 1;
        while ($row = $res->fetch()) {
            $result[$row['lab_id']]['lab_name'] = $row['short_name'];
            $result[$row['lab_id']]['boss_name'] = $row['assigna'];
            $result[$row['lab_id']]['boss_position'] = $row['WORK_POSITION'];
            $result[$row['lab_id']][] = $row;
        }
        //		$this->pre($result);
        $styleTable = array('alignment' => 'center', 'borderSize' => 5, 'borderColor' => '000000', 'width' => '100%');
        $section = $phpWord->addSection();
        $table_oborud = $section->addTable($styleTable);

        $table_oborud->addRow(null, array('tblHeader' => true));
        $table_oborud->addCell(self::percentToTwips(5), $cellAllCentered)->addText('№п/п', array('bold' => true, 'size' => 12), $cellHCentered);
        $table_oborud->addCell(self::percentToTwips(40), $cellAllCentered)->addText('Наименование', array('bold' => true, 'size' => 12), $cellHCentered);
        $table_oborud->addCell(self::percentToTwips(15), $cellAllCentered)->addText('Зав. №', array('bold' => true, 'size' => 12), $cellHCentered);
        $table_oborud->addCell(self::percentToTwips(15), $cellAllCentered)->addText('Инв.№', array('bold' => true, 'size' => 12), $cellHCentered);
        $table_oborud->addCell(self::percentToTwips(25), $cellAllCentered)->addText('Место установки или хранения', array('bold' => true, 'size' => 12), $cellHCentered);

        foreach ($result as $val) {
            $table_oborud->addRow();
            $table_oborud->addCell(null, $cellColSpan5)->addText($val['lab_name'], array('bold' => true, 'size' => 11), $cellHCentered);
            foreach ($val as $key => $oborud) {
                if (in_array($key, ['lab_name', 'boss_name', 'boss_position'])) {
                    continue;
                }

                $roomText = trim((string)($oborud['room_name'] ?? '') . ' ' . (string)($oborud['room_number'] ?? ''));
                if ($roomText === '') {
                    $roomText = '';
                }

                $table_oborud->addRow();
                $table_oborud->addCell(self::percentToTwips(5))->addText($i, array('size' => 11), $cellHCentered);
                $table_oborud->addCell(self::percentToTwips(40))->addText("{$oborud['OBJECT']} {$oborud['TYPE_OBORUD']}", array('size' => 11));
                $table_oborud->addCell(self::percentToTwips(15))->addText($oborud['FACTORY_NUMBER'], array('size' => 11), $cellHCentered);
                $table_oborud->addCell(self::percentToTwips(15))->addText($oborud['REG_NUM'], array('size' => 11), $cellHCentered);
                $table_oborud->addCell(self::percentToTwips(25))->addText($roomText, array('size' => 11), $cellHCentered);
                $i++;
            }
            $table_oborud->addRow();
            $table_oborud->addCell(null, $cellColSpan5)->addText("Материально ответственное лицо: {$val['boss_position']} <w:br/>
			<w:br/>{$val['boss_name']}", array('bold' => true, 'size' => 11));
        }

        $countStr = StringHelper::numberToRussian($i - 1);

        $info['countStr'] = $countStr;

        $template->setComplexBlock('table_oborud', $table_oborud);
        $template->setValues($info);

        $template->saveAs($fileDoc);

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename=Инвентарная ведомость.docx');
        readfile($fileDoc);
    }

    /**
     * @param $protocolId
     * @return array
     */
    public function sigProtocol($protocolId)
    {
        $protocolModel = new Protocol();
        $userModel = new User();

        $userId = $_SESSION['SESS_AUTH']['USER_ID'];

        $protocolInfo = $protocolModel->getProtocolById($protocolId);
        $userInfo = $userModel->getUserShortById($userId);

        if (empty($protocolInfo)) {
            return [
                'success' => false,
                'error' => 'Не удалось получить данные о протоколе.'
            ];
        }

        //		$yearProtocol = substr($protocolInformation['protocolYear'],-2);
        //		$numDeal = str_replace('/', '.', $protocolInformation['dealNum']);
        //		$att = !empty($protocolInformation['Attestat']) ? ' С' : '';
        //		$nameProtocol = "ПИ {$protocolInformation['number']}.{$yearProtocol}{$att} {$numDeal}";

        $qrPath = $protocolInfo['full_protocol_path'] . 'qrNEW.png';
        QRcode::png("https://niistrom.pro/check/index.php?NUMP=" . $protocolInfo['NUMBER'] . "&DATE=" . $protocolInfo['DATE'], $qrPath);
        $pathDoc = $protocolInfo['full_protocol_path'] . 'forsign.docx';

        try {
            $template = new \PhpOffice\PhpWord\TemplateProcessor($pathDoc);
            $template->setValue('work_position', $userInfo['work_position']);
            $template->setValue('header', $userInfo['short_name']);
            $template->setValue('date_sign', date("d.m.Y", strtotime($protocolInfo['DATE'])));
            $template->saveAs($pathDoc);
            if ($_SESSION['SESS_AUTH']['USER_ID'] == 15) {
                $pathFile2 = $_SERVER['DOCUMENT_ROOT'] . "/sign_15_new.png";
            } else {
                $pathFile2 = $_SERVER['DOCUMENT_ROOT'] . "/sign_{$_SESSION['SESS_AUTH']['USER_ID']}.png";
            }
            $newTemplate = new \PhpOffice\PhpWord\Template($pathDoc);
            $newTemplate->setImageValue('image2.png', $qrPath);
            $newTemplate->setImageValue('image3.jpg', $pathFile2);
            $newTemplate->setImageValue('image3.png', $pathFile2);

            $newTemplate->saveAs($protocolInfo['full_protocol_path'] . 'signed.docx'); // Сохранение документа

            // Сохранение в PDF
            $command = "libreoffice --headless --convert-to pdf --outdir " . escapeshellarg($protocolInfo['full_protocol_path']) . " " . escapeshellarg($protocolInfo['full_protocol_path'] . 'signed.docx');

            // Выполнение команды
            exec($command, $output, $returnCode);

            $originalFilename = pathinfo($protocolInfo['full_protocol_path'] . 'signed.docx', PATHINFO_FILENAME);
            $tempPdfPath = $protocolInfo['full_protocol_path'] . DIRECTORY_SEPARATOR . $originalFilename . '.pdf';

            // Желаемое имя конечного файла (без расширения .pdf)

            $finalPdfPath = $protocolInfo['full_protocol_path'] . DIRECTORY_SEPARATOR . $protocolInfo['pdf_name'];

            // Переименовываем файл
            if (file_exists($tempPdfPath)) {
                rename($tempPdfPath, $finalPdfPath);
            }

            //            $converter = new  OfficeConverter($protocolInfo['full_protocol_path'] . 'signed.docx', $protocolInfo['full_protocol_path']);
            //            $converter->convertTo($protocolInfo['pdf_name']); // генерирует pdf файл в том же каталоге
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Не удалось изменить документ: ' . $e->getMessage()
            ];
        }

        $base64 = $protocolModel->getBase64EncodeFile($protocolInfo['full_protocol_path'], $protocolInfo['pdf_name']);

        if (empty($base64)) {
            return [
                'success' => false,
                'error' => 'Не удалось получить base64.'
            ];
        }

        return [
            'success' => true,
            'file_base64' => $base64,
            'url_file' => PROTOCOL_GENERATOR_URL . $protocolInfo['protocol_path'] . $protocolInfo['pdf_name'],
            'pdf_file_name' => $protocolInfo['pdf_name'],
        ];
    }

    /**
     *
     */
    public function getPriceList()
    {

        $methodModel = new Methods();
        $lab = $_POST['lab'];
        $stage = $_POST['stage'];
        if (!empty($_POST['stage'])) {
            $filter['search']['stage'] = $_POST['stage'];
        }
        if (!empty($_POST['lab'])) {
            $filter['search']['lab'] = $_POST['lab'];
        }
        $filter['order']['by']  = 'gost_id';
        $filter['order']['dir'] = 'ASC';

        $data = $methodModel->getJournalList($filter);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID')
            ->setCellValue('B1', '№ ОА')
            ->setCellValue('C1', 'Документ')
            ->setCellValue('D1', 'Пункт')
            ->setCellValue('E1', 'Год')
            ->setCellValue('F1', 'Определяемая характеристика')
            ->setCellValue('G1', 'Метод')
            ->setCellValue('H1', 'Цена текущая')
            ->setCellValue('I1', 'Цена Актуальная');

        $i = 2;
        foreach ($data as $val) {
            $sheet->setCellValue("A{$i}", $val['method_id'])
                ->setCellValue("B{$i}", $val['num_oa'])
                ->setCellValue("C{$i}", $val['reg_doc'])
                ->setCellValue("D{$i}", $val['clause'])
                ->setCellValue("E{$i}", $val['year'])
                ->setCellValue("F{$i}", $val['name'])
                ->setCellValue("G{$i}", $val['test_method'])
                ->setCellValue("H{$i}", $val['price']);
            $i++;
        }
        for ($a = 'A'; $a < 'I'; $a++) {
            $sheet->getColumnDimension($a)->setAutoSize(true);
        }

        $filename = 'priceList.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        ob_end_clean();
        $writer->save('php://output');
        exit();
    }

    public function formatTemperature($temperature)
    {
        $temperature = trim($temperature);

        if (str_starts_with($temperature, '-')) {
            return 'минус ' . ltrim($temperature, '-');
        } elseif (str_starts_with($temperature, '+')) {
            return 'плюс ' . ltrim($temperature, '+');
        }

        return 'плюс ' . $temperature;
    }

    /**
     * @param $dealId
     */
    public function actVrGenerate($dealId, $paydocs, $actNumber, $currentActDate)
    {
        //Реквизиты
        $companyModel = new Company();
        $company = $companyModel->getRequisiteByDealId($dealId);

        $requestModel = new Request();

        // Создаем запись об УПД
        $actVrModel = new Requirement();
        $tzId = $actVrModel->getTzIdByDealId($dealId);
        $order = $actVrModel->getDogovor($dealId);

        $existingAct = $this->DB->Query("SELECT NUMBER FROM `AKT_VR` WHERE `TZ_ID` = {$tzId} LIMIT 1")->Fetch();
        $actNum = $this->DB->ForSql($existingAct['NUMBER'] ?? '');
        $actDate = $this->DB->ForSql($existingAct['DATE'] ?? '');
        $currentDateTime = date("d-m-Y_H-i-s");
        $currentTime = date("H-i-s");

        $fileNames = [
            'ru' => "Упд_{$actNum}_{$currentDateTime}",
            'en' => "UPD_{$actNum}_{$currentDateTime}"
        ];



        //Данные документа
        $actVr = $actVrModel->getActVr($tzId);
        $dateUpdRu = StringHelper::getMonthGen($actVr['DATE']);
        $dateUpd = date('d.m.Y', strtotime($actVr['DATE']));

        $currentDate = date('Y-m-d', strtotime($currentActDate));

        $desiredFilename = "УПД №{$actVr['NUMBER']} от {$dateUpdRu} г";

        $params = [
            'ACTUAL_VER' => $this->DB->ForSql($desiredFilename),
            'NUMBER' => $actNum ?: 'Ждем ответ от 1С',
            'DATE' => $actDate,
            'TZ_ID' => $tzId
        ];

        if ($existingAct) {
            if (!$actNumber) $actNumber = '--';

            $this->DB->Query("UPDATE `AKT_VR` SET
                 ACTUAL_VER = '{$desiredFilename}', NUMBER = '{$actNumber}', paydocs = '{$paydocs}', DATE = '{$currentDate}'
                 WHERE TZ_ID = {$params['TZ_ID']}");
        } else {
            $this->DB->Query("INSERT INTO `AKT_VR`
                 (TZ_ID, ACTUAL_VER, NUMBER, DATE)
                 VALUES (
                    {$params['TZ_ID']},
                    '{$params['ACTUAL_VER']}',
                    '{$params['NUMBER']}',
                    '{$currentDate}'
                 )");
        }

        //Данные о счете
        $invoice = $requestModel->getInvoice1С($tzId);

        //Данные о протоколах
        $protocolModel = new Protocol();
        $protocolArr = $protocolModel->getProtocolsByDealId($dealId);
        $protocolStringArr = [];
        foreach ($protocolArr as $protocolValue) {
            $protocolDate = date('d.m.Y', strtotime($protocolValue['DATE']));
            $protocolStringArr[] = "{$protocolValue['NUMBER_AND_YEAR']} от {$protocolDate} г.";
        }

        //Отправляем массив в 1С
        if ($order['CONTRACT_TYPE'] === 'Счет-оферта') {
            $typeOrder = 'cчет-оферта ';
        }

        $arr_data_1c = [
            "fl" => 1,
            "number" => $invoice['NUMBER'],
            "date" => date('d.m.Y', strtotime($invoice['DATE'])),
            "inn" => $company['RQ_INN'],
            "p_number" => $protocolStringArr,
            "n_dogovor" => "{$typeOrder}{$order['NUMBER']}",
            "d_dogovor" => date('d.m.Y', strtotime($order['DATE'])),
            "kpp" => $company['RQ_KPP'],
        ];
        if (empty($existingAct['NUMBER'])) {
            $this->saveRequestTo1C($arr_data_1c);
        }
        // Путь к шаблону
        $templatePath = $_SERVER['DOCUMENT_ROOT'] . '/upd.xlsx';

        // Загрузка шаблона
        $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);

        // Получение активного листа (исправлено на getActiveSheet)
        $sheet = $spreadsheet->getActiveSheet();
        // Устанавливаем русскую локаль
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'rus_RUS.UTF-8', 'russian');


        //НомерУПД
        $sheet->getCell('P1')->setValue($actVr['NUMBER']);

        //Дата УПД
        $sheet->getCell('Y1')->setValue($dateUpdRu);

        list($day, $month, $year) = explode(' ', $dateUpdRu);
        $sheet->getCell('O31')->setValue("« {$day} »   {$month}  {$year}  года");

        //К платежно-расчетному документу №
        $sheet->getCell('R9')->setValue($paydocs);

        //Документ об отгрузке
        $sheet->getCell('R10')->setValue("Универсальный передаточный документ, № {$actVr['NUMBER']} от {$dateUpd} г.");


        //Название компании
        $sheet->getCell('BE4')->setValue($company['NAME']);
        //Адрес компании
        $sheet->getCell('BE5')->setValue($company['RQ_ACCOUNTANT']);
        //ИНН/КПП
        $sheet->getCell('BE6')->setValue("{$company['RQ_INN']}/{$company['RQ_KPP']}");

        $sheet->getCell('AS40')->setValue("{$company['NAME']}, ИНН/КПП {$company['RQ_INN']}/{$company['RQ_KPP']}");

        //Получение договора
        $order = $actVrModel->getDogovor($dealId);
        if (!empty($order['number_client'])) {
            $order['NUMBER'] = $order['number_client'];
        }
        $orderDate = date('d.m.Y', strtotime($order['DATE']));
        $orderStr = "{$order['CONTRACT_TYPE']} {$order['NUMBER']} {$orderDate}";

        $sheet->getCell('T22')->setValue($orderStr);

        // Получение значения из ячейки наименование товара


        $protocolString = implode(', ', $protocolStringArr);

        $nameSale = "Лабораторные испытания строительных материалов. по протоколам {$protocolString}";
        $sheet->getCell('J15')->setValue($nameSale);

        //Данные о стоимости
        $requestData = $requestModel->getTzByDealId($dealId);

        //Цена (тариф) за единицу измерения
        $sheet->getCell('AD15')->setValue(number_format($requestData['price_discount'], 2, ',', ''));

        //Стоимость товаров (работ, услуг), имущественных прав без налога - всего
        $sheet->getCell('AN15')->setValue(number_format($requestData['price_discount'], 2, ',', ' '));
        $sheet->getCell('AN16')->setValue(number_format($requestData['price_discount'], 2, ',', ' '));

        //Сумма налога, предъявляемая покупателю
        $nds_mult = 0.07;
        $NDS = $requestData['price_discount'] * $nds_mult;
        $sheet->getCell('BB15')->setValue(number_format($NDS, 2, ',', ''));
        $sheet->getCell('BB16')->setValue(number_format($NDS, 2, ',', ''));

        //Стоимость товаров (работ, услуг), имущественных прав с налогом всего
        $summNDS = $requestData['price_discount'] * (1 + $nds_mult);
        $sheet->getCell('BF15')->setValue(number_format($summNDS, 2, ',', ' '));
        $sheet->getCell('BF16')->setValue(number_format($summNDS, 2, ',', ' '));


        // Сохранение файла

        $writer = new Xlsx($spreadsheet);
        $fullPathFile = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/interim_archive_aktvr/{$dealId}/{$fileNames['en']}.xlsx";

        $dir = dirname($fullPathFile);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $writer->save($fullPathFile);

        $fullPathPDFFile = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/archive_aktvr/{$dealId}/{$fileNames['en']}.pdf";
        $fullPathPDF = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/archive_aktvr/{$dealId}/";

        if ($actNum != '' && $actNum != 'Ждем ответ от 1С') {

            $dir = dirname($fullPathPDFFile);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            // Сохранение в PDF
            $command = "libreoffice --headless --convert-to pdf --outdir " . escapeshellarg($fullPathPDF) . " " . escapeshellarg($fullPathFile);

            // Выполнение команды
            exec($command, $output, $returnCode);

            $originalFilename = pathinfo($fullPathFile, PATHINFO_FILENAME);
            $tempPdfPath = $fullPathPDF . DIRECTORY_SEPARATOR . $originalFilename . '.pdf';

            // Желаемое имя конечного файла (без расширения .pdf)

            $finalPdfPath = $fullPathPDF . DIRECTORY_SEPARATOR . $desiredFilename . '.pdf';

            // Переименовываем файл
            if (file_exists($tempPdfPath)) {
                rename($tempPdfPath, $finalPdfPath);
            }
        }

        //Скачиваем Excel
        if (file_exists($fullPathFile)) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . basename($fullPathFile) . '"');
            header('Content-Length: ' . filesize($fullPathFile));
            readfile($fullPathFile);
            exit;
        }
    }


    /**
     * @param $value - Одиночное значение или массив значений
     * @param int|null $decimals - Количество знаков после запятой (если null — без форматирования через number_format)
     * @return array|string|string[]
     */
    public function formatNumber($value, ?int $decimals = null)
    {
        if (is_array($value)) {
            return array_map(function ($item) use ($decimals) {
                return $this->formatNumber($item, $decimals);
            }, $value);
        }

        if (is_numeric($value)) {
            $floatValue = (float)$value;
            if ($decimals !== null) {
                return number_format($floatValue, $decimals, ',', '');
            }
            return str_replace('.', ',', (string)$value);
        }

        // Если значение не распознано как число, но может содержать числа в виде строки (например: "1,234.56")
        $normalized = str_replace(',', '.', $value);
        if (is_numeric($normalized)) {
            $floatValue = (float)$normalized;
            if ($decimals !== null) {
                return number_format($floatValue, $decimals, ',', '');
            }
            return str_replace('.', ',', (string)$normalized);
        }

        return str_replace('.', ',', $value);
    }

    /**
     * @param array $requestData
     */
    public function saveRequestTo1C(array $requestData)
    {
        // Получаем текущие запросы
        $currentRequest = $this->DB->Query("SELECT REQ FROM `REQUESTS_FOR_1C` WHERE `ID` = 1")->Fetch();

        // Подготавливаем массив запросов
        $requests = [];
        if (!empty($currentRequest['REQ'])) {
            $requests = unserialize($currentRequest['REQ']);
        }

        // Добавляем новый запрос
        if (!isset($requests['requests'])) {
            $requests['requests'] = [];
        }

        $requests['requests'][] = $requestData;

        // Сохраняем обновленные данные
        //        $this->pre($requests);
        $serializedData = $this->DB->ForSql(serialize($requests));
        $this->DB->Query("UPDATE `REQUESTS_FOR_1C` SET `REQ` = '{$serializedData}' WHERE `ID` = 1");
    }
}
