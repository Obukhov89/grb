<?php

/**
 * Контроллер для журнала актов несоответствия
 */
class DiscrepancyController extends Controller
{
    public function index()
    {
        $random = rand();
        $this->data['title'] = "Журнал актов несоответствия";
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
        $this->addJS("/assets/plugins/modal/modalWindow.js");
        $this->addJS('/assets/plugins/magnific-popup/jquery.magnific-popup.min.js');
        $this->addJs("/assets/js/discrepancy-list.js?v={$random}");


        $this->view();
    }

    protected function collectFilter(array &$filter): void
    {
        foreach ($_POST['columns'] as $column) {
            if (!empty($column['search']['value'])) {
                $filter['search'][$column['data']] = $column['search']['value'];
            }
        }

        if (isset($_POST['order']) && !empty($_POST['columns'])) {
            $filter['order']['by'] = $_POST['columns'][$_POST['order'][0]['column']]['data'];
            $filter['order']['dir'] = $_POST['order'][0]['dir'];
        }

        if (!empty($_POST['dateStart'])) {
            $filter['search']['dateStart'] = date('Y-m-d', strtotime($_POST['dateStart']));
            $filter['search']['dateEnd'] = date('Y-m-d', strtotime($_POST['dateEnd']));
        }
        if (!empty($_POST['stage'])) {
            $filter['search']['stage'] = $_POST['stage'];
        }
        if (!empty($_POST['lab'])) {
            $filter['search']['lab'] = $_POST['lab'];
        }
        if (!empty($_POST['everywhere'])) {
            $filter['search']['everywhere'] = $_POST['everywhere'];
        }
    }

    public function getListProcessingAjax()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $discrepancyJournal = new Discrepancy();

        $filter = [
            'paginate' => [
                'length' => $_POST['length'], // кол-во строк на страницу
                'start' => $_POST['start'],  // текущая страница
            ],
            'search' => [],
            'order' => []
        ];

        $this->collectFilter($filter);
        $data = $discrepancyJournal->getDataToJournal($filter);

        $jsonData = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $data['recordsTotal'],
            "recordsFiltered" => $data['recordsFiltered'],
            "data" => $data['data']
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    public function create()
    {
        $attributes = $_POST;
        $attributes['created_at'] = date('Y-m-d');

        //todo: наверное валидацию сделать над
        $journal = new Discrepancy();
        $journal->createDiscrepancy($attributes);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function delete()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $rowId = $_POST['id'];
        if (is_null($rowId)) {
            return json_encode(['error' => true]);
        }

        $journal = new Discrepancy();
        $journal->deleteRow($rowId);

        return json_encode(['error' => false]);
    }

    public function addResponsible()
    {
        $fio = $_POST['fio'];

        if ($fio) {
            $journal = new Discrepancy();
            $journal->createResponsible($fio);
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function getResponsible()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $journal = new Discrepancy();
        $data = $journal->getResponsible();

        $response = [
            'data' => $data,
        ];

        echo json_encode($response);
    }

    public function getRow()
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();

        $id = $_POST['id'];
        $journal = new Discrepancy();
        $response = [];
        $response['data'] = $journal->getRow($id);

        echo json_encode($response);
    }

    public function edit()
    {
        $attributes = $_POST;

        $journal = new Discrepancy();
        $journal->editDiscrepancy($attributes);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    protected function collectRemarkForImport(?string $remark): array
    {
        $result = [
            'fix_date' => null,
            'fix_number' => null,
        ];
        if (is_null($remark) || empty($remark)) {
            return $result;
        }

        $pattern = '/Акт №(\d+) от (\d{2}\.\d{2}\.\d{2})/';

        if (preg_match($pattern, $remark, $matches)) {
            $fixNumber = $matches[1];
            $fixDate = DateTime::createFromFormat('d.m.y', $matches[2])->format('Y-m-d');

            $result['fix_date'] = $fixDate;
            $result['fix_number'] = $fixNumber;

            return $result;
        }

        return $result;
    }

    public function import()
    {
        $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        if(isset($_FILES['excel-file']['name']) && in_array($_FILES['excel-file']['type'], $file_mimes)) {
            $arr_file = explode('.', $_FILES['excel-file']['name']);
//            $extension = end($arr_file);

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($_FILES['excel-file']['tmp_name']);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();


            $attributes = [];
            for ($i = 0; $i < count($sheetData); $i++) {
                if ($i == 0) {
                    continue;
                }

                if (is_null($sheetData[$i][1]) || is_null($sheetData[$i][2]) || is_null($sheetData[$i][3])) {
                    continue;
                }

                $createdAt = DateTime::createFromFormat('m/d/Y', $sheetData[$i][1])->format('Y-m-d');
                $responsible = trim($sheetData[$i][2]);
                $summary = trim($sheetData[$i][3]);
                $remark = $this->collectRemarkForImport(trim($sheetData[$i][4]));
                $attributes[] = [
                    'created_at' => $createdAt,
                    'responsible' => $responsible,
                    'summary' => $summary,
                    'fix_date' => $remark['fix_date'],
                    'fix_number' => $remark['fix_number'],
                ];
            }

            $journal = new Discrepancy();
            $journal->clearTableForImport();
            for ($i = 0; $i < count($attributes); $i++) {
                $journal->createDiscrepancy($attributes[$i]);
            }
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
}