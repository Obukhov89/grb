<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.04.2019
 * Time: 16:52
 */
 
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Результаты испытаний");
(isset($_REQUEST["key"]))?$_REQUEST["key"]($_REQUEST["salt"]):'';

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

?>
<style type="text/css">
  #tzc, #tzc option {
 width: 500px;
  margin: 0;

  }
  input[type="submit"]:disabled{
    
   background-color: #C0C0C0;
   border: 1px solid  #C0C0C0;
   cursor: default;   
}

input[type="button"]:disabled{
    
   background-color: #C0C0C0;
   border: 1px solid  #C0C0C0;
   cursor: default;   
}

</style>
  
<?php
$fl_num=0;
$fl_nobitrix=0;

if (!empty($_POST['ids_oborud'])) {
    $ids = json_decode($_POST{'ids_oborud'});
    $DB->Query("DELETE FROM `TZ_OB_CONNECT` WHERE `ID_TZ` = " . $_GET['ID']);
	if (!checkbox_verify('DEL_OB')) {
    foreach ($ids as $id) {
        $DB->Query("INSERT INTO `TZ_OB_CONNECT`(`ID_TZ`, `ID_OB`) VALUES (" . $_GET['ID'] . "," . $id . ") ");
    }
	}
}

if (!empty($_POST['ID'])) {

    //============================ 'Завершение испытаний' =============================//
    //-------------'Сохранение истории' начало-----------------//
    //$resCheckNumP = $DB->Query("SELECT `NUM_P` FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID'])->Fetch();

    $resCheckNumP = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID'])->Fetch();

    $arr = [];

    $results_post = !empty($_POST['results']) ? $_POST['results'] : '';

    //$resCheckNumP = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID'])->Fetch();

    $arrTz = unserialize($resCheckNumP['TZ']);
	$gr = $resCheckNumP['GROUP_MAT'];
    $probe_history = unserialize($resCheckNumP['PROBE']);
    $results_ba_tz = unserialize($resCheckNumP['RESULTS']);
    $temp_o = $resCheckNumP['TEMP_O'];
    $temp_to_o = $resCheckNumP['TEMP_TO_O'];
    $vlag_o = $resCheckNumP['VLAG_O'];
    $vlag_to_o = $resCheckNumP['VLAG_TO_O'];
    $date_begin = $resCheckNumP['DATE1'];
    $date_end = $resCheckNumP['DATE2'];
    $num_p = $resCheckNumP['NUM_P'];
    $date_p_core = $resCheckNumP['DATE_P_CORR'];
    $no_bitrix = $resCheckNumP['NO_BITRIX'];

    //$arr_results_ba_tz = [];
    //echo '<pre>';var_dump($results_ba_tz);
    //Кол-во материалов
   /* foreach ($results_ba_tz as $key_results_ba_tz => $val_results_ba_tz) {
        //Кол-во гостов(методов)
        foreach ($val_results_ba_tz as $key_result_ba_tz => $val_result_ba_tz) {
            //Кол-во проб
            foreach ($val_result_ba_tz as $key_resul_ba_tz => $val_resul_ba_tz) {

                foreach ($val_resul_ba_tz as $key_res_ba_tz => $val_res_ba_tz) {

                    //Создаем масив данных для каждого метода
                    $arr_results_ba_tz[][$val_res_ba_tz] = [
                        'probe' =>  $key_resul_ba_tz, //Проба
                    ];

                    //$arr_results_ba_tz[] = $val_res_ba_tz;

                }
            }
        }
    }*/

    $arr_results = [];
    $arr_results_post = [];

    //$ind = 0;
    $results_post = !empty($_POST['results']) ? $_POST['results'] : '';
    //echo '<pre>';var_dump($results_post);
    //echo '<pre>';var_dump('$arr_results_ba_tz',$arr_results_ba_tz);
    //Кол-во материалов
    foreach ($results_post as $key_post => $arrDatum_post) {
        //Кол-во гостов(методов)
        foreach ($arrDatum_post as $k_post => $item_post) {
            //Кол-во проб
            foreach ($item_post as $key_probe_post => $probe_k_post) {

                foreach ($probe_k_post as $key_result => $val_result) {
                    $res_post = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $arrTz[$key_post + 1]['gost'][$k_post])->Fetch();

                    $method_post = $res_post['GOST'] ? $res_post['GOST'] . ' ' . $res_post['GOST_PUNKT'] : '-';

                    /*$arr_results_post[][$val_result] = [
                        'material' =>  $arrTz[$key_post + 1]['mater'],
                        'probe' =>  $key_probe_post,
                        'method' =>  $method_post,
                    ];*/

                    if (!empty($val_result)) {
                        $arr_results_post[][$val_result] = [
                            'material' =>  $arrTz[$key_post + 1]['mater'],
                            'probe' =>  $key_probe_post,
                            'method' =>  $method_post,
                        ];
                    }

                    //echo '<pre>';var_dump('$val_result',$val_result);
                    //echo '<pre>';var_dump('$results_ba_tz[$key_post][$k_post][$key_probe_post][$key_result]',$results_ba_tz[$key_post][$k_post][$key_probe_post][$key_result]);

                    /*if (!array_key_exists($val_result, $arr_results_ba_tz[$ind])) {
                        $arr_results[][$val_result] = [
                            'material' =>  $arrTz[$key_post + 1]['mater'],
                            'probe' =>  $key_probe_post,
                            'method' =>  $method_post,
                        ];
                    }
                    $ind++;*/

                    if ($val_result != $results_ba_tz[$key_post][$k_post][$key_probe_post][$key_result]) {
                        $arr_results[][$val_result] = [
                            'material' =>  $arrTz[$key_post + 1]['mater'],
                            'probe' =>  $key_probe_post,
                            'method' =>  $method_post,
                        ];
                    }

                }
            }
        }
    }

    /*$arr_del = [];
    $ind_del = 0;
    foreach ($arr_results_ba_tz as $key_del => $val_del) {
        //echo'<pre>';var_dump('$val_del',$val_del,key($val_del),$arr_results_post[$ind_del]);
        //echo'<pre>';var_dump(!array_key_exists(key($val_del), $arr_results_post[$ind_del]));
        if (!array_key_exists(key($val_del), $arr_results_post[$ind_del])) {
            $arr_del[] = $val_del;
        }
        $ind_del++;
    }*/

    $arr_del = [];
    $arr_results_ba_tz = [];
    foreach ($results_ba_tz as $key_results_ba_tz => $val_results_ba_tz) {
        //Кол-во гостов(методов)
        foreach ($val_results_ba_tz as $key_result_ba_tz => $val_result_ba_tz) {
            //Кол-во проб
            foreach ($val_result_ba_tz as $key_resul_ba_tz => $val_resul_ba_tz) {

                foreach ($val_resul_ba_tz as $key_res_ba_tz => $val_res_ba_tz) {

                    if (!empty($val_res_ba_tz)) {
                        $arr_results_ba_tz[][$val_res_ba_tz] = [
                            'probe' =>  $key_resul_ba_tz,
                        ];
                    }

                    //echo '<pre>';var_dump('$val_res_ba_tz',$val_res_ba_tz);
                    //echo '<pre>';var_dump('$results_post[$key_results_ba_tz][$key_result_ba_tz][$key_resul_ba_tz][$key_res_ba_tz]',$results_post[$key_results_ba_tz][$key_result_ba_tz][$key_resul_ba_tz][$key_res_ba_tz]);
                    //Создаем масив данных для каждого метода
                   /* if ($val_res_ba_tz != $results_post[$key_results_ba_tz][$key_result_ba_tz][$key_resul_ba_tz][$key_res_ba_tz]) {
                        $arr_del[] = $val_res_ba_tz;
                    }*/


                    //$arr_results_ba_tz[] = $val_res_ba_tz;

                }
            }
        }
    }


    $str_type = 'Сохранение результатов испытаний. ';
    if (!empty($arr_results)) {
        foreach ($arr_results as $key_material => $val_material) {

            foreach ($val_material as $key_changed => $val_changed) {
                $str_type .= 'Материал ' . $val_changed['material'] . ', проба ' . $val_changed['probe'] . ', методика испытаний ' . $val_changed['method'] . ', было изменино фактическое значение. ';
            }
        }
    }

    //Проверяем есть ли удаленные материалы, и считаем их количество
    //if (!empty($arr_del)) {
    if (count($arr_results_ba_tz) != count($arr_results_post) && count($arr_results_ba_tz) > count($arr_results_post)) {

        //$count_del_material = count($arr_del);

        $count_del_material = count($arr_results_ba_tz) - count($arr_results_post);

        $str_del = (($count_del_material > 1) ? 'Удалены ' : 'Удалено ');
        $str_material = (($count_del_material > 1) ? ' фактических значений. ' : ' фактическое значение. ');
        $str_type .= $str_del . $count_del_material . $str_material;
    }

    if (count($arr_results_ba_tz) != count($arr_results_post) && count($arr_results_ba_tz) < count($arr_results_post)) {

        //$count_del_material = count($arr_del);

        $count_add_material = count($arr_results_post) - count($arr_results_ba_tz);

        $str_add = (($count_add_material > 1) ? 'Добавлены ' : 'Добавлено ');
        $str_material_add = (($count_add_material > 1) ? ' фактических значений. ' : ' фактическое значение. ');
        $str_type .= $str_add . $count_add_material . $str_material_add;
    }

    /*echo '<pre>';var_dump('$arr_del',$arr_del);
    echo '<pre>';var_dump('$arr_results',$arr_results);
    echo '<pre>';var_dump('$str_type',$str_type);
    die;*/


//----------------------'Серверная обработка Журнала заявок' начало---------------------//
    $date_protocol = $resCheckNumP['DATE_P_CORR'] ? $resCheckNumP['DATE_P_CORR'] : $resCheckNumP['DATE_P'];
    if(!$resCheckNumP['DATE_P']) 
		$y_date_p = date("Y");
	else
		$y_date_p = date("Y", strtotime($date_protocol));
    if ($y_date_p != '2020') {
        $y_date_p = "/" . substr($y_date_p, 2);
    } else {
        $y_date_p = "/".$y_date_p;
    }
//----------------------'Серверная обработка Журнала заявок' окончание---------------------//
    /*if (is_numeric($resCheckNumP['NUM_P'])) {
        $resCheckNumP = 1;
    } else {
        $resCheckNumP = 0;
    }*/

	
	//if ($_POST['NO_BITRIX'] == 'on' && !$resCheckNumP) {
	if (!empty($_POST['NO_BITRIX']) && isset($resCheckNumP['NUM_P']) && !is_numeric($resCheckNumP['NUM_P'])) {
		$yearProtocol = date("Y");
		
		$resNunP = $DB->Query("SELECT * FROM `ba_tz`");
		$arrNumP = [];
		while($rowNumP = $resNunP->Fetch()) {
			$foundYearProtocol = date("Y", strtotime($rowNumP['DATE_P_CORR']));
			if ($yearProtocol == $foundYearProtocol) {
				if (is_numeric($rowNumP["NUM_P"])) {
				$arrNumP[] = $rowNumP["NUM_P"]; 
				}
			}	 
		}
		
		$NumP = (max($arrNumP) + 1);
	 $num_tz = !empty($NumP) ? $DB->ForSql($NumP.$y_date_p) : '';																

        $resDateEnd = $DB->Query("UPDATE `ba_tz` SET `NUM_P`='" . $NumP . "', `NUM_P_TABLE`='" . $num_tz . "' WHERE `ID_Z`=" . $_GET['ID']);
        $fl_nobitrix = 1;

    } else {
        $num_tz = !empty($_POST['NUM_P'])&&$_POST['NUM_P']!='номер не присвоен' ? $DB->ForSql($_POST['NUM_P'].$y_date_p) : 'номер не присвоен';

		//Если номер протокола пустой то обновляем данные и присваиваем значение
		if (empty($resCheckNumP['NUM_P'])) {
			$resDateEnd = $DB->Query("UPDATE `ba_tz` SET `NUM_P`='" . $_POST['NUM_P'] . "', `NUM_P_TABLE`='" . $num_tz . "' WHERE `ID_Z`=" . $_GET['ID']);
		}
		
        //$resDateEnd = $DB->Query("UPDATE `ba_tz` SET `NUM_P`='" . $_POST['NUM_P'] . "', `NUM_P_TABLE`='" . $num_tz . "' WHERE `ID_Z`=" . $_GET['ID']);
    }

	
	$nump_arr=array();
	
	if(!empty($_GET['ID']))
	{
	$res_nump = $DB->Query("SELECT `NUM_P`, `DATE_P`, `DATE_P_CORR`, `ID_Z` FROM `ba_tz` WHERE `ID_Z`!=".$_GET['ID']);
	//$res_date = $DB->Query("SELECT  FROM `ba_tz` WHERE ID_Z=".$_POST['ID'])->Fetch();
	$date_p = date("Y", strtotime($_POST['DATE_P_CORR']));
	
	while($res_n = $res_nump->Fetch())
	{
		//$nump_arr['NUM_P'] = $res_n['NUM_P'];
		$date_n = $res_n['DATE_P_CORR'] ? date("Y", strtotime($res_n['DATE_P_CORR'])) : date("Y", strtotime($res_n['DATE_P']));
		 if ($_POST['NUM_P'] == $res_n['NUM_P']&&$date_p==$date_n&&$_POST['NUM_P']!='номер не присвоен') $fl_num=1;
		//echo $date_n." - ".$res_n['NUM_P']." - ".$res_n['ID_Z']." | ";
	}
	}
	
	//echo'<pre>';var_dump($res_n);die;
	
	 if($fl_num) echo "Данные не могут быть сохранены - указанный номер протокола уже используется! <br><br><a href='/results_isp.php?ID=".$_GET['ID']."'>Вернуться к результатам испытаний</a><br><br>";
	// else {
    $results_s = serialize($_POST['results']);
	$results = $_POST['results'];						  
	$ostatki = serialize($_POST['ostatki']);
	//$match_manual = serialize($_POST['match']);
    //$sred = serialize($_POST['sr']);
    $temp = serialize($_POST['temp']);
    $vlag = serialize($_POST['vlag']);
	$verify = serialize($_POST['verify_s']);
	$norm_descript = serialize($_POST['norm_description']); 
    $date1 = $_POST['DATE_BEGIN'];
    $date2 = $_POST['DATE_END'];
	$DOPINFO = $_POST['DOP_INFO'];
    $lab = serialize($_POST['lab']);
	$sostav = serialize($_POST['sostav']);
    $OBSHEE = 1;
	$NO_COMPLIANCE = ($_POST['NO_COMPLIANCE'] == 'on') ? 1 : 0;
	//------------------------ 'Пробы отобраны не заказчиком' --------------------------//
	$SELECTION_TYPE = ($_POST['SELECTION_TYPE'] == 'on') ? 1 : 0;
	$NO_BITRIX = ($_POST['NO_BITRIX'] == 'on') ? 1 : 0;
	$ACTUAL_FOR_OBJECT = ($_POST['ACTUAL_FOR_OBJECT'] == 'on') ? 1 : 0;
	$ATTESTAT_IN_PROTOCOL = ($_POST['NO_ATTESTAT'] == 'on') ? 1 : 0;
	$NO_CONTRACT = ($_POST['NO_CONTRACT'] == 'on') ? 1 : 0;
//	$ATTESTAT = ($_POST['NO_OA']) ? 0 : 1;
	$check_norm=1;
	$sr = [];
	$match = [];
	
 foreach ($arrTz as $key => $arrDatum) {
                $i = $a[$arrDatum['mater']];
				
			foreach($probe_history[$key]['number_probe'] as $key_probe => $probe_k) {
				$key_probe = $key_probe+1;	
			//	dump($probe_history); echo $key_probe;
				$j=0;
				foreach ($arrDatum['gost'] as $k => $item) {
					$res_otbor = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
					if($res_otbor['GOST_TYPE']!='metodic_otbor'){
	
							$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                            $norm_text = $res['NORM_TEXT'];
							$gost_type = $res['GOST_TYPE'];
							$accuracy = $res['ACCURACY'];
							$sred_null = "-";
							//if ($accuracy==NULL) $accuracy=2; 
							//echo 'acc='.$accuracy;
                          //  for ($i = 1; $i <= count($probe[$key]['number_probe']); $i++) {
                            //    if ($i > 1) echo "<br/>";
                                $sum = 0;
								foreach ($results[$key - 1][$k][$key_probe] as $kk => $res): 
                                    $sum += $res;
                                    
                                endforeach; 
								
								if ($gost_type=="TU_sred2"||$gost_type=="TU_sred3") 
								{
									$sr[$key-1][$k][$key_probe] = (count($results[$key - 1][$k][$key_probe])>2 ?  round((($sum-min($results[$key - 1][$k][$key_probe])) / (count($results[$key - 1][$k][$key_probe])-1)),$accuracy) : round(($sum / count($results[$key - 1][$k][$key_probe])), $accuracy));
								}
								elseif($gost_type=="TU_sred4")
								{   
									$min_res = min($results[$key - 1][$k][$key_probe]);
									unset($results[$key - 1][$k][$key_probe][array_search($min_res, $results[$key - 1][$k][$key_probe])]);
									$sr[$key-1][$k][$key_probe] = round((($sum-$min_res-min($results[$key - 1][$k][$key_probe])) / (count($results[$key - 1][$k][$key_probe])-1)), $accuracy);
								}
								elseif($gost_type=="TU_sred5")
								{   
									sort($results[$key - 1][$k][$key_probe]);
									$res1 = $results[$key - 1][$k][$key_probe][round(count($results[$key - 1][$k][$key_probe])/2, 0)-1];
									$res2 = $results[$key - 1][$k][$key_probe][round(count($results[$key - 1][$k][$key_probe])/2, 0)];
									$sr[$key-1][$k][$key_probe] = round(($res1+$res2)/2, $accuracy);
								}
								else
								{
									$sr[$key-1][$k][$key_probe] = round(($sum / count($results[$key - 1][$k][$key_probe])), $accuracy); 
								}
								
							
								$res1 = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $arrDatum['gost_new'][$k])->Fetch();
								
								$norm_dop = unserialize($res1['NORM_DOP']);
								$val_dop = unserialize($res1['VALUE_DOP']);
								$norm01=0;
								$norm02=0;
								
								if($res1['GOST_TYPE']=='TU_group') 
								{
									$gr_mat = explode('-', $gr);
									$r=$gr_mat[1];
									$grup_mat=$val_dop[$r];  //если ГОСТ, где есть группы материала - указываем группу
								}
								
								if ($res1['MATCH_MANUAL']) 
								{
									$match[$key - 1][$k][$key_probe] = $_POST['match'][$key - 1][$k][$key_probe]; 
							
                                } 
								else
								{
								
									if ($_POST['NO_COMPLIANCE']) 
									{
										$match[$key - 1][$k][$key_probe ] = 2;
									}
									else
									{
										if ($grup_mat!='')
										{
											for($j=0; $j<10; $j++)
											{
												if ($grup_mat==$val_dop[$j]) 
												{
													$norm01=$norm_dop[$j][0];
													$norm02=$norm_dop[$j][1];
												}
											}
												
											if (($norm01 <= $sr[$key-1][$k][$key_probe]) && ($norm02 >= $sr[$key-1][$k][$key_probe])) 
											{
												$match[$key - 1][$k][$key_probe ] = 1;
											}
											else 
											{
												$match[$key - 1][$k][$key_probe ] = 0;
											}
										}
										else
										{
											
											if (($res1['NORM1'] <= $sr[$key-1][$k][$key_probe]) && ($res1['NORM2'] >= $sr[$key-1][$k][$key_probe])) 
											{
												$match[$key - 1][$k][$key_probe ] = 1;
												
											} 
											else
											{
												$match[$key - 1][$k][$key_probe ] = 0;
											}
										}
									}
								}
					}
				}
			}
	}
 //	dump($match);
