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
include $_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php';

$db = new CDB();

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

//Функция сохранения файла
function file_force_contents( $fullPath, $contents, $flags = 0 ){
	$parts = explode( '/', $fullPath );
	array_pop( $parts );

	$dir = implode( '/', $parts );

	if( !is_dir( $dir ) )
			mkdir( $dir, 0777, true );

	file_put_contents( $fullPath, $contents, $flags );
}

//Функция преобразования штрихкода инвентарного номера в нужный вид
function translit($s) {
  $s = (string) $s; // преобразуем в строковое значение
  $s = str_replace([' ', '№'], '', $s);//Находим все пробелы,номер и убираем их
  //$s = trim($s); // убираем пробелы в начале и конце строки
  //$s = str_replace('№', '', $s); //Находим номер и обрезаем его
  //$s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
  //$s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
  $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','В'=>'B','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>'')); //Преобразуем кирилицу "В" в латиницу "B"
  $s = mb_strimwidth($s, 0, 12); //Обрезаем строку определенного размера
  return $s; // возвращаем результат
}

if(!empty($_GET['laba'])) {
	$laba = $_GET['laba'];
}

//$arrayIdent = ['SI','VO','TS', 'IO', 'SO', 'REACT', 'OOPP'];//Наименование, тип (марка)
$arrayIdent = ['SI','VO','IO'];//Наименование, тип (марка)
//$arrayIdent = ['SI'];//Наименование, тип (марка)

