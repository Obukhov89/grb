<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
//---------------------------'При выборе материала, добавление госта'-------------------------//

if (!empty($_POST['id_material'])) {
    //Получаем все госты привязанные к материалу, (без повторений)
    $gostList = $DB->Query("SELECT DISTINCT g_c.gost, g_c.id FROM gosts_connection AS g_c INNER JOIN ba_gost AS b_g ON g_c.id = b_g.id_gost WHERE `NUM_OA_NEW` > '0' AND `NON_ACTUAL` != 1 AND b_g.DOP =" . (int)$_POST['id_material']);

    $arrGostList = [];
    if ($gostList->SelectedRowsCount()) {
        while ($gost = $gostList->Fetch()) {
            $arrGostList[]= array('id' => $gost['id'], 'name' => $gost['gost']);
        }
    }

    echo json_encode($arrGostList);
    /*-------------'При выборе материала, добавление госта' окончание--------------*/
}
exit
?>
