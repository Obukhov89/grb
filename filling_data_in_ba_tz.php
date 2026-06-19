<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

    include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

    //$result_select_ba_tz = $DB->Query("SELECT * FROM ba_tz");

    /*while($row_ba_tz = $result_select_ba_tz->Fetch()) {
        //ID сделки
        $id_deal = !empty($row_ba_tz['ID_Z']) ? (int)$row_ba_tz['ID_Z'] : '';

        $id_ba_tz = !empty($row_ba_tz['ID']) ? (int)$row_ba_tz['ID'] : '';
        
        //Проверяет установлен ли модуль и если установлен, то подключает его. Возвращает "true", если модуль установлен, иначе - "false".
        if (CModule::IncludeModule('crm')) {

            //Находим сделку по ID
            $deal = CCrmDeal::GetByID($id_deal);

            //$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос
            //стадия
            $stage_id = !empty($deal['STAGE_ID']) ? $DB->ForSql($deal['STAGE_ID']) : '';

            //Обновляем стадию в таблице ba_tz
            if (!empty($id_ba_tz)) {
               // $result_update_satage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $stage_id . "' WHERE `ID`=" . (int)$row_ba_tz['ID']);
            }

            //номер стадии
            switch ($deal['STAGE_ID']) {
                case 'NEW':
                    $c=0;
                    break;
                case 'PREPARATION':
                    $c=0;
                    break;
                case 'PREPAYMENT_INVOICE':
                    $c=0;
                    break;
                case 'EXECUTING':
                    $c=0;
                    break;
                case 'FINAL_INVOICE':
                    $c=1;
                    break;
                case '1':
                    $c=2;
                    break;
                case '2':
                    $c=3;
                    break;
                case '4':
                    $c=4;
                    break;
                // case '3':
                // $color='green';
                // $c=5;
                // $title='Протокол выдан';
                // break;
                case 'WON':
                    $c=6;
                    break;
                case 'LOSE':
                    $c=7;
                    break;
                case '7':
                    $c=9;
                    break;
                case '6':
                    $c=9;
                    break;
                case '5':
                    $c=9;
                    break;
                case '8':
                    $c=9;
                    break;
                case '9':
                    $c=9;
                    break;
                case '10':
                    $c=9;
                    break;
                case '11':
                    $c=9;
                    break;
                case '12':
                    $c=9;
                    break;
                case '13':
                    $c=9;
                    break;
            }
            if ($res_select_edit['ACT_NUM']&&($deal_edited['STAGE_ID']=='NEW'||$deal_edited['STAGE_ID']=='PREPARATION'||$deal_edited['STAGE_ID']=='PREPAYMENT_INVOICE'||$deal_edited['STAGE_ID']=='EXECUTING')) {
                $c=1;
            }
            if ($res_select_edit['PRICE']&&$res_select_edit['RESULTS']&&(!$res_select_edit['OPLATA']||$res_select_edit['OPLATA']<$res_select_edit['PRICE'])&&$deal_edited['STAGE_ID']=='2') {
                $c=5;
            }

            //Номер стадии в журнале заявок
            if (isset($c) && !empty($id_ba_tz)) {
              // $result_update_stage_number = $DB->Query("UPDATE `ba_tz` SET `STAGE_NUMBER`='" . (int)$c . "' WHERE `ID`=" . (int)$row_ba_tz['ID']);
            }

            //Имя главного ответственного
            $assigned_by_name = !empty($deal['ASSIGNED_BY_NAME']) ? $deal['ASSIGNED_BY_NAME'] : '';
            
            //Фамилия главного ответственного
            $assigned_by_last_name = !empty($deal['ASSIGNED_BY_LAST_NAME']) ? $deal['ASSIGNED_BY_LAST_NAME'] : '';
        
            $str_assigneds = getInitials($assigned_by_name) ." ". $assigned_by_last_name;

            //Получение значения пользовательского поля:
            $assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $id_deal);
            
            //Находим Доп. ответственого('UF_CRM_1571643970'), если он существует
            if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {

                foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
                {
                    $assign_user = CUser::GetByID((int)$assign)->Fetch();
                    $str_assigneds .= ", ".getInitials($assign_user['NAME'])." ".$assign_user['LAST_NAME'];
                }
            }
        
            if (!empty($id_ba_tz) && !empty($str_assigneds) && $str_assigneds != '.') {
               // $result_update_assigned = $DB->Query("UPDATE `ba_tz` SET `ASSIGNED`='" . $DB->ForSql($str_assigneds) . "' WHERE `ID`=" . $id_ba_tz);
            }

            //Заявка(Номер заявки)
            $title_deal_edit = !empty($deal['TITLE']) ? $DB->ForSql($deal['TITLE']) : '';
            
            if (!empty($id_ba_tz)) {
               // $result_update_request_tiltle = $DB->Query("UPDATE `ba_tz` SET `REQUEST_TITLE`='" . $title_deal_edit . "' WHERE `ID`=" . $id_ba_tz);
            }

            //Клиент
            $company_title_deal_edit = !empty($deal['COMPANY_TITLE']) ? $DB->ForSql($deal['COMPANY_TITLE']) : '';
            
            if (!empty($id_ba_tz)) {
               // $result_update_company_tiltle = $DB->Query("UPDATE `ba_tz` SET `COMPANY_TITLE`='" . $company_title_deal_edit . "' WHERE `ID`=" . $id_ba_tz);
            }

            //Лабораториии, Находим к какой лаборатории относиться главный ответсвенный
            $userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
            $id_laba = '';
            if (!empty($userr['UF_DEPARTMENT'][0])) {
                //ID лаборатории к которой относиться главный ответственный
                $id_laba = $userr['UF_DEPARTMENT'][0];
            }

            //Лабораториии,Находим к какой лаборатории относиться дополнительный ответственный
            $assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
            if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
                
                foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
                {
                    $assign_user = CUser::GetByID((int)$assign)->Fetch();
                    if($assign_user['UF_DEPARTMENT'][0])
                    {
                        //ID лаборатории к которой относиться дополнительный ответственный
                        $id_laba .= ',' . $assign_user['UF_DEPARTMENT'][0];
                    }
                }
            }
            
            if (!empty($id_ba_tz)) {
               // $result_update_laba_id = $DB->Query("UPDATE `ba_tz` SET `LABA_ID`='" . $id_laba . "' WHERE `ID`=" . $id_ba_tz);
            }

            //Дата (дата создания)
            $date_create_deal_new = !empty($deal['DATE_CREATE']) ? date("d.m.Y", strtotime($deal['DATE_CREATE'])) : '';

            //Поиск полей из списка дат в журнале заявок
            $date_create_datetime_deal_new = !empty($deal['DATE_CREATE']) ? date("Y-m-d H:i:s", strtotime($deal['DATE_CREATE'])) : '';
            
            if (!empty($id_ba_tz)) {
               // $result_update_date_create = $DB->Query("UPDATE `ba_tz` SET `DATE_CREATE`='" . $date_create_deal_new . "', `DATE_CREATE_TIMESTAMP`='" . $date_create_datetime_deal_new . "' WHERE `ID`=" . $id_ba_tz);
            }

            //Колонка "Крайний срок"
            $deadlineDate = ($row_ba_tz['DEADLINE'] ? ruDate($row_ba_tz['DEADLINE']) : $deal['UF_CRM_1577124965971']);
            //echo'<pre>';var_dump($deal['UF_CRM_1577124965971']);
            if ($deadlineDate=="01.01.1970") {
                $deadlineDate=date('d.m.Y', strtotime($deal['DATE_CREATE'])+ 86400*3);
            } 
			
            if (!empty($id_ba_tz)) {
               // $result_update_deadline = $DB->Query("UPDATE `ba_tz` SET `DEADLINE_TABLE`='" . $deadlineDate . "' WHERE `ID`=" . $id_ba_tz);
            }

            //Тип SALE-ИЦ, COMPLEX-ОСК, 1-ВЛК, 2-МСИ, 3-Дорожная
            $type_id_edited = !empty($deal["TYPE_ID"]) ?  $DB->ForSql($deal["TYPE_ID"]) : '';
            
            if (!empty($id_ba_tz)) {
               // $result_update_type_id = $DB->Query("UPDATE `ba_tz` SET `TYPE_ID`='" . $type_id_edited . "' WHERE `ID`=" . $id_ba_tz);
            }
        }

        if (!empty($id_ba_tz))
        {
            $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . $id_ba_tz . " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
            $history_ass=$history['ASSIGNED'];
            $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
            $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
            if($history_date<$history_date_null) $history_date='';

            //Колонка "последнее изменение"
            $assignet_history = $history_ass . " " . $history_date;

           // $update_assignet_ba_tz = $DB->Query("UPDATE ba_tz SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID` = " . $id_ba_tz);
        }

        $date_protocol = $row_ba_tz['DATE_P_CORR'] ? $row_ba_tz['DATE_P_CORR'] : $row_ba_tz['DATE_P'];
        $y_date_p = date("Y", strtotime($date_protocol));
        if ($y_date_p != '2020') {
            $y_date_p = "/" . substr($y_date_p, 2);
        } else {
            $y_date_p = "/".$y_date_p;
        }

        $num_tz = !empty($row_ba_tz['NUM_P']) && $row_ba_tz['NUM_P'] != 'номер не присвоен' ? $DB->ForSql($row_ba_tz['NUM_P'].$y_date_p) : 'номер не присвоен';

        if (!empty($id_ba_tz)) {
            //$result_update_num_p_table = $DB->Query("UPDATE `ba_tz` SET `NUM_P_TABLE`='" . $num_tz . "' WHERE `ID`=" . $id_ba_tz);
        }

        $act = !empty($row_ba_tz['ACT_NUM']) ? $row_ba_tz['ACT_NUM'] : '';
        $dateAct = !empty($row_ba_tz['DATE_ACT']) ? $row_ba_tz['DATE_ACT'] : '';

        $y_date_act = '';
        if (!empty($dateAct)) {
            $y_date_act = date("Y", strtotime($dateAct));
            if ($y_date_act != '2020') {
                $y_date_act = "/" . substr($y_date_act, 2);
            } else {
                $y_date_act = "/" . $y_date_act;
            }
        }
        //Колонка "АКТ ПП "(номер АКТа приемки проб)
        $num_act = !empty($act) ? $act . $y_date_act : '';
        
        if (!empty($id_ba_tz)) {
           // $resUp = $DB->Query("UPDATE `ba_tz` SET `NUM_ACT_TABLE`='" . $num_act . "' WHERE `ID`=". $id_ba_tz);
        }

        if (!empty($id_ba_tz)) {
            $dogovorNum = !empty($row_ba_tz['DOGOVOR_NUM']) ? $row_ba_tz['DOGOVOR_NUM'] : '';       
            $dogovor_table_arr = !empty($dogovorNum) ? explode(',', $dogovorNum) : '';
            $dogovor_table = !empty($dogovor_table_arr[0]) ? $dogovor_table_arr[0] : '';
           // $updateBaTz = $DB->Query("UPDATE `ba_tz` SET `DOGOVOR_TABLE` ='". $dogovor_table ."' WHERE `ID`=" . $id_ba_tz);
        }

        //Колонка "Обьект испытания"
		$mater_arr=array();
        $mater_arr_id=array();
    
        if (!empty($id_deal)) {
            $res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=" . $id_deal);
            while($mater=$res_mat->Fetch())
            {
                $mater_arr[]=$mater['NAME_MATERIAL'];
                $mater_arr_id[]=$mater['ID_MATERIAL'];
            }

            $dealMaterials = (!empty($mater_arr) ? implode(', ', $mater_arr) : '');

			//--'Вывод номера счета в журнале заявок''Исправил вывод материала' начало--//
            //$result_update_material = $DB->Query("UPDATE `ba_tz` SET `MATERIAL`='" . $dealMaterials . "' WHERE `ID`=" . $id_ba_tz);
			//--'Вывод номера счета в журнале заявок' окончание--//
		}
		
		//--'Вывод номера счета в журнале заявок' начало--//
		//Счет
		if (!empty($id_ba_tz)) {
            $res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $id_ba_tz)->Fetch();   
            $res_acc_id = !empty($res_acc['ID']) ? $res_acc['ID'] : '';
			
            //$update_account_ba_tz = $DB->Query("UPDATE `ba_tz` SET `ACCOUNT` ='". $res_acc_id ."' WHERE `ID`=" . $id_ba_tz);
        }
		//--'Вывод номера счета в журнале заявок' окончание--//
    
    }*/
	
	//Проверяет установлен ли модуль и если установлен, то подключает его. Возвращает "true", если модуль установлен, иначе - "false".
	if (CModule::IncludeModule('crm')) {
    
		//Вывод списка сделок
		$deals = CCrmDeal::GetListEx();
		
		while ($deal = $deals->Fetch()) {
			
			//ID сделки
			$id_deal = !empty($deal['ID']) ? (int)$deal['ID'] : '';
			
			//--'Сохранени данных в ba_tz с 1786 заявки'--//
			// if ($id_deal <= 2520) {
				// continue;
			// }
			
			//--'Сохранени данных в ba_tz с 1786 заявки'--//
			$result_select_ba_tz = $DB->Query("SELECT * FROM ba_tz WHERE `ID_Z` = " . $id_deal)->Fetch();
			
			//ID ba_tz
			$id_ba_tz = !empty($result_select_ba_tz['ID']) ? (int)$result_select_ba_tz['ID'] : '';
			
			//Заявка(Номер заявки)
			//$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос
            $title_deal_edit = !empty($deal['TITLE']) ? $DB->ForSql($deal['TITLE']) : '';
            
            if (!empty($id_ba_tz)) {
             // $result_update_request_tiltle = $DB->Query("UPDATE `ba_tz` SET `REQUEST_TITLE`='" . $title_deal_edit . "' WHERE `ID`=" . $id_ba_tz);
            } else {
				//$result_insert_request_tiltle = $DB->Query("INSERT INTO `ba_tz` (`REQUEST_TITLE`, `ID_Z`) VALUES ('" . $title_deal_edit . "', '" . $id_deal . "')");
			}
			
			$result_select_ba_tz = $DB->Query("SELECT * FROM ba_tz WHERE `ID_Z` = " . $id_deal)->Fetch();
			
			//ID ba_tz
			$id_ba_tz = !empty($result_select_ba_tz['ID']) ? (int)$result_select_ba_tz['ID'] : '';
			
			//$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос
            //стадия
            $stage_id = !empty($deal['STAGE_ID']) ? $DB->ForSql($deal['STAGE_ID']) : '';

			
			//Обновляем стадию в таблице ba_tz
            if (!empty($id_ba_tz)) {
             //  $result_update_satage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $stage_id . "' WHERE `ID`=" . $id_ba_tz);
            }
			
			//номер стадии
            switch ($deal['STAGE_ID']) {
                case 'NEW':
                    $c=0;
                    break;
                case 'PREPARATION':
                    $c=0;
                    break;
                case 'PREPAYMENT_INVOICE':
                    $c=0;
                    break;
                case 'EXECUTING':
                    $c=0;
                    break;
                case 'FINAL_INVOICE':
                    $c=1;
                    break;
                case '1':
                    $c=2;
                    break;
                case '2':
                    $c=3;
                    break;
                case '4':
                    $c=4;
                    break;
                // case '3':
                // $color='green';
                // $c=5;
                // $title='Протокол выдан';
                // break;
                case 'WON':
                    $c=6;
                    break;
                case 'LOSE':
                    $c=7;
                    break;
                case '7':
                    $c=9;
                    break;
                case '6':
                    $c=9;
                    break;
                case '5':
                    $c=9;
                    break;
                case '8':
                    $c=9;
                    break;
                case '9':
                    $c=9;
                    break;
                case '10':
                    $c=9;
                    break;
                case '11':
                    $c=9;
                    break;
                case '12':
                    $c=9;
                    break;
                case '13':
                    $c=9;
                    break;
            }
            if ($result_select_ba_tz['ACT_NUM']&&($deal['STAGE_ID']=='NEW'||$deal['STAGE_ID']=='PREPARATION'||$deal['STAGE_ID']=='PREPAYMENT_INVOICE'||$deal['STAGE_ID']=='EXECUTING')) {
                $c=1;
            }
            if ($result_select_ba_tz['PRICE']&&$result_select_ba_tz['RESULTS']&&(!$result_select_ba_tz['OPLATA']||$result_select_ba_tz['OPLATA']<$result_select_ba_tz['PRICE'])&&$deal['STAGE_ID']=='2') {
                $c=5;
            }

            //Номер стадии в журнале заявок
            if (isset($c) && !empty($id_ba_tz)) {
            //  $result_update_stage_number = $DB->Query("UPDATE `ba_tz` SET `STAGE_NUMBER`='" . (int)$c . "' WHERE `ID`=" . $id_ba_tz);
            }
			
			//Имя главного ответственного
            $assigned_by_name = !empty($deal['ASSIGNED_BY_NAME']) ? $deal['ASSIGNED_BY_NAME'] : '';
            
            //Фамилия главного ответственного
            $assigned_by_last_name = !empty($deal['ASSIGNED_BY_LAST_NAME']) ? $deal['ASSIGNED_BY_LAST_NAME'] : '';
        
            $str_assigneds = getInitials($assigned_by_name) ." ". $assigned_by_last_name;

            //Получение значения пользовательского поля:
            $assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $id_deal);
            
            //Находим Доп. ответственого('UF_CRM_1571643970'), если он существует
            if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {

                foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
                {
                    $assign_user = CUser::GetByID((int)$assign)->Fetch();
                    $str_assigneds .= ", ".getInitials($assign_user['NAME'])." ".$assign_user['LAST_NAME'];
                }
            }
        
            if (!empty($id_ba_tz) && !empty($str_assigneds) && $str_assigneds != '.') {
            //   $result_update_assigned = $DB->Query("UPDATE `ba_tz` SET `ASSIGNED`='" . $DB->ForSql($str_assigneds) . "' WHERE `ID`=" . $id_ba_tz);
            }
			
			//Дата (дата создания)
            $date_create_deal_new = !empty($deal['DATE_CREATE']) ? date("d.m.Y", strtotime($deal['DATE_CREATE'])) : '';

            //Поиск полей из списка дат в журнале заявок
            $date_create_datetime_deal_new = !empty($deal['DATE_CREATE']) ? date("Y-m-d H:i:s", strtotime($deal['DATE_CREATE'])) : '';
            
            if (!empty($id_ba_tz)) {
             //  $result_update_date_create = $DB->Query("UPDATE `ba_tz` SET `DATE_CREATE`='" . $date_create_deal_new . "', `DATE_CREATE_TIMESTAMP`='" . $date_create_datetime_deal_new . "' WHERE `ID`=" . $id_ba_tz);
            }
			
            //Клиент
            $company_title_deal_edit = !empty($deal['COMPANY_TITLE']) ? $DB->ForSql($deal['COMPANY_TITLE']) : '';
            
            if (!empty($id_ba_tz)) {
            //  $result_update_company_tiltle = $DB->Query("UPDATE `ba_tz` SET `COMPANY_TITLE`='" . $company_title_deal_edit . "' WHERE `ID`=" . $id_ba_tz);
            }
			
			//Лабораториии, Находим к какой лаборатории относиться главный ответсвенный
            $userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
            $id_laba = '';
            if (!empty($userr['UF_DEPARTMENT'][0])) {
                //ID лаборатории к которой относиться главный ответственный
                $id_laba = $userr['UF_DEPARTMENT'][0];
            }

            //Лабораториии,Находим к какой лаборатории относиться дополнительный ответственный
            $assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
            if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
                
                foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
                {
                    $assign_user = CUser::GetByID((int)$assign)->Fetch();
                    if($assign_user['UF_DEPARTMENT'][0])
                    {
                        //ID лаборатории к которой относиться дополнительный ответственный
                        $id_laba .= ',' . $assign_user['UF_DEPARTMENT'][0];
                    }
                }
            }
            
            if (!empty($id_ba_tz)) {
            //   $result_update_laba_id = $DB->Query("UPDATE `ba_tz` SET `LABA_ID`='" . $id_laba . "' WHERE `ID`=" . $id_ba_tz);
            }
			
            //Колонка "Крайний срок"
			$deadlineDate = ($deal['UF_CRM_1577124965971'] ? $deal['UF_CRM_1577124965971'] : ruDate($result_select_ba_tz['DEADLINE']));
			if ($deadlineDate=="01.01.1970") { $deadlineDate=date('d.m.Y', strtotime($deal['DATE_CREATE'])+ 86400*3); }
			
			 if (!empty($id_ba_tz)) {
              // $result_update_deadline = $DB->Query("UPDATE `ba_tz` SET `DEADLINE_TABLE`='" . $deadlineDate . "' WHERE `ID`=" . $id_ba_tz);
            }
			
			
            //Тип SALE-ИЦ, COMPLEX-ОСК, 1-ВЛК, 2-МСИ, 3-Дорожная
            $type_id_edited = !empty($deal["TYPE_ID"]) ?  $DB->ForSql($deal["TYPE_ID"]) : '';
            
            if (!empty($id_ba_tz)) {
            //    $result_update_type_id = $DB->Query("UPDATE `ba_tz` SET `TYPE_ID`='" . $type_id_edited . "' WHERE `ID`=" . $id_ba_tz);
            }
			
			if (!empty($id_ba_tz))
			{
				$history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . $id_ba_tz . " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
				$history_ass=$history['ASSIGNED'];
				$history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
				$history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
				if($history_date<$history_date_null) $history_date='';

				//Колонка "последнее изменение"
				$assignet_history = $history_ass . " " . $history_date;

			 //   $update_assignet_ba_tz = $DB->Query("UPDATE ba_tz SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID` = " . $id_ba_tz);
			}
			
			$date_protocol = $result_select_ba_tz['DATE_P_CORR'] ? $result_select_ba_tz['DATE_P_CORR'] : $result_select_ba_tz['DATE_P'];
			$y_date_p = date("Y", strtotime($date_protocol));
			if ($y_date_p != '2020') {
				$y_date_p = "/" . substr($y_date_p, 2);
			} else {
				$y_date_p = "/".$y_date_p;
			}

			$num_tz = !empty($result_select_ba_tz['NUM_P']) && $result_select_ba_tz['NUM_P'] != 'номер не присвоен' ? $DB->ForSql($result_select_ba_tz['NUM_P'].$y_date_p) : 'номер не присвоен';

			if (!empty($id_ba_tz)) {
				//$result_update_num_p_table = $DB->Query("UPDATE `ba_tz` SET `NUM_P_TABLE`='" . $num_tz . "' WHERE `ID`=" . $id_ba_tz);
			}
			
			$act = !empty($result_select_ba_tz['ACT_NUM']) ? $result_select_ba_tz['ACT_NUM'] : '';
			$dateAct = !empty($result_select_ba_tz['DATE_ACT']) ? $result_select_ba_tz['DATE_ACT'] : '';

			$y_date_act = '';
			if (!empty($dateAct)) {
				$y_date_act = date("Y", strtotime($dateAct));
				if ($y_date_act != '2020') {
					$y_date_act = "/" . substr($y_date_act, 2);
				} else {
					$y_date_act = "/" . $y_date_act;
				}
			}
			//Колонка "АКТ ПП "(номер АКТа приемки проб)
			$num_act = !empty($act) ? $act . $y_date_act : '';
			
			if (!empty($id_ba_tz)) {
			   $resUp = $DB->Query("UPDATE `ba_tz` SET `NUM_ACT_TABLE`='" . $num_act . "' WHERE `ID`=". $id_ba_tz);
			}
			
			if (!empty($id_ba_tz)) {
				$dogovorNum = !empty($result_select_ba_tz['DOGOVOR_NUM']) ? $result_select_ba_tz['DOGOVOR_NUM'] : '';       
				$dogovor_table_arr = !empty($dogovorNum) ? explode(',', $dogovorNum) : '';
				$dogovor_table = !empty($dogovor_table_arr[0]) ? $dogovor_table_arr[0] : '';
			    $updateBaTz = $DB->Query("UPDATE `ba_tz` SET `DOGOVOR_TABLE` ='". $dogovor_table ."' WHERE `ID`=" . $id_ba_tz);
			}
			
			//Колонка "Обьект испытания"
			$mater_arr=array();
			$mater_arr_id=array();
		
			if (!empty($id_deal)) {
				$res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=" . $id_deal);
				while($mater=$res_mat->Fetch())
				{
					$mater_arr[]=$mater['NAME_MATERIAL'];
					$mater_arr_id[]=$mater['ID_MATERIAL'];
				}

				$dealMaterials = (!empty($mater_arr) ? implode(', ', $mater_arr) : '');

				//--'Вывод номера счета в журнале заявок''Исправил вывод материала' начало--//
			//	$result_update_material = $DB->Query("UPDATE `ba_tz` SET `MATERIAL`='" . $dealMaterials . "' WHERE `ID`=" . $id_ba_tz);
				//--'Вывод номера счета в журнале заявок' окончание--//
			}
			
			//--'Вывод номера счета в журнале заявок' начало--//
			//Счет
			if (!empty($id_ba_tz)) {
				$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `TZ_ID`=" . $id_ba_tz)->Fetch();   
				$res_acc_id = !empty($res_acc['ID']) ? $res_acc['ID'] : '';
				
			//	$update_account_ba_tz = $DB->Query("UPDATE `ba_tz` SET `ACCOUNT` ='". $res_acc_id ."' WHERE `ID`=" . $id_ba_tz);
			}
			//--'Вывод номера счета в журнале заявок' окончание--//
			}
	
	}
?>