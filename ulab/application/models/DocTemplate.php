<?php

class DocTemplate extends Model
{

    /**
     * Путь до папки сохраненных шаблонов
     */
    const FILE_TEMPLATE_PATH = UPLOAD_DIR . '/doc_template';
    const FILE_TEMPLATE_URL = UPLOAD_URL . '/doc_template';


    /**
     * @param $data
     * @return false|mixed|string
     */
    public function add($data, $file)
    {
        $fileName = time() . '_' . $file["name"];

        $result = $this->saveFile(self::FILE_TEMPLATE_PATH, $fileName, $file["tmp_name"]);

        if ( !$result['success'] ) {
            return $result;
        }

        $data['file_name'] = $fileName;

        $sqlData = $this->prepearTableData('ulab_template', $data);

        $resultInsert = $this->DB->Insert('ulab_template', $sqlData);

        if ( empty($resultInsert) ) {
            return [
                'success' => false,
                'error' => "Ошибка! Не удалось создать шаблон.",
            ];
        } else {
            return [
                'success' => true,
                'id' => $resultInsert,
            ];
        }
    }


    /**
     * @param $id
     * @param $data
     * @param $file
     * @return array
     */
    public function update($id, $data, $file)
    {
        if ( !empty($file["name"]) ) {
            $fileName = time() . '_' . $file["name"];

            $result = $this->saveFile(self::FILE_TEMPLATE_PATH, $fileName, $file["tmp_name"]);

            if ( !$result['success'] ) {
                return $result;
            }

            $template = $this->get($id);

            unset($template['file_dir']);

            $data['file_name'] = $fileName;
        }

        $sqlData = $this->prepearTableData('ulab_template', $data);

        $this->DB->Update('ulab_template', $sqlData, "WHERE id = {$id}");

        return [
            'success' => true,
            'id' => $id,
        ];
    }


    /**
     * @param int $id
     * @return array|false
     */
    public function get(int $id)
    {
        $sql = $this->DB->Query(
            "select t.*, tt.name as type_text, tt.dir, tt.name_save_file 
                    from ulab_template t, ulab_template_type tt where t.id = {$id} and t.id_template_type = tt.id"
        );

        $result = [];
        while ($row = $sql->Fetch()) {
            $row['file_dir'] = self::FILE_TEMPLATE_PATH . '/' . $row['file_name'];
            $row['file_url'] = self::FILE_TEMPLATE_URL . '/' . $row['file_name'];
            $result = $row;
        }

        return $result;
    }


    /**
     * @param int $type - ид типа в таблице ulab_template_type
     * @return array
     */
    public function getList(int $type = 0)
    {

        $where = '1';

        if ( !empty($type) ) {
            $where = "t.id_template_type = {$type}";
        }

        $sql = $this->DB->Query(
            "select t.*, tt.name as type_text, tt.dir, tt.name_save_file 
                from ulab_template t, ulab_template_type tt where t.id_template_type = tt.id and {$where}"
        );

        $result = [];
        while ($row = $sql->Fetch()) {
            $row['file_dir'] = self::FILE_TEMPLATE_PATH . '/' . $row['file_name'];
            $row['file_url'] = self::FILE_TEMPLATE_URL . '/' . $row['file_name'];
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getTemplateTypeList()
    {
        $sql = $this->DB->Query(
            "select * from ulab_template_type"
        );

        $result = [];
        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getMacrosTypeList()
    {
        $sql = $this->DB->Query(
            "select * from ulab_template_macros_type"
        );

        $result = [];
        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * Удаляет файл и запись о шаблоне
     * @param $id
     */
    public function deleteTemplate($id)
    {
        $templateData = $this->DB->Query("select * from ulab_template where id = {$id}")->Fetch();

        unlink(self::FILE_TEMPLATE_PATH . '/' . $templateData['file_name']);

        $this->DB->Query("delete from ulab_template where id = {$id}");
    }


    /**
     * @param $filter
     * @return array
     */
    public function getDataJournalTemplate($filter)
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
                if ( isset($filter['search']['file_name']) ) {
                    $where .= "t.`file_name` like '%{$filter['search']['file_name']}%' and ";
                }
                if ( isset($filter['search']['name']) ) {
                    $where .= "t.`name` like '%{$filter['search']['name']}%' and ";
                }
                if ( isset($filter['search']['type_text']) ) {
                    $where .= "t.`id_template_type` = '{$filter['search']['type_text']}' and ";
                }
            }

            // работа с сортировкой
            if ( !empty($filter['order']) ) {
                if ( $filter['order']['dir'] === 'asc' ) {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'file_name':
                        $order['by'] = 't.file_name';
                        break;
                    case 'name':
                        $order['by'] = 't.name';
                        break;
                    case 'type':
                        $order['by'] = 't.id_template_type';
                        break;
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
            "SELECT t.*, tt.name as type_text 
                    FROM ulab_template t
                    join ulab_template_type tt on t.id_template_type = tt.id
                    WHERE {$where}
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM ulab_template t
                    join ulab_template_type tt on t.id_template_type = tt.id
                    WHERE 1"
        )->Fetch();
        $dataFiltered = $this->DB->Query(
            "SELECT count(*) val
                    FROM ulab_template t
                    join ulab_template_type tt on t.id_template_type = tt.id
                    WHERE {$where}"
        )->Fetch();

        while ($row = $data->Fetch()) {
            $row['file_dir'] = self::FILE_TEMPLATE_PATH . '/' . $row['file_name'];
            $row['file_url'] = self::FILE_TEMPLATE_URL . '/' . $row['file_name'];
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal['val'];
        $result['recordsFiltered'] = $dataFiltered['val'];

        return $result;
    }


    /**
     * @param $filter
     * @return array
     */
    public function getDataJournalMacros($filter)
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
                if ( isset($filter['search']['macros']) ) {
                    $where .= "tm.`macros` like '%{$filter['search']['macros']}%' and ";
                }
                if ( isset($filter['search']['description']) ) {
                    $where .= "tm.`description` like '%{$filter['search']['description']}%' and ";
                }
                if ( isset($filter['search']['type_name']) ) {
                    $where .= "tm.`type_id` = '{$filter['search']['type_name']}' and ";
                }
            }

            // работа с сортировкой
            if ( !empty($filter['order']) ) {
                if ( $filter['order']['dir'] === 'asc' ) {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'macros':
                        $order['by'] = 'macros';
                        break;
                    case 'description':
                        $order['by'] = 'description';
                        break;
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
            "SELECT tm.*, tt.name type_name
                    FROM ulab_template_macros tm
                    inner join ulab_template_macros_type tt on tt.id = tm.type_id
                    WHERE {$where}
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT count(*) val
                    FROM ulab_template_macros
                    WHERE 1"
        )->Fetch();
        $dataFiltered = $this->DB->Query(
            "SELECT count(*) val
                    FROM ulab_template_macros tm
                    inner join ulab_template_macros_type tt on tt.id = tm.type_id
                    WHERE {$where}"
        )->Fetch();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal['val'];
        $result['recordsFiltered'] = $dataFiltered['val'];

        return $result;
    }
}
