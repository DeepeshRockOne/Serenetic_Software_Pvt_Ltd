<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$module_access_type = has_access(90);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Commission Agent";
$breadcrumbes[2]['link'] = 'agents_commissions.php';

$incr = '';
$sch_params = [];
$has_querystring = false;
$SortDirection = "DESC";
$currSortDirection = "ASC";

if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';

if($is_ajaxed){
$agent_id = !empty($_GET['agents_ids']) ? checkIsset($_GET['agents_ids'],'arr') : "";

if (!empty($agent_id)) {
  // pre_print($agent_id);
  $agent_id = "'" . implode("','", makeSafe($agent_id)) . "'";
  $incr .= " where a.id IN ($agent_id) ";
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
  'results_per_page' => $per_page,
  'url' => 'agents_commissions.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';

          try {
          	
            $sel_sql = "SELECT a.id as agentId,a.rep_id as agentDispId,CONCAT(a.fname,' ',a.lname) as agentName,
            a.email as agentemail,a.cell_phone as agentephone FROM customer a
            JOIN commission c ON(a.id=c.customer_id) " . $incr . " GROUP BY c.customer_id order by agentName ASC";
            $started = microtime(true);
            $paginate = new pagination($page, $sel_sql, $options);
            $end = microtime(true);
            $difference = $end - $started;
            $queryTime = number_format($difference, 10);
            if ($paginate->success == true) {
              $fetch_rows = $paginate->resultset->fetchAll();
              $total_rows = count($fetch_rows);
            }
          } catch (paginationException $e) {
            echo $e;
            exit();
          }

          include_once 'tmpl/agents_commissions.inc.php';
          exit;
    }              

$resAgent = $pdo->selectOne("SELECT a.id as agentId,a.rep_id as agentDispId,CONCAT(a.fname,' ',a.lname) as agentName,ss.company_name FROM customer a JOIN customer_settings ss ON(ss.customer_id=a.id) WHERE a.id=1");

$selCommAgent = "SELECT a.id as agentId,a.rep_id as agentDispId,CONCAT(a.fname,' ',a.lname) as agentName
				FROM customer a JOIN commission c ON(a.id=c.customer_id) GROUP BY c.customer_id order by agentName ASC";
$resCommAgent = $pdo->select($selCommAgent);

// $excludePrdList = get_active_global_products_for_filter(0,true);
$selectize = true;

  activity_feed(3, $_SESSION['admin']['id'], 'Admin',0, 'commission','Read Commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
// Read Commisssions Page activity code ends



$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css',   
	'thirdparty/select2/css/select2.css'
);

$exJs = array( 
	'thirdparty/multiple-select-master/jquery.multiple.select.js',    
	'thirdparty/select2/js/select2.full.min.js'
);

$template = 'agents_commissions.inc.php';
include_once 'layout/end.inc.php';
?>