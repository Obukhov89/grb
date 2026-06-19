<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

echo 1;

$imap = imap_open ("{imap.yandex.ru:993/imap/ssl}INBOX", "AlexKov123456@yandex.ru", "Kov123456") 
     or die("can't connect: " . imap_last_error());

$mails_id = imap_search($imap, 'TO "fssdfsfsd@jfefuyweuf.few"'); 

foreach ($mails_id as $key => $num) {

	//$header = imap_header($imap, $num);
	//var_dump($header);
 

echo'====================================================================================================';

	//$body = imap_body($imap, $num);
	//var_dump($body);
	
}
 
imap_close($imap);
	 

?>