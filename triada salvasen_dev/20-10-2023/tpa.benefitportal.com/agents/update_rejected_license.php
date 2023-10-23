<?php
include_once __DIR__ . '/layout/start.inc.php';
$agent_id = md5($_SESSION["agents"]["id"]);
$sql = "SELECT al.*
		FROM customer c
		LEFT JOIN agent_license al on(al.agent_id = c.id)
		WHERE md5(c.id)=:agent_id AND al.is_deleted='N' AND al.is_rejected='Y'";
$params = array(":agent_id" => $agent_id);
$license_res = $pdo->select($sql, $params);

$states = $pdo->select("SELECT * FROM states_c WHERE country_id=231");

$rejection_text = '';
$rejection_data = $pdo->selectOne("SELECT license_reject_status,license_reject_text FROM customer_settings WHERE customer_id=:id",array(":id"=>$_SESSION["agents"]["id"]));
if(!empty($rejection_data)) {
    $rejection_text = $rejection_data['license_reject_text'];
}

$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css'
);
$exJs = array(
	"thirdparty/jquery_custom.js",
	'thirdparty/masked_inputs/jquery.maskedinput.min.js',
	'thirdparty/sweetalert2/sweetalert2.js',
	'thirdparty/multiple-select-master/jquery.multiple.select.js'
);

$template = 'update_rejected_license.inc.php';
include_once 'layout/iframe.layout.php';
include_once 'tmpl/' . $template;
?>
