<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); 

$APPLICATION->SetTitle("Область аккредитации и перечень ТУ");
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php"; 


	$norm_dop=[];
	$val_dop=[];
    $res = $DB->Query("SELECT * FROM `ba_gost`");
	$checkArr = [
	"GOST"           => "Нормативный документ",
	"SPECIFICATION"  => "Определяемая характеристика",
	"NORM1_TEXT"     => "Норма 1",
	"NORM2_TEXT"     => "Норма 2",
	"ED"             => "Единица измерения",
	"PRICE"          => "Стоимость",
	"NORM_COMMENT"   => "Текст нормативного значения",
	"NAME_GOST"      => "Наименование ГОСТ",
	"DOP"            => "МАТЕРИАЛ",
	"GOST_TYPE"      => "Тип документа",
	"GOST_PUNKT"     => "Пункт документа",
	"ACCURACY"       => "Знаков после запятой в результате",
	"LFMI"           => "Лаборатории и сотрудники",
	"TNVED"          => "Код ТН ВЭД ЕАЭС",
	"GOST_OBJECT"    => "Наименование объекта ГОСТ",
	"PLACE"          => "Номер кабинета",
	"TEMP"           => "Температура",
	"VLAG"           => "Влажность",
	"PRESS"          => "Атм. давление",
	"ASSIGNED"       => "Ответственный",
	"GOST_YEAR"      => "Год",
	"CHECKED"        => "Проверено",
	"OKPD"           => "Код ОКПД 2"
	];
    ?>
    <table id="gost-table">
	<thead>
    <!--<tr>-->
	<tr>
		<th></th>
		<th>№ ОА</th>
        <th >Опред. характеристика</th>
        <th>Нормативный документ</th>
		<th>Пункт ГОСТ</th>
		<th>Материал</th>
		<th>Год</th>
		<th>Незаполненные поля</th>
	</tr>
	<tr class='trSearch'>
		<th style="margin:0px"></th>
		<th><input type="text" name="num_filter" id="num_filter" style="margin:0px" value=""></th>
		<th><input type="text" name="characteristic_filter" id="characteristic_filter" style="margin:0px" value=""></th>
		<th><input type="text" name="normative_filter" id="normative_filter" style="margin:0px" value=""></th>
		<th></th>
		<th><input type="text" name="gost_name_filter" id="gost_name_filter" style="margin:0px" value=""></th>
		<th></th>
		<th><input type="text" name="check_filter" id="check_filter" style="margin:0px" value=""></th>
	
	</tr>
	</thead>
	<tbody>
    <!--</tr>-->
	<div id='buttonRightTable'></div>
	<div id='buttonLeftTable'></div>
    <?php
	$i=0;
	
    while ($row = $res->Fetch()) {
		
	   $noValArr = array();
	   //$resSert = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=".$row['ID'])->Fetch();
	   //if(!$resSert['ID']) $noValArr[] = "Свидетельство о поверке";
	   foreach($row as $field => $val_field)
	   {
		   if((is_null($val_field)||$val_field=='')&&$checkArr[$field]) 
			   $noValArr[] = $checkArr[$field];
	   }
		
//		$res_ob = $DB->Query("SELECT * FROM `ba_connection` WHERE `GOST_ID` = " . $row['ID'])->Fetch();
		//var_dump($res_ob);
		?>
		
        <tr>
		<td></td>
		<td><?= ($row['NUM_OA'] ? $row['NUM_OA'] : '') ?></td>
		<td><?= (($row['SPECIFICATION']) ? $row['SPECIFICATION'] : 'Не заполнено') ?></td>
        <td> <a href='/obl_acc.php?ID=<?= $row['ID'] ?>'><?=  $row['GOST'] ?></a> </td>
		<td><?= $row['GOST_PUNKT'] ?></td>
		<td><?= $row['GOST_OBJECT'] ?></td>
        <td><?= $row['GOST_YEAR'] ?></td>
        <td><?php $n=1;
				foreach($noValArr as $noValField)
				{
				 echo $n.". ".$noValField."<br>";	
				 $n++;
				}
				 ?></td>
	<? } ?>
    
	
    </tbody></table>";



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
	<link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
	<script>
<!---'Правки протоколов'начало---->
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
<!---'Правки протоколов'окончание---->
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

	<script>
	 $(document).ready(function () {
            
			$('.obl2').select2({
				placeholder: "Выберите"
				
			});
			$('.oborud_change').select2({
				placeholder: "Выберите"
				
			});
	 });
	</script>
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
				  "targets": 5,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				}
				
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
			
			
			$('#gost_name_filter').on( 'keyup', function () {
				table
					.columns( 5 )
					.search( this.value )
					.draw();
			} );
			$('#check_filter').on( 'keyup', function () {
				table
					.columns( 7 )
					.search( this.value )
					.draw();
			} );
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
	
	if (status!="1"&&status!="10"&&status!="22"&&status!="12"&&status!="11"&&status!="13"&&status!="16"&&status!="64"&&status!="15"&&status!="34"&&status!="33"&&status!="24"&&status!="35"&&status!="13"&&status!="17"&&status!="21"&&status!="20"&&status!="58"&&status!="45"&&status!="43"&&status!="31"&&status!="62")
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
	</script>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>