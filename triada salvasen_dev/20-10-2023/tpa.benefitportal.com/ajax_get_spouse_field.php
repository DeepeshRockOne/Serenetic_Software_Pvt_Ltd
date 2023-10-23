<?php include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
$MemberEnrollment = new MemberEnrollment();
$response = array();

$spouseData = isset($_POST['spouse']) ? $_POST['spouse'] : array();
$product_list = isset($_POST['spouse_products_list'])? explode(",", $_POST['spouse_products_list']):array();
$product_plan = isset($_POST['product_plan'])?$_POST['product_plan']:array();
$enrolleeElementsVal = !empty($_POST['enrolleeElementsVal'])? json_decode($_POST['enrolleeElementsVal'],true):array();

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
	$cust_dep_sql = "SELECT *,GROUP_CONCAT(product_id) as product_ids FROM customer_dependent WHERE cd_profile_id=:cd_profile_id AND order_id=:order_id GROUP BY cd_profile_id";
	$dep_row = $pdo->selectOne($cust_dep_sql,array(":cd_profile_id"=>$_GET['cd_profile_id'],":order_id"=>$_GET['order_id']));	

	if(!empty($dep_row)) {
		$spouse_fname_value = $dep_row["fname"];
		$spouse_lname_value = $dep_row["lname"];
		$spouse_SSN_value = $dep_row["ssn"];
		$spouse_email_value = $dep_row["email"];
		$spouse_birthdate_value = date("m/d/Y",strtotime($dep_row["birth_date"]));
		$spouse_gender_value = $dep_row["gender"];

		$dep_row["hire_date"] = (strtotime($dep_row["hire_date"]) > 0?date("m/d/Y",strtotime($dep_row["hire_date"])):'');
		$dep_row['product_ids'] = explode(',',$dep_row['product_ids']);

		$custom_que_sql = "SELECT ccq.answer,q.label,q.control_type
                      FROM customer_custom_questions ccq 
                      JOIN prd_enrollment_questions q ON(q.id = ccq.question_id AND q.is_deleted='N')
                      WHERE ccq.customer_id=:customer_id AND ccq.enrollee_type='spouse' AND ccq.dependent_id=:dependent_id AND ccq.is_deleted='N'";
	    $custom_que_res = $pdo->select($custom_que_sql,array(":customer_id"=>$dep_row["customer_id"],":dependent_id"=>$dep_row["id"]));
	    if(!empty($custom_que_res)) {
	        foreach ($custom_que_res as $custom_que_row) {
	        	${"spouse_".$custom_que_row['label']."_value"} = $custom_que_row['answer'];
	            
	        }
	    }

	}
}

$spouse_dep_row = array();
if(!empty($_POST['customer_id']) && !empty($_POST['is_add_product'])){
	$cust_dep_sql = "SELECT * FROM customer_dependent_profile WHERE customer_id=:customer_id AND relation in('Wife','Husband','wife','husband') AND is_deleted = 'N' GROUP BY id";
	$spouse_dep_row = $pdo->select($cust_dep_sql,array(":customer_id"=>$_POST['customer_id']));

}

if(!empty($enrolleeElementsVal['spouse_fname_1'])) {
	$spouse_fname_value = $enrolleeElementsVal['spouse_fname_1'];
} else {
	$spouse_fname_value = isset($spouse_fname_value)?$spouse_fname_value:'';
}

if(!empty($enrolleeElementsVal['spouse_email_1'])) {
	$spouse_email_value = $enrolleeElementsVal['spouse_email_1'];
} else {
	$spouse_email_value = isset($spouse_email_value)?$spouse_email_value:'';
}

if(!empty($enrolleeElementsVal['spouse_birthdate_1'])) {
	$spouse_birthdate_value = $enrolleeElementsVal['spouse_birthdate_1'];
} else {
	$spouse_birthdate_value = isset($spouse_birthdate_value)?$spouse_birthdate_value:'';
}

if(!empty($enrolleeElementsVal['spouse_gender_1'])) {
	$spouse_gender_value = $enrolleeElementsVal['spouse_gender_1'];
} else {
	$spouse_gender_value = isset($spouse_gender_value)?$spouse_gender_value:'';
}

$spouse_zip_value = isset($_POST['primary_zip'])? trim($_POST['primary_zip']):"";
$spouse_city_value = isset($zipCacheList[$spouse_zip_value])? $zipCacheList[$spouse_zip_value]['region_name']:"";

