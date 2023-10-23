<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="icon-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Carriers';
$page_title = "Carriers";
 
$sch_params=array();
$incr=''; 
$SortBy = "t.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$per_page = isset($_GET['pages']) ? $_GET['pages'] : 10;

$admins = $pdo->select("SELECT id,display_id,CONCAT(fname,' ',lname) as admin_name FROM admin where status IN('Active')");

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
  $has_querystring = true;
  $SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
  $has_querystring = true;
  $currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = checkIsset($_GET['is_ajaxed']);
$is_export = !empty($_GET['is_export']) ? $_GET['is_export'] : ''; 
$member_id = checkIsset($_GET['member_id']);
$transaction_id = checkIsset($_GET['transaction_id']);
$admin_id = checkIsset($_GET['admin_id'],'arr');
$status = checkIsset($_GET['status'],'arr');
$orderIds = checkIsset($_GET['orderIds']); 
$reversal_type = checkIsset($_GET['reversal_type']); 
$reversal_method = checkIsset($_GET['reversal_method']); 

$orderIds = cleanSearchKeyword($orderIds); 
$transaction_id = cleanSearchKeyword($transaction_id); 
 
if (!empty($member_id)) { 
  $incr.=" AND c.id IN($member_id)";
}
if (!empty($reversal_type)) { 
  $incr.=" AND ro.return_type = :return_type";
  $sch_params[':return_type'] = $reversal_type;
}

if (!empty($reversal_method)) { 
  $refundType = array('CC','ACH','Admin');
  $reversal_method = $reversal_method != 'Cheque' ? "'".implode("','",$refundType)."'" : "'".$reversal_method."'";
  $incr.=" AND ro.refund_by IN($reversal_method)";
}

if(!empty($orderIds)){
  $orderIds = explode(',', trim($orderIds));
  $orderIds = array_map('trim',$orderIds);
  $incr .= " AND o.display_id IN ('".implode("','",$orderIds)."')";
}

if (!empty($admin_id)) { 
  $admin_id = implode(',', $admin_id);
  $incr.=" AND a.id IN($admin_id)";
}

if (!empty($status)) {
  $status = "'" . implode("','", $status) . "'";
  $incr.=" AND t.transaction_type IN ($status)";
}

if (!empty($transaction_id)) {
    $transaction_id = explode(',', trim($transaction_id));
    $transaction_id = array_map('trim', $transaction_id);
    $transaction_id = "'" . implode("','", makeSafe($transaction_id)) . "'";
    $incr .= " AND IF(t.transaction_status='Chargeback',tt.transaction_id IN($transaction_id),t.transaction_id IN($transaction_id))";
}

$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$order_fromdate = isset($_GET["order_fromdate"])?$_GET["order_fromdate"]:"";
$order_todate = isset($_GET["order_todate"])?$_GET["order_todate"]:"";
$order_added_date = isset($_GET["order_added_date"])?$_GET["order_added_date"]:"";

$reversal_range = isset($_GET['reversal_range'])?$_GET['reversal_range']:"";
$reversal_fromdate = isset($_GET["reversal_fromdate"])?$_GET["reversal_fromdate"]:"";
$reversal_todate = isset($_GET["reversal_todate"])?$_GET["reversal_todate"]:"";
$reversal_added_date = isset($_GET["reversal_added_date"])?$_GET["reversal_added_date"]:"";


