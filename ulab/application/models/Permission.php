<?php

/**
 * Класс для работы с доступами пользователей
 * Class Permission
 */
class Permission extends Model
{

    /**
     * @desc Получает примечание у класса или метода
     * @param ReflectionClass|ReflectionMethod $object
     * @return string|mixed
     */
    protected function getDescFromDocDocumentByObject($object)
    {
        $doc = $object->getDocComment();

        $match = [];
        preg_match('#@desc(.*)#', $doc, $match);

        return $match[1]?? '';
    }


    /**
     * @desc Получает список доступов
     * @return array
     */
    public function getPermission()
    {
        $sql = $this->DB->Query("SELECT * FROM `ulab_permission`");

        $result = [];

        while ($row = $sql->Fetch()) {
            $row['permission'] = json_decode($row['permission'], true);
            $result[] = $row;
        }

        return $result;
    }


    /**
     * @desc Обновляет доступы группе
     * @param $permissionId
     * @param $data
     * @return bool|int|string
     */
    public function setPermission($permissionId, $data)
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $d = [
            'results' => $this->quoteStr("{$json}"),
        ];

        $where = "WHERE id = {$permissionId}";
        return $this->DB->Update('ulab_permission', $d, $where);
    }


    /**
     * @desc Получает доступы конкретного пользователя
     * @param $userId
     * @return array|false
     */
    public function getUserPermission($userId)
    {
        $where = '';
        //проверить
        if (empty($userId)) {
            $where = 'OR p.id = 1';
        }
        $row = $this->DB->Query(
            "SELECT p.* 
                    FROM `ulab_permission` as p  
                    LEFT JOIN `ulab_user_permission` as u ON p.id = u.permission_id
                    WHERE u.user_id = {$userId} {$where}")->Fetch();

        $row['permission'] = json_decode($row['permission'], true);

        return $row;
    }


    /**
     * @desc получает список контроллеров и их методы
     * @return array - [контроллер][метод]
     */
    public function getControllerMethod()
    {
        $controllers = $this->getFilesFromDir(APP_PATH . '/controllers/');

        $result = [];

        foreach ($controllers as $file) {
            require_once(APP_PATH . '/controllers/' . $file);

            $match = [];
            preg_match('#((.*)Controller)\.php$#', $file, $match);

            $reflector = new ReflectionClass($match[1]);

            $methods = get_class_methods($match[1]);

            $resultMethods = [];
            foreach ($methods as $method) {
                $descMethod = $this->getDescFromDocDocumentByObject($reflector->getMethod($method));
                $resultMethods[] = [
                    'name' => $method,
                    'desc' => $descMethod,
                ];
            }

            $descClass = $this->getDescFromDocDocumentByObject($reflector);
            $result[] = [
                'name' => $match[2],
                'desc' => $descClass,
                'methods' => $resultMethods,
            ];

        }

        return $result;
    }


    /**
     * @desc Получает информацию о роли
     * @param $roleId
     * @return array|false
     */
    public function getRoleInfo($roleId)
    {
        $row = $this->DB->Query("SELECT * FROM `ulab_permission` WHERE id = {$roleId}")->Fetch();

        $row['permission'] = json_decode($row['permission'], true);

        return $row;
    }


    /**
     * @param $userId
     * @return int
     */
    public function getUserRole($userId)
    {
        $row = $this->DB->Query("SELECT permission_id FROM `ulab_user_permission` WHERE user_id = {$userId}")->Fetch();

        if ( empty($row) ) {
            return 1;
        } else {
            return (int)$row['permission_id'];
        }
    }


    /**
     * @desc Обновляет доступы у роли
     * @param $roleId
     * @param $data
     */
    public function updateRole($roleId, $data)
    {
        $where = "WHERE id = {$roleId}";

        $d = [
            'home_page' => $this->quoteStr($this->DB->ForSql($data['home_page'])),
            'permission' => $this->quoteStr($this->DB->ForSql(json_encode($data['permission']))),
        ];

        $this->DB->Update('ulab_permission', $d, $where);
    }


    /**
     * @desc Обновляет роль у пользователя
     * @param $userId
     * @param $roleId
     */
    public function updateUser($userId, $roleId)
    {
        $this->DB->Query("REPLACE `ulab_user_permission` SET `user_id` = {$userId}, `permission_id` = {$roleId}");
    }

    /**
     * @desc Функция проверки прав доступа у определённой роли
     * Данную функцию необходимо запускать в начале любой функции, к которой нужно сделать выборочный доступ
     * @hide true
     */
    public function checkPermission($function, $f) {
        global $USER;

        $file = str_replace('Controller', '', basename($f, '.php'));
        $userId = $USER->GetID();

        $permissionData = $this->getUserPermission($userId);

        $permissions = $permissionData['permission'];
        $homePage = $permissionData['home_page'];
        $roleName = $permissionData['name'];
        $roleViewName = $permissionData['view_name'];
        $isGostRole = $permissionData['is_gost'] == '1' ? true : false;

        $permissionsAll = $permissions;


        $replacementUser = $this->getReplacementUserId($userId);
        if(isset($replacementUser)) {
            $permissionDataReplacement = $this->getUserPermission($replacementUser);
            $rolePermissionReplacement = $permissionDataReplacement['permission'];
            $roleViewNameReplacement = $permissionDataReplacement['view_name'];
            $isGostRoleReplacement = $permissionDataReplacement['is_gost'] == '1' ? true : false;

            $permissionsAll = array_merge([0 => $permissions], [$rolePermissionReplacement]);

            if ($roleViewNameReplacement == 'admin')
                $roleViewName = 'admin';

            if ($isGostRoleReplacement)
                $isGostRole = true;
        }

        if (!($roleViewName == 'admin' || $isGostRole || (isset($permissionsAll[$file]) && isset($permissionsAll[$file][$function]) && $permissionsAll[$file][$function] === "1") )) {
            $this->showWarningMessage('У пользователя с ролью "'.$roleName.'"' . ' недостаточно прав. Вы были возвращены на главную.');
            header("Location: $homePage");
            exit();
        }
        return true;
    }

    public function getReplacementUserId($userId) {
        $replacementUser = $this->DB->Query("SELECT replacement_user_id as REPLACEMENT_USER_ID FROM ulab_user_status WHERE user_id = $userId")->Fetch()['REPLACEMENT_USER_ID'];
        return $replacementUser ?? null;
    }

    /**
     * @param array $filter
     * @return array
     * @hide true
     */
    public function getDatatoJournalUsers(array $filter = []) {
        $where = "";
        $whereDepartment = "";
        $limit = "";
        $order = [
            'by' => 'FULL_NAME',
            'dir' => 'DESC'
        ];

//        $this->pre($filter['search']);

        if ( !empty($filter['search']) ) {
            foreach ($filter['search'] as $key => $value) {
                $filter['search'][$key] = $this->DB->ForSql(trim($value));
            }

            // ФИО
            if (isset($filter['search']['FULL_NAME'])) {
                $where .= "CONCAT(u.LAST_NAME, ' ', u.NAME, ' ', u.SECOND_NAME) LIKE '%{$filter['search']['FULL_NAME']}%' AND ";
            }
            // Отдел
            if (isset($filter['search']['WORK_DEPARTMENT'])) {
                if($filter['search']['WORK_DEPARTMENT'] == 'Отдел не указан') {
                    $whereDepartment .= "(WORK_DEPARTMENT LIKE '%{$filter['search']['WORK_DEPARTMENT']}%' OR WORK_DEPARTMENT IS NULL OR WORK_DEPARTMENT = '') AND ";
                }else {
                    $where .= "(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(b_dep_data.UF_DEPARTMENT, 'i:', -1), ';', 1) AS SIGNED) = {$filter['search']['WORK_DEPARTMENT']}) AND ";
                }
            }
            // Логин
            if (isset($filter['search']['LOGIN'])) {
                $where .= "LOGIN LIKE '%{$filter['search']['LOGIN']}%' AND ";
            }
            // Почта
            if (isset($filter['search']['EMAIL'])) {
                $where .= "EMAIL LIKE '%{$filter['search']['EMAIL']}%' AND ";
            }
            // Должность
            if (isset($filter['search']['WORK_POSITION'])) {
                $where .= "(u.WORK_POSITION LIKE '%{$filter['search']['WORK_POSITION']}%' OR u.WORK_POSITION IS NULL OR u.WORK_POSITION = '') AND ";
            }
            // Роль
            if (isset($filter['search']['permission_name'])) {
                $where .= "(COALESCE(perm.name, 'По умолчанию') LIKE '%{$filter['search']['permission_name']}%' OR COALESCE(perm.name, 'По умолчанию') IS NULL OR perm.name = '') AND ";
            }
        }

        if (!empty($filter['order'])) {
            if ($filter['order']['dir'] === 'asc') {
                $order['dir'] = 'ASC';
            }
            switch ($filter['order']['by']) {
                case 'FULL_NAME':
                    $order['by'] = "CASE WHEN FULL_NAME IS NULL OR FULL_NAME = '' THEN 1 ELSE 0 END, FULL_NAME";
                    break;
                case 'LOGIN':
                    $order['by'] = "CASE WHEN LOGIN IS NULL OR LOGIN = '' THEN 1 ELSE 0 END, LOGIN";
                    break;
                case 'EMAIL':
                    $order['by'] = "CASE WHEN EMAIL IS NULL OR EMAIL = '' THEN 1 ELSE 0 END, EMAIL";
                    break;
                case 'WORK_DEPARTMENT':
                    $order['by'] = "CASE WHEN WORK_DEPARTMENT IS NULL OR WORK_DEPARTMENT = '' THEN 1 ELSE 0 END, WORK_DEPARTMENT";
                    break;
                case 'WORK_POSITION':
                    $order['by'] = "CASE WHEN u.WORK_POSITION IS NULL OR u.WORK_POSITION = '' THEN 1 ELSE 0 END, u.WORK_POSITION";
                    break;
                case 'permission_name':
                    $order['by'] = "CASE WHEN permission_name IS NULL OR permission_name = '' THEN 1 ELSE 0 END, permission_name";
                    break;
                default:
                    $order['by'] = 'b_id';
            }
        }

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
        $whereDepartment .= "1 ";

        $data = $this->DB->Query("
            SELECT *
            FROM (
                SELECT 
                    group_concat(bl.ID) as IS_HEAD_DEPT,
                    u.ID,
                    u.NAME,
                    u.LAST_NAME,
                    u.LOGIN, 
                    u.EMAIL,
                    u.SECOND_NAME,
                    CONCAT(u.LAST_NAME, ' ', u.NAME, ' ', u.SECOND_NAME) AS FULL_NAME,
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(b_dep_data.UF_DEPARTMENT, 'i:', -1), ';', 1) AS SIGNED) AS DEPARTMENT_ID,
                    bui.UF_DEPARTMENT_NAME as WORK_DEPARTMENT_OLD,
                    COALESCE(
                        (
                            SELECT `BL`.NAME
                            FROM `ba_laba` as `BL` 
                            WHERE `BL`.id_dep = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(b_dep_data.UF_DEPARTMENT, 'i:', -1), ';', 1) AS SIGNED)
                        ),
                        (
                            SELECT `BL`.NAME
                            FROM `ba_laba` as `BL` 
                            WHERE `BL`.id_dep = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(b_dep_data.UF_DEPARTMENT, 'i:', -1), ';', 1) AS SIGNED)
                        ),
                        'Отдел не указан'
                    ) AS WORK_DEPARTMENT,
                    CASE
                        WHEN u.WORK_POSITION IS NULL OR u.WORK_POSITION = '' THEN 'Должность не указана'
                        ELSE u.WORK_POSITION
                    END AS WORK_POSITION,
                    p.permission_id,
                    CASE
                        WHEN perm.name IS NULL OR perm.name = '' THEN 'По умолчанию'
                        ELSE perm.name
                    END AS permission_name,
                    perm.view_name as permission_view_name
                FROM
                    b_user u
                LEFT JOIN
                    ulab_user_permission p ON u.ID = p.user_id
                LEFT JOIN
                    ulab_permission perm ON p.permission_id = perm.id
                LEFT JOIN
                    (
                        SELECT VALUE_ID, UF_DEPARTMENT 
                        FROM b_uts_user 
                    ) AS b_dep_data ON b_dep_data.VALUE_ID = u.ID
                LEFT JOIN
                    b_user_index bui ON bui.USER_ID = u.ID 
                LEFT JOIN 
                    ba_laba bl ON bl.HEAD_ID = bui.USER_ID
                WHERE
                    u.ACTIVE = 'Y' AND {$where}
                GROUP BY u.ID
                ORDER BY
                    {$order['by']} {$order['dir']} {$limit}
                ) AS subquery
            WHERE $whereDepartment;
        ");

        $dataTotal = (int)$this->DB->Query("
            SELECT count(*) as val
                FROM (
                      SELECT
                        u.ID,
                        u.NAME,
                        u.LAST_NAME,
                        u.LOGIN, 
                        u.EMAIL,
                        u.SECOND_NAME,
                        CONCAT(u.LAST_NAME, ' ', u.NAME, ' ', u.SECOND_NAME) AS FULL_NAME,
                        CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(b_dep_data.UF_DEPARTMENT, 'i:', -1), ';', 1) AS SIGNED) AS DEPARTMENT_ID,
                        bui.UF_DEPARTMENT_NAME as WORK_DEPARTMENT_OLD,
                        COALESCE(
                        (
                            SELECT `BL`.NAME
                            FROM `ba_laba` as `BL` 
                            WHERE `BL`.id_dep = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(b_dep_data.UF_DEPARTMENT, 'i:', -1), ';', 1) AS SIGNED)
                        ),
                        (
                            SELECT `BL`.NAME
                            FROM `ba_laba` as `BL` 
                            WHERE `BL`.id_dep = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(b_dep_data.UF_DEPARTMENT, 'i:', -1), ';', 1) AS SIGNED)
                        ),
                        'Отдел не указан'
                        ) AS WORK_DEPARTMENT,
                        CASE
                            WHEN u.WORK_POSITION IS NULL OR u.WORK_POSITION = '' THEN 'Должность не указана'
                            ELSE u.WORK_POSITION
                        END AS WORK_POSITION,
                        p.permission_id,
                        CASE
                            WHEN perm.name IS NULL OR perm.name = '' THEN 'По умолчанию'
                            ELSE perm.name
                        END AS permission_name
                    FROM
                        b_user u
                    LEFT JOIN
                        ulab_user_permission p ON u.ID = p.user_id
                    LEFT JOIN
                        ulab_permission perm ON p.permission_id = perm.id
                    LEFT JOIN
                        b_user_index bui ON bui.USER_ID = u.ID 
                    LEFT JOIN
                    (
                        SELECT VALUE_ID, UF_DEPARTMENT 
                        FROM b_uts_user 
                    ) AS b_dep_data ON b_dep_data.VALUE_ID = u.ID
                    WHERE
                        u.ACTIVE = 'Y'
                    GROUP BY
                        u.ID
            ) subquery"
        )->Fetch()['val'];

        $dataFiltered = (int)$this->DB->Query("
            SELECT count(*) as val
                FROM (
                      SELECT
                        u.ID,
                        u.NAME,
                        u.LAST_NAME,
                        u.LOGIN, 
                        u.EMAIL,
                        u.SECOND_NAME,
                        CONCAT(u.LAST_NAME, ' ', u.NAME, ' ', u.SECOND_NAME) AS FULL_NAME,
                        CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(b_dep_data.UF_DEPARTMENT, 'i:', -1), ';', 1) AS SIGNED) AS DEPARTMENT_ID,
                        bui.UF_DEPARTMENT_NAME as WORK_DEPARTMENT_OLD,
                        COALESCE(
                        (
                            SELECT `BL`.NAME
                            FROM `ba_laba` as `BL` 
                            WHERE `BL`.id_dep = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(b_dep_data.UF_DEPARTMENT, 'i:', -1), ';', 1) AS SIGNED)
                        ),
                        (
                            SELECT `BL`.NAME
                            FROM `ba_laba` as `BL` 
                            WHERE `BL`.id_dep = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(b_dep_data.UF_DEPARTMENT, 'i:', -1), ';', 1) AS SIGNED)
                        ),
                        'Отдел не указан'
                        ) AS WORK_DEPARTMENT,
                        CASE
                            WHEN u.WORK_POSITION IS NULL OR u.WORK_POSITION = '' THEN 'Должность не указана'
                            ELSE u.WORK_POSITION
                        END AS WORK_POSITION,
                        p.permission_id,
                        CASE
                            WHEN perm.name IS NULL OR perm.name = '' THEN 'По умолчанию'
                            ELSE perm.name
                        END AS permission_name
                    FROM
                        b_user u
                    LEFT JOIN
                        ulab_user_permission p ON u.ID = p.user_id
                    LEFT JOIN
                        ulab_permission perm ON p.permission_id = perm.id
                    LEFT JOIN
                        b_user_index bui ON bui.USER_ID = u.ID 
                    LEFT JOIN
                    (
                        SELECT VALUE_ID, UF_DEPARTMENT 
                        FROM b_uts_user 
                    ) AS b_dep_data ON b_dep_data.VALUE_ID = u.ID
                    WHERE
                        u.ACTIVE = 'Y' AND {$where}
                    GROUP BY
                        u.ID
            ) subquery
            WHERE $whereDepartment;"
        )->Fetch()['val'];

        while ($row = $data->Fetch()) {
            $result[] = $row;
        }

        $result['recordsTotal'] = $dataTotal;
        $result['recordsFiltered'] = $dataFiltered;

        return $result;
    }
}