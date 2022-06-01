<!-- 課題1評価確認ページ -->
<?php
require('PHP/dbconnect.php');
//URL変数を取得
$u_id = $_GET["u_id"];
$task = $_GET["task"];

//MariaDB接続
$con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
//データベース選択
mysqli_select_db($con, 'prepinfo2');
//文字コード指定
mysqli_set_charset($con, "utf8");

$query1 = "SELECT * FROM user";
$result1 = mysqli_query($con, $query1);

$query2 = "SELECT * FROM user";
$result2 = mysqli_query($con, $query2);

//ログインしたユーザの氏名とグループ番号を表示
while ($row = mysqli_fetch_assoc($result1)) {
  if ($u_id == $row["identifier"] && $row['tutor'] != "yes") {
    $group_no = $row['team'];
    $sname = $row['name'];
    $tutor = $row['tutor'];
    $skey = $row['storagekey'];
    break;
  }
}
$blockkey = $skey . "." . $task;

//学習者に対応するチューター名を取得
while ($row = mysqli_fetch_assoc($result2)) {
  if ($row['tutor'] == "yes" && $group_no == $row['team']) {
    $tname = $row['name'];
    $tkey = $row['storagekey'];
    break;
  }
}

$query3 = "SELECT * FROM Evaluation$task WHERE studnet='$sname' ORDER BY time DESC LIMIT 1";
$result3 = mysqli_query($con, $query3);

while ($row = mysqli_fetch_assoc($result3)) {
  $check1 = $row['check1'];
  $check2 = $row['check2'];
  $check3 = $row['check3'];
  $check4 = $row['check4'];
  $check5 = $row['check5'];
  $advice = $row['advice'];
}

// $query4 = "SELECT * FROM Evaluation" . $task . " WHERE process_id='' ORDER BY time DESC";
$query4 = "SELECT * FROM (SELECT e.*, COUNT(cnt.eva_id) as com_cnt FROM (SELECT * FROM inner_comment" . $task . " WHERE studnet='" . $sname . "') as cnt RIGHT JOIN  Evaluation" . $task . " e ON cnt.eva_id=e.id GROUP BY e.id) as x ORDER BY x.time DESC";
$result4 = mysqli_query($con, $query4);

