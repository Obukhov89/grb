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
$APPLICATION->SetTitle("Матрица компетентности"); 

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

function echoTD ($arr, $arrayDep, $gost)
{
	$filter = Array();
	//$arrayDep = array('54','55','56','57','58');
	$usersAll = CUser::GetList(($by='LAST_NAME'), ($order='asc'), $filter);
	while($user = $usersAll->Fetch())
		{
			$userData = CUser::GetByID($user['ID'])->Fetch();
			$userDep = $userData['UF_DEPARTMENT'][0];
			if(in_array($userDep, $arrayDep)) $usersIdDep[$user['ID']] = $userDep;
				
		}
		
	asort($usersIdDep);

	// foreach($usersIdDep as $userId => $userDep)
	// {
		// if($userDep != $dep) 
			// echo "<td style='border-left: 1px solid #ccc'>".count($arr[$userId])."</td>";
		// else
			// echo "<td>".count($arr[$userId])."</td>";
		// $dep = $userDep;	
	// }
	
	foreach($usersIdDep as $userId => $userDep)
	{
		if($userDep != $dep) 
			echo "<td style='border-left: 1px solid #ccc'>1</td>";
		else
			echo "<td>1</td>";
		$dep = $userDep;	
	}
}
//$emplLastName = "Овчинникова";
//$letter = substr($emplLastName, 3, 1);
$glasArr = ['а', 'е', 'и', 'о', 'у', 'ю', 'я', 'э'];
//echo $letter. "    ".in_array($letter, $glasArr);
//echo'<pre>';var_dump($_POST);echo'</pre>';
if(!empty($_POST['comp']))
{

	//$res_oa = $DB->Query("SELECT * FROM `ba_gost` ORDER BY `NUM_OA` DESC");	
	//$arrrr=['1'];
	//dump($_POST["comp"]);
	
	foreach($_POST["comp"] as $id => $gostID)
	{
		$assignedGost = [];	
		
		foreach($gostID as $idUser => $userComp)
		{
			$assignedGost[] = $idUser;	
		}
		
		$assignedGostSer = serialize($assignedGost);
		//echo "  ".$id."  ";
		
		//$DB->Query("UPDATE `ba_gost` SET `ASSIGNED` = '".$assignedGostSer."' WHERE `ID`=".$id);
		
		if (!empty($id))
		{
			
			$resultCompetentMatrix = $DB->Query("SELECT * FROM `competent_matrix` WHERE `gost_id`=" . $id);
		
			$countRowCompetentMatrix = $resultCompetentMatrix->SelectedRowsCount();
			
			//Если данные по сутрудникам для ГОСТа есть то обновляем иначе сохраняем под новым id 
			if (!empty($countRowCompetentMatrix)) 
			{
				$DB->Query("UPDATE `competent_matrix` SET `assigned` = '" . $assignedGostSer . "' WHERE `gost_id`=" . $id);
			} else {
				$DB->Query("INSERT INTO `competent_matrix` (`assigned`, `gost_id`) VALUES ('" . $assignedGostSer . "', '" . $id . "')");
			}
		
		}
	
	}
	
	// while($gost = $res_oa->Fetch())
	// { 
		// if(!$gost['NON_ACTUAL'])
		// {
		
			// $assignedGost = [];	
			// foreach($_POST["comp[".$gost['ID']."]"] as $id => $userComp)
			// {
				// $assignedGost[] = $id;	
			// }
			
			// $assigned = serialize($assignedGost);
			
			// 
			
			// // echo "<tr id=".$gost['ID']."><td >".$inOA."</td><td></td><td ><b>".$gost['GOST']." ".$gost['GOST_PUNKT']."</b></td><td></td><td >".$gost['SPECIFICATION']."</td>";
			// // foreach($usersIdDep as $userId => $userDep)
			// // {
				
				// // $ch = in_array($userId, $assignedGost) ? 'checked' : '';
				// // echo "<td><input type='checkbox' name='comp[".$userId."][".$gost['ID']."]' ".$ch." ></td>"; 
				
			// // }
		
			// // echo "</tr>";
		// }
	// }	
 
	
}


?>
<style>
.results_isp_filter, .dataTables_filter { display: none; }
.results_isp_length, .dataTables_length { display: none; }
#gost-table th { background-image: none; }
.sorting { background-image: none; }

</style>

<?php
//массив пользователей для отображения в журнале
$arrayUsers = ['60', '30', '29', '32', '40', '45', '39', '11', '12', '13', '14', '63', '24', '15', '16', '52', '58', '18',
	'31', '19', '22', '64', '51', '56', '32'];
	
/**
 * '54' - "ЛФХИ"
 * '55' - "ДСЛ"
 * '56' - "ЛФМИ"
 * '57' - "ЛСМ"
 * '58' - "ОСК"
 */
