<?php
include_once __DIR__ . '/includes/connect.php'; 
$agent_id = $_SESSION["agents"]["id"];
if(isset($_POST['operation'])) {
	$validate = new Validation();
	$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	$response = array();
	if ($validate->isValid()) {
		setNotifySuccess('Successfully saved Personal Branding details.');
	    $response['status'] = 'success';
	    echo json_encode($response);
	    exit;
    } else {
    	if(count($validate->getErrors()) > 0){
	        $response['status'] = "errors";   
	        $response['errors'] = $validate->getErrors();   
	    }
    }
    echo json_encode($response);
    exit();
}

$agent_sql = "SELECT cs.brand_icon
    FROM `customer` c
    JOIN customer_settings cs on(cs.customer_id = c.id)
    WHERE c.id=:id";
$agent_where = array(':id' => $agent_id);
$agent_row = $pdo->selectOne($agent_sql, $agent_where);

$contract_business_image = !empty($agent_row["brand_icon"])?$agent_row["brand_icon"]:"";

$get_theme_color = $pdo->selectOne("SELECT theme_color from customer_settings where id = :id",array(":id" => $agent_id));

$tmp_theme_color = isset($get_theme_color['theme_color']) && !empty($get_theme_color['theme_color']) ? $get_theme_color['theme_color'] : "skin-default";

$exStylesheets = array(
	'thirdparty/dropzone/css/basic.css',
	'thirdparty/colorbox/colorbox.css',
);
$exJs = array(
	'thirdparty/dropzone/dropzone.min.js',
	'thirdparty/colorbox/jquery.colorbox.js',
	'thirdparty/vue-js/vue.min.js'
);
$template = 'personal_branding.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>