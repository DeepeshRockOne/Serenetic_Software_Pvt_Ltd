<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Payables";
$breadcrumbes[0]['link'] = 'account_payable.php';
$breadcrumbes[2]['class'] = "Active";
 
$incr = '';
$schParams = array();
$SortBy = "pd.id";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$sch_params = array();
$has_querystring = false;


$today = date("m/d/Y");
// $lastDay = date("m/t/Y");
$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";//OP29-566 updates
$fromdate = isset($_GET["fromdate"]) && !empty($_GET["fromdate"]) ? $_GET["fromdate"]:'';
$todate = isset($_GET["todate"]) &&  !empty($_GET["todate"]) ? $_GET["todate"]:'';
$added_date = !empty($_GET["added_date"]) ? $_GET["added_date"] : $today;
$viewPayable = !empty($_GET["viewPayable"]) ? $_GET["viewPayable"] : 'dailyPayable';//OP29-566 updates
$is_from_all_orders = !empty($_GET["is_from_all_orders"]) ? $_GET["is_from_all_orders"] : 'N';
if($is_from_all_orders == 'Y'){
    $viewPayable='allPayable';
}
if($viewPayable == "dailyPayable" && empty($join_range)){
    $join_range = "Exactly";
}
// $added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";
$products = isset($_GET["products"])?$_GET["products"]:"";
$order_id = isset($_GET["order_id"])?$_GET["order_id"]:"";
$transaction_id = isset($_GET["transaction_id"])?$_GET["transaction_id"]:"";
$payee_type = isset($_GET["payee_type"])?$_GET["payee_type"]:array();
$fee_id = isset($_GET["fee_id"])?$_GET["fee_id"]:"";
$payee_id = isset($_GET["payee_id"])?$_GET["payee_id"]:"";
$policy_id = isset($_GET["policy_id"])?$_GET["policy_id"]:"";
$rep_id = !empty($_GET['member_id']) ? explode(",", $_GET['member_id']) : "";
$paymentType = checkIsset($_GET['paymentType']);
$coverage_period = checkIsset($_GET['coverage_period'],'arr');
$order_status = checkIsset($_GET['order_status'],'arr');

if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate)).' 00:00:00';
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate)).' 23:59:59';
    $incr.=" AND pd.created_at >= :fromdate AND pd.created_at <= :todate ";
  }else if($join_range == "Exactly" && $added_date!=''){
    $sch_params[':added_fromdate'] = date("Y-m-d",strtotime($added_date)).' 00:00:00';
    $sch_params[':added_todate'] = date("Y-m-d",strtotime($added_date)).' 23:59:59';
    $incr.=" AND pd.created_at >= :added_fromdate AND pd.created_at <= :added_todate ";
  }else if($join_range == "Before" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date)).' 00:00:00';
    $incr.=" AND pd.created_at < :added_date ";
  }else if($join_range == "After" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date)).' 23:59:59';
    $incr.=" AND pd.created_at > :added_date ";
  }
}

$order_id = cleanSearchKeyword($order_id); 
$transaction_id = cleanSearchKeyword($transaction_id);
$payee_id = cleanSearchKeyword($payee_id); 
$policy_id = cleanSearchKeyword($policy_id);
$fee_id = cleanSearchKeyword($fee_id); 
 
if(!empty($products)){
	$products = implode("','", $products);
    $incr .= " AND (pm.id IN('".$products."') OR pm.parent_product_id IN('".$products."'))";
} 

if(!empty($order_id)){
  $order_id_data = str_replace(" ", "", $order_id);
  $order_id_data = explode(',', $order_id_data);
  $order_id_data = "'" . implode("','", $order_id_data) . "'";
  $incr .= " AND o.display_id IN ($order_id_data)";
}

if(!empty($transaction_id)){
  $transaction_id = str_replace(" ", "", $transaction_id);
  $transaction_id = explode(',', $transaction_id);
  $transaction_id = "'" . implode("','", $transaction_id) . "'";
  $incr .= " AND t.transaction_id IN ($transaction_id)";
}