//ID лабораторий
$arrayDep = ['54','55','56','57','58']; 
//ID выбранной лаборатории
$selectedLaboratoryId = !empty($_GET['ID']) ? (int)$_GET['ID'] : 0;
//ID пользователей разрешенных для сохранения данных
$arrayIdUser = ['9', '1'];
?>

	<form method='post'>
    <!--<input type='submit' name='bt' value='Сохранить'>-->
	<div class="wrapper-top">
		<input type='submit' name='bt' class="button save-btn" value='Сохранить' <?= (!empty($selectedLaboratoryId) || !in_array($USER->GetID(), $arrayIdUser)) ? 'disabled' : '' ?>>
	
		<select class="laboratories">
			<option class="laboratory" value="" <?= empty($selectedLaboratoryId) ? 'selected' : '' ?>>Все лаборатории</option>
			<option class="laboratory" value="54" 
				<?= !empty($selectedLaboratoryId) &&  $selectedLaboratoryId === 54 ? 'selected' : '' ?>>
				Лаборатория физико-химических испытаний
			</option>
			<option class="laboratory" value="55" 
				<?= !empty($selectedLaboratoryId) &&  $selectedLaboratoryId === 55 ? 'selected' : '' ?>>
				Дорожно-строительная лаборатория
			</option>
			<option class="laboratory" value="56" 
				<?= !empty($selectedLaboratoryId) &&  $selectedLaboratoryId === 56 ? 'selected' : '' ?>>
				Лаборатория физико-механических испытаний
			</option>
			<option class="laboratory" value="57" 
				<?= !empty($selectedLaboratoryId) &&  $selectedLaboratoryId === 57 ? 'selected' : '' ?>>
				Лаборатория строительных материалов
			</option>
			<option class="laboratory" value="58" 
				<?= !empty($selectedLaboratoryId) &&  $selectedLaboratoryId === 58 ? 'selected' : '' ?>>
				Отдел строительного контроля
			</option>
		</select>
	</div>
	<br />
	<table id="gost-table" class="cell-border">
	<thead>
    <tr>
		<th>ОА</th>
		<th>ГОСТ</th>
		<th>Характеристика</th>
		
		<?php
		$filter = Array();
		//$arrayDep = array('54','55','56','57');
		//$arrayDep = array('54','55','56','57','58'); //Добавлен ОСК
		$usersAll = CUser::GetList(($by='LAST_NAME'), ($order='asc'), $filter);
		$usersIdDep = array();
		//dump($usersAll);
		while($user = $usersAll->Fetch())
		{
			$userData = CUser::GetByID($user['ID'])->Fetch();
			$userDep = $userData['UF_DEPARTMENT'][0];
			$currentUserId = !empty($userData['ID']) ? $userData['ID'] : 0; //ID пользователя
			
			//Если пользователя нет в масииве то не выводим его
			if (!in_array($currentUserId, $arrayUsers)) {
				continue;
			}
			//dump($user);
			if(in_array($userDep, $arrayDep)) $usersIdDep[$user['ID']] = $userDep;//echo "<th>".$user['LAST_NAME']."</th>"; 
		}
		
		asort($usersIdDep);
		//dump($usersIdDep);
		$dep=0;
		$nameDep='';
		foreach($usersIdDep as $userId => $userDep)
		{
			//Если выбранна лаборатория, то показываем только её данные 
			if (!empty($selectedLaboratoryId) && $userDep != $selectedLaboratoryId) {
				continue;
			}
			
			switch($userDep) {
			case 54:
				$nameDep = "ЛФХИ";
				break;
			case 55:
				$nameDep = "ДСЛ";
				break;
			case 56:
				$nameDep = "ЛФМИ";
				break;
			case 57:
				$nameDep = "ЛСМ";
				break;
			case 58:
				$nameDep = "ОСК";
				break;
			}
			
			if($userDep != $dep) echo "<th style='border-left: 1px solid #ccc'><h3>".$nameDep."</h3></th>";
			else echo "<th></th>";
			
			$dep = $userDep;
					
		}
			
		?>
	</tr>
	<tr>
		<th></th>
		<th><input type="text" name="gost_filter" id="gost_filter" style="margin:0px" value=""></th>
	
		<th></th>
	<?php
		$dep=0;
		foreach($usersIdDep as $userId => $userDep)
		{
			//Если выбранна лаборатория, то показываем только её данные 
			if (!empty($selectedLaboratoryId) && $userDep != $selectedLaboratoryId) {
				continue;
			}
			
			$userData = CUser::GetByID($userId)->Fetch();
			$emplLastName = $userData['LAST_NAME'];
			$letter = substr($emplLastName, 3, 1);
			$emplCutLN = in_array($letter, $glasArr) ? substr($emplLastName, 0, 5) : substr($emplLastName, 0, 4);
			if(strlen($emplCutLN)!=strlen($emplLastName))
				$emplCutLN = $emplCutLN.".";
			if($userDep != $dep) 
				echo "<th style='border-left: 1px solid #ccc'>".$emplCutLN."</th>";
			else
				echo "<th>".$emplCutLN."</th>";
			$dep = $userDep;		
		}
	?>
	</tr>	
	</thead>
	<tbody>
	<?php 
	
	$res_oa = $DB->Query("SELECT * FROM `ba_gost` ORDER BY `NUM_OA` DESC");	
	$arrrr=['1'];
	while($gost = $res_oa->Fetch())
	{ 
		if(!$gost['NON_ACTUAL'])
		{
			$curentGostId = (!empty($gost['ID']) ? (int)$gost['ID'] : 0);
			$selectCompetentMatrix = null;
			if (!empty($curentGostId)) 
			{
				$selectCompetentMatrix = $DB->Query("SELECT * FROM `competent_matrix` WHERE `gost_id`=" . $curentGostId)->Fetch();
			}
			
			$arrayUsers = !empty($selectCompetentMatrix['assigned']) ? unserialize($selectCompetentMatrix['assigned']) : [];
			
			//Если выбранна лаборатория, и к ГОСТу не привязаны собрудники, то не показываем ГОСТ(строку)
			if (!empty($selectedLaboratoryId)) {
		
				//Если пользователи у ГОСТА отсутствуют(не выбраны) то непоказываем ГОСТ(строку)
				if (empty($arrayUsers)) {
					continue;
				}
				
				$departament = [];
				foreach ($arrayUsers as $kyeUser => $idUser) {
					if (!empty($usersIdDep[$idUser])) {
						$departament[] = $usersIdDep[$idUser];
					}
				}
				
				//Если пользователи есть но относятся к другой лаборатории то непоказываем ГОСТ(строку)
				if (!empty($arrayUsers) && !in_array($selectedLaboratoryId, $departament)) {
					continue;
				}
				
			}
			
			//Если ГОСТ не в ОА(области акредитации) то не выводим
			if (empty($gost['IN_OA']))
			{
				continue;
			}
			
			$inOA = $gost['IN_OA'] ? 'OA' : '';
			$typeGost = (($gost['GOST_TYPE']=='TU_standart'||$gost['GOST_TYPE']=='TU_research'||$gost['GOST_TYPE']=='TU_group') ? 'ТУ' : '');
			//$assignedGost = unserialize($gost['ASSIGNED']);
			$assignedGost = !empty($selectCompetentMatrix['assigned']) ? unserialize($selectCompetentMatrix['assigned']) : '';
			
			//echo "<tr id=".$gost['ID']."><td >".$inOA."</td><td></td><td><b>".$gost['GOST']." ".$gost['GOST_PUNKT']."</b></td><td></td><td >".$gost['SPECIFICATION']."</td>";
			echo "<tr id=".$gost['ID']."><td >".$inOA."</td><td><b>".$gost['GOST']." ".$gost['GOST_PUNKT']."</b></td><td >".$gost['SPECIFICATION']."</td>";
			foreach($usersIdDep as $userId => $userDep)
			{
				//Если выбранна лаборатория, то показываем только её данные 
				if (!empty($selectedLaboratoryId) && $userDep != $selectedLaboratoryId) {
					continue;
				}
				
				//$ch = in_array($userId, $assignedGost) ? 'checked' : '';
				//echo "<td><input type='checkbox' name='comp[".$gost['ID']."][".$userId."]' ".$ch." ></td>"; 
				$ch = in_array($userId, $assignedGost) ? 'v' : '';
                echo "<td style='text-align: center'>" . $ch . "</td>"; 
				
			}
		
			echo "</tr>";
		}
	}
