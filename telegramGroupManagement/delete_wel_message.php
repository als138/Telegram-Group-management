<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');


$content = file_get_contents("php://input");
$update = json_decode($content, true);

include('class/bot.php');
include('class/db.php');

$db = new db();

$bot = new bot("7540254634:AAGsY4BoGFtrOK5NAwfQgnMXHUjqXQI9WsM", $content, $db);
$chatid = -1002412626645; // فقط یه گروه خاص

$messages = $db->deleteExpiredMessages();
 foreach ($messages as $message) {
        // $chat_id = $message['chat_id'];
        $message_id = $message['message_id'];
        $chat_id = -1002412626645; // فقط یه گروه خاص

        
        // Call the function to delete the message
        $bot->deleteTelegramMessage($chat_id, $message_id);

        // Optionally, delete the message from the database after it has been removed from Telegram
        $db->deleteMessageFromDatabase($message_id);
    }