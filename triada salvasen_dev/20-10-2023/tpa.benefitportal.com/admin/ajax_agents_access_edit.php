<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$access_name = checkIsset($_POST['access_name']);
$id = $_POST['id'];
$displayDirectEnroll = !empty($_POST['displayDirectEnroll']) ? implode(",", $_POST['displayDirectEnroll']) : '';
$additionalAccess = !empty($_POST['additionalAccess']) ? implode(",", $_POST['additionalAccess']) : '';

$features = array_unique(checkIsset($_POST['feature'],'arr'));
if(!empty($features)){
    foreach ($features as $a => $b) {
        if ($b == 'undefined') {
            unset($features[$a]);
        }
    }
}
$validate->string(array('required' => true, 'field' => 'access_name', 'value' => $access_name), array('required' => 'Access Name is required'));

if(empty($features)){
    $validate->setError("features","Please Select Any One Option");
}

if($validate->isValid())
{
    if (count($features) > 0) {
        $features = implode(',', array_unique($features));
    } else {
        $features = "";
    }
    $updateSql = array('feature_access' => makeSafe($features));
    $where = array("clause" => 'md5(id)=:id', 'params' => array(':id' => $id));
    $access_update = $pdo->update('customer', $updateSql, $where,true);


    $updateSql = array('displayDirectEnroll' => $displayDirectEnroll,'additionalAccess' => $additionalAccess);
    $where = array("clause" => 'md5(customer_id)=:id', 'params' => array(':id' => $id));
    $access_update = $pdo->update('customer_settings', $updateSql, $where,true);

    
 
    if(!empty($access_update['feature_access']) || !empty($features)){
        $unselected = $selected = $new_fe = $old_fe = array();
        $old_fe = explode(',',$features);
        if(!empty($access_update['feature_access'])){
            $new_fe = explode(',',$access_update['feature_access']);
        }
        $unselected = array_diff($new_fe,$old_fe);
        $selected = array_diff($old_fe,$new_fe);
        $old_features=$New_features='';
        if(!empty($unselected)){
            $old_features = $pdo->selectOne("SELECT GROUP_CONCAT(title) as unselected from agent_feature_access where ID IN(".implode(',',$unselected) .")");
        }
        if(!empty($selected)){
            $New_features = $pdo->selectOne("SELECT GROUP_CONCAT(title) as selected from agent_feature_access where ID IN(".implode(',',$selected)." ) " );
        }
        if(!empty($old_features) || !empty($New_features)){
            $agent_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) as name ,rep_id from customer where md5(id)=:id",array(":id"=>$id));
            $description = array();
            $description['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>'  Updated Feature Access In Agent '.$agent_name['name'].' (',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_name['id']),
                    'title'=> $agent_name['rep_id'],
                ),
                'ac_message_2' =>')<br>',
                );
            if(!empty($New_features)){
                $description['description'] = array("Feature Selected : ".$New_features['selected']);
            }
            if(!empty($old_features)){
                $description['description1'] = array("Feature Unselected : ".$old_features['unselected']);
            }
            $desc=json_encode($description);
            activity_feed(3,$agent_name['id'], 'Agent' , $agent_name['id'], 'Agent Feature Updated', 'Agent Feature Updated',$_SESSION['admin']['name'],"",$desc);
        }
    }


    $response['status'] = 'success';
    setNotifySuccess('Access Level Updated');

}else{
    $errors = $validate->getErrors();
    $response['status'] = 'error';
    $response['errors'] = $errors;
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>