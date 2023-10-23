<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/benefit_tier_change_function.php';
include_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
/************************/
//WE ARE NOT USE THIS FILE
/************************/
$validate = new Validation();
$action = isset($_REQUEST['action'])?$_REQUEST['action']:'benefit_tier_change';
$ws_id = $_REQUEST['ws_id'];
$ws_sql = "SELECT ws.*
            FROM website_subscriptions ws
            WHERE ws.id=:id";
$ws_row = $pdo->selectOne($ws_sql, array(":id" => $ws_id));
if($action == "policy_change") {
    $new_prd_id = $_REQUEST['new_prd_id'];
    $new_plan_type = isset($_REQUEST['new_plan_type'])?$_REQUEST['new_plan_type']:$ws_row['prd_plan_type_id'];
    $label = "policy";

} elseif($action == "benefit_amount_change") {
    $new_prd_id = $ws_row['product_id'];
    $new_plan_type = isset($_REQUEST['new_plan_type'])?$_REQUEST['new_plan_type']:$ws_row['prd_plan_type_id'];
    $label = "benefit amount";

} else {
    $new_prd_id = $ws_row['product_id'];
    $new_plan_type = $_REQUEST['new_plan_type'];    
    $label = "benefit tier";
}

$prd_row = get_product_row($ws_row['product_id']);
$new_prd_row = get_product_row($new_prd_id);

