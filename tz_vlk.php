<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Формирование технического задания");

include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";

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
        /*-------------'Методика и исполнители не соответствуют'--------------*/
        $(document).ready(function () {

            $('.usersCompany').select2();
            $spanSelect2 = $('.usersCompany').closest('.companyEmployees').find('.select2-selection.select2-selection--single');
            $spanSelect2.css({
                'width': '470px'
            });
        });
    </script>

    <script>
        /*$(document).ready(function () {

            $('.gost').select2();
        });*/
    </script>
    <!-----------------------'Подключение select2 к методикам' начало--------------------->
    <script>
        $(document).ready(function () {
            <!--'При наведении мыши на списки методики, подключаем select2'-->
            /*$('#form_tz').on('mouseenter', '.styleGost', function () {
                $(this).select2();
            });*/

            $('#form_tz').on('mouseenter', '.tdGost', function () {
                //console.log('mouseenter this', $(this));
				//Запрет на редактирование данных
                $(this).find('.styleGost').select2();
            });

            $('#form_tz').on('mouseleave', '.tdGost', function () {
                var focusedName = document.activeElement.className;

                if (focusedName == 'select2-search__field') {
                    return true;
                }

                $(".styleGost.select2-hidden-accessible").select2('destroy');
            });

            <!--'После выбора методики отлючаем select2'-->
            $('#form_tz').on('change', '.styleGost', function (i, obj) {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    setTimeout(function () {
                        $(".styleGost.select2-hidden-accessible").select2('destroy');
                    }, 0);
                }
            });

            <!--'При попадании в фокус элемента, проверяем, если элемент не является списком методик, то отключаем select2 у оставшихся подключенных методик'-->
            $('#form_tz').on('focusin input', function (event) {
                var targetElement = event.target;
                if (!$(targetElement).is(".select2-selection")) {
                    $(".styleGost.select2-hidden-accessible").select2('destroy');
                }
            });

        });
    </script>
    <!-----------------------'Подключение select2 к методикам' окончание--------------------->
	
	<!--'Техническое задание утверждено руководителем лаборатории' окончание-->

	<!--'Техническое задание утверждено руководителем лаборатории' окончание-->
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

    $ba_tz = $DB->Query("SELECT * FROM `ba_tz_vlk` WHERE ID=" . $_POST['tz_copy'])->Fetch();

    $res = $DB->Query("INSERT INTO `ba_tz`(`TZ`,`OBJECT`, `DATE_SOZD`, `ID_Z`, `DOGOVOR_NUM`, `ACT_NUM`, `DESCRIPTION`, `DATE_ACT`) VALUES ('" . $ba_tz['TZ'] . "','" . $_POST['OBJECT'] . "',NOW()," . (int)$_GET['ID'] . ",'" . $_POST['DOGOVOR_NUM'] . "'," . (int)$_POST['ACT_NUM'] . ",'" . $_POST['DESCRIPTION'] . "','" . $_POST['DATE_ACT'] . "')");
    if ($res->result = 1) {
        echo "<h3>Успешно</h3>";
    } else {
        echo "<h3>Ошибка</h3>";
    }

    $arr = unserialize($ba_tz['TZ']);

    $ID_TZ = (!empty(((int)$_POST['id_tz'])) ? (int)$_POST['id_tz'] : $DB->LastID());

    /*if (CModule::IncludeModule('crm')) {
        $deal = new CCrmDeal;
        $arDealUpdate = array('UF_CRM_1554700718621' => $_SERVER["SERVER_NAME"] . "/tz_show.php?ID=" . $ID_TZ, 'STAGE_ID' => 'PREPARATION');
        $resUpd = $deal->Update((int)$_POST['id_deal'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    }*/

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
    <a href="/tz_show_vlk.php">К списку ТЗ</a>
    |
    <a href="/<?= ($_POST['ACT_LIST'] == '3' ? 'control_list' : 'probe') ?>.php?ID=<?= $_POST['id_deal'] ?>"><?= ($_POST['ACT_LIST'] == '3' ? 'Создать контрольный лист' : 'Создать акт приемки проб') ?></a>
    <?php
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
    exit;
}

