<?php

class Viewer extends Model {

    public function insertUpdateView($userId, $moduleId, $moduleName)
    {
        $userId = $_SESSION['SESS_AUTH']['USER_ID'];

        $sql = "INSERT IGNORE INTO module_viewed (user_id, module_id, module_name, created_at) 
                VALUES ({$userId}, {$moduleId}, '{$moduleName}', now())";

        return $this->DB->Query($sql);
    }

    public function deleteView($moduleId, $moduleName)
    {
        $sql = "DELETE FROM module_viewed 
                WHERE module_id = {$moduleId} AND module_name = '{$moduleName}'";

        return $this->DB->Query($sql);
    }

    public function getView($userId, $moduleId, $moduleName)
    {
        return $this->DB->Query("
                    SELECT * FROM module_viewed 
                    WHERE user_id = {$userId} AND module_id = {$moduleId} AND module_name = '{$moduleName}'
               ")->fetch();
    }
}