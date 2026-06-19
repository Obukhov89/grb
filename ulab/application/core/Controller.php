<?php

/**
 * Ядро - контроллер
 * Class Controller
 */
class Controller
{
    /** @var array $data */
    public $data;
    public $contentView;

    public $messageError = '';
    public $messageSuccess = '';
    public $messageWarning = '';

    private $addedJS     = [];
    private $addedCDN     = [];
    private $addedCSS    = [];
    private $topNavMenu  = [];

    public function __construct()
    {
        $this->data['currentUser']['id'] = $_SESSION['SESS_AUTH']['USER_ID'];
    }

    protected static function getClassName(): string
    {
        return get_called_class();
    }

    protected function redirect($location, $code = 302)
    {
        header("Location: ".URI."{$location}", true, $code);

        exit;
    }

    protected function model($model)
    {
        return new $model();
    }

    protected function view($view = 'index', $dir = '', $template = "template_view")
    {
        global $APPLICATION;

        /** @var Permission $permissionModel */
        $permissionModel = $this->model('Permission');

        $permissionInfo = $permissionModel->getUserPermission($_SESSION['SESS_AUTH']['USER_ID']);
        $viewName = $permissionInfo['view_name']?? '';

        if ( empty($dir) ) {
            $dir = strtolower(str_replace('Controller', '', $this::getClassName()));
        }


        if ( !empty($viewName) && file_exists(APP_PATH . "views/{$dir}/{$view}_{$viewName}.php") ) {
            $this->contentView = "{$dir}/{$view}_{$viewName}.php";
        } else {
            $this->contentView = "{$dir}/{$view}.php";

            if ( !file_exists(APP_PATH . 'views/' . $this->contentView) ) {
                // TODO:  редирект на страницу с ошибкой
            }
        }

        if ( isset($this->data['title']) ) {
            $APPLICATION->SetTitle($this->data['title']);
        }

        if ( isset($_SESSION['message_danger']) ) {
            $this->messageError = $_SESSION['message_danger'];
        }
        if ( isset($_SESSION['message_success']) ) {
            $this->messageSuccess = $_SESSION['message_success'];
        }
        if ( isset($_SESSION['message_warning']) ) {
            $this->messageWarning = $_SESSION['message_warning'];
        }

        if ( file_exists(APP_PATH . "views/template_view.php") ) {
            require(APP_PATH . "views/template_view.php");
        } else {
            include APP_PATH.'views/'.$this->contentView;
        }

		unset($_SESSION['message_danger']);
		unset($_SESSION['message_success']);
		unset($_SESSION['message_warning']);
		unset($this->data);
		unset($this->addedCSS);
		unset($this->addedJS);
		unset($this->topNavMenu);
		unset($_POST);

        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");

    }

    protected function viewEmpty($view = 'index', $dir = '')
    {
        global $APPLICATION;

        if ( empty($dir) ) {
            $dir = strtolower(str_replace('Controller', '', $this::getClassName()));
        }

        $this->contentView = "{$dir}/{$view}.php";

        if ( !file_exists(APP_PATH . 'views/' . $this->contentView) ) {
            // TODO:  редирект на страницу с ошибкой
        }

        if ( isset($_SESSION['message_danger']) ) {
            $this->messageError = $_SESSION['message_danger'];
        }
        if ( isset($_SESSION['message_success']) ) {
            $this->messageSuccess = $_SESSION['message_success'];
        }
        if ( isset($_SESSION['message_warning']) ) {
            $this->messageWarning = $_SESSION['message_warning'];
        }

        if ( file_exists(APP_PATH . "views/template_empty.php") ) {
            require(APP_PATH . "views/template_empty.php");
        } else {
            include APP_PATH.'views/'.$this->contentView;
        }

        unset($_SESSION['message_danger']);
        unset($_SESSION['message_success']);
        unset($_SESSION['message_warning']);
        unset($this->data);
        unset($this->addedCSS);
        unset($this->addedJS);
        //unset($this->topNavMenu);
        unset($_POST);
    }

    protected function showErrorMessage( $msg )
    {
        $_SESSION['message_danger'] = $msg;
    }

    protected function showSuccessMessage( $msg )
    {
        $_SESSION['message_success'] = $msg;
    }

