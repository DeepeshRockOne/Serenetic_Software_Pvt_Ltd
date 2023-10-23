<?php
include_once __DIR__ . '/includes/connect.php';
$validate = new Validation();

// pre_print($_REQUEST);
//  step-1 variables
    $report_id = isset($_POST['report_id']) ? $_POST['report_id'] : '';
    $report_sql = "SELECT * FROM $REPORT_DB.rps_reports WHERE id=:id"; 
    $report_row = $pdo->selectOne($report_sql,array(":id" => $report_id));

    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';
    $user_data = get_user_data_for_af($user_id,$user_type);
    
    $schedule_type = isset($_POST['schedule_type']) ? $_POST['schedule_type'] : '';
    $schedule_frequency = isset($_POST['schedule_frequency']) ? $_POST['schedule_frequency'] : '' ;
    $schedule_end_type =  isset($_POST['schedule_end_type']) ? $_POST['schedule_end_type'] : '';
    $schedule_no_of_times = isset($_POST['schedule_no_of_times']) ? $_POST['schedule_no_of_times'] : '';
    $schedule_on_date = isset($_POST['schedule_on_date']) ? $_POST['schedule_on_date'] : '';
    $cancel_processing = isset($_POST['cancel_processing']) ? $_POST['cancel_processing'] : '';

    $time = isset($_POST['time']) ? $_POST['time'] : '';
    $timezone = isset($_POST['timezone']) ? $_POST['timezone'] : '';

    $days_of_week = isset($_POST['days_of_week']) ? implode(',', $_POST['days_of_week']) : '';
    $day_month = isset($_POST['day_month']) ? $_POST['day_month'] : '';
    // day of month
    $days_of_month = isset($_POST['days_of_month']) ? $_POST['days_of_month'] : '';
    // on the specific day
    $day_type = isset($_POST['day_type']) ? $_POST['day_type'] : '';
    $selected_day = isset($_POST['selected_day']) ? $_POST['selected_day'] : '';
    $months = isset($_POST['months']) ? $_POST['months'] : '';

// step-2 variables
    $generate_via = isset($_POST['generate_via']) ? $_POST['generate_via'] : '';
    $email = isset($_POST['email'])?rtrim(trim($_POST['email']),";"):'';
    $password = isset($_POST['password']) ? $_POST['password'] : '';


    $hdn_columns = isset($_POST['hdn_columns']) ? $_POST['hdn_columns'] : "";


    // check if report schedule is setup already 
    $rs_schedule_sql = "SELECT * FROM $REPORT_DB.rps_reports_schedule WHERE is_deleted='N' AND report_id=:id AND user_id=:user_id AND user_type=:user_type";
    $where_id = array(':id'=>$report_id,':user_id'=>$user_data['user_id'],':user_type'=>$user_data['user_type']);
    $rs_schedule_row = $pdo->selectOne($rs_schedule_sql,$where_id);

    if($cancel_processing == 'Y'){
        if(!empty($rs_schedule_row)){
            $req_where = array(
                "clause" => "id=:id",
                "params" => array(
                    ":id" => $rs_schedule_row['id'],
                )
            );
            $req_data = array(
                'cancel_processing' => $cancel_processing,
                'next_scheduled' => '',
            );
            $pdo->update("$REPORT_DB.rps_reports_schedule",$req_data,$req_where);
        }
        $desc = array();
        $desc['ac_message'] =array(
            'ac_red_1'=>array(
                'href'=> $user_data['profile_page_url'],
                'title'=> $user_data['rep_id'],
            ),
            'ac_message_1' =>' deleted scheduled delivery for report ',
            'ac_red_2'=>array(
                'title'=>$report_row['report_name'],
            ),
        ); 
        activity_feed(3,$user_data['user_id'],$user_data['user_type'],$user_data['user_id'],$user_data['user_type'],'Report Schedule Deleted','','',json_encode($desc));
        setNotifySuccess("Report Schedule Deleted Successfully");
        $response['status'] = 'success';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;

    }

    $validate->string(array('required' => true, 'field' => 'schedule_type', 'value' => $schedule_type), array('required' => 'Please select an option'));
    if(isset($schedule_type) && in_array($schedule_type,array("daily","weekly"))){
        $validate->string(array('required' => true, 'field' => 'schedule_frequency', 'value' => $schedule_frequency), array('required' => 'Please select an option'));
    }
    $validate->string(array('required' => true, 'field' => 'schedule_end_type', 'value' => $schedule_end_type), array('required' => 'Please select an option'));

    if(!empty($schedule_end_type)){
        if($schedule_end_type == "no_of_times"){
             $validate->string(array('required' => true, 'field' => 'schedule_no_of_times', 'value' => $schedule_no_of_times), array('required' => 'Please select an option'));
        } else if($schedule_end_type == "on_date") {
             $validate->string(array('required' => true, 'field' => 'schedule_on_date', 'value' => $schedule_on_date), array('required' => 'Valid date is required'));
        }
    }

    $validate->string(array('required' => true, 'field' => 'time', 'value' => $time), array('required' => 'Please select Time'));
    $validate->string(array('required' => true, 'field' => 'timezone', 'value' => $timezone), array('required' => 'Please select Timezone'));

    if($schedule_type == "weekly"){
        if(empty($days_of_week)){
            $validate->setError("days_of_week","Please select an option");
        }
    } else if($schedule_type == "monthly" || $schedule_type == "yearly"){
        $validate->string(array('required' => true, 'field' => 'day_month', 'value' => $day_month), array('required' => 'Please select an option'));
        if($day_month == "days_of_month"){
            if(!isset($days_of_month) || empty($days_of_month)){
                $validate->setError("days_of_month","Please select an option");
            }
        }elseif ($day_month == "on_the_day") {
             $validate->string(array('required' => true, 'field' => 'day_type', 'value' => $day_type), array('required' => 'Please select an option'));
             $validate->string(array('required' => true, 'field' => 'selected_day', 'value' => $selected_day), array('required' => 'Please select an option'));
        }
        if($schedule_type == "yearly"){
            if(empty($months)){
                $validate->setError("months","Please select an option");
            }
        }
    }

    $validate->string(array('required' => true, 'field' => 'generate_via', 'value' => $generate_via), array('required' => 'Please select any option'));
    if(!empty($generate_via)){
        if($generate_via == 'Email'){
            $validate->string(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'At least one email required'));
            if (!empty($email)) {
                $email_arr = explode(';', $email);
                foreach ($email_arr as $tmp_email) {
                    if (!empty($tmp_email)) {
                            $validate->email(array('required' => true, 'field' => 'email', 'value' => $tmp_email), array('required' => 'Email is required', 'invalid' => 'Please add all valid email'));
                    }
                }
            }

            if (empty($rs_schedule_row)) {
                $validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
                if(!empty($password)){
                    if (strlen($password) < 6 || strlen($password) > 20) {
                        $validate->setError('password', 'Password must be 6-20 characters');
                    } else if (!ctype_alnum($password)) {
                        $validate->setError('password', 'Special character not allowed');
                    }
                }
            }
        }
    }

    /*--- Filter Fields ---*/
    $filter_options = array();
    $filter_fields = get_report_filter_fields($report_row['report_key'],'schedule_popup');
    if(!empty($filter_fields)) {
        foreach ($filter_fields as $key => $field_data) {
            $tmp_var = isset($_POST['filter_'.$field_data['name']])?$_POST['filter_'.$field_data['name']]:'';

            if($field_data['is_reuired'] == true && empty($tmp_var)) {
                $fl_error_msg = $field_data['label']." is required";
                if(isset($field_data['error_msg'])) {
                    $fl_error_msg = $field_data['error_msg'];
                }
                $validate->setError('filter_'.$field_data['name'],$fl_error_msg);
            }

            if(!empty($tmp_var)) {
                $filter_options[$field_data['name']] = $tmp_var;
            }
        }
    }
    /*---/Filter Fields ---*/



