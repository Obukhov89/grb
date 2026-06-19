<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

    include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";


	if (CModule::IncludeModule('crm')) {
    

		$deals = CCrmDeal::GetList(array('ID' => 'ASC'), array("STAGE_ID" => '4'));  
		
		while ($deal = $deals->Fetch()) {
			

			$id_deal = !empty($deal['ID']) ? (int)$deal['ID'] : '';
			
			$result_select_ba_tz = $DB->Query("SELECT * FROM ba_tz WHERE `ID_Z` = " . $id_deal)->Fetch();
			

			$id_ba_tz = !empty($result_select_ba_tz['ID']) ? (int)$result_select_ba_tz['ID'] : '';
			


            $stage_id = !empty($deal['STAGE_ID']) ? $DB->ForSql($deal['STAGE_ID']) : '';

			

            if (!empty($id_ba_tz)) {
               $result_update_satage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $stage_id . "' WHERE `ID`=" . $id_ba_tz);
            }
			

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


            if (isset($c) && !empty($id_ba_tz)) {
              $result_update_stage_number = $DB->Query("UPDATE `ba_tz` SET `STAGE_NUMBER`='" . (int)$c . "' WHERE `ID`=" . $id_ba_tz); 
            }

		}
	
	}

?>