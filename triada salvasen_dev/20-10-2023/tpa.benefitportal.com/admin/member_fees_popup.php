<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$sch_params = array();
$incr=''; 

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
  $has_querystring = true;
  $SortBy = $_GET["sort_by"];
}

$popup_is_ajaxed = checkIsset($_GET['popup_is_ajaxed']);  
$id = checkIsset($_GET['id']); 
$name = checkIsset($_GET['name']); 
$display_id = checkIsset($_GET['display_id']); 
$total_rows = checkIsset($_GET['count']); 

if(!empty($id)){
  $sch_params[":id"] = makeSafe($id);
  $incr .= " AND md5(pf.id) = :id"; 
} 

if (count($sch_params) > 0) {
  $has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
  
$options = array(
  'results_per_page' => $per_page,
  'url' => 'member_fees_popup.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
 
if ($popup_is_ajaxed) {
  try {
    $sel_sql = "SELECT c.rep_id,CONCAT(c.fname,' ',c.lname) as customer_name,c.status,count(w.id) as members_per_id 
              FROM prd_fees pf 
              JOIN prd_assign_fees pa ON (pa.prd_fee_id = pf.id) 
              JOIN website_subscriptions w ON (pa.product_id = w.product_id)
              JOIN customer c ON (w.customer_id = c.id)
              WHERE pf.is_deleted = 'N' AND pa.is_deleted='N'   $incr 
              HAVING members_per_id > 0";
     
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }

  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/member_fees_popup.inc.php';
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

  $sql = "SELECT c.rep_id,CONCAT(c.fname,' ',c.lname) as customer_name,c.status,count(w.id) as members_per_id FROM vendor v 
            JOIN vendor_fee vf on v.id = vf.vendor_id
            JOIN prd_main p on(FIND_IN_SET(p.id,vf.product_ids))
            JOIN website_subscriptions w on p.id = w.product_id
            JOIN customer c on w.customer_id = c.id
        Where v.is_deleted = 'N' $incr HAVING members_per_id > 0";
  $vendor_data = $pdo->select($sql,$sch_params);

  if($vendor_data){
    foreach ($vendor_data as $key => $value) {
      $content .= $value['rep_id'] . $csv_seprator .
                  $value['customer_name'] . $csv_seprator .
                  $value['members_per_id'] . $csv_seprator .
                  $value['status'] . $csv_line;
    }
    if ($content) {
        $csv_filename = "vendor_members_" . date("Ymd", time()) . ".xls";
        header('Content-type: application/vnd.ms-excel');
        header('Content-disposition: attachment;filename=' . $csv_filename);
        echo $content;
        exit;
    }
  }else{
    setNotifyError("No Members Found");
  }
}

$template = 'member_fees_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>