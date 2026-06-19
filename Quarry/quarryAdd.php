<?php
require_once ($_SERVER['DOCUMENT_ROOT']) . '/bitrix/header.php';

$APPLICATION->SetTitle('Новый карьер');
include $_SERVER['DOCUMENT_ROOT'] . '/include/function.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php';

$db_conn = new CDB();

// ? quarry, LAT, LNG, rock_porod[], TYPE_ROCK[], CAR_ACCESS
// !  Серверная обработка начало ===========================
// ! вставка нового карьера
$quarryPost = (!empty($_REQUEST['quarry']) ? $_REQUEST['quarry'] : '');
$LATPost = (!empty($_REQUEST['LAT']) ? $_REQUEST['LAT'] : '');
$LNGPost = (!empty($_REQUEST['LNG']) ? $_REQUEST['LNG'] : '');
$rock_porodPost = (!empty($_REQUEST['rock_porod']) ? $_REQUEST['rock_porod'] : '');
$type_rockPost = (!empty($_REQUEST['TYPE_ROCK']) ? $_REQUEST['TYPE_ROCK'] : '');
$car_accessPost = (!empty($_REQUEST['CAR_ACCESS']) ? $_REQUEST['CAR_ACCESS'] : '');

$quarryEdit = (!empty($_REQUEST['quarry']) ? $_REQUEST['quarry'] : '');
$LATEdit = (!empty($_REQUEST['LAT']) ? $_REQUEST['LAT'] : '');
$LNGEdit = (!empty($_REQUEST['LNG']) ? $_REQUEST['LNG'] : '');
$rock_porodEdit = (!empty($_REQUEST['rock_porod']) ? $_REQUEST['rock_porod'] : '');
$type_rockEdit = (!empty($_REQUEST['TYPE_ROCK']) ? $_REQUEST['TYPE_ROCK'] : '');
$car_accessEdit = (!empty($_REQUEST['CAR_ACCESS']) ? $_REQUEST['CAR_ACCESS'] : '');

