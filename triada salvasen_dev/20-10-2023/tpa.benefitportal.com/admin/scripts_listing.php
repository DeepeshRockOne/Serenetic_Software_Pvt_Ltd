<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

  $sch_params = array();
  $has_querystring = false;
  
  $is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';

  $per_page=10;
  if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
  }
  $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

  $options = array(
      'results_per_page' => $per_page,
      'url' => 'scripts_listing.php?is_ajaxed=1&' . $query_string,
      'db_handle' => $pdo->dbh,
      'named_params' => $sch_params
  );

  $page = isset($_GET["page"]) && $_GET['page'] > 0 ? $_GET['page'] : 1;
  $options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT md5(ss.id) as id,ss.script_type,
              if(ss.last_processed is NOT NULL,ss.last_processed,'') as last_processed,
              if(ss.next_processed is NOT NULL,ss.next_processed,'') as next_processed,
              ss.status
             FROM system_scripts ss
             WHERE ss.is_deleted='N'
             ORDER BY ss.id ASC";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/scripts_listing.inc.php';
  exit;
}
?>