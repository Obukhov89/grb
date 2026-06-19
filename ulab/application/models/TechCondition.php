<?php

/**
 * Модель для работы с ТУ (тех условия) для ГОСТа
 * Class TechCondition
 */
class TechCondition extends Model
{
    /**
     * @param $data
     * @return false|mixed|string
     */
    public function add($data)
    {
        $data['is_text_norm'] = isset($data['is_text_norm'])? 1 : 0;

        if ( $data['is_text_norm'] ) {
            $data['is_manual'] = 1;
        } else {
            $data['is_manual'] = isset($data['is_manual'])? 1 : 0;
        }

        $data['is_output'] = $data['is_output'] ?? 0;


        $dopValueList = [];
        $dopNormList = [];

        foreach ($data['dop_v'] as $dopVal) {
            $dopValueList[] = $dopVal;
        }

        $data['dop_value'] = serialize($dopValueList);

        foreach ($data['dop_n'] as $dopNorm) {
            $dopNormList[] = $dopNorm;
        }

        $data['dop_norm'] = serialize($dopNormList);


        $data['clause'] = StringHelper::removeSpace($data['clause']);
        $data['reg_doc'] = StringHelper::removeSpace($data['reg_doc']);

        
        $sqlData = $this->prepearTableData('ulab_tech_condition', $data);

        return $this->DB->Insert('ulab_tech_condition', $sqlData);
    }


    /**
     * @param $id
     * @param $data
     * @return bool|int|string
     */
    public function update($id, $data)
    {
        $data['is_text_norm'] = isset($data['is_text_norm'])? 1 : 0;

        if ( $data['is_text_norm'] ) {
            $data['is_manual'] = 1;
        } else {
            $data['is_manual'] = isset($data['is_manual'])? 1 : 0;
        }

        $data['is_output'] = $data['is_output'] ?? 0;


        $dopValueList = [];
        $dopNormList = [];

        foreach ($data['dop_v'] as $dopVal) {
            $dopValueList[] = $dopVal;
        }

        $data['dop_value'] = serialize($dopValueList);

        foreach ($data['dop_n'] as $dopNorm) {
            $dopNormList[] = $dopNorm;
        }

        $data['dop_norm'] = serialize($dopNormList);


        $data['clause'] = StringHelper::removeSpace($data['clause']);
        $data['reg_doc'] = StringHelper::removeSpace($data['reg_doc']);


        $sqlData = $this->prepearTableData('ulab_tech_condition', $data);

        $where = "WHERE id = {$id}";
        return $this->DB->Update('ulab_tech_condition', $sqlData, $where);
    }


    /**
     * @param $id
     * @return int|string
     */
    public function copy($id)
    {
        $this->DB->Query(
            "insert into `ulab_tech_condition` 
                    (`reg_doc`, `clause`, `year`, `unit_id`, `measured_properties_name`, `name`, `type`, `norm_comment`, 
                   `decimal_places`, `definition_range_1`, `definition_range_2`, `definition_range_type`, `is_text_norm`, 
                   `is_manual`, `dop_material`, `dop_value`, `dop_norm`)
                select 
                   `reg_doc`, `clause`, `year`, `unit_id`, `measured_properties_name`, `name`, `type`, `norm_comment`, 
                   `decimal_places`, `definition_range_1`, `definition_range_2`, `definition_range_type`, `is_text_norm`, 
                   `is_manual`, `dop_material`, `dop_value`, `dop_norm` 
                from `ulab_tech_condition` where id = {$id}"
        );

        return $this->DB->LastID();
    }