//Номер формирования документа по счету, для создания нового экземляря класса
$numberDocument = 0;
$sqlTot = 'SELECT * FROM `ba_oborud` WHERE `IDENT` = {?} AND `place_of_installation_or_storage` = {?}';
//перебираем все типы(марки) оборудования
foreach($arrayIdent as $keyIdent => $valIdent) {

	$arr = array($valIdent, $laba);
	$res = $db->select($sqlTot, $arr);
	//Запрашиваем данные
	// $res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `IDENT`='" . $valIdent ."'");

	//Кол-во оборудования
	// $amountQquipment = $res->SelectedRowsCount();
	$amountQquipment = count($res);

	//Счетчик количества карточек оборудования , обнуляем при смене типы(марки) оборудования
	$quantity = 0;
	//Номер прикрепленный к названию файла , возращаем заного с единицы(1) для нового типа(марки) оборудования
	$number_file = 1;

	//Счетчик для посчета количества Элементов(этераций) для каждого типа(марки) оборудования
	$i = 1;
	$arrContent = [];

	//Перебираем найденное оборудование по типу(марке)
	// while($sql = $res->Fetch()) {
	foreach($res as $key => $sql) {
		//Массив с обьектами документ очищаем на каждой этерации для освобождения памяти
		$document = [];

		//$file = new Bitrix\Main\IO\File(__DIR__ . '/../Markers.docx');
		//Если карточки ИО или ВО применяем другой шаблон
		if (!empty($valIdent) && in_array($valIdent, ['IO'])) {
			$file = new Bitrix\Main\IO\File(__DIR__ . '/Markers_IO_VO.docx');
		} else {
			$file = new Bitrix\Main\IO\File(__DIR__ . '/Markers.docx');
		}

		$document[$numberDocument] = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

		//Находим для оборудования данные (сведетельство о поверке)
		$res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$sql['ID']);
		$row_certificate = $res_certificate_select->Fetch();
		$certificateName_arr = unserialize($row_certificate['name_f']);

		$cert_arr=array();

		foreach($certificateName_arr as $cName)
		{
			$cert_arr[]=$cName;
		}
		$ob_v = ($sql['certificate_of_verification'] ? $sql['certificate_of_verification'] : '-');
		//Находим последний результат поверки (берем последний элемент массива)
		//Наименование проверки
		$certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v;

		if (in_array($row_certificate['ID'], ['188','189'])) {
			//Убираем переносы строк и заменяем на пробелы
			$certificateName = str_replace(array("\r\n", "\r", "\n"), ' ', $certificateName);

			//echo'<pre>';var_dump($certificateName);echo'</pre>';
		}


		//Наименование, тип (марка)
		$ident='';
		switch ($sql['IDENT']) {
			case 'IO':
				$ident='ИО';
				break;
			case 'VO':
				$ident='ВО';
				break;
			case 'TS':
				$ident='ТС';
				break;
			case 'SI':
				$ident='СИ'; //Системы измерения
				break;
			case 'SO':
				$ident='СО';
				break;
			case 'REACT':
				$ident='реактив';
				break;
			case 'OOPP':
				$ident='ООПП';
				break;
		}
		switch ($sql['METR_CONTROL']) {
			case 'CALIBR':
				$metr_control='Калибровка';
				break;
			case 'ATTESTATION':
				$metr_control='Аттестация';
				break;
			case 'POVERKA':
				$metr_control='Поверка';
				break;
			case 'TECH_CHAR':
				$metr_control='Проверка тех. харктеристик';
				break;
		}

		//Находим ответственного
		$assign_id = CUser::GetByID($sql['ID_ASSIGN1'])->Fetch();
		$assigned="";
		//Если ответственный существует находим Имя и Фамилию
		if($assign_id['ID']) {
			$assigned = $assign_id['NAME']." ".$assign_id['LAST_NAME'];
		} else {
			$assigned = '-';
		}

		//Если данные оборудования по 10 карточкам записали и счетчик($quantity) обнулился, очищаем массив с данными полей(arrayDataSetValues) для следующих 10-ти карточек
		if ($quantity == 0) {
			$arrayDataSetValues = [];
		}
		if ($quantity < 10) {
			$arrayDataSetValues['OBJECT'.$quantity] = !empty($sql['OBJECT']) ? $sql['OBJECT'] : '-'; //Наименование

			$arrayDataSetValues['TYPE_OBORUD'.$quantity] = !empty($sql['TYPE_OBORUD']) ? $sql['TYPE_OBORUD'] : '-'; //тип (марка)

			$arrayDataSetValues['IDENT'.$quantity] = !empty($ident) ? $ident : '-'; //Наименование, тип (марка)

			$arrayDataSetValues['REG_NUM_ZAV'.$quantity] = !empty($sql['FACTORY_NUMBER']) ? str_replace('№', '', $sql['FACTORY_NUMBER']) : '-'; //Заводской  номер

			$arrayDataSetValues['REG_NUM'.$quantity] = !empty($sql['REG_NUM']) ? str_replace('№', '', $sql['REG_NUM']) : '-'; //Инвентарный номер

			//Инвертарный номер для штрихкода
			$arrayDataSetValues['BARCODE'.$quantity] = '';
			if (!empty($sql['REG_NUM'])) {
				$arrayDataSetValues['BARCODE'.$quantity] = translit($sql['REG_NUM']);
			}

			$arrayDataSetValues['ASSIGNED'.$quantity] = !empty($assigned) ? $assigned : '-'; //Ответственный

			$arrayDataSetValues['SERT'.$quantity] = !empty($certificateName) ? $certificateName : '-'; //Свидетельство о поверке (сертификат о калибровке) №

			//$arrayDataSetValues['POVERKA'.$quantity] = ($sql['POVERKA_LAST'] ? date("d.m.Y", strtotime($sql['POVERKA_LAST'])) : "-"); //до какой даты
			//$arrayDataSetValues['POVERKA'.$quantity] = ($sql['POVERKA'] ? date("d.m.Y", strtotime($sql['POVERKA'])) : "-"); //до какой даты
			$arrayDataSetValues['POVERKA'.$quantity] = (($sql['POVERKA'] && $sql['POVERKA'] != '1970-01-01') ? date("d.m.Y", strtotime($sql['POVERKA'])) : "-"); //до какой даты

			$arr_cert_temp = unserialize($sql['cert_temp']);
			$str_c_t = '<br>Аттестованные t. °C ';
			$ii = 0;
			$end = count($arr_cert_temp);
			foreach($arr_cert_temp as $kk => $vv) {
				if(++$ii == $end) {
					$str_c_t .= ( $vv); // для последней итерации не выводим запятую
				}else{
					$str_c_t .= ( $vv . ', ' );
				}
			}

			// выводим только для ИО
			// и для тех оборудований, где занесены аттестованные температуры
			if($ident == 'ИО')
				if(!empty($arr_cert_temp))
					$arrayDataSetValues['ATTEST_TEMP'.$quantity] = $str_c_t;
		}

		$quantity++;

		//Если количество заполненых карточек = 10 или количество этераций равна последниму элементу $i == $amountQquipment ,
		//записываем массив данных в документ и сохраняем, обнуляем счетчик $quantity для следующих 10 карточек, уыеличиваем номер следующего файла $number_file

		if($sql == end($res)){
			// echo('конец второго foreach<br>');
			if($valIdent == end($arrayIdent)){
				// echo('конец первого foreach<br>');

				$document[$numberDocument]->normalizeContent();
				$document[$numberDocument]->setValues($arrayDataSetValues);
				$result = $document[$numberDocument]->process();
				$content = $document[$numberDocument]->getContent();

				$newpath = "archive_card_laba/" . $ident . "_" . $number_file . ".docx";
				file_force_contents($newpath, $content);

				$number_file++;
				$quantity = 0;
			}
		}
		if ($quantity == 10 || $i == $amountQquipment) {

			$document[$numberDocument]->normalizeContent();
			$document[$numberDocument]->setValues($arrayDataSetValues);
			$result = $document[$numberDocument]->process();
			$content = $document[$numberDocument]->getContent();

			$newpath = "archive_card_laba/" . $ident . "_" . $number_file . ".docx";

			file_force_contents($newpath, $content);

			$number_file++;
			$quantity = 0;
		}

		//Счетчик для посчета количества Элементов(этераций) для каждого типа(марки) оборудования
		$i++;
		//Номер формирования документа по счету, для создания нового экземляря класса
		$numberDocument++;

	}
}

