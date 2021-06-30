<?php

if(empty($_GET["pass"]) or empty($_GET["pass"]) or empty($_GET["token"])){ //I know that's not a secure way to do tokens...
echo "LOGIN ERROR";
die();
}

if(md5($_GET["token"])!=="529c33c057279833e0b1b50ebf149b64"){
echo "TOKEN ERROR";
die();
}


$user = $_GET["user"];
$pass = $_GET["pass"];


/* GET VERIFICATION TOKEN*/
$cURLConnection = curl_init();

curl_setopt($cURLConnection, CURLOPT_URL, 'https://operation.notimeapi.com:443/');
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
    "Connection: close",
    "Upgrade-Insecure-Requests: 1",
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML
    like Gecko) Chrome/85.0.4183.121 Safari/537.36",
    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
    "Sec-Fetch-Site: cross-site",
    "Sec-Fetch-Mode: navigate",
    "Sec-Fetch-User: ?1",
    "Sec-Fetch-Dest: document",
    "Referer: https://www.notime.ch/",
    "Accept-Language: en-US,en;q=0.9"
));

$output = curl_exec($cURLConnection);
curl_close($cURLConnection);
libxml_use_internal_errors(true);
$dom = new DOMDocument();
@$dom->loadHTML($output);
libxml_use_internal_errors(false);
$xpath = new DOMXpath($dom);
$node = $xpath->query('//input[@name="__RequestVerificationToken"]/attribute::value');
$token = $node->item(0)->nodeValue;


/* LOGIN */
$cURLConnection = curl_init();

curl_setopt($cURLConnection, CURLOPT_URL, 'https://operation.notimeapi.com:443/Home/Login');
curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, array("Login"=> $user, "Password"=> $pass, "ReturnUrl"=>""));
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
curl_setopt($cURLConnection, CURLOPT_HEADER, 1);

curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
    "Connection: close",
    "x-csrf-token: ".$token,
    "Upgrade-Insecure-Requests: 1",
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML
    like Gecko) Chrome/85.0.4183.121 Safari/537.36",
    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
    "Sec-Fetch-Site: cross-site",
    "Sec-Fetch-Mode: navigate",
    "Sec-Fetch-User: ?1",
    "Sec-Fetch-Dest: document",
    "Referer: https://www.notime.ch/",
    "Accept-Language: en-US,en;q=0.9"
));

$output = curl_exec($cURLConnection);

preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $output, $matches);
$cookies = array();
foreach($matches[1] as $item) {
    parse_str($item, $cookie);
    $cookies = array_merge($cookies, $cookie);
}
$cookies_str="";
foreach($cookies as $k=>$v){
  $cookies_str.=$k."=".$v."; ";
}
$cookies_str = substr($cookies_str, 0, -2);
curl_close($cURLConnection);

/* Get timeslot data*/

$cURLConnection = curl_init();
$date = new DateTime('now');
$from = $date->format('d.m.Y');
$unix = $date->format('U');
$date = new DateTime('now +1 month');
$to = $date->format('d.m.Y');
curl_setopt($cURLConnection, CURLOPT_URL, 'https://operation.notimeapi.com/TimeSlotPicker/GetTimeSlots?from='.$from.'&to='.$to.'&_='.$unix);
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
    "Connection: close",
    "Upgrade-Insecure-Requests: 1",
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36",
    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
    "Sec-Fetch-Site: none",
    "Sec-Fetch-Mode: navigate",
    "Sec-Fetch-User: ?1",
    "Sec-Fetch-Dest: document",
    "Accept-Encoding: deflate",
    "Accept-Language: en-US,en;q=0.9",
    "Cookie: ".$cookies_str
));

$output = curl_exec($cURLConnection);
curl_close($cURLConnection);

$json = json_decode(json_decode($output, true), true);


/*create ical*/
include 'ICS.php';

header('Content-Type: text/calendar; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Disposition: attachment; filename=calendar.ics');


$content = array();


$events = $json["events"];

foreach ($events as $k => $v){
if ($v["Status"]==2 or $v["Status"]==1){
    $content[]=array(
    'location' => "",
    'description' => $v["TimeSlotTemplateDescriptionText"],
    'dtstart' => $v["start"],
    'dtend' => $v["end"],
    'summary' => $v["TimeSlotTemplateText"],
    'url' => ""
    );
    }

}

$ics = new ICS($content);
echo $ics->to_string();
