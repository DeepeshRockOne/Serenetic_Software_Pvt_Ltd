<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$sch_params = array();
$SortBy = "changed_at";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$id = $_REQUEST['id'];
$real_id = getname('leads',$id,'id','MD5(id)');
$user_type = $_REQUEST['user_type'];
$custom_date = isset($_POST['acc_his_custom_date']) ? $_POST['acc_his_custom_date'] : '';
$fromdate = isset($_POST["acc_his_fromdate"]) ? $_POST["acc_his_fromdate"] : '';
$todate = isset($_POST["acc_his_todate"]) ? $_POST["acc_his_todate"] : '';
$activity_type = isset($_POST["activity_type"]) ? $_POST["activity_type"] : '';
$keyword = checkIsset($_POST['keyword_search']);

$from_limit = isset($_REQUEST['from_limit']) ? $_REQUEST['from_limit'] : '0';

$limit = getActivityFeedLimit()->limit;
$tz = new UserTimeZone('m/d/Y g:i A T',$_SESSION['groups']['timezone']);
$userTimeZone = $tz->defaultTimeZone;

$incr = "";
$key_incr = "";

$keyword = cleanSearchKeyword($keyword); 
 
if($custom_date =="Range"){
if ($fromdate != "") {
    $sch_params[':from_date'] = date('Y-m-d', strtotime($fromdate));
    $incr .= " AND DATE(CONVERT_TZ(changed_at,'+00:00','".$userTimeZone."')) >= :from_date";
}
if ($todate != "") {
    $sch_params[':to_date'] = date('Y-m-d', strtotime($todate));
    $incr .= " AND DATE(CONVERT_TZ(changed_at,'+00:00','".$userTimeZone."')) <= :to_date";
}}else
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

if($keyword!='')
{
    $key_incr.= " AND (entity_action like '%$keyword%' OR description like '%$keyword%') ";
}

if($from_limit > 0){
   $limit = getActivityFeedLimit()->nextLimit;
}
try {
    $limit_incr = ' LIMIT '.$from_limit.','.$limit;

    $sel_sql = "SELECT af.* FROM activity_feed af
                WHERE 
                af.entity_action!='New Order' AND 
                ((user_id=:id AND af.user_type='Lead') OR (entity_id=:id AND af.entity_type='Lead')) AND
                af.is_deleted ='N' $incr $key_incr 
                GROUP BY af.id  ORDER BY $SortBy $currSortDirection $limit_incr";
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
