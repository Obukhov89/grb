<?php

/**
 * Пробы
 * Class ProbeController
 */
class MaterialController extends Controller
{
    public function index()
    {
        $this->redirect('/request/new/');
    }


    /**
     * route /material/list/
     * список актов приёмки проб
     */
    public function list()
    {
        $this->data['title'] = 'Журнал материалов';

//        if ( !in_array($_SESSION['SESS_AUTH']['USER_ID'], [1, 61, 33]) ) {
//            $this->showErrorMessage("Нет доступа в журнал материалов");
//            $this->redirect('/request/new/');
//        }


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

        $r = rand();
        $this->addJs("/assets/js/material-list.js?v={$r}");

        $this->view('list');
    }


    public function add() {
        /** @var Material $materialModel */
        $materialModel = $this->model('Material');

        $validName = $this->validateField($_POST['name'], 'Название');
        if ( !$validName['success'] ) {
            $this->showErrorMessage($validName['error']);
            $this->redirect('/material/list/');
        }

        $materialModel->add($_POST['name']);

        $this->showSuccessMessage("Материал '{$_POST['name']}' успешно добавлен");
        $this->redirect('/material/list/');
    }


    /**
     *
     */
    public function addGroup()
    {
        /** @var Material $materialModel */
        $materialModel = $this->model('Material');
        if($_SESSION['SESS_AUTH']['USER_ID'] == 61 || $_SESSION['SESS_AUTH']['USER_ID'] == 10 || $_SESSION['SESS_AUTH']['USER_ID'] == 250 ||
            $_SESSION['SESS_AUTH']['USER_ID'] == 227 || $_SESSION['SESS_AUTH']['USER_ID'] == 239 || $_SESSION['SESS_AUTH']['USER_ID'] == 222 ||
            $_SESSION['SESS_AUTH']['USER_ID'] == 33 || $_SESSION['SESS_AUTH']['USER_ID'] == 212 ||  $_SESSION['SESS_AUTH']['USER_ID'] == 220 ||
            $_SESSION['SESS_AUTH']['USER_ID'] == 234 || $_SESSION['SESS_AUTH']['USER_ID'] == 83 ||  $_SESSION['SESS_AUTH']['USER_ID'] == 123) {
            $groupId = $materialModel->addGroupMaterial((int)$_POST['material_id'], $_POST['name']);
        } else {
            $this->showErrorMessage("Запрещено");
            $this->redirect("/material/card/{$_POST['material_id']}");
        }

        if ( empty($groupId) ) {
            $this->showErrorMessage("Не удалось добавить группу '{$_POST['name']}'");
        } else {
            $materialModel->updateTuGroupMaterial($groupId, $_POST['group']['new']['tu']);

            $this->showSuccessMessage("Группа '{$_POST['name']}' добавлена");
        }

        $this->redirect("/material/card/{$_POST['material_id']}");
    }


    /**
     *
     */
    public function updateGroups()
    {
        /** @var Material $materialModel */
        $materialModel = $this->model('Material');
        if($_SESSION['SESS_AUTH']['USER_ID'] == 61 || $_SESSION['SESS_AUTH']['USER_ID'] == 10 || $_SESSION['SESS_AUTH']['USER_ID'] == 250 ||
            $_SESSION['SESS_AUTH']['USER_ID'] == 227 || $_SESSION['SESS_AUTH']['USER_ID'] == 239 || $_SESSION['SESS_AUTH']['USER_ID'] == 222 ||
            $_SESSION['SESS_AUTH']['USER_ID'] == 33) {
            $materialModel->updateGroupAndTu((int)$_POST['material_id'], $_POST['group']);
        } else {
            $this->showErrorMessage("Запрещено");
            $this->redirect("/material/card/{$_POST['material_id']}");
        }

        $this->showSuccessMessage("Данные групп обновлены");

        $this->redirect("/material/card/{$_POST['material_id']}");
    }


    /**
     * @param $id
     */
	public function card( $id )
	{
		$matID = (int) $id;

        /** @var Material $materialModel */
        $materialModel = $this->model('Material');
        /** @var Scheme $schemeModel */
        /** @var Methods $methodsModel */
        $methodsModel = $this->model('Methods');
		/** @var TechCondition $tcModel */
		$tcModel = $this->model('TechCondition');

		$this->data['title'] = "Карточка материала";

        $this->data['id'] = $matID;

		$data = $materialModel->getById($matID);

        $this->data['groups'] = $materialModel->getGroupMaterial($matID);

        $this->data['gost_to_material'] = $materialModel->getGostToMaterialByMatID($matID);

		$this->data['method_list'] = $methodsModel->getList();
		$this->data['condition_list'] = $tcModel->getList();
		$this->data['scheme'] = $materialModel->getSchemeByMaterial($matID);
        $this->data['name'] = $data['NAME'];

        $this->data['gost'] = $methodsModel->getList();

		$this->addCSS("/assets/plugins/select2/css/select2.min.css");
		$this->addCSS("/assets/plugins/select2/css/select2-bootstrap-5-theme.min.css");

        $this->addJs('/assets/js/bootstrap.min.js');
        $this->addJs('/assets/js/bootstrap.bundle.min.js');
        $this->addJs('/assets/plugins/select2/js/select2.min.js');
        $this->addJs('/assets/js/material.js');

		$this->view('card');
	}