if (!empty($_POST)) {
    $response = array();
    $dependant = isset($_POST['dependant']) ? $_POST['dependant'] : array();
    $tier_change_date = $_POST['tier_change_date'];
    $billing_profile = $_POST['billing_profile'];
    $is_take_charge = $_POST['is_take_charge'];
    $new_plan_id = $_REQUEST['new_plan_id'];

    $validate->string(array('required' => true, 'field' => 'new_plan_id', 'value' => $new_plan_id), array('required' => 'Missing information: all pricing criteria must be added for enrollees to complete this change.'));

    $validate->string(array('required' => true, 'field' => 'tier_change_date', 'value' => $tier_change_date), array('required' => 'Please select effective date'));
    if($is_take_charge == "Y") {
        $validate->string(array('required' => true, 'field' => 'billing_profile', 'value' => $billing_profile), array('required' => 'Please payment method'));
    }
    $tier_change_date = date("Y-m-d",strtotime($tier_change_date));

    if(strtotime($tier_change_date) > 0 && strtotime('now') > strtotime($tier_change_date)) {
        $validate->string(array('required' => true, 'field' => 'billing_profile', 'value' => $billing_profile), array('required' => 'Select Payment Method to change to this '.$label.'.'));
    }

    $dependant_ids_arr = array_values($dependant);
    $dependant_ids = implode(',', $dependant_ids_arr);

    $relations = array();
    $spouse_dep = 0;
    $child_dep = 0;

    if(!empty($dependant_ids)){
    	$dep_res = $pdo->select("SELECT LOWER(relation) as relation,id FROM customer_dependent_profile WHERE id in($dependant_ids)");
    	if($dep_res){
    		foreach ($dep_res as $dep_row) {
                if(in_array($dep_row['relation'], array('husband','wife'))){
                    $spouse_dep++;
                } else if(in_array($dep_row['relation'], array('daughter','son'))){
                    $child_dep++;
                }
    		}
    	}
    }

    if($action == "benefit_amount_change") {
        $primary_benefit_amount = isset($_REQUEST['primary_benefit_amount'])?$_REQUEST['primary_benefit_amount']:'';
        $dep_benefit_amount = isset($_REQUEST['dep_benefit_amount'])?$_REQUEST['dep_benefit_amount']:array();

        $validate->string(array('required' => true, 'field' => 'benefit_amount', 'value' => $primary_benefit_amount), array('required' => 'Please select benefit amount for all enrollees.'));

        if(!$validate->getError('benefit_amount')) {
            foreach ($dependant_ids_arr as $dependant_id) {
                if(!isset($dep_benefit_amount[$dependant_id])) {
                    $validate->setError('benefit_amount','Please select benefit amount for all enrollees.');

                } elseif($dep_benefit_amount[$dependant_id] == 0 || $dep_benefit_amount[$dependant_id] == '') {
                    $validate->setError('benefit_amount','Please select benefit amount for all enrollees.');
                }

                if($validate->getError('benefit_amount')) {
                    break;
                }
            }
        }
    }
    
    if ($new_plan_type == "4") {
        $family_plan_rule = $new_prd_row['family_plan_rule'];

        if($family_plan_rule=="Spouse And Child"){
            if ($spouse_dep > 0 && $child_dep > 0) {

            } else {
                $validate->setError('dependant', "Select at least one child and spouse dependant to change this.");
            }
            
        } else if($family_plan_rule=="Minimum One Dependent"){
            if (count($dependant) < 1) {
                $validate->setError('dependant', "Select at least one dependant to change this");
            }

        } else if($family_plan_rule=="Minimum Two Dependent"){
            if(count($dependant) < 2){
                $validate->setError('dependant', "Select at least two dependant to change this");
            }
        }
    } 
    
    if ($new_plan_type == "2") {
        if ($child_dep == 0) {
            $validate->setError('dependant', 'Select at least one child to change this');
        }
    }

    if ($new_plan_type == "3") {
        if ($spouse_dep == 0) {
            $validate->setError('dependant', 'Select at least spouse to change this');
        }
    }

    if ($new_plan_type == "5") {
        if (count($dependant) != 1) {
            $validate->setError('dependant', 'Select only one dependant to change this');
        }
    }

    if ($spouse_dep > 1) {
        $validate->setError('dependant', 'Select only one spouse to change this.');
    }

    if ($validate->isValid()) {
        $extra_detail = array(
            'action' => $action, 
            'tier_change_date' => $tier_change_date, 
            'ws_id' => $ws_row['id'],
            'new_plan_id' => $new_plan_id,
            'quali_event' => "", 
            'other_quali_event' => "",
            'dependants' => $dependant,
            'billing_id' => $billing_profile,
        );

        if($action == "benefit_amount_change") {
            $extra_detail['primary_benefit_amount'] = $primary_benefit_amount;
            if ($new_plan_type > 1) {
                $extra_detail['dep_benefit_amount'] = $dep_benefit_amount;    
            }            
        }
        

        $subscription_response = change_subscription($tier_change_date,$ws_row['id'],$new_prd_id,$new_plan_type,$extra_detail,'admin');
        
        if($subscription_response['status'] == true) {
            setNotifySuccess($subscription_response['message']);
            $response['status'] = 'success';
        } else {
            setNotifyError($subscription_response['message']);
            $response['status'] = 'fail';
            $response['errors']['billing_profile'] = $subscription_response['message'];
        }
    } else {
        $response['status'] = 'fail';
        $response['errors'] = $validate->getErrors();
    }
    echo json_encode($response);
    exit();
}

$cur_plan_price = $ws_row['price'];
$cur_plan_type_title = $prdPlanTypeArray[$ws_row['prd_plan_type_id']]['title'];
$new_plan_type_title = $prdPlanTypeArray[$new_plan_type]['title'];

$other_params = array();
$new_policy_price_data = get_product_price_detail($ws_row['customer_id'],$new_prd_id,$new_plan_type,$ws_row['id'],$other_params);
$new_plan_price = $new_policy_price_data['price'];
$new_plan_id = $new_policy_price_data['plan_id'];
$pricing_model = $new_policy_price_data['pricing_model'];

$date_selection_options = get_tier_change_date_selection_options($ws_row['id']);
$prd_benefit_tier_res = get_product_benefit_tiers($new_prd_id);

