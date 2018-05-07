$json_str = file_get_contents('php://input'); //接收request的body
$json_obj = json_decode($json_str); //轉成json格式

$myfile = fopen("log.txt", "w+") or die("Unable to open file!"); //設定一個log.txt來印訊息
fwrite($myfile, "\xEF\xBB\xBF".$json_str); //在字串前面加上\xEF\xBB\xBF轉成utf8格式

$sender_userid = $json_obj->events[0]->source->userId; //取得訊息發送者的id
$sender_txt = $json_obj->events[0]->message->text; //取得訊息內容
$sender_replyToken = $json_obj->events[0]->replyToken; //取得訊息的replyToken


?>
