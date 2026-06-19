<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Формирование технического задания");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

function checkbox_verify($_name)
// Выполняет: проверку checkbox
{
$result=0;

if (isset($_REQUEST[$_name]))
{ if ($_REQUEST[$_name]=='on') {$result=1;}
}
return $result;
}

?>
	<link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
	<script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
	<script>
	 $(document).ready(function () {
            
			$('.gost').select2();
	 });
	</script>
	<script>
	 $(document).ready(function () {
            
			$('.gost2').select2();
	 });
	</script>
<style type="text/css">
  #tzc, #tzc option {
 width: 500px;
  margin: 0;

  }
</style>


    <datalist id="gost">
        <?php
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
        while ($gost = $ba_gost->Fetch()) {
            echo "<option data-type='" . $gost['NORM_TEXT'] . "' " . (($arrDatum['gost'] == $gost['ID']) ? 'selected' : '') . " value='" . $gost['ID'] . ' | ' . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] .  "'></option>";
        }
        ?>
    </datalist>
	
     <datalist id="gost_new">
        <?php
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
        while ($gost = $ba_gost->Fetch()) {
            echo "<option data-type='" . $gost['NORM_TEXT'] . "' " . (($arrDatum['gost'] == $gost['ID']) ? 'selected' : '') . " value='" . $gost['ID'] . "'>" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
        }
        ?>
    </datalist>
	
	<datalist id="objects">
	<?php
        $ba_obj = $DB->Query("SELECT * FROM `DEV_OBJECTS`");
        while ($obj = $ba_obj->Fetch()) {
            echo "<option data-type='" . $obj['NAME'] . "' >" . $obj['NAME'] . "</option>";
        }
        ?>
	</datalist>
	
	<datalist id="contracts">
	<?php
        $ba_contr = $DB->Query("SELECT * FROM `DOGOVOR`");
        while ($obj = $ba_contr->Fetch()) {
            echo "<option data-id='" . $obj['ID'] . "' >Договор №" . $obj['NUMBER'] . " от " . ruDate($obj['DATE']). ", ID" . $obj['ID'] . "</option>";
        }
        ?>
	</datalist>

