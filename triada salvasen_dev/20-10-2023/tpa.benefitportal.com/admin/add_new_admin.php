<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(3);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Admins";
$breadcrumbes[1]['link'] = "admins.php";
$breadcrumbes[2]['title'] = "Add New Admin";
$breadcrumbes[2]['class'] = "Active";
$page_title = "Add New Admin"; 
 
$from_email = get_app_settings('default_email_from'); 
 
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $user_id = $_GET['id'];
    $selectUser = "SELECT id,type FROM admin WHERE id = :id";
    $where_select_user = array(':id' => makeSafe($user_id));
    $user = $pdo->selectOne($selectUser, $where_select_user);
    $access_level = $user['type'];
}
$access_level = checkIsset($access_level);

$acl_names = array();
$acl_features = array();
$sql_acl = "SELECT id,name,dashboard,feature_access 
            FROM access_level where feature_access !='' 
            ORDER BY name"; 
$acls = $pdo->select($sql_acl);

if(!empty($acls)){
    foreach($acls as $acll){
        $acl_names[] = $acll['name'];
        $acl[$acll['id']] = $acll['name'];
        $acl_features[$acll['name']] = explode(',', $acll['feature_access']);
    }
}

$features_arr = get_admin_feature_access_options();

$trigger_id = 1;
$trigger = $pdo->selectOne("SELECT id,email_subject,email_content,sms_content from triggers where id=".$trigger_id);

$company_sql = "SELECT * FROM company WHERE id = 3";
$company_res = $pdo->select($company_sql);
 
$summernote = "Y";
 
$exJs = array(
    'thirdparty/clipboard/clipboard.min.js',  
    'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
    'thirdparty/ckeditor/ckeditor.js'
);
 
$page_title = "Add New Admin";
$template = "add_new_admin.inc.php";
include_once 'layout/end.inc.php';
