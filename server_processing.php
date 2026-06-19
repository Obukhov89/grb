<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$params = $columns = $totalRecords = $data = array();
//Переменные HTTP-запроса, (который по умолчанию содержит данные переменных $_GET, $_POST и $_COOKIE) переданные из DataTable
$params =$_REQUEST;

//Колонки из базы данных таблицы HISTORY
$columns = array(
    0 => 'REQUEST',
    1 => 'PROT_NUM',
    2 => 'TZ_ID',
    3 => 'DATE',
    4 => 'TYPE',
    5 => 'ASSIGNED'
);

$where_condition = $sqlTot = $sqlRec = $where_search_column = "";

$where_condition = " WHERE 1";

//Производим поиск в базе данных по отдельным столбцам(полям)
if (!empty($params['columns'])) {
    foreach ($params['columns'] as $key_param => $val_param) {

        if (!empty($val_param['search']['value'])) {
            $where_condition .= " AND " . $val_param['data'] . " LIKE '%" . $val_param['search']['value'] . "%' ";
        }
    }
}

//Производим поиск в базе данных по общиму поиску.Если были переданы(введены) в поле поиска символы, конкотенируем строку где ищем по этим символам
if (!empty($params['search']['value'])) {
    $where_condition .= " AND (REQUEST LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR PROT_NUM LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR TZ_ID LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR DATE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR TYPE LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR ASSIGNED LIKE '%" . $params['search']['value'] . "%') ";
}

$sql_query = " SELECT * FROM `HISTORY` ";
$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}
/*
 Сортируем выборку
 $params['order'][0]['column'] - Смотрим по какому полю у нас установлена сортировка в DataTable: "order": [[ 2, "asc" ]],
$params['order'][0]['dir'] - Смотрим сортировка по убыванию или возростанию,
$params['start'] - ограничение количества результатов(с какого начинаем, Пример: c 0),
$params['length'] - ограничение количества результатов(каким заканчиваем, Пример: до 50) в DataTable "iDisplayLength": 50,
  */
$sqlRec .= " ORDER BY " . $columns[$params['order'][0]['column']] . " " .$params['order'][0]['dir'] . " LIMIT " . $params['start'] . " ," . $params['length'] . " ";

$queryTot = $DB->Query($sqlTot);
//количество выбранных записей при запросе
$totalRecords = $queryTot->SelectedRowsCount();

$queryRecords = $DB->Query($sqlRec);

//Делает выборку значений полей(строк) в массив
while ($row = $queryRecords->Fetch()) {
    $data[] = $row;
}

$json_data = array(
    "draw" => intval( $params['draw'] ),
    "recordsTotal" => intval( $totalRecords ),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data
);

echo json_encode($json_data);

?>

