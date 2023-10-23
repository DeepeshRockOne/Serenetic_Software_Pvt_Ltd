<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$sch_params = array();
$SortBy = "af.id";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$id = $_REQUEST['id'];
$user_type = $_REQUEST['user_type'];
$custom_date = isset($_POST['acc_his_custom_date']) ? $_POST['acc_his_custom_date'] : '';
$fromdate = isset($_POST["acc_his_fromdate"]) ? $_POST["acc_his_fromdate"] : '';
$todate = isset($_POST["acc_his_todate"]) ? $_POST["acc_his_todate"] : '';
$activity_type = isset($_POST["activity_type"]) ? $_POST["activity_type"] : '';
$keyword = checkIsset($_POST['keyword_search']);

$maxid = isset($_REQUEST['maxid']) ? $_REQUEST['maxid'] : '';

$limit = getActivityFeedLimit()->limit;
$tz = new UserTimeZone('m/d/Y g:i A T',$_SESSION['agents']['timezone']);
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
// if ($user_type != '') {
//     $sch_params[':user_type'] = $user_type;
//     $incr .= " AND user_type = :user_type";
// }

if($keyword!='')
{
    $key_incr.= " AND (entity_action like '%$keyword%' OR description like '%$keyword%') ";
}

if($maxid > 0){
   $sch_params[':mxid'] = $maxid;
   $incr .= " AND af.id < :mxid";

   $limit = getActivityFeedLimit()->nextLimit;
}
try {

    $sel = "SELECT count(*) as num_rows FROM activity_feed af 
            LEFT JOIN customer c on ((c.id=af.entity_id AND af.entity_type='Customer') OR (c.id=af.user_id AND af.user_type='Customer')) 
            WHERE 
            af.entity_action!='New Order' AND 
            ((md5(user_id)=:id AND af.user_type='Customer') OR (md5(entity_id)=:id AND af.entity_type='Customer')) AND
            af.is_deleted ='N' $incr $key_incr 
            GROUP BY af.id  ORDER BY $SortBy $currSortDirection LIMIT $limit";
    $sch_params[':id'] = $id;
    $resp = $pdo->selectOne($sel,$sch_params);
    if(!empty($resp['num_rows'])) {
        $allNumRows = $resp['num_rows'];
    } else {
        $allNumRows = 0;    
    }
    
    if($allNumRows < 1){
        unset($maxid);
        unset($minmum);
    }
    $sel_sql = "SELECT af.* FROM activity_feed af 
                LEFT JOIN customer c on ((c.id=af.entity_id AND af.entity_type='Customer') OR (c.id=af.user_id AND af.user_type='Customer')) 
                WHERE 
                af.entity_action!='New Order' AND 
                ((md5(user_id)=:id AND af.user_type='Customer') OR (md5(entity_id)=:id AND af.entity_type='Customer')) AND
                af.is_deleted ='N' $incr $key_incr 
                GROUP BY af.id  ORDER BY $SortBy $currSortDirection LIMIT $limit";
    $fetch_rows = $pdo->select($sel_sql, $sch_params);
    $total_rows = count($fetch_rows);

    $arr = array();
    foreach ($fetch_rows as $value) {
        $arr[] = $value['id'];
        $minmum = (min($arr));
    }
} catch (Exception $e) {
    echo $e;
    exit();
}

include_once 'tmpl/activity_history_general.inc.php';
?>
