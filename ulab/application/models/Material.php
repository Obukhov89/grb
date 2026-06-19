<?php
/**
 * Модель для работы с материалами
 * Class Material
 */
class Material extends Model
{
	CONST SIEVE_ARR = ['200', '120', '100', '80', '60', '40', '31,5', '22,4', '20', '16', '15', '11,2', '10', '8',
		'5', '4', '2,5', '2', '1,25', '1', '0,63', '0,5', '0,315', '0,25', '0,16', '0,125', '0,1', '0,071', '0,063',
		'0,05', 'менее 0,05', '5,6', '45', '63', '90', '12,5', '7,5', '25', '50', '30', '87,5', '70', '55', '126',
		'180', '2,8', '3', '17,5', 'менее 0,16', 'менее 0,125', '125', '56 мкм', '20 мкм', '10 мкм', '5 мкм',
		'2 мкм', 'менее 2 мкм', '150', '105', '35', '1000', '850', '700', '600', '500', 'менее 500', 'менее 1 мм',
		'0,8', '0,4', '0,2', '1.6', 'менее 2,5 мм', 'менее 5 мм', 'менее 10 мм', 'менее 20 мм'];

	/**
     * @return array
     */
    public function getList($isActive = false): array
    {

        $str = '';
        if ( $isActive ) {
            $str = "and is_active = 1";
        }
        $result = [];
        $materials = $this->DB->Query(
            "SELECT * FROM `MATERIALS` 
            WHERE `NAME` <> '' {$str}
            group by `NAME`, `GROUPS`
            ORDER BY `NAME` ASC"
        );

        while ($row = $materials->Fetch()) {
            $row['GROUPS'] = unserialize($row['GROUPS']);

            $result[] = $row;
        }

        return $result;
    }