if (!empty($_POST['mater'])) {
	//--Заявка ВЛК начало--//
	$getEditIdTz = !empty($_GET['EDIT']) ? (int)$_GET['EDIT'] : 0; //ID ТЗ
	$actNum = !empty($_POST['ACT_NUM']) ? $_POST['ACT_NUM'] : 0; //Номер АКТа приемки проб
	//--Заявка ВЛК окончание--//

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
        $res_tz = $DB->Query("SELECT * FROM `ba_tz_vlk` WHERE `ID`=" . $_GET['EDIT'])->Fetch();
        $probe_edit = unserialize($res_tz['PROBE']);

    }
	//--' Вывод дедлайн в журнале заявок' начало--//
	if (!empty($_GET['ID'])) {
        //$res_tz_get_id = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID'])->Fetch();
    }
	//--' Вывод дедлайн в журнале заявок' окончание--//
	
    //$deal1 = CCrmDeal::GetByID($_GET['ID']);
    $y_date = date("Y", strtotime($_POST['DATE_ACT']));
    if ($y_date != '2020') $y_date = "/" . substr($y_date, 2);
    else $y_date = "/" . $y_date;

    //$extranet = $USER_FIELD_MANAGER->GetUserFields('CRM_COMPANY', $deal1['COMPANY_ID']);
    //$extranet_id = $extranet['UF_CRM_1571818346092']['VALUE'];
	
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

    //$dealUp = CCrmDeal::GetByID($res_tz['ID_Z']);

    //$updDeal = CCrmDeal::GetByID($_GET['ID']);

    //$id_deal = (isset($_GET['EDIT'])) ? $dealUp['ID'] : $updDeal['ID'];
	//--'Техническое задание утверждено руководителем лаборатории' начало--//
	
	/*$current_deal = null;
	if (!empty($dealUp)) {
		$current_deal = $dealUp;
	} elseif(!empty($updDeal)) {
		$current_deal = $updDeal;
	}*/
	
	//--'Техническое задание утверждено руководителем лаборатории' окончание--//
	//--Заявка ВЛК начало--//
	//==============================================================================================================================//
	//Проверяем если существует и выбрана заявка для экспорта, производим импорт данных в ВЛК
	if (!empty($_POST['req_VLK'])) {
		//Получаем id Заявки(сделки), откуда данные будут экспортироваться
//		$arrayDataRequest = explode('ID', $_POST['req_VLK']);
//		$idRequestExport = !empty($arrayDataRequest[1]) ? trim($arrayDataRequest[1]) : '';
		$idRequestExport = $_POST['req_VLK'] ?? '';
		$numProbeExportAll = $_POST['probe-VLK'] ?? '';
		$numProbeExport = explode('/', $numProbeExportAll);
		$resultSelectBaTzDataExport = null;
		if (!empty($idRequestExport)) {
			$resultSelectBaTzDataExport = $DB->Query('SELECT * FROM `ba_tz` WHERE `ID`=' . (int)$idRequestExport)->Fetch();
		}
		
		//ID Заявки сделки
		$idExportRequest = !empty($resultSelectBaTzDataExport['ID_Z']) ? (int)$resultSelectBaTzDataExport['ID_Z'] : '';
		
		//Находим данные Экспортируемой Заявки(сделки) из битрикса
		$dataRequestExport = CCrmDeal::GetByID($idExportRequest);
		$companyIdExport = !empty($dataRequestExport['COMPANY_ID']) ? $dataRequestExport['COMPANY_ID'] : ''; //id Компании
		
		
		/*if (CModule::IncludeModule('crm')) {
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
		}*/
		
		//Клиент
		$companyTitleDealEdit = !empty($dataRequestExport['COMPANY_TITLE']) ? $DB->ForSql($dataRequestExport['COMPANY_TITLE']) : '';
		
		//Является ли клиен ИП
		$checkIp = !empty($resultSelectBaTzDataExport['CHECK_IP']) ? $resultSelectBaTzDataExport['CHECK_IP'] : '';
		
		//Импортируем данные выбранной заявки в заявку ВЛК
		/*if (!empty($current_deal['ID'])) {
			$resultUpdateImport = $DB->Query("UPDATE `ba_tz` SET `COMPANY_TITLE`= '" . $company_title_deal_edit . "', `CHECK_IP`= '" . $checkIp . "', `IMPORT_TZ_ID_VLK`= " . $idRequestExport . " WHERE `ID_Z`=" . (int)$current_deal['ID']);
		}*/
		if (!empty($getEditIdTz)) {
			$resultUpdateImport = $DB->Query("UPDATE `ba_tz_vlk` SET `COMPANY_TITLE`= '" . $companyTitleDealEdit . "', `CHECK_IP`= '" . $checkIp . "', `IMPORT_TZ_ID_VLK`= " . $_POST['req_VLK'] . ", `import_probe` = '{$numProbeExport[0]}' WHERE `ID`=" . $getEditIdTz);
		}
		
		//Смотрим есть ли запись в таблице AKT_VLK для выбраной заявки ВЛК
		$resultSelectActVlk = null;
		if (!empty($getEditIdTz)) {
			$resultSelectActVlk = $DB->Query('SELECT * FROM `AKT_VLK` WHERE `tz_id`=' . $getEditIdTz)->Fetch();
		}
	
		if (empty($resultSelectActVlk['id']) && !empty($getEditIdTz)) { //Если нет записи по этой заявке(сделки), то создаем
			$resultInsertAktVlk = $DB->Query("INSERT INTO `AKT_VLK` SET `tz_id` = {$getEditIdTz}, `act_number` = '{$actNum}'");
		} else if (!empty($resultSelectActVlk['id']) && !empty($getEditIdTz)) { //Если есть записи по этой заявке , то обновляем
		
			$resultUpdateAktVlk = $DB->Query('UPDATE `AKT_VLK` SET `act_number`=' . $actNum . ' WHERE `tz_id`=' . $getEditIdTz);
		}
	}

	//--Заявка ВЛК окончание--//

	$extranet_id = !empty($current_deal['COMPANY_ID']) && $current_deal['COMPANY_ID'] == 1184 ? 56 : '';
	
    //$res = $DB->Query("delete from `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = " . (int)$id_deal);
	if ($getEditIdTz) {
		$res = $DB->Query("delete from `MATERIALS_TO_REQUESTS` WHERE `ID_TZ_VLK` = " . $getEditIdTz);
		
		foreach ($ar_material_id as $key => $ar_m) {
			//$DB->Query("INSERT INTO `MATERIALS_TO_REQUESTS` (`ID_DEAL`, `ID_MATERIAL`, `NAME_MATERIAL`) VALUES (" . (int)$id_deal . ", " . (int)$ar_m . ",'" . $ar_material[$key] . "')");
			$DB->Query("INSERT INTO `MATERIALS_TO_REQUESTS` (`ID_TZ_VLK`, `ID_MATERIAL`, `NAME_MATERIAL`) VALUES (" . $getEditIdTz . ", " . (int)$ar_m . ",'" . $ar_material[$key] . "')");
		}
	}
    

    

