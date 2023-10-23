<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$vendor_id = $_GET['id'];
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
} else {
  $per_page = 10;
}

$sch_params = array();
$incr = "";
$SortBy = "v.id";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$has_querystring = false;

if ($_GET["sort"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort"];
}
$vendor_name = "";
if($vendor_id){
	$vendor_name = getname('vendor',$vendor_id,'vendor_name','id');
	$incr .= " AND v.id = :vendor_id";
	$sch_params['vendor_id'] = $vendor_id;
}

if ($_GET["direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["direction"];
}

$is_ajaxed = $_GET['is_ajaxed'];
if (count($sch_params) > 0) {
  $has_querystring = true;
}

$query_string = $has_querystring ? ($_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'vendors.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {

	$vendor_name = "";
	if($vendor_id){
		$vendor_name = getname('vendor',$vendor_id,'vendor_name','id');
	}
	try {
		$sql = "SELECT p.product_code,p.name,p.status FROM vendor v 
	            JOIN vendor_fee vf on v.id = vf.vendor_id
	            JOIN prd_main p on(FIND_IN_SET(p.id,vf.product_ids))
	    		Where v.is_deleted = 'N' $incr GROUP BY p.id";

		$paginate = new pagination($page, $sql, $options);
	    if ($paginate->success == true) {
	        $vendor_data = $paginate->resultset->fetchAll();
	        $total_rows = count($vendor_data);
	        
	    }
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/vendor_product_popup.inc.php';
  	exit;
}
if (isset($_GET["is_export"]) && $_GET["is_export"]) {
    $csv_line = "\n";
    $csv_seprator = "\t";
    $content = "";

    $content .= "Product Name" . $csv_seprator .
                "Product ID" . $csv_seprator .
                "Current Status" . $csv_line;

    $sql = "SELECT p.product_code,p.name,p.status FROM vendor v 
	            JOIN vendor_fee vf on v.id = vf.vendor_id
	            JOIN prd_main p on(FIND_IN_SET(p.id,vf.product_ids))
	    		Where v.is_deleted = 'N' $incr GROUP BY p.id";
    $vendor_data = $pdo->select($sql,$sch_params);

    if($vendor_data){
      foreach ($vendor_data as $key => $value) {
        $content .= $value['name'] . $csv_seprator .
                    $value['product_code'] . $csv_seprator .
                    $value['status'] . $csv_line;
      }
      if ($content) {
          $csv_filename = "vendor_products_" . date("Ymd", time()) . ".xls";
          header('Content-type: application/vnd.ms-excel');
          header('Content-disposition: attachment;filename=' . $csv_filename);
          echo $content;
          exit;
      }
    }else{
    	setNotifyError("No Members Found");
    }
}

$template = 'vendor_product_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>