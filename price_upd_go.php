<? 
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!empty($_GET['gost'])&&empty($_GET['contract']))
{
	//dump($_POST);
	$DB->Query("UPDATE `ba_gost` SET `PRICE`=".(float)$_GET['price']." WHERE `ID`=".$_GET['gost']);	  
}
elseif(!empty($_GET['contract']))
{
	$DB->Query("UPDATE `PRICE_FOR_CONTRACTS` SET `PRICE`=".(float)$_GET['price']." WHERE `ID_CONTRACT`=".(int)$_GET['contract']." AND `ID_GOST` =".$_GET['gost']);
}

?>

<?
die; ?>