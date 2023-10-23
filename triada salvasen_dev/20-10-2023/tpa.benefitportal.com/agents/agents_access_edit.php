<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$agent_id = $_GET['agent_id'];

$features_arr = array();
if(isset($agent_id)){
    $features_arr = get_agent_feature_access_options();
}

$acl_name = '';
if (isset($agent_id)) {
    $sql_acl = "SELECT acl.id as id,acl.level AS name,c.feature_access,cs.agent_coded_level FROM customer c 
                LEFT JOIN customer_settings cs ON(cs.customer_id= c.id) 
                LEFT JOIN agent_coded_level acl ON(acl.id = cs.agent_coded_id)
                WHERE md5(c.id)=:id AND c.feature_access !='' ORDER BY id DESC"; 
    $selected = $pdo->selectOne($sql_acl,array(":id"=>$agent_id));
    // pre_print($selected);
    if (!empty($selected)) {
        $selected_acl = explode(',', $selected['feature_access']);
        $acl_name = $selected['name'];
    }
}
$acl = [];
$acl_names = [];
$acl_features = [];
$sql_acl = "SELECT acl.id as id,acl.level AS name,c.feature_access,cs.agent_coded_level FROM customer c 
            LEFT JOIN customer_settings cs ON(cs.customer_id= c.id) 
            LEFT JOIN agent_coded_level acl ON(acl.id = cs.agent_coded_id)
            WHERE md5(c.id)=:id AND c.feature_access !='' ORDER BY id DESC";
$acls = $pdo->select($sql_acl,array(":id"=>$agent_id));

foreach($acls as $acll){
    $acl_names[] = $acll['name'];
    $acl[$acll['id']] = $acll['name'];
    $acl_features[$acll['name']] = explode(',', $acll['feature_access']);
}

$template = 'agents_access_edit.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
