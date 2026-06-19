<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

require_once $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";

$user_id = !empty($USER->GetID()) ? (int)$USER->GetID() : ''; 

//if (!empty($_POST['btn_transfer']) && $_POST['btn_transfer'] == 'transfer') {
if (!empty($_POST['transfer']) && $_POST['transfer'] == 'transfer') {
	
	$id_tz = !empty($_POST['id_tz']) ? (int)$_POST['id_tz'] : ''; 
	
	$date_submission = date('Y-m-d H:i:s');  
	  
	$select_ba_tz = null;
	if (!empty($id_tz)) {
		$select_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . $id_tz)->Fetch();
	}

	$group_all_main_assigned = CGroup::GetGroupUser(25);

	$id_z = !empty($select_ba_tz['ID_Z']) ? $select_ba_tz['ID_Z'] : '';
	
	$arr_all_id_main_assigned = [
		'53' => '9',
		'54' => '11',
		'55' => '13',
		'56' => '15',
		'57' => '58',
		'58' => '43',
	];
	
	$arr_all_fio_main_assigned = [
		'11' => 'Татьяна Овчинникова лаборатория ФХИ',
		'13' => 'Татьяна Велютич лаборатория ДСЛ',
		'15' => 'Шингарев Виталий лаборатория ФМИ',
		'58' => 'Регина Живцова лаборатория СМ',
		'43' => 'Елена Лавреньтьева лаборатория ОСК',
	];
