<?php
//Файл рандомного расчета и заполнения расхода реактивов (первый вариант, устаревший)
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал верификации");
echo'<pre>';var_dump(11111111111);echo'</pre>';

$resultSelectAllReagentsFlow = $DB->Query("SELECT * FROM `REAGENTS_FLOW`");

while($resultReagentsFlow = $resultSelectAllReagentsFlow->Fetch()) {
echo'<pre>';var_dump(2222222222);echo'</pre>';	
	
	$count = 0;
	$idReagents = !empty($resultReagentsFlow['ID']) ? $resultReagentsFlow['ID'] : '';
	$idReag = !empty($resultReagentsFlow['ID_REAG']) ? $resultReagentsFlow['ID_REAG'] : '';
	$idGostReagents = !empty($resultReagentsFlow['ID_GOST']) ? $resultReagentsFlow['ID_GOST'] : '';
	$flowMin = !empty($resultReagentsFlow['FLOW_MIN']) ? $resultReagentsFlow['FLOW_MIN'] : '';
	$flowMax = !empty($resultReagentsFlow['FLOW_MAX']) ? $resultReagentsFlow['FLOW_MAX'] : '';
	$flowMean = !empty($flowMin) && !empty($flowMax) ? $flowMin + (($flowMax - $flowMin) / 2) : '';
	
	$baGost = null;
	if (!empty($idGostReagents)) {
		$baGost = $DB->Query("SELECT * FROM `ba_gost` WHERE `NON_ACTUAL`!=1 AND `ID`=" . $idGostReagents)->Fetch();
	}
	
	$gostReagent = !empty($baGost['GOST']) ? $baGost['GOST'] : ''; 
	
	$resultBaTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `DATE_P_CORR` > '2019-12-31' ORDER BY `DATE_P_CORR`");
	
	while($rowBaTz = $resultBaTz->Fetch())
	{
		$tz = !empty($rowBaTz['TZ']) ? unserialize($rowBaTz['TZ']) : '';
		$dateP = !empty($rowBaTz['DATE_P']) ? $rowBaTz['DATE_P'] : '';
		$dateProtocol = !empty($rowBaTz['DATE_P_CORR']) ? $rowBaTz['DATE_P_CORR'] : $rowBaTz['DATE_P'];
		$dateСoming = date("Y") . '-01-01';
		
		if (empty($dateProtocol)) {
			continue;
		}
		
		foreach($tz as $keyTz => $valTz) {
			
			foreach($valTz as $key => $val) {
				
				if (!empty($key) && $key == 'gost') {
					
					
					if (!empty($val) && in_array($idGostReagents, $val)) {
						
						foreach($val as $keyGost => $valGost) {
							if (!empty($valGost) && $valGost == $idGostReagents) { 
							
								
								
								$count++;
							}
						}
					}
				}
			}
		}
	}

	$countSpentYear = $count * $flowMean;
	
	$remainder = $countSpentYear;
	$ff++;
	
	$resultСoming = $DB->Query("INSERT INTO `REACTIVE` (ID_REACT, DATE_POSTUP, PRIKHOD, NUMBER_PART, OSTATOK) 
	VALUES ('" . $idReag . "', '" . $dateСoming . "', " . (float)$countSpentYear . ", '" . $gostReagent . "', " . (float)$remainder . ")");
	
	if (!empty($idReag)) {
		//$resOst = $DB->Query("SELECT * FROM `REACTIVE_OST` WHERE `ID`=" . $idReag)->Fetch();
		//$ost = $resOst['REACT_OST'] + $remainder; 
		//echo'<pre>';var_dump('ost',$ost);echo'</pre>';
		//$resOst = $DB->Query("UPDATE `REACTIVE_OST` SET `REACT_OST` =" . $ost . " WHERE `ID`=" . $idReag);
	}
	
	$idCount = 0;
	
	$sumIdCount = 0;
	
	$sumFlow = 0;
	
	$resultBaTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `DATE_P_CORR` > '2019-12-31' ORDER BY `DATE_P_CORR`");
	
	while($rowBaTz = $resultBaTz->Fetch())
	{
		$tz = !empty($rowBaTz['TZ']) ? unserialize($rowBaTz['TZ']) : '';
		$dateP = !empty($rowBaTz['DATE_P']) ? $rowBaTz['DATE_P'] : '';
		$dateProtocol = !empty($rowBaTz['DATE_P_CORR']) ? $rowBaTz['DATE_P_CORR'] : $rowBaTz['DATE_P'];
		$dateСoming = date("Y") . '-01-01';
		
		if (empty($dateProtocol)) {
			continue;
		}
		
		foreach($tz as $keyTz => $valTz) {
			
			foreach($valTz as $key => $val) {
				
				if (!empty($key) && $key == 'gost') {
					
					
					if (!empty($val) && in_array($idGostReagents, $val)) {
					
						foreach($val as $keyGost => $valGost) {
							if (!empty($valGost) && $valGost == $idGostReagents) { 
							
							$idCount++;
							$sumIdCount++;
							$remainder = null;
							
							$consumption = null;
								switch($idCount) {
									case '1': 
										
										
										if ($count == $sumIdCount) {
											$flowEnd = $countSpentYear - $sumFlow;
											$consumption = $flowEnd;
											
											
											$remainder = $countSpentYear - ($sumFlow + $flowEnd);
										} else {
											$consumption = $flowMean;
											
											$remainder = $countSpentYear - ($sumFlow + $flowMean);
										}
									
										$sumFlow += $flowMean;
										
										if (!empty($idReag)) {
											//$resultOst = $DB->Query("SELECT * FROM `REACTIVE_OST` WHERE `ID`=" . $idReag)->Fetch();
											//$ostatok = $resultOst['REACT_OST'] - $remainders; 
											//echo'<pre>';var_dump('ostatok',$ostatok);echo'</pre>';
											//$resultOst = $DB->Query("UPDATE `REACTIVE_OST` SET `REACT_OST` =" . $ostatok . " WHERE `ID`=" . $idReag);
										}
										
										//echo'<pre>';var_dump('consumption',$consumption);echo'</pre>';
										//echo'<pre>';var_dump('dateProtocol',$dateProtocol);echo'</pre>';
										//echo'<pre>';var_dump('remainder',$remainder);echo'</pre>';
										
										$resultConsumption = $DB->Query("INSERT INTO `REACTIVE` (ID_REACT, DATE_POSTUP, PRIKHOD, NUMBER_PART, OSTATOK) 
										VALUES ('" . $idReag . "', '" . $dateProtocol . "', " . (float)-$consumption . ", '" . $gostReagent . "', " . (float)$remainder . ")");
									break;
									case '2': 
									
										if ($count == $sumIdCount) {
											$flowEnd = $countSpentYear - $sumFlow;
											$consumption = $flowEnd;
											
											$remainder = $countSpentYear - ($sumFlow + $flowEnd);
										} else {
											$consumption = $flowMin;
											
											$remainder = $countSpentYear - ($sumFlow + $flowMin);
										}
										
										$sumFlow += $flowMin;
										
										if (!empty($idReag)) {
											//$resultOst = $DB->Query("SELECT * FROM `REACTIVE_OST` WHERE `ID`=" . $idReag)->Fetch();
											//$ostatok = $resultOst['REACT_OST'] - $remainders; 
											//echo'<pre>';var_dump('ostatok',$ostatok);echo'</pre>';
											//$resultOst = $DB->Query("UPDATE `REACTIVE_OST` SET `REACT_OST` =" . $ostatok . " WHERE `ID`=" . $idReag);
										}
										
										
										$resultConsumption = $DB->Query("INSERT INTO `REACTIVE` (ID_REACT, DATE_POSTUP, PRIKHOD, NUMBER_PART, OSTATOK) 
										VALUES ('" . $idReag . "', '" . $dateProtocol . "', " . (float)-$consumption . ", '" . $gostReagent . "', " . (float)$remainder . ")");
									break;
									case '3': 
									
										
										if ($count == $sumIdCount) {
											$flowEnd = $countSpentYear - $sumFlow;
											$consumption = $flowEnd;
											
											$remainder = $countSpentYear - ($sumFlow + $flowEnd);
										} else {
											$consumption = $flowMax;
											
											$remainder = $countSpentYear - ($sumFlow + $flowMax);
										}
										
										$sumFlow += $flowMax;
										
										if (!empty($idReag)) {
											//$resultOst = $DB->Query("SELECT * FROM `REACTIVE_OST` WHERE `ID`=" . $idReag)->Fetch();
											//$ostatok = $resultOst['REACT_OST'] - $remainders; 
											//echo'<pre>';var_dump('ostatok',$ostatok);echo'</pre>';
											//$resultOst = $DB->Query("UPDATE `REACTIVE_OST` SET `REACT_OST` =" . $ostatok . " WHERE `ID`=" . $idReag);
										}
										
										$resultConsumption = $DB->Query("INSERT INTO `REACTIVE` (ID_REACT, DATE_POSTUP, PRIKHOD, NUMBER_PART, OSTATOK) 
										VALUES ('" . $idReag . "', '" . $dateProtocol . "', " . (float)-$consumption . ", '" . $gostReagent . "', " . (float)$remainder . ")");
									
									$idCount = 0;
									break;
								}
							
							}
						}
					}
				}
			}
		}
	}
    
	if (!empty($idReagents)) {
		//$updateReactiveFlow = $DB->Query("UPDATE `REAGENTS_FLOW` SET `COUNT_SPENT`='" . $countSpentYear . "' WHERE `ID`=" . $idReagents);
	}
echo'=====================================================================================';
} 

echo 'Расчет окончен';

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); 
?>