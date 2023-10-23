<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Emailer Dashboard';
$breadcrumbes[1]['link'] = 'emailer_dashboard.php';
$breadcrumbes[2]['title'] = 'Triggers';
$breadcrumbes[2]['class'] = 'Active';

$validate = new Validation();
$cat_title="";

if(isset($_POST['cat_save'])){  
  $cat_title = $_POST['cat_title'];
  $company_id = $_POST['company_id'];
  $validate->string(array('required' => true, 'field' => 'cat_title', 'value' => $cat_title), array('required' => 'Title is required'));
  
  if($validate->isValid()){
    $cat_params = array(
      'title' => makeSafe($cat_title),
      'company_id' => $company_id,
      'create_date' => 'msqlfunc_NOW()'
    );
    $triger_cat_id = $pdo->insert("trigger_category",$cat_params); 
    
    /* Code for audit log*/
    
    $user_data = get_user_data($_SESSION['admin']);
    audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Category Inserted Id is ".$triger_cat_id, '', $cat_params, 'trigger category inserted by admin');


  /* End Code for audit log*/
    
    setNotifySuccess('Category added successfully.');
    redirect(basename($_SERVER['PHP_SELF']));
  }
}

$SortBy = "tc.id";
$SortDirection = "ASC";
$currSortDirection = "DESC";
$incr = "";
$sch_params = array();

if(isset($_GET['show']) && in_array(strtolower($_GET['show']), array('active','inactive'))) {
  $sch_params[':status'] = makeSafe(ucfirst($_GET['show']));
  $incr .= " AND t.status = :status";
}
if(isset($_GET['cat_id']) && !empty($_GET['cat_id'])) {
  $cat_id = $_GET['cat_id'];
  $sch_params[':cat_id'] = makeSafe($cat_id);
  $incr .= " AND tc.id = :cat_id";
}
$strQuery="SELECT t.id,t.is_deleted, tc.id as category_id, tc.title as category, t.title, t.email_subject, t.sms_content, t.status, t.programming_status, t.create_at, t.type,c.id as company_id,c.company_name
          FROM trigger_category tc
          LEFT JOIN triggers t ON(tc.id = t.category_id AND t.is_visible = 'Y') 
          LEFT JOIN company c ON(c.id = tc.company_id)
          WHERE tc.id > 0 AND (is_deleted='N' OR ISNULL(is_deleted)) $incr
          ORDER BY tc.id ASC,t.id ASC";
$rs = $pdo->select($strQuery,$sch_params);
 
$strTotalPerPage = count($rs);
 //echo $strTotalPerPage; 

$strQuery = "SELECT tc.id, tc.title,c.company_name FROM trigger_category tc LEFT JOIN company c ON(c.id = tc.company_id) ORDER BY id";
$rsTrigger = $pdo->select($strQuery);
 

$strCompanyQuery = "SELECT c.id,c.company_name FROM company c ORDER BY id";
$rsCompany = $pdo->select($strCompanyQuery);

$errors = $validate->getErrors();
$page_title = "Triggers";
$template = "triggers_v1.inc.php"; 	
include_once 'layout/end.inc.php';
?>