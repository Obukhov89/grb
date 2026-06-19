<?php

/**
 * Ядро - application
 * Class App
 */
class App
{
    protected $controller = "IndexController";
    protected $method = "index";
    protected $id = '';

    public function __construct()
    {
        $url = $this->parseUrl();

        $controllerName = "index";
        if (!empty($url[0]) && file_exists(APP_PATH . 'controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $controllerName = ucfirst($url[0]);
            $controller = $controllerName . 'Controller';
            $this->controller = $controller;
            unset($url[0]);
        }

        $this->controller = new $this->controller;

        if (isset($url[1])) {
            $method = $url[1];
            if (method_exists($this->controller, $method)) {
                $this->method = $method;
                unset($url[1]);
            }
        }

        if (isset($url[2]) && !empty($url[2])) {
            $tmp = explode('?', $url[2]);
            $this->id = $tmp[0] ?? '';
        }

        // костыль для extranet
        if (in_array($_SESSION['SESS_AUTH']['USER_ID'], [90, 91, 106])) {
            header("Location: /extranet/ulab/production/list/");
        }

        // если нет доступа
        $homePage = '';
        if ( !$this->checkPermission($_SESSION['SESS_AUTH']['USER_ID'], $controllerName, $this->method, $homePage) ) {
            $_SESSION['message_danger'] = "Недостаточно прав для просмотра или действия";
            //TODO: перенаправление
            if (isset($_SESSION['last_uri'])) {
                $lastUri = $_SESSION['last_uri'];
                unset($_SESSION['last_uri']);

//                header("Location: {$lastUri}");
                header("Location: " . URI . $homePage);
            } else {
                header("Location: " . URI . $homePage);
            }
        } else {
            // сохраняем текущую страницу
            $_SESSION['last_uri'] = $_SERVER['REQUEST_URI'];

            call_user_func([$this->controller, $this->method], $this->id);
        }
    }


    protected function parseUrl()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $url = str_replace(URI, '', $_SERVER['REQUEST_URI']);
            $uri = explode('?', $url);
            $uri = explode('/', filter_var(rtrim($uri[0], '/'), FILTER_SANITIZE_URL));
            array_shift($uri);
            return $uri;
        }

        return [];
    }


    /**
     * @param $userId - ид текущего пользователя
     * @param $controller - название контроллера
     * @param $method - название метода
     * @param $homePage
     * @return bool
     */
    protected function checkPermission($userId, $controller, $method, &$homePage)
    {
        global $DB;

        $row = $DB->Query(
            "SELECT distinct p.* 
                    FROM `ulab_permission` as p  
                    LEFT JOIN `ulab_user_permission` as u ON p.id = u.permission_id
                    WHERE u.user_id = {$userId} OR p.id = 1
                    order by p.id desc"
        )->Fetch();

        $row['permission'] = json_decode($row['permission'], true);
        $homePage = $row['home_page'];


        if ( $_SESSION['SESS_AUTH']['USER_ID'] == 61 || $row['permission'] == 'all' ) {
            return true;
        }

		$_SESSION['SESS_AUTH']['ROLE'] = $row['id'];

        return isset($row['permission'][$controller][$method]);
    }
}
