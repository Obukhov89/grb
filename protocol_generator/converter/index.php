<?php
/*ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/
 //echo dirname(dirname(__FILE__)) . '/converter/vendor/autoload.php';die;
require dirname(dirname(__FILE__)) . '/converter/vendor/autoload.php';


// если вы используете composer, просто используйте это 
use  NcJoes\OfficeConverter\OfficeConverter;

/*$converter  =  new  OfficeConverter ( 'test-file.docx' ); 
$converter -> convertTo ( 'output-file.pdf' ); // генерирует pdf файл в том же каталоге, что и test-file.docx 
$converter -> convertTo ( 'output-file.html' ); // генерирует HTML-файл в той же директории, что и test-file.docx

// чтобы указать выходной каталог, укажите его как второй аргумент для конструктора 
$converter  =  new  OfficeConverter ( 'test-file.docx' , ' ./' );*/


?>