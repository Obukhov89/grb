<?php


/**
 * Class TestController
 */
class TestController extends Controller
{
	public function index()
	{
		/** @var Request $request */
		$request = $this->model('Request');
		/** @var Material $material */
		$material = $this->model('Material');
		/** @var Requirement $requirement */
		$requirement = $this->model('Requirement');
		/** @var Gost $gost */
		$gost = $this->model('Gost');
		/** @var User $user */
		$user = $this->model('User');
		/** @var Order $orderModel */
        $orderModel = $this->model('Order');

        global $DB;

		$dealID = 11268;


//        foreach ($dealIDarr as $dealID) {

            $dealInformation = $request->getTzByDealId($dealID);

            $TZ_ID = $dealInformation['ID'];

            $date = date('Y-m-d H:i:s');

            $dateTZ = date('d-m-Y_H-i-s', strtotime($date));
            $curdateEng = "SO_" . $TZ_ID . "_" . $dateTZ;

            $request->pre($dealID, false);
            $res_deals = $orderModel->getContractDealByDealId($dealID);


            $id = $res_deals['ID_CONTRACT'];
            $res_dogovor = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`= {$id}")->Fetch();


            $curYear = date('y');
            $number = $res_dogovor['NUMBER'];
            $numberAcc = explode('/', $res_dogovor['NUMBER'])[0];
            $date = $res_dogovor['DATE'];
            $curdate = "SO_{$number}_{$dateTZ}";
            $data = [
                'DATE' => "'{$date}'",
                'TZ_ID' => $TZ_ID,
                'ACTUAL_VER' => "'{$curdateEng}'",
                'CONTRACT_TYPE' => 'Счет-оферта',
                'NUMBER' => "'{$number}'",
                'DEAL_ID' => $dealID,
            ];
            if (empty($DB->Query("select * from INVOICE where TZ_ID = {$TZ_ID}")->fetch())) {

                $dataInv = [
                    'DATE' => "'{$date}'",
                    'TZ_ID' => $TZ_ID,
                    'ACTUAL_VER' => "'{$curdateEng}'",
                    'NUMBER' => "'{$numberAcc}'",
                ];
                $DB->Insert('INVOICE', $dataInv);

                $str_type = 'Формирование Счета-Оферты';
                $invoice = "Счет-оферта №{$number} от " . StringHelper::dateRu($date);

                $ba_tz = [
                    'DOGOVOR_NUM' => $id,
                    'DOGOVOR_TABLE' => "'{$invoice}'",
                    'ACCOUNT' => $numberAcc,
                ];
            }

            $DB->Update('ba_tz', $ba_tz, "where ID_Z = {$dealID}");
//        }

		$this->view('index');
	}

	public function test2()
	{
		/** @var Request $request */
		$request = $this->model('Request');
		/** @var Material $material */
		$material = $this->model('Material');
		/** @var Requirement $requirement */
		$requirement = $this->model('Requirement');
		/** @var Gost $gost */
		$gost = $this->model('Gost');
		/** @var User $user */
		$user = $this->model('User');
		/** @var Order $orderModel */
		$orderModel = $this->model('Order');

		global $DB;

		$dealID = 11640;
		$actNum = '1309';
		$cipherStart = '100';

		$res = $DB->Query("select * from ulab_material_to_request where deal_id = {$dealID}");

		while ($umtr = $res->fetch()) {
			$cipher = $actNum . '.' . $cipherStart . "/23";
			$val['cipher'] = "'{$cipher}'";
			$val['in_act'] = 1;



//			$umtrSqlData = $orderModel->prepearTableData('ulab_material_to_request', $val);

//			$orderModel->pre($val);
//			$DB->Update('ulab_material_to_request', $val, "where id = {$umtr['id']}");

			$cipherStart++;
		}

//		foreach ($data['probe'] as $umtr_id => $val) {
//
//			$val['cipher'] = $data['ACT_NUM'] . '.' . $i . "/24";
//			$val['in_act'] = 1;
//
//			$this->setLabHeaderByUmtrId($umtr_id);
//
//			$umtrSqlData = $this->prepearTableData('ulab_material_to_request', $val);
//			$this->DB->Update('ulab_material_to_request', $umtrSqlData, "where id = {$umtr_id}");
//			$i++;
//
//			$this->addHistory($umtr_id, "Пробу принял");
//		}

		$this->view('index');
	}


}
