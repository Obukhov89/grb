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

// require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/Morphos-master/vendor/wapmorgan/morphos/web/case_conversion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
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

$file = new Bitrix\Main\IO\File(__DIR__ . '/../oborud_akt_ver.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

// запрашиваем данные
if(!empty($_GET['OB_ID'])){
  $res = $DB_Conn->select("SELECT * FROM `ba_oborud` WHERE `ID` = {?}", array((int)$_GET['OB_ID']));
}else{
  exit();
}
// подразделение ИЦ
$res_laba = $DB_Conn->select("SELECT * from ba_laba where ID = {?}", array($res[0]['place_of_installation_or_storage']));

// берём сертификат из таблички b_verification_certificate по ключу id_oborud
$res_cert = $DB_Conn->select("SELECT * FROM `b_verification_certificate` WHERE `id_oborud` = {?}", array((int)$_GET['OB_ID']));

$certificationName_arr = unserialize($res_cert[0]['name_f']);
$cert_arr = [];
foreach($certificationName_arr as $key => $value) { $cert_arr[] = $value; }

// кладём нужные значения в массив
$arr_out = [];

$arr_out['NAME'] = $res[0]['OBJECT'];
$arr_out['TYPE'] = $res[0]['TYPE_OBORUD'];
$arr_out['INV_NUM'] = $res[0]['FACTORY_NUMBER'];
$arr_out['MANUF'] = $res[0]['manufacturer'];

$tmp_date = new DateTime($res[0]['god_vvoda_expluatation']);
$arr_out['DATE_VVOD'] = $tmp_date->format('d-m-Y'); // объект DateTime
$arr_out['DATE'] = $tmp_date->format('d-m-Y');

$arr_out['UNIT'] = $res_laba[0]['NAME']; // название файла

$arr_out['document'] = $res[0]['DOCUMENT']; // название файла

$arr_out['certificate'] = $cert_arr[array_key_last($cert_arr)]; // из табличка b_verification_certificate

$arr_out['damage'] = "не повреждена";
$arr_out['conform'] = "соответствует";
$arr_out['complet'] = "комплектно";
$arr_out['defects'] = "не обнаружены";


$assignedId = $res[0]['ID_ASSIGN1'];
$user = CUser::GetByID($assignedId)->Fetch();
$n = mb_substr($user['NAME'], 0, 1, 'UTF-8');
$o = mb_substr($user['SECOND_NAME'], 0, 1, 'UTF-8');

$arr_out['ASSIGNED'] = "{$user['LAST_NAME']} {$n}.{$o}.";

switch ($res[0]['place_of_installation_or_storage']) {
  case '1':
      $arr_out['UNIT'] = "ЛФХИ";
      break;
  case '2':
      $arr_out['UNIT'] = "ЛСМ";
      break;
case '3':
      $arr_out['UNIT'] = "ЛФМИ";
      break;
case '4':
      $arr_out['UNIT'] = "ДСЛ";
      break;
case '5':
      $arr_out['UNIT'] = "ОСК";
      break;
}

// $arr_out['UNIT'] = $new_unit['dative'];

// подставляем данные в документ
$document->normalizeContent();
$document->setValues([
  'NAME' => $arr_out['NAME'],
  'TYPE' => $arr_out['TYPE'],
  'INV_NUM' => $arr_out['INV_NUM'],
  'MANUF' => $arr_out['MANUF'],
  'DATE_VVOD' => $arr_out['DATE_VVOD'],
  'UNIT' => $arr_out['UNIT'],
  'ASSIGNED' => $arr_out['ASSIGNED'],

  'document' => $arr_out['document'],
  'certificate' => $arr_out['certificate'],

  'damage' => $arr_out['damage'],
  'conform' => $arr_out['conform'],
  'complet' => $arr_out['complet'],
  'defects' => $arr_out['defects'],

  'DATE' => $arr_out['DATE'],
]);

// обработка файла
$result = $document->process();
$content = $document->getContent();

// отдать word в браузер на скачку
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="akt_ver.docx"');
header('Cache-Control: max-age=0');
echo $content;

die;

?>
