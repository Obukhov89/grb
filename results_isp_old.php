<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DataStorage.php');

$newResult = new NewResult();

$getId = !empty($_GET['ID']) ? (int)$_GET['ID'] : 0;

//if (!empty($getId) && in_array((int)$getId, $newResult->arrIdDeal) || !empty($getId) && $getId === 5138  || !empty($getId) && $getId === 5479 || !empty($getId) && $getId === 5682 || !empty($getId) && $getId === 5697 || !empty($getId) && $getId === 5893) {
if (!empty($getId) && (in_array((int)$getId, $newResult->arrIdDeal) || (int)$getId > $newResult->idDealВefore)) {
    //require_once($_SERVER["DOCUMENT_ROOT"] . "/results_isp_binding_multiple_protocols_connection.php");
	
	require_once($_SERVER["DOCUMENT_ROOT"] . "/results_isp_multiple_protocols_OLD.php");
} else {
   require_once($_SERVER["DOCUMENT_ROOT"] . "/results_isp_old_protocols.php");
}

?>


