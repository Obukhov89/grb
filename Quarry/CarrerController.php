<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/Controller/Controller.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DataStorage.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/Quarry/models/Quarry.php');
CModule::IncludeModule('crm');

use Quarry\models\Quarry;
use Controller\Controller;

$params = $_REQUEST;

class CarrerController extends Controller{
    private $params;

    public function __construct($params)
    {

        $this->params = $params;
        parent::__construct();
    }

    public function index()
    {
        header("Location: http://172.16.255.3/Quarry/view/career_log.php");
        exit;
    }

    public function getData()
    {

        $newCDB = new CDB();
        $newQuarry = new Quarry($newCDB);
        $cDeal = new CCrmDeal;

        $newResult = new NewResult();

        $jsonData = $newQuarry->selectDataTable($this->params, $cDeal, $newResult);

        echo json_encode($jsonData);
    }
}

new CarrerController($params);

