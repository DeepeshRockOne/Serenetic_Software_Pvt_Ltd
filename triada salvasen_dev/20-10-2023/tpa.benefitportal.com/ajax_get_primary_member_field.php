<?php include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
include_once __DIR__ . '/includes/function.class.php';
$MemberEnrollment = new MemberEnrollment();
$function_list = new functionsList();
$response = array();

$is_address_verified = isset($_POST['is_address_verified'])?$_POST['is_address_verified']:"";
$is_valid_address = isset($_POST['is_valid_address'])?$_POST['is_valid_address']:"";
$product_list = !empty($_POST['product_list'])? explode(",",$_POST['product_list']):array();
$product_list_without_waive = $product_list;
$enrolleeElementsVal = !empty($_POST['enrolleeElementsVal'])? json_decode($_POST['enrolleeElementsVal'],true):array();
$primaryData = isset($_POST['primary']) ? $_POST['primary'] : array();
$customer_id = isset($_POST['customer_id'])?$_POST['customer_id']:0;
$lead_id = isset($_POST['lead_id'])?$_POST['lead_id']:0;
$enrollmentLocation = isset($_POST['enrollmentLocation'])?$_POST['enrollmentLocation']:"";
$is_group_member = isset($_POST['is_group_member'])?$_POST['is_group_member']:"N";
$sponsor_id = isset($_POST['sponsor_id'])?$_POST['sponsor_id']:"";
$group_company_res = array();

if($product_list){
  foreach ($product_list as $k => $p_id) {
    if(isset($_POST['monthly_benefit_amount_'.$p_id])){
      $primaryData[$p_id]['benefit_amount'] = $_POST['monthly_benefit_amount_'.$p_id];
    }
  }
}
if($enrollmentLocation=='groupSide' || $is_group_member == "Y"){

  $waive_checkbox = !empty($_POST['waive_checkbox']) ? $_POST['waive_checkbox'] : '';

  $sponsorRes=$pdo->selectOne("SELECT fname,lname,business_name FROM customer where id=:sponsor_id",array(":sponsor_id"=>$sponsor_id));

  $group_cmp_res = $pdo->select("SELECT id,name,location FROM group_company where group_id = :id AND is_deleted = 'N'",array(':id' => $sponsor_id));

  if(!empty($waive_checkbox)){

    $group_waive_product=isset($_POST['waive_products'])?$_POST['waive_products']:array();

    if(!empty($group_waive_product)){
      $group_product_list=$MemberEnrollment->getGroupWaiveProductList($waive_checkbox,$group_waive_product);
      $product_list = array_merge($product_list,$group_product_list);
    }
    
  }

  if(empty($group_company_res)){
    $group_company_res[0]['id'] = 0;
    $group_company_res[0]['name'] = $sponsorRes['business_name'];
    $group_company_res[0]['location'] = ''; 
  }

  if(count($group_cmp_res)){
    foreach ($group_cmp_res as $key => $value) {
      $group_company_res[$key+1] = $value;    
    }
  }
}

if(!empty($lead_id)) {
    $lead_sql = "SELECT group_company_id,fname,lname,email,cell_phone,address,address2,AES_DECRYPT(ssn_itin_num,'" . $CREDIT_CARD_ENC_KEY . "') as ssn FROM leads WHERE id=:id";
    $lead_res = $pdo->selectOne($lead_sql, array(":id" => $lead_id));
    if(!empty($lead_res)) {
        $primary_fname_value = $lead_res['fname'];
        $primary_lname_value = $lead_res['lname'];
        $primary_email_value = $lead_res['email'];
        $primary_phone_value = $lead_res['cell_phone'];
        $primary_address_value = $lead_res['address'];
        $primary_address2_value = $lead_res['address2'];
        $primary_SSN_value = $lead_res['ssn'];
        $primary_group_company_id = $lead_res['group_company_id'];
    }
}