    /**
     * @param $id
     * @return array|false
     */
    public function get($id)
    {
        if ( empty($id) ) {
            return [];
        }
        
        $methodSql = $this->DB->Query(
            "select 
                    tu.*, 
                    d.unit_rus, d.name unit_name, d.fsa_id unit_fsa_id
                from `ulab_tech_condition` as tu
                left join `ulab_dimension` as d on d.id = tu.unit_id
                where tu.id = {$id}"
        );

        $result = $methodSql->Fetch();

        if ( !empty($result) ) {
            $strYear = !empty($result['year']) ? "-{$result['year']}" : '';
            $strMpName = '--';
            if ( !empty($result['measured_properties_name']) ) {
                $result['measured_properties_name'] = StringHelper::removeSpace($result['measured_properties_name']);
                $strMpName = $result['measured_properties_name'];
            }
            $result['reg_doc'] = StringHelper::removeSpace($result['reg_doc']);

            $result['clause'] = StringHelper::removeSpace($result['clause']);
            $strClause = !empty($result['clause']) ? " {$result['clause']}" : '';

            $result['view_name'] = "{$result['reg_doc']}{$strYear}{$strClause} | {$strMpName}";
            $result['view_name_for_protocol'] = "{$result['reg_doc']}{$strClause}";
//            $result['view_name_for_protocol'] = "{$result['reg_doc']}{$strYear}{$strClause}";

            $result['dop_value'] = unserialize($result['dop_value']);
            $result['dop_norm'] = unserialize($result['dop_norm']);
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getList()
    {
        $sql = $this->DB->Query(
            "select tu.*,
                        d.id unit_id, d.unit_rus, d.name unit_name
                    from `ulab_tech_condition` as tu
                    left join `ulab_dimension` as d on d.id = tu.unit_id
                    where 1 AND tu.is_actual = 1
                    order by tu.id desc"
        );

        $result = [];

        while ($row = $sql->Fetch()) {

            $strYear = !empty($row['year']) ? "-{$row['year']}" : '';
            $strMpName = '--';
            if ( !empty($row['measured_properties_name']) ) {
                $row['measured_properties_name'] = StringHelper::removeSpace($row['measured_properties_name']);
                $strMpName = $row['measured_properties_name'];
            }
            $row['clause'] = StringHelper::removeSpace($row['clause']);
            $strClause = !empty($row['clause']) ? " {$row['clause']}" : '';
            $row['reg_doc'] = StringHelper::removeSpace($row['reg_doc']);
            $row['view_name'] = "{$row['reg_doc']}{$strYear}{$strClause} | {$strMpName}";

            $row['dop_value'] = unserialize($row['dop_value']);
            $row['dop_norm'] = unserialize($row['dop_norm']);

            $result[] = $row;
        }

        return $result;
    }


    public function delete($id)
    {
        return;
    }


    public function getJournalList($filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'tu.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // Номер документа
                if ( isset($filter['search']['reg_doc']) ) {
                    $where .= "tu.reg_doc LIKE '%{$filter['search']['reg_doc']}%' AND ";
                }
                // Год
                if ( isset($filter['search']['year']) ) {
                    $where .= "tu.year LIKE '%{$filter['search']['year']}%' AND ";
                }
                // clause
                if ( isset($filter['search']['clause']) ) {
                    $where .= "tu.clause LIKE '%{$filter['search']['clause']}%' AND ";
                }
                // Определяемая характеристика / показатель
                if ( isset($filter['search']['measured_properties_name']) ) {
                    $where .= "tu.measured_properties_name LIKE '%{$filter['search']['measured_properties_name']}%' AND ";
                }
                // Единица измерения
                if ( isset($filter['search']['unit_rus']) ) {
                    $where .= "d.unit_rus LIKE '%{$filter['search']['unit_rus']}%' AND ";
                }
                // Актуальность
                if ( isset($filter['search']['is_actual']) ) {
                    $where .= "tu.is_actual = '{$filter['search']['is_actual']}' AND ";
                }
            }
            // везде
            if (isset($filter['search']['everywhere'])) {
                $where .=
                    "";
            }
        }

        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
                case 'reg_doc':
                    $order['by'] = 'tu.reg_doc';
                    break;
                case 'year':
                    $order['by'] = 'tu.year';
                    break;
                case 'measured_properties_name':
                    $order['by'] = 'tu.measured_properties_name';
                    break;
                case 'unit_rus':
                    $order['by'] = 'd.unit_rus';
                    break;
                default:
                    $order['by'] = 'tu.id';
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

        $where .= "1 ";

        $result = [];

        $data = $this->DB->Query(
            "SELECT distinct 
                        tu.*,
                        d.unit_rus
                    FROM ulab_tech_condition tu
                    LEFT JOIN ulab_dimension as d ON d.id = tu.unit_id
                    WHERE {$where}
                    ORDER BY  {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT distinct 
                        tu.id
                    FROM ulab_tech_condition tu
                    LEFT JOIN ulab_dimension as d ON d.id = tu.unit_id
                    WHERE 1"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT distinct 
                        tu.id
                    FROM ulab_tech_condition tu
                    LEFT JOIN ulab_dimension as d ON d.id = tu.unit_id
                    WHERE {$where}"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            if($row['is_actual']) {
                $row['bgStage'] = 'bg-green';
                $row['titleStage'] = 'ТУ Актуально';
            }else {
                $row['bgStage'] = 'bg-grey';
                $row['titleStage'] = 'ТУ Не актуально';
            }

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    public function nonActualMethod($id)
    {
        $this->DB->Query("UPDATE ulab_tech_condition SET is_actual = 0 WHERE id = '{$id}'");
    }

    public function actualMethod($id)
    {
        $this->DB->Query("UPDATE ulab_tech_condition SET is_actual = 1 WHERE id = '{$id}'");
    }
}
