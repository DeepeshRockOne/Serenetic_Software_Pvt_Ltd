<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
agent_has_access(17);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'My Production';
$breadcrumbes[2]['title'] = 'Transaction';
$breadcrumbes[2]['link'] = 'payment_transaction.php';
$agentId = $_SESSION['agents']['id'];
$displayDirectEnroll = !empty($_SESSION['agents']['displayDirectEnroll']) ? explode(",", $_SESSION['agents']['displayDirectEnroll']) : array();
$reptableIncr = '';
$incr = '';
$extra_export_arr = $schParams = array();
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
$orderIds = !empty($_GET['orderIds']) ? $_GET['orderIds'] : "";
$mbrIds = !empty($_GET['mbrIds']) ? explode(",", $_GET['mbrIds']) : "";
$mbrName = checkIsset($_GET['mbrName']);
$enrollingAgents = checkIsset($_GET['enrollingAgents'],'arr');
$treeAgents = checkIsset($_GET['treeAgents'],'arr');
$paymentType = checkIsset($_GET['paymentType']);
$saleType = checkIsset($_GET['saleType']);
$lastCcAchNo = checkIsset($_GET['lastCcAchNo']);
$products = checkIsset($_GET['products'],'arr');
$transStatus = checkIsset($_GET['transStatus'],'arr');

$join_range = checkIsset($_GET['join_range']);
$fromdate = checkIsset($_GET["fromdate"]);
$todate = checkIsset($_GET["todate"]);
$today = date("m/d/Y");
$added_date = !empty($_GET["added_date"]) ? $_GET["added_date"] : $today;
$viewTrans = !empty($_GET["viewTrans"]) ? $_GET["viewTrans"] : 'todayTrans';

if($viewTrans == "todayTrans" && empty($join_range)){
  $join_range = "Exactly";
}

if(!empty($displayDirectEnroll) && in_array('Members', $displayDirectEnroll)){
    $incr .= " AND (c.sponsor_id = :agent_id OR (s.sponsor_id =:agent_id AND scs.agent_coded_level = 'LOA'))";
    $schParams[':agent_id']=$agentId;
}else{
    $incr .= " AND c.upline_sponsors LIKE '%,".$agentId.",%'";
}

$transIds = cleanSearchKeyword($transIds);
$orderIds = cleanSearchKeyword($orderIds);
$mbrIds = cleanSearchKeyword($mbrIds);
$mbrName = cleanSearchKeyword($mbrName);
$lastCcAchNo = cleanSearchKeyword($lastCcAchNo); 
 
if(!empty($transIds)){
  $transIds = str_replace(" ", "", $transIds);
  $transIds = explode(',', $transIds);
  $transIds = "'" . implode("','", $transIds) . "'";
  $incr .= " AND t.transaction_id IN ($transIds)";
}

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
  $incr .= " AND c.sponsor_id IN (".implode(",",$enrollingAgents).")";
}

if(!empty($treeAgents)){
  $incr .= " AND (s.id IN(".implode(",",$treeAgents).") OR (s.sponsor_id IN (".implode(",",$treeAgents).") AND scs.agent_coded_id = 1)) ";
}

if(!empty($paymentType)) {
  $incr .= " AND t.payment_type = :paymentType";
  $schParams[':paymentType'] = $paymentType;
}

if(!empty($saleType)){
  $incr .= " AND o.is_renewal = :saleType";
  $schParams[':saleType'] = $saleType;
}

if($lastCcAchNo != "") {
  $incr .= " AND obi.last_cc_ach_no = :lastCcAchNo";
  $schParams[':lastCcAchNo'] = $lastCcAchNo;
  $reptableIncr .= 'LEFT JOIN order_billing_info obi ON(obi.order_id = o.id)';
}

if(!empty($products)) {
  $incr .= " AND st.product_id IN (".implode(",",$products).")";
}

if(!empty($transStatus)) {
  $incr .= " AND t.transaction_status IN ('".implode("','",$transStatus)."')";
}