    /**
     * @return array
     */
    public function getNewSchemeMaterial()
    {
        $sql = $this->DB->Query(
            "select distinct sm.material_id as id, m.NAME from ulab_scheme_material_new as sm
                    inner join MATERIALS as m on sm.material_id = m.ID"
        );

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param $materialId
     * @return array
     */
    public function getNewScheme($materialId)
    {
        $sql = $this->DB->Query(
            "select * from ulab_scheme_material_new as sm
                    where sm.material_id = {$materialId}"
        );

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param $schemeId
     * @return array
     */
    public function getNewSchemeMethods($schemeId)
    {
        $methodModel = new Methods();

        $sql = $this->DB->Query(
            "select * from ulab_scheme_material_method_new as smm
                    where smm.usm_id = {$schemeId}"
        );

        $result = [];

        while ($row = $sql->Fetch()) {
            $methodInfo = $methodModel->get($row['method_id']);

            $methodInfo['smm_id'] = $row['id'];

            $result[] = $methodInfo;
        }

        return $result;
    }


    /**
     * @param $smmId
     */
    public function deleteNewSchemeMethods($smmId)
    {
        $this->DB->Query("delete from ulab_scheme_material_method_new where id = {$smmId}");
    }


    /**
     * @param $schemeId
     * @param $methods
     */
    public function addNewSchemeMethods($schemeId, $methods)
    {
        foreach ($methods as $methodId) {
            $this->DB->Insert('ulab_scheme_material_method_new', ['usm_id' => $schemeId, 'method_id' => $methodId]);
        }
    }


    /**
     * @param $id
     */
    public function changeActiveMaterial($id)
    {
        $this->DB->Query("update `MATERIALS` set `is_active` = ! `is_active` where ID = {$id}");
    }


    /**
     * @param int $materialId
     * @return array|false
     */
    public function getById(int $materialId)
    {
        $material = $this->DB->Query("SELECT * FROM `MATERIALS` WHERE ID = {$materialId}");

        return $material->Fetch();
    }

	/**
	 * @param int $umtrId
	 */
    public function getMaterialByUmtrId(int $umtrId)
	{
		$material = $this->DB->Query("SELECT material_id FROM `ulab_material_to_request` WHERE id = {$umtrId}");

		return $material->Fetch()['material_id'];
	}

    /**
     * @param $id
     */
    public function deleteMaterial($id)
    {
        $this->DB->Query("delete FROM `MATERIALS` WHERE ID = {$id}");
    }


    /**
     * получает список групп материалов
     * @param $materialId
     * @return array
     */
    public function getGroupMaterial($materialId)
    {

        $sql = $this->DB->Query(
            "select 
                        mg.id as group_id, mg.name as group_name, mgtu.* 
                    from materials_groups as mg
                    left join materials_groups_tu as mgtu on mg.id = mgtu.materials_groups_id
                    where mg.material_id = {$materialId}
                    order by mg.id"
        );

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[$row['group_id']]['name'] = $row['group_name'];
            $result[$row['group_id']]['id'] = $row['group_id'];

            $result[$row['group_id']]['tu'][] = [
                'id' => $row['id'],
                'tu_id' => $row['tu_id'],
                'comparison_val_1' => $row['comparison_val_1'],
                'val_1' => $row['val_1'],
                'comparison_val_2' => $row['comparison_val_2'],
                'val_2' => $row['val_2'],
            ];
        }

        return $result;
    }


    /**
     * Добавляем группу материала
     * @param $materialId
     * @param $groupName
     * @return false|mixed|string
     */
    public function addGroupMaterial($materialId, $groupName)
    {
        $groupName = StringHelper::removeSpace($groupName);
        $sqlData = $this->prepearTableData('materials_groups', ['material_id' => $materialId, 'name' => $groupName]);

        return $this->DB->Insert('materials_groups', $sqlData);
    }


    /**
     * Добавляем ТУ в группу материала
     * @param $groupId
     * @param $data
     */
    public function updateTuGroupMaterial($groupId, $data)
    {
        $this->DB->Query("delete from materials_groups_tu where materials_groups_id = {$groupId}");

        foreach ($data as $row) {
            if ( $row['val_1'] == '' ) {
                unset($row['val_1']);
            }
            if ( $row['val_2'] == '' ) {
                unset($row['val_2']);
            }
            $sqlData = $this->prepearTableData('materials_groups_tu', $row);
            $sqlData['materials_groups_id'] = $groupId;

            $this->DB->Insert('materials_groups_tu', $sqlData);
        }
    }


    /**
     * @param $materialId
     * @param $data
     */
    public function updateGroupAndTu($materialId, $data)
    {
        foreach ($data as $groupId => $group) {
            $groupName = StringHelper::removeSpace($group['name']);
            $sqlData = $this->prepearTableData('materials_groups', ['material_id' => $materialId, 'name' => $groupName]);

            $this->DB->Update('materials_groups', $sqlData, "where id = {$groupId}");

            $this->updateTuGroupMaterial($groupId, $group['tu']);
        }
    }


    /**
     * @param $groupId
     */
    public function deleteGroup($groupId)
    {
        if ( empty($groupId) ) { return; }

        $this->DB->Query("delete from materials_groups_tu where materials_groups_id = {$groupId}");

        $this->DB->Query("delete from materials_groups where id = {$groupId}");
    }


    /**
     * @deprecated
     * @param int $dealId
     * @return array
     */
    public function getMaterialsToRequest(int $dealId)
    {
        $result = [];
        $materials = $this->DB->Query(
            "SELECT m.ID id, m.NAME name, mtr.OBIEM count, mtr.ID mtrId 
                    FROM `MATERIALS_TO_REQUESTS` mtr, `MATERIALS` m 
                    WHERE mtr.ID_DEAL = {$dealId} AND mtr.ID_MATERIAL <> 0 AND m.ID = mtr.ID_MATERIAL"
        );

        while ($row = $materials->Fetch()) {
            $row['name'] = htmlentities($row['name']);
            $result[] = $row;
        }

        return $result;
    }

	/**
	 * @deprecated
	 * @param int $dealId
	 * @return array
	 */
	public function getMaterialsToRequestNew(int $dealId)
	{
		$result = [];
		$materials = $this->DB->Query(
			"SELECT * 
                    FROM `ulab_material_to_request` 
                    WHERE deal_id = {$dealId}"
		);

		while ($row = $materials->Fetch()) {
			unset($row['id']);
			$result[] = $row;
		}

		return $result;
	}

    public function getLabsForProbe($probeId)
    {
        $result = [];

        $response = $this->DB->Query(
            "
            SELECT DISTINCT bl.short_name, bl.ID, sp.date_send
            FROM ulab_material_to_request umtr
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            INNER JOIN ulab_methods_lab uml ON uml.method_id = ugtp.method_id
            INNER JOIN ba_laba bl ON bl.ID = uml.lab_id
            LEFT JOIN sended_probes sp ON sp.probe_id = umtr.id AND sp.lab_id = bl.ID
            WHERE umtr.id = '{$probeId}' AND bl.ID <> 6
            "
        );

        while($row = $response->Fetch()) {
            $result []= $row;
        }

        return $result;
    }

    public function getLabsForProbeWithCheckTz($probeId) {
        $result = [];

        $response = $this->DB->Query(
            "
            SELECT DISTINCT bl.short_name, bl.ID, sp.date_send
            FROM ulab_material_to_request umtr
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            INNER JOIN ulab_methods_lab uml ON uml.method_id = ugtp.method_id
            INNER JOIN ba_laba bl ON bl.ID = uml.lab_id
            LEFT JOIN sended_probes sp ON sp.probe_id = umtr.id AND sp.lab_id = bl.ID
            INNER JOIN ba_tz bt ON bt.ID_Z = umtr.deal_id
            INNER JOIN CHECK_TZ ct ON ct.tz_id = bt.ID AND ct.leader = bl.HEAD_ID
            WHERE umtr.id = '{$probeId}' AND bl.ID <> 6
            "
        );

        while($row = $response->Fetch()) {
            $result []= $row;
        }

        return $result;
    }
    
    /**
     * @param int $dealId
     * @return array
     */
    public function getMaterialProbeToRequest(int $dealId, $materialId = '', $probeId = '')
    {
        $methodsModel = new Methods();
        $probeModel = new Probe();

        $where = '';

        if ( !empty($materialId) ) {
            $where .= "umtr.material_id = {$materialId} and ";
        }
        if ( !empty($probeId) ) {
            $where .= "umtr.id = {$probeId} and ";
        }

        $where .= '1';

        $sql = $this->DB->Query(
            "select 
                        umtr.*,
                        mater.NAME material_name, mater.GROUPS,
                        ugtp.id as ugtp_id, ugtp.new_method_id as method_id, ugtp.measuring_sheet,  
                        ugtp.tech_condition_id as condition_id, ugtp.assigned_id, ugtp.price, ugtp.gost_number, 
                        res.actual_value 
                    from ulab_material_to_request as umtr
                    left join MATERIALS as mater on mater.ID = umtr.material_id
                    left join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
                    left join ulab_trial_results as res on res.gost_to_probe_id = ugtp.id
                    where umtr.deal_id = {$dealId} and {$where}
                    order by umtr.probe_number, ugtp.gost_number
                    "
        );

        $result = [];
        $labInfo = [];
        while ($row = $sql->Fetch()) {

            if ( !isset($result[$row['material_id']]['price']) ) {
                $result[$row['material_id']]['price'] = 0;
            }

            $result[$row['material_id']]['material_id'] = $row['material_id'];
            $result[$row['material_id']]['mtr_id'] = $row['mtr_id'];
            $result[$row['material_id']]['deal_id'] = $row['deal_id'];
            $result[$row['material_id']]['material_id'] = $row['material_id'];
            $result[$row['material_id']]['price'] += $row['price'];

            $result[$row['material_id']]['material_name'] = $row['material_name'];
            $result[$row['material_id']]['groups'] = unserialize($row['GROUPS']);

            if ( !isset($result[$row['material_id']]['probe'][$row['id']]['price']) ) {
                $result[$row['material_id']]['probe'][$row['id']]['price'] = 0;
            }

            if ( !isset($result[$row['material_id']]['probe'][$row['id']]['number_method']) ) {
                $result[$row['material_id']]['probe'][$row['id']]['number_method'] = 0;
            }

            $count = $row['probe_number'] + 1;
            $result[$row['material_id']]['probe'][$row['id']]['cipher'] = empty($row['cipher'])? 'Не присвоен шифр #'.$count : $row['cipher'];
            $result[$row['material_id']]['probe'][$row['id']]['probe_number'] = $row['probe_number'];
            $result[$row['material_id']]['probe'][$row['id']]['name_for_protocol'] = htmlentities($row['name_for_protocol']);
            $result[$row['material_id']]['probe'][$row['id']]['group'] = $row['group'];
            $result[$row['material_id']]['probe'][$row['id']]['place'] = htmlentities($row['place']);
            $result[$row['material_id']]['probe'][$row['id']]['date_probe'] = $row['date_probe'];
            $result[$row['material_id']]['probe'][$row['id']]['quarry_id'] = $row['quarry_id'];
            $result[$row['material_id']]['probe'][$row['id']]['price'] += $row['price'];
            $result[$row['material_id']]['probe'][$row['id']]['is_send'] = $row['is_send'];
            $result[$row['material_id']]['probe'][$row['id']]['date_send'] = date('d.m.Y H:i', strtotime($row['date_send']));

            if ( empty($row['ugtp_id']) ) { $row['ugtp_id'] = 'new_0'; }

            $methodData = $methodsModel->get($row['method_id']);

//            if ( !isset($labInfo[$row['id']]) ) {
//                $labInfo[$row['id']] = $methodData['lab_info'];
//            } else {
//                $labInfo[$row['id']] = array_merge($labInfo[$row['id']], $methodData['lab_info']);
//            }

            $labInfo[$row['id']] = array_unique($labInfo[$row['id']], SORT_REGULAR);

            $labs = $this->getLabsForProbeWithCheckTz($row['id']);
            $labsArray = [];

            foreach ($labs as $value) {
                $labsArray []= $value['short_name'];
            }

            $currentProbeState = 'МФЦ';

            $currentProbeToLab = $this->DB->Query("SELECT id FROM probe_to_lab WHERE umtr_id = '{$row['id']}'")->Fetch()['id'];

            if ($currentProbeToLab) {
                $currentProbeState = '';
            }

            $result[$row['material_id']]['probe'][$row['id']]['is_in_act'] = $row['in_act'];
            $result[$row['material_id']]['probe'][$row['id']]['lab_info'] = $labs;
            $result[$row['material_id']]['probe'][$row['id']]['lab_name'] = implode(', ', $labsArray);
            $result[$row['material_id']]['probe'][$row['id']]['state'] = $currentProbeState;
            $result[$row['material_id']]['probe'][$row['id']]['user_status'] = $probeModel->getAcceptStatusUser($row['id'], $_SESSION['SESS_AUTH']['USER_ID']);

            $acceptStatus = $probeModel->getAcceptStatus($row['id']);
            $acceptUser = [];
            foreach ($acceptStatus as $status) {
                if ( $status['accept_probe'] == 1 ) {
                    $acceptUser[] = $status['user_info'];
                }
            }

            if ( !empty($acceptUser) ) {
                $result[$row['material_id']]['probe'][$row['id']]['state'] = "Проба принята: " . implode(', ', array_column($acceptUser, 'short_name'));
            }


            if ( !empty($methodData) ) {
                $result[$row['material_id']]['probe'][$row['id']]['number_method']++;
            }

            if ( isset($row['price']) && !is_null($row['price']) ) {
                $methodData['price'] = $row['price'];
            }

            $methodData['gost_number'] = $row['gost_number'];

            $methodData['is_have_result'] = 0;
            $sheet = json_decode($row['measuring_sheet'], true);
            $actualVal = json_decode($row['actual_value'], true);
            if ( !empty($sheet) || $actualVal[0] != '' ) {
                $methodData['is_have_result'] = 1;
            }

            $methodData['assigned_id'] = $row['assigned_id'];
            $methodData['ugtp_id'] = $row['ugtp_id'];

            $result[$row['material_id']]['probe'][$row['id']]['method'][] = $methodData;

            $result[$row['material_id']]['probe'][$row['id']]['condition'][] = $row['condition_id'];
        }

        return $result;
    }

    /**
     * @param int $dealId
     * @return array
     */
    public function getMaterialProbeToRequestWithActNum(int $dealId, $actNum, $materialId = '', $probeId = '')
    {
        $methodsModel = new Methods();
        $probeModel = new Probe();

        $where = '';

        if ( !empty($materialId) ) {
            $where .= "umtr.material_id = {$materialId} and ";
        }
        if ( !empty($probeId) ) {
            $where .= "umtr.id = {$probeId} and ";
        }

        $where .= '1';

        $sql = $this->DB->Query(
            "select 
                        umtr.*,
                        mater.NAME material_name, mater.GROUPS,
                        ugtp.id as ugtp_id, ugtp.new_method_id as method_id, ugtp.measuring_sheet,  
                        ugtp.tech_condition_id as condition_id, ugtp.assigned_id, ugtp.price, ugtp.gost_number, 
                        res.actual_value 
                    from ulab_material_to_request as umtr
                    left join MATERIALS as mater on mater.ID = umtr.material_id
                    left join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
                    left join ulab_trial_results as res on res.gost_to_probe_id = ugtp.id
                    where umtr.deal_id = {$dealId} and {$where} and umtr.cipher LIKE '{$actNum}.%'
                    order by umtr.probe_number, ugtp.gost_number
                    "
        );

        $result = [];
        $labInfo = [];
        while ($row = $sql->Fetch()) {

            if ( !isset($result[$row['material_id']]['price']) ) {
                $result[$row['material_id']]['price'] = 0;
            }

            $result[$row['material_id']]['material_id'] = $row['material_id'];
            $result[$row['material_id']]['mtr_id'] = $row['mtr_id'];
            $result[$row['material_id']]['deal_id'] = $row['deal_id'];
            $result[$row['material_id']]['material_id'] = $row['material_id'];
            $result[$row['material_id']]['price'] += $row['price'];

            $result[$row['material_id']]['material_name'] = $row['material_name'];
            $result[$row['material_id']]['groups'] = unserialize($row['GROUPS']);

            if ( !isset($result[$row['material_id']]['probe'][$row['id']]['price']) ) {
                $result[$row['material_id']]['probe'][$row['id']]['price'] = 0;
            }

            if ( !isset($result[$row['material_id']]['probe'][$row['id']]['number_method']) ) {
                $result[$row['material_id']]['probe'][$row['id']]['number_method'] = 0;
            }

            $count = $row['probe_number'] + 1;
            $result[$row['material_id']]['probe'][$row['id']]['cipher'] = empty($row['cipher'])? 'Не присвоен шифр #'.$count : $row['cipher'];
            $result[$row['material_id']]['probe'][$row['id']]['probe_number'] = $row['probe_number'];
            $result[$row['material_id']]['probe'][$row['id']]['name_for_protocol'] = htmlentities($row['name_for_protocol']);
            $result[$row['material_id']]['probe'][$row['id']]['group'] = $row['group'];
            $result[$row['material_id']]['probe'][$row['id']]['place'] = htmlentities($row['place']);
            $result[$row['material_id']]['probe'][$row['id']]['quarry_id'] = $row['quarry_id'];
            $result[$row['material_id']]['probe'][$row['id']]['price'] += $row['price'];
            $result[$row['material_id']]['probe'][$row['id']]['is_send'] = $row['is_send'];
            $result[$row['material_id']]['probe'][$row['id']]['date_send'] = date('d.m.Y H:i', strtotime($row['date_send']));

            if ( empty($row['ugtp_id']) ) { $row['ugtp_id'] = 'new_0'; }

            $methodData = $methodsModel->get($row['method_id']);

//            if ( !isset($labInfo[$row['id']]) ) {
//                $labInfo[$row['id']] = $methodData['lab_info'];
//            } else {
//                $labInfo[$row['id']] = array_merge($labInfo[$row['id']], $methodData['lab_info']);
//            }

            $labInfo[$row['id']] = array_unique($labInfo[$row['id']], SORT_REGULAR);

            $labs = $this->getLabsForProbeWithCheckTz($row['id']);
            $labsArray = [];

            foreach ($labs as $value) {
                $labsArray []= $value['short_name'];
            }

            $result[$row['material_id']]['probe'][$row['id']]['is_in_act'] = $row['in_act'];
            $result[$row['material_id']]['probe'][$row['id']]['lab_info'] = $labs;
            $result[$row['material_id']]['probe'][$row['id']]['lab_name'] = implode(', ', $labsArray);
            $result[$row['material_id']]['probe'][$row['id']]['state'] = $row['state'];
            $result[$row['material_id']]['probe'][$row['id']]['user_status'] = $probeModel->getAcceptStatusUser($row['id'], $_SESSION['SESS_AUTH']['USER_ID']);

            $acceptStatus = $probeModel->getAcceptStatus($row['id']);
            $acceptUser = [];
            foreach ($acceptStatus as $status) {
                if ( $status['accept_probe'] == 1 ) {
                    $acceptUser[] = $status['user_info'];
                }
            }

            if ( !empty($acceptUser) ) {
                $result[$row['material_id']]['probe'][$row['id']]['state'] = "Проба принята: " . implode(', ', array_column($acceptUser, 'short_name'));
            }


            if ( !empty($methodData) ) {
                $result[$row['material_id']]['probe'][$row['id']]['number_method']++;
            }

            if ( isset($row['price']) && !is_null($row['price']) ) {
                $methodData['price'] = $row['price'];
            }

            $methodData['gost_number'] = $row['gost_number'];

            $methodData['is_have_result'] = 0;
            $sheet = json_decode($row['measuring_sheet'], true);
            $actualVal = json_decode($row['actual_value'], true);
            if ( !empty($sheet) || $actualVal[0] != '' ) {
                $methodData['is_have_result'] = 1;
            }

            $methodData['assigned_id'] = $row['assigned_id'];
            $methodData['ugtp_id'] = $row['ugtp_id'];

            $result[$row['material_id']]['probe'][$row['id']]['method'][] = $methodData;

            $result[$row['material_id']]['probe'][$row['id']]['condition'][] = $row['condition_id'];
        }

        return $result;
    }


    /**
     * mtr = MATERIALS_TO_REQUESTS
     * @param $oldMtrIdList
     * @param $newMtrIdList
     */
    public function copyProbeAndGostByMtr($oldMtrIdList, $newMtrIdList)
    {
        foreach ($oldMtrIdList as $i => $oltId) {
            $stmpOld = $this->DB->Query("SELECT id FROM probe_to_materials WHERE material_request_id = {$oltId}");

            $dataNewPtm = [
                'material_request_id' => $newMtrIdList[$i]
            ];

            while ($row = $stmpOld->Fetch()) {
                $newProbeId = $this->DB->Insert('probe_to_materials', $dataNewPtm);

                $stmpOldGost = $this->DB->Query("SELECT gost_method, gost_conditions, price FROM gost_to_probe WHERE probe_id = {$row['id']}");

                while ($rowGost = $stmpOldGost->Fetch()) {
                    $dataNewProbeGost = [
                        'probe_id' => $newProbeId,
                        'gost_method' => $rowGost['gost_method'],
                        'gost_conditions' => $rowGost['gost_conditions'],
                        'price' => $this->quoteStr($this->DB->ForSql($rowGost['price'])),
                    ];

                    // TODO: новая область
                    if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
                        $gostData['new_method_id'] = $rowGost['gost_method'];
                    }

                    $this->DB->Insert('gost_to_probe', $dataNewProbeGost);
                }
            }
        }
    }


