<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
agent_has_access(3);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Enroll";
$breadcrumbes[2]['title'] = "+ Agent";
$breadcrumbes[2]['link'] = "javascript:void(0);";
$breadcrumbes[2]['class'] = "Active";
$page_title = "+ Agent";

if (in_array($_SESSION['agents']['agent_coded_level'], array('LOA'))) {
	setNotifyError("You are not authorised to access this page");
	redirect('dashboard.php');
}

$summernote = true;

$agentId = $_SESSION["agents"]["id"];
$profile_id = !empty($_GET["profile_id"]) ? $_GET["profile_id"] : 1;

$trigger_id = 17;
$trigger_content = $pdo->selectOne("SELECT email_subject,sms_content,email_content FROM triggers WHERE id=" . $trigger_id);

$sms_content = $trigger_content['sms_content'];

$email_from = get_app_settings('default_email_from'); 
$email_subject = $trigger_content['email_subject'];
$email_content = $trigger_content['email_content'];

$sms_url = $HOST . '/agents/[[agent_id]]';

$lead_id = '';
$fname = '';
$lname = '';
$email = '';
$cell_phone = '';
$state = '';
if (!empty($_GET['lead_id'])) {
    $lead_row = $pdo->selectOne("SELECT l.* FROM leads l WHERE md5(l.id)=:id", array(':id' => $_GET['lead_id']));
    $lead_id = $lead_row['id'];
    $fname = $lead_row['fname'];
    $lname = $lead_row['lname'];
    $email = $lead_row['email'];
    $cell_phone = $lead_row['cell_phone'];
    $state = $lead_row['state'];
}

$exStylesheets = array(
    "thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css",
    'thirdparty/multiple-select-master/multiple-select.css',
    'thirdparty/summernote-master/dist/summernote.css'
);

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array(
    'thirdparty/multiple-select-master/jquery.multiple.select.js',
    'thirdparty/clipboard/clipboard.min.js',
    'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
    'thirdparty/vue-js/vue.min.js',
    'thirdparty/summernote-master/dist/popper.js', 
    'thirdparty/summernote-master/dist/summernote.js'
);

$template = "invite_agent.inc.php";
include_once 'layout/end.inc.php';
?>