<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

  $sch_params = array();
  $has_querystring = false;
  $qincr = '';
  $is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
  $name = isset($_GET['name']) ? $_GET['name'] : '';

  $name = cleanSearchKeyword($name); 
   
  if(!empty($name)){
    $sch_params[':title'] = "%" . makeSafe($name) . "%";
    $qincr .= " AND title like :title ";
  }

  $per_page=10;
  if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
  }
  $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

  $options = array(
      'results_per_page' => $per_page,
      'url' => 'get_eticket_quick_reply.php?is_ajaxed=1&' . $query_string,
      'db_handle' => $pdo->dbh,
      'named_params' => $sch_params
  );

  $page = isset($_GET["page"]) && $_GET['page'] > 0 ? $_GET['page'] : 1;
  $options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT title,md5(id) as id from s_ticket_quick_reply where is_deleted='N' $qincr order by title ASC";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/get_eticket_quick_reply.inc.php';
  exit;
}
?>