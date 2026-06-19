<?php

/**
 * Модель для работы со схемами для материала
 * Class Scheme
 */
class Scheme extends Model
{
    /**
     * получает весь список схем
     * @return array
     */
    public function getList(): array
    {
        $result = [];
        $sql = $this->DB->Query(
            "select sm.*, m.name as material_name 
                from `ulab_scheme_material` as sm, `ulab_material` as m 
                where sm.material_id = m.id 
                order by sm.material_id desc, sm.`name` asc");

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * получает список схем по материалу
     * @param int $materialId
     * @return array
     */
    public function getSchemeListByMaterial(int $materialId): array
    {
        $result = [];
        $sql = $this->DB->Query(
            "select sm.*, m.name as material_name 
                from `ulab_scheme_material` as sm, `ulab_material` as m 
                where sm.material_id = m.id and sm.material_id = {$materialId}
                order by sm.material_id desc, sm.`name` asc");

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    // TODO: получить методики по схеме
    public function getMethodsBySchemeId($schemeId)
    {

    }

    // TODO: добавить схему к материалу


    // TODO: добавить схеме методику
}