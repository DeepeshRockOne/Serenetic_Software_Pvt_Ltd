<?php
include_once __DIR__ . '/includes/connect.php'; 
$agent_id = 0;
$pb_id = 0;
if(isset($_GET['site'])) { 
	/*--- AAE Enrollment Website ---*/
	$user_name = isset($_GET['user_name'])?$_GET['user_name']:'';
	$agent_sql = "SELECT c.id,c.type,c.sponsor_id,c.fname,c.lname,c.feature_access,c.public_name,c.public_email,c.public_phone,cs.not_show_eo_expired,cs.display_in_member,cs.is_branding,cs.brand_icon,ad.e_o_expiration
		FROM customer c 
		LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) 
		LEFT JOIN agent_document ad ON (c.id=ad.agent_id)
		WHERE c.user_name=:user_name AND c.is_deleted='N' AND c.type !='Customer' AND c.status IN('Active','Contracted')";
	$agent_row = $pdo->selectOne($agent_sql,array(":user_name" => $user_name));

	if(empty($agent_row)) {
		$pb_sql = "SELECT pg.*,pi.image_name as cover_image 
            FROM page_builder pg 
            LEFT JOIN page_builder_images pi ON (pi.id = pg.cover_image) 
            WHERE pg.is_deleted='N' AND pg.status='Active' AND pg.user_name=:user_name";
		$pb_row = $pdo->selectOne($pb_sql,array(":user_name"=>$user_name));	
	

		if(empty($pb_row['product_ids'])) {
			$template = '404.inc.php';
			$layout = 'iframe.layout.php';
			include_once 'layout/end.inc.php';
			exit();
		}	

		$pb_id = $pb_row['id'];
		$agent_sql = "SELECT c.id,c.type,c.sponsor_id,c.fname,c.lname,c.feature_access,c.public_name,c.public_email,c.public_phone,cs.not_show_eo_expired,cs.display_in_member,cs.is_branding,cs.brand_icon,ad.e_o_expiration  
			FROM customer c 
			LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) 
			LEFT JOIN agent_document ad ON (c.id=ad.agent_id)
			WHERE c.id=:id AND c.is_deleted='N' AND c.type !='Customer' AND c.status IN('Active','Contracted')";
		$agent_row = $pdo->selectOne($agent_sql,array(":id" => $pb_row['agent_id']));
	}
} else { 
	/*--- Self-Enrollment Website ---*/
	$user_name = isset($_GET['user_name'])?$_GET['user_name']:'';
	$pb_sql = "SELECT pg.*,pi.image_name as cover_image 
            FROM page_builder pg 
            LEFT JOIN page_builder_images pi ON (pi.id = pg.cover_image) 
            WHERE pg.is_deleted='N' AND pg.status='Active' AND pg.user_name=:user_name";
	$pb_row = $pdo->selectOne($pb_sql,array(":user_name"=>$user_name));

	if(empty($pb_row['product_ids'])) {
		$template = '404.inc.php';
		$layout = 'iframe.layout.php';
		include_once 'layout/end.inc.php';
		exit();
	}

	$user_id = isset($pb_row['agent_id']) ? $pb_row['agent_id'] : 0;
	$agent_sql = "SELECT c.id,c.type,c.sponsor_id,c.fname,c.lname,c.feature_access,c.public_name,c.public_email,c.public_phone,cs.not_show_eo_expired,cs.display_in_member,cs.is_branding,cs.brand_icon,ad.e_o_expiration,c.status
		FROM customer c
		LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) 
		LEFT JOIN agent_document ad ON (c.id=ad.agent_id)
		WHERE c.id=:user_id AND c.is_deleted='N' AND c.type !='Customer' AND c.status IN('Active','Contracted')";
	$agent_row = $pdo->selectOne($agent_sql,array(":user_id" => $user_id));

	if(empty($agent_row['id'])) {
		$template = '404.inc.php';
		$layout = 'iframe.layout.php';
		include_once 'layout/end.inc.php';
		exit();
	}
	if($agent_row['type'] == 'Group'){
		include 'group_prd_preview.php';
	} else {
		include 'prd_preview.php';
	}
	exit();
}

if(empty($agent_row)) {
	$template = '404.inc.php';
	$layout = 'iframe.layout.php';
	include_once 'layout/end.inc.php';
	exit();
}

