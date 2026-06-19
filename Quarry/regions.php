<?
 require_once ($_SERVER['DOCUMENT_ROOT']) . '/bitrix/header.php';

 $APPLICATION->SetTitle('Карта карьеров');
 include $_SERVER['DOCUMENT_ROOT'] . '/include/function.php';
 include $_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php';

?>

<!DOCTYPE html>

<html>

<head>
    <title>Карта карьеров</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="https://api-maps.yandex.ru/2.1-dev/?apikey=395f00ca-8c2e-479b-918a-0f4ad308eb72&lang=ru_RU" type="text/javascript"></script>
    <link rel="stylesheet" href="https://yastatic.net/bootstrap/3.3.4/css/bootstrap.min.css"/>
    <script src="https://yastatic.net/jquery/2.2.3/jquery.min.js"></script>
    <script src="https://yastatic.net/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="regions.js" type="text/javascript"></script>

    <style>
        html, body, #map {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
        }
    </style>
</head><body>
    <div id="map" style="width: 1200px; height: 600px"></div>
</body></html>

<?
require_once ($_SERVER['DOCUMENT_ROOT']) . '/bitrix/footer.php';
?>