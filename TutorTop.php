<!-- チューター用のTOPページ -->
<?php
// ini_set('display_errors', 1);
require('PHP/dbconnect.php');

//MariaDB接続
$con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
//データベース選択
mysqli_select_db($con, 'prepinfo2');
//文字コード指定
mysqli_set_charset($con, "utf8");

date_default_timezone_set('Asia/Tokyo');
$time = date('Y/m/d H:i:s');

// htmlspecialcharsのショートカット
function h($value)
{
  return htmlspecialchars($value, ENT_QUOTES);
}
// URL変数を取得
$u_id = $_GET["u_id"];
$task = $_GET["task"];
$selection = $_GET["selection"];
$hidden = 0;

$users1 = $db->query('SELECT * FROM user');

// ログインしたユーザの氏名とグループ番号を表示
while ($row = $users1->fetch(PDO::FETCH_ASSOC)) {
  if ($u_id == $row["identifier"] && $row['tutor'] == "yes") {
    $id = $row['user_id'];
    $group_no = $row['team'];
    $name = $row['name'];
    $tutor = $row['tutor'];
    $key = $row['storagekey'];
    $t_id = $row["identifier"];
    break;
  }
}

$users2 = $db->query('SELECT * FROM user');

$query1 = $db->query('SELECT COUNT(*) as cnt FROM (SELECT * FROM Evaluation' . $task . ' WHERE check1="" OR check2="" OR check3="" OR check4="" OR check5="") AS x');
while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
  $row_cnt = $row['cnt'];
}