//----------------------'Серверная обработка Журнала заявок' начало---------------------//
    //Колонка "Обьект испытания"
		$mater_arr_up=array();
		//$res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=" . (int)$id_deal);
		$res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_TZ_VLK`=" . $getEditIdTz);
		while($mater=$res_mat->Fetch())
		{
			$mater_arr_up[]=$mater['NAME_MATERIAL'];
		}
		//$dealMaterials = ($deal_new['UF_CRM_1576411922643'] ? $deal_new['UF_CRM_1576411922643'] : implode(', ', $deal_new['UF_CRM_1569151529509']));
		//$dealMaterials = ($dealMaterials  ? $dealMaterials : implode(', ', $mater_arr_up));
		$dealMaterials = (!empty($mater_arr_up)  ? implode(', ', $mater_arr_up) : '');
    //----------------------'Серверная обработка Журнала заявок' окончание---------------------//
    /*-------------'Одинаковое наименование материалов в tz' начало----------------------------*/
    foreach ($_POST['mater'] as $key => $item) {

        /*$arrData[$_POST['mater'][$key]][$key]['gost'] = $_POST['gost'][$key];
        $arrData[$_POST['mater'][$key]][$key]['obiem'] = $_POST['obiem'][$key];
        $arrData[$_POST['mater'][$key]][$key]['gost_new'] = $_POST['gost_new'][$key];
        $arrData[$_POST['mater'][$key]][$key]['price'] = $_POST['price'][$key];*/

        $arrData[$key][$_POST['mater'][$key]][$key]['gost'] = $_POST['gost'][$key];
        $arrData[$key][$_POST['mater'][$key]][$key]['obiem'] = $_POST['obiem'][$key];
        $arrData[$key][$_POST['mater'][$key]][$key]['gost_new'] = $_POST['gost_new'][$key];
        $arrData[$key][$_POST['mater'][$key]][$key]['price'] = $_POST['price'][$key];
        /*-------------'Одинаковое наименование материалов в tz' окончание--------------------------*/
        $probe[$key_p] = array(
            "zakaz_man" => $deal1['COMPANY_ID'],
            "number_probe" => [],
            "probe_proizv" => 'заказчик',
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

    /*-------------'Одинаковое наименование материалов в tz' начало----------------------------*/

    //foreach ($arrData as $key => $arrDatum) {
    foreach ($arrData as $keys => $arrDatums) {

        foreach ($arrDatums as $key => $arrDatum) {

            foreach ($arrDatum as $item) {

                /*-------------'Одинаковое наименование материалов в tz' окончание----------------------------*/
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
            /*-------------'Одинаковое наименование материалов в tz' начало----------------------------*/
        }
        /*-------------'Одинаковое наименование материалов в tz' окончание----------------------------*/
    }


    for ($i = 1; $i <= count($arr); $i++)
        for ($j = 0; $j < count($arr[$i]['gost']); $j++) {
            $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $arr[$i]['gost'][$j])->Fetch();
            if (!$res['IN_OA'] && $res['ID'] != 2875) //Выезд специалиста (ID 2875) не учитываем
                $attestat[] = $res['ID'];

            $price = $price + ($arr[$i]['price'][$j]) * $arr[$i]['obiem'];

        }

    $in_oa = 1;
    if (count($attestat)) $in_oa = 0;
	//----------------------'Серверная обработка Журнала заявок' начало---------------------//
    
    $contract_array = explode('ID', $_POST['DOGOVOR_NUM']);
    //Колонка "Договор"
    $dogovor_table = !empty($contract_array[0]) ? $contract_array[0] : '';
    
	//--' Вывод дедлайн в журнале заявок' начало--// 
    //Колонка "Крайний срок"
    //$deadline_table = !empty($res_tz['DEADLINE_TABLE']) ? $res_tz['DEADLINE_TABLE'] : date('d.m.Y H:i:s', strtotime("01.01.1970")+ 86400*3);
	$result_select_ba_tz = null;
	if (!empty($_GET['ID'])) { //Если формируем новое ТЗ
		$result_select_ba_tz = $res_tz_get_id;
	} elseif (!empty($_GET['EDIT'])) { //Если редактируем ТЗ
		$result_select_ba_tz = $res_tz;
	}

	//$date_create = !empty($result_select_ba_tz['DATE_CREATE']) ? date("d.m.Y", strtotime($deal_new['DATE_CREATE'])) : date('d.m.Y H:i:s');
    $deadline_table = !empty($result_select_ba_tz['DEADLINE_TABLE']) ? $result_select_ba_tz['DEADLINE_TABLE'] : date('d.m.Y H:i:s');
    //--' Вывод дедлайн в журнале заявок' окончание--//
	$deadline_table_up = !empty($_POST['DEADLINE_PROTOCOL']) ? date('d.m.Y H:i:s', strtotime($_POST['DEADLINE_PROTOCOL'])) : $deadline_table;
    if (!empty($_POST['DATE_ACT'])) {
        $y_date_act = date("Y", strtotime($_POST['DATE_ACT']));
        if ($y_date_act != '2020') {
            $y_date_act = "/" . substr($y_date_act, 2);
        } else {
            $y_date_act = "/" . $y_date_act;
        }
    }
   
    $num_act = !empty($_POST['ACT_NUM']) ? $_POST['ACT_NUM'] : '';
    $first_probe = $_POST['first_probe'] ?? '';
																

    if (!empty($_POST['id_tz'])) {
        $deadlineProtocol = $_POST['DEADLINE_PROTOCOL'];
		//--'Договор в request_new' начало--//
        $res = $DB->Query("UPDATE `ba_tz_vlk` SET `TZ`='" . serialize($arr) . "',`OBJECT`='" . $_POST['OBJECT'] . "',`DESCRIPTION`='" . $_POST['DESCRIPTION'] . "', `ACT_NUM`='{$_POST['ACT_NUM']}', `secret_probe`='{$_POST['ACT_NUM']}', `PRICE`=" . $price . ",`DATE_ACT`='" . $_POST['DATE_ACT'] . "',`EXTRANET_ID`='" . $extranet_id . "',`CONTROL_LIST`='" . ($_POST['ACT_LIST'] == '3' ? '1' : '0') . "', `PROBE` = '" . serialize($probe) . "',`DEADLINE` = '" . $deadlineProtocol . "', `DEADLINE_TABLE` = '" . $deadline_table_up . "', `MATERIAL` = '" . $dealMaterials . "', `NUM_ACT_TABLE` = '" . $num_act . "', `ATTESTAT` = '" . $in_oa . "', `first_probe` = '{$first_probe}' WHERE ID = " . (int)$_POST['id_tz']);
        //--'Договор в request_new' окончание--//
		// $res_obj = $DB->Query("UPDATE `ba_tz` SET `TZ`='" . serialize($arr) . "',`OBJECT`='" . $_POST['OBJECT'] . "',`DESCRIPTION`='" . $_POST['DESCRIPTION'] . "',`ID_Z`='" . (int)$_POST['id_deal'] . "',`TYPE_TZ`='" . (int)$_POST['type_tz'] . "',`DOGOVOR_NUM`='" . $_POST['DOGOVOR_NUM'] . "',`ACT_NUM`='" . (int)$_POST['ACT_NUM'] . "',`PRICE`='" . $price . "',`DATE_ACT`='" . $_POST['DATE_ACT'] . "',`EXTRANET_ID`='" . $extranet_id . "' WHERE ID = " . (int)$_POST['id_tz']);
        
		if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    }
	//--'Нумерация материалов в ТЗ' начало--//
	$id_ba_tz = !empty($result_select_ba_tz['ID']) ? $result_select_ba_tz['ID'] : '';
    //$ID_TZ = (!empty(((int)$_POST['id_tz'])) ? (int)$_POST['id_tz'] : $DB->LastID());
    $ID_TZ = (!empty(((int)$_POST['id_tz'])) ? (int)$_POST['id_tz'] : $id_ba_tz);
	//--'Нумерация материалов в ТЗ' окончание--//

    for ($i = 0; $i < count($res_otbor['NUM_PROBE']); $i++) {
        for ($j = 0; $j < count($res_otbor['NUM_PROBE'][$i]); $j++) {
            $res_all_otbor = $DB->Query("INSERT INTO `PODGOTOVKA`(`NUM_PROBE`,`GOST_ID`, `TZ_ID`, `PROBE_NAME`, `GOST_OTBOR_ID`) VALUES ('" . $res_otbor['NUM_PROBE'][$i][$j] . "', " . (int)$res_otbor['GOST_ID'][$i][$j] . ", " . (int)$ID_TZ . ", '" . $res_otbor['PROBE_NAME'][$i][$j] . "', " . (int)$res_otbor['GOST_OTBOR_ID'][$i][$j] . ")");
        }
    }

    $numberField = $_GET['EDIT'] ? "`ID`=" . (int)$_GET['EDIT'] : "`ID_Z`=" . (int)$_GET['ID'];

    if (isset($_POST['hiddenNotDiscount']) && $_POST['hiddenNotDiscount'] == 1
        && (isset($_GET['EDIT']) && !empty($_GET['EDIT']) || isset($_GET['ID']) && !empty($_GET['ID']))) {
        $DB->Query("UPDATE `ba_tz_vlk` SET `DISCOUNT`='0' WHERE " . $numberField)->Fetch();
    }

    if (isset($_POST['hiddenIsDiscount']) && $_POST['hiddenIsDiscount'] == 1
        && (isset($_GET['EDIT']) && !empty($_GET['EDIT']) || isset($_GET['ID']) && !empty($_GET['ID']))) {
        $DB->Query("UPDATE `ba_tz_vlk` SET `DISCOUNT`='" . (int)$_POST['discountName'] . "' WHERE " . $numberField)->Fetch();
    }

    //Дата сохранения технического задания в историю
    $date_save = date('Y-m-d H:i:s');

    $assigned = CUser::GetByID($USER->GetID())->arResult[0];
    //Имя пользователя
    $assigned_name = !empty($assigned['NAME']) ? $assigned['NAME'] : '';
    //Фамилия пользователя
    $assigned_lastname = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';
    //Пользователь
    $assigned_user = $assigned_name.' '.$assigned_lastname;

    if (!empty($_GET['EDIT'])) {
        $res_update = $DB->Query("SELECT * FROM `ba_tz_vlk` WHERE `ID`=" . (int)$_GET['EDIT']);
        $ba_tz_update = $res_update->Fetch();
		//---------'Изменение номера ТЗ' начало ------//
        $tz = $ba_tz_update['ID'];
        //---------'Изменение номера ТЗ' окончание ------//														 
        //Номер протокола
        //$ID_P = $ba_tz_update['NUM_P'] ? $ba_tz_update['NUM_P'] : 'номер не присвоен';
        $ID_P = $ba_tz_update['NUM_P'] ? $ba_tz_update['NUM_P'] : '';
        //Номер заявки
        //$num_request = !empty($dealUp["TITLE"]) ? $dealUp["TITLE"] : '';
        $num_request = !empty($ba_tz_update["REQUEST_TITLE"]) ? $ba_tz_update["REQUEST_TITLE"] : '';
        //---------'Изменение номера ТЗ' начало ------//
        $res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date_save . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$tz . "','" . $USER->GetID() . "', 'Обновление технического задания', '" . $num_request . "')");
        //---------'Изменение номера ТЗ' окончание ------//

        //----------------------'Серверная обработка Журнала заявок' начало---------------------//
        //if ($ba_tz_update['ID_Z'])
        if ($ba_tz_update['ID'])
        {
            $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$tz. " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
            $history_ass=$history['ASSIGNED'];
            $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
            $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
            if($history_date<$history_date_null) $history_date='';
        }
        //Колонка "последнее изменение"
        $assignet_history = $history_ass . " " . $history_date;

        $update_assignet_ba_tz = $DB->Query("UPDATE `ba_tz_vlk` SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID` = " . (int)$ba_tz_update['ID']);

    }
	
	$current_ba_tz = null;
	if (!empty($ba_tz_insert)) {
		$current_ba_tz = $ba_tz_insert;
	} elseif (!empty($ba_tz_update)) {
		$current_ba_tz = $ba_tz_update;
	}
	//--'Техническое задание утверждено руководителем лаборатории' окончание--//
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
	<a href="/request_card_vlk.php?ID=<?=$ba_tz_update['ID_Z'] ?>">Вернуться в карточку</a> |
    <a href="/tz_vlk.php?EDIT=<?= $ID_TZ ?>">Вернуться к редактированию</a>
    <?php
    if (!empty($_POST['ACT_NUM'])) {
        echo "| <a href='/results_isp_vlk.php?ID=" . $_GET['EDIT'] . "'>Перейти к испытаниям</a>";
    }
    ?>
	<input class="tz id-tz" type="hidden" name="id_tz" value="<?= $current_ba_tz['ID'] ?>">
	 
	<?php

	//--'Техническое задание утверждено руководителем лаборатории' окончание--//
	
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
    exit;
}

