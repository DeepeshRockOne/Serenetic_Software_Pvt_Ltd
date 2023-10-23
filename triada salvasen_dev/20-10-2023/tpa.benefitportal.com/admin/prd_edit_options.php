<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(15);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['link'] = 'manage_product.php';
$breadcrumbes[1]['title'] = 'Product List';
$breadcrumbes[2]['title'] = 'Manage Products';
$page_title = "Manage Products";

$export_val = isset($_GET['export_val']) ? $_GET["export_val"] : '';

if(!empty($export_val)){
	$sch_params = array();
	$incr = '';
	$reportType = isset($_GET['reportType']) ? $_GET["reportType"] : '';
	$search_val = isset($_GET['serachVal']) ? $_GET["serachVal"] : '';

	if($search_val != ""){
		if ($reportType == 'company_offering_products') {
			$sch_params[':search_val'] = "%" . makeSafe($search_val) . "%";
			$incr = " AND pc.company_name LIKE :search_val";
		}else if($reportType == 'product_categories' ){
			$sch_params[':search_val'] = "%" . makeSafe($search_val) . "%";
			$incr = " AND pc.title LIKE :search_val";
		}else if($reportType == 'sub_products' ){
			$sch_params[':search_val'] = "%" . makeSafe($search_val) . "%";
			$incr = " AND (sp.product_code LIKE :search_val OR sp.product_name LIKE :search_val OR pc.name LIKE :search_val) ";
		}else if($reportType == 'connected_products' ){
			$sch_params[':search_val'] = "%" . makeSafe($search_val) . "%";
			$incr = " AND (pc.title LIKE :search_val OR p.name LIKE :search_val OR p.product_code LIKE :search_val)";
		}
	}
	
	  
	if(!empty($reportType)){
		include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';

		$job_id=add_export_request_api('EXCEL', $_SESSION['admin']['id'], 'Admin', strtoupper(str_replace('_',' ',$reportType)),'__'.$reportType, $incr, $sch_params);
		$reportDownloadURL = $AWS_REPORTING_URL['__'.$reportType]."&job_id=".$job_id;
	
		$ch = curl_init($reportDownloadURL);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_exec($ch);
		$apiResponse = curl_exec($ch);
		curl_close($ch);
	
		echo json_encode(array("status"=>"success","message"=>"Your export request is added","url"=>$reportDownloadURL)); 
	}else{
		echo json_encode(array("status"=>"fail","message"=>"Something went wrong!")); 
	}
	exit;
}

$managePrdArr=array(
	0 => array('title'=>'COMPANIES OFFERING PRODUCTS' , 'add_name'=>'Company' , 'id'=>'company_offering_products'),
	1 => array('title'=>'PRODUCT CATEGORIES' , 'add_name'=>'Category' , 'id'=>'product_categories'),
	2 => array('title'=>'SUB PRODUCTS' , 'add_name'=>'Sub Product' , 'id'=>'sub_products'),
	3 => array('title'=>'CONNECTED PRODUCTS' , 'add_name'=>'Connect Existing Product(s)' , 'id'=>'connected_products'),
);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'prd_edit_options.inc.php';
include_once 'layout/end.inc.php';
?>