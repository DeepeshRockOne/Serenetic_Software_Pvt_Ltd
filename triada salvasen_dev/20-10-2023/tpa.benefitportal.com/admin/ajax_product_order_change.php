<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$productList = $_POST["data"];
$res = array();

$order_by=1;
if(!empty($productList)){
  foreach ($productList as $key => $product) {
    $diff = explode("_", $product);

    $productType=$diff[0];
    $id=$diff[1];
    $connection_id=isset($diff[2]) ? $diff[2] : 0;

    if($productType=='main'){
      $update_params = array(
          'order_by' => $order_by,
      );
      $update_where = array(
          'clause' => 'id = :id',
          'params' => array(
              ':id' => makeSafe($id)
          )
      );
      $pdo->update("prd_main", $update_params, $update_where);
      $order_by++;
    }else{
      $sqlProduct = "SELECT product_id FROM prd_connected_products WHERE connection_id=:connection_id AND is_deleted='N'";
      $resProduct = $pdo->select($sqlProduct,array(":connection_id"=>$connection_id));

      if(!empty($resProduct)){
        foreach ($resProduct as $keyPr => $valuePr) {
          $update_params = array(
              'order_by' => $order_by,
          );
          $update_where = array(
              'clause' => 'id = :id',
              'params' => array(
                  ':id' => makeSafe($valuePr['product_id'])
              )
          );
          $pdo->update("prd_main", $update_params, $update_where);

          $update_params = array(
              'order_by' => $order_by,
          );
          $update_where = array(
              'clause' => 'connection_id = :connection_id AND product_id = :product_id',
              'params' => array(
                  ':connection_id' => makeSafe($connection_id),
                  ':product_id' => makeSafe($valuePr['product_id'])
              )
          );
          $pdo->update("prd_connected_products", $update_params, $update_where);
          $order_by++;
        }
      }
    }

    
  }
  $res['status'] = 'success';
} else {
  $res['status'] = 'fail';
}
header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
