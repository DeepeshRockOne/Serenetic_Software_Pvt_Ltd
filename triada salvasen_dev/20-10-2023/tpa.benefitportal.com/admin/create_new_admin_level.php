<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(3);

//features array for Choose Modules
$features_arr = get_admin_feature_access_options();

$selected_acl = array();
$selected_name = '';
$selected_dashboard = '';
$id = 0;
$acl_name = '';
if (isset($_GET['id'])) {
    // $id = (int) makeSafe($_GET['id']);
    $id = $_GET['id'];
    $sql_acl = "SELECT id,name,dashboard,feature_access FROM access_level where md5(id) = :id ORDER BY id"; 
    $selected = $pdo->selectOne($sql_acl,array(":id"=>$id));
    if (!empty($selected)) {
        $selected_acl = explode(',', $selected['feature_access']);
        $selected_name = $selected['name'];
        $selected_dashboard = $selected['dashboard'];
        $acl_name = $selected['name'];
    }
}

$acl = array();
$acl_names = array();
$acl_features = array();
$sql_acl = "SELECT id,name,dashboard,feature_access FROM access_level where feature_access !='' ORDER BY id";
$acls = $pdo->select($sql_acl);
if(!empty($acls)){
    foreach($acls as $acll){
        $acl_names[] = $acll['name'];
        $acl[$acll['id']] = $acll['name'];
        $acl_features[$acll['name']] = explode(',', $acll['feature_access']);
    }
}
$admin_res = [];
if(isset($_GET['action']) && $_GET['action'] == 'delete'){
    $admin_res = $pdo->select("SELECT id,type,display_id,CONCAT(fname,' ',lname) as name from admin where type =:type and is_deleted='N' ",array(":type"=>$selected_name));

    if(empty($admin_res)) {
        $lv_name=$pdo->selectOne("SELECT name,id FROM access_level where md5(id) = :id",array(":id"=>$id));

        $delTrgSql = "DELETE FROM access_level WHERE md5(id)=:id ";
        $params = array(
            ':id' => $id,
        );
        $pdo->delete($delTrgSql, $params);

        $activity_upd_params = array(
            'is_deleted' => 'Y',
            'changed_at' => 'msqlfunc_NOW()'
        );

        $activity_update_where = array(
            'clause' => 'entity_id=:entity_id AND entity_type=:entity_type',
            'params' => array(
                ':entity_id' => makeSafe($id),
                ':entity_type' => 'access_level'
            )
        );
        $extra['user_display_id'] = $_SESSION['admin']['display_id'];
        $extra['access_lvl_name'] = $lv_name['name'];

        $description = array();
        $description['description'] = $_SESSION['admin']['display_id'].' deleted admin access level '.$lv_name['name'];
        activity_feed(3, $_SESSION['admin']['id'],'Admin', $lv_name['id'], 'access_level','Access Level Deleted', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
        setNotifySuccess('Access Level Deleted Successfully!');
        redirect('add_access_level.php',true);
        exit();
    }
}

$template = 'create_new_admin_level.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
