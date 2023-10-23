<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';

$function = new functionsList();
$SortBy = "IFNULL(c.joined_date,c.invite_at)";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$gsearch = isset($_GET['gsearch']) ? $_GET['gsearch'] : '';
$is_ajaxed_agent = isset($_GET['is_ajaxed_agent']) ? $_GET['is_ajaxed_agent'] : '';

$has_querystring = false;
$sch_params = array();

$incr = isset($incr) ? $incr : '';
if ($gsearch != "") {

	$sch_params[':name'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':business_name'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':email'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':rep_id'] = makeSafe($gsearch);
	$sch_params[':company'] = makeSafe($gsearch);
	$sch_params[':user_id'] = makeSafe($gsearch);
	$sch_params[':cell_phone'] = '%' . makeSafe($gsearch) . '%';
	$incr .= " AND (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(trim(c.fname),' ',trim(c.lname)) LIKE :name OR CONCAT(trim(c.lname),' ',trim(c.fname)) LIKE :name OR c.email LIKE :email OR c.business_name LIKE :business_name OR c.rep_id=:rep_id OR cs.company=:company OR c.display_id=:user_id OR c.cell_phone LIKE :cell_phone)";
	// $sch_params[':name'] = '%' . makeSafe($gsearch) . '%';
	// $sch_params[':user_id'] = "%" . makeSafe($gsearch) . "%";
	// $sch_params[':email'] = '%' . makeSafe($gsearch) . '%';
	// $sch_params[':rep_id'] = makeSafe($gsearch);

	// $gsearch = str_replace("+1", "", $gsearch);

	// $incr .= " AND (c.fname LIKE :name OR c.lname LIKE :name OR CONCAT(c.fname,' ',c.lname) LIKE :name OR CONCAT(c.lname,' ',c.fname) LIKE :name OR c.email LIKE :email OR c.rep_id=:rep_id OR c.display_id=:user_id";

	// $p = "/\(\d{3}\)\s\d{3}-\d{4}/";
	// if(preg_match($p,$gsearch,$m)){
	//     $gsearch = str_replace("(", "", $gsearch);
	//     $gsearch = str_replace(")", "", $gsearch);
	//     $gsearch = str_replace("-", "", $gsearch);
	//     $gsearch = str_replace(" ", "", $gsearch);

	//     $sch_params[':cell_phone'] = "%" . makeSafe($gsearch) . "%";
	// 	$incr .= " OR c.cell_phone LIKE :cell_phone";
	// }else if(preg_match("/^[1-9][0-9]*$/",$gsearch,$m)){
	// 	$sch_params[':cell_phone'] = "%" . makeSafe($gsearch) . "%";
	// 	$incr .= " OR c.cell_phone LIKE :cell_phone";
	// }

	// //$p = "/\([a-zA-Z]{1}\)([0-9]))/";
	// $p="/^[a-z0-9.\-]+$/i";

	// if(preg_match($p,$gsearch)){
	//     $sch_params[':rep_id'] = "%" . makeSafe($gsearch) . "%";
	// 	$incr .= " OR c.rep_id LIKE :rep_id";
	// }
	// $incr .= ")";
	
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'global_agents.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed_agent){
	// $sel_sql = "SELECT c.rep_id, c.joined_date, c.id, c.type, c.fname, c.lname, c.email, c.status, c.cell_phone,c.user_name,c.business_name,s.fname as s_fname,c.access_type,s.lname as s_lname,c.sponsor_id, s.rep_id as sponsor_rep_id,cs.agent_coded_level,cs.recontract_status,cs.is_contract_approved,HOUR(TIMEDIFF(NOW(), c.invite_at)) as invite_time_diff,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,l.license_exp_date,SUM(IF(l.license_exp_date<CURDATE() AND l.license_exp_date!='0000-00-00' AND l.license_exp_date is not null,1,0)) as countExpired,SUM(IF(l.license_exp_date>=CURDATE() AND l.license_exp_date!='0000-00-00' AND l.license_exp_date is not null,1,0)) as countValid
 //                  FROM customer c
 //                  JOIN customer_settings cs ON (c.id = cs.customer_id)
 //                  LEFT JOIN agent_license l ON(c.id=l.agent_id AND l.is_deleted='N')
 //                  LEFT JOIN customer as s on(s.id= c.sponsor_id)
 //                  WHERE c.type='Agent' AND c.is_deleted = 'N' " . $incr . " GROUP BY c.id ORDER BY  $SortBy $currSortDirection";

    $sel_sql = "SELECT GROUP_CONCAT(DISTINCT(l.selling_licensed_state)) AS license_states,c.rep_id, c.joined_date,c.invite_at,md5(c.id) as id, c.type, c.fname, c.lname, c.email, c.status,c.cell_phone,c.user_name,c.business_name,s.fname as s_fname,c.access_type,s.lname as s_lname,c.sponsor_id, c.upline_sponsors,s.rep_id as sponsor_rep_id,cs.company_name,cs.company,cs.agent_coded_level,cs.agent_coded_id,HOUR(TIMEDIFF(NOW(), c.invite_at)) as invite_time_diff,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,SUM(IF(l.license_exp_date<CURDATE() AND l.license_exp_date is not null AND l.license_exp_date is not null,1,0)) as countExpired,l.license_exp_date,SUM(IF(l.license_exp_date>=CURDATE() AND l.license_exp_date is not null AND l.license_exp_date is not null,1,0)) as countValid,ad.e_o_expiration,cs.account_type,count(DISTINCT p.id) as total_products,SUM(IF(l.new_request='Y',1,0)) as countNewLicenseRequest
                  FROM customer c
                  JOIN customer_settings cs ON (c.id = cs.customer_id)
                  LEFT JOIN agent_license l ON(c.id=l.agent_id AND l.is_deleted='N')
                  LEFT JOIN customer as s on(s.id= c.sponsor_id)
				  LEFT JOIN agent_document ad ON(ad.agent_id=c.id)
				  LEFT JOIN agent_product_rule apr on(apr.agent_id = c.id AND apr.is_deleted = 'N')
				  LEFT JOIN prd_main p on(apr.product_id = p.id AND p.is_deleted = 'N' AND p.product_type != 'Healthy Step')
                  WHERE c.type='Agent'  AND c.is_deleted = 'N' 
                  " . $incr . " 
                  GROUP BY c.id 
				  ORDER BY  $SortBy $currSortDirection";
	
	$paginate = new pagination($page, $sel_sql, $options);
	if ($paginate->success == true) {
		$fetch_rows = $paginate->resultset->fetchAll();
		$total_rows = count($fetch_rows);
	}
			
}


include_once 'tmpl/global_agents.inc.php';
?>