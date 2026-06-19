<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->RestartBuffer();

if (!empty($_POST)) {
	$id = $_POST['dealId'];
	$id_protocol = $_POST['current_protocol_id'];
	$id_oborud = $_POST['idOb'];
	$res =$DB->Query("SELECT tob.`ID_GOST`, bg.`GOST`, bg.`GOST_PUNKT` FROM `TZ_OB_CONNECT` as tob, `ba_gost` as bg WHERE tob.`ID_TZ` = {$id} AND tob.`PROTOCOL_ID` = {$id_protocol} AND tob.`ID_OB` = {$id_oborud} AND bg.`ID` = tob.`ID_GOST`");
	while ($row = $res->Fetch()) {
				$gostArr[] = ['ID' => $row['ID_GOST'],
					'GOST' => $row['GOST'] . ' ' . $row['GOST_PUNKT']];
			}
}

echo json_encode($gostArr, JSON_UNESCAPED_UNICODE);
