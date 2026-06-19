<?php

use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Field;
use NcJoes\OfficeConverter\OfficeConverter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use Mpdf\Mpdf as MpdfLib;
use \Bitrix\Main\Loader;

class Secondment extends Model
{
    const DATE_START = '2022-01-01';
    const DATE_END = '2023-12-31';

    public function getDateStart()
    {
        return self::DATE_START;
    }

    public function getDateEnd()
    {
        //return self::DATE_END;
        return date('Y') + 1 . "-12-31";
    }

    /**
     * @param $userId
     * @param $filter
     * @return array
     */
    public function getDataToSecondmentJournal($filter)
    {
        global $DB;

        $where = "s.del = 0 AND ";
        $limit = "";
        $order = [
            'by' => 's_id',
            'dir' => 'DESC'
        ];
        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел


            // работа с фильтрами
            if (!empty($filter['search'])) {
                // Заявка
                if (isset($filter['search']['title'])) {
                    $where .= "CONCAT(s.id, '/', IF(YEAR(s.created_at)  % 10, SUBSTR(YEAR(s.created_at), -2), YEAR(s.created_at))) LIKE '%{$filter['search']['title']}%' AND ";
                }
                // Пользователь
                if (isset($filter['search']['fio'])) {
                    $where .= "CONCAT(b_u.LAST_NAME, ' ', b_u.NAME, ' ', b_u.SECOND_NAME) LIKE '%{$filter['search']['fio']}%' AND ";
                }
                // Населенный пункт
                if (isset($filter['search']['s_s_name'])) {
                    $where .= "s_s.name LIKE '%{$filter['search']['s_s_name']}%' AND ";
                }
                // Объект
                if (isset($filter['search']['d_o_name'])) {
                    $where .= "d_o.name LIKE '%{$filter['search']['d_o_name']}%' AND ";
                }
                // Дата начала командировки
                if (isset($filter['search']['date_begin'])) {
                    $where .= "LOCATE('{$filter['search']['date_begin']}', DATE_FORMAT(s.date_begin, '%d.%m.%Y')) > 0 AND ";
                }
                // Дата окончания командировки
                if (isset($filter['search']['date_end'])) {
                    $where .= "LOCATE('{$filter['search']['date_end']}', DATE_FORMAT(s.date_end, '%d.%m.%Y')) > 0 AND ";
                }
                // Запланированные затраты(Итого)
                if (isset($filter['search']['planned_expenses'])) {
                    $where .= "s.planned_expenses LIKE '%{$filter['search']['planned_expenses']}%' AND ";
                }
                // Фактические затраты(Всего потрачено)
                if (isset($filter['search']['total_spent'])) {
                    $where .= "s.total_spent LIKE '%{$filter['search']['total_spent']}%' AND ";
                }
                // Перерасход %
                if (isset($filter['search']['overspending'])) {
                    $where .= "s.overspending LIKE '%{$filter['search']['overspending']}%' AND ";
                }

                if (isset($filter['search']['dateStart'])) {
                    $where .= "(s.date_begin <= '{$filter['search']['dateEnd']}' AND s.date_end >= '{$filter['search']['dateStart']}') AND ";
                }

                if (isset($filter['search']['stage_filter'])) {
                    $where .= "s.stage IN ({$filter['search']['stage_filter']}) AND ";
                }

                if (isset($filter['search']['oborud_list'])) {
                    $where .= "b_o.OBJECT LIKE '%{$filter['search']['oborud_list']}%' AND ";
                }

                // везде
                if (isset($filter['search']['everywhere'])) {
                    $where .=
                        "(
                        CONCAT(s.id, '/', IF(YEAR(s.created_at)  % 10, SUBSTR(YEAR(s.created_at), -2), YEAR(s.created_at))) LIKE '%{$filter['search']['everywhere']}%' 
                        OR CONCAT(b_u.LAST_NAME, ' ', b_u.NAME, ' ', b_u.SECOND_NAME) LIKE '%{$filter['search']['everywhere']}%' 
                        OR s_s.name LIKE '%{$filter['search']['everywhere']}%' 
                        OR d_o.name LIKE '%{$filter['search']['everywhere']}%' 
                        OR LOCATE('{$filter['search']['everywhere']}', DATE_FORMAT(s.date_begin, '%d.%m.%Y')) > 0 
                        OR LOCATE('{$filter['search']['date_end']}', DATE_FORMAT(s.date_end, '%d.%m.%Y')) > 0  
                        OR s.planned_expenses LIKE '%{$filter['search']['everywhere']}%'  
                        OR s.total_spent LIKE '%{$filter['search']['everywhere']}%'  
                        OR s.overspending LIKE '%{$filter['search']['everywhere']}%'  
                        ) AND ";
                }
            }

