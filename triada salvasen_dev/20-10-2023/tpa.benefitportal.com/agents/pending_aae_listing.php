<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
agent_has_access(11);
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['agents']['timezone']);
$agent_id = $_SESSION['agents']['id'];
$displayDirectEnroll = !empty($_SESSION['agents']['displayDirectEnroll']) ? explode(",", $_SESSION['agents']['displayDirectEnroll']) : array();
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Book of Business';
$breadcrumbes[2]['title'] = 'Pending AAE';
$breadcrumbes[2]['link'] = 'pending_aae_listing.php';

$today = date('m/d/Y');
$sch_params=array();
$incr=''; 
$SortBy = "l.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$per_page = isset($_GET['pages']) ? $_GET['pages'] : 10;

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
$is_export = !empty($_GET['is_export']) ? $_GET['is_export'] : ''; 
$lead_ids = checkIsset($_GET['lead_ids']);
$status = checkIsset($_GET['status'],'arr');
$lead_name = checkIsset($_GET['lead_name']);

$lead_ids = cleanSearchKeyword($lead_ids); 
$lead_name = cleanSearchKeyword($lead_name); 
 
if(!empty($agent_id)){
    if(!empty($displayDirectEnroll) && in_array('Leads', $displayDirectEnroll)){
        $incr .= " AND (c.sponsor_id = :agent_id OR (s.sponsor_id =:agent_id AND scs.agent_coded_level = 'LOA'))";
        $sch_params[':agent_id']=$agent_id;
    }else{
        $incr .= " AND c.upline_sponsors LIKE '%,".$agent_id.",%'";
    }
}

if (!empty($lead_ids)) { 
  $lead_ids = str_replace(' ', '', $lead_ids);
  $lead_ids = explode(',', $lead_ids);
  $lead_ids = "'" . implode ( "', '", $lead_ids ) . "'";
  $incr .=" AND l.lead_id IN($lead_ids)";
}

if (!empty($lead_name)) { 
  $incr.=" AND (l.fname LIKE :name OR l.fname LIKE :name OR CONCAT(l.fname,' ',l.lname) LIKE :name)";
  $sch_params[':name'] = '%' . $lead_name . '%';
}

if (!empty($status)) {
  if(in_array("Post Payment",$status)){
    $incr.=" AND ( (l.status = 'Working' AND c.status='Post Payment') OR (l.status IN('".implode("','",$status)."')) )";  
  }else{
    $incr.=" AND l.status IN ('".implode("','",$status)."') AND c.status!='Post Payment'";  
  }
}

$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$lead_fromdate = isset($_GET["lead_fromdate"])?$_GET["lead_fromdate"]:"";
$lead_todate = isset($_GET["lead_todate"])?$_GET["lead_todate"]:"";
$lead_added_date = isset($_GET["lead_added_date"])?$_GET["lead_added_date"]:"";

$post_payment_range = isset($_GET['post_payment_range'])?$_GET['post_payment_range']:"";
$post_payment_fromdate = isset($_GET["post_payment_fromdate"])?$_GET["post_payment_fromdate"]:"";
$post_payment_todate = isset($_GET["post_payment_todate"])?$_GET["post_payment_todate"]:"";
$post_payment_added_date = isset($_GET["post_payment_added_date"])?$_GET["post_payment_added_date"]:"";

if($join_range != ""){
  if($join_range == "Range" && $lead_fromdate!='' && $lead_todate!=''){
    $sch_params[':lead_fromdate'] = date("Y-m-d",strtotime($lead_fromdate));
    $sch_params[':lead_todate'] = date("Y-m-d",strtotime($lead_todate));
    $incr.=" AND DATE(l.created_at) >= :lead_fromdate AND DATE(l.created_at) <= :lead_todate";
  }else if($join_range == "Exactly" && $lead_added_date!=''){
    $sch_params[':lead_added_date'] = date("Y-m-d",strtotime($lead_added_date));
    $incr.=" AND DATE(l.created_at) = :lead_added_date";
  }else if($join_range == "Before" && $lead_added_date!=''){
    $sch_params[':lead_added_date'] = date("Y-m-d",strtotime($lead_added_date));
    $incr.=" AND DATE(l.created_at) < :lead_added_date";
  }else if($join_range == "After" && $lead_added_date!=''){
    $sch_params[':lead_added_date'] = date("Y-m-d",strtotime($lead_added_date));
    $incr.=" AND DATE(l.created_at) > :lead_added_date";
  }
}