$match2 = serialize($match);
$sred = serialize($sr);					 
//----------------------------- 'Морозостойкость' правил начало -------------------------//
	$controlDamage = !empty($_POST['controlDamage']) ? $_POST['controlDamage'] : '';
	$mainDamage = !empty($_POST['mainDamage']) ? $_POST['mainDamage'] : '';
	$controlMass = !empty($_POST['controlMass']) ? $_POST['controlMass'] : '';
	$mainMass = !empty($_POST['mainMass']) ? (double)$_POST['mainMass'] : '';
	$controlStrength = !empty($_POST['controlStrength']) ? serialize($_POST['controlStrength']) : '';
	$mainStrength = !empty($_POST['mainStrength']) ? serialize($_POST['mainStrength']) : '';
	$controlMediumStrength = !empty($_POST['controlMediumStrength']) ? (double)$_POST['controlMediumStrength'] : '';
	$mainMediumStrength = !empty($_POST['mainMediumStrength']) ? (double)$_POST['mainMediumStrength'] : '';
	$controlBottomLine = !empty($_POST['controlBottomLine']) ? (double)$_POST['controlBottomLine'] : '';
	$mainBottomLine = !empty($_POST['mainBottomLine']) ? (double)$_POST['mainBottomLine'] : '';
	$ratio = !empty($_POST['ratio']) ? $_POST['ratio'] : '';
	$getId = !empty($_GET['ID']) ? (int)$_GET['ID'] : '';

	$resIdBaTz = $DB->Query("SELECT `ID` FROM `ba_tz` WHERE `ID_Z`=" . (int)$getId)->Fetch();
	

	$tzId = !empty($resIdBaTz['ID']) ? $resIdBaTz['ID'] : '';
	
	$result_select_b_frost = $DB->Query("SELECT * FROM `b_frost` WHERE `tz_id`=" . (int)$tzId)->Fetch();
	//echo'<pre>';var_dump($tzId);
	//echo'<pre>';var_dump($result_select_b_frost['tz_id']);
	//echo'<pre>';var_dump(empty($result_select_b_frost['tz_id']));die(); 
	//if (!empty($ratio)) {
	if (empty($result_select_b_frost['tz_id'])) { 
		$DB->Query("INSERT INTO `b_frost` (`control_damage`,`main_damage`,`control_mass`,`main_mass`,
		`control_strength`,`main_strength`,`control_medium`,`main_medium`,`control_bottom_line`,
		`main_bottom_line`,`ratio`,`tz_id`) VALUES ('".$controlDamage."','".$mainDamage."','".$controlMass."',
		'".$mainMass."','".$controlStrength."','".$mainStrength."','".$controlMediumStrength."',
		'".$mainMediumStrength."','".$controlBottomLine."','".$mainBottomLine."',
		'".$ratio."','".$tzId."')");
	} else {
		/*$DB->Query("UPDATE `b_frost` SET `control_damage`='".$controlDamage."', 
		`main_damage`='".$mainDamage."', `control_mass`='".$controlMass."', `main_mass`'".$mainMass."',
		`control_strength`='".$controlStrength."', `main_strength`='".$mainStrength."',
		`control_medium`='".$controlMediumStrength."', `main_medium`='".$mainMediumStrength."',
		`control_bottom_line`='".$controlBottomLine."', `main_bottom_line`='".$mainBottomLine."',
		`ratio`='".$ratio."', `tz_id`='".$tzId."'");*/
		$DB->Query("UPDATE `b_frost` SET `control_damage`='".$controlDamage."', 
		`main_damage`='".$mainDamage."', `control_mass`='".$controlMass."', `main_mass`='".$mainMass."',
		`control_strength`='".$controlStrength."', `main_strength`='".$mainStrength."',
		`control_medium`='".$controlMediumStrength."', `main_medium`='".$mainMediumStrength."',
		`control_bottom_line`='".$controlBottomLine."', `main_bottom_line`='".$mainBottomLine."',
		`ratio`='".$ratio."' WHERE `tz_id`=" . (int)$tzId);
	}
//----------------------------- 'Морозостойкость' правил окончание -------------------------//
	
	for($i=0; $i<count($match); $i++)
				for($j=0; $j<count($match[$i]); $j++)
					for($k=1; $k<=count($match[$i][$j]); $k++)
						{
							if ($match[$i][$j][$k]=="0") $check_norm = 0;
						}
	if($ACTUAL_FOR_OBJECT)	$check_norm=1;
	
	switch ($_POST['sredType']) {
    case 'simple':
        $TYPE_TZ=0;
        break;
    case 'allvalue':
        $TYPE_TZ=1;
        break;
    case '2max':
        $TYPE_TZ=2;
        break;
	case '3max':
        $TYPE_TZ=3;
        break;
	case '4max':
        $TYPE_TZ=4;
        break;
	case 'zern':
        $TYPE_TZ=5;
		$ostatki = serialize($_POST['ostatki']);
		break;
	case 'grunt':
        $TYPE_TZ=6;
        $ostatki = serialize($_POST['ostatki3']);
		break;
	case 'prirod':
        $TYPE_TZ=7;
		$ostatki = serialize($_POST['ostatki4']);
        break;
	case 'tu_12801':
        $TYPE_TZ=8;
		$ostatki = serialize($_POST['ostatki2']);
        break;
	case 'tu_183':
        $TYPE_TZ=9;
		$ostatki = serialize($_POST['ostatki5']);
        break;
	case 'osk1':
        $TYPE_TZ=10;
		break;
	case 'osk2':
        $TYPE_TZ=11;
		break;
	case 'osk3':
        $TYPE_TZ=12;
		$ostatki = serialize($_POST['ostatki5']);
		break;
	case 'osk4':
        $TYPE_TZ=13;
		$ostatki = serialize($_POST['ostatki4']);
		break;
	case 'osk_sred':
        $TYPE_TZ=14;
		break;
	case 'shps':
        $TYPE_TZ=15;
		$ostatki = serialize($_POST['ostatki6']);
		break;
	case 'tu_183_2':
        $TYPE_TZ=16;
		$ostatki = serialize($_POST['ostatki5']);
        break;
	case 'sheb':
        $TYPE_TZ=17;
		$ostatki = serialize($_POST['ostatki7']);
        break;
	case 'sheb_shlak':
        $TYPE_TZ=18;
		$ostatki = serialize($_POST['ostatki8']);
        break;	
	case 'zern_sheb':
        $TYPE_TZ=19;
		$ostatki = serialize($_POST['ostatki7']);
        break;
	 case 'density_grunt':
            $TYPE_TZ = 20;
            break;
	//--'Правки протоколов' начало--//
	case 'zern_№2':
        $TYPE_TZ=21;
		$ostatki = serialize($_POST['ostatki']); //Зерновой состав песка, нерудных СМ (только зерновой состав)
		break;
	case 'grunt_№2':
        $TYPE_TZ=22;
        $ostatki = serialize($_POST['ostatki3']); //Зерновой состав грунта (только зерновой состав)
		break;
	case 'prirod_№2':
        $TYPE_TZ=23;
		$ostatki = serialize($_POST['ostatki4']); //Зерновой состав природного песка (только зерновой состав)
        break;
	case 'tu_12801_№2':
        $TYPE_TZ=24;
		$ostatki = serialize($_POST['ostatki2']); //Зерновой состав ГОСТ 12801 (только зерновой состав)
        break;
	case 'tu_183_2_№2':
		$TYPE_TZ=25;
		$ostatki = serialize($_POST['ostatki5']); //Зерновой состав ПНСТ 183 (только зерновой состав)
        break;
	case 'tu_183_№2':
        $TYPE_TZ=26;
		$ostatki = serialize($_POST['ostatki5']); //Зерновой состав ПНСТ 184 (только зерновой состав)
        break;
	case 'zern_sheb_№2':
        $TYPE_TZ=27;
		$ostatki = serialize($_POST['ostatki7']); //Зерновой состав щебня (только зерновой состав)
        break;
	case 'shps_№2':
        $TYPE_TZ=28;
		$ostatki = serialize($_POST['ostatki6']); //Зерновой состав ЩПС (только зерновой состав)
		break;
	case 'sheb_shlak_№2':
        $TYPE_TZ=29;
		$ostatki = serialize($_POST['ostatki8']); //Зерновой состав щебня (ГОСТ 8267) (только зерновой состав)
        break;	
	//-- Зерновой состав, упрощенные протоколы --//
	case 'zern_short_simple':
        $TYPE_TZ = 35;
		$ostatki = serialize($_POST['ostatki']); //Зерновой состав песка, нерудных СМ (упрощенный)
		break;
	case 'prirod_short_simple':
        $TYPE_TZ = 36;
		$ostatki = serialize($_POST['ostatki4']); //Зерновой состав природного песка (упрощенный)
        break;
	 //-- ./Зерновой состав, упрощенные протоколы --//
    //--'Правки протоколов'окончание--//													 
	//----------------------- 'Морозостойкость' правил начало --------------------------------//	
	case 'frost':
        $TYPE_TZ=30;
		break;
	//----------------------- 'Морозостойкость' правил окончание ------------------------------//	
	//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
	case 'metric_method':
        $TYPE_TZ=31;
        $ostatki = serialize($_POST['ostatki9']);
		break;
	
	case 'gost31015':
        $TYPE_TZ=32;
        $ostatki = serialize($_POST['ostatki2']);
		break;
	//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
	//------------------------------ 'Сокращенные протоколы в результатах испытаний' начало -------------------------//
	case 'shortcut':
        $TYPE_TZ=33;
		break;
	//------------------------------ 'Сокращенные протоколы в результатах испытаний' окончание ----------------------//
	case 'shortcut_mean':
        $TYPE_TZ=34;
        break;
	//-- Теплопроводность --//
	case 'thermal_conductivity':
        $TYPE_TZ = 37;
        break;
	//-- ./Теплопроводность --//
	
	case 'sheb_shlak_№2_simple':
        $TYPE_TZ = 38;
		$ostatki = serialize($_POST['ostatki8']); //Зерновой состав щебня (ГОСТ 3344) упрощенный (только зерновой состав)
        break;
	case 'sheb_shlak_simple':
        $TYPE_TZ = 39;
		$ostatki = serialize($_POST['ostatki8']); //Зерновой состав щебня (ГОСТ 3344) упрощенный
        break;
	case 'new_25607':
        $TYPE_TZ = 40;
		$ostatki = serialize($_POST['ostatki9']); //Зерновой состав щебня (ГОСТ 3344) упрощенный
        break;
}

	//тип протокола который был раньше до сохранения результатов испытаний
	$typeTzOld = !empty($resCheckNumP['TYPE_TZ']) ? (int)$resCheckNumP['TYPE_TZ'] : '';

	//Путь до pdf файла
	$pathToPdfFile = null;
	if (!empty($tzId) && $resCheckNumP['DATE_ACT'] && !empty($resCheckNumP['NUM_P']) && !empty($date_protocol) && is_numeric($resCheckNumP['NUM_P'])) {
		$pathToPdfFile = './protocol_generator/archive/' . $tzId . date('Y', strtotime($resCheckNumP['DATE_ACT'])) . '/Протокол №' . $resCheckNumP['NUM_P'] . ' от ' . date('d.m.Y', strtotime($date_protocol)) . '.pdf';
	}

	//Путь до sig файла
	$pathToSigFile = null;
	//Путь до forsign.docx файла
	$pathToForsignFile = null;
	if (!empty($tzId) && $resCheckNumP['DATE_ACT'] && !empty($resCheckNumP['NUM_P']) && is_numeric($resCheckNumP['NUM_P'])) {
		
		$pathToSigFile = './protocol_generator/archive/' . $tzId . date('Y', strtotime($resCheckNumP['DATE_ACT'])) . '/' . $resCheckNumP['NUM_P'] . '.sig'; 

		$pathToForsignFile = './protocol_generator/archive/' . $tzId . date('Y', strtotime($resCheckNumP['DATE_ACT'])) . '/forsign.docx'; 
	}

	//Если протокол выдан вне ЛИС , то удаляем файлы sig и pdf и forsign.docx
	if (!empty($_POST['NO_BITRIX'])) {
		
		//Удаляем файл sig 
		unlink($pathToSigFile);
		
		//Удаляем файл forsign.docx 
		unlink($pathToForsignFile); 
		
		//Удаляем файл pdf 
		unlink($pathToPdfFile); 	
	} else {
		//Если тип протокола НЕ упрощенный, проверяем тип протокола который был раньше.Если тип заявки был раньше до сохранения результатов испытаний упрощенный, то удаляем файл sig и forsign.docx
		if (isset($TYPE_TZ) && !in_array($TYPE_TZ, [33, 34, 35, 36, 37, 38, 39]) && in_array($typeTzOld, [33, 34, 35, 36, 37, 38, 39])) {
			
			//Удаляем файл sig
			unlinkFile($pathToSigFile);
			
			//Удаляем файл forsign.docx
			unlinkFile($pathToForsignFile);

		}

		//При смене типа протокола удаляем pdf файл
		if (isset($typeTzOld) && isset($TYPE_TZ) && $TYPE_TZ != $typeTzOld) {

			//Удаляем файл pdf
			unlinkFile($pathToPdfFile);
		}

	}

	$res_act = $DB->Query("SELECT `ID` FROM `ba_tz` WHERE `NUM_P`='" . $NUM_P."' && `ID`!= '" . $_POST['ID']."'")->Fetch(); 	

	// $norm_group = array('80'=>'0', '40'=>'0', '31.5'=>'0', '22.4'=>'0', '20'=>'0', '16'=>'0', '15'=>'0', '11.2'=>'0', '10'=>'0', '8'=>'0', '5'=>'0', 
	// '4'=>'0', '2.5'=>'0', '2'=>'0', '1.25'=>'0', '1'=>'0', '0.63'=>'0', '0.5'=>'0', '0.315'=>'0', '0.25'=>'0', '0.16'=>'0', '0.125'=>'0', '0.1'=>'0', '0.071'=>'0', '0.063'=>'0', '0.05'=>'0', '<0.05'=>'0');
	
	$fl_num_p=0;
	
	$currentDate = date("Y-m-d");
	$dateCorr = $fl_nobitrix ? $currentDate : $_POST['DATE_P_CORR'];
/*--------------------'Протокол с обьединением ячеек' начало--------------------*/
    //Количество пустых строчек в шаблоне
    //Если чекбокс отмечен присмаиваем 1
    $INSERT_STR = 0;
    if (!empty($_POST['INSERT_STR'])) {
        $INSERT_STR = 1;
    }
    $SUM_STR = !empty($_POST['SUM_STR']) ? $_POST['SUM_STR'] : 0;
/*--------------------'Протокол с обьединением ячеек' окончание--------------------*/
	
//	if($res_act&&$NUM_P) { echo "<script>alert(\"Такой номер протокола уже используется!.\");</script>";	$fl_num_p=1;}
//-------------'Сохранение истории' начало-----------------//
    //Если значения не равы, была изменинена темпиратура
    if (isset($temp_o) && isset($_POST['TEMP_O']) && $temp_o != $_POST['TEMP_O'] || isset($temp_to_o) && isset($_POST['TEMP_TO_O']) && $temp_to_o != $_POST['TEMP_TO_O']) {
        $str_type .= 'Изменили значение темпиратуры. ';
    }

    //Если значения не равы, была изменинена влажность
    if (isset($vlag_o) && isset($_POST['VLAG_O']) && $vlag_o != $_POST['VLAG_O'] || isset($vlag_to_o) && isset($_POST['VLAG_TO_O']) && $vlag_to_o != $_POST['VLAG_TO_O']) {
        $str_type .= 'Изменили значение влажности. ';
    }

    //Если значения не равы, была изменинена дата начала испытаний
    if (isset($date_begin) && isset($_POST['DATE_BEGIN']) && $date_begin != $_POST['DATE_BEGIN']) {
        $str_type .= 'Изменили дату начала испытаний. ';
    }

    //Если значения не равы, была изменинена дата окончания испытаний
    if (isset($date_end) && isset($_POST['DATE_END']) && $date_end != $_POST['DATE_END']) {
        $str_type .= 'Изменили дату окончания испытаний. ';
    }
  /*  echo '<pre>';var_dump('$num_p',$num_p);
    echo '<pre>';var_dump('$_POST[\'NUM_P\']',$_POST['NUM_P']);
    echo '<pre>';var_dump('$date_p_core',$date_p_core);
    echo '<pre>';var_dump('$_POST[\'DATE_P_CORR\']',$_POST['DATE_P_CORR']);
    echo '<pre>';var_dump('$no_bitrix',$no_bitrix);
    echo '<pre>';var_dump('$NO_BITRIX',$NO_BITRIX);
    echo '<pre>';var_dump('isset($no_bitrix)',isset($no_bitrix));
    echo '<pre>';var_dump('isset($NO_BITRIX)',isset($NO_BITRIX));
    echo '<pre>';var_dump('$no_bitrix != $NO_BITRIX',$no_bitrix != $NO_BITRIX);die;*/
    //Если значения не равы, был изменинен номер протокола
    if (isset($_POST['NUM_P']) && $num_p != $_POST['NUM_P']) {
        $str_type .= 'Изменили номер протокола. ';
    }

    //Если значения не равы, была изменинена дата протокола
    if (isset($_POST['DATE_P_CORR']) && $date_p_core != $_POST['DATE_P_CORR']) {
        $str_type .= 'Изменили дату протокола. ';
    }

    //Если значения не равы, был изменинен чекбокс "Протокол выдается вне ЛИС"
    if (isset($NO_BITRIX) && $no_bitrix != $NO_BITRIX) {
        $str_type .= 'Изменили протокол выдается вне ЛИС. ';
    }

    //-------------'Сохранение истории' окончание-----------------//																						  
    $TEMP_O = $_POST['TEMP_O'];
    $VLAG_O = $_POST['VLAG_O'];
	$TEMP_TO_O = $_POST['TEMP_TO_O'];
    $VLAG_TO_O = $_POST['VLAG_TO_O'];
	$assigned = CUser::GetByID($USER->GetID())->arResult[0];
	$assigned_user = $assigned['NAME'].' '.$assigned['LAST_NAME'];
	$ID_P = $_POST['NUM_P'] ? $_POST['NUM_P'] : (int)$_GET['ID']+485;

    //-------------'Сохранение истории' начало-----------------//
    $deal_history = CCrmDeal::GetByID($_GET['ID']); //Берём значение сделки
    //Номер заявки
    $num_request = !empty($deal_history["TITLE"]) ? $deal_history["TITLE"] : '';

    //$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . date('Y-m-d H:i:s') . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$_GET['ID'] . "','" . $USER->GetID() . "', '" . $str_type . "', '" . $num_request . "')");

    //$dogovor_num = ($ba_tz['DOGOVOR_NUM'] ? $ba_tz['DOGOVOR_NUM'] : 'Договор №'.$ID);
    //$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`) VALUES ('" . date('Y-m-d H:i:s') . "','" . $assigned_user . "','" . $ID_P . "','" . $_GET['ID'] . "','" . $USER->GetID() . "', 'Сохранение результатов испытаний')");
//-------------'Сохранение истории' окончание-----------------//
   // if (!$fl_num_p)
	//$res = $DB->Query("UPDATE `ba_tz` SET `RESULTS`='" . $results . "', `DATE_RESULTS`='" . date("Y-m-d") . "', `ostatki`='" . $ostatki . "',`OBSHEE`='" . $OBSHEE . "',`NO_COMPLIANCE`='" . $NO_COMPLIANCE . "', `SELECTION_TYPE`='" . $SELECTION_TYPE . "',`TYPE_TZ`='" . $TYPE_TZ . "',`TEMP_O`='" . $TEMP_O . "',`VLAG_O`='" . $VLAG_O . "',`TEMP`='" . $temp . "',`VLAG`='" . $vlag . "',`LAB`='" . $lab . "',`DATE1`='" . $date1 . "',`DATE2`='" . $date2 . "',`MATCH_MANUAL`='" . $match . "',`NORM_DESC`='" . $norm_descript . "',`SRED`='" . $sred . "', `DOPINFO`='" . $DOPINFO . "', `DATE_P_CORR`='" . $_POST['DATE_P_CORR'] . "', `CHECK_NORM`=" . (int)$check_norm . ",`ACTUAL_FOR_OBJECT`='" . $ACTUAL_FOR_OBJECT . "',`GROUP_MAT`='" . $_POST['group_mat'] . "',`SOSTAV`='" . $sostav . "', `NO_BITRIX`='" . $NO_BITRIX . "', `VERIFY` = '".$verify."', `ATTESTAT` = ".$ATTESTAT.", `NO_CONTRACT` = ".$NO_CONTRACT." WHERE ID = " . (int)$_POST['ID']);
	if (!$fl_num_p) 
	$res = $DB->Query("UPDATE `ba_tz` SET `RESULTS`='" . $results_s . "',
		`ostatki`='" . $ostatki . "',`OBSHEE`='" . $OBSHEE . "',
		`DATE_RESULTS`='" . date("Y-m-d") . "',
		`NO_COMPLIANCE`='" . $NO_COMPLIANCE . "', `SELECTION_TYPE`='" . $SELECTION_TYPE . "',
		`TYPE_TZ`='" . $TYPE_TZ . "',`TEMP_O`='" . $TEMP_O . "',
		`TEMP_TO_O`='" . $TEMP_TO_O . "', `VLAG_O`='" . $VLAG_O . "',
		`VLAG_TO_O`='" . $VLAG_TO_O . "', `TEMP`='" . $temp . "',`VLAG`='" . $vlag . "',
		`LAB`='" . $lab . "',`DATE1`='" . $date1 . "',`DATE2`='" . $date2 . "',
		`MATCH_MANUAL`='" . $match2 . "',`NORM_DESC`='" . $norm_descript . "',
		`SRED`='" . $sred . "', `DOPINFO`='" . $DOPINFO . "',
		`DATE_P_CORR`='" . $dateCorr  . "', `CHECK_NORM`=" . (int)$check_norm . ",
		`ACTUAL_FOR_OBJECT`='" . $ACTUAL_FOR_OBJECT . "',
		`GROUP_MAT`='" . $_POST['group_mat'] . "',`SOSTAV`='" . $sostav . "',
		`NO_BITRIX`='" . $NO_BITRIX . "', `VERIFY` = '".$verify."', `NO_CONTRACT` = ".$NO_CONTRACT.",
		`INSERT_STR` = ".$INSERT_STR.", `SUM_STR` = ".$SUM_STR.", `ATTESTAT_IN_PROTOCOL`= ".$ATTESTAT_IN_PROTOCOL." WHERE ID = " . (int)$_POST['ID']);
	//----------------------------- 'Морозостойкость' правил начало -------------------------//
	//$tz_id = !empty() ? : ;
	//$resultFrost = $DB->Query("SELECT * FROM `b_frost` WHERE `ID_Z`!=".$_GET['ID'])->Fetch();
	
	//----------------------------- 'Морозостойкость' правил окончание ----------------------//
	
    //---------'Изменение номера ТЗ' начало ------//
    $ba_tz_select = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID'])->Fetch();
    $ID_P = !empty($ba_tz_select['NUM_P']) ? $ba_tz_select['NUM_P'] : $_POST['NUM_P'];
    $tz = !empty($ba_tz_select["ID"]) ? $ba_tz_select["ID"] : '';
    
    $res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . date('Y-m-d H:i:s') . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$tz . "','" . $USER->GetID() . "', '" . $str_type . "', '" . $num_request . "')");

    /*----------------------'Серверная обработка Журнала заявок' начало---------------------*/
    $get_id = !empty($_GET['ID']) ? (int)$_GET['ID'] : '';

if ($ba_tz_select['ID_Z'])
{
    $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$tz . " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
    $history_ass=$history['ASSIGNED'];
    $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
    $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
    if($history_date<$history_date_null) $history_date='';

    //Колонка "последнее изменение"
    $assignet_history = $history_ass . " " . $history_date;

    $update_assignet_ba_tz = $DB->Query("UPDATE `ba_tz` SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . $get_id);

}

/*----------------------'Серверная обработка Журнала заявок' окончание---------------------*/
    /*---------'Изменение номера ТЗ' окончание ------*/
    $deal2 = CCrmDeal::GetByID($_GET['ID']);

    if (CModule::IncludeModule('crm')&&$deal2['STAGE_ID']!='2'&&$deal2['STAGE_ID']!='3'&&$deal2['STAGE_ID']!='4'&&$deal2['STAGE_ID']!='WON'&&$deal2['TYPE_ID']!='COMPLEX') {
        $deal = new CCrmDeal;
		$num_p = (int)$_GET['ID']+485;
        $arDealUpdate = array('UF_CRM_1554719008992' => $_SERVER["SERVER_NAME"] . "/results_isp.php?ID=" . (int)$_GET['ID'], 'STAGE_ID' => '1', 'UF_CRM_1570518000868' => ($_POST['NUM_P']) ? $_POST['NUM_P']."/19" : $num_p."/19");
        $resUpd = $deal->Update((int)$_GET['ID'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );

		//--'Правки по вводу данных в журнал заявок' начало--//
        //----------------------'Серверная обработка Журнала заявок' начало---------------------//
        //$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос.
        //Обновляем стадию изменения в ba_tz
        $update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('1') . "', `STAGE_NUMBER`= " . (int)2 . " WHERE `ID_Z`=" . $get_id);
        //----------------------'Серверная обработка Журнала заявок' окончание---------------------//
		//--'Правки по вводу данных в журнал заявок' окончание--//
    }
}

/*--Диапозон фактического значения начало--*/
$arrayOutRange = [];
/*--Диапозон фактического значения окончание--*/

if (!empty($_GET['ID'])&&!$fl_num) {
	$grup_mat = ""; //Группа исследуемого материала
	$no_oa=0; //Признак того, что все рез-ты в области
    $deal = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), array('ID' => $_GET['ID']), array("UF_CRM_1554700718621"))->Fetch();
    preg_match('/[a-z\.\/\?_A-Z-]*=([0-9]*)/', $deal['UF_CRM_1554700718621'], $output_array);

  	$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
	$sql = $res->Fetch();
	//----------------------- 'Морозостойкость' правил начало --------------------------------//
	$resFrost = $DB->Query("SELECT * FROM `b_frost` WHERE `tz_id`=" . (int)$sql['ID']);
	$sqlFrost = $resFrost->Fetch();
	//----------------------- 'Морозостойкость' правил окончание ------------------------------//
	$deal2 = CCrmDeal::GetByID($_GET['ID']);
	$sostav = unserialize($sql['SOSTAV']);	

    if (CModule::IncludeModule('crm')&&$deal2['STAGE_ID']!='2'&&$deal2['STAGE_ID']!='3'&&$deal2['STAGE_ID']!='4'&&$deal2['STAGE_ID']!='WON'&&$deal2['TYPE_ID']!='COMPLEX') {
        $deal = new CCrmDeal;
        $arDealUpdate = array('STAGE_ID' => '1');
        $resUpd = $deal->Update((int)$_GET['ID'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    //----------------------'Серверная обработка Журнала заявок' начало---------------------//
        //$DB->ForSql() - Подготавливает строку (заменяет кавычки и прочее) для вставки в SQL запрос.
        //Обновляем стадию изменения в ba_tz
        $update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('1') . "' WHERE `ID_Z`=" . $_GET['ID']);
        //----------------------'Серверная обработка Журнала заявок' окончание---------------------//
    }
    ?> <form method="post" id="result" enctype="multipart/form-data">
	<!--------------------------------------- 'Общее меню' -------------------------------------->
	<div>
		<div class='return disable ' title='Вернуться назад'></div>
		<div class='edit disable' title='Редактировать'></div>
		<a href="/requests_test.php">
			<div class='list' title='Вернуться к списку'></div><!--Список заявок-->
		</a>
		<a href="/request_card.php?ID=<?= $deal2['ID'] ?>">
			<div class='card' title='Карточка заявки'></div><!--Карточка заявки-->
		</a>
		<!---------------------------- 'Скачать протокол' правил начало -------------------------->
		<?php 
			$resDownload=$DB->Query("SELECT `PDF`, `OBSHEE` FROM `ba_tz` WHERE `ID_Z`=".(int)$_GET['ID'])->Fetch();
		?>
		<div id="protDownload">
			<a href="/protocol_generator/protocol.php?ID=<?= !empty($_GET['ID']) ? $_GET['ID'] : ''?>&
			TZ_ID=<?= !empty($sql['ID']) ? $sql['ID'] : '' ?>" style="pointer-events:
			<?= (empty($resDownload['PDF']) && !empty($resDownload['OBSHEE']) && 
			$resDownload['OBSHEE']) ? 'auto' : 'none' ?>">
				<div class="download <?= (empty($resDownload['PDF']) && !$resDownload['PDF'] 
				&& !empty($resDownload['OBSHEE']) && $resDownload['OBSHEE']) ? '' : 'disable' ?>" 
				title="Скачать протокол"></div>
			</a>
		</div>
		<!---------------------------- 'Скачать протокол' правил окончание ------------------------>
		<div class='copy disable' title='Скопировать'></div>
		<div class='remove disable' title='Удалить'></div>
		<div class='add disable' title='Добавить'></div>
		<div class='report disable' title='Отчёт'></div>
		<div class='further disable' title='Далее'></div>
	</div>
	<br/>	
	<?php 
	//	if (in_array('1',$USER->GetUserGroupArray()))  {echo  " | <a href='//172.16.255.2/crm/deal/details/". $_GET['ID'] . "/'>Сделка</a>"; } else {};
		//var_dump($_POST['norm_description']);
		$status='';
		if (($deal2['STAGE_ID']=='2'||$deal2['STAGE_ID']=='3'||$deal2['STAGE_ID']=='4'||$deal2['STAGE_ID']=='WON'))
		{ $status = 'Выдан';}
		elseif($deal2['TYPE_ID']=='COMPLEX')
		{$status = 'OCK';}
		else
		{$status = 'Не выдан';}
	 ?>  Статус: <label><?= $status ?></label><input id="status" type="hidden" value="<?= $status ?>"> | Архивный <input type="checkbox" name="ACTUAL_FOR_OBJECT" <?= ($sql['ACTUAL_FOR_OBJECT']) ? 'checked' : '' ?>>
	<br/><br/>
   
               <table id="results_isp2" style="border: 1px solid #ccc" valign="top">
		<thead>
            <th>Материал </th>
            <th>Опред. харак-ки</th>
            <th>Ед. изм.</th>
            <th>ТУ</th>
            <th>Нормат. значение</th>
            <th>Методика испытаний</th>
			<?= (!1) ? '<th>Температура</th>' : '' ?>
           <?= (!1) ? '<th>Влажность</th>' : '' ?>
            <!--<th>Дата начала</th>
            <th>Дата окончания</th>
           <th>Лаборатория</th>-->
            <th>Фактическое значение</th>
            <th>Среднее значение</th>
            <th>Соответствие треб.</th>
			<th>В ОА?</th>
			<th>Диапазон из ОА</th>
           </thead> 
            <?php
            $arr = unserialize($sql['TZ']);
            $results = unserialize($sql['RESULTS']);
			$ostatki = unserialize($sql['ostatki']);
            $match = unserialize($sql['MATCH_MANUAL']);
            $temp = unserialize($sql['TEMP']);
            $vlag = unserialize($sql['VLAG']);
            $date1 = $sql['DATE1'];
            $date2 = $sql['DATE2'];
			$dopinfo = $sql['DOPINFO'];
            $lab = unserialize($sql['LAB']);
            $probe = unserialize($sql['PROBE']);
			$norm_d = unserialize($sql['NORM_DESC']);
			$verify_arr = unserialize($sql['VERIFY']);
			$sr = unserialize($sql['SRED']);					   
			
			//----------------------- 'Морозостойкость' правил начало --------------------------------//
			$control_damage = $sqlFrost['control_damage'];
            $main_damage = $sqlFrost['main_damage'];
			$control_mass = $sqlFrost['control_mass'];
            //$main_mass = $sqlFrost['main_mass'];
			$main_mass = number_format($sqlFrost['main_mass'],2);
            $control_strength = unserialize($sqlFrost['control_strength']);
            $main_strength = unserialize($sqlFrost['main_strength']);
            //$control_medium = $sqlFrost['control_medium'];
			$control_medium = number_format($sqlFrost['control_medium'],1);
            //$main_medium = $sqlFrost['main_medium'];
			$main_medium = number_format($sqlFrost['main_medium'],1);
			//$control_bottom_line = $sqlFrost['control_bottom_line'];
			$control_bottom_line = number_format($sqlFrost['control_bottom_line'],2);
			//$main_bottom_line = $sqlFrost['main_bottom_line'];
			$main_bottom_line = number_format($sqlFrost['main_bottom_line'],2);
			$ratio = $sqlFrost['ratio'];
			
			//----------------------- 'Морозостойкость' правил окончание ------------------------------//
