<?php

/**
 * Класс контроллер для Зернового состава
 * Class GrainController
 */
class GrainController extends Controller
{
    public function card(int $grainListID)
    {
        /** @var Grain $grain*/
        $grain = $this->model('Grain');

        $this->data['title'] = "Зерновой состав";

        $this->data['grain_list_id'] = $grainListID;
        $this->data['grain_list_gost'] = $grain->getGrainGostList($grainListID);
        $this->data['grain_seave_size'] = $grain->getGrainSeaveSize();
        $this->data['grain'] = $grain->getGrainSeaveValues($grainListID);

        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");

        $this->addJs('/assets/plugins/select2/dist/js/select2.js');

        $this->addJs('/assets/js/grain.js');
        $this->view('card');
    }

    public function updateGrainList(int $grainListID)
    {
        /** @var Grain $grain*/
        $grain = $this->model('Grain');

        $grain->update($grainListID, $_POST);

        $this->redirect('/grain/card/' . $grainListID);
    }

    public function list()
    {
        $this->data['title'] = 'Журнал зерновых составов';

        /** @var Request $request */
        $request = $this->model('Request');
        /** @var Grain $grain */
        $grain = $this->model('Grain');

        $this->data['date_start'] = $request->getDateStart();

        $this->addCSS("/assets/plugins/select2/dist/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/dist/css/select2-bootstrap-5-theme.min.css");

        $this->addJs('/assets/plugins/select2/dist/js/select2.min.js');

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

        $this->addJs("/assets/js/grain-list.js");

        $this->view('list');
    }

    public function getGostAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Grain $grain */
        $grain = $this->model('Grain');

        $result = [];

        $result['gosts'] = $grain->getGostsToSelect();
        $result['groups'] = $grain->getMaterialGroups();

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function addZern()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Grain $grain */
        $grain = $this->model('Grain');

        $data =
            [
                'GOST_ID' => $_POST['gost'],
                'TYPE_MAT_ID' => $_POST['group'],
                'NAME' => $_POST['name']
            ];

        $newId = $grain->createNewZern($data);

        $this->redirect("/ulab/grain/card/{$newId}");
    }

    public function getListProcessingAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        /** @var Grain $grain */
        $grain = $this->model('Grain');

        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],
                'start'      => $_POST['start'],
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if ( !empty($column['search']['value']) ) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        $data = $grain->getDataToJournalGrain($filter);

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

    public function convertOldGrainToNew()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $grain = $this->model('Grain');

        $grain->convertOldToNewFormat();
    }

    public function addSieve()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        $grain = $this->model('Grain');

        $lower = 0;
        if(!empty($_POST['lower']) && $_POST['lower'] == 'on') {
            $lower = 1;
        }

        $data =
            [
                'size' => $_POST['size'],
                'lower' => $lower,
                'meas' => $_POST['meas']
            ];

        $grain->createSieve($data);

        $this->redirect('/ulab/grain/card/' . $_POST['zern_id']);
    }
}