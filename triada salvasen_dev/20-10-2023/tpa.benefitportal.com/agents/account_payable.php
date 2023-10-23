<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
agent_has_access(13);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "My Production";
$breadcrumbes[2]['title'] = "Payables";
$breadcrumbes[0]['link'] = 'account_payable.php';
$breadcrumbes[2]['class'] = "Active";
$agent_id = $_SESSION['agents']['id'];

$incr = '';
$schParams = array();
$SortBy = "pd.id";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$reportIncr = $sch_params = array();
$has_querystring = false;

$today = date("m/d/Y");
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
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
$payee_id = isset($_GET["payee_id"])? (!is_array($_GET["payee_id"]) ? array($_GET["payee_id"]) : $_GET["payee_id"]) : array();
if ($is_ajaxed) { 
$tree_agent_id = !empty($_GET['tree_agent_id']) ? $_GET['tree_agent_id'] : "";
$products = isset($_GET["products"])?$_GET["products"]:"";
$order_id = isset($_GET["order_id"])?$_GET["order_id"]:"";
$transaction_id = isset($_GET["transaction_id"])?$_GET["transaction_id"]:"";
$payee_type = isset($_GET["payee_type"])?$_GET["payee_type"]:array();
$fee_id = isset($_GET["fee_id"])?$_GET["fee_id"]:"";
$policy_id = isset($_GET["policy_id"])?$_GET["policy_id"]:"";
$rep_id = !empty($_GET['member_id']) ? explode(",", $_GET['member_id']) : "";
$paymentType = checkIsset($_GET['paymentType']);
$coverage_period = checkIsset($_GET['coverage_period'],'arr');
$order_status = checkIsset($_GET['order_status'],'arr');

if($join_range != ""){
	$custom_date = ' pd.created_at ';
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE($custom_date) >= :fromdate AND DATE($custom_date) <= :todate ";
  }else if($join_range == "Exactly" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE($custom_date) = :added_date ";
  }else if($join_range == "Before" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE($custom_date) < :added_date ";
  }else if($join_range == "After" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE($custom_date) > :added_date ";
  }
}

if(!empty($displayDirectEnroll) && in_array('Members', $displayDirectEnroll)){
  $incr .= " AND (ag.id = :agent_id OR (ag.sponsor_id =:agent_id AND scs.agent_coded_level = 'LOA'))";
  $sch_params[':agent_id']=$agent_id;
}else{
  $incr .= " AND (ag.upline_sponsors LIKE '%,$agent_id,%' OR ag.id=:agent_id) ";
  $sch_params[':agent_id']=$agent_id;
}

if(!empty($products)){
	$products = implode("','", $products);
    $incr .= " AND (pm.id IN('".$products."') OR pm.parent_product_id IN('".$products."'))";
} 

if(!empty($order_id)){
  $order_id = str_replace(" ", "", $order_id);
  $order_id = explode(',', $order_id);
  $order_id = "'" . implode("','", $order_id) . "'";
  $incr .= " AND o.display_id IN ($order_id)";
  $reportIncr['table_join'] = ' JOIN orders o ON(o.id=py.order_id) ';
}

if(!empty($transaction_id)){
  $transaction_id = str_replace(" ", "", $transaction_id);
  $transaction_id = explode(',', $transaction_id);
  $transaction_id = "'" . implode("','", $transaction_id) . "'";
  $incr .= " AND t.transaction_id IN ($transaction_id)";
}

if (!empty($payee_type)) {
    $payee_type = implode("','", $payee_type);
    $incr .= " AND CASE WHEN pd.payee_type='Agent' THEN pd.type ELSE pd.payee_type END IN('".$payee_type."')";
}

if (strpos($fee_id, ',')){	
	$fee_id = str_replace(',',"','",$fee_id);
	$fee_id = str_replace(' ',"",$fee_id);
}

if ($fee_id != "") {
    $incr .= " AND fee_prd.product_code IN('".$fee_id."')";
}

if (!empty($payee_id)) {
  $payee_id = implode("','", $payee_id);
  $incr .= " AND ag.rep_id IN('".$payee_id."')";
}

if(!empty($tree_agent_id)){
  $incr .= " AND (ag.id IN(".implode(",",$tree_agent_id).") OR (ag.sponsor_id IN (".implode(",",$tree_agent_id).") AND csag.agent_coded_id = 1)) ";
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

$export_val = isset($_GET['export']) ? $_GET["export"] : '';

  if(!empty($export_val) && $export_val == 'payables_export'){
    include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';

    $job_id=add_export_request_api('EXCEL', $_SESSION['agents']['id'], 'Agent', 'Payable Export Agent','__agent_payables_listing', $incr, $sch_params,$reportIncr);
    $reportDownloadURL = $AWS_REPORTING_URL['__agent_payables_listing']."&job_id=".$job_id;
  
    $ch = curl_init($reportDownloadURL);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, false);
    $apiResponse = curl_exec($ch);
    curl_close($ch);
  
    echo json_encode(array("status"=>"success","message"=>"Your export request is added","url"=>$reportDownloadURL)); 
    exit;
  }

	try {

        $sel_sql = "SELECT 
                    pd.created_at AS ADDED_DATE,
                    o.display_id AS ORDER_ID,
                    ws.website_id AS POLICY_ID,
                    IF(pd.payee_type='Agent',pd.type,pd.payee_type) as PAYEE_TYPE,
                    IF(pd.payee_type='Agent',csag.company_name,'') as agencyNameDis,
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
                    JOIN customer ag ON (ag.id = pd.payee_id AND pd.payee_type='Agent')
                    JOIN customer_settings csag ON (csag.customer_id = ag.id AND csag.account_type='Business' AND csag.company_name!='')
                    LEFT JOIN prd_fees pf ON(pf.id=pd.payee_id AND pf.is_deleted='N')
                    LEFT JOIN prd_main fee_prd ON (fee_prd.id = pd.fee_price_id)
                    LEFT JOIN prd_matrix fee_matrix ON(fee_matrix.product_id=fee_prd.id AND fee_matrix.is_deleted='N')
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
    'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
    'title'=>$_SESSION['agents']['rep_id'],
  ),
  'ac_message_1' =>' read payables page'
);
$desc=json_encode($description);
activity_feed(3,$_SESSION['agents']['id'], 'Agent' ,$_SESSION['agents']['id'], 'Agent', 'Agent Read Payable Page.',$_SESSION['agents']['fname'].' '.$_SESSION['agents']['lname'],"",$desc);

// $company_arr = get_active_global_products_for_filter();

$tree_agent_sql = "SELECT c.id,c.rep_id,c.fname,c.lname,cs.company_name
					FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id)
          where c.type='Agent' AND c.is_deleted = 'N' AND (c.upline_sponsors LIKE '%,$agent_id,%' OR c.id=:agent_id) ";
$tree_agent_res = $pdo->select($tree_agent_sql,array(":agent_id"=>$agent_id));

$selectize = true;
$company_arr = get_active_global_products_for_filter($_SESSION['agents']['id'],false,true);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'account_payable.inc.php';
include_once 'layout/end.inc.php';
?>