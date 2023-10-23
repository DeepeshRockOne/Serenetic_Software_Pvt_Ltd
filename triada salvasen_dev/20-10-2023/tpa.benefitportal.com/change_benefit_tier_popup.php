<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/benefit_tier_change_function.php';

$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$ws_id = $_REQUEST['ws_id'];
$ws_sql = "SELECT w.* FROM website_subscriptions w WHERE md5(w.id)=:id";
$ws_where = array(':id' => $ws_id);
$ws_row = $pdo->selectOne($ws_sql, $ws_where);

$ce_sql = "SELECT ce.* FROM customer_enrollment ce WHERE ce.website_id=:website_id";
$ce_where = array(':website_id' => $ws_row['id']);
$ce_row = $pdo->selectOne($ce_sql, $ce_where);

$policy_change_reason = 'benefit_tier_change';
if(!empty($ws_row['policy_change_reason'])) {
    $policy_change_reason = $ws_row['policy_change_reason'];
}

$website_id = $ws_row['id'];
$tier_change_date = $ce_row['tier_change_date'];

$sponser_type = $pdo->selectOne("SELECT cs.type FROM `customer` c JOIN `customer` cs ON (c.sponsor_id=cs.id) WHERE c.id = :id",array(':id' => $ws_row['customer_id']));