?>
	
	</tbody>
	</table>
	</form>


	
	<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/fixed.css"/>
	
	
    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<!--<script src="https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js"></script>-->
	<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/filtering/type-based/html.js"></script>
	
	
	
	<script>
        $(document).ready(function () {
            table = $('#gost-table').DataTable({
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
				// "columns": [
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false },
					// {"orderable": false }
					// ],
				"fixedHeader": {
					header: false,
					footer: false
				},
				"order": [[ 1, "asc" ]],
				"iDisplayLength": 50,
				"sort": false,
				"paginate": false,
				"dom": 'Blfrtip',
				"info": false,
				"buttons": [ 'copy', 'excel', 'print' ]
				
            });
			$('#gost_filter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			} );
			
			//Выбор лаборатории
			$('.laboratories').on('change', function() {
				//объект отражающий URL
				var url = new URL(window.location.href);
				//Наименование хоста
				var hostName = window.location.hostname;
				//ID выбранной лаборатории
				var selectedLaboratoryId = $(this).val();
				
				//Если ID лаборатории существует то добавляем в url параметр ID лаборатории, иначе удаляем параметр ID из url  
				if (selectedLaboratoryId) {
					url.searchParams.set('ID', $(this).val());
				} else {
					url.searchParams.delete('ID');
				}
				
				//Перезагружаем страницу
				if(hostName === '172.16.255.2') {
					window.location.href = url.href;
				}
			});
        });
		
		
    </script>    
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>