<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

global $DB, $USER, $APPLICATION;

$APPLICATION->RestartBuffer();

//if (!empty($_POST['tz_id']) && !empty($_POST['satge_id']) && $_POST['satge_id'] == 'WON') {
if (!empty($_POST['tz_id'])) {

	$satge_id = !empty($_POST['satge_id']) ? $_POST['satge_id'] : '';
	
	$text = null;
	
	switch ($_POST['satge_id']) {
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
			$text = 'Акты получены сделка завершена!';
			break;
		case 'LOSE':
			$c=7;
			$text = 'Сделка не состоялась!';
			break;
		case '7':
			$c=9;
			$text = 'Не проводим подобные испытания!';
			break;
		case '6':
			$c=9;
			$text = 'Заказчик не выходит на связь!';
			break;
		case '5':
			$c=9;
			$text = 'Не устроила цена!';
			break;
		case '8':
			$text = 'Создана другая заявка по данному запросу!';
			$c=9;
			break;
		case '9':
			$c=9;
			$text = 'Заказчик выбрал лабораторию в своем городе!';
			break;
		case '10':
			$c=9;
			$text = 'Заказчик решил не проводить испытания!';
			break;
		case '11':
			$c=9;
			$text = 'Отказались сами - большая загруженность!';
			break;
		case '12':
			$c=9;
			$text = 'Судебная экспертиза!';
			break;
		case '13':
			$c=9;
			$text = 'Участие в тендере!';
			break;
        case 'WON-NO-ACT':
            $c=6;
            $text = 'Акты не требуются, заявка учтена в основной';
	}
	if ($res_select_edit['ACT_NUM']&&($deal_edited['STAGE_ID']=='NEW'||$deal_edited['STAGE_ID']=='PREPARATION'||$deal_edited['STAGE_ID']=='PREPAYMENT_INVOICE'||$deal_edited['STAGE_ID']=='EXECUTING')) {
		$c=1;
	}
	if ($res_select_edit['PRICE']&&$res_select_edit['RESULTS']&&(!$res_select_edit['OPLATA']||$res_select_edit['OPLATA']<$res_select_edit['PRICE'])&&$deal_edited['STAGE_ID']=='2') {
		$c=5;
	} 
	

	$stage_number = !empty($c) ? (int)$c : '';
	
	$result_select_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID` = " . (int)$_POST['tz_id'])->Fetch();
	
	if (!empty($result_select_ba_tz['ID_Z'])) {

		if (CModule::IncludeModule('crm')) {
			$deal = new CCrmDeal;
			$arDealUpdate = array('STAGE_ID' => $satge_id);
			$resUpd = $deal->Update($result_select_ba_tz['ID_Z'],
				$arDealUpdate,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}
	}
	


	$satge_id = !empty($satge_id) ? $DB->ForSql($satge_id) : '';
	
	
	$res_assign_update = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $satge_id . "', `STAGE_NUMBER`= " . $stage_number . " WHERE `ID`=" . (int)$_POST['tz_id'])->Fetch();

	if ( $_POST['satge_id'] === 'WON' ) {
        $actVr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`= {$_POST['tz_id']}")->Fetch();
        $date_act = date('d.m.Y', strtotime($actVr['DATE']));
	    $data = [
	        'act_number' => "'{$actVr['NUMBER']}'",
	        'request_id' => $result_select_ba_tz['ID_Z'],
            'date_act'   => "'{$date_act}'",
//            'date_act'   => "'{$actVr['DATE']}'",
        ];
	    $DB->Insert('CompletedAct', $data);
        $_SESSION['message_success'] = "Сделка успешно завершена";
    }

	echo $resUpd;

}
