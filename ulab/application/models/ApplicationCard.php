<?php

class ApplicationCard extends Model
{
    public function getDataToJournal(int $contractorId, ?int $schemeIdFormRequest): array
    {
        if (is_null($schemeIdFormRequest)) {
            $sql = "SELECT * FROM osk_executive_documentation WHERE contractor_id = $contractorId;";

            $data = $this->DB->Query($sql)->Fetch();
            if (!$data) {
                return [];
            }
            $schemeId = $data['scheme_id'];
        } else{
            $schemeId = $schemeIdFormRequest;
        }

        if (!$schemeId) {
            return [];
        }

        $rowsCount = $this->DB->Query(
            "SELECT scheme_info.*, oit.name
                FROM `osk_app_card_scheme_info` scheme_info
                join `osk_id_type` oit on scheme_info.scheme_type_id = oit.id
                where application_card_id = $contractorId and scheme_id = $schemeId and is_hidden = 0
                order by scheme_info.id desc;"
        )->SelectedRowsCount();
        if (!$rowsCount) {
            $this->synchroniseSchemaInformation($contractorId, $schemeId);
        }

        $this->DB->Update("osk_app_card_scheme_info", [
            'is_hidden' => '"' . "1" . '"',
        ], "WHERE application_card_id = $contractorId");
        $this->DB->Update("osk_app_card_scheme_info", [
            'is_hidden' => '"' . "0" . '"',
        ], "WHERE application_card_id = $contractorId and scheme_id = $schemeId");

        $sql = "SELECT scheme_info.*, oit.name
                FROM `osk_app_card_scheme_info` scheme_info
                join `osk_id_type` oit on scheme_info.scheme_type_id = oit.id
                where application_card_id = $contractorId and scheme_id = $schemeId and is_hidden = 0
                order by scheme_info.id desc;";
        $data = $this->DB->Query($sql);

        $result = [];
        $index = 0;
        while ($row = $data->Fetch()) {
            $result[] = [
                'index' => $index,
                'view_id' => $row['name'],
                'checkbox' => $row['checkbox'],
                'photo' => "photo",
                'comment' => $row['comment'] ?? "",
                'contractor_id' => $contractorId,
                'scheme_id' => $schemeId,
                'scheme_type_id' => $row['scheme_type_id'],
                'file_path' => $row['img_path'],
            ];

            $index++;
        }

         return $result;
    }

    protected function uploadFiles(array $files, int $contractorId): array
    {
        $File = new File();

        $filePaths = [];
        foreach ($files as $key => $value) {
            if ($value['size'] == 0) {
                continue;
            }

            $name = htmlspecialchars($value["name"]);
            $tmpName = $value["tmp_name"];
            $uploadPath = "/application_card/";
            $File->uploadFileServer(
                $name,
                $tmpName,
                $uploadPath
            );

            $filePaths[$key] = "/ulab/upload" . $uploadPath . $name;
        }

        return $filePaths;
    }


    public function getFilePath($files, $index, $cardId, $schemeId, $schemeTypeId): ?string
    {
        $data = $this->DB->Query("SELECT * FROM `osk_app_card_scheme_info` WHERE application_card_id = $cardId and scheme_id = $schemeId and scheme_type_id = $schemeTypeId")->Fetch();
        if ($data['img_path']) {
            return $data['img_path'];
        }

        $File = new File();

        if ($files['size'][$index] == 0) {
            return null;
        }

        $name = htmlspecialchars($files["name"][$index]['img']);
        if (!$name) {
            return null;
        }
        $tmpName = $files["tmp_name"][$index]['img'];
        $uploadPath = "/application_card/";
        $File->uploadFileServer($name, $tmpName, $uploadPath);

        return "/ulab/upload" . $uploadPath . $name;
    }

    public function synchroniseSchemaInformation($cardId, $schemeId)
    {
        if (!$schemeId) {
            return;
        }
        $sql = "SELECT * from `osk_isp_doc_types` 
         where card_id = $schemeId and deleted_at is null 
           and type_id not in 
               (select scheme_type_id from `osk_app_card_scheme_info` where application_card_id = $cardId and scheme_id = $schemeId);";

        $data = $this->DB->Query($sql);
        if ($data) {
            while ($row = $data->Fetch()) {
                $this->DB->Insert("osk_app_card_scheme_info", ['application_card_id' => $cardId, 'scheme_id' => $schemeId, 'scheme_type_id' => $row['type_id'],]);
            }
        }
    }

    public function update(array $attributes)
    {
        $contractorId = $attributes['contractorId'];
        $schemeId = $attributes['scheme_id'];

//        $filePaths = $this->uploadFiles($_FILES, $contractorId);

        $dataToUpdate = [//            'executive_scheme' => filter_var($attributes['executive_scheme'], FILTER_VALIDATE_BOOLEAN),
//            'materials_used' => filter_var($attributes['materials_used'], FILTER_VALIDATE_BOOLEAN),
//            'quality_document' => filter_var($attributes['quality_document'], FILTER_VALIDATE_BOOLEAN),
//            'executive_scheme_comment' => $attributes['executive_scheme_comment'],
//            'materials_used_comment' => $attributes['materials_used_comment'],
//            'quality_document_comment' => $attributes['quality_document_comment'],
            'general_comment' => $attributes['general_comment'], 'scheme_id' => $schemeId,];

//        foreach ($filePaths as $key => $value) {
//            $dataToUpdate[$key] = $value;
//        }
        $this->synchroniseSchemaInformation($contractorId, $schemeId);

        foreach ($dataToUpdate as $key => $value) {
            $dataToUpdate[$key] = empty($value) ? "" : '"' . $this->DB->ForSql($value) . '"';
        }

        $this->DB->Update("osk_executive_documentation", $dataToUpdate, "WHERE contractor_id = $contractorId");


        foreach ($_POST['schema_info'] as $schemaInfo) {
            $filePath = $this->getFilePath($_FILES['schema_info'], $schemaInfo['index'], $contractorId, $schemeId, $schemaInfo['scheme_type_id']);

            $attrs = [
                'img_path' => "'" . $filePath . "'",
                'comment' => "'" . $schemaInfo['comment']  . "'",
                'checkbox' => "'" . filter_var($schemaInfo['checkbox'], FILTER_VALIDATE_BOOLEAN) . "'"
            ];

            $this->DB->Update("osk_app_card_scheme_info", $attrs, "WHERE application_card_id = $contractorId and scheme_id = $schemeId and scheme_type_id = {$schemaInfo['scheme_type_id']}");
        }

        return 0;
    }

    public function closeCard(int $cardId): bool
    {
        $data = [
            'closed' => 1,
        ];

        return $this->DB->Update("osk_executive_documentation", $data, "WHERE contractor_id = {$cardId}");
    }

    public function openCard(int $cardId): bool
    {
        $data = [
            'closed' => 0,
        ];

        return $this->DB->Update("osk_executive_documentation", $data, "WHERE contractor_id = {$cardId}");
    }
}