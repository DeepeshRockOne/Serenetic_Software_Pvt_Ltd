<?php 
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/Api.class.php';
include_once __DIR__ . '/includes/apiUrlKey.php';
include_once __DIR__ . '/includes/group_member_enrollment.class.php';
$groupMemberEnrollment = new groupMemberEnrollment();

$ajaxApiCall = new Api();

$response = array();

$product_list = !empty($_POST['product_list']) ? explode(",",$_POST['product_list']) : array();
$principal_beneficiary_number = !empty($_POST['principal_beneficiary_field_number']) ? explode("_", $_POST['principal_beneficiary_field_number']) : array();

$number = checkIsset($principal_beneficiary_number[0]);
$display_number = checkIsset($principal_beneficiary_number[1]);
$response['number'] = $number;

$dependent_information = array();
$dependent_count = 0;

$principal_existing_dependent = checkIsset($_POST['principal_existing_dependent'],'arr');
$primary_address1 = checkIsset($_POST['primary_address1']);
$spouse_fname = checkIsset($_POST['spouse_fname'],'arr');
$child_fname = checkIsset($_POST['child_fname'],'arr');

if(!empty($spouse_fname)){
	foreach ($spouse_fname as $key => $value) {
		$dependent_information[$dependent_count]['fname'] = !empty($_POST['spouse_fname'][$key]) ? $_POST['spouse_fname'][$key] : '';
		$dependent_information[$dependent_count]['lname'] = !empty($_POST['spouse_lname'][$key]) ? $_POST['spouse_lname'][$key] : '';
		$dependent_information[$dependent_count]['phone'] = !empty($_POST['spouse_phone'][$key]) ? $_POST['spouse_phone'][$key] : '';
		$dependent_information[$dependent_count]['email'] = !empty($_POST['spouse_email'][$key]) ? $_POST['spouse_email'][$key] : '';
		$dependent_information[$dependent_count]['ssn']  = !empty($_POST['spouse_SSN'][$key]) ? $_POST['spouse_SSN'][$key] : '';
		$dependent_information[$dependent_count]['address']  = $primary_address1;
		$dependent_information[$dependent_count]['type']  = 'Spouse';
		$dependent_information[$dependent_count]['id']  = 'Spouse_1';
		$dependent_information[$dependent_count]['key']  = 1;
		$dependent_count++;
	}
}
if(!empty($child_fname)){
	foreach ($child_fname as $key => $value) {
		$dependent_information[$dependent_count]['fname'] = !empty($_POST['child_fname'][$key]) ? $_POST['child_fname'][$key] : '';
		$dependent_information[$dependent_count]['lname'] = !empty($_POST['child_lname'][$key]) ? $_POST['child_lname'][$key] : '';
		$dependent_information[$dependent_count]['phone'] = !empty($_POST['child_phone'][$key]) ? $_POST['child_phone'][$key] : '';
		$dependent_information[$dependent_count]['email'] = !empty($_POST['child_email'][$key]) ? $_POST['child_email'][$key] : '';
		$dependent_information[$dependent_count]['ssn']  = !empty($_POST['child_SSN'][$key]) ? $_POST['child_SSN'][$key] : '';
		$dependent_information[$dependent_count]['address']  = $primary_address1;
		$dependent_information[$dependent_count]['type']  = 'Child';
		$dependent_information[$dependent_count]['id']  = 'Child_'.$key;
		$dependent_information[$dependent_count]['key']  = $key;
		$dependent_count++;
	}
}

$waive_checkbox = checkIsset($_POST['waive_checkbox']);
if(!empty($waive_checkbox)){
  $group_waive_product = checkIsset($_POST['waive_products'],'arr');

  if(!empty($group_waive_product)){
    $group_product_list = $groupMemberEnrollment->getGroupWaiveProductList($waive_checkbox,$group_waive_product);
    $product_list = array_merge($product_list,$group_product_list);
  }
}
// pre_print($product_list);
$postArray = array(
	'productList' => $product_list,
	'api_key' => 'getPrincipalBeneficiary'
);
$apiResponse = $ajaxApiCall->ajaxApiCall($postArray,true);
$principal_beneficiary_field = checkIsset($apiResponse["data"],'arr');

if(!empty($product_list)){
	$postArray = array(
		'productList' => $product_list,
		'api_key' => 'getProductDetails'
	);
	$apiResponse = $ajaxApiCall->ajaxApiCall($postArray,true);
	$productRes = checkIsset($apiResponse["data"],'arr');
}

