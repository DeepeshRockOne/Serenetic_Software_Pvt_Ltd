<?php
include_once __DIR__ . '/layout/start.inc.php';

$incr = "";
$sch_params = array();

$id = checkIsset($_GET["id"]);
$is_ajaxed = isset($_GET['is_ajaxed'])?$_GET['is_ajaxed']:'';
$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
$todate = isset($_GET["todate"])?$_GET["todate"]:"";
$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";
$products = isset($_GET["products"])?$_GET["products"]:"";
$order_id = isset($_GET["order_id"])?$_GET["order_id"]:"";
$transaction_id = isset($_GET["transaction_id"])?$_GET["transaction_id"]:"";
$payee_type = isset($_GET["payee_type"])?$_GET["payee_type"]:array();
$fee_id = isset($_GET["fee_id"])?$_GET["fee_id"]:"";
$payee_id = isset($_GET["payee_id"])?$_GET["payee_id"]:"";
$policy_id = isset($_GET["policy_id"])?$_GET["policy_id"]:"";
$paymentType = checkIsset($_GET['paymentType']);
$coverage_period = checkIsset($_GET['coverage_period'],'arr');
$order_status = checkIsset($_GET['order_status'],'arr');

$has_querystring = false;

$queryJoin = "";

$sch_params[':rp_id'] = $id ;

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

if(!empty($products)){
	$products = implode("','", $products);
    $incr .= " AND (pm.id IN('".$products."') OR pm.parent_product_id IN('".$products."'))";
} 

if (strpos($order_id, ',')){	
	$order_id = str_replace(',',"','",$order_id);
	$order_id = str_replace(' ',"",$order_id);
}

if ($order_id != "") {
	$incr .= " AND o.display_id IN ('".$order_id."')";
}

if (strpos($transaction_id, ',')){	
	$transaction_id = str_replace(',',"','",$transaction_id);
	$transaction_id = str_replace(' ',"",$transaction_id);
}

if ($transaction_id != "") {
	$incr .= " AND t.transaction_id IN ('".$transaction_id."')";
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

if (strpos($payee_id, ',')){	
	$payee_id = str_replace(',',"','",$payee_id);
	$payee_id = str_replace(' ',"",$payee_id);
}
$tbl_incr = '';
if ($payee_id != "") {
    $incr .= " AND CASE WHEN pd.payee_type='Agent' THEN ag.rep_id ELSE pf.display_id END IN('".$payee_id."')";
}

if (strpos($policy_id, ',')){
	$policy_id = str_replace(',',"','",$policy_id);
	$policy_id = str_replace(' ',"",$policy_id);
}
if ($policy_id != "") {
	$incr .= " AND ws.website_id IN ('".$policy_id."')";
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

if (count($sch_params) > 1) {
	$has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$per_page = $_GET['pages'];
	$has_querystring = true;
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
$options = array(
	'results_per_page' => $per_page,
	'url' => 'effective_orders_payable.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);
$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	try {
		 $sel_sql = "SELECT 
                    pd.created_at AS ADDED_DATE,
                    o.display_id AS ORDER_ID,
                    ws.website_id AS POLICY_ID,
                    IF(pd.payee_type='Agent',pd.type,pd.payee_type) AS PAYEE_TYPE,
                    IF(pd.payee_type='Agent',ag.rep_id,pf.display_id) AS PAYEE_ID,
                    IF(pd.commission_id > 0,CONCAT(ag.fname,' ',ag.lname),pf.name) AS PAYEE,
                    pd.credit AS CREDIT,
                    pd.debit AS DEBIT,
                    pm.product_code AS PRODUCT_ID,
                    pm.name AS PRODUCT_NAME,
                    t.transaction_id AS TRANSACTION_ID,
                    t.transaction_status AS TRANSACTION_STATUS,
                    t.id AS ai_transaction_id,
                    fee_prd.product_code AS FEE_CODE,
                    fee_prd.name AS FEE_NAME
                FROM regenerated_payable rp
				JOIN regenerated_order_payable ro ON(rp.id=ro.request_id)
                JOIN payable py ON(ro.payable_id = py.id AND py.is_deleted='N')
                JOIN payable_details pd ON(pd.payable_id = py.id AND pd.is_deleted='N' AND pd.id = ro.payable_detail_id)
                JOIN orders o ON(o.id=py.order_id)
                JOIN order_details od ON(od.order_id=py.order_id AND od.product_id=py.product_id AND od.is_deleted='N')
                JOIN website_subscriptions ws ON(ws.id = od.website_id)
                JOIN prd_main pm ON(pm.id = ws.product_id)
                LEFT JOIN prd_fees pf ON(pf.id=pd.payee_id AND pf.is_deleted='N')
                LEFT JOIN prd_main fee_prd ON (fee_prd.id = pd.fee_price_id)
                LEFT JOIN prd_matrix fee_matrix ON(fee_matrix.product_id=fee_prd.id AND fee_matrix.is_deleted='N')
                LEFT JOIN customer ag ON (ag.id = pd.payee_id AND pd.payee_type='Agent')
                LEFT JOIN commission comm ON(comm.id=pd.commission_id AND comm.is_deleted='N')
                JOIN transactions t ON(t.id=pd.transaction_tbl_id)
                WHERE md5(rp.id) = :rp_id ".$incr ."
                GROUP BY ro.id
                ORDER BY ro.created_at DESC";
        // pre_print($sel_sql);
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/effective_orders_payable.inc.php';
	exit;
}

$productSql="SELECT p.id,p.name,p.product_code,p.type,if(p.product_type='Healthy Step','Healthy Step',c.title) as title
            FROM prd_main p 
            LEFT JOIN prd_category c ON (c.id = p.category_id)
            where (p.type!='Fees' OR (p.product_type='Healthy Step' AND p.record_type = 'Primary')) AND p.is_deleted='N' AND p.status='Active'  GROUP BY p.id ORDER BY name ASC";

$company_arr = $pdo->selectGroup($productSql,array(),'title');

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'effective_orders_payable.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>