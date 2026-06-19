<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Формирование технического задания");

$res = $DB->Query("SELECT * FROM `ba_gost`");

  while($row=$res->Fetch())
  {
		$assign = unserialize($row['ASSIGNED']);
		if (!$assign) echo "<a href='/obl_acc.php?ID=".$row['ID']."' target='_blank'>".$row['ID']. "  ".$row['GOST']. " " .$row ['SPECIFICATION']. "</a><br> ";
  }
	
 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>