if (isset($selection)) {
  // if (isset($_REQUEST["position"]) !== true) {
  //   if ($selection == '0') {   // いいね数でソート（デフォルト）
  //     // $query3 = $db->query('SELECT x.*, u1.storagekey AS stdkey, u2.storagekey AS checkkey, COUNT(g.com_id) AS like_cnt FROM( SELECT * FROM( SELECT id, studnet, checker, check1, check2, check3, check4, check5, advice, time FROM Evaluation' . $task .' UNION ALL SELECT id, studnet, checker, "", "", "", "", "", comment, time from inner_comment' . $task .' ) as sub) as x LEFT JOIN user u1 ON x.studnet = u1.name LEFT JOIN user u2 ON x.checker = u2.name LEFT JOIN good' . $task . ' g ON x.id=g.com_id GROUP BY x.id ORDER BY like_cnt DESC');
  //     $query3 = $db->query('SELECT u1.storagekey AS stdkey, u2.storagekey AS checkkey, e.*, COUNT(g.com_id) AS like_cnt FROM (SELECT * FROM Evaluation' . $task . ' WHERE check1="" OR check2="" OR check3="" OR check4="" OR check5="") AS e LEFT JOIN user u1 ON e.studnet = u1.name LEFT JOIN user u2 ON e.checker = u2.name LEFT JOIN good' . $task . ' g ON e.id=g.com_id GROUP BY e.id ORDER BY like_cnt DESC, time DESC LIMIT 10');
  //   } else if ($selection == '1') {  // チューター名でソート
  //     $query3 = $db->query('SELECT u1.storagekey AS stdkey, u2.storagekey AS checkkey, e.*, COUNT(g.com_id) AS like_cnt FROM (SELECT * FROM Evaluation' . $task . ' WHERE check1="" OR check2="" OR check3="" OR check4="" OR check5="") AS e LEFT JOIN user u1 ON e.studnet = u1.name LEFT JOIN user u2 ON e.checker = u2.name LEFT JOIN good' . $task . ' g ON e.id=g.com_id GROUP BY e.id ORDER BY checker ASC, time DESC LIMIT 10');
  //   } else if ($selection == '2') {  // 時間（新しい順）でソート
  //     $query3 = $db->query('SELECT u1.storagekey AS stdkey, u2.storagekey AS checkkey, e.*, COUNT(g.com_id) AS like_cnt FROM (SELECT * FROM Evaluation' . $task . ' WHERE check1="" OR check2="" OR check3="" OR check4="" OR check5="") AS e LEFT JOIN user u1 ON e.studnet = u1.name LEFT JOIN user u2 ON e.checker = u2.name LEFT JOIN good' . $task . ' g ON e.id=g.com_id GROUP BY e.id ORDER BY time DESC LIMIT 10');
  //   } else if ($selection == '3') {  // 時間（古い順）でソート
  //     $query3 = $db->query('SELECT u1.storagekey AS stdkey, u2.storagekey AS checkkey, e.*, COUNT(g.com_id) AS like_cnt FROM (SELECT * FROM Evaluation' . $task . ' WHERE check1="" OR check2="" OR check3="" OR check4="" OR check5="") AS e LEFT JOIN user u1 ON e.studnet = u1.name LEFT JOIN user u2 ON e.checker = u2.name LEFT JOIN good' . $task . ' g ON e.id=g.com_id GROUP BY e.id ORDER BY time ASC LIMIT 10');
  //   }
  // } else if (isset($_REQUEST["position"]) == true || $row_cnt < 10) {
  if ($selection == '0') {   // いいね数でソート（デフォルト）
    $query3 = $db->query('SELECT u1.storagekey AS stdkey, u2.storagekey AS checkkey, e.*, COUNT(g.com_id) AS like_cnt FROM Evaluation' . $task . ' e LEFT JOIN user u1 ON e.studnet = u1.name LEFT JOIN user u2 ON e.checker = u2.name LEFT JOIN good' . $task . ' g ON e.id=g.com_id GROUP BY e.id ORDER BY like_cnt DESC, time DESC');
  } else if ($selection == '1') {  // チューター名でソート
    $query3 = $db->query('SELECT u1.storagekey AS stdkey, u2.storagekey AS checkkey, e.*, COUNT(g.com_id) AS like_cnt FROM Evaluation' . $task . ' e LEFT JOIN user u1 ON e.studnet = u1.name LEFT JOIN user u2 ON e.checker = u2.name LEFT JOIN good' . $task . ' g ON e.id=g.com_id GROUP BY e.id ORDER BY checker ASC, time DESC');
  } else if ($selection == '2') {  // 時間（新しい順）でソート
    $query3 = $db->query('SELECT u1.storagekey AS stdkey, u2.storagekey AS checkkey, e.*, COUNT(g.com_id) AS like_cnt FROM Evaluation' . $task . ' e LEFT JOIN user u1 ON e.studnet = u1.name LEFT JOIN user u2 ON e.checker = u2.name LEFT JOIN good' . $task . ' g ON e.id=g.com_id GROUP BY e.id ORDER BY time DESC');
  } else if ($selection == '3') {  // 時間（古い順）でソート
    $query3 = $db->query('SELECT u1.storagekey AS stdkey, u2.storagekey AS checkkey, e.*, COUNT(g.com_id) AS like_cnt FROM Evaluation' . $task . ' e LEFT JOIN user u1 ON e.studnet = u1.name LEFT JOIN user u2 ON e.checker = u2.name LEFT JOIN good' . $task . ' g ON e.id=g.com_id GROUP BY e.id ORDER BY time ASC');
  }
  //   $hidden = 1;
  // }
}

/** ------------------- いいね機能 ------------------- **/
// いいねが押されたとき
if (isset($_GET['like'])) {
  // いいねを押したメッセージの投稿者を調べる
  $contributor = $db->prepare('SELECT checker FROM Evaluation' . $task . ' WHERE id=?');
  $contributor->execute(array($_GET['like']));
  $pressed_message = $contributor->fetch();

  // いいねを押したユーザとメッセージ投稿者が同一でないとき
  if ($name != $pressed_message['checker']) {
    // 過去にいいね済みか確認
    $pressed = $db->prepare('SELECT COUNT(*) AS cnt FROM good' . $task . ' WHERE com_id=? AND tutor_id=?');
    $pressed->execute(array(
      $_GET['like'],
      $id
    ));
    $my_like_cnt = $pressed->fetch();

    // いいねのデータを挿入 or 削除
    if ($my_like_cnt['cnt'] < 1) {
      $press = $db->prepare('INSERT INTO good' . $task . ' SET com_id=?, tutor_id=?, time=?');
      $press->execute(array(
        $_GET['like'],
        $id,
        $time
      ));
      header("Location: TutorTop.php?task=" . $task . "&selection=" . $selection . "&u_id=" . $u_id);
      exit();
    } else {
      $cancel = $db->prepare('DELETE FROM good' . $task . ' WHERE com_id=? AND tutor_id=?');
      $cancel->execute(array(
        $_GET['like'],
        $id
      ));
      header("Location: TutorTop.php?task=" . $task . "&selection=" . $selection . "&u_id=" . $u_id);
      exit();
    }
  }
}

