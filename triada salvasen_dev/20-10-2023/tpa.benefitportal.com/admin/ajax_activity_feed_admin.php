<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
$sch_params = array();
$SortBy = "changed_at";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$admin_id = $_SESSION['admin']['id'];

$id = $_REQUEST['id'];
$real_id = getname('admin',$id,'id','MD5(id)');
$user_type = $_REQUEST['user_type'];
$custom_date = isset($_POST['acc_his_custom_date']) ? $_POST['acc_his_custom_date'] : '';
$fromdate = isset($_POST["acc_his_fromdate"]) ? $_POST["acc_his_fromdate"] : '';
$todate = isset($_POST["acc_his_todate"]) ? $_POST["acc_his_todate"] : '';
$activity_type = isset($_POST["activity_type"]) ? $_POST["activity_type"] : '';
$keyword = checkIsset($_POST['keyword_search']);

$from_limit = isset($_REQUEST['from_limit']) ? $_REQUEST['from_limit'] : '0';

$limit = getActivityFeedLimit()->limit;
$tz = new UserTimeZone('m/d/Y g:i A T',$_SESSION['admin']['timezone']);
$userTimeZone = $tz->defaultTimeZone;

$incr = "";
$key_incr = "";

if($custom_date =="Range"){
if ($fromdate != "") {
    $sch_params[':from_date'] = date('Y-m-d', strtotime($fromdate));
    $incr .= " AND DATE(CONVERT_TZ(changed_at,'+00:00','".$userTimeZone."')) >= :from_date";
}
if ($todate != "") {
    $sch_params[':to_date'] = date('Y-m-d', strtotime($todate));
    $incr .= " AND DATE(CONVERT_TZ(changed_at,'+00:00','".$userTimeZone."')) <= :to_date";
}}
if ($fromdate != "") {
    if($custom_date == 'exactly')
    {
        $sch_params[':from_date'] = date('Y-m-d', strtotime($fromdate));
        $incr .= " AND DATE(CONVERT_TZ(changed_at,'+00:00','".$userTimeZone."')) = :from_date";

    }else if($custom_date == 'before')
    {
        $sch_params[':from_date'] = date('Y-m-d', strtotime($fromdate));
        $incr .= " AND DATE(CONVERT_TZ(changed_at,'+00:00','".$userTimeZone."')) < :from_date";
    }else if($custom_date == 'after')
    {
        $sch_params[':from_date'] = date('Y-m-d', strtotime($fromdate));
        $incr .= " AND DATE(CONVERT_TZ(changed_at,'+00:00','".$userTimeZone."')) > :from_date";
    }
}
if ($activity_type != '') {
    $sch_params[':entity_action'] = $activity_type;
    $incr .= " AND entity_action = :entity_action";
}

$keyword = cleanSearchKeyword($keyword); 
 
if($keyword!='')
{
    $key_incr.= " AND ( entity_action like '%$keyword%' OR description like '%$keyword%' OR note_admin_name like '%$keyword%' )  ";
}

if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'export_activity') {
    if($fromdate !='' && $todate != '') {
        $no_days=0;
        if($fromdate != '' && $todate != '') {
            $date1 = date_create($fromdate);
            $date2 = date_create($todate);
            $diff = date_diff($date1,$date2);
            $no_days = $diff->format("%a");
        }
        
        if($no_days > 62) {
            echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
            exit();
        }
    }

    $tmp_incr = $incr . $key_incr . ' AND md5(user_id)=:id';
    $sch_params[':id'] = $id;

    $extra_params = array('timezone' => $_SESSION['admin']['timezone']);
    $extra_params['activit_feed']['entity_id'] = getname('admin',$id,'id','md5(id)');
    $extra_params['activit_feed']['entity_type'] = 'Admin';
    $job_id = add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Admin History","admin_history",$tmp_incr,$sch_params,$extra_params,'admin_history');
    $reportDownloadURL = $AWS_REPORTING_URL['admin_history']."&job_id=".$job_id;
    echo json_encode(array("status"=>"success","message"=>"Your export request is added","reportDownloadURL" => $reportDownloadURL)); 
    exit;
}

if($from_limit >0){
   $limit = getActivityFeedLimit()->nextLimit;
}
try {

    $limit_incr = ' LIMIT '.$from_limit.','.$limit;

    $sel_sql = "SELECT * FROM activity_feed WHERE  entity_action!='New Order' AND is_deleted ='N' AND user_type='Admin' AND user_id=:id $incr  $key_incr AND is_deleted='N' ORDER BY $SortBy $currSortDirection $limit_incr";
    $sch_params[':id'] = $real_id;
    $fetch_rows = $rpdo->select($sel_sql, $sch_params);
    $total_rows = count($fetch_rows);

    if($total_rows > 0){
        $from_limit = $from_limit + $total_rows;
    }else{
        $from_limit = 0;
    }

} catch (Exception $e) {
    echo $e;
    exit();
}

include_once 'tmpl/activity_history_general.inc.php';
?>
