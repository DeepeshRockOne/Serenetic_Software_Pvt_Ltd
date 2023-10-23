<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_GET['commission'];

$sel_sql = "SELECT cr.commission_on,cr.commission_json,p.name,p.type, p.product_code,p.id as prod_id 
  FROM commission_rule cr
  JOIN prd_main p ON (cr.product_id=p.id)  
  WHERE md5(cr.id)= :id";
$res_sql = $pdo->selectOne($sel_sql, array(":id" => $id));

$commission_json = json_decode($res_sql['commission_json'], true);

$template = 'commission_per_level.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>