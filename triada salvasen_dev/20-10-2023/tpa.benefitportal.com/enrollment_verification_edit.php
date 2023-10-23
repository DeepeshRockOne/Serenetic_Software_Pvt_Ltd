<?php
include_once __DIR__ . '/includes/connect.php'; 


$md5_token_id = checkIsset($_GET['id']);
$product_list = checkIsset($_GET['product_ids']);
$customer_res = $resCustomerDep = $billing_data = array();
$same_as_personal = false;
if(!empty($md5_token_id)){

    $lead_quote_details_res = $pdo->selectOne("SELECT id,md5(customer_ids) as customer_id,order_ids,billing_info_param,plan_ids FROM lead_quote_details WHERE md5(token) = :token", array(":token" => $md5_token_id));

    $customer_id = $lead_quote_details_res['customer_id'];

    $customer_res = $pdo->selectOne("SELECT id,fname,lname,ssn,email,address,city,state,zip,birth_date,gender from customer where md5(id)=:id and is_deleted='N'",array(":id"=>$customer_id));

    if(empty($customer_res)){
        setNotifyError("quote_not_found");
        redirect($HOST . "/lead_quote_enrollment_response.php");
        exit();
    }

    if(!empty($product_list) && !empty($customer_res['id'])){
        $sqlCustomerDep="SELECT c.id,c.product_id,c.product_plan_id,c.relation,c.fname,c.lname,c.gender,c.email,c.ssn,c.birth_date
        FROM customer_dependent c WHERE c.customer_id=:customer_id AND c.product_id in ($product_list) GROUP BY c.id";
        $resCustomerDep=$pdo->select($sqlCustomerDep,array(":customer_id"=>$customer_res['id']));
        // pre_print($resCustomerDep);
    }

    if(!empty($lead_quote_details_res['billing_info_param'])) {
        $billing_data = json_decode($lead_quote_details_res['billing_info_param'], true);
    } else {
        $billing_data = $pdo->selectOne("SELECT *,AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as card_no_full, AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_account_number, AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_routing_number FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N' ORDER BY id DESC", array(":customer_id" => $customer_id));
    }
    
    if (!empty($billing_data)) {
        if (($customer_res['fname'] == $billing_data['fname']) && ($customer_res['lname'] == $billing_data['lname']) && ($customer_res['address'] == $billing_data['address']) && ($customer_res['city'] == $billing_data['city']) && ($customer_res['state'] == $billing_data['state']) && ($customer_res['zip'] == $billing_data['zip'])) {
            $same_as_personal = true;
        }
    }


    // pre_print($billing_data);
}else{
    setNotifyError("quote_not_found");
    redirect($HOST . "/lead_quote_enrollment_response.php");
    exit();
}

$exStylesheets = array('thirdparty/bootstrap-datepicker-master/css/datepicker.css');
$exJs = array('thirdparty/masked_inputs/jquery.maskedinput.min.js','thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js');
$template = 'enrollment_verification_edit.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';

?>