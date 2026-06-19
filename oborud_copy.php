<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Копирование карточки оборудования");

$res = $DB->Query("SELECT `place_of_installation_or_storage` FROM `ba_oborud` WHERE ID=".$_GET['ID'])->Fetch();	
$labID = $res['place_of_installation_or_storage'] ? $res['place_of_installation_or_storage'] : 6;
		$lab = 6;
		switch($labID)
		{
			case 1:
				$lab=1;
				break;
			case 2:
				$lab=4;
				break;
			case 3:
				$lab=3;
				break;
			case 4:
				$lab=2;
				break;
			case 5:
				$lab=5;
				break;
		}
		$y_date = date("Y");
		$yearStart = $y_date."-01-01";
		
		$resMaxInvNum = $DB->Query("select MAX(INV_NUM) from ba_oborud where place_of_installation_or_storage=".$labID." and god_vvoda_expluatation >='".$yearStart."'")->Fetch();
		$invNum = (int)$resMaxInvNum['MAX(INV_NUM)']+1;	
		$newInvNum = "№ ".substr($y_date, 2).".".$lab."-".$invNum;

 $res_tz = $DB->Query("INSERT INTO `ba_oborud` (`NAME`, `OBJECT`,`TYPE_OBORUD`,`POVERKA`,`manufacturer`,`god_vvoda_expluatation`,
 `measuring_range`,`сlass_precision_and_accuracy`,`certificate_of_verification`,`property_rights`,`place_of_installation_or_storage`,
 `IN_AREA`,`IN_STOCK`,`CHECKED`,`FACTORY_NUMBER`,`note`, `roomnumber`, `GOSREESTR`, `IDENT`, `TECH`, `OVV_EX`, `TOO_EX`, `AD_EX`, 
 `OVV_UH`, `TOO_UH`, `AD_UH`, `YEAR`, `REG_NUM_RC`, `COND`, `MC_INTERVAL`, `METR_CONTROL`, `NUM_PRIKAZ`, `DATE_PRIKAZ`, `ASSIGNED`,
 `SPISANIE`, `DATE_SP`, `POVERKA_LAST`, `POVERKA_PLACE`, `ID_ASSIGN1`, `ID_ASSIGN2`, `OVV_EX2`, `TOO_EX2`, `AD_EX2`, `PO_NAME`, `PO_ID`, 
 `NAPR`, `FREQ` ) SELECT `NAME`, `OBJECT`,`TYPE_OBORUD`,`POVERKA`,`manufacturer`,`god_vvoda_expluatation`,
 `measuring_range`,`сlass_precision_and_accuracy`,`certificate_of_verification`,`property_rights`,`place_of_installation_or_storage`,
 `IN_AREA`,`IN_STOCK`,`CHECKED`,`FACTORY_NUMBER`,`note`, `roomnumber`, `GOSREESTR`, `IDENT`, `TECH`, `OVV_EX`, `TOO_EX`, `AD_EX`, 
 `OVV_UH`, `TOO_UH`, `AD_UH`, `YEAR`, `REG_NUM_RC`, `COND`, `MC_INTERVAL`, `METR_CONTROL`, `NUM_PRIKAZ`, `DATE_PRIKAZ`, `ASSIGNED`,
 `SPISANIE`, `DATE_SP`, `POVERKA_LAST`, `POVERKA_PLACE`, `ID_ASSIGN1`, `ID_ASSIGN2`, `OVV_EX2`, `TOO_EX2`, `AD_EX2`, `PO_NAME`, `PO_ID`, 
 `NAPR`, `FREQ`  FROM `ba_oborud` WHERE ID=".$_GET['ID']);
 $id_new = intval($DB->LastID());
 $DB->Query("UPDATE `ba_oborud` SET `REG_NUM` ='".$newInvNum."', `INV_NUM` = ".$invNum." WHERE ID=".$id_new);
 if ($res_tz) echo "Скопировано! <a href='/oborud.php?ID=".$id_new."'> Перейти к скопированной карточке</a>";


	
 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>