<?php
ini_set('display_errors', '0');


class bot
{


    public $token;
    ///
    public $db;
    ////
    public $step;
    public $title;
    public $chatid;
    public $recmsg;
    public $message_id;
    public $userid;
    public $username;
    public $firstname;
    public $lastname;
    public $datamytext;
    public $datachatid;
    public $phone;
    public $exRecmsg;
    public $exRecGlass;
    public $chattype;
    public $dInline;
    public $msgid;
    public $msgtype;
    public $r;
    // public $phone;
    // public $exRecmsg;
    // public $exRecGlass;
    public $gpFromChatId;
    public $gpFromFirstname;
    public $gpFromLastname;
    public $gpFromUsername;
    public $gpNewMemberChatid;
    public $gpNewMemberFirstname;
    public $gpNewMemberLastname;
    public $gpNewMemberUsername;
    public $gpLeftMemberChatid;
    public $gpLeftMemberFirstname;
    public $gpLeftMemberLastname;
    public $gpLeftMemberUsername;
    public $gpChatTitle;
    public $gpActionType;
    public $gpSecondMemberChatid;
    public $gpSecondMemberFirstname;
    public $gpSecondMemberLastname;
    public $gpSecondMemberUsername;
    public $backKey;
    public $mainKey;
    public $fileidBest;
    public $fileidGood;
    public $fileid;
    public $fileidBad;
    public $fileidVeryBad;
    public $documentFileId;
    public $newMembers;
    public $photoId;
    public $caption;
    public $newMember;
    public $newMemId;
    public $newMemFirst;
    function __construct($token, $content, $dbInstance)
    {
        $this->token = $token;
        ///
        $this->db = $dbInstance;
        ////

        if ($content != '') {
            $update = json_decode($content, true);
            $message = $update["message"];
            $this->recmsg = $message['text'];
            $this->recmsg = str_replace('@teletools_member_counter_bot', '', $this->recmsg);
            $this->message_id = $message['message_id'];
            $this->chatid = $message['chat']['id'];
            $this->userid = $message['from']['id'];
            $this->chattype = $message["chat"]["type"];

            $this->username = $message['from']['username'];
            $this->firstname = $message['from']['first_name'];
            $this->lastname = $message['from']['last_name'];
            $this->dInline = $update["callback_query"];
            $this->msgid   = $update["callback_query"]["message"]["message_id"];
            $this->datamytext = $update["callback_query"]["data"];
            $this->datachatid = $update["callback_query"]["from"]["id"];

            $this->photoId=$update['message']['photo'];
            $this->caption=$update['message']['caption'];

            $this->r;
            if ($this->chatid == '') {
                $this->chatid = $this->datachatid;
                $this->recmsg = $this->datamytext;
            }
            $this->title = $message['chat']['title'];
            //////////////////////////////
            if (isset($message['text'])) {
                $message_type = 'text';
            } elseif (isset($message['photo'])) {
                $message_type = 'photo';
            } elseif (isset($message['gif'])) {
                $message_type = 'gif';
            } elseif (isset($message['video'])) {
                $message_type = 'video';
            } elseif (isset($message['voice'])) {
                $message_type = 'voice';
            } elseif (isset($message['video_note'])) {
                $message_type = 'video_note';
            } else {
                // پیام از نوع مورد قبول نیست
                return;
            }
            $this->msgtype = $message_type;
            /////////////////////////////

            $this->phone = $message["contact"]["phone_number"];
            $erecmsg = str_replace("/start", "", $this->recmsg);
            $erecmsg = str_replace(" ", "", $erecmsg);
            $exrec = explode("_", $erecmsg);
            $this->exRecmsg = $exrec;
            $this->exRecGlass = explode("a", $this->datamytext);


            #####################
            $this->newMemId = $update["chat_member"]["new_chat_member"]["from"]["id"];
            $this->newMemFirst =$update["chat_member"]["new_chat_member"]["from"]["first_name"];
            ########################
            ///Group information
            
            $this->newMembers = $message['new_chat_members'];
            $this->newMember = $message['new_chat_member'];


            $this->gpFromChatId = $message['from']['id'];
            $this->gpFromFirstname = $message['from']['first_name'];
            $this->gpFromLastname = $message['from']['last_name'];
            $this->gpFromUsername = $message['from']['username'];

            $this->gpNewMemberChatid = $message['new_chat_members']['id'];
            $this->gpNewMemberFirstname = $message['new_chat_members']['first_name'];
            $this->gpNewMemberLastname = $message['new_chat_members']['last_name'];
            $this->gpNewMemberUsername = $message['new_chat_members']['username'];


            $this->gpLeftMemberChatid = $message['left_chat_member']['id'];
            $this->gpLeftMemberFirstname = $message['left_chat_member']['first_name'];
            $this->gpLeftMemberLastname = $message['left_chat_member']['last_name'];
            $this->gpLeftMemberUsername = $message['left_chat_member']['username'];
            $this->gpChatTitle = $message['chat']['title'];


            $this->fileidBest = $message['photo'][4]['file_id'];
            $this->fileidGood = $message['photo'][3]['file_id'];
            $this->fileid = $message['photo'][2]['file_id'];
            $this->fileidBad = $message['photo'][1]['file_id'];
            $this->fileidVeryBad = $message['photo'][0]['file_id'];
            $this->documentFileId = $message['document']['file_id'];



            if (isset($message['new_chat_members']['id']) && $message['new_chat_members']['id'] == $this->gpFromChatId) {

                $this->gpActionType = 'joined';
                $this->gpSecondMemberChatid = $message['new_chat_members']['id'];
                $this->gpSecondMemberFirstname = $message['new_chat_members']['first_name'];
                $this->gpSecondMemberLastname = $message['new_chat_members']['last_name'];
                $this->gpSecondMemberUsername = $message['new_chat_members']['username'];
            } elseif (isset($message['new_chat_members']['id'])) {
                $this->gpActionType = 'added';
                $this->gpSecondMemberChatid = $message['new_chat_members']['id'];
                $this->gpSecondMemberFirstname = $message['new_chat_members']['first_name'];
                $this->gpSecondMemberLastname = $message['new_chat_members']['last_name'];
                $this->gpSecondMemberUsername = $message['new_chat_members']['username'];
            } elseif (isset($message['left_chat_member']['id']) && $message['left_chat_member']['id'] == $this->gpFromChatId) {
                $this->gpActionType = 'left';
                $this->gpSecondMemberChatid = $message['left_chat_member']['id'];
                $this->gpSecondMemberFirstname = $message['left_chat_member']['first_name'];
                $this->gpSecondMemberLastname = $message['left_chat_member']['last_name'];
                $this->gpSecondMemberUsername = $message['left_chat_member']['username'];
            } elseif (isset($message['left_chat_member']['id'])) {
                $this->gpActionType = 'removed';
                $this->gpSecondMemberChatid = $message['left_chat_member']['id'];
                $this->gpSecondMemberFirstname = $message['left_chat_member']['first_name'];
                $this->gpSecondMemberLastname = $message['left_chat_member']['last_name'];
                $this->gpSecondMemberUsername = $message['left_chat_member']['username'];
            }
        }
    }

