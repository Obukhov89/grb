<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10.04.2019
 * Time: 15:36
 */


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал счетов");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
?>

  
<?php
    $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'));
	
	// $order = array('sort' => 'asc');
	// $tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
	// $rsUsers = CUser::GetList($order, $tmp)->Fetch();
	
	// var_dump($rsUsers);
    ?>
    <table id="dogovor">
	<thead>
		<th></th>
        <th>№</th>
		<th>Дата</th>
		<th>Сумма</th>
		<!--<th>Дата договора</th>-->
		<th>Клиент ID</th>
		<th>Наименование материала</th>
		<!--<th>Шифр образца (пробы)</th>-->
		<th>Ответственный</th>
		<th>Заявка</th>
	</thead>
	<tbody>
        <?php 
		//$res_probe = $DB->Query("SELECT * FROM `ba_probes` ORDER BY `ID`")->Fetch();
		$res_dog = $DB->Query("SELECT * FROM `INVOICE`");
		$color=''; $c=0; $title='';
		while ($res_dogovor = $res_dog->Fetch()) {
		if($res_dogovor['TZ_ID'])
		{
        $res_tz   = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=".$res_dogovor['TZ_ID'])->Fetch();   
		
		$deal = CCrmDeal::GetByID($res_tz['ID_Z']);	
		$id_stage = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2');
		if(in_array($deal['STAGE_ID'], $id_stage)&&$res_tz['PRICE']&&!$res_tz['OPLATA'])
		{
			$color='red';
			$c=0;
			$title='Счет не оплачен';
		}
		elseif(in_array($deal['STAGE_ID'], $id_stage)&&$res_tz['PRICE']&&$res_tz['OPLATA']&&$res_tz['PRICE']>$res_tz['OPLATA'])
		{
			$color='#FFC0CB';
			$c=1;
			$title='Счет оплачен не полностью';
		}
		elseif(in_array($deal['STAGE_ID'], $id_stage)&&$res_tz['PRICE']&&$res_tz['OPLATA']&&$res_tz['PRICE']==$res_tz['OPLATA'])
		{
			$color='green';
			$c=2;
			$title='Счет оплачен полностью';
		}
		else
		{
			$c=3;
		}
		
		
           // var_dump($deal);
		   $num_dog ="Счет №".$res_dogovor['ID']." от ".date('d.m.Y', strtotime($res_dogovor['DATE']));
		   $deal_fields = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
			$mater1 = $deal_fields['UF_CRM_1569151529509']['VALUE'];
			$mater = $deal_fields['UF_CRM_1576411922643']['VALUE'];
			// echo "<pre />";
			// var_dump($deal_fields);
	
			if($c!=3){
			?>
			
            <tr>
				<td style="background-color: <?=$color?>; color: <?=$color?>" title="<?=$title?>"><?= $c ?></td>
				<td><?php 
					
						$dir  = 'protocol_generator/archive_dog/'.$deal['ID'].'/';
						echo "<a href='".$dir.$res_dogovor['ACTUAL_VER'].".docx'>".$res_dogovor['ID']."</a>";
						
					
				?></td>
				<td><?= date('d.m.Y', strtotime($res_dogovor['DATE'])) ?></td>
				<td><?= $res_tz['PRICE'] ?></td>
				<td style='font-weight: bold'><?= 'ID '.$deal['COMPANY_ID'] ?></td>
                <td><?= $mater ? $mater : implode(', ', $mater1) ?></td>
				<td><?= getInitials($deal['ASSIGNED_BY_NAME']) ?> <?= $deal['ASSIGNED_BY_LAST_NAME'] ?> </td>
				
				<td><?php 
		echo  " <a href='//172.16.255.2/request_card_ID.php?ID=". $deal['ID'] . "'>Перейти</a> "; ;
	 ?>
                   
                </td>
            </tr>
			<?php }
		}
	} ?>
    </tbody></table>

    <?php

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
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>
	
	<script>
        $(document).ready(function () {
            $('#dogovor').DataTable({
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
				"columnDefs": [{ "type": "num", "targets": 0 },{ "type": "natural-nohtml", "targets": 1 },{ "sType": "ruDate", "targets": 2 }],
				"order": [[ 1, "desc" ]],
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
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>