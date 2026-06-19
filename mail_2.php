<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require($_SERVER["DOCUMENT_ROOT"] . "/PHPMailer/PHPMailer.php");
require($_SERVER["DOCUMENT_ROOT"] . "/PHPMailer/SMTP.php");

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
//include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";

function mail_to ($addr, $name, $client_name, $msg, $attachment, $subj)
{
// Создаем письмо
$mail = new PHPMailer();
$mail->isSMTP();                   // Отправка через SMTP
$mail->CharSet = 'UTF-8';
$mail->Host   = 'smtp.yandex.ru';  // Адрес SMTP сервера
$mail->SMTPAuth   = true;          // Enable SMTP authentication
//$mail->Username   = 'niistrom74';       // ваше имя пользователя (без домена и @)

$mail->Username   = 'lab@2170812.ru';       // ваше имя пользователя (без домена и @)
$mail->Password   = 'nslkgfytppmbkafu';    // ваш пароль
//$mail->Username   = 'AlexKov123456@yandex.ru';
//$mail->Password   = 'Kov123456';

$mail->SMTPSecure = 'ssl';         // шифрование ssl
$mail->Port   = 465;               // порт подключения
 
//$mail->setFrom('niistrom74@yandex.ru', $name);    // от кого	

$mail->setFrom('lab@2170812.ru', $name);    // от кого
//$mail->setFrom('AlexKov123456@yandex.ru', $name);
	 
$mail->addAddress($addr, $client_name); // кому

$mail->addAttachment($attachment); // прикрепляем один файл

 
$mail->Subject = $subj;

$mail->AddEmbeddedImage('./DataTables/DataTables-1.10.18/images/logo.png', 'logo_company');//прикрепляем картинку для отображения в теле письма

$mail->msgHTML($msg);

//dump($mail);

// Отправляем
if ($mail->send()) {
  echo 'Письмо отправлено!';
	//"&BB4EQgQ,BEAEMAQyBDsENQQ9BD0ESwQ1-" - папка исходящие
	$mail->copyToFolder("&BB4EQgQ,BEAEMAQyBDsENQQ9BD0ESwQ1-");
} else {
  echo 'Ошибка: ' . $mail->ErrorInfo;
}

}

if (CModule::IncludeModule('crm') && !empty($_GET['ID'])) {
	$dealRequest = CCrmDeal::GetByID((int)$_GET['ID']);
}
$nRequest = (!empty($dealRequest["TITLE"]) ? $dealRequest["TITLE"] : '');
$dRequest = (!empty($dealRequest["DATE_CREATE"]) ? date("d.m.Y", strtotime($dealRequest["DATE_CREATE"])) : '');

// if (!empty($_GET['TZ_ID'])) {
	// $resKp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . (int)$_GET['TZ_ID'])->Fetch();
	// $resAktVR = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . (int)$_GET['TZ_ID'])->Fetch();
// }

// $idKp = (!empty($resKp["ID"]) ? $resKp["ID"] : '');
// $dKp = (!empty($resKp["DATE"]) ? date("d.m.Y", strtotime($resKp["DATE"])) : '');
// $yKp = (!empty($resKp["DATE"]) ? date("Y", strtotime($resKp["DATE"])) : '');

// if (!empty($_GET['ID'])) {
	// $resDogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
// }

// $nDogovor = (!empty($resDogovor["NUMBER"]) ? $resDogovor["NUMBER"] : '');
// $dDogovor = (!empty($resDogovor["DATE"]) ? date("d.m.Y", strtotime($resDogovor["DATE"])) : '');
// $flAkt=0;

// if (!empty($_GET['TZ_ID'])) {
	// $resAnnexTz = $DB->Query("SELECT * FROM `TZ_DOC` WHERE `TZ_ID`=" . (int)$_GET['TZ_ID'])->Fetch();
// }

// $nTz = (!empty($resAnnexTz["TZ_ID"]) ? $resAnnexTz["TZ_ID"] : '');
// $dTz = (!empty($resAnnexTz["DATE"]) ? date("d.m.Y", strtotime($resAnnexTz["DATE"])) : '');
// $yTz = (!empty($resAnnexTz["DATE"]) ? date("Y", strtotime($resAnnexTz["DATE"])) : '');

// if (!empty($_GET['TZ_ID'])) {
	// $resTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID'])->Fetch();
// }

// if (CModule::IncludeModule('crm') && !empty($resTz['ID_Z'])) {
	// $dealRequest = CCrmDeal::GetByID((int)$resTz['ID_Z']);
// }
// $nRequestDogovor = (!empty($dealRequest["TITLE"]) ? $dealRequest["TITLE"] : '');
// $dRequestDogovor = (!empty($dealRequest["DATE_CREATE"]) ? date("d.m.Y", strtotime($dealRequest["DATE_CREATE"])) : '');

// if (!empty($_GET['TZ_ID'])) {
	// $resAcc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $_GET['TZ_ID'])->Fetch();
// }

// $nAcc = (!empty($resAcc["ID"]) ? $resAcc["ID"] : '');
// $dAcc = (!empty($resAcc["DATE"]) ? date("d.m.Y", strtotime($resAcc["DATE"])) : '');
// $yAcc = (!empty($resAcc["DATE"]) ? date("Y", strtotime($resAcc["DATE"])) : '');

// /*-----------------'Отправка писам Договор, Счет, ТЗ' начало-------------------*/
// //Получаем выбранные письма(чекбоксы)
// $check_dogovor = in_array('Договор', $_GET['CHECK']) ? 'Договор' : '';
// $check_tz = in_array('ТЗ', $_GET['CHECK']) ? 'ТЗ' : '';
// $check_account = in_array('Счет', $_GET['CHECK']) ? 'Счет' : '';
// /*-----------------'Отправка писам Договор, Счет, ТЗ' окончание----------------*/

		$msg1 = "<html>
					<head>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
					</head>
					<body style='margin:0; padding:0;'>
						<div style='max-width:680px;'>
							<table width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td>
										<table width='100%' cellspacing='0' cellpadding='0' border='0'>
											<tr>
												<td align='center'>
													<table cellpadding='10'>
														<tr>
															<td align='center'>
																<span style='font-size:30px;font-weight:700;'>Сокращенный вариант протокола по заявке ".$nRequest." от ".$dRequest."</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='15'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;font-weight:700;'>Направляем Вам сокращенный вариант протокола для ознакомления</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='left'>
													<table width='100%' cellspacing='0' cellpadding='0' border='0'>
														<tr>
															<td width='75%'>
																<table cellspacing='0' cellpadding='0' border='0'>
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201, Руководитель ИЦ Габдрахманова Алия Агзамовна</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 210, инженер ИЦ Печерская Маргарита Михайловна</span>
																		</td>
																	</tr>
																</table>
															</td>
															<td valign='top' align='center'>
																<img src='cid:logo_company' alt='Логотип компании' width='150' height='65' border='0'>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>
					</body>
				</html>";
		
		
		$subj1= "Заявка ".$nRequest." от ".$dRequest." - Заявка";
		
        $attach =  "protocol_generator/archive_prot/".$_GET['TZ_ID']."/".$_GET['ATTACH'].".pdf";
      

$addr1 = $_GET['EMAIL'];
$name = $_GET['NAME'];
$client_name = "111";



if($_GET['EMAIL']) {
	mail_to($addr1, $name, $client_name, $msg1, $attach, $subj1);
	
}
else
echo "В карточке компании не указан электронный адрес! Письмо не отправлено.";

 ?>