<?php
/**
 * Created by Makaralex92.
 * User: Алексей
 * Date: 15.04.2019
 * Time: 10:07
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/Classes/PhpWord/PhpWord.php");
use PhpOffice\PhpWord\PhpWord;
$PHPWord = new PhpWord();
$document = $PHPWord->loadTemplate('Protocol.doc'); //шаблон
$document->setValue('nProtocol', '777'); //номер договора
$document->save('Template_full.docx'); //имя заполненного шаблона для сохранения
