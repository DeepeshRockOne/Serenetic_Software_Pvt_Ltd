<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
agent_has_access(2);
$sponsor_id = $_SESSION["agents"]["id"];

if(!empty($_GET['quote_id'])){
	$lead_quote_detail_id = $_GET['quote_id'];

	$lead_quote_detail_sql = "SELECT id,agent_id FROM lead_quote_details WHERE MD5(id) = :id AND status = 'Pending' AND is_assisted_enrollment = 'Y'";
	$lead_quote_row = $pdo->selectOne($lead_quote_detail_sql, array(":id" => $lead_quote_detail_id));

	if (empty($lead_quote_row["agent_id"])) {
		setNotifyError("quote_not_found");
		redirect($HOST . "/lead_quote_enrollment_response.php");
		exit();
	}else{
	 	$sponsor_id = $lead_quote_row["agent_id"];
	}
} elseif (!empty($_GET['lead_id'])) {

    $lead_quote_detail_sql = "SELECT id,agent_id FROM lead_quote_details 
    						WHERE MD5(lead_id)=:lead_id AND 
    						status='Pending' AND is_assisted_enrollment = 'Y' AND 
    						expire_time > :curr_datetime ORDER BY id DESC
    						";
	$lead_quote_row = $pdo->selectOne($lead_quote_detail_sql, array(":lead_id" => $_GET['lead_id'],':curr_datetime' => date("Y-m-d H:i:s")));
	if (!empty($lead_quote_row["agent_id"])) {
		$sponsor_id = $lead_quote_row['agent_id'];
	}else {
		$lead_sql = "SELECT id,sponsor_id FROM leads WHERE MD5(id) = :id";
		$lead_res = $pdo->selectOne($lead_sql, array(":id" => $_GET['lead_id']));
		if(!empty($lead_res["sponsor_id"])){
			$sponsor_id = $lead_res['sponsor_id'];
		}
	}
}

$eo_expiration = checkEOExpiredOrNot($sponsor_id);

if(!empty($eo_expiration) && $eo_expiration['expired'] == 'expired'){
	setNotifyError("E&O is Expired.");
	redirect($AGENT_HOST . "/dashboard.php");
}

$status = getname('customer',$sponsor_id,'status');
if(!in_array($status ,array('Active','Contracted'))){
	setNotifyError("Restricted Link: Contact Representative");
	redirect($AGENT_HOST . "/dashboard.php");
}

include_once __DIR__ . '/../includes/member_enrollment.class.php';
$MemberEnrollment = new MemberEnrollment();

$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Enroll';
$breadcrumbes[1]['link'] = 'member_enrollment.php';
$breadcrumbes[2]['title'] = '+ Member';

$displayEnrollmentHeader=false;
$instruction_text="Welcome to Agent Assisted Application (AAA)! Begin by entering the requested primary enrollees information and prospective spouse/child dependents";
$customer_id = 0;
$from_group_side = false;
$enrollmentLocation ="agentSide";

$trigger_res = $pdo->selectOne("SELECT * FROM triggers WHERE id = 84");
if ($trigger_res > 0) {
	$sms_content = $trigger_res['sms_content'];
	$email_content = html_entity_decode($trigger_res['email_content']);
	$email_subject = $trigger_res['email_subject'];
}

$coverage_date_selection_date_array = array();
$coverage_date_selection_prd_array = array();
$enrollment_type = "";
$lead_quote_detail_id = 0;