//---*Agent Menu Member Access Condition Start agent_has_menu_feature_access()---*//
else{
	$featureArr  = !empty($agent_row['feature_access']) ? explode(',',$agent_row['feature_access']) : array();
	if(!in_array(2,$featureArr)){
		$template = '404.inc.php';
		$layout = 'iframe.layout.php';
		include_once 'layout/end.inc.php';
		exit();
	}
}
//---*Agent Menu Member Access Condition End---*//

/*--- EO Expiration Code Start checkEOExpiredOrNot() ---*/
$eo_expiration_date = checkIsset($agent_row["e_o_expiration"]);

if(!empty($eo_expiration_date) && (strtotime($eo_expiration_date) < strtotime(date('Y-m-d')))){
	setNotifyError("E&O is Expired.");
	$template = '404.inc.php';
	$layout = 'iframe.layout.php';
	include_once 'layout/end.inc.php';
	exit();
}
/*--- EO Expiration Code Ends ---*/

$parent_agent_row = !empty($agent_row) ? $agent_row : array();

$sponsor_id = $agent_row['id'];
$customer_id = 0;
$enrollmentLocation ="aae_site";
if($pb_id > 0){
	$enrollmentLocation ="self_enrollment_site";
}
$from_group_side = false;
$group_billing_method = 'individual';
if($agent_row['type']=='Group'){
	$display_header = true;
	$class_ids = "";
	if(!empty($pb_row)){
		$from_group_side = true;
		$class_ids = $pb_row['class_ids'];
	}
	$enrollmentLocation ="groupSide";

	$sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
	$resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$sponsor_id));
	
	if(!empty($resBillingType)){
		$group_billing_method = $resBillingType['billing_type'];

	}
	$incr = "";
	if(!empty($class_ids)){
		$incr .=" AND id in ($class_ids)";
	}
	$sqlGroupClass="SELECT id, class_name FROM group_classes WHERE group_id=:group_id AND is_deleted='N' $incr";
	$sqlGroupClassWhere=array(':group_id'=>$sponsor_id);
	$resGroupClass=$pdo->select($sqlGroupClass,$sqlGroupClassWhere);

	if($from_group_side){
		
		$sqlGroupCoveragePeriod="SELECT gcp.id,gcp.coverage_period_name 
			FROM group_coverage_period  gcp
			JOIN group_coverage_period_offering gcpo ON (gcp.id = gcpo.group_coverage_period_id AND gcpo.is_deleted='N')
			WHERE gcp.group_id=:group_id 
			AND CURDATE()>= gcpo.open_enrollment_start AND CURDATE()<= gcpo.open_enrollment_end 
			AND gcp.is_deleted='N' GROUP BY gcp.id";
		$sqlGroupCoveragePeriodWhere=array(':group_id'=>$sponsor_id);
		$resGroupCoveragePeriod=$pdo->select($sqlGroupCoveragePeriod,$sqlGroupCoveragePeriodWhere);

	}else{
		$sqlGroupCoveragePeriod="SELECT id, coverage_period_name FROM group_coverage_period WHERE group_id=:group_id AND is_deleted='N'";
		$sqlGroupCoveragePeriodWhere=array(':group_id'=>$sponsor_id);
		$resGroupCoveragePeriod=$pdo->select($sqlGroupCoveragePeriod,$sqlGroupCoveragePeriodWhere);
	}

	
}

$trigger_res = $pdo->selectOne("SELECT * FROM triggers WHERE id = 84");
if ($trigger_res > 0) {
	$sms_content = $trigger_res['sms_content'];
	$email_content = html_entity_decode($trigger_res['email_content']);
	$email_subject = $trigger_res['email_subject'];
}
$enrollment_type = "";
$lead_quote_detail_id = 0;
/*---- Existing Customer OR Lead -----*/

if(isset($_GET['user_id']) && isset($_GET['user_type'])) {
	if($_GET['user_type'] == "l") { //Lead
		$_GET['lead_id'] = $_GET['user_id'];
	}
	if($_GET['user_type'] == "m") { //Member
		$_GET['customer_id'] = $_GET['user_id'];	
	}
	if($_GET['user_type'] == "q") { //Member
		$_GET['quote_id'] = $_GET['user_id'];	
	}
}

