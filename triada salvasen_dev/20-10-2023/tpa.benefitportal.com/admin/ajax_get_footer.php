<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$footer_id = $_POST['footer_id'];

$i_query = "SELECT content FROM trigger_footer WHERE id=:footer_id";
$i_where = array(':footer_id' => $footer_id);
$i_res = $pdo->selectOne($i_query, $i_where);

$res = array();

if (!empty($i_res)) {
  $res['footer'] = html_entity_decode($i_res['content']);
  $res['status'] = 'success';
} else {
  $res['status'] = 'fail';
}

header('Content-Type:appliaction/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>