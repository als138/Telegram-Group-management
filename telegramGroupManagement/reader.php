<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');


$content = file_get_contents("php://input");
$update = json_decode($content, true);

include('class/bot.php');
include('class/db.php');

$db = new db();

$bot = new bot("7540254634:AAGsY4BoGFtrOK5NAwfQgnMXHUjqXQI9WsM", $content, $db);
       // file_put_contents("salimi.txt", print_r($update. PHP_EOL, true), FILE_APPEND);

$cBackData = $bot->datamytext;
$chi = -1002412626645;
#################################### INLINE ##########################################
if ($cBackData == "forbidden_words") {
    $db->setStep($bot->datachatid, 'forbidden_words');
    $bot->sendMessage("کلمه مورد نظر را ارسال کنید", $bot->chatid, '');
}
if ($cBackData == "tab_msg") {
    $db->setStep($bot->datachatid, 'tab_msg');
    $bot->deleteAdvertKey($bot->chatid,"در صورتی که میخواهید تبلیغات قبلی حذف شوند روی دکمه زیر بزنید");
    $bot->sendMessage("تبلیغ خود را ارسال کنید", $bot->chatid, '');
}
if ($cBackData == "delete_advert") {
    $db->deletePhotoAdvert();
            $db->deleteTextAdvert();
    $bot->sendMessage("تبلیغات قبلی حذف شد", $bot->chatid, '');
}
if ($cBackData == "tab_date") {
    $db->setStep($bot->datachatid, 'tab_date');
    $bot->sendMessage("ساعت ارسال تبلیغ در گروه را به فرمت XX:XX وارد کنید ", $bot->chatid, '');
}
if ($cBackData == "back") {
    $db->setStep($bot->datachatid, null);
    $bot->adminInlineKeyboard($bot->chatid);
}
//////////////////////////////###### /INLINE ######///////////////////////////////////

