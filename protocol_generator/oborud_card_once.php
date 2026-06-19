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

//Функция сохранения файла
function file_force_contents($fullPath, $contents, $flags = 0)
{
    $parts = explode('/', $fullPath);
    array_pop($parts);

    $dir = implode('/', $parts);

    if (!is_dir($dir))
        mkdir($dir, 0777, true);

    file_put_contents($fullPath, $contents, $flags);
}

//Функция преобразования штрихкода инвентарного номера в нужный вид
function translit($s)
{
    $s = (string)$s; // преобразуем в строковое значение
    $s = str_replace([' ', '№'], '', $s);//Находим все пробелы,номер и убираем их
    //$s = trim($s); // убираем пробелы в начале и конце строки
    //$s = str_replace('№', '', $s); //Находим номер и обрезаем его
    //$s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
    //$s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
    $s = strtr($s, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'В' => 'B', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => '')); //Преобразуем кирилицу "В" в латиницу "B"
    $s = mb_strimwidth($s, 0, 12); //Обрезаем строку определенного размера
    return $s; // возвращаем результат
}

//$arrayIdent = ['SI','VO','TS', 'IO', 'SO', 'REACT', 'OOPP'];//Наименование, тип (марка)
$arrayIdent = ['SI', 'VO', 'IO'];//Наименование, тип (марка)
//$arrayIdent = ['SI'];//Наименование, тип (марка)


$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID` = {$_GET['OB_ID']}")->Fetch();
$valIdent = $res['IDENT'];


//Счетчик для посчета количества Элементов(этераций) для каждого типа(марки) оборудования
//	$i = 1;
$arrContent = [];

//Перебираем найденное оборудование по типу(марке)
// while($res = $res->Fetch()) {
//	foreach($res as $key => $res) {
//Массив с обьектами документ очищаем на каждой этерации для освобождения памяти
$document = [];

//$file = new Bitrix\Main\IO\File(__DIR__ . '/../Markers.docx');
//Если карточки ИО или ВО применяем другой шаблон
if (!empty($valIdent) && $valIdent == 'IO') {
    $file = new Bitrix\Main\IO\File(__DIR__ . '/Markers_IO_VO_once.docx');

    $sql = $DB->Query("select * from ba_oborud_certificate where oborud_id = {$res['ID']} and is_actual = 1 order by id desc ");

    $sertArr = [];
    while ($row = $sql->Fetch()) {
        $dEnd = date("d.m.Y", strtotime($row['date_end']));
        $sertArr[] = $row['name'] . ", до " . $dEnd;

        if ( !empty($row['certified_values']) ) {
            $sertArr[] = 'Аттестованные значения: ' . $row['certified_values'];
        }
    }

    $certificateName = implode("\n ", $sertArr);
} else {
    $file = new Bitrix\Main\IO\File(__DIR__ . '/Markers_once.docx');

    $sertInfo = $DB->Query("select * from ba_oborud_certificate where oborud_id = {$res['ID']} order by id desc ")->Fetch();
    $certificateName = $sertInfo['name'];
}

$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

//Наименование, тип (марка)
$ident = '';
switch ($res['IDENT']) {
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
}
switch ($res['METR_CONTROL']) {
    case 'CALIBR':
        $metr_control = 'Калибровка';
        break;
    case 'ATTESTATION':
        $metr_control = 'Аттестация';
        break;
    case 'POVERKA':
        $metr_control = 'Поверка';
        break;
    case 'TECH_CHAR':
        $metr_control = 'Проверка тех. характеристик';
        break;
}

//Находим ответственного
$assign_id = CUser::GetByID($res['ID_ASSIGN1'])->Fetch();
$assigned = "";
//Если ответственный существует находим Имя и Фамилию
if ($assign_id['ID']) {
    $assigned = $assign_id['NAME'] . " " . $assign_id['LAST_NAME'];
} else {
    $assigned = '-';
}


$arrayDataSetValues['OBJECT'] = !empty($res['OBJECT']) ? $res['OBJECT'] : '-'; //Наименование

$arrayDataSetValues['TYPE_OBORUD'] = !empty($res['TYPE_OBORUD']) ? $res['TYPE_OBORUD'] : '-'; //тип (марка)

$arrayDataSetValues['IDENT'] = !empty($ident) ? $ident : '-'; //Наименование, тип (марка)

$arrayDataSetValues['REG_NUM_ZAV'] = !empty($res['FACTORY_NUMBER']) ? str_replace('№', '', $res['FACTORY_NUMBER']) : '-'; //Заводской  номер

$arrayDataSetValues['REG_NUM'] = !empty($res['REG_NUM']) ? str_replace('№', '', $res['REG_NUM']) : '-'; //Инвентарный номер

$arrayDataSetValues['ASSIGNED'] = !empty($assigned) ? $assigned : '-'; //Ответственный

$arrayDataSetValues['SERT'] = !empty($certificateName) ? $certificateName : '-'; //Свидетельство о поверке (сертификат о калибровке) №

//$arrayDataSetValues['POVERKA'.$quantity] = ($res['POVERKA_LAST'] ? date("d.m.Y", strtotime($res['POVERKA_LAST'])) : "-"); //до какой даты
//$arrayDataSetValues['POVERKA'.$quantity] = ($res['POVERKA'] ? date("d.m.Y", strtotime($res['POVERKA'])) : "-"); //до какой даты
$arrayDataSetValues['POVERKA'] = date("d.m.Y", strtotime($sertInfo['date_end'])); //до какой даты


$document->normalizeContent();
$document->setValues($arrayDataSetValues);
$result = $document->process();
$content = $document->getContent();

//$newpath = "archive_card_laba/" . $ident . "_" . $number_file . ".docx";
//file_force_contents($newpath, $content);

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Этикетка оборудования №' . $res['ID'] . '.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;
