<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require($_SERVER["DOCUMENT_ROOT"] . "/PHPMailer/PHPMailer.php");
require($_SERVER["DOCUMENT_ROOT"] . "/PHPMailer/SMTP.php");

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

function mail_to ($addr, $name, $client_name, $msg, $attachment, $subj)
{
$mail = new PHPMailer();
$mail->isSMTP();                   
$mail->CharSet = 'UTF-8';
$mail->Host   = 'smtp.yandex.ru';  // Адрес SMTP сервера
$mail->SMTPAuth   = true;          // Enable SMTP authentication
//$mail->Username   = 'niistrom74';       // ваше имя пользователя (без домена и @)

$mail->Username   = 'lab@2170812.ru';       // ваше имя пользователя (без домена и @)
$mail->Password   = 'nslkgfytppmbkafu';    // ваш пароль


$mail->SMTPSecure = 'ssl';         // шифрование ssl
$mail->Port   = 465;               // порт подключения
 
//$mail->setFrom('niistrom74@yandex.ru', $name);    // от кого	

$mail->setFrom('lab@2170812.ru', $name);    // от кого
	 
$mail->addAddress($addr, $client_name); 

if (is_array($attachment)) {
	foreach ($attachment as $keyAttach => $valAttach) {
		$mail->addAttachment($valAttach);
	}
} else {
	$mail->addAttachment($attachment);
}

 
$mail->Subject = $subj;

$mail->AddEmbeddedImage('./DataTables/DataTables-1.10.18/images/logo.png', 'logo_company');//прикрепляем картинку для отображения в теле письма

$mail->msgHTML($msg);


if ($mail->send()) {
  echo 'Письмо отправлено!';
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

if (!empty($_GET['TZ_ID'])) {
	$resKp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . (int)$_GET['TZ_ID'])->Fetch();
	$resAktVR = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . (int)$_GET['TZ_ID'])->Fetch();
}

$idKp = (!empty($resKp["ID"]) ? $resKp["ID"] : '');
$dKp = (!empty($resKp["DATE"]) ? date("d.m.Y", strtotime($resKp["DATE"])) : '');
$yKp = (!empty($resKp["DATE"]) ? date("Y", strtotime($resKp["DATE"])) : '');

if (!empty($_GET['ID'])) {
	$resDogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
}

$nDogovor = (!empty($resDogovor["NUMBER"]) ? $resDogovor["NUMBER"] : '');
$dDogovor = (!empty($resDogovor["DATE"]) ? date("d.m.Y", strtotime($resDogovor["DATE"])) : '');
$flAkt=0;

if (!empty($_GET['TZ_ID'])) {
	$resAnnexTz = $DB->Query("SELECT * FROM `TZ_DOC` WHERE `TZ_ID`=" . (int)$_GET['TZ_ID'])->Fetch();
}

$nTz = (!empty($resAnnexTz["TZ_ID"]) ? $resAnnexTz["TZ_ID"] : '');
$dTz = (!empty($resAnnexTz["DATE"]) ? date("d.m.Y", strtotime($resAnnexTz["DATE"])) : '');
$yTz = (!empty($resAnnexTz["DATE"]) ? date("Y", strtotime($resAnnexTz["DATE"])) : '');

if (!empty($_GET['TZ_ID'])) {
	$resTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID'])->Fetch();
}

if (CModule::IncludeModule('crm') && !empty($resTz['ID_Z'])) {
	$dealRequest = CCrmDeal::GetByID((int)$resTz['ID_Z']);
}
$nRequestDogovor = (!empty($dealRequest["TITLE"]) ? $dealRequest["TITLE"] : '');
$dRequestDogovor = (!empty($dealRequest["DATE_CREATE"]) ? date("d.m.Y", strtotime($dealRequest["DATE_CREATE"])) : '');

if (!empty($_GET['TZ_ID'])) {
	$resAcc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $_GET['TZ_ID'])->Fetch();
}

