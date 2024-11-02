<?php
include "asl.php";
// اتصال به پایگاه داده و دریافت پیام‌های قدیمی‌تر از یک دقیقه
$pdo = new PDO("mysql:host=$servername;dbname=$mydb", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));

// دریافت پیام‌هایی که باید حذف شوند (بیش از یک دقیقه گذشته)
$stmt = $pdo->query("SELECT * FROM messages WHERE created_at < NOW() - INTERVAL 1 MINUTE");
$messages = $stmt->fetchAll();

$botToken = "7540254634:AAGsY4BoGFtrOK5NAwfQgnMXHUjqXQI9WsM";

// حذف هر پیام
foreach ($messages as $message) {
    deleteMessage($message['chat_id'], $message['message_id']);
    // حذف پیام از پایگاه داده
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$message['id']]);
}

// تابع حذف پیام
function deleteMessage($chatId, $messageId) {
    global $botToken;
    $url = "https://api.telegram.org/bot$botToken/deleteMessage";
    $data = [
        'chat_id' => $chatId,
        'message_id' => $messageId
    ];

    $options = [
        'http' => [
            'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}
?>
