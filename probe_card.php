<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04.04.2019
 * Time: 15:23
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Карточка пробы");
?>
  
<?php

function num2word($n, $titles)
{
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
}



if (!empty($_GET['ID'])) {
	
	$deal = CCrmDeal::GetByID((int)$_GET['ID']);

    $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID'] ." AND `ACT_NUM`=". (int)$_GET['ACT_NUM']);
    $row = $res->Fetch();

    $deal = CCrmDeal::GetByID($row['ID_Z']);
    ?>
   <!-- <a href='//172.16.255.2/crm/deal/details/<?= $deal['ID'] ?>/'>Сделка</a> | <a href="/tz_show.php">К
        списку</a> | <a href="/protocol_generator/account.php?ID=<?=$deal['ID']?>&TZ_ID=<?=$row['ID']?>">Скачать счет</a>-->
    <h3><a href='/tz_show.php?ID=<?=$row['ID']?>'>Акт приемки проб №<?=$_GET['ACT_NUM']?> от <?= date("d.m.Y",strtotime($row['DATE_ACT'])) ?></a> 
        | <?= $deal['COMPANY_TITLE'] ?> | <?= $deal['ASSIGNED_BY_NAME'] ?> <?= $deal['ASSIGNED_BY_LAST_NAME'] ?></h3>
    <table id="tz2">
        <thead>
        <th>№ п/п</th>
        <th>Тип документа</th>
		<th>Номер документа</th>
        <th>Дата изменения</th>
	<!--	<th>Методика</th>
		<th>Цена</th>-->
        </thead>
		
      
            <tr>
            <td>1</td>
			<td>Заявка</td><td> <a href='//172.16.255.2/crm/deal/details/<?= $deal['ID'] ?>/'><?= $deal['TITLE'] ?></a></td>
			<td><?= date("d.m.Y",strtotime($deal['DATE_CREATE'])) ?></td>
            </tr>
			<tr>
            <td>2</td>
			<td>Техническое задание</td><td> <a href='//172.16.255.2/tz_show.php?ID=<?= $row['ID'] ?>'><?= $row['ID'] ?></a></td>
			<td><?= date("d.m.Y",strtotime($row['DATE_SOZD'])) ?></td>
            </tr>
			<tr>
            <td>3</td>
			<td>Счет</td><td><a href="/protocol_generator/account.php?ID=<?=$deal['ID']?>&TZ_ID=<?=$row['ID']?>"><?= $deal['ID'] ?></a></td>
			<td><?= date("d.m.Y",strtotime($deal['DATE_CREATE'])) ?></td>
            </tr>
			<tr>
            <td>4</td>
			<td>Договор</td><td> <?= $deal['TITLE'] ?></td>
			<td><?= date("d.m.Y",strtotime($deal['DATE_CREATE'])) ?></td>
            </tr>
			<tr>
            <td>5</td>
			<td>Результаты испытаний</td><td> <a href='//172.16.255.2/results_isp.php?ID=<?= $deal['ID'] ?>'><?= $row['ID'] ?></a></td>
			<td><?= ($row['DATE_P_CORR'] ? date("d.m.Y", strtotime($row['DATE_P_CORR'])) : date("d.m.Y", strtotime($row['DATE_P']))) ?></td>
            </tr>
			<tr>
            <td>6</td>
			<td>Протокол</td><td><a href='//172.16.255.2/protocol_generator/protocol.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?= $row['ID'] ?>'><?= $row['NUM_P'] ?></a></td>
			<td><?= ($row['DATE_P_CORR'] ? date("d.m.Y", strtotime($row['DATE_P_CORR'])) : date("d.m.Y", strtotime($row['DATE_P']))) ?></td>
            </tr>
			<!--<tr>
            <td>7</td>
			<td>Заявка <a href='//172.16.255.2/crm/deal/details/<?= $deal['ID'] ?>/'><?= $deal['TITLE'] ?></a></td>
			<td><?= date("d.m.Y",strtotime($deal['DATE_CREATE'])) ?></td>
            </tr>
			<tr>
            <td>8</td>
			<td>Заявка <a href='//172.16.255.2/crm/deal/details/<?= $deal['ID'] ?>/'><?= $deal['TITLE'] ?></a></td>
			<td><?= date("d.m.Y",strtotime($deal['DATE_CREATE'])) ?></td>
            </tr>
			<tr>
            <td>9</td>
			<td>Заявка <a href='//172.16.255.2/crm/deal/details/<?= $deal['ID'] ?>/'><?= $deal['TITLE'] ?></a></td>
			<td><?= date("d.m.Y",strtotime($deal['DATE_CREATE'])) ?></td>
            </tr>-->
          
        
<!--    </table>-->
    <?php
} 
?>
    </tbody></table><br />
	
	Список версий протокола:
	<br /><br />
	<?php 
	$dir  = 'protocol_generator/archive/'.$row['NUM_P'].'/';
	$skip = array('.', '..');
	$files = scandir($dir);
	foreach($files as $file) {
    if(!in_array($file, $skip))
        echo "<li><a href='$dir$file'>$file</a></li> <br>";
	}
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
				"order": [[ 2, "desc" ]],
				"columnDefs": [{ "type": "num", "targets": 2 },{ "sType": "ruDate", "targets": 1 }
				],
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