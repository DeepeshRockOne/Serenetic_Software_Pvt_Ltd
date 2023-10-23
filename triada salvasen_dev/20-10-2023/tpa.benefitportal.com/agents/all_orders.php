<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/enrollment_dates.class.php';
agent_has_access(15);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Orders";
$breadcrumbes[2]['link'] = 'all_orders.php';
$agentId = $_SESSION['agents']['id'];
$displayDirectEnroll = !empty($_SESSION['agents']['displayDirectEnroll']) ? explode(",", $_SESSION['agents']['displayDirectEnroll']) : array();
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

$reptableIncr ='';
$incr = '';
$extra_export_arr = $schParams = array();
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
$orderIds = !empty($_GET['orderIds'])?$_GET['orderIds']: "";
$mbrName = checkIsset($_GET['mbrName']);
$mbrIds = !empty($_GET['mbrIds']) ? explode(",", $_GET['mbrIds']) : "";
$enrollingAgents = checkIsset($_GET['enrollingAgents'],'arr');
$treeAgents = checkIsset($_GET['treeAgents'],'arr');
$paymentType = checkIsset($_GET['paymentType'],'arr');
$saleType = checkIsset($_GET['saleType'],'arr');
$lastCcAchNo = checkIsset($_GET['lastCcAchNo']);
$products = checkIsset($_GET['products'],'arr');
$odrStatus = checkIsset($_GET['odrStatus'],'arr');

$today = date("m/d/Y");
$viewOdr = !empty($_GET["viewOdr"]) ? $_GET["viewOdr"] : 'todayOdr';
$join_range = checkIsset($_GET['join_range']);
$fromdate = checkIsset($_GET["fromdate"]);
$todate = checkIsset($_GET["todate"]);
$added_date = !empty($_GET["added_date"]) ? $_GET["added_date"] : $today;

$post_join_range = checkIsset($_GET['post_join_range']);
$post_fromdate = checkIsset($_GET["post_fromdate"]);
$post_todate = checkIsset($_GET["post_todate"]);
$post_added_date = !empty($_GET["post_added_date"]) ? $_GET["post_added_date"] : '';

if(!empty($displayDirectEnroll) && in_array('Members', $displayDirectEnroll)){
    $incr .= " AND (c.sponsor_id = :agent_id OR (s.sponsor_id =:agent_id AND scs.agent_coded_level = 'LOA'))";
    $schParams[':agent_id']=$agentId;
}else{
    $incr .= " AND c.upline_sponsors LIKE '%,".$agentId.",%'";
}
$incr .= " AND c.upline_sponsors LIKE '%,".$agentId.",%'";

if($viewOdr == "todayOdr" && empty($join_range)){
  $join_range = "Exactly";
}

$orderIds = cleanSearchKeyword($orderIds);
$mbrName = cleanSearchKeyword($mbrName);
$mbrIds = cleanSearchKeyword($mbrIds);
$lastCcAchNo = cleanSearchKeyword($lastCcAchNo); 
  
if(!empty($orderIds)){
  $orderIds = str_replace(" ", "", $orderIds);
  $orderIds = explode(',', $orderIds);
  $orderIds = "'" . implode("','", $orderIds) . "'";
  $incr .= " AND o.display_id IN ($orderIds)";
}

$leadIncr = false;
if($mbrName != "") {
  $schParams[':mbrName'] = "%" . makeSafe($mbrName) . "%";
  $incr .= " AND (CONCAT(c.fname,' ',c.lname) LIKE :mbrName OR c.fname LIKE :mbrName OR c.lname LIKE :mbrName OR (c.type='Group' AND c.business_name LIKE :mbrName) OR (c.status='Post Payment' AND (CONCAT(l.fname,' ',l.lname) LIKE :mbrName OR l.fname LIKE :mbrName OR l.lname LIKE :mbrName)))";
  $leadIncr = true;
}

if(!empty($mbrIds)){
  $mbrIds = array_map('trim',$mbrIds);
  $incr .= " AND (c.rep_id IN ('".implode("','",$mbrIds)."') OR (c.status='Post Payment' AND l.lead_id IN ('".implode("','",$mbrIds)."')))";
  $leadIncr = true;
}

