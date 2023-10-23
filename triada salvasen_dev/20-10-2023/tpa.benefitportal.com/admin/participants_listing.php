<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/participants.class.php';
has_access(89);
$participantsObj = new Participants();

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'User Group';
$breadcrumbes[2]['title'] = 'Participants';
$breadcrumbes[2]['link'] = 'javascript:void(0)';
$curr_page_url = '';

$participants_tag_res = $participantsObj->get_participants_tags();
$sch_params = array();
$incr = '';
$SortBy = "p.created_at";
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

$is_ajaxed = checkIsset($_GET['is_ajaxed']);
$participants_id = checkIsset($_GET['participants_id']);
$employee_id = checkIsset($_GET['employee_id']);
$participants_name = checkIsset($_GET['participants_name']);
$email = checkIsset($_GET['email']);
$phone = checkIsset($_GET['phone']);
$participants_type = checkIsset($_GET['participants_type']);

$participants_tag = checkIsset($_GET['participants_tag'],'arr');
$participants_status = checkIsset($_GET['participants_status'],'arr');

$join_range = isset($_GET['join_range']) ? $_GET['join_range'] : "";
$fromdate = isset($_GET["fromdate"]) ? $_GET["fromdate"] : "";
$todate = isset($_GET["todate"]) ? $_GET["todate"] : "";
$added_date = isset($_GET["added_date"]) ? $_GET["added_date"] : "";

$participants_id = cleanSearchKeyword($participants_id);
$email = cleanSearchKeyword($email);
$phone = cleanSearchKeyword($phone);
$participants_name = cleanSearchKeyword($participants_name);
$employee_id = cleanSearchKeyword($employee_id); 
 
if (!empty($participants_id)) {
    $sch_params[':participants_id'] = "%" . makeSafe($participants_id) . "%";
    $incr .= " AND p.participants_id LIKE :participants_id";
}

if (!empty($employee_id)) {
    $sch_params[':employee_id'] = "%" . makeSafe($employee_id) . "%";
    $incr .= " AND p.employee_id LIKE :employee_id";
}

if (!empty($participants_name)) {
    $sch_params[':participants_name'] = "%" . makeSafe($participants_name) . "%";
    $incr .= " AND (p.fname LIKE :participants_name OR p.lname LIKE :participants_name OR CONCAT(p.fname,' ',p.lname) LIKE :participants_name)";
}

if (!empty($email)) {
    $sch_params[':email'] = "%" . makeSafe($email) . "%";
    $incr .= " AND p.email LIKE :email";
}

if (!empty($phone)) {
    $sch_params[':phone'] = "%" . makeSafe($phone) . "%";
    $incr .= " AND p.cell_phone LIKE :phone";
}

if (!empty($participants_type)) {
    $sch_params[':participants_type'] = "%" . makeSafe($participants_type) . "%";
    $incr .= " AND p.participants_type LIKE :participants_type";
}

if(!empty($participants_status)) {
	$participants_status = "'" . implode("','", makeSafe($participants_status)) . "'";
    $incr .= " AND p.status IN ($participants_status)";
}

if (!empty($participants_tag)) {
    $participants_tag = "'" . implode("','", makeSafe($participants_tag)) . "'";
    $incr .= " AND p.participants_tag IN ($participants_tag)";
}

$getfromdate = '';
$gettodate = '';
if ($join_range != "") {
    if ($join_range == "Range" && !empty($fromdate) && !empty($todate)) {
        $sch_params[':fromdate'] = date("Y-m-d", strtotime($fromdate));
        $sch_params[':todate'] = date("Y-m-d", strtotime($todate));
        $incr .= " AND DATE(p.created_at) >= :fromdate AND DATE(p.created_at) <= :todate";
        $getfromdate = $fromdate;
        $gettodate = $todate;
    } else if ($join_range == "Exactly" && !empty($added_date)) {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $getfromdate = $added_date;
        $gettodate = $added_date;
        $incr .= " AND DATE(p.created_at) = :added_date";
    } else if ($join_range == "Before" && !empty($added_date)) {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(p.created_at) < :added_date";
        $getfromdate = $added_date;
        $gettodate = date('Y-m-d');
    } else if ($join_range == "After" && !empty($added_date)) {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(p.created_at) > :added_date";
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
    'url' => 'participants_listing.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';

if ($is_ajaxed) {
    
    if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'export_participants'){

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
        $job_id=add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Participants Summary","participants_summary",$incr, $sch_params,'','participants_summary');
        $reportDownloadURL = $AWS_REPORTING_URL['participants_summary']."&job_id=".$job_id;

        $description = array();
        $description['ac_message'] = array(
            'ac_red_1' => array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' => ' Created participants export file',
        );
        $desc = json_encode($description);
        activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'], 'Admin', 'Created Participants Export File', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);

        // $ch = curl_init($reportDownloadURL);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        // curl_setopt($ch, CURLOPT_POST, false);
        // curl_exec($ch);
        // $apiResponse = curl_exec($ch);
        // curl_close($ch);
    
        echo json_encode(array("status"=>"success","message"=>"Your export request is added")); 
        exit;
    }

    try {
        $sel_sql = "SELECT md5(p.id) as id,p.id as pid,p.participants_id,CONCAT(p.fname,' ',p.lname) as name,
                    p.created_at as addedDate,p.cell_phone,p.email,p.participants_type,p.participants_tag,
                    p.status, md5(a.id) as adminId,a.display_id as adminDispId,
                    CONCAT(a.fname,' ',a.lname) as adminName
                   FROM participants p
                   LEFT JOIN admin a ON(a.id=p.admin_id)
                   WHERE p.is_deleted = 'N' " . $incr . " 
                   ORDER BY  $SortBy $currSortDirection";
        $paginate = new pagination($page, $sel_sql, $options);

        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }
    
    include_once 'tmpl/participants_listing.inc.php';
    exit;
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);
$template = 'participants_listing.inc.php';
include_once 'layout/end.inc.php';
?>