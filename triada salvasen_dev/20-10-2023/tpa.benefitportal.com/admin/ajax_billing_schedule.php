<?php
include_once 'layout/start.inc.php';
$admin_id = $_SESSION['admin']['id'];
$validate = new Validation();

// pre_print($_REQUEST);
//  step-1 variables
    $file_id = isset($_POST['file_id']) ? $_POST['file_id'] : '';
    $schedule_type = isset($_POST['schedule_type']) ? $_POST['schedule_type'] : '';
    $schedule_frequency = isset($_POST['schedule_frequency']) ? $_POST['schedule_frequency'] : '' ;
    $schedule_end_type =  isset($_POST['schedule_end_type']) ? $_POST['schedule_end_type'] : '';
    $schedule_no_of_times = isset($_POST['schedule_no_of_times']) ? $_POST['schedule_no_of_times'] : '';
    $schedule_on_date = isset($_POST['schedule_on_date']) ? $_POST['schedule_on_date'] : '';
    $added_date = isset($_POST['added_date']) ? $_POST['added_date'] : '';
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
    $file_type = isset($_POST['file_type']) ? $_POST['file_type'] : '';
    $generate_via = isset($_POST['generate_via']) ? $_POST['generate_via'] : '';
    $email = checkIsset($_POST['email']);
    $password = isset($_POST['password']) ? $_POST['password'] : '';


     // check if file schedule is  setup already 
    $eligiblity_sql = "SELECT * FROM billing_schedule WHERE is_deleted='N' AND file_id=:id";
    $where_id = array(':id' => $file_id);
    $billing_res = $pdo->selectOne($eligiblity_sql, $where_id);

    if($cancel_processing == 'Y'){
        if($billing_res){
            $req_where = array(
            "clause"=>"file_id=:id",
            "params"=>array(
              ":id"=>$file_id,
                )
            );
            $req_data = array(
                'is_deleted' => 'Y',
            );
            $pdo->update("billing_schedule",$req_data,$req_where); 
        }
        $req_where = array(
            "clause"=>"id=:id",
            "params"=>array(
              ":id"=>$file_id,
                )
            );
        $req_data = array(
            'cancel_processing' => $cancel_processing,
            'next_scheduled' => '',
        );
        $pdo->update("billing_files",$req_data,$req_where);

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
            }else if($schedule_end_type == "on_date"){
                 $validate->string(array('required' => true, 'field' => 'schedule_on_date', 'value' => $schedule_on_date), array('required' => 'Valid date is required'));
            }
    }

     $validate->string(array('required' => true, 'field' => 'time', 'value' => $time), array('required' => 'Please select Time'));
     $validate->string(array('required' => true, 'field' => 'timezone', 'value' => $timezone), array('required' => 'Please select Timezone'));

    if($schedule_type == "weekly"){
        if(empty($days_of_week)){
            $validate->setError("days_of_week","Please select an option");
        }
    }else if($schedule_type == "monthly" || $schedule_type == "yearly"){
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

     $validate->string(array('required' => true, 'field' => 'file_type', 'value' => $file_type), array('required' => 'Please select an option'));

     $validate->string(array('required' => true, 'field' => 'generate_via', 'value' => $generate_via), array('required' => 'Please select any option'));
    if(!empty($generate_via)){
        if($generate_via == 'Email'){
            $validate->string(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required'));    
            if(!empty($email)){
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $validate->setError("email", "Valid Email is required");
                }   
            } 
            if (empty($billing_res)) {
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

    $validate->string(array('required' => true, 'field' => 'added_date', 'value' => $added_date), array('required' => 'Please select an option'));
    $filter_options = array();
    $filter_options['added_date'] = $added_date;


if ($validate->isValid()) {
        $file_name = "";
        if($file_id){
            $file_name = getname('billing_files',$file_id,'file_name','id');
        }

        $ins_params = array(
            'file_id' => $file_id,
            'file_type' => $file_type,
            'filter_options' => (!empty($filter_options)?json_encode($filter_options):''),
            'user_id' => $_SESSION['admin']['id'],
            'user_type' => "Admin",
            'schedule_type' => $schedule_type,
            'schedule_end_type' => $schedule_end_type,
            'schedule_end_val' => '',
            'time' => $time,
            'timezone' => $timezone,
            'generate_via' => $generate_via,
            'created_at' => 'msqlfunc_NOW()',
        );

        if($schedule_end_type == "no_of_times"){
            $ins_params['schedule_end_val'] = $schedule_no_of_times;
        }else if($schedule_end_type == "on_date"){
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

        if (!empty($billing_res) && is_array($billing_res)) {
            if(($billing_res['schedule_type'] != $schedule_type) || ($billing_res['schedule_end_type'] != $schedule_end_type)){
                $ins_params['process_cnt'] = 0;
            }
            unset($ins_params['created_at']);
            $ins_params['updated_at'] = "msqlfunc_NOW()";
            $up_where = array(
                'clause' => 'file_id=:id',
                'params' => array(
                    ':id' => $file_id
                )
            );
            $pdo->update("billing_schedule", $ins_params, $up_where);

            $next_schedule_date = next_billing_schedule($billing_res['id'],date('Y-m-d'));
            if($next_schedule_date != ''){
                $req_where = array(
                "clause"=>"id=:id",
                "params"=>array(
                  ":id"=>$file_id,
                    )
                );
                $req_data = array(
                    'next_scheduled' => date('Y-m-d H:i',strtotime($next_schedule_date)),
                );
                $pdo->update("billing_files",$req_data,$req_where);
            }


            $oldVaArray = $billing_res;
            $NewVaArray = $ins_params;
            $activityFeedDesc=array();
            unset($oldVaArray['id']);
            unset($oldVaArray['updated_at']);
            unset($NewVaArray['updated_at']);
            $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);

            $activityFeedDesc['ac_message'] =array(
              'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id']),
              'ac_message_1' =>' Updated Billing Schedule ',
              'ac_red_2'=>array(
                'href'=>'',
                'title'=>$file_name,
              ),
            ); 

            if(!empty($checkDiff)){
              foreach ($checkDiff as $key1 => $value1) {
                $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.($oldVaArray[$key1] ? $oldVaArray[$key1] : 'Blank').' To '.($NewVaArray[$key1] ? $NewVaArray[$key1] : 'Blank');
              } 
            }

            if(!empty($activityFeedDesc) && !empty($activityFeedDesc['key_value']['desc_arr'])){ 
                activity_feed(3, $_SESSION['admin']['id'], 'Admin', $file_id, 'billing_files','Admin Updated Billing Schedule', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
            }

        }else{
            $scheduled_id = $pdo->insert("billing_schedule",$ins_params);    
            generate_billing_request($scheduled_id);
 
            //  next scheduled date code start
            $next_schedule_date = next_billing_schedule($scheduled_id,date('Y-m-d'));
            if($next_schedule_date != ''){
                $req_where = array(
                "clause"=>"id=:id",
                "params"=>array(
                  ":id"=>$file_id,
                    )
                );
                $req_data = array(
                    'next_scheduled' => date('Y-m-d H:i',strtotime($next_schedule_date)),
                    'cancel_processing' => 'Y',
                );
                $pdo->update("billing_files",$req_data,$req_where);
            }
            //  next scheduled date code ends

            $description['ac_message'] =array(
              'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
              ),
              'ac_message_1' =>' Scheduled Billing File ',
              'ac_red_2'=>array(
                //'href'=>  '',
                'title'=>$file_name,
              ),
            ); 
            activity_feed(3, $_SESSION['admin']['id'], 'Admin', $file_id, 'billing_files','Admin Scheduled Billing File', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
        }
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