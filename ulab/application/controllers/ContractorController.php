<?php

/**
 *
 * Class ContractorController
 */
class ContractorController extends Controller
{
    const TG_TOKEN = "5839740335:AAEb7rbKoNqdVzpCba1u_NV2_ESOCACmaLk";

    public function journal()
    {
        $this->data['title'] = 'Журнал заявок подрядчиков';

        $contractor = $this->model('Contractor');
        $this->data["result_stages"] = $contractor->getContractorResult();
        $this->data["user_list"] = $contractor->getTgUserList();

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");
        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");

        $this->addJS("/assets/plugins/DataTables/DataTables-1.11.3/js/jquery.dataTables.min.js");
        $this->addJS("/assets/plugins/DataTables/ColReorder-1.5.5/js/dataTables.colReorder.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/dataTables.buttons.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.colVis.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.print.min.js");
        $this->addJS("/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.html5.min.js");
        $this->addJS("/assets/plugins/DataTables/JSZip-2.5.0/jszip.min.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/ellipsis.js");
        $this->addJS("/assets/plugins/DataTables/dataRender/intl.js");
        $this->addJS("/assets/plugins/DataTables/FixedHeader-3.2.0/js/dataTables.fixedHeader.min.js");
        $this->addJS('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');
        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");

        $this->addCSS("/assets/css/contractor.css?v=" . rand());

        $this->addJs("/assets/js/contractor-journal.js?v=" . rand());

        $this->view('journal');
    }

    public function getJournalAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Contractor $contractor */
        $contractor = $this->model('Contractor');

        $filter = [
            'paginate' => [
                'length' => $_POST['length'],  // кол-во строк на страницу
                'start' => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if (!empty($column['search']['value'])) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if (isset($_POST['order']) && !empty($_POST['columns'])) {
            $filter['order']['by'] = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        if (!empty($_POST['date_start'])) {
            $filter['search']['date_start'] = $_POST['date_start'];
        }

        if (!empty($_POST['date_end'])) {
            $filter['search']['date_end'] = $_POST['date_end'];
        }

        $userId = $_SESSION['SESS_AUTH']['USER_ID'];

        $data = $contractor->getJournal($filter);

        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];

        unset($data['recordsTotal']);
        unset($data['recordsFiltered']);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "result_stages" => $contractor->getContractorResult(),
            "data" => $data,
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    public function updateRowAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $contractor = $this->model('Contractor');

        $id = $_POST["row_id"];

        $data = [
            "act" => $_POST["act"],
            "aok" => $_POST["aok"],
            "assigned_completed" => $_POST["assigned_completed"],
            "comment" => $_POST["comment"],
            "result" => $_POST["result"],
            "job_desc" => $_POST["job_desc"],
            "weather" => $_POST["weather"],

            "tg_id" => $_POST["tg_id"],
            "area_number" => $_POST["area_number"],
            "datetime" => $_POST["datetime"],
            "work_place" => $_POST["work_place"],
            "content" => $_POST["content"],
            "work_object" => $_POST["work_object"],
            "constructive" => $_POST["constructive"],
            "checklist" => $_POST["checklist"],
        ];

        if ($id) {
            $contractor->updateRow($data, $id);
        } else {
            $data["created_at"] = date('Y-m-d H:i:s');
            $data["monthly_order_number"] = $contractor->getCountMonth() + 1;

            $contractor->insertRow($data);
        }

        //echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function updateStatusAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $contractor = $this->model('Contractor');
        $telegram = $this->model('Telegram');

        $id = $_POST["row_id"];

        $data = [
            "status" => $_POST["status"]
        ];

        $contractor->updateRow($data, $id);

        $contractorData = $contractor->getById($id);

        $message = "<b>Статус заявки</b> №{$contractorData["order_number"]}: {$contractorData["content"]} изменен на <b>\"{$contractorData["status_name"]}\"</b>";

        $telegram->sendText($contractorData["tg_id"], $message, self::TG_TOKEN);

        echo json_encode($contractorData, JSON_UNESCAPED_UNICODE);
    }

    public function user($id)
    {
        $this->data['title'] = 'Карточка пользователя';

        $contractor = $this->model('Contractor');

        $this->data["card"] = $contractor->getUser($id);

        $this->view('user');
    }

    public function updateUser()
    {
        if (isset($_POST["id"])) {
            $contractor = $this->model('Contractor');

            $id = $_POST["id"];

            $data = [
              "fio" => $_POST["fio"],
              "phone" => $_POST["phone"],
              "company_name" => $_POST["company_name"],
            ];

            $contractor->updateUser($data, $id);

            $this->redirect("/ulab/contractor/user/{$id}");
        } else {
            $this->redirect("/ulab/contractor/journal/");
        }
    }

}