<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!empty($_POST['id_grad'])) {
	$select_grad_dep = $DB->Query("SELECT * FROM `COINCED_COEFF` WHERE `ID`=" . (int)$_POST['id_grad'])->Fetch();
	
	$concrete_type = !empty($select_grad_dep['CONCRETE_TYPE']) ? round($select_grad_dep['CONCRETE_TYPE'], 2) : '';
	$calibr_dep_a = !empty($select_grad_dep['COEFF']) ? round($select_grad_dep['COEFF'], 4) : '';
	//$calibr_dep_b = !empty($select_grad_dep['CALIBR_DEP_B']) ? round($select_grad_dep['CALIBR_DEP_B'], 1) : '';
	
//	$arr_data_grad['concrete_type'] = $concrete_type;
	$arr_data_grad['calibr_dep_a'] = $calibr_dep_a;
//	$arr_data_grad['calibr_dep_b'] = $calibr_dep_b;

	echo json_encode($arr_data_grad);
	
}
?>