if($post_payment_range != ""){
  if($post_payment_range == "Range" && $post_payment_fromdate!='' && $post_payment_todate!=''){
    $sch_params[':post_payment_fromdate'] = date("Y-m-d",strtotime($post_payment_fromdate));
    $sch_params[':post_payment_todate'] = date("Y-m-d",strtotime($post_payment_todate));
    $incr.=" AND DATE(o.post_date) >= :post_payment_fromdate AND DATE(o.post_date) <= :post_payment_todate";
  }else if($post_payment_range == "Exactly" && $post_payment_added_date!=''){
    $sch_params[':post_payment_added_date'] = date("Y-m-d",strtotime($post_payment_added_date));
    $incr.=" AND DATE(o.post_date) = :post_payment_added_date";
  }else if($post_payment_range == "Before" && $post_payment_added_date!=''){
    $sch_params[':post_payment_added_date'] = date("Y-m-d",strtotime($post_payment_added_date));
    $incr.=" AND DATE(o.post_date) < :post_payment_added_date";
  }else if($post_payment_range == "After" && $post_payment_added_date!=''){
    $sch_params[':post_payment_added_date'] = date("Y-m-d",strtotime($post_payment_added_date));
    $incr.=" AND DATE(o.post_date) > :post_payment_added_date";
  }
  $incr.=" AND c.status='Post Payment'";
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
    'url' => 'pending_aae_listing.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = (checkIsset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed){

  if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'export_pending_aae') {
    if($lead_fromdate !='' && $lead_todate != '') {
          $no_days=0;
          $date1 = date_create($lead_fromdate);
          $date2 = date_create($lead_todate);
          $diff = date_diff($date1,$date2);
          $no_days = $diff->format("%a");
          
          if($no_days > 62) {
              echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
              exit();
          }
      }

      $job_id = add_export_request_api('EXCEL',$_SESSION['agents']['id'],'Agent',"Pending AAE","pendingAaeExport",$incr,$sch_params,array(),'pending_aae_export');
      $reportDownloadURL = $AWS_REPORTING_URL['pending_aae_export']."&job_id=".$job_id;
      echo json_encode(array("status"=>"success","message"=>"Your export request is added","reportDownloadURL" => $reportDownloadURL)); 
      exit;
  }

    

  try {

    $sql="SELECT COUNT(DISTINCT l.id) AS total_lead,
      COUNT(DISTINCT (CASE WHEN ((l.status = 'Working' AND c.status='Post Payment')) THEN l.id END)) AS post_payment,
      COUNT(DISTINCT (CASE WHEN ((l.status = 'Working' AND c.status!='Post Payment')) THEN l.id END)) AS pending_validation,
      COUNT(DISTINCT (CASE WHEN ((l.status = 'Abandoned')) THEN l.id END)) AS abandoned,
      COUNT(DISTINCT (CASE WHEN ((l.status = 'Converted')) THEN l.id END)) AS converted
              FROM leads l
              JOIN lead_quote_details ld on(ld.lead_id = l.id)
              JOIN customer c on(c.id = l.customer_id AND c.is_deleted='N')
              JOIN customer s ON (s.id = l.sponsor_id)
              JOIN customer_settings scs ON(s.id = scs.customer_id)
              JOIN orders o on(o.id = ld.order_ids AND o.status != 'Cancelled')        
              WHERE l.opt_in_type = 'Agent Assisted Enrollment' AND l.status in('Converted','Working','Abandoned')" . $incr;

    $counts = $pdo->selectOne($sql,$sch_params);
      
    $sql="SELECT l.*,o.grand_total as grand_total,count(DISTINCT od.product_id) as total_products,GROUP_CONCAT(od.product_name SEPARATOR 'separator') as products_name,o.status as order_status,o.post_date,o.id as o_id,o.future_payment,ld.id as lead_quote_id,c.status as mbrStatus,s.rep_id as spnsorRepId,CONCAT(s.fname,' ',s.lname) as sponsorName
              FROM leads l
              JOIN lead_quote_details ld on(ld.lead_id = l.id)
              JOIN customer c on(c.id = l.customer_id AND c.is_deleted='N')
              JOIN customer s ON (s.id = l.sponsor_id)
              JOIN customer_settings scs ON(s.id = scs.customer_id)
              JOIN orders o on(o.id = ld.order_ids AND o.status != 'Cancelled')
              JOIN order_details od on(od.order_id = o.id AND od.product_type!='Fees' AND od.is_deleted='N')
              WHERE l.opt_in_type = 'Agent Assisted Enrollment' AND l.status in('Converted','Working','Abandoned')" . $incr . "
              GROUP BY l.id ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);

      if ($total_rows > 0) {
          $emails = array();
          $phones = array();
          foreach ($fetch_rows as $row) {
              if(!empty($row['email'])) {
                  $emails[] = $row['email'];
              }
              if(!empty($row['cell_phone'])) {
                  $phones[] = '+1'.$row['cell_phone'];
              }
          }
          $emails = array_unique($emails);
          $emails = implode("','",$emails);
          $phones = array_unique($phones);
          $phones = implode("','",$phones);

         $tri_log_sql = "SELECT res.status,res.response,res.created_at,res.type,res.to_send
            FROM (
                SELECT eld.created_at,'Email' as type,eld.status,eld.response,e.to_email as to_send
                FROM email_log e
                LEFT JOIN email_log_details eld ON eld.id = (SELECT id FROM email_log_details WHERE log_id = e.id ORDER BY id DESC LIMIT 1)
                WHERE e.to_email IN('".$emails."') AND e.trigger_id = 84 GROUP BY e.id
            UNION 
                SELECT sld.created_at,'SMS' as type,sld.status,'' as response,s.to_number as to_send
                FROM sms_log s
                LEFT JOIN sms_log_details sld ON sld.id = (SELECT id FROM sms_log_details WHERE log_id = s.id ORDER BY id DESC LIMIT 1)
                WHERE s.to_number IN('".$phones."') AND s.trigger_id = 84 GROUP BY s.id
              ) AS res order BY res.created_at desc";
        $tri_log_res = $pdo->select($tri_log_sql);
        $email_trigger_log = array();
        $phone_trigger_log = array();
        if(!empty($tri_log_res)) {
            foreach ($tri_log_res as $value) {
                
                if($value['type'] == "Email") {
                    if(!isset($email_trigger_log[$value['to_send']])) {
                        $email_trigger_log[$value['to_send']] = $value;
                    }
                }
                if($value['type'] == "SMS") {
                    if(!isset($phone_trigger_log[$value['to_send']])) {
                        $phone_trigger_log[$value['to_send']] = $value;
                    }
                }
            }          
        }
      }
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
 
  include_once 'tmpl/pending_aae_listing.inc.php';
  exit;
}

$desc = array();
$desc['ac_message'] = array(
    'ac_red_1' => array(
        'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
        'title' => $_SESSION['agents']['rep_id'],
    ),
    'ac_message_1' => ' read Pending AAE Page'
);
$desc = json_encode($desc);
activity_feed(3, $_SESSION['agents']['id'], 'Agent', $_SESSION['agents']['id'], 'Agent', 'Agent Read Pending AAE Page.', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'pending_aae_listing.inc.php';
include_once 'layout/end.inc.php';
?>