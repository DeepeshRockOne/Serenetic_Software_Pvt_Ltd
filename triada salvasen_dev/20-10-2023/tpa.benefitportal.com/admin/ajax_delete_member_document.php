<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$document_id = isset($_POST['removeId'])? $_POST['removeId']:'';
$member_id = isset($_POST['member_id'])? $_POST['member_id']:'';

$memberResponse = $pdo->selectOne("SELECT id,rep_id FROM customer WHERE md5(id) = :id",array(":id" => $member_id));
$rep_id = $memberResponse['rep_id'];
$id = $memberResponse['id'];

if(!empty($document_id)){
    
    $updParams=array(
        'is_deleted'=>'Y'
    );
    $updWhere=array(
        'clause'=>'id=:id',
        'params'=>array(
        ":id"=>$document_id,
        )
    );
    $pdo->update("customer_document", $updParams, $updWhere);

    $description['ac_message'] =array( 'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']), 
        'title'=>$_SESSION['admin']['display_id'], 
    ), 
    'ac_message_1' =>'Removed Member Document on (', 
    'ac_red_2'=>array( 
        'href'=> $ADMIN_HOST.'/members_details.php?id='.$member_id, 
        'title'=> $rep_id,
    ), 
    'ac_message_2' => ')', );
    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $id, 'customer','Admin Removed Member Document', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    $response['status'] = 'success';
}else{
    $response['status'] = 'fail';
}
header('Content-Type: application/json');
	echo json_encode($response);
	exit;
?>