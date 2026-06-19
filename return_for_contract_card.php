<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php'; 
	/*-----------------'Договор Клиента' начало--------------*/
	//$idDogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `CLIENT_NUMBER`!='1' ORDER BY `ID` DESC LIMIT 1")->Fetch();
	$idDogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `CLIENT_NUMBER`='0' OR `CLIENT_NUMBER` IS NULL ORDER BY `ID` DESC LIMIT 1")->Fetch();
	/*-----------------'Договор Клиента' окончание--------------*/
	
	$dogovorNumber = !empty($idDogovor['NUMBER']) ? $idDogovor['NUMBER'] : '';
	//echo'<pre>';var_dump($idDogovor);
	echo $dogovorNumber;
?>	
<? exit ?>