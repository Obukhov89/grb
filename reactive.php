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
$APPLICATION->SetTitle("Журнал учета реактивов");
?>
<link rel="stylesheet" type="text/css" href="/ulab/assets/css/bootstrap.min.css" />
<style>
    .form-group {
        margin-bottom: 1rem;
    }
</style>
<datalist id="supply">
<?php
$res_supply = $DB->Query("SELECT * FROM `REACT_SUPPLY`");
while ($row_supply = $res_supply->Fetch()) {
	$reactId = $row_supply['id'];
	$numParty = $row_supply['number'];
	$dateCreate = $row_supply['date_create'];
	$quality = $row_supply['quality'];
	$nd = $row_supply['nd'];
	$dateTo = $row_supply['date_to'];
	$unit = !empty($row_supply['unit']) ? $row_supply['unit'] : '';

	echo "<option data-id = '". $reactId ."' data-number = '". $numParty ."' data-unit = '". $unit ."' data-dc = '". $dateCreate ."' data-quality = '". $quality ."' data-nd = '". $nd ."' data-dt = '". $dateTo ."' value = '" . $numParty . "'></option>";
}
?>
</datalist>

<?php
if (!empty($_POST['year_sel'])) {
 header("Location: //" . $_SERVER['HTTP_HOST'] . "/protocol_generator/otchet_msi.php?YEAR=".$_POST['year_sel']);
}

