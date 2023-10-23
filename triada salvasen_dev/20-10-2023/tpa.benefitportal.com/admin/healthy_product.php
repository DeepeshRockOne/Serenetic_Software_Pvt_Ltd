<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$product_id = checkIsset($_GET['product_id']);
$health_id = checkIsset($_GET['health_id']);

$sel_sql = "SELECT count(DISTINCT paf.product_id) as total_products,GROUP_CONCAT(paf.product_id) as ids
FROM prd_main p
JOIN prd_assign_fees paf ON(paf.fee_id=p.id  AND paf.is_deleted='N' )
JOIN prd_fees pf ON(pf.id=paf.prd_fee_id AND pf.is_deleted='N'  AND pf.setting_type='Healthy Step')
WHERE p.type='Fees' AND p.product_type='Healthy Step' AND p.record_type='Primary' AND p.is_deleted='N' AND md5(p.id)=:prd_id AND md5(pf.id)=:health_id";
$res_sql = $pdo->selectOne($sel_sql, array(":prd_id" => $product_id,":health_id"=>$health_id));

$sch_params = array(":prd_id" => $product_id,":health_id"=>$health_id);
$has_querystring = false;

if (count($sch_params) > 0) {
  $has_querystring = true;
}
  $has_querystring = true;
  $per_page = 5;
$query_string = $has_querystring ? (!empty($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'healthy_product.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = !empty($_GET['page']) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

try {
    $sel_sql = "SELECT prm.id,prm.name,prm.product_code,prm.status
    FROM prd_main p
    JOIN prd_assign_fees paf ON(paf.fee_id=p.id AND  paf.is_deleted='N')
    JOIN prd_fees pf ON(pf.id=paf.prd_fee_id AND pf.is_deleted='N' AND pf.setting_type='Healthy Step')
    JOIN prd_main prm  ON(prm.id=paf.product_id AND prm.is_deleted='N')
    WHERE p.type='Fees' AND p.product_type='Healthy Step' AND p.record_type='Primary' AND p.is_deleted='N'  AND md5(p.id)=:prd_id AND md5(pf.id)=:health_id
    ORDER BY prm.name ASC";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }

$template = "healthy_product.inc.php";
include_once 'layout/iframe.layout.php';
?>