<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 29.05.2019
 * Time: 16:33
 */
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?php
function parse_excel_file( $filename ){
    // подключаем библиотеку
    require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

    $result = array();

    // получаем тип файла (xls, xlsx), чтобы правильно его обработать
    $file_type = PHPExcel_IOFactory::identify( $filename );
    // создаем объект для чтения
    $objReader = PHPExcel_IOFactory::createReader( $file_type );
    $objPHPExcel = $objReader->load( $filename ); // загружаем данные файла в объект
    $result = $objPHPExcel->getActiveSheet()->toArray(); // выгружаем данные из объекта в массив

    return $result;
}

?>
<table>
    <?php
    global $DB;
    $number = 0;
    $gost = '';
    $p = '';
    $last_spec = '';
    foreach (parse_excel_file("./OA.xlsx") as $items) {
        if((trim($items[1]) == '') && (trim($items[2]) == '') && (trim($items[3]) == '')) continue;
        if(($number !== $items[0]) && (trim($items[0]) !== '')) {
            $gost = $items[1];
            $gost_full = $gost;
        }elseif($items[1] == ''){
            $gost_full = $gost . " " . $p;
        } else {
            $p=$items[1];
            $gost_full = $gost . " " . $items[1];
        }

        if(trim($items[2]) === ''){
            $last_spec = $last_spec;
        }else{
            $last_spec = $items[2];
        }


        preg_match('/\(([0-9,\.]*)\-([0-9,\.]*)\)([\%а-яцьсА-ЯЦЬС\/0-9]*)/', $items[3], $output_array);

        echo"<tr>";
        echo "<td>".$gost_full."</td>";
        echo "<td>".$last_spec."</td>";
        if((trim($items[3]) !== '') && (trim($items[3]) != '-'))
            echo "<td> От ".$output_array[1]." до ".$output_array[2]." в ".$output_array[3]."</td>";
        elseif(trim($items[3]) === '-') {
            $output_array[1] = 0;
            $output_array[2] = 0;
            $output_array[3] = '-';
        }
        else
        {
            $output_array[1] = 0;
            $output_array[2] = 0;
            $output_array[3] = 'Заполните';
        }

//        $res = $DB->Query("INSERT INTO `ba_gost`
//        (GOST,
//        GOST_DOC,
//        GOST_VAL,
//        SPECIFICATION,
//        NORM1,
//        NORM2,
//        IN_OUT,
//        ED,PRICE,
//        COMMENT)
//        VALUES
//        ('".$gost_full."',
//        '',
//        '".trim($items[3])."',
//        '".$last_spec."',
//        '".$output_array[1]."',
//        '".$output_array[2]."',
//        1,
//        '".$output_array[3]."',
//        0,
//        '')");

    }
    ?>
</table>
