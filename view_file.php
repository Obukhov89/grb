<?php
// Конфигурация
$secretKey = 'bnjvskdvjfsaczva!'; // Должен совпадать с ключом в генераторе
$allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt']; // Разрешенные для просмотра типы

// Проверка параметров
if (!isset($_GET['file']) || !isset($_GET['exp']) || !isset($_GET['hash'])) {
    http_response_code(400);
    die('Неверные параметры запроса');
}

// Декодирование и проверка данных
$filePath = base64_decode($_GET['file']);
$expiry = (int)$_GET['exp'];
$hash = $_GET['hash'];

// Валидация ссылки
function validateViewLink($filePath, $expiry, $hash) {
    global $secretKey;
    $expectedHash = hash_hmac('sha256', $filePath . $expiry, $secretKey);
    return hash_equals($hash, $expectedHash) && (time() < $expiry);
}

if (!validateViewLink($filePath, $expiry, $hash)) {
    http_response_code(403);
    die('Ссылка недействительна или срок ее действия истек');
}

// Проверка существования файла
if (!file_exists($filePath)) {
    http_response_code(404);
    die('Файл не найден');
}

// Проверка типа файла
$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedTypes)) {
    http_response_code(403);
    die('Этот тип файла нельзя просматривать');
}

// Определение Content-Type
$mimeTypes = [
    'pdf'  => 'application/pdf',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
    'txt'  => 'text/plain',
];

$contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

// Отправка файла для просмотра в браузере
header('Content-Type: ' . $contentType);
header('Content-Length: ' . filesize($filePath));
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', $expiry));
header('Cache-Control: public, max-age=' . ($expiry - time()));

readfile($filePath);
exit;
?>