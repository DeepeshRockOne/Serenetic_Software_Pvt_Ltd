<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
//has_access(37);
$SortBy = "af.changed_at";
$SortDirection = "ASC";
$currSortDirection = "DESC";
$is_ajaxed_agents = isset($_REQUEST['is_ajaxed_agents']) ? $_REQUEST['is_ajaxed_agents'] : '';
$is_all_activity_popup = isset($_REQUEST['is_all_activity_popup']) ? $_REQUEST['is_all_activity_popup'] : '';
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

if($type=='Agent' && $is_all_activity_popup=='Y')
{	
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
	
	$sch_params = array();
	$incr = isset($incr) ? $incr : '';
	
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
		$incr .=" AND af.entity_id = :impacted_name AND af.entity_type IN('admin','Agent','customer','Group','Lead','leads')";
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
				$incr .=" AND DATE(af.changed_at) >= :date_from ";
			}
			if($to_date!="")
			{
				$sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
				$incr .=" AND DATE(af.changed_at) <= :to_date ";
			}
		}else if($join_range=="exactly")
		{
			$sch_params[":exactly"] = date("Y-m-d",strtotime($join_date));
			$incr .=" AND DATE(af.changed_at) = :exactly ";
		}else if($join_range=="before")
		{
			$sch_params[":before1"] = date("Y-m-d",strtotime($join_date));
			$incr .=" AND DATE(af.changed_at) <= :before1 ";
		}else if($join_range=="after")
		{
			$sch_params[":after1"] = date("Y-m-d",strtotime($join_date));
			$incr .=" AND DATE(af.changed_at) >= :after1 ";		
		}
	}

	if($from_limit > 0){
		$limit = getActivityFeedLimit()->nextLimit;
	 }
	
	try {
	
		$limit_incr = ' LIMIT '.$from_limit.','.$limit;

		$sel_sql = "SELECT af.* FROM activity_feed af 
		JOIN customer c ON (c.type='Agent' AND ((c.id=af.entity_id AND af.entity_type='Agent') OR (c.id=af.user_id AND af.user_type='Agent'))) 
		WHERE af.is_deleted ='N' $incr ORDER BY $SortBy $currSortDirection $limit_incr";
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
if(!$is_ajaxed_agents)
{
	$sel_activity_type = "SELECT DISTINCT entity_action FROM activity_feed WHERE user_type='Agent'";
	$activity_type_res = $rpdo->select($sel_activity_type);
}

include_once 'tmpl/global_activity_agents_tabs.inc.php';
?>