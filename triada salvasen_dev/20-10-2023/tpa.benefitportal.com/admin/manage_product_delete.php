<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();
has_access(9);

$id = $_POST['id'];
$res = array();
$prdSql="SELECT id,product_code,parent_product_id FROM prd_main where md5(id) = :id or md5(parent_product_id) = :id AND is_deleted='N'";
$prdRes=$pdo->select($prdSql,array(":id"=>$id));

if(!empty($prdRes)){
  foreach ($prdRes as $key => $value) {
    $pupdate_params = array(
      'status' => 'Inactive',
      'is_deleted' => 'Y',
    );
    $pupdate_where = array(
      'clause' => 'id= :id',
      'params' => array(
          ':id' => makeSafe($value['id'])
      )
    );
    $pdo->update("prd_main", $pupdate_params, $pupdate_where);

    $actFeed=$functionsList->prdActtivityFeed($value['id'],$value['parent_product_id'],$value['product_code'],'Deleted Product','Deleted Product');  
  }
  $res['status'] = 'success';
  $res['msg'] = 'Product deleted successfully';
} else {
    $res['status'] = 'fail';
    $res['msg'] = 'Something is wrong here';
}
header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
