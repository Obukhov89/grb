<?php

/**
 * @desc ФСА - АПИ для Россаккредитации
 * Class Fsa
 */
class Fsa extends Model
{
/*
 *
 *
 *
 * test
 * http://5.143.238.171:8080
 * HARygsmRgpySl1meCfrpoBFwAeYnVOLQ0Dp48KlIOSesgn8WB!V3v5gwKyurGXsAGKW1zoQeY2n64J6b7EvYRCk0a#5ITp!s7WAHnaBTmBLvmaea2AHsvnP5!GEF5eeA
 *
 * boevoi
 * http://5.143.238.143:8080
 * CQJqpP#EmiZuhhuEpc1vHarF5nb5R82uYVJV6oMOB@C$Bn9GDw!jSGP5f!Uyp8gSZV1UnBGW7KPIWhcn8KB5h@XOxgo6f0im3fWkjcxUWKqOtwmQJHngUY0aTv44u@oh
 */
    protected $address = '';
    protected $apiKey = '';


    public function __construct()
    {
        parent::__construct();

        $settings = $this->getSettings();

        $this->address = $settings['address'];
        $this->apiKey  = $settings['api_key'];
    }


    /**
     * @return string
     */
    public function generateGUID()
    {
        if (function_exists('com_create_guid') === true) {
            return strtolower(trim(com_create_guid(), '{}'));
        }

        return strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
    }


    /**
     * @return array
     */
    public function getSettings()
    {
        $sql = $this->DB->Query("select * from ulab_fsa_settings");

        $result = [];
        while ($row = $sql->Fetch()) {
            $result[$row['param']] = $row['value'];
        }

        return $result;
    }


    public function setSettings($data)
    {
        foreach ($data as $param => $value) {
            $updateData = [
                'value' => $this->quoteStr($this->DB->ForSql(trim($value)))
            ];
            $this->DB->Update('ulab_fsa_settings', $updateData, "WHERE param = '{$param}'");
        }
    }


    /**
     * @param $idXml
     * @param $idSig
     * @param $guid
     * @return array
     */
    public function sendMethods($idXml, $idSig, $guid)
    {
        $response['request'] = $this->sendRequest($idXml, $idSig, $guid, "researchMethods");

        sleep(1);

        $response['result'] = $this->getResults($guid, "researchMethods");

        return $response;
    }


    /**
     * @return false|string|null
     */
    public function getResultLastRequest()
    {
        $history = $this->DB->Query("SELECT `guid_request`, `method` FROM `history_fsa` ORDER BY id DESC LIMIT 1")->Fetch();

        return $this->getResults($history['guid_request'], $history['method']);
    }


    /**
     * @param $fileDirName
     * @param false $moreInfo
     * @return false|string|null
     */
    public function sendFile($fileDirName, $moreInfo = false)
    {
        $cmd = $moreInfo? '2>&1' : '';

        return shell_exec("curl -X 'POST' '{$this->address}/api/file'  -H 'accept: */*' -H 'X-API-KEY: {$this->apiKey}' -H 'Content-Type: multipart/form-data' -F 'file=@\"{$fileDirName}\"' {$cmd}");
    }


    /**
     * Сохраняем логи
     * @param $guidRequest
     * @param $guidXml
     * @param $guidSig
     * @param $method
     * @param $file
     * @param $result
     */
    public function saveHistory($guidRequest, $guidXml, $guidSig, $method, $file, $result)
    {
        $data = [
            'guid_request'  => $this->quoteStr($guidRequest),
            'guid_xml'      => $this->quoteStr($guidXml),
            'guid_sig'      => $this->quoteStr($guidSig),
            'method'        => $this->quoteStr($method),
            'xml_file'      => $this->quoteStr($file),
            'result'        => $this->quoteStr($this->DB->ForSql($result)),
            'datetime'      => $this->quoteStr(date('Y-m-d H:i:s')),
            'user_id'       => $_SESSION['SESS_AUTH']['USER_ID'],
        ];

        $this->DB->Insert('history_fsa', $data);
    }


    public function getDataToJournalHistory($filter)
    {
        $result = [];

        $where = "";
        $limit = "";
        $order = [
            'by' => 'id',
            'dir' => 'DESC'
        ];
        if ( !empty($filter) ) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if ( !empty($filter['search']) ) {

                // везде
                if ( isset($filter['search']['everywhere']) ) {
                    $where .=
                        "";
                }
            }

            // работа с сортировкой
            if ( !empty($filter['order']) ) {
                if ( $filter['order']['dir'] === 'asc' ) {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {

                }
            }

            // работа с пагинацией
            if ( isset($filter['paginate']) ) {
                $offset = 0;
                // количество строк на страницу
                if ( isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0 ) {
                    $length = $filter['paginate']['length'];

                    if ( isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0 ) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT {$offset}, {$length}";
                }
            }
        }
        $where .= "1 ";

        $data = $this->DB->Query(
            "SELECT `guid_request`, `method`, `xml_file`, `datetime`
                    FROM history_fsa
                    WHERE {$where}
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM history_fsa
                    WHERE 1"
        )->Fetch();
        $dataFiltered = $this->DB->Query(
            "SELECT count(*) val
                    FROM history_fsa
                    WHERE {$where}"
        )->Fetch();

        while ($row = $data->Fetch()) {
            $row['date'] = date('d.m.Y', strtotime($row['datetime']));
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal['val'];
        $result['recordsFiltered'] = $dataFiltered['val'];

        return $result;
    }


    /**
     * @param $guid
     * @param $method
     * @return false|string|null
     */
    public function getResults($guid, $method)
    {
        return shell_exec("curl -X 'GET' '{$this->address}/api/{$method}/{$guid}' -H 'accept: application/json' -H 'X-API-KEY: {$this->apiKey}'");
    }


    /**
     * @param $idXml
     * @param $idSig
     * @param $guid
     * @param $method
     * @return false|string|null
     */
    public function sendRequest($idXml, $idSig, $guid, $method)
    {
        return shell_exec(
            "curl -X 'POST' '{$this->address}/api/{$method}/{$guid}' -H 'accept: */*' -H 'X-API-KEY: {$this->apiKey}'   -H 'Content-Type: application/json' -d '{
              \"dataLink\": \"{$idXml}\",
              \"dataSigLink\": \"{$idSig}\",
              \"attachments\": []
              }' -v 2>&1"
        );
    }
}