if (!empty($_POST)) {

    if (isset($_POST['cancel_benefit_tier_update'])) { // If Cancel benefit tier update
        
        //start check sponser is group and assign admin fee of this plan
        if($sponser_type['type'] == 'Group'){
            $checkAdminFee = $pdo->selectOne("SELECT ws.id as ws_admin_fee FROM `website_subscriptions` ws
            JOIN `customer` c ON (c.id = ws.customer_id AND c.is_deleted = 'N')
            JOIN `customer` cs ON (cs.id = c.sponsor_id AND cs.type = 'Group' AND cs.is_deleted = 'N')
            JOIN prd_main prd ON (prd.id = ws.product_id AND prd.product_type='AdminFee')
            WHERE ws.fee_applied_for_product=:ws_prd AND ws.status IN ('Active','Pending') AND ws.customer_id=:ws_cus_id AND ws.product_type='Fees'",array(":ws_prd"=>$ws_row['product_id'],":ws_cus_id"=>$ws_row['customer_id']));
            //end check sponser is group and assign admin fee of this plan
            if(empty($checkAdminFee)){
            // start admin fee is not assign old product then termainte new admin fee
                include_once __DIR__ . '/includes/member_setting.class.php';
                $memberSetting = new memberSetting();
                $new_ce_row = "";
                $ce_sql = "SELECT ce.id,ce.website_id
                    FROM customer_enrollment ce
                    WHERE ce.website_id=:website_id";
                $ce_row = $pdo->selectOne($ce_sql, array(":website_id" => $ws_row['id']));
                $ce_id = $ce_row['id'];
                $extra = array("is_cancel_benefit_tier" => true);
                $member_status = $memberSetting->get_status_by_change_benefit_tier("","","","",$extra);
                $new_ce_sql = "SELECT * FROM customer_enrollment WHERE parent_coverage_id=:id AND process_status='Pending'";
                $new_ce_row = $pdo->selectOne($new_ce_sql, array(":id" => $ce_id));
                // start admin fee is not assign old product then termainte new admin fee
            }
        }

        //start main product plan
        cancel_tier_change($ws_row['id'],$location);
        //end main product plan
        
        if($sponser_type['type'] == 'Group'){
            if(!empty($checkAdminFee)){
                // start check sponser is group and assign admin fee of this plan cancel admin fee
                cancel_tier_change($checkAdminFee['ws_admin_fee'],$location);
            }else{
                // start admin fee is not assign old product then termainte new admin fee
                if(!empty($new_ce_row)){
                    $prd_plan_ws_sql = "SELECT w.id,w.product_id,w.customer_id FROM website_subscriptions w WHERE w.id=:id";
                    $prd_plan_ws_where = array(':id' => $new_ce_row['website_id']);
                    $prd_plan_ws_row = $pdo->selectOne($prd_plan_ws_sql, $prd_plan_ws_where);

                    $new_ws_sql = "SELECT w.id,w.policy_change_reason,w.eligibility_date,w.customer_id,w.product_id,w.plan_id FROM website_subscriptions w
                                    JOIN prd_main p ON(w.product_id=p.id) WHERE p.product_type = 'AdminFee' AND w.status = 'Pending' AND w.customer_id=:customer_id AND w.fee_applied_for_product=:product_id";
                    $new_ws_where = array(':customer_id' => $prd_plan_ws_row['customer_id'],':product_id' => $prd_plan_ws_row['product_id']);
                    $new_ws_row = $pdo->selectOne($new_ws_sql, $new_ws_where);
                }

                if ($new_ce_row && $new_ws_row) {
                    $policy_change_reason = 'benefit_tier_change';

                    if(!empty($new_ws_row['policy_change_reason'])) {
                        $policy_change_reason = $new_ws_row['policy_change_reason'];
                    }

                    $termination_reason_id = $SYSTEM_TERMINATION_REASONS['Cancelled Benefit Tier Change'];

                    if($policy_change_reason == "policy_change") {

                        $termination_reason_id = $SYSTEM_TERMINATION_REASONS['Cancelled Policy Change'];

                    } elseif($policy_change_reason == "benefit_amount_change") {

                        $termination_reason_id = $SYSTEM_TERMINATION_REASONS['Cancelled Benefit Amount Change'];
                    }

                    $ce_update_params = array(
                        "process_status" => 'Cancelled',
                    );
                    $ce_update_where = array(
                        "clause" => "website_id=:website_id",
                        "params" => array(":website_id" => $new_ce_row['website_id'])
                    );
                    $pdo->update("customer_enrollment", $ce_update_params, $ce_update_where);

                    $new_ws_data = array(
                        "status" => $member_status['policy_status'],
                        "updated_at" => "msqlfunc_NOW()",
                        "termination_date" => $new_ws_row['eligibility_date'],
                        "term_date_set" => date('Y-m-d'),
                        'termination_reason_id' => $termination_reason_id,
                    );
                    $new_ws_where = array("clause" => "id=:id", "params" => array(":id" => $new_ws_row['id']));
                    $pdo->update("website_subscriptions", $new_ws_data, $new_ws_where);

                    $web_history_data = array(
                        'customer_id' => $new_ws_row['customer_id'],
                        'website_id' => $new_ws_row['id'],
                        'product_id' => $new_ws_row['product_id'],
                        'plan_id' => $new_ws_row['plan_id'],
                        'order_id' => 0,
                        'status' => 'Update',
                        'message' => array_search($termination_reason_id, $SYSTEM_TERMINATION_REASONS),
                        'admin_id' => checkIsset($_SESSION['admin']['id']),
                        'authorize_id' => '',
                        'processed_at' => 'msqlfunc_NOW()',
                        'created_at' => 'msqlfunc_NOW()',
                    );
                    $pdo->insert("website_subscriptions_history", $web_history_data);
                // start admin fee is not assign old product then termainte new admin fee    
                }
                
            }
        }
        //end check sponser is group and assign admin fee of this plan cancel admin fee
        
        $response['status'] = 'success';
        if($policy_change_reason == "policy_change") {
            setNotifySuccess('Successfully cancelled future plan change');

        } elseif ($policy_change_reason == "benefit_amount_change") {
            setNotifySuccess('Successfully cancelled future benefit amount change');
        
        } else {
            setNotifySuccess('Successfully cancelled future coverage update');    
        }        
        echo json_encode($response);
        exit();
    } elseif (strtotime($ce_row['tier_change_date']) != strtotime($_POST['tier_change_date'])) { 
        // If tier_change_date Is Changed
        $tier_change_date = $_POST['tier_change_date'];
        $validate = new Validation();
        $validate->string(array('required' => true, 'field' => 'tier_change_date', 'value' => $tier_change_date), array('required' => 'Effective date is required'));

        $tier_change_date = date("Y-m-d",strtotime($tier_change_date));

        if ($validate->isValid()) {
            $subscription_response = update_tier_change_date($ws_row['id'],$tier_change_date,array(),$location);
            if($subscription_response['status'] == true) {
                if($policy_change_reason == "policy_change") {
                    setNotifySuccess('Successfully saved future plan change');

                } elseif ($policy_change_reason == "benefit_amount_change") {
                    setNotifySuccess('Successfully saved future benefit amount change');
                
                } else {
                    setNotifySuccess('Successfully saved future coverage update');
                }            
                $response['status'] = 'success';
            } else {
                $response['status'] = 'fail';
                $response['errors']['tier_change_date'] = $subscription_response['message'];
            }
        } else {
            $errors = $validate->getErrors();
            $response['fail'] = 'success';
            $response['errors'] = $errors;
        }
        echo json_encode($response);
        exit();
    } else {
        if($policy_change_reason == "policy_change") {
            setNotifySuccess('Successfully saved future plan change');
            
        } elseif ($policy_change_reason == "benefit_amount_change") {
            setNotifySuccess('Successfully saved future benefit amount change');

        } else {
            setNotifySuccess('Successfully saved future coverage update');
        }
        $response['status'] = 'success';
        echo json_encode($response);
        exit();
    }
}
$date_selection_options = get_tier_change_date_selection_options($ws_row['id']);
$template = 'change_benefit_tier_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>