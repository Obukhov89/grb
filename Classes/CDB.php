<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

  // require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

  // $APPLICATION->SetTitle("Добавление сотрудника аккредитации");
  // include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

  use \Bitrix\Main\Loader;
  use \Bitrix\Main\Application;
  use \Bitrix\Main;

  // require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/vendor/autoload.php';


class CDB {

  private static $db = null;
  private $connection;
  private $sqlHelper;
  private $sym_query = "{?}";
  private $pattern = '/{\?}/';

  function __construct() {
    try{
      $this->connection = Application::getConnection();
      $this->sqlHelper = $this->connection->getSqlHelper();

      if(!$this->connection) {
        throw new Main\DB\SqlException('Ошибка соединения с БД');
      }
    } catch (Main\DB\SqlException $e) {
      // echo("<script>alert('Невозможно установить соединение с Базой Данных, повторите попытку позже')</script>");
      echo $e->getMessage();
    }
  }

  private function utf8_substr_replace($str, $repl, $start, $length = null){
    preg_match_all('/./us', $str, $ar);
    preg_match_all('/./us', $repl, $rar);

    $length = is_int($length) ? $length : utf8_strlen($str);

    array_splice($ar[0], $start, $length, $rar[0]);

    return implode($ar[0]);
  }


  private function str_replace_once($search, $replace, $text){
    $pos = strpos($text, $search);

    return $pos!==false ? $this->utf8_substr_replace($text, $replace, $pos, strlen($search)) : $text;
  }

  // * Подготовление запросов
  // Вспомогательный метод, который заменяет "символ значения в запросе" на конкретное значение, которое проходит через "функции безопасности"
  private function getQuery($query, $params) {

    if($params) {
      for($i = 0; $i < count($params); $i++) {
        //$args = "'".$params[$i]."'";

		$args = "'".$this->sqlHelper->forSql($params[$i])."'";

        $query = $this->str_replace_once($this->sym_query, $args, $query);
      }
    }
    return $query;
  }

  // ! SELECT МЕТОДЫ ===================
// SELECT-метод, возвращающий таблицу результатов
  public function select($query, $params = false) {
    $t = $this->getQuery($query, $params);
    $result_set = $this->connection->query($t);

    if(!$result_set) {
      return false;
    } else {
      return $this->resultSetMap($result_set);
    }
  }

  // SELECT-метод, возвращающий одну первую строку с результатом
  public function selectRow($query, $params = false) {
    $result_set = $this->connection->query($this->getQuery($query, $params), 1);

    if($result_set>num_rows != 1) {
      return false;
    }else {
      return $result_set->fetch();
    }
  }

  // ! INSERT UPDATE DELETE Методы =================
  public function queryExec($query, $params = false) {



    $t = $this->getQuery($query, $params);
    $success = $this->connection->queryExecute($t);

    /*if($success) {
      if($this->connection->insert_id === 0) {
        return true;
      } else {
        return $this->connection->insert_id;
      }
    }else{
      return false;
    }*/
	
	//при INSERT Вернет ID вставленной записи, при UPDATE вернет 0
	$id_insert = $this->connection->getInsertedId();

	if(!empty($id_insert)) {
		return $id_insert;
	} else {
		return false;
	}
  }

  // ? Преобразование выходных параметров
  private function resultSetMap($result_set) {
    $array = array();

    while($row = $result_set->fetch()) {
      $array[] = $row;
    }

    return $array;
  }

  // закрыть соединение с БД
  public function __destruct() {
    if($this->connection) {
      $this->connection->disconnect();
      $this->connection = false;
      unset($this->connection);
    }
  }
};

// $user = new CDB();
// $tmp = $user->selectRow('SELECT * FROM INVOICE WHERE INVOICE.TZ_ID >= {?} AND SEND_DATE>{?}', array('1', '<<2020-05-03>>'));

// echo('<pre>');
// echo($tmp['DATE']);
// echo('<br>');
// print_r($tmp);
// echo('<br>');
// echo(count($tmp['DATE']));
// echo('-');
// echo(count($tmp));
// echo('</pre>');

// unset($user);

// $user = new CDB();
// $tmp = $user->queryExec("INSERT INTO INVOICE (TZ_ID, DATE, SEND_DATE) VALUES (9999, '2020-12-15 11:11:11', '2020-12-15')");

// echo('<pre>');
// echo($tmp);
// echo('</pre>');

// $user1 = new CDB();
// $tmp = $user1->queryExec("UPDATE INVOICE SET ACTUAL_VER = 'Счет_2020-12-15-11:11:11' WHERE TZ_ID=9999");

// echo('<pre>');
// echo($tmp);
// echo('</pre>');

// $user = new CDB();
// $tmp = $user->queryExec("DELETE FROM INVOICE WHERE TZ_ID=9999");

// echo('<pre>');
// echo($tmp);
// echo('</pre>');

?>