if($join_range != ""){
  if($join_range == "Range" && $order_fromdate!='' && $order_todate!=''){
    $sch_params[':order_fromdate'] = date("Y-m-d",strtotime($order_fromdate));
    $sch_params[':order_todate'] = date("Y-m-d",strtotime($order_todate));
    $incr.=" AND DATE(o.created_at) >= :order_fromdate AND DATE(o.created_at) <= :order_todate";
  }else if($join_range == "Exactly" && $order_added_date!=''){
    $sch_params[':order_added_date'] = date("Y-m-d",strtotime($order_added_date));
    $incr.=" AND DATE(o.created_at) = :order_added_date";
  }else if($join_range == "Before" && $order_added_date!=''){
    $sch_params[':order_added_date'] = date("Y-m-d",strtotime($order_added_date));
    $incr.=" AND DATE(o.created_at) < :order_added_date";
  }else if($join_range == "After" && $order_added_date!=''){
    $sch_params[':order_added_date'] = date("Y-m-d",strtotime($order_added_date));
    $incr.=" AND DATE(o.created_at) > :order_added_date";
  }
}

if($reversal_range != ""){
  if($reversal_range == "Range" && $reversal_fromdate!='' && $reversal_todate!=''){
    $sch_params[':reversal_fromdate'] = date("Y-m-d",strtotime($reversal_fromdate));
    $sch_params[':reversal_todate'] = date("Y-m-d",strtotime($reversal_todate));
    $incr.=" AND DATE(t.created_at) >= :reversal_fromdate AND DATE(t.created_at) <= :reversal_todate";
  }else if($reversal_range == "Exactly" && $reversal_added_date!=''){
    $sch_params[':reversal_added_date'] = date("Y-m-d",strtotime($reversal_added_date));
    $incr.=" AND DATE(t.created_at) = :reversal_added_date";
  }else if($reversal_range == "Before" && $reversal_added_date!=''){
    $sch_params[':reversal_added_date'] = date("Y-m-d",strtotime($reversal_added_date));
    $incr.=" AND DATE(t.created_at) < :reversal_added_date";
  }else if($reversal_range == "After" && $reversal_added_date!=''){
    $sch_params[':reversal_added_date'] = date("Y-m-d",strtotime($reversal_added_date));
    $incr.=" AND DATE(t.created_at) > :reversal_added_date";
  }
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (checkIsset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'payment_reversal.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = (checkIsset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed){
  if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'transaction_export') {
      include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
      include_once dirname(__DIR__) . '/includes/export_report.class.php';
      $config_data = array(
        'user_id' => $_SESSION['admin']['id'],
        'user_type' => 'Admin',
        'user_rep_id' => $_SESSION['admin']['display_id'],
        'user_profile_page' => $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'timezone' => $_SESSION['admin']['timezone'],
        'file_type' => 'EXCEL',
        'report_location' => 'reversal_transaction_listing',
        'report_key' => 'admin_payment_reversal_transactions',
        'incr' => $incr,
        'sch_params' => $sch_params,
        'check_validation' => false,
      );
      $exportreport = new ExportReport(0,$config_data);
      $response = $exportreport->run();
      echo json_encode($response);
      exit();
  }

  try {
    //Please do changes on below file if applicable
    //adminPaymentReversalTransactions.php
    //operation29.com\admin\payment_reversal.php

    $transSummRes = array();
    $transSummSql="SELECT 
            SUM(IF(t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order',(t.new_business_total + t.renewal_total),0)) AS grossSaleAmt,
            COUNT(DISTINCT (CASE WHEN t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order' THEN t.id END)) AS grossSaleCnt,
            SUM(IF((t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order') AND t.payment_type='CC',(t.new_business_total + t.renewal_total),0)) AS grossSaleCCAmt,
            SUM(IF((t.transaction_type = 'Renewal Order' OR t.transaction_type = 'New Order' OR t.transaction_type = 'List Bill Order') AND t.payment_type='ACH',(t.new_business_total + t.renewal_total),0)) AS grossSaleACHAmt,

            SUM(if(t.transaction_type = 'Chargeback', t.debit, 0)) as cbAmt,
             COUNT(DISTINCT(CASE WHEN t.transaction_type = 'Chargeback' THEN t.id END)) AS cbTransCnt,
            SUM(if(t.transaction_type = 'Chargeback' AND t.payment_type='CC', t.debit, 0)) as cbCCAmt,
            SUM(if(t.transaction_type = 'Chargeback' AND t.payment_type='ACH', t.debit, 0)) as cbACHAmt,

            SUM(if(t.transaction_type = 'Refund Order', t.debit, 0)) as refundAmt,
             COUNT(DISTINCT(CASE WHEN t.transaction_type = 'Refund Order' THEN t.id END)) AS refundTransCnt,
            SUM(if(t.transaction_type = 'Refund Order' AND t.payment_type='CC', t.debit, 0)) as refundCCAmt,
            SUM(if(t.transaction_type = 'Refund Order' AND t.payment_type='ACH', t.debit, 0)) as refundACHAmt,

            SUM(if(t.transaction_type = 'Void Order', t.debit, 0)) as voidAmt,
             COUNT(DISTINCT(CASE WHEN t.transaction_type = 'Void Order' THEN t.id END)) AS voidTransCnt,
            SUM(if(t.transaction_type = 'Void Order' AND t.payment_type='CC', t.debit, 0)) as voidCCAmt,
            SUM(if(t.transaction_type = 'Void Order' AND t.payment_type='ACH', t.debit, 0)) as voidACHAmt,

            SUM(if(t.transaction_type = 'Payment Returned', t.debit, 0)) as returnedAmt,
             COUNT(DISTINCT(CASE WHEN t.transaction_type = 'Payment Returned' THEN t.id END)) AS returnedTransCnt,
            SUM(if(t.transaction_type = 'Payment Returned' AND t.payment_type='CC', t.debit, 0)) as returnedCCAmt,
            SUM(if(t.transaction_type = 'Payment Returned' AND t.payment_type='ACH', t.debit, 0)) as returnedACHAmt,

            SUM(IF((t.transaction_type = 'Refund Order' OR t.transaction_type = 'Chargeback' OR t.transaction_type = 'Payment Returned' OR t.transaction_type = 'Void Order'),t.new_business_total + t.renewal_total,0)) AS reversalsAmt

            FROM transactions t
            LEFT JOIN return_orders ro on(t.id = ro.transaction_id AND t.order_id = ro.order_id)
            LEFT JOIN orders o ON(o.id= t.order_id)
            LEFT JOIN admin a ON (a.id = ro.admin_id)
            LEFT JOIN customer c ON (c.id = o.customer_id)
            LEFT JOIN (
                SELECT id,transaction_id,order_id,transaction_status,customer_id FROM transactions WHERE transaction_status='Payment Approved'
              ) AS tt ON (tt.order_id=t.order_id AND t.transaction_status='Chargeback' AND t.customer_id=tt.customer_id)
            WHERE o.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND c.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') 
            AND t.transaction_type IN ('New Order','Renewal Order','List Bill Order','Refund Order','Chargeback','Payment Returned','Void Order')" . $incr . "
            ";
    $transSummRes = $pdo->selectOne($transSummSql, $sch_params);
    
  $grossSaleCnt = !empty($transSummRes['grossSaleCnt']) ? $transSummRes['grossSaleCnt'] : 0;
  $grossSaleAmt = !empty($transSummRes['grossSaleAmt']) ? $transSummRes['grossSaleAmt'] : 0;
  $grossSaleCCAmt = !empty($transSummRes['grossSaleCCAmt']) ? $transSummRes['grossSaleCCAmt'] : 0;
  $grossSaleACHAmt = !empty($transSummRes['grossSaleACHAmt']) ? $transSummRes['grossSaleACHAmt'] : 0;

  $cbAmt = !empty($transSummRes['cbAmt']) ? $transSummRes['cbAmt'] : 0;
  $cbTransCnt = !empty($transSummRes['cbTransCnt']) ? $transSummRes['cbTransCnt'] : 0;
  $cbCCAmt = !empty($transSummRes['cbCCAmt']) ? $transSummRes['cbCCAmt'] : 0;
  $cbACHAmt = !empty($transSummRes['cbACHAmt']) ? $transSummRes['cbACHAmt'] : 0;

  $refundAmt = !empty($transSummRes['refundAmt']) ? $transSummRes['refundAmt'] : 0;
  $refundTransCnt = !empty($transSummRes['refundTransCnt']) ? $transSummRes['refundTransCnt'] : 0;
  $refundCCAmt = !empty($transSummRes['refundCCAmt']) ? $transSummRes['refundCCAmt'] : 0;
  $refundACHAmt = !empty($transSummRes['refundACHAmt']) ? $transSummRes['refundACHAmt'] : 0;

  $voidAmt = !empty($transSummRes['voidAmt']) ? $transSummRes['voidAmt'] : 0;
  $voidTransCnt = !empty($transSummRes['voidTransCnt']) ? $transSummRes['voidTransCnt'] : 0;
  $voidCCAmt = !empty($transSummRes['voidCCAmt']) ? $transSummRes['voidCCAmt'] : 0;
  $voidACHAmt = !empty($transSummRes['voidACHAmt']) ? $transSummRes['voidACHAmt'] : 0;

  $returnedAmt = !empty($transSummRes['returnedAmt']) ? $transSummRes['returnedAmt'] : 0;
  $returnedTransCnt = !empty($transSummRes['returnedTransCnt']) ? $transSummRes['returnedTransCnt'] : 0;
  $returnedCCAmt = !empty($transSummRes['returnedCCAmt']) ? $transSummRes['returnedCCAmt'] : 0;
  $returnedACHAmt = !empty($transSummRes['returnedACHAmt']) ? $transSummRes['returnedACHAmt'] : 0;

  $reversalsAmt = !empty($transSummRes['reversalsAmt']) ? $transSummRes['reversalsAmt'] : 0;
  $netSaleAmt = $grossSaleAmt - $reversalsAmt;
    

    $sql="SELECT t.created_at, t.debit as refund_amount,o.id as o_id,o.display_id,CONCAT(a.fname,' ',a.lname) as admin_name,a.display_id as admin_display_id,CONCAT(c.fname,' ',c.lname) as member_name,t.transaction_type as status,CONCAT(c.fname,' ',c.lname) as member_name,c.rep_id,c.id as cust_id,c.type as cust_type,o.created_at as order_date,IF(ro.return_type != 'Partial','Full','Partial') as return_type,t.id as t_id,IF(t.transaction_status='Chargeback',tt.transaction_id,t.transaction_id) as transactionID,IF(ro.refund_by='Cheque','Check','Original Payment') as reversalMethod
              FROM transactions t
              LEFT JOIN return_orders ro on(t.id = ro.transaction_id and t.order_id = ro.order_id)
              LEFT JOIN orders o ON(o.id= t.order_id)
              LEFT JOIN admin a ON (a.id = ro.admin_id)
              LEFT JOIN customer c ON (c.id = o.customer_id)
              LEFT JOIN (
                SELECT id,transaction_id,order_id,transaction_status,customer_id FROM transactions WHERE transaction_status='Payment Approved'
              ) AS tt ON (tt.order_id=t.order_id AND t.transaction_status='Chargeback' AND t.customer_id=tt.customer_id)
              WHERE t.transaction_type in('Refund Order','Void Order','Chargeback','Payment Returned') AND o.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND c.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') " . $incr . "
              GROUP BY t.id ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
       
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
 
  include_once 'tmpl/payment_reversal.inc.php';
  exit;
}

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Payment';
$breadcrumbes[3]['title'] = 'Reversals';
$breadcrumbes[3]['link'] = 'payment_reversal.php';

$selectize = true;
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' viewed refunded orders ',
); 

activity_feed(3, $_SESSION['admin']['id'], 'Admin',0, 'orders','Viewed Refunded Orders', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

$page_title = "Payment Reversals";
$template = 'payment_reversal.inc.php';
include_once 'layout/end.inc.php';
?>