if(!empty($customer_id)){
  $sqlCustomer="SELECT c.fname,c.lname,c.email,c.cell_phone,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn,c.address,c.address_2,c.city,c.state,c.zip,c.birth_date,c.gender,cs.salary,cs.employmentStatus,cs.tobacco_use,cs.smoke_use,cs.height_feet,cs.height_inch,cs.weight,cs.benefit_level,cs.hours_per_week,cs.pay_frequency,cs.us_citizen,cs.no_of_children,cs.has_spouse,cs.hire_date,c.group_company_id,cs.is_valid_address,cs.is_address_verified
      FROM customer c 
      LEFT JOIN customer_settings cs ON (c.id = cs.customer_id)
      WHERE c.id=:customer_id";
  $resCustomer=$pdo->selectOne($sqlCustomer,array(":customer_id"=>$customer_id));

  if($resCustomer){
    $primary_fname_value = $resCustomer['fname'];
    $primary_lname_value = $resCustomer['lname'];
    $primary_email_value = $resCustomer['email'];
    $primary_phone_value = $resCustomer['cell_phone'];
    $primary_SSN_value = $resCustomer['ssn'];
    $primary_address_value = $resCustomer['address'];
    $primary_address2_value = $resCustomer['address_2'];
    $primary_city_value = $resCustomer['city'];
    $primary_state_value = $resCustomer['state'];
    $primary_zip_value = $resCustomer['zip'];
    $primary_group_company_id = $resCustomer['group_company_id'];
    $primary_birthdate_value = date('m/d/Y',strtotime($resCustomer['birth_date']));
    $primary_gender_value = $resCustomer['gender'];
    $primary_salary_value = $resCustomer['salary'];
    $primary_employment_status_value = $resCustomer['employmentStatus'];
    $primary_tobacco_status_value = $resCustomer['tobacco_use'];
    $primary_smoking_status_value = $resCustomer['smoke_use'];
    $primary_height_feet_value = $resCustomer['height_feet'];
    $primary_height_inch_value = $resCustomer['height_inch'];
    $primary_height_value = (!empty($primary_height_feet_value) || !empty($primary_height_inch_value)) ? $primary_height_feet_value.'.'.$primary_height_inch_value : '';
    $primary_weight_value = $resCustomer['weight'];
    $primary_benefit_level_value = $resCustomer['benefit_level'];
    $primary_hours_per_week_value = $resCustomer['hours_per_week'];
    $primary_pay_frequency_value = $resCustomer['pay_frequency'];
    $primary_us_citizen_value = $resCustomer['us_citizen'];
    $primary_no_of_children_value = $resCustomer['no_of_children'];
    $primary_has_spouse_value = $resCustomer['has_spouse'];
    $is_address_verified = $resCustomer['is_address_verified'];
    $is_valid_address = $resCustomer['is_address_verified'];
    $primary_date_of_hire_value = !empty($resCustomer['hire_date']) ? date('m/d/Y',strtotime($resCustomer['hire_date'])) : '';

  
    $custom_que_sql = "SELECT ccq.answer,q.label,q.control_type
                      FROM customer_custom_questions ccq 
                      JOIN prd_enrollment_questions q ON(q.id = ccq.question_id)
                      WHERE ccq.customer_id=:customer_id AND ccq.enrollee_type='primary' AND ccq.is_deleted='N'";
    $custom_que_res = $pdo->select($custom_que_sql,array(":customer_id"=>$customer_id));
    if(!empty($custom_que_res)) {
        foreach ($custom_que_res as $custom_que_row) {
            ${"primary_".$custom_que_row['label']."_value"} = $custom_que_row['answer'];
        }
    }
  
  }
}

if(!empty($_POST['primary_fname'])) {
  $primary_fname_value = trim($_POST['primary_fname']);
} else {
  $primary_fname_value = isset($primary_fname_value)?trim($primary_fname_value):'';
}

if(!empty($_POST['primary_email'])) {
  $primary_email_value = trim($_POST['primary_email']);
} else {
  $primary_email_value = isset($primary_email_value)?trim($primary_email_value):'';
}

