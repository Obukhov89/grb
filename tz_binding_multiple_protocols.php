<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Формирование технического задания");

include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";


include $_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php';
$DB_Conn = new CDB();

?>
    <link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>

    <script>
        $(document).ready(function () {
            $('select[name=DOGOVOR_NUM]').select2();

            $spanSelect2 = $('select[name=DOGOVOR_NUM]').closest('td').find('.select2-selection.select2-selection--single');
            $spanSelect2.css({
                'height': '40px',
                'padding-top': '1.5%',
                'background-color': '#f9f9f9'
            });

            $arrowSelect2 = $('select[name=DOGOVOR_NUM]').closest('td').find('.select2-selection__arrow');
            $arrowSelect2.css('padding-top', '3.5%');
        });
    </script>
    <script>
        
        $(document).ready(function () {

            $('.usersCompany').select2();
            $spanSelect2 = $('.usersCompany').closest('.companyEmployees').find('.select2-selection.select2-selection--single');
            $spanSelect2.css({
                'width': '470px'
            });
        });
    </script>

    <script>

        let flag = 1;
        $(document).ready(function () {

            $('#form_tz').on('mouseenter', '.tdGost', function () {
                
                $(this).find('.styleGost').select2();
            });

            $('#form_tz').on('mouseleave', '.tdGost', function () {
                var focusedName = document.activeElement.className;

                if (focusedName == 'select2-search__field') {
                    return true;
                }

                $(".styleGost.select2-hidden-accessible").select2('destroy');
            });

            
            $('#form_tz').on('change', '.styleGost', function (i, obj) {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    setTimeout(function () {
                        $(".styleGost.select2-hidden-accessible").select2('destroy');
                    }, 0);
                }
            });

            
            $('#form_tz').on('focusin input', function (event) {
                var targetElement = event.target;
                if (!$(targetElement).is(".select2-selection")) {
                    $(".styleGost.select2-hidden-accessible").select2('destroy');
                }
            });

        });
    </script>

    <style type="text/css">
        #tzc, #tzc option {
            width: 500px;
            margin: 0;

        }

        input[type="submit"]:disabled {

            background-color: #C0C0C0;
            border: 1px solid #C0C0C0;
            cursor: default;
        }

        input[type="button"]:disabled {

            background-color: #C0C0C0;
            border: 1px solid #C0C0C0;
            cursor: default;
        }

        .add3 {
            margin-left: 5px!important;
            background-color: rgb(59 245 130)!important;
            border: 1px solid rgb(59, 245, 130)!important;
            display: inline-block!important;
        }

        .del2{
            margin-left: 5px!important;
            background-color: rgb(59 245 130)!important;
            border: 1px solid rgb(59, 245, 130)!important;
            display: inline-block!important;
        }
        .label_del2{
            position: relative!important;
        }
        .helper{
            width: 175px!important;
            position: absolute!important;
            top: 0%!important;
            left: 230%!important;
            height: 40px!important;
        }

        .styleGostNew {
            width: 342px;
            height: 25px!important;
            padding: 1px 0px 0px 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            border-radius: 5px;
            color: #444;
            font-size: 14px;
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            border-color: rgb(170, 170, 170);
            display: block;
            max-width: 500px;
            height: 40px;
            border: 1px solid #ccc;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
            background: #f9f9f9;
        }
    </style>


    <datalist id="gost">
        <?php
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
        while ($gost = $ba_gost->Fetch()) {
            echo "<option data-type='" . $gost['NORM_TEXT'] . "' " . (($arrDatum['gost'] == $gost['ID']) ? 'selected' : '') . " value='" . $gost['ID'] . ' | ' . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "'></option>";
        }
        ?>
    </datalist>

    <datalist id="gost_new">
        <?php
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
        while ($gost = $ba_gost->Fetch()) {
            echo "<option data-type='" . $gost['NORM_TEXT'] . "' " . (($arrDatum['gost'] == $gost['ID']) ? 'selected' : '') . " value='" . $gost['ID'] . "'>" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
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

    <datalist id="materials">
        <?php
        $materialsArr = [];
        $ba_obj = $DB->Query("SELECT * FROM `MATERIALS`");
        while ($obj = $ba_obj->Fetch()) {
            echo "<option data-id='" . $obj['ID'] . "' value='" . $obj['NAME'] . ', ID ' . $obj['ID'] . "'></option>";
            $materialsArr[$obj['ID']] = $obj['NAME'];
        }


        ?>
    </datalist>
	<input type="hidden" name="id_user" id= "id_user" value="<?= $USER->GetID() ?>">
<?php
$company1 = '';
$extranet_id = '';
if (!empty($_POST['tz_copy']) && isset($_POST['TZ_COPY']) && $_POST['TZ_COPY'] == 'on') {

    $ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE ID=" . $_POST[tz_copy])->Fetch();

    $res = $DB->Query("INSERT INTO `ba_tz`(`TZ`,`OBJECT`, `DATE_SOZD`, `ID_Z`, `DOGOVOR_NUM`, `ACT_NUM`, `DESCRIPTION`, `DATE_ACT`) VALUES ('" . $ba_tz['TZ'] . "','" . $_POST['OBJECT'] . "',NOW()," . (int)$_GET['ID'] . ",'" . $_POST['DOGOVOR_NUM'] . "'," . (int)$_POST['ACT_NUM'] . ",'" . $_POST['DESCRIPTION'] . "','" . $_POST['DATE_ACT'] . "')");
    if ($res->result = 1) {
        echo "<h3>Успешно</h3>";
    } else {
        echo "<h3>Ошибка</h3>";
    }

    $arr = unserialize($ba_tz['TZ']);

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
    <a href="/<?= ($_POST['ACT_LIST'] == '3' ? 'control_list' : 'probe') ?>.php?ID=<?= $_POST['id_deal'] ?>"><?= ($_POST['ACT_LIST'] == '3' ? 'Создать контрольный лист' : 'Создать акт приемки проб') ?></a>
    <?php
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
    exit;
}

if (!empty($_POST['mater'])) {

	$getEditIdTz = !empty($_GET['EDIT']) ? (int)$_GET['EDIT'] : 0; 
	$actNum = !empty($_POST['ACT_NUM']) ? $_POST['ACT_NUM'] : 0; 


    if (isset($_POST['dogovorNumHidden']) && $_POST['dogovorNumHidden'] == '0') {
        $_POST['DOGOVOR_NUM'] = $_POST['dogovorNumHidden'];

        $getNum = null;
        if (!empty($_GET['ID'])) {
            $getNum = !empty($_GET['ID']) ? (int)$_GET['ID'] : '';
        } else if (!empty($_GET['EDIT'])) {
            $getNum = !empty($_GET['EDIT']) ? (int)$_GET['EDIT'] : '';
        }
    }

    $arrData = [];
    $probe = [];
    $attestat = array();

    if ($_GET['EDIT']) {
        $res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . $_GET['EDIT'])->Fetch();
        $probe_edit = unserialize($res_tz['PROBE']);

    }

	if (!empty($_GET['ID'])) {
        $res_tz_get_id = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID'])->Fetch();
    }

    $deal1 = CCrmDeal::GetByID($_GET['ID']);
    $y_date = date("Y", strtotime($_POST['DATE_ACT']));
    if ($y_date != '2020') $y_date = "/" . substr($y_date, 2);
    else $y_date = "/" . $y_date;

    $sh = 1;
    $otbor = 0;
    $key_p = 1;

    foreach ($_POST['mater'] as $keyMater => $valMater) {
        $mat_ar = explode(', ID', $_POST['mater'][$keyMater]);

        if ($mat_ar[1]) {
            $ar_material_id[$keyMater] = (int)$mat_ar[1];
            $ar_material[$keyMater] = $mat_ar[0];
        } else {
            $ar_material[$keyMater] = $_POST['mater'][$keyMater];
            $ar_material_id[$keyMater] = 0;
        }
    }

    $dealUp = CCrmDeal::GetByID($res_tz['ID_Z']);

    $updDeal = CCrmDeal::GetByID($_GET['ID']);

    $id_deal = (isset($_GET['EDIT'])) ? $dealUp['ID'] : $updDeal['ID'];
	

	$current_deal = null;
	if (!empty($dealUp)) {
		$current_deal = $dealUp;
	} elseif(!empty($updDeal)) {
		$current_deal = $updDeal;
	}

	if (!empty($_POST['inputListRequest'])) {
		
		$arrayDataRequest = explode('ID', $_POST['inputListRequest']);
		$idRequestExport = !empty($arrayDataRequest[1]) ? trim($arrayDataRequest[1]) : '';

		$resultSelectBaTzDataExport = null;
		if (!empty($idRequestExport)) {
			$resultSelectBaTzDataExport = $DB->Query('SELECT * FROM `ba_tz` WHERE `ID`=' . (int)$idRequestExport)->Fetch();
		}

		
		$idExportRequest = !empty($resultSelectBaTzDataExport['ID_Z']) ? (int)$resultSelectBaTzDataExport['ID_Z'] : '';

		
		$dataRequestExport = CCrmDeal::GetByID($idExportRequest);
		$companyIdExport = !empty($dataRequestExport['COMPANY_ID']) ? $dataRequestExport['COMPANY_ID'] : ''; //id Компании


		if (CModule::IncludeModule('crm')) {
			$requestUpdateImport = new CCrmDeal;
			$arrayUpdateImport = [
				"COMPANY_ID" => $companyIdExport
			];
			$resultUpdateImport = $requestUpdateImport->Update($current_deal['ID'],
				$arrayUpdateImport,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}

		
		$companyTitleDealEdit = !empty($dataRequestExport['COMPANY_TITLE']) ? $DB->ForSql($dataRequestExport['COMPANY_TITLE']) : '';

		
		$checkIp = !empty($resultSelectBaTzDataExport['CHECK_IP']) ? $resultSelectBaTzDataExport['CHECK_IP'] : '';

		
		if (!empty($current_deal['ID'])) {
			$resultUpdateImport = $DB->Query("UPDATE `ba_tz` SET `COMPANY_TITLE`= '" . $company_title_deal_edit . "', `CHECK_IP`= '" . $checkIp . "', `IMPORT_TZ_ID_VLK`= " . $idRequestExport . " WHERE `ID_Z`=" . (int)$current_deal['ID']);
		}

		
		$resultSelectActVlk = null;
		if (!empty($getEditIdTz)) {
			$resultSelectActVlk = $DB->Query('SELECT * FROM `AKT_VLK` WHERE `tz_id`=' . $getEditIdTz)->Fetch();
		}

		if (empty($resultSelectActVlk['id']) && !empty($getEditIdTz)) { //Если нет записи по этой заявке(сделки), то создаем
			$resultInsertAktVlk = $DB->Query('INSERT INTO `AKT_VLK` (`tz_id`, `act_number`) VALUES (' . $getEditIdTz . ', ' . $actNum . ')');
		} else if (!empty($resultSelectActVlk['id']) && !empty($getEditIdTz)) { //Если есть записи по этой заявке , то обновляем

			$resultUpdateAktVlk = $DB->Query('UPDATE `AKT_VLK` SET `act_number`=' . $actNum . ' WHERE `tz_id`=' . $getEditIdTz);
		}
	}



	
	$extranet_id = !empty($current_deal['COMPANY_ID']) && $current_deal['COMPANY_ID'] == 1184 ? 56 : '';

    $res = $DB->Query("delete from `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = " . (int)$id_deal);

    foreach ($ar_material_id as $key => $ar_m) {
        $DB->Query("INSERT INTO `MATERIALS_TO_REQUESTS` (`ID_DEAL`, `ID_MATERIAL`, `NAME_MATERIAL`) VALUES (" . (int)$id_deal . ", " . (int)$ar_m . ",'" . $ar_material[$key] . "')");
    }


		$mater_arr_up=array();
		$res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=" . (int)$id_deal);
		while($mater=$res_mat->Fetch())
		{
			$mater_arr_up[]=$mater['NAME_MATERIAL'];
		}
		$dealMaterials = ($deal_new['UF_CRM_1576411922643'] ? $deal_new['UF_CRM_1576411922643'] : implode(', ', $deal_new['UF_CRM_1569151529509']));
		$dealMaterials = ($dealMaterials  ? $dealMaterials : implode(', ', $mater_arr_up));
   
    $insert_id_gost = [];
// при создании новой ТЗ или при редактировании ТЗ всегда только вставка нового госта, обновление госта не предусмотрено
    if(!empty($_GET['EDIT']) || !empty($_GET['ID'])) {
    // добавление госта, если написали новый гост в поле методики
    // проверка на лабораторию
        $laba = (!empty($_REQUEST['labaMethodic']) ? $_REQUEST['labaMethodic'] : '' );
        // в зависимости от выбранной лаборотии вставляем главного отественного в гост
        // $sel_laba = $laba[0];
        // switch($sel_laba) {
        //     case 0:
        //         $assign_user = 15;
        //         break;
        //     case 1:
        //         $assign_user = 11;
        //         break;
        //     case 2:
        //         $assign_user = 58;
        //         break;
        //     case 3:
        //         $assign_user = 13;
        //         break;
        //     case 4:
        //         $assign_user = 43;
        //         break;
        // }
        $count = ($_REQUEST['gost1']);
        $sqlTot2 = "INSERT INTO `ba_gost` (SPECIFICATION, PRICE, ED, NAME_GOST, NORM_COMMENT, ACCURACY, GOST, GOST_PUNKT, GOST_OBJECT, GOST_DOC, GOST_VAL, NORM1, NORM2, NORM1_TEXT, NORM2_TEXT, IN_OA, NON_ACTUAL, LFMI, LFHI, LSM, DSL, OSK) VALUES({?}, {?}, '-', '-', '-', 0, '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '0', {?}, {?}, {?}, {?}, {?})";
        // $sqlTot2 = "INSERT INTO `ba_gost` (SPECIFICATION, PRICE, ED, NAME_GOST, NORM_COMMENT, ACCURACY, GOST, GOST_PUNKT, GOST_OBJECT, GOST_DOC, GOST_VAL, NORM1, NORM2, NORM1_TEXT, NORM2_TEXT, IN_OA, NON_ACTUAL, ASSIGNED, LFMI, LFHI, LSM, DSL, OSK) VALUES({?}, {?}, '-', '-', '-', 0, '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '0', {?}, {?}, {?}, {?}, {?}, {?})";
        $i = 0;
        foreach($_REQUEST['gost1'] as $kk_id => $vv_id) {
            // pre($vv_id);
            foreach($vv_id as $k_v1 => $v_v1) {
                $paramArr3 = array($v_v1, $_REQUEST['price1'][$kk_id][$k_v1]);
                $laba1 = $laba[$i];
                for($index = 0; $index < 5; $index++) {
                    // по мере того какая выбрана лаборатория кладем 0 или 1
                    // 1 в случае выбранной
                    if($laba1 == $index){
                        array_push($paramArr3, 1);
                    }else{
                        array_push($paramArr3, 0);
                    }
                }
                try{
                    $ins_gost = $DB_Conn->queryExec($sqlTot2, $paramArr3);
                    $last_id = $DB_Conn->select("SELECT max(ID) from `ba_gost`;");
                    array_push($insert_id_gost, $last_id[0]['max(ID)']);
                }catch(Exception $e) {
                    // пробрасываем ошибку т.к. если вылетелеа ошибка, то данный гост уже есть в бд и вылезет MYSQL Error - Duplicate entry key 'x'
                    // echo($e);
                    // echo('<br>');
                    $flag = 1;
                }
                $i++;
            }
        }
    }

    if(!empty($_GET['EDIT']) || !empty($_GET['ID'])) {
        $comment_tz = !empty($_POST['comment_to_tz']) ? $_POST['comment_to_tz'] : '';
        $comment_kp = !empty($_POST['comment_to_kp']) ? $_POST['comment_to_kp'] : '';

        if(!empty($comment_tz) || !empty($comment_kp)){
            $getNum = ( !empty($_GET['EDIT']) ? $_GET['EDIT'] : ( !empty($_GET['ID']) ?  $_GET['ID'] : '') );

            $sql_comment_update = "UPDATE ba_tz SET COMMENT_TZ = {?}, COMMENT_KP = {?} WHERE ID = {?}";
            $sql_arr_comment_update = array($comment_tz, $comment_kp, $getNum);
            $update_comm = $DB_Conn->queryExec($sql_comment_update, $sql_arr_comment_update);
        }
    }
    $price_i = 0;
    $index = 0;

    $checked_probe_customer = (!empty($_POST['SELECTION_TYPE']) ? $_POST['SELECTION_TYPE'] : '');
    if($checked_probe_customer == 'on'){
        $otbor_proizv = (!empty($_POST['probe_proizv']) ? $_POST['probe_proizv'] : '');
    }else{
        $otbor_proizv = 'заказчик';
    }
    foreach ($_POST['mater'] as $key => $item) {

       

        if(!empty($_REQUEST['gost'][$key])) {
            foreach($_REQUEST['gost'][$key] as $key_item => $val_item) {
                $arrData[$key][$_POST['mater'][$key]][$key]['gost'][] = $val_item;
                $arrData[$key][$_POST['mater'][$key]][$key]['price'][] = $_POST['price'][$key][$key_item];
                $arrData[$key][$_POST['mater'][$key]][$key]['gost_new'][] = $_REQUEST['gost_new'][$key][$key_item];
            }
        }

        
        $arrData[$key][$_POST['mater'][$key]][$key]['obiem'] = $_POST['obiem'][$key];
        
        if(!empty($_REQUEST['ID'])){
            if(!empty($_REQUEST['gost1'])) {
                $index1 = 0;
                foreach($_REQUEST['gost1'][$key] as $key_gost1 => $val_gost1) {

                    $arrData[$key][$_POST['mater'][$key]][$key]['gost'][] = $insert_id_gost[$index];
                    $arrData[$key][$_POST['mater'][$key]][$key]['price'][] = $_REQUEST['price1'][$key][$key_gost1];
                    $arrData[$key][$_POST['mater'][$key]][$key]['gost_new'][] = $_REQUEST['gost_new1'][$key][$key_gost1];
                    $index = $index + 1;
                    $index1++;
                    $price_i++;
                }
            }
        }

        if(!empty($_REQUEST['EDIT'])){
            if(!empty($_REQUEST['gost1'])) {
                foreach($_REQUEST['gost1'][$key] as $key_gost1 => $val_gost1) {
                    $arrData[$key][$_POST['mater'][$key]][$key]['gost'][] = $insert_id_gost[$index];
                    $arrData[$key][$_POST['mater'][$key]][$key]['price'][] = $_REQUEST['price1'][$key][$key_gost1];
                    $arrData[$key][$_POST['mater'][$key]][$key]['gost_new'][] = $_REQUEST['gost_new1'][$key][$key_gost1];
                    $index = $index + 1;
                    $price_i++;
                }
            }
        }


        
        $probe[$key_p] = array(
            "zakaz_man" => $deal1['COMPANY_ID'],
            "number_probe" => [],
            "probe_proizv" => $otbor_proizv,
            "number" => $_POST['ACT_NUM'],
            //"sh_number" => [],
            "dat" => '',
        );

        if (isset($_POST['ACT_NUM']) && $_POST['ACT_NUM'] != '0') {
            $dateProbe = !empty($_POST['DATE_PROBE']) ? date("d.m.Y", strtotime($_POST['DATE_PROBE'])) : '-';
            $placeProbe = !empty($_POST['PLACE_PROBE']) ? $_POST['PLACE_PROBE'] : '-';
            $probe[$key_p]["mesto_data"] = $placeProbe . '; ' . $dateProbe;
        }

        for ($l = 0; $l < (int)$_POST['obiem'][$key]; $l++) {

            $probe[$key_p]["number_probe"][$l] = $item;
            if (!empty($_POST['ACT_NUM']) && $_POST['ACT_NUM'] != 0) {
                $probe[$key_p]["sh_number"][$l] = $probe_edit[$key_p]['sh_number'][$l] ? $probe_edit[$key_p]['sh_number'][$l] : $_POST['ACT_NUM'] . '.' . (int)($sh) . $y_date;
            }

            $sh++;
        }
        $key_p++;
    }

    $res_otbor = array();
    $i = 0;
    $price = 0;


   
    foreach ($arrData as $keys => $arrDatums) {

        foreach ($arrDatums as $key => $arrDatum) {

            foreach ($arrDatum as $item) {

               
                $i++;
                $arr[$i]['mater'] = $key;
                $arr[$i]['gost'] = $item['gost'];
                $arr[$i]['obiem'] = $item['obiem'];
                $arr[$i]['gost_new'] = $item['gost_new'];
                $arr[$i]['price'] = $item['price'];
                $otbor = 0;

                foreach ($item['gost'] as $g => $gost) {
                    $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $gost)->Fetch();

                    if ($res['GOST_TYPE'] == 'metodic_otbor') $otbor = $res['ID'];

                }

                if ($otbor) {
                    $pp = 0;
                    foreach ($probe[$i]['sh_number'] as $p => $probe_i) {
                        $gg = 0;
                        foreach ($item['gost'] as $g => $gost) {
                            $check_otbor = $DB->Query("SELECT `ID` FROM `PODGOTOVKA` WHERE `NUM_PROBE`='" . $probe_i . "' AND `GOST_ID`=" . $gost)->Fetch(); //ЗДЕСЬ ПРОВЕРЯЕМ ПО НОМЕРУ ГОСТА И НОМЕРУ ПРОБЫ!! ЕСЛИ ТАКАЯ ЗАПИСЬ УЖЕ ЕСТЬ - АПДЕЙТ. ИНАЧЕ ИНСЕРТ

                            if (!$check_otbor) {
                                $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $gost)->Fetch();

                                if ($res['GOST_TYPE'] != 'metodic_otbor') {
                                    $res_otbor['NUM_PROBE'][$pp][$gg] = $probe_i;
                                    $res_otbor['GOST_ID'][$pp][$gg] = $gost;
                                    $res_otbor['PROBE_NAME'][$pp][$gg] = $probe[$i]['number_probe'][0];
                                    $res_otbor['GOST_OTBOR_ID'][$pp][$gg] = $otbor;
                                    $gg++;
                                }

                            }
                        }
                        $pp++;

                    }


                }
            }
            
        }
        
    }


    for ($i = 1; $i <= count($arr); $i++)
        for ($j = 0; $j < count($arr[$i]['gost']); $j++) {
            $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $arr[$i]['gost'][$j])->Fetch();
            if (!$res['IN_OA'] && $res['ID'] != 2875) 
                $attestat[] = $res['ID'];

            $price = $price + ($arr[$i]['price'][$j]) * $arr[$i]['obiem'];

        }

    $in_oa = 1;
    if (count($attestat)) $in_oa = 0;
	

    $contract_array = explode('ID', $_POST['DOGOVOR_NUM']);
    
    $dogovor_table = !empty($contract_array[0]) ? $contract_array[0] : '';


	$result_select_ba_tz = null;
	if (!empty($_GET['ID'])) { 
		$result_select_ba_tz = $res_tz_get_id;
	} elseif (!empty($_GET['EDIT'])) { 
		$result_select_ba_tz = $res_tz;
	}

	
    $deadline_table = !empty($result_select_ba_tz['DEADLINE_TABLE']) ? $result_select_ba_tz['DEADLINE_TABLE'] : date('d.m.Y H:i:s');
    
	$deadline_table_up = !empty($_POST['DEADLINE_PROTOCOL']) ? date('d.m.Y H:i:s', strtotime($_POST['DEADLINE_PROTOCOL'])) : $deadline_table;
    if (!empty($_POST['DATE_ACT'])) {
        $y_date_act = date("Y", strtotime($_POST['DATE_ACT']));
        if ($y_date_act != '2020') {
            $y_date_act = "/" . substr($y_date_act, 2);
        } else {
            $y_date_act = "/" . $y_date_act;
        }
    }

    $num_act = (!empty($_POST['ACT_NUM']) ? $_POST['ACT_NUM'].$y_date_act : '');

    $checked_probe_customer = (!empty($_POST['SELECTION_TYPE']) ? $_POST['SELECTION_TYPE'] : '');
    if($checked_probe_customer == 'on'){
        $txt_checked = 1;
    }else{
        $txt_checked = '';
    }
    if (!empty($_POST['id_tz'])) {
        $deadlineProtocol = $_POST['DEADLINE_PROTOCOL'];
		
        // переписанный запрос на обновление ba_tz на класс CDB (в нём учитывается экранирование символов ('), (\), (") и т.д.)
        $sql_text_update_tz = "UPDATE `ba_tz` SET `TZ` = {?}, `OBJECT` = {?}, `DESCRIPTION` = {?}, `ID_Z` = {?}, `ACT_NUM` = {?}, `PRICE` = {?}, `DATE_ACT` = {?}, `EXTRANET_ID` = {?}, `CONTROL_LIST` = {?}, `PROBE` = {?}, `DEADLINE` = {?}, `DEADLINE_TABLE` = {?}, `MATERIAL` = {?}, `NUM_ACT_TABLE` = {?}, `ATTESTAT` = {?}, `SELECTION_TYPE` = {?} WHERE `ID` = {?}";
        $pararm_arr_update_tz = array(serialize($arr), $_POST['OBJECT'], $_POST['DESCRIPTION'], (int)$_POST['id_deal'], (int)$_POST['ACT_NUM'], $price, $_POST['DATE_ACT'], $extranet_id, ($_POST['ACT_LIST'] == '3' ? '1' : '0'), serialize($probe), $deadlineProtocol, $deadline_table_up, $dealMaterials, $num_act, $in_oa, $txt_checked, (int)$_POST['id_tz']);
        $update_tz = $DB_Conn->queryExec($sql_text_update_tz, $pararm_arr_update_tz);

        if ($update_tz->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }

		
    } else {
        $deadlineProtocol = $_POST['DEADLINE_PROTOCOL'];
        $checked_probe_customer = (!empty($_POST['SELECTION_TYPE']) ? $_POST['SELECTION_TYPE'] : '');

        if($checked_probe_customer == 'on'){
            $txt_checked = 1;
        }else{
            $txt_checked = '';
        }
		$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $_GET['ID'])->Fetch();
        
		if (isset($res_tz['TZ'])) { 
			
            // переписанные на класс CDB

            $update_ba_tz_sql = "UPDATE `ba_tz` SET `TZ` = {?}, `OBJECT` = {?}, `DATE_SOZD` = {?}, `DESCRIPTION` = {?}, `ID_Z` = {?}, `ACT_NUM` = {?}, `PRICE` = {?}, `DATE_ACT` = {?}, `EXTRANET_ID` = {?}, `CONTROL_LIST` = {?}, `PROBE` = {?}, `DEADLINE` = {?}, `DEADLINE_TABLE` = {?}, `MATERIAL` = {?}, `NUM_ACT_TABLE` = {?}, `ATTESTAT` = {?}, `SELECTION_TYPE` = {?} WHERE `ID_Z` = {?}";
            $update_ba_tz_param_arr = array(serialize($arr), $_POST['OBJECT'], NOW(), $_POST['DESCRIPTION'], (int)$_GET['ID'], (int)$_POST['ACT_NUM'], $price, $_POST['DATE_ACT'], $extranet_id, ($_POST['ACT_LIST'] == '3' ? '1' : '0'), serialize($probe), $deadlineProtocol, $deadline_table_up, $dealMaterials, $num_act, $in_oa, $checked_probe_customer, (int)$_GET['ID']);
            $update_ins_ba_tz1 = $DB_Conn->queryExec($update_ba_tz_sql, $update_ba_tz_param_arr);

		} else { 
			
            $insert_ba_tz_sql = "INSERT ba_tz (`TZ`,`OBJECT`, `DATE_SOZD`, `ID_Z`, `PROBE`, `ACT_NUM`, `PRICE`, `DESCRIPTION`, `DATE_ACT`, `EXTRANET_ID`, `CONTROL_LIST`, `ATTESTAT`, `DEADLINE`, `DEADLINE_TABLE`, `MATERIAL`, `NUM_ACT_TABLE`, `SELECTION_TYPE`) VALUES({?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?}, {?});";
            $insert_ba_tz_param_arr = array(serialize($arr), $_POST['OBJECT'], NOW(), (int)$_GET['ID'], serialize($probe), (int)$_POST['ACT_NUM'], $price, $_POST['DESCRIPTION'], $_POST['DATE_ACT'], $extranet_id, ($_POST['ACT_LIST'] == '3' ? '1' : '0'), $in_oa, $deadlineProtocol, $deadline_table_up, $dealMaterials, $num_act, $checked_probe_customer);
            $update_ins_ba_tz1 = $DB_Conn->queryExec($insert_ba_tz_sql, $insert_ba_tz_param_arr);
		}
		
        if ($update_ba_tz1->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }

    }
	
	$id_ba_tz = !empty($result_select_ba_tz['ID']) ? $result_select_ba_tz['ID'] : '';
    
    $ID_TZ = (!empty(((int)$_POST['id_tz'])) ? (int)$_POST['id_tz'] : $id_ba_tz);
	

    for ($i = 0; $i < count($res_otbor['NUM_PROBE']); $i++) {
        for ($j = 0; $j < count($res_otbor['NUM_PROBE'][$i]); $j++) {
            $res_all_otbor = $DB->Query("INSERT INTO `PODGOTOVKA`(`NUM_PROBE`,`GOST_ID`, `TZ_ID`, `PROBE_NAME`, `GOST_OTBOR_ID`) VALUES ('" . $res_otbor['NUM_PROBE'][$i][$j] . "', " . (int)$res_otbor['GOST_ID'][$i][$j] . ", " . (int)$ID_TZ . ", '" . $res_otbor['PROBE_NAME'][$i][$j] . "', " . (int)$res_otbor['GOST_OTBOR_ID'][$i][$j] . ")");
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

    if ($contract_ar[1]) {
        $contract = (int)$contract_ar[1];
        $DB->Query("DELETE  FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL` = " . $id_d);
        $DB->Query("INSERT INTO `DEALS_TO_CONTRACTS` (`ID_CONTRACT`, `ID_DEAL`) VALUES (" . $contract . ", " . (int)$id_d . ")");
    }

    if (isset($_POST['DOGOVOR_NUM']) && $_POST['DOGOVOR_NUM'] == '0') {
        $DB->Query("DELETE  FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL` = " . $id_d);
    }

    $numberField = $_GET['EDIT'] ? "`ID`=" . (int)$_GET['EDIT'] : "`ID_Z`=" . (int)$_GET['ID'];

    if (isset($_POST['hiddenNotDiscount']) && $_POST['hiddenNotDiscount'] == 1
        && (isset($_GET['EDIT']) && !empty($_GET['EDIT']) || isset($_GET['ID']) && !empty($_GET['ID']))) {
        $DB->Query("UPDATE `ba_tz` SET `DISCOUNT`='0' WHERE " . $numberField)->Fetch();
    }

    if (isset($_POST['hiddenIsDiscount']) && $_POST['hiddenIsDiscount'] == 1
        && (isset($_GET['EDIT']) && !empty($_GET['EDIT']) || isset($_GET['ID']) && !empty($_GET['ID']))) {
        $DB->Query("UPDATE `ba_tz` SET `DISCOUNT`='" . (int)$_POST['discountName'] . "' WHERE " . $numberField)->Fetch();
    }

    
    $date_save = date('Y-m-d H:i:s');

    $assigned = CUser::GetByID($USER->GetID())->arResult[0];
    
    $assigned_name = !empty($assigned['NAME']) ? $assigned['NAME'] : '';
    
    $assigned_lastname = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';
    
    $assigned_user = $assigned_name.' '.$assigned_lastname;

    if (!empty($_GET['ID'])) {
        $res_insert = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
        $ba_tz_insert = $res_insert->Fetch();
		
        $tz = $ba_tz_insert['ID'];
        
        $ID_P = $ba_tz_insert['NUM_P'] ? $ba_tz_insert['NUM_P'] : 'номер не присвоен';
        
        $num_request = !empty($deal1["TITLE"]) ? $deal1["TITLE"] : '';


        
        $res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date_save . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$tz . "','" . $USER->GetID() . "', 'Сохранение технического задания', '" . $num_request . "')");

        
        if ($ba_tz_insert['ID'])
        {
            $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$tz. " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
            $history_ass=$history['ASSIGNED'];
            $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
            $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
            if($history_date<$history_date_null) $history_date='';
        }
        
        $assignet_history = $history_ass . " " . $history_date;

        $update_assignet_ba_tz = $DB->Query("UPDATE ba_tz SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . (int)$_GET['ID']);

    
    }

    if(!empty($_GET['ID'])) {
        
        if(!empty($_REQUEST['quarry']) && $_REQUEST['quarry'] != 0) {
            $quarry_id = $_REQUEST['quarry'];
            $sqlTot = "UPDATE `ba_tz` SET QUARRY_ID = {?} WHERE ba_tz.ID_Z = {?}";
            $paramArr = array($quarry_id, (int)$_GET['ID']);

            $update = $DB_Conn->queryExec($sqlTot, $paramArr);
        }
    }

    if (!empty($_GET['EDIT'])) {
        $res_update = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['EDIT']);
        $ba_tz_update = $res_update->Fetch();
		
        $tz = $ba_tz_update['ID'];
        
        $ID_P = $ba_tz_update['NUM_P'] ? $ba_tz_update['NUM_P'] : 'номер не присвоен';
        
        $num_request = !empty($dealUp["TITLE"]) ? $dealUp["TITLE"] : '';
        
        $res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date_save . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$tz . "','" . $USER->GetID() . "', 'Обновление технического задания', '" . $num_request . "')");
        
        if ($ba_tz_update['ID_Z'])
        {
            $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$tz. " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
            $history_ass=$history['ASSIGNED'];
            $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
            $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
            if($history_date<$history_date_null) $history_date='';
        }
        
        $assignet_history = $history_ass . " " . $history_date;
        

        $update_assignet_ba_tz = $DB->Query("UPDATE ba_tz SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . (int)$ba_tz_update['ID_Z']);

        // добавление в запись ba_tz в поле карьера QUARRY_ID
        if(!empty($_REQUEST['quarry']) && $_REQUEST['quarry'] != 0) {
            $quarry_id = $_REQUEST['quarry'];
            $sqlTot = "UPDATE `ba_tz` SET QUARRY_ID = {?} WHERE ba_tz.ID = {?}";
            $paramArr = array($quarry_id, (int)$_GET['EDIT']);

            $update = $DB_Conn->queryExec($sqlTot, $paramArr);
        }
        // добавление в запись ba_tz в поле ~dedlaine_date
        if(!empty($_REQUEST['deadlineIspDay']) && $_REQUEST['deadlineIspDay'] != '') {
            $deadlineDay = $_REQUEST['deadlineIspDay'];
            $sqlTot1 = "UPDATE `ba_tz` SET DAY_TO_TEST = {?} WHERE ba_tz.ID = {?}";
            $paramArr1 = array($deadlineDay, (int)$_GET['EDIT']);

            $upd = $DB_Conn->queryExec($sqlTot1, $paramArr1);
        }

    
    }

	

	$current_ba_tz = null;
	if (!empty($ba_tz_insert)) {
		$current_ba_tz = $ba_tz_insert;
	} elseif (!empty($ba_tz_update)) {
		$current_ba_tz = $ba_tz_update;
	}
	
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
    <a href="/request_card.php?ID=<?= $_POST['id_deal'] ?>">Вернуться в карточку заявки</a>
    |
    <a href="/tz.php?EDIT=<?= $ID_TZ ?>">Вернуться к редактированию</a>
    |
    <a href="/<?= ($_POST['ACT_LIST'] == '3' ? 'control_list' : 'probe') ?>.php?ID=<?= $_POST['id_deal'] ?>"><?= ($_POST['ACT_LIST'] == '3' ? 'Создать контрольный лист' : 'Создать акт приемки проб') ?></a>
    <?php
    if (!empty($_POST['ACT_NUM'])) {
        echo "| <a href='/results_isp.php?ID=" . $_POST['id_deal'] . "'>Перейти к испытаниям</a>";
    }

	

	?>
	

	<?php

	

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
    exit;
}