<?php
$company1=''; $extranet_id='';
if(!empty($_POST['tz_copy']) && isset($_POST['TZ_COPY']) && $_POST['TZ_COPY'] == 'on') {
	//var_dump($_POST);
	$ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE ID=".$_POST[tz_copy])->Fetch();
	//var_dump($ba_tz);
    
        $res = $DB->Query("INSERT INTO `ba_tz`(`TZ`,`OBJECT`, `DATE_SOZD`, `ID_Z`, `DOGOVOR_NUM`, `ACT_NUM`, `DESCRIPTION`, `DATE_ACT`) VALUES ('" . $ba_tz['TZ'] . "','" . $_POST['OBJECT'] . "',NOW()," . (int)$_GET['ID'] . ",'" . $_POST['DOGOVOR_NUM'] . "'," . (int)$_POST['ACT_NUM'] . ",'" . $_POST['DESCRIPTION'] . "','" . $_POST['DATE_ACT'] . "')");
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    
	$arr = unserialize($ba_tz['TZ']);
	//var_dump($arr);
    $ID_TZ = (!empty(((int)$_POST['id_tz'])) ? (int)$_POST['id_tz'] : $DB->LastID());

    if (CModule::IncludeModule('crm')) {
        $deal = new CCrmDeal;
        $arDealUpdate = array('UF_CRM_1554700718621' => $_SERVER["SERVER_NAME"] . "/tz_show.php?ID=" . $ID_TZ, 'STAGE_ID' => 'PREPARATION');
        $resUpd = $deal->Update((int)$_POST['id_deal'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    }

    ?>
    <table id="tz">
        <thead>
        <th>Наименование материала</th>
        <th>Объём работ</th>
        <th>Наименование показателя</th>
        </thead>
        <tbody id="tz_body">
        <?php
        $data = [];
        foreach ($arr as $key => $arrDatum) {
            $data[] = $arrDatum['mater'];
        }
        $a = array_count_values($data);

        $last_gost = '';

        foreach ($arr as $key => $arrDatum) {
            $i = $a[$arrDatum['mater']];
            ?>
            <tr>
                <?php
                if ($last_gost != $arrDatum['mater']) {
                    ?>
                    <td rowspan="<?= $i ?>"><?= $arrDatum['mater'] ?></td>
                <?php }
                $last_gost = $arrDatum['mater'];
                ?>
                <td><?= $arrDatum['obiem'] ?> <?= num2word($arrDatum['obiem'], ['проба', 'пробы', 'проб']) ?></td>
                <td>
                    <?php
                    foreach ($arrDatum['gost'] as $item) {
                        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                        echo "<p>" . $res['SPECIFICATION'] . "</p>";
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
	<br/>
    <a href="/tz_show.php">К списку ТЗ</a>
    |
    <a href="/<?=($_POST['ACT_LIST']=='3' ? 'control_list' : 'probe')?>.php?ID=<?= $_POST['id_deal'] ?>"><?=($_POST['ACT_LIST']=='3' ? 'Создать контрольный лист' : 'Создать акт приемки проб')?></a>
    <?php
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
    exit;
}
 
if (!empty($_POST['mater'])) {
	
    $arrData = []; $probe = []; $attestat=array();
	
	if($_GET['EDIT']) 
	{
		$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=".$_GET['EDIT'])->Fetch();
		$probe_edit = unserialize($res_tz['PROBE']);
		//var_dump($probe_edit);
	}
	$deal1 = CCrmDeal::GetByID($_GET['ID']);
	$y_date = date("Y", strtotime($_POST['DATE_ACT']));
	if ($y_date != '2020') $y_date = "/".substr($y_date, 2);
	else $y_date = "/".$y_date;
	
	$extranet = $USER_FIELD_MANAGER->GetUserFields('CRM_COMPANY', $deal1['COMPANY_ID']);
	$extranet_id = $extranet['UF_CRM_1571818346092']['VALUE']; $sh=1;
	$otbor=0;
	$key_p=1;
	//echo $extranet_id;
	//dump($_POST);
    foreach ($_POST['mater'] as $key => $item) {
        $arrData[$_POST['mater'][$key]][$key]['gost'] = $_POST['gost'][$key];
        $arrData[$_POST['mater'][$key]][$key]['obiem'] = $_POST['obiem'][$key];
        $arrData[$_POST['mater'][$key]][$key]['gost_new'] = $_POST['gost_new'][$key];
		$arrData[$_POST['mater'][$key]][$key]['price'] = $_POST['price'][$key];
		//echo $key;
		
			if (!empty($_POST['ACT_NUM'])) {
				$probe[$key_p] = array(
				"zakaz_man" => $deal1['COMPANY_ID'],
				"number_probe" => [],
				"mesto_data" => $_POST['PLACE_PROBE'].'; '.date("d.m.Y", strtotime($_POST['DATE_PROBE'])),
				"probe_proizv" => 'заказчик',
				"number" => $_POST['ACT_NUM'],
				"sh_number" => [],
				"dat" => '',
				);
				
			
				for($l=0; $l<(int)$_POST['obiem'][$key]; $l++){
					$probe[$key_p]["number_probe"][$l] = $probe_edit[$key_p]['number_probe'][$l] ? $probe_edit[$key_p]['number_probe'][$l] : $item;
					$probe[$key_p]["sh_number"][$l] =  $probe_edit[$key_p]['sh_number'][$l] ? $probe_edit[$key_p]['sh_number'][$l] : $_POST['ACT_NUM'].'.'.(int)($sh).$y_date;
					$sh++;
				}
				$key_p++;
			}
        // $arrData[$_POST['mater'][$key]][$key]['gost_new_desc'] = $_POST['gost_new_desc'][$key];
        // $arrData[$_POST['mater'][$key]][$key]['norm_1'] = $_POST['norm_1'][$key];
        // $arrData[$_POST['mater'][$key]][$key]['norm_2'] = $_POST['norm_2'][$key];
        // $arrData[$_POST['mater'][$key]][$key]['ed_izm'] = $_POST['ed_izm'][$key];
    }
	//echo "<br />PROBE<br />"; 
	//dump($probe);
	$res_otbor = array();
    $i = 0; $price=0;
    foreach ($arrData as $key => $arrDatum) {
        foreach ($arrDatum as $item) {
            $i++;
            $arr[$i]['mater'] = $key;
            $arr[$i]['gost'] = $item['gost'];
            $arr[$i]['obiem'] = $item['obiem'];
            $arr[$i]['gost_new'] = $item['gost_new'];
			$arr[$i]['price'] = $item['price'];
			$otbor=0;
			
			//var_dump($item['gost']);
			// foreach($item['gost_new'] as $g => $gost_new)
			// {
				// $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $gost_new)->Fetch();
				// if($res['PRICE']) $price=$price+$res['PRICE']*(int)$item['obiem'];
			
				
			// }
			
			foreach($item['gost'] as $g => $gost)
			 {
				$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $gost)->Fetch();
			
				if($res['GOST_TYPE']=='metodic_otbor') $otbor=$res['ID'];
			
			 }
			// dump($probe[$i]);
			if ($otbor)
				{
					$pp=0;
					foreach($probe[$i]['sh_number'] as $p => $probe_i)
			 		{ 
						$gg=0;
						foreach($item['gost'] as $g => $gost)
						
						 {
							$check_otbor = $DB->Query("SELECT `ID` FROM `PODGOTOVKA` WHERE `NUM_PROBE`=".$probe_i." AND `GOST_ID`=" . $gost)->Fetch(); //ЗДЕСЬ ПРОВЕРЯЕМ ПО НОМЕРУ ГОСТА И НОМЕРУ ПРОБЫ!! ЕСЛИ ТАКАЯ ЗАПИСЬ УЖЕ ЕСТЬ - АПДЕЙТ. ИНАЧЕ ИНСЕРТ
							
							if(!$check_otbor) {
								$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $gost)->Fetch();
								//var_dump($res); 
								//echo $gg;
								if($res['GOST_TYPE']!='metodic_otbor') {
									$res_otbor['NUM_PROBE'][$pp][$gg] = $probe_i;
									$res_otbor['GOST_ID'][$pp][$gg] = $gost;
									$res_otbor['PROBE_NAME'][$pp][$gg] = $probe[$i]['number_probe'][0];
									$res_otbor['GOST_OTBOR_ID'][$pp][$gg] = $otbor;
								$gg++;	
								}
								
								// var_dump($res_otbor);
								// echo "<br /><br />";
							}
							
						
						 } $pp++;
					
					}
						
					
						
				}
			//echo $otbor;
			
			//$price=$price+$res['PRICE'];
            // $arr[$i]['gost_new_desc'] = $item['gost_new_desc'];
            // $arr[$i]['norm_1'] = $item['norm_1'];
            // $arr[$i]['norm_2'] = $item['norm_2'];
            // $arr[$i]['ed_izm'] = $item['ed_izm'];
        }
    }
    
	
	for($i=1; $i<=count($arr); $i++)
		for($j=0; $j<count($arr[$i]['gost']); $j++)
		{
			
			//$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $arr[$i]['gost'][$j])->Fetch();
			//if($res['PRICE']) 
				$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $arr[$i]['gost'][$j])->Fetch();
				if(!$res['IN_OA']) $attestat[]=$res['ID'];
			//if($res['PRICE']) 
				$price=$price+($arr[$i]['price'][$j])*$arr[$i]['obiem'];
				
			
		//	echo $price;
		//dump($arr[$i]['obiem']);
			
		}
	  
	$in_oa=1;
	if(count($attestat)) 	$in_oa=0;	
	
    if (!empty($_POST['id_tz'])) {
		
        $res = $DB->Query("UPDATE `ba_tz` SET `TZ`='" . serialize($arr) . "',`OBJECT`='" . $_POST['OBJECT'] . "',`DESCRIPTION`='" . $_POST['DESCRIPTION'] . "',`ID_Z`='" . (int)$_POST['id_deal'] . "',`TYPE_TZ`='" . (int)$_POST['type_tz'] . "',`DOGOVOR_NUM`='" . $_POST['DOGOVOR_NUM'] . "',`ACT_NUM`='" . (int)$_POST['ACT_NUM'] . "',`PRICE`=" . $price . ",`DATE_ACT`='" . $_POST['DATE_ACT'] . "',`EXTRANET_ID`='" . $extranet_id . "',`CONTROL_LIST`='" . ($_POST['ACT_LIST']=='3' ? '1' : '0') . "', `PROBE` = '" . serialize($probe) . "', `ATTESTAT` = " . $in_oa . " WHERE ID = " . (int)$_POST['id_tz']);
		// $res_obj = $DB->Query("UPDATE `ba_tz` SET `TZ`='" . serialize($arr) . "',`OBJECT`='" . $_POST['OBJECT'] . "',`DESCRIPTION`='" . $_POST['DESCRIPTION'] . "',`ID_Z`='" . (int)$_POST['id_deal'] . "',`TYPE_TZ`='" . (int)$_POST['type_tz'] . "',`DOGOVOR_NUM`='" . $_POST['DOGOVOR_NUM'] . "',`ACT_NUM`='" . (int)$_POST['ACT_NUM'] . "',`PRICE`='" . $price . "',`DATE_ACT`='" . $_POST['DATE_ACT'] . "',`EXTRANET_ID`='" . $extranet_id . "' WHERE ID = " . (int)$_POST['id_tz']);
		if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
		// $check_obj = $DB->Query("SELECT `ID`, `NAME` FROM `DEV_OBJECTS` WHERE `NAME`='".$_POST['OBJECT']."'")->Fetch();
		// if(!$check_obj) 
			// $res_obj = $DB->Query("INSERT INTO `DEV_OBJECTS` (`NAME`, `ID_COMPANY`) VALUES ('" . $_POST['OBJECT'] . "', '" . $deal1['COMPANY_ID'] . "')");
    } else {
        $res = $DB->Query("INSERT INTO `ba_tz`(`TZ`,`OBJECT`, `DATE_SOZD`, `ID_Z`, `TYPE_TZ`, `DOGOVOR_NUM`, `PROBE`, `ACT_NUM`, `PRICE`, `DESCRIPTION`, `DATE_ACT`, `EXTRANET_ID`, `CONTROL_LIST`, `ATTESTAT`) VALUES ('" . serialize($arr) . "','" . $_POST['OBJECT'] . "',NOW()," . (int)$_GET['ID'] . "," . (int)$_POST['type_tz'] . ",'" . $_POST['DOGOVOR_NUM'] . "', '" . serialize($probe) . "'," . (int)$_POST['ACT_NUM'] . "," . $price . ",'" . $_POST['DESCRIPTION'] . "','" . $_POST['DATE_ACT'] . "','" . $extranet_id . "', '" . ($_POST['ACT_LIST']=='3' ? '1' : '0') . "', ".$in_oa.")");
       
		if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
		// $check_obj = $DB->Query("SELECT `ID`, `NAME` FROM `DEV_OBJECTS` WHERE `NAME`='".$_POST['OBJECT']."'")->Fetch();
		// if(!$check_obj) $res_obj = $DB->Query("INSERT INTO `DEV_OBJECTS` (`NAME`, `ID_COMPANY`) VALUES ('" . $_POST['OBJECT'] . "', '" . $deal1['COMPANY_ID'] . "')");
    }
	//dump($probe);
    $ID_TZ = (!empty(((int)$_POST['id_tz'])) ? (int)$_POST['id_tz'] : $DB->LastID());
	//echo $ID_TZ;
	//echo "ARR:"; dump($arr);
	// var_dump($res_otbor);
	for ($i=0; $i<count($res_otbor['NUM_PROBE']); $i++)
	{
		for ($j=0; $j<count($res_otbor['NUM_PROBE'][$i]); $j++)
		{
			$res_all_otbor = $DB->Query("INSERT INTO `PODGOTOVKA`(`NUM_PROBE`,`GOST_ID`, `TZ_ID`, `PROBE_NAME`, `GOST_OTBOR_ID`) VALUES ('".$res_otbor['NUM_PROBE'][$i][$j]."', ".(int)$res_otbor['GOST_ID'][$i][$j].", ".(int)$ID_TZ.", '".$res_otbor['PROBE_NAME'][$i][$j]."', ".(int)$res_otbor['GOST_OTBOR_ID'][$i][$j].")");
		}
	}
	
	$id_d = !empty($_GET['EDIT']) ? (int)$res_tz['ID_Z'] : (int)$_GET['ID']; 

    if (CModule::IncludeModule('crm')) {
        $deal = new CCrmDeal;
        $arDealUpdate = array('UF_CRM_1554700718621' => $_SERVER["SERVER_NAME"] . "/tz_show.php?ID=" . $ID_TZ, 'UF_CRM_1570765979554' => $price, 'OPPORTUNITY' => $price);
        $resUpd = $deal->Update($id_d,
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    }
	
	$contract_ar = explode('ID', $_POST['DOGOVOR_NUM']);
	
	if($contract_ar[1]) 
	{
		$contract = (int)$contract_ar[1];
		$DB->Query("DELETE  FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL` = " . $id_d);
		$DB->Query("INSERT INTO `DEALS_TO_CONTRACTS` (`ID_CONTRACT`, `ID_DEAL`) VALUES (".$contract.", ".(int)$id_d.")");
	}
		
	// if (!empty($_POST['ACT_NUM'])) {
    // $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
    // $sql = $res->Fetch();
    // $probe = unserialize($sql['PROBE']);

    // $probe[$_GET['mater']] = array(
        // "zakaz_man" => $_POST['zakaz_man'],
        // "number_probe" => $_POST['number_probe'],
        // "mesto_data" => $_POST['mesto_data'],
        // "probe_proizv" => $_POST['probe_proizv'],
        // "number" => $_POST['number'],
        // "sh_number" => $_POST['sh_number'],
        // "dat" => $_POST['dat'],
    // );

    // $probe = serialize($probe);
    // $res = $DB->Query("UPDATE `ba_tz` SET `PROBE`='" . $probe . "' WHERE ID = " . (int)$_GET['TZ_ID']);

    // if (CModule::IncludeModule('crm')) {
        // $deal = new CCrmDeal;
        // $arDealUpdate = array('STAGE_ID' => 'FINAL_INVOICE');
        // $resUpd = $deal->Update((int)$_GET['ID'],
            // $arDealUpdate,
            // true,
            // true,
            // array('DISABLE_USER_FIELD_CHECK' => true)
        // );
    // }
// }

    ?>
    <table id="tz">
        <thead>
        <th>Наименование материала</th>
        <th>Кол-во проб/участков контроля</th>
        <th>Наименование показателя</th>
        </thead>
        <tbody id="tz_body">
        <?php
        $data = [];
        foreach ($arr as $key => $arrDatum) {
            $data[] = $arrDatum['mater'];
        }
        $a = array_count_values($data);

        $last_gost = '';

        foreach ($arr as $key => $arrDatum) {
            $i = $a[$arrDatum['mater']];
            ?>
            <tr>
                <?php
                if ($last_gost != $arrDatum['mater']) {
                    ?>
                    <td rowspan="<?= $i ?>"><?= $arrDatum['mater'] ?></td>
                <?php }
                $last_gost = $arrDatum['mater'];
                ?>
                <td><?= $arrDatum['obiem'] ?> <?= num2word($arrDatum['obiem'], ['проба', 'пробы', 'проб']) ?></td>
                <td>
                    <?php
                    foreach ($arrDatum['gost'] as $item) {
                        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                        echo "<p>" . $res['SPECIFICATION'] . "</p>";
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
	<br/>
    <a href="/request_card.php?ID=<?=$_POST['id_deal']?>">Вернуться в карточку заявки</a>
    |
    <a href="/<?=($_POST['ACT_LIST']=='3' ? 'control_list' : 'probe')?>.php?ID=<?= $_POST['id_deal'] ?>"><?=($_POST['ACT_LIST']=='3' ? 'Создать контрольный лист' : 'Создать акт приемки проб')?></a>
    <?php
	if (!empty($_POST['ACT_NUM'])) {echo "| <a href='/results_isp.php?ID=".$_POST['id_deal']."'>Перейти к испытаниям</a>";}
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
    exit;
}

if (!empty($_GET['EDIT'])) {
    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['EDIT']);
    $row = $res->Fetch();

    $deal = CCrmDeal::GetByID($row['ID_Z']);
    $arr = unserialize($row['TZ']);
	$probe_arr = unserialize($row['PROBE']);
	$probe_place=explode(";", $probe_arr[1]['mesto_data']);
	$company1 = CCrmCompany::GetByID(19);
	$extranet_id = CCrmCompany::GetUserFields(19);//$company1['UF_CRM_1571818346092'];
	//echo $deal['DATE_CREATE'];
	//var_dump($probe_place);// $extranet_id;
	
	$id_d = !empty($_GET['EDIT']) ? (int)$row['ID_Z'] : (int)$_GET['ID']; 
	
		if (CModule::IncludeModule('crm')) {
			$deal = new CCrmDeal;
			$arDealUpdate = array('STAGE_ID' => 'PREPARATION');
			$resUpd = $deal->Update($id_d,
				$arDealUpdate,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}
	
	//var_dump($row);
    ?>
    <form id="form_tz" method="post">
        <a href="/request_card.php?ID=<?=$row['ID_Z']?>">Назад</a> | <a href="/<?=($row['CONTROL_LIST'] ? 'control_list' : 'probe')?>.php?ID=<?= $row['ID_Z'] ?>"><?=($row['CONTROL_LIST'] ? 'Создать контрольный лист' : 'Создать акт приемки проб')?></a>
        <input type="text" name="id_deal" value="<?=$row['ID_Z']?>" hidden>
        <table id="tz"> 
        <?php
		//dump($USER->GetUserGroupArray());
        $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'));//, array('STAGE_ID' => 'PREPARATION'));
        ?>
		<!--<tr>
		<td width="250" height="40">
		
        Заявка для формирования ТЗ
		</td><td>
             <select name="id_deal" style="width:500px" >
                <option value="" disabled selected>Выберите</option>
                <?php while ($deal = $deals->Fetch()) { 
				//var_dump($deal);
				?>
                    <option data-mater="<?= implode(';', $deal['UF_CRM_1569151529509']) ?>" <?= (empty($deal['COMPANY_ID']) || !empty($deal['UF_CRM_1554700718621']) && ($deal['ID'] != $row['ID_Z'])) ? 'disabled' : '' ?> <?= ($deal['ID'] == $row['ID_Z']) ? 'selected' : '' ?>
                            value="<?= $deal['ID'] ?>"><?= $deal['DATE_CREATE'] ?> 
                        || <?= $deal['TITLE'] ?> || <?= $deal['CONTACT_FULL_NAME'] ?></option>
                <?php } 
				
				?>
            </select>
        
        </td></tr>
        <!--<hr/>-->
		
        <tr><td width="250" height="40">Объект строительства</td><td><input class="tz" multiple type="text" list="objects" name="OBJECT" size="64" value="<?= $row['OBJECT'] ?>"></td></tr>
		<tr><td width="250" height="40">Описание объекта испытаний</td><td><textarea name="DESCRIPTION" style="width:496px" required><?= $row['DESCRIPTION'] ?></textarea></td></tr>
		<tr><td width="250" height="40">Основание для проведения испытаний </td><td><input class="tz" type="text" list="contracts" name="DOGOVOR_NUM" value="<?php 
		$res_dog = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `TZ_ID`=".$_GET['EDIT'])->Fetch();
		if($res_dog['ID']) echo "Договор №".$res_dog['ID']." от ".date ('d.m.Y', strtotime($res_dog['DATE']));
		else echo ($row['DOGOVOR_NUM']); ?>" size="65"></td></tr>
		<tr><td width="250" height="40">Основание для формирования протокола </td><td><select class="tz" name="ACT_LIST">
		 <option value="1" <?=$row['CONTROL_LIST']=='0' ? 'selected' : ''?>>Акт приемки/передачи проб</option>
		 <!--<option value="2">Акт передачи проб</option>-->
		 <option value="3" <?=$row['CONTROL_LIST']=='1' ? 'selected' : ''?>>Контрольный лист</option>
		 </select>
		</td></tr>
		<tr><td width="250" height="40">Номер документа-основания для формирования протокола </td><td><input class="tz" type="number" name="ACT_NUM" value="<?= $row['ACT_NUM'] ?>" ></td></tr>
		<tr><td width="250" height="40">Дата документа-основания</td><td><input class="tz" type="date" name="DATE_ACT" value="<?= $row['DATE_ACT'] ?>" size="20"></td></tr>
		<tr><td width="250" height="40">Место отбора проб </td><td><input class="tz" type="text" name="PLACE_PROBE" value="<?= $probe_place[0] ?>" ></td></tr>
		<tr><td width="250" height="40">Дата отбора проб</td><td><input class="tz" type="date" name="DATE_PROBE" value="<?= date("Y-m-d", strtotime($probe_place[1])) ?>" size="20"></td></tr>
		</table>
       
		<br />
		<br />
        <!--<button id="add">Добавить материал</button>-->
		<?php
            foreach ($arr as $key => $arrDatum) {
                $html = "<select name='gost[]' required>";
                ?>
		<table id="tz">
		<tr>
		<td style="font-weight:bold">Материал</td>
		<td><input class="tz" type='text' name='mater[<?= $key ?>]' required value="<?= $arrDatum['mater'] ?>"></td>
        <td style="font-weight:bold">Кол-во проб/образцов</td>
		<td><input class="tz" type='number' name='obiem[<?= $key ?>]' min='1'  required value="<?= $arrDatum['obiem'] ?>"></td>
		
		</tr>
		</table>
		<br />
        <table id="tz">
            <thead>
           
            <th>Методика испытаний</th>
            <th>Тех.условия </th>
            <?php// if(in_array('8', $USER->GetUserGroupArray())) {?>
			<th>Цена</th>
			<?php //}?><!--
            <th>Норма 1</th>
            <th>Норма 2</th>
            <th>Ед. измерения</th>-->
            <th>Без цены</th>
			<th></th>
            </thead>
            <tbody id="tz_body<?=$key?>" name="tz_body">
            
				
                <!--<tr>
                  
                    <td>
                        <input  list="gost" class="gost" data-id="<?= $key ?>"></td>
						<td>
						<input list="gost_new" class="gost_new" data-id="<?= $key ?>"></td></tr>-->
                        <?php
						$g=0;
						//dump($arrDatum);
                        foreach ($arrDatum['gost'] as $item => $value) {

                            $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $value)->Fetch();
							$g_new = $arrDatum['gost_new'][$g]; //echo $g_new."__";
							
							if($g_new) $res_tu = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $g_new)->Fetch();?>
							<tr id="<?=$key?>" ><td>
							<select name="gost[<?=$key?>][]" style="width:350px" class="gost" data-id="<?=$key?>" required >
								<option value="" disabled selected>Выберите</option>
								<?php
									$ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
									while ($gost = $ba_gost->Fetch()) {
										echo "<option data-price='" . $gost['PRICE'] . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] .  "' " .($gost['ID']==$res['ID'] ? 'selected' : '') . ">" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] .  "</option>";
									}
									?>
							</select></td>
							<td>
							<select name="gost_new[<?=$key?>][]" style="width:350px" class="gost2" data-id="<?=$key?>">
								<option value="2522" selected>Не нормируется</option>
								<?php
									$ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
									while ($gost = $ba_gost->Fetch()) {
										echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] .  "' " .($gost['ID']==$res_tu['ID'] ? 'selected' : '') . ">" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] .  "</option>";
									}
									?>
							</select></td>
						<td><input type="number" name="price[<?=$key?>][]" class="oborud" value="<?=$arrDatum['price'][$g]?>"></input>
						</td>
						<td><input type="checkbox" class="dash"></td>
							<?php
							
							if(!$g) echo "<td><input style='margin-left:5px' type='button' class='add2' value='+'></td>";
							else echo "<td><input style='margin-left:5px' type='button' class='del' value='-'></td>";
						
							?>
							</tr>
							<?php
                           // echo "<tr><td><input  list='gost' class='gost' data-id='$key' ><input name='gost[$key][]' type='hidden' value='$value'><a style='float: right;' href='#' onclick='$(this).parent().remove()'>Удалить</a></td>
							//<td><input  list='gost_new' class='gost_new' data-id='$key' value='$res_tu[GOST] $res_tu[SPECIFICATION]'><input name='gost_new[$key][]' type='hidden' value='$g_new'><a style='float: right;' href='#' onclick='$(this).parent().remove()'>Удалить</a></td></tr>";
							
							$g++;
                        }
                        ?>
                   
					
                        <?php
                        // foreach ($arrDatum['gost_new'] as $item => $value) {
							// //echo $value;
							// if ($value!='--') {$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $value)->Fetch();
                            // echo "<p><label>$res[GOST] $res[SPECIFICATION]<input name='gost_new[$key][]' type='hidden' value='$value'></label><a style='float: right;' href='#' onclick='$(this).parent().remove()'>Удалить</a></p>";
											  // }
