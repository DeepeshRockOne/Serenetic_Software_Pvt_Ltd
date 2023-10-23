<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require dirname(__DIR__) .'/libs/awsSDK/vendor/autoload.php';
//WE ARE NOT USE THIS FILE
redirect("dashboard.php");
exit();

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Export Requests";
$breadcrumbes[1]['link'] = 'export_requests.php';
$breadcrumbes[1]['class'] = "Active";

$user_id = $_SESSION['admin']['id'];
$user_type = "Admin";
$incr = "";
$sch_params = array();
$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';

$has_querystring = false;
if(isset($_GET['is_download']) && isset($_GET['file_name']) && $_GET['file_name']!=''){
	$keyname = urldecode($_GET['file_name']);
	
	$s3 = new S3Client([
		'version' => 'latest',
		'region'  => $S3_REGION,
		'credentials'=>array(
            'key'=> $S3_KEY,
			'secret'=> $S3_SECRET
		)
	]);
	
	try {
		// Get the object.
		$result = $s3->getObject([
			'Bucket' => $S3_BUCKET_NAME,
			'Key'    => $keyname,
		]);

		header("Content-Type: {$result['ContentType']}");
		header("Content-Disposition: attachment; filename=" . $_GET['file_name']);
		echo $result['Body']; exit;
	} catch (S3Exception $e) {
		//echo $e->getMessage() . PHP_EOL;
		redirect('export_requests.php');
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
	'results_per_page' => 50, //$per_page,
	'url' => 'export_requests.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	try {
		$sel_sql = "SELECT er.*,IF(ag.id IS NOT NULL,ag.rep_id,ad.display_id) as requester_rep_id,
					IF(ag.id IS NOT NULL,CONCAT(ag.fname,' ',ag.lname),CONCAT(ad.fname,' ',ad.lname)) as requester_name 
					FROM $REPORT_DB.export_requests er 
					LEFT JOIN customer ag ON(ag.id = er.user_id AND er.user_type='Agent')
					LEFT JOIN admin ad ON(ad.id = er.user_id AND er.user_type='Admin')
          			WHERE
          			(ag.id IS NOT NULL OR ad.id IS NOT NULL) AND
          			er.is_cancelled='N' AND
          			er.user_id = '$user_id' AND
          			er.user_type = '$user_type' 
          			" . $incr . " ORDER BY id DESC";

		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/export_requests.inc.php';
	exit;
}
$page_title = "Export Requests";
$template = 'export_requests.inc.php';
include_once 'layout/end.inc.php';
?>