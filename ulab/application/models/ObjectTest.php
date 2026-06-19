<?php

class ObjectTest extends Model
{
    public function add($data) {
        $sqlData = $this->prepearTableData('DEV_OBJECTS', $data);
        $this->DB->Insert('DEV_OBJECTS', $sqlData);
//       // $this->DB->Insert('DEV_OBJECTS', $data);
//        [
//            "NAME" => $name,
//            "ID_COMPANY" => $companyId,
//            "COORD" => $coord,
//            "CITY_ID" => $city,
//            "KM" => $km
//        ] = $data;
//
//        $this->DB->Query("
//            INSERT INTO `DEV_OBJECTS` (NAME, ID_COMPANY, COORD, CITY_ID, KM)
//            VALUES ('{$name}', {$companyId}, '{$coord}', {$city}, {$km})
//        ");

        return intval($this->DB->LastID());
    }

    // Добавить города в справочник
    public function addSql($cityName)
    {
        $this->DB->Query("
            INSERT INTO `settlements` (name, type, country) 
            VALUES ('{$cityName}', 'город', 'Россия')
        ");
    }


    public function update(array $data, string $table, int $id)
    {
        $where = "WHERE ID = {$id}";
        return $this->DB->Update($table, $data, $where);
    }


}