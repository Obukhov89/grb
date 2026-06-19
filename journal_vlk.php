<?php 
//Журнал ВЛК
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); 

$APPLICATION->SetTitle("Журнал шифрованных проб");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";


//$resultAllDataVlk = $DB->Query("SELECT * FROM `VLK`");
//$resultAllDataRequestVlk = $DB->Query("SELECT * FROM `ba_tz` WHERE `TYPE_ID`=1");

$where_condition = '';
switch ($_GET['year']) {
    case '2021':
        $where_condition = " AND (`DATE_SOZD` > '2020-12-31' AND `DATE_SOZD` < '2022-01-01') ";
        break;
    case '2019':
        $where_condition = " AND (`DATE_SOZD` > '2018-12-31' AND `DATE_SOZD` < '2020-01-01') ";
        break;
    case '2020':
        $where_condition = " AND (`DATE_SOZD` > '2019-12-31' AND `DATE_SOZD` < '2021-01-01') ";
        break;
    case '2022':
        $where_condition = " AND (`DATE_SOZD` > '2021-12-31' AND `DATE_SOZD` < '2023-01-01') ";
        break;
    case 'all':
        $where_condition = " AND (`DATE_SOZD` > '2019-01-01' AND `DATE_SOZD` < '2029-12-31') ";
        break;
}
$resultAllDataRequestVlk = $DB->Query("SELECT * FROM `ba_tz_vlk` WHERE `TYPE_ID` = 1 {$where_condition}");

?>

<!--Таблица VLK-->
<a href="/vlk.php">Перейти к графику ВЛК</a>
<select class="obl" name="lab_gost" id="year"
        onchange="window.location.assign('/journal_vlk.php?year='+this.options[this.selectedIndex].value)"
        style="float: right;">
        <option value='all' style='font-weight:bold' <?=$_GET['year'] == 'all' ? 'selected' : ''?>>Все года</option>
        <option value="2022" style='font-weight:bold' <?=$_GET['year'] == '2022' ? 'selected' : ''?>>2022</option>
        <option value="2021" style='font-weight:bold' <?=$_GET['year'] == '2021' ? 'selected' : ''?>>2021</option>
        <option value="2020" style='font-weight:bold' <?=$_GET['year'] == '2020' ? 'selected' : ''?>>2020</option>
        <option value='2019' style='font-weight:bold' <?=$_GET['year'] == '2019' ? 'selected' : ''?>>2019</option>