if ($validate->isValid()) {
    $report_name = $report_row['report_name'];



    $extra_params = array("timezone" => $user_data['timezone']);
    $report_columns_arr = array();
    if($hdn_columns){
        $report_columns_arr = explode(",", $hdn_columns);
    }
    if(!empty($report_columns_arr)) {
        $extra_params['report_columns'] = $report_columns_arr;
    }

    $ins_params = array(
        'report_id' => $report_id,
        'filter_options' => (!empty($filter_options)?json_encode($filter_options):''),
        'schedule_type' => $schedule_type,
        'schedule_end_type' => $schedule_end_type,
        'schedule_end_val' => '',
        'time' => $time,
        'timezone' => $timezone,
        'generate_via' => $generate_via,
        'extra' => json_encode($extra_params),
        'cancel_processing' => 'N',
        'created_at' => 'msqlfunc_NOW()',
    );

    if($schedule_end_type == "no_of_times"){
        $ins_params['schedule_end_val'] = $schedule_no_of_times;
    } else if($schedule_end_type == "on_date"){
        $ins_params['schedule_end_val'] = $schedule_on_date;
    }

    if($schedule_type == "daily" || $schedule_type == "weekly"){
       $ins_params['schedule_frequency'] = $schedule_frequency;
    }

    if($schedule_type == "weekly"){
        $ins_params['days_of_week'] = $days_of_week;
    }elseif ($schedule_type == "monthly" || $schedule_type == "yearly") {
        $ins_params['month_option'] = $day_month;
        if($day_month == "days_of_month"){
            $ins_params['days_of_month'] = implode(",", $days_of_month);
        }elseif ($day_month == "on_the_day") {
            $ins_params['day_type'] = $day_type;
            $ins_params['selected_day'] = $selected_day;
        }
        if($schedule_type == "yearly"){
            $ins_params['months'] = implode(",", $months);
        } 
    }

    if($generate_via == "Email"){
        $ins_params['email'] = $email;
        if($password != ''){
            $ins_params['password'] = "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";
        }
    }    
    
    if (!empty($rs_schedule_row) && is_array($rs_schedule_row)) {
        if(($rs_schedule_row['schedule_type'] != $schedule_type) || ($rs_schedule_row['schedule_end_type'] != $schedule_end_type)){
            $ins_params['process_cnt'] = 0;
        }
        unset($ins_params['created_at']);
        $ins_params['updated_at'] = "msqlfunc_NOW()";
        $up_where = array(
            'clause' => 'id=:id',
            'params' => array(
                ':id' => $rs_schedule_row['id']
            )
        );
        $pdo->update("$REPORT_DB.rps_reports_schedule", $ins_params, $up_where);
        generate_report_request($rs_schedule_row['id'],false);
        
        $next_schedule_date = next_rps_reports_schedule($rs_schedule_row['id'],date('Y-m-d'));
        if($next_schedule_date != ''){
            $up_where = array(
                'clause' => 'id=:id',
                'params' => array(
                    ':id' => $rs_schedule_row['id']
                )
            );
            $req_data = array(
                'next_scheduled' => date('Y-m-d H:i',strtotime($next_schedule_date)),
            );
            $pdo->update("$REPORT_DB.rps_reports_schedule",$req_data,$up_where);
        }

        $oldVaArray = $rs_schedule_row;
        $NewVaArray = $ins_params;
        $activityFeedDesc=array();
        unset($oldVaArray['id']);
        unset($oldVaArray['updated_at']);
        unset($NewVaArray['updated_at']);
        $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);

        $activityFeedDesc['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=> $user_data['profile_page_url'],
            'title'=>$user_data['rep_id']),
          'ac_message_1' =>' updated schedule for report ',
          'ac_red_2'=>array(
            'href'=>'',
            'title'=>$report_name,
          ),
        );

        if(!empty($checkDiff)){
            foreach ($checkDiff as $key1 => $value1) {
                if(in_array($key1,array("process_cnt","user_id","password","extra","next_scheduled"))) {
                    continue;
                }
                
                if($key1 == "filter_options") {
                    $filter_options_old = json_decode($oldVaArray[$key1],true);
                    $filter_options_new = json_decode($NewVaArray[$key1],true);
                    foreach ($filter_options_old as $fl_key => $value) {
                        if($filter_options_old[$fl_key] != $filter_options_new[$fl_key]) {
                            $fl_key1_tmp = key_to_display_value($fl_key);
                            $activityFeedDesc['key_value']['desc_arr']['Filter Options : '.$fl_key1_tmp] = ' From '.($filter_options_old[$fl_key] ? key_to_display_value($filter_options_old[$fl_key]) : 'Blank').' To '.($filter_options_new[$fl_key] ? key_to_display_value($filter_options_new[$fl_key]) : 'Blank');
                        }
                    }
                    continue;
                }
                $key1_tmp = key_to_display_value($key1);
                $activityFeedDesc['key_value']['desc_arr'][$key1_tmp]='From '.($oldVaArray[$key1] ? key_to_display_value($oldVaArray[$key1]) : 'Blank').' To '.($NewVaArray[$key1] ? key_to_display_value($NewVaArray[$key1]) : 'Blank');
            }
        }

        if(!empty($activityFeedDesc) && !empty($activityFeedDesc['key_value']['desc_arr'])) {
            activity_feed(3,$user_data['user_id'],$user_data['user_type'],$user_data['user_id'],$user_data['user_type'],'Report Schedule Updated','','',json_encode($activityFeedDesc));
        }

    } else {
        $ins_params['user_id'] = $user_data['user_id'];
        $ins_params['user_type'] = $user_data['user_type'];
        $scheduled_id = $pdo->insert("$REPORT_DB.rps_reports_schedule",$ins_params);    
        generate_report_request($scheduled_id,false);

        //  next scheduled date code start
        $next_schedule_date = next_rps_reports_schedule($scheduled_id,date('Y-m-d'));
        if($next_schedule_date != ''){
            $up_where = array(
                'clause' => 'id=:id',
                'params' => array(
                    ':id' => $scheduled_id
                )
            );
            $req_data = array(
                'next_scheduled' => date('Y-m-d H:i',strtotime($next_schedule_date)),
            );
            $pdo->update("$REPORT_DB.rps_reports_schedule",$req_data,$up_where);
        }
        //  next scheduled date code ends

        $description['ac_message'] =array(
            'ac_red_1'=>array(
                'href'=> $user_data['profile_page_url'],
                'title'=>$user_data['rep_id'],
            ),
            'ac_message_1' =>' created schedule for report ',
            'ac_red_2'=>array(
                'title'=>$report_name,
            ),
        ); 
        activity_feed(3,$user_data['user_id'],$user_data['user_type'],$user_data['user_id'],$user_data['user_type'],'Report Schedule Created','','',json_encode($description));
    }
    setNotifySuccess("Report Scheduled Successfully");
    $response['status'] = 'success';
} else {
    $errors = $validate->getErrors();
    $response['status'] = 'error';
    $response['errors'] = $errors;
}
  
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>