/*--------------------'Протокол с обьединением ячеек' начало--------------------*/
            //Количество пустых строчек в шаблоне
            $insert_str = !empty($sql['INSERT_STR']) ? $sql['INSERT_STR'] : '';
            $sum_str = !empty($sql['SUM_STR']) ? $sql['SUM_STR'] : '';
    /*--------------------'Протокол с обьединением ячеек' окончание--------------------*/
			
            $data = [];
            foreach ($arr as $arrDatum) {
                $data[] = $arrDatum['mater'];
            }
            $a = array_count_values($data);
            foreach ($arr as $key => $arrDatum) {
                $i = $a[$arrDatum['mater']];
			  foreach($probe[$key]['number_probe'] as $key_probe => $probe_k) {
				$key_probe = $key_probe+1;	
				$j=0;
				foreach ($arrDatum['gost'] as $k => $item) {
				$res_otbor = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
				if($res_otbor['GOST_TYPE']!='metodic_otbor'){
                ?>
                <tr valign="top">
                    <td style="font-weight:bold">
                        
                       <?= $probe_k." ".$probe[$key]['sh_number'][$key_probe-1]  ?>
                    </td>
                    <td>
                        <?php 
                             $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                             echo $res['SPECIFICATION'];
                         ?>
                    </td>
                    <td>
                        <?php 
							$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                            echo $res['ED'].($res['ED_INDEX']?$res['ED_INDEX']:'');
							//echo $item;
                         ?>
                    <td>
                        <?php 
						//var_dump($arrDatum);
						// foreach ($arrDatum['gost_new'] as $k => $item) {
                             //echo $j;
							 $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $arrDatum['gost_new'][$j])->Fetch();
                             echo $res['GOST'].' '.$res['SPECIFICATION'];
                        //} ?>
                    </td>
                    <td> 
                        <?php
                        
                        
							$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $arrDatum['gost_new'][$j])->Fetch();
							$norm_dop = unserialize($res['NORM_DOP']);
							$val_dop = unserialize($res['VALUE_DOP']);
							//dump($norm_dop);
							if($res['GOST_TYPE']=='TU_group') {
								$gr_mat = explode('-', $sql['GROUP_MAT']);
								//dump($gr_mat);
								$r=$gr_mat[1];
								// for($i=0; $i<count($norm_dop); $i++)
								// {
									// if ($results[0][$k][1][0]>$norm_dop[$i][0]&&$results[0][$k][1][0]<$norm_dop[$i][1])
										// $r=$i;
								// }
								$norm_descript= 'Oт '.$norm_dop[$r][0].' до '.$norm_dop[$r][1];
								echo $norm_descript;
								echo '<input type="hidden" name="norm_description[' . $k . ']" value = "'.$norm_descript.'">';
								$grup_mat=$val_dop[$r];  //если ГОСТ, где есть группы материала - указываем группу
							//echo $grup_mat;	
							}
							 else {
							$key_m=(int)$key-1;
							//echo "---".count($arr)."---";
							if($res['GOST_TYPE']=='TU_research') {
								
								 $norm_descript='';
								
								if (count($arr)>1) echo '<input class="res" type="text" name="norm_description['. $key_m .']['.$key_probe.'][' . $k . ']" value = "'.$norm_d[$key_m][$key_probe][$k].'">';
								else echo '<input class="res" type="text" name="norm_description['.$key_probe.'][' . $k . ']" value = "'.$norm_d[$key_probe][$k].'">';
								
							}
							else {
							echo $res['NORM_COMMENT']; 
							if (count($arr)>1) echo '<input type="hidden" name="norm_description['. $key_m .']['.$key_probe.'][' . $k . ']" value = "'.$res['NORM_COMMENT'].'">';
							else echo '<input type="hidden" name="norm_description['.$key_probe.'][' . $k . ']" value = "'.$res['NORM_COMMENT'].'">'; 
								 }
							 }
							
                        
                        ?>
                    </td>
                    <td>
                        <?php
                        
                            $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();

                            echo "<a href='/obl_acc.php?ID=".$res['ID']."' target='_blank'>".($res['GOST'] ? $res['GOST'].' '.$res['GOST_PUNKT'] : '-')."</a>";
                        //} ?>
                    </td>
                   
                    <td>
					
                        <?php
						//var_dump($arrDatum['gost']);
                        
                            $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                            $norm_text = $res['RES_TEXT'];
							
							/*--Диапозон фактического значения начало--*/
							$gost_n1 = $res['NORM_TEXT'] ? $res['NORM1_TEXT'] : $res['NORM1'];
							$gost_n2 = $res['NORM_TEXT'] ? $res['NORM2_TEXT'] : $res['NORM2'];
							$gost_in_out = $res['IN_OUT'];
							
							$actualValue  = !empty($results[$key - 1][$k][$key_probe][0]) ? str_replace(',', '.', $results[$key - 1][$k][$key_probe][0]) : 0;
							
							$outRange = '';
							
							//Если лаборатория ЛФХИ
							//if (!empty($res['LFHI']) && is_numeric($actualValue)) {
							if (!empty($res['LFHI']) && is_numeric($actualValue) && !empty($actualValue)) {
							//if (!empty(1) && is_numeric($actualValue)) {
								//$gost_in_out==1 , то от $gost_n1 и до $gost_n2
								if($gost_in_out==1) 
								//if($gost_in_out!=1) 
								{
									
									if($actualValue < $gost_n1) { //менее
										$outRange = 'менее ' . $gost_n1;
									} elseif($actualValue > $gost_n2) { //Более
										$outRange = 'болee ' . $gost_n2;
									}
								} else {
									
									if($actualValue > $gost_n1) { //Более
										$outRange = 'более ' . $gost_n1;
									} elseif($actualValue < $gost_n2) { //менее
										$outRange = 'менее ' . $gost_n2;
									}
								}
							}
							
							if (!empty($outRange)) {
								$arrayOutRange[] = $outRange;
							}
							
							//echo $res['GOST_TYPE'];
                            //echo ($k > 0) ? "<hr/>" : '';
                         //   for ($i = 1; $i <= count($probe[$key]['number_probe']); $i++) {

                            
							
								//if ($i > 1) echo "<br/>";
								
                                ?>
                             
                                    
										   
                                   <div style="display:flex; padding-left:5px"> 
								    <?php 
								   //Если фактическое значение вне диапазона или не число, то показываем текстовое поле
								   if (!$outRange && is_numeric($actualValue)) {
								   ?>
								   <input class="res" <?= ($norm_text) ? 'type="text"' : 'type="number" step="0.0001"' ?> 
                                            name="results[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][0]"
                                            value="<?= $results[$key - 1][$k][$key_probe][0] ?>">
									<?php } else {
										$actualValue  = !empty($actualValue) ? str_replace('.', ',', $actualValue) : 0;
										$outRange  = !empty($outRange) ? str_replace('.', ',', $outRange) : 0;
										?>	
										<input class="res" type="text"
                                            name="results[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][0]"
                                            value="<?= $outRange ? $outRange : $actualValue ?>">
									<?php }?>	
									<!--Диапозон фактического значения окончание-->
											<input style="margin-left:5px" type="button" class="add" value="+" data-keys="<?= $key - 1 ?>-<?= $k ?>-<?= $key_probe ?>"></div>
                                    <? foreach ($results[$key - 1][$k][$key_probe] as $kk => $res): ?>
                                        <? if ($kk === 0) continue; ?>
										   <div style="display:flex">
                                        <input class="res" <?= ($norm_text) ? 'type="text"' : 'type="number" step="0.0001"' ?> style="margin-left:5px"
                                                name="results[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>][<?= $kk ?>]"
                                                value="<?= $res ?>"><input style="margin-left:5px" type="button" value="-" onclick="$(this).parent().remove();"></div>
                                    <? endforeach; ?>
                                
							<?php  
							
                         //   }
							
                         ?>
                    </td>
                    <td>
                        <?php
						
                        
                        
                            $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                            $gost_type = $res['GOST_TYPE'];
							$accuracy = $res['ACCURACY'];
							$sred_null = "-";
							if($gost_type=="TU_sred5"||$gost_type=="TU_sred4"||$gost_type=="TU_sred3"||$gost_type=="TU_sred2"||$gost_type=="TU_sred")
								{
									echo '<label>'. (is_nan($sr[$key-1][$k][$key_probe]) ? $sred_null : number_format($sr[$key-1][$k][$key_probe], $accuracy, ',', '')) .'</label>';
								}
								
								else echo '<label>Не расcчитывается</label>';
                               
						
						?>
                    </td>
                    <td>
                        <?php
                      
                        
                             $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $arrDatum['gost_new'][$j])->Fetch();
							 $res2 = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
						//	 for ($i = 1; $i <= count($probe[$key]['number_probe']); $i++) {
								 if(!$res2['NORM_TEXT']&&$sr[$key-1][$k][$key_probe])
								 {
									if(($sr[$key-1][$k][$key_probe]<$res2['NORM1']||$sr[$key-1][$k][$key_probe]>$res2['NORM2'])&&($res2['IN_OUT']))
									{
										echo "Внимание! Значение вне области!";
										if($res2['IN_OA']) $no_oa=1; //если ГОСТ в ОА и значение не в ОА, то протокол д.б. без аттестата ОА
											
									}
								    elseif (($sr[$key-1][$k][$key_probe]>$res2['NORM1']||$sr[$key-1][$k][$key_probe]<$res2['NORM2'])&&(!$res2['IN_OUT']))
									 {
										echo "Внимание! Значение вне области!";
										if($res2['IN_OA']) $no_oa=1;
									 }
								 }
								 
						//	 }
					//	dump($match);
							if($res) 
							{
								if ($res['MATCH_MANUAL']) 
								{
								
										?>
										<label>
											<select class="res" name="match[<?= $key - 1 ?>][<?= $k ?>][<?= $key_probe ?>]">
												<option value="0" <?= ($match[$key - 1][$k][$key_probe] == 0) ? 'selected' : '' ?>>
													Не соответствует
												</option>
												<option value="1" <?= ($match[$key - 1][$k][$key_probe] == 1) ? 'selected' : '' ?>>
													Соответствует
												</option>
												<option value="2" <?= ($match[$key - 1][$k][$key_probe] == 2) ? 'selected' : '' ?>>
													-
												</option>
												 <option value="3" <?= ($match[$key - 1][$k][$key_probe] == 3) ? 'selected' : '' ?>>
													Не нормируется
												</option>
											</select>
										</label>
										<?php
								 //   }
								} 
								else 
								{
									
									switch($match[$key - 1][$k][$key_probe])
									{
										case 0:
											echo "Не соответствует";
											break;
										case 1:
											echo "Cоответствует";
											break;
										case 2:
											echo "-";
											break;
									}
								}
							}
                        
                        ?>
                    </td>
					<td>
                        <?php
						                       
                        
                            $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                           
							if($res['IN_OA'])
								{
									echo "+";
								}
								
								else echo "-";
                               
						
						?>
                    </td>
                   <td>
                        <?php
						
                        
                        
                            $res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
                            $gost_n1 = $res['NORM_TEXT'] ? $res['NORM1_TEXT'] : $res['NORM1'];
							$gost_n2 = $res['NORM_TEXT'] ? $res['NORM2_TEXT'] : $res['NORM2'];
							$gost_in_out = $res['IN_OUT'];
							//$sred_null = "-";
							if($gost_in_out==1)
								{
									echo "<label> от ".($gost_n1 ? $gost_n1  : '-')." до ".($gost_n2 ? $gost_n2  : '-')."</label>";
								}
								
								else echo "<label> до ".($gost_n1 ? $gost_n1  : '-') ." и от ".($gost_n2 ? $gost_n2  : '-')."</label>";
                               
						
						?>
                    </td>
                </tr>
				
                <?php 
				}
				else {$res_otbor2 = $DB->Query("UPDATE `ba_tz` SET OSK_METOD_OTBOR_ID = ".$res_otbor['ID']);}
				$j++;
				}
				?><tr style="height:10px; background-color:#3bc8f557"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><?php
				
			}
			}
            ///////////////////////////////////////////////////////////////////////////////////////
            ?>
        </table>
        <input type="hidden" name="ID" value="<?= $sql['ID']//(int)$output_array[1] ?>">
		<input type="hidden" name="NO_OA" value="<?= $no_oa ?>">
		</br> 
		<table width="800" id="results_isp2" >
		<tr><td width="250" height="35">
		ТИП ПРОТОКОЛА </td><td width="500">
		<select name="sredType" id="sredType" onChange="Selected(this)">
		  <option <?= ($sql['TYPE_TZ']==0) ? 'selected' : '' ?> value='simple'>Стандартный</option>
		  <option <?= ($sql['TYPE_TZ']==1) ? 'selected' : '' ?> value='allvalue'>Средние значения</option>
		  <!--<option <?= ($sql['TYPE_TZ']==2) ? 'selected' : '' ?> value='2max'>Среднее по 2-м наибольшим</option>
		  <option <?= ($sql['TYPE_TZ']==3) ? 'selected' : '' ?> value='3max'>Среднее по 3-м наибольшим</option>
		  <option <?= ($sql['TYPE_TZ']==4) ? 'selected' : '' ?> value='4max'>Среднее по 4-м наибольшим</option>-->
		  <!--'Расположение типов протоколов,(только с зерновым составом)' начало-->
		  <option <?= ($sql['TYPE_TZ']==5) ? 'selected' : '' ?> value='zern'>Зерновой состав песка, нерудных СМ</option>
		  <!-- Зерновой состав, упрощенные протоколы -->
		  <option <?= ($sql['TYPE_TZ'] == 35) ? 'selected' : '' ?> value='zern_short_simple'>Зерновой состав песка, нерудных СМ (упрощенный)</option>
		  <!-- ./Зерновой состав, упрощенные протоколы -->
		   <option <?= ($sql['TYPE_TZ']==21) ? 'selected' : '' ?> value='zern_№2'>Зерновой состав песка, нерудных СМ (только зерновой состав)</option>
		  <option <?= ($sql['TYPE_TZ']==6) ? 'selected' : '' ?> value='grunt'>Зерновой состав грунта</option>
		  <option <?= ($sql['TYPE_TZ']==22) ? 'selected' : '' ?> value='grunt_№2'>Зерновой состав грунта (только зерновой состав)</option>
		  <option <?= ($sql['TYPE_TZ']==7) ? 'selected' : '' ?> value='prirod'>Зерновой состав природного песка</option>
		  <!-- Зерновой состав, упрощенные протоколы -->
		  <option <?= ($sql['TYPE_TZ'] == 36) ? 'selected' : '' ?> value='prirod_short_simple'>Зерновой состав природного песка (упрощенный)</option>
		  <!-- ./Зерновой состав, упрощенные протоколы -->
		   <option <?= ($sql['TYPE_TZ']==23) ? 'selected' : '' ?> value='prirod_№2'>Зерновой состав природного песка (только зерновой состав)</option>
		  <option <?= ($sql['TYPE_TZ']==8) ? 'selected' : '' ?> value='tu_12801'>Зерновой состав ГОСТ 12801</option>
		  <option <?= ($sql['TYPE_TZ']==24) ? 'selected' : '' ?> value='tu_12801_№2'>Зерновой состав ГОСТ 12801 (только зерновой состав)</option>
		  <option <?= ($sql['TYPE_TZ']==32) ? 'selected' : '' ?> value='gost31015'>Зерновой состав ГОСТ 12801 (ГОСТ 31015)</option>
		  <option <?= ($sql['TYPE_TZ']==16) ? 'selected' : '' ?> value='tu_183_2'>Зерновой состав ПНСТ 183</option>
		  <option <?= ($sql['TYPE_TZ']==25) ? 'selected' : '' ?> value='tu_183_2_№2'>Зерновой состав ПНСТ 183 (только зерновой состав)</option>
		  <option <?= ($sql['TYPE_TZ']==9) ? 'selected' : '' ?> value='tu_183'>Зерновой состав ПНСТ 184</option>
		  <option <?= ($sql['TYPE_TZ']==26) ? 'selected' : '' ?> value='tu_183_№2'>Зерновой состав ПНСТ 184 (только зерновой состав)</option>
		  <option <?= ($sql['TYPE_TZ']==19) ? 'selected' : '' ?> value='zern_sheb'>Зерновой состав щебня</option>
		  <option <?= ($sql['TYPE_TZ']==27) ? 'selected' : '' ?> value='zern_sheb_№2'>Зерновой состав щебня (только зерновой состав)</option>
		  <option <?= ($sql['TYPE_TZ']==10) ? 'selected' : '' ?> value='osk1'>ОСК щебень</option>
		  <option <?= ($sql['TYPE_TZ']==11) ? 'selected' : '' ?> value='osk2'>ОСК дороги</option>
		  <option <?= ($sql['TYPE_TZ']==12) ? 'selected' : '' ?> value='osk3'>ОСК ПНСТ 183, 184</option>
		  <option <?= ($sql['TYPE_TZ']==13) ? 'selected' : '' ?> value='osk4'>ОСК песок</option>
		  <option <?= ($sql['TYPE_TZ']==14) ? 'selected' : '' ?> value='osk_sred'>ОСК средние значения</option>
		  <option <?= ($sql['TYPE_TZ']==15) ? 'selected' : '' ?> value='shps'>Зерновой состав ЩПС</option>
		  <option <?= ($sql['TYPE_TZ']==28) ? 'selected' : '' ?> value='shps_№2'>Зерновой состав ЩПС (только зерновой состав)</option>
		  <option <?= ($sql['TYPE_TZ']==17) ? 'selected' : '' ?> value='sheb'>Зерновой состав щебня ОСК</option>
		  <option <?= ($sql['TYPE_TZ']==18) ? 'selected' : '' ?> value='sheb_shlak'>Зерновой состав щебня (ГОСТ 8267)</option>
		  <option <?= ($sql['TYPE_TZ']==29) ? 'selected' : '' ?> value='sheb_shlak_№2'>Зерновой состав щебня (ГОСТ 8267) (только зерновой состав)</option>
		  <option <?= ($sql['TYPE_TZ'] == 39) ? 'selected' : '' ?> value='sheb_shlak_simple'>Зерновой состав щебня (ГОСТ 3344) упрощенный</option>
		  <option <?= ($sql['TYPE_TZ'] == 38) ? 'selected' : '' ?> value='sheb_shlak_№2_simple'>Зерновой состав щебня (ГОСТ 3344) упрощенный (только зерновой состав)</option>
			<option <?= ($sql['TYPE_TZ'] == 20) ? 'selected' : '' ?> value='density_grunt'>ОСК режущее кольцо</option> 																											 
		    <!--'Расположение типов протоколов,(только с зерновым составом)' окончание-->											   
		    <!------------------------------ 'Морозостойкость' правил начало ------------------------->
		   <option <?= ($sql['TYPE_TZ']==30) ? 'selected' : '' ?> value='frost'>Морозостойкость</option>
		   <!------------------------------ 'Морозостойкость' правил окончание ------------------------->
		   
		   <!------------------------------ 'Новый протокол, Ареометрический метод' правил начало ------------------------->
		   <option <?= ($sql['TYPE_TZ']==31) ? 'selected' : '' ?> value='metric_method'>Зерновой состав грунта (ареометрическим метод)</option>
		   <!------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ------------------------->
		   <!------------------------------ 'Сокращенные протоколы в результатах испытаний' начало ------------------------->
		   <option <?= ($sql['TYPE_TZ']==33) ? 'selected' : '' ?> value='shortcut'>Упрощенный</option> 
		   <option <?= ($sql['TYPE_TZ']==34) ? 'selected' : '' ?> value='shortcut_mean'>Упрощенный (средние значения)</option>
		   <!------------------------------ 'Сокращенные протоколы в результатах испытаний' окончание ------------------------->
			<!-- Теплопроводность -->
		   <option <?= ($sql['TYPE_TZ'] == 37) ? 'selected' : '' ?> value='thermal_conductivity'>Теплопроводность</option>
		   <!-- ./Теплопроводность -->
		   <option <?= ($sql['TYPE_TZ'] == 40) ? 'selected' : '' ?> value='new_25607'>ГОСТ 25607 НОВЫЙ</option>
		</select>
		</td></tr>
		
		
		<!--<tr><td>
        Общий протокол </td><td>
            <input onchange="document.getElementById('result').submit(); return false;" type="checkbox"
                   name="OBSHEE" <?= ($sql['OBSHEE']) ? 'checked' : '' ?>>
        </td></tr>-->
		<tr><td width="250" height="35">
		ГРУППА МАТЕРИАЛА </td><td><select class="tz" name="group_mat">
        <option value="0" selected>Выбрать</option>
        <?php
        $select = $DB->Query("SELECT * FROM `MATERIALS`");
		$select_groups = array();
		$gr_mat = explode('-', $sql['GROUP_MAT']);
		
        while ($option = $select->Fetch()) {
			$select_group = $DB->Query("SELECT `GROUPS` FROM `MATERIALS` WHERE `ID`=".$option['ID'])->Fetch();
			$select_groups = unserialize($select_group['GROUPS']);
            echo "<option value='" . $option['ID'] . "' style='font-weight:bold' disabled>" . $option['NAME'] . "</option>";
			foreach ($select_groups as $s => $group)
				
			{  
				$opt_sel='';
				if($gr_mat[0]==$option['ID']&&$gr_mat[1]==$s) $opt_sel='selected';
				if ($group) echo "<option ". $opt_sel ." value='" . $option['ID'] .'-'.$s. "'> -- ". $group  . "</option>";
			}
        }
		
        ?>
    </select></td></tr>
		<tr><td height="35">
		Не оценивать соответствие нормам
		</td><td>
            <input type="checkbox" name="NO_COMPLIANCE" <?= ($sql['NO_COMPLIANCE']) ? 'checked' : '' ?>>
        </td></tr>
		<!------------------------ 'Пробы отобраны не заказчиком' -------------------------->
		<tr>
			<td height="35">
				Пробы отобраны не заказчиком
			</td>
			<td>
				<input type="checkbox" name="SELECTION_TYPE" <?= ($sql['SELECTION_TYPE']) ? 'checked' : '' ?>>
			</td>
		</tr>
            <!---------------------'Протокол с обьединением ячеек' начало---------------------------->
        <tr>
            <td height="35">
                Количество пустых строчек в шаблоне
            </td>
            <td>
                <input type="checkbox" id='insert_str' name="INSERT_STR" <?= ($insert_str) ? 'checked' : '' ?>>
                <input class="tz" id='sum_str' type="number" name="SUM_STR" min='1' step="1" value="<?= ($sum_str) ? $sum_str : '' ?>" <?= ($insert_str) ? '' : 'disabled' ?>>
            </td>
        </tr>
            <!---------------------'Протокол с обьединением ячеек' окончание ---------------------------->
		<tr>
			<td height="35">
				Указать акт ПП в качестве документа-основания
			</td>
			<td>
				<input type="checkbox" name="NO_CONTRACT" <?= ($sql['NO_CONTRACT']) ? 'checked' : '' ?>>
			</td>
		</tr>
		<tr>
			<td height="35">Температура</td>
			<td>
				<!-------'Вывод старых и новых значений в поля Температура и Влажность' начало ----->
				<?php
				$str = (!empty($sql['TEMP_O']) ? $sql['TEMP_O'] : '');
				preg_match_all("/\d+[\.,]?\d*/", $str, $matchesTemp);
				
				$tempO = null;
				$tempToO = null;
				
				if (empty($sql['TEMP_TO_O']) && !empty($matchesTemp[0])) {
					if (count($matchesTemp[0]) == 2) {
						foreach($matchesTemp[0] as $keyMatchesTemp => $valMatchesTemp) {
							if ($keyMatchesTemp == 0) {
								$tempO = str_replace(",",".",$valMatchesTemp);
							}
							
							if ($keyMatchesTemp == 1) {
								$tempToO = str_replace(",",".",$valMatchesTemp);
							}
						}
					}
					
					if (count($matchesTemp[0]) == 1) {
						foreach($matchesTemp[0] as $keyMatchesTemp => $valMatchesTemp) {
							if ($keyMatchesTemp == 0) {
								$tempO = str_replace(",",".",$valMatchesTemp);
								$tempToO = str_replace(",",".",$valMatchesTemp);
							}
						}
					}
				}
				
				if (!empty($sql['TEMP_TO_O'])) {
					$tempToO = $sql['TEMP_TO_O'];
					$tempO = (!empty($matchesTemp[0][0]) ? $matchesTemp[0][0] : '');
					
				}
				
				?>
				
				От <input class="tz" id='quantity' type="number" name="TEMP_O" step="0.1" value="<?= $tempO ?>">
				- до <input class="tz" type="number" name="TEMP_TO_O" step="0.1" value="<?= $tempToO ?>">
				
				<!----'Вывод старых и новых значений в поля Температура и Влажность' окончание ----->
				<!--<input class="tz" type="text" name="TEMP_O" value="<//?= $sql['TEMP_O'] ?>">-->
				<!--От <input class="tz" id='quantity' type="number" name="TEMP_O" step="0.1" value="<//?= $sql['TEMP_O'] ?>">
				- до <input class="tz" type="number" name="TEMP_TO_O" step="0.1" value="<//?= $sql['TEMP_TO_O'] ?>">-->
			</td>
		</tr>
		<tr>
			<td height="35">Влажность</td>
				<td>
					<!-------'Вывод старых и новых значений в поля Температура и Влажность' начало ----->
					<?php
					$str = (!empty($sql['VLAG_O']) ? $sql['VLAG_O'] : '');
					preg_match_all("/\d+[\.,]?\d*/", $str, $matchesTemp);
					
					$vlagO = null;
					$vlagToO = null;
					
					if (empty($sql['VLAG_TO_O']) && !empty($matchesTemp[0])) {
						if (count($matchesTemp[0]) == 2) {
							foreach($matchesTemp[0] as $keyMatchesTemp => $valMatchesTemp) {
								if ($keyMatchesTemp == 0) {
									$vlagO = str_replace(",",".",$valMatchesTemp);
								}
								
								if ($keyMatchesTemp == 1) {
									$vlagToO = str_replace(",",".",$valMatchesTemp);
								}
							}
						}
						
						if (count($matchesTemp[0]) == 1) {
							foreach($matchesTemp[0] as $keyMatchesTemp => $valMatchesTemp) {
								if ($keyMatchesTemp == 0) {
									$vlagO = str_replace(",",".",$valMatchesTemp);
									$vlagToO = str_replace(",",".",$valMatchesTemp);
								}
							}
						}
					}
					
					if (!empty($sql['VLAG_TO_O'])) {
						$vlagToO = $sql['VLAG_TO_O'];
						$vlagO = (!empty($matchesTemp[0][0]) ? $matchesTemp[0][0] : '');
						
					}
					
					?>
					
					От <input class="tz" type="number" name="VLAG_O" step="0.1" value="<?= $vlagO ?>">
					- до <input class="tz" type="number" name="VLAG_TO_O" step="0.1" value="<?= $vlagToO ?>">
					
					<!----'Вывод старых и новых значений в поля Температура и Влажность' окончание ----->
					<!--<input class="tz" type="text" name="VLAG_O" value="<//?= $sql['VLAG_O'] ?>">-->
					<!--От <input class="tz" type="number" name="VLAG_O" step="0.1" value="<//?= $sql['VLAG_O'] ?>">
					- до <input class="tz" type="number" name="VLAG_TO_O" step="0.1" value="<//?= $sql['VLAG_TO_O'] ?>">-->
				</td>
			</tr>
		<tr><td height="35">
        Дата начала испытаний
		</td><td>
        <input type="date" name="DATE_BEGIN" value="<?= $date1 ?>" required>
        </td></tr>
		<tr><td height="35">
        Дата окончания испытаний
		</td><td>
        <input type="date" name="DATE_END" value="<?= $date2 ?>" required>
		</td></tr>
		<tr><td height="35">
        Дополнительная информация
		</td><td>
       <!------------'Правки протоколов' начало------------------>
            <textarea id="dop_info" class="tz" name="DOP_INFO" size="60"><?= $dopinfo ?></textarea>
		
        </td></tr>
		<tr>
			<td height="35">
			Номер протокола
			</td>
			<td id='yourNumber'>
			<input class="tz" type="text" name="NUM_P" 
			value="<?= $sql['NUM_P'] ? $sql['NUM_P'] : 'номер не присвоен' ?>" size="10" 
			required <?= (!$sql['NUM_P'] || $sql['NUM_P']) ? 'readonly': '' ?>>
			</td>
		</tr>
		<tr><td height="35">
        Дата протокола
		</td><td>
        <input type="date" name="DATE_P_CORR" value="<?= $sql['DATE_P_CORR'] ? $sql['DATE_P_CORR'] : date('Y-m-d') ?>" size="10" required readonly>
        </td></tr>
		<tr><td>Добавить оборудование</td><td>
		<select id="first" class="oborud_change" style="max-width: 800px; margin-top:0px">
                            <?php
							
							$i=0;
							
                            $res = $DB->Query("SELECT * FROM `ba_oborud` ORDER BY `OBJECT`");
                            while ($row = $res->Fetch()) {
                              //  if (in_array($row['place_of_installation_or_storage'], $poios))
								  if($row['IDENT'] != "VO"&&$row['IDENT'] != "TS"&&$row['IDENT'] != "REACT") {
								  echo "<option value=" . $row['ID'] . ">" . ($row['TYPE_OBORUD'] ? $row['TYPE_OBORUD'].' ' : '') . "" . $row['OBJECT'] . ", инв. номер " . $row['REG_NUM'] . " </option>";
								  }
                            }
                            ?>
                        </select></td>
		</tr>
		<tr><td>Используемое оборудование</td><td>
		<form method="post">
                            
                                <?php $ids = []; $id = [];
								$res_tz_ob = $DB->Query("SELECT * FROM `TZ_OB_CONNECT` WHERE `ID_TZ` = " . $_GET['ID']);
								//var_dump($res_tz_ob);
								if(intval($res_tz_ob->SelectedRowsCount())>0) {
								
									while($row = $res_tz_ob->Fetch())
									{
										
										$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=".$row['ID_OB'])->Fetch();
										//echo $res['IDENT'];
										if($res['IDENT'] != "VO"&&$res['IDENT'] != "TS"&&$res['IDENT'] != "REACT") {
										$ids[] = ($res['TYPE_OBORUD'] ? $res['TYPE_OBORUD'].' ' : '') . "" . $res['OBJECT'] . ", инв. номер " . $res['REG_NUM'];
										$id[] = $res['ID'];
										}
										
									}
									
								} else {
									
									foreach ($arr as $key => $arrDatum) {
										foreach ($arrDatum['gost'] as $k => $item) {
											
											$res = $DB->Query("SELECT * FROM `ba_connection`,`ba_oborud` WHERE `ba_connection`.`GOST_ID`=" . $item . " AND `ba_oborud`.`ID`=`ba_connection`.`OBORUD_ID` ORDER BY `OBJECT`");
											while ($row = $res->Fetch()) {
												if($row['IDENT'] != "VO"&&$row['IDENT'] != "TS"&&$row['IDENT'] != "REACT") {
												$ids[] = ($row['TYPE_OBORUD'] ? $row['TYPE_OBORUD'].' ' : '') . "" . $row['OBJECT'] . ", инв. номер " . $row['REG_NUM'];
												$id[] = $row['ID'];
												}
											}
										}
									}
								}
								$ids = array_unique($ids); $id = array_unique($id);// var_dump($id);
								$id_final = [];
                                ?>
							<select id="second" multiple style="height: 200px;">
								<?php foreach($ids as $k => $item)
										{

											 echo "<option value=" . $id[$k] . ">" . $item . " </option>";	
											 $id_final[] = $id[$k];
										}
								?>
                            </select>
                            <input type="hidden" id="ids_oborud" name="ids_oborud" value='<?= json_encode($id_final) ?>'>
							
                            Вернуть оборудование по умолчанию <input type="checkbox" name="DEL_OB" >
                            <input type="submit" style="max-width: 400px" value="Сохранить оборудование">
                        </form></td>
		</tr>
		<tr><td>Подпись в протоколе</td><td><select id="verify" class="verify_change" name="verify_s[]" style="max-width: 800px; margin-top:0px" multiple="multiple">
                            <option value="<?= $deal2['ASSIGNED_BY_ID'] ?> " <?= $sql['VERIFY']&&in_array((int)$deal2['ASSIGNED_BY_ID'], $verify_arr) ? 'selected' : '' ?>><?= $deal2['ASSIGNED_BY_NAME'] ?> <?= $deal2['ASSIGNED_BY_LAST_NAME'] ?></option>
							<?php
							
							$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal2['ID']);
							if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
								
								foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
								{
									$assign_user = CUser::GetByID((int)$assign)->Fetch();
									echo "<option value= '".$assign_user['ID']."' ".(in_array($assign_user['ID'], $verify_arr) ? 'selected' : '' )." >".$assign_user['NAME']." ".$assign_user['LAST_NAME']."</option>";
								}
							}
                            // $res = $DB->Query("SELECT * FROM `ba_oborud` ORDER BY `OBJECT`");
                            // while ($row = $res->Fetch()) {
                              // //  if (in_array($row['place_of_installation_or_storage'], $poios))
								  // if($row['IDENT'] != "VO"&&$row['IDENT'] != "TS"&&$row['IDENT'] != "REACT") {
								  // echo "<option value=" . $row['ID'] . ">" . ($row['TYPE_OBORUD'] ? $row['TYPE_OBORUD'].' ' : '') . "" . $row['OBJECT'] . ", инв. номер " . $row['REG_NUM'] . " </option>";
								  // }
                            // }
                            ?>
                        </select></td>
		</tr></td></tr>
		<tr>
			<td height="35">
			Протокол выдается вне ЛИС 
			</td>
			<td>
			<input type="checkbox" name="NO_BITRIX" <?= ($sql['NO_BITRIX'] || $_POST['NO_BITRIX']) ? 'checked' : '' ?>>
			</td>
		</tr>
		<?php  
		if(!$no_oa)
			$DB->Query("UPDATE `ba_tz` SET  `IN_ATTESTAT_DIAPASON` = 1 WHERE ID = " . (int)$sql['ID']);
		else
			$DB->Query("UPDATE `ba_tz` SET  `IN_ATTESTAT_DIAPASON` = 0 WHERE ID = " . (int)$sql['ID']);
		
		$res_att = $DB->Query("SELECT `ATTESTAT`, `IN_ATTESTAT_DIAPASON`, `ATTESTAT_IN_PROTOCOL` FROM `ba_tz` WHERE ID = " . (int)$sql['ID'])->Fetch();
		$check_att = ''; if(!$res_att['ATTESTAT']&&$no_oa) $check_att = 'disabled';
		
		?>
		<tr><td height="35">
        C аттестатом аккредитации
		</td><td><!------------------------'disabled'------------------------------->
        <input type="checkbox" name="NO_ATTESTAT" <?= $res_att['ATTESTAT_IN_PROTOCOL'] ? 'checked' : '' ?> <?= ($res_att['ATTESTAT']&&$res_att['IN_ATTESTAT_DIAPASON']&&(($USER->GetID()=='1')||($USER->GetID()=='10')||($USER->GetID()=='9'))) ? '' : 'disabled' ?>  > 
        </td></tr>
		
        </table>
		<?php
		 // $assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal2['ID']);
		  //dump($_POST['verify_s']);

