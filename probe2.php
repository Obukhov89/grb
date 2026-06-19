<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10.04.2019
 * Time: 15:36
 */


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал приемки проб");
?>

  
<?php
if (!empty($_POST['zakaz_man'])) {
    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
    $sql = $res->Fetch();
    $probe = unserialize($sql['PROBE']);
	$num_probe = array();
	foreach($_POST['number_probe'] as $p_n => $probe_num)
	{
		$num_probe[] = addslashes($probe_num); 
	}

	
	//var_dump($_POST['number_probe']);
    $probe[$_GET['mater']] = array(
        "zakaz_man" => $_POST['zakaz_man'],
        "number_probe" => $num_probe,//$_POST['number_probe'],
        "mesto_data" => $_POST['mesto_data'],
        "probe_proizv" => $_POST['probe_proizv'],
        "number" => $_POST['number'],
        "sh_number" => $_POST['sh_number'],
        "dat" => $_POST['dat'],
    );

    $probe = serialize($probe);
	var_dump($probe);
    $res = $DB->Query("UPDATE `ba_tz` SET `PROBE`='" . $probe . "' WHERE ID = " . (int)$_GET['TZ_ID']);
	$deal1 = CCrmDeal::GetByID((int)$_GET['ID']);
	if ($deal1['TYPE_ID']!='COMPLEX') {
		if (CModule::IncludeModule('crm')) {
			$deal = new CCrmDeal;
			$arDealUpdate = array('STAGE_ID' => 'FINAL_INVOICE');
			$resUpd = $deal->Update((int)$_GET['ID'],
				$arDealUpdate,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}
	}
}

if (!empty($_GET['TZ_ID'])) {
    $deal = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('ID' => (int)$_GET['ID']))->Fetch();
    if ($deal['STAGE_ID'] == 'FINAL_INVOICE' || $deal['TYPE_ID'] == "COMPLEX") {
        $edit = true;
    } else {
        $edit = false;
        echo "<h3>Для редактирования необходимо что бы сделка была в статусе 'Акт приёмки'</h3>";
    }
    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
    $sql = $res->Fetch();
    $tz = unserialize($sql['TZ'])[$_GET['mater']];
    $probe = unserialize($sql['PROBE'])[$_GET['mater']];
	//var_dump($probe);
    $materials = $deal['UF_CRM_1569151529509'];
	
    $companys = CCrmCompany::GetList();
	
    ?>
    <?php if ($edit): ?>
        <form method="post">
    <?php endif; 
	//echo $_GET['mater'];
	
	$probe_num='';
	if (!empty($sql['ACT_NUM']))
		$probe_num=$sql['ACT_NUM'].'/19';
	else
		$probe_num = (!empty($probe['number'])) ? $probe['number'] : $sql['ID'] . '/19'; 
	?>
    <table id="probe">
        <tr>
            <th colspan="2">Регистрационный номер в ИЦ
                <u><?= $probe_num ?></u>
                Дата поступления в ИЦ
                <u><?= (!empty($probe['dat']) ? date("d.m.Y", strtotime($probe['dat'])) : date("d.m.Y")) ?></u></th>
            <input type="hidden" name="number"
                   value="<?= $probe_num ?>">
            <input type="hidden" name="dat"
                   value="<?= (!empty($probe['dat']) ? date("d.m.Y", strtotime($probe['dat'])) : date("d.m.Y")) ?>">
        </tr>
        <tr>
            <td>
                <label>Заказчик: <?= in_array('1',$USER->GetUserGroupArray()) ? $deal['COMPANY_TITLE'] : "Нет доступа" ?>
                    <input type="hidden" name="zakaz_man" value="<?= $deal['COMPANY_ID'] ?>">
                </label>
            </td>
        </tr>
    </table>
    <br/>
    <table id="probe">
        <thead>
		<tr>
            <th>Шифр в ИЦ</th>
            <th>Наименование пробы (образцов)</th>
        </tr>
		</thead>
        <?php 
		$k=0;
		for ($i = (int)$_GET['num_probe']+1; $i <= (int)$_GET['num_probe']+(int)$tz['obiem']; $i++) { 
		$probe_sh='';
			if (!empty($sql['ACT_NUM']))
				$probe_sh=$sql['ACT_NUM'].".".$i."/19";
			else
				$probe_sh = (!empty($probe['sh_number'][$k])) ? $probe['sh_number'][$k] : $sql['ID'] . ".".$i;
			//var_dump($probe['sh_number']);
		?><tbody>
            <tr>
                <td width="250px">
                   <!-- <label><?= $probe_sh ?></label>-->
                    <input type="text" class="tz" name="sh_number[]"
                           value="<?= $probe['sh_number'][$k] ?>">
                </td>
                <td>
                    <label><input type="text" class="tz" name="number_probe[]" size="70"
                                  value="<?= ($probe['number_probe'][$k] ? $probe['number_probe'][$k] : $materials[$_GET['mater'] - 1]) ?>"></label>
                </td>
            </tr>
			</tbody>
        <?php $k++; } ?>
    </table>
    <br/><br/>
	
	<br/>
	
    <table id="probe">
		<thead>
		<tr>
            <th>ДОПОЛНИТЕЛЬНЫЕ ХАРАКТЕРИСТИКИ</th> <th> 
            </th>
        </tr>
		</thead>
        <tr>
            <td >
                Место и дата отбора проб (образцов) </td><td> <input type="text" class="tz" name="mesto_data" size="70"
                                                                   value="<?= $probe['mesto_data'] ?>">
            </td>
        </tr>
        <tr>
            <td>
                Отбор проб произвёл </td><td><input type="text" class="tz" name="probe_proizv" size="70"
                                                   value="<?= $probe['probe_proizv'] ?>">
            </td>
        </tr>
        <tr>
            <td>
                Определяемые показатели: </td><td>
                    <?php
                    $gosts = [];
                    foreach ($tz['gost'] as $item) {
                        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
                        $gost = $res->Fetch();
                        $gosts[] = $gost['SPECIFICATION'];
                    }
                    echo implode(', ', $gosts);
                    ?>
                
            </td>
        </tr>
    </table>
    <?php if ($edit): ?>
        <input type="submit" value="Сохранить">
    <?php endif; ?>
    <?php if ($edit): ?>
        </form>
    <?php endif; ?>
	<br />
    <a href="/probe.php?ID=<?= $_GET['ID'] ?>">Назад</a> |
    <a href="/protocol_generator/probe.php?ID=<?= $_GET['ID'] ?>&TZ_ID=<?= $_GET['TZ_ID'] ?>&mater=<?= $_GET['mater'] ?>">Скачать</a> | 
	<a href="/results_isp.php?ID=<?= $_GET['ID'] ?>">Перейти к испытаниям</a>
    <?php
} elseif (!empty($_GET['ID'])) {
	$deal1 = CCrmDeal::GetByID((int)$_GET['ID']);
	if ($deal1['TYPE_ID']!='COMPLEX') {
		if (CModule::IncludeModule('crm')) {
			$deal = new CCrmDeal;
			$arDealUpdate = array('STAGE_ID' => 'FINAL_INVOICE');
			$resUpd = $deal->Update((int)$_GET['ID'],
				$arDealUpdate,
				true,
				true,
				array('DISABLE_USER_FIELD_CHECK' => true)
			);
		}
	}
    ?>
    <a href="/probe.php">К списку</a> 
	<?php 
		if (in_array('1',$USER->GetUserGroupArray()))  {echo  " | <a href='//172.16.255.2/crm/deal/details/". $_GET['ID'] . "/'>Сделка</a>"; } else {};
	 ?>
    <br/><br/>
    <form method="post">
        <table id="probe">
		<thead>
            <th>Наименование материала</th>
            <th>Наименование показателя</th>
            <th>Объём работ</th>
            <th>Функции</th>
			</thead>
            <?php
            $deal = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('ID' => $_GET['ID']), array("UF_CRM_1554700718621"))->Fetch();
            preg_match('/[a-z\.\/\?_A-Z-]*=([0-9]*)/', $deal['UF_CRM_1554700718621'], $output_array);

            $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$output_array[1]);
            $sql = $res->Fetch();
            $arr = unserialize($sql['TZ']);
            $probe = unserialize($sql['PROBE']);
			//var_dump($probe);
            ///////////////////////////////////////////////////////////////////////////////////////
            $data = [];
            foreach ($arr as $key => $arrDatum) {
                $data[] = $arrDatum['mater'];
            }
            $a = array_count_values($data);

            $last_gost = '';
			$num_probe=0;
            foreach ($arr as $key => $arrDatum) {
                ?>
                <tr>
                    <td><?= $arrDatum['mater'] ?></td>
                    <td>
                        <?php
                        foreach ($arrDatum['gost'] as $item) {
                            $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                            echo "<p>" . $res['SPECIFICATION'] . "</p>";
                        }
						
                        ?>
                    </td>
                    <td><?= $arrDatum['obiem'] ?></td>
                    <td><a href="/probe.php?ID=<?= $_GET['ID'] ?>&TZ_ID=<?= $sql['ID'] ?>&mater=<?= $key ?>&num_probe=<?=$num_probe?>"> Заполнить/изменить акт
                            приёмки</a>
                        |
                        <a href="/protocol_generator/probe.php?ID=<?= $_GET['ID'] ?>&TZ_ID=<?= $sql['ID'] ?>&mater=<?= $key ?>">Скачать
                            акт приёмки</a></td>
                </tr>
                <?php
				$num_probe=$num_probe+(int)$arrDatum['obiem'];
            }
            ///////////////////////////////////////////////////////////////////////////////////////
            ?>
        </table>
        <input type="hidden" name="ID" value="<?= (int)$output_array[1] ?>">
        <!--        <input type="submit" value="Сохранить изменения">-->
    </form>
	<br/>
    <a href="/results_isp.php?ID=<?= $_GET['ID'] ?>">Перейти к испытаниям</a>
    <?php
} else {
    $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC', 'STAGE_ID' => 'DESC'));
	// $order = array('sort' => 'asc');
	// $tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
	// $rsUsers = CUser::GetList($order, $tmp)->Fetch();
	
	// var_dump($rsUsers);
    ?>
    <table id="probe">
	<thead>
        <th>Номер акта</th>
		<th>Основание</th>
		<th>Дата поступления</th>
		<th>Наименование заявителя</th>
		<th>Наименование продукции</th>
		<!--<th>Шифр образца (пробы)</th>-->
		<th>Ответственный</th>
		<!--<th>Наименование лаборатории</th>-->
        <th>Функции</th>
	</thead>
	<tbody>
        <?php 
		$res_probe = $DB->Query("SELECT * FROM `ba_probes` ORDER BY `ID`")->Fetch();
		while ($deal = $deals->Fetch()) {
            // if ($deal['STAGE_ID'] == 'FINAL_INVOICE')
                // $color = 'green';
            // else
                // $color = 'red';
           // var_dump($deal);
		   $res_tz   = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=".$deal['ID'])->Fetch(); 
		   $num_dog ="Договор №".$deal['ID']." от ".date('d.m.Y', strtotime($deal['DATE_CREATE']));
			?>
			
            <tr>
				<td style="color:<?= $color ?>;"><a href="//172.16.255.2/probe_card.php?ID=<?=$deal['ID']?>&ACT_NUM=<?=$res_tz['ACT_NUM']?>"><?= ($res_tz['ACT_NUM'] ? $res_tz['ACT_NUM'] : $deal['ID'])?></td>
				<td style="color:<?= $color ?>;"><?= ($res_tz['DOGOVOR_NUM'] ? $res_tz['DOGOVOR_NUM'] : $num_dog) ?></td>
                <td style="color:<?= $color ?>;"><?= $res_tz['DATE_ACT'] ? date("d.m.Y", strtotime($res_tz['DATE_ACT'])) : date("d.m.Y", strtotime($deal['DATE_CREATE']))?></td>
				<td style="color:<?= $color ?>;"><?= $deal['COMPANY_TITLE'] ?></td>
                <td style="color:<?= $color ?>;"><?= $deal['UF_CRM_1560745341'][0] ? $deal['UF_CRM_1560745341'][0] : implode(', ', $deal['UF_CRM_1569151529509']) ?></td>
                <!--<td style="color:<?//= $color ?>;"><?//= $res_probe['NAME'] ?></td>-->
				<td style="color:<?= $color ?>;"><?= $deal['ASSIGNED_BY_LAST_NAME'] ?> <?= $deal['ASSIGNED_BY_NAME'] ?></td>
                <!--<td style="color:<?//= $color ?>;"><?//= $res_probe['NAME'] ?></td> -->  
				<td><?php 
		if (in_array('1',$USER->GetUserGroupArray()))  {echo  " <a href='//172.16.255.2/crm/deal/details/". $deal['ID'] . "/'>Сделка</a> | "; } else {};
	 ?>
                    <a href="/probe.php?ID=<?= $deal['ID'] ?>">Открыть</a>
                </td>
            </tr>
        <?php } ?>
    </tbody></table>

    <?php
}
(isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';
?>
	<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"/>-->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>
	
    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>
	<script src="https://cdn.datatables.net/colreorder/1.5.1/js/dataTables.colReorder.min.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/ellipsis.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>
	
	<script>
        $(document).ready(function () {
            $('#probe').DataTable({
                "language":{
                    "processing": "Подождите...",
                    "search": "Поиск:",
                    "lengthMenu": "Показать _MENU_ записей",
                    "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                    "infoEmpty": "Записи с 0 до 0 из 0 записей",
                    "infoFiltered": "(отфильтровано из _MAX_ записей)",
                    "infoPostFix": "",
                    "loadingRecords": "Загрузка записей...",
                    "zeroRecords": "Записи отсутствуют.",
                    "emptyTable": "В таблице отсутствуют данные",
                    "paginate": {
                        "first": "Первая",
                        "previous": "Предыдущая",
                        "next": "Следующая",
                        "last": "Последняя"
                    },
					"buttons": {
						"copy": "",
						'colvis': "",
						'print': "",
						"excel": ""
					},
                    "aria": {
                        "sortAscending": ": активировать для сортировки столбца по возрастанию",
                        "sortDescending": ": активировать для сортировки столбца по убыванию"
                    }
                },
				"iDisplayLength": 50,
				"colReorder": true,
				"columnDefs": [{ "type": "natural-nohtml", "targets": 0 },{ "sType": "ruDate", "targets": 2 },{ "type": "num", "targets": 6 }, 
				{
				  "targets": 4,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				}
				],
				"order": [[ 0, "desc" ]],
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
        });
		
		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
		"ruDate-asc": function ( a, b ) {
			var ruDatea = $.trim(a).split('.');
			var ruDateb = $.trim(b).split('.');

			if(ruDatea[2]*1 < ruDateb[2]*1)
				return 1;
			if(ruDatea[2]*1 > ruDateb[2]*1)
				return -1;
			if(ruDatea[2]*1 == ruDateb[2]*1)
				{
				if(ruDatea[1]*1 < ruDateb[1]*1)
					return 1;
				if(ruDatea[1]*1 > ruDateb[1]*1)
					return -1;
				if(ruDatea[1]*1 == ruDateb[1]*1)
					{
					if(ruDatea[0]*1 < ruDateb[0]*1)
						return 1;
					if(ruDatea[0]*1 > ruDateb[0]*1)
						return -1;
					}
					else
						return 0;
				}				
		},
 
		"ruDate-desc": function ( a, b ) {
			var ruDatea = $.trim(a).split('.');
			var ruDateb = $.trim(b).split('.');
			
			if(ruDatea[2]*1 < ruDateb[2]*1)
				return -1;
			if(ruDatea[2]*1 > ruDateb[2]*1)
				return 1;
			if(ruDatea[2]*1 == ruDateb[2]*1)
				{
				if(ruDatea[1]*1 < ruDateb[1]*1)
					return -1;
				if(ruDatea[1]*1 > ruDateb[1]*1)
					return 1;
				if(ruDatea[1]*1 == ruDateb[1]*1)
					{
					if(ruDatea[0]*1 < ruDateb[0]*1)
						return -1;
					if(ruDatea[0]*1 > ruDateb[0]*1)
						return 1;
					}
					else
						return 0;
				}
		}
	});
		
		
    </script>  
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>