$nAcc = (!empty($resAcc["ID"]) ? $resAcc["ID"] : '');
$dAcc = (!empty($resAcc["DATE"]) ? date("d.m.Y", strtotime($resAcc["DATE"])) : '');
$yAcc = (!empty($resAcc["DATE"]) ? date("Y", strtotime($resAcc["DATE"])) : '');


$check_dogovor = in_array('Договор', $_GET['CHECK']) ? 'Договор' : '';
$check_tz = in_array('ТЗ', $_GET['CHECK']) ? 'ТЗ' : '';
$check_account = in_array('Счет', $_GET['CHECK']) ? 'Счет' : '';

if(!empty($_GET['TYPE']) && $_GET['TYPE'] == '9') {
	$number_date_request = 'Заявка';
	$number_date_request_subj = 'Заявка';
	if(!empty($_GET['ID_REQUESTS'])) {
		
		$arr_id_requests = explode('_', $_GET['ID_REQUESTS']);
		
		foreach ($arr_id_requests as $key_request => $val_id_request) { 
			
			if (CModule::IncludeModule('crm') && !empty($val_id_request)) {
				$dealRequest = CCrmDeal::GetByID((int)$val_id_request);
			}
			
			$n_request_account = (!empty($dealRequest["TITLE"]) ? $dealRequest["TITLE"] : '');
			$d_request_account = (!empty($dealRequest["DATE_CREATE"]) ? date("d.m.Y", strtotime($dealRequest["DATE_CREATE"])) : '');
			
			$number_date_request .= " ". $n_request_account . " от " . $d_request_account;
			
			$number_date_request_subj .= " ". $n_request_account . " от " . $d_request_account;
			
			if (end($arr_id_requests) != $val_id_request) {
				$number_date_request .= "<br> ";
				
				$number_date_request_subj .= ", ";
			}
		} 
	}
	
	if (!empty($_GET['ID_CONTRACT'])) {
		$res_select_invoice= $DB->Query("SELECT * FROM `INVOICE` WHERE `CONTRACT_ID`=" . (int)$_GET['ID_CONTRACT'] ." ORDER BY `ID` DESC LIMIT 1")->Fetch();
	}

	$n_acc = (!empty($res_select_invoice["ID"]) ? $res_select_invoice["ID"] : '');
	$d_acc = (!empty($res_select_invoice["DATE"]) ? date("d.m.Y", strtotime($res_select_invoice["DATE"])) : '');
	$y_acc = (!empty($res_select_invoice["DATE"]) ? date("Y", strtotime($res_select_invoice["DATE"])) : '');
	
	$actual_ver_account = (!empty($res_select_invoice["ACTUAL_VER"]) ? $res_select_invoice["ACTUAL_VER"] : '');
	
	$id_requests = !empty($_GET['ID_REQUESTS']) ? $_GET['ID_REQUESTS'] : '';
}