</select>
    <table id="vlk_table" class="table-vlk table-all">
	<thead>
		<tr>
			<!--Заявки ВЛК начало-->
			<th rowspan="2">Заявки</th>
			<!--Заявки ВЛК окончание-->
			<th rowspan="2">Дата регистрации</th>
			<th rowspan="2">Материал</th>
			<th rowspan="2">Шифр пробы<br>(№пробы в ИЦ/№ протокола)</th>
			<th rowspan="2">Конролируемый показатель</th>
			<th colspan="2">Номер НД на МИ</th>
			<th colspan="2">Результаты анализов</th>
			<th colspan="2">Фамилия исполнителя</th>
			<th rowspan="2">Результат<br> контрольной<br>процедуры</th>
			<th rowspan="2">Норматив контроля,<br>RЛ (dk, Кт)</th>
			<th rowspan="2">Отметка о дефектном<br>результате контрольной<br>процедуры (–)</th>
		</tr>
		<tr>
			<th>Первичного анализа</th>
		    <th>Повторного анализа</th>
			<th>первичного,</th>
		    <th>повторного,</th>
			<th>первичного результата<br> анализа</th>
		    <th>повторного результата<br> анализа</th>
		</tr>
		<tr>
			<!--Заявки ВЛК начало-->
			<!--Заявки-->
			<th>
				<input type="text" name="request" id="request" class="input-request" value="">
			</th>
			<!--Заявки ВЛК окончание-->
			<!--Дата регистрации-->
			<th>
				<input type="text" name="dateProbeFilter" id="dateProbeFilter" class="input-revise" value="">
			</th>
			<!--Материал-->
			<th>
				<input type="text" name="materialFilter" id="materialFilter" class="input-revise" value="">
			</th>
			<!--Шифр пробы(№пробы в ИЦ/№ протокола)-->
			<th>
				<input type="text" name="sampleCodeFilter" id="sampleCodeFilter" class="input-revise" value="">
			</th>
			<!--Конролируемый показатель-->
			<th>
				<input type="text" name="specificationNameFilter" id="specificationNameFilter" class="input-revise" value="">
			</th>
			<!--Номер НД на МИ(Первичного анализа)-->
			<th>
				<input type="text" name="gostNameStrFilter" id="gostNameStrFilter" class="input-revise" value="">
			</th>
			<!--Номер НД на МИ(Повторного анализа)-->
			<th></th>
			<!--Результаты анализов первичного-->
			<th>
				<input type="text" name="testResultActualValueICFilter" id="testResultActualValueICFilter" class="input-revise" value="">
			</th>
			<!--Результаты анализов повторного-->
			<th>
				<input type="text" name="testResultActualValueVlkFilter" id="testResultActualValueVlkFilter" class="input-revise" value="">
			</th>
			<!--Фамилия исполнителя первичного результата анализа-->
			<th>
				<input type="text" name="assignedICFilter" id="assignedICFilter" class="input-revise" value="">
			</th>
			<!--Фамилия исполнителя повторного результата анализа-->
			<th>
				<input type="text" name="assignedVlkFilter" id="assignedVlkFilter" class="input-revise" value="">
			</th>
			<!--Результат контрольной процедуры-->
			<th>
				<input type="text" name="resultControlProcedureFilter" id="resultControlProcedureFilter" class="input-revise" value="">
			</th>
			<th>
				<input type="text" name="counterpartyFilter" id="counterpartyFilter" class="input-revise" value="">
			</th>
			<th>
				<input type="text" name="dogovorFilter" id="dogovorFilter" class="input-revise" value="">
			</th>
		</tr>
	</thead>
	<tbody>
    <?php

	if (!empty($resultAllDataRequestVlk)) :
		while ($rowDataRequestVlk = $resultAllDataRequestVlk->Fetch()) :
