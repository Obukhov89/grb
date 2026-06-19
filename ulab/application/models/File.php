<?php


class File extends Model
{
    public function createWordDoc($docTempalate, $dirName, $fields)
    {
//        $id = $fields["id"];
//
//        $docTempalate = new \PhpOffice\PhpWord\TemplateProcessor($docTempalate);
//        $docTempalate->setValues($fields);
//        $outputFile = "./{$dirName}/{$id}/" .  trim($fields["fio"]) . "№ " . $fields["id"] . ".docx";
//        $dir = $_SERVER["DOCUMENT_ROOT"] . "/ulab/{$dirName}/{$id}/";
//        if ( !is_dir($dir) ) {
//            $mkdirResult = mkdir($dir, 0766, true);
//        }
//        $docTempalate->saveAs($outputFile);
    }


    public function removeFile(string $dir, string $file): array
    {
        if ( empty($file) ) {
            return [
                'success' => false,
                'error' => [
                    'message' => "Не указан или указан не верно параметр удаляемого фала",
                ]
            ];
        }

        if ( !unlink($dir) ) {
            return [
                'success' => false,
                'error' => [
                    'message' => "Файл {$_POST['file']} не может быть удален из-за ошибки",
                ]
            ];
        } else {
            return [
                'success' => true
            ];
        }
    }

    /**
     * сохранить файл формата CSV
     * @param string $path
     * @param array $file
     * @param string $fileName
     * @return array
     */
    public function saveCsvFile(string $path, array $file, string $fileName): array
    {
        $supportedTypes = [
            'text/csv',
            'text/plain',
            'inode/x-empty',
        ];

        if (empty($path) || empty($file['name'])) {
            return [
                'success' => false,
                'error' => [
                    'message' => "Ошибка в параметрах при загрузке файла",
                ]
            ];
        }

        $type = mime_content_type($file['tmp_name']);

        if (!in_array($type, $supportedTypes, true) || pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
            return [
                'success' => false,
                'error' => [
                    'message' => "Ошибка формата CSV при загрузке файла: не csv формат или поврежден",
                ]
            ];
        }

        $uploaddir = UPLOAD_DIR . "/{$path}";

        return $this->saveFile($uploaddir, $fileName, $file['tmp_name']);
    }

    /**
     * сохранить файл формата PNG
     * @param string $path
     * @param array $file
     * @param string $fileName
     * @return array
     */
    public function savePNGFile(string $path, array $file, string $fileName): array
    {
        $supportedTypes = [
            'image/png',
            //'image/jpeg',
            //'application/pdf'
        ];

        if (empty($path) || empty($file['name'])) {
            return [
                'success' => false,
                'error' => [
                    'message' => "Ошибка в параметрах при загрузке файла",
                ]
            ];
        }

        $type = mime_content_type($file['tmp_name']);

        if (!in_array($type, $supportedTypes, true)) {
            return [
                'success' => false,
                'error' => [
                    'message' => "Ошибка формата PNG при загрузке файла: не png формат или поврежден",
                ]
            ];
        }

        $uploaddir = UPLOAD_DIR . "/{$path}";

        return $this->saveFile($uploaddir, $fileName, $file['tmp_name']);
    }

    /**
     * Создает CSV файл из переданных в массиве данных.
     * @param array  $createData   Массив данных из которых нужно созать CSV файл.
     * @param string $file         Путь до файла 'path/to/test.csv'. Если не указать, то просто вернет результат.
     * @param string $colDelimiter Разделитель колонок. Default: `;`.
     * @param string $rowDelimiter Разделитель рядов. Default: `\r\n`.
     * @return false|string CSV строку или false, если не удалось создать файл.
     */
    public function createCsvFile(array $createData, string $file = null, string $colDelimiter = ';', string $rowDelimiter = "\r\n" )
    {

        if(!is_array($createData)){
            return false;
        }

        if($file && !is_dir(dirname( $file ))){
            return false;
        }

        // строка, которая будет записана в csv файл
        $strCsv = '';

        // перебираем все данные
        foreach($createData as $row){
            $cols = [];

            foreach($row as $colVal) {
                // строки должны быть в кавычках ""
                // кавычки " внутри строк нужно предварить такой же кавычкой "
                //if($colVal && preg_match('/[",;\r\n]/', $colVal)){
                    // поправим перенос строки
                    //if( $rowDelimiter === "\r\n" ){
                    //    $colVal = str_replace(["\r\n", "\r"], ['\n', ''], $colVal);
                    //}
                    //elseif( $rowDelimiter === "\n" ){
                    //    $colVal = str_replace([ "\n", "\r\r"],'\r', $colVal);
                    //}

                    //$colVal = str_replace('"','""', $colVal); // предваряем "
                    //$colVal = '"'. $colVal .'"'; // обрамляем в "
                //}
                $colVal = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $colVal);
                $colVal = '"'. $colVal .'"'; // обрамляем в "

                $cols[] = $colVal; // добавляем колонку в данные
            }

            $strCsv .= implode($colDelimiter, $cols) . $rowDelimiter; // добавляем строку в данные
        }

        $strCsv = rtrim($strCsv, $rowDelimiter);

        // задаем кодировку windows-1251 для строки
        if($file){
            //$strCsv = iconv( "UTF-8", "cp1251",  $CSV_str );

            //создаем csv файл и записываем в него строку
            $done = file_put_contents($file, $strCsv);

            return $done ? $strCsv : false;
        }
        return $strCsv;
    }

    public function uploadFileServer($name, $tmpName, $folderPath): bool
    {
        $uploaddir = $_SERVER["DOCUMENT_ROOT"] . URI . "/upload" . $folderPath;

        if(!is_dir($uploaddir)) {
            mkdir($uploaddir, 0777, true);
        }

        $path = $uploaddir . $name;

        if (file_exists($path)) {
            return false;
        }

        return move_uploaded_file($tmpName, $path);
    }

    public function findChildFolder($folder, $filterArr)
    {
        return $folder->getChild(
            array(
                $filterArr,
                'TYPE' => \Bitrix\Disk\Internals\FolderTable::TYPE_FOLDER
            )
        );
    }

    public function findFactoryStorageFolder()
    {
        $driver = \Bitrix\Disk\Driver::getInstance();
        $storageID = "shared_files_s1";
        $folderCode = 185;
        $storage = $driver->getStorageByCommonId($storageID);

        return $this->findChildFolder($storage, ["ID" => $folderCode]);
    }
}