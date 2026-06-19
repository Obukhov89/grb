<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04.04.2019
 * Time: 15:23
 */
//header('location: /ulab/order/card/' . $_GET['ID']);


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Карточка договора");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
?>
 <style>
input[type="button"]
{
   margin: 5px 0 0 0px;
}

input[type="button"]:disabled
{
   background-color: #C0C0C0;
   border: 1px solid  #C0C0C0;
   cursor: default;
}

input[type="submit"]:disabled
{
   background-color: #C0C0C0;
   border: 1px solid  #C0C0C0;
   cursor: default;
}
</style>
<?php
if($_POST['LONGTERM'] == 'on')
{
	$resPriceForContract = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT`=".(int)$_GET['ID']." AND `ID_GOST`=2")->Fetch();
	if(!$resPriceForContract['ID'])
	{
		$res_oa = $DB->Query("SELECT * FROM `ba_gost` ORDER BY `PRICE` ASC");
		while($gost = $res_oa->Fetch())
		{
			if(!$gost['NON_ACTUAL'] && $gost['GOST_TYPE']!='TU_standart' && $gost['GOST_TYPE']!='TU_research' && $gost['GOST_TYPE']!='TU_group')
			{
				$DB->Query("INSERT INTO `PRICE_FOR_CONTRACTS` (`ID_CONTRACT`, `ID_GOST`, `PRICE`) VALUES (".(int)$_GET['ID'].", ".$gost['ID'].", ".$gost['PRICE'].")");
			}
		}
	}
}

if (!empty($_POST['check']))
{
    dump($_POST);
	$deals = array();
	foreach($_POST['check'] as $deal_num => $checked)
	{




        if (!empty($_POST['btn_hidden_act'])) {

            if ($deal_num != $checked) {
                $deals[] = $deal_num;
            }
        } else {
            $deals[] = $deal_num;
        }

	}

	$addr = implode('_', $deals);
	if(isset($_POST['Deal_payments']))
	{
		header ('Location: /deal_mass_payments.php?ID='.$addr.'&ID_CONTRACT='.$_GET['ID']);  // перенаправление на нужную страницу
		exit();
	}



    if (!empty($_POST['btn_hidden_act'])) {

        $akt_vr_num = !empty($_POST['akt_vr_num']) ? $_POST['akt_vr_num'] : '';

        $akt_vr_date = !empty($_POST['akt_vr_date']) ? $_POST['akt_vr_date'] : '';

        header('Location: /protocol_generator/total_act.php?ID=' . $addr . '&ID_CONTRACT=' . $_GET['ID'] . '&VR_NUM=' . $akt_vr_num . '&VR_DATE=' . $akt_vr_date);
        exit();
    }


	header ('Location: /protocol_generator/sum_invoice.php?ID='.$addr.'&ID_CONTRACT='.$_GET['ID']);  // перенаправление на нужную страницу
	exit();
}

if (!empty($_POST['CONTRACT_NUM']))
{
	$getId = !empty($_GET['ID']) ? (int)$_GET['ID'] : '';
	$cData = $DB->Query("SELECT * FROM `DOGOVOR`");
	$checkNum=0;
	while($cNum = $cData->Fetch())
	{
		if($cNum['NUMBER']==$_POST['CONTRACT_NUM'] && $cNum['ID']!=$_GET['ID'] && empty($_POST['CLIENT_NUMBER']))
			$checkNum=1;
	}

	$contract_summ = $_POST['CONTRACT_SUMM'];
	if(!is_numeric($contract_summ))
		$contract_summ=0;


	$checkClient = 0;

	$contracts = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=".$getId)->Fetch();
	// if (!empty($contracts['CLIENT_NUMBER']) && $contracts['CLIENT_NUMBER'] == 1) {
		// $checkClient = 1;
	// }

	if (!empty($_POST['CLIENT_NUMBER']) && $_POST['CLIENT_NUMBER'] == 'on') {
		$checkClient = 1;
	}



    $contractType = '';

    if (!empty($_POST['CONTRACT_TYPE'])) {

        $contractType = mb_strtolower($_POST['CONTRACT_TYPE']);



        $contractType = mb_strtoupper_first($contractType, $encoding = 'UTF8');
    }



	if (!empty($_POST['PAYMENT'])&&!$checkNum)
	{
		$payment = array_sum($_POST['PAYMENT']);

		/*$DB->Query("UPDATE `DOGOVOR` SET `PAYMENT`=" . $payment. ", `NUMBER`='".$_POST['CONTRACT_NUM']."',
		`CLIENT_NUMBER`=". (int)$checkClient .", `SUMM`=".($contract_summ ? $contract_summ : 0).",
		`DATE`='".$_POST['CONTRACT_DATE']."', `LONGTERM`='" . (($_POST['LONGTERM'] == 'on')? 1:0) . "'
		WHERE ID = " . $_GET['ID']);*/


        $DB->Query("UPDATE `DOGOVOR` SET `PAYMENT`=" . $payment . ", `NUMBER`='" . $_POST['CONTRACT_NUM'] . "',
		`CLIENT_NUMBER`=" . (int)$checkClient . ", `SUMM`=" . ($contract_summ ? $contract_summ : 0) . ",
		`DATE`='" . $_POST['CONTRACT_DATE'] . "', `LONGTERM`='" . (($_POST['LONGTERM'] == 'on') ? 1 : 0) . "',
		`FLOW_DATE`='" . (($_POST['FLOW_DATE'] == 'on') ? 1 : 0) . "',
		`CONTRACT_TYPE`='" . $contractType . "'  WHERE ID = " . $_GET['ID']);


		//$DB->Query("UPDATE `DOGOVOR` SET `PAYMENT`=" . $payment. ", `NUMBER`='".$_POST['CONTRACT_NUM']."', `SUMM`=".($contract_summ ? $contract_summ : 0).", `DATE`='".$_POST['CONTRACT_DATE']."', `LONGTERM`='" . (($_POST['LONGTERM'] == 'on')? 1:0) . "'  WHERE ID = " . $_GET['ID']);
		$DB->Query("DELETE  FROM `PAYMENTS_TO_CONTRACTS` WHERE ID_CONTRACT = " . $_GET['ID']);

		foreach($_POST['PAYMENT'] as $num => $pay)
		{
			$DB->Query("INSERT INTO `PAYMENTS_TO_CONTRACTS` (`ID_CONTRACT`, `PAY_SUMM`, `PAY_DATE`) VALUES (".$_GET['ID'].", ".$pay.",'".$_POST['DATE_PAY'][$num]."')");
		}

		//$resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_CONTRACT`=" . $getId)->Fetch();

		/*if (!empty($resDealsContract['ID_DEAL'])) {
			$resultBaTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$resDealsContract['ID_DEAL'])->Fetch();
			$dogovor = updateDogovorNum ($resultBaTz, $getId);
		}

		$DB->Query("UPDATE `ba_tz` SET `DOGOVOR_NUM` ='". $dogovor ."' WHERE `ID_Z`=" . (int)$resDealsContract['ID_DEAL']);*/

		/*$dealId = !empty($resDealsContract['ID_DEAL']) ? (int)$resDealsContract['ID_DEAL'] : '';

		$contractDate = !empty($_POST['CONTRACT_DATE']) ? ruDate($_POST['CONTRACT_DATE']) : '';

		$dogovor = "Договор №" . $_POST['CONTRACT_NUM'] . " от " . $contractDate . ", ID" . $getId;

		$DB->Query("UPDATE `ba_tz` SET `DOGOVOR_NUM` ='". $dogovor ."' WHERE `ID_Z`=" . $dealId);*/

		$resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_CONTRACT`=" . $getId);

		$contractDate = !empty($_POST['CONTRACT_DATE']) ? ruDate($_POST['CONTRACT_DATE']) : '';


        //$dogovor = "Договор №" . $_POST['CONTRACT_NUM'] . " от " . $contractDate . ", ID" . $getId;

        $dogovor = $contractType . ' №' . $_POST['CONTRACT_NUM'] . " от " . $contractDate . ", ID" . $getId;

		$dogovor_name = $contractType . ' №' . $_POST['CONTRACT_NUM'] . " от " . $contractDate;  //Для отображения в журнале заявок


		while($rowDealsContract = $resDealsContract->Fetch()) {

			$dealId = !empty($rowDealsContract['ID_DEAL']) ? (int)$rowDealsContract['ID_DEAL'] : '';

			$DB->Query("UPDATE `ba_tz` SET `DOGOVOR_NUM` ='". $dogovor ."', `DOGOVOR_TABLE` ='". $dogovor_name ."' WHERE `ID_Z`=" . $dealId);
		}

	}
	elseif (!$checkNum)
	{
		$payment=0;
		//$DB->Query("UPDATE `DOGOVOR` SET `PAYMENT`=" . $payment. ", `NUMBER`='".$_POST['CONTRACT_NUM']."', `SUMM`=".($_POST['CONTRACT_SUMM'] ? $_POST['CONTRACT_SUMM'] : 0).", `DATE`='".$_POST['CONTRACT_DATE']."', `LONGTERM`='" . (($_POST['LONGTERM'] == 'on')? 1:0) . "'  WHERE ID = " . $_GET['ID']);

		/*$DB->Query("UPDATE `DOGOVOR` SET `PAYMENT`=" . $payment. ", `NUMBER`=".$_POST['CONTRACT_NUM'].",
		`CLIENT_NUMBER`=". (int)$checkClient .", `SUMM`=".($_POST['CONTRACT_SUMM'] ? $_POST['CONTRACT_SUMM'] : 0).",
		`DATE`='".$_POST['CONTRACT_DATE']."', `LONGTERM`='" . (($_POST['LONGTERM'] == 'on')? 1:0) . "'
		WHERE ID = " . $_GET['ID']);*/


        $DB->Query("UPDATE `DOGOVOR` SET `PAYMENT`=" . $payment . ", `NUMBER`=" . $_POST['CONTRACT_NUM'] . ",
		`CLIENT_NUMBER`=" . (int)$checkClient . ", `SUMM`=" . ($_POST['CONTRACT_SUMM'] ? $_POST['CONTRACT_SUMM'] : 0) . ",
		`DATE`='" . $_POST['CONTRACT_DATE'] . "', `LONGTERM`='" . (($_POST['LONGTERM'] == 'on') ? 1 : 0) . "',
		`FLOW_DATE`='" . (($_POST['FLOW_DATE'] == 'on') ? 1 : 0) . "',
		`CONTRACT_TYPE`='" . $contractType . "' WHERE ID = " . $_GET['ID']);


		//$resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_CONTRACT`=" . $getId)->Fetch();

		/*if (!empty($resDealsContract['ID_DEAL'])) {
			$resultBaTz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$resDealsContract['ID_DEAL'])->Fetch();
			$dogovor = updateDogovorNum ($resultBaTz, $getId);
		}

		$DB->Query("UPDATE `ba_tz` SET `DOGOVOR_NUM` ='". $dogovor ."' WHERE `ID_Z`=" . (int)$resDealsContract['ID_DEAL']);*/

		/*$dealId = !empty($resDealsContract['ID_DEAL']) ? (int)$resDealsContract['ID_DEAL'] : '';

		$contractDate = !empty($_POST['CONTRACT_DATE']) ? ruDate($_POST['CONTRACT_DATE']) : '';

		$dogovor = "Договор №" . $_POST['CONTRACT_NUM'] . " от " . $contractDate . ", ID" . $getId;

		$DB->Query("UPDATE `ba_tz` SET `DOGOVOR_NUM` ='". $dogovor ."' WHERE `ID_Z`=" . $dealId);*/

		$resDealsContract = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_CONTRACT`=" . $getId);

		$contractDate = !empty($_POST['CONTRACT_DATE']) ? ruDate($_POST['CONTRACT_DATE']) : '';


        //$dogovor = "Договор №" . $_POST['CONTRACT_NUM'] . " от " . $contractDate . ", ID" . $getId;

        $dogovor = $contractType . ' №' . $_POST['CONTRACT_NUM'] . " от " . $contractDate . ", ID" . $getId;

		$dogovor_name = $contractType . ' №' . $_POST['CONTRACT_NUM'] . " от " . $contractDate;  //Для отображения в журнале заявок


		while($rowDealsContract = $resDealsContract->Fetch()) {

			$dealId = !empty($rowDealsContract['ID_DEAL']) ? (int)$rowDealsContract['ID_DEAL'] : '';

			$DB->Query("UPDATE `ba_tz` SET `DOGOVOR_NUM` ='". $dogovor ."', `DOGOVOR_TABLE` ='". $dogovor_name ."' WHERE `ID_Z`=" . $dealId);

		}
	}
	else
	{
		echo  "<script>alert('Сохранение невозможно, данный номер договора уже используется в системе!');</script>";
	}
}


if (!empty($_GET['ID'])||$_GET['ID']==0)
{
	$deal_list=array(); //список ID всех сделок, привязанных к договору

	$res = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$_GET['ID'])->Fetch();

	$res_deal_list = $DB->Query("SELECT * FROM `DEALS_TO_CONTRACTS` WHERE `ID_CONTRACT`=" . (int)$_GET['ID']);

	while($deal = $res_deal_list->Fetch())
	{
		$deal_list[] = $deal['ID_DEAL'];
	}
	rsort($deal_list);

	$company = CCrmCompany::GetByID($res['CLIENT_ID']); //Берём значение компании
	$companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch(); //Берём значение компании


	$req = new \Bitrix\Crm\EntityRequisite();
	$rs = $req->getList([
					"filter" => [
						"ENTITY_ID" => $res['CLIENT_ID'],
					]
				]);


	$requis = $rs->Fetch();

	//$email = $requis['RQ_EMAIL'];
	$email = !empty($requis['RQ_EMAIL']) ? $requis['RQ_EMAIL'] : '';

	$name_isp = 'Маргарита Черкозьянова';//$deal['ASSIGNED_BY_NAME'].' '.$deal['ASSIGNED_BY_LAST_NAME'];
	//$color = 'd1ffca';
	$date_protocol = $row['DATE_P_CORR'] ? $row['DATE_P_CORR'] :$row['DATE_P'];




	$assigned2 = CUser::GetByID($USER->GetID())->arResult[0];

	$assigned_name2 = !empty($assigned2['NAME']) ? $assigned2['NAME'] : '';

	$assigned_lastname2 = !empty($assigned2['LAST_NAME']) ? $assigned2['LAST_NAME'] : '';

	$assigned_user2 = $assigned_name2.' '.$assigned_lastname2;
	$name_isp2 =  $assigned_user2;

    ?>

	<div>
		<div class='return disable' title='Вернуться назад'></div>
		<div class='edit disable' title='Редактировать'></div>
		<a href="/dogovor_new.php">
			<div class='list' title='Вернуться к списку'></div><!--Вернуться в журнал договоров-->
		</a>
		<div class='card disable' title='Карточка заявки'></div>
		<div class='download disable' title='Скачать'></div>
		<div class='copy disable' title='Скопировать'></div>
		<a href="/dogovor_del.php?ID=<?= $_GET['ID'] ?>">
			<div class='remove' title='Удалить текущий договор'></div>
		</a>
		<div class='add disable' title='Добавить'></div>
		<div class='report disable' title='Отчёт'></div>
		<div class='further disable' title='Далее'></div>
	</div>

		<br /><br />
		<form method="POST">
		<table><tr>


        <td><input class='tz' type='text' name='CONTRACT_TYPE' value='<?= $res["CONTRACT_TYPE"] ? $res["CONTRACT_TYPE"] : "Договор" ?>' <?= $res['PDF'] ? 'readonly' : '' ?> required></td>
        <td><h3>№</h3></td>

		<!--<td><input class='tz' type='text' name='CONTRACT_NUM' value='<//?= $res['NUMBER'] ? $res['NUMBER'] : $res['ID']?>' style='width:50px' required ></td>-->

		<td><input class='tz' type='text' name='CONTRACT_NUM' value='<?= $res['NUMBER'] ? $res['NUMBER'] : $res['ID']?>' <?= $res['PDF'] ? 'readonly' : '' ?> required ></td>

		<td><h3>от</h3></td><td> <input class='tz' type='date' name='CONTRACT_DATE' value='<?= right_date($res['DATE']) ?>' <?= $res['PDF'] ? 'readonly' : '' ?> style='width:150px' required> </td>
		<td><h3> <?= $company['TITLE'] ?></h3></td>
		</tr>
		<tr>
		<td>Долгосрочный</td>
		<td><input name="LONGTERM" class='longterm_inp' type="checkbox" <?= $res['LONGTERM'] ? 'checked' : '' ?>></td>
		</tr>

		<tr>
			<td class='customer'>Договор клиента</td>
			<td>
				<input name="CLIENT_NUMBER" class='customerContract'
				type="checkbox" <?= (!empty($res['CLIENT_NUMBER']) && $res['CLIENT_NUMBER'] == '1') ? 'checked' : '' ?> <?= $res['PDF'] ? 'disabled' : '' ?>>
			</td>
		</tr>
		<tr><td>На 11 месяцев от текущей даты</td>
		<td><input name="FLOW_DATE" class='longterm_inp' type="checkbox" <?= $res['FLOW_DATE'] ? 'checked' : '' ?>></td></tr>

		</table>
		<br />
		<b>Контактное лицо:</b> <?=$requis['RQ_NAME']?>
		<br />
		<b>Е-mail:</b> <?=$requis['RQ_EMAIL']?>
		<br />
		<b>Телефон:</b> <?=$requis['RQ_PHONE']?>
		<br />
		<br />
		<table style="<?= $res['LONGTERM'] ? 'display:none' : 'display:block' ?>"><tr>
		<td>Сумма договора: </td>
		<td><input class='tz' type='text' name='CONTRACT_SUMM' value='<?= ($res['SUMM'] ? $res['SUMM'] : 'Без конечной суммы')  ?>' style='width:150px' ></td>
		</tr></table>
		<div style="<?= $res['LONGTERM'] ? 'display:none' : 'display:block' ?>">
		<br />
		Оплачено: <?= $res['PAYMENT'] ?><br />
		<br />
		Остаток к оплате: <?= $res['SUMM'] ? $res['SUMM'] - $res['PAYMENT'] : '-' ?><br />
		<br />
		<br />
		</div>
		<table>
		<thead>
		<th>Сумма оплаты</th>
		<th>Дата оплаты</th>
		<th>Добавить</th>
		</thead>
		<tbody name="new">
		<?php if($res['PAYMENT'])
		{
			$res_pay = $DB->Query("SELECT * FROM `PAYMENTS_TO_CONTRACTS` WHERE `ID_CONTRACT`=" . (int)$_GET['ID']);
			$k=0;
			while($payments_row = $res_pay->Fetch())
			{
				if (!$k)
					echo "<tr id='1'><td width='250' height='40'><input class='gost' type='number' step='0.01' name='PAYMENT[]' value=".($payments_row['PAY_SUMM'] ? $payments_row['PAY_SUMM'] : 0) ." style='width:95%'></td>
							<td><input class='tz' type='date' name='DATE_PAY[]' value='".$payments_row['PAY_DATE']."' style='width:95%' ></td>
							<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='add' value='+'></td></tr>";
				else
					echo "<tr id='1'><td width='250' height='40'><input class='gost' type='number' step='0.01' name='PAYMENT[]' value=".($payments_row['PAY_SUMM'] ? $payments_row['PAY_SUMM'] : 0)." style='width:95%'></td>
							<td><input class='tz' type='date' name='DATE_PAY[]' value='".$payments_row['PAY_DATE']."' style='width:95%' ></td>
							<td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='del' value='-'></td></tr>";
				$k++;
			}
		}
		else {
			?>
			<tr id='1'><td width='250' height='40'><input class='tz' type='number' step='0.01' name='PAYMENT[]' value='0' style='width:95%'></td>
			<td><input class='tz' type='date' name='DATE_PAY[]' value='' style='width:95%' ></td><td style='border-bottom:0px'><input style='margin-left:5px' type='button' class='add' value='+'></td>
			</tr>
		<? } ?>
		</tbody>
		</table>
		<input type="submit" value="Сохранить" id="new_tz" name="payment_submit">
		</form>
		<br /><?php
		if($res['PDF'])
		{
			echo "<br />Загружена PDF-версия: <a href='/pdf/".$res['PDF']."'>".$res['PDF']."</a><br /><br />";
		} ?>
		<br />
		<!--<form method="POST">-->

    <form method="POST" id="form_total">

    <table id="r_card" >
        <thead>
		<th>№ п/п</th>
	    <th>Заявка</th>
		<th>Дата</th>
		<th>Сумма </th>
		<th>Счет</th>
		<th>Акт ПП</th>
		<th>Скидка </th>
        <th>Оплата</th>
		<?php if ($res['LONGTERM']) { ?><th></th><? } ?>
	    </thead>
		<tbody>
		<?php
		$summPrice=0;
		$summPriceOpl=0;
		$summPriceNeOpl=0;
		for($i=0; $i<count($deal_list); $i++)
		{
			$deal = CCrmDeal::GetByID($deal_list[$i]);

			$stageArrLose = ['LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13'];

			$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$deal['ID'])->Fetch();
			$summPrice = in_array($deal['STAGE_ID'], $stageArrLose) ? $summPrice : $summPrice + $res_tz['PRICE'];
			if($res_tz['OPLATA']>$res_tz['PRICE']) $oplata = "Переплата";
			elseif($res_tz['OPLATA']&&($res_tz['OPLATA']== $res_tz['PRICE'])) { $oplata =  "Оплачена полностью"; $summPriceOpl = in_array($deal['STAGE_ID'], $stageArrLose) ? $summPriceOpl : $summPriceOpl + $res_tz['PRICE'];}
			elseif(!$res_tz['OPLATA']) { $oplata = "Не оплачена"; $summPriceNeOpl = in_array($deal['STAGE_ID'], $stageArrLose) ? $summPriceNeOpl : $summPriceNeOpl + $res_tz['PRICE'];}
			else $oplata =  "Оплачена частично";
			if(in_array($deal['STAGE_ID'], $stageArrLose)) $oplata = "Заявка неуспешна";
			//$longtermCheck = $res['LONGTERM'] ? "<td><input type='checkbox' name='check[".$deal['ID']."]'></td>" : '';
            $longtermCheck = $res['LONGTERM'] ? "<td><input type='checkbox' name='check[".$deal['ID']."]' class='check_request'></td>" : '';
			//$oplata = ( $res_tz['OPLATA']&&($res_tz['OPLATA']== $res_tz['PRICE']) ? 'оплачена' : 'не оплачена');
			echo "<tr><td>".(int)($i+1)."</td><td><a href='/request_card.php?ID=".$deal['ID']."'>".$deal['TITLE']."</a></td><td>".ruDate($deal['DATE_CREATE'])."</td><td style='background-color: ".(in_array($deal['STAGE_ID'], $stageArrLose) ? 'red' : 'white')."'>".number_format((int)$res_tz['PRICE'], 2, ',', '')."</td><td>".( $res_tz['ACCOUNT'] ? $res_tz['ACCOUNT'] : '' )."</td><td>".( $res_tz['ACT_NUM'] ? $res_tz['ACT_NUM'] : '' )."</td><td>". ($res_tz['DISCOUNT'] ? $res_tz['DISCOUNT'] : '0') ."</td><td>". $oplata ."</td>".$longtermCheck."</tr>";

            //echo "<tr><td><a href='/request_card.php?ID=" . $deal['ID'] . "'>" . $deal['TITLE'] . "</a></td><td>" . ruDate($deal['DATE_CREATE']) . "</td><td>" . $res_tz['PRICE'] . "</td><td>" . $oplata . "</td><td><input type='checkbox' name='check[" . $deal['ID'] . "]' class='check_request'><input type='hidden' name='request[" . $deal['TITLE'] . "]' id='request_num' value=''></td></tr>";

		}
		?>
		</tbody>

	</table>
	<p>Общая рассчетная сумма по всем рабочим заявкам договора: <b><?= number_format((int)$summPrice, 2, ',', '') ?> руб.</b></p>
	<p>Cумма поступлений по договору <b><?= number_format((int)$res['PAYMENT'], 2, ',', '') ?></b>, распределено по заявкам <b><?= number_format((int)$summPriceOpl, 2, ',', '') ?> руб.</b></p>
	<p>Сумма по всем неоплаченным заявкам договора: <b><?= number_format((int)$summPriceNeOpl, 2, ',', '') ?> руб.</b></p>


	<div class="wrapper_invoice">
		<input type="submit" value="Сформировать общий счет" id="Invoice" <?= $res['LONGTERM'] ? '' : 'disabled'?>>
		<input type="submit" value="Сформировать общий счет в pdf и отправить клиенту" class="sum-invoice" name="sum_invoice_pdf" <?= $res['LONGTERM'] ? '' : 'disabled'?>>
		<input type="hidden" class="email" name="email" value="<?= $email ?>">
		<input type="hidden" class="name-isp" name="name_isp" value="<?= $name_isp2 ?>">
		<input type="hidden" class="get" name="get" value="<?= $_GET['ID'] ?>">
		<input type="hidden" class="client-id" value="<?= $res['CLIENT_ID'] ?>">
		<input type="hidden" class="company-id" value="<?= $deal['COMPANY_ID'] ?>">
	</div>

	<input type="submit" value="Отметить оплаченными" name='Deal_payments' id="Deal_payments" <?= $res['LONGTERM'] ? '' : 'disabled'?> >

    <div class="wrapper-total-act">
        <div class="wrapper-total-num">
            <input type="number" step="1" class="oborud" id="id_akt_vr_num" name='akt_vr_num' value="">
        </div>
        <div class="wrapper-total-date">
            <input type="date" class="oborud" id="id_akt_vr_date" name='akt_vr_date' value="">
        </div>
        <div class="wrapper-total-btn">

            <input type="submit" value="Сформировать общий АКТ" id="total_act" name="btn_act"
                   <?= $res['LONGTERM'] ? '' : 'disabled' ?>>
            <input type="hidden" class="btn_hidden_act" name="btn_hidden_act" val=''>
        </div>
    </div>

	</form><?php

	if ( ! $_FILES )
	{
		$res_id=$DB->Query("SELECT `PDF` FROM `DOGOVOR` WHERE `ID`=".(int)$_GET['ID'])->Fetch();

		 /*if($res_id['PDF'])
		 {
			 echo "<br />Загружена PDF-версия: <a href='/pdf/".$res_id['PDF']."'>".$res_id['PDF']."</a><br /><br />";
		 }*/
	?>

	<div>
	<h3>Загрузить PDF-версию договора</h3>
	<form enctype="multipart/form-data" action="/contract_card.php?ID=<?=$_GET['ID']?>" method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="300000000" />
	Отправить этот файл: <input name="userfile" type="file" />
						<input type="submit" value="Отправить файл" />
	</form>
	</div>

	<?php
	}
	else
	{
		$res_id=$DB->Query("SELECT `ID` FROM `DOGOVOR` WHERE `ID`=".(int)$_GET['ID'])->Fetch();
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/pdf/';
		$uploadfile = $uploaddir . $_FILES['userfile']['name'];
		$user_name = "bitrix";

		$res_file=$DB->Query("UPDATE `DOGOVOR` SET `PDF`='".$_FILES['userfile']['name']."' WHERE `ID`=".(int)$res_id['ID']);

		if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
		{
			echo "Файл ".$_FILES['userfile']['name']."  был успешно загружен.\n";

		}
		else
		{
			echo "Ошибка загрузки!\n";
		}

	}

	$res_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . $res['TZ_ID'])->Fetch();
	$resPriceForContract = $DB->Query("SELECT * FROM `PRICE_FOR_CONTRACTS` WHERE `ID_CONTRACT`=".(int)$_GET['ID']);
}?>
	<br /><br />
	<?php if (!$res['LONGTERM']) { ?><a href="/protocol_generator/dogovor.php?ID=<?=$res_tz['ID_Z']?>&TZ_ID=<?=$res_tz['ID']?>&ID_C=<?= $_GET['ID'] ?>">Скачать договор</a> | <? } ?>
	<?php if ($res['LONGTERM'] && $resPriceForContract->SelectedRowsCount()>0) { ?> <a href="/price_upd.php?ID_CONTRACT=<?=$res['ID']?>">Перейти к прайсу</a> <? } ?>

	<script
            src="//code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>


	<script>


	$(document).ready(function () {

	//--'Выбранные Заявки не соответствуют компании(клиенту)' начало--//
		function findIdRequest(checkRequest) {
			var deals = [];

			if (checkRequest.length) {
				$(checkRequest).each(function(index,el) {
				  //Находим значение(id сделки) в скобках[] атрибута name и записываем в масив deals
				  $(checkRequest[index]).attr('name').replace(/\[(.+?)\]/g, function($brackets, $no_brackets) { deals.push($no_brackets) });
				});

				var addr = deals.join('_');//Строка с id сделок

				return addr;
			} else {
				return false;
			}
		}


	$("tbody").on("click", ".del", function () {
		  $(this).parent().parent().remove();
		});

	$("tbody[name=new]").on("click", ".add", function () {
		   table = $(this).parent().parent().parent().parent();
		   tbody = $(this).parent().parent().parent();
			tr = $(this).parent().parent();
			tr_dataid = tr.attr('id');
			console.log("+");
			$.ajax({

			  url: 'select_for_contract_card.php?KEY='+tr_dataid,

			  success: function(data) {

				tr.after(data);

			  }

			});

	});




		if ($('.customerContract').prop('checked')) {
			$('input[name=CONTRACT_NUM]').prop("readonly",false);
			$('input[name=CONTRACT_DATE]').prop("readonly",false);
            $('input[name=CONTRACT_TYPE]').prop("readonly", false);
		} else {
			$('input[name=CONTRACT_NUM]').prop("readonly",true);
			//$('input[name=CONTRACT_DATE]').prop("readonly",true);
            $('input[name=CONTRACT_TYPE]').prop("readonly", true);
		}


		$('.customerContract').on('click', function() {

			if ($('.customerContract').prop('checked')) {
				$('input[name=CONTRACT_NUM]').prop("readonly",false);
				$('input[name=CONTRACT_DATE]').prop("readonly",false);
                $('input[name=CONTRACT_TYPE]').prop("readonly", false);
			} else {
				$('input[name=CONTRACT_NUM]').prop("readonly",true);
				//$('input[name=CONTRACT_DATE]').prop("readonly",true);
                $('input[name=CONTRACT_TYPE]').prop("readonly", true);

				// $.ajax({
					// url: 'return_for_contract_card.php',
					// success: function(data) {
						// console.log(data);
						// if (data) {
							// $('input[name=CONTRACT_NUM]').val(data);
						// }
					// }
				// });
			}
		});

		//



        $('#total_act').on('click', function (event) {
            console.log();
            if ($('#id_akt_vr_num').val() == '') {
                alert('Заполните номер АКТа');
                return false;
            }

            if ($('#id_akt_vr_date').val() == '') {
                alert('Заполните дату АКТа');
                return false;
            }

            var btn_hidden_act = $('.btn_hidden_act');
            $(btn_hidden_act).val('totalBtn');
            event.preventDefault();

            var formTotal = $('#form_total');

            if ($('.check_request')) {

                var arrCkeckId = [];
                $('.check_request').each(function (iRequest, vRequest) {

                    if ($(vRequest).prop('checked')) {

                        var checkName = $(vRequest).attr('name');
                        var re = /\[(.*?)\]/g;

                        checkId = re.exec(checkName);


                        if (checkId[1]) {
                            arrCkeckId.push(checkId[1]);
                        }

                    }
                });


                if (arrCkeckId) {
                    $.ajax({
                        method: "POST",
                        url: "inquiry_for_contract_card.php",
                        data: {arr_check_id: arrCkeckId},
                        dataType: "json",
                        success: function (dataRequest) {

                            if (dataRequest['number'].length > 0) {
                                console.log('length', dataRequest['number'].length);

                                var endElement = dataRequest['number'][dataRequest['number'].length - 1];

                                var strEleRequest = '';
                                var strText = 'заяв';


                                if (dataRequest['number'].length > 1) {
                                    strText += 'кам';
                                } else {
                                    strText += 'ке';
                                }


                                var arrIdRequests = dataRequest['id'];


                                $.each(dataRequest['number'], function (indRequest, eleRequest) {


                                    strEleRequest += eleRequest;


                                    if (endElement != eleRequest) {
                                        strEleRequest += ',';
                                    }
                                });

                                var checkLength = $('.check_request:checked').length

                                function idDeal(vCheck) {
                                    var checkNameAll = $(vCheck).attr('name');
                                    var re = /\[(.*?)\]/g;
                                    //Получаем значение в скобках[]
                                    checkIdAll = re.exec(checkNameAll);

                                    return checkIdAll;
                                }

                                $iCheck = 0;
                                $('.check_request').each(function (iCheck, vCheck) {

                                    idDeal(vCheck);


                                    if (checkIdAll[1] && $(vCheck).prop('checked') && ($.inArray(checkIdAll[1], arrIdRequests) != -1)) {
                                        $iCheck++;
                                    }
                                });

                                if ($iCheck == checkLength) {
                                    alert('По ' + strText + ' ' + strEleRequest + ' акт уже сформирован.');
                                    return false;
                                }


                                saveTotalAct = confirm('По ' + strText + ' ' + strEleRequest + ' акт уже сформирован.Продолжить?');


                                if (saveTotalAct) {


                                    var checkAll = $('.check_request');
                                    if ($(checkAll)) {
                                        $(checkAll).each(function (iCheck, vCheck) {

                                            idDeal(vCheck);


                                            if (checkIdAll[1] && ($.inArray(checkIdAll[1], arrIdRequests) != -1)) {
                                                $(vCheck).val(checkIdAll[1]);
                                            }
                                        });
                                    }

                                    formTotal.submit();
                                }
                            } else {
                                formTotal.submit();
                            }

                        }
                    });
                }

            }
        });



		// $('#form_total').on('submit', function(event) {
			// event.preventDefault();
			// var clientIdValue = $('.client-id').val();
			// var copmpanyIdValue = $('.company-id').val();
			// var allCheckedRequests = $('.check_request:checked');
			// var _this = this;
			// var strIdRequests = findIdRequest(allCheckedRequests);

			// if (strIdRequests) {

				// $.ajax({
					// method: 'POST',
					// url: 'check_request.php',
					// data: {str_id_requests: strIdRequests, id_client_contract: clientIdValue},
					// success: function(dataCheckRequest) {

						// if (dataCheckRequest) {
							// alert(dataCheckRequest);
						// } else {
							// _this.submit();
						// }
					// }
				// });
			// }

		// });


		// $('#form_total').on('click', '.sum-invoice', function(event) {
			// event.preventDefault();
			// var checkRequest = $('.check_request:checked');
			// var nameIsp = $('.name-isp').val();
			// var email = $('.email').val();
			// var get = $('.get').val();

			// var clientId = $('.client-id').val();
			// /*var deals = [];
			// $(checkRequest).each(function(index,el) {

			  // $(checkRequest[index]).attr('name').replace(/\[(.+?)\]/g, function($brackets, $no_brackets) { deals.push($no_brackets) });
			// });

			// var addr = deals.join('_');//Строка с id сделок*/

			// var addr = findIdRequest(checkRequest);


			// /*if (checkRequest.length > 0) {
				// //window.open ('/protocol_generator/sum_invoice.php?ID=' + addr + '&SUM_INVOICE_PDF=SUM_INVOICE_PDF&EMAIL=' + email + '&NAME=' + nameIsp + '&ID_CONTRACT=' + get, '_blank');

			// }*/

			// if (addr) {
				// $.ajax({
					// method: 'POST',
					// url: 'check_request.php',
					// data: {str_id_requests: addr, id_client_contract: clientId},
					// success: function(dataCheckRequest) {

						// if (dataCheckRequest) {
							// alert(dataCheckRequest);
						// } else {
							// window.open ('/protocol_generator/sum_invoice.php?ID=' + addr + '&SUM_INVOICE_PDF=SUM_INVOICE_PDF&EMAIL=' + email + '&NAME=' + nameIsp + '&ID_CONTRACT=' + get, '_blank');
						// }
					// }
				// });
			// }

		// });
	
	});
	</script>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