if ($quarryEdit != ''){
  if(!empty($_GET['EDIT'])){

//    $edit = $db_conn->queryExec("UPDATE `Quarry` SET `NAME` = {?}, `CAR_ACCESS` = {?}, `LAT` = {?}, `LNG` = {?} WHERE `ID` = {?}",array($quarryEdit,$car_accessEdit,$LATEdit,$LNGEdit,$_GET['EDIT']));
    $edit = $DB->Query("UPDATE `Quarry` SET `NAME` = '{$quarryEdit}', `CAR_ACCESS` = '{$car_accessEdit}', `LAT` = '{$LATEdit}', `LNG` = '{$LNGEdit}' WHERE `ID` = {$_GET['EDIT']}");

    $rock_list = $db_conn->select("SELECT * FROM RockPorod WHERE Quarry_ID = {?}", array($_GET['EDIT']));

    $rockPorodLenght = count($rock_porodEdit);
    $rockSqlLenght = count ($rock_list);
    $diff = abs($rockPorodLenght-$rockSqlLenght);

    if($rockPorodLenght < $rockSqlLenght){
      foreach ($rock_list as $key => $value) {
        if(!in_array($value['ID'], $rock_porodEdit)){
          $valID = (int)$value['ID'];
          $dellSql = $db_conn->queryExec("DELETE from `RockPorod` WHERE `ID` = {?}", [$valID]);
        }
      }
    }

    if($rockPorodLenght > $rockSqlLenght){
      foreach ($rock_porodEdit as $key => $value) {

        if(( array_search($value, array_column($rock_list, 'ID')) === false )){
            $sql_query = "INSERT INTO `RockPorod`(`QUARRY_ID`,`ROCK_POROD_NAME`,`TYPE_ROCK_ID`) VALUES ({?}, {?}, {?})";
            if(is_numeric($value)) {
                $rock_l = $db_conn->select("SELECT * FROM RockPorod WHERE ID = {?}", array((int)$value));
                $sql_arr = array($_GET['EDIT'], $rock_l[0]['ROCK_POROD_NAME'], $type_rockEdit[$key]);
            }else{
                $sql_arr = array($_GET['EDIT'], $value, $type_rockEdit[$key]);
            }
            $upSql = $db_conn->queryExec($sql_query, $sql_arr);
        }
      }
    }

    if($rockPorodLenght == $rockSqlLenght){
        $rock_list1 = $db_conn->select("SELECT * FROM RockPorod WHERE Quarry_ID = {?}", array($_GET['EDIT']));
        foreach ($rock_list1 as $key => $value){
            if(is_numeric($rock_porodEdit[$key])){
                $search = $db_conn->select("SELECT * FROM RockPorod WHERE `ID`= $rock_porodEdit[$key]");
//                $updSql = $db_conn->queryExec('UPDATE `RockPorod` SET `ROCK_POROD_NAME`= {?},`TYPE_ROCK_ID`={?} WHERE `ID`= {?}', array($search[0]['ROCK_POROD_NAME'], $type_rockEdit[$key], $value['ID']));
                $updSql = $DB->query("UPDATE `RockPorod` SET `ROCK_POROD_NAME`= '{$search[0]['ROCK_POROD_NAME']}',`TYPE_ROCK_ID`='{$type_rockEdit[$key]}' WHERE `ID`= {$value['ID']}");
            }
            else{
                $sql_query = "INSERT INTO `RockPorod`(`QUARRY_ID`,`ROCK_POROD_NAME`,`TYPE_ROCK_ID`) VALUES ({?}, {?}, {?})";
                $sqlArr = [$_GET['EDIT'],$rock_porodEdit[$key],$type_rockEdit[$key]];
                $sqlDel= $db_conn->queryExec($sql_query,$sqlArr);
                $dellSql = $db_conn->queryExec("DELETE  FROM `RockPorod` WHERE `ID`={?}", array($value['ID']));
            }
        }
    }

  }else{
    $sel_id = $db_conn->select("SHOW TABLE STATUS LIKE 'Quarry'");
    $id_quarry = $sel_id[0]['Auto_increment'];

    // вставка в карьеры
    $car_accessPost = str_replace("'", "\\'", $car_accessPost);
	$DB->Query("INSERT INTO Quarry SET NAME = '{$quarryPost}',
	          	CAR_ACCESS = '{$car_accessPost}',
                LAT = '{$LATPost}',
                LNG = '{$LNGPost}',
                ROCK_POROD_ID = {$id_quarry}
	          	");

    $sqlTot1 = "INSERT INTO `Quarry`(`NAME`, `CAR_ACCESS`, `LAT`, `LNG`, `ROCK_POROD_ID`) VALUES ({?}, {?}, {?}, {?}, {?})";
    $quarry_arr = array($quarryPost, $car_accessPost, $LATPost, $LNGPost, $id_quarry);
//    echo "<pre>";
//	print_r($quarry_arr);
//    echo "</pre>";
//    die();
//    $insertRockPorod = $db_conn->queryExec($sqlTot1, $quarry_arr);

    $sqlTot = "INSERT INTO RockPorod (QUARRY_ID, ROCK_POROD_NAME, TYPE_ROCK_ID) VALUES({?}, {?}, {?})";

    if(is_array($rock_porodPost) && is_array($type_rockPost)){

      $countPost = count($rock_porodPost);
		$rockPorodData = [];
      for($i = 0; $i < $countPost; $i++) {

        if(!is_numeric($rock_porodPost[$i])){

          $sel_all_typeRock = $db_conn->select("SELECT * FROM TypeRock");
          if(in_array($type_rockPost[$i], $sel_all_typeRock)){
            $rockPorod_arr = array($id_quarry, $rock_porodPost[$i], $type_rockPost[$i]);
          }else{
            // вставляем тип породы
            $ins_type_rock = $db_conn->queryExec("INSERT INTO TypeRock (TYPE_ROCK_NAME) VALUES({?})", Array($type_rockPost[$i]));

            $last_type_rock_id_res = $db_conn->select('SELECT MAX(ID) from TypeRock');
            $last_type_rock_id = $last_type_rock_id_res[0]['MAX(ID)'];
            $rockPorod_arr = array($id_quarry, $rock_porodPost[$i], $last_type_rock_id);

			  $rockPorodData = ['QUARRY_ID' => $id_quarry,
				  'ROCK_POROD_NAME' => "'{$rock_porodPost[$i]}'",
				  'TYPE_ROCK_ID' => $last_type_rock_id,
			  ];
          }
          // вставляем сначала в породы
        }else{
          // запишем все данные из выбранной породы и вставим к новой породе
          $selRockPorodId = $db_conn->select("SELECT * FROM RockPorod WHERE ID = {?}", array($rock_porodPost[$i], $type_rockPost[$i]));
          // и вставим с новым Quarry
			$selRockPorodIdA = $selRockPorodId[0]['ROCK_POROD_NAME'];
          $rockPorod_arr = array($id_quarry, $selRockPorodIdA, $type_rockPost[$i]);

          $rockPorodData = ['QUARRY_ID' => $id_quarry,
			  				'ROCK_POROD_NAME' => "'{$selRockPorodIdA}'",
			  				'TYPE_ROCK_ID' => $type_rockPost[$i],
			  ];
        }
//        $insertRockPorod = $db_conn->queryExec($sqlTot, $rockPorod_arr);
		  $DB->Insert("RockPorod", $rockPorodData);
      }
    }
  }
};
// !  Серверная обработка конец ==========================
?>

<?
// подстановка значений в форму
$getQuarry = $db_conn->select("SELECT * FROM `Quarry` WHERE `ID`={?}", array($_GET['EDIT']));
$getMaterial = $db_conn-> select("SELECT * FROM `RockPorod` WHERE `Quarry_ID` = {?}", array($getQuarry[0]['ROCK_POROD_ID']));
?>

<form method="post" enctype="multipart/form-data">
  <table id="tz">
    <tbody>
      <tr><td><h2>Добавление/редактирование карьера</h2></td></tr>
      <tr>
        <td>Имя карьера</td>
        <td>
          <datalist id="quarryList">
            <?
                $res = $db_conn->select("SELECT * FROM `Quarry` WHERE `is_active` = 1");
                foreach ($res as $key => $value) {
                    echo "<option>{$value['NAME']}</option>";
                }
            ?>
          </datalist>
          <input name="quarry" list="quarryList" type="text" placeholder="Выберите или напишите новый карьер" value="<?= ((!empty($getQuarry[0]['NAME'])) ? $getQuarry[0]['NAME'] : '') ?>" style="width: -webkit-fill-available;">
        </td>
      </tr>

      <tr>
        <td>Координаты карьера</td>
        <td>
          <div class="name_quarry" style="margin-bottom: 5px;">
            <label id="tr_label">
              <div class="helper">Вводите в формате **.****** и т.д</div>
              Долгота: <input class="" type="text" name="LAT" style="width: 223px; margin-bottom: 5px;" required value="<?= ((!empty($getQuarry[0]['LAT'])) ? $getQuarry[0]['LAT'] : '') ?>"><br>
              Ширина: <input class="" type="text" name="LNG" style="width: 223px;" required value="<?= ((!empty($getQuarry[0]['LNG'])) ? $getQuarry[0]['LNG'] : '') ?>">
            </label>
          </div>
        </td>
        <!-- <td class="btn-method-add-del" style='display: table-cell;'>
            <input style='margin-bottom:5px; width: 32px;' type='button' class='add' value='+'>
        </td> -->
      </tr>

      <?
        $index=0;
        if(empty($getMaterial)){
            ?>
            <tr>
                <td>Материалы карьера</td>
                <td>
                    <div class="porods" style="display: inline-block;">
                        Название породы:<br>
                        <select class="obl2" name="rock_porod[]" style="margin-bottom: 5px;" required >
                            <?
                            $rock_porod_list = $db_conn->select("SELECT * FROM RockPorod");
                            echo("<option selected value='0' >Выберите материал породы</option>");

                            foreach($rock_porod_list as $key => $value) {
                                echo("<option value={$value['ID']}>{$value['ROCK_POROD_NAME']}</option>");
                            }
                            ?>
                        </select> <br>
                        Тип породы:<br>
                        <select name="TYPE_ROCK[]" required>
                            <?
                            $type_rock_list = $db_conn->select("SELECT * FROM TypeRock");

                            echo("<option selected value='0' >Выберите тип породы</option>");
                            foreach($type_rock_list as $key => $value) {
                                echo("<option value='{$value['ID']}'>{$value['TYPE_ROCK_NAME']}</option>");
                            }
                            ?>
                        </select>
                    </div>
                </td>
                <td class="btn-method-add-del" style='display: table-cell;'>
                    <input style='margin-bottom:5px; width: 32px;' type='button' class='add2' value='+'>
                    <?
                    if($index>=1){
                        echo ("<input style='margin-bottom:5px; width: 32px;' type='button' class='del' value='-'>");
                    }
                    ?>
                </td>
            </tr>
            <?
        }else {

        foreach ($getMaterial as $key => $value) {
          ?>
          <tr>
            <td>Материалы карьера</td>
            <td>
              <div class="porods" style="display: inline-block;">
              Название породы:<br>
                <select class="obl2" name="rock_porod[]" style="margin-bottom: 5px;" required >
                  <?
                    $rock_porod_list = $db_conn->select("SELECT * FROM RockPorod");
                    echo("<option selected value={$value['ID']}>{$value['ROCK_POROD_NAME']}</option>");

                    foreach($rock_porod_list as $key => $value_1) {
                      echo("<option value={$value_1['ID']}>{$value_1['ROCK_POROD_NAME']}</option>");
                    }
                  ?>
                </select> <br>
                Тип породы:<br>
                <select name="TYPE_ROCK[]" required>
                  <?
                    $type_rock_list = $db_conn->select("SELECT * FROM TypeRock");
                    $getType = $db_conn->select("SELECT * FROM `TypeRock` WHERE `ID`={?}", array($value['TYPE_ROCK_ID']));

                    echo("<option selected value={$getType[0]['ID']}>{$getType[0]['TYPE_ROCK_NAME']}</option>");
                    foreach($type_rock_list as $key => $value2) {
                      echo("<option value='{$value2['ID']}'>{$value2['TYPE_ROCK_NAME']}</option>");
                    }
                  ?>
                </select>
              </div>
            </td>
            <td class="btn-method-add-del" style='display: table-cell;'>
                <input style='margin-bottom:5px; width: 32px;' type='button' class='add2' value='+'>
                <?
                    if($index >= 1){
                      echo ("<input style='margin-bottom:5px; width: 32px;' type='button' class='del' value='-'>");
                    }
                ?>
            </td>
          </tr>

          <? $index ++;
        }
        }
      ?>

      <tr>
        <td>Транспортная доступность</td>
        <td>
          <textarea class="obl" name="CAR_ACCESS" style="height: 70px;" placeholder="Кемеровская область, г. Берёзовский, п. Барзас, ул. Центральная, д. 35А" required><?= ((!empty($getQuarry[0]['CAR_ACCESS'])) ? $getQuarry[0]['CAR_ACCESS'] : '') ?></textarea>
        </td>
      </tr>
    </tbody>
  </table>

  <input class="btn_save_obl_acc" type="submit" value="Сохранить"><br><br>
</form>

<br>
<h4>Список всех карьеров</h4>
  <table id="quarryTable">
    <thead>
      <th>№ п/п</th>
      <th>Наименование карьера</th>
      <th>Координаты карьера</th>
      <th>ID - карьера</th>
      <th>Материалы карьера</th>
      <th>Транспортная доступность карьера</th>
      <th>Привязанные ТЗ к карьеру</th>
    </thead>
    <tbody>
    <?

      // запрос к БД за списком карьеров
      $quarList = $db_conn->select('select * from Quarry where is_active = 1');
      foreach($quarList as $key => $value) {

        echo("
          <tr>
            <td></td>
            <td>{$value['NAME']}</td>
            <td>{$value['LNG']}&deg; долготы \n{$value['LAT']}&deg; широты</td>
            <td><a href='/Quarry/quarryAdd.php?EDIT={$value['ID']}'>Редактировать</a></td>
            <td>
        ");

          // получаем все материалы
        $materList = $db_conn->select('select ROCK_POROD_NAME, TYPE_ROCK_NAME from RockPorod, TypeRock where TYPE_ROCK_ID = TypeRock.ID AND QUARRY_ID = {?}', array($value['ID']));
        foreach($materList as $key1 => $val1) {
          echo($val1['ROCK_POROD_NAME'] . ' - ' . $val1['TYPE_ROCK_NAME']);
          echo('<br>');
        }
        echo("
            </td>
            <td>{$value['CAR_ACCESS']}</td>
        ");

        $res_tz_select_quarry = $db_conn->select("SELECT * from ba_tz where QUARRY_ID = {?}", array($value['ID']));

        $str_id_tz = '';
        echo('<td>');
        foreach($res_tz_select_quarry as $kk => $vv) {
          if(!empty($vv['NUM_P'])){
            $link = 'Номер протокола - <a href="/../request_card.php?ID='.$vv['ID_Z'].'">'.$vv['NUM_P'].'</a>';
            echo($link . '<br>');
          }
        }
        echo('</td></tr>');
      }
    ?>
    </tbody>
  </table>

<?
  // ! script начало ===========================
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
	<script type="text/javascript" language="javascript"
            src="/jszip/jquery.dataTables.customSortandFilters.js"></script>
	<link rel="stylesheet" type="text/css" href="../select2/dist/css/select2.min.css"/>
	<script type="text/javascript" src="../select2/dist/js/select2.full.js"></script>

	<!-- плагин jQuery для масок в input -->
	<!-- <script src="jquery.maskedinput.min.js"></script> -->
	<script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js" type="text/javascript"></script>

		<!-- собственный календарь при выборе отрезка времени -->
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="/vendor/jquery_localization-ui/localization.js"></script>

	<script type="text/javascript" src="https://rawgit.com/ultimatedelman/autogrow/master/autogrow.min.js"></script>

<!-- добавление масок и прочего -->
<script>
  $(document).ready(function() {
      // ! FIX jquery warning - passive event handler ==
    jQuery.event.special.touchstart = {
      setup: function( _, ns, handle ) {
        this.addEventListener('touchstart', handle, { passive: !ns.includes('noPreventDefault') });
      }
    };
    jQuery.event.special.touchmove = {
      setup: function( _, ns, handle ) {
          this.addEventListener('touchmove', handle, { passive: !ns.includes('noPreventDefault') });
      }
    };
    // ! ==
    $('.obl2').select2({
			placeholder: 'Выберите',
      tags: true,
		});

		$('input[name=LAT]').mask('99.999999');
		$('input[name=LNG]').mask('99.999999');

  });
</script>

<!-- отслеживаение rликов и event -->
<script>
  $(document).ready(function() {
    // автоматическая нумерацич строк
    $('#quarryTable tbody tr').each(function(index) {
      $(this).find('td:first').text(index + 1);
    });

    let buttons = "<td class='btn-method-add-del' style='display: table-cell;'><input style='margin-right:5px; margin-bottom:5px; width: 32px;' type='button' class='add' value='+'><input style='width: 32px;' type='button' class='del' value='-'></td>";
    let buttons2 = "<td class='btn-method-add-del' style='display: table-cell;'><input style='margin-right:5px; margin-bottom:5px; width: 32px;' type='button' class='add2' value='+'><input style='width: 32px;' type='button' class='del' value='-'></td>";

    $('#tz').on('click', '.add', function() {
      let html = $(this).parent().parent().find('.name_quarry').parent().html();
      tr = $(this).parent().parent();
      tr.after('<tr><td></td><td>' + html + '</td>' + buttons + '</tr>');
    });

    $('#tz').on('click', '.add2', function() {
      $('.obl2').select2('destroy');
      let html = $(this).parent().parent().find('.porods').parent().html();
      tr = $(this).parent().parent();
      $(tr).after('<tr><td></td><td>' + html + '</td>' + buttons2 + '</tr>');

      $('.obl2').select2({
        tags: true,
      });
    });

    $('#tz').on('click', '.del', function() {
			tr = $(this).parent().parent();
			tr.remove();
		});

  });
</script>

<?
require_once ($_SERVER['DOCUMENT_ROOT']) . '/bitrix/footer.php';
