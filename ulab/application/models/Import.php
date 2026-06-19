<?php


/**
 * Модель для импорта данных в систему U-LAB
 * Class Import
 */
class Import extends Model
{
    /**
     * @param $data
     * @return array
     */
    public function prepareOnboarding($data)
    {
        $columns = $this->getColumnsByTable('ulab_onboarding');

        $sqlData = [];

        foreach ($columns as $column) {
            if ( isset($data[$column]) ) {
                $sqlData[$column] = $this->quoteStr($this->DB->ForSql(trim($data[$column])));
            }
        }

        $sqlData['description'] = $this->quoteStr(htmlentities($data['description'], ENT_QUOTES, 'UTF-8'));

        return $sqlData;
    }

    /**
     * @param string $name
     * @param string $delimiter
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getCsvData($name, $delimiter, $limit, $offset)
    {
        $nameModel = ucfirst($name);
        $path = MODULE_PATH . "Import/upload/{$name}.csv";

        $dataParse = $this->parseCsv($path, $delimiter, $limit, filesize($path), $offset);

        if (empty($dataParse['success'])) {
            return $dataParse;
        }

        $data = (new $nameModel)->prepareCsvData($dataParse['data']);

        return [
            'success' => true,
            'data' => $data
        ];
    }

    /**
     * удалить переносы и табы
     * @param array $matches
     * @return mixed|string|string[]
     */
    function deleteRN($matches = [])
    {
        if (isset($matches[1])) {
            return str_replace(["\r\n", "\r", "\n", "\t"], ' ', $matches[1]);
        } else {
            return $matches[0];
        }
    }

    /**
     * разобрать и получить данные csv файла
     * @param string $file - файл формата csv
     * @param string $delimiter - разделитель
     * @param int $limit - кол-во строк
     * @param int $size - длина самой длинной строки (установка в 0 или null приведёт к тому, что длина строки будет не ограничена.)
     * @param int|null $offset - количество игнорируемых строк с начала данных
     * @return array
     */
    public function parseCsv($file = '', $delimiter = ',', $limit = null, $size = 0, $offset = null)
    {
        // var_dump($file);
        // echo "<br>";
        // var_dump($delimiter);
        // echo "<br>";
        // var_dump($limit);
        // echo "<br>";
        // var_dump($size);
        // echo "<br>";
        // var_dump($offset);
        // die;
        $response = [];
        $supportedTypes = [
            'text/csv',
            'text/plain',
            'inode/x-empty',
        ];
        $i = 0;

        if (!file_exists($file) || !is_readable($file)) {
            return [];
        }

        if (!in_array(mime_content_type($file), $supportedTypes, true) || pathinfo($file, PATHINFO_EXTENSION) !== 'csv') {
            return [
                'success' => false,
                'error' => [
                    'message' => "Ошибка формата CSV при получении данных: не csv формат или поврежден",
                ]
            ];
        }

        //Задаем кодировку UTF-8
        $content = trim(file_get_contents($file));
        $encodedContent = mb_convert_encoding($content, 'UTF-8', $this->getEncoding($content));

        file_put_contents($file, $encodedContent);
        unset($content);


        $fileToRead = fopen("{$file}", 'r');

        if ($fileToRead === FALSE) {
            return [
                'success' => false,
                'error' => [
                    'message' => 'Ошибка при получении данных! Невозможно прочитать файл.',
                ]
            ];
        }


        if (!empty($limit) && $limit > 0) {
            while (!feof($fileToRead) && $i < $limit + $offset) {
                $response[] = fgetcsv($fileToRead, $size, $delimiter);
                $i++;
            }
        } else {
            while (!feof($fileToRead)) {
                $response[] = fgetcsv($fileToRead, $size, $delimiter);
            }
        }
        fclose($fileToRead);

        //Убирать заголовок (количество игнорируемых строк с начала данных)
        if ($offset !== null && $offset > 0) {
            $response = array_slice($response, $offset, null, true);
        }

        return [
            'success' => true,
            'data' => $response
        ];
    }

