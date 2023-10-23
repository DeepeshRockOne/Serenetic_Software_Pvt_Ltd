<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

  $sch_params = array();
  $has_querystring = false;
  $tblIncr = '';
  $incr = '';
  
  $is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
  $name = isset($_GET['name']) ? $_GET['name'] : '';
  $rep_id = isset($_GET['rep_id']) ? $_GET['rep_id'] : '';

  $rep_id = cleanSearchKeyword($rep_id); 
  $name = cleanSearchKeyword($name); 
   
  if(!empty($rep_id)){
      $sch_params[':display_id'] = "%" . makeSafe($rep_id) . "%";
      $incr .= " AND a.display_id like :display_id ";
  }
  if(!empty($name)){
    $sch_params[':title'] = "%" . makeSafe($name) . "%";
    $incr .= " AND sc.title like :title ";
  }

  $per_page=10;
  if (isset($_GET['pages']) && $_GET['pages'] > 0) {
    $has_querystring = true;
    $per_page = $_GET['pages'];
  }
  $query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

  $options = array(
      'results_per_page' => $per_page,
      'url' => 'get_eticket_groups.php?is_ajaxed=1&' . $query_string,
      'db_handle' => $pdo->dbh,
      'named_params' => $sch_params
  );

  $page = isset($_GET["page"]) && $_GET['page'] > 0 ? $_GET['page'] : 1;
  $options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT sc.*,md5(sc.id) as id,count(a.id) as totalassignedAdmin from s_ticket_group sc LEFT JOIN s_ticket_assign_admin sa ON(sa.s_ticket_group_id=sc.id and sa.is_deleted='N') LEFT JOIN admin a ON(a.id=sa.admin_id and a.is_deleted='N')  where sc.is_deleted='N' $incr GROUP BY sc.id order by sc.title ASC";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/get_eticket_groups.inc.php';
  exit;
}
?>