//			if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
//				pre($rowDataRequestVlk);
//			}
		$specification = []; //Массив для наименований методик
		$resultActualValue = []; //Массив для Результатов испытания, фактических значений
		$shNumbers = []; //Масив для записи шифр пробы
		$resultActualValueVlk = []; //Массив для результатов испытания повторного анализа
		$resultActualValueIC = []; //Массив для результатов испытания первичного анализа
		$gostName = []; //Массив наименований ГОСТовж
		$typeResultsVlk = !empty($rowDataRequestVlk['type_results_vlk']) ? $rowDataRequestVlk['type_results_vlk']: '';
						
		
		$idTz = !empty($rowDataRequestVlk['ID']) ? (int)$rowDataRequestVlk['ID'] : 0; //ID ТЗ
		$id_z = !empty($rowDataRequestVlk['ID_Z']) ? (int)$rowDataRequestVlk['ID_Z'] : 0; //ID Завки(сделки) ВЛК (Повторной заявки ВЛК)
		
		//Данные Завки(сделки) ВЛК (Повторной заявки ВЛК)
		$dealVlk = CCrmDeal::GetByID($id_z);
		
		//Дата создания завки ВЛК
		//$dateCreateRequestVlk = !empty($dealVlk['DATE_CREATE']) ? $dealVlk['DATE_CREATE'] : '';
		$dateCreateRequestVlk = !empty($rowDataRequestVlk['DATE_CREATE']) ? $rowDataRequestVlk['DATE_CREATE'] : '';
	
		//(первичная ИЦ завка)ID ТЗ откуда были экспортированы данные в Заявку ВЛК 
		$importTzIdVlk = !empty($rowDataRequestVlk['IMPORT_TZ_ID_VLK']) ? (int)$rowDataRequestVlk['IMPORT_TZ_ID_VLK'] : 0;
		
		//(первичная ИЦ завка)Данные ТЗ которые были частичноэкспортированы в Заявку ВЛК 
		$resultAllDataRequestIC = null;
		if (!empty($importTzIdVlk)) {
			$resultAllDataRequestIC = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . $importTzIdVlk)->Fetch();
		}
		
		$idZIC = !empty($resultAllDataRequestIC['ID_Z']) ? (int)$resultAllDataRequestIC['ID_Z'] : 0; //ID Завки(сделки) ИЦ (Первичной заявки ИЦ)
		$tzIC = unserialize($resultAllDataRequestIC['TZ']);

		//Данные Завки(сделки) ИЦ (первичной заявки ИЦ)
		$dealIC = CCrmDeal::GetByID($idZIC);
		
		$resultAllDataVlk = null;
		if (!empty($idTz)) {
			$resultAllDataVlk = $DB->Query("SELECT * FROM `VLK` WHERE `TZ_ID`=" . $idTz)->Fetch();
		}
		
		$idVlk = !empty($resultAllDataVlk['ID']) ? $resultAllDataVlk['ID'] : ''; //ID vlk
		
		//Массив данных о пробе
		$probeArray = !empty($rowDataRequestVlk['PROBE']) ? unserialize($rowDataRequestVlk['PROBE']) : '';
		
		//Наименование материала
		$material = !empty($rowDataRequestVlk['MATERIAL']) ? $rowDataRequestVlk['MATERIAL'] : '';

		$idLabaVlk = !empty($rowDataRequestVlk['LABA_ID']) ? $rowDataRequestVlk['LABA_ID'] : 0; //ID Лаборатории

		//--Вывод ГОСТа в Журнале ВЛК начало--//
		$OAID = !empty($resultAllDataVlk['OA_ID']) ? $resultAllDataVlk['OA_ID'] : ''; //OA ID

		if (!empty($OAID)) {
			$resOA = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $OAID)->Fetch();
		}
		
		$gostPunctOA = !empty($resOA['GOST_PUNKT']) ? $resOA['GOST_PUNKT'] : ''; //Пунк ГОСТа
		
		$gostNameOA =  $resOA['GOST'] . ' '. $gostPunctOA;
		//--Вывод ГОСТа в Журнале ВЛК окончание--//

		//Номер пробы Заявки ИЦ//
		$numProbe = explode('.', $rowDataRequestVlk['import_probe']);
		$kProbe = $numProbe[1];

		foreach ($tzIC[$kProbe]['gost'] as $g => $gost) {
			if ($gost == $OAID) {
				break;
			}
		}

		//Наименование лабораторий, ключ-id лабораторий
		$arrayAllNameLaba = [
			1 => 'ЛФХИ',
			3 => 'ЛФМИ',
			4 => 'ДСЛ',
			2 => 'ЛСМ',
			5 => 'ОСК'
		];

		$nameLabaVlk = !empty($arrayAllNameLaba[$idLabaVlk]) ? $arrayAllNameLaba[$idLabaVlk] : ''; //Наименование лабораторий

		$yearDateCreateRequest = (!empty($dateCreateRequestVlk) ? date("y", strtotime($dateCreateRequestVlk)) : ''); //Год создания завки ВЛК из 2 цифр
		
		foreach ($probeArray as $prob) {
			
			foreach ($prob as $key => $item) {
				
				switch ($key) {
					case 'sh_number': //Если элемент равняется шифр пробы, записываем его
						//$numbers = array_merge($numbers, $item);
						$shNumbers = array_merge($shNumbers, $item); 
						break;
				}
			}
		}

		$shNumbersStr = !empty($shNumbers) ? implode(', ', $shNumbers) : ''; //Собираем шифр пробы из массива в строку
		
//		$sampleCode = !empty($idVlk) ? $idVlk . 'ВЛК.' . $nameLabaVlk . '/' . $yearDateCreateRequest . ' ' . $shNumbersStr : '-'; //Шифр пробы(№пробы в ИЦ/№ протокола)
		$sampleCode = $rowDataRequestVlk['secret_probe'] ?? '';
//
//        $updateBaTzVlk = $DB->Query("UPDATE `ba_tz_vlk` SET `NUM_ACT_TABLE` = '{$sampleCode}', `ACT_NUM` = '{$idVlk}'");

