<?php
/**
 * Created by Makaralex92.
 * User: Алексей
 * Date: 07.05.2019
 * Time: 16:11
 */

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

if (!function_exists('mb_ucfirst') && extension_loaded('mbstring'))
{
    /**
     * mb_ucfirst - преобразует первый символ в верхний регистр
     * @param string $str - строка
     * @param string $encoding - кодировка, по-умолчанию UTF-8
     * @return string
     */
    function mb_ucfirst($str, $encoding='UTF-8')
    {
        $str = mb_ereg_replace('^[\ ]+', '', $str);
        $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
            mb_substr($str, 1, mb_strlen($str), $encoding);
        return $str;
    }
}

$dealsId = array();

$dealsId = explode('_', $_GET['ID']);

$deal = CCrmDeal::GetByID($dealsId[0]); //Берём значение сделки
$company = CCrmCompany::GetByID($deal['COMPANY_ID']);

$str = '';

foreach($dealsId as $key => $id)
{
    $requestArr[$key] = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $id)->Fetch();

    $priceArr[] = (int)$requestArr[$key]['PRICE'];

    $y_date = date("Y", strtotime($requestArr[$key]['DATE_P_CORR']));
    if ($y_date != '2020') {
        $y_date = "/".substr($y_date, 2);
    } else {
        $y_date = "/".$y_date;
    }

    $str .= $requestArr[$key]['NUM_P'].$y_date." от ".date("d.m.Y", strtotime($requestArr[$key]['DATE_P_CORR']))." г.";

    //Если последний элемент то запятую не ставим
    if($id != end($dealsId)) {
        $str .= ', №';
    }
}

$sumPrice = array_sum($priceArr);

$AKT_DATE = !empty($_GET['VR_DATE']) ? $_GET['VR_DATE'] : '';

$AKT_NUM = !empty($_GET['VR_NUM']) ? $_GET['VR_NUM'] : '';

$curdate = "Акт_". $AKT_NUM .'_'. $AKT_DATE;
$curdateEng = "Act_". $AKT_NUM .'_'. $AKT_DATE;


$contractData = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$_GET['ID_CONTRACT'])->Fetch();

$numberDogovor = !empty($contractData['NUMBER']) ? $contractData['NUMBER'] : '';
$dateDogovor = !empty($contractData['DATE']) ? $contractData['DATE'] : '';

$file = new Bitrix\Main\IO\File(__DIR__ . '/../akt_vr.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$req = new \Bitrix\Crm\EntityRequisite();
$rs = $req->getList([
    "filter" => [
        "ENTITY_ID" => $deal['COMPANY_ID'],
    ]
]);
$requis = $rs->fetch();

//Заполняем данные
$document->normalizeContent();

$document->setValues([
        'nProtocol' => $str,
        'nDogovor' => "Договор №".$numberDogovor,
        'dDorovor' => " от ".ruDate($dateDogovor),
        'nZakazchik' => !empty($deal['COMPANY_TITLE']) ? $deal['COMPANY_TITLE'] : '',
        'innZakazchik' => !empty($requis['RQ_INN']) ? $requis['RQ_INN'] : '',
        'ogrnZakazchik' => !empty($requis['RQ_OGRN']) ? $requis['RQ_OGRN'] : '',
        'aZakazchik' => !empty($requis['RQ_ACCOUNTANT']) ? $requis['RQ_ACCOUNTANT'] : '',
        'SumAll' => number_format($sumPrice, 2, ',', ''),
        'SumAllWord' => mb_ucfirst(num2str($sumPrice)),
        'AllName' => 1,
        'NUM_ACC' => $AKT_NUM,
        'DATE_ACC' => date("d.m.Y", strtotime($AKT_DATE)),
    ]
);

$result = $document->process();
$content = $document->getContent();
//$newpath = "archive_acc/".$ID."/".$curdate.".docx";
$newpath = "archive_aktvr/".$ID."/".$curdate.".docx";
/*$interimPath = "interim_archive_aktvr/".$ID."/".$curdateEng.".docx";
$outputFile = $curdate.".pdf";
$outputPath = "archive_aktvr/".$ID."/";*/

function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );

    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );

    file_put_contents( $fullPath, $contents, $flags );
}
/*$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_aktvr/'.$ID;
if( !is_dir( $newDirectory ) ) {
    mkdir($newDirectory, 0777, true);
}
file_force_contents($interimPath, $content);

$fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$interimPath;
$fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$outputPath;

// если вы используете composer, просто используйте это
use  NcJoes\OfficeConverter\OfficeConverter;

$converter  =  new  OfficeConverter($fullPathDocx, $fullPathPDF);//, $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/');
$converter -> convertTo($outputFile); // генерирует pdf файл в том же каталоге, что и test-file.docx*/

file_force_contents($newpath, $content);

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Акт № ' . $AKT_NUM . ' от ' . date("d.m.Y", strtotime($AKT_DATE)) . '.docx"');
header('Cache-Control: max-age=0');
echo $content;

die;
?>