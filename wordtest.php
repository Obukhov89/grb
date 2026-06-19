<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpqrcode/qrlib.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';
use  NcJoes\OfficeConverter\OfficeConverter;

QRcode::png("https://niistrom.pro/check/index.php?NUMP=".$_GET['NUMP']."&DATE=".$_GET['DATE'], 'qrNEW.png');
$pathDoc = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive/'.$_GET['ID'].date("Y", strtotime($_GET['DATE'])).'/forsign.docx';
$pathFile = $_SERVER['DOCUMENT_ROOT'] .'/qrNEW.png';
$pathFile2 = $_SERVER['DOCUMENT_ROOT'] .'/sign.png';
$newTemplate = new \PhpOffice\PhpWord\Template($pathDoc);
$newTemplate->setImageValue('image2.png', $pathFile);
$newTemplate->setImageValue('image3.png', $pathFile2);
//$phpWord = new \PhpOffice\PhpWord\PhpWord();
//$document = $phpWord->loadTemplate($pathDoc);
$newTemplate->saveAs($_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive/'.$_GET['ID'].date("Y", strtotime($_GET['DATE'])).'/signed.docx'); // Сохранение документа

$outputFile = "Протокол №".$_GET['NUMP']." от ".date("d.m.Y", strtotime($_GET['DATE'])).".pdf";
$fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive/'.$_GET['ID'].date("Y", strtotime($_GET['DATE']))."/signed.docx";
$fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive/'.$_GET['ID'].date("Y", strtotime($_GET['DATE']))."/";
$converter  =  new  OfficeConverter($fullPathDocx, $fullPathPDF);//, $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/');
$converter -> convertTo($outputFile); // генерирует pdf файл в том же каталоге, что и test-file.docx


echo "OK";