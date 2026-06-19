<?php
/**
 * Created by Makaralex92.
 * User: Алексей
 * Date: 15.04.2019
 * Time: 10:07
 */

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

function num2word($n, $titles)
{
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
}

function dump($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}

//Запрашиваем данные
$ID = $_GET['ID']; //ID Заявки
$TZ_ID = $_GET['TZ_ID']; //ID Технического задания. В нём хранится ТЗ, результаты исследований и информация о пробах
$mater = $_GET['mater']; // ID материала, указанного в ТЗ
$probe_ID = $_GET['probe']; // ID пробы, указанного в пробах, заполненных на основе ТЗ

$deal = CCrmDeal::GetByID($ID); //Берём значение сделки

global $DB;
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
$ba_tz = $res->Fetch();
$tz = unserialize($ba_tz['TZ'])[$_GET['mater']];
$probe = unserialize($ba_tz['PROBE'])[$_GET['mater']];
$type_tz = $ba_tz['TYPE_TZ'];

if ($type_tz == 1) {
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
} else {
    $file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol2.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
}

//Формируем данные
$oborud = [];
$results = [];
$lab = [];
$arr_soot = [
    0 => 'Не соответсвует',
    1 => 'Соответствует',
    2 => '-',
];
foreach ($tz['gost'] as $k => $item) {
    $results_tz = unserialize($ba_tz['RESULTS'])[$_GET['mater'] - 1][$k][$probe_ID];
    $temp[] = unserialize($ba_tz['TEMP'])[$_GET['mater'] - 1][$k][$probe_ID];
    $vlag[] = unserialize($ba_tz['VLAG'])[$_GET['mater'] - 1][$k][$probe_ID];
    $date1[] = unserialize($ba_tz['DATE1'])[$_GET['mater'] - 1][$k][$probe_ID];
    $date2[] = unserialize($ba_tz['DATE2'])[$_GET['mater'] - 1][$k][$probe_ID];
    $labs = unserialize($ba_tz['LAB'])[$_GET['mater'] - 1][$k][$probe_ID];
    $MATCH_MANUAL = unserialize($ba_tz['MATCH_MANUAL'])[$probe_ID-1];
    $SRED = unserialize($ba_tz['SRED'])[$_GET['mater']-1];
    $GOST_VAL[] = "От " . $tz['norm_1'][$k] . " до " . $tz['norm_2'][$k] . " " . $tz['ed_izm'][$k];
    $GOST_DOC[] = $tz['gost_new'][$k];
    $ED[] = $tz['ed_izm'][$k];

    $res = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID`=" . $labs);
    $ba_laba = $res->Fetch();
    $lab[] = $ba_laba['NAME'];

    $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
    $gost = $ba_gost->Fetch();

    $ba_connection = $DB->Query("SELECT * FROM `ba_connection` WHERE `GOST_ID`=" . $gost['ID']);
    while ($oborud_id = $ba_connection->Fetch()) {
        $ba_oborud = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . $oborud_id['OBORUD_ID']);
        $ob = $ba_oborud->Fetch();

        if (!in_array([
            'Name' => $ob['OBJECT'] . ', №' . $ob['REG_NUM'],
            'Class' => $ob['сlass_precision_and_accuracy'],
            'Predel' => $ob['measuring_range'],
            'Poverka' => date("d.m.Y", strtotime($ob['POVERKA'])).(($ob['certificate_of_verification'])? ', '.$ob['certificate_of_verification'] : '')
        ], $oborud)
        ) {
            $oborud[] = [
                'Name' => $ob['OBJECT'] . ', №' . $ob['REG_NUM'],
                'Class' => $ob['сlass_precision_and_accuracy'],
                'Predel' => $ob['measuring_range'],
                'Poverka' => date("d.m.Y", strtotime($ob['POVERKA'])).(($ob['certificate_of_verification'])? ', '.$ob['certificate_of_verification'] : '')
            ];
        }
    }

    $req = new \Bitrix\Crm\EntityRequisite();
    $rs = $req->getList([
        "filter" => [
            "ENTITY_ID" => $deal['COMPANY_ID'],
        ]
    ]);
    $requis = $rs->fetch();
    $results[] = [
        'Object' => '№ ' . $probe['number'] . "." . $probe_ID,
        'Specification' => $gost['SPECIFICATION'],
        'Ed' => $ED[$k],
        'Normdoc' => $GOST_DOC[$k],

        'Normval' => $GOST_VAL[$k],
        'Normdocmet' => $gost['GOST'],
        'Value' => $SRED[$k][$probe_ID],
        'Result' => $arr_soot[$MATCH_MANUAL[$k][$probe_ID]]
    ];

    $SPECIFICATION[] = $gost['SPECIFICATION'];
    $GOSTS[] = $gost['GOST'];
}
//Заполняем данные
$document->normalizeContent();
$document->setValues([
    'nProtocol' => $ID . '-' . $probe['number'],
    'dProtocol' => date("d.m.Y"),
    'nDogovor' => $ID,
    'dDorovor' => $deal['BEGINDATE'],
    'nZakazchik' => $deal['COMPANY_TITLE'],
    'innZakazchik' => $requis['RQ_INN'],
    'ogrnZakazchik' => $requis['RQ_OGRN'],
    'aZakazchik' => $deal['CONTACT_ADDRESS'],
    'dProbe' => $probe['dat'],
    'mestoSboraProbe' => (string)$probe['mesto_data'],
    'oStroit' => $ba_tz['OBJECT'],
    'oIspit' => $probe['number_probe'][$probe_ID - 1],
    'nProbe' => $probe['number'] . "." . $probe_ID,
    'celIspit' => implode(", ",$SPECIFICATION),
    'metodIspit' => implode(", ",$GOSTS),
    'mIspit' => implode(", ",array_unique($lab)),
    'd1Ispit' => date("d.m.Y", strtotime(min($date1))),
    'd2Ispit' => date("d.m.Y", strtotime(max($date2))),
    'tIspit' => array_sum($temp) / count($temp),
    'vIspit' => array_sum($vlag) / count($vlag),

    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $oborud,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'TableItemName' => 'Table.Item.Name',
    'TableItemClass' => 'Table.Item.Class',
    'TableItemPredel' => 'Table.Item.Predel',
    'TableItemPoverka' => 'Table.Item.Poverka',


    'Results' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'ResultsItemObject' => 'Results.Item.Object',
    'ResultsItemSpecification' => 'Results.Item.Specification',
    'ResultsItemEd' => 'Results.Item.Ed',
    'ResultsItemNormdoc' => 'Results.Item.Normdoc',
    'ResultsItemNormval' => 'Results.Item.Normval',
    'ResultsItemNormdocmet' => 'Results.Item.Normdocmet',
    'ResultsItemValue' => 'Results.Item.Value',
    'ResultsItemResult' => 'Results.Item.Result',
]);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Протокол № '.($ID . '-' . $probe['number']).' от '.date("d.m.Y").'.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;