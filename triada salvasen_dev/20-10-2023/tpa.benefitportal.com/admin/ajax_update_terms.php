<?php  
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();

$res = array();
$terms = $_POST['terms'];
$term_id = $_POST['id'];
$type = $_POST['type'];
$sql_t = "SELECT id,type,terms FROM terms WHERE type=:type AND md5(id)=:id AND status='Active' ";
$res_t = $pdo->selectOne($sql_t,array(":type"=>$type,":id"=>$term_id)); 

if (!empty($res_t) && !empty($terms)) {

    $update_params = array(
      'terms' => $terms,
    );
    $update_where = array(
        'clause' => 'md5(id) = :id and type = :type',
        'params' => array(
            ':id' => $term_id,
            ':type' => $type
        )
    );

    $pdo->update('terms', $update_params, $update_where);
    
    if($type == 'Member'){
        $documnet_term['terms'] = $terms;
        $documnet_term['is_default'] = 'Y';
        $document_term_id = $pdo->insert('member_terms',$documnet_term);

        $update_term_where = array(
            'clause' => 'id != :id',
            'params' => array(
                ':id' => $document_term_id
            )
        );
        $update_term_params = array(
            'is_default' => 'N',
          );
        $pdo->update('member_terms', $update_term_params,$update_term_where);
    }

    $oldValArray = $res_t;
    $NewValArray = $update_params;

    unset($oldValArray['id']);
    unset($oldValArray['type']);

    $activity = array_diff_assoc($oldValArray, $NewValArray);
    
    if(!empty($activity)){
        $tmp = array();
        $tmp2 = array();
        if(array_key_exists('terms',$activity)){
            $tmp['display_desc'] = base64_encode($res_t['terms']);
            $tmp2['display_desc'] = base64_encode($update_params['terms']);
        }

        if($type == 'Admin'){
            $link = $ADMIN_HOST.'/add_access_level.php';
            $functionsList->generalActivityFeed($tmp,$tmp2,$link,'Agreement',$res_t['id'],'terms','Admin Updated Admin Agreement','Updated Admin');
        }else if($type == 'Agent'){
            $link = $ADMIN_HOST.'/manage_agents.php';
            $functionsList->generalActivityFeed($tmp,$tmp2,$link,'Agreement',$res_t['id'],'terms','Admin Updated Agent Agreement','Updated Agent');
        }else if($type == 'Group'){
            $link = $ADMIN_HOST.'/manage_groups.php';
            $functionsList->generalActivityFeed($tmp,$tmp2,$link,'Agreement',$res_t['id'],'terms','Admin Updated Group Agreement','Updated Group');
        }else if($type == 'Member'){
            $link = $ADMIN_HOST.'/manage_members.php';
            $functionsList->generalActivityFeed($tmp,$tmp2,$link,'Agreement',$res_t['id'],'terms','Admin Updated Member Agreement','Updated Member');
        }
    }
    
    $res['status'] = 'success';
    $res['msg'] = $type.' Agreement Updated Successfully.';
}else if(!empty($terms)){
    $params = array(
        'terms'=> $terms,
        'type'=> $type,
        'status'=>'Active',
        'created_at'=>'msqlfunc_NOW()'
    );
    $pdo->insert('terms',$params);

    $extra['display_id'] =  $_SESSION['admin']['display_id'];
    $description['description'] =  $_SESSION['admin']['display_id'].' insert '. $type .' terms and conditions';
    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $_SESSION['admin']['id'], 'insert_terms_and_condition','Terms And Condition Insert', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));

    $res['status'] = 'success';
    $res['msg'] = 'Terms and condition insert sucessfully.';
}else{
    $res['status'] = 'fail';
    $res['msg'] = 'Terms and condition not updated.'; 
}

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>