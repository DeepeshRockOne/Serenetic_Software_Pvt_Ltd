<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$lead_tag_res = get_lead_tags();
$curr_page_url = '';
$SortBy = "l.created_at";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$gsearch = isset($_GET['gsearch']) ? $_GET['gsearch'] : '';
$is_ajaxed_leads = isset($_GET['is_ajaxed_leads']) ? $_GET['is_ajaxed_leads'] : '';
$has_querystring = false;
$sch_params = array();
$incr = isset($incr) ? $incr : '';
if ($gsearch != "") {
	$sch_params[':name'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':email'] = '%' . makeSafe($gsearch) . '%';
	$sch_params[':lead_id'] = makeSafe($gsearch);
	$sch_params[':cell_phone'] = '%' . makeSafe($gsearch) . '%';
	
	$incr .= " AND (l.fname LIKE :name OR l.lname LIKE :name OR CONCAT(trim(l.fname),' ',trim(l.lname)) LIKE :name OR CONCAT(trim(l.lname),' ',trim(l.fname)) LIKE :name OR l.email LIKE :email OR l.lead_id=:lead_id OR l.cell_phone LIKE :cell_phone)";
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
	'url' => 'global_leads.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed_leads){
	
	$agent_res = get_active_agents_for_select();

	$sel_sql = "SELECT l.*,md5(l.id) as id,s.rep_id as sponsor_rep_id,CONCAT(s.fname,' ',s.lname) as sponsor_name,s.business_name,s.type as sponsor_type
                  FROM leads l
                  JOIN customer s ON (s.id = l.sponsor_id)
                  WHERE l.is_deleted = 'N' " . $incr . " 
				  ORDER BY  $SortBy $currSortDirection";
	
	$paginate = new pagination($page, $sel_sql, $options);
	if ($paginate->success == true) {
		$fetch_rows = $paginate->resultset->fetchAll();
		$total_rows = count($fetch_rows);

		$link_array = !empty($paginate->links_array['links'])?$paginate->links_array['links']:array();

        if (count($link_array) > 0) {
            foreach ($link_array as $value) {
                foreach ($value as $key => $val) {
                    if ($val['is_current_page'] != 0) {
                        $curr_page_url = $val['link_url'];
                    }
                }
            }
        }
	}

}
include_once 'tmpl/global_leads.inc.php';
?>