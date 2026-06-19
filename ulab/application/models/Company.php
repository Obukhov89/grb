<?php

use Bitrix\Crm\EntityRequisite;
use Bitrix\Crm\EntityBankDetail;
use Bitrix\Main\Loader;
use Bitrix\Socialservices;

class Company extends Model
{
    /**
     * @return array
     */
    public function getList(): array
    {
        $result = [];

        try {
            if ( Loader::IncludeModule('crm') ) {
                $arOrder  = ['ID' => 'ASC'];
                $arFilter = ['CHECK_PERMISSIONS' => 'N'];
                $arSelect = [];
                $companies = CCrmCompany::GetList( $arOrder, $arFilter, $arSelect );
                while ( $row = $companies->fetch() ) {
                	$row['TITLE'] = StringHelper::removeSpace($row['TITLE']);
                    $result[] = $row;
                }
            }
        } catch (Exception $e) {
            // замалчивание...
        }

        return $result;
    }

    /**
     * @param $companyId
     * @return array|false
     */
    public function getById($companyId)
    {
        $result = [];

        try {
            if ( Loader::IncludeModule('crm') ) {
                $arOrder  = [];
                $arFilter = ["ID" => $companyId];
                $arSelect = [];
                $companies = CCrmCompany::GetList( $arOrder, $arFilter, $arSelect );
                while ( $row = $companies->fetch() ) {
                    $row['TITLE'] = StringHelper::removeSpace($row['TITLE']);
                    $result = $row;
                }
            }
        } catch (Exception $e) {
            // замалчивание...
            return $result;
        }

        return $result;
    }


    /**
     * @param $dealId
     * @return array|false|null
     */
    public function getRequisiteByDealId($dealId)
    {
        $requestModel = new Request();

        $request = $requestModel->getDealById($dealId);

        return $this->getRequisiteByCompanyId($request['COMPANY_ID']);
    }


    /**
     * получить реквизиты компании
     * @param $id
     * @return array|false|null
     */
    public function getRequisiteByCompanyId($id)
    {
        $requisite = new EntityRequisite();
        $bankObj = new EntityBankDetail();

        $reqArr = $requisite->getList(["filter" => ["ENTITY_ID" => $id]])->fetch();

        $params = [
            'filter' => [
                'ENTITY_ID' => $reqArr['ID'],
                'ENTITY_TYPE_ID' => CCrmOwnerType::Requisite]
        ];

        $bankReq = $bankObj->getList($params)->fetch();
        $address['address'] = EntityRequisite::getAddresses($reqArr['ID']);

        if ( empty($bankReq) ) {
            return array_merge($reqArr, $address);
        }

        return array_merge($bankReq, $reqArr, $address);
    }

    /**
     * @param $id - company id
     * @param $data - data fields
     * @return array
     */
    public function setRequisiteByCompanyId($id, $data)
    {
        $requisite = new EntityRequisite();
        $bankObj = new EntityBankDetail();

        $old = $requisite->getById($id);

        $requisite->deleteByEntity(CCrmOwnerType::Company, $id);

//        $this->pre($data);

        try {
            $result = $requisite->add($data);
        } catch (Exception $e) {
            $requisite->add($old);

            return [
                'success' => false,
                'error' => "Не обновились реквизиты: " . $e->getMessage()
            ];
        }

        if ( !method_exists($result, 'getId') ) {
            $errors = $result->getErrors();
            $errorMsg = implode('; ', $errors);

            $requisite->add($old);

            return [
                'success' => false,
                'error' => "Ошибка: {$errorMsg}"
            ];
        }

        if ( empty($result) || !$result->getId() ) {
            $requisite->add($old);

            $errors = $result->getErrors();

            $msgError = implode('; ', $errors);

            return [
                'success' => false,
                'error' => "Не обновились реквизиты. {$msgError}"
            ];
        }

        $data['ENTITY_ID'] = $result->getId();
        $data['ENTITY_TYPE_ID'] = CCrmOwnerType::Requisite;

        $result = $bankObj->add($data);

//        if ( !method_exists($result, 'getId') ) {
//            $errors = $result->getErrors();
//            $errorMsg = implode('; ', $errors);
//
//            return [
//                'success' => false,
//                'error' => "Ошибка: {$errorMsg}"
//            ];
//        }
//
//        if ( empty($result) || !$result->getId() ) {
//            return [
//                'success' => false,
//                'error' => "Не обновились банковские данные"
//            ];
//        }

        return [
            'success' => true
        ];
    }

