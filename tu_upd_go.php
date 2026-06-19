<? 
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!empty($_GET['gost']))
{
	//dump($_POST);
	$DB->Query("UPDATE `ba_gost` SET `ID_TU`=".$_GET['tu']." WHERE `ID`=".$_GET['gost']);	  
}

?>

<?
die; ?>