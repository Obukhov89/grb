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
$APPLICATION->SetTitle("Область аккредитации и перечень ТУ");
  include $_SERVER["DOCUMENT_ROOT"]."/include/function.php"; 
?>

<link rel="stylesheet" href="/ulab/assets/plugins/magnific-popup/magnific-popup.css">
<style>
	.bg-light {
		background-color:#f8f9fa!important
	}
	.col-md-4 {
		flex:0 0 auto;width:33.3333333333%
	}
	.m-auto{
		margin:auto!important
	}
	.p-3{padding:1rem!important}
	.position-relative{position:relative!important}
	.mb-3{margin-bottom:1rem!important}
	.h-25{height:25%!important}
	.row{--bs-gutter-x:1.5rem;--bs-gutter-y:0;display:flex;flex-wrap:wrap;margin-top:calc(var(--bs-gutter-y) * -1);margin-right:calc(var(--bs-gutter-x)/ -2);margin-left:calc(var(--bs-gutter-x)/ -2)}
	.col{flex:1 0 0%}
	.line-dashed, .line-dashed-small {
		border-top: 1px dashed #DDD;
		height: 1px;
		margin: 1.25rem auto;
	}
	.line-dashed-small {
		margin: 1rem auto;
	}
	.form-label{margin-bottom:.5rem}
	.form-control{display:block;width:100%;padding:.375rem .75rem;font-size:1rem;font-weight:400;line-height:1.5;color:#212529;background-color:#fff;background-clip:padding-box;border:1px solid #ced4da;-webkit-appearance:none;-moz-appearance:none;appearance:none;border-radius:.25rem;transition:border-color .15s ease-in-out,box-shadow .15s ease-in-out}
	.table{caption-side:bottom;border-collapse:collapse}
	.table-hover>tbody>tr:hover{--bs-table-accent-bg:var(--bs-table-hover-bg);color:var(--bs-table-hover-color)}
	.mb-3{margin-bottom:1rem!important}
	.w-100{width:100%!important}
</style>

<?php
//pre($_POST);
//Сохранение неопределенности
if ( !empty($_POST['infelicity']) ) {
	$DB->Query("DELETE FROM `infelicity` WHERE `gost_id` = {$_GET['ID']}");
	for ($i = 0; $i < count($_POST['infelicity']); $i++) {
		$dFrom = $_POST['diapasonFrom'][$i] ?? '';
		$dTo = $_POST['diapasonTo'][$i] ?? '';
		$infel = $_POST['infelicity'][$i] ?? '';
		$DB->Query("INSERT INTO `infelicity` SET `gost_id` = {$_GET['ID']}, `diapason_from` = '{$dFrom}', `diapason_to` = '{$dTo}', `infelicity` = '{$infel}'");
	}
}
if ( !empty($_GET['ID']) ) {
	$infelicity = $DB->Query("SELECT * FROM `infelicity` WHERE `gost_id` = {$_GET['ID']}");
	while ($infelicities = $infelicity->Fetch()) {
		$rr[] = $infelicities;
	}
}
//Сохранение неопределенности. Конец

$norm_dop=[];
$val_dop=[];
if ($_GET['ID'] > 0) {
if (!empty($_POST['equip'])) {
		//$ids = json_decode($_POST{'ids_oborud'});
		$DB->Query("DELETE FROM `ba_connection` WHERE `GOST_ID` = " . $_GET['ID']);
		
		foreach ($_POST['equip'] as $k => $id) {
			$DB->Query("INSERT INTO `ba_connection`(`GOST_ID`, `OBORUD_ID`, `EQUIP_GOST`, `COMMENT`) VALUES (" . $_GET['ID'] . "," . $id . ", '".$_POST['equip_gost'][$k]."',
			'".$_POST['comment'][$k]."'	) ");
		}
	}
}
if($_POST['ch_char'])
{
	// echo "Внимание! Условия эксплуатации оборудования ".$_POST['ch_char']." не совпадают с условиями проведения испытаний данной методики. Сохранение невозможно! <br>";
	// echo "<a href = '/obl_acc.php?ID=".$_POST['ID']."'>Вернуться к редактированию</a>";
	// die;
}

if (isset($_POST['ID'])) {
	/*-------------------'Не нормируется' начало---------------------*/
    //Проверка Темпиратура, Влажность, Давление отмечен checkbox 'Не нормируется' или нет
    $temperature = ((!empty($_POST['checkTemperature']) && $_POST['checkTemperature'] == 'on') ? (int)1 : '');
    $humidity = ((!empty($_POST['checkHumidity']) && $_POST['checkHumidity'] == 'on') ? (int)1 : '');
    $pressure = ((!empty($_POST['checkPressure']) && $_POST['checkPressure'] == 'on') ? (int)1 : '');
    /*-------------------'Не нормируется' окончание-------------------*/
	
	
	
    if (!empty($_POST['ID'])) {
		$res_tz = $DB->Query("SELECT * FROM `ba_tz` ORDER BY `ID` DESC");
		$count_tz=0;
		
		while($tz = $res_tz->Fetch())
		{
			$TZ1 = unserialize($tz['TZ']);
			for($j=1; $j<=count($TZ1); $j++)
				for($l=0; $l<=count($TZ1[$j]['gost']); $l++)
					{
					   if($TZ1[$j]['gost'][$l]==$_POST['ID']) 
					   {
						   $count_tz++;
						   $req_ar[] = "<a href='/request_card.php?ID=".$tz['ID_Z']."' target='_blank'>".$tz['REQUEST_TITLE']."</a>";
					   }
					}
		}
		
		$norm_dop = serialize($_POST['NORM_DOP']);
		$val_dop = serialize($_POST['VALUE_DOP']);
		$temp = serialize($_POST['TEMP']);
		$vlag = serialize($_POST['VLAG']);
		$press = serialize($_POST['PRESS']);
		$place = serialize($_POST['PLACE']);
		$assign = serialize($_POST['ASSIGNED']);
		$group_price = serialize($_POST['GROUP_PRICE']);
        $res = $DB->Query("UPDATE `ba_gost` SET 
        `GOST`='" . $_POST['GOST'] . "', 
        `GOST_DOC`='" . $_POST['GOST_DOC'] . "', 
        `GOST_VAL`='" . $_POST['GOST_VAL'] . "', 
        `SPECIFICATION`='" . $_POST['SPECIFICATION'] . "', 
        `".(($_POST['NORM_TEXT'] == 'on')? 'NORM1_TEXT':'NORM1')."`='" . $_POST['NORM1'] . "', 
        `".(($_POST['NORM_TEXT'] == 'on')? 'NORM2_TEXT':'NORM2')."`='" . $_POST['NORM2'] . "', 
        `IN_OUT`=" . (($_POST['IN_OUT'] == 'on') ? 1 : 0) . ", 
        `MATCH_MANUAL`=" . (($_POST['MATCH_MANUAL'] == 'on') ? 1 : 0) . ", 
        `NORM_TEXT`=" . (($_POST['NORM_TEXT'] == 'on') ? 1 : 0) . ", 
		`UNNORM`=" . (($_POST['UNNORM'] == 'on') ? 1 : 0) . ", 
        `ED`='" . $_POST['ED'] . "',
        `COMMENT`='" . $_POST['COMMENT'] . "',
		`NORM_COMMENT`='" . $_POST['NORM_COMMENT'] . "',
		`NAME_GOST`='" . $_POST['NAME_GOST'] . "',
		`DOP`='" . $_POST['DOP'] . "',
		`NORM_DOP`='" . $norm_dop . "',
		`VALUE_DOP`='" . $val_dop . "',
		`GROUP_PRICE`='" . $group_price . "',
		`TEMP`='" . $temp . "',
		`VLAG`='" . $vlag . "',
		`PRESS`='" . $press . "',
		`PLACE`='" . $place . "',
		`GOST_TYPE`='" . $_POST['GOST_TYPE'] . "',
		`GOST_YEAR`='" . $_POST['YEAR'] . "',
		`ED_INDEX`=" . (int)$_POST['ED_INDEX'] . ",
		`GOST_PUNKT`='" . $_POST['GOST_PUNKT'] . "',
		`GOST_OBJECT`='" . $_POST['GOST_OBJECT'] . "',
		`IN_OA`=" . (($_POST['IN_OA'] == 'on') ? 1 : 0) . ",
		`RES_TEXT`=" . (($_POST['RES_TEXT'] == 'on') ? 1 : 0) . ",
		`LFMI`=" . (($_POST['LFMI'] == 'on') ? 1 : 0) . ",
		`LFHI`=" . (($_POST['LFHI'] == 'on') ? 1 : 0) . ",
		`LSM`=" . (($_POST['LSM'] == 'on') ? 1 : 0) . ",
		`DSL`=" . (($_POST['DSL'] == 'on') ? 1 : 0) . ",
		`OSK`=" . (($_POST['OSK'] == 'on') ? 1 : 0) . ",
		`NO_EXPORT`=" . (($_POST['NO_EXPORT'] == 'on') ? 1 : 0) . ",
		`CHECKED`=" . (($_POST['CHECKED'] == 'on') ? 1 : 0) . ",
		`NON_ACTUAL`=" . (($_POST['NON_ACTUAL'] == 'on') ? 1 : 0) . ",
		`ACCURACY`='" . $_POST['ACCURACY'] . "',
		`ASSIGNED`='" . $assign . "',
		`TNVED`='" . $_POST['TNVED'] . "',
		`OKPD`='" . $_POST['OKPD'] . "',
		`MARKA`='" . $_POST['MARKA'] . "',
		`NUM_OA`=" . (int)$_POST['NUM_OA'] . ",
		`NUM_OA_NEW`=" . (int)$_POST['NUM_OA_NEW'] . ",
        `PRICE`=" . $_POST['PRICE'] . ",
        `EXTENSION`=" . (($_POST['ext'] == 'on') ? 1 : 0) .",
		/*-------------------'Не нормируется' начало---------------------*/
         `TEMPERATURE`='" . $temperature . "',
         `HUMIDITY`='" . $humidity . "',
         `PRESSURE`='" . $pressure . "'
         /*-------------------'Не нормируется' окончание-------------------*/
        WHERE `ID`=" . (int)$_GET['ID']);
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
		if($count_tz && $_POST['NON_ACTUAL'] == 'on') 
		{
			echo "Внимание! Методика/ТУ  переведена в неактуальные, но используется в следующих заявках: ". implode(', ', array_unique($req_ar));
			echo "<br><a href = '/obl_acc.php?ID=".$_POST['ID']."'>Вернуться к методике/ТУ</a>";
			die;
			
		}
		//echo $count_tz;
    } else {
		/*-------------------'Не нормируется' начало---------------------*/
        //Проверка Темпиратура, Влажность, Давление отмечен checkbox 'Не нормируется' или нет
        $temperature = (!empty($_POST['checkTemperature']) && $_POST['checkTemperature'] == 'on' ? (int)1 : '');
        $humidity = (!empty($_POST['checkHumidity']) && $_POST['checkHumidity'] == 'on' ? (int)1 : '');
        $pressure = (!empty($_POST['checkPressure']) && $_POST['checkPressure'] == 'on' ? (int)1 : '');
		
		$temp = serialize($_POST['TEMP']);
		$vlag = serialize($_POST['VLAG']);
		$press = serialize($_POST['PRESS']);
		$place = serialize($_POST['PLACE']);
		$assign = serialize($_POST['ASSIGNED']);
		$group_price = serialize($_POST['GROUP_PRICE']);
        /*$res = $DB->Query("INSERT INTO `ba_gost`
        (GOST,GOST_DOC,GOST_VAL,SPECIFICATION,".(($_POST['NORM_TEXT'] == 'on')? 'NORM1_TEXT':'NORM1').",".(($_POST['NORM_TEXT'] == 'on')? 'NORM2_TEXT':'NORM2').",IN_OUT,MATCH_MANUAL,NORM_TEXT,ED,PRICE,COMMENT, IN_OA, NORM_COMMENT, NAME_GOST, UNNORM, DOP, NORM_DOP,  GOST_TYPE, GOST_PUNKT, ACCURACY, LFHI, LFMI, DSL, LSM, OSK, TNVED, GOST_OBJECT, RES_TEXT, NO_EXPORT, PLACE, GOST_YEAR, ASSIGNED, ED_INDEX, NUM_OA, TEMP, VLAG, PRESS, CHECKED, NUM_OA_NEW, OKPD, MARKA, NON_ACTUAL, GROUP_PRICE)
        VALUES
        ('" . $_POST['GOST'] . "', '" . $_POST['GOST_DOC'] . "', '" . $_POST['GOST_VAL'] . "', '" . $_POST['SPECIFICATION'] . "', '" . $_POST['NORM1'] . "', '" . $_POST['NORM2'] . "', " . (($_POST['IN_OUT'] == 'on') ? 1 : 0) . ", " . (($_POST['MATCH_MANUAL'] == 'on') ? 1 : 0) . ", " . (($_POST['NORM_TEXT'] == 'on') ? 1 : 0) . ", '" . $_POST['ED'] . "', " . (($_POST['PRICE']) ? $_POST['PRICE'] : 0) . ", '" . $_POST['COMMENT'] . "', " . (($_POST['IN_OA'] == 'on') ? 1 : 0) . ", '" . $_POST['NORM_COMMENT'] . "', '" . $_POST['NAME_GOST'] . "', " . (($_POST['UNNORM'] == 'on') ? 1 : 0) . ", '" . $_POST['DOP'] . "', '" . $norm_dop . "', '" . $_POST['GOST_TYPE'] . "', '" . $_POST['GOST_PUNKT'] . "', '" . $_POST['ACCURACY'] . "', " . (($_POST['LFHI'] == 'on') ? 1 : 0) . ", " . (($_POST['LFMI'] == 'on') ? 1 : 0) . ", " . (($_POST['DSL'] == 'on') ? 1 : 0) . ", " . (($_POST['LSM'] == 'on') ? 1 : 0) . ", " . (($_POST['OSK'] == 'on') ? 1 : 0) . ", '" . $_POST['TNVED'] . "', '" . $_POST['GOST_OBJECT'] . "', " . (($_POST['RES_TEXT'] == 'on') ? 1 : 0) . ", " . (($_POST['NO_EXPORT'] == 'on') ? 1 : 0) . ", '" . $place . "', '" . $_POST['YEAR'] . "', '" . $assign . "', " . (int)$_POST['ED_INDEX'] . ", " . (int)$_POST['NUM_OA'] . ", '" . $temp . "', '" . $vlag . "', '" . $press . "', " . (($_POST['CHECKED'] == 'on') ? 1 : 0) . ", " . (int)$_POST['NUM_OA_NEW'] . ", '" . $_POST['OKPD'] . "', '" . $_POST['MARKA'] . "', " . (($_POST['NON_ACTUAL'] == 'on') ? 1 : 0) . ", '" . $group_price . "')");*/
		
		$res = $DB->Query("INSERT INTO `ba_gost`
        (GOST,GOST_DOC,GOST_VAL,SPECIFICATION," . (($_POST['NORM_TEXT'] == 'on') ? 'NORM1_TEXT' : 'NORM1') . "," . (($_POST['NORM_TEXT'] == 'on') ? 'NORM2_TEXT' : 'NORM2') . ",IN_OUT,MATCH_MANUAL,NORM_TEXT,ED,PRICE,COMMENT, IN_OA, NORM_COMMENT, NAME_GOST, UNNORM, DOP, NORM_DOP,  GOST_TYPE, GOST_PUNKT, ACCURACY, LFHI, LFMI, DSL, LSM, OSK, TNVED, GOST_OBJECT, RES_TEXT, NO_EXPORT, PLACE, GOST_YEAR, ASSIGNED, ED_INDEX, NUM_OA, TEMP, VLAG, PRESS, CHECKED, NUM_OA_NEW, OKPD, MARKA, NON_ACTUAL, GROUP_PRICE, `TEMPERATURE`, `HUMIDITY`, `PRESSURE`, `EXTENSION`)
        VALUES
        ('" . $_POST['GOST'] . "', '" . $_POST['GOST_DOC'] . "', '" . $_POST['GOST_VAL'] . "', '" . $_POST['SPECIFICATION'] . "', '" . $_POST['NORM1'] . "', '" . $_POST['NORM2'] . "', " . (($_POST['IN_OUT'] == 'on') ? 1 : 0) . ", " . (($_POST['MATCH_MANUAL'] == 'on') ? 1 : 0) . ", " . (($_POST['NORM_TEXT'] == 'on') ? 1 : 0) . ", '" . $_POST['ED'] . "', " . (($_POST['PRICE']) ? $_POST['PRICE'] : 0) . ", '" . $_POST['COMMENT'] . "', " . (($_POST['IN_OA'] == 'on') ? 1 : 0) . ", '" . $_POST['NORM_COMMENT'] . "', '" . $_POST['NAME_GOST'] . "', " . (($_POST['UNNORM'] == 'on') ? 1 : 0) . ", '" . $_POST['DOP'] . "', '" . $norm_dop . "', '" . $_POST['GOST_TYPE'] . "', '" . $_POST['GOST_PUNKT'] . "', '" . $_POST['ACCURACY'] . "', " . (($_POST['LFHI'] == 'on') ? 1 : 0) . ", " . (($_POST['LFMI'] == 'on') ? 1 : 0) . ", " . (($_POST['DSL'] == 'on') ? 1 : 0) . ", " . (($_POST['LSM'] == 'on') ? 1 : 0) . ", " . (($_POST['OSK'] == 'on') ? 1 : 0) . ", '" . $_POST['TNVED'] . "', '" . $_POST['GOST_OBJECT'] . "', " . (($_POST['RES_TEXT'] == 'on') ? 1 : 0) . ", " . (($_POST['NO_EXPORT'] == 'on') ? 1 : 0) . ", '" . $place . "', '" . $_POST['YEAR'] . "', '" . $assign . "', " . (int)$_POST['ED_INDEX'] . ", " . (int)$_POST['NUM_OA'] . ", '" . $temp . "', '" . $vlag . "', '" . $press . "', " . (($_POST['CHECKED'] == 'on') ? 1 : 0) . ", " . (!empty($_POST['NUM_OA_NEW']) ? (int)$_POST['NUM_OA_NEW'] : '1') . ", '" . $_POST['OKPD'] . "', '" . $_POST['MARKA'] . "', " . (($_POST['NON_ACTUAL'] == 'on') ? 1 : 0) . ", '" . $group_price . "',  '" . $temperature . "',  '" . $humidity . "', '" . $pressure . "', '" . (($_POST['ext'] == 'on') ? 1 : 0) . "')");
        /*-------------------'Не нормируется' окончание-------------------*/
        
		$resLastId = $DB->Query("SELECT * FROM `ba_gost` ORDER BY `ID` DESC LIMIT 1")->Fetch();
		
		if (!empty($_POST['equip'])) {
																					  
			foreach ($_POST['equip'] as $k => $id) {
				$DB->Query("INSERT INTO `ba_connection`(`GOST_ID`, `OBORUD_ID`, `EQUIP_GOST`, `COMMENT`) VALUES (" . $resLastId['ID'] . "," . $id . ", '".$_POST['equip_gost'][$k]."',
				'".$_POST['comment'][$k]."'	) ");
			
			}
		}
		if ($res->result = 1) {
            header("Location: //" . $_SERVER['HTTP_HOST']
                . "/obl_acc.php");
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
    }
	
	$res_history = $DB->Query("INSERT INTO `HISTORY_OA`(`TIME`, `USER_ID`, `TYPE`, `ID_ELEM`) VALUES ('" . date('Y-m-d H:i:s') . "'," . $USER->GetID() . ",'2'," . $_POST['ID'] . ")");
}

// Поиск по ID. Вывод формы для создания/редактирования
if (isset($_GET['ID'])) {
    global $DB;
	$norm_dop2 = [];
	$val_dop2 = [];
	
    if (!empty($_GET['del'])) {
        $res = $DB->Query("DELETE FROM `ba_gost` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();
        if ($res->result = 1) {
            echo "<h3>Успешно</h3>";
        } else {
            echo "<h3>Ошибка</h3>";
        }
        echo "<br><a href=\"/obl_acc.php\">К списку</a>";
        echo "<hr>";
    } else {

        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . (int)$_GET['ID']);
        $row = $res->Fetch();
		$norm_dop2 = unserialize($row['NORM_DOP']);
		$val_dop2 = unserialize($row['VALUE_DOP']);
		$temp_val = unserialize($row['TEMP']);
		$vlag_val = unserialize($row['VLAG']);
		$press_val = unserialize($row['PRESS']);
		$place = unserialize($row['PLACE']);
		$assign = unserialize($row['ASSIGNED']);
		$group_price = unserialize($row['GROUP_PRICE']);
		//var_dump($norm_dop2);
		//dump($_POST);
        ?>	
		<input id='status' type='hidden' value='<?=$USER->GetID()?>'></input>
		<!---------------------------------------  начало 'Общее меню' -------------------------------------->
		<div>
			<div class='return disable' title='Вернуться назад'></div>
			<div class='edit disable' title='Редактировать'></div>
			<a href="/obl_acc.php">
				<div class='list' title='Вернуться к списку'></div><!--К списку-->
			</a>
			<div class='card disable' title='Карточка заявки'></div>
			<div class='download disable' title='Скачать'></div>
			<?php if ($_GET['ID'] > 0) { ?> <!--|--> <a href="/metodic_copy.php?ID=<?= $_GET['ID'] ?>">
				<div class='copy' title='Скопировать'></div><!--Скопировать-->
			</a> 
				<?php if(($USER->GetID()=='1')||($USER->GetID()=='10')||($USER->GetID()=='9') ||($USER->GetID()=='8')) {?> <!--|--> <a href="/obl_acc.php?ID=<?= $_GET['ID'] ?>&del=true"
				   onclick="if(confirm('Вы действительно хотите удалить?')) {return true;} else {return false;}">
					<div class='remove' title='Удалить'></div><!--Удалить-->
				</a>
			<?php } } ?> 
			<div class='add disable' title='Добавить'></div>
			<a href="/validation_card.php?ID=<?= $_GET['ID'] ?>">
				<div class='report' title='Отчёт'></div><!--Отчеты о верификации и валидации-->
			</a>
			<div class='further disable' title='Далее'></div>
            

            <a href="/ulab/index/gostOldToNew/<?= $_GET['ID'] ?>">СКОПИРОВАТЬ В НОВУЮ БАЗУ</a>

		</div>
		<!---------------------------------------- окончание 'Общее меню' ----------------------------------->
        <hr>
        <h2><?php echo ($_GET['ID'] == 0) ? 'Создание' : 'Редактирование'; ?></h2>
        <form method='post' enctype="multipart/form-data">
		
		
            <table id="obl_tab">
			<tr><td><h3>Основные характеристики</h3></td></tr>	
                <tr>
                    <td>Нормативный документ</td>
                    <td><input class="obl" type="text" name="GOST" value="<?= $row['GOST'] ?>" required></td>
				<tr>
                    <td>Пункт документа</td>
                    <td><input class="obl" type="text" name="GOST_PUNKT" value="<?= $row['GOST_PUNKT'] ?>" ></td>
				<tr>
                    <td>Год</td>
                    <td><input class="obl_num" type="number" name="YEAR" value="<?= $row['GOST_YEAR'] ?>"></td>
				<tr>
                    <td>Определяемая характеристика</td>
                    <td><input id="feature" class="obl" type="text" name="SPECIFICATION" value="<?= $row['SPECIFICATION'] ?>" size="75" required></td>
				<tr>
				    <td>Наименование ГОСТ</td>
                    <td><input class="obl" type="text" name="NAME_GOST" value="<?= $row['NAME_GOST'] ?>" size="75" required></td>
				<tr>
				    <td>Наименование объекта ГОСТ</td>
                    <td><textarea  class="obl" name="GOST_OBJECT" size="75" style="height:50px;" ><?= $row['GOST_OBJECT'] ?></textarea></td>
				<tr>
	<?php 
		$metodicsArr = array(
			'metodic' => 'Методика испытаний',
			'metodic_otbor' => 'Методика отбора проб (изг. образцов)',
			'TU_standart' => 'Тех. условия стандартные',
			'TU_group' => 'ТУ или Методика определения группы материала',
			'TU_research' => 'ТУ или Методика исследования по группе материала',
			'TU_LFHI' => 'Методика ЛФХИ',
			'TU_sred' => 'Методика с расчетом среднего значения',
			'TU_sred2' => 'Методика с расчетом среднего значения по 2 наиб.',
			'TU_sred3' => 'Методика с расчетом среднего значения по 3 наиб.',
			'TU_sred4' => 'Методика с расчетом среднего значения по 4 наиб.',
			'TU_sred5' => 'Методика с расчетом по 2 средним значениям',
			'TU_zern' => 'Зерновой состав песка строит.',
			'TU_nerud' => 'Зерновой состав нерудных СМ',
			'TU_prirod' => 'Зерновой состав песка природ.',
			'TU_shgp' => 'Зерновой состав ЩГП-смеси',
			'TU_grunt' => 'Зерновой состав грунтов',
			//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
			'TU_metric_method' => 'Зерновой состав грунтов  (aреометрическим метод)',
		   //------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ------------------------//
			'TU_12801' => 'Зерновой состав асф.-бет. смеси (ГОСТ 12801)',
			'TU_183' => 'Зерновой состав асф.-бет. смеси (ПНСТ 183)',
			'TU_184' => 'Зерновой состав асф.-бет. смеси (ПНСТ 184)',
			'TU_184_A32' => 'Зерновой состав асф.-бет. смеси (ПНСТ 184, А32НН)',
			'TU_shps' => 'Зерновой состав ЩПС',
			'sheb' => 'Зерновой состав щебня',
			'sheb_shlak' => 'Зерновой состав щебня шлакового (ГОСТ 3344)'
	);
	 ?>
	    <td>Тип документа</td>
              <td><select id="docType" class="obl" name="GOST_TYPE" required>
		<?php foreach($metodicsArr as $metodicKey => $metodicValue) : ?>
			<option <?= ($row['GOST_TYPE'] == $metodicKey) ? 'selected' : '' ?> value="<?= $metodicKey  ?>"><?= $metodicValue ?></option>
		<?php endforeach; ?>
	</select>
	</td>
                
				<tr>
                    <td>Текст нормативного значения</td>
                    <td><input class="obl" type="text" name="NORM_COMMENT" value="<?= $row['NORM_COMMENT'] ?>" size="75" required></td>
				<tr>
                    <td>Номер в ОА</td>
                    <td><input class="obl_num" type="number"  name="NUM_OA" value="<?=$row['NUM_OA']?>"></td>
				
				<tr>
                    <td>Номер в НОВОЙ ОА</td>
                    <td><input class="obl_num" type="number"  name="NUM_OA_NEW" value="<?=$row['NUM_OA_NEW']?>"></td>
				
              
                <tr><td><h3>Единицы измерения и нормы</h3></td></tr>
                <tr>
                    <td>Единица измерения</td>
					<!---'Правки протоколов'  начало---->
                     <td><div style="display:flex"><input class="obl_num" type="text" name="ED" value="<?= $row['ED'] ?>" style="width:135px" required>&nbsp;^&nbsp;<input class="obl" type="number" name="ED_INDEX" value="<?=$row['ED_INDEX']?>" style="width:50px" ></div></td>
					<!---'Правки протоколов'  окончание---->		
				<tr>
                    <td>Знаков после запятой в результате</td>
                    <td><input class="obl_num" type="number" name="ACCURACY" value="<?= $row['ACCURACY'] ?>" required></td>
				
                <tr>
                    <td>Диапазон определения 1</td>
                    <td><input class="obl_num" type="number" step="0.0001" name="NORM1" value="<?= (number_format($row['NORM1'], $row['ACCURACY'], '.', ''))?>"></td>
                <tr>
                    <td>Диапазон определения 2</td>
                    <td><input class="obl_num" type="number" step="0.0001" name="NORM2" value="<?= (number_format($row['NORM2'], $row['ACCURACY'], '.', '')) ?>"></td>
				
				<tr><td><h3>Лаборатории и сотрудники</h3></td></tr>
				<tr>
                    <td>ЛФХИ</td>
                    <td><input type="checkbox" name="LFHI" <?= ($row['LFHI'] == 1) ? 'checked' : '' ?>></td>
				<tr>
                    <td>ЛФMИ</td>
                    <td><input type="checkbox" name="LFMI" <?= ($row['LFMI'] == 1) ? 'checked' : '' ?>></td>
				<tr>
                    <td>ДСЛ</td>
                    <td><input type="checkbox" name="DSL" <?= ($row['DSL'] == 1) ? 'checked' : '' ?>></td>
				<tr>
                    <td>ЛCM</td>
                    <td><input type="checkbox" name="LSM" <?= ($row['LSM'] == 1) ? 'checked' : '' ?> ></td>
				<tr>
                    <td>ОCK</td>
                    <td><input type="checkbox" name="OSK" <?= ($row['OSK'] == 1) ? 'checked' : '' ?>></td>
				<tr>
                    <td>Номер кабинета</td>
                    <td><select class="obl2" name="PLACE[]" multiple="multiple">
                            
                            <?php
                            $res = $DB->Query("SELECT * FROM `ROOMS`");
                            while ($value = $res->Fetch()) {
                                echo "<option ";
                                if (in_array($value['ID'], $place)) {
                                    echo "selected";
                                }
                                echo " value='" . $value['ID'] . "'>" . $value['NAME'] . ' ' . ($value['NUMBER'] ? $value['NUMBER']:'') . "</option>";
                            }
                            ?>
                        </select></td>
				<tr>
                    <td>Ответственный</td>
                    <td>
					<select class="obl2" name="ASSIGNED[]" multiple="multiple">
						
						<?php
						$order = array('sort' => 'asc');
						$tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
						$select = CUser::GetList($order, $tmp);
						
						$selected='';
						while ($option = $select->Fetch()) {
							$selected = ((in_array($option['ID'], $assign)) ? 'selected' : '');
							echo "<option value='" . $option['ID'] . "'" . $selected . ">" . $option['NAME'] . ' ' . $option['LAST_NAME'] . "</option>";
						}
						?>
					</select></td>
				
				<tr><td><h3>Типы и марки материала</h3></td></tr>
				<tr>
                    <td>Марка материала</td>
                    <td><input class="obl" type="text" name="MARKA" value="<?= $row['MARKA'] ?>" size="75"></td>
				<tr>
                    <td>МАТЕРИАЛ</td>
                    <td>
					<select class="obl" name="DOP">
						<option value="0" selected>Не указан</option>
						<?php
						$select = $DB->Query("SELECT * FROM `MATERIALS`");
						$selected='';
						while ($option = $select->Fetch()) {
							$selected = (($option['ID']==$row['DOP']) ? 'selected' : '');
							echo "<option value='" . $option['ID'] . "'" . $selected . ">" . $option['NAME'] . "</option>";
						}
						?>
					</select>
					
				<?php
						$val_dop = $DB->Query("SELECT * FROM `MATERIALS` WHERE `ID`=" . (int)$row['DOP'])->Fetch();
						$val_dop2 = unserialize($val_dop['GROUPS']);
						$group_name = $val_dop['GROUP_NAME'];
						//dump($val_dop2);
						?>	
					
				<tr>
					<td>Название группы материала</td>
					<td><input class="obl" type="text" name="GN" value="<?= $group_name ?>" disabled ></td>
                <?php foreach($val_dop2 as $key => $vl)
						{ 
							if($vl) {
								echo 	"<tr><td>Группа материала</td>
								<td> <input class='obl' type='text' name='VALUE_DOP[".$key."]' value='".$vl."' ></td>
								<td>Нормы</td><td>от&nbsp;</td><td><input style='width:100%' class='obl_num' type='number' step='0.001' name='NORM_DOP[".$key."][0]' value=".$norm_dop2[$key][0]."></td>
								<td>&nbsp; до&nbsp; </td><td><input style='width:100%' class='obl_num' type='number' step='0.001' name='NORM_DOP[".$key."][1]' value=".$norm_dop2[$key][1]."></td>
								<td>Стоимость</td><td><input style='width:100%' class='obl_num' type='number' step='0.001' name='GROUP_PRICE[".$key."]' value='".$group_price[$key]."'></td>
								</tr>";
							}
						}
				?>
				<!-------------------'Не нормируется' начало---------------------->
                <!-----------------'Условия применения'-------------->
				<tr><td><h3>Условия применения</h3></td></tr>
				<tr>
                    <td>Температура (°С)</td>
					<td>
						<div class='dataTemperature' style="display:flex">от&nbsp; <input class="obl_num" type="number" step="0.1" name="TEMP[0]" value="<?=$temp_val[0]?>">&nbsp;до&nbsp; <input class="obl_num" type="number" step="0.1" name="TEMP[1]" value="<?=$temp_val[1]?>">
							<input type='checkbox' class='notTemperature' name='checkTemperature' <?= ((!empty($row['TEMPERATURE']) && $row['TEMPERATURE'] == '1') ? 'checked' : '') ?>>
							<span class='notNormalized'>Не нормируется</span>
						</div>
					</td>
				<tr>
                    <td>Влажность (%)</td>
                    <td>
						<div class='dataHumidity' style="display:flex">от&nbsp;  <input class="obl_num" type="number" step="0.1" name="VLAG[0]" value="<?=$vlag_val[0]?>">&nbsp;до&nbsp; <input class="obl_num" type="number" step="0.1" name="VLAG[1]" value="<?=$vlag_val[1]?>">
							<input type='checkbox' class='notHumidity' name='checkHumidity' <?= ((!empty($row['HUMIDITY']) && $row['HUMIDITY'] == '1') ? 'checked' : '') ?>>
							<span class='notNormalized'>Не нормируется</span>
						</div>
					</td>
				<tr>
                    <td>Атм. давление (КПа)</td>
                    <td>
						<div class='dataPressure' style="display:flex">от&nbsp;  <input class="obl_num" type="number" step="0.1" name="PRESS[0]" value="<?=$press_val[0]?>"> &nbsp;до&nbsp; <input class="obl_num" type="number" step="0.1" name="PRESS[1]" value="<?=$press_val[1]?>">
							<input type='checkbox' class='notPressure' name='checkPressure' <?= ((!empty($row['PRESSURE']) && $row['PRESSURE'] == '1') ? 'checked' : '') ?>>
							<span class='notNormalized'>Не нормируется</span>
						</div>
					</td>
				<!-- /.'Условия применения' -->
                <!-------------------'Не нормируется' окончание---------------------->
				<tr><td><h3>Дополнительные характеристики</h3></td></tr>
				<tr>
                    <td>Стоимость (руб.)</td>
                    <td><input class="obl_num" type="number" step="0.001" name="PRICE" value="<?= $row['PRICE'] ?>" required></td>
				<tr>
					<td>Код ТН ВЭД ЕАЭС</td>
					<td><textarea class="obl"  name="TNVED" value="<?= $row['TNVED'] ?>" style="height:70px;"><?= $row['TNVED'] ?></textarea></td>
				<tr>
					<td>Код ОКПД 2</td>
					<td><textarea class="obl"  name="OKPD" value="<?= $row['OKPD'] ?>" style="height:70px;"><?= $row['OKPD'] ?></textarea></td>
				<tr>
                    <td>НЕ НОРМИРУЕТСЯ!</td>
                    <td><input type="checkbox" name="UNNORM" <?= ($row['UNNORM'] == 1) ? 'checked' : '' ?>></td>
                <tr>
                    <td>Входящий диапазон определения</td>
                    <td><input type="checkbox" name="IN_OUT" <?= (($_GET['ID'] == 0)||($row['IN_OUT'] == 1)) ? 'checked' : '' ?>></td>
                <tr>
                    <td>Ручное управление "соотв/не соотв"</td>
                    <td><input type="checkbox" name="MATCH_MANUAL" <?= ($row['MATCH_MANUAL'] == 1) ? 'checked' : '' ?>></td>
                <tr>
                    <td>Нормы текстом?</td>
                    <td><input type="checkbox" name="NORM_TEXT" <?= ($row['NORM_TEXT'] == 1) ? 'checked' : '' ?>></td>
				<tr>
                    <td>Факт. значения текстом?</td>
                    <td><input type="checkbox" name="RES_TEXT" <?= ($row['RES_TEXT'] == 1) ? 'checked' : '' ?> 
		<?= in_array($row['GOST_TYPE'], ["TU_sred", "TU_sred2", "TU_sred3", "TU_sred4"]) ? 'disabled'  : '' ?>
		<?= in_array($row['GOST_TYPE'], ["TU_sred", "TU_sred2", "TU_sred3", "TU_sred4"]) ? 'title="Для данного типа документа не допускаются текстовые значения"'   : '' ?></td>
                <tr>
                    <td>Комментарий</td>
                    <td><input class="obl" type="text" name="COMMENT" value="<?= $row['COMMENT'] ?>" size="75"></td>				
				
				<tr><td><h3>Контроль</h3></td></tr>
				<tr>
                    <td>Проверено</td>
                    <td><input type="checkbox" name="CHECKED" <?= ($row['CHECKED'])? 'checked' : '' ?> <?= ($USER->GetID()=='1'||$USER->GetID()=='10'||$USER->GetID()=='33'||$USER->GetID()=='61') ? '' : 'disabled' ?>></td>
				<tr>
                    <td>Не актуален</td>
                    <td><input type="checkbox" name="NON_ACTUAL" <?= ($row['NON_ACTUAL'])? 'checked' : '' ?> <?= ($USER->GetID()=='1'||$USER->GetID()=='10'||$USER->GetID()=='11'||$USER->GetID()=='58') ? '' : 'disabled' ?>></td>
				<tr>
                    <td>В области аккредитации</td>
                    <td><input type="checkbox" name="IN_OA" <?= ($row['IN_OA'] == 1) ? 'checked' : '' ?>></td>
				<tr>
					<td>Расширение области аккредитации</td>
					<td><input type="checkbox" name="ext" <?= ($row['EXTENSION'] == 1) ? 'checked' : '' ?>></td>
				<!--<tr>
                    <td>Нормы группы мат.</td>
					<td><div style="display:flex">от&nbsp; <input class="obl_num" type="number" step="0.001" name="NORM_DOP[9][0]" value="<?= $norm_dop2[9][0] ?>">&nbsp; до&nbsp; <input class="obl_num" type="number" step="0.001" name="NORM_DOP[9][1]" value="<?= $norm_dop2[9][1] ?>"></div></td>
                    <td>Группа материала <input class="obl" type="text" name="VALUE_DOP[9]" value="<?= $val_dop2[9] ?>" disabled ></td>	-->
            </table>
            <input type="hidden" name="ID" value="<?= $row['ID'] ?>">

			<h3>Расчет неопределенности</h3>
		<div class="form-horizontal">
			<?php if ( empty($rr) ):?>
				<div class="input-group-obl-acc mb-3">
					<span class="input-group-text-obl-acc">От</span>
					<input type="number" step="0.001" name="diapasonFrom[]" value="">
					<span class="input-group-text-obl-acc">До</span>
					<input type="number" step="0.001" name="diapasonTo[]" value="">
					<span class="input-group-text-obl-acc">U(w)</span>
					<input type="number" step="0.001" name="infelicity[]" value="">
					<input style='margin-left:5px' type='button' class='add-infelicity button-obl-acc' value='+'>
					<input style='margin-left:5px' type='button' class='del-infelicity button-obl-acc' value='-'>
				</div>
			<?php else:
				foreach ($rr as $indefl):?>
					<div class="input-group-obl-acc mb-3">
						<span class="input-group-text-obl-acc">От</span>
						<input type="number" step="0.001" name="diapasonFrom[]" value="<?=$indefl['diapason_from']?>">
						<span class="input-group-text-obl-acc">До</span>
						<input type="number" step="0.001" name="diapasonTo[]" value="<?=$indefl['diapason_to']?>">
						<span class="input-group-text-obl-acc">U(w)</span>
						<input type="number" step="0.001" name="infelicity[]" value="<?=$indefl['infelicity']?>">
						<input style='margin-left:5px' type='button' class='add-infelicity button-obl-acc' value='+'>
						<input style='margin-left:5px' type='button' class='del-infelicity button-obl-acc' value='-'>
					</div>
					<!--			<td><input type="button" class="add" value="add"></td>-->
				<?php endforeach;
			endif;?>
		</div>
		<a class="btn popup-with-form" href="#tu-method" style="color: white">Привязать ТУ</a>

            
        
        <?php if ($_GET['ID'] >= 0) {// var_dump($_POST['GOST_TYPE']);//var_dump($_POST['NORM_DOP']); ?>
            <hr>
			 <table id="tz">
                    <thead>
                    <th>Оборудование ГОСТ</th>
                    <th>Оборудование ИЦ</th>
					
                    <th>Тип</th>
                    <th>Заключение</th>
                    <th></th>
                    </thead>
                    <tbody id="tz_body" name="tz_body">

									 
					 
																					   
					 

                    
                    <?php
                   
						$key=0;
                    //dump($arrDatum);
                    $res = $DB->Query("SELECT * FROM `ba_connection`,`ba_oborud` WHERE `ba_connection`.`GOST_ID`=" . $_GET['ID'] . " AND `ba_oborud`.`ID`=`ba_connection`.`OBORUD_ID` ");
					if($res->SelectedRowsCount()>0)
					{
						while ($row = $res->Fetch()) {
							//dump($row);
							switch ($row['IDENT']) {
							case 'SI':
								$ident = "СИ";
								break;
							case 'IO':
								$ident = "ИО";
								break;
							case 'VO':
								$ident = "ВО";
								break;
							case 'TS':
								$ident = "ТС";
								break;
							case 'SO':
								$ident = "ТС";
								break;
							case 'REACT':
								$ident = "Реактивы";
								break;
							case 'OOPP':
								$ident = "ООПП";
								break;
						}
							 ?>
							
							<tr id="<?= $key ?>" data-gost="0">
							
								
								
								<td class="tdGost">
									<input class="obl" type="text" style="max-width: 1000px!important;"  name="equip_gost[]" value="<?= $row['EQUIP_GOST'] ?>" size="75">
								</td>
								<td class="tdGost">
							
									<select name="equip[]" style="width:350px" class="gost methods styleGost"
											data-id="<?= $key ?>" >
							
										<option value="" disabled selected>Выберите</option>
										<?php
										$ba_gost = $DB->Query("SELECT * FROM `ba_oborud` WHERE 1");
										$check_char =1;
										
										while ($gost = $ba_gost->Fetch()) {
											// $check_char =1;
											// if((int)$gost['OVV_EX']>(int)$vlag_val[0] || (int)$gost['OVV_EX2']<(int)$vlag_val[1] || (int)$gost['TOO_EX']>(int)$temp_val[0] || (int)$gost['TOO_EX2']<(int)$temp_val[1])
												// $check_char = 0;
											echo "<option value='" . $gost['ID'] . "' " . ($gost['ID'] == $row['OBORUD_ID'] ? 'selected' : '')  .">" . ($gost['TYPE_OBORUD'] ? $gost['TYPE_OBORUD'] : '') . ' ' . $gost['OBJECT'] . ' | ' . $gost['REG_NUM'] . "</option>";
										}
										?>
									</select>
									
								</td>
							
								
								<td><?= $ident ?></td>
								<td><input class="obl" type="text" name="comment[]" value="<?= $row['COMMENT'] ? $row['COMMENT'] : 'Соответствует требованиям'?>" size="75"></td>
							   
								<?php

								if (!$key) {
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

						  
							$key++;
						}
					}
					else
					{ $key=0;
						$row=[];
						?>
						<tr id="<?= $key ?>" data-gost="0">
							
								
								
								<td class="tdGost">
									<input class="obl" type="text" style="max-width: 1000px!important; width: 100%"  name="equip_gost[<?= $key ?>]" value="<?= $row['EQUIP_GOST'] ?>" size="75">
								</td>
								<td class="tdGost">
							
									<select name="equip[<?= $key ?>]" style="width:350px" class="gost methods styleGost"
											data-id="<?= $key ?>" >
							
										<option value="" disabled selected>Выберите</option>
										<?php
										$ba_gost = $DB->Query("SELECT * FROM `ba_oborud` WHERE 1");
										$check_char =1; $ch_char=0;
										
										while ($gost = $ba_gost->Fetch()) {
											// $check_char =1;
											// if((((int)$gost['OVV_EX']>(int)$vlag_val[0] || (int)$gost['OVV_EX2']<(int)$vlag_val[1])&&!$row['HUMIDITY']) || (((int)$gost['TOO_EX']>(int)$temp_val[0] || (int)$gost['TOO_EX2']<(int)$temp_val[1])&&!$row['TEMPERATURE']))
											// { 
												// $check_char = 0;
												// $ch_char = $gost['OBJECT'] . '  ' . $gost['REG_NUM'];
											// }
											
											echo "<option value='" . $gost['ID'] . "' " . ($gost['ID'] == $row['OBORUD_ID'] ? 'selected' : '') . ">" . ($gost['TYPE_OBORUD'] ? $gost['TYPE_OBORUD'] : '') . ' ' . $gost['OBJECT'] . ' | ' . $gost['REG_NUM'] . "</option>";
										}
										?> 
									</select>
									<input type='hidden' value="<?= $ch_char ?>" name="ch_char">
								</td>
								
								<td><?= $ident ?></td>
								<td><input class="obl" type="text" name="comment[<?= $key ?>]" value="<?= $row['COMMENT'] ? $row['COMMENT'] : 'Соответствует требованиям' ?>" size="75"></td>
							   
								<?php

								if (!$key) {
									echo "<td class='btn-method-add-del'><input style='margin-left:5px' type='button' class='add2' value='+'></td>";
								} else {
									echo "<td class='btn-method-add-del'>
											<input style='margin-left:5px' type='button' class='add2' value='+'>
											<input style='margin-left:5px' type='button' class='del' value='-'>
										</td>";
								}
								?>
							</tr>
					<?	
					}
                    ?>
                    </tbody>
                </table>
			<input class='btn_save_obl_acc' type='submit' value='Сохранить'><br><br>
			</form>
            <?php
        }
    }
} else {// Вывод скиска ГОСТов

    global $DB;

$tu = "`GOST_TYPE` = 'TU_standart' OR `GOST_TYPE` = 'TU_research' OR `GOST_TYPE` = 'TU_group'";
$nTu = "`GOST_TYPE` != 'TU_standart' AND `GOST_TYPE` != 'TU_research' AND `GOST_TYPE` != 'TU_group'";

    if (!empty($_GET['lab']) && ($_GET['lab'] != 0)) {
		switch ($_GET['lab']) {
				case 1:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `LFHI` = 1 AND `NUM_OA_NEW` > '0' AND `NUM_OA` > '0' AND " . $nTu);
					break;
				case 2:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `LSM` = 1 AND `NUM_OA_NEW` > '0' AND `NUM_OA` > '0' AND " . $nTu);
					break;
				case 3:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `LFMI` = 1 AND `NUM_OA_NEW` > '0' AND `NUM_OA` > '0'AND " . $nTu);
					break;
				case 4:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `DSL` = 1 AND `NUM_OA_NEW` > '0' AND `NUM_OA` > '0' AND " . $nTu);
					break;
				case 5:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `OSK` = 1 AND `NUM_OA_NEW` > '0' AND `NUM_OA` > '0' AND " . $nTu);
					break;
				case 10:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `IN_OA` = 1 AND `EXTENSION` != '1' AND `NUM_OA_NEW` > '0' AND `NUM_OA` > '0' AND " . $nTu);
					break;
				case 11:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `IN_OA` = 0 AND " . $nTu);
					break;
				case 12:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE" . $tu);
					break;
				case 13:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `NUM_OA_NEW` = '0' AND" . $nTu);
					break;
				case 14:
					$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `EXTENSION` = '1'");
					break;
			}
		
        
    } else {
        $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `IN_OA` = 1 AND `NUM_OA_NEW` > '0' AND `EXTENSION` != '1' AND `NUM_OA` > '0' AND " . $nTu);
    }


   // $res = $DB->Query("SELECT * FROM `ba_gost`");
    ?>
 <a href="/obl_acc.php?ID=0">Добавить новый ГОСТ</a> | <a href='/protocol_generator/obl_acc_doc.php'>Скачать ОА</a> <!--| <a href='/protocol_generator/passport_rc.php'>Скачать формы 2-6</a>*/-->

<div class="btn-filter-OA">
		<button class="btn-filter-OA" id="<?= ($_GET['lab'] == '10') ? 'btn-active' : '' ?>" value="10" onclick="window.location.assign('/obl_acc.php?lab='+this.value)">ОА</button>
		<button class="btn-filter-OA" id="<?= ($_GET['lab'] == '14') ? 'btn-active' : '' ?>" value="14" onclick="window.location.assign('/obl_acc.php?lab='+this.value)">РОА</button>
		<button class="btn-filter-OA" id="<?= ($_GET['lab'] == '12') ? 'btn-active' : '' ?>" value="12" onclick="window.location.assign('/obl_acc.php?lab='+this.value)">ТУ</button>
		<button class="btn-filter-OA" id="<?= ($_GET['lab'] == '11') ? 'btn-active' : '' ?>" value="11" onclick="window.location.assign('/obl_acc.php?lab='+this.value)">Вне ОА</button>
		<button class="btn-filter-OA" id="<?= ($_GET['lab'] == '13') ? 'btn-active' : '' ?>" value="13" onclick="window.location.assign('/obl_acc.php?lab='+this.value)">Старая ОА</button>

	<select class="obl" name="lab_gost" id="lab"
            onchange="window.location.assign('/obl_acc.php?lab='+this.options[this.selectedIndex].value)"
            style="float: right;">
      <option value="0" selected>Выберите лабораторию</option>
<!--		<option value="10" --><?//=($_GET['lab']=='10' ? 'selected': '')?><!--Только область аккредитации</option>-->
<!--		<option value="11" --><?//=($_GET['lab']=='11' ? 'selected': '')?><!--Вне области аккредитации</option>-->
        <?php
        $select = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID` != '6'");
        while ($option = $select->Fetch()) {
            echo "<option ";
            if ($_GET['lab'] == $option['ID']) {
                echo "selected";
            }
            echo " value='" . $option['ID'] . "'>" . $option['NAME'] . "</option>";
        }
        ?>
    </select>
</div>
	<br>
	<br>
    <hr>
<table id="gost-table">
	<thead>
    <!--<tr>-->
	<tr>
		<th></th>
		<th>№ ОА</th>
        <th >Опред. характеристика</th>
        <!--<th>Нормативный документ на метод испытания</th>
        <th>Нормативная документация</th>-->
        <th>Нормативный документ</th>
		<th>Объект ГОСТ</th>
		<th>Пункт ГОСТ</th>
		<th>Наименование ГОСТ</th>
		<th>Тип</th>
		<th>Год</th>
		<th>Норма 1</th>
        <th>Норма 2</th>
        <th>Ед. изм.</th>
        <th>Стоимость (р.)</th>
       <!-- <th>Комментарий</th>-->
		<th>В области?</th>
		<th>Верификация</th>
		<!--<th>Функции</th>-->
	</tr>
	<tr class='trSearch'>
		<th style="margin:0px"></th>
		<th><input type="text" name="num_filter" id="num_filter" style="margin:0px" value=""></th>
		<th><input type="text" name="characteristic_filter" id="characteristic_filter" style="margin:0px" value=""></th>
		<th><input type="text" name="normative_filter" id="normative_filter" style="margin:0px" value=""></th>
		<th><input type="text" name="gost_object_filter" id="gost_object_filter" style="margin:0px" value=""></th>
		<th></th>
		<th><input type="text" name="gost_name_filter" id="gost_name_filter" style="margin:0px" value=""></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
    <!--</tr>-->
	<div id='buttonRightTable'></div>
	<div id='buttonLeftTable'></div>
    <?php
	$i=0;
	
    while ($row = $res->Fetch()) {
		$color="style='background-color: green; color: green'";
		$title="title='С методикой все ОК'";
		$c=1;
		$res_ob = $DB->Query("SELECT * FROM `ba_connection` WHERE `GOST_ID` = " . $row['ID'])->Fetch();
		$resVerif = $DB->Query("SELECT * FROM `OA_VV` WHERE `ID_GOST` = " . $row['ID']);
		$maxDateVerif = '';
		while($resV = $resVerif->Fetch())
		{
				if(strtotime($resV['DATE']) > strtotime($maxDateVerif))
					$maxDateVerif = $resV['DATE'];
		}
		//var_dump($res_ob);
		
		if(!$row['NAME_GOST']||!$row['GOST_OBJECT']||!$res_ob&&$row['GOST_TYPE']!='TU_standart'&&$row['GOST_TYPE']!='TU_research'&&$row['GOST_TYPE']!='metodic_otbor'&&$row['GOST_TYPE']!='TU_group'||!$row['CHECKED']) {$color="style='background-color: #fbb; color: #fbb'"; $c=2; $title="title='В методике заполнены не все требуемые поля!'";}
		if($row['NON_ACTUAL']) {$color="style='background-color: grey; color: grey'"; $c=3; $title="title='Методика неактуальна'";}
        echo "<tr>";
		echo "<td " . $color . " " .$title ." >" . $c . "</td>";
		echo "<td>" . ($row['NUM_OA'] ? $row['NUM_OA'] : '') . "</td>";
		echo "<td>" . (($row['SPECIFICATION']) ? $row['SPECIFICATION'] : 'Не заполнено') . '' . "</td>";
        echo "<td> <a href='/obl_acc.php?ID=" . $row['ID'] . "'>" . $row['GOST'] . "</a> </td>";
		echo "<td>" . $row['GOST_OBJECT'] . "</td>";
		echo "<td>" . $row['GOST_PUNKT'] . "</td>";
        /*echo "<td>" . $row['GOST_DOC'] . "</td>";
        echo "<td>" . $row['GOST_VAL'] . "</td>";*/
		echo "<td>" . $row['NAME_GOST'] . "</td>";
		echo "<td>" . (($row['GOST_TYPE']=='TU_standart'||$row['GOST_TYPE']=='TU_research'||$row['GOST_TYPE']=='TU_group') ? 'ТУ' : 'Методика') . "</td>";
		echo "<td>" . $row['GOST_YEAR'] . "</td>";
        echo "<td>" . ($row['NORM1']) . "</td>";
        echo "<td>" . ($row['NORM2']) . "</td>";
        echo "<td>" . $row['ED'] . "<sup>".($row['ED_INDEX'] ? $row['ED_INDEX'] : '') ."</sup></td>";
        echo "<td>" . $row['PRICE'] . "</td>";
		//echo "<td>" . $row['COMMENT'] . "</td>";
		echo "<td>" . (($row['IN_OA'] == 1) ? 'Да':'Нет') . "</td>";
		echo "<td>" . ($maxDateVerif ? ruDate($maxDateVerif) : '-') . "</td>";
		//echo "<td><a href='/obl_acc.php?ID=". $row['ID'] . "&del=true'>Удалить</a></td>";
		$i++;
    }
	
    echo "</tbody></table>";
}
	$gostForForm = [];
	$arrTU = [];
	$CdbGostForm = $DB->Query("SELECT `ID`, `GOST`, `SPECIFICATION` FROM `ba_gost`");
	while ($gostForm = $CdbGostForm->Fetch()) {
		$gostForForm[] = $gostForm;
		$arrTU[$gostForm['ID']] = [
			'GOST' => $gostForm['GOST'],
			'ID' => $gostForm['ID'],
			'SPECIFICATION' => $gostForm['SPECIFICATION'],
		];
	}
	if (!empty($_GET['ID'])) {
		$tuForForm = [];
		$tuForGost = $DB->Query("SELECT `ID_TU` FROM `ba_gost` WHERE `ID` = {$_GET['ID']}")->Fetch();
		$idTuForForm = json_decode($tuForGost['ID_TU'], true);
		foreach ($idTuForForm as $tu) {
			$tuForForm[] = $arrTU[$tu];
		}
	}
	?>
	<form id="tu-method" method="post" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="tu_for_method.php">
		<div class="title mb-3 h-2">
			Привязка технического условия к методике
		</div>
		<input type="hidden" name="id" value="<?=$_GET['ID']?>">

		<div class="line-dashed-small"></div>
		<div class="form-group mb-3">
			<label for="select-gost" class="form-label">Выберите ТУ для привязки к методике:</label>
			<br>
			<select id="select-gost" class="form-control" style="width: 100%;">
				<option value="0" selected disabled>Выберите</option>
				<?php foreach ($gostForForm as $optionGost): ?>
					<option value="<?= $optionGost['ID'] ?>" data-id="<?= $optionGost['ID'] ?>"
							data-gost="<?= $optionGost['GOST'] ?>"
							data-spec="<?= $optionGost['SPECIFICATION'] ?>"><?= $optionGost['GOST'] ?>
						|| <?= $optionGost['SPECIFICATION'] ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form-group">
			<table class="table table-hover w-100 mb-3" id="table-gost">
				<thead>
				<tr>
					<th scope="col">ГОСТ</th>
					<th scope="col">Определяемая характеристика</th>
					<th scope="col"></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($tuForForm as $arrGost): ?>
					<tr>
						<td>
							<a href="/obl_acc.php?ID=<?= $arrGost['ID'] ?>" target="_blank"><?= $arrGost['GOST'] ?></a>
							<input
									type="hidden" value="<?= $arrGost['ID'] ?>"
									name="arrGost[]">
						</td>
						<td><?= $arrGost['SPECIFICATION'] ?></td>
						<td>
							<button type="button" class="btn del-gost">X</button>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<button type="submit" class="btn btn-success">Сохранить</button>
	</form>
	<script>
        var s = document.getElementById("second");
        var ids = document.getElementById("ids_oborud");
        document.getElementById("first").onchange  = function () {
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
	
	<!---'Правки протоколов'  начало---->
	<!--'Подключаем текстовый редактор'-->
    <script type="text/javascript" src="/tinymce/tinymce.min.js"></script>
	<!--'Настраиваем текстовый редактор'-->
    <script>tinymce.init({ 
		//Текстовые поля к которым будет подключен текстовый редактор
       //selector:'input.units, input.feature',
		//Если поля tinymce не пустые то сохраняем, для полей обезательных к заполнению (require)
		setup: function (editor) {
			editor.on('change', function () {
				tinymce.triggerSave();
			});
		},
		//Настраиваем отображение меню (1 строка)
		//superscript - надстрочный индекс, subscript - подстрочный индекс
		menu: {
			format: { title: 'Format', items: 'superscript subscript' },
		},
		//Отключаем дополнительное меню (2 строка)
		toolbar: false,
		plugins : "paste", //плагин paste, отвечает за обработку вставленного в редактор текста.
		paste_remove_styles: true, //удаляет CSS стили
		paste_remove_spans: true, //удаляет теги span
		paste_strip_class_attributes: 'all', // удаляет атрибуты class
		paste_block_drop: true, // блокирует вставку с помощью drag&drop.
    });
    
    </script>
    <!---'Правки протоколов'  окончание---->
	
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
	<link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
	<script src="/ulab/assets/plugins/magnific-popup/jquery.magnific-popup.min.js"></script>
	<script>
//Правки протоколов начало
//$('.btn_save_obl_acc') - кнопка сохранить
$('.btn_save_obl_acc').on('click', function() {
	//Проверяем если поле пустое в tinyMCE выводим alert
	/*if (tinyMCE.activeEditor.getContent() == '') {
	  alert('Введиет единицу измерения!');
	}*/	

    //Проверяем если поле пустое в tinyMCE выводим alert
	/*if ($(".units").val() == '') {
		alert('Заполните поле "Единица измерения"!');
	} else if ($(".feature").val() == '') {
		alert('Заполните поле "Определяемая характеристика"!');
	}*/
});
<!--'Правки протоколов'окончание-->
</script>
	<script>
		$(document).ready(function () {

			let body = $('body')

			$('.popup-with-form').magnificPopup({
				type: 'inline',
				closeBtnInside:true,
				fixedContentPos: false
			})

			$.magnificPopup.instance._onFocusIn = function (e) {
				if ($(e.target).hasClass('select2-search__field')) {
					return true;
				}
				$.magnificPopup.proto._onFocusIn.call(this, e);
			}

			$('#select-gost').select2({
			})

			$('#select-gost').on('change', function () {
				let gostId = $('#select-gost option:selected').data('id')
				let gost = $('#select-gost option:selected').data('gost')
				let spec = $('#select-gost option:selected').data('spec')
				let tr = `<tr>
                    <td><a href="/obl_acc.php?ID=${gostId}" target="_blank">${gost}</a><input type="hidden" value="${gostId}" name="arrGost[]"></td>
                    <td>${spec}</td>
                    <td><button type="button" class="btn btn-outline-secondary del-gost">X</button></td>
                </tr>`

				if ($('#table-gost tbody tr').length !== 0 ) {
					$('#table-gost tbody tr:first').before(tr)
				} else {
					$('#table-gost tbody').append(tr)
				}
			})

			body.on('click', '.del-gost', function () {
				$(this).closest('tr').remove()
			})

			$('.obl2').select2({
				placeholder: "Выберите"

			});
			$('.oborud_change').select2({
				placeholder: "Выберите"

			});
		});
	</script>

<script>
	var docType = document.getElementById('docType'),
	resText = document.querySelector("input[name=RES_TEXT]");
	docType.addEventListener('change', function() {
	if (docType['value'] === 'TU_sred' || docType['value'] === 'TU_sred2' ||  docType['value'] === 'TU_sred3' || docType['value']=== 'TU_sred4') {
		document.querySelector("input[name=RES_TEXT]").disabled = true;
		document.querySelector("input[name=RES_TEXT]").title = 'Для данного типа документа не допускаются текстовые значения';
	}  else {
		document.querySelector("input[name=RES_TEXT]").disabled = false;
		document.querySelector("input[name=RES_TEXT]").title = '';
	}

	});
	
</script>

<!--	<script>-->
<!--	 $(document).ready(function () {-->
<!--            -->
<!--			$('.obl2').select2({-->
<!--				placeholder: "Выберите"-->
<!--				-->
<!--			});-->
<!--			$('.oborud_change').select2({-->
<!--				placeholder: "Выберите"-->
<!--				-->
<!--			});-->
<!--	 });-->
<!--	</script>-->
	<script>
        $(document).ready(function () {
            <!--'При наведении мыши на списки методики, подключаем select2'-->
            /*$('#form_tz').on('mouseenter', '.styleGost', function () {
                $(this).select2();
            });*/

            $('#tz').on('mouseenter', '.tdGost', function () {
                //console.log('mouseenter this', $(this));
                $(this).find('.styleGost').select2();
            });

            $('#tz').on('mouseleave', '.tdGost', function () {
                var focusedName = document.activeElement.className;

                if (focusedName == 'select2-search__field') {
                    return true;
                }

                $(".styleGost.select2-hidden-accessible").select2('destroy');
            });

            <!--'После выбора методики отлючаем select2'-->
            $('#tz').on('change', '.styleGost', function (i, obj) {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    setTimeout(function () {
                        $(".styleGost.select2-hidden-accessible").select2('destroy');
                    }, 0);
                }
            });

            <!--'При попадании в фокус элемента, проверяем, если элемент не является списком методик, то отключаем select2 у оставшихся подключенных методик'-->
            $('#tz').on('focusin input', function (event) {
                var targetElement = event.target;
                if (!$(targetElement).is(".select2-selection")) {
                    $(".styleGost.select2-hidden-accessible").select2('destroy');
                }
            });

        });
    </script>
	<script>
        $(document).ready(function () {
            var table = $('#gost-table').DataTable({
				"sDom": 'Bfrtlip',
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
				// "scrollY": true,
				// "scroller": {
					// "rowHeight": 30
				// },
				"columnDefs": [ 
				{
				  "targets": 2,
				  "render": $.fn.dataTable.render.ellipsis( 100, true )
				},
				{
				  "targets": 3,
				  "render": $.fn.dataTable.render.ellipsis( 100, true )
				},
				{
				  "targets": 4,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 6,
				  "render": $.fn.dataTable.render.ellipsis( 60, true )
				},
				{"sType": "ruDate", "targets": 14}
				],
				"iDisplayLength": 50,
				"order": [[ 1, "asc" ]],
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
			
			$('#num_filter').on( 'keyup', function () {
				table
					.columns( 1 )
					.search( this.value )
					.draw();
			} );
			
			$('#characteristic_filter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			} );
			
			$('#normative_filter').on( 'keyup', function () {
				table
					.columns( 3 )
					.search( this.value )
					.draw();
			} );
			
			$('#gost_object_filter').on( 'keyup', function () {
				table
					.columns( 4 )
					.search( this.value )
					.draw();
			} );
			
			$('#gost_name_filter').on( 'keyup', function () {
				table
					.columns( 6 )
					.search( this.value )
					.draw();
			} );
        });
		
		jQuery.extend(jQuery.fn.dataTableExt.oSort, {
            "ruDate-asc": function (a, b) {
                var ruDatea = $.trim(a).split('.');
                var ruDateb = $.trim(b).split('.');
				
				if(ruDatea[0]=='-'&&ruDateb[0]=='-') return 0;
				if(ruDatea[0]=='-'&&ruDateb[0]!='-') return 1;
				if(ruDatea[0]!='-'&&ruDateb[0]=='-') return -1;
				
					if (ruDatea[2] * 1 < ruDateb[2] * 1)
						return 1;
					if (ruDatea[2] * 1 > ruDateb[2] * 1)
						return -1;
					if (ruDatea[2] * 1 == ruDateb[2] * 1) {
						if (ruDatea[1] * 1 < ruDateb[1] * 1)
							return 1;
						if (ruDatea[1] * 1 > ruDateb[1] * 1)
							return -1;
						if (ruDatea[1] * 1 == ruDateb[1] * 1) {
							if (ruDatea[0] * 1 < ruDateb[0] * 1)
								return 1;
							if (ruDatea[0] * 1 > ruDateb[0] * 1)
								return -1;
						} else
							return 0;
					}
				
            },

            "ruDate-desc": function (a, b) {
                var ruDatea = $.trim(a).split('.');
                var ruDateb = $.trim(b).split('.');
				if(ruDatea[0]=='-'&&ruDateb[0]=='-') return 0;
				if(ruDatea[0]=='-'&&ruDateb[0]!='-') return -1;
				if(ruDatea[0]!='-'&&ruDateb[0]=='-') return 1;

                if (ruDatea[2] * 1 < ruDateb[2] * 1)
                    return -1;
                if (ruDatea[2] * 1 > ruDateb[2] * 1)
                    return 1;
                if (ruDatea[2] * 1 == ruDateb[2] * 1) {
                    if (ruDatea[1] * 1 < ruDateb[1] * 1)
                        return -1;
                    if (ruDatea[1] * 1 > ruDateb[1] * 1)
                        return 1;
                    if (ruDatea[1] * 1 == ruDateb[1] * 1) {
                        if (ruDatea[0] * 1 < ruDateb[0] * 1)
                            return -1;
                        if (ruDatea[0] * 1 > ruDateb[0] * 1)
                            return 1;
                    } else
                        return 0;
                }
            }
        });
		
    </script> 
<script>
	// $("tbody[name=new]").on("click", ".add", function () {
		   // table = $(this).parent().parent().parent().parent();
		   // tbody = $(this).parent().parent().parent();
			// tr = $(this).parent().parent();
			// tr_dataid = tr.attr('id');
			// console.log(tr_dataid);
			// $.ajax({

			  // url: 'select_for_newrequest.php?KEY='+tr_dataid,

			  // success: function(data) {

				// tr.after(data);

			  // }
			
			// });
			
	
		
	// });
</script>
	
<script>
	window.onload = function (){
	
	var status = document.getElementById("status").value;
	
	if (status!="1"&&status!="10"&&status!="22"&&status!="12"&&status!="11"&&status!="13"&&status!="16"&&status!="64"&&status!="15"&&status!="34"&&status!="33"&&status!="24"&&status!="35"&&status!="13"&&status!="17"&&status!="21"&&status!="20"&&status!="58"&&status!="45"&&status!="43"&&status!="31"&&status!="62"&&status!="61"&&status!="69"&&status!="101"&&status!="100")
	{
		var input = document.querySelectorAll("input");
		var select = document.querySelectorAll("select");
		for (var i = 0, cnt = input.length; i < cnt; i++) {
			input[i].disabled = true;
		}
		for (var i = 0, cnt = select.length; i < cnt; i++) {
			select[i].disabled = true;
		}
		
	}
	
	};
	
	

	</script>	
	
	<script>
	//Прокрутка таблицы с помощью кнопок
	$(document).ready(function () {
		
		var container = $('.workarea-content-paddings'),
			scroll = $('.dataTable.no-footer').width(),
			topTable = $('.workarea-content-paddings').offset().top,
			topTableBody = $('.dataTable.no-footer > tbody').offset().top,
			sumHeaderHeight = topTableBody - topTable;
		
			$('#buttonRightTable').css('top', sumHeaderHeight + "px");
			$('#buttonLeftTable').css('top', sumHeaderHeight + "px");
			
		$('#buttonRightTable').hover(function() {
			container.animate({
				'scrollLeft': scroll
			},{duration: 4000, queue: false});
		}, function(){
			container.stop();
		});
		$('#buttonLeftTable').hover(function() {
			container.animate({
				'scrollLeft': -scroll
			},{duration: 4000, queue: false});
		}, function(){
			container.stop();
		});

		$(document).scroll(function() {
			var positionScroll = $(window).scrollTop();
			if (positionScroll > 250 && positionScroll < 2550) {
				$('#buttonRightTable').css('transform','translateY(' + (positionScroll-250) +  'px' + ')');
				$('#buttonLeftTable').css('transform','translateY(' + (positionScroll-250) +  'px' + ')');
			}
		});

	});
	$("#tz").on("click", ".del", function () {
                $(this).parent().parent().remove();
            });
	
	$("#tz").on("click", ".add2", function () {

                tbody = $(this).parent().parent().parent();
                tr = $(this).parent().parent();
                tr_dataid = tr.attr('id');

                $.ajax({

                    url: 'select_for_OA.php?KEY=' + tr_dataid,

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
	let $body = $("body")
	$body.on("click", ".add-infelicity", function () {
		let $formGroupContainer = $(this).parents('.input-group-obl-acc')
		// let countAddedEmail = $('.input-group-obl-acc').length + 1
		$formGroupContainer.after(
			`<div class="input-group-obl-acc mb-3">
				<span class="input-group-text-obl-acc">От</span>
				<input type="number" step="0.001" name="diapasonFrom[]" value="">
				<span class="input-group-text-obl-acc">До</span>
				<input type="number" step="0.001" name="diapasonTo[]" value="">
				<span class="input-group-text-obl-acc">U(w)</span>
				<input type="number" step="0.001" name="infelicity[]" value="">
				<input style='margin-left:5px' type='button' class='add-infelicity button-obl-acc' value='+'>
				<input style='margin-left:5px' type='button' class='del-infelicity button-obl-acc' value='-'>
			</div>`
		)
	})
	$body.on("click", ".del-infelicity", function () {
		let $formGroupContainer = $(this).parents('.input-group-obl-acc')

		$formGroupContainer.remove()
	})
	</script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
