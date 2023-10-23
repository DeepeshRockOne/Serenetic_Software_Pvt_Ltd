<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();

$validate = new Validation();

$id = $_POST['id'];
$access_name = checkIsset($_POST['access_name']);
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

if($validate->isValid()){   

    $features = !empty($features) ? implode(',', array_unique($features)) : "";

    $agentLevelSql = "SELECT id,feature_access,level_heading FROM agent_coded_level WHERE md5(id)=:id";
    $resAgentLevel = $pdo->selectOne($agentLevelSql,array(":id" => $id));


    if(!empty($resAgentLevel['id'])){

        $updParams = array(
                        'feature_access' => makeSafe($features),
                        'level_heading' => makeSafe($access_name)
                    );
        
        $updWhere = array("clause" => 'md5(id)=:id', 'params' => array(':id' => makeSafe($id)));
        $pdo->update('agent_coded_level', $updParams, $updWhere);

        $oldValArr = $resAgentLevel;
        unset($oldValArr['id']);
        $newValArr = $updParams;

        $activity = array_diff_assoc($oldValArr, $newValArr);

        if(!empty($activity)){
            $tmp = array();
            $tmp2 = array();

            if(array_key_exists('level_heading',$activity)){
                $tmp['Level Name'] = $oldValArr['level_heading'];
                $tmp2['Level Name'] = $newValArr['level_heading'];
            }

            if(array_key_exists('feature_access',$activity)){
                if(!empty($oldValArr['feature_access'])){
                    $resOldFetures = $pdo->selectOne("SELECT GROUP_CONCAT(title) as featureAccess FROM agent_feature_access WHERE id IN(".$oldValArr['feature_access'].")");
                }else{
                    $resOldFetures['featureAccess'] = 'Blank';
                }
                $resNewFetures = $pdo->selectOne("SELECT GROUP_CONCAT(title) as featureAccess FROM agent_feature_access WHERE id IN(".$newValArr['feature_access'].")");

                $tmp['Feature Access'] = $resOldFetures['featureAccess'];
                $tmp2['Feature Access'] = $resNewFetures['featureAccess'];
            }

            $link = $ADMIN_HOST.'/manage_agents.php';
            $functionsList->generalActivityFeed($tmp,$tmp2,$link,'Agent Level',$resAgentLevel['id'],'agent_coded_level','Admin Updated Agent Level','Updated');

        }

        $response['status'] = 'success';
        setNotifySuccess('Access Level Updated');
    }else{
        $response['status'] = 'error';
    }

    if(isset($_POST["update"])){
        $update_where = array(
                'clause' => 'id=:id',
                'params' => array(':id' => 1),
            );
        $update_params=array("version"=>"msqlfunc_version+0.01");
        $pdo->update("cache_management",$update_params,$update_where);
    
        unlink($CACHE_PATH_DIR.$CACHE_FILE_NAME);
    }
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