if(!empty($_GET['quote_id'])){
	$lead_quote_detail_id = $_GET['quote_id'];


	$lead_quote_detail_sql = "SELECT * FROM lead_quote_details WHERE MD5(id) = :id AND status = 'Pending' AND is_assisted_enrollment = 'Y'";
	$lead_quote_row = $pdo->selectOne($lead_quote_detail_sql, array(":id" => $lead_quote_detail_id));

	if (empty($lead_quote_row)) {
		setNotifyError("quote_not_found");
		redirect($HOST . "/lead_quote_enrollment_response.php");
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
			redirect($AGENT_HOST . "/member_enrollment.php");
			exit();
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

if(!empty($_GET['customer_id'])) {
	$is_add_product = 1;
}

if(isset($lead_quote_row) && !empty($lead_quote_row)){
	if (strtotime(date('Y-m-d H:i:s')) > strtotime($lead_quote_row['expire_time'])) {
		setNotifyError("quote_expired");
		redirect($HOST . "/lead_quote_expired.php");
		exit();
	}
	
	$customer_id = $lead_quote_row['customer_ids'];
	$enrollment_type = "quote";
	$order_id = $lead_quote_row['order_ids'];
	$order_sql = "SELECT * FROM orders WHERE id ='$order_id'";
	$order_res = $pdo->selectOne($order_sql);

	if ($order_res['status'] != 'Pending Quote' && $order_res['status'] != 'Pending Validation') {
		$customer_res = $pdo->selectOne("SELECT id FROM customer WHERE id = :id AND status IN ('Pending Quote', 'Pending Quotes','Pending Validation')", array(':id' => $order_res['customer_id']));
		if (empty($customer_res)) {
			setNotifyError("already_enrolled");
			redirect($HOST . "/lead_quote_enrollment_response.php");
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
	
	$od_sql = "SELECT od.product_id,od.plan_id,od.unit_price as price,p.type,p.product_type,od.start_coverage_period,p.pricing_model,od.prd_plan_type_id
            FROM order_details od
            JOIN prd_main p ON(p.id = od.product_id)
            WHERE od.order_id=:order_id AND od.is_deleted='N'";
    $od_res = $pdo->select($od_sql, array(":order_id" => $lead_quote_row['order_ids']));
    if(!empty($od_res)) {
    	foreach ($od_res as $od_row) {
    		if($od_row['product_type'] == "Healthy Step") {
    			$quote_healthy_step_fee = $od_row['product_id'];
    		} elseif($od_row['type'] == "Normal") {
				$prdRow = $pdo->selectOne("SELECT id,category_id,is_short_term_disablity_product from prd_main where id=:id",array(":id"=>$od_row['product_id']));
    			$quote_products[] = array(
    				'product_id' => $od_row['product_id'],
    				'price' => $od_row['price'],
					'category_id' => $prdRow['category_id'],
					'is_short_term_disablity_product' => $prdRow['is_short_term_disablity_product'],
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
			'benefit_amount' => isset($benefit_amount_arr['benefit_amount']) ? $benefit_amount_arr['benefit_amount'] : '',
			'in_patient_benefit' => isset($benefit_amount_arr['in_patient_benefit']) ? $benefit_amount_arr['in_patient_benefit'] : '',
			'out_patient_benefit' => isset($benefit_amount_arr['out_patient_benefit']) ? $benefit_amount_arr['out_patient_benefit'] : '',
			'monthly_income' => isset($benefit_amount_arr['monthly_income']) ? $benefit_amount_arr['monthly_income'] : '',
			'benefit_percentage' => isset($benefit_amount_arr['benefit_percentage']) ? $benefit_amount_arr['benefit_percentage'] : '',
		);
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
	//pre_print($principal_beneficiary);
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

	//pre_print($billing_data);
}

if(isset($is_add_product) && $is_add_product == 1) {
	$pyament_methods = get_pyament_methods($sponsor_id);
} else {
	$pyament_methods = get_pyament_methods($sponsor_id,false);	
}
$is_cc_accepted = $pyament_methods['is_cc_accepted'];
$is_ach_accepted = $pyament_methods['is_ach_accepted'];
$acceptable_cc = $pyament_methods['acceptable_cc'];

$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css',
	'thirdparty/summernote-master/dist/summernote.css',
	'thirdparty/signature_pad-master/example/css/signature-pad.css',
	'thirdparty/jquery-asRange-master/dist/css/asRange.css'.$cache,
	'thirdparty/ionrangeSlider/css/ion.rangeSlider.css'.$cache
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
	'thirdparty/jquery-asRange-master/dist/jquery-asRange.min.js'.$cache,
	'thirdparty/ionrangeSlider/js/ion.rangeSlider.js'.$cache
	
);

$template = "../../tmpl/member_enrollment.inc.php";
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>