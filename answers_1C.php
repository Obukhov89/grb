<?
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";


 $link = mysqli_connect("localhost", "baseuser", "nageibei7uToonee71", "nii_base")
 or die("Ошибка " . mysqli_error($link));

$log = date("Y.m.d G:i:s") . "\n";
$log = print_r("GET") . "\n";
$log .= print_r($_GET, 1). "\n";
file_put_contents(getcwd() . '/LOG1С.txt', $log, FILE_APPEND);

 if(!empty($_GET['number']) && !empty($_GET['date'])){
	// получаем все (id, id_tz) из таблички INVOICE по номеру счета

	// select *, DATE_FORMAT(DATE, '%d.%m.%Y') as date_act from INVOICE where DATE_FORMAT(DATE, '%d.%m.%Y')='25.05.2021' AND TZ_ID = 4799;
	// условие если дата счета и номер счета в таблице INVOICE нашла запись, то берём из нее TZ_ID и по ней делаем запрос ниже
	$querry_invoice = "select * FROM INVOICE WHERE DATE_FORMAT(DATE, '%d.%m.%Y') = '".$_GET['date']."' AND NUMBER = " . $_GET['number'];

	$res_select_querry_invoice = mysqli_query($link, $querry_invoice);

	$row_querry_invoice = $res_select_querry_invoice->fetch_array(MYSQLI_ASSOC);

	$id_tz_from_invoice = $row_querry_invoice['TZ_ID'];

	$id_from_invoice = $row_querry_invoice['ID'];
}


if (!empty($_GET['result']) && $_GET['result'] !== 'OK') {

	//$fl = !empty($_GET['fl']) ? (int)$_GET['fl'] : '';

//	$number_arr = !empty($_GET['result']) ? explode("-", $_GET['result']) : '';
	$number_arr = !empty($_GET['result']) ? $_GET['result'] : '';
//$number_akt = !empty($_GET['result']) ? $_GET['result'] : '';
//	$number_akt = !empty($number_arr[1]) ? intval($number_arr[1]) : '';
	$number_akt = !empty($number_arr) ? $number_arr : '';
	//$date_akt = !empty($_GET['date']) ? $_GET['date'] : '';
	$number_invoice = !empty($_GET['number']) ? (int)$_GET['number']+2450 : '';
	//$inn = !empty($_GET['inn']) ? (int)$_GET['inn'] : '';
	$paydocs = !empty($_GET['paydocs']) ? $_GET['paydocs'] : '';
	$guid = !empty($_GET['GUID']) ? $_GET['GUID'] : '';
	//echo'<pre>';var_dump($number_arr,$number_akt,$number_invoice);die;

	/*$y_date_act = '';
	if (!empty($date_akt)) {
		$y_date_act = date("Y", strtotime($date_akt));
		if ($y_date_act != '2020') {
			$y_date_act = "/" . substr($y_date_act, 2);
		} else {
			$y_date_act = "/" . $y_date_act;
		}
	}*/


	//$num_act = $number_akt . $y_date_act;


	if(!empty($number_akt) && !empty($number_invoice))
	{
		// выполняем операции с базой данных
		// $query_acc = "SELECT * FROM `INVOICE` WHERE `ID`=" . $number_invoice;
		$query_acc = "SELECT * FROM `INVOICE` WHERE `ID`=" . $id_from_invoice;

		$res_acc = mysqli_query($link, $query_acc) or die("Ошибка " . mysqli_error($link));
		//$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `ID`=" . $number_invoice)->Fetch();
		$row_acc = $res_acc->fetch_array(MYSQLI_ASSOC);
		$tz_id = !empty($row_acc['TZ_ID']) ? (int)$row_acc['TZ_ID'] : '';

		$akt_date = date("Y-m-d"); //Дата акта
		$dateAcc = date("d-m-Y_H-i-s");
		$curdate = "Акт_". $number_akt .'_'.$dateAcc;

		// выполняем операции с базой данных
		//$query_update_acc = "UPDATE `ba_tz` SET `ACT_NUM`='" . $number_akt . "', `DATE_ACT`='" . $date_akt . "', `NUM_ACT_TABLE`='" . $num_act . "' WHERE `ID`=". $tz_id;
		// $query_update_acc = "UPDATE `AKT_VR` SET `ACTUAL_VER` ='".$curdate."', `NUMBER`='".$number_akt."', `DATE`='".$akt_date."' WHERE `TZ_ID`=" . $tz_id;
		$query_update_acc = "UPDATE `AKT_VR` SET `ACTUAL_VER` ='".$curdate."', `NUMBER`='".$number_akt."', 
		`DATE`='".$akt_date."', `paydocs` ='".$paydocs."', `guid` ='".$guid."' WHERE `TZ_ID`=" . $id_tz_from_invoice; // новая версия правки vsatonin

		$res_update_acc = mysqli_query($link, $query_update_acc) or die("Ошибка " . mysqli_error($link));
		//$resUp = $DB->Query("UPDATE `ba_tz` SET `ACT_NUM`='" . $number_akt . "', `DATE_ACT`='" . $date_akt . "', `NUM_ACT_TABLE`='" . $num_act . "' WHERE `ID`=". $tz_id);

	}
}
elseif($_GET['result'] === 'OK')
{

	$number_invoice = !empty($_GET['number']) ? (int)$_GET['number']+2450 : ''; 

	if (!empty($number_invoice)) {
		// $query_update_acc = "UPDATE INVOICE SET `ANSWER_1C` = " . (int)1 . " WHERE `ID` = " . $number_invoice;
		$query_update_acc = "UPDATE INVOICE SET `ANSWER_1C` = " . (int)1 . " WHERE `ID` = " . $id_from_invoice;

		$res_update_acc = mysqli_query($link, $query_update_acc) or die("Ошибка " . mysqli_error($link));
	}


}

$log = date("Y.m.d G:i:s") . "\n";
$log .= print_r($_GET, 1). "\n";
file_put_contents(getcwd() . '/LOG1С.txt', $log, FILE_APPEND);

die; ?>
