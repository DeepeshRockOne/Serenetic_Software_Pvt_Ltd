<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ .'/includes/Api.class.php';
include_once __DIR__ .'/includes/apiUrlKey.php';

$ajaxApiCall = new Api();
$body_class ="group-enroll";
$response = [];
$_POST['api_key'] = 'memberEnrollmentPlan';
$_POST['orderId'] = !empty($_GET['orderId']) ? $_GET['orderId'] : "";
$_POST['memberId'] = checkIsset($_GET['memberId']);
$_POST['subscriptionIds'] = checkIsset($_GET['subscription_ids']);
$_POST['fromEnrollment'] = "Y";
$pageUserName = checkIsset($_GET['user_name']);
$pageBuilderLink = !empty($pageUserName) ? $GROUP_ENROLLMENT_WEBSITE_HOST.'/'.$pageUserName : $HOST;
$policyInfo = $ajaxApiCall->ajaxApiCall($_POST,true);

$response = isset($policyInfo['productInfo'][0]['customerId']) && $policyInfo['productInfo'][0]['customerId']!='' ? $policyInfo['productInfo'] : array();

$memberName = !empty($policyInfo['memberName']) ? $policyInfo['memberName'] : '';
$agent_row = !empty($policyInfo['agent_row']) ? $policyInfo['agent_row'] : '';

$template = 'group_member_enrollment_plan.inc.php';
$layout = 'single.layout.php';
include_once 'layout/end.inc.php';
?>
