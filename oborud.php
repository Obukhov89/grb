<?php
//header("Location: /ulab/oborud/list/");
/**
 * Created by PhpStorm.
 * User: User
 * Date: 28.03.2019
 * Time: 10:12
 */

?>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
include $_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php';
$DB_Conn = new CDB();
?>
<?php
$APPLICATION->SetTitle("Оборудование");
$APPLICATION->SetTitle("Оборудование");

?>
    <!-- <style>


         #oborud_table {
             border: 1px solid grey
         }
     </style>-->
<?php
if (isset($_POST['ID'])) {
	/*-------------------'Не нормируется' начало---------------------*/
    //Проверка Темпиратура, Влажность, Давление отмечен checkbox 'Не нормируется' или нет
    $temperature = ((!empty($_POST['checkTemperature']) && $_POST['checkTemperature'] == 'on') ? (int)1 : '');
    $humidity = ((!empty($_POST['checkHumidity']) && $_POST['checkHumidity'] == 'on') ? (int)1 : '');
    $pressure = ((!empty($_POST['checkPressure']) && $_POST['checkPressure'] == 'on') ? (int)1 : '');
    /*-------------------'Не нормируется' окончание-------------------*/

    if (!empty($_POST['ID'])) {
        $res = $DB->Query("UPDATE `ba_oborud` SET
        `NAME`='" . $_POST['NAME'] . "',
        `OBJECT`='" . $_POST['OBJECT'] . "',
        `TYPE_OBORUD`='" . $_POST['TYPE_OBORUD'] . "',
        `REG_NUM`='" . $_POST['REG_NUM'] . "',
        `POVERKA`='" . date("Y-m-d", strtotime($_POST['POVERKA'])) . "',
        `manufacturer`='" . $_POST['manufacturer'] . "',
        `god_vvoda_expluatation`='" .  date("Y-m-d", strtotime($_POST['god_vvoda_expluatation'])) . "',
        `measuring_range`='" . $_POST['measuring_range'] . "',
        `сlass_precision_and_accuracy`='" . $_POST['сlass_precision_and_accuracy'] . "',
        `certificate_of_verification`='" . $_POST['certificate_of_verification'] . "',
        `property_rights`='" . $_POST['property_rights'] . "',
        `place_of_installation_or_storage`='" . $_POST['place_of_installation_or_storage'] . "',
        `IN_AREA`='" . (($_POST['IN_AREA'] == 'on') ? 1 : 0) . "',
        `IN_STOCK`='" . (($_POST['IN_STOCK'] == 'on') ? 1 : 0) . "',
        `CHECKED`='" . (($_POST['CHECKED'] == 'on') ? 1 : 0) . "',
		`NO_METR_CONTROL`='" . (($_POST['NO_METR_CONTROL'] == 'on') ? 1 : 0) . "',
		`is_portable`='" . (($_POST['is_portable'] == 'on') ? 1 : 0) . "',
		`LONG_STORAGE`='" . (($_POST['LONG_STORAGE']) ? $_POST['LONG_STORAGE_DATE'] : 0) . "',
		`LONG_STORAGE_DATE`='" . (($_POST['LONG_STORAGE_DATE']) ? $_POST['LONG_STORAGE_DATE'] : '') . "',
        `FACTORY_NUMBER`='" . $_POST['FACTORY_NUMBER'] . "',
        `GOSREESTR`='" . $_POST['GOSREESTR'] . "',
		`IDENT`='" . $_POST['IDENT'] . "',
		`OBJECT_GROUP`='" . $_POST['OBJECT_GROUP'] . "',
		`TECH`='" . $_POST['TECH'] . "',
		`OVV_EX`='" . $_POST['OVV_EX'] . "',
		`TOO_EX`='" . $_POST['TOO_EX'] . "',
		`AD_EX`='" . $_POST['AD_EX'] . "',
		`OVV_EX2`='" . $_POST['OVV_EX2'] . "',
		`TOO_EX2`='" . $_POST['TOO_EX2'] . "',
		`AD_EX2`='" . $_POST['AD_EX2'] . "',
		`OVV_UH`='" . $_POST['OVV_UH'] . "',
		`TOO_UH`='" . $_POST['TOO_UH'] . "',
		`AD_UH`='" . $_POST['AD_UH'] . "',
		`YEAR`='" . $_POST['YEAR'] . "',
		`NUM_PRIKAZ`='" . $_POST['NUM_PRIKAZ'] . "',
		`DATE_PRIKAZ`='" . $_POST['DATE_PRIKAZ'] . "',
		`ASSIGNED`='" . $_POST['ASSIGNED'] . "',
		`SPISANIE`='" . $_POST['SPISANIE'] . "',
		`DATE_SP`='" . $_POST['DATE_SP'] . "',
		`MC_INTERVAL`='" . $_POST['MC_INTERVAL'] . "',
		`REG_NUM_RC`='" . $_POST['REG_NUM_RC'] . "',
		`COND`='" . $_POST['COND'] . "',
		`METR_CONTROL`='" . $_POST['METR_CONTROL'] . "',
		`note`='" . $_POST['note'] . "',
		`POVERKA_PLACE`='" . $_POST['POVERKA_PLACE'] . "',
		`POVERKA_LAST`='" . $_POST['POVERKA_LAST'] . "',
		`ID_ASSIGN1`='" . $_POST['ID_ASSIGN1'] . "',
		`ID_ASSIGN2`='" . $_POST['ID_ASSIGN2'] . "',
		`PO_NAME`='" . $_POST['PO_NAME'] . "',
		`PO_ID`='" . $_POST['PO_ID'] . "',
		`NAPR`='" . $_POST['NAPR'] . "',
		`FREQ`='" . $_POST['FREQ'] . "',
		`DOCUMENT`='" . $_POST['document'] . "',
        `roomnumber`='" . $_POST['roomnumber'] . "',
        `TEMPERATURE`='" . $temperature . "',
        `HUMIDITY`='" . $humidity . "',
        `PRESSURE`='" . $pressure . "'
        WHERE `ID`=" . (int)$_GET['ID']);
        //-------------------'Не нормируется' начало--------------------- со строки `TEMPERATURE`='" . $temperature . "',
        /*-------------------'Не нормируется' окончание-------------------*/
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }

		$res_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$_GET['ID']);
		$row_select = $res_select->Fetch();

		$delFile = [];
		if (isset($_POST["hiddenDelPdf"]) && !empty($_POST["hiddenDelPdf"])) {
			foreach($_POST["hiddenDelPdf"] as $keyDelPdf => $valDelPdf) {
				if ($valDelPdf) {
					$delFile[$keyDelPdf] = $_SERVER['DOCUMENT_ROOT'] . $valDelPdf;
					unlink($_SERVER['DOCUMENT_ROOT'].$valDelPdf);
				}
			}
		}

        if (!empty($_POST["hiddenDelPhoto"])) {
            $valDelPhoto = $_POST["hiddenDelPhoto"];
            unlink($_SERVER['DOCUMENT_ROOT'] . $valDelPhoto);
            $DB->Query("UPDATE `ba_oborud` SET `photo_oborud` = NULL WHERE `ID` = {$_GET['ID']}");
        }

        if (!empty($_POST["hiddenDelDesc"])) {
            $valDelPhoto = $_POST["hiddenDelDesc"];
            unlink($_SERVER['DOCUMENT_ROOT'] . $valDelPhoto);
            $DB->Query("UPDATE `ba_oborud` SET `desc_oborud` = '' WHERE `ID` = {$_GET['ID']}");
        }

        if (!empty($_POST["hiddenDelProp"])) {
            $valDelProp = $_POST["hiddenDelProp"];
            unlink($_SERVER['DOCUMENT_ROOT'] . $valDelProp);
            $DB->Query("UPDATE `ba_oborud` SET `property_rights_pdf` = '' WHERE `ID` = {$_GET['ID']}");
        }
        if (!empty($_POST["hiddenDelPassport"])) {
            $valDelProp = $_POST["hiddenDelPassport"];
            unlink($_SERVER['DOCUMENT_ROOT'] . $valDelProp);
            $DB->Query("UPDATE `ba_oborud` SET `passport_pdf` = '' WHERE `ID` = {$_GET['ID']}");
        }



		$res_count_id = $DB->Query("SELECT `id_oborud` FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$_GET['ID']);
		$row_count_id = $res_count_id->Fetch();

		//--Дата начала и окончания для каждого св-ва в карточке оборудования начало--//
		//Если у оборудования не заполнено свидетельсьво о поверке и нет данных в таблице b_verification_certificate(свидетельство о поверке), вставляем заполненые данные в таблице b_verification_certificate
		if(empty($row_count_id['id_oborud'])) {
			//echo'<pre>';var_dump('$_FILES', $_FILES);echo'</pre>';die();
			//Получаем из заполненых данных массив наименований свидетельства и сответсвующий сведетельству путь загруженного файла и дату начала и окончания поверки
			list($arrFieldsCertificate) = saveCertificateOborud($_POST, $_FILES);

			$names = []; //Наименования свидетельств о поверки
			$files = []; //Пути расположения загруженных файлов
			$dateStartVerification = []; //Даты начала поверки
			$dateEndVerification = []; //Даты окончания поверки
			//$relevanceСertificate = []; //Актуальность свидетельств о поверки

			for ($i = 0; $i < count($arrFieldsCertificate); $i++) {

				//Сохраняем отдельно Наименования свидетельств о поверки для сохранения в БД
				if (!empty($arrFieldsCertificate[$i]['name'])) {
					$names[$i] = $arrFieldsCertificate[$i]['name'];
				}

				//Сохраняем отдельно Даты начала поверки для сохранения в БД
				if (!empty($arrFieldsCertificate[$i]['date_start_verification'])) {
					$dateStartVerification[$i] = $arrFieldsCertificate[$i]['date_start_verification'];
				}

				//Сохраняем отдельно Даты окончания поверки для сохранения в БД
				if (!empty($arrFieldsCertificate[$i]['date_end_verification'])) {
					$dateEndVerification[$i] = $arrFieldsCertificate[$i]['date_end_verification'];
				}

				//Сохраняем отдельно Пути расположения загруженных файловы для сохранения в БД
				if (!empty($arrFieldsCertificate[$i]['file'])) {
					$files[$i] = $arrFieldsCertificate[$i]['file'];
				}

				//Сохраняем отдельно Актуальность свидетельств о поверки для сохранения в БД
				/*if (isset($arrFieldsCertificate[$i]['relevance_certificate'])) {
					$relevanceСertificate[$i] = $arrFieldsCertificate[$i]['relevance_certificate'];
				}*/
			}

			//Сериализуем данные массивов для сохранения в БД
			$serializeNames = !empty($names) ? serialize($names) : ''; //Наименования свидетельств о поверки
			$serializeFiles = !empty($files) ? serialize($files) : '';; //Пути расположения загруженных файлов
			$serializeDateStartVerification = !empty($dateStartVerification) ? serialize($dateStartVerification) : '';; //Даты начала поверки
			$serializeDateEndVerification = !empty($dateEndVerification) ? serialize($dateEndVerification) : '';; //Даты окончания поверки
			//$serializeRelevanceСertificate = isset($relevanceСertificate) ? serialize($relevanceСertificate) : '';; //Актуальность свидетельств о поверки
			$relevanceСertificate = isset($_POST['relevance_certificate']) ? $_POST['relevance_certificate'] : '0'; //Актуальность свидетельств о поверки

			//Сохраняем данные свидетельств о поверки
			if (!empty($_GET['ID'])) {
				//$res_certificate = $DB->Query("INSERT INTO `b_verification_certificate` (`name_f`, `file_address`, `id_oborud`, `date_start_verification`, `date_end_verification`, `relevance_certificate`)
				//VALUES ('" . $serializeNames . "', '" . $serializeFiles . "', '" . (int)$_GET['ID'] . "', '" . $serializeDateStartVerification . "', '" . $serializeDateEndVerification . "', '" . $serializeRelevanceСertificate . "')");

				$res_certificate = $DB->Query("INSERT INTO `b_verification_certificate` (`name_f`, `file_address`, `id_oborud`, `date_start_verification`, `date_end_verification`, `relevance_certificate`)
				VALUES ('" . $serializeNames . "', '" . $serializeFiles . "', '" . (int)$_GET['ID'] . "', '" . $serializeDateStartVerification . "', '" . $serializeDateEndVerification . "', '" . $relevanceСertificate . "')");
			}

			/*if (!empty($names) && !empty($files)) {
				$res_certificate = $DB->Query("INSERT INTO `b_verification_certificate` (`name_f`, `file_address`, `id_oborud`)
				VALUES ('" . serialize($names) . "', '" . serialize($files) . "', '" . (int)$_GET['ID'] . "')");
			} elseif (!empty($names) || empty($files)) {
				$res_certificate = $DB->Query("INSERT INTO `b_verification_certificate` (`name_f`, `id_oborud`)
				VALUES ('" . serialize($names) . "', '" . (int)$_GET['ID'] . "')");
			} elseif (empty($names) || !empty($files)) {
				$res_certificate = $DB->Query("INSERT INTO `b_verification_certificate` (`file_address`, `id_oborud`)
				VALUES ('" . serialize($files) . "', '" . (int)$_GET['ID'] . "')");
			}*/
		} else {
			$res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$_GET['ID']);
			$row_certificate = $res_certificate_select->Fetch();

			//$certificateName = unserialize($row_certificate['name']);
			//$certificateAddress = unserialize($row_certificate['file_address']);
			$certificateName = !empty($row_certificate['name_f']) ? unserialize($row_certificate['name_f']) : ''; //Наименования свидетельств о поверки
			$certificatePlace = !empty($row_certificate['name_f']) ? unserialize($row_certificate['name_f']) : ''; //Наименования свидетельств о поверки
			$certificateAddress = !empty($row_certificate['file_address']) ? unserialize($row_certificate['file_address']) : ''; //Пути расположения загруженных файлов
			$certificateDateStartVerification = !empty($row_certificate['date_start_verification']) ? unserialize($row_certificate['date_start_verification']) : ''; //Даты начала поверки
			$certificateDateEndVerification = !empty($row_certificate['date_end_verification']) ? unserialize($row_certificate['date_end_verification']) : ''; //Даты окончания поверки
			//$certificateRelevanceСertificate = !empty($row_certificate['relevance_certificate']) ? unserialize($row_certificate['relevance_certificate']) : ''; //Актуальность свидетельств о поверки

			//$names = [];
			//$files = [];
			$names = []; //Наименования свидетельств о поверки
			$files = []; //Пути расположения загруженных файлов
			$dateStartVerification = []; //Даты начала поверки
			$dateEndVerification = []; //Даты окончания поверки
			$relevanceСertificate = []; //Актуальность свидетельств о поверки

			/*echo'<pre>';var_dump('$_POST[certificate]', $_POST['certificate']);echo'</pre>';
			echo'<pre>';var_dump('$certificateName', $certificateName);echo'</pre>';
			echo'<pre>';var_dump('$certificateAddress', $certificateAddress);echo'</pre>';
			echo'<pre>';var_dump('$certificateDateStartVerification', $certificateDateStartVerification);echo'</pre>';
			echo'<pre>';var_dump('$certificateDateEndVerification', $certificateDateEndVerification);echo'</pre>';
			echo'<pre>';var_dump('$certificateRelevanceСertificate', $certificateRelevanceСertificate);echo'</pre>';*/
			//echo'<pre>';var_dump('$_POST', $_POST['relevance_certificate']);echo'</pre>';die();
			/*$f = 0;
			foreach ($certificateName as $key => $name) {
				if (is_int($key)) {
					if (isset($_POST['certificate'][$key]['name'])) {
						$names[$key] = $_POST['certificate'][$key]['name'];
					}
				} else {
					if (isset($_POST['certificate'][$f]['name'])) {
						$names[$f] = $_POST['certificate'][$f]['name'];
					}
				}
				$f++;
			}*/

			//Функция перебора данных вертификата из БД и заменяем на те данные которые нам пришли из $_POST, в новый массив $names
			function foreachCertificateTable($certificate, $post, $array, $data) {
				foreach ($certificate as $key => $val) {

					if (isset($post['certificate'][$key][$data])) {
						$array[$key] = $post['certificate'][$key][$data];
					}

				}
			}

			//Перебираем данные Наименования свидетельств о поверки из БД и заменяем на те данные которые нам пришли из $_POST, в новый массив $names (если были удалены Наименования свидетельств о поверки, то не не будет записи в новый массив $names)
			/*foreach ($certificateName as $key => $name) {

				if (isset($_POST['certificate'][$key]['name'])) {
					$names[$key] = $_POST['certificate'][$key]['name'];
				}

			}*/
			foreachCertificateTable($certificateName, $_POST, $names, 'name');


			//Перебираем данные Даты начала поверки из БД и заменяем на те данные которые нам пришли из $_POST, в новый массив $dateStartVerification (если были удалены Наименования свидетельств о поверки, то не не будет записи в новый массив $dateStartVerification)
			/*foreach ($certificateDateStartVerification as $keyDateStart => $DateStart) {

				if (isset($_POST['certificate'][$keyDateStart]['date_start_verification'])) {
					$dateStartVerification[$keyDateStart] = $_POST['certificate'][$keyDateStart]['date_start_verification'];
				}

			}*/
			foreachCertificateTable($certificateDateStartVerification, $_POST, $dateStartVerification, 'date_start_verification');

			//Перебираем данные Даты окончания поверки из БД и заменяем на те данные которые нам пришли из $_POST, в новый массив $dateEndVerification (если были удалены Наименования свидетельств о поверки, то не не будет записи в новый массив $dateEndVerification)
			/*foreach ($certificateDateEndVerification as $keyDateEnd => $DateEnd) {

				if (isset($_POST['certificate'][$keyDateEnd]['date_end_verification'])) {
					$dateEndVerification[$keyDateEnd] = $_POST['certificate'][$keyDateEnd]['date_end_verification'];
				}

			}*/
			foreachCertificateTable($certificateDateEndVerification, $_POST, $dateEndVerification, 'date_end_verification');

			//Перебираем данные Актуальность свидетельств о поверки из БД и заменяем на те данные которые нам пришли из $_POST, в новый массив $relevanceСertificate
			/*foreach ($certificateRelevanceСertificate as $keyRelevanceСertificate => $valRelevanceСertificate) {

				if (isset($_POST['certificate'][$keyRelevanceСertificate]['relevance_certificate'])) {
					$relevanceСertificate[$keyRelevanceСertificate] = $_POST['certificate'][$keyRelevanceСertificate]['relevance_certificate'];
				}

			}*/
			//foreachCertificateTable($certificateRelevanceСertificate, $_POST, $relevanceСertificate, 'relevance_certificate');

			//Перебираем данные свидетельств о поверки из $_POST и заменяем или добавляем данные в новом массиве $names,dateStartVerification на те данные которые нам пришли из $_POST(на случай если добавили новые данные свидетельств о поверке)
			foreach ($_POST['certificate'] as $key => $val) {
				//Данные Наименований свидетельств о поверки
				if (isset($val['name'])) {
					$names[$key] = $val['name'];
				}

				//Данные Дат начала поверки
				if (isset($val['date_start_verification'])) {
					$dateStartVerification[$key] = $val['date_start_verification'];
				}

				//Данные Дат окончания поверки
				if (isset($val['date_end_verification'])) {
					$dateEndVerification[$key] = $val['date_end_verification'];
				}

				//Актуальность свидетельств о поверки
				/*if (isset($val['relevance_certificate'])) {
					$relevanceСertificate[$key] = $val['relevance_certificate'];
				}*/
			}

			//Удаляем пустые значения
			function unsetEmptyValue($array) {
				foreach ($array as $keyArray => $valArray) {
					if (empty($valArray)) {
						unset($array[$keyArray]);
					}
				}
			}

			//Перебираем данные Наименования свидетельств о поверки из нового массива $names и удаляем пустые
			/*foreach ($names as $keyName => $valName) {
				if (empty($valName)) {
					unset($names[$keyName]);
				}
			}*/
			unsetEmptyValue($names);

			//Перебираем данные Даты начала поверки из нового массива $dateStartVerification и удаляем пустые
			/*foreach ($dateStartVerification as $keyDateStart => $valDateStart) {
				if (empty($valDateStart)) {
					unset($dateStartVerification[$keyDateStart]);
				}
			}*/
			unsetEmptyValue($dateStartVerification);

			//Перебираем данные Даты начала поверки из нового массива $dateStartVerification и удаляем пустые
			/*foreach ($dateEndVerification as $keyDateEnd => $valDateEnd) {
				if (empty($valDateEnd)) {
					unset($dateEndVerification[$keyDateEnd]);
				}
			}*/
			unsetEmptyValue($dateEndVerification);

			//Перебираем данные Актуальность свидетельств о поверки из нового массива $relevanceСertificate и удаляем пустые
			/*foreach ($relevanceСertificate as $keyRelevanceСertificate => $valRelevanceСertificate) {
				if (isset($valRelevanceСertificate) && $valRelevanceСertificate == '') {
					unset($relevanceСertificate[$keyRelevanceСertificate]);
				}
			}*/
			//unsetEmptyValue($relevanceСertificate);

			$a = 0;

			$certificatePdf = unserialize($_POST['certificatePdf']);

			foreach ($certificatePdf as $key => $name) {
				if (is_int($key)) {
					if (isset($_FILES['certificate']['tmp_name'][$key]['file']) && empty($_FILES['certificate']['tmp_name'][$key]['file'])) {
						$files[$key] = $name;
					} else {
						unset($delFile[$key]);
					}
				} else {
					if (isset($_FILES['certificate']['tmp_name'][$a]['file']) && empty($_FILES['certificate']['tmp_name'][$a]['file'])) {
						$files[$a] = $name;
					} else {
						unset($delFile[$a]);
					}
				}

				$a++;
			}

			for ($i = 0; $i <= max(array_keys($_FILES['certificate']['tmp_name'])); $i++) {
				if (!isset($_FILES['certificate']['tmp_name'][$i]['file'])) {
					continue;
				}

				if(empty($_FILES['certificate']['tmp_name'][$i]['file'])){
					continue;
				}

				$uploadfile = uploadFileOborud($i);

				$files[$i] = $uploadfile;
			}

			$h = 0;
			if (!empty($files)) {
				foreach ($files as $key => $name) {
					if (is_int($key)) {
						if (isset($delFile[$key]) && !empty($delFile[$key])) {
							unset($files[$key]);
						}
					} else {
						if (isset($delFile[$h]) && !empty($delFile[$h])) {
							unset($files[$key]);
						}
					}
					$h++;
				}
			}

			foreach ($files as $keyFiles => $valFiles) {
				if (empty($valFiles)) {
					unset($files[$keyFiles]);
				}
			}

			//echo'<pre>';var_dump('$names', $names);echo'</pre>';
			//echo'<pre>';var_dump('$files', $files);echo'</pre>';
			//echo'<pre>';var_dump('$dateStartVerification', $dateStartVerification);echo'</pre>';
			//echo'<pre>';var_dump('$dateEndVerification', $dateEndVerification);echo'</pre>';
			//echo'<pre>';var_dump('$relevanceСertificate', $relevanceСertificate);echo'</pre>';

			//Сериализуем данные массивов для сохранения в БД
			$serializeNames = !empty($names) ? serialize($names) : ''; //Наименования свидетельств о поверки
			$serializeFiles = !empty($files) ? serialize($files) : ''; //Пути расположения загруженных файлов
			$serializeDateStartVerification = !empty($dateStartVerification) ? serialize($dateStartVerification) : ''; //Даты начала поверки
			$serializeDateEndVerification = !empty($dateEndVerification) ? serialize($dateEndVerification) : ''; //Даты окончания поверки
			//$serializeRelevanceСertificate = isset($relevanceСertificate) ? serialize($relevanceСertificate) : ''; //Актуальность свидетельств о поверки
			$relevanceСertificate = isset($_POST['relevance_certificate']) ? $_POST['relevance_certificate'] : '0'; //Актуальность свидетельств о поверки


			//echo'<pre>';var_dump('$serializeNames', $serializeNames);echo'</pre>';
			//echo'<pre>';var_dump('$serializeFiles', $serializeFiles);echo'</pre>';
			//echo'<pre>';var_dump('$serializeDateStartVerification', $serializeDateStartVerification);echo'</pre>';
			//echo'<pre>';var_dump('$serializeDateEndVerification', $serializeDateEndVerification);echo'</pre>';
			//echo'<pre>';var_dump('$serializeRelevanceСertificate', $serializeRelevanceСertificate);echo'</pre>';
			//echo'<pre>';var_dump('$relevanceСertificate', $relevanceСertificate);echo'</pre>';

			//Обновляем данные свидетельств о поверки
			if (!empty($_GET['ID'])) {
				//$resCertificate = $DB->Query("UPDATE `b_verification_certificate` SET `name_f`='" . $serializeNames . "',`file_address`='" . $serializeFiles . "', `date_start_verification`='" . $serializeDateStartVerification . "', `date_end_verification`='" . $serializeDateEndVerification . "', `relevance_certificate`='" . $serializeRelevanceСertificate . "' WHERE `id_oborud`=" . (int)$_GET['ID']);
				$resCertificate = $DB->Query("UPDATE `b_verification_certificate` SET `name_f`='" . $serializeNames . "',`file_address`='" . $serializeFiles . "', `date_start_verification`='" . $serializeDateStartVerification . "', `date_end_verification`='" . $serializeDateEndVerification . "', `relevance_certificate`='" . $relevanceСertificate . "' WHERE `id_oborud`=" . (int)$_GET['ID']);
			}

			/*if (!empty($names) && !empty($files)) {
				$res_certificate = $DB->Query("UPDATE `b_verification_certificate` SET `name_f`='" . serialize($names) . "',`file_address`='" . serialize($files) . "' WHERE `id_oborud`=" . (int)$_GET['ID']);
			} elseif (!empty($names) && empty($files)) {
				$res_certificate = $DB->Query("UPDATE `b_verification_certificate` SET `name_f`='" . serialize($names) . "',`file_address`='' WHERE `id_oborud`=" . (int)$_GET['ID']);
			} elseif (empty($names) && !empty($files)) {
				$res_certificate = $DB->Query("UPDATE `b_verification_certificate` SET `name_f`='',`file_address`='" . serialize($files) . "' WHERE `id_oborud`=" . (int)$_GET['ID']);
			} elseif (empty($names) && empty($files)) {
				$res_certificate = $DB->Query("UPDATE `b_verification_certificate` SET `name_f`='',`file_address`='' WHERE `id_oborud`=" . (int)$_GET['ID']);
			}*/
		}

        // вставка описание типа оборудования в таблицу ba_oborud
        // хранится путь до файла - поле desc_oborud
        if (!empty($_FILES['desc_oborud_file'] && $_FILES['desc_oborud_file']['error'] == 0)) {
            $uploadDescFileDir = $_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $_GET['ID'];
            $uploadDescFileDirDesc = $_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $_GET['ID'] . '/description_oborud/';
            $uploadDescFile = $uploadDescFileDirDesc . $_FILES['desc_oborud_file']['name'];
			if ( !is_dir($uploadDescFileDirDesc) ) {
				mkdir($uploadDescFileDirDesc, 0777, true);
//				if ( !is_dir($uploadDescFileDirDesc) ) {
//					mkdir($uploadDescFileDirDesc);
//				}
			}


			$sqlTot = "UPDATE `ba_oborud` SET desc_oborud = {?} WHERE ID = {?}";
            $sqlArr = array($_FILES['desc_oborud_file']['name'], (int)$_GET['ID']);
            $updateFileDescObor = $DB_Conn->queryExec($sqlTot, $sqlArr);
            if (move_uploaded_file($_FILES['desc_oborud_file']['tmp_name'], $uploadDescFile)) {
                //echo('Файл описания оборудования успешно загружен!<br>');
            } else {
                echo('Файл описания оборудования не загрузился из-за ошибки<br>' . $uploadDescFile);
            }
        }
        //вставка фото и сканов оборудования
        if (!empty($_FILES['photo_oborud_file'] && $_FILES['photo_oborud_file']['error'] == 0)) {
            $uploadPhotoFileDir = $_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $_GET['ID'];
            $uploadPhotoFile = $uploadPhotoFileDir . '/' . $_FILES['photo_oborud_file']['name'];
            if ( !is_dir($uploadPhotoFileDir) ) {
                mkdir($uploadPhotoFileDir);
            }

            $pattern = "/\.(pdf)$/i";
            if (preg_match($pattern, $_FILES['photo_oborud_file']['name']) ) {
                $sqlTot = "UPDATE `ba_oborud` SET photo_oborud = {?} WHERE ID = {?}";
                $sqlArr = array($_FILES['photo_oborud_file']['name'], (int)$_GET['ID']);
                $updateFilePhotoObor = $DB_Conn->queryExec($sqlTot, $sqlArr);

                if (move_uploaded_file($_FILES['photo_oborud_file']['tmp_name'], $uploadPhotoFile)) {
                    echo ('Файл описания оборудования успешно загружен!<br>');
                } else {
                    echo ('Файл описания оборудования не загрузился из-за ошибки<br>');
                }
            } else {
                echo ('Формат файла несоответствует<br>');
            }
        }

        if (!empty($_FILES['property_rights_pdf'] && $_FILES['property_rights_pdf']['error'] == 0)) {
            $uploadPropFileDir = $_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $_GET['ID'] . '/';
            $uploadPropFile = $uploadPropFileDir . $_FILES['property_rights_pdf']['name'];
            if ( !is_dir($uploadPropFileDir) ) {
                mkdir($uploadPropFileDir);
            }

            $pattern = "/\.(pdf)$/i";
            if (preg_match($pattern, $_FILES['property_rights_pdf']['name'])) {
                $sqlTot = "UPDATE `ba_oborud` SET property_rights_pdf = {?} WHERE ID = {?}";
                $sqlArr = array($_FILES['property_rights_pdf']['name'], (int)$_GET['ID']);
                $updateFilePropObor = $DB_Conn->queryExec($sqlTot, $sqlArr);
                if (move_uploaded_file($_FILES['property_rights_pdf']['tmp_name'], $uploadPropFile)) {
                    echo ('Файл описания оборудования успешно загружен!<br>');
                } else {
                    echo ('Файл описания оборудования не загрузился из-за ошибки<br>');
                }
            } else {
                echo ('Формат файла несоответствует<br>');
            }
        }

		if (!empty($_FILES['passport_pdf'] && $_FILES['passport_pdf']['error'][0] == 0)) {
            $jsonPassportFile = $DB->Query("SELECT `passport_pdf` FROM `ba_oborud` WHERE ID = {$_GET['ID']}")->Fetch();
            $PassportFile = json_decode($jsonPassportFile, true);
			for ($i = 0; $i < count($_FILES['passport_pdf']['error']); $i++) {
				if ($_FILES['passport_pdf']['error'][$i] == 0) {
					$uploadPasFileDir = $_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $_GET['ID'] . '/passport/';
					$uploadPasFile = $uploadPasFileDir . $_FILES['passport_pdf']['name'][$i];
					if (!is_dir($uploadPasFileDir)) {
						mkdir($uploadPasFileDir, 0755, true);
					}
				}
				$pattern = "/\.(pdf)$/i";
//				if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
//					echo '<pre>';
//					print_r($_FILES);
//					exit();
//				}
				if (preg_match($pattern, $_FILES['passport_pdf']['name'][$i])) {
					$filesPas[] = $_FILES['passport_pdf']['name'][$i];
					if (move_uploaded_file($_FILES['passport_pdf']['tmp_name'][$i], $uploadPasFile)) {
						echo('Файл описания оборудования успешно загружен!<br>');
					} else {
						echo('Файл описания оборудования не загрузился из-за ошибки<br>');
					}
				} else {
					echo('Формат файла не соответствует<br>');
				}
			}
            $pasFile = json_encode($filesPas, JSON_UNESCAPED_UNICODE);
			$sqlTot = "UPDATE `ba_oborud` SET passport_pdf = {?} WHERE ID = {?}";
			$sqlArr = array($pasFile, (int)$_GET['ID']);
			$updateFilePropObor = $DB_Conn->queryExec($sqlTot, $sqlArr);
		}

        // вставка аттестованных температур
        if (!empty($_REQUEST['certif_temp'])) {
            $id_oborud = (int)$_GET['ID'];
            $arr_cert_temp = serialize($_REQUEST['certif_temp']);

            $sqlTotTemp = "UPDATE `ba_oborud` SET `cert_temp` = {?} where ID = {?}";
            $paramArr = array($arr_cert_temp, $id_oborud);

            $uploadActTemp = $DB_Conn->queryExec($sqlTotTemp, $paramArr);
        }

    } else {
		$numberLaba = $_POST['place_of_installation_or_storage'];

		$numberOborud = $_POST['IDENT'];
		if ($numberLaba > 0) {
			if ($numberLaba == 5 && ($_POST['roomnumber']==9 || $_POST['roomnumber']==10 || $_POST['roomnumber']==11)) {
				$resMaxCardId = $DB->Query("SELECT MAX(`card_id`) as max_card_id FROM `ba_oborud` WHERE `place_of_installation_or_storage`='".$numberLaba."' AND `IDENT`='".$numberOborud."' AND (`roomnumber`=9 OR `roomnumber`=10 OR `roomnumber`=11)")->Fetch();
				$maxCardId = $resMaxCardId["max_card_id"] + 1;
			} else if ($numberLaba == 5 && !($_POST['roomnumber']==9 || $_POST['roomnumber']==10 || $_POST['roomnumber']==11)) {
				$resMaxCardId = $DB->Query("SELECT MAX(`card_id`) as max_card_id FROM `ba_oborud` WHERE `place_of_installation_or_storage`='".$numberLaba."' AND `IDENT`='".$numberOborud."' AND !(`roomnumber`=9 OR `roomnumber`=10 OR `roomnumber`=11)")->Fetch();
				$maxCardId = $resMaxCardId["max_card_id"] + 1;
			}else {
				$resMaxCardId = $DB->Query("SELECT MAX(`card_id`) as max_card_id FROM `ba_oborud` WHERE `place_of_installation_or_storage`='".$numberLaba."' AND `IDENT`='".$numberOborud."'")->Fetch();
				$maxCardId = $resMaxCardId["max_card_id"] + 1;
			}
		} else {
			$maxCardId = 0;
		}



		/*-------------------'Не нормируется' начало---------------------*/
        //Проверка Темпиратура, Влажность, Давление отмечен checkbox 'Не нормируется' или нет
        $temperature = (!empty($_POST['checkTemperature']) && $_POST['checkTemperature'] == 'on' ? (int)1 : '');
        $humidity = (!empty($_POST['checkHumidity']) && $_POST['checkHumidity'] == 'on' ? (int)1 : '');
        $pressure = (!empty($_POST['checkPressure']) && $_POST['checkPressure'] == 'on' ? (int)1 : '');
		$labID = $_POST['place_of_installation_or_storage'] ? $_POST['place_of_installation_or_storage'] : 6;
		$lab = 6;
		switch($labID)
		{
			case 1:
				$lab=1;
				break;
			case 2:
				$lab=4;
				break;
			case 3:
				$lab=3;
				break;
			case 4:
				$lab=2;
				break;
			case 5:
				$lab=5;
				break;
			case 7:
				$lab=7;
				break;
		}
		$y_date = date("Y");
		$yearStart = $y_date."-01-01";

		$resMaxInvNum = $DB->Query("select MAX(INV_NUM) from ba_oborud where place_of_installation_or_storage=".$labID." and god_vvoda_expluatation >='".$yearStart."'")->Fetch();
		$invNum = (int)$resMaxInvNum['MAX(INV_NUM)']+1;
		$newInvNum = "№ ".substr($y_date, 2).".".$lab."-".$invNum;
        /*$res = $DB->Query("INSERT INTO `ba_oborud`
        (`NAME`,`OBJECT`,`TYPE_OBORUD`,`REG_NUM`,`POVERKA`,`manufacturer`,`god_vvoda_expluatation`,`measuring_range`,`сlass_precision_and_accuracy`,`certificate_of_verification`,`property_rights`,`place_of_installation_or_storage`,`IN_AREA`,`IN_STOCK`,`CHECKED`,`FACTORY_NUMBER`,`note`, `roomnumber`, `GOSREESTR`, `IDENT`, `TECH`, `OVV_EX`, `TOO_EX`, `AD_EX`, `OVV_UH`, `TOO_UH`, `AD_UH`, `YEAR`, `REG_NUM_RC`, `COND`, `MC_INTERVAL`, `METR_CONTROL`, `NUM_PRIKAZ`, `DATE_PRIKAZ`, `ASSIGNED`, `SPISANIE`, `DATE_SP`, `POVERKA_LAST`, `POVERKA_PLACE`, `ID_ASSIGN1`, `ID_ASSIGN2`, `OVV_EX2`, `TOO_EX2`, `AD_EX2`, `PO_NAME`, `PO_ID`, `NAPR`, `FREQ` ,`card_id`, `DOCUMENT`)
        VALUES
        ('" . $_POST['NAME'] . "', '" . $_POST['OBJECT'] . "', '" . $_POST['TYPE_OBORUD'] . "', '" . $_POST['REG_NUM'] . "', '" . date("Y-m-d", strtotime($_POST['POVERKA'])) . "', '" . $_POST['manufacturer'] . "', '" . date("Y-m-d", strtotime($_POST['god_vvoda_expluatation'])) . "', '" . $_POST['measuring_range'] . "', '" . $_POST['сlass_precision_and_accuracy'] . "', '" . $_POST['certificate_of_verification'] . "', '" . $_POST['property_rights'] . "', '" . $_POST['place_of_installation_or_storage'] . "','" . (($_POST['IN_AREA'] == 'on') ? 1 : 0) . "','" . (($_POST['IN_STOCK'] == 'on') ? 1 : 0) . "','" . (($_POST['CHECKED'] == 'on') ? 1 : 0) . "', '" . $_POST['FACTORY_NUMBER'] . "', '" . $_POST['note'] . "', '" . $_POST['roomnumber'] . "', '" . $_POST['GOSREESTR'] . "', '" . $_POST['IDENT'] . "', '" . $_POST['TECH'] . "', '" . $_POST['OVV_EX'] . "', '" . $_POST['TOO_EX'] . "', '" . $_POST['AD_EX'] . "', '" . $_POST['OVV_UH'] . "', '" . $_POST['TOO_UH'] . "', '" . $_POST['AD_UH'] . "', '" . $_POST['YEAR'] . "', '" . $_POST['REG_NUM_RC'] . "', '" . $_POST['COND'] . "', '" . $_POST['MC_INTERVAL'] . "', '" . $_POST['METR_CONTROL'] . "', '" . $_POST['NUM_PRIKAZ'] . "', '" . $_POST['DATE_PRIKAZ'] . "', '" . $_POST['ASSIGNED'] . "', '" . $_POST['SPISANIE'] . "', '" . $_POST['DATE_SP'] . "', '" . $_POST['POVERKA_LAST'] . "', '" . $_POST['POVERKA_PLACE'] . "', '" . $_POST['ID_ASSIGN1'] . "', '" . $_POST['ID_ASSIGN2'] . "', '" . $_POST['OVV_EX2'] . "', '" . $_POST['TOO_EX2'] . "', '" . $_POST['AD_EX2'] . "',  '" . $_POST['PO_NAME'] . "',  '" . $_POST['PO_ID'] . "',  '" . $_POST['NAPR'] . "',  '" . $_POST['FREQ'] . "',  '" . $maxCardId . "',  '" . $_POST['document'] . "')");*/

		$res = $DB->Query("INSERT INTO `ba_oborud`
        (`NAME`,`OBJECT`,`TYPE_OBORUD`,`REG_NUM`,`POVERKA`,`manufacturer`,`god_vvoda_expluatation`,`measuring_range`,`сlass_precision_and_accuracy`,`certificate_of_verification`,`property_rights`,`place_of_installation_or_storage`,`IN_AREA`,`IN_STOCK`,`CHECKED`,`FACTORY_NUMBER`,`note`, `roomnumber`, `GOSREESTR`, `IDENT`, `TECH`, `OVV_EX`, `TOO_EX`, `AD_EX`, `OVV_UH`, `TOO_UH`, `AD_UH`, `YEAR`, `REG_NUM_RC`, `COND`, `MC_INTERVAL`, `METR_CONTROL`, `NUM_PRIKAZ`, `DATE_PRIKAZ`, `ASSIGNED`, `SPISANIE`, `DATE_SP`, `POVERKA_LAST`, `POVERKA_PLACE`, `ID_ASSIGN1`, `ID_ASSIGN2`, `OVV_EX2`, `TOO_EX2`, `AD_EX2`, `PO_NAME`, `PO_ID`, `NAPR`, `FREQ` ,`card_id`, `DOCUMENT`, `TEMPERATURE`, `HUMIDITY`, `PRESSURE`, `INV_NUM`, `LONG_STORAGE`, `LONG_STORAGE_DATE`, `OBJECT_GROUP`)
        VALUES
        ('" . $_POST['NAME'] . "', '" . $_POST['OBJECT'] . "', '" . $_POST['TYPE_OBORUD'] . "', '" . $newInvNum . "', '" . date("Y-m-d", strtotime($_POST['POVERKA'])) . "', '" . $_POST['manufacturer'] . "', '" . date("Y-m-d", strtotime($_POST['god_vvoda_expluatation'])) . "', '" . $_POST['measuring_range'] . "', '" . $_POST['сlass_precision_and_accuracy'] . "', '" . $_POST['certificate_of_verification'] . "', '" . $_POST['property_rights'] . "', '" . $_POST['place_of_installation_or_storage'] . "','" . (($_POST['IN_AREA'] == 'on') ? 1 : 0) . "','" . (($_POST['IN_STOCK'] == 'on') ? 1 : 0) . "','" . (($_POST['CHECKED'] == 'on') ? 1 : 0) . "', '" . $_POST['FACTORY_NUMBER'] . "', '" . $_POST['note'] . "', '" . $_POST['roomnumber'] . "', '" . $_POST['GOSREESTR'] . "', '" . $_POST['IDENT'] . "', '" . $_POST['TECH'] . "', '" . $_POST['OVV_EX'] . "', '" . $_POST['TOO_EX'] . "', '" . $_POST['AD_EX'] . "', '" . $_POST['OVV_UH'] . "', '" . $_POST['TOO_UH'] . "', '" . $_POST['AD_UH'] . "', '" . $_POST['YEAR'] . "', '" . $_POST['REG_NUM_RC'] . "', '" . $_POST['COND'] . "', '" . $_POST['MC_INTERVAL'] . "', '" . $_POST['METR_CONTROL'] . "', '" . $_POST['NUM_PRIKAZ'] . "', '" . $_POST['DATE_PRIKAZ'] . "', '" . $_POST['ASSIGNED'] . "', '" . $_POST['SPISANIE'] . "', '" . $_POST['DATE_SP'] . "', '" . $_POST['POVERKA_LAST'] . "', '" . $_POST['POVERKA_PLACE'] . "', '" . $_POST['ID_ASSIGN1'] . "', '" . $_POST['ID_ASSIGN2'] . "', '" . $_POST['OVV_EX2'] . "', '" . $_POST['TOO_EX2'] . "', '" . $_POST['AD_EX2'] . "',  '" . $_POST['PO_NAME'] . "',  '" . $_POST['PO_ID'] . "',  '" . $_POST['NAPR'] . "',  '" . $_POST['FREQ'] . "',  '" . $maxCardId . "',  '" . $_POST['document'] . "',  '" . $temperature . "',  '" . $humidity . "',  '" . $pressure . "', ".$invNum.", '" . (($_POST['LONG_STORAGE']) ? 1 : 0) . "', '" . (($_POST['LONG_STORAGE_DATE']) ? $_POST['LONG_STORAGE_DATE'] : '') . "','" . $_POST['OBJECT_GROUP'] . "')");
        /*-------------------'Не нормируется' окончание-------------------*/

		if ($res->result = 1) {
            echo "<h3>Успешно</h3><br>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
        $insert_id = $res->DB->db_Conn->insert_id;

        // вставка описание типа оборудования в таблицу ba_oborud
        // хранится путь до файла - поле desc_oborud
        if(!empty($_FILES['desc_oborud_file'])){
            $uploadDescFileDir = $_SERVER['DOCUMENT_ROOT'] . '/file_oborud/description_oborud/';
            $uploadDescFile = $uploadDescFileDir . $_FILES['desc_oborud_file']['name'];

            $sqlTot = "UPDATE `ba_oborud` SET desc_oborud = {?} WHERE ID = {?}";
            $sqlArr = array($_FILES['desc_oborud_file']['name'], $insert_id);
            $updateFileDescObor = $DB_Conn->queryExec($sqlTot, $sqlArr);
            if(move_uploaded_file($_FILES['desc_oborud_file']['tmp_name'], $uploadDescFile)) {
                //echo('Файл описания оборудования успешно загружен!<br>');
            }else{
                //echo('Файл описания оборудования не загрузился из-за ошибки<br>');
            }


                    // вставка аттестованных температур
        if (!empty($_REQUEST['certif_temp'])) {
            $id_oborud = (int)$_GET['ID'];
            $arr_cert_temp = serialize($_REQUEST['certif_temp']);

            $sqlTotTemp = "UPDATE `ba_oborud` SET `cert_temp` = {?} where ID = {?}";
            $paramArr = array($arr_cert_temp, $id_oborud);

            $uploadActTemp = $DB_Conn->queryExec($sqlTotTemp, $paramArr);
        }
        }


		//--Дата начала и окончания для каждого св-ва в карточке оборудования начало--//
		//При создании новой карточки оборудования
        $insert_id = $res->DB->db_Conn->insert_id;
		//echo'<pre>';var_dump('$_POST, $_FILES', $_POST, $_FILES);echo'</pre>';
		//Получаем из заполненых данных массив наименований свидетельства и сответсвующий сведетельству путь загруженного файла и дату начала и окончания поверки
		list($arrFieldsCertificate) = saveCertificateOborud($_POST, $_FILES);
		//echo'<pre>';var_dump('arrFieldsCertificate', $arrFieldsCertificate);echo'</pre>';die();
		$names = []; //Наименования свидетельств о поверки
		$files = []; //Пути расположения загруженных файлов
		$dateStartVerification = []; //Даты начала поверки
		$dateEndVerification = []; //Даты окончания поверки
		//$relevanceСertificate = []; //Актуальность свидетельств о поверки

		for ($i = 0; $i < count($arrFieldsCertificate); $i++) {
			//Сохраняем отдельно Наименования свидетельств о поверки для сохранения в БД
			if (!empty($arrFieldsCertificate[$i]['name'])) {
				$names[$i] = $arrFieldsCertificate[$i]['name'];
			}

			//Сохраняем отдельно Даты начала поверки для сохранения в БД
			if (!empty($arrFieldsCertificate[$i]['file'])) {
				$files[$i] = $arrFieldsCertificate[$i]['file'];
			}

			//Сохраняем отдельно Даты окончания поверки для сохранения в БД
			if (!empty($arrFieldsCertificate[$i]['date_end_verification'])) {
				$dateEndVerification[$i] = $arrFieldsCertificate[$i]['date_end_verification'];
			}

			//Сохраняем отдельно Пути расположения загруженных файловы для сохранения в БД
			if (!empty($arrFieldsCertificate[$i]['date_start_verification'])) {
				$dateStartVerification[$i] = $arrFieldsCertificate[$i]['date_start_verification'];
			}

			//Сохраняем отдельно Актуальность свидетельств о поверки для сохранения в БД
			/*if (isset($arrFieldsCertificate[$i]['relevance_certificate'])) {
				$relevanceСertificate[$i] = $arrFieldsCertificate[$i]['relevance_certificate'];
			}*/
		}

		//Сериализуем данные массивов для сохранения в БД
		$serializeNames = !empty($names) ? serialize($names) : ''; //Наименования свидетельств о поверки
		$serializeFiles = !empty($files) ? serialize($files) : ''; //Пути расположения загруженных файлов
		$serializeDateStartVerification = !empty($dateStartVerification) ? serialize($dateStartVerification) : ''; //Даты начала поверки
		$serializeDateEndVerification = !empty($dateEndVerification) ? serialize($dateEndVerification) : ''; //Даты окончания поверки
		//$serializeRelevanceСertificate = isset($relevanceСertificate) ? serialize($relevanceСertificate) : ''; //Актуальность свидетельств о поверки
		$relevanceСertificate = isset($_POST['relevance_certificate']) ? $_POST['relevance_certificate'] : '0'; //Актуальность свидетельств о поверки

		//Сохраняем данные свидетельств о поверки
		if (!empty($insert_id)) {
			//$res_certificate = $DB->Query("INSERT INTO `b_verification_certificate` (`name_f`, `file_address`, `id_oborud`, `date_start_verification`, `date_end_verification`, `relevance_certificate`)
			//VALUES ('" . $serializeNames . "', '" . $serializeFiles . "', '" . (int)$insert_id . "', '" . $serializeDateStartVerification . "', '" . $serializeDateEndVerification . "', '" . $serializeRelevanceСertificate . "')");

			$res_certificate = $DB->Query("INSERT INTO `b_verification_certificate` (`name_f`, `file_address`, `id_oborud`, `date_start_verification`, `date_end_verification`, `relevance_certificate`)
			VALUES ('" . $serializeNames . "', '" . $serializeFiles . "', '" . (int)$insert_id . "', '" . $serializeDateStartVerification . "', '" . $serializeDateEndVerification . "', '" . $relevanceСertificate . "')");
		}

		/*if (!empty($names) && !empty($files)) {
			$res_certificate = $DB->Query("INSERT INTO `b_verification_certificate` (`name_f`, `file_address`, `id_oborud`)
				VALUES ('" . serialize($names) . "', '" . serialize($files) . "', '" . $insert_id . "')");
		} elseif (!empty($names) && empty($files)) {
			$res_certificate = $DB->Query("INSERT INTO `b_verification_certificate` (`name_f`, `id_oborud`)
				VALUES ('" . serialize($names) . "', '" . $insert_id . "')");
		} elseif (empty($names) && !empty($files)) {
			$res_certificate = $DB->Query("INSERT INTO `b_verification_certificate` (`file_address`, `id_oborud`)
				VALUES ('" . serialize($files) . "', '" . $insert_id . "')");
		}*/
    }

    $res_history = $DB->Query("INSERT INTO `HISTORY_OA`(`TIME`, `USER_ID`, `TYPE`, `ID_ELEM`) VALUES ('" . date('Y-m-d H:i:s') . "'," . $USER->GetID() . ",'1'," . (int)$_POST['ID'] . ")");
}

// Поиск по ID. Вывод формы для создания/редактирования
if (isset($_GET['ID'])) {
    global $DB;

    if (!empty($_GET['del']))
	{
		$resEquip = $DB->Query("SELECT * FROM `TZ_OB_CONNECT` WHERE `ID_OB` = " . $_GET['ID']);
		if(intval($resEquip->SelectedRowsCount())>0)
		{
			echo "Оборудование не может быть удалено, поскольку используется в технических заданиях следующих заявок: <br>";
			while($rowEquip = $resEquip->Fetch())
			{
				echo "<a href='/request_card.php?ID=".$rowEquip['ID_TZ']."'>".$rowEquip['ID_TZ']."</a><br>";
			}

		}
		else
		{
			$res = $DB->Query("DELETE FROM `ba_oborud` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
			$res_certificate_del = $DB->Query("DELETE FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$_GET['ID'])->Fetch();

			if ($res->result = 1) {
				header("Location: //" . $_SERVER['HTTP_HOST']
					. "/oborud.php");
				echo "<h3>Успешно</h3>";
			} else {
				echo "<h3>Ошибка</h3>";
			}
			echo "<br><a href=\"/oborud.php\">К списку</a>";
		}
    }
	else
	{
		//--Дата начала и окончания для каждого св-ва в карточке оборудования начало--//
		$certificateName = null; //Наименования свидетельств о поверки
		$certificateAddress = null; //Пути расположения загруженных файлов
		$certificateDateStartVerification = null; //Даты начала поверки
		$certificateDateEndVerification = null; //Даты окончания поверки
		$certificateRelevanceСertificate = null; //Актуальность свидетельств о поверки
		//--Дата начала и окончания для каждого св-ва в карточке оборудования начало--//

		$res_count_id = $DB->Query("SELECT COUNT(`id_oborud`) FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$_GET['ID']);
		$row_count_id = $res_count_id->Fetch();
		if ($row_count_id['COUNT(`id_oborud`)']) {
			$res = $DB->Query("SELECT * FROM `ba_oborud` AS `ba_ob` INNER JOIN `b_verification_certificate` AS `b_v_certificate` ON `ba_ob`.`ID`=`b_v_certificate`.`id_oborud` WHERE `ba_ob`.`ID`=" . (int)$_GET['ID']);
			$row = $res->Fetch();
			//--Дата начала и окончания для каждого св-ва в карточке оборудования начало--//
			//$certificateName = unserialize($row['name_f']);
			//$certificateAddress = unserialize($row['file_address']);
			$passportOborud = json_decode($row['passport_pdf'], true);
			$certificateName = !empty($row['name_f']) ? unserialize($row['name_f']) : ''; //Наименования свидетельств о поверки
			$certificateAddress = !empty($row['file_address']) ? unserialize($row['file_address']) : ''; //Пути расположения загруженных файлов
			$certificateDateStartVerification = !empty($row['date_start_verification']) ? unserialize($row['date_start_verification']) : ''; //Даты начала поверки
			$certificateDateEndVerification = !empty($row['date_end_verification']) ? unserialize($row['date_end_verification']) : ''; //Даты окончания поверки
			//$certificateRelevanceСertificate = !empty($row['relevance_certificate']) ? unserialize($row['relevance_certificate']) : ''; //Актуальность свидетельств о поверки
			$certificateRelevanceСertificate = isset($row['relevance_certificate']) ? $row['relevance_certificate'] : ''; //Актуальность свидетельств о поверки

			//--Дата начала и окончания для каждого св-ва в карточке оборудования начало--//
		} else {
			$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . (int)$_GET['ID']);
			$row = $res->Fetch();
		}
        //echo'<pre>';var_dump($row['relevance_certificate']);echo'</pre>';
        //echo'<pre>';var_dump($certificateName,$certificateAddress,$certificateDateStartVerification,$certificateDateEndVerification,$certificateRelevanceСertificate);echo'</pre>';
        ?>
		<!---------------------------------------  начало 'Общее меню' -------------------------------------->
		<div>
			<div class='return disable' title='Вернуться назад'></div>
			<!-- Правил, начало -->
			<?php if($_POST) {
				$resMaxID = $DB->Query("SELECT `ID` FROM `ba_oborud` ORDER BY `ID` DESC LIMIT 1");
				$rowMaxID = $resMaxID->Fetch();
			?>
				<a href="/oborud.php?ID=<?= $rowMaxID['ID'] ?>">
					<div class='edit' title='Редактировать'></div><!--Редактировать оборудование-->
				</a>
			<?php } ?>
			<!-- Правил, окончание -->
			<a href="/oborud.php">
				<div class='list' title='Вернуться к списку'></div><!--К списку-->
			</a>
			<div class='card disable' title='Карточка заявки'></div>
			<?php if ($_GET['ID'] > 0) { ?>
				<div class='download downloadOborud' title='Скачать'>
					<ul class='downloadMenu'>
						<li>
							<a href="/protocol_generator/oborud_card.php?OB_ID=<?= $_GET['ID'] ?>">
								Скачать карточку оборудования
							</a>
						</li>
						<li>
							<a href="/protocol_generator/oborud_akt_vvoda.php?OB_ID=<?= $_GET['ID'] ?>">
								Скачать акт ввода
							</a>
						</li>
						<li>
							<a href="/protocol_generator/oborud_akt_sp.php?OB_ID=<?= $_GET['ID'] ?>">
								Скачать акт списания
							</a>
						</li>
                        <li>
                            <a href="/protocol_generator/oborud_akt_post.php?OB_ID=<?= $_GET['ID'] ?>">
                                Скачать акт о постановки на длительное хранение
                            </a>
                        </li>
                        <li>
                            <a href="/protocol_generator/oborud_akt_ver.php?OB_ID=<?= $_GET['ID'] ?>">
                                Скачать акт о верификации оборудования
                            </a>
                        </li>
						<li>
							<a href="/protocol_generator/oborud_card_once.php?OB_ID=<?= $_GET['ID'] ?>">
								Скачать этикетку оборудования
							</a>
						</li>
					</ul>
				</div>
				<a href="/oborud_copy.php?ID=<?= $_GET['ID'] ?>">
					<div class='copy' title='Скопировать'></div><!--Скопировать-->
				</a>
				<?php if(($USER->GetID()=='1')||($USER->GetID()=='10')||($USER->GetID()=='9') ||($USER->GetID()=='8')||($USER->GetID()=='11')||($USER->GetID()=='61')||($USER->GetID()=='32')) {?>
					<a href="/oborud.php?ID=<?= $_GET['ID'] ?>&del=true"
					   onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">
						<div class='remove' title='Удалить'></div><!--Удалить-->
					</a>
			<?php }} ?>
			<div class='add disable' title='Добавить'></div>
			<div class='report disable' title='Отчёт'></div>
			<div class='further disable' title='Далее'></div>
		</div>
        <hr>
		<!---------------------------------------- окончание 'Общее меню' ----------------------------------->
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post' enctype="multipart/form-data">
            <table id="oborud_table">
                <tr>
                    <td><h3>Основные характеристики</h3></td>
                <tr>
                    <td>Измеряемая характеристика</td>
                    <td><textarea class="oborud" name="NAME"><?= $row['NAME'] ?></textarea></td>
                <tr>
                    <td>Наименование оборудования <span class='redStars'>*<span></td>
                    <td><input type="text" class="oborud" name="OBJECT" value = "<?= $row['OBJECT'] ?>" required></input></td>
                <tr>
                    <td>Тип оборудования</td>
                    <td><textarea class="oborud" name="TYPE_OBORUD"><?= $row['TYPE_OBORUD'] ?></textarea></td>
                <tr>
                    <td>№ по Государственному реестру РФ</td>
                    <td><input class="oborud" type="text" name="GOSREESTR" value="<?= $row['GOSREESTR'] ?>"></input>
                    </td>
                <tr>
                    <td>Идентификация оборудования</td>
                    <td><select class="oborud" name="IDENT">
                            <option <?= ($row['IDENT'] == 'SI') ? 'selected' : '' ?> value='SI'>СИ</option>
                            <option <?= ($row['IDENT'] == 'IO') ? 'selected' : '' ?> value='IO'>ИО</option>
                            <option <?= ($row['IDENT'] == 'VO') ? 'selected' : '' ?> value='VO'>ВО</option>
                            <option <?= ($row['IDENT'] == 'TS') ? 'selected' : '' ?> value='TS'>ТС</option>
                            <option <?= ($row['IDENT'] == 'SO') ? 'selected' : '' ?> value='SO'>CO</option>
                            <option <?= ($row['IDENT'] == 'REACT') ? 'selected' : '' ?> value='REACT'>Реактивы</option>
                            <option <?= ($row['IDENT'] == 'OOPP') ? 'selected' : '' ?> value='OOPP'>Оборудование для
                                отбора/подготовки проб
                            </option>
                        </select></td>
				 <tr>
                    <td>Группа испытываемых объектов</td>
                    <td><textarea class="oborud" name="OBJECT_GROUP"><?= $row['OBJECT_GROUP'] ?></textarea></td>
                <tr>
                <tr>
                    <td>Страна, производитель</td>
                    <td><textarea class="oborud" name="manufacturer"><?= $row['manufacturer'] ?></textarea></td>
                <tr>
                    <td>Инвентарный номер</td>
                    <td><input class="oborud" type="text" name="REG_NUM" value="<?= $row['REG_NUM'] ?>" <?= (($USER->GetID() == '34')&&$row['place_of_installation_or_storage']==5)||(($USER->GetID() == '32')&&$row['place_of_installation_or_storage']==5)||( $USER->GetID() == '10') ? '' : 'readonly' ?>></td>
                <tr>
                    <td>Заводской номер</td>
                    <td><input class="oborud" type="text" name="FACTORY_NUMBER" value="<?= $row['FACTORY_NUMBER'] ?>">
                    </td>
                <tr>
                    <td>Год выпуска</td>
                    <td><input class="oborud" type="text" name="YEAR" value="<?= $row['YEAR'] ?>"></td>
                <tr>
                    <td>Дата ввода в эксплуатацию <span class='redStars'>*<span></td>
                    <td><input class="oborud" type="date" name="god_vvoda_expluatation"
                            value="<?= $row['god_vvoda_expluatation']!='1970-01-01' ? $row['god_vvoda_expluatation'] : '' ?>" required>
                    </td>
                <tr>
                    <td>Регистрационный номер в ИЦ</td>
                    <td><input class="oborud" type="text" name="REG_NUM_RC" value="<?= $row['REG_NUM_RC'] ?>"></td>
                <tr>
                    <td>Описание типа оборудования</td>
                    <td> <input type="text" name="desc_oborud" class="oborud" value="<?= $row['desc_oborud'] ?>"> </td>
                    <td>
                        <input name="desc_oborud_file" type="file" style="margin-left: 15px; <?= (!empty($row['desc_oborud']) ? 'display:none;' : '') ?> ">
                        <div class="wrapperDel"
                             style='<?= (!empty($row['desc_oborud'])) ? "display:block" : "display:none" ?>'>
                            <a href="/file_oborud/<?= $_GET['ID'] ?>/description_oborud/<?= $row['desc_oborud'] ?>" download>
                                <div class="docFile"></div>
                            </a>
                            <div class="delFile" title="Удалить файл, загрузить новый"></div>
                        </div>
                    </td>
                </tr>
				<tr class='trHidden'>
					<input type='hidden' name='hiddenDelDesc' class='hiddenDel'>
				</tr>
                <!--Интерфейс загрузки фото и сканов-->
                <tr>
                    <td>
                        Фотография оборудования
                    </td>
                    <td> <input type="text" name="photo_oborud_oborud" class="oborud" value="<?= $row['photo_oborud'] ?>"> </td>
                    <td>
                        <input name="photo_oborud_file" type="file" style="margin-left: 15px; <?= (!empty($row['photo_oborud']) ? 'display:none;' : '') ?> ">
                        <div class="wrapperDel" style='<?= (!empty($row['photo_oborud'])) ? "display:block" : "display:none" ?>'>
                            <a href="/file_oborud/<?= $_GET['ID'] ?>/<?=$row['photo_oborud']?>" download>
                                <div class="docFile"></div>
                            </a>
                            <div class="delFile" title="Удалить файл, загрузить новый"></div>
                        </div>
                    </td>
                </tr>
                <tr class='trHidden'>
                    <input type='hidden' name='hiddenDelPhoto' class='hiddenDel'>
                </tr>
                <tr>
                    <td>
                        Скан-копия документа о праве собственности на оборудование
                    </td>
                    <td> <input type="text" name="property_rights_oborud" class="oborud" value="<?= $row['property_rights_pdf'] ?>"> </td>
                    <td>
                        <input name="property_rights_pdf" type="file" style="margin-left: 15px; <?= (!empty($row['property_rights_pdf']) ? 'display:none;' : '') ?> ">
                        <div class="wrapperDel" style='<?= (!empty($row['property_rights_pdf'])) ? "display:block" : "display:none" ?>'>
                            <a href="/file_oborud/<?= $_GET['ID'] ?>/<?=$row['property_rights_pdf']?>" download>
                                <div class="docFile"></div>
                            </a>
                            <div class="delFile" title="Удалить файл, загрузить новый"></div>
                        </div>
                    </td>
                </tr>
                <tr class='trHidden'>
                    <input type='hidden' name='hiddenDelProp' class='hiddenDel'>
                </tr>
                <tr>
                    <td>Состояние при покупке</td>
                    <td><select class="oborud" name="COND">
                            <option <?= ($row['COND'] == 'NEW') ? 'selected' : '' ?> value='NEW'>Новое</option>
                            <option <?= ($row['COND'] == 'OLD') ? 'selected' : '' ?> value='OLD'>Б/у</option>
                        </select>
                    </td>
				</tr>
                <tr>
                    <td>
                        Паспорт / руководство по эксплуатации
                    </td>
                    <td> <?php if (empty($passportOborud)): ?>
                        <input type="text" name="passport_oborud[]" class="oborud" value=""></td>
                    <?php else: ?>
                        <?php foreach ($passportOborud as $pasPDF):?>
                            <input type="text" name="passport_oborud[]" class="oborud" value="<?= $pasPDF ?>">
<!--                            <p>--><?//= $pasPDF ?><!--</p>-->
                        <?php endforeach; ?>
                        </td>
                    <?php endif;?>
                    <td>
                        <input name="passport_pdf[]" type="file"
                               style="margin-left: 15px; <?=!empty($row['passport_pdf']) ? 'display:none;' : ''?>"
                               multiple>
                        <?php foreach ($passportOborud as $pasPDF): ?>
                            <div class="wrapperDel" style='<?= (!empty($pasPDF)) ? "display:block" : "display:none" ?>'>
                                <a href="/file_oborud/<?= $_GET['ID'] ?>/passport/<?= $pasPDF ?>" download>
                                    <div class="docFile"></div>
                                </a>
                                <div class="delFile" title="Удалить файл, загрузить новый"></div>
                                <label for="addFile" style="cursor: pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-clipboard2-plus" viewBox="0 0 16 16">
                                        <path d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5h3Z"/>
                                        <path d="M3 2.5a.5.5 0 0 1 .5-.5H4a.5.5 0 0 0 0-1h-.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1H12a.5.5 0 0 0 0 1h.5a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-12Z"/>
                                        <path d="M8.5 6.5a.5.5 0 0 0-1 0V8H6a.5.5 0 0 0 0 1h1.5v1.5a.5.5 0 0 0 1 0V9H10a.5.5 0 0 0 0-1H8.5V6.5Z"/>
                                    </svg>
                                </label>
                                <input name="passport_pdf[]" type="file" id="addFile" style="display: none;"
                                       multiple>
                            </div>
                        <?php endforeach; ?>
                    </td>
                </tr>
				<tr class='trHidden'>
					<input type='hidden' name='hiddenDelPassport' class='hiddenDel'>
				</tr>
                <tr>
                    <td>В области акредитации</td>
                    <td><input type="checkbox" name="IN_AREA" <?= ($row['IN_AREA']) ? 'checked' : '' ?>></td>
                <tr>
                    <td>В наличии</td>
                    <td><input type="checkbox" name="IN_STOCK" <?= ($row['IN_STOCK']) ? 'checked' : '' ?>></td>
                <tr>
                    <td>Проверено</td>
                    <td><input type="checkbox" name="CHECKED" <?= ($row['CHECKED']) ? 'checked' : '' ?>
                               <?= ($USER->GetID() == '1' || $USER->GetID() == '10' || $USER->GetID() == '58' || $USER->GetID() == '22' || $USER->GetID() == '33' || $USER->GetID() == '101' || $USER->GetID() == '137') ? '' : 'disabled' ?>>
                    </td>
                <tr>
				<!-------------------'Не нормируется' начало---------------------->
                    <!-----------------'Условия эксплуатации'-------------->
                    <td><h3>Условия эксплуатации</h3></td>
                <tr>
                    <td>Относительная влажность воздуха, %</td>
                    <td>
                        <div class='dataHumidity' style="display:flex">от&nbsp; <input class="obl_num" type="text" name="OVV_EX"
                                                                  value="<?= $row['OVV_EX'] ?>"></input>&nbsp;до&nbsp;
                            <input class="obl_num" type="number" step="0.1" name="OVV_EX2"
                                   value="<?= $row['OVV_EX2'] ?>"></input>
							<input type='checkbox' class='notHumidity' name='checkHumidity' <?= ((!empty($row['HUMIDITY']) && $row['HUMIDITY'] == '1') ? 'checked' : '') ?>>
                            <span class='notNormalized'>Не нормируется</span>
						</div>
                    </td>
                <tr>
                    <td>Температура окружающего воздуха, ºС</td>
                    <td>
                        <div class='dataTemperature' style="display:flex">от&nbsp; <input class="obl_num" type="text" name="TOO_EX"
                                                                  value="<?= $row['TOO_EX'] ?>"></input>&nbsp;до&nbsp;
                            <input class="obl_num" type="number" step="0.1" name="TOO_EX2"
                                   value="<?= $row['TOO_EX2'] ?>"></input>
							<input type='checkbox' class='notTemperature' name='checkTemperature' <?= ((!empty($row['TEMPERATURE']) && $row['TEMPERATURE'] == '1') ? 'checked' : '') ?>>
                            <span class='notNormalized'>Не нормируется</span>
						</div>
                    </td>
                <tr>
                    <td>Атмосферное давление, кПа</td>
                    <td>
                        <div class='dataPressure' style="display:flex">от&nbsp; <input class="obl_num" type="text" name="AD_EX"
                                                                  value="<?= $row['AD_EX'] ?>"></input>&nbsp;до&nbsp;
                            <input class="obl_num" type="number" step="0.1" name="AD_EX2"
                                   value="<?= $row['AD_EX2'] ?>"></input>
							<input type='checkbox' class='notPressure' name='checkPressure' <?= ((!empty($row['PRESSURE']) && $row['PRESSURE'] == '1') ? 'checked' : '') ?>>
                            <span class='notNormalized'>Не нормируется</span>
						</div>
                    </td>
					<!-- /.'Условия эксплуатации' -->
                    <!------------'Не нормируется' окончание--------------->
                <tr>
                    <td>Напряжение питающей сети, В</td>
                    <td><input class="oborud" type="text" name="NAPR" value="<?= $row['NAPR'] ?>"></input></td>
                <tr>
                    <td>Частота, Гц</td>
                    <td><input class="oborud" type="text" name="FREQ" value="<?= $row['FREQ'] ?>"></input></td>
                <tr>
                    <td><h3>Условия хранения</h3></td>
                <tr>
                    <td>Относительная влажность воздуха, %</td>
                    <td><input class="oborud" type="text" name="OVV_UH" value="<?= $row['OVV_UH'] ?>"></input></td>
                <tr>
                    <td>Температура окружающего воздуха, ºС</td>
                    <td><input class="oborud" type="text" name="TOO_UH" value="<?= $row['TOO_UH'] ?>"></input></td>
                <tr>
                    <td>Атмосферное давление, кПа</td>
                    <td><input class="oborud" type="text" name="AD_UH" value="<?= $row['AD_UH'] ?>"></input></td>
                <tr>
                    <td><h3>Технические данные</h3></td>
                <tr>
                    <td>Технические характеристики</td>
                    <td><textarea class="oborud" name="TECH"><?= $row['TECH'] ?></textarea></td>
                <tr>
                    <td>Диапазон измерения</td>
                    <td><textarea class="oborud" name="measuring_range"><?= $row['measuring_range'] ?></textarea></td>

                <? if($row['IDENT'] == 'IO'){ ?>
                <!-- выводим только если оборудование ИО (для низкотемпературных печек с аттестованными температурами)-->
                <!-- далее температуры выводятся в этикетках-->
                <tr>
                    <td>Аттестованные температуры</td>
                    <td id="tr_cert_temp" style="display: flex;">
                    <?
                        $arr_temp = unserialize($row['cert_temp']);
                        foreach($arr_temp as $k => $v) {
                            echo('<input type="text" class="oborud" name="certif_temp[]" style="width: 67px; margin-right: 5px; padding: 5px;" placeholder="100 °С" value="' . $v .'" >');
                        }
                        if(count($arr_temp) == 1) echo('<input type="text" class="oborud" name="certif_temp[]" style="width: 67px; margin-right: 5px; padding: 5px;" placeholder="+100 °С">');
                    ?>
                    </td>
                    <td>
                        <input type="button" class="add3" value="+" style="margin-left:5px; width:35px;">
                    </td>
                <? } ?>
                <tr>
                    <td>Класс точности и (разряд), погрешность</td>
                    <td><textarea class="oborud"
                                  name="сlass_precision_and_accuracy"><?= $row['сlass_precision_and_accuracy'] ?></textarea>
                    </td>
                <tr>
                    <td><h3>Метрологический контроль</h3></td>
                <tr>
                    <td>Дата последней поверки</td>
                    <td><input class="oborud" type="date" name="POVERKA_LAST" value="<?= $row['POVERKA_LAST'] ? $row['POVERKA_LAST'] : ''?>"></td>
                <tr>
                    <td>Дата окончания поверки</td>
                    <td><input class="oborud" type="date" name="POVERKA" value="<?= $row['POVERKA']!='1970-01-01' ? $row['POVERKA'] : ''   ?>"></td>
				<!--Дата начала и окончания для каждого св-ва в карточке оборудования начало-->
				</tr>
				<?php
				//Счетчик кол-ва элементов(Свидетельств о поверке)
				$countCertificate = 0;
				//Если нет наименование свидетельства о поверке и загруженного файла
				if (empty($certificateName) && empty($certificateAddress)) :
				?>
				<!--Счетчик кол-ва элементов(Свидетельств о поверке)-->
				<input type="hidden" class="countCertificate" value="<?= $countCertificate ?>">
					<tr class='trRelevanceCertificate'>
						<td>Актуальный сертификат</td>
						<!--<td><input class="relevance-certificate" type="radio" name="certificate[<?= $countCertificate ?>][relevance_certificate]" value="<?= $countCertificate ?>"></td>-->
						<td><input class="relevance-certificate" type="radio" name="relevance_certificate" value="<?= $countCertificate ?>"></td>
					</tr>
					<tr class='trDateStartVerification'>
						<td>Дата начала поверки</td>
						<td><input class="oborud" type="date" name="certificate[<?= $countCertificate ?>][date_start_verification]" value=""></td>
					</tr>
					<tr class='trDateEndVerification'>
						<td>Дата окончания поверки</td>
						<td><input class="oborud" type="date" name="certificate[<?= $countCertificate ?>][date_end_verification]" value=""></td>
					</tr>
					<!--Дата начала и окончания для каждого св-ва в карточке оборудования окончание-->
					<tr class='trCertificate'>
						<td>Свидетельство о поверке</td>
						<td><textarea class="oborud"
									  name="certificate[<?= $countCertificate ?>][name]"><?= $row['certificate_of_verification']  ?></textarea>
						</td>
						<td>
							<input name="certificate[<?= $countCertificate ?>][file]" type="file"
								   style="margin-left: 15px; <?= (!empty($certificateName)) ? 'display:none' : '' ?>"
								   value='<?= $certificateAddress[$keyName] ?>'>
							<div class='wrapperDel'
								 style='<?= (!empty($certificateAddress[$keyName])) ? "display:block" : "display:none" ?>'>
								<div class='docFile'></div>
								<div class='delFile' title='Удалить файл, загрузить новый'></div>
							</div>
						</td>
						<td><input style='margin-left:5px' type='button' class='add2' value='+'></td>
					<tr>
                    <?php endif; ?>
                    <?php
					//Если есть Наименование свидетельства о поверке или Загруженный файл или Даты начала поверки или Даты окончания поверки
					//if (!empty($certificateName) || !empty($certificateAddress)) {
					if (!empty($certificateName) || !empty($certificateAddress) || !empty($certificateDateStartVerification) || !empty($certificateDateEndVerification) || !empty($certificateRelevanceСertificate)) {

						$g = 0;
						//Собираемм данные Наименования свидетельства, пути расположения загруженного файла, Даты начала и окончания поверки в один массив разбивая на подмассивы все Свидетельства о поверки
						$certificateArr = [];
						foreach($certificateName as $keyName => $valName) {
							$certificateArr[$keyName]['name'] = $valName;
						}
						foreach($certificateAddress as $keyFile => $valFile) {
							$certificateArr[$keyFile]['file'] = $valFile;
						}

						//--Дата начала и окончания для каждого св-ва в карточке оборудования начало--//
						//Перебираем Даты начала поверки и собираем в один массив для каждого Свидетельства о поверки
						foreach($certificateDateStartVerification as $keyStartDate => $valStartDate) {
							$certificateArr[$keyStartDate]['date_start_verification'] = $valStartDate;
						}

						//Перебираем Даты окончания поверки и собираем в один массив для каждого Свидетельства о поверки
						foreach($certificateDateEndVerification as $keyEndDate => $valEndDate) {
							$certificateArr[$keyEndDate]['date_end_verification'] = $valEndDate;
						}

						//Перебираем Актуальности свидетельства о поверки и собираем в один массив для каждого Свидетельства о поверки
						/*foreach($certificateRelevanceСertificate as $keyRelevanceCertificate => $valRelevanceCertificate) {
							$certificateArr[$keyRelevanceCertificate]['relevance_certificate'] = $valRelevanceCertificate;
						}*/
						//--Дата начала и окончания для каждого св-ва в карточке оборудования начало--//
						//Сортируем массив по ключам
						ksort($certificateArr);
						$i = 0;

						$certificatePdf = [];
						foreach($certificateArr as $keyCertificate => $certificate) {
							if (empty($certificate['file'])) {
								$certificatePdf[$keyCertificate] = '';
							} else {
								$certificatePdf[$keyCertificate] = $certificate['file'];
							}

							if (is_int($keyCertificate)) {
								$iKey = $keyCertificate;
							} else {
								$iKey = $i;
							}
							$filePath = pathinfo($certificate['file']);
                    ?>
				<!--Дата начала и окончания для каждого св-ва в карточке оборудования начало-->
				<tr class='trRelevanceCertificate'>
                    <td>Актуальный сертификат</td>
                    <!--<td><input class="relevance-certificate" type="radio" name="certificate[<?= $iKey ?>][relevance_certificate]" value="<?= isset($certificate['relevance_certificate']) ? $certificate['relevance_certificate'] : '' ?>" <?= isset($certificate['relevance_certificate']) ? 'checked' : '' ?>></td>-->
					<td><input class="relevance-certificate" type="radio" name="relevance_certificate" value="<?= $iKey ?>" <?= isset($certificateRelevanceСertificate) && $certificateRelevanceСertificate != '' && $certificateRelevanceСertificate == $iKey ? 'checked' : ''?>></td>
				</tr>
				<tr class='trDateStartVerification'>
                    <td>Дата начала поверки</td>
                    <td><input class="oborud" type="date" name="certificate[<?= $iKey ?>][date_start_verification]" value="<?= !empty($certificate['date_start_verification']) ? $certificate['date_start_verification'] : '' ?>"></td>
                </tr>
				<tr class='trDateEndVerification'>
                    <td>Дата окончания поверки</td>
					<td><input class="oborud" type="date" name="certificate[<?= $iKey ?>][date_end_verification]" value="<?= !empty($certificate['date_end_verification']) ? $certificate['date_end_verification'] : '' ?>"></td>
				</tr>
				<!--Дата начала и окончания для каждого св-ва в карточке оборудования окончание-->
                <tr class='trCertificate'>
						<td>Свидетельство о поверке</td>
                    <td>
					<textarea class="oborud" name="certificate[<?= $iKey ?>][name]"><?= trim($certificate['name']) ?></textarea>
                    </td>
                    <td>
                        <input name="certificate[<?= $iKey ?>][file]" type="file"
                               style="margin-left: 15px;<?= (!empty($certificate['file'])) ? 'display:none' : '' ?>"'>
                        <div class='wrapperDel'
                             style='<?= (!empty($certificate['file']))
							 ? "display:block" : "display:none" ?>'>

							<a href="/file_oborud/<?= $filePath['basename'] ?>" download><div class='docFile'></div></a>
                            <div class='delFile' title='Удалить файл, загрузить новый'></div>
                        </div>
                    </td>
                    <?php
                    if (!$g) echo "<td><input style='margin-left:5px' type='button' class='add2' value='+'></td>";
                    else echo "<td><input style='margin-left:5px' type='button' class='del' value='-'></td>";
                    $g++;
                    ?>
				<tr class='trHidden'>
					<input type='hidden' name='hiddenDelPdf[<?= $iKey ?>]' class='hiddenDel'>
                    <?php
					$i++;
                    }
                    }
                    ?>
					<!-- Правил, окончание -->
				<tr>
                    <td>Межкалибровочный интервал</td>
                    <td><input class="oborud" type="text" name="MC_INTERVAL" value="<?= $row['MC_INTERVAL'] ?>"></td>
                <tr>
                    <td>Первичный метрологический контроль</td>
                    <td><select class="oborud" name="METR_CONTROL">
                            <option <?= ($row['METR_CONTROL'] == 'ATTESTATION') ? 'selected' : '' ?>
                                    value='ATTESTATION'>Аттестация
                            </option>
                            <option <?= ($row['METR_CONTROL'] == 'CALIBR') ? 'selected' : '' ?> value='CALIBR'>
                                Калибровка
                            </option>
                            <option <?= ($row['METR_CONTROL'] == 'POVERKA') ? 'selected' : '' ?> value='POVERKA'>
                                Поверка
                            </option>
                            <option <?= ($row['METR_CONTROL'] == 'TECH_CHAR') ? 'selected' : '' ?> value='TECH_CHAR'>
                                Проверка тех. харктеристик
                            </option>
                        </select>
                    </td>
                <tr>
				<input type="hidden" name="certificatePdf" value='<?= serialize($certificatePdf) ?>'>
                    <td>Меcто поверки</td>
                    <td><input class="oborud" type="text" name="POVERKA_PLACE" value="<?= $row['POVERKA_PLACE'] ?>">
                    </td>
				<tr>
                    <td>Не подлежит поверке</td>
                    <td><input type="checkbox" name="NO_METR_CONTROL" <?= ($row['NO_METR_CONTROL']) ? 'checked' : '' ?>></td>
                <tr>
                    <td><h3>Местонахождение и собственность</h3></td>
                <tr>
                    <td>Право собственности</td>
                    <td><textarea class="oborud" name="property_rights"><?= $row['property_rights'] ?></textarea></td>
                <tr>
                    <td>Место установки или хранения <span class='redStars'>*<span></td>
                    <td>
                        <select class="oborud" name="place_of_installation_or_storage" required>
                            <option value="" selected disabled="">Выберите</option>
							<?php
                            $res = $DB->Query("SELECT * FROM `ba_laba`");
                            while ($value = $res->Fetch()) {
                                echo "<option ";
                                if ($row['place_of_installation_or_storage'] == $value['ID']) {
                                    echo "selected";
                                }
                                echo " value='" . $value['ID'] . "'>" . $value['NAME'] . "</option>";
                            }
                            ?>
							<option value="7" <?= $row['place_of_installation_or_storage'] == 7 ? 'selected' : '' ?> >Отдел СМК и метрологии</option>

                        </select>
                    </td>
                    <!--  <tr>
                    <td>Номер комнаты</td>
                    <td><textarea class="oborud" name="" disabled><?= $row['roomnumber'] ?></textarea></td>-->
                <tr>
                    <td>Помещение</td>
                    <td><select class="obl" name="roomnumber">
                            <option value="0" selected disabled="">Выберите</option>
                            <?php
                            $res = $DB->Query("SELECT * FROM `ROOMS`");
                            while ($value = $res->Fetch()) {
                                echo "<option ";
                                if ($row['roomnumber'] == $value['ID']) {
                                    echo "selected";
                                }
                                echo " value='" . $value['ID'] . "'>" . $value['NAME'] . ' ' . ($value['NUMBER'] ? $value['NUMBER'] : '') . "</option>";
                            }
                            ?>
                        </select></td>
                <tr>
                    <td>Переносное оборудование</td>
                    <td><input type="checkbox" name="is_portable" value="on" <?= ($row['is_portable']) ? 'checked' : '' ?>></td>
                <tr>
                    <td><h3>Ввод в эксплуатацию и списание</h3></td>
                <tr>
                    <td>Номер приказа о вводе в экспл.</td>
                    <td><input class="oborud" type="text" name="NUM_PRIKAZ" value="<?= $row['NUM_PRIKAZ'] ?>"></td>
                <tr>
                    <td>Дата приказа о вводе в экспл.</td>
                    <td><input class="oborud" type="date" name="DATE_PRIKAZ" value="<?= $row['DATE_PRIKAZ'] ?>"></td>
                <tr>
                    <td>Ввод в экспл. произвел</td>
                    <td><select class="oborud" name="ASSIGNED">
                            <option selected disabled>Выберите</option>
                            <option <?= ($row['ASSIGNED'] == '1') ? 'selected' : '' ?> value='1'>Овчинникова Т.А.
                            </option>
                            <option <?= ($row['ASSIGNED'] == '2') ? 'selected' : '' ?> value='2'>Велютич Т.В.</option>
                            <option <?= ($row['ASSIGNED'] == '3') ? 'selected' : '' ?> value='3'>Живцова Р.А</option>
                            <option <?= ($row['ASSIGNED'] == '4') ? 'selected' : '' ?> value='4'>Щелканова А.В.</option>
							 <option <?= ($row['ASSIGNED'] == '5') ? 'selected' : '' ?> value='5'>Живцов М.С.</option>
							 <option <?= ($row['ASSIGNED'] == '6') ? 'selected' : '' ?> value='6'>Лапков А.В.</option>
							 <option <?= ($row['ASSIGNED'] == '7') ? 'selected' : '' ?> value='7'>Мухаматнуров А.Ф.</option>
							 <option <?= ($row['ASSIGNED'] == '8') ? 'selected' : '' ?> value='8'>Печерский М.В.</option>
							 <option <?= ($row['ASSIGNED'] == '9') ? 'selected' : '' ?> value='9'>Стыценков Д.С.</option>
							 <option <?= ($row['ASSIGNED'] == '10') ? 'selected' : '' ?> value='10'>Попов А.Н.</option>

                        </select>
                    </td>
                <tr>
                    <td>На длительном хранении</td>
                    <td><input type="checkbox" name="LONG_STORAGE" <?=$row['LONG_STORAGE'] != 0 ? 'checked' : ''?>></td>
				<tr>
					<td>Дата постановки на длительное хранение</td>
					<td><input class="oborud" type="date" name="LONG_STORAGE_DATE" value ="<?= date('Y-m-d', strtotime($row['LONG_STORAGE_DATE'])) ?>"></td>
                <tr>
                    <td>Основание для списания</td>
                    <td><input class="oborud" type="text" name="SPISANIE" value="<?= $row['SPISANIE'] ?>"></td>
                <tr>
                    <td>Дата списания</td>
                    <td><input class="oborud" type="date" name="DATE_SP" value="<?= $row['DATE_SP'] ?>"></td>
                <tr>
                    <td><h3>Ответственные лица</h3></td>
                <tr>
                    <td>Ответственный сотрудник</td>
                    <td><select class="oborud" name="ID_ASSIGN1">
                            <option value="0" selected>Не указан</option>
                            <?php
                            $order = array();
							$filter = Array('ACTIVE' => 'Y');
                            $tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
                            $select = CUser::GetList(($by='ID'), ($order='DESC'), $filter);

                            $selected = '';
                            while ($option = $select->Fetch()) {
                                $selected = (($option['ID'] == $row['ID_ASSIGN1']) ? 'selected' : '');
                                echo "<option value='" . $option['ID'] . "'" . $selected . ">" . $option['NAME'] . ' ' . $option['LAST_NAME'] . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                <tr>
                    <td>Доп. ответственный сотрудник</td>
                    <td><select class="oborud" name="ID_ASSIGN2">
                            <option value="0" selected>Не указан</option>
                            <?php
                            $order = array();
							$filter = Array('ACTIVE' => 'Y');
                            $tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
                            $select = CUser::GetList(($by='ID'), ($order='DESC'), $filter);

                            $selected = '';
                            while ($option = $select->Fetch()) {
                                $selected = (($option['ID'] == $row['ID_ASSIGN2']) ? 'selected' : '');
                                echo "<option value='" . $option['ID'] . "'" . $selected . ">" . $option['NAME'] . ' ' . $option['LAST_NAME'] . "</option>";
                            }
                            ?>
                        </select>
                    </td>

                <tr>
                    <td><h3>Программное обеспечение</h3></td>
                <tr>
                    <td>Наименование ПО</td>
                    <td><input class="oborud" type="text" name="PO_NAME" value="<?= $row['PO_NAME'] ?>"></td>
                <tr>
                    <td>Идент. номер версии ПО</td>
                    <td><input class="oborud" type="text" name="PO_ID" value="<?= $row['PO_ID'] ?>"></td>

                <tr>
                    <td><h3>Дополнительная информация</h3></td>
				<tr>
                    <td>Наличие документа (паспорт, рук-во)</td>
                    <td><textarea class="oborud" name="document"><?= $row['DOCUMENT'] ?></textarea></td>
                <tr>
                    <td>Примечание</td>
                    <td><textarea class="oborud" name="note"><?= $row['note'] ?></textarea></td>
            </table>

            <input type="hidden" name="ID" value="<?= $row['ID'] ?>">
            <input id='status' type='hidden' value='<?= $USER->GetID() ?>'></input>
            <input type='submit' value='Сохранить'><br/><br/>
        </form>
        <?php
    }
} else {// Вывод скиска

    global $DB;
	$vagon = "`REG_NUM` LIKE '%В%'";
	$noVagon = "`REG_NUM` NOT LIKE '%В%'";
	$noArchive = "`LONG_STORAGE` = '0' AND (`DATE_SP` = '' OR `DATE_SP` IS NULL)";
    if (!empty($_GET['laba']) && ($_GET['laba'] != 0) && ($_GET['laba'] < 100)) {
        $res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage`= {$_GET['laba']}");
    } elseif (($_GET['laba'] >= 100) && ($_GET['laba'] != 9999)) {
        $id_room = (int)$_GET['laba'] - 100;
        $res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `roomnumber`=" . $id_room);
    } elseif ($_GET['laba'] == 9999) {
        $res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage`=0 ");
    } elseif ($_GET['stage'] == 'archive') {
		$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `SPISANIE` != ''");
    } elseif ($_GET['stage'] == 'longstorage') {
		$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `LONG_STORAGE` IS NOT NULL AND `LONG_STORAGE` != 0 AND `SPISANIE` = ''");
	} elseif ($_GET['stage'] == 'vagon') {
		$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE {$vagon}");
	}
	else {
        $res = $DB->Query("SELECT * FROM `ba_oborud` WHERE {$noArchive} AND {$noVagon}");
    }
    $location_stage = "";
    $location_laba = "";
    if (!empty($_GET['laba']) || !empty($_GET['stage']) || $_GET['laba'] == '0') {
        $location_stage = "window.location.assign('/oborud.php?laba=" . $_GET['laba'] . "&stage='+this.options[this.selectedIndex].value)";
        $location_laba = "window.location.assign('/oborud.php?stage=" . $_GET['stage'] . "&laba='+this.options[this.selectedIndex].value)";
    } else {
        $location_stage = "window.location.assign('/oborud.php?stage='+this.options[this.selectedIndex].value)";
        $location_laba = "window.location.assign('/oborud.php?laba='+this.options[this.selectedIndex].value)";
    }
    ?>
    <a href="/oborud.php?ID=0">Добавить новое оборудование</a> | <a href="/protocol_generator/oborud_card2.php?ID=0">Скачать карточки</a> | <a href="/protocol_generator/metr_request.php">Скачать заявку на поверку</a> | <a href='/protocol_generator/passport_rc.php'>Скачать формы 2-5</a>
    <table style="float: right;">
        <tr>
            <td>
                <select class="oborud" name="stage" id="stage"
                        onchange="<?= $location_stage ?>"
                        style="float: left; width: 100%">

                    <option value='all' style='font-weight:bold' <?= $_GET['stage'] == 'all' ? 'selected' : '' ?>
                            selected>Все статусы
                    </option>
                    <option value="norm" style='font-weight:bold' <?= $_GET['stage'] == 'norm' ? 'selected' : '' ?>>Нет
                        замечаний
                    </option>
                    <option value='unnorm' style='font-weight:bold' <?= $_GET['stage'] == 'unnorm' ? 'selected' : '' ?>>
                        Не заполнено
                    </option>
                    <option value='unchecked' style='font-weight:bold'
                            <?= $_GET['stage'] == 'unchecked' ? 'selected' : '' ?>>Не проверено
                    </option>
                    <option value='poverka' style='font-weight:bold'
                            <?= $_GET['stage'] == 'poverka' ? 'selected' : '' ?>>Истекает срок поверки
                    </option>
                    <option value='poverka_alarm' style='font-weight:bold'
                            <?= $_GET['stage'] == 'poverka_alarm' ? 'selected' : '' ?>>Истек срок поверки
                    </option>
					<option value='archive' style='font-weight:bold'
						<?= $_GET['stage'] == 'archive' ? 'selected' : '' ?>>Архив
					</option>
					<option value='longstorage' style='font-weight:bold'
						<?= $_GET['stage'] == 'longstorage' ? 'selected' : '' ?>>На длительном
					</option>
					<option value='vagon' style='font-weight:bold'
						<?= $_GET['stage'] == 'vagon' ? 'selected' : '' ?>>Вагоны
					</option>


                </select>
            </td>
            <td>
                <select class="oborud" name="place_of_installation_or_storage" id="laba"
                        onchange="<?= $location_laba ?>"
                        style="float: right; width: 100%;">
                    <option value="0" selected>Все лаборатории</option>
                    <option value="9999" <?= ($_GET['laba'] == '9999' ? 'selected' : '') ?>>Вне лабораторий</option>
                    <?php
                    $select = $DB->Query("SELECT * FROM `ba_laba`");
                    while ($option = $select->Fetch()) {
                        $select_room = $DB->Query("SELECT * FROM `ROOMS` WHERE `LAB_ID`=" . $option['ID']);
                        echo "<option ";
                        if ($_GET['laba'] == $option['ID']) {
                            echo "selected";
                        }
                        echo " value='" . $option['ID'] . "' style='font-weight:bold'>" . $option['NAME'] . "</option>";
                        while ($option_room = $select_room->Fetch()) {
                            $id_room = (int)$option_room['ID'] + 100;
                            echo "<option ";
                            if ((int)$_GET['laba'] == $id_room) {
                                echo "selected";
                            }
                            echo " value='" . $id_room . "'>  -- " . $option_room['NAME'] . " " . ($option_room['NUMBER'] ? $option_room['NUMBER'] : '') . "</option>";
                        }

                    }
					echo "<option value='7' style='font-weight:bold' " . ((!empty($_GET['laba']) && (int)$_GET['laba'] === 7) ? 'selected' : '') . ">Отдел СМК и метрологии</option>";
					echo "<option value='132' " . ((!empty($_GET['laba']) && (int)$_GET['laba'] === 132) ? 'selected' : '') . ">  -- Помещение  303</option>";
                    ?>
                </select>
                </select></td>
        </tr>
    </table>
    <br>
    <br>
    <hr>
    <table id='oborud'>
    <thead>
    <tr>
        <th></th>
        <th>Измеряемая харак-ка</th>
        <th>Наименование</th>
        <th>Тип</th>
        <th>Идент.</th>
        <th>Заводской номер</th>
        <th>Инв. номер</th>
        <th>Ввод в экспл.</th>
        <th>Диапазон измерения</th>
        <th>Класс точности</th>
        <th>Свид-во о поверке</th>
        <th>Меcто поверки</th>
        <th>Право собственности</th>
        <th>Место установки</th>
		<th>Поверка от</th>
        <th>Поверка до</th>
        <th>В области акк.</th>
        <th>В наличии</th>
        <th>Проверено</th>
        <th>Производитель</th>
		<th>Примечание</th>
    </tr>
    <tr>
        <th></th>
        <th><input type="text" name="characteristic_filter" id="characteristic_filter" value=""></th>
        <th><input type="text" name="name_filter" id="name_filter" value=""></th>
        <th><input type="text" name="type_filter" id="type_filter" value=""></th>
        <th><input type="text" name="os_filter" id="os_filter" style="width:50px" value=""></th>
        <th><input type="text" name="factory_number_filter" id="factory_number_filter" value=""></th>
        <th><input type="text" name="inv_number_filter" id="inv_number_filter" value=""></th>
        <th><input type="text" name="date_exp_filter" id="date_exp_filter" value=""></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
		<th><input type="text" name="last_metr_control_filter" id="last_metr_control_filter" value=""></th>
        <th><input type="text" name="metr_control_filter" id="metr_control_filter" value=""></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    </thead>

    <tbody>
    <div id='buttonRightTable'></div>
    <div id='buttonLeftTable'></div>
    <?php
	$poverka_time = 5184000;
    $c = 1;
    $title = '';
    $sort = '';
    while ($row = $res->Fetch()) {
        $poverka = strtotime($row['POVERKA']) - strtotime(date('Y-m-d'));
		if (($poverka > $poverka_time || $row['NO_METR_CONTROL']) && $row['CHECKED'] && $row['IN_STOCK'] && !($row['LONG_STORAGE'] || !empty($row['SPISANIE']))) {
            $color = 'd1ffca';
            $c = 1;
            $title = 'С оборудованием все ОК';
            $sort = 'norm';
        } elseif (($poverka < 0 )&&!$row['NO_METR_CONTROL'] && !($row['LONG_STORAGE'] || !empty($row['SPISANIE']))) {
            $color = 'fbb';
            $c = 2;
            $title = 'Истек срок поверки!';
            $sort = 'poverka_alarm';
        } elseif (($poverka > $poverka_time || $row['NO_METR_CONTROL']) && $row['CHECKED'] == '0' && $row['IN_STOCK'] && !($row['LONG_STORAGE'] || !empty($row['SPISANIE']))) {
            $color = '9999cc';
            $c = 3;
            $title = 'Оборудование не проверено отделом метрологии!';
            $sort = 'unchecked';
        } elseif(!$row['NO_METR_CONTROL'] && !($row['LONG_STORAGE'] || !empty($row['SPISANIE']))) {
            $color = 'ffa';
            $c = 4;
            $title = 'До истечения срока поверки осталось менее 60 дней!';
            $sort = 'poverka';
        }
        if($row['LONG_STORAGE'] || !empty($row['SPISANIE'])) {
            $color = 'ccc';
            $c = 5;
            $title = 'Оборудование списано или находится на консервации';
            $sort = 'archive';
        }
//		if($row['IN_STOCK'] == '0'&&!$row['LONG_STORAGE']&&(empty($row['SPISANIE'])||$row['SPISANIE']==''))
//		{
//			$color = 'cc0000';
//            $c = 7;
//            $title = 'Оборудования нет в наличии!';
//            $sort = 'no_in_stock';
//            //echo $row['ID']." " ;
//		}


		if ($_GET['stage'] == 'norm' && $sort == 'norm'
			|| $_GET['stage'] == 'unnorm' && $sort == 'unnorm'
			|| $_GET['stage'] == 'unchecked' && $sort == 'unchecked'
			|| $_GET['stage'] == 'poverka' && $sort == 'poverka'
			|| $_GET['stage'] == 'poverka_alarm' && $sort == 'poverka_alarm'
			|| !$_GET['stage'] || $_GET['stage'] == 'archive'
			|| $_GET['stage'] == 'vagon' || $_GET['stage'] == 'all'
			|| $_GET['stage'] == 'longstorage') {

            //echo $row['IN_STOCK'];

                //Находим для оборудования данные (сведетельство о поверке)
                $res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$row['ID']);
                $row_certificate = $res_certificate_select->Fetch();
                $certificateName_arr = unserialize($row_certificate['name_f']);
                $certificateName_arr = unserialize($row_certificate['name_f']);

                foreach($certificateName_arr as $cName){
                    $cert_arr[]=$cName;
                }

                $certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v;
            ?>
            <tr>
                <td style="background-color: #<?= $color ?>; color: #<?= $color ?>" title="<?= $title ?>">
                    <label><?= $c ?></label></td>
                <td><?= $row['NAME'] ?></td>
                <td><a href='/oborud.php?ID=<?= $row['ID'] ?>'><?= $row['OBJECT'] ?></a></td>
                <td><?= $row['TYPE_OBORUD'] ?></td>
                <td><?php $ident = "";
                    switch ($row['IDENT']) {
                        case 'SI':
                            $ident = "СИ";
                            break;
                        case 'IO':
                            $ident = "ИО";
                            break;
                        case 'VO':
                            $ident = "ВО";
                            break;
                        case 'TS':
                            $ident = "ТС";
                            break;
                        case 'SO':
                            $ident = "ТС";
                            break;
                        case 'REACT':
                            $ident = "Реактивы";
                            break;
                        case 'OOPP':
                            $ident = "ООПП";
                            break;
                    }
                    echo $ident;
                    ?></td>
                <td><?= $row['FACTORY_NUMBER'] ?></td>
                <td><?= $row['REG_NUM'] ?></td>
                <td><?= date("d.m.Y", strtotime($row['god_vvoda_expluatation'])) ?></td>
                <td><?= $row['measuring_range'] ?></td>
                <td><?= $row['сlass_precision_and_accuracy'] ?></td>
                <td><?= $certificateName ?></td>
                <td><?= $row['POVERKA_PLACE'] ?></td>
                <td><?= $row['property_rights'] ?></td>
                <td>
                    <?php
                    $result = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID`=" . $row['place_of_installation_or_storage'])->Fetch();
                    echo $result['NAME'];
                    ?>
                </td>
				<td><?= date("d.m.Y", strtotime($row['POVERKA_LAST'])) ?></td>
                <td><?= date("d.m.Y", strtotime($row['POVERKA'])) ?></td>
                <td><?= ($row['IN_AREA']) ? 'В области' : 'Не в области' ?></td>
                <td><?= ($row['IN_STOCK']) ? 'В наличии' : 'Нет в наличии' ?></td>
                <td><?= ($row['CHECKED']) ? 'Да' : 'Нет' ?></td>
                <td><?= $row['manufacturer'] ?></td>
                <td><?= $row['note'] ?></td>
            </tr>
            <?php
        }
    }
    echo "</tbody></table>";

}

?>
    <table>
        <tr>
            <td> План-график
            </td>
            <td><select id="type_sel">
                    <option value="1">поверки СИ</option>
                    <option value="2">калибровки СИ</option>
                    <option value="3">аттестации ИО</option>
                    <option value="4">проведения измерений ТС</option>
                </select></td>
            <td>
                на
            </td>
            <td><select id="year_sel">
                    <option>2022</option>
                    <option>2021</option>
                    <option>2020</option>
                    <option>2019</option>
                </select></td>
            <td> год</td>
            <td><input type="button" value="Скачать"
                       onclick="window.open ('/protocol_generator/plan_graphic.php?YEAR=' + document.getElementById ('year_sel').value +'&TYPE='+document.getElementById ('type_sel').value, '_blank')">
            </td>
    </table>
    <link rel="stylesheet" type="text/css" href="/DataTables/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
    <!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"/>-->
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>

    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
    <script type="text/javascript" language="javascript"
            src="/jszip/jquery.dataTables.customSortandFilters.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>

    <!--<script type="text/javascript" src="DataTables/datatables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/dataTables.bootstrap4.min.css"/>
    <link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/buttons/1.3.1/css/buttons.bootstrap4.min.css"/>
    <script type="text/javascript" src="http://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="http://cdn.datatables.net/buttons/1.3.1/js/buttons.bootstrap4.min.js"></script>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="http://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
    <script type="text/javascript" src="http://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
    <script type="text/javascript" src="http://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>-->
    <script>
        $(document).ready(function () {
            table = $('#oborud').DataTable({
				"sDom": 'Bfrtlip',
                "language": {
                    "processing": "Подождите...",
                    "search": "Поиск:",
                    "lengthMenu": "Показать _MENU_ записей",
                    "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                    "infoEmpty": "Записи с 0 до 0 из 0 записей",
                    "infoFiltered": "(отфильтровано из _MAX_ записей)",
                    "infoPostFix": "",
                    "loadingRecords": "Загрузка записей...",
                    "zeroRecords": "Записи отсутствуют.",
                    "emptyTable": "В таблице отсутствуют данные",
                    "paginate": {
                        "first": "Первая",
                        "previous": "Предыдущая",
                        "next": "Следующая",
                        "last": "Последняя"
                    },
                    "buttons": {
                        "copy": "",
                        'colvis': "",
                        'print': "",
                        "excel": ""
                    },
                    "aria": {
                        "sortAscending": ": активировать для сортировки столбца по возрастанию",
                        "sortDescending": ": активировать для сортировки столбца по убыванию"
                    }
                },
                "iDisplayLength": 50,
                "order": [[2, "asc"]],
                "colReorder": true,
                "bSortCellsTop": true,
                "dom": 'Blfrtip',
                // "sScrollX": '100%',
                //"scrollY": 500,
                //"scrollX": true,
                "buttons": ['copy', 'excel', 'colvis', 'print'],
                "columnDefs": [
                    {
                        "targets": 1,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 2,
                        "render": $.fn.dataTable.render.ellipsis(60, true)
                    },
                    {
                        "targets": 5,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 8,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {"sType": "ruDate", "targets": 7},
                    {
                        "targets": 9,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 10,
                        "render": $.fn.dataTable.render.ellipsis(120, true)
                    },
                    {
                        "targets": 11,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 12,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
                    {
                        "targets": 13,
                        "render": $.fn.dataTable.render.ellipsis(50, true)
                    },
					{
                        "targets": 20,
                        "render": $.fn.dataTable.render.ellipsis(30, true)
                    },
                    {"sType": "ruDate", "targets": 14},
					{"sType": "ruDate", "targets": 15}
                ]

            });

            $('#characteristic_filter').on('keyup', function () {
                table
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#name_filter').on('keyup', function () {
                table
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#type_filter').on('keyup', function () {
                table
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#os_filter').on('keyup', function () {
                table
                    .columns(4)
                    .search(this.value)
                    .draw();
            });

            $('#factory_number_filter').on('keyup', function () {
                table
                    .columns(5)
                    .search(this.value)
                    .draw();
            });
			$('#inv_number_filter').on('keyup', function () {
                table
                    .columns(6)
                    .search(this.value)
                    .draw();
            });
			$('#date_exp_filter').on('keyup', function () {
                table
                    .columns(7)
                    .search(this.value)
                    .draw();
            });
			$('#last_metr_control_filter').on('keyup', function () {
                table
                    .columns(14)
                    .search(this.value)
                    .draw();
            });
			$('#metr_control_filter').on('keyup', function () {
                table
                    .columns(15)
                    .search(this.value)
                    .draw();
            });
        });

        jQuery.extend(jQuery.fn.dataTableExt.oSort, {
            "ruDate-asc": function (a, b) {
                var ruDatea = $.trim(a).split('.');
                var ruDateb = $.trim(b).split('.');

                if (ruDatea[2] * 1 < ruDateb[2] * 1)
                    return 1;
                if (ruDatea[2] * 1 > ruDateb[2] * 1)
                    return -1;
                if (ruDatea[2] * 1 == ruDateb[2] * 1) {
                    if (ruDatea[1] * 1 < ruDateb[1] * 1)
                        return 1;
                    if (ruDatea[1] * 1 > ruDateb[1] * 1)
                        return -1;
                    if (ruDatea[1] * 1 == ruDateb[1] * 1) {
                        if (ruDatea[0] * 1 < ruDateb[0] * 1)
                            return 1;
                        if (ruDatea[0] * 1 > ruDateb[0] * 1)
                            return -1;
                    } else
                        return 0;
                }
            },

            "ruDate-desc": function (a, b) {
                var ruDatea = $.trim(a).split('.');
                var ruDateb = $.trim(b).split('.');

                if (ruDatea[2] * 1 < ruDateb[2] * 1)
                    return -1;
                if (ruDatea[2] * 1 > ruDateb[2] * 1)
                    return 1;
                if (ruDatea[2] * 1 == ruDateb[2] * 1) {
                    if (ruDatea[1] * 1 < ruDateb[1] * 1)
                        return -1;
                    if (ruDatea[1] * 1 > ruDateb[1] * 1)
                        return 1;
                    if (ruDatea[1] * 1 == ruDateb[1] * 1) {
                        if (ruDatea[0] * 1 < ruDateb[0] * 1)
                            return -1;
                        if (ruDatea[0] * 1 > ruDateb[0] * 1)
                            return 1;
                    } else
                        return 0;
                }
            }
        });


    </script>
    <script>
        window.onload = function () {

            var status = document.getElementById("status").value;

            if (status != "1" && status != "9" && status != "10" && status != "61" && status!="33" && status!="34" && status!="32" && status!="7" && status!="69" && status!="27" && status!="17" && status!="101" && status!="137") {
                var input = document.querySelectorAll("input");
                var select = document.querySelectorAll("select");
                var textarea = document.querySelectorAll("textarea");
                for (var i = 0, cnt = input.length; i < cnt; i++) {
                    input[i].disabled = true;
                }
                for (var i = 0, cnt = select.length; i < cnt; i++) {
                    select[i].disabled = true;
                }
                for (var i = 0, cnt = textarea.length; i < cnt; i++) {
                    textarea[i].disabled = true;
                }

            }

        };


    </script>

    <script>
        $(document).ready(function () {
            $("#oborud_table").on("click", ".del", function () {
				var tr = $(this).closest('tr');
				var wrapperDel = tr.find('td:nth-child(3) > div.wrapperDel');
				var trHidden = tr.next('.trHidden');
				var pathFileDel = wrapperDel.find('a').attr('href');
				trHidden.find('.hiddenDel').val(pathFileDel);

				//--Дата начала и окончания для каждого св-ва в карточке оборудования начало--//
				//Удаляем Актуальный сертификат удаляемого Свидетельства о поверке
				console.log($(this).closest('tr'));
				console.log($(this).closest('tr').prevAll('.trRelevanceCertificate').first());
				$(this).closest('tr').prevAll('.trRelevanceCertificate').first().remove();
				//Удаляем Дата начала поверки удаляемого Свидетельства о поверке
				$(this).closest('tr').prevAll('.trDateStartVerification').first().remove();
				//Удаляем Дата окончания поверки удаляемого Свидетельства о поверке
				$(this).closest('tr').prevAll('.trDateEndVerification').first().remove();
				//--Дата начала и окончания для каждого св-ва в карточке оборудования окончание--//
                $(this).closest('tr').remove();

            });

            $("#oborud_table").on("click", ".add2", function () {
                //tr = $(this).closest('tr');
                var trLast = $('tr.trCertificate:last');

				//--Дата начала и окончания для каждого св-ва в карточке оборудования начало--//
				//находим индекс(номер) последнего сертификата
				var valLastRelevanceCertificate = $('.relevance-certificate:last') ? $('.relevance-certificate:last').val() : 0;

				//следующий добавленый индекс(номер) последнего сертификата
				var nextValRelevanceCertificate = Number(valLastRelevanceCertificate) + 1;

                $.ajax({
					method: 'post',
                    url: 'tr_for_oborud.php',
					data: {next_relevance_certificate: nextValRelevanceCertificate},
                    success: function (data) {
                        //tr.after(data);
                        trLast.after(data);
                    }
                });

				//--Дата начала и окончания для каждого св-ва в карточке оборудования окончание--//
            });

            $("#oborud_table").on("click", ".add3", function() {

                let inp_certmp = "<input type='text' class='oborud' name='certif_temp[]' style='width: 67px; margin-right: 5px; padding: 5px;' placeholder='+110 °С'>";
                $('#tr_cert_temp').append(inp_certmp);

                });
        });
    </script>

	    <script>
        // дата ввода в эксплуатацию = текущий год
        // $('form').on('submit', function(event) {
            // let inputDate = new Date($("input[name='god_vvoda_expluatation']").val());
            // let inputYear = inputDate.getFullYear();

            // let yearDate = new Date();
            // let currentYear = yearDate.getFullYear();

            // if(inputYear != currentYear) {
                // alert('Год ввода в эксплуатацию должнен быть текущим (' + currentYear + ')!');
                // event.preventDefault();
                // return false;
            // }
        // });
    </script>

    <script>
        //Прокрутка таблицы с помощью кнопок
        $(document).ready(function () {

            var container = $('.workarea-content-paddings'),
                scroll = $('.dataTable.no-footer').width(),
                topTable = $('.workarea-content-paddings').offset().top,
                topTableBody = $('.dataTable.no-footer > tbody').offset().top,
                sumHeaderHeight = topTableBody - topTable;

            $('#buttonRightTable').css('top', sumHeaderHeight + "px");
            $('#buttonLeftTable').css('top', sumHeaderHeight + "px");

            $('#buttonRightTable').hover(function () {
                container.animate({
                    'scrollLeft': scroll
                }, {duration: 4000, queue: false});
            }, function () {
                container.stop();
            });
            $('#buttonLeftTable').hover(function () {
                container.animate({
                    'scrollLeft': -scroll
                }, {duration: 4000, queue: false});
            }, function () {
                container.stop();
            });

            $(document).scroll(function () {
                var positionScroll = $(window).scrollTop();
                if (positionScroll > 250 && positionScroll < 2550) {
                    $('#buttonRightTable').css('transform', 'translateY(' + (positionScroll - 250) + 'px' + ')');
                    $('#buttonLeftTable').css('transform', 'translateY(' + (positionScroll - 250) + 'px' + ')');
                }
            });

            if ($(window).outerWidth(true) < 1083 && $(window).outerWidth(true) > 410) {
                $('#buttonRightTable').css('right', (1083 - $(window).outerWidth(true)) + "px");
            } else {
                $('#buttonRightTable').css('right', 0 + "px");
            }

            $(window).resize(function () {
                if ($(window).outerWidth(true) < 1083 && $(window).outerWidth(true) > 410) {
                    $('#buttonRightTable').css('right', (1083 - $(window).outerWidth(true)) + "px");
                } else {
                    $('#buttonRightTable').css('right', 0 + "px");
                }
            });

        });
    </script>
    <script>
      $(document).ready(function () {
            $("#oborud_table").on("click", ".delFile", function () {
                $(this).closest('.wrapperDel').css('display', 'none');
                $(this).closest('td').find('input[type=file]').css('display', 'block');
            });

			$("#oborud_table").on("click", ".delFile", function () {
				var tr = $(this).closest('tr');
				var wrapperDel = $(this).closest('.wrapperDel');
				var trHidden = tr.next('.trHidden');
				var pathFileDel = wrapperDel.find('a').attr('href');
				trHidden.find('.hiddenDel').val(pathFileDel);
				console.log(pathFileDel)
            });
        });
    </script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
