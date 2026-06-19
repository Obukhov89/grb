<?php

/**
 * Ядро - модель
 * Class Model
 */
class Model
{
    /** @var CDatabase $DB */
    public $DB;


    /**
     * @param $str
     * @return string
     */
    protected function quoteStr($str)
    {
        return "'{$str}'";
    }


    /**
     * Получает список имен полей таблицы БД
     * @param $tableName - название таблицы БД
     * @return array
     */
    protected function getColumnsByTable($tableName)
    {
        $dbName = $this->DB->DBName;

        $sql = $this->DB->Query(
            "SELECT `COLUMN_NAME` 
                FROM `INFORMATION_SCHEMA`.`COLUMNS` 
                WHERE `TABLE_SCHEMA`='{$dbName}' AND `TABLE_NAME`='{$tableName}'");

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row['COLUMN_NAME'];
        }

        return $result;
    }


    /**
     * @param $table
     * @param $data
     * @return array
     */
    protected function prepearTableData($table, $data)
    {
        $columns = $this->getColumnsByTable($table);

        $sqlData = [];

        foreach ($columns as $column) {
            if ( $column == 'id' || $column == 'ID' ) { continue; }
            if (isset($data[$column])) {
                $sqlData[$column] = $this->quoteStr($this->DB->ForSql(strip_tags($data[$column])));
            }
        }

        return $sqlData;
    }


    public function __construct()
    {
        global $DB;
        $this->DB = $DB;
    }


    /**
     * @param $path
     * @param $fileName
     * @return string
     */
    public function getBase64EncodeFile($path, $fileName)
    {
        $file = file_get_contents($path . $fileName);

        return base64_encode($file);
    }


    /**
     * @note получает имена файлов из директории
     * @param string $dir
     * @param array $skip
     * @return array
     */
    public function getFilesFromDir(string $dir, array $skip = [])
    {
        $result = [];

        $files = scandir($dir);
        $skipAll = array_merge($skip, ['.', '..']);
        foreach ($files as $file) {
            if (!in_array($file, $skipAll)) {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * @note получает имена файлов из директории
     * @param string $dir
     * @param array $skip
     * @param string $type
     * @return array
     */
    public function getFilesFromDirWithFileType(string $dir, array $skip = [], string $type = "pdf")
    {
        $result = [];

        $files = scandir($dir);
        $skipAll = array_merge($skip, ['.', '..']);
        foreach ($files as $file) {
            $currentFileArray = explode(".", $file);

            $currentFileType = $currentFileArray[count($currentFileArray) - 1];

            if($currentFileType != $type) {
                continue;
            }

            if (!in_array($file, $skipAll)) {
                $result[] = $file;
            }
        }

        return $result;
    }


    /**
     * @param $dir
     * @param $fileName
     * @param $tmpName
     * @return array
     */
    public function saveFile($dir, $fileName, $tmpName)
    {

        if ( !is_dir($dir) ) {
            $mkdirResult = mkdir($dir, 0766, true);

            if ( !$mkdirResult ) {
                return [
                    'success' => false,
                    'error' => "Ошибка! Не удалось создать папку. {$dir}",
                ];
            }
        }

        $uploadfile = $dir."/".$fileName;

        if (!move_uploaded_file($tmpName, $uploadfile) ) {
            return [
                'success' => false,
                'error' => "Ошибка! Не удалось загрузить файл на сервер! tpmName: {$tmpName}, uploadfile: {$uploadfile}",
            ];
        } else {
            return [
                'success' => true,
                'data' => $fileName,
                'upload' => $uploadfile
            ];
        }
    }

    public function checkTypeFile($file, $types)
	{
		if (!in_array($file, $types)) {
			return false;
		}

		return true;
	}

	public function byteFileToServer($byteFile, $localPath)
    {
        $bin = base64_decode($byteFile, true);
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . $localPath, $bin);
    }

	/**
	 * @param $text
	 * @param bool $exit
	 */
	public function pre($text, $exit = true) {
		if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
			echo '<pre>';
			print_r($text);
			if ($exit) {
				exit();
			}
		}
	}
}