            // работа с сортировкой
            if (!empty($filter['order'])) {
                if ($filter['order']['dir'] === 'asc') {
                    $order['dir'] = 'ASC';
                }

                switch ($filter['order']['by']) {
                    case 'fio':
                        $order['by'] = "CONCAT(b_u.LAST_NAME, ' ', b_u.NAME, ' ', b_u.SECOND_NAME)";
                        break;
                    case 'title':
                        $order['by'] = "s.id";
                        break;
                    case 's_s_name':
                        $order['by'] = 's_s.name';
                        break;
                    case 'd_o_name':
                        $order['by'] = 'd_o.name';
                        break;
                    case 'date_begin':
                        $order['by'] = 's.date_begin';
                        break;
                    case 'date_end':
                        $order['by'] = 's.date_end';
                        break;
                    case 'planned_expenses':
                        $order['by'] = 's.planned_expenses';
                        break;
                    case 'total_spent':
                        $order['by'] = 's.total_spent';
                        break;
                    case 'overspending':
                        $order['by'] = 's.overspending';
                        break;
                    case 'stage':
                        $order['by'] = 's.stage';
                        break;
                }
            }

            // работа с пагинацией
            if (isset($filter['paginate'])) {
                $offset = 0;
                // количество строк на страницу
                if (isset($filter['paginate']['length']) && $filter['paginate']['length'] > 0) {
                    $length = $filter['paginate']['length'];

                    if (isset($filter['paginate']['start']) && $filter['paginate']['start'] > 0) {
                        $offset = $filter['paginate']['start'];
                    }
                    $limit = "LIMIT {$offset}, {$length}";
                }
            }
        }

        if (isset($filter["managerAccess"])) {
            $useridList = $filter["managerAccess"];
            $where .= "s.user_id IN ({$useridList}) AND ";
        }

        $where .= "1 ";

        $result = [];

        $userId = $_SESSION['SESS_AUTH']['USER_ID'];
