<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
agent_has_access(7);

/* notification code start */
$function = new functionsList();
if (isset($_REQUEST["noti_id"])) {
	openAdminNotification($_REQUEST["noti_id"]);
}

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Book of Business";
$breadcrumbes[2]['title'] = "Agents";
$breadcrumbes[2]['link'] = 'agent_listing.php';
$breadcrumbes[2]['class'] = "Active";

//$user_groups = "active";

$sch_params = array();
$incr ='';
$tbl_incr ='';
$having=""; 
$tble_incr = "";
$agnt_prd = "";	
$sponsorSetting = "";	
$license_incr ="";
$qry_incr ='';
$SortBy = " IF(c.joined_date IS NOT NULL,c.joined_date,c.created_at)";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$agent_id = $_SESSION['agents']['id'];

$agentSql = "SELECT c.id as agentId,cs.agent_coded_id
			FROM customer c
			JOIN customer_settings cs ON(c.id=cs.customer_id)
			WHERE c.type='Agent' AND c.id=:agentId";
$agentRes = $pdo->selectOne($agentSql,array(":agentId" => $agent_id));

$agent_coded_id = !empty($agentRes["agent_coded_id"]) ? $agentRes["agent_coded_id"] : 0;

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
$rep_id = isset($_GET['rep_id']) ? $_GET['rep_id'] : array();
$bus_name = isset($_GET['bus_name']) ? $_GET["bus_name"] : '';
$p_agent_name = isset($_GET['p_agent_name']) ? $_GET["p_agent_name"] : '';
$phone = isset($_GET['phone']) ? $_GET["phone"] : '';
$email = isset($_GET['email']) ? $_GET["email"] : '';
$account_type = isset($_GET['account_type']) ? $_GET["account_type"] : '';
$agent_level = isset($_GET['agent_level']) ? $_GET['agent_level'] : "";
$agents_status = isset($_GET['agents_status']) ? $_GET['agents_status'] : '';
$tree_agent_id =  isset($_GET['tree_agent_id']) ? $_GET['tree_agent_id'] : array();
$combination_product = isset($_GET['combination_product']) ? $_GET['combination_product'] : array();
$company = isset($_GET['company']) ? $_GET['company'] : array();
$license_state = isset($_GET['license_state']) ? $_GET['license_state'] : '';
$license_status = isset($_GET['license_status']) ? $_GET["license_status"] : '';
$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
$todate = isset($_GET["todate"])?$_GET["todate"]:"";
$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";
$select_alert = checkIsset($_GET['select_alert']);
$displayDirectEnroll = !empty($_SESSION['agents']['displayDirectEnroll']) ? explode(",", $_SESSION['agents']['displayDirectEnroll']) : array();

if (isset($_SESSION['company_id']) && $_SESSION['company_id'] != "") {
	$sch_params[':company_id'] = makeSafe($_SESSION['company_id']);
	$incr .= " AND c.company_id = :company_id";
}

if (!empty($rep_id)) {
	$rep_id = "'" . implode("','", makeSafe($rep_id)) . "'";
	$incr .= " AND c.rep_id IN ($rep_id)";
}

if (!empty($bus_name)) {
	// $sch_params[':business_name'] = "%" . makeSafe($bus_name) . "%";
	// $incr .= " AND cs.company_name LIKE :business_name";

	$bus_name = "'" . implode("','", makeSafe($bus_name)) . "'";
	$incr .= " AND cs.company_name IN ($bus_name)";

}

if (!empty($p_agent_name)) {
	// $sch_params[':fname'] = "%" . makeSafe($p_agent_name) . "%";
	// $incr .= " AND (c.fname LIKE :fname or c.lname LIKE :fname) ";

	$p_agent_name = "'" . implode("','", makeSafe($p_agent_name)) . "'";
	$incr .= " AND c.rep_id IN ($p_agent_name)";
}

if ($email != "") {
	$sch_params[':email'] = "%" . makeSafe($email) . "%";
	$incr .= " AND c.email LIKE :email";
}

if ($phone != "") {
	$sch_params[':phone'] = "%" . makeSafe($phone) . "%";
	$incr .= " AND c.cell_phone LIKE :phone";
}

if($account_type!=""){
	$sch_params[':account_type'] = makeSafe($account_type);
	$incr .= " AND cs.account_type = :account_type ";
}

