<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$page_title = "Advance Commissions Builder";

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Commissions";
$breadcrumbes[1]['class'] = "";
$breadcrumbes[2]['title'] = "Advances";
$breadcrumbes[2]['link'] = "advances_commission.php";
$breadcrumbes[3]['title'] = "+ Advance Commission";

$advRuleId = !empty($_GET['advRuleId']) ? $_GET['advRuleId'] : 0;
$advFeeIds = '';
$advRes = array();
$feeRow = array();

$displayId=get_advance_comm_id();
$status = "Active";

if(!empty($advRuleId)){
  $advSql = "SELECT id,display_id,charged_to,rule_type,status
  				FROM prd_fees WHERE setting_type='ServiceFee' AND is_deleted = 'N' AND md5(id) = :id";
  $advRes = $pdo->selectOne($advSql, array(":id" => $advRuleId));

  if(!empty($advRes['id'])){
    $displayId = checkIsset($advRes['display_id']);
    $status = checkIsset($advRes['status']);

    $feeSql = "SELECT GROUP_CONCAT(id) as id
    FROM prd_main WHERE is_deleted = 'N' AND prd_fee_id = :id
    ORDER BY id";
    $feeRow = $pdo->selectOne($feeSql, array(":id" => $advRes['id']));
    $advFeeIds = !empty($feeRow['id']) ? $feeRow['id'] : 0;

    //************* Activity Code Start *************
      $description['ac_message'] =array(
      'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' Read Advance Commission Rule ',
      'ac_red_2'=>array(
      'href'=> $ADMIN_HOST.'/add_member_advance_global_rule.php?advRuleId='.md5($advRes['id']),
      'title'=> $advRes['display_id'],
      ),
      );
      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $advRes['id'], 'prd_fees',"Admin Read Advance Commission Rule", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    //************* Activity Code End *************
  }
}



if (isset($_GET['delete']) && isset($_GET['feeId'])) {
  $feeId = $_GET['feeId'];
  $response = array();
  if (!empty($feeId)) {

    $selPrdFees = "SELECT pm.id as advFeeId,pm.product_code as display_id
                  FROM prd_main pm
                  WHERE md5(pm.id) =:id AND pm.is_deleted='N'";
    $resPrdFees = $pdo->selectOne($selPrdFees,array(':id'=>$feeId));

    if(!empty($resPrdFees)){

      $advFeeId = $resPrdFees['advFeeId'];

      $insParams = array("is_deleted"=>'Y');
      $updWhere=array(
            'clause'=>'id=:id',
            'params'=>array(":id"=>$advFeeId)
        );
        $pdo->update("prd_main",$insParams,$updWhere);

      $insParams = array("is_deleted"=>'Y');
      $updWhere=array(
            'clause'=>'product_id=:product_id AND is_deleted="N"',
            'params'=>array(":product_id"=>$advFeeId)
        );
      $pdo->update("prd_matrix",$insParams,$updWhere);

      $insParams = array("is_deleted"=>'Y');
      $updWhere=array(
            'clause'=>'fee_id=:fee_id AND is_deleted="N"',
            'params'=>array(":fee_id"=>$advFeeId)
        );
      $pdo->update("prd_assign_fees",$insParams,$updWhere);

      //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' Deleted Advance Commission Fee',
          'ac_red_2'=>array(
            //'href'=> '',
            'title'=>$resPrdFees['display_id'],
          ),
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $advFeeId, 'prd_main','Admin Deleted Advance Commission Fee', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
      //************* Activity Code End *************

      $response['status']="success";
      $response['message']="Advance Commission Fee Deleted Successfully";
    }else{
      $response['status'] = 'fail';
      $response['message'] = "Advance Commission Fee Not Found";
    }
  }
  echo json_encode($response);
  exit();
}

$template = 'add_member_advance_global_rule.inc.php';
include_once 'layout/end.inc.php';
?>