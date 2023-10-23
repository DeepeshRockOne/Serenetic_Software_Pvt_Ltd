<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/chat.class.php';
$LiveChat = new LiveChat();
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Client Support";
$breadcrumbes[2]['title'] = "Live Chat";
$breadcrumbes[2]['link'] = 'live_chat_dashboard.php';

$sch_params = array();
$incr = '';
$SortBy = "co.creation_time";
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
$display_id = isset($_GET['display_id']) ? $_GET['display_id'] : '';
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : '';
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$assigned_admin_id = isset($_GET['assigned_admin_id']) ? $_GET['assigned_admin_id'] : '';
$department = isset($_GET['department']) ? $_GET['department'] : array();
$status_code = isset($_GET['status_code']) ? $_GET['status_code'] : array();

$join_range = isset($_GET['join_range']) ? $_GET['join_range'] : "";
$fromdate = isset($_GET["fromdate"]) ? $_GET["fromdate"] : "";
$todate = isset($_GET["todate"]) ? $_GET["todate"] : "";
$added_date = isset($_GET["added_date"]) ? $_GET["added_date"] : "";

$user_id = cleanSearchKeyword($user_id); 
 
if (!empty($display_id)) {
    $display_id = explode(',',trim($display_id));
    $display_id = array_map('trim', $display_id);
    $display_id = "'" . implode("','", makeSafe($display_id)) . "'";
    $incr .= " AND co.display_id IN ($display_id)";
}

if (!empty($user_type)) {
    $sch_params[':user_type'] = $user_type;
    $incr .= " AND u.app_user_type=:user_type";
}

if (!empty($user_id)) {
    $userId = explode(',',trim($user_id));
    $userId = array_map('trim', $userId);
    $userId = "'" . implode("','", makeSafe($userId)) . "'";
    $incr .= " AND (u.id IN (".$userId.") OR c.rep_id IN(".$userId."))";
}

if (!empty($assigned_admin_id)) {
    $sch_params[':assigned_admin_id'] = $assigned_admin_id;
    $incr .= " AND a.id=:assigned_admin_id";
}

if (!empty($department)) {
    $department = "'" . implode("','", makeSafe($department)) . "'";
    $incr .= " AND co.department IN ($department)";
}

if (!empty($status_code)) {
    $status_code = "'" . implode("','", makeSafe($status_code)) . "'";
    $incr .= " AND co.status_code IN ($status_code)";
}

if ($join_range != "") {
    if ($join_range == "Range" && $fromdate != '' && $todate != '') {
        $sch_params[':fromdate'] = date("Y-m-d", strtotime($fromdate));
        $sch_params[':todate'] = date("Y-m-d", strtotime($todate));
        $incr .= " AND DATE(co.creation_time) >= :fromdate AND DATE(co.creation_time) <= :todate";
    } else if ($join_range == "Exactly" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(co.creation_time) = :added_date";
    } else if ($join_range == "Before" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(co.creation_time) < :added_date";
    } else if ($join_range == "After" && $added_date != '') {
        $sch_params[':added_date'] = date("Y-m-d", strtotime($added_date));
        $incr .= " AND DATE(co.creation_time) > :added_date";
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
    'url' => 'live_chat_dashboard.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';

if ($is_ajaxed) {
	try {
        $sel_sql = "SELECT co.id,co.display_id,co.creation_time,co.creation_time,u.id as user_rep_id,CONCAT(u.first_name,' ',u.last_name) as user_name,a.display_id as admin_rep_id,CONCAT(a.fname,' ',a.lname) as admin_name,u.user_type,co.department,co.status_code,co.user_id,au.app_user_id,
            IFNULL(c.rep_id,u.id) as userDispId,IFNULL(CONCAT(c.fname,' ',c.lname),CONCAT(u.first_name,' ',u.last_name)) as userName,u.app_user_type as userType
                  FROM $LIVE_CHAT_DB.sb_conversations co
                  JOIN $LIVE_CHAT_DB.sb_users u ON (u.id = co.user_id)
                  LEFT JOIN $LIVE_CHAT_DB.sb_users au ON (au.id = co.initial_assign_id)
                  LEFT JOIN admin a ON (a.id = au.app_user_id)
                  LEFT JOIN customer c ON(c.id=u.app_user_id)
                  WHERE 1 " . $incr . "
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
    $lc_departments = $LiveChat->get_departments();

    include_once 'tmpl/live_chat_dashboard.inc.php';
    exit;
}

//pre_print($is_ajaxed);
$online_admins = $LiveChat->get_online_admins();
$idle_admins = $LiveChat->get_idle_admins();
$live_conversations = $LiveChat->get_live_conversations();
$in_queue_conversations = $LiveChat->get_in_queue_conversations();
$served_conversations = $LiveChat->get_served_conversations();
$lc_departments = $LiveChat->get_departments_by_name();



/*--- Activity Feed -----*/
$desc = array();
$desc['ac_message'] =array(
    'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>'  read Live Chat Page',
);
$desc=json_encode($desc);
activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'admin','Admin Read Live Chat Page',$_SESSION['admin']['name'],"",$desc);
/*---/Activity Feed -----*/

$exJs = array(
'thirdparty/highcharts/js/highcharts.js',
'thirdparty/highcharts/js/accessibility.js',
);
$template = 'live_chat_dashboard.inc.php';
include_once 'layout/end.inc.php';
?>