<?php
include_once __DIR__ . '/layout/start.inc.php';
$agent_id = $_GET['id'];
$sql = "SELECT al.*
		FROM customer c
		LEFT JOIN agent_license al on(al.agent_id = c.id)
		WHERE md5(c.id)=:agent_id AND al.is_deleted='N' AND al.new_request='Y'";
$params = array(":agent_id" => $agent_id);
$license_res = $pdo->select($sql, $params);

$states = $pdo->select("SELECT * FROM states_c WHERE country_id=231");

$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css'
);
$exJs = array(
	"thirdparty/jquery_custom.js",
	'thirdparty/masked_inputs/jquery.maskedinput.min.js',
	'thirdparty/sweetalert2/sweetalert2.js',
	'thirdparty/multiple-select-master/jquery.multiple.select.js',
	'thirdparty/ckeditor/ckeditor.js'
);

// $exStylesheets = array('thirdparty/summernote-master/dist/summernote.css');
// $exJs = array(
//   'thirdparty/summernote-master/dist/popper.js',
//   'thirdparty/summernote-master/dist/summernote.js'
// );

$template = 'agent_review_license.inc.php';
include_once 'layout/iframe.layout.php';
include_once 'tmpl/' . $template;
?>
