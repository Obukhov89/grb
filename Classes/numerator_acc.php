<?
/**
 * @author Vladislav Satonin
 * @email vlad.satonin74@gmail.com
 * @create date 2021-05-13 13:18:42
 * @modify date 2021-05-13 13:18:42
 * @desc [description]
*/

// ! import
  require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

/**
 * [Description FileException]
 * Создадим свой собственный класс обработки ошибок работы с файлом
 * FileException(1) - ошибка существования файла
 * FileException(2) - файл закрыт на запись
 * FileException(3) - ошибка открытия файла
 * FileException(4) - ошибка записи в файл
 */
class FileException extends Exception {
  /**
   * @param mixed $code
   * @code - код ошибки 1 - 3 (описание кода ошибки выше)
   */
  public function __construct($code){
    parent::__construct('', $code);
  }
}
/**
 * [Description CAccountNumber]
класс нумератор (получение следующего числа при его вызове)
условия: каждый год происходит сброс до 1
вызов: создание экземпляра класса и вызов какой-то функции -> (возвращает цифру в int)
методы класса
  - конструктор класса
    открытие файла на чтение
  - деструктор класса
    закрытие файла на чтение
  - получение числа getNumber()
    проверка текущего числа
      другой метод в классе
      ifDate()
    считывание последнего числа с текстового файла
    autoincrement его и последующая запись обратно в файл
    return ai_число
  - ifDate - проверка текущей даты
    если текущая дата вызова скрипта явяется следующим годом, то перезаписать в файл новое число начиная с 1 + '/y'
  - метод записи в файл setNumberFile(number)
 */
class CAccountNumber {
  // ? переменные класса
  private $path_to_file = '/Classes/number_acc_no_open/test_number_acc.txt'; // путь до файла
  private $handler; // обработчик файла
  private $value_in_file; // значение, лежашие в файле

  // * методы класса
  /** Конструктор файла, инициализируем переменные
   * и открываем файл с флагом rb для чтения чисел
   * выводим ошибки, если таковые имеются
   */
  public function __construct(){
    $this->path_to_file = $_SERVER['DOCUMENT_ROOT'] . $this->path_to_file;
    // открытие файла на чтение
    try{

      if(!file_exists($this->path_to_file)) throw new FileException(1);
      if(!is_writable(($this->path_to_file))) throw new FileException(2);

      $this->handler = fopen($this->path_to_file, 'rb'); // открываем для записи

      if(!$this->handler) throw new FileException(3);

    }catch (FileException $e) {
      // обработка ошибки чтения файла (не найден, не удалось открыть и т.д.)
      switch($e->getCode()){
        case 1:
          echo('Файл не найден <br>');
          break;
        case 2:
          echo('Файл закрыт для записи <br>');
          break;
        case 3:
          echo('Ошибка при открытии файла <br>');
          break;
      }
      echo $e->getMessage();
    }
  }
  /** Проверка даты последнего изменения файла и текущей даты
   * @return 1/0 (При 1 записываем в файл значение 1)
   */
  private function checkDate(){
    $nowDate = new DateTime();
    $mod_date = date('Y', filemtime($this->path_to_file));

    // если текущий год больше последней модификации файла
    // ситуация: если наступил новый год (2022), а файл последний раз изменялся в 2021 году
    // то в файл пишем цифру 1

    // $nowDate_tmp = '2022'; // временно для проверки
    if($nowDate->format('Y') > $mod_date) {
    // if($nowDate_tmp > $mod_date) {
      return 1;
    }else{
      return 0;
    }
  }
  /** Запись числа в файл
   * для этого нужно будет открыть его для записи с флагом w
   * @param mixed $value
   *
   * @return [type]
   */
  private function setValueFromFile($value) {
    $value_from_file = (string)$value;
    // поместим указатель в начало
    fseek($this->handler, 0);
    $this->handler = fopen($this->path_to_file, 'w'); // открываем для записи

    if(fwrite($this->handler, $value_from_file) === FALSE) {
      throw new FileException(4);
    }
  }
  /** Получить число с файла
   * @return Number
   */
  private function __getValueFromFile() {
    $contents = fread($this->handler, filesize($this->path_to_file));
    return $contents;
  }
  /**Публичная функция для получения числа из других файлов
   * главный метод для работы с классом
   * @return [int] - новое число
   */
  public function getValue(){
    $this->value_in_file = (int)$this->__getValueFromFile();
    // проверка даты
    $flag = $this->checkDate();

    if($flag) {
      $new_value_from_file = 1;
    }else{
      $new_value_from_file = $this->value_in_file + 1;
    }
    // записываем новое значение +1 обратно в файл
    $this->setValueFromFile($new_value_from_file);
    // отдаём значение + 1
    return $new_value_from_file;
  }
  /**Деструктор класса, закрываем поток работы с файлом
   * @return [type]
   */
  public function __destruct(){
    fclose($this->handler);
  }

}

// // Пример вызова объекта класса
// $numerAcc = new CAccountNumber();
// $newNumber = $numerAcc->getValue();

// echo("новое число = " . $newNumber);
// echo('<br>');
// echo("тип нового числа = " . gettype($newNumber)); // -> int

// $numerAcc = null; // присваиваем null - закрываем поток с файлом (необязательно, php сам закроет все переменные после завершения работы файла)
?>