if(!empty($_POST['primary_zip'])) {
  $primary_zip_value = trim($_POST['primary_zip']);
} else {
  $primary_zip_value = isset($primary_zip_value)?trim($primary_zip_value):'';
}

if(!empty($_POST['primary_birthdate'])) {
  $primary_birthdate_value = $_POST['primary_birthdate'];
} else {
  $primary_birthdate_value = isset($primary_birthdate_value)?$primary_birthdate_value:'';
}

if(!empty($_POST['primary_gender'])) {
  $primary_gender_value = $_POST['primary_gender'];
} else {
  $primary_gender_value = isset($primary_gender_value)?$primary_gender_value:'';
}

$primary_city_value = isset($primary_city_value)?$primary_city_value:'';

if(!empty($primary_zip_value)){
  $city_response = $function_list->uspsCityVerification($primary_zip_value);
  if(!empty($city_response) && $city_response['status']=='success'){
    $primary_city_value = ucwords(strtolower($city_response['city']));
  }
}


$state_incr="";
if(!empty($primary_zip_value)){
    $state_code=$zipCacheList[$primary_zip_value]['state_code'];
    $getDetailOnPinCode=$pdo->selectOne("SELECT * FROM states_c WHERE country_id = '231' AND short_name=:short_name",array(":short_name"=>$state_code));

    if(!empty($getDetailOnPinCode)){
       $primary_state_value = $getDetailOnPinCode['name'];
    }
}


