<?php
include_once __DIR__ . '/layout/start.inc.php';


$export_val = isset($_GET['export_val']) ? $_GET["export_val"] : '';
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET["is_ajaxed"] : '';
$payment_master_id = isset($_GET['payment_id']) ? $_GET["payment_id"] : '';

if(!empty($export_val) && $is_ajaxed && !empty($payment_master_id)){

	include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';

	if(!empty($payment_master_id)){
		$rincr = " AND md5(p.id) IN('".$payment_master_id."') ";
		$extra_export_arr['processor_incr'] = $rincr;
		$job_id=add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Variant Agent Merchant Assignment(s)","agentMerchantExport",'',array(),$extra_export_arr,'agent_merchant_assignment');
		$reportDownloadURL = $AWS_REPORTING_URL['agent_merchant_assignment']."&job_id=".$job_id;
		echo json_encode(array("status"=>"success","message"=>"Your export request is added","reportDownloadURL" => $reportDownloadURL)); 
		exit;
	}
}

$payment_master_id = $_GET['payment_id'];
$payment_master_res = $pdo->selectOne("SELECT * FROM payment_master WHERE md5(id) = :id", array(":id" => $payment_master_id));
if(!empty($payment_master_res)){

	if($payment_master_res['is_assigned_to_all_product'] == 'N') {
		$products_res = $pdo->selectOne("SELECT GROUP_CONCAT(distinct(product_id)) as assigned_product from payment_master_assigned_product where payment_master_id=:id and is_deleted='N'",array(":id"=>$payment_master_res['id']));
			$product_ids = explode(",", $products_res['assigned_product']);
	}

	if($payment_master_res['is_assigned_to_all_agent'] == 'N') {
		$agents_res = $pdo->select("SELECT * from payment_master_assigned_agent where payment_master_id=:id and is_deleted='N' and status!='Deleted' ",array(":id"=>$payment_master_res['id']));
		$agent_ids =$agent_downline_id_arr=$agents_loa_id_arr = array();//explode(",", $agents_res['assigned_agent']);
		if(!empty($agents_res)){
			foreach($agents_res as $agent){
				array_push($agent_ids,$agent['agent_id']);
				if($agent['include_downline'] == 'Y'){
					array_push($agent_downline_id_arr,$agent['agent_id']);
				}
				if($agent['loa_only'] == 'Y'){
					array_push($agents_loa_id_arr,$agent['agent_id']);
				}
			}
		}
			
	}
}

if(!empty($payment_master_id)){
	$sch_p_param[':id'] = $payment_master_id;
	$incr_p = ' AND md5(payment_master_id)!=:id ';
}
$assigned_products = $pdo->selectOne("SELECT GROUP_CONCAT(product_id) as assigned_product from payment_master_assigned_product where 1 and is_deleted='N' $incr_p ",$sch_p_param);

if(!empty($assigned_products['assigned_product']))
	$assigned_products = array_unique(explode(',',$assigned_products['assigned_product']));


$agent_res = $pdo->select("SELECT id, fname, lname, rep_id FROM customer WHERE is_deleted = 'N' AND type ='Agent'");

$productSql="SELECT p.id,p.name,p.parent_product_id,p.product_code,pc.title as category_name 
			FROM prd_main p
			LEFT JOIN prd_category pc on(pc.id=p.category_id and pc.is_deleted='N')
			WHERE p.type!='Fees' AND p.name != '' AND p.is_deleted='N' AND type!='Kit'  
			ORDER BY p.name ASC";
$productRes=$pdo->select($productSql);

$company_arr = array('No category' => array());
if(!empty($productRes)){
	foreach ($productRes as $key => $value) {
		if($value['category_name']!=''){
			$company_arr[$value['category_name']][]=$value;
		}else{
			$company_arr['No category'][]=$value;
		}
	}
	if(empty($company_arr['No category'])){
		unset($company_arr['No category']);
	}
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,'thirdparty/ajax_form/jquery.form.js');

$template = 'variant_assign_processor_popup.inc.php';
include_once 'layout/iframe.layout.php';
include_once 'tmpl/' . $template;
?>