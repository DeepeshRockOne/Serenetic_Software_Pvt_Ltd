<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Communications';
$breadcrumbes[2]['title'] = 'Email';
$breadcrumbes[2]['class'] = 'Active';


$sch_params = array();
$incr = '';
$SortBy = "e.id";
$SortDirection = "DESC";
$currSortDirection = "ASC";


$email = checkIsset($_GET['email']);
$join_range = checkIsset($_GET['join_range']);
$fromdate = checkIsset($_GET["fromdate"]);
$todate = checkIsset($_GET["todate"]);
$status = checkIsset($_GET["status"]);
$today = date("m/d/Y");
$added_date = !empty($_GET["added_date"]) ? $_GET["added_date"] : $today;
$viewEmail = !empty($_GET["viewEmail"]) ? $_GET["viewEmail"] : 'todayEmail';

if($viewEmail == "todayEmail" && empty($join_range)){
  $join_range = "Exactly";
}

$email = cleanSearchKeyword($email); 
 
if(!empty($email)){
  $sch_params[':email'] = "%" . makeSafe($email) . "%";
  $incr .= " AND (e.from_email LIKE :email OR e.to_email LIKE :email)";
}

if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(e.created_at) >= :fromdate AND DATE(e.created_at) <= :todate";
  }else if($join_range == "Exactly" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(e.created_at) = :added_date";
  }else if($join_range == "Before" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(e.created_at) < :added_date";
  }else if($join_range == "After" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(e.created_at) > :added_date";
  }
}


if ($status != "") {
  $sch_params[':status'] = makeSafe($status);
  $incr .= " AND e.status = :status";
}


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
  'url' => 'emailer_dashboard.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params,
);

$page = (!empty($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
if ($is_ajaxed) {
  try {
  
    $sel_sql = "SELECT md5(e.id) as id,e.created_at,e.from_email,e.to_email,e.status,eld.status as details_status
                FROM email_log e
                LEFT JOIN email_log_details eld ON eld.id = (SELECT id FROM email_log_details WHERE log_id = e.id ORDER BY id DESC LIMIT 1)
                WHERE e.id > 0" . $incr . " GROUP BY e.id ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $sel_sql, $options);
      if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/emailer_dashboard.inc.php';
  exit;
}


$page_title = "Email";
$template = 'emailer_dashboard.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