//      $sampleCode = $shNumbersStr ? $shNumbersStr : '-';
		
		//Массив данных материала и методик (Повторного анализа)
		$tz = !empty($rowDataRequestVlk['TZ']) ? unserialize($rowDataRequestVlk['TZ']) : '';
		
		//Данные результатов испытаний (ФАКТИЧЕСКОЕ ЗНАЧЕНИЕ из results_isp.php) (Результаты анализов повторного)
		$testResultsVlk = $rowDataRequestVlk['RESULTS'] ?? '';
		$testResultsVlkFirst = $rowDataRequestVlk['RESULTS_FIRST'] ?? '';
		//Данные результатов испытаний (ФАКТИЧЕСКОЕ ЗНАЧЕНИЕ из results_isp.php) (Результаты анализов первичного)


		$testResultsIC = !empty($resultAllDataRequestIC['RESULTS']) ? unserialize($resultAllDataRequestIC['RESULTS']) : '';

		//Если методика рассчитывает средние значения, то вывдить средние значение
		if($resOA['GOST_TYPE']=="TU_sred5"||$resOA['GOST_TYPE']=="TU_sred4"||$resOA['GOST_TYPE']=="TU_sred3"||$resOA['GOST_TYPE']=="TU_sred2"||$resOA['GOST_TYPE']=="TU_sred") {
			$testResultsIC = !empty($resultAllDataRequestIC['SRED']) ? unserialize($resultAllDataRequestIC['SRED']) : '';
			$testResultsVlk = !empty($rowDataRequestVlk['SRED']) ? unserialize($rowDataRequestVlk['SRED']) : '';
		}

			$probe = !empty($rowDataRequestVlk['PROBE']) ? unserialize($rowDataRequestVlk['PROBE']) : '';
		
		$endProbeDate = !empty(end($probe)['dat']) ? end($probe)['dat'] : ''; //Дата пробы последнего элемента массива
		
		//Дата регистрации(в Журнале ВЛК)	
		$dateProbe = ($resultAllDataVlk['DATE'] ? date("d.m.Y", strtotime($resultAllDataVlk['DATE'])) : $endProbeDate);
		
		//--Заявки ВЛК начало--//
		//Номер(наименование) заявки(сделки)
		$requestTitle = ($rowDataRequestVlk['REQUEST_TITLE'] ? $rowDataRequestVlk['REQUEST_TITLE'] : '');
		
		$actNum = !empty($rowDataRequestVlk['ACT_NUM']) ? $rowDataRequestVlk['ACT_NUM'] : ''; //Номер АКТа приемки проб
		//--Заявки ВЛК окончание--//

//		pre($testResultsIC[$kProbe - 1]);
		//Перебираем материалы с их методиками

		foreach ($tz as $key => $items) {

//			foreach($probe[$key]['number_probe'] as $keyProbe => $probe_k) {
//				$keyProbe = $keyProbe + 1;

				foreach ($items['gost'] as $k => $idGost) {

					if(!empty($idGost)) 
					{
						//Находим данные ГОСТа по ID ГОСТа
						$gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $idGost)->Fetch();
						
						//Записываем наименование методики
						if (!empty($gost['SPECIFICATION'])) {
							$specification[] =  $gost['SPECIFICATION'];
						}
						
						//Записываем наименование ГОСТа , номер госта
						if (!empty($gost['GOST'])) {
							$gostPunct = !empty($gost['GOST_PUNKT']) ? $gost['GOST_PUNKT'] : ''; //Пунк ГОСТа
							$gostName[] =  $gost['GOST'] . ' '. $gostPunct;
						}
						
						
						
					}

					//Если высчитываем среднее значение, то присваиваем результат среднего значения
					if($resOA['GOST_TYPE']=="TU_sred5"||$resOA['GOST_TYPE']=="TU_sred4"||$resOA['GOST_TYPE']=="TU_sred3"||$resOA['GOST_TYPE']=="TU_sred2"||$resOA['GOST_TYPE']=="TU_sred") {
						if (!empty($testResultsVlk)) {
;							//Результат испытания, фактическое значение (первое) (Результаты анализов повторного)
							$resultActualValueVlk[] = $testResultsVlk;
						}
						if (!empty($testResultsIC[$kProbe - 1][$g][1]) && $typeResultsVlk == 1) {
							//Результат испытания, фактическое значение (первое) (Результаты анализов первичного)
							$resultActualValueIC[] = $testResultsIC[$key - 1][$k][1];
						} elseif ((!empty($testResultsVlkFirst)) && $typeResultsVlk == 2) {
							$resultActualValueIC[] = $testResultsVlkFirst;
						}

					} else {
						//Если есть фактическое значение(первое)(Результаты анализов повторного), то заисваем в массив
						if (!empty($testResultsVlk)) {
							//Результат испытания, фактическое значение (первое) (Результаты анализов повторного)
							$resultActualValueVlk[] = $testResultsVlk;
						}

						//Если есть фактическое значение(первое)(Результаты анализов первичного), то заисваем в массив
						if (!empty($testResultsIC[$kProbe - 1][$g][1][0] && $typeResultsVlk == 1)) {
							//Результат испытания, фактическое значение (первое) (Результаты анализов первичного)
							$resultActualValueIC[] = $testResultsIC[$kProbe - 1][$g][1][0];
						} elseif ((!empty($testResultsVlkFirst)) && $typeResultsVlk == 2) {
							$resultActualValueIC[] = $testResultsVlkFirst;
						}
					}
					
				}
