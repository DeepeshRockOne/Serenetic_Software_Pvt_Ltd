<?php
include_once __DIR__ . '/includes/connect.php'; 



$product_id =  checkIsset($_GET['product_id']);
$sponsor_id =  checkIsset($_GET['sponsor_id']);
$sponsorsCompany = '';

//EL8-1438 Remove Member Agreement Public URL
$agentData = $pdo->selectOne("SELECT id FROM customer WHERE is_deleted='N' AND type IN('Group','Agent') AND md5(id)=:id",array(":id"=>$sponsor_id));
if(empty($agentData)){
    redirect('404.php');
    exit();
}
if(!empty($product_id)){
    $description = $pdo->selectOne("SELECT description,id,product_id from prd_member_portal_information where md5(product_id)=:product_id and is_deleted='N' ORDER BY id DESC",array(":product_id"=>$product_id));

    if($description['description']){
      $smart_tags = get_user_smart_tags($description['product_id'],'product');
      if($smart_tags){
        foreach ($smart_tags as $key => $value) {
          $description['description'] = str_replace("[[" . $key . "]]", $value, $description['description']);
        }
      }
    }

}else{
  $member_terms = $pdo->selectOne('SELECT md5(id) as id,type,terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Member',":status"=>'Active')); 
  $sponsorsCompany = getname('customer_settings',$sponsor_id,'company','md5(customer_id)');
}

/*
$product_id =  checkIsset($_GET['product_id']);
$product_list =  checkIsset($_GET['product_list']);
$order_id = checkIsset($_GET['id']);
$prd_fee_id = checkIsset($_GET['prd_fee_id']);
$membership_fee_id = checkIsset($_GET['membership_fee_id']);
$display_member_terms = checkIsset($_GET['display_member_terms']);
$enrollmentLocation = isset($_POST['enrollmentLocation'])?$_POST['enrollmentLocation']:"";

/*if($enrollmentLocation=='groupSide'){

  $waive_checkbox = !empty($_POST['waive_checkbox']) ? $_POST['waive_checkbox'] : '';
  if(!empty($waive_checkbox)){

    $group_waive_product=isset($_POST['waive_products'])?$_POST['waive_products']:array();

    if(!empty($group_waive_product)){
      $group_product_list=$MemberEnrollment->getGroupWaiveProductList($waive_checkbox,$group_waive_product);
      $product_list = array_merge($product_list,$group_product_list);
    }
  }
}

if(!empty($order_id)){
    $product_ids = $pdo->selectOne("SELECT GROUP_CONCAT(distinct(product_id)) as products from order_details where md5(order_id)=:order_id",array(":order_id"=>$order_id));
    $terms_condition = $pdo->select("SELECT p.id,p.name,terms_condition FROM prd_terms_condition pt LEFT JOIN prd_main p ON(p.id=pt.product_id) where product_id IN(".$product_ids['products'].") and pt.is_deleted='N'  and p.is_deleted='N' ");
}

if(!empty($product_list)){
    
    $terms_condition = $pdo->select("SELECT p.id,p.name,terms_condition FROM prd_terms_condition pt LEFT JOIN prd_main p ON(p.id=pt.product_id) where product_id IN(".$product_list.") and pt.is_deleted='N'  and p.is_deleted='N' ");
}

if(!empty($product_list) || !empty($order_id) || !empty($display_member_terms)){
    $member_terms = $pdo->selectOne('SELECT md5(id) as id,type,terms FROM terms WHERE type=:type and status=:status',array(":type"=>'Member',":status"=>'Active')); 
}

if(!empty($product_id)){
    $description = $pdo->selectOne("SELECT description,id from prd_member_portal_information where md5(product_id)=:product_id and is_deleted='N' ORDER BY id DESC",array(":product_id"=>$product_id));
}

if(!empty($prd_fee_id)){
    $description = $pdo->selectOne("SELECT id,enrollment_desc as description from prd_descriptions where md5(product_id)=:product_id",array(":product_id"=>$prd_fee_id));
}

if(!empty($membership_fee_id)){
    $description = $pdo->selectOne("SELECT p.id ,p.benefits as description FROM prd_fees p JOIN prd_assign_fees pf ON(pf.prd_fee_id = p.id AND pf.is_deleted='N') WHERE md5(pf.fee_id)=:product_id AND p.is_deleted='N' GROUP BY pf.fee_id",array(":product_id"=>$membership_fee_id));
}
*/
$member_terms['terms'] = !empty($member_terms['terms']) ? str_replace("[[AGENT_ASSOCIATED_COMPANY]]",$sponsorsCompany,$member_terms['terms']) : '';
$template = 'verification_terms.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';

?>