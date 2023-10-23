<?php include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
$MemberEnrollment = new MemberEnrollment();
$response = array();

$childData = isset($_POST['child']) ? $_POST['child'] : array();
$product_list = isset($_POST['child_products_list'])? explode(",", $_POST['child_products_list']):array();
$product_plan = isset($_POST['product_plan'])?$_POST['product_plan']:array();

$enrolleeElementsVal = !empty($_POST['enrolleeElementsVal'])? json_decode($_POST['enrolleeElementsVal'],true):array();

$dependent_field_number = isset($_POST['dependent_field_number'])? explode("_", $_POST['dependent_field_number']):array();

$number = $dependent_field_number[0];
$display_number = $dependent_field_number[1];

$response['number']=$number;
$enrollmentLocation = isset($_POST['enrollmentLocation'])?$_POST['enrollmentLocation']:"";
$is_group_member = isset($_POST['is_group_member'])?$_POST['is_group_member']:"N";

if($enrollmentLocation=='groupSide' || $is_group_member == "Y"){

  $waive_checkbox = !empty($_POST['waive_checkbox']) ? $_POST['waive_checkbox'] : '';
  if(!empty($waive_checkbox)){

    $group_waive_product=isset($_POST['waive_products'])?$_POST['waive_products']:array();

    if(!empty($group_waive_product)){
      $group_product_list=$MemberEnrollment->getGroupWaiveProductList($waive_checkbox,$group_waive_product);
      $product_list = array_merge($product_list,$group_product_list);
    }
  }
}
if(!empty($_GET['cd_profile_id']) && !empty($_GET['order_id'])) {
	$cust_dep_sql = "SELECT *,GROUP_CONCAT(product_id,',') as product_ids  FROM customer_dependent WHERE cd_profile_id=:cd_profile_id AND order_id=:order_id GROUP BY cd_profile_id";
	$dep_row = $pdo->selectOne($cust_dep_sql,array(":cd_profile_id"=>$_GET['cd_profile_id'],":order_id"=>$_GET['order_id']));	

	if(!empty($dep_row)) {
		$child_fname_value = $dep_row["fname"];
		$child_lname_value = $dep_row["lname"];
		$child_SSN_value = $dep_row["ssn"];
		$child_email_value = $dep_row["email"];
		$child_birthdate_value = date("m/d/Y",strtotime($dep_row["birth_date"]));
		$child_gender_value = $dep_row["gender"];

		$dep_row["hire_date"] = (strtotime($dep_row["hire_date"]) > 0?date("m/d/Y",strtotime($dep_row["hire_date"])):'');
		$dep_row['product_ids'] = explode(',',$dep_row['product_ids']);

		$custom_que_sql = "SELECT ccq.answer,q.label,q.control_type
                      FROM customer_custom_questions ccq 
                      JOIN prd_enrollment_questions q ON(q.id = ccq.question_id AND q.is_deleted='N')
                      WHERE ccq.customer_id=:customer_id AND ccq.enrollee_type='child' AND ccq.dependent_id=:dependent_id AND ccq.is_deleted='N'";
	    $custom_que_res = $pdo->select($custom_que_sql,array(":customer_id"=>$dep_row["customer_id"],":dependent_id"=>$dep_row["id"]));
	    if(!empty($custom_que_res)) {
	        foreach ($custom_que_res as $custom_que_row) {
	        	${"child_".$custom_que_row['label']."_value"} = $custom_que_row['answer'];
	            
	        }
	    }
	}
}
$child_dep_row = array();
if(!empty($_POST['customer_id']) && !empty($_POST['is_add_product'])){
	$cust_dep_sql = "SELECT * FROM customer_dependent_profile WHERE customer_id=:customer_id AND relation in('Son','son','daughter','Daughter') AND is_deleted = 'N' GROUP BY id";
	$child_dep_row = $pdo->select($cust_dep_sql,array(":customer_id"=>$_POST['customer_id']));	
}

