<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Communications';
$breadcrumbes[2]['title'] = 'Email';
$breadcrumbes[2]['link'] = 'emailer_dashboard.php';
$breadcrumbes[3]['title'] = 'Templates';
$breadcrumbes[3]['link'] = 'emailer_template.php';
$breadcrumbes[4]['title'] = '+ Templates';
$breadcrumbes[4]['link'] = 'add_emailer_template.php';
$summernote = "Y";

$templateRes = array();
$templateId = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if(!empty($templateId)){
    $templateSql = "SELECT * FROM trigger_template WHERE md5(id)=:id AND is_deleted='N'";
    $templateParams = array(":id" => $templateId);
    $templateRes = $pdo->selectOne($templateSql,$templateParams);
    
    // Read trigger code start
    if($templateRes){
      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Read template ',
        'ac_red_2'=>array(
            'href'=>$ADMIN_HOST.'/add_emailer_template.php?id='. $templateId,
            'title'=>$templateRes['display_id'],
        ),
      ); 
      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $templateRes['id'], 'trigger_template','Read Template', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    }
}





$title = (!empty($templateRes['title']) && $action != 'Clone') ? $templateRes['title'] : '';
$companyId = checkIsset($templateRes['company_id']);
$content = checkIsset($templateRes['content']);

// get company code start
$companyRes = $pdo->select("SELECT id,company_name FROM prd_company WHERE is_deleted = 'N'");



$exJs = array('thirdparty/ckeditor/ckeditor.js');

$page_title = "Email Templates";
$template = 'add_emailer_template.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
