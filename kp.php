<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10.04.2019
 * Time: 15:36
 */


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал договоров");
?>

  
<?php
if (!empty($_GET['TZ_ID'])) {

$res = $DB->Query("SELECT `ID` FROM `KP` WHERE `TZ_ID`=" . $_GET['TZ_ID']);

	$sql = $res->Fetch();   
    
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>