// s.planned_expenses - заменил на расчет
        $sql = "SELECT s.id s_id, s.project_id, s.user_id, s.settlement_id, s.date_begin, s.date_end, s.stage, s.per_diem,
                s.ticket_price + s.gasoline_consumption + s.gasoline_consumption_object + s.per_diem + s.accommodation + COALESCE(SUM(s_ot.sum), 0) AS planned_expenses,
      
                s.total_spent, YEAR(s.created_at) created_year, s.overspending, 
                CONCAT(b_u.LAST_NAME, ' ', b_u.NAME, ' ', b_u.SECOND_NAME) fio, 
                CONCAT(s.id, '/', IF(YEAR(s.created_at)  % 10, SUBSTR(YEAR(s.created_at), -2), YEAR(s.created_at))) title, 
                s_s.name s_s_name, d_o.NAME d_o_name, f_s.settlement,
                o_p.name project_name,
                (SELECT m_v.id FROM module_viewed m_v WHERE m_v.module_id = s.id AND m_v.user_id = {$userId}) AS viewed,
                (SELECT GROUP_CONCAT(CONCAT(b_o.OBJECT) SEPARATOR '\n') FROM ba_oborud AS b_o RIGHT JOIN secondment_oborud AS s_o ON b_o.ID = s_o.oborud_id WHERE s.id = s_o.secondment_id) AS oborud_list,
                (SELECT count(action) FROM confirm_report WHERE s.id = secondment_id) as confirmeds_report
            FROM secondment AS s 
            LEFT JOIN b_user AS b_u ON s.user_id = b_u.ID 
            LEFT JOIN settlements AS s_s ON s.settlement_id = s_s.id 
            LEFT JOIN DEV_OBJECTS AS d_o ON s.object_id = d_o.ID
            LEFT JOIN full_settlements AS f_s ON d_o.CITY_ID = f_s.id 
            LEFT JOIN secondment_oborud AS s_o ON s_o.secondment_id = s.id 
            LEFT JOIN ba_oborud AS b_o ON b_o.ID = s_o.oborud_id 
            LEFT JOIN osk_project AS o_p ON o_p.id = s.project_id
            LEFT JOIN secondment_other AS s_ot ON s_ot.secondment_id = s.id
            WHERE {$where} 
            GROUP BY s.id 
            ORDER BY {$order['by']} {$order['dir']} {$limit}";
        $data = $DB->Query($sql);

        $dataTotal = $this->DB->Query(
            "SELECT s.id val
             FROM secondment AS s 
             LEFT JOIN b_user AS b_u ON s.user_id = b_u.ID 
             LEFT JOIN settlements AS s_s ON s.settlement_id = s_s.id 
             LEFT JOIN DEV_OBJECTS AS d_o ON s.object_id = d_o.ID
             LEFT JOIN secondment_oborud AS s_o ON s_o.secondment_id = s.id 
             LEFT JOIN ba_oborud AS b_o ON b_o.ID = s_o.oborud_id 
             GROUP BY s.id"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT s.id val
             FROM secondment AS s 
             LEFT JOIN b_user AS b_u ON s.user_id = b_u.ID 
             LEFT JOIN settlements AS s_s ON s.settlement_id = s_s.id 
             LEFT JOIN DEV_OBJECTS AS d_o ON s.object_id = d_o.ID
             LEFT JOIN secondment_oborud AS s_o ON s_o.secondment_id = s.id 
             LEFT JOIN ba_oborud AS b_o ON b_o.ID = s_o.oborud_id 
             WHERE {$where} 
             GROUP BY s.id"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $stage = $this->getStageColor($row['stage']);
            $row['stage_border_color'] = $stage['border_color'];

            $row['fio'] = !empty($row['fio']) ?
                htmlentities(trim($row['fio']), ENT_QUOTES, 'UTF-8') : '';

            $row['overspending'] = $row['overspending'] ?: '';

            //Если отчет подтвердили более 1 пользователя то показывает "Фактические затраты"(Всего потрачено)
            //  $row['confirmeds_report'] = json_decode($row['confirmeds_report'], true);
            //   $row['total_spent'] = !empty($row['total_spent']) && !empty($row['confirmeds_report']) &&
            // count($row['confirmeds_report']) > 1 ? $row['total_spent'] : '';
            //$row['total_spent'] = $row['confirmeds_report'] > 1 ? $row['total_spent'] : '';

            $row['planned_expenses'] = $row['planned_expenses'] ?: 0;
            $totalDays = date_diff(new DateTime($row['date_begin']), new DateTime($row['date_end']))->days + 1;

           if (!intval($row['per_diem'])) {
               if ($totalDays == 1) {
                   $row['per_diem'] = 0;
               } else {
                   $row['per_diem'] = $totalDays * 700; // суточные
               }

               $row['planned_expenses'] +=  $row['per_diem'];
           }


            $row['uri'] = URI;

            $row['date_begin_ru'] = !empty($row['date_begin']) && $row['date_begin'] !== "0000-00-00" ?
                date('d.m.Y', strtotime($row['date_begin'])) : '';

            $row['date_end_ru'] = !empty($row['date_end']) && $row['date_end'] !== "0000-00-00" ?
                date('d.m.Y', strtotime($row['date_end'])) : '';

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;
        $result['test'] = $sql;

        return $result;
    }

    /**
     * @param array $data
     * @param string $table
     * @return int
     */
    public function create(array $data, string $table): int
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $result = $this->DB->Insert($table, $data);

        return intval($result);
    }

    /**
     * @param array $data
     * @param string $table
     * @param int $id
     */
    public function update(array $data, string $table, int $id)
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $where = "WHERE ID = {$id}";
        return $this->DB->Update($table, $data, $where);
    }

    public function delete($table, $where)
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $this->DB->Query($sql);

    }

    /**
     * @return array
     */
    public function getSettlementsData(): array
    {
        $response = [];

        // $settlements = $this->DB->Query("SELECT * FROM settlements");
        $settlements = $this->DB->Query("SELECT * FROM full_settlements");

        while ($row = $settlements->Fetch()) {
            $row['name'] = !empty($row['settlement']) ?
                htmlentities($row['settlement'], ENT_QUOTES, 'UTF-8') : '';
//            $row['country'] = !empty($row['country']) ?
//                htmlentities($row['country'], ENT_QUOTES, 'UTF-8') : '';

            $response[] = $row;
        }

        return $response;
    }

    public function getSettlementsByName($name)
    {
        $response = [];

        // $settlements = $this->DB->Query("SELECT * FROM settlements");
        $settlements = $this->DB->Query("SELECT * FROM full_settlements WHERE settlement LIKE '%'{$name}'%' GROUP BY settlement");

        while ($row = $settlements->Fetch()) {
            $row['name'] = !empty($row['settlement']) ?
                htmlentities($row['settlement'], ENT_QUOTES, 'UTF-8') : '';
//            $row['country'] = !empty($row['country']) ?
//                htmlentities($row['country'], ENT_QUOTES, 'UTF-8') : '';

            $response[] = $row;
        }

        return $response;
    }

    /**
     * @return array
     */
    public function getObjectsData(): array
    {
        $response = [];

        $objects = $this->DB->Query("SELECT * FROM DEV_OBJECTS");

        while ($row = $objects->Fetch()) {
            $row['NAME'] = !empty($row['NAME']) ?
                htmlentities($row['NAME'], ENT_QUOTES, 'UTF-8') : '';

            $response[] = $row;
        }

        return $response;
    }

    // TODO: Проверить нужен ли еще метод
    /**
     * @param int|null $id
     * @return array
     */
    public function getObjectDataById(?int $id): array
    {
        $response = [];

        $object = $this->DB->Query("SELECT DEV_OBJECTS.*, full_settlements.settlement FROM DEV_OBJECTS 
                                    LEFT JOIN full_settlements ON DEV_OBJECTS.CITY_ID = full_settlements.id 
                                    WHERE DEV_OBJECTS.ID={$id}")->Fetch();

        if (!empty($object)) {
            $object['NAME'] = !empty($object['NAME']) ?
                htmlentities($object['NAME'], ENT_QUOTES, 'UTF-8') : '';

            $response = $object;
        }

        return $response;
    }

    /**
     * @param int|null $id
     * @return array
     */
    public function getObjectDataByCompanyId(?int $id): array
    {
        $response = [];

        if (empty($id) || $id < 0) {
            return $response;
        }

        $objects = $this->DB->Query("SELECT DEV_OBJECTS.*, full_settlements.settlement, full_settlements.id AS settlement_id FROM DEV_OBJECTS 
                                     LEFT JOIN full_settlements ON DEV_OBJECTS.CITY_ID = full_settlements.id  
                                     WHERE DEV_OBJECTS.ID_COMPANY={$id}");

        while ($row = $objects->Fetch()) {
            $row['NAME'] = !empty($row['NAME']) ?
                htmlentities($row['NAME'], ENT_QUOTES, 'UTF-8') : '';

            $response[$row["ID"]] = $row;
        }

        return $response;
    }

    /**
     * @param $id
     * @return array
     */
    public function getSecondmentDataById(int $id): array
    {
        $response = [];

        if (empty($id) || $id < 0) {
            return $response;
        }

//        SELECT s.id s_id, s.user_id, s.settlement_id, s.object_id, s.contract_id, s.date_begin, s.date_end, s.total_days,
//                s.content, s.ticket_price, s.comment_ticket_price, s.gasoline_consumption, s.comment_gasoline_consumption,
//                s.per_diem, s.comment_per_diem, s.accommodation, s.comment_accommodation, s.other, s.comment_other,
//                s.planned_expenses, s.comment_planned_expenses, s.stage,  s.total_spent,
//                s.transport, s.comment, s.memo, s.overspending,
//                s.company_id, s_s.name s_s_name, s.vehicle_id, s.del, s.contract, s.improvement_reason,YEAR(s.created_at) created_year

        $secondment = $this->DB->Query(
            "SELECT s.*, s.id s_id, YEAR(s.created_at) created_year, s_s.name s_s_name
                FROM secondment AS s 
                    LEFT JOIN settlements AS s_s ON s.settlement_id = s_s.id
                    WHERE s.id={$this->DB->ForSql($id)}"
        )->Fetch();


        if (!empty($secondment) && is_array($secondment)) {
            $secondment['year_format'] = (int)$secondment['created_year'] % 10 ?
                substr($secondment['created_year'], -2) : $secondment['created_year'];
            $secondment['title'] = 'Заявка № ' . $secondment['s_id'] . '/' . $secondment['year_format'];

            //$secondment['confirmeds_report'] = json_decode($secondment['confirmeds_report'], true);
            //$secondment['verification_overspending'] = json_decode($secondment['verification_overspending'], true);


            $secondment['comment_ticket_price'] = !empty($secondment['comment_ticket_price']) ?
                htmlentities($secondment['comment_ticket_price'], ENT_QUOTES, 'UTF-8') : '';
            $secondment['comment_gasoline_consumption'] = !empty($secondment['comment_gasoline_consumption']) ?
                htmlentities($secondment['comment_gasoline_consumption'], ENT_QUOTES, 'UTF-8') : '';
            $secondment['comment_per_diem'] = !empty($secondment['comment_per_diem']) ?
                htmlentities($secondment['comment_per_diem'], ENT_QUOTES, 'UTF-8') : '';
            $secondment['comment_accommodation'] = !empty($secondment['comment_accommodation']) ?
                htmlentities($secondment['comment_accommodation'], ENT_QUOTES, 'UTF-8') : '';
            $secondment['comment_other'] = !empty($secondment['comment_other']) ?
                htmlentities($secondment['comment_other'], ENT_QUOTES, 'UTF-8') : '';
            $secondment['comment_planned_expenses'] = !empty($secondment['comment_planned_expenses']) ?
                htmlentities($secondment['comment_planned_expenses'], ENT_QUOTES, 'UTF-8') : '';
            $secondment['transport'] = !empty($secondment['transport']) ?
                htmlentities($secondment['transport'], ENT_QUOTES, 'UTF-8') : '';
            $secondment['comment'] = !empty($secondment['comment']) ?
                htmlentities($secondment['comment'], ENT_QUOTES, 'UTF-8') : '';
            $secondment['memo'] = !empty($secondment['memo']) ?
                htmlentities($secondment['memo'], ENT_QUOTES, 'UTF-8') : '';

            $response = $secondment;
        }

        return $response;
    }

    /**
     * @param string|null $stage
     * @return array|string[]
     */
    public function getStageColor(?string $stage): array
    {
        if (empty($stage)) {
            return [
                'bg_color' => 'bg-red',
                'border_color' => 'border-red',
            ];
        }

        switch ($stage) {
            case 'Новая':
                $bgColor = 'bg-powder-blue';
                $borderColor = 'border-powder-blue';
                break;
            case 'Ожидает подтверждения':
                //$bgColor = 'bg-sky-blue';
                //$borderColor = 'border-sky-blue';
                $bgColor = 'bg-gold';
                $borderColor = 'border-gold';
                break;
            case 'Отклонена':
                $bgColor = 'bg-red';
                $borderColor = 'border-red';
                break;
            case 'Нужна доработка':
                //$bgColor = 'bg-steel-blue';
                //$borderColor = 'border-steel-blue';
                $bgColor = 'bg-orange';
                $borderColor = 'border-orange';
                break;
            case 'Подготовка приказа и СЗ':
                $bgColor = 'bg-deep-sky-blue';
                $borderColor = 'border-deep-sky-blue';
                break;
            case 'Согласована':
                $bgColor = 'bg-dodger-blue';
                $borderColor = 'border-dodger-blue';
                break;
            case 'В командировке':
                $bgColor = 'bg-royal-blue';
                $borderColor = 'border-royal-blue';
                break;
            case 'Подготовка отчета':
                $bgColor = 'bg-blue';
                $borderColor = 'border-blue';
                break;
            case 'Проверка отчета':
                $bgColor = 'bg-medium-blue';
                $borderColor = 'border-medium-blue';
                break;
            case 'Отчет подтвержден':
                $bgColor = 'bg-dark-blue';
                $borderColor = 'border-dark-blue';
                break;
            case 'Затраты не подтверждены':
                $bgColor = 'bg-dark-orange';
                $borderColor = 'border-dark-orange';
                break;
            case 'Завершена':
                $bgColor = 'bg-green';
                $borderColor = 'border-green';
                break;
            default:
                $bgColor = 'border-red';
                $borderColor = 'border-red';
                break;
        }

        return [
            'bg_color' => $bgColor,
            'border_color' => $borderColor,
        ];
    }


    //TODO: Переделать, изменять имя файла
    public function saveAnyFile(string $path, array $file, ?string $nameFile = null)
    {
        if ($file['error'] !== 0 || empty($file['tmp_name'])) {
            return ['error' => 'Файл пустой или ошибка загрузки'];
        }

        $uploaddir = UPLOAD_DIR . "/{$path}";
        if (!is_dir($uploaddir)) {
            mkdir($uploaddir, 0755, true);
        }

        if ($nameFile) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = pathinfo($nameFile, PATHINFO_FILENAME) . '.' . $ext;
        } else {
            $newFileName = $file['name'];
        }

        $saveResult = $this->saveFile($uploaddir, $newFileName, $file['tmp_name']);

        if ($saveResult) {
            return ['success' => true, 'filename' => $newFileName];
        }

        return ['error' => 'Ошибка сохранения файла'];
    }

    /**
     * @param int|null $secondmentId
     * @param int $userId
     * @param int $action
     * @return int|null
     */
    public function setConfirmRejectReport(?int $secondmentId, int $userId, int $action = 0): ?int
    {
        $result = null;

        if (empty($secondmentId) || empty($userId)) {
            return null;
        }

        $this->DB->Query("DELETE FROM confirm_report WHERE secondment_id = {$secondmentId} AND user_id = {$userId}");

        $result = $this->DB->Insert(
            'confirm_report',
            [
                'secondment_id' => $secondmentId,
                'user_id' => $userId,
                'action' => $action
            ]
        );

        return intval($result);
    }

    /**
     * @param int|null $secondmentId
     * @return array
     */
    public function getConfirmRejectReport(?int $secondmentId): array
    {
        $response = [];

        if (empty($secondmentId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM confirm_report WHERE secondment_id = {$secondmentId}");

        while ($row = $result->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    public function getConfirmReport(?int $secondmentId): array
    {
        $response = [];

        if (empty($secondmentId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM confirm_report WHERE secondment_id = {$secondmentId} AND action = 1");

        while ($row = $result->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    /**
     * @param int|null $secondmentId
     * @param int $userId
     * @return array
     */
    public function getConfirmationCurrentUser(?int $secondmentId, int $userId): array
    {
        $response = [];

        if (empty($secondmentId) || empty($userId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM confirm_report 
            WHERE secondment_id = {$secondmentId} AND user_id = {$userId} AND action = 1")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    // Получить список городов
    public function getCityArr($name)
    {
        $response = [];

        $result = $this->DB->Query("SELECT * FROM full_settlements WHERE settlement LIKE '%{$name}%' GROUP BY settlement");

        while ($row = $result->fetch()) {
            $arr = [
                "id" => $row["id"],
                "text" => $row["settlement"]
            ];

            $response[] = $arr;
        }

        return $response;
    }

    public function getCityById($id)
    {
        $result = $this->DB->Query("SELECT * FROM full_settlements WHERE id = {$id}");

        return $result->fetch()["settlement"];
    }

    // Получить список компаний
    public function getCompanyList()
    {
        $companyList = [];
        $companyObj = CCrmCompany::GetList();

        $i = 0;

        while ($row = $companyObj->Fetch()) {
            $companyList[$i]["id"] = $row["ID"];
            $companyList[$i]["title"] = $row["TITLE"];
            $i++;
        }

        return $companyList;
    }

    public function getOtherFieldsById($secondmentId)
    {
        $response = [];

        $result = $this->DB->Query("SELECT * FROM secondment_other WHERE secondment_id = {$secondmentId}");

        while ($row = $result->fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    public function getAdditionalFieldsById($secondmentId)
    {
        $response = [];

        $result = $this->DB->Query("SELECT * FROM secondment_additional WHERE secondment_id = {$secondmentId}");

        while ($row = $result->fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    // Получить договоры по id клиента (без сделки)
    public function getContractsByCompanyId($id)
    {
        $response = [];

        $result = $this->DB->Query("SELECT * FROM contracts WHERE client_id = {$id}");

        while ($row = $result->fetch()) {
            $response[$row["id"]] = $row;
        }

        return $response;
    }

    public function getArchiveCard($id)
    {
        return $this->DB->Query("SELECT *, DATE_FORMAT(created_at, '%d.%m.%Y') AS created_at FROM secondment_archive WHERE id = {$id}")->fetch();
    }

    public function getArchiveListBySecondmentId($id)
    {
        $response = [];

        $result = $this->DB->Query("SELECT *, DATE_FORMAT(created_at, '%d.%m.%Y') AS created_at FROM secondment_archive WHERE secondment_id  = {$id}");

        while ($row = $result->fetch()) {
            $arrData = json_decode($row["json_data"], JSON_UNESCAPED_UNICODE);

            $row = array_merge($row, $arrData);

            $row["date_begin"] = date_format(date_create($arrData["date_begin"]),"d.m.Y");
            $row["date_end"] = date_format(date_create($arrData["date_end"]),"d.m.Y");

            $response[] = $row;
        }

        return $response;
    }

    public function getCompensationBySecondmentId($id) {
        return $this->DB->Query("SELECT * FROM secondment_compensations WHERE secondment_id = {$id}")->fetch();
    }

    public function insertUpdateCompensation($sum, $secondmentId) {
        $sql = "INSERT INTO secondment_compensations (sum, secondment_id) VALUES ({$sum}, {$secondmentId})
                ON DUPLICATE KEY UPDATE sum = VALUES(sum)";

        return $this->DB->Query($sql);
    }

    public function getDeptUsers($deptId, $managerId)
    {
        $user = new User();

        $arr = $user->getUserList(
            ['LAST_NAME' => 'asc'],
            [],
            ["ID"],
            [
                'UF_DEPARTMENT' => [$deptId], // ИД лабораторий

                'ID' => [$managerId] // ИД отдельных сотрудников
            ]);

        $userIdList = [];

        if (!empty($arr)) {
            foreach ($arr as $user) {
                $userId = intval($user["ID"]);
                if ($userId !== $managerId) {
                    $userIdList[] = intval($user["ID"]);
                }
            }
        }

        return $userIdList;
    }

    public function updateRow($data, $id)
    {
        $sqlData = $this->prepearTableData('secondment', $data);
        $where = "WHERE id = {$id}";
        return $this->DB->Update('secondment', $sqlData, $where);
    }

    public function addOrder($data)
    {
        $user = new User();
        $userId = $data['userId'];
        $res = $this->DB->Query("SELECT `UF_DEPARTMENT` as depId FROM `b_uts_user` WHERE `VALUE_ID` = {$userId}")->Fetch();
        $tmp = unserialize($res['depId']);
        $userDep = $tmp[0];
        $res = $this->DB->Query("SELECT `NAME` as name FROM `ba_laba` WHERE `id_dep` = {$userDep}")->Fetch();
        $res2 = $this->DB->Query("SELECT CONCAT(LAST_NAME, ' ', NAME, ' ', SECOND_NAME) AS FULL_NAME, WORK_POSITION FROM `b_user` WHERE `ID` = {$userId}")->Fetch();
        $info = [
            'number' => trim($data['edict_number']),
            'date' => date('d.m.Y'),
            'name' => trim($res2['FULL_NAME']),
            'position' => trim($res2['WORK_POSITION']),
            'object' => trim($res['name']),
            'city' => trim($data['city']),
            'org' => trim($data['org']),
            'start' => trim($data['start']),
            'end' => trim($data['end']),
            'days' => trim($data['totalDays']),
            'days-to' => trim($data['totalDays']),
            'goal' => trim($data['contract']),
        ];

        $templateDoc = $_SERVER["DOCUMENT_ROOT"] . '/SecondmentOrder.docx';
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $template = new \PhpOffice\PhpWord\TemplateProcessor($templateDoc);

        $interimPath = "upload/secondment/service_assignment/" . $data['secondment_id'] . "/СЗ " . $data['edict_number'] . ".docx";
        $interimPathPDF = "upload/secondment/service_assignment/" . $data['secondment_id'] . "/СЗ  " . $data['edict_number'] . ".pdf";
        $outputFile = "СЗ " . $data['edict_number'] . ".pdf";
        $outputPath = "upload/secondment/service_assignment/" . $data['secondment_id'] . "/";

        $file = $_SERVER['DOCUMENT_ROOT'] .'/ulab/'.$interimPath;
        $filePDF = $_SERVER['DOCUMENT_ROOT'] .'/ulab/'.$interimPathPDF;

        $newDirectory = $_SERVER['DOCUMENT_ROOT'] . "/ulab/upload/secondment/service_assignment/" . $data['secondment_id'];

        if (!is_dir($newDirectory)) {
            mkdir($newDirectory, 0777, true);
        }

        $template->setValues($info);

        $template->saveAs($file);

// --- ДОБАВЛЕНО: временные латинские имена для конвертации ---
        $tmpBase = "tmp_" . $data['secondment_id'] . "_" . $data['edict_number'];
        $tmpDocx = $newDirectory . "/" . $tmpBase . ".docx";
        $tmpPdf  = $newDirectory . "/" . $tmpBase . ".pdf";
// -----------------------------------------------------------

        $fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/ulab/'.$interimPath;
        $fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/ulab/'.$outputPath;

        if (!is_dir($fullPathPDF)) {
            mkdir($fullPathPDF, 0777, true);
        }

// --- ДОБАВЛЕНО: копируем DOCX в латиницу, конвертим, переименовываем ---
        copy($fullPathDocx, $tmpDocx);

        $converter = new OfficeConverter($tmpDocx, $fullPathPDF);
        $converter->convertTo($tmpBase . ".pdf");

        rename($tmpPdf, $filePDF);
        unlink($tmpDocx);
        unlink($fullPathDocx);
// ---------------------------------------------------------------------
    }


//    public function getVehiclesList()
//    {
//        $response = [];
//
//        $result = $this->DB->Query("
//            SELECT v.*, f_t.title, f_t.price FROM vehicles AS v
//            LEFT JOIN fuel_types AS f_t ON v.fuel_id = f_t.id
//        ");
//
//        while ($row = $result->fetch()) {
//            $response[$row["id"]] = $row;
//        }
//
//        return $response;
//    }
}