<?php
$link = mysqli_connect('localhost', 'baseuser', 'nageibei7uToonee71', 'nii_base');

	if (!$link)
	{
		die('Ошибка подключения (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
	}

// echo 'Соединение установлено... ' . mysqli_get_host_info($link) . "\n";


$coord = array();
$res_dev = mysqli_query($link, "SELECT * FROM `Quarry`");
// var_dump ($res_dev);

$i = 0;
while($res = mysqli_fetch_assoc($res_dev))
{
	$coord['coordLat'][$i] = $res['LAT'];
	$coord['coordLng'][$i] = $res['LNG'];
	$coord['name'][$i] = $res['NAME'];
	$coord['id'][$i] = $res['ID'];
	$i++;
}
//echo "<pre>";
//	var_dump($coord);

echo json_encode($coord, JSON_UNESCAPED_UNICODE); //показать созданный массив
mysqli_close($link);
exit;
?>