    /**
     *
     */
	public function viewSchemeMaterial()
    {
        /** @var Material $materialModel */
        $materialModel = $this->model('Material');
        /** @var Methods $methodsModel */
        $methodsModel = $this->model('Methods');

        $this->data['title'] = "Схемы материала";

        $this->data['material_list'] = $materialModel->getNewSchemeMaterial();

        $this->data['material_id'] = '';
        if ( !empty($_SESSION['saved_material_id']) ) {
            $this->data['material_id'] = $_SESSION['saved_material_id'];
            unset($_SESSION['saved_material_id']);
        }

        $this->data['gost_list'] = $methodsModel->getList();

        $this->addCSS("/assets/plugins/select2/css/select2.min.css");
        $this->addCSS("/assets/plugins/select2/css/select2-bootstrap-5-theme.min.css");

        $this->addJs('/assets/plugins/select2/js/select2.min.js');

        $this->addJs('/assets/js/material-new-scheme.js');

        $this->view('new_scheme');
    }


    /**
     *
     */
    public function addNewSchemeMethods()
    {
        /** @var Material $materialModel */
        $materialModel = $this->model('Material');

        $_SESSION['saved_material_id'] = $_POST['material_id'];
        $_SESSION['saved_scheme_id'] = $_POST['scheme_id'];

        $materialModel->addNewSchemeMethods($_POST['scheme_id'], $_POST['methods']);

        $this->showSuccessMessage('Добавлено');
        $this->redirect("/ulab/material/viewSchemeMaterial/");
    }


	/**
	 * @param $ID
	 */
	public function test( $ID )
	{
		$matID = (int) $ID;

		/** @var Request $request */
		$request = $this->model('Request');
		/** @var User $user */
		$user = $this->model('User');
		/** @var Requirement $requirement */
		$requirement = $this->model('Requirement');
		/** @var Probe $probeModel */
		$probeModel = $this->model('Probe');
		/** @var Material $lab */
		$material = $this->model('Material');
		/** @var Gost $gost */
		$gost = $this->model('Gost');

		$this->data['title'] = "Карточка материала";

		$this->data['id'] = $matID;
		$this->data['scheme'] = $material->getSchemeByMaterId($ID);
		$this->data['unit'] = $request->getUnit();

		$data = $material->getMaterial($matID);

		$gostToMaterial = $material->getGostToMaterialByMatID($matID);

		$gostArr = $gost->getListForVeiw();

		$matGroups = unserialize($data['GROUPS']);

		$this->data['name'] = $data['NAME'];
		$this->data['name_group'] = $data['GROUP_NAME'];
		$this->data['groups'] = $matGroups;
		$this->data['gost'] = $gostArr;
		$this->data['gost_to_material'] = $gostToMaterial;


		$this->data['test'] = $gostToMaterial;

		$this->addCSS("/assets/plugins/magnific-popup/magnific-popup.css");
		$this->addCSS("/assets/plugins/select2/css/select2.min.css");
		$this->addCSS("/assets/plugins/select2/css/select2-bootstrap-5-theme.min.css");

		$this->addJs('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');
		$this->addJs('/assets/js/bootstrap.min.js');
		$this->addJs('/assets/js/bootstrap.bundle.min.js');
		$this->addJs('/assets/plugins/select2/js/select2.min.js');
		$this->addJs('/assets/js/material.js');

		$this->view('test');
	}


    /**
     *
     */
	public function insertUpdate() {
        /** @var Material $material */
        $material = $this->model('Material');

        $_SESSION['material_post'] = $_POST;

		$ID = $_POST['id'];
		$successMsg = empty($_POST['id'])? 'Материал успешно создан' : "Материал успешно изменен";

        $dataGroup = [];
        $dataGost = [];
        $Groups = '';

        if (!empty($_POST['GROUP_VAL'])) {
            $arrGroup = array_diff($_POST['GROUP_VAL'], array('', NULL, false));
            $Groups = serialize($arrGroup);
        }
		$dataGost = $_POST['arrGost'];

        $dataGroup['GROUP_NAME'] = !empty($_POST['GROUP_NAME']) ? $_POST['GROUP_NAME'] : '';
        $dataGroup['GROUP_VAL'] = $Groups;

        if (!empty($_POST['NAME']) && empty($ID)) {
            $name = $_POST['NAME'];
            $ID = $material->setNewMaterial($name);
        }

        $location = "/material/card/{$ID}";

//        $material->setGroupMat($ID, $dataGroup);
        $material->setGostToMaterial($ID, $_POST['price']);

        $this->showSuccessMessage($successMsg);
        unset($_SESSION['request_post']);
        $this->redirect($location);
    }


