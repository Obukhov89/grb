<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 26.04.2019
 * Time: 9:38
 */


use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";

$file = new Bitrix\Main\IO\File(__DIR__ . '/../MetrRequest.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

//Запрашиваем данные
$currentDate = date('Y-m-d');

$res_ob_si = $DB->Query(
    "SELECT b.*, l.NAME laba_name, c.name as certificate_name, c.date_start, c.date_end
        FROM ba_oborud as b
        inner join ba_oborud_certificate as c on c.oborud_id = b.ID and c.is_actual = 1
        LEFT JOIN ba_laba as l ON l.ID = b.place_of_installation_or_storage
        WHERE 
              b.`IDENT` LIKE '%SI%'  
              and b.NO_METR_CONTROL <> 1  
              and b.LONG_STORAGE = 0 
              and b.is_decommissioned = 0
              and (b.IN_POVERKA = 0 OR b.IN_POVERKA IS NULL)
              and c.is_actual = 1
              and (c.date_end - interval 90 day) <= '{$currentDate}' and c.date_end > '{$currentDate}'
        group by b.ID
    ");

$si = 1;
while ($sql = $res_ob_si->Fetch()) {
    switch ($sql['METR_CONTROL']) {
        case 'CALIBR':
            $metr_control = '4';
            break;
        case 'ATTESTATION':
            $metr_control = '-';
            break;
        case 'POVERKA':
            $metr_control = '3';
            break;
        case 'TECH_CHAR':
            $metr_control = '-';
            break;

    }

    $results2[] = [
        'NUM' => $si,
        'CHAR' => $sql['NAME'],
        'METR_TYPE' => $metr_control,
        'Labaratory' => $sql['laba_name'] ?? '',
        'NAME' => $sql['OBJECT'],
        'TYPE' => $sql['TYPE_OBORUD'],
        'GOSREESTR' => $sql['GOSREESTR'],
        'MANUFACTURER' => $sql['manufacturer'],
        'IDENT' => 'СИ',
        'TECH' => $sql['TECH'],
        'OVV_EX' => "от " . $sql['OVV_EX'] . " до " . $sql['OVV_EX2'],
        'TOO_EX' => "от " . $sql['TOO_EX'] . " до " . $sql['TOO_EX2'],
        'AD_EX' => "от " . $sql['AD_EX'] . " до " . $sql['AD_EX2'],
        'OVV_UH' => $sql['OVV_UH'],
        'TOO_UH' => $sql['TOO_UH'],
        'AD_UH' => $sql['AD_UH'],
        'REG_NUM_ZAV' => $sql['FACTORY_NUMBER'],
        'YEAR' => $sql['YEAR'],
        'EXPLUATATION' => ($sql['god_vvoda_expluatation'] != "1970-01-01" ? date("d.m.Y", strtotime($sql['god_vvoda_expluatation'])) : "-"),
        'REG_NUM' => $sql['REG_NUM'],
        'REG_NUM_RC' => $sql['REG_NUM_RC'],
        'COND' => '',
        'PLACE' => '',
        'METROLOG' => $metr_control,    //$sql['POVERKA'],
        'DOCUMENT' => $sql['DOCUMENT'],
        'PO_NAME' => $sql['PO_NAME'],
        'PO_ID' => $sql['PO_ID'],
        'NAPR' => $sql['NAPR'],
        'FREQ' => $sql['FREQ'],
        'ASSIGNED' => $assigned,
        'DIAP' => $sql['measuring_range'],
        'PREC' => $sql['сlass_precision_and_accuracy'],
        'METR_CONTROL' => $metr_control,
        'SERT' => $sql['certificate_name'],
        'POVERKA_LAST' => ($sql['POVERKA_LAST'] ? date("d.m.Y", strtotime($sql['POVERKA_LAST'])) : "-"),
        'INTERVAL' => $sql['MC_INTERVAL'],
        'property_rights' => $sql['property_rights'],
        'NOTE' => $sql['NOTE']

    ];
    $si++;
}


$document->normalizeContent();
$document->setValues([

    'Date' => date("d.m.Y"),
    'Results2' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results2,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'Results2ItemOB_ID' => 'Results2.Item.OB_ID',
    'Results2ItemLabaratory' => 'Results2.Item.Labaratory',
    'Results2ItemNAME' => 'Results2.Item.NAME',
    'Results2ItemTYPE' => 'Results2.Item.TYPE',
    'Results2ItemGOSREESTR' => 'Results2.Item.GOSREESTR',
    'Results2ItemMANUFACTURER' => 'Results2.Item.MANUFACTURER',
    'Results2ItemIDENT' => 'Results2.Item.IDENT',
    'Results2ItemTECH' => 'Results2.Item.TECH',
    'Results2ItemOVV_EX' => 'Results2.Item.OVV_EX',
    'Results2ItemTOO_EX' => 'Results2.Item.TOO_EX',
    'Results2ItemAD_EX' => 'Results2.Item.AD_EX',
    'Results2ItemOVV_UH' => 'Results2.Item.OVV_UH',
    'Results2ItemTOO_UH' => 'Results2.Item.TOO_UH',
    'Results2ItemAD_UH' => 'Results2.Item.AD_UH',
    'Results2ItemREG_NUM_ZAV' => 'Results2.Item.REG_NUM_ZAV',
    'Results2ItemYEAR' => 'Results2.Item.YEAR',
    'Results2ItemEXPLUATATION' => 'Results2.Item.EXPLUATATION',
    'Results2ItemREG_NUM' => 'Results2.Item.REG_NUM',
    'Results2ItemREG_NUM_RC' => 'Results2.Item.REG_NUM_RC',
    'Results2ItemCOND' => 'Results2.Item.COND',
    'Results2ItemPLACE' => 'Results2.Item.PLACE',
    'Results2ItemMETROLOG' => 'Results2.Item.METROLOG',
    'Results2ItemDOCUMENT' => 'Results2.Item.DOCUMENT',
    'Results2ItemPO_NAME' => 'Results2.Item.PO_NAME',
    'Results2ItemPO_ID' => 'Results2.Item.PO_ID',
    'Results2ItemNAPR' => 'Results2.Item.NAPR',
    'Results2ItemFREQ' => 'Results2.Item.FREQ',
    'Results2ItemASSIGNED' => 'Results2.Item.ASSIGNED',
    'Results2ItemDIAP' => 'Results2.Item.DIAP',
//	'Results2ItemDIAP' => 'Results2.Item.DIAP',
    'Results2ItemPREC' => 'Results2.Item.PREC',
    'Results2ItemMETR_CONTROL' => 'Results2.Item.METR_CONTROL',
    'Results2ItemSERT' => 'Results2.Item.SERT',
    'Results2ItemPOVERKA_LAST' => 'Results2.Item.POVERKA_LAST',
    'Results2ItemINTERVAL' => 'Results2.Item.INTERVAL',
    'Results2ItemNOTE' => 'Results2.Item.NOTE',
    'Results2ItemCHAR' => 'Results2.Item.CHAR',
    'Results2ItemNUM' => 'Results2.Item.NUM',
    'Results2Itemproperty_rights' => 'Results2.Item.property_rights',


]);

$result = $document->process();
$content = $document->getContent();
// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Заявка на поверку.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;