<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/Api.class.php';


$ajaxApiCall = new Api();
$is_ajaxed = isset($_POST['is_ajaxed']) ? $_POST['is_ajaxed'] : '';
if($is_ajaxed){
	// bundle recomdation list 
	if(isset($_POST['api_key']) && $_POST['api_key'] == 'bundleRecommendationsList'){
		$apiResponse = $ajaxApiCall->ajaxApiCall($_POST,true);
		$fetch_rows = !empty($apiResponse['data']) ? $apiResponse['data']['data']: array();
		$total_rows = count($fetch_rows);
		// pre_print($fetch_rows);
		if($total_rows > 0){
			$paginate = $ajaxApiCall->paginate($apiResponse['data'],'bundle_recommendations_tab.php');
			$paginageLinks = $paginate ['links'];
			$per_page = $paginate ['per_page'];
		}
	}
	include_once 'tmpl/bundle_recommendations_tab.inc.php';
	exit;
	// bundle recomdation list
}
// include_once 'tmpl/bundle_recommendations_tab.inc.php';
$template = 'bundle_recommendations_tab.inc.php';
include_once 'layout/end.inc.php';
?>