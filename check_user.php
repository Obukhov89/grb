<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!empty($_POST['id_main_assigned'])) {

		if (!empty($_POST['id_request']) && CModule::IncludeModule('crm')) {
			$request = CCrmDeal::GetByID($_POST['id_request']);
			
			$date_create_request = !empty($request['DATE_CREATE']) ? date('Y-m-d', strtotime($request['DATE_CREATE'])) : ''; //Дата создании заявки
			
			if (!empty($date_create_request) && $date_create_request < '2020-09-12') {
				exit; 
			}
		}
	
		$arr_all_id_main_assigned = [
			'54' => '11',
			'55' => '13',
			'56' => '15',
			'57' => '58',
			'58' => '43',
		];  
 
		$user_data = CUser::GetByID($_POST['id_main_assigned'])->Fetch();
		
		$id_laboratory = !empty($user_data['UF_DEPARTMENT'][0]) ? $user_data['UF_DEPARTMENT'][0] : '';
		
		$id_main_user = !empty($arr_all_id_main_assigned[$id_laboratory]) ? $arr_all_id_main_assigned[$id_laboratory] : '';
	  
		if (!in_array($_POST['id_main_assigned'], [$id_main_user, 62, 35, 56])) {
			echo "Выбранный главный ответственный не является руководителем лаборатории"; 
		} 
		
}
?>