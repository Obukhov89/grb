<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Копирование методики");


 $res_tz = $DB->Query("INSERT INTO `ba_gost`
        (GOST,GOST_DOC,GOST_VAL,SPECIFICATION, NORM1_TEXT, NORM1, NORM2_TEXT, IN_OUT, MATCH_MANUAL, NORM_TEXT, ED, PRICE,COMMENT, 
		IN_OA, NORM_COMMENT, NAME_GOST, UNNORM, DOP, NORM_DOP,  GOST_TYPE, GOST_PUNKT, ACCURACY, LFHI, LFMI, DSL, LSM, OSK, TNVED, 
		GOST_OBJECT, RES_TEXT, NO_EXPORT, PLACE, GOST_YEAR, ASSIGNED, ED_INDEX, NUM_OA, TEMP, VLAG, PRESS, NUM_OA_NEW, 
		OKPD, MARKA, NON_ACTUAL, GROUP_PRICE) SELECT GOST,GOST_DOC,GOST_VAL,SPECIFICATION, NORM1_TEXT, NORM1, NORM2_TEXT, IN_OUT, MATCH_MANUAL, NORM_TEXT, ED, PRICE,COMMENT, 
		IN_OA, NORM_COMMENT, NAME_GOST, UNNORM, DOP, NORM_DOP,  GOST_TYPE, GOST_PUNKT, ACCURACY, LFHI, LFMI, DSL, LSM, OSK, TNVED, 
		GOST_OBJECT, RES_TEXT, NO_EXPORT, PLACE, GOST_YEAR, ASSIGNED, ED_INDEX, NUM_OA, TEMP, VLAG, PRESS,  NUM_OA_NEW, 
		OKPD, MARKA, NON_ACTUAL, GROUP_PRICE FROM `ba_gost` WHERE ID=".$_GET['ID']);
		
	// $res = $res_tz->Fetch();
	// var_dump($res_tz);
	$id_new = intval($DB->LastID());
	//$ob_arr = array();
	
  $res_ob =  $DB->Query("SELECT * FROM `ba_connection` WHERE `GOST_ID` = " . $_GET['ID']);
  while ($ob = $res_ob->Fetch())
  {
	  //$ob_arr[] = $ob['OBORUD_ID'];
	  $DB->Query("INSERT INTO `ba_connection`(`GOST_ID`, `OBORUD_ID`) VALUES (".$id_new.", ".$ob['OBORUD_ID'].")");
  
  }
  
    
 if ($res_tz) echo "Скопировано! <a href='/obl_acc.php?ID=".$id_new."'> Перейти к скопированной методике</a>";
 else echo "Ошибка копирования! <a href='/obl_acc.php'> Вернуться к списку методик</a>";


	
 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>