$number = 0;
$response['number']=$number;
$state_incr="";
if(!empty($spouse_zip_value)){
	$state_code=$zipCacheList[$spouse_zip_value]['state_code'];

	$getDetailOnPinCode=$pdo->selectOne("SELECT * FROM states_c WHERE country_id = '231' AND short_name=:short_name",array(":short_name"=>$state_code));

	if(!empty($getDetailOnPinCode)){
		$spouse_state_value = $getDetailOnPinCode['name'];
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

if(empty($spouse_lname_value)){
	$spouse_lname_value = !empty($_POST['primary_lname']) ? $_POST['primary_lname'] : '';
}


if(!empty($product_list)){
	$productIds = implode(",", $product_list);
	$productSql= "SELECT id,name,product_code FROM prd_main where id in ($productIds)";
	$productRes=$pdo->select($productSql);
	$spouse_field =$MemberEnrollment->get_spouse_field($product_list);
	$tmp_spouse_field = $spouse_field;
	ob_start();
	?>
	<div class="row enrollment_auto_row">
	   <div class="col-sm-6">
	      <div class="form-inline">
	         <div class="form-group">
	            <h5 class="mn">Spouse </h5>
	         </div>
	         <?php if($spouse_dep_row){ ?>
	         <div class="form-group m-l-15">
	            <select class="form-control" name="existing_spouse_dependent" id="existing_spouse_dependent" data-id="<?= $number ?>">
	               <option value="">Existing Spouse</option>
	               <?php 
	                  foreach ($spouse_dep_row as $s) { ?>
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
	         <a href="javascript:void(0);" class="red-link" id="removeSpouseField">Remove</a>
	      </div>
	   </div>
	</div>
 	<div class="row enrollment_auto_row">
 		<div class="col-sm-3">
	      <div class="form-group">
	      	<input type="hidden" name="spouse_cd_profile_id[<?= $number ?>]" id="spouse_cd_profile_id_<?= $number ?>" value="<?=!empty($_GET['cd_profile_id'])?$_GET['cd_profile_id']:'0'?>">
	      	<select id="que_spouse_assign_products_<?= $number ?>"  name="spouse_assign_products[<?= $number ?>][]" class="se_multiple_select spouse_dependent_multiple_select spouse_member_field " multiple="multiple" data-id="<?= $number ?>" >
	      		<?php if(!empty($productRes)) { ?>
	      			<?php foreach ($productRes as $key => $productRow) { ?>
						<option value="<?= $productRow['id'] ?>" <?= (!isset($dep_row['product_ids']) && !empty($product_plan) && $product_plan[$productRow['id']] != 5) || (!empty($dep_row['product_ids']) && in_array($productRow['id'],$dep_row['product_ids'])) ? 'selected' : ''?> <?= !empty($memberPlusOneProduct) && in_array($productRow['id'], $memberPlusOneProduct) ? 'disabled' : '' ?>><?= $productRow['name'].' ('.$productRow['product_code'] .')' ?></option>	      		
	      			<?php } ?>
	      		<?php } ?>
	      	</select>
	        <label>Assign Products<em>*</em></label>
	        <p class="error" id="error_spouse_assign_products"></p>
	      </div>
	    </div>
	    <?php if(array_key_exists('fname', $spouse_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <input type="text" name="spouse_fname[<?= $number ?>]" id="que_spouse_fname" class="form-control spouse_fname_1 <?= !empty($spouse_fname_value) ? 'has-value' : '' ?>" value="<?= !empty($spouse_fname_value) ? $spouse_fname_value : '' ?>">
		        <label>First Name<?php if($spouse_field['fname']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		        <p class="error" id="error_spouse_fname"></p>
		      </div>
		    </div>
		    <?php unset($spouse_field['fname']); ?>
		<?php } ?>
	    <?php if(array_key_exists('lname', $spouse_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <input type="text" name="spouse_lname[<?= $number ?>]" id="que_spouse_lname" class="form-control spouse_lname_1 spouse_last_name <?= !empty($spouse_lname_value) ? 'has-value' : '' ?>" value="<?= !empty($spouse_lname_value) ? $spouse_lname_value : '' ?>">
		        <label>Last Name<?php if($spouse_field['lname']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		        <p class="error" id="error_spouse_lname"></p>
		      </div>
		    </div>
		    <?php unset($spouse_field['lname']); ?>
	    <?php } ?>
	    <?php if(array_key_exists('email', $spouse_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <input type="text" name="spouse_email[<?= $number ?>]" id="que_spouse_email" class="form-control spouse_email_1 no_space <?= !empty($spouse_email_value) ? 'has-value' : '' ?>" value="<?= !empty($spouse_email_value) ? $spouse_email_value : '' ?>" <?= !empty($spouse_email_value) ? 'readonly' : '' ?>>
		        <label>Email<?php if($spouse_field['email']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		        <p class="error" id="error_spouse_email"></p>
		      </div>
		    </div>
		    <?php unset($spouse_field['email']); ?>
	    <?php } ?>
	    <?php if(array_key_exists('SSN', $spouse_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <input type="text" name="spouse_SSN[<?= $number ?>]" id="que_spouse_SSN" class="form-control spouse_SSN_1 <?= !empty($spouse_SSN_value) ? 'has-value' : '' ?> SSN_mask" value="<?= !empty($spouse_SSN_value) ? $spouse_SSN_value : '' ?>">
		        <label>SSN<?php if($spouse_field['SSN']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		        <p class="error" id="error_spouse_SSN"></p>
		      </div>
		    </div>
		    <?php unset($spouse_field['SSN']); ?>
		<?php } ?>
	    <?php if(array_key_exists('birthdate', $spouse_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <div class="input-group">
		          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
		          <div class="pr">
		            <input type="text" name="spouse_birthdate[<?= $number ?>]" id="que_spouse_birthdate" class="form-control spouse_birthdate_1 <?= !empty($spouse_birthdate_value) ? 'has-value' : '' ?> dob" value="<?= !empty($spouse_birthdate_value) ? $spouse_birthdate_value : '' ?>" <?= !empty($spouse_birthdate_value) ? 'readonly' : '' ?>>
		            <label>DOB<?php if($spouse_field['birthdate']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?> (MM/DD/YYYY)</label>
		          </div>
		        </div>
		        <p class="error" id="error_spouse_birthdate"></p>
		      </div>
		    </div>
		    <?php unset($spouse_field['birthdate']); ?>
	    <?php } ?>
	    <?php if(array_key_exists('gender', $spouse_field)) { ?>
		    <div class="col-sm-3">
		      <div class="form-group">
		        <div class="btn-group colors btn-group-justified <?= !empty($spouse_gender_value) ? 'btn-group-disabled' : '' ?>"" data-toggle="buttons">
		          <label class="btn btn-info <?= !empty($spouse_gender_value) && $spouse_gender_value=="Male" ? 'active' : '' ?>" <?= !empty($spouse_gender_value) ? 'disabled' : '' ?>>
		            <input type="radio" name="spouse_gender[<?= $number ?>]" value="Male" autocomplete="off" class="js-switch" <?= !empty($spouse_gender_value) ? 'readonly' : '' ?> <?= !empty($spouse_gender_value) && $spouse_gender_value=="Male" ? 'checked' : '' ?>> Male
		          </label>
		          <label class="btn btn-info <?= !empty($spouse_gender_value) && $spouse_gender_value=="Female" ? 'active' : '' ?>" <?= !empty($spouse_gender_value) ? 'disabled' : '' ?>>
		            <input type="radio" name="spouse_gender[<?= $number ?>]" value="Female" autocomplete="off" class="js-switch" <?= !empty($spouse_gender_value) ? 'readonly' : '' ?> <?= !empty($spouse_gender_value) && $spouse_gender_value=="Female" ? 'checked' : '' ?>> Female
		          </label>
		        </div>
		        <p class="error" id="error_spouse_gender"></p>
		      </div>
		    </div>
		    <?php unset($spouse_field['gender']); ?>
	    <?php } ?>
  	</div>
	<?php if(!empty($spouse_field)){ 
		$spouse_benefit_arr = array('spouse_benefit_amount','spouse_in_patient_benefit','spouse_out_patient_benefit','spouse_monthly_income','spouse_benefit_percentage'); 
		?>
		<div class="p-t-20 hidden-sm"></div>
	  	<p class="hline-title" ><span>Additional Spouse Information</span></p>
	  	<div class="row enrollment_auto_row">
	    	<?php foreach ($spouse_field as $key => $row) { ?>
		        <?php
					$prd_question_id = $row['id'];
					$is_required= $row['required'];
					$control_name = "spouse_".$row['label'];
					$label = $row['display_label'];
					$control_type = $row['control_type'];
					$class = $row['control_class'];
					$maxlength = $row['control_maxlength'];
					$control_attribute = $row['control_attribute'];
					$questionType = $row['questionType'];

					if(in_array($row['label'],array('fname','lname','SSN','email','birthdate','gender'))){
						continue;
					}

					$control_value=isset($enrolleeElementsVal[$control_name."_1"])?$enrolleeElementsVal[$control_name."_1"]:"";

					if(empty($control_value) && !empty(${$control_name.'_value'})){
						$control_value = ${$control_name.'_value'};
					}

					if(empty($control_value) && isset($dep_row[$control_name])) {
						$control_value = $dep_row[$control_name];
					}
		        ?>
		        <?php if($questionType=="Default"){ ?>
		          	<?php if($control_type=='text' && !in_array($control_name,$spouse_benefit_arr)){?>
	    				<div class="col-lg-3 col-md-6 col-sm-6">
	    					<div class="form-group">
	    						<input type="text" id="<?= $control_name ?>" maxlength="<?= $maxlength ?>" name="<?= $control_name ?>[<?= $number ?>]" value="<?= $control_value ?>" class="form-control spouse_member_field <?= $class ?> <?=  $control_value != '' ? "has-value" : "" ?>"  required <?=  !empty($control_value) ? "readonly" : "" ?>>
	    						<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
	    						<p class="error" id="error_<?= $control_name ?>"></p>
	    					</div>
	    				</div>
		          	<?php }else if($control_type=='date_mask' && !in_array($control_name,$spouse_benefit_arr)){ ?>
		          		<?php 
			                $dateValue='';
			                if ($control_value != ''){
			                  $dateValue = date('m/d/Y', strtotime($control_value));
			               	}
		          		?>
		          		<div class="col-lg-3 col-md-6 col-sm-6">
			                <div class="form-group">
			              		<input type="text" id="<?= $control_name ?>"  name="<?= $control_name ?>[<?= $number ?>]" value="<?= $dateValue ?>" class="form-control spouse_member_field <?= $dateValue != "" ? "has-value" : "" ?> <?= $class ?>"   required  <?=  !empty($control_value) ? "readonly" : "" ?>>
			  			            <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
			                  	<p class="error" id="error_<?= $control_name ?>"></p>
			               	</div>
		         	  	</div>
		          	<?php }else if($control_type=='select' && !in_array($control_name,$spouse_benefit_arr)){ ?>
		        		<div class="col-lg-3 col-md-6 col-sm-6">
			              	<div class="form-group">
				                <?php if(!empty($control_value)) { ?>
				                  <input type="text" id="<?= $control_name ?>" name="<?= $control_name ?>[<?= $number ?>]" value="<?= $control_value ?>" class="form-control spouse_member_field <?= $class ?> <?=  $control_value != '' ? "has-value" : "" ?>"  <?=  !empty($control_value) ? "readonly" : "" ?>>
				                <?php }else{ ?>
				              		<select id="<?= $control_name ?>"  name="<?= $control_name ?>[<?= $number ?>]" class="spouse_select spouse_member_field <?= $control_value != '' ? "has-value" : "" ?>" required data-live-search="true">
				              		<?php if($control_name=='spouse_state'){ ?>
				                      	<?php foreach ($state_res as $key => $value) { ?>
				                        	<option data-state_id="<?= $value["id"]; ?>" value="<?= $value["name"]; ?>" <?php echo $value["name"] == $control_value ? 'selected' : '' ?>><?php echo $value['name']; ?></option>
				                      	<?php } ?>
					                        
				                    <?php }else if(in_array($control_name,array('spouse_height'))){ ?>
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
			                	  	<?php }else if(in_array($control_name,array('spouse_weight'))){ ?>
				                  		<option value=""></option>
				    			            <?php for($i=1; $i<=1000;$i++){?>
				    			                <option value="<?= $i ?>" <?php echo $control_value == $i ? "selected='selected'" : '' ?>><?= $i ?></option>
				    			            <?php }?>
			                	  	<?php }else if(in_array($control_name,array('spouse_no_of_children'))){ ?>
				                  		<option value=""></option>
				                  		<?php for($i=1; $i<=15;$i++){?>
				                         <option value="<?=$i?>" <?php echo $control_value == $i ? "selected='selected'" : '' ?>><?= $i ?></option>
				                       <?php }?>
				                    <?php }else if(in_array($control_name,array('spouse_pay_frequency'))){ ?>
				                      <option value=""></option>
			                       	  <option value="Annual" <?php echo $control_value == "Annual" ? "selected='selected'" : '' ?>>Annual</option>
				                      <option value="Monthly" <?php echo $control_value == "Monthly" ? "selected='selected'" : '' ?>>Monthly</option>
				                      <option value="Semi-Monthly" <?php echo $control_value == "Semi-Monthly" ? "selected='selected'" : '' ?>>Semi-Monthly</option>
				                      <option value="Semi-Weekly" <?php echo $control_value == "Semi-Weekly" ? "selected='selected'" : '' ?>>Semi-Weekly</option>
				                      <option value="Weekly" <?php echo $control_value == "Weekly" ? "selected='selected'" : '' ?>>Weekly</option>
				                      <option value="Hourly" <?php echo $control_value == "Hourly" ? "selected='selected'" : '' ?>>Hourly</option>
			                	  	<?php }else if(in_array($control_name,array('spouse_benefit_percentage'))){ ?>
										<option value=""></option>
										<?php for($i=1; $i<=100;$i++){?>
											<option value="<?=$i?>"><?= $i ?></option>
										<?php }?>
									<?php } ?>
				                	</select>
				                <?php } ?>
				              	<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
				            	<p class="error" id="error_<?= $control_name ?>"></p>
		              		</div>
		          		</div>
		          	<?php }else if($control_type=='radio' && !in_array($control_name,$spouse_benefit_arr)){ ?>
			          	<div class="col-lg-3 col-md-6 col-sm-6">
			              <div class="form-group">
			                <div class="btn-group colors btn-group-justified <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
		            			<?php if($control_name=='spouse_smoking_status'){ ?>
		            				<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
		  	                      		<input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="Y" class="js-switch spouse_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>> Smokes
		  	                    	</label>
		  	                    	<label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
		  	                      		<input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="N" class="js-switch spouse_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>> Non Smokes
		  	                    	</label>
		            			<?php }else if($control_name=='spouse_tobacco_status'){ ?>
		            				<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
		  	                      		<input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="Y" class="js-switch spouse_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?>> Tobacco
	  	                    		</label>
		  	                    	<label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
		  	                      		<input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="N" class="js-switch spouse_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>> Non Tobacco
		  	                    	</label>
		            			<?php }else if($control_name=='spouse_has_spouse'){ ?>
		            				<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
		  	                      		<input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="Y" class="js-switch spouse_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?>> Spouse
		  	                    	</label>
		  	                    	<label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
		  	                      		<input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="N" class="js-switch spouse_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>> No Spouse
		  	                    	</label>
		                  		<?php }else if($control_name == 'spouse_employment_status'){ ?>
		                  			<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
		                        		<input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="Y" class="js-switch spouse_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?>> Employed
		                      		</label>
		                      		<label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
		                        		<input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="N" class="js-switch spouse_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>>  Unemployed
		                      		</label>
		                  		<?php }else if($control_name == 'spouse_us_citizen'){ ?>
		                  			<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
		  	                      		<input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="Y" class="js-switch spouse_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?>> U.S. Citizen
		                    		</label>
		                    		<label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
		                        		<input type="radio" name="<?= $control_name ?>[<?= $number ?>]" value="N" class="js-switch spouse_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>> Not  U.S. Citizen
		                    		</label>
		            			<?php } ?>
			                </div>
			                <p class="error" id="error_<?= $control_name ?>"></p>
			              </div>
			          	</div>
		          	<?php }?>

		          	<?php if(in_array($control_name,$spouse_benefit_arr)) { ?>
				        <?php if(!empty($spouseData)) { ?>
				          <?php foreach ($spouseData as $productID => $dataArr) { ?>
				            <?php $productName=getname('prd_main',$productID,'name','id'); ?>
				            <?php if(!empty($dataArr)) { ?>
				              <?php foreach ($dataArr as $dataArrkey => $data) { ?>
				                <?php if((isset($data[str_replace('spouse_','',$control_name)]) && $data[str_replace('spouse_','',$control_name)] >= 0)){ 
									$benefitControlValue = isset($data[str_replace('spouse_','',$control_name)]) ? $data[str_replace('spouse_','',$control_name)] : 0;
									?>
				                  <div class="col-sm-3">
				                    <div class="form-group">
				                      <input type="text" name="<?=$control_name?>[<?= $number ?>][<?= $productID ?>]" id="<?=$control_name?>_<?= $productID ?>" class="form-control has-value" value="<?= $benefitControlValue ?>" readonly>
				                      <label><?= $productName ?> <?=ucwords(str_replace(array('spouse','_'),array('',' '),$control_name))?></label>
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
		            <div class="col-sm-12 form-inline">
	           	      <div class="form-group height_auto m-r-15">
		             		 <label><?= $label ?></label>
		              </div>
		              <div class="form-group height_auto w-300 custom_question">
		                <select id="<?= $control_name ?>"  name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]" class="spouse_select spouse_member_field <?= $control_value != '' ? "has-value" : "" ?>" required data-live-search="true">
		                  <option value=""></option>
		                  <?php if(!empty($resAnswer)){
		                    foreach ($resAnswer as $ansKey => $ansValue) { ?>
		                      <option value="<?= $ansValue['answer'] ?>" <?= ($control_value==$ansValue['answer'] ? 'selected=selected' : '') ?> data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
		                    <?php } ?>
		                  <?php } ?>
		                </select>
		                <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		                <p class="error" id="error_<?= $control_name ?>"></p>
		              </div>
		            </div>
		          <?php }else if($control_type=='radio'){ ?>
		             <div class="col-sm-12 form-inline">
		               <div class="form-group height_auto m-r-15">
	                      <label><?= $label ?></label>
	                   </div>
		              <div class="form-group height_auto">
		                <div class="btn-group colors custom-question-btn" data-toggle="buttons">
		                  <?php if(!empty($resAnswer)){
		                    foreach ($resAnswer as $ansKey => $ansValue) { ?>
		                      <label class="btn btn-info <?= (!empty($control_value) && $control_value== $ansValue['answer'] ? 'active' : '') ?>">
		                            <input type="radio" name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]" value="<?= $ansValue['answer'] ?>" data-ans-eligible="<?= $ansValue['answer_eligible'] ?>" class="js-switch spouse_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value==$ansValue['answer'] ? 'checked' : '') ?> > <?= $ansValue['answer'] ?>
		                          </label>
		                    <?php } ?>
		                  <?php } ?>
		                </div>
		                <p class="error" id="error_<?= $control_name ?>"></p>
		              </div>
		            </div>
		          <?php }else if($control_type=='select_multiple'){ ?>
		           <div class="col-sm-12 form-inline">
		           	 <div class="form-group  m-r-15">
	              		<label><?= $label ?></label>
	                 </div>
		              <div class="form-group  w-300 custom_question">
		                <select id="<?= $control_name ?>"  name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>][]" class="se_multiple_select spouse_multiple_select spouse_member_field <?= $control_value != '' ? "has-value" : "" ?>" required multiple="multiple">
		                  <?php if(!empty($resAnswer)){
		                    $tmp_control_value = explode(',',$control_value);
		                    foreach ($resAnswer as $ansKey => $ansValue) { ?>
		                      <option value="<?= $ansValue['answer'] ?>" <?= (is_array($tmp_control_value) && in_array($ansValue['answer'],$tmp_control_value) ? 'selected=selected' : '') ?> data-ans-eligible="<?= $ansValue['answer_eligible'] ?>"><?= $ansValue['answer'] ?></option>
		                    <?php } ?>
		                  <?php } ?>
		                </select>
		                <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
		                <p class="error" id="error_<?= $control_name ?>"></p>
		              </div>
		            </div>
				  <?php }else if($control_type=='textarea'){ ?>
							<div class="col-sm-12 form-inline m-b-25">
								<div class="form-group  m-r-15">
									<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
								</div>
								<textarea id="<?= $control_name ?>" class="form-control <?= $control_value != '' ? "has-value" : "" ?>" name="<?= $custom_name ?>[<?= $number ?>][<?= $prd_question_id ?>]"  rows="3" cols="50" maxlength="300"><?= $control_value ?></textarea>
								<p class="error" id="error_<?= $control_name ?>"></p>
							</div>
		          <?php } ?>
		        <?php } ?>
		    <?php } ?>
	  	</div>

	<?php } ?>
	<?php
	$html = ob_get_clean();
	$response['html']=$html;
	$response['status']='success';
	$response['spouse_field']=$tmp_spouse_field;
}else{
	$response['status']='fail';
}


echo json_encode($response);
dbConnectionClose();
exit;
?>