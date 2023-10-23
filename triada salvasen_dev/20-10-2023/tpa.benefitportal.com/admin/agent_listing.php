<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
$module_access_type = has_access(5);

/* notification code start */
$function = new functionsList();
if (isset($_REQUEST["noti_id"])) {
	openAdminNotification($_REQUEST["noti_id"]);
}

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Agents";
$breadcrumbes[1]['link'] = 'agent_listing.php';
$breadcrumbes[1]['class'] = "Active";
$page_title = "Agents";
$user_groups = "active";

$sch_params = array();
$incr ='';
$tbl_incr ='';
$having=""; 
$tble_incr = "";
$agnt_prd = "";	
$license_incr ="";
$qry_incr ='';
$SortBy = " IF(c.joined_date IS NOT NULL,c.joined_date,c.created_at)";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$extra_export_arr = array();

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
}
// Get $dis_id From TPA Sites
$company =  !empty($_GET['company']) ? $_GET['company'] : '';
$license_state = !empty($_GET['license_state']) ?  explode(",", $_GET['license_state']) : '';
$combination_product = isset($_GET['combination_product']) ? $_GET['combination_product'] : '';

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$bus_name = isset($_GET['bus_name']) ? $_GET["bus_name"] : '';
$phone = isset($_GET['phone']) ? $_GET["phone"] : '';
$email = isset($_GET['email']) ? $_GET["email"] : '';
if($is_ajaxed){
	$rep_id = !empty($_GET['rep_id']) ? explode(",", $_GET['rep_id']) : "";
	$p_agent_name = isset($_GET['p_agent_name']) ? $_GET["p_agent_name"] : '';
	$account_type = isset($_GET['account_type']) ? $_GET["account_type"] : '';
	$agent_level = isset($_GET['agent_level']) ? $_GET['agent_level'] : "";
	$agents_status = isset($_GET['agents_status']) ? $_GET['agents_status'] : '';
	$tree_agent_id = !empty($_GET['tree_agent_id']) ? $_GET['tree_agent_id'] : "";
	$advances =  isset($_GET['advances']) ? $_GET['advances'] : '';
	$pmpms =  isset($_GET['pmpms']) ? $_GET['pmpms'] : '';
	$license_status = isset($_GET['license_status']) ? $_GET["license_status"] : '';
	$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
	$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
	$todate = isset($_GET["todate"])?$_GET["todate"]:"";
	$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";
	$select_alert = checkIsset($_GET['select_alert']);
	
	$bus_name = cleanSearchKeyword($bus_name);
	$p_agent_name = cleanSearchKeyword($p_agent_name);
	$email = cleanSearchKeyword($email);
	$phone = cleanSearchKeyword($phone); 
	 
	if (isset($_SESSION['company_id']) && $_SESSION['company_id'] != "") {
		$sch_params[':company_id'] = makeSafe($_SESSION['company_id']);
		$incr .= " AND c.company_id = :company_id";
	}
	
	if (!empty($rep_id)) {
		$rep_id = "'" . implode("','", makeSafe($rep_id)) . "'";
		$incr .= " AND c.id IN ($rep_id)";
	}
	
	if ($bus_name != "") {
		$sch_params[':business_name'] = "%" . makeSafe($bus_name) . "%";
		$incr .= " AND cs.company_name LIKE :business_name";
	}
	
	if ($p_agent_name) {
		$sch_params[':fname'] = "%" . makeSafe($p_agent_name) . "%";
		$incr .= " AND (c.fname LIKE :fname OR c.lname LIKE :fname)";
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
		$companyArr = explode(',',$company);
		$imploded_company = "'" . implode("','", makeSafe($companyArr)) . "'";
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
	
	if(!empty($tree_agent_id)){
		$sch_params[':tree_agent_id'] = makeSafe($tree_agent_id);
		$incr .= " AND (c.id IN(:tree_agent_id) OR (c.sponsor_id IN (:tree_agent_id) AND cs.agent_coded_id = 1)) ";
	}

	// if (!empty($tree_agent_id)) {
	// 	if (count($tree_agent_id) > 0) {
	// 		$incr .= " AND (";
	// 		foreach ($tree_agent_id as $key => $value) {
	// 			if (end($tree_agent_id) == $value) {
	// 				$incr .= " c.upline_sponsors LIKE '%," . $value . ",%'";
	// 			} else {
	// 				$incr .= " c.upline_sponsors LIKE '%," . $value . ",%' OR";
	// 			}
	// 		}
	// 		$incr .= ")";
	// 	}
	// }
	 
	$prd_incr = '';
	if(!empty($combination_product)){
		$prd_incr .= " AND p.id IN ($combination_product) AND p.is_deleted = 'N'";
	} 
	$tbl_incr .= "JOIN ( SELECT COUNT(DISTINCT product_id) AS total_products, GROUP_CONCAT(p.id) as pid, apr.agent_id AS customer_id FROM agent_product_rule apr JOIN prd_main p ON(apr.product_id = p.id AND p.is_deleted = 'N' AND p.product_type != 'Healthy Step'" . $prd_incr . ") WHERE apr.is_deleted = 'N' GROUP BY apr.agent_id ) p ON(p.customer_id = c.id)";
	
	if($pmpms != '' || $advances != ''){
		if($advances != '' && $advances=="yes"){
			$qry_incr .='ac.agent_id,';
			if(empty($having)){
				$having .= " HAVING ac.agent_id IS NOT NULL ";
			}else{
				$having .= "  AND ac.agent_id IS NOT NULL ";
			}
			$tbl_incr .= " LEFT JOIN advance_commission ac ON (ac.is_deleted='N' and if(ac.type='Global' and ac.agent_id=0 and ac.status='Active',1,ac.agent_id=c.id and ac.status='Active')) ";
	
		}else if($advances != '' && $advances=="no"){
			$qry_incr .='ac.agent_id,ac.type,';
			if(empty($having)){
				$having .= " HAVING ac.agent_id IS NULL ";
			}else{
				$having .= "  AND ac.agent_id IS NULL ";
			}
			$tbl_incr .= "  LEFT JOIN advance_commission ac ON (ac.is_deleted='N' and if(ac.type='Global' and ac.agent_id=0 and ac.status='Active',1,ac.agent_id=c.id and ac.status='Active'))  ";
		}
		if($pmpms!='' && $pmpms=="yes"){
			$tbl_incr .= " LEFT JOIN pmpm_commission pmpm ON (pmpm.agent_id=c.id and pmpm.is_deleted='N') ";
			$qry_incr .='pmpm.agent_id,';
			if(empty($having)){
				$having .= " HAVING pmpm.agent_id IS NOT NULL ";
			}else{
				$having .= "  AND pmpm.agent_id IS NOT NULL ";
			}
		}else if($pmpms!='' && $pmpms=="no"){
			$qry_incr .='pmpm.agent_id,';
			$tbl_incr .= " LEFT JOIN pmpm_commission pmpm ON (pmpm.agent_id=c.id and pmpm.is_deleted='N') ";
			if($having !=''){
				$having .= " AND pmpm.agent_id IS NULL ";
			}else{
				$having .= " having pmpm.agent_id IS NULL ";
			}
		}
	} 
	
	if($license_state != "" && $license_status!="Not Setup"){
		$imploded_states = "'" . implode("','", makeSafe($license_state)) . "'";
		$incr .= " AND l.selling_licensed_state IN ($imploded_states)";
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
	$getfromdate = '';
	$gettodate = '';
	if($join_range != ""){
	  if($join_range == "Range" && $fromdate!='' && $todate!=''){
		$sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
		$sch_params[':todate'] = date("Y-m-d",strtotime($todate));
		$incr.=" AND DATE(c.joined_date) >= :fromdate AND DATE(c.joined_date) <= :todate";
		$getfromdate = $fromdate;
    	$gettodate = $todate;
	  }else if($join_range == "Exactly" && $added_date!=''){
		$sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
		$incr.=" AND DATE(c.joined_date) = :added_date";
		$getfromdate = $added_date;
    	$gettodate = $added_date;
	  }else if($join_range == "Before" && $added_date!=''){
		$sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
		$incr.=" AND DATE(c.joined_date) < :added_date";
		$getfromdate = $added_date;
    	$gettodate = date('Y-m-d');
	  }else if($join_range == "After" && $added_date!=''){
		$sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
		$incr.=" AND DATE(c.joined_date) > :added_date";
		$getfromdate = date('Y-m-d');
    	$gettodate = $added_date;
	  }
	}
	
	if($select_alert!=''){
		if($select_alert == 'eo_expired'){
			$incr.=" AND (DATE(ad.e_o_expiration) < CURDATE() or ad.e_o_expiration is null)";
		}
		if($select_alert == 'license_expired'){
			$incr.=" AND l.license_exp_date < CURDATE() AND l.license_exp_date is not null AND l.license_exp_date is not null ";
		}
		if($select_alert == 'license_approval_request'){
			$incr.=" AND l.new_request='Y' AND l.is_deleted='N'";
		}
	}

	$export_val = isset($_GET['export_val']) ? $_GET["export_val"] : '';

	if(!empty($export_val)){

		include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';

		$extra_export_arr['qry_incr'] = $qry_incr;
		$extra_export_arr['license_incr'] = $license_incr;
		$extra_export_arr['tbl_incr'] = $tbl_incr;
		$extra_export_arr['having'] = $having;
		
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
		$job_id=add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Agent Summary PCI","agent_summery_export",$incr, $sch_params,$extra_export_arr,'agent_export');
		echo json_encode(array("status"=>"success","message"=>"Your export request is added")); 
		exit;
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
		try {
	
			$sel_sql = "SELECT $qry_incr GROUP_CONCAT(DISTINCT(l.selling_licensed_state)) AS license_states,c.rep_id, c.joined_date,c.invite_at,md5(c.id) as id, c.type, c.fname, c.lname, c.email, c.status,c.cell_phone,c.user_name,c.business_name,s.fname as s_fname,c.access_type,s.lname as s_lname,c.sponsor_id, c.upline_sponsors,s.rep_id as sponsor_rep_id,cs.company,cs.company_name,cs.agent_coded_level,cs.agent_coded_id,HOUR(TIMEDIFF(NOW(), c.invite_at)) as invite_time_diff,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,SUM(IF(l.license_exp_date<CURDATE() AND l.license_exp_date is not null AND l.license_exp_date is not null,1,0)) as countExpired,l.license_exp_date,SUM(IF(l.license_exp_date>=CURDATE() AND l.license_exp_date is not null AND l.license_exp_date is not null,1,0)) as countValid,ad.e_o_expiration,cs.account_type,p.total_products,SUM(IF(l.new_request='Y',1,0)) as countNewLicenseRequest
					  FROM customer c
					  JOIN customer_settings cs ON (c.id = cs.customer_id)
					  LEFT JOIN agent_license l ON(c.id=l.agent_id AND l.is_deleted='N' $license_incr)
					  LEFT JOIN customer as s on(s.id= c.sponsor_id)
					  LEFT JOIN agent_document ad ON(ad.agent_id=c.id)
					 
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
	
		include_once 'tmpl/agent_listing.inc.php';
		exit;
}

// $excludePrdList = get_active_global_products_for_filter(0,true);
$selectize = true;

// $companyArr = $pdo->select("SELECT cs.company from customer c JOIN customer_settings cs ON(cs.customer_id=c.id) where c.type='Agent' AND c.is_deleted='N' AND cs.company IS NOT NULL GROUP BY cs.company");

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