if (!empty($payee_type)) {
    $payee_type_org = $payee_type;
    $payee_type = implode("','", $payee_type);
    $comm_payee_types = array_intersect($payee_type_org,array('Advance Commission','Commission','Fee Commission','PMPM'));
    $payee_type_incr = "";
    if(count($comm_payee_types) > 0) {
        $payee_type_incr = "(pd.payee_type='Agent' AND pd.type IN('".$payee_type."'))";
    }
    $fees_payee_types = array_intersect($payee_type_org,array('Carrier','Membership','Vendor'));
    if(count($fees_payee_types) > 0) {
        if(!empty($payee_type_incr)) {
            $payee_type_incr .= " OR ";
        }
        $payee_type_incr .= "(pd.payee_type!='Agent' AND pd.payee_type IN('".$payee_type."'))";
    }
    $incr .= " AND (".$payee_type_incr.")";
}

if (strpos($fee_id, ',')){	
	$fee_id = str_replace(',',"','",$fee_id);
	$fee_id = str_replace(' ',"",$fee_id);
}

if ($fee_id != "") {
    $incr .= " AND fee_prd.product_code IN('".$fee_id."')";
}

if (strpos($payee_id, ',')){	
	$payee_id = str_replace(',',"','",$payee_id);
	$payee_id = str_replace(' ',"",$payee_id);
}
$tbl_incr = '';
if ($payee_id != "") {
    $incr .= " AND ((pd.payee_type='Agent' AND ag.rep_id IN('".$payee_id."')) OR (pd.payee_type!='Agent' AND pf.display_id IN('".$payee_id."')))";
}

if (strpos($policy_id, ',')){
	$policy_id = str_replace(',',"','",$policy_id);
	$policy_id = str_replace(' ',"",$policy_id);
}
if ($policy_id != "") {
	$incr .= " AND ws.website_id IN ('".$policy_id."')";
}

if (!empty($rep_id)) {
  $rep_id = "'" . implode("','", makeSafe($rep_id)) . "'";
  $incr .= " AND c.id IN ($rep_id)";
}

if(!empty($paymentType)){
    $sch_params[':payemnt_type'] = $paymentType;
    $incr .= " AND o.payment_type = :payemnt_type ";
}


if(!empty($coverage_period)){
  // $sch_params[':coverage_period'] = $coverage_period;
  $incr .= " AND od.renew_count IN(".implode(',',$coverage_period).")";
}

