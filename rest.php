<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09.04.2019
 * Time: 17:10
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

\Bitrix\Main\Loader::includeModule('rest');
$client = new \Bitrix\Main\Web\HttpClient();
$webHookUrl = 'https://niistrom.basis-rus.ru/rest/1/m1xdxlp916y8kpij/';
$prefix = 'documentgenerator';

$data = [
    'id' => 4,
    'providerClassName' => '\\Bitrix\\DocumentGenerator\\DataProvider\\Rest',
    'value' => 1,
];
$url = $webHookUrl.$prefix.'.template.getfields/';
$answer = $client->post($url, $data);
try
{
    $result = \Bitrix\Main\Web\Json::decode($answer);
}
catch(Exception $e)
{
    var_dump($answer);
}

print_r($result);


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>