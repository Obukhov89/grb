<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!empty($_POST['str_id_requests']) && !empty($_POST['id_client_contract'])) {
	$arr_id_requests = explode('_', $_POST['str_id_requests']);
	
	$id_company_contract = !empty($request['CLIENT_ID']) ? $request['CLIENT_ID'] : '';

	$string_error = count($arr_id_requests) > 1 ? 'Компания не соответствует указанным заявкам ' : 'Компания не соответствует указанно заявке ';
	
	$arr_error = [];
	
	foreach($arr_id_requests as $key_id_request => $id_request) {
		$request = null; 
		if (!empty($id_request) && CModule::IncludeModule('crm')) {
			$request = CCrmDeal::GetByID($id_request); 
		}
		
		$id_client_request = !empty($request['COMPANY_ID']) ? $request['COMPANY_ID'] : '';
		 
		$title_request = !empty($request['TITLE']) ? $request['TITLE'] : '';
	
		if (!empty($id_client_request) && $_POST['id_client_contract'] != $id_client_request) {
			
			$arr_error[] = $title_request;
		}
	}
	
	$request_error = !empty($arr_error) ? implode(', ', $arr_error) : '';
	
	if (!empty($request_error) && !empty($string_error)) {
		echo $string_error . $request_error; 
	}
}
?>