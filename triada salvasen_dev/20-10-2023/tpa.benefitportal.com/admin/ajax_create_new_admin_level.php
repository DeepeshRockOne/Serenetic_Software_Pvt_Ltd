<?php
include_once 'layout/start.inc.php';

$validate = new Validation();
$response = array();

$action = checkIsset($_POST['action']);
$id = checkIsset($_POST['id']);
if($action!='delete'){
    $access_name = checkIsset($_POST['access_name']);
    $dashboard = checkIsset($_POST['dashboard']);
    $features = array_unique(checkIsset($_POST['feature'],'arr'));
    if(!empty($features)){
        foreach ($features as $a => $b) {
            if ($b == 'undefined') {
                unset($features[$a]);
            }
        }
    }
    $validate->string(array('required' => true, 'field' => 'access_name', 'value' => $access_name), array('required' => 'Access Name is required'));
    if(count($dashboard) > 1){
        $validate->string(array('required' => true, 'field' => 'dashboard', 'value' => $dashboard), array('required' => 'Please Select Any One Option'));
    }
    if(empty($features)){
        $validate->setError("features","Please Select Any One Option");
    }
}
if ($validate->isValid()) {
    if($action !='delete'){
        $params = array(
            'name' => makeSafe(trim($access_name)),
            'dashboard' => $dashboard[0],
            'feature_access' => implode(',', $features),
        );
    }
    
    if (!empty($id) && $id!='') {
        $where = array("clause" => "md5(id)=:id", "params" => array(":id" => $id));
        if (isset($action) && $action == 'edit') {
            $params['updated_at'] = 'msqlfunc_NOW()';

            $old_feature = $pdo->selectOne('SELECT feature_access,name FROM access_level where md5(id)=:id',array(":id"=>$id));
            $extra['old_type'] = $old_feature['feature_access'];
            $extra['new_type'] = $params['feature_access'];
            $data['access_lvl_name'] = $old_feature['name'].' from ';

            $feature = array_diff(explode(',',$extra['new_type']),explode(',',$extra['old_type']));
            
            $data['from'] = " blank to checked.";

            $features_id = implode("','",$feature);
            
            if(!empty($features_id)){
            $feature_name=$pdo->selectOne("SELECT GROUP_CONCAT(title SEPARATOR ', ') as features from feature_access where id IN('$features_id')");
            $data['features'] = (isset($feature_name['features'])?$feature_name['features']:'');

            $ro_feature_name = $pdo->selectOne("SELECT GROUP_CONCAT(' Read Only ',title) as features from feature_access where CONCAT('ro_',id) IN('$features_id')");
            if(!empty($ro_feature_name['features'])) {
                if(!empty($data['features'])) {
                    $data['features'] .=",";
                }
                $data['features'] .= $ro_feature_name['features'];
            }
            $data['user_display_id'] = $_SESSION['admin']['display_id'];
            $description['description'] = $_SESSION['admin']['display_id'].' updated '.$data['access_lvl_name'].' '.$data['features'].' '.$data['from'];
            activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $_SESSION['admin']['id'], 'update_old_to_new_feature','Admin Feature Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($data));
            }
            
            if(count($feature) == 0 || count($feature) > 0 ){
                $feature_blank = array_diff(explode(',',$extra['old_type']),explode(',',$extra['new_type']));
                $data['from'] = " checked to blank.";
                $data['features'] = "";
                $features_id = implode("','",$feature_blank);
                if(!empty($features_id)){
                    $feature_name=$pdo->selectOne("SELECT GROUP_CONCAT(title SEPARATOR ', ') as features from feature_access where id IN('$features_id')");
                    if(!empty($feature_name['features'])) {
                        $data['features'] = $feature_name['features'].' from ';
                    }

                    $ro_feature_name = $pdo->selectOne("SELECT GROUP_CONCAT(' Read Only ',title) as features from feature_access where CONCAT('ro_',id) IN('$features_id')");
                    if(!empty($ro_feature_name['features'])) {
                        if(!empty($data['features'])) {
                            $data['features'] .=",";
                        }
                        $data['features'] .= $ro_feature_name['features'].' from ';
                    }
                }
                if(count($feature_blank) > 0){
                    $data['user_display_id'] = $_SESSION['admin']['display_id'];
                    $description['description'] = $_SESSION['admin']['display_id'].' updated '.$data['access_lvl_name'].' '.$data['features'].' '.$data['from'];
                    activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $_SESSION['admin']['id'], 'update_old_to_new_feature','Admin Feature Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($data));
                }
            }

            $pdo->update("access_level", $params, $where);            
            
            $message = 'Access Level Updated';
        } elseif (isset($action) && $action == 'delete') {
            $lv_name=$pdo->selectOne("SELECT name,id FROM access_level where md5(id) = :id",array(":id"=>$id));
            $delTrgSql = "DELETE FROM access_level WHERE md5(id) = :id ";
            $params = array(
                ':id' => $id,
            );
            $pdo->delete($delTrgSql, $params);

            $activity_upd_params = array(
                'is_deleted' => 'Y',
                'changed_at' => 'msqlfunc_NOW()'
            );

            $activity_update_where = array(
                'clause' => 'entity_id = :entity_id AND entity_type = :entity_type',
                'params' => array(
                    ':entity_id' => makeSafe($id),
                    ':entity_type' => 'access_level'
                )
            );
            $extra['user_display_id'] = $_SESSION['admin']['display_id'];
            $extra['access_lvl_name'] = $lv_name['name'];
            $description['description'] = $_SESSION['admin']['display_id'].' deleted admin access level '.$lv_name['name'];
            activity_feed(3, $_SESSION['admin']['id'],'Admin', $lv_name['id'], 'access_level','Access Level Deleted', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
            $message = 'Access Level Deleted';
            
        }
    } else {
        $params['created_at'] = 'msqlfunc_NOW()';
        $inserted_id = $pdo->insert("access_level", $params);

        $extra['user_display_id'] = $_SESSION['admin']['display_id'];
        $extra['access_lvl_name'] = $params['name'];

        $description['description'] = $_SESSION['admin']['display_id'].' created admin access level '.$params['name'];
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $inserted_id, 'access_level','Access Level Created', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
        $message= 'New Access Level Added';
    }
    setNotifySuccess($message);
    $response['status'] = 'success';

}else {
    $errors = $validate->getErrors();
    $response['status'] = 'error';
    $response['errors'] = $errors;
}
  
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>