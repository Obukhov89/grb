<?php

use Bitrix\Main\Loader;

/**
 * Страница по-умолчанию
 * Class IndexController
 */
class IndexController extends Controller
{
    public function index()
    {
        $this->redirect('/request/new/');
    }

    public function info()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        phpinfo();
    }


    /**
     * Устанавливает статус успешно завершено, если заявка старая, по ней все сделано, но статус не сменен
     */
    public function setStatusWON()
    {
        return;
        global $DB;

        $requestModel = new Request();

        $reqSql = $DB->Query("
                        select distinct tz.REQUEST_TITLE, tz.ID, tz.ID_Z, tz.OPLATA, tz.PRICE, year(tz.DATE_CREATE_TIMESTAMP), tz.STAGE_ID 
                        from ba_tz tz, AKT_VR act
                        where 
                          tz.OPLATA = tz.PRICE 
                          and (tz.STAGE_ID = 2 or tz.STAGE_ID = 4) 
                          and (year(tz.DATE_CREATE_TIMESTAMP) = 2020 or year(tz.DATE_CREATE_TIMESTAMP) = 2021)
                          and tz.ID = act.TZ_ID
                          and (act.NUMBER is not null and act.NUMBER not like 'Ждем ответ от 1С')
                        order by
                          year(tz.DATE_CREATE_TIMESTAMP), tz.ID_Z
                        ");

        $i = 0;
        while ($row = $reqSql->Fetch()) {
            $requestModel->updateStageDeal($row['ID_Z'], 'WON', 6);

            $i++;
        }
        echo 'OK ';
        echo $i;
    }


    /**
     * устанавливает статус заявки ПРОИГРАНА если она старая и по ней ничего не происходит
     */
    public function setStatusLOSE()
    {
        return;
        global $DB;

        $requestModel = new Request();

        $reqSql = $DB->Query("
                        select distinct tz.REQUEST_TITLE, tz.ID, tz.ID_Z, tz.OPLATA, tz.PRICE, year(tz.DATE_CREATE_TIMESTAMP), tz.STAGE_ID 
                        from ba_tz tz
                        where 
                          tz.STAGE_ID IN ('NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING') 
                          and (year(tz.DATE_CREATE_TIMESTAMP) = 2020 or year(tz.DATE_CREATE_TIMESTAMP) = 2021)
                          AND IF(tz.ACT_NUM, TRUE, FALSE) = FALSE
                        order by
                          year(tz.DATE_CREATE_TIMESTAMP), tz.ID_Z
                        ");

        $i = 0;
        while ($row = $reqSql->Fetch()) {
            $requestModel->updateStageDeal($row['ID_Z'], 'LOSE', 7);

            $i++;
        }
        echo 'OK ';
        echo $i;
    }


    public function updateGostNewToOld($methodId = '')
    {
        return;
        global $DB;

        $where = "1";
        if ($methodId > 0) {
            $where = "um.id = {$methodId}";
        }

        $methodModel = new Methods();

        $reqSql = $DB->Query("
            select 
                   ug.reg_doc, ug.year, ug.description, ug.code_eaes, ug.code_okpd2, ug.materials,
                   um.*,
                   ud.unit_rus, ud.name unit_name
            from ulab_gost as ug
            inner join ulab_methods as um on um.gost_id = ug.id
            inner join ulab_dimension as ud on ud.id = um.unit_id
            where 
                  um.in_field = 1 and um.is_confirm = 1 and um.id in (select ulab_method_id from ba_gost where ulab_method_id > 0) and {$where}
        ");

        $i = 0;
        while ($row = $reqSql->Fetch()) {
            $baGost = $DB->Query("select * from ba_gost where ulab_method_id = {$row['id']}")->Fetch();

            if (empty($baGost)) {
                continue;
            }

            $laba = $methodModel->getLab($row['id']);
            $room = $methodModel->getRoom($row['id']);
            $ass = $methodModel->getAssigned($row['id']);
            $oborud = $methodModel->getOborud($row['id']);
            $infelicity = $methodModel->getUncertainty($row['id']);

            $data = [
                'GOST' => "'{$row['reg_doc']}'",
                'SPECIFICATION' => "'{$row['name']}'",
                'GOST_PUNKT' => "'{$row['clause']}'",
                'NAME_GOST' => "'{$row['description']}'",
                'NORM1' => "'{$row['definition_range_1']}'",
                'NORM2' => "'{$row['definition_range_2']}'",
                'MATCH_MANUAL' => 0,
                'NORM_TEXT' => $row['is_text_norm'],
                'ED' => "'{$row['unit_rus']}'",
                'PRICE' => "'{$row['price']}'",
                'IN_OA' => 1,
                'GOST_TYPE' => "'metodic'",
                'TNVED' => "'{$row['code_eaes']}'",
                'GOST_OBJECT' => "'{$row['materials']}'",
                'RES_TEXT' => $row['is_text_fact'],
                'GOST_YEAR' => $row['year'],
                'NUM_OA' => $row['num_oa'],
                'NUM_OA_NEW' => $row['num_oa'],
                'ACCURACY' => $row['decimal_places'],
                'OKPD' => "'{$row['code_okpd2']}'",
                'NON_ACTUAL' => 0,
                'PRESSURE' => $row['is_not_cond_pressure'],
                'HUMIDITY' => $row['is_not_cond_wet'],
                'TEMPERATURE' => $row['is_not_cond_temp'],
                'test_method_id' => $row['test_method_id'],
                'ulab_method_id' => $row['id'],
                'CHECKED' => $row['is_confirm'],
            ];

            if ( $row['definition_range_type'] == 1 ) {
                $data['IN_OUT'] = 0;
            }
            if ( $row['definition_range_type'] == 2 ) {
                $data['IN_OUT'] = 1;
            }
            if ( $row['definition_range_type'] == 3 ) {
                $data['UNNORM'] = 1;
            }

            $unserPlace = unserialize($baGost['PLACE']);
            foreach ($room as $item) {
                if (!in_array($item, $unserPlace)) {
                    $unserPlace[] = $item;
                }
            }
            $place = serialize($unserPlace);

            $press = serialize([$row['cond_pressure_1'], $row['cond_pressure_2']]);
            $temp = serialize([$row['cond_temp_1'], $row['cond_temp_2']]);
            $vlag = serialize([$row['cond_wet_1'], $row['cond_wet_2']]);

            $unserAssign = unserialize($baGost['ASSIGNED']);
            foreach ($ass as $item) {
                if (!in_array($item, $unserAssign)) {
                    $unserAssign[] = $item;
                }
            }
            $assign = serialize($unserAssign);

            $data['PLACE'] = "'{$place}'";
            $data['ASSIGNED'] = "'{$assign}'";
            $data['VLAG'] = "'{$vlag}'";
            $data['TEMP'] = "'{$temp}'";
            $data['PRESS'] = "'{$press}'";

            foreach ($laba as $lab) {
                if ( $lab == 1 ) {
                    $data['LFHI'] = 1;
                } else {
                    $data['LFHI'] = 0;
                }
                if ( $lab == 3 ) {
                    $data['LFMI'] = 1;
                } else {
                    $data['LFMI'] = 0;
                }
                if ( $lab == 4 ) {
                    $data['DSL'] = 1;
                } else {
                    $data['DSL'] = 0;
                }
                if ( $lab == 2 ) {
                    $data['LSM'] = 1;
                } else {
                    $data['LSM'] = 0;
                }
                if ( $lab == 5 ) {
                    $data['OSK'] = 1;
                } else {
                    $data['OSK'] = 0;
                }
            }

            $DB->Update('ba_gost', $data, "where ID = {$baGost['ID']}");
//            $gostId = $baGost['ID'];
//
//            foreach ($infelicity as $item) {
//                $dataItem = [
//                    'gost_id' => $gostId,
//                    'diapason_from' => $item['uncertainty_1'],
//                    'diapason_to' => $item['uncertainty_2'],
//                    'infelicity' => $item['uncertainty_3'],
//                ];
//
//                $DB->Insert('infelicity', $dataItem);
//            }
//
//            foreach ($oborud as $item) {
//                $dataItem = [
//                    'GOST_ID' => $gostId,
//                    'OBORUD_ID' => $item['id_oborud'],
//                    'COMMENT' => "'{$item['comment']}'",
//                    'EQUIP_GOST' => "'{$item['gost']}'",
//                ];
//
//                $DB->Insert('ba_connection', $dataItem);
//            }

            $i++;
        }

        echo 'OK ' . $i;
    }


    public function ulGostToProbe()
    {
        return;
        global $DB;

        $sql = $DB->Query("select * from ulab_gost_to_probe");

        $i = 0;
        while ($row = $sql->Fetch()) {
            $idMethod = $DB->Query("select ulab_method_id from ba_gost where ID = {$row['method_id']}")->Fetch();

            if ( empty($idMethod['ulab_method_id']) ) {
                continue;
            }

            $DB->Update('ulab_gost_to_probe', ['new_method_id' => $idMethod['ulab_method_id']], "where id = {$row['id']}");

            ///////////////////////////////////////////////////////////////////////////////

            $cond = $DB->Query("select id from ulab_tech_condition where ba_gost_id = {$row['conditions_id']}")->Fetch();

            if ( empty($cond['id']) ) {
                continue;
            }

            $DB->Update('ulab_gost_to_probe', ['tech_condition_id' => $cond['id']], "where conditions_id = {$row['conditions_id']}");


            $i++;
//
//            echo $idMethod['ulab_method_id'];
//            echo '<br>';
        }
        echo 'OK ' . $i;
    }


    public function updateClauseTu()
    {
        global $DB;

        $sql = $DB->Query("select * from ulab_tech_condition");

        $i = 0;
        while ($row = $sql->Fetch()) {
            if ( empty($row['ba_gost_id']) ) {
                continue;
            }
            $gost = $DB->Query("select GOST_PUNKT from ba_gost where ID = {$row['ba_gost_id']}")->Fetch();

            if ( !empty($gost['GOST_PUNKT']) ) {
                $i++;
                $DB->Update('ulab_tech_condition', ['clause' => "'{$gost['GOST_PUNKT']}'"], "where id = {$row['id']}");
            }
        }

        echo 'OK ' . $i;
    }

	public function gostOldToNew($idOld)
	{
		global $DB;

		/** @var Methods $methodsModel */
		$methodsModel = $this->model('Methods');
		/** @var Gost $gostModel */
		$gostModel = $this->model('Gost');

		$resultAddMethod = 0;

		$sql = $DB->Query("SELECT * FROM ba_gost WHERE ID = {$idOld}");

		while ($row = $sql->Fetch()) {

			$gostName = trim($row['GOST']);
			$newGost = $DB->Query("select id from ulab_gost where reg_doc like '{$gostName}'")->Fetch();

			if ( empty($newGost['id']) ) {
				$dataGost = [
					'reg_doc' => trim($row['GOST']),
					'year' => $row['GOST_YEAR'],
					'description' => $row['NAME_GOST'],
					'code_eaes' => $row['TNVED'],
					'code_okpd2' => $row['OKPD'],
					'materials' => $row['GOST_OBJECT'],
				];
				$gostId = $gostModel->addGost($dataGost);
			} else {
				$gostId = $newGost['id'];
			}

			$ed = $DB->ForSql(trim($row['ED']));
			$unit = $DB->Query("SELECT id FROM ulab_dimension WHERE unit_rus = '{$ed}'")->Fetch();

			$temp = unserialize($row['TEMP']);
			$wet = unserialize($row['VLAG']);
			$pressure = unserialize($row['PRESS']);

			$rangeType = 1;
			if ( $row['UNNORM'] == 1 ) {
				$rangeType = 3;
			} else if ($row['IN_OUT']) {
				$rangeType = 2;
			}

			$data = [
				'gost_id' => $gostId,
				'unit_id' => $unit['id'] ?? 767,
				'name' => $row['SPECIFICATION'],
				'clause' => $row['GOST_PUNKT'],
				'decimal_places' => $row['ACCURACY'],
				'in_field' => $row['IN_OA'],
				'definition_range_1' => $row['NORM1'],
				'definition_range_2' => $row['NORM2'],
				'definition_range_type' => $rangeType,
				'cond_temp_1' => $temp[0],
				'cond_temp_2' => $temp[1],
				'is_not_cond_temp' => $row['TEMPERATURE'],
				'cond_wet_1' => $wet[0],
				'cond_wet_2' => $wet[1],
				'is_not_cond_wet' => $row['HUMIDITY'],
				'cond_pressure_1' => $pressure[0],
				'cond_pressure_2' => $pressure[1],
				'is_not_cond_pressure' => $row['PRESSURE'],
				'is_text_norm' => $row['NORM_TEXT'],
				'is_text_fact' => $row['RES_TEXT'],
				'price' => number_format($row['PRICE'], 2, '.', ''),
			];

			$resultAddMethod = $methodsModel->add($data);

			if ( !empty($resultAddMethod) ) {

				// laba
				$labIds = [];
				if ( $row['LFHI'] ) {
					$labIds[] = 1;
				}
				if ( $row['LFMI'] ) {
					$labIds[] = 3;
				}
				if ( $row['DSL'] ) {
					$labIds[] = 4;
				}
				if ( $row['LSM'] ) {
					$labIds[] = 2;
				}
				if ( $row['OSK'] ) {
					$labIds[] = 5;
				}

				$methodsModel->updateLab($resultAddMethod, $labIds);
				// \ laba

				// rooms
				$rooms = unserialize($row['PLACE']);

				$methodsModel->updateRoom($resultAddMethod, $rooms);
				// \ rooms

				// ass
				$ass = unserialize($row['ASSIGNED']);

				$methodsModel->updateAssigned($resultAddMethod, $ass);
				// \ ass

				// oborud
				$sqlOborud = $DB->Query("SELECT * FROM ba_connection WHERE GOST_ID = {$row['ID']}");

				$dataOborud = [];
				while ($rowOborud = $sqlOborud->Fetch()) {
					$dataOborud[] = [
						'gost' => $rowOborud['EQUIP_GOST']?? '-',
						'id_oborud' => $rowOborud['OBORUD_ID'],
						'comment' => $rowOborud['COMMENT'],
					];
				}

				$methodsModel->updateOborud($resultAddMethod, $dataOborud);
				// \ oborud

				// Расчет неопределенности
				$sqlInfelicity = $DB->Query("SELECT * FROM infelicity WHERE gost_id = {$row['ID']}");
				$dataInfelicity = [];
				while ($rowInfelicity = $sqlInfelicity->Fetch()) {
					$dataInfelicity[] = [
						'uncertainty_1' => $rowInfelicity['diapason_from']?? '-',
						'uncertainty_2' => $rowInfelicity['diapason_to'],
						'uncertainty_3' => $rowInfelicity['infelicity'],
					];
				}

				$methodsModel->updateUncertainty($resultAddMethod, $dataInfelicity);
				// \ Расчет неопределенности
			}
		}

		if ( !empty($resultAddMethod) ) {
			$this->showSuccessMessage('Успех');
			$this->redirect('/ulab/gost/method/' . $resultAddMethod);
		}
	}


    /**
     * перекинуть из ба_гост в ТУ по ид
     * @param $baGostId - ид из старой
     */
    public function tuOldToNew($baGostId)
    {
        global $DB;

        /** @var TechCondition $tuModel */
        $tuModel = $this->model('TechCondition');

        $resultAddMethod = 0;

        $sql = $DB->Query("SELECT * FROM ba_gost WHERE ID = {$baGostId}");

        while ($row = $sql->Fetch()) {

            $gostName = trim($row['GOST']);

            $ed = $DB->ForSql(trim($row['ED']));
            $unit = $DB->Query("SELECT id FROM ulab_dimension WHERE unit_rus = '{$ed}'")->Fetch();

            $rangeType = 1;
            if ( $row['UNNORM'] == 1 ) {
                $rangeType = 3;
            } else if ($row['IN_OUT']) {
                $rangeType = 2;
            }

            $data = [
                'reg_doc' => $gostName,
                'year' => $row['GOST_YEAR'],
                'clause' => $row['GOST_PUNKT'],
                'unit_id' => $unit['id'] ?? 767,
                'measured_properties_name' => $row['SPECIFICATION'],
                'name' => $row['NAME_GOST'],
                'type' => $row['GOST_TYPE'],
                'decimal_places' => $row['ACCURACY'],
                'definition_range_1' => $row['NORM1'],
                'definition_range_2' => $row['NORM2'],
                'definition_range_type' => $rangeType,
                'is_text_norm' => $row['NORM_TEXT'],
                'is_manual' => $row['MATCH_MANUAL'],
                'norm_comment' => $row['NORM_COMMENT'],
                'ba_gost_id' => $row['ID'],
                'dop_material' => $row['DOP'],
                'dop_value' => $row['VALUE_DOP'],
                'dop_norm' => $row['NORM_DOP'],
            ];

            $tuModel->add($data);

            $resultAddMethod++;
        }

        echo 'OK ' . $resultAddMethod;
    }

    public function scype()
	{
	    return;
	    global $DB;
		$res = $DB->Query("select gtp.*, bg.ulab_method_id from gost_to_probe as gtp
					left join probe_to_materials ptm on ptm.id = gtp.probe_id
					left join MATERIALS_TO_REQUESTS mtr on mtr.id = ptm.material_request_id
					left join ba_gost bg ON gtp.gost_method = bg.ID
					where mtr.ID_DEAL = 9393");

		while ($row = $res->Fetch()) {
			$DB->Query("UPDATE gost_to_probe SET new_method_id = {$row['ulab_method_id']} where id = {$row['id']}");

		}
	}


	public function setDepartmentProtocol()
    {
        return;
        global $DB;

        /** @var User $userModel */
        $userModel = $this->model('User');

        $sql = $DB->Query("select id, VERIFY from PROTOCOLS");

        while ($row = $sql->Fetch()) {
            $users = unserialize($row['VERIFY']);
            $dep = [];

            if ( empty($users) ) {
                continue;
            }

            foreach ($users as $user) {
                $data = [
                    'protocol_id' => $row['id'],
                    'department_id' => $userModel->getDepartmentByUserId($user)
                ];

                $DB->Insert('protocol_lab', $data);
            }
        }

        echo 'OK';
    }


    public function fillOborud()
    {
        global $DB;

        $DB->Update('ba_oborud', ['is_vagon' => 1], "where REG_NUM like '%B%' or REG_NUM like '%В%'");

        $sql = $DB->Query("select * from ba_oborud");

        while ($row = $sql->Fetch()) {
            $a = explode('-', $row['REG_NUM']);

            $b = end($a);

            if ( $b && is_numeric($b) ) {
                $DB->Update('ba_oborud', ['number' => $b], "where ID = {$row['ID']}");
            }
        }

        echo 'OK ';
    }


    public function sertificateOborud()
    {
        return;
        global $DB;

        $sql = $DB->Query("select * from b_verification_certificate");

        $result = [];

        while ($row = $sql->Fetch()) {
            $row['name_f'] = unserialize($row['name_f']);
            $row['file_address'] = unserialize($row['file_address']);
            $row['date_start_verification'] = unserialize($row['date_start_verification']);
            $row['date_end_verification'] = unserialize($row['date_end_verification']);

            for ( $i = 0; $i < count($row['name_f']); $i++ ) {
                if ( !empty($row['name_f'][$i]) ) {

                    $tmp = explode('/', $row['file_address'][$i]);
                    $fileName = end($tmp);

                    $data = [
                        'oborud_id' => $row['id_oborud'],
                        'name' => "'{$row['name_f'][$i]}'",
                        'file' => "'{$fileName}'",
                        'date_start' => "'{$row['date_start_verification'][$i]}'",
                        'date_end' => "'{$row['date_end_verification'][$i]}'",
                        'is_actual' => $row['relevance_certificate'] == $i,
                    ];

                    $DB->Insert('ba_oborud_certificate', $data);
                }
            }

            $result[] = $row;
        }

        echo 'OK';
//        echo '<pre>';

//        print_r($result);
    }

    public function copyFileOborudCert()
    {
        return;
        global $DB;

        $sql = $DB->Query("select * from ba_oborud_certificate where file <> ''");

        while ($row = $sql->Fetch()) {
            $uploadDescFileDir = $_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $row['oborud_id'] . '/' . $row['file'];
            $oldUploadDescFileDir = $_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $row['file'];
            if ( file_exists($oldUploadDescFileDir) ) {
                exec("cp {$oldUploadDescFileDir} {$uploadDescFileDir}");
            }
        }

        echo 'OK';
    }

    public function testEquipmentFsa()
    {
        echo '<pre>';
        $result = shell_exec("curl -X 'GET' 'http://5.143.238.171:8060/equipment' -H 'accept: */*' -H 'X-API-KEY: HARygsmRgpySl1meCfrpoBFwAeYnVOLQ0Dp48KlIOSesgn8WB!V3v5gwKyurGXsAGKW1zoQeY2n64J6b7EvYRCk0a#5ITp!s7WAHnaBTmBLvmaea2AHsvnP5!GEF5eeA'");

        print_r( json_decode($result, true) );

        exit;
        $ch = curl_init('http://5.143.238.171:8060/equipment');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: /',
            'X-API-KEY: HARygsmRgpySl1meCfrpoBFwAeYnVOLQ0Dp48KlIOSesgn8WB!V3v5gwKyurGXsAGKW1zoQeY2n64J6b7EvYRCk0a#5ITp!s7WAHnaBTmBLvmaea2AHsvnP5!GEF5eeA'
        ]);

        $json = curl_exec($ch);

        if (curl_errno($ch)) {
            $msg = curl_error($ch);
            curl_close($ch);

            echo "Ошибка: {$msg}";
        }

        curl_close($ch);

        print_r( json_decode($json, true) );
    }


    public function oborubPassport()
    {
        return;
        global $DB;

        $model = new Model();

        $sql = $DB->Query("select * from ba_oborud");

        while ($row = $sql->Fetch()) {
            $files = $model->getFilesFromDir($_SERVER['DOCUMENT_ROOT'] . '/file_oborud/' . $row['ID'] . '/passport');

            if ( empty($files) ) {continue;}

            $file = "'{$files[0]}'";

            $DB->Update('ba_oborud', ['passport_pdf' => $file], "where ID = {$row['ID']}");

//            echo '<pre>';
//
//            print_r($files);
//
//            echo '</pre><hr>';
        }

        echo 'OK';
    }

    public function testCopyProbe()
	{
		global $DB;
		/** @var Requirement $requirementModel */
		$requirementModel = $this->model('Requirement');


		$res = $DB->Query("select ugtp.id from ulab_gost_to_probe ugtp
					inner join ulab_material_to_request as umtr on ugtp.material_to_request_id = umtr.id
					where umtr.deal_id = 13105 and ugtp.gost_number > 5");
//		where umtr.deal_id = 12923 and ugtp.assigned_id != 11");
//					where ugtp.material_to_request_id in (70051,70052,70053,70054,70055,70056,70057,70058,70059,70060) and ugtp.method_id != 2675");

//
//		while ($row = $res->fetch()) {
//			$DB->Query("delete from ulab_gost_to_probe where id = {$row['id']}");
//		}
////
//		exit();

//		$umtrId = [66747,66748,66749,66750,66751,66752,66753,66754,66755,66756];
//////
//		foreach ($umtrId as $id) {
//			$requirementModel->copyProbeToDeal($id, 12941);
//		}

//Групповое добавление ответственных
//		$res = $DB->Query("select ugtp.id from ulab_gost_to_probe ugtp
//					inner join ulab_material_to_request as umtr on ugtp.material_to_request_id = umtr.id
//				where umtr.deal_id = 12915 and ugtp.method_id in (714, 709)");
//
//		while ($row = $res->fetch()) {
//			$DB->Query("update ulab_gost_to_probe set assigned_id = 154 where id = {$row['id']}");
//		}
	}

	public function setClientIdForSO()
	{
		global $DB;

		$res = $DB->Query("select d.*, bt.COMPANY_ID from DOGOVOR as d
 							left join ba_tz as bt on d.DEAL_ID = bt.ID_Z
 							where d.CONTRACT_TYPE = 'счет-оферта' and bt.COMPANY_ID is not null");

		while ($row= $res->fetch()) {
			$data = ['CLIENT_ID' => $row['COMPANY_ID']];

			$DB->Update('DOGOVOR', $data, "where ID = {$row['ID']}");
		}

	}


	public function updateMaterialDeal()
    {
        global $DB;

        $res = $DB->Query(
            "select umtr.deal_id, GROUP_CONCAT(distinct m.`name` SEPARATOR ', ') as material 
            from ulab_material_to_request as umtr
            inner join MATERIALS as m on m.ID = umtr.material_id and m.NAME <> ''
            where umtr.deal_id > 0
            group by umtr.deal_id"
        );

        $i = 0;
        while ($row = $res->fetch()) {
            $i++;
            $material = trim($row['material']);
            $DB->Update("ba_tz", ['MATERIAL' => "'{$material}'"], "where ID_Z = {$row['deal_id']}");
        }

        echo 'OK ' . $i;
    }


    public function testPdf()
    {
        $file = UPLOAD_DIR . '/test/1.pdf';

        $this->addJS("/assets/plugins/pdf-lib/pdf-lib-1.4.0.js");

        $this->data['link'] = UPLOAD_URL . '/test/1.pdf';

        $this->view('pdf');
    }

//    public function testInv()
//    {
//		$requestModel = new Request();
//		$requestModel->pre($requestModel->getNextInvoiceNumber());
//
//    }

	public function setProbeXlsx()
	{
		/** @var Methods $method */
		$method = $this->model('Methods');

		$method->setProbeListTest();
	}

    public function connectTzWithDogovor()
    {
        global $DB;

        $response = $DB->Query(
            "
                SELECT td.ID as tdoc_id, d.ID as d_id
                FROM TZ_DOC td
                INNER JOIN ba_tz bt ON bt.ID = td.TZ_ID
                INNER JOIN DEALS_TO_CONTRACTS dtc ON dtc.ID_DEAL = bt.ID_Z
                INNER JOIN DOGOVOR d ON d.ID = dtc.ID_CONTRACT
                WHERE td.ID >= 5871
            "
        );

        while($row = $response->Fetch()) {
            $DB->Query(
                "
                  UPDATE TZ_DOC SET dogovor_id = '{$row['d_id']}' WHERE ID = '{$row['tdoc_id']}' 
                "
            );
        }
    }

    public function changeTzNumbers()
    {
        global $DB;

        $response = $DB->Query(
            "
                SELECT td.ID tdoc_id, bt.request_number
                FROM TZ_DOC td
                INNER JOIN ba_tz bt ON bt.ID = td.TZ_ID
                WHERE YEAR(td.DATE) = '2025'
            "
        );

        while($row = $response->Fetch()) {
            $DB->Query(
                "
                UPDATE TZ_DOC SET number = '{$row['request_number']}' WHERE ID = '{$row['tdoc_id']}'
                "
            );
        }
    }

    public function changeDogovors()
    {
        global $DB;

        $response = $DB->Query("
                SELECT *
                FROM ba_tz 
                WHERE COMPANY_ID = 1902 AND YEAR(DATE_CREATE_TIMESTAMP) = '2023'
        ");

        while($row = $response->Fetch()) {
            $DB->Query("UPDATE ba_tz SET DOGOVOR_NUM = 1644 WHERE ID = '{$row['ID']}'");
            $DB->Query("UPDATE DEALS_TO_CONTRACTS SET ID_CONTRACT = 1644 WHERE ID_DEAL = '{$row['ID_Z']}'");
        }
    }

    public function cipherProbesExcel($dealId)
    {
        global $DB;

        $fileContent = readfile("/excelProbes/{$dealId}.xlsx");

        echo "<pre>";
        print_r($fileContent);
        echo "</pre>";
    }

    public function updateDogovorTypes()
    {
        global $DB;

        $response = $DB->Query(
            "
            SELECT * FROM DOGOVOR  
            "
        );

        while($row = $response->Fetch()) {
            if($row['type_id'] == 1) {
                $DB->Query("UPDATE DOGOVOR SET dogovor_type_id = 1 WHERE ID = '{$row['ID']}'");
            }else if($row['type_id'] == 2) {
                $DB->Query("UPDATE DOGOVOR SET dogovor_type_id = 2 WHERE ID = '{$row['ID']}'");
            }
        }
    }

    public function loadOldDogovorors()
    {
        global $DB;

        $response = $DB->Query(
            "
            SELECT * FROM DOGOVOR WHERE PDF IS NOT NULL AND PDF <> ''
            "
        );

        while($row = $response->Fetch()) {
            $date = $row['SEND_DATE'] ? $row['SEND_DATE'] : $row['DATE'];

            $DB->Query(
                "
                INSERT INTO dogovor_documents(actual_version, type_id, dogovor_id, is_scan, date)
                VALUES('{$row['PDF']}', 3, '{$row['ID']}', 0, '{$date}')
                "
            );
        }
    }

    public function changeOldStatusListToNewList()
    {
        global $DB;

        $DB->Query("DELETE FROM tz_status_history");

        $requestResponse = $DB->Query(
            "
            SELECT bt.ID_Z, ptl.id as 'work_in_lab', ptl.accept_date, bt.STAGE_ID as 'close_request', av.SEND_DATE, bt.DATE_SOZD, bt.lose_comment
                 , bt.TYPE_ID, bt.TAKEN_ID_DEAL
            FROM ba_tz bt
            INNER JOIN ulab_material_to_request umtr ON umtr.deal_id = bt.ID_Z
            LEFT JOIN probe_to_lab ptl ON ptl.umtr_id = umtr.id AND ptl.accept_probe = 1
            LEFT JOIN AKT_VR av ON av.TZ_ID = bt.ID
            WHERE (bt.DATE_SOZD between '2024-01-01' and '2025-12-31') 
            GROUP BY bt.ID_Z
            ORDER BY bt.ID DESC
            "
        );

        while ($row = $requestResponse->Fetch()) {
            $DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 1, '{$row['DATE_SOZD']}')");

            if ($row['work_in_lab']) {
                $DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 2, '{$row['accept_date']}')");
                $DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 3, '{$row['accept_date']}')");
            }

            if($row['close_request'] == 2 || $row['close_request'] == 4 || $row['close_request'] == 'WON') {
                $DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 5, '{$row['accept_date']}')");

                if($row['TYPE_ID'] == "COMPLEX" || $row['TYPE_ID'] == '7' || $row['TAKEN_ID_DEAL']) {
                    $DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 6, '{$row['accept_date']}')");
                }
            }

            if($row['close_request'] == 'WON-NO-ACT' || $row['close_request'] == 4 || $row['close_request'] == 'WON') {
                $DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 6, '{$row['accept_date']}')");
            }

            if ($row['SEND_DATE']) {
                $DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 6, '{$row['SEND_DATE']}')");
            }

            if($row['close_request'] == 'LOSE' || $row['close_request'] == '5' || $row['close_request'] == '6'
                || $row['close_request'] == '7' || $row['close_request'] == '8' || $row['close_request'] == '9'
                || $row['close_request'] == '10' || $row['close_request'] == '11') {
                $DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 7, '{$row['accept_date']}')");

                if (!$row['lose_comment']) {
                    $DB->Query("UPDATE ba_tz SET lose_comment = '-' WHERE ID_Z = '{$row['ID_Z']}'");
                }
            }

            $countUmtr = $DB->Query(
                "
                SELECT ugtp.id
                FROM ulab_material_to_request umtr
                INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
                WHERE umtr.deal_id = '{$row['ID_Z']}'
                "
            )->SelectedRowsCount();

            if ($countUmtr <= 0) continue;

            $umtrResponse = $DB->Query(
                "
                SELECT ugtp.actual_value, ugtp.measuring_sheet
                FROM ulab_material_to_request umtr
                INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
                WHERE umtr.deal_id = '{$row['ID_Z']}'
                "
            );

            while($r = $umtrResponse->Fetch()) {
                if (!$r['actual_value'] && strval($r['actual_value']) != '0' && !$r['measuring_sheet']) {
                    continue 2;
                }
            }

            $DB->Query("INSERT INTO tz_status_history(deal_id, user_id, status_id, date) VALUES('{$row['ID_Z']}', 61, 4, '{$row['accept_date']}')");
        }
    }

    public function correctSuccessStatuses()
    {
        global $DB;

        $requestResponse = $DB->Query(
            "
            SELECT bt.ID_Z, bt.dateEnd, tsh.id
            FROM ba_tz bt
            INNER JOIN tz_status_history tsh ON tsh.deal_id = bt.ID_Z AND tsh.status_id = 5
            "
        );

        while ($row = $requestResponse->Fetch()) {
            if (!$row['dateEnd']) {
                $DB->Query("DELETE FROM tz_status_history WHERE id = '{$row['id']}'");

                $lastSuccessStatus = $DB->Query(
                    "
                    SELECT id FROM tz_status_history WHERE deal_id = '{$row['ID_Z']}' AND status_id = 6
                    "
                )->Fetch()['id'];

                if ($lastSuccessStatus) {
                    $DB->Query("DELETE FROM tz_status_history WHERE id = '{$lastSuccessStatus}'");
                }
            }
        }
    }

    public function getBitrixFolders()
    {
        $webhookUrl = 'https://ulab.niistrom.pro/rest/61/xg2s53ej268lr63r/disk.folder.getchildren';
        $folderId = 1; // 0 — корневая папка (можно указать ID конкретной папки)

        // Параметры запроса
        $queryData = [
            'id' => $folderId,
            'filter' => [
                'TYPE' => 'folder' // Только папки (исключает файлы)
            ]
        ];

        // Отправляем запрос
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webhookUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($queryData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        // Обработка ответа
        $data = json_decode($response, true);

        if (isset($data['result'])) {
            echo "<h2>Список папок:</h2>";
            foreach ($data['result'] as $folder) {
                echo "ID: <b>{$folder['ID']}</b> | Название: <b>{$folder['NAME']}</b><br>";
            }
        } else {
            echo "Ошибка: " . print_r($data, true);
        }
    }

    function checkFolderAccess($folderId) {
        $webhookUrl = 'https://ulab.niistrom.pro/rest/61/xg2s53ej268lr63r/disk.folder.get';

        $queryData = [
            'id' => $folderId
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $webhookUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($queryData),
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['result'])) {
            echo "<pre>";
            print_r( [
                'exists' => true,
                'name' => $data['result']['NAME'],
                'rights' => $data['result']['RIGHTS'] // Права доступа
            ]);
            echo "</pre>";
        } else {
            return [
                'exists' => false,
                'error' => $data['error_description'] ?? 'Unknown error'
            ];
        }
    }
}