$getfromdate = '';
$todate = '';
if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $schParams[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $schParams[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(t.created_at) >= :fromdate AND DATE(t.created_at) <= :todate";
    $getfromdate = $fromdate;
    $gettodate = $todate;
  }else if($join_range == "Exactly" && $added_date!=''){
    $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(t.created_at) = :added_date";
    $getfromdate = $added_date;
    $gettodate = $added_date;
  }else if($join_range == "Before" && $added_date!=''){
    $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(t.created_at) < :added_date";
    $getfromdate = $added_date;
    $gettodate = date('Y-m-d');
  }else if($join_range == "After" && $added_date!=''){
    $schParams[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(t.created_at) > :added_date";
    $getfromdate = date('Y-m-d');
    $gettodate = $added_date;
  }
}

if(isset($_REQUEST['export_val']) && $_REQUEST['export_val'] == 'transaction_export') {
  include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
    $reptableIncr .= 'JOIN customer_settings scs ON(s.id = scs.customer_id)';
    $extra_export_arr['tbl_incr'] = $reptableIncr;
    
    if($getfromdate!='' && $gettodate != '') {
			$no_days=0;
      if($getfromdate!= '' && $gettodate!='') {
        $date1 = date_create($getfromdate);
        $date2 = date_create($gettodate);
        $diff = date_diff($date1,$date2);
        $no_days=$diff->format("%a");
      }
      if($no_days>62) {
        echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
        exit();
      }
    }

    $job_id=add_export_request_api('EXCEL',$_SESSION['agents']['id'],'Agent',"Agent Transaction Export","agent_quick_sales_summary",$incr, $schParams,$extra_export_arr,'agent_quick_sales_summary');
	
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
  'url' => 'payment_transaction.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $schParams,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) { 
  try {
  	$selTrans = "SELECT t.id as transTblId,t.transaction_status as transStatus,o.status as odrStatus, o.customer_id as odrMbrId, o.id as odrID, o.display_id as odrDispId, o.is_renewal as saleType, o.grand_total as odrTotal, o.created_at as odrDate, c.rep_id as mbrDispId, IF(c.type='Group',c.business_name,CONCAT(c.fname,' ',c.lname)) as mbrName,s.business_name as sponBusinessName, c.state as mbrState, s.rep_id as agentDispId, CONCAT(s.fname,' ',s.lname) as agentName, c.sponsor_id,t.id as transId,
      s.id as agentId,t.reason as transNote,t.transaction_id as transId,s.type as sponsorType,
      l.id as leadId,l.lead_id as leadDispId,CONCAT(l.fname,' ',l.lname) as leadName,c.status as mbrStatus,
      MIN(od.renew_count) AS minCov,MAX(od.renew_count) AS maxCov,c.id as mbrId,c.type as user_type, t.created_at as transDate
	       	FROM transactions t
	        JOIN orders o ON(t.order_id = o.id)
	        JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
	        JOIN customer c ON (c.id = o.customer_id)
	        JOIN customer s ON (c.sponsor_id = s.id)
          JOIN customer_settings scs ON(s.id = scs.customer_id)
	        LEFT JOIN order_billing_info obi ON(obi.customer_id = c.id)
          LEFT JOIN sub_transactions st ON(st.transaction_id=t.id)
          LEFT JOIN leads l ON(l.customer_id=c.id AND l.is_deleted='N')
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

$description['ac_message'] = array(
    'ac_red_1' => array(
        'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
        'title' => $_SESSION['agents']['rep_id'],
    ),
    'ac_message_1' => ' read Transactions'
);
$desc = json_encode($description);
activity_feed(3, $_SESSION['agents']['id'], 'Agent', $_SESSION['agents']['id'], 'Agent', 'Agent Read Transactions.', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);

$agentSql = "SELECT id,rep_id,CONCAT(fname,' ',lname) as name
          FROM customer 
          WHERE type='Agent' AND is_deleted = 'N' AND (id=:agentId OR upline_sponsors LIKE('%,".$agentId.",%')) ORDER BY name";
$agentRes = $pdo->select($agentSql,array(":agentId" => $agentId));

$companyArr = get_active_global_products_for_filter($agentId,true);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);
$template = 'payment_transaction.inc.php';
include_once 'layout/end.inc.php';
?>