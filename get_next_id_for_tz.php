<?

  include $_SERVER['DOCUMENT_ROOT'].'/Classes/CDB.php';

  if(!empty($_REQUEST['last_id'])){
    $tmp = $_REQUEST['last_id'];

    if($tmp == 1){
      $DB_Conn = new CDB();
      $ins_id = $DB_Conn->select("SHOW TABLE STATUS LIKE 'ba_gost';");
      $next_id = $ins_id[0]['Auto_increment'];
      echo($next_id);
      exit();
    }
  }else{
    echo('пустой');
  }

?>
