<?php
include 'settings.php';
error_reporting(0);
if(!strlen(strstr($_SERVER['HTTP_USER_AGENT'],"TrinitySeal")) <= 0 ) {
$programid = strip_tags(trim(xss_clean(mysqli_real_escape_string($con, $_POST['programtoken']))));
$key = strip_tags(trim(xss_clean(mysqli_real_escape_string($con, $_POST['key']))));
$username = strip_tags(trim(xss_clean(mysqli_real_escape_string($con, $_POST['username']))));
$password = strip_tags(trim(xss_clean(mysqli_real_escape_string($con, $_POST['password']))));
$hwid = strip_tags(trim(xss_clean(mysqli_real_escape_string($con, $_POST['hwid']))));

$programid = str_replace("#", "+", $programid);
$username = str_replace("#", "+", $username);
$password = str_replace("#", "+", $password);
$hwid = str_replace("#", "+", $hwid);

$programid = Decrypt($programid);
$username = Decrypt($username);
$password = Decrypt($password);
$hwid = Decrypt($hwid);

$username = str_replace("'", "", $username);

$password = str_replace("'", "", $password);

$hwid = str_replace("'", "", $hwid);

$programid = str_replace("'", "", $programid);

$key = str_replace("#", "+", $key);

$key = Decrypt($key);

$key = str_replace("'", "", $key);


$sqlerror = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to grab variables. SQL error.")));
$nullentry = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to grab variables. Invalid input, please fill in all fields!")));
$invalidkey = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to grab variables. Invalid variable key.")));
$sqlerror = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to grab variables. SQL error.")));
$incorrectdetails = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to grab variables. Incorrect username or password.")));
$userbanned = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to grab variables. Your account has been banned!")));
$incorrecthwid = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to grab variables. Incorrect machine ID.")));
$timeexpired = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to grab variables. Your time has expired!")));
$nullentry = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to grab variables. Please fill in all fields before attempting to login!")));
$resethwid = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to grab variables. Your HWID has been reset, please login again.")));

if(empty($programid) || empty($username) || empty($password) || empty($hwid)){
    die($nullentry);
}

$checkprogram1 = mysqli_query($con, "SELECT * FROM `programs` WHERE `authtoken` = '$programid'") or die(Encrypt($sqlerror));
if(mysqli_num_rows($checkprogram1) > 0){ //program ID exists
$result = mysqli_query($con, "SELECT * FROM `users` WHERE `username` = '$username' AND `programtoken` = '$programid'") or die(mysqli_error($con));

while($row1 = mysqli_fetch_array($checkprogram1)){
        $hwidlock = $row1['hwidlock'];
      }

if(mysqli_num_rows($result) < 1){
    
die($incorrectdetails); //username doesn't exist
      
}elseif(mysqli_num_rows($result) > 0){

      while($row = mysqli_fetch_array($result)){
        $user = $row['username'];
        $pass = $row['password'];
        $level = $row['level'];
        $isbanned = $row['banned'];
        $hwidd = $row['hwid'];
        $expires = $row['expires'];
        $ip = $row['ip'];
        $email = $row['email'];
      } //username exists, carry on..
if(strtolower($username) == strtolower($user) && (password_verify($password, $pass))){ //check username and pass after all checks are done..
      if ($hwidd == "RESET"){
       $lulzz = mysqli_query($con, "UPDATE `users` SET `hwid` = '$hwid' WHERE `username` = '$username' AND `programtoken` = '$programid'") or die(Encrypt($sqlerror));
       if ($lulzz) {
       die($resethwid);   
       }
       else{
         die($sqlerror);
       }
      }
      
      $date = new DateTime($expires);
      $today = new DateTime();
      if ($date < $today){
        die($timeexpired);
      }
      else {
      if ($isbanned == 1){
      die($userbanned);
      }
       else{ //user isn't banned, next check..
        if ($hwid == $hwidd || $hwid === $hwidd){ //hwid matches, carry on...
         $checkprogram = mysqli_query($con, "SELECT * FROM `programs` WHERE `variablekey` = '$key' AND `authtoken` = '$programid'") or die(Encrypt($sqlerror));
if(mysqli_num_rows($checkprogram) > 0){
$encrypted_var = array();
$sql="select * from `vars` where `programtoken` = '$programid'";
$result=mysqli_query($con,$sql);
while($row = $result->fetch_assoc()) {
	 $encrypted_var[$row["name"]] = Encrypt($row["value"]);
}
die(Encrypt('{"status":"success","vars":'.json_encode($encrypted_var, JSON_FORCE_OBJECT).'}'));

}
else{
   die($invalidkey); 
}
        }
        else{ //hwid doesn't exist
        if ($hwidlock == "1") { 
        die($incorrecthwid);
        }
        else{
         $checkprogram = mysqli_query($con, "SELECT * FROM `programs` WHERE `variablekey` = '$key' AND `authtoken` = '$programid'") or die(Encrypt($sqlerror));
if(mysqli_num_rows($checkprogram) > 0){
$encrypted_var = array();
$sql="select * from `vars` where `programtoken` = '$programid'";
$result=mysqli_query($con,$sql);
while($row = $result->fetch_assoc()) {
	 $encrypted_var[$row["name"]] = Encrypt($row["value"]);
}
die(Encrypt('{"status":"success","vars":'.json_encode($encrypted_var, JSON_FORCE_OBJECT).'}'));

}
else{
   die($invalidkey); 
}
        }
        }
       }
     }
}
else{
 die($incorrectdetails);
}
}
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