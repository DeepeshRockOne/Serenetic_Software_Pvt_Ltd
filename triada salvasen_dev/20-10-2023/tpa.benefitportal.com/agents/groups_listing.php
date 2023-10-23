<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
agent_has_access(9);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Book of Business";
$breadcrumbes[2]['title'] = "Groups";
$breadcrumbes[2]['link'] = 'groups_listing.php';


$sch_params = array();
$incr ='';

$having=""; 
$tble_incr = "";
$agnt_prd = "";	
$license_incr ="";
$sponsorSetting = "";


$SortBy = "c.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

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
$group_name = isset($_GET['group_name']) ? $_GET["group_name"] : '';
$contact_name = isset($_GET['contact_name']) ? $_GET["contact_name"] : '';
$phone = isset($_GET['phone']) ? $_GET["phone"] : '';
$email = isset($_GET['email']) ? $_GET["email"] : '';
$group_status = isset($_GET['group_status']) ? $_GET['group_status'] : '';
$tree_agent_id =  isset($_GET['tree_agent_id']) ? $_GET['tree_agent_id'] : array();
$combination_product = isset($_GET['combination_product']) ? $_GET['combination_product'] : array();
$member_id = isset($_GET['member_id']) ? $_GET['member_id'] : array();
$agent_name = isset($_GET['agent_name']) ? $_GET['agent_name'] : array();



$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
$todate = isset($_GET["todate"])?$_GET["todate"]:"";
$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";
$select_alert = checkIsset($_GET['select_alert']);
$displayDirectEnroll = !empty($_SESSION['agents']['displayDirectEnroll']) ? explode(",", $_SESSION['agents']['displayDirectEnroll']) : array();

$rep_id = cleanSearchKeyword($rep_id);
$group_name = cleanSearchKeyword($group_name); 
$contact_name = cleanSearchKeyword($contact_name);
$email = cleanSearchKeyword($email);
$phone = cleanSearchKeyword($phone);
$contact_name = cleanSearchKeyword($contact_name); 
 
if (!empty($rep_id)) {
	$rep_id = "'" . implode("','", makeSafe($rep_id)) . "'";
	$incr .= " AND c.rep_id IN ($rep_id)";
}

if (!empty($group_name)) {

	$sch_params[':business_name'] = "%" . makeSafe($group_name) . "%";
	$incr .= " AND (c.business_name LIKE :business_name) ";
}

if (!empty($contact_name)) {
	$sch_params[':fname'] = "%" . makeSafe($contact_name) . "%";
	$incr .= " AND (c.fname LIKE :fname or c.lname LIKE :fname) ";
}

if ($email != "") {
	$sch_params[':email'] = "%" . makeSafe($email) . "%";
	$incr .= " AND c.email LIKE :email";
}

if ($phone != "") {
	$sch_params[':phone'] = "%" . makeSafe($phone) . "%";
	$incr .= " AND c.cell_phone LIKE :phone";
}

 
if ($group_status != "") {
	$sch_params[':group_status'] = makeSafe($group_status);
	$incr .= " AND c.status = :group_status";
}

if (!empty($member_id)) {
	$member_id = "'" . implode("','", makeSafe($member_id)) . "'";
	$incr .= " AND m.rep_id IN ($member_id)";
}

if (!empty($agent_name)) {
	$agent_id = implode(",", makeSafe($agent_name));
	$incr .= " AND s.id IN ($agent_id)";
}

if(!empty($tree_agent_id)){
	// $incr .= " AND c.sponsor_id IN (".implode(",",$tree_agent_id).")";
	$incr .= " AND (s.id IN(".implode(",",$tree_agent_id).") OR (s.sponsor_id IN (".implode(",",$tree_agent_id).") AND scs.agent_coded_id = 1)) ";
}
 

