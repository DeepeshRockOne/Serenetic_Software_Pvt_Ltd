<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(53);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Memberships";
$breadcrumbes[1]['class'] = "Active";
$manage_products = "active";

$membership_display_ids = $pdo->select("SELECT display_id FROM prd_fees WHERE setting_type = 'membership' AND is_deleted = 'N'");

$status = !empty($_GET["status"]) ? $_GET["status"] : '';

$sch_params = array();
$incr = '';
$SortBy = "pf.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";


$has_querystring = false;
if (!empty($_GET["sort_by"])) {
  $has_querystring = true;
  $SortBy = $_GET["sort_by"];
}


if (!empty($_GET["sort_direction"])) {
  $has_querystring = true;
  $currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = !empty($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$is_export = !empty($_GET['is_export']) ? $_GET['is_export'] : '';
$membership_display_id = !empty($_GET['membership_display_id']) ? $_GET['membership_display_id'] : array();
$search_date_options = (isset($_GET["search_date_options"])) ? $_GET["search_date_options"] : "";
$search_date_options = (isset($_GET["join_range"])) ? $_GET["join_range"] : "";
$from_date = (isset($_GET["from_date"])) ? $_GET["from_date"] : "";
$end_date = (isset($_GET["end_date"])) ? $_GET["end_date"] : "";
$added_date = (isset($_GET["added_date"])) ? $_GET["added_date"] : "";
$membership_name = !empty($_GET["membership_name"]) ? $_GET["membership_name"] : '';
$contact_name = !empty($_GET["contact_name"]) ? $_GET["contact_name"] : '';
$member_id = !empty($_GET["member_id"]) ? $_GET["member_id"] : '';
$product = !empty($_GET["product"]) ? implode(',',$_GET["product"]) : '';
$fee_status = !empty($_GET['fee_status']) ? $_GET['fee_status'] : '';

if (!empty($membership_display_id)) {
  // $membership_display_id = str_replace(" ", "", $membership_display_id);
  // $membership_display_id = explode(',', $membership_display_id);
  $membership_display_id = "'" . implode("','", $membership_display_id) . "'";
  $incr.=" AND pf.display_id IN ($membership_display_id)";
}

if($search_date_options != ""){
  if($search_date_options == "Range"){
    $sch_params[':from_date'] = date("Y-m-d",strtotime($from_date));
    $sch_params[':end_date'] = date("Y-m-d",strtotime($end_date));
    $incr.=" AND DATE(pf.created_at) >= :from_date AND DATE(pf.created_at) <= :end_date";
  }else if($search_date_options == "Exactly"){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pf.created_at) = :added_date";
  }else if($search_date_options == "Before"){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pf.created_at) < :added_date";
  }else if($search_date_options == "After"){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pf.created_at) > :added_date";
  }
}

$membership_name= cleanSearchKeyword($membership_name);
$contact_name = cleanSearchKeyword($contact_name); 
 
if ($membership_name != "") {
  $sch_params[':name'] = "%" . makeSafe($membership_name) . "%";
  $incr .= " AND pf.name LIKE :name";
}
if ($contact_name != "") {
  $sch_params[':conatct_name'] = "%" . makeSafe($contact_name) . "%";
  $incr .= " AND (pf.contact_fname LIKE :conatct_name OR pf.contact_lname LIKE :conatct_name OR CONCAT(pf.contact_fname,' ',pf.contact_lname) LIKE :conatct_name)";
}
if ($fee_status != "") {
  $sch_params[':status'] = makeSafe($fee_status);
  $incr .= " AND pf.status = :status";
}

