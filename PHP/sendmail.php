<?php
$student = $_POST['student'];
$task = $_POST['tasknumber'];
$to = $_POST['mail'];
// $to = 'g231t034@s.iwate-pu.ac.jp';
$subject = "【入学前教育】課題のチェックを行ってください";
$message = $student . "さんが課題" . $task . "を提出しました。課題チェックページから確認してください。";
// テスト用
// $from = "g031p143@s.iwate-pu.ac.jp";

// 本番用
$from = "soft-prep@ml.iwate-pu.ac.jp";
$header = "From: {$from}\nReply-To: {$from}\nContent-Type: text/plain;";
$header .= "\n";
$header .= "Cc: g231t034@s.iwate-pu.ac.jp";

mb_language("japanese");
mb_internal_encoding("UTF-8");

if (mb_send_mail($to, $subject, $message, $header)) {
	// echo "送信しました";
} else {
	// echo "送信できません";
}
?>
