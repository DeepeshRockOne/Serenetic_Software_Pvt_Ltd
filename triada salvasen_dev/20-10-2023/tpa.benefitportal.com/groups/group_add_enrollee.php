<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Enrollees';
$breadcrumbes[1]['link'] = 'group_enrollees.php';
$breadcrumbes[2]['title'] = '+ Enrollee';
group_has_access(4);

$group_id = $_SESSION['groups']['id'];

$lead_tag_res = get_lead_tags($group_id);

$sponsorRes=$pdo->selectOne("SELECT fname,lname,business_name FROM customer where id=:sponsor_id",array(":sponsor_id"=>$group_id));

$sqlCompany = "SELECT id,name,location from group_company where group_id = :group_id AND is_deleted='N'";
$resCompany = $pdo->select($sqlCompany,array(":group_id"=>$group_id));


$sqlClass = "SELECT id,class_name from group_classes where group_id = :group_id AND is_deleted='N'";
$resClass = $pdo->select($sqlClass,array(":group_id"=>$group_id));

$sqlCoverageCheck = "SELECT id,coverage_period_name,coverage_period_start,coverage_period_end FROM group_coverage_period where group_id=:group_id AND is_deleted='N'";
$resCoverageCheck = $pdo->select($sqlCoverageCheck,array(":group_id"=>$group_id));

$exStylesheets = array(
	'thirdparty/bootstrap-datepicker-master/css/datepicker.css'.$cache,
);

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array(
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js'.$cache,
	'thirdparty/ajax_form/jquery.form.js',
	'thirdparty/price_format/jquery.price_format.2.0.js',
);


$template = 'group_add_enrollee.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>