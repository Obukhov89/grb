<?php


use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

function dump($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}
$ba_gost=[];
$i=1;
//global $DB;
$res = $DB->Query("SELECT * FROM `OA_VV` WHERE `ID`=".$_GET['ID_V'])->Fetch();
$res_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=".$res['ID_GOST'])->Fetch();
$lab='';
if($res_gost['LFHI']) $lab='ЛФХИ';
elseif($res_gost['LFMI']) $lab='ЛФМИ';
elseif($res_gost['DSL']) $lab='ДСЛ';
elseif($res_gost['LSM']) $lab='ЛСМ';
elseif($res_gost['OSK']) $lab='ОСК';

$assigned = CUser::GetByID($res_gost['ASSIGNED'])->Fetch();

dump($assigned);

$idSI = []; $idIO = []; $idVO = []; $sert=[]; $fl_control = 0;
$res_ob = $DB->Query("SELECT * FROM `ba_connection`,`ba_oborud` WHERE `ba_connection`.`GOST_ID`=" . $res['ID_GOST'] . " AND `ba_oborud`.`ID`=`ba_connection`.`OBORUD_ID` ORDER BY `OBJECT`");
    while ($row = $res_ob->Fetch()) {
		
        $res_certificate_select = $DB->Query("SELECT * FROM `b_verification_certificate` WHERE `id_oborud`=" . (int)$row['OBORUD_ID']);
		$row_certificate = $res_certificate_select->Fetch();
		$certificateName_arr = unserialize($row_certificate['name_f']);
		$cert_arr=array();
		$verif_date = date("Y-m-d", strtotime($res['DATE']));
		$control_date = date("Y-m-d", strtotime($row['POVERKA_LAST']));
		
		if ($verif_date <  $control_date)
		{
			$fl_control = 1;
			echo "В карточке оборудования <a href='/oborud.php?ID=".$row['ID']."'>".$row['OBJECT'] . ", инв. номер " . $row['REG_NUM']. "</a> не указаны"
		}
		
		foreach($certificateName_arr as $cName)
		{
			$cert_arr[]=$cName;
		}
		$ob_v = ($row['certificate_of_verification'] ? $row['certificate_of_verification'] : '');
		$certificateName = $cert_arr[count($cert_arr)-1] ? $cert_arr[count($cert_arr)-1] : $ob_v;	
		$oborud =  $row['OBJECT'] . ", инв. номер " . $row['REG_NUM']. ", зав. номер ".$row['FACTORY_NUMBER'].", ".$certificateName." до ".date("d.m.Y", strtotime($row['POVERKA']));
		if($row['IDENT'] == 'SI') 
		$idSI[] = ['SI' => $oborud, 'GOST_SI' => $row['EQUIP_GOST'], 'COMMENT_SI' => $row['COMMENT']];
		elseif($row['IDENT'] == 'IO')
		$idIO[] = ['IO' => $oborud, 'GOST_IO' => $row['EQUIP_GOST'], 'COMMENT_IO' => $row['COMMENT']];
		elseif($row['IDENT'] == 'VO')
		$idVO[] = ['VO' => $oborud, 'GOST_VO' => $row['EQUIP_GOST'], 'COMMENT_VO' => $row['COMMENT']];
		
		

    }

//	dump($idSI); date_start_verification и date_end_verification


$file = new Bitrix\Main\IO\File(__DIR__ . '/../Validation.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$document->normalizeContent();
$document->setValues([
	'GOST' => $res_gost['GOST'],
	'GOST_YEAR' => $res_gost['GOST_YEAR'] ? "-".$res_gost['GOST_YEAR'] : '',
	'GOST_NAME' => $res_gost['NAME_GOST'],
	'GOST_PUNKT' => $res_gost['GOST_PUNKT'],
	'Lab' => $lab,
	'Assigned' => $assigned['NAME']." ".$assigned['LAST_NAME'],
	'GOST_OBJECT' => $res_gost['GOST_OBJECT'],
	'TEMP' => $res['TEMP'],
	'VLAG' => $res['VLAG'],
	'WORK_POS' => $assigned['WORK_POSITION'],
	'Validation' => $res['VALIDATION'] ? "3.7	Результаты испытаний по ГОСТ ".$res_gost['GOST']." приведены в приложении." : "",
	
	
   'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $idSI,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
	'TableItemSI' => 'Table.Item.SI',
	'TableItemGOST_SI' => 'Table.Item.GOST_SI',
	'TableItemCOMMENT_SI' => 'Table.Item.COMMENT_SI',

    'Table2' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $idIO,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
	'Table2ItemIO' => 'Table2.Item.IO',
	'Table2ItemGOST_IO' => 'Table2.Item.GOST_IO',
	'Table2ItemCOMMENT_IO' => 'Table2.Item.COMMENT_IO',
	
	'Table3' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $idVO,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
	'Table3ItemVO' => 'Table3.Item.VO',
	'Table3ItemGOST_VO' => 'Table3.Item.GOST_VO',
	'Table3ItemCOMMENT_VO' => 'Table3.Item.COMMENT_VO',
]

);

$result = $document->process();
$content = $document->getContent();

//вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Отчет о верификации и валидации.docx"');
header('Cache-Control: max-age=0');
echo $content; 
die;



