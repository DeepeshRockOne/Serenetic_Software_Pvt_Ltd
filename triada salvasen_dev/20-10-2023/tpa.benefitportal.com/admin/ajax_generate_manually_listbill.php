<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/list_bill.class.php';
$validate = new Validation();
$ListBill = new ListBill();
$admin_id = $_SESSION['admin']['id'];
$admin_display_id=$_SESSION['admin']['display_id'];
$groupId = checkIsset($_POST['groupId']);
$listbillDate = checkIsset($_POST['lbDate']);
$lbDate = date('Y-m-d',strtotime($listbillDate));
$today = date('Y-m-d');

$validate->string(array('required' => true, 'field' => 'groupId', 'value' => $groupId), array('required' => 'Group ID is required'));
$validate->string(array('required' => true, 'field' => 'lbDate', 'value' => $listbillDate), array('required' => 'List Bill Date is required'));

if(!$validate->getError('groupId')){
  $resSql = $pdo->selectOne("SELECT id FROM customer WHERE is_deleted='N' AND type='Group' AND rep_id='" . $groupId . "'");
  $groupAutoId = !empty($resSql['id']) ? $resSql['id'] : 0;
  if(empty($groupAutoId)){
    $validate->setError("groupId","Please Enter Valid Group ID");
  }
}


if(!empty($lbDate) && (strtotime($lbDate) >= strtotime($today)) && $SITE_ENV == 'Live'){
  $validate->setError("lbDate","List Bill Date must be past date, it should not current or future date");
}

if ($validate->isValid()) {
  $is_regenerate = false;
  $list_bill_id = 0;
  $newMemberListBillSql = "SELECT GROUP_CONCAT(DISTINCT ws.last_list_bill_id) AS last_list_bill_id,l.id AS listBillId,l.customer_id AS listBillGroupId,l.class_id,l.list_bill_date,l.time_period_start_date
      FROM list_bills l
      JOIN customer c ON (l.customer_id = c.sponsor_id)
      JOIN website_subscriptions ws ON (c.id = ws.customer_id AND ws.payment_type='list_bill')
      WHERE l.status='open' AND l.customer_id = :customer_id AND l.list_bill_date = :list_bill_date AND l.is_deleted='N' ORDER BY l.id DESC";
  $newMemberListBillParams = array(":customer_id"=> $groupAutoId, ':list_bill_date'=>$lbDate);
  $newMemberListBillRes = $pdo->selectOne($newMemberListBillSql, $newMemberListBillParams);
  if(!empty($newMemberListBillRes)){
    $oldListBillId = explode(',',$newMemberListBillRes['last_list_bill_id']);
     if(in_array("0",$oldListBillId)){
       $is_regenerate = true; 
       $groupAutoId = $newMemberListBillRes['listBillGroupId']; 
       $list_bill_id = $newMemberListBillRes['listBillId'];
     }
  }
  
  $extra = array();
  $extra['type'] = 'manual';
  $extra['today'] = $lbDate;
  $list_bill_id_arr = $ListBill->generateListBill($is_regenerate, $groupAutoId, $list_bill_id, '', $extra);

  $response['status'] = "success";
} else {
  $response['status'] = "fail";
  $response['errors'] = $validate->getErrors();
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>