    /**
     * @param $dealId
     * @param $dataList
     * @return array
     */
    public function setMaterialToRequest($dealId, $dataList)
    {
        // TODO: не помню зачем, возможно не надо
//        $this->DB->Query("DELETE FROM `MATERIALS_TO_REQUESTS` WHERE ID_DEAL = {$dealId}");

        $mtrArrayId = [];

        $m_number = 1;
        foreach ($dataList as $material) {
            $k = 1;

            $data = [
                'ID_DEAL' => $dealId,
                'ID_MATERIAL' => $material['id'],
                'OBIEM' => $material['count'],
                'NAME_MATERIAL' => $this->quoteStr($this->DB->ForSql($material['name'])),
            ];
            // TODO: устарело
            $mtrId = $this->DB->Insert('MATERIALS_TO_REQUESTS', $data);
            $mtrArrayId[] = $mtrId;

            $dataProbe = [
                'mtr_id' => $mtrId,
                'deal_id' => $dealId,
                'material_id' => $material['id'],
                'material_number' => $m_number
            ];

//            for ($i = 0; $i < $material['count']; $i++) {
//                $dataProbe['probe_number'] = $k;
//                $mtrId = $this->DB->Insert('ulab_material_to_request', $dataProbe);
//                $k++;
//				$mtrArrayId[] = $mtrId;
//            }

            $m_number++;
        }

        return $mtrArrayId;
    }

