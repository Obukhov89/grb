<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

// Получаем хэш из URL
$hash = $_GET['hash'] ?? '';

if (!$hash) {
    header('HTTP/1.0 404 Not Found');
    die('File not found');
}

// Ищем файл в кеше Битрикс
$cache = Bitrix\Main\Data\Cache::createInstance();
$cacheKey = 'file_' . $hash;
$cacheDir = '/file_access/';

// Если хэш есть в кеше, получаем путь к файлу
if ($cache->initCache(3600, $cacheKey, $cacheDir)) {
    $filePath = $cache->getVars();

    // Проверяем, существует ли файл
    if (file_exists($filePath)) {
        $mimeType = mime_content_type($filePath);
        $fileSize = filesize($filePath);

        // Отдаем файл
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
        readfile($filePath);
        exit;
    }
}

// Если хэш невалидный или файл не найден
header('HTTP/1.0 404 Not Found');
die('File not found or link expired');