?>
		<!-------------------------------------------------------------------!!!!!!!!!!!!!!ПЕСОК СТРОИТЕЛЬНЫЙ!!!!!!!!!!------------------------------------------->
			<table id="zern_table" style="display:none;">
			<tr><td> ЗЕРНОВОЙ СОСТАВ</td>
			<tr>
							<td width="262">Остаток на сите 2,5</td>
							<td><input type="number" step="0.01" name="ostatki[0]" value="<?= $ostatki[0] ?>"></td>
					<tr>
							<td>Остаток на сите 1,25</td>
							<td>	<input type="number" step="0.01" name="ostatki[1]" value="<?= $ostatki[1] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,63</td>
							<td>	<input type="number" step="0.01" name="ostatki[2]" value="<?= $ostatki[2] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,315</td>
							<td>	<input type="number" step="0.01" name="ostatki[3]" value="<?= $ostatki[3] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,16</td>
							<td>	<input type="number" step="0.01" name="ostatki[4]" value="<?= $ostatki[4] ?>">
							</td>
							<tr>
							<td>Проход через сито 0,16</td>
							<td>	<input type="number" step="0.01" name="ostatki[5]" value="<?= $ostatki[5] ?>">
							</td>
							<tr>
							<td>Остаток на сите 2,5 полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki[6]" value="<?= $ostatki[6] ?>">
							</td>
							<tr>
							<td>Остаток на сите 1,25 полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki[7]" value="<?= $ostatki[7] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,63 полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki[8]" value="<?= $ostatki[8] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,315 полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki[9]" value="<?= $ostatki[9] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,16 полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki[10]" value="<?= $ostatki[10] ?>">
							</td>
							<tr>
							<td>Проход через сито 0,16 полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki[11]" value="<?= $ostatki[11] ?>">
							</td>
								  </table>
							
		
		<!-------------------------------------------------------------------АСФАЛЬТ. ЗЕРНОВОЙ СОСТАВ МИН. ЧАСТИ. ГОСТ 12801, 9128------------------------------------------->
		
		<table id="zern_table2" style="display:none;">
			<tr><td> ЗЕРНОВОЙ СОСТАВ</td>
							<tr>
							<!--<td width="262">Содержание зерен размером мельче 80 мм</td>
							<td><input type="number" step="0.01" name="ostatki2[10]" value="<?= $ostatki[10] ?>"></td>
							<tr>-->
							<td width="262">Содержание зерен размером мельче 40 мм</td>
							<td><input type="number" step="0.01" name="ostatki2[11]" value="<?= $ostatki[11] ?>"></td>
							<tr>
							<td width="262">Содержание зерен размером мельче 20 мм</td>
							<td><input type="number" step="0.01" name="ostatki2[0]" value="<?= $ostatki[0] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 15 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki2[1]" value="<?= $ostatki[1] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче 10 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki2[2]" value="<?= $ostatki[2] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче 5 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki2[3]" value="<?= $ostatki[3] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче 2,5 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki2[4]" value="<?= $ostatki[4] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче 1,25 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki2[5]" value="<?= $ostatki[5] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче 0,63 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki2[6]" value="<?= $ostatki[6] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче 0,315 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki2[7]" value="<?= $ostatki[7] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче 0,16 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki2[8]" value="<?= $ostatki[8] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче 0,071 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki2[9]" value="<?= $ostatki[9] ?>">
							</td>
							
								  </table>
		
			<!-------------------------------------------------------------------!!!!!!!!!!!!!!ГРУНТ!!!!!!!!!!------------------------------------------->
			<br>
			<table id="zern_table3" style="display:none;">
			<tr><td> ЗЕРНОВОЙ СОСТАВ</td>
			<!------------'Правки протоколов' зерновой состав грунта начало------------------>    
            <tr>
                <td width="262">Фракция грунта 100 мм</td>
                <td><input type="number" step="0.01" name="ostatki3[21]" value="<?= $ostatki[21] ?>"></td>
            <tr>
                <td width="262">Фракция грунта 60 мм</td>
                <td><input type="number" step="0.01" name="ostatki3[20]" value="<?= $ostatki[20] ?>"></td>
            <tr>
                <td width="262">Фракция грунта 40 мм</td>
                <td><input type="number" step="0.01" name="ostatki3[19]" value="<?= $ostatki[19] ?>"></td>
            <tr>
                <td width="262">Фракция грунта 20 мм</td>
                <td><input type="number" step="0.01" name="ostatki3[18]" value="<?= $ostatki[18] ?>"></td>    
            <!------------'Правки протоколов' зерновой состав грунта окончание------------------>  
			<tr>
							<td width="262">Фракция грунта 10 мм</td>
							<td><input type="number" step="0.01" name="ostatki3[0]" value="<?= $ostatki[0] ?>"></td>
							<tr>
							<td>Фракция грунта 5,0 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki3[1]" value="<?= $ostatki[1] ?>">
							</td>
							<tr>
							<td>Фракция грунта 2,0 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki3[2]" value="<?= $ostatki[2] ?>">
							</td>
							<tr>
							<td>Фракция грунта 1,0 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki3[3]" value="<?= $ostatki[3] ?>">
							</td>
							<tr>
							<td>Фракция грунта 0,5 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki3[4]" value="<?= $ostatki[4] ?>">
							</td>
							<tr>
							<td>Фракция грунта 0,25 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki3[5]" value="<?= $ostatki[5] ?>">
							</td>
							<tr>
							<td>Фракция грунта 0,1 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki3[6]" value="<?= $ostatki[6] ?>">
							</td>
							<tr>
							<td>Фракция грунта 0,05 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki3[7]" value="<?= $ostatki[7] ?>">
							</td>
							<tr>
							<td>Фракция грунта менее 0,05 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki3[8]" value="<?= $ostatki[8] ?>">
							</td>
							<tr>
							<!------------'Правки протоколов' зерновой состав грунта начало------------------>    
							<td width="262">Фракция грунта 100 мм</td>
                <td><input type="number" step="0.01" name="ostatki3[25]" value="<?= $ostatki[25] ?>"></td>
            <tr>
                <td width="262">Фракция грунта 60 мм</td>
                <td><input type="number" step="0.01" name="ostatki3[24]" value="<?= $ostatki[24] ?>"></td>
            <tr>
                <td width="262">Фракция грунта 40 мм</td>
                <td><input type="number" step="0.01" name="ostatki3[23]" value="<?= $ostatki[23] ?>"></td>
            <tr>
                <td width="262">Фракция грунта 20 мм</td>
                <td><input type="number" step="0.01" name="ostatki3[22]" value="<?= $ostatki[22] ?>"></td>   
            <tr> 
            <!------------'Правки протоколов' зерновой состав грунта окончание------------------>  
							<td width="262">Фракция грунта 10 мм полн.</td>
							<td><input type="number" step="0.01" name="ostatki3[9]" value="<?= $ostatki[9] ?>"></td>
							<tr>
							<td>Фракция грунта 5,0 мм полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki3[10]" value="<?= $ostatki[10] ?>">
							</td>
							<tr>
							<td>Фракция грунта 2,0 мм полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki3[11]" value="<?= $ostatki[11] ?>">
							</td>
							<tr>
							<td>Фракция грунта 1,0 мм полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki3[12]" value="<?= $ostatki[12] ?>">
							</td>
							<tr>
							<td>Фракция грунта 0,5 мм полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki3[13]" value="<?= $ostatki[13] ?>">
							</td>
							<tr>
							<td>Фракция грунта 0,25 мм полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki3[14]" value="<?= $ostatki[14] ?>">
							</td>
							<tr>
							<td>Фракция грунта 0,1 мм полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki3[15]" value="<?= $ostatki[15] ?>">
							</td>
							<tr>
							<td>Фракция грунта 0,05 мм полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki3[16]" value="<?= $ostatki[16] ?>">
							</td>
							<tr>
							<td>Фракция грунта менее 0,05 мм полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki3[17]" value="<?= $ostatki[17] ?>">
							</td>
							</table>
							
