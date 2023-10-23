<?php
include_once 'layout/start.inc.php';
$validate = new Validation();
$id = $_POST['id'];
$admin_ids = isset($_POST['admin_ids'])?$_POST['admin_ids']:array();
$levels = isset($_POST['level_name'])?$_POST['level_name']:array();
if(!empty($admin_ids)) {
    foreach($admin_ids as $key => $admin_id){
        if(empty($levels[$key])) {
            $validate->setError('level_name_'.$admin_id,'Please Select Access Level');
        } else {
            $validate->string(array('required' => true, 'field' => 'level_name_'.$admin_id, 'value' => $levels[$key]), array('required' => 'Please Select Access Level'));    
        }
        
    }
}

if ($validate->isValid()) {
    $access_level_row = $pdo->selectOne('SELECT id,feature_access FROM access_level WHERE md5(id)=:id ',array(":id"=>$id));
    $str = '';
    $description = array();
    $description['ac_message'] =array(
    'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
    ),

    'ac_message_1' =>' update access level : <br>'
    );

    foreach($admin_ids as $key => $admin_id){
        $admin_row = $pdo->selectOne('SELECT * FROM admin WHERE id=:id',array(":id"=>$admin_id));
        if(!empty($admin_row)) {
            $params = array(
                'feature_access' => $access_level_row['feature_access'],
                'type' => $levels[$key],
            );
            $upd_where = array(
                'clause'=>"id=:id",
                'params' => array(":id" => $admin_id)
            );
            $pdo->update("admin",$params,$upd_where);
            $str .= $admin_row['fname'].' '.$admin_row['lname'] .' ('.$admin_row['display_id'].") from ".$admin_row['type']." to ".$levels[$key]."<br>";
        }
    }

    if(!empty($str)) {
        $description['description'] = $str;    
        activity_feed(3,$_SESSION['admin']['id'],'Admin',$_SESSION['admin']['id'],'Admin','Admin Access Level Updated','','',json_encode($description));
    }
    setNotifySuccess('Admin level updated successfully!');
    $response['status'] = 'success';
} else {
    $errors = $validate->getErrors();
    $response['status'] = 'error';
    $response['errors'] = $errors;
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>