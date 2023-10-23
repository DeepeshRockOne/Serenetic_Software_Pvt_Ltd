<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
 
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Carriers";
$breadcrumbes[1]['link'] = "carrier.php";
$breadcrumbes[2]['title'] = "+ Carriers";
$breadcrumbes[2]['link'] = "manage_carrier.php"; 

$carrier_fee_id='';
$carrier_id=0;
$is_clone = "N";
if (!empty($_GET['carrier_id'])) {
	$carrier_id = $_GET['carrier_id'];
}
if (!empty($_GET['is_clone'])) {
  $is_clone = $_GET['is_clone'];
} 

if (!empty($carrier_id)) {

  $carrierSql = "SELECT * from prd_fees where is_deleted = 'N' AND md5(id) = :id";
  $carrierRow = $pdo->selectOne($carrierSql, array(":id" => $carrier_id));

  if(!empty($carrierRow)){
    $name=$carrierRow['name'];
    $display_id=$carrierRow['display_id'];
    $contact_fname=$carrierRow['contact_fname'];
    $contact_fname=($carrierRow['contact_lname']!='')?$contact_fname.' '.$carrierRow['contact_lname']:$contact_fname;
    $phone=$carrierRow['phone'];
    $email=$carrierRow['email'];
    $status=$carrierRow['status'];
    $appointments=$carrierRow['use_appointments'];
  }   

  $feeSql = "SELECT GROUP_CONCAT(id) as id from prd_main where is_deleted = 'N' AND md5(prd_fee_id) = :id ORDER BY id";
  $feeRow = $pdo->selectOne($feeSql, array(":id" => $carrier_id));
  if(!empty($feeRow) && !empty($feeRow['id'])){
    $carrier_fee_id=$feeRow['id'];
  }else{
  	$carrier_fee_id=$feeRow['id'];
  }

  if($is_clone == 'Y'){
    $carrier_id=0;
    $name='';
    $display_id=get_carrier_id();
  }else{
    //************* Activity Code Start *************
      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Read Carrier ',
        'ac_red_2'=>array(
          'href'=> $ADMIN_HOST.'/manage_carrier.php?carrier_id='.$carrier_id,
          'title'=> $carrierRow['display_id'],
        ),
      );  

      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $carrierRow['id'], 'Carrier',"Admin Read Carrier", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    //************* Activity Code End *************
  }

  if($is_clone == 'Y'  && !empty($carrier_fee_id)){
    $fee_ids = array();
    $carrier_fees = explode(",", $carrier_fee_id);

    foreach ($carrier_fees as $key => $value) {

      $carrierFee = $pdo->selectOne("SELECT * FROM prd_main where id = :id", array(':id' => $value));

      $insert_params = array(
        'name' => $carrierFee['name'],
        'product_code' => get_carrier_fee_id(),
        'product_type' => $carrierFee['product_type'],
        'type' => 'Fees',
        'fee_type' => $carrierFee['fee_type'],
        'initial_purchase' => $carrierFee['initial_purchase'],
        'is_fee_on_renewal' => $carrierFee['is_fee_on_renewal'],
        'fee_renewal_type' => $carrierFee['fee_renewal_type'],
        'fee_renewal_count' => $carrierFee['fee_renewal_count'],
        'is_benefit_tier' => $carrierFee['is_benefit_tier'],
        'status' => $carrierFee['status'],
      );

      $new_fee_id = $pdo->insert('prd_main',$insert_params);
      array_push($fee_ids, $new_fee_id);

      $assignSql = "SELECT id,product_id FROM prd_assign_fees where fee_id=:fee_id AND is_deleted='N'";
      $assignRow = $pdo->select($assignSql, array(":fee_id" => $carrierFee['id']));

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
      $feePlanRow = $pdo->select($feePlanSql, array(":product_id" => $carrierFee['id']));

      if(!empty($feePlanRow)){
        foreach ($feePlanRow as $key => $pm) {
          $plan_params = array(
            'product_id' => $new_fee_id,
            'plan_type' => $pm['plan_type'],  
            'price_calculated_on' => $pm['price_calculated_on'],  
            'price_calculated_type' => $pm['price_calculated_type'],  
            'pricing_effective_date' => $pm['pricing_effective_date'],  
            'pricing_termination_date' => $pm['pricing_termination_date'],  
            'price' => $pm['price'],
          );
          $fee_plan_price = $pdo->insert("prd_matrix", $plan_params);           
        }
      }

    }

    $carrier_fee_id=implode(",", $fee_ids);
  }
}else{
  $display_id=get_carrier_id();
} 
 
 
$exJs = array(
  "thirdparty/ajax_form/jquery.form.js",
  'thirdparty/masked_inputs/jquery.inputmask.bundle.js' );

$template = 'manage_carrier.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>