<?php

class RecipeController extends Controller
{
    private string $nameModel = 'Recipe';

    public function list($param = '')
    {
        $this->data['title'] = 'Журнал рецептов';

        /** @var Request $request */
        $request = $this->model('Request');

        /** @var  Recipe $usedModel */
        $usedModel = $this->model($this->nameModel);

        $this->data['date_start'] = $request->getDateStart();
        $this->data['param'] = urldecode($param);
        $this->data['reactive'] = $usedModel->getFromSQL('reactive');
        $this->data['doc'] = $usedModel->getFromSQL('doc');
        $this->data['recipe'] = $usedModel->getFromSQL('recipe');

        $this->addCSS("/assets/plugins/select2/css/select2.min.css");
        $this->addJs("/assets/plugins/select2/js/select2.min.js");

        $this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
        $this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');

        $this->addCSS("/assets/plugins/DataTables/datatables.min.css");
        $this->addCSS("/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css");
        $this->addCSS("/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css");

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

        $this->addJS("/assets/plugins/DataTables/Responsive-2.4.0/dataTables.responsive.js");
        $this->addCSS("/assets/plugins/DataTables/Responsive-2.4.0/responsive.dataTables.css");
        
        $this->addJs("/assets/js/recipe-journal2.js");

        $this->view('list2');
    }

    public function getListProcessingAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Recipe $usedModel */
        $usedModel = $this->model($this->nameModel);

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

        if (!empty($_POST['param'])) {
            $filter['search']['name_recipe'] = $_POST['param'];
        }

        $data = $usedModel->getList($filter);

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

    public function addModelRecipe()
    {
        $successMsg = 'Рецепт успешно сохранен';
        $unsuccessfulMsg = 'Не удалось сохранить рецепт';

        $usedModel = $this->model('Recipe');

        $nameRecipe = $_POST['name_recipe'];
        $quantitySolution = $_POST['quantity_solution'];
        $concentration = $_POST['concentration'];
        $storageLifeInDay = $_POST['storage_life_in_day'];
        $idDoc = $_POST['id_doc'];
        $reactives = $_POST['reactives'];
        $solvent = $_POST['solvent'];

        $newRecipe = [
            'name_recipe' => $nameRecipe,
            'concentration' => $concentration,
            'id_doc' => $idDoc,
            'quantity_solution' => $quantitySolution,
            'storage_life_in_day' => $storageLifeInDay,
            'reactives' => $reactives,
            'solvent' => $solvent
        ];

        $recipeIsAdd = $usedModel->addModelRecipe($newRecipe);

        if (!$recipeIsAdd) {
            $this->showErrorMessage($unsuccessfulMsg);
            $this->redirect($usedModel->location);
        }

        $this->showSuccessMessage($successMsg);
        $this->redirect($usedModel->location);
    }

    public function addSolutionAsReactive()
    {
        $successMsg = 'Реактив успешно сохранен';
        $unsuccessfulMsg = 'Не удалось сохранить реактив';
        $usedModel = $this->model('Recipe');

        $reactive = $_POST['reactive'];
        $solutionAsReactive[] = $_POST['recipe'];
        $newReactive = [
            'reactive' => $reactive,
            'solutionAsReactive' => $solutionAsReactive
        ];
        $recipeIsAdd = $usedModel->addSolutionAsReactive($newReactive);

        if (!$recipeIsAdd) {
            $this->showErrorMessage($unsuccessfulMsg);
            $this->redirect($usedModel->location);
        }

        $this->showSuccessMessage($successMsg);
        $this->redirect($usedModel->location);
    }
}
