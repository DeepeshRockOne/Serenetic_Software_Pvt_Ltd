<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$address_id = $_POST['address_id'];

$i_query = "SELECT address FROM trigger_address WHERE id=:address_id";
$i_where = array(':address_id' => $address_id);
$i_res = $pdo->selectOne($i_query, $i_where);

$res = array();

if (!empty($i_res)) {
  $res['address'] = html_entity_decode($i_res['address']);
  $res['status'] = 'success';
} else {
  $res['status'] = 'fail';
}

header('Content-Type:appliaction/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>