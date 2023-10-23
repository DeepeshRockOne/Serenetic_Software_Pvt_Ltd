<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id=$_GET['commission'];
$total_agents=$_GET['total_agents'];

$sel_sql = "SELECT p.name,p.type,p.product_code,p.id as prod_id 
  FROM commission_rule cr
  JOIN prd_main p ON (cr.product_id=p.id)  
  WHERE md5(cr.id)= :id";
$res_sql = $pdo->selectOne($sel_sql, array(":id" => $id));

$sch_params = array(":id"=>$id);
$has_querystring = false;

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
    'url' => 'commission_agents_assigned.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = !empty($_GET['page']) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

try {
    $sel_sql = "SELECT c.rep_id,c.fname,c.lname,c.status 
		FROM customer c
		LEFT JOIN agent_commission_rule acr ON (acr.agent_id=c.id AND acr.is_deleted='N')
		WHERE md5(acr.commission_rule_id) = :id GROUP BY c.id";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }


$template = 'commission_agents_assigned.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
