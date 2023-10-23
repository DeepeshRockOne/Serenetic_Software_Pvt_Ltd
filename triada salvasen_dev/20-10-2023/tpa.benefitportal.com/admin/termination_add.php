<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$is_ajaxed = checkIsset($_GET['is_ajaxed']);
$is_delete = checkIsset($_GET['action']);
if($is_ajaxed && $is_delete=='delete'){
    $reason_id = $_GET['id'];

    $reason_rows = $pdo->selectOne("SELECT id,name from termination_reason where is_deleted='N' and md5(id)=:id",array(":id"=>$reason_id));
    if(!empty($reason_rows['id'])){
        $where = array(
            "clause" => " id = :id ",
            "params" => array(
                ":id" => $reason_rows['id']
            )
        );
        $pdo->update("termination_reason",array("is_deleted"=>'Y'),$where);
        $message = ' Deleted reason : <br> '.$reason_rows['name'];
        $response['msg'] = 'Termination Reason Deleted successfully!';

        $activityFeedDesc['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>$message,
        ); 

        activity_feed(3,$_SESSION['admin']['id'], 'Admin', $_SESSION['admin']['id'], 'Admin', 'Termination Reason',$_SESSION['admin']['name'],"",json_encode($activityFeedDesc));
        $response['status'] = 'success';
    }else{
        $response['msg'] = 'Something went wrong!';
        $response['status'] = 'Fail';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;

}

$is_ajaxed = checkIsset($_POST['is_ajaxed']);
if($is_ajaxed){
    $reason_name = checkIsset($_POST['reason_name']);
    $reason_id = checkIsset($_POST['reason_id']);
    $action = checkIsset($_POST['action']);
    $validate = new Validation();
    $validate->string(array('required' => true, 'field' => 'reason_name', 'value' => $reason_name), array('required' => 'Please Enter Reason.'));

    $response = array();
    if($validate->isValid()){
        $message = '';
        if($action == 'Edit' && !empty($reason_id)){
            $reason_rows = $pdo->selectOne("SELECT id,name from termination_reason where is_deleted='N' and md5(id)=:id",array(":id"=>$reason_id));
            if(!empty($reason_rows['id'])){
                $where = array(
                    "clause" => " id = :id ",
                    "params" => array(
                        ":id" => $reason_rows['id']
                    )
                );
                $pdo->update("termination_reason",array("name"=>makesafe($reason_name)),$where);
                $message = ' updated reason : <br> From : '.$reason_rows['name'].' to '.$reason_name;
            }
            $response['msg'] = 'Termination Reason updated successfully!';
        }else{           
            $pdo->insert("termination_reason",array("name"=>makesafe($reason_name)));
            $activity = 'insert';
            $message = ' Created reason  : <br>'.$reason_name;
            $response['msg'] = 'Termination Reason updated successfully!';
        }

        $activityFeedDesc['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>$message,
        ); 
        
        activity_feed(3,$_SESSION['admin']['id'], 'Admin', $_SESSION['admin']['id'], 'Admin', 'Termination Reason',$_SESSION['admin']['name'],"",json_encode($activityFeedDesc));
        
        $response['status'] = 'success';
    }else{
        $response['status'] = "fail";
	    $response['errors'] = $validate->getErrors();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'Add';
$reason_id = checkIsset($_GET['id']);
$rows = array();
if($action == 'Edit'){
    $rows = $pdo->selectOne("SELECT md5(id) as id,name from termination_reason where is_deleted='N' and md5(id)=:id",array(":id"=>$reason_id));
}

$template = 'termination_add.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
