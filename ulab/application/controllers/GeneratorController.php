<?php

/**
 * Контроллер для заявок
 * Class GeneratorController
 */
class GeneratorController extends Controller
{
    /**
     * @param $dealID
     */
	public function	CommercialOffer($dealID)
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var DocumentGenerator $generator */
		$generator = $this->model('DocumentGenerator');

		$generator->commercialOffer($dealID);

		$this->showSuccessMessage("Коммерческое предложение сформировано");
	}


    /**
     * @param $dealID
     */
    public function TechnicalSpecification($dealID)
    {
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

        /** @var DocumentGenerator $generator */
        $generator = $this->model('DocumentGenerator');

        $generator->technicalSpecification($dealID);

        $this->showSuccessMessage("Техническое задание сформировано");
    }


    /**
     * @param $protocolID
     */
    public function ProtocolDocument($protocolID)
    {
        global $APPLICATION;

		$APPLICATION->RestartBuffer();

        /** @var DocumentGenerator $generator */
        $generator = $this->model('DocumentGenerator');

        $generator->protocolGenerator($protocolID);

        $this->showSuccessMessage("Протокол сформирован");
    }

	/**
	 * @param $protocolID
	 */
	public function actSampleDocument($dealID)
	{
	    global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var DocumentGenerator $generator */
		$generator = $this->model('DocumentGenerator');
        /** @var Request $request */
        $request = $this->model('Request');
        /** @var Status $status */
        $status = $this->model('Status');


		if ($dealID < DEAL_NEW_TZ && $dealID != 9735) {
			$generator->actSampleGenerator($dealID);
		} else {
			$generator->actSampleGeneratorNew($dealID);
		}

        $requestData = $request->getTzByDealId($dealID);

        $this->data['payment']['surcharge'] = (float)$requestData['price_discount'] - (float)$requestData['OPLATA'];

		$this->showSuccessMessage("Акт приема проб сформирован");
	}

	/**
	 * @param $actId
	 */
	public function actSampleDocumentNew($actId)
	{
	    global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var DocumentGenerator $generator */
		$generator = $this->model('DocumentGenerator');
        /** @var Request $request */
        $request = $this->model('Request');
        /** @var Status $status */
        $status = $this->model('Status');
        /** @var Requirement $requirement */
        $requirement = $this->model('Requirement');

        $dealID = $requirement->getDealIdByActId($actId);

		$generator->actSampleGeneratorNewWithAct($actId, $dealID);

        $requestData = $request->getTzByDealId($dealID);

        $this->data['payment']['surcharge'] = (float)$requestData['price_discount'] - (float)$requestData['OPLATA'];

		$this->showSuccessMessage("Акт приема проб сформирован");
	}

	public function invoiceOfferDocument($dealID)
	{
	    global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var DocumentGenerator $generator */
		$generator = $this->model('DocumentGenerator');

        $generator->invoiceOfferGenerator($dealID);

		$this->showSuccessMessage("Счет-оферта сформирован");
	}

    public function invoiceDoc($dealId)
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var DocumentGenerator $generator */
        $generator = $this->model('DocumentGenerator');

        $generator->invoiceGenerator($dealId);

        $this->showSuccessMessage("Счет сформирован");
    }

    /**
     *
     */
	public function getContentDocx()
	{
		/** @var DocumentGenerator $generator */
		$generator = $this->model('DocumentGenerator');

		$generator->getContent();
	}

	public function getVerificationGraph()
	{
		/** @var DocumentGenerator $generator */
		$generator = $this->model('DocumentGenerator');

		$year = $_POST['year'];
		$type = (int)$_POST['type'];
		$oa = $_POST['in_oa'];
		$month = $_POST['month2'];

		$generator->VerificationGraph($year, $type, $oa, $month);
	}

	/**
	 *
	 */
	public function getInventoryList()
	{
		/** @var DocumentGenerator $generator */
		$generator = $this->model('DocumentGenerator');

		$inform = [
		'dateInv' => !empty($_POST['inputDateEnd']) ? $_POST['inputDateEnd'] : date('Y-m-d'),
		'dateInvStart' => !empty($_POST['invDateStart']) ? $_POST['invDateStart'] : date('Y-m-d'),
		'directive' => !empty($_POST['directive']) ? $_POST['directive'] : '',
		'directive_date' => !empty($_POST['directive_date']) ? $_POST['directive_date'] : date('Y-m-d'),
		];
		$oa = $_POST['in_oa'] ?: 0;

		$generator->InventoryList($inform, $oa);
	}

	/**
	 *
	 */
	public function getPrice()
	{
		/** @var Methods $methodsModel */
		$methodsModel = $this->model('Methods');

		/** @var DocumentGenerator $generator */
		$generator = $this->model('DocumentGenerator');

		$generator->getPriceList();
	}

	/**
	 *
	 */
	public function setPrice()
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		/** @var Methods $methodsModel */
		$methodsModel = $this->model('Methods');

		/** @var DocumentGenerator $generator */
		$generator = $this->model('DocumentGenerator');

		$file = $_FILES['importXLSX']['tmp_name'];

		$countResult = $methodsModel->setPriceList($file);

		echo json_encode($countResult, JSON_UNESCAPED_UNICODE);
	}

	public function getMesList()
	{
		/** @var Methods $methodsModel */
		$methodsModel = $this->model('Methods');

		$this->data['html'] = $methodsModel->setMesList();

		$r = rand();
		$this->addJs("/assets/js/testMeasuring.js?v={$r}");

		$this->view('testMeasuring');
	}

	public function actVR()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();
        $dealId = $_POST['deal_id'];
        $paydocs = $_POST['paydocs'];
        $actNumber = $_POST['actNumber'];
        $actDate = $_POST['actDate'];
        /** @var DocumentGenerator $generator */
        $generator = $this->model('DocumentGenerator');

        $generator->actVrGenerate((int)$dealId, $paydocs, $actNumber, $actDate);

        $this->showSuccessMessage("УПД сформировано");
    }
}