    /**
     * @param $name
     * @return false|int
     */
    public function add($name)
    {
        $newComp = new CCrmCompany;

        $data = ['TITLE' => $name];

        return $newComp->Add($data);
    }


    public function getCompanyIdByInn($inn)
    {
        $company = $this->DB->Query("SELECT ENTITY_ID FROM b_crm_requisite WHERE RQ_INN LIKE '{$inn}'")->Fetch();

        return !empty($company['ENTITY_ID'])? $company['ENTITY_ID'] : false;
    }


    /**
     * @param $inn
     * @return array
     */
	public function getCompanyByInn($inn)
	{
		CModule::IncludeModule('socialservices');
		$client = new Socialservices\Properties\Client();

		$company = $client->getByInn((string)$inn);

		if ( $company === false ) {
		    return [];
        }

        if(!$company['NAME_SHORT'] && $company['TYPE_NAME'] == 'Индивидуальный предприниматель') {
            $company['NAME_SHORT'] = "ИП {$company['LAST_NAME']} {$company['NAME']} {$company['SECOND_NAME']}";
            $company['NAME'] = "Индивидуальный предприниматель {$company['LAST_NAME']} {$company['NAME']} {$company['SECOND_NAME']}";
        }

		$data = [
			'inn' => $company['INN'],
			'ogrn' => $company['OGRN'],
			'kpp' => $company['KPP'],
			'name' => $company['NAME'],
			'name_short' => $company['NAME_SHORT'],
			'position_name' => mb_convert_case($company['OFFICIALS'][0]['POSITION_NAME'], MB_CASE_TITLE, "UTF-8"),
			'official_name' => mb_convert_case($company['OFFICIALS'][0]['LAST_NAME'] . ' ' . $company['OFFICIALS'][0]['NAME'] . ' ' . $company['OFFICIALS'][0]['SECOND_NAME'], MB_CASE_TITLE, 'UTF-8'),
			'adress' => $company['ADDRESS_INDEX'] . ', ' . mb_strtolower(substr($company['ADDRESS_REGION_TYPE'], 0, 1)) . '. ' .
			mb_convert_case($company['ADDRESS_REGION_NAME'], MB_CASE_TITLE, "UTF-8") . ', ' .
			mb_strtolower($company['ADDRESS_STREET_TYPE']) . ' ' . mb_convert_case($company['ADDRESS_STREET_NAME'], MB_CASE_TITLE, "UTF-8") . ', ' .
			mb_strtolower($company['ADDRESS_HOUSE']) . ' ' . $company['ADDRESS_BUILDING']
		];

		return $data;
	}


    /**
     * @param int $id
     * @return array
     */
    public function getCompanyDataByCompanyId(?int $id): array
    {
        $response = [];

        if (empty($id) || $id < 0) {
            return $response;
        }

        $company = CCrmCompany::GetByID($id);

        if (!empty($company)) {
            $company['TITLE'] = !empty($company['TITLE']) ?
                htmlentities($company['TITLE'], ENT_QUOTES, 'UTF-8') : '';

            $response = $company;
        }

        return $response;
    }

    /**
     * @param $fields
     * @return int|false
     */
    public function addCompany($fields)
    {
        $company = new CCrmCompany(false);
        return $company->Add($fields);
    }

