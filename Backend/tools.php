<?php
$type = xss_clean($_GET['type']);
$input = xss_clean($_GET['input']);

if(!strlen(strstr($_SERVER['HTTP_USER_AGENT'],"SealAPI")) <= 0 ){
    
if (strpos($input,"trinityseal") !== false) {
    die("Not resolving TrinitySeal today m8.");
}    

if ($type == "skyperesolver"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=resolve&string=".$input."", false));
}
else if ($type == "usernameresolve"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=resolvedb&string=".$input."", false));
}
else if ($type == "ip2skype"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=ip2skype&string=".$input."", false));
}
else if ($type == "email2skype"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=email2skype&string=".$input."", false));
}
else if ($type == "geoip"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=geoip&string=".$input."", false));
}
else if ($type == "dnsresolver"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=dns&string=".$input."", false));
}
else if ($type == "cloudflareresolver"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=cloudflare&string=".$input."", false));
}
else if ($type == "phoneresolver"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=phonenumbercheck&string=".$input."", false));
}
else if ($type == "siteheaders"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=header&string=".$input."", false));
}
else if ($type == "sitewhois"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=whois&string=".$input."", false));
}
else if ($type == "ping"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=ping&string=".$input."", false));
}
else if ($type == "portscan"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=portscan&string=".$input."", false));
}
else if ($type == "disposablemailcheck"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=disposable_email&string=".$input."", false));
}
else if ($type == "ip2website"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=ip2websites&string=".$input."", false));
}
else if ($type == "domaininfo"){
    die(Call("https://webresolver.nl/api.php?key=UD5EN-MR2I9-LHYL1-BGVT1&action=domaininfo&string=".$input."", false));
}
else{
    die("Invalid type.");
}
}
else{
    die("API can only be accessed from TrinitySeal dll!");
}


function Call($url, $json){
    $curlSession = curl_init();
    curl_setopt($curlSession, CURLOPT_URL, $url);
    curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
    
    if ($json) {
    return json_decode(curl_exec($curlSession));
    }
    else{
        return curl_exec($curlSession);
    }
    curl_close($curlSession);
}



















function xss_clean($data)
  {
     return strip_tags($data);
  }
?>