if (isset($_POST['ID_REACT'])) {
    if (!empty($_POST['ID_REACT'])) {
		
		$res_ost = $DB->Query("SELECT * FROM `REACTIVE_OST` WHERE `ID`=".$_POST['ID_REACT'])->Fetch();
		$ostatok = $res_ost['REACT_OST'] + $_POST['PRIKHOD']; 
		$res_ost = $DB->Query("UPDATE `REACTIVE_OST` SET `REACT_OST` =".$ostatok." WHERE `ID`=".$_POST['ID_REACT']);

		$res_supply = $DB->Query("SELECT * FROM `REACT_SUPPLY` WHERE `number` = {$_POST['NUMBER_PART']}")->Fetch();
		if (!empty($res_supply)) {
			$DB->Query("UPDATE `REACT_SUPPLY` SET `number` = {$_POST['NUMBER_PART']}, `date_create` = '{$_POST['date_create']}', `quality` = '{$_POST['quality']}', `nd` = '{$_POST['GOST']}', `date_to` = '{$_POST['date_to']}', `unit` = '{$_POST['unit']}' WHERE `number` = {$_POST['NUMBER_PART']}");
//			$DB->Query("UPDATE `supply_rem` SET `date` = ")
			$party = $res_supply['id'];
		} else {
			$DB->Query("INSERT INTO `REACT_SUPPLY` SET `number` = {$_POST['NUMBER_PART']}, `date_create` = '{$_POST['date_create']}', `quality` = '{$_POST['quality']}', `nd` = '{$_POST['GOST']}', `date_to` = '{$_POST['date_to']}', `unit` = '{$_POST['unit']}'");
			$party = $DB->LastID();
			$DB->Query("INSERT INTO `supply_rem` SET `date` = NOW(), `id_supply` = {$party}, `income` = {$_POST['PRIKHOD']}");
				}


//		$party = $_POST['GOST'] . ', Партия №' . $_POST['NUMBER_PART'] . ' от ' . $_POST['date_create'] . ', '. $_POST['quality'];
		$res = $DB->Query("INSERT INTO `REACTIVE`
        (ID_REACT, DATE_POSTUP, PRIKHOD, NUMBER_PART, OSTATOK, SROK_GODN) VALUES ('" . $_POST['ID_REACT'] . "', '" . $_POST['DATE_POSTUP'] . "', " . (float)$_POST['PRIKHOD'] . ", '" . $party . "', '" . $ostatok . "', '" . $_POST['date_to'] ."')");
        if ($res->result = 1) {
            header("Location: //" . $_SERVER['HTTP_HOST']
                . "/reactive.php?ID_REACT=".$_POST['ID_REACT']);
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    }
}



// Поиск по ID. Вывод формы для создания/редактирования
if (isset($_GET['ID'])) {
    global $DB;
	$group_val=[];
    if (!empty($_GET['del'])) {
		$res_id = $DB->Query("SELECT * FROM `REACTIVE` WHERE `ID`=".$_GET['ID'])->Fetch();
		$react_id = $res_id['ID_REACT'];
		
		$res_ost = $DB->Query("SELECT * FROM `REACTIVE_OST` WHERE `ID`=".$react_id)->Fetch();
		$ostatok = $res_ost['REACT_OST'] - $res_id['PRIKHOD']; 
		$res_ost = $DB->Query("UPDATE `REACTIVE_OST` SET `REACT_OST` =".$ostatok." WHERE `ID`=".$react_id);
		
        $res = $DB->Query("DELETE FROM `REACTIVE` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href=\"/reactive.php\">К списку</a>";
        echo "<hr>";
    } else {

       
        ?>
        <a href="/reactive.php">К списку</a>
		
     
        <form method='post' style="width: 45%" >
            <div class="form-group row">
                <label for="exampleFormControlSelect1" class="col-sm-2 col-form-label">Наименование:</label>
                <div class="col-sm-10">
                    <select type="email" class="form-control" id="exampleFormControlSelect1" name="ID_REACT" required placeholder="Наименование">
                        <option value="" selected disabled="">Выберите реактив</option>
                        <?php
                            $res = $DB->Query("SELECT * FROM `REACTIVE_OST`");
                            while ($value = $res->Fetch()) {
                                echo "<option value='" . $value['ID'] . "' ".($value['ID']==$_GET['ID'] ? 'selected' : '')." >" . $value['REACT_NAME'] ."</option>";
                            }?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputdate" class="col-sm-2 col-form-label">Дата поступления:</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="inputdate" name="DATE_POSTUP" value="<?=date("Y-m-d")?>" placeholder="Дата поступления" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputincome" class="col-sm-2 col-form-label">Приход</label>
                <div class="col-sm-10">
                    <div class="input-group mb-3">
                        <input style="min-width: 0;" type="number" class="form-control" placeholder="Количество" aria-label="Количество" aria-describedby="basic-addon2" id="inputincome" step="0.0001"  name="PRIKHOD" value="0" required>
                        <div class="input-group-append">
                            <input type="text" class="input-group-text" id="basic-addon2" name="unit" placeholder="ед.изм" required>
                        </div>
                    </div>
                </div>
            </div>
            <h2>Информация о партии:</h2>
            <div class="form-group row">
                <label for="NP" class="col-sm-2 col-form-label">№ Партии:</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="NP" multiple list="supply" name="NUMBER_PART" required placeholder="№ Партии">
                </div>
            </div>
            <div class="form-group row">
                <label for="date_create" class="col-sm-2 col-form-label">Дата изготовления:</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="date_create" name="date_create" required placeholder="Дата изготовления">
                </div>
            </div>
            <div class="form-group row">
                <label for="ND" class="col-sm-2 col-form-label">НД:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="ND" name="GOST" required placeholder="НД">
                </div>
            </div>
            <div class="form-group row">
                <label for="Qual" class="col-sm-2 col-form-label">Квалификация:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="Qual" name="quality" required placeholder="Квалификация">
                </div>
            </div>
            <div class="form-group row">
                <label for="date" class="col-sm-2 col-form-label">Срок годности:</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="date" name="date_to" required placeholder="Срок годности">
                </div>
            </div>

            <input type='submit' value='Сохранить'>
        </form>
       
            <hr>
            
            <?php
        }
    
}
elseif (isset($_GET['ID_REACT'])) 
{?>
 <a href="/reactive.php?ID=<?= $_GET['ID_REACT'] ?>">Добавить запись</a> |  <a href="/reactive_new.php">Добавить реактив</a> | <a href="/reactive_new.php?ID=<?= $_GET['ID_REACT'] ?>">Редактировать реактив</a> | <a href="/reactive.php">К списку</a>
 <br /><br />
 <!--<select onchange="top.location='/reactive.php?ID_REACT='+this.value" class="obl" name="ID_REACT" required>
                            <option value="" selected disabled="">Выберите реактив</option>
                            <?php
                            //$res = $DB->Query("SELECT * FROM `REACTIVE_OST`");
                            //while ($value = $res->Fetch()) {
                           //     echo "<option value='" . $value['ID'] . "'>" . $value['REACT_NAME'] ."</option>";
                            //}
                            ?>
                        </select>
 	-->
	<br>
	
	 <table id="mat_table">
	<thead>
    <tr>
		<th></th>
		<th>Дата поступления</th>
		<th>Приход</th>
		<th>Расход</th>
		<th>Остаток партии</th>
        <th>НД № партии квалификация</th>
		<th>Срок годности</th>
		<th></th>
	</tr>
	<tr>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th><input type="text" name="party_filter" id="party_filter" style="margin:0px" value=""></th>
		<th></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
    <!--</tr>-->
    <?php
	$i=0;

	$res = $DB->Query("SELECT * FROM `REACTIVE` WHERE `ID_REACT`=".$_GET['ID_REACT']);
    while ($row = $res->Fetch()) {
    	$date = new DateTime($row['SROK_GODN']);
		$dateEnd = clone $date;
    	$date->modify('-1 month');
    	$now = new DateTime;
    	echo $dateEnd->format('d.m.Y');
		if ($now < $date) {
			$color = '#d1ffca';
		} elseif ($now >= $date && $now < $dateEnd) {
			$color = '#ffa';
		} elseif ($now >= $dateEnd) {
			$color = '#fbb';
		}

//    	if (strtotime($now) >= strtotime($date))
    	if ($row['ID'] > '3184') {
			$supply = $DB->Query("SELECT * FROM `REACT_SUPPLY` WHERE `NUMBER_PART` = rs.`id`")->Fetch();
							echo "<tr>";
							echo "<td style='background-color: " . $color . "'></td>";
							echo "<td>" . date("d.m.Y", strtotime($row['DATE_POSTUP'])). "</td>";
							echo "<td>" . $row['OSTATOK'] . "</td>";
							echo "<td>". $row['NUMBER_PART'] ."</td>";
							echo "<td><a href='/supply.php?ID=" . $row['NUMBER_PART'] . "'> Партия №" . $row['number'] ." от " . date('d.m.Y', strtotime($row['date_create'])) . ", " . $row['nd'] . ", " . $row['quality'] . "</a></td>";
							echo "<td>". date("d.m.Y", strtotime($row['SROK_GODN'])) ."</td>";
							echo "<td><a href='/reactive.php?ID=".$row['ID']."&del=1'>Удалить</a></td>";
							echo "</tr>";
    	} else {
			echo "<tr>";
			echo "<td style='background-color: " . $color . "'></td>";
			echo "<td>" . date("d.m.Y", strtotime($row['DATE_POSTUP'])). "</td>";
			echo "<td>" . ($row['PRIKHOD']>0 ? $row['PRIKHOD'] : '') . "</td>";
			echo "<td>". ($row['PRIKHOD']<0 ? $row['PRIKHOD'] : '') ."</td>";
			echo "<td>" . $row['OSTATOK'] . "</td>";
			echo "<td>". $row['NUMBER_PART'] ."</td>";
			echo "<td>". date("d.m.Y", strtotime($row['SROK_GODN'])) ."</td>";
			echo "<td><a href='/reactive.php?ID=".$row['ID']."&del=1'>Удалить</a></td>";
		}
    }?>
 	 </tbody>
 </table>
	<!--<table>
	<tr>
	<td><input type="button" value="Скачать журнал" onclick="window.open ('/protocol_generator/otchet_msi.php?YEAR=' + document.getElementById ('year_sel').value, '_blank')"> за</td><td> <select id="year_sel">
	<option>2019</option>
	<option>2018</option>
	<option>2017</option>
	<option>2016</option>
	</select></td><td> год </td>
	</table>-->
	<?php
//}


} else {// Вывод скиска ГОСТов

    global $DB;
	
    
    $res = $DB->Query("SELECT * FROM `REACTIVE`");
    


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    <a href="/reactive.php?ID=0">Добавить запись</a> | <a href="/reactive_new.php">Добавить реактив</a> 
	
	<br>
	<br>
    <hr>

					
    <table id='oborud'>
	<thead>
	<tr>
		<th>№</th>
		<th>Наименование реактива</th>
		<th>Остаток</th>
		<th><select class="laboratories">
				<option class="laboratory" value="" <?= empty($_GET['lab']) ? 'selected' : '' ?>>Лаборатории</option>
				<option class="laboratory" value="1"
					<?= !empty($_GET['lab']) &&  $_GET['lab'] == 1 ? 'selected' : '' ?>>
					ЛФХИ
				</option>
				<option class="laboratory" value="4"
					<?= !empty($_GET['lab']) &&  $_GET['lab'] == 4 ? 'selected' : '' ?>>
					ДСЛ
				</option>
				<option class="laboratory" value="3"
					<?= !empty($_GET['lab']) &&  $_GET['lab'] == 3 ? 'selected' : '' ?>>
					ЛФМИ
				</option>
				<option class="laboratory" value="2"
					<?= !empty($_GET['lab']) &&  $_GET['lab'] == 2 ? 'selected' : '' ?>>
					ЛСМ
				</option>
				<option class="laboratory" value="5"
					<?= !empty($_GET['lab']) &&  $_GET['lab'] == 5 ? 'selected' : '' ?>>
					ОСК
				</option>
			</select>
		</th>
	</tr>
	<tr>
		<th></th>
		<th><input type="text" name="react_filter" id="react_filter" style="margin:0px" value=""></th>
		<th></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
		$i=1; 
		$lab_name='';
		$res = $DB->Query("SELECT * FROM `REACTIVE_OST` ORDER BY `REACT_NAME` ASC");
		while ($row = $res->Fetch())
		{
			if (!empty($_GET['lab']) && $row['ID_LAB'] != $_GET['lab']) {
				continue;
			}
			switch ($row['ID_LAB']) {
				case 1:
					$lab_name="ЛФХИ";
					break;
				case 2:
					$lab_name="ЛСМ";
					break;
				case 3:
					$lab_name="ЛФМИ";
					break;
				case 4:
					$lab_name="ДСЛ";
					break;
				case 5:
					$lab_name="ОСК";
					break;
		
			}?>
	<tr>
		<td><?=$i?></td>
		<td><a href='/reactive.php?ID_REACT=<?=$row['ID']?>'><?=$row['REACT_NAME']?></a></td>
		<td><?=$row['REACT_OST']?></td>
		<td><?=$lab_name?></td>
	</tr>
	<?php
			$i++;
		}
	?>
	</tbody>
	</table>
	<?php
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
	
	<script>
        $(document).ready(function () {
            let table_react = $('#mat_table').DataTable({
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
				"columnDefs": [{ "sType": "ruDate", "targets": 0 }],
				"order": [[ 0, "asc" ]],
				"colReorder": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });

			$('#party_filter').on('keyup', function () {
				table_react
					.columns( 5 )
					.search( this.value )
					.draw();
			} );

			function col_sum(table, col) {
				let sum = 0, tr = table.find("tr"); // Все tr внутри table.
				for (let i = 0; i < tr.length - 1; i++) {
					let td = $(tr[i]).find("td"); // Все td внутри строки; th не учитываем.
					if (!td.length || !td[col - 1]) continue;
					// В tr нет td, или есть, но такого столбика не существует.

					sum += Number( td[col - 1].textContent ) || 0;
					// Если в textContent не число, Number вернет NaN, возьмется 0 вместо него.
				}

				return sum;
			}

			$('.count-3').text(col_sum($('#mat_table'), 3))
			$('.count-4').text(col_sum($('#mat_table'), 4))
			$('.count-5').text(col_sum($('#mat_table'), 5))

            let table = $('#oborud').DataTable({
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
                "columnDefs": [{ "sType": "ruDate", "targets": 0 }],
                "order": [[ 0, "asc" ]],
                "colReorder": true,
                "dom": 'Blfrtip',
                "buttons": [ 'copy', 'excel', 'colvis', 'print' ]

            });

            $('#react_filter').on('keyup', function () {
                table
                    .columns( 1 )
                    .search( this.value )
                    .draw();
            } );

            $('.laboratories').on('change', function() {
                //объект отражающий URL
                var url = new URL(window.location.href);
                //Наименование хоста
                var hostName = window.location.hostname;
                //ID выбранной лаборатории
                var selectedLaboratoryId = $(this).val();

                //Если ID лаборатории существует то добавляем в url параметр ID лаборатории, иначе удаляем параметр ID из url
                if (selectedLaboratoryId) {
                    url.searchParams.set('lab', $(this).val());
                } else {
                    url.searchParams.delete('lab');
                }

                //Перезагружаем страницу
                if(hostName === '172.16.255.2') {
                    window.location.href = url.href;
                }
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

		$("input[name=NUMBER_PART]").on("change", function() {

			num = $(this).val()
			// arr_inn=arr[2].slice(4);
			party_id = $('#supply option[data-number='+num+']').data('id');
			party_number = $('#supply option[data-number='+num+']').data('number');
			party_dc = $('#supply option[data-number='+num+']').data('dc');
			party_quality = $('#supply option[data-number='+num+']').data('quality');
			party_nd = $('#supply option[data-number='+num+']').data('nd');
			party_dateTo = $('#supply option[data-number='+num+']').data('dt');
			party_unit = $('#supply option[data-number='+num+']').data('unit');
			console.log(party_unit)

			$("input[name=date_create]").val(party_dc)
			$("input[name=GOST]").val(party_nd)
			$("input[name=quality]").val(party_quality)
			$("input[name=date_to]").val(party_dateTo)
			$("input[name=unit]").val(party_unit)

			// requisiteId= $('#clients option[data-id='+arr_inn+']').data('requisite');
			// client_inn= $('#clients option[data-id='+arr_inn+']').data('inn');
			// client_ogrn= $('#clients option[data-id='+arr_inn+']').data('ogrn');
			// client_addr= $('#clients option[data-id='+arr_inn+']').data('addr');
			// client_name= $('#clients option[data-id='+arr_inn+']').data('name');
			// client_phone= $('#clients option[data-id='+arr_inn+']').data('phone');
			// client_email= $('#clients option[data-id='+arr_inn+']').data('email');
			//
			// client_kpp = $('#clients option[data-id='+arr_inn+']').data('kpp');
			// client_position = $('#clients option[data-id='+arr_inn+']').data('position');
			// client_genitive_position = $('#clients option[data-id='+arr_inn+']').data('position');
			// client_company = $('#clients option[data-id='+arr_inn+']').data('company');
			// client_director = $('#clients option[data-id='+arr_inn+']').data('director');
		});
    </script>    
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
