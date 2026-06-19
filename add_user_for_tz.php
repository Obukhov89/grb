<? require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
//----------------------'Серверная обработка Журнала заявок' начало---------------------//
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
/*---------------------'Серверная обработка Журнала заявок' окончание---------------------*/
if (!empty($_POST['id_user'])) {

    $idZ = null;

    if (!empty($_POST['edit'])) {
        $resTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_POST['edit'])->Fetch();
        $idZ = !empty($resTz['ID_Z']) ? $resTz['ID_Z'] : '';
    } else if (!empty($_POST['id'])) {
        $idZ = $_POST['id'];
    }

    $idUser = !empty($_POST['id_user']) ? $_POST['id_user'] : '';

    if (CModule::IncludeModule("crm") && $idZ) {

        $deal = CCrmDeal::GetByID($idZ);

        //ID Главного ответсвенного
        $idUserMain = !empty($deal["ASSIGNED_BY_ID"]) ? $deal["ASSIGNED_BY_ID"] : '';

        $dealId = !empty($deal['ID']) ? $deal['ID'] : '';

        $dataRequest = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $dealId);

        //Другие, дополнительные ответственные
        $arrOtherResponsible = !empty($dataRequest['UF_CRM_1571643970']['VALUE']) ? $dataRequest['UF_CRM_1571643970']['VALUE'] : '';
		
        if (!empty($arrOtherResponsible)) {
            //array_push($arrOtherResponsible, $idUser);
	    // проверяем есть ли в доп.ответсвенных уже такие люди
            // и если есть не добавляем
            // также с главным
            if(!in_array($idUser, $arrOtherResponsible) && $idUser != $idUserMain){
                array_push($arrOtherResponsible, $idUser);
            }
        } else {
			$arrOtherResponsible = [];
            $arrOtherResponsible[] =  $idUser;
        }
		
		 //----------------------'Серверная обработка Журнала заявок' начало---------------------//   
        //Создаем экземпляр класса сделки
        $deal_up = new CCrmDeal;
        //$deal = new CCrmDeal;
        $arDealUpdate = array(
            'UF_CRM_1571643970' => $arrOtherResponsible
        );

        //Обновляем данные
        //$resUpd = $deal->Update($dealId,
		$resUpd = $deal_up->Update($dealId,
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );

        //$resultUpdate = $resUpd;
		
		//Имя главного ответственного
        $assigned_by_name = !empty($deal['ASSIGNED_BY_NAME']) ? $deal['ASSIGNED_BY_NAME'] : '';
            
        //Фамилия главного ответственного
        $assigned_by_last_name = !empty($deal['ASSIGNED_BY_LAST_NAME']) ? $deal['ASSIGNED_BY_LAST_NAME'] : '';
    
        $str_assigneds = getInitials($assigned_by_name) ." ". $assigned_by_last_name;


        //Лабораториии, Находим к какой лаборатории относиться главный ответсвенный
		$userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
		$id_laba = '';
		if (!empty($userr['UF_DEPARTMENT'][0])) {
			//ID лаборатории к которой относиться главный ответственный
			$id_laba = $userr['UF_DEPARTMENT'][0];
		}
       
        //Находим к какой лаборатории относиться дополнительный ответственный
		$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $dealId);
		if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
			
			foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
			{
				$assign_user = CUser::GetByID((int)$assign)->Fetch();
				if($assign_user['UF_DEPARTMENT'][0])
				{
					//ID лаборатории к которой относиться дополнительный ответственный
					$id_laba .= ',' . $assign_user['UF_DEPARTMENT'][0];
                }

                //Находим Доп. ответственого
                $str_assigneds .= ", ".getInitials($assign_user['NAME'])." ".$assign_user['LAST_NAME'];
                
			}
        }
		
		//Обновляем ID лабораторий в ba_tz
        $result_update_laba_id = $DB->Query("UPDATE `ba_tz` SET `LABA_ID`='" . $id_laba . "', `ASSIGNED`='" . $DB->ForSql($str_assigneds) . "' WHERE `ID_Z`=" . $idZ);


        $users = $DB->Query("SELECT user_id, is_main FROM `assigned_to_request` WHERE deal_id = {$dealId}");
		if (!empty($users->SelectedRowsCount())) {
            $dataAssignedToRequest = [
                'deal_id' => $dealId,
                'user_id' => $idUser,
                'is_main' => 0,
            ];

            $DB->Insert('assigned_to_request', $dataAssignedToRequest);
        }

        $data = [];
        $data['resultUpdate'] = $resUpd;
        $data['arrOtherResponsible'] = $arrOtherResponsible;

        echo json_encode($data);
    }

}



exit;
?>