    /**
     * @param string $msg
     */
    protected function showWarningMessage(string $msg)
    {
        $_SESSION['message_warning'] = $msg;
    }

    /**
     * проверка валидности почты
     * @param string $email
     * @param int $maxLength - 0 - unlimited
     * @param bool $required
     * @return array
     */
    protected function validateEmail(string $email, $required = true, $maxLength = 255): array
    {
        if ( $email !== '' ) {
            if ( $maxLength > 0 && strlen($email) > $maxLength ) {
                return $this->response(false, [], "Превышена максимальная допустимая длинна в {$maxLength} символов.");
            }

            // проверка наличия '@' и '.'
            $pattern = "/.+@.+\..+/i";
            if ( preg_match($pattern, $email) !== 1 ) {
                return $this->response(false, [], "Некорректный E-mail.");
            }

        } elseif ( $required ) {
            return $this->response(false, [], "E-mail обязателен для заполнения и не может быть пустым.");
        }

        return $this->response(true);
    }

    /**
     * проверка поля
     * @param string $text
     * @param string $fieldName
     * @param int $maxLength - 0 - unlimited
     * @param bool $required
     * @return array
     */
    protected function validateField($text, string $fieldName, $required = true, $maxLength = 255): array
    {
        if ( $text !== '' ) {
            if ( $maxLength > 0 && strlen($text) > $maxLength ) {
                return $this->response(false, [], "У поля {$fieldName} превышена максимальная допустимая длинна в {$maxLength} символов.");
            }
        } elseif ( $required ) {
            return $this->response(false, [], "Поле {$fieldName} обязательно для заполнения и не может быть пустым.");
        }

        return $this->response(true);
    }

    /**
     * @param int|string|double $number
     * @param string $fieldName
     * @param bool $required
     * @return array
     */
    protected function validateNumber($number, string $fieldName, bool $required = true, $maxLength = 255): array
    {
        if ( $number !== ''  ) {
            if ( $maxLength > 0 && strlen($number) > $maxLength ) {
                return $this->response(false, [], "У поля {$fieldName} превышена максимальная допустимая длинна в {$maxLength} символов.");
            }
            if ( !is_numeric($number) ) {
                return $this->response(false, [], "Поле {$fieldName} не является числом");
            }
        } elseif ( $required ) {
            return $this->response(false, [], "Поле {$fieldName} обязательно для заполнения и не может быть пустым.");
        }

        return $this->response(true);
    }


    /**
     * @param $date
     * @param string $fieldName
     * @param bool $required
     * @return array
     */
    protected function validateDate($date, string $fieldName, bool $required = true): array
    {
        if ( $date !== ''  ) {
            if ( !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date) ) {
                return $this->response(false, [], "Поле {$fieldName} не является датой");
            }
        } elseif ( $required ) {
            return $this->response(false, [], "Поле {$fieldName} обязательно для заполнения и не может быть пустым.");
        }

        return $this->response(true);
    }


    /**
     * Стандартизированный ответ на всё
     * @param bool $success
     * @param array $data
     * @param string $errorMsg
     * @return array
     */
    protected function response(bool $success, array $data = [], string $errorMsg = 'Неизвестная ошибка'): array
    {
        return [
            'success' => $success,
            'data'    => $data,
            'error'   => $errorMsg,
        ];
    }


    /**
     * @param $path
     */
    protected function addJS($path)
    {
        $this->addedJS[] = $path;
    }

	/**
	 * @param $path
	 */
	protected function addCDN($path)
	{
		$this->addedCDN[] = $path;
	}


    /**
     * @param $path
     */
    protected function addCSS($path)
    {
        $this->addedCSS[] = $path;
    }


    /**
     * @param $href
     * @param string $class
     * @param string $iconClass
     */
    protected function addTopMenu($href, $class = '', $iconClass = '')
    {
        $this->topNavMenu[] = [
            'href' => $href,
            'class' => $class,
            'icon_class' => $iconClass,
        ];
    }


    /**
     * @return array
     */
    protected function getJS()
    {
        return $this->addedJS;
    }

	/**
	 * @return array
	 */
	protected function getCDN()
	{
		return $this->addedCDN;
	}


    /**
     * @return array
     */
    protected function getCSS()
    {
        return $this->addedCSS;
    }
}
