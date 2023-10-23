<?php
include_once (__DIR__) . '/includes/connect.php';

$content = "";
$sel = "SELECT id,email_subject,email_content,sms_content FROM triggers order by id asc";
$res = $pdo->select($sel);

foreach ($res as $val) {
  $email_content = $val['email_content'];
  $sms_content = $val['sms_content'];
   
  $content .= "<br><strong>Trigger ID: ".$val['id']."</strong><br>";
  $content .= "------------------------<br>";
  $content .= "<strong>Email Content</strong><br><br>";
  $content .= $email_content."<br><br>";
   
  if($sms_content !=""){
    $content .= "<strong>SMS Content</strong><br><br>";
    $content .= $sms_content."<br>";
  }
}

echo $content;
dbConnectionClose();
exit;
?>
