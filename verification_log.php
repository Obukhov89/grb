<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал верификации");

//Получаем из Журнал параметров расхода реактивов(таблицы REAGENTS_FLOW) реактив и наименование ГОСТа для этого реактива
//Получаем все данные реагентов из таблицы (REAGENTS_FLOW)
//$resultSelectAllReagentsFlow = $DB->Query("SELECT * FROM `REAGENTS_FLOW`");

/*while($resultReagentsFlow = $resultSelectAllReagentsFlow->Fetch()) {
	echo'<pre>';var_dump($resultReagentsFlow);echo'</pre>';
}*/

//Находим все реактивы
//$resultSelectAllReagentsOst = $DB->Query("SELECT * FROM `REACTIVE_OST`");

//while($rowReagentsOst = $resultSelectAllReagentsOst->Fetch()) {
//	echo'<pre>';var_dump($rowReagentsOst);echo'</pre>';
//}


if (!empty($_POST)) {
	
	//echo'<pre>';var_dump($_POST);echo'</pre>';die();
	
	$reactive = !empty($_POST['reactive']) ? (int)$_POST['reactive'] : ''; //Наименование реактива
	$ntdMaterial = !empty($_POST['ntd_material']) ? $DB->ForSql($_POST['ntd_material']) : ''; //НТД на материал
	$qualification = !empty($_POST['qualification']) ? $DB->ForSql($_POST['qualification']) : ''; //Сорт, квалификация и др.
	$view = !empty($_POST['view']) ? $DB->ForSql($_POST['view']) : ''; //Вид упаковки, количество материала
	$manufacturer = !empty($_POST['manufacturer']) ? $DB->ForSql($_POST['manufacturer']) : ''; //Производитель
	$producer = !empty($_POST['producer']) ? $_POST['producer'] : ''; //Поставщик
	$resulrVisualControl = !empty($_POST['resulr_visual_control']) ? $DB->ForSql($_POST['resulr_visual_control']) : ''; //Результат визуального контроля
	$ntdOnmethodologyTrials = !empty($_POST['ntd_onmethodology_trials']) ? (int)$_POST['ntd_onmethodology_trials'] : ''; //НТД на методику испытаний
	$сonclusionQualityMaterial = !empty($_POST['сonclusion_quality_material']) ? $DB->ForSql($_POST['сonclusion_quality_material']) : ''; //Заключение о качестве материала
	$reactiveAssigned = !empty($_POST['reactive_assigned']) ? (int)$_POST['reactive_assigned'] : ''; //Ф.И.О. ответственного за верификацию
	
	if (!empty($reactive)) {
		$updateReactive = $DB->Query("UPDATE `REACTIVE` SET `NTD_MATERIAL`='" . $ntdMaterial . "', `QUALIFICATION`='" . $qualification . "', 
		`VIEW`='" . $view . "', `MANUFACTURER`='" . $manufacturer . "', `PRODUCER`='" . $producer . "', 
		`RESULT_VISUAL_CONTROL`='" . $resulrVisualControl . "', `NTD_ONMETHODOLOGY_TRIALS`=" . $ntdOnmethodologyTrials . ", 
		`CONCLISION_QUALITY_MATERIAL`='" . $сonclusionQualityMaterial . "', `REACTIVE_ASSIGNED`=" . $reactiveAssigned . " 
		WHERE `ID`=" . $reactive);
	}
}


if (isset($_GET['ID'])) {
?>
<a href="/verification_log.php" target="_break">Вернуться к таблице</a>
<br>
<br>
	<!--Форма заполнения "Журнала верификации"-->
	<form id="form_reactive" method="post">
		<table id="table_form_reactive">
			<thead>
				<tr>
					<td>Наименование реактива</td>
					<td class="tdSelect">
						<select id="reactive" name="reactive" class="select-reactive reactive" required>
							<option value="" selected disabled=""></option>
							<?php
							//Находим все данные учета реактивов
							$resultSelectAllReactive = $DB->Query("SELECT * FROM `REACTIVE`");
							
							while($rowReactive = $resultSelectAllReactive->Fetch()) {
								$reactId = !empty($rowReactive['ID_REACT']) ? $rowReactive['ID_REACT'] : '';
								$idRegistration = !empty($rowReactive['ID']) ? $rowReactive['ID'] : '';
								//Дата поступления
								$datePostup = !empty($rowReactive['DATE_POSTUP']) ? date("d.m.Y", strtotime($rowReactive['DATE_POSTUP'])) : '';
								//НД № партии квалификация
								$numberPart = !empty($rowReactive['NUMBER_PART']) ? $rowReactive['NUMBER_PART'] : '';
								
								if (!empty($reactId)) {
									//Находим данные реактивов
									$resultSelectAllReagentsOst = $DB->Query("SELECT * FROM `REACTIVE_OST` WHERE `ID`=" . $reactId);
								
									while($rowReagentsOst = $resultSelectAllReagentsOst->Fetch()) {
										$reactiveName = !empty($rowReagentsOst['REACT_NAME']) ? $rowReagentsOst['REACT_NAME'] : ''; //Название реактива
										//$reactiveId = !empty($rowReagentsOst['ID']) ? $rowReagentsOst['ID'] : ''; //ID реактива
										
										echo "<option data-id='" . $reactiveId . "' value='" . $idRegistration . "'>" . $reactiveName . ', от ' . $datePostup . ' , ' . $numberPart . "</option>";
									}
								}
								
							}
							
							
							//Находим данные реактивов
							/*$resultSelectAllReagentsOst = $DB->Query("SELECT * FROM `REACTIVE_OST`");
							
							while($rowReagentsOst = $resultSelectAllReagentsOst->Fetch()) {
								$reactiveName = !empty($rowReagentsOst['REACT_NAME']) ? $rowReagentsOst['REACT_NAME'] : ''; //Название реактива
								$reactiveId = !empty($rowReagentsOst['ID']) ? $rowReagentsOst['ID'] : ''; //ID реактива
								
								echo "<option data-id='" . $reactiveId . "' value='" . $reactiveId . "'>" . $reactiveName . "</option>";
							}*/
							?>
						</select>
						<!--<input class="reactive" type="text" list="reactive" name="name_reactive" value="">-->
					</td>
				</tr>
				<tr>
					<td>НТД на материал</td>
					<td>
						<!--НТД на материал-->
						<input class="ntd-material input-modal" type="text" name="ntd_material" value="">
					</td>
				</tr>
				<tr>
					<td>Сорт, квалификация и др.</td>
					<td>
						<!--Сорт, квалификация и др.-->
						<input class="qualification input-modal" type="text" name="qualification" value="">
					</td>
				</tr>
				<tr>
					<td>Вид упаковки, количество материала</td>
					<td>
						<!--Вид упаковки, количество материала-->
						<input class="view input-modal" type="text" name="view" value="">
					</td>
				</tr>
				<tr>
					<td>Производитель</td>
					<td>
						<!--Производитель-->
						<input class="manufacturer input-modal" type="text" name="manufacturer" value="">
					</td>
				</tr>
				<tr>
					<td>Поставщик</td>
					<td class="tdSelect">
						<!--Поставщик-->
						<input class="producer input-modal" type="text" name="producer" value="">
						<!--<select id="producer" name="producer" class="select-reactive producer">
							<option value="" selected disabled=""></option>
							<?php
							/*$arOrder  = ['ID' => 'ASC'];
							$arFilter = [];
							$arSelect = [];
							$res = CCrmCompany::GetList( $arOrder, $arFilter, $arSelect );
							while( $arCompany = $res->fetch() )
							{
								
								$companyTitle = !empty($arCompany['TITLE']) ? $arCompany['TITLE'] : '';
								$companyId = !empty($arCompany['ID']) ? $arCompany['ID'] : '';
								
								echo "<option value='" . $companyId . "'>" . $companyTitle . "</option>";
							}
							*/
							?>
						</select>-->
					</td>
				</tr>
				<tr>
					<td>Результат визуального контроля</td>
					<td>
						<!--Результат визуального контроля-->
						<input class="resulr-visual-control input-modal" type="text" name="resulr_visual_control" value="">
					</td>
				</tr>
				<tr>
					<td>НТД на методику испытаний</td>
					<td class="tdSelect">
						<!--НТД на методику испытаний-->
						<!--<input class="ntd-onmethodology-trials input-modal" type="text" name="ntd_onmethodology_trials" value="">-->
						<select name="ntd_onmethodology_trials" class="select-reactive ntd-onmethodology-trials input-modal">
						
							<option value="" disabled selected></option>
							<?php
							//Получаем все ГОСТы (методики)
							$baGost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");
							
							while ($gost = $baGost->Fetch()) {
							
								$gostId = !empty($gost['ID']) ? $gost['ID'] : ''; //ID ГОСТа 
								$gostNumber = !empty($gost['GOST']) ? $gost['GOST'] : ''; // ГОСТ 
								$gostSpecification = !empty($gost['SPECIFICATION']) ? $gost['SPECIFICATION'] : ''; //ID ГОСТа 
								$gostPunct = !empty($gost['GOST_PUNKT']) ? $gost['GOST_PUNKT'] : ''; //пункт ГОСТа 
								
								echo "<option value='" . $gostId . "' >" . $gostNumber . ' ' . $gostSpecification . ' (' . $gostNumber . ")</option>";
						
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Заключение о качестве материала</td>
					<td>
						<!--Заключение о качестве материала-->
						<input class="сonclusion-quality-material input-modal" type="text" name="сonclusion_quality_material" value="">
					</td>
				</tr>
				<tr>
					<td>Ф.И.О. ответственного за верификацию</td>
					<td class="tdSelect">
						<!--Ф.И.О. ответственного за верификацию-->
						<!--<input class="fio-responsible-verification input-modal" type="text" name="fio_responsible_verification" value="">-->
						<select id="reactive_assigned" name="reactive_assigned" class="select-reactive fio-responsible-verification">
							<option value="" selected disabled=""></option>
							<?php
							$order = ['LAST_NAME' => 'asc'];
							$filter = ["ACTIVE" => 'Y'];
							$tmp = 'sort';
							$lab = [54,55,56,57,58];
							//Получаем всех пользователей
							$user = CUser::GetList($order, $tmp, $filter);
							
							while ($optionUser = $user->Fetch()) {
								
								//ID пользователя 
								$userID = !empty($optionUser['ID']) ? $optionUser['ID'] : '';
								//Фамилия плозователя
								$userLastName = !empty($optionUser['LAST_NAME']) ? $optionUser['LAST_NAME'] : '';
								//Имя пользователя
								$userName = !empty($optionUser['NAME']) ? $optionUser['NAME'] : '';
								
								$userData = null;
								if (!empty($userID)) {
									$userData = CUser::GetByID($userID)->Fetch();
								}
								
								//К какой лаборатории относиться ответственный
								$userLaba = !empty($userData['UF_DEPARTMENT'][0]) ? $userData['UF_DEPARTMENT'][0] : '';
								
								if (!empty($userID) && !empty($userLaba) && in_array($userLaba, $lab)) {
									echo "<option data-id='" . $userID . "' value='" . $userID . "'>" . $userLastName . ' ' . $userName . "</option>";
								}
							}
							?>
						</select>
					</td>
				</tr>
			</thead>
		</table>
		
		<input type="submit" name="btn-form-reactive" value="Сохранить">
	</form>
<?php
} else {
?>
<a href="/verification_log.php?ID=0" target="_break">Добавить данные верификации</a>
<br>
<br>
<!--Таблица "Журнала верификации"-->
<table id="table_reactive" class="table-all">
	<thead>
		<tr>
			<th>Дата поступления/верификации</th>
			<th>Наименование материала</th>
			<th>НТД на материал</th>
			<th>Сорт, квалификация и др.</th>
			<th>Номерпартии,паспорта(сертификата)</th>
			<th>Вид упаковки, количество материала</th>
			<th>Дата изготовления, срок хранения</th>
			<th>Производитель</th>
			<th>Поставщик</th>
			<th>Результат визуального контроля</th>
			<th>НТД на методику испытаний</th>
			<th>Заключение о качестве материала</th>
			<th>Ф.И.О. ответственного за верификацию</th>
		</tr>
		<tr>
			<th><input type="text" name="date_postup_filter" id="date_postup_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="name_reactive_filter" id="name_reactive_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="ntd_material_filter" id="ntd_material_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="qualification_filter" id="qualification_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="number_part_filter" id="number_part_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="view_filter" id="view_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="srok_godn_filter" id="srok_godn_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="manufacturer_filter" id="manufacturer_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="producer_filter" id="producer_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="resulr_visual_control_filter" id="resulr_visual_control_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="ntd_onmethodology_trials_filter" id="ntd_onmethodology_trials_filter" style="margin:0px" value=""></th>
			<th><input type="text" name="ntd_сonclusion_quality_material" id="ntd_сonclusion_quality_material" style="margin:0px" value=""></th>
			<th><input type="text" name="сonclusion_quality_material" id="сonclusion_quality_material" style="margin:0px" value=""></th>
		</tr>
	</thead>
	<tbody>
	<?php
	//Находим все реактивы
	$selectAllReagentsOst = $DB->Query("SELECT * FROM `REACTIVE_OST`");
	
	while($rowReagents = $selectAllReagentsOst->Fetch()) {
	//echo'<pre>';var_dump('$rowReagents',$rowReagents);echo'</pre>';
		$nameReactive = !empty($rowReagents['REACT_NAME']) ? $rowReagents['REACT_NAME'] : ''; //Название реактива
		$idReactive = !empty($rowReagents['ID']) ? (int)$rowReagents['ID'] : ''; //ID реактива
		
		//Получаем данные реактивов
		$resultSelectAllDataReactive = null;
		if (!empty($idReactive)) {
			$resultSelectAllDataReactive = $DB->Query("SELECT * FROM `REACTIVE` WHERE `ID_REACT`=" . $idReactive);
		}
		
		if (!empty($resultSelectAllDataReactive)) {
			while($rowDataReactive = $resultSelectAllDataReactive->Fetch()) :
			
				//Дата поступления
				$datePostup = !empty($rowDataReactive['DATE_POSTUP']) ? date("d.m.Y", strtotime($rowDataReactive['DATE_POSTUP'])) : ''; 
				//Номер партии,паспорта(сертификата) (НД № партии квалификация)
				$numberPart = !empty($rowDataReactive['NUMBER_PART']) ? $rowDataReactive['NUMBER_PART'] : ''; 
				//Дата изготовления,срок хранения (Срок годности)
				$srokGodn = !empty($rowDataReactive['SROK_GODN']) ? date("d.m.Y", strtotime($rowDataReactive['SROK_GODN'])) : ''; 
			
				//НТД на материал
				$ntdMaterial = !empty($rowDataReactive['NTD_MATERIAL']) ? $rowDataReactive['NTD_MATERIAL'] : ''; 
				//Сорт, квалификация и др.
				$qualification = !empty($rowDataReactive['QUALIFICATION']) ? $rowDataReactive['QUALIFICATION'] : ''; 
				//Вид упаковки, количество материала
				$view = !empty($rowDataReactive['VIEW']) ? $rowDataReactive['VIEW'] : ''; 
				//Производитель
				$manufacturer = !empty($rowDataReactive['MANUFACTURER']) ? $rowDataReactive['MANUFACTURER'] : ''; 
				//Поставщик
				$producer = !empty($rowDataReactive['PRODUCER']) ? $rowDataReactive['PRODUCER'] : ''; 
				
				/*$resultDataCompany = null;
				if (!empty($producer)) {
					$resultDataCompany = CCrmCompany::GetByID($producer);
				}
				
				$titleCompany = !empty($resultDataCompany['TITLE']) ? $resultDataCompany['TITLE'] : ''; */
				
				//Результат визуального контроля
				$resulrVisualControl = !empty($rowDataReactive['RESULT_VISUAL_CONTROL']) ? $rowDataReactive['RESULT_VISUAL_CONTROL'] : ''; 
				//НТД на методику испытаний
				$ntdOnmethodologyTrials = !empty($rowDataReactive['NTD_ONMETHODOLOGY_TRIALS']) ? (int)$rowDataReactive['NTD_ONMETHODOLOGY_TRIALS'] : ''; 
				
				$resultDataGost = null;
				if (!empty($ntdOnmethodologyTrials)) {
					$resultDataGost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $ntdOnmethodologyTrials)->Fetch();
				}
				
				$gost = !empty($resultDataGost['GOST']) ? $resultDataGost['GOST'] : ''; //ГОСТ
				$gostPunct = !empty($resultDataGost['GOST_PUNKT']) ? $resultDataGost['GOST_PUNKT'] : ''; //пункт ГОСТа
				$gostTitle = $gost . ' ' . $gostPunct;
							
				//Заключение о качестве материала
				$сonclusionQualityMaterial = !empty($rowDataReactive['CONCLISION_QUALITY_MATERIAL']) ? $rowDataReactive['CONCLISION_QUALITY_MATERIAL'] : ''; 
				//Ф.И.О. ответственного за верификацию
				$reactiveAssigned = !empty($rowDataReactive['REACTIVE_ASSIGNED']) ? (int)$rowDataReactive['REACTIVE_ASSIGNED'] : ''; 


				$assigned = null;
				if (!empty($reactiveAssigned)) {
					$assigned = CUser::GetByID($reactiveAssigned)->Fetch();
					
				}
				
				//Имя пользователя
				$assignedName = !empty($assigned['NAME']) ? $assigned['NAME'] : '';
				//Фамилия пользователя
				$assignedLastname = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';
				//Пользователь
				$assignedUser = $assignedName.' '.$assignedLastname;
				?>	
				<tr>
					<!--Дата поступления-->
					<td><?= $datePostup ?></td>
					<!--Название реактива-->
					<td><?= $nameReactive ?></td>
					<!--НТД на материал-->
					<td><?= $ntdMaterial ?></td>
					<!--Сорт, квалификация и др.-->
					<td><?= $qualification ?></td>
					<!--Номер партии,паспорта(сертификата) (НД № партии квалификация)-->
					<td><?= $numberPart ?></td>
					<!--Вид упаковки, количество материала-->
					<td><?= $view ?></td>
					<!--Дата изготовления,срок хранения (Срок годности)-->
					<td><?= $srokGodn ?></td>
					<!--Производитель-->
					<td><?= $manufacturer ?></td>
					<!--Поставщик-->
					<td><?= $producer ?></td>
					<!--Результат визуального контроля-->
					<td><?= $resulrVisualControl ?></td>
					<!--НТД на методику испытаний-->
					<td><?= $gostTitle ?></td>
					<!--Заключение о качестве материала-->
					<td><?= $сonclusionQualityMaterial ?></td>
					<!--Ф.И.О. ответственного за верификацию-->
					<td><?= $assignedUser ?></td>
				</tr>
				<?php
			endwhile;
		}
	}
	?>
		
	</tbody>
</table>
<?php
}
?>

<link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
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
<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
	
<script>
	$(document).ready(function () {
		var table = $('#table_reactive').DataTable({
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
			"bSortCellsTop": true,
			"dom": 'Blfrtip',
			"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
			
		});
		
		//Дата поступления
		$('#date_postup_filter').on( 'keyup', function () {
			table
				.columns( 0 )
				.search( this.value )
				.draw();
		}); 
		//Название реактива
		$('#name_reactive_filter').on( 'keyup', function () {
			table
				.columns( 1 )
				.search( this.value )
				.draw();
		});
		//НТД на материал
		$('#ntd_material_filter').on( 'keyup', function () {
			table
				.columns( 2 )
				.search( this.value )
				.draw();
		});
		//Сорт, квалификация и др.
		$('#qualification_filter').on( 'keyup', function () {
			table
				.columns( 3 )
				.search( this.value )
				.draw();
		});
		//Номер партии,паспорта(сертификата) (НД № партии квалификация)
		$('#number_part_filter').on( 'keyup', function () {
			table
				.columns( 4 )
				.search( this.value )
				.draw();
		});
		//Вид упаковки, количество материала
		$('#view_filter').on( 'keyup', function () {
			table
				.columns( 5 )
				.search( this.value )
				.draw();
		});
		//Дата изготовления,срок хранения (Срок годности)
		$('#srok_godn_filter').on( 'keyup', function () {
			table
				.columns( 6 )
				.search( this.value )
				.draw();
		});
		//Производитель
		$('#manufacturer_filter').on( 'keyup', function () {
			table
				.columns( 7 )
				.search( this.value )
				.draw();
		});
		//Поставщик
		$('#producer_filter').on( 'keyup', function () {
			table
				.columns( 8 )
				.search( this.value )
				.draw();
		});
		//Результат визуального контроля
		$('#resulr_visual_control_filter').on( 'keyup', function () {
			table
				.columns( 9 )
				.search( this.value )
				.draw();
		});
		//НТД на методику испытаний
		$('#ntd_onmethodology_trials_filter').on( 'keyup', function () {
			table
				.columns( 10 )
				.search( this.value )
				.draw();
		});
		//Заключение о качестве материала
		$('#ntd_сonclusion_quality_material').on( 'keyup', function () {
			table
				.columns( 11 )
				.search( this.value )
				.draw();
		});
		//Ф.И.О. ответственного за верификацию
		$('#сonclusion_quality_material').on( 'keyup', function () {
			table
				.columns( 12 )
				.search( this.value )
				.draw();
		});
		
		<!--'При наведении мыши на списки методики, подключаем select2'-->
		$('#form_reactive').on('mouseenter', '.tdSelect', function () {
			$(this).find('.select-reactive').select2();
		});

		$('#form_reactive').on('mouseleave', '.tdSelect', function () {
			var focusedName = document.activeElement.className;

			if (focusedName == 'select2-search__field') {
				return true;
			}

			$(".select-reactive.select2-hidden-accessible").select2('destroy');
		});

		<!--'После выбора методики отлючаем select2'-->
		$('#form_reactive').on('change', '.select-reactive', function (i, obj) {
			if ($(this).hasClass("select2-hidden-accessible")) {
				setTimeout(function () {
					$(".select-reactive.select2-hidden-accessible").select2('destroy');
				}, 0);
			}
		});

		<!--'При попадании в фокус элемента, проверяем, если элемент не является списком методик, то отключаем select2 у оставшихся подключенных методик'-->
		$('#form_reactive').on('focusin input', function (event) {
			var targetElement = event.target;
			if (!$(targetElement).is(".select2-selection")) {
				$(".select-reactive.select2-hidden-accessible").select2('destroy');
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
</script>    

<?php 
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); 
?>