<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once __DIR__ . '/includes/connect.php';
require __DIR__ . '/libs/awsSDK/vendor/autoload.php';
include_once __DIR__ . '/includes/function.class.php';
include_once __DIR__ . "/UserTimezone.php";
global $S3_KEY,$S3_SECRET,$S3_REGION,$S3_BUCKET_NAME;

require __DIR__ . '/libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

$function_list = new functionsList();

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => $S3_REGION,
    'credentials'=>array(
        'key'=> $S3_KEY,
        'secret'=> $S3_SECRET
    )
]);
$s3Client->registerStreamWrapper();

$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : "";
$ws_id = isset($_GET['ws_id']) ? $_GET['ws_id'] : "";
if(empty($customer_id) || empty($ws_id)){
  redirect($HOST);
}
$sponsor_id = getname('customer',$customer_id,'sponsor_id','md5(id)');
$sponsor_row = $pdo->selectOne("SELECT c.id,c.type,IF(c.type='Group',cgs.billing_type,'individual') as billing_type FROM customer c LEFT JOIN customer_group_settings cgs ON (cgs.customer_id = c.id)WHERE c.is_deleted='N' AND c.id=:sponsor_id",array(":sponsor_id"=>$sponsor_id));
$websiteData = $pdo->selectOne("SELECT id,website_id,customer_id,agreement_id,parent_ws_id,created_at,payment_type,product_id,prd_plan_type_id FROM website_subscriptions where md5(id)=:id",array(":id"=>$ws_id));
$tax_pay_details = [];
if(!empty($websiteData['customer_id']) && !empty($websiteData['id'])){
  $tax_pay_details = $pdo->selectOne("SELECT * FROM tax_pay_deduction WHERE website_id=:ws_id  AND customer_id=:customer_id AND is_deleted='N'",array(':ws_id'=>$websiteData['id'],':customer_id'=>$websiteData['customer_id']));
}
$agreement_id = $member_agreement_content = '';
if(!empty($websiteData['id'])){
  $agreement_id = $websiteData['agreement_id'];
}
if(in_array(strtolower($websiteData['payment_type']),array('cc','ach')) || $sponsor_row['billing_type'] == 'individual'){
  $first_ord_sql = "SELECT t.id
      FROM order_details od
      JOIN transactions t ON(t.order_id = od.order_id)
      WHERE md5(od.website_id)=:website_id AND t.transaction_status IN('Payment Approved')
      ORDER BY t.id ASC";
    $firstPaidOrder = $pdo->selectOne($first_ord_sql,array(":website_id" => $ws_id));
if(empty($firstPaidOrder['id'])){
  setNotifyError('Agreement not downloaded due to Payment Not Approved');  
  $userType = isset($_GET['userType']) ?  $_GET['userType'] : '';
  $url = 'dashboard.php';
  if($userType == 'Admin'){
    $url = $ADMIN_HOST.'/members_details.php?id='.$customer_id;
  }else if($userType == 'Agent'){
    $url = $AGENT_HOST.'/members_details.php?id='.$customer_id;
  }else if($userType == 'Group'){
    $url = $GROUP_HOST.'/members_details.php?id='.$customer_id;
  }else if($userType == 'Member'){
    $url = $CUSTOMER_HOST.'/my_account.php?id='.$customer_id;
  }else if($userType == 'Policy'){
    $url = $ADMIN_HOST.'/policy_details.php?ws_id='.$ws_id;
  }
  redirect($url);
}
}
$agreement_incr = "";
if(!empty($agreement_id)) {
    $agreement_incr = " AND w.agreement_id=".$agreement_id;   
}
$activity_ids = '';
$blank_signature = false;
$admin_fee_price = 0;
if(!empty($agreement_id)) {
  $term_agreements = $pdo->selectOne("SELECT * FROM member_terms_agreement WHERE id=:id",array(":id" => $agreement_id));
  if(!empty($term_agreements['id'])){
    $agreementExtra = !empty($term_agreements['extra']) ? json_decode($term_agreements['extra'],true) : array();
    if(!empty($agreementExtra)){
      $activity_ids = !empty($agreementExtra['activity_ids']) ? $agreementExtra['activity_ids'] : '';
    }
    if($websiteData['parent_ws_id'] > 0 && empty($activity_ids) && empty($agreementExtra['activity_added'])) {
      $activity_ids = $function_list->add_activity_feed_for_member_terms(array($websiteData['id']),$websiteData['customer_id'],$agreement_id);
    }
  }
} else {
  $term_agreements = $pdo->selectOne("SELECT * FROM member_terms_agreement WHERE md5(customer_id) = :customer_id",array(":customer_id" => $customer_id));
}

