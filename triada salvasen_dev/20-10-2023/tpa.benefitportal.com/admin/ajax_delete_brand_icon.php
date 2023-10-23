<?php

include_once __DIR__ . '/includes/connect.php';
$admin_id = $_SESSION['admin']['id'];
$id = $_GET['id'];
$sql = "SELECT customer_id,brand_icon FROM customer_settings WHERE customer_id = :id";
$params = array(
    ':id' => makeSafe($id),
);
$row = $pdo->selectOne($sql, $params);
$response = array();
if (!$row) {
    $response['status'] = false;
    $response['message'] = "Picture not found.";
} else {
    if (file_exists($GROUPS_BRAND_ICON_DIR . $row['brand_icon'])) {
        unlink($GROUPS_BRAND_ICON_DIR . $row['brand_icon']);
    }
    $delete_params = array(
        'brand_icon' => '',
    );

   $u_where = array(
       'clause' => 'customer_id=:id',
        'params' => array(
            ':id' => $id,
        ),
    );
    $pdo->update('customer_settings', $delete_params, $u_where);
    $desc = array();
    $desc['ac_message'] = array(
        'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Updated Profile <br/>',
        'ac_message_2' =>' Brand Logo Removed'
    );
    $desc=json_encode($desc);
    activity_feed(3,$id,'Group',$id,'customer','Group Profile Updated',"","",$desc);
    $response['status'] = true;
    $response['message'] = "Brand logo deleted successfully";
}
echo json_encode($response);
dbConnectionClose();
?>