<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); 
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php"; 

//Получаем данные всего оборудования
//$resultSelectBaOborud = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . (int)$_GET['ID']);
$resultSelectBaOborud = $DB->Query("SELECT * FROM `ba_oborud`");

if (!empty($resultSelectBaOborud)) {
	while($oborud = $resultSelectBaOborud->Fetch()) {
		
		//Дата окончания поверки
		$proverca = !empty($oborud['POVERKA']) ? $oborud['POVERKA'] : '';
		//Дата последней поверки
		$provercaLast = !empty($oborud['POVERKA_LAST']) ? $oborud['POVERKA_LAST'] : '';
		
		$resVerificationCertificate = null;
		if (!empty($oborud['ID'])) {
			$resVerificationCertificate = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$oborud['ID'])->Fetch();
		}
		
		//Если есть Свидетельство о поверке , проверяем сколько свойств
		if (!empty($resVerificationCertificate)) {
			$nameVerificationCertificate = !empty($resVerificationCertificate['name_f']) ? unserialize($resVerificationCertificate['name_f']) : '';
			
			//echo'<pre>';var_dump('$nameVerificationCertificate11111111111111111111111111111',$nameVerificationCertificate);echo'</pre>';
			
			//Если количество свидетельств больше 1 , то пропускаем
			if ((!empty($nameVerificationCertificate) && count($nameVerificationCertificate) > 1) || empty($nameVerificationCertificate) || (!empty($nameVerificationCertificate) && in_array('-', $nameVerificationCertificate))) {
				continue;
			}
			
			$serializeDateStartVerification = !empty($proverca) && $proverca !='1970-01-01' ? serialize([0 => $proverca]) : ''; //Даты начала поверки
			$serializeDateEndVerification = !empty($provercaLast) && $provercaLast !='1970-01-01' ? serialize([0 => $provercaLast]) : ''; //Даты окончания поверки
			$relevanceСertificate = '0'; //Актуальность свидетельств о поверки
			
			//echo'<pre>';var_dump('??????????????????',(!empty($nameVerificationCertificate) && $nameVerificationCertificate == '-'));echo'</pre>';
			//echo'<pre>';var_dump('$oborud[ID]',$oborud['ID']);echo'</pre>';
			//echo'<pre>';var_dump($nameVerificationCertificate);echo'</pre>';
			//echo'<pre>';var_dump(count($nameVerificationCertificate));echo'</pre>';
			//echo'<pre>';var_dump($serializeDateStartVerification);echo'</pre>';
			//echo'<pre>';var_dump($serializeDateEndVerification);echo'</pre>';
			//echo'<pre>';var_dump($relevanceСertificate);echo'</pre>';
			//echo'<pre>';var_dump($proverca);echo'</pre>';
			//echo'<pre>';var_dump($provercaLast);echo'</pre>';
			//echo'===============================================================';
			//$res_certificate = $DB->Query("INSERT INTO `b_verification_certificate` (`date_start_verification`, `date_end_verification`, `relevance_certificate`) 
			//VALUES ('" . $serializeDateStartVerification . "', '" . $serializeDateEndVerification . "', '" . $relevanceСertificate . "')");
			
			if (!empty((int)$oborud['ID'])) {
				$resCertificate = $DB->Query("UPDATE `b_verification_certificate` SET `date_end_verification`='" . $serializeDateStartVerification . "', `date_start_verification`='" . $serializeDateEndVerification . "', `relevance_certificate`='" . $relevanceСertificate . "' WHERE `id_oborud`=" . (int)$oborud['ID']);
			}
			
		}
	}
}
?>