if($leadIncr){
  $reptableIncr.="LEFT JOIN leads l ON(l.customer_id=c.id AND l.is_deleted='N')";
}

if(!empty($enrollingAgents)){
  $enrollingAgents = array_map('trim',$enrollingAgents);
  $incr .= " AND c.sponsor_id IN (".implode(",",$enrollingAgents).")";
}

if(!empty($treeAgents)){
  $incr .= " AND (s.id IN(".implode(",",$treeAgents).") OR (s.sponsor_id IN (".implode(",",$treeAgents).") AND scs.agent_coded_id = 1)) ";
}
// if(!empty($treeAgents)){
//   $treeAgents = array_map('trim',$treeAgents);
//   $incr .= " AND c.upline_sponsors LIKE '%,".implode(",",$treeAgents).",%'";
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
  $reptableIncr .= 'LEFT JOIN order_billing_info obi ON(obi.order_id = o.id)';
}

$detIncr = "";
if(!empty($products)) {
  $detIncr .= " AND od.product_id IN (".implode(",",$products).")";
}

if(!empty($odrStatus)) {
  $incr .= " AND o.status IN ('".implode("','",$odrStatus)."')";
}

$getfromdate = '';
$gettodate = '';

if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $schParams[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $schParams[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(o.created_at) >= :fromdate AND DATE(o.created_at) <= :todate";
    $getfromdate = $fromdate;
    $gettodate = $todate;
  }else if($join_range == "Exactly" && $added_date!=''){
    $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(o.created_at) = :added_date";
    $getfromdate = $added_date;
    $gettodate = $added_date;
  }else if($join_range == "Before" && $added_date!=''){
    $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(o.created_at) < :added_date";
    $getfromdate = $added_date;
    $gettodate = date('Y-m-d');
  }else if($join_range == "After" && $added_date!=''){
    $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(o.created_at) > :added_date";
    $getfromdate = date('Y-m-d');
    $gettodate = $added_date;
  }
}

$pgetfromdate = '';
$pgettodate = '';
if($post_join_range != ""){
  if($post_join_range == "Range" && $post_fromdate!='' && $post_todate!=''){
    $schParams[':post_fromdate'] = date("Y-m-d",strtotime($post_fromdate));
    $schParams[':post_todate'] = date("Y-m-d",strtotime($post_todate));
    $incr.=" AND o.future_payment = 'Y' AND DATE(o.post_date) >= :post_fromdate AND DATE(o.post_date) <= :post_todate";
    $pgetfromdate = $post_fromdate;
    $pgettodate = $post_todate;
  }else if($post_join_range == "Exactly" && $post_added_date!=''){
    $schParams[':post_added_date'] = date("Y-m-d",strtotime($post_added_date));
    $incr.=" AND o.future_payment = 'Y' AND DATE(o.post_date) = :post_added_date";
    $pgetfromdate = $post_added_date;
    $pgettodate = $post_added_date;
  }else if($post_join_range == "Before" && $added_date!=''){
    $schParams[':post_added_date'] = date("Y-m-d",strtotime($post_added_date));
    $incr.=" AND o.future_payment = 'Y' AND DATE(o.post_date) < :post_added_date";
    $pgetfromdate = $post_added_date;
    $pgettodate = date('Y-m-d');
  }else if($post_join_range == "After" && $added_date!=''){
    $schParams[':post_added_date'] = date("Y-m-d",strtotime($post_added_date));
    $incr.=" AND o.future_payment = 'Y' AND DATE(o.post_date) > :post_added_date";
    $pgetfromdate = date('Y-m-d');
    $pgettodate = $post_added_date;
  }
}

