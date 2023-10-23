<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'User Group';
$breadcrumbes[2]['title'] = 'Leads';
$breadcrumbes[2]['link'] = 'javascript:void(0)';
$curr_page_url = '';
$lead_tag_res = get_lead_tags(0,'Admin');

$sch_params = array();
$incr = '';
$SortBy = "l.created_at";
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
$lead_id = !empty($_GET['lead_id']) ? explode(",", $_GET['lead_id']) : "";
// $lead_name = isset($_GET['lead_name']) ? $_GET["lead_name"] : '';
$email = isset($_GET['email']) ? $_GET["email"] : '';
$phone = isset($_GET['phone']) ? $_GET["phone"] : '';
$lead_type = isset($_GET['lead_type']) ? $_GET["lead_type"] : '';
$lead_tag = isset($_GET['lead_tag']) ? $_GET["lead_tag"] : array();
$leads_status = isset($_GET['leads_status']) ? $_GET['leads_status'] : array();

$join_range = isset($_GET['join_range']) ? $_GET['join_range'] : "";
$fromdate = isset($_GET["fromdate"]) ? $_GET["fromdate"] : "";
$todate = isset($_GET["todate"]) ? $_GET["todate"] : "";
$added_date = isset($_GET["added_date"]) ? $_GET["added_date"] : "";

if (!empty($lead_id)) {
    $lead_id = "'" . implode("','", makeSafe($lead_id)) . "'";
    $incr .= " AND l.id IN ($lead_id)";
}

// if ($lead_name) {
//     $sch_params[':lead_name'] = "%" . makeSafe($lead_name) . "%";
//     $incr .= " AND (l.fname LIKE :lead_name OR l.lname LIKE :lead_name OR CONCAT(l.fname,' ',l.lname) LIKE :lead_name)";
// }

$email = cleanSearchKeyword($email);
$phone = cleanSearchKeyword($phone); 
 
if ($email != "") {
    $sch_params[':email'] = "%" . makeSafe($email) . "%";
    $incr .= " AND l.email LIKE :email";
}

if ($phone != "") {
    $sch_params[':phone'] = "%" . makeSafe($phone) . "%";
    $incr .= " AND l.cell_phone LIKE :phone";
}

if ($lead_type != "") {
    $sch_params[':lead_type'] = "%" . makeSafe($lead_type) . "%";
    $incr .= " AND l.lead_type LIKE :lead_type";
}

if(!empty($leads_status)) {
	$leads_status = "'" . implode("','", makeSafe($leads_status)) . "'";
    $incr .= " AND l.status IN ($leads_status)";
}

if (!empty($lead_tag)) {
    $lead_tag = "'" . implode("','", makeSafe($lead_tag)) . "'";
    $incr .= " AND l.opt_in_type IN ($lead_tag)";
}
$getfromdate = '';
$gettodate = '';
if ($join_range != "") {
    if ($join_range == "Range" && $fromdate != '' && $todate != '') {
        $sch_params[':fromdate'] = date("Y-m-d", strtotime($fromdate));
        $sch_params[':todate'] = date("Y-m-d", strtotime($todate));
        $incr .= " AND DATE(l.created_at) >= :fromdate AND DATE(l.created_at) <= :todate";
        $getfromdate = $fromdate;
        $gettodate = $todate;
    } else if ($join_range == "Exactly" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $getfromdate = $added_date;
        $gettodate = $added_date;
        $incr .= " AND DATE(l.created_at) = :added_date";
    } else if ($join_range == "Before" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(l.created_at) < :added_date";
        $getfromdate = $added_date;
        $gettodate = date('Y-m-d');
    } else if ($join_range == "After" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(l.created_at) > :added_date";
        $getfromdate = date('Y-m-d');
        $gettodate = $added_date;
    }
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
    'url' => 'lead_listing.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';

if ($is_ajaxed) {
    
    if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'export_lead'){

        include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
       
        if($getfromdate!='' && $gettodate != '') {

            $no_days=0;
            if($getfromdate!= '' && $gettodate!='') {
                $date1 = date_create($getfromdate);
                $date2 = date_create($gettodate);
                $diff = date_diff($date1,$date2);
                $no_days=$diff->format("%a");
            }
            
            if($no_days>62) {
                echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
                exit();
            }
        }
        $job_id=add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Lead Summary","lead_summary",$incr, $sch_params,array(),'lead_summary');

        $description = array();
        $description['ac_message'] = array(
            'ac_red_1' => array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' => ' Created lead export file',
        );
        $desc = json_encode($description);
        activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'], 'Admin', 'Created Lead Export File', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);

        echo json_encode(array("status"=>"success","message"=>"Your export request is added")); 
        exit;
    }

    try {
        $sel_sql = "SELECT l.*,md5(l.id) as id,s.rep_id as sponsor_rep_id,CONCAT(s.fname,' ',s.lname) as sponsor_name,s.business_name,s.type as sponsor_type
                  FROM leads l
                  JOIN customer s ON (s.id = l.sponsor_id)
                  WHERE l.is_deleted = 'N' " . $incr . " 
				  ORDER BY  $SortBy $currSortDirection";
        $paginate = new pagination($page, $sel_sql, $options);

        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
            $link_array = !empty($paginate->links_array['links'])?$paginate->links_array['links']:array();

            if (count($link_array) > 0) {
                foreach ($link_array as $value) {
                    foreach ($value as $key => $val) {
                        if ($val['is_current_page'] != 0) {
                            $curr_page_url = $val['link_url'];
                        }
                    }
                }
            }
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }

    $agent_res = get_active_agents_for_select();
    
    include_once 'tmpl/lead_listing.inc.php';
    exit;
}
$selectize = true;
// $lead_sql = "SELECT id,lead_id,fname,lname FROM leads  WHERE is_deleted = 'N'";
// $lead_res = $pdo->select($lead_sql);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);
$template = 'lead_listing.inc.php';
include_once 'layout/end.inc.php';
?>