<!-------------------------------------------------------------------!!!!!!!!!!!!!!ПЕСОК ПРИРОДНЫЙ!!!!!!!!!!------------------------------------------->
							
			<table id="zern_table4" style="display:none;">
			<tr><td> ЗЕРНОВОЙ СОСТАВ</td>
			<tr>
							<td width="262">Остаток на сите 2,0</td>
							<td><input type="number" step="0.01" name="ostatki4[0]" value="<?= $ostatki[0] ?>"></td>
					<tr>
							<td>Остаток на сите 1,0</td>
							<td>	<input type="number" step="0.01" name="ostatki4[1]" value="<?= $ostatki[1] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,5</td>
							<td>	<input type="number" step="0.01" name="ostatki4[2]" value="<?= $ostatki[2] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,25</td>
							<td>	<input type="number" step="0.01" name="ostatki4[3]" value="<?= $ostatki[3] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,125</td>
							<td>	<input type="number" step="0.01" name="ostatki4[4]" value="<?= $ostatki[4] ?>">
							</td>
							<tr>
							<td>Кол-во в поддоне</td>
							<td>	<input type="number" step="0.01" name="ostatki4[5]" value="<?= $ostatki[5] ?>">
							</td>
							<tr>
							<td>Остаток на сите 2,0 полн.:</td>
							<td>	<input type="number" step="0.01" name="ostatki4[6]" value="<?= $ostatki[6] ?>">
							</td>
							<tr>
							<td>Остаток на сите 1,0 полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki4[7]" value="<?= $ostatki[7] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,5 полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki4[8]" value="<?= $ostatki[8] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,25 полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki4[9]" value="<?= $ostatki[9] ?>">
							</td>
							<tr>
							<td>Остаток на сите 0,125 полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki4[10]" value="<?= $ostatki[10] ?>">
							</td>
							<tr>
							<td>Кол-во в поддоне полн.</td>
							<td>	<input type="number" step="0.01" name="ostatki4[11]" value="<?= $ostatki[11] ?>">
							</td>
								  </table>
							
		
		
		<!------------------------------------------------------Гранулометрический состав минеральной части смеси ПНСТ 183------------------------------------------->
		
		<table id="zern_table5" style="display:none;">
			<tr><td> ЗЕРНОВОЙ СОСТАВ</td><td>Фактические данные</td><td>Данные заказчика</td>
			<tr>
							<td width="262">Содержание зерен размером мельче 31,5 мм</td>
							<td><input type="number" step="0.01" name="ostatki5[0]" value="<?= $ostatki[0] ?>"></td>
							<td><input type="number" step="0.01" name="sostav[0]" value="<?= $sostav[0] ?>"></td>
					<tr>
							<td>Содержание зерен размером мельче 22,4 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[1]" value="<?= $ostatki[1] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[1]" value="<?= $sostav[1] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 16,0 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[2]" value="<?= $ostatki[2] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[2]" value="<?= $sostav[2] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 11,2 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[3]" value="<?= $ostatki[3] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[3]" value="<?= $sostav[3] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 8,0 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[4]" value="<?= $ostatki[4] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[4]" value="<?= $sostav[4] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 5,6 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[5]" value="<?= $ostatki[5] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[5]" value="<?= $sostav[5] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 4,0 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[6]" value="<?= $ostatki[6] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[6]" value="<?= $sostav[6] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 2,0 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[7]" value="<?= $ostatki[7] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[7]" value="<?= $sostav[7] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 1,0 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[8]" value="<?= $ostatki[8] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[8]" value="<?= $sostav[8] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 0,5 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[9]" value="<?= $ostatki[9] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[9]" value="<?= $sostav[9] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 0,25 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[10]" value="<?= $ostatki[10] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[10]" value="<?= $sostav[10] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 0,125 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[11]" value="<?= $ostatki[11] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[11]" value="<?= $sostav[11] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 0,063 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki5[12]" value="<?= $ostatki[12] ?>">
							</td>
							<td><input type="number" step="0.01" name="sostav[12]" value="<?= $sostav[12] ?>"></td>
								  </table>
								  
								  
		<!-------------------------------------------------------------------!!!!!!!!!!!!!!ЩЕБЕНОЧНО-ПЕСЧАНАЯ СМЕСЬ!!!!!!!!!!------------------------------------------->
							
			<table id="zern_table6" style="display:none;">
						
			<tr><td> ЗЕРНОВОЙ СОСТАВ</td><td>Фактические данные</td><td>Требования ГОСТ 25607</td>
			<tr>
							<td width="262">Полный остаток на сите 120 мм</td>
							<td><input type="number" step="0.01" name="ostatki6[0]" value="<?= $ostatki[0] ?>"></td>
							<tr>
							<td>Полный остаток на сите 80 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[1]" value="<?= $ostatki[1] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 40 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[2]" value="<?= $ostatki[2] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 20 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[3]" value="<?= $ostatki[3] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 10 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[4]" value="<?= $ostatki[4] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 5 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[5]" value="<?= $ostatki[5] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 2,5 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[6]" value="<?= $ostatki[6] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 0,63 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[7]" value="<?= $ostatki[7] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 0,16 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[8]" value="<?= $ostatki[8] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 0,05 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[9]" value="<?= $ostatki[9] ?>">
							</td>
							<tr>
							<td width="262">Частный остаток на сите 120 мм</td>
							<td><input type="number" step="0.01" name="ostatki6[10]" value="<?= $ostatki[10] ?>"></td>
							<tr>
							<td>Частный остаток на сите 80 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[11]" value="<?= $ostatki[11] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 40 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[12]" value="<?= $ostatki[12] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 20 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[13]" value="<?= $ostatki[13] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 10 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[14]" value="<?= $ostatki[14] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 5 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[15]" value="<?= $ostatki[15] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 2,5 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[16]" value="<?= $ostatki[16] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 0,63 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[17]" value="<?= $ostatki[17] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 0,16 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[18]" value="<?= $ostatki[18] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 0,05 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki6[19]" value="<?= $ostatki[19] ?>">
							</td>
							<tr>
							</table>