/* Adding original agreement created date and ip addess*/
if(!empty($websiteData['id'])){
  $ord_sql = "SELECT mta.created_at,mta.ip_address
              FROM order_details od
              JOIN transactions t ON(t.order_id = od.order_id)
              JOIN member_terms_agreement mta on(mta.order_id = od.order_id)
              WHERE od.website_id=:website_id AND t.transaction_status IN('Payment Approved')
              ORDER BY t.id ASC";
  $tmp_term_agreements = $pdo->selectOne($ord_sql,array(":website_id" => $websiteData['id']));

  if(!empty($tmp_term_agreements)){
    if(isset($tmp_term_agreements['created_at']) && isset($tmp_term_agreements['ip_address'])){
      // if(!empty($tmp_term_agreements['created_at'])){
      //   $term_agreements['created_at'] = $tmp_term_agreements['created_at'];
      // }
      if(!empty($tmp_term_agreements['ip_address'])){
        $term_agreements['ip_address'] = $tmp_term_agreements['ip_address'];
      }
    }
  }else{
    $term_agreements['date_of_signature'] = $websiteData['created_at'];
  }
}

$ws_row = $pdo->select("SELECT CONCAT(s.fname,' ',s.lname) as agent_name, s.rep_id as agent_id,IF(p.name = '' AND p.product_type = 'ServiceFee','Service Fee',p.name) AS name,p.product_code,w.eligibility_date,w.termination_date,w.website_id,w.next_purchase_date,ppt.title as benefit_tier,w.price,DATE(w.created_at) as added_date,ce.fulfillment_date,p.id as p_id,pm.id as matrix_id,w.status,ce.tier_change_date,ce.process_status,c.id as customer_id,w.id as ws_id,w.last_order_id,p.type,p.product_type,pc.title as category,w.payment_type,s.type as sponsor_type
        FROM customer c
        JOIN website_subscriptions w ON (w.customer_id=c.id)
        JOIN customer_enrollment ce ON (ce.website_id=w.id)
        JOIN customer s ON (s.id=ce.sponsor_id)
        JOIN prd_main p ON (p.id=w.product_id)
        JOIN prd_matrix pm ON (pm.product_id=w.product_id AND w.plan_id = pm.id)
        LEFT JOIN prd_plan_type ppt ON (ppt.id = w.prd_plan_type_id)
        LEFT JOIN prd_category pc on(pc.id = p.category_id AND pc.is_deleted = 'N') 
        WHERE c.status in ('Active','Inactive','Hold') AND c.type='Customer' AND md5(w.customer_id) = :customer_id $agreement_incr GROUP BY w.id ORDER BY p.type DESC,p.name",array(':customer_id' => $customer_id));

$total_premium = $pdo->selectOne("SELECT SUM(w.price) as total
        FROM customer c
        JOIN website_subscriptions w ON (w.customer_id=c.id)
        JOIN customer_enrollment ce ON (ce.website_id=w.id)
        JOIN customer s ON (s.id=ce.sponsor_id)
        JOIN prd_main p ON (p.id=w.product_id)
        JOIN prd_matrix pm ON (pm.product_id=w.product_id AND w.plan_id = pm.id)
        LEFT JOIN prd_plan_type ppt ON (ppt.id = w.prd_plan_type_id)
        WHERE c.status in ('Active','Inactive','Hold') AND c.type='Customer' AND md5(w.customer_id) = :customer_id $agreement_incr ORDER BY p.type DESC,p.name",array(':customer_id' => $customer_id));

$customer_billing = $pdo->selectOne("SELECT * FROM order_billing_info WHERE order_id=:order_id ORDER BY id DESC",array(":order_id" => $term_agreements['order_id']));
if(empty($customer_billing)) {
    $customer_billing = $pdo->selectOne("SELECT * FROM customer_billing_profile WHERE is_default = 'Y' AND is_deleted = 'N' AND md5(customer_id) = :customer_id",array(":customer_id" => $customer_id));
}

$dependent_sql = "SELECT cd.fname,cd.lname,cd.gender,cd.birth_date,cd.relation
                  FROM customer_dependent cd
                  JOIN website_subscriptions w ON (w.id=cd.website_id)
                  WHERE md5(w.customer_id)=:customer_id AND cd.is_deleted = 'N' $agreement_incr
                  GROUP BY cd.cd_profile_id";
$dependents = $pdo->select($dependent_sql,array(":customer_id" => $customer_id));

$term_agreements_id = $term_agreements['member_terms_id'];

if(!empty($term_agreements_id)){
    $member_term_sql = "SELECT terms,id FROM member_terms WHERE  id=:term_id";
    $member_term_res = $pdo->selectOne($member_term_sql,array(":term_id" => $term_agreements_id));
    $member_agreement_content = $member_term_res['terms'];
}

if($term_agreements['agreement_file']){
    $result = $s3Client->getObject(array(
      'Bucket' => $S3_BUCKET_NAME,
      'Key'    => $term_agreements['agreement_file']
    ));
    $term_agreements['agreement'] = htmlspecialchars_decode($result['Body']);
}

$cust_row = $pdo->selectOne("SELECT c.fname,c.lname,c.rep_id,c.email,c.cell_phone,c.created_at,c.gender,c.address,c.address_2,c.city,c.state,c.zip,cs.signature_file,cs.ip_address,c.birth_date,c.sponsor_id FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) where md5(c.id) = :customer_id",array(":customer_id" => $customer_id));
$signature_data = '';
if(!empty($term_agreements['signature_img'])){
  try{
      $signature_data = $function_list->getSignatureFromS3Bucket($term_agreements['signature_img']);
  }catch(Exception $e){
  }
}

$sponsorsCompany = getname('customer_settings',$cust_row['sponsor_id'],'company','customer_id');
$on_enrollment = json_decode($term_agreements['primary_details'],true);

if(!empty($member_agreement_content)) {
  $member_terms = array();
  $member_terms['terms'] = str_replace("[[AGENT_ASSOCIATED_COMPANY]]",$sponsorsCompany,$member_agreement_content);
} else {
  $member_terms = $pdo->selectOne('SELECT md5(id) as id,type,terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Member',":status"=>'Active')); 
  $member_terms['terms'] = !empty($member_terms['terms']) ? str_replace("[[AGENT_ASSOCIATED_COMPANY]]",$sponsorsCompany,$member_terms['terms']) : '';  
}

//customer custom quetion
$customerCustomQuesSql = "SELECT * FROM member_agreement_custom_question WHERE customer_id=:customer_id AND agreement_id=:agreement_id ORDER BY FIELD('enrollee_type','primary','spouse','child'),dependent_id,question_id";
$customerCustomQueRes = $pdo->selectGroup($customerCustomQuesSql,array(":customer_id" => $websiteData['customer_id'],":agreement_id"=>$term_agreements['id']),'enrollee_type');
//customer custom quetion

// Ammendment Activity Feed
if(!empty($activity_ids)) {
    $ammendmentActivitySql = "SELECT * FROM activity_feed WHERE id IN(".$activity_ids.") ORDER BY id DESC";
    $fetch_rows = $pdo->select($ammendmentActivitySql);
    $total_rows = count($fetch_rows);
}
// Ammendment Activity Feed

$blank_signature = false;
if($websiteData['parent_ws_id'] > 0) {
    $blank_signature = true;
}

ob_start();
include_once 'tmpl/policy_document.inc.php';

$pdf_html_code = ob_get_clean();
$pdf_html_code = preg_replace("/(<input.*?type\s*?=[\s*?\'\"]checkbox[\'\"].*?)>/",'<img src="'.$HOST.'/images/icons/pdf_gray_checkbox.png" style="width: 15px;background-color: #000000;"> ',$pdf_html_code);
  
  require_once __DIR__ . '/libs/mpdf/vendor/autoload.php';
  // require_once "libs/mpdf/src/Mpdf.php";
  $mpdf = new \Mpdf\Mpdf();
  $stylesheet = file_get_contents('css/mpdf_common_style.css');
  $mpdf->WriteHTML($stylesheet,1);
  $mpdf->WriteHTML($pdf_html_code,2);
  $mpdf->use_kwt = true;
  $mpdf->shrink_tables_to_fit = 1;
// Output a PDF file directly to the browser
  

  header('Content-type:application/pdf');
  header('Content-disposition: attachment;filename="Member_Agreement_' . date('Ymd') . '.pdf"');
  echo $mpdf->Output("Member_Agreement_" . date('Ymd') . ".pdf","D");
exit;
?>