    ############################################################### Group ########################################################
    function sendmessage($text, $chatid, $keys)
    {
        $ch = curl_init("https://api.telegram.org/bot" . $this->token . "/sendmessage?chat_id=$chatid&text=" . urlencode($text) . "&parse_mode=HTML&reply_markup=$keys");
        curl_exec($ch);
    }
    function deleteMessages()
    {

        $api_url = "https://api.telegram.org/bot" . $this->token . "/deleteMessage?chat_id=" . $this->chatid . "&message_id=" . $this->message_id;
        file_get_contents($api_url); //   ‌

    }
    function deleteLink()
    {
        if ($this->hasUrl($this->recmsg) || strpos($this->recmsg, '@') !== false) {
            $this->deleteMessages();
        }

    }
    function hasUrl($text)
    {
        // الگوی عبارت با قاعده برای تشخیص URL
        $pattern = '/\b(?:https?|ftp):\/\/[^\s]+/i';

        // بررسی اینکه آیا حداقل یک لینک وجود دارد
        return preg_match($pattern, $text) === 1;
    }
 function welcomeMessage()
{
    // 
    if (isset($this->newMemId)) {
       
        // $members = $this->newMembers;

        // foreach ($members as $member) {
   
            $first_name = isset($this->newMemFirst) ? $this->newMemFirst : "کاربر ناشناس";
            $user_id = $this->newMemId;

            // 
            $message = $first_name . " عزیز، خوش آمدید به گروه! 🎉";

            //     
            $this->sendmessage($message, $this->chatid, '');
        
    }
}