<!-------------------------------------------------------------------!!!!!!!!!!!!!!ЩЕБЕНОЧНО-ПЕСЧАНАЯ СМЕСЬ ГОСТ 25607 НОВЫЙ!!!!!!!!!!------------------------------------------->
							
			<table id="zern_table9" style="display:none;">
						
			<tr><td> ЗЕРНОВОЙ СОСТАВ</td><td>Фактические данные</td><td>Требования ГОСТ 25607</td>
			<tr>
							<td width="262">Полный остаток на сите 31,5 мм</td>
							<td><input type="number" step="0.01" name="ostatki9[0]" value="<?= $ostatki[0] ?>"></td>
							<tr>
							<td>Полный остаток на сите 22,4 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[1]" value="<?= $ostatki[1] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 16 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[2]" value="<?= $ostatki[2] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 8 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[3]" value="<?= $ostatki[3] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 4 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[4]" value="<?= $ostatki[4] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 2 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[5]" value="<?= $ostatki[5] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 1 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[6]" value="<?= $ostatki[6] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 0,5 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[7]" value="<?= $ostatki[7] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 0,25 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[8]" value="<?= $ostatki[8] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 0,125 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[9]" value="<?= $ostatki[9] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите 0,063 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[10]" value="<?= $ostatki[10] ?>">
							</td>
							<tr>
							<td>Полный остаток на сите менее 0,063 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[11]" value="<?= $ostatki[11] ?>">
							</td>
							<tr>
							<td width="262">Частный остаток на сите 31,5 мм</td>
							<td><input type="number" step="0.01" name="ostatki9[12]" value="<?= $ostatki[12] ?>"></td>
							<tr>
							<td>Частный остаток на сите 22,4 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[13]" value="<?= $ostatki[13] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 16 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[14]" value="<?= $ostatki[14] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 8 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[15]" value="<?= $ostatki[15] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 4 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[16]" value="<?= $ostatki[16] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 2 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[17]" value="<?= $ostatki[17] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 1 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[18]" value="<?= $ostatki[18] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 0,5 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[19]" value="<?= $ostatki[19] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 0,25 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[20]" value="<?= $ostatki[20] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 0,125 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[21]" value="<?= $ostatki[21] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите 0,063 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[22]" value="<?= $ostatki[22] ?>">
							</td>
							<tr>
							<td>Частный остаток на сите менее 0,063 мм</td>
							<td>	<input type="number" step="0.01" name="ostatki9[23]" value="<?= $ostatki[23] ?>">
							</td>
							<tr>
							</table>							
									
<!--------------------------------ЩЕБЕНЬ------------------------------------------------------->
							
		<table id="zern_table7" style="display:none;">
			<tr><td> ЗЕРНОВОЙ СОСТАВ</td>
							
							<tr>
							<td width="262">Содержание зерен размером мельче 2D</td>
							<td><input type="number" step="0.01" name="ostatki7[0]" value="<?= $ostatki[0] ?>"></td>
							<tr>
							<td>Содержание зерен размером мельче 1.4D</td>
							<td>	<input type="number" step="0.01" name="ostatki7[1]" value="<?= $ostatki[1] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче D</td>
							<td>	<input type="number" step="0.01" name="ostatki7[2]" value="<?= $ostatki[2] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче D/1.4</td>
							<td>	<input type="number" step="0.01" name="ostatki7[3]" value="<?= $ostatki[3] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче d</td>
							<td>	<input type="number" step="0.01" name="ostatki7[4]" value="<?= $ostatki[4] ?>">
							</td>
							<tr>
							<td>Содержание зерен размером мельче d/2</td>
							<td>	<input type="number" step="0.01" name="ostatki7[5]" value="<?= $ostatki[5] ?>">
							</td>
		 </table>
		 
		 <!--------------------------------ЩЕБЕНЬ  ШЛАКОВЫЙ------------------------------------------------------->
							
		<table id="zern_table8" style="display:none;">
			<tr><td> ЗЕРНОВОЙ СОСТАВ</td>
			<tr>
							<td width="262">Частные остатки на сите 1,25D</td>
							<td><input type="number" step="0.01" name="ostatki8[0]" value="<?= $ostatki[0] ?>"></td>
					<tr>
							<td>Частные остатки на сите D</td>
							<td>	<input type="number" step="0.01" name="ostatki8[1]" value="<?= $ostatki[1] ?>">
							</td>
							<tr>
							<td>Частные остатки на сите 0,5(D+d)</td>
							<td>	<input type="number" step="0.01" name="ostatki8[2]" value="<?= $ostatki[2] ?>">
							</td>
							<tr>
							<td>Частные остатки на сите d</td>
							<td>	<input type="number" step="0.01" name="ostatki8[3]" value="<?= $ostatki[3] ?>">
							</td>
							<tr>
							<td>Частные остатки на сите d/2 </td>
							<td>	<input type="number" step="0.01" name="ostatki8[4]" value="<?= $ostatki[4] ?>">
							</td>
							<tr>
							<td>Частные остатки на сите менее d</td>
							<td>	<input type="number" step="0.01" name="ostatki8[5]" value="<?= $ostatki[5] ?>">
							</td>
							<tr>
							<td>Полные остатки на сите 1,25D</td>
							<td>	<input type="number" step="0.01" name="ostatki8[6]" value="<?= $ostatki[6] ?>">
							</td>
							<tr>
							<td>Полные остатки на сите D</td>
							<td>	<input type="number" step="0.01" name="ostatki8[7]" value="<?= $ostatki[7] ?>">
							</td>
							<tr>
							<td>Полные остатки на сите 0,5(D+d)</td>
							<td>	<input type="number" step="0.01" name="ostatki8[8]" value="<?= $ostatki[8] ?>">
							</td>
							<tr>
							<td>Полные остатки на сите d</td>
							<td>	<input type="number" step="0.01" name="ostatki8[9]" value="<?= $ostatki[9] ?>">
							</td>
							<tr>
							<td>Частные остатки на сите d/2 </td>
							<td>	<input type="number" step="0.01" name="ostatki8[10]" value="<?= $ostatki[10] ?>">
							</td>
							<tr>
							<td>Полные остатки на сите менее d</td>
							<td>	<input type="number" step="0.01" name="ostatki8[11]" value="<?= $ostatki[11] ?>">
							</td>
							
								  </table>
		<!-------------------------------------------------------------------!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!------------------------------------------>
		<!------------------------------ 'Морозостойкость' правил начало ------------------------->
		
		<table class='tableFrost'>
		</br>
		<tr>
			<td>Наличие трещин, сколов, шелушения (контрольных образцов)</td>
			<td>
				<input type='text' name='controlDamage' class='frost' 
				value='<?= !empty($control_damage) ? $control_damage : "-" ?>'>
			</td>
		</tr>
		<tr>
			<td>Наличие трещин, сколов, шелушения (основных образцов)</td>
			<td>
				<input type='text' name='mainDamage' class='frost' 
				value='<?= !empty($main_damage) ? $main_damage : "" ?>'>
			</td>
		</tr>
		<tr>
			<td>Среднее уменьшение массы образцов (контрольных образцов)</td>
			<td>
				<input type='text' name='controlMass' class='frost' 
				value='<?= !empty($control_mass) ? $control_mass : "-" ?>'>
			</td>
		</tr>
		<tr>
			<td>Среднее уменьшение массы образцов (основных образцов)</td>
			<td>
				<input type='number' name='mainMass' step="0.01" 
				value='<?= !empty($main_mass) ? $main_mass : "" ?>'>
			</td>
		</tr>
		<tr>
			<td>Прочность при сжатии насыщенных водой образцов (контрольных образцов)</td>
			<td>
				<input type='number' name='controlStrength[0]' step="0.01" 
				value='<?= !empty($control_strength[0]) ? $control_strength[0] : "" ?>'>
				<input type='number' name='controlStrength[1]' step="0.01" 
				value='<?= !empty($control_strength[1]) ? $control_strength[1] : "" ?>'>
				<input type='number' name='controlStrength[2]' step="0.01" 
				value='<?= !empty($control_strength[2]) ? $control_strength[2] : "" ?>'>
				<input type='number' name='controlStrength[3]' step="0.01" 
				value='<?= !empty($control_strength[3]) ? $control_strength[3] : "" ?>'>
				<input type='number' name='controlStrength[4]' step="0.01" 
				value='<?= !empty($control_strength[4]) ? $control_strength[4] : "" ?>'>
				<input type='number' name='controlStrength[5]' step="0.01" 
				value='<?= !empty($control_strength[5]) ? $control_strength[5] : "" ?>'>
			</td>
		</tr>
		<tr>
			<td>Прочность при сжатии насыщенных водой образцов (основных образцов)</td>
			<td>
				<input type='number' name='mainStrength[0]' step="0.01" 
				value='<?= !empty($main_strength[0]) ? $main_strength[0] : "" ?>'>
				<input type='number' name='mainStrength[1]' step="0.01" 
				value='<?= !empty($main_strength[1]) ? $main_strength[1] : "" ?>'>
				<input type='number' name='mainStrength[2]' step="0.01" 
				value='<?= !empty($main_strength[2]) ? $main_strength[2] : "" ?>'>
				<input type='number' name='mainStrength[3]' step="0.01" 
				value='<?= !empty($main_strength[3]) ? $main_strength[3] : "" ?>'>
				<input type='number' name='mainStrength[4]' step="0.01" 
				value='<?= !empty($main_strength[4]) ? $main_strength[4] : "" ?>'>
				<input type='number' name='mainStrength[5]' step="0.01" 
				value='<?= !empty($main_strength[5]) ? $main_strength[5] : "" ?>'>
			</td>
		</tr>
		<tr>
			<td>Средняя прочность при сжатии насыщенных водой образцов (контрольных образцов)</td>
			<td>
				<input type='number' name='controlMediumStrength' step="0.01" 
				value='<?= !empty($control_medium) ? $control_medium : "" ?>'>
			</td>
		</tr>
		<tr>
			<td>Средняя прочность при сжатии насыщенных водой образцов (основных образцов)</td>
			<td>
				<input type='number' name='mainMediumStrength' step="0.01" 
				value='<?= !empty($main_medium) ? $main_medium : "" ?>'>
			</td>
		</tr>
		<tr>
			<td>Нижняя граница доверительного интервала (контрольных образцов)</td>
			<td>
				<input type='number' name='controlBottomLine' step="0.01" 
				value='<?= !empty($control_bottom_line) ? $control_bottom_line : "" ?>'>
			</td>
		</tr>
		<tr>
			<td>Нижняя граница доверительного интервала (основных образцов)</td>
			<td>
				<input type='number' name='mainBottomLine' step="0.01" 
				value='<?= !empty($main_bottom_line) ? $main_bottom_line : "" ?>'>
			</td>
		</tr>
		<tr>
			<td>Соотношение</td>
			<td>
				<select class='ratio' name='ratio'>
					<option <?= ($ratio == 'соблюдается') ? 'selected' : '' ?> 
					value='соблюдается'>соблюдается</option>
					<option <?= ($ratio == 'не соблюдается') ? 'selected' : '' ?> 
					value='не соблюдается'>не соблюдается</option>
				</select>
			</td>
		</tr>
		</table>
        <!------------------------------ 'Морозостойкость' правил окончание ----------------------->
		
		<!---------------------------------'Новый протокол, Ареометрический метод' правил начало----------------------------------->
			<br>
			<table id="metric_method" style="display:none;">
			<tr><td> Ареометрический метод</td> 
            <tr>
                <td width="262">Фракция грунта 10 мм</td>
                <td><input type="number" step="0.01" name="ostatki9[0]" value="<?= $ostatki[0] ?>"></td>
            <tr>
                <td width="262">Фракция грунта 5 мм</td>
                <td><input type="number" step="0.01" name="ostatki9[1]" value="<?= $ostatki[1] ?>"></td>
            <tr>
                <td width="262">Фракция грунта 2 мм</td>
                <td><input type="number" step="0.01" name="ostatki9[2]" value="<?= $ostatki[2] ?>"></td>
            <tr>
                <td width="262">Фракция грунта 1 мм</td>
                <td><input type="number" step="0.01" name="ostatki9[3]" value="<?= $ostatki[3] ?>"></td>    
             
			<tr>
				<td width="262">Фракция грунта 0,5 мм</td>
				<td><input type="number" step="0.01" name="ostatki9[4]" value="<?= $ostatki[4] ?>"></td>
			<tr>
				<td>Фракция грунта 0,25 мм</td>
				<td><input type="number" step="0.01" name="ostatki9[5]" value="<?= $ostatki[5] ?>"></td>
			<tr>
				<td>Фракция грунта 0,1 мм</td>
				<td><input type="number" step="0.01" name="ostatki9[6]" value="<?= $ostatki[6] ?>"></td>
			<tr>
				<td>Фракция грунта 0,05 мм</td>
				<td><input type="number" step="0.01" name="ostatki9[7]" value="<?= $ostatki[7] ?>"></td>
			<tr>
				<td>Фракция грунта 0,01 мм</td>
				<td><input type="number" step="0.01" name="ostatki9[26]" value="<?= $ostatki[26] ?>"></td>
			<tr>
				<td>Фракция грунта 0,002 мм</td>
				<td><input type="number" step="0.01" name="ostatki9[27]" value="<?= $ostatki[27] ?>"></td>
			<tr>
				<td>Фракция грунта менее 0,002 мм</td>
				<td><input type="number" step="0.01" name="ostatki9[28]" value="<?= $ostatki[28] ?>"></td>
			<tr>
				<td width="262">Фракция грунта 10 мм полн.</td>
				<td><input type="number" step="0.01" name="ostatki9[9]" value="<?= $ostatki[9] ?>"></td>
			<tr>
				<td>Фракция грунта 5 мм полн.</td>
				<td><input type="number" step="0.01" name="ostatki9[10]" value="<?= $ostatki[10] ?>"></td>
			<tr>
				<td>Фракция грунта 2 мм полн.</td>
				<td><input type="number" step="0.01" name="ostatki9[11]" value="<?= $ostatki[11] ?>"></td>
			<tr>
				<td>Фракция грунта 1 мм полн.</td>
				<td><input type="number" step="0.01" name="ostatki9[12]" value="<?= $ostatki[12] ?>"></td>
			<tr>
				<td>Фракция грунта 0,5 мм полн.</td>
				<td><input type="number" step="0.01" name="ostatki9[13]" value="<?= $ostatki[13] ?>"></td>
			<tr>
				<td>Фракция грунта 0,25 мм полн.</td>
				<td><input type="number" step="0.01" name="ostatki9[14]" value="<?= $ostatki[14] ?>"></td>
			<tr>
				<td>Фракция грунта 0,1 мм полн.</td>
				<td><input type="number" step="0.01" name="ostatki9[15]" value="<?= $ostatki[15] ?>"></td>
			<tr>
				<td>Фракция грунта 0,05 мм полн.</td>
				<td><input type="number" step="0.01" name="ostatki9[16]" value="<?= $ostatki[16] ?>"></td>
			<tr>
				<td>Фракция грунта 0,01 мм полн.</td>
				<td><input type="number" step="0.01" name="ostatki9[29]" value="<?= $ostatki[29] ?>"></td>
			<tr>
				<td>Фракция грунта 0,002 мм полн.</td>
				<td><input type="number" step="0.01" name="ostatki9[30]" value="<?= $ostatki[30] ?>"></td>
			<tr>
				<td>Фракция грунта менее 0,002 мм полн.</td>
				<td><input type="number" step="0.01" name="ostatki9[31]" value="<?= $ostatki[31] ?>"></td>
		</table>
		<!---------------------------------'Новый протокол, Ареометрический метод' правил окончание----------------------------------->
		<br>
        <input type="submit" value="Сохранить изменения" style="margin-left:0px">
    </form>
	<button class='btnPdf'>Прикрепить PDF</button>
	<div class='uploadProtocol'>
		<h2>Загрузить PDF-версию протокола</h2>
		<form enctype="multipart/form-data" action="/results_isp.php?ID=<?=$_GET['ID']?>" method="POST">
   
			<input type="hidden" name="MAX_FILE_SIZE" value="300000000" />
		   
			Отправить этот файл: <input name="userfile" type="file" />
			<input type="submit" value="Отправить файл" />
		</form>
	</div>
	<?php if ( ! $_FILES )
{
	$res_id=$DB->Query("SELECT `PDF` FROM `ba_tz` WHERE `ID_Z`=".(int)$_GET['ID'])->Fetch();
	//$str=strpos($res_id['PDF'], "№");
	//$row=substr($res_id['PDF'], $str+1);
	if($res_id['PDF']) echo "<br />Загружена PDF-версия: <a href='/pdf/".$res_id['PDF']."'>".$res_id['PDF']."</a><br /><br />";
	?>
		
	<?php
	} 