//	$arr_all_fio_main_assigned = [
//		'11' => 'Татьяна Овчинникова лаборатория ФХИ',
//		'13' => 'Татьяна Велютич лаборатория ДСЛ',
//		'15' => 'Шингарев Виталий лаборатория ФМИ',
//		'9' => 'Алия Габдрахманова лаборатория СМ',
//		'43' => 'Елена Лавреньтьева лаборатория ОСК',
//	];
	 
	
    if (CModule::IncludeModule("crm") && $id_z) {

        $deal = CCrmDeal::GetByID($id_z);

        $id_user_main = !empty($deal["ASSIGNED_BY_ID"]) ? $deal["ASSIGNED_BY_ID"] : '';

		$deal_id = !empty($deal['ID']) ? $deal['ID'] : '';

        $data_request = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal_id);


		$arr_other_responsible = !empty($data_request['UF_CRM_1571643970']['VALUE']) ? $data_request['UF_CRM_1571643970']['VALUE'] : '';

		$assigned_to_request = $DB->Query("SELECT `user_id` FROM `assigned_to_request` WHERE `deal_id` = {$deal_id}");
		while ($mainA = $assigned_to_request->Fetch()) {
			$aArr[] = $mainA['user_id'];
		}

		$arr_all_id_main_assigned = [
			'53' => '9',
			'54' => '11',
			'55' => '13',
			'56' => '15',
			'57' => '58',
			'58' => '43',
		];

		$request_main_assigned = array_intersect($aArr, $arr_all_id_main_assigned);

		$arr_all_assigned = [];


		if (!empty($arr_other_responsible)) {
				array_push($arr_other_responsible, (int)$id_user_main);

				$arr_all_assigned = $arr_other_responsible;
		} else {
			$arr_all_assigned[] =  $id_user_main;
		}


        $assigned_by_name = !empty($deal['ASSIGNED_BY_NAME']) ? $deal['ASSIGNED_BY_NAME'] : '';
        
        $assigned_by_last_name = !empty($deal['ASSIGNED_BY_LAST_NAME']) ? $deal['ASSIGNED_BY_LAST_NAME'] : '';
    
        $str_assigneds = getInitials($assigned_by_name) ." ". $assigned_by_last_name;

		$userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();

		$arr_id_assigned = [];

		foreach ($arr_all_assigned as $main_leader)
		{
//			$find_leader_assigned = array_search($main_leader, $group_all_main_assigned);
			$find_leader_assigned = array_search($main_leader, $arr_all_id_main_assigned);
			if (($find_leader_assigned !== false) && ($main_leader != 1)) $main_leader_assigned[] = $main_leader;
			else continue;
		}
		
		$no_leader = [];
		$no_leader_lab = '';
		
		if (!empty($userr['UF_DEPARTMENT'][0])) {
			
			$id_main_laba = $userr['UF_DEPARTMENT'][0];
			
			$id_main_leader = !empty($arr_all_id_main_assigned[$id_main_laba]) ? $arr_all_id_main_assigned[$id_main_laba] : '';

			if (!empty($id_user_main) && !empty($id_main_leader) && !empty($id_main_laba) && $id_main_laba != 53 && !in_array($id_main_leader, $arr_all_assigned) && $id_user_main != $id_main_leader) {
				
				
				$data_main_leader = !empty($arr_all_fio_main_assigned[$id_main_leader]) ? $arr_all_fio_main_assigned[$id_main_leader] : '';

				if (!empty($data_main_leader)) {
					$no_leader[] = $data_main_leader;
				}
				
			} elseif (!empty($id_user_main) && !empty($id_main_leader) && !empty($id_main_laba) && $id_main_laba != 53 && $id_user_main == $id_main_leader) { 
//				$arr_id_assigned[] = $id_user_main;
				$arr_id_assigned[] = $main_leader_assigned;

			} elseif (!empty($id_main_laba) && $id_main_laba == 53) { 
				$no_leader_lab = 'Главный ответственный ' . $str_assigneds . ' не является руководителем лаборатории. ';
			}
			
		}
		
		$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal_id);
		if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {

			foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
			{
				$id_user_additional = !empty($assign) ? $assign : '';
				
				$assign_user = CUser::GetByID((int)$assign)->Fetch();
				if($assign_user['UF_DEPARTMENT'][0])
				{

					$id_additional_laba = $assign_user['UF_DEPARTMENT'][0];
					
					if ($id_additional_laba == '53' || $id_additional_laba == '58') {
						continue;
					}
					
					$id_additional_leader = !empty($arr_all_id_main_assigned[$id_additional_laba]) ? $arr_all_id_main_assigned[$id_additional_laba] : '';

					if (!empty($id_user_additional) && !empty($id_additional_leader) && !in_array($id_additional_leader, $arr_all_assigned) && $id_user_additional != $id_additional_leader) {
						
						
						$data_additional_leader = !empty($arr_all_fio_main_assigned[$id_additional_leader]) ? $arr_all_fio_main_assigned[$id_additional_leader] : '';
						
						if (!empty($data_additional_leader)) {
							$no_leader[] = $data_additional_leader;
						}
						
					}
                }
			}
        }

	
		if (!empty($no_leader) || !empty($no_leader_lab)) {

			$no_leader_unique = array_unique($no_leader);

			$str_leader = '';

			if (count($no_leader_unique) > 1) {
				$str_leader = 'руководителей лабораторий ';
			} else {
				$str_leader = 'руководителя лаборатории ';
			}

			$error_leader_assigned = 'Укажите ' . $str_leader;

			$str_no_leader = implode(', ', $no_leader_unique);

			$error_no_leader = '';

			if (!empty($no_leader) && !empty($no_leader_lab)) {
				$error_no_leader = $no_leader_lab . $error_leader_assigned . $str_no_leader;
			} elseif (!empty($no_leader) && empty($no_leader_lab)) {
				$error_no_leader = $error_leader_assigned . $str_no_leader;
			} elseif (empty($no_leader) && !empty($no_leader_lab)) {
				$error_no_leader = $no_leader_lab;
			}

			$arr_error = [];

			$arr_error['error'] =  $error_no_leader;
//			$arr_error['error'] =  $data_additional_leader;

			echo json_encode($arr_error);


		} else {
			
			//if (!empty($arr_id_assigned)) {

			if (!empty($arr_id_assigned) && !empty($_POST['btn']) && $_POST['btn'] == 'btn_transfer') { //Если кнопка "$_POST['btn_transfer']"=="Передать на рассмотрение руководителю лаборатории"
				
				foreach($request_main_assigned  as $id_assign)
				{
					if ($id_assign == '43') {
						continue;
					}
					$result_insert_check_tz = $DB->Query("INSERT INTO `CHECK_TZ` (`tz_id`, `user_sent_id`, `date_submission`, `leader`) VALUES (" . $id_tz . ", " . $user_id . ", '" . $date_submission . "', '" . $id_assign . "')");

				}
				$result_select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $id_tz);
				
				$sum_select_check_tz = $result_select_check_tz->SelectedRowsCount();
				
				if (!empty($sum_select_check_tz)) {
					$arr_transfer['transfer'] = true;

					echo json_encode($arr_transfer);
				}
					
			} elseif (!empty($_POST['btn']) && $_POST['btn'] == 'btn_confirm') { //Если кнопка(чекбокс) "$_POST['btn_confirm']"=="Техническое задание утверждено руководителем лаборатории"
				$arr_transfer['transfer'] = true;

				echo json_encode($arr_transfer);
			} 
		}
		
    }
	
}

  
if (!empty($_POST['checbox_confirm']) && $_POST['checbox_confirm'] == 'confirm' && !empty($_POST['id_tz'])) {
	
	$tz_confirm = !empty($_POST['tz_confirm']) ? (int)$_POST['tz_confirm'] : (int)0;
	
	$date_reply = date('Y-m-d H:i:s');

	$result_update_check_tz = $DB->Query("UPDATE `CHECK_TZ` SET `date_reply`='" . $date_reply . "', `confirm`=" . $tz_confirm . " WHERE `tz_id`=" . (int)$_POST['id_tz'] . " AND `leader`=" . $user_id);
	if (!empty($result_update_check_tz->result) && !empty($tz_confirm)) {
		echo 'Техническое задание проверено!';
	}
}

//Действие по событию click на кнопку "Вернуть"('.btn-no-transfer') - Снять заявку переданную на рассмотрение руководителю лаборатории
if (!empty($_POST['transfer']) && $_POST['transfer'] == 'return' && !empty($_POST['id_tz'])) {
	//Дата возврата(снятия) технического задания с расмотрения
	$date_return = date('Y-m-d H:i:s');

	$result_update_check_tz = $DB->Query("DELETE FROM `CHECK_TZ` WHERE `tz_id`={$_POST['id_tz']}");
	
	if (!empty($result_update_check_tz->result)) {
		$arr_transfer['return'] = 'ТЗ вернули с расмотрения!';
					
		echo json_encode($arr_transfer);
	}
}
?>