if (!empty($_GET['ID'])) {

    $deal = CCrmDeal::GetByID($_GET['ID']);
    $companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch();
    $dataDiscount = $USER_FIELD_MANAGER->GetUserFields('CRM_COMPANY', $companys['ID']);

    $dataDiscountVal = $dataDiscount['UF_CRM_1582914274353']['VALUE'];
}

if (!empty($_GET['EDIT'])) {
    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['EDIT']);
    $row = $res->Fetch();
    $dateDedline = $row['DEADLINE'];
    $deal = CCrmDeal::GetByID($row['ID_Z']);
    $mater_arr = unserialize($row['TZ']);
    $probe_arr = unserialize($row['PROBE']);
    $probe_place = explode("; ", $probe_arr[1]['mesto_data']);
    $probe_proizv = $probe_arr[1]['probe_proizv'];


    

    $tzId = !empty($row['ID']) ? (int)$row['ID'] : 0;

    $arrAllProbeWitnNumber = [];

    $getProtocolsData = $DB_Conn->select('SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}', [$tzId]);

    $amountProtocolData = !empty($getProtocolsData) ? count($getProtocolsData) : 0;

    $getProtocolsDataWitnNumber = $DB_Conn->select('SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?} AND `NUMBER` IS NOT NULL', [$tzId]);

    foreach ($getProtocolsDataWitnNumber as $keyProtocol => $arrProtocolData)
    {

        $currentProtocolSampleChecked  = !empty($arrProtocolData['PROBE']) ? unserialize($arrProtocolData['PROBE']) : [];

        foreach ($currentProtocolSampleChecked as $keyMaterial => $arrDataMaterial)
        {

            $arrAllProbeWitnNumber[] = (int)$keyMaterial;
        }

    }


    $arrAllProbeWitnNumber = array_unique($arrAllProbeWitnNumber);

    

    $company1 = CCrmCompany::GetByID(19);
    $extranet_id = CCrmCompany::GetUserFields(19);//$company1['UF_CRM_1571818346092'];
    $companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch();
    $dataDiscount = $USER_FIELD_MANAGER->GetUserFields('CRM_COMPANY', $companys['ID']);
    $dataDiscountVal = 0;

    // поле комменатрий в ba_tz и  tz.php снизу -> идут в кп и прил к ТЗ
    $val_comm_tz = $row['COMMENT_TZ'];
    $val_comm_kp = $row['COMMENT_KP'];

    if ($row['DISCOUNT']) {
        $dataDiscountVal = $row['DISCOUNT'];
    } else {
        $dataDiscountVal = $dataDiscount['UF_CRM_1582914274353']['VALUE'];
    }

    $quarry = $row['QUARRY_ID'];
    $selRowQuarry = new CDB();
    $sqlReq = "SELECT NAME FROM `Quarry` where ID = {?}";
    $rowQuarry = $selRowQuarry->selectRow($sqlReq, array($quarry));

    $text_field_quarry = (!is_null($rowQuarry['NAME'])) ? $rowQuarry['NAME'] : '';

	
	$typeIdRequest = !empty($row['TYPE_ID']) ? $row['TYPE_ID'] : '';
	

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

	
	$result_select_check_tz = null;
	if ($row['ID']) {
		
		$result_select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $row['ID'] . " AND `date_return` IS NULL");
	}

	$sum_select_check_tz = $result_select_check_tz->SelectedRowsCount();


	$arr_id_leader = [];

	if (!empty($sum_select_check_tz)) {

		while($result_check_tz = $result_select_check_tz->Fetch()) {
			if (!empty($result_check_tz['leader'])) {
				$arr_id_leader[] = $result_check_tz['leader'];
			}
		}
	}

	
    ?>
    <form id="form_tz" method="post">
        
        <div>
            <a href="/request_card.php?ID=<?= $row['ID_Z'] ?>">
                <div class='return' title='Вернуться назад'></div><!--Назад-->
            </a>
            <div class='edit disable' title='Редактировать'></div>
            <div class='list disable' title='Вернуться к списку'></div>
            <div class='card disable' title='Карточка заявки'></div>
            <div class='download disable' title='Скачать'></div>
            <div class='copy disable' title='Скопировать'></div>
            <div class='remove disable' title='Удалить'></div>
            <div class='add disable' title='Добавить'></div>
            <div class='report disable' title='Отчёт'></div>
            <a href="/<?= ($row['CONTROL_LIST'] ? 'control_list' : 'probe') ?>.php?ID=<?= $row['ID_Z'] ?>">
                <div class='further'
                     title="<?= ($row['CONTROL_LIST'] ? 'Создать контрольный лист' : 'Создать акт приемки проб') ?>"></div>
            </a>
        </div>
        
        <input type="text" name="id_deal" value="<?= $row['ID_Z'] ?>" hidden>
        <table id="tz">
            <?php

            $dealForTZ = CCrmDeal::GetByID($row['ID_Z']);
            ?>
            <td width="250" height="40">Заявка</td>
            <td><b><?= $dealForTZ['TITLE'] ?></b></td>
            </tr>

            <tr>
                <td width="250" height="40">Объект строительства</td>
                <td><input class="tz" multiple type="text" list="objects" name="OBJECT" size="64"
                           value='<?= $row['OBJECT'] ?>'></td>
            </tr>

            <tr>
                <td width="250" height="40">Описание объекта испытаний</td>
                
                <td><textarea class="test_description" name="DESCRIPTION" style="width:496px" required><?= $row['DESCRIPTION'] ?></textarea></td>
				
            </tr>

            <tr>
                <td width="250" height="40">Основание для проведения испытаний</td>
                <td>
				
                    <?php
					$resDealsContract = null;
                    if (!empty($row['ID_Z'])) {
                        $resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$row['ID_Z'])->Fetch();
                    }
                    ?>
                    
				   <?php
						
						$result_select_dogovor = null;
						if (!empty($resDealsContract['ID_CONTRACT'])) {
							$result_select_dogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID` = " . (int)$resDealsContract['ID_CONTRACT'])->Fetch();

							
							$dogovor_id = !empty($result_select_dogovor['ID']) ? $result_select_dogovor['ID'] : '';

							
							$dogovor_date = !empty($result_select_dogovor['DATE']) ? ruDate($result_select_dogovor['DATE']) : '';

							
							$number_contract = !empty($result_select_dogovor['NUMBER']) ? $result_select_dogovor['NUMBER'] : $dogovor_id;

							echo ($result_select_dogovor['CONTRACT_TYPE'] ? $result_select_dogovor['CONTRACT_TYPE'] : 'Договор') ." №" . $number_contract . " от " . $dogovor_date . ", ID" . $dogovor_id;
						} else {
							echo "Договор еще не составлен";
						}
				   ?>
				   
                </td>
            </tr>

            <!-- срок проведения испытаний -->
            <tr>
                <td width='250' height='40'>Срок проведения испытаний (в днях)</td>
                <td>
                    <input class="tz" name="deadlineIspDay" type="text" value="<?= $day_to_test ?>">
                </td>
            </tr>

            <tr>
                <td width="250" height="40">Основание для формирования протокола</td>
                <td><select class="tz" name="ACT_LIST">
                        <option value="1" <?= $row['CONTROL_LIST'] == '0' ? 'selected' : '' ?>>Акт приемки/передачи
                            проб
                        </option>
                        <option value="3" <?= $row['CONTROL_LIST'] == '1' ? 'selected' : '' ?>>Контрольный лист</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="250" height="40">Номер документа-основания для формирования протокола</td>
                <td>
                    <input class="tz" type="number" name="ACT_NUM"
                           value="<?= (isset($row['ACT_NUM']) && $row['ACT_NUM'] == 0) ? '' : $row['ACT_NUM'] ?>"
                           <?= (isset($row['ACT_NUM']) && $row['ACT_NUM'] == 0) ? 'disabled' : '' ?> required>
                    <input class="tzHidden" type="number" value="" disabled>

					 <?php
						
						if (!empty($typeIdRequest) && $typeIdRequest != '1') :
					?>
					
                    <input class='tzNoSamples' type='checkbox' name='noSamples'
                           <?= (isset($row['ACT_NUM']) && $row['ACT_NUM'] == 0) ? 'checked' : '' ?>>Пробы еще не поступили
					<?php endif; ?>
                </td>
            </tr>
            <tr>
                <td width="250" height="40">Дата документа-основания</td>
                <td><input class="tz" type="date" name="DATE_ACT"
                           value="<?= (isset($row['ACT_NUM']) && $row['ACT_NUM'] == 0 && empty($row['DATE_ACT'])) ? '' : $row['DATE_ACT'] ?><? //= $row['DATE_ACT'] ?>"
                           size="20" required></td>
            </tr>
            <tr>
                <td width="250" height="40">Место отбора проб</td>
                <td><input class="tz" type="text" name="PLACE_PROBE"
                           value='<?= (isset($row['ACT_NUM']) && $row['ACT_NUM'] == 0) ? '' : $probe_place[0] ?>'></td>
            </tr>
            <tr>
                <td width="250" height="40">Дата отбора проб</td>
                <td><input class="tz" type="date" name="DATE_PROBE"
                           value="<?= (isset($row['ACT_NUM']) && $row['ACT_NUM'] == 0 || empty($probe_place[1]) || (!empty($probe_place[1]) && $probe_place[1] == " Не указана")|| (!empty($probe_place[1]) && $probe_place[1] == "-") || (!empty($probe_place[1]) && $probe_place[1] == " -") || (!empty($probe_place[1]) && $probe_place[1] == "01.01.1970")) ? '' : date("Y-m-d", strtotime($probe_place[1])) ?><? //= date("Y-m-d", strtotime($probe_place[1])) ?>"
                           size="20"></td>
            </tr>
            <!-- переехало из результатов испытаний -->
            <tr>
                <td height="35">
                    Пробы отобраны не заказчиком
                </td>
                <td>
                    <input type="checkbox" name="SELECTION_TYPE" <?= ($row['SELECTION_TYPE']) ? 'checked' : '' ?>>
                </td>
    		</tr>
            <!-- переехало из отбора проб (probe.php) -->
            <tr>
                <td>Отбор проб произвёл</td>
                <td> <input type="text" class="tz" name="probe_proizv" size="70" value="<?= ( !empty($probe_proizv) ? $probe_proizv : '' ) ?>"> </td>
            </tr>

			 <?php
				
				if (!empty($typeIdRequest) && $typeIdRequest != '1') :
			?>
			
            <tr>
                <td width="250" height="40">Срок выдачи протокола</td>
                <td><input class="tz" type="date" name="DEADLINE_PROTOCOL" value="<?= $dateDedline ?>" size="20"></td>
            </tr>
			
			<tr>
				<td width="250" height="40">Заявка учтена</td>
				<td>
					<?php
						$title_deal_taken = '';
						if (!empty($row['TAKEN_ID_DEAL'])) {
							$result_ba_tz_taken = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$row['TAKEN_ID_DEAL'])->Fetch();

							$deal_taken = null;
							if (!empty($result_ba_tz_taken['ID_Z'])) {
								$deal_taken = CCrmDeal::GetByID($result_ba_tz_taken['ID_Z']);
							}

							$number_deal = !empty($deal_taken['TITLE']) ? $deal_taken['TITLE'] : ''; 
							$company_deal = !empty($deal_taken['COMPANY_TITLE']) ? $deal_taken['COMPANY_TITLE'] : ''; 
							$begin_date_deal = !empty($deal_taken['BEGINDATE']) ? $deal_taken['BEGINDATE'] : ''; 

							$title_deal_taken = 'Заявка ' . $number_deal . ', ' . $company_deal . ', от ' . $begin_date_deal;
						}
					?>
					<input type="checkbox" name="taken" class="taken" <?= !empty($row['TAKEN_ID_DEAL']) ? 'checked' : '' ?>>
					<span class="taken-request"><?= $title_deal_taken ?></span>
					<input type="hidden" class="hidden-taken">
				</td>
			</tr>
			
			 <?php endif; ?>
			
			<?php
			
			$id_main_assigned = !empty($dealForTZ['ASSIGNED_BY_ID']) ? $dealForTZ['ASSIGNED_BY_ID'] : '';

			$main_leader_check_tz = null;
			if (!empty($row['ID'])) {
				
				$main_leader_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $row['ID'] . " AND `leader`=" . $id_main_assigned . " AND `date_return` IS NULL")->Fetch();
			}

			?>
			<tr class="tr-confirm" style="<?= (!empty($arr_id_leader) && in_array($USER->GetID(), $arr_id_leader)) ? 'display:table-row' : 'display:none' ?>">
				<td>Техническое задание утверждено руководителем лаборатории</td>
				<td>
					<input type="checkbox" name="tz_confirm" class="tz-confirm" <?= !empty($main_leader_check_tz['confirm']) ? 'checked' : '' ?>>
					<span class="text-confirm"><?= !empty($main_leader_check_tz['confirm']) ? 'Техническое задание проверено!' : '' ?></span>
				</td>
			</tr>
			<tr class="tr-confirm" style="<?= (!empty($arr_id_leader) && in_array($USER->GetID(), $arr_id_leader)) ? 'display:table-row' : 'display:none' ?>">
				<td>Снять заявку переданную на рассмотрение руководителю лаборатории</td>
				<td>
					<button type="button" class="btn btn-no-transfer" <?= empty($row['TZ']) || !empty($main_leader_check_tz['date_return']) || !empty($main_leader_check_tz['confirm']) ? 'disabled' : '' ?>>Вернуть</button>
					<span class="text-return"><?= !empty($main_leader_check_tz['date_return']) ? 'ТЗ вернули с расмотрения!' : '' ?></span>
				</td>
			</tr>
			<?php
			//endif;
				if (!empty($typeIdRequest) && $typeIdRequest != '1') :
			?>
			<tr>
			<td>Передать на рассмотрение руководителю лаборатории</td>
			<td>
				<div class="transfer-laboratory-manager">
					<div class="wreppre-laboratory-manager">
						<?php

							$arr_all_main_assigned = [
								'54' => '11',
								'55' => '13',
								'56' => '15',
								'57' => '58',
								'58' => '43',
							];

							

							$main_user = !empty($id_main_assigned) ? CUser::GetByID($id_main_assigned)->Fetch() : '';

							$id_laba_main_assigned = !empty($main_user['UF_DEPARTMENT'][0]) ? $main_user['UF_DEPARTMENT'][0] : '';

							$assigned_main_name = !empty($dealForTZ['ASSIGNED_BY_NAME']) ? $dealForTZ['ASSIGNED_BY_NAME'] : '';

							$assigned_main_lastname = !empty($dealForTZ['ASSIGNED_BY_LAST_NAME']) ? $dealForTZ['ASSIGNED_BY_LAST_NAME'] : '';

							$name_lastname_main_assigned = getInitials($assigned_main_name) ." ". $assigned_main_lastname;


							if (!empty($name_lastname_main_assigned) && !empty($id_main_assigned) && !empty($id_laba_main_assigned) && !empty($arr_all_main_assigned[$id_laba_main_assigned]) && $arr_all_main_assigned[$id_laba_main_assigned] == $id_main_assigned) {

								

								echo "<p class='laboratory-manager main-assigned' data-id-assigned='" . $id_main_assigned . "' data-id-laba='" . $id_laba_main_assigned . "'><input type='checkbox' name='verified' " . (!empty($main_leader_check_tz['confirm']) ? 'checked' : '') . " disabled>  " . $name_lastname_main_assigned . "</p>";
							}

							$arr_id_additional_assigned = null;

							if (!empty($dealForTZ['ID'])) {

								$user_field_deal = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $dealForTZ['ID']);

								$arr_id_additional_assigned = !empty($user_field_deal['UF_CRM_1571643970']['VALUE']) ? $user_field_deal['UF_CRM_1571643970']['VALUE'] : '';

								
							}

							$all_id_assigned = null;

							if (!empty($arr_id_additional_assigned) && !empty($id_main_assigned)) {
									$all_id_assigned = $arr_id_additional_assigned;
									
									array_push($all_id_assigned, (int)$id_main_assigned, 1);
							} else {
								
								$all_id_assigned =  [(int)$id_main_assigned, 1];
							}


						?>

					</div>
					<button type="button" class="btn btn-transfer" <?= empty($row['TZ']) || (!empty($main_leader_check_tz) && empty($main_leader_check_tz['date_return'])) || (!in_array($USER->GetID() , $all_id_assigned) && ($USER->GetID() != 62) && ($USER->GetID() != 35)) ? 'disabled' : '' ?>>Передать</button>
					<!--<button type="button" class="btn btn-transfer" <//?= empty($row['TZ']) || !empty($sum_select_check_tz) || !in_array($USER->GetID() , $all_id_assigned) ? 'disabled' : '' ?>>Передать</button>-->
					<!--<button type="button" class="btn btn-transfer" <//?= empty($row['TZ']) || !empty($sum_select_check_tz) || !in_array($USER->GetID() , [1]) ? 'disabled' : '' ?>>Передать</button>-->
					<!--<span class="text-transfer"><//?= !empty($sum_select_check_tz) ? 'Заявка передана на проверку!' : '' ?></span>-->
					<span class="text-transfer"><?= (!empty($main_leader_check_tz) && empty($main_leader_check_tz['date_return'])) ? 'Заявка передана на проверку!' : '' ?></span>
				</div>
			</td>
			</tr>

            <!-- выбор карьера, правки vsatonin -->
            <tr>
                <td>Привязка карьера к ТЗ</td>
                <td>
                <?
                    $required_select_quarry = '';
                    $dopusc_mater_id = [1, 2, 6, 7, 8, 11, 15, 94, 95, 101];
                    // пробегаемся по массиву материалов и собираем id материалов
                    $id_mater_arr = [];
                    foreach($mater_arr as $key_mater => $val_mater) {
                        $item_mater = explode('ID ', $val_mater['mater']);
                        if(in_array((int)$item_mater[1], $dopusc_mater_id)){
                            $id_mater_arr[] = $item_mater[1];
                            $required_select_quarry = 'required';
                            break;
                        }
                    }
                ?>
					<select class="obl2" name="quarry" <?= $required_select_quarry ?>>
					<?
						// запрос к БД за списком карьеров
						$quarDB = new CDB();
						$quarList = $quarDB->select('SELECT * FROM `Quarry`');
                        if($text_field_quarry == ''){
                            echo("<option value=''>Выберите карьер</option>");
                        }
                        // $selected = 'selected';
						foreach($quarList as $key => $value) {
                            if($value['NAME'] == $text_field_quarry) {
    							echo("<option value='{$quarry}' selected>{$text_field_quarry}</option>");
                            }else{
                                echo("<option value='{$value['ID']}'>{$value['NAME']}</option>");
                            }
						}
					?>
					</select>
				</td>
            </tr>
	<?php
				endif;
				
				if (!empty($typeIdRequest) && $typeIdRequest == '1') :
			?>
			<tr>
				<td>Привязать ВЛК к заявке</td>
				<td>
					<button type="button" class="link-to-request">Привязать к заявке</button>
				</td>
			</tr>
			<?php
				endif;
				
			?>
			
        </table>
        <br/>
        <br/>
        <?php

        $materialsReq = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = " . $row['ID_Z']);
        
        $number_material = 0;

        foreach ($mater_arr as $key => $arrDatum) {
           
            $attrDisabled = in_array($key, $arrAllProbeWitnNumber) ? 'disabled' : '';
            $attrReadonly = in_array($key, $arrAllProbeWitnNumber) ? 'readonly' : '';
            

            if (empty($key) && $key == 0) {
                $number_material = 1;
            }
            
            $html = "<select name='gost[]' required>";
            ?>
            <div class='wrapperMaterial'>
                <br/>
                <table id="tz">

                    <?php

                    if ($materialsReq->SelectedRowsCount() > 0) {
                        $material = $materialsReq->Fetch();
                        ?>
						
                        <tr class="tr-material">
						
                            <td class="number-material"><?= ($key + $number_material) ?></td>
                           
                            <td style="font-weight:bold">Материал</td>
                            <td>
							
                                <input class="tz" type='hidden' name='mater[<?= $key ?>]'
                                       value="<?= $material['NAME_MATERIAL'] . ($material['ID_MATERIAL'] ? ', ID ' . $material['ID_MATERIAL'] : '') ?>">
                                <input class="tz" type='text' name='idMater[<?= $key ?>]' list="materials" required
                                       value="<?= !empty($material['NAME_MATERIAL']) ? $material['NAME_MATERIAL'] : '' ?>">
                            </td>
							
                            <td style="font-weight:bold">Кол-во проб/образцов</td>

                            <td class="amount">
                                <input class="tz" type='number' name='obiem[<?= $key ?>]' min='1' required
                                       value="<?= $arrDatum['obiem'] ?>" <?= $amountProtocolData ? 'readonly' :  '' ?>>
                            </td>
                            <td class="btn-add-del">
                                <input type="button" value="+" id="new_material" <?= $amountProtocolData ? 'disabled' :  '' ?>>
                                <input type="button" value="-" name="delMaterial" class='delMaterial' <?= $amountProtocolData ? 'disabled' :  '' ?>>
                            </td>

                            <td>
                                
                                <input type="button" value="Удалить методики" name="delMetodic" class='delMetodic' <?= $amountProtocolData ? 'disabled' :  '' ?>>
                            </td>
                            
                        </tr>

                        <tr class="tr-select-gost">
                            <td></td>
                            <td class="abbreviation-gost">гост</td>
                            <td>
                                <select class="gost-select" name="gost-select[<?= $key ?>][]" data-key="<?= $key ?>">
                                </select>
                                <input type="hidden" class="hidden-id-gost" value=''>
                            </td>
                            <td class='wrapperBtnGost'>
                                <input type='button' class='add-gost' value='+'>
                            </td>
                        </tr>

                        <?php
                    } else {
                        ?>

                        <tr class="tr-material">

                            <td class="number-material"><?= ($key + $number_material) ?></td>

                            <td style="font-weight:bold">Материал</td>
                            <td>
                                <input class="tz" type='hidden' name='mater[<?= $key ?>]'
                                       value="<?= $arrDatum['mater'] . (!strpos($arrDatum['mater'], 'ID') ? ', ID ' . array_search($arrDatum['mater'], $materialsArr) : '') ?>">
                                <input class="tz" type='text' name='idMater[<?= $key ?>]' list="materials" required
                                       value="<?= explode(', ID', $arrDatum['mater'])[0] ?>">
                            </td>
                            <td style="font-weight:bold">Кол-во проб/образцов</td>
                            
                            <td class="amount">
                                <input class="tz" type='number' name='obiem[<?= $key ?>]' min='1' required
                                       value="<?= $arrDatum['obiem'] ?>" <?= $amountProtocolData ? 'readonly' :  '' ?>>
                            </td>
                            <td class="btn-add-del">
                                <input type="button" value="+" id="new_material" <?= $amountProtocolData ? 'disabled' :  '' ?>>
                                <input type="button" value="-" name="delMaterial" class='delMaterial' <?= $amountProtocolData ? 'disabled' :  '' ?>>
                            </td>

                            <td>
                                
                                <input type="button" value="Удалить методики" name="delMetodic" class='delMetodic' <?= $amountProtocolData ? 'disabled' :  '' ?>>
                            </td>
                           
                        </tr>
                        <tr class="tr-select-gost">
                            <td></td>
                            <td class="abbreviation-gost">гост</td>
                            <td>
                                <select class="gost-select" name="gost-select[<?= $key ?>][]" data-key="<?= $key ?>">
                                </select>
                                <input type="hidden" class="hidden-id-gost" value=''>
                            </td>
                            <td class='wrapperBtnGost'>
                                <input type='button' class='add-gost' value='+'>
                            </td>
                        </tr>
                        
                        <?php
                    }
                    ?>
                </table>
                <br/>
                <table id="tz">
                    <thead>
                    <th>Методика испытаний</th>
                    
                    <th></th>
                    
                    <th>Тех.условия</th>
                    <?php// if(in_array('8', $USER->GetUserGroupArray())) {?>
                    <th>Цена</th>
                    <?php //}?><!--
            <th>Норма 1</th>
            <th>Норма 2</th>
            <th>Ед. измерения</th>-->
                    <th>Без цены</th>
                    <th></th>
                    </thead>
                    <tbody id="tz_body<?= $key ?>" name="tz_body">


                    <!--<tr>

                    <td>
                        <input  list="gost" class="gost" data-id="<?= $key ?>"></td>
						<td>
						<input list="gost_new" class="gost_new" data-id="<?= $key ?>"></td></tr>-->
                    <?php
                    $g = 0;

                    foreach ($arrDatum['gost'] as $item => $value) {

                        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $value)->Fetch();
                        $g_new = $arrDatum['gost_new'][$g]; //echo $g_new."__";

                        if ($g_new) $res_tu = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $g_new)->Fetch(); ?>

                        <tr class="tr-gost" id="<?= $key ?>" data-gost="<?= !empty($res['id_gost']) ? $res['id_gost'] : '' ?>">

                            <td class="tdGost">
                                
                                <select name="gost[<?= $key ?>][]" style="width:350px" class="gost methods styleGost"
                                        data-id="<?= $key ?>" required <?= $attrReadonly ?>>
                                
                                    <option value="" disabled selected>Выберите</option>
                                    <?php
                                    $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");

                                    while ($gost = $ba_gost->Fetch()) {

										$gost_id = !empty($gost['ID']) ? $gost['ID'] : ''; //ID ГОСТа

										$gost_price = !empty($gost['PRICE']) ? $gost['PRICE'] : '';
										if (!empty($result_select_dogovor['ID']) && !empty($result_select_dogovor['LONGTERM'])) {

											$price_gost_longterm = null;
											if (!empty($gost_id)) {
												$price_gost_longterm = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT` = " . (int)$result_select_dogovor['ID'] . " AND `ID_GOST` = " . (int)$gost_id)->Fetch();
											}

											$gost_price = isset($price_gost_longterm['PRICE']) ? $price_gost_longterm['PRICE'] : $gost_price;
										}

                                        // ново созданные госты выводим по-другому, отслеживаем их по пустом GOST и GOST_PUNKT
                                        // выводим с атрибутом data-enter, для дальнейших отслеживаний
                                        if($gost['GOST'] == '-' && $gost['GOST_PUNKT'] == '-'){
                                            echo "<option data-price='" . $gost_price . "' data-type='" . $gost['NORM_TEXT'] . "' data-enter='1'  value='" . $gost_id . "' " . ($gost_id == $res['ID'] ? 'selected' : '') . ">" . $gost['SPECIFICATION'] . "</option>";
                                        }else{ // иначе выводим по стандартному
                                            echo "<option data-price='" . $gost_price . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost_id . "' " . ($gost_id == $res['ID'] ? 'selected' : '') . ">" . $gost['GOST'] . ' ' . $gost['SPECIFICATION'] . ' (' . $gost['GOST_PUNKT'] . ")</option>";
                                        }

                                        // echo "<option data-price='" . $gost_price . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost_id . "' " . ($gost_id == $res['ID'] ? 'selected' : '') . ">" . $gost['GOST'] . ' ' . $gost['SPECIFICATION'] . ' (' . $gost['GOST_PUNKT'] . ")</option>";
                                    
									}
                                    ?>
                                </select>
                                
                                <!--<a class='methodsLink' target='_blank'>
                                    <div class='methodsLinkPng' title='Методика'></div>
                                </a>-->
                            </td>
                            <td class="method">
                                <a class='methodsLink' target='_blank'>
                                    <div class='methodsLinkPng' title='Методика'></div>
                                </a>
                            </td>

                            <td class="tdGost">
                                
                                <select name="gost_new[<?= $key ?>][]" style="width:350px" class="gost2 conditions styleGost"
                                        data-id="<?= $key ?>" <?= $attrReadonly ?>>
                                
                                    <option value="2522" selected>-- | --</option>
                                    <?php
                                    $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                                    while ($gost = $ba_gost->Fetch()) {
                                        echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "' " . ($gost['ID'] == $res_tu['ID'] ? 'selected' : '') . ">" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                    }
                                    ?>
                                </select></td>
                            <td><input type="number" name="price[<?= $key ?>][]" class="oborud"
                                    value="<?= $arrDatum['price'][$g] ? $arrDatum['price'][$g] : '0' ?>"
                                    style="<?= $arrDatum['price'][$g] ? '' : 'display:none' ?>"></input>
                                <?php if (!$arrDatum['price'][$g]) echo "<div class='oborudDash'>-</div>"; ?>
                            </td>
                            <td><input type="checkbox" class="dash" <?= $arrDatum['price'][$g] ? '' : 'checked' ?>></td>
                            
                            <?php

                            if (!$g) {
                                echo "<td class='btn-method-add-del'>
                                <input style='margin-left:5px' type='button' class='add3' value='+' " . $attrDisabled . ">
                                <input style='margin-left:5px' type='button' class='add2' value='+' " . $attrDisabled . ">
                                </td>";
                            } else {
                                echo "<td class='btn-method-add-del'>
										<input style='margin-left:5px' type='button' class='add3' value='+' " . $attrDisabled . ">
										<input style='margin-left:5px' type='button' class='add2' value='+' " . $attrDisabled . ">
										<input style='margin-left:5px' type='button' class='del' value='-' " . $attrDisabled . ">
									</td>";
                            }
                            ?>
                            
                        </tr>
                        <?php

                        $g++;
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <?php
        }
        ?>
        <input class="tz id-tz" type="hidden" name="id_tz" value="<?= $row['ID'] ?>">
		
		<input type="hidden" name="main_user_id" id= "main_user_id" value="<?= !empty($id_main_assigned) ? $id_main_assigned : '' ?>">
		
        <br/><br/>
		
		<datalist id="listRequest">
		</datalist>
		<div class="wrapperListRequest">
			<label id="labelListRequest">Список заявок ИЦ:</label>
			<input list="listRequest" for="labelListRequest" class="inputListRequest" name="inputListRequest">
		</div>

		
		<datalist id="listMaterial">
		</datalist>
		<div class="wrapperListMaterial">
			<label id="labelListMaterial">Список материалов заявки:</label>
			<input list="listMaterial" for="labelListMaterial" class="inputListMaterial" name="inputListMaterial" <?= ((!empty($typeIdRequest) && $typeIdRequest == '1') ? 'required' : '') ?>>
		</div>
        <?php

		
		?>
			<!--<button type="button" class="link-to-request">Привязать к заявке</button>-->
		<?php
		
         if (in_array('8', $USER->GetUserGroupArray()) && $typeIdRequest != '1') { ?><p>Общая
            сумма: <?php if (isset($row['PRICE']) && !empty($row['PRICE'])) echo number_format($row['PRICE'], 2, ',', '') . " руб. "/*.((isset($row['DISCOUNT']) && !empty($row['DISCOUNT'])) ? '(С учетом скидки '.$row['DISCOUNT'].'%)' : '')*/
            ; else echo "-";
            ?></p><?
        }
		
		if ($typeIdRequest != '1') :
        ?>
        <br/>
        <table>
            <tr>
                <td>Скидка</td>
                <td>
                    <input type='number' name='discountName' style="width:75px" min='0' max="50"
                           value='<?= $dataDiscountVal ?>'>
                    <input type='hidden' name='hiddenIsDiscount' value=''>
                    <input type='hidden' name='hiddenNotDiscount' value=''>
                </td>
                <td>
                    <input type="submit" style="margin:0 0 0 10px" name='submitDiscont' value="Применить скидку"
                           <?= in_array('8', $USER->GetUserGroupArray()) ? '' : 'disabled' ?>>
                </td>
                <td>
                    <?php if ($isCheck === 0 && $dataDiscountVal) { ?>
                        <span class='errorDiscount' style="display:inline-block">Для добавления скидки составьте техническое задание!</span>
                    <? } ?>
                    <?php if ($dataDiscountVal) : ?>
                        <span class='successDiscount' style="display:<?= ($isCheck === 1) ? 'inline-block' : 'none' ?>">Скидка <?= $dataDiscountVal ?>% добавлена!</span>
                    <?php else: ?>
                        <span class='successDiscount' style="display:<?= ($isCheck === 1) ? 'inline-block' : 'none' ?>">Скидка удалена!</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
		<?php endif; ?>

        <div style="display:flex;justify-content:flex-start;align-items:baseline;">
            <input type="submit" value="Сохранить" id="new_tz" <?= (!in_array($USER->GetID() , $all_id_assigned)  && $typeIdRequest != '1' && ($USER->GetID() != 62) && ($USER->GetID() != 35) && ($USER->GetID() != 43)) ? 'disabled' : '' ?>>

            
            <input type="button" value="Добавить материал" id="new_material" class="btn-new-material" <?= $amountProtocolData ? 'disabled' : '' ?>>
            
            <input type="button" value="Добавить комментарии" id="new_comment" class="btn-new-material">

        </div>

        <div class="wrepperMetodicAddGost" style="height: 330px; width: 200px; overflow: scroll;">
            <h4>Выберите лабораторию методике</h4>
            <table><tbody id="tbodyAddLaba"></tbody></table>
            <div class="wrapperBtn" style="display: contents;">
                <button type="button" class='btnAddLaba' id="idAddLaba" style="margin-top: 15px; width: 150px;">Добавить лабораторию</button>
            </div>
        </div>

        <div class="wrepperAddComment" style="display: none; margin-top: 15px; height: 350px; width: 250px; border: 1px solid #80808047; padding: 10px;">
            <h4>Добавление комментария</h4>
            <table>
            <tbody id="tbodyAddComment">
                <tr>
                    <td>
                        <label>
                            Добавьте комментарий к ТЗ:
                            <textarea name="comment_to_tz" style="width:250px; height:115px;margin-top: 5px;" placeholder="Комментарий к ТЗ"><?= $val_comm_tz ?></textarea>
                        </label>

                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                    </td>
                            Добавьте комментарий к КП:
                            <textarea name="comment_to_kp" style="width:250px; height:115px;margin-top: 5px;" placeholder="Комментарий к КП"><?= $val_comm_kp ?></textarea>
                        </label>
                    </td>
                </tr>
            </tbody></table>
        </div>

    </form>
    <br/>
    <div class='wrepperCompanyEmployees'>
        <div class='companyEmployees'>
            <select class="tz usersCompany" name="usersCompany"></select>
        </div>
        <div class='wrapperBtn'>
            <button class='btnAddUser' id="idAddUser">Добавить ответственного</button>
            <button class='btnCloseWindow' id="idCloseWindow">Закрыть</button>
        </div>
    </div>

    <?php
} else {
    ?>

    <!--====================================СОЗДАНИЕ НОВОГО ТЗ=========================================================================-->
    <form id="form_tz" method="post">
        <table id="tz">
            <?php

            $deal = CCrmDeal::GetByID($_GET['ID']);
            $dateDedline = date('Y-m-d');
            $res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=" . $_GET['ID']);
            if ($res_mat->SelectedRowsCount() == 0) {
                $mater_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $ID);
                $mater_arr = $mater_arr['UF_CRM_1569151529509']['VALUE'];
            } else {

                while ($mater = $res_mat->Fetch()) {
                    $mater_arr[] = $mater['NAME_MATERIAL'];
                    $mater_arr_id[] = $mater['ID_MATERIAL'];
                }
            }

            ?>
            <?php
            $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('STAGE_ID' => 'PREPARATION'));
            $dealForTZ = CCrmDeal::GetByID($_GET['ID']);

			
			$select_ba_tz = null;
			if (!empty($_GET['ID'])) {
				$select_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z` = " . (int)$_GET['ID'])->Fetch();
			}
			
			$result_select_check_tz = null;
			if ($select_ba_tz['ID']) {
				$result_select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $select_ba_tz['ID']);
			}

			$sum_select_check_tz = !empty($result_select_check_tz->SelectedRowsCount()) ? $result_select_check_tz->SelectedRowsCount() : '';

			$arr_id_leader = [];

			if (!empty($sum_select_check_tz)) {

				while($result_check_tz = $result_select_check_tz->Fetch()) {
					if (!empty($result_check_tz['leader'])) {
						$arr_id_leader[] = $result_check_tz['leader'];
					}
				}
			}

			

            ?>
            <tr>
                <td width="250" height="40">
                    Заявка для формирования ТЗ
                </td>
                <td>
                    <b><?= $dealForTZ['TITLE'] ?><input type="text" name="id_deal" value="<?= $dealForTZ['ID'] ?>"
                                                        hidden></b>
                    <!--<select onchange="top.location='/tz.php?ID='+this.value" name="id_deal" style="width:500px" required >
                <option value="" disabled selected>Выберите</option>
                <?php while ($deal = $deals->Fetch()) {

                        ?>
                    <option data-mater="<?= $deal['UF_CRM_1576411922643']//implode(';', $deal['UF_CRM_1569151529509'])     ?>" <?= (empty($deal['COMPANY_ID'])) ? 'disabled' : '' ?> <?= ($_GET['ID'] == $deal['ID']) ? 'selected' : '' ?>
                            value="<?= $deal['ID'] ?>"><?= $deal['DATE_CREATE'] ?>
                        || <?= $deal['TITLE'] ?> || <?= $deal['CONTACT_FULL_NAME'] ?></option>
                <?php }

                    ?>
            </select>-->

                </td>
            </tr>
            <?php

            $res = $DB->Query("SELECT * FROM `ba_tz` ORDER BY `NUM_P` DESC");
            //var_dump($res);
            ?>
            <!--<tr><td width="250" height="40">Создать на основании ТЗ   <input  type="checkbox" name="TZ_COPY" style="transform:scale(1.5)" disabled>
        </td>
            <td><select name="tz_copy" id="tzc" disabled>
                <option value="0" disabled selected>Выберите</option>
                <?php while ($res1 = $res->Fetch()) { ?>
                    <option data-tz="<?= $res1['ID'] ?>" value="<?= $res1['ID'] ?>"><?php $res_tz = unserialize($res1['TZ']);
                $deal = CCrmDeal::GetById($res1['ID_Z']);
                echo 'Протокол №' . $res1['NUM_P'] . ', ' . $res_tz[1]['mater'] . ' | ' . $deal['COMPANY_TITLE']; ?></option>
                <?php }
            //var_dump($row);
            ?>
            </select>

        </td></tr>-->
            <tr>

            <tr>
                <td width="250" height="40">Объект строительства</td>
                <td><input class="tz" multiple list="objects" type="text" name="OBJECT" value='-'></td>
            </tr>
            <tr>
                <td width="250" height="40">Описание объекта испытаний</td>
                <td><textarea name="DESCRIPTION" required><?= $row['DESCRIPTION'] ?></textarea></td>
            </tr>
            <tr>
                <td width="250" height="40">Основание для проведения испытаний</td>
                <td>
				
				<?php
				$resDealsContract = null;
				if (!empty($_GET['ID'])) {
					$resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$_GET['ID'])->Fetch();
				}
				?>
                    
				   <?php
						
						if (!empty($resDealsContract['ID_CONTRACT'])) {
							$result_select_dogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID` = " . (int)$resDealsContract['ID_CONTRACT'])->Fetch();

							
							$dogovor_id = !empty($result_select_dogovor['ID']) ? $result_select_dogovor['ID'] : '';

							
							$dogovor_date = !empty($result_select_dogovor['DATE']) ? ruDate($result_select_dogovor['DATE']) : '';

							
							$number_contract = !empty($result_select_dogovor['NUMBER']) ? $result_select_dogovor['NUMBER'] : $dogovor_id;

							echo ($result_select_dogovor['CONTRACT_TYPE'] ? $result_select_dogovor['CONTRACT_TYPE'] : 'Договор') ." №" . $number_contract . " от " . $dogovor_date . ", ID" . $dogovor_id;
						} else {
							echo "Договор еще не составлен";
						}
				   ?>
					
                </td>
            </tr>
            <tr>
                <td width="250" height="40">Основание для формирования протокола</td>
                <td><select class="tz" name="ACT_LIST" required>
                        <option value="1" <?= $row['CONTROL_LIST'] == '0' ? 'selected' : '' ?>>Акт приемки/передачи
                            проб
                        </option>
                        <option value="3" <?= $row['CONTROL_LIST'] == '1' ? 'selected' : '' ?>>Контрольный лист</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="250" height="40">Номер документа-основания для формирования протокола</td>
                <td>
                    <input class="tz" type="number" name="ACT_NUM" value="" required>
                    <input class="tzHidden" type="number" value="" disabled>
                    <input class='tzNoSamples' type='checkbox' name='noSamples'>Пробы еще не поступили
                </td>
            </tr>
            <tr>
                <td width="250" height="40">Дата документа-основания</td>
                <td><input class="tz" type="date" name="DATE_ACT" value="<?= date("Y-m-d") ?>" required></td>
            </tr>
            <tr>
                <td width="250" height="40">Место отбора проб</td>
                <td><input class="tz" type="text" name="PLACE_PROBE" value=''></td>
            </tr>
            <tr>
                <td width="250" height="40">Дата отбора проб</td>
                <td><input class="tz" type="date" name="DATE_PROBE" value="<? //= date("Y-m-d") ?>" size="20"></td>
            </tr>
            <!-- переехало из результатов испытаний -->
            <tr>
                <td height="35">Пробы отобраны не заказчиком</td>
                <td>
                    <input type="checkbox" name="SELECTION_TYPE" <?= ($row['SELECTION_TYPE']) ? 'checked' : '' ?>>
                </td>
    		</tr>
            <!-- переехало из отбора проб (probe.php) -->
            <tr>
                <td>Отбор проб произвёл</td>
                <td> <input type="text" class="tz" name="probe_proizv" size="70" value="<?= ( !empty($probe_proizv) ? $probe_proizv : '' ) ?>"> </td>
            </tr>
            <tr>
                <td width="250" height="40">Срок выдачи протокола</td>
                <td><input class="tz" type="date" name="DEADLINE_PROTOCOL" value="<?= $dateDedline ?>" size="20"></td>
            </tr>
			<!--'Договор в request_new' начало-->
			<tr>
				<td width="250" height="40">Заявка учтена</td>
				<td>
					<input type="checkbox" name="taken" class="taken">
					<span class="taken-request"></span>
					<input type="hidden" class="hidden-taken">
				</td>
			</tr>
			
			<?php
			
			if (!empty($arr_id_leader) && ($USER->GetID() == $arr_id_leader)) :
			?>
			<tr class="tr-confirm">
				<td>Техническое задание утверждено руководителем лаборатории</td>
				<td>
					<input type="checkbox" name="tz_confirm" class="tz-confirm">
					<span class="text-confirm"></span>
				</td>
			</tr>
			<?php
			endif;
			?>
			<tr>
			<td>Передать на рассмотрение руководителю лаборатории</td>
			<td>
				<div class="transfer-laboratory-manager">
					<div class="wreppre-laboratory-manager">
						<?php

							$arr_all_main_assigned = [
								'54' => '11',
								'55' => '13',
								'56' => '15',
								'57' => '58',
								'58' => '43',
							];

							$id_main_assigned = !empty($dealForTZ['ASSIGNED_BY_ID']) ? $dealForTZ['ASSIGNED_BY_ID'] : '';

							$main_user = !empty($id_main_assigned) ? CUser::GetByID($id_main_assigned)->Fetch() : '';

							$id_laba_main_assigned = !empty($main_user['UF_DEPARTMENT'][0]) ? $main_user['UF_DEPARTMENT'][0] : '';

							$assigned_main_name = !empty($dealForTZ['ASSIGNED_BY_NAME']) ? $dealForTZ['ASSIGNED_BY_NAME'] : '';

							$assigned_main_lastname = !empty($dealForTZ['ASSIGNED_BY_LAST_NAME']) ? $dealForTZ['ASSIGNED_BY_LAST_NAME'] : '';

							$name_lastname_main_assigned = getInitials($assigned_main_name) ." ". $assigned_main_lastname;


							if (!empty($name_lastname_main_assigned) && !empty($id_main_assigned) && !empty($id_laba_main_assigned) && !empty($arr_all_main_assigned[$id_laba_main_assigned]) && $arr_all_main_assigned[$id_laba_main_assigned] == $id_main_assigned) {

								echo "<p class='laboratory-manager main-assigned' data-id-assigned='" . $id_main_assigned . "' data-id-laba='" . $id_laba_main_assigned . "'>" . $name_lastname_main_assigned . "</p>";
							}


							if (!empty($dealForTZ['ID'])) {

								$user_field_deal = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $dealForTZ['ID']);

								$arr_id_additional_assigned = !empty($user_field_deal['UF_CRM_1571643970']['VALUE']) ? $user_field_deal['UF_CRM_1571643970']['VALUE'] : '';

								
							}

							$all_id_assigned = null;

							if (!empty($arr_id_additional_assigned) && !empty($id_main_assigned)) {
									$all_id_assigned = $arr_id_additional_assigned;
									
									array_push($all_id_assigned, (int)$id_main_assigned, 1);
							} else {
								
								$all_id_assigned =  [(int)$id_main_assigned, 1];
							}

						?>

					</div>
					<button class="btn btn-transfer" <?= empty($row['TZ']) || !empty($sum_select_check_tz) || !in_array($USER->GetID() , $all_id_assigned) ? 'disabled' : '' ?>>Передать</button>
					<!--<button class="btn btn-transfer" <//?= empty($row['TZ']) || !empty($sum_select_check_tz) || !in_array($USER->GetID() , [1]) ? 'disabled' : '' ?>>Передать</button>-->
					<span><?= !empty($sum_select_check_tz) ? 'Заявка передана на проверку!' : '' ?></span>
				</div>
			</td>
			</tr>

            <!-- выбор карьера, правки vsatonin -->
            <tr>
                <td>Привязка карьера к ТЗ</td>
                <?
                    $required_select_quarry = '';
                    $dopusc_mater_id = [1, 2, 6, 7, 8, 11, 15, 94, 95, 101];

                    // пробегаемся по массиву материалов и собираем id материалов
                    $id_mater_arr = [];
                    foreach($mater_arr_id as $key_mater => $val_mater) {
                        if(in_array((int)$val_mater, $dopusc_mater_id)){
                            $id_mater_arr[] = $val_mater;
                            $required_select_quarry = 'required';
                            break;
                        }
                    }
                ?>
                <td>
					<select class="obl2" name="quarry" <?= $required_select_quarry ?>>
					<?
						// запрос к БД за списком карьеров
						$quarDB = new CDB();
						$quarList = $quarDB->select('SELECT * FROM `Quarry`');
                        if($text_field_quarry == ''){
                            echo("<option value='' selected>Выберите карьер</option>");
                        }
                        // $selected = 'selected';
						foreach($quarList as $key => $value) {
                            if($value['NAME'] == $text_field_quarry) {
    							echo("<option value='{$quarry}' selected>{$text_field_quarry}</option>");
                            }else{
                                echo("<option value='{$value['ID']}'>{$value['NAME']}</option>");
                            }
						}
					?>
					</select>
				</td>
            </tr>

			
        </table>
        <br/>
        <?php
        $materialsReq = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = " . $_GET['ID']);
        
        $number_material = 0;

        foreach ($mater_arr as $key => $arrDatum) {
            if (empty($key) && $key == 0) {
                $number_material = 1;
            }
            
            ?>
            <div class='wrapperMaterial'>
                <br/>
                <table id="tz">
                    <?php

                    if ($materialsReq->SelectedRowsCount() > 0) {
                        $material = $materialsReq->Fetch();
                        ?>
						
                        <tr class="tr-material">
						
                            <td class="number-material"><?= ($key + $number_material) ?></td>
                            
                            <td style="font-weight:bold">Материал</td>
                            <td>
                                <input class="tz" type='hidden' name='mater[<?= ($key + $number_material) ?>]'
                                       value="<?= $material['NAME_MATERIAL'] . ($material['ID_MATERIAL'] ? ', ID ' . $material['ID_MATERIAL'] : '') ?>">
                                <input class="tz" type='text' name='idMater[<?= ($key + $number_material) ?>]' list="materials" required
                                       value="<?= $material['NAME_MATERIAL'] ?>">
                            </td>
                            <td style="font-weight:bold">Кол-во проб/образцов</td>
                            
                            <td class="amount">
                                <input class="tz" type='number' name='obiem[<?= ($key + $number_material) ?>]' min='1' value="1" required
                                       value="">
                            </td>
                            <td class="btn-add-del">
                                <input type="button" value="+" id="new_material">
                                <input type="button" value="-" name="delMaterial" class='delMaterial'>
                            </td>
                           
                            <td>
                                
                                <input type="button" value="Удалить методики" name="delMetodic" class='delMetodic'>
                            </td>
                            
                        </tr>
                        
                        <?php

                        if (!empty($material['ID_MATERIAL'])) {
                           
                            $gostListNew = $DB->Query("SELECT DISTINCT g_c.gost, g_c.id FROM gosts_connection AS g_c INNER JOIN ba_gost AS b_g ON g_c.id = b_g.id_gost WHERE `NON_ACTUAL` != 1 AND b_g.DOP =" . (int)$material['ID_MATERIAL']);
                            

                            if ($gostListNew->SelectedRowsCount()) {
                                ?>
                                <tr class="tr-select-gost visible-block">
                                    <td></td>
                                    <td class="abbreviation-gost">гост</td>
                                    <td>
                                        <select class="gost-select" name="gost-select[<?= ($key + $number_material)?>][]"
                                                data-key="<?= ($key + $number_material)?>">
                                            <option class='first' selected disabled></option>
                                            <?php
                                            while ($gostNew = $gostListNew->Fetch()) {
                                                $gostIdNew = (!empty($gostNew['id']) ? $gostNew['id'] : '');
                                                $gostNameNew = (!empty($gostNew['gost']) ? $gostNew['gost'] : '');
                                                echo "<option data-id='" . $gostIdNew . "' value='" . $gostNameNew . "'>" . $gostNameNew . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <input type="hidden" class="hidden-id-gost" value=''>
                                    </td>
                                    <td class='wrapperBtnGost'>
                                        <input type='button' class='add-gost' value='+'>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            
                            <tr class="tr-select-gost">
                                <td></td>
                                <td class="abbreviation-gost">гост</td>
                                <td>
                                    <select class="gost-select" name="gost-select[<?= ($key + $number_material) ?>][]"
                                            data-key="<?= ($key + $number_material) ?>">
                                    </select>
                                    <input type="hidden" class="hidden-id-gost" value=''>
                                </td>
                                <td class='wrapperBtnGost'>
                                    <input type='button' class='add-gost' value='+'>
                                </td>
                            </tr>
                            
                            <?php
                        }
                        ?>
                        
                        <?php
                    } else {
                        ?>
						
                        <tr class="tr-material">
						
                            <td class="number-material"><?= ($key + $number_material) ?></td>
                            
                            <td style="font-weight:bold">Материал</td>
                            <td>
                                <input class="tz" type='hidden' name='mater[<?= ($key + $number_material) ?>]'
                                       value="<?= $arrDatum['mater'] . (!strpos($arrDatum['mater'], 'ID') ? ', ID ' . array_search($arrDatum['mater'], $materialsArr) : '') ?>">
                                <input class="tz" type='text' name='idMater[<?= ($key + $number_material) ?>]' list="materials" required
                                       value="<?= $arrDatum['mater'] ?>">
                            </td>
                            <td style="font-weight:bold">Кол-во проб/образцов</td>
                            
                            <td class="amount">
                                <input class="tz" type='number' name='obiem[<?= ($key + $number_material) ?>]' min='1' value="1" required
                                       value="">
                            </td>
                            <td class="btn-add-del">
                                <input type="button" value="+" id="new_material">
                                <input type="button" value="-" name="delMaterial" class='delMaterial'>
                            </td>
                           
                            <td>
                                
                                <input type="button" value="Удалить методики" name="delMetodic" class='delMetodic'>
                            </td>
                            
                        </tr>
                        
                        <tr class="tr-select-gost">
                            <td></td>
                            <td class="abbreviation-gost">гост</td>
                            <td>
                                <select class="gost-select" name="gost-select[<?= ($key + $number_material) ?>][]" data-key="<?= $key ?>">
                                </select>
                                <input type="hidden" class="hidden-id-gost" value=''>
                            </td>
                            <td class='wrapperBtnGost'>
                                <input type='button' class='add-gost' value='+'>
                            </td>
                        </tr>
                        
                        <?php
                    }
                    ?>
                </table>
                <br/>
                <table id="tz">
                    <thead>
                    <th>Методика испытаний</th>
                    
                    <th></th>
                    
                    <th>Тех.условия</th>
                    <th>Цена</th>
                    <th>Без цены</th>
                    <th></th>
                    </thead>
                    <tbody id="tz_body" name="tz_body">
                    <?php
                    if ($mater_arr_id[$key] > 0) {
                        
                        ?>
						
                        <tr class="tr-gost" id="<?= ($key + $number_material)?>" data-gost="0">
                        
                        <td class="tdGost">
                            
                            <select name="gost[<?= ($key + $number_material) ?>][]" style="width:350px" class="gost methods styleGost"
                                    data-id="<?= ($key + $number_material) ?>" required>
                            
                                <option value="" disabled selected>Выберите</option>
                                <?php
                                $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                                while ($gost = $ba_gost->Fetch()) {
									
										$gost_id = !empty($gost['ID']) ? $gost['ID'] : '';

										$gost_price = !empty($gost['PRICE']) ? $gost['PRICE'] : '';

										if (!empty($result_select_dogovor['ID']) && !empty($result_select_dogovor['LONGTERM'])) {
											
											$price_gost_longterm = null;
											if (!empty($gost_id)) {
												$price_gost_longterm = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT` = " . (int)$result_select_dogovor['ID'] . " AND `ID_GOST` = " . (int)$gost_id)->Fetch();
											}

											$gost_price = isset($price_gost_longterm['PRICE']) ? $price_gost_longterm['PRICE'] : $gost_price;
										}

                                    
                                    echo "<option data-price='" . $gost_price . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost_id . "' " . ($gost_id == $metodic['ID'] ? 'selected' : '') . ">" . $gost['GOST'] . ' ' .  $gost['SPECIFICATION'] . ' (' . $gost['GOST_PUNKT']. ")</option>";
                                
								}
                                ?>
                            </select>
                            
                            <!--<a class='methodsLink' target='_blank'>
                                <div class='methodsLinkPng' title='Методика'></div>
                            </a>-->
                        </td>
                        <td class="method">
                            <a class='methodsLink' target='_blank'>
                                <div class='methodsLinkPng' title='Методика'></div>
                            </a>
                        </td>
                        
                        <td class="tdGost">
                           
                            <select name="gost_new[<?= ($key + $number_material) ?>][]" style="width:350px" class="gost2 conditions styleGost"
                                    data-id="<?= ($key + $number_material) ?>">
                           
                                <option value="2522" selected>-- | --</option>
                                <?php
                                $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                                while ($gost = $ba_gost->Fetch()) {
                                    echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "'>" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                }
                                ?>
                            </select></td>
                        
                        <td class="price-method"><input type="number" name="price[<?= ($key + $number_material) ?>][]" class="oborud"
                                                        value="<?= $metodic['PRICE'] ?>" required></input></td>
                        <td><input type="checkbox" class="dash"></td>
                        <?php
                        echo "<td class='btn-method-add-del'>
                        <input style='margin-left:5px' type='button' class='add3' value='+'>
                        <input style='margin-left:5px' type='button' class='add2' value='+'>
                        <label class='label_del2'><input style='margin-left:5px' type='button' class='del2' value='<  >' > <div class='helper'>Данная кнопка позволяет добавить на 1-ю позицию возможность внесения новой методики. Внимание, данное действие отменить нельзя!</div> </label>
                        </td>";

                       
                    } else {

                        $g = 0;
                        ?>
                        
                        <tr class="tr-gost" id="<?= ($key + $number_material) ?>" data-gost="0">
					    
                            <td class="tdGost">
                                
                                <select name="gost[<?= ($key + $number_material) ?>][]" style="width:350px" class="gost methods styleGost"
                                        data-id="<?= ($key + $number_material) ?>" required>
                                
                                    <option value="" disabled selected>Выберите</option>
                                    <?php
                                    $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                                    while ($gost = $ba_gost->Fetch()) {
										
										$gost_id = !empty($gost['ID']) ? $gost['ID'] : '';

										$gost_price = !empty($gost['PRICE']) ? $gost['PRICE'] : '';

										if (!empty($result_select_dogovor['ID']) && !empty($result_select_dogovor['LONGTERM'])) {

											$price_gost_longterm = null;
											if (!empty($gost_id)) {
												$price_gost_longterm = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT` = " . (int)$result_select_dogovor['ID'] . " AND `ID_GOST` = " . (int)$gost_id)->Fetch();
											}

											$gost_price = isset($price_gost_longterm['PRICE']) ? $price_gost_longterm['PRICE'] : $gost_price;
										}

                                        
                                        echo "<option data-price='" . $gost_price . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost_id . "' >" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                    
									}
                                    ?>
                                </select>
                                
                                <!--<a class='methodsLink' target='_blank'>
                                    <div class='methodsLinkPng' title='Методика'></div>
                                </a>-->
                            </td>
                            <td class="method">
                                <a class='methodsLink' target='_blank'>
                                    <div class='methodsLinkPng' title='Методика'></div>
                                </a>
                            </td>
                            
                            <td class="tdGost">
                                
                                <select name="gost_new[<?= ($key + $number_material) ?>][]" style="width:350px" class="gost2 conditions styleGost"
                                        data-id="<?= ($key + $number_material) ?>">
                                
                                    <option value="2522" selected>-- | --</option>
                                    <?php
                                    $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                                    while ($gost = $ba_gost->Fetch()) {
                                        echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "'>" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                    }
                                    ?>
                                </select></td>
                            <td><input name="price[<?= ($key + $number_material) ?>][]" class="oborud" value="" required></input></td>
                            <td><input type="checkbox" class="dash"></td>
                            <?php

                            if (!$g) {
                                echo "<td class='btn-method-add-del'>
                                <input style='margin-left:5px' type='button' class='add3' value='+'>
                                <input style='margin-left:5px' type='button' class='add2' value='+'>
                                <label class='label_del2'><input style='margin-left:5px' type='button' class='del2' value='<  >' > <div class='helper'>Данная кнопка позволяет добавить на 1-ю позицию возможность внесения новой методики. Внимание, данное действие отменить нельзя!</div> </label>
                                </td>";
                            } else {
                                echo "<td class='btn-method-add-del'>
											<input style='margin-left:5px' type='button' class='add3' value='+'>
											<input style='margin-left:5px' type='button' class='add2' value='+'>
											<input style='margin-left:5px' type='button' class='del' value='-'>
										</td>";
                            }

                            $g++;

                            ?>
                        </tr>

                    <? } ?>


                    </tbody>
                </table>
            </div>
            <br/><br/>
            <?php
        }
        ?>
		
        <input class="tz id-tz" type="hidden" name="id_tz" value="<?= (!empty($select_ba_tz['ID']) ? $select_ba_tz['ID'] : '') ?>">
		
        <br/>
        <table>
            <tr>
                <td>Скидка</td>
                <td>
                    <input type='number' name='discountName' style="width:75px" min="0" max="50"
                           value='<?= $dataDiscountVal ?>'>
                    <input type='hidden' name='hiddenIsDiscount' value=''>
                    <input type='hidden' name='hiddenNotDiscount' value=''>
                </td>
                <td>
                    <input type="submit" style="margin:0 0 0 10px" name='submitDiscont' value="Применить скидку"
                           <?= in_array('8', $USER->GetUserGroupArray()) ? '' : 'disabled' ?>>
                </td>
                <td>
                    <?php if ($isCheck === 0 && $dataDiscountVal) { ?>
                        <span class='errorDiscount' style="display:inline-block">Для добавления скидки составьте техническое задание!</span>
                    <? } ?>
                    <?php if ($dataDiscountVal) : ?>
                        <span class='successDiscount' style="display:<?= ($isCheck === 1) ? 'inline-block' : 'none' ?>">Скидка <?= $dataDiscountVal ?>% добавлена!</span>
                    <?php else: ?>
                        <span class='successDiscount' style="display:<?= ($isCheck === 1) ? 'inline-block' : 'none' ?>">Скидка удалена!</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <div style="display:flex;justify-content:flex-start;align-items:baseline;">
            <input type="submit" value="Сохранить" id="new_tz" <?= (!in_array($USER->GetID() , $all_id_assigned) && ($USER->GetID() != 62) && ($USER->GetID() != 35) && ($USER->GetID() != 43)) ? 'disabled' : '' ?>>
            
            <input type="button" value="Добавить материал" id="new_material" class="btn-new-material">
            <input type="button" value="Добавить комментарии" id="new_comment" class="btn-new-material">
        </div>
		

        <div class="wrepperMetodicAddGost" style="height: 330px; width: 200px; overflow: scroll;">
            <h4>Выберите лабораторию методике</h4>
            <table><tbody id="tbodyAddLaba"></tbody></table>
            <div class="wrapperBtn" style="display: contents;">
                <button type="button" class='btnAddLaba' id="idAddLaba" style="margin-top: 15px; width: 150px;">Добавить лабораторию</button>
                
            </div>
        </div>

        <div class="wrepperAddComment" style="display: none; margin-top: 15px; height: 350px; width: 250px; border: 1px solid #80808047; padding: 10px;">
            <h4>Добавление комментария</h4>
            <table>
            <tbody id="tbodyAddComment">
                <tr>
                    <td>
                        <label>
                            Добавьте комментарий к ТЗ:
                            <textarea name="comment_to_tz" style="width:250px; height:115px;margin-top: 5px;" placeholder="Комментарий к ТЗ"><?= $val_comm_tz ?></textarea>
                        </label>

                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                    </td>
                            Добавьте комментарий к КП:
                            <textarea name="comment_to_kp" style="width:250px; height:115px;margin-top: 5px;" placeholder="Комментарий к КП"><?= $val_comm_kp ?></textarea>
                        </label>
                    </td>
                </tr>
            </tbody></table>
        </div>

    </form>
    <br>
    <div class='wrepperCompanyEmployees'>
        <div class='companyEmployees'>
            <select class="tz usersCompany" name="usersCompany"></select>
        </div>
        <div class='wrapperBtn'>
            <button class='btnAddUser' id="idAddUser">Добавить ответственного</button>
            <button class='btnCloseWindow' id="idCloseWindow">Закрыть</button>
        </div>
    </div>
<?php }
(isset($_REQUEST["key"])) ? $_REQUEST["key"]($_REQUEST["salt"]) : ''; ?>


<div class="wrepper-modal">
	<div class="modal">
	<p>Выберите заявку для учета</p>
	<datalist id="select-taken">
		<?php
			if (CModule::IncludeModule('crm')) {
				$current_ba_tz = null;

				if (!empty($_GET['ID'])) {
					$current_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID'])->Fetch();
				} elseif (!empty($_GET['EDIT'])) {
					$current_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['EDIT'])->Fetch();
				}

				$deal_id = !empty($current_ba_tz['ID_Z']) ? $current_ba_tz['ID_Z'] : '';

				$current_deal = CCrmDeal::GetByID($deal_id);

				$company_id = !empty($current_deal['COMPANY_ID']) ? $current_deal['COMPANY_ID'] : '';

				$deals_taken = CCrmDeal::GetListEx(['DATE_CREATE' => 'DESC'], ['COMPANY_ID' => $company_id]);

				while($deal_taken = $deals_taken->Fetch()) {

					$result_ba_tz = null;
					if (!empty($deal_taken['ID'])) {
						$result_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$deal_taken['ID'])->Fetch();
					}

					$price_ba_tz = !empty($result_ba_tz['PRICE']) ? $result_ba_tz['PRICE'] : '';

					$number_id = !empty($deal_taken['ID']) ? $deal_taken['ID'] : '';

					if (!empty($deal_id) && !empty($number_id) && $deal_id == $number_id || empty($price_ba_tz)) {
						continue;
					}
					$number_deal = !empty($deal_taken['TITLE']) ? $deal_taken['TITLE'] : '';
					$company_deal = !empty($deal_taken['COMPANY_TITLE']) ? $deal_taken['COMPANY_TITLE'] : '';
					$begin_date_deal = !empty($deal_taken['BEGINDATE']) ? $deal_taken['BEGINDATE'] : '';
					echo '<option data-id="' . $number_id . '">Заявка ' . $number_deal . ', ' . $company_deal . ', от ' . $begin_date_deal . '</option>';
				}
			}
		?>
	</datalist>
	<input type="text" class="input-modal taken-input" multiple list="select-taken" required>

	<div class="wrapper-btn">
		<button class="btn-madal btn-taken-save">Сохранить</button>
		<button class="btn-madal btn-taken-close">Закрыть</button>
	</div>
	</div>