if (!empty($_GET['EDIT'])) {
    $res = $DB->Query("SELECT * FROM `ba_tz_vlk` WHERE `ID`=" . (int)$_GET['EDIT']);
    $row = $res->Fetch();
    $dateDedline = $row['DEADLINE'];
    //$deal = CCrmDeal::GetByID($row['ID_Z']);
    $mater_arr = unserialize($row['TZ']);
    $probe_arr = unserialize($row['PROBE']);
    $probe_place = explode("; ", $probe_arr[1]['mesto_data']);
    $company1 = CCrmCompany::GetByID(19);
    $extranet_id = CCrmCompany::GetUserFields(19);//$company1['UF_CRM_1571818346092'];
    //$companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch();
    //$dataDiscount = $USER_FIELD_MANAGER->GetUserFields('CRM_COMPANY', $companys['ID']);
    $dataDiscountVal = 0;
    if ($row['DISCOUNT']) {
        $dataDiscountVal = $row['DISCOUNT'];
    } else {
        //$dataDiscountVal = $dataDiscount['UF_CRM_1582914274353']['VALUE'];
    }
	//--'Техническое задание утверждено руководителем лаборатории' начало--//
	$result_select_check_tz = null;
	if ($row['ID']) {
		//$result_select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $row['ID']);  
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
	
	//--'Техническое задание утверждено руководителем лаборатории' окончание--//
    ?>
    <form id="form_tz" method="post">
        <!---------------------------------------  начало 'Меню' -------------------------------------->
        <div>
            <a href="/request_card_vlk.php?ID=<?= $row['ID_Z'] ?>">
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
                <div class='further disable'
                     title="<?= ($row['CONTROL_LIST'] ? 'Создать контрольный лист' : 'Создать акт приемки проб') ?>"></div>
            </a>
        </div>
        <!---------------------------------------- окончание 'Меню' ----------------------------------->
        <input type="text" name="id_deal" value="<?= $row['ID_Z'] ?>" hidden>
        <table id="tz">
            <td width="250" height="40">Заявка</td>
            <td><b><?= $row['REQUEST_TITLE'] ?></b></td>
            </tr>
			<tr>
				<td width="250" height="40">Номер первичной пробы:</td>
				<td>
					<input class="tz" type="text" name="first_probe"  value="<?= $row['first_probe']?>" required>
				</td>
			</tr>
			<tr>
                <td width="250" height="40">Номер шифрованной пробы:</td>
                <td>
					<input class="tz" type="text" name="ACT_NUM"  value="<?= $row['secret_probe']?>" required>
                    <input class="tzHidden" type="number" value="" disabled>
                </td>
            </tr>
<!--            <tr>-->
<!--                <td width="250" height="40">Дата документа-основания</td>-->
<!--				<td><input class="tz" type="date" name="DATE_ACT"-->
<!--						   value="--><?//=!empty($row['DATE_SOZD']) ? date('Y-m-d', strtotime($row['DATE_SOZD'])) : ''?><!--"-->
<!--						   size="20" required readonly></td>-->
<!--            </tr>-->
			<?php
				//--Заявка ВЛК начало--//
				//Если тип заявки(сделки) ВЛК Привязка к заявке, показываем кнопку привязки к заявке
				if (!empty($row['type_results_vlk']) && $row['type_results_vlk'] == '1') :
			?>
			<tr>
				<td>Привязать ВЛК к заявке:</td>
				<?php
					if (!empty($row['IMPORT_TZ_ID_VLK'])) {
					$res_con = $DB->Query("SELECT * FROM ba_tz WHERE ID=" . $row['IMPORT_TZ_ID_VLK'])->Fetch();
						echo "<td><a href='request_card.php?ID=" . $res_con['ID_Z'] . "' target='_blank'>" . $res_con['REQUEST_TITLE'] .' '. $res_con['COMPANY_TITLE'] ."</a></td>";
					} else {?>
					<td>
						<select class="req_VLK" name="req_VLK">
							<option disabled selected>выберите заявку</option>
							<?php $reqVLK = $DB->Query("SELECT * FROM `ba_tz` WHERE `ACT_NUM` IS NOT NULL AND `PROBE` IS NOT NULL");
							while ($rowVLK = $reqVLK->Fetch()):?>
							<option value="<?= $rowVLK['ID']?>" <?= $row['IMPORT_TZ_ID_VLK'] == $rowVLK['ID'] ? 'selected' : '' ?>><?= $rowVLK['REQUEST_TITLE']?></option>
							<?php endwhile;?>
						</select>
					</td>
					<td>
						<select class="probe-VLK" name="probe-VLK" disabled>
							<option selected>Выберите пробу</option>
						</select>
					</td>
<!--				<td>-->
<!--					<button type="button" class="link-to-request">Привязать к заявке</button><br>-->
<!--                    <div class="wrapperListRequest" style="margin-top: 5px">-->
<!--                        <br><label id="labelListRequest">Список заявок ИЦ:</label>-->
<!--                        <input list="listRequest" for="labelListRequest" class="inputListRequest" name="inputListRequest">-->
<!--                    </div>-->
<!--				</td>-->
			</tr>
			<?php }
				endif;
				//--Заявка ВЛК окончание--//
			?>
			<!--'Техническое задание утверждено руководителем лаборатории' окончание-->
        </table>
        <br/>
        <br/>
        <?php
		
        //$materialsReq = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = " . $row['ID_Z']);
        $materialsReq = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_TZ_VLK` = " . $row['ID']);
        /*----------------'При выборе материала, добавление методик' начало---------------------*/
        $number_material = 0;

        foreach ($mater_arr as $key => $arrDatum) {
            if (empty($key) && $key == 0) {
                $number_material = 1;
            }
            /*----------------'При выборе материала, добавление методик' окончание------------------*/
            $html = "<select name='gost[]' required>";
            ?>
            <div class='wrapperMaterial'>
                <br/>
                <table id="tz">

                    <?php

                    if ($materialsReq->SelectedRowsCount() > 0) {
                        $material = $materialsReq->Fetch();
                        ?>
						<!--'Нумерация материалов в ТЗ' начало-->
                        <tr class="tr-material">
						<!--'Нумерация материалов в ТЗ' окончание-->
                            <!----------------'При выборе материала, добавление методик' начало--------------------->
                            <td class="number-material"><?= ($key + $number_material) ?></td>
                            <!----------------'При выборе материала, добавление методик' окончание------------------>
                            <td style="font-weight:bold">Материал</td>
                            <td>
							<!--'Нумерация материалов в ТЗ' начало-->
                                <input class="tz" type='hidden' name='mater[<?= $key ?>]'
                                       value="<?= $material['NAME_MATERIAL'] . ($material['ID_MATERIAL'] ? ', ID ' . $material['ID_MATERIAL'] : '') ?>">
                                <input class="tz" type='text' name='idMater[<?= $key ?>]' list="materials" required
                                       value="<?= !empty($material['NAME_MATERIAL']) ? $material['NAME_MATERIAL'] : '' ?>">
                            </td>
							<!--'Нумерация материалов в ТЗ' окончание-->
                            <td style="font-weight:bold">Кол-во проб/образцов</td>
                            <!----------------'При выборе материала, добавление методик' начало--------------------->
                            <td class="amount">
                                <input class="tz" type='number' name='obiem[<?= $key ?>]' min='1' required
                                       value="<?= $arrDatum['obiem'] ?>">
                            </td>
<!--                            <td class="btn-add-del">-->
<!--                                <input type="button" value="+" id="new_material">-->
<!--                                <input type="button" value="-" name="delMaterial" class='delMaterial'>-->
<!--                            </td>-->
                            <!----------------'При выборе материала, добавление методик' окончание------------------>
                            <!----------------'Удаление методики' начало------------------->
<!--                            <td>-->
<!--                               'Кнопка удаление методики'-->
<!--                                <input type="button" value="Удалить методики" name="delMetodic" class='delMetodic'>-->
<!--                            </td>-->
                            <!----------------'Удаление методики' окончание------------------->
                        </tr>
                        <!-------------------'При выборе материала, добавление методик' начало-------->
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
                        <!-------------------'При выборе материала, добавление методик' окончание-------->
                        <?php
                    } else {
                        ?>
						<!--'Нумерация материалов в ТЗ' начало-->
                        <tr class="tr-material">
						<!--'Нумерация материалов в ТЗ' окончание-->
                            <!----------------'При выборе материала, добавление методик' начало--------------------->
                            <td class="number-material"><?= ($key + $number_material) ?></td>
                            <!----------------'При выборе материала, добавление методик' окончание------------------>
                            <td style="font-weight:bold">Материал</td>
                            <td>
                                <input class="tz" type='hidden' name='mater[<?= $key ?>]'
                                       value="<?= $arrDatum['mater'] . (!strpos($arrDatum['mater'], 'ID') ? ', ID ' . array_search($arrDatum['mater'], $materialsArr) : '') ?>">
                                <input class="tz" type='text' name='idMater[<?= $key ?>]' list="materials" required
                                       value="<?= explode(', ID', $arrDatum['mater'])[0] ?>">
                            </td>
                            <td style="font-weight:bold">Кол-во проб/образцов</td>
                            <!----------------'При выборе материала, добавление методик' начало--------------------->
                            <td class="amount">
                                <input class="tz" type='number' name='obiem[<?= $key ?>]' min='1' required
                                       value="<?= $arrDatum['obiem'] ?>">
                            </td>
                            <td class="btn-add-del">
<!--                                <input type="button" value="+" id="new_material">-->
<!--                                <input type="button" value="-" name="delMaterial" class='delMaterial'>-->
                            </td>
<!--                            <--------------'При выборе материала, добавление методик' окончание------------------>
<!--                            <--------------'Удаление методики' начало------------------->
                            <td>
<!--                                'Кнопка удаления методики'-->
<!--                                <input type="button" value="Удалить методики" name="delMetodic" class='delMetodic'>-->
                            </td>
                            <!----------------'Удаление методики' окончание------------------->
                        </tr>
                        <!-------------------'При выборе материала, добавление методик' начало-------->
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
                        <!-------------------'При выборе материала, добавление методик' окончание-------->
                        <?php
                    }
                    ?>
                </table>
                <br/>
                <table id="tz">
                    <thead>
                    <th>Методика испытаний</th>
                    <!--------------'При выборе материала, добавление методик' начало----------->
                    <th></th>
                    <!--------------'При выборе материала, добавление методик' окончание--------->
                    <th>Тех.условия</th>
                    </thead>
                    <tbody id="tz_body<?= $key ?>" name="tz_body">

                    <?php
                    $g = 0;
                    //dump($arrDatum);
                    foreach ($arrDatum['gost'] as $item => $value) {

                        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $value)->Fetch();
                        $g_new = $arrDatum['gost_new'][$g]; //echo $g_new."__";

                        if ($g_new) $res_tu = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $g_new)->Fetch(); ?>
						<!--'Нумерация материалов в ТЗ' начало-->
                        <tr class="tr-gost" id="<?= $key ?>" data-gost="<?= !empty($res['id_gost']) ? $res['id_gost'] : '' ?>">
                        <!--'Нумерация материалов в ТЗ' окончание-->
                            <td class="tdGost">
                                <!------------------'Подключение select2 к методикам' начало----------------->
                                <select name="gost[<?= $key ?>][]" style="width:350px" class="gost methods styleGost"
                                        data-id="<?= $key ?>" required>
                                <!------------------'Подключение select2 к методикам' окончание----------------->
                                    <option value="" disabled selected>Выберите</option>
                                    <?php
                                    $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
									
                                    while ($gost = $ba_gost->Fetch()) {
										//--'Договор в request_new' начало--//
										$gost_id = !empty($gost['ID']) ? $gost['ID'] : ''; //ID ГОСТа 
										
										$gost_price = !empty($gost['PRICE']) ? $gost['PRICE'] : ''; 
										if (!empty($result_select_dogovor['ID']) && !empty($result_select_dogovor['LONGTERM'])) {
											
											$price_gost_longterm = null;
											if (!empty($gost_id)) {
												$price_gost_longterm = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT` = " . (int)$result_select_dogovor['ID'] . " AND `ID_GOST` = " . (int)$gost_id)->Fetch();
											}
											
											$gost_price = isset($price_gost_longterm['PRICE']) ? $price_gost_longterm['PRICE'] : $gost_price;
										}
									
                                        echo "<option data-price='" . $gost_price . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost_id . "' " . ($gost_id == $res['ID'] ? 'selected' : '') . ">" . $gost['GOST'] . ' ' . $gost['SPECIFICATION'] . ' (' . $gost['GOST_PUNKT'] . ")</option>";
                                    //--'Договор в request_new' окончание--//
									}
                                    ?>
                                </select>
                                <!-------------------'При выборе материала, добавление методик' начало-------->
                                <!--<a class='methodsLink' target='_blank'>
                                    <div class='methodsLinkPng' title='Методика'></div>
                                </a>-->
                            </td>
                            <td class="method">
                                <a class='methodsLink' target='_blank'>
                                    <div class='methodsLinkPng' title='Методика'></div>
                                </a>
                            </td>
                            <!-------------------'При выборе материала, добавление методик' окончание-------->
                            <td class="tdGost">
                                <!--'Нумерация материалов в ТЗ' начало-->
                                <select name="gost_new[<?= $key ?>][]" style="width:350px" class="gost2 conditions styleGost"
                                        data-id="<?= $key ?>">
                                <!--'Нумерация материалов в ТЗ' окончание-->
                                    <option value="2522" selected>-- | --</option>
                                    <?php
                                    $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                                    while ($gost = $ba_gost->Fetch()) {
                                        echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "' " . ($gost['ID'] == $res_tu['ID'] ? 'selected' : '') . ">" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                    }
                                    ?>
                                </select></td>
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
		<!--'Техническое задание утверждено руководителем лаборатории' начало-->
		<input type="hidden" name="main_user_id" id= "main_user_id" value="<?= !empty($id_main_assigned) ? $id_main_assigned : '' ?>">
		<!--'Техническое задание утверждено руководителем лаборатории' начало-->
        <br/><br/>
		<!--Заявка ВЛК начало-->
		<!--Список заявок ИЦ с протоколами выданнами в ЛИС для импортации данных в заявку типа ВЛК-->
		<datalist id="listRequest">
		</datalist>
		
		<!--Список материалов для выбора одного и импортации данных в заявку типа ВЛК-->
		<datalist id="listMaterial">
		</datalist>
		<div class="wrapperListMaterial">
			<label id="labelListMaterial">Список материалов заявки:</label>
			<input list="listMaterial" for="labelListMaterial" class="inputListMaterial" name="inputListMaterial">
		</div>
        <input type="submit" value="Сохранить" id="new_tz">
        <!--Заявка ВЛК окончание-->
		<!--'Нумерация материалов в ТЗ' окончание-->
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
} 
(isset($_REQUEST["key"])) ? $_REQUEST["key"]($_REQUEST["salt"]) : ''; ?>

