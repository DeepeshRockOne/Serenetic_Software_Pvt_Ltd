<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$file_id = isset($_GET['id']) ? $_GET['id'] : "";
	$button_text = "Schedule";
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
	$file_type = '';
	$days_of_week_arr = array();
	$cancel_processing = 'N';
	$filter_options = array();

if(!empty($file_id)){
	$sql_file = "SELECT * FROM billing_files WHERE id=:file_id"; 
    $resFile = $pdo->selectOne($sql_file,array(":file_id" => $file_id)); 
    $FTP = $resFile['ftp_name']; 
    $file_name = $resFile['file_name'];
    $cancel_processing = $resFile['cancel_processing'];

	$eligiblity_sql = "SELECT *,AES_DECRYPT(password,'$CREDIT_CARD_ENC_KEY') as file_password FROM billing_schedule WHERE is_deleted='N' AND file_id=:id";
	$where_id = array(':id' => $file_id);
	$eligiblity_res = $pdo->selectOne($eligiblity_sql, $where_id);

	 
	if (!empty($eligiblity_res) && is_array($eligiblity_res)) {
		$button_text = 'Update';
	  	$schedule_type = $eligiblity_res['schedule_type'];
	  	$schedule_frequency = $eligiblity_res['schedule_frequency'];
	  	$schedule_end_type = $eligiblity_res['schedule_end_type'];
	  	if($schedule_end_type == "no_of_times"){
	  		$schedule_end_times = $eligiblity_res['schedule_end_val'];
	  	}else if($schedule_end_type == "on_date"){
	  		$schedule_end_date = date('m/d/Y',strtotime($eligiblity_res['schedule_end_val']));
	  	}


	  	$schedule_end_val = $eligiblity_res['schedule_end_val'];

	  	$time = $eligiblity_res['time'];
	  	$timezone = $eligiblity_res['timezone'];

	  	$days_of_week = $eligiblity_res['days_of_week'];
	  	$days_of_week_arr = explode(",",$days_of_week);

	  	$month_option = $eligiblity_res['month_option'];
	  	$days_of_month = $eligiblity_res['days_of_month'];
	  	$days_of_month_arr = explode(",",$days_of_month);

	  	$day_type = $eligiblity_res['day_type'];
	  	$selected_day = $eligiblity_res['selected_day'];

	  	$months = $eligiblity_res['months'];
	  	$months_arr = explode(",",$months);


	  	$generate_via = $eligiblity_res['generate_via'];
	  	$email = $eligiblity_res['email'];
	  	$password = $eligiblity_res['file_password'];

	  	$file_type = $eligiblity_res['file_type'];

	  	if(!empty($eligiblity_res['filter_options'])) {
	  		$filter_options = json_decode($eligiblity_res['filter_options'],true);
	  	}
	}
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "billing_schedule.inc.php";
include_once 'layout/iframe.layout.php';
?>