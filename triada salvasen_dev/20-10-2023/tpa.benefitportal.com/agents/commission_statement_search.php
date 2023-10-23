<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$sch_params = array();
$SortBy = "cs.pay_period";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$has_querystring = false;


$sch_params = array();
$incr = "";

$is_ajaxed = $_GET['is_ajaxed'];
$agent_id = $_SESSION['agents']['id'];

$sch_params[":agentId"] = $agent_id;

$idEmailName = $_GET["idEmailName"];

$idEmailName = cleanSearchKeyword($idEmailName); 
  
if ($idEmailName != "") {
  $sch_params[':idEmailName'] = "%" . $idEmailName . "%";
  $incr.=" AND (o.display_id LIKE :idEmailName OR o.status LIKE :idEmailName OR c.rep_id LIKE :idEmailName OR c.fname LIKE :idEmailName OR c.lname LIKE :idEmailName OR CONCAT(c.fname,' ',c.lname) LIKE :idEmailName OR CONCAT(c.lname,' ',c.fname) LIKE :idEmailName OR c.email LIKE :idEmailName)";

}

if (count($sch_params) > 0) {
  $has_querystring = true;
}
$per_page=10;
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (!empty($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'commission_statement_search.php?is_ajaxed=1&' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = !empty($_GET['page']) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {

      $sel_sql = "SELECT 
             DATE(cs.created_at) as orderDate,
             o.display_id as orderDispId,o.status as orderStatus,
             cs.pay_period,cs.commission_duration,
             CONCAT(c.fname,' ',c.lname) as memberName,
             c.rep_id as memberRepId,
             od.product_name as prdName,
             od.unit_price as prdPrice,
             IF(o.is_renewal='N','New Business','Renewals') as saleType,

             SUM(cs.amount) as totalComm,
             SUM(if(cs.sub_type='New' OR cs.sub_type='Renewals',cs.amount,0)) as earnedComm,
             SUM(if(cs.sub_type='Advance',cs.amount,0)) as advanceComm,
             SUM(if(cs.sub_type='PMPM',cs.amount,0)) as pmpmComm,
             SUM(if(cs.sub_type='Reverse',cs.amount,0)) as reverseComm,
             SUM(if(cs.sub_type='Fee',cs.amount,0)) as feeComm,
             SUM(if(cs.type='Adjustment',cs.amount,0)) as adjustComm

            FROM commission cs
             JOIN orders o ON (o.id = cs.order_id)
             JOIN order_details od ON (od.id = cs.order_detail_id AND od.is_deleted='N')
             LEFT JOIN prd_main p ON(p.id=cs.product_id)
             LEFT JOIN customer c ON (c.id = cs.payer_id)
             WHERE cs.customer_id = :agentId AND cs.status IN ('Approved','Pending') 
             AND cs.amount != 0 AND cs.is_deleted = 'N' " . $incr . "
            GROUP BY cs.order_id,cs.order_detail_id ORDER BY  $SortBy $currSortDirection";      
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/commission_statement_search.inc.php';
  exit;
}
?>