	public function copyGroup()
	{
		/** @var Material $material */
		$material = $this->model('Material');

		$material->copyMethodGroupById($_POST['idOldScheme'],$_POST['idScheme']);

		$this->redirect("/material/card/{$_POST['material_id']}");
	}

    /**
     *
     */
    public function getListAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Material $material*/
        $material = $this->model('Material');

        $filter = [
            'paginate' => [
                'length'    => $_POST['length'],  // кол-во строк на страницу
                'start'      => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        foreach ($_POST['columns'] as $column) {
            if ( $column['search']['value'] !== '' ) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if ( isset($_POST['order']) && !empty($_POST['columns']) ) {
            $filter['order']['by']  = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        $data = $material->getDatatoJournalMaterial($filter);

        $recordsTotal = $data['recordsTotal'];
        $recordsFiltered = $data['recordsFiltered'];

        unset($data['recordsTotal']);
        unset($data['recordsFiltered']);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }


    public function deleteMaterialAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Material $material*/
        $material = $this->model('Material');

        $material->deleteMaterial((int)$_POST['id']);
    }


    public function updateMaterialAjax()
    {

		global $APPLICATION;

		$APPLICATION->RestartBuffer();
        /** @var  Material $material*/
        $material = $this->model('Material');

        $id = $_POST['id'];
        $name = $_POST['name'];

        $successMsg = 'Материал изменен';

        $material->updateMaterial($name, $id);

        echo $name;
    }

    public function getSchemeByMaterialId($materialId)
	{
		/** @var  Material $material*/
		$material = $this->model('Material');

		return $material->getSchemeByMaterId($materialId);
	}

	public function setSchemeAjax()
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();
		/** @var  Material $material*/
		$material = $this->model('Material');
		$id = $_POST['id_scheme'];
		$materialId = !empty($_POST['id_material']) ? $_POST['id_material'] : 0;

		$dataGost = $_POST['gosts'];

		$data = [
			'name' => $_POST['name'],
			'material_id' => $materialId,
		];

		if (!empty($id)) {
			$material->updateScheme($id, $data);
			$this->showSuccessMessage('Схема успешно обновлена');
		} else {
			$id = $material->addNewScheme($data);
			$this->showSuccessMessage('Схема успешно создана');
		}

		$material->addGostToScheme($id, $dataGost);

		echo '1';
	}

	public function deleteSchemeAjax()
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();
		/** @var  Material $material*/
		$material = $this->model('Material');

		$id = $_POST['id_scheme'];

		$material->deleteScheme($id);
		$this->showSuccessMessage('Схема успешно удалена');

		echo '1';
	}

	/**
	 * @deprecated
	 */
	public function getGroupMaterialAjax()
	{
		global $APPLICATION;

		$APPLICATION->RestartBuffer();
		/** @var  Material $material*/
		$material = $this->model('Material');

		$data = $material->getSieveAndNormNew($_POST['groupId']);

		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}


    /**
     *
     */
	public function deleteGroupAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Material $materialModel */
        $materialModel = $this->model('Material');

        $materialModel->deleteGroup((int)$_POST['group_id']);

        echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
    }


    /**
     *
     */
    public function getGroupByMaterialAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Material $materialModel */
        $materialModel = $this->model('Material');

        $data = $materialModel->getGroupMaterial((int)$_POST['material_id']);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     *
     */
    public function getGroupByMaterialByUmtrIdAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Material $materialModel */
        $materialModel = $this->model('Material');
		foreach ($_POST['probe_id_list'] as $umtrId) {
			$materialId = $materialModel->getMaterialByUmtrId($umtrId);

			$data[$materialId] = $materialModel->getGroupMaterial($materialId);
			$data[$materialId]['material_name'] = $materialModel->getById($materialId)['NAME'];
		}

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    /**
     *
     */
    public function getSchemeByMaterialAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Material $materialModel */
        $materialModel = $this->model('Material');

        $data = $materialModel->getSchemeByMaterial((int)$_POST['material_id']);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    /**
     *
     */
    public function getSchemeParamAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Material $materialModel */
        $materialModel = $this->model('Material');

        $data = $materialModel->getSchemeParam((int)$_POST['scheme_id']);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    /**
     * @desc Меняет статус
     */
    public function changeActiveMaterialAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var Material $materialModel */
        $materialModel = $this->model('Material');

        $materialModel->changeActiveMaterial((int) $_POST['id']);
    }


    /**
     *
     */
    public function getNewSchemeAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Material $materialModel */
        $materialModel = $this->model('Material');

        $data = $materialModel->getNewScheme((int)$_POST['id']);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    /**
     *
     */
    public function getNewSchemeMethodsAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Material $materialModel */
        $materialModel = $this->model('Material');

        $data = $materialModel->getNewSchemeMethods((int)$_POST['id']);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    /**
     *
     */
    public function deleteNewSchemeMethodsAjax()
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        /** @var  Material $materialModel */
        $materialModel = $this->model('Material');

        $materialModel->deleteNewSchemeMethods((int)$_POST['id']);

        echo json_encode([], JSON_UNESCAPED_UNICODE);
    }
}
