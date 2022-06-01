<!-- 履歴を記録 -->
<?php
require('dbconnect.php');

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
$savexml = str_replace(array("\r\n", "\r", "\n"), "", $blank);

$similar = "";
$count = 0;

//MariaDB接続
$con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
//データベース選択
mysqli_select_db($con, 'prepinfo2');
//文字コード指定
mysqli_set_charset($con, "utf8");

$query1 = "SELECT * FROM blockly$task";
$result = mysqli_query($con, $query1);

while ($row = mysqli_fetch_assoc($result)) {
  //学習者の判別とsimilarに最後に数字が入っているidを取得
  if (strpos($row["blockkey"], $blockkey) !== false) {
    if ($row["similar"] == "yes") {
      $id = $row['process_id'];
      $levtext = $row['xml'];
      $count++;
    }
  }
}

// レーベンシュタイン距離で類似度を算出
$sim = levenshtein_normalized_utf8($levtext, $xml);

if ($count == 0) { //もしsimilarに何も入っていなかったら（1回目だったら）
  $similar = "yes";
} else if ($sim < 0.5) { //もしレーベンシュタイン距離で得られた値が0.5以下だったら（類似度が0.5以下）
  $similar = "yes";
} else {
  $similar = "";
}

// blocklyテーブルに挿入
$query2 = "INSERT INTO `blockly$task` (`process_id`, `blockkey`, `xml`, `time`, `action`, `clear`, `submit`, `similar`) VALUES (NULL, '$blockkey', '$savexml', '$time', '', '', '', '$similar');";
mysqli_query($con, $query2);

// commentテーブルに挿入
$query3 = "INSERT INTO `comment$task` (`process_id`, `blockkey`, `xml`, `time`, `action`, `clear`, `submit`, `similar`, `comment`) VALUES (NULL, '$blockkey', '$savexml', '$time', '', '', '', '$similar', '');";
mysqli_query($con, $query3);

// similarの件数の取得
$result4 = $db->prepare("SELECT * FROM blockly" . $task . " WHERE blockkey=? AND similar='yes' ORDER BY time ASC");
$result4->bindValue(1, $blockkey);
$result4->execute();
$cnt = $result4->rowCount();

// similarにyesが入っているレコードのxmlを取得
$data = $result4->fetchAll();
$xml = array_column($data, 'xml');

if ($cnt > 20) {
  $a = 0;
  $compare = 0.8;
  while($cnt > 20){
    $cnter = $cnt - 2;
    while ($a <= $cnter) {
      $s1 = $xml[$a];
      $s2 = $xml[$a + 1];

      // レーベンシュタイン距離で類似度を算出
      $sim = levenshtein_normalized_utf8($s1, $s2);
      if ($sim > $compare) {
        $query = $db->prepare("UPDATE blockly" . $task . " SET similar='' WHERE xml=?");
        $query->bindValue(1, $s2);
        $query->execute();
        $query2 = $db->prepare("UPDATE comment" . $task . " SET similar='' WHERE xml=?");
        $query2->bindValue(1, $s2);
        $query2->execute();
      }
      $a++;
    }
    $simCount = $db->prepare("SELECT * FROM blockly" . $task . " WHERE blockkey=? AND similar='yes' ORDER BY time ASC");
    $simCount->bindValue(1, $blockkey);
    $simCount->execute();
    $cnt = $simCount->rowCount();
    $compare = $compare - 0.01;
  }
}



//以下レーベンシュタイン距離とそれを標準化する計算式
function levenshtein_normalized_utf8($s1, $s2, $cost_ins = 1, $cost_rep = 1, $cost_del = 1)
{
  $l1 = mb_strlen($s1, 'UTF-8');
  $l2 = mb_strlen($s2, 'UTF-8');
  $size = max($l1, $l2);
  if (!$size) {
    return 0;
  }
  if (!$s1) {
    return $l2 / $size;
  }
  if (!$s2) {
    return $l1 / $size;
  }
  return 1.0 - levenshtein_utf8($s1, $s2, $cost_ins, $cost_rep, $cost_del) / $size;
}
function levenshtein_utf8($s1, $s2, $cost_ins = 1, $cost_rep = 1, $cost_del = 1)
{
  $s1 = preg_split('//u', $s1, -1, PREG_SPLIT_NO_EMPTY);
  $s2 = preg_split('//u', $s2, -1, PREG_SPLIT_NO_EMPTY);
  $l1 = count($s1);
  $l2 = count($s2);
  if (!$l1) {
    return $l2 * $cost_ins;
  }
  if (!$l2) {
    return $l1 * $cost_del;
  }
  $p1 = array_fill(0, $l2 + 1, 0);
  $p2 = array_fill(0, $l2 + 1, 0);
  for ($i2 = 0; $i2 <= $l2; ++$i2) {
    $p1[$i2] = $i2 * $cost_ins;
  }
  for ($i1 = 0; $i1 < $l1; ++$i1) {
    $p2[0] = $p1[0] + $cost_ins;
    for ($i2 = 0; $i2 < $l2; ++$i2) {
      $c0 = $p1[$i2] + ($s1[$i1] === $s2[$i2] ? 0 : $cost_rep);
      $c1 = $p1[$i2 + 1] + $cost_del;
      if ($c1 < $c0) {
        $c0 = $c1;
      }
      $c2 = $p2[$i2] + $cost_ins;
      if ($c2 < $c0) {
        $c0 = $c2;
      }
      $p2[$i2 + 1] = $c0;
    }
    $tmp = $p1;
    $p1 = $p2;
    $p2 = $tmp;
  }
  return $p1[$l2];
}
?>
