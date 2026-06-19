<?php

/**
 * Класс модель для Зернового состава
 * Class Grain
 */
class Grain extends Model
{
    const SEAVE = [
        0 => [
            "seave_main_type" => "round",
            "seave_type" => [
                0 => [
                    "type_to_input" => 200,
                    "type_to_title" => "Сита"
                ]
            ]
        ],
    ];

    public function getGrainGostList(int $grainListID)
    {
        $result = [];
        $ba_gost = $this->DB->Query("SELECT bg.ID AS gost_id,
                                            bg.GOST,
                                            bg.GOST_PUNKT,
                                            bg.SPECIFICATION,
                                            zr.ID AS grain_id
                                     FROM ba_gost AS bg

                                     LEFT JOIN ZERN AS zr
                                     ON bg.ID = zr.GOST_ID

                                     WHERE bg.NON_ACTUAL != 1
        ");

        while ($gost = $ba_gost->Fetch()) {
            $result[] = [
                "gost_id" => $gost['gost_id'],
                "gost" => $gost['GOST'],
                "gost_point" => $gost['GOST_PUNKT'],
                "gost_specification" => $gost['SPECIFICATION'],
                "grain_list_id" => $gost['grain_id']
            ];
        }

        return $result;
    }

    public function getGrainSeaveSize()
    {
        $result = SELF::SEAVE;

        $response = $this->DB->Query("SELECT * FROM sieves");

        $sievesArray = [];

        $lowerMkmArray = [];
        $lowerMmArray = [];
        $mmArray = [];
        $mkmArray = [];
        while($row = $response->Fetch()) {
            $row['title'] = str_replace(',', '.', $row['title']);

            if(is_numeric(strpos($row['title'], 'менее '))) {
                $row['title'] = str_replace('менее ', '', $row['title']);

                if(is_numeric(strpos($row['title'], ' мкм'))) {
                    $row['title'] = str_replace(' мкм', '', $row['title']);

                    $lowerMkmArray[$row['id']] = $row['title'];
                }else {
                    $row['title'] = str_replace(' мм', '', $row['title']);

                    $lowerMmArray[$row['id']] = $row['title'];
                }
            }else if(is_numeric(strpos($row['title'], ' мкм'))) {
                $row['title'] = str_replace(' мкм', '', $row['title']);

                $mkmArray[$row['id']] = $row['title'];
            }else {
                $row['title'] = str_replace(' мм', '', $row['title']);

                $mmArray[$row['id']] = $row['title'];
            }
        }

        arsort($lowerMkmArray);
        arsort($lowerMmArray);
        arsort($mmArray);
        arsort($mkmArray);

        foreach ($mmArray as $key => $value) {
            $value .= ' мм';

            $sievesArray []= [
                'id' => $key,
                'title' => $value
            ];
        }

        foreach ($mkmArray as $key => $value) {
            $value .= ' мкм';

            $sievesArray []= [
                'id' => $key,
                'title' => $value
            ];
        }

        foreach ($lowerMmArray as $key => $value) {
            $value = 'менее ' . $value . ' мм';

            $sievesArray []= [
                'id' => $key,
                'title' => $value
            ];
        }

        foreach ($lowerMkmArray as $key => $value) {
            $value = 'менее ' . $value . ' мкм';

            $sievesArray []= [
                'id' => $key,
                'title' => $value
            ];
        }

        $result[0]['seave_type'][0]['values'] = $sievesArray;

        return $result;
    }

    public function getGrainSeaveValues(int $grainListID)
    {
        $result = [];
        $query = $this->DB->Query("SELECT *
                                   FROM ZERN
                                   WHERE ID = " . $grainListID
        )->Fetch();

        $result = [
            "name" => $query['NAME'],
            "data" => json_decode($query['DATA'], true)
        ];

        return $result;
    }

    public function update(int $grainListID, array $post)
    {
        $data['NAME'] = $this->quoteStr($post['grain_list_name']);
        $data['GOST_ID'] = isset($post['grain_list_gost']) ? $post['grain_list_gost'] : "";
        $data['DATA'] =  $this->quoteStr(json_encode($post['grain'], JSON_UNESCAPED_UNICODE));

        $this->DB->Update("ZERN", $data,
                          "WHERE ID = " . $grainListID);
    }

    public function getDataToJournalGrain(array $filter = []): array
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'NAME',
            'dir' => 'DESC'
        ];
        $result = [];

