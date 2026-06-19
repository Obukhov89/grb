<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Формирование технического задания");

// if($_GET['ID']) 
	// {
		$date = date("Y-m-d", strtotime("2020-01-01"));
		$res_tz = $DB->Query("SELECT * FROM `ba_tz`");
		while($res = $res_tz->Fetch())
		{
			$price = $res['PRICE'];
			$oplata = $price;
			$date_p = $res['DATE_P_CORR'] ? $res['DATE_P_CORR'] : $res['DATE_P'];
			if (date("Y-m-d", strtotime($date_p))<$date&&$oplata) {
			  $res_op = $DB->Query("UPDATE `ba_tz` SET `OPLATA` = ".$oplata." WHERE `ID`=".$res['ID']);
			  echo $res['ID']."  ";
			}
		}
		// $tz = unserialize($res_tz['TZ']);
		// $price_all=0;
		// //var_dump($probe_edit);
		
		// foreach($tz as $key_mater => $mater)
		 // foreach($mater['gost'] as $key_gost => $gost)
		 // {
			 // $res = $DB->Query("SELECT `PRICE` FROM `ba_gost` WHERE `ID`=" . $gost)->Fetch();
			 
			 // $price = $res['PRICE'] ? $res['PRICE'] : '0';
			 
			 // $tz[$key_mater]['price'][$key_gost] = $price;
			 // $price_all+=$price*$mater['obiem'];
		
				// // if($res['PRICE']) $price=$price+$res['PRICE']*(int)$item['obiem'];
		 // }
	
			 	// echo "<pre />";
		// var_dump($tz);
		// echo $price_all;
		
		// $tz_up = serialize($tz);
		
		
		// $res_price = $DB->Query("UPDATE `ba_tz` SET `TZ` = '".$tz_up."', `PRICE` = ".$price_all." WHERE `ID`=".$_GET['ID']);

	// }
	// else
	// {
		
		 // $deals = CCrmDeal::GetList(Array('ID' => 'ASC'));
		 // while($deal = $deals->Fetch())
		 // {
		   // $deal_price = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
		   // $price = $deal_price['UF_CRM_1570765979554']['VALUE']; 
		    // if (CModule::IncludeModule('crm')) {
				// $deal2 = new CCrmDeal;
				// $arDealUpdate = array('OPPORTUNITY' => (int)$price);
				// $resUpd = $deal2->Update($deal['ID'],
					// $arDealUpdate,
					// true,
					// true,
					// array('DISABLE_USER_FIELD_CHECK' => true)
				// );
			// }
		 // }
		
//	}
	
	
	
 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>