	/**
	 * @param $dealId
	 * @param $dataList
	 * @return array
	 */
	public function setMaterialToRequestNew($dealId, $oldDealId)
	{
		$materials = $this->DB->Query(
			"SELECT * 
                    FROM `ulab_material_to_request` 
                    WHERE deal_id = {$oldDealId}"
		);

		while ($row = $materials->fetch())
		{
			$oldMtr = $row['id'];
			$dataMtr = [];

			$dataMtr = [
				'material_id' => $row['material_id'],
				'deal_id' => $dealId,
				'probe_number' => $row['probe_number'],
				'material_number' => $row['material_number'],
				'group' => $row['group'],
			];

			$newIdMtr = $this->DB->Insert("ulab_material_to_request", $dataMtr);

            $gostRes = $this->DB->Query("SELECT 
                                            ugp.*,
                                            um.price AS material_price
                                        FROM 
                                            ulab_gost_to_probe ugp
                                        INNER JOIN 
                                            ulab_methods um
                                            ON ugp.method_id = um.id
                                        WHERE 
                                            ugp.material_to_request_id = {$oldMtr}");

			while ($gostRow = $gostRes->fetch())
			{
				$dataGost = [];

				$dataGost = [
					'method_id' => $gostRow['method_id'],
					'new_method_id' => $gostRow['new_method_id'],
					'conditions_id' => "",
					'tech_condition_id' => "",
					'price' => $gostRow['material_price'],
					'material_to_request_id' => $newIdMtr,
					'gost_number' => $gostRow['gost_number'],
				];

				$this->DB->Insert("ulab_gost_to_probe", $dataGost);
			}
		}

		return true;
	}


    /**
     * @param $name
     * @return false|mixed|string
     */
    public function add($name)
    {
        $name = StringHelper::removeSpace($name);

        $sql = $this->DB->Query("select ID from MATERIALS where NAME like '{$name}'")->Fetch();

        if ( !empty($sql['ID']) ) {
            return $sql['ID'];
        }

        $sqlData = $this->prepearTableData('MATERIALS', ['NAME' => $name]);

        return $this->DB->Insert('MATERIALS', $sqlData);
    }


    /**
     * @param int $materialId
     * @return array
     */
    public function getBaGostByMaterialId(int $materialId): array
    {
        $result = [];

        //$baGost = $this->DB->Query("SELECT * FROM ba_gost WHERE `NUM_OA_NEW` > '0' AND `NON_ACTUAL` <> 1 AND `DOP` ={$materialId} GROUP BY GOST");
        $baGost = $this->DB->Query("SELECT * FROM ba_gost WHERE `NUM_OA_NEW` <> 0 AND `NON_ACTUAL` <> 1 AND `DOP` ={$materialId} GROUP BY GOST");

        while ($row = $baGost->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    public function deleteProbe($materialRequestId)
    {
        $this->DB->Query("DELETE FROM probe_to_materials WHERE material_request_id = {$materialRequestId}");
    }

    /**
     * @param $data
     * @return false|mixed|string
     */
    public function probeAdd($data) {
        $probeData = [
            'material_request_id' => $data['material_request_id'],
            'cipher' => $this->quoteStr($this->DB->ForSql(trim($data['cipher']))),
        ];

        return $this->DB->Insert('probe_to_materials', $probeData);
    }


    /**
     * @param $probeId
     * @return array
     */
    public function getGostByProbe($probeId)
    {
        $gost = $this->DB->Query("SELECT * FROM `gost_to_probe` WHERE `probe_id` = {$probeId}");

        $result = [];

        while ($row = $gost->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param $data
     * @return false|mixed|string
     */
    public function gostAdd($data) {
        $gostData = [
            'probe_id' => $data['probe_id'],
            'gost_method' => $data['gost_method'],
            'new_method_id' => $data['gost_method'],
            'gost_conditions' => $data['gost_conditions'],
            'price' => $data['price']
        ];

        return $this->DB->Insert('gost_to_probe', $gostData);
    }


    public function fillCipher($dealId = 0)
    {
        $where = "1";
        if ( $dealId > 0 ) {
            $where = "mtr.ID_DEAL = {$dealId}";
        }

        $stmpMtr = $this->DB->Query(
            "SELECT 
                        @row_number:=CASE
                            WHEN @deal = mtr.ID_DEAL THEN @row_number + 1
                            ELSE 1
                        END AS num,
                        @deal:=mtr.ID_DEAL as ID_DEAL, ptm.id AS ptm_id, mtr.ID mtr_id, mtr.ID_MATERIAL,
                        ab.ACT_NUM, ab.ACT_DATE 
                    FROM 
                        MATERIALS_TO_REQUESTS AS mtr, ACT_BASE AS ab, 
                        probe_to_materials as ptm, 
                        (SELECT @deal:=0,@row_number:=0) as t
                    WHERE 
                        mtr.ID_DEAL = ab.ID_Z AND ptm.material_request_id = mtr.ID AND {$where}
                    ORDER BY 
                        mtr.ID_DEAL, mtr.ID, ptm.id"
        );

        while ($row = $stmpMtr->Fetch()) {
            $date = strtotime($row['ACT_DATE']);
            $year = date("Y", $date)%10 ? substr(date("Y", $date), -2) : date("Y", $date);

            $cipher = $row['ACT_NUM'] . '.' . $row['num'] . '/' . $year;

            //echo $cipher . '<br>';

            $date = [
                'cipher' => $this->quoteStr($cipher)
            ];

            $this->DB->Update("probe_to_materials", $date, "WHERE id = {$row['ptm_id']}");
        }
    }

    /**
     * добавить шифр для новой таблицы ulab_material_to_request
     * @param int $dealId
     */
    public function addCipher($dealId = 0)
    {
        $where = "1";
        if ( $dealId > 0 ) {
            $where = "umtr.deal_id = {$dealId}";
        }

        $umtr = $this->DB->Query(
            "SELECT
               @row_number:=CASE
                WHEN @deal = umtr.deal_id THEN @row_number + 1
                ELSE 1
            END AS num,
              @deal:=umtr.deal_id as deal_id, umtr.id umtr_id, umtr.material_id, ab.ACT_NUM, ab.ACT_DATE, @row_number, @deal
            FROM 
              ulab_material_to_request AS umtr, 
              ACT_BASE AS ab, 
              (SELECT @deal:=0,@row_number:=0) as t
            WHERE 
              umtr.deal_id = ab.ID_Z AND umtr.mtr_id <> 0 AND {$where} 
            ORDER BY 
              umtr.material_number, umtr.id"
		);

		$i = 1;
		while ($row = $umtr->Fetch()) {
			$date = strtotime($row['ACT_DATE']);
			$year = date("Y", $date) % 10 ? substr(date("Y", $date), -2) : date("Y", $date);

			if (!empty($row['cipher'])) {
				continue;
			}

			$cipher = $row['ACT_NUM'] . '.' . $i . '/' . $year;

			$dateProbe = [
				'cipher' => "'{$cipher}'"
			];

			$this->DB->Update("ulab_material_to_request", $dateProbe, "WHERE id = {$row['umtr_id']}");
			$i++;
		}
	}

    public function getZern()
	{
		$result = [];

		$res = $this->DB->Query("SELECT * FROM ZERN");

		while ($row = $res->Fetch()) {
			$result[] = $row;
		}

		return $result;
	}

    public function getSieveAndNormNew($id)
    {
        $result = [];

        $res = $this->DB->Query("SELECT * FROM ZERN WHERE ID = {$id}")->Fetch();

        $result['NAME'] = $res['NAME'];

        $arrayData = json_decode($res['DATA'], JSON_UNESCAPED_UNICODE);

//        foreach ($arrayData['round']['200']['seave_values__to'] as $key => $value) {
//            if(empty($value) && empty($arrayData['round']['200']['seave_values__from'][$key])) {
//                continue;
//            }
//
//            $sieveTitle = $this->DB->Query("SELECT title FROM sieves WHERE id = '{$key}'")->Fetch()['title'];
//
//            $this->pre($arrayData, true);
//
//            $sieveTitle = str_replace('менее', '<', $sieveTitle);
//            $sieveTitle = str_replace(' мм', '', $sieveTitle);
//
//            $result['sieve'] []= $sieveTitle;
//
//            $result['norm_to'] []= $value;
//            $result['norm_from'] []= $arrayData['round']['200']['seave_values__from'][$key];
//        }

        $lowerMkmArray = [];
        $lowerMmArray = [];
        $mmArray = [];
        $mkmArray = [];

        foreach ($arrayData['round']['200']['seave_values__to'] as $key => $value) {
            if(empty($value) && empty($arrayData['round']['200']['seave_values__from'][$key])) {
                continue;
            }

            $sieveTitle = $this->DB->Query("SELECT title FROM sieves WHERE id = '{$key}'")->Fetch()['title'];

            $sieveTitle = str_replace(',', '.', $sieveTitle);

            if(is_numeric(strpos($sieveTitle, 'менее '))) {
                $sieveTitle = str_replace('менее ', '', $sieveTitle);

                if(is_numeric(strpos($sieveTitle, ' мкм'))) {
                    $sieveTitle = str_replace(' мкм', '', $sieveTitle);

                    $lowerMkmArray [$sieveTitle]= [
                        'from' => $arrayData['round']['200']['seave_values__from'][$key],
                        'to' => $value
                    ];
                }else {
                    $sieveTitle = str_replace(' мм', '', $sieveTitle);

                    $lowerMmArray [$sieveTitle]= [
                        'from' => $arrayData['round']['200']['seave_values__from'][$key],
                        'to' => $value
                    ];
                }
            }else if(is_numeric(strpos($sieveTitle, ' мкм'))) {
                $sieveTitle = str_replace(' мкм', '', $sieveTitle);

                $mkmArray [$sieveTitle] = [
                    'from' => $arrayData['round']['200']['seave_values__from'][$key],
                    'to' => $value
                ];
            }else {
                $sieveTitle = str_replace(' мм', '', $sieveTitle);

                $mmArray [$sieveTitle] = [
                    'from' => $arrayData['round']['200']['seave_values__from'][$key],
                    'to' => $value
                ];
            }
        }

        krsort($mmArray);
        krsort($mkmArray);
        krsort($lowerMmArray);
        krsort($lowerMkmArray);

        foreach ($mmArray as $key => $value) {
            $result['norm_from'] []= $value['from'];
            $result['norm_to'] []= $value['to'];
            $result['sieve'] []= $key;
        }

        foreach ($mkmArray as $key => $value) {
            $result['norm_from'] []= $value['from'];
            $result['norm_to'] []= $value['to'];
            $result['sieve'] []= $key . ' мкм';
        }

        foreach ($lowerMmArray as $key => $value) {
            $result['norm_from'] []= $value['from'];
            $result['norm_to'] []= $value['to'];
            $result['sieve'] []= 'менее ' . $key . ' мм';
        }

        foreach ($lowerMkmArray as $key => $value) {
            $result['norm_from'] []= $value['from'];
            $result['norm_to'] []= $value['to'];
            $result['sieve'] []= 'менее ' . $key . ' мкм';
        }

        return $result;
    }

	public function getSieveAndNorm($id)
	{
		$result = [];

		$res = $this->DB->Query("SELECT * FROM ZERN WHERE ID = {$id}")->Fetch();

		$norm1 = unserialize($res['NORM1']);
		$norm2 = unserialize($res['NORM2']);
		$sieve = [];
		$norm_from = [];
		$norm_to = [];
		$m = [];

		foreach ($norm1 as $key => $val) {
			if ($val == '') {
				continue;
			}
			$norm_from[] = $val;
			$norm_to[] = $norm2[$key];
			if (gettype(self::SIEVE_ARR[$key]) != 'string') {
				$sieve[] = (float)str_replace(',', '.', self::SIEVE_ARR[$key]);
			} else {
				$sieve[] = self::SIEVE_ARR[$key];
			}
		}
//		$this->pre($sieve);
//		arsort($sieve);



		foreach ($sieve as $k => $item) {
			if (stripos($sieve[$k], 'менее') !== false) {
				$m[] = [
					'name' => str_replace('.', ',', $sieve[$k]),
					'nf1' => $norm_from[$k],
					'nt1' => $norm_to[$k],
				];
			} else {
				$sieve1[] = str_replace('.', ',', $sieve[$k]);
				$norm_from1[] = $norm_from[$k];
				$norm_to1[] = $norm_to[$k];
			}
		}

		foreach ($m as $val) {
			array_push($sieve1, $val['name']);
			array_push($norm_from1, $val['nf1']);
			array_push($norm_to1, $val['nt1']);
		}

		$result = [
			'NAME' => $res['NAME'],
			'norm_from' => $norm_from1,
			'norm_to' => $norm_to1,
			'sieve' => $sieve1,
		];

		return $result;
	}


    /**
     * @param $matID
     * @return array
     */
    public function getGostToMaterialByMatID($matID) {

        $result = [];

        $res = $this->DB->Query(
            "SELECT 
                        m.*, 
                        g.reg_doc, g.year, g.description, g.materials
                    FROM `gost_to_material` gm
                    inner join `ulab_methods` as m on gm.method_id = m.id 
                    inner join `ulab_gost` as g on g.id = m.gost_id
                    WHERE gm.`material_id` = {$matID} ");

        while ($row = $res->Fetch()) {
            $strYear = !empty($row['year']) ? "-{$row['year']}" : '';
            $strClause = !empty($row['clause']) ? " {$row['clause']}" : '';
            $row['view_gost'] = "{$row['reg_doc']}{$strYear}{$strClause} | {$row['name']}";

            $result[] = $row;
        }
        return $result;
    }


    /**
     * @param $id
     * @param $data
     */
    public function setGostToMaterial($id, $data)
    {
        $this->DB->Query("DELETE FROM `gost_to_material` WHERE `material_id` = {$id}");

        foreach ($data as $gost) {

            $dataGost = [
                'method_id' => $gost,
                'material_id' => $id,
            ];

            $this->DB->Insert('gost_to_material', $dataGost);

        }
    }


    /**
     * @param array $filter
     * @return mixed
     */
    public function getDatatoJournalMaterial(array $filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'm.NAME',
            'dir' => 'DESC'
        ];

        if ( !empty($filter['search']) ) {
            // Заявка
            if (isset($filter['search']['NAME'])) {
                $where .= "m.NAME LIKE '%{$filter['search']['NAME']}%' AND ";
            }
            if (isset($filter['search']['is_active'])) {
                $where .= "m.is_active = {$filter['search']['is_active']} AND ";
            }
        }

        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }
            switch ($filter['order']['by']) {
                case 'requestTitle':
                    $order['by'] = 'm.NAME';
                    break;
            }
        }

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

        $data = $this->DB->Query(
            "SELECT * 
                    FROM MATERIALS m                   
                    WHERE `NAME` <> '' and `NAME` is not null and {$where}
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT m.ID, m.NAME 
                    FROM MATERIALS m 
                    where `NAME` <> '' and `NAME` is not null
                    "
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT m.ID, m.NAME 
                    FROM MATERIALS m 
                    where `NAME` <> '' and `NAME` is not null and {$where}
                    "
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


	/**
	 * @param $id
	 * @param $data
	 */
	public function updateScheme($id, $data)
	{
		$data['name'] = $this->quoteStr($data['name']);
		$this->DB->Update('ulab_material_scheme', $data, "where id = {$id}");
	}

	/**
	 * @param $id
	 * @param $data
	 */
	public function addNewScheme($data)
	{	$data['name'] = $this->quoteStr($data['name']);
		$id = $this->DB->Insert('ulab_material_scheme', $data);
		return $id;
	}


	/**
	 * @param $id
	 * @param $data
	 */
	public function addGostToScheme($id, $data)
	{
		$this->DB->Query("delete from ulab_scheme_param where scheme_id = {$id}");

		if (!empty($data)) {
			foreach ($data as $gost) {
				$gost['scheme_id'] = $id;
				$this->DB->Insert('ulab_scheme_param', $gost);
			}
		}
	}


	/**
	 * @param $id
	 */
	public function deleteScheme($id)
	{
		$this->DB->Query("delete from ulab_scheme_param where scheme_id = {$id}");
		$this->DB->Query("delete from ulab_material_scheme where id = {$id}");
	}


	/**
	 * @param $materialId
	 * @return array
	 */
	public function getSchemeByMaterial($materialId)
	{
		$result = [];
        $param = [];

		$res = $this->DB->Query("SELECT ms.id, ms.name, 
						sp.method_id, sp.tu_id
                    FROM `ulab_material_scheme` as ms
					LEFT JOIN `ulab_scheme_param` as sp ON sp.scheme_id = ms.id
                    WHERE ms.`material_id` = {$materialId}");

		while ($row = $res->Fetch()) {
            $param[$row['id']][] = [
                'method_id' => $row['method_id'],
                'tu_id' => $row['tu_id']
            ];

            $result[$row['id']] = [
                'id' => $row['id'],
                'name' => $row['name']
            ];

			$result[$row['id']]['param'] = $param[$row['id']];
		}

		return $result;
	}


    /**
     * @param $schemeId
     * @return array
     */
    public function getSchemeParam($schemeId)
    {
        $result = [];

        $res = $this->DB->Query(
            "SELECT *
                    FROM `ulab_scheme_param`
                    WHERE `scheme_id` = {$schemeId}"
        );

        while ($row = $res->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function copyMethodGroupById($schemeOldId, $schemeId)
	{
//
		$res = $this->DB->Query("select * from ulab_scheme_param where scheme_id = {$schemeOldId}");

		while ($row = $res->fetch()) {
			$data = [
				'scheme_id' => $schemeId,
				'method_id' => $row['method_id'],
				'tu_id' => $row['tu_id'],
				];

			$this->DB->Insert("ulab_scheme_param", $data);
		}
	}

    /**
     * @return array
     */
    public function getMaterialsKeyValue(): array
    {
        $materials = [];
        $res = $this->DB->Query("SELECT `ID`, `NAME` FROM `MATERIALS`");

        while ($row = $res->Fetch()) {
            $materials[$row['ID']] = $row['NAME'];
        }

        return $materials;
    }
}