if(!empty($_GET['quote_id'])){
	$lead_quote_detail_id = $_GET['quote_id'];

	$lead_quote_detail_sql = "SELECT * FROM lead_quote_details WHERE MD5(id) = :id AND status = 'Pending' AND is_assisted_enrollment = 'Y'";
	$lead_quote_row = $pdo->selectOne($lead_quote_detail_sql, array(":id" => $lead_quote_detail_id));

	if (empty($lead_quote_row)) {
		$template = '404.inc.php';
		$layout = 'iframe.layout.php';
		include_once 'layout/end.inc.php';
		exit();
	}
	$lead_quote_detail_id = $lead_quote_row['id'];

} elseif (!empty($_GET['lead_id'])) {

    $lead_quote_detail_sql = "SELECT * FROM lead_quote_details 
    						WHERE MD5(lead_id)=:lead_id AND 
    						status='Pending' AND is_assisted_enrollment = 'Y' AND 
    						expire_time > :curr_datetime ORDER BY id DESC
    						";
	$lead_quote_row = $pdo->selectOne($lead_quote_detail_sql, array(":lead_id" => $_GET['lead_id'],':curr_datetime' => date("Y-m-d H:i:s")));
	if (!empty($lead_quote_row)) {
		$lead_quote_detail_id = $lead_quote_row['id'];
	} else {
		$lead_sql = "SELECT * FROM leads WHERE MD5(id) = :id";
		$lead_res = $pdo->selectOne($lead_sql, array(":id" => $_GET['lead_id']));

		if(in_array($lead_res['status'],array("Converted"))) {
			if($enrollmentLocation == "aae_site") {
				redirect($AAE_WEBSITE_HOST . "/" . $user_name);
				exit();
        	} else if($enrollmentLocation == "self_enrollment_site") {
				redirect($ENROLLMENT_WEBSITE_HOST . "/" . $user_name);
				exit();
        	}
		}

		$customer_id = $lead_res['customer_id'];

		$lead_id = $lead_res['id'];
		$primary_fname = $lead_res['fname'];
		$primary_lname = $lead_res['lname'];
		$primary_gender = $lead_res['gender'];
		$primary_birthdate = !empty($lead_res['birth_date']) ? date('m/d/Y',strtotime($lead_res['birth_date'])) : '';
		$primary_zip = $lead_res['zip'];
		$primary_email = $lead_res['email'];
		$primary_cell_phone = $lead_res['cell_phone'];
		$primary_state = $lead_res['state'];
	}	

} elseif (!empty($_GET['customer_id'])) {
	$is_add_product = 1;
	$customer_sql = "SELECT id FROM customer WHERE md5(id) =:id";
	$customer_res = $pdo->selectOne($customer_sql,array(":id"=>$_GET['customer_id']));
	if(!empty($customer_res)) {
		$customer_id = $customer_res['id'];

		$def_bill_sql = "SELECT payment_mode,id,card_no,card_type,AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number,last_cc_ach_no,is_default FROM customer_billing_profile WHERE customer_id=:customer_id AND is_deleted='N'";
		$def_bill_row = $pdo->select($def_bill_sql,array('customer_id' => $customer_id));
		if(!empty($def_bill_row)) {
			$display_default_billing = 'Y';
		}
	}
}

if(isset($lead_quote_row) && !empty($lead_quote_row)){
	if (strtotime(date('Y-m-d H:i:s')) > strtotime($lead_quote_row['expire_time'])) {
		/*setNotifyError("quote_expired");
		redirect($HOST . "/lead_quote_expired.php");
		exit();*/
		$template = '404.inc.php';
		$layout = 'iframe.layout.php';
		include_once 'layout/end.inc.php';
		exit();
	}
	
	$customer_id = $lead_quote_row['customer_ids'];
	$enrollment_type = "quote";
	$order_id = $lead_quote_row['order_ids'];

	if($group_billing_method=='individual'){
		$order_sql = "SELECT id,status,future_payment,post_date,customer_id FROM orders WHERE id =:id";
		$order_res = $pdo->selectOne($order_sql,array(":id"=>$order_id));
	}else{
		$order_sql = "SELECT id,status,future_payment,post_date,customer_id FROM group_orders WHERE id =:id";
		$order_res = $pdo->selectOne($order_sql,array(":id"=>$order_id));
	}

	if ($order_res['status'] != 'Pending Quote' && $order_res['status'] != 'Pending Validation') {
		$customer_res = $pdo->selectOne("SELECT id FROM customer WHERE id = :id AND status IN ('Pending Quote', 'Pending Quotes','Pending Validation')", array(':id' => $order_res['customer_id']));
		if (empty($customer_res)) {
			/*setNotifyError("already_enrolled");
			redirect($HOST . "/lead_quote_enrollment_response.php");
			exit();*/
			$template = '404.inc.php';
			$layout = 'iframe.layout.php';
			include_once 'layout/end.inc.php';
			exit();
		}
	}
}