// для работы с ZIP-файлами
if(extension_loaded('zip')) {
  $zip = new ZipArchive();
  $zip_name = date('d-m.Y')." - Marki.zip";

  if($zip->open($zip_name, ZipArchive::CREATE) !== TRUE) {
    $error .= "* К сожалению, создание ZIP-архива в данный момент не удалось";
  }

  $dirFile = 'archive_card_laba/';
  $filesPasspotsRc = scandir($dirFile);

	$arr_files = [];
  foreach($filesPasspotsRc as $file) {
    if($file == '.' || $file == '..') {
      continue;
    }

    $zip->addFile($dirFile.DIRECTORY_SEPARATOR.$file, $file);
		$arr_files[] = $dirFile.DIRECTORY_SEPARATOR.$file;
  }

  $zip->close();

  if(file_exists($zip_name))
	{
		// отдаём файл на скачивание
		header('Content-type: application/zip');
		header('Content-Disposition: attachment; filename="'.$zip_name.'"');
		readfile($zip_name);
		// удаляем zip файл если он существует
		unlink($zip_name);
		foreach($arr_files as $k => $v) {
			unlink($v);
		}
	}else{
    echo('<br>');
    echo('файл zip не найден');
  }

}else{
	$error .= "* У вас нет расширения ZIP";
}
echo($error);

echo 'Карточки оборудования сформированы!';

// вывод непосредственно в браузер
//header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
//header('Content-Disposition: attachment;filename="Карточка оборудования №'.$sql['ID'].'.docx"');
//header('Cache-Control: max-age=0');
//echo $content;
die;
