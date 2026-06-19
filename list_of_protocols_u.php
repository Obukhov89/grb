<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$resultSelectBaTz = $DB->Query("SELECT * FROM `ba_tz` WHERE DATE_P_CORR > '2020-12-31' AND `TYPE_TZ` IN (33, 34, 35, 36, 37)");
?>
<h3>Список протоколов упрощенных за 2021 год и подписанных более 1 сотрудником:</h3>
<ol>
<?php
while ($rowSelectBaTz = $resultSelectBaTz->Fetch()) {
	$verify = !empty($rowSelectBaTz['VERIFY']) ? unserialize($rowSelectBaTz['VERIFY']) : '';
	
	if (count($verify) < 2) {
		continue;
	}
	
	$typeTz = !empty($rowSelectBaTz['TYPE_TZ']) ? $rowSelectBaTz['TYPE_TZ'] : '';
	$numPTable = !empty($rowSelectBaTz['NUM_P_TABLE']) ? $rowSelectBaTz['NUM_P_TABLE'] : '';
	$numP = !empty($rowSelectBaTz['NUM_P']) ? $rowSelectBaTz['NUM_P'] : '';
	$requestTitle = !empty($rowSelectBaTz['REQUEST_TITLE']) ? $rowSelectBaTz['REQUEST_TITLE'] : '';
	
	//echo '<li>Протокол № <strong>' . $numPTable . '</strong>, Заявка № ' . $requestTitle . '</li>';
	echo "<li>Протокол № <strong>" . $numPTable . "</strong>, Заявка № <a href='/request_card.php?ID=". $rowSelectBaTz['ID_Z'] ."&ACT_NUM=".$rowSelectBaTz['ACT_NUM']."' target='_blank'>".$rowSelectBaTz['REQUEST_TITLE']."</a></li>";	
	
}
?>

</ol>