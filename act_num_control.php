<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

    include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
	
	$res = $DB->Query("SELECT `REQUEST_TITLE`,`ACT_NUM`, `NUM_ACT_TABLE` FROM `ba_tz`");
	while($row = $res->Fetch())
	{
		echo $row['REQUEST_TITLE']." -- ".$row['ACT_NUM']." -- ".$row['NUM_ACT_TABLE']."<br>";
	}
	
?>