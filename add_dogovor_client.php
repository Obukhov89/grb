<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/function.php';

if (!empty($_POST['id_client'])) {
	
	$data_dogovor_client = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `CLIENT_ID` = " . $_POST['id_client']);
	
	$arr_dogovor = [];
	
	$i = 0;
	
	while($dogovor_client = $data_dogovor_client->Fetch()) {

		$arr_dogovor[$i]['id_dogovor'] = !empty($dogovor_client['ID']) ? $dogovor_client['ID'] : '';
		$arr_dogovor[$i]['type_dogovor'] = !empty($dogovor_client['CONTRACT_TYPE']) ? $dogovor_client['CONTRACT_TYPE'] : 'Договор';
		$arr_dogovor[$i]['number_dogovor'] = !empty($dogovor_client['NUMBER']) ? $dogovor_client['NUMBER'] : '';
		$arr_dogovor[$i]['date_dogovor'] = !empty($dogovor_client['DATE']) ? ruDate($dogovor_client['DATE']) : '';
		
		$i++;
	}
	
	echo json_encode($arr_dogovor);
}
exit
?>