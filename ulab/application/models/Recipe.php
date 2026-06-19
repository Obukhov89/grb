<?php

/**
 * Модель для работы с рецептами расторов
 * Class Recipe
 */
class Recipe extends Model
{
    public string $location = "/recipe/list/";

    private function funcTrim(array $data): array
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }
        return $data;
    }

    public function getList($filter): array
    {
        $where = "";
        $limit = "";

        function addWhere($filter, $filterValue): string
        {
            $nameTable = $filterValue[0];
            $nameColumn = $filterValue[1];
            $filterUsed = $filter['search'][$nameColumn];
            if (isset($filterUsed)) {
                return "$nameTable.$nameColumn LIKE '%$filterUsed%' AND ";
            }
            return '';
        }

        $tableColumnForFilter = [
            ['recipe_model', 'name_recipe'],
            ['ba_gost', 'GOST'],
            ["REACTIVE_OST", "REACT_NAME"]
        ];

        if (!empty($filter)) {
            foreach ($tableColumnForFilter as $item) {
                $where .= addWhere($filter, $item);
            }

            if (isset($filter['paginate'])) {
                $offset = 0;
                // количество строк на страницу
                if (isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0) {
                    $length = $filter['paginate']['length'];

                    if (isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT $offset, $length";
                }
            }
        }
        $result = [];
        //Затычка, что бы не было пустого WHERE в SQL запросе
        $where .= "1 ";

        $requestSQL = $this->DB->Query(
            "SELECT recipe_model.*,ba_gost.GOST,
            GROUP_CONCAT(CONCAT(name_reactive,' К-во = ',unit_reactive.quantity_reactive, ' г') SEPARATOR '\n') reactives,
            CONCAT(name_solvent,' К-во = ',unit_solvent.quantity_reactive, ' мл') as solvent
            FROM recipe_model 
            JOIN unit_reactive ON unit_reactive.id_recipe_model = recipe_model.id
            JOIN ba_gost ON recipe_model.id_doc=ba_gost.ID
            JOIN unit_solvent ON unit_solvent.id_recipe_model = recipe_model.id
            WHERE $where
            GROUP by unit_reactive.id_recipe_model
            ORDER BY recipe_model.id DESC 
            $limit
        "
        );

        while ($row = $requestSQL->Fetch()) {
            $result[] = $row;
        }

        $dataTotal = $this->DB->Query(
            "SELECT id
                                       FROM recipe_model
        "
        )->SelectedRowsCount();

        $dataFiltered = $requestSQL->SelectedRowsCount();

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    public function addModelRecipe(array $data): bool
    {
        $dataModelRecipe = array_slice($data, 0, 5, true);
        $dataModelRecipe = $this->funcTrim($dataModelRecipe);
        $idModelRecipe = $this->DB->Insert('recipe_model', $dataModelRecipe);

        $dataReactives = $data['reactives'];
        if ($idModelRecipe) {
            $result = $this->addReactives($idModelRecipe, $dataReactives);
        }

        if ($result) {
            $dataSolvent = $data['solvent'];
            $idReactive = $dataSolvent['id_reactive'];
            $dataName = $this->DB->Query(
                "SELECT REACT_NAME 
                                        FROM REACTIVE_OST
                                        WHERE ID = $idReactive
        "
            )->Fetch();
            $dataSolvent['name_solvent'] = $dataName['REACT_NAME'];
            $dataSolvent['id_recipe_model'] = $idModelRecipe;
            $dataSolvent = $this->funcTrim($dataSolvent);
            $result = $this->DB->Insert('unit_solvent', $dataSolvent);
        }
        if ($result) {
            return true;
        }

        return false;
    }

    private function addReactives(int $idModelRecipe, array $dataReactives): bool
    {
        $result = true;
        if ($result) {
            foreach ($dataReactives as $value) {
                $idReactive = $value['id_reactive'];
                $dataName = $this->DB->Query(
                    "SELECT REACT_NAME 
                                        FROM REACTIVE_OST
                                        WHERE ID = $idReactive
                "
                )->Fetch();
                $value['name_reactive'] = $dataName['REACT_NAME'];
                $value['id_recipe_model'] = $idModelRecipe;
                $value = $this->funcTrim($value);
                $result = $this->DB->Insert('unit_reactive', $value);
            }
        }
        if ($result) {
            return true;
        }
        return false;
    }

    public function addSolutionAsReactive(array $data): bool
    {
        $reactive = $data['reactive'];
        $reactive = $this->funcTrim($reactive);
        $idReactive = $this->DB->Insert('REACTIVE_OST', $reactive);

        if ($idReactive) {
            $solutionAsReactive = $data['solutionAsReactive'];
            $solutionAsReactive['id_reactive'] = $idReactive;
            $solutionAsReactive = $this->funcTrim($solutionAsReactive);
            $result = $this->DB->Insert('solution_as_reactive', $solutionAsReactive);
        }
        if ($result) {
            return true;
        }
        return false;
    }

    public function addToSQL(array $data, string $type = null): int
    {
        $namesTable = [
            'reactive_model' => 'reactive_model',
            'reactive' => 'reactive',
            'receive' => 'receive_reactive',
            'reactive_doc' => 'reactive_doc',
            'solution_as_reactive' => 'solution_as_reactive'
        ];
        if ($type == null) {
            $name = array_key_first($data);
            if (!isset($namesTable[$name])) {
                return 0;
            }
            $dataAdd = $data[$name];
        }
        if ($type == 'reactive') {
            $dataModelReactive['reactive_model'] = $data['reactive_model'];
            $idModelReactive = $this->addToSQL($dataModelReactive);
            if (!$idModelReactive) {
                return 0;
            }
            $data['reactive']['id_reactive_model'] = $idModelReactive;
            return $this->addToSQL($data);
        }

        foreach ($dataAdd as $key => $item) {
            if (is_string($item)) {
                $dataAdd[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }
        return $this->DB->Insert($namesTable[$name], $dataAdd);
    }

    public function getFromSQL(string $name): array
    {
        $nameArray = [
            'reactive' => 'REACTIVE_OST',
            'doc' => 'ba_gost',
            'recipe' => 'recipe_model'
        ];
        $response = [];
        if (isset($nameArray[$name])) {
            $requestFromSQL = $this->DB->Query("select * from $nameArray[$name]");
        }
        while ($row = $requestFromSQL->Fetch()) {
            $response[] = $row;
        }
        return $response;
    }

}

