<html>
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
                                        <span style='font-size:30px;font-weight:700;'>Заявка <?= $nRequestDogovor ?> от <?= $dRequestDogovor ?></span>
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
                                <?php if (!empty($check_dogovor)) : ?>
                                    <tr style='" . $check_dogovor . "'>
                                        <td align='left' style='text-indent: 45px;'>
                                        <span style='font-size:18px;'>
                                            - Договор № <?= $nDogovor ?> от <?= $dDogovor ?>
                                        </span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($check_account)) : ?>
                                    <tr style='" . $check_tz . "'>
                                        <td align='left' style='text-indent: 45px;'>
                                        <span style='font-size:18px;'>
                                            - Счет на оплату № <?= $nAcc ?>/<?= $yAcc ?> от <?= $dAcc ?>
                                        </span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($check_tz)) : ?>
                                    <tr style='" . $check_account . "'>
                                        <td align='left' style='text-indent: 45px;'>
                                        <span style='font-size:18px;'>
                                            - Техническое задание № <?= $nTz ?>/<?= $yTz ?> от <?= $dTz ?>
                                        </span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align='center'>
                            <table cellpadding='20'>
                                <tr>
                                    <td align='center'>
                                        <span style='font-size:18px;font-weight:700;'>
                                            <?php if (!empty($check_dogovor) || !empty($check_account) || !empty($check_tz)) : ?>
                                                Просим Вас
                                                <?php if (!empty($check_tz) && empty($check_dogovor)) : ?>подписать ТЗ № <?= $nTz ?>/<?= $yTz ?><?php endif; ?>
                                                <?php if (!empty($check_dogovor)) : ?>подписать Договор № <?= $nDogovor ?><?php endif; ?>
                                                <?php if (!empty($check_dogovor) && !empty($check_account) || !empty($check_account) && !empty($check_tz)) : ?>и<?php endif; ?>
                                                <?php if (!empty($check_account)) : ?>произвести оплату Счета № <?= $nAcc ?>/<?= $yAcc ?><?php endif; ?>
                                            <?php endif; ?>
                                            <br>
                                            <?php if (!empty($check_dogovor) || !empty($check_tz)) : ?>
                                                Скан-копию
                                                <?php if (!empty($check_dogovor)) : ?>подписанного Договора<?php if (!empty($check_dogovor) && empty($check_tz)) : ?>,<?php endif; ?><?php endif; ?>
                                                <?php if (!empty($check_dogovor) && !empty($check_tz)) : ?>и<?php endif; ?>
                                                <?php if (!empty($check_tz)) : ?>ТЗ,<?php endif; ?> просим направить на почту
                                                <a href='mailto:lab@2170812.ru' target='_blank'>lab@2170812.ru</a>
                                            <?php endif; ?>
                                        </span>
										<?php if (!empty($check_dogovor)) : ?>
										<br>
										<span style='font-size:18px;font-weight:700;'>
											ОБРАТИТЕ ВНИМАНИЕ! Оригиналы подписанного заказчиком договора следует предоставить при первичной передаче проб, либо направить на почтовый адрес: 454047, г.Челябинск а/я 5177
										</span>
										<?php endif; ?>
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
                                            <?= $additionalTextBlock ?>
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
</html>
