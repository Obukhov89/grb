<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05.04.2019
 * Time: 12:12
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("УДАЛЕНИЕ");

?>
<style>
    /* #oborud th, #oborud td {
        border:1px solid
    } */
</style>
<?php
// $res_all = $DB->Query("SELECT * FROM `ba_gost` ORDER BY `ID` ASC");
// $gost_all=$res_all->Fetch();
// $ID[] = $gost_all['ID'];
// var_dump

$res = $DB->Query("SELECT * FROM `ba_gost` WHERE (`NAME_GOST` IS NULL OR `NAME_GOST`='')");
while($gost=$res->Fetch())
{
		
		echo " | ";
		$i=0;
		$res2 = $DB->Query("SELECT `ID` FROM `ba_gost` WHERE (`GOST`='".$gost['GOST']."' AND `SPECIFICATION` = '".$gost['SPECIFICATION']."')");
		   while($res3=$res2->Fetch())
		   {
			   
			     if ($i>0) 
				 //{$res_del = $DB->Query("DELETE FROM `ba_gost` WHERE `ID`=".(int)$res3['ID'])->Fetch();}	 
				 
				 echo " <a href=http://172.16.255.2/obl_acc.php?ID=".$res3['ID'].">".$res3['ID']."</a> ";
				 $i++;
		   }
				
		
	
	
}



 //       
 

?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>
