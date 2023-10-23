<?php  
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();
$validate = new Validation();
$response = array();

$section = $_GET['section'];

$default_email_from = isset($_POST['default_email_from'])?$_POST['default_email_from']:'';
$default_from_name = isset($_POST['default_from_name'])?$_POST['default_from_name']:'';
$sms_twilio_number = isset($_POST['sms_twilio_number'])?phoneReplaceMain($_POST['sms_twilio_number']):'';


$agent_services_cell_phone = isset($_POST['agent_services_cell_phone'])?phoneReplaceMain($_POST['agent_services_cell_phone']):'';
$member_services_cell_phone = isset($_POST['member_services_cell_phone'])?phoneReplaceMain($_POST['member_services_cell_phone']):'';
$group_services_cell_phone = isset($_POST['group_services_cell_phone'])?phoneReplaceMain($_POST['group_services_cell_phone']):'';
$agent_services_email = isset($_POST['agent_services_email'])?$_POST['agent_services_email']:'';
$member_services_email = isset($_POST['member_services_email'])?$_POST['member_services_email']:'';
$group_services_email = isset($_POST['group_services_email'])?$_POST['group_services_email']:'';
$enrollment_display_name = isset($_POST['enrollment_display_name'])?$_POST['enrollment_display_name']:'';

$immediate_destination = checkIsset($_POST["immediate_destination"]);
$immediate_destination_name = checkIsset($_POST["immediate_destination_name"]);
$immediate_origin = checkIsset($_POST["immediate_origin"]);
$immediate_origin_name = checkIsset($_POST["immediate_origin_name"]);
$company_entry_description = checkIsset($_POST["company_entry_description"]);
$originating_dfi_id = checkIsset($_POST["originating_dfi_id"]);

$app_settings = array();