if(!empty($enrolleeElementsVal['child_fname_'.$display_number])) {
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

$child_zip_value = isset($_POST['primary_zip'])? trim($_POST['primary_zip']):"";
$child_city_value = isset($zipCacheList[$child_zip_value])? $zipCacheList[$child_zip_value]['region_name']:"";




$state_incr="";
if(!empty($child_zip_value)){
	$state_code=$zipCacheList[$child_zip_value]['state_code'];

	$getDetailOnPinCode=$pdo->selectOne("SELECT * FROM states_c WHERE country_id = '231' AND short_name=:short_name",array(":short_name"=>$state_code));

	if(!empty($getDetailOnPinCode)){
		$child_state_value = $getDetailOnPinCode['name'];
	}
}else{
	$state_res = $pdo->select("SELECT * FROM states_c WHERE country_id = 231");
}

$spouse_assign_products = !empty($_POST['spouse_assign_products']) ? $_POST['spouse_assign_products'] : array();
$child_assign_products = !empty($_POST['child_assign_products']) ? $_POST['child_assign_products'] : array();
$memberPlusOneProduct = array();
if(!empty($spouse_assign_products)){
	foreach ($spouse_assign_products as $key => $productArr) {
		if(!empty($productArr)){
			foreach ($productArr as $key => $value) {
				if(!empty($product_plan) && $product_plan[$value] == 5){
					array_push($memberPlusOneProduct,$value);
				}
			}
		}
	}
}

if(!empty($child_assign_products)){
	foreach ($child_assign_products as $key => $productArr) {
		if(!empty($productArr)){
			foreach ($productArr as $key => $value) {
				if(!empty($product_plan) && $product_plan[$value] == 5){
					array_push($memberPlusOneProduct,$value);
				}
			}
		}
	}
}

if(empty($child_lname_value)){
	$child_lname_value = !empty($_POST['primary_lname']) ? $_POST['primary_lname'] : '';
}
if(!empty($product_list)){
	$productIds = implode(",", $product_list);
	$productRes=$pdo->select("SELECT id,name,product_code FROM prd_main where id in ($productIds)");
	$child_field =$MemberEnrollment->get_child_field($product_list);

	ob_start();
	?>
	<div id="inner_child_field_<?= $number ?>" class="inner_child_field depedent_border_box">
	<div class="row enrollment_auto_row">
	   <div class="col-sm-6">
	      <div class="form-inline">
	         <div class="form-group">
	            <h5 class="mn">Child <span data-display_number="<?= $display_number ?>" data-id="<?= $number ?>" id="dependent_number_<?= $number ?>" class="display_number"><?= $display_number ?></span>
	            </h5>
	         </div>
	         <?php if($child_dep_row){ ?>
	         <div class="form-group m-l-15">
	            <select class="form-control existing_child_dependent" name="existing_child_dependent" id="existing_child_dependent_<?=$number?>" data-id="<?= $number ?>">
	               <option data-hiddent="true" value="">Existing Child</option>
	               <?php 
	                  foreach ($child_dep_row as $s) { ?>
	               <option value="<?=$s['id']?>" 
	                  data-fname="<?=$s['fname']?>"
	                  data-lname="<?=$s['lname']?>"
	                  data-email="<?=$s['email']?>"
	                  data-gender="<?=$s['gender']?>"
	                  data-birth_date="<?=date("m/d/Y",strtotime($s["birth_date"]));?>"
	                  data-ssn="<?=$s["ssn"]?>"
	                  >
	                  <?=$s['fname'] ." ". $s['lname']?>			
	               </option>
	               <?php } ?>
	            </select>
	         </div>
	         <?php } ?>
	      </div>
	   </div>
	   <div class="col-sm-6 text-right">
	      <div class="m-b-25">	
	         <a href="javascript:void(0);" class="red-link removeChildField" data-id="<?= $number ?>">Remove</a>
	      </div>
	   </div>
	</div>
 	<div class="row enrollment_auto_row">
 		<div class="col-sm-3">
	      <div class="form-group">
	      	<input type="hidden" name="child_cd_profile_id[<?= $number ?>]" id="child_cd_profile_id_<?= $number ?>" value="<?=!empty($_GET['cd_profile_id'])?$_GET['cd_profile_id']:'0'?>">
	      	<select id="que_child_assign_products_<?= $number ?>"  name="child_assign_products[<?= $number ?>][]" class="se_multiple_select child_dependent_multiple_select" multiple="multiple" data-id="<?= $number ?>">
	      		<?php if(!empty($productRes)) { ?>
	      			<?php foreach ($productRes as $key => $productRow) { ?>
						<option value="<?= $productRow['id'] ?>" data-product-plan="<?= $product_plan[$productRow['id']] ?>" <?= (!isset($dep_row['product_ids']) && !empty($product_plan) && $product_plan[$productRow['id']] != 5) || (!empty($dep_row['product_ids']) && in_array($productRow['id'],$dep_row['product_ids'])) ? 'selected' : ''?> <?= !empty($memberPlusOneProduct) && in_array($productRow['id'], $memberPlusOneProduct) ? 'disabled' : '' ?>><?= $productRow['name'].' ('.$productRow['product_code'] .')' ?></option>	      		
	      			<?php } ?>
	      		<?php } ?>
	      	</select>
	        <label>Assign Products<em>*</em></label>
	        <p class="error" id="error_child_assign_products_<?= $number ?>"></p>
	      </div>
	    </div>

	    <?php if(array_key_exists('fname', $child_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <input type="text" name="child_fname[<?= $number ?>]" id="que_child_fname_<?= $number ?>" class="form-control child_fname_<?= $number ?> <?= !empty($child_fname_value) ? 'has-value' : '' ?>" value="<?= !empty($child_fname_value) ? $child_fname_value : '' ?>" data-id="<?= $number ?>">
		        <label>First Name<?php if($child_field['fname']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		        <p class="error" id="error_child_fname_<?= $number ?>"></p>
		      </div>
		    </div>
		    <?php unset($child_field['fname']); ?>
		<?php } ?>
		<?php if(array_key_exists('lname', $child_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <input type="text" name="child_lname[<?= $number ?>]" id="que_child_lname_<?= $number ?>" class="form-control child_last_name child_lname_<?= $number ?> <?= !empty($child_lname_value) ? 'has-value' : '' ?>" value="<?= !empty($child_lname_value) ? $child_lname_value : '' ?>" data-id="<?= $number ?>">
		        <label>Last Name<?php if($child_field['lname']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		        <p class="error" id="error_child_lname_<?= $number ?>"></p>
		      </div>
		    </div>
		    <?php unset($child_field['lname']); ?>
	    <?php } ?>
		<?php if(array_key_exists('email', $child_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <input type="text" name="child_email[<?= $number ?>]" id="que_child_email_<?= $number ?>" class="form-control no_space child_email_<?= $number ?> <?= !empty($child_email_value) ? 'has-value' : '' ?>" value="<?= !empty($child_email_value) ? $child_email_value : '' ?>" <?= !empty($child_email_value) ? 'readonly' : '' ?> data-id="<?= $number ?>">
		        <label>Email<?php if($child_field['email']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		        <p class="error" id="error_child_email_<?= $number ?>"></p>
		      </div>
		    </div>
		    <?php unset($child_field['email']); ?>
	    <?php } ?>
		<?php if(array_key_exists('SSN', $child_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <input type="text" name="child_SSN[<?= $number ?>]" id="que_child_SSN_<?= $number ?>" class="form-control child_SSN_<?= $number ?> <?= !empty($child_SSN_value) ? 'has-value' : '' ?> SSN_mask" value="<?= !empty($child_SSN_value) ? $child_SSN_value : '' ?>" data-id="<?= $number ?>">
		        <label>SSN<?php if($child_field['SSN']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		        <p class="error" id="error_child_SSN_<?= $number ?>"></p>
		      </div>
		    </div>
		    <?php unset($child_field['SSN']); ?>
	    <?php } ?>
		<?php if(array_key_exists('birthdate', $child_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <div class="input-group">
		          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
		          <div class="pr">
		            <input type="text" name="child_birthdate[<?= $number ?>]" id="que_child_birthdate_<?= $number ?>" class="form-control child_birthdate_<?= $number ?> <?= !empty($child_birthdate_value) ? 'has-value' : '' ?> dob" value="<?= !empty($child_birthdate_value) ? $child_birthdate_value : '' ?>" <?= !empty($child_birthdate_value) ? 'readonly' : '' ?> data-id="<?= $number ?>">
		            <label>DOB<?php if($child_field['birthdate']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?> (MM/DD/YYYY)</label>
		          </div>
		        </div>
		        <p class="error" id="error_child_birthdate_<?= $number ?>"></p>
		      </div>
		    </div>
		    <?php unset($child_field['birthdate']); ?>
	    <?php } ?>
		<?php if(array_key_exists('gender', $child_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <div class="btn-group colors btn-group-justified <?= !empty($child_gender_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
		          <label class="btn btn-info <?= !empty($child_gender_value) && $child_gender_value=="Male" ? 'active' : '' ?>" <?= !empty($child_gender_value) ? 'disabled' : '' ?>>
		            <input type="radio" name="child_gender[<?= $number ?>]" value="Male" autocomplete="off" class="js-switch" <?= !empty($child_gender_value) ? 'readonly' : '' ?> <?= !empty($child_gender_value) && $child_gender_value=="Male" ? 'checked' : '' ?> data-id="<?= $number ?>"> Male
		          </label>
		          <label class="btn btn-info <?= !empty($child_gender_value) && $child_gender_value=="Female" ? 'active' : '' ?>" <?= !empty($child_gender_value) ? 'disabled' : '' ?>>
		            <input type="radio" name="child_gender[<?= $number ?>]" value="Female" autocomplete="off" class="js-switch" <?= !empty($child_gender_value) ? 'readonly' : '' ?> <?= !empty($child_gender_value) && $child_gender_value=="Female" ? 'checked' : '' ?> data-id="<?= $number ?>"> Female
		          </label>
		        </div>
		        <p class="error" id="error_child_gender_<?= $number ?>"></p>
		      </div>
		    </div>
		    <?php unset($child_field['gender']); ?>
	    <?php } ?>
  	</div>
  	<?php 

  	?>
	<?php if(!empty($child_field)){ 
		$child_benefit_arr = array('child_benefit_amount','child_in_patient_benefit','child_out_patient_benefit','child_monthly_income','child_benefit_percentage'); 
		?>
		<div class="p-t-20 hidden-sm"></div>
  		<p class="hline-title" ><span>Additional Child Information</span></p>
  		<div class="row enrollment_auto_row">
		    <?php foreach ($child_field as $key => $row) { ?>
		        <?php
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
		          
		          	$control_value=isset($enrolleeElementsVal[$control_name."_".$display_number])?$enrolleeElementsVal[$control_name."_".$display_number]:"";
		          
		          	if(empty($control_value) && !empty(${$control_name.'_value'})){
		          		$control_value = ${$control_name.'_value'};
		          	}

		          	$tmp_control_name = get_column_name_by_control_name($row['label']);
		          	if(empty($control_value) && isset($dep_row[$tmp_control_name])) {
						$control_value = $dep_row[$tmp_control_name];
					}
		        ?>
		        <?php if($questionType=="Default"){ ?>
		          	<?php if($control_type=='text' && !in_array($control_name,$child_benefit_arr)){?>
	    				<div class="col-lg-3 col-md-6 col-sm-6">
	    					<div class="form-group">
	    						<input type="text" id="<?= $control_name ?>_<?= $number ?>" maxlength="<?= $maxlength ?>" name="<?= $control_name ?>[<?= $number ?>]" value="<?= $control_value ?>" class="form-control child_member_field <?= $class ?> <?=  $control_value != '' ? "has-value" : "" ?>"  required <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>">
	    						<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
	    						<p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
	    					</div>
	    				</div>
	          		<?php }else if($control_type=='date_mask' && !in_array($control_name,$child_benefit_arr)){ ?>
			          	<?php 
			                $dateValue='';
			                if ($control_value != ''){
			                  $dateValue = date('m/d/Y', strtotime($control_value));
			               	}
			          	?>
			          	<div class="col-lg-3 col-md-6 col-sm-6">
			                <div class="form-group">
			              		<input type="text" id="<?= $control_name ?>_<?= $number ?>"  name="<?= $control_name ?>[<?= $number ?>]" value="<?= $dateValue ?>" class="form-control child_member_field <?= $dateValue != "" ? "has-value" : "" ?> <?= $class ?>"   required  <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>">
			  			            <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
			                  	<p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
			               	</div>
		         	  	</div>
		          	<?php }else if($control_type=='select' && !in_array($control_name,$child_benefit_arr)){ ?>
		        		<div class="col-lg-3 col-md-6 col-sm-6">
		              		<div class="form-group">
				                <?php if(!empty($control_value)) { ?>
			                  		<input type="text" id="<?= $control_name ?>_<?= $number ?>" name="<?= $control_name ?>[<?= $number ?>]" value="<?= $control_value ?>" class="form-control child_member_field <?= $class ?> <?=  $control_value != '' ? "has-value" : "" ?>"  <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>">
				                <?php }else{ ?>
				              		<select id="<?= $control_name ?>_<?= $number ?>"  name="<?= $control_name ?>[<?= $number ?>]" class="child_select child_select_<?= $number ?> child_member_field <?= $control_value != '' ? "has-value" : "" ?>" required data-live-search="true" data-id="<?= $number ?>">
				              		<?php if($control_name=='child_state'){ ?>
				                      	<?php foreach ($state_res as $key => $value) { ?>
				                        	<option data-state_id="<?= $value["id"]; ?>" value="<?= $value["name"]; ?>" <?php echo $value["name"] == $control_value ? 'selected' : '' ?>><?php echo $value['name']; ?></option>
				                      	<?php } ?>
					                        
				                    <?php }else if(in_array($control_name,array('child_height'))){ ?>
				                    	<option value=""></option>
				                  		<?php for($i=1; $i<=8;$i++){?>
				                    		<?php for($j=0; $j<=11;$j++){?>
				                      			<option value="<?=$i.'.'.$j?>" <?php echo $control_value == $i.'.'.$j ? "selected='selected'" : '' ?>>
				                        		<?php
				                        			echo $i.' Ft. ';
				                        			if($j>0){
				                          				echo $j.' In. ';
				                        			}  
				                        		?>
				                      			</option>
				                    		<?php }?>
				                  		<?php }?>
			                	  	<?php }else if(in_array($control_name,array('child_weight'))){ ?>
				                  		<option value=""></option>
				    			            <?php for($i=1; $i<=1000;$i++){?>
				    			                <option value="<?= $i ?>" <?php echo $control_value == $i ? "selected='selected'" : '' ?>><?= $i ?></option>
				    			            <?php }?>
			                	  	<?php }else if(in_array($control_name,array('child_no_of_children'))){ ?>
				                  		<option value=""></option>
				                  		<?php for($i=1; $i<=15;$i++){?>
				                         <option value="<?=$i?>" <?php echo $control_value == $i ? "selected='selected'" : '' ?>><?= $i ?></option>
				                       <?php }?>
				                    <?php }else if(in_array($control_name,array('child_pay_frequency'))){ ?>
				                      <option value=""></option>
				                      <option value="Annual" <?php echo $control_value == "Annual" ? "selected='selected'" : '' ?>>Annual</option>
				                      <option value="Monthly" <?php echo $control_value == "Monthly" ? "selected='selected'" : '' ?>>Monthly</option>
				                      <option value="Semi-Monthly" <?php echo $control_value == "Semi-Monthly" ? "selected='selected'" : '' ?>>Semi-Monthly</option>
				                      <option value="Semi-Weekly" <?php echo $control_value == "Semi-Weekly" ? "selected='selected'" : '' ?>>Semi-Weekly</option>
				                      <option value="Weekly" <?php echo $control_value == "Weekly" ? "selected='selected'" : '' ?>>Weekly</option>
				                      <option value="Hourly" <?php echo $control_value == "Hourly" ? "selected='selected'" : '' ?>>Hourly</option>
			                	  	<?php } ?>
				                	</select>
				                <?php } ?>
				              	<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		            	  		<p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
		              		</div>
		          		</div>
		          	<?php }else if($control_type=='radio' && !in_array($control_name,$child_benefit_arr)){ ?>
			          	<div class="col-lg-3 col-md-6 col-sm-6">
			              <div class="form-group">
			                <div class="btn-group colors btn-group-justified  <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
		            			<?php if($control_name=='child_smoking_status'){ ?>
			            				<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
				  	                      <input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="Y" class="js-switch child_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>"> Smokes
				  	                    </label>
				  	                    <label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
				  	                      <input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="N" class="js-switch child_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>"> Non Smokes
				  	                    </label>
		            			<?php }else if($control_name=='child_tobacco_status'){ ?>
			            				<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
				  	                      <input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="Y" class="js-switch child_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>"> Tobacco
				  	                    </label>
				  	                    <label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
				  	                      <input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="N" class="js-switch child_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>"> Non Tobacco
				  	                    </label>
			  	                    
		            			<?php }else if($control_name=='child_has_spouse'){ ?>
			            				<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
				  	                      <input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="Y" class="js-switch child_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>"> Spouse
				  	                    </label>
				  	                    <label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
				  	                      <input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="N" class="js-switch child_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>"> No Spouse
				  	                    </label>
			                    <?php }else if($control_name == 'child_employment_status'){ ?>
					                  	<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
					                        <input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="Y" class="js-switch child_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>"> Employed
					                      </label>
					                      <label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
					                        <input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="N" class="js-switch child_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>">  Unemployed
					                      </label>
			                  	<?php }else if($control_name == 'child_us_citizen'){ ?>
				                  	<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
				  	                    <input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="Y" class="js-switch child_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>"> U.S. Citizen
				                    </label>
				                    <label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
				                        <input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="N" class="js-switch child_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?> data-id="<?= $number ?>"> Not  U.S. Citizen
				                    </label>
		            			<?php } ?>
			                </div>
			                <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
			              </div>
			          	</div>
		          	<?php }?>

		          	<?php if(in_array($control_name,$child_benefit_arr)) { ?>
				        <?php if(!empty($childData)) { ?>
				          <?php foreach ($childData as $productID => $dataArr) { ?>
				            <?php $productName=getname('prd_main',$productID,'name','id'); ?>
				            <?php if(!empty($dataArr)) { ?>
				              <?php foreach ($dataArr as $dataArrkey => $data) { ?>
				              	<?php
				              		if($dataArrkey != $number) {
				              			continue;
				              		}
				              	?>
				                <?php if((isset($data[str_replace('child_','',$control_name)]) && $data[str_replace('child_','',$control_name)] >= 0)){ 
									$benefitControlValue = isset($data[str_replace('child_','',$control_name)]) ? $data[str_replace('child_','',$control_name)] : 0;
									?>
				                  <div class="col-sm-3">
				                    <div class="form-group">
				                      <input type="text" name="<?=$control_name?>[<?= $number ?>][<?= $productID ?>]" id="<?=$control_name?>_<?= $productID ?>" class="form-control has-value" value="<?= $benefitControlValue ?>" readonly>
				                      <label><?= $productName ?> <?=ucwords(str_replace(array('child','_'),array('',' '),$control_name))?></label>
				                      <p class="error" id="error_<?=$control_name?>_<?= $productID ?>"></p>
				                    </div>
				                  </div>
				                <?php } ?> 
				              <?php } ?>
				            <?php } ?>
				          <?php } ?>
				        <?php } ?>
			      	<?php } ?>
		        <?php }else {
		        	$custom_name = str_replace($prd_question_id,"", $control_name);
		          $sqlAnswer="SELECT answer,answer_eligible FROM prd_enrollment_answers where prd_question_id=:prd_question_id AND is_deleted='N'";
		          $resAnswer=$pdo->select($sqlAnswer,array(":prd_question_id"=>$prd_question_id));
		          ?>
		          <div class="clearfix"></div>
		          
		          <?php if($control_type=='select'){ ?>
		           <div class="col-sm-12">
	           	    <p>
	          				<label><?= $label ?></label>
	                </p>
		              <div class="form-group height_auto w-300 custom_question">
		                <select id="<?= $control_name ?>_<?= $number ?>"  name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]" class="child_select child_select_<?= $number ?> child_member_field <?= $control_value != '' ? "has-value" : "" ?>" required data-live-search="true" data-id="<?= $number ?>">
		                  <option value=""></option>
		                  <?php if(!empty($resAnswer)){
		                    foreach ($resAnswer as $ansKey => $ansValue) { ?>
		                      <option value="<?= $ansValue['answer'] ?>" <?= ($control_value==$ansValue['answer'] ? 'selected=selected' : '') ?> data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
		                    <?php } ?>
		                  <?php } ?>
		                </select>
		                <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		                <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
		              </div>
		            </div>
		          <?php }else if($control_type=='radio'){ ?>
		            <div class="col-sm-12 m-b-25">
		              <p>
	              		<label><?= $label ?></label>
	             	  </p>
		              <div class="radio-button">
		                <div class="btn-group colors custom-question-btn" data-toggle="buttons">
		                  <?php if(!empty($resAnswer)){
		                    foreach ($resAnswer as $ansKey => $ansValue) { ?>
		                      <label class="btn btn-info <?= (!empty($control_value) && $control_value== $ansValue['answer'] ? 'active' : '') ?>">
		                            <input type="radio" name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]" value="<?= $ansValue['answer'] ?>" data-ans-eligible="<?= $ansValue['answer_eligible'] ?>" class="js-switch child_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value==$ansValue['answer'] ? 'checked' : '') ?> data-id="<?= $number ?>"> <?= $ansValue['answer'] ?>
		                          </label>
		                    <?php } ?>
		                  <?php } ?>
		                </div>
		              </div>
		              <p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
		            </div>
		          <?php }else if($control_type=='select_multiple'){ ?>
		          	<div class="col-sm-12">
			        			<p>
		                	<label><?= $label ?></label>
		              	</p>
		              	<div class="form-group height_auto w-300 custom_question">
		                	<select id="<?= $control_name ?>_<?= $number ?>"  name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>][]" class="se_multiple_select child_multiple_select child_multiple_select_<?= $number ?> child_member_field <?= $control_value != '' ? "has-value" : "" ?>" required multiple="multiple" data-id="<?= $number ?>">
		                  <?php if(!empty($resAnswer)){
		                    $tmp_control_value = explode(',',$control_value);
		                    foreach ($resAnswer as $ansKey => $ansValue) { ?>
		                      <option value="<?= $ansValue['answer'] ?>" <?= (is_array($tmp_control_value) && in_array($ansValue['answer'],$tmp_control_value) ? 'selected=selected' : '') ?> data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
		                    <?php } ?>
		                  <?php } ?>
		                	</select>
			                <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		                	<p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
		             	 </div>
		            </div>
				  <?php }else if($control_type=='textarea'){ ?>
							<div class="col-sm-12 form-inline m-b-25">
								<p>
									<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
								</p>
								<textarea id="<?= $control_name ?>_<?= $number ?>" class="form-control <?= $control_value != '' ? "has-value" : "" ?>" name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]"  rows="3" cols="50" maxlength="300" data-id="<?= $number ?>"><?= $control_value ?></textarea>
								<p class="error" id="error_<?= $control_name ?>_<?= $number ?>"></p>
							</div>
		          <?php } ?>
		        <?php } ?>
	    	<?php } ?>
  		</div>
	<?php } ?>
	</div>

	<?php

	$html = ob_get_clean();
	$response['html']=$html;
	$response['status']='success';
}else{
	$response['status']='fail';
}


echo json_encode($response);
dbConnectionClose();
exit;
?>