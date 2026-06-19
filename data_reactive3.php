<?php
//Файл рандомного расчета и заполнения расхода реактивов
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Журнал верификации");

$resultBaTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `DATE_P_CORR` > '2019-12-31' ORDER BY `DATE_P_CORR`");
	
while($rowBaTz = $resultBaTz->Fetch())
{
	$tz = !empty($rowBaTz['TZ']) ? unserialize($rowBaTz['TZ']) : '';
	$dateP = !empty($rowBaTz['DATE_P']) ? $rowBaTz['DATE_P'] : '';
	$dateProtocol = !empty($rowBaTz['DATE_P_CORR']) ? $rowBaTz['DATE_P_CORR'] : $rowBaTz['DATE_P'];
	
	foreach($tz as $keyTz => $valTz) 
	{
		
		foreach($valTz as $key => $val) 
		{
			
			if (!empty($key) && $key == 'gost') 
			{
				
				
				
				foreach($val as $keyGost => $valGost) 
				{
					
				
					$resultReagentsFlow = $DB->Query("select * from `REAGENTS_FLOW`");
					while($rowReagentsFlow = $resultReagentsFlow->Fetch())
					{
						
						$interval = [];
						
						$arrayOstatokPostup = [];
						
						$idReact = !empty($rowReagentsFlow['ID_REAG']) ? $rowReagentsFlow['ID_REAG'] : '';
						$gostId = !empty($rowReagentsFlow['ID_GOST']) ? $rowReagentsFlow['ID_GOST'] : '';
						$flowMin = !empty($rowReagentsFlow['FLOW_MIN']) ? $rowReagentsFlow['FLOW_MIN'] : '';
						$flowMax = !empty($rowReagentsFlow['FLOW_MAX']) ? $rowReagentsFlow['FLOW_MAX'] : '';
						$flowMean = !empty($flowMin) && !empty($flowMax) ? $flowMin + (($flowMax - $flowMin) / 2) : '';
						
						if (!empty($valGost) && !empty($gostId) && $valGost == $gostId) {
							echo'<pre>';var_dump('$idReact', $idReact);echo'</pre>';
							
							$resultReact =  $DB->Query("SELECT * FROM `REACTIVE` WHERE `ID_REACT`=" . $idReact . " AND `PRIKHOD`>0");
							
							while($rowReact = $resultReact->Fetch())
							{
								$datePostupReactive = !empty($rowReact['DATE_POSTUP']) ? $rowReact['DATE_POSTUP'] : '';
								$ostatokPostupReactive = !empty($rowReact['OSTATOK']) ? $rowReact['OSTATOK'] : '';
								$idPostupReactive = !empty($rowReact['ID']) ? $rowReact['ID'] : '';
								
								
								if ($dateProtocol < $datePostupReactive) {
									continue;
								}
								
								
								$interval[$idPostupReactive] = strtotime($dateProtocol) - strtotime($datePostupReactive);
								
								$arrayOstatokPostup[$idPostupReactive] = $ostatokPostupReactive;
							}
							
							
							$minIdDatePostup = array_keys($interval, min($interval));
							
							$idReactive = !empty($minIdDatePostup[0]) ? $minIdDatePostup[0] : '';
							
							if (count($minIdDatePostup) > 1) {
								$maxIdOstatokPostup = array_keys($arrayOstatokPostup, max($arrayOstatokPostup));
								
								$idReactive = !empty($maxIdOstatokPostup[0]) ? $maxIdOstatokPostup[0] : '';
								
								//echo'<pre>';var_dump('$maxIdOstatokPostup', $maxIdOstatokPostup);echo'</pre>';
								//echo'<pre>';var_dump('$idReactive2222', $idReactive);echo'</pre>';
							}
							
							$resultReactPostup = null;
							if (!empty($idReactive)) {
								$resultReactPostup =  $DB->Query("SELECT * FROM `REACTIVE` WHERE `ID`=" . $idReactive)->Fetch();
							}
							
							$numberPart = !empty($resultReactPostup['NUMBER_PART']) ? $resultReactPostup['NUMBER_PART'] : '';
							
							$srokGodn = !empty($resultReactPostup['SROK_GODN']) ? $resultReactPostup['SROK_GODN'] : '';
							
							$ostatokReactive = !empty($resultReactPostup['OSTATOK']) ? $resultReactPostup['OSTATOK'] : '';
							
							
							$resultReactLastDeduction =  $DB->Query("SELECT * FROM `REACTIVE` WHERE `ID_REACT`=" . $idReact . " AND `PRIKHOD`<0 ORDER BY ID DESC LIMIT 1")->Fetch();
							//echo'<pre>';var_dump('$$resultReactLastDeduction[OSTATOK]', $resultReactLastDeduction['OSTATOK']);echo'</pre>';
							$deductionIdReactive = null;
							if (!empty($resultReactLastDeduction)) {
								
								$deductionIdReactive = !empty($resultReactLastDeduction['ID_DEDUCTION']) ? $resultReactLastDeduction['ID_DEDUCTION'] : '';
	
							}
							
							
							if (!empty($deductionIdReactive)) {
								
								if ($idReactive == $deductionIdReactive) {
									$ostatokReactive = !empty($resultReactLastDeduction['OSTATOK']) ? $resultReactLastDeduction['OSTATOK'] : '';
									//echo'<pre>';var_dump('$ostatokReactive222222222222', $ostatokReactive);echo'</pre>';
								} else {
									$reactivePrihod = !empty($resultReactPostup['PRIKHOD']) ? $resultReactPostup['PRIKHOD'] : '';
									//echo'<pre>';var_dump('$reactivePrihod', $reactivePrihod);echo'</pre>';
									$reactLastDeduction = !empty($resultReactLastDeduction['OSTATOK']) ? $resultReactLastDeduction['OSTATOK'] : '';
									//echo'<pre>';var_dump('$reactLastDeduction', $reactLastDeduction);echo'</pre>';
									
									if (!empty($resultReactPostup['ID_DEDUCTION'])) {
										$ostatokReactive = $reactLastDeduction;
										//echo'<pre>';var_dump('$ostatokReactive333333333333333333', $ostatokReactive);echo'</pre>';
									} else {
										$ostatokReactive = $reactivePrihod + $reactLastDeduction;
										//echo'<pre>';var_dump('$ostatokReactive444444444444444444', $ostatokReactive);echo'</pre>';
										
										$updateReactive = $DB->Query("UPDATE `REACTIVE` SET `OSTATOK`=" . $ostatokReactive . " WHERE `ID`=" . $idReactive);
									}
									
								}
							} else {
								//$ostatokPostupReac = !empty($resultReactLastDeduction['OSTATOK']) ? $resultReactLastDeduction['OSTATOK'] : '';
								//echo'<pre>';var_dump('$ostatokPostupReac', $ostatokPostupReac);echo'</pre>';
								//if (!empty($ostatokPostupReac)) {
								//	$updateReactive = $DB->Query("UPDATE `REACTIVE` SET `OSTATOK`=" . $ostatokPostupReac . " WHERE `ID`=" . $idReactive);
								//}
								
							}
							
							
							//$updateReactive = $DB->Query("UPDATE `REACTIVE` SET `ID_DEDUCTION`=" . $idReactive . " WHERE `ID`=" . $idReactive);
							if (!empty($idReactive)) {
								$updateReactive = $DB->Query("UPDATE `REACTIVE` SET `ID_DEDUCTION`=" . $idReactive . " WHERE `ID`=" . $idReactive);
							} else {
								continue;
							}
							
							//echo'<pre>';var_dump('$ostatokReactive5555555', $ostatokReactive);echo'</pre>';
							
							$idCount++;
							$remainder = null;
							
							$consumption = null;
								switch($idCount) {
									case '1': 
										
										$consumption = $flowMean;
										
										$remainder = $ostatokReactive - $flowMean;
										
										//echo'<pre>';var_dump('1');echo'</pre>';
										//echo'<pre>';var_dump('$ostatokReactive6666666', $ostatokReactive);echo'</pre>';
										//echo'<pre>';var_dump('$consumption', $consumption);echo'</pre>';
										//echo'<pre>';var_dump('$remainder', $remainder);echo'</pre>';
										
										$resultConsumption = $DB->Query("INSERT INTO `REACTIVE` (ID_REACT, DATE_POSTUP, PRIKHOD, NUMBER_PART, OSTATOK, ID_DEDUCTION, SROK_GODN) 
										VALUES ('" . $idReact . "', '" . $dateProtocol . "', " . (float)-$consumption . ", '" . $numberPart . "', " . (float)$remainder . ", " . $idReactive . ", '" . $srokGodn . "')");
									break;
									case '2': 
										$consumption = $flowMin;
										
										$remainder = $ostatokReactive - $flowMin;
										
										//echo'<pre>';var_dump('2');echo'</pre>';
										//echo'<pre>';var_dump('$ostatokReactive6666666', $ostatokReactive);echo'</pre>';
										//echo'<pre>';var_dump('$consumption', $consumption);echo'</pre>';
										//echo'<pre>';var_dump('$remainder', $remainder);echo'</pre>';
										
										$resultConsumption = $DB->Query("INSERT INTO `REACTIVE` (ID_REACT, DATE_POSTUP, PRIKHOD, NUMBER_PART, OSTATOK, ID_DEDUCTION, SROK_GODN) 
										VALUES ('" . $idReact . "', '" . $dateProtocol . "', " . (float)-$consumption . ", '" . $numberPart . "', " . (float)$remainder . ", " . $idReactive . ", '" . $srokGodn . "')");
									break;
									case '3': 
										$consumption = $flowMax;
										
									
										$remainder = $ostatokReactive - $flowMax;
										
										//echo'<pre>';var_dump('3');echo'</pre>';
										//echo'<pre>';var_dump('$ostatokReactive666666', $ostatokReactive);echo'</pre>';
										//echo'<pre>';var_dump('$consumption', $consumption);echo'</pre>';
										//echo'<pre>';var_dump('$remainder', $remainder);echo'</pre>';
										
										$resultConsumption = $DB->Query("INSERT INTO `REACTIVE` (ID_REACT, DATE_POSTUP, PRIKHOD, NUMBER_PART, OSTATOK, ID_DEDUCTION, SROK_GODN) 
										VALUES ('" . $idReact . "', '" . $dateProtocol . "', " . (float)-$consumption . ", '" . $numberPart . "', " . (float)$remainder . ", " . $idReactive . ", '" . $srokGodn . "')");
									
									
									$idCount = 0;
									break;
								}
								
								//echo'=========================================================';
						}
					}
					
					//echo'<pre>';var_dump('$valGost', $valGost);echo'</pre>';
				
					$count++;
					
					//echo'<pre>';var_dump('$count', $count);echo'</pre>';
				}

			}
		}
	}
}

$resReagentsFlow = $DB->Query("SELECT DISTINCT `ID_REAG` FROM `REAGENTS_FLOW`");

while($rowReagFlow = $resReagentsFlow->Fetch())
{
	//echo'<pre>';var_dump('$rowReagFlow[ID_REAG]', $rowReagFlow['ID_REAG']);echo'</pre>';
	
	if (!empty($rowReagFlow['ID_REAG'])) {
		
		
		$resReactiveСonsumption = $DB->Query("SELECT * FROM `REACTIVE` WHERE `ID_REACT`=" . (int)$rowReagFlow['ID_REAG'] . " AND `PRIKHOD`<0 ORDER BY ID DESC LIMIT 1")->Fetch();
		
		$dateReactivePostup = !empty($resReactiveСonsumption['DATE_POSTUP']) ? $resReactiveСonsumption['DATE_POSTUP'] : '';
		
		$ostatokReactivePostup = !empty($resReactiveСonsumption['OSTATOK']) ? $resReactiveСonsumption['OSTATOK'] : 0;
		
		//echo'<pre>';var_dump('$ostatokReactivePostup', $ostatokReactivePostup);echo'</pre>';
		//echo'<pre>';var_dump('$dateReactivePostup', $dateReactivePostup);echo'</pre>';
		
		if (!empty($dateReactivePostup)) {
			
			$resReactiveIncome = $DB->Query("SELECT * FROM `REACTIVE` WHERE `ID_REACT`=" . (int)$rowReagFlow['ID_REAG'] . " AND `PRIKHOD`>0 AND `DATE_POSTUP`>'" . $dateReactivePostup . "'");
			
			$countNewProkhod = 0;
			while($rowReactiveIncome = $resReactiveIncome->Fetch())
			{
				
				$idReac = !empty($rowReactiveIncome['ID']) ? $rowReactiveIncome['ID'] : '';
				
				
				$prikhodNew = !empty($rowReactiveIncome['PRIKHOD']) ? $rowReactiveIncome['PRIKHOD'] : 0;
				
				//echo'<pre>';var_dump('$countNewProkhod', $countNewProkhod);echo'</pre>';
				
				if ($countNewProkhod > 0) {
					if (!empty($idReac)) {
						$resReactivePrevious = $DB->Query("SELECT * FROM `REACTIVE` WHERE `ID_REACT`=" . (int)$rowReagFlow['ID_REAG'] . " AND `PRIKHOD`>0 AND `DATE_POSTUP`>'" . $dateReactivePostup . "' AND `ID`<" . $idReac . " ORDER BY ID DESC LIMIT 1");
					
						
						$ostatokReactivePrikhodLast = !empty($resReactivePrevious['OSTATOK']) ? $resReactivePrevious['OSTATOK'] : 0;
					
						
						$ostatokNew = $ostatokReactivePrikhodLast + $prikhodNew;
						//echo'<pre>';var_dump('$ostatokNew2222', $ostatokNew);echo'</pre>';
						if (!empty($idReac)) {
							$updateReac = $DB->Query("UPDATE `REACTIVE` SET `OSTATOK`=" . $ostatokNew . " WHERE `ID`=" . $idReac);
						}
					}
					
				} else {
					
					$ostatokNew = $ostatokReactivePostup + $prikhodNew;
					//echo'<pre>';var_dump('$prikhodNew', $prikhodNew);echo'</pre>';
					//echo'<pre>';var_dump('$ostatokReactivePostup', $ostatokReactivePostup);echo'</pre>';
					//echo'<pre>';var_dump('$ostatokNew', $ostatokNew);echo'</pre>';
					if (!empty($idReac)) {
						$updateReac = $DB->Query("UPDATE `REACTIVE` SET `OSTATOK`=" . $ostatokNew . " WHERE `ID`=" . $idReac);
					}
				}
				
				$countNewProkhod++;
			}
		}
		
	}
	
	//echo'--------------------------------------';
}

echo 'Расчет окончен';

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); 
?>