//			}
			
		}
//		pre($testResultsIC[$kProbe - 1][$g][1][0]);
//		pre($testResultsIC[$kProbe - 1][$g][$kProbe][0]);
		$specificationName = implode(", ", array_unique($specification)); //Наименование методики
		
		$gostNameStr = implode(", ", array_unique($gostName)); //Наименование ГОСТа
		//$specificationName = !empty($SPECIFICATION[0]) ? $SPECIFICATION[0] : '';
		//Результаты анализов повторного

		$testResultActualValueVlk = !empty($resultActualValueVlk[0]) ? $resultActualValueVlk[0] : 0;


			//Результаты анализов первичного
		$testResultActualValueIC = !empty($resultActualValueIC[0]) ? $resultActualValueIC[0] : 0;
//		echo '<pre>';
//		var_dump($testResultActualValueIC);
//		var_dump($testResultActualValueVlk);

		//Результат контрольной процедуры
		$resultControlProcedure = (int)$testResultActualValueIC - (int)$testResultActualValueVlk;

		$assignedByLastNameIC = !empty($resultAllDataRequestIC['ASSIGNED']) ? $resultAllDataRequestIC['ASSIGNED'] : ''; // Фамилия главного ответственного (Фамилия исполнителя первичного результата анализа)
		
		//$assignedByLastNameVlk = !empty($dealVlk['ASSIGNED_BY_LAST_NAME']) ? $dealVlk['ASSIGNED_BY_LAST_NAME'] : ''; // Фамилия главного ответственного (Фамилия исполнителя повторного результата анализа)
		$assignedByLastNameVlk = !empty($rowDataRequestVlk['performer']) ? $rowDataRequestVlk['performer'] : ''; // И.Фамилия главного ответственного (Фамилия исполнителя повторного результата анализа)
		
		//Ответственный (Фамилия исполнителя первичного результата анализа)
		if ($rowDataRequestVlk['type_results_vlk'] == 1) {
		$assignedIC =  $assignedByLastNameIC;
		} elseif ($rowDataRequestVlk['type_results_vlk'] == 2) {
			$assignedIC =  $rowDataRequestVlk['performer_first'] ?? '' ;
		}
		
		//Ответственный (Фамилия исполнителя повторного результата анализа)
		$assignedVlk =  $assignedByLastNameVlk;

		$precisionLimitRL = '';

		$assessmentOfResultControlProcedure = '';