if(!empty($product)){
  $incr .= " AND (pp.id in (".$product.") OR pp.parent_product_id in (".$product."))";
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {

  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (!empty($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
  'results_per_page' => $per_page,
  'url' => 'memberships.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params,
);

$page = (!empty($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if (isset($_GET['fees_status']) && isset($_GET['fee_id'])) {
  $change_type = $_GET['fees_status'];
  $fee_id = $_GET['fee_id'];

  if ($fee_id != "" && $fee_id > 0 && $change_type != "") {
    
    $query = "SELECT status FROM prd_fees WHERE id =" . $fee_id;
    $srow = $pdo->selectOne($query);

    $updateSql = array('status' => makeSafe($change_type));
    $where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($fee_id)));
    $pdo->update("prd_fees", $updateSql, $where);

    
    setNotifySuccess("Membership status changed successfully");

    redirect("memberships.php");
  }
}

if (isset($_GET['delete']) && isset($_GET['fee_id'])) {
  $fee_id = $_GET['fee_id'];

  if ($fee_id != "" && $fee_id > 0) {
    
    $query = "SELECT status,display_id FROM prd_fees WHERE id =" . $fee_id;
    $srow = $pdo->selectOne($query);

    $updateSql = array('is_deleted' => 'Y');
    $where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($fee_id)));
    $pdo->update("prd_fees", $updateSql, $where);

    $assign_fees = $pdo->select("SELECT id FROM prd_assign_fees where prd_fee_id = :fee_id AND is_deleted = 'N'",array(':fee_id' => $fee_id));

    if($assign_fees){
      $updateSql = array('is_deleted' => 'Y');
      $where = array("clause" => 'prd_fee_id=:id', 'params' => array(':id' => makeSafe($fee_id)));
      $pdo->update("prd_assign_fees", $updateSql, $where);
    }

    $assign_products = $pdo->select("SELECT p.id FROM prd_main p
                                     JOIN prd_assign_fees paf on(p.id = paf.fee_id)
                                     where paf.prd_fee_id = :fee_id AND p.is_deleted = 'N'",array(':fee_id' => $fee_id));

    if($assign_products){
      foreach ($assign_products as $key => $value) {
        $updateSql = array('is_deleted' => 'Y');
        $where = array("clause" => 'id=:id', 'params' => array(':id' => $value['id']));
        $pdo->update("prd_main", $updateSql, $where);
      }
    }

    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' deleted membership ',
      'ac_red_2'=>array(
          // 'href'=>$ADMIN_HOST.'/memberships_mange.php?id='.md5($fee_id),
          'title'=>$srow['display_id'],
      ),
    ); 

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $fee_id, 'prd_fees','deleted membership', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    $response['status'] = 'success';
    $response['message'] = "Membership deleted successfully";

    echo json_encode($response);
    exit();
  }
}
if ($is_ajaxed) {

  try {
  
    $sel_sql = "SELECT pf.*,paf2.price as total_fee,count(DISTINCT pp.id)as total_products,count(distinct c.id) as total_members
                FROM prd_fees pf
                LEFT JOIN (
                    SELECT MIN(pm2.price) AS price,paf3.prd_fee_id
                    FROM prd_assign_fees paf3
                    JOIN prd_main p2 ON paf3.fee_id=p2.id
                    JOIN prd_matrix pm2 ON pm2.product_id = p2.id
                    WHERE pm2.is_deleted='N' AND p2.is_deleted='N' AND paf3.is_deleted='N' GROUP BY paf3.prd_fee_id
                ) AS paf2 ON(paf2.prd_fee_id = pf.id)
                LEFT JOIN prd_assign_fees paf on (pf.id = paf.prd_fee_id AND paf.is_deleted = 'N')
                LEFT JOIN prd_main pp on (FIND_IN_SET(paf.prd_fee_id,pp.membership_ids) AND paf.product_id = pp.id  AND pp.is_deleted = 'N')
                LEFT JOIN website_subscriptions w on (pp.id = w.product_id AND w.status IN('Active','Inactive'))
                LEFT JOIN customer c on (w.customer_id = c.id AND c.is_deleted='N' AND c.STATUS NOT IN('Customer Abandon','Pending Quote','Pending Validation','Post Payment','Pending'))
                WHERE pf.is_deleted = 'N' AND pf.setting_type = 'membership' " . $incr . " GROUP BY pf.id ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $sel_sql, $options);
      if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }

  include_once 'tmpl/memberships.inc.php';
  exit;
}

