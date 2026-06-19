<?php


use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

function dump($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}
$ba_gost=[];
$i=1;
//global $DB;
$res = $DB->Query("SELECT * FROM `OA_VV` WHERE `ID`=".$_GET['ID_V'])->Fetch();
//$res_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=".$res['ID_GOST'])->Fetch();
$res_gost = $DB->Query("select um.*, ug.reg_doc, ug.description, ug.year, ug.materials  
						from `ulab_methods` as um 
						join `ulab_gost` as ug on ug.id = um.gost_id
						where um.`id`=".$res['ID_GOST'])->Fetch();

$lab = $DB->Query("select * from ba_laba where id_dep = {$res['lab']}")->Fetch()['short_name'];

$assignedSql = $DB->Query("SELECT *  FROM `b_uts_iblock_5_section` lab, `b_user` u 
						WHERE lab.`VALUE_ID` = {$res['lab']} AND 
						lab.`UF_HEAD` = u.`ID`")->Fetch();

$name = trim($assignedSql['NAME']);
$lastName = trim($assignedSql['LAST_NAME']);
$secondName = trim($assignedSql['SECOND_NAME']);
$shortName = mb_substr($name, 0, 1, 'UTF-8') . '. '. mb_substr($secondName, 0, 1, 'UTF-8') . '. ' . $lastName;
$work_position =  trim($assignedSql['WORK_POSITION']);
//$lab="';
//if($res_gost['LFHI']) $lab='ЛФХИ';
//elseif($res_gost['LFMI']) $lab='ЛФМИ';
//elseif($res_gost['DSL']) $lab='ДСЛ';
//elseif($res_gost['LSM']) $lab='ЛСМ';
//elseif($res_gost['OSK']) $lab='ОСК';

//$assigned = CUser::GetByID($res_gost['ASSIGNED'])->Fetch();

//dump($assigned);
//--Проверка верификации методики начало--//
//Массив с несоответствиями Даты отчета и Дат Свидетельсва о поверке
//$arrayNotMatch = [];
//--Проверка верификации методики окончание--//
//echo'<pre>';var_dump('$res[DATE]', $res['DATE']);echo'</pre>';
$idSI = []; $idIO = []; $idVO = []; $sert=[];
//$res_ob = $DB->Query("SELECT * FROM `ba_connection`,`ba_oborud` WHERE `ba_connection`.`GOST_ID`=" . $res['ID_GOST'] . " AND `ba_oborud`.`ID`=`ba_connection`.`OBORUD_ID` ORDER BY `OBJECT`");
$res_ob = $DB->Query("SELECT * FROM `ulab_methods_oborud` as umo,`ba_oborud` as bo WHERE umo.`method_id`=" . $res['ID_GOST'] . " AND `bo`.`ID`= umo.`id_oborud` ORDER BY `OBJECT`");
    while ($row = $res_ob->Fetch()) {
		$idOrorud = !empty($row['id_oborud']) ? $row['id_oborud'] : ''; // ID Оборудования
		$object = !empty($row['OBJECT']) ? $row['OBJECT'] : ''; // ID Оборудования
		$reportDate = !empty($res['DATE']) ? $res['DATE'] : ''; // Дата отчета

        $res_certificate_select = $DB->Query("SELECT * FROM `ba_oborud_certificate` WHERE `is_actual` = 1 and `oborud_id`=" . (int)$row['id_oborud']);
//		dump($res_certificate_select);
		$arrCertificate = [];
		$arrayNotMatch = [];
		$match = 0;
		$noDate = 0;
		$dateNotMatch = 0;
		while ($row_certificate = $res_certificate_select->Fetch()) {
			if (!empty($reportDate) && !empty($row_certificate['date_start']) && !empty($row_certificate['date_end'])
				&& (strtotime($row_certificate['date_start']) <= strtotime($reportDate)
				&& strtotime($reportDate) <= strtotime($row_certificate['date_end'])) ) {
					if ((empty($row_certificate['date_start']) || empty($row_certificate['date_end']))&&!$row['NO_METR_CONTROL']) {
						$arrayNotMatch[] = "<a href='/ulab/oborud/edit/" . $idOrorud . "'>У оборудования: " . $object . " отутствуют даты свидетельства о поверке</a><br>";
					}
			} else {
				$arrayNotMatch[] = "Создание отчета не возможно. Проверьте дату отчета, дату поверок. Оборудование <a href='/ulab/oborud/edit/" . $idOrorud . "'>" . $object . "</a><br>";
			}
			$dateEnd = date("d.m.Y", strtotime($row_certificate['date_end']));
			$arrCertificate[] = "{$row_certificate['name']} до {$dateEnd}";
		}
		$certificateName_arr = $row_certificate['name'];
		//--Проверка верификации методики начало--//

		$provercaLast = !empty($row_certificate['date_start']) ? $row_certificate['date_start'] : ''; //Дата последней поверки актуального (последнего) сертификата о поверке проверяемого оборудования
		$proverca = !empty($row_certificate['date_end']) ? $row_certificate['date_end'] : ''; //Дата окончания поверки актуального (последнего) сертификата о поверке проверяемого оборудования
		$dateStartVerification = !empty($row_certificate['date_start_verification']) ? unserialize($row_certificate['date_start_verification']) : ''; //Массив Дата начала поверки (Свидетельсво о поверки)
		$dateEndVerification = !empty($row_certificate['date_end_verification']) ? unserialize($row_certificate['date_end_verification']) : ''; //Массив Дата окончания поверки (Свидетельсво о поверки)
		$relevanceCertificate = isset($row_certificate['is_actual']) ? $row_certificate['is_actual'] : ''; //Актуальность сертификата (Свидетельсво о поверки)
	
		//--Проверка верификации методики окончание--//
		$certString = implode('; ', $arrCertificate);
		$cert_arr=array();
		foreach($certificateName_arr as $cName)
		{
			$cert_arr[]=$cName;
		}
		$ob_v = ($row['certificate_of_verification'] ? $row['certificate_of_verification'] : '');
//		$certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v;
		$certificateName = $cName ? $cName : $ob_v;
//		$oborud =  $row['OBJECT'] . ", инв. номер " . $row['REG_NUM']. ", зав. номер ".$row['FACTORY_NUMBER'].", ".$certificateName." до ".date("d.m.Y", strtotime($row['POVERKA']));
		$oborud =  $row['OBJECT'] . ", инв. номер " . $row['REG_NUM']. ", зав. номер ".$row['FACTORY_NUMBER'].", ".$certString;
		//--Проверка верификации методики начало--//
		
		//Соответствие даты очтета
//		$match = 1;
//		//Отсутствуют даты у свидетельства о поверки
//		$noDate = 0;
//		//не сответствуют даты у свидетельства о поверки c датой очтета
//		$dateNotMatch = 0;
//		//Проверяем дату очтета, если не входит в диапазон актуального (последнего) сертификата о поверке проверяемого оборудования
//		// TODO: 123
//		if (!empty($reportDate) && !empty($provercaLast) && !empty($proverca) && !($provercaLast <= $reportDate && $reportDate <= $proverca)) {
//
//			$match = 0;
//
//			//Если мысивы дат, Дата начала поверки и Дата окончания поверки (Свидетельсва о поверки) существуют, то перебираем даты и сравниваем с Датой отчета
//			if (!empty($dateStartVerification) && !empty($dateEndVerification)) {
//
//				foreach($dateStartVerification as $kyeDateVerification => $valDateStartVerification) {
//					//Дата окончания поверки
//					$valDateEndVerification = !empty($dateEndVerification[$kyeDateVerification]) ? $dateEndVerification[$kyeDateVerification] : '';
//					//Наименование свидетельства о поверки
//					$currentCertificateName = !empty($certificateName_arr[$kyeDateVerification]) ? $certificateName_arr[$kyeDateVerification] : '';
//
//					//Сравниваем дату отчета с другими датами Свидетельст о поверки, если входит в диапозон, то выбираем данный сертификат(Свидетельство о поверки)
//					if (!empty($valDateStartVerification) && !empty($valDateEndVerification) && ($valDateStartVerification <= $reportDate && $reportDate <= $valDateEndVerification)) {
//						$match = 1;
//						$oborud =  $row['OBJECT'] . ", инв. номер " . $row['REG_NUM']. ", зав. номер ".$row['FACTORY_NUMBER'].", ".$currentCertificateName." до ".date("d.m.Y", strtotime($valDateEndVerification));
//
//					} else if (!empty($valDateStartVerification) && !empty($valDateEndVerification) && !($valDateStartVerification <= $reportDate && $reportDate <= $valDateEndVerification)) {
//						$dateNotMatch = 1;
//					} else if ((empty($valDateStartVerification) || empty($valDateEndVerification))&&!$row['NO_METR_CONTROL']) {
//						$noDate = 1;
//					}
//				}
//
//			} else if (empty($dateStartVerification) || empty($dateEndVerification)) {
//				$arrayNotMatch[] = "<a href='/ulab/oborud/edit/" . $idOrorud . "'>У оборудования: " . $object . " отутствуют даты свидетельства о поверке</a><br>";
//			}
//		}
//
//		//Если дата отчета не соответствует добавляем ссылки в массив не соответствий Даты отчета и Дат Свидетельсва о поверке
//		if (empty($match)&&!$row['NO_METR_CONTROL']) {
//			if (!empty($noDate) && !empty($dateNotMatch)) {
//				$arrayNotMatch[] = "<a href='/ulab/oborud/edit/" . $idOrorud . "'>Дата отчета не соответствует указанным датам у oборудования: " . $object . "</a><br>";
//			} else if (empty($noDate) && !empty($dateNotMatch)) {
//				$arrayNotMatch[] = "<a href='/ulab/oborud/edit/" . $idOrorud . "'>Дата отчета не соответствует указанным датам у oборудования: " . $object . "</a><br>";
//			} else if (!empty($noDate) && empty($dateNotMatch)) {
//				$arrayNotMatch[] = "<a href='/ulab/oborud/edit/" . $idOrorud . "'>У оборудования: " . $object . " отутствуют даты свидетельства о поверке</a><br>";
//			}
//		}
		
		//$oborud =  $row['OBJECT'] . ", инв. номер " . $row['REG_NUM']. ", зав. номер ".$row['FACTORY_NUMBER'].", ".$certificateName." до ".date("d.m.Y", strtotime($row['POVERKA']));
		//--Проверка верификации методики окончание--//
		if($row['IDENT'] == 'SI') 
		$idSI[] = ['SI' => $oborud, 'GOST_SI' => $row['gost'], 'COMMENT_SI' => $row['comment']];
		elseif($row['IDENT'] == 'IO')
		$idIO[] = ['IO' => $oborud, 'GOST_IO' => $row['gost'], 'COMMENT_IO' => $row['comment']];
		elseif($row['IDENT'] == 'VO')
		$idVO[] = ['VO' => $oborud, 'GOST_VO' => $row['gost'], 'COMMENT_VO' => $row['comment']];
		
		

    }
	
	//Если есть не соответствие Даты отчета и Дат Свидетельсва о поверке, выводим ссылки
	if (!empty($arrayNotMatch)) {
		foreach($arrayNotMatch as $keyNotMatch => $valNotMatch) {
			echo $valNotMatch . '<br>';
		}

//		exit();
	}


$file = new Bitrix\Main\IO\File(__DIR__ . '/../Validation.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$document->normalizeContent();
$document->setValues([
	'GOST' => $res_gost['reg_doc'],
	'GOST_YEAR' => $res_gost['year'] ? "-".$res_gost['year'] : '',
	'GOST_NAME' => $res_gost['description'],
	'GOST_PUNKT' => $res_gost['clause'],
    'OPR_HAR' => $res_gost['name'] ? $res_gost['name'] : '--',
	'Lab' => $lab,
	'Assigned' => $shortName,
	'GOST_OBJECT' => $res_gost['materials'],
	'TEMP' => $res['TEMP'],
	'VLAG' => $res['VLAG'],
	'WORK_POS' => $work_position,
	'Validation' => $res['VALIDATION'] ? "3.7	Результаты испытаний по ГОСТ ".$res_gost['reg_doc']." приведены в приложении." : "",
	
	
   'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $idSI,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
	'TableItemSI' => 'Table.Item.SI',
	'TableItemGOST_SI' => 'Table.Item.GOST_SI',
	'TableItemCOMMENT_SI' => 'Table.Item.COMMENT_SI',

    'Table2' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $idIO,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
	'Table2ItemIO' => 'Table2.Item.IO',
	'Table2ItemGOST_IO' => 'Table2.Item.GOST_IO',
	'Table2ItemCOMMENT_IO' => 'Table2.Item.COMMENT_IO',
	
	'Table3' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $idVO,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
	'Table3ItemVO' => 'Table3.Item.VO',
	'Table3ItemGOST_VO' => 'Table3.Item.GOST_VO',
	'Table3ItemCOMMENT_VO' => 'Table3.Item.COMMENT_VO',
]

);

$result = $document->process();
$content = $document->getContent();

//вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Отчет о верификации и валидации.docx"');
header('Cache-Control: max-age=0');
echo $content; 
die;



