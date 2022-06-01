<!-- どのタイミングて課題を提出したか記録する -->
<?php
//ajaxのデータを取得
$blockkey = $_POST['blockkey'];
$xml = $_POST['blockXML'];
$task = $_POST['tasknumber'];
//日本の時間を取得
date_default_timezone_set('Asia/Tokyo');
$time = date('Y/m/d H:i:s');

//余計な空白を削除（2行以上空いていたら削除している）
$blank = str_replace(array("  ", "　　"), "", $xml);
//改行を削除する
$xml = str_replace(array("\r\n", "\r", "\n"), "", $blank);

echo 'XML';
echo $xml;

//MariaDB接続
$con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
//データベース選択
mysqli_select_db($con, 'prepinfo2');
//文字コード指定
mysqli_set_charset($con, "utf8");

//本番用
$block_query = "SELECT * FROM blockly$task";
$block_result = mysqli_query($con, $block_query);

//最新の作業を取得
while ($row = mysqli_fetch_assoc($block_result)) {
  if (strpos($row['blockkey'], $blockkey) === false) {
  } else {
    $block_run_id = $row['process_id'];
  }
}

$comment_query = "SELECT * FROM comment$task";
$comment_result = mysqli_query($con, $comment_query);

//最新の作業を取得
while ($row2 = mysqli_fetch_assoc($comment_result)) {
  if (strpos($row2['blockkey'], $blockkey) === false) {
  } else {
    $comment_run_id = $row2['process_id'];
  }
}

//最新の作業で実行したことにする
$query2 = "UPDATE blockly$task SET xml = '$xml' , submit = 'submit' WHERE blockly$task.process_id = '$block_run_id';";
mysqli_query($con, $query2);
$query3 = "UPDATE comment$task SET xml = '$xml' , submit = 'submit' WHERE comment$task.process_id = '$comment_run_id';";
mysqli_query($con, $query3);
/*
$query2 = "INSERT INTO `blockly$task` (`process_id`, `blockkey`, `xml`, `time`, `action`, `submit`, `similar`) VALUES (NULL, '$blockkey', '$savexml', '$time', '', 'submit', '');";
mysqli_query($con, $query2);
*/
?>
