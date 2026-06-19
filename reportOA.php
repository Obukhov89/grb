<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 25.03.2019
 * Time: 12:35
 */

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("Отчет об использовании области аккредитации");
?>
<?php
function checkbox_verify($_name)
// Выполняет: проверку checkbox
{
$result=0;

if (isset($_REQUEST[$_name]))
{ if ($_REQUEST[$_name]=='on') {$result=1;}
}
return $result;
}

$norm_dop=[];
$val_dop=[];
?>
<form id="form_report" method="post">
	<table>
	<tr>
    <td><input type="date" class="res" name="date1" value="2021-01-01"></td>
	<td><input type="date" class="res" name="date2" value="2021-12-31"></td>
	<select class="obl" name="lab_gost" id="lab" style="float: right;">
        <option value="0" selected>Все методики</option>
		<option value="10" >Только область аккредитации</option>
		<option value="11" >Вне области аккредитации</option>
        <?php
        $select = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID` != '6'");
        while ($option = $select->Fetch()) {
            echo "<option value='" . $option['ID'] . "'>" . $option['NAME'] . "</option>";
        }
        ?>
    </select>
	<td><input type="submit" value="Сформировать" style="margin:0px;"></td>
  </table>
</form>


<?php



// Поиск по ID. Вывод формы для создания/редактирования
if (!empty($_POST['date1'])&&!empty($_POST['date2'])) {
   

    global $DB;
	$date1= $_POST['date1'];
	$date2= $_POST['date2'];
	$dt1 = date("d.m.Y", strtotime($date1));
	$dt2 = date("d.m.Y", strtotime($date2));
	$datetime1=date("Y-m-d", strtotime($date1));
	$datetime2=date("Y-m-d", strtotime($date2));
	$lab_name='';
	
	//echo $diff;
    if (!empty($_POST['lab_gost']) && ($_POST['lab_gost'] != 0)) {
		switch ($_POST['lab_gost']) {
				case 1:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `LFHI` = 1");
					$lab_name="ЛФХИ";
					break;
				case 2:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `LSM` = 1");
					$lab_name="ЛСМ";
					break;
				case 3:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `LFMI` = 1");
					$lab_name="ЛФМИ";
					break;
				case 4:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `DSL` = 1");
					$lab_name="ДСЛ";
					break;
				case 5:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `OSK` = 1");
					$lab_name="ОСК";
					break;
				case 6:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `OSK` = 0");
					$lab_name="Только ИЦ";
					break;
				case 10:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `IN_OA` = 1");
					$lab_name="Только область аккредитации";
					break;
				case 11:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `IN_OA` = 0");
					$lab_name="Только методики вне области";
					break;
			}
		
        
    } else {
        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE 1");
			$lab_name="Все методики";
    }


    
    ?>	
	<br />
	<p style="font-weight: bold;">Отчет за период с <?= $dt1?> по <?=$dt2 ?> (<?=$lab_name?>)</p>
	<br>
    <hr>
    <table id="gost-table">
	<thead>
    <!--<tr>-->
		<th>Нормативный документ</th>
		<th>Наименование ГОСТ</th>
		<th>Пункт ГОСТ</th>
		<th>Объект ГОСТ</th>
		<th>Опред. харак-ка</th>
		<th>Кол-во использований (по пробам)</th>
		<th>План ВЛК на сл. год</th>
		<th>Перечень заявок</th>
		<th>В области?</th>
	</thead>
	<tbody>
    <!--</tr>-->
    <?php
	$i=0;
	$count_all=0;
	$countAllProbes=0;
	// $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'));
	 $deal_arr=array();
	// while($deal = $deals->Fetch())
	// {
		// $deal_arr[]=$deal['ID'];
	// }
	//var_dump($deal_arr);
	$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `DATE_P_CORR`>='".$datetime1."' AND `DATE_P_CORR`<='".$datetime2."' ORDER BY `ID` DESC"); //WHERE `DATE_P_CORR`>=".$datetime1." AND `DATE_P_CORR`<=".$datetime2." 
	while($tz1 = $res_tz->Fetch())
			{
				$deal_arr[] = $tz1;
			}
    while ($row = $res->Fetch()) {
		$count=0;
		$countProbes=0;
		$req_ar = array();
		//$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `DATE_P_CORR`>='".$datetime1."' AND `DATE_P_CORR`<='".$datetime2."' ORDER BY `ID` DESC"); //WHERE `DATE_P_CORR`>=".$datetime1." AND `DATE_P_CORR`<=".$datetime2." 
			foreach($deal_arr as $tz)
			{
				
				$datetime_tz=date("Y-m-d", strtotime($tz['DATE_P_CORR']));
				$arrReqName = explode('№', $tz['REQUEST_TITLE']);
				$arrReqName2 = explode('/', $arrReqName[1]);
				
				if ($tz['REQUEST_TITLE']) {
					
					if($datetime_tz>=$datetime1&&$datetime_tz<=$datetime2)
					{   
						$tz_idz = $tz['ID_Z'];
						$tz_actnum = $tz['ACT_NUM'];
						//$req_ar =$req_ar.", <a href='/request_card.php?ID=".$tz_idz."&ACT_NUM=".$tz_actnum."'>".$tz_idz."</a>";
						$TZ1 = unserialize($tz['TZ']);
						for($j=1; $j<=count($TZ1); $j++)
						{
							
							for($l=0; $l<=count($TZ1[$j]['gost']); $l++)
							{
							   if($TZ1[$j]['gost'][$l]==$row['ID']) {
								   $count++;
								   $countProbes = $countProbes+(int)$TZ1[$j]['obiem'];
								   $req_ar[] = "<a href='/request_card.php?ID=".$tz_idz."&ACT_NUM=".$tz_actnum."' target='_blank'>".$arrReqName2[0]."</a>";
							   }
							   
							}
						}
					}
				//var_dump($TZ1);
				}
			}
		//var_dump($res_ob);
		$changeValue = 0;
		if($countProbes >= 2 && $countProbes <= 25)
			$changeValue = 2;
		elseif($countProbes >= 26 && $countProbes <= 50)
			$changeValue = 3;
		elseif($countProbes >= 51 && $countProbes <= 90)
			$changeValue = 5;
		elseif($countProbes >= 91 && $countProbes <= 150)
			$changeValue = 8;
		elseif($countProbes >= 151 && $countProbes <= 280)
			$changeValue = 13;
		elseif($countProbes >= 281 && $countProbes <= 500)
			$changeValue = 20;
		elseif($countProbes >= 501 && $countProbes <= 1200)
			$changeValue = 32;
		
		if($count) {
		echo "<tr>";
		echo "<td> <a href='/obl_acc.php?ID=" . $row['ID'] . "'>" . $row['GOST'] . "</a> </td>";
		echo "<td>" . $row['NAME_GOST'] . "</td>";
		echo "<td>" . $row['GOST_PUNKT'] . "</td>";
        echo "<td>" . $row['GOST_OBJECT'] . "</td>";
		echo "<td>" . $row['SPECIFICATION'] . "</td>";
        echo "<td>" . $count . " (".$countProbes. ")</td>";
		echo "<td>" . ($changeValue ? $changeValue : '-' ) ."</td>";
		echo "<td>" . implode(', ', array_unique($req_ar)) . "</td>";
		echo "<td>" . ($row['IN_OA'] ? 'OA' : '' ) . "</td>";
		$i++;
		}
		$count_all += $count;
		$countAllProbes += $countProbes;
    }
	
    echo "</tbody></table><br /><b>ИТОГО за период:</b> ".$count_all."<br /><b>ИТОГО за период по пробам:</b> ".$countAllProbes;
	


	
} else {}// Вывод скиска ГОСТов
?>

	<script>
        var s = document.getElementById("second");
        var ids = document.getElementById("ids_oborud");
        document.getElementById("first").onchange = function () {
            s.options[s.options.length] = new Option(this.options[this.selectedIndex].text, this.value);
            ids.value = JSON.stringify(Array.prototype.slice.call(JSON.parse(ids.value)).concat(this.value));
        }

        s.onchange = function () {
            arr = Array.prototype.slice.call(JSON.parse(ids.value));
            var index = arr.indexOf(this.value);
            if (index !== -1) arr.splice(index, 1);
            ids.value = JSON.stringify(arr);

            s.removeChild(s.options[s.selectedIndex]);

        }
    </script>    
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
	
	<script>
        $(document).ready(function () {
            $('#gost-table').DataTable({
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
				"columnDefs": [
				{
				  "targets": 1,
				  "render": $.fn.dataTable.render.ellipsis( 60, true )
				},
				{
				  "targets": 3,
				  "render": $.fn.dataTable.render.ellipsis( 60, true )
				}
				],
				"iDisplayLength": 50,
				"order": [[ 4, "desc" ]],
				"colReorder": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
        });
		
		
    </script>    
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>