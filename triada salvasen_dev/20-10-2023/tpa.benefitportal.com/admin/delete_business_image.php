<?php
include_once __DIR__ . '/includes/connect.php';
$id = $_GET['id'];
$sql = "SELECT c.id as id,cs.customer_id,cs.brand_icon,c.id,c.fname,c.lname,c.rep_id FROM customer_settings cs JOIN customer c ON(c.id=cs.customer_id) WHERE md5(cs.customer_id) = :id AND c.is_deleted='N'";
$params = array(
    ':id' => makeSafe($id),
);
$row = $pdo->selectOne($sql, $params);
$response = array();
if (!$row) {
    $response['status'] = false;
    $response['message'] = "Picture not found.";
} else {
    $id = $row['id'];
    if (file_exists($AGENTS_BRAND_ICON . $row['brand_icon'])) {
        unlink($AGENTS_BRAND_ICON . $row['brand_icon']);
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
            'href'=> $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=> $_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Brand Logo Removed For Agent '.$row['fname'].' '.$row['lname'].' ',
        'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($id),
            'title'=> $row['rep_id'],
        ),
    );

    $desc=json_encode($desc);
    activity_feed(3,$_SESSION['admin']['id'],'Admin',$id,'Agent','Agent Profile Updated',"","",$desc);

    $response['status'] = true;
    $response['message'] = "Brand logo deleted successfully";
}
echo json_encode($response);
dbConnectionClose();
?>