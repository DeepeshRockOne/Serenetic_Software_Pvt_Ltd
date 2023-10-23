<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
//error_reporting(E_ALL);
// sub_agent_has_access(18);
// $sch_params = array();
// $SortBy = "pb.created_at";
// $SortDirection = "DESC";
// $currSortDirection = "DESC";

// $has_querystring = false;
// if ($_GET["sort_by"] != "") {
//   $has_querystring = true;
//   $SortBy = $_GET["sort_by"];
// }
// if ($_GET["sort_direction"] != "") {
//   $has_querystring = true;
//   $currSortDirection = $_GET["sort_direction"];
// }

// $agent_id = $_SESSION['agents']['id'];
// $is_ajaxed = $_GET['is_ajaxed'];

// $sch_params[':agent_id'] = $agent_id;
// $incr =" AND pb.agent_id = :agent_id";

// $per_page=10;
// if (count($sch_params) > 0) {
//   $has_querystring = true;
// }
// if (isset($_GET['pages']) && $_GET['pages'] > 0) {
//   $has_querystring = true;
//   $per_page = $_GET['pages'];
// }
// $query_string = $has_querystring ? ($_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

// $options = array(
//     'results_per_page' => $per_page,
//     'url' => 'personalized_sales_page.php?' . $query_string,
//     'db_handle' => $pdo->dbh,
//     'named_params' => $sch_params
// );

// $page = $_GET['page'] > 0 ? $_GET['page'] : 1;
// $options = array_merge($pageinate_html, $options);

// if ($is_ajaxed) {
//   try {
//       $sel_sql = "SELECT pb.* FROM page_builder pb 
//                   JOIN customer as c on(c.id= pb.agent_id) 
//                   WHERE pb.is_deleted ='N' " . $incr . " ORDER BY  $SortBy $currSortDirection";
//       $paginate = new pagination($page, $sel_sql, $options);
//     if ($paginate->success == true) {
//       $fetch_rows = $paginate->resultset->fetchAll();
//       //pre_print($fetch_rows);
//       $total_rows = count($fetch_rows);
//     }
//   } catch (paginationException $e) {
//     echo $e;
//     exit();
//   }
  
//   include_once 'tmpl/personalized_sales_page.inc.php';
//   exit;
// }

// $sel = "SELECT * FROM agent_coded_level WHERE id<=:id";
// $arr = array(":id"=>$agent_level);
// $resAgentLevel = $pdo->select($sel,$arr);


$exStylesheets = array('thirdparty/sweetalert/sweetalert.css');

$exJs = array('thirdparty/clipboard/clipboard.min.js', 'thirdparty/sweetalert/jquery.sweet-alert.custom.js');

$template = 'personalized_sales_page.inc.php';
include_once 'layout/end.inc.php';
?>