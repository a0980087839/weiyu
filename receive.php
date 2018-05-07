<?php

$json_str = file_get_contents('php://input'); //接收request的body
$json_obj = json_decode($json_str); //轉成json格式

$myfile = fopen("log.txt", "w+") or die("Unable to open file!"); //設定一個log.txt來印訊息
fwrite($myfile, "\xEF\xBB\xBF".$json_str); //在字串前面加上\xEF\xBB\xBF轉成utf8格式

$sender_userid = $json_obj->events[0]->source->userId; //取得訊息發送者的id
$sender_txt = $json_obj->events[0]->message->text; //取得訊息內容
$sender_replyToken = $json_obj->events[0]->replyToken; //取得訊息的replyToken

$sender_txt=rawurlencode($sender_txt); //因為使用get的方式呼叫luis api，所以需要轉碼
$ch = curl_init('https://westus.api.cognitive.microsoft.com/luis/v2.0/apps/cd632f15-651e-4aba-8bb7-53574d0a058f?subscription-key=2a863065f54443ea94d997c076d6727b&staging=true&verbose=true&timezoneOffset=0&q='.$sender_txt); 
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result_str = curl_exec($ch);
fwrite($myfile, "\xEF\xBB\xBF".$result_str); //在字串前加上\xEF\xBB\xBF轉成utf8格式
$result = json_decode($result_str);
$ans_txt = $result -> topScoringIntent -> intent;
$response = array (
"to" => $sender_userid,
"messages" => array (
array (
"type" => "text",
"text" => $ans_txt
)
)
);
//fwrite($myfile, "\xEF\xBB\xBF".json_encode($response)); //在字串前面加上\xEF\xBB\xBF轉成utf8格式
$header[] = "Content-Type: application/json";
$header[] = "Authorization: Bearer zXq+0AN6U2Qg+vItWGLyzN3bxMrwD0b485KPzI00cPBGMPzmL/NTVuCSnLvxvwHXIOXzUFYmyUrVcs+TVempzKtQRXGIHzDkOsgqK4kjJXhjb4TyvwbbGhrYbFKGaHBSfzSsF3UHUh/YmYn3f+vD2gdB04t89/1O/w1cDnyilFU=";
$ch = curl_init("https://api.line.me/v2/bot/message/push");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response)); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 

?>
