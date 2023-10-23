<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once "../includes/reporting_function.php";
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Dashboard";
$breadcrumbes[1]['class'] = "Active";
//$_SESSION['admin']['access'] = explode(',','2,8,42,53,10,58,12,13,14,20,16,17,26,28,30,31,60,62,64,34,63,36,37,1,9,11,15,25,27,33,46');
//$_SESSION['admin']['access'] = range(1,10);


//$custRow['feature_access'] = ($_SESSION['admin']['feature_access'] !== '' || $_SESSION['admin']['feature_access'] !== NULL) ? $_SESSION['admin']['feature_access'] : $res_acls['feature_access'];


/*-----*/
//$_SESSION['admin']['access'] = range(1,10);
if($_SESSION['admin']['type'] =='Call Center Executives'){
	redirect('call_center.php');
}
if($_SESSION['admin']['type'] == 'Member Services' || $_SESSION['admin']['type'] == 'Support'){
	redirect($HOST.'/admin/member_access.php');
}
$has_access9 = has_menu_access(9);
$has_access21 = has_menu_access(21);
$has_access25 = has_menu_access(25);
$has_access27 = has_menu_access(27);
$has_access33 = has_menu_access(33);
$has_access35 = has_menu_access(35);
$has_access38 = has_menu_access(38);

/* foreach($_SESSION['admin']['access'] as $nacl){
  $has_access{$nacl} = has_menu_access($nacl);
} */
$user_group_menu = has_menu_access(1);
$all_user_menu = has_menu_access(2);
$admin_menu = has_menu_access(3);
$affiliates_menu = has_menu_access(4);
$agent_menu = has_menu_access(5);
$leads_menu = has_menu_access(7);
$member_menu = has_menu_access(8);
$order_menu = has_menu_access(11);
$all_order_menu = has_menu_access(12);
$returns_orders_menu = has_menu_access(13);
$subscriptions_menu = has_menu_access(14);
$product_menu = has_menu_access(15);
$listing_menu = has_menu_access(16);
$place_an_order_menu = has_menu_access(18);
$all_ticket_menu = has_menu_access(26);

$exStylesheets = array('thirdparty/colorbox/colorbox.css','thirdparty/bower_components/morrisjs/morris.css');
$exJs = array(
  'thirdparty/colorbox/jquery.colorbox.js',
  'thirdparty/bower_components/morrisjs/morris.js',
  'thirdparty/bower_components/raphael/raphael-min.js'
);

$page_title = "Dashboard";
$template = 'dashboard.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>