if($section == "system_setup") {
    $validate->email(array('required' => true, 'field' => 'default_email_from', 'value' => $default_email_from), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
    if(!$validate->getError('default_email_from')) {
        if (!filter_var($default_email_from, FILTER_VALIDATE_EMAIL)) {
            $validate->setError("default_email_from", "Valid Email is required");
        }
    }
    $validate->string(array('required' => true, 'field' => 'default_from_name', 'value' => $default_from_name), array('required' => 'From Name is required'));

    $validate->digit(array('required' => true, 'field' => 'sms_twilio_number', 'value' => $sms_twilio_number, 'min' => 10, 'max' => 10), array('required' => 'SMS/Twilio Number is required', 'invalid' => 'Valid SMS/Twilio Number is required'));

    $app_settings['default_from_name'] = $default_from_name;
    $app_settings['default_email_from'] = $default_email_from;
    $app_settings['sms_twilio_number'] = $sms_twilio_number;
}

if($section == "system_support") {
    $validate->digit(array('required' => true, 'field' => 'agent_services_cell_phone', 'value' => $agent_services_cell_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
    $validate->digit(array('required' => true, 'field' => 'member_services_cell_phone', 'value' => $member_services_cell_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
    $validate->digit(array('required' => true, 'field' => 'group_services_cell_phone', 'value' => $group_services_cell_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
    $validate->string(array('required' => true, 'field' => 'enrollment_display_name', 'value' => $enrollment_display_name), array('required' => 'Enrollment Display Name is required'));

    $validate->email(array('required' => true, 'field' => 'agent_services_email', 'value' => $agent_services_email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
    if(!$validate->getError('agent_services_email')) {
        if (!filter_var($agent_services_email, FILTER_VALIDATE_EMAIL)) {
            $validate->setError("agent_services_email", "Valid Email is required");
        }
    }
    $validate->email(array('required' => true, 'field' => 'member_services_email', 'value' => $member_services_email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
    if(!$validate->getError('member_services_email')) {
        if (!filter_var($member_services_email, FILTER_VALIDATE_EMAIL)) {
            $validate->setError("member_services_email", "Valid Email is required");
        }
    }
    $validate->email(array('required' => true, 'field' => 'group_services_email', 'value' => $group_services_email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
    if(!$validate->getError('group_services_email')) {
        if (!filter_var($group_services_email, FILTER_VALIDATE_EMAIL)) {
            $validate->setError("group_services_email", "Valid Email is required");
        }
    }

    $app_settings['agent_services_cell_phone'] = $agent_services_cell_phone;
    $app_settings['member_services_cell_phone'] = $member_services_cell_phone;
    $app_settings['group_services_cell_phone'] = $group_services_cell_phone;
    $app_settings['agent_services_email'] = $agent_services_email;
    $app_settings['member_services_email'] = $member_services_email;
    $app_settings['group_services_email'] = $group_services_email;
    $app_settings['enrollment_display_name'] = $enrollment_display_name;
}

if($section == "nacha_file") {
    $validate->string(array('required' => true, 'field' => 'immediate_destination', 'value' => $immediate_destination), array('required' => 'Value is required'));
    $validate->string(array('required' => true, 'field' => 'immediate_destination_name', 'value' => $immediate_destination_name), array('required' => 'Value is required'));
    $validate->string(array('required' => true, 'field' => 'immediate_origin', 'value' => $immediate_origin), array('required' => 'Value is required'));
    $validate->string(array('required' => true, 'field' => 'immediate_origin_name', 'value' => $immediate_origin_name), array('required' => 'Value is required'));
    $validate->string(array('required' => true, 'field' => 'company_entry_description', 'value' => $company_entry_description), array('required' => 'Value is required'));
    $validate->string(array('required' => true, 'field' => 'originating_dfi_id', 'value' => $originating_dfi_id), array('required' => 'Value is required'));

    $app_settings['immediate_destination'] = $immediate_destination;
    $app_settings['immediate_destination_name'] = $immediate_destination_name;
    $app_settings['immediate_origin'] = $immediate_origin;
    $app_settings['immediate_origin_name'] = $immediate_origin_name;
    $app_settings['company_entry_description'] = $company_entry_description;
    $app_settings['originating_dfi_id'] = $originating_dfi_id;
}

if($validate->isValid()){

    if(!empty($app_settings)) {
        $app_settings_db = array();
        $resTxt = "";
        if($section == "system_setup"){
            $resTxt = "System Setup Detail ";
        }else if($section == "system_support"){
            $resTxt = "System Support Information ";
        }else if($section == "nacha_file"){
            $resTxt = "NACHA File Information ";
        }

        foreach ($app_settings as $setting_key => $setting_value) {
            if($setting_key == "immediate_origin"){
                $setting_value = "msqlfunc_AES_ENCRYPT('" . $setting_value . "','" . $CREDIT_CARD_ENC_KEY . "')";
            }
            $setting_sql = "SELECT * FROM app_settings WHERE setting_key=:setting_key";
            $setting_row = $pdo->selectOne($setting_sql,array(":setting_key" => $setting_key));
            if(!empty($setting_row)) {
                $update_params = array(
                    'setting_value' => $setting_value,
                    'updated_at'=>'msqlfunc_NOW()'
                );
                $update_where = array(
                    'clause' => "setting_key=:setting_key",
                    'params' => array(
                        ':setting_key'=>$setting_key,
                    )
                );
                $pdo->update('app_settings', $update_params, $update_where);

                if($setting_row['setting_value'] != $setting_value && $setting_key!='immediate_origin') {
                    $app_settings_db[$setting_key] = $setting_row['setting_value'];    
                }                
            } else {
                $params = array(
                    'admin_id'=> $_SESSION['admin']['id'],
                    'setting_key'=> $setting_key,
                    'setting_value'=> $setting_value,
                    'created_at'=>'msqlfunc_NOW()'
                );
                $pdo->insert('app_settings',$params);

                $app_settings_db[$setting_key] = '';
            }
        }

        /*---- update all trigger ----*/
        $triggers_upd_data = array();
        if(isset($app_settings['default_email_from']) && !empty($app_settings['default_email_from'])) {
            $triggers_upd_data['from_email'] = $app_settings['default_email_from'];
        }
        if(isset($app_settings['default_from_name']) && !empty($app_settings['default_from_name'])) {
            $triggers_upd_data['from_name'] = $app_settings['default_from_name'];
        }        
        if(!empty($triggers_upd_data)) {
            $triggers_upd_where = array(
                'clause' => "id > 0",
                'params' => array(
                )
            );
            $pdo->update('triggers', $triggers_upd_data,$triggers_upd_where);
        }  
        /*----/update all trigger ----*/

        $flg = "true";
        $desc = array();
        $desc['ac_message'] = array(
            'ac_red_1' => array(
                'href' => 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title' => $_SESSION['admin']['display_id'],
            ),
            'ac_message_1' => $resTxt . "Updated <br/>"
        );

        foreach($app_settings_db as $key2 => $val){
            if(array_key_exists($key2,$app_settings)){
                    if(in_array($key2,array('group_services_cell_phone','member_services_cell_phone','agent_services_cell_phone','sms_twilio_number'))) {
                        $app_settings[$key2] = format_telephone($app_settings[$key2]);
                        $val = format_telephone($val);
                    }
                    $tmp_key2 = ucwords(str_replace('_',' ',$key2));
                    $desc['key_value']['desc_arr'][$tmp_key2] = ' Updated From '.$val." To ".$app_settings[$key2].".<br>";
                    $flg = "false";
            } else {
                $desc['description2'][] = ucwords(str_replace('_',' ',$val));
                $flg = "false";
            }
        }
        if($flg == "true"){
            $desc['description_novalue'] = 'No updates in system setup page.';
        }

        $activity_feed_title = $resTxt . 'Updated';

        $desc = json_encode($desc);
        activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'Admin',$activity_feed_title,($_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname']),"",$desc);
    }
    $response['status'] = "success";
    if($section == "system_setup") {
        $response['msg'] = $resTxt ." saved successfully";
    } else {
        $response['msg'] = $resTxt ." saved successfully";
    }
}

if(count($validate->getErrors()) > 0){
    $response['status'] = "errors";
    $response['errors'] = $validate->getErrors();
}

echo json_encode($response);
dbConnectionClose();
exit();
?>