else
{

$res_id=$DB->Query("SELECT `ID` FROM `ba_tz` WHERE `ID_Z`=".(int)$_GET['ID'])->Fetch();


$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/pdf/';
$uploadfile = $uploaddir . $_FILES['userfile']['name'];
$user_name = "bitrix";

$res_file=$DB->Query("UPDATE `ba_tz` SET `PDF`='".$_FILES['userfile']['name']."', `NO_BITRIX`='1' WHERE `ID`=".(int)$res_id['ID']);
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "</br>Файл ".$_FILES['userfile']['name']."  был успешно загружен.\n</br>";
} else {
    echo "</br>Ошибка загрузки!\n</br>";
}

}?>
	<!-- Правил, начало -->
	</br>
	<!------------------------------------ 'Скачать протокол' начало правил ------------------------------>
	<?php 
		$resDownload=$DB->Query("SELECT `PDF`, `OBSHEE`, `TYPE_TZ` FROM `ba_tz` WHERE `ID_Z`=".(int)$_GET['ID'])->Fetch();
		 $req = new \Bitrix\Crm\EntityRequisite();
    $rs = $req->getList([
        "filter" => [
            "ENTITY_ID" => $deal2['COMPANY_ID'],
        ]
    ]);


    $requis = $rs->Fetch();
    $email = $requis['RQ_EMAIL'];
    //$name_isp = 'Маргарита Черкозьянова';//$deal['ASSIGNED_BY_NAME'].' '.$deal['ASSIGNED_BY_LAST_NAME'];
    $name_isp = 'Маргарита Печерская';
	$res_acc = $DB->Query("SELECT * FROM `PROTOCOLS_FOR_CUSTOMER` WHERE `ID_TZ`=" . $sql['ID'])->Fetch();
	?>
    <div id="protDownload"><?php  if(!$resDownload['PDF'] && !empty($resDownload['OBSHEE']) && $resDownload['OBSHEE']) echo '<a href="/protocol_generator/protocol.php?ID=' . $_GET['ID'] . '&TZ_ID=' . $sql['ID'] . '">Скачать протокол</a>'; ?></div>
    <div id="protDownload"><?php  if(!$resDownload['PDF']&& !empty($resDownload['OBSHEE']) && $resDownload['OBSHEE'] && $resDownload['TYPE_TZ']==0) echo '| <a href="/protocol_generator/protocol_union.php?ID=' . $_GET['ID'] . '&TZ_ID=' . $sql['ID'] . '">Скачать протокол без объединения ячеек</a>'; ?></div>
	 <div id="protDownload"><?php  if(!$resDownload['PDF']&& !empty($resDownload['OBSHEE']) && $resDownload['OBSHEE'] && $resDownload['TYPE_TZ']==0) echo '| <a href="/protocol_generator/protocol_for_customer.php?ID=' . $_GET['ID'] . '&TZ_ID=' . $sql['ID'] . '">Сформировать сокращенный протокол</a>'; ?></div>
	<input type="button" title="Отправить клиенту" class="add send"
                       onclick="window.open ('/mail_2.php?ID=<?= $deal2['ID'] ?>&TZ_ID=<?= $sql['ID'] ?>&TYPE=4&EMAIL=<?= $email ?>&NAME=<?= $name_isp ?>&ATTACH=<?= $res_acc['ACTUAL_VER'] ?>&TITLE=<?= $deal2['TITLE'] ?>', '_blank')"
                       <?= !$res_acc['ID'] ? 'disabled' : '' ?>>
	<!------------------------------------ 'Скачать протокол' окончание правил -------------------------->
	<!-- Правил, окончание -->
	<!--<br>
    <?//= ($sql['OBSHEE']) ? '<a href="/protocol_generator/protocol.php?ID=' . $_GET['ID'] . '&TZ_ID=' . $sql['ID'] . '&TEST">Скачать общий протокол (!!!TEST!!!)</a>' : '' ?>-->

    <?php
} elseif(!$fl_num) {
	 
	header('Location: http://172.16.255.2/protocols_test.php');
	die();
    ?>

    <?php
    $deals = CCrmDeal::GetList(Array('DATE_CREATE' => 'DESC'), Array('!TYPE_ID' => '3'));
	
	$location_year = ""; $location_laba = ""; $location_protocol = "";
	if(!empty($_GET['laba'])&&empty($_GET['year'])&&empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&!empty($_GET['year'])&&empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?year=".$_GET['year']."&protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&empty($_GET['year'])&&!empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?protocol=".$_GET['protocol']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?protocol=".$_GET['protocol']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&&empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?year=".$_GET['year']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&year=".$_GET['year']."&protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&empty($_GET['year'])&&!empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&protocol=".$_GET['protocol']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?protocol=".$_GET['protocol']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?laba=".$_GET['laba']."&protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(empty($_GET['laba'])&&!empty($_GET['year'])&&!empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?protocol=".$_GET['protocol']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?year=".$_GET['year']."&protocol=".$_GET['protocol']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?year=".$_GET['year']."&protocol='+this.options[this.selectedIndex].value)";
	}
	elseif(!empty($_GET['laba'])&&!empty($_GET['year'])&&!empty($_GET['protocol'])) {
		$location_year = "window.location.assign('/results_isp.php?protocol=".$_GET['protocol']."&laba=".$_GET['laba']."&year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?year=".$_GET['year']."&protocol=".$_GET['protocol']."&laba='+this.options[this.selectedIndex].value)";
		$location_protocol= "window.location.assign('/results_isp.php?year=".$_GET['year']."&laba=".$_GET['laba']."&protocol='+this.options[this.selectedIndex].value)";
	}
	else {
		$location_year = "window.location.assign('/results_isp.php?year='+this.options[this.selectedIndex].value)";
		$location_laba = "window.location.assign('/results_isp.php?laba='+this.options[this.selectedIndex].value)";
		$location_protocol = "window.location.assign('/results_isp.php?protocol='+this.options[this.selectedIndex].value)";
	}
	
    ?>
	 <table style="float: right;"><tr>
	 <td>
		<select class="oborud" name="protocol" id="protocol" onchange="<?= $location_protocol?>"  style="float: left; width: 100%">
		
			<option value='all' style='font-weight:bold' <?=$_GET['protocol'] == 'all' ? 'selected' : ''?> selected>Все стадии</option>
			<option value="1" style='font-weight:bold' <?=$_GET['protocol'] == '1' ? 'selected' : ''?>>Испытания еще не проводились</option>
			<option value='2' style='font-weight:bold' <?=$_GET['protocol'] == '2' ? 'selected' : ''?>>Проводятся испытания</option>
			<option value='3' style='font-weight:bold' <?=$_GET['protocol'] == '3' ? 'selected' : ''?>>Испытания прошли, протокол выдан</option>
		<!--	<option value='4' style='font-weight:bold' <?=$_GET['protocol'] == '4' ? 'selected' : ''?>>Заявка прекращена</option>-->
		</select>
	 </td>
	 <td>
	 <select class="oborud" name="place_of_installation_or_storage" id="laba"
            onchange="<?= $location_year?>"
            style="float: left; width: 100%">
		<option value="" selected>Выберите год</option>
        <option value='all' style='font-weight:bold' <?=$_GET['year'] == 'all' ? 'selected' : ''?>>Все года</option>;
		<option value="2020" style='font-weight:bold' <?=$_GET['year'] == '2020' ? 'selected' : ''?>>2020</option>
		<option value='2019' style='font-weight:bold' <?=$_GET['year'] == '2019' ? 'selected' : ''?>>2019</option>;
			
       
    </select>
	</td><td>
	<select class="oborud" name="year_p" id="year"
            onchange="<?= $location_laba?>"
            style="float: left; width: 100%">
        <option value="0" selected>Все лаборатории</option>
		
        <?php
        $select = $DB->Query("SELECT * FROM `ba_laba`");
        while ($option = $select->Fetch()) {
			
            echo "<option ";
            if ($_GET['laba'] == $option['ID']) {
                echo "selected";
            }
            echo " value='" . $option['ID'] . "' style='font-weight:bold'>" . $option['NAME'] . "</option>";
			
        }
        ?>
    </select></td>
	<td><input type="button" class="add2" value="Х" onclick="window.location.assign('/results_isp.php')"></input></td>
	<td><input type="checkbox" name="a_filter" id="a_filter" value="">С аттестатом</td>
	</tr></table>
	<br /><br /><br />
    <table id="results_isp">
	<thead>
	<tr>
		<th></th>
		<th>ОА</th>
        <th>№</th>
		<th>Дата</th>
		<th>Клиент</th>
		<th>Объект испытаний</th>
		<th>Ответственный</th>
		<th>№ ТЗ</th>
		<th>Стоимость</th>
		<th>Заявка</th>
		<th>Рез-ты испытаний</th>
		<th>DOC/PDF</th>
		<th>Последнее изменение</th>
		<th>Соот./не соот.</th>
    </tr>
	<tr>
	<th style="margin:0px"></th>
	<th style="margin:0px"></th>
	<th><input type="text" name="num_filter" id="num_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="date_filter" id="date_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="customer_filter" id="customer_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="object_filter" id="object_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="assigned_filter" id="assigned_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="tz_filter" id="tz_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="cost_filter" id="cost_filter" style="margin:0px" value=""></th>
	<th><input type="text" name="request_filter" id="request_filter" style="margin:0px" value=""></th>
	<th></th>
	<th></th>
	<th></th>
	<th></th>
	
	</tr>
	</thead>
	<tbody>
		<div id='buttonRightTable' class='center'></div>
		<div id='buttonLeftTable' class='center'></div>
	
        <?php
			
			while ($deal = $deals->Fetch()) { 
			$id_protocol = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING','FINAL_INVOICE', '1', '2', '3', '4', 'WON');
				switch ($_GET['protocol']) {
						case '1':
							$id_protocol = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING','FINAL_INVOICE');
							break;
						case '2':
							$id_protocol = array('1');
							break;	
						case '3':
							$id_protocol = array('2', '3', '4', 'WON');
							break;
						case '4':
							$id_protocol = array('LOSE');
							break;
						case 'all':
							$id_protocol = array('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING','FINAL_INVOICE', '1', '2', '3', '4', 'WON');
							break;
				}
				
			// if($id_protocol && !in_array($deal['STAGE_ID'], $id_protocol)) {
				// continue;
			// }
			
			switch ($_GET['laba']) {
						case '1':
							$id_dep = 54;
							break;
						case '2':
							$id_dep = 57;
							break;
						case '3':
							$id_dep = 56;
							break;
						case '4':
							$id_dep = 55;
							break;
						case '5':
							$id_dep = 58;
							break;
						case '6':
							$id_dep = 53;
							break;
					}	
					
			switch ($_GET['year']) {
						case '2019':
							$id_year1=date("Y-m-d", strtotime("2018-12-31"));
							$id_year2=date("Y-m-d", strtotime("2020-01-01"));
							break;
						case '2020':
							$id_year1=date("Y-m-d", strtotime("2019-12-31"));
							$id_year2=date("Y-m-d", strtotime("2021-01-01"));
							break;
						case 'all':
							$id_year1=date("Y-m-d", strtotime("2019-01-01"));
							$id_year2=date("Y-m-d", strtotime("2029-12-31"));
							break;
						
					}
			if(empty($_GET['year']))
			{
				$id_year1=date("Y-m-d", strtotime("2019-11-30"));
				$id_year2=date("Y-m-d", strtotime("2021-01-01"));
			}
			$userr = CUser::GetByID($deal['ASSIGNED_BY_ID'])->Fetch();
				$user_dep = $userr['UF_DEPARTMENT'][0];
			
			$dop_assign=0;
				$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
							if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
								
								foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
								{
									$assign_user = CUser::GetByID((int)$assign)->Fetch();
									if($assign_user['UF_DEPARTMENT'][0]==$id_dep)
									{$dop_assign=1;}
								}
							}
				
			
			$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $deal['ID'])->Fetch();
			$A='';
			if($res_tz['ATTESTAT']&&$res_tz['IN_ATTESTAT_DIAPASON']) $A='A';
			if($res_tz['DATE_P']||$res_tz['PDF']|| $res_tz['DATE_P_CORR']) {
			if ((!empty($_GET['laba']) && ($user_dep == $id_dep||$dop_assign))||empty($_GET['laba']))
				{
				$date_protocol = $res_tz['DATE_P_CORR'] ? $res_tz['DATE_P_CORR'] :$res_tz['DATE_P'];
				$date_p = date("Y-m-d",strtotime($date_protocol));	
				if($date_p>$id_year1 && $date_p<$id_year2)
				{
					//echo $date_p."--";	
					//$date_protocol = $res_tz['DATE_P_CORR'] ? $res_tz['DATE_P_CORR'] :$res_tz['DATE_P'];
						
						$y_date_act = date("Y", strtotime($res_tz['DATE_ACT']));
						if ($y_date_act != '2020') $y_date_act = "/".substr($y_date_act, 2);
								else $y_date_act = "/".$y_date_act;
						
						$y_date_p = date("Y", strtotime($date_protocol));
						if ($y_date_p != '2020') $y_date_p = "/".substr($y_date_p, 2);
								else $y_date_p = "/".$y_date_p;
								
				if ($res_tz['NUM_P'] === 'номер не присвоен') {
					$num_tz = 'номер не присвоен';
				} else {
					$num_tz = $res_tz['NUM_P'] ? $res_tz['NUM_P'].$y_date_p : 'номер не присвоен';
				}		
				
				//$num_tz = $res_tz['NUM_P'] ? $res_tz['NUM_P'] : (int)$res_tz['ID']+485;
				$color=''; $c=0; $title='';
				$history_ass='';
				$history_date='';
				if ($res_tz['ID']) 
				{	
					$history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . $res_tz['ID']. " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
					$history_ass=$history['ASSIGNED'];
					$history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
					$history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
					if($history_date<$history_date_null) $history_date='';
				}
			
				//var_dump($history);
				switch ($deal['STAGE_ID']) {
					case 'NEW':
						$color='yellow';
						$c=1;
						$title='Протокол не выдан. Испытания еще не проводились.';
						break;
					case 'PREPARATION':
						$color='yellow';
						$c=1;
						$title='Протокол не выдан. Испытания еще не проводились.';
						break;
					case 'PREPAYMENT_INVOICE':
						$color='yellow';
						$c=1;
						$title='Протокол не выдан. Испытания еще не проводились.';
						break;
					case 'EXECUTING':
						$color='orange';
						$c=1;
						$title='Протокол не выдан. Проводятся испытания.';
						break;
					case 'FINAL_INVOICE':
						$color='orange';
						$c=1;
						$title='Протокол не выдан. Проводятся испытания.';
						break;
					case '1':
						$color='orange';
						$c=2;
						$title='Протокол не выдан. Проводятся испытания.';
						break;
					case '2':
						$color='green';
						$c=3;
						$title='Протокол выдан';
						break;
					case '4':
						$color='green';
						$c=3;
						$title='Протокол выдан';
						break;
					case '3':
						$color='green';
						$c=3;
						$title='Протокол выдан';
						break;
					case 'WON':
						$color='green';
						$c=3;
						$title='Протокол выдан';
						break;
					case '5':
					case '6':
					case '7':
					case '8':
					case '9':
					case '10':
					case '11':
					case '12':
					case 'LOSE':
						$color='red';
						$c=4;
						$title='Испытания не проведены. Заявка прекращена.';
						break;
				}
				
				$mater_arr=[];
				$mater_arr_id=[];
								
				$res_mat = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=".$deal['ID']);
						while($mater=$res_mat->Fetch())
						{
							$mater_arr[]=$mater['NAME_MATERIAL'];
							$mater_arr_id[]=$mater['ID_MATERIAL'];
						}
					
					$dealMaterials = ($deal['UF_CRM_1576411922643'] ? $deal['UF_CRM_1576411922643'] : implode(', ', $deal['UF_CRM_1569151529509']));
					$dealMaterials = ($deal['UF_CRM_1569151529509'][0] ? $dealMaterials : implode(', ', $mater_arr));
						$color2='green';
						$tu = 'Соотв.';
						$tz = unserialize($res_tz['TZ']);
						$tz_mater = $tz[1]['mater'];
						for($i=2; $i<=count($tz); $i++)
						{
							$tz_mater = $tz_mater . "; " . $tz[$i]['mater'];
						}
						$results = unserialize($res_tz['MATCH_MANUAL']);
						//var_dump($results);
						for($i=0; $i<count($results); $i++)
							for($j=0; $j<count($results[$i]); $j++)
								for($k=1; $k<=count($results[$i][$j]); $k++)
									{
										if ($results[$i][$j][$k]=="0") {$tu = 'Не соотв.'; $color2='red';}
									}
						//var_dump($tz);
						//$tz_mater = implode('; ', $tz[])
						if ($res_tz['ACTUAL_FOR_OBJECT']) { $tu = 'Архивный'; $color='grey';}
						
					$probe = unserialize($res_tz['PROBE']);
					$probePlace = $probe[1]['mesto_data'];
				?>
				<tr>
					<td style="background-color: <?=$color?>; color: <?=$color?>" title="<?=$title?>"><?= $c ?></td>
					<td ><?= $A ?></td>
					<td><?= $num_tz?> </td>
					<td><?= ($res_tz['DATE_P_CORR'] ? date("d.m.Y", strtotime($res_tz['DATE_P_CORR'])) : date("d.m.Y", strtotime($res_tz['DATE_P']))) ?> </td>
					<td><?= $deal['COMPANY_TITLE'] ?></td>
					<td><?= $dealMaterials//$deal['UF_CRM_1576411922643'] ? $deal['UF_CRM_1576411922643'] : implode(', ', $deal['UF_CRM_1569151529509']) ?></td>
					<td><?php echo "<b>".getInitials($deal['ASSIGNED_BY_NAME'])." ".$deal['ASSIGNED_BY_LAST_NAME']."</b>";
						if ($assign_arr['UF_CRM_1571643970']['VALUE'][0]) {
								$assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $deal['ID']);
								foreach($assign_arr['UF_CRM_1571643970']['VALUE'] as $assign)
								{
									$assign_user = CUser::GetByID((int)$assign)->Fetch();
									echo ", ".getInitials($assign_user['NAME'])."&nbsp;".$assign_user['LAST_NAME'];
								}
							}						
						?>
						</td>
					<td><a href="/tz_show.php?ID=<?= $res_tz['ID'] ?>" target="_blank"><?= $res_tz['ID'] ?></a></td>
					<td><a href='/protocol_generator/account.php?ID=<?= $deal['ID'] ?>&TZ_ID=<?=$res_tz['ID']?>'><?= number_format($res_tz['PRICE'], 2, ',', '') ?></a></td> 
					<td><a href='/request_card.php?ID=<?= $deal['ID'] ?>&ACT_NUM=<?=$res_tz['ACT_NUM']?>' target="_blank"><?= $deal['TITLE'] ?></a></td>
					<td><a href="/results_isp.php?ID=<?= $deal['ID'] ?>">Открыть</a></td>
					<td><?php if(!$res_tz['NO_BITRIX'])
						{ 
							
								
								if($res_tz['ACTUAL_VER']&&$res_tz['NUM_P']!='номер не присвоен'&&$res_tz['NUM_P'])
							{
								$dir  = '/protocol_generator/archive/'.$res_tz['ID'].date("Y", strtotime($res_tz['DATE_ACT'])).'/';?>
								<a href="<?= $dir.$res_tz['ACTUAL_VER'] ?>.docx" target="_blank">Скачать</a>
								<?php
							}
						} 
						else 
						{ ?>
						<a href="/pdf/<?= $res_tz['PDF'] ?>" target="_blank"><?= $res_tz['PDF'] ? 'Скачать' : ''  ?> </a>
						<?php } ?>
					</td>
					<td><?= $history_ass ?> <?=$history_date?></td>
					<td style="color:<?=$color2?>"><?= $tu?></td>
				</tr>
			<?php  }
				}
			}
		//substr($deal['UF_CRM_1570765979554'],0,-1)
		} ?>
		
    </tbody></table>

    <?php
}

