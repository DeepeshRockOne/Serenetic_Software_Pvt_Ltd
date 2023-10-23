<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$response = array();

$validate = new Validation();

$circle_name = checkIsset($_POST['circle_name']);
$invite_admins = checkIsset($_POST['invite_admins'],'arr');
$status = checkIsset($_POST['status']);
$circle_id = checkIsset($_POST['circle_id']);

$validate->string(array('required' => true, 'field' => 'circle_name', 'value' => $circle_name), array('required' => 'Circle Name is required'));

if(empty($invite_admins)){
    $validate->setError('invite_admins','Please select Admin.');
}else if(!empty($invite_admins) && count($invite_admins) < 2){
    $validate->setError('invite_admins','Please select at least 2 Admins.');
}

$validate->string(array('required' => true, 'field' => 'status', 'value' => $status), array('required' => 'Status is required'));

if($validate->isValid()){

    include_once dirname(__FILE__) .'/adminCircle.class.php';
    $adminCircle = new adminCircle();

    $crclArr = $pdo->selectOne("SELECT * from admin_circle where is_deleted='N' AND md5(id)=:circle_id",array(":circle_id"=>$circle_id));

    $insertArr = array(
        "name" => makeSafe($circle_name),
        "status" => makeSafe($status),
        "created_by_admin_id"=>$_SESSION['admin']['id'],
        "invite_admins" => $invite_admins,        
    );

    if(!empty($crclArr['id'])){

        $adminCircle->updateAdminCircle($crclArr['id'],$insertArr);
        $response['message'] = 'Circle updated Successfully.';
    }else{

        $adminCircle->insertAdminCircle($insertArr);
        $response['message'] = 'Circle created Successfully.';
    }

    $response['status'] = 'success';
    
}else{
    $response['status'] = 'error';
    $response['errors'] = $validate->getErrors();
}

header("content-type: application/json");
echo json_encode($response);
dbConnectionClose();
exit;
?>