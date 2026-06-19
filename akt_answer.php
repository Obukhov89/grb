<? 
 
 // подключаемся к серверу
 $link = mysqli_connect("localhost", "baseuser", "nageibei7uToonee71", "nii_base") 
 or die("Ошибка " . mysqli_error($link));
 
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
global $DB;

// выполняем операции с базой данных
$query = "SELECT * FROM `CompletedAct` WHERE ID=1";
$resReq = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link)); 

if($resReq)
{	
	$row = $resReq->fetch_array(MYSQLI_ASSOC);
	//$arrReq = unserialize($row['REQ']);
	
	// $query_null = "UPDATE `CompletedAct` SET `act_number`='', `request_id`='', `is_won`='' WHERE ID=1";
	// $resReq = mysqli_query($link, $query_null) or die("Ошибка " . mysqli_error($link)); 
	
	echo json_encode($row);
	
}


die; ?>