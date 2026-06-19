<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 26.04.2019
 * Time: 9:38
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Field;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\SimpleType\TblWidth;

global $DB;
global $APPLICATION;
$APPLICATION->RestartBuffer();

$lab = [
	'1' => 'ЛФХИ',
	'2' => 'ЛСМ',
	'3' => 'ЛФМИ',
	'4' => 'ДСЛ',
	'5' => 'ОСК',
	'6' => 'Испытательный центр "УралНИИстром", г. Челябинск, ул. Сталеваров, д.5 к2',
	'7' => 'Отдел СМК и метрологии'
];

$head = [
	'1' => 'Овчинникова Т.А.',
	'2' => 'Живцова Р.А.',
	'3' => 'Шингарев В.В.',
	'4' => 'Велютич Т.В.',
	'5' => 'Шишкина А.С.',
	'6' => 'Испытательный центр "УралНИИстром", г. Челябинск, ул. Сталеваров, д.5 к2',
	'7' => 'Апанович Е.Э.'
];

$date = date('d.m.Y');

$data = [];
$i = 1;
$res = $DB->Query("SELECT * FROM `ba_oborud` 
					WHERE `roomnumber` != '9' 
						AND `roomnumber` != '10' 
						AND `roomnumber` != '11'
						AND `roomnumber` != '14'
						AND `REG_NUM` NOT LIKE '%В%'
						AND `LONG_STORAGE` = '0' 
						AND `SPISANIE` = ''
						AND (`DATE_SP` = '' OR `DATE_SP` IS NULL)
					ORDER BY `place_of_installation_or_storage`");
while ($row = $res->Fetch()) {
	$poverka = strtotime($row['POVERKA']) - time();
	if ($row['NO_METR_CONTROL'] == 0 && $poverka <= 0) {
		continue;
	}
	$name = !empty($row['TYPE_OBORUD']) ? trim($row['OBJECT']) . ', ' . $row['TYPE_OBORUD'] : trim($row['OBJECT']);
	$laba = $lab[$row['place_of_installation_or_storage']];
	$assigned = $head[$row['place_of_installation_or_storage']];
	$data[$row['place_of_installation_or_storage']][] = [
		'num' => $i,
		'name' => $name,
		'factory_number' => $row['FACTORY_NUMBER'],
		'reg_num' => $row['REG_NUM'],
	];
	$i++;
}

$template = $_SERVER['DOCUMENT_ROOT'] . '/inv_list.docx';

$phpWord = new \PhpOffice\PhpWord\PhpWord();
$template_document = new \PhpOffice\PhpWord\TemplateProcessor($template);

$template_document->setValue('date', $date);

$styleTable = array('borderSize' => 10, 'borderColor' => '999999', 'unit' => TblWidth::TWIP);
$cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
$cellRowContinue = array('vMerge' => 'continue');
$cellColSpan2 = array('gridSpan' => 2, 'valign' => 'center');
$cellColSpan3 = array('gridSpan' => 3, 'valign' => 'center');
$cellColSpan4 = array('gridSpan' => 4, 'valign' => 'center');

$cellHCentered = array('align' => 'center');
$cellVCentered = array('valign' => 'center');

$table = new Table($styleTable);
$table->addRow(null, array('tblHeader' => true));
$table->addCell(500, $cellVCentered)->addText('№ п/п', array('bold' => true), $cellHCentered);
$table->addCell(6500, $cellVCentered)->addText('Наименование', array('bold' => true), $cellHCentered);
$table->addCell(1500, $cellVCentered)->addText('Зав. №', array('bold' => true), $cellHCentered);
$table->addCell(2000, $cellVCentered)->addText('Инв.№', array('bold' => true), $cellHCentered);
foreach ($data as $k => $item) {
	$table->addRow();
	$table->addCell(null, $cellColSpan4)->addText($lab[$k], array('bold' => true), $cellHCentered);
	foreach ($item as $elem) {
		$table->addRow();
		$table->addCell(null, $cellVCentered)->addText($elem['num'], array('size'=>11), $cellHCentered);
		$table->addCell(null, $cellVCentered)->addText($elem['name'], array('size'=>11));
		$table->addCell(null, $cellVCentered)->addText(!empty($elem['factory_number']) ? $elem['factory_number'] : 'б/н', array('size'=>11), $cellHCentered);
		$table->addCell(null, $cellVCentered)->addText(!empty($elem['reg_num']) ? $elem['reg_num'] : 'б/н' , array('size'=>11), $cellHCentered);
	}
	$table->addRow();
	$table->addCell();
	$table->addCell(null, $cellVCentered)->addText('Материально ответственное лицо: Руководитель ' . $lab[$k] . '<w:br/><w:br/>' . $head[$k], array('bold'=>true, 'size'=>11));
	$table->addCell();
	$table->addCell();
}
$template_document->setComplexBlock('table', $table);

$filename = 'Инвентарная ведомость от ' . $date .'.docx';

$file = $_SERVER['DOCUMENT_ROOT'] . '/' . $filename;
$template_document->saveAs($file);
//copy($file, $newfile);
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($file);

