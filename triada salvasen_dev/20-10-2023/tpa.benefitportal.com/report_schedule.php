<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/reports.php';
$reports_class = new Report();
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : "";
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : "";
$report_id = isset($_GET['id']) ? $_GET['id'] : "";
$is_schedule_popup = true;
$schedule_type = '';
$schedule_frequency = '';
$schedule_end_type = '';
$schedule_end_times = '';
$schedule_end_date = '';
$time = '';
$timezone = '';
$days_of_week = '';
$month_option = '';
$days_of_month = '';
$days_of_month_arr = '';
$months = '';
$months_arr = '';
$day_type = '';
$selected_day = '';
$generate_via = '';
$email = '';
$password = '';
$cancel_processing = 'N';
$days_of_week_arr = array();
$button_text = "Schedule";
$filter_options = array();
$user_data = get_user_data_for_af($user_id,$user_type);
$get_selected_column = false;
if(!empty($report_id)){
	$report_sql = "SELECT * FROM $REPORT_DB.rps_reports WHERE md5(id)=:id"; 
    $report_row = $pdo->selectOne($report_sql,array(":id" => $report_id));
    $FTP = $report_row['ftp_name']; 
    $report_name = $report_row['report_name'];
    
    $rs_schedule_sql = "SELECT *,AES_DECRYPT(password,'$CREDIT_CARD_ENC_KEY') as file_password FROM $REPORT_DB.rps_reports_schedule WHERE is_deleted='N' AND md5(report_id)=:id AND user_id=:user_id AND user_type=:user_type";
    $where_id = array(':id'=>$report_id,':user_id'=>$user_data['user_id'],':user_type'=>$user_data['user_type']);
    $rs_schedule_row = $pdo->selectOne($rs_schedule_sql,$where_id);

    $report_columns = $reports_class->getfields($report_row['report_key']);
    $selected_columns = array_keys($report_columns);
	 
	if (!empty($rs_schedule_row) && is_array($rs_schedule_row)) {
		$button_text = "Update";
		$cancel_processing = $rs_schedule_row['cancel_processing'];
		if($cancel_processing == "N") {
		  	$schedule_type = $rs_schedule_row['schedule_type'];
		  	$schedule_frequency = $rs_schedule_row['schedule_frequency'];
		  	$schedule_end_type = $rs_schedule_row['schedule_end_type'];
		  	if($schedule_end_type == "no_of_times"){
		  		$schedule_end_times = $rs_schedule_row['schedule_end_val'];
		  	} else if($schedule_end_type == "on_date") {
		  		$schedule_end_date = date('m/d/Y',strtotime($rs_schedule_row['schedule_end_val']));
		  	}

		  	$schedule_end_val = $rs_schedule_row['schedule_end_val'];

		  	$time = $rs_schedule_row['time'];
		  	$timezone = $rs_schedule_row['timezone'];

		  	$days_of_week = $rs_schedule_row['days_of_week'];
		  	$days_of_week_arr = explode(",",$days_of_week);

		  	$month_option = $rs_schedule_row['month_option'];
		  	$days_of_month = $rs_schedule_row['days_of_month'];
		  	$days_of_month_arr = explode(",",$days_of_month);

		  	$day_type = $rs_schedule_row['day_type'];
		  	$selected_day = $rs_schedule_row['selected_day'];

		  	$months = $rs_schedule_row['months'];
		  	$months_arr = explode(",",$months);

		  	$generate_via = $rs_schedule_row['generate_via'];
		  	$email = $rs_schedule_row['email'];
		  	$password = $rs_schedule_row['file_password'];

		  	if(!empty($rs_schedule_row['filter_options'])) {
		  		$filter_options = json_decode($rs_schedule_row['filter_options'],true);
		  	}

		  	$extra = json_decode($rs_schedule_row['extra'],true);
		  	if(!empty($extra['report_columns'])) {
		  		$selected_columns = $extra['report_columns'];
		  	} else {
		  		$get_selected_column = true;	
		  	}
		}
	} else {
		$get_selected_column = true;
	}
	
	if($get_selected_column) {
		$setting_row = $reports_class->get_rps_user_report_settings($user_data['user_id'],$user_data['user_type'],$report_row['id']);
		if(!empty($setting_row['selected_columns'])) {
			$selected_columns = explode(',',$setting_row['selected_columns']);
		}
	}
}


$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css'.$cache,
	'thirdparty/lou-multi-select/css/multi-select.css'.$cache,
);
$exJs = array(
	'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
	'thirdparty/lou-multi-select/js/jquery.multi-select.js'.$cache,
);
$template = "report_schedule.inc.php";
include_once 'layout/iframe.layout.php';
?>