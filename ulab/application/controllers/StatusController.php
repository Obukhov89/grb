<?php
/**
 * Контроллер для статусов заявки
 * Class StatusController
 */
class StatusController extends Controller
{
    public function index()
    {
        $this->redirect('/request/new/');
    }

    public function updateTzStatusAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Request $status */
        $status = $this->model('Status');

        $data = [
            'user_id' => $_POST['user_id'],
            'status_id' => $_POST['status_id'],
            'date' => date('Y-m-d H:i:s')
        ];

        $status->setNewTzStatus($_POST['deal_id'], $data);
    }

    public function updateTzStatusAjaxWithTrials()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Request $status */
        $status = $this->model('Status');

        $dealStatuses = $status->getStatusTrials($_POST['deal_id']);

        if(!in_array('start', $dealStatuses) && !in_array('pause', $dealStatuses) && !in_array('', $dealStatuses)) {
            $data = [
                'user_id' => $_POST['user_id'],
                'status_id' => 18,
                'date' => date('Y-m-d H:i:s')
            ];

            $status->setNewTzStatus($_POST['deal_id'], $data);
        }
    }

    public function getStatusForDealAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Status $status */
        $status = $this->model('Status');

        $result = $status->getStatusesForDeal($_POST['deal_id']);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function test()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Status $status */
        $status = $this->model('Status');

        $status->getStatusesForDeal(12813);
    }
}