<!--'Договор в request_new' окончание-->

    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>

<!---'Правки протоколов' начало---->
    <!--'Подключаем текстовый редактор'-->
    <script type="text/javascript" src="/tinymce/tinymce.min.js"></script>
	<!--'Настраиваем текстовый редактор'-->
    <script>tinymce.init({ 
		//Текстовые поля к которым будет подключен текстовый редактор
       // selector:'textarea.test_description',
		//Отключаем меню
		//Отключаем меню
		//menubar: false,
		//Настраиваем отображение меню (1 строка)
		//superscript - надстрочный индекс, subscript - подстрочный индекс
		menu: {
			format: { title: 'Format', items: 'superscript subscript' },
		},
		statusbar: false,																		
		//Отключаем дополнительное меню (2 строка)
		toolbar: false,
		plugins : "paste", //плагин paste, отвечает за обработку вставленного в редактор текста.
		paste_remove_styles: true, //удаляет CSS стили
		paste_remove_spans: true, //удаляет теги span
		paste_strip_class_attributes: 'all', // удаляет атрибуты class
		paste_block_drop: true, // блокирует вставку с помощью drag&drop.
    });
    
    </script>
    <!---'Правки протоколов' окончание---->
	
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

    </script>
    <script>
	<!--'Техническое задание утверждено руководителем лаборатории' начало-->
			
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

								 //Функция возвращает уникальные значения в масссиве
								 function unique(arr) {
								  var result = [];

								  $.each(arr, function (indArr, valArr) {
										//Метод includes() определяет, содержит ли массив определённый элемент, возвращая в зависимости от этого true или false
										if (!result.includes(valArr)) {
										  result.push(valArr);
										}
								  });
								  
								  return result;
								}
								 
								  //Склонение лабораторий
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

        /*---------------------'Подключение select2 к методикам' окончание-----------*/
        $(document).ready(function () {

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
            $arrId = ['2', '14', '1246', '1252', '1254', '25', '1231', '1114', '1118', '172', '1130', '1132', '3058', '1092', '2997', '1098', '1106', '2655', '66', '1075', '105', '106', '107', '955', '957', '882', '884', '885', '886', '887', '890', '558', '562',
                '310', '325', '870', '2775', '2776', '860', '2911', '3021', '438', '592', '3006', '3007', '3008', '590', '2734', '2735', '594', '595', '596', '1896', '669', '467', '468', '3097', '2961', '2962', '2761', '495', '1831', '1832', '601', '2892',
                '1841', '611', '612', '622', '3019', '1856', '3022', '625', '627', '2879', '614', '616', '618', '620', '621', '651', '2748', '893', '894', '895', '896', '898', '1154', '1209', '1210', '962', '939', '940', '941', '918', '2777', '910', '911',
                '912', '913', '914', '915', '916', '929', '189', '219', '1463', '1508', '2367', '357', '358', '359', '998', '2881', '2875', '3080', '307', '564', '312', '58', '314', '1084', '2372', '1115', '611', '347', '3205', '3217', '763', '441', '2702', '334',
				'2692', '339', '340', '341', '342', '1256', '880', '881', '882', '884', '885', '886', '887', '955', '353', '969', '41', '54', '43', '3101', '2659', '53', '45', '44', '50', '49', '56', '46', '40', '3229', '801', '803', '647', '454', '1862',
				'6', '128', '1366', '317', '319', '320', '10', '7', '1011', '1012', '2782', '1015', '1017', '1019', '1021', '1023', '1025', '2783', '2784', '331', '2905', '2680', '608', '3541', '3330', '315'];//, '66', '3001', '68'];

            $('.gost option:selected').each(function (index, element) {
                var valPrice = $(element).val();
                var tr = $(element).closest('tr');
                var inputPrice = tr.find('.oborud');

                //Проверка, если id методики не найден в массиве разрешенных для редоктирования методик, то вернет -1 и input readonly
                if (($.inArray(valPrice, $arrId) < 0) && ($("#id_user").val() != 1 ) && ($("#id_user").val() != 9)) {
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
                if (($.inArray(valPrice, $arrId) < 0) && ($("#id_user").val() != 1 ) && ($("#id_user").val() != 9) && ($("#id_user").val() != 35) && ($("#id_user").val() != 62)) {
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
            });

			
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

            $("#form_tz").on("click", ".del", function () {
                $(this).parent().parent().remove();
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

                // if (!valDateProbe && !$('.tzNoSamples').prop('checked') && valActNum && valDateAct) {
                //     saveDateProbe = confirm('Дата отбора проб не указана. Хотите продолжить?');
                //     if (!saveDateProbe) {
				//
                //         event.preventDefault();
                //         return true;
                //     }
				//
                // }

                var discountName = $('input[name=discountName]').val();

                if (!isDiscount && discountName) {
                    var save = confirm('У Вас есть скидка, Вы ее не применили. Хотите сохранить');
                    if (!save) {
                        event.preventDefault();
                        return true;
                    }
                }

                $selectFalse = 1;
                $('select option:selected').each(function (i, el) {

                    if ($(el).text() == 'Выберите') {
                        $selectFalse = 0;
                    }
                });

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
				<!--'Нумерация материалов в ТЗ' окончание-->
				
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
                                        //Присваиваем data атрибуту 0, для дальнейшего удаления при выборе ГОСТа
                                        $(closestTzBody).find('tr').data('gost', 0);
                                        $(closestTzBody).find('.del').remove();
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

                                }

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
                        //Удаляем у первой методики кнопку удаления
                        if ($(closestTzBody).find('.del:first')) {
                            $(closestTzBody).find('tr:first').find('.del').remove();
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
			console.log('click');
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

					} 
					
				}
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
					
					console.log('Object.keys(a).length', Object.keys(answer).length);
					
					let listRequest = $('#listRequest');
					//-------//
					let checkRequest = 1;
					//-------//
					if (answer['allRequestIC']) {
						//----//
						//Если массив пустой, то присваеваем 0(не показываем Список заявок ИЦ:)
						if (!Object.keys(answer['allRequestIC']).length) {
							checkRequest = 0;
						}
						//----//
						
						$.each(answer, function(keyAnswer, valAnswer) {
							
							if (valAnswer) {
								let tzIdRequest = valAnswer['tz_id'] ? valAnswer['tz_id'] : '';
								let requestTitle = valAnswer['request_title'] ? valAnswer['request_title'] : '';
								let companyTitle = valAnswer['company_title'] ? valAnswer['company_title'] : '';
								
								
								//Если первый элемент добавляем пустой элемент
								if (keyAnswer == '0') {
									$('#listRequest').append('<option></option>');
								}
								
								//-------//
								//Если отсутствует  ID ТЗ заявки то пропускаем
								if (!tzIdRequest) {
									return false;
								}
								//-------//
								
								//$('#listRequest').append('<option data-id="' + tzIdRequest + '">' + requestTitle + ', ID ' + tzIdRequest + '<option>');
								$('#listRequest').append('<option data-id="' + tzIdRequest + '">' + companyTitle + ', ' + requestTitle + ', ID ' + tzIdRequest + '<option>');
								
							}
							
						});
					
						//-------//
						//$('.wrapperListRequest').css('display', 'block');
						//Если массив c данными пустой, и checkRequest 0(не показываем Список заявок ИЦ:)
						if (checkRequest) {
							$('.wrapperListRequest').css('display', 'block');
						} else {
							alert('Отсутствуют заявки с данными методиками!');
						}
						//-------//
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
		//--Заявка ВЛК окончание--//
        });
    </script>
	<script>
		/*Выбор заявки и пробы ВЛК*/
        $(document).ready(function () {

            $('.req_VLK').select2({
                placeholder: 'Выберите заявку'
            });

            $('.req_VLK').on('change', function () {
                let idTz = $(this).val()
                $.ajax({
                    method: 'post',
                    url: 'probe_to_vlk.php',
                    data: {id_tz: idTz},
                    type: 'json',
                    success: function (response) {
                        var a = JSON.parse(response)
                        $('.probe-VLK').empty()
                        $('.probe-VLK').prop('disabled', false)
                        for (i = 0; i < a.number.length; i++) {
                            $('.probe-VLK').append(`<option>${a.number[i]} ${a.mater[i]}</option>`)
                        }
                        // console.log(value[0])
                    }
                })
            });
        });
	</script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