    function deleteWelcomeMessage() {
         
    }
    //////////////////////////////////////////////////////////OWNER///////////////////////////////////////////////////////////
    function adminInlineKeyboard($chatid)
    {
        $bot_url = "https://api.telegram.org/bot" . $this->token . "/sendMessage";


        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'تعیین کلمات رکیک', 'callback_data' => 'forbidden_words']
                ],
                [
                    ['text' => 'پیام تبلیغ', 'callback_data' => 'tab_msg']
                ]
                // [
                //     ['text' => 'تعیین ساعت تبلیغ', 'callback_data' => 'tab_date']
                // ]
            ]
        ];

        $replyMarkup = json_encode($keyboard);

        $text = "شما ادمین هستید:";

        $postData = [
            'chat_id' => $chatid,
            'text' => $text,
            'reply_markup' => $replyMarkup
        ];

        $ch = curl_init($bot_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
    function deleteAdvertKey($chatid,$text)
    {
        $bot_url = "https://api.telegram.org/bot" . $this->token . "/sendMessage";


        $keyboard = [
            'inline_keyboard' => [
                
                [
                    ['text' => 'حذف تبلیغات قبلی', 'callback_data' => 'delete_advert']
                ]
            ]
        ];

        $replyMarkup = json_encode($keyboard);

        // $text = "شما ادمین هستید:";

        $postData = [
            'chat_id' => $chatid,
            'text' => $text,
            'reply_markup' => $replyMarkup
        ];

        $ch = curl_init($bot_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    function backKey($chatid,$text)
    {
        $bot_url = "https://api.telegram.org/bot" . $this->token . "/sendMessage";


        $keyboard = [
            'inline_keyboard' => [
                
                [
                    ['text' => 'بازگشت', 'callback_data' => 'back']
                ]
            ]
        ];

        $replyMarkup = json_encode($keyboard);

        // $text = "شما ادمین هستید:";

        $postData = [
            'chat_id' => $chatid,
            'text' => $text,
            'reply_markup' => $replyMarkup
        ];

        $ch = curl_init($bot_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    function getOwner($chatid)
    {
        $api_url = "https://api.telegram.org/bot" . $this->token . "/getChatAdministrators?chat_id=" . $chatid;
        $response = file_get_contents($api_url);
        $admins = json_decode($response, true);

        $adminid = null;
        foreach ($admins['result'] as $admin) {
            if ($admin['status'] == 'creator' || $admin['status'] == 'admin' ) {
                $adminid = $admin['user']['id'];
                $this->db->saveAdminId($adminid);
            }
        }
        
    }
    ######################################  ADVERT ####################################################
    
// ////////
    
function sendphoto($address, $chatid, $key, $caption)
{

    $bot_url = "https://api.telegram.org/bot" . $this->token . "/";
        $url = $bot_url . "sendPhoto?caption=" . urlencode($caption) . "&reply_markup=$key&parse_mode=Html&chat_id=" . $chatid;

    $file = '' . 'upload' . '/' . $address .".jpg";
    $mime = mime_content_type($file);
    $info = pathinfo($file);
    $name = $info['basename'];
    $output = new CURLFile($file, $mime, $name);

    $data = array(
        "photo" => $output,
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
    }
    curl_close($ch);
    $data = json_decode($result,true);
    $da = $data['result']['message_id'];
    return $da;
}
function delWelAfterOneMin ($data)  {
    $now =time() ;
    $one_min_ago = $now +12540;
    if (strtotime($data['created_at']) < $one_min_ago) {
        $this->deleteMsg($data['message_id']);
    }
}
function deleteMsg($message_id){
    $api_url = "https://api.telegram.org/bot" . $this->token . "/deleteMessage?chat_id=" . $this->chatid . "&message_id=" . $message_id;
    file_get_contents($api_url);
}


/////////////////
//////////////////
function deleteTelegramMessage($chat_id, $message_id){
    $bot_token = $this->token; // توکن ربات تلگرام شما
    $url = "https://api.telegram.org/bot$bot_token/deleteMessage";

    // تنظیم داده‌ها برای درخواست حذف پیام
    $data = [
        'chat_id' => $chat_id,
        'message_id' => $message_id
    ];

    // ارسال درخواست حذف پیام به API تلگرام
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
/////////////
function saveAdminsToDatabase($chat_id) {
    // URL برای دریافت لیست ادمین‌ها از API تلگرام
    $url = "https://api.telegram.org/bot" . $this->token. "/getChatAdministrators?chat_id=" . $chat_id;

    // استفاده از CURL برای ارسال درخواست
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // دریافت نتیجه
    $result = curl_exec($ch);
    curl_close($ch);

    // تبدیل نتیجه به JSON
    $result_json = json_decode($result, true);

    // بررسی اینکه آیا درخواست موفقیت‌آمیز بوده است یا خیر
    if ($result_json['ok']) {
        // لیست ادمین‌ها
        $admins = $result_json['result'];

        // آماده کردن کوئری SQL برای ذخیره‌سازی در دیتابیس
        // $query = "INSERT INTO admins (user_id) VALUES (:user_id)
        //           ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)";

        // آماده‌سازی کوئری برای اجرای در دیتابیس
        // $stmt = $pdo->prepare($query);

        // ذخیره‌سازی هر ادمین در دیتابیس
        foreach ($admins as $admin) {
            $user = $admin['user'];

            // اجرای کوئری برای هر ادمین
            
                 
                 $this->db->saveAdmin($user['id']);

        }

        // echo "Admins saved successfully!";
    } else {
        // در صورت خطا
        echo "Error: " . $result_json['description'];
    }
}



}
