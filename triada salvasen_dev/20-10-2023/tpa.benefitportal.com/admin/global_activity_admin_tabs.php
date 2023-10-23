<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
//has_access(37);
// error_reporting(E_ALL);
$is_ajaxed_admins = isset($_REQUEST['is_ajaxed_admins']) ? $_REQUEST['is_ajaxed_admins'] : '';
$is_all_activity_popup = isset($_REQUEST['is_all_activity_popup']) ? $_REQUEST['is_all_activity_popup'] : '';
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$SortBy = "af.id";
$SortDirection = "ASC";
$currSortDirection = "DESC";


if($type=='Admin' && $is_all_activity_popup=='Y'){
	$join_range = isset($_REQUEST['join_range']) ? $_REQUEST['join_range'] : '';
	$join_date = isset($_REQUEST['join_date']) ? $_REQUEST['join_date'] : '';
	$activity_type = isset($_REQUEST['activity_type']) ? $_REQUEST['activity_type'] : '';
	$activity_by = isset($_REQUEST['activity_by']) ? $_REQUEST['activity_by'] : '';
	$date_from = isset($_REQUEST['date_from']) ? $_REQUEST['date_from'] : '';
	$to_date = isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : '';
	$impacted_name = isset($_REQUEST['impacted_name']) ? $_REQUEST['impacted_name'] : '';
	$ip_address = isset($_REQUEST['ip_address']) ? $_REQUEST['ip_address'] : '';
	$from_limit = isset($_REQUEST['from_limit']) ? $_REQUEST['from_limit'] : '0';
	$limit = getActivityFeedLimit()->limit;
	
	$tz = new UserTimeZone('m/d/Y g:i A T',$_SESSION['admin']['timezone']);
	$userTimeZone = $tz->defaultTimeZone;

	$has_querystring = false;
	$sch_params = array();
	$incr = isset($incr) ? $incr : '';
	$aincr = isset($aincr) ? $aincr : '';
	
	if($activity_type!="")
	{
		$sch_params[':activity_type'] = makeSafe($activity_type);
		$incr .=" AND af.entity_action = :activity_type ";
	}
	if($activity_by != "")
	{
		$sch_params[':activity_by'] = makeSafe($activity_by);
		$incr .=" AND af.user_id = :activity_by";
	}
	if($impacted_name!="")
	{
		$sch_params[':impacted_name'] = makeSafe($impacted_name);
		$aincr .=" AND af.entity_id = :impacted_name AND af.entity_type IN('admin','Agent','customer','Group','Lead','leads')";
	}
	if($ip_address!="")
	{
		$sch_params[':ip_address'] = makeSafe($ip_address);
		$incr .=" AND af.ip_address = :ip_address ";
	}
	if($join_range!="" && !empty($join_range)){
	
		if($join_range=="range")
		{	
			if($date_from!="")
			{
				$sch_params[":date_from"] = date("Y-m-d",strtotime($date_from));
				$incr .=" AND DATE(CONVERT_TZ(af.changed_at,'+00:00','".$userTimeZone."')) >= :date_from ";
			}
			if($to_date!="")
			{
				$sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
				$incr .=" AND DATE(CONVERT_TZ(af.changed_at,'+00:00','".$userTimeZone."')) <= :to_date ";
			}
		}else if($join_range=="exactly")
		{
			$sch_params[":exactly"] = date("Y-m-d",strtotime($join_date));
			$incr .=" AND DATE(CONVERT_TZ(af.changed_at,'+00:00','".$userTimeZone."')) = :exactly ";
		}else if($join_range=="before")
		{
			$sch_params[":before1"] = date("Y-m-d",strtotime($join_date));
			$incr .=" AND DATE(CONVERT_TZ(af.changed_at,'+00:00','".$userTimeZone."')) <= :before1 ";
		}else if($join_range=="after")
		{
			$sch_params[":after1"] = date("Y-m-d",strtotime($join_date));
			$incr .=" AND DATE(CONVERT_TZ(af.changed_at,'+00:00','".$userTimeZone."')) >= :after1 ";		
		}
	}
	
	if($from_limit >0){
		$limit = getActivityFeedLimit()->nextLimit;
	 }
	try {
		
		$limit_incr = ' LIMIT '.$from_limit.','.$limit;
	
		$sel_sql = "SELECT af.* FROM activity_feed af 
		JOIN admin a ON ((a.id=af.user_id AND af.user_type='Admin') OR (a.id=af.entity_id AND af.entity_type='Admin')) 
		WHERE af.is_deleted ='N' $incr $aincr ORDER BY $SortBy $currSortDirection $limit_incr";
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
	exit;

}
$activity_type_res = array();
if(!$is_ajaxed_admins){
	$activity_type_res_user = $rpdo->select("SELECT DISTINCT entity_action FROM activity_feed WHERE user_type='admin'");
	$activity_type_res_ent = $rpdo->select("SELECT DISTINCT entity_action FROM activity_feed WHERE entity_type='admin'");
    $activity_type_res = array_merge($activity_type_res_user,$activity_type_res_ent);
}

include_once 'tmpl/global_activity_admin_tabs.inc.php';
?>