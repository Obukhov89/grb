<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require($_SERVER["DOCUMENT_ROOT"] . "/PHPMailer/PHPMailer.php");
require($_SERVER["DOCUMENT_ROOT"] . "/PHPMailer/SMTP.php");
require($_SERVER["DOCUMENT_ROOT"] . "/PHPMailer/Exception.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DataStorage.php');

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$newResult = new NewResult();

$user_id = !empty($USER->GetID()) ? (int)$USER->GetID() : '';

//if($user_id == 61) {
//    $_GET['EMAIL'] = 'darkshine445@gmail.com';
//}

function mail_to ($addr, $name, $client_name, $msg, $attachment, $subj, $newAttachName)
{
    if ($_GET['TYPE'] == 8) {
        $username = 'buh@2170812.ru';
        $passowrd = 'pqlidazmqzqcumyo';
    }else {
        $username = 'lab@2170812.ru';
        $passowrd = 'mxfzdbscinknqwgn';
    }


    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Host   = 'ssl://smtp.yandex.ru';
    $mail->Username   = $username;
    $mail->Password   = $passowrd;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Port   = 465;

    $mail->setFrom($username, $name);



    if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
        //Для аккаунта тех. поддержки перехватываем письмо ВСЕ письма отправляем только на тестовый адрес
        $testEmail = 'darkshine445@gmail.com';
        $mail->addAddress($testEmail, $client_name);
//        if ( !is_array($addr) ) {
//            $emails = explode(',', $addr);
//        } else {
//            $emails = $addr;
//        }
//
//        foreach ($emails as $email) {
//            $mail->addAddress($email, $client_name);
//        }
    } else {
        if ( !is_array($addr) ) {
            $emails = explode(',', $addr);
        } else {
            $emails = $addr;
        }

        foreach ($emails as $email) {
            $mail->addAddress($email, $client_name);
        }
    }

    if (is_array($attachment)) {
        foreach ($attachment as $valAttach) {
            if ($newAttachName !== null) {
                $mail->AddAttachment($valAttach, $newAttachName);
            } else {
                $mail->AddAttachment($valAttach);
            }
        }
    } else {
        if ($newAttachName !== null) {
            $mail->AddAttachment($attachment, $newAttachName);
        } else {
            $mail->AddAttachment($attachment);
        }
    }

    $mail->Subject = $subj;

    $mail->AddEmbeddedImage('./DataTables/DataTables-1.10.18/images/logo.png', 'logo_company');//прикрепляем картинку для отображения в теле письма

    $mail->msgHTML($msg);

//    echo "<pre>";
//    print_r($mail);
//    echo "</pre>";
//    die();

    if ($mail->send()) {
        echo 'Письмо отправлено на '.$addr.'! ';
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

$idDogovor = (!empty($resDogovor["ID"]) ? $resDogovor["ID"] : '');
$nDogovor = (!empty($resDogovor["NUMBER"]) ? $resDogovor["NUMBER"] : '');
if(!empty($resDogovor["number_client"])) {
    $nDogovor = $resDogovor["number_client"];
}
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

$nAcc1 = (!empty($resAcc["ID"]) ? $resAcc["ID"] : '');
$nAcc = (!empty($resAcc["NUMBER"]) ? $resAcc["NUMBER"] : '');
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

// Получаем сопроводительный текст и формируем шаблон блока для вставки в письмо
$additionalText = isset($_GET['BODY']) ? trim($_GET['BODY']) : '';

$allowedTags = '<b><i><u><strong><em><ul><ol><li><a><br><p><span>';
$additionalText = strip_tags($additionalText, $allowedTags);

$additionalTextBlock = '';

if (!empty($additionalText)) {
    $additionalTextBlock = "
        <tr>
            <td style='border-bottom: 1px solid black; padding-bottom: 4px;'>
                <span style='font-size: 15px; letter-spacing: 0.2px;'>$additionalText</span>
            </td>
        </tr>
        <tr>
            <td height='8'></td>
        </tr>
    ";
}

$msg1 = '';
//
//echo "<pre>";
//print_r($_GET);
//echo "</pre>";
//die();

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
																    $additionalTextBlock
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 303, 305, 307 Отдел по работе с клиентами</span>
																		</td>																		
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201 Руководитель ИЦ Габдрахманова Алия Агзамовна </span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>Моб. телефон: 8(900)064-48-11 Telegram, MAX</span>
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

        break;
    case '2':


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
																<span style='font-size:18px;'>Цифровая лаборатория U-LAB «УралНИИстром» сформировала:<br>- Коммерческое предложение (КП) № ".$idKp."/".$yKp." от ".$dKp."</span>
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
															    $additionalTextBlock
																<table cellspacing='0' cellpadding='0' border='0'>
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 303, 305, 307 Отдел по работе с клиентами</span>
																		</td>
																		
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201 Руководитель ИЦ Габдрахманова Алия Агзамовна </span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>Моб. телефон: 8(900)064-48-11 Telegram, MAX</span>
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

        $attach[] = $_SERVER["DOCUMENT_ROOT"] . "/protocol_generator/archive_kp/".$_GET['ID']."/".$_GET['ATTACH'].".pdf";
        $attach[] = "/home/bitrix/www/Предложение ФОТОФИКСАЦИЯ.jpg";


        $subj1= "Заявка ".$nRequest." от ".$dRequest." - КП";
        $request1 = "UPDATE `KP` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
        break;
    case '3':


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
																<br>
																<span style='font-size:18px;font-weight:700;'>
																	ОБРАТИТЕ ВНИМАНИЕ! Оригиналы подписанного заказчиком договора следует предоставить при первичной передаче проб, либо направить на почтовый адрес: 454047, г.Челябинск а/я 5177
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
															    $additionalTextBlock
																<table cellspacing='0' cellpadding='0' border='0'>
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 303, 305, 307 Отдел по работе с клиентами</span>
																		</td>																		
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201 Руководитель ИЦ Габдрахманова Алия Агзамовна </span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>Моб. телефон: 8(900)064-48-11 Telegram, MAX</span>
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



        $subj1= "Заявка ".$nRequestDogovor." от ".$dRequestDogovor." - Договор";
        $request1 = "UPDATE `DOGOVOR` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID']." OR ID = ".$idDogovor;
        break;
    case '4':


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
															    $additionalTextBlock
																<table cellspacing='0' cellpadding='0' border='0'>
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 303, 305, 307 Отдел по работе с клиентами</span>
																		</td>																		
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201 Руководитель ИЦ Габдрахманова Алия Агзамовна </span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>Моб. телефон: 8(900)064-48-11 Telegram, MAX</span>
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

        $subj1= "Заявка ".$nRequest." от ".$dRequest." - Счет";
        $request1 = "UPDATE `INVOICE` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
        break;
    case '5':

        $fileSig = !empty($_GET['SIG']) ? $_GET['SIG'] : '';
        $filePdf = !empty($_GET['PDF']) ? $_GET['PDF'] : '';
        $fileNamePdf = !empty($filePdf) ? str_replace('.pdf', '', $filePdf) : '';
        $fileNamePdf = preg_replace('/^.*?(П)/', '$1', $fileNamePdf);
        $yearProtocol = !empty($_GET['YEAR']) ? $_GET['YEAR'] : '';

        $idP = !empty($_GET['ID_P']) ? $_GET['ID_P'] : '';


        $tzId = !empty($_GET['TZ_ID']) ? $_GET['TZ_ID'] : '';

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
																	- ".$fileNamePdf."
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
																    $additionalTextBlock
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 303, 305, 307 Отдел по работе с клиентами</span>
																		</td>																		
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201 Руководитель ИЦ Габдрахманова Алия Агзамовна </span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>Моб. телефон: 8(900)064-48-11 Telegram, MAX</span>
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



        //if (!empty($resTz['ID_Z']) && in_array((int)$resTz['ID_Z'], $newResult->arrIdDeal) || !empty($resTz['ID_Z']) && (int)$resTz['ID_Z'] === 5138 || !empty($resTz['ID_Z']) && (int)$resTz['ID_Z'] === 5479 || !empty($resTz['ID_Z']) && (int)$resTz['ID_Z'] === 5697 || !empty($resTz['ID_Z']) && (int)$resTz['ID_Z'] === 5893) {
        if (!empty($resTz['ID_Z']) && (in_array((int)$resTz['ID_Z'], $newResult->arrIdDeal) || (int)$resTz['ID_Z'] > $newResult->idDealВefore)) {
//			if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
//				echo '<pre>';
//				var_dump($_GET);
//				exit();
//			}
            if ($_GET['OL'] == 1) {
                $attach[] = $_SERVER['DOCUMENT_ROOT'] . $filePdf;
                $attach[] = $_SERVER['DOCUMENT_ROOT'] . $filePdf . '.sig';
            } else {
                $attach[] = "/home/bitrix/www/protocol_generator/archive/" . $tzId . $yearProtocol . "/" . $idP . "/" . $filePdf;
                $attach[] = "/home/bitrix/www/protocol_generator/archive/" . $tzId . $yearProtocol . "/" . $idP . "/" . $filePdf  . '.sig';
            }

            $subj1= "Протокол испытаний от Испытательного центра ООО УралНИИстром. Заявка ".$_GET['TITLE'];

            $request1 = "UPDATE `PROTOCOLS` SET `PROTOCOL_SEND_DATETIME`='".date("Y-m-d H:i:s")."' WHERE `ID`=" . $idP;
        } else {
            $attach[] = "/home/bitrix/www/protocol_generator/archive/" . $tzId . $yearProtocol . "/" . $filePdf;
//            $attach[] = "/home/bitrix/www/protocol_generator/archive/" . $tzId  . $yearProtocol . "/" . $fileSig;

            $subj1= "Протокол испытаний от Испытательного центра ООО УралНИИстром. Заявка ".$_GET['TITLE'];
            $request1 = "UPDATE `ba_tz` SET `PROTOCOL_SEND_DATE`='".date('Y-m-d')."' WHERE `ID`=" . $tzId;
        }

        break;

    case '6':


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
																    $additionalTextBlock
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 303, 305, 307 Отдел по работе с клиентами</span>
																		</td>																		
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201 Руководитель ИЦ Габдрахманова Алия Агзамовна </span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>Моб. телефон: 8(900)064-48-11 Telegram, MAX</span>
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

        $subj1= "Заявка ".$nRequest." от ".$dRequest." - ТЗ";
        $request1 = "UPDATE `TZ_DOC` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];
        break;
    case '7':

        ob_start();
        include 'general_mail.php';
        $msg1 .= ob_get_clean();


        if (!empty($check_dogovor)) {
            $attach[] = "protocol_generator/archive_dog/".$_GET['ID']."/".$_GET['ATTACH1'].".pdf";
            $request1 = "UPDATE `DOGOVOR` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID']." OR ID = ".$idDogovor;
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

        break;

    case '8':

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
																	- Универсальный передаточный документ № ".$resAktVR['NUMBER']." от ".date('d.m.Y', strtotime($resAktVR['DATE']))."
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
																	Просим Вас подписать Универсальный передаточный документ № ".$resAktVR['NUMBER']." от ".date('d.m.Y', strtotime($resAktVR['DATE']))."
																	<br>Скан-копию подписанного УПД просим направить на почту
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
																    $additionalTextBlock
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 303, 305, 307 Отдел по работе с клиентами</span>
																		</td>																		
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201 Руководитель ИЦ Габдрахманова Алия Агзамовна </span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>Моб. телефон: 8(900)064-48-11 Telegram, MAX</span>
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

        $subj1= "Заявка ".$nRequest." от ".$dRequest." - УПД";
        $request1 = "UPDATE `AKT_VR` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID'];

        $statusData =
            [
                'user_id' => $_SESSION['SESS_AUTH']['USER_ID'],
                'date' => date('Y-m-d H:i:s')
            ];

        $DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$_GET['ID']}', '{$statusData['user_id']}', 6, '{$statusData['date']}')");

        $flAkt=1;
        break;

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
																    $additionalTextBlock
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 303, 305, 307 Отдел по работе с клиентами</span>
																		</td>																		
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201 Руководитель ИЦ Габдрахманова Алия Агзамовна </span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>Моб. телефон: 8(900)064-48-11 Telegram, MAX</span>
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

        $subj1= $number_date_request_subj . " - Счет";
        $id_contract = !empty($_GET['ID_CONTRACT']) ? $_GET['ID_CONTRACT'] : '';

        if ($res_select_invoice['ID']) {
            $request1 = "UPDATE `INVOICE` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `ID`=" . (int)$res_select_invoice['ID'];
        }
        break;

    case '10':
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
																<span style='font-size:18px;'>Цифровая лаборатория U-LAB «УралНИИстром» <br>сформировала Прайс</span>
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
																<span style='font-size:18px;font-weight:700;'>Просим Вас согласовать Прайс <br>и сообщить о результатах согласования на почту <a href='mailto:lab@2170812.ru' target='_blank'>lab@2170812.ru</a></span>
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
																    $additionalTextBlock
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 303, 305, 307 Отдел по работе с клиентами</span>
																		</td>																		
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201 Руководитель ИЦ Габдрахманова Алия Агзамовна </span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>Моб. телефон: 8(900)064-48-11 Telegram, MAX</span>
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


        $attach = !empty($_GET['ID_PRICE']) ? 'protocol_generator/archive_price/' . $_GET['ID_PRICE'] . '.pdf' : '';

        $subj1= "Прайс на проведение испытаний от Испытательного центра ООО УралНИИстром.";

        $request1 = !empty($_GET['ID_PRICE']) ? "UPDATE `SEND_PRICE` SET `SEND_DATE`='".date('Y-m-d H:i:s')."' WHERE `ID`=" . (int)$_GET['ID_PRICE'] : '';
        break;
    case '11':


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
																	- Счет-оферту № ".$nDogovor." от ".$dDogovor."
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
																	Просим Вас оплатить счет-оферту № ".$nDogovor."
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
															    $additionalTextBlock
																<table cellspacing='0' cellpadding='0' border='0'>
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 303, 305, 307 Отдел по работе с клиентами</span>
																		</td>																		
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201 Руководитель ИЦ Габдрахманова Алия Агзамовна </span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>Моб. телефон: 8(900)064-48-11 Telegram, MAX</span>
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

        $basename = basename($attach);

        $parts = explode("/", $nDogovor);
        $number = $parts[0];
        if (preg_match('/SO_([0-9]+)_/', $basename, $matches)) {
            $newAttachName = str_replace("SO_", "СО № ", $basename);
            $newAttachName = str_replace($matches[1], $number, $newAttachName);
        }

        $subj1= "Заявка ".$nRequestDogovor." от ".$dRequestDogovor." - Счет-оферта";
        $request1 = "UPDATE `DOGOVOR` SET `SEND_DATE`='".date('Y-m-d')."' WHERE `TZ_ID`=".$_GET['TZ_ID']." OR ID = ".$idDogovor;
        break;
    case '12':
        $fileNamePdf2 = [];
        foreach ($_GET['PROTOCOLS'] as $value) {
            $protocolDataArray = explode("|N", $value);

            $_GET['PDF'] = $protocolDataArray[0] == '--' ? '' : $protocolDataArray[0];
            $_GET['SIG'] = $protocolDataArray[1] == '--' ? '' : $protocolDataArray[1];
            $_GET['OL'] = $protocolDataArray[2] == '--' ? '' : $protocolDataArray[2];
            $_GET['YEAR'] = $protocolDataArray[3] == '--' ? '' : $protocolDataArray[3];
            $_GET['ID_P'] = $protocolDataArray[4] == '--' ? '' : $protocolDataArray[4];

            $fileSig = !empty($_GET['SIG']) ? $_GET['SIG'] : '';
            $filePdf = !empty($_GET['PDF']) ? $_GET['PDF'] : '';
            $fileNamePdf = !empty($filePdf) ? str_replace('.pdf', '', $filePdf) : '';
            $fileNamePdf2[] = !empty($filePdf) ? str_replace('.pdf', '', $filePdf) : '';
            $yearProtocol = !empty($_GET['YEAR']) ? $_GET['YEAR'] : '';

            $idP = !empty($_GET['ID_P']) ? $_GET['ID_P'] : '';


            $tzId = !empty($_GET['TZ_ID']) ? $_GET['TZ_ID'] : '';

            if (!empty($resTz['ID_Z']) && (in_array((int)$resTz['ID_Z'], $newResult->arrIdDeal) || (int)$resTz['ID_Z'] > $newResult->idDealВefore)) {
                if ($_GET['OL'] == 1) {
                    $attach[] = $_SERVER['DOCUMENT_ROOT'] . $filePdf;
                    $attach[] = $_SERVER['DOCUMENT_ROOT'] . $filePdf . '.sig';
                } else {
                    $attach[] = "/home/bitrix/www/protocol_generator/archive/" . $tzId . $yearProtocol . "/" . $idP . "/" . $filePdf;
                    $attach[] = "/home/bitrix/www/protocol_generator/archive/" . $tzId . $yearProtocol . "/" . $idP . "/" . $filePdf  . '.sig';
                }

                $subj1= "Протокол испытаний от Испытательного центра ООО УралНИИстром. Заявка ".$_GET['TITLE'];

                $DB->Query("UPDATE `PROTOCOLS` SET `PROTOCOL_SEND_DATETIME`='".date("Y-m-d H:i:s")."' WHERE `ID`=" . $_GET['ID_P']);
            } else {
                $attach[] = "/home/bitrix/www/protocol_generator/archive/" . $tzId . $yearProtocol . "/" . $filePdf;

                $subj1= "Протокол испытаний от Испытательного центра ООО УралНИИстром. Заявка ".$_GET['TITLE'];
                $request1 = "UPDATE `ba_tz` SET `PROTOCOL_SEND_DATE`='".date('Y-m-d')."' WHERE `ID`=" . $tzId;
            }
        }
        $fileNamePdf2 = implode("<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;", $fileNamePdf2);
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
																	- ".$fileNamePdf2."
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
																    $additionalTextBlock
																	<tr>
																		<td>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>При возникновении вопросов телефон для связи +7 (351) 217-08-12</span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 303, 305, 307 Отдел по работе с клиентами</span>
																		</td>																		
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>доб. 201 Руководитель ИЦ Габдрахманова Алия Агзамовна </span>
																		</td>
																	</tr>
																	<tr>
																		<td style='text-indent: 45px;'>
																			<span style='font-size: 15px; letter-spacing: 0.2px;'>Моб. телефон: 8(900)064-48-11 Telegram, MAX</span>
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


        break;
}

$addr1 = $_GET['EMAIL'];
$dopMail1 = $_GET['EMAIL1'];
$dopMail2 = $_GET['EMAIL2'];
$dopMail3 = $_GET['EMAIL3'];
$name = $_GET['NAME'];
$client_name = "";

if($_GET['EMAIL']) {
//	if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
//		echo '<pre>';
//		var_dump($attach);
//		var_dump(file_exists($attach));
//		exit();
//	}

//    echo "<pre>";
//    print_r($subj1);
//    echo "</pre>";
//    die();

    mail_to($addr1, $name, $client_name, $msg1, $attach, $subj1, $newAttachName);

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


        if (!empty($_GET['ID'])) {
            $update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('4') . "', `STAGE_NUMBER`= " . (int)4 . " WHERE `ID_Z`=" . (int)$_GET['ID']);
        }

    }

    if ($request2) $DB->Query($request2);

    if ($request3) $DB->Query($request3);
}
else
    echo "В карточке компании не указан электронный адрес! Письмо не отправлено.";

if($_GET['EMAIL1']) {
    mail_to($dopMail1, $name, $client_name, $msg1, $attach, $subj1);
};

if($_GET['EMAIL2']) {
    mail_to($dopMail2, $name, $client_name, $msg1, $attach, $subj1);
};

if($_GET['EMAIL3']) {
    mail_to($dopMail3, $name, $client_name, $msg1, $attach, $subj1);
}
if (is_array($addr1)) {
    $sendMails = implode(', ', $addr1);
} else {
    $sendMails = $addr1;
}
$_SESSION['message_success'] = 'Письмо отправлено ' . $sendMails . '!';
Header('Location: /ulab/request/card/' . $resTz['ID_Z']);