// ログイン中のユーザがいいねしたメッセージをすべて取得
$like = $db->prepare('SELECT com_id FROM good' . $task . ' WHERE tutor_id=?');
$like->execute(array($id));
while ($like_record = $like->fetch()) {
  $my_like[] = $like_record;
}

// チェックボックスの値を保持するための処理
$selections = filter_input(INPUT_GET, "selection");
$selected["selection"] = ["0" => "", "1" => "", "2" => "", "3" => ""];
$selected["selection"][$selections ?: ""] = " selected";


// ボタンを押したときにその位置に戻る
$position = 0;
if (isset($_REQUEST["position"]) == true) {
  $position = $_REQUEST["position"];
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>チューター用トップページ</title>
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
  <?php if ($tutor == "yes") { ?>
    <div class="header">課題<?php echo h($task); ?>のチェックページ</div>
    <p><u>ユーザ名：<?php echo h($name); ?></u>&nbsp;&nbsp;<u>グループ番号：<?php echo h($group_no); ?></u></p>
    <table>
      <thead>
        <tr>
          <th>作成者</th>
          <th>提出状況</th>
          <th>提出時コメント</th>
        </tr>
      </thead>

      <?php while ($row = $users2->fetch(PDO::FETCH_ASSOC)) { ?>
        <?php if ($row['tutor'] != "yes" && $group_no == $row['team']) { ?>
          <tbody>
            <tr>
              <td><a href=https://ichi-lab.net/~g231t034/blockly/PE2022/Check<?php echo $task ?>.php?checker=<?php echo $name ?>&student=<?php echo $row['name'] ?>&u_id=<?php echo $row['storagekey'] ?>&key=<?php echo $key ?>&identifier=<?php echo $u_id ?>&selection=<?php echo $selection ?>><?php echo $row['name'] ?></a></td>
              <!-- <td>
                <form method="POST" name="form1" id="form1" action="data/post" action="Check<?php echo $task ?>.php">
                <input type="hidden" name="checker" value="<?php echo $name ?>">
                <input type="hidden" name="student" value="<?php echo $row['name'] ?>">
                <input type="hidden" name="u_id" value="<?php echo $row['storagekey'] ?>">
                <input type="hidden" name="key" value="<?php echo $key ?>">
                <input type="hidden" name="identifier" value="<?php echo $u_id ?>">
                <input type="hidden" name="selection" value="<?php echo $selection ?>">
                <a href="javascript:form1.submit()"><?php echo $row['name'] ?></a>
                </form>
              </td> -->
              <td><?php echo h($row['task' . $task]); ?></td>
              <td class="comment"><?php echo h($row['com' . $task]); ?></td>
            </tr>
          </tbody>
        <?php } ?>
      <?php } ?>
    </table>
    <hr>


    <div class='other_tutor_comment'>
      <h2 class='aaa' style='margin: 0 20px 0 5px; float: left'><i class="fas fa-comments" style="margin-right:15px"></i>他チューターの過去のコメント</h2>
      <form class='aaa' action='tutorTop.php?task=<?php echo h($task); ?>&u_id=<?php echo h($u_id); ?>' method='GET'>
        <select name="selection" class="sort-select pad" style="margin-right:3px">
          <option value="0" <?= $selected["selection"][0]; ?>>参考になった</option>
          <option value="1" <?= $selected["selection"][1]; ?>>チューター名</option>
          <option value="2" <?= $selected["selection"][2]; ?>>時間（新しい順）</option>
          <option value="3" <?= $selected["selection"][3]; ?>>時間（古い順）</option>
        </select>
        <input type="hidden" name="task" value="<?php echo h($task); ?>">
        <input type="hidden" name="u_id" value="<?php echo h($u_id); ?>">
        <button type='submit' class="sort"><i class="fas fa-sort"></i>&nbsp;並び替え</button>
      </form>
    </div>
    <p class="description" style="clear:both">
      他のチューターが学習者へ書いたコメントや、去年のチューターのコメントで良かったものをピックアップして表示しています。<br>
      自分がコメントをする際の参考にしてください。また、<b style="color:red">自分がコメントする際に参考にしたコメントには「参考になった」ボタンを押してください。</b>
    </p>

    <table class="list">
      <thead>
        <tr>
          <th width='12%'>チューター名</th>
          <th width='70%'>コメント</th>
          <th width='6%'>参考になった</th>
          <th width='12%'></th>
        </tr>
      </thead>

      <?php foreach ($query3 as $row) : ?>
        <?php if (($row['check1'] != "ok" || $row['check2'] != "ok" || $row['check3'] != "ok" || $row['check4'] != "ok" || $row['check5'] != "ok")) { ?>
          <tbody class="list-item">
            <tr>
              <td> <?php echo h($row['checker']) ?></td>
              <td class="comment"> <?php echo nl2br($row['advice']); ?>
                <?php
                $inner_com_cnt = $db->prepare('SELECT * FROM inner_comment' . $task . ' WHERE eva_id=?');
                $inner_com_cnt->bindValue('1', $row['id']);
                $inner_com_cnt->execute();
                $count = $inner_com_cnt->rowCount();

                if ($count >= 1) {
                  echo "<details style='color:red; cursor:pointer;'>";
                  echo "<summary>プログラム中のコメント</summary>";
                  foreach ($inner_com_cnt as $row2) {
                    echo "<p style='color:black; margin:0;'>・" . h($row2['comment']) . "<br /></p>";
                  }
                  echo "</details>";
                }
                ?>
              </td>
              <td>
                <!-- いいねボタン -->
                <?php
                $my_like_cnt = 0;
                if (!empty($my_like)) {
                  foreach ($my_like as $like_post) {
                    foreach ($like_post as $like_post_id) {
                      if ($like_post_id == $row['id']) {
                        $my_like_cnt = 1;
                      }
                    }
                  }
                }
                ?>
                <?php if ($my_like_cnt < 1) : ?>
                  <form class="heart" name="goodbtn" method="GET" action="">
                    <input name="position" type="hidden" value="0">
                    <input type="hidden" name="task" value="<?php echo h($task); ?>">
                    <input type="hidden" name="u_id" value="<?php echo h($u_id); ?>">
                    <input type="hidden" name="selection" value="<?php echo h($selection); ?>">
                    <input type="hidden" name="like" value="<?php echo h($row['id']); ?>">
                    <button type="submit" class="heart" onclick="location.href='tutorTop.php?task=<?php echo h($task); ?>&selection=<?php echo h($selection); ?>&u_id=<?php echo h($u_id); ?>'"><i class="far fa-thumbs-up fa-lg"></i></i></button>
                  </form>
                <?php else : ?>
                  <form class="heart" name="goodbtn" method="GET" action="">
                    <input name="position" type="hidden" value="0">
                    <input type="hidden" name="task" value="<?php echo h($task); ?>">
                    <input type="hidden" name="u_id" value="<?php echo h($u_id); ?>">
                    <input type="hidden" name="selection" value="<?php echo h($selection); ?>">
                    <input type="hidden" name="like" value="<?php echo h($row['id']); ?>">
                    <button type="submit" class="heart" onclick="location.href='tutorTop.php?task=<?php echo h($task); ?>&selection=<?php echo h($selection); ?>&u_id=<?php echo h($u_id); ?>'"><i class="fas fa-thumbs-up fa-lg"></i></i></button>
                  </form>
                <?php endif; ?>
                <span><?php echo h($row['like_cnt']); ?></span>
              </td>

              <td>
                <?php
                $latest_eva = "SELECT * FROM (SELECT * FROM Evaluation" . $task . " WHERE check1!='ok' or check2!='ok' or check3!='ok' or check4!='ok' or check5!='ok') as x where studnet='" . $row["studnet"] . "' ORDER BY time DESC LIMIT 1";
                $result_latest_eva = mysqli_query($con, $latest_eva);
                while ($row3 = mysqli_fetch_assoc($result_latest_eva)) {
                  $latest_eva_id = $row3['id'];
                  $student_empty = $row3['studnet'];
                }
                if ($student_empty != "" && $latest_eva_id == $row['id']) {
                  echo '<form action="Share' . h($task) . '.php" method="POST" target="_blank" class="flex">';
                  echo '<input type="hidden" name="id" value="' . h($row["id"]) . '">';
                  echo '<input type="hidden" name="checker" value="' . h($row["checker"]) . '">';
                  echo '<input type="hidden" name="studnet" value="' . h($row["studnet"]) . '">';
                  echo '<input type="hidden" name="u_id" value="' . h($row["stdkey"]) . '">';
                  echo '<input type="hidden" name="key" value="' . h($row["checkkey"]) . '">';
                  echo '<input type="hidden" name="advice" value="' . h($row["advice"]) . '">';
                  echo '<input type="hidden" name="check1" value="' . h($row["check1"]) . '">';
                  echo '<input type="hidden" name="check2" value="' . h($row["check2"]) . '">';
                  echo '<input type="hidden" name="check3" value="' . h($row["check3"]) . '">';
                  echo '<input type="hidden" name="check4" value="' . h($row["check4"]) . '">';
                  echo '<input type="hidden" name="check5" value="' . h($row["check5"]) . '">';
                  echo '<button type="submit" class="details" style="float: left;">詳細を見る</button>';
                  echo '</form>';
                  // echo $row['id'];
                }
                ?>
              </td>
            </tr>
          </tbody>
        <?php } ?>
      <?php endforeach; ?>
    </table>
    <!-- <?php if ($row_cnt > 10 && $hidden == 0) { ?>
      <form id="form" action="" method="post" enctype="application/x-www-form-urlencoded">
        <input name="position" type="hidden" value="0">
        <input type="hidden" name="selection" value="<?php echo h($selection); ?>">
        <input name="send" type="button" value="全部見る">
      </form>
    <?php } ?> -->
    <p></p>
    <div class="list-btn">
      <button class="more_btn">もっと見る</button>
    </div>
    <p></p>
  <?php } else { ?>
    <h2>このページはチューター専用ページです</h2>
  <?php } ?>

  <script>
    // // 全部見るボタンを押したときにその位置から表示する処理
    // $(document).ready(function() {
    //   window.onload = function() {
    //     $(window).scrollTop(<?php echo $position; ?>);
    //   }
    //   $("input[type=button]").click(function() {
    //     var position = $(window).scrollTop();
    //     $("input:hidden[name=position]").val(position);
    //     $("#form").submit();
    //   });
    // });

    /* 他チューターのコメントで最初に表示しておく件数 */
    var moreNum = 10;

    /* 表示するリストの数以降を隠しておく */
    $('.list-item:nth-child(n + ' + (moreNum + 2) + ')').addClass('is-hidden');

    /* 「もっと見る」ボタンをクリックしたら件数ごとに表示し，全てのリストを表示したら「もっと見る」ボタンをフェードアウトする */
    $('.list-btn').on('click', function() {
      $('.list-item.is-hidden').slice(0, moreNum).removeClass('is-hidden');
      if ($('.list-item.is-hidden').length == 0) {
        $('.list-btn').fadeOut();
      }
    });

    /* リストの数が表示するリストの数以下だった場合「もっとみる」ボタンを非表示にする */
    $(function() {
      var list = $(".list tbody").length;
      if (list < moreNum) {
        $('.list-btn').addClass('is-btn-hidden');
      }
    });
  </script>
</body>

</html>
