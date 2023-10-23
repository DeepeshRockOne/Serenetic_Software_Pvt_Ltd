<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/policy_setting.class.php';
$policySetting = new policySetting();
$error = "";

$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$ws_id = isset($_REQUEST['ws_id']) ? $_REQUEST['ws_id'] : "";
$ws_row = $pdo->selectOne("SELECT * from website_subscriptions where md5(id)=:id",array(':id' => $ws_id));

$customer_id = $ws_row['customer_id'];
$product_id = $ws_row['product_id'];
$plan_id = $ws_row['plan_id'];

$customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
$customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));
if(isset($_POST['is_submit']) && $_POST['is_submit'] == 'Y'){

    $termination_date = isset($_POST['termination_date']) ? $_POST['termination_date'] : "";
    $reason = isset($_POST['reason']) ? $_POST['reason'] : "";
    $cobra_options = isset($_POST['cobra_options']) ? $_POST['cobra_options'] : "";
    if(empty($termination_date)){
        $error = "Please select termination date";
        $response['error'] = $error;
        $response['status'] = 'fail';
    }else{
        if (!empty(strtotime($termination_date))) {
            $extra_params = array();
            $extra_params['location'] = "member_detail";
            $extra_params['portal'] = $location;
            $policySetting->setTerminationDate($ws_row['id'],$termination_date,$reason,$extra_params);
            
            //start - terminate the plan and if assign any adminfee this product so terminate admin fee
            $checkAdminFee = $pdo->selectOne("SELECT ws.id FROM `website_subscriptions` ws
            JOIN `customer` c ON (c.id = ws.customer_id AND c.is_deleted = 'N')
            JOIN `customer` cs ON (cs.id = c.sponsor_id AND cs.type = 'Group' AND cs.is_deleted = 'N')
            JOIN prd_main prd ON (prd.id = ws.product_id AND prd.product_type='AdminFee')
            WHERE ws.fee_applied_for_product=:product_id AND ws.customer_id=:customer_id AND ws.product_type='Fees' AND ws.status='Active'",array(":product_id"=>$product_id,":customer_id"=>$customer_id));
            
            if(!empty($checkAdminFee)){
                $policySetting->setTerminationDate($checkAdminFee['id'],$termination_date,$reason_id,$extra_params);
            }
            //end - terminate the plan and if assign any adminfee this product so terminate admin fee
        
        }
        $response['status'] = 'success';
        $response['termination_date'] = date('m/d/Y',strtotime($termination_date));
        $response['message'] ='The termination date has been set.';
        $response['cobra_options'] = $cobra_options;
    }
    echo json_encode($response);
    exit();

}

$reasons = get_policy_termination_reasons();

$sponsor_id = getname('customer',$customer_id,'sponsor_id','id');
$sponsor_type = getname('customer',$sponsor_id,'type','id');
$allow_cobra_benefit = 'N';
if($sponsor_type == 'Group' && $ws_row['is_cobra_coverage'] == 'N'){
    $check_cobra_benefits = $pdo->selectOne("SELECT group_use_cobra_benefit FROM group_cobra_benefits WHERE is_deleted = 'N'");
    if($check_cobra_benefits && $check_cobra_benefits['group_use_cobra_benefit'] == 'Y'){
        $allow_cobra_benefit = 'Y';
    }
}

$template = 'add_term_date.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
