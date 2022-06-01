<!-- チューター用のTOPページ -->
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link href="CSS/checkTop.css" rel="stylesheet" type="text/css" media="all" />
  <title>チェック結果確認ページ</title>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>

<body>
  <?php
  $count = 0;
  $task = $_GET['kadai'];

  //MariaDB接続
  $con = mysqli_connect('localhost', 'g231q008', 'bD2TPx9E');
  //データベース選択
  mysqli_select_db($con, 'g231q008');
  //文字コード指定
  mysqli_set_charset($con, "utf8");

  $query = "SELECT * FROM Evaluation" . $task;
  $result = mysqli_query($con, $query);

  echo "<ul class='tab clearfix'></ul>";
  echo "<div class=area>";
  echo "</br>";
  echo "<dd>";
  print "<table border = 1>";

  print "<th>チューター</th>";
  print "<th>生徒</th>";
  print "<th>項目1</th>";
  print "<th>項目2</th>";
  print "<th>項目3</th>";
  print "<th>項目4</th>";
  print "<th>項目5</th>";
  print "<th>アドバイス</th>";
  print "<th>時間</th>";

  while ($row = mysqli_fetch_assoc($result)) {
    print('<p>');
    print "<tr>";
    print('<td>' . $row['checker'] . '</td>');
    print('<td>' . $row['studnet'] . '</td>');
    print('<td>' . $row['check1'] . '</td>');
    print('<td>' . $row['check2'] . '</td>');
    print('<td>' . $row['check3'] . '</td>');
    print('<td>' . $row['check4'] . '</td>');
    print('<td>' . $row['check5'] . '</td>');
    print('<td>' . $row['advice'] . '</td>');
    print('<td>' . $row['time'] . '</td>');
    print "</tr>";
    print('</p>');
    $count++;
  }
  print "</table>";
  echo "</dd>";
  echo "</div>";

  echo $count;
  ?>
</body>

</html>
