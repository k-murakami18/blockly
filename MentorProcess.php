<!-- 作業確認ページ -->
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <!--
		<link href="CSS/checkTop.css" rel="stylesheet" type="text/css" media="all" />
		-->
  <title>作業確認用ページ</title>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>

<body>
  <?php
  //URL変数を取得
  $task = $_GET['kadai'];
  $student = $_GET['name'];

  $count = 0;

  //MariaDB接続
  $con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
  //データベース選択
  mysqli_select_db($con, 'prepinfo2');
  //文字コード指定
  mysqli_set_charset($con, "utf8");

  $query = "SELECT * FROM tutor$task";
  $result = mysqli_query($con, $query);

  echo "<ul class='tab clearfix'></ul>";
  echo "<div class=area>";
  echo "</br>";
  echo "<dd>";
  print "<table border = 1>";
  print "<th>チューター</th>";
  print "<th>学習者</th>";
  print "<th>作業内容</th>";
  print "<th>時間</th>";

  while ($row = mysqli_fetch_assoc($result)) {
    if ($row['studnet_name'] == $student) {
      print('<p>');
      print "<tr>";
      print('<td>' . $row['tutor_name'] . '</td>');
      print('<td>' . $row['studnet_name'] . '</td>');
      print('<td>' . $row['task_content'] . '</td>');
      print('<td>' . $row['time'] . '</td>');
      print "</tr>";
      print('</p>');
      $count++;
    }
  }
  print "</table>";
  echo "</dd>";
  echo "</div>";

  echo $count;
  ?>
</body>

</html>
