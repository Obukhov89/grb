<?php

class Transport extends Model {

    /**
     * @param array $data
     * @param string $table
     * @return int
     */
    public function prepareData($arr)
    {
        foreach ($arr as $index => $item) {
            if (gettype($item) === "string" && $item !== "NULL") {
                $arr[$index] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        return $arr;
    }

    public function create(array $data, string $table): int
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $result = $this->DB->Insert($table, $data);

        return intval($result);
    }

    /**
     * @param array $data
     * @param string $table
     * @param int $id
     */
    public function update(array $data, string $table, int $id)
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $where = "WHERE ID = {$id}";
        return $this->DB->Update($table, $data, $where);
    }

    public function getTransportList()
    {
        $response = [];

        $result = $this->DB->Query("
            SELECT v.*, f_t.title, f_t.price FROM transport AS v
            LEFT JOIN fuel_types AS f_t ON v.fuel_id = f_t.id 
            WHERE v.del != 1
        ");

        while ($row = $result->fetch()) {
            $response[$row["id"]] = $row;
        }

        return $response;
    }

    public function getTransportById($id)
    {
        $result = $this->DB->Query("
            SELECT v.*, f_t.title, f_t.price FROM transport AS v
            LEFT JOIN fuel_types AS f_t ON v.fuel_id = f_t.id 
            WHERE v.id = {$id}
        ");

        return $result->fetch();
    }

    public function getFuelTypes()
    {
        $response = [];

        $result = $this->DB->Query("
            SELECT * FROM fuel_types
        ");

        while ($row = $result->fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    public function getTransportListToJournal($filter)
    {
        global $DB;

        $where = "del != 1 AND ";
        $limit = "";
        $order = [
            'by' => 'v.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // Модель
            if (!empty($filter['search'])) {
                if (isset($filter['search']['model'])) {
                    $where .= "v.model LIKE '%{$filter['search']['model']}%' AND ";
                }
                // Номер
                if (isset($filter['search']['number'])) {
                    $where .= "v.number LIKE '%{$filter['search']['number']}%' AND ";
                }
                // Владелец
                if (isset($filter['search']['owner_name'])) {
                    $where .= "v.owner_name LIKE '%{$filter['search']['owner_name']}%' AND ";
                }
                // Название топлива
                if (isset($filter['search']['fuel_title'])) {
                    $where .= "f_t.title LIKE '%{$filter['search']['fuel_title']}%' AND ";
                }
                // Расход топлива
                if (isset($filter['search']['consumption_rate'])) {
                    $where .= "v.consumption_rate LIKE '%{$filter['search']['consumption_rate']}%' AND ";
                }
            }

            // работа с сортировкой
            if (!empty($filter['order'])) {
                if ($filter['order']['dir'] === 'asc') {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'model':
                        $order['by'] = "v.model";
                        break;
                    case 'number':
                        $order['by'] = "v.number";
                        break;
                    case 'owner_name':
                        $order['by'] = "v.owner_name";
                        break;
                    case 'fuel_title':
                        $order['by'] = "f_t.title";
                        break;
                    case 'consumption_rate':
                        $order['by'] = "v.consumption_rate";
                        break;
                }
            }
        }

        $where .= "1 ";

        $result = [];

        $data = $DB->Query("
            SELECT v.*, f_t.title AS fuel_title, f_t.price AS fuel_price FROM transport AS v
            LEFT JOIN fuel_types AS f_t ON v.fuel_id = f_t.id 
            WHERE {$where}
            ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query("
            SELECT v.id AS val FROM transport AS v
            LEFT JOIN fuel_types AS f_t ON v.fuel_id = f_t.id WHERE del != 1"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT v.id AS val FROM transport AS v
             LEFT JOIN fuel_types AS f_t ON v.fuel_id = f_t.id
             WHERE {$where}"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    public function getFuelListToJournal($filter)
    {
        global $DB;

        $where = "";
        $limit = "";
        $order = [
            'by' => 'id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // Модель
            if (!empty($filter['search'])) {
                // Название
                if (isset($filter['search']['title'])) {
                    $where .= "title LIKE '%{$filter['search']['title']}%' AND ";
                }
                // Цена
                if (isset($filter['search']['price'])) {
                    $where .= "price LIKE '%{$filter['search']['price']}%' AND ";
                }
            }

            // работа с сортировкой
            if (!empty($filter['order'])) {
                if ($filter['order']['dir'] === 'asc') {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'id':
                        $order['by'] = "id";
                        break;
                    case 'title':
                        $order['by'] = "title";
                        break;
                    case 'price':
                        $order['by'] = "price";
                        break;

                }
            }
        }

        $where .= "1 ";

        $result = [];

        $data = $DB->Query("
            SELECT * FROM fuel_types 
            WHERE {$where}
            ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query("
            SELECT * FROM fuel_types"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT * FROM fuel_types
             WHERE {$where}"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    public function parseFuelPrice($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');

        curl_setopt($ch, CURLOPT_BUFFERSIZE, MAX_FILE_SIZE);

        $header = array(
            'Accept: text/html', // Prefer HTML format
            'Accept-Charset: utf-8', // Prefer UTF-8 encoding
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $doc = curl_exec($ch);

        curl_close($ch);

        $str = htmlspecialchars($doc);

        preg_match_all('/fuel-box__price svelte-13wxv0z(.*?)p/', $str, $match);

        $fuelArr = [];

        $fuelArr[1] = preg_replace("/[^,.0-9]/", '', $match[1][2]);
        $fuelArr[2] = preg_replace("/[^,.0-9]/", '', $match[1][3]);
        $fuelArr[3] = preg_replace("/[^,.0-9]/", '', $match[1][4]);
        $fuelArr[4] = preg_replace("/[^,.0-9]/", '', $match[1][5]);
        $fuelArr[5] = preg_replace("/[^,.0-9]/", '', $match[1][6]);
        $fuelArr[6] = preg_replace("/[^,.0-9]/", '', $match[1][1]);
        $fuelArr[7] = preg_replace("/[^,.0-9]/", '', $match[1][0]);

        return $fuelArr;

    }

    public function getReposrtListToJournal($filter)
    {
        global $DB;

        $where = "t_r.del != 1 AND ";
        $limit = "";
        $order = [
            'by' => 'id',
            'dir' => 'DESC'
        ];


        if (!empty($filter)) {
            // Модель
            if (!empty($filter['search'])) {
                if (isset($filter['search']['date'])) {
                    $where .= "date LIKE '%{$filter['search']['date']}%' AND ";
                }

                if (isset($filter['search']['fio'])) {
                    $where .= "b_u.LAST_NAME LIKE '%{$filter['search']['fio']}%' AND ";
                }

                if (isset($filter['search']['transport_model'])) {
                    $where .= "t.model LIKE '%{$filter['search']['transport_model']}%' AND ";
                }

                if (isset($filter['search']['time_start'])) {
                    $where .= "time_start LIKE '%{$filter['search']['time_start']}%' AND ";
                }

                if (isset($filter['search']['time_end'])) {
                    $where .= "time_end LIKE '%{$filter['search']['time_end']}%' AND ";
                }

                if (isset($filter['search']['km'])) {
                    $where .= "t_r.km LIKE '%{$filter['search']['km']}%' AND ";
                }

                if (isset($filter['search']['gsm'])) {
                    $where .= "t_r.gsm LIKE '%{$filter['search']['gsm']}%' AND ";
                }

                if (isset($filter['search']['price'])) {
                    $where .= "t_r.price LIKE '%{$filter['search']['price']}%' AND ";
                }

                if (isset($filter['search']['full_sum'])) {
                    $where .= "ROUND(t_r.gsm * t_r.price, 2) LIKE '%{$filter['search']['full_sum']}%' AND ";
                }

                if (isset($filter['search']['date_start'])) {
                    $where .= "t_r.date >= '{$filter['search']['date_start']}' AND ";
                }

                if (isset($filter['search']['date_end'])) {
                    $where .= "t_r.date <= '{$filter['search']['date_end']}' AND ";
                }
            }

            // работа с сортировкой
            if (!empty($filter['order'])) {
                if ($filter['order']['dir'] === 'asc') {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'date':
                        $order['by'] = "date";
                        break;
                    case 'fio':
                        $order['by'] = "b_u.LAST_NAME";
                        break;
                    case 'transport_model':
                        $order['by'] = "t.model";
                        break;
                    case 'time_start':
                        $order['by'] = "t_r.time_start";
                        break;
                    case 'time_end':
                        $order['by'] = "t_r.time_end";
                        break;
                    case 'km':
                        $order['by'] = "t_r.km";
                        break;
                    case 'gsm':
                        $order['by'] = "t_r.gsm";
                        break;
                    case 'price':
                        $order['by'] = "t_r.price";
                        break;
                    case 'full_sum':
                        $order['by'] = "ROUND(t_r.gsm * t_r.price, 2)";
                        break;

                }
            }

            // работа с пагинацией
            if (isset($filter['paginate'])) {
                $offset = 0;
                // количество строк на страницу
                if (isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0) {
                    $length = $filter['paginate']['length'];

                    if (isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT {$offset}, {$length}";
                }
            }
        }

        if (isset($filter["manager"])) {
            $useridList = $filter["manager"];
            $where .= "t_r.user_id IN ({$useridList}) AND ";
        }

        $where .= "1 ";

        $result = [];

        $data = $DB->Query("
            SELECT t_r.id, t_r.user_id, t_r.transport_id, 
                   CONCAT(b_u.LAST_NAME, ' ', UPPER(SUBSTRING(b_u.NAME,1,1)), '.') AS fio,
                   t.model AS transport_model, t.number AS transport_number
            FROM transport_report AS t_r
            LEFT JOIN transport AS t ON t.id = t_r.transport_id 
            LEFT JOIN b_user AS b_u ON b_u.ID = t_r.user_id 
            WHERE {$where}
            ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query("
            SELECT t_r.*, CONCAT(b_u.LAST_NAME, ' ', UPPER(SUBSTRING(b_u.NAME,1,1)), '.', UPPER(SUBSTRING(b_u.SECOND_NAME,1,1)) ,'.') AS fio
            FROM transport_report AS t_r
            LEFT JOIN b_user AS b_u ON b_u.ID = t_r.user_id  WHERE del != 1"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT t_r.*, CONCAT(b_u.LAST_NAME, ' ', UPPER(SUBSTRING(b_u.NAME,1,1)), '.', UPPER(SUBSTRING(b_u.SECOND_NAME,1,1)) ,'.') AS fio
             FROM transport_report AS t_r
             LEFT JOIN b_user AS b_u ON b_u.ID = t_r.user_id
             WHERE {$where}"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    public function getReportTable($reportId)
    {

        global $DB;

        $result = [];

        $stmt = $DB->Query("
            SELECT trr.*,
                 DATE_FORMAT(trr.date, '%d.%m.%Y') AS date_str,
                 DATE_FORMAT(trr.time_start, '%H:%i') AS time_start,
                 DATE_FORMAT(trr.time_end, '%H:%i') AS time_end,
                 op.name AS project_name 
            FROM transport_report_row AS trr
            LEFT JOIN osk_project AS op ON op.id = trr.project_id
            WHERE trr.report_id = {$reportId} AND trr.del != 1
            ORDER BY date ASC"
        );

        while ($row = $stmt->fetch()) {
            $row["sum"] = round($row["price"] * $row["gsm"], 2);
            $result[] = $row;
        }

        return $result;
    }

    public function getCheckTable($reportId)
    {

        global $DB;

        $result = [];

        $stmt = $DB->Query("
            SELECT *,
                 DATE_FORMAT(date, '%d.%m.%Y') AS date_str
            FROM transport_report_check
            WHERE report_id = {$reportId} AND del != 1
            ORDER BY date ASC"
        );

        while ($row = $stmt->fetch()) {
           // $row["sum"] = round($row["price"] * $row["gsm"], 2);
            $result[] = $row;
        }

        return $result;
    }

    public function getDataByReportId($id)
    {
        global $DB;

        $result = [];

        $stmt = $DB->Query("
            SELECT t_r.*, t.model, t.number, t.consumption_rate,
                   DATE_FORMAT(t_r.secondment_date_start, '%d.%m.%Y') AS date_start_str,
                   DATE_FORMAT(t_r.secondment_date_end, '%d.%m.%Y') AS date_end_str,
                   CONCAT(b_u.LAST_NAME, ' ', UPPER(SUBSTRING(b_u.NAME,1,1)), '.', UPPER(SUBSTRING(b_u.SECOND_NAME,1,1))) AS fio, t_r.date AS report_date 
            FROM transport_report AS t_r 
            LEFT JOIN transport AS t ON t.id = t_r.transport_id 
            LEFT JOIN b_user AS b_u ON b_u.ID = t_r.user_id 
            WHERE t_r.id = {$id}"
        );

        return $stmt->fetch();
    }


    public function generateMemoDoc($fields)
    {
//        $fullPathDocx = $_SERVER['DOCUMENT_ROOT'] . "/ulab/upload/transport/temp.docx";
//        $fullPathPDF = $_SERVER['DOCUMENT_ROOT'] ."/ulab/upload/transport/test.pdf";
//
//        $converter  =  new NcJoes\OfficeConverter\OfficeConverter($fullPathDocx, $fullPathPDF);
//        $converter -> convertTo("test.pdf");
        $id = $fields["id"];

        $fileArr = $this->getFilesFromDir($_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/memo/{$id}/");

        if (!empty($fileArr)) {
            foreach ($fileArr as $fileName) {
                unlink($_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/memo/{$id}/" . $fileName);
            }
        }

        $docTempalate = new \PhpOffice\PhpWord\TemplateProcessor('./upload/docTemplates/transport/memo.docx');
        $docTempalate->setValues($fields);

      //  $outputFile = "./upload/transport/memo/{$id}/" .  trim($fields["fio"]) . "№ " . $fields["id"] ." от " . $fields["date"] .".docx";
        $fileName = "Служебная записка " . $id . ".docx";
        $outputFile = "./upload/transport/memo/{$id}/" . $fileName;
        $dir = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/memo/{$id}/";
        if ( !is_dir($dir) ) {
            $mkdirResult = mkdir($dir, 0766, true);
        }
        $docTempalate->saveAs($outputFile);

//        $fullPathDocx = "/home/bitrix/www/ulab/upload/transport/memo/1/123.docx";
//        $fullPathPDF = $_SERVER['DOCUMENT_ROOT'] ."/ulab/upload/transport/tes.pdf";
//        $converter  =  new NcJoes\OfficeConverter\OfficeConverter($fullPathDocx, $fullPathPDF);
//        $converter -> convertTo("test.pdf");

        return $fileName;
     //   $href = "/ulab/upload/transport/memo/{$id}/" . trim($fields["fio"]) . "№ " . $fields["id"] ." от " . $fields["date"] .".docx";


    }

    public function generateReportDoc($fields)
    {
        $id = $fields["id"];
        $tableArr = $fields["tableArr"];

        unset($fields["tableArr"]);

        $fileArr = $this->getFilesFromDir($_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/report/{$id}/");

        if (!empty($fileArr)) {
            foreach ($fileArr as $fileName) {
                unlink($_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/report/{$id}/" . $fileName);
            }
        }

        $docTempalate = new \PhpOffice\PhpWord\TemplateProcessor('./upload/docTemplates/transport/report.docx');
        $docTempalate->setValues($fields);
        $fileName = "Отчет " . $id . ".docx";
        $outputFile = "./upload/transport/report/{$id}/" . $fileName;
        $dir = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/report/{$id}/";
        if ( !is_dir($dir) ) {
            $mkdirResult = mkdir($dir, 0766, true);
        }

        $docTempalate->cloneRowAndSetValues('n', $tableArr);

        $docTempalate->saveAs($outputFile);

        return $fileName;
    }

    public function generateCompensationDoc($fields)
    {
        $id = $fields["id"];

        $fileArr = $this->getFilesFromDir($_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/compensation/{$id}/");

        if (!empty($fileArr)) {
            foreach ($fileArr as $fileName) {
                unlink($_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/compensation/{$id}/" . $fileName);
            }
        }

        $docTempalate = new \PhpOffice\PhpWord\TemplateProcessor('./upload/docTemplates/transport/compensation.docx');
        $docTempalate->setValues($fields);
        $fileName = "Компенсация " . $id . ".docx";
        $outputFile = "./upload/transport/compensation/{$id}/" . $fileName;
        $dir = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/compensation/{$id}/";
        if ( !is_dir($dir) ) {
            $mkdirResult = mkdir($dir, 0766, true);
        }

        $docTempalate->saveAs($outputFile);

        return $fileName;
    }

    public function  getReportProjectById($projectId)
    {
        $result = [];

        $stmt = $this->DB->Query("
                    SELECT s.*, DATE_FORMAT(s.date_end, '%d.%m.%Y') AS date_end,
                           CONCAT(b_u.LAST_NAME, ' ', LEFT(b_u.NAME, 1), ' ', LEFT(b_u.SECOND_NAME, 1)) fio    
                    FROM secondment AS s 
                    LEFT JOIN b_user AS b_u ON s.user_id = b_u.ID
                    WHERE project_id = {$projectId}
        ");

        while ($row = $stmt->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function generateRouteDoc($fields)
    {
        $id = $fields["id"];
        $tableArr = $fields["table_arr"];

        unset($fields["table_arr"]);

        $fileArr = $this->getFilesFromDir($_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/route/{$id}/");

        if (!empty($fileArr)) {
            foreach ($fileArr as $fileName) {
                unlink($_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/route/{$id}/" . $fileName);
            }
        }

        $docTempalate = new \PhpOffice\PhpWord\TemplateProcessor('./upload/docTemplates/transport/route.docx');
        $docTempalate->setValues($fields);
        $fileName = "Маршрутный лист " . $id . ".docx";
        $outputFile = "./upload/transport/route/{$id}/" . $fileName;
        $dir = $_SERVER["DOCUMENT_ROOT"] . "/ulab/upload/transport/route/{$id}/";
        if ( !is_dir($dir) ) {
            $mkdirResult = mkdir($dir, 0766, true);
        }

        $docTempalate->cloneRowAndSetValues('n', $tableArr);

        $docTempalate->saveAs($outputFile);

        return $fileName;
    }

    public function getReportSum($reportId)
    {
        global $DB;

        $stmt = $DB->Query("
            SELECT ROUND(SUM(trr.price * trr.gsm), 2) AS sum
            FROM transport_report_row AS trr
            WHERE trr.report_id = {$reportId} AND trr.del != 1"
        );

        return $stmt->fetch()["sum"];
    }

    public function updateReport($data, $id)
    {
        $data = $this->prepareData($data);
        return $this->DB->Update("transport_report", $data, "WHERE id = {$id}");
    }

    public function copyTransport($transportId, $copyAmount)
    {
        $response = $this->DB->Query("SELECT * FROM transport_report_row WHERE id = '{$transportId}'")->Fetch();

        for($i = 0; $i < $copyAmount; $i++) {
            $this->DB->Query(
                "
                INSERT INTO transport_report_row(report_id, date, time_start, time_end, km, gsm, price, place, company
                , purpose, project_id, del) 
                VALUES('{$response['report_id']}', '{$response['date']}', '{$response['time_start']}', '{$response['time_end']}'
                , '{$response['km']}', '{$response['gsm']}', '{$response['price']}', '{$response['place']}', '{$response['company']}'
                , '{$response['purpose']}', '{$response['project_id']}', '{$response['del']}')
                "
            );
        }
    }

}