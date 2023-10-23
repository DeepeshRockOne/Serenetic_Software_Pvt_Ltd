<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$action = 'add';
$report_id = isset($_GET['id']) ? $_GET['id'] : "";
$portal = "";
$category_id = "";
$report_key = "";
$report_name = "";
$export_file_report_name = "";
$purpose_summary = "";
$definitions = "";
$is_allow_schedule = "Y";
$order_by = "";
$file_type = "CSV";
$ftp_name = "";

$products = array();
if(!empty($report_id)){
	$report_sql = "SELECT r.*,c.portal 
				FROM $REPORT_DB.rps_reports r 
				JOIN $REPORT_DB.rps_category c ON(c.id = r.category_id)
				WHERE md5(r.id)=:id";
	$report_row = $pdo->selectOne($report_sql,array(':id' => $report_id));

	if($report_row){
		$portal = $report_row['portal'];
		$category_id = $report_row['category_id'];
		$report_key = $report_row['report_key'];
		$report_name = $report_row['report_name'];
		$export_file_report_name = $report_row['export_file_report_name'];
		$purpose_summary = $report_row['purpose_summary'];
		$definitions = $report_row['definitions'];
		$is_allow_schedule = $report_row['is_allow_schedule'];
		$order_by = $report_row['order_by'];
		$file_type = $report_row['file_type'];
		$ftp_name = $report_row['ftp_name'];
	}
}
$category_res = $pdo->select("SELECT * FROM $REPORT_DB.rps_category WHERE is_deleted='N' ORDER BY order_by");

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache, 'thirdparty/ckeditor/ckeditor.js');

// $exStylesheets = array('thirdparty/summernote-master/dist/summernote.css');
// $exJs = array(
//   'thirdparty/summernote-master/dist/popper.js',
//   'thirdparty/summernote-master/dist/summernote.js'
// );
$template = "add_report.inc.php";
include_once 'layout/iframe.layout.php';
?>