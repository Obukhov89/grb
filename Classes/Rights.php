<?php
if (!class_exists('CDB')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php';
}

class Rights {
    private static $db;

    private static function getList(string $query, array $arr = []): array
    {
        self::$db = new CDB();
        return self::$db->select($query, $arr) ?? [];
    }

    public static function getActionRights(string $action): array
    {
        $query = 'SELECT * FROM `action_rights` WHERE `action`={?}';
        $arr = [$action];
        return self::getList($query, $arr) ?? [];

    }

    public static function getActionRightsBy(string $action, string  $groupId): array
    {
        $query = 'SELECT * FROM `action_rights` WHERE `action`="' . $action . '" AND `group_id` IN (' . $groupId . ')';
        return self::getList($query) ?? [];

    }

    public static function getActionRightsByItem(string $action, string  $groupId, int $itemId): array
    {
        $query = 'SELECT * FROM `action_rights` WHERE `action`="' . $action . '" AND `group_id` IN (' . $groupId . ') AND `item_id`=' . $itemId;
        return self::getList($query) ?? [];

    }

    public static function getGroupId(string $action): array
    {
        $groupsId = [];

        if (empty(self::getActionRights($action))) {
            return [];
        }

        foreach (self::getActionRights($action) as $key => $val) {

            if (!empty($val['group_id'])) {
                $groupsId[] = $val['group_id'];
            }
        }
        return $groupsId;

    }

    public static function check(CUser $USER, string $action): bool
    {

        //TODO: какие действия если для файла не указана группа?
        /*if (empty(self::getGroupId($action))) {
            return false;
        }*/

        if (empty(array_intersect(self::getGroupId($action), $USER->GetUserGroupArray()))) {
            return false;
        }
        return true;
    }

    public static function access(CUser $USER, string $action)//: bool
    {
        $tmp = [];
        $groupsId = $USER->GetUserGroupArray() ? implode(', ', $USER->GetUserGroupArray()) : '';

        $actionRights = self::getActionRightsBy($action, $groupsId);

        foreach ($actionRights as $value) {
            if (!isset($tmp[$value['group_id']])) {
                $tmp[$value['item_id']] = (int)$value['sing'];
            } else {
                $tmp[$value['item_id']] |= (int)$value['sing'];
            }
        }
        
        return $tmp;
    }

    public static function checkingAccess(CUser $USER, string $action, int $itemId): int
    {
        $tmp = [];
        $groupsId = $USER->GetUserGroupArray() ? implode(', ', $USER->GetUserGroupArray()) : '';

        $actionRights = self::getActionRightsByItem($action, $groupsId, $itemId);

        foreach ($actionRights as $value) {
            if (!isset($tmp[$value['item_id']])) {
                $tmp[$value['item_id']] = (int)$value['sing'];
            } else {
                $tmp[$value['item_id']] |= (int)$value['sing'];
            }
        }

        return  $tmp[$itemId] ?? 0;
    }
}

