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








// ٫تبلیغاتی که عکس و کپشن است
$adverts = $db->getAdverts();

foreach ($adverts as $advert) {
    $address = $advert['addr_photo'];
    
    $addr = 'upload/' . $address . '.jpg';
    $caption = $advert['caption'];
    
    // $bot->sendphoto($addr, $chatid, '', $caption);
    //$messageId = sendTelegramPhoto($bot->token, $chatid, $address, $caption);
    $messageId = $bot->sendphoto($address,$chatid,'',$caption);
    file_put_contents("aaaaaaaaaa.log", print_r($messageId. PHP_EOL, true), FILE_APPEND);
    $db->insertAdvertMessageId($messageId);
}



# فقط تبلیغاتی که متنی اند
$textAdverts = $db->getTextAdverts();
foreach ($textAdverts as $textAdvert) {
    
    $msgid = sendTelegramMessage($bot->token, $chatid, $textAdvert['text']);
    $db->insertAdvertMessageId($msgid);
}


// هر ۱۲ ساعت
#function
function sendTelegramPhoto($botToken, $chatId, $photoUrl, $caption)
{
    // URL API تلگرام برای ارسال عکس
    $url = "https://api.telegram.org/bot$botToken/sendPhoto";

    // پارامترهای درخواست
    $postData = [
        'chat_id' => $chatId,
        'photo' => $photoUrl,
        'caption' => $caption
    ];

    // استفاده از cURL برای ارسال درخواست
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    // پاسخ را به آرایه JSON تبدیل می‌کنیم
    $responseData = json_decode($response, true);

    // بررسی موفقیت آمیز بودن درخواست
    if ($responseData['ok']) {
        // آیدی پیام ارسال شده را برمی‌گرداند
        return $responseData['result']['message_id'];
    } else {
        // در صورت بروز خطا، متن خطا را برمی‌گرداند
        return "Error1: " . $responseData['description'];
    }
}

function sendTelegramMessage($botToken, $chatId, $message)
{
    // URL API تلگرام برای ارسال پیام
    $url = "https://api.telegram.org/bot$botToken/sendMessage";

    // پارامترهای درخواست
    $postData = [
        'chat_id' => $chatId,
        'text' => $message
    ];

    // استفاده از cURL برای ارسال درخواست
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    // پاسخ را به آرایه JSON تبدیل می‌کنیم
    $responseData = json_decode($response, true);

    // بررسی موفقیت آمیز بودن درخواست
    if ($responseData['ok']) {
        // آیدی پیام ارسال شده را برمی‌گرداند
        return $responseData['result']['message_id'];
    } else {
        // در صورت بروز خطا، متن خطا را برمی‌گرداند
        return "Error2: " . $responseData['description'];
    }
}