if ($testResultActualValueIC && $testResultActualValueVlk) {
	switch ($resOA['ID']) {
		case 1062:
			if ($testResultActualValueIC <= '70') {
				$precisionLimitRL = 5;
				if (abs($resultControlProcedure) <= $precisionLimitRL) {
					$assessmentOfResultControlProcedure = 1;
				} else {$assessmentOfResultControlProcedure = 2; }
			} elseif (($testResultActualValueIC > '70') && ($testResultActualValueIC <= '130')) {
				$precisionLimitRL = 6;
				if (abs($resultControlProcedure) <= $precisionLimitRL) {
					$assessmentOfResultControlProcedure = 1;
				} else {$assessmentOfResultControlProcedure = 2; }
			} elseif ($testResultActualValueIC > '130') {
				$precisionLimitRL = 7;
				if (abs($resultControlProcedure) <= $precisionLimitRL) {
					$assessmentOfResultControlProcedure = 1;
				} else {$assessmentOfResultControlProcedure = 2; }
			}
			break;
			//Лаборатория ЛФХМ//
		case 5593:
			if (($testResultActualValueIC >= 0.05) && ($testResultActualValueIC <= 1)) {
				$r = 0.05;
			} elseif (($testResultActualValueIC > 1) && ($testResultActualValueIC <= 2)) {
				$r = 0.10;
			}
			$or = $r/2.77;
			$oR = $or/0.7;
			$oRl = 0.84 * $oR;
			$precisionLimitRL = 2.77 * $oRl;
			$assessmentOfResultControlProcedure = (abs($resultControlProcedure) <= $precisionLimitRL) ? 1 : 2;
			break;
		case 5595:
			if (($testResultActualValueIC >= 0.05) && ($testResultActualValueIC <= 1)) {
				$r = 0.05;
			} elseif (($testResultActualValueIC > 1) && ($testResultActualValueIC <= 3)) {
				$r = 0.10;
			}
			$or = $r/2.77;
			$oR = $or/0.7;
			$oRl = 0.84 * $oR;
			$precisionLimitRL = 2.77 * $oRl;
			$assessmentOfResultControlProcedure = (abs($resultControlProcedure) <= $precisionLimitRL) ? 1 : 2;
			break;
		case 5597:
			if (($testResultActualValueIC >= 30) && ($testResultActualValueIC <= 40)) {
				$r = 0.05;
			}
			$or = $r/2.77;
			$oR = $or/0.7;
			$oRl = 0.84 * $oR;
			$precisionLimitRL = 2.77 * $oRl;
			$assessmentOfResultControlProcedure = (abs($resultControlProcedure) <= $precisionLimitRL) ? 1 : 2;
			break;
		case 5598:
			if (($testResultActualValueIC >= 0.15) && ($testResultActualValueIC <= 1)) {
				$r = 0.05;
			} elseif (($testResultActualValueIC > 1) && ($testResultActualValueIC <= 3)) {
				$r = 0.15;
			}
			$or = $r/2.77;
			$oR = $or/0.7;
			$oRl = 0.84 * $oR;
			$precisionLimitRL = 2.77 * $oRl;
			$assessmentOfResultControlProcedure = (abs($resultControlProcedure) <= $precisionLimitRL) ? 1 : 2;
			break;
		case 5599:
			if (($testResultActualValueIC >= 0.05) && ($testResultActualValueIC <= 1)) {
				$r = 0.05;
			} elseif (($testResultActualValueIC > 1) && ($testResultActualValueIC <= 2.5)) {
				$r = 0.10;
			}
			$or = $r/2.77;
			$oR = $or/0.7;
			$oRl = 0.84 * $oR;
			$precisionLimitRL = 2.77 * $oRl;
			$assessmentOfResultControlProcedure = (abs($resultControlProcedure) <= $precisionLimitRL) ? 1 : 2;
			break;
		case 5600:
			if (($testResultActualValueIC >= 0.04) && ($testResultActualValueIC <= 0.2)) {
				$r = 0.04;
			} elseif (($testResultActualValueIC > 0.2) && ($testResultActualValueIC <= 0.3)) {
				$r = 0.6;
			} elseif ($testResultActualValueIC > 0.3) {
				$r = 0.8;
			}
			$or = $r/2.77;
			$oR = $or/0.7;
			$oRl = 0.84 * $oR;
			$precisionLimitRL = 2.77 * $oRl;
			$assessmentOfResultControlProcedure = (abs($resultControlProcedure) <= $precisionLimitRL) ? 1 : 2;
			break;
		case 5607:
			if (($testResultActualValueIC >= 10) && ($testResultActualValueIC <= 15)) {
				$r = 0.30;
			}
			$or = $r/2.77;
			$oR = $or/0.7;
			$oRl = 0.84 * $oR;
			$precisionLimitRL = 2.77 * $oRl;
			$assessmentOfResultControlProcedure = (abs($resultControlProcedure) <= $precisionLimitRL) ? 1 : 2;
			break;
		case 5615:
			if (($testResultActualValueIC >= 0.1) && ($testResultActualValueIC >= 0.2)) {
				$precisionLimitRL = 0.4;
			} elseif (($testResultActualValueIC > 0.2) && ($testResultActualValueIC >= 0.5)) {
				$precisionLimitRL = 0.6;
			} elseif (($testResultActualValueIC > 0.5) && ($testResultActualValueIC >= 1)) {
				$precisionLimitRL = 0.8;
			} elseif (($testResultActualValueIC > 1) && ($testResultActualValueIC >= 2)) {
				$precisionLimitRL = 0.11;
			} elseif (($testResultActualValueIC > 2) && ($testResultActualValueIC >= 5)) {
				$precisionLimitRL = 0.2;
			} elseif (($testResultActualValueIC > 5) && ($testResultActualValueIC >= 10)) {
				$precisionLimitRL = 0.25;
			} elseif (($testResultActualValueIC > 10) && ($testResultActualValueIC >= 20)) {
				$precisionLimitRL = 0.4;
			} elseif (($testResultActualValueIC > 20) && ($testResultActualValueIC >= 50)) {
				$precisionLimitRL = 0.5;
			} elseif ($testResultActualValueIC > 50) {
				$precisionLimitRL = 0.7;
			}
			$assessmentOfResultControlProcedure = (abs($resultControlProcedure) <= $precisionLimitRL) ? 1 : 2;
			break;
		case 4948:
			if (($testResultActualValueIC >= 0.1) && ($testResultActualValueIC >= 0.2)) {
				$precisionLimitRL = 0.05;
			} elseif (($testResultActualValueIC > 0.2) && ($testResultActualValueIC >= 0.5)) {
				$precisionLimitRL = 0.08;
			} elseif (($testResultActualValueIC > 0.5) && ($testResultActualValueIC >= 1)) {
				$precisionLimitRL = 0.1;
			} elseif (($testResultActualValueIC > 1) && ($testResultActualValueIC >= 2)) {
				$precisionLimitRL = 0.12;
			} elseif (($testResultActualValueIC > 2) && ($testResultActualValueIC >= 5)) {
				$precisionLimitRL = 0.2;
			} elseif (($testResultActualValueIC > 5) && ($testResultActualValueIC >= 10)) {
				$precisionLimitRL = 0.25;
			} elseif (($testResultActualValueIC > 10) && ($testResultActualValueIC >= 20)) {
				$precisionLimitRL = 0.4;
			} elseif (($testResultActualValueIC > 20) && ($testResultActualValueIC >= 50)) {
				$precisionLimitRL = 0.6;
			} elseif ($testResultActualValueIC > 50) {
				$precisionLimitRL = 0.7;
			}
			$assessmentOfResultControlProcedure = (abs($resultControlProcedure) <= $precisionLimitRL) ? 1 : 2;
			break;
		default:
    // Q(P,n) коэффициент (при n = 2) = 2.77
		$coefQ = 2.77;

		//значение точечной оценки показателя (среднеарифметическое значение результатов контрольных измерений)
		$precision = ((int)$testResultActualValueIC + (int)$testResultActualValueVlk) / 2;

		//предел внутрилабораторной прецизионности Rл
		$precisionLimitRL = $coefQ * $precision;

		//Результат контрольной процедуры
		$assessmentOfResultControlProcedure = (abs($resultControlProcedure) <= $precisionLimitRL) ? 1 : 2;
		}
}
			?>
		<tr>
			<!--Заявки ВЛК начало-->
			<!--Заявки-->
			<td><a href='/request_card_vlk.php?ID=<?= $id_z ?>&ACT_NUM=<?= $actNum ?>' target='_blank'><?= $requestTitle ?></a></td>
			<!--Заявки ВЛК окончание-->
			<!--Дата регистрации-->
			<td><?= $dateProbe ?></td>
			<!--Материал-->
			<td><?= $material ?></td>
			<!--Шифр пробы(№пробы в ИЦ/№ протокола)-->
			<td><?= $sampleCode ?></td>
			<!--Конролируемый показатель-->
			<td><?= $specificationName ?></td>
			<!--Номер НД на МИ(Первичного анализа)-->
			<!--Вывод ГОСТа в Журнале ВЛК начало-->
			<td><?= $gostNameStr ?></td>
			<!--Вывод ГОСТа в Журнале ВЛК окончание-->
			<td><?= $gostNameOA ?></td>
			<!--Номер НД на МИ(Повторного анализа)-->
			<!--<td></td>-->
			<!--Результаты анализов первичного-->
			<td><?= $testResultActualValueIC ?></td>
			<!--Результаты анализов повторного-->
			<td><?= $testResultActualValueVlk ?></td>
			<!--Фамилия исполнителя первичного результата анализа-->
			<td><?= $assignedIC ?></td>
			<!--Фамилия исполнителя повторного результата анализа-->
			<td><?= $assignedVlk  ?></td>
			<!--Результат контрольной процедуры-->
			<!--abs — Абсолютное значение (модуль числа)-->
			<td><?= abs($resultControlProcedure) ?></td>
			<!--Норматив контрля Rл-->
			<td><?= $precisionLimitRL ?></td>
			<!--Результат процедуры-->
			<td><?= (!empty($assessmentOfResultControlProcedure)) ? (($assessmentOfResultControlProcedure == 1) ? 'Результаты анализов удовлетворительны' : 'Результаты неудовлетворительны. Процедуру следует повторить') : ''?></td>
		</tr>
	<?php
		endwhile;
	endif;
    ?>
	<table>
	
	<!--Подключаем jquery-->
	<script src="//code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
			crossorigin="anonymous"></script>

	<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
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
	<script src="https://cdn.datatables.net/plug-ins/1.10.21/dataRender/intl.js"></script>
	
	<script>
        $(document).ready(function () {
            table= $('#vlk_table').DataTable({
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
				"columnDefs": [
					{ "type": "natural-nohtml", "targets": 0 },
					{ "sType": "ruDate", "targets": 1 },
					{
					  "targets": 2,
					  "render": $.fn.dataTable.render.ellipsis( 50, true )
					},
					{ "type": "natural-nohtml", "targets": 3 },
					{
					  "targets": 4,
					  "render": $.fn.dataTable.render.ellipsis( 50, true )
					},
					{
					  "targets": 5,
					  "render": $.fn.dataTable.render.ellipsis( 30, true )
					},
					{ "type": "num", "targets": 7 },
					{ "type": "num", "targets": 8 },
					{
					  "targets": 9, 
					  "render": $.fn.dataTable.render.ellipsis( 50, true ) 
					},
					{
					  "targets": 10, 
					  "render": $.fn.dataTable.render.ellipsis( 50, true ) 
					},
					{ "type": "num", "targets": 11 }
				],
				"order": [[ 0, "asc" ]], //Сортировка по 1 колонке по умолчанию
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
			
			
			<!--Заявки-->
			$('#request').on( 'keyup', function () {
				table
					.columns( 0 )
					.search( this.value )
					.draw();
			});
			
			<!--Дата регистрации-->
			$('#dateProbeFilter').on( 'keyup', function () {
				table
					.columns( 1 )
					.search( this.value )
					.draw();
			});
			
			<!--Материал-->
			$('#materialFilter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			});
			
			<!--Шифр пробы(№пробы в ИЦ/№ протокола)-->
			 $('#sampleCodeFilter').on( 'keyup', function () {
				 table
					 .columns( 3 )
					 .search( this.value )
					 .draw();
			 });
			
			<!--Конролируемый показатель-->
			$('#specificationNameFilter').on( 'keyup', function () {
				table
					.columns( 4 )
					.search( this.value )
					.draw();
			});
			
			<!--Номер НД на МИ(Первичного анализа)-->
			$('#gostNameStrFilter').on( 'keyup', function () {
				table
					.columns( 5 )
					.search( this.value )
					.draw();
			});
			
			<!--Результаты анализов первичного-->
			$('#testResultActualValueICFilter').on( 'keyup', function () {
				table
					.columns( 7 )
					.search( this.value )
					.draw();
			});
			
			<!--Результаты анализов повторного-->
			$('#testResultActualValueVlkFilter').on( 'keyup', function () {
				table
					.columns( 8 )
					.search( this.value )
					.draw();
			});
			
			<!--Фамилия исполнителя первичного результата анализа-->
			$('#assignedICFilter').on( 'keyup', function () {
				table
					.columns( 9 )
					.search( this.value )
					.draw();
			});
			
			<!--Фамилия исполнителя повторного результата анализа-->
			$('#assignedVlkFilter').on( 'keyup', function () {
				table
					.columns( 10 )
					.search( this.value )
					.draw();
			});
			
			<!--Результат контрольной процедуры-->
			$('#resultControlProcedureFilter').on( 'keyup', function () {
				table
					.columns( 11 )
					.search( this.value )
					.draw();
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
