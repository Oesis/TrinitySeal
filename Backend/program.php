<?php
include 'settings.php';
error_reporting(0);
if(!strlen(strstr($_SERVER['HTTP_USER_AGENT'],"TrinitySeal")) <= 0 ) {
$programid = xss_clean(mysqli_real_escape_string($con, $_POST['programtoken']));

$programid = str_replace("#", "+", $programid);

$programid = Decrypt($programid);

$programid = str_replace("'", "", $programid);

$sqlerror = Encrypt(json_encode(array("status" => "failed", "info" => "SQL error.")));
$programbanned = Encrypt(json_encode(array("status" => "failed", "info" => "This program has been banned!")));
$noprogram = Encrypt(json_encode(array("status" => "failed", "info" => "Unable to find a program with this ID, contact the developer.")));


$checkprogram = mysqli_query($con, "SELECT * FROM `programs` WHERE `authtoken` = '$programid'") or die(Encrypt($sqlerror));
if(mysqli_num_rows($checkprogram) > 0){ //program ID exists
while($row = mysqli_fetch_array($checkprogram)){
        $version = $row['version'];
        $name = $row['name'];
        $banned = $row['banned'];
        $clients = $row['clients'];
        $freemode = $row['freemode'];
        $enabled = $row['enabled'];
        $message = $row['message'];
        $downloadlink = $row['downloadlink'];
        $hash = $row['hash'];
        $filename = $row['filename'];
        $devmode = $row['developermode'];
        $hwidlock = $row['hwidlock'];
      } 
      if ($banned == 1){
        die($programbanned);
      }
      else{
        $success = Encrypt(json_encode(array("status" => "success", "info" => "Successfully grabbed variables", "version" => $version, "clients" => $clients, "freemode" => $freemode, "enabled" => $enabled, "message" => $message, "downloadlink" => $downloadlink, "hash" => $hash, "filename" => $filename, "devmode" => $devmode, "hwidlock" => $hwidlock, "programname" => $name)));
        die($success);
      }
    }
else {
die($noprogram);
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