<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Формирование прайса");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

if (!empty($_POST['gost']))
{
	//dump($_POST);
	$DB->Query("UPDATE `ba_gost` SET `PRICE`=".tofloat($_POST['price_new'][0])." WHERE `ID`=".$_POST['gost']);	  
}

$res_oa = $DB->Query("SELECT * FROM `ba_gost` ORDER BY `PRICE` ASC");	

$changeArr = [2, 14, 1246, 1252, 1254, 25, 1231, 1114, 1118, 172, 1130, 1132, 3058, 1092, 2997, 1098, 1106, 2655, 66, 1075, 105, 106, 107, 955, 957, 882, 884, 885, 886, 887, 890, 558, 562,
310, 325, 870, 2775, 2776, 860, 2911, 3021, 438, 592, 3006, 3007, 3008, 590, 2734, 2735, 594, 595, 596, 1896, 669, 467, 468, 3097, 2961, 2962, 2761, 495, 1831, 1832, 601, 2892,
1841, 611, 612, 622, 3019, 1856, 3022, 625, 627, 2879, 614, 616, 618, 620, 621, 651, 2748, 893, 894, 895, 896, 898, 1154, 1209, 1210, 962, 939, 940, 941, 918, 2777, 910, 911,
912, 913, 914, 915, 916, 929, 189, 219, 1463, 1508, 2367, 357, 358, 359, 998, 2881, 2875, 3080, 307, 564, 312, 58, 314, 1084, 2372, 1115, 611];
?>
<table style="border: 1px"><?
while($gost = $res_oa->Fetch())
{ 
	if(!$gost['NON_ACTUAL'] && $gost['GOST_TYPE']!='TU_standart' && $gost['GOST_TYPE']!='TU_research' && $gost['GOST_TYPE']!='TU_group' && $gost['PRICE']==0 && $gost['GOST_TYPE']!= 'metodic_otbor'){
	$changeFlag = in_array((int)$gost['ID'], $changeArr) ? 'Возможно редактирование' : '';
	$inOA = $gost['IN_OA'] ? 'OA' : '';
	$typeGost = (($gost['GOST_TYPE']=='TU_standart'||$gost['GOST_TYPE']=='TU_research'||$gost['GOST_TYPE']=='TU_group') ? 'ТУ' : '');
	echo "<form method='post'><tr id=".$gost['ID']."><td> ID: ".$gost['ID']."</td><td style='width:100px'>".$inOA."</td><td style='width:60px'>".$typeGost."</td><td style='width:250px'><b>".$gost['GOST']." ".$gost['GOST_PUNKT']."</b></td><td style='width: 400px'>".$gost['SPECIFICATION']."</td><td> цена: ".$gost['PRICE'].
	"</td><td><input type='text' value='".$gost['ID']."' name='gost' hidden><input type='number' value='' class='price_new' style='width:100%; margin: 11px 0 0 0px'></td><td><input type='button' name='bt' value='Сохранить'></td><td>".$changeFlag."</td></tr></form>";
	}
}
?>
</table>
<script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
<script>
$('input[name=bt]').on("click", function () {
				
			 
				tr = $(this).parent().parent();
				tr_dataid = tr.attr('id');
				price_inp = tr.find('.price_new');
				price = price_inp[0].value;
				console.log(tr_dataid);
				console.log(price);
				$.ajax({

				  url: 'price_upd_go.php?gost='+tr_dataid+'&price='+price,
	
				  success: function(data) {
					alert('OK');
				  }
				
				});
				
		
			
		});
</script>
<?

	
 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>