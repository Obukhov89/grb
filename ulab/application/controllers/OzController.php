<?php

class OzController extends Controller
{
    public function getPlanList()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $ozModel = $this->model('Oz');

        $result = $ozModel->getListForJournal($_POST);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function getPlanListCount()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $ozModel = $this->model('Oz');

        $result = $ozModel->getCountForJournal($_POST);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}