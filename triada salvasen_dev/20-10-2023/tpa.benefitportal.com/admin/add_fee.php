<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(3);

$selected_acl = [];
$selected_name = '';
$selected_dashboard = '';
//print_r($_GET['id']);
$id = 0;
$acl_name = '';
if (isset($_GET['id'])) {
    $id = (int) makeSafe($_GET['id']);
    
    $sql_acl = "SELECT id,name,dashboard,feature_access FROM access_level where id = $id ORDER BY id"; //TODO sql injection
    $selected = $pdo->select($sql_acl);
    if (!empty($selected)) {
        $selected_acl = explode(',', $selected[0]['feature_access']);
        $selected_name = $selected[0]['name'];
        $selected_dashboard = $selected[0]['dashboard'];
        $acl_name = $selected[0]['name'];
    }
}

$acl = [];
$acl_names = [];
$acl_features = [];
$sql_acl = "SELECT id,name,dashboard,feature_access FROM access_level where feature_access !='' ORDER BY id"; //TODO sql injection
$acls = $pdo->select($sql_acl);
foreach($acls as $acll){
    $acl_names[] = $acll['name'];
    $acl[$acll['id']] = $acll['name'];
    $acl_features[$acll['name']] = explode(',', $acll['feature_access']);
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js');

$template = 'add_fee.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
