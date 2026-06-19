<?php
include $_SERVER['DOCUMENT_ROOT']."/Classes/CDB.php";
$db_conn = new CDB();

$res_check_tz = $db_conn->select("SELECT ba.`REQUEST_TITLE`, ba.`COMPANY_TITLE`, ba.`ID_Z`, ba.`ACCOUNT`, ch.`tz_id`, ch.`date_reply`, ch.`date_return`, ch.`confirm`FROM 
`CHECK_TZ` ch, `ba_tz` ba WHERE ba.`ACCOUNT` is NULL AND ch.`tz_id` = ba.`ID`");

if (!empty($res_check_tz)) {
	foreach ($res_check_tz as $item) {
		if (empty($item['date_reply'])) {
			if (!empty($item['date_return'])) {
				continue;
			} else {
			$status = 'На рассмотрении руководителя лаборатории';
			$color = '';
			}
		} elseif (!empty($item['date_reply']) && empty($item['date_return'])) {
			if ($item['confirm'] == 0) {
				continue;
			} else {
				$status = 'Заявка принята руководителем. Счет не выставлен.';
				$color = 'style="background: #ff81813b"';
			}
		}
		 elseif (!empty($item['date_reply']) && $item['confirm'] == 0) {
//					$status = 'Заявка возвращена руководителем';
//					$color = '';
 			continue;
		}
		unset($item['date_reply'], $item['date_return']);
		$item += ['STATUS' => $status,
				'color' => $color
			];
		$arrModalCont [] = $item;

	}
	}


echo json_encode($arrModalCont, JSON_UNESCAPED_UNICODE);




