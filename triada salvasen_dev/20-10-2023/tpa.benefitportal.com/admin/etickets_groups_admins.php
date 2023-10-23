<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$category_id = $_GET['id'];
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
if($is_ajaxed){
    $sch_params = array();
    $has_querystring = false;
    $sch_params[':id'] = $category_id;
    $incr = " AND md5(s_ticket_group_id)=:id ";
    $per_page = 250 ;
    if (isset($_GET['pages']) && $_GET['pages'] > 0) {
      $has_querystring = true;
      $per_page = $_GET['pages'];
    }
    $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
    
    $options = array(
        'results_per_page' => $per_page,
        'url' => 'etickets_groups_admins.php?' . $query_string,
        'db_handle' => $pdo->dbh,
        'named_params' => $sch_params
    );
    
    $page = isset($_GET["page"]) && $_GET['page'] > 0 ? $_GET['page'] : 1;
    $options = array_merge($pageinate_html, $options);
    
    try {
      $sel_sql = "SELECT fname,lname,display_id,status from s_ticket_assign_admin sa JOIN admin a ON(a.id=sa.admin_id and a.is_deleted='N') where sa.is_deleted='N' $incr ";
      $paginate = new pagination($page, $sel_sql, $options);
      if ($paginate->success == true) {
        $fetch_rows = $paginate->resultset->fetchAll();
        $total_rows = count($fetch_rows);
      }
    } catch (paginationException $e) {
      echo $e;
      exit();
    }
    include_once 'tmpl/etickets_groups_admins.inc.php';
    exit;
}else{
    $layout = 'iframe.layout.php';
    $template = 'etickets_groups_admins.inc.php';
    include_once 'layout/end.inc.php';
}
?>