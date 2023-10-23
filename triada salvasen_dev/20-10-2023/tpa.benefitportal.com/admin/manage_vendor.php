<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(65);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['link'] = 'vendors.php';
$breadcrumbes[1]['title'] = 'Vendors';
$breadcrumbes[2]['title'] = 'Manage Vendor';
$page_title = "Manage Vendor";

$vendor_fee_id = '';
$vendor_attachment_id = '';
$is_clone = "N";
$google_api = true;
$vendor_id = !empty($_GET['vendor_id']) ? $_GET['vendor_id'] : 0;
$is_clone = !empty($_GET['is_clone']) ? $_GET['is_clone'] : "N";

if(!empty($vendor_id)){
  $vendorSql = "SELECT * from prd_fees where is_deleted = 'N' AND md5(id) = :id";
  $vendorRow = $pdo->selectOne($vendorSql, array(":id" => $vendor_id));
 
  if(!empty($vendorRow)){
    $name=$vendorRow['name'];
    $display_id=$vendorRow['display_id'];
    $contact_fname=$vendorRow['contact_fname'];
    $contact_fname=($vendorRow['contact_lname']!='')?$contact_fname.' '.$vendorRow['contact_lname']:$contact_fname;
    $phone=$vendorRow['phone'];
    $email=$vendorRow['email'];
    $address=$vendorRow['address'];
    $address2=$vendorRow['address2'];
    $city=$vendorRow['city'];
    $state=$vendorRow['state'];
    $zipcode=$vendorRow['zipcode'];
    $taxid=$vendorRow['tax_id'];
  }

  $feeSql = "SELECT GROUP_CONCAT(id) as id
  FROM prd_main where is_deleted = 'N' AND md5(prd_fee_id) = :id
  ORDER BY id";
  $feeRow = $pdo->selectOne($feeSql, array(":id" => $vendor_id));

  $vendor_fee_id = !empty($feeRow['id']) ? $feeRow['id'] : 0;

  $attachmentSql = "SELECT *
  FROM fees_attachments
  WHERE is_deleted = 'N' AND type='Vendor' AND md5(prd_fee_id) = :id ORDER BY id";
  $attachmentRow = $pdo->select($attachmentSql, array(":id" => $vendor_id));

  $vendor_attachment_array = array();
  if(!empty($attachmentRow)){
    foreach ($attachmentRow as $key => $value) {
      array_push($vendor_attachment_array, $value['id']);
    }
    $vendor_attachment_id=implode(",", $vendor_attachment_array);
  }

  if($is_clone == 'Y'){
    $vendor_id=0;
    $name='';
    $display_id=get_vendor_id();
  }else{
      //************* Activity Code Start *************
      $description['ac_message'] =array(
      'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' Read Vendor ',
      'ac_red_2'=>array(
      'href'=> $ADMIN_HOST.'/manage_vendor.php?vendor_id='.$vendor_id,
      'title'=> $vendorRow['display_id'],
      ),
      );
      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $vendorRow['id'], 'Carrier',"Admin Read Vendor", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
      //************* Activity Code End *************
  }

  if($is_clone == 'Y'  && !empty($vendor_fee_id)){

    $fee_ids = array();
    $vendor_fees = explode(",", $vendor_fee_id);
    foreach ($vendor_fees as $key => $value) {
      $vendorFee = $pdo->selectOne("SELECT * FROM prd_main where id = :id", array(':id' => $value));
      
      $insert_params = array(
      'name' => $vendorFee['name'],
      'product_code' => get_vendor_fee_id(),
      'product_type' => $vendorFee['product_type'],
      'type' => 'Fees',
      'fee_type' => $vendorFee['fee_type'],
      
      'initial_purchase' => $vendorFee['initial_purchase'],
      'is_fee_on_renewal' => $vendorFee['is_fee_on_renewal'],
      'fee_renewal_type' => $vendorFee['fee_renewal_type'],
      'fee_renewal_count' => $vendorFee['fee_renewal_count'],
      'is_benefit_tier' => $vendorFee['is_benefit_tier'],
      'status' => $vendorFee['status'],
      );
      $new_fee_id = $pdo->insert('prd_main',$insert_params);
      array_push($fee_ids, $new_fee_id);

      $assignSql = "SELECT id,product_id FROM prd_assign_fees WHERE fee_id=:fee_id AND is_deleted='N'";
      $assignRow = $pdo->select($assignSql, array(":fee_id" => $vendorFee['id']));

      if(!empty($assignRow)){
        foreach ($assignRow as $key => $value) {
          $insert_params = array(
          'product_id' => $value['product_id'],
          'fee_id' => $new_fee_id,
          );
          $prd_assign_fees = $pdo->insert("prd_assign_fees", $insert_params);
        }
      }

      $feePlanSql = "SELECT id,plan_type,price_calculated_on,price_calculated_type,price,product_id,pricing_effective_date,pricing_termination_date
      FROM prd_matrix where product_id = :product_id AND is_deleted='N'";
      $feePlanRow = $pdo->select($feePlanSql, array(":product_id" => $vendorFee['id']));
      if(!empty($feePlanRow)){
        foreach ($feePlanRow as $key => $pm) {
          $plan_params = array(
          'product_id' => $new_fee_id,
          'plan_type' => $pm['plan_type'],
          'price_calculated_on' => $pm['price_calculated_on'],
          'price_calculated_type' => $pm['price_calculated_type'],
          'price' => $pm['price'],
          'pricing_effective_date' => $pm['pricing_effective_date'],
          'pricing_termination_date' => $pm['pricing_termination_date'],
          );
          $fee_plan_price = $pdo->insert("prd_matrix", $plan_params);
        }
      }
    }
    $vendor_fee_id=implode(",", $fee_ids);
  }

  if($is_clone == 'Y'  && !empty($attachmentRow)){
    $attachment_id_clone = array();
    foreach ($attachmentRow as $key => $attachments) {
      $attachment_params = array(
      'type' => 'Vendor',
      'file_name' => $attachments['file_name'],
      'file_path' => $attachments['file_path'],
      'file_type' => $attachments['file_type'],
      'is_deleted' => 'N',
      );
      $attachment_id = $pdo->insert("fees_attachments", $attachment_params);
    array_push($attachment_id_clone, $attachment_id);
  }

  $vendor_attachment_id=implode(",", $attachment_id_clone);

    $attachmentSql = "SELECT id,file_name
    FROM fees_attachments
    WHERE is_deleted = 'N' AND type='Vendor' AND id in ($vendor_attachment_id) ORDER BY id";
    $attachmentRow = $pdo->select($attachmentSql);
  }
}else{
  $display_id=get_vendor_id();
}

$exStylesheets = array('thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css'.$cache);

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array("thirdparty/ajax_form/jquery.form.js",'thirdparty/masked_inputs/jquery.inputmask.bundle.js');

$template = 'manage_vendor.inc.php';
include_once 'layout/end.inc.php';
?>