if ($is_export) {
    $csv_line = "\n";
    $csv_seprator = "\t";
    $content = "";

    $content .= "Membership ID" . $csv_seprator .
                "Added Date" . $csv_seprator .
                "Membership Name" . $csv_seprator .
                "Contact Name" . $csv_seprator .
                "Phone" . $csv_seprator .
                "Email" . $csv_seprator .
                "Address" . $csv_seprator .
                "City" . $csv_seprator .
                "State" . $csv_seprator .
                "Zip Code" . $csv_seprator .
                "Products" . $csv_seprator .
                "Fees" . $csv_seprator .
                "Members" . $csv_seprator .
                "Status" . $csv_line;

    $sql = "SELECT pf.*,sum(paf2.price) as total_fee,count(DISTINCT pp.id)as total_products,count(distinct c.id) as totat_members 
                FROM prd_fees pf
                LEFT JOIN (
                    SELECT MIN(pm2.price) AS price,paf3.prd_fee_id
                    FROM prd_assign_fees paf3
                    JOIN prd_main p2 ON paf3.fee_id=p2.id
                    JOIN prd_matrix pm2 ON pm2.product_id = p2.id
                    WHERE pm2.is_deleted='N' AND p2.is_deleted='N' AND paf3.is_deleted='N' GROUP BY paf3.prd_fee_id
                ) AS paf2 ON(paf2.prd_fee_id = pf.id)
                LEFT JOIN prd_assign_fees paf on (pf.id = paf.prd_fee_id AND paf.is_deleted = 'N')
                LEFT JOIN prd_main pp on (FIND_IN_SET(paf.prd_fee_id,pp.membership_ids) AND paf.product_id = pp.id  AND pp.is_deleted = 'N')
                LEFT JOIN website_subscriptions w on (pp.id = w.product_id AND w.status IN('Active','Inactive'))
                LEFT JOIN customer c on (w.customer_id = c.id AND c.is_deleted='N' AND c.STATUS NOT IN('Customer Abandon','Pending Quote','Pending Validation','Post Payment','Pending'))
                WHERE pf.is_deleted = 'N' AND pf.setting_type = 'membership' " . $incr . " GROUP BY pf.id ORDER BY $SortBy $currSortDirection";            
    $membership_data = $pdo->select($sql,$sch_params);

    if($membership_data){
      foreach ($membership_data as $key => $value) {
        $content .= $value['display_id'] . $csv_seprator .
                    date('m/d/Y',strtotime($value['created_at'])) . $csv_seprator .
                    $value['name'] . $csv_seprator .
                    $value['contact_fname'] . " " . $value['contact_lname'] . $csv_seprator .
                    $value['phone'] . $csv_seprator .
                    $value['email'] . $csv_seprator .
                    $value['address'] . " " . $value['address2'] . $csv_seprator .
                    $value['city'] . $csv_seprator .
                    $value['state'] . $csv_seprator .
                    $value['zipcode'] . $csv_seprator .
                    $value['total_products'] . $csv_seprator .
                    $value['total_fee'] . $csv_seprator .
                    $value['totat_members'] . $csv_seprator .
                    $value['status'] . $csv_line;
      }
      if ($content) {
          $csv_filename = "memberships_" . date("Ymd", time()) . ".xls";
          header('Content-type: application/vnd.ms-excel');
          header('Content-disposition: attachment;filename=' . $csv_filename);
          echo $content;
          exit;
      }
    }
}

$company_arr = get_active_global_products_for_filter();

$page_title = "Memberships";
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js','thirdparty/masked_inputs/jquery.maskedinput.min.js');
$template = 'memberships.inc.php';
include_once 'layout/end.inc.php';
?>