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
$APPLICATION->SetTitle("График ВЛК");
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


?>
  <?php
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `IN_OA` = 1 AND `NUM_OA_NEW` > '0' AND `EXTENSION` != '1' AND `NUM_OA` > '0' AND `GOST_TYPE` != 'TU_standart' AND `GOST_TYPE` != 'TU_research' AND `GOST_TYPE` != 'TU_group'");

        $mater = $DB->Query("SELECT DISTINCT `ID`, `NAME` FROM `MATERIALS` WHERE `NAME` <> '' ORDER BY `NAME` ASC");

if (!empty($_POST['year_sel'])) {
 header("Location: //" . $_SERVER['HTTP_HOST'] . "/protocol_generator/otchet_vlk.php?YEAR=".$_POST['year_sel']);
}

if (isset($_POST['ID'])) {
    if (!empty($_POST['ID'])) {
		$res = $DB->Query("UPDATE `VLK` SET 
        `LAB_ID`=" . (int)$_POST['LAB_ID'] . ", 
        `USER_ID`=" . (int)$_POST['USER_ID'] . ",
		`OA_ID`=" . (int)$_POST['OA_ID'] . ",
		`DATE`='" . date("d.m.Y", strtotime($_POST['DATE'])) . "',
		`RESULT`=" . (($_POST['RESULT'] == 'on') ? 1 : 0) . ",
		`material` = '{$_POST['mater']}',
		`type_vlk` = {$_POST['type_vlk']}
        WHERE `ID`=" . (int)$_GET['ID']);
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    } else {
		
        $res = $DB->Query("INSERT INTO `VLK`
        (`LAB_ID`, `USER_ID`, `OA_ID`, `DATE`, `RESULT`, `material`, `type_vlk`) VALUES ('{$_POST['LAB_ID']}', '{$_POST['USER_ID']}', '{$_POST['OA_ID']}', '" . date("d.m.Y", strtotime($_POST['DATE'])) . "', '" . (($_POST['RESULT'] == 'on') ? 1 : 0) . "', '{$_POST['mater']}', {$_POST['type_vlk']})");
        if ($res->result = 1) {
            header("Location: //" . $_SERVER['HTTP_HOST']
                . "/vlk.php");
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
        $res = $DB->Query("DELETE FROM `VLK` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href='/vlk.php'>К списку</a>";
        echo "<hr>";
    } else {

        $res = $DB->Query("SELECT * FROM `VLK` WHERE `ID`=" . (int)$_GET['ID']);
        $row = $res->Fetch();
        if (!empty($row['DEAL_ID'])) {
        	$baTz = $DB->Query("SELECT `REQUEST_TITLE` FROM `ba_tz_vlk` WHERE `ID_Z`= {$row['DEAL_ID']}")->Fetch();
		}

		$deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('TYPE_ID' => '1'));
        ?>
        <a href="/vlk.php">К списку</a>
		
        <?php if ($_GET['ID'] > 0) { ?> |
            <a href="/vlk.php?ID=<?= $_GET['ID'] ?>&del=true"
               onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">Удалить</a>
        <?php }  ?> 
        <hr>
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post' id="form-vlk">
            <table id="oborud_table">
			<tbody id="oborud_body">
                <tr>
                    <td>Лаборатория</td>
                    <td><select class="gost styleGost"  name="LAB_ID">
                            <option value="0" selected disabled="">Выберите</option>
                            <?php
                            $res2 = $DB->Query("SELECT * FROM `ba_laba`");
                            while ($value = $res2->Fetch()) {
                                echo "<option ";
                                if ($row['LAB_ID'] == $value['ID']) {
                                    echo "selected";
                                }
                                echo " value='" . $value['ID'] . "'>" . $value['NAME'] . "</option>";
                            }
                            ?>
                        </select>
					</td>
				<tr>
                    <td>Ответственный</td>
                    <td class="tdGost">
					<select class="gost styleGost" name="USER_ID">
						<option value="0" selected disabled>Не указан</option>
						<?php
                        $order = array('LAST_NAME' => 'asc');
                        $filter = array("ACTIVE" => 'Y');
						$tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
						$select = CUser::GetList($order, $tmp);
/*						value="<?=$row['OA_ID']?>"*/
						$selected='';
						while ($option = $select->Fetch()) {
							$selected = (($option['ID']==$row['USER_ID']) ? 'selected' : '');
							echo "<option value='" . $option['ID'] . "'" . $selected . ">" . $option['NAME'] . ' ' . $option['LAST_NAME'] . "</option>";
						}
						?>
					</select></td>
				<tr>
					<td>Материал</td>
					<td>
						<select class="gost styleGost" name="mater" required>
                            <option value="0" selected disabled>Выберите</option>
							<?php while ($obj = $mater->Fetch()):?>
								<option value='<?=$obj['ID']?>' <?=($obj['ID'] == $row['material']) ? 'selected' : ''?>><?=$obj['NAME']?></option>
							<?php endwhile;?>
						</select>
					</td>
				</tr>
				<tr>
                    <td>Методика</td>
                    <td>
						<select class="gost styleGost" name="OA_ID" required>
                            <option value="0" selected disabled>Выберите</option>
					<?php while ($gost = $ba_gost->Fetch()):?>
						<option data-type="<?=$gost['NORM_TEXT']?>" value='<?=$gost['ID']?>' <?=($gost['ID'] == $row['OA_ID']) ? 'selected' : ''?>><?=$gost['GOST'] . ' ' . $gost['GOST_PUNKT'] . ' | ' . $gost['SPECIFICATION'] ?></option>
					<?php endwhile;?>
						</select>
					</td>
				<tr>
                   <td>Дата</td><td><input class="gost" type="date"  name="DATE" value="<?=!empty($row['DATE']) ? date('Y-m-d', strtotime($row['DATE'])) : ''?>" required></td>
				</tr>
				<tr <?=empty($row['DEAL_ID']) ? 'class="visually-hidden"' : ''?> id="anchor-req">
					<td>Заявка:</td><td><a href="/request_card_vlk.php?ID=<?=$row['DEAL_ID']?>" target="_blank"><?=$baTz['REQUEST_TITLE']?></a></td>
				</tr>
				<tr>
                    <td>Результат положительный</td>
                    <td><input type="checkbox" name="RESULT" <?= ($row['RESULT'] == 1) ? 'checked' : '' ?>></td>
                </tr>
				<tr>
					<td>Тип испытаний:</td>
					<td>
						<select class="gost styleGost" name="type_vlk">
							<option value="1" <?= $row['type_vlk'] == 1 ? 'selected' : ''?>>Привязать к заявке</option>
							<option value="2" <?= $row['type_vlk'] == 2 ? 'selected' : ''?>>Параллельные</option>
						</select>
					</td>
				</tr>
				
<!--                <tr>-->
<!--		<td width="250" height="40">-->
<!--        Заявка для формирования ТЗ-->
<!--		</td><td>-->
<!--             <select name="DEAL_ID" style="width:500px" required>-->
<!--                <option value="0" disabled selected>Выберите</option>-->
<!--                --><?php //while ($deal = $deals->Fetch()) {
//				?>
<!--                    <option data-mater="--><?//= implode(';', $deal['UF_CRM_1569151529509']) ?><!--" --><?//= ($deal['TYPE_ID']!='1') ? 'disabled' : '' ?><!-- --><?//= ($deal['ID'] == $row['DEAL_ID']) ? 'selected' : '' ?>
<!--                            value="--><?//= $deal['ID'] ?><!--">--><?//= $deal['DATE_CREATE'] ?><!-- -->
<!--                        || --><?//= $deal['TITLE'] ?><!-- || --><?//= $deal['CONTACT_FULL_NAME'] ?><!--</option>-->
<!--                --><?php //}
//
//				?>
<!--            </select>-->
<!--        -->
<!--        </td></tr>-->
                </tbody>
            </table>
            <input type="hidden" name="ID" value="<?= $row['ID'] ?>">
            <input type='submit' class="btn btn-vlk" value='Сохранить'>
			<?php if (!empty($_GET['ID'])):?>
            <input type='button' class="btn btn-vlk btn-create <?=empty($row['DEAL_ID']) ? '' : 'visually-hidden'?>" value='Создать заявку ВЛК'>
			<a class="btn btn-vlk btn-req <?=!empty($row['DEAL_ID']) ? '' : 'visually-hidden'?>" href="/request_card_vlk.php?ID=<?=$row['DEAL_ID']?>" target="_blank">Перейти к заявке</a>
			<?php endif;?>
        </form>
        <?php if ($_GET['ID'] > 0) {// var_dump($_POST['GOST_TYPE']);//var_dump($_POST['NORM_DOP']); ?>
            <hr>
            
            <?php
        }
    }
} else {// Вывод скиска ГОСТов

    global $DB;

    if (!empty($_GET['laba'])) {
		$res = $DB->Query("SELECT * FROM `VLK` WHERE `LAB_ID` = {$_GET['laba']}");
	} else {
    $res = $DB->Query("SELECT * FROM `VLK`");
	}


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
    <a href="/vlk.php?ID=0">Добавить новое задание</a> | 
	<a href="/journal_vlk.php">Перейти в журнал шифрованных проб</a>
	<select style="float:right; margin-bottom: 0" class="oborud" onchange="window.location.assign('/vlk.php?laba='+this.options[this.selectedIndex].value)">
		<option value="" selected>Все лаборатории</option>
		<?php
		$res2 = $DB->Query("SELECT * FROM `ba_laba`");
		while ($value = $res2->Fetch()) {
			echo "<option ";
			if ($_GET['laba'] == $value['ID']) {
				echo "selected";
			}
			echo " value='" . $value['ID'] . "'>" . $value['NAME'] . "</option>";
		}
		?>
	</select>
	<br>
	<br>
    <hr>
    <table id="mat_table">
	<thead>
    <!--<tr>-->
		<th>№</th>
		<th>Лаборатория</th>
		<th>Методика</th>
        <th>Ответственный</th>
		<th>Дата (месяц)</th>
		<th>№ ТЗ</th>
        <!--<th>Нормативный документ на метод испытания</th>
        <th>Нормативная документация</th>-->
        
	</thead>
	<tbody>
    <!--</tr>-->
    <?php
	$i=0;
	//$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=");
    while ($row = $res->Fetch()) {
		
		$res_deal = CCrmDeal::GetByID($row['DEAL_ID']);
		$res_oa = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=".$row['OA_ID'])->Fetch();
		$res_user = CUser::GetByID($row['USER_ID'])->Fetch();
		$res_lab = $DB->Query("SELECT * FROM `ba_laba` WHERE ID=".$row['LAB_ID'])->Fetch();
		// $soisp_name = '';
		// if ($res_user['ID']==$res_deal['ASSIGNED_BY_ID']) $soisp_name = $res_deal['ASSIGNED_BY_NAME'] .' '. $res_deal['ASSIGNED_BY_LAST_NAME'];
		// else {
		// $soispolnitel = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $row['DEAL_ID');
		// $soispolnitel = $soispolnitel['UF_CRM_1571643970']['VALUE'];
		
		// foreach($soispolnitel as $s => $soisp)
		// {
			// $user_s = CUser::GetByID($soisp)->Fetch();
			// if($user_s['ID']) == $res_user['ID'];
			// $soisp_name = $user_s['NAME'] .' '. $user_s['LAST_NAME'];
			
		// }
		// }
		//var_dump($res_deal);
//		$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=".(int)$res_deal['ID']." AND `USER_VLK`=".$res_user['ID'])->Fetch();
        echo "<tr>";
		echo "<td><a href='/vlk.php?ID=" . $row['ID'] . "'>" . $row['ID'] . "</a></td>";
		echo "<td>". $res_lab['NAME']."</td>";
		echo "<td>" . $res_oa['GOST'] .' '. $res_oa['GOST_PUNKT'] . ' | ' . $res_oa['SPECIFICATION'] . "</td>";
		echo "<td>" . $res_user['NAME'] . ' ' . $res_user['LAST_NAME'] . "</td>";
		echo "<td>" . date('m.Y', strtotime($row['DATE'])) . "</td>";
		echo "<td><a href='/tz_vlk.php?EDIT=" . $row['TZ_ID'] . "'>" . $row['TZ_ID'] . "</a></td>";
		
    }
	
    echo "</tbody></table>";
    ?>
	<table>
	<tr>
	<td><input type="button" value="Скачать отчет" onclick="window.open ('/protocol_generator/otchet_vlk.php?YEAR=' + document.getElementById ('year_sel').value, '_blank')"> за</td><td> <select id="year_sel">
	<option>2022</option>
	<option>2021</option>
	<option>2020</option>
    <option>2019</option>
	<option>2018</option>
	<option>2017</option>
	<option>2016</option>
	</select></td><td> год </td>
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
    <link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
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
    <script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>

    <script>
        $(document).ready(function () {
            $('.styleGost').select2();
        })
    </script>
	<script>
		$(document).ready(function () {
			$('.btn-create').on('click', function() {
				let dataForm = $('#form-vlk').serialize()
				$.ajax ({
					method: 'post',
					url: 'create_vlk.php',
					data: dataForm,
					success: function(data) {
						// $('.btn-create').addClass('visually-hidden')
						// $('.btn-req').removeClass('visually-hidden')
						// $('#anchor-req').removeClass('visually-hidden')
                        location.reload()
					}
				})
			})
		})
	</script>
	<script>
        $(document).ready(function () {
            $('#mat_table').DataTable({
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
				"order": [[ 0, "desc" ]],
				"colReorder": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
        });
		   function add(mater = '') {
            if (!isNaN($('.coord:last').data('id'))) {
                last_gost = $('.coord:last').data('id');
            } else {
                last_gost = 1;
            }
			console.log(last_gost);
            $("#obj_body").append("<tr data-name='row_" + (last_gost + 1) + "'>" +
                "<td data-name='coord'>     <input type='number' step ='0.000001' class='coord' name='coord[" + (last_gost + 1) + "][0]' value='" + Number(mater) + "' size='10' data-id='" + (last_gost + 1) + "'><input type='number' step ='0.000001' name='coord[" + (last_gost + 1) + "][1]'  size='10'></td>"   +
                // "<td data-name='gost_new_desc'>  </td>" +
                // "<td data-name='norm_1'>    </td>" +
                // "<td data-name='norm_2'>    </td>" +
                // "<td data-name='ed_izm'>    </td>" +
                "<td data-name='a_href'>    <a href='#' onclick='$(this).parent().parent().remove()'>Удалить</a></td>");
            
			
        }
        $(document).ready(function () {
            $("#add").click(function () {
                add();
            })


			$('select[name=LAB_ID]').on('select2:select', function () {
				let lab = $('select[name=LAB_ID]').val()
				switch ($('select[name=LAB_ID]').val()) {
					case '1':
						$('select[name=USER_ID]').val('11').trigger('change')
						var labData = [
							{id: 5593, text: 'ГОСТ 19609.1-89 Массовая доли оксида железа (III)'},
							{id: 5595, text: 'ГОСТ 19609.2-89 Массовая доля оксида титана'},
							{id: 5597, text: 'ГОСТ 19609.3-89 Массовая доля оксида алюминия'},
							{id: 5598, text: 'ГОСТ 19609.4-89 Массовая доля оксида кальция'},
							{id: 4948, text: 'ГОСТ 2642.8-2017 п.5 Массовая доля оксида магния'},
							{id: 5607, text: 'ГОСТ 19609.13-89 Потери массы при прокаливании'},
							{id: 5615, text: 'ГОСТ 2642.3-2014 п.5 Массовая доля оксида кремния'},
							{id: 5600, text: 'ГОСТ 19609.6-89 Массовая доли оксида серы (VI)'},
							{id: 5599, text: 'ГОСТ 19609.5-89 Массовая доля оксидов калия и натрия'}
						];
                        $('select[name=OA_ID]').empty()
                        $('select[name=OA_ID]').select2({
							data: labData
						});
						break
					case '2':
						$('select[name=USER_ID]').val('58').trigger('change')
						break
					case '3':
						$('select[name=USER_ID]').val('15').trigger('change')
						break
					case '4':
						$('select[name=USER_ID]').val('13').trigger('change')
                        labData = [
                            {id: 5148, text: 'ГОСТ 33143 Температура хрупкости'},
                            {id: 5147, text: 'ГОСТ 33142 Температура размягчения по кольцу и шару'},
                            {id: 5146, text: 'ГОСТ 33140 Изменение массы образца после старения'},
                            {id: 5145, text: 'ГОСТ 33136 Глубина проникания иглы'},
                            {id: 5304, text: 'ГОСТ 33138 Растяжимость'},
                            {id: 5312, text: 'ГОСТ 32763, п.9.2 Истинная плотность'},
                            {id: 5313, text: 'ГОСТ 32764, п.5.1 Средняя плотность'},
                            {id: 5419, text: 'ГОСТ 32766 Битумоёмкость'},
                            {id: 5309, text: 'ГОСТ 32719 Зерновой состав'}
                        ];
                        $('select[name=OA_ID]').empty()
                        $('select[name=OA_ID]').select2({
                            data: labData
                        });
						break
					case '5':
						$('select[name=USER_ID]').val('43').trigger('change')
						break
				}
			})

            // $("select[name=id_deal]").change(function () {
                // optionSelected = $("option:selected", this);
                // maters = optionSelected.data('mater').split(';');
                // $.each(maters, function (index, value) {
                    // if (value) {
                        // console.log("INDEX: " + index + " VALUE: " + value);
                        // add(value);
                    // }
                // });
            // })
			
            // gost(); gost_new();
        })
		
    </script>    
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