if(!empty($customer_id)) {
	$customer_sql = "SELECT * FROM customer WHERE id =:id";
	$customer_res = $pdo->selectOne($customer_sql,array(":id"=>$customer_id));

	if(!empty($customer_res)){
		if(!isset($primary_fname)) {
			$primary_fname = $customer_res['fname'];	
		}
		if(!isset($primary_email)) {
			$primary_email = $customer_res['email'];
		}
		$primary_gender = $customer_res['gender'];
		$primary_birthdate = !empty($customer_res['birth_date']) ? date('m/d/Y',strtotime($customer_res['birth_date'])) : '';
		$primary_zip = $customer_res['zip'];
		$customer_setting_sql = "SELECT group_coverage_period_id,class_id,relationship_to_group,relationship_date,termination_date FROM customer_settings WHERE customer_id =:id";
		$customer_setting_res = $pdo->selectOne($customer_setting_sql,array(":id"=>$customer_id));
		if(!empty($customer_setting_res)){	
			$coverage_period = $customer_setting_res['group_coverage_period_id'];
			$enrolle_class = $customer_setting_res['class_id'];
			$relationship_to_group = $customer_setting_res['relationship_to_group'];
			$relationship_date = !empty($customer_setting_res['relationship_date']) ? date('m/d/Y',strtotime($customer_setting_res['relationship_date'])) : '';
			$termination_date = !empty($customer_setting_res['termination_date']) ? date('m/d/Y',strtotime($customer_setting_res['termination_date'])) : '';
		}
	}

    if(empty($lead_id)) {
        $lead_id = getname("leads",$customer_id,"id","customer_id");
    }

    if(in_array($customer_res['status'],$MEMBER_ABONDON_STATUS)) {
	    $lead_sql = "SELECT * FROM leads WHERE id=:id";
		$lead_res = $pdo->selectOne($lead_sql, array(":id" => $lead_id));

		$primary_fname = $lead_res['fname'];
		$primary_lname = $lead_res['lname'];
		$primary_gender = $lead_res['gender'];
		$primary_birthdate = !empty($lead_res['birth_date']) ? date('m/d/Y',strtotime($lead_res['birth_date'])) : '';
		$primary_zip = $lead_res['zip'];
		$primary_email = $lead_res['email'];
		$primary_cell_phone = $lead_res['cell_phone'];
		$primary_state = $lead_res['state'];
    }
}

