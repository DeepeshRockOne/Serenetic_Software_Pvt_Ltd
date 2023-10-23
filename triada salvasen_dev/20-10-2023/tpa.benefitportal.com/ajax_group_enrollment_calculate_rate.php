<?php
	include_once __DIR__ . '/includes/connect.php';
	include_once __DIR__ .'/includes/Api.class.php';
	include_once __DIR__ .'/includes/apiUrlKey.php';

	$ajaxApiCall = new Api();
	$apiResponse = "";
	// pre_print($_REQUEST);
	$_POST['userName'] = $_POST['groupId'];
	$_POST['api_key'] = "calculateRateQuestionsDetails";

	$_POST['product'] = checkIsset($_GET['product']);
	$_POST['pricing_model'] = checkIsset($_GET['pricing_model']);
	$_POST['addType'] = checkIsset($_GET['addType']);
	$_POST['submitType'] = checkIsset($_GET['submitType']);
	$_POST['matrix_id'] = checkIsset($_GET['matrix_id']);
	$_POST['accepted'] = checkIsset($_GET['accepted']);
	$electedBundle = checkIsset($_POST['elected_bundle']);
	$_POST['product_matrix'] = !empty($_POST['bundle_product_matrix'][$electedBundle]) ? array_merge_recursive($_POST['bundle_product_matrix'][$electedBundle],checkIsset($_POST['product_matrix'],'arr')) : $_POST['product_matrix'];
	
	$_POST['product_price'] = !empty($_POST['bundle_product_price'][$electedBundle]) ? array_merge_recursive($_POST['bundle_product_price'][$electedBundle],checkIsset($_POST['product_price'],'arr')) : $_POST['product_price'];
	$_POST['display_product_price'] = !empty($_POST['bundle_display_product_price'][$electedBundle]) ? array_merge_recursive($_POST['bundle_display_product_price'][$electedBundle],checkIsset($_POST['display_product_price'],'arr')) : $_POST['display_product_price'];
	$_POST['product_category'] = !empty($_POST['bundle_product_category'][$electedBundle]) ? array_merge_recursive($_POST['bundle_product_category'][$electedBundle],checkIsset($_POST['product_category'],'arr')) : $_POST['product_category'];

	$_POST['product_plan'] = checkIsset($_POST['product_benefit_tier'],'arr');
	$_POST['product_plan'] = empty($_POST['product_plan']) ? checkIsset($_POST['bundle_product_benefit_tier'][$electedBundle]) : $_POST['product_plan'];
	$_POST['coverage_period'] = checkIsset($_POST['coveragePeriod']);
	$_POST['hdn_enrolle_class'] = checkIsset($_POST['hdn_enrolle_class']);
	$_POST['hdn_relationship_to_group'] = checkIsset($_POST['hdn_relationship_to_group']);
	$_POST['relationship_date'] = checkIsset($_POST['relationshipDate']);

	$_POST['is_group_member'] = 'Y';
	$_POST['sponsor_id'] = getname('customer',$_POST['userName'],'id','user_name');
	$prdResponse = $ajaxApiCall->ajaxApiCall($_POST);

	header('Content-type:application/json');
	echo $prdResponse;
	exit();

?>