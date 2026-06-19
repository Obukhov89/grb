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
$APPLICATION->SetTitle("Отчет об использовании оборудования");
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
    <td><input type="date" class="res" name="date1" value="2020-01-01"></td>
	<td><input type="date" class="res" name="date2" value="2020-12-31"></td>
	<select class="obl" name="lab_gost" id="lab" style="float: right;">
        <option value="0" selected>Всё оборудование</option>
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
					$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage` = 1");
					$lab_name="ЛФХИ";
					break;
				case 2:
					$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage` = 2");
					$lab_name="ЛСМ";
					break;
				case 3:
					$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage` = 3");
					$lab_name="ЛФМИ";
					break;
				case 4:
					$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage` = 4");
					$lab_name="ДСЛ";
					break;
				case 5:
					$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage` = 5");
					$lab_name="ОСК";
					break;
				case 6:
					$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `place_of_installation_or_storage` != 5");
					$lab_name="Только ИЦ";
					break;
				
			}
		
        
    } else {
        $res = $DB->Query("SELECT * FROM `ba_oborud`");
			$lab_name="Всё оборудование";
    }


    
    ?>	
	<br />
	<p style="font-weight: bold;">Отчет за период с <?= $dt1?> по <?=$dt2 ?> (<?=$lab_name?>)</p>
	<br>
    <hr>
    <table id="gost-table">
	<thead>
    <!--<tr>-->
		<th>Наименование</th>
		<th>Опред. харак-ка</th>
		<th>Тип</th>
		<th>Инв. номер</th>
		<th>Зав. номер</th>
		<th>Кол-во использований</th>
		<th>Перечень заявок</th>
	</thead>
	<tbody>
    <!--</tr>-->
    <?php
	$i=0;
	$count_all=0;
	// $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'));
	// $deal_arr=array();
	// while($deal = $deals->Fetch())
	// {
		// $deal_arr[]=$deal['ID'];
	// }
	//var_dump($deal_arr);
	
	
    while ($row = $res->Fetch()) {
		$count=0;
		$req_ar = array();
		
		
			
				$resTzOb = $DB->Query("SELECT * FROM `TZ_OB_CONNECT` WHERE `ID_OB`=". $row['ID']);
				while($tzOb = $resTzOb->Fetch())
				{
				
					$tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=". $tzOb['ID_TZ'])->Fetch();
					$datetime_tz=date("Y-m-d", strtotime($tz['DATE_P_CORR']));
										
					if($datetime_tz>=$datetime1&&$datetime_tz<=$datetime2)
					{   
						$tz_idz = $tz['ID_Z'];
					
							  if($tzOb['ID_OB']==$row['ID']) 
							  {
								   $count++;
								   $req_ar[] = "<a href='/request_card.php?ID=".$tz_idz." target='_blank'>".$tz_idz."</a>";
							  }
					}
							
				}
				
		
		if($count) {
		echo "<tr>";
		echo "<td> <a href='/oborud.php?ID=" . $row['ID'] . "'>" . $row['OBJECT'] . "</a> </td>";
		echo "<td>" . $row['NAME'] . "</td>";
		echo "<td>" . $row['TYPE_OBORUD'] . "</td>";
        echo "<td>" . $row['REG_NUM'] . "</td>";
		echo "<td>" . $row['FACTORY_NUMBER'] . "</td>";
        echo "<td>" . $count . "</td>";
		echo "<td>" . implode(', ', array_unique($req_ar)) . "</td>";
		//echo "<td>" . $diff1 . "</td>";
		$i++;
		}
		$count_all += $count;
    }
	
    echo "</tbody></table><br /><b>ИТОГО за период:</b> ".$count_all;
	
}
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