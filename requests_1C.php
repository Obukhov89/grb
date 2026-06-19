<? 
 //require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
 
 // подключаемся к серверу
 $link = mysqli_connect("localhost", "baseuser", "nageibei7uToonee71", "nii_base") 
 or die("Ошибка " . mysqli_error($link));
 
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
global $DB;

// выполняем операции с базой данных
$query = "SELECT * FROM `REQUESTS_FOR_1C` WHERE ID=1";
$resReq = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link)); 

if($resReq)
{	
	$row = $resReq->fetch_array(MYSQLI_ASSOC);
	$arrReq = unserialize($row['REQ']);
	
	$query_null = "UPDATE `REQUESTS_FOR_1C` SET `REQ`='' WHERE ID=1";
	$resReq = mysqli_query($link, $query_null) or die("Ошибка " . mysqli_error($link)); 
	
	echo json_encode($arrReq);
	
}

	//$resReq = $DB->Query("SELECT * FROM `REQUESTS_FOR_1C` WHERE ID=1")->Fetch();
	//$arrReq = unserialize($resReq['REQ']);
	
	// $arrReq = array(
  // "requests" => [
    // [
      // "fl" => 0,
      // "number" => 174,
      // "date" => "13.05.2020",
      // "inn" => "7447171717",
      // "summ" => 3456
    // ],
    // [
      // "fl" => 1,
      // "number" => 174,
      // "date" => "13.05.2020",
      // "inn" => "7447171717",
      // "p_number" => "232 от 24.05.2020"
    // ]
  // ]);
  
  //echo json_encode($arrReq);
die; ?>