$quote_products = array();
$quote_healthy_step_fee = 0;
if(!empty($lead_quote_row)) {
	$lead_id = $lead_quote_row['lead_id'];
	
	if($group_billing_method=='individual'){
		$od_sql = "SELECT od.product_id,od.plan_id,od.unit_price as price,p.type,p.product_type,od.start_coverage_period,p.pricing_model,od.prd_plan_type_id
				FROM order_details od
				JOIN prd_main p ON(p.id = od.product_id)
				WHERE od.order_id=:order_id AND od.is_deleted='N'";
		$od_res = $pdo->select($od_sql, array(":order_id" => $lead_quote_row['order_ids']));
	}else{
		$od_sql = "SELECT od.product_id,od.plan_id,od.unit_price as price,p.type,p.product_type,od.start_coverage_period,p.pricing_model,od.prd_plan_type_id
		FROM group_order_details od
		JOIN prd_main p ON(p.id = od.product_id)
		WHERE od.order_id=:order_id AND od.is_deleted='N'";
		$od_res = $pdo->select($od_sql, array(":order_id" => $lead_quote_row['order_ids']));
	}
    if(!empty($od_res)) {
    	foreach ($od_res as $od_row) {
    		if($od_row['product_type'] == "Healthy Step") {
    			$quote_healthy_step_fee = $od_row['product_id'];
    		} elseif($od_row['type'] == "Normal") {
				$prdRow = $pdo->selectOne("SELECT id,category_id,is_short_term_disablity_product from prd_main where id=:id",array(":id"=>$od_row['product_id']));
    			$quote_products[] = array(
    				'product_id' => $od_row['product_id'],
    				'category_id' => $prdRow['category_id'],
					'is_short_term_disablity_product' => $prdRow['is_short_term_disablity_product'],
    				'price' => $od_row['price'],
    				'matrix_id' => $od_row['plan_id'],
    				'prd_plan_type_id' => $od_row['prd_plan_type_id'],
    				'pricing_model' => $od_row['pricing_model'],
    				'start_coverage_period' => $od_row['start_coverage_period'],
    			);
    			$coverage_date_selection_prd_array[] = $od_row['product_id'];
    			$coverage_date_selection_date_array[$od_row['product_id']] = date('m/d/Y',strtotime($od_row['start_coverage_period']));
    		}
    	}
    }

	$waive_coverage = array();
	$sele_waiv = "SELECT category_id,reason,other_reason FROM customer_waive_coverage WHERE is_deleted='N' AND customer_id=:customer_id";
	$resWaive = $pdo->select($sele_waiv,array(':customer_id'=>$customer_id));
	if(!empty($resWaive)){
		foreach($resWaive as $wa){
			if(!in_array($wa['category_id'],array_column($quote_products,'category_id'))){
				$waive_coverage[] = $wa;
			}
		}
	}

    $resCustomerDep = array();
	
	$sqlCustomerDep="SELECT *,GROUP_CONCAT(product_id,'-',benefit_amount) as benefit_amount,GROUP_CONCAT(product_id,'-',in_patient_benefit) as in_patient_benefit,GROUP_CONCAT(product_id,'-',out_patient_benefit) as out_patient_benefit,GROUP_CONCAT(product_id,'-',monthly_income) as monthly_income,GROUP_CONCAT(product_id,'-',benefit_percentage) as benefit_percentage FROM customer_dependent WHERE order_id=:order_id GROUP BY cd_profile_id ORDER BY FIELD(relation,'husband','wife','son','daughter')";
	$resCustomerDep=$pdo->select($sqlCustomerDep,array(":order_id"=>$lead_quote_row['order_ids']));	

	//dependent member information start
	$child_dep = array();
	$spouse_dep = array();
	foreach($resCustomerDep as $dep){
		if(!empty($dep['benefit_amount'])) {
			$dep['benefit_amount'] = explode(',',$dep['benefit_amount']);
			if(!empty($dep['benefit_amount'])) {
				$benefit_amount = array();
				foreach ($dep['benefit_amount'] as $key => $value) {
					$tmp_value = explode('-',$value);
					if(!empty($tmp_value[0]) && !empty($tmp_value[1])) {
						$benefit_amount[$tmp_value[0]] = $tmp_value[1];	
					}					
				}
				$dep['benefit_amount'] = $benefit_amount;
			}
		}
		if(!empty($dep['in_patient_benefit'])) {
			$dep['in_patient_benefit'] = explode(',',$dep['in_patient_benefit']);
			if(!empty($dep['in_patient_benefit'])) {
				$in_patient_benefit = array();
				foreach ($dep['in_patient_benefit'] as $key => $value) {
					$tmp_value = explode('-',$value);
					if(!empty($tmp_value[0]) && !empty($tmp_value[1])) {
						$in_patient_benefit[$tmp_value[0]] = $tmp_value[1];	
					}					
				}
				$dep['in_patient_benefit'] = $in_patient_benefit;
			}
		}
		if(!empty($dep['out_patient_benefit'])) {
			$dep['out_patient_benefit'] = explode(',',$dep['out_patient_benefit']);
			if(!empty($dep['out_patient_benefit'])) {
				$out_patient_benefit = array();
				foreach ($dep['out_patient_benefit'] as $key => $value) {
					$tmp_value = explode('-',$value);
					if(!empty($tmp_value[0]) && !empty($tmp_value[1])) {
						$out_patient_benefit[$tmp_value[0]] = $tmp_value[1];	
					}					
				}
				$dep['out_patient_benefit'] = $out_patient_benefit;
			}
		}
		if(!empty($dep['monthly_income'])) {
			$dep['monthly_income'] = explode(',',$dep['monthly_income']);
			if(!empty($dep['monthly_income'])) {
				$monthly_income = array();
				foreach ($dep['monthly_income'] as $key => $value) {
					$tmp_value = explode('-',$value);
					if(!empty($tmp_value[0]) && !empty($tmp_value[1])) {
						$monthly_income[$tmp_value[0]] = $tmp_value[1];	
					}					
				}
				$dep['monthly_income'] = $monthly_income;
			}
		}
		if(!empty($dep['benefit_percentage'])) {
			$dep['benefit_percentage'] = explode(',',$dep['benefit_percentage']);
			if(!empty($dep['benefit_percentage'])) {
				$benefit_percentage = array();
				foreach ($dep['benefit_percentage'] as $key => $value) {
					$tmp_value = explode('-',$value);
					if(!empty($tmp_value[0]) && !empty($tmp_value[1])) {
						$benefit_percentage[$tmp_value[0]] = $tmp_value[1];	
					}					
				}
				$dep['benefit_percentage'] = $benefit_percentage;
			}
		}

		$dep = array(
			'dep_id' => $dep['id'],
			'cd_profile_id' => $dep['cd_profile_id'],
			'order_id' => $dep['order_id'],
			'product_id' => $dep['product_id'],
			'product_plan_id' => $dep['product_plan_id'],
			'fname' => $dep['fname'],
			'birthdate' => date('m/d/Y',strtotime($dep['birth_date'])),
			'hire_date' => (strtotime($dep['hire_date']) > 0?date('m/d/Y',strtotime($dep['hire_date'])):''),
			'relation' => $dep['relation'],
			'gender' => $dep['gender'],
			'email' => $dep['email'],
			'phone' => $dep['phone'],
			'ssn' => $dep['ssn'],
			'city' => $dep['city'],
			'state' => $dep['state'],
			'zip_code' => $dep['zip_code'],
			'height_feet' => $dep['height_feet'],
			'height_inches' => $dep['height_inches'],
			'weight' => $dep['weight'],
			'smoke_use' => $dep['smoke_use'],
			'tobacco_use' => $dep['tobacco_use'],
			'benefit_level' => $dep['benefit_level'],
			'employmentStatus' => $dep['employmentStatus'],
			'salary' => $dep['salary'],
			'hours_per_week' => $dep['hours_per_week'],
			'pay_frequency' => $dep['pay_frequency'],
			'us_citizen' => $dep['us_citizen'],
			'benefit_amount' => $dep['benefit_amount'],
			'in_patient_benefit' => $dep['in_patient_benefit'],
			'out_patient_benefit' => $dep['out_patient_benefit'],
			'monthly_income' => $dep['monthly_income'],
			'benefit_percentage' => $dep['benefit_percentage'],
		);

		if(in_array($dep['relation'],array("Son","Daughter"))) {
			$child_dep[] = $dep;
		} else {
			$spouse_dep[] = $dep;
		}
	}
	//dependent member information end

	//primary additional question code start
	$primary_additional_data = array();

	$customer_setting = $pdo->selectOne("SELECT * FROM customer_settings WHERE customer_id=:customer_id",array(":customer_id"=>$customer_id));
	if(!empty($customer_setting)) {
		$benefit_amount_arr = array();

		$customer_benefit_amount = $pdo->select("SELECT * FROM customer_benefit_amount WHERE type='Primary' AND customer_id=:customer_id AND is_deleted='N'",array(":customer_id"=>$customer_id));
		foreach ($customer_benefit_amount as $key => $value) {
			$benefit_amount_arr['benefit_amount'][$value['product_id']] = $value['amount'];
			$benefit_amount_arr['in_patient_benefit'][$value['product_id']] = $value['in_patient_benefit'];
			$benefit_amount_arr['out_patient_benefit'][$value['product_id']] = $value['out_patient_benefit'];
			$benefit_amount_arr['monthly_income'][$value['product_id']] = $value['monthly_income'];
			$benefit_amount_arr['benefit_percentage'][$value['product_id']] = $value['benefit_percentage'];
		}

		$primary_additional_data = array(
			'fname' => $primary_fname,
			'gender' => $primary_gender,
			'birthdate' => $primary_birthdate,
			'zip' => $primary_zip,
			'smoking_status' => $customer_setting['smoke_use'],
			'tobacco_status' => $customer_setting['tobacco_use'],
			'height' => (!empty($customer_setting['height_feet'])?$customer_setting['height_feet'].'.'.$customer_setting['height_feet']:''),
			'weight' => $customer_setting['weight'],
			'no_of_children' => $customer_setting['no_of_children'],
			'has_spouse' => $customer_setting['has_spouse'],
		);
		if(!empty($benefit_amount_arr)) {
			$primary_additional_data['benefit_amount'] = $benefit_amount_arr['benefit_amount'];
			$primary_additional_data['in_patient_benefit'] = $benefit_amount_arr['in_patient_benefit'];
			$primary_additional_data['out_patient_benefit'] = $benefit_amount_arr['out_patient_benefit'];
			$primary_additional_data['monthly_income'] = $benefit_amount_arr['monthly_income'];
			$primary_additional_data['benefit_percentage'] = $benefit_amount_arr['benefit_percentage'];
		}
	}
	//primary additional question code end

	//beneficiary code start
	$customer_beneficiary = $pdo->select("SELECT *,AES_DECRYPT(ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn FROM customer_beneficiary WHERE customer_id=:customer_id AND is_deleted='N'",array(":customer_id"=>$customer_id));
	//pre_print($customer_beneficiary);
	$contingent_beneficiary = array();
	$principal_beneficiary = array();
	foreach ($customer_beneficiary as $key => $value) {
		if($value['beneficiary_type'] == "Contingent") {
			$contingent_beneficiary[] = $value;
		}
		if($value['beneficiary_type'] == "Principal") {
			$principal_beneficiary[] = $value;	
		}
	}
	//beneficiary code End

    $billing_data = $pdo->selectOne("SELECT *,AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as card_no_full, AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_account_number, AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_routing_number FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N' ORDER BY id DESC", array(":customer_id" => $customer_id));
	if(!empty($lead_quote_row['billing_info_param'])){
		$tmp_billing_data = json_decode($lead_quote_row['billing_info_param'], true);
		if(!empty($tmp_billing_data['payment_mode'])) {
			$billing_data = $tmp_billing_data;
		}
	}

	if($order_res['future_payment'] == "Y") {
		$post_date = date('m/d/Y',strtotime($order_res['post_date']));
		$future_payment = "Y";
	}
}
/*----/Existing Customer OR Lead -----*/

