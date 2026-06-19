<?php

/**
 * Модель для работы с Приготовленными растворами
 * Class Solution
 */
class Solution extends Model
{
    private function funcTrim(array $data): array
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }
        return $data;
    }

    public function getList($filter)
    {
        global $USER;
        $where = "";
        $limit = "";
        $result = [];
        function addWhere($filter, $filterValue)
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
            ["solution_prepared", "Name"],
            ["model_recipe", "name"]
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
        $where .= "1 ";
        $requestSQL = $this->DB->Query(
            "SELECT * from `solution_prepared` 
            JOIN `model_recipe` ON solution_prepared.id_model_recipe=model_recipe.id
            WHERE $where
            ORDER BY solution_prepared.id DESC
            $limit
        "
        );

        while ($row = $requestSQL->Fetch()) {
            $arname = $USER->GetByID($row["id_people"]);
            $rsname = $arname->Fetch();
            $name = $rsname["NAME"] . " " . $rsname["LAST_NAME"];
            $row["id_people"] = $name;
            $result[] = $row;
        }

        $dataTotal = $this->DB->Query(
            "SELECT id FROM solution_prepared
        "
        )->SelectedRowsCount();

        $dataFiltered = $requestSQL->SelectedRowsCount();
        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    public function addSolution(array $data): bool
    {
        $data = $this->funcTrim($data);
        $result = $this->DB->Insert('solution_prepared', $data);
        if ($result) {
            return true;
        }
        return false;
    }

    public function getFromSQL(string $name): array
    {
        $nameArray = [
            'model_recipe' => 'model_recipe'
        ];
        $response = [];
        if (isset($nameArray[$name])) {
            $requestFromSQL = $this->DB->Query("select * from $nameArray[$name]");
            while ($row = $requestFromSQL->Fetch()) {
                $response[] = $row;
            }
        }
        return $response;
    }

}