$total_dep = 0;
$spouse_dep = 0;
$child_dep = 0;
$all_dep_res = array();
$selected_dep_ids = array();
$prd_benefit_amount = array();
if($action == "policy_change" || $action == "benefit_amount_change") {
    $sel = "SELECT id,LOWER(relation) as relation,display_id,CONCAT(fname,' ',lname) as name,IF(LOWER(relation)IN('husband','wife'),'Spouse','Child') as crelation 
    FROM customer_dependent_profile 
    WHERE customer_id=:customer_id AND is_deleted='N'
    ORDER BY FIELD (crelation,'Spouse','Child') ASC, created_at DESC";
    $arr1 = array(":customer_id" => $ws_row['customer_id']);
    $all_dep_res = $pdo->select($sel, $arr1);
    if(!empty($all_dep_res)) {
        $total_dep = count($all_dep_res);
        foreach ($all_dep_res as $key => $dep_row) {
            if(in_array($dep_row['relation'], array('husband','wife'))) {
                $spouse_dep++;
            } else if(in_array($dep_row['relation'], array('daughter','son'))) {
                $child_dep++;
            }

            $tmp_row = $pdo->selectOne('SELECT benefit_amount FROM customer_dependent WHERE website_id=:website_id AND cd_profile_id=:cd_profile_id',array(":website_id" => $ws_row['id'],":cd_profile_id" => $dep_row['id']));
            if(!empty($tmp_row)) {
                $all_dep_res[$key]['benefit_amount'] = $tmp_row['benefit_amount'];
            } else {
                $all_dep_res[$key]['benefit_amount'] = 0;
            }
        }
    }

    $tmp_row = $pdo->selectOne('SELECT GROUP_CONCAT(cd_profile_id) as selected_dep_ids FROM customer_dependent WHERE website_id=:website_id',array(":website_id" => $ws_row['id']));
    if(!empty($tmp_row)) {
        $selected_dep_ids = explode(',',$tmp_row['selected_dep_ids']);
    }

    if($action == "benefit_amount_change") {
        $MemberEnrollment = new MemberEnrollment();

        $assignedQuestionValue = $MemberEnrollment->assignedQuestionValue($ws_row['product_id']);
        if(!empty($assignedQuestionValue)) {
            foreach ($assignedQuestionValue as $enrollee_type => $value) {
                if(!empty($value['benefit_amount'])) {
                    foreach ($value['benefit_amount'] as $key1 => $value2) {
                        if(empty($prd_benefit_amount[$enrollee_type]) || !in_array($value2,$prd_benefit_amount[$enrollee_type])) {
                            $prd_benefit_amount[$enrollee_type][] = $value2;
                        }
                    }
                }
                
            }
        }
    }
}

if ($new_plan_type == "4" || $new_plan_type == "5") {
    $sel = "SELECT *,IF(LOWER(relation)IN('husband','wife'),'Spouse','Child') as crelation FROM customer_dependent_profile WHERE customer_id=:customer_id AND is_deleted='N' ORDER BY FIELD (crelation,'Spouse','Child') ASC, created_at DESC";
    $arr1 = array(":customer_id" => $ws_row['customer_id']);
    $dep_res = $pdo->select($sel, $arr1);
}
if ($new_plan_type == "2") {
    $sel = "SELECT * FROM customer_dependent_profile WHERE customer_id=:customer_id AND is_deleted='N' AND LOWER(relation) IN ('son','daughter') ORDER BY created_at DESC";
    $arr1 = array(":customer_id" => $ws_row['customer_id']);
    $dep_res = $pdo->select($sel, $arr1);
}
if ($new_plan_type == "3") {
    $sel = "SELECT * FROM customer_dependent_profile WHERE customer_id=:customer_id AND is_deleted='N' AND LOWER(relation) IN ('wife','husband') ORDER BY created_at DESC";
    $arr1 = array(":customer_id" => $ws_row['customer_id']);
    $dep_res = $pdo->select($sel, $arr1);
}

$billing_sql = "SELECT payment_mode,id,card_no,card_type,AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number,last_cc_ach_no,is_default FROM customer_billing_profile WHERE customer_id=:customer_id";
$billing_res = $pdo->select($billing_sql,array('customer_id' => $ws_row['customer_id']));

$state_res = $pdo->select("SELECT * FROM states_c WHERE country_id = 231");

$cust_sql = "SELECT * FROM customer WHERE id=:id";
$cust_row = $pdo->selectOne($cust_sql, array(":id" => $ws_row['customer_id']));

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css' . $cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js' . $cache);
/************************/
//WE ARE NOT USE THIS FILE
/************************/
$template = 'benefit_tier_change.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>