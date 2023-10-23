<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(5);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Agents";
$breadcrumbes[1]['link'] = "agent_listing.php";
$breadcrumbes[2]['title'] = "New Agent";
$breadcrumbes[2]['class'] = "Active";
$page_title = "New Agent";

$summernote=true;

$profile_id = !empty($_GET["profile_id"]) ? $_GET["profile_id"] : 1;

$trigger_id = 17;
$trigger_content = $pdo->selectOne("SELECT email_subject,sms_content,email_content FROM triggers WHERE id=".$trigger_id);

$sms_content = $trigger_content['sms_content'];

$email_from = get_app_settings('default_email_from'); 
$email_subject = $trigger_content['email_subject'];
$email_content = $trigger_content['email_content'];

$sms_url = $HOST . '/agents/[[agent_id]]';


$default_agent_row = $pdo->select("SELECT c.id, c.rep_id as rep_id, c.email, concat(c.fname,' ', c.lname) as name,
	CASE
    WHEN cs.advance_on = 'Y' THEN 'advance'
    WHEN cs.graded_on = 'Y' THEN 'graded'
    ELSE 'earned'
    END AS commType
FROM customer c
JOIN customer_settings cs on (cs.customer_id=c.id)
WHERE c.type='Agent' AND cs.agent_coded_level!='LOA'");

$feature_level = $pdo->select("SELECT id,feature_access FROM agent_coded_level");
$agent_level_features = array();
if(!empty($feature_level)){
    foreach($feature_level as $agent_level_row){
        $agent_level_features[$agent_level_row['id']] = !empty($agent_level_row['feature_access']) ? explode(',', $agent_level_row['feature_access']) : '';
    }
}
$features_arr = get_agent_feature_access_options();

$exStylesheets = array(
	"thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css", 
	'thirdparty/multiple-select-master/multiple-select.css', 
);

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array(
	'thirdparty/multiple-select-master/jquery.multiple.select.js', 
	'thirdparty/clipboard/clipboard.min.js',
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/ckeditor/ckeditor.js'
);

$template = "invite_agent.inc.php";
include_once 'layout/end.inc.php';
?>