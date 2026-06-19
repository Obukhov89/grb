<?php //require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

function getInitials($name)
{
 return mb_substr($name, 0, 1, 'UTF-8') . '.';	
}

function getCurrentYearForDocs()
{
	return "/".date("Y");
}

function num2word($n, $titles)
{
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
}

function right_date($str)
{
	return date("Y-m-d", strtotime($str));
}

function ruDate($str)
{
	return date("d.m.Y", strtotime($str));
}

function nul_change($val)
{
 return ($val ? $val : '');
}

function dump($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}

function tofloat($num) {
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
        ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
  
    if (!$sep) {
        return floatval(preg_replace("/[^0-9]/", "", $num));
    }

    return floatval(
        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
        preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
    );
}

function checkbox_verify($_name)
// Выполняет: проверку checkbox
{
	$result=0;

	if (isset($_REQUEST[$_name]))
	{ if ($_REQUEST[$_name]=='on') {$result=1;}
	}
	return $result;
}

function threeDayAfterSending($date)
{
	if($date){
	$three_day = 259200;
	$current_time_after_sending = time() - strtotime($date);
	if ($current_time_after_sending>$three_day) return 1;
	else return 0;}
	else return 0;
}

function priceDiscount($res_tz, $dataDiscount) {
	
	if ($res_tz['DISCOUNT']) {
		$dataDiscountVal = $res_tz['DISCOUNT'];
	} else {
		$dataDiscountVal = $dataDiscount['UF_CRM_1582914274353']['VALUE'];
	}

	$resultPrice = $res_tz['PRICE'] - ($res_tz['PRICE'] / 100) * $dataDiscountVal;
	
	return $resultPrice;
}

function saveCertificateOborud($post, $file) {
	$arrFieldsCertificate = [];
	
	for ($j = 0; $j < count($post['certificate']); $j++) {
		if ($post['certificate'][$j]['name']) {
			$arrFieldsCertificate[$j]['name'] = $post['certificate'][$j]['name'];
		} else {
			$arrFieldsCertificate[$j]['name'] = '';
		}
		
	}

	for ($i = 0; $i < count($file['certificate']['tmp_name']); $i++) {
		if($file['certificate']['tmp_name'][$i]['file']){
			$info = getimagesize($file['certificate']['tmp_name'][$i]['file']);
			$extension = image_type_to_extension($info[2]);
			$uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/file_oborud/';
			$uploadfile = $uploaddir . uniqid() . time() . '.pdf';

			move_uploaded_file($file['certificate']['tmp_name'][$i]['file'], $uploadfile);

			$arrFieldsCertificate[$i]['file'] = $uploadfile;
		} else {
			$arrFieldsCertificate[$i]['file'] = '';
		}
	}
	
	return [$arrFieldsCertificate];
}

function uploadFileOborud($key) {
	$info = getimagesize($_FILES['certificate']['tmp_name'][$key]['file']);
	$extension = image_type_to_extension($info[2]);
	$uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/file_oborud/';
	$uploadfile = $uploaddir . uniqid() . time() . '.pdf';

	move_uploaded_file($_FILES['certificate']['tmp_name'][$key]['file'], $uploadfile);
	
	return $uploadfile;
}

function num2str($num) {
	$nul='ноль';
	$ten=array(
		array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
		array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
	);
	$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	$unit=array( // Units
		array('копейка' ,'копейки' ,'копеек',	 1),
		array('рубль'   ,'рубля'   ,'рублей'    ,0),
		array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
		array('миллион' ,'миллиона','миллионов' ,0),
		array('миллиард','милиарда','миллиардов',0),
	);
	//
	list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
	$out = array();
	if (intval($rub)>0) {
		foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
			if (!intval($v)) continue;
			$uk = sizeof($unit)-$uk-1; // unit key
			$gender = $unit[$uk][3];
			list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
			// mega-logic
			$out[] = $hundred[$i1]; # 1xx-9xx
			if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
			else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
			// units without rub & kop
			if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
		} //foreach
	}
	else $out[] = $nul;
	$out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
	$out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
	return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}


// Склоняем словоформу 

function morph($n, $f1, $f2, $f5) {
	$n = abs(intval($n)) % 100;
	if ($n>10 && $n<20) return $f5;
	$n = $n % 10;
	if ($n>1 && $n<5) return $f2;
	if ($n==1) return $f1;
	return $f5;
}
//========================== начало 'ФИО и ДОЛЖНОСТЬ родитетьный падеж' ==================================//
// Cклоняем по падежам 
function conversion($name) {
	$gender = morphos\Russian\detectGender($name);
	$cases = morphos\Russian\inflectName($name, null, $gender);
	return $cases;
}
//========================== окончание 'ФИО и ДОЛЖНОСТЬ родитетьный падеж' ==================================//

//----------------------------'договор еще не составлен' начало --------------//
function addNumContract($tzId, $id, $resDog) {
	if(!empty($resDog)) {
		$idContract = !empty($resDog['ID']) ? $resDog['ID'] : '';
		$numberContract = !empty($resDog['NUMBER']) ? $resDog['NUMBER'] : $idContract;
		$dogovor = "Договор №" . $numberContract . " от " . ruDate($resDog['DATE']). ", ID" . $idContract ;
		$dogovorNum = !empty($dogovor) ? $dogovor : '';
		return $dogovorNum;
	}
}
//----------------------------'договор еще не составлен' окончание --------------//
function updateDogovorNum ($resBaTz, $id) {
	$arrDgovor = !empty($resBaTz['DOGOVOR_NUM']) ? explode(' ',$resBaTz['DOGOVOR_NUM']) : '';
	$dateDogovor = !empty($arrDgovor[3]) ? $arrDgovor[3] : '';
	$dogovor = "Договор №" . $_POST['CONTRACT_NUM'] . " от " . $dateDogovor . " ID" . $id ;
	return $dogovor;
}

//------------------------------'Действует на основании' начало------------------------//
//преобразуем певую букву в должности в верхний регистр
function mb_strtoupper_first($str, $encoding = 'UTF8')
{
    return
        mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
        mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
}
//---------------------------------'Действует на основании' окончание -----------------//

function random_float($num1, $num2, $accur=1000000)
{
	if($num1 > $num2)
		return false;
	else
		return mt_rand($num1*$accur, $num2*$accur)/$accur;
}
 ?>