// } 
                        ?>
                    
                    
                    
                
            </tbody>
        </table>
		<br /><br />
		<?php
            }
            ?>
        <input class="tz" type="hidden" name="id_tz" value="<?= $row['ID'] ?>">
        <input type="submit" value="Сохранить" id="new_tz">
    </form>
	<br/>
  <!--  <a href="/tz_show.php">К списку ТЗ</a>
    |-->
    
    <?php
} else {
    ?>

    <!--====================================СОЗДАНИЕ НОВОГО ТЗ=========================================================================-->
    <form id="form_tz" method="post">
        <table id="tz"> 
        <?php
		
		$deal = CCrmDeal::GetByID($_GET['ID']);
		//$mater_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $ID);
		//$mater_arr = array($mater_arr['UF_CRM_1576411922643']['VALUE']);//$mater_arr['UF_CRM_1569151529509']['VALUE'];UF_CRM_1576411922643
		//$mater_arr = $mater_arr['UF_CRM_1569151529509']['VALUE'];
		$res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=".$_GET['ID']);
		while($mater=$res_mat->Fetch())
		{
			$mater_arr[]=$mater['NAME_MATERIAL'];
			$mater_arr_id[]=$mater['ID_MATERIAL'];
		}
        ?>
		<?php
        $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('STAGE_ID' => 'PREPARATION'));
        ?>
		<tr>
		<td width="250" height="40">
        Заявка для формирования ТЗ
		</td><td>
             <select onchange="top.location='/tz.php?ID='+this.value" name="id_deal" style="width:500px" required >
                <option value="" disabled selected>Выберите</option>
                <?php while ($deal = $deals->Fetch()) { 
				//var_dump($deal);
				?>
                    <option data-mater="<?= $deal['UF_CRM_1576411922643']//implode(';', $deal['UF_CRM_1569151529509']) ?>" <?= (empty($deal['COMPANY_ID'])) ? 'disabled' : '' ?> <?= ($_GET['ID'] == $deal['ID']) ? 'selected' : '' ?>
                            value="<?= $deal['ID'] ?>"><?= $deal['DATE_CREATE'] ?> 
                        || <?= $deal['TITLE'] ?> || <?= $deal['CONTACT_FULL_NAME'] ?></option>
                <?php } 
				
				?>
            </select>
        
        </td></tr>
		<?php
		
		$res = $DB->Query("SELECT * FROM `ba_tz` ORDER BY `NUM_P` DESC");
		//var_dump($res);
			?>
		<!--<tr><td width="250" height="40">Создать на основании ТЗ   <input  type="checkbox" name="TZ_COPY" style="transform:scale(1.5)" disabled>
        </td>
            <td><select name="tz_copy" id="tzc" disabled>
                <option value="0" disabled selected>Выберите</option>
                <?php while ($res1 = $res->Fetch()) { ?>
                    <option data-tz="<?= $res1['ID'] ?>" value="<?= $res1['ID'] ?>"><?php $res_tz = unserialize($res1['TZ']); $deal=CCrmDeal::GetById($res1['ID_Z']); echo 'Протокол №'.$res1['NUM_P'].', '. $res_tz[1]['mater'] . ' | ' . $deal['COMPANY_TITLE'];?></option>
                <?php } 
				//var_dump($row);
				?>
            </select>
        
        </td></tr>-->
        <tr><td width="250" height="40">Объект строительства</td><td><input class="tz" multiple list="objects" type="text" name="OBJECT"  value="-"></td></tr>
		<tr><td width="250" height="40">Описание объекта испытаний</td><td><textarea name="DESCRIPTION"  required><?= $row['DESCRIPTION'] ?></textarea></td></tr>
		<tr><td width="250" height="40">Основание для проведения испытаний </td><td><input class="tz" type="text" list="contracts" name="DOGOVOR_NUM" value="<?= ($row['DOGOVOR_NUM']) ?>" ></td></tr>
		<tr><td width="250" height="40">Основание для формирования протокола </td><td><select class="tz" name="ACT_LIST" required>
		 <option value="1" <?=$row['CONTROL_LIST']=='0' ? 'selected' : ''?>>Акт приемки/передачи проб</option>
		 <!--<option value="2">Акт передачи проб</option>-->
		 <option value="3" <?=$row['CONTROL_LIST']=='1' ? 'selected' : ''?>>Контрольный лист</option>
		 </select>
		</td></tr>
		<tr><td width="250" height="40">Номер документа-основания для формирования протокола </td><td><input class="tz" type="number" name="ACT_NUM" value=""></td></tr>
		<tr><td width="250" height="40">Дата документа-основания </td><td><input class="tz" type="date" name="DATE_ACT" value="<?= date("Y-m-d") ?>" ></td></tr>
		<tr><td width="250" height="40">Место отбора проб </td><td><input class="tz" type="text" name="PLACE_PROBE" value="" required ></td></tr>
		<tr><td width="250" height="40">Дата отбора проб</td><td><input class="tz" type="date" name="DATE_PROBE" value="<?= date("Y-m-d") ?>" size="20" ></td></tr>
		</table>
		<br />
		<?php
		//dump($mater_arr);
        foreach ($mater_arr as $key => $arrDatum) {
        //$nameMaterial =  $DB->Query("SELECT * FROM `MATERIALS` WHERE `ID`=".(int)$arrDatum)->Fetch();      
                ?>
		<table id="tz">
		<tr>
		<td style="font-weight:bold">Материал</td>
		<td><input class="tz" type='text' name='mater[<?= $key ?>]' required value="<?=$arrDatum?>"></td>
        <td style="font-weight:bold">Кол-во проб/образцов</td>
		<td><input class="tz" type='number' name='obiem[<?= $key ?>]' min='1' value="1" required value=""></td>
		
		</tr>
		</table>
		<br />
        <table id="tz">
            <thead>
           
            <th>Методика испытаний</th>
            <th>Тех.условия </th>
            <th>Цена</th>
            <th>Без цены</th>
			<th></th>
            </thead>
            <tbody id="tz_body" name="tz_body">
            <?php 
			if($mater_arr_id[$key]>0) 
			{
				$metodicList =  $DB->Query("SELECT * FROM `ba_gost` WHERE `DOP`=".(int)$mater_arr_id[$key]);
				$g=0;
				while($metodic=$metodicList->Fetch())
				{
					if(!$metodic['NON_ACTUAL'])
					{
					?>
					
							<tr id="<?=$key?>" ><td>
								<select name="gost[<?=$key?>][]" style="width:350px" class="gost" data-id="<?=$key?>" required>
									<option value="" disabled selected>Выберите</option>
									<?php
										$ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
										while ($gost = $ba_gost->Fetch()) {
											echo "<option data-price='" . $gost['PRICE'] . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] .  "' ".($gost['ID']==$metodic['ID'] ? 'selected' : '').">" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] .  "</option>";
										}
										?>
								</select></td>
								<td>
								<select name="gost_new[<?=$key?>][]" style="width:350px" class="gost2" data-id="<?=$key?>">
									<option value="2522" selected>Не нормируется</option>
									<?php
										$ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
										while ($gost = $ba_gost->Fetch()) {
											echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] .  "'>" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] .  "</option>";
										}
										?>
								</select></td>
								<td><input name="price[<?=$key?>][]" class="oborud" value="<?= $metodic['PRICE'] ?>" required></input></td>
								<td><input type="checkbox" class="dash"></td>
								<?php
							if(!$g) echo "<td><input style='margin-left:5px' type='button' class='add2' value='+'></td>";
							else echo "<td><input style='margin-left:5px' type='button' class='del' value='-'></td>";
							$g++;	
								
								?>
								</tr>
				<?php
					}
				 } 
			}
			else
			{ ?>
				<tr id="<?=$key?>" ><td>
								<select name="gost[<?=$key?>][]" style="width:350px" class="gost" data-id="<?=$key?>" required>
									<option value="" disabled selected>Выберите</option>
									<?php
										$ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
										while ($gost = $ba_gost->Fetch()) {
											echo "<option data-price='" . $gost['PRICE'] . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] .  "' >" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] .  "</option>";
										}
										?>
								</select></td>
								<td>
								<select name="gost_new[<?=$key?>][]" style="width:350px" class="gost2" data-id="<?=$key?>">
									<option value="2522" selected>Не нормируется</option>
									<?php
										$ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
										while ($gost = $ba_gost->Fetch()) {
											echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] .  "'>" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] .  "</option>";
										}
										?>
								</select></td>
								<td><input name="price[<?=$key?>][]" class="oborud" value="" required></input></td>
								<td><input type="checkbox" class="dash"></td>
								<?php
								echo "<td><input style='margin-left:5px' type='button' class='add2' value='+'></td>";
								
								
								?>
								</tr>
				
			<? } ?>
                    
                
            </tbody>
        </table>
		<br /><br />
		<?php
            }
            ?>
        <input class="tz" type="hidden" name="id_tz" value="<?= $row['ID'] ?>">
		
        <input type="submit" value="Сохранить" id="new_tz">
    </form>
       
       
