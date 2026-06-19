<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

//Подключаем модули documentgenerator и crm
Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

if (!empty($_GET['ID_CONTRACT'])) {
	//$idContract = $_POST['id_contract']; //ID договора
	$idContract = $_GET['ID_CONTRACT']; //ID договора
	
	//Находим данные по ID Договора
	$resultDataCurrentContrant = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$idContract)->Fetch();
	$idCompany = !empty($resultDataCurrentContrant['CLIENT_ID']) ? (int)$resultDataCurrentContrant['CLIENT_ID'] : 0; //ID клиента(компании)
	$contractNumber = !empty($resultDataCurrentContrant['NUMBER']) ? $resultDataCurrentContrant['NUMBER'] : $idContract; //Номер договора
	
	//Получаем данные по компании
	$company = $idCompany ? CCrmCompany::GetByID($idCompany) : ''; //Данные компании
	
	//Контрагент (клиент,компания)
	$companyTitle = !empty($company['TITLE']) ? $company['TITLE'] : ''; //Наименование компании
	
	$file = new Bitrix\Main\IO\File(__DIR__ . '/../Protocol_revise.docx');
    $document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
	
	$dealList= []; //список ID всех сделок, привязанных к договору
	
	//Находим связь договора и сделок по ID договора
	$resultDealList = null;
	if (!empty($idContract)) {
		$resultDealList = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_CONTRACT`=" . (int)$idContract);
	}
	
	while($deal = $resultDealList->Fetch())
	{
		$dealList[] = $deal['ID_DEAL'];
	}
	rsort($dealList);
	
	//Итого к оплате
	$totalPayable = 0;
	
	//Итого не оплачено
	$totalNotPaid = 0;
	
	//Общая Сумма фактической оплаты по данной заявке
	$sumProtocolOplata = 0;
	
	if (!empty($dealList)) {
		foreach ($dealList as $keyDealList => $idDeal) {
			if (!empty($idDeal)) {
				//Находим данные ТЗ где есть номер протокола
				$resultSelectTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `NUM_P` !='номер не присвоен' AND `NUM_P` IS NOT NULL AND `ID_Z`=" . (int)$idDeal)->Fetch();
				
				//Если нет такого ТЗ или номер протокола не присвоен, то пропускаем этерацию, не производим подсчет 
				if (empty($resultSelectTz)) {
					continue;
				}
				
				//--добавление номер и дату акта выполненных работ начало--//
				$tzId = !empty($resultSelectTz['ID']) ? (int)$resultSelectTz['ID'] : '';
				
				//Находим данные Акта выполненых работ
				$resAktVr = null;
				if (!empty($tzId)) {
					$resAktVr = $DB->Query("SELECT * FROM `AKT_VR` WHERE `TZ_ID`=" . $tzId)->Fetch();
				}
				
				//Номаер Аата выполненых работ
				$numberAktVr = '';
				
				if (!empty($resAktVr['ID'])) {
					$numberAktVr = $resAktVr['NUMBER'] ? $resAktVr['NUMBER'] : $resAktVr['ID'];
				} else {
					$numberAktVr = 'Не сформирован';
				}
				
				//Дата Акта выполненых работ
				$dateAktVr = !empty($resAktVr['DATE']) ? date("d.m.Y", strtotime($resAktVr['DATE'])) : '';
				
				//--добавление номер и дату акта выполненных работ окончание--//
				
				//Итого к оплате
				$totalPayable += !empty($resultSelectTz['PRICE']) ? $resultSelectTz['PRICE'] : 0;
				
				//Номер протокола
				$protocolNumber = !empty($resultSelectTz['NUM_P']) ? $resultSelectTz['NUM_P'] : 0;
				
				//Дата протокола
				$protocolDate = $resultSelectTz['DATE_P_CORR'] ? $resultSelectTz['DATE_P_CORR'] : $resultSelectTz['DATE_P'];
				
				$protocolDateY = '';
				
				if (!empty($protocolDate)) {
					$protocolDateY = date("Y", strtotime($protocolDate));
					
					//Если дата протокола раньше 2020 года, то обрезаем у года первые 2 цыфры 
					if ((int)$protocolDateY < 2020) { 
						$protocolDateY = "/" . substr($protocolDateY, 2);
					} else {
						$protocolDateY = "/" . $protocolDateY;
					}
				}
				
				//Сумма заявки по данному протоколу
				$protocolPrice = !empty($resultSelectTz['PRICE']) ? $resultSelectTz['PRICE'] : 0;
				
				//Сумма фактической оплаты по данной заявке
				$protocolOplata = !empty($resultSelectTz['OPLATA']) ? $resultSelectTz['OPLATA'] : 0;
				
				//Общая Сумма фактической оплаты по данной заявке
				$sumProtocolOplata += !empty($resultSelectTz['OPLATA']) ? $resultSelectTz['OPLATA'] : 0;
				
				$resultsArray[] = [
				//--добавление номер и дату акта выполненных работ начало--//
					'numberAktVr' => $numberAktVr, //Номаер Аата выполненых работ
					'dateAktVr' => $dateAktVr, //Дата Акта выполненых работ
				//--добавление номер и дату акта выполненных работ окончание--//
					'protocolNumber' => $protocolNumber . $protocolDateY, //Номер протокола
					'protocolDate' => (!empty($protocolDate) ? date('d.m.Y', strtotime($protocolDate)) : ''), //Дата протокола			   
					'protocolPrice' => $protocolPrice, //Сумма заявки по данному протоколу
					'protocolOplata' => $protocolOplata //Сумма фактической оплаты по данной заявке
				];
				
				//Итого не оплачено($totalNotPaid)
				//if (isset($resultSelectTz['OPLATA']) && $resultSelectTz['OPLATA'] == null || $resultSelectTz['OPLATA'] == 0) {
				//	$totalNotPaid += !empty($resultSelectTz['PRICE']) ? $resultSelectTz['PRICE'] : 0;
					//увеличиваем кол-во неоплаченных заявок
					//$countRequestNoPayment++;
				//}
			}
		}
	}
	
	//Итого не оплачено($totalNotPaid)
	$totalNotPaid = $totalPayable - $sumProtocolOplata;
	
	$curdate = 'Акт_сверки_' .date("d-m-Y_H-i-s");
	
	$document->normalizeContent();
	
	$document->setValues([
	'sumProtocolOplata' => $sumProtocolOplata, //Общая Сумма фактической оплаты
	'companyTitle' => $companyTitle, //Наименование компании
	'contractNumber' => $contractNumber, //Номер договора
    'totalPayable' => $totalPayable, //Итого к оплате
	'totalNotPaid' => $totalNotPaid, //Итого не оплачено
	'Results' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $resultsArray,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'ResultsItemProtocolNumber' => 'Results.Item.protocolNumber', //Номер протокола
	'ResultsItemProtocolDate' => 'Results.Item.protocolDate', //Дата протокола											 
    'ResultsItemProtocolPrice' => 'Results.Item.protocolPrice', //Сумма заявки по данному протоколу
	'ResultsItemProtocolOplata' => 'Results.Item.protocolOplata', //Сумма фактической оплаты по данной заявке
	//--добавление номер и дату акта выполненных работ начало--//
	'ResultsItemNumberAktVr' => 'Results.Item.numberAktVr', //Номаер Аата выполненых работ
	'ResultsItemDateAktVr' => 'Results.Item.dateAktVr', //Дата Акта выполненых работ	
	//--добавление номер и дату акта выполненных работ окончание--//
	]);
	
	$result = $document->process();
	$content = $document->getContent();
	
	//Путь сохранения протокола
	$newpath = 'archive_revise/' . $idContract . '/' . $curdate . '.docx';
	
	function file_force_contents( $fullPath, $contents, $flags = 0 ){
		$parts = explode( '/', $fullPath );
		array_pop( $parts );
		$dir = implode( '/', $parts );
	   
		if( !is_dir( $dir ) )
			mkdir( $dir, 0777, true );
	   
		file_put_contents( $fullPath, $contents, $flags );
	}

	file_force_contents($newpath, $content);
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	header('Content-Disposition: attachment;filename="Акт сверки № ' . $idContract . ' от ' . date("d.m.Y") . '.docx"');
	header('Cache-Control: max-age=0');
	echo $content; 
	
}

?>