switch ($_GET['TYPE']) {
    case '1':
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
																<span style='font-size:30px;font-weight:700;'>Заявка ".$nRequest." от ".$dRequest." зарегистрирована в Цифровой лаборатории U-LAB</span>
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
																<span style='font-size:18px;font-weight:700;'>Просим Вас заполнить бланк Заявки <br>и отправить на почту <a href='mailto:lab@2170812.ru' target='_blank'>lab@2170812.ru</a></span>
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
		
		$attach = "Бланк_заявки_УралНИИстром.docx";
		//$subj1= "Бланк заявки на проведение испытаний от Испытательного центра ООО УралНИИстром. Заявка ".$_GET['TITLE'];
		$subj1= "Заявка ".$nRequest." от ".$dRequest." - Заявка";
		if (CModule::IncludeModule('crm')) {
        $deal = new CCrmDeal;
        $arDealUpdate = array('UF_CRM_1579541506559' => date('Y-m-d'));
        $resUpd = $deal->Update((int)$_GET['ID'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
		}
		//$request = "UPDATE `ba_tz` SET `SEND_DATE`=".date('Y-m-d')." WHERE `ID`=".$_GET['ID'];
        break;
	case '2':
        /*$msg1 = "<html><body>
                <h1>Здравствуйте!</h1>
                <p>Направляем Вам коммерческое предложение на проведение испытаний.</p>
                </body></html>";*/
				
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
																<span style='font-size:30px;font-weight:700;'>Заявка ".$nRequest." от ".$dRequest."</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='10'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;'>Цифровая лаборатория U-LAB «УралНИИстром» <br>сформировала Коммерческое предложение (КП) № ".$idKp."/".$yKp." от ".$dKp."</span>
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
																<span style='font-size:18px;font-weight:700;'>Просим Вас согласовать Коммерческое предложение № ".$idKp."  <br>и сообщить о результатах согласования на почту <a href='mailto:lab@2170812.ru' target='_blank'>lab@2170812.ru</a></span>
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
		
		$attach = "protocol_generator/archive_kp/".$_GET['ID']."/".$_GET['ATTACH'].".pdf";
		//$subj1= "Коммерческое предложение на проведение испытаний от Испытательного центра ООО УралНИИстром. Заявка ".$_GET['TITLE'];
		$subj1= "Заявка ".$nRequest." от ".$dRequest." - КП";
		$request1 = "UPDATE `KP` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
        break;
	case '3':
        /*$msg1 = "<html><body>
                <h1>Здравствуйте!</h1>
                <p>Направляем Вам договор на проведение испытаний.</p>
                </body></html>";*/
				
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
													<table cellpadding='15'>
														<tr>
															<td align='center'>
																<span style='font-size:30px;font-weight:700;'>Заявка ".$nRequestDogovor." от ".$dRequestDogovor."</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='0' cellspacing='0'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;'>Цифровая лаборатория U-LAB «УралНИИстром» сформировала:</span>
															</td>
														</tr>
														<tr>
															<td align='left' style='text-indent: 45px;'>
																<span style='font-size:18px;'>
																	- Договор № ".$nDogovor." от ".$dDogovor."
																</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='20'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;font-weight:700;'>
																	Просим Вас подписать Договор № ".$nDogovor."
																	<br>Скан-копию подписанного Договора, просим направить на почту 
																	<a href='mailto:lab@2170812.ru' target='_blank'>lab@2170812.ru</a>
																</span>
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
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				При возникновении вопросов телефон для связи +7 (351) 217-08-12
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 201, Руководитель ИЦ Габдрахманова Алия Агзамовна
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 210, инженер ИЦ Печерская Маргарита Михайловна
																			</span>
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
		
		$attach = "protocol_generator/archive_dog/".$_GET['ID']."/".$_GET['ATTACH'].".pdf";
		
		//$attach = "protocol_generator/archive_dog/".$_GET['ID']."/".$_GET['ATTACH'].".docx";
		//$subj1= "Договор на проведение испытаний от Испытательного центра ООО УралНИИстром. Заявка ".$_GET['TITLE'];
		$subj1= "Заявка ".$nRequestDogovor." от ".$dRequestDogovor." - Договор";
		$request1 = "UPDATE `DOGOVOR` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
        break;
	case '4':
        /*$msg1 = "<html><body>
                <h1>Здравствуйте!</h1>
                <p>Направляем Вам счет за проведение испытаний по Вашей заявке.</p>
                </body></html>";*/
				
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
													<table cellpadding='15'>
														<tr>
															<td align='center'>
																<span style='font-size:30px;font-weight:700;'>Заявка ".$nRequestDogovor." от ".$dRequestDogovor."</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='0' cellspacing='0'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;'>Цифровая лаборатория U-LAB «УралНИИстром» сформировала:</span>
															</td>
														</tr>
														<tr>
															<td align='left' style='text-indent: 45px;'>
																<span style='font-size:18px;'>
																	- Счет на оплату № ".$nAcc."/".$yAcc." от ".$dAcc."
																</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='20'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;font-weight:700;'>
																	Просим Вас произвести оплату Счета № ".$nAcc."/".$yAcc." 
																	<br>Скан-копию оплаченного Счета, просим направить на почту 
																	<a href='mailto:lab@2170812.ru' target='_blank'>lab@2170812.ru</a>
																</span>
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
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				При возникновении вопросов телефон для связи +7 (351) 217-08-12
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 201, Руководитель ИЦ Габдрахманова Алия Агзамовна
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 210, инженер ИЦ Печерская Маргарита Михайловна
																			</span>
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
		
		$attach = "protocol_generator/archive_acc/".$_GET['ID']."/".$_GET['ATTACH'].".pdf";
		//$subj1= "Счет за проведение испытаний от Испытательного центра ООО УралНИИстром. Заявка ".$_GET['TITLE'];
		$subj1= "Заявка ".$nRequest." от ".$dRequest." - Счет";
		$request1 = "UPDATE `INVOICE` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
        break;
	case '5':
        $msg1 = "<html><body>
                <h1>Здравствуйте!</h1>
                <p>Направляем Вам протокол испытаний.</p>
                </body></html>";
		
		$attach = "protocol_generator/archive/".$_GET['ID']."/".$_GET['ATTACH'].".docx";
		$subj1= "Протокол испытаний от Испытательного центра ООО УралНИИстром. Заявка ".$_GET['TITLE'];
		$request1 = "UPDATE `ba_tz` SET `PROTOCOL_SEND_DATE`='".date('Y-m-d')."' WHERE `ID`=".$_GET['TZ_ID'];
        break;
	case '6':
        /*$msg1 = "<html><body>
                <h1>Здравствуйте!</h1>
                <p>Направляем Вам Техническое задание.</p>
                </body></html>";*/
				
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
													<table cellpadding='15'>
														<tr>
															<td align='center'>
																<span style='font-size:30px;font-weight:700;'>Заявка ".$nRequestDogovor." от ".$dRequestDogovor."</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='0' cellspacing='0'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;'>Цифровая лаборатория U-LAB «УралНИИстром» сформировала:</span>
															</td>
														</tr>
														<tr>
															<td align='left' style='text-indent: 45px;'>
																<span style='font-size:18px;'>
																	- Техническое задание № ".$nTz."/".$yTz." от ".$dTz."
																</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='20'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;font-weight:700;'>
																	Просим Вас подписать Техническое задание № ".$nTz."/".$yTz."
																	<br>Скан-копию подписанного Технического задания, просим направить на почту 
																	<a href='mailto:lab@2170812.ru' target='_blank'>lab@2170812.ru</a>
																</span>
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
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				При возникновении вопросов телефон для связи +7 (351) 217-08-12
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 201, Руководитель ИЦ Габдрахманова Алия Агзамовна
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 210, инженер ИЦ Печерская Маргарита Михайловна
																			</span>
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
		
		$attach = "protocol_generator/archive_tz/".$_GET['TZ_ID']."/".$_GET['ATTACH'].".pdf";
		//$subj1= "Бланк заявки на проведение испытаний от Испытательного центра ООО УралНИИстром";
		$subj1= "Заявка ".$nRequest." от ".$dRequest." - ТЗ";
		$request1 = "UPDATE `TZ_DOC` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
        break;
	case '7':
        /*$msg1 = "<html><body>
                <h1>Здравствуйте!</h1>
                <p>Направляем Вам бланк заявки на проведение испытаний.</p>
                </body></html>";*/
				
		/*------------------------'Отправка писам Договор, Счет, ТЗ'начало-----------------*/
		/*$msg1 = "<html>
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
													<table cellpadding='15'>
														<tr>
															<td align='center'>
																<span style='font-size:30px;font-weight:700;'>Заявка ".$nRequestDogovor." от ".$dRequestDogovor."</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='0' cellspacing='0'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;'>Цифровая лаборатория U-LAB «УралНИИстром» сформировала:</span>
															</td>
														</tr>
														<tr>
															<td align='left' style='text-indent: 45px;'>
																<span style='font-size:18px;'>
																	- Договор № ".$nDogovor." от ".$dDogovor."
																</span>
															</td>
														</tr>
														<tr>
															<td align='left' style='text-indent: 45px;'>
																<span style='font-size:18px;'>
																	- Счет на оплату № ".$nAcc."/".$yAcc." от ".$dAcc."
																</span>
															</td>
														</tr>
														<tr>
															<td align='left' style='text-indent: 45px;'>
																<span style='font-size:18px;'>
																	- Техническое задание № ".$nTz."/".$yTz." от ".$dTz."
																</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='20'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;font-weight:700;'>Просим Вас подписать 
																	Договор № ".$nDogovor." и произвести оплату 
																	Счета № ".$nAcc."/".$yAcc." 
																	<br>Скан-копию подписанного Договора и ТЗ, просим направить на почту 
																	<a href='mailto:lab@2170812.ru' target='_blank'>lab@2170812.ru</a>
																</span>
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
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				При возникновении вопросов телефон для связи +7 (351) 217-08-12
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 201, Руководитель ИЦ Габдрахманова Алия Агзамовна
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 210, инженер ИЦ Печерская Маргарита Михайловна
																			</span>
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
				</html>";*/

        ob_start();
        include 'general_mail.php';
        $msg1 .= ob_get_clean();

        
        if (!empty($check_dogovor)) {
            $attach[] = "protocol_generator/archive_dog/".$_GET['ID']."/".$_GET['ATTACH1'].".pdf";
            $request1 = "UPDATE `DOGOVOR` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
        }

        if (!empty($check_tz)) {
            $attach[] = "protocol_generator/archive_tz/".$_GET['TZ_ID']."/".$_GET['ATTACH3'].".pdf";
            $request2 = "UPDATE `TZ_DOC` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
        }

        if (!empty($check_account)) {
            $attach[] = "protocol_generator/archive_acc/".$_GET['ID2']."/".$_GET['ATTACH2'].".pdf";
            $request3 = "UPDATE `INVOICE` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
        }

        $strMail = '';
        if (!empty( $_GET['CHECK'])) {
            foreach ($_GET['CHECK'] as $keyCheck => $valCheck) {
                $strMail .= $valCheck;

                if($valCheck != end($_GET['CHECK'])) {
                    $strMail .= ', ';
                }
            }
        }

        $subj1= "Заявка ".$nRequestDogovor." от ".$dRequestDogovor." - " . $strMail;

		/*$attach[] = "protocol_generator/archive_dog/".$_GET['ID']."/".$_GET['ATTACH1'].".pdf";
		$attach[] = "protocol_generator/archive_tz/".$_GET['TZ_ID']."/".$_GET['ATTACH3'].".pdf";
		$attach[] = "protocol_generator/archive_acc/".$_GET['ID2']."/".$_GET['ATTACH2'].".pdf";*/

		/*$subj1= "Заявка ".$nRequestDogovor." от ".$dRequestDogovor." - Договор, ТЗ, Счет";
		$request1 = "UPDATE `DOGOVOR` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
		$request2 = "UPDATE `TZ_DOC` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
		$request3 = "UPDATE `INVOICE` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];*/

		/*$attach = "ТЗ_УралНИИстром.docx";
		$subj1= "Бланк заявки на проведение испытаний от Испытательного центра ООО УралНИИстром";*/
        break;

		case '8':
        /*$msg1 = "<html><body>
                <h1>Здравствуйте!</h1>
                <p>Направляем Вам Техническое задание.</p>
                </body></html>";*/
				
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
													<table cellpadding='15'>
														<tr>
															<td align='center'>
																<span style='font-size:30px;font-weight:700;'>Заявка ".$nRequestDogovor." от ".$dRequestDogovor."</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='0' cellspacing='0'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;'>Цифровая лаборатория U-LAB «УралНИИстром» сформировала:</span>
															</td>
														</tr>
														<tr>
															<td align='left' style='text-indent: 45px;'>
																<span style='font-size:18px;'>
																	- Акт выполненных работ № ".$resAktVR['NUMBER']." от ".date('d.m.Y', strtotime($resAktVR['DATE']))."
																</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='20'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;font-weight:700;'>
																	Просим Вас подписать Акт выполненных работ № ".$resAktVR['NUMBER']." от ".date('d.m.Y', strtotime($resAktVR['DATE']))."
																	<br>Скан-копию подписанного Акта просим направить на почту 
																	<a href='mailto:lab@2170812.ru' target='_blank'>lab@2170812.ru</a>
																</span>
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
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				При возникновении вопросов телефон для связи +7 (351) 217-08-12
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 201, Руководитель ИЦ Габдрахманова Алия Агзамовна
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 210, инженер ИЦ Печерская Маргарита Михайловна
																			</span>
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
		
		$attach = "protocol_generator/archive_aktvr/".$_GET['ID']."/".$_GET['ATTACH'].".pdf";
		//$subj1= "Бланк заявки на проведение испытаний от Испытательного центра ООО УралНИИстром";
		$subj1= "Заявка ".$nRequest." от ".$dRequest." - Акт";
		$request1 = "UPDATE `AKT_VR` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
		$flAkt=1;
        break;
		/*$attach = "ТЗ_УралНИИстром.docx";
		$subj1= "Бланк заявки на проведение испытаний от Испытательного центра ООО УралНИИстром";
        break;*/
   
   //-------------'Формирование общего счета в pdf и отправка' начало-----------------//
		//Контент(текст письма) общего счета
		case '9': 
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
													<table cellpadding='15'>
														<tr>
															<td align='center'>
																<span style='font-size:30px;font-weight:700;'>" . $number_date_request . "</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='0' cellspacing='0'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;'>Цифровая лаборатория U-LAB «УралНИИстром» сформировала:</span>
															</td>
														</tr>
														<tr>
															<td align='left' style='text-indent: 45px;'>
																<span style='font-size:18px;'>
																	- Счет на оплату № ".$n_acc."/".$y_acc." от ".$d_acc."
																</span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align='center'>
													<table cellpadding='20'>
														<tr>
															<td align='center'>
																<span style='font-size:18px;font-weight:700;'>
																	Просим Вас произвести оплату Счета № ".$n_acc."/".$y_acc." 
																	<br>Скан-копию оплаченного Счета, просим направить на почту 
																	<a href='mailto:lab@2170812.ru' target='_blank'>lab@2170812.ru</a>
																</span>
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
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				При возникновении вопросов телефон для связи +7 (351) 217-08-12
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 201, Руководитель ИЦ Габдрахманова Алия Агзамовна
																			</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>
																				доб. 210, инженер ИЦ Печерская Маргарита Михайловна
																			</span>
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
		
		$attach = "protocol_generator/archive_acc/".$id_requests."/".$actual_ver_account.".pdf";
		//$subj1= "Заявка ".$nRequest." от ".$dRequest." - Счет";
		$subj1= $number_date_request_subj . " - Счет";
		$id_contract = !empty($_GET['ID_CONTRACT']) ? $_GET['ID_CONTRACT'] : '';
		//$request1 = "UPDATE `INVOICE` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `CONTRACT_ID`=" . $id_contract;
        if ($res_select_invoice['ID']) {
			$request1 = "UPDATE `INVOICE` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `ID`=" . (int)$res_select_invoice['ID'];
		}
		break;
}

$addr1 = $_GET['EMAIL'];
$name = $_GET['NAME'];
$client_name = "";
//echo'<pre>';var_dump($addr1, $name, $client_name, $msg1, $attach, $subj1);echo'</pre>';die();
if($_GET['EMAIL']) {
	mail_to($addr1, $name, $client_name, $msg1, $attach, $subj1);

	if ($request1) $DB->Query($request1);
	
	if($flAkt)
	{
		if (CModule::IncludeModule('crm')) {
		$deal = new CCrmDeal;
		$arDealUpdate = array('STAGE_ID' => '4');
		$resUpd = $deal->Update($_GET['ID'],
			$arDealUpdate,
			true,
			true,
			array('DISABLE_USER_FIELD_CHECK' => true)
		);
	}
	}

	if ($request2) $DB->Query($request2);
	
	if ($request3) $DB->Query($request3);
}
else
echo "В карточке компании не указан электронный адрес! Письмо не отправлено.";

 ?>