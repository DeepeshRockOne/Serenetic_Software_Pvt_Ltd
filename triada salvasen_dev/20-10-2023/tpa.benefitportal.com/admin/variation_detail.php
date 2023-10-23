<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$incr = "";
$sch_params = array();
$has_querystring = false;

$page_title = "Commission Variations";

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Commissions Builder";
$breadcrumbes[1]['link'] = "commission_builder.php";
$breadcrumbes[2]['title'] = "Commission Variations";
$breadcrumbes[2]['class'] = "Active";

$has_querystring = false;

$commission = !empty($_GET['commission']) ? $_GET['commission'] : 0;

if(!empty($commission)){
   $sch_params[':id'] = makeSafe($commission);
   $incr .= " AND md5(cr.parent_rule_id) = :id";	

   $variation_sql = "SELECT p.name,p.product_code
                FROM commission_rule cr 
                JOIN prd_main p ON (cr.product_id=p.id)  
                WHERE cr.is_deleted='N' AND md5(cr.id)=:id";
   $variation_where=array(":id"=>$commission);
   $variation_res=$pdo->selectOne($variation_sql,$variation_where);
}else{
  setNotifyError("No Commission Found");
  redirect($ADMIN_HOST.'/commission_builder.php');
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
    'url' => 'variation_detail.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = !empty($_GET['page']) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

try {
  $sel_sql = "SELECT md5(cr.id) as id,cr.rule_code,cr.created_at,cr.status,p.name,p.type, p.product_code,md5(p.id) as prod_id,count(DISTINCT(acr.id))as agent_total
      FROM commission_rule cr 
      JOIN prd_main p ON (cr.product_id=p.id)  
      LEFT JOIN agent_commission_rule acr ON (acr.commission_rule_id = cr.id AND acr.is_deleted='N')
      WHERE cr.is_deleted='N' " . $incr . " GROUP BY cr.id  ORDER BY cr.id DESC";
  $paginate = new pagination($page, $sel_sql, $options);
  if ($paginate->success == true) {
    $fetch_rows = $paginate->resultset->fetchAll();
    $total_rows = count($fetch_rows);
  }
} catch (paginationException $e) {
  echo $e;
  exit();
}


$template = 'variation_detail.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>

