<?
/**
 * @author Vladislav Satonin
 * @email vlad.satonin74@gmail.com
 * @create date 2021-05-12 11:06:37
 * @modify date 2021-05-12 11:06:37
 * @desc [description]
 */
// !import
  require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
  include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
  include $_SERVER["DOCUMENT_ROOT"]."/Classes/CDB.php";
  $DB_Conn = new CDB();
  $APPLICATION->SetTitle("Удаление договора");


  // id пришедшего договора с таблицы DOGOVOR
$dogovor_id = $_GET['ID'];
// $deal_id = $_GET['ID_DEAL'];
// $tz_id = $_GET['ID_TZ'];

if(empty($dogovor_id)){
  exit('Страница открыта с ошибкой, пожалуйста, проверить правильность ссылки');
}

$res_select_d_t_c = $DB_Conn->select('SELECT * FROM DEALS_TO_CONTRACTS WHERE ID_CONTRACT = {?}', array($dogovor_id));
$count_dogovor_for_deals = count($res_select_d_t_c);

//? РАЗМЕТКА FRONT
?>
<div>
  <?
  // не отображаем кнопку карточки договора т.к она удалится
    if($count_dogovor_for_deals > 1) {
      ?>
        <a href="/contract_card.php?ID=<?= $dogovor_id ?>">
          <div class='card' title='Карточка договора'></div>
        </a>
      <?
    }
    // не отображаем кнопку карточки заявки если их несколько и если она одна но пустая
    if($count_dogovor_for_deals == 1 && !empty($res_select_d_t_c[0]['ID_DEAL'])) {
      ?>
        <a href="/request_card.php?ID=<?= $res_select_d_t_c[0]['ID_DEAL'] ?>">
          <div class='copy' title='Карточка заявки'></div>
        </a>
      <?
    }
  ?>
  <a href="/dogovor_new.php">
    <div class='list' title='Вернуться к списку договоров'></div><!--Вернуться в журнал договоров-->
  </a>
</div>
<?

$res_select_dog = $DB_Conn->select("SELECT * FROM DOGOVOR WHERE ID = {?}", array($dogovor_id));

$file_path = $_SERVER['DOCUMENT_ROOT'] . "/protocol_generator/archive_dog/" . $res_select_dog[0]['ID'] . '/' . $res_select_dog[0]['ACTUAL_VER'];
$file_path_to_docx = $file_path . '.docx';
$file_path_to_pdf = $file_path . '.pdf';

if($count_dogovor_for_deals > 1) {
  // echo('Удаление невозможно так как текущий договор есть в нескольких сделках');
  // echo('<br>');
  exit('Удаление невозможно так как текущий договор прикреплён к нескольким сделкам');
  // die();
}

$deal_id = $res_select_dog[0]['TZ_ID'];
// если сделка 1 получаем её для определения stage
$select_ba_tz = $DB_Conn->select("SELECT * FROM ba_tz where ID = {?}", array($deal_id));

$stageId = $select_ba_tz[0]['STAGE_ID'];
$stageNumber = $select_ba_tz[0]['STAGE_NUMBER'];

switch($stageId){
  // удовлетворительные стадии (раньше рез.исп)
  case 'NEW':
  case 'PREPARATION':
  case 'PREPAYMENT_INVOICE':
  case 'EXECUTING':
  case 'FINAL_INVOICE':
    // echo('Текущая стадия не превышает допустимую - все хорошо<br>');
    break;
  // не удовлетворительные стадии (дальше рез.исп и другие)
  default:
    exit('Удаление невозможно так как текущая стадия сделки превышает допустимую');
    break;
}
// если условия выше не сработали, значит все хорошо и можно удалить договор
// удаляем его из таблицы DOGOVOR
$del_for_dog = $DB_Conn->queryExec("DELETE FROM DOGOVOR WHERE ID = {?}", array($dogovor_id));
// и DEALS_TO_CONTRACTS
$del_for_dog = $DB_Conn->queryExec("DELETE FROM DEALS_TO_CONTRACTS WHERE ID_CONTRACT = {?}", array($dogovor_id));
// удаляем также файл на сервера
unlink($file_path_to_docx);
unlink($file_path_to_pdf);

echo('<h3>Договор успешно удалён</h3>');

require ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');

?>
