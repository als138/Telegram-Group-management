<?php


// class db {}
// include("asl.php");

// try {
//     $conn = new PDO("mysql:host=$servername;dbname=$mydb", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
//     file_put_contents("telegram_tut.log", print_r("ok", true), FILE_APPEND);
// } catch (PDOException $e) {
//     file_put_contents("telegram_tut.log", print_r($e, true), FILE_APPEND);
// }

class db
{
    public $conn;

    function __construct()
    {
        include("asl.php");
        $conn = new PDO("mysql:host=$servername;dbname=$mydb", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn = $conn;
    }

    ##################################################### GROUP #################################################################


    ##################################################### OWNER ####################################################################
    public function getStep($admin_id)
    {
        $stmt = $this->conn->prepare("SELECT step FROM admin_steps WHERE admin_id = :admin_id");
        $stmt->execute([':admin_id' => $admin_id]);
        return $stmt->fetchColumn();
    }

    public function setStep($admin_id, $step)
    {
        //INSERT INTO user_steps (user_id, step) VAUES (:user_id, :step) ON DUPLICATE KEY UPDATE step = :step
        $stmt = $this->conn->prepare("INSERT INTO admin_steps (admin_id, step) VALUES (:admin_id, :step) ON DUPLICATE KEY UPDATE step = :step");
        $stmt->execute([':admin_id' => $admin_id, ':step' => $step]);
    }
    function saveAdminId($adminid)
    {
        $stmt = $this->conn->prepare("INSERT INTO admin (admin_id) VALUES (:admin_id) ON DUPLICATE KEY UPDATE admin_id = :admin_id");
        $stmt->execute([':admin_id' => $adminid]);
    }
    function checkWord($word)
    {
        $sql = "select * from forb_words where word='" . $word . "'  ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $numrow = $stmt->rowCount();
        if ($numrow == 0) {
            return false;
        }
        return true;
    }
    function insertWord($adminid, $word)
    {
        $stmt = $this->conn->prepare("INSERT INTO forb_words (admin_id , word ) VALUES (:admin_id , :word)");
        $stmt->execute([':admin_id' => $adminid, ':word' => $word]);
    }
    
function containsBannedWord($message) {
    // دریافت لیست کلمات از دیتابیس
    $query = "SELECT word FROM forb_words";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    
    // تمام کلمات را در قالب یک آرایه دریافت کنید
    $words = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $c=0;
    // بررسی هر کلمه از دیتابیس در پیام کاربر
    foreach ($words as $word) {
        // بررسی اینکه آیا کلمه در پیام وجود دارد
        if (stripos($message, $word) !== false) {
            $c=$c+1; // اگر کلمه یافت شد، true برگردانید
        }
    }
    if($c == 0){
        return false;
    }else{
        return true;
    }
    // اگر هیچ کلمه‌ای یافت نشد، false برگردانید
    
}

    ########################################## WARNING ############################################
    function insertUserId($user_id)
    {
        $stmt = $this->conn->prepare("INSERT INTO user (user_id) VALUES (:user_id) ON DUPLICATE KEY UPDATE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
    }
    function increaseWarning($user_id)
    {
        $stmt = $this->conn->prepare("UPDATE user SET warning = warning + 1 WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
    }
    function statusWarning($user_id)
    {
        $stmt = $this->conn->prepare("SELECT warning FROM user WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchColumn();
    }
    function deleteWarning($user_id)
    {
        $stmt = $this->conn->prepare("UPDATE user SET warning = 0 WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
    }
    function addToBanList($user_id)
    {
        $stmt = $this->conn->prepare("INSERT INTO user_silent (user_id) VALUE (:user_id) ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)"); // ON DUPLICATE KEY UPDATE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $this->deleteWarning($user_id);
    }

    function deleteBan($user_id)
    {

        $stmt = $this->conn->prepare("DELETE FROM user_silent WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
    }
    function checkBanTime($user_id)
    {
        $stmt = $this->conn->prepare("SELECT silent_date FROM user_silent WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $row = $stmt->fetchColumn();
        $current_time = time();
        $one_hour_ago = $current_time + 9000; //+ 9000; // convert to local time (12600 - 3600 = 9000)
        if ($row) {
            if (strtotime($row) < $one_hour_ago) {
                $this->deleteBan($user_id);
                return false;
                ///////////////////
                /////////////////////////
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
    function getTimeRemaining($user_id)
    {
        $stmt = $this->conn->prepare("SELECT silent_date FROM user_silent WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $row = $stmt->fetchColumn();
        $current_time = time();
        $past = $current_time - strtotime($row) + 12600;
        $rem = 3600 - $past;
        $min = $rem / 60;
        return $min;
    }
    function checkWarn($user_id){
        $stmt = $this->conn->prepare("SELECT check_warning FROM user WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $row = $stmt->fetchColumn();
        return $row;
    }
    function checked($user_id) {
        
        $stmt = $this->conn->prepare("UPDATE user SET check_warning = 1 WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);

        
    }
    function zeroCheck($user_id){
        $stmt = $this->conn->prepare("UPDATE user SET check_warning = 0 WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
    }
    ######################################### /WARNING #############################################

    ########################################## ADVERT ##################################################
    function addAdvert($admin_id,$addr_photo,$caption) {
        $stmt=$this->conn->prepare("INSERT INTO advert (admin_id,addr_photo,caption) VALUE (:admin_id,:addr_photo,:caption)");
        $stmt->execute([':admin_id' => $admin_id ,':addr_photo'=>$addr_photo , ':caption'=>$caption ]);
    }
    function textAdvert($admin_id,$text){
        $stmt=$this->conn->prepare("INSERT INTO advert_text (admin_id,text) VALUE (:admin_id,:text)");
        $stmt->execute([':admin_id' => $admin_id ,':text'=>$text ]);
    }
    function getAdverts(){
        $sql = "SELECT * FROM advert";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        // $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $data = $stmt->fetchAll();
        return $data;
    }
    function getTextAdverts(){
        $sql = "SELECT * FROM advert_text";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $data = $stmt->fetchAll();
        return $data;
        // $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // return $row['text'];
    }
    function insertAdvertMessageId($messageId){
        $stmt=$this->conn->prepare("INSERT INTO advert_message_id (message_id) VALUE (:message_id)");
        $stmt->execute([':message_id' => $messageId]);
    }
    function getMessageId(){
        $sql = "SELECT * FROM advert_message_id";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        // $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $data = $stmt->fetchAll();
        return $data;
    }
    function insertMsg($message_id){
        //
        $stmt=$this->conn->prepare("INSERT INTO message (message_id) VALUES (:message_id)");
        $stmt->execute([':message_id' => $message_id ]);
    }
    function deleteMsgAfterWeek(){
        //DELETE FROM message
        $sql = "SELECT * FROM message";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        // $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $data = $stmt->fetchAll();
        return $data;
    }
    function deleteTextAdvert()  {
        $stmt=$this->conn->prepare("DELETE FROM advert_text ");
        $stmt->execute();
    }
    function deletePhotoAdvert()  {
        $stmt=$this->conn->prepare("DELETE FROM advert ");
        $stmt->execute();
    }

    ################################# WELCOME MESSAGE ##############################
   function welMsgDelete(){
        $sql = "SELECT * FROM welcome_messages ";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        // $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $data = $stmt->fetchAll();
        return $data;
        
    }
    function insertWelMsg($message_id){
        //
        $stmt=$this->conn->prepare("INSERT INTO welcome_messages (message_id) VALUES (:message_id)");
        $stmt->execute([':message_id' => $message_id ]);
    }
    /////////////////////
    /////////////////////////

    function deleteExpiredMessages(){
        // SQL query to select messages where the current time is greater than the stored time + 1 minute
        $sql = "SELECT message_id, created_at FROM welcome_messages WHERE created_at <= NOW() - INTERVAL 1 MINUTE";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $messages;
    }
    function deleteMessageFromDatabase($message_id){
        // SQL query to delete the message from the database
        $sql = "DELETE FROM welcome_messages WHERE message_id = :message_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':message_id', $message_id);
        $stmt->execute();
    }
    function saveAdmin($admin_id){
        //"INSERT INTO admins (user_id) VALUES (:user_id)
                //   ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)"//
                $stmt=$this->conn->prepare("INSERT INTO admin (admin_id) VALUES (:admin_id) ON DUPLICATE KEY UPDATE admin_id = VALUES(admin_id)");
        $stmt->execute([':admin_id' => $admin_id ]);
    }
    
        function isAdmin($admin_id){
            //"SELECT COUNT(*) FROM admins WHERE user_id = :user_id"
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM admin WHERE admin_id = :admin_id");
        $stmt->execute([':admin_id' => $admin_id]);
        $row = $stmt->fetchColumn();
        if($row > 0){
            return true;
        }else{
            return false;
        }
          
        }
}