if(!empty($company)){
	$imploded_company = "'" . implode("','", makeSafe($company)) . "'";
	$incr .= " AND cs.company IN ($imploded_company)";
}

if($agent_level != ""){
	$imploded_level = "'" . implode("','", makeSafe($agent_level)) . "'";
	$incr .= " AND cs.agent_coded_id IN ($imploded_level)";
}
 
if ($agents_status != "") {
	$sch_params[':agents_status'] = makeSafe($agents_status);
	$incr .= " AND c.status = :agents_status";
}
$tree_search = false;//we need Logged in Agent on tree search so Added this condition
if(!empty($tree_agent_id)){
	// $incr .= " AND c.sponsor_id IN (".implode(",",$tree_agent_id).")";
	$incr .= " AND (c.id IN(".implode(",",$tree_agent_id).") OR (c.sponsor_id IN (".implode(",",$tree_agent_id).") AND cs.agent_coded_id = 1)) ";
	$tree_search = true;
}
 
if(!empty($combination_product)){
	$combination_product = implode(',', $combination_product);
	$agnt_prd = "LEFT JOIN agent_product_rule apr on(apr.agent_id = c.id AND apr.is_deleted = 'N')";
	$incr .= " AND apr.product_id IN ($combination_product) AND apr.is_deleted = 'N'";
} 

if($license_state != "" && $license_status!="Not Setup"){
	$imploded_level = "'" . implode("','", makeSafe($license_state)) . "'";
	$incr .= " AND l.selling_licensed_state IN ($imploded_level)";
}

if($license_status!=''){
	if($license_status=="Active"){
		$incr .= " AND c.status not in('".implode("','",$AGENT_ABANDON_STATUS)."') ";
		if($having !=''){
			$having .= " AND countExpired=0 AND countValid>0 ";
		}else{
			$having .= " HAVING countExpired=0 AND countValid>0 ";
		}
	}else if($license_status=="Expired"){
		$incr .= " AND c.status not in('".implode("','",$AGENT_ABANDON_STATUS)."') ";
		if($having !=''){
			$having .= " AND countExpired>0 ";
		}else{
			$having .= " HAVING countExpired>0 ";
		}

	}else if($license_status=="Not Setup"){
		if(!empty($license_state)){
			$imploded_st = implode("','", makeSafe($license_state));
			$license_incr.=" OR l.selling_licensed_state='' ";
			$sch_params[':license_states'] = '%'. makeSafe($imploded_st) . '%';

			if($having !=''){
				$having .= " AND  (license_states NOT LIKE :license_states) ";
			}else{
				$having .= " HAVING (license_states NOT LIKE :license_states)";
			}
		}else{
			if($having !=''){
				$having .= " AND  countExpired=0 AND countValid=0 ";
			}else{
				$having .= " HAVING countExpired=0 AND countValid=0";
			}
		}
	}
}

if($join_range != ""){
	$custom_date = ' if(c.joined_date is not null or c.joined_date != "0000-00-00" or c.joined_date!="" ,c.joined_date,c.invite_at) ';
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE($custom_date) >= :fromdate AND DATE($custom_date) <= :todate";
  }else if($join_range == "Exactly" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE($custom_date) = :added_date";
  }else if($join_range == "Before" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE($custom_date) < :added_date";
  }else if($join_range == "After" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE($custom_date) > :added_date";
  }
}