$com_cnt_query = "SELECT COUNT(*) as cnt FROM inner_comment" . $task . " WHERE studnet='" . $sname . "' and del_flg!=1";
$com_cnt_result = mysqli_query($con, $com_cnt_query);
while ($row = mysqli_fetch_assoc($com_cnt_result)) {
  $com_cnt = $row['cnt'];
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>学習者用評価確認ページ</title>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <!-- CSS -->
  <link href="CSS/CheckTop.css" rel="stylesheet" type="text/css" media="all" />
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <!-- faviconの設定 -->
  <link rel="apple-touch-icon" sizes="180x180" href="IMG/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="IMG/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="IMG/favicon/favicon-16x16.png">
  <link rel="manifest" href="/site.webmanifest">
  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">
</head>

<body>
  <a href="https://prep.ipusoft-el.jp/course/view.php?id=20&section=7" class="details">＜前のページに戻る</a>
  <?php if ($tutor == "no") { ?>
    <div class="header">課題<?php echo $task ?>の評価確認ページ</div>
    <p><u>ユーザ名：<?php echo $sname ?></u>&nbsp;&nbsp;<u>グループ番号：<?php echo $group_no ?></u>&nbsp;&nbsp;<u>チューター名：<?php echo $tname ?></u></p>
    <div class="detail_area">
      <form action="Confirm<?php echo $task; ?>.php" method="POST">
        <input type="hidden" name="identifier" value=<?php echo $u_id; ?>>
        <input type="hidden" name="checker" value=<?php echo $tname; ?>>
        <input type="hidden" name="student" value=<?php echo $sname; ?>>
        <input type="hidden" name="u_id" value=<?php echo $skey; ?>>
        <input type="hidden" name="key" value=<?php echo $tkey; ?>>
        <input type="hidden" name="advice" value=<?php echo $advice; ?>>
        <input type="hidden" name="check1" value=<?php echo $check1; ?>>
        <input type="hidden" name="check2" value=<?php echo $check2; ?>>
        <input type="hidden" name="check3" value=<?php echo $check3; ?>>
        <input type="hidden" name="check4" value=<?php echo $check4; ?>>
        <input type="hidden" name="check5" value=<?php echo $check5; ?>>
        <button type="submit" class="details" style="float:left;">詳細を見る</button>
      </form>
      <p style="font-size:0.8em; margin-left:10px">プログラム中のコメント総数：</p><b style="font-size:1.1em; color:brown"><?php echo $com_cnt; ?></b>
    </div>

    <table style="clear:both;">
      <thead>
        <tr>
          <th width='7%'>項目1</th>
          <th width='7%'>項目2</th>
          <th width='7%'>項目3</th>
          <th width='7%'>項目4</th>
          <th width='7%'>項目5</th>
          <th width='10%'>追加されたコメント数</th>
          <th width='40%'>総合コメント</th>
          <th width='13%'>時間</th>
        </tr>
      </thead>

      <?php while ($row = mysqli_fetch_assoc($result4)) { ?>
        <?php if ($row['studnet'] == $sname) { ?>
          <tbody>
            <tr>
              <td><?php echo $row['check1']; ?></td>
              <td><?php echo $row['check2']; ?></td>
              <td><?php echo $row['check3']; ?></td>
              <td><?php echo $row['check4']; ?></td>
              <td><?php echo $row['check5']; ?></td>
              <td><?php echo $row['com_cnt']; ?></td>
              <td class='comment'><?php echo $row['advice']; ?>
                <!-- <?php
                // $inner_com_cnt = $db->prepare('SELECT * FROM inner_comment' . $task . ' WHERE eva_id=? AND del_flg!=1');
                // $inner_com_cnt->bindValue('1', $row['id']);
                // $inner_com_cnt->execute();
                // $count = $inner_com_cnt->rowCount();

                // if ($count >= 1) {
                //   echo "<details style='color:red; cursor:pointer;'>";
                //   echo "<summary>プログラム中のコメント</summary>";
                //   foreach ($inner_com_cnt as $row2) {
                //     echo "<p style='color:black; margin:0;'>・" . $row2['comment'] . "<br /></p>";
                //   }
                //   echo "</details>";
                // }
                ?> -->
              </td>
              <td><?php echo $row['time']; ?></td>
            </tr>
          </tbody>
        <?php } ?>
      <?php } ?>
    </table>
    <p></p>
    <div class="checklist">
      <p class="checklist_title"><i class="fas fa-check-square"></i> チェックリスト</p>
      <p class="checklist_text">
        <?php if ($task == 1) { ?>
          項目1：和・差・積・商の順番で答えが表示されているか<br>
          項目2：数値は入力できるようになっているか<br>
          項目3：出力された答えは正しいか<br>
          項目4：変数は正しく使えているか<br>
          項目5：必要のない処理は行っていないか<br>
        <?php } else if ($task == 2) { ?>
          項目1：分岐処理が使われているか<br>
          項目2：数値は入力できるようになっているか<br>
          項目3：変数は正しく使えているか<br>
          項目4：正しく動作しているか<br>
          項目5：必要のない処理は行っていないか<br>
        <?php } else if ($task == 3) { ?>
          項目1：反復処理が使われているか<br>
          項目2：複数の星が描けているか<br>
          項目3：1箇所に重複していないか<br>
          項目4：星の形はすべて同じであるか<br>
          項目5：必要のない処理は行っていないか<br>
        <?php } else if ($task == 4) { ?>
          項目1：数値は入力できるようになっているか<br>
          項目2：分岐処理を使っているか<br>
          項目3：反復処理を使っているか<br>
          項目4：変数は正しく使えているか<br>
          項目5：作品は正常に動いているか<br>
        <?php } ?>
      </p>
    </div>
    <p></p>
  <?php } else { ?>
    <h2>このページは学習者専用ページです</h2>
  <?php } ?>
</body>

</html>
