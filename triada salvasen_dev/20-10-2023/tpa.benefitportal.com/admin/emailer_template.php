<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Communications';
$breadcrumbes[2]['title'] = 'Email';
$breadcrumbes[2]['link'] = 'emailer_dashboard.php';
$breadcrumbes[3]['title'] = 'Templates';
$breadcrumbes[3]['link'] = 'emailer_template.php';


$sch_params = array();
$incr = '';
$SortBy = "t.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$has_querystring = false;
if (!empty($_GET["sort_by"])) {
  $has_querystring = true;
  $SortBy = $_GET["sort_by"];
}

if (!empty($_GET["sort_direction"])) {
  $has_querystring = true;
  $currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = !empty($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';


/* ------- Delete trigger code start --------- */
if(isset($_GET['action']) && ($_GET['action'] == 'delTemplate')){
  $templateId = $_GET['id'];
  
  if (!empty($templateId)){
    $templateSql = "SELECT id,display_id FROM trigger_template WHERE md5(id)=:id AND is_deleted='N'";
    $templateParams = array(":id" => $templateId);
    $templateRes = $pdo->selectOne($templateSql,$templateParams);
  
    if(!empty($templateRes['id'])){
      $updateSql = array('is_deleted' => "Y");
      $where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($templateRes['id'])));
      $pdo->update("trigger_template", $updateSql, $where);

      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Deleted template '.$templateRes['display_id'],
      ); 

      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $templateRes['id'], 'trigger_template','Deleted Template', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    }
    setNotifySuccess("Template deleted successfully");
    redirect("emailer_template.php");
  }
}
/* ------- Delete trigger code ends --------- */


if (count($sch_params) > 0) {
  $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {

  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (!empty($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';


$options = array(
  'results_per_page' => $per_page,
  'url' => 'emailer_template.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params,
);

$page = (!empty($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
  
    $sel_sql = "SELECT md5(t.id) as id,t.display_id,t.created_at,t.title,c.company_name,t.content,t.is_default,COUNT(DISTINCT(tg.id)) as triggerCnt
                FROM trigger_template t
                LEFT JOIN prd_company c ON(t.company_id=c.id)
                LEFT JOIN triggers tg ON(t.id=tg.template_id AND tg.is_deleted='N')
                WHERE t.is_deleted = 'N'" . $incr . " GROUP BY t.id ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $sel_sql, $options);
      if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }

  include_once 'tmpl/emailer_template.inc.php';
  exit;
}


$page_title = "Email";
$template = 'emailer_template.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
