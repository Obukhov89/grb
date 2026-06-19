<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04.04.2019
 * Time: 15:23
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DataStorage.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php');

$APPLICATION->SetTitle("Журнал тех. заданий");
?>
  
<?php
$newResult = new NewResult();
$db = new CDB();

//ID Сделки с которого начинается рефакторинг Результатов испытания (TODO: Для новых лабораторий удалить или добавить если производится рефакторинг результатов испытаний, так же убрать из карточки card.php)
define('RESULT_REFACTORING_START_ID', 8846);

function num2word($n, $titles)
{
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
}

if (!empty($_GET['DEL'])) {
    $res = $DB->Query("DELETE FROM `ba_tz` WHERE `ID`=" . (int)$_GET['DEL']);
    $row = $res->Fetch();
    if (CModule::IncludeModule('crm')) {
        $deal = new CCrmDeal;
        $arDealUpdate = array('UF_CRM_1554700718621' => '');
        $resUpd = $deal->Update((int)$_GET['DEAL'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    }
}
(isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';
if (!empty($_GET['ID'])) {

    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['ID']);
    $row = $res->Fetch();
	


    $deal = CCrmDeal::GetByID($row['ID_Z']);
    ?>
	
	<div>
		<div class='return disable' title='Вернуться назад'></div>
		<a href="/request_card.php?ID=<?=$deal['ID']?>">
			<div class='card' title='Карточка заявки'></div><!--Карточка заявки-->
		</a>
		<a href="/requests.php">
			<div class='list' title='Вернуться к списку заявок'></div><!--Список заявок-->
		</a> 
		
		<?php
            if ($deal['STAGE_ID']!='2'&&$deal['STAGE_ID']!='3'&&$deal['STAGE_ID']!='4'&&$deal['STAGE_ID']!='WON') {

            /*$linkTz = '';
            
            if (!empty($row['ID_Z']) && in_array((int)$row['ID_Z'], $newResult->arrIdDeal) || !empty($row['ID_Z']) && (int)$row['ID_Z'] === 5138 || !empty($row['ID_Z']) && (int)$row['ID_Z'] === 5479|| !empty($row['ID_Z']) && (int)$row['ID_Z'] === 5682 || !empty($row['ID_Z']) && (int)$row['ID_Z'] === 5697 || !empty($row['ID_Z']) && (int)$row['ID_Z'] === 5893) {
                
                $linkTz = '/tz_binding_multiple_protocols.php?EDIT=' . $row['ID'];
            } else {
                $linkTz = '/tz.php?EDIT=' . $row['ID'];
            }*/
			
			$linkTz = '/tz.php?EDIT=' . $row['ID'];
        ?>
		<!--< ?php if ($deal['STAGE_ID']!='2'&&$deal['STAGE_ID']!='3'&&$deal['STAGE_ID']!='4'&&$deal['STAGE_ID']!='WON') { ?>-->

		<!--<a href="/tz.php?EDIT=< ?=$row['ID']?>">
			<div class='edit1' title='Редактировать ТЗ'></div>
		</a>-->
        <a href="<?= $linkTz ?>"> 
            <div class='edit1' title='Редактировать ТЗ'></div>
        </a>
        
            <?php }
		echo "<div class='download disable' title='Скачать'></div>
			<div class='copy disable' title='Скопировать'></div>
			<div class='remove disable' title='Удалить'></div>
			<div class='add disable' title='Добавить'></div>
			<div class='report disable' title='Отчёт'></div>";	
		if($deal['STAGE_ID']!='2'&&$deal['STAGE_ID']!='3'&&$deal['STAGE_ID']!='4'&&$deal['STAGE_ID']!='WON') {
			if (!empty($deal['ID']) && (in_array((int)$deal['ID'], $newResult->arrIdDeal) || (int)$deal['ID'] > $newResult->idDealВefore)) {
				$selectedProtocolData = null;
				if (!empty($_GET['ID'])) {
					$selectedProtocolData = $db->select("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ`={?}", [$_GET['ID']]);
				}
				$idFistProtocol = !empty($selectedProtocolData[0]['ID']) ? $selectedProtocolData[0]['ID'] : 0;


                if ($deal['ID'] > RESULT_REFACTORING_START_ID) {
                    echo "<a href='/ulab/result/card/".$deal['ID']."?protocol_id=".$idFistProtocol."'>
                            <div class='further' title='Перейти к испытаниям'></div>
                        </a>";
                } else {
                    echo "<a href='/results_isp.php?ID=".$deal['ID']."&ID_P=".$idFistProtocol."'>
                            <div class='further' title='Перейти к испытаниям'></div>
                        </a>";
                }
				
			} else {
                if ($deal['ID'] > RESULT_REFACTORING_START_ID) {
                    echo "<a href='/ulab/result/card/".$deal['ID']."'>
                            <div class='further' title='Перейти к испытаниям'></div>
                        </a>";
                } else {
                    echo "<a href='/results_isp.php?ID=".$deal['ID']."'>
                            <div class='further' title='Перейти к испытаниям'></div>
                        </a>";
                }
			}
		}?> 
	</div>	
    <h3><?= date("d.m.Y",strtotime($deal['DATE_CREATE'])) ?> | Техническое задание №<?=$_GET['ID']?> | <?= $deal['TITLE'] ?>
        | <?= $deal['COMPANY_TITLE'] ?></h3>
    <table id="tz2">
        <thead>
        <th>Наименование материала</th>
        <th>Объём работ</th>
        <th>Наименование показателя</th>
		<th>Методика</th>
		<?php if(in_array('8', $USER->GetUserGroupArray())) {?><th>Цена</th><?php }?>
		<th>ОА</th>
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
                        echo "<p><a href='/obl_acc.php?ID=".$item."'>" . $res['SPECIFICATION'] . "</p>";
                    }
                    ?>
                </td>
				<td>
                    <?php
                    foreach ($arrDatum['gost'] as $item) {
                        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                        echo "<p>" . $res['GOST'] . '-' . $res['GOST_YEAR'] . " " .$res['GOST_PUNKT'] . "</p>";
                    }
                    ?>
                </td>
				<?php if(in_array('8', $USER->GetUserGroupArray())) {?><td>
                    <?php
                    foreach ($arrDatum['price'] as $item) {
                        
                        if($item) echo "<p>" . number_format($item, 2, ',', '') . "</p>";
						else echo "<p>-</p>";
                    }
                    ?>
                </td><?php }?>
				<td>
                    <?php
                    foreach ($arrDatum['gost'] as $item) {
                        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                        echo "<p>" . $res['NUM_OA'] . " " .$res['IN_OA'] . "</p>";
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        
<!--    </table>-->
    <?php
} else {
    ?>
    <table id="tz">
    <thead>
    <th>№ ТЗ</th>
	<th>Сделка</th>
	<th>Клиент</th>
    <th>Дата создания</th>
	<th>Протокол</th>
	<th>Акт приемки (контр. лист)</th>
    <th>Функции</th>
    </thead>
	<tbody>
    <?php
    $res = $DB->Query("SELECT * FROM `ba_tz` ORDER BY `DATE_SOZD` DESC");
    while ($row = $res->Fetch()) {

        $deal = CCrmDeal::GetByID($row['ID_Z']);
        echo "<tr><td><a href='/tz_show.php?ID=" . $row['ID'] . "'>" . $row['ID'] . "</a></td>";
		echo "<td><a href='//172.16.255.2/crm/deal/details/" . $row['ID_Z'] . "/'>".$deal['TITLE']."</a></td>";
		echo "<td>".$deal['COMPANY_TITLE']."</td>";
        echo "<td>".date("d.m.Y",strtotime($row['DATE_P']))."</td>";
		echo "<td>". ($row['NUM_P'] ? $row['NUM_P']."/19" : "" )."</td>";
		echo "<td>". ($row['ACT_NUM'] ? $row['ACT_NUM']."/19" : "" )."</td>";
        echo "<td>";

       // $deal = CCrmDeal::GetByID($row['ID_Z']);
        if ($deal['STAGE_ID'] == 'PREPARATION') {
            // echo "<a href='//172.16.255.2/crm/deal/details/" . $row['ID_Z'] . "/'>Сделка</a>";
            // echo " | ";
            echo "<a href='/tz.php?EDIT=" . $row['ID'] . "'>Изменить</a>";
            echo " | ";
            echo "<a href='/tz_show.php?DEL=" . $row['ID'] . "&DEAL=" . $row['ID_Z'] . "'>Удалить</a>";
            echo " | ";
            echo "<a href='/protocol_generator/dogovor.php?ID=" . $row['ID_Z'] . "&TZ_ID=" . $row['ID'] . "'>Сформировать договор</a>";
			echo " | ";
            echo "<a href='/protocol_generator/kp.php?ID=" . $row['ID_Z'] . "&TZ_ID=" . $row['ID'] . "'>Сформировать КП</a>";
        } else {
            echo 'Формирование ТЗ завершено';
            echo " | ";
            echo "<a href='/protocol_generator/dogovor.php?ID=" . $row['ID_Z'] . "&TZ_ID=" . $row['ID'] . "'>Скачать договор</a>";
        }
        echo "</td>";
    }

}

?>
    </tbody></table><br />
	<?php if(in_array('8', $USER->GetUserGroupArray())) {?>Общая сумма: <?php if($row['PRICE']) echo number_format($row['PRICE'], 2, ',', '')." руб. "/*.((isset($row['DISCOUNT']) && !empty($row['DISCOUNT'])) ? '(С учетом скидки '.$row['DISCOUNT'].'%)' : '')*/; else echo "-";?> <?}?>

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
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>
	
	<script>
        $(document).ready(function () {
            $('#tz').DataTable({
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
				
				"columnDefs": [{ "type": "natural", "targets": 0 },{ "sType": "ruDate", "targets": 3 }
				],
				"order": [[ 0, "desc" ]],
				"colReorder": true,
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
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