if(!empty($principal_beneficiary_field)){
	ob_start();
?>
	<div id="inner_principal_beneficiary_field_<?= $number ?>" class="inner_principal_beneficiary_field">
	<div class="clearfix m-b-25">
	    <h5 class="mn pull-left">Principal Beneficiary <span data-display_number="<?= $display_number ?>" data-id="<?= $number ?>" id="principal_beneficiary_number_<?= $number ?>" class="display_principal_beneficiary_number"><?= $display_number ?></span>
	    <a href="javascript:void(0);" class="red-link removePrincipalBeneficiaryField" data-id="<?= $number ?>">Remove</a>
	    </h5>
  	</div>

 	<div class="row enrollment_auto_row">
 		<div class="col-sm-3">
	      <div class="form-group">
	      	<input type="hidden" name="principal_beneficiary_id[<?= $number ?>]" id="principal_beneficiary_id_<?= $number ?>" value="0">
	      	<select id="principal_existing_dependent_<?= $number ?>"  name="principal_existing_dependent[<?= $number ?>]" class="principal_beneficiary_select_<?= $number ?> principal_beneficiary_select" data-id="<?= $number ?>" data-select-val="">
	      		<option value="" ></option>
	      		<?php if(!empty($dependent_information)){ ?>
	      			<?php foreach ($dependent_information as $key => $row) { ?>
	      				<?php if(!empty($row['fname'])){ ?>
	      					<option value="<?= $row['id'] ?>" data-key="<?= $row['key'] ?>" data-full-name="<?= $row['fname'] .' '.$row['lname'] ?>" data-type="<?= $row['type'] ?>" data-fname = "<?= $row['fname'] ?>" data-lname = "<?= $row['lname'] ?>" data-phone = "<?= $row['phone'] ?>" data-email = "<?= $row['email'] ?>" data-ssn = "<?= $row['ssn'] ?>" data-address="<?= $row['address'] ?>" style="<?= !empty($principal_existing_dependent) && in_array($row['id'],$principal_existing_dependent) ? 'display:none' : '' ?>"><?= $row['fname'] .' '.$row['lname'] .' ('.$row['type'].')' ?></option>
	      				<?php } ?>	
	      			<?php } ?>
	      		<?php } ?>
	      	</select>
	        <label>Select Existing Dependent</label>
	      </div>
	    </div>
	</div>
	<div class="row enrollment_auto_row">
	    <?php foreach ($principal_beneficiary_field as $key => $row) { ?>
	        <?php
	          $prd_question_id = $row['id'];
	          $is_required= $row['required'];
	          $control_name = 'principal_'.$row['label'];
	          $label = $row['display_label'];
	          $control_type = $row['control_type'];
	          $class = $row['control_class'];
	          $maxlength = $row['control_maxlength'];
	          $control_attribute = $row['control_attribute'];
	          $questionType = $row['questionType'];
	          if($control_name == "principal_queBeneficiaryAllow3"){
	          	continue;
	          }
	        ?>
	      	<?php if($control_type=='text'){?>
				<div class="col-lg-3 col-md-6">
					<?php if($control_name =="principal_queBeneficiaryPercentage"){?>
						<div class="form-group">
							<div class="input-group">
								<div class="pr">
									<input type="text" id="<?= $control_name ?>_<?= $number ?>" maxlength="<?= $maxlength ?>" name="<?= $control_name ?>[<?= $number ?>]" value="" class="form-control <?= $class ?>"  required data-id="<?= $number ?>" onkeypress="return isNumber(event)">
									<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
								</div>
								<div class="input-group-addon"> % </div>
							</div>
							<p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
						</div>
					<?php }else{?>
						<div class="form-group">
							<input type="text" id="<?= $control_name ?>_<?= $number ?>" maxlength="<?= $maxlength ?>" name="<?= $control_name ?>[<?= $number ?>]" value="" class="form-control <?= ($control_name == "principal_queBeneficiaryEmail") ? "no_space" : ""; ?> <?= $class ?>"  required data-id="<?= $number ?>">
							<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
							<p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
						</div>
					<?php } ?>
					
				</div>
	      	<?php }else if($control_type=='select'){ ?>
	    		<div class="col-lg-3 col-md-6">
              		<div class="form-group">
		              	<select id="<?= $control_name ?>_<?= $number ?>"  name="<?= $control_name ?>[<?= $number ?>]" class="principal_beneficiary_select_<?= $number ?> <?= $class ?>" required data-live-search="true" data-id="<?= $number ?>">
		              		<option value="" hidden></option>
		              		<?php if($control_name=='principal_queBeneficiaryRelationship'){ ?>
		                        <option value="Child">Child</option>
		                        <option value="Spouse">Spouse</option>
		                        <option value="Parent">Parent</option>
		                        <option value="Grandparent">Grandparent</option>
		                        <option value="Friend">Friend</option>
		                        <option value="Other">Other</option>
		                	<?php } ?>
	                	</select>
		              	<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
            	  		<p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
	              </div>
	          	</div>
	        <?php }else if($control_type=='select_multiple'){ ?>
	        	<div class="col-lg-3 col-md-6">
              		<div class="form-group">
		              	<select id="<?= $control_name ?>_<?= $number ?>"  name="<?= $control_name ?>[<?= $number ?>][]" class="se_multiple_select principal_beneficiary_multiple_select_<?= $number ?>" required data-live-search="true" data-id="<?= $number ?>" multiple="multiple">
		              		<?php if($control_name=='principal_product'){  ?>
		                		<?php if(!empty($productRes)) { ?>
					      			<?php foreach ($productRes as $key => $productRow) { ?>
					      				<?php if(in_array($productRow['id'],$row['product_ids'])){ ?>
					      					<option value="<?= $productRow['id'] ?>" <?= (!empty($row['product_ids']) && in_array($productRow['id'],$row['product_ids'])) ? '' : ''?>><?= $productRow['name'].' ('.$productRow['product_code'] .')' ?></option>	
					      				<?php } ?>
										      		
					      			<?php } ?>
					      		<?php } ?>
		                	<?php } ?>
	                	</select>
		              	<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
            	  		<p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
	              </div>
	          	</div>
	      	<?php }?>
		<?php } ?>
	</div>
	<hr>
	</div>

	<?php
	$html = ob_get_clean();
	$response['html']=$html;
	$response['status']='success';
}else{
	$response['status']='fail';
}

echo json_encode($response);
exit;
?>