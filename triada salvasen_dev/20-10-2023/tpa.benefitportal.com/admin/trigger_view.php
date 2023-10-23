<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);

$id = $_GET['id'];

$sql = "SELECT * FROM triggers WHERE id=:id ";
$sql_where = array(
    ':id' => makeSafe($id)
);
$row = $pdo->selectOne($sql, $sql_where);

$category = $row['category_id'];
$title = stripslashes($row['title']);
$type = $row['type'];
$description = stripslashes($row['description']);
$email_subject = stripslashes($row['email_subject']);
$sms_content = stripslashes($row['sms_content']);
$trigger_content = stripslashes($row['email_content']);
$template_id = $row['template_id'] > 0 ? $row['template_id'] : 10;
$email_content = generate_trigger_template($template_id);
$email_content = str_replace("[[msg_content]]",$trigger_content, $email_content);

$template = "trigger_view.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>
