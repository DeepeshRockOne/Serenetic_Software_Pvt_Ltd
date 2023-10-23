<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/Api.class.php';
include_once __DIR__ . '/includes/apiUrlKey.php';

$ajaxApiCall = new Api();

$response = array();
$childData = isset($_POST['child']) ? $_POST['child'] : array();
$product_list = isset($_POST['child_products_list'])? explode(",", $_POST['child_products_list']):array();

$child_doc = !empty($_POST['coverage_child_verification_doc']) ? $_POST['coverage_child_verification_doc'] : array();
$childFileArr = [];
if(!empty($child_doc)){
	foreach($child_doc as $key => $child){
		$tmp = explode('\\',$child);
		$childFileArr[$key]	= end($tmp);
	}
}
$product_plan = array();
if(!empty($product_list)){
	foreach($product_list as $key => $value){
		$product_plan[$value] = 4;
	}
}

$enrolleeElementsVal = !empty($_POST['enrolleeElementsVal'])? json_decode($_POST['enrolleeElementsVal'],true):array();

$dependent_field_number = isset($_POST['dependent_field_number'])? explode("_", $_POST['dependent_field_number']):array();

$number = $dependent_field_number[0];
$display_number = $dependent_field_number[1];

$response['number']=$number;
$enrollmentLocation = "groupSide";

$is_group_member = 'Y';
$is_add_product = 0;

$cd_profile_id = isset($_POST['cd_profile_id']) ? $_POST['cd_profile_id'] : 0;

$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;

$child_dep_row = array();

$customer_rep_id = isset($_POST['groupId']) ? $_POST['groupId'] : "";
$member_rep_id = isset($_POST['memberId']) ? $_POST['memberId'] : "";
$primary_lname = isset($_POST['primary_lname']) ? $_POST['primary_lname'] : "";
$child_zip_value = isset($_POST['primaryZipcode'])? $_POST['primaryZipcode']:"";

$child_assign_products = array();
$spouse_assign_products = array();

$postArray = array(
	'productList' => $product_list,
	'productPlan' => $product_plan,
	'number' => $number,
	'displayNumber' => $display_number,
	'enrollmentLocation' => $enrollmentLocation,
	'isGroupMember' => $is_group_member,
	'cdProfileId' => $cd_profile_id,
	'orderId' => $order_id,
	'groupId' => $customer_rep_id,
	'memberId' => $member_rep_id,
	'childZipValue' => $child_zip_value,
	'isAddProduct' => $is_add_product,
	'spouseAssignProducts' => array($product_list),
	'childAssignProducts' => array($product_list),
	'api_key' => 'getChildField'
);

$response = $ajaxApiCall->ajaxApiCall($postArray,true);

$productRes = $response['productRes'];
$memberPlusOneProduct = $response['memberPlusOneProduct'];
$child_field = $response['childField'];
$stateRes = $response['stateRes'];
$child_dep_row = $response['childDepRow'];

if(!empty($enrolleeElementsVal['child_fname_'.$display_number])){
	$child_fname_value = $enrolleeElementsVal['child_fname_'.$display_number];
} else {
	$child_fname_value = isset($child_fname_value)?$child_fname_value:'';
}

if(!empty($enrolleeElementsVal['child_email_'.$display_number])) {
	$child_email_value = $enrolleeElementsVal['child_email_'.$display_number];
} else {
	$child_email_value = isset($child_email_value)?$child_email_value:'';
}

if(!empty($enrolleeElementsVal['child_birthdate_'.$display_number])) {
	$child_birthdate_value = $enrolleeElementsVal['child_birthdate_'.$display_number];
} else {
	$child_birthdate_value = isset($child_birthdate_value)?$child_birthdate_value:'';
}

if(!empty($enrolleeElementsVal['child_gender_'.$display_number])) {
	$child_gender_value = $enrolleeElementsVal['child_gender_'.$display_number];
} else {
	$child_gender_value = isset($child_gender_value)?$child_gender_value:'';
}

$child_zip_value = isset($_POST['primary_zip'])? $_POST['primary_zip']:"";
$child_state_value = isset($_POST['primary_state'])? $_POST['primary_state']:"";

