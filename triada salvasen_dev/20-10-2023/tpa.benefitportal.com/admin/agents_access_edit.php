<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$agent_id = $_GET['agent_id'];

$features_arr = get_agent_feature_access_options();

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

$sqlDirectEnroll = "SELECT displayDirectEnroll,additionalAccess FROM customer_settings where md5(customer_id) = :agent_id";
$resDirectEnroll = $pdo->selectOne($sqlDirectEnroll,array(":agent_id"=>$agent_id));

$displayDirectEnrollArr = !empty($resDirectEnroll) && !empty($resDirectEnroll['displayDirectEnroll']) ? explode(",",$resDirectEnroll['displayDirectEnroll']) : array();
$additionalAccessArr = !empty($resDirectEnroll) && !empty($resDirectEnroll['additionalAccess']) ? explode(",",$resDirectEnroll['additionalAccess']) : array();

$displayDirectEnrollList = array("Agents","Leads","Members","Groups");
$additionalAccessList = array(
    "benefit_tier"=> array("descriptions" => "Plan Tier: User has ability to update plan tier of plan following granted access."),
    "policy_change" => array("descriptions" =>"Plan Change: User has ability to upgrade/downgrade plan following granted access."),
    "effective_date" => array("descriptions" =>"Effective Date: User has ability to change effective date of plan following granted access."),
    "termination_date" => array("descriptions" =>"Termination Date: User has ability to set and change termination date of plan following granted access."),
    "reversals_orders" => array("descriptions" =>"Reversals Orders: User has ability to perform reversals on orders following granted access.")
);
$template = 'agents_access_edit.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
