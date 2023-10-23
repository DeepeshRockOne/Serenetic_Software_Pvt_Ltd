<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/participants.class.php';

$participantsObj = new Participants();

$participants_tag_res = $participantsObj->get_participants_tags();

$SortBy = "p.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$gsearch = isset($_GET['gsearch']) ? $_GET['gsearch'] : '';
$is_ajaxed_participants = isset($_GET['is_ajaxed_participants']) ? $_GET['is_ajaxed_participants'] : '';

$has_querystring = false;
$sch_params = array();

$incr = isset($incr) ? $incr : '';
if ($gsearch != "") {

	$sch_params[':name'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':participants_id'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':employee_id'] = '%' . makeSafe($gsearch) . '%';
	$incr .= " AND (p.fname LIKE :name OR p.lname LIKE :name OR CONCAT(trim(p.fname),' ',trim(p.lname)) LIKE :name OR CONCAT(trim(p.lname),' ',trim(p.fname)) LIKE :name OR p.participants_id LIKE :participants_id OR p.employee_id LIKE :employee_id)";
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
	'url' => 'global_participants.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed_participants){

    $sel_sql = "SELECT md5(p.id) as id,p.id as pid,p.participants_id,CONCAT(p.fname,' ',p.lname) as name,
                    p.created_at as addedDate,p.cell_phone,p.email,p.participants_type,p.participants_tag,
                    p.status, md5(a.id) as adminId,a.display_id as adminDispId,
                    CONCAT(a.fname,' ',a.lname) as adminName
                   FROM participants p
                   LEFT JOIN admin a ON(a.id=p.admin_id)
                   WHERE p.is_deleted = 'N'  
                  " . $incr . " 
				  ORDER BY  $SortBy $currSortDirection";
	
	$paginate = new pagination($page, $sel_sql, $options);
	if ($paginate->success == true) {
		$fetch_rows = $paginate->resultset->fetchAll();
		$total_rows = count($fetch_rows);
	}
			
}


include_once 'tmpl/global_participants.inc.php';
?>