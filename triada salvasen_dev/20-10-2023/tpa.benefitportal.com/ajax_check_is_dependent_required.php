<?php include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
$MemberEnrollment = new MemberEnrollment();
$response = array();
$product_list = !empty($_POST['product_list'])? explode(",",$_POST['product_list']):array();
$product_list_without_waive = $product_list;
$plan_list = $_POST['product_plan'];
$response['status']='fail';

$display_additional_dependent = false;
$display_additional_dependent_array=array();
$is_spouse = array();
$is_child = array();
$enrollmentLocation = isset($_POST['enrollmentLocation'])?$_POST['enrollmentLocation']:"";

if($enrollmentLocation=='groupSide'){

  $waive_checkbox = !empty($_POST['waive_checkbox']) ? $_POST['waive_checkbox'] : '';
  if(!empty($waive_checkbox)){

    $group_waive_product=isset($_POST['waive_products'])?$_POST['waive_products']:array();

    if(!empty($group_waive_product)){
      $group_product_list=$MemberEnrollment->getGroupWaiveProductList($waive_checkbox,$group_waive_product);
      $product_list = array_merge($product_list,$group_product_list);
    }
  }
}
$enrolleeElementsVal = !empty($_POST['enrolleeElementsVal'])? json_decode($_POST['enrolleeElementsVal'],true):array();
$childCount = array();
if(!empty($enrolleeElementsVal)){
	foreach ($enrolleeElementsVal as $key => $value) {
		
		if (strpos($key, 'child_fname_') !== false) {
		    $tmpKey = str_replace(",","child_fname_", $key);
		    if(!in_array($tmpKey, $childCount)){
		    	array_push($childCount, $tmpKey);
		    }
		}
	}
}
$response['child_count']=count($childCount);
if(!empty($product_list)){
	foreach ($product_list as $key => $row) {
		$plan = $plan_list[$row];
		if($plan!=1){
			if($plan == "3" || $plan == "4" || $plan =="5"){
				array_push($is_spouse, $row);
			}
			if($plan == "2" || $plan == "4" || $plan =="5"){
				array_push($is_child, $row);
			}
			
			$response['status']='success';
		}
	}
}
if(!empty($is_child)){
	if(empty(count($childCount))){
		$response['child_count']=1;
	}
	$response['is_child']=implode(",", $is_child);
}
if(!empty($is_spouse)){
	$response['is_spouse']=implode(",", $is_spouse);
}

$principal_beneficiary_field =$MemberEnrollment->get_principal_beneficiary_field($product_list_without_waive);
$contingent_beneficiary_field =$MemberEnrollment->get_contingent_beneficiary_field($product_list_without_waive);

if(!empty($principal_beneficiary_field)){
	$response['principal_beneficiary'] = true;
	if(isset($principal_beneficiary_field['queBeneficiaryAllow3'])){
		$response["principal_beneficiary_allow_upto_3"] = true;
	}
}
if(!empty($contingent_beneficiary_field)){
	$response['contingent_beneficiary'] = true;
	if(isset($contingent_beneficiary_field['queBeneficiaryAllow3'])){
		$response["contingent_beneficiary_allow_upto_3"] = true;
	}
}




echo json_encode($response);
dbConnectionClose();
exit;
?>