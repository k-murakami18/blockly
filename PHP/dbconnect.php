<?php
try {
    $db = new PDO('mysql:dbname=prepinfo2;host=localhost;charset=utf8', 'g231t034', 'Ku9Mm8gL');
} catch (PDOException $e) {
    echo 'DB接続エラー： ' . $e->getMessage();
}
?>
