<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "User Groups";
$breadcrumbes[2]['title'] = "Groups";
$breadcrumbes[2]['link'] = 'groups_listing.php';
$breadcrumbes[3]['title'] = "+ Group";
$breadcrumbes[3]['link'] = 'add_groups.php';

$sqlAgent = "SELECT id, rep_id, email, fname, lname FROM customer WHERE type='Agent' AND status='Active' ";
$rowAgent = $pdo->select($sqlAgent);


$trigger_id = 61;
$trigger_content = $pdo->selectOne("SELECT email_subject,sms_content,email_content FROM triggers WHERE id=".$trigger_id);

$sms_content = $trigger_content['sms_content'];

$email_from = get_app_settings('default_email_from');
$email_subject = $trigger_content['email_subject'];
$email_content = $trigger_content['email_content'];

$summernote = true;

$exStylesheets = array(
	"thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css", 
	'thirdparty/multiple-select-master/multiple-select.css', 
);
$exJs = array(
	'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache, 
	'thirdparty/clipboard/clipboard.min.js',
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/ckeditor/ckeditor.js'
);
$template = 'invite_group.inc.php';
include_once 'layout/end.inc.php';
?>