<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/enrollment_dates.class.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Orders";
$breadcrumbes[2]['link'] = 'all_orders.php';
$module_access_type = has_access(11);
$enrollDate = new enrollmentDate();

$updOrderStatusOptions = array(
  "Payment Approved" => array("Payment Approved","Chargeback","Payment Declined","Payment Returned","Refund","Void"),
  "Chargeback" => array("Chargeback","Payment Approved"),
  "Refund" => array("Refund","Payment Approved"),
  "Void" => array("Void","Payment Approved"),
  "Payment Declined" => array("Payment Declined","Payment Approved"),
  "Post Payment" => array("Post Payment","Cancelled"),
  "Pending Settlement" => array("Pending Settlement","Payment Approved","Payment Returned","Void"),
  "Payment Returned" => array("Payment Returned","Payment Approved")
);

// Read order activity code start
  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' viewed orders ',
  ); 

  activity_feed(3, $_SESSION['admin']['id'], 'Admin',0, 'orders','Viewed Order', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
// Read order activity code ends

$tableIncr = '';
$incr = '';
$transactionIncr='';
$schParams = array();
$SortBy = "o.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

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
$is_ajaxed_count = checkIsset($_GET['is_ajaxed_count']);
$orderIds = checkIsset($_GET['orderIds']);
$mbrName = checkIsset($_GET['mbrName']);
$mbrIds = !empty($_GET['mbrIds']) ? explode(",", $_GET['mbrIds']) : "";
$enrollingAgents = !empty($_GET['enrollingAgents']) ? explode(",", $_GET['enrollingAgents']) : "";
$treeAgents = checkIsset($_GET['treeAgents']);
$paymentType = checkIsset($_GET['paymentType'],'arr');
$saleType = checkIsset($_GET['saleType'],'arr');
$lastCcAchNo = checkIsset($_GET['lastCcAchNo']);
$products = checkIsset($_GET['products']);
$odrStatus = checkIsset($_GET['odrStatus'],'arr');

$join_range = checkIsset($_GET['join_range']);
$fromdate = checkIsset($_GET["fromdate"]);
$todate = checkIsset($_GET["todate"]);
$today = date("m/d/Y");
$added_date = !empty($_GET["added_date"]) ? $_GET["added_date"] : $today;
$viewOdr = isset($_GET["viewOdr"]) ? $_GET["viewOdr"] : 'todayOdr';

if($viewOdr == "todayOdr" && empty($join_range)){
  $join_range = "Exactly";
}

$lastCcAchNo = cleanSearchKeyword($lastCcAchNo); 
$orderIds = cleanSearchKeyword($orderIds); 
 
if(!empty($orderIds)){
  $orderIds = str_replace(" ", "", $orderIds);
  $orderIds = explode(',', $orderIds);
  $orderIds = "'" . implode("','", $orderIds) . "'";
  $incr .= " AND o.display_id IN ($orderIds)";
}

if($mbrName != "") {
  $schParams[':mbrName'] = "%" . makeSafe($mbrName) . "%";
  $incr .= " AND (CONCAT(c.fname,' ',c.lname) LIKE :mbrName OR c.fname LIKE :mbrName OR c.lname LIKE :mbrName OR (c.type='Group' AND c.business_name LIKE :mbrName))";
}

if(!empty($mbrIds)){
  $mbrIds = array_map('trim',$mbrIds);
  $incr .= " AND c.id IN ('".implode("','",$mbrIds)."')";
}

if(!empty($enrollingAgents)){
  $enrollingAgents = array_map('trim',$enrollingAgents);
  $incr .= " AND c.sponsor_id IN (".implode(",",$enrollingAgents).")";
}

if(!empty($treeAgents)){
  $incr .= " AND (s.id IN(".$treeAgents.") OR (s.sponsor_id IN (".$treeAgents.") AND scs.agent_coded_id = 1)) ";
  $tableIncr .= 'JOIN customer_settings scs ON(s.id = scs.customer_id)';
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
  $incr .= " AND o.payment_type IN ('".implode("','",$paymentType)."')";
}

if(!empty($saleType)){
  $incr .= " AND o.is_renewal IN ('".implode("','",$saleType)."')";
}

$obiIncr = "";
if($lastCcAchNo != "") {
  $incr .= " AND obi.last_cc_ach_no = :lastCcAchNo";
  $schParams[':lastCcAchNo'] = $lastCcAchNo;
  $obiIncr = " AND obi.last_cc_ach_no=:lastCcAchNo";
}

$detIncr = "";
if(!empty($products)) {
  $product = explode(',',$products);
  $variations = $pdo->selectOne("SELECT GROUP_CONCAT(id) as product_ids FROM prd_main WHERE (id in('".implode("','", $product)."') OR parent_product_id in('".implode("','", $product)."')) AND is_deleted = 'N' AND name !='' AND type!='Fees'");
  if($variations){
      $product = explode(',', $variations['product_ids']);
  }
  $detIncr .= " AND od.product_id IN (".implode(",",$product).")";
}

if(!empty($odrStatus)) {
  $incr .= " AND o.status IN ('".implode("','",$odrStatus)."')";
}

$count_incr = $incr;

if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $schParams[':fromdate'] = date("Y-m-d",strtotime($fromdate)).' 00:00:00';
    $schParams[':todate'] = date("Y-m-d",strtotime($todate)).' 23:59:59';
    $transactionIncr.=" AND t.created_at >= :fromdate AND t.created_at <= :todate";
    $count_incr.=" AND o.created_at >= :fromdate AND o.created_at <= :todate";
  }else if($join_range == "Exactly" && $added_date!=''){
    $schParams[':fromdate'] = date("Y-m-d",strtotime($added_date)).' 00:00:00';
    $schParams[':todate'] = date("Y-m-d",strtotime($added_date)).' 23:59:59';
    $transactionIncr.=" AND t.created_at >= :fromdate AND t.created_at <= :todate";
    $count_incr.=" AND o.created_at >= :fromdate AND o.created_at <= :todate";
  }else if($join_range == "Before" && $added_date!=''){
    $schParams[':added_date'] = date("Y-m-d",strtotime($added_date)).' 00:00:00';
    $transactionIncr.=" AND t.created_at < :added_date";
    $count_incr.=" AND o.created_at < :added_date";
  }else if($join_range == "After" && $added_date!=''){
    $schParams[':added_date'] = date("Y-m-d",strtotime($added_date)).' 23:59:59';
    $transactionIncr.=" AND t.created_at > :added_date";
    $count_incr.=" AND o.created_at > :added_date";
  }
}

  if(isset($_REQUEST['export_val']) && $_REQUEST['export_val'] == 'order_export') {
      include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
      include_once dirname(__DIR__) . '/includes/export_report.class.php';
      if($detIncr !=''){
        $incr.=$detIncr;
      }
      if($transactionIncr !=''){
        $incr.=$transactionIncr;
      }
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
  'url' => 'all_orders.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $schParams,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) { 
  try {
    $selOdr = "SELECT o.is_list_bill_order,o.future_payment as isPostOrder,o.post_date as postDate,o.status as odrStatus, o.customer_id as odrMbrId, o.id as odrID, o.display_id as odrDispId, o.is_renewal as saleType, o.grand_total as odrTotal, t.created_at as odrDate, c.rep_id as mbrDispId,IF(c.type='Group',c.business_name,CONCAT(c.fname,' ',c.lname)) as mbrName,s.business_name as sponBusinessName, c.state as mbrState, s.rep_id as agentDispId, CONCAT(s.fname,' ',s.lname) as agentName, c.sponsor_id,t.id as transId,s.id as agentId,t.reason  as orderNote,o.payment_processor_res as processorResponse,
      IFNULL(MIN(DISTINCT CASE WHEN od.product_type!='Fees'  THEN od.renew_count END),0) AS minCov,
      IFNULL(MAX(DISTINCT CASE WHEN od.product_type!='Fees'  THEN od.renew_count END),0) AS maxCov,
      c.id as mbrId,
      l.id as leadId,l.lead_id as leadDispId,CONCAT(l.fname,' ',l.lname) as leadName,c.status as mbrStatus,c.type as user_type
              FROM orders o
              JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
              JOIN customer c ON (c.id = o.customer_id AND c.is_deleted='N' AND c.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation'))
              JOIN customer s ON (c.sponsor_id = s.id)
              $tableIncr
              LEFT JOIN order_billing_info obi ON(obi.order_id = o.id)
              JOIN (SELECT MAX(id) AS id,created_at,order_id FROM transactions WHERE is_deleted = 'N' GROUP BY order_id) AS res ON (res.order_id = o.id)
              JOIN transactions t ON (t.id = res.id  $transactionIncr)
              LEFT JOIN leads l ON(l.customer_id=c.id AND l.is_deleted='N')
              WHERE o.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') $incr $detIncr
              GROUP BY o.id
              ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $selOdr, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/all_orders.inc.php';
  exit;
}elseif($is_ajaxed_count){
  $odrSummSql = "SELECT 
            SUM(o.grand_total) as allOdrAmt,
            SUM(if(o.payment_type='CC', o.grand_total, 0)) as allCcOdrAmt,
            SUM(if(o.payment_type='ACH', o.grand_total, 0)) as allAchOdrAmt,
            COUNT(DISTINCT(o.id)) AS allOdrCnt,

            SUM(if(o.status = 'Payment Approved' AND o.is_renewal='N', o.grand_total, 0)) as nbOdrAmt,
            SUM(if(o.status = 'Payment Approved' AND o.is_renewal='N' AND o.payment_type='CC', o.grand_total, 0)) as nbCcOdrAmt,
            SUM(if(o.status = 'Payment Approved' AND o.is_renewal='N' AND o.payment_type='ACH', o.grand_total, 0)) as nbAchOdrAmt,
            COUNT(DISTINCT(CASE WHEN o.status = 'Payment Approved' AND o.is_renewal='N' THEN o.id END)) AS nbOdrCnt,

            SUM(if(o.status = 'Payment Approved' AND o.is_renewal='Y', o.grand_total, 0)) as renewOdrAmt,
            SUM(if(o.status = 'Payment Approved' AND o.is_renewal='Y' AND o.payment_type='CC', o.grand_total, 0)) as renewCcOdrAmt,
            SUM(if(o.status = 'Payment Approved' AND o.is_renewal='Y' AND o.payment_type='ACH', o.grand_total, 0)) as renewAchOdrAmt,
            COUNT(DISTINCT(CASE WHEN o.status = 'Payment Approved' AND o.is_renewal='Y' THEN o.id END)) AS renewOdrCnt,

            SUM(if(o.status = 'Payment Declined', o.grand_total, 0)) as decOdrAmt,
            SUM(if(o.status = 'Payment Declined' AND o.payment_type='CC', o.grand_total, 0)) as decCcOdrAmt,
            SUM(if(o.status = 'Payment Declined' AND o.payment_type='ACH', o.grand_total, 0)) as decAchOdrAmt,
            COUNT(DISTINCT(CASE WHEN o.status = 'Payment Declined' THEN o.id END)) AS decOdrCnt,

            SUM(if(o.status IN ('Void','Refund','Chargeback','Payment Returned'), o.grand_total, 0)) as revOdrAmt,
            SUM(if(o.status IN ('Void','Refund','Chargeback','Payment Returned') AND o.payment_type='CC', o.grand_total, 0)) as revCcOdrAmt,
            SUM(if(o.status IN ('Void','Refund','Chargeback','Payment Returned') AND o.payment_type='ACH', o.grand_total, 0)) as revAchOdrAmt,
            COUNT(DISTINCT(CASE WHEN o.status IN ('Void','Refund','Chargeback','Payment Returned') THEN o.id END)) AS revOdrCnt,

            SUM(if(o.status = 'Post Payment', o.grand_total, 0)) as postOdrAmt,
            SUM(if(o.status = 'Post Payment' AND o.payment_type='CC', o.grand_total, 0)) as postCcOdrAmt,
            SUM(if(o.status = 'Post Payment' AND o.payment_type='ACH', o.grand_total, 0)) as postAchOdrAmt,
            COUNT(DISTINCT(CASE WHEN o.status = 'Post Payment' THEN o.id END)) AS postOdrCnt,

            SUM(if(o.status = 'Pending Settlement', o.grand_total, 0)) as pendOdrAmt,
            SUM(if(o.status = 'Pending Settlement' AND o.payment_type='CC', o.grand_total, 0)) as pendCcOdrAmt,
            SUM(if(o.status = 'Pending Settlement' AND o.payment_type='ACH', o.grand_total, 0)) as pendAchOdrAmt,
            COUNT(DISTINCT(CASE WHEN o.status = 'Pending Settlement' THEN o.id END)) AS pendOdrCnt

            FROM orders o
            LEFT JOIN 
              (SELECT o.id
                      FROM orders o 
                      JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                      WHERE o.id > 0 $detIncr GROUP BY o.id 
              ) AS ord ON ord.id = o.id AND ord.id IS NOT NULL
            JOIN customer c ON (c.id = o.customer_id AND c.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation')AND c.is_deleted='N')
            JOIN customer s ON (c.sponsor_id = s.id)
            $tableIncr
            LEFT JOIN 
            (SELECT obi.order_id as order_id,obi.last_cc_ach_no
                FROM order_billing_info obi
                WHERE obi.order_id > 0 $obiIncr GROUP BY obi.order_id 
            ) AS obi ON obi.order_id = o.id
            WHERE o.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') " . $count_incr . " 
            ORDER BY $SortBy $currSortDirection";
  $odrSummRes = $pdo->selectOne($odrSummSql, $schParams);

  $allOdrCnt = checkNumberSet($odrSummRes['allOdrCnt']);
  $allOdrAmt = checkNumberSet($odrSummRes['allOdrAmt']);
  $allCcOdrAmt = checkNumberSet($odrSummRes['allCcOdrAmt']);
  $allAchOdrAmt = checkNumberSet($odrSummRes['allAchOdrAmt']);

  $nbOdrCnt = checkNumberSet($odrSummRes['nbOdrCnt']);
  $nbOdrAmt = checkNumberSet($odrSummRes['nbOdrAmt']);
  $nbCcOdrAmt = checkNumberSet($odrSummRes['nbCcOdrAmt']);
  $nbAchOdrAmt = checkNumberSet($odrSummRes['nbAchOdrAmt']);

  $renewOdrCnt = checkNumberSet($odrSummRes['renewOdrCnt']);
  $renewOdrAmt = checkNumberSet($odrSummRes['renewOdrAmt']);
  $renewCcOdrAmt = checkNumberSet($odrSummRes['renewCcOdrAmt']);
  $renewAchOdrAmt = checkNumberSet($odrSummRes['renewAchOdrAmt']);

  $decOdrCnt = checkNumberSet($odrSummRes['decOdrCnt']);
  $decOdrAmt = checkNumberSet($odrSummRes['decOdrAmt']);
  $decCcOdrAmt = checkNumberSet($odrSummRes['decCcOdrAmt']);
  $decAchOdrAmt = checkNumberSet($odrSummRes['decAchOdrAmt']);

  $revOdrCnt = checkNumberSet($odrSummRes['revOdrCnt']);
  $revOdrAmt = checkNumberSet($odrSummRes['revOdrAmt']);
  $revCcOdrAmt = checkNumberSet($odrSummRes['revCcOdrAmt']);
  $revAchOdrAmt = checkNumberSet($odrSummRes['revAchOdrAmt']);

  $postOdrCnt = checkNumberSet($odrSummRes['postOdrCnt']);
  $postOdrAmt = checkNumberSet($odrSummRes['postOdrAmt']);
  $postCcOdrAmt = checkNumberSet($odrSummRes['postCcOdrAmt']);
  $postAchOdrAmt = checkNumberSet($odrSummRes['postAchOdrAmt']);

  $pendOdrCnt = checkNumberSet($odrSummRes['pendOdrCnt']);
  $pendOdrAmt = checkNumberSet($odrSummRes['pendOdrAmt']);
  $pendCcOdrAmt = checkNumberSet($odrSummRes['pendCcOdrAmt']);
  $pendAchOdrAmt = checkNumberSet($odrSummRes['pendAchOdrAmt']);

  include_once 'tmpl/all_orders.inc.php';
  exit;
}

// $companyArr = get_active_global_products_for_filter(0,true);

$selectize = true;
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);


$template = 'all_orders.inc.php';
include_once 'layout/end.inc.php';
?>