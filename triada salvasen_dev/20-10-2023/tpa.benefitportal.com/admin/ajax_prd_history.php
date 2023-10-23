<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$sch_params = array();
$SortBy = "changed_at";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$admin_id = $_SESSION['admin']['id'];

$id=!empty($_POST['id']) ? $_POST['id'] : '';
$user_type=!empty($_POST['user_type']) ? $_POST['user_type'] : '';
$custom_date = isset($_POST['acc_his_custom_date']) ? $_POST['acc_his_custom_date'] : '';
$fromdate = isset($_POST["acc_his_fromdate"]) ? $_POST["acc_his_fromdate"] : '';
$todate = isset($_POST["acc_his_todate"]) ? $_POST["acc_his_todate"] : '';
$activity_type = isset($_POST["activity_type"]) ? $_POST["activity_type"] : '';
$keyword = checkIsset($_POST['keyword_search']);
$type=!empty($_POST['type']) ? $_POST['type'] : '';
$product_id = !empty($_POST['product']) ? getname('prd_main',$_POST['product'],'id','MD5(id)') : 0;

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
    }
}
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
if ($user_type != '') {
    $sch_params[':user_type'] = $user_type;
    $incr .= " AND user_type = :user_type";
}

if ($type != '' && $type !='all') {
    $sch_params[':extra'] = 'prd_'.$type;
    $incr .= " AND extra = :extra";
}


if($keyword!='')
{
    $key_incr.= " AND (entity_action like '%".$keyword."%'  OR description like '%".$keyword."%' OR note_admin_name like '%".$keyword."%')";
}

if($from_limit >0){
   $limit = getActivityFeedLimit()->nextLimit;
}
try {

    $limit_incr = ' LIMIT '.$from_limit.','.$limit;
    
    $sel_sql = "SELECT * FROM activity_feed WHERE  is_deleted ='N' AND entity_type='product' AND md5(entity_id)=:product_id  $incr  $key_incr ORDER BY $SortBy $currSortDirection $limit_incr";
    $sch_params[':product_id'] = $product_id;
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