        if ( !empty($filter) ) {
            if ( !empty($filter['search']) ) {
                if ( isset($filter['search']['material_name']) ) {
                    $where .= "NAME LIKE '%{$filter['search']['material_name']}%' AND ";
                }
            }

            if ( !empty($filter['order']) ) {
                if ( $filter['order']['dir'] === 'asc' ) {
                    $order['dir'] = 'ASC';
                }
            }

            if ( isset($filter['paginate']) ) {
                $offset = 0;
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

        $data = $this->DB->Query("SELECT `ID`,
                                         `NAME`
                                  FROM ZERN
                                  WHERE {$where}
                                  GROUP BY ID
                                  ORDER BY {$order['by']} {$order['dir']} {$limit}
        ");

        $dataTotal = $this->DB->Query("SELECT count(*) val
                                       FROM ZERN
        ")->Fetch();

        $dataFiltered = $this->DB->Query("SELECT count(*) val
                                          FROM ZERN
        ")->Fetch();

        while ($row = $data->Fetch()) {
            $row['material_name'] = $row['NAME'];
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal['val'];
        $result['recordsFiltered'] = $dataFiltered['val'];

        return $result;
    }

    public function convertOldToNewFormat()
    {
        $SIEVE_ARR = ['200 мм', '120 мм', '100 мм', '80 мм', '60 мм', '40 мм', '31,5 мм', '22,4 мм', '20 мм', '16 мм', '15 мм', '11,2 мм', '10 мм', '8 мм',
            '5 мм', '4 мм', '2,5 мм', '2 мм', '1,25 мм', '1 мм', '0,63 мм', '0,5 мм', '0,315 мм', '0,25 мм', '0,16 мм', '0,125 мм', '0,1 мм', '0,071 мм', '0,063 мм',
            '0,05 мм', 'менее 0,05 мм', '5,6 мм', '45 мм', '63 мм', '90 мм', '12,5 мм', '7,5 мм', '25 мм', '50 мм', '30 мм', '87,5 мм', '70 мм', '55 мм', '126 мм',
            '180 мм', '2,8 мм', '3 мм', '17,5 мм', 'менее 0,16 мм', 'менее 0,125 мм', '125 мм', '56 мкм', '20 мкм', '10 мкм', '5 мкм',
            '2 мкм', 'менее 2 мкм', '150 мм', '105 мм', '35 мм', '1000 мкм', '850 мкм', '700 мкм', '600 мкм', '500 мкм', 'менее 500 мкм', 'менее 1 мм',
            '0,80 мм', '0,40 мм', '0,20 мм', '1.6 мм', 'менее 2,5 мм', 'менее 5 мм', 'менее 10 мм', 'менее 20 мм', '0,010 мм', '0,005 мм', '0,001 мм', '0,001 мм', '300 мм', 'менее 0.05 мм'];

        $NEW_SIEVE_ARR = [];

        $sieveResponse = $this->DB->Query("SELECT * FROM sieves");
        while($row = $sieveResponse->Fetch()) {
            $NEW_SIEVE_ARR[$row['id']] = $row['title'];
        }

        $result = [];

        $response = $this->DB->Query("SELECT * FROM ZERN");
        while($row = $response->Fetch()) {
            $decodedFrom = unserialize($row['NORM1']);
            $decodedTo = unserialize($row['NORM2']);

            $result[$row['ID']]['round'][200]['seave_values__from'] = [];
            $result[$row['ID']]['round'][200]['seave_values__to'] = [];

            foreach ($decodedFrom as $key => $value) {
                $oldSieve = $SIEVE_ARR[$key];
                $newSieveKey = array_search($oldSieve, $NEW_SIEVE_ARR);

                $result[$row['ID']]['round'][200]['seave_values__from'][$newSieveKey] = $value;
            }

            foreach ($decodedTo as $key => $value) {
                $oldSieve = $SIEVE_ARR[$key];
                $newSieveKey = array_search($oldSieve, $NEW_SIEVE_ARR);

                $result[$row['ID']]['round'][200]['seave_values__to'][$newSieveKey] = $value;
            }
        }

        foreach ($result as $key => $value) {
            $jsonResult = json_encode($value, JSON_UNESCAPED_UNICODE);

            $this->DB->Query("UPDATE ZERN SET DATA = '{$jsonResult}' WHERE ID = '{$key}'");
        }
    }

    public function createSieve($data)
    {
        $result = '';

        if(!empty($data['lower'])) {
            $result .= 'менее ';
        }

        $result .= $data['size'] . ' ' . $data['meas'];

        $this->DB->Query("INSERT INTO sieves(title) VALUES('{$result}')");
    }

    public function getGostsToSelect()
    {
        $result = [];

        $response = $this->DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL` != 1");

        while($row = $response->Fetch()) {
            $result []= $row;
        }

        return $result;
    }

    public function getMaterialGroups()
    {
        $result = [];

        $materialResponse = $this->DB->Query("SELECT * FROM `MATERIALS`");

        while($row = $materialResponse->Fetch()) {
            $currentGroup = unserialize($row['GROUPS']);

            $row['current_group'] = $currentGroup;

            $result []= $row;
        }

        return $result;
    }

    public function createNewZern($data)
    {
        $this->DB->Query("INSERT INTO ZERN(`GOST_ID`, `TYPE_MAT_ID`, `NAME`) VALUES('{$data['GOST_ID']}', '{$data['TYPE_MAT_ID']}', '{$data['NAME']}')");

        $currentId = $this->DB->Query("SELECT ID FROM ZERN ORDER BY ID DESC LIMIT 1")->Fetch()['ID'];

        return $currentId;
    }
}