<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$incr = "";
$sch_params = array();
$SortBy = "s.fname";
$SortDirection = "ASC";
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

$agentId = checkIsset($_GET["agentId"]);
$agentName = checkIsset($_GET["agentName"]);

  $join_range = checkIsset($_GET['join_range']);
  $fromdate = checkIsset($_GET["fromdate"]);
  $todate = checkIsset($_GET["todate"]);
  $today = date("m/d/Y");
  $added_date = !empty($_GET["added_date"]) ? $_GET["added_date"] : $today;

  if($join_range != ""){
    if($join_range == "Range" && $fromdate!='' && $todate!=''){
      $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
      $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
      $incr.=" AND DATE(cw.created_at) >= :fromdate AND DATE(cw.created_at) <= :todate";
    }else if($join_range == "Exactly" && $added_date!=''){
      $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
      $incr.=" AND DATE(cw.created_at) = :added_date";
    }else if($join_range == "Before" && $added_date!=''){
      $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
      $incr.=" AND DATE(cw.created_at) < :added_date";
    }else if($join_range == "After" && $added_date!=''){
      $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
      $incr.=" AND DATE(cw.created_at) > :added_date";
    }
  }

$agentId = cleanSearchKeyword($agentId); 
$agentName = cleanSearchKeyword($agentName); 
 
if (!empty($agentId)) {
  $sch_params[':agentId'] = "%" . makeSafe($agentId) . "%";
  $incr .= " AND s.rep_id LIKE :agentId";
}

if (!empty($agentName)) {
  $sch_params[':agentName'] = "%" . makeSafe($agentName) . "%";
  $incr .= " AND (s.fname LIKE :agentName or s.lname LIKE :agentName or CONCAT(s.fname,s.lname) LIKE :agentName)";
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}

  $per_page=10;
  if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
  }
  $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

  $options = array(
      'results_per_page' => $per_page,
      'url' => 'commission_wallet.php?is_ajaxed=1&' . $query_string,
      'db_handle' => $pdo->dbh,
      'named_params' => $sch_params
  );

  $page = isset($_GET["page"]) && $_GET['page'] > 0 ? $_GET['page'] : 1;
  $options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
   $sel_sql = "SELECT cw.id as walletId,cw.balance,s.id as agentId,s.rep_id as agentDispId,CONCAT(s.fname,' ',s.lname) as agentName
    FROM commission_wallet cw
    JOIN customer as s on(s.id = cw.agent_id)
    LEFT JOIN direct_deposit_account dda ON(s.id = dda.customer_id)
    WHERE s.id = cw.agent_id $incr GROUP BY cw.id";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();

      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/commission_wallet.inc.php';
  exit;
}
?>