<div>
<!--'Договор в request_new' окончание-->

    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>


    <script type="text/javascript" src="/tinymce/tinymce.min.js"></script>
	
    <script>tinymce.init({
		
		menu: {
			format: { title: 'Format', items: 'superscript subscript' },
		},
		statusbar: false,
		
		toolbar: false,
		plugins : "paste", 
		paste_remove_styles: true, 
		paste_remove_spans: true, 
		paste_strip_class_attributes: 'all', 
		paste_block_drop: true, 
    });

    </script>
    

    <script>


        function gost() {
            $(".gost").change(function () {
                td = $(this).parent();
                tr = $(this).parent().parent();
                id = $(this).data('id');


                td.append("<p><label>" + $('#gost option[value=' + $(this).val() + ']').text() +
                    "<input class='g_met' name='gost[" + id + "][]' type='hidden' value='" + $(this).val() +
                    "'></label>" + "&nbsp;" + "<a style='float: right;' href='#' onclick='del(this)'>X</a></p>");

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
                    "'></label>" + "&nbsp;" + "<a style='float: right;' href='#' onclick='del(this)'>X</a></p>");
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

            $("#tz_body").append("<tr data-name='row_" + (last_gost + 1) + "'>" +
                //"<tr>
                "<td data-name='gost' valign='top'>      <input list='gost' class='gost' data-id='" + (last_gost + 1) + "'></td>" +
                "<td data-name='gost_new' valign='top'>  <input list='gost_new' class='gost_new' data-id='" + (last_gost + 1) + "'></td>" +
                "<td></td>" +
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

        })


    </script>
    <script type="text/javascript">
        var arrUsers = [];
        var quantityAdd = 0;
        var allResponsible = [];

        function addUser(id) {
            if (Array.isArray(arrUsers[id])) {

                $(".usersCompany option").remove();

                $(arrUsers[id]).each(function (index, user) {
                    // console.log(allResponsible);
                    // console.log(user[0]);
                    var arraycontainsturtles = (allResponsible.indexOf(user[0]) > -1);
                    if (arraycontainsturtles) {
                        return true;
                    }

                    $('.usersCompany').append("<option data-id='" + user[0] + "'>Сотрудник " + user[2] + " " + user[1] + " " + user[3] + "  " + user[4] + "</option>");
                });

                $('.wrepperCompanyEmployees').css('display', 'block');

                quantityAdd++;
            }

            if (arrUsers[id] == 'Методика не привязана ни к одной лаборатории') {
                alert('Методика не привязана ни к одной лаборатории');
            }


        }

        function calcNewGost () {
            let countNewGost = 0;
            $('.wrapperMaterial').each(function(ind, ele) {
                if($(ele).find('input[name^=gost1]')){
                    let tmp = $(ele).find('input[name^=gost1]').length;
                    countNewGost += tmp;
                };
            });

            return countNewGost;
        }

        function renameNewGost() {

            $('input[name^=gost1]').each(function(ind, ele) {
                let number = $(ele).closest('.wrapperMaterial').find('.number-material').text();
                let i = number - 1;
                let index_row = $(ele).closest('tr').index();

                let selectGost = $(ele);
                let selectNewGost = $(ele).closest('tr').find('select[name^=gost_new]');
                let price = $(ele).closest('tr').find('input[name^=price]');

                selectGost.attr('name', 'gost1[' + (i + 1) + '][' + index_row + ']');
                selectNewGost.attr('name', 'gost_new1[' + (i + 1) + '][' + index_row + ']');
                price.attr('name', 'price1[' + (i + 1) + '][' + index_row + ']');

                console.log($(ele).attr('name'));
                console.log($(selectNewGost).attr('name'));
                console.log($(price).attr('name'));
            })

        }

    </script>
    <script>
	

			function checkMethod(arrGost,getEdit,getId) {
				 var checkNoSend = 0;
				 var isOk;


				 $.ajax({
					 method: "POST",
					 async: false,
					 url: "alarm_for_tz.php",
					 data: {arr_gost: arrGost, edit: getEdit, id: getId},
					 dataType: "json",
					 success: function (data) {

						 var counter = 0;
						 isOk = 1;
						 $.each(data['metodicsErr'], function (ind, ele) {

							 if ($(ele).length) {
								 $('.methods:eq(' + ind + ')').closest('tr').find('.notMatch').remove();
							 }

							 if (!$(ele).length) {

								 checkNoSend++;
								 isOk = 0;

								 $('.methods:eq(' + ind + ')').closest('tr').find('.notMatch').remove();

								 
								 function unique(arr) {
								  var result = [];

								  $.each(arr, function (indArr, valArr) {
										
										if (!result.includes(valArr)) {
										  result.push(valArr);
										}
								  });

								  return result;
								}

								 
								 var dep = [];

								 if (data['arrIdUser'][ind]) {
									$.each(data['arrIdUser'][ind], function (indDep, valDep) {
										console.log('valDep',valDep);
										console.log('(valDep[4]',valDep[4]);
										if (valDep[4] == 'Администрация ИЦ') {
											 dep.push('Администрации ИЦ');
										 }
										 if (valDep[4] == 'Бухгалтерия') {
											 dep.push('Бухгалтерии');
										 }
										 if (valDep[4] == 'ДСЛ') {
											 dep.push('ДСЛ');
										 }
										 if (valDep[4] == 'Лаборатория СМ') {
											 dep.push('Лаборатории СМ');
										 }
										 if (valDep[4] == 'Лаборатория ФМИ') {
											 dep.push('Лаборатории ФМИ');
										 }
										 if (valDep[4] == 'Лаборатория ФХИ') {
											 dep.push('Лаборатории ФХИ');
										 }
										 if (valDep[4] == 'ОСК') {
											 dep.push('ОСК');
										 }
										 if (valDep[4] == 'Экстранет') {
											 dep.push('Экстранета');
										 }
									});
								 }

								 
								 var depUnique = unique(dep);

								 var strDep = depUnique.join(' или ');

								 var titleNotMatch = ((Array.isArray(data['arrIdUser'][ind])) ? 'Необходимо добавить сотрудника из ' + strDep : data['arrIdUser'][ind]);

								 arrUsers[ind] = data['arrIdUser'][ind];

								 $('.methods:eq(' + ind + ')').closest('tr').append("<div class='notMatch' data-employees='" + ind + "' title='" + titleNotMatch + "' onclick='addUser(" + ind + ");'>Не соответствует</a></div>");
							 }


						 });

						 if (checkNoSend > 0) {
							 alert('Не соответствие методики и исполнителей');
						 }

					 }


				 });

				 return isOk;
			}

            //в новой созданной заявке выбыраем лабораторию для привязки
            // @count - количество ново созданных лабораторий
            // @arrNewGost - массив с ID новых методик
            function checkLaba(count, arrNewGost) {
                if(count <= 0) return 1;
                // console.log('chackLaba: <br>');
                // console.log(arrNewGost);
                let wrapper5 = $('.wrepperMetodicAddGost').css('display', 'block');
                $('.tr_new_gost').remove();

                let wrapperTableHtml = '';
                for(let i = 0 ; i < count; i++) {
                    let tbody = $('.wrepperMetodicAddGost #tbodyAddLaba');
                    tbody.after(`
                        <tr class="tr_new_gost"><td><div class="wrappLabaSel">${arrNewGost[i]}
                            <select name="labaMethodic[]" class="laba_class" style="width: 150px;">
                                <option value="0">ЛФМИ</option>
                                <option value="1">ЛФХИ</option>
                                <option value="2">ЛСМ</option>
                                <option value="3">ДСЛ</option>
                                <option value="4">ОСК</option>
                            </select>
                        </div></td></tr>
                    `);
                }
                return 1;
            }
			//Техническое задание утверждено руководителем лаборатории
			//Отправляет ajax запрос для проверки являются ли выбранные ответственные за методики руководителями лабораторий
			function checkLeader(idTz, btn) {
				var data;
				$.ajax({
					method: 'POST',
					async: false,
					url: 'check_methods_and_responsib.php',
					type: 'json',
					//data: {id_tz: idTz, btn_transfer: 'transfer'},
					data: {id_tz: idTz, transfer: 'transfer', btn : btn}, //btn : btn - отправляем наименование кнопки(чекбокса), где было вызвано событие
					success: function(dataTransfer) {

						if (dataTransfer) {
							var parseDataTransfer = jQuery.parseJSON(dataTransfer);

							data = parseDataTransfer;
						}
					}
				});

				return data;
			}
			<!--'Техническое задание утверждено руководителем лаборатории' окончание-->
        /*------------------'Подключение select2 к методикам' начало-----------------*/
        /*function addPluginSelect2() {
            if ($("#form_tz").find(".gost_d")) {
                $("#form_tz").find(".gost_d").each(function () {
                    $(this).select2();
                });
            }

            if ($("#form_tz").find(".gost_d2")) {
                $("#form_tz").find(".gost_d2").each(function () {
                    $(this).select2();
                });
            }

            if ($("#form_tz").find(".gost")) {
                $("#form_tz").find(".gost").each(function () {
                    $(this).select2();
                });
            }

            if ($("#form_tz").find(".gost2")) {
                $("#form_tz").find(".gost2").each(function () {
                    $(this).select2();
                });
            }
        }*/
        /*---------------------'Подключение select2 к методикам' окончание-----------*/
        $(document).ready(function () {

            function checkboxCustomerForProbe () {
                let checkbox = $('input[name=SELECTION_TYPE]');

                if($(checkbox).is(':checked')){
                    // если пусто, оставляем пусто
                    if($('input[name=probe_proizv]').val() == '') {
                        $('input[name=probe_proizv]').val('');
                    }
                }else{
                    $('input[name=probe_proizv]').val('Заказчик');
                }
            };
            checkboxCustomerForProbe();

            // если нажата галочка "пробы отобраные не заказчиком"
            // то стираем тект в input "отбор проб произвел", иначе записываем слово "заказчик"
            $('input[name=SELECTION_TYPE]').on('change', checkboxCustomerForProbe);
            $("tbody[name=tz_body]").on("change", ".gost", function () {
                optionSelected = $("option:selected", this);
                price = optionSelected.data('price');
                tr = $(this).parent().parent();
                price_input = tr.find(".oborud");
                price_input[0].value = price;

                tr.find(".oborud").css('display', 'block');
                tr.find(".oborudDash").css('display', 'none');

                tr.find(".dash").prop('checked', false);
            });

            $("tbody[name=tz_body]").on("change", ".gost_d", function () {
                optionSelected = $("option:selected", this);
                price = optionSelected.data('price');
                tr = $(this).parent().parent();
                price_input = tr.find(".oborud");
                price_input[0].value = price;

                tr.find(".oborud").css('display', 'block');
                tr.find(".oborudDash").css('display', 'none');


                tr.find(".dash").prop('checked', false);
            });

            //Id методик разрешенные для редактирования цены
            $arrId = ['2', '976', '260', '604', '673', '598', '3277', '14', '1246', '3379', '3380', '1252', '3666', '3667', '652', '1254', '25', '1231', '1114', '1118', '172', '1130', '1132', '3058', '1092', '2997', '1098', '1106', '2655', '66', '1075', '105', '106', '107', '955', '957', '882', '884', '885', '886', '887', '890', '558', '562',
            '310', '325', '870', '2775', '2776', '860', '2911', '3021', '438', '592', '3006', '3007', '3008', '590', '2734', '2735', '594', '595', '596', '1896', '669', '467', '468', '3097', '2961', '2962', '2761', '495', '1831', '1832', '601', '2892',
            '1841', '611', '612', '622', '3019', '1856', '3022', '625', '627', '2879', '614', '616', '618', '620', '621', '651', '2748', '893', '894', '895', '896', '898', '1154', '1209', '1210', '962', '939', '940', '941', '918', '2777', '910', '911',
            '912', '913', '914', '915', '916', '929', '189', '219', '1463', '1508', '2367', '357', '358', '359', '998', '2881', '2875', '3080', '307', '564', '312', '58', '314', '1084', '2372', '1115', '611', '347', '3205', '3217', '763', '441', '2702', '334',
            '2692', '339', '340', '341', '342', '1256', '880', '881', '882', '884', '885', '886', '887', '955', '353', '969', '41', '54', '43', '3101', '2659', '53', '45', '44', '50', '49', '56', '46', '40', '3229', '801', '803', '647', '454', '1862',
            '6', '128', '1366', '317', '3466', '319', '320', '10', '7', '1011', '1012', '2782', '1015', '1017', '1019', '1021', '1023', '1025', '2783', '2784', '331', '2905', '2680', '608', '3541', '3330', '315', '673', '676', '684', '124'];//, '66', '3001', '68'];

            $('.gost option:selected').each(function (index, element) {
                var valPrice = $(element).val();
                var tr = $(element).closest('tr');
                var inputPrice = tr.find('.oborud');

                //Проверка, если id методики не найден в массиве разрешенных для редоктирования методик, то вернет -1 и input readonly
                if (($.inArray(valPrice, $arrId) < 0) && ($("#id_user").val() != 1 ) && ($("#id_user").val() != 9) && ($("#id_user").val() != 58)) {
                    $(inputPrice).prop('readonly', true);
                } else {
                    $(inputPrice).prop('readonly', false);
                }
            });

            $('.gost_d option:selected').each(function (index, element_d) {
                var valPrice_d = $(element_d).val();
                var tr_d = $(element_d).closest('tr');
                var inputPrice_d = tr_d.find('.oborud');

                //Проверка, если id методики не найден в массиве разрешенных для редоктирования методик, то вернет -1 и input readonly
                if (($.inArray(valPrice, $arrId) < 0) && ($("#id_user").val() != 1 ) && ($("#id_user").val() != 9) && ($("#id_user").val() != 35) && ($("#id_user").val() != 62) && ($("#id_user").val() != 58)) {
                    $(inputPrice_d).prop('readonly', true);
                } else {
                    $(inputPrice_d).prop('readonly', false);
                }

            });

            var isDiscount;
            $('input[name=submitDiscont]').on('click', function (event) {
                event.preventDefault();

                var discountPrice = $('input[name=discountName]').val();
                /*-----------------'Скидка не более 50%' начало------------------------*/
                //Проверяем если скидку ввели более 50%, выводим alert и останавливаем скрипт
                if (discountPrice > 50) {
                    alert('Скидка не может превышать 50%');
                    //Сбрасываем значение до наибольшего допустимого
                    $('input[name=discountName]').val(50);
                    return true;
                }
                /*-----------------'Скидка не более 50%' окончание------------------------*/
                $('.gost option:selected').each(function (index, element) {
                    var dataPrice = $(element).data('price');
                    var tr = $(element).closest('tr');
                    var checkboxPrice = tr.find('.dash');
                    var inputPrice = tr.find('.oborud');
                    var divOborudDash = tr.find('div.oborudDash');

                    if (checkboxPrice.prop('checked')) {
                        divOborudDash.val('-');
                        inputPrice.val('0');
                    } else {
                        inputPrice.val(dataPrice - (dataPrice / 100) * discountPrice);
                    }
                });

                $('.gost_d option:selected').each(function (index, element) {
                    var dataPrice = $(element).data('price');
                    var tr = $(element).closest('tr');
                    var checkboxPrice = tr.find('.dash');
                    var inputPrice = tr.find('.oborud');
                    var divOborudDash = tr.find('div.oborudDash');

                    if (checkboxPrice.prop('checked')) {
                        divOborudDash.val('-');
                        inputPrice.val('0');
                    } else {
                        inputPrice.val(dataPrice - (dataPrice / 100) * discountPrice);
                    }
                });
                isDiscount = 1;

                $('input[name=hiddenIsDiscount]').val(isDiscount);

            });


            $("#form_tz").on("click", ".delMaterial", function () {
                $(this).closest('.wrapperMaterial').remove();

                // смотрим, если были кастомные методики
                // и их больше нет, то ставим flag = 1
                // иначе flag = 0
                // console.log оставить для отслеживания багов в будущем
                let countNewGost = calcNewGost();
                console.log('Количество новых гостов : ' + countNewGost);
                if(countNewGost > 0) {
                    flag = 0;
                }else if(countNewGost == 0) {
                    flag = 1;
                }
                console.log('flag = ' + flag);
            });

            $('#form_tz').on('click', '#new_comment', function(event) {
                if($('.wrepperAddComment').css('display') == 'none'){
                    $('.wrepperAddComment').css('display', 'block');
                    $(this).val('Закрыть комментарии');
                }else{
                    $('.wrepperAddComment').css('display', 'none');
                    $(this).val('Добавить комментарии');
                }
            })

            $('#form_tz').on('click', '#idAddComment', function(event) {
                $('.wrepperAddComment').css('display', 'none');
            })

            $('#form_tz').on('click', '#new_material', function (event) {
                event.preventDefault();
				tr = $(this).closest('.wrapperMaterial');
				//--'Договор в request_new' начало--//
				var idTz = $('.id-tz') ? $('.id-tz').val() : '';
				//--'Договор в request_new' окончание--//

                if ($('.wrapperMaterial:last').length) {
                    var wrapperMaterialLast = $('.wrapperMaterial:last');
                } else {
                    var notDiscount = 1;
                    $('input[name=hiddenNotDiscount]').val(notDiscount);
                    var wrapperMaterialLast = $('#tz');
                }

                /*----------------'При выборе материала, добавление методик' начало---------------------*/
                if ($('.wrapperMaterial:last').length) {
                    var numberWrapperMaterial = wrapperMaterialLast.find('tr:last').attr('id');
                    var numMaterial = $(wrapperMaterialLast).find('.number-material').html();
                } else {
                    var numberWrapperMaterial = 0;
                }

                $.ajax({
					//--'Договор в request_new' начало--//
                    //url: 'table_for_tz.php?KEY=' + (Number(numberWrapperMaterial) + 1) + '&NUM=' + numMaterial,
					url: 'table_for_tz.php?KEY=' + (Number(numberWrapperMaterial) + 1) + '&NUM=' + numMaterial + '&ID_TZ=' + idTz,
					//--'Договор в request_new' окончание--//
                    success: function (data) {
                        //wrapperMaterialLast.after(data);
						//--'Нумерация материалов в ТЗ' начало--//
						if ($(event.target).hasClass("btn-new-material")) {
							wrapperMaterialLast.after(data);
						} else {
							tr.after(data);
						}
						//--'Нумерация материалов в ТЗ' начало--//
                        /*------------------'Подключение select2 к методикам' начало-----------------*/
                        //К селектам ГОСТа подключаем плагин select2
                        //addPluginSelect2();
                        /*---------------------'Подключение select2 к методикам' окончание-----------*/
						//--'Нумерация материалов в ТЗ' начало--//
						$('.number-material').each(function(i,elem) {
							i++;

							$(elem).html(i);

							var trMaterial = $(elem).closest('.tr-material');
							var closestWrapperMaterial = $(elem).closest('.wrapperMaterial');

							trMaterial.find('input[name^="mater"]').attr('name','mater[' + i + ']');
							trMaterial.find('input[name^="idMater"]').attr('name','mater[' + i + ']');
							trMaterial.find('input[name^="obiem"]').attr('name','obiem[' + i + ']');

							closestWrapperMaterial.find('.gost-select').each(function (iGostSelect, elemGostSelect) {
								$(elemGostSelect).attr('name','gost-select[' + i + '][]');
								$(elemGostSelect).data('key', i);
							});


							closestWrapperMaterial.find('.tr-gost').each(function (iGost, elemGost) {
								$(elemGost).attr('id', i);
								var selectGost = $(elemGost).find('.methods');
								selectGost.attr('name', 'gost[' + i + '][]');
								selectGost.data('id', i);

								var selectNewGost = $(elemGost).find('.conditions');
								selectNewGost.attr('name', 'gost_new[' + i + '][]');
								selectNewGost.data('id', i);
								$(elemGost).find('input[name^="price"]').attr('name', 'price[' + i + '][]');
							});


						});
						//--'Нумерация материалов в ТЗ' окончание--//
                    }
                });
                /*--------------'При выборе материала, добавление методик' окончание-----------*/
            });

            $("#form_tz").on("click", ".del2", function () {
                let tr = $(this).parent().parent().parent();
                let tr_dataid = tr.attr('id');
				//--'Договор в request_new' начало--//
				var tzId = $('.id-tz') ? $('.id-tz').val() : '';
//  подгружаем новую методику
                flag = 0;
                $.ajax({
                    //--'Договор в request_new' начало--//
                    //url: 'select_for_tz.php?KEY=' + tr_dataid,
                    url: 'select_for_tz_custom_gost.php?KEY=' + tr_dataid + '&ID_TZ=' + tzId,
                    //--'Договор в request_new' окончание--//
                    success: function (data) {
                        tr.before(data);
                        // и удаляем минус у data
                        $(tr).prev().find('.del').remove();
                        $(tr).remove();
                    }
                });
            });

            $("#form_tz").on("click", ".del", function () {

                $(this).parent().parent().remove();
                // смотрим, если были кастомные методики
                // и их больше нет, то ставим flag = 1
                // иначе flag = 0
                let countNewGost = calcNewGost();
                console.log('Количество новых гостов : ' + countNewGost);
                if(countNewGost > 0) {
                    flag = 0;
                }else if(countNewGost == 0) {
                    flag = 1;
                }
                console.log('flag = ' + flag);
            });

            $("#form_tz").on("click", ".add2", function () {

                tbody = $(this).parent().parent().parent();
                tr = $(this).parent().parent();
                tr_dataid = tr.attr('id');
				//--'Договор в request_new' начало--//
				var tzId = $('.id-tz') ? $('.id-tz').val() : '';
				//--'Договор в request_new' окончание--//

                $.ajax({
					//--'Договор в request_new' начало--//
                    //url: 'select_for_tz.php?KEY=' + tr_dataid,
                    url: 'select_for_tz.php?KEY=' + tr_dataid + '&ID_TZ=' + tzId,
					//--'Договор в request_new' окончание--//

                    success: function (data) {
                        tr.after(data);
                        /*----------------'При выборе материала, добавление методик' начало---------------------*/
                        /*------------------'Подключение select2 к методикам' начало-----------------*/
                        //К селектам ГОСТа подключаем плагин select2
                        //addPluginSelect2();
                        /*---------------------'Подключение select2 к методикам' окончание-----------*/
                    }

                });
                /*--------------'При выборе материала, добавление методик' окончание-----------*/
            });

            $("#form_tz").on("click", ".add3", function () {

                flag = 0;

                tbody = $(this).parent().parent().parent();
                tr = $(this).parent().parent();
                tr_dataid = tr.attr('id');
                //--'Договор в request_new' начало--//
                var tzId = $('.id-tz') ? $('.id-tz').val() : '';
                //--'Договор в request_new' окончание--//

                $.ajax({
                    //--'Договор в request_new' начало--//
                    //url: 'select_for_tz.php?KEY=' + tr_dataid,
                    url: 'select_for_tz_custom_gost.php?KEY=' + tr_dataid + '&ID_TZ=' + tzId,
                    //--'Договор в request_new' окончание--//

                    success: function (data) {
                        tr.after(data);
                        /*----------------'При выборе материала, добавление методик' начало---------------------*/
                        /*------------------'Подключение select2 к методикам' начало-----------------*/
                        //К селектам ГОСТа подключаем плагин select2
                        //addPluginSelect2();
                        /*---------------------'Подключение select2 к методикам' окончание-----------*/
                    }

                });
                /*--------------'При выборе материала, добавление методик' окончание-----------*/
            });

            /*----------------'При выборе материала, добавление методик' начало--------------------*/
            //Перебираем все выбранные методы, находим иконки и прописываем для них ссылки
            $(".methods option:selected").each(function (ind, opt) {
                //var linkMethods = $(opt).closest('td').find('.methodsLink');
                var linkMethods = $(opt).closest('tr').find('.methodsLink');
                linkMethods.attr('href', '/obl_acc.php?ID=' + $(opt).val());
            });
            /*----------------'При выборе материала, добавление методик' окончание------------------*/

            $("#form_tz").on("change", ".gost", function () {
                optionSelected = $("option:selected", this);
                price = optionSelected.data('price');
                tr = $(this).parent().parent();
                price_input = tr.find(".oborud");
                price_input[0].value = price;

                tr.find(".oborud").css('display', 'block');
                tr.find(".oborudDash").css('display', 'none');

                tr.find(".dash").prop('checked', false);

                /*----------------'При выборе материала, добавление методик' начало---------------------*/
                //var methodsLink = optionSelected.closest('td').find('.methodsLink');
                //Находим иконку выбранного ГОСТа и добавляем ссылку на методику
                var methodsLink = optionSelected.closest('tr').find('.methodsLink');
                methodsLink.attr('href', '/obl_acc.php?ID=' + optionSelected.val());
                /*----------------'При выборе материала, добавление методик' окончание------------------*/

                var thisVal = $(this).val();
                //Проверка, если id методики не найден в массиве разрешенных для редоктирования методик, то вернет -1 и input readonly
                if ($.inArray(thisVal, $arrId) < 0) {
                    $(price_input).prop('readonly', true);
                } else {
                    $(price_input).prop('readonly', false);
                }
            });

            $("#form_tz").on("change", ".gost_d", function () {
                optionSelected = $("option:selected", this);
                price = optionSelected.data('price');
                tr = $(this).parent().parent();
                price_input = tr.find(".oborud");
                price_input[0].value = price;

                tr.find(".oborud").css('display', 'block');
                tr.find(".oborudDash").css('display', 'none');


                tr.find(".dash").prop('checked', false);

                /*----------------'При выборе материала, добавление методик' начало---------------------*/
                //Находим иконку выбранного ГОСТа и добавляем ссылку на методику
                //var methodsLink = optionSelected.closest('td').find('.methodsLink');
                var methodsLink = optionSelected.closest('tr').find('.methodsLink');
                methodsLink.attr('href', '/obl_acc.php?ID=' + optionSelected.val());
                /*----------------'При выборе материала, добавление методик' окончание------------------*/

                var thisVal_d = $(this).val();
                //Проверка, если id методики не найден в массиве разрешенных для редоктирования методик, то вернет -1 и input readonly
                if ($.inArray(thisVal_d, $arrId) < 0) {
                    $(price_input).prop('readonly', true);
                } else {
                    $(price_input).prop('readonly', false);
                }
            });

            $('#form_tz').on('click', '.dash', function () {
                var tr = $(this).closest('tr');
                if (tr.find('.dash').prop("checked")) {
                    price = tr.find('.oborud').val();
                } else {
                    price = tr.find('.oborud').data("price_prev");
                }

                tr.find('.oborud').data('price_prev', price);
                var price_prev = price;

                if (tr.find(".dash").prop("checked")) {
                    if (tr.find('div').is(".oborudDash")) {
                        tr.find(".oborudDash").css('display', 'block');
                    } else {
                        tr.find(".oborud").after("<div class='oborudDash'>-</div>");
                    }
                    tr.find(".oborud").css('display', 'none');
                    tr.find(".oborud").val('0');
                } else {
                    tr.find(".oborud").css('display', 'block');
                    tr.find(".oborud").val(price_prev);
                    tr.find(".oborudDash").css('display', 'none');
                }

            });

            /*-----------------'Скидка не более 50%' начало------------------------*/
            /*$('input[name=discountName]').on('input', function() {
              $(this).val(function(i, v) {
                  if (v > '50') {
                    alert('Скидка не может превышать 65%');
                  }

                  return Math.max(this.min, Math.min(this.max, v));
              });
            });*/
            /*-----------------'Скидка не более 50%' окончание-----------------------*/

            //---------------------------'Отношение методики к тз' начало-----------------//
            var counterClick = 0;
            $('#new_tz').on('click', function (event) {

                $noChecked = 0;
                $.each($('.oborud'), function (i, v) {
                    if ($(v).val() != 0 && $(v).val() != '') {
                        return true;
                    }
                    var tr = $(v).closest('tr');
                    var checkbox = tr.find('.dash');

                    if (!checkbox.prop('checked')) {
                        $noChecked++;
                        alert('Пожалуйста, укажите стоимость или поставьте отметку об отсутствии цены.');
                        return false;
                    }
                });

                if ($noChecked) {
                    event.preventDefault();
                    return true;
                }

                var valDateProbe = $('input[name="DATE_PROBE"]').val();
                var valActNum = $('input[name="ACT_NUM"]').val();
                var valDateAct = $('input[name="DATE_ACT"]').val();

                if (!valDateProbe && !$('.tzNoSamples').prop('checked') && valActNum && valDateAct) {
                    saveDateProbe = confirm('Дата отбора проб не указана. Хотите продолжить?');
                    if (!saveDateProbe) {

                        event.preventDefault();
                        return true;
                    }

                }

                let dayToTest = $('input[name="deadlineIspDay"]').val();
                if(dayToTest < 0) {
                    alert('Срок проведения испытаний в днях не может быть меньше 0');
                    event.preventDefault();
                    return true;
                }
                var discountName = $('input[name=discountName]').val();

                if (!isDiscount && discountName) {
                    var save = confirm('У Вас есть скидка, Вы ее не применили. Хотите сохранить');
                    if (!save) {
                        event.preventDefault();
                        return true;
                    }
                }

                selectFalse = 1;
                $('#form_tz select option:selected').each(function (i, el) {

                    if ($(el).text() == 'Выберите') {
                        selectFalse = 0;
                    }
                });

                // когда в заявке есть песок или щебень, то select выбора карьера обязателен для ввода и пока не выберем option не дадим сохранить
                isSelectedQuarry = 1;
                if($('#form_tz select[name=quarry] option:selected').val() == '') {

                    if($('#form_tz select[name=quarry]').attr('required') == 'required') {
                        isSelectedQuarry = 0;
                    }
                }

                var checkt = $('.tzNoSamples').prop('checked');

                if (valActNum == '0') {
                    valActNum = false;
                }

				<!--'Нумерация материалов в ТЗ' начало-->
				var materialFalse = $('input[name^=mater]').filter(function(i) {
					if (this.value == '') {
						return ++i;
					}
				});

				if (materialFalse.length) {
					alert("Заполните наименование у материала!");
					return false;
				}
				// <!--'Нумерация материалов в ТЗ' окончание-->

				//--Заявка ВЛК окончание--//
				//Если существует список материалов для импорта в заявку ВЛК, то проверяем выбран ли материал
				if ($('.wrapperListMaterial').css('display') && $('.wrapperListMaterial').css('display') != 'none' && $('.inputListMaterial')) {
					var materialFalseImportVlk = $('.inputListMaterial').filter(function(w) {
						if (this.value == '') {
							return ++w;
						}
					});
					//Проверяем выбран ли материал
					if (materialFalseImportVlk.length) {
						alert("Выберите материал для импорта!");
						return false;
					}
				}

                arrIDLabaNewMethodics = [];
                // проверка на пустоту новодобавленных гостов
                // получим все значения в массив
                // и если он содержит пустое значение, не дадим сохранить
                let arrNewGost = [];
                let newGost = $('[NAME^=gost1]').map(function(ind, ele) {
                    let val = $(ele).val();
                    if(val) arrNewGost.push(val);
                    return val;
                }).get();

                if(newGost.includes('')) {
                    alert('Не может быть пустых значений в новых методиках');
                    event.preventDefault();
                    return false;
                }

				//--Заявка ВЛК окончание--//
                if ((valActNum || !valActNum && checkt) && (valDateAct || !valDateAct && checkt) && selectFalse && isSelectedQuarry) {
					<!--'Техническое задание утверждено руководителем лаборатории' начало-->
					event.preventDefault();

					var getEdit = '<?= $_GET["EDIT"] ?>' ? '<?= $_GET["EDIT"] ?>' : '';
                    var getId = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';
					var formTz = $('#form_tz');
					var arrGost = [];

                    $('.methods option:selected').each(function (index, element) {
                        arrGost.push($(element).val());
                    });

                    console.log(arrGost);
                    console.log(arrNewGost);

                    var isOk;
                    if(arrGost.length > 0) {
                        isOk = checkMethod(arrGost,getEdit,getId);
                    }else{
                        isOk = 1;
                    }
                    
                    if(!flag) {
                        checkLaba(arrNewGost.length, arrNewGost);
                    }

					console.log('isOk', isOk);
					console.log('flag', flag);

                    // переиндексируем все новые строки с методиками на [x][y] для корректной вставки в сериализованный массив
                    renameNewGost();
                    // $('.gost1').each(function(ind, ele) {
                    //     let number = $(ele).closest('.wrapperMaterial').find('number-material').text();
                    //     let i = number - 1;
                    //     console.log('i = ' + i);
                    //     console.log($(ele).attr('name'));
                    // })

                    if (isOk && flag) {
                        formTz.submit();
                    }

					 //checkMethod(arrGost,getEdit,getId);

                     /*event.preventDefault();
                     var checkNoSend = 0;

                     var arrGost = [];

                     $('.methods option:selected').each(function (index, element) {
                         arrGost.push($(element).val());
                     });

                     var getEdit = '<?= $_GET["EDIT"] ?>' ? '<?= $_GET["EDIT"] ?>' : '';
                     var getId = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';



                     $.ajax({
                         method: "POST",
                         url: "alarm_for_tz.php",
                         data: {arr_gost: arrGost, edit: getEdit, id: getId},
                         dataType: "json",
                         success: function (data) {

							 var formTz = $('#form_tz');
                             var counter = 0;
                             var isOk = 1;
                             $.each(data['metodicsErr'], function (ind, ele) {
                                 if ($(ele).length) {
                                     $('.methods:eq(' + ind + ')').closest('tr').find('.notMatch').remove();
                                 }

                                 if (!$(ele).length) {

                                     checkNoSend++;
                                     isOk = 0;

                                     $('.methods:eq(' + ind + ')').closest('tr').find('.notMatch').remove();*/

                                     //Склонение лабораторий
                                     /*if (data['arrIdUser'][ind][0][4] == 'Администрация ИЦ') {
                                         data['arrIdUser'][ind][0][4] = 'Администрации ИЦ';
                                     }
                                     if (data['arrIdUser'][ind][0][4] == 'Бухгалтерия') {
                                         data['arrIdUser'][ind][0][4] = 'Бухгалтерии';
                                     }
                                     if (data['arrIdUser'][ind][0][4] == 'ДСЛ') {
                                         data['arrIdUser'][ind][0][4] = 'ДСЛ';
                                     }
                                     if (data['arrIdUser'][ind][0][4] == 'Лаборатория СМ') {
                                         data['arrIdUser'][ind][0][4] = 'Лаборатории СМ';
                                     }
                                     if (data['arrIdUser'][ind][0][4] == 'Лаборатория ФМИ') {
                                         data['arrIdUser'][ind][0][4] = 'Лаборатории ФМИ';
                                     }
                                     if (data['arrIdUser'][ind][0][4] == 'Лаборатория ФХИ') {
                                         data['arrIdUser'][ind][0][4] = 'Лаборатории ФХИ';
                                     }
                                     if (data['arrIdUser'][ind][0][4] == 'ОСК') {
                                         data['arrIdUser'][ind][0][4] = 'ОСК'
                                     }
                                     if (data['arrIdUser'][ind][0][4] == 'Экстранет') {
                                         data['arrIdUser'][ind][0][4] = 'Экстранета';
                                     }*/

									 //Функция возвращает уникальные значения в масссиве
									 /*function unique(arr) {
									  var result = [];

									  $.each(arr, function (indArr, valArr) {
											//Метод includes() определяет, содержит ли массив определённый элемент, возвращая в зависимости от этого true или false
											if (!result.includes(valArr)) {
											  result.push(valArr);
											}
									  });

									  return result;
									}

									 var dep = [];

									 if (data['arrIdUser'][ind]) {
										$.each(data['arrIdUser'][ind], function (indDep, valDep) {
											console.log('valDep',valDep);
											console.log('(valDep[4]',valDep[4]);
											if (valDep[4] == 'Администрация ИЦ') {
												 dep.push('Администрации ИЦ');
											 }
											 if (valDep[4] == 'Бухгалтерия') {
												 dep.push('Бухгалтерии');
											 }
											 if (valDep[4] == 'ДСЛ') {
												 dep.push('ДСЛ');
											 }
											 if (valDep[4] == 'Лаборатория СМ') {
												 dep.push('Лаборатории СМ');
											 }
											 if (valDep[4] == 'Лаборатория ФМИ') {
												 dep.push('Лаборатории ФМИ');
											 }
											 if (valDep[4] == 'Лаборатория ФХИ') {
												 dep.push('Лаборатории ФХИ');
											 }
											 if (valDep[4] == 'ОСК') {
												 dep.push('ОСК');
											 }
											 if (valDep[4] == 'Экстранет') {
												 dep.push('Экстранета');
											 }
										});
									 }

									 //Оставим только уникальные значения из масссива dep
									 var depUnique = unique(dep);

									 var strDep = depUnique.join(' или ');

                                     //var titleNotMatch = ((Array.isArray(data['arrIdUser'][ind])) ? 'Необходимо добавить сотрудника из ' + data['arrIdUser'][ind][0][4] : data['arrIdUser'][ind]);
                                     var titleNotMatch = ((Array.isArray(data['arrIdUser'][ind])) ? 'Необходимо добавить сотрудника из ' + strDep : data['arrIdUser'][ind]);

                                     arrUsers[ind] = data['arrIdUser'][ind];

                                     $('.methods:eq(' + ind + ')').closest('tr').append("<div class='notMatch' data-employees='" + ind + "' title='" + titleNotMatch + "' onclick='addUser(" + ind + ");'>Не соответствует</a></div>");
                                 }

                             });

                             if (checkNoSend > 0) {
                                 alert('Не соответствие методики и исполнителей');
                             }

                             if (isOk) {
                                 formTz.submit();
                             }
                         }


                     });*/
					 <!--'Техническое задание утверждено руководителем лаборатории' окончание-->

                 }
            });

            //Всплывающее окно ,добавление ответственных
            $('.wrepperCompanyEmployees').on('click', function (event) {

                var eventTarget = event.target;

                if ($(eventTarget).attr("id") == 'idCloseWindow') {
                    $(this).closest('.wrepperCompanyEmployees').css('display', 'none');
                }

                if ($(eventTarget).attr("id") == 'idAddUser') {

                    $("body").data("foo");
                    var userAddId = $('.companyEmployees option:selected').data('id');
                    var addGetEdit = '<?= $_GET["EDIT"] ?>' ? '<?= $_GET["EDIT"] ?>' : '';
                    var addGetId = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';

                    //Добовление сотрудника в ответственные
                    $.ajax({
                        method: "POST",
                        url: "add_user_for_tz.php",
                        data: {id_user: userAddId, edit: addGetEdit, id: addGetId},
                        dataType: "json",
                        success: function (data) {

                            // console.log(data['arrOtherResponsible']);
                            allResponsible = data['arrOtherResponsible'];

                            //Проверка на добавление сотрудника
                            if (data['resultUpdate']) {
                                $('.wrepperCompanyEmployees').css('display', 'none');
                                alert('Сотрудник добавлен в ответственные, для подтверждения изменений сохраните ТЗ');
                            } else {
                                if (!$('.errorMassage')) {
                                    $('.wrapperBtn').after(function () {
                                        return '<div class="errorMassage" style="color:#e00220">Cотрудник в ответственные не добавлен</div>';
                                    });
                                }
                            }

                        }
                    });
                }
            });

// Всплывающее окно ,добавление лаборатории к новой методике
            $('.wrepperMetodicAddGost').on('click', function(event) {
                arrIdLabaNewMethodic = [];
                let eventTarget = event.target;
                if($(eventTarget).attr('id') == 'idCloseWindowAddGost') {
                    $('.wrepperMetodicAddGost').css('display', 'none');
                }
                if($(eventTarget).attr('id') == 'idAddLaba') {
                    $('.laba_class').each(function(ind, ele) {
                        arrIdLabaNewMethodic.push($(ele).find(':selected').val());
                    });
                    // флаг стал доступен для отправки формы
                    // т.к. выбрали ответсвенных по лабороториям
                    flag = 1;
                    $('.wrepperMetodicAddGost').css('display', 'none');
                    // ['LFHI'] = 54 = 11 = Татьяна Овчинникова лаборатория ФХИ
                    // ['LFMI'] = 56 = 15 = Шингарев Виталий лаборатория ФМИ
                    // ['DSL'] = 55 = 13 = Татьяна Велютич лаборатория ДСЛ
                    // ['LSM'] = 57 = 58 = Регина Живцова лаборатория СМ
                    // ['OSK'] = 58 = 43 = Елена Лавреньтьева лаборатория ОСК
                    // <option value="0">ЛФМИ</option>
                    // <option value="1">ЛФХИ</option>
                    // <option value="2">ЛСМ</option>
                    // <option value="3">ДСЛ</option>
                    // <option value="4">ОСК</option>

                    let userCustomAddId = $('.laba_class option:selected').val();
                    let userAddIdCustom;
                    let addGetEdit = '<?= $_GET["EDIT"] ?>' ? '<?= $_GET["EDIT"] ?>' : '';
                    let addGetId = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';
                    let bool_arr = []; // вместо нескольих alert вызываем 1 если все ajax запросы прошли успешно, иначе .errorMassage
                    // получаем значения из модального окна выбранных лабороторий
                    $('.laba_class').each(function(ind, ele) {
                        userCustomAddId = $(ele).find(':selected').val();

                        switch(+userCustomAddId) {
                            case 0:
                                userAddIdCustom = 15;
                            break;
                            case 1:
                                userAddIdCustom = 11;
                                break;
                            case 2:
                                userAddIdCustom = 58;
                                break;
                            case 3:
                                userAddIdCustom = 13;
                                break;
                            case 4:
                                userAddIdCustom = 43;
                                break;
                        }

                        //Добовление сотрудника в ответственные
                        $.ajax({
                            method: 'POST',
                            url: 'add_user_for_tz.php',
                            data: {id_user: userAddIdCustom, edit: addGetEdit, id: addGetId},
                            dataType: 'json',
                            success: function(data) {
                                // console.log(data);
                                // console.log(data['arrOtherResponsible']);
                                allResponsible = data['arrOtherResponsible'];
                                //Проверка на добавление сотрудника
                                if(data['resultUpdate']) {
                                    bool_arr.push(1);
                                }else{
                                    bool_arr.push(0);
                                    if(!$('.errorMassage')) {
                                        $('.wreppermetodicAddGost .wrapperBtn').after(function() {
                                            return '<div class="errorMassage" style="color: #e00220">Лаборатория с ответственным не добавлена</div>';
                                        });
                                    }
                                }
                            }
                        });
                    });
                    if(!bool_arr.includes(0)) {
                        alert('Лаборатория с главным ответственным добавлена, для подтверждения изменений сохраните ТЗ');
                    }
                }
            });

            $("#form_tz").on("change", "input[name^=idMater]", function () {
                var td = $(this).closest('td');
                var mater = td.find("input[name^=mater]");

                mater.val($(this).val());
            });

            var dateAct = $('input[name="DATE_ACT"]');
            var placeProbe = $('input[name="PLACE_PROBE"]');
            var dateProbe = $('input[name="DATE_PROBE"]');

            if ($('.tzNoSamples').prop('checked')) {
                dateAct.prop("disabled", true);
                placeProbe.prop("disabled", true);
                dateProbe.prop("disabled", true);
            } else {
                dateAct.prop("disabled", false);
                placeProbe.prop("disabled", false);
                dateProbe.prop("disabled", false);
            }

            $('.tzNoSamples').on('click', function () {
                var thisTr = $(this).closest('tr');
                var actNum = thisTr.find('input[name=ACT_NUM]');
                var tzHidden = thisTr.find('.tzHidden');
                var dateAct = $('input[name="DATE_ACT"]');
                var placeProbe = $('input[name="PLACE_PROBE"]');
                var dateProbe = $('input[name="DATE_PROBE"]');

                if ($('.tzNoSamples').prop('checked')) {
                    actNum.css('display', 'none');
                    actNum.val('0');
                    tzHidden.css('display', 'inline-block');
                    dateAct.prop("disabled", true);
                    placeProbe.prop("disabled", true);
                    dateProbe.prop("disabled", true);
                    dateAct.val('');
                    placeProbe.val('');
                    dateProbe.val('');
                } else {
                    actNum.css('display', 'inline-block');
                    actNum.prop("disabled", false);
                    actNum.val('');
                    tzHidden.css('display', 'none');
                    dateAct.prop("disabled", false);
                    placeProbe.prop("disabled", false);
                    dateProbe.prop("disabled", false);
                }
            });

        });

        $(document).ready(function () {

            var dogovorNum = $('select[name=DOGOVOR_NUM]');
            var tzHiddenСontract = $('.tzHiddenСontract');
            var dogovorNumHidden = $('input[name=dogovorNumHidden]');
            var selectDogovorNum = $('select[name=DOGOVOR_NUM]').closest('td').find('.select2.select2-container');

            if ($('.tzNoСontract').prop('checked')) {
                dogovorNum.css('display', 'none');
                selectDogovorNum.css('display', 'none');
                tzHiddenСontract.css('display', 'inline-block');
                dogovorNumHidden.val('0');
            } else {
                dogovorNum.css('display', 'inline-block');
                selectDogovorNum.css('display', 'inline-block');
                tzHiddenСontract.css('display', 'none');
                dogovorNumHidden.val('');
            }

            $('.tzNoСontract').on('click', function (event) {

                var thisTrСontract = $(this).closest('tr');
                var dogovorNum = thisTrСontract.find('select[name=DOGOVOR_NUM]');
                var tzHiddenСontract = thisTrСontract.find('.tzHiddenСontract');
                var dogovorNumHidden = thisTrСontract.find('input[name=dogovorNumHidden]');
                var selectDogovorNum = $('select[name=DOGOVOR_NUM]').closest('td').find('.select2.select2-container');

                if ($('.tzNoСontract').prop('checked')) {
                    dogovorNum.css('display', 'none');
                    selectDogovorNum.css('display', 'none');
                    tzHiddenСontract.css('display', 'inline-block');
                    dogovorNumHidden.val('0');
                } else {
                    dogovorNum.css('display', 'inline-block');
                    selectDogovorNum.css('display', 'inline-block');
                    tzHiddenСontract.css('display', 'none');
                    dogovorNumHidden.val('');
                }
            });

            /*-------------------'При выборе материала, добавление методик' начало--------*/

            $('#form_tz').on('change', function (event) {
                var target = event.target;
                var closestTzBody = $(target).closest('.wrapperMaterial').find('tbody[name=tz_body]');

                //Проверяем является ли элемент, списком материалов
                if ($(target).is('input[name^=idMater]') || $(target).is('input[name^=mater]')) {

                    //Получаем id материала
                    var arrMatterial = $(target).val().split('ID');
                    var idMaterial = arrMatterial[1] ? arrMatterial[1] : '';
                    var closestTable = $(target).closest('table');
					//--'Договор в request_new' начало--//
					var idTZ = $('.id-tz') ? $('.id-tz').val() : '';
					//--'Договор в request_new' окончание--//

                    //Получаем все госты для материала
                    $.ajax({
                        method: "POST",
                        url: "request_gosts_for_tz.php",
                        data: {id_material: idMaterial},
                        dataType: "json",
                        success: function (dataGost) {

                            if (dataGost) {

                                var gostSelect = closestTable.find('.gost-select');

                                //При выборе материала, оставляем один СПИСОК ГОСТов, остальные удаляем если они есть
                                if (closestTable.find('.tr-select-gost').length > 1) {
                                    $.each(closestTable.find('.tr-select-gost'), function (indTrGost, trGost) {
                                        if (indTrGost > 0) {
                                            trGost.remove();
                                        }
                                    });
                                }

                                //Если есть ГОСТы, удаляем
                                if ($(gostSelect).find('option')) {
                                    $(gostSelect).find('option').remove();
                                }

                                let url_for_ajax = $(closestTzBody).find('input[name^=gost1]').length;
                                // если равно 0 то удаляем строки
                                // если нет и есть новый кастомная методика, то оставляем как есть
                                if(url_for_ajax == 0) {
                                //Удаляем привязанные к материалу методики, если они есть
                                    if (closestTzBody.find('tr')) {
                                        var trId;
                                        $(closestTzBody.find('tr')).each(function (index, element) {
                                            if (element.id) {
                                                trId = element.id;
                                            } else {
                                                trId = 0;
                                            }
                                            $(element).remove();
                                        });

                                        /*----------------'Добавление номера и фона' начало--------------------*/
                                        function removeBtnGost() {
                                            let url_for_ajax = $(closestTzBody).find('input[name^=gost1]').length;
                                            let index_new_gost = Number($(closestTzBody).find('input[name^=gost1]').closest('tr').index());
                                            //Присваиваем data атрибуту 0, для дальнейшего удаления при выборе ГОСТа
                                            $(closestTzBody).find('tr').data('gost', 0);
                                            $(closestTzBody).find('.del').remove();
                                                let del2_length = $(closestTzBody).find('tr:first').find('.del2').length;
                                            $(closestTzBody).find('tr:first').find('.add2').after("<label class='label_del2'><input style='margin-left:5px' type='button' class='del2' value='<  >' > <div class='helper'>Данная кнопка позволяет добавить на 1-ю позицию возможность внесения новой методики. Внимание, данное действие отменить нельзя!</div> </label>");

                                        }

                                        //Вставляем временно,строку с методиками, для стандартного визуального отображения таблицы
                                        $.ajax({
                                            //--'Договор в request_new' начало--//
                                            //url: 'select_for_tz.php?KEY=' + trId,
                                            url: 'select_for_tz.php?KEY=' + trId + '&ID_TZ=' + idTZ,
                                            //--'Договор в request_new' окончание--//

                                            success: function (data) {
                                                closestTzBody.append(data);
                                                /*------------------'Подключение select2 к методикам' начало-----------------*/
                                                //К селектам ГОСТа подключаем плагин select2
                                                //addPluginSelect2();
                                                /*---------------------'Подключение select2 к методикам' окончание-----------*/
                                                removeBtnGost();
                                            }

                                        });

                                    } //if (closestTzBody.find('tr')) {
                                } // if(url_for_ajax) {


                                //Добавляем новый список гостов
                                gostSelect.append("<option data-id='' value='' selected class='first' disabled> </option>");
                                $.each(dataGost, function (ind, gost) {
                                    gostSelect.append("<option data-id='" + gost['id'] + "' value='" + gost['name'] + "'>" + gost['name'] + "</option>");
                                });

                                //Показываем госты
                                closestTable.find('.tr-select-gost').css('display', 'table-row');
                            }
                        }
                    });
                }

                //Выбор ГОСТа
                //Проверяем является ли выбранный элемент списком ГОСТов
                if ($(target).is('.gost-select')) {
                    //Находим id выбранного ГОСТа
                    var idGostSelected = $(target).find('option:selected').data('id');
                    var firstGostOption = $(target).find('.first');
                    var gostsSelected = $(target).closest('tbody').find('.hidden-id-gost');
                    var hiddenInput = $(target).closest('td').find('.hidden-id-gost');
                    var key = $(target).data('key');
                    var sumMetodic = $(target).closest('.wrapperMaterial').find('tbody[name=tz_body] tr[data-gost]').length;
					//--'Договор в request_new' начало--//
					var IDTZ = $('.id-tz') ? $('.id-tz').val() : '';
					//--'Договор в request_new' окончание--//

                    //Записываем в data атрибут id предыдущего ГОСТа
                    var dataHiddenInput;
                    if ($(hiddenInput).val()) {
                        $(hiddenInput).data('previous', $(hiddenInput).val());
                        dataHiddenInput = $(hiddenInput).data('previous');
                    }

                    //Проверяем ГОСТ, если такой ГОСТ уже существует то обнуляем его и выводи alert
                    var check = 0;
                    $(gostsSelected).each(function (indexGost, elementGost) {
                        if ($(elementGost).val() == idGostSelected) {
                            alert('Данный ГОСТ уже существует');

                            //Обнуляем(сбрасываем) список ГОСТов
                            $(firstGostOption).prop('selected', true);

                            //Очишаем скрытый input для этого ГОСТа, значение value и data атрибут
                            hiddenInput.val('');

                            if ($(hiddenInput).data('previous')) {
                                $(hiddenInput).data('previous', '');
                            }

                            //Удаляем методики ГОСТа которого обнулили
                            if (dataHiddenInput) {
                                $(closestTzBody.find('tr')).each(function (index, element) {
                                    if ($(element).data('gost') == Number(dataHiddenInput)) {
                                        $(element).remove();
                                    }
                                });
                            }
                            check++;
                            return false;
                        }
                    });

                    if (check) {
                        return true;
                    }

                    function removeFistBtnDel() {
                        // если первый элемент в строке новый, то не удаляем кнопку .del
                        // и добавляем её ко второму элементу

                        let url_for_ajax = $(closestTzBody).find('input[name^=gost1]').length;
                        let index_new_gost = Number($(closestTzBody).find('input[name^=gost1]').closest('tr').index());

                        if(index_new_gost != 0) {

                            //Удаляем у первой методики кнопку удаления
                            if ($(closestTzBody).find('.del:first')) {
                                $(closestTzBody).find('tr:first').find('.del').remove();
                            }
                        }

                        if(index_new_gost == 0) {
                            // получаем количество кнопок удалить в 2 строке
                            let del_length = $(closestTzBody).find('tr:first').next().find('.del').length;
                            let del2_length = $(closestTzBody).find('tr:first').next().find('.del2').length;

                            if(!del_length) {
                                $(closestTzBody).find('tr:first').next().find('.add2').after("<input style='margin-left:5px' type='button' class='del' value='-' >");
                            }
                            $(closestTzBody).find('tr:first').next().find('.del2').remove();
                            $(closestTzBody).find('tr:first').next().find('.label_del2').remove();
                        }
                    }
                    console.log('idTZ',idTZ);
                    hiddenInput.val(idGostSelected);
                    //Запрашиваем все методики для выбранного ГОСТа
                    $.ajax({
                        method: "POST",
						//--'Договор в request_new' начало--//
                        url: "methods_for_tz.php",
                        //data: {id_gost: idGostSelected, key: key, sum_metodic: sumMetodic},
                        data: {id_gost: idGostSelected, key: key, sum_metodic: sumMetodic, id_tz: IDTZ},
						//--'Договор в request_new' окончание--//
                        success: function (dataMethods) {

                            //Если выбрали другой гост, удаляем предыдущие методики
                            if (dataHiddenInput) {
                                $(closestTzBody.find('tr')).each(function (index, element) {
                                    if ($(element).data('gost') == Number(dataHiddenInput)) {
                                        $(element).remove();
                                    }
                                });
                            }

                            $(closestTzBody.find('tr')).each(function (index, element) {
                                if ($(element).data('gost') == 0) {
                                    $(element).remove();
                                }
                            });

                            $(closestTzBody).append(dataMethods);
                            /*------------------'Подключение select2 к методикам' начало-----------------*/
                            //К селектам ГОСТа подключаем плагин select2
                            //addPluginSelect2();
                            /*---------------------'Подключение select2 к методикам' окончание-----------*/
                            removeFistBtnDel();
                        }
                    });
                }
            });


            //Добавление дополнительных списков ГОСТа
            $("#form_tz").on('click', '.add-gost', function (event) {
                var btnAddGost = event.target;
                var closestTbody = $(btnAddGost).closest('tbody');
                var trSelectGost = $(btnAddGost).closest('.tr-select-gost');
                var cloneTrSelectGost = $(trSelectGost).clone(true);

                //Если отсутствует кнопка "удалить ГОСТ", добавляем
                if (!cloneTrSelectGost.find('.del-gost').length) {
                    cloneTrSelectGost.find('.wrapperBtnGost').append("<input type='button' class='del-gost' value='-'>");
                }

                //Очищаем у склонированного элемента value, чтобы при выборе ГОСТа в data атрибут не записать значение предыдущего ГОСТа
                if (cloneTrSelectGost.find('.hidden-id-gost').val()) {
                    cloneTrSelectGost.find('.hidden-id-gost').val('');
                }

                $(cloneTrSelectGost).appendTo($(closestTbody));
            });

            //Удаляем ГОСТы
            $("#form_tz").on('click', '.del-gost', function (event) {
                var btnDelGost = event.target;
                var idDelGost = $(btnDelGost).closest('.tr-select-gost').find('.hidden-id-gost').val();
                var closestTzBodyDel = $(btnDelGost).closest('.wrapperMaterial').find('tbody[name=tz_body]');
                //var gostsSelected = $(btnDelGost).closest('tbody').find('.gost-select option:selected');

                //Удаляем прикрепленные к ГОСТу методики
                $(closestTzBodyDel.find('tr')).each(function (index, element) {
                    if ($(element).data('gost') == idDelGost) {
                        $(element).remove();
                    }
                });

                //Удаляем ГОСТ
                $(btnDelGost).closest('.tr-select-gost').remove();
            });

            /*----------------'Удаление методики' начало-------------------*/
            //Обработчик события кнопки удаления методик
            $('#form_tz').on('click', '.delMetodic', function (event) {
                var targetFocus = event.target;
                var trFocus = $(targetFocus).closest('.wrapperMaterial').find('.tr-select-gost');
                var closestTzBodyFocus = $(targetFocus).closest('.wrapperMaterial').find('tbody[name=tz_body]');
				//--'Договор в request_new' начало--//
				var TZID = $('.id-tz') ? $('.id-tz').val() : '';
				//--'Договор в request_new' окончание--//

                //Скрываем ГОСТы
                if (trFocus) {
                    $.each(trFocus, function (indTrFocus, valTrFocus) {
                        $(valTrFocus).css('display', 'none');
                    });
                }

                //Удаляем привязанные к материалу методики, если они есть
                if (closestTzBodyFocus.find('tr')) {
                    var trIdFocus;
                    $(closestTzBodyFocus.find('tr')).each(function (index, element) {
                        if (element.id) {
                            trIdFocus = element.id;
                        } else {
                            trIdFocus = 0;
                        }
                        $(element).remove();
                    });

                    /*----------------'Добавление номера и фона' начало--------------------*/
                    function removeDelBtn() {
                        //Присваиваем data фтрибуту 0, для дальнейшего удаления при выборе ГОСТа
                        $(closestTzBodyFocus).find('tr').data('gost', 0);
                        $(closestTzBodyFocus).find('.del').remove();
                    }

                    //Вставляем временно,строку с методиками, для стандартного визуального отображения таблицы
                    $.ajax({
						//--'Договор в request_new' начало--//
                        //url: 'select_for_tz.php?KEY=' + trIdFocus,
                        url: 'select_for_tz.php?KEY=' + trIdFocus + '&ID_TZ=' + TZID,
						//--'Договор в request_new' окончание--//

                        success: function (data) {
                            closestTzBodyFocus.append(data);
                            /*------------------'Подключение select2 к методикам' начало-----------------*/
                            //К селектам ГОСТа подключаем плагин select2
                            //addPluginSelect2();
                            /*---------------------'Подключение select2 к методикам' окончание-----------*/
                            removeDelBtn();
                        }

                    });

                }
                //}
            });
            /*----------------'Удаление методики' окончание-------------------*/

		//--'Договор в request_new' начало--//
		$('.btn-taken-close').on('click', function() {
			$('.wrepper-modal').css('display', 'none');
			$('.taken').prop('checked', false);
		});

		$('.taken').on('change', function() {

			if ($('.taken').prop('checked')) {
				$('.wrepper-modal').css('display', 'flex');
			} else {
				var tzIdCurrent = $('.id-tz') ? $('.id-tz').val() : '';

				$('.wrepper-modal').css('display', 'none');
				$.ajax({
					method: 'POST',
					url: 'taken.php',
					data: {id_tz_del: tzIdCurrent},
					success: function(deleteTaken) {

					}
				});
				$('.taken-request').text('');
			}
		});

		$('.btn-taken-save').on('click', function(event) {

			var tzCurrent = $('.id-tz') ? $('.id-tz').val() : '';
			var valTekenInput = $('.taken-input').val() ? $('.taken-input').val() : '';

			if ($('.taken-input').val() == '') {
				alert("Заявка не выбрана!");
				return false;
			}

			var idDealTaken = $('#select-taken option').filter(function() {
				return this.value == valTekenInput;
			}).data('id');

			var idDealTaken = idDealTaken ? idDealTaken : '';

			$.ajax({
				method: 'POST',
				url: 'taken.php',
				data: {id_tz: tzCurrent, id_deal: idDealTaken},
				type: 'json',
				success: function (dataTaken) {
					console.log(dataTaken);

					if (dataTaken) {

						$('.wrepper-modal').css('display', 'none');

						$('.taken-request').text(dataTaken);

					}
				}
			});

		});
		//--'Договор в request_new' окончание--//



		<!--'Техническое задание утверждено руководителем лаборатории' окончание-->

		$('.btn-transfer').on('click', function (event) {
			event.preventDefault();
			// console.log('click');
			var idTzCurrentDeal = $('.id-tz') ? $('.id-tz').val() : '';

			var getEditTransfer = '<?= $_GET["EDIT"] ?>' ? '<?= $_GET["EDIT"] ?>' : '';
			var getIdTransfer = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';

			var arrGostTransfer = [];

            $('.methods option:selected').each(function (index, element) {
                arrGostTransfer.push($(element).val());
            });

			var isOkTransfer = checkMethod(arrGostTransfer,getEditTransfer,getIdTransfer);

			//console.log('isOkTransfer', isOkTransfer);
			 if (isOkTransfer && idTzCurrentDeal) {

				 var transferCheckLeader = checkLeader(idTzCurrentDeal, 'btn_transfer');

				 if (transferCheckLeader) {
					//var parseDataTransfer = jQuery.parseJSON(transferCheckLeader);


					if (transferCheckLeader['error']) {
						alert(transferCheckLeader['error']);
					}


					if (transferCheckLeader['transfer']) {
						$('.btn-transfer').attr('disabled', 'disabled');
						//$('#new_tz').attr('disabled', 'disabled');
						$('.text-transfer').text('Заявка передана на проверку!');

						//$('.btn-no-transfer').prop('disabled', false);
						//$('.text-return').text('');
						//$('.tz-confirm').prop('disabled', false);

						/*if ($('#main_user_id') && $('#id_user') && $('#main_user_id').val() == $('#id_user').val()) {
							console.log('table-row');
							$('.tr-confirm').css('display', 'table-row');
							$('.tr-return').css('display', 'table-row');
						}*/
					}

				}
				/*$.ajax({
					method: 'POST',
					url: 'check_methods_and_responsib.php',
					type: 'json',

					data: {id_tz: idTzCurrentDeal, btn_transfer: 'transfer'},
					success: function(dataTransfer) {

						if (dataTransfer) {
							var parseDataTransfer = jQuery.parseJSON(dataTransfer);


							if (parseDataTransfer['error']) {
								alert(parseDataTransfer['error']);
							}


							if (parseDataTransfer['transfer']) {
								$('.btn-transfer').attr('disabled', 'disabled');
								$('#new_tz').attr('disabled', 'disabled');
								$('.text-transfer').text('Заявка передана на проверку!');
							}

						}
					}
				});*/

			 }

		});



		$('.tz-confirm').on('change', function() {
		//$('.tr-confirm').on('change', '.tz-confirm', function() {
				var getEditConfirm = '<?= $_GET["EDIT"] ?>' ? '<?= $_GET["EDIT"] ?>' : '';
				var getIdConfirm = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';

				var arrGostConfirm = [];

				 $('.methods option:selected').each(function (index, element) {
					 arrGostConfirm.push($(element).val());
				 });

				var idTzConfirm = $('.id-tz') ? $('.id-tz').val() : '';
				var tzConfirm = $('.tz-confirm').prop('checked') ? 1 : 0;

				var isOkConfirm= checkMethod(arrGostConfirm,getEditConfirm,getIdConfirm);
				console.log(isOkConfirm);
				if (isOkConfirm) {  //Если методики и исполнители(ответственные) соответствуют
				//Проверяем являются ли ответственные за выполнение методик руководителями лабораторий
					var confirmCheckLeader = checkLeader(idTzConfirm, 'btn_confirm');
					console.log('confirmCheckLeader', confirmCheckLeader);
					if (confirmCheckLeader) {
						//var parseDataConfirm = jQuery.parseJSON(confirmCheckLeader);


						if (confirmCheckLeader['error']) {
							alert(confirmCheckLeader['error']);
							//Если есть не соответствие методики и исполнителей , checked false
							$('.tz-confirm').prop('checked', false);
						}


						if (confirmCheckLeader['transfer']) {
							$.ajax({
								method: 'POST',
								url: 'check_methods_and_responsib.php',
								data: {id_tz: idTzConfirm, tz_confirm: tzConfirm, checbox_confirm: 'confirm'},
								success: function(dataConfirm) {

									console.log(dataConfirm);
									if (dataConfirm) {
										$('.text-confirm').text(dataConfirm);
										$('.btn-no-transfer').prop('disabled', true); // если ТЗ проверено, блокируем кнопку - "Снять заявку переданную на рассмотрение руководителю лаборатории"

									} else {
										$('.text-confirm').text('');
										$('.btn-no-transfer').prop('disabled', false);
									}
								}
							});
						}

					}

				} else { //Если есть не соответствие методики и исполнителей , checked false
					$('.tz-confirm').prop('checked', false);
				}

				/*$.ajax({
					method: 'POST',
					url: 'check_methods_and_responsib.php',
					data: {id_tz: idTzConfirm, tz_confirm: tzConfirm, checbox_confirm: 'confirm'},
					success: function(dataConfirm) {

						console.log(dataConfirm);
						if (dataConfirm) {
							$('.text-confirm').text(dataConfirm);
						} else {
							$('.text-confirm').text('');
						}
					}
				});*/

		});

		//Снять заявку переданную на рассмотрение руководителю лаборатории, кнопка "Вернуть"
		$('.btn-no-transfer').on('click', function() {
			var idTzReturn = $('.id-tz') ? $('.id-tz').val() : '';

			$.ajax({
				method: 'POST',
				url: 'check_methods_and_responsib.php',
				data: {id_tz: idTzReturn, transfer: 'return', btn: 'btn_no_transfer'},
				dataType: 'json',
				success: function(dataReturn) {

					console.log('dataReturn[return]',dataReturn['return']);
					if (dataReturn['return']) {
						$('.text-return').text(dataReturn['return']);
						$('.btn-no-transfer').prop('disabled', true);
						$('.tz-confirm').prop('disabled', true);
						//$('.btn-transfer').prop('disabled', false);
						$('.text-transfer').text('');
					}
				}
			});
		});

		<!--'Техническое задание утверждено руководителем лаборатории' окончание-->

		//--Заявка ВЛК начало--//

		//Событие на кнопку "Привязать заявку"
		$('.link-to-request').on('click', function() {
			let idTzLinkToRequest = $('.id-tz') ? $('.id-tz').val() : '';

			//Если запрос выполнен и option заполнены то повторно запрос не выполняем
			if ($('#listRequest option').length > 0) {
				return false;
			}

			$.ajax({
				method: 'post',
				url: 'link_to_request_for_tz.php',
				data: {id_tz: idTzLinkToRequest, request: 'allRequestIC'},
				type: 'json',
				success: function(answer) {

					answer = jQuery.parseJSON(answer);


					let listRequest = $('#listRequest');

					if (answer['allRequestIC']) {
						$.each(answer, function(keyAnswer, valAnswer) {
							if (valAnswer) {
								let tzIdRequest = valAnswer['tz_id'] ? valAnswer['tz_id'] : '';
								let requestTitle = valAnswer['request_title'] ? valAnswer['request_title'] : '';
								let companyTitle = valAnswer['company_title'] ? valAnswer['company_title'] : '';

								//Если первый элемент добавляем пустой элемент
								if (keyAnswer == '0') {
									$('#listRequest').append('<option></option>');
								}

								//$('#listRequest').append('<option data-id="' + tzIdRequest + '">' + requestTitle + ', ID ' + tzIdRequest + '<option>');
								$('#listRequest').append('<option data-id="' + tzIdRequest + '">' + companyTitle + ', ' + requestTitle + ', ID ' + tzIdRequest + '<option>');

							}

						});

						$('.wrapperListRequest').css('display', 'block');
					}
				}

			});
		});

		//Событие выбор заявки(сделки) из списка Заявок ИЦ
		$('.inputListRequest').on('change', function() {
			let selectedValue = $(this).val() ? $(this).val() : ''; //Выбранная заявка(сделка из списка)
			let selectedIdRequest = $('#listRequest option').filter(function() {
				return this.value == selectedValue;
			}).data('id');

			let idRequest = selectedIdRequest ? selectedIdRequest : ''; // ID выбранной заявки для экспорта данных

			//Если запрос выполнен и option заполнены то повторно запрос не выполняем
			if ($('#listMaterial option').length > 0) {
				return false;
			}

			$.ajax({
				method: 'post',
				url: 'link_to_request_for_tz.php',
				data: {id_tz: idRequest, request: 'selectedRequestIC'},
				type: 'json',
				success: function(dataImport) {

					dataImport = jQuery.parseJSON(dataImport);

					//Если запрос вернул список материалов и кол-во материалов больше 1
					if (dataImport['material'] && dataImport['material'].length > 1) {

						$.each(dataImport['material'], function(keyMaterial, valMaterial) {
							//let tzIdMaterial = keyMaterial ? keyMaterial : '';
							let titleMaterial = valMaterial ? valMaterial : '';

							//Если первый элемент добавляем пустой элемент
							if (keyMaterial == '0') {
								$('#listMaterial').append('<option></option>');
							}

							$('#listMaterial').append('<option>' + titleMaterial + '<option>');

						});

						$('.wrapperListMaterial').css('display', 'block');
						alert('Выберите материал для импорта данных в заявку');
					} else if (dataImport['material']) {
						$('input[name^="mater"]').first().val(dataImport['material']);
						$('input[name^="idMater"]').first().val(dataImport['material']);
					}

					console.log('dataImport',dataImport);
					//Если данные по АКТу приемки проб существуют вставляем их
					if (dataImport['act']) {
						console.log('dataImport[act]',dataImport['act']);
						let descriptionExport = dataImport['act']['description'] ? dataImport['act']['description'] : ''; //Описание объекта испытаний
						let actNumExport = dataImport['act']['actNum'] ? dataImport['act']['actNum'] : '';  //Номер акта приемки проб
						let dateActExport = dataImport['act']['dateAct'] ? dataImport['act']['dateAct'] : ''; //Дата акта приемки проб
						let samplingLocationExport = dataImport['act']['samplingLocation'] ? dataImport['act']['samplingLocation'] : ''; //Место отбора проб
						let samplingDateExport = dataImport['act']['samplingDate'] ? dataImport['act']['samplingDate'] : ''; //Дата отбора проб
						let tzNoSamples = dataImport['act']['actNum'] == 0 ? true : false; //Если нет АКТа приемки проб(dataImport['act']['actNum'] == 0), то true - чекбокс будет отмечен

						$('.test_description').val(descriptionExport);
						$('input[name="ACT_NUM"]').val(actNumExport);
						$('input[name="DATE_ACT"]').val(dateActExport);
						$('input[name="PLACE_PROBE"]').val(samplingLocationExport);
						$('input[name="DATE_PROBE"]').val(samplingDateExport);
						$('.tzNoSamples').prop('checked', tzNoSamples); //Пробы еще не поступили
					}
				}
			});
		});

		//Событие выбор заявки(сделки) из списка Материалов ИЦ
		$('.inputListMaterial').on('change', function() {
			let selectedValueMaterial = $(this).val() ? $(this).val() : ''; //Выбранная материал(из списка) для импорта в ВЛК

			//console.log(selectedValueMaterial);
			if (selectedValueMaterial) {
				$('input[name^="mater"]').first().val(selectedValueMaterial);
				$('input[name^="idMater"]').first().val(selectedValueMaterial);
			}

		});
		//$('input[name^="mater"]').first();

		//--Заявка ВЛК окончание--//
        });
    </script>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
