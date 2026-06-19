<?php
//Форма градуировочной зависимости

//require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php"; 
?>

<datalist id="clients">
	<?php
	//Находим всех клиентов и создаем список клиентов с ID
	if (CModule::IncludeModule("crm")) {
		
		$all_company = CCrmCompany::GetList($arOrder = ['ID' => 'ASC']);
		if (!empty($all_company)) {
			while($arr_company = $all_company->fetch()) {
				
				$title = !empty($arr_company['TITLE']) ? $arr_company['TITLE'] : '';
				$id = !empty($arr_company['ID']) ? $arr_company['ID'] : '';
				
				echo "<option data-id='" . $id . "' value='" . $title . ' , ID ' . $id ."'></option>";
			}
		}
	}
    ?>
</datalist>

<?php
if (!empty($_POST['client'])) { 
	$id_grad = !empty($_POST['id_grad']) ? (int)$_POST['id_grad'] : '';
	$date_grad = !empty($_POST['date_grad']) ? date("Y-m-d", strtotime($_POST['date_grad'])) : '';
	$temp = !empty($_POST['temp']) ? $_POST['temp'] : ''; //Температура
	$humid = !empty($_POST['humid']) ? $_POST['humid'] : ''; //Влажность
	$object = !empty($_POST['object']) ? $_POST['object'] : ''; //Обьект строительства
	$client = !empty($_POST['client']) ? $_POST['client'] : '';
	$arr_data_client = !empty($_POST['client']) ? explode(', ID ', $_POST['client']) : ''; //Заказчик
	$client_id = !empty($arr_data_client[1]) ? $arr_data_client[1] : ''; //id Заказчика
	$name = !empty($_POST['name']) ? $_POST['name'] : '';
	$name_serializ = !empty($_POST['name']) ? serialize($_POST['name']) : '';
	$once_val1 = !empty($_POST['once_val1']) ? $_POST['once_val1'] : ''; //Единичные значения
	$once_val1_serializ = !empty($_POST['once_val1']) ? serialize($_POST['once_val1']) : ''; //Единичные значения
	$once_val2 = !empty($_POST['once_val2']) ? $_POST['once_val2'] : ''; //Единичные значения$once_val1 = !empty($_POST['once_val1']) ? serialize($_POST['once_val1']) : ''; //Единичные значения
	$once_val2_serializ = !empty($_POST['once_val2']) ? serialize($_POST['once_val2']) : ''; //Единичные значения
	$prochn_separation = !empty($_POST['prochn_separation']) ? $_POST['prochn_separation'] : ''; //Прочность бетона на участке методом отрыва со скалыванием, МПа
	$prochn = !empty($_POST['prochn']) ? $_POST['prochn'] : '';
	//$prochn_serializ = !empty($_POST['prochn']) ? serialize($_POST['prochn']) : '';
	$prochn_serializ = !empty($_POST['prochn_separation']) ? serialize($_POST['prochn_separation']) : '';
	
	$arr_mean =  []; //Среднее значение на участке
	foreach($once_val1 as $key_once => $val1) {
		$val1 = !empty($val1) ? tofloat($val1) : 0;
		$val2 = !empty($once_val2[$key_once]) ? tofloat($once_val2[$key_once]) : 0;
		//echo $val1. "  ".$val2. ";";
		$arr_mean[] = $val1 / $val2;
	}
	
	
	
	$arr_mean_serializ = !empty($arr_mean) ? serialize($arr_mean) : ''; 
	
	//V, мс
	//$count_mean = count($arr_mean);
	$count_mean = !empty(count($arr_mean)) ? count($arr_mean) : 0;

	//$_V = array_sum($arr_mean) / $count_mean; //округляем до целого числа
	$_V = !empty($count_mean) ? array_sum($arr_mean) / $count_mean : 0; //округляем до целого числа

	//$sq_prochn_separation_and_grad = [];
	//Прочность бетона на участке методом отрыва со скалыванием, МПа
	// $count_prochn = count($prochn_separation);
	// $R_grad = 0;
	// foreach($prochn_separation as $key_prochn => $val_prochn) {
		// $val_prochn = str_replace(',', '.', $val_prochn); //Заменяем запятую на точку
		// $R_grad += $val_prochn; //Суммируем всех значений
		
		// //$prochn_grad = str_replace(',', '.', $prochn[$key_prochn]); //Заменяем запятую на точку
		
		// //$sq_prochn_separation_and_grad[] = pow(($val_prochn - $prochn_grad), 2); //pow(val,2) в квадрате, возведение в степень
	// }
	
	
	// //$_R = $R_grad / $count_prochn; 
	// $_R = !empty($count_prochn) ? $R_grad / $count_prochn : 0; 
	

	// //a
	// $arr_prochn_R = [];
	// foreach($prochn_separation as $key_prochn => $val_prochn) {
		// $val_prochn = str_replace(',', '.', $val_prochn); //Заменяем запятую на точку
		// $arr_prochn_R[] = $val_prochn - $_R; 
	// }
	
	// $arr_mean_V = [];
	// $denominator_mean_V = [];
	
	// foreach($arr_mean as $key_mean => $val_mean) {
		// $val_mean = str_replace(',', '.', $val_mean); //Заменяем запятую на точку
		// $arr_mean_V[] = $val_mean - $_V;
		// $denominator_mean_V[] = pow(($val_mean - $_V), 2); //pow(val,2) в квадрате, возведение в степень
	// }

	// $prochn_R_mean_V = [];
	// foreach($arr_prochn_R as $key_prochn_R => $val_prochn_R) {
		// $prochn_R_mean_V[] = $val_prochn_R * $arr_mean_V[$key_prochn_R];
	// }

	// $numerator = array_sum($prochn_R_mean_V); //Числитель

	// $denominator = array_sum($denominator_mean_V); //Знаминатель
	
	// //$a = $numerator / $denominator; 
	// $a = !empty($denominator) ? $numerator / $denominator : 0; 
	
	// $b = $_R - $a * $_V; //округляем 1 цифра после запятой
	
	// //Прочность бетона по градуировочной зависимости, МПа
	// $arr_prochn_grad = [];
	// foreach($arr_mean as $key_m => $val_m) {
		// $val_m = str_replace(',', '.', $val_m); //Заменяем запятую на точку
		// $arr_prochn_grad[] = $val_m * $a + $b;
	// }
	
	// $prochn_grad_serializ = !empty($arr_prochn_grad) ? serialize($arr_prochn_grad) : '';
	
	// $sq_prochn_separation_and_grad = [];
	// foreach($prochn_separation as $key_prochn => $val_prochn) {
		// $val_prochn = str_replace(',', '.', $val_prochn); //Заменяем запятую на точку
		
		// $prochn_grad = str_replace(',', '.', $arr_prochn_grad[$key_prochn]); //Заменяем запятую на точку
		
		// $sq_prochn_separation_and_grad[] = pow(($val_prochn - $prochn_grad), 2); //pow(val,2) в квадрате, возведение в степень
	// }
	
	// //S, мПа
	// $count_sq_prochn_separation_and_grad = count($sq_prochn_separation_and_grad);
	// //$_S = sqrt(array_sum($sq_prochn_separation_and_grad) / ($count_sq_prochn_separation_and_grad - 2)); //sqrt(val) - квадратный корень
	// $_S = !empty($count_sq_prochn_separation_and_grad) ? sqrt(array_sum($sq_prochn_separation_and_grad) / ($count_sq_prochn_separation_and_grad - 2)) : 0; //sqrt(val) - квадратный корень
	
	// //SRcp
	// //$_SR = $_S / $_R;
	// $_SR = !empty($_R) ? $_S / $_R : 0;
	
	// $prochn_grad_R_and_separation_R = [];
	// $sq_prochn_grad_R = [];
	// $sq_prochn_separation_R = [];
	// //Условие отбраковки единичных результатов испытаний |RiH-Riф|/S
	// $condition = [];
	// //foreach($prochn as $key_prochn_grad => $val_prochn_grad) {
	// foreach($arr_prochn_grad as $key_prochn_grad => $val_prochn_grad) {
		// $val_prochn_grad = str_replace(',', '.', $val_prochn_grad); //Заменяем запятую на точку
		// $val_prochn_separation = str_replace(',', '.', $prochn_separation[$key_prochn_grad]); //Заменяем запятую на точку
		
		// //$condition[] = abs(($val_prochn_grad - $prochn_separation[$key_prochn_grad]) / $_S); //abs — Абсолютное значение (модуль числа)
		// $condition[] = abs(($val_prochn_grad - $val_prochn_separation) / $_S); //abs — Абсолютное значение (модуль числа)
		
		// $prochn_grad_R_and_separation_R[] = ($val_prochn_grad - $_R) * $arr_prochn_R[$key_prochn_grad];
		
		// $sq_prochn_separation_R[] = pow($arr_prochn_R[$key_prochn_grad], 2);
		
		// $sq_prochn_grad_R[] = pow(($val_prochn_grad - $_R), 2);
	// }
	
	// $condition_serialize = !empty($condition) ? serialize($condition) : '';
	
	// $sum_prochn_grad_R_and_separation_R = array_sum($prochn_grad_R_and_separation_R);
	
	// //$sum_sq_prochn_separation_R = sqrt(array_sum($sq_prochn_separation_R));
	// $sum_sq_prochn_separation_R = !empty(array_sum($sq_prochn_separation_R)) ? sqrt(array_sum($sq_prochn_separation_R)) : 0;
	
	// //$sum_sq_prochn_grad_R = sqrt(array_sum($sq_prochn_grad_R));
	// $sum_sq_prochn_grad_R = !empty(sqrt(array_sum($sq_prochn_grad_R))) ? sqrt(array_sum($sq_prochn_grad_R)) : 0;
	
	// //r
	// //$r = $sum_prochn_grad_R_and_separation_R / ($sum_sq_prochn_grad_R * $sum_sq_prochn_separation_R);   
	// $r = !empty($sum_sq_prochn_grad_R * $sum_sq_prochn_separation_R) ? $sum_prochn_grad_R_and_separation_R / ($sum_sq_prochn_grad_R * $sum_sq_prochn_separation_R) : 0;   
	
	// $SR_result = '';
	// if ($_SR < 0.15) {
		// $SR_result = 'Сооответствует';
	// } else {
		// $SR_result = 'Не соответствует';
	// }
	
	// $r_result = '';
	// if ($r > 0.7) {
		// $r_result = 'Сооответствует';
	// } else {
		// $r_result = 'Не соответствует';
	// }

	
	$select_grad_dep = null;
	if(!empty($id_grad)) {
		$select_grad_dep = $DB->Query("SELECT * FROM `COINCED_COEFF` WHERE `ID`=" . $id_grad)->Fetch();
	}
	
	if (!empty($select_grad_dep['ID'])) {
		$update_grad_dep = $DB->Query("UPDATE `COINCED_COEFF` SET `CLIENT_ID`=" . $client_id . ", `OBJECT`='" . $DB->ForSql($object) . "', 
		`NAME`='" . $name_serializ . "', `DATE`='" . $date_grad . "', `PROCHN_PM`='" .$once_val1_serializ  . "', `PROCHN_KM`='" .$once_val2_serializ  . "', 
		`PROCHN_CHAST`='" . $arr_mean_serializ . "', `TEMP`='" . $temp . "', `HUMID`='" . $humid . "',  `COEFF`=" . $_V . "  WHERE `ID`=" . $id_grad);
	} else {
		$insert_grad_dep = $DB->Query("INSERT INTO `COINCED_COEFF` (`CLIENT_ID`, `OBJECT`, `NAME`, `DATE`, `PROCHN_PM`, `PROCHN_KM`, `PROCHN_CHAST`, `TEMP`, `HUMID`, `COEFF`) 
	VALUES (" . $client_id . ", '" . $DB->ForSql($object) . "', '" . $name_serializ . "', '" . $date_grad . "', '" .$once_val1_serializ  . "', '" .$once_val2_serializ  . "', '" . $arr_mean_serializ . "', '" . $temp . "', '" . $humid . "',  " . $_V . ")");
	}
}
?>


<?php
if (!empty($_GET['ID']) ) {
	$select_grad_dep = $DB->Query("SELECT * FROM `COINCED_COEFF` WHERE `ID`=" . $_GET['ID'])->Fetch();
	
	$id_grad = !empty($select_grad_dep['ID']) ? (int)$select_grad_dep['ID'] : '';
	$date_grad = !empty($select_grad_dep['DATE']) ? $select_grad_dep['DATE'] : '';
	$temp = !empty($select_grad_dep['TEMP']) ? $select_grad_dep['TEMP'] : ''; //Температура
	$humid = !empty($select_grad_dep['HUMID']) ? $select_grad_dep['HUMID'] : ''; //Влажность
	$object = !empty($select_grad_dep['OBJECT']) ? $select_grad_dep['OBJECT'] : ''; //Обьект строительства
	$client_id = !empty($select_grad_dep['CLIENT_ID']) ? $select_grad_dep['CLIENT_ID'] : ''; //id Заказчика
	$arr_name = !empty($select_grad_dep['NAME']) ?  unserialize($select_grad_dep['NAME']) : '';
	$arr_once_val1 = !empty($select_grad_dep['PROCHN_PM']) ?  unserialize($select_grad_dep['PROCHN_PM']) : '';
	$arr_once_val2 = !empty($select_grad_dep['PROCHN_KM']) ?  unserialize($select_grad_dep['PROCHN_KM']) : '';
	$arr_prochn = !empty($select_grad_dep['PROCHN_CHAST']) ? unserialize($select_grad_dep['PROCHN_CHAST']) : '';
	
	$val_coeff = !empty($select_grad_dep['COEFF']) ? round($select_grad_dep['COEFF'], 2) : '';
	
	$deal = null;
	if (!empty($client_id)) {
		$deal = CCrmCompany::GetByID($client_id);
	}
	
	$title = !empty($deal['TITLE']) ? $deal['TITLE'] : '';	
?>
<form id="form_grad_dep" method="post">
	<div class="link-grad"><a href="/coin_coeff.php?TABLE=TABLE" class="table-grad">Вернуться к журналу</a> | <a href="/coin_coeff.php">Создать лист расчета </a></div>
	<div class="header-form">
		<div class="field">
			<label for="id_grad" class="label">Лист расчета №</label>
			<div class="col-right">
				<input type="number" id="id_grad" name="id_grad" class="input-grad id-grad" value="<?= $id_grad ?>"> от
				<input type="date" name="date_grad" class="input-grad date_grad" value="<?= $date_grad ?>">
			</div>
		</div>
		<p class="circs">Условия проведения оспытаний</p>
		<div class="field">
			<label for="temp" class="label">Температура</label>
			<div class="col-right">
			<input type="number" id="temp" step="0.01" name="temp" class="input-grad temp" value="<?= $temp ?>">
			</div>
		</div>
		<div class="field">
			<label for="humid" class="label">Влажность</label>
			<div class="col-right">
			<input type="number" id="humid" step="0.01" name="humid" class="input-grad humid" value="<?= $humid ?>">
			</div>
		</div>
		<div class="field">
			<label for="object" class="label">Обьект строительства</label>
			<div class="col-right">
			<input type="text" id="object" name="object" class="input-grad object" value='<?= $object ?>'>
			</div>
		</div>
		<div class="field">
			<label for="client" class="label">Заказчик</label>
			<div class="col-right">
			<input type="text" id="client" name="client" list="clients" class="input-grad client-id" value='<?= $title . ' , ID ' . $client_id ?>' required>
			</div>
		</div> 
	</div>
	
	<table id="table-grad">
		<thead class="thead">
			<tr>
				<th rowspan="3">Наименование, месторасположение и дата бетонирования конструкции</th>
				<th colspan="2">Прочность бетона участка конструкции на сжатие, МПа</th>
				<th rowspan="1" >Частное значение</th>
				<th rowspan="1" colspan="2">Проверка соблюдения условия для Roc/Rкосв</th>
			</tr>
			<tr>
				<th colspan="1">Прямым методом</th>
				<th rowspan="1">Косвенным методом</th>
				<th colspan="1">Rос/Rкосв</th>
				<th rowspan="1">0,7≤Rос/Rкосв≤1,3</th>
				<th colspan="1">0,85*Кс≤Rос/Rкосв≤1,15*Кс</th>
				
			</tr>
			
		</thead>
		<tbody>
		<?php
			$g = 0; 
			
				
			foreach($arr_name as $key_name => $name):
			
				$name = !empty($name) ?  $name : '';
				$once_val1 = !empty($arr_once_val1[$key_name]) ?  $arr_once_val1[$key_name] : '';
				$once_val2 = !empty($arr_once_val2[$key_name]) ?  $arr_once_val2[$key_name] : '';
				$prochn = !empty($arr_prochn[$key_name]) ? $arr_prochn[$key_name] : '';
				
				
				$val_prochn_grad = !empty($array_prochn_grad[$key_name]) ? number_format($array_prochn_grad[$key_name], 1, ',', ' ') : '0';
				
				$val_condition = !empty($arr_condition[$key_name]) ? number_format($arr_condition[$key_name], 2, ',', ' ') : '0';
				
				$value_mean = !empty($arr_mean[$key_name]) ? round($arr_mean[$key_name], 2) : '';
				
				$check_r1 = 'Соответствует';
				$check_r2 = 'Cоответствует';
				if($prochn<=0.7||$prochn>=1.3 ) $check_r1 = 'Не соответствует'; 
				if($prochn<=0.85*$val_coeff||$prochn>=1.15*$val_coeff ) $check_r2 = 'Не соответствует'; 
				
		?>
			<tr class="tr-grad">
				<td class="td-grad">
					<!--'Наименование, месторасположение и дата бетонирования конструкции'-->
					<input type="text" name="name[]" class="input-grad name" value="<?= $name ?>">
				</td>
				<td class="td-grad">
					<!--'Единичные значения'-->
					<input type="text" name="once_val1[]" class="input-grad once-val1" value="<?= $once_val1 ?>">
				</td>
				<td class="td-grad">
					<!--'Единичные значения'-->
					<input type="text" name="once_val2[]" class="input-grad once-val2" value="<?= $once_val2 ?>">
				</td>
				<td class="td-grad">
					<!--'Среднее значение на участке'-->
					<input type="text" name="mean" class="input-grad mean" value="<?= number_format($prochn , 2, ',', ' ')?>">
				</td>
				<td class="td-grad">
					<!--'Прочность бетона на участке методом отрыва со скалыванием, МПа'-->
					<input type="text" name="prochn_separation[]" class="input-grad prochn_separation" value="<?= $check_r1 ?>">
				</td>
				<td class="td-grad">
					<!--'Прочность бетона по градуировочной зависимости, МПа'-->
					<input type="text" name="prochn[]" class="input-grad prochn" value="<?= $check_r2 ?>">
				</td>
				
				<td class="td-btn td-grad">
					<?php
						if (empty($g)) {
							echo '<input type="button" class="add2" value="+">';
						} else {
							echo '<input type="button" class="add2" value="+">
								<input type="button" class="del" value="-">';
						}
					?>
				</td>
			</tr>
			<?php
			$g++; 
				endforeach;
			?>
		</tbody>
	</table>
	
	<div class="footer-form">
		
		<div class="field">
			<label for="V_grad">Коэффициент совпадения</label>
			<div class="col-right">
				<input type="number" name="V_grad" id="V_grad" class="input-grad V-grad" value="<?= $val_coeff ?>">
			</div>
		</div>
		
	</div>
	<button type="submit" class="btn btn-save">Сохранить</button>
</form>
	<?php
	} else if(!empty($_POST['client'])) { //------------------------------------'Вывод при сохранении в POST'---------------------------------//

		
		$select_last_grad_dep = $DB->Query("SELECT * FROM `COINCED_COEFF` ORDER BY ID DESC LIMIT 1")->Fetch();
		
		$last_grad_id = !empty($select_last_grad_dep['ID']) ? $select_last_grad_dep['ID'] : '';
	?>
	<form id="form_grad_dep" method="post">
		<div class="link-grad"><a href="/coin_coeff.php?TABLE=TABLE" class="table-grad">Вернуться к журналу</a> | <a href="/coin_coeff.php">Создать лист расчета</a></div>
		<div class="header-form">
		<div class="field">
			<label for="id_grad" class="label">Лист расчета №</label>
			<div class="col-right">
				<input type="number" id="id_grad" name="id_grad" class="input-grad id-grad" value="<?= $last_grad_id ?>"> от
				<input type="date" name="date_grad" class="input-grad date_grad" value="<?= $date_grad ?>">
			</div>
		</div>
		<p class="circs">Условия проведения испытаний</p>
		<div class="field">
			<label for="temp" class="label">Температура</label>
			<div class="col-right">
			<input type="number" id="temp" step="0.01" name="temp" class="input-grad temp" value="<?= $temp ?>">
			</div>
		</div>
		<div class="field">
			<label for="humid" class="label">Влажность</label>
			<div class="col-right">
			<input type="number" id="humid" step="0.01" name="humid" class="input-grad humid" value="<?= $humid ?>">
			</div>
		</div>
		<div class="field">
			<label for="object" class="label">Обьект строительства</label>
			<div class="col-right">
			<input type="text" id="object" name="object" class="input-grad object" value='<?= $object ?>'>
			</div>
		</div>
		<div class="field">
			<label for="client" class="label">Заказчик</label>
			<div class="col-right">
			<input type="text" id="client" name="client" list="clients" class="input-grad client-id" value='<?= $client ?>' required>
			</div>
		</div> 
	</div>
	
	<table id="table-grad">
		<thead class="thead">
			<tr>
				<th rowspan="3">Наименование, месторасположение и дата бетонирования конструкции</th>
				<th colspan="2">Прочность бетона участка конструкции на сжатие, МПа</th>
				<th rowspan="1" >Частное значение</th>
				<th rowspan="1" colspan="2">Проверка соблюдения условия для Roc/Rкосв</th>
			</tr>
			<tr>
				<th colspan="1">Прямым методом</th>
				<th rowspan="1">Косвенным методом</th>
				<th colspan="1">Rос/Rкосв</th>
				<th rowspan="1">0,7≤Rос/Rкосв≤1,3</th>
				<th colspan="1">0,85*Кс≤Rос/Rкосв≤1,15*Кс</th>
				
			</tr>
			
		</thead>
		<tbody>
		<?php
			$g = 0; 
			
				//echo'<pre>';var_dump($once_val1,$once_val2);
			foreach($name as $key_name => $val_name):
			
				$name = !empty($name) ?  $val_name : '';
				$once_val1_post = !empty($once_val1[$key_name]) ?  $once_val1[$key_name] : '0';
				$once_val2_post = !empty($once_val2[$key_name]) ?  $once_val2[$key_name] : '0';
				$mean = !empty($arr_mean[$key_name]) ? round($arr_mean[$key_name], 2) : '0'; 
				$prochn_separation_post = !empty($prochn_separation[$key_name]) ? $prochn_separation[$key_name] : '0';
				
				$condition_post = !empty($condition[$key_name]) ? number_format($condition[$key_name], 2, ',', ' ') : '0';
				//$condition_post = !empty($condition[$key_name]) ? round($condition[$key_name], 2) : '';
				
				$prochn_grad_post = !empty($arr_prochn_grad[$key_name]) ? number_format($arr_prochn_grad[$key_name], 1, ',', ' ') : '0';
				//$prochn_grad_post = !empty($arr_prochn_grad[$key_name]) ? round($arr_prochn_grad[$key_name], 1) : '';
				$check_r1 = 'Соответствует';
				$check_r2 = 'Cоответствует';
				if($mean<=0.7||$mean>=1.3 ) $check_r1 = 'Не соответствует'; 
				if($mean<=0.85*$_V||$mean>=1.15*$_V ) $check_r2 = 'Не соответствует';
				
		?>
			<tr class="tr-grad">
				<td class="td-grad">
					<!--'Наименование, месторасположение и дата бетонирования конструкции'-->
					<input type="text" name="name[]" class="input-grad name" value="<?= $name ?>">
				</td>
				<td class="td-grad">
					<!--'Единичные значения'-->
					<input type="text" name="once_val1[]" class="input-grad once-val1" value="<?= $once_val1_post ?>">
				</td>
				<td class="td-grad">
					<!--'Единичные значения'-->
					<input type="text" name="once_val2[]" class="input-grad once-val2" value="<?= $once_val2_post ?>">
				</td>
				<td class="td-grad">
					<!--'Среднее значение на участке'-->
					<input type="text" name="mean" class="input-grad mean" value="<?= $mean ?>">
				</td>
				<td class="td-grad">
					<!--'Прочность бетона на участке методом отрыва со скалыванием, МПа'-->
					<input type="text" name="prochn_separation[]" class="input-grad prochn_separation" value="<?= $check_r1 ?>">
				</td>
				<td class="td-grad">
					<!--'Прочность бетона по градуировочной зависимости, МПа'-->
					<input type="text" name="prochn[]" class="input-grad prochn" value="<?= $check_r2 ?>">
				</td>
				
				<td class="td-btn td-grad">
					<?php
						if (empty($g)) {
							echo '<input type="button" class="add2" value="+">';
						} else {
							echo '<input type="button" class="add2" value="+">
								<input type="button" class="del" value="-">';
						}
					?>
				</td>
			</tr>
			<?php
			$g++; 
				endforeach;
			?>
		</tbody>
	</table>
	
	<div class="footer-form">
		
		<div class="field">
			<label for="V_grad">Коэффициент совпадения</label>
			<div class="col-right">
				<input type="number" name="V_grad" id="V_grad" class="input-grad V-grad" value="<?= round($_V, 2) ?>">
			</div>
		</div>
		
	</div>
	<button type="submit" class="btn btn-save">Сохранить</button>
</form>
	<?php
	} else if (empty($_GET)) {
	?>
<form id="form_grad_dep" method="post">
	<div class="link-grad"><a href="/grad_dep.php?TABLE=TABLE" class="table-grad">Вернуться к журналу</a> | <a href="/grad_dep.php">Создать лист расчета</a></div>
	<div class="header-form">
		<div class="field">
			<label for="id_grad" class="label">Лист расчета №</label>
			<div class="col-right">
				<input type="number" id="id_grad" name="id_grad" class="input-grad id-grad" value=""> от
				<input type="date" name="date_grad" class="input-grad date_grad" value="">
			</div>
		</div>
		<p class="circs">Условия проведения испытаний</p>
		<div class="field">
			<label for="temp" class="label">Температура</label>
			<div class="col-right">
			<input type="number" id="temp" step="0.01" name="temp" class="input-grad temp" value="">
			</div>
		</div>
		<div class="field">
			<label for="humid" class="label">Влажность</label>
			<div class="col-right">
			<input type="number" id="humid" step="0.01" name="humid" class="input-grad humid" value="">
			</div>
		</div>
		<div class="field">
			<label for="object" class="label">Обьект строительства</label>
			<div class="col-right">
			<input type="text" id="object" name="object" class="input-grad object" value=''>
			</div>
		</div>
		<div class="field">
			<label for="client" class="label">Заказчик</label>
			<div class="col-right">
			<input type="text" id="client" name="client" list="clients" class="input-grad client-id" value='' required>
			</div>
		</div> 
	</div>
	
	<table id="table-grad">
		<thead class="thead">
			<tr>
				<th rowspan="3">Наименование, месторасположение и дата бетонирования конструкции</th>
				<th colspan="2">Прочность бетона участка конструкции на сжатие, МПа</th>
				<th rowspan="1" >Частное значение</th>
				<th rowspan="1" colspan="2">Проверка соблюдения условия для Roc/Rкосв</th>
			</tr>
			<tr>
				<th colspan="1">Прямым методом</th>
				<th rowspan="1">Косвенным методом</th>
				<th colspan="1">Rос/Rкосв</th>
				<th rowspan="1">0,7≤Rос/Rкосв≤1,3</th>
				<th colspan="1">0,85*Кс≤Rос/Rкосв≤1,15*Кс</th>
				
			</tr>
			
		</thead>
		<tbody>
			<tr class="tr-grad">
				<td class="td-grad">
					<!--'Наименование, месторасположение и дата бетонирования конструкции'-->
					<input type="text" name="name[]" class="input-grad name" value="">
				</td>
				<td class="td-grad">
					<!--'Прямой метод'-->
					<input type="text" name="once_val1[]" class="input-grad once-val1" value="">
				</td>
				<td class="td-grad">
					<!--'Косвенный метод'-->
					<input type="text" name="once_val2[]" class="input-grad once-val2" value="">
				</td>
				<td class="td-grad">
					<!--'Частное значение'-->
					<input type="text" name="mean" class="input-grad mean" value="">
				</td>
				<td class="td-grad">
					<!--'Проверка 1'-->
					<input type="text" name="prochn_separation[]" class="input-grad prochn_separation" value="">
				</td>
				<td class="td-grad">
					<!--'Проверка 2'-->
					<input type="text" name="prochn[]" class="input-grad prochn" value="">
				</td>
				
				<td class="td-btn td-grad">
					<input type="button" class="add2" value="+">
				</td>
			</tr>
		</tbody>
	</table>
	
	<div class="footer-form">
		
		<div class="field">
			<label for="V_grad">Коэффициент совпадения </label>
			<div class="col-right">
				<input type="number" name="V_grad" id="V_grad" class="input-grad V-grad" value="">
			</div>
		</div>
		
	</div>
	<button type="submit" class="btn btn-save">Сохранить</button>
</form>
	<?php
	}
	?>

<?php
	if (!empty($_GET['TABLE']) && $_GET['TABLE'] == 'TABLE') :
	$select_data_grad_dep = $DB->Query("SELECT * FROM `COINCED_COEFF`");
?>
<a href="/coin_coeff.php?">Создать лист расчета</a>
<h3>Журнал листов расчета</h3>
<table id="table_grad">
	<thead>
		<tr>
			<th>Номер</th>
			<th>Дата</th>
		</tr>
		<!--<tr>
			<th><input type="text" name="number_filter" id="number_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="dates_filter" id="dates_filter" style="margin:0px" value=""></th>
		</tr>-->
	</thead>
	<tbody>
		<?php
			while($data_grad_dep = $select_data_grad_dep->Fetch()):
			
			$id_grap_dep = !empty($data_grad_dep['ID']) ? $data_grad_dep['ID'] : '';
			$date_grap_dep = !empty($data_grad_dep['DATE']) ? date('d.m.Y', strtotime($data_grad_dep['DATE'])) : '';
		?>
			<tr>
				<td><a href="/coin_coeff.php?ID=<?= $id_grap_dep ?>"><?= $id_grap_dep ?></a></td>
				<td><?= $date_grap_dep ?></td>
			</tr>	
		<?php
			endwhile;
		?>
	</tbody>
</table>
<?php
endif;
?>

<script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    
	<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"/>-->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>
	
    
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
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/filtering/type-based/html.js"></script>		
<script>
//var jq = jQuery.noConflict();
//jq(function($) {
$(function() {
	/*table= $('#table_grad').DataTable({
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
				"columnDefs": [{ "type": "natural-nohtml", "targets": 1 }, { "sType": "ruDate", "targets": 2 }],
				"order": [[ 2, "asc" ]],
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
			
			$('#number_filter').on( 'keyup', function () {
				table
					.columns( 0 )
					.search( this.value )
					.draw();
			} );
			
			$('#dates_filter').on( 'keyup', function () {
				table
					.columns( 1 )
					.search( this.value )
					.draw();
			} );*/
	
	$('#form_grad_dep').on('click', '.add2', function() {
		
		var closestTrGrad = $(this).closest('.tr-grad'); //Строка таблицы
		var cloneTrGrad = $(closestTrGrad).clone(true); //Клонированная строка
		
		//Удаляем данные клонированной строки
		if (cloneTrGrad) {
			$(cloneTrGrad).find('.input-grad').each(function(index,input) {
				console.log(index,input);
				if ($(input).val()) {
					$(input).val('');
				}
			});
		}
		
		//Если клонированная строка не содержит кнопку удаления('.del'), то добавляем ее
		if (!$(cloneTrGrad).find('.del').length) {
			$(cloneTrGrad).find('.td-btn').append('<input type="button" class="del" value="-">');
		}
		
		//Добавляем после элемента
		if (closestTrGrad && cloneTrGrad) {
			closestTrGrad.after(cloneTrGrad);
		}
	});
	
	//Удаляем строку во время события click по кнопке удаления строки
	$("#form_grad_dep").on("click", ".del", function () {
		$(this).closest('.tr-grad').remove();
	});

});
</script>
