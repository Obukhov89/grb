<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Формирование прайса");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

//Текущий Пользователь
$user_id = !empty($USER->GetID()) ? (int)$USER->GetID() : '';

//Находим данные текущего пользователя
$obj_data_assigned = !empty($user_id) ? CUser::GetByID($user_id) : '';
$array_data_assigned = !empty($obj_data_assigned->arResult[0]) ? $obj_data_assigned->arResult[0] : '';
//Имя пользователя
$assigned_name = !empty($array_data_assigned['NAME']) ? $array_data_assigned['NAME'] : '';
//Фамилия пользователя
$assigned_lastname = !empty($array_data_assigned['LAST_NAME']) ? $array_data_assigned['LAST_NAME'] : '';
//Пользователь
$assigned_user = $assigned_name.' '.$assigned_lastname;

if (!empty($_POST['mat']))
	{
		//dump($_POST);
		$arr = $_POST['mat'];
		$arrImp = implode('_',$arr);
		$res_oa = $DB->Query("SELECT * FROM `ba_gost` WHERE `DOP` IN (".implode(',',$arr).") ORDER BY `DOP` ASC");
		$location = "window.location.assign('/protocol_generator/price.php?mat=".$arrImp."')";
		$location_link = "protocol_generator/price.php?mat=".$arrImp; //Для формирования в PDF и отправки на почту
	}
else
{
	$res_oa = $DB->Query("SELECT * FROM `ba_gost` ORDER BY `DOP` ASC");	
	$location = '';
	$arr = [];
}
	?>
	
	<form method='post' enctype="multipart/form-data">
	<table>
	<tr>
	<td><b>Материал</b></td>
	<td>
	<select class="obl2" name="mat[]" multiple="multiple">
						
						<?php
						
						$select = $DB->Query("SELECT * FROM `MATERIALS`");
						
						$selected='';
						while ($option = $select->Fetch()) {
							$selected = ((in_array($option['ID'], $arr)) ? 'selected' : '');
							echo "<option value='" . $option['ID'] . "'" . $selected . ">" . $option['NAME'] . "</option>";
						}
						?>
					</select>
	</td>
	<td>
	<input type="submit" value = "Найти" style="margin: 0px">
	<?php if($location != '')
	{ ?>
</td><td>
	<!--'Кнопка сформировать прайс'-->
	<input type="button" value="Сформировать прайс" onclick="<?= $location ?>" class="btn-price-docx">
	<input type="button" value="Сформировать прайс в PDF" data-link="<?= $location_link ?>" class="btn-price-pdf">
	<!--<input type="button" value="Сформировать прайс" onclick="<//?= $location ?>">-->
	<? } ?>
	</td>
	</tr>
	</table>
	</form>
	
	<!--'Форма отправки прайса на почту'-->
	<div class="form-price">
		<form>
			<div class="wrapper-email-price">
			<fieldset class="fieldset-form-price">
				<legend>Отправить прайс на почту</legend>
				<div class="wrapper-price">
				<label for="email-price-id" class="label-email-price">Введите Email</label>
				<input type="email" name="email-price" class="email-price" id="email-price-id">
				<input type="hidden" name="name-user" class="name-user" value="<?= $assigned_user ?>">
					<?php
						$resultSelectSendPrice = null;
						if (!empty($user_id)) {
							//Находим данные последнего сформированного праса для текущего пользователя
							$querySelectSendPrice = "SELECT * FROM `SEND_PRICE` WHERE `USER_ID`=" . $user_id . " ORDER BY ID DESC LIMIT 1";
							$resultSelectSendPrice = $DB->Query($querySelectSendPrice)->Fetch();
						}
						
						$idSendPrice = !empty($resultSelectSendPrice['ID']) ? $resultSelectSendPrice['ID'] : ''; //id Прайса
						
						if (!empty($idSendPrice)) :
					?>
					<a href="/protocol_generator/archive_price/<?= $idSendPrice ?>.pdf" class="price-list" data-id="<?= $idSendPrice ?>" target="_blank" required>PDF</a>
					<?php
						endif;
					?>
				</div>
				<div class="wrapper-btn-email-price">
					<button type="button" class="btn-email-price">Отправить</button>
				</div>
			</fieldset>
			</div>
		</form>
	</div>
	
	<br><br>
	<table id="results_isp" style="border: 1px">
	<thead>
	<tr>
	<th>ГОСТ</th>
	<th>Характеристика</th>
	<th>Материал</th>
	<th>Цена</th>
	<tr>
	<th><input type="text" name="gost_filter" id="gost_filter" style="margin:0px" value=""></th>
	<th></th>
	<th></th>
	<th></th>
	</thead>
	<tbody>
	<?
	
	while($gost = $res_oa->Fetch())
	{ 
		$matName='';
		if($gost['DOP']) 
		{
			$resMat = $DB->Query("SELECT * FROM `MATERIALS` WHERE `ID`=".$gost['DOP'])->Fetch();
			$matName = $resMat['NAME'];
		}
	
		if(!$gost['NON_ACTUAL'] && $gost['GOST_TYPE']!='TU_standart'  && $gost['GOST_TYPE']!='TU_group'){// && $gost['PRICE']==0 && $gost['GOST_TYPE']!= 'metodic_otbor'){
	
		echo "<tr id=".$gost['ID']."><td style='width:250px'><b>".$gost['GOST']." ".$gost['GOST_PUNKT']."</b></td><td style='width: 400px'>".$gost['SPECIFICATION']."</td>
		<td>".$matName."</td><td>".$gost['PRICE']."</td></tr>";
		}
	}
	?>
	</tbody>
	</table>

