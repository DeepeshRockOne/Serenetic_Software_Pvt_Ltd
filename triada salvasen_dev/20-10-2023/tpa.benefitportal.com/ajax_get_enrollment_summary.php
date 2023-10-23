<?php include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
$MemberEnrollment = new MemberEnrollment();

$response = array();
 
$product_list = !empty($_POST['product_list'])? explode(",",$_POST['product_list']):array();
$product_plan = isset($_POST['product_plan'])?$_POST['product_plan']:array();
$product_price = isset($_POST['product_price'])?$_POST['product_price']:array();
$display_product_price = isset($_POST['display_product_price'])?$_POST['display_product_price']:array();

$sponsor_id = $_POST['sponsor_id'];
$primary_fname = isset($_POST['primary_fname']) ? $_POST['primary_fname'] : '';
$primary_lname = isset($_POST['primary_lname']) ? $_POST['primary_lname'] : '';
$dependent_array = isset($_POST['dependent_array']) ? json_decode($_POST['dependent_array'],true) : array();
$primary_member_name = $primary_fname.' '.$primary_lname;
$customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : 0;
$is_add_product = !empty($_POST['is_add_product']) ? $_POST['is_add_product'] : '';
$enrollmentLocation = isset($_POST['enrollmentLocation'])?$_POST['enrollmentLocation']:"";
$is_group_member = isset($_POST['is_group_member'])?$_POST['is_group_member']:"N";

/*if($enrollmentLocation=='groupSide' || $is_group_member == "Y"){

  $waive_checkbox = !empty($_POST['waive_checkbox']) ? $_POST['waive_checkbox'] : '';
  if(!empty($waive_checkbox)){

    $group_waive_product=isset($_POST['waive_products'])?$_POST['waive_products']:array();

    if(!empty($group_waive_product)){
      $group_product_list=$MemberEnrollment->getGroupWaiveProductList($waive_checkbox,$group_waive_product);
      $product_list = array_merge($product_list,$group_product_list);
    }
  }
}*/

$extra = array();
$extra['is_add_product'] = $is_add_product;
$extra['customer_id'] = $customer_id;
if(!empty($product_list)){
	if($enrollmentLocation=='groupSide' || $is_group_member == "Y"){
		$enrolle_class = isset($_POST['hdn_enrolle_class'])?$_POST['hdn_enrolle_class']:"";
		$coverage_period = isset($_POST['coverage_period'])?$_POST['coverage_period']:"";
		$relationship_to_group = !empty($_POST['hdn_relationship_to_group']) ? $_POST['hdn_relationship_to_group'] : '';
		$relationship_date = !empty($_POST['relationship_date']) ? $_POST['relationship_date'] : '';
		$extra['is_group_member']=$is_group_member;
		$extra['enrollmentLocation']=$enrollmentLocation;
		$extra['enrolle_class']=$enrolle_class;
		$extra['coverage_period']=$coverage_period;
		$extra['relationship_to_group']=$relationship_to_group;
		$extra['relationship_date']=date("Y-m-d",strtotime($relationship_date));
	}
	$summaryList =$MemberEnrollment->get_coverage_period($product_list,0,$extra);
	
	if(!empty($summaryList)){
		foreach ($summaryList as $key => $value) {
			$plan_name = $prdPlanTypeArray[$product_plan[$key]]['title'];
			$product_total = $product_price[$key];
			$display_product_total = $display_product_price[$key];

			$dependent_count = (!empty($dependent_array) && !empty($dependent_array[$key])) ? $dependent_array[$key]['child_dependent'] + $dependent_array[$key]['spouse_dependent'] : 0;

			$summaryList[$key]['primary_member_name'] = $primary_member_name;
			$summaryList[$key]['plan_name'] = $plan_name;
			$summaryList[$key]['product_total'] = $product_total;
			$summaryList[$key]['display_product_total'] = $display_product_total;
			$summaryList[$key]['dependent_count'] = $dependent_count;
		}
	}
	
	
	if(!empty($summaryList)){
		$response['summaryList']=$summaryList;
		$response['status']='success';
	}else{
		$response['status']='fail';
	}
}else{
	$response['status']='fail';
}


echo json_encode($response);
dbConnectionClose();
exit;
?>