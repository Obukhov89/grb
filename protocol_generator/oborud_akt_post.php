<?
/**
 * @author Vladislav Satonin
 * @email vlad.satonin74@gmail.com
 * @create date 2021-05-06 13:11:07
 * @modify date 2021-05-06 13:11:07
 * @desc [description]
 */

//* =========== Подключения ===========
use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php';
$DB_Conn = new CDB();

Loader::IncludeModule('documentgenerator');
Loader::IncludeModule('crm');

//* =========== Функции ===========


// если php ниже 7.3 собственная функция ищущая последний ключ в массиве
if (! function_exists("array_key_last")) {
  function array_key_last($array) {
      if (!is_array($array) || empty($array)) {
          return NULL;
      }

      return array_keys($array)[count($array)-1];
  }
}

$month_arr = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая'];

//* =========== Загрузка шаблона word ===========

$file = new Bitrix\Main\IO\File(__DIR__ . '/../oborud_akt_post.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

// запрашиваем данные
if(!empty($_GET['OB_ID'])){
  $res = $DB_Conn->select("SELECT * FROM `ba_oborud` WHERE `ID` = {?}", array((int)$_GET['OB_ID']));
}else{
  exit();
}

$arr_out = [];

// подразделение ИЦ
if(!empty($res[0]['place_of_installation_or_storage'])){
  $res_laba = $DB_Conn->select("SELECT * from ba_laba where ID = {?}", array($res[0]['place_of_installation_or_storage']));

  $arr_out['NAME'] = $res_laba[0]['NAME'];
}else{
  $arr_out['NAME'] = ' ';
}

// берём сертификат из таблички b_verification_certificate по ключу id_oborud
$res_cert = $DB_Conn->select("SELECT * FROM `b_verification_certificate` WHERE `id_oborud` = {?}", array((int)$_GET['OB_ID']));

$certificationName_arr = unserialize($res_cert[0]['name_f']);
$cert_arr = [];
foreach($certificationName_arr as $key => $value) { $cert_arr[] = $value; }

$certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v;

$tmp_date = new DateTime($res[0]['god_vvoda_expluatation']);
$arr_out['DATE'] = $tmp_date->format('d-m-Y'); // объект DateTime
$arr_out['LONG_STORAGE_DATE'] = date('d.m.Y', strtotime($res[0]['LONG_STORAGE_DATE']));

switch ($res[0]['IDENT']) {
    case 'IO':
        $ident = 'ИО';
        break;
    case 'VO':
        $ident = 'ВО';
        break;
    case 'TS':
        $ident = 'ТС';
        break;
    case 'SI':
        $ident = 'СИ'; //Системы измерения
        break;
    case 'SO':
        $ident = 'СО';
        break;
    case 'REACT':
        $ident = 'реактив';
        break;
    case 'OOPP':
        $ident = 'ООПП';
        break;
    default:
        $ident = "СИ/ИО/ВО";
}

$assignedId = $res[0]['ID_ASSIGN1'];
$user = CUser::GetByID($assignedId)->Fetch();
$n = mb_substr($user['NAME'], 0, 1, 'UTF-8');
$o = mb_substr($user['SECOND_NAME'], 0, 1, 'UTF-8');

$arr_out['Assigned'] = "{$user['LAST_NAME']} {$n}.{$o}.";


// $arr_out['Assigned'] = $res[0]['NAME'];

$tmp_date1 = new DateTime($res[0]['POVERKA_LAST']);
$arr_table[0] = [
  'Name' => $res[0]['OBJECT'],
  'Type' => $res[0]['TYPE_OBORUD'],
  'Number' => $res[0]['FACTORY_NUMBER'] . '/' . $res[0]['REG_NUM'],
  'Date' => $tmp_date1->format('d-m-Y'),
  'Cert' => $certificateName,
  'Note' => $res[0]['note'],
];

// подставляем данные в документ
$document->normalizeContent();
$document->setValues([
  'NAME' => $arr_out['NAME'],
  'DATE' => $arr_out['DATE'],
  'LONG_STORAGE_DATE' => $arr_out['LONG_STORAGE_DATE'],
  'Assigned' => $arr_out['Assigned'],
  'IDENT' => $arr_out['IDENT'],

  'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
    $arr_table,
    [
      'ITEM_NAME' => 'Item',
      'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
    ]),
    'TableItemName' => 'Table.Item.Name',
    'TableItemType' => 'Table.Item.Type',
    'TableItemNumber' => 'Table.Item.Number',
    'TableItemDate' => 'Table.Item.Date',
    'TableItemCert' => 'Table.Item.Cert',
    'TableItemNote' => 'Table.Item.Note',
]);

// обработка файла
$result = $document->process();
$content = $document->getContent();

// отдать word в браузер на скачку
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="akt_post.docx"');
header('Cache-Control: max-age=0');
echo $content;

die;