if(!empty($product_list)){
  $primary_member_field =$MemberEnrollment->get_primary_member_field($product_list_without_waive);
  $is_ssn_exists = array_key_exists('SSN',$primary_member_field);
  ob_start();
  ?>
  <h4 class="m-b-25 m-t-0" id="title_primary_contact">Primary Information</h4>
  <input type="hidden" name="is_address_verified" id="is_address_verified" value="<?= !empty($is_address_verified) ? $is_address_verified : 'N' ?>">
  <input type="hidden" name="is_valid_address" id="is_valid_address" value="<?= !empty($is_valid_address) ? $is_valid_address : 'N' ?>">
  <div class="row enrollment_auto_row">
    <?php if(array_key_exists('fname',$primary_member_field)) { ?>
      <div class="<?= $is_ssn_exists ? 'col-sm-3' : 'col-sm-4'?>">
        <div class="form-group">
          <input type="text" name="primary_fname" id="que_primary_fname" class="form-control primary_fname_1 <?= !empty($primary_fname_value) ? 'has-value' : '' ?>" value="<?= !empty($primary_fname_value) ? $primary_fname_value : '' ?>">
          <label>First Name<?php if($primary_member_field['fname']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
          <p class="error" id="error_primary_poliy_holder"></p>
          <p class="error" id="error_primary_fname"></p>
        </div>
      </div>
      <?php unset($primary_member_field['fname']); ?>
    <?php } ?>
    <?php if(array_key_exists('lname',$primary_member_field)) { ?>
      <div class="<?= $is_ssn_exists ? 'col-sm-3' : 'col-sm-4'?>">
        <div class="form-group">
          <input type="text" name="primary_lname" id="que_primary_lname" class="form-control primary_lname_1 <?= !empty($primary_lname_value) ? 'has-value' : '' ?>" value="<?= !empty($primary_lname_value) ? $primary_lname_value : '' ?>">
          <label>Last Name<?php if($primary_member_field['lname']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
          <p class="error" id="error_primary_lname"></p>
        </div>
      </div>
      <?php unset($primary_member_field['lname']); ?>
    <?php } ?>
    <?php if(array_key_exists('SSN',$primary_member_field)) { ?>
      <div class="col-sm-3">
        <div class="form-group">
          <input type="text" name="primary_SSN" id="que_primary_SSN" class="form-control primary_SSN_1 <?= !empty($primary_SSN_value) ? 'has-value' : '' ?> SSN_mask" value="<?= !empty($primary_SSN_value) ? $primary_SSN_value : '' ?>">
          <label>SSN<?php if($primary_member_field['SSN']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
          <p class="error" id="error_primary_SSN"></p>
        </div>
      </div>
      <?php unset($primary_member_field['SSN']); ?>
    <?php } ?>
    <?php if(array_key_exists('phone',$primary_member_field)) { ?>
      <div class="<?= $is_ssn_exists ? 'col-sm-3' : 'col-sm-4'?>">
        <div class="form-group">
          <input type="text" name="primary_phone" id="que_primary_phone" class="form-control primary_phone_1 phone_mask <?= !empty($primary_phone_value) ? 'has-value' : '' ?>" value="<?= !empty($primary_phone_value) ? $primary_phone_value : '' ?>">
          <label>Phone<?php if($primary_member_field['phone']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
          <p class="error" id="error_primary_phone"></p>
        </div>
      </div>
      <?php unset($primary_member_field['phone']); ?>
    <?php } ?>
    <?php if(array_key_exists('address1',$primary_member_field)) { ?>
      <div class="col-sm-6">
        <div class="form-group">
          <input type="text" name="primary_address1" id="que_primary_address1" class="form-control primary_address_1 <?= !empty($primary_address_value) ? 'has-value' : '' ?>" value="<?= !empty($primary_address_value) ? $primary_address_value : '' ?>" placeholder="">
          <label>Address<?php if($primary_member_field['address1']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
          <p class="error" id="error_primary_address1"></p>
        </div>
      </div>
      <?php unset($primary_member_field['address1']); ?>
    <?php } ?>
    <?php if(array_key_exists('address2',$primary_member_field)) { ?>
      <div class="col-sm-6">
        <div class="form-group">
          <input type="text" name="primary_address2" id="que_primary_address2" class="form-control primary_address_2 <?= !empty($primary_address2_value) ? 'has-value' : '' ?>" value="<?= !empty($primary_address2_value) ? $primary_address2_value : '' ?>" placeholder="" onkeypress="return block_special_char(event)">
          <label>Address 2 (suite, apt) <?php if($primary_member_field['address2']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
          <p class="error" id="error_primary_address2"></p>
        </div>
      </div>
      <?php unset($primary_member_field['address2']); ?>
    <?php } ?>
    <?php if(array_key_exists('city',$primary_member_field)) { ?>
      <div class="col-sm-2">
        <div class="form-group">
          <input type="text" name="primary_city" id="que_primary_city" class="form-control primary_city_1 <?= !empty($primary_city_value) ? 'has-value' : '' ?>" value="<?= !empty($primary_city_value) ? $primary_city_value : '' ?>" <?= !empty($primary_city_value) ? 'readonly' : '' ?>>
          <label>City<?php if($primary_member_field['city']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
          <p class="error" id="error_primary_city"></p>
        </div>
      </div>
      <?php unset($primary_member_field['city']); ?>
    <?php } ?>
    <?php if(array_key_exists('state',$primary_member_field)) { ?>
      <div class="col-sm-2">
        <div class="form-group">
          <input type="text" name="primary_state" id="que_primary_state" class="form-control primary_state_1 <?= !empty($primary_state_value) ? 'has-value' : '' ?>" value="<?= !empty($primary_state_value) ? $primary_state_value : '' ?>" <?= !empty($primary_state_value) ? 'readonly' : '' ?>>
          <label>State<?php if($primary_member_field['state']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
          <p class="error" id="error_primary_state"></p>
        </div>
      </div>
      <?php unset($primary_member_field['state']); ?>
    <?php } ?>
    <?php if(array_key_exists('zip',$primary_member_field)) { ?>
      <div class="col-sm-2">
        <div class="form-group">
          <input type="text" name="primary_zip" id="que_primary_zip" class="form-control primary_zip_1 <?= !empty($primary_zip_value) ? 'has-value' : '' ?>" value="<?= !empty($primary_zip_value) ? $primary_zip_value : '' ?>" <?= !empty($primary_zip_value) ? 'readonly' : '' ?>>
          <label>Zip Code<?php if($primary_member_field['zip']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
          <p class="error" id="error_primary_zip"></p>
        </div>
      </div>
      <?php unset($primary_member_field['zip']); ?>
    <?php } ?>
    <?php if(array_key_exists('email',$primary_member_field)) { ?>
      <div class="col-sm-3">
        <div class="form-group">
          <input type="text" name="primary_email" id="que_primary_email" class="form-control no_space primary_email_1 <?= !empty($primary_email_value) ? 'has-value' : '' ?>" value="<?= !empty($primary_email_value) ? $primary_email_value : '' ?>">
          <label>Email<?php if($primary_member_field['email']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
          <p class="error" id="error_primary_email2"></p>
        </div>
      </div>
      <?php unset($primary_member_field['email']); ?>
    <?php } ?>
    <?php if(array_key_exists('birthdate',$primary_member_field)) { ?>
      <div class="col-sm-3">
        <div class="form-group">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <div class="pr">
              <input type="text" name="primary_birthdate" id="que_primary_birthdate" class="form-control primary_birthdate_1 <?= !empty($primary_birthdate_value) ? 'has-value' : '' ?> dob" value="<?= !empty($primary_birthdate_value) ? $primary_birthdate_value : '' ?>" <?= !empty($primary_birthdate_value) ? 'readonly' : '' ?>>
              <label>DOB<?php if($primary_member_field['birthdate']['required'] == 'Y') { ?><span class="req-indicator">*</span><?php } ?> (MM/DD/YYYY)</label>
              <p class="error" id="error_primary_birthdate"></p>
            </div>
          </div>
        </div>
      </div>
      <?php unset($primary_member_field['birthdate']); ?>
    <?php } ?>
    <?php if(array_key_exists('gender',$primary_member_field)) { ?>
      <div class="col-sm-3">
        <div class="form-group">
          <div class="btn-group colors btn-group-justified <?= !empty($primary_gender_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
            <label class="btn btn-info <?= !empty($primary_gender_value) && $primary_gender_value=="Male" ? 'active' : '' ?>" <?= !empty($primary_gender_value) ? 'disabled' : '' ?>>
              <input type="radio" name="primary_gender" value="Male" autocomplete="off" class="js-switch" <?= !empty($primary_gender_value) ? 'readonly' : '' ?>> Male
            </label>
            <label class="btn btn-info <?= !empty($primary_gender_value) && $primary_gender_value=="Female" ? 'active' : '' ?>" <?= !empty($primary_gender_value) ? 'disabled' : '' ?>>
              <input type="radio" name="primary_gender" value="Female" autocomplete="off" class="js-switch" <?= !empty($primary_gender_value) ? 'readonly' : '' ?>> Female
            </label>
          </div>
          <p class="error" id="error_primary_gender"></p>
        </div>
      </div>
      <?php unset($primary_member_field['gender']); ?>
    <?php } ?>
    <?php if(!empty($group_company_res)) { ?>
      <div class="col-sm-3">
        <div class="form-group">
          <select id="group_company_id"  name="group_company_id" class="form-control primary_select primary_group_company_id_1 <?= isset($primary_group_company_id) ? "has-value" : "" ?>" >
            <option value=""></option>
            <?php foreach ($group_company_res as $key => $value) { ?>
                <option value="<?=$value['id']?>" <?= (isset($primary_group_company_id) && $primary_group_company_id == $value['id'] ? 'selected' : '') ?>><?=$value['name']?></option>
            <?php } ?>
          </select>
          <label>Group Company <span class="req-indicator">*</span></label>
          <p class="error" id="error_group_company_id"></p>
        </div>
      </div>
    <?php } ?>
  </div>
	<?php if(!empty($primary_member_field)){ 
      $primary_benefit_arr = array('primary_benefit_amount','primary_in_patient_benefit','primary_out_patient_benefit','primary_monthly_income','primary_benefit_percentage');  
    ?>
		<div class="p-t-20 hidden-sm"></div>
  	<p class="hline-title" ><span>Additional Primary Information</span></p>
  	<div class="row enrollment_auto_row">
	    <?php foreach ($primary_member_field as $key => $row) { ?>
        <?php
          $prd_question_id = $row['id'];
          $is_required= $row['required'];
          $control_name = "primary_".$row['label'];
          $label = $row['display_label'];
          $control_type = $row['control_type'];
          $class = $row['control_class'];
          $maxlength = $row['control_maxlength'];
          $control_attribute = $row['control_attribute'];
          $questionType = $row['questionType'];

          if(in_array($row['label'],array('fname','lname','SSN','phone','address1','city','state','zip','email','birthdate','gender'))){
            continue;
          }
          
          
          $control_value=isset($enrolleeElementsVal[$control_name."_1"])?$enrolleeElementsVal[$control_name."_1"]:"";
          
          if(empty($control_value) && !empty(${$control_name.'_value'})){
          	$control_value = ${$control_name.'_value'};
          }
        ?>
        <?php if($questionType=="Default"){ ?>
          <?php if($control_type=='text' && !in_array($control_name,$primary_benefit_arr)){?>
    				<div class="col-lg-3 col-md-6 col-sm-6">
    					<div class="form-group">
    						<input type="text" id="<?= $control_name ?>" maxlength="<?= $maxlength ?>" name="<?= $control_name ?>" value="<?= $control_value ?>" class="form-control primary_member_field <?= $class ?> <?=  $control_value != '' ? "has-value" : "" ?>"  required <?=  !empty($control_value) ? "readonly" : "" ?>>
    						<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
    						<p class="error" id="error_<?= $control_name ?>"></p>
    					</div>
    				</div>
          <?php }else if($control_type=='date_mask' && !in_array($control_name,$primary_benefit_arr)){ ?>
          	<?php 
                $dateValue='';
                if ($control_value != ''){
                  $dateValue = date('m/d/Y', strtotime($control_value));
               	}
          	?>
          	<div class="col-lg-3 col-md-6 col-sm-6">
                <div class="form-group">
              		<input type="text" id="<?= $control_name ?>"  name="<?= $control_name ?>" value="<?= $dateValue ?>" class="form-control primary_member_field <?= $dateValue != "" ? "has-value" : "" ?> <?= $class ?>"   required  <?=  !empty($control_value) ? "readonly" : "" ?>>
  			            <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                  	<p class="error" id="error_<?= $control_name ?>"></p>
               	</div>
         	  </div>
          <?php }else if($control_type=='select' && !in_array($control_name,$primary_benefit_arr)){ ?>
        		<div class="col-lg-3 col-md-6 col-sm-6">
              <div class="form-group">
                <?php if(!empty($control_value)) { ?>
                  <input type="text" id="<?= $control_name ?>" name="<?= $control_name ?>" value="<?= $control_value ?>" class="form-control primary_member_field <?= $class ?> <?=  $control_value != '' ? "has-value" : "" ?>"  <?=  !empty($control_value) ? "readonly" : "" ?>>
                <?php }else{ ?>
              		<select id="<?= $control_name ?>"  name="<?= $control_name ?>" class="primary_select primary_member_field <?= $control_value != '' ? "has-value" : "" ?>" required data-live-search="true">
              			
                    <?php if(in_array($control_name,array('primary_height'))){ ?>
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
                	  <?php }else if(in_array($control_name,array('primary_weight'))){ ?>
                  		<option value=""></option>
    			            <?php for($i=1; $i<=1000;$i++){?>
    			                <option value="<?= $i ?>"><?= $i ?></option>
    			            <?php }?>
                	  <?php }else if(in_array($control_name,array('primary_no_of_children'))){ ?>
                  		<option value=""></option>
                  		<?php for($i=1; $i<=15;$i++){?>
                         <option value="<?=$i?>"><?= $i ?></option>
                       <?php }?>
                	  <?php }else if(in_array($control_name,array('primary_pay_frequency'))){ ?>
                      <option value=""></option>
                      <option value="Annual">Annual</option>
                      <option value="Monthly">Monthly</option>
                      <option value="Semi-Monthly">Semi-Monthly</option>
                      <option value="Semi-Weekly">Semi-Weekly</option>
                      <option value="Weekly">Weekly</option>
                      <option value="Hourly">Hourly</option>
                    <?php } ?>
                	</select>
                <?php } ?>
              	<label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
            	  <p class="error" id="error_<?= $control_name ?>"></p>
              </div>
          	</div>
          <?php }else if($control_type=='radio' && !in_array($control_name,$primary_benefit_arr)){ ?>
          	<div class="col-lg-3 col-md-6 col-sm-6">
              <div class="form-group">
                <div class="btn-group colors btn-group-justified <?= !empty($control_value) ? 'btn-group-disabled' : '' ?>" data-toggle="buttons">
            			<?php if($control_name=='primary_smoking_status'){ ?>
            				<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
  	                      <input type="radio" name="<?= $control_name ?>" value="Y" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>> Smokes
  	                    </label>
  	                    <label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
  	                      <input type="radio" name="<?= $control_name ?>" value="N" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>> Non Smokes
  	                    </label>
            			<?php }else if($control_name=='primary_tobacco_status'){ ?>
            				<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
  	                      <input type="radio" name="<?= $control_name ?>" value="Y" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?>> Tobacco
  	                    </label>
  	                    <label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
  	                      <input type="radio" name="<?= $control_name ?>" value="N" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>> Non Tobacco
  	                    </label>
  	                    
            			<?php }else if($control_name=='primary_has_spouse'){ ?>
            				<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
  	                      <input type="radio" name="<?= $control_name ?>" value="Y" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?>> Spouse
  	                    </label>
  	                    <label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
  	                      <input type="radio" name="<?= $control_name ?>" value="N" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>> No Spouse
  	                    </label>
                  <?php }else if($control_name == 'primary_employment_status'){ ?>
                  	<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
                        <input type="radio" name="<?= $control_name ?>" value="Y" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?>> Employed
                      </label>
                      <label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
                        <input type="radio" name="<?= $control_name ?>" value="N" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>>  Unemployed
                      </label>
                  <?php }else if($control_name == 'primary_us_citizen'){ ?>
                  	<label class="btn btn-info <?= (!empty($control_value) && $control_value=='Y' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
  	                      <input type="radio" name="<?= $control_name ?>" value="Y" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='Y' ? 'checked' : '') ?>  <?=  !empty($control_value) ? "readonly" : "" ?>> U.S. Citizen
                    </label>
                    <label class="btn btn-info <?= (!empty($control_value) && $control_value=='N' ? 'active' : '') ?>" <?=  !empty($control_value) ? "disabled" : "" ?>>
                        <input type="radio" name="<?= $control_name ?>" value="N" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value=='N' ? 'checked' : '') ?> <?=  !empty($control_value) ? "readonly" : "" ?>> Not  U.S. Citizen
                    </label>
            			<?php } ?>
                </div>
                <p class="error" id="error_<?= $control_name ?>"></p>
              </div>
          	</div>
          <?php }?>
          <?php if(in_array($control_name,$primary_benefit_arr)) { ?>
            <?php if(!empty($primaryData)) { ?>
              <?php foreach ($primaryData as $productID => $dataArr) { ?>
                <?php $productName=getname('prd_main',$productID,'name','id'); ?>
                <?php if(!empty($dataArr)) { ?>
                  <?php foreach ($dataArr as $dataArrkey => $data) { ?>
                    <?php if((isset($data[str_replace('primary_','',$control_name)]) && $data[str_replace('primary_','',$control_name)] >= 0)){ 
                      $benefitControlValue = isset($data[str_replace('primary_','',$control_name)]) ? $data[str_replace('primary_','',$control_name)] : 0;
                      ?>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <input type="text" name="<?=$control_name?>[<?= $productID ?>]" id="<?=$control_name?>_<?= $productID ?>" class="form-control has-value" value="<?= $benefitControlValue ?>" readonly>
                          <label><?= $productName ?> <?=ucwords(str_replace(array('primary','_'),array('',' '),$control_name))?></label>
                          <p class="error" id="error_<?=$control_name?>_<?= $productID ?>"></p>
                        </div>
                      </div>
                    <?php } ?> 
                  <?php } ?>
                <?php } ?>
              <?php } ?>
            <?php } ?>
          <?php } ?>
        <?php } else {
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
                <select id="<?= $control_name ?>"  name="<?= $custom_name ?>[<?= $prd_question_id ?>]" class="primary_select primary_member_field <?= $control_value != '' ? "has-value" : "" ?>" required data-live-search="true">
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
            <div class="col-sm-12 m-b-25">
              <p>
              <label><?= $label ?></label>
             </p>
              <div class="radio-button ">
                <div class="btn-group colors  custom-question-btn" data-toggle="buttons">
                  <?php if(!empty($resAnswer)){
                    foreach ($resAnswer as $ansKey => $ansValue) { ?>
                      <label class="btn btn-info <?= (!empty($control_value) && $control_value== $ansValue['answer'] ? 'active' : '') ?>">
                            <input type="radio" name="<?= $custom_name ?>[<?= $prd_question_id ?>]" value="<?= $ansValue['answer'] ?>" data-ans-eligible="<?= $ansValue['answer_eligible'] ?>" class="js-switch primary_member_field" autocomplete="false" <?= (!empty($control_value) && $control_value==$ansValue['answer'] ? 'checked' : '') ?> > <?= $ansValue['answer'] ?>
                          </label>
                    <?php } ?>
                  <?php } ?>
                </div>
              </div>
              <p class="error" id="error_<?= $control_name ?>"></p>
            </div>
          <?php }else if($control_type=='select_multiple'){ ?>
            <div class="col-sm-12">
              <p>
                <?= $label ?>
              </p>
              <div class="form-group height_auto w-300 custom_question">
                <select id="<?= $control_name ?>"  name="<?= $custom_name ?>[<?= $prd_question_id ?>][]" class="se_multiple_select primary_multiple_select primary_member_field <?= $control_value != '' ? "has-value" : "" ?>" required multiple="multiple">
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
                      <p>
                          <label><?= $label ?><?php if( $is_required == 'Y') { ?><span class="req-indicator">*</span><?php } ?></label>
                      </p>
                      <textarea id="<?= $control_name ?>" class="form-control <?= $control_value != '' ? "has-value" : "" ?>" name="<?= $custom_name ?>[<?= $prd_question_id ?>]"  rows="3" cols="50" maxlength="300"><?= $control_value ?></textarea>
                      <p class="error" id="error_<?= $control_name ?>"></p>
                  </div>
          <?php } ?>
        <?php } ?>
      <?php } ?>
      <?php foreach ($product_list as $k => $p_id) { ?>
          <?php if(isset($_POST['monthly_benefit_amount_'.$p_id])){ ?>
            <?php 
              $productName = $pdo->selectOne("SELECT id,name,is_short_term_disablity_product from prd_main where id=:id",array(':id'=>$p_id)); 
              if($productName['is_short_term_disablity_product'] == 'Y'){
            ?>
            <?php $benefit=$_POST['monthly_benefit_amount_'.$p_id];?>
            <div class="col-sm-3">
              <div class="form-group">
                <input type="text" name="std_monthly_benefit[<?= $p_id ?>]" id="std_monthly_benefit_<?= $p_id ?>" class="form-control has-value" value="<?= $benefit ?>" readonly>
                <label><?= $productName['name'] ?> Monthly Benefit</label>
                <p class="error" id="error_std_monthly_benefit_<?= $p_id ?>"></p>
              </div>
            </div>
        <?php } } ?>
      <?php } ?>
    </div>
  <?php }
  $html = ob_get_clean();
  $response['html']=$html;
  $response['status']='success';
}else{
  $response['status']='fail';
}

$response['site_location'] = $SITE_ENV;

echo json_encode($response);
dbConnectionClose();
exit;
?>