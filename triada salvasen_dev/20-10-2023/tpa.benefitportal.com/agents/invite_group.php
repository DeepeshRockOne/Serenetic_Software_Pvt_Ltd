<?php
include_once __DIR__ . '/includes/connect.php';
agent_has_access(4);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Enroll";
$breadcrumbes[2]['title'] = "+ Group";
$breadcrumbes[2]['link'] = "javascript:void(0);";
$breadcrumbes[2]['class'] = "Active";
$page_title = "+ Group";


$sqlAgent = "SELECT id, rep_id, email, fname, lname FROM customer WHERE type='Agent' AND status='Active' ";
$rowAgent = $pdo->select($sqlAgent);

$trigger_id = 61;
$trigger_content = $pdo->selectOne("SELECT email_subject,sms_content,email_content FROM triggers WHERE id=".$trigger_id);

$sms_content = $trigger_content['sms_content'];

$email_from = get_app_settings('default_email_from');
$email_subject = $trigger_content['email_subject'];
$email_content = $trigger_content['email_content'];

$summernote = true;

$lead_id = '';
$company_name = '';
$fname = '';
$lname = '';
$email = '';
$cell_phone = '';
$state = '';

if (!empty($_GET['lead_id'])) {
    $lead_row = $pdo->selectOne("SELECT l.* FROM leads l WHERE md5(l.id)=:id", array(':id' => $_GET['lead_id']));
    $lead_id = $lead_row['id'];
    $company_name = $lead_row['company_name'];
    $fname = $lead_row['fname'];
    $lname = $lead_row['lname'];
    $email = $lead_row['email'];
    $cell_phone = $lead_row['cell_phone'];
    $state = $lead_row['state'];
}

$exStylesheets = array('thirdparty/summernote-master/dist/summernote.css',
    'thirdparty/multiple-select-master/multiple-select.css' . $cache,
    "thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css", 'thirdparty/summernote-master/dist/summernote.css' );
$exJs = array('thirdparty/summernote-master/dist/popper.js',
    'thirdparty/summernote-master/dist/summernote.js',
    'thirdparty/multiple-select-master/jquery.multiple.select.js' . $cache,
    'thirdparty/clipboard/clipboard.min.js',
    'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
    'thirdparty/vue-js/vue.min.js',
    'thirdparty/summernote-master/dist/popper.js', 
    'thirdparty/summernote-master/dist/summernote.js'
);

$template = 'invite_group.inc.php';
include_once 'layout/end.inc.php';
?>