    public function getByInnFromBx($inn)
    {
        $result = $this->DB->Query("
            SELECT ENTITY_ID, NAME FROM b_crm_requisite WHERE RQ_INN = {$inn} AND ENTITY_TYPE_ID = 4
        ");

        return $result->fetch();
    }

    public function getListByTitle($title = "")
    {
        $where = "";

        if ($title) {
            $where = "WHERE TITLE LIKE '%{$title}%'";
        }

        $sql = "SELECT ID AS id, TITLE as title
                FROM b_crm_company 
                {$where}";

        $stmt = $this->DB->Query($sql);

        $result = [];

        while ($row = $stmt->Fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * получить информацию о текущей компании(лаборатории)
     * @return array
     */
    public function getCompanyInfo($withReplacement = true): array
    {
        $result = [];

        $companyInfo = $this->DB->Query(
            "select *
                from ulab_company_info WHERE id = 1"
        )->Fetch();

        if (!empty($companyInfo)) {
            $companyInfo['add_email'] = json_decode($companyInfo['add_email'], true);

            $result = $companyInfo;
        }

        require_once __DIR__ . '/User.php';
        $userModel = new User();
        if ($withReplacement) {
            if (!empty($companyInfo['director_id'])) {
                if ($userModel->checkUserHasRepalcement($companyInfo['director_id'])) {
                    $replacementUser = $userModel->getReplacementByUserId($companyInfo['director_id']);

                    $result['director'] = $replacementUser['FULL_NAME'];
                    $result['position'] = $replacementUser['WORK_POSITION'];
                    $result['position_genitive'] = $replacementUser['WORK_POSITION_GENITIVE'];
                    $result['director_short'] = $replacementUser['SHORT_NAME'];
                }
            }

            if (!empty($companyInfo['accountant_id'])) {
                if ($userModel->checkUserHasRepalcement($companyInfo['accountant_id'])) {
                    $replacementUser = $userModel->getReplacementByUserId($companyInfo['accountant_id']);

                    $result['accountant_position'] = $replacementUser['WORK_POSITION'];
                    $result['accountant'] = $replacementUser['SHORT_NAME'];
                }
            }
        }


        return $result;
    }

    /**
     * @param $id
     * @param $data
     * @return false|mixed|string
     */
    public function updateCompanyInfo($id, $data)
    {
        $sqlData = $this->prepareCompanyInfo($data);

        $where = "WHERE id = {$id}";
        return $this->DB->Update('ulab_company_info', $sqlData, $where);
    }


    /**
     * @param $data
     * @return array
     */
    public function prepareCompanyInfo($data)
    {
        $columns = $this->getColumnsByTable('ulab_company_info');

        $sqlData = [];

        foreach ($columns as $column) {
            if ( isset($data[$column]) ) {
                if ($column == 'director_id' || $column == 'accountant_id' || $column == 'add_email')
                    $sqlData[$column] = $data[$column];
                else
                    $sqlData[$column] = $this->quoteEscapedStr($this->DB->ForSql(trim($data[$column])));
            }
        }

        $addEmail = json_encode($data['add_email'], JSON_UNESCAPED_UNICODE);

        $sqlData['ip'] = !empty($data['ip']) ? 1 : 0;

        if ($sqlData['add_email'])
            $sqlData['add_email'] = "'{$addEmail}'";

        if ($sqlData['director_id'])
            $sqlData['director_id'] = intval($sqlData['director_id']);

        if ($sqlData['accountant_id'])
            $sqlData['accountant_id'] = intval($sqlData['accountant_id']);

        return $sqlData;
    }

    /**
     * @param $data
     * @return false|mixed|string
     */
    public function addCompanyInfo($data)
    {
        $sqlData = $this->prepareCompanyInfo($data);

        return $this->DB->Insert('ulab_company_info', $sqlData);
    }

    protected function quoteEscapedStr(string $str = ""): string
    {
        $escapedStr = htmlspecialcharsbx(trim($str));
        return "'{$escapedStr}'";
    }
}
