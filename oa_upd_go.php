<? 
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!empty($_GET['gost']))
{
	//dump($_POST);
	$DB->Query("UPDATE `ba_gost_new` SET `ACCURACY`=".(int)$_GET['sign'].", `NORM1`=". $_GET['norm1'].", `NORM2`=". $_GET['norm2'].", `ED`='".$_GET['ed']."', `NUM_OA` = ".$_GET['num']." WHERE `ID`=".$_GET['gost']);	  
}


?>

<?
die; ?>