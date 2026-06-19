<?php

class SolutionController extends Controller
{

    public function list()
    {
        $this->data['title'] = 'Журнал регистрации растворов';

        /** @var Company $company */
        $company = $this->model('Company');
        /** @var User $user */
        $user = $this->model('User');
        /** @var Request $request */
        $request = $this->model('Request');

        /** @var  Solution $solution */
        $solution = $this->model('Solution');

        $this->data['date_start'] = $request->getDateStart();
        $this->data['model_recipe'] = $solution->getFromSQL('model_recipe');

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
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $this->addCSS("/assets/plugins/select2/css/select2.min.css");
        $this->addJs("/assets/plugins/select2/js/select2.min.js");

        $this->addJs("/assets/js/solution-journal.js");

        $this->view('list');
    }

    public function getListProcessingAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Solution $solution */
        $solution = $this->model('Solution');

        $filter = [
            'paginate' => [
                'length' => $_POST['length'],  // кол-во строк на страницу
                'start' => $_POST['start'],  // текущая страница
            ],
            'search' => [],
        ];

        foreach ($_POST['columns'] as $column) {
            if (!empty($column['search']['value'])) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if (!empty($_POST['everywhere'])) {
            $filter['search']['everywhere'] = $_POST['everywhere'];
        }
        $data = $solution->getList($filter);

        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];


        unset($data['recordsTotal']);
        unset($data['recordsFiltered']);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }
    public function addSolution()
    {
        global $USER;

        $location = "/solution/list/";
        $successMsg = 'Реактив успешно создан';
        $unsuccessfulMsg = 'Не удалось создать реактива';

        $curDate = date('Y-m-d');

        $solution = $this->model('Solution');

        /** @var Company $company */
        $company = $this->model('Company');
        /** @var Request $request */
        $request = $this->model('Request');
        
        $name = $_POST['name_solution'];
        $idModelRecipe = $_POST['id_model_recipe'];
        $datePreparation = $_POST['date_preparation'];
        $user = $USER->GetID();
        $newSolution = [
            'name' => $name,
            'id_model_recipe' => $idModelRecipe,
            'date_preparation' => $datePreparation,
            'id_people' =>  $user
        ];

        $solutionIsAdd = $solution->addSolution($newSolution);

        if (!$solutionIsAdd) {
            $this->showErrorMessage($unsuccessfulMsg);
            $this->redirect($location);
        }

        $this->showSuccessMessage($successMsg);
        $this->redirect($location);
    }
}
