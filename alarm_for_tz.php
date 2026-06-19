<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
//---------------------------'Отношение методики к тз'-------------------------//

if (!empty($_POST['arr_gost'])) {

    if (!empty($_POST['edit'])) {
        $res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_POST['edit'])->Fetch();

    }

    CModule::IncludeModule("crm");
    $id_z = !empty($res_tz['ID_Z']) ? $res_tz['ID_Z'] : '';
    $id = !empty($_POST['id']) ? $_POST['id'] : '';
    $dealUp = CCrmDeal::GetByID($id_z);
    $updDeal = CCrmDeal::GetByID($id);

    $id_deal = (!empty($_POST['edit'])) ? $dealUp['ID'] : $updDeal['ID'];

    $checkDeal = CCrmDeal::GetByID($id_deal);
	//Находим главного ответственного
    $userAssign = $checkDeal['ASSIGNED_BY_ID'];
	
    $assign_arr = $USER_FIELD_MANAGER->GetUserFields('CRM_DEAL', $id_deal);
	//Находим дополнительного ответственного
    $usersAssign = $assign_arr['UF_CRM_1571643970']['VALUE'];
	
    //array_push($usersAssign, $userAssign);
	if (!empty($usersAssign)) {
			array_push($usersAssign, $userAssign);
	} else {
		$usersAssign = [];
		$usersAssign[] =  $userAssign;
	}
	
    $userDeps = array();
    foreach ($usersAssign as $user) {
        $userData = CUser::GetByID($user)->Fetch();

        $userDeps[] = $userData['UF_DEPARTMENT'][0];


    }

/*-------------'Методика и исполнители не соответствуют' начало--------------*/
    //Выбераем все лаборатории привязанные к методике
    foreach ($_POST['arr_gost'] as $kyeGost => $gost) {
        $gostsLab[$kyeGost] = array(0, 0, 0, 0, 0);
        $resGost = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $gost)->Fetch();

        if ($resGost['LFHI']) $gostsLab[$kyeGost][0] = 54;
        if ($resGost['LFMI']) $gostsLab[$kyeGost][1] = 56;
        if ($resGost['DSL']) $gostsLab[$kyeGost][2] = 55;
        if ($resGost['LSM']) $gostsLab[$kyeGost][3] = 57;
        if ($resGost['OSK']) $gostsLab[$kyeGost][4] = 58;
    }

    //массив соответствия ответственных и методик
    $metodicsErr = [];
	
    //массив с сотрудниками несоответствующих методик
    $arrIdUser = [];

    function some($arr)
    {
        foreach ($arr as $value) {
            if ($value != '0') {
                return true;
            }
        }
        return false;
    }

    foreach ($gostsLab as $keyGostLab => $gostLab) {

        //Проверка на соответствие методики и ответственных, если $userDeps(ответственные) не соответствуют методике(лаборатории), то вернет пустой массив
        $metodicsErr[] = $metodicsErr2 = array_uintersect($userDeps, $gostsLab[$keyGostLab], "strcasecmp");

        //Если $userDeps(ответственные) не соответствуют методике(лаборатории), получаем данные сотрудника и лабораторию(подразделение)
        if (empty($metodicsErr2)) {

            //Проверяем существуют ли методики не прикрепленные ни к одной из лабораторий, если не прикреплены ни к одной лаборатории вернет false
            $check = some($gostLab);

            if (empty($check)) {
                $arrIdUser[$keyGostLab] = 'Методика не привязана ни к одной лаборатории';
            }

            foreach ($gostLab as $idLab) {

                if (!empty($idLab)) {
                    //Выбираем всех сотрудников из лабаратории(подразделения)
                    $dbUsers = CUser::GetList(
                        $by = 'ID',
                        $order = 'asc',
                        array('ACTIVE' => 'Y', 'UF_DEPARTMENT' => $idLab),
                        array('SELECT' => array('UF_*'))
                    );

                    $num = 0;
                    while ($rs = $dbUsers->Fetch()) {
                        //Получаем id,ФИО каждого сотрудника
                        $arrIdUser[$keyGostLab][$num][] = $rs['ID'];
                        $arrIdUser[$keyGostLab][$num][] = $rs['NAME'];
                        $arrIdUser[$keyGostLab][$num][] = $rs['LAST_NAME'];
                        $arrIdUser[$keyGostLab][$num][] = $rs['SECOND_NAME'];

                        // Получаем информацию о подразделении сотрудника
                        //$oUserinfo = CUser::GetByID($rs['ID']);
                        //$res = $oUserinfo->getNext();

                        // Получаем название лаборатории(подразделения) сотрудника
                        $oDep = CIntranetUtils::GetDepartmentsData($rs["UF_DEPARTMENT"]);
                        $dep = current($oDep);
                        $arrIdUser[$keyGostLab][$num][] = $dep;

                        $num++;
                    }

                }

            }

        } else {
            $arrIdUser[$keyGostLab] = [];
        }
    }

    $data = [];
    $data['metodicsErr'] = $metodicsErr;
    $data['arrIdUser'] = $arrIdUser;
	
    echo json_encode($data);
    /*-------------'Методика и исполнители не соответствуют' окончание--------------*/
}
exit
?>