if(!empty($order_status)) {
  $incr .= " AND t.transaction_status IN ('".implode("','",$order_status)."')";
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';

$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'account_payable.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';
if ($is_ajaxed) {   
  if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'payables_export') {
      include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
      include_once dirname(__DIR__) . '/includes/export_report.class.php';
      $config_data = array(
        'user_id' => $_SESSION['admin']['id'],
        'user_type' => 'Admin',
        'user_rep_id' => $_SESSION['admin']['display_id'],
        'user_profile_page' => $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'timezone' => $_SESSION['admin']['timezone'],
        'file_type' => 'EXCEL',
        'report_location' => 'payables_listing',
        'report_key' => 'payables_export',
        'incr' => $incr,
        'sch_params' => $sch_params,
        'check_validation' => false,
      );
      $_POST['added_or_transaction_date'] = 'added_date';
      $exportreport = new ExportReport(0,$config_data);
      $response = $exportreport->run();
      echo json_encode($response);
      exit();
  }

	try {

       

        $sel_sql = "SELECT 
                    pd.created_at AS ADDED_DATE,
                    o.display_id AS ORDER_ID,
                    ws.website_id AS POLICY_ID,
                    IF(pd.payee_type='Agent',pd.type,pd.payee_type) as PAYEE_TYPE,
                    IF(pd.payee_type='Agent',ag.rep_id,pf.display_id) as PAYEE_ID,
                    IF(pd.commission_id > 0,CONCAT(ag.fname,' ',ag.lname),pf.name) as PAYEE,
                    
                    CASE
                        WHEN pd.commission_id > 0 THEN
                            CASE 
                                WHEN comm.is_advance='Y' THEN 
                                    CASE 
                                        WHEN comm.sub_type='Reverse' THEN (SELECT CONCAT(advance_month,' Months') FROM commission WHERE advance_reverse_id = comm.id LIMIT 1)
                                        ELSE CONCAT(comm.advance_month,' Months')
                                    END                                    
                                WHEN comm.is_pmpm_comm='Y' THEN CONCAT('$',ABS(comm.amount))
                                WHEN comm.is_fee_comm='Y' THEN IF(comm.original_amount IS NOT NULL AND comm.original_amount != 0,CONCAT('$',ABS(comm.original_amount)),CONCAT(comm.percentage,'%'))
                                ELSE IF(comm.original_amount IS NOT NULL AND comm.original_amount != 0,CONCAT('$',ABS(comm.original_amount)),CONCAT(comm.percentage,'%'))
                            END
                        WHEN fee_prd.id IS NOT NULL THEN
                            CASE 
                                WHEN fee_matrix.price_calculated_on = 'Percentage' THEN CONCAT(pd.payout,'%')
                                ELSE CONCAT('$',pd.payout)
                            END
                        ELSE CONCAT('$',pd.payout)
                    END AS PAYOUT,

                    pd.credit AS CREDIT,
                    pd.debit AS DEBIT,
                    pm.product_code AS PRODUCT_ID,
                    pm.name AS PRODUCT_NAME,
                    md5(c.id) as member_id,
                    CONCAT(c.fname,' ',c.lname) AS MEMBER_NAME,
                    c.rep_id AS MEMBER_REP_ID,
                    t.transaction_id AS TRANSACTION_ID,
                    t.transaction_status AS TRANSACTION_STATUS,
                    t.id as ai_transaction_id,
                    fee_prd.product_code as FEE_CODE,
                    fee_prd.name as FEE_NAME
                    FROM payable py 
                    JOIN payable_details pd ON(pd.payable_id = py.id AND pd.is_deleted='N')
                    JOIN orders o ON(o.id=py.order_id)
                    JOIN order_details od ON(od.order_id=py.order_id AND od.id=py.order_detail_id AND od.is_deleted='N')
                    JOIN website_subscriptions ws ON(ws.id = od.website_id)
                    JOIN prd_main pm ON(pm.id = py.product_id)
                    LEFT JOIN prd_fees pf ON(pf.id=pd.payee_id AND pf.is_deleted='N')
                    LEFT JOIN prd_main fee_prd ON (fee_prd.id = pd.fee_price_id)
                    LEFT JOIN prd_matrix fee_matrix ON(fee_matrix.product_id=fee_prd.id AND fee_matrix.is_deleted='N')
                    LEFT JOIN customer ag ON (ag.id = pd.payee_id AND pd.payee_type='Agent')
                    LEFT JOIN customer c ON(c.id=ws.customer_id AND c.is_deleted='N')
                    LEFT JOIN commission comm ON(comm.id=pd.commission_id AND comm.is_deleted='N')
                    JOIN transactions t ON(t.id=pd.transaction_tbl_id)
                    WHERE 1 $incr
                    GROUP BY pd.id
                    ORDER BY pd.created_at DESC";
        $paginate = new pagination($page, $sel_sql, $options);
        
		if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);

            $DebitAmt = 0;
            $DebitCnt = 0;
            $CreditAmt = 0;
            $CreditCnt = 0;
            if($total_rows > 0) {
                $tmp_res = $pdo->select($sel_sql,$sch_params);
                foreach ($tmp_res as $key => $tmp_row) {
                    if($tmp_row['DEBIT'] != 0) {
                        $DebitCnt++;
                        $DebitAmt += abs($tmp_row['DEBIT']);
                    }

                    if($tmp_row['CREDIT'] != 0) {
                        $CreditCnt++;
                        $CreditAmt += abs($tmp_row['CREDIT']);
                    }
                }
            }
            $TotalCnt = $DebitCnt + $CreditCnt;
            $TotalAmt = $DebitAmt - $CreditAmt;

		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

    /*   * ****************    Export Code End ******************** */
    include_once 'tmpl/account_payable.inc.php';
	exit;
}

$description['ac_message'] =array(
  'ac_red_1'=>array(
    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
    'title'=>$_SESSION['admin']['display_id'],
  ),
  'ac_message_1' =>' read payables page'
);
$desc=json_encode($description);
activity_feed(3,$_SESSION['admin']['id'], 'Admin' ,$_SESSION['admin']['id'], 'Admin', 'Admin Read Payable Page.',$_SESSION['admin']['name'],"",$desc);

$company_arr = get_active_global_products_for_filter(0,false,true);
$selectize = true;

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'account_payable.inc.php';
include_once 'layout/end.inc.php';
?>