if($select_alert!=''){
	if($select_alert == 'eo_expired'){
		$incr.=" AND (DATE(ad.e_o_expiration) < CURDATE() or ad.e_o_expiration is null)";
	}
	if($select_alert == 'license_expired'){
		$incr.=" AND l.license_exp_date < CURDATE() AND l.license_exp_date is not null AND l.license_exp_date is not null ";
	}
}
if(!$tree_search){
	if(!empty($displayDirectEnroll) && in_array('Agents', $displayDirectEnroll)){
		$sponsorSetting = "LEFT JOIN customer_settings scs ON (s.id = scs.customer_id)";
		$incr .= " AND (c.sponsor_id = :agent_id OR (s.sponsor_id =:agent_id AND scs.agent_coded_level = 'LOA'))";
		$sch_params[':agent_id']=$agent_id;
	}else{
		$incr .= " AND c.upline_sponsors LIKE '%,$agent_id,%' ";
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
	'url' => 'agent_listing.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';
if ($is_ajaxed) { 
	try {

		$sel_sql = "SELECT $qry_incr GROUP_CONCAT(DISTINCT(l.selling_licensed_state)) AS license_states,c.rep_id, c.joined_date,c.invite_at,md5(c.id) as id, c.type, c.fname, c.lname, c.email, c.status,c.cell_phone,c.user_name,c.business_name,s.fname as s_fname,c.access_type,s.lname as s_lname,c.sponsor_id, c.upline_sponsors,s.rep_id as sponsor_rep_id,cs.company,cs.company_name,cs.agent_coded_level,cs.agent_coded_id,HOUR(TIMEDIFF(NOW(), c.invite_at)) as invite_time_diff,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,SUM(IF(l.license_exp_date<CURDATE() AND l.license_exp_date is not null AND l.license_exp_date is not null,1,0)) as countExpired,l.license_exp_date,SUM(IF(l.license_exp_date>=CURDATE() AND l.license_exp_date is not null AND l.license_exp_date is not null,1,0)) as countValid,ad.e_o_expiration,cs.account_type
                  FROM customer c
                  JOIN customer_settings cs ON (c.id = cs.customer_id)
                  LEFT JOIN agent_license l ON(c.id=l.agent_id AND l.is_deleted='N' $license_incr)
                  LEFT JOIN customer as s on(s.id= c.sponsor_id)
				  LEFT JOIN agent_document ad ON(ad.agent_id=c.id)
                  ".$sponsorSetting."
				  ".$agnt_prd."
				  ".$tbl_incr."
				  WHERE c.type='Agent'  AND c.is_deleted = 'N' 
				  
                  " . $incr . " 
                  GROUP BY c.id 
                  $having 
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

	/*   * ****************    Export Code End ******************** */
	include_once 'tmpl/agent_listing.inc.php';
	exit;
}

$tree_agent_sql = "SELECT c.id,c.rep_id,c.fname,c.lname,cs.company_name
					FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id)
					where c.type='Agent' AND c.is_deleted = 'N' AND c.upline_sponsors LIKE '%,$agent_id,%' ";
$tree_agent_res = $pdo->select($tree_agent_sql);
$excludePrdList = get_active_global_products_for_filter($agent_id,true);

$agent_summery_arr = array(
	'Active' => 0,
	'Invited' => 0,
	'Pending Contract' => 0,
	'Pending Documentation' => 0,
	'Pending Approval' => 0,
	'Suspended' => 0,
	'Terminated' => 0,
);
$tmpIncr = "";
$tmpSchparams = array();
$tmpSponsorSetting = "";
if(!empty($displayDirectEnroll) && in_array('Agents', $displayDirectEnroll)){
	$tmpSponsorSetting = " LEFT JOIN customer s ON (s.id = c.sponsor_id) LEFT JOIN customer_settings scs ON (scs.customer_id = s.id)";
	$tmpIncr .= " AND (c.sponsor_id = :agent_id OR (s.sponsor_id =:agent_id AND scs.agent_coded_level = 'LOA'))";
	$tmpSchparams[':agent_id']=$agent_id;
}else{
	$tmpIncr .= " AND c.upline_sponsors LIKE '%,$agent_id,%' ";
}
$agent_summerySql = "SELECT c.status,count(c.status) as total_status 
					from customer c
					".$tmpSponsorSetting."
					where c.is_deleted='N' and c.type='Agent' ".$tmpIncr." group by c.status";
$agent_summery = $pdo->select($agent_summerySql,$tmpSchparams);
if(!empty($agent_summery)){
	foreach($agent_summery as $summary){
		if(array_key_exists($summary['status'],$agent_summery_arr)){
			$agent_summery_arr[$summary['status']] = $summary['total_status'];
		}
	}
}
$select_state = "SELECT * FROM `states_c` WHERE country_id in(:id) order by name ASC";
$license_state_res = $pdo->select($select_state,array(":id"=>231));

$companyArr = $pdo->select("SELECT cs.company from customer c JOIN customer_settings cs ON(cs.customer_id=c.id) where c.type='Agent' AND c.is_deleted='N' AND cs.company IS NOT NULL AND c.upline_sponsors LIKE '%,".$agent_id.",%'  GROUP BY cs.company");

$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css',   
	'thirdparty/select2/css/select2.css'
);

$exJs = array(
	  
	'thirdparty/multiple-select-master/jquery.multiple.select.js',    
	'thirdparty/select2/js/select2.full.min.js'
);

$template = 'agent_listing.inc.php';
include_once 'layout/end.inc.php';
?>