/*--Диапозон фактического значения начало--*/
//Если пересчитаны фактические значения показываем модальное окно для сохранения
if (!empty($arrayOutRange)) {
	?>
		<div class="wrepper-modal-export" style="display: flex">
			<div class="modal-export">
			<p>Внимание пересчитаны значения в не диапазона ОА.Для сохранения изменений нажмите "Сохранить".</p>
			<div class="wrapper_export_btn">
				<input type="submit" class="btn-export btn-export-copy" value="Сохранить">
				<!--<button class="btn-export btn-export-close">Закрыть</button>-->
			</div>
			</div>
		<div>
	<?php
	
}
/*--Диапозон фактического значения окончание--*/
?>

    <script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <script>
        $(".add").click(function () {
            td = $(this).parent().parent();
            keys = $(this).data("keys").split("-");
            td.append('<div style="display:flex"><input class="res" "margin-left:5px" type="number" step="0.001" name="results[' + keys[0] + '][' + keys[1] + '][' + keys[2] + '][]"  ><input style="margin-left:5px" type="button" value="- " onclick="$(this).parent().remove();"></div>');
        })
    </script>
		<script>
        var s = document.getElementById("second");
        var ids = document.getElementById("ids_oborud");
		console.log(ids.value);
        document.getElementById("first").onchange  = function () {
            s.options[s.options.length] = new Option(this.options[this.selectedIndex].text, this.value);
            ids.value = JSON.stringify(Array.prototype.slice.call(JSON.parse(ids.value)).concat(this.value));
			console.log(ids.value);
        }

        s.ondblclick = function () {
            arr = Array.prototype.slice.call(JSON.parse(ids.value));
			console.log(arr);
            var index = arr.indexOf(this.value);
			console.log(index);
            if (index !== -1) arr.splice(index, 1);
            ids.value = JSON.stringify(arr);

            s.removeChild(s.options[s.selectedIndex]);
			console.log(ids.value);
        }
    </script>  
	<link rel="stylesheet" type="text/css" href="/DataTables/datatables.css"/>
	<link rel="stylesheet" type="text/css" href="/DataTables/button.css"/>
	<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"/>-->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.0/css/scroller.dataTables.min.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css"/>
	
    
	<!---'Правки протоколов' зерновой состав грунта начало---->
	<!--'Подключаем текстовый редактор'-->
    <script type="text/javascript" src="/tinymce/tinymce.min.js"></script>
	<!--'Настраиваем текстовый редактор'-->
    <script>/*tinymce.init({ 
		//Текстовые поля к которым будет подключен текстовый редактор
        selector:'textarea#dop_info',
		//Отключаем меню
		//Отключаем меню
		//menubar: false,
		//Настраиваем отображение меню (1 строка)
		//superscript - надстрочный индекс, subscript - подстрочный индекс
		menu: {
			format: { title: 'Format', items: 'superscript subscript' },
		},
		statusbar: false,		   
		//Отключаем дополнительное меню (2 строка)
		toolbar: false,
		plugins : "paste", //плагин paste, отвечает за обработку вставленного в редактор текста.
		paste_remove_styles: true, //удаляет CSS стили
		paste_remove_spans: true, //удаляет теги span
		paste_strip_class_attributes: 'all', // удаляет атрибуты class
		paste_block_drop: true, // блокирует вставку с помощью drag&drop.
    });*/
    
    </script>
	
	<script>tinymce.init({ 
		//Текстовые поля к которым будет подключен текстовый редактор
        selector:'input.normative_value',
		//Настраиваем отображение меню (1 строка)
		//superscript - надстрочный индекс, subscript - подстрочный индекс
		menu: {
			format: { title: 'Format', items: 'superscript subscript' },
		},
		//Убераем надпись бренда снизу текстового редактора
		branding: false,
		//отключить путь элемента в строке состояния внизу редактора(Путь к элементу показывает выбранный элемент и родительские элементы)
		elementpath: false,
		//этот параметр позволяет установить минимальную высоту
		min_height: 40,
		//Этот параметр позволяет указать, должен ли TinyMCE отображать строку состояния в нижней части редактора.
		statusbar: false,	   
		//Отключаем дополнительное меню (2 строка)
		toolbar: false,
		plugins : "paste", //плагин paste, отвечает за обработку вставленного в редактор текста.
		paste_remove_styles: true, //удаляет CSS стили
		paste_remove_spans: true, //удаляет теги span
		paste_strip_class_attributes: 'all', // удаляет атрибуты class
		paste_block_drop: true, // блокирует вставку с помощью drag&drop.
    });
    
    </script>
    <!---'Правки протоколов' зерновой состав грунта окончание---->
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
	<script type="text/javascript" language="javascript" src="/jszip/jquery.dataTables.customSortandFilters.js"></script>
	<link rel="stylesheet" type="text/css" href="/select2/dist/css/select2.min.css"/>
	<script type="text/javascript" src="/select2/dist/js/select2.full.js"></script>
	<script>
	 $(document).ready(function () {
            
			$('.oborud_change').select2();
			$('.verify_change').select2();
	 });
	</script>
	<script>
        $(document).ready(function () {
             table= $('#results_isp').DataTable({
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
				"columnDefs": [{ "type": "natural", "targets": 2 },{ "sType": "ruDate", "targets": 3 },{ "type": "natural-nohtml", "targets": 7 }, 
				{
				  "targets": 5,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 4,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 3,
				  "render": $.fn.dataTable.render.ellipsis( 50, true )
				},
				{
				  "targets": 6,
				  "render": $.fn.dataTable.render.ellipsis( 60, true )
				}
				],
				"order": [[ 3, "asc" ]],
				"colReorder": true,
				"bSortCellsTop": true,
				"dom": 'Blfrtip',
				"buttons": [ 'copy', 'excel', 'colvis', 'print' ]
				
            });
			
			$('#a_filter').on( 'click', function () {
				if ($('#a_filter').prop("checked")) {
					table
					.columns( 1 )
					.search( 'A' )
					.draw();
				} else {
					table
					.columns( 1 )
					.search( '' )
					.draw();
				}
				
			} );
			
			$('#num_filter').on( 'keyup', function () {
				table
					.columns( 2 )
					.search( this.value )
					.draw();
			} );
			
			$('#date_filter').on( 'keyup', function () {
				table
					.columns( 3 )
					.search( this.value )
					.draw();
			} );
			
			$('#customer_filter').on( 'keyup', function () {
				table
					.columns( 4 )
					.search( this.value )
					.draw();
			} );
			$('#object_filter').on( 'keyup', function () {
				table
					.columns( 5 )
					.search( this.value )
					.draw();
			} );
			$('#assigned_filter').on( 'keyup', function () {
				table
					.columns( 6 )
					.search( this.value )
					.draw();
			} );
			
			$('#tz_filter').on( 'keyup', function () {
				table
					.columns( 7 )
					.search( this.value )
					.draw();
			} );
			
			$('#cost_filter').on( 'keyup', function () {
				table
					.columns( 8 )
					.search( this.value )
					.draw();
			} );
			
			$('#request_filter').on( 'keyup', function () {
				table
					.columns( 9 )
					.search( this.value )
					.draw();
			} );
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
	<script>
	<!----------------------- 'Морозостойкость' правил начало -------------------------------->
	window.onload = function (){
	//показываем инпуты зернового состава и морозостойкости
	var label = document.getElementById("sredType").value;
	switch (label) {
		case 'zern_№2':
	 	case 'zern':
		//-- Зерновой состав, упрощенные протоколы --//
		case 'zern_short_simple':
		//-- ./Зерновой состав, упрощенные протоколы --//
		document.getElementById("zern_table").style.display='block';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	   case 'grunt_№2':	 
	  case 'grunt':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='block';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	case 'prirod_№2':				   
	  case 'prirod':
		//-- Зерновой состав, упрощенные протоколы --//
		case 'prirod_short_simple':
		//-- ./Зерновой состав, упрощенные протоколы --//
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='block';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	 case 'tu_12801_№2':				
	  case 'tu_12801':
	  case 'gost31015':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='block';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	case 'tu_183_№2':			  
	  case 'tu_183':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='block';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	  case 'tu_183_2':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='block';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	  case 'osk4':
		document.getElementById("zern_table").style.display='block';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	  case 'osk3':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='block';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	 case 'shps_№2':				
	  case 'shps':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='block';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
		case 'zern_sheb_№2':				
		case 'sheb':
		case 'zern_sheb':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='block';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
		case 'sheb_shlak_simple':
		case 'sheb_shlak_№2_simple':
		case 'sheb_shlak_№2':				 
		case 'sheb_shlak':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='block';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	case 'frost':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='block';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;	
	//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
	case 'metric_method':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		document.getElementById("metric_method").style.display='block';
		break;
	
    //------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
	case 'new_25607':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='block';
		document.querySelector(".tableFrost").style.display='none';
		document.getElementById("metric_method").style.display='none';
		break;
	  default:
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
	}	<!-------------------------- 'Морозостойкость' правил окончание --------------------------->
	//блокируем документ, если он выдан
	
	var status = document.getElementById("status").value;
	
	if (status=="Выдан")
	{
		var input = document.querySelectorAll("input");
		var select = document.querySelectorAll("select");
		for (var i = 0, cnt = input.length; i < cnt; i++) {
			input[i].disabled = true;
		}
		for (var i = 0, cnt = select.length; i < cnt; i++) {
			select[i].disabled = true;
		}
		document.getElementById('protDownload').style.display='none';
	}
	
	};
	
	

	</script>
	<script>
	<!----------------------- 'Морозостойкость' правил начало -------------------------------->
	function Selected(sredType) {
	var label = sredType.value;
	switch (label) {
		case 'zern_№2':		   
		case 'zern':
		//-- Зерновой состав, упрощенные протоколы --//
		case 'zern_short_simple':
		//-- ./Зерновой состав, упрощенные протоколы --//
		document.getElementById("zern_table").style.display='block';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	  case 'grunt_№2':						  
	  case 'grunt':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='block';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	  case 'prirod_№2':					  
	  case 'prirod':
	  //-- Зерновой состав, упрощенные протоколы --//
	  case 'prirod_short_simple':
	  //-- ./Зерновой состав, упрощенные протоколы --//
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='block';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	  case 'tu_12801_№2':						
	  case 'tu_12801':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='block';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	  case 'tu_183_№2':					  
	  case 'tu_183':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='block';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	  case 'tu_183_2':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='block';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	  case 'osk4':
		document.getElementById("zern_table").style.display='block';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	  case 'osk3':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='block';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	  case 'shps_№2':					
	  case 'shps':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='block';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
		case 'zern_sheb_№2':						
		case 'sheb':
		case 'zern_sheb':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='block';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
		case 'sheb_shlak_simple':
		case 'sheb_shlak_№2_simple':
		case 'sheb_shlak_№2':						 
		case 'sheb_shlak':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='block';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	case 'frost':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='block';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
		break;
	//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
	case 'metric_method':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		document.getElementById("metric_method").style.display='block';
		break;
	case 'new_25607':
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='block';
		document.querySelector(".tableFrost").style.display='none';
		document.getElementById("metric_method").style.display='none';
		break;
	//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//	
	  default:
		document.getElementById("zern_table").style.display='none';
		document.getElementById("zern_table2").style.display='none';
		document.getElementById("zern_table3").style.display='none';
		document.getElementById("zern_table4").style.display='none';
		document.getElementById("zern_table5").style.display='none';
		document.getElementById("zern_table6").style.display='none';
		document.getElementById("zern_table7").style.display='none';
		document.getElementById("zern_table8").style.display='none';
		document.getElementById("zern_table9").style.display='none';
		document.querySelector(".tableFrost").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил начало -------------------------//
		document.getElementById("metric_method").style.display='none';
		//------------------------------ 'Новый протокол, Ареометрический метод' правил окончание ----------------------//
	}
	};
		<!-------------------------- 'Морозостойкость' правил окончание --------------------------->
	</script>

	<script>
	//Показывает загрузку выданного вручную протокола 
	$('div.uploadProtocol').css('display','none');
	
	$('button.btnPdf').on('click', function(event) {
		event.preventDefault();
		$('div.uploadProtocol').css('display','block');
	});
	</script>
	<script>
	/*---------------------'Темпиратура и влажность' начало----------------------------*/
		$(document).ready(function () {
			$('input[name=TEMP_O]').on('input', function () {
				var valTempOf = $(this).val();
				$('input[name=TEMP_TO_O]').val(valTempOf);
			});
			9
			$('input[name=VLAG_O]').on('input', function () {
				var valTempOf = $(this).val();
				$('input[name=VLAG_TO_O]').val(valTempOf);
			});
		});
		/*---------------------'Темпиратура и влажность' окончание----------------------------*/
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
	</script>
	<script>
        //Количество пустых строчек в шаблоне
        $(document).ready(function () {
        /*---------------------'Протокол с обьединением ячеек' начало---------------------------*/
            $('#insert_str').on('click', function() {
                var inputSumStr = $('#sum_str');
                //Если чекбокс не отмечен, блокируем поле ввода "Количество пустых строчек в шаблоне"
                if ($(this).prop('checked')) {
                    inputSumStr.prop('disabled', false);
                } else {
                    inputSumStr.prop('disabled', true);
                }
            });
        });
            /*---------------------'Протокол с обьединением ячеек' окончание ---------------------*/
    </script>
														
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>