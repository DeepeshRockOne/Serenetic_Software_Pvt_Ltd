<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Payment';
$breadcrumbes[2]['title'] = 'Transaction';
$breadcrumbes[2]['link'] = 'payment_transaction.php';



// Read transaction activity code start
	$description['ac_message'] =array(
	  'ac_red_1'=>array(
	    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	    'title'=>$_SESSION['admin']['display_id'],
	  ),
	  'ac_message_1' =>' viewed transactions ',
	); 

	activity_feed(3, $_SESSION['admin']['id'], 'Admin',0, 'transactions','Viewed Transactions', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
// Read transaction activity code ends

$tableIncr = '';
$incr = '';
$schParams = array();
$SortBy = "t.id";
$SortDirection = "DESC";
$currSortDirection = "DESC";

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
$transIds = !empty($_GET['transIds']) ? $_GET['transIds'] : "";
$orderIds = !empty($_GET['orderIds']) ? $_GET['orderIds'] : '';
$order_id = !empty($_GET['order_id']) ? $_GET['order_id'] : '';
$is_from_all_orders = !empty($_GET['is_from_all_orders']) ? $_GET['is_from_all_orders'] : 'N';
$member_id = !empty($_GET['member_id']) ? explode(",", $_GET['member_id']) : "";
$enrollingAgents = !empty($_GET['enrollingAgents']) ? explode(",", $_GET['enrollingAgents']) : "";
$treeAgents = isset($_GET['treeAgents']) ? $_GET['treeAgents'] : array();
$paymentType = checkIsset($_GET['paymentType'],'arr');
$saleType = checkIsset($_GET['saleType'],'arr');
$lastCcAchNo = checkIsset($_GET['lastCcAchNo']);
$products = checkIsset($_GET['products'],'arr');
$processorIds = checkIsset($_GET['processorIds'],'arr');
$transStatus = checkIsset($_GET['transStatus'],'arr');

$join_range = checkIsset($_GET['join_range']);
$fromdate = checkIsset($_GET["fromdate"]);
$todate = checkIsset($_GET["todate"]);
$today = date("m/d/Y");
$added_date = !empty($_GET["added_date"]) ? $_GET["added_date"] : $today;
$viewTrans = !empty($_GET["viewTrans"]) ? $_GET["viewTrans"] : 'todayTrans';

if($is_from_all_orders == 'Y'){
  $viewTrans='allTrans';
}

if($viewTrans == "todayTrans" && empty($join_range)){
  $join_range = "Exactly";
}

$orderIds = cleanSearchKeyword($orderIds); 
$transIds = cleanSearchKeyword($transIds); 
$lastCcAchNo = cleanSearchKeyword($lastCcAchNo); 
 
if(!empty($transIds)){
  $transIds = str_replace(" ", "", $transIds);
  $transIds = explode(',', $transIds);
  $transIds = "'" . implode("','", $transIds) . "'";
  $incr .= " AND t.transaction_id IN ($transIds)";
}

if(!empty($orderIds)){
  $orderIds = explode(',', trim($orderIds));
  $orderIds = array_map('trim',$orderIds);
  $incr .= " AND o.display_id IN ('".implode("','",$orderIds)."')";
}

if(!empty($member_id)){
  $member_id = array_map('trim',$member_id);
  $incr .= " AND c.id IN (".implode(",",$member_id).")";
}

if(!empty($enrollingAgents)){
  $incr .= " AND c.sponsor_id IN (".implode(",",$enrollingAgents).")";
}

if(!empty($treeAgents)){
  $incr .= " AND (s.id IN(".implode(",",$treeAgents).") OR (s.sponsor_id IN (".implode(",",$treeAgents).") AND scs.agent_coded_id = 1)) ";
  $tableIncr .= 'JOIN customer_settings scs ON(scs.customer_id=s.id)';
}

// if (!empty($treeAgents)) {
//   if (count($treeAgents) > 0) {
//       $incr .= " AND (";
//       foreach ($treeAgents as $key => $value) {
//           if (end($treeAgents) == $value) {
//               $incr .= " c.upline_sponsors LIKE '%," . $value . ",%'";
//           } else {
//               $incr .= " c.upline_sponsors LIKE '%," . $value . ",%' OR";
//           }
//       }
//       $incr .= ")";
//   }
// }

if(!empty($paymentType)){
  $incr .= " AND t.payment_type IN ('".implode("','",$paymentType)."')";
}

if(!empty($saleType)){
  $incr .= " AND o.is_renewal IN ('".implode("','",$saleType)."')";
}

if($lastCcAchNo != "") {
  $incr .= " AND obi.last_cc_ach_no = :lastCcAchNo";
  $schParams[':lastCcAchNo'] = $lastCcAchNo;
}

if(!empty($products)) {
  $prdIds = implode(",",$products);
  $selProduct = "SELECT GROUP_CONCAT(DISTINCT id) as productsIds FROM prd_main WHERE (id IN($prdIds) OR parent_product_id IN($prdIds)) AND is_deleted='N'";
  $resProduct = $pdo->selectOne($selProduct);

  $productsIds = checkIsset($resProduct["productsIds"]);
  $incr .= " AND st.product_id IN ($productsIds)";
}

if(!empty($processorIds)) {
  $incr .= " AND t.payment_master_id IN (".implode(",",$processorIds).")";
}

if(!empty($transStatus)) {
  $incr .= " AND t.transaction_status IN ('".implode("','",$transStatus)."')";
}

if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $schParams[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $schParams[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(t.created_at) >= :fromdate AND DATE(t.created_at) <= :todate";
  }else if($join_range == "Exactly" && $added_date!=''){
    $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(t.created_at) = :added_date";
  }else if($join_range == "Before" && $added_date!=''){
    $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(t.created_at) < :added_date";
  }else if($join_range == "After" && $added_date!=''){
    $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(t.created_at) > :added_date";
  }
}

if (count($schParams) > 0) {
  $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
  'results_per_page' => $per_page,
  'url' => 'payment_transaction.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $schParams,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
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
        'report_location' => 'transaction_listing',
        'report_key' => 'admin_payment_transaction_report',
        'incr' => $incr,
        'sch_params' => $schParams,
        'check_validation' => false,
      );
      $exportreport = new ExportReport(0,$config_data);
      $response = $exportreport->run();
      echo json_encode($response);
      exit();
  }

  try {
  	$selTrans = "SELECT t.id as transTblId,t.transaction_status as transStatus,o.status as odrStatus, o.customer_id as odrMbrId, o.id as odrID, o.display_id as odrDispId, o.is_renewal as saleType, o.grand_total as odrTotal, o.created_at as odrDate, c.rep_id as mbrDispId,IF(c.type='Group',c.business_name,CONCAT(c.fname,' ',c.lname)) as mbrName,s.business_name as sponBusinessName, c.state as mbrState, s.rep_id as agentDispId, CONCAT(s.fname,' ',s.lname) as agentName, c.sponsor_id,t.id as transId,o.order_count as covPeriod,s.id as agentId,t.reason as transNote,IF(t.transaction_status='Chargeback',tt.transaction_id,t.transaction_id) as transId,pym.name as processorName,
      IFNULL(MIN(DISTINCT CASE WHEN od.product_type!='Fees'  THEN od.renew_count END),0) AS minCov,
      IFNULL(MAX(DISTINCT CASE WHEN od.product_type!='Fees'  THEN od.renew_count END),0) AS maxCov,
      c.id as mbrId,t.created_at as transDate,
      IF(t.order_type='Credit', t.credit,t.debit) as transTotal,
      l.id as leadId,l.lead_id as leadDispId,CONCAT(l.fname,' ',l.lname) as leadName,c.status as mbrStatus,c.type as user_type
	       	FROM transactions t
	        JOIN orders o ON(t.order_id = o.id)
	        JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
	        JOIN customer c ON (c.id = o.customer_id)
	        JOIN customer s ON (c.sponsor_id = s.id)
          $tableIncr
          LEFT JOIN order_billing_info obi ON(obi.order_id = o.id)
	        LEFT JOIN payment_master pym ON(pym.id=t.payment_master_id)
          LEFT JOIN sub_transactions st ON(st.transaction_id=t.id)
          LEFT JOIN leads l ON(l.customer_id=c.id AND l.is_deleted='N')
          LEFT JOIN (
            SELECT id,transaction_id,order_id,transaction_status,customer_id FROM transactions WHERE transaction_status='Payment Approved'
          ) AS tt ON (tt.order_id=t.order_id AND t.transaction_status='Chargeback' AND t.customer_id=tt.customer_id)
	       WHERE c.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND o.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND c.is_deleted='N' " . $incr . "
	        GROUP BY t.id
          ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $selTrans, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  }catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/payment_transaction.inc.php';
  exit;
}

$companyArr = get_active_global_products_for_filter(0,true);

$processorSql = "SELECT id,name  FROM payment_master WHERE is_deleted = 'N'";
$processorRes = $pdo->select($processorSql);

$selectize = true;
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);


$template = 'payment_transaction.inc.php';
include_once 'layout/end.inc.php';
?>