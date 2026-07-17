<?php


/**
 * Класс результатов испытаний
 * Class Result
 */
class Result extends Model
{
    /**
     * получить пробы без прикрепленного протокола или без номера протокола
     * @param int $dealId
     * @return array
     */
    public function getProbsWithoutProtocolNum(int $dealId): array
    {
        $response = [];

        if (empty($dealId) || $dealId < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT umtr.deal_id, umtr.protocol_id, p.NUMBER FROM ulab_material_to_request umtr 
            LEFT JOIN PROTOCOLS p ON p.ID = umtr.protocol_id 
            WHERE umtr.deal_id = {$dealId} AND (umtr.protocol_id IS NULL || p.NUMBER IS NULL)");

        while ($row = $result->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    /**
     * морозостойкость
     * @param int|null $protocolId
     * @return array
     */
    public function getFrostByProtocolId(?int $protocolId): array
    {
        $response = [];

        if (empty($protocolId) || $protocolId < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM b_frost WHERE protocol_id = {$protocolId}")->Fetch();

        if (!empty($result)) {
            $result['control_strength'] = unserialize($result['control_strength']);
            $result['main_strength'] = unserialize($result['main_strength']);

            $response = $result;
        }

        return $response;
    }

    /**
     * @param int $frostId
     * @param array $data
     */
    public function updateFrost(int $protocolId, array $data)
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $where = "WHERE protocol_id = {$protocolId}";
        return $this->DB->Update('b_frost', $data, $where);
    }

    /**
     * протокол недействителен
     * @param int|null $protocolId
     * @return array
     */
    public function getUlabProtocolInvalidByProtocolId(?int $protocolId): array
    {
        $response = [];

        if (empty($protocolId) || $protocolId < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM ulab_protocol_invalid WHERE protocol_id = {$protocolId}")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
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
     * записываем историю изменений
     * @param array $data
     * @return int
     */
    public function addHistory(array $data): int
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $result = $this->DB->Insert('HISTORY', $data);

        return intval($result);
    }


    /**
     * сохранить PDF файл
     * @param string $path
     * @param array $file
     * @param string $fileName
     * @return array
     */
    public function savePdfFile(string $path, array $file, string $fileName): array
    {
        if (empty($path) || empty($file['name'])) {
            return [
                'success' => false,
                'error' => [
                    'message' => "Ошибка в параметрах при загрузке файла",
                ]
            ];
        }

        if (mime_content_type($file['tmp_name']) !== 'application/pdf') {
            return [
                'success' => false,
                'error' => [
                    'message' => "Ошибка формата PDF: не pdf формат или поврежден",
                ]
            ];
        }


        $uploaddir = UPLOAD_DIR . "/{$path}";

        return $this->saveFile($uploaddir, $fileName, $file['tmp_name']);
    }

    /**
     * удаляем созданный протокол
     * @param int $protocolId
     * @return mixed
     */
    public function deleteProtocolById(int $protocolId)
    {
        return $this->DB->Query("DELETE FROM PROTOCOLS WHERE ID = {$protocolId}");
    }

    /**
     * @param int $dealId
     * @return array
     */
    public function getProtocolsByDealId(int $dealId): array
    {
        $response = [];

        if (empty($dealId) || $dealId < 0) {
            return $response;
        }

        $result = $this->DB->Query("select p.ID, p.DATE, p.NUMBER, p.ostatki, p.sostav, p.EDIT_RESULTS, p.PROTOCOL_OUTSIDE_LIS, 
            p.DATE_END, p.CHANGE_TRIALS_CONDITIONS, upi.id upi_id, upi.action upi_action, 
            (select COUNT(*) from ulab_material_to_request where protocol_id = p.ID) probe_count FROM PROTOCOLS p 
            LEFT JOIN ulab_protocol_invalid upi 
            ON upi.protocol_id = p.ID where p.DEAL_ID = {$dealId} order by p.ID");


        while ($row = $result->Fetch()) {
            $row['view_number'] = (int)$row['NUMBER'] ?: 'Номер не присвоен';
            $row['date_ru'] = !empty($row['DATE']) && $row['DATE'] !== '0000-00-00' ?
                date('d.m.Y', strtotime($row['DATE'])) : '';
            $row['ostatki'] = !empty($row['ostatki']) ? unserialize($row['ostatki']) : [];
            $row['sostav'] = !empty($row['sostav']) ? unserialize($row['sostav']) : [];

            $response[] = $row;
        }

        return $response;
    }

    /**
     * @param int|null $protocolId
     * @return array
     */
    public function getProtocolById(?int $protocolId): array
    {
        $response = [];

        if (empty($protocolId) || $protocolId < 0) {
            return $response;
        }

        $result = $this->DB->Query("select p.*, upi.id upi_id, upi.action upi_action, 
            (select COUNT(*) from ulab_material_to_request where protocol_id = p.ID) probe_count 
            FROM PROTOCOLS p 
            LEFT JOIN ulab_protocol_invalid upi ON upi.protocol_id = p.ID 
            where p.ID = {$protocolId}")->Fetch();

        if (!empty($result)) {
            $result['view_number'] = (int)$result['NUMBER'] ?: 'Номер не присвоен';
            $result['date_ru'] = !empty($result['DATE']) && $result['DATE'] !== '0000-00-00' ?
                date('d.m.Y', strtotime($result['DATE'])) : '';
            $result['ostatki'] = !empty($result['ostatki']) ? unserialize($result['ostatki']) : [];
            $result['sostav'] = !empty($result['SOSTAV']) ? unserialize($result['SOSTAV']) : [];
            $result['verify'] = !empty($result['VERIFY']) ? unserialize($result['VERIFY']) : [];

            $response = $result;
        }

        return $response;
    }

    /**
     * Добавить условия к функционалу созданных протоколов
     * @param $protocols
     * @param $deal
     * @param string $selected
     * @param string $checked
     * @return array
     */
    public static function addConditionsToProtocols($protocols, $deal, $selected = '', $checked = '')
    {
        $requestModel = new Request;

        if (empty($protocols)) {
            return [];
        }

        $isDealOsk = $deal['TYPE_ID'] == 'COMPLEX';

        foreach ($protocols as $key => $protocol) {
            if (empty($protocol['PROTOCOL_OUTSIDE_LIS']) && empty($protocol['PDF'])) {
                //TODO: Доработать после рефакторинга формирования протоколов
                $year = !empty($protocol['DATE']) ?
                    date("Y", strtotime($protocol['DATE'])) : date("Y", strtotime($protocol['DATE_END']));
                $dir = "/home/bitrix/www/protocol_generator/archive/{$protocol['ID_TZ']}{$year}/{$protocol['ID']}/";
                $path = "/protocol_generator/archive/{$protocol['ID_TZ']}{$year}/{$protocol['ID']}/";
                $files = $requestModel->getFilesFromDirWithFileType($dir, ['signed.docx', 'forsign.docx', 'qrNEW.png', "{$protocol['NUMBER']}.sig"], "pdf");
                usort($files, function ($a, $b) {
                    $a = mb_substr($a, -24);
                    $b = mb_substr($b, -24);

                    if ($a == $b) {
                        return 0;
                    }
                    return ($a < $b) ? -1 : 1;
                });
            } else {
                $path = "/ulab/upload/result/pdf/{$protocol['ID']}/";
                $files = $requestModel->getFilesFromDir(UPLOAD_DIR . "/result/pdf/{$protocol['ID']}", ["{$protocol['NUMBER']}.sig"]);
            }


            // Если тип заявки НЕ "ОСК" и не выбрано "Изменить условия испытаний", то проверка по данным "Журнала условий"
            $protocols[$key]['validation_class'] = !$isDealOsk && empty($protocol['CHANGE_TRIALS_CONDITIONS']) ? 'validate-conditions' : 'validate-protocol';

            // "Информация по протоколу" (если протокол не выбран ИЛИ не действителен ИЛИ присвоен номер протоколу и протокол неразблокирован ИЛИ отсутствуют прикреплённые пробы, то доступ к информации протокола запрещён)
            $protocols[$key]['protocol_info'] = $selected !== $protocol['ID'] || !empty($protocol['INVALID']) ||
                !empty($protocol['NUMBER']) && empty($protocol['EDIT_RESULTS']) || empty($protocol['probe_count']);

            // "PDF-версия"
            $protocols[$key]['file']['number'] = $protocol['NUMBER'];
            $protocols[$key]['file']['dir'] = $path;
            $protocols[$key]['file']['file'] = $files[0];




            // "Только выбранные пробы" (радио кнопка checked если выбрана и соответствует протоколу)
            $protocols[$key]['selected_probe'] = $checked === $protocol['ID']  ? 'checked' : '';

            // Отображение выбранного протокола и прикреплённых проб в "Таблице созданных протоколов" и "Таблице результатов испытаний"
            $protocols[$key]['table_green'] = $selected === $protocol['ID'] ? 'table-gradient-green' : '';

            // "Сформировать протокол" (если протокол не выбран ИЛИ не действителен ИЛИ не выбраны пробы для протокола,
            // то мы не можем сформировать его)
            $protocols[$key]['is_create_protocol'] = $selected !== $protocol['ID'] || !empty($protocol['INVALID']) ||
                empty($protocol['probe_count']);

            // "Скачать протокол" (если нет файла ИЛИ протокол не действителен ИЛИ протокол выдан в не ЛИС, то мы не можем скачать файл)
            $protocols[$key]['doc_send'] = empty($protocols[$key]['file']['file']) ||
                !empty($protocol['INVALID']) || !empty($protocol['PROTOCOL_OUTSIDE_LIS']);

            // "Присвоить номер" (если уже был присвоен номер протоколу ИЛИ протокол не выбран ИЛИ не действителен ИЛИ
            // не сохранена дата начала испытаний ИЛИ не сохранена дата окончания испытаний ИЛИ
            // не сформирован протокол(нет актуальной версии) и небыл выдан в не ЛИС ИЛИ не выбраны пробы для протокола,
            // то присвоить номер мы не можем)
            $protocols[$key]['add_protocol_number'] = !empty($protocol['NUMBER']) ||
                $selected !== $protocol['ID'] || !empty($protocol['INVALID']) ||
                empty($protocol['DATE_BEGIN']) || $protocol['DATE_BEGIN'] === '0000-00-00' ||
                empty($protocol['DATE_END']) || $protocol['DATE_END'] === '0000-00-00' ||
                (empty($protocol['ACTUAL_VERSION']) && empty($protocol['PROTOCOL_OUTSIDE_LIS'])) || empty($protocol['probe_count']);

            // "Удалить протокол" (если протокол не выбран ИЛИ не действителен ИЛИ присвоен номер протоколу, то удалить протокол нельзя)
            $protocols[$key]['delete_protocol'] = $selected !== $protocol['ID'] ||
                !empty($protocol['INVALID']) || !empty($protocol['NUMBER']);

            // "Разблокировать" (если протокол не выбран ИЛИ не действителен, то разблокировать протокол нельзя).
            // Функционал "Разблокировать" - доступен роли "Админ" и роли "Руководитель ИЦ"
            $protocols[$key]['edit_results'] = $selected !== $protocol['ID'] ||
                !empty($protocol['INVALID']);

            // "Протокол недействителен" (если протокол не выбран ИЛИ уже признан недействительным ИЛИ отсутствует номер протокола,
            // то признать протокол недействительным нельзя). Функционал "Протокол недействителен" - доступен роли "Админ" и роли "Руководитель ИЦ"
            $protocols[$key]['protocol_is_invalid'] = $selected !== $protocol['ID'] ||
                !empty($protocol['INVALID']) || empty($protocol['NUMBER']);
        }

        return $protocols;
    }


    /**
     * записываем информацию для протокола
     * @param array $data
     * @return int
     */
    public function addProtocols(array $data): int
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $result = $this->DB->Insert('PROTOCOLS', $data);

        return intval($result);
    }

    /**
     * @param int $protocolId
     * @param array $data
     * @return mixed
     */
    public function updateProtocolById(int $protocolId, array $data)
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $where = "WHERE ID = {$protocolId}";
        return $this->DB->Update('PROTOCOLS', $data, $where);
    }

    /**
     * TODO: Временно сохраняет сериализованные данные в ba_tz, для работы остальных скриптов до их рефакторинга
     * @param int $dealId
     * @param array $data
     */
    public function updateTzByDealId(int $dealId, array $data)
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $where = "WHERE ID_Z = {$dealId}";
        return $this->DB->Update('ba_tz', $data, $where);
    }

    /**
     * NEW получить данные материала по id ulab_material_to_request
     * @param int $umtr_id - id ulab_material_to_request
     * @return array
     */
    public function materialToRequestData(int $umtr_id): array
    {
        $response = [];

        if (empty($umtr_id) || $umtr_id < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM ulab_material_to_request umtr WHERE umtr.id = {$umtr_id}")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * получить данные материала по id ulab_material_to_request
     * @param int $umtr_id - id ulab_material_to_request
     * @return array
     */
    public function getMaterialToRequestData(int $umtr_id): array
    {
        $response = [];

        if (empty($umtr_id) || $umtr_id < 0) {
            return $response;
        }

        $result = $this->DB->Query(
            "SELECT umtr.id umtr_id, umtr.deal_id, umtr.probe_number, umtr.cipher, umtr.protocol_id,
                umtr.material_id, umtr.material_number, m.ID m_id,
                m.NAME m_mame, ugtp.id ugtp_id, ugtp.method_id, ugtp.conditions_id, ugtp.gost_number, bgm.GOST bgm_gost, bgm.IN_OA bgm_in_oa, bgm.IN_OUT bgm_in_out,
                bgm.IN_OUT bgm_in_out, bgm.GOST_PUNKT bgm_punkt, bgm.NORM_TEXT bgm_norm_text,
                bgm.NORM1_TEXT bgm_norm1_text, bgm.NORM2_TEXT bgm_norm2_text, bgm.NORM1 bgm_norm1,
                bgm.NORM2 bgm_norm2, bgm.GOST_TYPE bgm_gost_type,
                bgm.SPECIFICATION bgm_specification, bgm.ED bgm_ed, bgm.ED_INDEX bgm_ed_index,
                bgc.GOST bgc_gost, bgc.SPECIFICATION bgc_specification, bgc.NORM_DOP bgc_norm_dop, bgc.GOST_TYPE bgc_gost_type,
                bgc.MATCH_MANUAL bgc_match_manual, bgc.NORM_COMMENT bgc_norm_comment, utr.match, utr.actual_value,
                utr.normative_value, p.NUMBER p_number,
                p.EDIT_RESULTS p_edit_results, p.EDIT_RESULTS p_edit_results, p.GROUP_MAT p_group_mat
            FROM ulab_material_to_request umtr
            INNER JOIN MATERIALS m ON m.ID = umtr.material_id
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            INNER JOIN ba_gost bgm ON bgm.ID = ugtp.method_id
            INNER JOIN ba_gost bgc ON bgc.ID = ugtp.conditions_id
            LEFT JOIN ulab_trial_results utr ON utr.gost_to_probe_id = ugtp.id
            LEFT JOIN PROTOCOLS p ON p.ID = umtr.protocol_id
            WHERE umtr.id = {$umtr_id}"
        )->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * @param int $protocolId
     * @return array
     */
    public function getMaterialToRequestByProtocolId(int $protocolId): array
    {
        $response = [];

        if (empty($protocolId) || $protocolId < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT umtr.* FROM `ulab_material_to_request` as umtr, `ulab_gost_to_probe` as ugtp WHERE ugtp.material_to_request_id = umtr.id and (umtr.`protocol_id` = {$protocolId} or ugtp.`protocol_id` = {$protocolId})");

        while ($row = $result->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    /**
     * @param int $umtr_id - id ulab_material_to_request
     * @param array $data
     * @return mixed
     */
    public function updateMaterialToRequest(int $umtr_id, array $data)
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $where = "WHERE id = {$umtr_id}";
        return $this->DB->Update('ulab_material_to_request', $data, $where);
    }

    /**
     * @param int $protocolId
     * @param array $data
     * @return mixed
     */
    public function updateMaterialToRequestByProtocolId(int $protocolId, array $data)
    {
        foreach ($data as $key => $item) {
            if (is_string($item)) {
                $data[$key] = $this->quoteStr($this->DB->ForSql(trim($item)));
            }
        }

        $where = "WHERE protocol_id = {$protocolId}";
        $this->DB->Update('ulab_material_to_request', $data, $where);
        $this->DB->Update('ulab_gost_to_probe', $data, $where);
    }

    /**
     * получить данные результатов испытаний по id таблицы ulab_gost_to_probe
     * @param int $ugtp_id - id ulab_gost_to_probe
     * @return array
     */
    public function getTrialResult($ugtp_id): array
    {
        $response = [];

        if (!$ugtp_id || empty($ugtp_id) || $ugtp_id < 0) {
            return $response;
        }

        $result = $this->DB->Query(
            "SELECT utr.*, ugtp.measuring_sheet 
                FROM `ulab_trial_results` as utr, `ulab_gost_to_probe` as ugtp 
                WHERE utr.`gost_to_probe_id` = ugtp.`id` AND ugtp.`id` = {$ugtp_id}"
        );

        while ($row = $result->Fetch()) {
            $response = $row;
            $response['normative_value'] = htmlspecialchars($row['normative_value']);
        }
        return $response;
    }

    /**
     * @deprecated
     * записываем данные результатов испытаний
     * @param array $data
     * @return int
     */
    public function addTrialResults(array $data): int
    {
        $result = $this->DB->Insert('ulab_trial_results', $data);

        return intval($result);
    }

    /**
     * @deprecated
     * обновляем данные результатов испытаний
     * @param int $ugtp_id - id ulab_gost_to_probe
     * @param array $data
     * @return mixed
     */
    public function updateTrialResults(int $ugtp_id, array $data)
    {
        $where = "WHERE gost_to_probe_id = {$ugtp_id}";
        return $this->DB->Update('ulab_trial_results', $data, $where);
    }

    /**
     * Получить данные материалов и гостов
     * @param int $dealId
     * @param bool $isCheck
     * @param int|null $protocolId
     * @param int|null $selectedProtocol
     * @return array
     */
    public function materialGostList(
        int $dealId,
        bool $isCheck = true,
        ?int $protocolId = null,
        ?int $selectedProtocol = null
    ): array {
        $resultModel = new Result;
        $methodsModel = new Methods;
        $labModel = new Lab;
        $protocolModel = new Protocol;
        $techConditionModel = new TechCondition;
        $user = new User;

        $response = [];
        $where = '';

        if (empty($dealId)) {
            return $response;
        }

        if ($protocolId) {
            $where = "AND umtr.protocol_id = {$protocolId}";
        }

        $result = $this->DB->Query("SELECT *, umtr.protocol_id as protocol_id, ugtp.protocol_id as ugtp_protocol_id,
                umtr.id umtr_id, umtr.name_for_protocol, 
                m.ID m_id, m.NAME m_mame, 
                ugtp.id ugtp_id, ugtp.method_id ugtp_method_id, ugtp.assigned_id,
                g.reg_doc g_reg_doc, 
                um.name um_name, um.decimal_places um_decimal_places, um.definition_range_1 um_definition_range_1, 
                um.definition_range_2 um_definition_range_2, um.definition_range_type um_definition_range_type,   
                um.is_text_norm um_is_text_norm, 
                mg.name as group_name, mgt.*
            FROM ulab_material_to_request umtr 
            INNER JOIN MATERIALS as m ON m.ID = umtr.material_id 
            INNER JOIN ulab_gost_to_probe as ugtp ON ugtp.material_to_request_id = umtr.id 
            LEFT JOIN ulab_methods as um ON um.id = ugtp.method_id 
            LEFT JOIN ulab_gost as g ON g.id = um.gost_id 
            LEFT JOIN ulab_dimension as d on d.id = um.unit_id
            LEFT JOIN materials_groups as mg on mg.id = umtr.group 
            LEFT JOIN materials_groups_tu as mgt on mg.id = mgt.materials_groups_id and mgt.tu_id = ugtp.conditions_id
            WHERE umtr.deal_id = {$dealId} AND um.is_selection <> 1 {$where} ORDER BY umtr.id, ugtp.gost_number"); //, umtr.material_number, umtr.probe_number

        $prevMaterialNumber = 0;
        $prevProbeNumber = 0;
        while ($row = $result->Fetch()) {
            if (empty($row['protocol_id']) && $row['ugtp_protocol_id']) {
                $row['protocol_id'] = $row['ugtp_protocol_id'];
            }

            $row['rooms'] = $labModel->getGostRoom($row['ugtp_id']);
            $row['start_trials'] = $resultModel->getStateLastAction($row['ugtp_id']);
            $row['labs'] = $methodsModel->getLab($row['ugtp_method_id']);
            $row['protocol'] = !empty($row['protocol_id']) ? $protocolModel->getProtocolById($row['protocol_id']) : [];
            $row['measurement'] = $this->getMeasurement($row['measurement_id']);
            $row['trial_results'] = $this->getTrialResult($row['ugtp_id']);
            $row['tech'] = $techConditionModel->get($row['conditions_id']);

            if (!empty($row['assigned_id'])) {
                $row['tester'] = $user->getUserShortById($row['assigned_id']);
            }

            $row['rooms_name'] = implode('<br>', array_column($row['rooms'], 'name'));
            $row['measuring_sheet'] = json_decode($row['measuring_sheet'], true);

            // Разделение проб и материалов (проба - одинарная толстая линия, материал - двойная толстая линия)
            $row['border_row'] = $row['material_number'] !== $prevMaterialNumber ? 'material-border-top' : ($row['probe_number'] !== $prevProbeNumber ? 'probe-border-top' : '');
            $prevMaterialNumber = $row['material_number'];
            $prevProbeNumber = $row['probe_number'];

            // Единицы измерения
            $row['units'] = $row['unit_rus'];

            // Если методика "В области аккредитации" и "Факт. значения текстом" и не подтверждено значение в ОА, то подтверждать
            $row['confirm_oa'] = !empty($row['in_field']) && !empty($row['is_text_fact']) && empty($row['is_confirm_oa']);
            // Если подтверждено значение в ОА, то запрет на редактирование
            $row['confirm_oa_readonly'] = !empty($row['in_field']) && !empty($row['is_text_fact']) && !empty($row['is_confirm_oa']);

            // ОА (Если "Входящий диапазон" то "от - до")
            $row['range_ao'] = $row['um_definition_range_type'] == 1 ?
                'от ' . ($row['um_definition_range_1'] ?? '-') . ' до ' . ($row['um_definition_range_2'] ?? '-') : ($row['um_definition_range_type'] == 2 ? 'до ' . ($row['um_definition_range_1'] ?? '-') . ' и от ' . ($row['um_definition_range_2'] ?? '-') : '');



            // "Выбранные пробы" (если к пробе привязан протокол и протокол не выбран ИЛИ
            // если к пробе привязан протокол с номером и протокол не разблокирован,
            // то открепить пробы нельзя)
            $row['probe_selected'] =
                !empty($row['protocol_id']) && $row['protocol_id'] != $selectedProtocol ||
                !empty($row['protocol']['NUMBER']) && empty($row['protocol']['EDIT_RESULTS']);

            // Отображение прикреплённых проб к выбранному протоколу
            $row['table_green'] =
                !empty($row['protocol_id']) && $row['protocol_id'] == $selectedProtocol ? 'table-gradient-green' : '';

            // "Испытание" (если есть номер протокола и протокол не разблокирован,
            // то начать или возобновить или завершить испытание неользя)
            $row['trial'] = !empty($row['protocol']['NUMBER']) && empty($row['protocol']['EDIT_RESULTS']);

            // Лист измерения (если есть номер протокола и протокол не разблокирован ИЛИ
            // для текущей методики отсутствует лист измерения, то лист измерения не доступен)
            $row['measurement_sheet'] =
                !empty($row['protocol']['NUMBER']) && empty($row['protocol']['EDIT_RESULTS']) || empty($row['measurement']['name']);
            $row['sheet_title'] =
                empty($row['measurement']['name']) ? 'Для текущей методике отсутствует или не выбран лист измерения' : (!empty($row['protocol']['NUMBER']) && empty($row['protocol']['EDIT_RESULTS']) ?
                    'Лист измерения недоступен, у протокола есть номер и протокол не разблокирован' : '');

            if ($isCheck) {
                // Нормативное значение
                $normativeData = $this->getNormativeData($row);
                $row = array_merge($row, $normativeData);

                // Фактическое значение
                $actualData = $this->getActualData($row);
                $row = array_merge($row, $actualData);

                // Соответствие требованиям
                $matchData = $this->getMatchData($row);
                $row = array_merge($row, $matchData);

                // В ОА
                $actualValue = $row['actual_value'];
                $isConfirmOa = $row['is_confirm_oa'] ?? 0; // Фактическое значение подтверждено что в ОА?
                $attestatData = $this->getAttestatData($row, $actualValue, $isConfirmOa);
                $row = array_merge($row, $attestatData);
            }

            if (!empty($row['group'] && ($row['val_1'] != '' || $row['val_2'] != ''))) {
                $ex = [
                    'less_or_equal' => 'до',
                    'more_or_equal' => 'от',
                    'more' => 'более',
                    'less' => 'менее',
                ];

                $val1 = $row['val_1'] != '' ? $ex[$row['comparison_val_1']] . ' ' . $row['val_1'] : '';
                $val2 = $row['val_2'] != '' ? $ex[$row['comparison_val_2']] . ' ' . $row['val_2'] : '';

                $row['normative_value'] = $val1 . ' ' . $val2;
                $row['normative_message'] = 'группа материала ' . $row['group_name'];
            }

            $response[$row['umtr_id']][$row['ugtp_id']] = $row;
        }

        return $response;
    }


    /**
     * @param $filter
     * @return array
     */
    public function getMethodProbeJournal($filter)
    {
        $methodModel = new Methods();
        $tuModel = new TechCondition();
        $protocolModel = new Protocol();
        $userModel = new User();

        $where = "";
        $limit = "";
        $order = [
            //'mtr.deal_id desc, gtp.method_id asc, gtp.gost_number asc, mtr.material_number asc, mtr.probe_number asc',
            'mtr.deal_id desc, gtp.method_id asc, mtr.probe_number asc, mtr.cipher asc, gtp.gost_number asc, mtr.material_number asc',
            'mtr.deal_id desc, mtr.material_number asc, mtr.probe_number asc, gtp.method_id asc, gtp.gost_number asc',
        ];
        $whereDealId = "mtr.deal_id";
        if (!isset($filter['order'])) {
            $filter['order'] = 0;
        }

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // ид заявки
                if (isset($filter['search']['deal_id'])) {
                    $whereDealId = "mtr.deal_id = {$filter['search']['deal_id']}";
                }
                // ид материалов из таблицы materials
                if (isset($filter['search']['material_id'])) {
                    $str = implode(',', $filter['search']['material_id']);
                    $where .= "mtr.material_id in ({$str}) AND ";
                }
                if (isset($filter['search']['method_id'])) {
                    $str = implode(',', $filter['search']['method_id']);
                    $where .= "gtp.method_id in ({$str}) AND ";
                }
                if (isset($filter['search']['probe_id'])) {
                    $str = implode(',', $filter['search']['probe_id']);
                    $where .= "mtr.id in ({$str}) AND ";
                }
                if (isset($filter['search']['lab_id'])) {
                    $str = implode(',', $filter['search']['lab_id']);
                    $where .= "uml.lab_id in ({$str}) AND ";
                }
                if (isset($filter['search']['protocol_id'])) {
                    $where .= "gtp.protocol_id = {$filter['search']['protocol_id']} AND ";
                }
                if (isset($filter['search']['assigned_id'])) {
                    $str = implode(',', $filter['search']['assigned_id']);
                    $where .= "gtp.assigned_id in ({$str}) AND ";
                    //                    if ( $filter['search']['assigned_id'] == -1 ) {
                    //                        $where .= "(gtp.assigned_id = '' or gtp.assigned_id is null) AND ";
                    //                    } else {
                    //                        $where .= "gtp.assigned_id = {$filter['search']['assigned_id']} AND ";
                    //                    }
                }
                if (isset($filter['search']['deal_title'])) {
                    $where .= "tz.REQUEST_TITLE like '%{$filter['search']['deal_title']}%' and ";
                }
                if (isset($filter['search']['cipher'])) {
                    $where .= "mtr.cipher like '%{$filter['search']['cipher']}%' and ";
                } else {
                    //                    $where .= "mtr.cipher <> '' and ";
                    $where .= "";
                }
                if (isset($filter['search']['status'])) {
                    $arrStartWhere = [
                        '',
                        "gtp.id not in (select ugtp_id from ulab_start_trials)",
                        "'complete' not in (select state from ulab_start_trials where ugtp_id = gtp.id) and ('start' in (select state from ulab_start_trials where ugtp_id = gtp.id) or 'pause' in (select state from ulab_start_trials where ugtp_id = gtp.id))",
                        "'complete' in (select state from ulab_start_trials where ugtp_id = gtp.id)",
                    ];
                    $where .= "{$arrStartWhere[$filter['search']['status']]} and ";
                }
            }

            if ($filter['order'] == 1) {
                $where .= "gtp.assigned_id > 0 AND ";
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
        $where .= "1 ";

        $data = $this->DB->Query(
            "SELECT 
                        gtp.*, gtp.id as ugtp_id, gtp.protocol_id as gtp_protocol_id, gtp.assigned_id, 
                        mtr.*, mtr.id as mtr_id, mtr.id as umtr_id, 
                        m.ID as material_id, m.NAME as material_name,
                        mtr.deal_id, tz.REQUEST_TITLE, mg.name as material_group_name
                    FROM ulab_material_to_request as mtr
                    inner join MATERIALS as m ON m.ID = mtr.material_id
                    left join materials_groups as mg ON mtr.group = mg.id
                    inner join ulab_gost_to_probe as gtp on gtp.material_to_request_id = mtr.id
                    inner join ba_tz as tz on tz.ID_Z = mtr.deal_id
                    inner join ulab_methods as method on method.id = gtp.method_id
                    left join ulab_methods_lab as uml on uml.method_id = method.id
                    WHERE method.is_selection = 0 and {$whereDealId} and {$where}
                    group by gtp.id, mtr.id
                    ORDER BY {$order[$filter['order']]} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT 
                        gtp.method_id, mtr.id
                    FROM ulab_material_to_request as mtr
                    inner join MATERIALS as m ON m.ID = mtr.material_id
                    inner join ulab_gost_to_probe as gtp on gtp.material_to_request_id = mtr.id
                    inner join ulab_methods as method on method.id = gtp.method_id
                    left join ulab_methods_lab as uml on uml.method_id = method.id
                    WHERE method.is_selection = 0 and {$whereDealId}
                    group by gtp.id, mtr.id"
        )->SelectedRowsCount();

        $dataFiltered = $this->DB->Query(
            "SELECT 
                        gtp.method_id, mtr.id
                    FROM ulab_material_to_request as mtr
                    inner join MATERIALS as m ON m.ID = mtr.material_id
                    inner join ulab_gost_to_probe as gtp on gtp.material_to_request_id = mtr.id
                    inner join ba_tz as tz on tz.ID_Z = mtr.deal_id
                    inner join ulab_methods as method on method.id = gtp.method_id
                    left join ulab_methods_lab as uml on uml.method_id = method.id
                    WHERE method.is_selection = 0 and {$whereDealId} and {$where}
                    group by gtp.id, mtr.id"
        )->SelectedRowsCount();

        $result = [];

        while ($row = $data->Fetch()) {

            if (!empty($row['material_group_name'])) {
                $row['material_name'] .= " ({$row['material_group_name']})";
            }

            $row['material_cipher'] = "{$row['material_name']} {$row['material_group_name']} ({$row['name_for_protocol']}) | {$row['cipher']}";

            if (!empty($row['assigned_id'])) {
                $row['assigned_user'] = $userModel->getUserData($row['assigned_id'])['short_name'];
            } else {
                $row['assigned_user'] = "Не назначен";
            }

            $methodInfo = $methodModel->get($row['method_id']);
            $row = array_merge($row, $methodInfo);
            $row['um_definition_range_type'] = $methodInfo['definition_range_type'];
            $row['um_definition_range_1'] = $methodInfo['definition_range_1'];
            $row['um_definition_range_2'] = $methodInfo['definition_range_2'];

            $tuInfo = $tuModel->get($row['tech_condition_id']);
            $row['utc_definition_range_type'] = $tuInfo['definition_range_type'];
            $row['utc_definition_range_1'] = $tuInfo['definition_range_1'];
            $row['utc_definition_range_2'] = $tuInfo['definition_range_2'];

            // Единицы измерения
            $row['units'] = $methodInfo['unit_rus'];
            $row['view_gost'] = $methodInfo['view_gost'];
            $row['tu_name'] = $tuInfo['reg_doc'];
            $row['measurement'] = $this->getMeasurement($methodInfo['measurement_id']);
            $row['state_action'] = $this->getStateLastAction($row['ugtp_id']);
            $row['protocol'] = !empty($row['gtp_protocol_id']) ? $protocolModel->getProtocolById($row['gtp_protocol_id']) : [];
            $row['in_field'] = $methodInfo['in_field'];

            $row['measuring_sheet'] = json_decode($row['measuring_sheet'], true);

            $row['selected_protocol_id'] = $filter['search']['selected_protocol_id'];

            $row['tech'] = $tuInfo;

            // Если методика "В области аккредитации" и "Факт. значения текстом" и не подтверждено значение в ОА, то подтверждать
            $row['confirm_oa'] = !empty($methodInfo['in_field']) && !empty($methodInfo['is_text_fact']) && empty($row['is_confirm_oa']);
            // Если подтверждено значение в ОА, то запрет на редактирование
            $row['confirm_oa_readonly'] = !empty($methodInfo['in_field']) && !empty($methodInfo['is_text_fact']) && !empty($row['is_confirm_oa']);

            // ОА (Если "Входящий диапазон" то "от - до")
            $row['range_ao'] = $row['um_definition_range_type'] == 1 ?
                'от ' . ($row['um_definition_range_1'] ?? '-') . ' до ' . ($row['um_definition_range_2'] ?? '-') : ($row['um_definition_range_type'] == 2 ? 'до ' . ($row['um_definition_range_1'] ?? '-') . ' и от ' . ($row['um_definition_range_2'] ?? '-') : '');

            // "Испытание" (если есть номер протокола и протокол не разблокирован,
            // то начать или возобновить или завершить испытание неользя)
            $row['trial'] = !empty($row['protocol']['NUMBER']) && empty($row['protocol']['EDIT_RESULTS']);

            // Лист измерения (если есть номер протокола и протокол не разблокирован ИЛИ
            // для текущей методики отсутствует лист измерения, то лист измерения не доступен)
            $row['measurement_sheet'] =
                !empty($row['protocol']['NUMBER']) && empty($row['protocol']['EDIT_RESULTS']) || empty($row['measurement']['name']);
            $row['sheet_title'] =
                empty($row['measurement']['name']) ? 'Для текущей методике отсутствует или не выбран лист измерения' : (!empty($row['protocol']['NUMBER']) && empty($row['protocol']['EDIT_RESULTS']) ?
                    'Лист измерения недоступен, у протокола есть номер и протокол не разблокирован' : '');

            if (true) {
                // Нормативное значение
                $normativeData = $this->getNormativeData($row);
                $row = array_merge($row, $normativeData);

                // Фактическое значение
                $actualData = $this->getActualData($row);
                $row = array_merge($row, $actualData);

                // Соответствие требованиям
                $matchData = $this->getMatchData($row);
                $row = array_merge($row, $matchData);

                // В ОА
                $actualValue = $row['actual_value'];
                $isConfirmOa = $row['is_confirm_oa'] ?? 0; // Фактическое значение подтверждено что в ОА?
                $attestatData = $this->getAttestatData($row, $actualValue, $isConfirmOa);
                $row = array_merge($row, $attestatData);
            }

            if (!empty($row['group'] && (!empty($row['val_1']) || $row['val_1'] == '0'))) {
                $ex = [
                    'less_or_equal' => 'до',
                    'more_or_equal' => 'от',
                    'more' => 'более',
                    'less' => 'менее',
                ];

                $row['normative_value'] = $ex[$row['comparison_val_1']] . ' ' . $row['val_1'] . ' ' . $ex[$row['comparison_val_2']] . ' ' . $row['val_2'];
                $row['normative_message'] = 'группа материала ' . $row['group_name'];
            }

            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }



    /**
     * @deprecated
     * получает данные материалов, методик и тех.условий
     * @param int $dealId
     * @return array
     */
    public function materialGostDataByDealId(int $dealId): array
    {
        $resultModel = new Result;
        $labModel = new Lab;
        $user = new User();

        $response = [];

        if (empty($dealId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT *, 
                umtr.id umtr_id, 
                m.ID m_id, m.NAME m_mame, 
                ugtp.id ugtp_id, ugtp.method_id ugtp_method_id, ugtp.assigned_id, 
                g.reg_doc g_reg_doc, 
                um.name um_name, um.decimal_places um_decimal_places, um.definition_range_1 um_definition_range_1, 
                um.definition_range_2 um_definition_range_2, um.definition_range_type um_definition_range_type,   
                um.is_text_norm um_is_text_norm, um.clause um_clause,    
                utc.reg_doc utc_reg_doc, utc.type utc_type, utc.definition_range_1 utc_definition_range_1, 
                utc.definition_range_2 utc_definition_range_2, utc.definition_range_type utc_definition_range_type, 
                p.NUMBER p_number, p.EDIT_RESULTS p_edit_results, p.GROUP_MAT p_group_mat, utr.normative_value,
                mt.name mt_name, mt.id mt_id    
            FROM ulab_material_to_request umtr 
            INNER JOIN MATERIALS as m ON m.ID = umtr.material_id 
            INNER JOIN ulab_gost_to_probe as ugtp ON ugtp.material_to_request_id = umtr.id 
            LEFT JOIN ulab_methods as um ON um.id = ugtp.method_id 
            LEFT JOIN ulab_gost as g ON g.id = um.gost_id 
            LEFT JOIN ulab_dimension as d on d.id = um.unit_id 
            LEFT JOIN ulab_methods_lab as uml on uml.method_id = um.id 
            LEFT JOIN ulab_tech_condition as utc ON utc.id = ugtp.conditions_id 
            LEFT JOIN ulab_trial_results as utr ON utr.gost_to_probe_id = ugtp.id 
            LEFT JOIN ulab_measurement as mt ON mt.id = um.measurement_id 
            LEFT JOIN PROTOCOLS as p ON p.ID = umtr.protocol_id 
            WHERE umtr.deal_id = {$dealId} AND um.is_selection <> 1 ORDER BY material_number ASC, probe_number ASC");


        while ($row = $result->Fetch()) {
            $rooms = $labModel->getGostRoom($row['ugtp_id']);
            $row['start_trials'] = $resultModel->getStateLastAction($row['ugtp_id']);

            if (!empty($row['assigned_id'])) {
                $row['tester'] = $user->getUserShortById($row['assigned_id']);
            }


            $row['rooms'] = implode('<br>', array_column($rooms, 'name'));
            $row['measuring_sheet'] = json_decode($row['measuring_sheet'], true);

            //Единицы измерения
            $row['units'] = $row['unit_rus'];

            if (!empty($row['assigned_id'])) {
                $row['tester'] = $user->getUserShortById($row['assigned_id']);
            }

            //Фактическое значение
            $row['actual_value'] = json_decode($row['actual_value'], true);
            if (isset($row['measuring_sheet']['result_value'])) {
                $row['actual_value'][0] = $row['measuring_sheet']['result_value'];
            }


            //Нормативное значение
            $row['dop_norm'] = !empty($row['dop_norm']) ? unserialize($row['dop_norm']) : [];
            $row['dop_value'] = !empty($row['dop_value']) ? unserialize($row['dop_value']) : [];
            $numGroupMat = explode('-', $row['p_group_mat'])[1] ?? null;
            $norms = "от {$row['dop_norm'][$numGroupMat][0]} до {$row['dop_norm'][$numGroupMat][1]}";

            //Если НЕ Ручное управление "соотв/не соотв" в ТУ, то берём диапазон
            if ($row['is_manual']) {
                $row['view_normative_value'] = html_entity_decode($row['normative_value']);
            } else {
                $norm = '';
                if ($row['utc_definition_range_type'] == 1) {
                    $norm = "от {$row['utc_definition_range_1']} до {$row['utc_definition_range_2']}";
                } elseif ($row['utc_definition_range_type'] == 2) {
                    $norm = "до {$row['utc_definition_range_1']} от {$row['utc_definition_range_2']}";
                } elseif ($row['utc_definition_range_type'] == 3) {
                    $norm = "-";
                }

                $row['view_normative_value'] = $norm;
            }


            //Фактическое значение
            $row['actual_value'][0] = isset($row['actual_value'][0]) ? str_replace(',', '.', $row['actual_value'][0]) : null;
            $row['out_range'] = '';


            if (
                $row['lab_id'] == 1 && isset($row['actual_value'][0]) && is_numeric($row['actual_value'][0]) &&
                is_numeric($row['um_definition_range_1']) && is_numeric($row['um_definition_range_2'])
            ) {
                if ($row['um_definition_range_type'] == 1) {
                    if ($row['actual_value'][0] < $row['um_definition_range_1']) {
                        $row['out_range'] = 'менее ' . $row['um_definition_range_1'];
                    } elseif ($row['actual_value'][0] > $row['um_definition_range_2']) {
                        $row['out_range'] = 'болee ' . $row['um_definition_range_2'];
                    }
                } else if ($row['um_definition_range_type'] == 2) {
                    if ($row['actual_value'][0] > $row['um_definition_range_1']) {
                        $row['out_range'] = 'более ' . $row['um_definition_range_1'];
                    } elseif ($row['actual_value'][0] < $row['um_definition_range_2']) {
                        $row['out_range'] = 'менее ' . $row['um_definition_range_2'];
                    }
                }
            }


            //Соответствие требованиям
            if (!empty($row['protocol_id'])) {
                if (!empty($row['in_field'])) {
                    // если нормы НЕ текстом в методике, то проверяем на соответсвие диапазона
                    if (empty($row['um_is_text_norm'])) {
                        if (is_numeric($row['actual_value'][0])) {
                            if (($row['actual_value'][0] < $row['um_definition_range_1'] || $row['actual_value'][0] > $row['um_definition_range_2'])
                                && $row['um_definition_range_type'] == 1
                            ) { // Входящий(внутренний) диапазон 1
                                $row['match_message'] = 'Внимание! Значение вне области!';
                                $row['no_oa'][$row['protocol_id']] = 1;
                            }

                            if (($row['actual_value'][0] > $row['um_definition_range_1'] && $row['actual_value'][0] < $row['um_definition_range_2']) &&
                                $row['um_definition_range_type'] == 2
                            ) { // Исходящий(внешний) диапазон 2
                                if (!empty($row['in_field']) && !empty($row['protocol_id'])) {
                                    $row['match_message'] = 'Внимание! Значение вне области!';
                                    $row['no_oa'][$row['protocol_id']] = 1;
                                }
                            }
                        } else { // если методика не в области
                            $row['match_message'] = 'Внимание! Значение вне области!';
                            $row['no_oa'][$row['protocol_id']] = 1; // Без аттестата и в не диапазона
                        }
                    }
                } else { // если методика не в области
                    $row['no_oa'][$row['protocol_id']] = 1; // Без аттестата и в не диапазона
                }
            }

            switch ($row['match']) {
                case 0:
                    $row['match_view'] = 'Не соответствует';
                    break;
                case 1:
                    $row['match_view'] = 'Соответствует';
                    break;
                case 2:
                    $row['match_view'] = '-';
                    break;
                case 3:
                    $row['match_view'] = 'Не нормируется';
                    break;
                default:
                    $row['match_view'] = '';
            }


            //ОА (Если "Входящий диапазон" то "от - до")
            $row['range_title'] = $row['um_definition_range_type'] == 1 ?
                'от ' . ($row['um_definition_range_1'] ?: '-') . ' до ' . ($row['um_definition_range_2'] ?: '-') : ($row['um_definition_range_type'] == 2 ? 'до ' . ($row['um_definition_range_1'] ?: '-') . ' и от ' . ($row['um_definition_range_2'] ?: '-') : '');

            if ($row['um_definition_range_type'] == 1 && empty($row['in_field']) && !empty($row['protocol_id'])) {
                $row['no_oa'][$row['protocol_id']] = 1;
            }

            $response[$row['umtr_id']][$row['ugtp_id']] = $row;
        }

        return $response;
    }

    /**
     * получает данные материалов, методик и тех.условий
     * @param int $dealId
     * @return array
     */
    public function getMaterialGostDataByDealId(int $dealId): array
    {
        $user = new User();

        $response = [];

        if (empty($dealId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT
                umtr.id umtr_id, umtr.deal_id, umtr.probe_number, umtr.cipher, umtr.protocol_id,
                m.ID m_id, m.NAME m_mame,
                ugtp.id ugtp_id, ugtp.method_id, ugtp.conditions_id, ugtp.gost_number, ugtp.assigned_id,
                bgm.GOST bgm_gost, bgm.IN_OA bgm_in_oa, bgm.IN_OUT bgm_in_out,
                bgm.IN_OUT bgm_in_out, bgm.GOST_PUNKT bgm_punkt, bgm.NORM_TEXT bgm_norm_text,
                bgm.NORM1_TEXT bgm_norm1_text, bgm.NORM2_TEXT bgm_norm2_text, bgm.NORM1 bgm_norm1,
                bgm.NORM2 bgm_norm2, bgm.GOST_TYPE bgm_gost_type,
                bgm.SPECIFICATION bgm_specification, bgm.ED bgm_ed, bgm.ED_INDEX bgm_ed_index, bgm.RES_TEXT bgm_res_text,
                bgm.LFHI bgm_lfhi, bgm.ACCURACY bgm_accuracy,
                bgc.GOST bgc_gost, bgc.SPECIFICATION bgc_specification, bgc.NORM_DOP bgc_norm_dop, bgc.GOST_TYPE bgc_gost_type,
                bgc.MATCH_MANUAL bgc_match_manual, bgc.NORM_COMMENT bgc_norm_comment, bgc.VALUE_DOP bgc_value_dop,
                utr.match, utr.actual_value, utr.normative_value, utr.average_value,
                p.NUMBER p_number, p.EDIT_RESULTS p_edit_results, p.EDIT_RESULTS p_edit_results, p.GROUP_MAT p_group_mat
            FROM ulab_material_to_request umtr
            INNER JOIN MATERIALS m ON m.ID = umtr.material_id
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            INNER JOIN ba_gost bgm ON bgm.ID = ugtp.method_id
            INNER JOIN ba_gost bgc ON bgc.ID = ugtp.conditions_id
            LEFT JOIN ulab_trial_results utr ON utr.gost_to_probe_id = ugtp.id
            LEFT JOIN PROTOCOLS p ON p.ID = umtr.protocol_id
            WHERE umtr.deal_id = {$dealId} AND bgm.GOST_TYPE <> 'metodic_otbor'");

        while ($row = $result->Fetch()) {
            if (
                empty($row['bgm_in_oa']) && $row['bgm_gost_type'] !== 'metodic_otbor' &&
                !in_array($row['method_id'], [2875, 3376]) && !empty($row['protocol_id'])
            ) {
                $row['no_oa'][$row['protocol_id']] = 1;
            }

            if (!empty($row['assigned_id'])) {
                $row['tester'] = $user->getUserShortById($row['assigned_id']);
            }

            //Единицы измерения
            $row['units'] = $row['bgm_ed'] . ($row['bgm_ed_index'] ? "<sup>{$row['bgm_ed_index']}</sup>" : '');


            //Фактическое значение
            $row['actual_value'] = json_decode($row['actual_value'], true);


            //Нормативное значение
            $bgcNormDop = !empty($row['bgc_norm_dop']) ? unserialize($row['bgc_norm_dop']) : [];
            $numGroupMat = explode('-', $row['p_group_mat'])[1] ?? null;
            $norms = "от {$bgcNormDop[$numGroupMat][0]} до {$bgcNormDop[$numGroupMat][1]}";

            if ($row['bgc_gost_type'] === 'TU_group') { //Методика определения группы материала
                $row['view_normative_value'] = $norms;
            } elseif ($row['bgc_gost_type'] === 'TU_research') { //Методика исследования по группе материала
                $row['view_normative_value'] = $row['normative_value'];
            } else {
                $row['view_normative_value'] = $row['bgc_norm_comment'] ?: '';
            }


            //Фактическое значение
            $row['bgm_norm_1'] = $row['bgm_norm_text'] ? $row['bgm_norm1_text'] : $row['bgm_norm1'];
            $row['bgm_norm_2'] = $row['bgm_norm_text'] ? $row['bgm_norm2_text'] : $row['bgm_norm2'];
            $row['actual_value'][0] = isset($row['actual_value'][0]) ? str_replace(',', '.', $row['actual_value'][0]) : null;
            $row['out_range'] = '';


            if (
                !empty($row['bgm_lfhi']) && isset($row['actual_value'][0]) && is_numeric($row['actual_value'][0]) &&
                is_numeric($row['bgm_norm_1']) && is_numeric($row['bgm_norm_2'])
            ) {
                if (!empty($row['bgm_in_out'])) {
                    if ($row['actual_value'][0] < $row['bgm_norm_1']) {
                        $row['out_range'] = 'менее ' . $row['bgm_norm_1'];
                    } elseif ($row['actual_value'][0] > $row['bgm_norm_2']) {
                        $row['out_range'] = 'болee ' . $row['bgm_norm_2'];
                    }
                } else {
                    if ($row['actual_value'][0] > $row['bgm_norm_1']) {
                        $row['out_range'] = 'более ' . $row['bgm_norm_1'];
                    } elseif ($row['actual_value'][0] < $row['bgm_norm_2']) {
                        $row['out_range'] = 'менее ' . $row['bgm_norm_2'];
                    }
                }
            }


            //Среднее значение
            if (in_array($row['bgm_gost_type'], ['TU_sred5', 'TU_sred4', 'TU_sred3', 'TU_sred2', 'TU_sred'])) {
                $row['view_average_value'] = is_nan($row['average_value']) ? '-' : number_format($row['average_value'], $row['bgm_accuracy'], ',', '');
            } else {
                $row['view_average_value'] = 'Не рассчитывается';
            }


            //Соответствие требованиям
            //            if ($row['bgm_res_text']) {
            //                $actualValue = preg_replace("/[^,.0-9]/", '', $row['actual_value'][0]);
            //
            //                if (($actualValue < $row['bgm_norm1'] || $actualValue > $row['bgm_norm2']) && $row['bgm_in_out']) {
            //                    $row['match_message'] = 'Внимание! Значение вне области!';
            //
            //                    if (!empty($row['bgm_in_oa']) && !empty($row['protocol_id'])) {
            //                        $row['no_oa'][$row['protocol_id']] = 1;
            //                    }
            //                } elseif (($actualValue > $row['bgm_norm1'] || $actualValue < $row['bgm_norm2']) && empty($row['bgm_in_out'])) {
            //                    $row['match_message'] = 'Внимание! Значение вне области!';
            //
            //                    if (!empty($row['bgm_in_oa']) && !empty($row['protocol_id'])) {
            //                        $row['no_oa'][$row['protocol_id']] = 1;
            //                    }
            //                }
            //            } else {
            if (empty($row['bgm_norm_text']) && $row['average_value'] !== null) {
                if ($row['method_id'] === 6402) {
                    //TODO: а если несколько фактических значений ?
                    $row['average_value'] = explode('F', $row['actual_value'][0])[1];
                }

                if (($row['average_value'] < $row['bgm_norm1'] || $row['average_value'] > $row['bgm_norm2']) && $row['bgm_in_out']) {
                    $row['match_message'] = 'Внимание! Значение вне области!';

                    if (!empty($row['bgm_in_oa']) && !empty($row['protocol_id'])) {
                        $row['no_oa'][$row['protocol_id']] = 1;
                    }
                } elseif (($row['average_value'] > $row['bgm_norm1'] || $row['average_value'] < $row['bgm_norm2'])
                    && empty($row['bgm_in_out'])
                ) {
                    $row['match_message'] = 'Внимание! Значение вне области!';

                    if (!empty($row['bgm_in_oa']) && !empty($row['protocol_id'])) {
                        $row['no_oa'][$row['protocol_id']] = 1;
                    }
                }
            }
            //            }


            switch ($row['match']) {
                case 0:
                    $row['match_view'] = 'Не соответствует';
                    break;
                case 1:
                    $row['match_view'] = 'Соответствует';
                    break;
                case 2:
                    $row['match_view'] = '-';
                    break;
                case 3:
                    $row['match_view'] = 'Не нормируется';
                    break;
                default:
                    $row['match_view'] = '';
            }


            //ОА (Если "Входящий диапазон" то "от - до")
            $row['range_title'] = $row['bgm_in_out'] ?
                'от ' . ($row['bgm_norm_1'] ?: '-') . ' до ' . ($row['bgm_norm_2'] ?: '-') :
                'до ' . ($row['bgm_norm_1'] ?: '-') . ' и от ' . ($row['bgm_norm_2'] ?: '-');

            if (!empty($row['bgm_in_out']) && empty($row['bgm_in_oa']) && !empty($row['protocol_id'])) {
                $row['no_oa'][$row['protocol_id']] = 1;
            }


            $response[$row['umtr_id']][$row['ugtp_id']] = $row;
        }

        return $response;
    }

    /**
     * NEW
     * @param int $ugtp_id
     * @return array
     */
    public function materialToRequestByUgtpId(int $ugtp_id): array
    {
        $response = [];

        if (empty($ugtp_id)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT *, 
                umtr.id umtr_id, 
                m.ID m_id, m.NAME m_mame, 
                ugtp.id ugtp_id, ugtp.method_id ugtp_method_id, 
                g.reg_doc g_reg_doc, 
                um.name um_name, um.decimal_places um_decimal_places, um.definition_range_1 um_definition_range_1, 
                um.definition_range_2 um_definition_range_2, um.definition_range_type um_definition_range_type, 
                um.is_text_norm um_is_text_norm,  
                utc.reg_doc utc_reg_doc, utc.type utc_type, utc.definition_range_1 utc_definition_range_1, 
                utc.definition_range_2 utc_definition_range_2, utc.definition_range_type utc_definition_range_type, 
                p.NUMBER p_number, p.EDIT_RESULTS p_edit_results, p.GROUP_MAT p_group_mat  
            FROM ulab_material_to_request umtr 
            INNER JOIN MATERIALS as m ON m.ID = umtr.material_id 
            INNER JOIN ulab_gost_to_probe as ugtp ON ugtp.material_to_request_id = umtr.id 
            LEFT JOIN ulab_methods as um ON um.id = ugtp.method_id 
            LEFT JOIN ulab_gost as g ON g.id = um.gost_id 
            LEFT JOIN ulab_dimension as d on d.id = um.unit_id 
            LEFT JOIN ulab_methods_lab as uml on uml.method_id = um.id 
            LEFT JOIN ulab_tech_condition as utc ON utc.id = ugtp.conditions_id 
            LEFT JOIN ulab_trial_results as utr ON utr.gost_to_probe_id = ugtp.id 
            LEFT JOIN PROTOCOLS as p ON p.ID = umtr.protocol_id 
            WHERE ugtp.id = {$ugtp_id} ORDER BY material_number ASC, probe_number ASC")->Fetch();

        if (!empty($result)) {
            $result['measuring_sheet'] = json_decode($result['measuring_sheet'], true);
            $result['dop_norm'] = !empty($result['dop_norm']) ? unserialize($result['dop_norm']) : [];
            $result['dop_value'] = !empty($result['dop_value']) ? unserialize($result['dop_value']) : [];
            $response = $result;
        }

        return $response;
    }

    /**
     * @param int $ugtp_id
     * @return array
     */
    public function getUlabGostToProbeById(int $ugtp_id): array
    {
        $response = [];

        if (empty($ugtp_id)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT umtr.id umtr_id, umtr.deal_id, umtr.probe_number, umtr.cipher, umtr.protocol_id,
            umtr.material_number, m.ID m_id,
            m.NAME m_mame, ugtp.id ugtp_id, ugtp.method_id, ugtp.conditions_id, ugtp.gost_number, bgm.GOST bgm_gost, bgm.IN_OA bgm_in_oa, bgm.IN_OUT bgm_in_out,
            bgm.IN_OUT bgm_in_out, bgm.GOST_PUNKT bgm_punkt, bgm.NORM_TEXT bgm_norm_text,
            bgm.NORM1_TEXT bgm_norm1_text, bgm.NORM2_TEXT bgm_norm2_text, bgm.NORM1 bgm_norm1,
            bgm.NORM2 bgm_norm2, bgm.GOST_TYPE bgm_gost_type,
            bgm.SPECIFICATION bgm_specification, bgm.ED bgm_ed, bgm.ED_INDEX bgm_ed_index,  bgm.RES_TEXT bgm_res_text,
            bgm.LFHI bgm_lfhi, bgm.ACCURACY bgm_accuracy,
            bgc.GOST bgc_gost, bgc.SPECIFICATION bgc_specification, bgc.NORM_DOP bgc_norm_dop, bgc.GOST_TYPE bgc_gost_type,
            bgc.MATCH_MANUAL bgc_match_manual, bgc.NORM_COMMENT bgc_norm_comment, bgc.NORM1 bgc_norm1, bgc.NORM2 bgc_norm2,
            utr.match, utr.actual_value, utr.normative_value, p.NUMBER p_number,
            p.EDIT_RESULTS p_edit_results, p.EDIT_RESULTS p_edit_results, p.GROUP_MAT p_group_mat
            FROM ulab_material_to_request umtr
            INNER JOIN MATERIALS m ON m.ID = umtr.material_id
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            INNER JOIN ba_gost bgm ON bgm.ID = ugtp.method_id
            INNER JOIN ba_gost bgc ON bgc.ID = ugtp.conditions_id
            LEFT JOIN ulab_trial_results utr ON utr.gost_to_probe_id = ugtp.id
            LEFT JOIN PROTOCOLS p ON p.ID = umtr.protocol_id
            WHERE ugtp.id = {$ugtp_id}")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    public function updateResult(array $d, string $where)
    {
        return $this->DB->Update('trial_results', $d, $where);
    }

    /**
     * @param array $data
     * @return int
     */
    public function createResult(array $data): int
    {
        $result = $this->DB->Insert('trial_results', $data);

        return intval($result);
    }

    /**
     * @deprecated
     * @param int $dealId
     * @return array
     */
    public function getCountTrialResults(int $dealId): array
    {
        $response = [];

        if (empty($dealId)) {
            return $response;
        }

        $result = $this->DB->Query("SELECT COUNT(*) count_utr     
            FROM ulab_material_to_request umtr 
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id 
            INNER JOIN ulab_trial_results utr ON utr.gost_to_probe_id = ugtp.id 
            WHERE umtr.deal_id = {$dealId}")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * Проверка, внесёны результаты испытаний или нет
     * @param $dealId
     * @return bool
     */
    public function isResultNotEmpty($dealId)
    {
        $response = false;

        if (empty($dealId)) {
            return $response;
        }

        $sql = $this->DB->Query(
            "SELECT * 
            FROM ulab_material_to_request umtr 
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            INNER JOIN probe_to_lab ptl ON ptl.umtr_id = umtr.id AND ptl.accept_probe = 1
            WHERE umtr.deal_id = {$dealId} 
                AND ((ugtp.actual_value IS NOT NULL AND ugtp.actual_value <> '') OR ugtp.measuring_sheet IS NOT NULL)"
        );

        if ($sql->Fetch()) {
            $response = true;
        }

        return $response;
    }

    /**
     * @param $id
     * @return array
     */
    public function getMeasurement($id)
    {
        $response = [];

        if (empty($id) || $id < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM `ulab_measurement` WHERE id = {$id}")->Fetch();

        if (!empty($result)) {
            $result['initial_data_json'] = $result['initial_data'];
            $result['initial_data'] = json_decode($result['initial_data'], true);
            $response = $result;
        }

        return $response;
    }

    /**
     * @param $data
     * @param $ugtpId
     * @return mixed
     */
    public function saveMeasurementData($data, $ugtpId)
    {

        $unserialized = [];
        parse_str($data, $unserialized);
        foreach ($unserialized['form_data'] as $val) {
        }
        //        $tmp = $unserialized['form_data'];
        //        unset($unserialized['form_data']);
        //        $unserialized = array_merge($unserialized, $tmp);
        $dataJson = json_encode($unserialized, JSON_UNESCAPED_UNICODE);

        $sqlData = [
            'measuring_sheet' => $this->quoteStr($this->DB->ForSql(trim($dataJson)))
        ];

        $where = "WHERE id = {$ugtpId}";
        return $this->DB->Update('ulab_gost_to_probe', $sqlData, $where);
    }


    /**
     * @param $data
     * @param $ugtpId
     * @return bool|int|string
     */
    public function saveMeasurementDataNew($data, $ugtpId)
    {
        //        $this->pre($data);
        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        $sqlData = [
            'measuring_sheet' => $this->quoteStr($this->DB->ForSql(trim($dataJson)))
        ];

        $where = "WHERE id = {$ugtpId}";
        return $this->DB->Update('ulab_gost_to_probe', $sqlData, $where);
    }


    public function grain($ugtp, $methodId)
    {
        $materialModel = new Material();
        $methodModel = new Methods();
        $gostModel = new Gost();

        $material = [];

        $sieve = $materialModel->getZern();

        $material['sieve'] = $sieve;
        $material['method_id'] = $methodId;

        return $material;
    }

    public function asphalt($ugtp, $methodId)
    {
        $materialModel = new Material();
        $methodModel = new Methods();
        $gostModel = new Gost();

        $material = [];

        $sieve = $materialModel->getZern();

        $material['sieve'] = $sieve;
        $material['method_id'] = $methodId;

        return $material;
    }

    public function grain_composition_31424($ugtp, $methodId)
    {
        $materialModel = new Material();
        $methodModel = new Methods();
        $gostModel = new Gost();

        $material = [];

        $material['method_id'] = $methodId;

        return $material;
    }

    public function average($ugtp, $methodId)
    {
        $methodsModel = new Methods();
        $method = $methodsModel->get($methodId);

        // Если количество знаков после запятой было сохранено в листе измерений то берём из листа измеений, иначе берём из методики
        $decimalPlaces = $ugtp['measuring_sheet']['decimal_places'] ?? $method['decimal_places'];
        $method['decimal'] = $decimalPlaces;

        return $method;
    }

    public function average2($ugtp, $methodId)
    {
        $methodsModel = new Methods();
        $method = $methodsModel->get($methodId);

        // Если количество знаков после запятой было сохранено в листе измерений то берём из листа измеений, иначе берём из методики
        $decimalPlaces = $ugtp['measuring_sheet']['decimal_places'] ?? $method['decimal_places'];
        $method['decimal'] = $decimalPlaces;

        return $method;
    }

    public function average3($ugtp, $methodId)
    {
        $methodsModel = new Methods();
        $method = $methodsModel->get($methodId);

        // Если количество знаков после запятой было сохранено в листе измерений то берём из листа измеений, иначе берём из методики
        $decimalPlaces = $ugtp['measuring_sheet']['decimal_places'] ?? $method['decimal_places'];
        $method['decimal'] = $decimalPlaces;

        return $method;
    }

    public function average4($ugtp, $methodId)
    {
        $methodsModel = new Methods();
        $method = $methodsModel->get($methodId);

        // Если количество знаков после запятой было сохранено в листе измерений то берём из листа измеений, иначе берём из методики
        $decimalPlaces = $ugtp['measuring_sheet']['decimal_places'] ?? $method['decimal_places'];
        $method['decimal'] = $decimalPlaces;

        return $method;
    }

    public function medium2($ugtp, $methodId)
    {
        $methodsModel = new Methods();
        $method = $methodsModel->get($methodId);

        // Если количество знаков после запятой было сохранено в листе измерений то берём из листа измеений, иначе берём из методики
        $decimalPlaces = $ugtp['measuring_sheet']['decimal_places'] ?? $method['decimal_places'];
        $method['decimal'] = $decimalPlaces;

        return $method;
    }

    /**
     * Морозостойкость
     * @param $ugtp
     * @param $methodId
     * @return array
     */
    public function frost($ugtp, $methodId)
    {
        $materialModel = new Material();
        $methodsModel = new Methods();

        $umtr = $this->materialToRequestData($ugtp['material_to_request_id']);
        $materials = $materialModel->getById($umtr['material_id']);
        $method = $methodsModel->get($ugtp['method_id']);

        return [
            'material' => $materials['NAME'],
            'cipher' => $umtr['cipher'],
            'reg_doc' => $method['reg_doc'],
            'clause' => $method['clause'],
        ];
    }

    /**
     * Прочность на сжатие ГОСТ 17624 п. 7
     * @param $ugtp
     * @param $methodId
     * @return array
     */
    public function concrete_strength_17624_7($ugtp, $methodId)
    {
        $gostModel = new Gost();
        $nkModel = new Nk;

        $material = $gostModel->getMaterialByUgtpId($ugtp['id']);

        $response = [];
        $measurementId = $ugtp['measuring_sheet']['measurement_id'] ?? '';
        $response['name_for_protocol'] = $material['name_for_protocol'];

        // Ссылка на список листов измерений в зависимости от схемы испытаний
        if ($ugtp['measuring_sheet']['scheme'] === 'v') { // Схема "В"
            $graduation = $nkModel->getGraduation($measurementId);
            $response['graduations'] = $nkModel->getGraduationList();

            $response['a'] = $graduation['data']['a'] ?? '';
            $response['b'] = $graduation['data']['b'] ?? '';
            $response['round_a'] = $graduation['data']['round_a'] ?? '';
            $response['round_b'] = $graduation['data']['round_b'] ?? '';
            $response['S'] = $graduation['data']['S'] ?? '';
            $response['r'] = $graduation['data']['r'] ?? '';
            $response['concrete_class'] = $graduation['data']['concrete_class'] ?? '';
            $response['measuring_device'] = $graduation['data']['measuring_device'] ?? '';
            $response['method'] = $graduation['data']['method'] ?? '';
            $response['day_to_test'] = $graduation['data']['day_to_test'] ?? '';

            $response['is_data_v'] = $ugtp['measuring_sheet']['scheme'] === 'v' && !empty($measurementId);
            $response['mean_count'] = count($ugtp['measuring_sheet']['mean']);

            $response['anchor_journal'] = '/ulab/nk/graduationList/';
        } elseif ($ugtp['measuring_sheet']['scheme'] === 'g') { // Схема "Г"
            $response['anchor_journal'] = '/ulab/nk/matchCoefficientList/';
        }

        $response['anchor_disabled'] = $ugtp['measuring_sheet']['scheme'] ? '' : 'icon-disabled';



        return $response;
    }

    /**
     * Фактический класса бетона ГОСТ 18105 п. 8.4.1
     * @param $ugtp
     * @param $methodId
     * @return array
     */
    public function actual_class_18105_8_4_1($ugtp, $methodId)
    {
        $gostModel = new Gost();
        $methodsModel = new Methods();
        $nkModel = new Nk;

        $material = $gostModel->getMaterialByUgtpId($ugtp['id']);
        $methods = $methodsModel->getMethodByUmtrId($ugtp['material_to_request_id'], [$ugtp['id']], true);

        $response = [];
        $measurementId = $ugtp['measuring_sheet']['measurement_id'] ?? '';
        $response['name_for_protocol'] = $material['name_for_protocol'];
        $response['methods'] = $methods;

        // Ссылка на список листов измерений в зависимости от схемы испытаний
        if ($ugtp['measuring_sheet']['scheme'] === 'v') { // Схема "В"
            $graduation = $nkModel->getGraduation($measurementId);
            $response['graduations'] = $nkModel->getGraduationList();

            $response['a'] = $graduation['data']['a'] ?? '';
            $response['b'] = $graduation['data']['b'] ?? '';
            $response['round_a'] = $graduation['data']['round_a'] ?? '';
            $response['round_b'] = $graduation['data']['round_b'] ?? '';
            $response['S'] = $graduation['data']['S'] ?? '';
            $response['r'] = $graduation['data']['r'] ?? '';
            $response['concrete_class'] = $graduation['data']['concrete_class'] ?? '';
            $response['measuring_device'] = $graduation['data']['measuring_device'] ?? '';
            $response['method'] = $graduation['data']['method'] ?? '';
            $response['day_to_test'] = $graduation['data']['day_to_test'] ?? '';

            $response['is_data_v'] = $ugtp['measuring_sheet']['scheme'] === 'v' && !empty($measurementId);
            $response['mean_count'] = count($ugtp['measuring_sheet']['mean']);

            $response['anchor_journal'] = '/ulab/nk/graduationList/';
        } elseif ($ugtp['measuring_sheet']['scheme'] === 'g') { // Схема "Г"
            $response['anchor_journal'] = '/ulab/nk/matchCoefficientList/';
        }

        $response['anchor_disabled'] = $ugtp['measuring_sheet']['scheme'] ? '' : 'icon-disabled';



        return $response;
    }

    /**
     * начать испытания
     * @param int $ugtpId
     * @return array
     */
    public function startTrial(int $ugtpId): array
    {
        $resultModel = new Result;
        $methodsModel = new Methods;

        $isSuccess = true;
        $errors = [];

        $stateLastAction = $resultModel->getStateLastAction($ugtpId);
        $method = $methodsModel->getMethodByUgtpId($ugtpId);

        $anchor = "<a href='" . URI . "/gost/method/{$method['id']}'>{$method['view_gost_for_protocol']}</a>";

        if (in_array($stateLastAction['state'], ['start', 'complete'])) {
            $errors = ["Не удалось начать испытание для методики {$anchor}, испытание уже начато или закончено"];
            return [
                'success' => false,
                'errors' => $errors,
                'data' => '',
            ];
        }

        $data = [
            'ugtp_id' => $ugtpId,
            'state' => "'start'",
            'date' => '"' . date('Y-m-d') . '"',
            'user' => (int)$_SESSION['SESS_AUTH']['USER_ID']
        ];

        $result = $this->DB->Insert('ulab_start_trials', $data);

        if (empty(intval($result))) {
            $errors = ["Не удалось начать испытание для методики {$anchor}"];
            $isSuccess = false;
        }

        return [
            'success' => $isSuccess,
            'errors' => $errors,
            'data' => '',
        ];
    }


    /**
     * приостановить испытание
     * @param int $ugtpId
     * @return array
     */
    public function pauseTrial(int $ugtpId): array
    {
        $resultModel = new Result;
        $methodsModel = new Methods;

        $stateLastAction = $resultModel->getStateLastAction($ugtpId);
        $method = $methodsModel->getMethodByUgtpId($ugtpId);

        $anchor = "<a href='" . URI . "/gost/method/{$method['id']}'>{$method['view_gost_for_protocol']}</a>";

        if (in_array($stateLastAction['state'], ['pause', 'complete'])) {
            return [
                'success' => false,
                'errors' => ["Не удалось приостановить испытание для методики {$anchor}, испытание уже приостановлено или закончено"]
            ];
        }

        $data = [
            'ugtp_id' => $ugtpId,
            'state' => "'pause'",
            'date' => '"' . date('Y-m-d') . '"',
            'user' => (int)$_SESSION['SESS_AUTH']['USER_ID']
        ];

        $result = $this->DB->Insert('ulab_start_trials', $data);

        if (empty(intval($result))) {
            return [
                'success' => false,
                'errors' => ["Не удалось приостановить испытание для методики {$anchor}"]
            ];
        }

        return [
            'success' => true,
            'errors' => []
        ];
    }


    /**
     * завершить испытания
     * @param int $ugtpId
     * @return array
     */
    public function stopTrial(int $ugtpId): array
    {
        $resultModel = new Result;
        $methodsModel = new Methods;

        $isSuccess = true;
        $errors = []; // тексты сообщений ошибок

        $stateLastAction = $resultModel->getStateLastAction($ugtpId);
        $method = $methodsModel->getMethodByUgtpId($ugtpId);

        $anchor = "<a href='" . URI . "/gost/method/{$method['id']}'>{$method['view_gost_for_protocol']}</a>";

        if (in_array($stateLastAction['state'], ['complete'])) {
            $errors = ["Не удалось завершить испытание для методики {$anchor}, испытание уже завершено"];
            return [
                'success' => false,
                'errors' => $errors,
                'data' => '',
            ];
        }

        $data = [
            'ugtp_id' => $ugtpId,
            'state' => "'complete'",
            'date' => '"' . date('Y-m-d') . '"',
            'user' => (int)$_SESSION['SESS_AUTH']['USER_ID']
        ];

        $result = $this->DB->Insert('ulab_start_trials', $data);

        if (empty(intval($result))) {
            $errors = ["Не удалось завершить испытание для методики {$anchor}"];
            $isSuccess = false;
        }

        return [
            'success' => $isSuccess,
            'errors' => $errors,
            'data' => '',
        ];
    }

    /**
     * получить данные начала/окончания испытаний
     * @param int $ugtp_id
     * @return array
     */
    public function getStartTrials(int $ugtp_id): array
    {
        $response = [];

        if (empty($ugtp_id) || $ugtp_id < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM `ulab_start_trials` WHERE `ugtp_id` = {$ugtp_id} AND is_actual = 1");

        while ($row = $result->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    /**
     * @param int $ugtpId
     * @return array
     */
    public function getLastStartTrials(int $ugtpId): array
    {
        $response = [];

        if (empty($ugtpId) || $ugtpId < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM `ulab_start_trials` WHERE `ugtp_id` = {$ugtpId} AND state = 'start' ORDER BY id DESC LIMIT 1")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * @param int $ugtpId
     * @return array
     */
    public function getStateLastAction(int $ugtpId): array
    {
        $response = [];

        if (empty($ugtpId) || $ugtpId < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT * FROM `ulab_start_trials` WHERE `ugtp_id` = {$ugtpId}  ORDER BY id DESC LIMIT 1")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * получить изменённые данные начала испытаний
     * @param int $ugtpId
     * @return array
     */
    public function getModifiedStartTrials(int $ugtpId): array
    {
        $response = [];

        if (empty($ugtpId) || $ugtpId < 0) {
            return $response;
        }

        $result = $this->DB->Query(
            "SELECT * FROM `ulab_start_trials` WHERE `ugtp_id` = {$ugtpId} AND state = 'start' AND is_change = 1"
        )->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * Получить изменённые данные завершения испытаний
     * @param int $ugtpId
     * @return array
     */
    public function trialsCompletionModific(int $ugtpId): array
    {
        $response = [];

        if (empty($ugtpId) || $ugtpId < 0) {
            return $response;
        }

        $result = $this->DB->Query(
            "SELECT * FROM `ulab_start_trials` WHERE `ugtp_id` = {$ugtpId} AND state = 'complete' AND is_change = 1"
        )->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * изменение "начало/окончание" испытаний
     * @param array $ugtpIds
     * @param array $data
     */
    public function changeStartTrials(array $ugtpIds, array $data)
    {
        $protocolModel = new Protocol();

        $currentUserId = (int)$_SESSION['SESS_AUTH']['USER_ID'];
        $dateBegin = $data['DATE_BEGIN'] ?? '';
        $dateEnd = $data['DATE_END'] ?? '';
        $changeTrialsDate = !empty($data['CHANGE_TRIALS_DATE']) ? 1 : 0;
        foreach ($ugtpIds as $ugtpId) {
            //если дату испытаний не меняли или даты начала испытаний нет
            if (!$changeTrialsDate || !$dateBegin) {
                continue;
            }

            $startTrial = $this->getStartTrials($ugtpId);
            $modifiedStartTrials = $this->getModifiedStartTrials($ugtpId);
            $trialsCompletionModific = $this->trialsCompletionModific($ugtpId);

            //Делаем не актуальные даты испытания
            if (!empty($startTrial) && empty($modifiedStartTrials)) {
                $data = [
                    'is_actual' => 0,
                ];

                $where = "WHERE ugtp_id = {$ugtpId}";
                $this->DB->Update('ulab_start_trials', $data, $where);
            }

            // если дата испытаний ,была уже изменена обновляем её, если дату еще не меняли то делаем даты неактуальными и создаём новые измененные
            if (!empty($modifiedStartTrials)) {
                foreach ($startTrial as $data) {
                    if ($data['state'] === 'start') {
                        $dataUpdate = [
                            'date' => '"' . $dateBegin . '"',
                        ];

                        $where = "WHERE id = {$data['id']}";
                        $this->DB->Update('ulab_start_trials', $dataUpdate, $where);
                    }
                }
            } else {
                $dataStart = [
                    'ugtp_id' => $ugtpId,
                    'state' => "'start'",
                    'date' => '"' . $dateBegin . '"',
                    'user' => $currentUserId,
                    'is_actual' => 1,
                    'is_change' => 1,
                ];
                $this->DB->Insert('ulab_start_trials', $dataStart);
            }

            if (!empty($trialsCompletionModific)) {
                foreach ($startTrial as $data) {
                    if ($data['state'] === 'complete') {
                        $dataUpdate = [
                            'date' => '"' . $dateEnd . '"',
                        ];

                        $where = "WHERE id = {$data['id']}";
                        $this->DB->Update('ulab_start_trials', $dataUpdate, $where);
                    }
                }
            } else {
                if ($dateEnd) {
                    $dataStop = [
                        'ugtp_id' => $ugtpId,
                        'state' => "'complete'",
                        'date' => '"' . $dateEnd . '"',
                        'user' => $currentUserId,
                        'is_actual' => 1,
                        'is_change' => 1,
                    ];
                    $this->DB->Insert('ulab_start_trials', $dataStop);
                }
            }
        }
    }

    /**
     * получить данные начала/окончания испытаний по id протоколу
     * @param int|null $protocolId
     * @return array
     */
    public function getStartTrialsByProtocol(?int $protocolId): array
    {
        $response = [];

        if (empty($protocolId) || $protocolId < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT MIN(ust.date) date_begin, MAX(ust.date) date_end 
                                        FROM ulab_material_to_request umtr 
                                        LEFT JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id 
                                        LEFT JOIN ulab_start_trials ust ON ust.ugtp_id = ugtp.id  
                                        WHERE (umtr.protocol_id = {$protocolId} or (umtr.protocol_id = 0 and ugtp.protocol_id = {$protocolId})) AND ust.is_actual = 1")->Fetch();

        if (!empty($result)) {
            $response = $result;
        }

        return $response;
    }

    /**
     * @param array $data
     */
    public function saveSelectedRooms(array $data)
    {
        foreach ($data as $ugtpId => $rooms) {
            foreach ($rooms as $roomId) {
                $sqlData = [
                    'ugtp_id' => (int)$ugtpId,
                    'room_id' => (int)$roomId
                ];

                $this->DB->Insert('ulab_gost_room', $sqlData);
            }
        }
    }

    /**
     * получить данные гостов ulab_gost_to_probe
     * @param int|null $protocolId
     * @return array
     */
    public function getUGTPByProtocolId(?int $protocolId): array
    {
        $response = [];

        if (empty($protocolId) || $protocolId < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT ugtp.* FROM ulab_material_to_request umtr 
                                        INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
                                        WHERE umtr.protocol_id = {$protocolId}");

        while ($row = $result->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    /**
     * получить данные гостов ulab_gost_to_probe без методик отбора
     * @param int|null $protocolId
     * @return array
     */
    public function getUGTPNotSelection(?int $protocolId): array
    {
        $response = [];

        if (empty($protocolId) || $protocolId < 0) {
            return $response;
        }

        $result = $this->DB->Query("SELECT ugtp.* FROM ulab_material_to_request umtr 
                                        INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id 
                                        INNER JOIN ulab_methods as um ON um.id = ugtp.method_id 
                                        WHERE umtr.protocol_id = {$protocolId} AND um.is_selection <> 1");

        while ($row = $result->Fetch()) {
            $response[] = $row;
        }

        return $response;
    }

    public function getTrialStatisticsList($filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'ugtp.id',
            'dir' => 'DESC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                // Методика
                if (isset($filter['search']['method'])) {
                    $where .= "CONCAT(ug.reg_doc, ' ', um.clause, ' / ', um.name) LIKE '%{$filter['search']['method']}%' AND ";
                }
                // Кол-во испытаний
                if (isset($filter['search']['ugtp_count'])) {
                    $where .= "ugtp_count = '{$filter['search']['ugtp_count']}' AND ";
                }
            }
            // везде
            if (isset($filter['search']['everywhere'])) {
                $where .=
                    "";
            }
        }

        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
                case 'method':
                    $order['by'] = "CONCAT(ug.reg_doc, ' ', um.clause, ' ', um.name)";
                    break;
                case 'ugtp_count':
                default:
                    $order['by'] = 'ugtp_count';
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

        $where .= "1 ";

        $result = [];

        $data = $this->DB->Query(
            "SELECT 
                        ugtp.id id_ugtp, ugtp.method_id, COUNT(DISTINCT ugtp.id) ugtp_count, 
                        um.id id_um, um.clause, um.name, 
                        ug.reg_doc, 
                        CONCAT(ug.reg_doc, ' ', um.clause, ' / ', um.name) method 
                    FROM ulab_gost_to_probe ugtp 
                    INNER JOIN ulab_start_trials ust ON ust.ugtp_id = ugtp.id AND ust.is_actual = 1 AND ust.state = 'complete'  
                    INNER JOIN ulab_methods um ON um.id = ugtp.method_id 
                    INNER JOIN ulab_gost ug ON ug.id = um.gost_id 
                    GROUP BY ugtp.method_id 
                    HAVING {$where}
                    ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT 
                        ugtp.id id_ugtp, ugtp.method_id, COUNT(DISTINCT ugtp.id) ugtp_count, 
                        um.id id_um, um.clause, um.name, 
                        ug.reg_doc, 
                        CONCAT(ug.reg_doc, ' ', um.clause, ' / ', um.name) method 
                    FROM ulab_gost_to_probe ugtp 
                    INNER JOIN ulab_start_trials ust ON ust.ugtp_id = ugtp.id AND ust.is_actual = 1 AND ust.state = 'complete' 
                    INNER JOIN ulab_methods um ON um.id = ugtp.method_id 
                    INNER JOIN ulab_gost ug ON ug.id = um.gost_id 
                    GROUP BY ugtp.method_id 
                    HAVING {$where}"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT 
                        ugtp.id id_ugtp, ugtp.method_id, COUNT(DISTINCT ugtp.id) ugtp_count, 
                        um.id id_um, um.clause, um.name, 
                        ug.reg_doc, 
                        CONCAT(ug.reg_doc, ' ', um.clause, ' / ', um.name) method 
                    FROM ulab_gost_to_probe ugtp 
                    INNER JOIN ulab_start_trials ust ON ust.ugtp_id = ugtp.id AND ust.is_actual = 1 AND ust.state = 'complete' 
                    INNER JOIN ulab_methods um ON um.id = ugtp.method_id 
                    INNER JOIN ulab_gost ug ON ug.id = um.gost_id 
                    GROUP BY ugtp.method_id 
                    HAVING {$where}"
        )->SelectedRowsCount();

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    public function getStartStopTrials($filter = [])
    {
        $where = "";
        $limit = "";
        $order = [
            'by' => 'ust.id',
            'dir' => 'ASC'
        ];

        if (!empty($filter)) {
            // из $filter собирать строку $where тут
            // формат такой: $where .= "что-то = чему-то AND ";
            // или такой:    $where .= "что-то LIKE '%чему-то%' AND ";
            // слева без пробела, справа всегда AND пробел

            // работа с фильтрами
            if (!empty($filter['search'])) {
                if (isset($filter['search']['method_id'])) {
                    $where .= "ugtp.method_id = {$filter['search']['method_id']} AND ust.is_actual = 1 AND ";
                }
            }
            // везде
            if (isset($filter['search']['everywhere'])) {
                $where .=
                    "";
            }
        }

        // работа с сортировкой
        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }

            switch ($filter['order']['by']) {
                case 'date':
                    $order['by'] = 'ust.date';
                    break;
                default:
                    $order['by'] = 'ust.id';
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

        $where .= "1 ";

        $result = [];

        $data = $this->DB->Query(
            "SELECT 
                       ugtp.id id_ugtp, ugtp.method_id, 
                       ust.*, 
                       bu.NAME, bu.LAST_NAME, bu.SECOND_NAME, 
                       umtr.cipher, umtr.deal_id, 
                       bcd.TITLE  
                   FROM ulab_gost_to_probe ugtp 
                   INNER JOIN ulab_material_to_request umtr ON umtr.id = ugtp.material_to_request_id 
                   INNER JOIN b_crm_deal bcd ON bcd.id = umtr.deal_id 
                   INNER JOIN ulab_start_trials ust ON ust.ugtp_id = ugtp.id 
                   INNER JOIN b_user bu ON bu.id = ust.user 
                   WHERE {$where} 
                   ORDER BY {$order['by']} {$order['dir']} {$limit}"
        );

        $dataTotal = $this->DB->Query(
            "SELECT 
                       ugtp.id id_ugtp, ugtp.method_id, 
                       ust.*, 
                       bu.NAME, bu.LAST_NAME, bu.SECOND_NAME, 
                       umtr.cipher, umtr.deal_id, 
                       bcd.TITLE 
                   FROM ulab_gost_to_probe ugtp 
                   INNER JOIN ulab_material_to_request umtr ON umtr.id = ugtp.material_to_request_id 
                   INNER JOIN b_crm_deal bcd ON bcd.id = umtr.deal_id 
                   INNER JOIN ulab_start_trials ust ON ust.ugtp_id = ugtp.id 
                   INNER JOIN b_user bu ON bu.id = ust.user 
                   WHERE {$where}"
        )->SelectedRowsCount();
        $dataFiltered = $this->DB->Query(
            "SELECT 
                       ugtp.id id_ugtp, ugtp.method_id, 
                       ust.*, 
                       bu.NAME, bu.LAST_NAME, bu.SECOND_NAME, 
                       umtr.cipher, umtr.deal_id, 
                       bcd.TITLE 
                   FROM ulab_gost_to_probe ugtp 
                   INNER JOIN ulab_material_to_request umtr ON umtr.id = ugtp.material_to_request_id 
                   INNER JOIN b_crm_deal bcd ON bcd.id = umtr.deal_id 
                   INNER JOIN ulab_start_trials ust ON ust.ugtp_id = ugtp.id 
                   INNER JOIN b_user bu ON bu.id = ust.user 
                   WHERE {$where}"
        )->SelectedRowsCount();

        $arrUst = [];
        while ($row = $data->Fetch()) {
            $arrUst[$row['id_ugtp']][] = $row;
        }

        $i = 0;
        foreach ($arrUst as $ugtpId => $ust) {
            // Если испытание не завершено, то пропускаем
            if (end($ust)['state'] !== 'complete') {
                continue;
            }

            // Получаем дату начала испытаний и испольнителя
            if ($ust[0]['state'] === 'start' && empty($result[$ugtpId]['date_start'])) {
                $result[$i]['date_start'] = $ust[0]['date'] ? date('d.m.Y', strtotime($ust[0]['date'])) : '';

                $name = trim($ust[0]['NAME']);
                $lastName = trim($ust[0]['LAST_NAME']);
                $shortName = StringHelper::shortName($name);
                $result[$i]['short_name'] = "{$shortName}. {$lastName}";

                $result[$i]['TITLE'] = $ust[0]['TITLE'] ?? '';
                $result[$i]['cipher'] = $ust[0]['cipher'] ?? '';
                $result[$i]['deal_id'] = $ust[0]['deal_id'] ?? '';
            }

            // Получаем дату окончания испытаний
            if (end($ust)['state'] === 'complete' && empty($result[$i]['date_complete'])) {
                $result[$i]['date_complete'] = end($ust)['date'] ? date('d.m.Y', strtotime(end($ust)['date'])) : '';
            }

            $periods = [];
            foreach ($ust as $key => $val) {
                if ($val['state'] == 'start') {
                    $id = $val['ugtp_id'];
                    $dateBegin = $val['created_at'];
                }

                if (
                    !empty($dateBegin) && !empty($id) && $id == $val['ugtp_id'] &&
                    ($val['state'] == 'pause' || $val['state'] == 'complete')
                ) {
                    $dateEnd = $val['created_at'];
                    $periods[] = abs(strtotime($dateEnd) - strtotime($dateBegin));

                    $id = 0;
                    $dateBegin = '';
                }
            }

            // Получаем время потраченное на испытание
            $diff = array_sum($periods);

            $timeYears = 365 * 60 * 60 * 24;
            $timeMonths = 30 * 60 * 60 * 24;
            $timeDays = 60 * 60 * 24;
            $timeHours = 60 * 60;
            $timeMinutes = 60;

            $years = floor($diff / ($timeYears));
            $months = floor(($diff - $years * $timeYears) / ($timeMonths));
            $days = floor(($diff - $years * $timeYears - $months * $timeMonths) / ($timeDays));
            $hours = floor(($diff - $years * $timeYears - $months * $timeMonths - $days * $timeDays) / ($timeHours));
            $hoursAll = floor($diff / ($timeHours));
            $minutes = floor(($diff - $years * $timeYears - $months * $timeMonths - $days * $timeDays - $hours * $timeHours) / $timeMinutes);
            $seconds = floor(($diff - $years * $timeYears - $months * $timeMonths - $days * $timeDays - $hours * $timeHours - $minutes * $timeMinutes));

            $result[$i]['time'] = "{$hoursAll} ч. {$minutes} мин. {$seconds} сек.";

            if (!$result[$i]['cipher']) {
                $result[$i]['cipher'] = '-';
            }

            if (!$result[$i]['date_start']) {
                $result[$i]['date_start'] = '-';
            }

            if (!$result[$i]['deal_id']) {
                $result[$i]['deal_id'] = '-';
            }

            if (!$result[$i]['short_name']) {
                $result[$i]['short_name'] = '-';
            }

            if (!$result[$i]['TITLE']) {
                $result[$i]['TITLE'] = '-';
            }

            $i++;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }

    /**
     * Получить нормативное значение
     * @param array $data
     * @param string|null $normativeValue - записанное вручную нормативное значение
     * @return array
     */
    public function getNormativeData(array $data, ?string $normativeValue = null): array
    {
        $result = [];

        // ТУ выбрано при формировании ТЗ
        if (!empty($data['tech']['id'])) {
            // Если в ТУ(Тех.Усл.) - Ручное управление "соотв/не соотв"
            if (!empty($data['tech']['is_manual'])) {
                //                $result['normative_value'] = $normativeValue ?? ($data['normative_val'] ?? '');
                $result['normative_value'] = $normativeValue ? html_entity_decode($normativeValue) : ($data['normative_val'] ? html_entity_decode($data['normative_val']) : '');
                $result['normative_message'] = 'В ТУ Ручное управление "соотв/не соотв';
                $result['readonly_normative_value'] = !(empty($data['protocol']['NUMBER']) || !empty($data['protocol']['EDIT_RESULTS']));
            } else {
                $dopNorms = $data['tech']['dop_norm'];
                $dopValue = $data['tech']['dop_value'];
                $keyNorm = array_search($data['group'], $dopValue, true);

                // Если "Группы материала" выбрана в ТЗ и "Группы материала" создана в ГОСТе Тех.Усл., получаем "Нормы от" и "Нормы до"
                if (!empty($data['group']) && $keyNorm !== false) {
                    $result['normative_value'] = "от {$dopNorms[$keyNorm][0]} до {$dopNorms[$keyNorm][1]}";
                    $result['normative_message'] = 'нормы ТУ';
                } else { // Получаем данные "Диапазон определения 1" и "Диапазон определения 2" из ГОСТа Тех.Усл.
                    $range1 = $data['tech']['definition_range_1'];
                    $range2 = $data['tech']['definition_range_2'];

                    if ($data['tech']['definition_range_type'] == 1) { // Внутренний диапазон
                        $result['normative_value'] = "от {$range1} до {$range2}";
                        $result['normative_message'] = 'внутренний диапазон ТУ';
                    } elseif ($data['tech']['definition_range_type'] == 2) { // Внешний диапазон
                        $result['normative_value'] = "до {$range1} от {$range2}";
                        $result['normative_message'] = 'внешний диапазон ТУ';
                    } elseif ($data['tech']['definition_range_type'] == 3) { // Не нормируется
                        $result['normative_value'] = "-";
                        $result['normative_message'] = 'не нормируется диапазон ТУ';
                    } else {
                        $result['normative_value'] = "-";
                        $result['normative_message'] = 'не выбран диапазон ТУ';
                    }
                }

                $result['normative_text'] = $data['tech']['is_output'] ? "в протокол: <strong>{$data['tech']['norm_comment']}</strong>" : '';
                $result['readonly_normative_value'] = true;
            }
        } else {
            $result['normative_value'] = '-';
            $result['normative_message'] = 'не выбрано ТУ';
            $result['normative_text'] = '';
            $result['readonly_normative_value'] = true; // НЕ доступно для редактировния
        }

        return $result;
    }

    /**
     * Получить фактическое значение
     * @param array $data
     * @param null $actualValue - записанное вручную фактическое значение
     * @return array
     */
    public function getActualData(array $data, $actualValue = null): array
    {
        $result = [];
        $result['out_range'] = '';
        $result['actual_value_message'] = '';

        // Если есть лист измерения у методики, то фактическое значение берём из листа измерения
        if (!empty($data['measurement_id'])) {
            if (!empty($data['is_select_fact'])) {
                if (!empty($data['tech']['id']) && empty($data['tech']['is_manual'])) { // В ГОСТе ТУ НЕ Ручное управление "соотв/не соотв"
                    $result['actual_value'] = '';
                    $result['actual_value_type'] = 'select';
                    $result['actual_facts_select'] = json_decode($data['facts_select'], JSON_UNESCAPED_UNICODE);
                    $result['readonly_actual_value'] = true; // Не доступно для редактировния
                    $result['actual_value_message'] = "<span class='text-danger'>Параметр М-ки(ф/значение из списка) не соотв. параметру в ТУ (управление не вручную)</span>";
                } else {
                    $result['actual_value'] = $data['measuring_sheet']['result_value'] ?? '';
                    $result['actual_value_type'] = 'select';
                    $result['actual_facts_select'] = json_decode($data['facts_select'], JSON_UNESCAPED_UNICODE);
                    $result['actual_value_message'] = 'Данные из листа из-я. М-ка со значением из списка';
                    $result['readonly_actual_value'] = true; // Не доступно для редактировния
                }
            } elseif (!empty($data['is_text_fact'])) { // В ГОСТе Методике "Факт. значения текстом"
                if (!empty($data['tech']['id']) && empty($data['tech']['is_manual'])) { // В ГОСТе ТУ НЕ Ручное управление "соотв/не соотв"
                    $result['actual_value'] = '';
                    $result['actual_value_type'] = 'type="text"';
                    $result['readonly_actual_value'] = true; // Не доступно для редактировния
                    $result['actual_value_message'] = "<span class='text-danger'>Параметр М-ки(ф/значение текстом) не соотв. параметру в ТУ (управление не вручную)</span>";
                } else {
                    $result['actual_value'] = $data['measuring_sheet']['result_value'] ?? '';
                    $result['actual_value_type'] = 'type="text"';
                    $result['actual_value_message'] = 'Данные из листа из-я. М-ка со значением текстом';
                    $result['readonly_actual_value'] = true; // Не доступно для редактировния
                }
            } else {
                $result['actual_value'] = $data['measuring_sheet']['result_value'] ?? '';
                $result['actual_value_message'] = 'Данные из листа из-я. М-ка с числовым знач-ем';
                $result['actual_value_type'] = 'type="number" step="any"';
                $result['readonly_actual_value'] = true; // Не доступно для редактировния

                // Текст диапазона в ГОСТе методике выбран(отмечен)
                if ($data['is_range_text'] && isset($result['actual_value']) && is_numeric($result['actual_value'])) {
                    // Внутренний диапазон
                    if ($data['um_definition_range_type'] == 1) {
                        if ($result['actual_value'] < $data['um_definition_range_1']) {
                            $result['out_range'] = "в протокол: <strong>{$data['range_text_in']} {$data['um_definition_range_1']}</strong>";
                        } elseif ($result['actual_value'] > $data['um_definition_range_2']) {
                            $result['out_range'] = "в протокол: <strong>{$data['range_text_out']} {$data['um_definition_range_2']}</strong>";
                        }
                    } else if ($data['um_definition_range_type'] == 2) { // Внешний диапазон
                        if ($result['actual_value'] > $data['um_definition_range_1']) {
                            $result['out_range'] = "в протокол: <strong>{$data['range_text_out']} {$data['um_definition_range_1']}</strong>";
                        } elseif ($result['actual_value'] < $data['um_definition_range_2']) {
                            $result['out_range'] = "в протокол: <strong>{$data['range_text_in']} {$data['um_definition_range_2']}</strong>";
                        }
                    }
                }
            }
        } elseif (!empty($data['is_select_fact'])) {
            if (!empty($data['tech']['id']) && empty($data['tech']['is_manual'])) { // В ГОСТе ТУ НЕ Ручное управление "соотв/не соотв"
                $result['actual_value'] = '';
                $result['actual_value_type'] = 'select';
                $result['actual_facts_select'] = json_decode($data['facts_select'], JSON_UNESCAPED_UNICODE);
                $result['readonly_actual_value'] = true;
                $result['actual_value_message'] = "<span class='text-danger'>Параметр М-ки(ф/значение из списка) не соотв. параметру в ТУ (управление не вручную)</span>";
            } else {
                $result['actual_value'] = $actualValue ?? ($data['actual_value'] ?? '');
                $result['actual_value_type'] = 'select';
                $result['actual_facts_select'] = json_decode($data['facts_select'], JSON_UNESCAPED_UNICODE);
                $result['actual_value_message'] = 'М-ка со значением из списка';
                $result['readonly_actual_value'] = !(empty($data['protocol']['NUMBER']) || !empty($data['protocol']['EDIT_RESULTS']));
            }
        } elseif (!empty($data['is_text_fact'])) { // В ГОСТе Методике "Факт. значения текстом"
            if (!empty($data['tech']['id']) && empty($data['tech']['is_manual'])) { // В ГОСТе ТУ НЕ Ручное управление "соотв/не соотв"
                $result['actual_value'] = '';
                $result['actual_value_type'] = 'type="text"';
                $result['readonly_actual_value'] = true;
                $result['actual_value_message'] = "<span class='text-danger'>Параметр М-ки(ф/значение текстом) не соотв. параметру в ТУ (управление не вручную)</span>";
            } else {
                $result['actual_value'] = $actualValue ?? ($data['actual_value'] ?? '');
                $result['actual_value_type'] = 'type="text"';
                $result['actual_value_message'] = 'М-ка со значением текстом';
                $result['readonly_actual_value'] = !(empty($data['protocol']['NUMBER']) || !empty($data['protocol']['EDIT_RESULTS']));
            }
        } else {
            $result['actual_value'] = $actualValue ?? ($data['actual_value'] ?? '');
            $result['actual_value_message'] = 'М-ка с числовым знач-ем';
            $result['actual_value_type'] = 'type="number" step="any"';
            $result['readonly_actual_value'] = !(empty($data['protocol']['NUMBER']) || !empty($data['protocol']['EDIT_RESULTS']));

            // Текст диапазона в ГОСТе методике выбран(отмечен)
            if ($data['is_range_text'] && isset($result['actual_value']) && is_numeric($result['actual_value'])) {
                // Внутренний диапазон
                if ($data['um_definition_range_type'] == 1) {
                    if ($result['actual_value'] < $data['um_definition_range_1']) {
                        $result['out_range'] = "в протокол: <strong>{$data['range_text_in']} {$data['um_definition_range_1']}</strong>";
                    } elseif ($result['actual_value'] > $data['um_definition_range_2']) {
                        $result['out_range'] = "в протокол: <strong>{$data['range_text_out']} {$data['um_definition_range_2']}</strong>";
                    }
                } else if ($data['um_definition_range_type'] == 2) { // Внешний диапазон
                    if ($result['actual_value'] > $data['um_definition_range_1']) {
                        $result['out_range'] = "в протокол: <strong>{$data['range_text_out']} {$data['um_definition_range_1']}</strong>";
                    } elseif ($result['actual_value'] < $data['um_definition_range_2']) {
                        $result['out_range'] = "в протокол: <strong>{$data['range_text_in']} {$data['um_definition_range_2']}</strong>";
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Получить данные проверки на соответсвие требованиям
     * @param array $data
     * @param int|null $match - выбраное вручную соответвие требованиям
     * @return array
     */
    public function getMatchData(array $data, ?int $match = null): array
    {
        // ТУ не выбрано при формировании ТЗ
        if (empty($data['tech']['id'])) {
            $result['match'] = 2;
            $result['match_view'] = '-';
            $result['match_text'] = 'не выбрано ТУ';
            $result['readonly_match'] = true; // НЕ доступно для редактировния
            return $result;
        }

        // Если у методики фактическое значение текстом
        if ($data['is_text_fact']) {
            // Если в ТУ(Тех.Усл.) - Ручное управление "соотв/не соотв"
            if (!empty($data['tech']['is_manual'])) {
                $result['match'] = (int)($match ?? ($data['match'] ?? 2));
                switch ($result['match']) {
                    case 0:
                        $result['match_view'] = 'Не соответствует';
                        break;
                    case 1:
                        $result['match_view'] = 'Соответствует';
                        break;
                    case 2:
                        $result['match_view'] = '-';
                        break;
                    case 3:
                        $result['match_view'] = 'Не нормируется';
                        break;
                    default:
                        $result['match_view'] = '';
                }
                $result['match_text'] = 'В методике ф/знач. текстом';
                // Доступно для редактирования если нет номера у протокола или есть номер но протокол разблокирован
                $result['readonly_match'] = !(empty($data['protocol']['NUMBER']) || !empty($data['protocol']['EDIT_RESULTS']));
            } else {
                $result['match'] = 2;
                $result['match_view'] = '-';
                $result['match_text'] = '<span class="text-danger">Параметр М-ки(ф/значение текстом) не соотв. параметру в ТУ (управление не вручную)</span>';
                $result['readonly_match'] = true; // НЕ доступно для редактировния
            }

            return $result;
        }

        // Если в ТУ(Тех.Усл.) - Ручное управление "соотв/не соотв"
        if (!empty($data['tech']['is_manual'])) {
            $result['match'] = (int)($match ?? ($data['match'] ?? 2));
            switch ($result['match']) {
                case 0:
                    $result['match_view'] = 'Не соответствует';
                    break;
                case 1:
                    $result['match_view'] = 'Соответствует';
                    break;
                case 2:
                    $result['match_view'] = '-';
                    break;
                case 3:
                    $result['match_view'] = 'Не нормируется';
                    break;
                default:
                    $result['match_view'] = '';
            }
            $result['match_text'] = 'В ТУ ручное управление "соотв/не соотв"';
            $result['readonly_match'] = !(empty($data['protocol']['NUMBER']) || !empty($data['protocol']['EDIT_RESULTS']));

            return $result;
        }

        // Если отсутвует фактическое значение или не верно заполнено
        if (!is_numeric($data['actual_value'])) {
            $result['match'] = 2;
            $result['match_view'] = '-';
            $result['match_text'] = 'не заполнено или не верно заполнено ф/значение';
            $result['readonly_match'] = true; // НЕ доступно для редактировния
            return $result;
        }


        $dopNorms = $data['tech']['dop_norm'];
        $dopValue = $data['tech']['dop_value'];
        $keyNorm = array_search($data['group'], $dopValue, true);

        // Если "Группы материала" выбрана в ТЗ и "Группы материала" создана в ГОСТе Тех.Усл., получаем "Нормы от" и "Нормы до"
        //        if (!empty($data['group']) && $keyNorm !== false) {
        if (!empty($data['group'] && !empty($data['tu_id']))) {
            // Если не заполнены или не верно заполнены нормы ТУ
            //            if ( !is_numeric($dopNorms[$keyNorm][0]) || !is_numeric($dopNorms[$keyNorm][1]) ) {
            //                $result['match'] = 2;
            //                $result['match_view'] = '-';
            //                $result['match_text'] = 'не заполнены или не верно заполнены нормы ТУ';
            //                $result['readonly_match'] = true; // НЕ доступно для редактировния
            //                return $result;
            //            }

            $val1 = true;
            $val2 = true;
            if ((!empty($data['val_1']) || $data['val_1'] == '0')) {
                switch ($data['comparison_val_1']) {
                    case 'more':
                        $val1 = $data['actual_value'] > $data['val_1'];
                        break;
                    case 'less':
                        $val1 = $data['actual_value'] < $data['val_1'];
                        break;
                    case 'more_or_equal':
                        $val1 = $data['actual_value'] >= $data['val_1'];
                        break;
                    case 'less_or_equal':
                        $val1 = $data['actual_value'] <= $data['val_1'];
                        break;
                }
            }

            if (!empty($data['val_2'])) {
                switch ($data['comparison_val_2']) {
                    case 'more':
                        $val2 = $data['actual_value'] > $data['val_2'];
                        break;
                    case 'less':
                        $val12 = $data['actual_value'] < $data['val_2'];
                        break;
                    case 'more_or_equal':
                        $val2 = $data['actual_value'] >= $data['val_2'];
                        break;
                    case 'less_or_equal':
                        $val2 = $data['actual_value'] <= $data['val_2'];
                        break;
                }
            }

            // TODO: Если в ходе работы нужно будет учитывать входящий и исходящий диапазон, то доработать!
            if ($val1 && $val2) {
                $result['match'] = 1;
                $result['match_view'] = 'Соответствует';
                $result['match_text'] = 'соот. нормам ТУ';
                $result['readonly_match'] = true;
            } else {
                $result['match'] = 0;
                $result['match_view'] = 'Не соответствует';
                $result['match_text'] = 'не соот. нормам ТУ';
                $result['readonly_match'] = true;
            }
        } else { // Получаем данные "Диапазон определения от" и "Диапазон определения до" из ГОСТа Тех.Усл.
            $range1 = $data['tech']['definition_range_1'];
            $range2 = $data['tech']['definition_range_2'];

            if ($data['tech']['definition_range_type'] == 1) { // В ГОСТе ТУ - Внутренний диапазон
                if (
                    $range1 <= $data['actual_value'] &&
                    $range2 >= $data['actual_value']
                ) {
                    $result['match'] = 1;
                    $result['match_view'] = 'Соответствует';
                    $result['match_text'] = 'соот. внутренниму диапазону ТУ';
                } else {
                    $result['match'] = 0;
                    $result['match_view'] = 'Не соответствует';
                    $result['match_text'] = 'не соот. внутренниму диапазону ТУ';
                }
            } elseif ($data['tech']['definition_range_type'] == 2) { // В ГОСТе ТУ - Внешний диапазон
                if (
                    $range1 < $data['actual_value'] &&
                    $range2 > $data['actual_value']
                ) {
                    $result['match'] = 0;
                    $result['match_view'] = 'Не соответствует';
                    $result['match_text'] = 'внешнему диапазону ТУ';
                } else {
                    $result['match'] = 1;
                    $result['match_view'] = 'Соответствует';
                    $result['match_text'] = 'внешнему диапазону ТУ';
                }
            } elseif ($data['tech']['definition_range_type'] == 3) { // В ГОСТе ТУ - Не нормируется
                $result['match'] = 2;
                $result['match_view'] = '-';
                $result['match_text'] = 'не нормируется диапазон ТУ';
            } else {
                $result['match'] = 2;
                $result['match_view'] = '-';
                $result['match_text'] = 'не выбран диапазон ТУ';
            }
        }

        $result['readonly_match'] = true;

        return $result;
    }

    /**
     * Получить данные по аттестации
     * @param $data - данные из БД
     * @param null $actualValue
     * @param null $isConfirmOa - Фактическое значение подтверждено что в ОА?(checkbox отмечен?)
     * @return array
     */
    public function getAttestatData($data, $actualValue = null, $isConfirmOa = null)
    {
        $result = [];
        $result['match_message'] = '';

        // Если методика не в области аккредитации
        if (!$data['in_field']) {
            $result['match_message'] = '';
            $result['out_diapason'] = 1; // метод вне диапазона аттестации
            return $result;
        }

        // Если у методики фактическое значение текстом
        if (!empty($data['is_text_fact'])) {
            $confirmOa = (int)($isConfirmOa ?? ($data['is_confirm_oa'] ?? 0));

            // Если Фактическое значение НЕ подтверждено что в ОА (checkbox НЕ отмечен) и ф/значение не пустое
            if (
                empty($confirmOa) &&
                !(!isset($actualValue) || $actualValue === '')
            ) {
                $result['match_message'] = 'Ф/значение текстом. Отсутствует подтверждение нахождения значения в ОА!';
                $result['out_diapason'] = 1;
            }
        } else {
            // Если отсутствует ф/значение, то оно не попадает в протокол и не проверяется
            if (!isset($actualValue) || $actualValue === '' || $actualValue === null) {
                return $result;
            }

            // Если фактическое значение не является числом
            if (!is_numeric($actualValue)) {
                $result['match_message'] = 'Внимание! Сохранённое ф/значение не является числом';
                $result['out_diapason'] = 1;
                return $result;
            }

            // Получаем данные "Диапазон определения от" и "Диапазон определения до" из ГОСТа Методики
            $range1 = $data['um_definition_range_1'];
            $range2 = $data['um_definition_range_2'];

            if ($data['um_definition_range_type'] == 1) { // В ГОСТе Методике - Внутренний диапазон
                if ($actualValue < $range1 || $actualValue > $range2) {
                    if ($data['is_range_text'] && !$data['is_match_check']) { // Для ЛФХИ не проверять на соответвии ОА
                        $result['match_message'] = 'Внимание! Методика не проверяет соответствие ОА!';
                        return $result;
                    }

                    $result['match_message'] = 'Внимание! Значение вне области!';
                    $result['out_diapason'] = 1;
                }
            } elseif ($data['um_definition_range_type'] == 2) { // В ГОСТе Методике - Внешний диапазон
                if ($actualValue > $range1 || $actualValue < $range2) {
                    if ($data['is_range_text'] && !$data['is_match_check']) {
                        $result['match_message'] = 'Внимание! Методика не проверяет соответствие ОА!';
                        return $result;
                    }

                    $result['match_message'] = 'Внимание! Значение вне области!';
                    $result['out_diapason'] = 1;
                }
            }
        }

        return $result;
    }

    /**
     * Обновляет информацию по пробам и методам
     * @param $dealId
     * @param $data - данные POST запроса
     */
    public function updateProbeMethod($dealId, $data)
    {

        $resultModel = new Result();
        $protocolModel = new Protocol();

        $materialGostList = $this->materialGostList($dealId, false);

        $outDiapason = [];

        foreach ($materialGostList as $umtrId => $value) {
            foreach ($value as $ugtpId => $methodData) {
                // Нормативное значение
                $normative = $data['normative_value'][$umtrId][$ugtpId] ?? null;
                $normativeData = $resultModel->getNormativeData($methodData, $normative);
                $normativeValue = $normativeData['normative_value'];

                // Фактическое значение
                $actual = $data['actual_value'][$umtrId][$ugtpId] ?? null;
                $actualData = $resultModel->getActualData($methodData, $actual);
                $unsert = $data['uncertainty_value'][$umtrId][$ugtpId] ?? null;

                // Соответствие требованиям
                $match = $data['match'][$umtrId][$ugtpId] ?? null;
                $matchData = $resultModel->getMatchData($methodData, $match);
                $matchValue = $matchData['match'];

                // Проверка метода и значений в аттестованном диапазоне
                $isConfirmOa = $data['is_confirm_oa'][$umtrId][$ugtpId] ?? ($methodData['is_confirm_oa'] ?? 0); // Фактическое значение подтверждено что в ОА?
                $attestatData = $resultModel->getAttestatData($methodData, $actualData['actual_value'], $isConfirmOa);
                if (!empty($methodData['protocol_id']) && !empty($attestatData['out_diapason'])) {
                    $outDiapason[$methodData['protocol_id']] = $attestatData['out_diapason'];
                }

                // Дата обновления факт. значения
                $currentDateUpdate = date('Y-m-d H:i:s');

                // Дата первичного ввода факт. значения
                $currentDateSet = "";
                if (isset($actualData['actual_value']) && $actualData['actual_value']) {
                    $currentDateSet = $methodData['date_set_actual_value'] ? $methodData['date_set_actual_value'] : date('Y-m-d H:i:s');
                } else if ($methodData['date_set_actual_value']) {
                    $currentDateSet = $methodData['date_set_actual_value'];
                }

                $sqlMethodData = [
                    'normative_val' => isset($normativeValue) ? $this->quoteStr($this->DB->ForSql(htmlspecialchars($normativeValue))) : 'NULL',
                    'actual_value' => isset($actualData['actual_value']) ? $this->quoteStr($this->DB->ForSql($actualData['actual_value'])) : 'NULL',
                    'match' => isset($normativeValue) ? $this->quoteStr($this->DB->ForSql($matchValue)) : 'NULL',
                    'is_confirm_oa' => $isConfirmOa,
                    'date_last_update_actual_value' => "'{$currentDateUpdate}'",
                    'date_set_actual_value' => "'{$currentDateSet}'",
                    'uncertainty' => isset($unsert) ? $this->quoteStr($this->DB->ForSql($unsert)) : 'NULL',
                ];


                $this->DB->Update('ulab_gost_to_probe', $sqlMethodData, "WHERE id = {$ugtpId}");
            }
        }
        $protocolModel->updateAttestat($dealId, $outDiapason);
    }

    /**
     * Открепить пробы от протокола
     * @param int $protocolId
     * @param array $data - данные из $_POST запроса
     * @return array
     */
    public function unpinProbe(int $protocolId, array $data): array
    {
        $response = ['success' => true];
        $mtrData = ['protocol_id' => null];

        if (empty($protocolId) || empty($data)) {
            return [
                'success' => false,
                'errors' => "Не удалось открепить пробы, отсутвуют параметры"
            ];
        }

        $umtr = $this->getMaterialToRequestByProtocolId($protocolId);

        foreach ($umtr as $key => $val) {
            if (!empty($val['protocol_id']) && empty($_POST['probe_checkbox'][$val['id']])) {
                $result = $this->updateMaterialToRequest($val['id'], $mtrData);

                if ($result !== 1) {
                    $response = [
                        'success' => false,
                        'errors' => "Не все пробы удалось открепить от протокола"
                    ];
                }
            }
        }

        return $response;
    }

    /**
     * Прикрепляем пробы к протоколу
     * @param int $dealId
     * @param int $protocolId
     * @param array $data - данные из $_POST запроса
     * @return array
     */
    public function attachProbe(int $dealId, int $protocolId, array $data): array
    {
        $requirementModel = new Requirement;
        $protocolModel = new Protocol;

        $sampleChecked = [];
        $probe = [];
        $response = ['success' => true];
        $mtrData = ['protocol_id' => $protocolId];

        if (empty($dealId) || empty($protocolId) || (empty($data['probe_checkbox']) && empty($data['gost_check']))) {
            return [
                'success' => false,
                'errors' => "Не удалось прикрепить пробы, отсутствуют параметры"
            ];
        }

        $tz = $requirementModel->getTzByDealId($dealId);

        $dateProbe = $data['DATE_PROBE'] ?: null;
        $dateProbeRu = !empty($dateProbe) ? date("d.m.Y", strtotime($data['DATE_PROBE'])) : '-';
        $placeProbe = !empty($data['PLACE_PROBE']) ? $data['PLACE_PROBE'] : '-';

        foreach ($data['probe_checkbox'] as $umtrId => $val) {
            $umtr = $this->materialToRequestData($umtrId);

            if (!empty($umtr['protocol_id']) && (int)$umtr['protocol_id'] !== $protocolId) {
                continue;
            }

            $result = $this->updateMaterialToRequest($umtrId, $mtrData);

            $this->DB->Update('ulab_gost_to_probe', ['protocol_id' => $protocolId], "where material_to_request_id = {$umtrId}");

            if ($result !== 1) {
                $response = [
                    'success' => false,
                    'errors' => "Не все пробы удалось прикрепить к протоколу"
                ];
            }

            //TODO: (Выбор проб) Временные массивы данных, сохраняет сериализованные данные, для работы остальных скриптов до их рефакторинга
            $numberProbe[$umtr['probe_number'] - 1] = $tz['PROBE'][$umtr['material_number']]['number_probe'][$umtr['probe_number'] - 1] ?: [];
            $shNumber[$umtr['probe_number'] - 1] = $tz['PROBE'][$umtr['material_number']]['sh_number'][$umtr['probe_number'] - 1] ?: [];
            $probe[$umtr['material_number']] = $tz['PROBE'][$umtr['material_number']];
            $probe[$umtr['material_number']]['number_probe'] = $numberProbe;
            $probe[$umtr['material_number']]['sh_number'] = $shNumber;
            $probe[$umtr['material_number']]['mesto_data'] = $placeProbe . '; ' . $dateProbeRu;

            $sampleChecked[$umtr['material_number'] - 1][0][$umtr['probe_number']][0] = $val;
        }

        foreach ($data['gost_check'] as $ugtpId => $val) {
            $ugtp = $this->DB->Query("select * from ulab_gost_to_probe where id = {$ugtpId}")->Fetch();

            if (!empty($ugtp['protocol_id'])) {
                continue;
            }

            $this->DB->Update('ulab_gost_to_probe', ['protocol_id' => $protocolId], "where id = {$ugtpId}");
        }

        $data = [
            'SAMPLE_CHECKED' => serialize($sampleChecked),
            'PROBE' => serialize($probe)
        ];
        $protocolModel->update($protocolId, $data);

        return $response;
    }


    /**
     * @param $ugtpId
     */
    public function unboundProtocol($ugtpId)
    {
        $this->DB->Update('ulab_gost_to_probe', ['protocol_id' => 'NULL'], "where id = {$ugtpId}");
    }


    /**
     * @param $ugtpId
     * @param $data
     */
    public function updateUlabGostToProbe($ugtpId, $data)
    {
        $sqlData = $this->prepearTableData('ulab_gost_to_probe', $data);

        $this->DB->Update('ulab_gost_to_probe', $sqlData, "where id = {$ugtpId}");
    }


    /**
     * @param $protocolId
     * @param $data
     * @return mixed
     */
    public function updateProtocol($protocolId, $data)
    {
        $permissionModel = new Permission;
        $protocolModel = new Protocol;

        //        $this->pre($data, true);

        $dopInfoArray = explode("\n", $data['DOP_INFO']);
        $dopInfoArray2 = explode("\n", $data['DOP_INFO2']);
        //        $data['DOP_INFO'] = str_replace(":marker", "");

        $data['DOP_INFO'] = "";
        $data['DOP_INFO2'] = "";

        //        $this->pre($dopInfoArray, true);

        foreach ($dopInfoArray as $key => $value) {
            if (!$value || empty($value)) continue;

            //            if($key != 0) {
            //                $data['DOP_INFO'] .= "\n";
            //            }

            if ($value[0] != '-' && $value[1] != '-') {
                $data['DOP_INFO'] .= "- {$value}";
            } else {
                $data["DOP_INFO"] .= $value;
            }
        }

        foreach ($dopInfoArray2 as $key => $value) {
            if (!$value || empty($value)) continue;

            //            if($key != 0) {
            //                $data['DOP_INFO'] .= "\n";
            //            }

            if ($value[0] != '-' && $value[1] != '-') {
                $data['DOP_INFO2'] .= "- {$value}";
            } else {
                $data["DOP_INFO2"] .= $value;
            }
        }

        $protocol = $protocolModel->getProtocolById($protocolId);
        $permissionInfo = $permissionModel->getUserPermission($_SESSION['SESS_AUTH']['USER_ID']);

        $data['VERIFY'] = serialize($data['VERIFY']);
        $data['NO_COMPLIANCE'] = $data['NO_COMPLIANCE'] ?? 0;
        $data['CHANGE_TRIALS_DATE'] = $data['CHANGE_TRIALS_DATE'] ?? 0;
        $data['CHANGE_TRIALS_CONDITIONS'] = $data['CHANGE_TRIALS_CONDITIONS'] ?? 0;
        $data['OUTPUT_IN_PROTOCOL'] = $data['OUTPUT_IN_PROTOCOL'] ?? 0;
        $data['PROTOCOL_OUTSIDE_LIS'] = $data['PROTOCOL_OUTSIDE_LIS'] ?? 0;
        $data['automatic_comments'] = $data['automatic_comments'] ?? 0;
        $data['determined_characteristics'] = $data['determined_characteristics'] ?? 0;
        $attestatInProtocol = $data['ATTESTAT_IN_PROTOCOL'] ?? 0;

        // Протокол с аттестатом акредитации если протокол соответствует диапазону(условиям) аттестации
        // И в результатах испытаний у протокола отмечен чекбокс "C аттестатом аккредитации"
        //        $data['ATTESTAT_IN_PROTOCOL'] = !empty($protocol['IN_ATTESTAT_DIAPASON']) && !empty($attestatInProtocol) ? 1 : 0;
        $data['ATTESTAT_IN_PROTOCOL'] = $data['ATTESTAT_IN_PROTOCOL'] ?? 0;

        // Место отбора проб
        if (empty($data['PLACE_PROBE'])) {
            unset($data['PLACE_PROBE']);
        }
        // Дата отбора проб
        if (empty($data['DATE_PROBE'])) {
            unset($data['DATE_PROBE']);
        }
        // Изменить условия испытаний
        // Проверка на доступ редактирования данных условий окружающей среды(помещения)
        if (!in_array($permissionInfo['id'], [ADMIN_PERMISSION, HEAD_IC_PERMISSION, HEAD_LAB_PERMISSION]) && !in_array($_SESSION['SESS_AUTH']['USER_ID'], [86, 277])) {
            unset($data['CHANGE_TRIALS_DATE']);
            unset($data['CHANGE_TRIALS_CONDITIONS']);
        }

        $this->saveProtocolInfoHistory($protocolId, $data);
        return $protocolModel->update($protocolId, $data);
    }

    public function updateSertsProtocol($oboruds, $certs)
    {
        $oborudIds = !empty($oboruds['equipment_ids']) ? json_decode($oboruds['equipment_ids']) : [];
        foreach ($oborudIds as $oborudId) {
            $this->DB->Query("
                UPDATE `ba_oborud_certificate`
                SET `in_protocol` = 0
                WHERE `oborud_id` = $oborudId
            ");
        }
        foreach ($certs as $certId) {
            $this->DB->Query("
                UPDATE `ba_oborud_certificate`
                SET `in_protocol` = 1
                WHERE `id` = $certId
            ");
        }
    }

    public function saveProtocolInfoHistory($protocolId, $data)
    {
        $historyModel = new History;
        $protocolModel = new Protocol;
        $requestModel = new Request;

        $protocol = $protocolModel->getProtocolById($protocolId);
        $deal = $requestModel->getDealById($protocol['DEAL_ID']);

        $historyType[] = 'Сохранение информации по протоколу';
        if ($protocol['PROTOCOL_OUTSIDE_LIS'] !== (string)$data['PROTOCOL_OUTSIDE_LIS']) {
            $historyType[] = 'Изменили "Протокол выдается вне ЛИС"';
        }
        if ($protocol['TEMP_O'] !== $data['TEMP_O'] || $protocol['TEMP_TO_O'] !== $data['TEMP_TO_O']) {
            $historyType[] = 'Изменили значение температуры';
        }
        if ($protocol['VLAG_O'] !== $data['VLAG_O'] || $protocol['VLAG_TO_O'] !== $data['VLAG_TO_O']) {
            $historyType[] = 'Изменили значение влажности';
        }
        if (isset($protocol['DATE_BEGIN']) && isset($data['DATE_BEGIN']) && $protocol['DATE_BEGIN'] !== $data['DATE_BEGIN']) {
            $historyType[] = 'Изменили дату начала испытаний';
        }
        if (isset($protocol['DATE_END']) && isset($data['DATE_END']) && $protocol['DATE_END'] !== $data['DATE_END']) {
            $historyType[] = 'Изменили дату окончания испытаний. ';
        }

        $strType = implode('. ', $historyType);

        $historyData = [
            'DATE' => date('Y-m-d H:i:s'),
            'ASSIGNED' => $_SESSION['SESS_AUTH']['NAME'],
            'PROT_NUM' => $protocol['NUMBER'],
            'TZ_ID' => $protocol['ID_TZ'],
            'USER_ID' => $_SESSION['SESS_AUTH']['USER_ID'],
            'TYPE' => $strType,
            'REQUEST' => $deal['TITLE'],
            'PROTOCOL_ID' => $protocolId
        ];

        $historyModel->addHistory($historyData);
    }


    /**
     * @param $methodId
     * @return array
     */
    public function getConditionRoomMethod($ugtpId)
    {
        $methodModel = new Methods();
        $labModel = new Lab();
        $reqModel = new Requirement();
        $materialModel = new Material();

        $method = $reqModel->getGostToProbe($ugtpId);
        $materialInfo = $materialModel->getById($method['material_id']);

        $methodId = $method['method_id'];

        $methodInfo = $methodModel->get($methodId);
        $rooms = $methodModel->getRoom($methodId);

        $result = [
            'umtr_id' => $method['umtr_id'],
            'probe_number' => $method['probe_number'],
            'material_number' => $method['material_number'],
            'cipher' => $method['cipher'],
            'material_name' => $materialInfo['NAME'],
            'ugtp_id' => $ugtpId,
            'method_id' => $methodId,
            'name' => $methodInfo['view_gost'],
            'cond_temp_1' => $methodInfo['cond_temp_1'],
            'cond_temp_2' => $methodInfo['cond_temp_2'],
            'is_not_cond_temp' => $methodInfo['is_not_cond_temp'],
            'cond_wet_1' => $methodInfo['cond_wet_1'],
            'cond_wet_2' => $methodInfo['cond_wet_2'],
            'is_not_cond_wet' => $methodInfo['is_not_cond_wet'],
            'cond_pressure_1' => $methodInfo['cond_pressure_1'],
            'cond_pressure_2' => $methodInfo['cond_pressure_2'],
            'is_not_cond_pressure' => $methodInfo['is_not_cond_pressure'],
            'rooms' => [],
        ];

        foreach ($rooms as $roomId) {
            $cond = $labModel->getConditionsRoomToday($roomId);
            $room = $labModel->getRoomById($roomId);

            $result['rooms'][] = [
                'room_id' => $roomId,
                'name' => $room['name'],
                'temp' => $cond['temp'],
                'wet' => $cond['humidity'],
                'pressure' => $cond['pressure'],
            ];
        }

        return $result;
    }


    /**
     * Сохраняет комнату температуру влажность и стартует испытание
     * @param $data
     */
    public function saveRoomStart($data)
    {
        $labModel = new Lab();

        foreach ($data as $ugtpId => $item) {
            $roomId = $item['room_id'];
            $sqlData = [
                'ugtp_id' => $ugtpId,
                'room_id' => $roomId,
                'temp' => $item[$roomId]['temp'],
                'wet' => $item[$roomId]['wet'],
                'pressure' => $item[$roomId]['pressure'],
            ];

            $condToday = $labModel->getConditionsRoomToday($roomId);

            if ($item[$roomId]['temp'] != '') {
                if (
                    empty($condToday)
                    || $condToday['temp'] != $item[$roomId]['temp']
                    || $condToday['humidity'] != $item[$roomId]['wet']
                    || $condToday['pressure'] != $item[$roomId]['pressure']
                ) {
                    $labModel->addConditions(
                        [
                            'room_id' => $roomId,
                            'temp' => $item[$roomId]['temp'],
                            'humidity' => $item[$roomId]['wet'],
                            'pressure' => $item[$roomId]['pressure'],
                            'user_id' => $_SESSION['SESS_AUTH']['USER_ID'],
                            'created_at' => date('Y-m-d H:i:s')
                        ]
                    );
                }
            }

            $this->DB->Insert('ulab_gost_room', $sqlData);

            $this->startTrial($ugtpId);
        }
    }

    /**
     * Сохраняет файл измерений
     * @param $ugtpId
     * @param $fileName
     * @param $tmpName
     * @return array
     */
    public function saveMeasurementFile($ugtpId, $fileName, $tmpName)
    {
        $uploadDescFileDir = UPLOAD_DIR . '/result/measurement/' . $ugtpId;

        return $this->saveFile($uploadDescFileDir, $fileName, $tmpName);
    }

    /**
     * @param $ugtpId
     */
    public function getUgtpDescription($ugtpId)
    {
        $result = $this->DB->Query("SELECT description FROM ulab_gost_to_probe WHERE id = '{$ugtpId}'")->Fetch()['description'];

        return ['description' => $result];
    }

    public function saveUgtpDescription($ugtpId, $description)
    {
        $this->DB->Query("UPDATE ulab_gost_to_probe SET description = '{$description}' WHERE id = '{$ugtpId}'");
    }

    public function checkResultsForProbes($dealId)
    {
        $result = true;

        $response = $this->DB->Query(
            "
            SELECT ugtp.*
            FROM ulab_material_to_request umtr
            INNER JOIN ulab_gost_to_probe ugtp ON ugtp.material_to_request_id = umtr.id
            WHERE umtr.deal_id = '{$dealId}'
            "
        );

        while ($row = $response->Fetch()) {
            if (!$row['actual_value'] && strval($row['actual_value']) != '0' && !$row['measuring_sheet']) {
                $result = false;
            }
        }

        return $result;
    }

    public function checkProtocolNumbers($dealId)
    {
        $response = $this->DB->Query("SELECT * FROM PROTOCOLS WHERE DEAL_ID = '{$dealId}' AND INVALID = 0");

        while ($row = $response->Fetch()) {
            if (!$row['NUMBER']) {
                return false;
            }
        }

        return true;
    }
}
