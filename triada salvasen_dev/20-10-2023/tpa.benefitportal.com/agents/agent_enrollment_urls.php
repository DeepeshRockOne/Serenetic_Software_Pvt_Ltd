<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
agent_has_access(3);

$features = array();
$page_title = "Agent Application Urls";
$breadcrumbes[1]['title'] = "Agents";
$breadcrumbes[1]['link'] = "agent_enrollment_urls.php";
$breadcrumbes[2]['title'] = $page_title;
$breadcrumbes[2]['class'] = "Active";
$_GET['iframe']="true";
if (in_array($_SESSION['agents']['agent_coded_level'], array('LOA', 'LOA_NEW'))) {
	setNotifyError("You are not authorised to access this page");
	redirect('dashboard.php',true);
}

$sponsor_id = $_SESSION["agents"]["id"];
$selSponDet = "SELECT CONCAT(c.fname,' ',c.lname) as agentName,c.user_name,cs.agent_coded_id,cs.agent_coded_level,cs.agent_coded_profile
				FROM customer c
				JOIN customer_settings cs ON(c.id=cs.customer_id)
				WHERE c.id=:id";
$sponsor_detail = $pdo->selectOne($selSponDet,array(":id" => $sponsor_id));
$profile_id = checkIsset($sponsor_detail["agent_coded_profile"]);


//for new agent, as per Troy
/** State Director agent level get generic agent level for all agent levels
 ** Individual agent invite for all non-state director agent level will always contract at either LOA or Agent
 ** Individual agent invite for all state director agent level will allow ability to choose agent level
 **/
$incr = "";
// if ($profile_id == 2) {
// 	//chcek agent current coded level to hide show coded on generic URL
// 	if ($sponsor_detail["agent_coded_id"] != 15) {
// //apply only to NON state director
// 		$incr = "AND (id<=:id AND id in(11,12))";
// 	} else {
// 		$incr = "AND id<=:id"; //if state director
// 	}
// } else {
	$incr = " AND id <= :id ";
// }

$coded_sql = "SELECT * FROM agent_coded_level WHERE 1=1 $incr AND is_active='Y' AND profile_id=:profile_id ORDER BY id DESC";
$coded_res = $pdo->select($coded_sql, array(":id" => $sponsor_detail["agent_coded_id"], ":profile_id" => $profile_id));

$validate = new Validation();
$res = array();
$select_type = array();

$errors = $validate->getErrors();

$exStylesheets = array("thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css", 'thirdparty/multiple-select-master/multiple-select.css', '/thirdparty/colorbox/colorbox.css', 'thirdparty/sweetalert/sweetalert.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js', 'thirdparty/jquery_autotab/jquery.autotab-1.1b.js', '/thirdparty/colorbox/jquery.colorbox.js', 'thirdparty/clipboard/clipboard.min.js', 'thirdparty/sweetalert/jquery.sweet-alert.custom.js', 'thirdparty/sweetalert/sweetalert.min.js');

$template = "agent_enrollment_urls.inc.php";
if (checkIsset($_GET['iframe']) == 'true') {
	include_once 'layout/iframe.layout.php';
	include_once 'tmpl/' . $template;
} else {
	include_once 'layout/end.inc.php';
}
?>