if($agent_row['type']=='Group'){
	if(isset($is_add_product) && $is_add_product == 1) {
		$pyament_methods = get_pyament_methods($agent_row['sponsor_id']);
	} else {
		$pyament_methods = get_pyament_methods($agent_row['sponsor_id'],false);	
	}
}else{
	if(isset($is_add_product) && $is_add_product == 1) {
		$pyament_methods = get_pyament_methods($sponsor_id);
	} else {
		$pyament_methods = get_pyament_methods($sponsor_id,false);	
	}
}
$is_cc_accepted = $pyament_methods['is_cc_accepted'];
$is_ach_accepted = $pyament_methods['is_ach_accepted'];
$acceptable_cc = $pyament_methods['acceptable_cc'];

$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css',
	'thirdparty/summernote-master/dist/summernote.css',
	'thirdparty/signature_pad-master/example/css/signature-pad.css',
	'thirdparty/jquery-asRange-master/dist/css/asRange.css'.$cache
);
$exJs = array(
	'thirdparty/ajax_form/jquery.form.js',
	'thirdparty/jquery_autotab/jquery.autotab-1.1b.js',
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/summernote-master/dist/summernote.js',
	'thirdparty/summernote-master/dist/popper.js',
	'thirdparty/signature_pad-master/example/js/signature_pad.js',
	'js/password_validation.js'.$cache,
	'thirdparty/price_format/jquery.price_format.2.0.js',
	'thirdparty/bower_components/moment/moment.js',
	'js/scrollfix.js',
	'thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js'.$cache,
	'js/notification.js'.$cache,
	'thirdparty/jquery-asRange-master/dist/jquery-asRange.min.js'.$cache
);
$template = "member_enrollment.inc.php";
$layout = 'quote.layout.php';
include_once 'layout/end.inc.php';
?>