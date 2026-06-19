<? 
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

	$invNumbers = 0;
	$res = $DB->Query("SELECT `REG_NUM`, `god_vvoda_expluatation`, `ID`, `OBJECT` FROM `ba_oborud`");	  
	while($regNum = $res->Fetch())
	{
		$inv = explode('-',$regNum['REG_NUM']);
		if(count($inv)>1) $invNumbers = (int)$inv[count($inv)-1];
		else $invNumbers=0;
		$DB->Query("UPDATE `ba_oborud` SET `INV_NUM` = ".$invNumbers." WHERE ID=". $regNum['ID']);	 
		// if($regNum['REG_NUM']&&(!$regNum['god_vvoda_expluatation']||$regNum['god_vvoda_expluatation']=='1970-01-01'))
			// echo "<a href='/oborud.php?ID=".$regNum['ID']."'>".$regNum['OBJECT']."</a><br>";
	}

	//dump($invNumbers);
	//$DB->Query("UPDATE `ba_gost` SET `ID_TU`=".$_GET['tu']." WHERE `ID`=".$_GET['gost']);	  
	echo "НИШТЯК!";

?>

<?
die; ?>