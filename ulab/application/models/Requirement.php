<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Класс модель техзадания
 * Class Requirement
 */
class Requirement extends Model
{
    const LABS = [
        'LFHI' => 54,
        'LFMI' => 56,
        'DSL' => 55,
        'LSM' => 57,
        'OSK' => 58
    ];


    /**
     * @param int $dealId
     * @return array
     */
    public function getMaterialFromTz($dealId = 0)
    {
        // $where = "umtr.deal_id >" . DEAL_NEW_RESULT;
        // if (!empty($dealId)) {

        // }

        $where = "umtr.deal_id = {$dealId}";
        $result = [];

        $sql = $this->DB->Query(
            "select distinct m.ID as id, m.NAME as name 
                    from ulab_material_to_request as umtr
                    inner join MATERIALS as m on m.ID = umtr.material_id
                    where {$where}
                    order by m.NAME"
        );

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param int $dealId
     * @return array
     */
    public function getProbeFromTz(int $dealId)
    {
        if (empty($dealId)) {
            return [];
        }

        $result = [];

        $sql = $this->DB->Query(
            "select distinct umtr.id, umtr.cipher, m.ID as material_id, m.NAME as material_name, umtr.place, umtr.date_probe
                from ulab_material_to_request as umtr
                inner join MATERIALS as m on m.ID = umtr.material_id
                where umtr.deal_id = {$dealId}
                order by umtr.material_number, umtr.probe_number"
        );

        while ($row = $sql->Fetch()) {
            if (empty($row['cipher'])) {
                $number = $row['probe_number'] + 1;
                $row['cipher'] = "Не присвоен шифр #{$number}";
            }

            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param int $dealId
     * @return array
     */
    public function getLabFromTz(int $dealId)
    {
        if (empty($dealId)) {
            return [];
        }

        $result = [];

        $sql = $this->DB->Query(
            "select distinct bl.short_name, bl.ID
                from ulab_material_to_request as umtr
                inner join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
                inner join ulab_methods_lab as uml on uml.method_id = ugtp.method_id
                inner join ba_laba as bl on bl.ID = uml.lab_id
                where umtr.deal_id = {$dealId}
                order by umtr.material_number, umtr.probe_number"
        );

        while ($row = $sql->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param int $dealId
     * @return array
     */
    public function getProbeFromTzWithoutAct(int $dealId)
    {
        if (empty($dealId)) {
            return [];
        }

        $result = [];

        $sql = $this->DB->Query(
            "select distinct umtr.id, umtr.cipher, m.ID as material_id, m.NAME as material_name, umtr.place, umtr.date_probe
                from ulab_material_to_request as umtr
                inner join MATERIALS as m on m.ID = umtr.material_id
                where umtr.deal_id = {$dealId} AND umtr.in_act <> 1
                order by umtr.material_number, umtr.probe_number"
        );

        while ($row = $sql->Fetch()) {
            if (empty($row['cipher'])) {
                $number = $row['probe_number'] + 1;
                $row['cipher'] = "Не присвоен шифр #{$number}";
            }

            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param int $dealId
     * @return array
     */
    public function getMethodsFromTz($dealId = 0)
    {
        // $where = "umtr.deal_id >" . DEAL_NEW_RESULT;
        // if (!empty($dealId)) {

        // }
        $where = "umtr.deal_id = {$dealId}";

        $methodsModel = new Methods();

        $result = [];

        $sql = $this->DB->Query(
            "select distinct ugtp.method_id 
                    from ulab_material_to_request as umtr
                    inner join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
                    where {$where}"
        );

        while ($row = $sql->Fetch()) {
            $result[] = $methodsModel->get($row['method_id']);
        }

        return $result;
    }


    /**
     * @param int $dealId
     * @return array
     */
    public function getAssignedFromTz($dealId = 0)
    {
        // $where = "umtr.deal_id >" . DEAL_NEW_RESULT;
        // if (!empty($dealId)) {
        // }
        $where = "umtr.deal_id = {$dealId}";
        $userModel = new User();

        $result = [];

        $sql = $this->DB->Query(
            "select distinct ugtp.assigned_id 
                    from ulab_material_to_request as umtr
                    inner join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
                    where {$where}"
        );

        while ($row = $sql->Fetch()) {
            if (empty($row['assigned_id'])) {
                continue;
            }

            $result[] = $userModel->getUserData($row['assigned_id']);
        }

        return $result;
    }


    /**
     * получает сумму цен методик
     * @param $dealId
     * @return int|float
     */
    public function getPrice($dealId)
    {
        if (empty($dealId)) {
            return 0;
        }

        $sql = $this->DB->Query("
            select sum(gost.price) as price 
            from ulab_gost_to_probe as gost
            inner join ulab_material_to_request as material on material.id = gost.material_to_request_id
            where material.deal_id = {$dealId}
        ")->Fetch();

        if (isset($sql['price'])) {
            return (float)$sql['price'];
        } else {
            return 0;
        }
    }


    /**
     * обновляем цену, учитываем скидку
     * @param $dealId
     * @return array
     */
    public function updatePrice($dealId)
    {
        $requestModel = new Request();

        $price = $this->getPrice($dealId);
        $priceDiscount = $price;

        $tzData = $requestModel->getTzByDealId($dealId);

        if ($tzData['discount_type'] == 'percent') {
            $priceDiscount -= $price * $tzData['DISCOUNT'] / 100;
        } elseif ($tzData['discount_type'] == 'rub') {
            $priceDiscount -= $tzData['DISCOUNT'];
        }

        if ($priceDiscount < 0) {
            $priceDiscount = 0;
        }

        $requestModel->updateTz($dealId, ['PRICE' => $price, 'price_discount' => $priceDiscount]);

        return [
            'price' => $price,
            'price_discount' => $priceDiscount,
            'price_ru' => StringHelper::priceFormatRus($priceDiscount),
        ];
    }

    public function checkDogovorPrice($dealId)
    {
        $priceArray = $this->DB->Query("SELECT PRICE, price_discount FROM ba_tz WHERE ID_Z = '{$dealId}'")->Fetch();
        $dogovorPrice = $this->DB->Query(
            "
            SELECT d.dogovor_price
            FROM DEALS_TO_CONTRACTS dtc
            LEFT JOIN DOGOVOR d ON d.ID = dtc.ID_CONTRACT
            WHERE dtc.ID_DEAL = '{$dealId}'
            "
        )->Fetch()['dogovor_price'];

        if (!$dogovorPrice || $dogovorPrice == '0.00') return false;

        $checkPrice = $priceArray['PRICE'];

        if ($priceArray['price_discrount']) $checkPrice = $priceArray['price_discrount'];

        return $checkPrice > $dogovorPrice;
    }

    public function getTzIdByDealId(int $dealId)
    {
        $result = $this->DB->Query("SELECT ID FROM `ba_tz` WHERE ID_Z = {$dealId}")->Fetch();

        if ($result === false) {
            return 0;
        }

        return $result['ID'];
    }

    public function getTzIdByActId(int $actId)
    {
        $result = $this->DB->Query("SELECT ID_TZ FROM ACT_BASE WHERE ID = '{$actId}'")->Fetch();

        if ($result === false) {
            return 0;
        }

        return $result['ID_TZ'];
    }

    public function getDealIdByActId(int $actId)
    {
        $result = $this->DB->Query("SELECT ID_Z FROM ACT_BASE WHERE ID = '{$actId}'")->Fetch();

        if ($result === false) {
            return 0;
        }

        return $result['ID_Z'];
    }

    public function getDealIdByTzId(int $tzId): int
    {
        $result = $this->DB->Query("SELECT ID_Z FROM `ba_tz` WHERE ID = {$tzId}")->Fetch();

        if ($result === false) {
            return 0;
        }

        return (int)$result['ID_Z'];
    }

    public function getKP($tzId)
    {
        return $this->DB->Query("SELECT * FROM `KP` WHERE `TZ_ID` = {$tzId}")->Fetch();
    }

    public function getContracts($dealId)
    {
        return $this->DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_DEAL` = {$dealId}")->Fetch();
    }


    /**
     * @param $dealId
     * @param $filter
     * @return array
     */
    public function getMaterialProbeJournal($dealId, $filter)
    {
        $userModel = new User();
        $labModel = new Lab();

        $where = "";
        $limit = "";
        $order = [
            'by' => 'b.ID',
            'dir' => 'DESC'
        ];

        $userData = $userModel->getUserData($_SESSION['SESS_AUTH']['USER_ID']);
        $labInfo = $labModel->getLabByUserId($userData['ID']);
        $joinHeadLab = "";
        if ($labInfo['HEAD_ID'] == $userData['ID'] && $labInfo['ID'] !== '4') {
            $lab_id = $labInfo['ID'];
            $joinHeadLab = "
                INNER JOIN ulab_methods_lab lab
                    ON lab.method_id = gtp.new_method_id
                AND lab.lab_id = {$lab_id}
                ";
        }

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // ид материалов из таблицы materials
                if (isset($filter['search']['material_id'])) {
                    $where .= "mtr.material_id = '{$filter['search']['material_id']}' AND ";
                }
                // шифр пробы №акт.поряд_номер/год
                if (isset($filter['search']['cipher'])) {
                    $where .= "mtr.cipher LIKE '%{$filter['search']['cipher']}%' AND ";
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
        }
        $where .= "1 ";

        $data = $this->DB->Query(
            "SELECT 
                        mtr.*, m.NAME as material_name, count(gtp.id) as count_methods, mg.name as material_group_name
                    FROM ulab_material_to_request as mtr
                    inner join MATERIALS as m ON m.ID = mtr.material_id
                    left join materials_groups as mg ON mtr.group = mg.id
                    left join ulab_gost_to_probe as gtp on gtp.material_to_request_id = mtr.id
                    {$joinHeadLab}
                    WHERE mtr.deal_id = {$dealId} and {$where}
                    group by mtr.id
                    ORDER BY mtr.material_number asc, mtr.probe_number asc {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT 
                        mtr.id
                    FROM ulab_material_to_request as mtr
                    inner join MATERIALS as m ON m.ID = mtr.material_id
                    WHERE mtr.deal_id = {$dealId}"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT 
                        mtr.id
                    FROM ulab_material_to_request as mtr
                    inner join MATERIALS as m ON m.ID = mtr.material_id
                    WHERE mtr.deal_id = {$dealId} and {$where}"
        )->SelectedRowsCount();

        $result = [];

        while ($row = $data->Fetch()) {

            if (empty($row['cipher'])) {
                $number = $row['probe_number'] + 1;
                $row['cipher'] = "Не присвоен шифр #{$number}";
            }

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


    /**
     * @param $dealId
     * @param $filter
     * @return array
     */
    public function getMethodJournal($dealId, $filter)
    {
        $userModel = new User();
        $referenceModel = new Reference();

        $where = "";
        $limit = "";
        $order = [
            'by' => 'b.ID',
            'dir' => 'DESC'
        ];

        $labModel = new Lab();

        $userData = $userModel->getUserData($_SESSION['SESS_AUTH']['USER_ID']);
        $labInfo = $labModel->getLabByUserId($userData['ID']);
        $joinHeadLab = "";
        if ($labInfo['HEAD_ID'] == $userData['ID'] && $labInfo['ID'] !== '4') {
            $lab_id = $labInfo['ID'];
            $joinHeadLab = "
                INNER JOIN ulab_methods_lab lab
                    ON lab.method_id = gtp.new_method_id
                AND lab.lab_id = {$lab_id}
                ";
        }

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // ид материалов из таблицы materials
                if (isset($filter['search']['probe_id'])) {
                    $in = implode(', ', $filter['search']['probe_id']);
                    $where .= "mtr.id in ({$in}) AND ";
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
        }
        $where .= "1 ";

        $data = $this->DB->Query(
            "SELECT 
                        mtr.cipher, mtr.material_number, mtr.probe_number,
                        m.NAME as material_name, 
                        gtp.*, 
                        methods.id as method_id, methods.name as method_name, methods.clause, methods.in_field, methods.is_extended_field, methods.is_confirm, methods.is_actual, methods.in_field,
                        gost.reg_doc, gost.year, 
                        tu.reg_doc as tu_reg_doc, tu.year as tu_year, tu.clause as tu_clause, tu.measured_properties_name
                    FROM ulab_material_to_request as mtr
                    inner join MATERIALS as m ON m.ID = mtr.material_id
                    inner join ulab_gost_to_probe as gtp ON gtp.material_to_request_id = mtr.id
                    inner join ulab_methods as methods ON gtp.new_method_id = methods.id
                    inner join ulab_gost as gost ON gost.id = methods.gost_id
                    left join ulab_tech_condition as tu ON tu.id = gtp.tech_condition_id
                    {$joinHeadLab}
                    WHERE mtr.deal_id = {$dealId} and {$where}
                    ORDER BY mtr.material_number asc, mtr.probe_number asc, gtp.gost_number asc {$limit}"
        );


        $dataTotal = $this->DB->Query(
            "SELECT 
                        gtp.id
                    FROM ulab_material_to_request as mtr
                    inner join MATERIALS as m ON m.ID = mtr.material_id
                    inner join ulab_gost_to_probe as gtp ON gtp.material_to_request_id = mtr.id
                    inner join ulab_methods as methods ON gtp.new_method_id = methods.id
                    inner join ulab_gost as gost ON gost.id = methods.gost_id
                    left join ulab_tech_condition as tu ON tu.id = gtp.tech_condition_id
                    WHERE mtr.deal_id = {$dealId}"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT 
                        gtp.id
                    FROM ulab_material_to_request as mtr
                    inner join MATERIALS as m ON m.ID = mtr.material_id
                    inner join ulab_gost_to_probe as gtp ON gtp.material_to_request_id = mtr.id
                    inner join ulab_methods as methods ON gtp.new_method_id = methods.id
                    inner join ulab_gost as gost ON gost.id = methods.gost_id
                    left join ulab_tech_condition as tu ON tu.id = gtp.tech_condition_id
                    WHERE mtr.deal_id = {$dealId} and {$where}"
        )->SelectedRowsCount();

        $result = [];

        while ($row = $data->Fetch()) {

            if (empty($row['cipher'])) {
                $number = $row['probe_number'] + 1;
                $row['cipher'] = "Не присвоен шифр #{$number}";
            }

            $row['material_probe'] = "{$row['material_name']} | {$row['cipher']}";

            $strMpName = StringHelper::removeSpace($row['method_name']);
            $clause = StringHelper::removeSpace($row['clause']);
            $strYear = !empty($row['year']) ? "-{$row['year']}" : '';
            $strClause = !empty($clause) ? " {$clause}" : '';
            $row['method_view_name'] = "{$row['reg_doc']}{$strYear}{$strClause} | {$strMpName}";

            if (!empty($row['tu_reg_doc'])) {
                $strMpName = StringHelper::removeSpace($row['measured_properties_name']);
                $clause = StringHelper::removeSpace($row['tu_clause']);
                $strYear = !empty($row['tu_year']) ? "-{$row['tu_year']}" : '';
                $strClause = !empty($clause) ? " {$clause}" : '';
                $row['tu_view_name'] = "{$row['tu_reg_doc']}{$strYear}{$strClause} | {$strMpName}";
            } else {
                $row['tu_view_name'] = "--/--";
            }

            $row['gost_number']++;

            $row['assigned_name'] = 'Не назначен';

            if (!empty($row['assigned_id'])) {
                $user = $userModel->getUserData($row['assigned_id']);

                $row['assigned_name'] = $user['short_name'];
            }

            $row['unit_info'] = 'Не выбрана';

            if (!empty($row['changed_unit_id'])) {
                $unitInfo = $referenceModel->getUnitById($row['changed_unit_id']);

                $row['unit_info'] = $unitInfo['unit_rus'] . ' | ' . $unitInfo['name'];
            }


            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }


    /**
     * @param int $dealId
     * @return array
     */
    public function getContractByDealId(int $dealId): array
    {
        $result = [];

        $contract = $this->DB->Query(
            "SELECT d_c.*, d_c.ID d_c_id, d.*, d.ID d_id 
                    FROM DEALS_TO_CONTRACTS d_c 
                    INNER JOIN DOGOVOR d ON d_c.ID_CONTRACT=d.ID 
                    WHERE d_c.ID_DEAL = {$dealId}"
        )->Fetch();

        if (count($contract) > 0) {
            $contract['DATE'] = date("d.m.Y", strtotime($contract['DATE']));
            $result = $contract;
        }

        return $result;
    }


    /**
     * @param $dealId
     * @return array|false
     */
    public function getDogovor($dealId)
    {
        $result = $this->DB->Query("SELECT DOGOVOR_NUM FROM `ba_tz` WHERE ID_Z = {$dealId} ")->Fetch();

        if (!empty($result)) {
            $idDogovorArr = explode('ID', $result['DOGOVOR_NUM']);

            if (isset($idDogovorArr[1])) {
                $idDogovor = $idDogovorArr[1];
            } else {
                $idDogovor = $idDogovorArr[0];
            }
        } else {
            return [];
        }

        return $this->DB->Query("SELECT * FROM `DOGOVOR` WHERE ID = '{$idDogovor}'")->Fetch();
    }

    public function getActBase($dealId)
    {
        $result = $this->DB->Query("SELECT * FROM `ACT_BASE` WHERE `ID_Z` = {$dealId} order by id desc ")->Fetch();
        $date = strtotime($result['ACT_DATE']);
        $result['date_ru'] = StringHelper::dateRu($result['ACT_DATE']);
        $result['year'] = (int)date("Y", $date) % 10 ? substr(date("Y", $date), -2) : date("Y", $date);

        return $result;
    }

    public function getActBaseWithActId($actId)
    {
        $result = $this->DB->Query("SELECT * FROM `ACT_BASE` WHERE `ID` = {$actId} order by id desc ")->Fetch();
        $date = strtotime($result['ACT_DATE']);
        $result['date_ru'] = StringHelper::dateRu($result['ACT_DATE']);
        $result['year'] = (int)date("Y", $date) % 10 ? substr(date("Y", $date), -2) : date("Y", $date);

        return $result;
    }

    public function getAct($tzId)
    {
        return $this->DB->Query("SELECT * FROM `ACT` WHERE `TZ_ID` = {$tzId}")->Fetch();
    }

    public function getActVr($tzId)
    {
        return $this->DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`= {$tzId}")->Fetch();
    }

    public function getProtocols($tzId)
    {

        $result = $this->DB->Query("SELECT * FROM `PROTOCOLS` WHERE `ID_TZ` = {$tzId}");

        $return = [];

        while ($row = $result->Fetch()) {
            $return[] = $row;
        }

        return $return;
    }

    public function getTzDoc($tzId)
    {
        return $this->DB->Query("SELECT *, year(`DATE`) as `year` FROM `TZ_DOC` WHERE `TZ_ID` = {$tzId}")->Fetch();
    }

    /**
     * @return array
     * @deprecated
     */
    public function getQuarry(): array
    {
        $result = [];

        $quarry = $this->DB->Query("SELECT * FROM `Quarry`");

        while ($row = $quarry->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function getTzByDealId(int $dealId)
    {
        $result = [];

        $baTz = $this->DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z` = {$dealId} ");

        while ($row = $baTz->Fetch()) {
            //TODO: deserialize
            $addMails = unserialize($row['SAVE_MAIL']);

            $row['addMail'] = [];
            foreach ($addMails as $mail) {
                if (!empty($mail)) {
                    $row['addMail'][] = $mail;
                }
            }

            $tz = unserialize($row['TZ']);
            $row['TZ'] = $tz;

            $probe = unserialize($row['PROBE']);
            $row['PROBE'] = $probe;

            $row['add_info'] = json_decode($row['add_info']);

            $regx = "/(\d+)$/";
            preg_match($regx, $row['DOGOVOR_NUM'], $match);
            $row['DOGOVOR_NUM'] = $match[1] ?? '';

            if (!empty($row['PRICE'])) {
                $row['PRICE'] = StringHelper::priceFormatRus($row['PRICE']);
            } else {
                $row['PRICE'] = '--';
            }

            $result = $row;
        }

        return $result;
    }


    /**
     * @param int $tzId
     * @return array
     */
    public function getTzByTzId(int $tzId): array
    {
        $result = [];

        $baTz = $this->DB->Query("SELECT * FROM `ba_tz` WHERE `ID` = {$tzId} ");

        while ($row = $baTz->Fetch()) {
            //TODO: deserialize
            $addMails = unserialize($row['SAVE_MAIL']);

            $row['addMail'] = [];
            foreach ($addMails as $mail) {
                if (!empty($mail)) {
                    $row['addMail'][] = $mail;
                }
            }

            $tz = unserialize($row['TZ']);
            $row['TZ'] = $tz;

            $probe = unserialize($row['PROBE']);
            $row['PROBE'] = $probe;

            $row['add_info'] = json_decode($row['add_info']);

            $regx = "/(\d+)$/";
            preg_match($regx, $row['DOGOVOR_NUM'], $match);
            $row['DOGOVOR_NUM'] = $match[1] ?? '';

            $price = 0;
            if (!empty($row['PRICE'])) {
                $price = (float)$row['PRICE'];

                if ($row['discount_type'] == 'percent') {
                    $price = (float)$row['PRICE'];
                    $discount = (float)str_replace(',', '.', (string)$row['DISCOUNT']);

                    $price = round($price - $price * $discount / 100, 2);
                    $price -= $price * $row['DISCOUNT'] / 100;
                } elseif ($row['discount_type'] == 'rub') {
                    $price -= $row['DISCOUNT'];
                }
            }

            if ($price < 0) {
                $price = 0;
            }

            $row['price_discount'] = $price;
            $row['price_ru'] = StringHelper::priceFormatRus($price);

            $result = $row;
        }

        return $result;
    }


    /**
     * @param int $dealId
     * @param array $materialDataList
     * @param array $probeDataList
     * @return bool[]|string[]
     * @deprecated
     */
    public function setMaterialProbeGostToRequest(int $dealId, array $materialDataList, array $probeDataList): array
    {
        $this->DB->Query("DELETE FROM `MATERIALS_TO_REQUESTS` WHERE ID_DEAL = {$dealId}");

        $this->DB->Query("DELETE FROM `ulab_material_to_request` WHERE deal_id = {$dealId}");

        $m_number = 1;
        foreach ($materialDataList as $keyMaterial => $material) {
            $k = 1;

            $materialData = [
                'ID_DEAL' => $dealId,
                'ID_MATERIAL' => $material['id'],
                'NAME_MATERIAL' => $this->quoteStr($this->DB->ForSql($material['name'])),
                'OBIEM' => $probeDataList[$keyMaterial]
            ];

            $materialToRequestId = $this->DB->Insert('MATERIALS_TO_REQUESTS', $materialData);

            if (empty($materialToRequestId)) {
                return [
                    'error' => "Не удалось создать материал '{$material['name']}'",
                ];
            }

            $dataProbe = [
                'deal_id' => $dealId,
                'material_id' => $material['id'],
                'material_number' => $m_number
            ];

            for ($i = 0; $i < $probeDataList[$keyMaterial]; $i++) {
                $key_g = 1;

                $dataProbe['probe_number'] = $k;
                $ulabMaterialToRequest = $this->DB->Insert('ulab_material_to_request', $dataProbe);
                $k++;


                $probeData = [
                    'material_request_id' => (int)$materialToRequestId
                ];

                $probeToMaterials = $this->DB->Insert('probe_to_materials', $probeData);

                if (empty($probeToMaterials)) {
                    return [
                        'error' => "Не удалось создать пробу для материала '{$material['name']}'",
                    ];
                }

                foreach ($material['gosts']['gost_method'] as $key => $item) {

                    $gostToProbeData = [
                        'method_id' => (int)$item['id'],
                        'new_method_id' => (int)$item['id'],
                        'conditions_id' => (int)$material['gosts']['gost_conditions'][$key]['id'],
                        'tech_condition_id' => (int)$material['gosts']['gost_conditions'][$key]['id'],
                        'price' => $this->quoteStr($this->DB->ForSql($material['gosts']['price'][$key])),
                        'material_to_request_id' => $ulabMaterialToRequest,
                        'assigned_id' => $this->quoteStr($this->DB->ForSql($material['gosts']['assigned'][$key])),
                        'gost_number' => $key_g
                    ];

                    $ulabGostToProbe = $this->DB->Insert('ulab_gost_to_probe', $gostToProbeData);


                    $gostData = [
                        'probe_id' => (int)$probeToMaterials,
                        'gost_method' => (int)$item['id'],
                        'new_method_id' => (int)$item['id'],
                        'gost_conditions' => (int)$material['gosts']['gost_conditions'][$key]['id'],
                        'tech_condition_id' => (int)$material['gosts']['gost_conditions'][$key]['id'],
                        'price' => $this->quoteStr($this->DB->ForSql($material['gosts']['price'][$key])),
                        'assigned' => $this->quoteStr($this->DB->ForSql($material['gosts']['assigned'][$key]))
                    ];

                    $gostToProbe = $this->DB->Insert('gost_to_probe', $gostData);

                    if (empty($gostToProbe)) {
                        return [
                            'error' => "Не удалось создать ГОСТ для материала '{$material['name']}'",
                        ];
                    }
                    $key_g++;
                }
            }
            $m_number++;
        }

        return [
            'success' => true
        ];
    }


    /**
     * Обновить материал и госты
     * @param array $materialDataList
     * @param array $probeDataList
     * @return bool[]|string[]
     */
    public function updateMaterialProbeGostToRequest($dealId, $materialDataList, $probeDataList)
    {
        $m_number = 1;
        foreach ($materialDataList as $keyMaterial => $material) {
            $k = 1;

            $materialData = [
                'ID_DEAL' => $dealId,
                'ID_MATERIAL' => $material['id'],
                'NAME_MATERIAL' => $this->quoteStr($this->DB->ForSql($material['name'])),
                'OBIEM' => $probeDataList[$keyMaterial]
            ];

            if (!empty($material['mtr_id'])) {
                $this->DB->Update('MATERIALS_TO_REQUESTS', $materialData, "WHERE ID = {$material['mtr_id']}");
            } else {
                $material['mtr_id'] = $this->DB->Insert('MATERIALS_TO_REQUESTS', $materialData);
            }


            $dataProbe = [
                'deal_id' => $dealId,
                'material_id' => $material['id'],
                'material_number' => $m_number,
                'mtr_id' => $material['mtr_id'],
            ];

            $this->DB->Query("DELETE FROM `probe_to_materials` WHERE material_request_id = {$material['mtr_id']}");
            $this->DB->Query("DELETE FROM `ulab_material_to_request` WHERE probe_number > {$probeDataList[$keyMaterial]} and mtr_id = {$material['mtr_id']}");
            for ($i = 0; $i < $probeDataList[$keyMaterial]; $i++) {
                $key_g = 1;

                $dataProbe['probe_number'] = $k;

                $c = $this->DB->Query("select id from ulab_material_to_request where mtr_id = {$material['mtr_id']} and probe_number = {$k}")->Fetch();

                if (isset($c['id'])) {
                    $this->DB->Update('ulab_material_to_request', $dataProbe, "WHERE mtr_id = {$material['mtr_id']} and probe_number = {$k}");
                    $ulabMaterialToRequest = $c['id'];
                } else {
                    $ulabMaterialToRequest = $this->DB->Insert('ulab_material_to_request', $dataProbe);
                }

                $k++;


                $probeData = [
                    'material_request_id' => (int)$material['mtr_id']
                ];

                $probeToMaterials = $this->DB->Insert('probe_to_materials', $probeData);

                foreach ($material['gosts']['gost_method'] as $key => $item) {

                    $gostToProbeData = [
                        'method_id' => (int)$item['id'],
                        'new_method_id' => (int)$item['id'],
                        'conditions_id' => (int)$material['gosts']['gost_conditions'][$key]['id'],
                        'tech_condition_id' => (int)$material['gosts']['gost_conditions'][$key]['id'],
                        'price' => $this->quoteStr($this->DB->ForSql($material['gosts']['price'][$key])),
                        'gost_number' => $key_g,
                        'assigned_id' => $this->quoteStr($this->DB->ForSql($material['gosts']['assigned'][$key]))
                    ];

                    $c2 = $this->DB->Query("select * from ulab_gost_to_probe where material_to_request_id = {$ulabMaterialToRequest} and gost_number = {$key_g}")->SelectedRowsCount();

                    if ($c2) {
                        $this->DB->Update('ulab_gost_to_probe', $gostToProbeData, "WHERE material_to_request_id = {$ulabMaterialToRequest} and gost_number = {$key_g}");
                    } else {
                        $gostToProbeData['material_to_request_id'] = $ulabMaterialToRequest;
                        $this->DB->Insert('ulab_gost_to_probe', $gostToProbeData);
                    }

                    $gostData = [
                        'probe_id' => (int)$probeToMaterials,
                        'gost_method' => (int)$item['id'],
                        'new_method_id' => (int)$item['id'],
                        'gost_conditions' => (int)$material['gosts']['gost_conditions'][$key]['id'],
                        'tech_condition_id' => (int)$material['gosts']['gost_conditions'][$key]['id'],
                        'price' => $this->quoteStr($this->DB->ForSql($material['gosts']['price'][$key])),
                        'assigned' => $this->quoteStr($this->DB->ForSql($material['gosts']['assigned'][$key]))
                    ];

                    $this->DB->Insert('gost_to_probe', $gostData);

                    $key_g++;
                }
            }
            $m_number++;
        }

        return [
            'success' => true
        ];
    }


    /**
     * @param $dealId
     * @return bool
     */
    public function isExistTz($dealId)
    {
        $sql = $this->DB->Query(
            "select 
                        ugtp.id 
                    from ulab_material_to_request as umtr
                    inner join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
                    where umtr.deal_id = {$dealId}"
        )->Fetch();

        return !empty($sql);
    }


    /**
     * Удаляет материал из заявки
     * @param $dealId
     * @param $materialId
     * @param $mtrId
     * @param $number
     * @deprecated
     */
    public function deleteMaterial($dealId, $materialId, $mtrId, $number)
    {
        $sql = $this->DB->Query("select * from probe_to_materials where material_request_id = {$mtrId}");

        while ($row = $sql->Fetch()) {
            $this->DB->Query("delete from gost_to_probe where probe_id = {$row['id']}");
        }

        $sql2 = $this->DB->Query("select * from ulab_material_to_request where deal_id = {$dealId} and material_id = {$materialId} and material_number = {$number}");

        while ($row = $sql2->Fetch()) {
            $this->DB->Query("delete from ulab_gost_to_probe where material_to_request_id = {$row['id']}");
        }

        $this->DB->Query("delete from probe_to_materials where material_request_id = {$mtrId}");

        $this->DB->Query("delete from ulab_material_to_request where deal_id = {$dealId} and material_id = {$materialId} and material_number = {$number}");

        $this->DB->Query("delete from MATERIALS_TO_REQUESTS where ID = {$mtrId}");
    }


    /**
     * Удаляет методику из материала
     * @param $gtpId
     * @param $dealId
     * @param $materialId
     * @param $numberGost
     * @param $number
     */
    public function deleteMaterialGost($gtpId, $dealId, $materialId, $numberGost, $number)
    {
        $sql2 = $this->DB->Query("select * from ulab_material_to_request where deal_id = {$dealId} and material_id = {$materialId} and material_number = {$number}");

        while ($row = $sql2->Fetch()) {
            $this->DB->Query("delete from ulab_gost_to_probe where material_to_request_id = {$row['id']} and gost_number = {$numberGost}");

            $sql3 = $this->DB->Query("select * from ulab_gost_to_probe where material_to_request_id = {$row['id']}");

            $i = 1;
            while ($row2 = $sql3->Fetch()) {
                $this->DB->Update('ulab_gost_to_probe', ['gost_number' => $i++], "where id = {$row2['id']}");
            }
        }

        $this->DB->Query("delete from gost_to_probe where id = {$gtpId}");
    }


    /**
     * удаляет испытание из пробы по ид
     * @param $tzId
     * @param $ugtpId
     * @return array
     */
    public function deleteProbeMethod($tzId, $ugtpId)
    {
        if (empty($ugtpId)) {
            return [
                'success' => false,
                'error' => "Нечего удалять"
            ];
        }

        $userModel = new User();
        $methodModel = new Methods();
        $labModel = new Lab();

        $results = $this->DB->Query(
            "select 
                ugtp.new_method_id, ugtp.measuring_sheet, ugtp.material_to_request_id, ugtp.gost_number, ugtp.actual_value as ugtp_actual_value, 
                utr.actual_value as utr_actual_value, 
                umtr.deal_id 
            from ulab_gost_to_probe as ugtp
            inner join ulab_material_to_request as umtr on umtr.id = ugtp.material_to_request_id 
            left join ulab_trial_results as utr on utr.gost_to_probe_id = ugtp.id 
            where ugtp.id = {$ugtpId}"
        )->Fetch();

        $value = json_decode($results['utr_actual_value'], true);
        $sheet = json_decode($results['measuring_sheet'], true);
        $actualValue = $results['deal_id'] >= DEAL_NEW_RESULT ? $results['ugtp_actual_value'] : $value[0];

        if ($actualValue != '' || $sheet) {
            return [
                'success' => false,
                'error' => "Невозможно удалить испытание, по которому уже внесены результаты"
            ];
        }

        if (empty($results)) {
            return [
                'success' => false,
                'error' => "Нечего удалять"
            ];
        }
        $ID_Z = $this->DB->Query("SELECT ID_Z FROM ba_tz WHERE ID = '{$tzId}'")->Fetch()['ID_Z'];

        $methodId = $this->DB->Query("SELECT method_id FROM ulab_gost_to_probe WHERE id = '{$ugtpId}'")->Fetch()['method_id'];
        $labs = $methodModel->getLab($methodId);
        foreach ($labs as $lab) {
            $l = $lab['lab_id'];
            $info = $this->DB->Query("select * from ulab_tz_lab_duration where deal_id = {$ID_Z} and lab_id = {$l}")->Fetch();
            $this->DB->Query("INSERT INTO ulab_tz_lab_duration_history(deal_id, lab_id, date_start, days) VALUES('{$info['deal_id']}', '{$info['lab_id']}', '{$info['date_start']}', '{$info['days']}')");
            $this->DB->Query("delete from ulab_tz_lab_duration where deal_id = {$ID_Z} and lab_id = {$l}");
        }
        $this->DB->Query("delete from ulab_gost_to_probe where id = {$ugtpId}");

        $sql = $this->DB->Query(
            "select id 
                    from ulab_gost_to_probe 
                    where material_to_request_id = {$results['material_to_request_id']} and gost_number > {$results['gost_number']} 
                    order by gost_number asc"
        );

        $i = $results['gost_number'];
        while ($row = $sql->Fetch()) {
            $this->DB->Update('ulab_gost_to_probe', ['gost_number' => $i++], "where id = {$row['id']}");
        }

        // обновляем цены у заявки
        $price = $this->updatePrice($results['deal_id']);
        // обновляем материалы в ba_tz
        $this->updateMaterialInDeal($results['deal_id']);

        // обновляем подтверждение
        $sql2 = $this->DB->Query(
            "select new_method_id 
                from ulab_gost_to_probe 
                where material_to_request_id = {$results['material_to_request_id']}"
        );

        $labs = $methodModel->getLab($results['new_method_id']);

        $arrLabs = [];
        while ($row = $sql2->Fetch()) {
            $currentLabs = $methodModel->getLab($row['new_method_id']);

            foreach ($currentLabs as $value) {
                $arrLabs[] = $value;
            }

            //            $arrLabs[] = $methodModel->getLab($row['new_method_id']);
        }

        foreach ($labs as $lab) {
            $user = $userModel->checkHeader($labModel->getDepartmentIdByLabId($lab));

            if (!empty($arrLabs) && !in_array($lab, $arrLabs)) {
                $this->deleteConfirmForUser($tzId, $user['user_id']);
            } else {
                $this->clearConfirmForUser($tzId, $user['user_id']);
            }
        }

        return [
            'success' => true,
            'data' => $price,
        ];
    }


    /**
     * @param $data
     */
    public function changeGostNumber($data)
    {
        foreach ($data as $row) {
            $this->DB->Update('ulab_gost_to_probe', ['gost_number' => $row['newPosition']], "where id = {$row['id']}");
        }
    }


    /**
     * @param $tzId
     * @param $ugtpId
     * @param $data - ['key' => val]
     * @return array
     */
    public function updateProbeGost($tzId, $ugtpId, $data)
    {
        if (empty($data)) {
            return [
                'success' => false,
                'error' => "Невозможно обновить"
            ];
        }

        if (empty($ugtpId)) {
            return [
                'success' => false,
                'error' => "Нечего обновлять"
            ];
        }

        $requestModel = new Request();
        $userModel = new User();
        $labModel = new Lab();
        $methodModel = new Methods();

        $userData = $userModel->getUserData($_SESSION['SESS_AUTH']['USER_ID']);

        $dealId = $requestModel->getDealIdByTzId($tzId);

        if (isset($data['price']) && !in_array(23, $userData['groups'])/* && $_SESSION['SESS_AUTH']['USER_ID'] != 60*/) {
            $info = $requestModel->getTzByDealId($dealId);
            if ($info['TYPE_ID'] == 'SALE') {
                return [
                    'success' => false,
                    'error' => "Нет прав для изменения цены"
                ];
            }
        }

        if (isset($data['new_method_id'])) {
            $method = $methodModel->get($data['new_method_id']);

            $data['price'] = $method['price'];
            $data['assigned_id'] = '';
        }

        if (isset($data['new_method_id']) || isset($data['tech_condition_id'])) {
            $ugtpInfo = $this->getGostToProbe($ugtpId);

            $methodIdList[] = $ugtpInfo['method_id'];

            if (isset($data['new_method_id']) && $ugtpInfo['method_id'] !== $data['new_method_id']) {
                $methodIdList[] = $data['new_method_id'];
            }

            foreach ($methodIdList as $methodId) {
                $labs = $methodModel->getLab($methodId);


                foreach ($labs as $lab) {
                    $user = $userModel->checkHeader($labModel->getDepartmentIdByLabId($lab));

                    if (empty($user) || in_array($lab, [5, 6])) {
                        continue;
                    }

                    $this->clearConfirmForUser($tzId, $user['user_id']);
                }
            }
        }

        $dealId = $requestModel->getDealIdByTzId($tzId);


        $this->DB->Update('ulab_gost_to_probe', $data, "where id = {$ugtpId}");


        // обновляем цены у заявки
        $price = $this->updatePrice($dealId);

        $this->updateAssigned($dealId);

        return [
            'success' => true,
            'data' => $price,
        ];
    }


    /**
     * @param $dealId
     * @param $oldMaterial
     * @param $newMaterialId
     */
    public function updateMaterialTz($dealId, $oldMaterial, $newMaterialId)
    {
        $this->DB->Update('ulab_material_to_request', ['material_id' => $newMaterialId], "where deal_id = {$dealId} and material_id = {$oldMaterial}");

        // обновляем материалы в ba_tz
        $this->updateMaterialInDeal($dealId);
    }


    /**
     * @param $umtrId
     * @param $data
     */
    public function updateProbeInfo($umtrId, $data)
    {
        if (isset($data['is_only_name_for_protocol'])) {
            $data['is_only_name_for_protocol'] = 1;
        } else {
            $data['is_only_name_for_protocol'] = 0;
        }

        $sqlData = $this->prepearTableData('ulab_material_to_request', $data);

        $this->DB->Update('ulab_material_to_request', $sqlData, "where id = {$umtrId}");
    }


    /**
     * @param $dealId
     */
    public function updateMaterialInDeal($dealId)
    {
        $select = $this->DB->Query(
            "select GROUP_CONCAT(distinct m.`name` SEPARATOR ', ') as material 
            from ulab_material_to_request as umtr
            inner join MATERIALS as m on m.ID = umtr.material_id
            where umtr.deal_id = {$dealId}"
        )->Fetch();

        $this->DB->Update("ba_tz", ['MATERIAL' => "'{$select['material']}'"], "where ID_Z = {$dealId}");
    }


    /**
     * удаляет пробу и испытания
     * @param $probeId
     * @return array
     */
    public function deleteProbe($dealId, $probeId)
    {
        $probeData = $this->DB->Query(
            "select *
            from ulab_material_to_request 
            where id = {$probeId}"
        )->Fetch();

        if (!empty($probeData['in_act'])) {
            return [
                'success' => false,
                'error' => "Невозможно удалить пробу, по которой сформирован акт."
            ];
        }

        $sql = $this->DB->Query(
            "select ugtp.id, ugtp.measuring_sheet, 
                utr.actual_value utr_actual_value, ugtp.actual_value ugtp_actual_value, 
                umtr.deal_id 
            from ulab_gost_to_probe ugtp
            left join ulab_trial_results utr on utr.gost_to_probe_id = ugtp.id 
            left join ulab_material_to_request umtr on umtr.id = ugtp.material_to_request_id 
            where ugtp.material_to_request_id = {$probeId}"
        );

        $gtpIds = [];
        while ($row = $sql->Fetch()) {
            $value = json_decode($row['utr_actual_value'], true);
            $sheet = json_decode($row['measuring_sheet'], true);
            $gtpIds[] = $row['id'];
            $actualValue = $row['deal_id'] >= DEAL_NEW_RESULT ? $row['ugtp_actual_value'] : $value[0];

            if ($actualValue != '' || $sheet) {
                return [
                    'success' => false,
                    'error' => "Невозможно удалить пробу. Есть испытания, у которых внесены результаты."
                ];
            }
        }

        if (!empty($gtpIds)) {
            $strId = implode(',', $gtpIds);

            $this->DB->Query("delete from ulab_trial_results where gost_to_probe_id in ({$strId})");
        }

        $this->DB->Query("delete from ulab_gost_to_probe where material_to_request_id = {$probeId}");

        $this->DB->Query("delete from ulab_material_to_request where id = {$probeId}");

        //        $this->DB->Update('ulab_material_to_request', ['child_deal_id' => 'NULL'], "where child_deal_id = {$dealId}");

        $requestModel = new Request();

        $tzId = $requestModel->getTzIdByDealId($dealId)['ID'];

        // обновляем цены у заявки
        $price = $this->updatePrice($dealId);
        // обновляем материалы в ba_tz
        $this->updateMaterialInDeal($dealId);

        //$this->confirmTzClear($tzId);

        return [
            'success' => true,
            'data' => $price,
        ];
    }


    /**
     * @param $dealId
     * @param $materialId
     * @return array
     */
    public function deleteMaterialNew($dealId, $materialId)
    {
        $sql = $this->DB->Query(
            "select mtr.id, mtr.cipher, ugtp.measuring_sheet, 
                utr.actual_value utr_actual_value, ugtp.actual_value ugtp_actual_value 
            from ulab_material_to_request mtr
            inner join ulab_gost_to_probe ugtp on ugtp.material_to_request_id = mtr.id
            left join ulab_trial_results utr on utr.gost_to_probe_id = ugtp.id
            where mtr.material_id = {$materialId} and mtr.deal_id = {$dealId}"
        );

        $mtrIds = [];
        while ($row = $sql->Fetch()) {
            $value = json_decode($row['utr_actual_value'], true);
            $sheet = json_decode($row['measuring_sheet'], true);
            $mtrIds[] = $row['id'];
            $actualValue = $dealId >= DEAL_NEW_RESULT ? $row['ugtp_actual_value'] : $value[0];

            if (!empty($row['cipher'])) {
                return [
                    'success' => false,
                    'error' => "Невозможно удалить материал. Есть пробы, по которым сформирован акт."
                ];
            }

            if ($actualValue != '' || $sheet) {
                return [
                    'success' => false,
                    'error' => "Невозможно удалить материал. Есть испытания, у которых внесены результаты."
                ];
            }
        }

        if (!empty($mtrIds)) {
            $strId = implode(',', $mtrIds);

            $this->DB->Query("delete from ulab_gost_to_probe where material_to_request_id in ({$strId})");
        }

        $this->DB->Query("delete from ulab_material_to_request where material_id = {$materialId} and deal_id = {$dealId}");

        $requestModel = new Request();

        $tzId = $requestModel->getTzIdByDealId($dealId)['ID'];

        // обновляем цены у заявки
        $price = $this->updatePrice($dealId);
        // обновляем материалы в ba_tz
        $this->updateMaterialInDeal($dealId);

        //$this->confirmTzClear($tzId);

        return [
            'success' => true,
            'data' => $price,
        ];
    }


    /**
     * меняет материал
     * @param $dealId
     * @param $data
     */
    public function updateMaterial($dealId, $data)
    {
        foreach ($data as $materialId => $newMaterialId) {
            if ($materialId != $newMaterialId) {
                $this->DB->Update('ulab_material_to_request', ['material_id' => $newMaterialId], "WHERE deal_id = {$dealId} and material_id = {$materialId}");
            }
        }

        // обновляем материалы в ba_tz
        $this->updateMaterialInDeal($dealId);
    }


    /**
     * обновляет информацию по пробам и методам
     * @param $dealId
     * @param $data
     */
    public function updateProbeMethod($dealId, $data)
    {
        $materialModel = new Material();
        $requestModel = new Request();
        $probeModel = new Probe();
        $arrMaterial = [];

        foreach ($data as $materialId => $materialData) {

            $materialId = (int)$materialId;

            if (!empty($materialId)) {
                $arrMaterial[$materialId] = $materialModel->getById($materialId)['NAME'];
            }

            foreach ($materialData['probe'] as $probeId => $probeData) {

                $sqlProbeData = $this->prepearTableData('ulab_material_to_request', $probeData);

                if (stripos($probeId, 'new') === false) {
                    $this->DB->Update('ulab_material_to_request', $sqlProbeData, "WHERE id = {$probeId}");
                } else {
                    $sqlProbeData['material_id'] = $materialId;
                    $sqlProbeData['deal_id'] = $dealId;
                    $probeId = $this->DB->Insert('ulab_material_to_request', $sqlProbeData);
                }

                $probeModel->setLabHeaderByUmtrId($probeId);

                // обновляет методы
                foreach ($probeData['method'] as $ugtpId => $methodData) {
                    if (empty($methodData['new_method_id'])) {
                        continue;
                    }
                    $methodData['method_id'] = $methodData['new_method_id'];
                    $methodData['conditions_id'] = $methodData['tech_condition_id'];

                    $sqlMethodData = $this->prepearTableData('ulab_gost_to_probe', $methodData);

                    if (stripos($ugtpId, 'new') === false) {
                        $this->DB->Update('ulab_gost_to_probe', $sqlMethodData, "WHERE id = {$ugtpId}");
                    } else {
                        $sqlMethodData['material_to_request_id'] = $probeId;
                        $this->DB->Insert('ulab_gost_to_probe', $sqlMethodData);
                    }
                }
            }
        }

        if (!empty($arrMaterial)) {
            $strMaterial = implode(', ', $arrMaterial);
            $requestModel->updateTz($dealId, ['MATERIAL' => "'{$strMaterial}'"]);
        }
    }

    public function updateProbeMethodTest($dealId, $data)
    {
        foreach ($data as $key => $val) {
            $this->DB->Query("UPDATE `ulab_material_to_request` SET `name_for_protocol` = '{$val}' WHERE `id` = {$key}");
        }
    }

    /**
     * получение рук лабораторий и статуса тз
     * @param $dealId
     * @return array
     */
    public function getLabHead($dealId)
    {
        $sql = $this->DB->Query(
            "select distinct 
                        ugtp.new_method_id as method_id 
                    from ulab_material_to_request as umtr
                    left join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
                    where umtr.deal_id = {$dealId}"
        );

        $methodModel = new Methods();
        $labModel = new Lab();
        $userModel = new User();

        $labDuration = $this->getTzLabDurationList($dealId);

        $labsId = [
            'user' => [],
            'is_curr_user' => 0,
            'curr_user_status' => 0,
            'check_state' => CHECK_TZ_NOT_SENT,
        ];
        $state = [];
        while ($row = $sql->Fetch()) {
            $labs = $methodModel->getLab($row['method_id']);
            foreach ($labs as $lab) {
                $user = $userModel->checkHeader($labModel->getDepartmentIdByLabId($lab));
                if (empty($user)) {
                    continue;
                }

                $user['is_confirm'] = $this->getHasConfirmUser($dealId, $user['user_id']);
                $state[] = $user['is_confirm'];

                if ($_SESSION['SESS_AUTH']['USER_ID'] == $user['user_id']) {
                    $labsId['is_curr_user'] = 1;
                    $labsId['curr_user_status'] = $user['is_confirm'];
                }

                if (!empty($labDuration[$lab])) {
                    $user['duration_str'] = "{$labDuration[$lab]['date_start']}; {$labDuration[$lab]['days']} д.";
                } else {
                    $user['duration_str'] = '';
                }

                $labsId['user'][$lab] = $user;
            }
        }

        return $labsId;
    }

    /**
     * получение рук лабораторий и статуса тз
     * @param $dealId
     * @return array
     */
    public function getCancelLabHead($dealId)
    {
        $userModel = new User();

        $result = [];

        $sql = $this->DB->Query(
            "
            SELECT ct.id as 'ct_id', ct.*
            FROM ba_tz bt
            INNER JOIN check_tz_return ctr ON ctr.tz_id = bt.ID
            INNER JOIN CHECK_TZ ct ON ct.tz_id = ctr.tz_id AND ct.leader = ctr.user_return AND ct.date_return IS NOT NULL AND IFNULL(ct.date_return, '') <> ''
            WHERE bt.ID_Z = '{$dealId}'
            GROUP BY ct.leader
            "
        );

        while ($row = $sql->Fetch()) {
            $result[] = $userModel->getUserData($row['leader']);
        }

        return $result;
    }


    public function updateAssigned($dealId)
    {
        $this->DB->Query("delete from assigned_to_request where deal_id = {$dealId} and is_creator = 0");

        $sql = $this->DB->Query(
            "select distinct 
                        ugtp.new_method_id as method_id, ugtp.assigned_id 
                    from ulab_material_to_request as umtr
                    left join ulab_gost_to_probe as ugtp on ugtp.material_to_request_id = umtr.id
                    where umtr.deal_id = {$dealId}"
        );

        $methodModel = new Methods();
        $labModel = new Lab();
        $userModel = new User();

        $users = [];
        $labList = [];
        while ($row = $sql->Fetch()) {
            if (!empty($row['assigned_id'])) {
                $users[$row['assigned_id']] = $row['assigned_id'];
            }

            $labs = $methodModel->getLab($row['method_id']);
            foreach ($labs as $lab) {
                $depId = $labModel->getDepartmentIdByLabId($lab);
                $labList[$lab] = $depId;
                $user = $userModel->checkHeader($depId);
                if (empty($user) || in_array($lab, [5, 6])) {
                    continue;
                }

                $users[$user['user_id']] = $user['user_id'];
            }
        }

        if (!empty($labList)) {
            $labStr = implode(',', $labList);
            $this->DB->Update("ba_tz", ['LABA_ID' => "'{$labStr}'"], "where ID_Z = {$dealId}");
        }

        foreach ($users as $id) {
            $ass = [
                'deal_id' => $dealId,
                'user_id' => $id,
            ];

            $this->DB->Insert('assigned_to_request', $ass);
        }
    }


    /**
     * возвращает статус тз (подтвержден, не подтвержден, в ожидании, не отправлено на рассмотрение)
     * @param $dealId
     * @return int
     */
    public function getStateConfirm($dealId)
    {
        $requestModel = new Request();

        $tzId = $requestModel->getTzIdByDealId($dealId)['ID'];

        $isSertificationDeal = false;

        $dealSertification = $this->DB->Query("SELECT tests_for FROM ba_tz WHERE ID_Z = '{$dealId}'")->Fetch()['tests_for'];

        if ($dealSertification == "certification") {
            $checkCount = $this->DB->Query("SELECT id FROM CHECK_TZ WHERE tz_id = '{$tzId}'")->SelectedRowsCount();

            if ($checkCount == 1) {
                $isSertificationDeal = true;
            }
        }

        $sql = $this->DB->Query("select * from CHECK_TZ where tz_id = {$tzId}");

        $stateList = [];
        while ($row = $sql->Fetch()) {
            if ($row['confirm'] == 1 && empty($row['date_return'])) {
                $stateList[] = CHECK_TZ_APPROVE;
            } else if ($row['confirm'] == 0 && empty($row['date_return'])) {
                $stateList[] = CHECK_TZ_WAIT;
            } else {
                $stateList[] = CHECK_TZ_NOT_APPROVE;
            }
        }

        if (empty($stateList)) {
            return CHECK_TZ_NOT_SENT;
        }


        if ($isSertificationDeal) {
            return CHECK_TZ_NOT_SENT;
        }


        if (array_search(CHECK_TZ_NOT_APPROVE, $stateList) !== false) {
            $state = CHECK_TZ_NOT_APPROVE;
        } else if (array_search(CHECK_TZ_WAIT, $stateList) !== false) {
            $state = CHECK_TZ_WAIT;
        } else {
            $state = CHECK_TZ_APPROVE;
        }

        return $state;
    }


    /**
     * отправка тз на проверку
     * @param $dealId
     */
    public function confirmTzSent($dealId)
    {
        $labHead = $this->getLabHead($dealId);

        $requestModel = new Request();

        $tzId = $requestModel->getTzIdByDealId($dealId)['ID'];

        //$this->confirmTzClear($tzId);

        foreach ($labHead['user'] as $user) {
            $data = [
                'tz_id' => $tzId,
                'user_sent_id' => $_SESSION['SESS_AUTH']['USER_ID'],
                'date_submission' => date('Y-m-d H:i:s'),
                'leader' => $user['user_id'],
            ];

            $sqlData = $this->prepearTableData('CHECK_TZ', $data);

            $this->DB->Insert('CHECK_TZ', $sqlData);
        }
    }


    /**
     * отправка тз на проверку конкретным пользователям
     * @param $dealId
     * @param $userList
     */
    public function confirmTzSentUsers($dealId, $userList)
    {
        $requestModel = new Request();
        $probeModel = new Probe();


        $tzId = $requestModel->getTzIdByDealId($dealId)['ID'];
        //$this->confirmTzClear($tzId);

        foreach ($userList as $userId) {
            $data = [
                'tz_id' => $tzId,
                'user_sent_id' => $_SESSION['SESS_AUTH']['USER_ID'],
                'date_submission' => date('Y-m-d H:i:s'),
                'leader' => $userId,
            ];

            $sqlData = $this->prepearTableData('CHECK_TZ', $data);

            $this->DB->Insert('CHECK_TZ', $sqlData);
        }

        $probeModel->sendNotify($userList, $tzId);
    }

    /**
     * отправка тз на проверку конкретным пользователям
     * @param $dealId
     * @param $userList
     */
    public function confirmTzSentUsersForCanceled($dealId, $userList)
    {
        $requestModel = new Request();

        $tzId = $requestModel->getTzIdByDealId($dealId)['ID'];

        foreach ($userList as $value) {
            $this->DB->Query("UPDATE CHECK_TZ SET date_return = NULL, desc_return = NULL WHERE tz_id = '{$tzId}' AND leader = '{$value}'");
        }
    }


    /**
     * очистить отправку на подтверждение тз
     * @param $tzId
     */
    public function confirmTzClear($tzId)
    {
        //        $this->DB->Query("delete from CHECK_TZ where tz_id = {$tzId}");
    }


    /**
     * @param $tzId
     * @param $userId
     */
    public function clearConfirmForUser($tzId, $userId)
    {
        $data = [
            'confirm' => 0,
            'date_reply' => 'NULL',
            'desc_return' => 'NULL',
            'date_return' => 'NULL',
        ];

        $this->DB->Update('CHECK_TZ', $data, "where tz_id = {$tzId} and leader = {$userId}");
    }


    /**
     * @param $tzId
     * @param $userId
     */
    public function deleteConfirmForUser($tzId, $userId) // delete first
    {
        $this->DB->Query("delete from CHECK_TZ where tz_id = {$tzId} and leader = {$userId}");
    }


    /**
     * рук лаб подтверждает ТЗ
     * @param $tzId
     * @param $userId
     */
    public function confirmTzApprove($tzId, $userId, $dataForLabDuration = [])
    {
        $data = [
            'confirm' => 1,
            'date_reply' => date('Y-m-d H:i:s'),
        ];

        $sqlData = $this->prepearTableData('CHECK_TZ', $data);

        $this->DB->Update('CHECK_TZ', $sqlData, "where tz_id = {$tzId} and leader = {$userId}");

        if ($dataForLabDuration) {
            $labId = $this->DB->Query("SELECT ID FROM ba_laba WHERE HEAD_ID = '{$dataForLabDuration['leaderId']}'")->Fetch()['ID'];
            $info = $this->DB->Query("select id from ulab_tz_lab_duration where deal_id = {$dataForLabDuration['dealId']} and lab_id = {$labId}")->Fetch();
            $dateStart = date('Y-m-d', strtotime($dataForLabDuration['dateLeader']));
            if (!empty($info)) {
                $this->DB->Query("delete from ulab_tz_lab_duration where id = {$info['id']}");
                $this->DB->Query("INSERT INTO ulab_tz_lab_duration(deal_id, lab_id, date_start, days) VALUES('{$dataForLabDuration['dealId']}', '{$labId}', '{$dateStart}', '{$dataForLabDuration['daysLeader']}')");
            } else {
                $this->DB->Query("INSERT INTO ulab_tz_lab_duration(deal_id, lab_id, date_start, days) VALUES('{$dataForLabDuration['dealId']}', '{$labId}', '{$dateStart}', '{$dataForLabDuration['daysLeader']}')");
            }
        }
    }


    /**
     * рук лаб возвращает ТЗ
     * @param $tzId
     * @param $userId
     * @param string $desc
     */
    public function confirmTzNotApprove($tzId, $userId, $desc = "")
    {
        $data = [
            'confirm' => 0,
            'date_return' => date('Y-m-d H:i:s'),
            'desc_return' => $desc,
            'tz_id' => $tzId,
            'user_return' => $userId,
        ];

        $sqlData = $this->prepearTableData('CHECK_TZ', $data);

        $this->DB->Update('CHECK_TZ', $sqlData, "where tz_id = {$tzId} and leader = {$userId}");

        $sqlData = $this->prepearTableData('check_tz_return', $data);

        $this->DB->Insert('check_tz_return', $sqlData);
    }


    /**
     * @param $tzId
     * @return array
     */
    public function getDescConfirmTzNotApprove($tzId)
    {
        $userModel = new User();

        $sql = $this->DB->Query("select * from check_tz_return where tz_id = {$tzId} and is_fixed = 0 and date_return is not null");

        $result = [];

        while ($row = $sql->Fetch()) {
            $userInfo = $userModel->getUserShortById($row['user_return']);

            $row['user_name'] = $userInfo['short_name'];
            $row['date_return'] = date('d.m.Y H:i:s', strtotime($row['date_return']));

            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param $tzId
     * @return array
     */
    public function getConfirmTzNotApproveHistory($tzId)
    {
        $userModel = new User();

        $sql = $this->DB->Query("select * from check_tz_return where tz_id = {$tzId}");

        $result = [];

        while ($row = $sql->Fetch()) {
            $userReturnInfo = $userModel->getUserShortById($row['user_return']);
            $userFixInfo = $userModel->getUserShortById($row['user_fixed']);

            $row['user_return_name'] = $userReturnInfo['short_name'];
            $row['user_fix_name'] = $userFixInfo['short_name'];

            $row['date_return'] = date('d.m.Y H:i:s', strtotime($row['date_return']));
            $row['date_fixed'] = date('d.m.Y H:i:s', strtotime($row['date_fixed']));

            $result[] = $row;
        }

        return $result;
    }


    public function setConfirmTzFix($id, $tzId, $leaderId)
    {
        $userModel = new User();
        $userFixInfo = $userModel->getUserShortById($_SESSION['SESS_AUTH']['USER_ID']);

        $this->DB->Update('CHECK_TZ', ['date_return' => 'NULL', 'desc_return' => ''], "where tz_id = {$tzId} and leader = {$leaderId}");

        $curTime = date('Y-m-d H:i:s');

        $data = [
            'is_fixed' => 1,
            'date_fixed' => $curTime,
            'user_fixed' => $_SESSION['SESS_AUTH']['USER_ID'],
        ];

        $sqlData = $this->prepearTableData('check_tz_return', $data);

        $this->DB->Update('check_tz_return', $sqlData, "where id = {$id}");

        $this->DB->Query("UPDATE CHECK_TZ SET confirm = 0 WHERE tz_id = '{$tzId}' AND leader = '{$leaderId}'");

        return [
            'success' => true,
            'data' => [
                'user_name' => $userFixInfo['short_name'],
                'date_fixed' => $curTime
            ]
        ];
    }


    /**
     * проверил юзер тз
     * @param $dealId
     * @param $userId
     * @return int
     */
    public function getHasConfirmUser($dealId, $userId)
    {
        if (empty($userId)) {
            return CHECK_TZ_NOT_SENT;
        }

        $requestModel = new Request();

        $tzId = $requestModel->getTzIdByDealId($dealId)['ID'];

        $check = $this->DB->Query("select * from CHECK_TZ where tz_id = {$tzId} and leader = {$userId}")->Fetch();

        if (empty($check)) {
            return CHECK_TZ_NOT_SENT;
        } elseif ($check['confirm'] == 0 && !empty($check['date_return'])) {
            return CHECK_TZ_NOT_APPROVE;
        } else {
            return $check['confirm'];
        }
    }


    /**
     * @param $dealId
     * @param $data
     */
    public function setTzLabDuration($dealId, $data)
    {
        if (empty($data)) {
            return;
        }

        $labModel = new Lab();
        $requestModel = new Request();

        $labInfo = $labModel->getLabByUserId($data['user_id']);

        $tzId = $requestModel->getTzIdByDealId($dealId)['ID'];
        if (empty($labInfo)) {
            return;
        }
        $info111 = $this->DB->Query("select id from ulab_tz_lab_duration where deal_id = {$dealId} and lab_id = {$labInfo['ID']}")->Fetch();
        $info2 = $this->DB->Query("select confirm from CHECK_TZ where tz_id = {$tzId} and leader = {$data['user_id']}")->Fetch();

        $data['lab_id'] = $labInfo['ID'];
        $data['deal_id'] = $dealId;

        $dataSql = $this->prepearTableData('ulab_tz_lab_duration', $data);

        if (!empty($info111) && $info2['confirm'] == 1) {
            $info = $this->DB->Query("select * from ulab_tz_lab_duration where deal_id = {$data['deal_id']}");
            $dateStart = date('Y-m-d', strtotime($data['date_start']));
            while ($row = $info->Fetch()) {
                $this->DB->Query("delete from ulab_tz_lab_duration where deal_id = {$row['deal_id']}");
                //$this->DB->Query("UPDATE CHECK_TZ SET confirm = 0 WHERE tz_id = '{$tzId}'");
                //$this->DB->Query("UPDATE CHECK_TZ SET confirm = 1 WHERE tz_id = '{$tzId}' AND leader = '{$data['user_id']}'");
                $this->DB->Query("INSERT INTO ulab_tz_lab_duration_history(deal_id, lab_id, date_start, days) VALUES('{$row['deal_id']}', '{$row['lab_id']}', '{$row['date_start']}', '{$row['days']}')");
            }
            $this->DB->Query("INSERT INTO ulab_tz_lab_duration(deal_id, lab_id, date_start, days) VALUES('{$data['deal_id']}', '{$data['lab_id']}', '{$dateStart}', '{$data['days']}')");
            $this->DB->Update('ulab_tz_lab_duration', $dataSql, "where id = {$info111['id']}");
        } elseif ($info2['confirm'] == 1) {
            $this->DB->Insert('ulab_tz_lab_duration', $dataSql);
        }

        $duration = $this->getTzDuration($dealId);

        $this->DB->Update('ba_tz', ['DAY_TO_TEST' => $duration], "where ID_Z = {$dealId}");
    }


    /**
     * @param $dealId
     * @return array
     */
    public function getTzLabDuration($dealId)
    {
        $labModel = new Lab();

        $labInfo = $labModel->getLabByUserId($_SESSION['SESS_AUTH']['USER_ID']);

        if (empty($labInfo)) {
            return [];
        }

        return $this->DB->Query("select * from ulab_tz_lab_duration where deal_id = {$dealId} and lab_id = {$labInfo['ID']}")->Fetch();
    }


    /**
     * @param $dealId
     * @return array
     */
    public function getTzLabDurationList($dealId)
    {
        $sql = $this->DB->Query("select * from ulab_tz_lab_duration where deal_id = {$dealId}");

        $result = [];

        while ($row = $sql->Fetch()) {
            $row['date_start'] = StringHelper::dateRu($row['date_start']);
            $result[$row['lab_id']] = $row;
        }

        return $result;
    }


    /**
     * @param $dealId
     * @return mixed
     */
    public function getTzDuration($dealId)
    {
        if (empty($dealId)) {
            return 0;
        }

        $sql = $this->DB->Query(
            "SELECT date_start, days 
         FROM ulab_tz_lab_duration 
         WHERE deal_id = {$dealId}
         ORDER BY date_start"
        );

        $intervals = [];
        while ($row = $sql->Fetch()) {
            $intervals[] = [
                'date_start' => $row['date_start'],
                'days' => $row['days']
            ];
        }

        if (empty($intervals)) {
            return 0;
        }

        $minStartDate = $intervals[0]['date_start'];
        foreach ($intervals as $interval) {
            if ($interval['date_start'] < $minStartDate) {
                $minStartDate = $interval['date_start'];
            }
        }

        $maxEndDate = null;
        foreach ($intervals as $interval) {
            $endDate = $this->addWorkingDays($interval['date_start'], $interval['days']);
            if ($maxEndDate === null || $endDate > $maxEndDate) {
                $maxEndDate = $endDate;
            }
        }

        $workingDays = $this->calculateWorkingDaysBetween($minStartDate, $maxEndDate);

        return $workingDays + 3;
    }

    private function addWorkingDays($startDate, $workingDaysToAdd)
    {
        $holidays = [
            "2025-01-01",
            "2025-01-02",
            "2025-01-03",
            "2025-01-04",
            "2025-01-05",
            "2025-01-06",
            "2025-01-07",
            "2025-01-08",
            "2025-01-11",
            "2025-01-12",
            "2025-01-18",
            "2025-01-19",
            "2025-01-25",
            "2025-01-26",
            "2025-02-01",
            "2025-02-02",
            "2025-02-08",
            "2025-02-09",
            "2025-02-15",
            "2025-02-16",
            "2025-02-22",
            "2025-02-23",
            "2025-03-01",
            "2025-03-02",
            "2025-03-08",
            "2025-03-09",
            "2025-03-15",
            "2025-03-16",
            "2025-03-22",
            "2025-03-23",
            "2025-03-29",
            "2025-03-30",
            "2025-04-05",
            "2025-04-06",
            "2025-04-12",
            "2025-04-13",
            "2025-04-19",
            "2025-04-20",
            "2025-04-26",
            "2025-04-27",
            "2025-05-01",
            "2025-05-02",
            "2025-05-03",
            "2025-05-04",
            "2025-05-08",
            "2025-05-09",
            "2025-05-10",
            "2025-05-11",
            "2025-05-17",
            "2025-05-18",
            "2025-05-24",
            "2025-05-25",
            "2025-05-31",
            "2025-06-01",
            "2025-06-07",
            "2025-06-08",
            "2025-06-12",
            "2025-06-13",
            "2025-06-14",
            "2025-06-15",
            "2025-06-21",
            "2025-06-22",
            "2025-06-28",
            "2025-06-29",
            "2025-07-05",
            "2025-07-06",
            "2025-07-12",
            "2025-07-13",
            "2025-07-19",
            "2025-07-20",
            "2025-07-26",
            "2025-07-27",
            "2025-08-02",
            "2025-08-03",
            "2025-08-09",
            "2025-08-10",
            "2025-08-16",
            "2025-08-17",
            "2025-08-23",
            "2025-08-24",
            "2025-08-30",
            "2025-08-31",
            "2025-09-06",
            "2025-09-07",
            "2025-09-13",
            "2025-09-14",
            "2025-09-20",
            "2025-09-21",
            "2025-09-27",
            "2025-09-28",
            "2025-10-04",
            "2025-10-05",
            "2025-10-11",
            "2025-10-12",
            "2025-10-18",
            "2025-10-19",
            "2025-10-25",
            "2025-10-26",
            "2025-11-02",
            "2025-11-03",
            "2025-11-04",
            "2025-11-08",
            "2025-11-09",
            "2025-11-15",
            "2025-11-16",
            "2025-11-22",
            "2025-11-23",
            "2025-11-29",
            "2025-11-30",
            "2025-12-06",
            "2025-12-07",
            "2025-12-13",
            "2025-12-14",
            "2025-12-20",
            "2025-12-21",
            "2025-12-27",
            "2025-12-28",
            "2025-12-31"
        ];

        $currentDate = strtotime($startDate);
        $daysAdded = 0;

        while ($daysAdded < $workingDaysToAdd) {
            $currentDate = strtotime('+1 day', $currentDate);
            $currentDateStr = date('Y-m-d', $currentDate);

            if (!in_array($currentDateStr, $holidays)) {
                $daysAdded++;
            }
        }

        return date('Y-m-d', $currentDate);
    }

    private function calculateWorkingDaysBetween($startDate, $endDate)
    {
        $holidays = [
            "2025-01-01",
            "2025-01-02",
            "2025-01-03",
            "2025-01-04",
            "2025-01-05",
            "2025-01-06",
            "2025-01-07",
            "2025-01-08",
            "2025-01-11",
            "2025-01-12",
            "2025-01-18",
            "2025-01-19",
            "2025-01-25",
            "2025-01-26",
            "2025-02-01",
            "2025-02-02",
            "2025-02-08",
            "2025-02-09",
            "2025-02-15",
            "2025-02-16",
            "2025-02-22",
            "2025-02-23",
            "2025-03-01",
            "2025-03-02",
            "2025-03-08",
            "2025-03-09",
            "2025-03-15",
            "2025-03-16",
            "2025-03-22",
            "2025-03-23",
            "2025-03-29",
            "2025-03-30",
            "2025-04-05",
            "2025-04-06",
            "2025-04-12",
            "2025-04-13",
            "2025-04-19",
            "2025-04-20",
            "2025-04-26",
            "2025-04-27",
            "2025-05-01",
            "2025-05-02",
            "2025-05-03",
            "2025-05-04",
            "2025-05-08",
            "2025-05-09",
            "2025-05-10",
            "2025-05-11",
            "2025-05-17",
            "2025-05-18",
            "2025-05-24",
            "2025-05-25",
            "2025-05-31",
            "2025-06-01",
            "2025-06-07",
            "2025-06-08",
            "2025-06-12",
            "2025-06-13",
            "2025-06-14",
            "2025-06-15",
            "2025-06-21",
            "2025-06-22",
            "2025-06-28",
            "2025-06-29",
            "2025-07-05",
            "2025-07-06",
            "2025-07-12",
            "2025-07-13",
            "2025-07-19",
            "2025-07-20",
            "2025-07-26",
            "2025-07-27",
            "2025-08-02",
            "2025-08-03",
            "2025-08-09",
            "2025-08-10",
            "2025-08-16",
            "2025-08-17",
            "2025-08-23",
            "2025-08-24",
            "2025-08-30",
            "2025-08-31",
            "2025-09-06",
            "2025-09-07",
            "2025-09-13",
            "2025-09-14",
            "2025-09-20",
            "2025-09-21",
            "2025-09-27",
            "2025-09-28",
            "2025-10-04",
            "2025-10-05",
            "2025-10-11",
            "2025-10-12",
            "2025-10-18",
            "2025-10-19",
            "2025-10-25",
            "2025-10-26",
            "2025-11-02",
            "2025-11-03",
            "2025-11-04",
            "2025-11-08",
            "2025-11-09",
            "2025-11-15",
            "2025-11-16",
            "2025-11-22",
            "2025-11-23",
            "2025-11-29",
            "2025-11-30",
            "2025-12-06",
            "2025-12-07",
            "2025-12-13",
            "2025-12-14",
            "2025-12-20",
            "2025-12-21",
            "2025-12-27",
            "2025-12-28",
            "2025-12-31"
        ];

        $workingDays = 0;
        $currentDate = strtotime($startDate);
        $endTimestamp = strtotime($endDate);

        while ($currentDate < $endTimestamp) {
            $currentDateStr = date('Y-m-d', $currentDate);

            if (!in_array($currentDateStr, $holidays)) {
                $workingDays++;
            }

            $currentDate = strtotime('+1 day', $currentDate);
        }

        return $workingDays;
    }

    /**
     * @param string $gostName
     * @param int $dealId
     * @return array
     * @deprecated
     */
    public function getGostsByName(string $gostName, int $dealId): array
    {
        $result = [];

        if (empty($gostName)) {
            return $result;
        }

        $baGost = $this->DB->Query("SELECT * FROM ba_gost WHERE NUM_OA_NEW <> 0 AND NON_ACTUAL <> 1 AND GOST = {$this->quoteStr($this->DB->ForSql($gostName))}");

        $contract = $this->DB->Query("
            SELECT d_c.*, d_c.ID d_c_id, d.*, d.ID d_id
                FROM `DEALS_TO_CONTRACTS` d_c
                INNER JOIN `DOGOVOR` d ON d_c.ID_CONTRACT=d.ID
                WHERE `ID_DEAL` = {$dealId}
        ")->Fetch();

        while ($row = $baGost->Fetch()) {
            if (!empty($contract['ID']) && !empty($contract['LONGTERM']) && $row['ID']) {
                $priceForContracts = $this->DB->Query("
                    SELECT * FROM `PRICE_FOR_CONTRACTS`
                        WHERE `ID_CONTRACT` = {$contract['d_id']} AND `ID_GOST` = {$row['ID']}
                ")->Fetch();

                $row['PRICE'] = $priceForContracts['PRICE'] ?? $row['PRICE'];
            }

            $gostName = !empty($row['GOST']) ? trim($row['GOST']) : '';
            $gostYear = !empty($row['GOST_YEAR']) ? '-' . trim($row['GOST_YEAR']) : '';
            $gostPunkt = !empty($row['GOST_PUNKT']) ? ' ' . trim($row['GOST_PUNKT']) : '';
            $gostSpecification = !empty($row['SPECIFICATION']) ? ' | ' . trim($row['SPECIFICATION']) : '';

            $row['view_gost'] = $gostName . $gostYear . $gostPunkt . $gostSpecification;

            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param int $gostId
     * @return array
     * @deprecated
     */
    public function getGostById(int $gostId): array
    {
        $result = [];

        $gost = $this->DB->Query("SELECT * FROM ba_gost WHERE ID = {$gostId}")->Fetch();

        if ($gost) {
            $gostName = !empty($gost['GOST']) ? trim($gost['GOST']) : '';
            $gostYear = !empty($gost['GOST_YEAR']) ? '-' . trim($gost['GOST_YEAR']) : '';
            $gostPunkt = !empty($gost['GOST_PUNKT']) ? ' ' . trim($gost['GOST_PUNKT']) : '';
            $gostSpecification = !empty($gost['SPECIFICATION']) ? ' | ' . trim($gost['SPECIFICATION']) : '';
            $gost['is_old'] = empty($gost['ulab_method_id']) && $gost['IN_OA'] == 1;
            $gost['view_gost'] = $gostName . $gostYear . $gostPunkt . $gostSpecification;

            $result = $gost;
        }

        return $result;
    }


    /**
     * @param int $probeId
     * @return array
     * @deprecated
     */
    public function getGostsToProbe(int $probeId): array
    {
        $methodModel = new Methods();
        $tcModel = new TechCondition();
        $result = [];

        $gostToProbe = $this->DB->Query(
            "SELECT *, id as gtp_id FROM gost_to_probe WHERE probe_id = {$probeId}"
        );

        while ($row = $gostToProbe->Fetch()) {
            $method = [];
            $conditions = [];

            /*if ($row['new_method_id']) {
                $method = $methodModel->get($row['new_method_id']);
            } else */
            if ($row['gost_method']) {
                $method = $this->getGostById($row['gost_method']);
            }

            /*if ($row['tech_condition_id']) {
                $conditions = $tcModel->get($row['tech_condition_id']);
            } else */
            if ($row['gost_conditions']) {
                $conditions = $this->getGostById($row['gost_conditions']);
            }

            $row['method'] = $method;
            $row['conditions'] = $conditions;

            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param int $probeId
     * @return array
     * @deprecated
     */
    public function getUlabGostsToProbe(int $probeId): array
    {
        $methodModel = new Methods();
        $tcModel = new TechCondition();

        $result = [];

        $gostToProbe = $this->DB->Query(
            "SELECT * FROM gost_to_probe WHERE probe_id = {$probeId}"
        );

        while ($row = $gostToProbe->Fetch()) {
            $method = [];
            $conditions = [];

            $assignedIdList = [];
            if ($row['new_method_id']) {
                $method = $methodModel->get($row['new_method_id']);
                $assignedIdList = $methodModel->getAssigned($row['new_method_id']);
            }

            if ($row['tech_condition_id']) {
                $conditions = $tcModel->get($row['tech_condition_id']);
            } else if ($row['gost_conditions']) {
                $conditions = $this->getGostById($row['gost_conditions']);
            }

            $row['method'] = $method;
            $row['assigned_list'] = $assignedIdList;
            $row['conditions'] = $conditions;

            $result[] = $row;
        }

        return $result;
    }

    public function getFirstProbeToMaterials(int $materialToRequestId): array
    {
        $result = [];

        $probeToMaterials = $this->DB->Query(
            "SELECT *, COUNT(id) counts FROM probe_to_materials WHERE material_request_id = {$materialToRequestId}"
        )->Fetch();

        if (count($probeToMaterials) > 0) {
            $result = $probeToMaterials;
        }

        return $result;
    }

    public function getGostsByMaterialId(int $materialId): array
    {
        $result = [];

        $gosts = $this->DB->Query("SELECT DISTINCT GOST FROM ba_gost WHERE NUM_OA_NEW > 0 AND NON_ACTUAL <> 1 AND DOP = {$materialId} ORDER BY GOST");

        while ($row = $gosts->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    public function getMaterialProbeGostToRequest(int $dealId): array
    {
        $result = [];

        $materials = $this->DB->Query(
            "SELECT m.ID id, m.NAME name, mtr.ID mtr_id, mtr.OBIEM amount 
                    FROM MATERIALS_TO_REQUESTS mtr, MATERIALS m 
                    WHERE mtr.ID_DEAL = {$dealId} AND mtr.ID_MATERIAL <> 0 AND m.ID = mtr.ID_MATERIAL"
        );

        $key = 0;

        while ($row = $materials->Fetch()) {
            $row['name'] = !empty($row['name']) ? $row['name'] : 'Материал не выбран';

            $result['material'][] = $row;

            $probe = $this->getFirstProbeToMaterials($row['mtr_id']);

            $result['amount'][] = $row['amount']; //TODO: Почему сделали в MATERIALS_TO_REQUESTS поле OBIEM?

            if (!empty($probe['id'])) {
                //$result['amount'][] = $probe['counts'];

                $gosts = $this->getGostsToProbe($probe['id']);

                foreach ($gosts as $k => $i) {
                    $result['methods'][$key][$k]['name'] = $i['method']['view_gost'];
                    $result['methods'][$key][$k]['id'] = $i['method']['ID'] ?? $i['method']['id'];
                    $result['methods'][$key][$k]['gtp_id'] = $i['gtp_id'];
                    $result['methods'][$key][$k]['is_old'] = $i['method']['is_old'];

                    $result['conditions'][$key][$k]['name'] = $i['conditions']['view_gost'];
                    $result['conditions'][$key][$k]['id'] = $i['conditions']['ID'] ?? $i['conditions']['id'];

                    //$result['price'][$key][$k] = $i['method']['PRICE'];
                    $result['price'][$key][$k] = (float)$i['price'];
                    $result['assigned'][$key][$k] = $i['assigned'];
                }
            }

            $gostsGroup = $this->getGostsByMaterialId($row['id']);

            if (!empty($gostsGroup)) {
                $result['gosts_group'][$key] = $gostsGroup;
            }

            $key++;
        }

        return $result;
    }


    /**
     * @param int $dealId
     * @return array
     */
    public function getUlabMaterialProbeGostToRequest(int $dealId): array
    {
        $result = [];

        $materials = $this->DB->Query(
            "SELECT m.ID id, m.NAME name, mtr.ID mtr_id, mtr.OBIEM amount 
                    FROM MATERIALS_TO_REQUESTS mtr, MATERIALS m 
                    WHERE mtr.ID_DEAL = {$dealId} AND mtr.ID_MATERIAL <> 0 AND m.ID = mtr.ID_MATERIAL"
        );

        $key = 0;

        while ($row = $materials->Fetch()) {
            $row['name'] = !empty($row['name']) ? $row['name'] : 'Материал не выбран';

            $result['material'][] = $row;

            $probe = $this->getFirstProbeToMaterials($row['mtr_id']);

            $result['amount'][] = $row['amount']; //TODO: Почему сделали в MATERIALS_TO_REQUESTS поле OBIEM?

            if (!empty($probe['id'])) {
                //$result['amount'][] = $probe['counts'];

                $gosts = $this->getUlabGostsToProbe($probe['id']);

                foreach ($gosts as $k => $i) {
                    $result['methods'][$key][$k]['name'] = $i['method']['view_gost'];
                    $result['methods'][$key][$k]['id'] = $i['method']['id'];
                    $result['methods'][$key][$k]['assigned_list'] = $i['assigned_list'];

                    $result['conditions'][$key][$k]['name'] = $i['conditions']['view_name'];
                    $result['conditions'][$key][$k]['id'] = $i['conditions']['ID'] ?? $i['conditions']['id'];

                    $result['price'][$key][$k] = (float)$i['price'];
                    $result['assigned'][$key][$k] = $i['assigned'];
                }
            }

            $key++;
        }

        return $result;
    }


    /**
     * @param int $tzId
     * @param array $data
     */
    public function updateTzByIdTz(int $tzId, array $data)
    {
        $data['TAKEN_SERT_ISP'] = ($data['tests_for'] == 'certification' || $data['tests_for'] == 'declaration') ? 1 : 0;
        $data['add_info'] = json_encode($data['add_info']);

        $sqlData = $this->prepearTableData('ba_tz', $data);

        //        $this->pre($data, true);

        if ($data['tests_for'] == 'certification' || $data['tests_for'] == 'declaration') {
            $response = $this->DB->Query("SELECT id FROM CHECK_TZ WHERE tz_id = '{$tzId}' AND leader = 10")->Fetch()['id'];

            if (!$response) {
                $userId = $_SESSION['SESS_AUTH']['USER_ID'];
                $currentDate = date('Y-m-d H:i:s');

                $this->DB->Query("INSERT INTO CHECK_TZ(tz_id, user_sent_id, date_submission, leader, confirm) 
                                    VALUES('{$tzId}', '{$userId}', '{$currentDate}', 10, 0)");
            }
        } else if ($data['test_for'] == 'own') {
            $response = $this->DB->Query("SELECT id FROM CHECK_TZ WHERE tz_id = '{$tzId}' AND leader = 10")->Fetch()['id'];

            if ($response) {
                $this->DB->Query("DELETE FROM CHECK_TZ WHERE tz_id = '{$tzId}' AND leader = 10"); // delete second
            }
        }

        $where = "WHERE ID = {$tzId}";
        $this->DB->Update('ba_tz', $sqlData, $where);
    }


    /**
     * @param $dealId
     * @param $materialId
     * @param $number
     */
    public function addMaterialToTz($dealId, $materialId, $number)
    {

        $ugtp_id = 0;
        $data = [
            'deal_id' => $dealId,
            'material_id' => $materialId,
        ];

        $sql = $this->DB->Query("select max(probe_number) as probe_number, material_number from ulab_material_to_request where deal_id = {$dealId} and material_id = {$materialId}")->Fetch();
        $probeNumber = $sql['probe_number'];
        $data['material_number'] = $sql['material_number'];

        if (!is_numeric($probeNumber)) {
            $probeNumber = -1;

            $sql = $this->DB->Query("select max(material_number) as material_number from ulab_material_to_request where deal_id = {$dealId}")->Fetch();
            $materialNumber = $sql['material_number'];
            $data['material_number'] = ++$materialNumber;
        }

        for ($i = 0; $i < $number; $i++) {
            $data['probe_number'] = ++$probeNumber;

            $ugtp_id = $this->DB->Insert('ulab_material_to_request', $data);
        }

        return $ugtp_id;
    }


    /**
     * @param $ugtpId
     * @param $data
     */
    public function setCommentGtp($ugtpId, $data)
    {
        if (empty($ugtpId)) {
            return;
        }

        $sqlData = $this->prepearTableData('ulab_gost_to_probe', $data);

        $this->DB->Update('ulab_gost_to_probe', $sqlData, "where id = {$ugtpId}");
    }


    /**
     * @param $dealId
     * @param $arrMaterialId
     * @param $number
     */
    public function addProbeToMaterial($dealId, $arrMaterialId, $number)
    {
        foreach ($arrMaterialId as $materialId) {
            $sql = $this->DB->Query("select max(probe_number) as probe_number, material_number from ulab_material_to_request where deal_id = {$dealId} and material_id = {$materialId}")->Fetch();
            $probeNumber = $sql['probe_number'];

            $data = [
                'deal_id' => $dealId,
                'material_id' => $materialId,
                'material_number' => $sql['material_number'],
            ];

            for ($i = 0; $i < $number; $i++) {
                $data['probe_number'] = ++$probeNumber;

                $this->DB->Insert('ulab_material_to_request', $data);
            }
        }
    }


    /**
     * @param $probeIdList - material_to_request_id
     * @param $data
     */
    public function addMethodsToProbe($probeIdList, $data)
    {
        foreach ($probeIdList as $probeId) {
            $sql = $this->DB->Query("select max(gost_number) as gost_number from ulab_gost_to_probe where material_to_request_id = {$probeId}")->Fetch();
            $gostNumber = $sql['gost_number'];
            if (!is_numeric($gostNumber)) {
                $gostNumber = -1;
            }

            foreach ($data as $item) {
                $sqlData = [
                    'material_to_request_id' => $probeId,
                    'method_id' => $item['new_method_id'],
                    'new_method_id' => $item['new_method_id'],
                    'conditions_id' => "'{$item['name_nd']}'",
                    'tech_condition_id' => "'{$item['name_nd']}'" ?? null,
                    'price' => $item['price'],
                    'assigned_id' => $item['assigned_id'],
                    'gost_number' => ++$gostNumber,
                    'tech_condition_string' => "'{$item['nd_string_input']}'",
                    'changed_unit_id' => $item['unit_id'],
                    'permissible_value' => "'{$item['permissible_value']}'",
                    'saving_rates' => "'{$item['saving_rates']}'"
                ];

                $this->DB->Insert('ulab_gost_to_probe', $sqlData);
            }
        }
    }

    /**
     * @param $dealId
     * @param $probesArray
     * @return void
     * При добавлении методик проверяет, учавствуют ли уже лаборатории, относящиеся к добавленным методикам
     */
    public function checkHaveLeader($dealId, $probesArray): void
    {

        $resultModel = new Result();
        $idTz = $this->getTzIdByDealId($dealId);

        $checkRows = $this->DB->Query("SELECT id FROM CHECK_TZ WHERE tz_id = '{$idTz}'")->Fetch();

        if ($checkRows === false) return;

        foreach ($probesArray as $value) {
            $leadersResponse = $this->DB->Query(
                "
                SELECT bl.HEAD_ID, bl.ID
                FROM ulab_methods_lab uml   
                INNER JOIN ba_laba bl ON bl.ID = uml.lab_id
                WHERE uml.method_id = '{$value['new_method_id']}'
                "
            );

            while ($row = $leadersResponse->Fetch()) {

                $checkLeader = $this->DB->Query("SELECT id FROM CHECK_TZ WHERE tz_id = '{$idTz}' AND leader = '{$row['HEAD_ID']}'")->Fetch();

                if ($checkLeader) continue;
                $currentDate = date('Y-m-d H:i:s');

                $this->DB->Query("INSERT INTO CHECK_TZ (tz_id, user_sent_id, date_submission, leader, confirm) 
                                    VALUES('{$idTz}', '{$_SESSION['SESS_AUTH']['USER_ID']}', '{$currentDate}', '{$row['HEAD_ID']}', 0)");
            }
        }
    }

    /**
     * @param $umtrId
     * @param $newDealId
     * @return array|bool[]
     */
    public function copyProbeToDeal($umtrId, $newDealId)
    {
        $mtr = $this->DB->Query("select * from ulab_material_to_request where id = {$umtrId} /*and (child_deal_id is null or child_deal_id = 0)*/")->Fetch();
        $sql = $this->DB->Query("select * from ulab_gost_to_probe where material_to_request_id = {$umtrId}");

        if (empty($mtr)) {
            return [
                'success' => false,
                'error' => "Проба уже перенесена"
            ];
        }

        unset($mtr['id']);
        unset($mtr['child_deal_id']);
        $mtr['deal_id'] = $newDealId;
        $sqlData = $this->prepearTableData('ulab_material_to_request', $mtr);

        $newMtrId = $this->DB->Insert('ulab_material_to_request', $sqlData);
        $this->DB->Update('ulab_material_to_request', ['child_deal_id' => $newDealId], "where id = {$umtrId}");

        while ($row = $sql->Fetch()) {

            unset($row['id']);
            $row['material_to_request_id'] = $newMtrId;

            $sqlData2 = $this->prepearTableData('ulab_gost_to_probe', $row);

            $this->DB->Insert('ulab_gost_to_probe', $sqlData2);
        }

        return [
            'success' => true,
        ];
    }


    /**
     * @return array
     */
    public function getObjects(): array
    {
        $result = [];
        $materials = $this->DB->Query("SELECT * FROM DEV_OBJECTS");

        while ($row = $materials->Fetch()) {
            $row['COORD'] = unserialize($row['COORD']);;

            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param int $dealId
     * @param int $companyId
     * @return array
     */
    public function getRequestsToCompany(int $dealId, int $companyId): array
    {
        $results = [];

        if (empty($companyId)) {
            return $results;
        }

        $deals = CCrmDeal::GetListEx(['DATE_CREATE' => 'DESC'], ['COMPANY_ID' => $companyId, '!=ID' => $dealId]);

        while ($deal = $deals->Fetch()) {
            $request = $this->getTzByDealId($deal['ID']);

            //            if (empty($request['PRICE']) || $request['PRICE'] === '--') {
            //                continue;
            //            }

            $request['DATE_CREATE'] = !empty($request['DATE_CREATE']) && $request['DATE_CREATE'] !== '' ?
                StringHelper::dateRu($request['DATE_CREATE']) : '';

            $results[] = $request;
        }

        return $results;
    }


    public function getTakenRequest($takenTzId): array
    {
        $results = [];

        if (empty($takenTzId) || !is_numeric($takenTzId)) {
            return $results;
        }

        $request = $this->getTzByTzId($takenTzId);

        if (count($request) > 0) {
            $request['DATE_CREATE'] = !empty($request['DATE_CREATE']) && $request['DATE_CREATE'] !== '' ?
                StringHelper::dateRu($request['DATE_CREATE']) : '';

            $results = $request;
        }

        return $results;
    }


    /**
     * @param $takenDealId
     * @return array
     */
    public function getDealByTakenDeal($takenDealId)
    {
        $sql = $this->DB->Query("SELECT ID_Z, REQUEST_TITLE FROM ba_tz WHERE TAKEN_ID_DEAL = {$takenDealId}");

        $result = [];

        while ($row = $sql->Fetch()) {
            $result[] = [
                'id' => $row['ID_Z'],
                'title' => $row['REQUEST_TITLE'],
            ];
        }

        return $result;
    }


    /**
     * @param array $methodsId
     * @return array
     */
    public function getLabsByMethodsId(array $methodsId): array
    {
        $response = [];
        $strMethodsId = implode(', ', $methodsId);

        var_dump($strMethodsId);
        exit();

        $gosts = $this->DB->Query("SELECT * FROM ba_gost WHERE ID IN ({$strMethodsId})");

        while ($row = $gosts->Fetch()) {
            if ($row['LFHI']) $response[$row['ID']][] = self::LABS['LFHI'];
            if ($row['LFMI']) $response[$row['ID']][] = self::LABS['LFMI'];
            if ($row['DSL']) $response[$row['ID']][] = self::LABS['DSL'];
            if ($row['LSM']) $response[$row['ID']][] = self::LABS['LSM'];
            if ($row['OSK']) $response[$row['ID']][] = self::LABS['OSK'];

            if (empty($row['LFHI']) && empty($row['LFMI']) && empty($row['DSL']) && empty($row['LSM']) && empty($row['OSK'])) {
                $response[$row['ID']] = [];
            }
        }

        return $response;
    }

    /**
     * @param array $labsId
     * @return array
     */
    public function getUserByLabId(array $labsId): array
    {
        $response = [];

        if (empty($labsId)) {
            return $response;
        }

        $by = 'ID';
        $order = 'asc';

        $dbUsers = CUser::GetList(
            $by,
            $order,
            ['ACTIVE' => 'Y', 'UF_DEPARTMENT' => $labsId],
            ['SELECT' => array('UF_*')]
        );

        $num = 0;
        while ($row = $dbUsers->Fetch()) {
            $shortName = StringHelper::shortName($row['NAME']);
            $departmentsData = CIntranetUtils::GetDepartmentsData($row["UF_DEPARTMENT"]);

            $keyLab = current($row["UF_DEPARTMENT"]);

            $response[$keyLab][$num]['department_name'] = current($departmentsData);
            $response[$keyLab][$num]['user_id'] = $row['ID'];
            $response[$keyLab][$num]['name'] = $row['NAME'];
            $response[$keyLab][$num]['last_name'] = $row['LAST_NAME'];
            $response[$keyLab][$num]['second_name'] = $row['SECOND_NAME'];
            $response[$keyLab][$num]['user_name'] = "{$row['LAST_NAME']} {$row['NAME']}";
            $response[$keyLab][$num]['short_name'] = "{$shortName}. {$row['LAST_NAME']}";
            $response[$keyLab][$num]['department'] = $row['UF_DEPARTMENT'];

            $num++;
        }

        return $response;
    }

    /**
     * @param int $tzId
     * @return array
     */
    public function getUnconfirmedTz(int $tzId): array
    {
        $response = [];

        if (empty($tzId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`={$tzId} AND confirm = 0 AND `date_return` IS NULL");

        while ($row = $result->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    /**
     * @param int $tzId
     * @return bool
     */
    public function isConfirm(int $tzId)
    {
        $result = $this->DB->Query("SELECT count(*) c FROM `CHECK_TZ` WHERE `tz_id`={$tzId} AND confirm = 0 AND `date_return` IS NULL")->Fetch();
        if ($result['c'] > 0) {
            return false;
        }

        return true;
    }


    /**
     * @param int $tzId
     * @return array
     */
    public function getConfirmedTz(int $tzId): array
    {
        $response = [];

        if (empty($tzId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`={$tzId} AND confirm = 1 AND `date_return` IS NULL");

        while ($row = $result->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    //TODO: Костыль для утверждения заявки

    /**
     * @param int $tzId
     * @return bool
     */
    public function isConfirmTz(int $tzId)
    {
        $result = $this->DB->Query("SELECT * FROM `CHECK_TZ` WHERE `tz_id`={$tzId}");

        if ($result->SelectedRowsCount() > 0) {
            while ($row = $result->Fetch()) {
                if (empty($row['confirm'])) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }


    /**
     * @param int $tzId
     * @return array
     */
    public function getLabLeadersTZ(int $tzId): array
    {
        $response = [];

        if (empty($tzId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM CHECK_TZ WHERE tz_id={$tzId} AND leader IS NOT NULL AND leader <> '' AND date_return IS NULL");

        while ($row = $result->Fetch()) {
            $response[] = $row['leader'];
        }

        return $response;
    }

    /**
     * @param int $tzId
     * @return array
     */
    public function getCheckTzByIdTz(int $tzId): array
    {
        $response = [];

        if (empty($tzId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM CHECK_TZ WHERE tz_id={$tzId} AND `date_return` IS NULL");

        while ($row = $result->Fetch()) {
            $response[$row['leader']] = $row;
        }

        return $response;
    }

    /**
     * @param array $methodsId
     * @return array
     */
    public function getMethodsNotInOA(array $methodsId): array
    {
        $response = [];

        if (empty($methodsId)) {
            return $response;
        }

        $strMethodsId = implode(', ', $methodsId);

        $result = $this->DB->Query("SELECT * FROM ba_gost WHERE IN_OA <> 1 AND ID IN ({$strMethodsId}) AND ID NOT IN (2875, 5962) AND GOST_TYPE <> 'metodic_otbor'");

        while ($row = $result->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    /**
     * @param int $tzId
     * @return array
     */
    public function getInvoice(int $tzId): array
    {
        $response = [];

        if (empty($tzId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM INVOICE WHERE TZ_ID = {$tzId}")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * @param int $dealId
     * @return array
     */
    public function getActBaseByDealId(int $dealId): array
    {
        $response = [];

        if (empty($dealId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM `ACT_BASE` WHERE `ID_Z`={$dealId}")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * @param array $data
     */
    public function saveHistory(array $data)
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $this->DB->Insert('HISTORY', $data);
    }

    /**
     * @param int $tzId
     * @return array
     */
    public function getLastHistory(int $tzId): array
    {
        $response = [];

        if (empty($tzId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM HISTORY WHERE TZ_ID = {$tzId} ORDER BY DATE DESC LIMIT 1")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }


    //TODO: Временное сохранение данных, сохраняет сериализованные данные, для работы остальных скриптов до их рефакторинга
    public function savingSerializedData(int $tzId, array $arrData, array $probe)
    {
        $arr = [];
        $i = 0;
        foreach ($arrData as $keys => $arrDatums) {
            foreach ($arrDatums as $key => $arrDatum) {
                foreach ($arrDatum as $item) {
                    $i++;
                    $arr[$i]['mater'] = $key;
                    $arr[$i]['gost'] = $item['gost'];
                    $arr[$i]['obiem'] = $item['obiem'];
                    $arr[$i]['gost_new'] = $item['gost_new'];
                    $arr[$i]['price'] = $item['price'];
                    //TODO: ДИЧАЙШИЙ КОСТЫЛЬ ДЛЯ ВЫВОДА МАТЕРИАЛОВ ИЗ КОПИРОВАННЫХ ЗАЯВОК! До рефакторинга копирования
                    $mater[] = $arr[$i]['mater'];
                }
            }
        }

        $dataTz = [
            'TZ' => serialize($arr),
            'PROBE' => serialize($probe),
            'MATERIAL' => implode(', ', $mater) //переделать
        ];

        $this->updateTzByIdTz($tzId, $dataTz);
    }

    //TODO: Сохранение измененных шифров в ACT_BASE и probe_to_materials
    public function savingShNumbersToActBase(int $dealId, int $actNum)
    {

        $y_date = $this->getActBase($dealId);

        $sh = 1;
        $probe_arr = [];
        $res_sh_num = $this->DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`= {$dealId}");
        $h = 0;
        while ($probeinform = $res_sh_num->Fetch()) {
            $z = 0;
            $NumProbe = [];
            for ($z = 0; $z < $probeinform['OBIEM']; $z++) {
                $NumProbe[$z] = $actNum . '.' . $sh . '/' . $y_date['year'];
                $sh++;
                $probe_arr[$h] = $NumProbe;
            }
            $h++;
        }
        $probe_arr = json_encode($probe_arr);
        $this->DB->Query("UPDATE `ACT_BASE` SET `PROBE` = '{$probe_arr}' WHERE ID_Z={$dealId}");
    }

    /**
     * @param int $dealId
     * @return array
     */
    public function getCountMaterials(int $dealId): array
    {
        $response = [];

        $result = $this->DB->Query("SELECT count(DISTINCT(umtr.material_id)) count_material 
            FROM ulab_material_to_request umtr WHERE umtr.deal_id = {$dealId} AND umtr.mtr_id <> 0")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * TODO: временный метод пока не будет сделано редактирование ТЗ с заполнеными данными результатов испытаний
     * получить кол-во заполненных данных в результатах испытаний
     * @param int $dealId
     * @return array
     */
    public function getCountFilledResultData(int $dealId): array
    {
        $response = [];

        if (empty($dealId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT COUNT(*) count_umtr     
            FROM ulab_material_to_request umtr 
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id 
            LEFT JOIN ulab_trial_results utr ON utr.gost_to_probe_id = ugtp.id 
            LEFT JOIN PROTOCOLS p ON p.ID = umtr.protocol_id 
            WHERE umtr.deal_id = {$dealId} AND (utr.id IS NOT NULL OR p.ID IS NOT NULL)")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }


    /**
     * @param int $ugtpId
     * @return array
     */
    public function getGostToProbe(int $ugtpId): array
    {
        $response = [];

        if (empty($ugtpId)) {
            return $response;
        }

        $result = $this->DB->Query(
            "SELECT ugtp.*, umtr.probe_number, umtr.material_number, umtr.cipher, umtr.name_for_protocol, umtr.id as umtr_id, umtr.material_id
                    FROM ulab_gost_to_probe as ugtp 
                    inner join ulab_material_to_request as umtr on umtr.id = ugtp.material_to_request_id
                    WHERE ugtp.id = {$ugtpId}"
        )->Fetch();

        if (!empty($result)) {
            $result['measuring_sheet'] = json_decode($result['measuring_sheet'], true);
            if (empty($result['cipher'])) {
                $number = $result['probe_number'] + 1;
                $result['cipher'] = "Не присвоен шифр #{$number}";
            }

            $response = $result;
        }

        return $response;
    }

    public function getGostToProbe2(int $ugtpId): array
    {
        $response = [];

        if (empty($ugtpId)) {
            return $response;
        }

        $result = $this->DB->Query(
            "SELECT ugtp.*, umtr.probe_number, umtr.material_number, umtr.cipher, umtr.name_for_protocol, umtr.id as umtr_id, umtr.material_id
                    FROM ulab_gost_to_probe as ugtp 
                    inner join ulab_material_to_request as umtr on umtr.id = ugtp.material_to_request_id
                    WHERE ugtp.protocol_id = {$ugtpId}"
        )->Fetch();

        if (!empty($result)) {
            $result['measuring_sheet'] = json_decode($result['measuring_sheet'], true);
            if (empty($result['cipher'])) {
                $number = $result['probe_number'] + 1;
                $result['cipher'] = "Не присвоен шифр #{$number}";
            }

            $response = $result;
        }

        return $response;
    }


    public function getAssignedGostToProbe($dealId)
    {
        $result = [];

        $response = $this->DB->Query("
            SELECT ugtp.assigned_id
            FROM ulab_material_to_request umtr
            LEFT JOIN  ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            WHERE deal_id = {$dealId}
        ");

        while ($row = $response->Fetch()) {
            $result[] = $row['assigned_id'];
        }

        return $result;
    }

    public function getMtrToDeal($dealId)
    {
        $result = [];

        $response = $this->DB->Query("
            SELECT umtr.id
            FROM ulab_material_to_request umtr
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            WHERE deal_id = '{$dealId}'
        ");

        while ($row = $response->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @param $umtrIdList
     * @return array
     */
    public function getGroupProbeMethod($umtrIdList)
    {
        $methodModel = new Methods();

        $strUmtr = implode(',', $umtrIdList);

        $sql = $this->DB->Query(
            "select distinct ugtp.method_id, ugtp.assigned_id from ulab_material_to_request as umtr
                    inner join ulab_gost_to_probe as ugtp on umtr.id = ugtp.material_to_request_id
                    where umtr.id in ({$strUmtr})"
        );

        $result = [];

        while ($row = $sql->Fetch()) {
            $row['method'] = $methodModel->get($row['method_id']);
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Обновляет методику для одной пробы (material_to_request_id).
     * Если $methodId пустой или null — методика будет удалена (удаляются все записи ulab_gost_to_probe для этой пробы).
     * Если $methodId задан — обновляет существующую методику или добавляет новую, если её нет.
     *
     * @param int $probeId ID пробы (material_to_request_id)
     * @param array $formData
     * @return void
     */
    public function setProbeMethod($probeId, $dealId, $formData)
    {
        // 1. Методика не выбрана на форме массового редактирования - ничего не делаем
        if (!isset($formData['method_id'])) {
            return;
        }

        $methodId = (int)$formData['method_id'];
        $probeId = (int)$probeId;
        $dealId = (int)$dealId;


        // 2. Выбрана опция "Нет методики" - удаляем все методики у этой пробы
        if ($methodId === 0) {
            $this->DB->Query("DELETE FROM ulab_gost_to_probe WHERE material_to_request_id = {$probeId}");

            return;
        }

        // 3. Проверяем, есть ли уже такая методика у этой пробы
        $exists = $this->DB->Query("SELECT id FROM ulab_gost_to_probe WHERE material_to_request_id = {$probeId} AND new_method_id = {$methodId}")->Fetch();
        if ($exists) {
            return;
        }

        // 4. Добавляем новую методику к пробе
        $sql = $this->DB->Query("SELECT max(gost_number) as gost_number FROM ulab_gost_to_probe WHERE material_to_request_id = {$probeId}")->Fetch();
        $gostNumber = $sql['gost_number'];

        if (!is_numeric($gostNumber)) {
            $gostNumber = -1;
        }

        $sqlData = [
            'material_to_request_id' => $probeId,
            'method_id' => $methodId,
            'new_method_id' => $methodId,
            'conditions_id' => $formData['tech_condition_id'] ?? null,
            'tech_condition_id' => $formData['tech_condition_id'] ?? null,
            'price' => $formData['price'] ?? null,
            'assigned_id' => $formData['assigned_id'] ?? null,
            'gost_number' => ++$gostNumber,
        ];

        $this->DB->Insert('ulab_gost_to_probe', $sqlData);

        // обновляем цены у заявки
        $this->updatePrice($dealId);
    }


    /**
     * @param $umtrId
     * @param $infoList
     */
    public function setGroupAssignedProbeMethod($umtrId, $infoList)
    {
        foreach ($infoList as $methodId => $userId) {
            $this->DB->Update('ulab_gost_to_probe', ['assigned_id' => $userId], "where method_id = {$methodId} and material_to_request_id = {$umtrId}");
        }
    }

    /**
     * @param $umtrId
     * @param $infoList
     */
    public function setGroupMaterialGroup($umtrId, $infoList)
    {
        foreach ($infoList as $materialId => $groupId) {
            $this->DB->Update('ulab_material_to_request', ['group' => $groupId], "where material_id = {$materialId} and id = {$umtrId}");
        }
    }


    /**
     * @param $umtrId
     */
    public function copyProbe($umtrId)
    {
        if (empty($umtrId)) {
            return [];
        }

        $umtr = $this->DB->Query("select * from ulab_material_to_request where id = {$umtrId}")->Fetch();

        if (empty($umtr)) {
            return [];
        }

        $maxNumber = $this->DB->Query("select max(probe_number) as max_number from ulab_material_to_request where deal_id = {$umtr['deal_id']} and material_id = {$umtr['material_id']}")->Fetch();

        $sql = $this->DB->Query("select * from ulab_gost_to_probe where material_to_request_id = {$umtrId}");

        unset($umtr['id']);
        unset($umtr['state']);
        unset($umtr['child_deal_id']);
        unset($umtr['protocol_id']);
        unset($umtr['in_act']);
        unset($umtr['cipher']);
        $umtr['probe_number'] = $maxNumber['max_number'] + 1;

        $sqlData = $this->prepearTableData('ulab_material_to_request', $umtr);

        $newUmtrId = $this->DB->Insert("ulab_material_to_request", $sqlData);

        while ($row = $sql->Fetch()) {
            unset($row['id']);
            unset($row['protocol_id']);
            $row['material_to_request_id'] = $newUmtrId;

            $sqlData = $this->prepearTableData('ulab_gost_to_probe', $row);

            $this->DB->Insert("ulab_gost_to_probe", $sqlData);
        }

        return $this->updatePrice($umtr['deal_id']);
    }

    /**
     * @param $umtrId
     */
    public function copyMassiveProbe($umtrId, $count)
    {
        if (empty($umtrId)) {
            return [];
        }

        $umtr = $this->DB->Query("select * from ulab_material_to_request where id = {$umtrId}")->Fetch();

        if (empty($umtr)) {
            return [];
        }

        $additionalSumToDeal = 0;

        $this->DB->Query("UPDATE ulab_material_to_request SET probe_number = probe_number + {$count} 
                                WHERE deal_id = '{$umtr['deal_id']}' 
                                  AND probe_number > {$umtr['probe_number']} AND material_number >= {$umtr['material_number']}");

        $ugtpResponse = $this->DB->Query("SELECT * FROM ulab_gost_to_probe WHERE material_to_request_id = '{$umtr['id']}'");

        $ugtpResult = [];

        while ($row = $ugtpResponse->Fetch()) {
            $ugtpResult[] = $row;
        }

        for ($i = 1; $i <= $count; $i++) {
            $currentProbeNumber = $umtr['probe_number'] + $i;

            $this->DB->Query(
                "
                INSERT INTO ulab_material_to_request(mtr_id, deal_id, material_id, probe_number, material_number, name_for_protocol
                , is_only_name_for_protocol, `group`, place, date_probe, quarry_id) 
                VALUES('{$umtr['mtr_id']}', '{$umtr['deal_id']}', '{$umtr['material_id']}', '{$currentProbeNumber}', '{$umtr['material_number']}'
                , '{$umtr['name_for_protocol']}', '{$umtr['is_only_name_for_protocol']}', '{$umtr['group']}', '{$umtr['place']}'
                , '{$umtr['date_probe']}', '{$umtr['quarry_id']}')
                "
            );

            $currentUmtrId = $this->DB->Query("SELECT id FROM ulab_material_to_request ORDER BY id DESC LIMIT 1")->Fetch()['id'];

            foreach ($ugtpResult as $value) {
                $json = json_encode(json_decode($value['measuring_sheet']));

                $this->DB->Query(
                    "
                    INSERT INTO ulab_gost_to_probe(method_id, new_method_id, conditions_id, tech_condition_id, price
                    , material_to_request_id, gost_number, measuring_sheet, assigned_id, normative_val, actual_value
                    , `match`, is_confirm_oa, protocol_id, comment, `sequence`, description) 
                    VALUES ('{$value['method_id']}', '{$value['new_method_id']}', '{$value['conditions_id']}', '{$value['tech_condition_id']}'
                    , '{$value['price']}', '{$currentUmtrId}', '{$value['gost_number']}', '{$json}'
                    , '{$value['assigned_id']}', '{$value['normative_val']}', '{$value['actual_value']}', '{$value['match']}'
                    , '{$value['is_confirm_oa']}', '{$value['protocol_id']}', '{$value['comment']}', '{$value['sequence']}'
                    , '{$value['description']}')
                    "
                );

                $additionalSumToDeal += $value['price'];
            }
        }

        $dealArray = $this->DB->Query("SELECT ID, DISCOUNT, price_discount, PRICE FROM ba_tz WHERE ID_Z = '{$umtr['deal_id']}'")->Fetch();

        $dealArray['PRICE'] += $additionalSumToDeal;

        $this->DB->Query("UPDATE ba_tz SET PRICE = {$dealArray['PRICE']} WHERE ID = '{$dealArray['ID']}'");

        return $dealArray['ID'];
    }

    public function getCommentGtp($ugtp_id)
    {
        $result = [];

        $response = $this->DB->Query(
            "
            SELECT comment, sequence
            FROM ulab_gost_to_probe
            WHERE id = '{$ugtp_id}'
            "
        );

        while ($row = $response->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    public function updateGroupType($data)
    {
        $this->DB->Query("UPDATE ba_tz SET group_type = '{$data['group_type']}' WHERE ID = '{$data['tz_id']}'");
    }

    /**
     * Заменяет методику для существующей пробы (material_to_request_id).
     *
     * @param int $probeId ID пробы (material_to_request_id)
     * @param int $oldMethodId ID метода для замены
     * @param array $newMethodData Параметры нового метода
     * @return void
     */
    public function replaceMethodForProbe($probeId, $oldMethodId, $newMethodData)
    {
        $probeId = (int)$probeId;
        $oldMethodId = (int)$oldMethodId;
        $newMethodId = (int)$newMethodData['id'];

        // Получаем все записи с этой пробой и старой методикой
        $ugtpRes = $this->DB->Query(
            "SELECT * FROM ulab_gost_to_probe WHERE material_to_request_id = {$probeId} AND new_method_id = {$oldMethodId}"
        );

        $updated = false;
        $dealId = null;

        while ($ugtp = $ugtpRes->Fetch()) {
            // Проверка: есть ли результаты испытаний
            $trial = $this->DB->Query(
                "SELECT utr.actual_value, ugtp.measuring_sheet
             FROM ulab_gost_to_probe ugtp
             LEFT JOIN ulab_trial_results utr ON utr.gost_to_probe_id = ugtp.id
             WHERE ugtp.id = {$ugtp['id']}"
            )->Fetch();

            $hasResults = false;
            if (!empty($trial)) {
                $actualValue = json_decode($trial['actual_value'], true);
                $sheet = json_decode($trial['measuring_sheet'], true);
                if (!empty($trial['actual_value']) || !empty($trial['measuring_sheet']) || !empty($actualValue) || !empty($sheet)) {
                    $hasResults = true;
                }
            }

            // Если есть результаты испытаний — пропускаем эту запись
            if ($hasResults) {
                continue;
            }

            // Получаем dealId только один раз (он одинаковый для всех записей этой пробы)
            if ($dealId === null) {
                $umtr = $this->DB->Query("SELECT deal_id FROM ulab_material_to_request WHERE id = {$probeId}")->Fetch();
                $dealId = $umtr['deal_id'] ?? null;
            }

            // Если новая методика совпадает со старой — обновить параметры
            if ($newMethodId === $oldMethodId) {
                $updateData = [
                    'conditions_id' => $newMethodData['tech_condition_id'] ?? null,
                    'tech_condition_id' => $newMethodData['tech_condition_id'] ?? null,
                    'price' => $newMethodData['price'] ?? null,
                    'assigned_id' => $newMethodData['assigned_id'] ?? null,
                ];

                $this->DB->Update('ulab_gost_to_probe', $updateData, "where id = {$ugtp['id']}");
                $updated = true;
                continue;
            }

            // Если новая методика отличается — заменить method_id, new_method_id и параметры
            $updateData = [
                'method_id' => $newMethodId,
                'new_method_id' => $newMethodId,
                'conditions_id' => $newMethodData['tech_condition_id'] ?? null,
                'tech_condition_id' => $newMethodData['tech_condition_id'] ?? null,
                'price' => $newMethodData['price'] ?? null,
                'assigned_id' => $newMethodData['assigned_id'] ?? null,
            ];

            $this->DB->Update('ulab_gost_to_probe', $updateData, "where id = {$ugtp['id']}");
            $updated = true;
        }

        // Если были изменения — обновляем цену
        if ($updated && $dealId) {
            $this->updatePrice($dealId);
        }
    }

    public function replaceMethodListNew($probeId, $oldMethodId, $newMethodData)
    {
        $ugtpResponse = $this->DB->Query("SELECT * FROM ulab_gost_to_probe WHERE material_to_request_id = '{$probeId}' AND method_id = {$oldMethodId}");

        while ($value = $ugtpResponse->Fetch()) {
            $this->DB->Query(
                "
                            UPDATE ulab_gost_to_probe 
                            SET method_id = '{$newMethodData['id']}'
                            , new_method_id = '{$newMethodData['id']}'
                            , conditions_id = '{$newMethodData['tech_condition_id']}'
                            , tech_condition_id = '{$newMethodData['tech_condition_id']}'
                            , price = '{$newMethodData['price']}'
                            , assigned_id = '{$newMethodData['assigned_id']}'
                            WHERE id = '{$value['id']}'
                            "
            );
        }
    }

    /**
     * Удаляет методику у переданной пробы.
     *
     * @param int $probeId ID пробы (material_to_request_id)
     * @param int $methodId ID методики (new_method_id)
     */
    public function deleteMethodsFromProbe($probeId, $methodId)
    {
        $probeId = (int)$probeId;
        $methodId = (int)$methodId;

        // Получаем все записи с этой пробой и методикой
        $sql = $this->DB->Query(
            "SELECT id FROM ulab_gost_to_probe WHERE material_to_request_id = {$probeId} AND new_method_id = {$methodId}"
        );

        $idsToDelete = [];

        while ($row = $sql->Fetch()) {
            // Проверяем, есть ли результаты испытаний
            $trial = $this->DB->Query(
                "SELECT utr.actual_value, ugtp.measuring_sheet
                 FROM ulab_gost_to_probe ugtp
                 LEFT JOIN ulab_trial_results utr ON utr.gost_to_probe_id = ugtp.id
                 WHERE ugtp.id = {$row['id']}"
            )->Fetch();

            $hasResults = false;
            if (!empty($trial)) {
                $actualValue = json_decode($trial['actual_value'], true);
                $sheet = json_decode($trial['measuring_sheet'], true);
                if (!empty($trial['actual_value']) || (!empty($trial['measuring_sheet']) && $trial['measuring_sheet'] != 0) || !empty($actualValue) || !empty($sheet)) {
                    $hasResults = true;
                }
            }

            // Невозможно удалить методику: по ней уже внесены результаты испытаний
            if ($hasResults) {
                return;
            }

            $idsToDelete[] = $row['id'];
        }

        // Нет методик для удаления или все имеют результаты испытаний
        if (empty($idsToDelete)) {
            return;
        }

        // Удаляем найденные методики
        $idsStr = implode(',', $idsToDelete);
        $this->DB->Query("DELETE FROM ulab_gost_to_probe WHERE id IN ({$idsStr})");

        // Обновляем цену у заявки
        $umtr = $this->DB->Query("SELECT deal_id FROM ulab_material_to_request WHERE id = {$probeId}")->Fetch();

        if (!empty($umtr['deal_id'])) {
            $this->updatePrice($umtr['deal_id']);
        }
    }

    /**
     * Обновляет поле sent_out_lims, сигнализирующее о том, что подтверждение было отправлено вне ЛИМС
     *
     * @param int $dealId
     * @param array $tzData
     * @return void
     */
    public function updateSentOutLims(int $dealId, array $tzData)
    {
        $sentOutLims = isset($tzData['sent_out_lims']) ? 1 : 0;

        // Обновляем поле sent_out_lims в таблице ba_tz для соответствующего ID_Z
        $sql = "UPDATE `ba_tz` SET `sent_out_lims` = {$sentOutLims} WHERE `ID_Z` = {$dealId}";
        $this->DB->Query($sql);
    }

    public function deleteMassiveProbes($data)
    {
        $dealId = $this->DB->Query("SELECT deal_id FROM ulab_material_to_request WHERE id = '{$data[0]}'")->Fetch()['deal_id'];
        $currentDeal = $this->DB->Query("SELECT * FROM ba_tz WHERE ID_Z = '{$dealId}'")->Fetch();

        $price = 0;

        foreach ($data as $key => $value) {
            $currentUmtr = $this->DB->Query("SELECT * FROM ulab_material_to_request WHERE id = '{$value}'")->Fetch();

            $currentUmtrPrice = $this->DB->Query(
                "
                SELECT SUM(price) as 'price'
                FROM ulab_gost_to_probe
                WHERE material_to_request_id = '{$value}'  
                "
            )->Fetch()['price'];
            $price += $currentUmtrPrice;

            $this->DB->Query("DELETE FROM ulab_gost_to_probe WHERE material_to_request_id = '{$currentUmtr['id']}'");

            $this->DB->Query("DELETE FROM ulab_material_to_request WHERE id = '{$currentUmtr['id']}'");
        }

        $newDealPrice = $currentDeal['PRICE'] - $price;

        $this->DB->Query("UPDATE ba_tz SET PRICE = {$newDealPrice} WHERE ID_Z = '{$dealId}'");

        $sortedUmtr = $this->DB->Query("SELECT * FROM ulab_material_to_request WHERE deal_id = '{$dealId}' ORDER BY material_number, probe_number");

        $currentIndex = 0;

        while ($row = $sortedUmtr->Fetch()) {
            $this->DB->Query("UPDATE ulab_material_to_request SET probe_number = '{$currentIndex}' WHERE id = '{$row['id']}'");

            $currentIndex++;
        }
    }

    public function deleteCanceledLab($tzId, $leaderId)
    {
        $this->DB->Query("DELETE FROM CHECK_TZ WHERE tz_id = '{$tzId}' AND leader = '{$leaderId}'");
    }

    public function checkSendedDogovor($dogovorId)
    {
        $response = $this->DB->Query("SELECT id FROM dogovor_documents WHERE dogovor_id = '{$dogovorId}' AND type_id = 3 AND is_scan = 0")->Fetch()['id'];

        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    public function importProbesFromExcel($data)
    {
        $reader = new Xlsx();

        $uploadDir = "/home/bitrix/www/ulab/upload/excelprobes/" . basename($_FILES['file']['name']);

        move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir);

        $file = $uploadDir;

        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->setActiveSheetIndex(0);

        $infoByTableReviews = $worksheet->rangeToArray("{$data['from']}:{$data['to']}", null, true, false, false);

        $dataArray = [];

        foreach ($infoByTableReviews as $method) {
            $currentString = $data['formatCipher'];

            for ($i = 1; $i <= 8; $i++) {
                if (stripos($currentString, "{{$i}}") || stripos($currentString, "{{$i}}") == 0) {
                    $currentString = str_replace("{{$i}}", $method[$i - 1], $currentString);
                }
            }

            $dataArray[] = $currentString;
        }


        foreach ($data['ugtpId'] as $key => $value) {
            $this->DB->Query("UPDATE ulab_material_to_request SET name_for_protocol = '{$dataArray[$key]}' WHERE id = '{$value}'");
        }

        unlink($uploadDir);
    }

    public function getTzInformationDataByDealId($dealId)
    {
        $response = $this->DB->Query(
            "
            SELECT tests_for, is_indicate_compliance, compliance, presented_as, protocol_given, add_info, req_inf
            , probes_to_ic, additional_services, location, tu_sto
            FROM ba_tz 
            WHERE ID_Z = '{$dealId}'  
            "
        )->Fetch();

        $addInfoArray = json_decode($response['add_info'], JSON_UNESCAPED_UNICODE);
        $addInfoResultArray = [];

        if (in_array('passport', $addInfoArray)) {
            $addInfoResultArray[] = 'особые сведения о физических, химических характеристиках продукции (состав, направления формования и т.д.)';
        }

        if (in_array('tu', $addInfoArray)) {
            $addInfoResultArray[] = 'ТУ на производство продукции';
        }

        if (in_array('consist', $addInfoArray)) {
            $addInfoResultArray[] = 'документ о качестве продукции';
        }

        if ($response['req_inf']) {
            $addInfoResultArray[] = 'иное: ' . $response['req_inf'];
        }

        $addInfoResult = implode(', ', $addInfoResultArray);

        $testsFor = '';
        if ($response['tests_for'] == 'own' || empty($response['tests_for'])) {
            $testsFor = 'для собственных нужд';
        } else if ($response['tests_for'] == 'certification') {
            $testsFor = 'для сертификации';
        } else if ($response['tests_for'] == 'declaration') {
            $testsFor = 'для декларации';
        } else if ($response['tests_for'] == 'forensic') {
            $testsFor = 'для проведения судебной экспертизы';
        } else {
            $testsFor = '-';
        }

        $norm = 'нет';
        if (strval($response['is_indicate_compliance']) == '0') {
            $norm = 'нет';
        } else if ($response['is_indicate_compliance'] == 1) {
            $norm = 'ГОСТ ТУ: ' . ($response['compliance'] ?? '-');
        } else if ($response['is_indicate_compliance'] == 2) {
            $norm = 'ТУ СТО: ' . ($response['tu_sto'] ?? '-');
        }
        $presented_as = '';
        if ($response['presented_as'] == 'protocol' || empty($response['presented_as'])) {
            $presented_as = 'ПИ с ЭЦП';
        } elseif ($response['presented_as'] == 'no') {
            $presented_as = 'Без выдачи протокола';
        } else {
            $presented_as = 'ПИ на бумажном носителе';
        }
        $result =
            [
                'tests_for' => $testsFor,
                'is_indicate_compliance' => $norm,
                'presented_as' => $presented_as,
                'protocol_given' => $response['protocol_given'] == 'fact' || empty($response['protocol_given']) ? 'по факту завершения испытаний в лаборатории (поэтапно)' : 'по окончанию всего комплекса испытаний (КПИ)',
                'add_info' => $addInfoResult,
                'probes_to_ic' => $response['probes_to_ic'] ? $response['probes_to_ic'] : '-',
                'additional_services' => $response['additional_services'] ? $response['additional_services'] : '-',
                'location' => $response['location'] ? $response['location'] : '-',
            ];

        return $result;
    }

    public function setDopInfo($data)
    {
        $ugtp_id = $data['ugtp_id'];

        $fields = [
            'manufacturer' => $data['manufacturer'],
            'packing_name' => $data['packing_name'],
            'packaging_integrity' => $data['packaging_integrity'],
            'batch_size' => $data['batch_size'],
            'nd' => $data['nd'],
            'storage_conditions' => $data['storage_conditions'],
            'delivery_method' => $data['delivery_method'],
            'manufacture_date' => $data['manufacturing_date'],
            'expiration_date' => $data['expiration_date'],
            'sampling_place' => $data['sampling_place'],
            'sampling_by' => $data['sampling_by']
        ];

        $where = 'where ugtp_id = ' . $ugtp_id;

        $sqlData = $this->prepearTableData('dop_info_requirement', $fields);
        $this->DB->Update('dop_info_requirement', $sqlData, $where);
    }

    public function getNDList($id_tz)
    {
        $result = [];
        $ndQuery = $this->DB->Query("select nd from dop_info_requirement where id_tz = {$id_tz}")->Fetch();
        return explode(';', $ndQuery['nd']);
    }

    public function getDopInfoByUgtpId($ugtp_id)
    {
        return $this->DB->Query("select * from dop_info_requirement where ugtp_id = {$ugtp_id}")->Fetch();
    }
}
