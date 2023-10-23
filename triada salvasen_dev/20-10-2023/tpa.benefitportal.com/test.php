<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';

$enrollClass = new MemberEnrollment();

// $res = $enrollClass->get_coverage_period(array(246));
// pre_print($res);
$user_id = $_GET['user_id'];
$user_type = $_GET['user_type'];
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;

$agent_tags = get_user_smart_tags($user_id,$user_type,$product_id);

// $params = array();
// $params['fname'] = 'test';
// $params['lname'] = 'testlname';
// $params['group_name'] = 'test_group';

//  $params = array_merge($agent_tags,$params);
pre_print($agent_tags);

?>