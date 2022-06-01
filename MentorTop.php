<!-- メンター用のTOPページ -->
<?php
//URL変数を取得
$task = $_GET["task"];

// htmlspecialcharsのショートカット
function h($value)
{
  return htmlspecialchars($value, ENT_QUOTES);
}

require('PHP/dbconnect.php');

$users1 = $db->query('SELECT * FROM user');

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>メンター用トップページ</title>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <!-- CSS -->
  <link rel="stylesheet" href="CSS/CheckTop.css" />
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
  <div class="header">課題<?php echo h($task); ?>の評価確認ページ</div>
  <table>
    <thead>
      <tr>
        <th style="width:120px">作成者</th>
        <th style="width:60px">提出状況</th>
        <th>提出時コメント</th>
        <th style="width:60px">総履歴数</th>
        <th style="width:60px">要約後</th>
      </tr>
    </thead>

    <?php while ($row = $users1->fetch(PDO::FETCH_ASSOC)) { ?>
      <?php if ($row['tutor'] != "yes" && $row['tutor'] == "no") { ?>
        <tbody>
          <tr>
            <td><a href="https://ichi-lab.net/~g231t034/blockly/PE2022/Mentor<?php echo $task ?>.php?student=<?php echo $row['name'] ?>&u_id=<?php echo $row['storagekey'] ?>"><?php echo $row['name'] ?></a></td>
            <td><?php echo h($row['task' . $task]); ?></td>
            <td class="comment"><?php echo h($row['com' . $task]); ?></td>
            <td>
              <?php
              $storagekey = $row['storagekey'] . ".1";
              $stepAll = $db->query('SELECT * FROM comment' . $task . ' WHERE blockkey="' . $storagekey . '"');
              $AllCount = $stepAll->rowCount();
              echo $AllCount;
              ?>
            </td>
            <td>
              <?php
              $stepShort = $db->query('SELECT * FROM comment' . $task . ' WHERE blockkey="' . $storagekey . '" AND similar="yes"');
              $ShortCount = $stepShort->rowCount();
              echo $ShortCount;
              ?>
            </td>
          </tr>
        </tbody>
      <?php } ?>
    <?php } ?>
  </table>
  <p></p>

</body>

</html>
