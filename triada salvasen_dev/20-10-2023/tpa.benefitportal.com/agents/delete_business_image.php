<?php
include_once __DIR__ . '/includes/connect.php';
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
            'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
            'title'=> $_SESSION['agents']['rep_id'],
        ),
        'ac_message_1' =>' Updated Profile <br/>',
        'ac_message_2' =>' Brand Logo Removed'
    );
    $desc=json_encode($desc);
    activity_feed(3,$_SESSION['agents']['id'],'Agent',$id,'customer','Agent Profile Updated',"","",$desc);
    $_SESSION['agents']['brand_icon'] = '';
    $response['status'] = true;
    $response['message'] = "Brand logo deleted successfully";
    if(isset($_REQUEST['personal_branding'])) {
        setNotifySuccess('Brand logo deleted successfully');
    }
}
echo json_encode($response);
dbConnectionClose();
?>