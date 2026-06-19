<?php

/**
 * @desc Карьеры
 * Class Quarry
 */
class Quarry extends Model
{

    /**
     * @return array
     */
    public function getList()
    {
//        $sql = $this->DB->Query("select * from ulab_quarry");
        $sql = $this->DB->Query("select * from Quarry");

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }
}