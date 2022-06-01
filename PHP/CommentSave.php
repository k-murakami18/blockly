<!--　チューターがコメントを付けた作業を記録　-->
<?php

//ajaxのデータを取得
$blockkey = $_POST['blockkey']; //blockkey
$content = $_POST['content']; //コメントを編集した箇所のプロセス数
$xml = $_POST['blockXML'];  //コメントを編集したXML
$task = $_POST['tasknumber']; //課題番号
$student = $_POST['student']; //学習者
$checker = $_POST['checker']; //チューター

//日本の時間を取得
date_default_timezone_set('Asia/Tokyo');
$time = date('Y/m/d H:i:s');

$blockkey = $blockkey.".".$task;

//余計な空白を削除（2行以上空いていたら削除している）
$blank = str_replace(array("  ", "　　"), "", $xml);
//改行を削除する
$savexml = str_replace(array("\r\n", "\r", "\n"), "", $blank);

if(strpos($xml, '</comment>') !== false){
  $comment = 'yes';
}else {
  $comment = '';
}

//MariaDB接続
$con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
//データベース選択
mysqli_select_db($con, 'prepinfo2');
//文字コード指定
mysqli_set_charset($con, "utf8");

// コメントを追加したレコードのprocess_idを取得
$query1 = "SELECT E.* FROM (SELECT * FROM comment$task WHERE blockkey = '$blockkey' ORDER BY process_id ASC LIMIT $content) AS E ORDER BY process_id DESC LIMIT 1;";
$result = mysqli_query($con, $query1);

while ($row = mysqli_fetch_assoc($result)) {
  $process_id = $row['process_id'];
}

$query2 = "UPDATE `comment$task` SET `xml` = '$savexml', `comment`= '$comment', `time` = '$time' WHERE process_id = '$process_id';";
mysqli_query($con, $query2);

/*------------------------   プログラム中のコメントを抜き出して保存   ------------------------*/
// Evaluationテーブルから学習者IDを元に最新のアドバイスのレコードのIDを取得
$query3 = "SELECT * FROM Evaluation$task WHERE studnet='$student' ORDER BY time DESC LIMIT 1;";
$result3 = mysqli_query($con, $query3);
while ($row = mysqli_fetch_assoc($result3)) {
  $eva_id = $row['id'];
}
$eva_id_plus = $eva_id + 1000;

$start = "<comment";
$end = "</comment>";
$end2 = ">";

$stack = array();
$a = 1;
// 特定文字が出てくる回数をカウント
$count = substr_count($savexml, $start);

while ($a <= $count) {
  // <comment がある位置を特定
  $start_position = strpos($savexml, $start);
  //切り出す部分の長さ
  $length = strpos($savexml, $end) - $start_position;
  //切り出し
  $next_xml = substr($savexml, $start_position, $length);

  $start_position2 = strpos($next_xml, $start);
  $end_position2 = strpos($next_xml, $end2) + strlen($end2);
  $length2 = $end_position2 - $start_position2;

  $remove = substr($next_xml, $start_position2, $length2);

  $complete = str_replace($remove, "", $next_xml);
  array_push($stack, $complete);

  // DBに同じコメントがすでに格納されていないかチェック
  $dbsearch = "SELECT COUNT(*) AS cnt FROM inner_comment" . $task . " WHERE process_id='" . $process_id . "' and comment='" . $complete . "' and del_flg=0;";
  $searchcount = mysqli_query($con, $dbsearch);
  while ($row = mysqli_fetch_assoc($searchcount)) {
    $cnt = $row['cnt'];
  }

  // すでにDBにある場合は保存しない
  if($cnt == 0){
    $query4 = "INSERT INTO inner_comment" . $task . " (`id`, `process_id`, `eva_id`, `studnet`, `checker`, `comment`, `del_flg`, `time`) VALUES (NULL, '$process_id', '$eva_id_plus', '$student', '$checker', '$complete', 0, '$time');";
    mysqli_query($con, $query4);
  }

  // 1回目にヒットした箇所を除いたXMLを生成
  $xml2 = strlen($savexml) - strpos($savexml, $end);
  $end_pos = strpos($savexml, $end) + strlen($end);
  $savexml = substr($savexml, $end_pos, $xml2);
  $a++;
}

if ($stack) {
  $whereIds = "AND comment NOT IN ('" . implode("', '", $stack) . "')";
} else {
  $whereIds = "";
}

$sql = "SELECT * FROM inner_comment" . $task . " WHERE studnet='" . $student . "' and process_id='" . $process_id . "' and 1=1 {$whereIds}";
$result10 = mysqli_query($con, $sql);
while ($row = mysqli_fetch_assoc($result10)) {
  $com = $row['comment'];
  $query5 = "UPDATE inner_comment" . $task . " SET del_flg=1 WHERE studnet='" . $student . "' and process_id='" . $process_id . "' AND comment='" . $com . "'";
  mysqli_query($con, $query5);
}

?>
