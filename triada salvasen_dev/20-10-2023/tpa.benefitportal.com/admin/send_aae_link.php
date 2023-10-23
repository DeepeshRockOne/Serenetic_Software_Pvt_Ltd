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
$lead_row = $pdo->selectOne("SELECT * FROM leads c WHERE c.id=:id", array(":id" => $lq_row['lead_id']));

$cust_row = $pdo->selectOne("SELECT * FROM customer c WHERE c.id=:id", array(":id" => $lq_row['customer_id']));
$sponsor_type = getname('customer',$cust_row['sponsor_id'],'type');
if(strtolower($sponsor_type) == 'group'){
    $group_id = $cust_row['sponsor_id'];
    $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
    $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$group_id));
    $group_billing_method = '';
    if(!empty($resBillingType)){
        $group_billing_method = $resBillingType['billing_type'];
    }
    
    if($group_billing_method=='individual'){
        $order_row = $pdo->selectOne("SELECT id FROM orders WHERE status IN('Pending Validation','Payment Declined','Pending Application') AND customer_id=:customer_id AND id=:order_id", array(":customer_id" => $lq_row['customer_id'], ":order_id" => $lq_row['order_ids']));
    }else{
        $order_row = $pdo->selectOne("SELECT id FROM group_orders WHERE status IN('Pending Validation','Payment Declined','Pending Application') AND customer_id=:customer_id AND id=:order_id", array(":customer_id" => $lq_row['customer_id'], ":order_id" => $lq_row['order_ids']));
    }
}else{
    $order_row = $pdo->selectOne("SELECT id FROM orders WHERE status IN('Pending Validation','Payment Declined','Pending Application') AND customer_id=:customer_id AND id=:order_id", array(":customer_id" => $lq_row['customer_id'], ":order_id" => $lq_row['order_ids']));
}

if (empty($lead_row) || empty($cust_row) || empty($order_row)) {
    setNotifyError('AAE Not Found.');
    echo '<script type="text/javascript">window.parent.location.href=window.parent.location.href;</script>';
    exit;
}

$url_link = $HOST . '/quote/enroll_varification/'. $lq_row['token'];
$url_params = array(
    'dest_url' => $url_link,
    'agent_id' => $cust_row['sponsor_id'],
    'customer_id' => $cust_row['id'],
);
$aae_link = get_short_url($url_params);

$sms_content = '';
$email_subject = '';
$email_content = '';

$trigger_res = $pdo->selectOne("SELECT * FROM triggers WHERE id = 84");
if (!empty($trigger_res)) {
    $sms_content = $trigger_res['sms_content'];
    $email_subject = $trigger_res['email_subject'];
    $email_content = html_entity_decode($trigger_res['email_content']);
}


$description['ac_message'] = array(
    'ac_red_1' => array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' => ' read AAE link of ' . $lead_row['fname'] . ' ' . $lead_row['lname'] . ' (',
    'ac_red_2' => array(
        'href' => 'lead_details.php?id=' . md5($lead_row['id']),
        'title' => $lead_row['lead_id'],
    ),
    'ac_message_2' => ')',
);
$desc = json_encode($description);
activity_feed(3, $_SESSION['admin']['id'],'Admin',$lead_row['id'], 'Lead','Admin Read AAE Link.', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);

$exStylesheets = array(
    // 'thirdparty/summernote-master/dist/summernote.css',
);
$exJs = array('thirdparty/clipboard/clipboard.min.js', 'thirdparty/ckeditor/ckeditor.js', 'thirdparty/vue-js/vue.min.js');

$layout = "iframe.layout.php";
$template = "send_aae_link.inc.php";
include_once 'layout/end.inc.php';
?>