if (isset($bot->recmsg)) {
    
    ######################################### GROUP  ###################################
    if ($bot->chattype === "group" || $bot->chattype === "supergroup") {
        if($bot->chatid == $chi){
            $db->insertMsg($bot->message_id);

        ###############
       // file_put_contents("chatidGroup.log", print_r($bot->chatid . PHP_EOL, true), FILE_APPEND);
        ###############
        # get Admin
        //$bot->getOwner($bot->chatid);
        $bot->saveAdminsToDatabase($bot->chatid);
        # remove Link
        $bot->deleteLink();

        #welcome Message
        $bot->welcomeMessage();
        $db->insertUserId($bot->userid); //موقتی  
        #delete Welcome message after 1 minutes
        /////////////////////////////////////

        /////////////////////////////////////

        # warning forbidden words
        if ($db->containsBannedWord($bot->recmsg)) {
            $db->increaseWarning($bot->userid);
        }
        # status warning
        if ($db->statusWarning($bot->userid) >  1) {
            # ban user for use forbidden word
            $db->addToBanList($bot->userid);
            $db->zeroCheck($bot->userid);
            
        }
        #send warning message 
        if ($db->statusWarning($bot->userid) == 1 && $db->checkWarn($bot->userid) == 0) {
            $bot->sendmessage($bot->firstname . "\n" . "شما از کلمات رکیک استفاده کردید\nدر صورت استفاده دوباره از کلمات رکیک شما برای یک ساعت به حالت سکوت در خواهید آمد", $bot->chatid, '');
            $db->checked($bot->userid);
        }
        #ban user
        if ($db->checkBanTime($bot->userid) == true) {
            #delete message for user baned
            $bot->deleteMessages();
            /////////////////
            $timeRemaining = $db->getTimeRemaining($bot->userid);
            ///////////////
            
            // $bot->sendmessage($bot->firstname . "\nشما تا " . $timeRemaining . " " . "دقیقه دیگر در حالت سکوت قرار دارید", $bot->chatid, '');
            
        }
    }
    } elseif ($bot->chattype === "private") {
        if($db->isAdmin($bot->userid)){
        ######################################### OWNER ##################################
        # OWNER
        #    MAIN INLINE
        if ($bot->recmsg == "/start") {
            $bot->adminInlineKeyboard($bot->chatid);
        }
        # send forbiden Word
        if ($db->getStep($bot->chatid) == 'forbidden_words') {
            // if ($db->checkWord($bot->recmsg) == false) {
            # not exist word
            $db->insertWord($bot->chatid, $bot->recmsg);
            $bot->backKey($bot->chatid, "کلمه ثبت شد \n در صورتی که کلمه دیگری میخواهید اضافه کنید  آن را ارسال کنید در غیر این صورت بازگشت را بزنید");

            // } else {
            # exist word
            //  $bot->sendmessage("کلمه از قبل وارد شده کلمه دیگری وارد کنید",$bot->chatid,'');
            // }


        }
        # tabligh matn
        if ($db->getStep($bot->chatid) == 'tab_msg') {

            $db->textAdvert($bot->userid,$bot->recmsg);
            $bot->backKey($bot->chatid,"اگر تبلیغ دیگری دارید قرار دهید در غیر اینصورت دکمه بازگشت را بزنید");
            // $db->deletePhotoAdvert();
            // $db->deleteTextAdvert();
        }
        # time tabligh
        if ($db->getStep($bot->chatid) == 'tab_date') {
        }
                
    }
    }

}

    if ($db->getStep($bot->chatid) == 'tab_msg') {
        if(isset($bot->photoId)){
            $file_id = $bot->photoId[count($bot->photoId)-1]['file_id'];
            $token = $bot->token;
            // دریافت اطلاعات فایل از تلگرام
            $file_path = "https://api.telegram.org/bot$token/getFile?file_id=$file_id";
            $file_info = file_get_contents($file_path);
            $file_info = json_decode($file_info, TRUE);
        
            // لینک دانلود فایل از سرور تلگرام
             
            $file_url = "https://api.telegram.org/file/bot$token/" . $file_info['result']['file_path'];
        
            // نام فایل برای ذخیره عکس
            $photo_path = 'upload/' . $file_info['result']['file_id'] . '.jpg';
        
            // دانلود و ذخیره عکس در سرور
            file_put_contents($photo_path, file_get_contents($file_url));

            $caption = "";
            // بررسی اینکه آیا کپشنی همراه با عکس وجود دارد
            if(isset($bot->caption)){
                $caption = $bot->caption;
                // ذخیره کپشن در فایل متنی همراه با عکس
                //$caption_file = 'photos/' . $file_info['result']['file_id'] . '.txt';
                //file_put_contents($caption_file, $caption);
            }
            $db->addAdvert($bot->chatid,$file_info['result']['file_id'],$caption);
        
        // $bot->backKey($bot->chatid, "اگر تبلیغ دیگری دارید قرار دهید در غیر اینصورت دکمه بازگشت را بزنید");
        }
    }
    
    

    

###############################

// Check if the webhook contains the new chat member information
if (isset($update['message']['new_chat_members'])) {
    // Get the chat ID where the new user joined
    $chat_id = $update['message']['chat']['id'];

    // Loop through the new members (if multiple users joined at once)
    foreach ($update['message']['new_chat_members'] as $new_member) {
        $first_name = $new_member['first_name']; // Get the first name of the new member

        // Prepare the welcome message
        $message = "سلام $first_name, خوش آمدی به گروه ما!";

        // Send the message to the group using the sendMessage method
       $ms_id = sendMessage($chat_id, $message);
       
       $db->insertWelMsg($ms_id);
    }
}

function sendMessage($chat_id, $message) {
    $url = "https://api.telegram.org/bot7540254634:AAGsY4BoGFtrOK5NAwfQgnMXHUjqXQI9WsM/sendMessage";
    $postData = [
        'chat_id' => $chat_id, // Send to group chat
        'text' => $message
    ];

    // Use curl to send the message
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
   $result= curl_exec($ch);
    curl_close($ch);
    $result_json = json_decode($result, true);

    // بررسی اینکه آیا پیام با موفقیت ارسال شده است یا خیر
    if ($result_json['ok']) {
        // برگرداندن message_id پیام ارسال شده
        return $result_json['result']['message_id'];
    }
}

$datas=$db->welMsgDelete();
foreach ($datas as $data) {
    $bot->delWelAfterOneMin($data);
}