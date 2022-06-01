<!-- 作業確認ページ -->
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link href="CSS/checkTop.css" rel="stylesheet" type="text/css" media="all" />
  <title>作業確認用ページ</title>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>

<body>
  <?php
  //URL変数を取得


  //MariaDB接続
  $con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
  //データベース選択
  mysqli_select_db($con, 'prepinfo2');
  //文字コード指定
  mysqli_set_charset($con, "utf8");

  $query = "SELECT * FROM user";
  $result = mysqli_query($con, $query);

  echo "<ul class='tab clearfix'></ul>";
  echo "<div class=area>";
  echo "</br>";
  echo "<dd>";

  print "<table border = 1>";
  print "<th>チューター名</th>";
  print "<th>課題1</th>";
  print "<th>課題2</th>";
  print "<th>課題3</th>";
  print "<th>課題4</th>";

  while ($row = mysqli_fetch_assoc($result)) {
    // if ($row['tutor'] != "yes" && $row['tutor'] != "no") {
      print('<p>');
      print "<tr>";
      print('<td>' . $row['name'] . '</td>');
      print('<td><a href = https://ichi-lab.net/~g231t034/blockly/PE2022/MentorProcess.php?kadai=1&name=' . $row['name'] . '>課題1</a></td>');
      print('<td><a href = https://ichi-lab.net/~g231t034/blockly/PE2018/MentorProcess.php?kadai=2&name=' . $row['name'] . '>課題2</a></td>');
      print('<td><a href = https://ichi-lab.net/~g231t034/blockly/PE2018/MentorProcess.php?kadai=3&name=' . $row['name'] . '>課題3</a></td>');
      print('<td><a href = https://ichi-lab.net/~g231t034/blockly/PE2018/MentorProcess.php?kadai=4&name=' . $row['name'] . '>課題4</a></td>');
      print "</tr>";
      print('</p>');
    // }
  }
  print "</table>";
  echo "</dd>";
  echo "</div>";
  ?>
</body>

</html>
