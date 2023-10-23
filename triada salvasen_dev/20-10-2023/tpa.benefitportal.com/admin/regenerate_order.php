<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$get_payent_method = isset($_POST['get_payent_method']) ? $_POST['get_payent_method'] : '';
if($get_payent_method){
    $customer_id = $_POST['customer_id'];
    $billSql = "SELECT *, 
        AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
        AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
        AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no 
        FROM customer_billing_profile WHERE md5(customer_id)=:customer_id and is_deleted='N'";
    $params = array(":customer_id" => $customer_id);
    $billRow = $pdo->select($billSql, $params);

    $class = !empty($billRow) ? "has-value" : "" ;
   $bill_desc = '<select class="form-control '.$class.'" id="payment_method" name="payment_method"><option data-hidden="true"></option>';
   if(count($billRow) > 0) { 
        foreach ($billRow as $key => $bill) {
            if($bill["payment_mode"] == "CC"){
                $type_of_cc = $bill["card_type"];
                $bill["payment_mode"] = '';
            } else {
                $type_of_cc = "";
                $bill["payment_mode"] = 'ACH';
            } 
            $selected = $bill["is_default"] == "Y" ? 'selected="selected"' : "" ;
            $is_default = $bill["is_default"] == "Y" ? '(Default)' : "" ;
        $bill_desc.="<option value='".$bill["id"]."' ".$selected." >".$bill["payment_mode"].' '.$type_of_cc.' (*'.$bill["last_cc_ach_no"].") $is_default</option>";
        } 
    } 
    $bill_desc .='<option value="add_new_payment_method">Add Payment Method</option></select>
    <label>Payment Method</label><p class="error error_payment_type"></p>';

    $response['bill_desc'] = $bill_desc;
    header('Content-type: application/json');
    echo json_encode($response);
    exit(); 
}

$get_coverage_period = isset($_POST['get_coverage_period']) ? $_POST['get_coverage_period'] : '';
$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';

if($get_coverage_period && !empty($product_id)){

    include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
    $enrollDate = new enrollmentDate();

    $customer_id = $_POST['customer_id'];
    $product_id = $_POST['product_id'];
    $member_payment_type = $_POST['member_payment_type'];
    $start_coverage_date = $_POST['start_date'];
    $product_dates = $enrollDate->getCoveragePeriod($start_coverage_date,$member_payment_type);
    $startCoveragePeriod = date('m/d/Y',strtotime($product_dates['startCoveragePeriod']));
    $effective_date = date('m/d/Y',strtotime($product_dates['eligibility_date']));
    $endCoveragePeriod = date('m/d/Y',strtotime($product_dates['endCoveragePeriod']));
    
    $response['start_coverage_date'] = $startCoveragePeriod;
    $response['end_coverage_date'] = $endCoveragePeriod;
    $response['effective_date'] = $effective_date;

    header('Content-type: application/json');
    echo json_encode($response);
    exit(); 
}

include_once dirname(__DIR__) . '/includes/function.class.php';
include_once dirname(__DIR__) . '/includes/member_enrollment.class.php';

$function_list = new functionsList();
$MemberEnrollment = new MemberEnrollment();

$customer_id = $_GET['customer_id'];
$orderId = $_GET['orderId'];
$location_from = checkIsset($_GET['location_from']);
$ask_for_effactive_date = false;
$is_regenerate_allow = true;
$is_renewal = 'N';

$order_display_id  = $function_list->get_order_id();
$od_res = $billRow = array();
if(!empty($orderId) && !empty($customer_id)){
    $order_sql = "SELECT o.id,o.display_id,o.is_renewal,o.customer_id,concat(c.fname,' ',c.lname) as name,c.rep_id,now() as currentTime 
                FROM orders o 
                JOIN customer c ON(o.customer_id = c.id) 
                WHERE md5(o.id)=:order_id and md5(o.customer_id)=:customer_id";

    $order_where = array(":order_id" => $orderId,":customer_id"=>$customer_id);
    $order_row = $pdo->selectOne($order_sql, $order_where);
    $customer_id = $order_row['customer_id'];
    if(!empty($order_row['id'])){
        $od_sql = "SELECT od.start_coverage_period,od.end_coverage_period,ws.id as ws_id,ws.eligibility_date,ws.customer_id,ws.next_purchase_date,p.name,od.product_id,od.unit_price,od.qty,p.product_type,p.type,p.payment_type_subscription,od.fee_applied_for_product,od.plan_id,p.direct_product
        FROM order_details od
        JOIN website_subscriptions ws ON(od.plan_id=ws.plan_id)
        JOIN prd_main p ON(p.id=od.product_id AND p.is_deleted='N')
        WHERE od.order_id=:id AND ws.customer_id=:customer_id AND od.is_deleted='N' GROUP BY od.id";
        $od_where = array(":id" => $order_row['id'], ":customer_id"=>$order_row['customer_id']);
        $od_res = $pdo->select($od_sql,$od_where);
        
        $product_list = array();
        $product_list_normal = array();
        if(count($od_res) > 0){
            $lowest_effective_date = date("Y-m-d", strtotime($od_res[0]['eligibility_date']));
            $lowest_end_coverge_date = date("Y-m-d", strtotime($od_res[0]['end_coverage_period']));
          
            foreach ($od_res as $key => $value) {
                if(!in_array($value['product_id'],$product_list)){
                    array_push($product_list,$value['product_id']);
                }
                if(!in_array($value['product_id'],$product_list_normal) && $value['type'] !='Fees'){
                    array_push($product_list_normal,$value['product_id']);
                }
                 if(strtotime($value['eligibility_date']) < strtotime($lowest_effective_date)){
                      $lowest_effective_date = date("Y-m-d", strtotime($value['eligibility_date']));
                 }
                 if(strtotime($value['end_coverage_period']) < strtotime($lowest_end_coverge_date)){
                      $lowest_end_coverge_date = date("Y-m-d", strtotime($value['end_coverage_period']));
                 }
            }
       }
        if($order_row['is_renewal'] == 'N'){
            $ask_for_effactive_date = true;
            $lowest_effective_date = date("Y-m-d", strtotime("-1 days",strtotime($lowest_effective_date)));
            if(strtotime(date("Y-m-d")) >= strtotime($lowest_effective_date)){
                $ask_for_effactive_date = true;
            } else {
                $from_date = date("m/d/Y");
                $to_date = date("m/d/Y", strtotime($lowest_effective_date));
            }
        } else {
            $is_renewal = 'Y';
            $lowest_end_coverge_date = date("Y-m-d", strtotime("-1 days",strtotime($lowest_end_coverge_date)));
            if(strtotime(date("Y-m-d")) >= strtotime($lowest_end_coverge_date)){
                $is_regenerate_allow = false;
            } else {
                $from_date = date("m/d/Y",strtotime("+1 days",strtotime(date('Y-m-d'))));
                $to_date = date("m/d/Y", strtotime($lowest_end_coverge_date));
            }
        }
        $summaryList = $MemberEnrollment->get_coverage_period($product_list);
        $coverage_dates = array();
        foreach ($summaryList as $key => $coverage) {
            $coverage_dates[$coverage['product_id']]=$coverage['coverage_date'];	
        }   
    }   
}
$exJs = array('thirdparty/Birthdate/moment.min.js');

$template = 'regenerate_order.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>