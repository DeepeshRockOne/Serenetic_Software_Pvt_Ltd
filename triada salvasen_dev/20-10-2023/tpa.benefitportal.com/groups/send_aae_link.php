<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$quote_id = $_GET['quote_id']; 
$lq_sql = "SELECT id,customer_ids as customer_id,order_ids,token,lead_id  FROM lead_quote_details WHERE md5(id)=:id";
$lq_where = array(":id"=>$quote_id);
$lq_row = $pdo->selectOne($lq_sql,$lq_where);
if (empty($lq_row)) {
    setNotifyError('AAE Not Found.');
    echo '<script type="text/javascript">window.parent.location.href=window.parent.location.href;</script>';
    exit;
}
$group_id = $_SESSION['groups']['id'];
$sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
$resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$group_id));
$group_billing_method = '';
if(!empty($resBillingType)){
    $group_billing_method = $resBillingType['billing_type'];
}

$lead_row = $pdo->selectOne("SELECT * FROM leads c WHERE c.id=:id", array(":id" => $lq_row['lead_id']));

if($group_billing_method=='individual'){
    $order_row = $pdo->selectOne("SELECT id FROM orders WHERE status IN('Pending Validation','Payment Declined','Pending Application') AND customer_id=:customer_id AND id=:order_id", array(":customer_id" => $lq_row['customer_id'], ":order_id" => $lq_row['order_ids']));
}else{
    $order_row = $pdo->selectOne("SELECT id FROM group_orders WHERE status IN('Pending Validation','Payment Declined','Pending Application') AND customer_id=:customer_id AND id=:order_id", array(":customer_id" => $lq_row['customer_id'], ":order_id" => $lq_row['order_ids']));
}
if (empty($order_row)) {
    setNotifyError('AAE Not Found.');
    echo '<script type="text/javascript">window.parent.location.href=window.parent.location.href;</script>';
    exit;
}

$sms_content = '';
$email_subject = '';
$email_content = '';

$trigger_res = $pdo->selectOne("SELECT * FROM triggers WHERE id = 84");
if (!empty($trigger_res)) {
    $sms_content = $trigger_res['sms_content'];
    $email_subject = $trigger_res['email_subject'];
    $email_content = html_entity_decode($trigger_res['email_content']);
}

$exStylesheets = array(
    'thirdparty/summernote-master/dist/summernote.css',
);
$exJs = array(
    'thirdparty/summernote-master/dist/popper.js',
    'thirdparty/summernote-master/dist/summernote.js',
    'thirdparty/vue-js/vue.min.js'
);

$layout = "iframe.layout.php";
$template = "send_aae_link.inc.php";
include_once 'layout/end.inc.php';
?>
