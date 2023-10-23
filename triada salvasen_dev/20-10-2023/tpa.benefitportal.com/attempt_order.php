<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/function.class.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
$function_list = new functionsList();
$enrollDate = new enrollmentDate();
$MemberEnrollment = new MemberEnrollment();
$location = (isset($_REQUEST['location'])?$_REQUEST['location']:'admin');
$lead_id = $_GET['lead_id'];
$order_id = $_GET['order_id'];

$order_row = $pdo->selectOne("SELECT id,customer_id,is_renewal,post_date,status FROM orders WHERE status in('Post Payment','Payment Declined') AND md5(id) = :order_id", array(":order_id" => $order_id));

if(empty($order_row['id'])) {
    echo "<script>
    parent.setNotifyError('Order not found');
    parent.$.colorbox.close();</script>";
    exit();
}

$order_id = $order_row['id'];
$customer_id = $order_row['customer_id'];

$lead_row = $pdo->selectOne("SELECT l.id,l.lead_id FROM leads l WHERE md5(l.id) = :id", array(":id" => $lead_id));
$lead_id = $lead_row['id'];

$cust_row = $pdo->selectOne("SELECT c.id,c.sponsor_id,c.fname,c.lname,c.address,c.address_2,c.city,c.state,c.zip FROM customer c WHERE c.id = :id", array(":id" => $customer_id));
$sponsor_row = $pdo->selectOne("SELECT c.id,c.rep_id,c.type,c.sponsor_id FROM customer c WHERE c.id=:id", array(":id" =>$cust_row['sponsor_id']));

$od_sql="SELECT od.id,od.order_id,website_id,pc.title,p.id as product_id,od.fee_applied_for_product,p.payment_type_subscription,od.prd_plan_type_id,p.type,od.plan_id,p.name,od.unit_price,od.start_coverage_period,od.end_coverage_period,p.product_type,p.fee_type,o.created_at FROM orders o 
            JOIN order_details od on (o.id=od.order_id AND od.is_deleted='N')
            LEFT JOIN prd_main p ON(p.id=od.product_id and p.is_deleted='N')
            LEFT JOIN prd_category pc ON(pc.id=p.category_id and pc.is_deleted='N')
            WHERE o.id=:order_id GROUP BY od.id";
$od_res=$pdo->select($od_sql,array(":order_id"=>$order_id));

$eligibility_dates = array();
$PlanIdArr = array();
if(count($od_res)){
    foreach ($od_res as $key => $row) {
            if(!empty($row['plan_id']) && !in_array($row['plan_id'], $PlanIdArr)){
                array_push($PlanIdArr, $row['plan_id']);
            }
            $eligibility_dates[$row['product_id']] = $row["start_coverage_period"];
    }
}

$eligibility_date = min(array_map(function($item) { return $item; }, array_values($eligibility_dates)));
if (!empty($eligibility_date)) {
    $coverge_effective_date = date('m/d/Y', strtotime($eligibility_date));
}


/*--------/Billing Data -------*/
$billing_data = $pdo->selectOne("SELECT *,AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as card_no_full, AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_account_number, AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_routing_number FROM customer_billing_profile WHERE is_default='Y' AND customer_id=:customer_id AND is_deleted='N' ORDER BY id DESC", array(":customer_id" => $customer_id));

$same_as_personal = false;
$payment_mode = '';
if (!empty($billing_data)) {
    if (($cust_row['fname'] == $billing_data['fname']) && ($cust_row['lname'] == $billing_data['lname']) && ($cust_row['address'] == $billing_data['address']) && ($cust_row['city'] == $billing_data['city']) && ($cust_row['state'] == $billing_data['state']) && ($cust_row['zip'] == $billing_data['zip'])) {
        $same_as_personal = true;
    }
    $payment_mode = $billing_data['payment_mode'];
}

$sale_type_params = array();
$sale_type_params['is_renewal'] = $order_row['is_renewal'];
$sale_type_params['customer_id'] = $customer_id;
if($sponsor_row['type']=='Group'){
    $payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_row['sponsor_id'], $payment_mode,$sale_type_params);
}else{
    $payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_row['id'], $payment_mode,$sale_type_params);
}
$payment_res = $pdo->selectOne("SELECT * from payment_master where id=:id and is_deleted='N'",array(":id"=>$payment_master_id));
$pyament_methods = get_pyament_methods($cust_row['sponsor_id']);
$is_cc_accepted = $pyament_methods['is_cc_accepted'];
$is_ach_accepted = $pyament_methods['is_ach_accepted'];
$acceptable_cc = $pyament_methods['acceptable_cc'];
/*-------- Billing Data -------*/

$post_date = "";
if($order_row['status'] == "Post Payment") {
    if(strtotime($order_row['post_date']) > 0) {
        $post_date = displayDate($order_row['post_date']);
    }
}

$exStylesheets = array(
);
$exJs = array(
    'thirdparty/masked_inputs/jquery.maskedinput.min.js',
    'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
);

$template = 'attempt_order.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>