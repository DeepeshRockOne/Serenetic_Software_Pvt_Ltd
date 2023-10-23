<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_GET['trigger_id'];
$type = $_GET['type'];

$trigger_sql = "SELECT * FROM triggers WHERE id=:trigger_id";
$trigger_res = $pdo->selectOne($trigger_sql, array(':trigger_id' => $id));

if ($trigger_res) {
    $category = $trigger_res['category_id'];
    $title = stripslashes($trigger_res['title']);
 
    $description = stripslashes($trigger_res['description']);
    $email_subject = stripslashes($trigger_res['email_subject']);
    $sms_content = stripslashes($trigger_res['sms_content']);
    $trigger_content = stripslashes($trigger_res['email_content']);
    $template_id = $trigger_res['template_id'] > 0 ? $trigger_res['template_id'] : 10;
    $email_content = generate_trigger_template($template_id);
    $email_content = str_replace("[[msg_content]]", $trigger_content, $email_content);
}
$template = 'trigger_detail_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
