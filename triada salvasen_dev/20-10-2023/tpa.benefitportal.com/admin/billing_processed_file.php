<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$file_id = isset($_GET['id']) ? $_GET['id'] : "";
$file_name = getname("billing_files",$file_id,'file_name','id');


$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Billing File";
$breadcrumbes[1]['link'] = "billing_files.php";
$breadcrumbes[2]['title'] = "History Details For ".$file_name." File";
$breadcrumbes[2]['link'] = 'billing_processed_file.php?id=' . $file_id;

$sch_params = array();
$SortBy = "bh.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";



$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';

$incr = "";

$products = isset($_GET['products']) ? $_GET['products'] : array();
$f_name = isset($_GET['file_name']) ? $_GET['file_name'] : "";
$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
$todate = isset($_GET["todate"])?$_GET["todate"]:"";
$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";

if($products){
	$incr .= " AND FIND_IN_SET(bf.products,:products)";
	$sch_params[':products'] = implode(',', $products);
}
if($f_name){
	$incr .= " AND bf.file_name = :file_name";
	$sch_params[':file_name'] = $f_name;
}

if($file_id){
	$incr .= " AND bf.id = :file_id";
	$sch_params[':file_id'] = $file_id;
}
if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(bh.created_at) >= :fromdate AND DATE(bh.created_at) <= :todate";
  }else if($join_range == "Exactly" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(bh.created_at) = :added_date";
  }else if($join_range == "Before" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(bh.created_at) < :added_date";
  }else if($join_range == "After" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(bh.created_at) > :added_date";
  }
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}

$page = isset($_GET['page']) ? $_GET['page'] : '';
$query_string = $has_querystring ? ($page ? str_replace('page=' . $page, "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'billing_processed_file.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if(!$is_ajaxed){
	$description['ac_message'] =array(
	  'ac_red_1'=>array(
	    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	    'title'=>$_SESSION['admin']['display_id'],
	  ),
	  'ac_message_1' =>' read processed billing files ',
	  'ac_red_2'=>array(
	    'title'=> $file_name,
	  ),
	); 
	activity_feed(3, $_SESSION['admin']['id'], 'Admin', 0, 'billing_files','Admin read processed billing files', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
}


if ($is_ajaxed) {
	try {
		
		$sel_sql = "SELECT bh.*,bf.file_name,bh.file_name as processed_file,CONCAT(a.fname,' ',a.lname) as admin_name,a.display_id 
		FROM billing_files bf
		JOIN billing_history bh on bh.service_group_id = bf.id
		LEFT JOIN admin a on a.id = bh.admin_id
		where bf.is_deleted = 'N' $incr order by bh.created_at DESC";

		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/billing_processed_file.inc.php';
	exit;
}





$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'billing_processed_file.inc.php';
include_once 'layout/end.inc.php';
?>
