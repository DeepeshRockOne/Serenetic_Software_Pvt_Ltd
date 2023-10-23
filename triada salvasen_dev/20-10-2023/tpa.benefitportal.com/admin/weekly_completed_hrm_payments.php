<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$module_access_type = has_access(91);
$incr = "";
$sch_params = array();
$SortBy = "s.fname";
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

$is_ajaxed = checkIsset($_GET['is_ajaxed']);
$pay_period = checkIsset($_REQUEST["pay_period"]);
$groupId = checkIsset($_GET["groupId"]);
$groupName = checkIsset($_GET["groupName"]);

if (empty($pay_period)) {
	redirect("hrm_payments.php", true);
} else {
	$sch_params[':pay_period'] = date("Y-m-d", strtotime($pay_period));
	$incr .= " AND hrmp.pay_period = :pay_period";
}

if (!empty($groupId)) {
	$sch_params[':groupId'] = "%" . makeSafe($groupId) . "%";
	$incr .= " AND s.rep_id LIKE :groupId";
}

if (!empty($groupName)) {
	$sch_params[':groupName'] = "%" . makeSafe($groupName) . "%";
	$incr .= " AND (s.fname LIKE :groupName or s.lname LIKE :groupName or CONCAT(s.fname,' ',s.lname) LIKE :groupName)";
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}

$per_page = 10;
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}

$page = isset($_GET['page']) ? $_GET['page'] : '';
$query_string = $has_querystring ? ($page ? str_replace('page=' . $page, "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'weekly_completed_hrm_payments.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
    try {
        $sel_sql = "SELECT hrmp.group_id AS groupId,COUNT(DISTINCT hrmp.payer_id) AS memberId,
						s.rep_id AS groupRepId,hrmp.pay_period,hrmp.pay_date,
						IF(cst.company_name != '',cst.company_name,CONCAT(s.fname,' ',s.lname)) AS groupName,
						SUM(IF(hrmp.sub_type='New' OR hrmp.sub_type='Renewals' ,hrmp.amount,0)) AS totalComm, nfe.id as nachafile,nfe.file_name as nacha_file,nfe.control_file_name as controlfile,nfe.file_type,nfe.created_at
						FROM hrm_payment hrmp
						JOIN customer AS s ON(s.id = hrmp.group_id AND s.type='Group')
						JOIN customer AS c ON(c.id = hrmp.payer_id AND c.type='Customer')
						JOIN customer_settings cst ON (s.id = cst.customer_id)
                        JOIN nacha_file_members nfm ON(nfm.is_deleted='N' AND nfm.customer_id=c.id)
                        JOIN nacha_file_export as nfe ON(nfe.is_deleted='N' AND nfm.nacha_batch_id=nfe.id AND nfe.group_id=s.id AND hrmp.pay_date=nfe.pay_date)
						WHERE hrmp.hrm_payment_duration='weekly' AND hrmp.status IN('Completed') AND hrmp.is_deleted='N' $incr
						GROUP BY s.id,nfe.id ORDER BY $SortBy $currSortDirection";
        $paginate = new pagination($page, $sel_sql, $options);
        if ($paginate->success == true) {
            $fetch_rows = $paginate->resultset->fetchAll();
            $total_rows = count($fetch_rows);
        }
    } catch (paginationException $e) {
        echo $e;
        exit();
    }

	include_once 'tmpl/weekly_completed_hrm_payments.inc.php';
	exit;
}

$template = 'weekly_completed_hrm_payments.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