if(isset($_REQUEST['export_val']) && $_REQUEST['export_val'] == 'order_export') {
  include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
    $reptableIncr .= 'JOIN customer_settings scs ON(s.id = scs.customer_id)';
		$extra_export_arr['tbl_incr'] = $reptableIncr;
    $proper_date_range = true;
		if($getfromdate!='' && $gettodate != '') {

			$no_days=0;
			if($getfromdate!= '' && $gettodate!='') {
				$date1 = date_create($getfromdate);
				$date2 = date_create($gettodate);
				$diff = date_diff($date1,$date2);
				$no_days=$diff->format("%a");
      }
      $proper_date_range = false;
    }

    if($pgetfromdate!='' && $pgettodate != '') {

      $no_days=0;
      if($pgetfromdate!= '' && $pgettodate!='') {
        $date1 = date_create($pgetfromdate);
        $date2 = date_create($pgettodate);
        $diff = date_diff($date1,$date2);
        $no_days=$diff->format("%a");
      }
      $proper_date_range = false;
    }

    if(!$proper_date_range){
			if($no_days>62) {
				echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
				exit();
			}
		}
		$job_id=add_export_request_api('EXCEL',$_SESSION['agents']['id'],'Agent',"Agent Order Export","agent_quick_sales_summary",$incr, $schParams,$extra_export_arr,'agent_quick_sales_summary');
	
		echo json_encode(array("status"=>"success","message"=>"Your export request is added")); 
		exit;
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
    $odrSummRes = array();
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
              JOIN customer c ON (c.id = o.customer_id)
              JOIN customer s ON (c.sponsor_id = s.id)
              JOIN customer_settings scs ON(s.id = scs.customer_id)
              LEFT JOIN leads l ON(l.customer_id=c.id AND l.is_deleted='N')
              LEFT JOIN 
              (SELECT o.id
                  FROM orders o 
                  JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                  WHERE o.id > 0 $detIncr GROUP BY o.id 
              ) AS ord ON ord.id = o.id 
              LEFT JOIN 
              (SELECT obi.order_id as order_id,obi.last_cc_ach_no
                  FROM order_billing_info obi
                  WHERE obi.order_id > 0 $obiIncr GROUP BY obi.order_id 
              ) AS obi ON obi.order_id = o.id
              WHERE ord.id IS NOT NULL AND c.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND o.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND c.is_deleted='N' " . $incr . "";
    $odrSummRes = $pdo->selectOne($odrSummSql, $schParams);

    $allOdrCnt = !empty($odrSummRes['allOdrCnt']) ? $odrSummRes['allOdrCnt'] : 0;
    $allOdrAmt = !empty($odrSummRes['allOdrAmt']) ? $odrSummRes['allOdrAmt'] : 0;
    $allCcOdrAmt = !empty($odrSummRes['allCcOdrAmt']) ? $odrSummRes['allCcOdrAmt'] : 0;
    $allAchOdrAmt = !empty($odrSummRes['allAchOdrAmt']) ? $odrSummRes['allAchOdrAmt'] : 0;

    $nbOdrCnt = !empty($odrSummRes['nbOdrCnt']) ? $odrSummRes['nbOdrCnt'] : 0;
    $nbOdrAmt = !empty($odrSummRes['nbOdrAmt']) ? $odrSummRes['nbOdrAmt'] : 0;
    $nbCcOdrAmt = !empty($odrSummRes['nbCcOdrAmt']) ? $odrSummRes['nbCcOdrAmt'] : 0;
    $nbAchOdrAmt = !empty($odrSummRes['nbAchOdrAmt']) ? $odrSummRes['nbAchOdrAmt'] : 0;

    $renewOdrCnt = !empty($odrSummRes['renewOdrCnt']) ? $odrSummRes['renewOdrCnt'] : 0;
    $renewOdrAmt = !empty($odrSummRes['renewOdrAmt']) ? $odrSummRes['renewOdrAmt'] : 0;
    $renewCcOdrAmt = !empty($odrSummRes['renewCcOdrAmt']) ? $odrSummRes['renewCcOdrAmt'] : 0;
    $renewAchOdrAmt = !empty($odrSummRes['renewAchOdrAmt']) ? $odrSummRes['renewAchOdrAmt'] : 0;

    $decOdrCnt = !empty($odrSummRes['decOdrCnt']) ? $odrSummRes['decOdrCnt'] : 0;
    $decOdrAmt = !empty($odrSummRes['decOdrAmt']) ? $odrSummRes['decOdrAmt'] : 0;
    $decCcOdrAmt = !empty($odrSummRes['decCcOdrAmt']) ? $odrSummRes['decCcOdrAmt'] : 0;
    $decAchOdrAmt = !empty($odrSummRes['decAchOdrAmt']) ? $odrSummRes['decAchOdrAmt'] : 0;

    $revOdrCnt = !empty($odrSummRes['revOdrCnt']) ? $odrSummRes['revOdrCnt'] : 0;
    $revOdrAmt = !empty($odrSummRes['revOdrAmt']) ? $odrSummRes['revOdrAmt'] : 0;
    $revCcOdrAmt = !empty($odrSummRes['revCcOdrAmt']) ? $odrSummRes['revCcOdrAmt'] : 0;
    $revAchOdrAmt = !empty($odrSummRes['revAchOdrAmt']) ? $odrSummRes['revAchOdrAmt'] : 0;

    $postOdrCnt = !empty($odrSummRes['postOdrCnt']) ? $odrSummRes['postOdrCnt'] : 0;
    $postOdrAmt = !empty($odrSummRes['postOdrAmt']) ? $odrSummRes['postOdrAmt'] : 0;
    $postCcOdrAmt = !empty($odrSummRes['postCcOdrAmt']) ? $odrSummRes['postCcOdrAmt'] : 0;
    $postAchOdrAmt = !empty($odrSummRes['postAchOdrAmt']) ? $odrSummRes['postAchOdrAmt'] : 0;

    $pendOdrCnt = !empty($odrSummRes['pendOdrCnt']) ? $odrSummRes['pendOdrCnt'] : 0;
    $pendOdrAmt = !empty($odrSummRes['pendOdrAmt']) ? $odrSummRes['pendOdrAmt'] : 0;
    $pendCcOdrAmt = !empty($odrSummRes['pendCcOdrAmt']) ? $odrSummRes['pendCcOdrAmt'] : 0;
    $pendAchOdrAmt = !empty($odrSummRes['pendAchOdrAmt']) ? $odrSummRes['pendAchOdrAmt'] : 0;



    $selOdr = "SELECT o.is_list_bill_order,o.future_payment as isPostOrder,o.post_date as postDate,o.status as odrStatus, o.customer_id as odrMbrId, o.id as odrID, o.display_id as odrDispId, o.is_renewal as saleType, o.grand_total as odrTotal, o.created_at as odrDate, c.rep_id as mbrDispId, IF(c.type='Group',c.business_name,CONCAT(c.fname,' ',c.lname)) as mbrName,s.business_name as sponBusinessName, c.state as mbrState, s.rep_id as agentDispId, CONCAT(s.fname,' ',s.lname) as agentName, c.sponsor_id,t.id as transId,s.id as agentId,o.order_comments as orderNote,o.payment_processor_res as processorResponse,MIN(od.renew_count) AS minCov,MAX(od.renew_count) AS maxCov,c.id as mbrId,
      l.id as leadId,l.lead_id as leadDispId,CONCAT(l.fname,' ',l.lname) as leadName,c.status as mbrStatus,c.type as user_type
              FROM orders o
              JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
              JOIN customer c ON (c.id = o.customer_id)
              JOIN customer s ON (c.sponsor_id = s.id)
              JOIN customer_settings scs ON(s.id = scs.customer_id)
              LEFT JOIN order_billing_info obi ON(obi.order_id = o.id)
              LEFT JOIN transactions t ON (t.order_id = o.id AND t.is_deleted='N')
              LEFT JOIN leads l ON(l.customer_id=c.id AND l.is_deleted='N')
              WHERE c.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND o.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') AND c.is_deleted='N' $incr $detIncr
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
}
 
$description['ac_message'] = array(
    'ac_red_1' => array(
        'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
        'title' => $_SESSION['agents']['rep_id'],
    ),
    'ac_message_1' => ' read Orders'
);
$desc = json_encode($description);
activity_feed(3, $_SESSION['agents']['id'], 'Agent', $_SESSION['agents']['id'], 'Agent', 'Agent Read Orders.', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);


$agentSql = "SELECT id,rep_id,CONCAT(fname,' ',lname) as name
          FROM customer 
          WHERE type='Agent' AND is_deleted = 'N' AND (id=:agentId OR upline_sponsors LIKE('%,".$agentId.",%')) ORDER BY name";
$agentRes = $pdo->select($agentSql,array(":agentId" => $agentId));

$companyArr = get_active_global_products_for_filter($_SESSION['agents']['id'],true);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);
$template = 'all_orders.inc.php';
include_once 'layout/end.inc.php';
?>