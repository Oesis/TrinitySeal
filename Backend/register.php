<?php
include 'settings.php';
error_reporting(0);

if(!strlen(strstr($_SERVER['HTTP_USER_AGENT'],"TrinitySeal")) <= 0 ) {

$username = xss_clean(mysqli_real_escape_string($con, $_POST['username']));
$password = xss_clean(mysqli_real_escape_string($con, $_POST['password']));
$email = xss_clean(mysqli_real_escape_string($con, $_POST['email']));
$hwid = xss_clean(mysqli_real_escape_string($con, $_POST['hwid']));
$programid = xss_clean(mysqli_real_escape_string($con, $_POST['programtoken']));
$token = xss_clean(mysqli_real_escape_string($con, $_POST['token']));
$ip = $_SERVER['REMOTE_ADDR'];

$username = str_replace("#", "+", $username);
$password = str_replace("#", "+", $password);
$email = str_replace("#", "+", $email);
$hwid = str_replace("#", "+", $hwid);
$programid = str_replace("#", "+", $programid);
$token = str_replace("#", "+", $token);

$username = Decrypt($username);
$password = Decrypt($password);
$email = Decrypt($email);
$hwid = Decrypt($hwid);
$programid = Decrypt($programid);
$token = Decrypt($token);

$username = str_replace("'", "", $username);
$password = str_replace("'", "", $password);
$hwid = str_replace("'", "", $hwid);
$email = str_replace("'", "", $email);
$programid = str_replace("'", "", $programid);
$token = str_replace("'", "", $token);

$sqlerror = Encrypt(json_encode(array("status" => "failed", "info" => "SQL error.")));
$usertaken = Encrypt(json_encode(array("status" => "failed", "info" => "Username already in use.")));
$invalidtoken = Encrypt(json_encode(array("status" => "failed", "info" => "Invalid token.")));
$finaleerror = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to insert data into database. Possible cleaning fail?")));
$tokenerror = Encrypt(json_encode(array("status" => "failed", "info" => "Couldn't update token.")));
$nullentry = Encrypt(json_encode(array("status" => "failed", "info" => "Please fill in all fields before attempting to register!")));
$bruh = Encrypt(json_encode(array("status" => "failed", "info" => "bruh.")));
$userexceed = Encrypt(json_encode(array("status" => "failed", "info" => "Program owner has exceeded their max user quota.")));
$programbanned = Encrypt(json_encode(array("status" => "failed", "info" => "The developer of this program has been banned, therefore you cannot login or register.")));

if(empty($username) || empty($password) || empty($email) || empty($hwid) || empty($programid) || empty($token)){
    die($nullentry);
}

$checkprogram = mysqli_query($con, "SELECT * FROM `programs` WHERE `authtoken` = '$programid'") or die(Encrypt($sqlerror));
            while($row = mysqli_fetch_array($checkprogram)){
            $owner = $row['owner']; 
            $bannned = $row['banned'];
            }
            
            if ($bannned == "1"){
          die($programbanned);
      }
            
if(mysqli_num_rows($checkprogram) > 0){ //program ID exists
$user_check = mysqli_query($con, "SELECT `username` FROM `users` WHERE `username` = '$username' AND `programtoken` = '$programid'") or die(mysqli_error($con));
  
$do_user_check = mysqli_num_rows($user_check);
  
if($do_user_check > 0){
die($usertaken);
}
else{ //username free, carry on..

$grabinfo = mysqli_query($con, "SELECT * FROM `owners` WHERE `username` = '$owner'") or die(mysqli_error($con));
            while($row = mysqli_fetch_array($grabinfo)){
            $subscription = $row['premium']; 
            }

$epicc211 = mysqli_query($con, "SELECT * FROM `users` WHERE `programtoken` = '$programid'") or die(mysqli_error($con));
if ($subscription == "0") {
if(mysqli_num_rows($epicc211) > 50) {     
die($userexceed);
}
}

if ($subscription == "1") {
if(mysqli_num_rows($epicc211) > 5000) {     
die($userexceed);
}
}

$checktoken = mysqli_query($con, "SELECT * FROM `tokens` WHERE `programtoken` = '$programid' AND `token` = '$token' AND `used` = '0'") or die(Encrypt($sqlerror));
if(mysqli_num_rows($checktoken) > 0){
while($row = mysqli_fetch_array($checktoken)){
    $days = $row['days'];
    $level = $row['level'];
}
$updatetoken = mysqli_query($con, "UPDATE `tokens` SET `used` = '1', `used_by` = '$username' WHERE `programtoken` = '$programid' AND `token` = '$token'") or die(Encrypt($sqlerror));
if ($updatetoken) {
$updateuser = mysqli_query($con, "UPDATE `programs` SET `clients` = clients + 1 WHERE `authtoken` = '$programid'") or die(Encrypt($sqlerror));
if ($updateuser) {
$today = new DateTime();
$newDate = $today->modify('+'.$days.' days');
$date2 = $newDate;
$TIME = ''.$date2->format('Y-m-d H:i:s').'';

$pass_encrypted = password_hash($password, PASSWORD_BCRYPT);
  
$addshit = mysqli_query($con, "INSERT INTO `users` (id, username, password, email, level, expires, hwid, ip, banned, programtoken)
VALUES ('', '$username', '$pass_encrypted', '$email', '$level', '$TIME', '$hwid', '$ip', '0', '$programid')") or die(mysqli_error($con));
if ($addshit){
$success = Encrypt(json_encode(array("status" => "success", "info" => "Successfully registered", "username" => $username, "expires" => $TIME, "level" => $level)));
die($success);
}
}
else{
die($finaleerror);
}
}
else{
die($tokenerror);
}
}
else{
die($invalidtoken);
}
}
}
else{
die($bruh);
}
}
else{
    die("You shouldn't be here.");
}

function SaltString($string){
    $string = str_replace("z", "?", $string);
    $string = str_replace("a", "!", $string);
    $string = str_replace("b", "}", $string);
    $string = str_replace("c", "{", $string);
    $string = str_replace("d", "]", $string);
    $string = str_replace("e", "[", $string);
    return $string;
}

function DesaltString($string){
    $string = str_replace("?", "z", $string);
    $string = str_replace("!", "a", $string);
    $string = str_replace("}", "b", $string);
    $string = str_replace("{", "c", $string);
    $string = str_replace("]", "d", $string);
    $string = str_replace("[", "e", $string);
    return $string;
}

    function Encrypt($string)
		{
           $plaintext = $string;
           $password = base64_decode(DesaltString($_POST['session_id']));
           $method = 'aes-256-cbc';
           $password = substr(hash('sha256', $password, true), 0, 32);
           $iv = base64_decode(DesaltString($_POST['session_salt']));
           $encrypted = base64_encode(openssl_encrypt($plaintext, $method, $password, OPENSSL_RAW_DATA, $iv));
           return $encrypted;
		}
		function Decrypt($string)
		{
           $plaintext = $string;
           $password = base64_decode(DesaltString($_POST['session_id']));
           $method = 'aes-256-cbc';
           $password = substr(hash('sha256', $password, true), 0, 32);
           $iv = base64_decode(DesaltString($_POST['session_salt']));
           $decrypted = openssl_decrypt(base64_decode($plaintext), $method, $password, OPENSSL_RAW_DATA, $iv);
           return $decrypted;
		}
   function xss_clean($data)
  {
     return strip_tags($data);
  }




?>