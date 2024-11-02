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


$messageIds = $db->getMessageId();
foreach ($messageIds as $messageId) {
    deleteMessagesAdvert($messageId['message_id'], $chatid,$bot->token);
}




function deleteMessagesAdvert($messageId, $chatid,$token)
{

    $api_url = "https://api.telegram.org/bot" . $token . "/deleteMessage?chat_id=" . $chatid . "&message_id=" . $messageId;
    file_get_contents($api_url); //   ‌

}
