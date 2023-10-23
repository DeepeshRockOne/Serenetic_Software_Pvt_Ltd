<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();

$connection_id = $_POST["connection_id"];
$res = array();

$query = "SELECT GROUP_CONCAT(CONCAT(p.name,'(',p.product_code,')')) as productInfo,pc.title as categoryName 
FROM prd_connected_products pcp 
JOIN prd_main p ON (p.id=pcp.product_id)
JOIN prd_category pc ON (pc.id = pcp.category_id)
WHERE pcp.connection_id = :connection_id AND pcp.is_deleted='N' 
group by pcp.connection_id";
$srow = $pdo->selectOne($query,array(":connection_id"=>$connection_id));

if (!empty($srow) && !empty($srow['productInfo']) && !empty($srow['categoryName'])) {
  $update_params = array(
      'is_deleted' => 'Y',
  );
  $update_where = array(
      'clause' => 'connection_id = :connection_id',
      'params' => array(
          ':connection_id' => makeSafe($connection_id)
      )
  );
  $pdo->update("prd_connected_products", $update_params, $update_where);

  $update_where = array(
      'clause' => 'id = :id',
      'params' => array(
          ':id' => makeSafe($connection_id)
      )
  );
  $pdo->update("prd_connections", $update_params, $update_where);

  $actProductInfo = $srow['productInfo'];
  $categoryName = $srow['categoryName'];
  $actFeed=$functionsList->generalActivityFeed('','','',$categoryName,$connection_id,'prd_connected_products','Admin Removed Connected Product','Removed Connected Product '.$actProductInfo.' For');
      
  $res['status'] = 'success';

} else {
  $res['status'] = 'fail';
}
header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
