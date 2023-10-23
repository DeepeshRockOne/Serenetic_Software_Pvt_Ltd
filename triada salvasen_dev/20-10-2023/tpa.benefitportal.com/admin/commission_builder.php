<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(28);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Commission Builder";
$breadcrumbes[1]['class'] = "Active";
$manage_commission = "active";

$incr = "";
$sch_params = array();

$has_querystring = false;

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : 0;
$extra_export_arr = array();
$commissionIds = isset($_GET['commissionIds']) ? $_GET['commissionIds'] : array();
$searchProduct = isset($_GET['searchProduct']) ? $_GET['searchProduct'] : array();
$searchStatus = isset($_GET['searchStatus']) ? $_GET['searchStatus'] : '';
$searchDate = isset($_GET['searchDate']) ? $_GET['searchDate'] : '';
$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
$todate = isset($_GET["todate"])?$_GET["todate"]:"";
$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";
$getfromdate = '';
$gettodate = '';

  if (!empty($commissionIds)) {
    $findCommSql = "SELECT GROUP_CONCAT(IF(cr.parent_rule_id > 0,cr.parent_rule_id,cr.id)) as ruleIds
      FROM commission_rule cr 
      WHERE cr.is_deleted='N' AND cr.id IN(".implode(",",$commissionIds).")
      ORDER BY cr.id DESC";
    $findCommRes = $pdo->selectOne($findCommSql);
    if(!empty($findCommRes["ruleIds"])){
      $incr.=" AND (cr.id IN(".$findCommRes["ruleIds"].") OR cr.parent_rule_id IN(".$findCommRes["ruleIds"]."))";
    }
  }


if (!empty($searchDate)) {
  if($searchDate == "Range" && !empty($fromdate) && !empty($todate)){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(cr.created_at) >= :fromdate AND DATE(cr.created_at) <= :todate";
    $getfromdate = $fromdate;
    $gettodate = $todate;
  }else if($searchDate == "Exactly" && !empty($added_date)){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(cr.created_at) = :added_date";
    $getfromdate = $added_date;
    $gettodate = $added_date;
  }else if($searchDate == "Before" && !empty($added_date)){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(cr.created_at) < :added_date";
    $getfromdate = $added_date;
    $gettodate = date('Y-m-d');
  }else if($searchDate == "After" && !empty($added_date)){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(cr.created_at) > :added_date";
    $getfromdate = date('Y-m-d');
    $gettodate = $added_date;
  }
}
if (!empty($searchProduct)) {
  $productList = implode(',', $searchProduct);
  $incr .= " AND prd.id IN ($productList)";
}
if (!empty($searchStatus)) {
  $sch_params[':status'] = makeSafe($searchStatus);
  $incr.=" AND cr.status =:status";
}

$export_val = isset($_GET['export_val']) ? $_GET["export_val"] : '';

	if(!empty($export_val)){

		include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
		
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
		$job_id=add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Commission Setup","commission_setup",$incr, $sch_params,$extra_export_arr,'commission_setup');

		echo json_encode(array("status"=>"success","message"=>"Your export request is added")); 
		exit;
	}

if (count($sch_params) > 0) {
  $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (!empty($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';


$options = array(
    'results_per_page' => $per_page,
    'url' => 'commission_builder.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = !empty($_GET['page']) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT md5(cr.id) as id,cr.rule_code,cr.created_at,cr.status,prd.name,prd.type, prd.product_code,md5(prd.id) as prod_id,count(DISTINCT(cv.id))as variation_total,count(DISTINCT(c.id))as agent_total
      FROM commission_rule cr 
      JOIN prd_main prd ON (cr.product_id=prd.id)  
      LEFT JOIN commission_rule cv ON (cv.parent_rule_id=cr.id AND cv.is_deleted='N')
      LEFT JOIN agent_commission_rule acr ON (acr.commission_rule_id = cr.id AND acr.is_deleted='N')
      LEFT JOIN customer c ON(c.id = acr.agent_id)
      WHERE cr.parent_rule_id=0 AND cr.is_deleted='N' " . $incr . " 
      GROUP BY cr.id  
      ORDER BY cr.id DESC";

    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
      
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }

  include_once 'tmpl/commission_builder.inc.php';
  exit;
}


$productSearchList=array();

$productSearchList = get_active_global_products_for_filter(0,true,true);

$selCommRules = "SELECT cr.id,cr.rule_code
      FROM commission_rule cr 
      WHERE cr.is_deleted='N' 
      GROUP BY cr.id  
      ORDER BY cr.id DESC";
$resCommRules = $pdo->select($selCommRules);

$page_title = "Commission Builder";


$exStylesheets = array(
  'thirdparty/multiple-select-master/multiple-select.css'.$cache  
);

$exJs = array(
  'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache    
);

$template = 'commission_builder.inc.php';
include_once 'layout/end.inc.php';
?>