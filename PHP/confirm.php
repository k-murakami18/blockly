<!-- 課題を提出したかどうかを記録 -->
<?php
//ajaxのデータを取得
$storagekey = $_POST['blockkey'];
$task = $_POST['tasknumber'];
$comment = $_POST['comment'];

//MariaDB接続
$con = mysqli_connect('localhost', 'g231t034', 'Ku9Mm8gL');
//データベース選択
mysqli_select_db($con, 'prepinfo2');
//文字コード指定
mysqli_set_charset($con, "utf8");

//本番用
$query = "SELECT * FROM blockly$task";
$result = mysqli_query($con, $query);

//課題を提出したどうか表示する
$query2 = "UPDATE `user` SET `task$task` = '済', `com$task` = '$comment' WHERE user.storagekey = '$storagekey';";
mysqli_query($con, $query2);
?>