if(!empty($combination_product)){
	$combination_product = implode(',', $combination_product);
	$incr .= " AND p.id IN ($combination_product)  AND p.is_deleted = 'N'";
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
if(!empty($displayDirectEnroll) && in_array('Groups', $displayDirectEnroll)){
	$incr .= " AND (c.sponsor_id = :agent_id OR (s.sponsor_id =:agent_id AND scs.agent_coded_level = 'LOA'))";
	$sch_params[':agent_id']=$_SESSION['agents']['id'];
}else{
	$incr .= " AND c.upline_sponsors LIKE :sponsor_id";
	$sch_params[':sponsor_id'] = '%,'.$_SESSION['agents']['id'].',%';
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
	'url' => 'groups_listing.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
$incr = isset($incr) ? $incr : '';
if ($is_ajaxed) { 
	try {

		$sel_sql = "SELECT c.rep_id, c.joined_date,c.created_at,c.invite_at,md5(c.id) as id, c.type, c.fname, c.lname, c.email, c.status,c.cell_phone,c.user_name,c.business_name,s.fname as s_fname,c.access_type,s.lname as s_lname,c.sponsor_id, c.upline_sponsors,s.rep_id as sponsor_rep_id,HOUR(TIMEDIFF(NOW(), c.invite_at)) as invite_time_diff,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,count(DISTINCT p.id) as total_products,count(DISTINCT(m.id)) as total_members ,scs.agent_coded_level as sponsor_coded_level,s.sponsor_id as s_sponsor_id
                  FROM customer c
                  JOIN customer as s on(s.id= c.sponsor_id)
				  JOIN agent_product_rule apr on(apr.agent_id = c.id AND apr.is_deleted = 'N')
				  JOIN prd_main p on(apr.product_id = p.id AND p.is_deleted = 'N' AND p.product_type ='Group Enrollment')
				  LEFT JOIN customer m ON (m.sponsor_id = c.id AND m.type='Customer')
				  LEFT JOIN customer_settings scs ON (s.id = scs.customer_id)
                  WHERE c.type='Group'  AND c.is_deleted = 'N' 
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
	include_once 'tmpl/groups_listing.inc.php';
	exit;
}

$temp_incr =' AND c.upline_sponsors LIKE :sponsor_id';
$temp_sch_params[':sponsor_id'] = '%' . $_SESSION['agents']['id'] . '%';

$tree_group_sql = "SELECT c.id,c.rep_id,c.fname,c.lname,c.business_name
					FROM customer c 
					where c.type='Group' AND c.is_deleted = 'N' $temp_incr";
$tree_group_res = $pdo->select($tree_group_sql,$temp_sch_params);

// $tree_agent_sql = "SELECT c.id,c.rep_id,c.fname,c.lname
// 					FROM customer c 
// 					where c.type='Agent' AND c.is_deleted = 'N' $temp_incr";
// $tree_agent_res = $pdo->select($tree_agent_sql,$temp_sch_params);

$tree_member_sql = "SELECT c.id,c.rep_id,c.fname,c.lname
					FROM customer c 
					JOIN customer s on (c.sponsor_id = s.id AND s.type='Group')
					where c.type='Customer' AND c.is_deleted = 'N' $temp_incr";
$tree_member_res = $pdo->select($tree_member_sql,$temp_sch_params);

$downline_agent_sql = "SELECT c.id as agentId,c.rep_id as agentDispId,CONCAT(c.fname,' ',c.lname) as agentName
					FROM customer c 
					where c.type='Agent' AND c.is_deleted = 'N' AND (c.id=:agentId OR c.sponsor_id=:agentId)";
$downline_agent_res = $pdo->select($downline_agent_sql,array(":agentId" => $_SESSION['agents']['id']));


$combinationProductSql="SELECT p.id,p.name,p.parent_product_id,p.product_code,pc.title as category_name 
						FROM agent_product_rule apr 
						JOIN prd_main p ON(apr.product_id=p.id AND p.product_type ='Group Enrollment' AND p.is_deleted='N' AND p.type!='Fees' AND p.name != '')
						LEFT JOIN prd_category pc on(pc.id=p.category_id and pc.is_deleted='N')
						WHERE apr.is_deleted='N' AND apr.agent_id=:agentId
						ORDER BY p.name ASC";
$combinationProductRes=$pdo->select($combinationProductSql,array(":agentId" => $_SESSION['agents']['id']));

$excludePrdList = array('No category' => array());
if(!empty($combinationProductRes)){
	foreach ($combinationProductRes as $key => $value) {
		if($value['category_name']!=''){
			$excludePrdList[$value['category_name']][]=$value;
		}else{
			$excludePrdList['No category'][]=$value;
		}
	}
	if(empty($excludePrdList['No category'])){
		unset($excludePrdList['No category']);
	}
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'groups_listing.inc.php';
include_once 'layout/end.inc.php';
?>