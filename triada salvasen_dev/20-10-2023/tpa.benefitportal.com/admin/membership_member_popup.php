<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$membership_id = $_GET['id'];
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
} else {
  $per_page = 10;
}

$sch_params = array();
$incr = "";
$SortBy = "pf.id";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$has_querystring = false;

if (isset($_GET["sort"]) && $_GET["sort"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort"];
}
$vendor_name = "";
if($membership_id){
	$membership_name = getname('prd_fees',$membership_id,'name','id');
	$incr .= " AND pf.id = :membership_id";
	$sch_params['membership_id'] = $membership_id;
}

if (isset($_GET["direction"]) && $_GET["direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["direction"];
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : 0;
if (count($sch_params) > 0) {
  $has_querystring = true;
}
$page = "";

if(isset($_GET['page'])){
	$page = $_GET['page'];
}

$query_string = $has_querystring ? ($page ? str_replace('page=' . $page, "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'memberships.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {

	$membership_name = "";
	if($membership_id){
		$membership_name = getname('prd_fees',$membership_id,'name','id');
	}
	try {

	    $sql = "SELECT c.rep_id,CONCAT(c.fname,' ',c.lname) as customer_name,c.status,count(w.id) as members_per_id
	    		FROM prd_main p
                JOIN prd_fees pf on FIND_IN_SET(pf.id,p.prd_fee_id)
                JOIN prd_assign_fees paf on pf.id = paf.fee_id
                JOIN website_subscriptions w on p.id = w.product_id
	            JOIN customer c on w.customer_id = c.id
                WHERE pf.is_deleted = 'N' $incr GROUP BY p.id having members_per_id > 0";

		$paginate = new pagination($page, $sql, $options);
	    if ($paginate->success == true) {
	        $membership_data = $paginate->resultset->fetchAll();
	        $total_rows = count($membership_data);
	        
	    }
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/membership_member_popup.inc.php';
  	exit;
}
if (isset($_GET["is_export"]) && $_GET["is_export"]) {
    $csv_line = "\n";
    $csv_seprator = "\t";
    $content = "";

    $content .= "Member ID" . $csv_seprator .
                "Member Name" . $csv_seprator .
                "Active Members Per ID" . $csv_seprator .
                "Status" . $csv_line;

    $sql = "SELECT c.rep_id,CONCAT(c.fname,' ',c.lname) as customer_name,c.status,count(w.id) as members_per_id
	    		FROM prd_main p
                JOIN prd_fees pf on FIND_IN_SET(pf.id,p.prd_fee_id)
                JOIN prd_assign_fees paf on pf.id = paf.fee_id
                JOIN website_subscriptions w on p.id = w.product_id
	            JOIN customer c on w.customer_id = c.id
                WHERE pf.is_deleted = 'N' $incr GROUP BY p.id having members_per_id > 0";
    $vendor_data = $pdo->select($sql,$sch_params);

    if($vendor_data){
      foreach ($vendor_data as $key => $value) {
        $content .= $value['rep_id'] . $csv_seprator .
                    $value['customer_name'] . $csv_seprator .
                    $value['members_per_id'] . $csv_seprator .
                    $value['status'] . $csv_line;
      }
      if ($content) {
          $csv_filename = "membership_members_" . date("Ymd", time()) . ".xls";
          header('Content-type: application/vnd.ms-excel');
          header('Content-disposition: attachment;filename=' . $csv_filename);
          echo $content;
          exit;
      }
    }else{
    	setNotifyError("No Members Found");
    	// redirect('');
    }
}


$template = 'membership_member_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>