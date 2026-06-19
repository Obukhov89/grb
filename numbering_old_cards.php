<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
		<?php 			
			/*$laba1['SI'] = [];
			$laba1['IO'] = [];
			$laba1['VO'] = [];
			$laba1['TS'] = [];
			$laba1['SO'] = [];
			
			$laba2['SI'] = [];
			$laba2['IO'] = [];
			$laba2['VO'] = [];
			$laba2['TS'] = [];
			$laba2['SO'] = [];
			
			$laba3['SI'] = [];
			$laba3['IO'] = [];
			$laba3['VO'] = [];
			$laba3['TS'] = [];
			$laba3['SO'] = [];
			
			$laba4['SI'] = [];
			$laba4['IO'] = [];
			$laba4['VO'] = [];
			$laba4['TS'] = [];
			$laba4['SO'] = [];
			
			$laba5_1['SI'] = [];
			$laba5_1['IO'] = [];
			$laba5_1['VO'] = [];
			$laba5_1['TS'] = [];
			$laba5_1['SO'] = [];
			
			$laba5_2['SI'] = [];
			$laba5_2['IO'] = [];
			$laba5_2['VO'] = [];
			$laba5_2['TS'] = [];
			$laba5_2['SO'] = [];
			
			$laba6['SI'] = [];
			$laba6['IO'] = [];
			$laba6['VO'] = [];
			$laba6['TS'] = [];
			$laba6['SO'] = [];*/
			
			$resOborud = $DB->Query("SELECT * FROM `ba_oborud`");
			
			while($rowOborud = $resOborud->Fetch()) {
				
				if ($rowOborud['place_of_installation_or_storage'] == 1 && $rowOborud['IDENT'] == 'SI') {
					++$i1;
					//$laba1['SI'][] = $i1;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i1 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 1 && $rowOborud['IDENT'] == 'IO') {
					$i2++;
					//$laba1['IO'][] = $i2;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i2 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 1 && $rowOborud['IDENT'] == 'VO') {
					$i3++;
					//$laba1['VO'][] = $i3;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i3 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 1 && $rowOborud['IDENT'] == 'TS') {
					$i4++;
					//$laba1['TS'][] = $i4;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i4 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 1 && $rowOborud['IDENT'] == 'SO') {
					$i5++;
					//$laba1['SO'][] = $i5;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i5 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				//laba2
				
				if ($rowOborud['place_of_installation_or_storage'] == 2 && $rowOborud['IDENT'] == 'SI') {
					$i6++;
					//$laba2['SI'][] = $i6; 
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i6 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 2 && $rowOborud['IDENT'] == 'IO') {
					$i7++;
					//$laba2['IO'][] = $i7;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i7 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 2 && $rowOborud['IDENT'] == 'VO') {
					$i8++;
					//$laba2['VO'][] = $i8;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i8 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 2 && $rowOborud['IDENT'] == 'TS') {
					$i9++;
					//$laba2['TS'][] = $i9;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i9 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 2 && $rowOborud['IDENT'] == 'SO') {
					$i10++;
					//$laba2['SO'][] = $i10;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i10 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				
				//laba3
				
				if ($rowOborud['place_of_installation_or_storage'] == 3 && $rowOborud['IDENT'] == 'SI') {
					$i11++;
					//$laba3['SI'][] = $i11; 
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i11 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 3 && $rowOborud['IDENT'] == 'IO') {
					$i12++;
					//$laba3['IO'][] = $i12;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i12 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 3 && $rowOborud['IDENT'] == 'VO') {
					$i13++;
					//$laba3['VO'][] = $i13;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i13 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 3 && $rowOborud['IDENT'] == 'TS') {
					$i14++;
					//$laba3['TS'][] = $i14;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i14 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 3 && $rowOborud['IDENT'] == 'SO') {
					$i15++;
					//$laba3['SO'][] = $i15;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i15. "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				//laba4
				
				if ($rowOborud['place_of_installation_or_storage'] == 4 && $rowOborud['IDENT'] == 'SI') {
					$i16++;
					//$laba4['SI'][] = $i16; 
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i16 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 4 && $rowOborud['IDENT'] == 'IO') {
					$i17++;
					//$laba4['IO'][] = $i17;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i17 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 4 && $rowOborud['IDENT'] == 'VO') {
					$i18++;
					//$laba4['VO'][] = $i18;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i18 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 4 && $rowOborud['IDENT'] == 'TS') {
					$i19++;
					//$laba4['TS'][] = $i19;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i19 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 4 && $rowOborud['IDENT'] == 'SO') {
					$i20++;
					//$laba4['SO'][] = $i20;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i20 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				//laba5
				
				if ($rowOborud['place_of_installation_or_storage'] == 5 && ($rowOborud['roomnumber'] == 9 ||  $rowOborud['roomnumber'] == 10 || $rowOborud['roomnumber'] == 11)) {
					
					if ($rowOborud['place_of_installation_or_storage'] == 5 && $rowOborud['IDENT'] == 'SI') {
						$i21_1++;
						//$laba5_1['SI'][] = $i21_1; 
						$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i21_1 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
					}
					
					if ($rowOborud['place_of_installation_or_storage'] == 5 && $rowOborud['IDENT'] == 'IO') {
						$i22_2++;
						//$laba5_1['IO'][] = $i22_2;
						$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i22_2 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
					}
					
					if ($rowOborud['place_of_installation_or_storage'] == 5 && $rowOborud['IDENT'] == 'VO') {
						$i23_3++;
						//$laba5_1['VO'][] = $i23_3;
						$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i23_3 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
					}
					
					if ($rowOborud['place_of_installation_or_storage'] == 5 && $rowOborud['IDENT'] == 'TS') {
						$i24_4++;
						//$laba5_1['TS'][] = $i24_4;
						$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i24_4 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
					}
					
					if ($rowOborud['place_of_installation_or_storage'] == 5 && $rowOborud['IDENT'] == 'SO') {
						$i25_5++;
						//$laba5_1['SO'][] = $i25_5;
						$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i25_5 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
					}
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 5 && !($rowOborud['roomnumber'] == 9 ||  $rowOborud['roomnumber'] == 10 || $rowOborud['roomnumber'] == 11)){
					
					if ($rowOborud['place_of_installation_or_storage'] == 5 && $rowOborud['IDENT'] == 'SI') {
						$i21_6++;
						//$laba5_2['SI'][] = $i21_6; 
						$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i21_6 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
					}
					
					if ($rowOborud['place_of_installation_or_storage'] == 5 && $rowOborud['IDENT'] == 'IO') {
						$i22_7++;
						//$laba5_2['IO'][] = $i22_7;
						$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i22_7 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
					}
					
					if ($rowOborud['place_of_installation_or_storage'] == 5 && $rowOborud['IDENT'] == 'VO') {
						$i23_8++;
						//$laba5_2['VO'][] = $i23_8;
						$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i23_8 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
					}
					
					if ($rowOborud['place_of_installation_or_storage'] == 5 && $rowOborud['IDENT'] == 'TS') {
						$i24_9++;
						//$laba5_2['TS'][] = $i24_9;
						$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i24_9 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
					}
					
					if ($rowOborud['place_of_installation_or_storage'] == 5 && $rowOborud['IDENT'] == 'SO') {
						$i25_10++;
						//$laba5_2['SO'][] = $i25_10;
						$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i25_10 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
					}
				}
				
				//laba6
				
				if ($rowOborud['place_of_installation_or_storage'] == 6 && $rowOborud['IDENT'] == 'SI') {
					$i26++;
					//$laba6['SI'][] = $i26; 
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i26 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 6 && $rowOborud['IDENT'] == 'IO') {
					$i27++;
					//$laba6['IO'][] = $i27;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i27 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 6 && $rowOborud['IDENT'] == 'VO') {
					$i28++;
					//$laba6['VO'][] = $i28;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i28 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 6 && $rowOborud['IDENT'] == 'TS') {
					$i29++;
					//$laba6['TS'][] = $i29;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i29 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
				if ($rowOborud['place_of_installation_or_storage'] == 6 && $rowOborud['IDENT'] == 'SO') {
					$i30++;
					//$laba6['SO'][] = $i30;
					$DB->Query("UPDATE `ba_oborud` SET `card_id`='" . (int)$i30 . "' WHERE `ID`=" . (int)$rowOborud['ID']);
				}
				
			}
			
			/*echo'<pre>';
			echo 'laba1<br>';
			print_r($laba1['SI']);
			print_r($laba1['IO']);
			print_r($laba1['VO']);
			print_r($laba1['TS']);
			print_r($laba1['SO']);
			echo 'laba2<br>';
			print_r($laba2['SI']);
			print_r($laba2['IO']);
			print_r($laba2['VO']);
			print_r($laba2['TS']);
			print_r($laba2['SO']);
			echo 'laba3<br>';
			print_r($laba3['SI']);
			print_r($laba3['IO']);
			print_r($laba3['VO']);
			print_r($laba3['TS']);
			print_r($laba3['SO']);
			echo 'laba4<br>';
			print_r($laba4['SI']);
			print_r($laba4['IO']);
			print_r($laba4['VO']);
			print_r($laba4['TS']);
			print_r($laba4['SO']);
			echo 'laba5_1<br>';
			print_r($laba5_1['SI']);
			print_r($laba5_1['IO']);
			print_r($laba5_1['VO']);
			print_r($laba5_1['TS']);
			print_r($laba5_1['SO']);
			echo 'laba5_2<br>';
			print_r($laba5_2['SI']);
			print_r($laba5_2['IO']);
			print_r($laba5_2['VO']);
			print_r($laba5_2['TS']);
			print_r($laba5_2['SO']);
			echo 'laba6<br>';
			print_r($laba6['SI']);
			print_r($laba6['IO']);
			print_r($laba6['VO']);
			print_r($laba6['TS']);
			print_r($laba6['SO']);*/
			
		?>
		<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>