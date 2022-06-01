<!--評価結果記録用-->
<?php
$check1 = $_POST['check1'];
$check2 = $_POST['check2'];
$check3 = $_POST['check3'];
$check4 = $_POST['check4'];
$check5 = $_POST['check5'];
$advice = $_POST['advice'];
$student = $_POST['sname'];
$checker = $_POST['cname'];
$skey = $_POST['skey'];
$ckey = $_POST['ckey'];
$identifier = $_POST['identifier'];

$kadai = $_POST['kadai'];
$confirmed = $_POST['confirmed'];
$xml = $_POST['blockXML'];

//日本の時間を取得
date_default_timezone_set('Asia/Tokyo');
$time = date('Y/m/d H:i:s');

//余計な空白を削除（2行以上空いていたら削除している）
$blank = str_replace(array("  ", "　　"), "", $xml);
//改行を削除する
$savexml = str_replace(array("\r\n", "\r", "\n"), "", $blank);

//MariaDB接続
$con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
//データベース選択
mysqli_select_db($con, 'prepinfo2');
//文字コード指定
mysqli_set_charset($con, "utf8");

//クエリ
$query = "INSERT INTO `Evaluation$kadai` (`id`, `studnet`, `checker`, `check1`, `check2`, `check3`, `check4`, `check5`, `advice`, `time`) VALUES (NULL, '$student', '$checker', '$check1', '$check2', '$check3', '$check4', '$check5', '$advice', '$time');";
mysqli_query($con, $query);

// Evaluationテーブルから学習者IDを元に最新のアドバイスのレコードのIDを取得
$query3 = "SELECT * FROM Evaluation". $kadai ." WHERE studnet='".$student."' ORDER BY time DESC LIMIT 1;";
$result3 = mysqli_query($con, $query3);
while ($row = mysqli_fetch_assoc($result3)) {
  $eva_id = $row['id'];
}

// 総合コメントが1回目かチェック
$eva_cnt = "SELECT COUNT(*) as cnt FROM Evaluation" . $kadai . " WHERE studnet='" . $student . "'";
$result_eva_cnt = mysqli_query($con, $eva_cnt);
while ($row = mysqli_fetch_assoc($result_eva_cnt)) {
  $cnt = $row['cnt'];
}

if ($cnt <= 1) {
  $eva_id_old = 1000;
} else {
  // Evaluationテーブルから学習者IDを元に最新の1個前のレコードのIDを取得
  $query4 = "SELECT * FROM Evaluation".$kadai." WHERE studnet='".$student."' ORDER BY time DESC LIMIT 2;";
  $result4 = mysqli_query($con, $query4);
  while ($row2 = mysqli_fetch_assoc($result4)) {
    $eva_id_old = $row2['id'];
  }
  $eva_id_old = $eva_id_old + 1000;
}

$query5 = "UPDATE inner_comment".$kadai." SET eva_id=".$eva_id." WHERE eva_id=".$eva_id_old.";";
mysqli_query($con, $query5);

if ($check1 == "ok" && $check2 == "ok" && $check3 == "ok" && $check4 == "ok" && $check5 == "ok") {
  $query2 = "UPDATE user SET task" . $kadai . " = '完', com" . $kadai . " = '" . $advice . "' WHERE storagekey='".$skey."';";
  //$query2 = "UPDATE `user` SET `task1` = '$skey', `com1` = ' ' WHERE user.storagekey = 'ooyymmdd';";
  mysqli_query($con, $query2);
} else {
  // $query2 = "UPDATE user SET task". $kadai ." = '". $eva_id ."', com" . $kadai . " = '" . $eva_id_old . "' WHERE storagekey = '" . $skey . "';";
  $query2 = "UPDATE user SET task" . $kadai . " = '再', com" . $kadai . " = '" . $advice . "' WHERE storagekey = '" . $skey . "';";
  //$query2 = "UPDATE `user` SET `task1` = '$ckey', `com1` = ' ' WHERE user.storagekey = 'ooyymmdd';";
  mysqli_query($con, $query2);
}

echo "チェック終了です。お疲れ様でした。<br>";
echo "<a href = https://ichi-lab.net/~g231t034/blockly/PE2022/TutorTop.php?task=".$kadai."&selection=0&u_id=".$identifier.">課題確認画面に戻る</a></br>";
echo "<a href = https://prep.ipusoft-el.jp/course/view.php?id=20&section=7>入学前教育ページに戻る</a>";
?>
