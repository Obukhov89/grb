<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
//Библиатека для конвертации в PDF
require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';

use  NcJoes\OfficeConverter\OfficeConverter;

$resultSelectBaTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `DATE_ACT`>'2020-12-31'");

while ($resultBaTz = $resultSelectBaTz->Fetch()) {

    $typeTz = isset($resultBaTz['TYPE_TZ']) ? $resultBaTz['TYPE_TZ'] : ''; //Тип протокола
    $numP = !empty($resultBaTz['NUM_P']) ? $resultBaTz['NUM_P'] : ''; //Номер протокола
    $idTz = !empty($resultBaTz['ID']) ? $resultBaTz['ID'] : '';
    $dateAct = !empty($resultBaTz['DATE_ACT']) ? date("Y", strtotime($resultBaTz['DATE_ACT'])) : '';
    $actualVer = !empty($resultBaTz['ACTUAL_VER']) ? $resultBaTz['ACTUAL_VER'] : '';
    $directory = !empty($idTz) && !empty($dateAct) ? $idTz . $dateAct : '';

    //Если протокол не упрощенный или не выдан в не ЛИС, есть актуальная версия,номер протокола и наименования директории для протокола то формируем pdf
    if (isset($typeTz) && !in_array($typeTz, [33, 34]) && empty($ba_tz['NO_BITRIX']) && !empty($actualVer) && !empty($numP) && $numP != 'номер не присвоен' && !empty($directory)) {

        //Начальный путь файла
        $initialPath = 'protocol_generator/archive/' . $directory . '/' . $actualVer . '.docx';

        //Промежуточный путь
        $interimPath = 'protocol_generator/interim_archive_prot/' . $directory . '/Protocol_' . $numP . '.docx';

        $outputFile = 'Протокол_' . $numP . '.pdf';
        $outputPath = 'protocol_generator/archive/' . $directory;

        //проверяем есть ли файл для конвертации и отсутствие pdf файла
        if (!empty($initialPath) && !empty($interimPath) && file_exists($initialPath) && !current(glob('./protocol_generator/archive/' . $directory . '/*.pdf'))) {

            //Если нет промежуточной дериктории, то создаем
            if (!is_dir('protocol_generator/interim_archive_prot/' . $directory)) {
                mkdir('protocol_generator/interim_archive_prot/' . $directory, 0777, true);
            }

            //Копируем в промежуточную дериктории и если скопировали, то конвертируем файл в pdf
            if (copy($initialPath, $interimPath)) {

                $fullPathDocx = $_SERVER['DOCUMENT_ROOT'] . '/' . $interimPath;
                $fullPathPDF = $_SERVER['DOCUMENT_ROOT'] . '/' . $outputPath;

                $converter = new  OfficeConverter($fullPathDocx, $fullPathPDF);
                $resultConvertPdf = $converter->convertTo($outputFile); // генерирует pdf файл

            }
        }

    }
}

?>