    /**
     * разобрать и получить данные csv файла
     * @param string $file - файл формата csv
     * @param string $delimiter - разделитель
     * @param int $limit - кол-во строк
     * @param int $size - длина самой длинной строки (установка в 0 или null приведёт к тому, что длина строки будет не ограничена.)
     * @param int|null $offset - количество игнорируемых строк с начала данных
     * @return array
     */
    public function parseCsvOld($file = '', $delimiter = ',', $limit = null, $size = 0, $offset = null)
    {
        $response = [];
        $supportedTypes = [
            'text/csv',
            'text/plain',
            'inode/x-empty',
        ];

        if (!file_exists($file) || !is_readable($file)) {
            return [];
        }

        if (!in_array(mime_content_type($file), $supportedTypes, true) || pathinfo($file, PATHINFO_EXTENSION) !== 'csv') {
            return [
                'success' => false,
                'error' => [
                    'message' => "Ошибка формата CSV при получении данных: не csv формат или поврежден",
                ]
            ];
        }

        //Задаем кодировку UTF-8
        $content = trim(file_get_contents($file));
        $encodedContent = mb_convert_encoding($content, 'UTF-8', $this->getEncoding($content));

        file_put_contents($file, $encodedContent);
        unset($content);


        $fileToRead = fopen("{$file}", 'r');

        if ($fileToRead === FALSE) {
            return [
                'success' => false,
                'error' => [
                    'message' => 'Ошибка при получении данных! Невозможно прочитать файл.',
                ]
            ];
        }

        while (!feof($fileToRead)) {
            $response[] = fgetcsv($fileToRead, $size, $delimiter);
        }
        fclose($fileToRead);

        //Убирать заголовок (количество игнорируемых строк с начала данных)
        if ($offset !== null && $offset > 0) {
            $response = array_slice($response, $offset, $limit, true);
        }

        return [
            'success' => true,
            'data' => $response
        ];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function importCsvData($name)
    {
        if (empty($name)) {
            return false;
        }

        $nameModel = ucfirst($name);
        $path = MODULE_PATH . "Import/upload/{$name}.csv";  //здесь читался файл из неправильной директории!

        $model = new $nameModel();
        $dataParse = $this->parseCsv($path, ';', null, filesize($path), 1);
        $dataPrepared = $model->prepareCsvData($dataParse['data']);
        $model->importData($dataPrepared);
        return true;
    }

    /**
     * @return array
     */
    public function getOnboardings(): array
    {
        $result = [];

        $onboarding = $this->DB->Query("select * from ulab_onboarding");

        while ( $row = $onboarding->Fetch() ) {
            $row['description'] = html_entity_decode($row['description'], ENT_QUOTES, 'UTF-8');
            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param int $onboardingId
     * @return array
     */
    public function getOnboardingById(int $onboardingId): array
    {
        $result = [];

        if ( empty($onboardingId) ) {
            return $result;
        }

        $onboarding = $this->DB->Query("select * from ulab_onboarding where id = {$onboardingId}")->Fetch();

        if ( !empty($onboarding) ) {
            $onboarding['description'] = html_entity_decode($onboarding['description'], ENT_QUOTES, 'UTF-8');
            $result = $onboarding;
        }

        return $result;
    }

    /**
     * @param $data
     * @return false|mixed|string
     */
    public function addOnboarding($data)
    {
        $sqlData = $this->prepareOnboarding($data);

        return $this->DB->Insert('ulab_onboarding', $sqlData);
    }

    /**
     * @param $id
     * @param $data
     * @return false|mixed|string
     */
    public function updateOnboarding($id, $data)
    {
        $sqlData = $this->prepareOnboarding($data);

        $where = "WHERE id = {$id}";
        return $this->DB->Update('ulab_onboarding', $sqlData, $where);
    }

    /**
     * @param int $id
     */
    public function deleteOnboarding($id)
    {
        $this->DB->Query("DELETE FROM ulab_onboarding WHERE id = {$id}");
    }

    //Определяем кодировку входящего файла с помощью iconv (Utf-8 или 1251?)
    public function getEncoding($str){
        $cp_list = array('utf-8', 'windows-1251');
        foreach ($cp_list as $k=>$codepage){
            if (md5($str) === md5(iconv($codepage, $codepage, $str))){
                return $codepage;
            }
        }
        return null;
    }

    public function deleteAllTnVeds() {
        $this->DB->Query("DELETE FROM classifier_tnved");
        $this->DB->Query("ALTER TABLE classifier_tnved AUTO_INCREMENT=0");
    }

    public function insertTnVed(array $tnved) {
        $this->DB->Query("INSERT INTO classifier_tnved
                        (section, `group`, position, sub_position, fullcode, name, note, short_name, date_from, date_to, parent_id) VALUES (" .
                        ($tnved['section'] ? "'" . $tnved['section']. "'" : "NULL") . ", " .
                        ($tnved['group'] ? "'" . $tnved['group']. "'" : "NULL") . ", " .
                        ($tnved['position'] ? "'" . $tnved['position']. "'" : "NULL") . ", " .
                        ($tnved['sub_position'] ? "'" . $tnved['sub_position']. "'" : "NULL") . ", " .
                        "'" . $tnved['fullcode'] . "', " .
                        "'" . $tnved['name'] . "', " .
                        ($tnved['note'] ? "'" . $tnved['note']. "'" : "''") . ", " .
                        ($tnved['short_name'] ? "'" . $tnved['short_name']. "'" : "NULL") . ", " .
                        "'" . $tnved['date_from'] . "', " .
                        ($tnved['date_to'] ? "'" . $tnved['date_to']. "'" : "NULL") . ", " .
                        ($tnved['parent_id'] ? "'" . $tnved['parent_id']. "'" : "NULL") .
                        ")");

        return "Section complete";
    }

    public function getTnVedParent(array $params) {
        $query = "SELECT * FROM classifier_tnved WHERE ";

        switch($params['type']) {
            case 1:
                $query .= "section = '{$params['parent_num']}' AND `group` IS NULL "; //Получаем родительский элемент для группы
                break;
            case 2:
                $query .= "`group` = '{$params['parent_num']}' AND position IS NULL AND section IS NOT NULL "; //Получаем родительский элемент для позиции
            break;
            case 3:
                $query .= "`group` = '{$params['parent_grp']}' AND position = '{$params['parent_num']}' AND sub_position IS NULL "; //Получаем родительский элемент для субпозиции
            break;
        }

        $query1 = $query . " AND date_from='{$params['date']}' LIMIT 1";
        $query2 = $query . " ORDER BY date_from DESC LIMIT 1";
        
        $q = $this->DB->Query($query1)->Fetch();

        if (!$q) {
            return $this->DB->Query($query2)->Fetch();
        }
        return $q;
    }

    public function getRoomOborudMethods($roomId)
    {
        $result = [];

        $responseMethods = $this->DB->Query(
            "
            SELECT um.id, um.name, ug.reg_doc, ug.year, um.clause
            FROM ulab_methods_room umr
            LEFT JOIN ulab_methods um ON um.id = umr.method_id
            LEFT JOIN ulab_gost ug ON ug.id = um.gost_id
            WHERE umr.room_id = '{$roomId}'
            "
        );

        $responseOborud = $this->DB->Query(
            "
            SELECT ba.ID, ba.OBJECT
            FROM ba_oborud ba
            WHERE ba.roomnumber = '{$roomId}' 
            "
        );

        $numberRoom = $this->DB->Query("SELECT NUMBER FROM ROOMS WHERE ID = '{$roomId}'")->Fetch()['NUMBER'];

        while($row = $responseMethods->Fetch()) {
            $strYear = !empty($row['year']) ? "-{$row['year']}" : '';
            $strClause = !empty($row['clause']) ? " {$row['clause']}" : '';

            $viewGost = "{$row['reg_doc']}{$strYear}{$strClause} | {$row['name']}";

            $result['methods'][] = [
                'id' => $row['id'],
                'name' => $viewGost
            ];
        }

        while($row = $responseOborud->Fetch()) {
            $result['oboruds'][] = $row;
        }

        $result['room'] = $numberRoom;

        return $result;
    }
}
