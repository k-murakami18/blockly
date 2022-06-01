<!--　チューターの作業を記録　-->
<?php
//ini_set('display_errors',1);

//ajaxのデータを取得
$tname = $_POST['tname'];
$tkey = $_POST['tkey'];
$sname = $_POST['sname'];
$task = $_POST['tasknumber'];
$condent = $_POST['content'];
//日本の時間を取得
date_default_timezone_set('Asia/Tokyo');
$time = date('Y/m/d H:i:s');

//MariaDB接続
$con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
//データベース選択
mysqli_select_db($con, 'prepinfo2');
//文字コード指定
mysqli_set_charset($con, "utf8");

//本番用
//result = mysql_query("INSERT INTO `blockly$task` (`process_id`, `blockkey`, `xml`, `time`, `run`) VALUES (NULL, '$blockkey', '$replace', '$time', '');");
//テスト用
$query = "INSERT INTO `tutor$task` (`task_id`, `tutor_name`, `tutor_key`, `studnet_name`, `task_no`, `task_content`, `time`) VALUES (NULL, '$tname', '$tkey', '$sname', '$task', '$condent', '$time');";

$result = mysqli_query($con, $query);

?>
