<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$group_id = $_GET['group_id'];

$features_arr = array();
if(isset($group_id)){
    $featureAccessSql="SELECT id, title, IF(parent_id = 0, id, parent_id) as parent_id 
    FROM group_feature_access 
    ORDER BY parent_id, id";
    
    $featureAccessRes=$pdo->select($featureAccessSql);
    if (!empty($featureAccessRes)) {
        foreach ($featureAccessRes as $feature) {
            if (!isset($features_arr[$feature['parent_id']])) {
                $features_arr[$feature['parent_id']] = $feature;
                $features_arr[$feature['parent_id']]['child'] = array();
            } else {
                $features_arr[$feature['parent_id']]['child'][] = $feature;
            }
        }
    }
}

$acl_name = '';
if (isset($group_id)) {
    $sql_acl = "SELECT c.id as id,c.rep_id AS name,c.feature_access FROM customer c 
                LEFT JOIN customer_settings cs ON(cs.customer_id= c.id) 
                WHERE md5(c.id)=:id AND c.feature_access !='' ORDER BY id DESC"; 
    $selected = $pdo->selectOne($sql_acl,array(":id"=>$group_id));
    if (!empty($selected)) {
        $selected_acl = explode(',', $selected['feature_access']);
        $acl_name = $selected['name'];
    }
}
$acl = [];
$acl_names = [];
$acl_features = [];
$sql_acl = "SELECT c.id as id,c.rep_id AS name,c.feature_access FROM customer c 
            LEFT JOIN customer_settings cs ON(cs.customer_id= c.id) 
            WHERE md5(c.id)=:id AND c.feature_access !='' ORDER BY id DESC";
$acls = $pdo->select($sql_acl,array(":id"=>$group_id));

foreach($acls as $acll){
    $acl_names[] = $acll['name'];
    $acl[$acll['id']] = $acll['name'];
    $acl_features[$acll['name']] = explode(',', $acll['feature_access']);
}
$automated_communication = array();
if (!empty($group_id)) {
    $selGroupSettings = "SELECT id,automated_communication FROM customer_group_settings WHERE md5(customer_id)=:group_id";
    $resGroupSettings = $pdo->selectOne($selGroupSettings,array(":group_id" =>$group_id ));
    if(!empty($resGroupSettings["automated_communication"])){
        $automated_communication = explode(",",$resGroupSettings["automated_communication"]);
    }
}

$template = 'groups_access_edit.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
