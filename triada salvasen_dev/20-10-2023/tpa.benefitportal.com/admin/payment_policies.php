<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Payment';
$breadcrumbes[2]['title'] = 'Policies';
$breadcrumbes[2]['link'] = 'payment_policies.php';

$sch_params=array();
$extraIncr = $incr=''; 
$SortBy = "w.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$per_page = isset($_GET['pages']) ? $_GET['pages'] : 10;
$ws_id = isset($_POST['ws_id']) ? $_POST['ws_id'] : "";
$status = isset($_POST['status']) ? $_POST['status'] : "";

if(!empty($ws_id) && !empty($status)){
  $ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions WHERE id = :id",array(':id' => $ws_id));
  if($ws_row){

    $update_params = array('updated_at' => 'msqlfunc_NOW()');
    $update_params['status'] = $status;

    if($status == 'Active'){
      $update_params['termination_date'] = NULL;
      $update_params['term_date_set'] = NULL;

      $upd_params = array('status' => 'Active','terminationDate' => NULL,'updated_at' => 'msqlfunc_NOW()');

    }else if($status == 'Pending'){

      $upd_params = array('status' => 'Pending','terminationDate' => NULL,'updated_at' => 'msqlfunc_NOW()');

      $cd_update_params = array('process_status' => 'Pending');
      $cd_udpate_where = array(
              "clause" => "website_id=:id",
              "params" => array(
                      ":id" => $ws_row['id'],
              ),
      );
      
      $pdo->update('customer_enrollment',$cd_update_params,$cd_udpate_where);
    }else if($status == 'Inactive'){
      $upd_params = array('status' => 'Inactive','updated_at' => 'msqlfunc_NOW()');
    }

    $udpate_where = array(
          "clause" => "id=:id",
          "params" => array(
                  ":id" => $ws_row['id'],
          ),
    );
    $pdo->update('website_subscriptions',$update_params,$udpate_where);


    $dependents = $pdo->select("SELECT id FROM customer_dependent WHERE website_id = :website_id and is_deleted = 'N'",array(':website_id' => $ws_id));

    if($dependents){
      foreach ($dependents as $value) {
      
        $udp_where = array(
                "clause" => "id=:id",
                "params" => array(
                        ":id" => $value['id'],
                ),
        );
        $pdo->update('customer_dependent',$upd_params,$udp_where);
      }
    }

    $insert_s_his_prm = array(
        'customer_id' => $ws_row['customer_id'],
        'website_id' => $ws_row['id'],
        'product_id' => $ws_row['product_id'],
        'plan_id' => $ws_row['plan_id'],
        'order_id' => $ws_row['last_order_id'],
        'status' => 'Change status',
        'message' => 'status changed from' . get_policy_display_status($ws_row['status']). ' to ' .get_policy_display_status($status),
        'admin_id' => $_SESSION['admin']['id'],
        'created_at' => 'msqlfunc_NOW()',
    );
    $pdo->insert("website_subscriptions_history", $insert_s_his_prm);

    $af_message = 'changed plan status';
    $af_desc = array();
    $af_desc['ac_message'] =array(
        'ac_red_1'=>array(
            'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=> $_SESSION['admin']['display_id'],
        ),
        'ac_message_1' => $af_message.' on ',
        'ac_red_2'=>array(
            'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
            'title'=>getname('customer',$ws_row['customer_id'],'rep_id','id'),
        ),
        'ac_message_2' =>' <br/> Plan : '.display_policy($ws_row['id']).' <br/> Plan status changed from : '. get_policy_display_status($ws_row['status']) .' to : '.get_policy_display_status($status),
    );
    activity_feed(3, $_SESSION['admin']['id'], 'Admin',$ws_row['customer_id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

  }
  $response['status'] = 'Success';
  setNotifySuccess("Status changed successfully");
  echo json_encode($response);
  exit();
}

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
  $has_querystring = true;
  $SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
  $has_querystring = true;
  $currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = checkIsset($_GET['is_ajaxed']);
$is_export = !empty($_GET['export_val']) ? $_GET['export_val'] : ''; 
$member_id = checkIsset($_GET['member_id']);
$status = checkIsset($_GET['status']);
$policy_ids = checkIsset($_GET['policy_ids']);
$product_ids = checkIsset($_GET['product']);

if (!empty($member_id)) { 
  $incr.=" AND c.id IN($member_id)";
  $extraIncr.=" AND res.customer_id IN($member_id)";
}

if (!empty($policy_ids)) {
  if(!empty($policy_ids)){
    $incr.=" AND w.id IN($policy_ids)";
    $extraIncr.=" AND w.id IN($policy_ids)";
  }
}
if (!empty($product_ids)) {  
  $product_ids = implode(',', $product_ids);
  // $incr.=" AND w.product_id IN($product_ids)";
  $incr.=" AND (p.id IN($product_ids) OR p.parent_product_id IN($product_ids)) ";
  $extraIncr.=" AND (prd.id IN($product_ids) OR prd.parent_product_id IN($product_ids))";
}


if ($status != "") {
  $db_status_arr = get_policy_db_status($status);
  if(!empty($db_status_arr)) {
    $incr.=" AND w.status IN('".implode("','",$db_status_arr)."')";
    $extraIncr .= " AND w.status IN('".implode("','",$db_status_arr)."')";
  } else {

    $sch_params[':status'] = $status;	
    $incr.=" AND w.status = :status";
    $extraIncr .= " AND w.status = :status ";
  }
}

$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$order_fromdate = isset($_GET["order_fromdate"])?$_GET["order_fromdate"]:"";
$order_todate = isset($_GET["order_todate"])?$_GET["order_todate"]:"";
$order_added_date = isset($_GET["order_added_date"])?$_GET["order_added_date"]:"";

$next_billing_range = isset($_GET['next_billing_range'])?$_GET['next_billing_range']:"";
$next_billing_fromdate = isset($_GET["next_billing_fromdate"])?$_GET["next_billing_fromdate"]:"";
$next_billing_todate = isset($_GET["next_billing_todate"])?$_GET["next_billing_todate"]:"";
$next_billing_added_date = isset($_GET["next_billing_added_date"])?$_GET["next_billing_added_date"]:"";

$getorderfromdate = '';
$getordertodate = '';
if($join_range != ""){
  if($join_range == "Range" && $order_fromdate!='' && $order_todate!=''){
    $sch_params[':order_fromdate'] = date("Y-m-d",strtotime($order_fromdate));
    $sch_params[':order_todate'] = date("Y-m-d",strtotime($order_todate));
    $incr.=" AND DATE(w.created_at) >= :order_fromdate AND DATE(w.created_at) <= :order_todate";
    $extraIncr.=" AND DATE(w.created_at) >= :order_fromdate AND DATE(w.created_at) <= :order_todate";
    $getorderfromdate = $order_fromdate;
      $getordertodate = $order_todate;
  }else if($join_range == "Exactly" && $order_added_date!=''){
    $sch_params[':order_added_date'] = date("Y-m-d",strtotime($order_added_date));
    $incr.=" AND DATE(w.created_at) = :order_added_date";
    $extraIncr.=" AND DATE(w.created_at) = :order_added_date";

    $getorderfromdate = $order_added_date;
      $getordertodate = $order_added_date;
  }else if($join_range == "Before" && $order_added_date!=''){
    $sch_params[':order_added_date'] = date("Y-m-d",strtotime($order_added_date));
    $incr.=" AND DATE(w.created_at) < :order_added_date";
    $extraIncr.=" AND DATE(w.created_at) < :order_added_date";
    $getorderfromdate = $order_added_date;
    	$getordertodate = date('Y-m-d');
  }else if($join_range == "After" && $order_added_date!=''){
    $sch_params[':order_added_date'] = date("Y-m-d",strtotime($order_added_date));
    $incr.=" AND DATE(w.created_at) > :order_added_date";
    $extraIncr.=" AND DATE(w.created_at) > :order_added_date";
    $getorderfromdate = date('Y-m-d');
    	$getordertodate = $order_added_date;
  }
}

$getNBfromdate = '';
$getNBtodate = '';

if($next_billing_range != ""){
  if($next_billing_range == "Range" && $next_billing_fromdate!='' && $next_billing_todate!=''){
    $sch_params[':next_billing_fromdate'] = date("Y-m-d",strtotime($next_billing_fromdate));
    $sch_params[':next_billing_todate'] = date("Y-m-d",strtotime($next_billing_todate));
    $incr.=" AND DATE(w.next_purchase_date) >= :next_billing_fromdate AND DATE(w.next_purchase_date) <= :next_billing_todate";
    $extraIncr.=" AND DATE(w.next_purchase_date) >= :next_billing_fromdate AND DATE(w.next_purchase_date) <= :next_billing_todate ";
    $getNBfromdate = $next_billing_fromdate;
      $getNBtodate = $next_billing_todate;
  }else if($next_billing_range == "Exactly" && $next_billing_added_date!=''){
    $sch_params[':next_billing_added_date'] = date("Y-m-d",strtotime($next_billing_added_date));
    $incr.=" AND DATE(w.next_purchase_date) = :next_billing_added_date";
    $extraIncr.=" AND DATE(w.next_purchase_date) = :next_billing_added_date ";
    $getNBfromdate = $next_billing_added_date;
      $getNBtodate = $next_billing_added_date;
  }else if($next_billing_range == "Before" && $next_billing_added_date!=''){
    $sch_params[':next_billing_added_date'] = date("Y-m-d",strtotime($next_billing_added_date));
    $incr.=" AND DATE(w.next_purchase_date) < :next_billing_added_date";
    $extraIncr.=" AND DATE(w.next_purchase_date) < :next_billing_added_date ";
    $getNBfromdate = $next_billing_added_date;
    	$getNBtodate = date('Y-m-d');
  }else if($next_billing_range == "After" && $next_billing_added_date!=''){
    $sch_params[':next_billing_added_date'] = date("Y-m-d",strtotime($next_billing_added_date));
    $incr.=" AND DATE(w.next_purchase_date) > :next_billing_added_date";
    $extraIncr.=" AND DATE(w.next_purchase_date) > :next_billing_added_date ";
    $getNBfromdate = date('Y-m-d');
    	$getNBtodate = $next_billing_added_date;
  }
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (checkIsset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'payment_policies.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

if($is_export) {

  include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
  
  if($getorderfromdate!='' && $getordertodate != '') {

    $no_days=0;
    if($getorderfromdate!= '' && $getordertodate!='') {
      $date1 = date_create($getorderfromdate);
      $date2 = date_create($getordertodate);
      $diff = date_diff($date1,$date2);
      $no_days=$diff->format("%a");
    }
    
    if($no_days>62) {
      echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
      exit();
    }
  }

  if($getNBfromdate!='' && $getNBtodate != '') {

    $no_days=0;
    if($getNBfromdate!= '' && $getNBtodate!='') {
      $date1 = date_create($getNBfromdate);
      $date2 = date_create($getNBtodate);
      $diff = date_diff($date1,$date2);
      $no_days=$diff->format("%a");
    }
    
    if($no_days>62) {
      echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
      exit();
    }
  }

  $job_id=add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Payment Policy Overview","payment_policy_overview",$extraIncr, $sch_params,array(),'payment_policy_overview');
  
  echo json_encode(array("status"=>"success","message"=>"Your export request is added")); 
  exit;
}

$page = (checkIsset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed){

  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' viewed policies ',
  ); 

  activity_feed(3, $_SESSION['admin']['id'], 'Admin',0, 'orders','Viewed Policies', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

  try {
      
    $sql="SELECT w.*,CONCAT(c.fname,' ',c.lname) as member_name,c.rep_id,p.name
              FROM website_subscriptions w
              JOIN customer c ON (c.id = w.customer_id)
              JOIN prd_main p on(w.product_id = p.id)
              WHERE w.status NOT IN ('Pending Declined','Pending Payment','Post Payment') AND c.status NOT IN ('Pending Quote','Pending Quotes','Pending Validation') " . $incr . "
              GROUP BY w.id ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
       
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
 
  include_once 'tmpl/payment_policies.inc.php';
  exit;
}

$company_arr = get_active_global_products_for_filter();

$selectize = true;
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);


$template = 'payment_policies.inc.php';
include_once 'layout/end.inc.php';
?>