<?php } (isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';?>
	<script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
	
   <script>
		
	
        function gost() {
            $(".gost").change(function () {
                td = $(this).parent();
                tr = $(this).parent().parent();
                id = $(this).data('id');
				
				
                td.append("<p><label>"  + $('#gost option[value=' + $(this).val() + ']').text() +
                    "<input class='g_met' name='gost[" + id + "][]' type='hidden' value='" + $(this).val() +
                    "'></label>"+"&nbsp;"+"<a style='float: right;' href='#' onclick='del(this)'>X</a></p>");
              
					$(this).val('');//}
				
            })
        }
		function gost_new() {
            $(".gost_new").change(function () {
                td = $(this).parent();
                tr = $(this).parent().parent();
                id = $(this).data('id');
	
				td.append("<p><label>" + $('#gost_new option[value=' + $(this).val() + ']').text() +
                    "<input class='g_new' name='gost_new[" + id + "][]' type='hidden' value='" + $(this).val() +
                    "'></label>"+"&nbsp;"+"<a style='float: right;' href='#' onclick='del(this)'>X</a></p>");
					//console.log("!"+$(this).val());
					$(this).val('');// }
               
            })
        }
		
        function add(mater = '') {
            if (!isNaN($('.gost:last').data('id'))) {
                last_gost = $('.gost:last').data('id');
            } else {
                last_gost = 0;
            }
			//console.log(last_gost);
            $("#tz_body").append("<tr data-name='row_" + (last_gost + 1) + "'>" +
                //"<tr>
                "<td data-name='gost' valign='top'>      <input list='gost' class='gost' data-id='" + (last_gost + 1) + "'></td>" +
                "<td data-name='gost_new' valign='top'>  <input list='gost_new' class='gost_new' data-id='" + (last_gost + 1) + "'></td>"  +
				"<td></td>"+
                // "<td data-name='gost_new_desc'>  </td>" +
                // "<td data-name='norm_1'>    </td>" +
                // "<td data-name='norm_2'>    </td>" +
                // "<td data-name='ed_izm'>    </td>" +
                "<td data-name='a_href'>    <a href='#' onclick='$(this).parent().parent().remove()'>Удалить</a></td></tr>");
            //gost(); gost_new();
			
        }
        $(document).ready(function () {
            $("#add").click(function () {
                add();
            })
			
						
            $("select[name=id_deal]").change(function () {
                optionSelected = $("option:selected", this);
                maters = optionSelected.data('mater').split(';');
                $.each(maters, function (index, value) {
                    if (value) {
                        console.log("INDEX: " + index + " VALUE: " + value);
                        add(value);
                    }
                });
            })
			
            //gost(); gost_new();
        })
		
		
    </script>
	<script>
	$(document).ready(function () {
	$("tbody[name=tz_body]").on("click", ".del", function () {
		  $(this).parent().parent().remove();
		});
		
	$("tbody[name=tz_body]").on("click", ".add2", function () {
		   tbody = $(this).parent().parent().parent();
			tr = $(this).parent().parent();
			tr_dataid = tr.attr('id');
			console.log(tr_dataid);
			$.ajax({

			  url: 'select_for_tz.php?KEY='+tr_dataid,

			  success: function(data) {

				tbody.append(data);

			  }
			
			});
			
			setTimeout(function() {
      $("tbody[name=tz_body]").find(".gost_d").each(function() {
		//console.log($(this));
	  $(this).select2();
	  });
	  $("tbody[name=tz_body]").find(".gost_d2").each(function() {
		//console.log($(this));
	  $(this).select2();
	  });
    }, 1000);
		
	});
	
	$('#form_tz').on('click', function(event) {
	
		if ($('#new_tz').attr('id') == event.target.id) {
			$.each($('.oborud'), function(i, v) {
					if ($(v).val() != 0 && $(v).val() != '') {
						return true;
					}
				var tr = $(v).closest('tr');
				var checkbox = tr.find('.dash');
				
				if (!checkbox.prop('checked')) {
				alert('Пожалуйста, укажите стоимость или поставьте отметку об отсутствии цены.');
				event.preventDefault();
				return false;
				}
			});
		}
	});

	$("tbody[name=tz_body]").on("change", ".gost", function() {
		optionSelected = $("option:selected", this);
        price = optionSelected.data('price');
		tr = $(this).parent().parent();
		price_input = tr.find(".oborud");
		price_input[0].value = price;
		
		tr.find(".oborud").css('display', 'block');
		tr.find(".oborudDash").css('display', 'none');
		
		tr.find(".dash").prop('checked',false);
	});
	
	/*$("tbody[name=tz_body]").on("change", ".gost_d", function() {
		optionSelected = $("option:selected", this);
        price = optionSelected.data('price');
		tr = $(this).parent().parent();
		price_input = tr.find(".oborud");
		//console.log(price_input[0].value);
		price_input[0].value = price;
		console.log(price);
		//alert('111');
	});*/
	
	$("tbody[name=tz_body]").on("change", ".gost_d", function() {
		optionSelected = $("option:selected", this);
        price = optionSelected.data('price');
		tr = $(this).parent().parent();
		price_input = tr.find(".oborud");
		price_input[0].value = price;
		
		tr.find(".oborud").css('display', 'block');
		tr.find(".oborudDash").css('display', 'none');
		
		
		tr.find(".dash").prop('checked',false);
	});
	
	
	
	
	$('tbody[name=tz_body]').on('click', '.dash', function() {
		var tr = $(this).closest('tr');
		if (tr.find('.dash').prop("checked")) {
			price = tr.find('.oborud').val();
		} else {
			price = tr.find('.oborud').data("price_prev");
		}

		tr.find('.oborud').data('price_prev', price);
		var price_prev = price;
		
		if (tr.find(".dash").prop("checked")) {
			if (tr.find('div').is( ".oborudDash" )) {
				tr.find(".oborudDash").css('display', 'block');
			} else {
				tr.find(".oborud").after( "<div class='oborudDash'>-</div>" );
			}
			tr.find(".oborud").css('display', 'none');
			tr.find(".oborud").val('0');
		} else {
			tr.find(".oborud").css('display', 'block');
			tr.find(".oborud").val(price_prev);
			tr.find(".oborudDash").css('display', 'none');
		}

	});
	
	})
 </script>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>