$data = "<div id='inner_child_field_".$number."' class='inner_child_field m-t-20'>";
	$data .= "<hr>";
	$data .= "<p class='font-bold'>Child <span class='font-bold display_number' data-display_number='".$display_number."' data-id='".$number."' id='dependent_number_".$number."'>".$display_number."</span> <a href='javascript:void(0);' class='btn red-link removeChildField' data-id='".$number."' id='removeChildField".$number."' data-toggle='tooltip' data-trigger='hover' data-container='body' title='Remove' data-placement='bottom'>Remove</a></p>";
	$data .= "<div class='row enrollment_auto_row'>";
	  if(!empty($child_dep_row)){
	  	$data .= "<div class='col-sm-4'>";
	  	  $data .= "<div class='form-group'>";
	  	  	$cp_id = !empty($_GET['cd_profile_id'])?$_GET['cd_profile_id']:'0';
	  	  	$data .= "<input type='hidden' name='child_cd_profile_id[".$number."]' id='child_cd_profile_id_".$number."' value='".$cp_id."'>";
	  	  	$data .= "<select class='form-control existing_child_dependent child_select_".$number."' name='existing_child_dependent' id='existing_child_dependent_".$number."' data-id='".$number."'>";
	  	  	  $data .= "<option data-hiddent='true' value=''>Existing Child</option>";
	  	  	  foreach($child_dep_row as $s){
	  	  	  	$data .= "<option value='".$s['id']."' 
		  	  	  	data-fname='".$s['fname']."' 
		  	  	  	data-lname='".$s['lname']."' 
		  	  	  	data-email='".$s['email']."' 
		  	  	  	data-gender='".$s['gender']."' 
		  	  	  	data-birth_date='".date("m/d/Y",strtotime($s["birth_date"]))."' 
		  	  	  	data-ssn='".$s['ssn']."'>".$s['fname']." ".$s['lname']."</option>";
	  	  	  }
	  	  	$data .= "</select>";
	  	  $data .= "</div>";
	  	$data .= "</div>";
	  }
	  $data .= "<div class='col-sm-4'>";
		$data .= "<div class='form-group'>";
		  $data .= "<select id='child_assign_products_".$number."' name='child_assign_products[".$number."][]' class='se_multiple_select child_dependent_multiple_select' multiple='multiple' data-id=".$number." >";
			if(!empty($productRes)){
			  foreach($productRes as $key => $productRow){
			  	$select = (!isset($dep_row['product_ids']) && !empty($product_plan) && $product_plan[$productRow['id']] != 5) || (!empty($dep_row['product_ids']) && in_array($productRow['id'],$dep_row['product_ids'])) ? 'selected' : '';
			  	$disable = !empty($memberPlusOneProduct) && in_array($productRow['id'], $memberPlusOneProduct) ? 'disabled' : '';
				$data .= "<option value='".$productRow['id']."' data-product-plan='".$product_plan[$productRow['id']]."' ".$select." ".$disable.">".$productRow['name']." (".$productRow['product_code'] .")</option>";
			  }
			}
		  $data .= "</select>";
		  $data .= "<label>Assign Product(s)*</label>";
		  $data .= "<p class='error' id='error_child_assign_products_".$number."'></p>";
	  	$data .= "</div>";	  	
	  $data .= "</div>";
	if(array_key_exists('fname',$child_field)){
	  $required = $child_field['fname']['required'] == 'Y' ? "<span class='req-indicator'>*</span>" : "";
	  $data .= "<div class='col-sm-4'>";
	    $data .= "<div class='form-group'>";
	      $data .= "<input type='input' class='form-control child_fname_".$number."' name='child_fname[".$number."]' id='child_fname_".$number."' data-id='".$number."' value='".$child_fname_value."'>";
	      $data .= "<label>Child First Name ".$required."</label>";
	      $data .= "<p class='error' id='error_child_fname_".$number."'></p>";
	    $data .= "</div>";
	  $data .= "</div>";
	  unset($child_field['fname']);
	}
	if(array_key_exists('lname', $child_field)) {
	  $required = $child_field['lname']['required'] == 'Y' ? "<span class='req-indicator'>*</span>" : "";
	  $data .= "<div class='col-sm-4'>";
	    $data .= "<div class='form-group'>";
	      $data .= "<input type='input' class='form-control child_last_name child_lname_".$number."' name='child_lname[".$number."]' value='$primary_lname' id='child_lname_".$number."' data-id='".$number."'>";
	      $data .= "<label>Child Last Name ".$required."</label>";
	      $data .= "<p class='error' id='error_child_lname_".$number."'></p>";
	    $data .= "</div>";
	  $data .= "</div>";
	  unset($child_field['lname']);
	}
	if(array_key_exists('email', $child_field)) {
	  $required = $child_field['email']['required'] == 'Y' ? "<span class='req-indicator'>*</span>" : "";
	  $data .= "<div class='col-sm-4'>";
	    $data .= "<div class='form-group'>";
	      $data .= "<input type='input' class='form-control no_space child_email_".$number."' name='child_email[".$number."]' id='child_email_".$number."' data-id='".$number."' value='".$child_email_value."'>";
	      $data .= "<label>Email".$required."</label>";
	      $data .= "<p class='error' id='error_child_email_".$number."'></p>";
	    $data .= "</div>";
	  $data .= "</div>";
	  unset($child_field['email']);
	}
	if(array_key_exists('SSN', $child_field)) {
		$data .= "<div class='col-sm-4'>";
			$data .= "<div class='form-group'>";
				$data .= "<input type='text' class='form-control SSN_mask' name='child_SSN[".$number."]' id='child_SSN_".$number."' data-element='ssn'>";
				$data .= "<label>SSN*</label>";
				$data .= "<p class='error' id='error_child_SSN_".$number."'></p>";
				unset($child_field['SSN']);
			$data .= "</div>";
		$data .= "</div>";
	}
	if(array_key_exists('birthdate', $child_field)) {
		$is_readonly = !empty($spouse_birthdate_value) ? "readonly" : "";
		$data .= "<div class='col-sm-4'>";
			$data .= "<div class='form-group'>";
				$data .= "<div class='input-group'>";
					$data .="<div class='input-group-addon'><i class='fa fa-calendar'></i></div>";
					$data .= "<div class='pr'>";
					$data .= "<input type='text' class='form-control dateClass date_picker child_birthdate_".$number." dob' data-id='".$number."' id='child_birthdate_".$number."' name='child_birthdate[".$number."]' data-element='birthdate' value='".$child_birthdate_value."' ".$is_readonly.">";
					$data .= "<label>DOB (MM/DD/YYYY)*</label>";
					$data .= "</div>";
				$data .= "</div>";
				$data .= "<p class='error' id='error_child_birthdate_".$number."'></p>";
			$data .= "</div>";
		$data .= "</div>";
		unset($child_field['birthdate']);
	}
	if(array_key_exists('gender', $child_field)) {
		$child_gender_readonly = !empty($child_gender_value) ? 'readonly' : '';
		$child_gender_disabled = !empty($child_gender_value) ? 'disabled' : '';
	  $data .= "<div class='col-sm-4'>";
	  	$data .= "<div class='form-group'>";
	  	  $data .= "<div class='btn-group btn-custom-group btn-group-justified ".(!empty($child_gender_value) ? "btn-group-disabled" : "" )."'>";
		  	$child_gender_class = !empty($child_gender_value) && $child_gender_value=="Male" ? 'active' : '';
			$child_gender_checked = !empty($child_gender_value) && $child_gender_value=="Male" ? 'checked' : '';
	  	  	$data .= "<div class='toggle-item'>";
	  	  	  $data .= "<input class='js-switch child_gender' name='child_gender[".$number."]' type='radio' value='Male' data-id='".$number."' id='child_gender_".$number."_Male' ".$child_gender_checked." ".$child_gender_readonly."/>";
	  	  	  $data .= "<label for='child_gender_".$number."_Male' class='btn btn-info ".$child_gender_class."' ".$child_gender_disabled.">Male</label>";
	  	  	$data .= "</div>";
	  	  	$child_gender_class = !empty($child_gender_value) && $child_gender_value=="Female" ? 'active' : '';
			$child_gender_checked = !empty($child_gender_value) && $child_gender_value=="Female" ? 'checked' : '';
	  	  	$data .= "<div class='toggle-item'>";
	  	  	  $data .= "<input class='js-switch child_gender' name='child_gender[".$number."]' type='radio' value='Female' data-id='".$number."' id='child_gender_".$number."_Female' ".$child_gender_checked." ".$child_gender_readonly."/>";
	  	  	  $data .= "<label for='child_gender_".$number."_Female' class='btn btn-info ".$child_gender_class."' ".$child_gender_disabled.">Female</label>";
	  	  	$data .= "</div>";
	  	  	$data .= "<input type='hidden' name='child_gender[".$number."]' id='hidden_child_gender_".$number."' value='".$child_gender_value."'>";
	  	  $data .= "</div>";
	  	  unset($child_field['gender']);
	  	  $data .= "<p class='error' id='error_child_gender_".$number."'></p>";
	  	$data .= "</div>";
	  $data .= "</div>";
	}
	$data .= "</div>";

	if(!empty($child_field)){
	  	$child_benefit_arr = array('child_benefit_amount','child_in_patient_benefit','child_out_patient_benefit','child_monthly_income','child_benefit_percentage');
	  	$data .= "<h5 class='m-t-15'>Additional Child Information</h5>";
	  	$data .= "<div class='row enrollment_auto_row'>";
	  	  foreach($child_field as $key => $row){
	  	  	$prd_question_id = $row['id'];
	  	  	$is_required= $row['required'];
			$control_name = "child_".$row['label'];
			$label = $row['display_label'];
			$control_type = $row['control_type'];
			$class = $row['control_class'];
			$maxlength = $row['control_maxlength'];
			$control_attribute = $row['control_attribute'];
			$questionType = $row['questionType'];

			if(in_array($row['label'],array('fname','lname','SSN','email','birthdate','gender'))){
            	continue;
          	}

          	$control_value = isset($enrolleeElementsVal[$control_name."_".$display_number])?$enrolleeElementsVal[$control_name."_".$display_number]:"";

          	if(empty($control_value) && !empty(${$control_name.'_value'})){
          		$control_value = ${$control_name.'_value'};
          	}

          	if($control_name == "spouse_state"){
          		pre_print($control_value);
          	}

          	if($questionType=="Default"){
          		if($control_type=='text' && !in_array($control_name,$child_benefit_arr)){
          			$is_readonly = !empty($control_value) ? "readonly" : "";
          			$required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : "";
          			$data .= "<div class='col-sm-4'>";
                      $data .= "<div class='form-group'>";
                        $data .=" <input type='text' maxlength='".$maxlength."' class='form-control ".$class."'  required name='".$control_name."[".$number."]' id='".$control_name."_".$number."' value='".$control_value."' ".$is_readonly.">";
                        $data .= "<label>".$label.$required."</label>"; 
                        $data .= "<p class='error' id='error_".$control_name."_".$number."'></p>";
                      $data .= "</div>";
                    $data .= "</div>";
          		}elseif($control_type=='date_mask' && !in_array($control_name,$child_benefit_arr)){
          			$dateValue = '';
          			if($dateValue != ''){
          				$dateValue = date('m/d/Y', strtotime($control_value));
          			}

          			$required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : "";
          			$data .= "<div class='col-sm-4'>";
                      $data .= "<div class='form-group'>";
					  $data .= "<div class='input-group'>";
                      	$data .= "<div class='input-group-addon'><i class='fa fa-calendar'></i></div>";
                      	  $data .= "<div class='pr'>";
                      	  	$data .= "<input type='text' class='form-control date_picker dateClass ".$class."' name='".$control_name."[".$number."]' id='".$control_name."_".$number."' value='".$dateValue."'>";
                      	  	$data .= "<label>".$label.$required."</label>";
                      	  $data .= "</div>";
                      	$data .= "</div>";
                      	$data .= "<p class='error' id='error_".$control_name."_".$number."'></p>";
                      $data .= "</div>";
                    $data .= "</div>";
          		}elseif($control_type=='select' && !in_array($control_name,$child_benefit_arr)){
          			$required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : "";
          			$data .= "<div class='col-sm-4'>";
                      $data .= "<div class='form-group'>";
                      if(!empty($control_value)){
                      	$is_readonly = !empty($control_value) ? "readonly" : "";
                      	$data .= "<input type='text' id='".$control_name."_".$number."' name='".$control_name."[".$number."]' class='form-control ".$class."' value='".$control_value."' ".$is_readonly." >";
                      }else{
                      	$data .= "<select class='form-control child_member_field child_select_".$number." ".$class."' name='".$control_name."[".$number."]' id='".$control_name."_".$number."' required data-live-search='true'>";
                      	if($control_name=='child_state'){
                      		$data .= "<option value=''></option>";
                      		foreach($stateRes as $key => $value){
                      		  $child_state_selected = $value['name'] == $control_value ? "selected" : "";
                      		  $data .= "<option data-state_id='".$value['id']."' value='".$value['name']."' ".$child_state_selected.">".$value['name']."</option>";
                      		}
                      	}elseif(in_array($control_name,array('child_height'))){
                      		$data .= "<option value=''></option>";
                      		for($i=1; $i<=8; $i++){
                      		  for($j=0; $j<=11; $j++){
                      		  	$is_selected = $control_value == $i.'.'.$j ? "selected" : "";
                      		  	$data .= "<option value='".$i.".".$j."' ".$is_selected.">".$i." Ft. ".$j." In.</option>";
                      		  }
                      		}
                      	}elseif(in_array($control_name,array('child_weight'))){
                      		$data .= "<option value=''></option>";
                      		for($i=1; $i<=1000; $i++){
                      			$is_selected = $control_value == $i ? "selected" : "";
                      			$data .= "<option value='".$i."' ".$is_selected.">".$i."</option>";
                      		}
                      	}elseif(in_array($control_name,array('child_no_of_children'))){
                      		$data .= "<option value=''></option>";
                      		for($i=1; $i<=15; $i++){
                      			$is_selected = $control_value == $i ? "selected" : "";
                      			$data .= "<option value='".$i."'>".$i."</option>";
                      		}
                      	}elseif(in_array($control_name,array('child_pay_frequency'))){
                      		$data .= "<option value=''></option>";
                      		$is_selected = $control_value == "Annual" ? "selected" : "";
                            $data .= "<option value='Annual' ".$is_selected.">Annual</option>";
                            $is_selected = $control_value == "Monthly" ? "selected" : "";
                            $data .= "<option value='Monthly' ".$is_selected.">Monthly</option>";
                            $is_selected = $control_value == "Semi-Monthly" ? "selected" : "";
                           	$data .= "<option value='Semi-Monthly' ".$is_selected.">Semi-Monthly</option>";
                           	$is_selected = $control_value == "Semi-Weekly" ? "selected" : "";
                           	$data .= "<option value='Semi-Weekly' ".$is_selected.">Semi-Weekly</option>";
                           	$is_selected = $control_value == "Weekly" ? "selected" : "";
                           	$data .= "<option value='Weekly' ".$is_selected.">Weekly</option>";
                           	$is_selected = $control_value == "Hourly" ? "selected" : "";
                           	$data .= "<option value='Hourly' ".$is_selected.">Hourly</option>";
                      	}
                      	$data .= "</select>";
                      }
                      	$data .= "<label>".$label.$required."</label>";
                      	$data .= "<p class='error' id='error_".$control_name."_".$number."'></p>";
                      $data .= "</div>";
                    $data .= "</div>";
          		}elseif($control_type=='radio' && !in_array($control_name,$child_benefit_arr)){
					if(in_array($control_name,array('child_smoking_status','child_tobacco_status','child_has_spouse','child_employment_status','child_us_citizen'))){
						$data .='<input type="hidden" name="'.$control_name.'['.$number.']" id="hidden_'.$control_name.'_'.$number.'" value="">';
					}
          			$data .= "<div class='col-sm-4'>";
                      $data .= "<div class='form-group'>";
                        $data .= "<div class='btn-group btn-custom-group btn-group-justified'>";
                        $is_readonly = !empty($control_value) ? "readonly" : "";
                        $is_disabled = !empty($control_value) ? "disabled" : "";
                        if($control_name=='child_smoking_status'){
                          $data .= "<div class='toggle-item'>";
                          	$is_active = !empty($control_value) && $control_value == 'Y' ? "active" : "";
                          	$is_checked = !empty($control_value) && $control_value == 'Y' ? "checked" : "";
                          	$data .= "<input class='js-switch child_member_field' id='".$control_name.$number."_y' type='radio' name='".$control_name."[".$number."]' value='Y' ".$is_checked." ".$is_readonly."/>";
                          	$data .= "<label for='".$control_name.$number."_y' class='btn btn-info ".$is_active."' ".$is_disabled.">Smokes</label>";
                          $data .= "</div>";
                          $data .= "<div class='toggle-item'>";
                          	$is_active = !empty($control_value) && $control_value == 'N' ? "active" : "";
                          	$is_checked = !empty($control_value) && $control_value == 'N' ? "checked" : "";
                          	$data .= "<input class='js-switch child_member_field' id='".$control_name.$number."_n' type='radio' name='".$control_name."[".$number."]' value='N' ".$is_checked." ".$is_readonly."/>";
                          	$data .= "<label for='".$control_name.$number."_n' class='btn btn-info ".$is_active."' ".$is_disabled.">Non Smokes</label>";
                          $data .= "</div>";
                        }else if($control_name=='child_tobacco_status'){
                          $data .= "<div class='toggle-item'>";
                          	$is_active = !empty($control_value) && $control_value == 'Y' ? "active" : "";
                          	$is_checked = !empty($control_value) && $control_value == 'Y' ? "checked" : "";
                          	$data .= "<input class='js-switch child_member_field' id='".$control_name.$number."_y' type='radio' name='".$control_name."[".$number."]' value='Y' ".$is_checked." ".$is_readonly."/>";
                          	$data .= "<label for='".$control_name.$number."_y' class='btn btn-info".$is_active."' ".$is_disabled.">Tobacco</label>";
                          $data .= "</div>";
                          $data .= "<div class='toggle-item'>";
                          	$is_active = !empty($control_value) && $control_value == 'N' ? "active" : "";
                          	$is_checked = !empty($control_value) && $control_value == 'N' ? "checked" : "";
                          	$data .= "<input class='js-switch child_member_field' id='".$control_name.$number."_n' type='radio' name='".$control_name."[".$number."]' value='N' ".$is_checked." ".$is_readonly."/>";
                          	$data .= "<label for='".$control_name.$number."_n' class='btn btn-info ".$is_active."' ".$is_disabled.">Non Tobacco</label>";
                          $data .= "</div>";
                        }else if($control_name=='child_has_spouse'){
                          $data .= "<div class='toggle-item'>";
                          	$is_active = !empty($control_value) && $control_value == 'Y' ? "active" : "";
                          	$is_checked = !empty($control_value) && $control_value == 'Y' ? "checked" : "";
                          	$data .= "<input class='js-switch child_member_field' id='".$control_name.$number."_y' type='radio' name='".$control_name."[".$number."]' value='Y' ".$is_checked." ".$is_readonly."/>";
                          	$data .= "<label for='".$control_name.$number."_y' class='btn btn-info ".$is_active."' ".$is_disabled.">Spouse</label>";
                          $data .= "</div>";
                          $data .= "<div class='toggle-item'>";
                          	$is_active = !empty($control_value) && $control_value == 'N' ? "active" : "";
                          	$is_checked = !empty($control_value) && $control_value == 'N' ? "checked" : "";
                          	$data .= "<input class='js-switch child_member_field' id='".$control_name.$number."_n' type='radio' name='".$control_name."[".$number."]' value='N' ".$is_checked." ".$is_readonly."/>";
                          	$data .= "<label for='".$control_name.$number."_n' class='btn btn-info ".$is_active."' ".$is_disabled.">Non Spouse</label>";
                          $data .= "</div>";
                        }else if($control_name == 'child_employment_status'){
                          $data .= "<div class='toggle-item'>";
                          	$is_active = !empty($control_value) && $control_value == 'Y' ? "active" : "";
                          	$is_checked = !empty($control_value) && $control_value == 'Y' ? "checked" : "";
                          	$data .= "<input class='js-switch child_member_field' id='".$control_name.$number."_y' type='radio' name='".$control_name."[".$number."]' value='Y' ".$is_checked." ".$is_readonly."/>";
                          	$data .= "<label for='".$control_name.$number."_y' class='btn btn-info ".$is_active."' ".$is_disabled.">Employed</label>";
                          $data .= "</div>";
                          $data .= "<div class='toggle-item'>";
                          	$is_active = !empty($control_value) && $control_value == 'N' ? "active" : "";
                          	$is_checked = !empty($control_value) && $control_value == 'N' ? "checked" : "";
                          	$data .= "<input class='js-switch child_member_field' id='".$control_name.$number."_n' type='radio' name='".$control_name."[".$number."]' value='N' ".$is_checked." ".$is_readonly."/>";
                          	$data .= "<label for='".$control_name.$number."_n' class='btn btn-info ".$is_active."' ".$is_disabled.">Unemployed</label>";
                          $data .= "</div>";
                        }else if($control_name == 'child_us_citizen'){
                          $data .= "<div class='toggle-item'>";
                          	$is_active = !empty($control_value) && $control_value == 'Y' ? "active" : "";
                          	$is_checked = !empty($control_value) && $control_value == 'Y' ? "checked" : "";
                          	$data .= "<input class='js-switch child_member_field' id='".$control_name.$number."_y' type='radio' name='".$control_name."[".$number."]' value='Y' ".$is_checked." ".$is_readonly."/>";
                          	$data .= "<label for='".$control_name.$number."_y' class='btn btn-info ".$is_active."' ".$is_disabled."> U.S. Citizen</label>";
                          $data .= "</div>";
                          $data .= "<div class='toggle-item'>";
                          	$is_active = !empty($control_value) && $control_value == 'N' ? "active" : "";
                          	$is_checked = !empty($control_value) && $control_value == 'N' ? "checked" : "";
                          	$data .= "<input class='js-switch child_member_field' id='".$control_name.$number."_n' type='radio' name='".$control_name."[".$number."]' value='N' ".$is_checked." ".$is_readonly."/>";
                          	$data .= "<label for='".$control_name.$number."_n' class='btn btn-info ".$is_active."' ".$is_disabled."> Not  U.S. Citizen</label>";
                          $data .= "</div>";
                        }
                        $data .= "</div>";
                        $data .= "<p class='error' id='error_".$control_name."_".$number."'></p>";
                      $data .= "</div>";
                    $data .= "</div>";
          		}
          		if(in_array($control_name,$child_benefit_arr)){

          		}
          	}else{
          		$custom_name = str_replace($prd_question_id,"", $control_name);
          		$resAnswer = $ajaxApiCall->ajaxApiCall(['api_key'=>'customeQuestionAnswer','questionId'=>$prd_question_id],true);
          		$data .= "<div class='clearfix'></div>";
          		if($control_type == 'select'){
          		  $required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : "";
          		  $data .= "<div class='col-sm-12'>";
                    $data .= "<p>";
                      $data .= "<label>".$label."</label>";
                    $data .= "</p>";
                    $data .= "<div class='form-group height_auto w-300 custom_question'>";
                      $data .= "<select class='form-control child_select child_select_'".$number." name='".$custom_name."[".$number."][".$prd_question_id."]' id='".$control_name."_".$number."' required data-live-search='true' data-id='".$number."'>";
                        $data .= "<option value=''></option>";
                        if(!empty($resAnswer)){
                          foreach($resAnswer as $ansKey => $ansValue){
                          	$data .= "<option value='".$ansValue['answer']."' data-ans-eligible='".$ansValue['answer_eligible']."'>".$ansValue['answer']."</option>";
                          }
                        }
                      $data .= "</select>";
                      $data .= "<label>".$label.$required."</label>";
                    	$data .= "<p class='error' id='error_".$custom_name."_".$number."_".$prd_question_id."'></p>";
                    $data .= "</div>";
                  $data .= "</div>";
          		}elseif($control_type=='radio'){
          		  $required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : "";
          		  $data .= "<div class='col-sm-12 m-b-25'>";
                    $data .= "<p>";
                      $data .= "<label>".$label.$required."</label>";
                    $data .= "</p>";
                    $data .= "<div class='radio-button'>";
                      $data .= "<div class='btn-group colors  custom-question-btn' data-toggle='buttons'>";
                      if(!empty($resAnswer)){
                      	foreach($resAnswer as $ansKey => $ansValue){
                      	  $data .= "<label class='btn btn-info'>";
                      	  	$data .= "<input type='radio' name='".$custom_name."[".$number."][".$prd_question_id."]' value='".$ansValue['answer']."' data-ans-eligible='".$ansValue['answer_eligible']."' class='js-switch child_member_field' autocomplete='false'>".$ansValue['answer'];
                      	  $data .= "</label>";
                      	}
                      }
                  	  $data .= "</div>";
                  	$data .= "</div>";
                  	$data .= "<p class='error' id='error_".$custom_name."_".$number."_".$prd_question_id."'></p>";
                  $data .= "</div>";
          		}elseif($control_type=='select_multiple'){
          		  $required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : "";
          		  $data .= "<div class='col-sm-12'>";
          		  	$data .= "<p>";
          		  	  $data .= "<label>".$label."</label>";
          		  	$data .= "</p>";
          		  	$data .= "<div class='form-group height_auto w-300 custom_question'>";
          		  	  $data .= "<select id='".$control_name."_".$number."' name='".$custom_name."[".$number."][".$prd_question_id."][]' class='se_multiple_select child_multiple_select child_member_field child_multiple_select_".$number."' required multiple='multiple' data-id='".$number."'>";
          		  	  if(!empty($resAnswer)){
          		  	  	foreach($resAnswer as $ansKey => $ansValue){
          		  	  	  $data .= "<option value='".$ansValue['answer']."' data-ans-eligible='".$ansValue['answer_eligible']."'>".$ansValue['answer']."</option>";
          		  	  	}
          		  	  }
          		  	  $data .= "</select>";
          		  	  $data .= "<label>".$label.$required."</label>";
          		  	  $data .= "<p class='error' id='error_".$custom_name."_".$number."_".$prd_question_id."'></p>";
          		  	$data .= "</div>";
          		  $data .= "</div>";
          		}elseif($control_type=='textarea'){
          		  $required = $is_required == 'Y' ? "<span class='req-indicator'>*</span>" : "";
          		  $data .= "<div class='col-sm-12 form-inline m-b-25'>";
          		  	$data .= "<p>";
          		  	  $data .= "<label>".$label.$required."</label>";
          		  	$data .= "</p>";
          		  	$data .= "<textarea id='".$control_name."_".$number."' class='form-control' name='".$custom_name."[".$number."][".$prd_question_id."]'rows='3' cols='50' maxlength='300' data-id=".$number."></textarea>";
          		  	$data .= "<p class='error' id='error_".$custom_name."_".$number."_".$prd_question_id."'></p>";
          		  $data .= "</div>";
          		}
          	}
	  	  }
			$data .= "</div>";
	}
	// $data .= "<p class='m-b-20'> Verification of Dependent ";
	//   		$data .="<i class='fa fa-info-circle' data-container='body' data-toggle='popover' title='Ways to Verify Dependents' data-trigger='hover' data-placement='top' data-html='true'>";
    //     $data .= "</i>";
	//   	$data .= "</p>";
	//   	$data .= "<div class='row'>";
	//   		$data .= "<div class='col-sm-4'>";
	//   	  	$data .= "<div class='form-group'>";
	//   	  		$data .= "<div class='custom_drag_control'>";
	//   	  			$data .="<span class='btn btn-info'>Upload</span>";
	//   	  			$data .="<input type='file' class='gui-file' id='child_verification_doc_".$number."' name='child_verification_doc[".($display_number-1)."]' ".(!empty($childFileArr[$display_number-1]) ? 'disabled' : '' ).">";
    //           $data .= "<input type='text' class='gui-input' placeholder='Choose File(s)' value='".checkIsset($childFileArr[$display_number-1])."' size='' ".(!empty($childFileArr[$display_number-1]) ? 'disabled' : '' ).">";
	// 		  $data .="<p class='error text-left' id='error_child_verification_doc_".($display_number-1)."'></p>";
	//   	  		$data .= "</div>";
	//   	  	$data .= "</div>";
	//   	  $data .= "</div>";
	//   	$data .= "</div>";

$data .= "</div>";
$res['number'] = $number;
$res['html'] = $data;
$res['status'] = 'success';
echo json_encode($res);
exit;
?>