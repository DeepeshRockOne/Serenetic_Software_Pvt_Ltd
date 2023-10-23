<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$access_id = 0;

if(isset($_GET['id'])){
    $access_id=$_GET['id'];
    $breadcrumbes[3]['title'] = 'Edit Feature Access';
}else{
    $breadcrumbes[3]['title'] = 'Add Feature Access';
}
$parentAccessSql="SELECT * FROM agent_feature_access";
$parentAcceesRes=$pdo->select($parentAccessSql);
$features_arr = array();
if(isset($access_id)){
    $features_arr = get_agent_feature_access_options();
}

$acl_name = '';
if (isset($access_id)) {
    $sql_acl = "SELECT id,level as name,feature_access FROM agent_coded_level where md5(id)=:id and feature_access !='' ORDER BY id desc"; 
    $selected = $pdo->selectOne($sql_acl,array(":id"=>$access_id));
    if (!empty($selected)) {
        $selected_acl = explode(',', $selected['feature_access']);
        $acl_name = $selected['name'];
    }
}
$acl = [];
$acl_names = [];
$acl_features = [];
$sql_acl = "SELECT id,level as name,feature_access FROM agent_coded_level where md5(id)=:id and feature_access !='' ORDER BY id desc";
$acls = $pdo->select($sql_acl,array(":id"=>$access_id));

foreach($acls as $acll){
    $acl_names[] = $acll['name'];
    $acl[$acll['id']] = $acll['name'];
    $acl_features[$acll['name']] = explode(',', $acll['feature_access']);
}

$template = 'edit_agents_level.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
