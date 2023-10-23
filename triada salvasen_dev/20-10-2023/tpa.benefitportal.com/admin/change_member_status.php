<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$status = $_REQUEST['status'];
$id = $_POST['id'];
$admin_id = $_SESSION['admin']['id'];
$res = array();

$query = "SELECT id, status, type,concat(fname,' ',lname) as name,rep_id FROM customer WHERE md5(id) =:id and is_deleted='N'";
$srow = $pdo->selectOne($query, array(':id' => $id));

if (!empty($srow)) {
  $update_params = array(
    'status' => makeSafe($status)
  );
  $update_where = array(
    'clause' => 'id = :id',
    'params' => array(
      ':id' => makeSafe($srow['id'])
    )
  );

  $pdo->update("customer", $update_params, $update_where);

  $old_status = get_member_display_status($srow['status']);
  $new_status = $status;

  $description['ac_message'] = array(
    'ac_red_1' => array(
      'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
      'title' => $_SESSION['admin']['display_id'],
    ),
    'ac_message_1' => ' updated Member ' . $srow['name'] . '(',
    'ac_red_2' => array(
      'href' => $ADMIN_HOST . '/members_details.php?id=' . $id,
      'title' => $srow['rep_id'],
    ),
    'ac_message_2' => ') status from ' . $old_status . ' to ' . $new_status,
  );

  $desc = json_encode($description);
  activity_feed(3, $_SESSION['admin']['id'], 'Admin', $srow['id'], 'Customer', 'Status Updated', $_SESSION['admin']['name'], "", $desc);
  // activity_feed(3, $srow['id'] , 'customer', $srow['id'], 'customer', 'Status Updated', $srow['name'], "", $desc);
  $res['status'] = 'success';
  $res['msg'] = 'Status Changed Successfully';
} else {
  $res['status'] = 'error';
  $res['msg'] = 'Something went wrong';
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