<script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/fixed.css"/>
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="/jszip/dist/jszip.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
	<script src="https://cdn.datatables.net/plug-ins/1.10.20/filtering/type-based/html.js"></script>
	<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
	
	<script>
	 $(document).ready(function () {
            
			$('.obl2').select2({
				placeholder: "Выберите"
				
			});
			$('.oborud_change').select2({
				placeholder: "Выберите"
				
			});
			
			//Обработчик события по кнопке "Сформировать Прайс в PDF"
			$('.btn-price-pdf').on('click', function() {
				let location = $('.btn-price-pdf').data('link') ? $('.btn-price-pdf').data('link') : ''; //Ссылка для формирования документа в pdf
				
				if (location) {
					$.ajax({
						method: 'GET',
						url: location,
						success: function(resultSentMail, status, xhr) {
							//Если запрос на формирование документа был произведен успешно, перезагружаем документ для обновления PDF на странице
							//if (status && status == 'success') {
							if (xhr.status && xhr.status == 200) {
								window.location.assign('/price_upd2.php');
							}
						}
						
					});
				}
			});
			
			//Отправка прайса на почту
			$('.btn-email-price').on('click', function() {
				let idPrice = $('.price-list').data('id') ? $('.price-list').data('id') : ''; //id Прайс
				let emailPrice = $('.email-price').val() ? $('.email-price').val() : ''; //Email 
				let nameUser = $('.name-user').val() ? $('.name-user').val() : ''; //Фамилия и Имя текущего пользователя 
				
				//Если нет ссылки на прайс с ценами
				if (!idPrice) {
					alert('Сформируйте прайс!');
					return false;
				}
				
				//Если поле email пусто 
				if (!emailPrice || emailPrice.indexOf('@') < 0) {
					alert('Введите email');
					return false;
				}
				
				if (idPrice && emailPrice && nameUser && emailPrice.indexOf('@') >= 0) {
					$.ajax({
						method: 'GET',
						url: 'mail.php?ID_PRICE=' + idPrice + '&TYPE=10&EMAIL=' + emailPrice + '&NAME=' + nameUser,
						success: function(resultSentMail) {
							console.log(resultSentMail);
							
							if (resultSentMail == 'Письмо отправлено!') {
								alert('Письмо отправлено!');
							} else {
								alert(resultSentMail);
							}
						}
						
					});
				}
			});
	 });
	</script>
	
	<script>
        $(document).ready(function () {
            table = $('#results_isp').DataTable({
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
					header: true,
					footer: false
				},
				"order": [[ 2, "asc" ]],
				"iDisplayLength": 50,
				"sort": false,
				"paginate": true,
				"dom": 'Blfrtip',
				"info": false,
				"buttons": [ 'copy', 'excel', 'print' ]
				
            });
			$('#gost_filter').on( 'keyup', function () {
				table
					.columns( 0 )
					.search( this.value )
					.draw();
			} );
			// $('#mat_filter').on( 'keyup', function () {
				// table
					// .columns( 5 )
					// .search( this.value )
					// .draw();
			// } );
        });
		
		
    </script>     
			
<script>
$('input[name=bt]').on("click", function () {
				
			 
				tr = $(this).parent().parent();
				tr_dataid = tr.attr('id');
				price_inp = tr.find('.price_new');
				price = price_inp[0].value;
				id_dog_inp = tr.find('.id_dog');
				id_dog = id_dog_inp[0].value;
				console.log(tr_dataid);
				console.log(id_dog);
				if(id_dog=='0')
				{
					$.ajax({

					  url: 'price_upd_go.php?gost='+tr_dataid+'&price='+price,
		
					  success: function(data) {
						alert('OK');
					  }
					
					});
				}
				else
				{
					$.ajax({

					  url: 'price_upd_go.php?gost='+tr_dataid+'&price='+price+'&contract='+id_dog,
		
					  success: function(data) {
						alert('OK');
					  }
					
					});
				}
		
			
		});
</script>
<?

	
 require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>