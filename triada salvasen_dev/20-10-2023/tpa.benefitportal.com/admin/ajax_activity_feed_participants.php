<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$sch_params = array();
$SortBy = "af.changed_at";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$id = $_REQUEST['id'];
$id_org = getname('participants', $id, 'id', "md5(id)");
$user_type = $_REQUEST['user_type'];
$custom_date = isset($_POST['acc_his_custom_date']) ? $_POST['acc_his_custom_date'] : '';
$fromdate = isset($_POST["acc_his_fromdate"]) ? $_POST["acc_his_fromdate"] : '';
$todate = isset($_POST["acc_his_todate"]) ? $_POST["acc_his_todate"] : '';
$activity_type = isset($_POST["activity_type"]) ? $_POST["activity_type"] : '';
$keyword = checkIsset($_POST['keyword_search']);

$maxid = isset($_REQUEST['maxid']) ? $_REQUEST['maxid'] : '';

$limit = getActivityFeedLimit()->limit;
$tz = new UserTimeZone('m/d/Y g:i A T',$_SESSION['admin']['timezone']);
$userTimeZone = $tz->defaultTimeZone;

$incr = "";
$key_incr = "";
$keyword = cleanSearchKeyword($keyword); 
 
if($custom_date =="Range"){
if ($fromdate != "") {
    $sch_params[':from_date'] = date('Y-m-d', strtotime($fromdate));
    $incr .= " AND DATE(CONVERT_TZ(af.changed_at,'+00:00','".$userTimeZone."')) >= :from_date";
}
if ($todate != "") {
    $sch_params[':to_date'] = date('Y-m-d', strtotime($todate));
    $incr .= " AND DATE(CONVERT_TZ(af.changed_at,'+00:00','".$userTimeZone."')) <= :to_date";
}}else
if ($fromdate != "") {
    if($custom_date == 'exactly')
    {
        $sch_params[':from_date'] = date('Y-m-d', strtotime($fromdate));
        $incr .= " AND DATE(CONVERT_TZ(af.changed_at,'+00:00','".$userTimeZone."')) = :from_date";

    }else if($custom_date == 'before')
    {
        $sch_params[':from_date'] = date('Y-m-d', strtotime($fromdate));
        $incr .= " AND DATE(CONVERT_TZ(af.changed_at,'+00:00','".$userTimeZone."')) < :from_date";
    }else if($custom_date == 'after')
    {
        $sch_params[':from_date'] = date('Y-m-d', strtotime($fromdate));
        $incr .= " AND DATE(CONVERT_TZ(af.changed_at,'+00:00','".$userTimeZone."')) > :from_date";
    }
}

if (!empty($activity_type)) {
    $sch_params[':entity_action'] = $activity_type;
    $incr .= " AND af.entity_action = :entity_action";
}
if (!empty($keyword)) {
    $key_incr.= " AND (af.entity_action like '%$keyword%' OR af.description like '%$keyword%') ";
}

if($maxid > 0){
   $sch_params[':mxid'] = $maxid;
   $incr .= " AND af.id < :mxid";
   $limit = getActivityFeedLimit()->nextLimit;
}
try {
    $sel = "SELECT count(DISTINCT af.id) as num_rows 
        FROM activity_feed af
        JOIN participants p ON ((af.entity_type='Participants' AND p.id=af.entity_id) OR (af.user_type='Participants' AND p.id=af.user_id)) 
        WHERE 
        af.is_deleted ='N' AND
        ((af.user_type='Participants' AND af.user_id=:id) OR (af.entity_type='Participants' AND af.entity_id=:id))
        $incr $key_incr";
    $sch_params[':id'] = $id_org;
    $resp = $pdo->selectOne($sel,$sch_params);

    $allNumRows = $resp['num_rows'];
    if($allNumRows < 1){
        unset($maxid);
        unset($minmum);
    }

    $sel_sql = "SELECT af.user_type,af.entity_action,af.changed_at,af.description,af.extra,af.id,af.ip_address 
                FROM activity_feed af 
                JOIN participants p ON ((af.entity_type='Participants' AND p.id=af.entity_id) OR (af.user_type='Participants' AND p.id=af.user_id)) 
                WHERE 
                af.is_deleted ='N' AND
                ((af.user_type='Participants' AND af.user_id=:id) OR (af.entity_type='Participants' AND af.entity_id=:id)) 
                $incr $key_incr 
                GROUP BY af.id ORDER BY $SortBy $currSortDirection LIMIT $limit";

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