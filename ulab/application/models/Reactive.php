<?php

/**
 * Модель для работы с рецептами расторов
 * Class Reactive
 */
class Reactive extends Model
{
//    public string $location = '/reactive/list/';

    public function getList($filter = [], $name = 'getList'): array
    {
        $where = "";
        $limit = "";
        $order = "";

        $tableColumnForFilter = [
        	'lab' => 'reactive.laba_id',
        	'lab_id' => 'reactive_receive.laboratory_id',
        	'is_reactive' => 'reactive_model.is_reactive',
            'name' => 'reactive_model.name',
            'aggregate_name' => 'aggregate_state.name',
            'short_name' => 'reactive_pure.short_name',
            'doc_name' => 'reactive.doc_name',
			'doc_receive' => 'reactive_receive.doc_receive',
			'date_receive' => 'reactive_receive.date_receive',
			'date_production' => 'reactive_receive.date_production',
			'date_expired' => 'reactive_receive.date_expired',
			'number_batch' => 'reactive_receive.number_batch',
			'quantity' => 'reactive_receive.quantity',
        ];
        function addWhere($filter, $key, $value): string
        {
            $filterUsed = $filter['search'][$key];
            if (isset($filterUsed)) {
                return "$value LIKE '%$filterUsed%' AND ";
            }
            return '';
        }

        if (!empty($filter)) {
            foreach ($tableColumnForFilter as $key => $value) {
                $where .= addWhere($filter, $key, $value);
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

        $orderFilter = [
            'by' => 'reactive_model.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $orderFilter['dir'] = 'ASC';
            }
            $orderFilter['by'] = $tableColumnForFilter[$filter['order']['by']];
        }
        $order = "{$orderFilter['by']} {$orderFilter['dir']} ";

        $result = [];
        //Затычка, что бы не было пустого WHERE в SQL запросе
        $where .= "1 ";
        $result = $this->getFromSQL($name, $where, $order, $limit);

        return $result;
    }

    public function addToSQL(array $data, string $type = null): int
    {
		$userModel = new User();
		$id_reactive_model = '';

        /*  if (count($data) > 1 && $type = null) {
            echo('Массив должен быть из одного элемента или введите тип добавления в БД');
            exit;
        }*/
        $namesTable = [
            'reactive_model' => 'reactive_model',
            'reactive' => 'reactive',
            'receive' => 'reactive_receive'
        ];

        foreach ($data as $name => $dataNameTable) {
			if (!isset($namesTable[$name])) {
				return 0;
			}
			$dataAdd = $data[$name];

			if ($namesTable[$name] == 'reactive') {
				$dataAdd['id_reactive_model'] = $id_reactive_model;
			}

			if ($namesTable[$name] == 'reactive_receive') {



				$userId = $_SESSION['SESS_AUTH']['USER_ID'];
				$dataAdd['creater'] = $userId;

				$depId = $userModel->getDepartmentByUserId($userId);
				$dataAdd['laboratory_id'] = $depId;
			}


			foreach ($dataAdd as $key => $item) {
				if (is_string($item)) {
					$dataAdd[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
				}
			}

			if ($namesTable[$name] == 'reactive_model') {
				$id_reactive_model = $this->DB->Insert($namesTable[$name], $dataAdd);
			} else {
				$this->DB->Insert($namesTable[$name], $dataAdd);
			}
		}

        return true;



//        if ($type == null) {
//            $name = array_key_first($data);
//            if (!isset($namesTable[$name])) {
//                return 0;
//            }
//            $dataAdd = $data[$name];
//        }
//
//		echo '<pre>';
//		print_r($data);
//		exit();
//        /*     if ($type == 'modelReactiveAndReactive') {
//                 $dataModelReactive['reactive_model'] = $data['reactive_model'];
//                 $idModelReactive = $this->addToSQL($dataModelReactive);
//                 if (!$idModelReactive) {
//                     return 0;
//                 }
//                 $data['reactive']['id_reactive_model'] = $idModelReactive;
//                 return $this->addToSQL($data, 'reactive');
//             }*/
//        if ($type == 'reactiveAndReceive') {
//            $dataReactive['reactive'] = $data['reactive'];
//            $idReactive = $this->addToSQL($dataReactive);
//            if (!$idReactive) {
//                return 0;
//            }
//            $data['receive']['id_reactive'] = $idReactive;
//            $dataReceive['receive'] = $data['receive'];
//            return $this->addToSQL($dataReceive);
//        }
//        foreach ($dataAdd as $key => $item) {
//            if (is_string($item)) {
//                $dataAdd[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
//            }
//        }
//        return $this->DB->Insert($namesTable[$name], $dataAdd);
    }

	public function updToSQL(array $data, string $type = null): int
	{
		$userModel = new User();
		$id = $data['reactive_model']['id'];
		unset($data['reactive_model']['id']);

		$namesTable = [
			'reactive_model' => 'reactive_model',
			'reactive' => 'reactive'
		];

		foreach ($data as $name => $dataNameTable) {
			if (!isset($namesTable[$name])) {
				return 0;
			}
			$dataUpd = $data[$name];

			if ($namesTable[$name] == 'reactive') {
				$dataUpd['id_reactive_model'] = $id;
			}

			if ($namesTable[$name] == 'reactive_receive') {

				$userId = $_SESSION['SESS_AUTH']['USER_ID'];
				$dataUpd['creater'] = $userId;

				$depId = $userModel->getDepartmentByUserId($userId);
				$dataUpd['laboratory_id'] = $depId;
			}


			foreach ($dataUpd as $key => $item) {
				if (is_string($item)) {
					$dataUpd[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
				}
			}



			if ($namesTable[$name] == 'reactive_model') {
				$this->DB->Update($namesTable[$name], $dataUpd, "WHERE id = $id");
			} else {
				$this->DB->Update($namesTable[$name], $dataUpd, "WHERE id_reactive_model = $id");
			}
		}

		return true;
	}



    public function getFromSQL(string $name, string $where = null, string $order = null, string $limit = null): array
    {
        $namesTable = [
            'aggregate' => 'aggregate_state',
            'pure' => 'reactive_pure',
            'reactive' => 'reactive_model',
            'unit_of_measurement' => 'unit_of_measurement'
        ];

        $response = [];

        if (isset($namesTable[$name])) {
            $requestFromSQL = $this->DB->Query("SELECT * from $namesTable[$name]");
        }
        if ($name == 'full_reactive_name') {
            $requestFromSQL = $this->DB->Query(
                "SELECT reactive.*,
            CONCAT( reactive_model.name,' (',reactive_pure.short_name,') - ', reactive.doc_name) as full_name
            FROM reactive
            JOIN reactive_model ON reactive.id_reactive_model = reactive_model.id
            JOIN reactive_pure ON reactive.id_pure = reactive_pure.id            
             "
            );
        }
        if ($name == 'getList') {
            $requestFromSQL = $this->DB->Query(
                "SELECT reactive_model.name, reactive_model.id, reactive.number, reactive_model.is_precursor, reactive_model.is_reactive,
                reactive.doc_name, aggregate_state.name as aggregate_name,reactive_pure.short_name, ba_laba.short_name as lab_id, b_user.NAME as user_name, b_user.LAST_NAME as user_last,
                reactive_receive.*, 
            CONCAT( reactive_model.name,' (',reactive_pure.short_name,') - ', reactive.doc_name) as full_name,
            CONCAT( reactive_receive.quantity,' ',unit_of_measurement.name) as full_quantity
            FROM reactive_receive 
            LEFT JOIN reactive ON reactive.id = reactive_receive.id_reactive
            LEFT JOIN reactive_pure ON reactive.id_pure = reactive_pure.id           
            LEFT JOIN reactive_model ON reactive.id_reactive_model = reactive_model.id
            LEFT JOIN unit_of_measurement ON reactive_receive.id_unit_of_measurement= unit_of_measurement.id
            LEFT JOIN aggregate_state ON reactive_model.id_aggregate_state = aggregate_state.id
            JOIN ba_laba ON reactive_receive.laboratory_id = ba_laba.id_dep
            JOIN b_user ON b_user.ID = reactive_receive.creater
            WHERE {$where}
            ORDER BY {$order}
            $limit
        "
            );

			$dataTotal = $this->DB->Query(
				"SELECT reactive_model.name, reactive_model.id, reactive.number, reactive_model.is_precursor, reactive_model.is_reactive,
                reactive.doc_name, aggregate_state.name as aggregate_name,reactive_pure.short_name, ba_laba.short_name as lab_id,
                reactive_receive.*, ba_laba.short_name as lab, b_user.NAME as user_name, b_user.LAST_NAME as user_last,
            CONCAT( reactive_model.name,' (',reactive_pure.short_name,') - ', reactive.doc_name) as full_name,
            CONCAT( reactive_receive.quantity,' ',unit_of_measurement.name) as full_quantity
            FROM reactive_receive 
            LEFT JOIN reactive ON reactive.id = reactive_receive.id_reactive
            LEFT JOIN reactive_pure ON reactive.id_pure = reactive_pure.id           
            LEFT JOIN reactive_model ON reactive.id_reactive_model = reactive_model.id
            LEFT JOIN unit_of_measurement ON reactive_receive.id_unit_of_measurement= unit_of_measurement.id
            LEFT JOIN aggregate_state ON reactive_model.id_aggregate_state = aggregate_state.id
            LEFT JOIN ba_laba ON reactive_receive.laboratory_id = ba_laba.id_dep
            JOIN b_user ON b_user.ID = reactive_receive.creater"
			)->SelectedRowsCount();

			$dataFiltered = $this->DB->Query(
				"SELECT reactive_model.name, reactive_model.id, reactive.number, reactive_model.is_precursor, reactive_model.is_reactive,
                reactive.doc_name, aggregate_state.name as aggregate_name,reactive_pure.short_name, ba_laba.short_name as lab_id,
                reactive_receive.*, ba_laba.short_name as lab, b_user.NAME as user_name, b_user.LAST_NAME as user_last,
            CONCAT( reactive_model.name,' (',reactive_pure.short_name,') - ', reactive.doc_name) as full_name,
            CONCAT( reactive_receive.quantity,' ',unit_of_measurement.name) as full_quantity
            FROM reactive_receive 
            LEFT JOIN reactive ON reactive.id = reactive_receive.id_reactive
            LEFT JOIN reactive_pure ON reactive.id_pure = reactive_pure.id           
            LEFT JOIN reactive_model ON reactive.id_reactive_model = reactive_model.id
            LEFT JOIN unit_of_measurement ON reactive_receive.id_unit_of_measurement= unit_of_measurement.id
            LEFT JOIN aggregate_state ON reactive_model.id_aggregate_state = aggregate_state.id
            LEFT JOIN ba_laba ON reactive_receive.laboratory_id = ba_laba.id_dep
            LEFT JOIN b_user ON b_user.ID = reactive_receive.creater
                    WHERE {$where}"
			)->SelectedRowsCount();

			$response['recordsTotal'] = $dataTotal;
			$response['recordsFiltered'] = $dataFiltered;
        }
        if ($name == 'getListReactive') {
            $requestFromSQL = $this->DB->Query(
                "SELECT reactive_model.name,reactive_model.id, reactive_model.is_precursor, ba_laba.short_name as lab, reactive_model.is_reactive,
                reactive.doc_name, aggregate_state.name as aggregate_name, reactive_pure.short_name,
            CONCAT( reactive_pure.name,' (',reactive_pure.short_name,')') as pure_name
            FROM reactive_model
            LEFT JOIN reactive ON reactive.id_reactive_model = reactive_model.id
            LEFT JOIN reactive_pure ON reactive.id_pure = reactive_pure.id           
            LEFT JOIN aggregate_state ON reactive_model.id_aggregate_state = aggregate_state.id
            LEFT JOIN ba_laba ON reactive.laba_id = ba_laba.id_dep
            WHERE $where
            ORDER BY $order
            $limit
        ");

			$dataTotal = $this->DB->Query(
				"SELECT reactive_model.name,reactive_model.id, reactive_model.is_precursor, ba_laba.short_name as lab, reactive_model.is_reactive,
                reactive.doc_name, aggregate_state.name as aggregate_name, reactive_pure.short_name,
            CONCAT( reactive_pure.name,' (',reactive_pure.short_name,')') as pure_name
            FROM reactive_model
            LEFT JOIN reactive ON reactive.id_reactive_model = reactive_model.id
            LEFT JOIN reactive_pure ON reactive.id_pure = reactive_pure.id           
            LEFT JOIN aggregate_state ON reactive_model.id_aggregate_state = aggregate_state.id
            LEFT JOIN ba_laba ON reactive.laba_id = ba_laba.id_dep"
			)->SelectedRowsCount();

			$dataFiltered = $this->DB->Query(
				"SELECT reactive_model.name,reactive_model.id,reactive_model.is_precursor, ba_laba.short_name as lab, reactive_model.is_reactive,
                reactive.doc_name, aggregate_state.name as aggregate_name, reactive_pure.short_name,
            CONCAT( reactive_pure.name,' (',reactive_pure.short_name,')') as pure_name
            FROM reactive_model
            LEFT JOIN reactive ON reactive.id_reactive_model = reactive_model.id
            LEFT JOIN reactive_pure ON reactive.id_pure = reactive_pure.id           
            LEFT JOIN aggregate_state ON reactive_model.id_aggregate_state = aggregate_state.id
            LEFT JOIN ba_laba ON reactive.laba_id = ba_laba.id_dep
                    WHERE {$where}"
			)->SelectedRowsCount();

			$response['recordsTotal'] = $dataTotal;
			$response['recordsFiltered'] = $dataFiltered;

		}
//		var_dump($dataTotal, $dataFiltered);

//
        if ($name == 'whereReactiveNumber') {
            $requestFromSQL = $this->DB->Query(
                "SELECT number from reactive where number REGEXP" . '"[[:<:]]' . $where . '[[:>:]]"'
            );
        }
        if ($name == 'whereModelReactiveName') {
            $requestFromSQL = $this->DB->Query(
                "SELECT name from reactive_model where name REGEXP" . '"[[:<:]]' . $where . '[[:>:]]"'
            );
        }

        if ($name == 'inReactivePure') {
            $requestFromSQL = $this->DB->Query(
                "SELECT * from reactive where id_reactive_model IN ($where) AND id_pure IN ($order)"
            );
        }
        if ($name == 'IDReactiveName') {
            $requestFromSQL = $this->DB->Query(
                "SELECT * from reactive_model where id IN ($where)"
            );
        }
        while ($row = $requestFromSQL->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    public function checkData(array $data, $type = null): bool
    {
        /* if (count($data) > 1 && $type = null) {
             echo('Массив должен быть из одного элемента или введите тип проверки');
             exit;
         }*/
        if ($type == null) {
            $type = array_key_first($data);
            $data = $data[$type];
        }

        if ($type == 'reactive_model') {
            $modelReactiveName = $this->getFromSQL('whereModelReactiveName', $data['name']);
            return count($modelReactiveName) != 0;
        }
        if ($type == 'reactiveNumber') {
            $numberReactive = $this->getFromSQL('whereReactiveNumber', $data['number']);
            return count($numberReactive) != 0;
        }

        if ($type == 'reactivePure') {
            $numberReactive = $this->getFromSQL('inReactivePure', $data['id_reactive_model'], $data['id_pure']);
            return count($numberReactive) != 0;
        }
        return true;
    }

    public function getByID(string $name, $ID): string
    {
        $getFrom = [
            'reactiveName' => 'IDReactiveName'
        ];

        $getName = $this->getFromSQL($getFrom[$name], $ID);
        return array_values_first($getName[0]);
    }

	/**
	 * @param $id
	 * @return false|string
	 */
    public function getReactiveInfo($id)
	{
		$result = $this->DB->Query("SELECT rm.id, rm.name as rmName, rm.is_precursor, r.doc_name, r.number, r.id as rId, r.id_pure, r.laba_id,
 									ags.name, ags.id as agsId, rp.name as rpName, rp.short_name, rm.is_reactive
 									FROM reactive_model as rm
									LEFT JOIN reactive as r ON rm.id = r.id_reactive_model
									LEFT JOIN aggregate_state as ags ON rm.id_aggregate_state = ags.id
									LEFT JOIN reactive_pure as rp ON r.id_pure = rp.id
									WHERE rm.id = {$id}")->Fetch();

		return  json_encode($result, JSON_UNESCAPED_UNICODE);

	}

    /**
     * @param $id
     */
	public function deleteReactive($id)
	{
		$this->DB->Query("DELETE FROM reactive WHERE id_reactive_model = {$id}");
		$this->DB->Query("DELETE FROM reactive_model WHERE id = {$id}");
	}

	public function getBatchControl($id)
    {
        $results = [];

        $res = $this->DB->Query("SELECT * FROM `reactive_control` WHERE `batch_id` = {$id}");

        while ($row = $res->Fetch()) {
            $results[] = $row;
        }

        return $results;
    }

}

