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
    <script>
        /*$(document).ready(function () {
 
            $('.gost2').select2();
        });*/
		/*$(document).ready(function() {
			
			$('.btn-transfer').on('click', function (event) {
				event.preventDefault();
				console.log('click');
				var idTzCurrentDeal = $('.id-tz') ? $('.id-tz').val() : '';
				
				var arrIdMetodicSelected = [];
				
				$('.methods option:selected').each(function (index, option) {
					arrIdMetodicSelected.push($(option).val());
				});
				var arrIdLaba = [];
				var arrIdAssigned = [];
				
				$('.laboratory-manager').each(function(inexAssigned, valueAssigned) {
					
					var idLaba = $(valueAssigned).data('id-laba');
					var idAssigned = $(valueAssigned).data('id-assigned');
				
					if ($.inArray(idLaba, arrIdLaba) == '-1') {
						arrIdLaba.push(idLaba); 
					}
					
					if ($.inArray(idAssigned, arrIdAssigned)  == '-1') {
						arrIdAssigned.push(idAssigned); 
					}
					
				}); 
				
				if (arrIdLaba && arrIdLaba) {
					$.ajax({
						method: 'POST',
						url: 'check_methods_and_responsib.php',
						type: 'json',
						data: {arr_id_metodic: arrIdMetodicSelected, arr_id_laba: arrIdLaba, arr_id_assigned: arrIdAssigned, id_tz: idTzCurrentDeal, btn_transfer: 'transfer'}, //btn_transfer: 'transfer'- уазываем что запрос относиться к (передачи на рассмотрение руководителю лаборатории)
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
					});
				}
			});
		});*/
    </script>
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
	.shema_v, .shema_v2, .shema_v3, .shema_v4{
	display:none;
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
	
	$id_grad_dep = null;
	$id_c_coeff = null;
	if ($_POST['grad_dep']) {
		$arr_grad_dep = explode(' ,', $_POST['grad_dep']);
		$id_grad_dep = !empty($arr_grad_dep[0]) ?  substr($arr_grad_dep[0], 1) : '';
	}
	if ($_POST['c_coeff']) {
		$arr_c_coeff = explode(' ,', $_POST['c_coeff']);
		$id_c_coeff = !empty($arr_c_coeff[0]) ?  substr($arr_c_coeff[0], 1) : '';
	}

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
	//--' Вывод дедлайн в журнале заявок' начало--//
	if (!empty($_GET['ID'])) {
        $res_tz_get_id = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID'])->Fetch();
    }
	//--' Вывод дедлайн в журнале заявок' окончание--//
	
    $deal1 = CCrmDeal::GetByID($_GET['ID']);
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

    $dealUp = CCrmDeal::GetByID($res_tz['ID_Z']);

    $updDeal = CCrmDeal::GetByID($_GET['ID']);

    $id_deal = (isset($_GET['EDIT'])) ? $dealUp['ID'] : $updDeal['ID'];
	//--'Техническое задание утверждено руководителем лаборатории' начало--//
	
	$current_deal = null;
	if (!empty($dealUp)) {
		$current_deal = $dealUp;
	} elseif(!empty($updDeal)) {
		$current_deal = $updDeal;
	}
	//--'Техническое задание утверждено руководителем лаборатории' окончание--//
	

	$extranet_id = !empty($current_deal['COMPANY_ID']) && $current_deal['COMPANY_ID'] == 1184 ? 56 : '';
	
    $res = $DB->Query("delete from `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = " . (int)$id_deal);

    foreach ($ar_material_id as $key => $ar_m) {
        $DB->Query("INSERT INTO `MATERIALS_TO_REQUESTS` (`ID_DEAL`, `ID_MATERIAL`, `NAME_MATERIAL`) VALUES (" . (int)$id_deal . ", " . (int)$ar_m . ",'" . $ar_material[$key] . "')");
    }

//----------------------'Серверная обработка Журнала заявок' начало---------------------//
    //Колонка "Обьект испытания"
		$mater_arr_up=array();
		$res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=" . (int)$id_deal);
		while($mater=$res_mat->Fetch())
		{
			$mater_arr_up[]=$mater['NAME_MATERIAL'];
		}
		$dealMaterials = ($deal_new['UF_CRM_1576411922643'] ? $deal_new['UF_CRM_1576411922643'] : implode(', ', $deal_new['UF_CRM_1569151529509']));
		$dealMaterials = ($dealMaterials  ? $dealMaterials : implode(', ', $mater_arr_up));
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
   
    $num_act = (!empty($_POST['ACT_NUM']) ? $_POST['ACT_NUM'].$y_date_act : '');
																

    if (!empty($_POST['id_tz'])) {
        $deadlineProtocol = $_POST['DEADLINE_PROTOCOL'];
		//--'Договор в request_new' начало--//
        //$res = $DB->Query("UPDATE `ba_tz` SET `TZ`='" . serialize($arr) . "',`OBJECT`='" . $_POST['OBJECT'] . "',`DESCRIPTION`='" . $_POST['DESCRIPTION'] . "',`ID_Z`='" . (int)$_POST['id_deal'] . "',`DOGOVOR_NUM`='" . $_POST['DOGOVOR_NUM'] . "',`ACT_NUM`='" . (int)$_POST['ACT_NUM'] . "',`PRICE`=" . $price . ",`DATE_ACT`='" . $_POST['DATE_ACT'] . "',`EXTRANET_ID`='" . $extranet_id . "',`CONTROL_LIST`='" . ($_POST['ACT_LIST'] == '3' ? '1' : '0') . "', `PROBE` = '" . serialize($probe) . "',`DEADLINE` = '" . $deadlineProtocol . "', `DEADLINE_TABLE` = '" . $deadline_table_up . "', `MATERIAL` = '" . $dealMaterials . "', `DOGOVOR_TABLE` = '" . $dogovor_table . "', `NUM_ACT_TABLE` = '" . $num_act . "', `ATTESTAT` = " . $in_oa . " WHERE ID = " . (int)$_POST['id_tz']);
        $res = $DB->Query("UPDATE `ba_tz` SET `TZ`='" . serialize($arr) . "',`OBJECT`='" . $_POST['OBJECT'] . "',`DESCRIPTION`='" . $_POST['DESCRIPTION'] . "',`ID_Z`='" . (int)$_POST['id_deal'] . "', `ACT_NUM`='" . (int)$_POST['ACT_NUM'] . "',`PRICE`=" . $price . ",`DATE_ACT`='" . $_POST['DATE_ACT'] . "',`EXTRANET_ID`='" . $extranet_id . "',`CONTROL_LIST`='" . ($_POST['ACT_LIST'] == '3' ? '1' : '0') . "', `PROBE` = '" . serialize($probe) . "',`DEADLINE` = '" . $deadlineProtocol . "', `DEADLINE_TABLE` = '" . $deadline_table_up . "', `MATERIAL` = '" . $dealMaterials . "', `NUM_ACT_TABLE` = '" . $num_act . "', `ATTESTAT` = " . $in_oa . ", `GRAD_ID`=" . ($id_grad_dep ? $id_grad_dep : 0)  . ", `COIN_COEFF`=".($id_c_coeff ? $id_c_coeff : 0) .", `COIN_COEFF_VAL`=".($_POST['coin_coeff'] ? $_POST['coin_coeff'] : 0)." WHERE ID = " . (int)$_POST['id_tz']);
        //--'Договор в request_new' окончание--//
		// $res_obj = $DB->Query("UPDATE `ba_tz` SET `TZ`='" . serialize($arr) . "',`OBJECT`='" . $_POST['OBJECT'] . "',`DESCRIPTION`='" . $_POST['DESCRIPTION'] . "',`ID_Z`='" . (int)$_POST['id_deal'] . "',`TYPE_TZ`='" . (int)$_POST['type_tz'] . "',`DOGOVOR_NUM`='" . $_POST['DOGOVOR_NUM'] . "',`ACT_NUM`='" . (int)$_POST['ACT_NUM'] . "',`PRICE`='" . $price . "',`DATE_ACT`='" . $_POST['DATE_ACT'] . "',`EXTRANET_ID`='" . $extranet_id . "' WHERE ID = " . (int)$_POST['id_tz']);
        
		if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    } else {
        $deadlineProtocol = $_POST['DEADLINE_PROTOCOL'];
		$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $_GET['ID'])->Fetch();
        //$res = $DB->Query("INSERT INTO `ba_tz`(`TZ`,`OBJECT`, `DATE_SOZD`, `ID_Z`, `TYPE_TZ`, `DOGOVOR_NUM`, `PROBE`, `ACT_NUM`, `PRICE`, `DESCRIPTION`, `DATE_ACT`, `EXTRANET_ID`, `CONTROL_LIST`, `ATTESTAT`, `DEADLINE`) VALUES ('" . serialize($arr) . "','" . $_POST['OBJECT'] . "',NOW()," . (int)$_GET['ID'] . "," . (int)$_POST['type_tz'] . ",'" . $_POST['DOGOVOR_NUM'] . "', '" . serialize($probe) . "'," . (int)$_POST['ACT_NUM'] . "," . $price . ",'" . $_POST['DESCRIPTION'] . "','" . $_POST['DATE_ACT'] . "','" . $extranet_id . "', '" . ($_POST['ACT_LIST'] == '3' ? '1' : '0') . "', " . $in_oa . ", '" . $deadlineProtocol . "')");
		//--'Договор в request_new' начало--//
		if (isset($res_tz['TZ'])) { //Для новых заявок если у них есть ТЗ
			//$res = $DB->Query("UPDATE `ba_tz` SET `TZ`='" . serialize($arr) . "',`OBJECT`='" . $_POST['OBJECT'] . "', `DATE_SOZD` = NOW(), `DESCRIPTION`='" . $_POST['DESCRIPTION'] . "',`ID_Z`='" . (int)$_GET['ID'] . "',`DOGOVOR_NUM`='" . $_POST['DOGOVOR_NUM'] . "',`ACT_NUM`='" . (int)$_POST['ACT_NUM'] . "',`PRICE`=" . $price . ",`DATE_ACT`='" . $_POST['DATE_ACT'] . "',`EXTRANET_ID`='" . $extranet_id . "',`CONTROL_LIST`='" . ($_POST['ACT_LIST'] == '3' ? '1' : '0') . "', `PROBE` = '" . serialize($probe) . "',`DEADLINE` = '" . $deadlineProtocol . "', `DEADLINE_TABLE` = '" . $deadline_table_up . "', `MATERIAL` = '" . $dealMaterials . "', `DOGOVOR_TABLE` = '" . $dogovor_table . "', `NUM_ACT_TABLE` = '" . $num_act . "', `ATTESTAT` = " . $in_oa . " WHERE ID_Z = " . (int)$_GET['ID']);
			$res = $DB->Query("UPDATE `ba_tz` SET `TZ`='" . serialize($arr) . "',`OBJECT`='" . $_POST['OBJECT'] . "', `DATE_SOZD` = NOW(), `DESCRIPTION`='" . $_POST['DESCRIPTION'] . "',`ID_Z`='" . (int)$_GET['ID'] . "', `ACT_NUM`='" . (int)$_POST['ACT_NUM'] . "',`PRICE`=" . $price . ",`DATE_ACT`='" . $_POST['DATE_ACT'] . "',`EXTRANET_ID`='" . $extranet_id . "',`CONTROL_LIST`='" . ($_POST['ACT_LIST'] == '3' ? '1' : '0') . "', `PROBE` = '" . serialize($probe) . "',`DEADLINE` = '" . $deadlineProtocol . "', `DEADLINE_TABLE` = '" . $deadline_table_up . "', `MATERIAL` = '" . $dealMaterials . "', `NUM_ACT_TABLE` = '" . $num_act . "', `ATTESTAT` = " . $in_oa . " WHERE ID_Z = " . (int)$_GET['ID']);
		} else { //Для старых заявок когда еще не создавалось ТЗ сразу 
			//$res = $DB->Query("INSERT INTO `ba_tz`(`TZ`,`OBJECT`, `DATE_SOZD`, `ID_Z`, `DOGOVOR_NUM`, `PROBE`, `ACT_NUM`, `PRICE`, `DESCRIPTION`, `DATE_ACT`, `EXTRANET_ID`, `CONTROL_LIST`, `ATTESTAT`, `DEADLINE`, `DEADLINE_TABLE`, `MATERIAL`, `DOGOVOR_TABLE`, `NUM_ACT_TABLE`) VALUES ('" . serialize($arr) . "','" . $_POST['OBJECT'] . "',NOW()," . (int)$_GET['ID'] . ",'" . $_POST['DOGOVOR_NUM'] . "', '" . serialize($probe) . "'," . (int)$_POST['ACT_NUM'] . "," . $price . ",'" . $_POST['DESCRIPTION'] . "','" . $_POST['DATE_ACT'] . "','" . $extranet_id . "', '" . ($_POST['ACT_LIST'] == '3' ? '1' : '0') . "', " . $in_oa . ", '" . $deadlineProtocol . "', '" . $deadline_table_up . "', '" . $dealMaterials . "', '" . $dogovor_table . "', '" . $num_act . "')");
			$res = $DB->Query("INSERT INTO `ba_tz`(`TZ`,`OBJECT`, `DATE_SOZD`, `ID_Z`, `PROBE`, `ACT_NUM`, `PRICE`, `DESCRIPTION`, `DATE_ACT`, `EXTRANET_ID`, `CONTROL_LIST`, `ATTESTAT`, `DEADLINE`, `DEADLINE_TABLE`, `MATERIAL`, `NUM_ACT_TABLE`) VALUES ('" . serialize($arr) . "','" . $_POST['OBJECT'] . "',NOW()," . (int)$_GET['ID'] . ", '" . serialize($probe) . "'," . (int)$_POST['ACT_NUM'] . "," . $price . ",'" . $_POST['DESCRIPTION'] . "','" . $_POST['DATE_ACT'] . "','" . $extranet_id . "', '" . ($_POST['ACT_LIST'] == '3' ? '1' : '0') . "', " . $in_oa . ", '" . $deadlineProtocol . "', '" . $deadline_table_up . "', '" . $dealMaterials . "', '" . $num_act . "')");
		}
		//--'Договор в request_new' окончание--//
		//$res = $DB->Query("UPDATE `ba_tz` SET `TZ`='" . serialize($arr) . "',`OBJECT`='" . $_POST['OBJECT'] . "', `DATE_SOZD` = NOW(), `TYPE_TZ`=" . (int)$_POST['type_tz'] . ", `DESCRIPTION`='" . $_POST['DESCRIPTION'] . "',`ID_Z`='" . (int)$_GET['ID'] . "',`DOGOVOR_NUM`='" . $_POST['DOGOVOR_NUM'] . "',`ACT_NUM`='" . (int)$_POST['ACT_NUM'] . "',`PRICE`=" . $price . ",`DATE_ACT`='" . $_POST['DATE_ACT'] . "',`EXTRANET_ID`='" . $extranet_id . "',`CONTROL_LIST`='" . ($_POST['ACT_LIST'] == '3' ? '1' : '0') . "', `PROBE` = '" . serialize($probe) . "',`DEADLINE` = '" . $deadlineProtocol . "', `DEADLINE_TABLE` = '" . $deadline_table_up . "', `MATERIAL` = '" . $dealMaterials . "', `DOGOVOR_TABLE` = '" . $dogovor_table . "', `NUM_ACT_TABLE` = '" . $num_act . "', `ATTESTAT` = " . $in_oa . " WHERE ID_Z = " . (int)$_GET['ID']);
        
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

    //Дата сохранения технического задания в историю
    $date_save = date('Y-m-d H:i:s');

    $assigned = CUser::GetByID($USER->GetID())->arResult[0];
    //Имя пользователя
    $assigned_name = !empty($assigned['NAME']) ? $assigned['NAME'] : '';
    //Фамилия пользователя
    $assigned_lastname = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';
    //Пользователь
    $assigned_user = $assigned_name.' '.$assigned_lastname;

    if (!empty($_GET['ID'])) {
        $res_insert = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
        $ba_tz_insert = $res_insert->Fetch();
		 //---------'Изменение номера ТЗ' начало ------//
        $tz = $ba_tz_insert['ID'];
        //---------'Изменение номера ТЗ' окончание ------//																	   
        //Номер протокола
        $ID_P = $ba_tz_insert['NUM_P'] ? $ba_tz_insert['NUM_P'] : 'номер не присвоен';
        //Номер заявки
        $num_request = !empty($deal1["TITLE"]) ? $deal1["TITLE"] : '';


        //---------'Изменение номера ТЗ' начало ------//
        $res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date_save . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$tz . "','" . $USER->GetID() . "', 'Сохранение технического задания', '" . $num_request . "')");

        //----------------------'Серверная обработка Журнала заявок' начало---------------------//
        if ($ba_tz_insert['ID'])
        {
            $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$tz. " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
            $history_ass=$history['ASSIGNED'];
            $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
            $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
            if($history_date<$history_date_null) $history_date='';
        }
        //Колонка "последнее изменение"
        $assignet_history = $history_ass . " " . $history_date;

        $update_assignet_ba_tz = $DB->Query("UPDATE ba_tz SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . (int)$_GET['ID']);

    //----------------------'Серверная обработка Журнала заявок' окончание---------------------//
        //---------'Изменение номера ТЗ' окончание ------//																			 
    }

    if (!empty($_GET['EDIT'])) {
        $res_update = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['EDIT']);
        $ba_tz_update = $res_update->Fetch();
		//---------'Изменение номера ТЗ' начало ------//
        $tz = $ba_tz_update['ID'];
        //---------'Изменение номера ТЗ' окончание ------//														 
        //Номер протокола
        $ID_P = $ba_tz_update['NUM_P'] ? $ba_tz_update['NUM_P'] : 'номер не присвоен';
        //Номер заявки
        $num_request = !empty($dealUp["TITLE"]) ? $dealUp["TITLE"] : '';
        //---------'Изменение номера ТЗ' начало ------//
        $res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date_save . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$tz . "','" . $USER->GetID() . "', 'Обновление технического задания', '" . $num_request . "')");
        //---------'Изменение номера ТЗ' окончание ------//

        //----------------------'Серверная обработка Журнала заявок' начало---------------------//
        if ($ba_tz_update['ID_Z'])
        {
            $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$tz. " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
            $history_ass=$history['ASSIGNED'];
            $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
            $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
            if($history_date<$history_date_null) $history_date='';
        }
        //Колонка "последнее изменение"
        $assignet_history = $history_ass . " " . $history_date;
        //echo'<pre>';var_dump($ba_tz_update['ID_Z'], $history_ass, $history_date);die;

        $update_assignet_ba_tz = $DB->Query("UPDATE ba_tz SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . (int)$ba_tz_update['ID_Z']);

    //----------------------'Серверная обработка Журнала заявок' окончание---------------------//
    }
	
	//--'Техническое задание утверждено руководителем лаборатории' начало--//
	
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
    <a href="/request_card.php?ID=<?= $_POST['id_deal'] ?>">Вернуться в карточку заявки</a>
    |
    <a href="/tz.php?EDIT=<?= $ID_TZ ?>">Вернуться к редактированию</a>
    |
    <a href="/<?= ($_POST['ACT_LIST'] == '3' ? 'control_list' : 'probe') ?>.php?ID=<?= $_POST['id_deal'] ?>"><?= ($_POST['ACT_LIST'] == '3' ? 'Создать контрольный лист' : 'Создать акт приемки проб') ?></a>
    <?php
    if (!empty($_POST['ACT_NUM'])) {
        echo "| <a href='/results_isp_nk.php?ID=" . $_POST['id_deal'] . "'>Перейти к испытаниям</a>";
    }
	
	//--'Техническое задание утверждено руководителем лаборатории' начало--//
	/*$result_select_check_tz = null;
	if ($result_select_ba_tz['ID']) {
		$result_select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $result_select_ba_tz['ID']);  
	}
	
	$sum_select_check_tz = !empty($result_select_check_tz->SelectedRowsCount()) ? $result_select_check_tz->SelectedRowsCount() : '';
	
	
	$arr_id_leader = [];
	
	if (!empty($sum_select_check_tz)) {
		while($result_check_tz = $result_select_check_tz->Fetch()) {
			if (!empty($result_check_tz['leader'])) {
				$arr_id_leader[] = $result_check_tz['leader'];
			}
		}
	}*/
	
	?>
	<!--<table id="table-transfer">
		<tr>
		<td class="td-transfer td-description">Передать на рассмотрение руководителю лаборатории</td> 
		<td class="td-transfer td-result">
			<div class="transfer-laboratory-manager">
				<div class="wreppre-laboratory-manager">
					<?php
						//Массив с id лабораторий и id руководителей
						/*$arr_all_main_assigned = [
							'54' => '11', //ФХИ Руководитель - Татьяна Овчинникова id=11, id лаборатории=54
							'55' => '13', //ДСЛ Руководитель - Татьяна Велютич id=13, id лаборатории=55
							'56' => '15', //ФМИ Руководитель - Виталий Шингарев id=15, id лаборатории=56
							'57' => '58', //СМ Руководитель - Регина Живцова id=58, id лаборатории=57
							'58' => '43', //ОСК Руководитель - Елена Лавреньтьева id=43, id лаборатории=58
						];
					
						//Находим id главного ответственного
						$id_main_assigned = !empty($current_deal['ASSIGNED_BY_ID']) ? $current_deal['ASSIGNED_BY_ID'] : '';
						
						 //Лабораториии, Находим к какой лаборатории относиться главный ответсвенный
						$main_user = !empty($id_main_assigned) ? CUser::GetByID($id_main_assigned)->Fetch() : '';
						
						//ID лаборатории к которой относиться главный ответственный
						$id_laba_main_assigned = !empty($main_user['UF_DEPARTMENT'][0]) ? $main_user['UF_DEPARTMENT'][0] : '';
						
						//Имя главного ответственного
						$assigned_main_name = !empty($current_deal['ASSIGNED_BY_NAME']) ? $current_deal['ASSIGNED_BY_NAME'] : '';
						
						//Фамилия главного ответственного
						$assigned_main_lastname = !empty($current_deal['ASSIGNED_BY_LAST_NAME']) ? $current_deal['ASSIGNED_BY_LAST_NAME'] : '';
						
						//getInitials($assigned_by_name) -получаем 1-ю букву имени и ставим точку, и далее конкатенируем фамилию
						$name_lastname_main_assigned = getInitials($assigned_main_name) ." ". $assigned_main_lastname;
						
						//$arr_all_main_assigned[$id_laba_main_assigned] - находим кто является ответсвенным в лаборатории(id руководителя лаборатории) и сравниваем с id ответвенного которого выбрали($id_main_assigned)
						if (!empty($name_lastname_main_assigned) && !empty($id_main_assigned) && !empty($id_laba_main_assigned) && !empty($arr_all_main_assigned[$id_laba_main_assigned]) && $arr_all_main_assigned[$id_laba_main_assigned] == $id_main_assigned) {
							
							//Получаем данные о проверки для главного ответственного
							$main_leader_check_tz = null;
							if (!empty($current_ba_tz['ID'])) {
								$main_leader_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $current_ba_tz['ID'] . " AND `leader`=" . $id_main_assigned)->Fetch();
							}
							
							//Выводи данные в ТЗ
							echo "<p class='laboratory-manager main-assigned' data-id-assigned='" . $id_main_assigned . "' data-id-laba='" . $id_laba_main_assigned . "'><input type='checkbox' name='verified' " . (!empty($main_leader_check_tz['confirm']) ? 'checked' : '') . " disabled>  " . $name_lastname_main_assigned . "</p>";
						}
						
						//echo'<pre>';var_dump($current_deal['ID']);
						if (!empty($current_deal['ID'])) {
							//Получаем пользовательские поля по ID сделки(заявки)
							$user_field_deal = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $current_deal['ID']);
							
							//Находим масив с id дополнительных ответственных
							$arr_id_additional_assigned = !empty($user_field_deal['UF_CRM_1571643970']['VALUE']) ? $user_field_deal['UF_CRM_1571643970']['VALUE'] : '';
							
							//Находим Доп. ответственого('UF_CRM_1571643970'), если он существует
							if ($arr_id_additional_assigned) {
								
								foreach($arr_id_additional_assigned as $id_additional_assigned)
								{
									//Находим данные дополнительного ответственного по id
									$assign_user = CUser::GetByID((int)$id_additional_assigned)->Fetch();
									
									$assign_name = !empty($assign_user['NAME']) ? $assign_user['NAME'] : '';
									$assign_lastname = !empty($assign_user['LAST_NAME']) ? $assign_user['LAST_NAME'] : '';
									$name_lastname_additional_assigned = getInitials($assign_name)." ".$assign_lastname;
									
									//ID лаборатории к которой относиться дополнительный ответственный
									$id_laba_additional_assigned = !empty($assign_user['UF_DEPARTMENT'][0]) ? $assign_user['UF_DEPARTMENT'][0] : '';
									
									//$arr_all_main_assigned[$id_laba_main_assigned] - находим кто является ответсвенным в лаборатории(id руководителя лаборатории) и сравниваем с id ответвенного которого выбрали($id_additional_assigned)
									if (!empty($name_lastname_additional_assigned) && !empty($id_additional_assigned) && !empty($id_laba_additional_assigned && !empty($arr_all_main_assigned[$id_laba_additional_assigned]) && $arr_all_main_assigned[$id_laba_additional_assigned] == $id_additional_assigned)) {
										
										//Получаем данные о проверки для каждого сотрудника
										$select_leader_check_tz = null;
										if (!empty($current_ba_tz['ID'])) {
											$select_leader_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $current_ba_tz['ID'] . " AND `leader`=" . $id_additional_assigned)->Fetch();
										}
										
										echo "<p class='laboratory-manager additional-assigned' data-id-assigned='" . $id_additional_assigned . "'  data-id-laba='" . $id_laba_additional_assigned . "'><input type='checkbox' name='verified' " . (!empty($select_leader_check_tz['confirm']) ? 'checked' : '') . " disabled>  " . $name_lastname_additional_assigned . "</p>";
									}
								}
							}
						}*/

						//echo'<pre>';var_dump($assigned_by_name,$assigned_by_last_name,$str_assigneds,$main_assigned ,$additional_assigned);
					?>
					
				</div>
				<button type="button" class="btn btn-transfer" <//?= empty($current_ba_tz['TZ']) || !empty($sum_select_check_tz) ? 'disabled' : '' ?>>Передать</button>
				<span class="text-transfer"><//?= !empty($sum_select_check_tz) ? 'Заявка передана на проверку!' : '' ?></span>
			</div> 
		</td>
		</tr>
	</table>
	<input class="tz id-tz" type="hidden" name="id_tz" value="<//?= $current_ba_tz['ID'] ?>">-->
	 
	<?php

	//--'Техническое задание утверждено руководителем лаборатории' окончание--//
	
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
    $company1 = CCrmCompany::GetByID(19);
    $extranet_id = CCrmCompany::GetUserFields(19);//$company1['UF_CRM_1571818346092'];
    $companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch();
    $dataDiscount = $USER_FIELD_MANAGER->GetUserFields('CRM_COMPANY', $companys['ID']);
    $dataDiscountVal = 0;
    if ($row['DISCOUNT']) {
        $dataDiscountVal = $row['DISCOUNT'];
    } else {
        $dataDiscountVal = $dataDiscount['UF_CRM_1582914274353']['VALUE'];
    }

    $id_d = !empty($_GET['EDIT']) ? (int)$row['ID_Z'] : (int)$_GET['ID'];

	$grad_id = !empty($row['GRAD_ID']) ? $row['GRAD_ID'] : '';
	
	$select_grad_dep = null;
	if (!empty($grad_id)) {
		$select_grad_dep = $DB->Query("SELECT * FROM `GRAD_DEP` WHERE `ID`=" . $grad_id)->Fetch();
	}
	
	$grad_date = !empty($select_grad_dep['DATE']) ? $select_grad_dep['DATE'] : '';
	
	$value_grad = !empty($grad_id) ? '№' . $grad_id . ' , от ' . $grad_date : '';
	
	$c_coef_id = !empty($row['COIN_COEFF']) ? $row['COIN_COEFF'] : '';
	
	$select_grad_dep_C = null;
	if (!empty($c_coef_id)) {
		$select_grad_dep_c = $DB->Query("SELECT * FROM `COINCED_COEFF` WHERE `ID`=" . $c_coef_id)->Fetch();
	}
	
	$grad_date_c = !empty($select_grad_dep_c['DATE']) ? $select_grad_dep_c['DATE'] : '';
	
	$value_grad_c = !empty($c_coef_id) ? '№' . $c_coef_id . ' , от ' . ruDate($grad_date_c) : '';


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
	
	//--'Техническое задание утверждено руководителем лаборатории' начало--//
	$result_select_check_tz = null;
	$sum_select_check_tz = null;
	if ($row['ID']) {
		//$result_select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $row['ID']);  
		$result_select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $row['ID'] . " AND `date_return` IS NULL");   
			
	}
	
	if($result_select_check_tz)
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
        <!---------------------------------------- окончание 'Меню' ----------------------------------->
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
                <!--<td><textarea name="DESCRIPTION" style="width:496px" required><//?= $row['DESCRIPTION'] ?></textarea></td>-->
				<!---'Текстовый редактор обьекта испытаний' начало---->
                <td><textarea class="test_description" name="DESCRIPTION" style="width:496px" required><?= $row['DESCRIPTION'] ?></textarea></td>
				<!---'Текстовый редактор обьекта испытаний' окончание---->
            </tr>

            <tr>
                <td width="250" height="40">Основание для проведения испытаний</td>
                <td>
				<!--'Договор в request_new' начало-->
                    <?php
					$resDealsContract = null;
                    if (!empty($row['ID_Z'])) {
                        $resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$row['ID_Z'])->Fetch();
                    }
                    ?>
                    <!--<select class="tz" name="DOGOVOR_NUM">
                        <?php
                        //echo '<option></option>';
					    /*$ba_contr = $DB->Query("SELECT * FROM `DOGOVOR`");// WHERE `CLIENT_ID`=".$dealForTZ['COMPANY_ID']);

						while ($obj = $ba_contr->Fetch()) {
                            $idContract = null;

                            $selectedContract = (!empty($resDealsContract['ID_CONTRACT']) && $resDealsContract['ID_CONTRACT'] == $obj['ID']) ? 'selected' : '';

                            $objId = !empty($obj['ID']) ? $obj['ID'] : '';
                            $numberContract = !empty($obj['NUMBER']) ? $obj['NUMBER'] : $objId;

                            echo "<option data-id='" . $objId . "' " . $selectedContract . ">
								". ($obj['CONTRACT_TYPE'] ? $obj['CONTRACT_TYPE'] : 'Договор') ." №" . $numberContract . " от " . ruDate($obj['DATE']) . ", ID" . $objId . "
							</option>";
                        }*/
                        ?>
                    </select>
                    <input class="tzHiddenСontract" type="text" value="" disabled>
                    <input type="hidden" name='dogovorNumHidden' value="">
                    <input class='tzNoСontract' type='checkbox' name='noСontract'
                           <//?= empty($resDealsContract['ID_CONTRACT']) ? 'checked' : '' ?>>Договор еще не составлен-->
				   <?php
						//Если Договор составлен выводим его , иначен "Договор еще не составлен"
						$result_select_dogovor = null;
						if (!empty($resDealsContract['ID_CONTRACT'])) {
							$result_select_dogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID` = " . (int)$resDealsContract['ID_CONTRACT'])->Fetch();
							
							//id договора
							$dogovor_id = !empty($result_select_dogovor['ID']) ? $result_select_dogovor['ID'] : '';
							
							//Дата создания договора
							$dogovor_date = !empty($result_select_dogovor['DATE']) ? ruDate($result_select_dogovor['DATE']) : '';
							
							//Номер договора
							$number_contract = !empty($result_select_dogovor['NUMBER']) ? $result_select_dogovor['NUMBER'] : $dogovor_id;
							
							echo ($result_select_dogovor['CONTRACT_TYPE'] ? $result_select_dogovor['CONTRACT_TYPE'] : 'Договор') ." №" . $number_contract . " от " . $dogovor_date . ", ID" . $dogovor_id;
						} else {
							echo "Договор еще не составлен";  
						}
				   ?>
				   <!--'Договор в request_new' окончание--> 
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
                    <input class='tzNoSamples' type='checkbox' name='noSamples'
                           <?= (isset($row['ACT_NUM']) && $row['ACT_NUM'] == 0) ? 'checked' : '' ?>>Пробы еще не
                    поступили
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
            <tr>
                <td width="250" height="40">Срок выдачи протокола</td>
                <td><input class="tz" type="date" name="DEADLINE_PROTOCOL" value="<?= $dateDedline ?>" size="20"></td>
            </tr>
			<!--'Договор в request_new' начало-->
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

							$number_deal = !empty($deal_taken['TITLE']) ? $deal_taken['TITLE'] : ''; //Номер заявка(сделки) которая учтена в качестве основной
							$company_deal = !empty($deal_taken['COMPANY_TITLE']) ? $deal_taken['COMPANY_TITLE'] : ''; //Компания(клиент) 
							$begin_date_deal = !empty($deal_taken['BEGINDATE']) ? $deal_taken['BEGINDATE'] : ''; //дата начала
							
							$title_deal_taken = 'Заявка ' . $number_deal . ', ' . $company_deal . ', от ' . $begin_date_deal;
						}
					?>
					<input type="checkbox" name="taken" class="taken" <?= !empty($row['TAKEN_ID_DEAL']) ? 'checked' : '' ?>>  
					<span class="taken-request"><?= $title_deal_taken ?></span>
					<input type="hidden" class="hidden-taken">
				</td>
			</tr>
			<!--'Договор в request_new' окончание-->
			
			<!--'Техническое задание утверждено руководителем лаборатории' начало-->
			<?php
			//echo'<pre>';var_dump($arr_id_leader);
			//$arr_id_leader = [1];
			$id_main_assigned = !empty($dealForTZ['ASSIGNED_BY_ID']) ? $dealForTZ['ASSIGNED_BY_ID'] : '';
			
			$main_leader_check_tz = null;
			if (!empty($row['ID'])) {
				//$main_leader_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $row['ID'] . " AND `leader`=" . $id_main_assigned)->Fetch();
				$main_leader_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $row['ID'] . " AND `leader`=" . $id_main_assigned . " AND `date_return` IS NULL")->Fetch();
			}
							
			//if (!empty($arr_id_leader) && in_array($USER->GetID(), $arr_id_leader)) :
			//if (!empty($arr_id_leader) && ($USER->GetID() == $id_main_assigned)) :
			//if (!empty($arr_id_leader) && in_array($USER->GetID(), $arr_id_leader)) :
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
						
							//$id_main_assigned = !empty($dealForTZ['ASSIGNED_BY_ID']) ? $dealForTZ['ASSIGNED_BY_ID'] : '';
							
							$main_user = !empty($id_main_assigned) ? CUser::GetByID($id_main_assigned)->Fetch() : '';
							
							$id_laba_main_assigned = !empty($main_user['UF_DEPARTMENT'][0]) ? $main_user['UF_DEPARTMENT'][0] : '';
							
							$assigned_main_name = !empty($dealForTZ['ASSIGNED_BY_NAME']) ? $dealForTZ['ASSIGNED_BY_NAME'] : '';
							
							$assigned_main_lastname = !empty($dealForTZ['ASSIGNED_BY_LAST_NAME']) ? $dealForTZ['ASSIGNED_BY_LAST_NAME'] : '';
						
							$name_lastname_main_assigned = getInitials($assigned_main_name) ." ". $assigned_main_lastname;
							
							
							if (!empty($name_lastname_main_assigned) && !empty($id_main_assigned) && !empty($id_laba_main_assigned) && !empty($arr_all_main_assigned[$id_laba_main_assigned]) && $arr_all_main_assigned[$id_laba_main_assigned] == $id_main_assigned) {
								
								/*$main_leader_check_tz = null;
								if (!empty($row['ID'])) {
									$main_leader_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $row['ID'] . " AND `leader`=" . $id_main_assigned)->Fetch();
								}*/
									
								echo "<p class='laboratory-manager main-assigned' data-id-assigned='" . $id_main_assigned . "' data-id-laba='" . $id_laba_main_assigned . "'><input type='checkbox' name='verified' " . (!empty($main_leader_check_tz['confirm']) ? 'checked' : '') . " disabled>  " . $name_lastname_main_assigned . "</p>";
							}
							
							$arr_id_additional_assigned = null;
							
							if (!empty($dealForTZ['ID'])) {
								
								$user_field_deal = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $dealForTZ['ID']);
								
								$arr_id_additional_assigned = !empty($user_field_deal['UF_CRM_1571643970']['VALUE']) ? $user_field_deal['UF_CRM_1571643970']['VALUE'] : '';
								
								/*if ($arr_id_additional_assigned) {
									
									foreach($arr_id_additional_assigned as $id_additional_assigned)
									{
										
										$assign_user = CUser::GetByID((int)$id_additional_assigned)->Fetch();
										
										$assign_name = !empty($assign_user['NAME']) ? $assign_user['NAME'] : '';
										$assign_lastname = !empty($assign_user['LAST_NAME']) ? $assign_user['LAST_NAME'] : '';
										$name_lastname_additional_assigned = getInitials($assign_name)." ".$assign_lastname;
										
										
										$id_laba_additional_assigned = !empty($assign_user['UF_DEPARTMENT'][0]) ? $assign_user['UF_DEPARTMENT'][0] : '';
										
										
										if (!empty($name_lastname_additional_assigned) && !empty($id_additional_assigned) && !empty($id_laba_additional_assigned) && !empty($arr_all_main_assigned[$id_laba_additional_assigned]) && $arr_all_main_assigned[$id_laba_additional_assigned] == $id_additional_assigned) {
										
											$select_leader_check_tz = null;
											if (!empty($row['ID'])) {
												$select_leader_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $row['ID'] . " AND `leader`=" . $id_additional_assigned)->Fetch();
											}
											
											echo "<p class='laboratory-manager additional-assigned' data-id-assigned='" . $id_additional_assigned . "'  data-id-laba='" . $id_laba_additional_assigned . "'><input type='checkbox' name='verified' " . (!empty($select_leader_check_tz['confirm']) ? 'checked' : '') . " disabled>  " . $name_lastname_additional_assigned . "</p>";
										}
									}
								}*/
							}
							
							$all_id_assigned = null;
							
							if (!empty($arr_id_additional_assigned) && !empty($id_main_assigned)) {
									$all_id_assigned = $arr_id_additional_assigned;
									//array_push($all_id_assigned, (int)$id_main_assigned); 
									array_push($all_id_assigned, (int)$id_main_assigned, 1); 
							} else {
								//$all_id_assigned[] =  (int)$id_main_assigned;  
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
			<!--'Техническое задание утверждено руководителем лаборатории' окончание-->
        <!------------------------------НЕРАЗРУШАЮЩИЙ КОНТРОЛЬ НАЧАЛО---------------->
		<tr><td width="250" height="40">Схема испытаний </td><td><select class="tz" id="SHEMA" name="SHEMA">
		 <option value="1" <?=$row['SHEMA']=='1' ? 'selected' : ''?>>Схема "Г"</option>
		 <!--<option value="2">Акт передачи проб</option>-->
		 <option value="2" <?=$row['SHEMA']=='2' ? 'selected' : ''?>>Схема "В"</option>
		 </select>
		</td></tr>
		<?php 
		//dump($dealForTZ);
		if($dealForTZ['TYPE_ID']==4){
		?>
		<!---->
		
		<tr class="shema_v" style="display:<?= empty($row['SHEMA']) ||  $row['SHEMA']=='1' ? 'none' : 'table-row' ?>">
		<td style="font-weight:bold">Список градуировочных зависимостей</td>
		<td>
			<datalist id="grad_dep">
				<?php
				$select_grad_dep = $DB->Query("SELECT * FROM `GRAD_DEP`");
				
				while($grad_dep = $select_grad_dep->fetch()) {
					
					$date_grad = !empty($grad_dep['DATE']) ? date('d.m.Y', strtotime($grad_dep['DATE'])) : '';
					$id_grad = !empty($grad_dep['ID']) ? $grad_dep['ID'] : '';
					
					echo "<option data-grad='" . $id_grad . "' value='№" . $id_grad . ' , от ' . $date_grad ."'></option>";
				}
					
				
				?>
			</datalist>
			<input class="tz" id="grad" type='text' list='grad_dep' name='grad_dep' value="<?= $value_grad ?>">
		</td>
        </tr>
		<!---->
		<tr class="shema_v2"  style="display:<?= empty($row['SHEMA']) || $row['SHEMA']=='1' ? 'none' : 'table-row' ?>">
		<td style="font-weight:bold" >Градуировочная зависимость</td>
		<td>
			<input class="tz" type='number' step='0.0001' name='calibrDepA' value="<?= $row['CALIBR_DEP_A'] ?>"> Х + <input class="tz" type='number' step='0.0001' name='calibrDepB' value="<?= $row['CALIBR_DEP_B'] ?>">
		</td>
        </tr>
			<tr  class="shema_g"  style="display:<?= $row['SHEMA']=='2' ? 'none' : 'table-row' ?>">
		<td style="font-weight:bold">Список листов расчета Кс</td>
		<td>
			<datalist id="c_coeff">
				<?php
				$select_c_c = $DB->Query("SELECT * FROM `COINCED_COEFF`");
				
				while($c_c = $select_c_c->fetch()) {
					
					$date_c = !empty($c_c['DATE']) ? date('d.m.Y', strtotime($c_c['DATE'])) : '';
					$id_c = !empty($c_c['ID']) ? $c_c['ID'] : '';
					
					echo "<option data-grad='" . $id_c . "' value='№" . $id_c . ' , от ' . $date_c ."'></option>";
				}
					
				
				?>
			</datalist>
			<input class="tz" id="coeff" type='text' list='c_coeff' name='c_coeff' value="<?= $value_grad_c ?>">
		</td>
        </tr>
		<!---->
		<tr  class="shema_g2"  style="display:<?= $row['SHEMA']=='2' ? 'none' : 'table-row' ?>">
		<td style="font-weight:bold">Коэффициент совпадения</td>
		<td>
			<input class="tz" type='number' step='0.0001' name='coin_coeff' value="<?= $row['COIN_COEFF_VAL'] ?>">
		</td>
        </tr>
		<tr class="shema_v3">
		<td style="font-weight:bold">Коэффициент вариации</td>
		<td>
			
			<select class="tz" type='text' name='varCoeff'>
			<option value="6" selected>6 и менее</option>
			<?
			 for($i=7; $i<17; $i++)
			 {
				 echo "<option value=".$i." ".((int)$row['VAR_KOEFF']==$i ? 'selected' : '')	.">".$i."</option>";
			 }
			?>
			</select>
		</td>
        </tr>
		<tr class="shema_v4"  style="display:<?= empty($row['SHEMA']) || $row['SHEMA']=='1' ? 'none' : 'table-row' ?>">
		<td style="font-weight:bold">Среднеквадратическое отклонение</td>
		<td>
			<input class="tz" type='number'  step="0.01" name='concreteType' value="<?= $row['CONCRETE_TYPE']?>">
		</td>
        </tr>
	
	
		
		<?php 
		}
		?>
		</table>
		
        <br/>
        <br/>
        <?php
		
        $materialsReq = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = " . $row['ID_Z']);
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
                            <td class="btn-add-del">
                                <input type="button" value="+" id="new_material">
                                <input type="button" value="-" name="delMaterial" class='delMaterial'>
                            </td>
                            <!----------------'При выборе материала, добавление методик' окончание------------------>
                            <!----------------'Удаление методики' начало------------------->
                            <td>
                                <!--'Кнопка удаление методики'-->
                                <input type="button" value="Удалить методики" name="delMetodic" class='delMetodic'>
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
                                <input type="button" value="+" id="new_material">
                                <input type="button" value="-" name="delMaterial" class='delMaterial'>
                            </td>
                            <!----------------'При выборе материала, добавление методик' окончание------------------>
                            <!----------------'Удаление методики' начало------------------->
                            <td>
                                <!--'Кнопка удаления методики'-->
                                <input type="button" value="Удалить методики" name="delMetodic" class='delMetodic'>
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
                            <td><input type="number" name="price[<?= $key ?>][]" class="oborud"
                                       value="<?= $arrDatum['price'][$g] ? $arrDatum['price'][$g] : '0' ?>"
                                       style="<?= $arrDatum['price'][$g] ? '' : 'display:none' ?>"></input>
                                <?php if (!$arrDatum['price'][$g]) echo "<div class='oborudDash'>-</div>"; ?>
                            </td>
                            <td><input type="checkbox" class="dash" <?= $arrDatum['price'][$g] ? '' : 'checked' ?>></td>
                            <?php

                            if (!$g) {
                                echo "<td class='btn-method-add-del'><input style='margin-left:5px' type='button' class='add2' value='+'></td>";
                            } else {
                                echo "<td class='btn-method-add-del'>
										<input style='margin-left:5px' type='button' class='add2' value='+'>
										<input style='margin-left:5px' type='button' class='del' value='-'>
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
		<!--'Техническое задание утверждено руководителем лаборатории' начало-->
		<input type="hidden" name="main_user_id" id= "main_user_id" value="<?= !empty($id_main_assigned) ? $id_main_assigned : '' ?>">
		<!--'Техническое задание утверждено руководителем лаборатории' начало-->
        <br/><br/>
        <?php

        if (in_array('8', $USER->GetUserGroupArray())) { ?><p>Общая
            сумма: <?php if (isset($row['PRICE']) && !empty($row['PRICE'])) echo number_format($row['PRICE'], 2, ',', '') . " руб. "/*.((isset($row['DISCOUNT']) && !empty($row['DISCOUNT'])) ? '(С учетом скидки '.$row['DISCOUNT'].'%)' : '')*/
            ; else echo "-";
            ?></p><?
        }
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
		<!--'Техническое задание утверждено руководителем лаборатории' начало-->
        <!--<input type="submit" value="Сохранить" id="new_tz" <//?= !empty($sum_select_check_tz) && $id_main_assigned != $USER->GetID() ? 'disabled' : '' ?>>-->
        <input type="submit" value="Сохранить" id="new_tz" <?= (!in_array($USER->GetID() , $all_id_assigned) && ($USER->GetID() != 62) && ($USER->GetID() != 35) && ($USER->GetID() != 43)) ? 'disabled' : '' ?>>
        <!--<input type="submit" value="Сохранить" id="new_tz" <//?= !in_array($USER->GetID() , [1]) ? 'disabled' : '' ?>>-->
		<!--'Техническое задание утверждено руководителем лаборатории' окончание-->
		<!--'Нумерация материалов в ТЗ' начало-->
        <input type="button" value="Добавить материал" id="new_material" class="btn-new-material">
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
			
			//--'Договор в request_new' начало--//
			$select_ba_tz = null;
			if (!empty($_GET['ID'])) {
				$select_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z` = " . (int)$_GET['ID'])->Fetch();
			}
			//--'Договор в request_new' окончание--//
			
			//--'Техническое задание утверждено руководителем лаборатории' начало--//
			$result_select_check_tz = null;
			$sum_select_check_tz = null;
			if ($select_ba_tz['ID']) {
				$result_select_check_tz = $DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`=" . $select_ba_tz['ID']);  
			}
			if($result_select_check_tz)
				$sum_select_check_tz = !empty($result_select_check_tz->SelectedRowsCount()) ? $result_select_check_tz->SelectedRowsCount() : '';
			
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
				<!--'Договор в request_new' начало-->
				<?php
				$resDealsContract = null;
				if (!empty($_GET['ID'])) {
					$resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL`=" . (int)$_GET['ID'])->Fetch();
				}
				?>
                    <!--<select class="tz" name="DOGOVOR_NUM">
                        <?php
                        /*echo '<option></option>';
                        $ba_contr = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `CLIENT_ID`=".$dealForTZ['COMPANY_ID']);
                        while ($obj = $ba_contr->Fetch()) {
							//--'Договор в request_new' начало--//
							$selectedContract = (!empty($resDealsContract['ID_CONTRACT']) && $resDealsContract['ID_CONTRACT'] == $obj['ID']) ? 'selected' : '';
							//--'Договор в request_new' окончание--//
                            $objId = !empty($obj['ID']) ? $obj['ID'] : '';
                            $numberContract = !empty($obj['NUMBER']) ? $obj['NUMBER'] : $objId;

                             echo "<option data-id='" . $objId . "' " . $selectedContract . ">
								". ($obj['CONTRACT_TYPE'] ? $obj['CONTRACT_TYPE'] : 'Договор') ." №" . $numberContract . " от " . ruDate($obj['DATE']) . ", ID" . $objId . "
							</option>";
                        }*/
                        ?> 
                    </select>
                    <input class="tzHiddenСontract" type="text" value="" disabled>
                    <input type="hidden" name='dogovorNumHidden' value="">
                    <input class='tzNoСontract' type='checkbox' name='noСontract'
                           <//?= (empty($resDealsContract['ID_CONTRACT'])) ? 'checked' : '' ?>>Договор
                    еще не составлен-->
				   <?php
						//Если Договор составлен выводим его , иначен "Договор еще не составлен"
						if (!empty($resDealsContract['ID_CONTRACT'])) {
							$result_select_dogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID` = " . (int)$resDealsContract['ID_CONTRACT'])->Fetch();
							
							//id договора
							$dogovor_id = !empty($result_select_dogovor['ID']) ? $result_select_dogovor['ID'] : '';
							
							//Дата создания договора
							$dogovor_date = !empty($result_select_dogovor['DATE']) ? ruDate($result_select_dogovor['DATE']) : '';
							
							//Номер договора
							$number_contract = !empty($result_select_dogovor['NUMBER']) ? $result_select_dogovor['NUMBER'] : $dogovor_id;
							
							echo ($result_select_dogovor['CONTRACT_TYPE'] ? $result_select_dogovor['CONTRACT_TYPE'] : 'Договор') ." №" . $number_contract . " от " . $dogovor_date . ", ID" . $dogovor_id;
						} else {
							echo "Договор еще не составлен";  
						}
				   ?>
					<!--'Договор в request_new' окончание-->
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
			<!--'Договор в request_new' окончание--> 
			<!--'Техническое задание утверждено руководителем лаборатории' начало-->
			<?php
			//echo'<pre>';var_dump($arr_id_leader);
			//$arr_id_leader = [1];
			//if (!empty($arr_id_leader) && in_array($USER->GetID(), $arr_id_leader)) :
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
								
								/*if ($arr_id_additional_assigned) {
									
									foreach($arr_id_additional_assigned as $id_additional_assigned)
									{
										
										$assign_user = CUser::GetByID((int)$id_additional_assigned)->Fetch();
										
										$assign_name = !empty($assign_user['NAME']) ? $assign_user['NAME'] : '';
										$assign_lastname = !empty($assign_user['LAST_NAME']) ? $assign_user['LAST_NAME'] : '';
										$name_lastname_additional_assigned = getInitials($assign_name)." ".$assign_lastname;
										
										
										$id_laba_additional_assigned = !empty($assign_user['UF_DEPARTMENT'][0]) ? $assign_user['UF_DEPARTMENT'][0] : '';
										
										if (!empty($name_lastname_additional_assigned) && !empty($id_additional_assigned) && !empty($id_laba_additional_assigned) && !empty($arr_all_main_assigned[$id_laba_additional_assigned]) && $arr_all_main_assigned[$id_laba_additional_assigned] == $id_additional_assigned) {
											echo "<p class='laboratory-manager additional-assigned' data-id-assigned='" . $id_additional_assigned . "'  data-id-laba='" . $id_laba_additional_assigned . "'>" . $name_lastname_additional_assigned . "</p>";
										}
									}
								}*/
							}
							
							$all_id_assigned = null;
							
							if (!empty($arr_id_additional_assigned) && !empty($id_main_assigned)) {
									$all_id_assigned = $arr_id_additional_assigned;
									//array_push($all_id_assigned, (int)$id_main_assigned); 
									array_push($all_id_assigned, (int)$id_main_assigned, 1); 
							} else {
								//$all_id_assigned[] =  (int)$id_main_assigned;  
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
			<!--'Техническое задание утверждено руководителем лаборатории' окончание-->
        </table>
        <br/>
        <?php
        $materialsReq = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` = " . $_GET['ID']);
        /*----------------'При выборе материала, добавление методик' начало---------------------*/
        $number_material = 0;

        foreach ($mater_arr as $key => $arrDatum) {
            if (empty($key) && $key == 0) {
                $number_material = 1;
            }
            /*----------------'При выборе материала, добавление методик' окончание------------------*/
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
                                <input class="tz" type='hidden' name='mater[<?= $key ?>]'
                                       value="<?= $material['NAME_MATERIAL'] . ($material['ID_MATERIAL'] ? ', ID ' . $material['ID_MATERIAL'] : '') ?>">
                                <input class="tz" type='text' name='idMater[<?= $key ?>]' list="materials" required
                                       value="<?= $material['NAME_MATERIAL'] ?>">
                            </td>
                            <td style="font-weight:bold">Кол-во проб/образцов</td>
                            <!----------------'При выборе материала, добавление методик' начало------------------>
                            <td class="amount">
                                <input class="tz" type='number' name='obiem[<?= $key ?>]' min='1' value="1" required
                                       value="">
                            </td>
                            <td class="btn-add-del">
                                <input type="button" value="+" id="new_material">
                                <input type="button" value="-" name="delMaterial" class='delMaterial'>
                            </td>
                            <!----------------'При выборе материала, добавление методик' окончание------------------>
                            <!----------------'Удаление методики' начало------------------->
                            <td>
                                <!--'Кнопка удаление методики'-->
                                <input type="button" value="Удалить методики" name="delMetodic" class='delMetodic'>
                            </td>
                            <!----------------'Удаление методики' окончание------------------->
                        </tr>
                        <!-------------------'При выборе материала, добавление методик' начало-------->
                        <?php
						
                        if (!empty($material['ID_MATERIAL'])) {
                            //Получаем все госты привязанные к материалу, (без повторений)
                            //$gostListNew = $DB->Query("SELECT DISTINCT g_c.gost, g_c.id FROM gosts_connection AS g_c INNER JOIN ba_gost AS b_g ON g_c.id = b_g.id_gost WHERE `NON_ACTUAL` != 1 AND b_g.DOP =" . (int)$material['ID_MATERIAL']);
                            /*----------------'Удаление методики' начало-------------------*/
                            $gostListNew = $DB->Query("SELECT DISTINCT g_c.gost, g_c.id FROM gosts_connection AS g_c INNER JOIN ba_gost AS b_g ON g_c.id = b_g.id_gost WHERE `NON_ACTUAL` != 1 AND b_g.DOP =" . (int)$material['ID_MATERIAL']);
                            /*----------------'Удаление методики' окончание-------------------*/
			
                            if ($gostListNew->SelectedRowsCount()) {
                                ?>
                                <tr class="tr-select-gost visible-block">
                                    <td></td>
                                    <td class="abbreviation-gost">гост</td>
                                    <td>
                                        <select class="gost-select" name="gost-select[<?= $key ?>][]"
                                                data-key="<?= $key ?>">
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
                            <!-------------------'При выборе материала, добавление методик' начало-------->
                            <tr class="tr-select-gost">
                                <td></td>
                                <td class="abbreviation-gost">гост</td>
                                <td>
                                    <select class="gost-select" name="gost-select[<?= $key ?>][]"
                                            data-key="<?= $key ?>">
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
                                       value="<?= $arrDatum['mater'] ?>">
                            </td>
                            <td style="font-weight:bold">Кол-во проб/образцов</td>
                            <!----------------'При выборе материала, добавление методик' начало------------------>
                            <td class="amount">
                                <input class="tz" type='number' name='obiem[<?= $key ?>]' min='1' value="1" required
                                       value="">
                            </td>
                            <td class="btn-add-del">
                                <input type="button" value="+" id="new_material">
                                <input type="button" value="-" name="delMaterial" class='delMaterial'>
                            </td>
                            <!----------------'При выборе материала, добавление методик' окончание------------------>
                            <!----------------'Удаление методики' начало------------------->
                            <td>
                                <!--'Кнопка удаление методики'-->
                                <input type="button" value="Удалить методики" name="delMetodic" class='delMetodic'>
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
                    <th>Цена</th>
                    <th>Без цены</th>
                    <th></th>
                    </thead>
                    <tbody id="tz_body" name="tz_body">
                    <?php
                    if ($mater_arr_id[$key] > 0) {
                        /*-------------------'При выборе материала, добавление методик' начало--------*/
                        //$metodicList = $DB->Query("SELECT * FROM `ba_gost` WHERE `DOP`=" . (int)$mater_arr_id[$key]);
                        //if (!$metodicList->SelectedRowsCount()) {
                        ?>
						<!--'Нумерация материалов в ТЗ' начало-->
                        <tr class="tr-gost" id="<?= $key ?>" data-gost="0">
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
										$gost_id = !empty($gost['ID']) ? $gost['ID'] : '';
										
										$gost_price = !empty($gost['PRICE']) ? $gost['PRICE'] : ''; 
										
										if (!empty($result_select_dogovor['ID']) && !empty($result_select_dogovor['LONGTERM'])) {
											//Находим цены по ID ГОСТа
											$price_gost_longterm = null;
											if (!empty($gost_id)) {
												$price_gost_longterm = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT` = " . (int)$result_select_dogovor['ID'] . " AND `ID_GOST` = " . (int)$gost_id)->Fetch();
											}
											
											$gost_price = isset($price_gost_longterm['PRICE']) ? $price_gost_longterm['PRICE'] : $gost_price;
										}
									
                                    //echo "<option data-price='" . $gost['PRICE'] . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "' " . ($gost['ID'] == $metodic['ID'] ? 'selected' : '') . ">" . $gost['GOST'] . ' ' .  $gost['SPECIFICATION'] . ' (' . $gost['GOST_PUNKT']. ")</option>";
                                    echo "<option data-price='" . $gost_price . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost_id . "' " . ($gost_id == $metodic['ID'] ? 'selected' : '') . ">" . $gost['GOST'] . ' ' .  $gost['SPECIFICATION'] . ' (' . $gost['GOST_PUNKT']. ")</option>";
                                //--'Договор в request_new' окончание--//
								}
                                ?>
                            </select>
                            <!----------------'При выборе материала, добавление методик' начало--------------------->
                            <!--<a class='methodsLink' target='_blank'>
                                <div class='methodsLinkPng' title='Методика'></div>
                            </a>-->
                        </td>
                        <td class="method">
                            <a class='methodsLink' target='_blank'>
                                <div class='methodsLinkPng' title='Методика'></div>
                            </a>
                        </td>
                        <!----------------'При выборе материала, добавление методик' окончание------------------>
                        <td class="tdGost">
                            <!--'Нумерация материалов в ТЗ' начало-->
                            <select name="gost_new[<?= $key ?>][]" style="width:350px" class="gost2 conditions styleGost"
                                    data-id="<?= $key ?>">
                            <!--'Нумерация материалов в ТЗ' окончание-->
                                <option value="2522" selected>-- | --</option>
                                <?php
                                $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                                while ($gost = $ba_gost->Fetch()) {
                                    echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "'>" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                }
                                ?>
                            </select></td>
                        <!-------------------'При выборе материала, добавление методик' начало------->
                        <td class="price-method"><input type="number" name="price[<?= $key ?>][]" class="oborud"
                                                        value="<?= $metodic['PRICE'] ?>" required></input></td>
                        <td><input type="checkbox" class="dash"></td>
                        <?php
                        echo "<td class='btn-method-add-del'><input style='margin-left:5px' type='button' class='add2' value='+'></td>";

                        /*}
                        $g = 0;
                        while ($metodic = $metodicList->Fetch()) {
                            if (!$metodic['NON_ACTUAL']) {
                                ?>

                                <tr id="<?= $key ?>">
                                    <td>
                                        <select name="gost[<?= $key ?>][]" style="width:350px" class="gost methods"
                                                data-id="<?= $key ?>" required>
                                            <option value="" disabled selected>Выберите</option>
                                            <?php
                                            $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                                            while ($gost = $ba_gost->Fetch()) {
                                                echo "<option data-price='" . $gost['PRICE'] . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "' " . ($gost['ID'] == $metodic['ID'] ? 'selected' : '') . ">" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <a class='methodsLink' target='_blank'>
                                            <div class='methodsLinkPng' title='Методика'></div>
                                        </a>
                                    </td>
                                    <td>
                                        <select name="gost_new[<?= $key ?>][]" style="width:350px" class="gost2"
                                                data-id="<?= $key ?>">
                                            <option value="2522" selected>-- | --</option>
                                            <?php
                                            $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                                            while ($gost = $ba_gost->Fetch()) {
                                                echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "'>" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                            }
                                            ?>
                                        </select></td>
                                    <td><input type="number" name="price[<?= $key ?>][]" class="oborud"
                                               value="<?= $metodic['PRICE'] ?>" required></input></td>
                                    <td><input type="checkbox" class="dash"></td>
                                    <?php

                                    if (!$g) {
                                        echo "<td><input style='margin-left:5px' type='button' class='add2' value='+'></td>";
                                    } else {
                                        echo "<td>
											<input style='margin-left:5px' type='button' class='add2' value='+'>
											<input style='margin-left:5px' type='button' class='del' value='-'>
										</td>";
                                    }

                                    $g++;

                                    ?>
                                </tr>
                                <?php
                            }
                        }*/
                    } else {

                        $g = 0;
                        ?>
                        <!--'Нумерация материалов в ТЗ' начало-->
                        <tr class="tr-gost" id="<?= $key ?>" data-gost="0">
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
										$gost_id = !empty($gost['ID']) ? $gost['ID'] : '';
										
										$gost_price = !empty($gost['PRICE']) ? $gost['PRICE'] : ''; 
										
										if (!empty($result_select_dogovor['ID']) && !empty($result_select_dogovor['LONGTERM'])) {
											
											$price_gost_longterm = null;
											if (!empty($gost_id)) {
												$price_gost_longterm = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT` = " . (int)$result_select_dogovor['ID'] . " AND `ID_GOST` = " . (int)$gost_id)->Fetch();
											}
											
											$gost_price = isset($price_gost_longterm['PRICE']) ? $price_gost_longterm['PRICE'] : $gost_price;
										}
									
                                        //echo "<option data-price='" . $gost['PRICE'] . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "' >" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                        echo "<option data-price='" . $gost_price . "' data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost_id . "' >" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                    //--'Договор в request_new' окончание--//
									}
                                    ?>
                                </select>
                                <!----------------'При выборе материала, добавление методик' начало--------------------->
                                <!--<a class='methodsLink' target='_blank'>
                                    <div class='methodsLinkPng' title='Методика'></div>
                                </a>-->
                            </td>
                            <td class="method">
                                <a class='methodsLink' target='_blank'>
                                    <div class='methodsLinkPng' title='Методика'></div>
                                </a>
                            </td>
                            <!----------------'При выборе материала, добавление методик' окончание------------------>
                            <td class="tdGost">
                                <!--'Нумерация материалов в ТЗ' начало-->
                                <select name="gost_new[<?= $key ?>][]" style="width:350px" class="gost2 conditions styleGost"
                                        data-id="<?= $key ?>">
                                <!--'Нумерация материалов в ТЗ' окончание-->
                                    <option value="2522" selected>-- | --</option>
                                    <?php
                                    $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
                                    while ($gost = $ba_gost->Fetch()) {
                                        echo "<option data-type='" . $gost['NORM_TEXT'] . "'  value='" . $gost['ID'] . "'>" . $gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] . "</option>";
                                    }
                                    ?>
                                </select></td>
                            <td><input name="price[<?= $key ?>][]" class="oborud" value="" required></input></td>
                            <td><input type="checkbox" class="dash"></td>
                            <?php

                            if (!$g) {
                                echo "<td class='btn-method-add-del'><input style='margin-left:5px' type='button' class='add2' value='+'></td>";
                            } else {
                                echo "<td class='btn-method-add-del'>
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
		<!--'Договор в request_new' начало-->
        <input class="tz id-tz" type="hidden" name="id_tz" value="<?= (!empty($select_ba_tz['ID']) ? $select_ba_tz['ID'] : '') ?>">
		<!--'Договор в request_new' окончание-->
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
        <input type="submit" value="Сохранить" id="new_tz" <?= (!in_array($USER->GetID() , $all_id_assigned) && ($USER->GetID() != 62) && ($USER->GetID() != 35) && ($USER->GetID() != 43)) ? 'disabled' : '' ?>>
        <!--<input type="submit" value="Сохранить" id="new_tz" <//?= !in_array($USER->GetID() , [1]) ? 'disabled' : '' ?>>-->
		<!--'Нумерация материалов в ТЗ' начало-->
        <input type="button" value="Добавить материал" id="new_material" class="btn-new-material">
		<!--'Нумерация материалов в ТЗ' окончание-->
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

<!--'Договор в request_new' начало'-->
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

                 if ((valActNum || !valActNum && checkt) && (valDateAct || !valDateAct && checkt) && $selectFalse) {
					<!--'Техническое задание утверждено руководителем лаборатории' начало-->
					event.preventDefault();
					
					var getEdit = '<?= $_GET["EDIT"] ?>' ? '<?= $_GET["EDIT"] ?>' : '';
                    var getId = '<?= $_GET["ID"] ?>' ? '<?= $_GET["ID"] ?>' : '';
					var formTz = $('#form_tz');
					var arrGost = [];

                     $('.methods option:selected').each(function (index, element) {
                         arrGost.push($(element).val());
                     });
					 
					 var isOk = checkMethod(arrGost,getEdit,getId);
		
					console.log('isOk', isOk);
					 
					 if (isOk) {
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
				$('#grad').on('change', function() {
			var idGrad;
			if ($(this).val()) {
				var arrGrad = $(this).val().split(' ,');
				idGrad = arrGrad[0] ? arrGrad[0].slice(1) : '';
				
			}
			
			if (idGrad) {
				$.ajax({
					method: 'post',
					url: 'grad_for_tz.php',
					data: {id_grad: idGrad},
					dataType: 'json',
					success: function(dataGrad) {
						if(dataGrad['concrete_type']) {
							$('input[name=concreteType]').val(dataGrad['concrete_type']);
						}
						
						if(dataGrad['calibr_dep_a']) {
							$('input[name=calibrDepA]').val(dataGrad['calibr_dep_a']);
						}
						
						if(dataGrad['calibr_dep_b']) {
							$('input[name=calibrDepB]').val(dataGrad['calibr_dep_b']);
						}
						$('input[name=coin_coeff]').val("");
						$('input[name=c_coeff]').val("");
					}
				});
			}
	
		});
		<!---->
		
		$('#coeff').on('change', function() { 
			var idGrad;
			if ($(this).val()) {
				var arrGrad = $(this).val().split(' ,');
				idGrad = arrGrad[0] ? arrGrad[0].slice(1) : '';
				
			}
			
			if (idGrad) {
				$.ajax({
					method: 'post',
					url: 'c_coeff_for_tz.php',
					data: {id_grad: idGrad},
					dataType: 'json',
					success: function(dataGrad) {
						
						
						if(dataGrad['calibr_dep_a']) {
							$('input[name=coin_coeff]').val(dataGrad['calibr_dep_a']);
						}
						
						
						
					}
				});
			}
	
		});
		
		$('#SHEMA').on('change', function() { 
		console.log ('!!!');
			var idShema = $(this).val();
			if (idShema==1) {
				$(this).parent().parent().parent().find('.shema_g').css('display','table-row');
				$(this).parent().parent().parent().find('.shema_g2').css('display','table-row');
				$(this).parent().parent().parent().find('.shema_v').css('display','none');
				$(this).parent().parent().parent().find('.shema_v2').css('display','none');
			//	$(this).parent().parent().parent().find('.shema_v3').css('display','none');
				$(this).parent().parent().parent().find('.shema_v4').css('display','none');
				
			}
			if (idShema==2) {
				$(this).parent().parent().parent().find('.shema_g').css('display','none');
				$(this).parent().parent().parent().find('.shema_g2').css('display','none');
				$(this).parent().parent().parent().find('.shema_v').css('display','table-row');
				$(this).parent().parent().parent().find('.shema_v2').css('display','table-row');
				//$(this).parent().parent().parent().find('.shema_v3').css('display','table-row');
				$(this).parent().parent().parent().find('.shema_v4').css('display','table-row');
				$('input[name=coin_coeff]').val("");
				$('input[name=c_coeff]').val("");
			}
			
		});
        });
    </script>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>