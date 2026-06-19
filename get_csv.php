<? 
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$res = $DB->Query("SELECT * FROM `ba_tz` ORDER BY `ID`");
while($tz = $res->Fetch())
{
	$arr = explode("/",$tz['NUM_P_TABLE']);
	if(!$tz['NUM_P'])
		//$DB->Query("UPDATE `ba_tz` SET `NUM_P_TABLE` = '' WHERE `ID`=".$tz['ID']);
	// if($arr[0]&&(!$arr[1]||$arr[1]=='70')&&$arr[0]!='номер не присвоен'&&$arr[0]!='545111'))
	// {
		// $date_protocol = $tz['DATE_P_CORR'] ? $tz['DATE_P_CORR'] : $tz['DATE_P'];
		// if(!$tz['DATE_P']) 
		// $y_date_p = date("Y");
		// else
		// $y_date_p = date("Y", strtotime($date_protocol));
		// if ($y_date_p != '2020') {
			// $y_date_p = "/" . substr($y_date_p, 2);
		// } else {
			// $y_date_p = "/".$y_date_p;
		// }
		
		// echo $arr[0].$y_date_p."<br>";
		
		// $DB->Query("UPDATE `ba_tz` SET `NUM_P_TABLE` = '". $arr[0].$y_date_p ."' WHERE `ID`=".$tz['ID']);
	// }
}


die; ?>